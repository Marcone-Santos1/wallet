<?php

namespace App\Application\Transaction\UseCases;

use Application\Transaction\DTOs\TransferDTO;
use Domain\Transaction\Repositories\TransactionRepositoryInterface;
use Domain\Wallet\Repositories\WalletRepositoryInterface;
use Domain\Wallet\ValueObjects\Money;
use Exception;
use Illuminate\Support\Facades\DB;

class DepositMoneyUseCase
{
    public function __construct(
        private WalletRepositoryInterface $walletRepository,
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    public function execute(TransferDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            // 1. Buscar a entidade de domínio
            $payerWallet = $this->walletRepository->findByUserIdWithLock($dto->payerId);
            $payeeWallet = $this->walletRepository->findByUserId($dto->payeeId);

            if (!$payerWallet || !$payeeWallet) {
                throw new Exception("Carteira não encontrada.");
            }

            $amount = new Money($dto->valueInCents);

            // 2. Executar a lógica de negócio (creditar o valor)
            $payeeWallet->credit($amount);

            // 3. Persistir o novo estado da carteira
            $this->walletRepository->save($payeeWallet);

            // 4. Usar o TransactionRepository para registrar o depósito
            $this->transactionRepository->create(
                payerWallet: $payerWallet, // Depósito não tem um pagador
                payeeWallet: $payeeWallet,
                amount: $amount,
                type: 'deposit'
            );
        });
    }
}
