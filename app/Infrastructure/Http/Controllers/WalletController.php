<?php

namespace Infrastructure\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Infrastructure\Database\Eloquent\Transaction;
use Infrastructure\Database\Eloquent\User;

class WalletController
{
    /**
     * Exibe a página da carteira com saldo e transações.
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = $user?->wallet;

        // Busca transações onde o usuário é o recebedor OU o pagador
        $transactions = Transaction::where('payee_wallet_id', $wallet?->id)
            ->orWhere('payer_wallet_id', $wallet?->id)
            ->with(['payerWallet.user', 'payeeWallet.user']) // Carrega usuários relacionados
            ->latest() // Ordena pelas mais recentes
            ->take(10) // Pega as últimas 10
            ->get();

        $users = User::where('id', '!=', $user->id)->get();

        return view('wallet.wallet', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'users' => $users
        ]);
    }
}
