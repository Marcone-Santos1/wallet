<?php

namespace Domain\Transaction\Services;

use Domain\Wallet\Entities\Wallet;
use Domain\Wallet\ValueObjects\Money;

class TransferDomainService
{
    /**
     * @throws \Exception
     */
    public function execute(Wallet $payerWallet, Wallet $payeeWallet, Money $amount): void
    {
        $payerWallet->debit($amount);
        $payeeWallet->credit($amount);
    }
}
