<?php

declare(strict_types=1);

namespace App\Parser\Csv\Options;

use App\Exception\UnsupportedFieldTypeException;

class Field
{
    public string $name;

    public string $type;

    public bool $nullable;

    public function __construct(string $name, string $type, bool $nullable)
    {
        if (false === Type::isAllowed($type)) {
            throw new UnsupportedFieldTypeException($name, $type);
        }

        $this->name     = $name;
        $this->type     = $type;
        $this->nullable = $nullable;
    }
}
