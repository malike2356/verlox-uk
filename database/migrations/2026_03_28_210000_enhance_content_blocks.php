<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_blocks', function (Blueprint $table) {
            $table->string('type', 16)->default('textarea')->after('title'); // text | textarea | html | image_url
            $table->boolean('is_active')->default(true)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('content_blocks', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_active']);
        });
    }
};
