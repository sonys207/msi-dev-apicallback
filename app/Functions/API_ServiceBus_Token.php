<?php

namespace App\Functions;

class API_ServiceBus_Token
{

    public function __construct()
    {
     
    }

  static function generateSasToken($uri, $sasKeyName, $sasKeyValue) 
{ 
    $targetUri = strtolower(rawurlencode(strtolower($uri))); 
    $expires = time();     
    $expiresInMins = 60; 
    $week = 60*60*24*7;
    $expires = $expires + $week; 
    $toSign = $targetUri . "\n" . $expires; 
    $signature = rawurlencode(base64_encode(hash_hmac('sha256',             
    $toSign, $sasKeyValue, TRUE))); 

    $token = "SharedAccessSignature sr=" . $targetUri . "&sig=" . $signature . "&se=" . $expires .         "&skn=" . $sasKeyName; 
    return $token; 
}

}