<?php

namespace App\UI\API\Controllers;

use App\Application\User\Commands\RegisterUserCommand;
use App\Application\User\Handlers\RegisterUserHandler;
use Illuminate\Http\Request;

final class RegisterApiController
{
    public function __construct(private RegisterUserHandler $handler) {}

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = $this->handler->handle(
            new RegisterUserCommand($data['email'], $data['password'])
        );

        return response()->json([
            'status' => 'ok',
            'data' => [
                'id' => $user->id,
                'email' => $user->email->value,
            ],
        ], 201);
    }
}
