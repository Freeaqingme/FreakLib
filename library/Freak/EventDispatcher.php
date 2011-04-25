<?php
class Freak_EventDispatcher implements Freak_Singleton {
	protected $_events = array ();
	
	protected $_controllerDirs = null;
	
	protected static $_instance;
	
	public function subscribe($event, $subscription) {
		$this->_events [$event] [] = $subscription;
	}
	
	public function dispatch($eventName, array $params = array()) {
		if(!isset($this->_events [$eventName])) {
		    return array();
		}
		
		$out = array ();
		foreach ( $this->_events [$eventName] as $event ) {
			$object = call_user_func ( $event ['class'] . '::getInstance' );
			
			// Call hook action
			call_user_func_array ( array ($object, $event ['method'] ), array ($params ) );
			$out [] = $this->_renderView ( $object->view, $event ['class'], $event ['method'] );
		}
		
		return $out;
	}
	
	protected function _renderView(Freak_EventDispatcher_View $view, $className, $method) {
		$module = substr ( $className, 0, strpos ( $className, '_' ) );
		$dir = realpath ( $this->_controllerDirs [strtolower ( $module )] . '/../views/hookScripts/' );
		$view->setScriptPath ( $dir );
		return $view->render ( $method . '.phtml' );
	}
	
	protected function __construct() {
		$this->_controllerDirs = Zend_Controller_Front::getInstance ()->getDispatcher ()->getControllerDirectory ();
	}
	
	public static function getInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self ();
		}
		
		return self::$_instance;
	}
}
