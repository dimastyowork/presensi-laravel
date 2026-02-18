<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAgreement extends Model
{
    protected $fillable = [
        'sso_user_id',
        'nip',
        'name',
        'unit',
        'agreed_at',
        'agreement_version',
    ];

    protected $casts = [
        'sso_user_id' => 'integer',
        'agreed_at' => 'datetime',
    ];
}

