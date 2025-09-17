<?php

namespace App\Domain\Transaction\Services;

use Domain\Wallet\Entities\Wallet;
use Domain\Wallet\ValueObjects\Money;

class ReversalDomainService
{
    /**
     * Executa a lógica de negócio de um estorno.
     *
     * @param Wallet $payerWallet A carteira que está DEVOLVENDO o dinheiro.
     * @param Wallet $payeeWallet A carteira que está RECEBENDO o dinheiro de volta.
     * @param Money $amount O valor a ser estornado.
     * @return void
     */
    public function execute(Wallet $payerWallet, Wallet $payeeWallet, Money $amount): void
    {
        $payerWallet->debit($amount);
        $payeeWallet->credit($amount);
    }
}
