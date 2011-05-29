<?php

abstract class Freak_Application_Bootstrap
	extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _executeResource($resourceName)
	{
		$resourceName = strtolower($resourceName);
		parent::_executeResource($resourceName);

		$appOptions = $this->getApplication()->getOptions();
		if(!$this->getContainer()->offsetExists($resourceName)) {
			return;
		}

		$resource = $this->getPluginResource($resourceName);
		if(!$resource instanceof Freak_Application_Resource_ModuleInterface) {
			return;
		}
		
		foreach($appOptions['submodules'] as $submodulename => $options) {
			if(isset($options['resources'][$resourceName])) {
				$resource->addModuleConfig($options['resources'][$resourceName]);
			}
		}
	}
}
