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

    /**
     * @option $config -c
     * @option $dryRun -n
     * @option $source
     */
    public function someMethodWithShortAliases(string $source, string $config = '', bool $dryRun = false): void
    {
    }
}
