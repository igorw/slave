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
		
		$this->request('config_file', $data);
		$this->request('create_table', $data);
		$this->request('final', $data);
	}
	
	protected function request($sub, $postData) {
		$fullURL = $this->config->baseURL . "install/index.php?mode=install&sub=$sub";
		return $this->doRequest('POST', $fullURL, $postData);
	}
	
	protected function doRequest($method, $url, $postData = array()) {
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL		=> $url,
			CURLOPT_RETURNTRANSFER	=> true,
		));
		if ('POST' == $method) {
			curl_setopt_array($ch, array(
				CURLOPT_POST		=> true,
				CURLOPT_POSTFIELDS	=> $postData,
			));
		}
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
}
