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
	public function execute(\Zend_Console_Getopt $opts) {
		$config = $this->parseOptions($opts);
		
		$client = new \Slave\Client($config);
		$client->install();
	}
	
	private function parseOptions(\Zend_Console_Getopt $opts) {
		$config = new \Slave\Configuration;

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
