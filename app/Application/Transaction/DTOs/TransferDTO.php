<?php

namespace Application\Transaction\DTOs;

class TransferDTO
{
    public function __construct(
        public readonly ?string $payerId,
        public readonly string $payeeId,
        public readonly int $valueInCents
    ) {}
}
