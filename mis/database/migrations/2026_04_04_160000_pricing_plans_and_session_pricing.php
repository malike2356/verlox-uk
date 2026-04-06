<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price_pence')->nullable();
            $table->unsignedBigInteger('compare_at_pence')->nullable();
            $table->char('currency', 3)->default('GBP');
            $table->string('billing_period', 32)->default('one_off');
            $table->unsignedSmallInteger('sessions_included')->nullable();
            $table->string('price_display_override')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_route')->nullable();
            $table->string('cta_url')->nullable();
            $table->foreignId('offering_id')->nullable()->constrained('offerings')->nullOnDelete();
            $table->boolean('show_on_home')->default(true);
            $table->boolean('show_on_book')->default(true);
            $table->boolean('show_on_va')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('pricing_plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_plan_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_included')->default(true);
            $table->timestamps();
        });

        Schema::table('booking_event_types', function (Blueprint $table) {
            $table->unsignedBigInteger('price_pence')->nullable()->after('color');
            $table->string('price_caption', 160)->nullable()->after('price_pence');
        });
    }

    public function down(): void
    {
        Schema::table('booking_event_types', function (Blueprint $table) {
            $table->dropColumn(['price_pence', 'price_caption']);
        });

        Schema::dropIfExists('pricing_plan_features');
        Schema::dropIfExists('pricing_plans');
    }
};
