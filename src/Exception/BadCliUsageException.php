<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Throwable;
use function trim;

class BadCliUsageException extends RuntimeException
{
    public function __construct(?string $shortName, ?string $longName, int $code = 0, Throwable $previous = null)
    {
        $option = '';
        if (null !== $shortName) {
            $option .= "-{$shortName}";
        }
        $option .= '|';
        if (null !== $longName) {
            $option .= "--{$longName}";
        }
        $option = trim($option, '|');

        parent::__construct("Bad option usage of: \"{$option}\".", $code, $previous);
    }
}
