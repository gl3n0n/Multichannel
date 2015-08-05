<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/login_trigger.php');

	$client_id = $_POST['client_id'];
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];
	$channel_id = $_POST['channel_id'];

	$trigger_parameter = $_POST['trigger_parameter'];
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$trigger_reward = $_POST['trigger_reward'];
	$created_by = $_POST['created_by'];

	if ((!empty($client_id) && !preg_match(DIGIT_REGEX, $client_id)) ||
	(!empty($brand_id) && !preg_match(DIGIT_REGEX, $brand_id)) ||
	(!empty($campaign_id) && !preg_match(DIGIT_REGEX, $campaign_id)) ||
	(!empty($channel_id) && !preg_match(DIGIT_REGEX, $channel_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Invalid Parameters';
        echo json_encode($response);
        return;
    }
	
	if (empty($trigger_parameter) || empty($start_date) || empty($end_date) || empty($trigger_reward) || 
	    empty($client_id) || empty($brand_id) || empty($channel_id) || empty($campaign_id))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }
	
	if ((!empty($start_date) && !preg_match(DATE_REGEX, $start_date)) ||
		(!empty($end_date) && !preg_match(DATE_REGEX, $end_date)))
	{
		$response['result_code'] = 405;
		$response['error_txt'] = 'Invalid Parameters';
		echo json_encode($response);
		return;
	}
		
	$trigger = new LoginTrigger($dbconn, $trigger_id);
    $response = $trigger->add($client_id, $brand_id, $campaign_id, $channel_id,
							     $trigger_parameter, $start_date, $end_date, $trigger_reward, $created_by);

    if (!$response)
    {
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Adding Login Trigger';
    }
    else
    {
        $response['result_code'] = 200;
    }

    echo json_encode($response);
?>