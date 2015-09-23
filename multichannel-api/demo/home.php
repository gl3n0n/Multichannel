<?php
	require_once('../config/database.php');
	session_start();
	if (!isset($_SESSION['login_user']))
	{
		header("Location: login");
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
	$data = array('client_id' => $_POST['client_id'], 'customer_id' => $_SESSION['login_id'],
				  'reward_config_id' => $_POST['reward_config_id']);
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
	$campaign_id = $_POST['campaign_id'];
	$brand_id = $_POST['brand_id'];
	$channel_id = $_POST['channel_id'];

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
	//if (empty($_POST['channel_id']) && empty($_POST['channel_id'])
	$data = array('actiontype_id' => $_POST['actiontype_id'], 'customer_id' => $_SESSION['login_id'],
				  'channel_id' => $_POST['channel_id'], 'campaign_id' => $_POST['campaign_id'],
				  'brand_id' => $_POST['brand_id'], 'client_id' => $_SESSION['client_id']);
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
	//$result_arr_add["pointofaction"] = $_POST['pointofaction'];
}

?>

<?php
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['update_coupon'])
	{
		$url = 'http://104.156.53.150/multichannel-api/coupon/generate.php';
		$data = array('coupon_id' => $_POST['thecouponid']);

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
		$data = array('coupon_id' => $_POST['thecouponid']);

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
		$data = array('coupon_id' => $_POST['coupon_id'],
					  'customer_id' => $_POST['customer_id'],
					  'generated_coupon_id' => $_POST['generated_coupon_id'],
					  'coupon_mapping_id' => $_POST['coupon_mapping_id'],
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
		$data = array('client_id' => '1',
					  'brand_id' => '1',
					  'channel_id' => '1',
					  'campaign_id' => '2',
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
		$data = array('coupon_id' => $_POST['thecoupid'],
		              'client_id' => '1',
					  'brand_id' => '1',
					  'channel_id' => '1',
					  'campaign_id' => '2',
					  'no_of_winners' => $_POST['thenoofwinners'],
					  'fda_no' => $_POST['thefdano'],
					  'source' => 'DEMO',
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
	
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['claim_coupon'])
	{
		$url = 'http://104.156.53.150/multichannel-api/coupon/do_redeemed_coupon.php';
		$data = array('coupon_id' => $_POST['coupon_id'],
					  'client_id' => $_SESSION['client_id'],
					  'customer_id' => $_SESSION['login_id'],
					  'code' => $_POST['code']);
					

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

		$claimcoup = json_decode($result, true);
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
		
		$data = array('campaign_id' => $_POST['campaign_id'],
					  'channel_id' => $_POST['channel_id'],
					  'client_id' => $_POST['client_id'],
					  'brand_id' => $_POST['brand_id'],
					  'points_id' => $_POST['points_id'],
					  'customer_id' => $_SESSION['login_id'],
					  'status' => 'ACTIVE');

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
			echo 'Notice: <font color="green">Successfully Participated coupon.<br></font>';
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
	$data = array('customer_id' => $_SESSION['login_id']);

	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
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
$data = array('customer_id' => $_SESSION['login_id'], 'client_id' => $_SESSION['client_id']);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

$result_arr = json_decode($result);

$total_points =  $result_arr->{"results"}->{"total_points"};
$breakdown = $result_arr->{"results"}->{"breakdown"};

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
	$data = array('customer_id' => $_SESSION['login_id'], 'client_id' => $_SESSION['client_id']);

	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	
	$campaigns = json_decode($result);
	$list_campaign = $campaigns->{"results"}->{"breakdown"};
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
		<input type="hidden" name="client_id" value="<?php echo $availableCampaigns->{"clientid"}; ?>">
		<input type="hidden" name="campaign_id" value="<?php echo $availableCampaigns->{"campaignid"}; ?>">
		<input type="hidden" name="brand_id" value="<?php echo $availableCampaigns->{"brandid"}; ?>">
		<input type="hidden" name="channel_id" value="<?php echo $availableCampaigns->{"channelid"}; ?>">
		<input type="hidden" name="points_id" value="<?php echo $availableCampaigns->{"pointsid"}; ?>">
		<td><input style="text-align: right" type="submit" value="Click to Participate"/></td><td><?php echo $availableCampaigns->{"companyname"}; ?></td><td><?php echo $availableCampaigns->{"brandname"}; ?></td><td><?php echo $availableCampaigns->{"campaignname"}; ?></td><td><?php echo $availableCampaigns->{"channelname"}; ?></td><td><?php echo $availableCampaigns->{"description"}; ?></td>
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
	$data = array('customer_id' => $_SESSION['login_id'], 'client_id' => $_SESSION['client_id']);

	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	$data_camp = json_decode($result, true);
	$list_campaign = $data_camp['results']['breakdown'];
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
			<input type="hidden" name="campaign_id" value="<?php echo $campaigns['campaignid']; ?>">
			<input type="hidden" name="brand_id" value="<?php echo $campaigns['brandid']; ?>">
			</td>
			<td><?php echo $campaigns["companyname"]; ?></td><td><?php echo $campaigns["brandname"]; ?></td><td><?php echo $campaigns["campaignname"]; ?></td><td><?php echo $campaigns["description"]; ?></td>
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
$data = array('customer_id' => $_SESSION['login_id'], 'client_id' => $_SESSION['client_id']);

$options = array(
	'http' => array(
		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'method'  => 'POST',
		'content' => http_build_query($data),
	),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

$redeemable_rewards = json_decode($result, true);
$arr_rdmble_rwrds = $redeemable_rewards["results"]["rewards"];
$rewards_available_counter = 0;
//echo '<pre>';
//print_r($arr_rdmble_rwrds);
//exit();
foreach ($arr_rdmble_rwrds as &$reward)
{
?>
	<form action="" method="post">
	<p><input style="text-align: right" type="submit" value="Claim"/>  <?php echo $reward['title']; ?> - <?php echo $reward['clientname']; ?> - <?php echo $reward['pointssystemname'];?> (worth <?php echo $reward['value']; ?> point(s)) </p>
	<input type="hidden" name="claim" value="true">
	<input type="hidden" name="client_id" value="<?php echo $reward['clientid']; ?>">
	<input type="hidden" name="reward_config_id" value="<?php echo $reward['rewardconfigid']; ?>">
	</form>
<?php
	$rewards_available_counter++;
}

?>

</br>
<b>Rewards Available:</b>
</br>
<?php
$url = 'http://104.156.53.150/multichannel-api/reward/list_of_rewards_available.php';
$data = array('customer_id' => $_SESSION['login_id'], 'client_id' => $_SESSION['client_id']);

$options = array(
	'http' => array(
		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'method'  => 'POST',
		'content' => http_build_query($data),
	),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

$available_rewards = json_decode($result, true);
if($available_rewards['results']['rewards'])
	{
	?>
		</br>
		<table border=1>
		<tr>
		<th>Reward</th><th>Client</th><th>Point System Name</th><th>Points Needed</th><th>Inventory</th>
		</tr>
		<?php
		foreach ($available_rewards['results']['rewards'] as &$rwrd)
		{
		?>
			<tr align="center">
			<td><?php echo $rwrd["title"]; ?></td><td><?php echo $rwrd["clientname"]; ?><td><?php echo $rwrd["pointssystemname"]; ?></td><td><?php echo $rwrd["value"]; ?></td><td><?php echo $rwrd["inventory"] ?></td>
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
	$data = array('brand_id' => $reward_params[0], 'campaign_id' => $reward_params[1], 'channel_id' => $reward_params[2]);

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
<b>Redeemed Rewards:</b>
</br>
<?php
$url = 'http://104.156.53.150/multichannel-api/reward/list_of_redeemed_rewards.php';
$data = array('customer_id' => $_SESSION['login_id'],'client_id' => $_SESSION['client_id']);

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
$redeemed= $data['results']['rewards'];
//echo '<pre>';
//print_r($data);
//exit();
//print_r($data['results']);
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
			<td><?php echo $redeemed_reward["title"]; ?></td><td><?php echo $redeemed_reward["clientname"]; ?><td><?php echo $redeemed_reward["pointssystemname"]; ?></td><td><?php echo $redeemed_reward["value"]; ?></td><td><?php echo $redeemed_reward["dateredeemed"]; ?></td>
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
$data = array('customer_id' => $_SESSION['login_id'],'client_id' => $_SESSION['client_id']);

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
$points = $data['results']['breakdown'];

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
			echo $actionpoints['brandid'];
			
			?>">
			<input type="hidden" name="channel_id" value="<?php echo $actionpoints['channelid']; ?>">
			<input type="hidden" name="campaign_id" value="<?php echo $actionpoints['campaignid']; ?>">
			<input type="hidden" name="brand_id" value="<?php echo $actionpoints['brandid']; ?>">
			<input type="hidden" name="actiontype_id" value="<?php echo $actionpoints['actiontypeid']; ?>">
			</form>
			</td>
			<td><?php echo $actionpoints['companyname']; ?></td>
			<td><?php echo $actionpoints['brandname']; ?></td>
			<td><?php echo $actionpoints['campaignname']; ?></td>
			<td><?php echo $actionpoints['channelname']; ?></td>
			<td><?php echo $actionpoints['pointsaction']; ?></td>
			<td><?php echo "+" . $actionpoints['value']; ?></td>
			<td><?php echo $actionpoints['pointslimit']; ?></td>
			<td><?php if ($actionpoints['pointscapping'] == "DAILY") echo "N/A"; else echo $actionpoints['pointslimit']; ?></td>
			</tr>
		<?
	}
}

?>
</table>


	</br></br></br></br>
	<b>>>>>> COUPONS AND RAFFLES DEMO <<<<<< </b></br></br>
	<b>Pending Coupons:</b>	</br>
<?php
	$url = 'http://104.156.53.150/multichannel-api/coupon/retrieve.php';
	/*$data = array(client_id' => '1',
	              'brand_id' => '1',
				  'channel_id' => '1',
				  'campaign_id' => '2',
				  'status' => 'PENDING');*/
	$data = array('status' => 'PENDING', 'client_id' => $_SESSION['client_id']);

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
	?>
		<table border=1>
		<tr>
		<th></th><th>Coupon Id</th><th>Quantity</th><th>Limit Per User</th><th>File</th><th>Brands</th><th>Channels</th><th>Campaigns</th>
		</tr>
		<?php
		$coupAL_arr = array();
		foreach ($data['results'] as &$coup)
		{
			$coupAL_arr[] = $coup["couponid"];
			?>
			<tr align="center">
			<td>
			<form action="#gencoupons" method="post">
				<input type="hidden" name="update_coupon" value="true">
				<input type="hidden" name="theclientid" value="<?php echo $coup["clientid"]; ?>">
				<input type="hidden" name="thebrandid" value="<?php echo $coup["brandid"]; ?>">
				<input type="hidden" name="thecampaignid" value="<?php echo $coup["campaignid"]; ?>">
				<input type="hidden" name="thechannelid" value="<?php echo $coup["channelid"]; ?>">
				<input type="hidden" name="thecouponid" value="<?php echo $coup["couponid"]; ?>">
				<input type="submit" name="submit" value="Generate"/>
			</form>
			</td>
			<td><?php echo $coup["couponid"]; ?></td><td><?php echo $coup["quantity"]; ?></td><td><?php echo $coup["limitperuser"]; ?></td><td><?php echo $coup["file"]; ?></td>
			<td><?php echo $coup["brandnames"]; ?></td><td><?php echo $coup["channelnames"]; ?></td><td><?php echo $coup["campaignnames"]; ?></td>
			</tr>
			
		<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		echo "</br>No pending coupons.</br></br>";
	}
	?>
	
	</br></br></br></br>
	<b>Pending Updated Coupons:</b>	</br>
	
<?php
	$url = 'http://104.156.53.150/multichannel-api/coupon/pending-edit-retrieve.php';
	/*$data = array('client_id' => '1',
	              'brand_id' => '1',
				  'channel_id' => '1',
				  'campaign_id' => '2',
				  'status' => 'PENDING');*/
	$data = array('edit_flag' => '1');

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
	?>
		<table border=1>
		<tr>
		<th></th><th>Coupon Id</th><th>Quantity</th><th>Limit Per User</th><th>File</th><th>Brands</th><th>Channels</th><th>Campaigns</th>
		</tr>
		<?php
		$coupAL_arr = array();
		foreach ($data['results'] as &$coup)
		{
			$coupAL_arr[] = $coup["couponid"];
			?>
			<tr align="center">
			<td>
			<form action="#updatecoupons" method="post">
				<input type="hidden" name="update_edit_coupon" value="true">
				<input type="hidden" name="theclientid" value="<?php echo $coup["clientid"]; ?>">
				<input type="hidden" name="thebrandid" value="<?php echo $coup["brandid"]; ?>">
				<input type="hidden" name="thecampaignid" value="<?php echo $coup["campaignid"]; ?>">
				<input type="hidden" name="thechannelid" value="<?php echo $coup["channelid"]; ?>">
				<input type="hidden" name="thecouponid" value="<?php echo $coup["couponid"]; ?>">
				<input type="submit" name="submit" value="Generate"/>
			</form>
			</td>
			<td><?php echo $coup["couponid"]; ?></td><td><?php echo $coup["quantity"]; ?></td><td><?php echo $coup["limitperuser"]; ?></td><td><?php echo $coup["file"]; ?></td>
			<td><?php echo $coup["brandnames"]; ?></td><td><?php echo $coup["channelnames"]; ?></td><td><?php echo $coup["campaignnames"]; ?></td>
			</tr>
			
		<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		echo "</br>No pending edit coupons.</br></br>";
	}
	?>

	
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
	$data = array('customer_id' => $_SESSION['login_id'],'client_id' => $_SESSION['client_id']);

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
	$coupavail = $data['results']['coupon'];
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
			<th>QR Code</th>
			<th>Code</th>
		</tr>
		
		<?php
		foreach ($coupavail as &$available_coupon)
		{?>
			<tr align="center">
				<td><?php echo $available_coupon["clientname"]; ?></td>
				<td><?php echo $available_coupon["pointssystemname"]; ?></td>
				<td><?php echo $available_coupon["couponid"]; ?></td>
				<td><img src="<?php echo $available_coupon["qr_code"]; ?>"></td>
				<td><?php echo $available_coupon["code"]; ?></td>
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

	$data = array('customer_id' => $_SESSION['login_id'],'client_id' => $_SESSION['client_id']);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ),
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

$results = json_decode($result, true);
$redeemed = $results['results']['coupon'];
//print_r($data['results']);
// echo '<pre>';
// print_r($redeemed);
// print_r($results);
// exit();
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
				<td><?php echo $redeemed_coupon["clientname"]; ?></td>
				<td><?php echo $redeemed_coupon["pointssystemname"]; ?></td>
				<td><?php echo $redeemed_coupon["couponid"]; ?></td>
				<td><img src="<?php echo $redeemed_coupon["qr_code"]; ?>"></td>
				<td><?php echo $redeemed_coupon["code"]; ?></td>
				<td><?php echo $redeemed_coupon["status"]; ?></td>
				<td><?php echo $redeemed_coupon["dateredeemed"]; ?></td>
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

</br></br>
	<b>Pending Raffles:</b>	</br>
<?php
	$url = 'http://104.156.53.150/multichannel-api/raffle_engine/retrieve';
	/*$data = array('client_id' => '1',
	              'brand_id' => '1',
				  'channel_id' => '1',
				  'campaign_id' => '2',
				  'status' => 'PENDING');*/
	$data = array('client_id' => $_SESSION['client_id'], 'status' => 'PENDING');

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
	?>
		<table border=1>
		<tr>
		<th></th><th>Coupon Id</th><th>Raffle Id</th><th>No. Of Winners</th><th>FDA NO.</th>
		</tr>
		<?php
		foreach ($data['results'] as &$raff)
		{?>
			<tr align="center">
			<td>
			<form id="pend_draw" action="#draw" method="post">
				<input type="hidden" name="update_raffle" value="true">
				<input type="hidden" name="theclientid" value="<?php echo $raff["clientid"]; ?>">
				<input type="hidden" name="thebrandid" value="<?php echo $raff["brandid"]; ?>">
				<input type="hidden" name="thecampaignid" value="<?php echo $raff["campaignid"]; ?>">
				<input type="hidden" name="thechannelid" value="<?php echo $raff["channelid"]; ?>">
				<input type="hidden" name="thecouponid" value="<?php echo $raff["couponid"]; ?>">
				<input type="hidden" name="thenoofwinners" value="<?php echo $raff["noofwinners"]; ?>">
				<input type="hidden" name="theraffleid" value="<?php echo $raff["raffleid"]; ?>">
				<input type="submit" name="submit" value="Approve Raffle"/>
			</form>
			</td>
			<td><?php echo $raff["couponid"]; ?></td><td><?php echo $raff["raffleid"]; ?></td><td><?php echo $raff["noofwinners"]; ?></td><td><?php echo $raff["fdano"]; ?></td>
			</tr>
			
		<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		echo "</br>You haven't generated any raffles.</br></br>";
	}
	?>

	</br></br>
	<b>Raffles:</b>	</br>
<?php
	$url = 'http://104.156.53.150/multichannel-api/raffle_engine/retrieve';
	/*$data = array('client_id' => '1',
	              'brand_id' => '1',
				  'channel_id' => '1',
				  'campaign_id' => '2',
				  'status' => 'ACTIVE');*/
	//$data = array('status' => 'ACTIVE');
	$data = array('client_id' => $_SESSION['client_id'], 'status' => 'ACTIVE');

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
	?>
		<table border=1>
		<tr>
		<th></th><th>Coupon Id</th><th>Raffle Id</th><th>No. Of Winners</th><th>FDA NO.</th>
		</tr>
		<?php
		foreach ($data['results'] as &$raff)
		{?>
			<tr align="center">
			<td>
			<form id="act_draw" action="#selectwinners" method="post">
				<input type="hidden" name="raffle_draw" value="true">
				<input type="hidden" name="theclientid" value="<?php echo $raff["clientid"]; ?>">
				<input type="hidden" name="thebrandid" value="<?php echo $raff["brandid"]; ?>">
				<input type="hidden" name="thecampaignid" value="<?php echo $raff["campaignid"]; ?>">
				<input type="hidden" name="thechannelid" value="<?php echo $raff["channelid"]; ?>">
				<input type="hidden" name="thecouponid" value="<?php echo $raff["couponid"]; ?>">
				<input type="hidden" name="thenoofwinners" value="<?php echo $raff["noofwinners"]; ?>">
				<input type="hidden" name="theraffid" value="<?php echo $raff["raffleid"]; ?>">
				<input type="submit" name="submit" value="Generate Participants"/>
			</form>
			</td>
			<td><?php echo $raff["couponid"]; ?></td><td><?php echo $raff["raffleid"]; ?></td><td><?php echo $raff["noofwinners"]; ?></td><td><?php echo $raff["fdano"]; ?></td>
			</tr>
			
		<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		echo "</br>You haven't generated any raffles.</br></br>";
	}
	?>
	
<span id="draw"/>
<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['raffle_draw'])
{
	$coups = $dbconn->quote($_POST['thecouponid'],'integer');
	$noofwinners = $dbconn->quote($_POST['thenoofwinners'],'integer');
	$query = "SELECT distinct(Email),customers.CustomerId as CustomerId from generated_coupons join customers on generated_coupons.CustomerId = customers.CustomerId where CouponId = $coups";
	//echo $query;
	$res = $dbconn->query($query);
	?>
	</br></br>
	<b>Please select the participants for the raffle draw:</b></br>
	<form  action="#selectwinners" method="post">
				<input type="hidden" name="show_winner" value="true">
				<input type="hidden" name="thecouponid" value="<?php echo $_POST["thecouponid"]; ?>">
				<input type="hidden" name="thenoofwinners" value="<?php echo $_POST["thenoofwinners"]; ?>">
				<input type="hidden" name="theraffid" value="<?php echo $_POST["theraffid"]; ?>">
				<?php while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) { ?>
					<input type="checkbox" checked="true" name="winner[]" value="<?php echo $row["customerid"] ?>"><?php echo $row["email"]; ?></input></br>
				<?php } ?>
				<input type="submit" name="submit" value="Draw Winner"/>
	</form>
<?php
}
?>	

<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['show_winner'])
{
	//var_dump($_POST);
	echo "</br></br><b>Winners:</b></br>";
	//$coups = $dbconn->quote($_POST['thecouponid'],'integer');
	//$noofwinners = $dbconn->quote($_POST['thenoofwinners'],'integer');
	$raffid = $dbconn->quote($_POST['theraffid'],'integer');
	$participants = "";
	foreach ($_POST["winner"] as &$participant)
	{
		$participants = $participants . "," . $participant;
	}
	$participants = substr($participants, 1);

	$url = 'http://104.156.53.150/multichannel-api/raffle_engine/draw_winner.php';
	$data = array('raffle_id' => $raffid,
				  'participants' => $participants);
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);

	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	//var_dump($data);
	//var_dump($result);
	$data = json_decode($result, true);

	foreach ($data['winners'] as &$winner)
	{
		echo "<font color=\"green\">" . $winner["email"] . "</font><br>";
	}

	echo "</br><b>Backup Winners:</b></br>";
	foreach ($data['backup_winners'] as &$winner)
	{
		echo "<font color=\"green\">" . $winner["email"] . "</font><br>";
	}

	/*if (sizeof($_POST["winner"]) < $noofwinners)
	{
		$noofwinners = sizeof($_POST["winner"]);
	}

	//echo $noofwinners;
	//print_r($_POST["winner"]); 
	$rand_keys = array_rand($_POST["winner"], $noofwinners);
	
	if ($rand_keys == 0)
		$rand_keys = array(0);
	
	$therealwinners = $_POST["winner"];
	foreach ($rand_keys as &$winner)
	{
		echo "<font color=\"green\">" . $therealwinners[$winner] . "</font><br>";
	}*/
	
}
?>
<span id="selectwinners"/>


<?php
/*if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['raffle_draw'])
{
	$coups = $dbconn->quote($_POST['thecouponid'],'integer');
	$noofwinners = $dbconn->quote($_POST['thenoofwinners'],'integer');
	$query = "SELECT distinct(Email) from redeemed_coupon join customers on redeemed_coupon.CustomerId = customers.CustomerId where CouponId = $coups ORDER BY RAND() LIMIT $noofwinners";
			
	//echo $query;
	$res = $dbconn->query($query);
	echo "<br><br>Current Winner(s) For Coupon $coups:<br>";
	$i = 0;
	while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
	{
		$i++;
		echo "<font color=\"green\">" . $row["email"] . "</font><br>";
	}
	
	if ($i == 0)
	{
		echo "<font color=\"red\">No Winners!</font><br>";
	}
}*/
?>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['logout'])
{
	session_destroy();
	header("Location: login");
}
?>

</body>
</html>
