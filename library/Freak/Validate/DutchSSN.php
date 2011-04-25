<?php
/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';
class Freak_Validate_DutchSSN extends Zend_Validate_Abstract
{
    const INVALID = 'SSNInvalid';
    const NOT_DUTCH_SSN = 'notDutchSSN';

    /**
     * @var array
     */
    protected $_messageTemplates = array(self::INVALID => "Invalid type given, value should only contain 9 numerical characters" ,
                                         self::NOT_DUTCH_SSN => "'%value%' does not appear to be a valid Dutch SSN");
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid Dutch SSN
     *
     * @param  mixed $value
     * @see http://wiki.pfz.nl/Invoer_validatie
     * @return boolean
     */
    public function isValid ($value)
    {
        if (! ctype_digit($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $length = strlen($value);
        if ($length == 8) {
            $value = '0' . (string) $value;
        } elseif ($length != 9) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);
        $aInvalid = array('111111110' , '999999990' , '000000000');

        if (in_array($value, $aInvalid)) {
            $this->_error(self::NOT_DUTCH_SSN);
            return false;
        }

        for ($i = 9, $sum = - $value % 10; $i > 1; $i --) {
            $sum += $i * $value{(9 - $i)};
        }

        if(!($sum % 11 == 0)) {
            $this->_error(self::NOT_DUTCH_SSN);
            return false;
        }

        return true;
    }
}
