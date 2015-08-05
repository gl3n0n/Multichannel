<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/coupon.php');
	
	$coupon_id = $_POST['coupon_id'];
	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];
	$status = $_POST['status'];
	
	
	/* UNCOMMENT AFTER DEMO!
	if ((empty($client_id) || !preg_match(DIGIT_REGEX, $client_id)) ||
	(!empty($coupon_id) && !preg_match(DIGIT_REGEX, $coupon_id)) ||
	(empty($brand_id) || !preg_match(DIGIT_REGEX, $brand_id)) ||
	(empty($campaign_id) || !preg_match(DIGIT_REGEX, $campaign_id)) ||
	(empty($channel_id) || !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }*/

	$coupon = new Coupon($dbconn, $coupon_id);
    $response = $coupon->retrieve($client_id, $brand_id, $campaign_id, $channel_id, $status);
    /*if ($response)
    {
        $response['result_code'] = 200;
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Coupon not found';
    }

    echo json_encode($response);*/
	if ($response)
    {		
		$data = array();
		$data['results'] = $response;
		$data['result_code'] = 200;
		$response = $data;
		//$response['result_code'] = 200;
        //$response['result_code'] = 200;
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Coupon not found';
    }

    echo json_encode($response);
?>