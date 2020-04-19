<?php

declare(strict_types=1);

namespace App\Parser\Csv\Exception;

use App\Exception\FieldCastException;
use RuntimeException;
use Throwable;

class NonNullableFieldException extends RuntimeException implements FieldCastException
{
    private string $fieldName;

    public function __construct(string $fieldName, int $lineNumber, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Field \"{$fieldName}\" cannot be \"null\" at line \"{$lineNumber}\".", $code, $previous);
        $this->fieldName = $fieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
