<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('va_client_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mis_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('company_name');
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('tier', 32)->default('starter');
            $table->string('status', 32)->default('onboarding');
            $table->decimal('monthly_rate_gbp', 10, 2)->default(0);
            $table->unsignedSmallInteger('hours_included')->default(0);
            $table->decimal('overage_rate_gbp', 10, 2)->default(0);
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->date('minimum_term_end')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('account_manager')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('email');
        });

        Schema::create('va_assistants', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('country')->nullable();
            $table->string('timezone')->default('Europe/London');
            $table->decimal('hourly_rate_gbp', 10, 2);
            $table->json('skills')->nullable();
            $table->string('availability', 32)->default('available');
            $table->decimal('perform_score', 3, 2)->nullable();
            $table->string('wise_email')->nullable();
            $table->char('payment_currency', 3)->default('GBP');
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('email');
        });

        Schema::create('va_engagements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('va_client_account_id')->constrained('va_client_accounts')->cascadeOnDelete();
            $table->foreignId('va_assistant_id')->constrained('va_assistants')->cascadeOnDelete();
            $table->string('tier', 32);
            $table->unsignedSmallInteger('hours_per_month');
            $table->decimal('client_rate_monthly_gbp', 10, 2);
            $table->decimal('va_hourly_rate_gbp', 10, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 32)->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('va_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('va_engagement_id')->constrained('va_engagements')->cascadeOnDelete();
            $table->foreignId('va_assistant_id')->constrained('va_assistants')->cascadeOnDelete();
            $table->foreignId('va_client_account_id')->constrained('va_client_accounts')->cascadeOnDelete();
            $table->date('work_date');
            $table->decimal('hours_logged', 6, 2);
            $table->text('task_description');
            $table->boolean('is_approved')->default(false);
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('va_nps_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('va_client_account_id')->constrained('va_client_accounts')->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->text('comment')->nullable();
            $table->unsignedTinyInteger('period_month');
            $table->unsignedSmallInteger('period_year');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('va_communication_logs', function (Blueprint $table) {
            $table->id();
            $table->string('related_type', 16);
            $table->foreignId('va_client_account_id')->nullable()->constrained('va_client_accounts')->cascadeOnDelete();
            $table->foreignId('va_assistant_id')->nullable()->constrained('va_assistants')->cascadeOnDelete();
            $table->string('type', 32);
            $table->string('summary');
            $table->text('details')->nullable();
            $table->string('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('va_communication_logs');
        Schema::dropIfExists('va_nps_responses');
        Schema::dropIfExists('va_time_logs');
        Schema::dropIfExists('va_engagements');
        Schema::dropIfExists('va_assistants');
        Schema::dropIfExists('va_client_accounts');
    }
};
