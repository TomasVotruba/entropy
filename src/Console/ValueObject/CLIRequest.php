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
        private readonly array $arguments = [],
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
     * Re-interpret a leading token (mistaken for a command name) as the first
     * positional argument of the resolved command.
     */
    public function withCommandNameAndPrependedArgument(string $commandName, string $argument): self
    {
        return new self($commandName, [$argument, ...$this->arguments], $this->options);
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

    public function isCommandHelp(): bool
    {
        if ($this->commandName === null) {
            return false;
        }

        return array_intersect(['h', 'help'], array_keys($this->options)) !== [];
    }
}
