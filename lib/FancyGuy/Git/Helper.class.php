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

namespace FancyGuy\Git;

/**
 * Description of Helper
 *
 * @author Steve Buzonas <steve@slbmeh.com>
 */
class Helper extends Command {
	private $_arguments;
	
	protected $_usage = <<<EOT
usage: git helper <subcommand>
	
Available subcommands are:
%s
Try 'git helper <subcommand> help' for more details.
EOT;
	
	public function __construct(Array $args) {
		if (empty($args)) {
			$this->usage();
			exit(1);
		}
		$this->_arguments = $args;
		$subcommand = $this->getNextArg();
		call_user_func(array($this, $subcommand));
		exit(0);
	}
	
	public function usage() {
		$modules = getModuleList();
		
		$module_list = "";
		
		foreach($modules as $module => $description) {
			$module_list .= "\t" . $module . "\t" . $description . "\n";
		}
		
		$usage_text = sprintf($this->_usage, $module_list);
		
		$this->cliPrintLn($usage_text);
	}
	
	public function getNextArg() {
		return array_shift($this->_arguments);
	}
	
	public function getNumArgs() {
		return count($this->_arguments);
	}
	
	public function __call($name, $arguments) {
		if (("help" == $name) || ("usage" == $name)) {
			$this->usage();
			exit(0);
		}
		
		$class_name = '\\FancyGuy\\Git\\SubCommands\\' . ucfirst($name);
		
		if (class_exists($class_name) && is_callable(array($class_name, 'builder'))) {
			$subcommand = call_user_func(array($class_name, 'builder'), $this);
			$subcommand->run();
			exit(0);
		}
		
		$this->cliPrintLn('Unrecognized command: ' . $name);
		exit(1);
	}
	
}
