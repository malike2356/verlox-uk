<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Velox UK');
            $table->string('tagline')->nullable();
            $table->string('website_url')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('tax_reference')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->default('United Kingdom');
            $table->string('phone')->nullable();
            $table->string('support_email')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_reply_to')->nullable();
            $table->text('stripe_publishable_key')->nullable();
            $table->text('stripe_secret_key')->nullable();
            $table->string('stripe_webhook_secret')->nullable();
            $table->string('zoho_client_id')->nullable();
            $table->text('zoho_client_secret')->nullable();
            $table->text('zoho_refresh_token')->nullable();
            $table->string('zoho_org_id')->nullable();
            $table->string('zoho_dc')->default('com');
            $table->unsignedSmallInteger('booking_slot_minutes')->default(30);
            $table->string('meeting_provider')->default('custom');
            $table->text('meeting_link_template')->nullable();
            $table->string('primary_hex')->default('#0ea5e9');
            $table->text('footer_legal_html')->nullable();
            $table->timestamps();
        });

        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('offerings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('type', 32);
            $table->unsignedInteger('display_order')->default(0);
            $table->unsignedBigInteger('price_pence')->nullable();
            $table->char('currency', 3)->default('GBP');
            $table->string('stripe_price_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('color_hex', 7)->default('#64748b');
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_stage_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offering_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('company_name')->nullable();
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->longText('message')->nullable();
            $table->string('source', 64)->default('web');
            $table->string('status', 32)->default('new');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name')->nullable();
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('zoho_contact_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 32)->default('draft');
            $table->date('valid_until')->nullable();
            $table->char('currency', 3)->default('GBP');
            $table->unsignedBigInteger('subtotal_pence')->default(0);
            $table->unsignedBigInteger('tax_pence')->default(0);
            $table->unsignedBigInteger('total_pence')->default(0);
            $table->longText('terms')->nullable();
            $table->string('zoho_estimate_id')->nullable();
            $table->timestamps();
        });

        Schema::create('quotation_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->unsignedBigInteger('unit_price_pence')->default(0);
            $table->unsignedBigInteger('line_total_pence')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('body');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('contract_template_id')->constrained()->restrictOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('status', 32)->default('draft');
            $table->longText('body_snapshot');
            $table->timestamp('signed_at')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 32)->default('draft');
            $table->string('stripe_checkout_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('due_at')->nullable();
            $table->char('currency', 3)->default('GBP');
            $table->unsignedBigInteger('subtotal_pence')->default(0);
            $table->unsignedBigInteger('tax_pence')->default(0);
            $table->unsignedBigInteger('total_pence')->default(0);
            $table->unsignedBigInteger('paid_pence')->default(0);
            $table->string('zoho_invoice_id')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->unsignedBigInteger('unit_price_pence')->default(0);
            $table->unsignedBigInteger('line_total_pence')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount_pence');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('status', 32)->default('pending');
            $table->json('raw')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_availability_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('weekday');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->text('meeting_url')->nullable();
            $table->string('status', 32)->default('confirmed');
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->string('timezone')->default('Europe/London');
            $table->timestamps();
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('subject')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction', 16);
            $table->longText('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamps();
        });

        Schema::create('zoho_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('direction', 16);
            $table->string('entity_type', 64);
            $table->unsignedBigInteger('local_id')->nullable();
            $table->string('remote_id')->nullable();
            $table->string('status', 32);
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zoho_sync_logs');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('booking_availability_rules');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('contract_templates');
        Schema::dropIfExists('quotation_lines');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('pipeline_stages');
        Schema::dropIfExists('offerings');
        Schema::dropIfExists('content_blocks');
        Schema::dropIfExists('company_settings');
    }
};
