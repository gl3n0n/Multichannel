<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/points_list.php');


//chk params
$client_id     = trim($_POST['clientid']);
$customer_id   = trim($_POST['customerid']);
$brand_id      = trim($_POST['brandid']);
$campaign_id   = trim($_POST['campaignid']);
$points_id     = trim($_POST['pointsid']);
$value         = trim($_POST['value']);
$action        = trim($_POST['action']);
$actiontype_id = trim($_POST['actiontypeid']);

// echo '<pre>';
// print_r($_POST);
// exit();
//filter
if (
	( strlen($client_id)     && ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
	( strlen($customer_id)   && ! @preg_match(DIGIT_REGEX, $customer_id) ) or
	( strlen($brand_id)      && ! @preg_match(DIGIT_REGEX, $brand_id   ) ) or
	( strlen($campaign_id)   && ! @preg_match(DIGIT_REGEX, $campaign_id) ) or
	( strlen($points_id)     && ! @preg_match(DIGIT_REGEX, $points_id  ) ) or
	( strlen($value)         && ! @preg_match(DIGIT_REGEX, $value      ) ) or
	( strlen($action)        && ! @preg_match("/^(CLAIM|ADD|DEDUCT)$/i",$action)) or
	( strlen($actiontype_id) && ! @preg_match(DIGIT_REGEX, $actiontype_id  ) )
)
{
	$response['result_code'] = 400;
	$response['error_txt']   = 'Invalid Parameters1';
	echo json_encode($response);
	return;
}

//default add
if(!strlen($action))
  $action = 'ADD';


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
$obj      = new PointsList($dbconn);
$response = $obj->do_update_points(array(
				"client_id"      => $client_id,
				"customer_id"    => $customer_id,
				"brand_id"       => $brand_id,
				"campaign_id"    => $campaign_id,
				"points_id"      => $points_id,
				"value"          => $value,
				"action"         => strtoupper($action),
				"actiontype_id"  => $actiontype_id,
				)
	    );

//give it back
echo json_encode($response);
?>
