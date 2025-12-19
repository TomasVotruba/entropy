<?php

namespace Entropy\Tests\Reflection\ParameterDescriptionResolver\Fixture;

final class SomeClassWithMethod
{
    /**
     * @param int $isEnabled Description of the option
     */
    public function someMethod(int $isEnabled)
    {
    }
}
