<?php

namespace App\Application\User\Listeners;

use App\Application\User\Events\UserRegistered;
use Illuminate\Support\Facades\Log;

final class SendWelcomeEmail
{
    public function handle(UserRegistered $event): void
    {
        // $event->user — это только что созданный пользователь
        Log::info("Отправляем приветственное письмо на {$event->user->email->value}");
    }
}
