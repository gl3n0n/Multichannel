<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/customer.php');

	$customer_id = $_POST['customerid'];

	$email = $_POST['email'];
	$fb_id = $_POST['fbid'];

    $response = array(
        'result_code' => '',
    );

    if (!empty($customer_id) && !preg_match(DIGIT_REGEX, $customer_id))
    {
        $response['result_code'] = 403;
        $response['error_txt'] = 'Invalid Parameters';
        echo json_encode($response);
        return;
    }

	if (empty($fb_id) && empty($email) && empty($customer_id))
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

	$customer = new Customer($dbconn, $customer_id);
    $response = $customer->retrieve($fb_id, $email);
    if ($response)
    {
        $response['result_code'] = 200;
    }
    else
    {
        $response['result_code'] = 404;
        $response['error_txt'] = 'Customer not found';
    }

    echo json_encode($response);

?>
