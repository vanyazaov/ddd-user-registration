<?php

namespace App\UI\CLI\Commands;

use App\Application\User\Commands\RegisterUserCommand as RegisterUserCmd;
use App\Application\User\Handlers\RegisterUserHandler;
use Illuminate\Console\Command;

final class RegisterUserCommand extends Command
{
    protected $signature = 'user:register {email} {password}';
    protected $description = 'Register a user via CLI';

    public function __construct(private RegisterUserHandler $handler)
    {
        parent::__construct();
    }

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = $this->handler->handle(new RegisterUserCmd($email, $password));

        $this->info("User registered: {$user->email->value}");
    }
}
