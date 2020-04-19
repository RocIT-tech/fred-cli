<?php

declare(strict_types=1);

namespace App\Parser\Csv;

use App\Parser\Csv\Options\Field;
use RuntimeException;
use function array_reduce;
use function preg_replace;
use function rtrim;
use function strlen;
use function strtr;
use const PHP_EOL;

class Tools
{
    public function guessDelimiter(string $filePath, Schema $schema): string
    {
        $handle    = fopen($filePath, 'rb');
        $firstLine = fgets($handle);
        fclose($handle);

        if (false === $firstLine) {
            throw new RuntimeException("Could not properly read the file at \"{$filePath}\".");
        }

        $firstLine = rtrim($firstLine, PHP_EOL);

        $fieldsNames = array_reduce(
            $schema->fields,
            static function (array $fieldsNames, Field $field): array {
                $fieldsNames[$field->name] = '';

                return $fieldsNames;
            },
            []
        );

        $delimitersOnly   = strtr($firstLine, $fieldsNames);
        $guessedDelimiter = preg_replace('#(.)\1*#', '$1', $delimitersOnly); // Remove consecutive "same" character

        if (strlen($guessedDelimiter) !== 1) {
            throw new RuntimeException(sprintf(
                'Could not guess the delimiter in file "%s". %d possibilities found: "%s"',
                $filePath,
                strlen($guessedDelimiter),
                $guessedDelimiter
            ));
        }

        return $guessedDelimiter;
    }
}
