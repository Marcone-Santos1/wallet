<?php

namespace Infrastructure\Database\Repositories;

use Domain\Wallet\Entities\Wallet as WalletEntity;
use Infrastructure\Database\Eloquent\Wallet as WalletModel;
use Domain\Wallet\Repositories\WalletRepositoryInterface;
use Domain\Wallet\ValueObjects\Money;

class EloquentWalletRepository implements WalletRepositoryInterface
{
    public function findByUserId(string $userId): ?WalletEntity
    {
        $walletModel = WalletModel::where('user_id', $userId)->first();
        return $walletModel ? $this->toEntity($walletModel) : null;
    }

    public function findByUserIdWithLock(string $userId): ?WalletEntity
    {
        $walletModel = WalletModel::where('user_id', $userId)->lockForUpdate()->first();
        return $walletModel ? $this->toEntity($walletModel) : null;
    }

    public function save(WalletEntity $walletEntity): void
    {
        WalletModel::where('id', $walletEntity->id)->update([
            'balance' => $walletEntity->getBalance()->amountInCents / 100 // Converte de volta
        ]);
    }

    // Mapeia do Model Eloquent para a Entidade de Domínio
    private function toEntity(WalletModel $model): WalletEntity
    {
        return new WalletEntity(
            id: $model->id,
            userId: $model->user_id,
            balance: new Money((int)round($model->balance * 100))
        );
    }
}
