<?php

/**
 * Test: Nette\Database test bootstrap.
 */

declare(strict_types=1);

$options = Tester\Environment::loadData() + ['user' => null, 'password' => null];

try {
	$connection = new Nette\Database\Connection($options['dsn'], $options['user'], $options['password']);
} catch (PDOException $e) {
	Tester\Environment::skip("Connection to '$options[dsn]' failed. Reason: " . $e->getMessage());
}

if (strpos($options['dsn'], 'sqlite::memory:') === false) {
	Tester\Environment::lock($options['dsn'], TEMP_DIR);
}

$driverName = $connection->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
$cacheMemoryStorage = new Nette\Caching\Storages\MemoryStorage;

$structure = new Nette\Database\Structure($connection, $cacheMemoryStorage);
$conventions = new Nette\Database\Conventions\DiscoveredConventions($structure);
$context = new Nette\Database\Context($connection, $structure, $conventions, $cacheMemoryStorage);
