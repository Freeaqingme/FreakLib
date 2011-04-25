<?php
class Freak_View_Helper_Date extends Zend_View_Helper_Abstract {
    protected $_date = null;

    public function date($date=null, $format,$separator=' ',$reset=false)
    {
        return Freak_Date::getDateString($date, $format,$reset,$separator);
    }
}
