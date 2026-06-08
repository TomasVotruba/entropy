<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\ValueObject;

use Entropy\Console\ValueObject\CLIRequest;
use PHPUnit\Framework\TestCase;

final class CLIRequestTest extends TestCase
{
    public function testWithCommandNameAndPrependedArgument(): void
    {
        $cliRequest = new CLIRequest(null, ['tests'], [
            'fix' => true,
        ]);

        $reinterpreted = $cliRequest->withCommandNameAndPrependedArgument('check', 'src');

        $this->assertSame('check', $reinterpreted->getCommandName());
        $this->assertSame(['src', 'tests'], $reinterpreted->getArguments());
        $this->assertSame([
            'fix' => true,
        ], $reinterpreted->getOptions());
    }
}
