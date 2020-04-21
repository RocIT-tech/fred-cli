<?php

declare(strict_types=1);

namespace App\Cli;

class Terminal
{
    public const SUCCESS = 0;
    public const INVALID_USAGE = 1;
    public const BAD_SAPI = 2;
    public const UNKNOWN_ERROR = 255;

    public static function terminate(int $code): void
    {
        exit($code);
    }
}
