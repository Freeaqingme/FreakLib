<?php
/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';
class Freak_Validate_DutchBank extends Zend_Validate_Abstract
{
    const INVALID = 'SSNInvalid';
    const NOT_DUTCH_SSN = 'notDutchSSN';
    const POSTBANK_TOO_LONG = 'postbankTooLong';

    /**
     * @var array
     */
    protected $_messageTemplates = array(self::INVALID => "Invalid type given, value should only contain 9 numerical characters" ,
                                         self::NOT_DUTCH_SSN => "'%value%' does not appear to be a valid Dutch Bank Account Number",
                                         self::POSTBANK_TOO_LONG => "'%value%' is too long for a postbank account");
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
        $value = str_replace('.','',$value);
        if (! ctype_digit($value) && !($value[0] == 'P' && ctype_digit(substr($value,1)))) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);
        $length = strlen($value);

        if($value[0] == 'P') {
            if($length > 8) { // 8 = maxlength(7) + 1
                $this->_error(self::POSTBANK_TOO_LONG);
                return false;
            }

            $this->_setValue($value);
            return true;
        }

        if ($length == 10 && $value[0]=='0') {
            $value = substr((string) $value,1);
        } elseif ($length != 9) {
            $this->_error(self::INVALID);
            return false;
        }

        $aInvalid = array('111111110' , '999999990' , '000000000', '123456789');

        if (in_array($value, $aInvalid)) {
            $this->_error(self::NOT_DUTCH_SSN);
            return false;
        }

        for( $i = 9, $sum = 0; $i > 0; $i-- ) {
            $sum += $i * $value{( 9 - $i )};
        }

        if(!($sum % 11 == 0)) {
            $this->_error(self::NOT_DUTCH_SSN);
            return false;
        }

        return true;
    }
}
