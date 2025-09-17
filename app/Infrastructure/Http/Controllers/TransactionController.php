<?php

namespace Infrastructure\Http\Controllers;

use Application\Transaction\DTOs\TransferDTO;
use Application\Transaction\UseCases\TransferMoneyUseCase;
use Illuminate\Support\Facades\Auth;
use Infrastructure\Http\Requests\StoreTransactionRequest;

class TransactionController
{
    public function __construct(private TransferMoneyUseCase $transferMoneyUseCase) {}

    public function transfer(StoreTransactionRequest $request)
    {
        try {
            $dto = new TransferDTO(
                payerId: Auth::id(),
                payeeId: $request->input('payee_id'),
                valueInCents: $request->input('value') * 100
            );

            // 2. Executa o Caso de Uso
            $this->transferMoneyUseCase->execute($dto);

            return back()->with('success', 'Transferência realizada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


}
