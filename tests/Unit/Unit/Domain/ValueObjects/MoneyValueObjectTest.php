<?php

use Domain\Wallet\ValueObjects\Money;

describe('Money Value Object', function () {

    test('it can add two amounts together', function () {
        $moneyA = new Money(1000); // R$ 10,00
        $moneyB = new Money(550);  // R$ 5,50

        $result = $moneyA->add($moneyB);

        expect($result->amountInCents)->toBe(1550);
    });

    test('it can subtract an amount', function () {
        $moneyA = new Money(2000); // R$ 20,00
        $moneyB = new Money(800);  // R$ 8,00

        $result = $moneyA->subtract($moneyB);

        expect($result->amountInCents)->toBe(1200);
    });

    test('it can correctly compare if an amount is less than another', function () {
        $smallAmount = new Money(100);
        $largeAmount = new Money(101);

        expect($smallAmount->isLessThan($largeAmount))->toBeTrue();
        expect($largeAmount->isLessThan($smallAmount))->toBeFalse();
        expect($smallAmount->isLessThan($smallAmount))->toBeFalse();
    });
});
