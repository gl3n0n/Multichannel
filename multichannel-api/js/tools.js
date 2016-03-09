$(function() {

	var DOMAIN = 'http://104.156.53.150';
	var params = {}

	params["/multichannel-api/points/inquire.php"] = ["clientid","subscription_id", "customerid", "brandid", "campaignid", "channelid","apitoken"];
	params["/multichannel-api/points/update.php"] = ["subscription_id", "customerid", "clientid", "brandid", "campaignid", "channelid", "points", "points_id","apitoken"];
	params["/multichannel-api/reports/generate_report2.php"] = ["clientid", "brandid", "campaignid","customerid","date_from","date_to","apitoken"];
	params["/multichannel-api/customer/insert.php"] = ["clientid","firstname", "middlename", "lastname","gender","contactnumber","address","email","fbid","twitterhandle","birthdate","apitoken"];
	params["/multichannel-api/customer/update.php"] = ["customerid","clientid","firstname", "middlename", "lastname","gender","contactnumber","email","address","status","fbid","twitterhandle","birthdate","apitoken"];

	params["/multichannel-api/coupon/redeem.php"] = ["generated_coupon_id", "coupon_id", "customerid","coupon_mapping_id","use_points","apitoken"];
	params["/multichannel-api/coupon/convert_to_points.php"] = ["generated_coupon_id", "coupon_id", "customerid","coupon_mapping_id","apitoken"];
	/* params["/multichannel-api/customer/retrieve.php"] = ["customerid", "clientid", "brandid", "campaignid", "channelid"];
	params["/multichannel-api/customer/retrieve_subscriptions.php"] = ["customerid", "brandid", "campaignid", "channelid"];
	params["/multichannel-api/reward/retrieve.php"] = ["reward_id", "clientid", "brandid", "campaignid", "channelid"];
	params["/multichannel-api/reward/update.php"] = ["reward_id", "clientid", "brandid", "campaignid", "channelid", "updated_by", "date_from" ,"date_to", "title", "description", "image", "quantity"];
	params["/multichannel-api/reward/redeem.php"] = ["reward_id", "user_id", "clientid", "brandid", "campaignid", "channelid", "source", "action" ,"date_redeemed"];
	params["/multichannel-api/coupon/generate.php"] = ["coupon_id"]; */

	params["/multichannel-api/campaigns/list_campaign.php"]               = ["clientid", "customerid","apitoken"];
	params["/multichannel-api/campaigns/participate_a_campaign.php"]      = ["clientid", "customerid","points_id","brandid","campaignid","apitoken"];
	params["/multichannel-api/campaigns/list_customer_subscriptions.php"] = ["clientid", "customerid","apitoken"];

	
	params["/multichannel-api/points/list_action_points.php"]             = ["clientid", "customerid","apitoken"];
	params["/multichannel-api/points/gain_points.php"]                    = ["customerid", "clientid", "brandid", "campaignid", "channelid", "actiontypeid","apitoken"]; 
	//params["/multichannel-api/points/gain_points.php"]                    = ["customerid", "clientid", "actiontype_id"]; 
	params["/multichannel-api/points/list_customer_points.php"]           = ["clientid", "customerid","apitoken"];
	
	params["/multichannel-api/coupon/list_available_coupon.php"]          = ["clientid", "customerid", "couponid","apitoken"];
	params["/multichannel-api/coupon/list_redeemed_coupon.php"]           = ["clientid", "customerid", "couponid","apitoken"];
	params["/multichannel-api/coupon/do_redeemed_coupon.php"]             = ["clientid", "customerid", "couponid", "code","apitoken"];
	params["/multichannel-api/coupon/do_redeemed_coupon_by_exch_pts.php"] = ["clientid", "customerid", "couponid", "apitoken"];
	
	
	params["/multichannel-api/reward/list_of_redeemable_rewards.php"]     = ["clientid", "customerid","apitoken"];
	params["/multichannel-api/reward/list_of_rewards_available.php"]      = ["clientid", "customerid","apitoken"];
	params["/multichannel-api/reward/list_of_redeemed_rewards.php"]       = ["clientid", "customerid","apitoken"];
	params["/multichannel-api/reward/do_redeemed_reward.php"]             = ["clientid", "customerid","rewardconfigid","apitoken"];
	
	params["/multichannel-api/points/update_points.php"]                  = ["customerid", "clientid", "brandid", "campaignid", "pointsid", "value", "action" , "actiontypeid","apitoken"]; 
	
	
	params["/multichannel-api/customer/list_customer_info.php"]           = ["customerid", "clientid", "email", "fbid", "twitterhandle", "apitoken"]; 
	
	
	$('#cmbAPIType').val(0);
	$('#tdParams').html('&nbsp');
	$('#tdResponse').html('&nbsp');

	function generateParams(key)
	{
		var html = "<table cellspacing='1' cellpadding='2' border='1' style='width:90%'>";
		for (var i = 0; i < params[key].length; i++)
		{
		    html += "<tr>";
		    html += "<td style='width:50%;'>" + params[key][i] + "</td>";
		    html += "<td><input type='text' value='' style='width:95%;' class='form-control'  /></td>";
		    html += "</tr>";
		}
		html += '</table>';
		return html;
	}

    function getData()
    {
        var data = {}
        $('#tdParams tr').each(function (idx, elem) {
            var key = '';
            var val = '';
            $('td', this).each (function (indx, el2) {
                if (indx == 0)
                    key = $(el2).html();
                else
                    val = $('input', el2).val();
            });

            data[key] = val;
        });

        return data;
    }

    $('#cmbAPIType').change(function (e) {
        if ($(this).val() != "")
        {
            $('#tdParams').html(generateParams($(this).val()));
            $('#txtURL').val(DOMAIN + $(this).val());
            //display the post params
            $('#tdResponse').html('&nbsp');
        }
        else
        {
            $('#tdParams').html('&nbsp;');
            $('#txtResponse').html('&nbsp');
            $('#txtURL').val("");
        }
    });

    $('#cmbPost').click(function (e) {
		$('#txtResponse').html('<p style="color:blue">Please wait while loading the data from API server .........</p>');
        if ($('#txtURL').val() != "")
        {
            var params = getData();
            $.post( $('#txtURL').val(), params)
              .done(function( resp ) {
                  $('#txtResponse').html('<p class="wbreak">'+resp+'</p>');
            });
        }
		else
		{
			$('#txtResponse').html('&nbsp');
		}

    });
});


