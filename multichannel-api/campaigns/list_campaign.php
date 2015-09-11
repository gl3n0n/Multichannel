<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/campaign.php');


//chk params
$client_id   = trim($_POST['client_id']);
$customer_id = trim($_POST['customer_id']);


//filter
if (
( strlen($client_id)   && ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
( strlen($customer_id) && ! @preg_match(DIGIT_REGEX, $customer_id) )
)
{
	$response['result_code'] = 405;
	$response['error_txt']   = 'Invalid Parameters';
	echo json_encode($response);
	return;
}


//prep
$data     = array();
$campaign = new Campaign($dbconn);
$response = $campaign->lists($client_id,$customer_id);


if ($response)
{		
	$data['results']     = $response;
	$data['result_code'] = 200;
	$response            = $data;
}
else
{
	$response['result_code'] = 404;
	$response['error_txt']   = 'No Campaign List to Participate found!';
}


//give it back
echo json_encode($response);
?>