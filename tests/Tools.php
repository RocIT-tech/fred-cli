<?php

declare(strict_types=1);

namespace App\Tests;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use function array_reduce;
use function class_exists;
use function get_declared_classes;
use function interface_exists;
use function is_subclass_of;
use function strtr;

class Tools
{
    public static function listClassesImplementing(string $interfaceFQN)
    {
        self::forceAutoloadTestsClasses();

        return array_reduce(
            get_declared_classes(),
            static function (array $foundClasses, $classToTest) use ($interfaceFQN): array {
                if (is_subclass_of($classToTest, $interfaceFQN) === false) {
                    return $foundClasses;
                }

                $foundClasses[] = $classToTest;

                return $foundClasses;
            },
            []
        );
    }

    public static function forceAutoloadTestsClasses(): void
    {
        $path = __DIR__;

        $allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $phpFiles = new RegexIterator($allFiles, '/\.php$/');

        foreach ($phpFiles as $phpFilePath => $phpFile) {
            $relativePath = strtr($phpFilePath, [$path . '/' => '', '.php' => '', '/' => '\\']);
            $classFQN     = __NAMESPACE__ . '\\' . $relativePath;

            class_exists($classFQN);
            interface_exists($classFQN);
        }
    }
}
