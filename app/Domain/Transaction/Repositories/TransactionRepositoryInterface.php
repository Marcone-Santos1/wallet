<?php

namespace Domain\Transaction\Repositories;

use Domain\Transaction\Entities\Transaction;
use Domain\Wallet\Entities\Wallet;
use Domain\Wallet\ValueObjects\Money;

interface TransactionRepositoryInterface
{

    public function create(
        ?Wallet $payerWallet,
        Wallet $payeeWallet,
        Money $amount,
        string $type,
        ?string $reversedTransactionId = null
    ): Transaction;

    public function findById(string $transactionId): ?Transaction;

    public function hasBeenReversed(string $originalTransactionId): bool;
}
