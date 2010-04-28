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

class Configuration {
	public $user, $password, $email;
	public $dbUser, $dbPassword, $dbName, $dbHost, $dbPort;
	public $dbDriver, $dbTablePrefix = 'phpbb_';
	public $baseURL;
}
