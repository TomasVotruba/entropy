<?php

declare(strict_types=1);

namespace Entropy\Console\ValueObject;

use Webmozart\Assert\Assert;

final readonly class ArgumentsAndOptions
{
    /**
     * @param Argument[] $arguments
     * @param Option[] $options
     */
    public function __construct(
        private array $arguments,
        private array $options
    ) {
        Assert::allIsInstanceOf($arguments, Argument::class);
        Assert::allIsInstanceOf($options, Option::class);
    }

    /**
     * @return Argument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return Option[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
