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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payer_wallet_id')->nullable()->constrained('wallets')->onDelete('restrict');

            $table->foreignUuid('payee_wallet_id')->constrained('wallets')->onDelete('restrict');

            $table->decimal('value', 15, 2)->unsigned();

            $table->enum('type', ['transfer', 'deposit', 'reversal']);

            $table->foreignUuid('reversed_transaction_id')->nullable()->constrained('transactions')->onDelete('restrict');

            $table->timestamps();

            $table->index(['payer_wallet_id', 'payee_wallet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
