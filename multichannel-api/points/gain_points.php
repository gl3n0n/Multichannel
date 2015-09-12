<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/points_action_type.php');


//chk params
$client_id     = trim($_POST['client_id']);
$customer_id   = trim($_POST['customer_id']);
$brand_id      = trim($_POST['brand_id']);
$campaign_id   = trim($_POST['campaign_id']);
$channel_id    = trim($_POST['channel_id']);
$actiontype_id = trim($_POST['actiontype_id']);

//filter
if (
( strlen($client_id)     && ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
( strlen($customer_id)   && ! @preg_match(DIGIT_REGEX, $customer_id) ) or
( strlen($brand_id)      && ! @preg_match(DIGIT_REGEX, $brand_id) ) or
( strlen($campaign_id)   && ! @preg_match(DIGIT_REGEX, $campaign_id) ) or
( strlen($channel_id)    && ! @preg_match(DIGIT_REGEX, $channel_id) ) or
( strlen($actiontype_id) && ! @preg_match(DIGIT_REGEX, $actiontype_id) ) or
(                          
	( strlen($client_id)     <= 0 ) or
	( strlen($customer_id)   <= 0 ) or
	( strlen($brand_id)      <= 0 ) or
	( strlen($campaign_id)   <= 0 ) or
	( strlen($channel_id)    <= 0 ) or
	( strlen($actiontype_id) <= 0 ) 
)                                
)
{
	$response['result_code'] = 400;
	$response['error_txt']   = 'Invalid Parameters';
	echo json_encode($response);
	return;
}



//prep
$data     = array();
$obj      = new PointsActionType($dbconn);
$response = $obj->gain_points(array(
				"client_id"      => $client_id,
				"customer_id"    => $customer_id,
				"channel_id"     => $channel_id,
				"brand_id"       => $brand_id,
				"campaign_id"    => $campaign_id,
				"actiontype_id"  => $actiontype_id,
				)
);


//give it back
echo json_encode($response);
?>