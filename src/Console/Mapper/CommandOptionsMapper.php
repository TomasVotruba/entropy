<?php

declare(strict_types=1);

namespace Entropy\Console\Mapper;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\ValueObject\ArgumentsAndOptions;
use Webmozart\Assert\Assert;

#[RelatedTest(\Entropy\Tests\Console\Mapper\CommandOptionsMapperTest::class)]
final class CommandOptionsMapper
{
    /**
     * @return mixed[]
     */
    public function resolveArguments(CommandInterface $command, ArgumentsAndOptions $argumentsAndOptions): array
    {
        Assert::methodExists($command, 'run');

        $runMethodReflection = new \ReflectionMethod($command, 'run');

        $args = [];

        $positionals = $argumentsAndOptions->getArguments();
        $posIndex = 0;

        foreach ($runMethodReflection->getParameters() as $parameterReflection) {
            $name = $parameterReflection->getName();
            $type = $parameterReflection->getType();

            $isBool = $type instanceof \ReflectionNamedType
                && $type->getName() === 'bool';

            // option name: dryRun → dry-run
            $optionName = $this->camelToKebab($name);

            if (array_key_exists($optionName, $argumentsAndOptions->getOptions())) {
                $value = $argumentsAndOptions->option($optionName);
            } elseif (! $isBool && isset($positionals[$posIndex])) {
                $value = $positionals[$posIndex++];
            } elseif ($parameterReflection->isDefaultValueAvailable()) {
                $value = $parameterReflection->getDefaultValue();
            } elseif ($isBool) {
                $value = false;
            } else {
                throw new \RuntimeException(sprintf('Missing required argument: %s', $name));
            }

            $args[] = $this->cast($value, $type);
        }

        return $args;
    }

    private function camelToKebab(string $name): string
    {
        return strtolower((string) preg_replace('/[A-Z]/', '-$0', $name));
    }

    private function cast(mixed $value, ?\ReflectionType $type): mixed
    {
        if (! $type instanceof \ReflectionNamedType) {
            return $value;
        }

        return match ($type->getName()) {
            'bool' => filter_var($value, FILTER_VALIDATE_BOOL),
            'int' => (int) $value,
            'float' => (float) $value,
            'string' => (string) $value,
            default => $value,
        };
    }
}
