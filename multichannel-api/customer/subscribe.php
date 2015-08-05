<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/customer.php');

	$customer_id = $_POST['customer_id'];
	$channel_id = $_POST['channel_id'];
	$campaign_id = $_POST['campaign_id'];
	$brand_id = $_POST['brand_id'];
	$client_id = $_POST['client_id'];
	$status = $_POST['status'];

	$response = array(
		'result_code' => '',
	);

    if (empty($customer_id) || !preg_match(DIGIT_REGEX, $customer_id))
    {
        $response['result_code'] = 403;
        $response['error_txt'] = 'Forbidden';
        echo json_encode($response);
        return;
    }

	if (empty($channel_id) || empty($client_id) || empty($campaign_id) ||
		empty($brand_id) || empty($status))
	{
		$response['result_code'] = 405;
		$response['error_txt'] = 'Missing Parameters';
		echo json_encode($response);
		return;
	}

	if ($status != "PENDING" && $status != "ACTIVE" && $status != "INACTIVE")
	{
		$response['result_code'] = 405;
		$response['error_txt'] = 'Invalid Status';
		echo json_encode($response);
		return;
	}

	$customer = new Customer($dbconn, $customer_id);
	
	//$customer->isAllowed($brand_id, $campaign_id, $channel_id)
	if ($customer->isAllowed($brand_id, $campaign_id, $channel_id, $client_id))
    {
		$response = $customer->subscribe($channel_id, $campaign_id, $brand_id, $status, $client_id);

		if ($response)
		{
			if($response[0] == "EXISTS")
			{
				$response['result_code'] = 403;
				$response['error_txt'] = 'Customer Already Subscribed';
				unset($response[0]);
			}
			else
			{
				$response['result_code'] = 200;
			}
		}
		else
		{
			$response['result_code'] = 500;
			$response['error_txt'] = 'Error Subscribing Customer';
		}
	}
	else
	{
		$response['result_code'] = 403;
        $response['error_txt'] = 'Promo expired';
	}

    echo json_encode($response);
?>
