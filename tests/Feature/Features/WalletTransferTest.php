<?php

use Infrastructure\Database\Eloquent\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('a user cannot transfer to a non-existent user', function () {
    // Arrange
    $payer = User::factory()->create();
    $wallet = $payer->wallet;
    $wallet->update(['balance' => 100.00]);

    // Act
    $response = actingAs($payer)->post(route('wallet.transfer'), [
        'payee_id' => 999, // Um ID de usuário que não existe
        'value' => 50.00,
    ]);

    // Assert
    $response->assertSessionHasErrors('payee_id'); // A validação deve falhar
    assertDatabaseHas('wallets', ['id' => $wallet->id, 'balance' => 100.00]); // Saldo inalterado
    assertDatabaseMissing('transactions', ['payer_wallet_id' => $wallet->id]); // Nenhuma transação
});

test('a user can successfully transfer money', function () {
    // Arrange
    $payer = User::factory()->create();
    $payerWallet = $payer->wallet;
    $payerWallet->update(['balance' => 200.00]);

    $payee = User::factory()->create();
    $payeeWallet = $payee->wallet;
    $payeeWallet->update(['balance' => 50.00]);

    // Act
    actingAs($payer)->post(route('wallet.transfer'), [
        'payee_id' => $payee->id,
        'value' => 100.00,
    ]);

    assertDatabaseHas('wallets', ['id' => $payerWallet->id, 'balance' => 100.00]);
    assertDatabaseHas('wallets', ['id' => $payeeWallet->id, 'balance' => 150.00]);
    assertDatabaseHas('transactions', [
        'payer_wallet_id' => $payerWallet->id,
        'payee_wallet_id' => $payeeWallet->id,
        'value' => 100.00,
        'type' => 'transfer'
    ]);
});

test('a user cannot transfer with insufficient funds', function () {
    // Arrange
    $payer = User::factory()->create();
    $payerWallet = $payer->wallet;
    $payerWallet->update(['balance' => 50.00]);

    $payee = User::factory()->create();
    $payeeWallet = $payee->wallet;
    $payeeWallet->update(['balance' => 50.00]);

    // Act
    $response = actingAs($payer)->post(route('wallet.transfer'), [
        'payee_id' => $payee->id,
        'value' => 100.00,
    ]);

    // Assert
    $response->assertSessionHas('error');
    assertDatabaseHas('wallets', ['id' => $payerWallet->id, 'balance' => 50.00]);
    assertDatabaseMissing('transactions', ['payer_wallet_id' => $payerWallet->id]);
});
