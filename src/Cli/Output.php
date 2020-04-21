<?php

declare(strict_types=1);

namespace App\Cli;

use InvalidArgumentException;
use function array_map;
use function explode;
use function fwrite;
use function gettype;
use function implode;
use function str_repeat;
use const PHP_EOL;
use const STDOUT;

class Output
{
    public static function writeln(string $message, int $indent = 0, $output = STDOUT): void
    {
        if (false === is_resource($output)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Argument must be a valid resource type. %s given.',
                    gettype($output)
                )
            );
        }

        $spaces          = str_repeat(' ', $indent * 2);
        $lines           = explode("\n", $message);
        $spacedLines     = array_map(static function (string $line) use ($spaces) {
            return "{$spaces}{$line}";
        }, $lines);
        $indentedMessage = implode("\n", $spacedLines);

        fwrite($output, $indentedMessage . PHP_EOL);
    }
}
