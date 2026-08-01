<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@dscmevent.test'],
            [
                'name' => 'DSCM Admin',
                'username' => 'admin',
                'phone' => '6281234567890',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ],
        );

        Event::query()->updateOrCreate(
            ['slug' => 'community-worship-night'],
            [
                'title' => 'Community Worship Night',
                'description' => 'A church-friendly event foundation designed to support future gatherings, celebrations, seminars, and retreats.',
                'location' => 'DSCM Main Hall',
                'start_date' => now()->addMonths(5)->toDateString(),
                'end_date' => now()->addMonths(5)->toDateString(),
                'hero_image' => null,
                'status' => EventStatus::Active,
            ],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'landing_poster_path'],
            ['value' => 'posters/dscm-official-poster.svg'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'whatsapp_provider'],
            ['value' => 'fonnte'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'whatsapp_api_token'],
            ['value' => 'demo-fonnte-token'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'fonnte_token'],
            ['value' => 'demo-fonnte-token'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'whatsapp_sender_name'],
            ['value' => 'Pendataan Anak TR'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'fonnte_number'],
            ['value' => '6281234567890'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'whatsapp_enabled'],
            ['value' => '1'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'whatsapp_delay'],
            ['value' => '5'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'whatsapp_retry_count'],
            ['value' => '1'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'whatsapp_timeout'],
            ['value' => '10'],
        );

        Setting::query()->updateOrCreate(
            ['key' => 'whatsapp_logging_enabled'],
            ['value' => '1'],
        );
    }
}
