<?php

namespace App\Domain\User\ValueObjects;

use App\Domain\User\Exceptions\InvalidPassword;

final class Password
{
    public function __construct(public readonly string $hashed)
    {
        if (strlen($hashed) < 10) {
            throw new InvalidPassword("Password hash too short");
        }
    }

    public static function fromPlain(string $plain): self
    {
        if (strlen($plain) < 6) {
            throw new InvalidPassword("Password too short");
        }
        return new self(password_hash($plain, PASSWORD_BCRYPT));
    }
}
