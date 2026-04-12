<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Email was globally unique on clients/suppliers, which blocked different users
     * from using the same contact email. Scope uniqueness to (user_id, email).
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->unique(['user_id', 'email']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->unique(['user_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'email']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->unique('email');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'email']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->unique('email');
        });
    }
};
