<?php

declare(strict_types=1);

namespace Entropy\Console\ValueObject;

use Webmozart\Assert\Assert;

final class CLIRequest
{
    /**
     * @param mixed[] $arguments
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly ?string $commandName,
        private array $arguments = [],
        private array $options = []
    ) {
        Assert::allString(array_keys($options));
    }

    public function getCommandName(): ?string
    {
        return $this->commandName;
    }

    /**
     * @return mixed[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function option(string $name, mixed $default = null): mixed
    {
        return $this->options[$name] ?? $default;
    }

    public function isHelp(): bool
    {
        return $this->commandName === null;
    }

    public function isCommandHelp(): bool
    {
        if ($this->commandName === null) {
            return false;
        }

        return array_intersect(['h', 'help'], array_keys($this->options)) !== [];
    }
}
