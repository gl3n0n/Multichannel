<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	session_start();

	if (isset($_SESSION['login_user']))
	{
		//header("Location: portal.php");
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
	
		$query_keys = array();
	
		if (!empty($username))
			$query_keys[] = 'Email = '. $dbconn->quote($username, 'text');
		 
		$query_keys[] = "Status = 'ACTIVE'";
		
		if (sizeof($query_keys) == 0)
			$query_string = null;
		else
			$query_string = implode(' AND ', $query_keys);
	
		$res = $dbconn->extended->autoExecute("customers", null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);
		
		if (PEAR::isError($res)) {
				return false;
			}
	
		$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		if($password == "demo1234" && !sizeof($row) == 0)
		{
			session_register("username");
			$_SESSION['login_user']=$row["firstname"] . " " . $row["lastname"];
			$_SESSION['login_id']=$row["customerid"];

			if ($_SESSION['login_id'] == 1 )
			{
				$_SESSION['client_id']=46;
				$_SESSION['subscription_id'] = 1;
			}
			else
			{
				$_SESSION['client_id']=44;
				$_SESSION['subscription_id'] = 2;
			}
	
			$q = "SELECT * FROM points_log WHERE SubscriptionId = '" . $_SESSION['subscription_id']. "'";
			$res2 = $dbconn->query($q);
			//var_dump($res2);
			if (PEAR::isError($res2)) {
				return false;
			}
			$row = $res2->fetchRow(MDB2_FETCHMODE_ASSOC);
			if($row)
			{
				//echo "test";
				header("location: home.php");
			}
			else
			{
				header("location: portal.php");
			}
		}
		else 
		{
			$error="Your Email or Password is invalid";
		}
	}
?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Multichannel API Demo</title>
  <!--<link rel="stylesheet" href="css/style.css">-->
  <script>
	function errorLogin()
	{
		document.getElementById("msg").innerHTML = "Your Enail or Password is invalid.";
	}
  </script>
</head>
<body>
  <form method="post" action="" class="login">
    <p>
      <label for="login">Email:</label>
      <input type="text" name="username" id="username" value="">
    </p>

    <p>
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" value="">
    </p>
	
	<p class="forgot-password" id="msg"></p>
    <p class="login-submit">
      <button type="submit" class="login-button">Login</button>
    </p>
  </form>
  
<?php
echo "</br>Fill up form below to Register. </br>";
echo "(<font color=\"red\"><b>*</b></font>) Required Fields </br></br>";
?>
	<table>
	<form action="" method="post">
	<input type="hidden" name="participate" value="true">
	<tr><td>FIRST NAME<font color="red"><b>*</b></font> : </td><td><input type="text" name="fname_txt" value=""></td></tr>
	<tr><td>MIDDLE NAME<font color="red"><b>*</b></font> : </td><td><input type="text" name="mname_txt" value=""></td></tr>
	<tr><td>LAST NAME<font color="red"><b>*</b></font> : </td><td><input type="text" name="lname_txt" value=""></td></tr>
	<tr><td>GENDER<font color="red"><b>*</b></font> : </td><td><input type="text" name="gender_txt" value=""></td></tr>
	<tr><td>CONTACT NO.<font color="red"><b>*</b></font> : </td><td><input type="text" name="contact_txt" value=""></td></tr>
	<tr><td>ADDRESS<font color="red"><b>*</b></font> : </td><td><input type="text" name="address_txt" value=""></td></tr>
	<tr><td>EMAIL<font color="red"><b>*</b></font> : </td><td><input type="text" name="email_txt" value=""></td></tr>
	<tr><td>FB ID:<font color="red"><b>*</b></font> : </td><td><input type="text" name="fbid_txt" value=""></td></tr>
	<tr><td>TWITTER HANDLE<font color="red"><b>*</b></font> : </td><td><input type="text" name="twitterhandler_txt" value=""></td></tr>
	<tr><td><input style="text-align: right" type="submit" value="Register"/></td></tr>
	<input type="hidden" name="campaign_id" value="<?php echo $_POST['campaign_id']; ?>">
	<input type="hidden" name="brand_id" value="<?php echo $_POST['brand_id']; ?>">
	<input type="hidden" name="channel_id" value="<?php echo $_POST['channel_id']; ?>">
	</form>
	</table>
</body>
</html>

<?php 
if (isset($error))
{
	echo "<script>errorLogin();</script>";
	unset($error);
}
?>