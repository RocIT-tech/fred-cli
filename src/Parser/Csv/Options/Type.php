<?php

declare(strict_types=1);

namespace App\Parser\Csv\Options;

use App\Exception\NonNullableFieldException;
use App\Exception\UnsupportedFieldTypeException;
use App\Exception\WrongDateFormatFieldCastException;
use DateTimeImmutable;
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
                throw new NonNullableFieldException($field->name);
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

            $date = DateTimeImmutable::createFromFormat($formatMapping[$type], $value);

            if (false === $date) {
                throw new WrongDateFormatFieldCastException($field->name, $formatMapping[$type], $value);
            }

            return $date;
        }

        throw new UnsupportedFieldTypeException($field->name, $type);
    }
}
