<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/customer.php');

	$customer_id = $_POST['customer_id'];
	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$channel_id = $_POST['channel_id'];
	$campaign_id = $_POST['campaign_id'];

    $response = array(
        'result_code' => '',
    );

    if (empty($customer_id))
    {
        $response['result_code'] = 403;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

   if ((!empty($brand_id) && !preg_match(DIGIT_REGEX, $brand_id)) ||
	   (!empty($client_id) && !preg_match(DIGIT_REGEX, $client_id)) ||
	   (!empty($channel_id) && !preg_match(DIGIT_REGEX, $channel_id)) ||
	   (!empty($customer_id) && !preg_match(DIGIT_REGEX, $customer_id)) ||
	   (!empty($campaign_id) && !preg_match(DIGIT_REGEX, $campaign_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Invalid Parameters';
        echo json_encode($response);
        return;
    }

	$customer = new Customer($dbconn, $customer_id);
    $response = $customer->retrieve_subscriptions($client_id, $brand_id, $channel_id, $campaign_id, $client_id);
    if ($response)
    {
		$response['results'] = $response; 
        $response['result_code'] = 200;
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Customer not found';
    }

    echo json_encode($response);

?>
