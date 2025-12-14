<?php

namespace App\Project;

use App\Project\Contract\CommandInterface;

final class SomeApplication
{
    /**
     * @param CommandInterface[] $commands
     */
    public function __construct(
        private readonly array $commands
    ) {
    }

    /**
     * @return CommandInterface[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }
}
