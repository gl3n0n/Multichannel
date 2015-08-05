<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/coupon.php');
	require_once('../includes/phpqrcode/qrlib.php');

	$coupon_id = $_POST['coupon_id'];
	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];
	$customer_id = $_POST['customer_id'];
	$generated_coupon_id = $_POST['generated_coupon_id'];
	
	/*if ((empty($client_id) || !preg_match(DIGIT_REGEX, $client_id)) ||
	    (empty($coupon_id) || !preg_match(DIGIT_REGEX, $coupon_id)) ||
	    (empty($brand_id) || !preg_match(DIGIT_REGEX, $brand_id)) ||
	    (empty($campaign_id) || !preg_match(DIGIT_REGEX, $campaign_id)) ||
		(empty($generated_coupon_id) || !preg_match(DIGIT_REGEX, $generated_coupon_id)) ||
	    (empty($channel_id) || !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }*/
	//echo $generated_coupon_id;
	if (empty($generated_coupon_id) || !preg_match(DIGIT_REGEX, $generated_coupon_id))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

	$coupon = new Coupon($dbconn, $coupon_id);

    $response = $coupon->retrieve_generated_specific($generated_coupon_id);
    if ($response)
    {
		$result = $response[0];
		//print_r($result);
		$coupon2 = new Coupon($dbconn, $result['couponid']);
		$response2 = $coupon2->retrieve(null, null, null, null, null);

		$name = '/var/www/html/multichannel-api/coupon/qr_codes/coup' . $result['generatedcouponid'] . '.png';
		$url_to_call = "http://104.156.53.150/multichannel-api/coupon/redeem?generated_coupon_id=" . $result['generatedcouponid'];
		//echo $url_to_call;

		QRcode::png($url_to_call, $name);

		$fp = fopen($name, 'rb');
		header("Content-Type: image/png");
		header("Content-Length: " . filesize($name));
		fpassthru($fp);
		return;
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Coupon not found';
    }

    echo json_encode($response);
?>