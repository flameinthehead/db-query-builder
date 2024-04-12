<?php

declare(strict_types=1);

namespace FpDbTest\App;

use FpDbTest\App\Enum\SpecifierEnum;
use FpDbTest\App\Exception\InvalidGeneralType;
use FpDbTest\App\Exception\InvalidNullableValue;
use FpDbTest\App\Exception\InvalidSpecifier;

readonly class SpecifierConverter
{
    public function __construct(private SpecifierEnum $specifierEnum)
    {
    }

    /**
     * @throws InvalidSpecifier
     */
    public function convert(mixed $value, string $specifier): int|float|array|string
    {
        if ($value === SpecifierEnum::SKIP_VALUE) {
            return $value;
        }

        $isNullableSpecifier = $this->isNullableSpecifier($specifier);
        if (is_null($value) && !$isNullableSpecifier) {
            throw new InvalidNullableValue(
                sprintf(
                    'Значение не может быть null. Только значения спецификаторов %s могут быть null',
                    implode($this->specifierEnum->getNullableSpecifiers())
                )
            );
        }

        if ($isNullableSpecifier && is_null($value)) {
            return 'NULL';
        }

        return match ($specifier) {
            SpecifierEnum::INTEGER => (int)$value,
            SpecifierEnum::FLOAT => (float)$value,
            SpecifierEnum::ARRAY => $this->convertArray($value),
            SpecifierEnum::IDENTIFIER => $this->convertIdentifier($value),
            SpecifierEnum::GENERAL => $this->convertGeneral($value),
            default => throw new InvalidSpecifier(sprintf(
                'Недопустимый спецификатор %s при попытке передать значение %s', $specifier, $value
            ))
        };
    }

    private function isNullableSpecifier(string $specifier): bool
    {
        $nullableSpecifiers = $this->specifierEnum->getNullableSpecifiers();
        return in_array($specifier, $nullableSpecifiers, true);
    }

    private function convertArray(array $arrayValue): string
    {
        $output = implode(', ', $arrayValue);
        if ($this->isAssoc($arrayValue)) {
            $parts = [];
            foreach ($arrayValue as $key => $value) {
                $parts[] = $this->shieldIdentifier($key) . ' = ' . $this->convertGeneral($value);
            }
            $output = implode(', ', $parts);
        }

        return $output;
    }

    private function convertIdentifier(string|array $identifierValue): string
    {
        if (is_string($identifierValue)) {
            return $this->shieldIdentifier($identifierValue);
        }

        $output = [];
        foreach ($identifierValue as $value) {
            $output[] = $this->shieldIdentifier($value);
        }

        return implode(', ', $output);
    }

    private function convertGeneral(mixed $value): string|int|float
    {
        return match(gettype($value)) {
            'string' => $this->shieldString((string)$value),
            'integer' => (int)$value,
            'double' => (float)$value,
            'boolean' => $value ? 1 : 0,
            'NULL' => 'NULL',
            default => throw new InvalidGeneralType(
                sprintf('Недопустимый тип для переменной %s - %s', $value, gettype($value))
            )
        };
    }

    private function isAssoc(array $array): bool
    {
        return !(array_keys($array) === range(0, count($array) - 1));
    }

    private function shieldString(string $value): string
    {
        return sprintf("'%s'", $value);
    }

    private function shieldIdentifier(string $value): string
    {
        return sprintf("`%s`", $value);
    }
}
