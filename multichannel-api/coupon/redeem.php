<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/redeem_coupon.php');

	$generated_coupon_id = $_POST['generated_coupon_id'];
	$coupon_id = $_POST['coupon_id'];
	$customer_id = $_POST['customer_id'];
	$coupon_mapping_id = $_POST['coupon_mapping_id'];
	$use_points = $_POST['use_points'];

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

	$redeem_coupon = new RedeemCoupon($dbconn, $coupon_id);


	if (!$redeem_coupon->isValidCoupon())
	{
			$response['result_code'] = 403;
			$response['error_txt'] = 'Invalid Coupon Id';
			echo json_encode($response);
			return;
	}

	if ($redeem_coupon->isOverTheLimit($coupon_id, $customer_id))
	{
		$response['result_code'] = 409;
		$response['error_txt'] = 'Coupon Limit Reached';
		echo json_encode($response);
		return;
	}
	
    if ("true" == $use_points)
	{
		$response = $redeem_coupon->redeemOnPoints($generated_coupon_id, $customer_id, $coupon_mapping_id);
	}
	else
	{
		$response = $redeem_coupon->redeem($generated_coupon_id, $customer_id, $coupon_mapping_id);
	}
	
	
	
	if (!$response)
	{
		$response['result_code'] = 404;
        $response['error_txt'] = 'Coupon not found';
	}
	else if ($response[0] == "ALREADY_REDEEMED")
	{
		unset($response[0]);
		$response['result_code'] = 409;
        $response['error_txt'] = 'Coupon Already Redeemed';
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
        $response['error_txt'] = 'Coupon on Points Config Not Found';
	}
	else if ($response[0] == "INSUFICENT_BAL")
	{
		unset($response[0]);
		$response['result_code'] = 409;
        $response['error_txt'] = 'Insufficent Balance';
	}
    else
    {
		//if ($redeem_coupon->deductQuantity($client_id, $brand_id, $campaign_id, $channel_id, $customer_id))
		//{
		$response['result_code'] = 200;
		//}
		//else
		//{
		//	$response['result_code'] = 500;
		//	$response['error_txt'] = 'Error Occurred on Deduct Quantity';
		//}
    }

    echo json_encode($response);
?>
