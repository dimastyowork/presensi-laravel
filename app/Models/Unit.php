<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'available_shifts',
        'working_days',
    ];

    protected $casts = [
        'available_shifts' => 'array',
        'working_days' => 'array',
    ];
}
