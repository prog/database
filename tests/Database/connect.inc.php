<?php

/**
 * Test: Nette\Database test bootstrap.
 */

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';


require __DIR__ . '/connect.create.inc.php';


/** Replaces [] with driver-specific quotes */
function reformat($s): string
{
	global $driverName;
	if (is_array($s)) {
		if (isset($s[$driverName])) {
			return $s[$driverName];
		}
		$s = $s[0];
	}
	if ($driverName === 'mysql') {
		return strtr($s, '[]', '``');
	} elseif ($driverName === 'pgsql') {
		return strtr($s, '[]', '""');
	} elseif ($driverName === 'sqlsrv' || $driverName === 'sqlite') {
		return $s;
	} else {
		trigger_error("Unsupported driver $driverName", E_USER_WARNING);
	}
}
