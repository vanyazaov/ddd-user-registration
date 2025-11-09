<?php

namespace App\Infrastructure\User\Persistence\Eloquent\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Password;
use App\Infrastructure\User\Persistence\Eloquent\Models\EloquentUser;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {
        EloquentUser::updateOrCreate(
            ['id' => $user->id],
            ['email' => $user->email->value, 'password' => $user->password->hashed]
        );
    }

    public function findByEmail(string $email): ?User
    {
        $model = EloquentUser::where('email', $email)->first();
        if (!$model) return null;

        return new User($model->id, new Email($model->email), new Password($model->password));
    }
}
