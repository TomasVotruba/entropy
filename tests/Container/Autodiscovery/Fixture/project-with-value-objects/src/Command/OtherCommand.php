<?php

declare(strict_types=1);

namespace App\Project\Command;

use App\Project\Contract\ServiceTypeInterface;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Validation\Assert;

final class OtherCommand implements CommandInterface
{
    /**
     * @param ServiceTypeInterface[] $serviceTypes
     */
    public function __construct(array $serviceTypes)
    {
        Assert::allIsInstanceOf($serviceTypes, ServiceTypeInterface::class);
    }

    public function getName(): string
    {
        return 'other-command';
    }

    public function getDescription(): string
    {
        return 'This is an other command.';
    }

    public function run(): int
    {
        return ExitCode::SUCCESS;
    }
}
