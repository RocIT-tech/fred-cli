<?php

declare(strict_types=1);

namespace App\Cli;

use App\Exception\BadCliUsageException;
use LogicException;
use function array_filter;
use function array_reduce;
use function gettype;
use function is_array;
use function is_string;
use function realpath;

class InputOption
{
    public const MODE_REQUIRED = 1;
    public const MODE_ARRAY = self::MODE_REQUIRED << 1;
    public const MODE_STRING = self::MODE_ARRAY << 1;
    public const MODE_INT = self::MODE_STRING << 1;
    public const MODE_BOOL = self::MODE_INT << 1;
    public const MODE_FILE = self::MODE_BOOL << 1;
    private const MODE_REQUIRE_VALUE = self::MODE_ARRAY +
                                       self::MODE_STRING +
                                       self::MODE_INT +
                                       self::MODE_FILE;

    private ?string $shortName;

    private ?string $longName;

    private int $mode;

    public function __construct(
        ?string $shortName,
        ?string $longName,
        int $mode = 0
    ) {
        $this->shortName = $shortName;
        $this->longName  = $longName;
        $this->mode      = $mode;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getLongName(): ?string
    {
        return $this->longName;
    }

    public function isRequired(): bool
    {
        return ($this->mode & self::MODE_REQUIRED) > 0;
    }

    public function requireValue(): bool
    {
        return ($this->mode & self::MODE_REQUIRE_VALUE) > 0;
    }

    private function computeCliMode(): string
    {
        $cliMode = '';

        if ($this->requireValue() === true) {
            $cliMode .= ':';

            if ($this->isRequired() === false) {
                $cliMode .= ':';
            }
        }

        return $cliMode;
    }

    public function dumpShortNameSyntax(): string
    {
        return "{$this->shortName}{$this->computeCliMode()}";
    }

    public function dumpLongNameSyntax(): string
    {
        return "{$this->longName}{$this->computeCliMode()}";
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function sanitizeRawValue($value)
    {
        if (null === $value) {
            return null;
        }

        if (($this->mode & self::MODE_ARRAY) > 0) {
            if (is_string($value) === true) {
                return $this->parseStringToArray($value);
            }

            if (is_array($value) === true) {
                return array_reduce(
                    $value,
                    function (array $values, $value): array {
                        $parsedValue = $value;
                        if (is_string($value) === true) {
                            $parsedValue = $this->parseStringToArray($value);
                        }

                        return [...$values, ...$parsedValue];
                    },
                    []
                );
            }

            throw new LogicException('Unsupported value type (' . gettype($value) . ') for value.');
        }

        if (is_array($value) === true) {
            throw new BadCliUsageException($this->shortName, $this->longName);
        }

        if (($this->mode & self::MODE_INT) > 0) {
            return (int) $value;
        }

        if (($this->mode & self::MODE_BOOL) > 0) {
            return true;
        }

        if (($this->mode & self::MODE_FILE) > 0) {
            return realpath($value);
        }

        return $value;
    }

    private function parseStringToArray(string $value): array
    {
        $matches = null;
        preg_match_all('#(?P<matched>[A-Za-z]*)#', $value, $matches, PREG_UNMATCHED_AS_NULL);

        return array_filter($matches['matched']);
    }
}
