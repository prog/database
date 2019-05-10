<?php

/**
 * Test: Nette\Database\Table: Row locking
 * @dataProvider? ../databases.ini, mysql
 */

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/../connect.inc.php'; // create $context
$cx1 = $context;

require __DIR__ . '/../connect.create.inc.php'; // create another $context
$cx2 = $context;


Nette\Database\Helpers::loadFromFile($cx1->getConnection(), __DIR__ . "/../files/{$driverName}-nette_test1.sql");
Nette\Database\Helpers::loadFromFile($cx2->getConnection(), __DIR__ . "/../files/{$driverName}-nette_test1.sql");


test(function () use ($cx1, $cx2) {
	$cx1->beginTransaction();
	$cx2->beginTransaction();

	$cx1->table('book')->where('id', 1)->for("SHARE")->fetch();
	$cx1->table('book')->where('id', 2)->for("UPDATE")->fetch();

	Assert::noError(function() use ($cx2) {
		$cx2->table('book')->where('id', 1)->for("SHARE")->fetch();
	});

	Assert::exception(function() use ($cx2) {
		$cx2->table('book')->where('id', 1)->for("UPDATE NOWAIT")->fetch();
	}, \Nette\Database\LockWaitTimeoutException::class);

	Assert::exception(function() use ($cx2) {
		$cx2->table('book')->where('id', 2)->for("UPDATE NOWAIT")->fetch();
	}, \Nette\Database\LockWaitTimeoutException::class);

	$cx1->rollBack();

	Assert::noError(function() use ($cx2) {
		$cx2->table('book')->where('id', 1)->for("UPDATE")->fetch();
	});

	Assert::noError(function() use ($cx2) {
		$cx2->table('book')->where('id', 2)->for("UPDATE")->fetch();
	});

	$cx2->rollBack();
});
