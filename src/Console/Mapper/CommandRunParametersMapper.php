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

        $paramDescriptions = $this->resolveParamDescriptions($runReflectionMethod);

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
            if ($key === 0) {
                $arguments[] = new Argument($name, $type, $description);
            } else {
                $options[] = new Option($name, $type, $description);
            }
        }

        return new ArgumentsAndOptions($arguments, $options);
    }

    /**
     * @return array<string, string> map: paramName => description
     */
    private function resolveParamDescriptions(\ReflectionMethod $method): array
    {
        $doc = $method->getDocComment();
        if ($doc === false || $doc === '') {
            return [];
        }

        // Match lines like:
        // @param string $name Description...
        // @param int $level
        $pattern = '/@param\s+[^\s]+\s+\$([A-Za-z_][A-Za-z0-9_]*)\s*(.*)$/m';

        if (preg_match_all($pattern, $doc, $matches, PREG_SET_ORDER) !== 1 && $matches === []) {
            return [];
        }

        $descriptions = [];

        foreach ($matches as $match) {
            $paramName = $match[1];
            $desc = trim($match[2] ?? '');

            if ($desc !== '') {
                $descriptions[$paramName] = $desc;
            }
        }

        return $descriptions;
    }
}
