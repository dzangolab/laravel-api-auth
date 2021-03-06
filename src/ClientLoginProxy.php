<?php

/*
 * Only responsibility of this class is to act as part of client for task related to client_id and client_secret.
 * For example: providing parameters and requesting token, accessing passport related db tables etc.
 */

namespace Dzangolab\Auth;

use Dzangolab\Auth\Exceptions\Http\InvalidCredentialsException;
use Dzangolab\Auth\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Optimus\ApiConsumer\Router;

class ClientLoginProxy
{
    const CLIENT_NAME = 'client_proxy';
    const REFRESH_TOKEN = 'refreshToken';

    protected $apiConsumer;
    protected $cookie;
    protected $request;

    public function __construct(Router $apiConsumer, Cookie $cookie, Request $request)
    {
        $this->apiConsumer = $apiConsumer;
        $this->cookie = Cookie::getFacadeRoot();
        $this->request = $request;
    }

    /**
     * Attempt to create an access token using user credentials.
     *
     * @param string $username
     * @param string $password
     *
     * @return array
     */
    public function attemptLogin($username, $password)
    {
        return $this->requestToken(
            'password',
            [
                'username' => $username,
                'password' => $password,
            ]
        );
    }

    /*
     * Revoke all access tokens and refresh tokens other than current session.
     */

    /**
     * Attempt to refresh the access token used a refresh token that
     * has been saved in a cookie.
     *
     * @param null $refreshToken
     *
     * @return array
     */
    public function attemptRefresh($refreshToken = null)
    {
        if (!$refreshToken) {
            $refreshToken = $this->getRequest()->cookie(self::REFRESH_TOKEN);

            if (!$refreshToken) {
                $refreshToken = $this->getRequest()->input(self::REFRESH_TOKEN);
            }
        }

        return $this->requestToken('refresh_token', [
            'refresh_token' => $refreshToken,
        ]);
    }

    // usage: $password = generate_password(12); // for a 12-char password containing [0-9, a-z, A-Z]
    // based on https://gist.github.com/zyphlar/7217f566fc83a9633959

    /**
     * Logs out the user. We revoke access token and refresh token.
     * Also instruct the client to forget the refresh cookie.
     */
    public function logout()
    {
        $accessToken = Auth::user()->token();

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update([
                'revoked' => true,
            ]);

        $accessToken->revoke();

        $this->cookie->queue($this->cookie->forget(self::REFRESH_TOKEN));
    }

    /**
     * Proxy a request to the OAuth server.
     *
     * @param string $grantType what type of grant type should be proxied
     * @param array  $data      the data to send to the server
     *
     * @return array
     */
    public function requestToken($grantType, array $data = [])
    {
        $client = $this->getPasswordClient();

        $parameters = array_merge($data, [
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => $grantType,
        ]);

        $response = $this->apiConsumer->post('/oauth/token', $parameters);

        if (!$response->isSuccessful()) {
            throw new InvalidCredentialsException();
        }

        $token = json_decode($response->getContent());

        return [
            'access_token' => $token->access_token,
            'expires_in' => $token->expires_in,
            'refresh_token' => $token->refresh_token,
        ];
    }

    // used for generate_password

    public function revokeOtherTokens()
    {
        $access_token = Auth::user()->token();

        $user_id = Auth::user()->id;

        DB::table('oauth_access_tokens')
            ->where('user_id', '=', $user_id)
            ->where('id', '<>', $access_token->id)
            ->update([
                'revoked' => true,
            ]);

        $access_tokens = DB::table('oauth_access_tokens')
            ->where('user_id', '=', $user_id)
            ->where('id', '<>', $access_token->id)
            ->get()->toArray();

        DB::table('oauth_refresh_tokens')
            ->whereIn('access_token_id', array_column($access_tokens, 'id'))
            ->update([
                'revoked' => true,
            ]);
    }

    public function revokeTokens(User $user)
    {
        DB::table('oauth_access_tokens')
            ->where('user_id', '=', $user->id)
            ->update([
                'revoked' => true,
            ]);

        $access_tokens = DB::table('oauth_access_tokens')
            ->where('user_id', '=', $user->id)
            ->get()->toArray();

        DB::table('oauth_refresh_tokens')
            ->whereIn('access_token_id', array_column($access_tokens, 'id'))
            ->update([
                'revoked' => true,
            ]);

        Cookie::queue($this->cookie->forget(self::REFRESH_TOKEN));
    }

    protected function findFirstPasswordClient()
    {
        return DB::table('oauth_clients')
            ->where('password_client', '=', 1)
            ->where('revoked', '=', 0)
            ->limit(1)
            ->first();
    }

    protected function generate_password($length)
    {
        return substr(
            preg_replace('/[^a-zA-Z0-9]/', '', base64_encode($this->get_random_bytes($length + 1))),
            0,
            $length
        );
    }

    protected function get_random_bytes($nb_bytes = 32)
    {
        $bytes = openssl_random_pseudo_bytes($nb_bytes, $strong);
        if (false !== $bytes && true === $strong) {
            return $bytes;
        } else {
            throw new Exception('Unable to generate secure token from OpenSSL.');
        }
    }

    protected function getPasswordClient()
    {
        $client = $this->findFirstPasswordClient();

        if (!$client) {
            Artisan::call('passport:client', [
                '-n' => true,
                '--name' => static::CLIENT_NAME,
                '--password' => true,
            ]);

            $client = $this->findFirstPasswordClient();
        }

        return $client;
    }

    protected function getRequest()
    {
        return $this->request;
    }
}
