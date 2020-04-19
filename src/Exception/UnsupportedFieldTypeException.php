<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Throwable;

class UnsupportedFieldTypeException extends RuntimeException implements FieldCastException
{
    private string $fieldName;

    public function __construct(string $fieldName, string $type, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unsupported type \"{$type}\" for field \"{$fieldName}\" in schema declaration.", $code, $previous);
        $this->fieldName = $fieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
