<?php

namespace Entropy\Tests\Reflection\ParameterDescriptionResolver\Fixture;

final class SomeClassWithMethod
{
    /**
     * @param string[] $paths
     * @param int $isEnabled Description of the option
     * @param bool $showChanges Show changes, do not apply them.
     */
    public function someMethod(array $paths, int $isEnabled, bool $showChanges)
    {
    }
}
