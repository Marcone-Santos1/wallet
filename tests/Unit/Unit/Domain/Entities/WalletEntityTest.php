<?php

use Domain\Wallet\Entities\Wallet;
use Domain\Wallet\ValueObjects\Money;

describe('Wallet Entity', function () {

    test('it should correctly credit an amount to the balance', function () {
        // Arrange (Preparação)
        // Uma carteira começando com R$ 100,00 (10000 centavos)
        $initialBalance = new Money(10000);
        $wallet = new Wallet(id: 1, userId: 1, balance: $initialBalance);

        $amountToCredit = new Money(5000); // R$ 50,00

        // Act (Ação)
        $wallet->credit($amountToCredit);

        // Assert (Verificação)
        // O novo saldo deve ser R$ 150,00 (15000 centavos)
        expect($wallet->getBalance()->amountInCents)->toBe(15000);
    });

    test('it should correctly debit an amount from the balance when funds are sufficient', function () {
        // Arrange
        $initialBalance = new Money(10000); // R$ 100,00
        $wallet = new Wallet(id: 1, userId: 1, balance: $initialBalance);

        $amountToDebit = new Money(3000); // R$ 30,00

        // Act
        $wallet->debit($amountToDebit);

        // Assert
        // O novo saldo deve ser R$ 70,00 (7000 centavos)
        expect($wallet->getBalance()->amountInCents)->toBe(7000);
    });

    test('it should throw an exception when trying to debit with insufficient funds', function () {
        // Arrange
        $initialBalance = new Money(5000); // Saldo de R$ 50,00
        $wallet = new Wallet(id: 1, userId: 1, balance: $initialBalance);

        $amountToDebit = new Money(6000); // Tentativa de debitar R$ 60,00

        // Act & Assert
        // "Saldo insuficiente."
        $wallet->debit($amountToDebit);

    })->throws(Exception::class, 'Saldo insuficiente.');

    test('balance should remain unchanged after a failed debit attempt', function () {
        // Arrange
        $initialBalance = new Money(5000);
        $wallet = new Wallet(id: 1, userId: 1, balance: $initialBalance);
        $amountToDebit = new Money(6000);

        // Act
        try {
            $wallet->debit($amountToDebit);
        } catch (Exception $e) {
        }

        // Assert
        expect($wallet->getBalance()->amountInCents)->toBe(5000);
    });
});
