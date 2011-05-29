<?php

require_once 'Zend/Application.php';

class Freak_Application extends Zend_Application
{
	protected $moduleDirectory;
	
	protected $submoduleConfigName = 'config/module.php';
	
    /**
     * Merge options recursively
     *
     * @param  array $array1
     * @param  mixed $array2
     * @return array
     */
    public function mergeOptions(array $array1, $array2 = null)
    {
    	return array_merge_recursive($array1, $array2);
    }
    
    public function getSubmoduleConfigName()
    {
    	return $this->submoduleConfigName;
    }
	
	/**
     * Set application options
     *
     * @param  array $options
     * @throws Zend_Application_Exception When no bootstrap path is provided
     * @throws Zend_Application_Exception When invalid bootstrap information are provided
     * @return Zend_Application
     */
    public function setOptions(array $options)
    {
    	parent::setOptions($options);
    	$this->setModuleDirectory($options['moduleDirectory']);
    	
    	// Get Modules
    	$submoduleConfigName = $this->getSubmoduleConfigName();
		$it = new FilesystemIterator($this->getModuleDirectory());
		$it->setFlags(FilesystemIterator::SKIP_DOTS);
		foreach ($it as $path => $fileinfo) {
			if(!is_readable($path . '/' . $submoduleConfigName)) {
				continue;
			}

			$this->_options['submodules'][$fileinfo->getFileName()] =
				$this->_loadConfig($path . '/' . $submoduleConfigName);
		}
    	
        return $this;
    }
    
    protected function setModuleDirectory($dir)
    {
    	$this->moduleDirectory = (string) $dir;
    }
    
    public function getModuleDirectory()
    {
    	return $this->moduleDirectory;
    }
}
