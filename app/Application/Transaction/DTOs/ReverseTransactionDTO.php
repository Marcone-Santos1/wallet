<?php

namespace Application\Transaction\DTOs;

class ReverseTransactionDTO
{
    public function __construct(
        public readonly string $originalTransactionId,
    )
    {
    }
}
