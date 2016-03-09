<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/points_action_type.php');


//chk params
$client_id     = trim($_POST['clientid']);
$customer_id   = trim($_POST['customerid']);
$brand_id      = trim($_POST['brandid']);
$campaign_id   = trim($_POST['campaignid']);
$channel_id    = trim($_POST['channelid']);
$actiontype_id = trim($_POST['actiontypeid']);

//filter
if (
( strlen($client_id)     && ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
( strlen($customer_id)   && ! @preg_match(DIGIT_REGEX, $customer_id) ) or
( strlen($actiontype_id) && ! @preg_match(DIGIT_REGEX, $actiontype_id) ) or
( strlen($brand_id)      && ! @preg_match(DIGIT_REGEX, $brand_id) ) or
( strlen($campaign_id)   && ! @preg_match(DIGIT_REGEX, $campaign_id) ) or
( strlen($channel_id)    && ! @preg_match(DIGIT_REGEX, $channel_id) ) 
)
{
	$response['result_code'] = 400;
	$response['error_txt']   = 'Invalid Parameters';
	echo json_encode($response);
	return;
}


//check token
require_once('../includes/api_token.php');
$atoken  = new ApiToken($dbconn);
$rtoken  = $atoken->is_valid_token();
if($rtoken['status'] <= 0)
{
		//Precondition Failed
		$tdata                = array();
		$tdata['result_code'] = 412;
		$tdata['error_txt']   = 'Api-Token is Invalid!';
		//give it back
		echo json_encode($tdata);
		return;
}
//customter-ACTIVE
if($rtoken['customer'] <= 0)
{
		//Precondition Failed
		$tdata                = array();
		$tdata['result_code'] = 413;
		$tdata['error_txt']   = 'Customer Status is Invalid!';
		//give it back
		echo json_encode($tdata);
		return;
}
//check token

//prep
$data     = array();
$obj      = new PointsActionType($dbconn);
$response = $obj->gain_points(array(
				"client_id"      => $client_id,
				"customer_id"    => $customer_id,
				"brand_id"       => $brand_id,
				"campaign_id"    => $campaign_id,
				"channel_id"     => $channel_id,
				"actiontype_id"  => $actiontype_id,
				)
);


//give it back
echo json_encode($response);
?>
