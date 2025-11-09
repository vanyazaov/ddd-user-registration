<?php

namespace App\Domain\Shared\Exceptions;
use Exception;
class BusinessException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Определяет, можно ли показывать пользователю.
     */
    public function isSafeForUser(): bool
    {
        return true;
    }
}
