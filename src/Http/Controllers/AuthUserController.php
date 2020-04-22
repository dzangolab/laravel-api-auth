<?php

namespace Dzangolab\Auth\Http\Controllers;

use Dzangolab\Auth\Exceptions\WrongPasswordException;
use Dzangolab\Auth\Services\AuthUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthUserController extends Controller
{
    protected $authUserService;

    public function __construct(AuthUserService $authUserService)
    {
        $this->authUserService = $authUserService;
    }

    public function enableUserWithToken(Request $request)
    {
        $confirmationToken = $request->get('confirmationToken');

        if ($this->getAuthUserService()->enableUserWithToken($confirmationToken)) {
            return [
                'success' => true,
            ];
        }

        return [
            'success' => false,
        ];
    }

    public function me()
    {
        return $this->getAuthUserService()
            ->getUserWithProfile(Auth::user());
    }

    public function signup(Request $request)
    {
        $featureUserConfirmation = (bool) config('app.features.user_confirmation');
        $useUsernameSameAsEmail = (bool) config('auth.username_same_as_email');

        // convert args to meaningful data to solution domain and ignore all other parts. not part of validation of data though
        $email = $request->get('email');
        $password = $request->get('password');
        $username = $request->get('username');

        if ($useUsernameSameAsEmail) {
            $username = $email;
        }

        $this->getAuthUserService()->createUser([
            'email' => $email,
            'password' => $password,
            'username' => $username,
        ]);

        if ($featureUserConfirmation) {
            return [
                'success' => true,
            ];
        }

        $authToken = $this->getAuthUserService()
            ->login($username, $password);

        /* FIXME [UKS 2020-03-07] clients use word 'auth_tokens' so kept
            but AuthToken is specific one token collection */
        return [
            'auth_tokens' => $authToken,
            'user' => Auth::user(),
        ];
    }

    public function updateMe(Request $request)
    {
        $user = Auth::user();

        $data = [];

        if ($request->get('username')) {
            $data['username'] = $request->get('username');
        }

        $data['given_name'] = $request->get('given_name');
        $data['gender'] = $request->get('gender');
        $data['surname'] = $request->get('surname');

        return $this->getAuthUserService()->update($user, $data);
    }

    public function updatePassword(Request $request)
    {
        $arguments = [
            'current_password' => $request->get('password')['current_password'],
            'new_password' => $request->get('password')['new_password'],
        ];

        try {
            $this->getAuthUserService()->updatePassword($arguments);
        } catch (WrongPasswordException $exception) {
            Log::error($exception->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to update password.',
            ];
        }

        $this->getAuthUserService()->revokeOtherTokens();

        return [
            'success' => true,
            'message' => 'Password updated successfully.',
        ];
    }

    public function updateProfile()
    {
        $user = Auth::user();

        return $this->getAuthUserService()->updateProfile($user);
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
