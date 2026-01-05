<?php

declare(strict_types=1);

namespace Entropy\Console\Mapper;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Exception\InvalidCommandException;
use Entropy\Console\ValueObject\Argument;
use Entropy\Console\ValueObject\ArgumentsAndOptions;
use Entropy\Console\ValueObject\Option;
use Entropy\Reflection\ParameterDescriptionResolver;
use ReflectionMethod;
use ReflectionNamedType;

final class CommandRunParametersMapper
{
    public function map(CommandInterface $command): ArgumentsAndOptions
    {
        $runReflectionMethod = new ReflectionMethod($command, 'run');

        $paramDescriptions = ParameterDescriptionResolver::resolve($runReflectionMethod);

        $arguments = [];
        $options = [];

        foreach ($runReflectionMethod->getParameters() as $key => $reflectionParameter) {
            $parameterType = $reflectionParameter->getType();
            if (! $parameterType instanceof ReflectionNamedType) {
                throw new InvalidCommandException(sprintf(
                    'Parameter "%s" of command "%s" must have explicit type declaration',
                    $reflectionParameter->getName(),
                    $command->getName()
                ));
            }

            $parameterName = $reflectionParameter->getName();
            $parameterType = $parameterType->getName();

            $description = $paramDescriptions[$parameterName] ?? null;

            // 1st param is argument by convention
            $acceptsMultipleValue = $parameterType === 'array';

            $defaultValue = null;
            if ($reflectionParameter->isDefaultValueAvailable()) {
                $defaultValue = $reflectionParameter->getDefaultValue();

                // not relevant default value
                if ($defaultValue === []) {
                    $defaultValue = null;
                }
            }

            // first param can be an arg by convention, only "string" and "array" are allowed types
            if ($key === 0 && in_array($parameterType, ['string', 'array'], true)) {
                $arguments[] = new Argument($parameterName, $description, $acceptsMultipleValue);
            } else {
                $options[] = new Option(
                    $parameterName,
                    $parameterType,
                    $description,
                    $acceptsMultipleValue,
                    $defaultValue
                );
            }
        }

        return new ArgumentsAndOptions($arguments, $options);
    }
}
