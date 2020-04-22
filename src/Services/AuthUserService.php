<?php

namespace Dzangolab\Auth\Services;

use Dzangolab\Auth\ClientLoginProxy;
use Dzangolab\Auth\Events\AbstractUserEvent;
use Dzangolab\Auth\Events\LoginEvent;
use Dzangolab\Auth\Events\PasswordChangedEvent;
use Dzangolab\Auth\Events\UserWasCreated;
use Dzangolab\Auth\Events\UserWasUpdated;
use Dzangolab\Auth\Exceptions\UserAlreadyExistsException;
use Dzangolab\Auth\Models\User;
use Exception;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthUserService
{
    // these are used for setting key of cookies of auth
    const ACCESS_TOKEN = 'accessToken';
    const ACCESS_TOKEN_EXPIRES_IN = 'accessTokenExpiresIn';
    const REFRESH_TOKEN = 'refreshToken';

    protected $clientLoginProxy;
    protected $dispatcher;
    protected $userRepository;

    public function __construct(
        ClientLoginProxy $clientLoginProxy,
        Dispatcher $dispatcher,
        User $user
    ) {
        $this->clientLoginProxy = $clientLoginProxy;
        $this->dispatcher = $dispatcher;
        $this->userRepository = $user;
    }

    public function attemptRefresh($refresh_token)
    {
        return $this->getClientLoginProxy()
            ->attemptRefresh($refresh_token);
    }

    public function createUser(array $data)
    {
        $featureUserConfirmation = (bool) config('app.features.user_confirmation');
        $useUsernameSameAsEmail = (bool) config('auth.username_same_as_email');

        $_data = [
            'email' => $data['email'],
            'username' => $data['username'],
        ];

        if (!$useUsernameSameAsEmail && !$_data['username']) {
            throw new Exception('Username is required for signup.');
        }

        $existingUser = $this->getUserRepository()->findExistingForSignup($_data['email'], $_data['username']);

        if ($existingUser) {
            throw new UserAlreadyExistsException(sprintf('An account already exists for username or email `%s`', $_data['email']));
        }

        $_data['password'] = Hash::make($data['password']);

        $user = new User();

        if ($featureUserConfirmation) {
            $_data['disabled'] = true;

            $user->confirmation_token = bin2hex(openssl_random_pseudo_bytes(16));
        }

        $user->fill($_data);

        $user->save();

        $this->fireEvent(new UserWasCreated($user));

        return $user;
    }

    public function enableUserWithToken(string $confirmationToken)
    {
        $user = $this->getUserRepository()
            ->getByConfirmationToken($confirmationToken);

        if (!$user) {
            return false;
        }

        $this->getUserRepository()->enableUser($user);

        return true;
    }

    public function getAll(array $fields = [], array $args = [])
    {
        return $this->getRepository()->get($fields, $args);
    }

    public function getByConfirmationToken($confirmationToken)
    {
        return $this->getUserRepository()
            ->getByConfirmationToken();
    }

    public function getByEmail($email)
    {
        return $this->getUserRepository()
            ->findByEmail($email);
    }

    public function getById($userId)
    {
        if (!$this->checksCurrentUser($userId)) {
            return;
        }

        return $this->getUserRepository()->getById($userId);
    }

    public function getClientLoginProxy()
    {
        return $this->clientLoginProxy;
    }

    public function getUserWithProfile($user)
    {
        return $this->getUserRepository()
            ->getWithProfile($user);
    }

    public function login($username, $password)
    {
        $user = $this->getUserRepository()->findForPassport($username);

        $authToken = $this->getClientLoginProxy()
            ->attemptLogin($username, $password);

        $this->getDispatcher()->dispatch(new LoginEvent($user));

        $this->createCookies($authToken);

        Auth::setUser($user);

        return $authToken;
    }

    public function logout()
    {
        $this->getClientLoginProxy()->logout();
    }

    public function resetPassword(User $user, $newPassword): User
    {
        $user = $this->getUserRepository()->resetPassword($user, $newPassword);

        if (null === $user) {
            return null;
        }

        $this->fireEvent(new PasswordChangedEvent($user));

        return $user;
    }

    public function revokeOtherTokens()
    {
        $this->getClientLoginProxy()->revokeOtherTokens();
    }

    public function revokeTokens(User $user)
    {
        $this->getClientLoginProxy()->revokeTokens($user);
    }

    public function update($user, array $data)
    {
        $user = $this->getUserRepository()->updateUser($user, $data);

        if (null === $user) {
            return null;
        }

        $this->fireEvent(new UserWasUpdated($user));

        return $user;
    }

    public function updatePassword($data)
    {
        $user = Auth::user();

        return $this->getUserRepository()
            ->updatePassword($user, $data);
    }

    public function updateProfile($userId, array $data)
    {
        $user = $this->checksCurrentUser($userId);

        $profile = $this->getUserRepository()->updateProfile($user, $data);

        if (null === $profile) {
            return null;
        }

        $this->fireEvent(new UserWasUpdated($user));

        return $profile;
    }

    protected function checksCurrentUser(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser && ($currentUser->id !== $user->id)) {
            throw new AccessDeniedHttpException('access denied');
        }

        return true;
    }

    protected function createCookies(array $authToken)
    {
        Cookie::queue(
            self::REFRESH_TOKEN,
            $authToken['refresh_token'],
            config('auth.refresh_token_lifetime.default') / 60,
            null,
            null,
            false,
            true // HttpOnly
        );

        Cookie::queue(
            self::ACCESS_TOKEN,
            $authToken['access_token'],
            $authToken['expires_in'] / 60,
            null,
            null,
            false,
            false
        );

        Cookie::queue(
            self::ACCESS_TOKEN_EXPIRES_IN,
            $authToken['expires_in'],
            $authToken['expires_in'] / 60,
            null,
            null,
            false,
            false
        );
    }

    protected function fireEvent(AbstractUserEvent $event)
    {
        $this->getDispatcher()->dispatch($event);
    }

    protected function getDispatcher()
    {
        return $this->dispatcher;
    }

    protected function getUserRepository(): User
    {
        return $this->userRepository;
    }
}
