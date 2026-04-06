<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('offering_id')->nullable()->after('contract_id')->constrained('offerings')->nullOnDelete();
        });

        Schema::table('company_settings', function (Blueprint $table) {
            $table->boolean('zoho_auto_sync_invoices')->default(true)->after('zoho_dc');
            $table->boolean('zoho_auto_sync_expenses')->default(false)->after('zoho_auto_sync_invoices');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('offering_id');
        });

        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['zoho_auto_sync_invoices', 'zoho_auto_sync_expenses']);
        });
    }
};
