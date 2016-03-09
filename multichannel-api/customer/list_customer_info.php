<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/customer_info.php');


//chk params
$client_id   = trim($_GET['clientid']);
$customer_id = trim($_GET['customerid']);
$fb_id    = trim($_GET['fbid']);
$twitter_handle    = trim($_GET['twitterhandle']);
$email       = trim($_GET['email']);

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
//check token



//prep
$data     = array();
$obj      = new CustomerInfo($dbconn);
$response = $obj->get_info(
			array(
				"client_id"   => $client_id,
				"customer_id" => $customer_id,
				"email"       => $email,
				"fb_id"    => $fb_id,
				"twitter_handle"    => $twitter_handle,
				)
			);


if ($response['status'])
{		
	$data['results']     = $response;
	$data['result_code'] = 200;
	$response            = $data;
}
else
{
	$response['result_code'] = 404;
	$response['error_txt']   = 'No List of Customer found!';
}


//give it back
echo json_encode($response);
?>