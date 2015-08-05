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
</head>
<body>
    <form action="" method="post">
	<table>
	<tr>
	<td><p> Welcome, <?php echo strtoupper($_SESSION['login_user']); ?>! </p></td>
	<td><input type="hidden" name="logout" value="true"><input style="text-align: right" type="submit" value="Logout"/></td>
	</tr>
	</table>
	</form>
</br>
<b>Campaigns To Participate In:</b>
</br>
<?php

//foreach($campaign_ids_mapped_brand_ids as $key=>$value) {
	$url = 'http://104.156.53.150/multichannel-api/campaigns/retrieve.php';
	$data = array('campaign_id' => '2', 'brand_id' => '1');

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
		</br>
		<form action="" method="post">
		<table border=1>
		<tr>
		<th></th><th>Brand</th><th>Campaign</th><th>Description</th>
		</tr>
		<?php
		$points = 2;
		foreach ($data['results'] as &$campaigns)
		{?>
			<tr align="center">
			<td><input style="text-align: right" type="submit" value="Click to Participate"/>
			<input type="hidden" name="campaign" value="true">
			<input type="hidden" name="points_to_add" value="<?php echo $points; ?>">
			<input type="hidden" name="campaign_id" value="<?php echo $reward['campaignid']; ?>">
			<input type="hidden" name="brand_id" value="<?php echo $reward['brandid']; ?>">
			<input type="hidden" name="channel_id" value="1">
			</td>
			<td><?php echo $campaigns["brandname"]; ?></td><td><?php echo $campaigns["campaignname"]; ?></td><td><?php echo $campaigns["description"]; ?></td>
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
//}

?>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['participate'])
{
	$url = 'http://104.156.53.150/multichannel-api/customer/insert.php';
	$data = array('first_name' => $_POST['fname_txt'], 'middle_name' => $_POST['mname_txt'],
	              'last_name' => $_POST['lname_txt'], 'gender' => $_POST['gender_txt'],
				  'contact_number' => $_POST['contact_txt'], 'address' => $_POST['address_txt'],
				  'email' => $_POST['email_txt'],'status' => 'ACTIVE',
				  'fb_id' => $_POST['fbid_txt'], 'twitter_handle' => $_POST['twitterhandle_txt'],);

	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	
	$json = json_decode($result);
	//var_dump($data);
//	if ($json->{"result_code"} == 200)
//	{
		$_SESSION['login_user']=$row["fname_txt"] . " " . $row["lname_txt"];
		header("Location: home");
//	}
/*
	else if ($json->{"result_code"} == 403)
	{
		$notice = "Notice: Email already taken.";
	}
	else
	{
		$notice = "Notice: Error Occurred";
	}
*/	
	//$_SESSION['subscription_id'] = $json->{"subscriptionid"};

}
?>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['campaign'])
{
echo "</br>Fill up form below to complete participation. </br>";
echo "(<font color=\"red\"><b>*</b></font>) Required Fields </br></br>";
?>
	<form action="" method="post">
	<input type="hidden" name="participate" value="true">
	FIRST NAME<font color="red"><b>*</b></font>:<input type="text" name="fname_txt" value=""></br>
	MIDDLE NAME<font color="red"><b>*</b></font>:<input type="text" name="mname_txt" value=""></br>
	LAST NAME<font color="red"><b>*</b></font>:<input type="text" name="lname_txt" value=""></br>
	GENDER<font color="red"><b>*</b></font>:<input type="text" name="gender_txt" value=""></br>
	CONTACT NO.<font color="red"><b>*</b></font>:<input type="text" name="contact_txt" value=""></br>
	ADDRESS<font color="red"><b>*</b></font>:<input type="text" name="address_txt" value=""></br>
	EMAIL<font color="red"><b>*</b></font>:<input type="text" name="email_txt" value=""></br>
	FB ID:<input type="text" name="fbid_txt" value=""></br>
	TWITTER HANDLER<b>*</b></font><input type="text" name="twitterhandler_txt" value=""></br>
	<input style="text-align: right" type="submit" value="Submit and Participate"/>
	<input type="hidden" name="campaign_id" value="<?php echo $_POST['campaign_id']; ?>">
	<input type="hidden" name="brand_id" value="<?php echo $_POST['brand_id']; ?>">
	<input type="hidden" name="channel_id" value="<?php echo $_POST['channel_id']; ?>">
	</form>
	
<?php
}
?>
<?php
echo $notice;
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
