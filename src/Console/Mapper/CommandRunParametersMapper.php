<?php

declare(strict_types=1);

namespace Entropy\Console\Mapper;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Exception\InvalidCommandException;
use Entropy\Console\ValueObject\Argument;
use Entropy\Console\ValueObject\ArgumentsAndOptions;
use Entropy\Console\ValueObject\Option;
use Entropy\Reflection\ParameterDescriptionResolver;

final class CommandRunParametersMapper
{
    public function map(CommandInterface $command): ArgumentsAndOptions
    {
        $runReflectionMethod = new \ReflectionMethod($command, 'run');

        $paramDescriptions = ParameterDescriptionResolver::resolve($runReflectionMethod);

        $arguments = [];
        $options = [];

        foreach ($runReflectionMethod->getParameters() as $key => $reflectionParameter) {
            $parameterType = $reflectionParameter->getType();
            if (! $parameterType instanceof \ReflectionNamedType) {
                throw new InvalidCommandException(sprintf(
                    'Parameter "%s" of command "%s" must have explicit type declaration',
                    $reflectionParameter->getName(),
                    $command->getName()
                ));
            }

            $name = $reflectionParameter->getName();
            $type = $parameterType->getName();
            $description = $paramDescriptions[$name] ?? null;

            // 1st param is argument by convention
            if ($key === 0 && in_array($type, ['string', 'array'], true)) {
                // only string and array are allowed args
                $arguments[] = new Argument($name, $type, $description);
            } else {
                $options[] = new Option($name, $type, $description);
            }
        }

        return new ArgumentsAndOptions($arguments, $options);
    }
}
