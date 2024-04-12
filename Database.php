<?php

namespace FpDbTest;

use FpDbTest\App\ConditionalBlocksConverter;
use FpDbTest\App\Enum\SpecifierEnum;
use FpDbTest\App\SpecifierConverter;
use FpDbTest\App\SpecifierFinder;
use mysqli;
use RuntimeException;

readonly class Database implements DatabaseInterface
{
    public function __construct(
        private mysqli $mysqli,
        private SpecifierFinder $specifierFinder,
        private SpecifierConverter $specifierConverter,
        private ConditionalBlocksConverter $conditionalBlocksConverter
    ) {
    }

    public function buildQuery(string $query, array $args = []): string
    {
        $convertedValues = [];
        $specifiers = $this->specifierFinder->find($query);
        if (count($specifiers) === 0) {
            return $query;
        }

        if (count($specifiers) !== count($args)) {
            throw new RuntimeException('Количество спецификаторов не равно количеству переданных аргументов!');
        }

        foreach ($specifiers as $ind => $specifier) {
            $convertedValues[] = $this->specifierConverter->convert($args[$ind], $specifier);
        }

        $replaced = $this->specifierFinder->replace($query, $convertedValues);
        return $this->conditionalBlocksConverter->check($replaced);
    }

    public function skip(): string
    {
        return SpecifierEnum::SKIP_VALUE;
    }
}
