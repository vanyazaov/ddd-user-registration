<?php

namespace App\Application\User\Handlers;

use App\Application\User\Commands\RegisterUserCommand;
use App\Application\User\Events\UserRegistered;
use App\Domain\User\Services\RegisterUserService;

final class RegisterUserHandler
{
    public function __construct(private RegisterUserService $service) {}

    public function handle(RegisterUserCommand $command)
    {
        $user = $this->service->register($command->email, $command->password);

        // Генерируем событие
        event(new UserRegistered($user));

        return $user;
    }
}
