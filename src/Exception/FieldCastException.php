<?php

declare(strict_types=1);

namespace App\Exception;

interface FieldCastException
{
    public function getFieldName(): string;
}
