<?php

declare(strict_types=1);

namespace App\Parser\Csv\Options;

use DateTimeImmutable;
use RuntimeException;
use function in_array;
use function settype;

class Type
{
    private const ALLOWED_TYPES = [
        'string',
        'int',
        'integer',
        'float',
        'bool',
        'boolean',
        'date',
        'time',
        'datetime',
    ];
    private const SPECIAL_TYPES = [
        'date',
        'time',
        'datetime',
    ];

    public static function isAllowed(string $type): bool
    {
        return true === in_array($type, self::ALLOWED_TYPES, true);
    }

    public static function cast(Field $field, $value)
    {
        if ('' === $value) {
            if (false === $field->nullable) {
                // TODO: custom exception to catch and add line number.
                throw new RuntimeException("Field \"{$field->name}\" cannot be \"null\".");
            }

            return null;
        }

        $type = $field->type;

        if (!in_array($type, self::SPECIAL_TYPES, true)) {
            settype($value, $type);

            return $value;
        }

        if (in_array($type, ['date', 'time', 'datetime',], true)) {
            static $formatMapping = [
                'time'     => 'H:i:s',
                'date'     => 'Y-m-d',
                'datetime' => 'Y-m-d H:i:s',
            ];

            // TODO: check false
            return DateTimeImmutable::createFromFormat($formatMapping[$type], $value);
        }

        throw new RuntimeException("Unsupported type cast: \"{$type}\".");
    }
}
