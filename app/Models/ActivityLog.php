<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'action',
        'target_type',
        'target_id',
        'ip_address',
        'user_agent',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
