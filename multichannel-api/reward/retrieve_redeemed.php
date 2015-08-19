<?php
    require_once('../config/database.php');
    require_once('../config/constants.php');
    require_once('../includes/redeem_reward.php');

    $reward_id = $_POST['reward_id'];
    $user_id = $_POST['user_id'];
    $client_id = $_POST['client_id'];
    $brand_id = $_POST['brand_id'];
    $campaign_id = $_POST['campaign_id'];
    $channel_id = $_POST['channel_id'];

    if ((empty($reward_id) || !preg_match(DIGIT_REGEX, $reward_id)) &&
        (empty($user_id) || !preg_match(DIGIT_REGEX, $user_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

	$redeem_reward = new RedeemReward($dbconn, $reward_id);

    $response = $redeem_reward->retrieveRedeemed($user_id,$client_id, $brand_id, $campaign_id, $channel_id);
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
        $response['error_txt'] = 'Redeemed Reward not found';
    }

    echo json_encode($response);
?>