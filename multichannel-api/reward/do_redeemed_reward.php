<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/reward_list.php');


//chk params
$client_id          = trim($_POST['clientid']);
$customer_id        = trim($_POST['customerid']);
$reward_config_id   = trim($_POST['rewardconfigid']);

//filter
if (
( strlen($client_id)          && ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
( strlen($customer_id)        && ! @preg_match(DIGIT_REGEX, $customer_id) ) or
( strlen($reward_config_id)   && ! @preg_match(DIGIT_REGEX, $reward_config_id) ) or
(                          
	( strlen($client_id)          <= 0 ) or
	( strlen($customer_id)        <= 0 ) or
	( strlen($reward_config_id)   <= 0 ) 
) 
)
{
	$response['result_code'] = 409;
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
$obj      = new RewardList($dbconn);
$response = $obj->do_redeemed_a_reward(
			array(
				"client_id"        => $client_id,
				"customer_id"      => $customer_id,
				"reward_config_id" => $reward_config_id,
				)
			);


//give it back
echo json_encode($response);
?>