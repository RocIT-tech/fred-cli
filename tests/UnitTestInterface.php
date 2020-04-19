<?php

declare(strict_types=1);

namespace App\Tests;

interface UnitTestInterface
{
//    public function doTests();

    public function getTests(): array;
}
