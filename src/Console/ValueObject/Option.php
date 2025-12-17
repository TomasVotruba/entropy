<?php

declare(strict_types=1);

namespace Entropy\Console\ValueObject;

final class Option
{
    private string $cliName;

    public function __construct(
        private string $name,
        private string $type,
    ) {
        $this->cliName = preg_replace('/([a-z])([A-Z])/', '$1-$2', $name);

        dump($this->cliName);
    }
}
