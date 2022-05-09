<?php

namespace Api\Users\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'role',
    ];

    protected $table = 'roles';

    public static function getRole($role)
    {
        return self::query()
            ->where('name', $role)
            ->first();
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_roles',
            'role_id',
            'user_id'
        )->using(UserRole::class
        )->withPivot(
            [
                'date_start',
                'date_end',
            ]
        );
    }
}
