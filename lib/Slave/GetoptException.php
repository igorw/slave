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

class GetoptException extends \Exception {
	private $opts;
	
	public function __construct($opts, $message = null) {
		parent::__construct($message);
		
		$this->opts = $opts;
	}
	
	public function getUsageMessage() {
		return $this->opts->getUsageMessage();
	}
}
