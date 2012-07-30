<?php

/*
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 
 *    Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 
 *    Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 
 *    Neither the name of FancyGuy Technologies nor the names of its
 *    contributors may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * Copyright © 2012, FancyGuy Technologies
 * All rights reserved.
 */

namespace GitHelper\Git;

/**
 * Description of SubCommand
 *
 * @author Steve Buzonas <steve@slbmeh.com>
 */
abstract class SubCommand extends Command {
	protected $_usage = "There is no help for this command.";
	protected $_requiresCleanTree = false;
	
	/**
	 * Create a new subcommand instance.
	 * @param \GitHelper\Git\Helper $helper
	 */
	public function __construct(\GitHelper\Git\Helper $helper) {
		$this->_helper = $helper;
	}
	
	public static function builder(\GitHelper\Git\Helper $helper) {
		return new static($helper);
	}
	
	public function run() {
		if ($this->_requiresCleanTree && !isRepoClean()) {
			$this->cliPrintLn('that action cannot be preformed without a clean working tree');
			exit(1);
		}
		$command = $this->getHelper()->getNextArg();
		if (empty($command)) {
			if (is_callable(array($this, 'main'))) {
				$command = 'main';
			} else {
				$this->usage();
				exit(1);
			}
		} else if ("help" == $command) {
			$command = "usage";
		}
		
		if (!is_callable(array($this, $command))) {
			$this->cliPrintLn('Unknown argument: ' . $command);
			$this->usage();
			exit(1);
		}
		
		call_user_func(array($this, $command));
		exit(0);
	}
}
