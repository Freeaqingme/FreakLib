<?php

class Freak_Navigation_Page_Combined extends Zend_Navigation_Page_Mvc
{
    protected function _init() {
        $array = explode('_',$this->getRoute(),3);
        $this->setModule(strtolower($array[0]));
        $this->setController(strtolower($array[1]));
        $this->setAction(strtolower($array[2]));

        if($this->getResource()==null) {
            $this->setResource($this->getRoute());
        }
//        $this->_params = $array;
    }

/*    public function getHref()
    {
        if ($this->_hrefCache) {
            return $this->_hrefCache;
        }

        if (null === self::$_urlHelper) {
            self::$_urlHelper =
                Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        }

        $params = $this->getParams();
        $url = self::$_urlHelper->url($params,
                                      $this->getRoute(),
                                      $this->getResetParams());

        return $this->_hrefCache = $url;
    }*/
}
