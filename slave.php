<?php
/**
 * This file is part of Slave.
 *
 * (c) 2010 Igor Wiedler
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Slave;

if ('cli' != php_sapi_name()) {
	exit('This script must be run from the command line.');
}

require __DIR__ . '/autoload.php';

$console = new Console;
$console->run();
