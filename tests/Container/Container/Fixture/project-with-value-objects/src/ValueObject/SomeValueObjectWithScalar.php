<?php

namespace App\Project\ValueObject;

use App\Project\Contract\SomeContract;

final class SomeValueObjectWithScalar implements SomeContract
{
    public function __construct(int $age)
    {
    }
}
