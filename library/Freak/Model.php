<?php

abstract class Freak_Model implements ArrayAccess
{
    protected $_data = array();

    public function __construct(array $data = array(), $forceIntegrity = true) {
        if(!$forceIntegrity) {
            $this->_data = $data + $this->_data;        	
        	return;
        }

/*        if(count(array_diff_key($data,$this->_data))>0) {
            throw new Freak_Model_Mapper_OutOfBoundsException(
                'Invalid key given for model user: '.print_r(array_diff_key($data,$this->_data),1)
            );
        }*/

        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __get($property)
    {
        $method = 'get'.ucfirst($property);
        if(method_exists($this,$method)) {
            return $this->$method();
        }

        if(!array_key_exists($property, $this->_data)) {
            throw new Freak_Model_Mapper_OutOfBoundsException(
                'Unknown property requested: '.(string)$property
            );
        }

        return $this->_data[$property];
    }

    public function __set($key, $value) {
        $method = 'set'.ucfirst($key);
        
        if(substr($key, 0, 8)=='discard_') {
            return;
        }

        if(method_exists($this,$method)) {
            return $this->$method($value);
        }

        if(!array_key_exists($key, $this->_data)) {
            throw new Freak_Model_Mapper_OutOfBoundsException(
                'Unknown property requested: '.(string)$key
            );
        }

        $this->_data[$key] = $value;
    }

    public function forceValues(array $values) {
        foreach($values as $key => $value) {
            $this->_data[$key] = $value;
        }
    }

    public function setId($value) {
    	if(!array_key_exists('id', $this->_data)) {
            throw new Freak_Model_Mapper_OutOfBoundsException(
                'Tried to set an unknown property: '.(string)$key
            );    		
    	}
    	
    	if($this->_data['id'] != null && $this->_data['id'] != (int)$value) {
    		throw new Freak_Model_Mapper_RuntimeException(
    		  'Cannot change id once set'
    		);
    	}
    	
    	$this->_data['id'] = (int)$value;
    }


    public function toArray($ignoreNull = false) {
        $out = array();
        foreach($this->_data as $key => $value) {
            if(!$ignoreNull || $value != null) {
                $out[$key] = $this->$key;
            }
        }
        return $out;
    }
    
    public function toJson($ignoreNull = false) {
    	return Zend_Json::encode($this->toArray($ignoreNull));
    }

    public function offsetExists($offset) {
        try {
            $this->__get ( $offset );
        } catch ( Freak_Model_Mapper_OutOfBoundsException $e ) {
            return false;
        }

        return true;
    }

    public function offsetGet($offset) {
        return $this->__get ( $offset );
    }

   public function offsetSet($offset, $value) {
        throw new Freak_Model_IllegalAccessException ( 'Can\'t set properties through array access' );
    }

    public function offsetUnset($offset) {
        throw new Freak_Model_IllegalAccessException ( 'Can\'t unset properties' );
    }
}
