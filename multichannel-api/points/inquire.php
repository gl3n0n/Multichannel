<?php
    require_once('../config/database.php');
    require_once('../includes/points.php');

    //params
    //
    #TODO

	$subscription_id = $_POST['subscription_id'];
    $customer_id = $_POST['customer_id'];
    $brand_id = $_POST['brand_id'];
    $campaign_id = $_POST['campaign_id'];
    $channel_id = $_POST['channel_id'];
	$client_id = $_POST['client_id'];

    $response = array(
        'result_code' => '',
    );

    if (empty($customer_id) && empty($client_id))
    {
		$response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
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

    $points = new Points($dbconn, $subscription_id);
    $total_points = $points->inquire($customer_id, $brand_id, $campaign_id, $channel_id,$client_id);
    if ($total_points)
    {
        $response['result_code'] = 200;
        $response['balance'] = $total_points['balance'];
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Balance not found';
    }

    echo json_encode($response);

?>

