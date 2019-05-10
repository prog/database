<?php

/**
 * @dataProvider? ../databases.ini, mysql
 */

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/../connect.inc.php'; // create $connection


$driver = $connection->getSupplementalDriver();
$mysqlBefore8 = version_compare($connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), '8', '<');

$query = 'SELECT 1 FROM t';
$driver->applyFor($query, '');
Assert::same('SELECT 1 FROM t', $query);

$query = 'SELECT 1 FROM t';
$driver->applyFor($query, 'UPDATE');
Assert::same('SELECT 1 FROM t FOR UPDATE', $query);

$query = 'SELECT 1 FROM t';
$driver->applyFor($query, 'UPDATE NOWAIT');
Assert::same('SELECT 1 FROM t FOR UPDATE NOWAIT', $query);

$query = 'SELECT 1 FROM t';
$driver->applyFor($query, 'SHARE');
Assert::same($mysqlBefore8 ? 'SELECT 1 FROM t LOCK IN SHARE MODE' : 'SELECT 1 FROM t FOR SHARE', $query);

$query = 'SELECT 1 FROM t';
$driver->applyFor($query, 'SHARE NOWAIT');
Assert::same('SELECT 1 FROM t FOR SHARE NOWAIT', $query);
