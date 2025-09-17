<?php

use App\Infrastructure\Http\Controllers\DepositController;
use Infrastructure\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Infrastructure\Http\Controllers\ReversalController;
use Infrastructure\Http\Controllers\TransactionController;
use Infrastructure\Http\Controllers\WalletController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Rota para exibir a página da carteira
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');

    // Rota para processar o formulário de transferência
    Route::post('/transfer', [TransactionController::class, 'transfer'])->name('wallet.transfer');

    // Rota para processar o formulário de depósito
    Route::post('/deposit', [DepositController::class, 'deposit'])->name('wallet.deposit');

    // Rota para processar a reversão de uma transferência
    Route::post('/reversal/{transaction}', [ReversalController::class, 'store'])->name('transaction.reverse');

});


require __DIR__.'/auth.php';
