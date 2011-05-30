<?php
/**
 * @see Zend_Application_Resource_Modules
 */
require_once 'Zend/Application/Resource/Modules.php';


/**
 * Module bootstrapping resource
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Freak_Application_Resource_Modules extends Zend_Application_Resource_Modules
{
  
    protected function bootstrapBootstraps(array $bootstraps)
    {
    	$options = $this->getOptions();
    	
    	if(isset($options['loadModulesCallback'])) {
    		$availableModules = call_user_func($options['loadModulesCallback']);
    		if(is_array($availableModules)) {
	    		foreach($bootstraps as $module => $bootstrap) {
	    			if(!in_array($module, $availableModules)) {
	    				unset($bootstraps[$module]);
	    			}
	    		}
    		}
    	}
    	
    	return parent::bootstrapBootstraps($bootstraps);
    }

}
