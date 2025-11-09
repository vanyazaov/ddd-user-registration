<?php

namespace App\Domain\User\Services;

use App\Domain\User\Entities\User;
use App\Domain\User\Exceptions\UserAlreadyExists;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use Ramsey\Uuid\Uuid;

final class RegisterUserService
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function register(string $email, string $plainPassword): User
    {
        if ($this->users->findByEmail($email)) {
            throw new UserAlreadyExists('Email already exists');
        }

        $user = new User(
            id: Uuid::uuid4()->toString(),
            email: new Email($email),
            password: Password::fromPlain($plainPassword),
        );

        $this->users->save($user);

        return $user;
    }
}
