<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/customer.php');

	$customer_id = $_POST['customer_id'];
	$first_name = $_POST['first_name'];
	$middle_name = $_POST['middle_name'];
	$last_name = $_POST['last_name'];
	$gender = $_POST['gender'];
	$birthdate = $_POST['birthdate'];
	$address = $_POST['address'];
	$status = $_POST['status'];
	$fb_id = $_POST['fb_id'];
	$twitter_handle = $_POST['twitter_handle'];
	$email = $_POST['email'];
	$contact_number = $_POST['contact_number'];
	$client_id = $_POST['client_id'];

    $response = array(
        'result_code' => '',
    );

	if (empty($customer_id))
    {
        $response['result_code'] = 403;
        $response['error_txt'] = 'Forbidden';
        echo json_encode($response);
        return;
    }

    if (!empty($customer_id) && !preg_match(DIGIT_REGEX, $customer_id) || 
		!empty($client_id) && !preg_match(DIGIT_REGEX, $client_id))
    {
        $response['result_code'] = 403;
        $response['error_txt'] = 'Forbidden';
        echo json_encode($response);
        return;
    }

	if (empty($first_name) && empty($middle_name) && empty($last_name) && empty($gender) && 
	    empty($birthdate) && empty($address) && empty($status) &&
		empty($fb_id) && empty($twitter_handle) && empty($email) && empty($contact_number))
    {
        $response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

	if ((!empty($birthdate) && !preg_match(DATE_REGEX, $birthdate)) ||
		(!empty($contact_number) && !preg_match(DIGIT_REGEX, $contact_number)) ||
		(!empty($status) && ($status != "PENDING" && $status != "ACTIVE" && $status != "INACTIVE")) ||
		(!empty($gender) && ($gender != "M" && $gender != "F")))
	    {
			$response['result_code'] = 406;
			$response['error_txt'] = 'Invalid Parameters';
			echo json_encode($response);
			return;
	    }

	$customer = new Customer($dbconn, $customer_id);
    $response = $customer->update($first_name, $middle_name, $last_name, $gender, $birthdate,
								  $address, $status, $fb_id, $twitter_handle, $email, $contact_number, $client_id);
    if (!$response)
    {
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Updating Customer';
    }
	else if ($response[0] == "NOTFOUND")
	{
		unset($response[0]);
		$response['result_code'] = 404;
        $response['error_txt'] = 'Customer not found';
	}
    else
    {
        $response['result_code'] = 200;
    }

    echo json_encode($response);
	
?>	