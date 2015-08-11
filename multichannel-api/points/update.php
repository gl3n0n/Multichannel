<?php
    require_once('../config/database.php');
    require_once('../includes/points.php');

	$subscription_id = $_POST['subscription_id'];
    $customer_id = $_POST['customer_id'];
    $brand_id = $_POST['brand_id'];
    $campaign_id = $_POST['campaign_id'];
    $client_id = $_POST['client_id'];
    $channel_id = $_POST['channel_id'];
	$points = $_POST['points'];
	$action = $_POST['action'];
	$points_id = $_POST['points_id'];

    $response = array(
        'result_code' => '',
    );

    if (empty($subscription_id))
    {
		$response['result_code'] = 405;
        $response['error_txt'] = 'Missing Parameters';
        echo json_encode($response);
        return;
    }

    $points_class = new Points($dbconn, $subscription_id);

	//
	$allowed_flag = $points_class->isAllowed($brand_id, $campaign_id, $channel_id, $client_id, $points_id);
	
	if ($allowed_flag[0] == "INVALID_CLIENT")
	{
		$response['result_code'] = 403;
        $response['error_txt'] = 'Invalid Client';
	}
	else if ($allowed_flag[0] == "INVALID_BRAND")
	{
		$response['result_code'] = 403;
        $response['error_txt'] = 'Invalid Brand';
	}
	else if ($allowed_flag[0] == "INVALID_CAMPAIGN")
	{
		$response['result_code'] = 403;
        $response['error_txt'] = 'Invalid Campaign';
	}
	else if ($allowed_flag[0] == "INVALID_CHANNEL")
	{
		$response['result_code'] = 403;
        $response['error_txt'] = 'Invalid Channel';
	}
	else if ($allowed_flag[0] == "INVALID_POINTS")
	{
		$response['result_code'] = 403;
        $response['error_txt'] = 'Invalid Promo';
	}
	else if ($allowed_flag)
    {
		// check if add or subtract
		if ($action != 'ADD')
		// if ($points < 0)
		{
			if ($action == 'CLAIM')
			{
				$total_points = $points_class->subtractClaimPoints($points,$customer_id, $brand_id, $campaign_id, $channel_id, $client_id);
			}
			else
			{
				$pointlogid = $_POST['point_log_id'];
				// check if pointlogid exists
				$points = $points_class->getPointIdInfo($pointlogid);
				if ($points > 0)
				{
					$total_points = $points_class->subtract($pointlogid, $points);
				}
				else
				{
					$response['result_code'] = 404;
					$response['error_txt'] = 'Deduct Balance not found';
					echo json_encode($response);
					return;
				}

			}
			
		}
		else
		{
			if (empty($points_id))
			{
				$response['result_code'] = 404;
				$response['error_txt'] = 'Balance not found';
				echo json_encode($response);
				return;
			}
			else
			{
				$total_points = $points_class->add($customer_id, $brand_id, $campaign_id, $channel_id, $action, $points_id, $client_id);
			}
		}
		

		if ($total_points['balance'])
		{
			$response['result_code'] = 200;
			$response['balance'] = $total_points['balance'];
		}
		else if ($total_points[0] == "MAX2")
		{
			$response['result_code'] = 405;
			$response['error_txt'] = 'Limit Reached';
		}
		else if ($total_points[0] == "MAX")
		{
			$response['result_code'] = 403;
			$response['error_txt'] = 'Daily Limit Reached';
		}
		else if ($total_points[0] == "MIN")
		{
			$response['result_code'] = 403;
			$response['error_txt'] = 'Insufficient Points';
		}
		else
		{
			$response['result_code'] = 404;
			$response['error_txt'] = 'Balance not found';
		}
	}
	else
	{
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Occurred';
	}

    echo json_encode($response);

?>

