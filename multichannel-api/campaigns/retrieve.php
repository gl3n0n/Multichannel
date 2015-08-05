<?php
	require_once('../config/database.php');		
	require_once('../config/constants.php');		
	require_once('../includes/campaign.php');
	
	$brand_id = $_POST['brand_id'];
	$campaign_id = $_POST['campaign_id'];

    /*if (empty($brand_id) &&
        empty($campaign_id))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }*/

   if ((!empty($brand_id) && !preg_match(DIGIT_REGEX, $brand_id)) ||
        (!empty($campaign_id) && !preg_match(DIGIT_REGEX, $campaign_id)))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Invalid Parameters';
        echo json_encode($response);
        return;
    }


	$campaign = new Campaign($dbconn);
    $response = $campaign->retrieve($brand_id, $campaign_id);
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
        $response['error_txt'] = 'Campaign not found';
    }

    echo json_encode($response);
?>