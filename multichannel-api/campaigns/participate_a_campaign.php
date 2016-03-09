<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/campaign.php');




//chk params ["client_id", "customer_id","points_id","brand_id","campaign_id"];
$client_id   = trim($_POST['clientid']);
$customer_id = trim($_POST['customerid']);
$points_id   = trim($_POST['pointsid']);
$brand_id    = trim($_POST['brandid']);
$campaign_id = trim($_POST['campaignid']);
$created_by  = trim($_POST['createdby']);



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
$campaign = new Campaign($dbconn);
$response = $campaign->participate(
		array(
			"client_id"   => $client_id,
			"customer_id" => $customer_id,
			"points_id"   => $points_id,
			"brand_id"    => $brand_id,
			"campaign_id" => $campaign_id,
			"created_by"  => $created_by,
		)
);

if ($response)
{		
	$data['results']     = $response;
	$data['result_code'] = 200;
	$response            = $data;
}


//give it back
echo json_encode($response);
?>
