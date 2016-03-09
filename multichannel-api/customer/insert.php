<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/customer.php');

	$first_name = $_POST['firstname'];
	$middle_name = $_POST['middlename'];
	$last_name = $_POST['lastname'];
	$gender = $_POST['gender'];
	$contact_number = $_POST['contactnumber'];
	$address = $_POST['address'];
	$email = $_POST['email'];
	//$status = $_POST['status'];
	$fb_id = $_POST['fbid'];
	$twitter_handle = $_POST['twitterhandle'];
	$created_by = $_POST['created_by'];
	$client_id = $_POST['clientid'];
	$birthdate = $_POST['birthdate'];

	$response = array(
		'result_code' => '',
	);

	$table_name = 'customers';

	if (!empty($client_id) && !preg_match(DIGIT_REGEX, $client_id))
    {
        $response['result_code'] = 403;
        $response['error_txt'] = 'Forbidden';
        echo json_encode($response);
        return;
    }

	if (empty($fb_id) && empty($email))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

	if (empty($first_name) || empty($last_name) || empty($email))
	{
		$response['result_code'] = 405;
		$response['error_txt'] = 'Missing Parameters';
		echo json_encode($response);
		return;
	}

	if ($gender != "M" && $gender != "F")
	{
		$gender = "M";
		if(0){
			$response['result_code'] = 405;
			$response['error_txt'] = 'Invalid Gender';
			echo json_encode($response);
			return;
		}
	}

	/*if ($status != "PENDING" && $status != "ACTIVE" && $status != "INACTIVE")
	{
		$response['result_code'] = 405;
		$response['error_txt'] = 'Invalid Status';
		echo json_encode($response);
		return;
	}*/

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

	$response = $customer->add($first_name, $middle_name, $last_name, $gender, $birthdate,
							   $address, 'ACTIVE', $fb_id, $twitter_handle, $email, $contact_number, $client_id); 

	if ($response)
	{
		if($response[0] == "EXISTS_FBID")
		{
			$response['result_code'] = 403;
			$response['error_txt'] = 'FB ID Already Taken';
			unset($response[0]);
		}
		else if ($response[0] == "EXISTS_EMAIL")
		{
			$response['result_code'] = 403;
			$response['error_txt'] = 'Email Address Already Taken';
			unset($response[0]);
		}
		else
		{
			$response['result_code'] = 200;
		}
	}
	else
	{
		$response['result_code'] = 500;
		$response['error_txt'] = 'Error Adding Customer';
	}

	 echo json_encode($response);
?>
