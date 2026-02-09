<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'available_shifts',
    ];

    protected $casts = [
        'available_shifts' => 'array',
    ];
}
