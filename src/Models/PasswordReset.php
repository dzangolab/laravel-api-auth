<?php

namespace Dzangolab\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    const UPDATED_AT = null;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'token',
    ];

    protected $table = 'password_resets';

    public function deleteByToken($token)
    {
        return $this
            ->where('token', $token)
            ->delete();
    }

    public function getByEmail($email)
    {
        return $this
            ->where('email', $email)
            ->first();
    }

    public function getByToken($token)
    {
        return $this
            ->where('token', $token)
            ->first();
    }
}
