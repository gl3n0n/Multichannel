<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/coupon_list.php');


//chk params
$client_id   = trim($_POST['client_id']);
$customer_id = trim($_POST['customer_id']);
$coupon_id   = trim($_POST['coupon_id']);
$code        = trim($_POST['code']);

//filter
if (
( strlen($client_id)   && ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
( strlen($customer_id) && ! @preg_match(DIGIT_REGEX, $customer_id) ) or
( strlen($coupon_id)   && ! @preg_match(DIGIT_REGEX, $coupon_id) ) or
(                          
	( strlen($client_id)   <= 0 ) or
	( strlen($customer_id) <= 0 ) or
	( strlen($coupon_id)   <= 0 ) or
	( strlen($code)        <= 0 ) 
) 
)
{
	$response['result_code'] = 409;
	$response['error_txt']   = 'Invalid Parameters';
	echo json_encode($response);
	return;
}


//prep
$data     = array();
$obj      = new CouponList($dbconn);
$response = $obj->do_redeemed_a_coupon(
			array(
				"client_id"   => $client_id,
				"customer_id" => $customer_id,
				"coupon_id"   => $coupon_id,
				"code"        => $code,
				)
			);


//give it back
echo json_encode($response);
?>