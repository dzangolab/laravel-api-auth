<?php

namespace Dzangolab\Auth\Models;

use Carbon\Carbon;
use Dzangolab\Auth\Exceptions\UserDisabledException;
use Dzangolab\Auth\Exceptions\UserNotFoundException;
use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

/**
 * @property string confirmation_token
 * @property int id
 * @property last_login
 * @property disabled
 * @property mixed username
 * @property mixed profile
 * @property false|string|null password
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable;
    use CanResetPassword;
    use HasApiTokens;
    use MustVerifyEmail;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'disabled',
        'email',
        'password',
        'username',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'confirmation_token',
        'password',
        'remember_token',
    ];

    protected $table = 'users';

    /**
     * Disables the user.
     *
     * @return $this for method chaining
     */
    public function disable()
    {
        $this->fill(
            [
                'disabled' => true,
            ]
        );

        return $this;
    }

    /**
     * Enables the user.
     *
     * @return $this for method chaining
     */
    public function enable()
    {
        $this->fill(
            [
                'disabled' => false,
            ]
        );

        return $this;
    }

    public function enableUser($user)
    {
        $user->enable();

        $user->save();

        return $user;
    }

    public function findByEmail($email)
    {
        return $this
            ->query()
            ->where('email', $email)
            ->first();
    }

    public function findExistingForSignup($username, $email)
    {
        return $this->query()
            ->where('username', $email)
            ->orWhere('username', $username)
            ->orWhere('email', $email)
            ->first();
    }

    public function findForPassport($username): User
    {
        $user = $this->query()
            ->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (!$user) {
            throw new UserNotFoundException();
        }

        if ($user->isDisabled()) {
            throw new UserDisabledException($user);
        }

        return $user;
    }

    public function getByConfirmationToken($token)
    {
        return static::query()
            ->where('confirmation_token', $token)
            ->first();
    }

    public function getById($userId)
    {
        return User::query()
            ->where('id', $userId)
            ->first();
    }

    public function getWithProfile($user)
    {
        return User::query()
            ->with('profile')
            ->where('id', $user->id)
            ->first();
    }

    public function isDisabled()
    {
        return $this->disabled;
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'id', 'id');
    }

    public function resetPassword(User $user, $newPassword)
    {
        $user->password = Hash::make($newPassword);

        $user->save();

        return $user;
    }

    public function updateLastLogin($user)
    {
        $user->last_login = Carbon::now();

        return $user->save();
    }

    public function updatePassword(User $user, array $data)
    {
        $doesPasswordMatch = Hash::check($data['current_password'], $user->password);

        if ($doesPasswordMatch) {
            $user->password = Hash::make($data['new_password']);

            $user->save();
        } else {
            throw new Exception('wrong current password');
        }

        return $user;
    }

    public function updateProfile(User $user, array $data)
    {
        $profile = $user->profile;

        $profile->fill($data);

        $profile->save();

        return $profile;
    }

    public function updateUser(User $user, array $data)
    {
        $user->update($data);

        return $user;
    }

    public function validateForPassportEmailGrant($request)
    {
        $requestParameters = (array) $request->getParsedBody();

        $user_email = $requestParameters['email'];

        return User::where('email', $user_email)->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(
            function ($user) {
                $user->profile()->create([]);
            }
        );
    }
}
