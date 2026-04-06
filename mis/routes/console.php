<?php

use App\Console\Commands\SendBookingReminders;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Run reminder emails every 30 minutes
Schedule::command(SendBookingReminders::class)->everyThirtyMinutes();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('velox:reset-admin {--password=ChangeMe!Velox2026}', function () {
    $password = (string) $this->option('password');
    User::query()->updateOrCreate(
        ['email' => 'admin@verlox.uk'],
        [
            'name' => 'Velox Admin',
            'password' => $password,
            'is_admin' => true,
            'email_verified_at' => now(),
        ]
    );
    $this->info('Admin ready: admin@verlox.uk (password from --password).');
})->purpose('Create or reset the Velox MIS admin user against the current database');
