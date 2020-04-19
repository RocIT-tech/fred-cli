<?php

declare(strict_types=1);

namespace App\Tests\unit\Parser\Csv;

use App\Parser\Csv\Schema;
use App\Parser\Csv\Tools;
use App\Tests\UnitTestInterface;
use function assert;
use function file_put_contents;
use function sys_get_temp_dir;
use function uniqid;

class ToolsTest implements UnitTestInterface
{
    public function getValidCsv()
    {
        yield 'simple csv with ;' => [
            'name;id;date
foo;5;2020-05-03
foo;9;2020-05-03
bar;1;2020-03-21
boo;4;2020-03-14
foo;12;2020-05-07
boo;5;2020-02-19
far;10;2020-04-30',
            '### Ceci est un commentaire.
# les lignes vide ne sont pas utilisées

name = string  # oui, il peut y avoir des espaces de chaque coté du "="
id=?int
date=date',
            ';',
        ];

        yield 'simple csv with #' => [
            'name#id#date
foo#5#2020-05-03
foo#9#2020-05-03
bar#1#2020-03-21
boo#4#2020-03-14
foo#12#2020-05-07
boo#5#2020-02-19
far#10#2020-04-30',
            '### Ceci est un commentaire.
# les lignes vide ne sont pas utilisées

name = string  # oui, il peut y avoir des espaces de chaque coté du "="
id=?int
date=date',
            '#',
        ];
    }

    public function getTests(): array
    {
        return [
            ['successfullyFindDelimiter', 'getValidCsv'],
        ];
    }

    public function successfullyFindDelimiter(string $csv, string $rawSchema, string $delimiter): void
    {
        $filePath       = sys_get_temp_dir() . '/' . uniqid('tools_valid_csv', true);
        $schemaFilePath = sys_get_temp_dir() . '/' . uniqid('tools_valid_schema', true);
        file_put_contents($filePath, $csv);
        file_put_contents($schemaFilePath, $rawSchema);

        $schema = Schema::fromFile($schemaFilePath);

        $tools            = new Tools();
        $guessedDelimiter = $tools->guessDelimiter($filePath, $schema);

        assert(
            $delimiter === $guessedDelimiter,
            "Wrong delimiter found. Expected: \"{$delimiter}\" got \"{$guessedDelimiter}\"."
        );
    }
}
