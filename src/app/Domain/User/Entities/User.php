<?php

namespace App\Domain\User\Entities;

use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use App\Domain\Shared\ValueObjects\Uuid;

final class User
{
    public function __construct(
        public readonly string $id,
        public Email $email,
        public Password $password,
    ) {}
}
