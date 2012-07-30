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

namespace GitHelper\Git\SubCommands;

/**
 * Git helper extension.
 *
 * @author Steve Buzonas <steve@slbmeh.com>
 */
class Feature extends \GitHelper\Git\SubCommand {
	protected $_requiresCleanTree = true;
	
	protected $_usage = <<<EOT
git helper feature create <name>     creates a new feature branch
git helper feature publish <name>    merges changes with a rebase and pushes to develop branch
git helper feature close <name>      deletes a feature branch
EOT;
	
	public function description() {
		return "description for Feature";
	}
	
	public function create() {
		if ($this->getHelper()->getNumArgs() != 1) {
			$this->usage();
			exit(1);
		}
		
		$branch = 'feature/' . $this->getHelper()->getNextArg();
		
		if (in_array($branch, getAllBranches())) {
			$this->cliPrintLn('The feature already exists.');
			exit(1);
		}
		
		gitAddBranch($branch, getGitConfigValue('githelper.branch.develop'));
	}
	
	public function publish() {
		if ($this->getHelper()->getNumArgs() != 1) {
			$this->usage();
			exit(1);
		}
		
		$feature = $this->getHelper()->getNextArg();
		
		$branch = 'feature/' . $feature;
		
		gitSwitchBranch($branch);
		cliExecOrDie('git pull --rebase . ' . getGitConfigValue('githelper.branch.develop'));
		gitSwitchBranch(getGitConfigValue('githelper.branch.develop'));
		if (1 == getGitConfigValue('githelper.feature.squash')) {
			$squash_msg = $this->cliPrompt('Enter the commit message for the squashed merge');
			if (1 == getGitConfigValue('githelper.feature.prefix')) {
				$squash_msg = '[' . $feature . '] ' . $squash_msg;
			}
			cliExecOrDie('git branch -m ' . $branch . ' githelper/tmp-squash');
			gitAddBranch($branch);
			cliExecOrDie('git merge --no-ff --quiet --squash -m"' .$squash_msg . '"');
			gitSwitchBranch(getGitConfigValue('githelper.branch.develop'));
			cliExecOrDie('git merge --ff-only --quiet');
		} else {
			cliExecOrDie('git merge --ff-only --quiet ' . $branch);
		}
		// remove our branch so we don't have to deal with rebase quirks in the future.
		gitDelBranch($branch, true);
		// add our branch back for further work until we close the feature.
		gitAddBranch($branch);
	}
	
	public function close() {
		if ($this->getHelper()->getNumArgs() != 1) {
			$this->usage();
			exit(1);
		}
		
		$branch = 'feature/' . $this->getHelper()->getNextArg();
		
		gitSwitchBranch($branch);
		exec('git diff ' . getGitConfigValue('githelper.branch.develop') . '..HEAD --name-only', $changes);
		
		if (!empty($changes)) {
			$continue = $this->cliPrompt('You have unmerged changes on "' . $branch . '".  Continue? ', 'y/N');
			if ('y' != $continue) {
				$this->cliPrintLn('Skipping further execution.');
				exit(0);
			}
		}
		
		gitSwitchBranch(getGitConfigValue('githelper.branch.develop'));
		
		gitDelBranch($branch, true);
	}
}
