<?php

namespace Slave;

require __DIR__ . '/vendor/Symfony/src/Symfony/Foundation/UniversalClassLoader.php';
$loader = new \Symfony\Foundation\UniversalClassLoader;
$loader->registerNamespaces(array(
	'Slave'		=> __DIR__ . '/lib',
	'Symfony'	=> __DIR__ . '/vendor/Symfony/src',
));
$loader->register();

$config = new Configuration;
$config->user = 'igor';
$config->password = 'password';
$config->email = 'my@email.com';
$config->dbUser = 'root';
$config->dbName = 'phpbb2';
$config->dbDriver = 'mysqli';
$config->baseURL = 'http://localhost/~igor/phpBB2/';

$client = new Client($config);
$client->install();
