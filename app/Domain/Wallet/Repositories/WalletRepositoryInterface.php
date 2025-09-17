<?php

namespace Domain\Wallet\Repositories;

use Domain\Wallet\Entities\Wallet;

interface WalletRepositoryInterface
{
    public function findByUserId(string $userId): ?Wallet;
    public function save(Wallet $wallet): void;
    public function findByUserIdWithLock(string $userId): ?Wallet; // O lockForUpdate agora é uma responsabilidade da implementação
}
