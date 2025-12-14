<?php

namespace App\Project\ValueObject;

use objects\src\Contract\SomeContract;

final class SomeValueObjectWithScalar implements SomeContract
{
    public function __construct(int $age)
    {
    }
}
