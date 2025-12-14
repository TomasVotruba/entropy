<?php

namespace App\Project\Command;

use App\Project\Contract\CommandInterface;
use App\Project\Contract\ServiceTypeInterface;

final class OtherCommand implements CommandInterface
{
    // scalar value? no, just array of services
    /**
     * @param ServiceTypeInterface[] $serviceTypes
     */
    public function __construct(array $serviceTypes)
    {
    }
}
