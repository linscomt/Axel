<?php 
namespace Axel\Module;
class NamespaceMap implements \Autoload\Module {
	private $baseDir;
	private $lowerCaseDirectories;
	private $lowercaseFiles;
	private $namespace;
	
	public function __construct($baseDir, $namespace = null,$lowercaseFiles = true) {
		$this->baseDir = $baseDir;
		$this->lowercaseFiles = $lowercaseFiles;
		$this->namespace = trim($namespace, '\\');
	}
	
	public function locate($className) {
		if ($this->namespace != null) {
			if (strpos(strtolower($className), strtolower($this->namespace)) === 0) {
				$className = str_replace($this->namespace, '', $className);				
			}	
		}
		
		$parts = explode('\\', $className);
		$fileName = array_pop($parts);
		$file = $this->baseDir . DIRECTORY_SEPARATOR .
				($this->lowercaseFiles ? strtolower(implode(DIRECTORY_SEPARATOR, $parts)) : implode(DIRECTORY_SEPARATOR, $parts)) . DIRECTORY_SEPARATOR .
				($this->lowercaseFiles ? strtolower($fileName) : $fileName) . '.php';
		
		if ($file !== null && is_file($file)) return $file;
	}
	
}