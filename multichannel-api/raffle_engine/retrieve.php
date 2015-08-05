<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/raffle_engine.php');
	
	$raffle_id = $_POST['raffle_id'];
	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];
	$status = $_POST['status'];
	
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
    $response = $raffle->retrieve($client_id, $brand_id, $campaign_id, $channel_id, $status);
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
        $response['error_txt'] = 'Raffle not found';
    }

    echo json_encode($response);
?>