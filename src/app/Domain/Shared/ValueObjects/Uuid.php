<?php

namespace App\Domain\Shared\ValueObjects;

use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;
use InvalidArgumentException;

final class Uuid
{
    private UuidInterface $value;

    private function __construct(UuidInterface $uuid)
    {
        $this->value = $uuid;
    }

    public static function generate(): self
    {
        return new self(RamseyUuid::uuid4());
    }

    public static function fromString(string $value): self
    {
        if (!RamseyUuid::isValid($value)) {
            throw new InvalidArgumentException("Invalid UUID: $value");
        }

        return new self(RamseyUuid::fromString($value));
    }

    public function value(): string
    {
        return $this->value->toString();
    }

    public function equals(Uuid $other): bool
    {
        return $this->value->equals($other->value);
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
