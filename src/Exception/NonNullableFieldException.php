<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Throwable;

class NonNullableFieldException extends RuntimeException implements FieldCastException
{
    private string $fieldName;

    public function __construct(string $fieldName, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Field \"{$fieldName}\" cannot be \"null\".", $code, $previous);
        $this->fieldName = $fieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
