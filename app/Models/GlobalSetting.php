<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
        'type',
        'is_active',
    ];

    // We can't strictly cast 'value' to array because it can be text/boolean/json.
    // But we can add a helper accessor or handle it in controller. 
    // For now, let's just keep it as string and decode manually when type is json.
}
