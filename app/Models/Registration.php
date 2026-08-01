<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'nickname',
        'date_of_birth',
        'home_address',
        'school_origin',
        'school_class',
        'gender',
        'service_interests',
        'whatsapp_number',
        'registration_number',
        'wa_status',
        'wa_sent_at',
        'wa_error',
        'wa_retry_count',
    ];

    protected function casts(): array
    {
        return [
            'service_interests' => 'array',
            'wa_sent_at' => 'datetime',
        ];
    }

    public function getWaStatusLabelAttribute(): string
    {
        return match ($this->wa_status) {
            'sent' => 'Terkirim',
            'failed' => 'Gagal',
            default => 'Pending',
        };
    }

    public static function generateRegistrationNumber(): string
    {
        $year = now()->year;
        $count = self::query()->count() + 1;

        return sprintf('REG%d-%05d', $year, $count);
    }
}
