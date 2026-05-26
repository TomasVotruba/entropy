<?php

namespace Entropy\Tests\Reflection\ParameterOptionMarkerResolver\Fixture;

final class SomeClassWithOptionMarker
{
    /**
     * @option $source
     * @param string $source The source path
     * @param bool $verbose Be loud
     */
    public function someMethod(string $source, bool $verbose = false): void
    {
    }
}
