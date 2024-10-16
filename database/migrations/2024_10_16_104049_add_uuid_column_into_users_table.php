<?php

use Illuminate\Support\Facades\{DB, Schema};
use Illuminate\Database\{Migrations\Migration, Schema\Blueprint};
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('uuid', 36)->nullable()->after('id')->index();
                $table->unique('uuid');
            });

            // Update Existing Records
            DB::table('users')->get()->each(function ($rule) {
                DB::table('users')->where('id', $rule->id)->update(['uuid' => Uuid::uuid4()]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};
