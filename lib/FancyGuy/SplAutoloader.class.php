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

namespace FancyGuy;

require_once 'Exceptions' . DIRECTORY_SEPARATOR . 'ClassNotFoundException.class.php';

/**
 * Description of SplAutoloader
 *
 * @author Steve Buzonas <steve@slbmeh.com>
 */
final class SplAutoloader {
	// change depending on coding standards
	const CLASS_FILE_EXT = '.class.php';
	const INTERFACE_FILE_EXT = '.interface.php';
	
	private $_includePath = "";
	
	public function __construct($include_path = "") {
		if (!empty($include_path)) {
			$this->_includePath = rtrim($include_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		}
	}
	
	public function loadClass($class) {
		if ($file = $this->locateClass($class)) {
			require $file;
			
			if (class_exists($class, false)) {
				return true;
			}
		}
		
		if ($file = $this->locateInterface($class)) {
			require $file;
			
			if (interface_exists($class, false)) {
				return true;
			}
		}
		
		throw new \GitHelper\Exceptions\ClassNotFoundException('Could not locate class "' . $class . '")');
	}
	
	public function autoload($class) {
		try {
			$this->loadClass($class);
		} catch(\GitHelper\Exceptions\ClassNotFoundException $clsnfe) {
			return false;
		}
		
		return true;
	}
	
	public function register($prepend = false) {
		spl_autoload_register(array($this, 'autoload'), true, $prepend);
	}
	
	public function unregister() {
		spl_autoload_unregister(array($this, 'autoload'));
	}
	
	public function locateClass($class) {
		return $this->findFile($class, self::CLASS_FILE_EXT);
	}
	
	public function locateInterface($interface) {
		return $this->findFile($interface, self::INTERFACE_FILE_EXT);
	}
	
	public function findFile($name, $extension) {
		$patterns = array(
		    '/_/',
		    '/\\\\/',
		);
		$filename = $this->_includePath . preg_replace($patterns, DIRECTORY_SEPARATOR, $name);
		
		$filename .= $extension;
		
		if (is_readable($filename)) {
			return $filename;
		}
	}
}