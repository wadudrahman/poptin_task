<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('rules') && !Schema::hasColumn('rules', 'uuid')) {
            Schema::table('rules', function (Blueprint $table) {
                $table->string('uuid')->nullable()->after('id')->index();
            });

            // Update Existing Records
            DB::table('rules')->get()->each(function ($rule) {
                DB::table('rules') ->where('id', $rule->id)->update(['uuid' => Uuid::uuid4()->toString()]);
            });

            // Unique Constraint
            Schema::table('rules', function (Blueprint $table) {
                $table->unique('uuid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('rules') && Schema::hasColumn('rules', 'uuid')) {
            Schema::table('rules', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};
