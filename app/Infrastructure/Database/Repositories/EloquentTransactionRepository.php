<?php

namespace Infrastructure\Database\Repositories;

use Domain\Transaction\Repositories\TransactionRepositoryInterface;
use Domain\Wallet\Entities\Wallet;
use Domain\Wallet\ValueObjects\Money;
use Infrastructure\Database\Eloquent\Transaction as TransactionModel;
use Domain\Transaction\Entities\Transaction as TransactionEntity;


class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(
        ?Wallet $payerWallet,
        Wallet  $payeeWallet,
        Money   $amount,
        string  $type,
        string  $reversedTransactionId = null
    ): TransactionEntity
    {

        $transactionModel = TransactionModel::create([
            'payer_wallet_id' => $payerWallet?->id,
            'payee_wallet_id' => $payeeWallet->id,
            'value' => $amount->amountInCents / 100, // Converte de centavos para decimal
            'type' => $type,
            'reversed_transaction_id' => $reversedTransactionId,
        ]);

        return $this->toEntity($transactionModel);
    }

    /**
     * Mapeia um Model Eloquent para a Entidade de Domínio.
     *
     * @param TransactionModel $model
     * @return TransactionEntity
     */
    private function toEntity(TransactionModel $model): TransactionEntity
    {
        return new TransactionEntity(
            id: $model->id,
            payerWallet: $model->payerWallet
                ? new Wallet(
                    $model?->payerWallet->id,
                    $model?->payerWallet->user->id,
                    new Money($model?->payerWallet->balance)
                )
                : null,
            payeeWallet: new Wallet(
                $model->payeeWallet->id,
                $model->payeeWallet->user->id,
                new Money($model->payeeWallet->balance)
            ),
            amount: new Money($model->value * 100), // Converte de decimal para centavos
            type: $model->type,
        );
    }

    public function findById(string $transactionId): ?TransactionEntity
    {
        $model = TransactionModel::find($transactionId);
        return $model ? $this->toEntity($model) : null;
    }

    public function hasBeenReversed(string $originalTransactionId): bool
    {
        return TransactionModel::where('reversed_transaction_id', $originalTransactionId)->exists();
    }
}
