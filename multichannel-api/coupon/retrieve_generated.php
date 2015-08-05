<?php
    require_once('../config/database.php');
    require_once('../config/constants.php');
    require_once('../includes/coupon.php');

    $coupon_id = $_POST['coupon_id'];
    /**$client_id = $_POST['client_id'];
    $brand_id = $_POST['brand_id'];
    $campaign_id = $_POST['campaign_id'];
    $channel_id = $_POST['channel_id'];**/

    /* RETURN THIS AFTER DEMO!!!
	if ((empty($coupon_id) || !preg_match(DIGIT_REGEX, $coupon_id)) &&
        (empty($customer_id) || !preg_match(DIGIT_REGEX, $customer_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }*/

	$coupon = new Coupon($dbconn, $coupon_id);

    $response = $coupon->retrieve_generated();
    if ($response)
    {		
		$data = array();
		$data['results'] = $response;
		$data['result_code'] = 200;
		$response = $data;
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Generated Coupon not found';
    }

    echo json_encode($response);
?>