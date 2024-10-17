<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainAndWidgetManagement extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable Local ENV
        if (!app()->environment('local')) {
            return;
        }

        // Create Domains Table
        if (!Schema::hasTable('domains')) {
            Schema::create('domains', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('domain')->index();
                $table->timestamps();
            });
        }

        // Create Widgets Table
        if (!Schema::hasTable('widgets')) {
            Schema::create('widgets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('domain_id')->constrained()->onDelete('cascade');
                $table->string('widget_name');
                $table->timestamps();
            });
        }

        // Alter Rules Table
        if (Schema::hasTable('rules')) {
            Schema::table('rules', function (Blueprint $table) {
                $table->foreignId('widget_id')->constrained()->onDelete('cascade');
                $table->dropColumn('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Enable Local ENV
        if (!app()->environment('local')) {
            return;
        }

        if (Schema::hasTable('rules') && Schema::hasColumn('rules', 'widget_id')) {
            Schema::table('rules', function (Blueprint $table) {
                $table->dropForeign(['widget_id']);
                $table->unsignedBigInteger('user_id')->after('id');
                $table->dropColumn('widget_id');
            });
        }

        Schema::dropIfExists('widgets');
        Schema::dropIfExists('domains');
    }
}

