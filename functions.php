<?php

function getCurrentSprint() {
	return cliExecSingleLine('git config --get ' . \FancyGuy\Git\SubCommands\Sprint::SPRINT_SETTING, 1);
}

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

function cliExecSingleLine($command, $line) {
	$output = array();
	$retval = 0;
	exec($command, $output, $retval);
	$line = $line - 1;
	if (count($output) >= $line) {
		return $output[$line];
	}
	return false;
}

function cliExecCheckReturn($command) {
	$output = array();
	$retval = 0;
	exec($command, $output, $retval);
	return $retval;
}