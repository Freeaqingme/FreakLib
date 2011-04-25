<?php

class Freak_Model_ModelCollection extends ArrayObject {

    public function offsetSet($offset, $value) {
        if(!$value instanceof Freak_Model) {
            throw new Freak_Model_RuntimeException(
                'Tried to add an element that was not of type Freak_Model'
            );
        }

        return parent::offsetSet($offset, $value);
    }

    /**
     * Converts collection to an array
     * @return array
     */
    public function toArray() {
        $out = array();
        foreach($this as $key => $value) {
            $out[$key] = $value->toArray();
        }

        return $out;
    }
}
