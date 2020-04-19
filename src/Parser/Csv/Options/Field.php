<?php

declare(strict_types=1);

namespace App\Parser\Csv\Options;

use RuntimeException;

class Field
{
    public string $name;

    public string $type;

    public bool $nullable;

    public function __construct(string $name, string $type, bool $nullable)
    {
        if (false === Type::isAllowed($type)) {
            throw new RuntimeException("Unsupported type \"{$type}\".");
        }

        $this->name     = $name;
        $this->type     = $type;
        $this->nullable = $nullable;
    }
}
