<?php

declare(strict_types=1);

namespace Entropy\Console\ValueObject;

final readonly class Option
{
    private string $name;

    public function __construct(
        string $name,
        private ?string $description = null,
        private bool $acceptsMultipleValues = false,
        private string|bool|int|null $defaultValue = null,
    ) {
        // rename parameter name to -- option name, camelCase to kebab-case conversion
        $this->name = strtolower((string) preg_replace('/([a-z])([A-Z])/', '$1-$2', $name));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDefaultValue(): int|string|bool|null
    {
        return $this->defaultValue;
    }

    public function doesAcceptMultipleValues(): bool
    {
        return $this->acceptsMultipleValues;
    }
}
