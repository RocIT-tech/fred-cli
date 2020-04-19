<?php

declare(strict_types=1);

namespace App\Parser\Csv;

use App\Parser\Csv\Options\Field;

class Options
{
    /**
     * Indexed by field name.
     *
     * @var array<string, Field>
     */
    public array $fields;

    public ?string $aggregateBy;

    public bool $prettify;

    public function __construct()
    {
        $this->fields      = [];
        $this->aggregateBy = null;
        $this->prettify    = false;
    }

    // TODO: check aggregate by is not nullable
}
