<?php

namespace Infrastructure\Http\Controllers;

use Application\Transaction\DTOs\ReverseTransactionDTO;
use Application\Transaction\UseCases\ReverseTransactionUseCase;
use Illuminate\Support\Facades\Auth;
use Infrastructure\Database\Eloquent\Transaction;
use Infrastructure\Database\Eloquent\User;

class ReversalController
{
    public function __construct(private ReverseTransactionUseCase $reverseTransactionUseCase)
    {
    }

    public function store(Transaction $transaction)
    {
        // Lógica de autorização simples: O usuário logado deve ser parte da transação original.
        $userWalletId = Auth::user()->wallet->id;
        if ($transaction->payer_wallet_id !== $userWalletId && $transaction->payee_wallet_id !== $userWalletId) {
            return back()->with('error', 'Você não tem permissão para estornar esta transação.');
        }

        try {
            $dto = new ReverseTransactionDTO($transaction->id);
            $this->reverseTransactionUseCase->execute($dto);
            return back()->with('success', 'Transação estornada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Não foi possível estornar a transação: ' . $e->getMessage());

        }
    }
}
