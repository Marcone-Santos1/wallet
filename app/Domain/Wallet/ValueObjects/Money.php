<?php

namespace Domain\Wallet\ValueObjects;

readonly class Money
{
    // Armazenamos o valor em centavos para evitar problemas com float.
    public function __construct(public int $amountInCents) {}

    public function add(Money $other): self
    {
        return new self($this->amountInCents + $other->amountInCents);
    }

    public function subtract(Money $other): self
    {
        return new self($this->amountInCents - $other->amountInCents);
    }

    public function isLessThan(Money $other): bool
    {
        return $this->amountInCents < $other->amountInCents;
    }
}
