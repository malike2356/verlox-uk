<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Event types ───────────────────────────────────────────────
        Schema::create('booking_event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            $table->string('color', 7)->default('#6366f1');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ── Intake questions ──────────────────────────────────────────
        Schema::create('booking_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_type_id')->nullable()
                ->constrained('booking_event_types')->nullOnDelete();
            $table->string('label');
            $table->enum('field_type', ['text', 'textarea', 'select'])->default('text');
            $table->json('options')->nullable(); // select choices
            $table->boolean('is_required')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ── Booking answers ───────────────────────────────────────────
        Schema::create('booking_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')
                ->constrained('booking_questions')->cascadeOnDelete();
            $table->text('answer')->nullable();
            $table->timestamps();
        });

        // ── Add event_type_id + reminder columns to bookings ──────────
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('event_type_id')->nullable()
                ->constrained('booking_event_types')->nullOnDelete()->after('id');
            $table->timestamp('reminder_24h_sent_at')->nullable()->after('manage_token');
            $table->timestamp('reminder_1h_sent_at')->nullable()->after('reminder_24h_sent_at');
        });

        // ── Calendar integrations ─────────────────────────────────────
        Schema::create('calendar_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('google'); // google | outlook
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('calendar_id')->nullable();
            $table->string('owner_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['event_type_id']);
            $table->dropColumn(['event_type_id', 'reminder_24h_sent_at', 'reminder_1h_sent_at']);
        });
        Schema::dropIfExists('booking_answers');
        Schema::dropIfExists('booking_questions');
        Schema::dropIfExists('booking_event_types');
        Schema::dropIfExists('calendar_integrations');
    }
};
