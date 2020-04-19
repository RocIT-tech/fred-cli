<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Throwable;

class WrongDateFormatFieldCastException extends RuntimeException implements FieldCastException
{
    private string $fieldName;

    public function __construct(
        string $fieldName,
        string $format,
        string $value,
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct("Unsupported date format \"{$format}\" for value \"{$value}\" on field \"{$fieldName}\".", $code, $previous);
        $this->fieldName = $fieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
