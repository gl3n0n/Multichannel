<?php
		
	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['generate_coupon'])
	{
		$url = 'http://104.156.53.150/multichannel-api/coupon/insert';
		$data = array('client_id' => '1',
					  'brand_id' => '1',
					  'channel_id' => '1',
					  'campaign_id' => '1',
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

		if ($data['result_code'] == 200)
		{
			echo "Successfully Added Coupon!<br>";
		}
		else
		{
			echo "Error Adding Coupon!<br>";
		}
	}

	if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['claim_coupon'])
	{
		$url = 'http://104.156.53.150/multichannel-api/coupon/redeem';
		$data = array('client_id' => $_POST['theclientid'],
					  'brand_id' => $_POST['thebrandid'],
					  'channel_id' => $_POST['thechannelid'],
					  'campaign_id' => $_POST['thecampaignid'],
					  'coupon_id' => $_POST['thecouponid'],
					  'customer_id' => '1');

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
	}
?>

<?php
	$url = 'http://104.156.53.150/multichannel-api/coupon/retrieve';
	$data = array('client_id' => '1',
	              'brand_id' => '1',
				  'channel_id' => '1',
				  'campaign_id' => '1');

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
		Coupons:
		<table border=1>
		<tr>
		<th></th><th>Coupon Id</th><th>Code</th><th>Quantity</th><th>Limit Per User</th><th>QR-code</th>
		</tr>
		<?php
		foreach ($data['results'] as &$coup)
		{?>
			<tr align="center">
			<td>
			<form action="" method="post">
				<input type="hidden" name="claim_coupon" value="true">
				<input type="hidden" name="theclientid" value="<?php echo $coup["clientid"]; ?>">
				<input type="hidden" name="thebrandid" value="<?php echo $coup["brandid"]; ?>">
				<input type="hidden" name="thecampaignid" value="<?php echo $coup["campaignid"]; ?>">
				<input type="hidden" name="thechannelid" value="<?php echo $coup["channelid"]; ?>">
				<input type="hidden" name="thecouponid" value="<?php echo $coup["couponid"]; ?>">
				<input type="submit" name="submit" value="Claim"/>
			</form>
			</td>
			<td><?php echo $coup["couponid"]; ?></td><td><?php echo $coup["code"]; ?></td><td><?php echo $coup["quantity"]; ?></td><td><?php echo $coup["limitperuser"]; ?></td>
			<td>
			<?php 
				$url2 = 'http://104.156.53.150/multichannel-api/coupon/retrieve_qr';
				$data2 = array('client_id' => $coup["clientid"],
							  'brand_id' => $coup["brandid"],
							  'channel_id' => $coup["channelid"],
							  'campaign_id' => $coup["campaignid"],
							  'coupon_id' => $coup["couponid"]);

				$options2 = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data2),
					),
				);
				$context2  = stream_context_create($options2);
				$result2 = file_get_contents($url2, false, $context2);

				echo "<img src='http://104.156.53.150/multichannel-api/coupon/qr_codes/" . $coup["couponid"] . ".png' />"; 
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
		echo "</br>You haven't redeemed any rewards.</br></br>";
	}
	?>

	<br><br>Generate Coupons:
	<table>
	<form action="" method="post">
		<input type="hidden" name="generate_coupon" value="true">
		<tr><td>Code<td/><td><input type="text" name="thecode" value="<?php echo uniqid(); ?>"></td></tr>
		<tr><td>Quantity<td/><td><input type="text" name="thequantity" value="1"></td></tr>
		<tr><td>Limit Per User<td/><td><input type="text" name="thelimitperuser" value="1"></td></tr>
		<tr><td><td/><td><input type="submit" name="submit" value="Generate"/></td></tr>
	</form>
	<table/>
	<?php
?>

</br>
<b>Redeemed Coupons:</b>
</br>
<?php
$url = 'http://104.156.53.150/multichannel-api/coupon/retrieve_redeemed';
$data = array('customer_id' => '1');

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
		<th>Code</th><th>Brand</th><th>Date Redeemed</th>
		</tr>
		<?php
		foreach ($data['results'] as &$redeemed_coupon)
		{?>
			<tr align="center">
			<td><?php echo $redeemed_coupon["code"]; ?></td><td><?php echo $redeemed_coupon["brandname"]; ?></td><td><?php echo $redeemed_coupon["dateredeemed"]; ?></td>
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