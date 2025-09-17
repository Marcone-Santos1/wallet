<?php


use Infrastructure\Database\Eloquent\Transaction;
use Infrastructure\Database\Eloquent\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->userA = User::factory()->create();
    $this->walletA = $this->userA->wallet;
    $this->walletA->update(['balance' => 100.00]);

    $this->userB = User::factory()->create();
    $this->walletB = $this->userB->wallet;
    $this->walletB->update(['balance' => 100.00]);

    $this->initialTransaction = Transaction::factory()->create([
        'payer_wallet_id' => $this->walletA->id,
        'payee_wallet_id' => $this->walletB->id,
        'value' => 30.00,
        'type' => 'transfer'
    ]);

    $this->walletA->update(['balance' => 70.00]);
    $this->walletB->update(['balance' => 130.00]);
});

test('a user can reverse a transaction', function () {
    // Act
    $response = actingAs($this->userB)->post(route('transaction.reverse', $this->initialTransaction));

    // Assert
    $response->assertSessionHas('success');

    assertDatabaseHas('wallets', ['id' => $this->walletA->id, 'balance' => 100.00]);
    assertDatabaseHas('wallets', ['id' => $this->walletB->id, 'balance' => 100.00]);

    assertDatabaseHas('transactions', [
        'type' => 'reversal',
        'payer_wallet_id' => $this->walletB->id,
        'payee_wallet_id' => $this->walletA->id,
        'value' => 30.00,
        'reversed_transaction_id' => $this->initialTransaction->id,
    ]);
});

test('a transaction cannot be reversed twice', function () {
    // Act 1: Primeiro estorno (bem-sucedido)
    actingAs($this->userB)->post(route('transaction.reverse', $this->initialTransaction));

    // Act 2: Segunda tentativa de estorno
    $response = actingAs($this->userA)->post(route('transaction.reverse', $this->initialTransaction));

    // Assert
    $response->assertSessionHas('error', 'Não foi possível estornar a transação: Esta transação já foi estornada.');

    expect(Transaction::where('type', 'reversal')->count())->toBe(1);
});
