<?php

namespace App\Application\User\Commands;

final class RegisterUserCommand
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
