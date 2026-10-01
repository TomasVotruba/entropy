<?php

declare(strict_types=1);

namespace Entropy\Tests\Validation;

use Entropy\Validation\Assert;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class AssertTest extends TestCase
{
    public function testStringPasses(): void
    {
        $this->expectNotToPerformAssertions();
        Assert::string($this->mixed('value'));
    }

    public function testStringFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::string($this->mixed(123));
    }

    public function testIsArrayFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::isArray($this->mixed('nope'));
    }

    public function testNotFalseFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::notFalse($this->mixed(false));
    }

    public function testDirectoryFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::directory(__DIR__ . '/non-existing-directory');
    }

    public function testFileExistsFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::fileExists(__DIR__ . '/non-existing-file.php');
    }

    public function testMethodExistsFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::methodExists($this, 'missingMethod');
    }

    public function testAllStringPasses(): void
    {
        $this->expectNotToPerformAssertions();
        Assert::allString($this->mixed(['a', 'b', 'c']));
    }

    public function testAllStringFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::allString($this->mixed(['a', 2]));
    }

    public function testAllIsInstanceOfFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::allIsInstanceOf($this->mixed([new stdClass(), 'not-object']), stdClass::class);
    }

    public function testNotEmptyFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::notEmpty($this->mixed(''));
    }

    public function testIsInstanceOfFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::isInstanceOf($this->mixed('not-object'), stdClass::class);
    }

    public function testAllFileExistsFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::allFileExists($this->mixed([__FILE__, __DIR__ . '/non-existing-file.php']));
    }

    public function testAllDirectoryFails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Assert::allDirectory($this->mixed([__DIR__, __DIR__ . '/non-existing-directory']));
    }

    public function testCustomMessageIsUsed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('custom message');
        Assert::string($this->mixed(1), 'custom message');
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function mixed($value)
    {
        return $value;
    }
}
