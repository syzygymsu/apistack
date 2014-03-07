<?php

namespace Bootstrap;

class AutoLoader {

	private $psr0Dirs = array();

	public function addPsr0Directory($dirName) {
		$this->psr0Dirs[] = rtrim($dirName, DIRECTORY_SEPARATOR);
	}

	public function loadClass($className) {
		$className = ltrim($className, '\\');
		foreach($this->psr0Dirs as $dirName) {
			$fileName = $dirName. DIRECTORY_SEPARATOR. str_replace('\\', DIRECTORY_SEPARATOR, $className). '.php';
			if(is_file($fileName) && is_readable($fileName)) {
				require $fileName;
				return;
			}
		}
	}

}
