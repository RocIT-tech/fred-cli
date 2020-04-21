<?php

declare(strict_types=1);

namespace App\Cli;

use ArrayAccess;
use LogicException;
use function array_key_exists;
use function array_keys;
use function array_reduce;
use function strlen;

class InputOptions implements ArrayAccess
{
    /** @var InputOption[] */
    private array $inputOptions;

    /** @var InputOption[]|null */
    private ?array $shortOptionsCache;

    /** @var InputOption[]|null */
    private ?array $longOptionsCache;

    /**
     * @param InputOption[] $inputOptions
     */
    public function __construct(array $inputOptions)
    {
        $this->inputOptions      = $inputOptions;
        $this->shortOptionsCache = null;
        $this->longOptionsCache  = null;
    }

    private function compileShortOptionsCache(): void
    {
        if (null === $this->shortOptionsCache) {
            $this->shortOptionsCache = array_reduce(
                $this->inputOptions,
                static function (array $shortOptions, InputOption $inputOption): array {
                    if (null === $inputOption->getShortName()) {
                        return $shortOptions;
                    }

                    $shortOptions[$inputOption->getShortName()] = $inputOption;

                    return $shortOptions;
                },
                []
            );
        }
    }

    private function compileLongOptionsCache(): void
    {
        if (null === $this->longOptionsCache) {
            $this->longOptionsCache = array_reduce(
                $this->inputOptions,
                static function (array $longOptions, InputOption $inputOption): array {
                    if (null === $inputOption->getLongName()) {
                        return $longOptions;
                    }

                    $longOptions[$inputOption->getLongName()] = $inputOption;

                    return $longOptions;
                },
                []
            );
        }
    }

    public function offsetExists($offset): bool
    {
        if (strlen($offset) === 1) {
            $this->compileShortOptionsCache();

            return array_key_exists($offset, $this->shortOptionsCache);
        }

        $this->compileLongOptionsCache();

        return array_key_exists($offset, $this->longOptionsCache);
    }

    public function offsetGet($offset)
    {
        if (strlen($offset) === 1) {
            return $this->shortOptionsCache[$offset];
        }

        return $this->longOptionsCache[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new LogicException('Unsupported operation');
    }

    public function offsetUnset($offset): void
    {
        throw new LogicException('Unsupported operation');
    }

    public function dumpShortNameSyntax(): string
    {
        return array_reduce(
            $this->inputOptions,
            static function (string $cliOptions, InputOption $inputOption): string {
                if (null === $inputOption->getShortName()) {
                    return $cliOptions;
                }

                return "{$cliOptions}{$inputOption->dumpShortNameSyntax()}";
            },
            ''
        );
    }

    public function dumpLongNameSyntax(): array
    {
        return array_reduce(
            $this->inputOptions,
            static function (array $cliOptions, InputOption $inputOption): array {
                if (null === $inputOption->getLongName()) {
                    return $cliOptions;
                }

                $cliOptions[] = $inputOption->dumpLongNameSyntax();

                return $cliOptions;
            },
            []
        );
    }

    public function sanitizeRawValues(array $rawValues): array
    {
        $this->compileShortOptionsCache();
        $this->compileLongOptionsCache();

        return array_reduce(
            array_keys($rawValues),
            function (array $options, string $cliOption) use ($rawValues): array {
                $inputOption = $this[$cliOption];

                $options[$cliOption] = $inputOption->sanitizeRawValue($rawValues[$cliOption]);

                return $options;
            },
            []
        );
    }
}
