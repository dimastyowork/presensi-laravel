<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    /** @use HasFactory<\Database\Factories\PresenceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'location_in',
        'location_out',
        'image_in',
        'image_out',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
