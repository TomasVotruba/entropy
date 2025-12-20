<?php

declare(strict_types=1);

namespace Entropy\Console\ValueObject;

final readonly class Option
{
    private string $cliName;

    public function __construct(
        private string $name,
        private string $type,
        private ?string $description = null,
    ) {
        // handle camelCase to kebab-case conversion
        $this->cliName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $name));
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCliName(): string
    {
        return $this->cliName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
