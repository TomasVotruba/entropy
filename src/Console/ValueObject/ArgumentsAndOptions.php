<?php

declare(strict_types=1);

namespace Entropy\Console\ValueObject;

/**
 * @api to be used soon
 */
final class ArgumentsAndOptions
{
    /**
     * @param mixed[] $arguments
     * @param array<string, mixed> $options
     */
    public function __construct(
        private ?string $commandName,
        private array $arguments = [],
        private array $options = []
    ) {
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
}
