<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('created_with_google')->default(false)->after('google_avatar');
        });

        DB::table('users')
            ->whereNotNull('google_id')
            ->update(['created_with_google' => true]);

        Schema::table('users', function (Blueprint $table) {
            $table->unique('google_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('created_with_google');
        });
    }
};
