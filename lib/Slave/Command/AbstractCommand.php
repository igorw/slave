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

abstract class AbstractCommand {
	abstract public function execute(\Zend_Console_Getopt $opts);
}
