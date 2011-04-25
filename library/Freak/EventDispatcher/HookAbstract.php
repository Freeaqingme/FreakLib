<?php

abstract class Freak_EventDispatcher_HookAbstract
    extends Zend_Controller_Action
    implements Freak_Singleton
{
	protected static $_instance = null;

    public static function getInstance() {
    	$className = get_called_class();
    	if($className::$_instance == null) {
    		$object = new $className(Zend_Controller_Front::getInstance()->getRequest(),
                              		 Zend_Controller_Front::getInstance()->getResponse()
                                           );
            $object->view = new Freak_EventDispatcher_View();
            $className::$_instance = $object;
    	}

    	return $className::$_instance;
    }
}
