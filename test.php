<?php

use FpDbTest\App\ConditionalBlocksConverter;
use FpDbTest\App\Enum\SpecifierEnum;
use FpDbTest\App\SpecifierConverter;
use FpDbTest\App\SpecifierFinder;
use FpDbTest\Database;
use FpDbTest\DatabaseTest;

spl_autoload_register(function ($class) {
    $class = str_replace('\\App\\', '\\src\\', $class);
    $a = array_slice(explode('\\', $class), 1);
    if (!$a) {
        throw new Exception();
    }
    $filename = implode('/', [__DIR__, ...$a]) . '.php';
    require_once $filename;
});

$mysqli = @new mysqli('localhost', 'root', 'password', 'simple_my_db', 3306);
if ($mysqli->connect_errno) {
    throw new Exception($mysqli->connect_error);
}

$specifierEnum = new SpecifierEnum();
$specifierConverter = new SpecifierConverter($specifierEnum);
$specifierFinder = new SpecifierFinder();
$conditionalBlocksConverter = new ConditionalBlocksConverter();

$db = new Database($mysqli, $specifierFinder, $specifierConverter, $conditionalBlocksConverter);
$test = new DatabaseTest($db);
$test->testBuildQuery();

exit('OK');
