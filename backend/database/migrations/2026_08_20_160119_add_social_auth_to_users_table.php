<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('auth_provider')->nullable()->after('email_verified_at');
            $table->string('auth_provider_id')->nullable()->after('auth_provider');
            $table->string('avatar_url')->nullable()->after('auth_provider_id');
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['auth_provider', 'auth_provider_id', 'avatar_url']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
