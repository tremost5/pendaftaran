<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'registration_id',
        'provider',
        'target',
        'sender_name',
        'message',
        'status',
        'attempt_count',
        'max_attempts',
        'error',
        'response',
    ];

    protected $casts = [
        'attempt_count' => 'integer',
        'max_attempts' => 'integer',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
