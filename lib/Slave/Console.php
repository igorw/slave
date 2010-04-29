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
		'version'	=> 'Version information',
	);
	
	public function run() {
		try {
			$opts = $this->setUpOptions();
			$command = $this->route($opts);
			echo $command->execute($opts);
		} catch (\InvalidArgumentException $e) {
			$this->handleException($e);
		} catch (ClientException $e) {
			$this->handleException($e);
		}
	}
	
	private function setUpOptions() {
		$opts = new \Zend_Console_Getopt('');
		$opts->addRules($this->rules);
		
		try {
			$opts->parse();
		} catch (\Zend_Console_Getopt_Exception $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
		
		return $opts;
	}
	
	private function route(\Zend_Console_Getopt $opts) {
		if ($opts->getOption('h') || 1 == $GLOBALS['argc']) {
			return new Command\HelpCommand($opts);
		} else if ($opts->getOption('version')) {
			return new Command\VersionCommand($opts);
		}
		
		return new Command\InstallCommand($opts);
	}
	
	private function handleException($e) {
		echo "Error: {$e->getMessage()}" . PHP_EOL;
		exit(1);
	}
}
