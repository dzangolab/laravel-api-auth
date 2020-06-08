<?php

namespace Dzangolab\Auth\Http\Controllers;

use Dzangolab\Auth\Exceptions\Http\TokenException;
use Dzangolab\Auth\Exceptions\Http\UserNotFoundException;
use Dzangolab\Auth\Http\Requests\ResetEmailRequest;
use Dzangolab\Auth\Http\Requests\ResetPasswordRequest;
use Dzangolab\Auth\Services\AuthUserService;
use Dzangolab\Auth\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class PasswordResetController extends Controller
{
    protected $authUserService;
    protected $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService, AuthUserService $authUserService)
    {
        $this->authUserService = $authUserService;
        $this->passwordResetService = $passwordResetService;
    }

    public function requestPasswordReset(ResetEmailRequest $request)
    {
        $email = $request->post('email');

        $result = $this->getPasswordResetService()
            ->sendPasswordResetMail($email);

        return [
            'success' => true,
        ];
    }

    public function reset(ResetPasswordRequest $request, $token)
    {
        $newPassword = $request->post('password');

        $passwordReset = $this->getPasswordResetService()->getByToken($token);

        if (is_null($passwordReset)) {
            throw new TokenException('Invalid reset token');
        }

        $user = $this->getPasswordResetService()
            ->getUserByEmail($passwordReset->email);

        if (!$user) {
            throw new UserNotFoundException('Password reset user not found');
        }

        $result = $this->getAuthUserService()->resetPassword($user, $newPassword);

        if (!$result) {
            throw new Exception('Failed to reset password');
        }

        $this->getAuthUserService()->revokeTokens($user);

        $this->getPasswordResetService()->deleteByToken($token);

        return [
            'success' => true,
            'message' => 'Password changed successfully',
        ];
    }

    protected function getAuthUserService(): AuthUserService
    {
        return $this->authUserService;
    }

    protected function getPasswordResetService(): PasswordResetService
    {
        return $this->passwordResetService;
    }

    protected function response($data, $statusCode = 200, array $headers = [])
    {
        return new JsonResponse($data, $statusCode, $headers);
    }
}
