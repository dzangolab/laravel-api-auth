<?php

namespace Dzangolab\Auth\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    public $timestamps = false;

    protected $dates = [
        'date_start',
        'date_end',
    ];

    protected $fillable = [
        'date_start',
        'date_end',
    ];

    protected $table = 'user_roles';

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
