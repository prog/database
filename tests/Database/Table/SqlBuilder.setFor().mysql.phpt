<?php

/**
 * Test: Nette\Database\Table\SqlBuilder: setFor()
 * @dataProvider? ../databases.ini, mysql
 */

declare(strict_types=1);

use Nette\Database\Table\SqlBuilder;
use Tester\Assert;

require __DIR__ . '/../connect.inc.php'; // create $connection


$pdo = $connection->getPdo();
$mysqlBefore8 = version_compare($pdo->getAttribute(PDO::ATTR_SERVER_VERSION), '8', '<');


test(function () use ($context) {
	$sqlBuilder = new SqlBuilder('book', $context);
	$sqlBuilder->setFor('UPDATE');

	Assert::same(reformat('SELECT * FROM [book] FOR UPDATE'), $sqlBuilder->buildSelectQuery());
});


test(function () use ($context) {
	$sqlBuilder = new SqlBuilder('book', $context);
	$sqlBuilder->setFor('UPDATE OF ?name NOWAIT', 'book');

	Assert::same(reformat('SELECT * FROM [book] FOR UPDATE OF ?name NOWAIT'), $sqlBuilder->buildSelectQuery());
});



test(function () use ($context) {
	$sqlBuilder = new SqlBuilder('book', $context);
	$sqlBuilder->setFor('SHARE');
	$sqlBuilder->setFor('UPDATE OF ?name', 'book'); // duplicit calls rewrite old values

	Assert::same(reformat('SELECT * FROM [book] FOR UPDATE'), $sqlBuilder->buildSelectQuery());
});


test(function () use ($context, $mysqlBefore8) {
	$sqlBuilder = new SqlBuilder('book', $context);
	$sqlBuilder->setFor('SHARE');

	$expected = $mysqlBefore8
		? 'SELECT * FROM [book] LOCK IN SHARE MODE'
		: 'SELECT * FROM [book] FOR SHARE';
	Assert::same(reformat($expected), $sqlBuilder->buildSelectQuery());
});
