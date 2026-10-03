<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajout des colonnes sur la table users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'affiliate_code')) {
                $table->string('affiliate_code')->nullable()->unique()->after('role');
            }
            if (!Schema::hasColumn('users', 'affiliate_balance')) {
                $table->decimal('affiliate_balance', 10, 2)->default(0.00)->after('affiliate_code');
            }
        });

        // 2. Création de la table commissions
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->string('affiliate_code');
            $table->decimal('order_amount', 10, 2);
            $table->decimal('commission_rate', 5, 2)->default(15.00); // 15% par défaut
            $table->decimal('commission_amount', 10, 2);
            $table->string('status')->default('approved'); // 'pending', 'approved', 'paid'
            $table->timestamps();
        });

        // 3. Création de la table affiliate_payouts
        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // 'wave', 'orange_money', 'mtn', 'paypal', 'bank'
            $table->string('payment_details'); // Numéro téléphone ou email PayPal
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected', 'paid'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('commissions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['affiliate_code', 'affiliate_balance']);
        });
    }
};
