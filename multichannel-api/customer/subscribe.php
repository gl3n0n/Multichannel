<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/customer.php');

	$customer_id = $_POST['customerid'];
	$channel_id = $_POST['channelid'];
	$campaign_id = $_POST['campaignid'];
	$brand_id = $_POST['brandid'];
	$client_id = $_POST['clientid'];
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
	//check token

	
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
