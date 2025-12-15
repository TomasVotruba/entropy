<?php

declare(strict_types=1);

namespace Entropy\Console\Contract;

use Entropy\Console\Enum\ExitCode;
use Entropy\Console\ValueObject\ArgumentsAndOptions;

interface CommandInterface
{
    /**
     * @return non-empty-string
     */
    public function getName(): string;

    /**
     * @return non-empty-string
     */
    public function getDescription(): string;

    /**
     * @return ExitCode::*
     */
    public function run(ArgumentsAndOptions $argumentsAndOptions): int;
}
