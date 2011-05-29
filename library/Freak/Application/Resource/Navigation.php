<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Navigation.php 23772 2011-02-28 21:35:29Z ralph $
 */

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';


/**
 * Resource for setting navigation structure
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Dolf Schimmel
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Freak_Application_Resource_Navigation
    extends Zend_Application_Resource_Navigation
    implements Freak_Application_Resource_ModuleInterface
{
	
	public function addModuleConfig(array $options)
	{
		if(isset($options['pages'])) {
			$this->getContainer()->addPages($options['pages']);
		}
	
        if(isset($options['hookins']))
        {
	        foreach($options['hookins'] as $hookName => $hookin) {
	        	$this->getContainer()
	        		->findOneBy('hook', $hookName)->addPages($hookin);
	        }
        }
        
	}
	
}
