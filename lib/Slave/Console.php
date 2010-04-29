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

class Console {
	public function run() {
		try {
			$opts = new \Zend_Console_Getopt('');
			$opts->addRules(array(
				'user|u=s'	=> 'Username',
				'password|p=s'	=> 'User password',
				'email|e=s'	=> 'User email address',

				'dbuser=s'	=> 'Database username',
				'dbpass=s'	=> 'Database password',
			));

			try {
				$opts->parse();
			} catch (\Zend_Console_Getopt_Exception $e) {
				throw new GetoptException($opts);
			}

			$config = new Configuration;
			if ($user = $opts->getOption('u')) {
				$config->user = $user;
			}
			if ($password = $opts->getOption('p')) {
				if (strlen($password) < 6) {
					throw new \InvalidArgumentException("Supplied user password is too short, must be at least 6 characters.");
				}
				$config->password = $password;
			}
			if ($email = $opts->getOption('e')) {
				$config->email = $email;
			}
			$config->dbUser = 'root';
			$config->dbName = 'phpbb2';

			$config->baseURL = array_shift($opts->getRemainingArgs());
			if ( ! $config->baseURL) {
				throw new GetoptException($opts);
			}

			if ( ! $this->isURL($config->baseURL)) {
				throw new \InvalidArgumentException("Supplied baseURL is invalid");
			}
			if ( ! $this->hasScheme($config->baseURL)) {
				$config->baseURL = "http://{$config->baseURL}";
			}
			if ( ! $this->hasPath($config->baseURL) || substr($config->baseURL, -1) != "/") {
				$config->baseURL .= "/";
			}
			
			var_dump($config);

			$client = new Client($config);
			$client->install();
		} catch (GetoptException $e) {
			// invalid options
			$usage = $e->getUsageMessage();
			$usage = str_replace('[ options ]', '[ options ] baseURL', $e->getUsageMessage());
			echo $usage;
			exit(1);
		} catch (\InvalidArgumentException $e) {
			echo "Error: {$e->getMessage()}\n";
			exit(1);
		} catch (ClientException $e) {
			echo "Error: {$e->getMessage()}\n";
		}
	}
	
	private function isURL($URL) {
		return (bool) parse_url($URL);
	}
	
	private function hasScheme($URL) {
		return (bool) parse_url($URL, PHP_URL_SCHEME);
	}
	
	private function hasPath($URL) {
		return (bool) parse_url($URL, PHP_URL_PATH);
	}
}
