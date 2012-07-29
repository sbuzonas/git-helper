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

require_once 'helpers/cli.functions.php';
require_once 'helpers/git.functions.php';

function getAvailableModules() {
	$modules = scandir(dirname(__FILE__) . '/lib/FancyGuy/Git/SubCommands');
	
	array_shift($modules); // remove '.' from stack
	array_shift($modules); // remove '..' from stack
	
	$real_modules = array();
	
	while(!empty($modules)) {
		$module = array_shift($modules);
		$module_name = strtolower(str_replace(\FancyGuy\SplAutoloader::CLASS_FILE_EXT, '', $module));
		$module_class = '\\FancyGuy\\Git\\SubCommands\\' . ucfirst($module_name);
		
		if (class_exists($module_class)) {
			$real_modules[] = $module_name;
		}
	}
	
	return $real_modules;
}

function getModuleList() {
	$modules = getAvailableModules();
	
	$module_list = array();
	
	$module_prefix = '\\FancyGuy\\Git\\SubCommands\\';
	
	foreach($modules as $module) {
		$module_class = $module_prefix . ucfirst($module);
		if (is_callable(array($module_class, 'description'))) {
			$module_list[$module] = call_user_func(array($module_class, 'description'));
		}
	}
	
	return $module_list;
}

function getCurrentSprint() {
	return cliExecSingleLine('git config --get ' . \FancyGuy\Git\SubCommands\Sprint::SPRINT_SETTING, 1);
}
