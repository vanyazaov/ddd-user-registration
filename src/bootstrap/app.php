<?php

use App\Application\User\Events\UserRegistered;
use App\Application\User\Listeners\SendWelcomeEmail;
use App\Domain\Shared\Exceptions\BusinessException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withEvents(discover: [
        __DIR__.'/../app/Application/*/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (Throwable $e, $request) {
            // 1️⃣ ValidationException — стандартная обработка Laravel
            if ($e instanceof ValidationException) {
                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput();
            }

            // 2️⃣ BusinessException — безопасно показываем пользователю
            if ($e instanceof BusinessException) {
                if (method_exists($request, 'wantsJson') && $request->wantsJson()) {
                    return response()->json(['error' => $e->getMessage()], 400);
                }

                return redirect()->back()
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
            }

            if (env('APP_DEBUG', false)) {
                // Dev-режим: показываем полное сообщение и stack trace
                if (method_exists($request, 'wantsJson') && $request->wantsJson()) {
                    return response()->json([
                        'error' => $e->getMessage(),
                        'trace' => $e->getTrace(),
                    ], 500);
                }

                // Для WEB
                return response(
                    "<h1>Ошибка разработки</h1><pre>{$e}</pre>",
                    500
                );
            }

            // 3️⃣ Любые другие исключения — внутренняя ошибка
            if (method_exists($request, 'wantsJson') && $request->wantsJson()) {
                return response()->json(['error' => 'Что-то пошло не так. Попробуйте позже.'], 500);
            }

            return response('Произошла непредвиденная ошибка', 500);
        });
    })
    ->withCommands([
        __DIR__.'/../app/UI/CLI/Commands',
    ])
    ->create();
