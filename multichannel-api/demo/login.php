<?php
	require_once('../config/database.php');
	require_once('../config/constants.php');
	session_start();

	if (isset($_SESSION['login_user']))
	{
		header("Location: portal.php");
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
				$_SESSION['client_id']=1;
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
			//if($row)
			//{
				//echo "test";
			//	header("location: home.php");
			//}
			//else
			//{
				header("location: home.php");
			//}
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
<script>
  //window.fbAsyncInit = function() {
  //  FB.init({
  //    appId      : '826716314073935',
  //    xfbml      : true,
  //    version    : 'v2.3'
  //  });
  //};
  //
  //(function(d, s, id){
  //   var js, fjs = d.getElementsByTagName(s)[0];
  //   if (d.getElementById(id)) {return;}
  //   js = d.createElement(s); js.id = id;
  //   js.src = "//connect.facebook.net/en_US/sdk.js";
  //   fjs.parentNode.insertBefore(js, fjs);
  // }(document, 'script', 'facebook-jssdk'));
   
   
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
</body>
</html>

<?php 
if (isset($error))
{
	echo "<script>errorLogin();</script>";
	unset($error);
}
?>