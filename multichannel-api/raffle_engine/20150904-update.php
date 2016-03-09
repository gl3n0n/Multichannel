<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/raffle_engine.php');
	
	$raffle_id = $_POST['raffle_id'];

	$updated_by = $_POST['updated_by'];
	
	$source = $_POST['source'];
	$no_of_winners = $_POST['no_of_winners'];
	$draw_date = $_POST['draw_date'];
	$status = $_POST['status'];

	/*if ((empty($client_id) || !preg_match(DIGIT_REGEX, $client_id)) ||
	(empty($raffle_id) || !preg_match(DIGIT_REGEX, $raffle_id)) ||
	(empty($brand_id) || !preg_match(DIGIT_REGEX, $brand_id)) ||
	(empty($campaign_id) || !preg_match(DIGIT_REGEX, $campaign_id)) ||
	(empty($channel_id) || !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }*/
	if (empty($raffle_id) || !preg_match(DIGIT_REGEX, $raffle_id))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	
	if (empty($source) && empty($no_of_winners) && empty($draw_date) && empty($status))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	
	if ((!empty($draw_date) && !preg_match(DATETIME_REGEX, $draw_date)) ||
	    (!empty($no_of_winners) && !preg_match(DIGIT_REGEX, $no_of_winners)))
	{
		$response['result_code'] = 406;
		$response['error_txt'] = 'Invalid Parameters';
		echo json_encode($response);
		return;
	}
		
	$raffle = new Raffle($dbconn, $raffle_id);
    $response = $raffle->update($client_id, $brand_id, $campaign_id, $channel_id, $updated_by,
								$source, $no_of_winners, $draw_date, $status);

    if (!$response)
    {
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Updating Raffle';
    }
	else if ($response[0] == "NOTFOUND")
	{
		unset($response[0]);
		$response['result_code'] = 404;
        $response['error_txt'] = 'Raffle not found';
	}
    else
    {
        $response['result_code'] = 200;
    }

    echo json_encode($response);
?>