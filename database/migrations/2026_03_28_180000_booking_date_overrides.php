<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_date_overrides', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->enum('type', ['unavailable', 'hours'])->default('unavailable');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('note', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_date_overrides');
    }
};
