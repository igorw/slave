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
	public $user = 'root', $password = 'password', $email = 'admin@example.com';
	public $dbUser = '', $dbPassword, $dbName, $dbHost, $dbPort;
	public $dbDriver = 'mysqli', $dbTablePrefix = 'phpbb_';
	public $baseURL;
}
