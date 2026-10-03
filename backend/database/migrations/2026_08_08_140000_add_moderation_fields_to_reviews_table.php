<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'status')) {
                $table->string('status')->default('approved')->after('rating');
            }
            if (!Schema::hasColumn('reviews', 'verified_buyer')) {
                $table->boolean('verified_buyer')->default(false)->after('status');
            }
            if (!Schema::hasColumn('reviews', 'admin_reply')) {
                $table->text('admin_reply')->nullable()->after('comment');
            }
            if (!Schema::hasColumn('reviews', 'admin_reply_at')) {
                $table->timestamp('admin_reply_at')->nullable()->after('admin_reply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['status', 'verified_buyer', 'admin_reply', 'admin_reply_at']);
        });
    }
};
