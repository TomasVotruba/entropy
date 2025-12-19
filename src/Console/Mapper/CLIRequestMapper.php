<?php

declare(strict_types=1);

namespace Entropy\Console\Mapper;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Exception\ConsoleInputMappingException;
use Entropy\Console\ValueObject\CLIRequest;
use ReflectionNamedType;
use ReflectionType;
use Webmozart\Assert\Assert;

#[RelatedTest(\Entropy\Tests\Console\Mapper\CLIRequestMapperTest::class)]
final class CLIRequestMapper
{
    /**
     * @var array<string, bool>
     */
    private const array IGNORED_OPTIONS = [
        'help' => true,
        'h' => true,
    ];

    /**
     * @return mixed[]
     */
    public function resolveArguments(CommandInterface $command, CLIRequest $cliRequest): array
    {
        Assert::methodExists($command, 'run');
        $reflectionMethod = new \ReflectionMethod($command, 'run');

        $args = [];

        $positionals = $cliRequest->getArguments();
        $options = $cliRequest->getOptions();

        $positionIndex = 0;

        /** @var array<string, true> */
        $consumedOptionNames = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $name = $reflectionParameter->getName();
            $type = $reflectionParameter->getType();

            $isBool = $type instanceof ReflectionNamedType && $type->getName() === 'bool';

            // option name: dryRun → dry-run
            $optionName = $this->camelToKebab($name);

            if (array_key_exists($optionName, $options)) {
                $value = $cliRequest->option($optionName);
                $consumedOptionNames[$optionName] = true;
            } elseif (! $isBool && isset($positionals[$positionIndex])) {
                $value = $positionals[$positionIndex++];
            } elseif ($reflectionParameter->isDefaultValueAvailable()) {
                $value = $reflectionParameter->getDefaultValue();
            } elseif ($isBool) {
                // bool flag missing => false (not required)
                $value = false;
            } else {
                // Required parameter missing: tell user the expected option name too
                throw new ConsoleInputMappingException(sprintf(
                    'Missing required value for "%s" (use "--%s" to provide it)',
                    $name,
                    $optionName,
                ));
            }

            $args[] = $this->castValueByParameterType($value, $type);
        }

        // 2) Extra options (unknown to run() signature) - ignore global ones
        $unknownOptions = array_diff_key($options, $consumedOptionNames, self::IGNORED_OPTIONS);

        if ($unknownOptions !== []) {
            throw new ConsoleInputMappingException(sprintf(
                'Unknown option%s: %s',
                count($unknownOptions) > 1 ? 's' : '',
                implode(', ', array_map(
                    static fn (string $name): string => '"--' . $name . '"',
                    array_keys($unknownOptions)
                ))
            ));
        }

        return $args;
    }

    private function camelToKebab(string $name): string
    {
        return strtolower((string) preg_replace('/[A-Z]/', '-$0', $name));
    }

    private function castValueByParameterType(mixed $value, ?ReflectionType $reflectionType): mixed
    {
        if (! $reflectionType instanceof ReflectionNamedType) {
            return $value;
        }

        return match ($reflectionType->getName()) {
            'bool' => filter_var($value, FILTER_VALIDATE_BOOL),
            'int' => (int) $value,
            'float' => (float) $value,
            'string' => (string) $value,
            'array' => (array) $value,
            default => $value,
        };
    }
}
