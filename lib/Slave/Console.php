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
	private $rules = array(
		'user|u=s'	=> 'Username',
		'password|p=s'	=> 'User password',
		'email|e=s'	=> 'User email address',
		
		'dbuser=s'	=> 'Database username',
		'dbpasswd=s'	=> 'Database password',
		'dbname=s'	=> 'Database name',
		'dbhost=s'	=> 'Database host',
		'dbport=i'	=> 'Database port',
		
		'dbdriver|d=s'	=> 'Database driver',
		'dbprefix=s'	=> 'Database prefix',
		
		'help|h'	=> 'This help',
	);
	
	public function run() {
		try {
			$config = $this->parseOptions();
			
			$client = new Client($config);
			$client->install();
		} catch (GetoptException $e) {
			$usage = $e->getUsageMessage();
			$usage = preg_replace('#Usage: (.*?)\\n#', "Usage: slave [ options ] baseURL" . PHP_EOL . PHP_EOL, $usage);
			echo $usage;
		} catch (\InvalidArgumentException $e) {
			echo "Error: {$e->getMessage()}" . PHP_EOL;
			exit(1);
		} catch (ClientException $e) {
			echo "Error: {$e->getMessage()}" . PHP_EOL;
			exit(1);
		}
	}
	
	private function parseOptions() {
		$config = new Configuration;
		
		$opts = new \Zend_Console_Getopt('');
		$opts->addRules($this->rules);

		try {
			$opts->parse();
		} catch (\Zend_Console_Getopt_Exception $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
		
		if ($opts->getOption('h') || 1 == $GLOBALS['argc']) {
			throw new GetoptException($opts);
		}
		
		if ($user = $opts->getOption('u')) {
			$config->user = $user;
		}
		if ($password = $opts->getOption('p')) {
			if (strlen($password) < 6) {
				throw new \InvalidArgumentException("Supplied user password is too short, must be at least 6 characters");
			}
			$config->password = $password;
		}
		if ($email = $opts->getOption('e')) {
			$validator = new \Zend_Validate_EmailAddress();
			if ( ! $validator->isValid($email)) {
				throw new \InvalidArgumentException("Supplied email address is invalid");
			}
			$config->email = $email;
		}
		
		if ($dbUser = $opts->getOption('dbuser')) {
			$config->dbUser = $dbUser;
		}
		if ($dbPassword = $opts->getOption('dbpasswd')) {
			$config->dbPassword = $dbPassword;
		}
		if ($dbName = $opts->getOption('dbname')) {
			$config->dbName = $dbName;
		} else {
			throw new \InvalidArgumentException("Manditory option --dbname was not supplied");
		}
		if ($dbHost = $opts->getOption('dbhost')) {
			$config->dbHost = $dbHost;
		}
		if ($dbPort = $opts->getOption('dbport')) {
			$config->dbPort = $dbPort;
		}
		
		if ($dbDriver = $opts->getOption('d')) {
			$config->dbDriver = $dbDriver;
		}
		if ($dbPrefix = $opts->getOption('dbprefix')) {
			$config->dbPrefix = $dbPrefix;
		}

		$config->baseURL = array_shift($opts->getRemainingArgs());
		if ( ! $config->baseURL) {
			throw new \InvalidArgumentException("Manditory argument baseURL not supplied");
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
		
		return $config;
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
