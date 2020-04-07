<?php

namespace Dzangolab\Auth\Http\Controllers;

use Dzangolab\Auth\Exceptions\InvalidCredentialsException;
use Dzangolab\Auth\Exceptions\UserDisabledException;
use Dzangolab\Auth\Exceptions\UserNotFoundException;
use Dzangolab\Auth\Services\AuthUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    protected $authUserService;

    public function __construct(AuthUserService $authUserService)
    {
        $this->authUserService = $authUserService;
    }

    public function handleAuthProvider(Request $request, $provider)
    {
        $has_code = $request->has('code');

        if ($has_code) {
            $has_redirect_url = $request->has('redirectUri');

            if ($has_redirect_url) {
                $response_user = Socialite::driver($provider)->redirectUrl($request->input('redirectUri'))->stateless()->user();
            } else {
                $response_user = Socialite::driver($provider)->stateless()->user();
            }

            $username = $response_user->getNickname();

            if (!$username) {
                $trimmed_name = str_replace(' ', '', $response_user->getName());

                $username = strtolower($trimmed_name);
            }

            $user_info = [
                'username' => $username,
                'email' => $response_user->getEmail(),
            ];

            return $this->response($this->getProxy()->attemptSocialLogin($user_info));
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function login(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        try {
            $authToken = $this->getAuthUserService()->login($username, $password);
        } catch (UserNotFoundException $exception) {
            throw new InvalidCredentialsException();
        } catch (UserDisabledException $exception) {
            Log::info(sprintf('Id of disable user who tried to login: %s', $exception->getUser()->username));

            throw new InvalidCredentialsException();
        }

        /* FIXME [UKS 2020-03-07] current clients use word 'auth_tokens' so kept
            but AuthToken is specific one token collection, so should be singular */
        return $this->response([
            'auth_tokens' => $authToken,
            'user' => $this->getAuthUserService()->getUserWithProfile(Auth::user()),
        ]);
    }

    public function logout()
    {
        $this->getAuthUserService()->logout();

        return $this->response([
        ]);
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->get('refresh_token');

        return $this->response($this->getAuthUserService()->attemptRefresh($refreshToken));
    }

    protected function getAuthUserService(): AuthUserService
    {
        return $this->authUserService;
    }

    protected function response($data, $statusCode = 200, array $headers = [])
    {
        return new JsonResponse($data, $statusCode, $headers);
    }
}
