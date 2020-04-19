<?php

declare(strict_types=1);

namespace App\Parser\Csv;

use App\Parser\Csv\Options\Field;
use RuntimeException;
use function array_combine;
use function array_fill;
use function array_intersect_key;
use function array_keys;
use function array_reduce;
use function count;
use function parse_ini_file;
use function preg_match;
use function var_dump;

class Schema
{
    /**
     * Indexed by field name.
     *
     * @var array<string, Field>
     */
    public array $fields;

    public function __construct()
    {
        $this->fields = [];
    }

    public static function fromFile(string $filePath): self
    {
        // WARNING. It is not exactly a .ini file. It should use ; instead of # for comments
        $parsedSchema = parse_ini_file($filePath);

        /** @var Field[] $fields */
        $fields = array_reduce(
            array_keys($parsedSchema),
            static function (array $fields, string $name) use ($parsedSchema) {
                $value = $parsedSchema[$name];

                $matched = null;
                preg_match('/(?P<nullable>^\??)(?P<type>[A-Za-z]*)/', $value, $matched);

                $type     = $matched['type'];
                $nullable = '?' === $matched['nullable'];

                if ('' === $type) {
                    throw new RuntimeException("Could not parse the value for key \"{$name}\".");
                }

                $fields[$name] = new Field($name, $type, $nullable);

                return $fields;
            },
            []
        );

        $schema         = new self();
        $schema->fields = $fields;

        return $schema;
    }

    /**
     * @param string[] $fieldsNames
     *
     * @return Field[]
     */
    public function listFieldsFrom(array $fieldsNames): array
    {
        return array_intersect_key(
            $this->fields,
            array_combine(
                $fieldsNames,
                array_fill(0, count($fieldsNames), true)
            )
        );
    }
}
