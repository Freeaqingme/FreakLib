<?php

class Freak_Yubikey_Token {
    private $publicId;
    private $cipherAes;
    
    private $cipher;
    
    private $privateId;
    private $useCtr;
    private $tstp; // sessionOffset
    private $sessionCtr;
    
    public function __construct($publicId, $cipherAes) {
        $this->publicId = $publicId;
        $this->cipherAes = $cipherAes;
    }
    
    public function decrypt($aesKey) {
        $this->cipher = bin2hex(
                           mcrypt_ecb(
                              MCRYPT_RIJNDAEL_128,
                              Freak_Yubikey::hex2bin($aesKey),
                              Freak_Yubikey::hex2bin($this->cipherAes), 
                              MCRYPT_DECRYPT,
                              Freak_Yubikey::hex2bin('00000000000000000000000000000000')
                           )
                         );
        $this->parseCipher();
    }
    
    private function parseCipher ()
    {
        $this->privateId = hexdec(substr($this->cipher, 0, 12));
        if(!self::isValidCRC($this->cipher)) {
            return false;
        }

        $this->useCtr = hexdec(substr($this->cipher, 14, 2) . substr($this->cipher, 12, 2));
/*        $this->tstp = ord($ydec["token_decoded_bin"][10])*65536
                    + ord($ydec["token_decoded_bin"][9])*256
                    + ord($ydec["token_decoded_bin"][8]);*/
        $this->tstp = hexdec(substr($this->cipher, 18, 2) // This is wrong, see above
                              . substr($this->cipher, 16, 2) 
                              . substr($this->cipher, 20, 2));

        $this->sessionCtr = hexdec(substr($this->cipher, 22, 2));
    }
    
    private function isValidCRC($token) {
        $crc = 0xffff;
        for ($i = 0; $i < 16; $i ++) {
            $b = hexdec($token[$i * 2] . $token[($i * 2) + 1]);
            $crc = $crc ^ ($b & 0xff);
            for ($j = 0; $j < 8; $j ++) {
                $n = $crc & 1;
                $crc = $crc >> 1;
                if ($n != 0) {
                    $crc = $crc ^ 0x8408;
                }
            }
        }

        return $crc == 0xf0b8;
    }
	/**
     * @return the $publicId
     */
    public function getPublicId ()
    {
        return $this->publicId;
    }

	/**
     * @return the $cipherAes
     */
    public function getCipherAes ()
    {
        return $this->cipherAes;
    }

	/**
     * @return the $cipher
     */
    public function getCipher ()
    {
        return $this->cipher;
    }

	/**
     * @return the $privateId
     */
    public function getPrivateId ()
    {
        return $this->privateId;
    }

	/**
     * @return the $useCtr
     */
    public function getUseCtr ()
    {
        return $this->useCtr;
    }

	/**
     * @return the $tstp
     */
    public function getTimestamp ()
    {
        return $this->tstp;
    }

	/**
     * @return the $sessionCtr
     */
    public function getSessionCtr ()
    {
        return $this->sessionCtr;
    }

}
