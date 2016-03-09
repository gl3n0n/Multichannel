<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/coupon_list.php');


//chk params
$client_id   = trim($_POST['clientid']);
$customer_id = trim($_POST['customerid']);
$coupon_id   = trim($_POST['couponid']);

//filter
if (
( strlen($client_id)   && ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
( strlen($customer_id) && ! @preg_match(DIGIT_REGEX, $customer_id) ) or
( strlen($coupon_id)   && ! @preg_match(DIGIT_REGEX, $coupon_id) ) or
(                          
	( strlen($client_id)   <= 0 ) or
	( strlen($customer_id) <= 0 ) or
	( strlen($coupon_id)   <= 0 ) 
) 
)
{
	$response['result_code'] = 409;
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
//customter-ACTIVE
if($rtoken['customer'] <= 0)
{
		//Precondition Failed
		$tdata                = array();
		$tdata['result_code'] = 413;
		$tdata['error_txt']   = 'Customer Status is Invalid!';
		//give it back
		echo json_encode($tdata);
		return;
}
//check token


//prep
$data     = array();
$obj      = new CouponList($dbconn);
$response = $obj->do_redeemed_a_coupon_by_exch_pts(
			array(
				"client_id"   => $client_id,
				"customer_id" => $customer_id,
				"coupon_id"   => $coupon_id,
				"qrlink"      => "http://104.156.53.150/multichannel-api/coupon/qr_codes/coup",
				)
			);


//give it back
echo json_encode($response);
?>