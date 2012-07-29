<?php

class GitHelperWhoami extends GitSubcommand {
	protected $usage = <<<EOF
usage: git helper init
EOF;
	
	public function _main() {
		exec('git config --get user.name', $username);
		exec('git config --get user.email', $email);
		echo $username[0], " <", $email[0], ">\n";
	}
	
	public function builder(GitHelper $helper) {
		return new GitHelperWhoami($helper);
	}
}