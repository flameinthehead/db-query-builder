<?php

namespace FpDbTest\App\Enum;

class SpecifierEnum
{
    public const string INTEGER = '?d'; // конвертация в целое число
    public const string FLOAT = '?f'; // конвертация в число с плавающей точкой
    public const string ARRAY = '?a'; // массив значений
    public const string IDENTIFIER = '?#'; // идентификатор или массив идентификаторов
    public const string GENERAL = '?'; // спецификатор без типа

    public const string SKIP_VALUE = '###';

    private const array NULLABLE_SPECIFIERS = [
        self::GENERAL,
        self::INTEGER,
        self::FLOAT
    ];

    public function getNullableSpecifiers(): array
    {
        return self::NULLABLE_SPECIFIERS;
    }
}
