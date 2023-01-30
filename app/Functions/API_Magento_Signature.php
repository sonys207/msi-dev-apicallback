<?php

namespace App\Functions;

class API_Magento_Signature
{

    public function __construct()
    {
     
    }

  static function generateMagentoSignature($oauth_nonce,$oauth_timestamp,$HTTP_method,$URL,$requestParams=[]) 
  { 

      $consumer_secret = "kvs5uz0uiry94tp2x0n99oydujf9fs97";
      $access_token_secret = "s8cj1cx7wpmp2yg7mddfnv2lzuicijy4";
   
      $oauthParams = [
          'oauth_nonce' => $oauth_nonce,
          'oauth_signature_method' => 'HMAC-SHA256',
          'oauth_timestamp' => $oauth_timestamp, 
          'oauth_version' => '1.0',
          'oauth_consumer_key' => 'h0e4me7qdycmggaqif2wsydcekjknu6n',
          'oauth_token' => 'lpdsohe1u9oqbxmpc2vp9du5u29nu5yh'
      ];
      
      $params = array_merge($requestParams, $oauthParams);

      ksort($params);
      $signData = [];
      foreach ($params as $key => $value) {
          $signData[] = $key . '=' . $value;
      }

      $HTTP_method_urlencode = urlencode($HTTP_method);
      $URL_urlencode = urlencode($URL);
   
      $URL_data = $HTTP_method_urlencode . "&" . $URL_urlencode . "&" . urlencode(implode('&', $signData));
      $Secert_Key = $consumer_secret . "&" . $access_token_secret;
      $signature = hash_hmac('sha256', $URL_data, $Secert_Key, true);
      $signatureEncode64 = base64_encode($signature);
      return $signatureEncode64;
  }

}