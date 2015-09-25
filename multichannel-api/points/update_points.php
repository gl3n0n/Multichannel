<?php

require_once('../config/database.php');		
require_once('../config/constants.php');		
require_once('../includes/points_list.php');


//chk params
$client_id     = trim($_POST['client_id'  ]);
$customer_id   = trim($_POST['customer_id']);
$brand_id      = trim($_POST['brand_id'   ]);
$campaign_id   = trim($_POST['campaign_id']);
$points_id     = trim($_POST['points_id'  ]);
$value         = trim($_POST['value'      ]);
$action        = trim($_POST['action'     ]);

//filter
if (
	( strlen($client_id)     && ! @preg_match(DIGIT_REGEX, $client_id  ) ) or
	( strlen($customer_id)   && ! @preg_match(DIGIT_REGEX, $customer_id) ) or
	( strlen($brand_id)      && ! @preg_match(DIGIT_REGEX, $brand_id   ) ) or
	( strlen($campaign_id)   && ! @preg_match(DIGIT_REGEX, $campaign_id) ) or
	( strlen($points_id)     && ! @preg_match(DIGIT_REGEX, $points_id  ) ) or
	( strlen($value)         && ! @preg_match(DIGIT_REGEX, $value      ) ) or
	( strlen($action)        && ! @preg_match("/^(CLAIM|ADD)$/i",$action)) or
	(                          
		( strlen($client_id)     <= 0 ) or
		( strlen($customer_id)   <= 0 ) or
		( strlen($brand_id)      <= 0 ) or
		( strlen($campaign_id)   <= 0 ) or
		( strlen($points_id)     <= 0 ) or
		( strlen($value)         <= 0 ) 
	)                                
)
{
	$response['result_code'] = 400;
	$response['error_txt']   = 'Invalid Parameters';
	echo json_encode($response);
	return;
}

//default add
if(!strlen($action))
  $action = 'ADD';



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
				)
	    );

//give it back
echo json_encode($response);
?>
