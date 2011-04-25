<?php
class Freak_Yubikey
{
    public static $modhexMap = array('0' => 'c',
    								 '1' => 'b',
    								 '2' => 'd',
    								 '3' => 'e',
    								 '4' => 'f',
    								 '5' => 'g',
    								 '6' => 'h',
    								 '7' => 'i',
    								 '8' => 'j',
    								 '9' => 'k',
    								 'a' => 'l',
    								 'b' => 'n',
    								 'c' => 'r',
    								 'd' => 't',
    								 'e' => 'u',
    								 'f' => 'v');
    
    public static function modhex ($input)
    {
        return str_replace(array_keys(self::$modhexMap), 
        array_values(self::$modhexMap), $input);
    }

    public static function hexmod ($input)
    {
        return str_replace(array_values(self::$modhexMap), 
        array_keys(self::$modhexMap), $input);
    }
    
    /**
     * @return Freak_Yubikey_Token
     * Enter description here ...
     * @param unknown_type $token
     */
    public static function tokenFactory($token) {
        if (!preg_match( "/^([cbdefghijklnrtuv]{0,16})([cbdefghijklnrtuv]{32})$/",
                        $token,
                        $matches))
        {
            return false;
        }
        
        return new Freak_Yubikey_Token(hexdec(self::hexmod($matches[1])),
                                       self::hexmod($matches[2]));
    }

    public static function hex2bin($h)
    {
        if (!is_string($h)) {
            return null;
        }
        
        $r='';
        for ($a=0; $a<strlen($h); $a+=2) {
            $r .=chr(hexdec($h{$a} . $h{($a+1)}));
        }
        
        return $r;
    }
}
