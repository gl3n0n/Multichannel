<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/login_trigger.php');
	
	$trigger_id = $_POST['trigger_id'];
	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];
	
	if (empty($trigger_id) || !preg_match(DIGIT_REGEX, $trigger_id))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	
	if ((!empty($coupon_id) && !preg_match(DIGIT_REGEX, $coupon_id)) ||
	(!empty($brand_id) && !preg_match(DIGIT_REGEX, $brand_id)) ||
	(!empty($campaign_id) && !preg_match(DIGIT_REGEX, $campaign_id)) ||
	(!empty($channel_id) && !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 406;
        $response['error_txt'] = 'Invalid Parameters';
        echo json_encode($response);
        return;
    }
	
	$login = new LoginTrigger($dbconn, $trigger_id);
    $response = $login->retrieve($client_id, $brand_id, $campaign_id, $channel_id);
    if ($response)
    {
        $response['result_code'] = 200;
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Login Trigger not found';
    }

    echo json_encode($response);
?>