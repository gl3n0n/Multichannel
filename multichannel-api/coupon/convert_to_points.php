<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/redeem_coupon.php');

	$generated_coupon_id = $_POST['generated_coupon_id'];
	$coupon_id = $_POST['coupon_id'];
	$customer_id = $_POST['customer_id'];
	$coupon_mapping_id = $_POST['coupon_mapping_id'];

    if ((empty($generated_coupon_id) || !preg_match(DIGIT_REGEX, $generated_coupon_id)) ||
                (empty($coupon_id) || !preg_match(DIGIT_REGEX, $coupon_id)) ||
				(empty($coupon_mapping_id) || !preg_match(DIGIT_REGEX, $coupon_mapping_id)) ||
                (empty($customer_id) || !preg_match(DIGIT_REGEX, $customer_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
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

	$redeem_coupon = new RedeemCoupon($dbconn, $coupon_id);


	if (!$redeem_coupon->isValidCoupon())
	{
			$response['result_code'] = 403;
			$response['error_txt'] = 'Invalid Coupon Id';
			echo json_encode($response);
			return;
	}
	
	$response = $redeem_coupon->couponToPoints($generated_coupon_id, $customer_id, $coupon_mapping_id);
	
	if (!$response)
	{
		$response['result_code'] = 404;
        $response['error_txt'] = 'Coupon not found';
	}
	else if ($response[0] == "ALREADY_CONVERTED")
	{
		unset($response[0]);
		$response['result_code'] = 403;
        $response['error_txt'] = 'Coupon Already Converted To Points';
	}
	else if ($response[0] == "NOT_REDEEMED")
	{
		unset($response[0]);
		$response['result_code'] = 403;
        $response['error_txt'] = 'Coupon Not Redeemed';
	}
	else if ($response[0] == "SUBSCRIPTION_NOT_FOUND")
	{
		unset($response[0]);
		$response['result_code'] = 404;
        $response['error_txt'] = 'Subscription Not Found';
	}
	else if ($response[0] == "CONFIG_NOT_FOUND")
	{
		unset($response[0]);
		$response['result_code'] = 404;
        $response['error_txt'] = 'Coupon To Points Configuration Not Found';
	}
	else if ($response[0] == "ERROR")
	{
		unset($response[0]);
		$response['result_code'] = 500;
        $response['error_txt'] = 'Internal Server Error';
	}
    else
    {
		$response['result_code'] = 200;
    }

    echo json_encode($response);
?>
