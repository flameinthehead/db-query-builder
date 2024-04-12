<?php

declare(strict_types=1);

namespace FpDbTest\App;

readonly class SpecifierFinder
{
    private const string SPECIFIER_PATTERN = '/\?([a-z#]+)?/';

    public function find(string $template): array
    {
        preg_match_all(self::SPECIFIER_PATTERN, $template, $matches);
        if (count($matches[1]) === 0) {
            return [];
        }
        return $matches[0];
    }

    public function replace(string $template, array $convertedValues): string
    {
        $output = $template;
        foreach ($convertedValues as $convertedValue) {
            $output = preg_replace(self::SPECIFIER_PATTERN, (string)$convertedValue, $output, 1);
        }
        return $output;
    }
}
