<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/redeem_reward.php');
	require_once('../includes/points.php');

	$reward_id = $_POST['reward_id'];
	$user_id = $_POST['user_id'];
	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];
	$source = $_POST['source'];
	$reward_config_id = $_POST['reward_config_id'];
	$currentinventory = $_POST['current_inventory'];
	$action = $_POST['action'];
	$date_redeemed = $_POST['date_redeemed'];
	
	$customer_id = $_POST['customer_id'];
	$points = $_POST['points'];
	//$points_id = $_POST['points_id'];

	if ((empty($client_id) || !preg_match(DIGIT_REGEX, $client_id)) ||
			(empty($reward_id) || !preg_match(DIGIT_REGEX, $reward_id)) ||
			(empty($brand_id) || !preg_match(DIGIT_REGEX, $brand_id)) ||
			(empty($campaign_id) || !preg_match(DIGIT_REGEX, $campaign_id)) ||
			(empty($user_id) || !preg_match(DIGIT_REGEX, $user_id)) ||
			(empty($reward_config_id) || !preg_match(DIGIT_REGEX, $reward_config_id)) ||
			(empty($currentinventory) || !preg_match(DIGIT_REGEX, $currentinventory)) ||
			(empty($customer_id) || !preg_match(DIGIT_REGEX, $customer_id)) ||
			(empty($points) || !preg_match(DIGIT_REGEX, abs($points))) ||
			(empty($channel_id) || !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;	
    }

    if (empty($source) || empty($action))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

    $redeem_reward = new RedeemReward($dbconn, $reward_id);

	if (!$redeem_reward->isValidReward())
	{
			$response['result_code'] = 403;
			$response['error_txt'] = 'Invalid Reward Id';
			echo json_encode($response);
			return;
	}
	$new_inventory = (int)$currentinventory - 1;

	$subscription_id = $redeem_reward->getSubscriptionId($customer_id, $client_id, $brand_id, $campaign_id, $channel_id);
	if (!$subscription_id)
	{
		$response['result_code'] = 404;
		$response['error_txt'] = 'Subscription Not Found';
		echo json_encode($response);
		return;
	}
	
	// Try to deduct points first
	$points_class = new Points($dbconn, $subscription_id);
	$total_points = $points_class->subtractClaimPoints($points,$customer_id, $brand_id, $campaign_id, $channel_id, $client_id);
	if ($total_points['balance'])
	{	
		$response = $redeem_reward->insert($client_id, $brand_id, $campaign_id, $channel_id, $user_id,
																			 $source, $action, $date_redeemed, $reward_config_id, $new_inventory);																	 
		if ($response)
		{
			$response['result_code'] = 200;
			$response['balance'] = $total_points['balance'];
			echo json_encode($response);
			return;
		}
		else
		{
			$response['result_code'] = 404;
			$response['error_txt'] = 'Reward not found';
			echo json_encode($response);
			return;
		}
	}
	if ($total_points[0] == "MIN")
	{
		$response['result_code'] = 403;
		$response['error_txt'] = 'Insufficient Points';
		echo json_encode($response);
		return;
	}
	else
	{
		$response['result_code'] = 404;
		$response['error_txt'] = 'Balance not found';
		echo json_encode($response);
		return;
	}

    echo json_encode($response);
?>
