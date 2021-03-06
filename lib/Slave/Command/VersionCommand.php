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

class VersionCommand extends AbstractCommand {
	public function execute(\Zend_Console_Getopt $opts) {
		return "Slave 0.0.1" . PHP_EOL;
	}
}
