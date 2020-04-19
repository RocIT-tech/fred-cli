<?php

declare(strict_types=1);

namespace App\Parser\Csv;

use App\Exception\NonNullableFieldException;
use App\Parser\Csv\Options\Type;
use RuntimeException;
use stdClass;
use function array_flip;
use function array_key_exists;
use function array_keys;
use function array_reduce;
use function fgetcsv;
use function fopen;

class_exists(Tools::class);

class Parser
{
    private Tools $tools;

    public function __construct(?Tools $tools = null)
    {
        $this->tools = $tools ?: new Tools();
    }

    public function __invoke(string $filePath, Schema $schema, Options $options): array
    {
        $delimiter = $this->tools->guessDelimiter($filePath, $schema);

        $openedFile = fopen($filePath, 'rb');

        if (false === $openedFile) {
            throw new RuntimeException("Could not properly read the file at \"{$filePath}\".");
        }

        $rowNumber = 0;
        $result    = [];
        $headers   = [];
        while (false !== ($row = fgetcsv($openedFile, 1000, $delimiter))) {
            $rowNumber++;

            if (1 === $rowNumber) {
                $headers = array_flip($row);

                continue;
            }

            $result[] = array_reduce(
                array_keys($headers),
                static function (stdClass $parsedRow, string $header) use ($headers, $row, $options, $rowNumber): stdClass {
                    $headerIndex = $headers[$header];

                    if (
                        array_key_exists($header, $options->fields) === false
                        && $options->aggregateBy !== $header // Add the aggregate field to be removed later in the script
                    ) {
                        return $parsedRow;
                    }

                    try {
                        $parsedRow->$header = Type::cast($options->fields[$header], $row[$headerIndex]);
                    } catch (NonNullableFieldException $nonNullableFieldException) {
                        throw new Exception\NonNullableFieldException(
                            $nonNullableFieldException->getFieldName(),
                            $rowNumber - 1,
                            $nonNullableFieldException->getCode(),
                            $nonNullableFieldException
                        );
                    }

                    return $parsedRow;
                },
                new stdClass()
            );
        }
        fclose($openedFile);

        if (null !== $options->aggregateBy) {
            $result = array_reduce(
                $result,
                static function (array $result, stdClass $row) use ($options): array {
                    $aggregateField = $options->aggregateBy;
                    $indexKey       = $row->$aggregateField;

                    if (false === array_key_exists($aggregateField, $options->fields)) {
                        unset($row->$aggregateField);
                    }

                    $result[$indexKey] ??= [];

                    $result[$indexKey][] = $row;

                    return $result;
                },
                []
            );
        }

        return $result;
    }
}
