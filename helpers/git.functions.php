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

function getGitConfigValue($property, $scope = "local", $file = "") {
	$config_switch = "";
	
	switch ($scope) {
		case "global":
			$config_switch = "--global";
			break;
		case "system":
			$config_switch = "--system";
			break;
		case "local":
			$config_switch = "--local";
			break;
		case "custom":
			$config_switch = "--file " . $file;
			break;
		default:
			throw new InvalidArgumentException();
	}
	
	return cliExecSingleLine('git config --get --' . $scope . ' ' . $property, 1);
}

function setGitConfigValue($property, $value, $scope = "local", $file = "") {
	$config_switch = "";
	
	switch ($scope) {
		case "global":
			$config_switch = "--global";
			break;
		case "system":
			$config_switch = "--system";
			break;
		case "local":
			$config_switch = "--local";
			break;
		case "custom":
			$config_switch = "--file " . $file;
			break;
		default:
			throw new InvalidArgumentException();
	}
	
	exec('git config --' . $scope . ' ' . $property . ' ' . $value);
}

function isRepoClean() {
	$status = cliExecSingleLine('git status', -1);
	if (strstr($status, 'nothing to commit')) {
		return true;
	}
	return false;
}

function getDefaultBranch($branch) {
	$branch_global = getGitConfigValue('githelper.branch.' . $branch, 'global');
	$branch_default = (!empty($branch_global)) ? $branch_global : $branch;
	return $branch_default;
}

function getLocalBranches() {
	exec('git branch', $output);
	
	$branches = array();
	
	foreach ($output as $branch) {
		$split = explode(' ', $branch);
		$branches[] = array_pop($split);
	}
	
	return $branches;
}

function getRemoteBranches() {
	exec('git branch -r', $output);
	
	$branches = array();
	
	foreach ($output as $branch) {
		$split = explode(' ', $branch);
		$branches[] = array_pop($split);
	}
	
	return $branches;
}

function getAllBranches() {
	return array_merge(getLocalBranches(), getRemoteBranches());
}

function gitAddBranch($branch, $tracks = null) {
	if (in_array($branch, getAllBranches())) {
		return false;
	}
	
	if (!empty($tracks) && in_array($tracks, getAllBranches())) {
		exec('git checkout --track -b ' . $branch . ' ' . $tracks);
		return true;
	}
	
	if (in_array(getGitConfigValue('githelper.branch.develop'), getAllBranches())) {
		exec('git checkout --quiet ' . getGitConfigValue('githelper.branch.develop'));
	}
	exec('git checkout --quiet -b ' . $branch);
	return true;
}

function gitDelBranch($branch) {
	$master_branch  = getGitConfigValue('githelper.branch.master');
	$release_branch = getGitConfigValue('githelper.branch.release');
	$develop_branch = getGitConfigValue('githelper.branch.develop');
	$hotfix_branch  = getGitConfigValue('githelper.branch.hotfix');
	
	switch ($branch) {
		case $master_branch:
		case $release_branch:
		case $develop_branch:
		case $hotfix_branch:
			return false;
	}
	
	exec('git checkout --quiet ' . $master_branch);
	exec('git branch -d ' . $branch);
}

function gitSwitchBranch($branch) {
	if (in_array($branch, getAllBranches())) {
		exec('git checkout --quiet ' . $branch);
		return true;
	}
	return false;
}