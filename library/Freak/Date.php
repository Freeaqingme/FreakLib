<?php
class Freak_Date extends Zend_Date {
	
    protected static $_instance = null;

    public static function getDateString( $date = null,  $format = '',  $reset = false,  $separator = ' ') {
        if ($date instanceof Zend_Date) {
            $instance = $date;
        } else {
            $instance = self::getInstance ( $date );
        }

        if ($reset) {
            $instance->set ( time () );
        }

        return $instance->get(implode('\''.$separator.'\'',(array)$format));
    }

    public static function getInstance( $param) {
        if(is_array($param) && array_key_exists('date',$param)) {
            $date = $param['date'];
            unset($param['date']);
            list($part,$locale) = array_merge((array)$param, array(null,null));
        } else {
            list($date,$part,$locale) = array_merge((array)$param, array(null,null,null));
        }

        if (self::$_instance === null) {
            self::$_instance = new Zend_Date ( $date, $part, $locale );
        } elseif($date!==null) {

            self::$_instance->set ( $date, $part, $locale );
        }

        return self::$_instance;
    }
}
