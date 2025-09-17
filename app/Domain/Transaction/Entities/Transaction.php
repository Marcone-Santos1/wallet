<?php

namespace Domain\Transaction\Entities;

use Domain\Wallet\Entities\Wallet;
use Domain\Wallet\ValueObjects\Money;

class Transaction
{
    public function __construct(
        public string  $id,
        public ?Wallet $payerWallet,
        public Wallet  $payeeWallet,
        public Money   $amount,
        public string  $type,
    )
    {
    }
}
