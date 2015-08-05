<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/coupon.php');
	
	$coupon_id = $_POST['coupon_id'];

	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];

	$code = $_POST['code'];
	$type = $_POST['type'];
	$type_id = $_POST['type_id'];
	$source = $_POST['source'];
	$image = $_POST['image'];
	$quantity = $_POST['quantity'];
	$limit_per_user = $_POST['limit_per_user'];
	$expiry_date = $_POST['expiry_date'];
	$created_by = $_POST['created_by'];
	$status = $_POST['status'];

	if ((empty($client_id) || !preg_match(DIGIT_REGEX, $client_id)) ||
	(empty($brand_id) || !preg_match(DIGIT_REGEX, $brand_id)) ||
	(empty($campaign_id) || !preg_match(DIGIT_REGEX, $campaign_id)) ||
	(empty($channel_id) || !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	
	if (empty($code) || empty($type) || empty($type_id) || empty($source) || 
	    empty($quantity) || empty($limit_per_user) || empty($expiry_date))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	
	if ((!empty($expiry_date) && !preg_match(DATETIME_REGEX, $expiry_date)) ||
		(!empty($limit_per_user) && !preg_match(DIGIT_REGEX, $limit_per_user)) ||
		(!empty($quantity) && !preg_match(DIGIT_REGEX, $quantity)))
	{
		$response['result_code'] = 406;
		$response['error_txt'] = 'Invalid Parameters';
		echo json_encode($response);
		return;
	}
		
	$coupon = new Coupon($dbconn, null);
    $response = $coupon->insert($client_id, $brand_id, $campaign_id, $channel_id, $created_by, $code,
								$type, $type_id, $source, $image, $quantity, $limit_per_user, $expiry_date, $status);

    if (!$response)
    {
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Inserting Coupon';
    }
	else if ($response[0] == "NOTINSERTED")
	{
		unset($response[0]);
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Inserting Coupon';
	}
    else
    {
        $response['result_code'] = 200;
    }

    echo json_encode($response);
	
?>