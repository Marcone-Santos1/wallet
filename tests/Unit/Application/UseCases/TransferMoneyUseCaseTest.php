<?php

use Domain\Transaction\Repositories\TransactionRepositoryInterface;
use Domain\Transaction\Services\TransferDomainService;
use Domain\Wallet\Entities\Wallet;
use Domain\Wallet\Repositories\WalletRepositoryInterface;
use Domain\Wallet\ValueObjects\Money;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use Application\Transaction\DTOs\TransferDTO;
use Application\Transaction\UseCases\TransferMoneyUseCase;

uses(TestCase::class);

beforeEach(function () {
    $this->walletRepository = Mockery::mock(WalletRepositoryInterface::class);
    $this->transactionRepository = Mockery::mock(TransactionRepositoryInterface::class);
    $this->transferDomainService = Mockery::mock(TransferDomainService::class);
});

afterEach(fn() => Mockery::close());

test('it should execute transfer successfully', function () {

    $dto = new TransferDTO(payerId: 1, payeeId: 2, valueInCents: 10000);
    $payerWallet = new Wallet(id: 1, userId: 1, balance: new Money(20000));
    $payeeWallet = new Wallet(id: 2, userId: 2, balance: new Money(5000));

    $this->walletRepository
        ->shouldReceive('findByUserIdWithLock')->with(1)->andReturn($payerWallet);
    $this->walletRepository
        ->shouldReceive('findByUserId')->with(2)->andReturn($payeeWallet);
    $this->transferDomainService
        ->shouldReceive('execute')->once();
    $this->walletRepository
        ->shouldReceive('save')->twice();
    $this->transactionRepository
        ->shouldReceive('create')->once();

    DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
        $callback();
    });

    // Act (Ação)
    $useCase = new TransferMoneyUseCase(
        $this->walletRepository,
        $this->transactionRepository,
        $this->transferDomainService
    );
    $useCase->execute($dto);
});
