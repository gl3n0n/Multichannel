<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/customer.php');

	$first_name = $_POST['first_name'];
	$middle_name = $_POST['middle_name'];
	$last_name = $_POST['last_name'];
	$gender = $_POST['gender'];
	$contact_number = $_POST['contact_number'];
	$address = $_POST['address'];
	$email = $_POST['email'];
	//$status = $_POST['status'];
	$fb_id = $_POST['fb_id'];
	$twitter_handle = $_POST['twitter_handle'];
	$created_by = $_POST['created_by'];
	$client_id = $_POST['client_id'];

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

	if (empty($first_name) || empty($middle_name) || empty($last_name) ||
		empty($gender) || empty($contact_number) || empty($address) || empty($email))
	{
		$response['result_code'] = 405;
		$response['error_txt'] = 'Missing Parameters';
		echo json_encode($response);
		return;
	}

	if ($gender != "M" && $gender != "F")
	{
		$response['result_code'] = 405;
		$response['error_txt'] = 'Invalid Gender';
		echo json_encode($response);
		return;
	}

	/*if ($status != "PENDING" && $status != "ACTIVE" && $status != "INACTIVE")
	{
		$response['result_code'] = 405;
		$response['error_txt'] = 'Invalid Status';
		echo json_encode($response);
		return;
	}*/

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
