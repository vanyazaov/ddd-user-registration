<?php

namespace App\UI\WEB\Controllers;

use App\Application\User\Commands\RegisterUserCommand;
use App\Application\User\Handlers\RegisterUserHandler;
use Illuminate\Http\Request;

final class RegisterController
{
    public function __construct(private RegisterUserHandler $handler) {}

    public function showForm()
    {
        return view('web::auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = $this->handler->handle(
            new RegisterUserCommand($data['email'], $data['password'])
        );

        return redirect()->route('register.success')->with('email', $user->email->value);
    }

    public function success(Request $request)
    {
        $email = $request->session()->get('email');

        if (!$email) {
            // Если пользователь зашел напрямую, редирект на форму
            return redirect()->route('register.form');
        }
        return view('web::auth.success', ['email' => $email]);
    }
}
