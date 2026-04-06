<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('favicon_path', 512)->nullable()->after('footer_legal_html');
            $table->string('logo_path', 512)->nullable()->after('favicon_path');
            $table->string('invoice_logo_path', 512)->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['favicon_path', 'logo_path', 'invoice_logo_path']);
        });
    }
};
