<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/customer.php');

	$customer_id = $_POST['customer_id'];

	$email = $_POST['email'];
	$fb_id = $_POST['fb_id'];

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
