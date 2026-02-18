<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'sso_unit_id',
        'available_shifts',
        'working_days',
    ];

    protected $casts = [
        'sso_unit_id' => 'integer',
        'available_shifts' => 'array',
        'working_days' => 'array',
    ];
}
