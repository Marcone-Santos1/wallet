<?php

namespace Domain\Wallet\Entities;

use Domain\Wallet\ValueObjects\Money;
use Exception;

class Wallet
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        private Money $balance
    ) {}

    public function getBalance(): Money
    {
        return $this->balance;
    }

    /**
     * @throws Exception
     */
    public function debit(Money $amount): void
    {
        if ($this->balance->isLessThan($amount)) {
            throw new Exception("Saldo insuficiente.");
        }
        $this->balance = $this->balance->subtract($amount);
    }

    public function credit(Money $amount): void
    {
        $this->balance = $this->balance->add($amount);
    }
}
