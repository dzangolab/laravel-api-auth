<?php

namespace Dzangolab\Auth\Services;

use Dzangolab\Auth\Mail\PasswordResetMessage;
use Dzangolab\Auth\Models\PasswordReset;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetService
{
    protected $authUserService;
    protected $passwordResetRepository;

    public function __construct(AuthUserService $authUserService, PasswordReset $passwordResetRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
        $this->authUserService = $authUserService;
    }

    public function deleteByToken($token)
    {
        return $this->getPassportResetRepository()->deleteByToken($token);
    }

    public function getByToken($token)
    {
        return $this->getPassportResetRepository()->getByToken($token);
    }

    public function getUserByEmail($email)
    {
        return $this->getAuthUserService()->getByEmail($email);
    }

    public function sendPasswordResetMail($email)
    {
        $user = $this->getUserByEmail($email);

        if (!$user) {
            return true;
        }

        $token = bin2hex(openssl_random_pseudo_bytes(16));

        $data = [
            'email' => $user->email,
            'token' => $token,
        ];

        $passwordReset = new PasswordReset();

        if ($passwordResetByEmail = $this->getPassportResetRepository()->getByEmail($data['email'])) {
            $passwordReset = $passwordResetByEmail;
        }

        $passwordReset->fill($data);

        $passwordReset->save();

        $message = new PasswordResetMessage($user, $this->getAppUrl(), $token, $this->getLocale());

        try {
            $message->onQueue('emails');

            Mail::to($message->getRecipient())
                ->queue($message);
        } catch (Exception $exception) {
            Log::error($exception);

            throw new Exception('Error while sending password reset mail.');
        }

        return true;
    }

    protected function getAppUrl()
    {
        return config('app.url');
    }

    protected function getAuthUserService(): AuthUserService
    {
        return $this->authUserService;
    }

    protected function getLocale()
    {
        return App::getLocale();
    }

    protected function getPassportResetRepository(): PasswordReset
    {
        return $this->passwordResetRepository;
    }
}
