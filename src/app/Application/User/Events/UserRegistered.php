<?php

namespace App\Application\User\Events;

use App\Domain\User\Entities\User;

final class UserRegistered
{
    public function __construct(
        public readonly \App\Domain\User\Entities\User $user
    ) {}
}
