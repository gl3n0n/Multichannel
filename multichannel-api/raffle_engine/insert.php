<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/raffle_engine.php');
	
	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];
	$coupon_id = $_POST['coupon_id'];
	$created_by = $_POST['created_by'];
	$source = $_POST['source'];
	$no_of_winners = $_POST['no_of_winners'];
	$fda_no = $_POST['fda_no'];
	$draw_date = $_POST['draw_date'];
	$status = $_POST['status'];

	if ((empty($client_id) || !preg_match(DIGIT_REGEX, $client_id)) ||
	    (empty($brand_id) || !preg_match(DIGIT_REGEX, $brand_id)) ||
	    (empty($campaign_id) || !preg_match(DIGIT_REGEX, $campaign_id)) ||
		(empty($coupon_id) || !preg_match(DIGIT_REGEX, $coupon_id)) ||
	    (empty($channel_id) || !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

	if (empty($source) || empty($no_of_winners) || empty($fda_no) || empty($draw_date))
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
		
	$raffle = new Raffle($dbconn, null);
    $response = $raffle->insert($coupon_id, $client_id, $brand_id, $campaign_id, $channel_id, $created_by,
								$source, $no_of_winners, $fda_no, $draw_date, $status);

    if (!$response)
    {
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Inserting Raffle';
    }
	else if ($response[0] == "NOTINSERTED")
	{
		unset($response[0]);
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Inserting Raffle';
	}
    else
    {
        $response['result_code'] = 200;
    }

    echo json_encode($response);
?>