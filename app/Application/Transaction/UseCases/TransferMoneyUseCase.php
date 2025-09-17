<?php

namespace Application\Transaction\UseCases;

use Application\Transaction\DTOs\TransferDTO;
use Domain\Transaction\Repositories\TransactionRepositoryInterface;
use Domain\Transaction\Services\TransferDomainService;
use Domain\Wallet\Repositories\WalletRepositoryInterface;
use Domain\Wallet\ValueObjects\Money;
use Exception;
use Illuminate\Support\Facades\DB;

class TransferMoneyUseCase
{
    public function __construct(
        private WalletRepositoryInterface $walletRepository,
        private TransactionRepositoryInterface $transactionRepository,
        private TransferDomainService $transferDomainService
    ) {}

    public function execute(TransferDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $payerWallet = $this->walletRepository->findByUserIdWithLock($dto->payerId);
            $payeeWallet = $this->walletRepository->findByUserId($dto->payeeId);

            if (!$payerWallet || !$payeeWallet) {
                throw new Exception("Carteira não encontrada.");
            }

            $amount = new Money($dto->valueInCents);
            $this->transferDomainService->execute($payerWallet, $payeeWallet, $amount);

            $this->walletRepository->save($payerWallet);
            $this->walletRepository->save($payeeWallet);

            $this->transactionRepository->create(
                $payerWallet,
                $payeeWallet,
                $amount,
                'transfer'
            );
        });
    }
}
