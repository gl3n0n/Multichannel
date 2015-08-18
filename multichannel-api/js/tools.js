$(function() {
    var DOMAIN = 'http://104.156.53.150';
    var params = {}

    params["/multichannel-api/points/inquire.php"] = ["client_id","subscription_id", "customer_id", "brand_id", "campaign_id", "channel_id"];
    params["/multichannel-api/points/update.php"] = ["subscription_id", "customer_id", "brand_id", "campaign_id", "channel_id", "points", "points_id"];
	params["/multichannel-api/reports/generate_report2.php"] = ["client_id", "brand_id", "campaign_id","customer_id","date_from","date_to"];
	params["/multichannel-api/customer/insert.php"] = ["client_id","first_name", "middle_name", "last_name","gender","contact_number","address","email","fb_id","twitter_handle","birthdate"];
	params["/multichannel-api/customer/update.php"] = ["customer_id","client_id","first_name", "middle_name", "last_name","gender","contact_number","address","email","status","fb_id","twitter_handle","birthdate"];
	
	params["/multichannel-api/coupon/redeem.php"] = ["generated_coupon_id", "coupon_id", "customer_id","coupon_mapping_id","use_points"];
	params["/multichannel-api/coupon/convert_to_points.php"] = ["generated_coupon_id", "coupon_id", "customer_id","coupon_mapping_id"];
   /* params["/multichannel-api/customer/retrieve.php"] = ["customer_id", "client_id", "brand_id", "campaign_id", "channel_id"];
	params["/multichannel-api/customer/retrieve_subscriptions.php"] = ["customer_id", "brand_id", "campaign_id", "channel_id"];
    params["/multichannel-api/reward/retrieve.php"] = ["reward_id", "client_id", "brand_id", "campaign_id", "channel_id"];
    params["/multichannel-api/reward/update.php"] = ["reward_id", "client_id", "brand_id", "campaign_id", "channel_id", "updated_by", "date_from" ,"date_to", "title", "description", "image", "quantity"];
    params["/multichannel-api/reward/redeem.php"] = ["reward_id", "user_id", "client_id", "brand_id", "campaign_id", "channel_id", "source", "action" ,"date_redeemed"];
	params["/multichannel-api/coupon/generate.php"] = ["coupon_id"]; */

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
            html += "<td><input type='text' value='' style='width:95%;' /></td>";
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
        }
        else
        {
            $('#tdParams').html('&nbsp;');
            $('#txtResponse').html('&nbsp');
            $('#txtURL').val("");
        }
    });

    $('#cmbPost').click(function (e) {
        if ($('#txtURL').val() != "")
        {
            var params = getData();
            $.post( $('#txtURL').val(), params)
              .done(function( resp ) {
                  $('#txtResponse').html(resp);
            });
        }

    });
});


