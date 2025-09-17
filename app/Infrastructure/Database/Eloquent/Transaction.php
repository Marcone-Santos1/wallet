<?php

namespace Infrastructure\Database\Eloquent;

use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'payer_wallet_id',
        'payee_wallet_id',
        'value',
        'type', // 'transfer', 'deposit'
        'reversed_transaction_id'
    ];

    /**
     * Cria uma nova instância da factory para o model.
     *
     * @return \Database\Factories\TransactionFactory
     */
    protected static function newFactory(): TransactionFactory
    {
        return TransactionFactory::new();
    }

    /**
     * Uma transação tem uma carteira pagadora.
     */
    public function payerWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'payer_wallet_id');
    }

    /**
     * Uma transação tem uma carteira recebedora.
     */
    public function payeeWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'payee_wallet_id');
    }

    public function reversal(): HasOne
    {
        return $this->hasOne(Transaction::class, 'reversed_transaction_id');
    }
}
