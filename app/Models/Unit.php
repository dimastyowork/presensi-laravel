<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'sso_unit_id',
        'working_days',
    ];

    protected $casts = [
        'sso_unit_id' => 'integer',
        'working_days' => 'array',
    ];
}
