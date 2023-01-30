<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Http\Response;

$router->get('/', function () use ($router) {
  //  echo 12345;
  // return $router->app->version();
  $content=['name' => 'Abigail', 'state' => 'US'];
  $statusCode="201";
  $value="jjson";
  $debug = env('AzureLog_ApiVersion', true);
  $instance_id = env('WEBSITE_INSTANCE_ID', true);
 /* return response($content, $statusCode)
         ->header('Content-Type', $value)
         ->header('X-Header-One', 'Header Value');*/
 //return response($content, $statusCode)->json(['name' => 'Abigail', 'state' => 'CA']);
 //下面这种方式最好
 return response()->json(['name' => $content, 'state' => $content])->header('X-Header-One', 'Header Value')->header('X-Header-2', 'Header Value 2');
});

$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});
$router->get('/redis1', 'Controller@redis1');
$router->get('/save', 'Controller@save');
$router->get('/sha512', 'Controller@sha512');
$router->get('/jwttoken', 'Controller@jwttoken');
$router->get('/export', 'Controller@export');
$router->get('/getkey', 'Controller@getkey');
$router->get('/create_order', 'Controller@create_order');
$router->get('/sendsbmsas', 'Controller@sendsbmsas');
$router->get('/sendsbmtt', 'Controller@sendsbmtt');
$router->get('/sendsbmsasbatch', 'Controller@sendsbmsasbatch');
$router->get('/test', 'Controller@test');
$router->get('/testAES', 'Controller@testAES');
$router->get('/testAES1', 'Controller@testAES1');
$router->get('/receivesbmsas', 'Controller@receivesbmsas');
$router->post('/handle_new_order', 'Controller@handle_new_order');
$router->post('/handle_require_delivery','Controller@handle_require_delivery');
$router->post('/handle_status_change','Controller@handle_status_change');
$router->post('/info_change','Controller@info_change');
$router->get('/auth1','Controller@auth1');
$router->get('/auth2','Controller@auth2');
$router->get('/auth3','Controller@auth3');
$router->get('/get_time_slot','Controller@get_time_slot');
$router->get('/update_time_slot','Controller@update_time_slot');
$router->get('/send_magento_to_tms','Controller@send_magento_to_tms');
$router->get('/bulk_update_time_slot','Controller@bulk_update_time_slot');
$router->get('/api_test_account','Controller@api_test_account');
$router->get('/api_Get_Sub_Merchant_Credentials','Controller@api_Get_Sub_Merchant_Credentials');
$router->get('/sub_merchant_authentication','Controller@sub_merchant_authentication');
$router->get('/sub_merchant_authentication1','Controller@sub_merchant_authentication1');
$router->get('/Sub_Merchant_KYC','Controller@Sub_Merchant_KYC');
$router->get('/Sub_Merchant_KYC_documents','Controller@Sub_Merchant_KYC_documents');
$router->get('/Add_Sub_Merchant_Bank_Account','Controller@Add_Sub_Merchant_Bank_Account');
$router->get('/Get_Sub_Merchant_Bank_Accounts','Controller@Get_Sub_Merchant_Bank_Accounts');
$router->get('/Get_Sub_Merchant_Balances','Controller@Get_Sub_Merchant_Balances');
$router->get('/Create_Sub_Merchant_Settlement','Controller@Create_Sub_Merchant_Settlement');
$router->get('/Create_Sub_Merchant_Settlement','Controller@Create_Sub_Merchant_Settlement');
$router->get('/Payments_on_behalf_Sub_Merchant','Controller@Payments_on_behalf_Sub_Merchant');
$router->get('/Payments_on_behalf_Sub_Merchant_Split_amount','Controller@Payments_on_behalf_Sub_Merchant_Split_amount');
$router->get('/Payments_on_behalf_Sub_Merchant_Split_percentage','Controller@Payments_on_behalf_Sub_Merchant_Split_percentage');
$router->get('/payout_Submit_a_payout','Controller@payout_Submit_a_payout');
$router->get('/payout_Get_balance','Controller@payout_Get_balance');


//$router->post('/deletesbmsas', 'Controller@deletesbmsas');
$router->group( ['middleware' => 'auth'], function() use ($router) {

});
/*$router->group( ['middleware' => 'auth:mgt_api'], function() use ($router) {
    $router->get('/test', 'ExampleController@test');
    $router->get('/sendsbm', 'ExampleController@sendsbm');
   
    $router->post('/getsbm', 'ExampleController@getsbm');
    $router->get('/testdeleteSBM', 'ExampleController@testdeleteSBM');
    $router->get('/userinfo', 'ExampleController@userinfo');
    $router->post('/create_order', 'ExampleController@create_order');
    $router->get('/user/profile', function () {
        echo 'Nancy';
    });

});*/
