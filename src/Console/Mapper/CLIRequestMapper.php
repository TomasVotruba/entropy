<?php

declare(strict_types=1);

namespace Entropy\Console\Mapper;

use Entropy\Attributes\RelatedTest;
use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Exception\ConsoleInputMappingException;
use Entropy\Console\ValueObject\CLIRequest;
use Entropy\Reflection\ParameterOptionMarkerResolver;
use Entropy\Tests\Console\Mapper\CLIRequestMapperTest;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use Webmozart\Assert\Assert;

#[RelatedTest(CLIRequestMapperTest::class)]
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
        $reflectionMethod = new ReflectionMethod($command, 'run');

        $optionMarkers = ParameterOptionMarkerResolver::resolve($reflectionMethod);

        $args = [];

        $positionals = $cliRequest->getArguments();
        $options = $cliRequest->getOptions();

        $positionIndex = 0;

        /** @var array<string, true> */
        $consumedOptionNames = [];

        foreach ($reflectionMethod->getParameters() as $key => $reflectionParameter) {
            $name = $reflectionParameter->getName();
            $type = $reflectionParameter->getType();

            $isBool = $type instanceof ReflectionNamedType && $type->getName() === 'bool';

            // option name: dryRun → dry-run
            $optionName = $this->camelToKebab($name);

            // enable singular --option to parameter plural $options
            if ($key !== 0 && (string) $reflectionParameter->getType() === 'array' && str_ends_with($optionName, 's')) {
                $optionName = substr($optionName, 0, -1);
            }

            // 1) Options always win (if present)
            if (array_key_exists($optionName, $options)) {
                $value = $cliRequest->option($optionName);

                $consumedOptionNames[$optionName] = true;

                $args[] = $this->castValueByParameterType($value, $type);
                continue;
            }

            // 1b) Param explicitly marked as "@option" must not consume positional arguments
            $isExplicitOption = isset($optionMarkers[$name]);
            if ($isExplicitOption) {
                if ($reflectionParameter->isDefaultValueAvailable()) {
                    $args[] = $reflectionParameter->getDefaultValue();
                    continue;
                }

                if ($isBool) {
                    $args[] = false;
                    continue;
                }

                throw new ConsoleInputMappingException(sprintf(
                    'Missing required value for "%s" (use "--%s" to provide it)',
                    $name,
                    $optionName,
                ));
            }

            // 2) Variadic param: consume all remaining positionals as separate arguments
            if ($reflectionParameter->isVariadic()) {
                $remaining = array_slice($positionals, $positionIndex);
                $positionIndex = count($positionals);

                foreach ($remaining as $remainingValue) {
                    $args[] = $this->castValueByParameterType($remainingValue, $type);
                }

                continue;
            }

            // 3) array-typed param: consume all remaining positionals into a single array argument
            if ($this->isArrayType($reflectionParameter, $type)) {
                $remaining = array_slice($positionals, $positionIndex);
                $positionIndex = count($positionals);

                // report missing value
                if ($remaining === [] && $key === 0) {
                    throw new ConsoleInputMappingException(sprintf('Missing required "%s" argument', $name));
                }

                // keep as array, but still run through cast for consistency
                $args[] = $this->castValueByParameterType($remaining, $type);
                continue;
            }

            // 4) Single positional
            if (! $isBool && isset($positionals[$positionIndex])) {
                $value = $positionals[$positionIndex++];
                $args[] = $this->castValueByParameterType($value, $type);
                continue;
            }

            // 5) Default / bool fallback / required missing
            if ($reflectionParameter->isDefaultValueAvailable()) {
                $value = $reflectionParameter->getDefaultValue();
                $args[] = $this->castValueByParameterType($value, $type, $reflectionParameter->getDefaultValue());
                continue;
            }

            if ($isBool) {
                $args[] = false;
                continue;
            }

            throw new ConsoleInputMappingException(sprintf(
                'Missing required value for "%s" (use "--%s" to provide it)',
                $name,
                $optionName,
            ));
        }

        // 1b) If user passed extra positionals and there was no array/variadic param to collect them, fail loudly
        if ($positionIndex < count($positionals)) {
            $extra = array_slice($positionals, $positionIndex);

            throw new ConsoleInputMappingException(sprintf(
                'Unknown argument%s: %s (tip: use a trailing "array $values" or a variadic "...$values" parameter to collect multiple values)',
                count($extra) > 1 ? 's' : '',
                implode(', ', array_map(static fn (string $v): string => '"' . $v . '"', $extra)),
            ));
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

    private function isArrayType(ReflectionParameter $reflectionParameter, ?ReflectionType $reflectionType): bool
    {
        // variadic is handled separately; here we only mean "one param that receives many values"
        if ($reflectionParameter->isVariadic()) {
            return false;
        }

        return $reflectionType instanceof ReflectionNamedType && $reflectionType->getName() === 'array';
    }

    private function castValueByParameterType(
        mixed $value,
        ?ReflectionType $reflectionType,
        mixed $defaultValue = 'unknown'
    ): mixed {
        if (! $reflectionType instanceof ReflectionNamedType) {
            return $value;
        }

        // fallback to default value if empty
        if ($defaultValue !== 'unknown' && empty($value)) {
            return $defaultValue;
        }

        // special case, use single value if param type is scalar
        if (in_array($reflectionType->getName(), ['string', 'int', 'float', 'bool'], true) && is_array($value)) {
            $value = array_shift($value);
        }

        return match ($reflectionType->getName()) {
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'int' => (int) $value,
            'float' => (float) $value,
            'string' => (string) $value,
            'array' => (array) $value,
            default => $value,
        };
    }
}
