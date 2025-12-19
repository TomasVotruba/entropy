<?php

namespace Entropy\Tests\Reflection\ParameterDescriptionResolver\Fixture;

final class SomeClassWithMethod
{
    /**
     * @param int $isEnabled Description of the option
     * @param bool $showChanges Show changes, do not apply them.
     */
    public function someMethod(int $isEnabled, bool $showChanges)
    {
    }
}
