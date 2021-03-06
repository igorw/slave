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

/**
* the installer
* great thanks to chris
* http://github.com/cs278/phpbb-vm-builder/raw/master/etc/phpbb-vm-builder/auto.php
*/
class Client {
	private $config;
	
	public function __construct(Configuration $config) {
		$this->config = $config;
	}
	
	public function install() {
		$data = array();
		
		$data = array_merge($data, array(
			'dbhost'	=> $this->config->dbHost,
			'dbport'	=> $this->config->dbPort,
			'dbname'	=> $this->config->dbName,
			'dbuser'	=> $this->config->dbUser,
			'dbpasswd'	=> $this->config->dbPassword,
			'dbms'		=> $this->config->dbDriver,
		));
		
		$data = array_merge($data, array(
			'default_lang'	=> 'en',
			'admin_name'	=> $this->config->user,
			'admin_pass1'	=> $this->config->password,
			'admin_pass2'	=> $this->config->password,
			'board_email1'	=> $this->config->email,
			'board_email2'	=> $this->config->email,
		));
		
		$parseURL = parse_url($this->config->baseURL);
		
		$data = array_merge($data, array(
			'email_enable'		=> false,
			'smtp_delivery'		=> false,
			'smtp_host'		=> '',
			'smtp_auth'		=> '',
			'smtp_user'		=> '',
			'smtp_pass'		=> '',
			'cookie_secure'		=> false,
			'force_server_vars'	=> false,
			'server_protocol'	=> $parseURL['scheme'] . '://',
			'server_name'		=> 'localhost',
			'server_port'		=> isset($parseURL['port']) ? (int) $parseURL['port'] : 80,
			'script_path'		=> $parseURL['path'],
		));
		
		/*
		15		img_imagick	hidden
		16		ftp_path	hidden
		17		ftp_user	hidden
		18		ftp_pass	hidden
		*/
		
		$response = $this->request('install');
		if (preg_match('#Fatal installation error#', $response->getBody())) {
			throw new ClientException('phpBB is already installed');
		}
		else if ($response->getStatus() != 200) {
			throw new ClientException("Request resulted in status code {$response->getStatus()}");
		}
		else if ( ! preg_match('#Welcome to Installation#', $response->getBody())) {
			throw new ClientException('baseURL is not a phpBB');
		}
		
		$response = $this->doRequest($this->config->baseURL . 'index.php', null, true);
		if (preg_match('#The config.php file could not be found.#', $response->getBody())) {
			throw new ClientException('config.php does not exist, create it');
		}
		else if ( ! $response->isRedirect()) {
			throw new ClientException('phpBB is already installed');
		}
		
		$response = $this->request('requirements');
		if (preg_match('#<dt>config.php:</dt>\s+<dd><strong style="color:green">Found</strong>, <strong style="color:red">Unwritable</strong></dd>#s', $response->getBody())) {
			throw new ClientException('config.php is not writable');
		}
		
		$response = $this->request('database', array_merge($data, array('testdb' => 1)));
		if (preg_match('#<strong style="color:red">(.*?)</strong>#s', $response->getBody(), $matches)) {
			$dbError = $matches[1];
			$dbError = str_replace('<br />', " ", $dbError);
			$dbError = htmlspecialchars_decode($dbError);
			throw new ClientException($dbError);
		}
		
		$this->request('config_file', $data);
		$this->request('create_table', $data);
		$this->request('final', $data);
	}
	
	protected function request($sub, $postData = null) {
		$fullURL = $this->config->baseURL . "install/index.php?mode=install&sub=$sub";
		return $this->doRequest($fullURL, $postData);
	}
	
	protected function doRequest($URI, $postData = null, $noRedirect = false) {
		try {
			$client = new \Zend_Http_Client($URI);
			if ($postData) {
				$client->setMethod(\Zend_Http_Client::POST);
				$client->setParameterPost($postData);
			}
			if ($noRedirect) {
				$client->setConfig(array('maxredirects' => 0));
			}
			$response = $client->request();

			return $response;
		} catch (\Zend_Http_Exception $e) {
			throw new ClientException("Could not connect to $URI (Zend_Http_Exception)");
		}
	}
}
