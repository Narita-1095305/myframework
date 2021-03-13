<?php
class Loader{
	protected $_directories = array();

	public function regDirectory($dir){
		$this->_directories[] = $dir;
	}

	public function register(){
		spl_autoload_register(array($this,'requireClsFile'));
	}

	public function requireClsFile($class){
		foreach($this->_directories as $dir){
			$file = $dir .'/'. $class . '.php';

			if(is_readable($file)){
				require $file;
				return;
			}
		}
	}
}
?>