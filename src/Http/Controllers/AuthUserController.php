<?php

namespace Dzangolab\Auth\Http\Controllers;

use Dzangolab\Auth\Exceptions\Http\TokenException;
use Dzangolab\Auth\Exceptions\Http\UserAlreadyExistsException as UserAlreadyExistsHttpException;
use Dzangolab\Auth\Exceptions\Http\UserDisabledException as UserDisabledHttpException;
use Dzangolab\Auth\Exceptions\Http\WrongPasswordException;
use Dzangolab\Auth\Exceptions\UserAlreadyExistsException;
use Dzangolab\Auth\Exceptions\UserDisabledException;
use Dzangolab\Auth\Http\Requests\CreateUserRequest;
use Dzangolab\Auth\Http\Requests\UpdatePasswordRequest;
use Dzangolab\Auth\Http\Requests\UpdateProfileRequest;
use Dzangolab\Auth\Http\Requests\UpdateUserRequest;
use Dzangolab\Auth\Services\AuthUserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthUserController extends Controller
{
    protected $authUserService;

    public function __construct(AuthUserService $authUserService)
    {
        $this->authUserService = $authUserService;
    }

    public function enableUserWithToken(Request $request)
    {
        $enabled = false;

        $confirmationToken = $request->get('token');

        if ($confirmationToken) {
            $enabled = $this->getAuthUserService()->enableUserWithToken($confirmationToken);
        }

        if (!$enabled) {
            throw new TokenException('Invalid confirmation token');
        }

        return [
            'success' => true,
            'message' => 'User enabled successfully.',
        ];
    }

    public function me()
    {
        return $this->getAuthUserService()
            ->getUserWithProfile(Auth::user());
    }

    public function signup(CreateUserRequest $request)
    {
        $featureUserConfirmation = (bool) config('dzangolabAuth.user_confirmation');
        $useUsernameSameAsEmail = (bool) config('dzangolabAuth.username_same_as_email');

        // convert args to meaningful data to solution domain and ignore all other parts. not part of validation of data though
        $email = $request->get('email');
        $password = $request->get('password');
        $username = $request->get('username');

        if ($useUsernameSameAsEmail) {
            $username = $email;
        }

        try {
            $user = $this->getAuthUserService()->createUser([
                'email' => $email,
                'password' => $password,
                'username' => $username,
            ]);

            $authToken = $this->getAuthUserService()->login($username, $password);
        } catch (UserAlreadyExistsException $exception) {
            throw new UserAlreadyExistsHttpException($exception->getMessage());
        } catch (UserDisabledException $exception) {
            throw new UserDisabledHttpException($user);
        }

        /* FIXME [UKS 2020-03-07] clients use word 'auth_tokens' so kept
            but AuthToken is specific one token collection */
        return [
            'auth_tokens' => $authToken,
            'user' => $this->getAuthUserService()->getUserWithProfile(Auth::user()),
        ];
    }

    public function updateMe(UpdateUserRequest $request)
    {
        $user = Auth::user();

        $data = $request->toArray();

        return $this->getAuthUserService()->update($user, $data);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $arguments = [
            'current_password' => $request->get('password')['current_password'],
            'new_password' => $request->get('password')['new_password'],
        ];

        try {
            $this->getAuthUserService()->updatePassword($arguments);
        } catch (Exception $exception) {
            throw new WrongPasswordException($exception->getMessage());
        }

        $this->getAuthUserService()->revokeOtherTokens();

        return [
            'success' => true,
            'message' => 'Password updated successfully.',
        ];
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $data = $request->get('profile', []);

        return $this->getAuthUserService()->updateProfile($user, $data);
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
