<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CustomException;
use App\Functions\API_Azure_Log;
use App\Functions\API_ServiceBus_Token;
use App\Functions\API_Magento_Signature;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;

class Controller extends BaseController
{
    
  
	public function sub_mc_settle_apicallback(Request $request)
    {
        $header = $request->header();
        $header = json_encode($header);
        $body = $request->input();
        $body = json_encode($body);
        $date = gmdate('Y-m-d h:i:s \G\M\T');
        $log = "sub_mc_settle_apicallback: body is ".$body."   header is ".$header;
        file_put_contents("php://stdout", $log);
        $file_path1 = base_path('storage/logs/sub_mc_settle_apicallback'.$date.'.log');
        error_log($log,3,$file_path1);

    }

    public function parse_parameters( $request1, $api_name){
         // dd($api_name);/* 方法名test*/
         $date = date("Y-m-d");
         return 'controlTest';
    }
    
	
	public function export(Request $Request)
    {
     
	    $data = array( 
		array("NAME" => "John Doe", "EMAIL" => "john.doe@gmail.com", "GENDER" => "Male", "COUNTRY" => "United States"), 
		array("NAME" => "Gary Riley", "EMAIL" => "gary@hotmail.com", "GENDER" => "Male", "COUNTRY" => "United Kingdom"), 
		array("NAME" => "Edward Siu", "EMAIL" => "siu.edward@gmail.com", "GENDER" => "Male", "COUNTRY" => "Switzerland"), 
		array("NAME" => "Betty Simons", "EMAIL" => "simons@example.com", "GENDER" => "Female", "COUNTRY" => "Australia"), 
		array("NAME" => "Frances Lieberman", "EMAIL" => "lieberman@gmail.com", "GENDER" => "Female", "COUNTRY" => "United Kingdom") 
		);
		
	  
		
		  // Excel file name for download 
		$fileName = "codexworld_export_data-" . date('Ymd') . ".csv"; 
		
		// Headers for download 
		header("Content-Disposition:attachment;filename=\"$fileName\""); 
        header("Content-Type:application/excel"); 
		$flag = false; 
		foreach($data as $row) { 
		    
			if(!$flag) { 
				// display column names as first row 
				//"\t" means xls. "," means csv
				echo implode(",", array_keys($row)) . "\n"; 
				$flag = true; 
			} 
			// filter data 
			array_walk($row,  function(&$str){ 
		$str = preg_replace("/\t/", "\\t", $str); 
		$str = preg_replace("/\r?\n/", "\\n", $str); 
		if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
	    } ); 
			echo implode(",", array_values($row)) . "\n"; 
		} 

		exit;
	}
	
	   
	
	
	  public function create_order(Request $Request)

    {
		//$issue="System Error:The create order function does not work";
		
		
		// file_put_contents("php://stdout", 'create_order-'.$issue."\r\n");
		// error_log('API Error:Some message here.');
		 throw new CustomException('Your error message',10086);  
		
		  Log::channel('go1')->debug('Something happened1234566666666!');;
		  Log::error('2 test-345');
		  Log::alert('alert-0620 test-345');
		  Log::channel('go3')->critical('Something happened12345!');
		
		 
		  
		
		//test
		 return 123; 
      //exception???
	   
	  // level的指定参数？？？
    }
	
    public function save()
    {   
		//echo "12345";
		echo getenv('DB_USERNAME');
        $result=DB::table('users')->insert(
            ["id" => "1b7161ea8542462dbf21db".mt_rand(1,1000000),
                'name' => 'sam',
                'email' => 'sam@mail.com'
            //    'password' => Hash::make("sam1"),
            ]
        );
        echo $result;
    }
	
	public function redis1(Request $Request)
    {   
	   $postdata2 = array(
            'message_type'=>'status_change',
            'message_content'=>array('alg'=>'RSA-OAEP-512-8',
            'value'=>"This is a audi Q8 from T606!!!"));
		$postdatajson = json_encode($postdata2);  
        $postkey="tutorial-n9".mt_rand(1,1000000);
		Redis::set($postkey, $postdatajson); 
		$test=Redis::get("ORDER:INDEX:000502171");
        echo "Stored string in redis:: " .$test; 
       	
	}
	
    public function sendsbmsasbatch(Request $Request)
    {
         //send message to service bus with token
         $cURL = curl_init();
         $header=array(
              'Content-Type:application/atom+xml;type=entry;charset=utf-8',
              'Authorization:SharedAccessSignature sr=https%3a%2f%2fsbn-tntdv-tmstset01.servicebus.windows.net%2fmagento-tms&sig=sCAAXNaFR75qDB8LqMxi%2Bez6ZDKGIEeezS%2B6e5U5KRk%3D&se=1686690092&skn=magento-tms_send',
            //  'BrokerProperties:{"Label":"M22","State":"Active","TimeToLive":3600}'
          );
          //message content
          $postdata2 = array(
            array('type'=>'order_info'), 
            array('alg'=>'RSA-OAEP-512-8',
             'value'=>"This is a audi Q8 from Tie!!!")
          );
      
    
         //转换为json格式
         $postdatajson = json_encode($postdata2);
         dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, "https://tie0502.servicebus.windows.net/magentoq/messages");
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $postdatajson);
         curl_setopt($cURL, CURLOPT_POST, true);
         $json_response_data1 = curl_exec($cURL);
         $info = curl_getinfo($cURL);
         curl_close($cURL);
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         //如果发送失败，将发送失败的信息（json格式）存入log。
         //页面提供一个功能，将json格式的信息黏贴进去，点击发送可以trigger这段代码再次发送message到service bus queue
         file_put_contents("php://stdout", 'Error(send message failure):  '.$postdatajson."\r\n");
    }



    //JWT TOKEN解析后有2个数组
    public function jwttoken(Request $Request)
    {
		$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6ImpTMVhvMU9XRGpfNTJ2YndHTmd2UU8yVnpNYyIsImtpZCI6ImpTMVhvMU9XRGpfNTJ2YndHTmd2UU8yVnpNYyJ9.eyJhdWQiOiJodHRwczovL3NlcnZpY2VidXMuYXp1cmUubmV0IiwiaXNzIjoiaHR0cHM6Ly9zdHMud2luZG93cy5uZXQvMjQyMmNhOTMtZDExNi00NjZjLWI4NTItMWUyNWY2MzAxMDM0LyIsImlhdCI6MTY1MTU0Njg0MSwibmJmIjoxNjUxNTQ2ODQxLCJleHAiOjE2NTE1NTA3NDEsImFpbyI6IkUyWmdZTGpwZVdqQ3lpYnZpTSszSm54Ty9pczZDUUE9IiwiYXBwaWQiOiIxYWJkYTBmYy1jYzJkLTRjNDQtODUxOC00ZDg1NmU4ZDcwMzQiLCJhcHBpZGFjciI6IjEiLCJpZHAiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC8yNDIyY2E5My1kMTE2LTQ2NmMtYjg1Mi0xZTI1ZjYzMDEwMzQvIiwib2lkIjoiYjE0OTE2NWEtOWM5Mi00OWQ2LTg4ZWItNTgzYmQzMGQ4NTQ4IiwicmgiOiIwLkFYMEFrOG9pSkJiUmJFYTRVaDRsOWpBUU5Qa09vWUJvZ1QxSnFfa3lsOFR2Ymp5YUFBQS4iLCJzdWIiOiJiMTQ5MTY1YS05YzkyLTQ5ZDYtODhlYi01ODNiZDMwZDg1NDgiLCJ0aWQiOiIyNDIyY2E5My1kMTE2LTQ2NmMtYjg1Mi0xZTI1ZjYzMDEwMzQiLCJ1dGkiOiJRM0dkYlVWaDZVS1ZRdVZOTWVnTkFBIiwidmVyIjoiMS4wIn0.C61IeGF1vpXGCDFy8_IMs9jF0WWbfiriS7UIWCeI29sTcuj-mWeVO5DVdGDXkp6PbxnQ18sGuClbWWbDrGDR7bXx4x07CYLltxoE7nmDdOwGBwjOewgfcnW4jLv419lP_4Oxoe81ewK8qDDyzjzLruAB53AKZy1FsBfOFi8frGOgQ83gcbc6Cm0MU3gby-AQVP3xdoy2kkL5OaOQ7zx80PAbDsfdM9fPhZGVkd2DcYUUoVTfU75BUoOGoAG_llTuYnEvCyqIeisBisQLdhTKv3YlaBVTgmX8VO7MkNFlnKFhU5KSGGYtb7iyjq9CPMlFtjUlImxM8o5NO-40Y2a--Q";
		$Signature=json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));
        dd($Signature);
		$arraySig=(array)$Signature;
		dd($arraySig);
	//    echo $arraySig['kid']."             ";
       // print_r(json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))));
       
	}	
    
    
    
    //key start
    /*思路，
    参考链接 https://stackoverflow.com/questions/32143743/verifying-jwt-from-azure-active-directory
    call microsoft API获取certificate list，
    通过解析Client传入的token header来对应具体是哪个certificate，
    而后通过openssl来解析certificate对应的public key。用public key来decode token从而获取token的body部分
    （JWT token加密JWT::encode($data, $publickey, 'RS256')）
    https://github.com/firebase/php-jwt
    */
    public function getkey(Request $Request)
    {   
       
        //https://login.microsoftonline.com/{tenant_id}/discovery/keys?appid={client_id}
		$string_microsoftPublicKeyURL = 'https://login.microsoftonline.com/2422ca93-d116-466c-b852-1e25f6301034/discovery/keys?appid=1abda0fc-cc2d-4c44-8518-4d856e8d7034';
        //$string_microsoftPublicKeyURL = 'https://login.microsoftonline.com/common/discovery/keys';
        $array_publicKeysWithKIDasArrayKey = $this->loadKeysFromAzure($string_microsoftPublicKeyURL);
        $string_JSONWebToken= "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6ImpTMVhvMU9XRGpfNTJ2YndHTmd2UU8yVnpNYyIsImtpZCI6ImpTMVhvMU9XRGpfNTJ2YndHTmd2UU8yVnpNYyJ9.eyJhdWQiOiJodHRwczovL3NlcnZpY2VidXMuYXp1cmUubmV0IiwiaXNzIjoiaHR0cHM6Ly9zdHMud2luZG93cy5uZXQvMjQyMmNhOTMtZDExNi00NjZjLWI4NTItMWUyNWY2MzAxMDM0LyIsImlhdCI6MTY1MTU0Njg0MSwibmJmIjoxNjUxNTQ2ODQxLCJleHAiOjE2NTE1NTA3NDEsImFpbyI6IkUyWmdZTGpwZVdqQ3lpYnZpTSszSm54Ty9pczZDUUE9IiwiYXBwaWQiOiIxYWJkYTBmYy1jYzJkLTRjNDQtODUxOC00ZDg1NmU4ZDcwMzQiLCJhcHBpZGFjciI6IjEiLCJpZHAiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC8yNDIyY2E5My1kMTE2LTQ2NmMtYjg1Mi0xZTI1ZjYzMDEwMzQvIiwib2lkIjoiYjE0OTE2NWEtOWM5Mi00OWQ2LTg4ZWItNTgzYmQzMGQ4NTQ4IiwicmgiOiIwLkFYMEFrOG9pSkJiUmJFYTRVaDRsOWpBUU5Qa09vWUJvZ1QxSnFfa3lsOFR2Ymp5YUFBQS4iLCJzdWIiOiJiMTQ5MTY1YS05YzkyLTQ5ZDYtODhlYi01ODNiZDMwZDg1NDgiLCJ0aWQiOiIyNDIyY2E5My1kMTE2LTQ2NmMtYjg1Mi0xZTI1ZjYzMDEwMzQiLCJ1dGkiOiJRM0dkYlVWaDZVS1ZRdVZOTWVnTkFBIiwidmVyIjoiMS4wIn0.C61IeGF1vpXGCDFy8_IMs9jF0WWbfiriS7UIWCeI29sTcuj-mWeVO5DVdGDXkp6PbxnQ18sGuClbWWbDrGDR7bXx4x07CYLltxoE7nmDdOwGBwjOewgfcnW4jLv419lP_4Oxoe81ewK8qDDyzjzLruAB53AKZy1FsBfOFi8frGOgQ83gcbc6Cm0MU3gby-AQVP3xdoy2kkL5OaOQ7zx80PAbDsfdM9fPhZGVkd2DcYUUoVTfU75BUoOGoAG_llTuYnEvCyqIeisBisQLdhTKv3YlaBVTgmX8VO7MkNFlnKFhU5KSGGYtb7iyjq9CPMlFtjUlImxM8o5NO-40Y2a--Q";
        $getKidIni=json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $string_JSONWebToken)[0]))));
        $getKid=((array)$getKidIni)['kid'];
        $publickeyini=$array_publicKeysWithKIDasArrayKey[$getKid];
        //$publickeyini=$array_publicKeysWithKIDasArrayKey['nOo3ZDrODXEK1jKWhXslHR_KXEg'];
       
       // $result=JWT::decode($string_JSONWebToken, $array_publicKeysWithKIDasArrayKey, ["RS256"]);
         $result=JWT::decode($string_JSONWebToken, new KEY($publickeyini,'RS256'));
        dd($result);
	}	

    public function loadKeysFromAzure($string_microsoftPublicKeyURL) {
        $array_keys = array();

        $jsonString_microsoftPublicKeys = file_get_contents($string_microsoftPublicKeyURL);
        $array_microsoftPublicKeys = json_decode($jsonString_microsoftPublicKeys, true);
   
        foreach($array_microsoftPublicKeys['keys'] as $array_publicKey) {
            $string_certText = "-----BEGIN CERTIFICATE-----\r\n".chunk_split($array_publicKey['x5c'][0],64)."-----END CERTIFICATE-----\r\n";
           
            $array_keys[$array_publicKey['kid']] = $this->getPublicKeyFromX5C($string_certText); 
           // dd($string_certText,$array_keys);
        }

        return $array_keys;
    }
    //get public key from certificate
    public function getPublicKeyFromX5C($string_certText) {
        $object_cert = openssl_x509_read($string_certText);
        $object_pubkey = openssl_pkey_get_public($object_cert);
        $array_publicKey = openssl_pkey_get_details($object_pubkey);
        return $array_publicKey['key'];
    }
    //key finish


    //tms-magento  order_status_change 
    public function sendsbmsas(Request $Request)
    {
         //send API request to get service bus sas token
         $uri="https://SBN-TNTDV-TMSTSET01.servicebus.windows.net/tms-magento";
         $sasKeyName="tms-magento_send";
         $sasKeyValue="rNsdXcn4VMNc+yg8oDCYSYqsqoidql0kJXADZlieTDI=";
         $SASToken=API_ServiceBus_Token::generateSasToken($uri,$sasKeyName,$sasKeyValue);
         //send message to service bus with token
         $cURL = curl_init();
         $header=array(
              'Content-Type:application/atom+xml;type=entry;charset=utf-8',
              'Authorization:'.$SASToken,
              'Message-Type:order_status_change'
          );
          //message content
          //  order_id 000502212  000502213
          $status_change_timestamp = Carbon::now()->timestamp;
          
          $order_id="100506057";
          $order_status="90";
          
       
          $order_status_change = array(
            'items'=>array(
                            array('order_id'=>$order_id,
                           'status'=>$order_status,
                           'status_changed_at'=>$status_change_timestamp)
           ));
         //转换为json格式
         $json_order_status_change = json_encode($order_status_change);
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, "https://SBN-TNTDV-TMSTSET01.servicebus.windows.net/tms-magento/messages");
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_order_status_change);
         curl_setopt($cURL, CURLOPT_POST, true);
         $json_response_data1 = curl_exec($cURL);
         die( curl_exec($cURL));
         $info = curl_getinfo($cURL);
         curl_close($cURL);
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         //发送成功201
         print_r("The sending message response code is ".$info['http_code']); 
         //如果发送失败，将发送失败的信息（json格式）存入log。
         //页面提供一个功能，将json格式的信息黏贴进去，点击发送可以trigger这段代码再次发送message到service bus queue
         file_put_contents("php://stdout", 'Error(send message failure):  '.$postdatajson1."\r\n");
    } 


 //tms-tms  change_log
 public function sendsbmtt(Request $Request)
 {
      //send API request to get service bus sas token
      $uri="https://SBN-TNTDV-TMSTSET01.servicebus.windows.net/tms-tms";
      $sasKeyName="tms-tms_send";
      $sasKeyValue="nzUZyk2USL4k5YG4q4UgIBLMxrT5yFbqo3LDc82NAyg=";
      $SASToken=API_ServiceBus_Token::generateSasToken($uri,$sasKeyName,$sasKeyValue);
     
      //send message to service bus with token
      $cURL = curl_init();
      $header=array(
           'Content-Type:application/atom+xml;type=entry;charset=utf-8',
           'Authorization:'.$SASToken,
           'Message-Type:change_log'
       );
       //message content
       //  order_id 000502212  000502213
       $status_change_timestamp = Carbon::now()->timestamp;
       $order_id="100506061";
       $order_status="90";
       
    
       $order_status_change = array(
         'items'=>array(
                         array('order_id'=>$order_id,
                        'status'=>$order_status,
                        'status_changed_at'=>$status_change_timestamp)
        ));
      //转换为json格式
      $json_order_status_change = json_encode($order_status_change);
     // dd($postdatajson);
      curl_setopt($cURL, CURLOPT_URL, "https://SBN-TNTDV-TMSTSET01.servicebus.windows.net/tms-tms/messages");
      curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
      curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_order_status_change);
      curl_setopt($cURL, CURLOPT_POST, true);
      $json_response_data1 = curl_exec($cURL);
      $info = curl_getinfo($cURL);
      curl_close($cURL);
      echo "<pre>";//输出换行，等同于键盘ctrl+u
      print_r($info);
      //发送成功201
      print_r("The sending message response code is ".$info['http_code']); 
      //如果发送失败，将发送失败的信息（json格式）存入log。
      //页面提供一个功能，将json格式的信息黏贴进去，点击发送可以trigger这段代码再次发送message到service bus queue
      file_put_contents("php://stdout", 'Error(send message failure):  '.$postdatajson."\r\n");
 } 

    //测试发送数据magento-tms
    public function send_magento_to_tms(Request $Request)
    {
        #ini_set('max_execution_time', '0');
         //send API request to get service bus sas token
         $uri="https://SBN-TNTDV-TMSTSET01.servicebus.windows.net/magento-tms";
         $sasKeyName="magento-tms_send";
         $sasKeyValue="M7jySLOK0jICBCAZ4Hy75hS/m3x7owhlB5pTsX1W/24=";
         $SASToken=API_ServiceBus_Token::generateSasToken($uri,$sasKeyName,$sasKeyValue);
         $header=array(
            'Content-Type:application/atom+xml;type=entry;charset=utf-8 ',
            'Authorization:'.$SASToken,
            'Message-Type:status_change'
        );
         //Message-Type
          //  new_order   require_delivery   info_change     status_change
       for ($i=0; $i<=0; $i++)
         {
            $order_id= (string)(100504096+$i);
         //send message to service bus with token
         $cURL = curl_init();

          $order_status_change = array(
           /* "order_id"=>$order_id,
            "order_type"=>"next_day",
            "init_at"=>1670335201,
            "store_id"=>"WS",
            "province"=>"Ontario",
            "city"=>"Scarborough",
            "addr"=>"1050 Huntingwood Dr",
            "postal_code"=>"M1S 3H5",
            "cell"=>"1234567890",
            "delivery_window_from"=>1670432400,
            "delivery_window_to"=>1670439600,
            "comment"=>"test",
            "est_weight"=>"2.0000",
            "unit"=>"lb"*/
                            
                                  /*'order_id'=>'000502493',  
                                  'comment'=>null,  
                                  'final_weight'=>'55.1100',  
                                  'scan_at'=>1667505600,  
                                  'reg_bag_amount'=>25,  
                                  'cold_bag_amount'=>4,  
                                  'freeze_bag_amount'=>2,  
                                  'warm_bag_amount'=>13*/
                                     
                                //问题：1181就可以
                                 /*  'order_id'=>$order_id,
                                  'province'=>'Ontario',
                                  'city'=>'Ottawa',
                                  'addr'=>'151 Lees Ave, Apt.1202',
                                  'postal_code'=>'K1S 5P3',
                                  'cell'=>'6138694146'*/

                                  'order_id'=>array($order_id),
                                 'new_status'=>99
                                ); 
           
         //转换为json格式
         $json_order_status_change = json_encode($order_status_change);
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, "https://SBN-TNTDV-TMSTSET01.servicebus.windows.net/magento-tms/messages");
         
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_order_status_change);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         curl_close($cURL);
        }  
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         //如果发送失败，将发送失败的信息（json格式）存入log。
         //页面提供一个功能，将json格式的信息黏贴进去，点击发送可以trigger这段代码再次发送message到service bus queue
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    }    


//receive data from logic app
    public function receivesbmsas(Request $Request)
    {
      /*  $la_paras = $Request->json()->all();
        // dd($la_paras,typeof($la_paras));
        //获取data(需要decode),message id,locktoken
        foreach ($la_paras as $message){
            $decode_ContentData=base64_decode( $message['ContentData'], $strict = true);
            $messageId=$message['Properties']['MessageId'];
            $LockToken=$message['Properties']['LockToken'];
            file_put_contents("php://stdout", 'MessageId is:  '.$messageId."\r\n");
            // dd(count($la_paras),$decode_ContentData,$la_paras[0]['Properties']['LockToken'],$la_paras[0]['Properties']['MessageId']);
            //写入数据库成功后，调用类中定义删除方法
            $this->deletesbmsas($messageId,$LockToken);
       }*/
 
       $messageId="d960408762a94a6ba37d4259dfc615d9";
       $LockToken="874767de-204f-40f6-8dba-f151cc277764";
       $this->deletesbmsas($messageId,$LockToken);
    }

    public function deletesbmsas($messageId,$LockToken)
    {
        $uri="https://SBN-TNTDV-TMSTSET01.servicebus.windows.net/magento-tms";
        $sasKeyName="magento-tms_listen";
        $sasKeyValue="eSGGBgF29rnrQ/PbH6tcqDnFohFcbDVLpSNNh5gTqwU=";
        $SASToken=API_ServiceBus_Token::generateSasToken($uri,$sasKeyName,$sasKeyValue);
        $cURL = curl_init();
        $header=array(
            'Authorization:'.$SASToken
         );      
       
        // dd($messageId,$LockToken);
         curl_setopt($cURL, CURLOPT_URL, "https://SBN-TNTDV-TMSTSET01.servicebus.windows.net/magento-tms/messages/".$messageId."/".$LockToken);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "DELETE");
         $json_response_data1 = curl_exec($cURL);
        //https://docs.microsoft.com/en-us/rest/api/servicebus/delete-message
        //http_code 200 means delete successfully
         $info = curl_getinfo($cURL);
         curl_close($cURL);
         print_r("The delete message response code is ".$info['http_code']."\r\n");
        return 'successfully';
    }
    



	public function sha512(Request $Request)
    {    
		$plaintext = "TNT1659578076d3dfc330c54c3f59d3dfc330c54c3f65";
		$sha512test = hash("sha512",$plaintext);
		echo $sha512test;
        $date = date("Y-m-d");
        return $date;
	}











    public function get_time_slot(Request $Request)
    {
        //Get Signature
        $order_id='000502219';
        $oauth_nonce=md5(microtime());
        $oauth_timestamp = Carbon::now()->timestamp;
        $HTTP_method = "GET";
        $URL = "https://test.tntnightmarket.biz/rest/V1/xmapi/tms/get-time-slot";
        $requestParams = [
            'order_id' => $order_id
        ];
        $Signature=API_Magento_Signature::generateMagentoSignature($oauth_nonce,$oauth_timestamp,$HTTP_method,$URL,$requestParams);
        // Send request to magento
        $cURL = curl_init();
        $header=array(
              'Authorization:OAuth oauth_consumer_key="h0e4me7qdycmggaqif2wsydcekjknu6n",oauth_token="lpdsohe1u9oqbxmpc2vp9du5u29nu5yh",oauth_signature_method="HMAC-SHA256",oauth_timestamp="'.$oauth_timestamp.'",oauth_nonce="'.$oauth_nonce.'",oauth_version="1.0",oauth_signature="'.$Signature.'"'
          );

        
         curl_setopt($cURL, CURLOPT_URL, $URL."?order_id=".$order_id);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         $magento_response_data = curl_exec($cURL);
         $info = curl_getinfo($cURL);
         curl_close($cURL);
         $decode_magento_response_data=json_decode($magento_response_data);
         if($decode_magento_response_data->code=="200"){
         //遍历数组获取可用时间窗
         dd($decode_magento_response_data->data);
         
        }
    }

    

    public function update_time_slot(Request $Request)
    {
        //Get Signature
        $oauth_nonce=md5(microtime());
        $oauth_timestamp = Carbon::now()->timestamp;
        $HTTP_method = "PUT";
        $URL = "https://test.tntnightmarket.biz/rest/V1/xmapi/tms/update-time-slot";
       
        $Signature=API_Magento_Signature::generateMagentoSignature($oauth_nonce,$oauth_timestamp,$HTTP_method,$URL);
        // Send request to magento
        $cURL = curl_init();
        $header=array(
              'Content-Type:application/json',
              'Authorization:OAuth oauth_consumer_key="h0e4me7qdycmggaqif2wsydcekjknu6n",oauth_token="lpdsohe1u9oqbxmpc2vp9du5u29nu5yh",oauth_signature_method="HMAC-SHA256",oauth_timestamp="'.$oauth_timestamp.'",oauth_nonce="'.$oauth_nonce.'",oauth_version="1.0",oauth_signature="'.$Signature.'"'
          );

        $post_order_timeslot = array(
            'order_id'=>'000502305',
            'start_time'=>'2022-09-03 14:00',
            'end_time'=>'2022-09-03 16:00',
        );
         //转换为json格式
         $post_order_timeslot_json = json_encode($post_order_timeslot);
         curl_setopt($cURL, CURLOPT_URL, $URL);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $post_order_timeslot_json);
         curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "PUT");
         $magento_response_data = curl_exec($cURL);
         $info = curl_getinfo($cURL);
         curl_close($cURL);
         
         $decode_magento_response_data=json_decode($magento_response_data);
         dd($magento_response_data);
         // "{"code":110005,"message":"2022-07-17 and %2 is invalid.","data":null,"tips":[]}"
         if($decode_magento_response_data->code=="200"){
         //返回给用户时间窗更新成功!
         
        }//如果为code 110005,则该时间窗没有capacity
    }
    
    public function bulk_update_time_slot(Request $Request)
    {
        //Get Signature
        $oauth_nonce=md5(microtime());
        $oauth_timestamp = Carbon::now()->timestamp;
        $HTTP_method = "POST";
        $URL = "https://test.tntnightmarket.biz/rest/V1/xmapi/tms/bulk-update-time-slot";
       
        $Signature=API_Magento_Signature::generateMagentoSignature($oauth_nonce,$oauth_timestamp,$HTTP_method,$URL);
        // Send request to magento
        $cURL = curl_init();
        $header=array(
              'Content-Type:application/json',
              'Authorization:OAuth oauth_consumer_key="h0e4me7qdycmggaqif2wsydcekjknu6n",oauth_token="lpdsohe1u9oqbxmpc2vp9du5u29nu5yh",oauth_signature_method="HMAC-SHA256",oauth_timestamp="'.$oauth_timestamp.'",oauth_nonce="'.$oauth_nonce.'",oauth_version="1.0",oauth_signature="'.$Signature.'"'
          );

        $post_order_timeslot = array('items'=>array(
            array(
            'order_id'=>'000502212',
            'start_time'=>'2022-08-11 16:00',
            'end_time'=>'2022-08-11 20:00'),
            array(
                'order_id'=>'000502213',
                'start_time'=>'2022-08-11 16:00',
                'end_time'=>'2022-08-11 20:00')
        ));
        
         //转换为json格式
         $post_order_timeslot_json = json_encode($post_order_timeslot);
         curl_setopt($cURL, CURLOPT_URL, $URL);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $post_order_timeslot_json);
         curl_setopt($cURL, CURLOPT_POST, true);
         $magento_response_data = curl_exec($cURL);
         $info = curl_getinfo($cURL);
         curl_close($cURL);
         $decode_magento_response_data=json_decode($magento_response_data);
         if($decode_magento_response_data->code=="200"){
         //返回给用户批量时间窗更新成功!
         dd($magento_response_data);
        }
    }



   //获取可用时间窗by order
   //只有Get方法有request parameter！！
    public function auth1(Request $Request)
    {
        //随机数
        $oauth_nonce=md5(microtime());
        $oauth_timestamp = Carbon::now()->timestamp;
        $HTTP_method = "GET";
        $URL = "https://test.tntnightmarket.biz/rest/V1/xmapi/tms/get-time-slot";
        $consumer_secret = "asoip3s65156sxofmsqtsfdf4rigozev";
        $access_token_secret = "86fsde2lbqqsg8cf4t24rr42ntoql6r0";

        $requestParams = [
            'order_id' => '000720490'
        ];
        $oauthParams = [
            'oauth_nonce' => 'gruVe81234YHvvsGgozYbDYbb0AmBc3h1v',
            'oauth_signature_method' => 'HMAC-SHA256',
            'oauth_timestamp' => '1671475300',
            'oauth_version' => '1.0',
            'oauth_consumer_key' => '24f2gfskiwi3hn5n1os71i19jpqwicon',
            'oauth_token' => '77yb7xmv0yp85nuxfrn9vns3vwt3g6gk'
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
        echo $URL_data;
        echo "<pre>";
        echo $signatureEncode64;
	}

//单一订单时间窗update
 public function auth2(Request $Request)
    {
        //随机数
        $oauth_nonce=md5(microtime());
        $oauth_timestamp = Carbon::now()->timestamp;
        $HTTP_method = "PUT";
        $URL = "https://test.tntnightmarket.biz/rest/V1/xmapi/tms/update-time-slot";
        $consumer_secret = "kvs5uz0uiry94tp2x0n99oydujf9fs97";
        $access_token_secret = "s8cj1cx7wpmp2yg7mddfnv2lzuicijy4";

        $requestParams = [
      
        ];

        $oauthParams = [
            'oauth_nonce' => 'g44V851234Y5vvsGgozYbDYbb0AmBc3h1v',
            'oauth_signature_method' => 'HMAC-SHA256',
            'oauth_timestamp' => '1671475300',
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
        echo $URL_data;
        echo "<pre>";
        echo $signatureEncode64;
	}



    public function auth3(Request $Request)
    {
        //随机数
        $oauth_nonce=md5(microtime());
        $oauth_timestamp = Carbon::now()->timestamp;
        $HTTP_method = "POST";
        $URL = "https://test.tntnightmarket.biz/rest/V1/xmapi/tms/bulk-update-time-slot";
        $consumer_secret = "kvs5uz0uiry94tp2x0n99oydujf9fs97";
        $access_token_secret = "s8cj1cx7wpmp2yg7mddfnv2lzuicijy4";

        $requestParams = [
           
        ];

        $oauthParams = [
            'oauth_nonce' => 'g44V85y234Y8vvsGgoz5brYbb0AmBc3h1v',
            'oauth_signature_method' => 'HMAC-SHA256',
            'oauth_timestamp' => '1561080878',
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
        echo $URL_data;
        echo "<pre>";
        echo $signatureEncode64;
	}









	//12669454f6746429fc24f2d58296b6031d1278b22dc61eaff9109b963af34b8fae5e6514d579929a9cd5bea7f9e308fb5e7637fa5d54aac061401143fcf2556c
	
	//12669454f6746429fc24f2d58296b6031d1278b22dc61eaff9109b963af34b8fae5e6514d579929a9cd5bea7f9e308fb5e7637fa5d54aac061401143fcf2556c
	
    public function testAES(Request $Request)
    {
       // $plaintext = "message to be encrypted";
      //  $cipher = "aes-256-ctr";
      //  $key="TNT1683394935xxxxxxx";//先被MD5
      //  $ivlen = openssl_cipher_iv_length($cipher);
      //  $iv = openssl_random_pseudo_bytes($ivlen);
      //  $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv);
     // $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0cyI6IjEyMzQ1Njc4OTAiLCJwcm9qZWN0X25hbWUiOiJUTVMifQ.keYrWYJCykugycAK-hDm_awsuE5TzozuJdVa76scpvs";
     // print_r(json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))));
     $plaintext = "TNT1653438687d3dfc330c54c3f59d3dfc330c54c3f65";
     $cipher = "aes-256-cbc";
     $key="d3dfc330c54c3f59d3dfc330c54c3f65";
   
    $iv = "c54c3f595a4f31e0";
   
    $ciphertext = openssl_encrypt($plaintext, $cipher, $key, false, $iv);
    //dd($ciphertext);
    $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv);
    echo $ciphertext."\n";
    }    

    public function testAES1(Request $Request)
    {
   
        $key="TNT1683394935xxxxxxx";//先被MD5
        $iv = 'b"ê\x17eÉ \e±Ä+ú2£┬\x13\x0E¼"';
        $ciphertext="bRVB9aclD+nflQggLeKXduQaRSQgrwo=";
        $cipher = "aes-256-ctr";
        $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv);
        dd($original_plaintext);
    }    

    public function handle_new_order(Request $Request)
    {
      $headers = $Request->header(); 
      $la_paras = $Request->json()->all();
     // dd($headers['auth-sign']);
     // echo $Request->ContentData;
     dd(gettype($la_paras['ContentData']));
     dd(json_decode($la_paras['ContentData'])->message_type,(array)(json_decode($la_paras['ContentData'])->message_content));
    } 
   
    public function handle_require_delivery(Request $Request)
    {
        return 2;
    } 
    public function handle_status_change(Request $Request)
    {
        return 3;
    } 
    public function info_change(Request $Request)
    {
        return 4;
    } 






    public function test(Request $Request)
    {
        

           // $customer_id = '9872b4f2-accb-48ef-ac19-b89fef49e327';
           // $shared_key =  'Gc5fAJst0TbKnllR32QmwiKzROCHXwR3SEEYH66HRKLYmwdlFCQu8gJ9hzrHbsLZkqp6fJlV7v1gtJtAc2G15w==';

            $client = new API_Azure_Log();
           // $date_test = "2022-06-21T11:50:00.625Z";
           // $date_test1 = "2022-06-21 12:24:22";
            $date_test = date('Y-m-d H:i:s');
            $date_test1 =str_replace('+00:00', 'Z', gmdate('c', strtotime($date_test)));
            $log_type = "ApacheAccessLogYY";
            $json_records = array();
            $record1= array( 
            "Log_ID" => "5cdad72f-c848-4df0-8aaa-ffe033e75d57",
            "Level" => "error",
            "date4" => $date_test,
            "processing_time1" => 57,
            "remote" => "101.202.74.59",
            "user" => "-",
            "method" => "GET / HTTP/1.1",
            "status" => "200",
            "size" => "-",
            "referer" => "-",
            "agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:27.0) Gecko/20100101 Firefox/27.0"
            );
       
            
            $record2 = array(
            "Log_ID" => "8145d82213a744ad859c36f31a84f6dd",
            "Level" => "info",
            "date1" => "2022-06-21 10:34:33",
            "processing_time" => "1099",
            "remote" => "201.78.74.59",
            "user" => "-",
            "method" => "GET /manager/html HTTP/1.1",
            "status" =>"200",
            "size" => "-",
            "referer" => "-",
            "agent" => "Mozilla/5.0 (Windows NT 5.1; rv:5.0) Gecko/20100101 Firefox/5.0"
            );
            array_push($json_records, $record1);
            array_push($json_records, $record2);

            $response =  $client->post_data($log_type, $json_records);
            var_dump($response);
          //  if (!API_Azure_Log::is_success($response)) 
          if (! $client->is_success($response)) 
            {
                echo "Problem reading data\n";
            } else {
                echo "Success!!\n";
            }
    } 

  
    












    //测试发送数据msi_api_test_account
    public function api_test_account(Request $Request)
    {
         $uri="https://marketplace-api.dlocal-sbox.com/accounts";

         $request_body = array(
            "merchant_type"=>"MSI",
            "name"=>"Sub Company Name12",
            "email"=>"submerchant20230130@marketplace.com",
            "country"=>"AR"
         ); 
           
         //转换为json格式
         $json_request_body = json_encode($request_body);
         $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhu";
         $X_Login="NgaIzcTrZY";
         $X_Date="2022-12-19T12:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
      
         $header=array(
            'X-Date: 2022-12-19T12:37:00.310Z',
            'X-Login: NgaIzcTrZY',
            'X-Trans-Key: mrN6BjFLDQ',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    }  
    
    
    public function api_Get_Sub_Merchant_Credentials(Request $Request)
    {   
         //从create account中获取的account_id:  dcd16a67-6372-4e95-98ba-f064ef19e434
         //146a8758-c0ed-4754-ae3a-4953c3e05081
         //518f8cef-5be4-4b29-ac1f-2d1f629d0041
         $uri="https://marketplace-api.dlocal-sbox.com/accounts/dcd16a67-6372-4e95-98ba-f064ef19e434/credentials";
         $json_request_body="";
         $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhu";
         $X_Login="NgaIzcTrZY";
         $X_Date="2022-12-19T12:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
         $header=array(
            'X-Date: 2022-12-19T12:37:00.310Z',
            'X-Login: NgaIzcTrZY',
            'X-Trans-Key: mrN6BjFLDQ',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
       //  curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    } 
    
    
     
     public function sub_merchant_authentication(Request $Request)
     {
          $uri="https://marketplace-api.dlocal-sbox.com/accounts/authenticate";
 
          $request_body = array(
             "email"=>"submerchant20230103-1@marketplace.com",
             "password"=>"123456789"
          ); 
          
          //使用sub merchant credentialsLL
          //转换为json格式
          $json_request_body = json_encode($request_body);
          $secretKey="iRvIOqXYeh4eh3MbxL7YTsvX83maySGE7";
          $X_Login="Xlj824irdT";
          $X_Date="2022-12-19T12:37:00.310Z";
          $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
          $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
       
          $header=array(
             'X-Date: 2022-12-19T12:37:00.310Z',
             'X-Login: C1kCheZf5L',
             'X-Trans-Key: y1SrfPid73',
             'Content-Type: application/json',
             'X-Version: 2.1',
             'User-Agent: MerchantTest / 1.0 ',
             'Authorization: V2-HMAC-SHA256, Signature: '.$signature
          );
         
         $cURL = curl_init();
         // dd($postdatajson);
          curl_setopt($cURL, CURLOPT_URL, $uri);       
          curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
          curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
          curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($cURL, CURLOPT_POST, true);
         /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
          curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
          curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
         */
         //option2:或设置成false
          curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
          $json_response_data1 = curl_exec($cURL);
         
          $info = curl_getinfo($cURL);
          $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
          $headerStr = substr( $json_response_data1 , 0 , $headerSize );
          $bodyStr = substr( $json_response_data1 , $headerSize );
          curl_close($cURL);
          print_r("The header size is ".$headerSize);
          print_r("The repsonse header is ".$headerStr); 
          print_r("The body is ".$bodyStr); 
          echo "<pre>";//输出换行，等同于键盘ctrl+u
          print_r($info);
          print_r("The sending message response code is ".$info['http_code']); 
          file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
     }  
     
     public function sub_merchant_authentication1(Request $Request)
     {
          $uri="https://marketplace-api.dlocal-sbox.com/accounts/authenticate";
 
          $request_body = array(
             "email"=>"submerchant20230103-1@marketplace.com",
             "password"=>"123456789"
          ); 
          //使用Anthony提供的credentialsLL
          //转换为json格式
          $json_request_body = json_encode($request_body);
          $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhu";
          $X_Login="NgaIzcTrZY";
          $X_Date="2022-12-19T12:37:00.310Z";
          $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
          $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
       
          $header=array(
             'X-Date: 2022-12-19T12:37:00.310Z',
             'X-Login: NgaIzcTrZY',
             'X-Trans-Key: mrN6BjFLDQ',
             'Content-Type: application/json',
             'X-Version: 2.1',
             'User-Agent: MerchantTest / 1.0 ',
             'Authorization: V2-HMAC-SHA256, Signature: '.$signature
          );
         
         $cURL = curl_init();
         // dd($postdatajson);
          curl_setopt($cURL, CURLOPT_URL, $uri);       
          curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
          curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
          curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($cURL, CURLOPT_POST, true);
         /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
          curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
          curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
         */
         //option2:或设置成false
          curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
          $json_response_data1 = curl_exec($cURL);
         
          $info = curl_getinfo($cURL);
          $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
          $headerStr = substr( $json_response_data1 , 0 , $headerSize );
          $bodyStr = substr( $json_response_data1 , $headerSize );
          curl_close($cURL);
          print_r("The header size is ".$headerSize);
          print_r("The repsonse header is ".$headerStr); 
          print_r("The body is ".$bodyStr); 
          echo "<pre>";//输出换行，等同于键盘ctrl+u
          print_r($info);
          print_r("The sending message response code is ".$info['http_code']); 
          file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
     }  


    public function Sub_Merchant_KYC(Request $Request)
    {
         $uri="https://marketplace-api.dlocal-sbox.com/accounts/146a8758-c0ed-4754-ae3a-4953c3e05081/kyc";
    
         //转换为json格式
         $json_request_body = '{
            "contact_information":{
                "business_phone_number":"+59899123456",
                "compliance_officer":{
                "name":"Joe Doe",
                "email":"compliance@email.com",
                "linkedin_profile":"https://www.linkedin.com/in/joe-doe/"
                },
                "legal_representative":{
                "id_number":"12345678",
                "email":"legalrepresentative@email.com",
                "country":"UY",
                "address":"Dr. Luis Bonavita 1234",
                "city":"Montevideo",
                "zip_code":"11300"
                 }
            },
            "company_information":{
                "type_of_business":"COMPANY",
                "registered_name":"My Company SRL",
                "aliases":"My Company Alias 1,My Company Alias 2",
                "website":"https://www.submerchantwebsite.com",
                "tax_id":"23523235235",
                "registered_company_number":"346435347",
                "country_of_incorporation":"UY",
                "address_of_incorporation":"Dr. Luis Bonavita 1234",
                "city_of_incorporation":"Montevideo",
                "zip_code_of_incorporation":"11300",
                "date_of_incorporation":"12-07-2021",
                "annual_sales_in_usd":"10000"
            },
            "ubos":[
                {
                "index":1,
                "name":"John Doe",
                "date_of_birth":"12-07-1987",
                "nationality":"UY",
                "place_of_birth":"UY",
                "is_pep":false
                },
                {
                "index":2,
                "name":"Jane Doe",
                "date_of_birth":"14-05-1977",
                "nationality":"BR",
                "place_of_birth":"BR",
                "is_pep":false
                }
            ],
            "directors":[
                {
                "index":1,
                "name":"Jack Doe",
                "date_of_birth":"12-07-1975",
                "nationality":"UY",
                "place_of_birth":"UY",
                "is_pep":false
                },
                {
                "index":2,
                "name":"Jane Doe",
                "date_of_birth":"14-05-1977",
                "nationality":"BR",
                "place_of_birth":"BR",
                "is_pep":false
                }
            ],
            "company_activity":{
                "description_of_product_or_service":"Selling of furniture online",
                "nature_of_the_business":"RETAIL_ONLINE",
                "countries_of_operation":[
                    "UY",
                    "AR",
                    "BR"
                ],
                "top_clients":[
                    "Client Company 1",
                    "Client Company 2"
                ],
                "user_case":"B2B_B2C",
                "sanctioned_country":false
            },
            "dlocal_services":{
                "dlocal_services":"PAYIN_CROSSBORDER,PAYOUT_CROSSBORDER",
                "countries_to_operate_with_dlocal":"UY,AR",
                "expected_monthly_activity":"10000",
                "ticket_size":"500"
            }
         }';
         $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhu";
         $X_Login="NgaIzcTrZY";
         $X_Date="2022-12-19T12:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
      
         $header=array(
            'X-Date: 2022-12-19T12:37:00.310Z',
            'X-Login: NgaIzcTrZY',
            'X-Trans-Key: mrN6BjFLDQ',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    }  


    public function Sub_Merchant_KYC_documents(Request $Request)
    {
         $uri="https://marketplace-api.dlocal-sbox.com/accounts/146a8758-c0ed-4754-ae3a-4953c3e05081/kyc/documents";
    
         //报错500
       /*  $json_request_body = '{
                "document_type": "COPY_OF_ID_FRONT",
                "document_content": "iVBORw0KGgoAAAANSUhEUgAAABMAAAAXCAYAAADpwXTaAAAABGdBTUEAALGPC/xhBQAAAAlwSFlzAAAScwAAEnMBjCK5BwAAAHBJREFUOE9jlJOT+89ALQAyjFqYgVoGgX04Eg1LmP//xJUr/0/MjSMrUlDDjKqGUZhMhkpsQsPsyqYmKkSAXNP/jcDYvHJl4/8mMsIPLczi/s8/BjSMUpfFzT0BdBH5Bo3YvElGDCIXYUMlBwwmbwIAg6hPogt8BeEAAAAASUVORK5CYII="
         }';*/
         $json_request_body ='{
            "document_type": "TAX_ID",
            "document_content":"/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUEBQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBT/wgARCADIAMgDASIAAhEBAxEB/8QAHQABAAICAwEBAAAAAAAAAAAAAAcIBgkBAgQFA//EABgBAQEBAQEAAAAAAAAAAAAAAAABAwIE/9oADAMBAAIQAxAAAAG1IAAAAAAAAAAAAAAAAAAAAAAAAAAAa8uTFNlzZdWx3eArabOTWw5suOUjf4+Q3q5IvyjyNJ+3atGexH2vuE+te/f6crQWcW3SLuL5sEH61hc6OGQ8YpN4+PJF+UeRpP27Voz2Ip7m6lEMKdLGitjY5k9OdunwFfzej0KdsL4EfGhcWcudeK8VPfTO7BUUYljwpotHW+14fiucq+FeesYHTsRiDR2dik69eF+sK2bZ5Li5y5sOqvxrmWECr3sTSppCz1Cn8WI44eFJLE4kjcYqw4ivcJ9a9+/wBOTVwtllum1WI4Lx/zvpbUNq4wM8j9grKjXCSYf1M+Pyp06cpwqL8opLhIRMWfJgThTWz2qwgIXzBseTSft2rRnsRWwhvMWwHN7WPGtuvndbfHBI03Fu/GpL7RrSDUjM8TnHEd1WwDeZuG1Ui9uPRVto+JsuvxeTDio4f52VJncxWsOGsZek9woiyaWC5A5rM2YH11zfPXbDnzkfId1W11pi42ZYsQMpwLjsrV7M5HplXpToi42izlTMO1D2UyOcRDOyL6sAfrXuE+te/f6cljKOqkxU+I/apoZWCm4jyoT249FTXE7hIo1xJNO3pHGovyikt2mMIV8+IGNG5W6aYlMmUrhyaTx/32rRmBx81V6W6JMrr6sKitVYa+2LBk47yTj86vTKwzSxmJF4sSMK0dEm9jOp+BxrR9x9wxlPEH96vLCRgkshEkeP3u2nmmcRxIMWZ/Z"
         }';
         $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhu";
         $X_Login="NgaIzcTrZY";
         $X_Date="2023-01-03T12:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
      
         $header=array(
            'X-Date: 2023-01-03T12:37:00.310Z',
            'X-Login: NgaIzcTrZY',
            'X-Trans-Key: mrN6BjFLDQ',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    }  

    public function Add_Sub_Merchant_Bank_Account(Request $Request)
    {
         $uri="https://marketplace-api.dlocal-sbox.com/accounts/dcd16a67-6372-4e95-98ba-f064ef19e434/bank_account";
    
         $json_request_body = '{
                "beneficiary_name":"John Doe1",
                "account_number":"235235270",
                "bank_name":"Santander1",
                "swift_number":"BSCHESMMXX1",
                "country":"AR"
         }';
         $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhu";
         $X_Login="NgaIzcTrZY";
         $X_Date="2023-01-29T02:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
      
         $header=array(
            'X-Date: 2023-01-29T02:37:00.310Z',
            'X-Login: NgaIzcTrZY',
            'X-Trans-Key: mrN6BjFLDQ',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    } 


    public function Get_Sub_Merchant_Bank_Accounts(Request $Request)
    {   
         //从create account中获取的account_id：63f06d6e-ac2f-454c-ab87-4d80704158f9
         $uri="https://marketplace-api.dlocal-sbox.com/accounts/dcd16a67-6372-4e95-98ba-f064ef19e434/banks";
         $json_request_body="";
         $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhu";
         $X_Login="NgaIzcTrZY";
         $X_Date="2023-01-29T02:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
         $header=array(
            'X-Date: 2023-01-29T02:37:00.310Z',
            'X-Login: NgaIzcTrZY',
            'X-Trans-Key: mrN6BjFLDQ',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
       //  curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    } 


    public function Get_Sub_Merchant_Balances(Request $Request)
    {   
         //从create account中获取的account_id：3935ff06-ee7c-4d11-8d1b-f8a9d2f3bc06
         $uri="https://marketplace-api.dlocal-sbox.com/accounts/dcd16a67-6372-4e95-98ba-f064ef19e434/balances";
         $json_request_body="";
         $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhu";
         $X_Login="NgaIzcTrZY";
         $X_Date="2023-01-29T02:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
         $header=array(
            'X-Date: 2023-01-29T02:37:00.310Z',
            'X-Login: NgaIzcTrZY',
            'X-Trans-Key: mrN6BjFLDQ',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
       //  curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    } 

     
    public function Create_Sub_Merchant_Settlement(Request $Request)
    {
         $uri="https://marketplace-api.dlocal-sbox.com/accounts/dcd16a67-6372-4e95-98ba-f064ef19e434/settlements";
       //有报错
         $json_request_body = '{
                "bank_account_id":235235270,
                "notification_url":"https://googeew.com/settlement_notifications"
         }';
         $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhu";
         $X_Login="NgaIzcTrZY";
         $X_Date="2022-12-19T12:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
      
         $header=array(
            'X-Date: 2022-12-19T12:37:00.310Z',
            'X-Login: NgaIzcTrZY',
            'X-Trans-Key: mrN6BjFLDQ',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    } 


   
    public function Payments_on_behalf_Sub_Merchant(Request $Request)
    {    
        //In order to create a payment on behalf of a Sub Merchant, the Sub Merchant’s account credentials must be used.
         $uri="https://sandbox.dlocal.com/payments";
    
         $json_request_body = '{
            "amount": 120.00,
            "currency" : "ARS",
            "country": "AR",
            "payment_method_id" : "CARD",
            "payment_method_flow" : "DIRECT",
            "payer":{
                "name" : "Thiago Gabriel",
                "email" : "thiago@example.com",
                "document" : "53033315550",
                "user_reference": "12345",
                "address": {
                    "state"  : "Rio de Janeiro",
                    "city" : "Volta Redonda",
                    "zip_code" : "27275-595",
                    "street" : "Servidao B-1",
                    "number" : "1106"
                }
            },
            "card":{
                "token": "CV-a2beb4a0-22b1-4bba-a87a-bae9a6c4e709",
            },
            "order_id": "657434343",
            "description": "Testing Sandbox",
            "notification_url": "http://merchant.com/notifications"
        }';
             /*    "card":{
                "holder_name":"Thiago Gabriel",
                "number":"4111111111111111",
                "cvv":"123",
                "expiration_month":10,
                "expiration_year":2040
            },*/
            // Sub Merchant’s account credentials （$secretKey、$X_Login、$X-Trans-Key）
         $secretKey="MENWzj8FySiK6g2sqOPPFVNA7CAnCasL8";
         $X_Login="uCfiJE71e1";
         $X_Date="2022-12-19T12:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
      
         $header=array(
            'X-Date: 2022-12-19T12:37:00.310Z',
            'X-Login: uCfiJE71e1',
            'X-Trans-Key: TdD2qTt888',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    } 
         //credential:marketplace account    account_id:sub merchant account  
          //从create account中获取的account_id:  dcd16a67-6372-4e95-98ba-f064ef19e434
         //146a8758-c0ed-4754-ae3a-4953c3e05081
         //try credential from 518f8cef-5be4-4b29-ac1f-2d1f629d0041

    public function Payments_on_behalf_Sub_Merchant_Split_amount(Request $Request)
    {
         $uri="https://sandbox.dlocal.com/payments";
    
         $json_request_body = '{
            "amount": 120.00,
            "currency" : "ARS",
            "country": "AR",
            "payment_method_id" : "VD",
            "payment_method_flow" : "DIRECT",
            "payer":{
            "name" : "Thiago Gabriel",
            "email" : "thiago@example.com",
            "document" : "53033315550",
            "user_reference": "12345",
            "address": {
            "state" : "Rio de Janeiro",
            "city" : "Volta Redonda",
            "zip_code" : "27275-595",
            "street" : "Servidao B-1",
            "number" : "1106"
            }
            },
            "card":{
                "token": "CV-dc193476-eddd-4956-98b5-353799dc2c64"
            },
            "order_id": "657434343",
            "notification_url": "http://merchant.com/notifications",
            "splits":[
            {              
            "account_id": "dcd16a67-6372-4e95-98ba-f064ef19e434",
            "amount": 45.00
            },
            {
            "account_id": "146a8758-c0ed-4754-ae3a-4953c3e05081",
            "amount": 75.00 
            }
            ]
            }';
            $secretKey="STHrl7j66mSce5z6CZyLVtupiasTkFEhuD";
            $X_Login="NgaIzcTrZY";
            $X_Date="2023-01-19T12:37:00.310Z";
            $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
            $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
         
            $header=array(
               'X-Date: 2023-01-19T12:37:00.310Z',
               'X-Login: NgaIzcTrZY',
               'X-Trans-Key: mrN6BjFLDQ',
               'Content-Type: application/json',
               'X-Version: 2.1',
               'User-Agent: MerchantTest / 1.0 ',
               'Authorization: V2-HMAC-SHA256, Signature '.$signature
             //  'Authorization: V2-HMAC-SHA256, Signature: '.$signature
            );
       
           $cURL = curl_init();
           // dd($postdatajson);
            curl_setopt($cURL, CURLOPT_URL, $uri);       
            curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
            curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cURL, CURLOPT_POST, true);
           /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
            curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
            curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
           */
           //option2:或设置成false
            curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
            $json_response_data1 = curl_exec($cURL);
           
            $info = curl_getinfo($cURL);
            $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
            $headerStr = substr( $json_response_data1 , 0 , $headerSize );
            $bodyStr = substr( $json_response_data1 , $headerSize );
            curl_close($cURL);
            print_r("The header size is ".$headerSize);
            print_r("The repsonse header is ".$headerStr); 
            print_r("The body is ".$bodyStr); 
            echo "<pre>";//输出换行，等同于键盘ctrl+u
            print_r($info);
            print_r("The sending message response code is ".$info['http_code']); 
            file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    } 

    public function Payments_on_behalf_Sub_Merchant_Split_percentage(Request $Request)
    {
         $uri="https://sandbox.dlocal.com/payments";
    
         $json_request_body = '{
                "amount": 120.00,
                "currency":"USD",
                "country": "BR",
                "payment_method_id":"CARD",
                "payment_method_flow":"DIRECT",
                "payer":{
                    "name":"Thiago Gabriel",
                    "email":"thiago@example.com",
                    "document":"53033315550",
                    "user_reference": "12345",
                    "address": {
                        "state" :"Rio de Janeiro",
                        "city":"Volta Redonda",
                        "zip_code":"27275-595",
                        "street":"Servidao B-1",
                        "number":"1106"
                        }
                },
                "card":{
                    "token": "CV-5e1de32e-2a76-4f18-a7b9-41426ce064ac"
                },
                {
                    "splits":[
                        {
                            "amount":{
                                "percentage":5.50
                                },
                                "account":"12661"
                        },
                        {
                            "amount":{
                                "percentage":94.50
                                },
                                "account":"12662"
                            }
                        ]
                },
                "order_id":"657434343",
                "notification_url":"http://merchant.com/notifications"
            }';
         $secretKey="4Hx0wNJwCRagWF6ybsByfNRacnl0p0Uj1";
         $X_Login="qa97yy3H5N";
         $X_Date="2022-12-19T12:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
      
         $header=array(
            'X-Date: 2022-12-19T12:37:00.310Z',
            'X-Login: qa97yy3H5N',
            'X-Trans-Key: tRkwMhgmdJ',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    } 

   //Invalid credentials401
    public function payout_Submit_a_payout(Request $Request)
    {
         $uri="https://sandbox.dlocal.com/api_curl/cashout_api/request_cashout";
    
         $json_request_body = '{
            "version": "2.0",
            "purpose": "EPTOUR",
            "external_id": "TFluAJthl",
            "document_id": "1234567890",
            "document_type": "CUIL/CUIT",
            "beneficiary_name": "Test",
            "beneficiary_lastname": "Test",
            "country": "AR",
            "bank_name": "Test bank",
            "bank_account": "0000003100060931875108",
            "amount": "1500",
            "address": "eXXAdJXguMNYDRXheWZV",
            "currency": "USD",
            "email": "test@dlocal.com",
            "notification_url": "http://test.com",
            "login": "01253b08-360a-40ad-8f72-87cbab906639",
            "pass": "123456789"
        }';
         $secretKey="piqeOTXcfanAjwnEepKd9IHfdyj92REDl";
         $X_Login="v2tFPJpbgO";
         $X_Date="2022-12-22T11:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
      
         $header=array(
            'X-Date: 2022-12-22T11:37:00.310Z',
            'X-Login: v2tFPJpbgO',
            'X-Trans-Key: lwqtnIVdo5',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    }

    //没有check sub merchant的credentials
    public function payout_Get_balance(Request $Request)
    {   
         //从create account中获取的account_id：3935ff06-ee7c-4d11-8d1b-f8a9d2f3bc06
         $uri="https://sandbox.dlocal.com/payouts/balances";
         $json_request_body="";
         $secretKey="piqeOTXcfanAjwnEepKd9IHfdyj92REDl";
         $X_Login="v2tFPJpbgO";
         $X_Date="2022-12-22T11:37:00.310Z";
         $secretkey_login_data=$X_Login.$X_Date.$json_request_body;
         $signature = hash_hmac("sha256", "$secretkey_login_data", $secretKey);
         $header=array(
            'X-Date: 2022-12-22T11:37:00.310Z',
            'X-Login: v2tFPJpbgO',
            'X-Trans-Key: lwqtnIVdo5',
            'Content-Type: application/json',
            'X-Version: 2.1',
            'User-Agent: MerchantTest / 1.0 ',
            'Authorization: V2-HMAC-SHA256, Signature: '.$signature
         );
    
        $cURL = curl_init();
        // dd($postdatajson);
         curl_setopt($cURL, CURLOPT_URL, $uri);       
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
       //  curl_setopt($cURL, CURLOPT_POSTFIELDS, $json_request_body);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($cURL, CURLOPT_POST, true);
        /*option1: CURLOPT_SSL_VERIFYPEER默认为true，要添加证书！
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, TRUE);
         curl_setopt($cURL, CURLOPT_CAINFO, "storage/cacert.pem");
        */
        //option2:或设置成false
         curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
         $json_response_data1 = curl_exec($cURL);
        
         $info = curl_getinfo($cURL);
         $headerSize = curl_getinfo( $cURL , CURLINFO_HEADER_SIZE);
         $headerStr = substr( $json_response_data1 , 0 , $headerSize );
         $bodyStr = substr( $json_response_data1 , $headerSize );
         curl_close($cURL);
         print_r("The header size is ".$headerSize);
         print_r("The repsonse header is ".$headerStr); 
         print_r("The body is ".$bodyStr); 
         echo "<pre>";//输出换行，等同于键盘ctrl+u
         print_r($info);
         print_r("The sending message response code is ".$info['http_code']); 
         file_put_contents("php://stdout", 'Error(send message failure):  '.$json_response_data1."\r\n");
    } 
}


