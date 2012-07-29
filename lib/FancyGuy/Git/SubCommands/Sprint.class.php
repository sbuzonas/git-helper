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

namespace FancyGuy\Git\SubCommands;

/**
 * Description of Sprint
 *
 * @author Steve Buzonas <steve@slbmeh.com>
 */
final class Sprint extends \FancyGuy\Git\SubCommand {
	const SPRINT_SETTING = 'githelper.sprint.current';
	
	protected $_usage = <<<EOT
usage:
\tgit helper sprint\tdisplays the current sprint
\tgit helper sprint set\tsets the current sprint
EOT;

	public function description() {
		return "manipulates the current sprint for the repository";
	}
	
	public function main() {
		$this->cliPrintLn(getCurrentSprint());
	}
	
	public function set() {
		if ($this->getHelper()->getNumArgs() != 1) {
			$this->usage();
			exit(1);
		}
		$sprint = $this->getHelper()->getNextArg();
		setGitConfigValue(self::SPRINT_SETTING, $sprint);
		$this->cliPrintLn('Configured repository to use sprint: ' . $sprint);
	}
		
}
