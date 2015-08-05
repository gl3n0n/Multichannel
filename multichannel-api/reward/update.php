<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/reward.php');
	
	$reward_id = $_POST['reward_id'];

	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];
	$updated_by = $_POST['updated_by'];
	
	$date_from = $_POST['date_from'];
	$date_to = $_POST['date_to'];
	$title = $_POST['title'];
	$description = $_POST['description'];
	$image = $_POST['image'];
	$quantity = $_POST['quantity'];

	if ((empty($client_id) || !preg_match(DIGIT_REGEX, $client_id)) ||
	(empty($reward_id) || !preg_match(DIGIT_REGEX, $reward_id)) ||
	(empty($brand_id) || !preg_match(DIGIT_REGEX, $brand_id)) ||
	(empty($campaign_id) || !preg_match(DIGIT_REGEX, $campaign_id)) ||
	(empty($channel_id) || !preg_match(DIGIT_REGEX, $channel_id)) ||
	 empty($updated_by))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	
	if (empty($date_from) && empty($date_to) && empty($title) && empty($description) && 
	    empty($image) && empty($quantity))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	
	if ((!empty($date_from) && !preg_match(DATE_REGEX, $date_from)) ||
	(!empty($date_to) && !preg_match(DATE_REGEX, $date_to)) ||
	(!empty($quantity) && !preg_match(DIGIT_REGEX, $date_to)))
	{
		$response['result_code'] = 406;
		$response['error_txt'] = 'Invalid Parameters';
		echo json_encode($response);
		return;
	}
		
	$reward = new Reward($dbconn, $reward_id);
    $response = $reward->update($client_id, $brand_id, $campaign_id, $channel_id, $updated_by,
								$date_from, $date_to, $title, $description, $image, $quantity);

    if (!$response)
    {
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Updating Reward';
    }
	else if ($response[0] == "NOTFOUND")
	{
		unset($response[0]);
		$response['result_code'] = 404;
        $response['error_txt'] = 'Reward not found';
	}
    else
    {
        $response['result_code'] = 200;
    }

    echo json_encode($response);
?>