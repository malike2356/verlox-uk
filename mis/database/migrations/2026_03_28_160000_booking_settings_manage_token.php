<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('booking_buffer_minutes')->default(0)->after('booking_slot_minutes');
            $table->unsignedSmallInteger('booking_min_notice_hours')->default(2)->after('booking_buffer_minutes');
            $table->unsignedSmallInteger('booking_max_days_ahead')->default(60)->after('booking_min_notice_hours');
            $table->string('booking_timezone', 64)->nullable()->after('booking_max_days_ahead');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('manage_token', 64)->nullable()->unique()->after('timezone');
        });

        foreach (DB::table('bookings')->whereNull('manage_token')->cursor() as $row) {
            DB::table('bookings')->where('id', $row->id)->update(['manage_token' => Str::random(48)]);
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('manage_token');
        });

        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_buffer_minutes',
                'booking_min_notice_hours',
                'booking_max_days_ahead',
                'booking_timezone',
            ]);
        });
    }
};
