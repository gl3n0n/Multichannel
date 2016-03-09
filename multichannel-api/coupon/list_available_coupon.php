<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/coupon_list.php');


//chk params
$client_id   = trim($_GET['clientid']);
$customer_id = trim($_GET['customerid']);
$coupon_id   = trim($_GET['coupon_id']);


//filter
if (
( strlen($client_id)   && ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
( strlen($customer_id) && ! @preg_match(DIGIT_REGEX, $customer_id) ) or
( strlen($coupon_id)   && ! @preg_match(DIGIT_REGEX, $coupon_id  ) ) or
(                          
	( strlen($client_id)     <= 0 ) or
	( strlen($customer_id)   <= 0 ) 
) 
)
{
	$response['result_code'] = 405;
	$response['error_txt']   = 'Invalid Parameters';
	echo json_encode($response);
	return;
}


//check token
require_once('../includes/api_token.php');
$atoken  = new ApiToken($dbconn);
$rtoken  = $atoken->is_valid_token();
if($rtoken['status'] <= 0)
{
		//Precondition Failed
		$tdata                = array();
		$tdata['result_code'] = 412;
		$tdata['error_txt']   = 'Api-Token is Invalid!';
		//give it back
		echo json_encode($tdata);
		return;
}
//check token

//prep
$data     = array();
$obj      = new CouponList($dbconn);
$response = $obj->list_of_available_coupon(
			array(
				"client_id"   => $client_id,
				"coupon_id"   => $coupon_id,
				"customer_id" => $customer_id,
				"qrlink"      => "http://104.156.53.150/multichannel-api/coupon/qr_codes/coup",
				)
			);


if ($response['status'])
{		
	$data['results']     = $response;
	$data['result_code'] = 200;
	$response            = $data;
}
else
{
	$response['result_code'] = 404;
	$response['error_txt']   = 'No List of Available Coupon found!';
	
}


//give it back
echo json_encode($response);
?>
