<?php

namespace Dzangolab\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'gender',
        'given_name',
        'middle_name',
        'surname',
    ];

    protected $table = 'profiles';
}
