<?php
/**
 * This file is part of Slave.
 *
 * (c) 2010 Igor Wiedler
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Slave\Command;

class InstallCommand extends AbstractCommand {
	private $opts;
	private $config;
	
	public function execute(\Zend_Console_Getopt $opts) {
		$this->opts = $opts;
		$this->config = new \Slave\Configuration;
		$this->parseOptions();
		$this->verifyConfig();
		$this->adjustConfig();
		
		$client = new \Slave\Client($this->config);
		$client->install();
	}
	
	private function parseOptions() {
		if ($user = $this->opts->getOption('u')) {
			$this->config->user = $user;
		}
		if ($password = $this->opts->getOption('p')) {
			$this->config->password = $password;
		}
		if ($email = $this->opts->getOption('e')) {
			$this->config->email = $email;
		}

		if ($dbUser = $this->opts->getOption('dbuser')) {
			$this->config->dbUser = $dbUser;
		}
		if ($dbPassword = $this->opts->getOption('dbpasswd')) {
			$this->config->dbPassword = $dbPassword;
		}
		if ($dbName = $this->opts->getOption('dbname')) {
			$this->config->dbName = $dbName;
		}
		if ($dbHost = $this->opts->getOption('dbhost')) {
			$this->config->dbHost = $dbHost;
		}
		if ($dbPort = $this->opts->getOption('dbport')) {
			$this->config->dbPort = $dbPort;
		}

		if ($dbDriver = $this->opts->getOption('d')) {
			$this->config->dbDriver = $dbDriver;
		}
		if ($dbPrefix = $this->opts->getOption('dbprefix')) {
			$this->config->dbPrefix = $dbPrefix;
		}

		$this->config->baseURL = array_shift($this->opts->getRemainingArgs());
		
		// default mysql user to 'root'
		if (!$this->config->dbUser && $this->config->dbDriver == 'mysql') {
			$this->config->dbUser = 'root';
		}
	}
	
	private function verifyConfig() {
		$validDrivers = array('firebird', 'mssql', 'mssql_odbc', 'mssqlnative', 'mysql', 'mysqli', 'oracle', 'postgres', 'sqlite');
		if (!in_array($this->config->dbDriver, $validDrivers)) {
			throw new \InvalidArgumentException("Supplied DBAL driver {$this->config->dbDriver} is invalid" . PHP_EOL . 
				"Valid drivers: " . implode(', ', $validDrivers));
		}
		
		if (strlen($this->config->password) < 6) {
			throw new \InvalidArgumentException("Supplied user password is too short, must be at least 6 characters");
		}
		
		$validator = new \Zend_Validate_EmailAddress();
		if ( ! $validator->isValid($this->config->email)) {
			throw new \InvalidArgumentException("Supplied email address is invalid");
		}

		if ( ! $this->config->dbName && $this->config->dbDriver != 'sqlite') {
			throw new \InvalidArgumentException("Manditory option --dbname was not supplied");
		}
		else if ( ! $this->config->dbHost && $this->config->dbDriver == 'sqlite') {
			throw new \InvalidArgumentException("Manditory sqlite option --dbhost was not supplied");
		}
		
		if ( ! $this->config->baseURL) {
			throw new \InvalidArgumentException("Manditory argument baseURL not supplied");
		}
		else if ( ! $this->isURL($this->config->baseURL)) {
			throw new \InvalidArgumentException("Supplied baseURL is invalid");
		}
	}
	
	private function adjustConfig() {
		if ( ! $this->hasScheme($this->config->baseURL)) {
			$this->config->baseURL = "http://{$this->config->baseURL}";
		}
		if ( ! $this->hasPath($this->config->baseURL) || substr($this->config->baseURL, -1) != "/") {
			$this->config->baseURL .= "/";
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
