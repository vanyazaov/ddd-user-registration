<?php

namespace App\Domain\User\ValueObjects;

use App\Domain\User\Exceptions\InvalidEmail;

final class Email
{
    public function __construct(public readonly string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmail("Invalid email: $value");
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return strtolower($this->value) === strtolower($other->value);
    }
}
