<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const LEAD_STATUSES = ['new', 'contacted', 'qualified', 'lost', 'converted'];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mis_role', 16)->nullable()->after('is_admin');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('deal_value_pence')->nullable()->after('status');
            $table->date('expected_close_date')->nullable()->after('deal_value_pence');
            $table->string('loss_reason', 500)->nullable()->after('expected_close_date');
            $table->string('utm_source', 128)->nullable()->after('loss_reason');
            $table->string('utm_medium', 128)->nullable()->after('utm_source');
            $table->string('utm_campaign', 128)->nullable()->after('utm_medium');
            $table->string('utm_term', 128)->nullable()->after('utm_campaign');
            $table->string('utm_content', 128)->nullable()->after('utm_term');
        });

        $allowed = implode("','", self::LEAD_STATUSES);
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("UPDATE leads SET status = 'converted' WHERE status = 'converted_to_client'");
            DB::statement("UPDATE leads SET status = 'new' WHERE status NOT IN ('{$allowed}')");
        } else {
            DB::table('leads')->where('status', 'converted_to_client')->update(['status' => 'converted']);
            DB::table('leads')->whereNotIn('status', self::LEAD_STATUSES)->update(['status' => 'new']);
        }

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 32);
            $table->text('body');
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('quotation_id')->constrained('leads')->nullOnDelete();
            $table->timestamp('sent_at')->nullable()->after('due_at');
            $table->timestamp('last_reminder_at')->nullable()->after('sent_at');
            $table->timestamp('next_reminder_at')->nullable()->after('last_reminder_at');
            $table->timestamp('written_off_at')->nullable()->after('next_reminder_at');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('action', 64);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
        });

        Schema::create('processed_stripe_events', function (Blueprint $table) {
            $table->string('stripe_event_id')->primary();
            $table->timestamp('processed_at')->useCurrent();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });

        Schema::dropIfExists('processed_stripe_events');
        Schema::dropIfExists('audit_logs');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
            $table->dropColumn(['sent_at', 'last_reminder_at', 'next_reminder_at', 'written_off_at']);
        });

        Schema::dropIfExists('lead_activities');

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'deal_value_pence', 'expected_close_date', 'loss_reason',
                'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mis_role');
        });
    }
};
