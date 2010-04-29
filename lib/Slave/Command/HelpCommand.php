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

class HelpCommand extends AbstractCommand {
	public function execute(\Zend_Console_Getopt $opts) {
		$usage = $opts->getUsageMessage();
		$usage = preg_replace('#Usage: (.*?)\\n#', "Usage: slave [ options ] baseURL" . PHP_EOL . PHP_EOL, $usage);
		return $usage;
	}
}
