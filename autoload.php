<?php
/**
 * This file is part of Slave.
 *
 * (c) 2010 Igor Wiedler
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require __DIR__ . '/vendor/Symfony/src/Symfony/Foundation/UniversalClassLoader.php';
$loader = new \Symfony\Foundation\UniversalClassLoader;
$loader->registerNamespaces(array(
	'Slave'		=> __DIR__ . '/lib',
	'Symfony'	=> __DIR__ . '/vendor/Symfony/src',
));
$loader->registerPrefixes(array('Zend_' => __DIR__.'/vendor/Zend/library'));
$loader->register();
set_include_path(__DIR__.'/vendor/Zend/library'.PATH_SEPARATOR.get_include_path());
