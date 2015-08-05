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
        $source = $_POST['source'];
        $reward_config_id = $_POST['reward_config_id'];
        $currentinventory = $_POST['current_inventory'];
        $action = $_POST['action'];
        $date_redeemed = $_POST['date_redeemed'];

        if ((empty($client_id) || !preg_match(DIGIT_REGEX, $client_id)) ||
                (empty($reward_id) || !preg_match(DIGIT_REGEX, $reward_id)) ||
                (empty($brand_id) || !preg_match(DIGIT_REGEX, $brand_id)) ||
                (empty($campaign_id) || !preg_match(DIGIT_REGEX, $campaign_id)) ||
                (empty($user_id) || !preg_match(DIGIT_REGEX, $user_id)) ||
                (empty($reward_config_id) || !preg_match(DIGIT_REGEX, $reward_config_id)) ||
                (empty($currentinventory) || !preg_match(DIGIT_REGEX, $currentinventory)) ||
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
		
    $response = $redeem_reward->insert($client_id, $brand_id, $campaign_id, $channel_id, $user_id,
                                                                         $source, $action, $date_redeemed, $reward_config_id, $new_inventory);																	 
    if ($response)
    {
		
        $response['result_code'] = 200;
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Reward not found';
    }

    echo json_encode($response);
?>
