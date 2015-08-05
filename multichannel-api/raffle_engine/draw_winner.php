<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/raffle_engine.php');
	
	$raffle_id = $_POST['raffle_id'];
	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];
	// comma separated values
	$participants = $_POST['participants'];
	
	/* UNCOMMENT AFTER DEMO
	if ((empty($client_id) || !preg_match(DIGIT_REGEX, $client_id)) &&
	(empty($raffle_id) || !preg_match(DIGIT_REGEX, $raffle_id)) &&
	(empty($brand_id) || !preg_match(DIGIT_REGEX, $brand_id)) &&
	(empty($campaign_id) || !preg_match(DIGIT_REGEX, $campaign_id)) &&
	(empty($channel_id) || !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }*/

	$raffle = new Raffle($dbconn, $raffle_id);
    $response = $raffle->draw_winner($participants);
    if (!$response)
    {
		$response['result_code'] = 404;
        $response['error_txt'] = 'Raffle not found';
    }
	else if ($response[0] == "INVALID")
	{
		unset($response[0]);
		$response['result_code'] = 409;
        $response['error_txt'] = 'Invalid Raffle';
	}
    else
    {
        $data = array();
		$data = $response;
		$data['result_code'] = 200;
		$response = $data;
    }

    echo json_encode($response);
?>