<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('category', 64)->default('other');
            $table->string('vendor', 255)->nullable();
            $table->string('description', 500);
            $table->unsignedBigInteger('amount_pence');
            $table->string('currency', 3)->default('GBP');
            $table->string('status', 16)->default('paid'); // draft | paid
            $table->string('reference', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('zoho_expense_id', 255)->nullable();
            $table->timestamps();
            $table->index('date');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
