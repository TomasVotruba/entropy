<?php

declare(strict_types=1);

namespace Entropy\Console\Mapper;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Exception\InvalidCommandException;
use Entropy\Console\ValueObject\Argument;
use Entropy\Console\ValueObject\ArgumentsAndOptions;
use Entropy\Console\ValueObject\Option;

final class CommandRunParametersMapper
{
    public function map(CommandInterface $command): ArgumentsAndOptions
    {
        $runReflectionMethod = new \ReflectionMethod($command, 'run');

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

            // resolve comment from a method docblock

            // 1st param is argument by convention
            if ($key === 0) {
                $arguments[] = new Argument($reflectionParameter->getName(), $parameterType->getName());
            } else {
                $options[] = new Option($reflectionParameter->getName(), $parameterType->getName());
            }
        }

        return new ArgumentsAndOptions($arguments, $options);
    }
}
