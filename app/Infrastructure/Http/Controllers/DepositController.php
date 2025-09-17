<?php

namespace App\Infrastructure\Http\Controllers;

use App\Application\Transaction\UseCases\DepositMoneyUseCase;
use App\Infrastructure\Http\Requests\StoreDepositRequest;
use Application\Transaction\DTOs\TransferDTO;
use Illuminate\Support\Facades\Auth;

class DepositController
{
    public function __construct(private DepositMoneyUseCase $depositMoneyUseCase) {}

    public function deposit(StoreDepositRequest $request)
    {
        try {
            $dto = new TransferDTO(
                payerId: Auth::id(),
                payeeId: $request->get('deposit_payee_id'),
                valueInCents: (int) ($request->validated('value') * 100)
            );

            $this->depositMoneyUseCase->execute($dto);

            return back()->with('success', 'Depósito realizado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


}
