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
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=197717040307202";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

window.onload = function () {	
	var page_like_or_unlike_callback = function(url, html_element) {
	  console.log(url);
	  $.post("#",
        {
          action: "true",
		  campaign_id : 2,
		  brand_id : 1,
		  channel_id : 1,
		  points_id: 98,
		  pointofaction : "Likes",
        },
        function(data,status){
			location.reload();  
        });
	}
	// In your onload handler
	FB.Event.subscribe('edge.create', page_like_or_unlike_callback);
}


// added test
window.fbAsyncInit = function() {
    FB.init({
      appId      : '414055372089101',
      xfbml      : true,
      version    : 'v2.3'
    });

    // ADD ADDITIONAL FACEBOOK CODE HERE
	function onLogin(response) {
  if (response.status == 'connected') {
    FB.api('/me?fields=first_name', function(data) {
      var welcomeBlock = document.getElementById('fb-welcome');
      welcomeBlock.innerHTML = 'Hello, ' + data.first_name + '!';
    });
  }
}

FB.getLoginStatus(function(response) {
  // Check login status on load, and if the user is
  // already logged in, go directly to the welcome message.
  if (response.status == 'connected') {
    onLogin(response);
  } else {
    // Otherwise, show Login dialog first.
    FB.login(function(response) {
      onLogin(response);
    }, {scope: 'user_friends, email'});
  }
});
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

<h1 id="fb-welcome"></h1>

<form action="" method="post">
<table>
<tr>
<td><p> Welcome<?php //echo strtoupper($_SESSION['login_user']); ?>! </p></td>
<td><input type="hidden" name="logout" value="true"><input style="text-align: right" type="submit" value="Logout"/></td>
</tr>
</table>
</form>


<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['claim'])
{

	$url = 'http://104.156.53.150/multichannel-api/points/update.php';
	$data = array('subscription_id' => $_SESSION['subscription_id'], 'points' => $_POST['points_to_deduct'], 'brand_id' => $_POST['brand_id'], 'action' => 'CLAIM');
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

	//echo $result;
	// ADD TO REDEEMED REWARDS
    $url = 'http://104.156.53.150/multichannel-api/reward/redeem.php';
	$data = array('client_id' => $_SESSION['client_id'], 'user_id' => $_SESSION['login_id'],
				  'channel_id' => $_POST['channel_id'], 'campaign_id' => $_POST['campaign_id'],
				  'brand_id' => $_POST['brand_id'],'reward_id' => $_POST['reward_id'],
				  'source' => 'POINTS', 'action' => 'Claim Reward', 'reward_config_id' => $_POST['reward_config_id'], 'current_inventory' => $_POST['current_inventory']);
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	// print_r($data);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
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
	 $url = 'http://104.156.53.150/multichannel-api/points/update.php';
	//$i = 1;
	//echo $_POST['thesubmit'];
	//$GLOBALS
	//if (empty($_POST['channel_id']) && empty($_POST['channel_id'])
	$data = array('subscription_id' => $_POST['subid'], 'customer_id' => $_SESSION['login_id'],
				  'channel_id' => $_POST['channel_id'], 'campaign_id' => $_POST['campaign_id'],
				  'brand_id' => $_POST['brand_id'],'points' => $_POST['points'], 'points_id' => $_POST['points_id'], 'client_id' => $_POST['client_id'], 'action' => 'ADD');
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
		$url = 'http://104.156.53.150/multichannel-api/coupon/redeem.php';
		$data = array('coupon_id' => $_POST['thecouponid'],
					  'generated_coupon_id' => $_POST['thegeneratedcouponid'],
					  'coupon_mapping_id' => $_POST['the_rdm_option'],
					  'customer_id' => $_POST['thecustid']);

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
			echo 'Notice: <font color="green">Successfully claimed coupon.<br></font>';
		}
		else if ($data["result_code"] == 409)
		{
			echo 'Notice: <font color="red">Limit exceeded for this coupon.<br></font>';
		}
		else
		{
			echo 'Notice: <font color="red">Invalid Coupon.<br></font>';
		}
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['participate_in_campaign'])
	{
		$url = 'http://104.156.53.150/multichannel-api/customer/subscribe.php';
		$data = array('campaign_id' => $_POST['campaign_id'],
					  'channel_id' => $_POST['channel_id'],
					  'client_id' => $_POST['client_id'],
					  'brand_id' => $_POST['brand_id'],
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
$url = 'http://104.156.53.150/multichannel-api/points/inquire.php';
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

$result_arr = json_decode($result);
$curr_bal =  $result_arr->{"balance"};
if (!$curr_bal)
{
	$curr_bal = 0;
}
echo "<b>Current Total Points:</b> " . $curr_bal . "</br>";

$array_of_points = array();
$i = 0;
foreach ($array_of_rewards as &$reward_params)
{
	$url2 = 'http://104.156.53.150/multichannel-api/points/inquire.php';
	$data2 = array('customer_id' => $_SESSION['login_id'], 'brand_id' => $reward_params[0], 'campaign_id' => $reward_params[1], 'channel_id' => $reward_params[2]);
	
	$options2 = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data2),
		),
	);
	
	$context2  = stream_context_create($options2);
	$result2 = file_get_contents($url2, false, $context2);

	$result_arr2 = json_decode($result2);
	$array_of_points[] =  $result_arr2->{"balance"};
	/*$s =  $result_arr2->{"balance"};
	if (!$s)
	{
		$s = 0;
	}
	echo "<b>Points:</b> " . $s;*/

	$i++;
}

if ($i > 0)
{
	?>
	Breakdown:</br>
	<table border="1">
	<tr>
	<th>Client</th><th>Brand</th><th>Campaign</th><th>Channel</th><th>Points</th>
	</tr>
	<?php for ($counter = 0; $counter < $i; $counter++)
		  {?>
		  <tr align="center">
		  <td><?php echo $array_of_rewards_names[$counter][3]; ?></td>
		  <td><?php echo $array_of_rewards_names[$counter][0]; ?></td>
		  <td><?php echo $array_of_rewards_names[$counter][1]; ?></td>
		  <td><?php echo $array_of_rewards_names[$counter][2]; ?></td>
		  <td><?php echo $array_of_points[$counter];?></td>
		  </tr>
		  <?php
		  }
	?>
	</table>
	<?php
}

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
	/*$url = 'http://104.156.53.150/multichannel-api/campaigns/retrieve.php';
	$data = array();

	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);*/
	$qq = "select clients.clientid as clientid, brands.brandid as brandid,channels.channelid as channelid,campaigns.campaignid as campaignid,companyname,brandname,channelname,campaignname,campaigns.description as description from channels join campaigns on channels.CampaignId = campaigns.CampaignId join brands on brands.BrandId = campaigns.BrandId join clients on campaigns.clientid = clients.clientid;";
	$d = $dbconn->query($qq);
	//;

	//$data = json_decode($result, true);
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
	<?php
	$available_campaigns_count = 0;
	while($campaign = $d->fetchRow(MDB2_FETCHMODE_ASSOC))
	{
		$skip = false;
		foreach ($array_of_rewards as &$oops)
		{
			//print_r($campaign);
			if ($campaign["brandid"] == $oops[0] && $campaign["campaignid"] == $oops[1] && $campaign["channelid"] == $oops[2])
			{
				$skip = true;
				break;
			}
		}
		if (!$skip){?>
		<tr>
		<form action="" method="post">
		<input type="hidden" name="participate_in_campaign" value="true">
		<input type="hidden" name="client_id" value="<?php echo $campaign['clientid']; ?>">
		<input type="hidden" name="campaign_id" value="<?php echo $campaign['campaignid']; ?>">
		<input type="hidden" name="brand_id" value="<?php echo $campaign['brandid']; ?>">
		<input type="hidden" name="channel_id" value="<?php echo $campaign['channelid']; ?>">
		<td><input style="text-align: right" type="submit" value="Click to Participate"/></td><td><?php echo $campaign["companyname"]; ?></td><td><?php echo $campaign["brandname"]; ?></td><td><?php echo $campaign["campaignname"]; ?></td><td><?php echo $campaign["channelname"]; ?></td><td><?php echo $campaign["description"]; ?></td>
		</form>
		</tr>
	<?php
			$available_campaigns_count++;
		}
	}

?>
</table>

</br></br>
<b>Campaigns That You Have Participated In:</b>
</br>
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
	?>
		</br>
		<form action="" method="post">
		<table border=1>
		<tr>
		<?php if (!$_SESSION['subscription_id']){ ?><th></th><?php }?><th>Client</th><th>Brand</th><th>Campaign</th><th>Channel</th><th>Description</th>
		</tr>
		<?php
		$points = 2;
		//$array_of_rewards = array();
		foreach ($data_camp['results'] as &$campaigns)
		{?>
			<tr align="center">
			<?php if (!$_SESSION['subscription_id']){ ?><td><input style="text-align: right" type="submit" value="Click to Participate"/><?php }?>
			<input type="hidden" name="campaign" value="true">
			<input type="hidden" name="campaign_id" value="<?php echo $reward['campaignid']; ?>">
			<input type="hidden" name="brand_id" value="<?php echo $reward['brandid']; ?>">
			<input type="hidden" name="channel_id" value="1">
			</td>
			<td><?php echo $campaigns["companyname"]; ?></td><td><?php echo $campaigns["brandname"]; ?></td><td><?php echo $campaigns["campaignname"]; ?></td><td><?php echo $campaigns["channelname"]; ?></td><td><?php echo $campaigns["description"]; ?></td>
			</tr>
			
		<?php
			$points++;
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
<b>Rewards Available:</b>
</br>
<?php
$rewards_available_counter = 0;

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
}


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
$url = 'http://104.156.53.150/multichannel-api/reward/retrieve_redeemed.php';
$data = array('user_id' => $_SESSION['login_id']);

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
//print_r($data['results']);
	if($data['results'])
	{
	?>
		</br>
		<table border=1>
		<tr>
		<th>Reward</th><th>Client</th><th>Brand</th><th>Description</th><th>Points Spent</th><th>Date Redeemed</th>
		</tr>
		<?php
		foreach ($data['results'] as &$redeemed_reward)
		{?>
			<tr align="center">
			<td><?php echo $redeemed_reward["title"]; ?></td><td><?php echo $redeemed_reward["companyname"]; ?><td><?php echo $redeemed_reward["brandname"]; ?></td><td><?php echo $redeemed_reward["description"]; ?></td><td><?php echo $redeemed_reward["value"]; ?></td><td><?php echo $redeemed_reward["dateredeemed"]; ?></td>
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

    $client_query = "SELECT ClientId, CompanyName FROM clients";
	$client_res = $dbconn->query($client_query);
	while ($clients = $client_res->fetchRow(MDB2_FETCHMODE_ASSOC))
	{
		$clientlist[$clients['clientid']] .= $clients['companyname'];
	}
	
	$brand_query = "SELECT BrandId, BrandName FROM brands";
	$brand_res = $dbconn->query($brand_query);
	while ($brands = $brand_res->fetchRow(MDB2_FETCHMODE_ASSOC))
	{
		$brandlist[$brands['brandid']] .= $brands['brandname'];
	}
	
	$campaign_query = "SELECT CampaignId, CampaignName FROM campaigns";
	$campaign_res = $dbconn->query($campaign_query);
	while ($campaigns = $campaign_res->fetchRow(MDB2_FETCHMODE_ASSOC))
	{
		$campaignlist[$campaigns['campaignid']] .= $campaigns['campaignname'];
	}
	
	$channel_query = "SELECT ChannelId, ChannelName FROM channels";
	$channel_res = $dbconn->query($channel_query);
	while ($channels = $channel_res->fetchRow(MDB2_FETCHMODE_ASSOC))
	{
		$channellist[$channels['channelid']] .= $channels['channelname'];
	}
	
	// echo '<pre>';
	// print_r($clientlist);
	// print_r($brandlist);
	// print_r($campaignlist);
	// print_r($channellist);
	// exit();

	

	foreach ($array_of_rewards as &$reward)
	{
		
		$pre = "select * from customer_subscriptions where BrandId = " . $reward[0] . " and CampaignId = " . $reward[1] . " and ChannelId = " . $reward[2] . " AND CustomerId = " . $_SESSION['login_id'] . " AND status = 'ACTIVE' LIMIT 1";
		//echo $pre . "<br>";
		// echo '<pre>';
		// print_r($pre);
		// exit();
		$pre_res = $dbconn->query($pre);
		$pre_row = $pre_res->fetchRow(MDB2_FETCHMODE_ASSOC);
		
	
		$query = "SELECT * FROM points where BrandId = " . $reward[0] . " and CampaignId = " . $reward[1] . " and ChannelId = " . $reward[2] . " AND status = 'ACTIVE'";
		$res = $dbconn->query($query);
		
		$i = 0;
		while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			?>
			<tr  align="center">
			
			<form action="" method="post">
			<td><?php //if ($row['pointaction'] != "Likes" && 0 != strcasecmp($row['pointaction'], "Like")) {
			       if (true) {
				?><input type="Submit" value="Participate"/><?php }else {?><div class="fb-like"data-layout="button" data-action="like" data-show-faces="false" data-share="false"><?php }?></div>
			<input type="hidden" name="action" value="true">
			<input type="hidden" name="brand_id" value="<?php 
			echo $row['brandid'];
			
			?>">
			<input type="hidden" name="client_id" value="<?php echo $row['clientid']; ?>">
			<input type="hidden" name="channel_id" value="<?php echo $row['channelid']; ?>">
			<input type="hidden" name="campaign_id" value="<?php echo $row['campaignid']; ?>">
			<input type="hidden" name="points_id" value="<?php echo $row['pointsid']; ?>">
			<input type="hidden" name="points" value="<?php echo $row['value']; ?>">
			<input type="hidden" name="pointofaction" value="<?php echo $row['pointaction']; ?>">
			<input type="hidden" name="subid" value="<?php echo $pre_row["subscriptionid"]?>"/>
			</form>
			</td>
			<td><?php echo $clientlist[$row['clientid']]; ?></td>
			<td><?php echo $brandlist[$row['brandid']]; ?></td>
			<td><?php echo $campaignlist[$row['campaignid']]; ?></td>
			<td><?php echo $channellist[$row['channelid']]; ?></td>
			<td><?php echo $row['pointaction']; ?></td>
			<td><?php echo "+" . $row['value']; ?></td>
			<td><?php echo $row['pointslimit']; ?></td>
			<td><?php if ($row['pointcapping'] == "DAILY") echo "N/A"; else echo $row['pointslimit']; ?></td>
			</tr>
			<?php
			$i++;
		}
	}
?>
</table>


	</br></br></br></br>
	<b>>>>>> COUPONS AND RAFFLES DEMO <<<<<< </b></br></br>
	<b>Pending Coupons:</b>	</br>
<?php
	$url = 'http://104.156.53.150/multichannel-api/coupon/retrieve.php';
	/*$data = array('client_id' => '1',
	              'brand_id' => '1',
				  'channel_id' => '1',
				  'campaign_id' => '2',
				  'status' => 'PENDING');*/
	$data = array('status' => 'PENDING');

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

<span  id="gencoupons"/>
</br></br>
	<b>Generated Coupons:</b>	</br>

<?php
	$url = 'http://104.156.53.150/multichannel-api/coupon/retrieve_generated.php';
	/*$data = array('client_id' => '1',
	              'brand_id' => '1',
				  'channel_id' => '1',
				  'campaign_id' => '2');*/
	$data = array();

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
		<th></th><th>Generated Coupon Id</th><th>Coupon Id</th><th>Code</th><th>QR Code</th>
		</tr>
		<?php
		$coupAL_arr = array();
		foreach ($data['results'] as &$coup)
		{
			$coupAL_arr[] = $coup["couponid"];
			?>
			<tr align="center">
			<td>
			<form id="generatedcoupons" action="#redeemedcoupons" method="post">
				<input type="hidden" name="claim_coupon" value="true">
				<input type="hidden" name="theclientid" value="<?php echo $coup["clientid"]; ?>">
				<input type="hidden" name="thebrandid" value="<?php echo $coup["brandid"]; ?>">
				<input type="hidden" name="thecampaignid" value="<?php echo $coup["campaignid"]; ?>">
				<input type="hidden" name="thechannelid" value="<?php echo $coup["channelid"]; ?>">
				<input type="hidden" name="thecouponid" value="<?php echo $coup["couponid"]; ?>">
				<input type="hidden" name="thegeneratedcouponid" value="<?php echo $coup["generatedcouponid"]; ?>">
				<input type="submit" name="submit" value="Claim AS: "/>
				<select name="thecustid">
				     <?php $query="SELECT Email,CustomerId FROM customers ORDER BY 2 ASC;"; 
					 $res = $dbconn->query($query);
					while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
					{
					 ?>
						<option value="<?php echo $row["customerid"]; ?>"><?php echo $row["email"]; ?></option>
					<?php } ?>
				</select>
				<select name="the_rdm_option">
				<?php 
				foreach ($coup["redeem_options"] as &$redeem_option)
				{?>
					<option value="<?php echo $redeem_option["couponmappingid"]; ?>"><?php echo $redeem_option["brandname"] . ", " . $redeem_option["campaignname"] . ", " . $redeem_option["channelname"]; ?></option>
				<?php } ?>
				</select>
			</form>
			</td>
			<td><?php echo $coup["generatedcouponid"]; ?></td><td><?php echo $coup["couponid"]; ?></td><td><?php echo $coup["code"]; ?></td>
			<td>
			<?php 
				$url2 = 'http://104.156.53.150/multichannel-api/coupon/retrieve_qr.php';
				$data2 = array('generated_coupon_id' => $coup["generatedcouponid"]);

				//print_r($data2);
				$options2 = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data2),
					),
				);
				$context2  = stream_context_create($options2);
				$result2 = file_get_contents($url2, false, $context2);

				echo "<img src='http://104.156.53.150/multichannel-api/coupon/qr_codes/coup" . $coup["generatedcouponid"] . ".png' />"; 
			?>
			</td>
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
$url = 'http://104.156.53.150/multichannel-api/coupon/retrieve_redeemed.php';
//$data = array('customer_id' => $_SESSION['login_id']);
	/*$data = array('client_id' => '1',
	              'brand_id' => '1',
				  'channel_id' => '1',
				  'campaign_id' => '2');*/
	$data = array();

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
//print_r($data['results']);
	if($data['results'])
	{
	?>
		</br>
		
		<table border=1>
		<tr>
		<th>Redeemed By</th><th>Coupon Id</th><th>Code</th><th>Brand Name</th><th>Campaign Name</th><th>Channel Name</th><th>Date Redeemed</th>
		</tr>
		<?php
		foreach ($data['results'] as &$redeemed_coupon)
		{?>
			<tr align="center">
			<td><?php echo $redeemed_coupon["email"]; ?></td><td><?php echo $redeemed_coupon["couponid"]; ?></td><td><?php echo $redeemed_coupon["code"]; ?></td>
			<!--<td>
			<?php 
				/*$url2 = 'http://104.156.53.150/multichannel-api/coupon/retrieve_qr';
				$data2 = array('generated_coupon_id' => $redeemed_coupon["generatedcouponid"]);

				//print_r($data2);
				$options2 = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data2),
					),
				);
				$context2  = stream_context_create($options2);
				$result2 = file_get_contents($url2, false, $context2);

				echo "<img src='http://104.156.53.150/multichannel-api/coupon/qr_codes/coup" . $redeemed_coupon["generatedcouponid"] . ".png' />"; 
				*/
			?>
			</td>-->
			<td><?php echo $redeemed_coupon["brandname"]; ?></td>
			<td><?php echo $redeemed_coupon["campaignname"]; ?></td>
			<td><?php echo $redeemed_coupon["channelname"]; ?></td>
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
	$data = array('status' => 'PENDING');

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
	$data = array('status' => 'ACTIVE');

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
