<?php
	require_once('../config/database.php');
	session_start();

	//if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['logout'])
	if(isset($_POST) and $_POST['logout'] === 'true')
	{
		session_destroy();
		header("Location: login");
		exit;
	}

	if (!isset($_SESSION['login_user']))
	{
		header("Location: login");
		exit;
	}
?>


<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Multichannel API Demo</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.2.min.js"></script>
</head>
<body>


<form action="" method="post">
<table>
<tr>
<td><p> Welcome <?php echo strtoupper($_SESSION['login_user']); ?>! </p></td>
<td><input type="hidden" name="logout" value="true"><input style="text-align: right" type="submit" value="Logout"/></td>
</tr>
</table>
</form>



<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['claim'])
{

    $url = 'http://104.156.53.150/multichannel-api/reward/do_redeemed_reward.php';
	$data = array('clientid' => $_POST['client_id'], 'customerid' => $_SESSION['login_id'],
				  'reward_config_id' => $_POST['reward_config_id'], 'apitoken' => $_SESSION['api_token']);
				  // echo '<pre>';
				  // print_r($data);
				  // exit();
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	$data = json_decode($result, true);
	//var_dump($result);
	if ($data["result_code"] == 200)
	{
		echo 'Notice: <font color="green">Successfully redeemed reward.<br></font>';
	}
	else
	{
		echo 'Notice: <font color="red">' . $data["error_txt"] . '<br></font>';
	}
}
?>	

<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['campaign'])
{
	// INSERT TO CUSTOMERS_SUBSCRIPTIONS AND CUSTOMER POINTS
	$campaign_id = $_POST['campaignid'];
	$brand_id = $_POST['brandid'];
	$channel_id = $_POST['channelid'];

	$query = "INSERT INTO customer_subscriptions (CustomerId,BrandId,CampaignId,ChannelId,Status) values " . 
	         "(" . $_SESSION['login_id'] . ", $brand_id, $campaign_id, $channel_id, 'ACTIVE') ";
			 
	$res = $dbconn->query($query);
	echo $query;

	$res = $dbconn->extended->autoExecute("customer_subscriptions", null, MDB2_AUTOQUERY_SELECT, 'SubscriptionId = '. $dbconn->quote($dbconn->lastInsertId("customer_subscriptions", 'SubscriptionId'), 'integer'), null, true, null);
	
	$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

	$_SESSION['subscription_id'] = $row['subscriptionid'];
//	echo $_SESSION['subscription_id'];
}
?>	

<?php

if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'])
{
	 $url = 'http://104.156.53.150/multichannel-api/points/gain_points.php';
	//$i = 1;
	//echo $_POST['thesubmit'];
	//$GLOBALS
	//if (empty($_POST['channelid']) && empty($_POST['channelid'])
	$data = array('actiontypeid' => $_POST['actiontypeid'], 'customerid' => $_SESSION['login_id'],
				  'channelid' => $_POST['channelid'], 'campaignid' => $_POST['campaignid'],
				  'brandid' => $_POST['brandid'], 'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	$result_arr_add = json_decode($result);
	
	if ($result_arr_add->result_code == 200)
		{
			//print_r($data);
			echo 'Notice: <font color="green">Successfully gained points.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">'.$result_arr_add->error_txt.'<br></font>';
		}
	//$result_arr_add["pointofaction"] = $_POST['pointofaction'];
}

?>

<?php
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['update_coupon'])
	{
		$url = 'http://104.156.53.150/multichannel-api/coupon/generate.php';
		$data = array('couponid' => $_POST['thecouponid'],
					  'apitoken' => $_SESSION['api_token']);

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$data = json_decode($result, true);

		if ($data["result_code"] == 200)
		{
			//print_r($data);
			echo 'Notice: <font color="green">Successfully generated ' . $data["generated_count"] . ' coupons.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">Error occurred while generating coupons.<br></font>';
		}
	}
?>
	
<?php
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['update_edit_coupon'])
	{

		$url = 'http://104.156.53.150/multichannel-api/coupon/regenerate.php';
		$data = array('couponid' => $_POST['thecouponid'],
					  'apitoken' => $_SESSION['api_token']);

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$data = json_decode($result, true);

		if ($data["result_code"] == 200)
		{
			//print_r($data);
			echo 'Notice: <font color="green">Successfully generated ' . $data["generated_count"] . ' coupons.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">Error on updating coupon: ' . $data["error_txt"] . '<br></font>';
		}
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['conv_coupon_to_points'])
	{
		$url = 'http://104.156.53.150/multichannel-api/coupon/convert_to_points.php';
		$data = array('couponid' => $_POST['couponid'],
					  'customerid' => $_POST['customerid'],
					  'generated_coupon_id' => $_POST['generated_coupon_id'],
					  'coupon_mapping_id' => $_POST['coupon_mapping_id'], 
					  'apitoken' => $_SESSION['api_token']
					  );

		//print_r($data);
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$data = json_decode($result, true);

		if ($data["result_code"] == 200)
		{
			//print_r($data);
			echo 'Notice: <font color="green">Successfully converted coupons to points.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">Error on updating coupon: ' . $data["error_txt"] . '<br></font>';
		}
	}
	
	/*if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['generate_coupon'])
	{
		$url = 'http://104.156.53.150/multichannel-api/coupon/insert';
		$data = array('clientid' => '1',
					  'brandid' => '1',
					  'channelid' => '1',
					  'campaignid' => '2',
					  'code' => $_POST['thecode'],
					  'quantity' => $_POST['thequantity'],
					  'limit_per_user' => $_POST['thelimitperuser'],
					  'source' => 'demo page',
					  'expiry_date' => '2016-01-01 00:00:00',
					  'type' => 'qrcode',
					  'type_id' => 'Type 2',
					  'status' => 'ACTIVE');

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$data = json_decode($result, true);

		if ($data["result_code"] == 200)
		{
			echo 'Notice: <font color="green">Successfully generated coupon.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">Error occurred while generating coupon.<br></font>';
		}
	}*/

	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['generate_raffle'])
	{
		$url = 'http://104.156.53.150/multichannel-api/raffle_engine/insert';
		$data = array('couponid' => $_POST['thecoupid'],
		              'clientid' => '1',
					  'brandid' => '1',
					  'channelid' => '1',
					  'campaignid' => '2',
					  'no_of_winners' => $_POST['thenoofwinners'],
					  'fda_no' => $_POST['thefdano'],
					  'source' => 'DEMO',
					  'apitoken' => $_SESSION['api_token'],
					  'draw_date' => date('Y-m-d H:i:s'),
					  'status' => 'ACTIVE');
					  

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$data = json_decode($result, true);

		if ($data["result_code"] == 200)
		{
			echo 'Notice: <font color="green">Successfully generated raffle.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">Error occurred while generating raffle.<br></font>';
		}
	}

	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['update_raffle'])
	{
		$url = 'http://104.156.53.150/multichannel-api/raffle_engine/update';
		$data = array('raffle_id' => $_POST['theraffleid'],
					  'status' => 'ACTIVE',
					  'apitoken' => $_SESSION['api_token']);
					  

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$data = json_decode($result, true);

		if ($data["result_code"] == 200)
		{
			echo 'Notice: <font color="green">Successfully generated raffle.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">Error occurred while generating raffle.<br></font>';
		}
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['claim_coupon'])
	{
		$url = 'http://104.156.53.150/multichannel-api/coupon/do_redeemed_coupon.php';
		$data = array('couponid' => $_POST['couponid'],
					  'clientid' => $_SESSION['client_id'],
					  'customerid' => $_SESSION['login_id'],
					  'apitoken' => $_SESSION['api_token'],
					  'code' => $_POST['code']);
					

		// print_r($data);
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$claimcoup = json_decode($result, true);
		// echo '<pre>';
		// print_r($data);
		// print_r($claimcoup);
		if ($claimcoup['result_code'] == 200)
		{
			echo 'Notice: <font color="green">Successfully claimed coupon.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">' . $claimcoup['error_txt'] . '<br></font>';
		}
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['participate_in_campaign'])
	{
		// $url = 'http://104.156.53.150/multichannel-api/customer/subscribe.php';
		$url = 'http://104.156.53.150/multichannel-api/campaigns/participate_a_campaign.php';
		
		$data = array('campaignid' => $_POST['campaignid'],
					  'channelid' => $_POST['channelid'],
					  'clientid' => $_POST['client_id'],
					  'brandid' => $_POST['brandid'],
					  'points_id' => $_POST['points_id'],
					  'customerid' => $_SESSION['login_id'],
					  'status' => 'ACTIVE', 
					  'apitoken' => $_SESSION['api_token']);

		//print_r($data);
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		$data = json_decode($result, true);
		if ($data["result_code"] == 200)
		{
			echo 'Notice: <font color="green">Successfully Subscribed.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">' . $data["error_txt"] . '<br></font>';
		}
	}
?>

<?php
//foreach($campaign_ids_mapped_brand_ids as $key=>$value) {
	$url = 'http://104.156.53.150/multichannel-api/customer/retrieve_subscriptions.php';
	$data = array('customerid' => $_SESSION['login_id'], 'apitoken' => $_SESSION['api_token']);

	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'GET',
			'content' => http_build_query($data),
		),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	$data_camp = json_decode($result, true);
	if($data_camp['results'])
	{
		foreach ($data_camp['results'] as &$campaigns)
		{
			$array_of_rewards[] = array($campaigns["brandid"], $campaigns["campaignid"], $campaigns["channelid"], $campaigns['clientid']);
			$array_of_rewards_names[] = array($campaigns["brandname"], $campaigns["campaignname"], $campaigns["channelname"], $campaigns["company_name"]);
		}
	}
?>


<?php
// $url = 'http://104.156.53.150/multichannel-api/points/inquire.php';
$url = 'http://104.156.53.150/multichannel-api/points/list_customer_points.php';



$data = array('customerid' => $_SESSION['login_id'], 'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);

$param = http_build_query($data);
$file = file_get_contents($url.'?'.$param, false);
$result_arr = json_decode($file);

$total_points =  $result_arr->results->total_points;
$breakdown = $result_arr->results->breakdown;

if (!$total_points)
{
	$total_points = 0;
}
echo "<b>Current Total Points:</b> " . $total_points . "</br>";

$array_of_points = array();
$i = 0;
?>
	<br />Breakdown:</br>
	<table border="1">
		<tr>
		<th>Client</th><th>Point System Name</th><th>Points</th>
		</tr>
		
<?
foreach ($breakdown as &$reward_params)
{
?>
	<tr align="center">
		<td><?php echo $reward_params->{"clientname"}; ?></td>
		<td><?php echo $reward_params->{"pointsystemname"}; ?></td>
		<td><?php echo $reward_params->{"total"}; ?></td>
	</tr>
<?
}
echo '</table>';
if ($result_arr_add->{"result_code"} == 403)
	echo '<br>Notice: <font color="red">' . $result_arr_add->{"error_txt"} . '</font>';
else if ($result_arr_add->{"result_code"} == 200)
    echo '<br>Notice: <font color="green">Successfully added points for specified action.</font>';
else if ($result_arr_add->{"result_code"} == 405)
    echo '<br>Notice: <font color="red">Limit exceeded for specified action.</font>';
?>

</br></br>
<b>Campaigns You Can Participate In:</b>
</br>
<?php
	$url = 'http://104.156.53.150/multichannel-api/campaigns/list_campaign.php';
	$data = array('customerid' => $_SESSION['login_id'], 'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);

	$param = http_build_query($data);
	$file = file_get_contents($url.'?'.$param, false);
	$campaigns = json_decode($file);

	$list_campaign = $campaigns->results->breakdown;
	?>

	<table border=1>
	<tr>
	<th></th>
	<th>Client</th>
	<th>Brand</th>
	<th>Campaign</th>
	<th>Channel</th>
	<th>Description</th>
	</tr>
	<?
		foreach ($list_campaign as &$availableCampaigns)
		{
	?>
	<tr>
		<form action="" method="post">
		<input type="hidden" name="participate_in_campaign" value="true">
		<input type="hidden" name="client_id" value="<?php echo $availableCampaigns->clientid; ?>">
		<input type="hidden" name="campaign_id" value="<?php echo $availableCampaigns->campaignid; ?>">
		<input type="hidden" name="brand_id" value="<?php echo $availableCampaigns->brandid; ?>">
		<input type="hidden" name="channel_id" value="<?php echo $availableCampaigns->channelid; ?>">
		<input type="hidden" name="points_id" value="<?php echo $availableCampaigns->pointsid; ?>">
		<td><input style="text-align: right" type="submit" value="Click to Participate"/></td><td><?php echo $availableCampaigns->companyname; ?></td><td><?php echo $availableCampaigns->brandname; ?></td><td><?php echo $availableCampaigns->campaignname; ?></td><td><?php echo $availableCampaigns->channelname; ?></td><td><?php echo $availableCampaigns->description; ?></td>
		</form>
		</tr>
	<? } ?>
</table>

</br></br>
<b>Campaigns That You Have Participated In:</b>
</br>
<?php
//foreach($campaign_ids_mapped_brand_ids as $key=>$value) {
	// $url = 'http://104.156.53.150/multichannel-api/customer/retrieve_subscriptions.php';
	$url = 'http://104.156.53.150/multichannel-api/campaigns/list_customer_subscriptions.php';
	$data = array('customerid' => $_SESSION['login_id'], 'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);

	$param = http_build_query($data);
	$file = file_get_contents($url.'?'.$param, false);
	$data_camp = json_decode($file);

	// $data_camp = json_decode($result, true);
	$list_campaign = $data_camp->results->breakdown;
	if($list_campaign)
	{
	?>
		</br>
		<form action="" method="post">
		<table border=1>
		<tr>
		<th>Client</th><th>Brand</th><th>Campaign</th><th>Description</th>
		</tr>
		<?php
		//$array_of_rewards = array();
		foreach ($list_campaign as &$campaigns)
		{?>
			<tr align="center">
			<input type="hidden" name="campaign" value="true">
			<input type="hidden" name="campaign_id" value="<?php echo $campaigns->campaignid; ?>">
			<input type="hidden" name="brand_id" value="<?php echo $campaigns->brandid; ?>">
			</td>
			<td><?php echo $campaigns->companyname; ?></td><td><?php echo $campaigns->brandname; ?></td><td><?php echo $campaigns->campaignname; ?></td><td><?php echo $campaigns->description; ?></td>
			</tr>
			
		<?php
		}
		?>
		</table>
		</form>
		<?php
	}
	else
	{
		echo "</br>There are no campaigns available yet.</br></br>";
	}


?>


</br>
<b>Rewards Redeemable:</b>
</br>
<?php
$url = 'http://104.156.53.150/multichannel-api/reward/list_of_redeemable_rewards.php';
$data = array('customerid' => $_SESSION['login_id'], 'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);

$param = http_build_query($data);
$result = file_get_contents($url.'?'.$param, false);
$redeemable_rewards = json_decode($result);

// $redeemable_rewards = json_decode($result, true);
$arr_rdmble_rwrds = $redeemable_rewards->results->rewards;
$rewards_available_counter = 0;
// echo '<pre>PARAMS# <hr>'.@var_export($data,1).'</pre>';
// echo '<pre>';
// print_r($arr_rdmble_rwrds);
// exit();
foreach ($arr_rdmble_rwrds as &$reward)
{
?>
	<form action="" method="post">
	<p><input style="text-align: right" type="submit" value="Claim"/>  <?php echo $reward->name; ?> - <?php echo $reward->clientname; ?> - <?php echo $reward->pointssystemname;?> (worth <?php echo $reward->value; ?> point(s)) </p>
	<input type="hidden" name="claim" value="true">
	<input type="hidden" name="client_id" value="<?php echo $reward->clientid; ?>">
	<input type="hidden" name="reward_config_id" value="<?php echo $reward->rewardconfigid; ?>">
	</form>
<?php

	$rewards_available_counter++;
}

?>

<?php
if ($rewards_available_counter == 0)
{
	echo "</br>No available rewards, participate in campaigns to gain points.</br></br>";
}
//print_r($result["results"]);
//$result_arr = json_decode($result);
//echo $result_arr->{"result"};
?>

</br>
<b>Rewards Available:</b>
</br>
<?php
$url = 'http://104.156.53.150/multichannel-api/reward/list_of_rewards_available.php';
$data = array('customerid' => $_SESSION['login_id'], 'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);

$param = http_build_query($data);
$result = file_get_contents($url.'?'.$param, false);
$available_rewards = json_decode($result);
/*
$options = array(
	'http' => array(
		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'method'  => 'GET',
		'content' => http_build_query($data),
	),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

$available_rewards = json_decode($result, true);
*/
if($available_rewards->results->rewards)
	{
	?>
		</br>
		<table border=1>
		<tr>
		<th>Reward</th><th>Client</th><th>Point System Name</th><th>Points Needed</th><th>Inventory</th>
		</tr>
		<?php
		foreach ($available_rewards->results->rewards as &$rwrd)
		{
		?>
			<tr align="center">
			<td><?php echo $rwrd->rewarddetailsname; ?></td><td><?php echo $rwrd->clientname; ?><td><?php echo $rwrd->pointssystemname; ?></td><td><?php echo $rwrd->value; ?></td><td><?php echo $rwrd->inventory ?></td>
			</tr>
		<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		echo "</br>No rewards available.</br></br>";
	}
?>

<?php
/*$rewards_available_counter = 0;

foreach ($array_of_rewards as &$reward_params)
{
	$url = 'http://104.156.53.150/multichannel-api/reward/retrieve.php';
	$data = array('brandid' => $reward_params[0], 'campaignid' => $reward_params[1], 'channelid' => $reward_params[2]);

	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	$data = json_decode($result, true);
	if($data['results'])
	{
		$j = 0;
		foreach ($data['results'] as &$reward) {
			if ($reward['value'] <= $array_of_points[$j]){?>
				<form action="" method="post">
				<p><input style="text-align: right" type="submit" value="Claim"/>  <?php echo $reward['title']; ?> - <?php echo $reward['description']; ?> (worth <?php echo $reward['value']; ?> point(s)) </p>
				<input type="hidden" name="claim" value="true">
				<input type="hidden" name="points_to_deduct" value="<?php echo "-" . $reward['value']; ?>">
				<input type="hidden" name="channel_id" value="<?php echo $reward['channelid']; ?>">
				<input type="hidden" name="campaign_id" value="<?php echo $reward['campaignid']; ?>">
				<input type="hidden" name="brand_id" value="<?php echo $reward['brandid']; ?>">
				<input type="hidden" name="reward_id" value="<?php echo $reward['rewardid']; ?>">
				<input type="hidden" name="client_id" value="<?php echo $reward['clientid']; ?>">
				<input type="hidden" name="reward_config_id" value="<?php echo $reward['rewardconfigid']; ?>">
				<input type="hidden" name="current_inventory" value="<?php echo $reward['currentinventory']; ?>">
				</form>
			<?php
				$rewards_available_counter++;
				$campaign_ids_mapped_brand_ids[$reward['campaignid']] = $reward['brandid'];
			}
			$j++;
		}
	}
}*/
?>


</br>
<b>Redeemed Rewards:</b>
</br>
<?php
$url = 'http://104.156.53.150/multichannel-api/reward/list_of_redeemed_rewards.php';
$data = array('customerid' => $_SESSION['login_id'],'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);


$param = http_build_query($data);
$result = file_get_contents($url.'?'.$param, false);
$data = json_decode($result);

$redeemed= $data->results->rewards;
// echo '<pre>';
// print_r($data);
// print_r($data['results']);
// exit();
	if($redeemed)
	{
	?>
		</br>
		<table border=1>
		<tr>
		<th>Reward</th><th>Client</th><th>Point System Name</th><th>Points Spent</th><th>Date Redeemed</th>
		</tr>
		<?php
		foreach ($redeemed as &$redeemed_reward)
		{?>
			<tr align="center">
			<td><?php echo $redeemed_reward->name; ?></td><td><?php echo $redeemed_reward->companyname; ?><td><?php echo $redeemed_reward->pointsystemname; ?></td><td><?php echo $redeemed_reward->value; ?></td><td><?php echo $redeemed_reward->dateredeemed; ?></td>
			</tr>
			
		<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		echo "</br>You haven't redeemed any rewards.</br></br>";
	}

?>



</br>
<b>Actions (Gain Points):</b>
</br></br>
<table border=1>
<tr>
<th>&nbsp;</th>
<th>Client</th>
<th>Brand</th>
<th>Campaign</th>
<th>Channel</th>
<th>Action</th>
<th>Points Reward</th>
<th>Max Daily</th>
<th>Max Overall</th>
</tr>
<?php

	$url = 'http://104.156.53.150/multichannel-api/points/list_action_points.php';
$data = array('customerid' => $_SESSION['login_id'],'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);

$param = http_build_query($data);
$result = file_get_contents($url.'?'.$param, false);
$data = json_decode($result);

$points = $data->results->breakdown;

if ($points)
{
	foreach ($points AS &$actionpoints)
	{
		?>
			<tr  align="center">
			
			<form action="" method="post">
			<td><?php //if ($row['pointaction'] != "Likes" && 0 != strcasecmp($row['pointaction'], "Like")) {
			       if (true) {
				?><input type="Submit" value="Participate"/><?php }else {?><div class="fb-like"data-layout="button" data-action="like" data-show-faces="false" data-share="false"><?php }?></div>
			<input type="hidden" name="action" value="true">
			<input type="hidden" name="brand_id" value="<?php 
			echo $actionpoints->brandid;
			
			?>">
			<input type="hidden" name="channel_id" value="<?php echo $actionpoints->channelid; ?>">
			<input type="hidden" name="campaign_id" value="<?php echo $actionpoints->campaignid; ?>">
			<input type="hidden" name="brand_id" value="<?php echo $actionpoints->brandid; ?>">
			<input type="hidden" name="actiontype_id" value="<?php echo $actionpoints->actiontypeid; ?>">
			</form>
			</td>
			<td><?php echo $actionpoints->companyname; ?></td>
			<td><?php echo $actionpoints->brandname; ?></td>
			<td><?php echo $actionpoints->campaignname; ?></td>
			<td><?php echo $actionpoints->channelname; ?></td>
			<td><?php echo $actionpoints->pointsaction; ?></td>
			<td><?php echo "+" . $actionpoints->value; ?></td>
			<td><?php echo $actionpoints->pointslimit; ?></td>
			<td><?php if ($actionpoints->pointscapping == "DAILY") echo "N/A"; else echo $actionpoints->pointslimit ?></td>
			</tr>
		<?
	}
}

?>
</table>


	
<span id="claimcoupon">
<br><br><b>Claim Coupon:<b><br><br>
	<table>
	<form action="" method="post">
		<input type="hidden" name="claim_coupon" value="true">
		<tr><td>Coupon Id<td/><td><input type="text" name="coupon_id" value=""></td></tr>
		<tr><td>Code<td/><td><input type="text" name="code" value=""></td></tr>
		<tr><td><td/><td><input type="submit" name="submit" value="Claim Coupon"/></td></tr>
	</form>
	<table/>
</br></br>
</span>
	
<span  id="gencoupons"/>
</br></br>
	<b>Generated Coupons:</b>	</br>

<?php
	$url = 'http://104.156.53.150/multichannel-api/coupon/list_available_coupon.php';
	$data = array('customerid' => $_SESSION['login_id'],'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);
	
	$param = http_build_query($data);
	$result = file_get_contents($url.'?'.$param, false);
	$data = json_decode($result);

	$coupavail = $data->results->coupon;
	// echo '<pre>';
	// print_r($coupavail);
	// exit();
	
	if($coupavail)
	{
	?>
		<table border=1>
		<tr>
			<th>Client</th>
			<th>Point System</th>
			<th>Coupon Id</th>
			<th>Code</th>
		</tr>
		
		<?php
		foreach ($coupavail as &$available_coupon)
		{?>
			<tr align="center">
				<td><?php echo $available_coupon->clientname; ?></td>
				<td><?php echo $available_coupon->pointssystemname; ?></td>
				<td><?php echo $available_coupon->couponid; ?></td>
				<td><?php echo $available_coupon->code; ?></td>
			</tr>
			
		<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		echo "</br>You haven't generated any coupons.</br></br>";
	}
	?>

</br></br>
<b>Redeemed Coupons:</b>
</br></br>
<span  id="redeemedcoupons"/>
<?php
$url = 'http://104.156.53.150/multichannel-api/coupon/list_redeemed_coupon.php';

	$data = array('customerid' => $_SESSION['login_id'],'clientid' => $_SESSION['client_id'], 'apitoken' => $_SESSION['api_token']);
	
	$param = http_build_query($data);
	$result = file_get_contents($url.'?'.$param, false);
	$results = json_decode($result);
$redeemed = $results->results->coupon;
	if($redeemed)
	{
	?>
		</br>
		
		<table border=1>
		<tr>
			<th>Client</th>
			<th>Point System</th>
			<th>Coupon Id</th>
			<th>QR Code</th>
			<th>Code</th>
			<th>Status</th>
			<th>Date Redeemed</th>
		</tr>
		<?php
		foreach ($redeemed as &$redeemed_coupon)
		{?>
			<tr align="center">
				<td><?php echo $redeemed_coupon->clientname; ?></td>
				<td><?php echo $redeemed_coupon->pointssystemname; ?></td>
				<td><?php echo $redeemed_coupon->couponid; ?></td>
				<td><img src="<?php echo $redeemed_coupon->qr_code; ?>"></td>
				<td><?php echo $redeemed_coupon->code; ?></td>
				<td><?php echo $redeemed_coupon->status; ?></td>
				<td><?php echo $redeemed_coupon->dateredeemed; ?></td>
			</tr>
			
		<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		echo "</br>You haven't redeemed any coupons.</br></br>";
	}
?>

<!--<br><br><b>Generate Raffle:<b><br><br>
	<table>
	<form action="" method="post">
		<input type="hidden" name="generate_raffle" value="true">
		<tr><td>Coupon Id<td/><td>
		<select name="thecoupid">
			<?php 
			//foreach ($coupAL_arr as &$tmp_data)
			//{?>
			<option value="<?php //echo $tmp_data?>"><?php //echo $tmp_data?></option>
			<?php //}?>
		</select>
		</td></tr>
		<tr><td>Fda No<td/><td><input type="text" name="thefdano" value="1234"></td></tr>
		<tr><td>No Of Winners<td/><td><input type="text" name="thenoofwinners" value="1"></td></tr>
		<tr><td><td/><td><input type="submit" name="submit" value="Generate"/></td></tr>
	</form>
	<table/>
</br></br>-->


<?php
//if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['logout'])
if(isset($_POST) and $_POST['logout'] === 'true')
{
	session_destroy();
	header("Location: login");
}
?>

</body>
</html>
