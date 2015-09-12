<?php
class CouponList
{
        public $conn;

        public function CouponList($conn) 
        {
            $this->conn       = $conn;
            $this->table_name = 'coupon';
        }

	
	public function list_of_available_coupon($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			$qrlink     = addslashes($pdata["qrlink"]);
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv          = array();
			$retv["coupon"]= array();
			$retv["status"]= 0;
			
			$query      = "
			SELECT  DISTINCT gen.GeneratedCouponId,gen.Code,
			        CONCAT('$qrlink',gen.GeneratedCouponId,'.png') as qr_code,
				CONCAT(cust.FirstName,' ' ,cust.LastName) as CustomerName,
				sub.ClientId ,
				clnt.CompanyName,
				sub.BrandId  ,
				brnd.BrandName,
				sub.CampaignId ,
				camp.CampaignName,
				gen.CouponId
			FROM 
				customer_subscriptions sub,
				coupon map,
				generated_coupons gen,
				customers  cust,
				campaigns  camp,
				brands     brnd,
				clients    clnt
			WHERE   1=1
				AND sub.ClientId   = '$client_id'
				AND sub.CustomerId = '$customer_id'
				AND sub.PointsId   = map.PointsId
				AND sub.ClientId   = map.ClientId
				AND sub.Status     = 'ACTIVE'
				AND gen.Status     = 'PENDING'
				AND sub.PointsId   = gen.PointsId
				AND map.CouponId   = gen.CouponId
				AND sub.CustomerId = cust.CustomerId
				AND sub.ClientId  = clnt.ClientId
				AND sub.BrandId   = brnd.BrandId
				AND sub.CampaignId= camp.CampaignId
				
			";



			//run
			$res = $this->conn->query($query);

			if (PEAR::isError($res)) {
				return $retv;
			}

			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
			{				
				$result_array["coupon"][] = $row;
				$counter++;
			}
			$result_array["totalrows"] = $counter;
			$result_array["status"]    = (($counter>0)?(1):(0));
			//give it back
			return ($counter == 0) ? (false) : ($result_array);
	}

 


	public function list_of_redeemed_coupon($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			$qrlink     = addslashes($pdata["qrlink"]);
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv          = array();
			$retv["coupon"]= array();
			$retv["status"]= 0;
			$query      = "
			SELECT  DISTINCT gen.GeneratedCouponId,gen.Code,
			        CONCAT('$qrlink',gen.GeneratedCouponId,'.png') as qr_code,
				CONCAT(cust.FirstName,' ' ,cust.LastName) as CustomerName,
				sub.ClientId ,
				clnt.CompanyName,
				sub.BrandId  ,
				brnd.BrandName,
				sub.CampaignId ,
				camp.CampaignName,
				gen.CouponId
			FROM 
				customer_subscriptions sub,
				coupon map,
				generated_coupons gen,
				customers  cust,
				campaigns  camp,
				brands     brnd,
				clients    clnt
			WHERE   1=1
				AND sub.ClientId   = '$client_id'
				AND sub.CustomerId = '$customer_id'
				AND sub.PointsId   = map.PointsId
				AND sub.ClientId   = map.ClientId
				AND sub.Status     = 'ACTIVE'
				AND gen.Status     = 'REDEEMED'
				AND sub.PointsId   = gen.PointsId
				AND map.CouponId   = gen.CouponId
				AND sub.CustomerId = cust.CustomerId
				AND sub.ClientId  = clnt.ClientId
				AND sub.BrandId   = brnd.BrandId
				AND sub.CampaignId= camp.CampaignId
				
			";



			//run
			$res = $this->conn->query($query);

			if (PEAR::isError($res)) {
				return $retv;
			}

			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
			{				
				$result_array["coupon"][] = $row;
				$counter++;
			}
			$result_array["totalrows"] = $counter;
			$result_array["status"]    = (($counter>0)?(1):(0));
			//give it back
			return ($counter == 0) ? ($query) : ($result_array);
	}



	public function do_redeemed_a_coupon($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			$coupon_id  = addslashes($pdata["coupon_id"]  );
			$code       = addslashes($pdata["code"]       );
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv          = array();
			$retv["result"]= array();
			$retv["status"]= 0;
			
			$query      = "
			SELECT  gen.GeneratedCouponId,gen.Code,
			        CONCAT('$qrlink',gen.GeneratedCouponId,'.png') as qr_code,
				CONCAT(cust.FirstName,' ' ,cust.LastName) as CustomerName,
				cust.CustomerId,
				cust.Status as Customer_Status,
				sub.ClientId ,
				clnt.Status as Client_Status,
				sub.BrandId  ,
				brnd.BrandName,
				brnd.Status as Brand_Status,
				sub.CampaignId ,
				camp.CampaignName,
				camp.Status as Campaign_Status,
				chan.ChannelId ,
				chan.ChannelName,
				chan.Status as Channel_Status,
				gen.CouponId,
				(curdate() <= map.ExpiryDate ) as coupon_not_expired,
				gen.Status as Generation_status,
				map.CouponType,
				map.Status as Coupon_status,
				IFNULL(map.PointsValue,0) as PointsValue,
				map.CouponId,
				date_format(chan.DurationFrom,'%Y%m%d') as Channel_DurationFrom,
				date_format(chan.DurationTo,  '%Y%m%d') as Channel_DurationTo,
				date_format(camp.DurationFrom,'%Y%m%d') as Campaign_DurationFrom,
				date_format(camp.DurationTo,  '%Y%m%d') as Campaign_DurationTo,
				date_format(brnd.DurationFrom,'%Y%m%d') as Brand_DurationFrom,
				date_format(brnd.DurationTo,  '%Y%m%d') as Brand_DurationTo,
				typ.ActiontypeId,
				sub.SubscriptionId,
				sub.PointsId
			FROM 
				customer_subscriptions sub,
				coupon map,
				generated_coupons gen,
				points_mapping pmap,
				action_type typ,
				customers  cust,
				channels   chan,
				campaigns  camp,
				brands     brnd,
				clients    clnt
			WHERE   1=1
				AND sub.ClientId   = '$client_id'
				AND sub.CustomerId = '$customer_id'
				AND sub.PointsId   = map.PointsId
				AND sub.ClientId   = map.ClientId
				AND sub.Status     = 'ACTIVE'
				AND gen.Status     = 'PENDING'
				AND sub.PointsId   = gen.PointsId
				AND map.CouponId   = gen.CouponId
				AND gen.CouponId   = '$coupon_id'
				AND gen.Code       = '$code'
				AND sub.CustomerId = cust.CustomerId
				AND sub.ClientId   = clnt.ClientId
				AND sub.BrandId    = brnd.BrandId
				AND sub.CampaignId = camp.CampaignId
				AND sub.PointsId   = pmap.PointsId
				AND sub.ClientId   = pmap.ClientId
				AND sub.BrandId    = pmap.BrandId
				AND sub.CampaignId = pmap.CampaignId
				AND chan.ChannelId = pmap.ChannelId
				AND sub.PointsId   = typ.PointsId
				AND sub.ClientId   = typ.ClientId
			LIMIT 1
			";
			//run
			$res = $this->conn->query($query);
			//run
			$res = $this->conn->query($query);
			if (PEAR::isError($res)) {
				$retv['result_code'] = 401;
				$retv['error_txt']   = 'Record Not Found (Not currently Subscribed)';
				//give it back
				return $retv;
			}

			//get all
			$row     = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$counter = (($row != null) ? (1) : (0));
			
			//set results
			$retv["result"]    = $row;
			$retv["status"]    = $counter;

			/*
			tables affected:
			    coupon (lookup)
			    customer_subscriptions (lookup)
			    generated_coupon (update, status=REDEEMED, CustomerId=customerid WHERE PointsId=? and CouponId=?)
			    customer_points (insert/update, if coupon.CouponType=CONVERT_TO_POINTS, Balance=Balance + coupon.PointsValue, Total = Total + coupon.PointsValue)
			    points_log (insert/update, LogType="COUPON_TO_POINTS", Value=coupon.PointsValue)
			
			expected output
			200        OK
			401        Record Not Found (not currently subscribed)
			402        Coupon already expired. (out of range, coupon.ExpiryDate >= curdate or coupon.Status != ACTIVE)
			403        Inactive Channel (out of range, channel.DurationFrom and channel.DurationTo, or channel.Status != ACTIVE)
			405        Inactive Campaign (out of range, campaign.DurationFrom and campaign.DurationTo, or campaign.Status != ACTIVE)
			406        Inactive Brands (out of range, brands.DurationFrom and brands.DurationTo, or brands.Status != ACTIVE)
			407        Inactive Client (client.Status != ACTIVE)
			408        Inactive Customer (customers.Status != ACTIVE)

			*/			
			
			//401        Record Not Found (not currently subscribed)
			if ($counter <= 0) {
				$retv['result_code'] = 401;
				$retv['error_txt']   = 'Record Not Found (Not currently Subscribed)';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}
			
			$dit = @date('Ymd');
			//402        Coupon already expired. (out of range, coupon.ExpiryDate >= curdate or coupon.Status != ACTIVE)
			if ( $row["coupon_not_expired"] <= 0 or 
			     $row["coupon_status"]  != 'ACTIVE'
			    ) 
			{
				$retv['result_code'] = 402;
				$retv['error_txt']   = 'Coupon already expired.';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}
			
			//403        Inactive Channel (out of range, channel.DurationFrom and channel.DurationTo, or channel.Status != ACTIVE)
			if ( $row["channel_status"]  != 'ACTIVE' or
			     ( ! ( $dit >= $row["channel_durationfrom"] and $dit <= $row["channel_durationto"]  ) )			     
			   ) 
			{
				$retv['result_code'] = 403;
				$retv['error_txt']   = 'Inactive Channel.';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}
			
			//405        Inactive Campaign (out of range, campaign.DurationFrom and campaign.DurationTo, or campaign.Status != ACTIVE)
			if ( $row["campaign_status"]  != 'ACTIVE' or
			     ( ! ( $dit >= $row["campaign_durationfrom"] and $dit <= $row["campaign_durationto"]  ) )			     
			   ) 
			{
				$retv['result_code'] = 405;
				$retv['error_txt']   = 'Inactive Campaign.';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}

			//406        Inactive Brands (out of range, brands.DurationFrom and brands.DurationTo, or brands.Status != ACTIVE)
			if ( $row["brand_status"]  != 'ACTIVE' or
			     ( ! ( $dit >= $row["brand_durationfrom"] and $dit <= $row["brand_durationto"]  ) )			     
			   ) 
			{
				$retv['result_code'] = 406;
				$retv['error_txt']   = 'Inactive Brands.';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}

			//407        Inactive Client (client.Status != ACTIVE)
			if ( $row["client_status"]  != 'ACTIVE' )
			{
				$retv['result_code'] = 407;
				$retv['error_txt']   = 'Inactive Client.';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}
			//408        Inactive Customer (customers.Status != ACTIVE)
			if ( $row["customer_status"]  != 'ACTIVE' )			   
			{
				$retv['result_code'] = 408;
				$retv['error_txt']   = 'Inactive Customer.';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}
			
			//good
			//generated_coupon (update, status=REDEEMED, CustomerId=customerid WHERE PointsId=? and CouponId=?)
			$pdata["generated_coupon_id"] = $row["generatedcouponid"];
			$pdata["value"]               = $row["pointsvalue"];
			$pdata["actiontype_id"]       = $row["actiontypeid"];
			$pdata["subscription_id"]     = $row["subscriptionid"];
			$pdata["points_id"]           = $row["pointsid"];
			$pdata["channel_id"]          = $row["channelid"];
			$pdata["brand_id"]            = $row["brandid"];
			$pdata["campaign_id"]         = $row["campaignid"];
			
			
			//set flag
			$rdata = array();
			$rdata = $this->update_redeemed_flag($pdata);
			
			//customer_points (insert/update, if coupon.CouponType=CONVERT_TO_POINTS, Balance=Balance + coupon.PointsValue, Total = Total + coupon.PointsValue)
			//points_log (insert/update, LogType="COUPON_TO_POINTS", Value=coupon.PointsValue)
			$sdata = array();
			$tdata = array();
			if($row["coupontype"] == 'CONVERT_TO_POINTS')
			{
			    
			    //points-log
			    $tdata = $this->save_points_log($pdata);
			    
			    //customer-points
			    $sdata = $this->save_customer_points($pdata);
			}

			//nice
			$retv["status"]      = 1;
			$retv['result_code'] = 200;
			$retv['error_txt']   = 'Success';
			$retv["result"]      = array();
			
			//just in case			
			if('show' == 'more')
				$retv['results']     = array('points_log'     => $tdata, 
							    'customer_points' => $sdata,
							    'redeemed_flag'   => $rdata);			
			//give it back
			return $retv;

	}


	public function save_points_log($pdata=null)
	{

			//fmt
			$client_id       = addslashes($pdata["client_id"]  );
			$customer_id     = addslashes($pdata["customer_id"]);
			$channel_id      = addslashes($pdata["channel_id"]  );
			$brand_id        = addslashes($pdata["brand_id"]   );
			$campaign_id     = addslashes($pdata["campaign_id"]);
			$actiontype_id   = addslashes($pdata["actiontype_id"] );
			$points_id       = addslashes($pdata["points_id"]  );
			$subscription_id = addslashes($pdata["subscription_id"]  );
			$created_by      = addslashes($pdata["created_by"]  );
			$value           = addslashes($pdata["value"]  );
			
			$retv   = array();
			$retv['save_points_log'] = 0;
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			INSERT INTO points_log (
				CustomerId     ,
				SubscriptionId ,
				ClientId       ,
				BrandId        ,
				CampaignId     ,
				ChannelId      ,
				PointsId       ,
				ActiontypeId   ,
				LogType        ,
				Value          ,
				CreatedBy      ,
				DateCreated 
			)
			VALUES (
				'$customer_id',     
				'$subscription_id', 
				'$client_id',       
				'$brand_id',        
				'$campaign_id',     
				'$channel_id',      
				'$points_id',       
				'$actiontype_id',   
				'COUPON_TO_POINTS',        
				'$value',          
				'$created_by',      
				Now()    
			)
			";

			//run
			$row = $this->conn->exec($query);
			//get it
			$retv['PointLogId']      = $this->conn->lastInsertId('points_log', 'PointLogId');
			$retv['save_points_log'] = $row;

			//give it back
			return $retv;
	}


	public function update_redeemed_flag($pdata=null)
	{

			//fmt
			$generated_coupon_id = addslashes($pdata["generated_coupon_id"]  );
			$customer_id         = addslashes($pdata["customer_id"]);
			$created_by          = addslashes($pdata["created_by"]);
			
			$retv   = array();
			$retv['update_redeemed_flag'] = 0;
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			UPDATE generated_coupons
			SET
					CustomerId   = '$customer_id',
					Status       = 'REDEEMED',
					UpdatedBy    = '$created_by',
					DateRedeemed = Now(),
					DateUpdated  = Now()
			WHERE 
				GeneratedCouponId    = '$generated_coupon_id'
			";

			//run
			$row = $this->conn->exec($query);
			//get it
			$retv['update_redeemed_flag'] = $row;

			//give it back
			return $retv;
	}


	public function save_customer_points($pdata=null)
	{

			//fmt
			$client_id       = addslashes($pdata["client_id"]  );
			$customer_id     = addslashes($pdata["customer_id"]);
			$channel_id      = addslashes($pdata["channel_id"]  );
			$brand_id        = addslashes($pdata["brand_id"]   );
			$campaign_id     = addslashes($pdata["campaign_id"]);
			$actiontype_id   = addslashes($pdata["actiontype_id"] );
			$points_id       = addslashes($pdata["points_id"]  );
			$subscription_id = addslashes($pdata["subscription_id"]  );
			$created_by      = addslashes($pdata["created_by"]  );
			$value           = addslashes($pdata["value"]  );
			
			
			$data       = array();
			$retv       = array();
			$query      = "
				SELECT  CustomerPointId
				FROM
					customer_points
				WHERE
				1=1
				AND SubscriptionId= '$subscription_id'
			";


			//run
			$res = $this->conn->query($query);
			if (PEAR::isError($res)) {
				//new
				$subid   = 0;
			}
		
			//get it
			$row     = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$subid   = $row["customerpointid"];
			$data['save_customer_points'] = 0 ;
			
			
			if($subid <= 0)
			{
				//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
				$query      = "
				INSERT INTO customer_points (
					SubscriptionId ,
					Balance        ,
					Total          ,
					CreatedBy      ,
					DateCreated 
				)
				VALUES (
					'$subscription_id', 
					'$value',          
					'$value',          
					'$created_by',      
					Now()    
				)
				";
				
				//run
				$res = $this->conn->exec($query);
				//get it
				$data['CustomerPointId']      = $this->conn->lastInsertId('customer_points', 'CustomerPointId');
				$data['save_customer_points'] = $res;
			}
			else
			{
				$data['CustomerPointId'] = $subid;
				$query      = "
					UPDATE 
						customer_points 
					SET
						Balance        = (Balance + '$value'),
						Total          = (Total   + '$value'),
						UpdatedBy      = '$created_by',
						DateUpdated    = Now()
					WHERE
						SubscriptionId = '$subscription_id'
				";
				//run
				$res = $this->conn->exec($query);
				$data['save_customer_points'] = $res;
			}

			//give it back
			return $data;
	}


	

}
?>