<?php

namespace Application\Transaction\UseCases;

use App\Domain\Transaction\Services\ReversalDomainService;
use Application\Transaction\DTOs\ReverseTransactionDTO;
use Domain\Transaction\Repositories\TransactionRepositoryInterface;
use Domain\Wallet\Repositories\WalletRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class ReverseTransactionUseCase
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private WalletRepositoryInterface $walletRepository,
        private ReversalDomainService $reversalDomainService
    ) {}

    public function execute(ReverseTransactionDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            // 1. Validar a transação original
            $originalTransaction = $this->transactionRepository->findById($dto->originalTransactionId);

            if (!$originalTransaction) {
                throw new Exception("Transação original não encontrada.");
            }

            if ($this->transactionRepository->hasBeenReversed($dto->originalTransactionId)) {
                throw new Exception("Esta transação já foi estornada.");
            }
            // 2. Buscar as carteiras (invertendo os papéis)
            $payerWallet = $this->walletRepository->findByUserIdWithLock($originalTransaction->payeeWallet->userId);
            $payeeWallet = $this->walletRepository->findByUserId($originalTransaction->payerWallet->userId);

            if (!$payerWallet || !$payeeWallet) {
                throw new Exception("Carteira não encontrada para o estorno.");
            }

            if ($payerWallet->id === $payeeWallet->id) {
                throw new Exception("Você não pode estornar para si mesmo.");
            }

            // 3. Executar a lógica de domínio do estorno
            $this->reversalDomainService->execute($payerWallet, $payeeWallet, $originalTransaction->amount);

            // 4. Persistir o novo estado das carteiras
            $this->walletRepository->save($payerWallet);
            $this->walletRepository->save($payeeWallet);

            // 5. Criar a nova transação de estorno, referenciando a original
            $this->transactionRepository->create(
                payerWallet: $payerWallet,
                payeeWallet: $payeeWallet,
                amount: $originalTransaction->amount,
                type: 'reversal',
                reversedTransactionId: $originalTransaction->id
            );
        });
    }
}
