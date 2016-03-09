<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	require_once('../includes/coupon.php');

	$client_id = $_POST['client_id'];
	$campaign_id = $_POST['campaign_id'];
	$customer_id = $_POST['customer_id'];
	$brand_id = $_POST['brand_id'];
	$date_from = $_POST['date_from'];
	$date_to = $_POST['date_to'];
	
	$query_keys = array();

	
	
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

	if (!empty($client_id))
		$query_keys[] = 'customer_subscriptions.ClientId = '. $dbconn->quote($client_id, 'integer');
	if (!empty($customer_id))
		$query_keys[] = 'customer_subscriptions.CustomerId = '. $dbconn->quote($customer_id, 'integer');
	if (!empty($brand_id))
		$query_keys[] = 'customer_subscriptions.BrandId = '. $dbconn->quote($brand_id, 'integer');
	if (!empty($campaign_id))
		$query_keys[] = 'customer_subscriptions.CampaignId = '. $dbconn->quote($campaign_id, 'integer');
	if (!empty($channel_id))
		$query_keys[] = 'customer_subscriptions.ChannelId = '. $dbconn->quote($channel_id, 'integer');
	if (!empty($date_from))
		$query_keys[] = 'customer_subscriptions.DateCreated >= '. $dbconn->quote($date_from, 'timestamp');
	if (!empty($date_to))
		$query_keys[] = 'customer_subscriptions.DateCreated <= '. $dbconn->quote($date_to, 'timestamp');
 
	if ($status != "PENDING")
	{
		$query_keys[] = "customer_subscriptions.Status = 'ACTIVE'  AND campaigns.Status = 'ACTIVE'";
	}
	else
	{
		$query_keys[] = "customer_subscriptions.Status = 'PENDING' AND campaigns.Status = 'ACTIVE'";
	}

	if (sizeof($query_keys) == 0)
		$query_string = null;
	else
		$query_string = implode(' AND ', $query_keys);
	
	//$query = "SELECT FirstName, MiddleName, LastName, customers.Email, FBId, customer_subscriptions.BrandId, customer_subscriptions.CustomerId, customer_subscriptions.CampaignId, customer_subscriptions.ClientId, BrandName,CompanyName, CampaignName FROM customer_subscriptions join customers on customer_subscriptions.CustomerId = customers.CustomerId join brands on brands.BrandId = customer_subscriptions.BrandId join campaigns on campaigns.CampaignId = customer_subscriptions.CampaignId join clients on clients.ClientId = customer_subscriptions.ClientId";
	$query="SELECT CONCAT(FirstName, ' ', MiddleName, ' ', LastName) as Name, customers.Email, FBId, BrandName as Brand,CompanyName as Company, CampaignName, customer_subscriptions.DateCreated as Campaign FROM customer_subscriptions join customers on customer_subscriptions.CustomerId = customers.CustomerId join brands on brands.BrandId = customer_subscriptions.BrandId join campaigns on campaigns.CampaignId = customer_subscriptions.CampaignId join clients on clients.ClientId = customer_subscriptions.ClientId";
	$query = $query . " WHERE " . $query_string;
	//echo $query;
	$res = $dbconn->query($query);
	//echo $query;

	$response = array();
	if (PEAR::isError($res))
	{
		$response['result_code'] = 500;
        $response['error_txt'] = 'Error Inserting Coupon';
		echo json_encode($response);
		return;
	}

	$timestamp = date("Ymd_Hisu");
	$filename = '/var/www/html/multichannel-api/reports/generated_reports/report_' . $timestamp . '.csv';
	$result_arr = array();
	$fp = fopen($filename,'w');
	fputcsv($fp, array('Customer Name','Customer Email','Customer FBId','Brand','Client','Campaign', 'Subscription Date'));
	while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
	{
		$result_arr[] = $row;
		fputcsv($fp, $row);
	}
	
	fclose($fp);
	
    $response['result_code'] = 200;
	$response['file'] = str_replace('/var/www/html','http://104.156.53.150', $filename);
	$response['file_contents'] = file_get_contents($filename);
    echo str_replace('"n"','"\\n"',stripslashes(json_encode($response)));
	
	//download
	/*if (file_exists($filename))
	{
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($filename));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename));
		readfile($filename);
		exit;
	}*/
?>