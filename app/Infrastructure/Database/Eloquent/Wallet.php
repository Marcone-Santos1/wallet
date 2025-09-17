<?php

namespace Infrastructure\Database\Eloquent;

use Database\Factories\WalletFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = ['user_id', 'balance'];

    /**
     * Uma carteira pertence a um usuário.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cria uma nova instância da factory para o model.
     *
     * @return \Database\Factories\WalletFactory
     */
    protected static function newFactory(): WalletFactory
    {
        return WalletFactory::new();
    }
}
