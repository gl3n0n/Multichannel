<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/coupon.php');

	$coupon_id = $_POST['coupon_id'];

	if (empty($coupon_id) || !preg_match(DIGIT_REGEX, $coupon_id))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	$coupon = new Coupon($dbconn, $coupon_id);
    $response = $coupon->regenerate();


    if (!$response)
    {
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Generating Coupons';
    }
	else if ($response[0] == "NOTGENERATED")
	{
		unset($response[0]);
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Generating Coupons';
	}
	else if ($response[0] == "INVALID")
	{
		unset($response[0]);
		$response['result_code'] = 409;
        $response['error_txt'] = 'Invalid Coupon';
	}
	else if ($response[0] == "STILL_PENDING")
	{
		unset($response[0]);
		$response['result_code'] = 409;
        $response['error_txt'] = 'Coupons Still Pending';
	}
	else if ($response[0] == "EDITED_ALREADY")
	{
		unset($response[0]);
		$response['result_code'] = 409;
        $response['error_txt'] = 'Coupons Already Edited';
	}
	else if ($response[0] == "FILENOTFOUND")
	{
		unset($response[0]);
		$response['result_code'] = 404;
        $response['error_txt'] = 'CSV Not Found';
	}
	else if ($response[0] == "NODIFF")
	{
		unset($response[0]);
		$response['result_code'] = 409;
        $response['error_txt'] = 'No Added codes';
	}
	else if ($response[0] == "LESSTHAN_CURRENT")
	{
		unset($response[0]);
		$response['result_code'] = 409;
        $response['error_txt'] = 'Quantity Must Be Higher Than Current';
	}
    else
    {
		//unset($response); 
        $response['result_code'] = 200;
    }

    echo json_encode($response);
	
?>