<?php

declare(strict_types=1);

namespace Entropy\Console\ValueObject;

final readonly class Argument
{
    public function __construct(
        private string $name,
        private string $type,
        private ?string $description = null,
        private bool $acceptsMultipleValues = false,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function doesAcceptMultipleValues(): bool
    {
        return $this->acceptsMultipleValues;
    }
}
