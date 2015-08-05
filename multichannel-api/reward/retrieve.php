<?php
	require_once('../config/database.php');		
	require_once('../config/constants.php');		
	require_once('../includes/reward.php');
	
	$reward_id = $_POST['reward_id'];
	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];

   if (!empty($reward_id) && !preg_match(DIGIT_REGEX, $reward_id) ||
        (!empty($client_id) && !preg_match(DIGIT_REGEX, $client_id)) ||
        (!empty($brand_id) && !preg_match(DIGIT_REGEX, $brand_id)) ||
        (!empty($campaign_id) && !preg_match(DIGIT_REGEX, $campaign_id)) ||
        (!empty($channel_id) && !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	$reward = new Reward($dbconn, $reward_id);
    $response = $reward->retrieve($client_id, $brand_id, $campaign_id, $channel_id);
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
        $response['error_txt'] = 'Reward not found';
    }

    echo json_encode($response);
?>