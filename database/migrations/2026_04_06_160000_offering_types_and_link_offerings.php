<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offering_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 64)->unique();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('offerings', function (Blueprint $table) {
            $table->foreignId('offering_type_id')
                ->nullable()
                ->after('type')
                ->constrained('offering_types')
                ->nullOnDelete();
        });

        // Backfill offering_types from existing offerings.type values.
        // We keep offerings.type for backwards compatibility, but stop using it in the MIS UI.
        $existingTypes = DB::table('offerings')
            ->select('type')
            ->whereNotNull('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->filter(fn ($t) => is_string($t) && trim($t) !== '')
            ->values();

        $order = 10;
        foreach ($existingTypes as $type) {
            $slug = Str::slug($type);
            if ($slug === '') {
                continue;
            }
            DB::table('offering_types')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => Str::of($type)->replace('-', ' ')->replace('_', ' ')->title()->toString(),
                    'display_order' => $order,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
            $order += 10;
        }

        $map = DB::table('offering_types')->pluck('id', 'slug'); // slug => id

        foreach ($existingTypes as $type) {
            $slug = Str::slug($type);
            $id = $map[$slug] ?? null;
            if ($id === null) {
                continue;
            }
            DB::table('offerings')->where('type', $type)->update(['offering_type_id' => $id]);
        }
    }

    public function down(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('offering_type_id');
        });

        Schema::dropIfExists('offering_types');
    }
};

