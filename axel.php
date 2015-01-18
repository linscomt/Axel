<?php
namespace Axel;
class Axel {
	private $paths = [];
	private $cache;
	private $saveCache = false;
	private $modules = [];
	private $cacheIndex;
	
	public function __construct(\ArrayAccess $cache = null, $cacheIndex = 'axelpaths') {
		$this->cache = $cache;
		$this->cacheIndex = $cacheIndex;
		spl_autoload_register([$this, 'load']);
		$this->paths = ($this->cache && $this->cache[$this->cacheIndex] !== null) ? $this->cache[$this->cacheIndex] : ['axel\module' => __DIR__ . '/module.php', 'axel\module\namespacemap' => __DIR__ .'/modules/namespacemap.php'];
		$this->addModule(new Module\NamespaceMap(__DIR__ . '/modules/', 'Axel\\Modules'));
	}

	public function load($className) {
		$className = trim($className, '\\');
		$classNameLc = strtolower($className);
		if (isset($this->paths[$classNameLc])) {
			if (file_exists($this->paths[$classNameLc])) require_once $this->paths[$classNameLc];
			else {
				$this->saveCache = true;
				unset($this->paths[$classNameLc]);
				//Something changed since the last run, clear the path for the file and try to load it again.
				$this->load($className);
			}
		}
		else {
			foreach ($this->modules as $module) {
				if ($file = $module->locate($className)) {
					$this->paths[$classNameLc] = $file;
					$this->saveCache = true;
					require_once $this->paths[$classNameLc];
					break;
				}
			}
		}
	}

	public function addModule(\Autoload\Module $module) {
		$this->modules[] = $module;
	}

	public function __destruct() {
		if ($this->cache !== null && $this->saveCache) $this->cache[$this->cacheIndex] = $this->paths;
	}
}