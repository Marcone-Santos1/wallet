<?php


use Infrastructure\Database\Eloquent\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('a user can deposit a positive amount into their own wallet', function () {
    // Arrange
    $user = User::factory()->create();
    $wallet = $user->wallet;
    $wallet->update(['balance' => 100.00]);

    // Act
    $response = actingAs($user)->post(route('wallet.deposit'), [
        'deposit_payee_id' => $user->id,
        'value' => 50.50,
    ]);

    // Assert
    $response->assertSessionHas('success');
    assertDatabaseHas('wallets', [
        'id' => $wallet->id,
        'balance' => 150.50,
    ]);
    assertDatabaseHas('transactions', [
        'payee_wallet_id' => $wallet->id,
        'value' => 50.50,
        'type' => 'deposit',
    ]);
});

test('a user cannot deposit an invalid amount', function (string $invalidValue) {
    // Arrange
    $user = User::factory()->create();
    $wallet = $user->wallet;
    $wallet->update(['balance' => 100.00]);
    // Act
    $response = actingAs($user)->post(route('wallet.deposit'), [
        'deposit_payee_id' => $user->id,
        'value' => $invalidValue,
    ]);

    // Assert
    $response->assertSessionHasErrors('value'); // A validação deve falhar
    assertDatabaseHas('wallets', ['id' => $wallet->id, 'balance' => 100.00]); // O saldo não deve mudar
    assertDatabaseMissing('transactions', ['payee_wallet_id' => $wallet->id]); // Nenhuma transação criada

})->with([
    'zero' => '0',
    'negative value' => '-50',
    'non-numeric value' => 'abc',
]);
