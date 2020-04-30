<?php

namespace Dzangolab\Auth\Http\Controllers;

use Dzangolab\Auth\Http\Requests\ResetEmailRequest;
use Dzangolab\Auth\Http\Requests\ResetPasswordRequest;
use Dzangolab\Auth\Services\AuthUserService;
use Dzangolab\Auth\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        if ($result) {
            return ['success' => true];
        }

        return ['success' => false];
    }

    public function reset(ResetPasswordRequest $request, $token)
    {
        $newPassword = $request->post('password');

        $passwordReset = $this->getPasswordResetService()->getByToken($token);

        if (is_null($passwordReset)) {
            return ['success' => false];
        }

        $user = $this->getPasswordResetService()
            ->getUserByEmail($passwordReset->email);

        if (!$user) {
            return ['success' => false];
        }

        $result = $this->getAuthUserService()->resetPassword($user, $newPassword);

        if (!$result) {
            return ['success' => false];
        }

        $this->getAuthUserService()->revokeTokens($user);

        $this->getPasswordResetService()->deleteByToken($token);

        return ['success' => true];
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
