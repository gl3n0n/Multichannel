<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="en">

    <link rel="stylesheet" type="text/css" href="" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/tools.js"></script>
    <title>API Console</title>
<style>
	.wbreak {
	    word-wrap: break-word;
	    -moz-hyphens:auto; 
	    -webkit-hyphens:auto; 
	    -o-hyphens:auto; 
	    word-wrap: break-word;
    	    overflow-wrap: break-word;
	   text-overflow: ellipsis;
	   width: 200px; 
	}
</style>
</head>

<body>

    <table cellspacing="0" cellpadding="5" border = "1" style="width:600px;">
        <tr>
            <td style="width:35%;">Choose API:</td>
            <td style="width:65%;">
                <select name="cmbAPIType" id="cmbAPIType" style="width:100%;">
                    <option value="">Select API</option>
                    <option value="/multichannel-api/points/inquire.php">Inquire Balance</option>
                    <option value="/multichannel-api/points/update.php">Add / Deduct Points</option>
					<option value="/multichannel-api/reports/generate_report2.php">Generate Report</option>
					<option value="/multichannel-api/customer/insert.php">Insert Customer</option>
					<option value="/multichannel-api/customer/update.php">Update Customer Details</option>
					<option value="/multichannel-api/coupon/redeem.php">Redeem Coupon (Set use_points to true, to claim via points)</option>
					<option value="/multichannel-api/coupon/convert_to_points.php">Convert Coupon To Points</option>
					
					<option value="/multichannel-api/campaigns/list_campaign.php">List of Available Campaign</option>
					<option value="/multichannel-api/campaigns/participate_a_campaign.php">Participate A Campaign</option>
					<option value="/multichannel-api/campaigns/list_customer_subscriptions.php">List of Participated Campaign</option>
					
					
<!--                    <option value="/multichannel-api/customer/retrieve.php">Retrieve Customer</option>
					<option value="/multichannel-api/customer/retrieve_subscriptions.php">Customer Subscriptions</option> 
                    <option value="/multichannel-api/reward/retrieve.php">View Reward</option>
                    <option value="/multichannel-api/reward/update.php">Update Reward</option>
                    <option value="/multichannel-api/reward/redeem.php">Redeem Reward</option>
					<option value="/multichannel-api/coupon/generate.php">Generate Coupons</option> -->
                </select>
            </td>
        </tr>

        <tr>
            <td>API URL:</td>
            <td><input type="text" id="txtURL" value="" readonly="true" style="width:100%;" /></td>
        </tr>

        <tr>
            <td>POST Params (Key / Value)</td>
            <td id="tdParams">
                &nbsp;
            </td>
        </tr>

        <tr>
            <td>Response:</td>
            <td id="txtResponse" class="wbreak">
               &nbsp;
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td><input type="button" value="POST API CALL" id="cmbPost" /></td>
        </tr>
    </table>

</body>
</html>

