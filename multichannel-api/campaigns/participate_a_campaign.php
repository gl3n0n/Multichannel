<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/campaign.php');




//chk params ["client_id", "customer_id","points_id","brand_id","campaign_id"];
$client_id   = trim($_POST['client_id']);
$customer_id = trim($_POST['customer_id']);
$points_id   = trim($_POST['points_id']);
$brand_id    = trim($_POST['brand_id']);
$campaign_id = trim($_POST['campaign_id']);
$created_by  = trim($_POST['created_by']);



//filter
if (
( @intval($client_id)   <= 0 or ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
( @intval($customer_id) <= 0 or ! @preg_match(DIGIT_REGEX, $customer_id) ) or
( @intval($points_id)   <= 0 or ! @preg_match(DIGIT_REGEX, $points_id  ) ) or
( @intval($brand_id)    <= 0 or ! @preg_match(DIGIT_REGEX, $brand_id   ) ) or
( @intval($campaign_id) <= 0 or ! @preg_match(DIGIT_REGEX, $campaign_id) ) 
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
$response = $campaign->participate($client_id,
			$customer_id,
			$points_id,
			$brand_id,
			$campaign_id,
			$created_by);

if ($response)
{		
	$data['results']     = $response;
	$data['result_code'] = 200;
	$response            = $data;
}
else
{
	$response['result_code'] = 404;
	$response['error_txt']   = 'No Customer Subscriptions List found!';
}


//give it back
echo json_encode($response);
?>