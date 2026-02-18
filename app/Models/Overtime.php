<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'reason',
        'status',
        'approved_by',
        'admin_note',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        // Because we use SSO, we don't have a local User model that contains all users.
        // We might want to use the SSO service to fetch user details if needed.
        // For now, this is just a reminder that user_id refers to SSO ID.
        return null; 
    }
}
