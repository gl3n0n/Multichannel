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
			$coupon_id  = addslashes($pdata["coupon_id"]);
			$qrlink     = addslashes($pdata["qrlink"]);

			//try			
			$coupon_sql = '';
			if($coupon_id > 0)
			{
				$coupon_sql = " AND map.CouponId   = '$coupon_id' ";
			}
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv          = array();
			$retv["coupon"]= array();
			$retv["status"]= 0;
			//fmt sql	
			$query      = "
			SELECT  DISTINCT gen.GeneratedCouponId,
			        CONCAT('$qrlink',gen.GeneratedCouponId,'.png') as qr_code,
				sub.PointsId,
				(
					select pts.Name
					from
					 points pts
					where
					pts.PointsId = sub.PointsId
					limit 1	
				) as PointsSystemName,
				sub.CustomerId,
				(
				   select CONCAT(cust.FirstName,' ' ,cust.LastName)
				   from
					customers  cust
				   where
					cust.CustomerId = sub.CustomerId
				   limit 1
				) as CustomerName,
				sub.ClientId,
				(
				   select clnt.CompanyName
				   from clients clnt
				   where
					clnt.ClientId = sub.ClientId
				   limit 1
				) as ClientName,
				
				gen.CouponId
			FROM 
				customer_subscriptions sub,
				coupon map,
				generated_coupons gen
			WHERE   1=1
				AND sub.ClientId   = '$client_id'
				AND sub.CustomerId = '$customer_id' $coupon_sql
				AND sub.PointsId   = map.PointsId
				AND sub.ClientId   = map.ClientId
				AND sub.Status     = 'ACTIVE'
				AND gen.Status     = 'PENDING'
				AND sub.PointsId   = gen.PointsId
				AND map.CouponId   = gen.CouponId
			";

			debug("SQL> $query;");
			
			//echo $query;
			//exit();

			//run
			$res = $this->conn->query($query);

			if (PEAR::isError($res)) {
				return $retv;
			}

			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
			{	
				$details = $this->getPtsMapBrandCampaign($row['pointsid']);
				$row['others'] = $details;
				$result_array["coupon"][] = $row;
				$counter++;
			}
			$result_array["totalrows"] = $counter;
			$result_array["status"]    = (($counter>0)?(1):(0));
			//give it back
			return ($counter == 0) ? (false) : ($result_array);
	}
	
	public function getPtsMapBrandCampaign($pointsid)
	{
		$query = "SELECT brandid, campaignid FROM points_mapping WHERE pointsid = $pointsid";
		debug("SQL> $query;");

		//run
		$res = $this->conn->query($query);
		
		$retv          = array();

		if (PEAR::isError($res)) {
			return null;
		}

		$result_array = array();
		while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$retv['brandid'] .= $row['brandid'];
			$retv['brandname'] .= $this->getTableFieldName("brandname", "brands", "brandid", $row['brandid']);
			$retv['campaignid'] .= $row['campaignid'];
			$retv['campaignname'] .= $this->getTableFieldName('campaignname', 'campaigns', 'campaignid', $row['campaignid']);
		}
		return $retv;
	}
	
	public function getTableFieldName($field, $table, $where, $value)
	{
		$query = "SELECT $field FROM $table WHERE $where = $value LIMIT 1";
		$res = $this->conn->query($query);
		while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{			
			
			return $row[$field];
		}
	}

 


	public function list_of_redeemed_coupon($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			$coupon_id  = addslashes($pdata["coupon_id"]);
			$qrlink     = addslashes($pdata["qrlink"]);

			//try			
			$coupon_sql = '';
			if($coupon_id > 0)
			{
				$coupon_sql = " AND map.CouponId   = '$coupon_id' ";
			}
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv          = array();
			$retv["coupon"]= array();
			$retv["status"]= 0;

			//fmt sql	
			$query      = "
			SELECT  DISTINCT gen.GeneratedCouponId,gen.Code,
			        CONCAT('$qrlink',gen.GeneratedCouponId,'.png') as qr_code,
				sub.PointsId,
				(
					select pts.Name
					from
					 points pts
					where
					pts.PointsId = sub.PointsId
					limit 1	
				) as PointsSystemName,
				sub.CustomerId,
				(
				   select CONCAT(cust.FirstName,' ' ,cust.LastName)
				   from
					customers  cust
				   where
					cust.CustomerId = sub.CustomerId
				   limit 1
				) as CustomerName,
				sub.ClientId,
				(
				   select clnt.CompanyName
				   from clients clnt
				   where
					clnt.ClientId = sub.ClientId
				   limit 1
				) as ClientName,
				gen.Status,
				gen.CouponId,
				gen.DateRedeemed
			FROM 
				customer_subscriptions sub,
				coupon map,
				generated_coupons gen
			WHERE   1=1
				AND sub.ClientId   = '$client_id'
				AND sub.CustomerId = '$customer_id' $coupon_sql
				AND sub.PointsId   = map.PointsId
				AND sub.ClientId   = map.ClientId
				AND sub.Status     = 'ACTIVE'
				AND gen.Status     != 'PENDING'
				AND gen.CustomerId = '$customer_id'
				AND sub.PointsId   = gen.PointsId
				AND map.CouponId   = gen.CouponId
			";
			debug("SQL> $query;");

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



	public function do_redeemed_a_coupon($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			$coupon_id  = addslashes($pdata["coupon_id"]  );
			$code       = addslashes($pdata["code"]       );
			$qrlink     = addslashes($pdata["qrlink"]);
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv          = array();
			$retv["result"]= array();
			$retv["status"]= 0;

			//chk if redeemed
			$done = $this->is_redeemed($pdata);
			//411   done already
			if ($done>0) {
				$retv['result_code'] = 411;
				$retv['error_txt']   = 'Coupon code already redeemed!';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}
			
			$query      = "
				SELECT  DISTINCT 
					gen.GeneratedCouponId,
					gen.Code,
					sub.PointsId,
					map.CouponType,
					IFNULL(map.PointsValue,0) as PointsValue,
					gen.CouponId,
					sub.SubscriptionId,
					sub.CustomerId,
					sub.ClientId,
					(
					select clnt.Status
					from clients clnt
					where
					clnt.ClientId = sub.ClientId
					limit 1
					) as Client_Status,
					gen.Status as Generation_status,
					(
					select cust.Status
					from
					customers  cust
					where
					cust.CustomerId = sub.CustomerId
					limit 1
					) as Customer_Status,
					map.Status as Coupon_Status,
					sub.BrandId,
					sub.CampaignId,
					(
					select chan.ChannelId
					from
					channels chan
					where
					  chan.ClientId     = sub.ClientId 
					and chan.BrandId    = sub.BrandId 
					and chan.CampaignId = sub.CampaignId 
					limit 1
					) as ChannelId,
					(
					select chan.Status
					from
					channels chan
					where
					  chan.ClientId     = sub.ClientId 
					and chan.BrandId    = sub.BrandId 
					and chan.CampaignId = sub.CampaignId 
					limit 1
					) as Channel_Status,
					(
					select brnd.Status
					from
					brands brnd
					where
					    brnd.BrandId   = sub.BrandId 
					limit 1
					) as Brand_Status,
					(
					select camp.Status
					from
					campaigns camp
					where
					    camp.CampaignId   = sub.CampaignId 
					limit 1
					) as Campaign_Status,
					(
					select typ.ActiontypeId
					from
					action_type typ
					where
					  typ.PointsId = map.PointsId
					and typ.ClientId = sub.ClientId 
					limit 1
					) as ActionTypeId,
					(
					select date_format(chan.DurationFrom,'%Y%m%d')
					from
					channels chan
					where
					    chan.ClientId   = sub.ClientId 
					and chan.BrandId    = sub.BrandId 
					and chan.CampaignId = sub.CampaignId 
					limit 1
					) as Channel_DurationFrom,
					(
					select date_format(chan.DurationTo,'%Y%m%d')
					from
					channels chan
					where
					    chan.ClientId   = sub.ClientId 
					and chan.BrandId    = sub.BrandId 
					and chan.CampaignId = sub.CampaignId 
					limit 1
					) as Channel_DurationTo,
					(
					select date_format(camp.DurationFrom,'%Y%m%d')
					from
					campaigns camp
					where
					    camp.CampaignId   = sub.CampaignId 
					limit 1
					) as Campaign_DurationFrom,
					(
					select date_format(camp.DurationTo,'%Y%m%d')
					from
					campaigns camp
					where
					    camp.CampaignId   = sub.CampaignId 
					limit 1
					) as Campaign_DurationTo,
					(
					select date_format(brnd.DurationFrom,'%Y%m%d')
					from
					brands brnd
					where
					    brnd.BrandId   = sub.BrandId 
					limit 1
					) as Brand_DurationFrom,
					(
					select date_format(brnd.DurationTo,'%Y%m%d')
					from
					brands brnd
					where
					    brnd.BrandId   = sub.BrandId 
					limit 1
					) as Brand_DurationTo,
					IFNULL((
					select sum(IFNULL(c.Balance,0))
					from
					 customer_points c
					 where
					     1=1
					 and c.PointsId       = sub.PointsId
					 and c.SubscriptionId = sub.SubscriptionId
					),0) as Customer_Points_Balance,
					IFNULL((
					select ifnull(d.Value,0) from 
					coupon_to_points d
					where 
					      d.CouponId  = map.CouponId
					  and d.status    = 'ACTIVE'
					  and d.ClientId  = sub.ClientId 
					),0) as Coupon_To_Points_Value,
					(curdate() <= map.ExpiryDate ) as coupon_not_expired,
					( (
						select count(1)
						from
						generated_coupons g
						where 1=1
						  and g.CouponId   = gen.CouponId
						  and g.PointsId   = gen.PointsId
						  and g.CustomerId = sub.CustomerId
					) < map.LimitPerUser ) as check_history_total
				FROM 
					customer_subscriptions sub,
					coupon map,
					generated_coupons gen
				WHERE   1=1
					AND sub.ClientId   = '$client_id'
					AND sub.CustomerId = '$customer_id'
					AND map.CouponId   = '$coupon_id'
					AND gen.Code       = '$code'
					AND sub.PointsId   = map.PointsId
					AND sub.ClientId   = map.ClientId
					AND sub.Status     = 'ACTIVE'
					AND gen.Status     = 'PENDING'
					AND sub.PointsId   = gen.PointsId
					AND map.CouponId   = gen.CouponId
					AND map.LimitPerUser > 0
			LIMIT 1
			";
			
			debug("SQL> $query;");
			
			
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
			//NOT-ALLOWED,coupon_to_points_value
			if(  @preg_match("/^(EXCHANGE_POINTS_TO_COUPON)$/i",$row["coupontype"]) )
			{
				$retv['result_code'] = 411;
				$retv['error_txt']   = 'Coupon Type is not allowed.';
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
			//no-balance,Customer_Points_Balance,coupon_to_points_value
			if(
			       @preg_match("/^(EXCHANGE_POINTS_TO_COUPON)$/i",$row["coupontype"]) and 
			       (
				       (    $row["customer_points_balance"] <  $row["pointsvalue"] and 
				          ( $row["customer_points_balance"] > 0 and $row["pointsvalue"] > 0 )
				       ) 
			       )
			   )
			{
				$retv['result_code'] = 409;
				$retv['error_txt']   = 'Insufficient Balance.';
				$retv["result"]      = null;
				//give it back
				return $retv;
			
			}
			//redeem coupon limit exceeded.
			if( @intval($row["check_history_total"])  == 0)
			{
				$retv['result_code'] = 410;
				$retv['error_txt']   = 'Redeem Coupon Limit Exceeded.';
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
			$pdata["coupon_type"]         = $row["coupontype"];
			$pdata["balance"]             = $row["pointsvalue"];
			$pdata["qr_code"]			  = $qrlink . $row["generatedcouponid"].'.png';
			
			
			
			//customer_points (insert/update, if coupon.CouponType=CONVERT_TO_POINTS, Balance=Balance + coupon.PointsValue, Total = Total + coupon.PointsValue)
			//points_log (insert/update, LogType="COUPON_TO_POINTS", Value=coupon.PointsValue)
			$rdata = array();
			$sdata = array();
			$tdata = array();
			
			//regular
			if(@preg_match("/^(REGULAR)$/i",$row["coupontype"]))
			{
				//set flag
				if($row["coupon_to_points_value"] > 0)
				{
					//have-record
					$pdata["value"]               = $row["coupon_to_points_value"];
					$pdata["balance"]             = $row["coupon_to_points_value"];
					$pdata["coupon_type"]         = 'REGULAR_COUPON_WITH_COUPON_TO_POINTS';
					$rdata = $this->update_redeemed_flag($pdata,'REDEEMED_AND_CONVERTED');
				}
				else
				{
					//no-record
					$rdata = $this->update_redeemed_flag($pdata,'REDEEMED');
				}
			}
			
			//convert pts
			if(@preg_match("/^(CONVERT_TO_POINTS)$/i",$row["coupontype"]))
			{
				$rdata = $this->update_redeemed_flag($pdata,'REDEEMED_AND_CONVERTED');
			}
			
			//exchange pts
			if(@preg_match("/^(EXCHANGE_POINTS_TO_COUPON)$/i",$row["coupontype"]))
			{
				$rdata = $this->update_redeemed_flag($pdata,'REDEEMED_AND_CONVERTED');
			}
			
			if($pdata["value"]>0)
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
			
			// expose rewwards list details
			$details = $this->getCouponDetails($coupon_id, $qrlink, $pdata["qr_code"]);
			
			
			// $retv["result"]      = array();
			$retv["result"]      = array($details);
			
			//just in case			
			if('show' == 'more')
				$retv['results']     = array('points_log'     => $tdata, 
							    'customer_points' => $sdata,
							    'redeemed_flag'   => $rdata);			
			//give it back
			return $retv;

	}
	
	public function getCouponDetails($couponid, $qrlink, $qr_code)
	{
		$query = "SELECT a.*, b.name AS ptsname FROM coupon a, points b WHERE  a.pointsid = b.pointsid AND couponid = $couponid LIMIT 1";
		debug("SQL> $query;");

		//run
		$res = $this->conn->query($query);
		
		$retv          = array();

		if (PEAR::isError($res)) {
			return null;
		}

		$result_array = array();
		while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$retv['couponid'] .= $row['couponid'];
			$retv['pointsid'] .= $row['pointsid'];
			$retv['pointssystemname'] .= $row['ptsname'];
			$retv['couponname'] .= $row['couponname'];
			$retv['expirydate'] .= $row['expirydate'];
			$retv['image'] .= $row['image'];
			$retv['couponurl'] .= $row['couponurl'];
			$retv['qr_code'] .= $qrlink . $qr_code . '.png';
			$details = $this->getPtsMapBrandCampaign($row['pointsid']);
			$retv['brandid'] = $details['brandid'];
			$retv['brandname'] = $details['brandname'];
			$retv['campaignid'] = $details['campaignid'];
			$retv['campaignname'] = $details['campaignname'];
			
		}
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
			$coupon_type     = addslashes($pdata["coupon_type"]  );
			
			$balstr          = "$value";
			if(@preg_match("/EXCHANGE_POINTS_TO_COUPON/i",$coupon_type))	
			{
				$balstr  = "-$value";
			}
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
				'$coupon_type',        
				'$balstr',          
				'$created_by',      
				Now()    
			)
			";

			debug("SQL> $query;");
			//run
			$row = $this->conn->exec($query);
			//get it
			$retv['PointLogId']      = $this->conn->lastInsertId('points_log', 'PointLogId');
			$retv['save_points_log'] = $row;

			//give it back
			return $retv;
	}


	public function update_redeemed_flag($pdata=null,$flag='REDEEMED')
	{

			//fmt
			$generated_coupon_id = addslashes($pdata["generated_coupon_id"]  );
			$customer_id         = addslashes($pdata["customer_id"]);
			$code                = addslashes($pdata["code"]);
			$points_id           = addslashes($pdata["points_id"]);
			$created_by          = addslashes($pdata["created_by"]);
			$flag                = addslashes($flag);
			
			$retv   = array();
			$retv['update_redeemed_flag'] = 0;
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			UPDATE generated_coupons
			SET
					CustomerId   = '$customer_id',
					Status       = '$flag',
					UpdatedBy    = '$created_by',
					DateRedeemed = Now(),
					DateUpdated  = Now()
			WHERE 
				GeneratedCouponId    = '$generated_coupon_id'
				LIMIT 1
			";

			debug("SQL> $query;");
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
			$balance         = addslashes($pdata["balance"]  );
			$coupon_type     = addslashes($pdata["coupon_type"]  );
			
			
			$data       = array();
			$retv       = array();
			$query      = "
				SELECT  CustomerPointId
				FROM
					customer_points
				WHERE
				1=1
				AND SubscriptionId = '$subscription_id'
				AND PointsId       = '$points_id'
			";

			debug("SQL> $query;");

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
			
			//pts
			$usedcol = '';
			$usedval = '';
			$balstr  = "$balance";
			$valsql  = " Total = (Total    + '$balance'), ";
			if(@preg_match("/EXCHANGE_POINTS_TO_COUPON/i",$coupon_type))	
			{
				$balstr  = "-$balance";
				$balsql  = " Used = (Used    + '$balance'), ";
				$usedcol = " Used,         ";
				$usedval = " '$balance',   ";
				$valsql  = '';
			}
			if($subid <= 0)
			{
				//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
				$query      = "
				INSERT INTO customer_points (
					SubscriptionId ,
					PointsId       ,
					Balance        ,$usedcol
					Total          ,
					CreatedBy      ,
					DateCreated 
				)
				VALUES (
					'$subscription_id', 
					'$points_id', 
					'$balstr'   ,$usedval          
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
						Balance        = (Balance + '$balstr'), $balsql
						UpdatedBy      = '$created_by', $valsql
						DateUpdated    = Now()
					WHERE
						SubscriptionId = '$subscription_id'
					    AND PointsId       = '$points_id'
				";
				//run
				$res = $this->conn->exec($query);
				$data['save_customer_points'] = $res;
			}

			debug("SQL> $query;");
			//give it back
			return $data;
	}


	function is_redeemed($pdata=array())
	{
		//fmt
		$client_id  = addslashes($pdata["client_id"]  );
		$customer_id= addslashes($pdata["customer_id"]);
		$coupon_id  = addslashes($pdata["coupon_id"]  );
		$code       = addslashes($pdata["code"]       );
		$is_redeemed= 0;
		
		$query      = "
				select 
					count(1) as is_redeemed 
				from generated_coupons 
				where 
					    CustomerId = '$customer_id' 
					and CouponId   = '$coupon_id' 
					and Status    != 'PENDING' 
					and Code       = '$code'
					and DateRedeemed is not null
			";

		debug("SQL> $query;");

		//run
		$res = $this->conn->query($query);
		if (PEAR::isError($res)) {
			//new
			$is_redeemed   = 0;
		}
	
		//get it
		$row           = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$is_redeemed   = $row["is_redeemed"];
		return $is_redeemed;
	}
	
	function get1coupon($pdata=array())
	{
		//fmt
		$client_id  = addslashes($pdata["client_id"]  );
		$customer_id= addslashes($pdata["customer_id"]);
		$coupon_id  = addslashes($pdata["coupon_id"]  );
		$is_redeemed= 0;
		//fmt
		$query      = "
					select
						gen.*
					from 
						generated_coupons gen,
						coupon c,
						customers cust,
						points pts
					where 1=1
						and gen.CustomerId IS NULL
						and gen.CouponId   = '$coupon_id' 
						and gen.DateRedeemed is  null
						and gen.CouponId   = c.CouponId
						and c.ClientId     = '$client_id'
						and c.CouponType   = 'EXCHANGE_POINTS_TO_COUPON'
						and c.ClientId     = cust.ClientId
						and cust.CustomerId= '$customer_id' 
						and c.PointsId     = pts.PointsId
						and c.ClientId     = pts.ClientId
						and gen.Status     = 'PENDING'
					order by rand()
					limit 1				
			";

		debug("SQL> $query;");

		//retv
		$retv           = array();
		$retv['exists'] = 0;
		
		//run
		$res = $this->conn->query($query);
		if (PEAR::isError($res)) {
			//new
			return $retv;
		}
	
		//get it
		$row           = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		if(isset($row))
		{
			$retv['exists'] = 1;
			$retv['data']   = $row;
		}
		//give it back
		return $retv;
	}

	public function do_redeemed_a_coupon_by_exch_pts($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			$coupon_id  = addslashes($pdata["coupon_id"]  );
			$qrlink     = addslashes($pdata["qrlink"]);
			
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv          = array();
			$retv["result"]= array();
			$retv["status"]= 0;

			//chk if redeemed
			$avail = $this->get1coupon($pdata);
			
			//411   no avail
			if ($avail['exists'] == 0 or !isset($avail['data'])) {
				$retv['result_code'] = 411;
				$retv['error_txt']   = 'No Coupon code is available for this coupon-type!';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}
			
			//get coupon code
			$code       = addslashes($avail['data']["code"]);
			
			$query      = "
				SELECT  DISTINCT 
					gen.GeneratedCouponId,
					gen.Code,
					sub.PointsId,
					map.CouponType,
					IFNULL(map.PointsValue,0) as PointsValue,
					gen.CouponId,
					sub.SubscriptionId,
					sub.CustomerId,
					sub.ClientId,
					(
					select clnt.Status
					from clients clnt
					where
					clnt.ClientId = sub.ClientId
					limit 1
					) as Client_Status,
					gen.Status as Generation_status,
					(
					select cust.Status
					from
					customers  cust
					where
					cust.CustomerId = sub.CustomerId
					limit 1
					) as Customer_Status,
					map.Status as Coupon_Status,
					sub.BrandId,
					sub.CampaignId,
					(
					select chan.ChannelId
					from
					channels chan
					where
					  chan.ClientId     = sub.ClientId 
					and chan.BrandId    = sub.BrandId 
					and chan.CampaignId = sub.CampaignId 
					limit 1
					) as ChannelId,
					(
					select chan.Status
					from
					channels chan
					where
					  chan.ClientId     = sub.ClientId 
					and chan.BrandId    = sub.BrandId 
					and chan.CampaignId = sub.CampaignId 
					limit 1
					) as Channel_Status,
					(
					select brnd.Status
					from
					brands brnd
					where
					    brnd.BrandId   = sub.BrandId 
					limit 1
					) as Brand_Status,
					(
					select camp.Status
					from
					campaigns camp
					where
					    camp.CampaignId   = sub.CampaignId 
					limit 1
					) as Campaign_Status,
					(
					select typ.ActiontypeId
					from
					action_type typ
					where
					  typ.PointsId = map.PointsId
					and typ.ClientId = sub.ClientId 
					limit 1
					) as ActionTypeId,
					(
					select date_format(chan.DurationFrom,'%Y%m%d')
					from
					channels chan
					where
					    chan.ClientId   = sub.ClientId 
					and chan.BrandId    = sub.BrandId 
					and chan.CampaignId = sub.CampaignId 
					limit 1
					) as Channel_DurationFrom,
					(
					select date_format(chan.DurationTo,'%Y%m%d')
					from
					channels chan
					where
					    chan.ClientId   = sub.ClientId 
					and chan.BrandId    = sub.BrandId 
					and chan.CampaignId = sub.CampaignId 
					limit 1
					) as Channel_DurationTo,
					(
					select date_format(camp.DurationFrom,'%Y%m%d')
					from
					campaigns camp
					where
					    camp.CampaignId   = sub.CampaignId 
					limit 1
					) as Campaign_DurationFrom,
					(
					select date_format(camp.DurationTo,'%Y%m%d')
					from
					campaigns camp
					where
					    camp.CampaignId   = sub.CampaignId 
					limit 1
					) as Campaign_DurationTo,
					(
					select date_format(brnd.DurationFrom,'%Y%m%d')
					from
					brands brnd
					where
					    brnd.BrandId   = sub.BrandId 
					limit 1
					) as Brand_DurationFrom,
					(
					select date_format(brnd.DurationTo,'%Y%m%d')
					from
					brands brnd
					where
					    brnd.BrandId   = sub.BrandId 
					limit 1
					) as Brand_DurationTo,
					IFNULL((
					select sum(IFNULL(c.Balance,0))
					from
					 customer_points c
					 where
					     1=1
					 and c.PointsId       = sub.PointsId
					 and c.SubscriptionId = sub.SubscriptionId
					),0) as Customer_Points_Balance,
					IFNULL((
					select ifnull(d.Value,0) from 
					coupon_to_points d
					where 
					      d.CouponId  = map.CouponId
					  and d.status    = 'ACTIVE'
					  and d.ClientId  = sub.ClientId 
					),0) as Coupon_To_Points_Value,
					(curdate() <= map.ExpiryDate ) as coupon_not_expired,
					( (
						select count(1)
						from
						generated_coupons g
						where 1=1
						  and g.CouponId   = gen.CouponId
						  and g.PointsId   = gen.PointsId
						  and g.CustomerId = sub.CustomerId
					) < map.LimitPerUser ) as check_history_total
				FROM 
					customer_subscriptions sub,
					coupon map,
					generated_coupons gen
				WHERE   1=1
					AND sub.ClientId   = '$client_id'
					AND sub.CustomerId = '$customer_id'
					AND map.CouponId   = '$coupon_id'
					AND gen.Code       = '$code'
					AND sub.PointsId   = map.PointsId
					AND sub.ClientId   = map.ClientId
					AND sub.Status     = 'ACTIVE'
					AND gen.Status     = 'PENDING'
					AND sub.PointsId   = gen.PointsId
					AND map.CouponId   = gen.CouponId
					and map.CouponType = 'EXCHANGE_POINTS_TO_COUPON'
					AND map.LimitPerUser > 0
			LIMIT 1
			";
			
			debug("SQL> $query;");
			
			
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
			//no-balance,Customer_Points_Balance,coupon_to_points_value
			if(
			       @preg_match("/^(EXCHANGE_POINTS_TO_COUPON)$/i",$row["coupontype"]) and 
			       (
				       (    $row["customer_points_balance"] <  $row["pointsvalue"] and 
				          ( $row["customer_points_balance"] > 0 and $row["pointsvalue"] > 0 )
				       ) 
			       )
			   )
			{
				$retv['result_code'] = 409;
				$retv['error_txt']   = 'Insufficient Balance.';
				$retv["result"]      = null;
				//give it back
				return $retv;
			
			}
			//redeem coupon limit exceeded.
			if( @intval($row["check_history_total"])  == 0)
			{
				$retv['result_code'] = 410;
				$retv['error_txt']   = 'Redeem Coupon Limit Exceeded.';
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
			$pdata["coupon_type"]         = $row["coupontype"];
			$pdata["balance"]             = $row["pointsvalue"];
			$pdata["qr_code"]			  = $qrlink . $row["generatedcouponid"].'.png';
			
			
			//customer_points (insert/update, if coupon.CouponType=CONVERT_TO_POINTS, Balance=Balance + coupon.PointsValue, Total = Total + coupon.PointsValue)
			//points_log (insert/update, LogType="COUPON_TO_POINTS", Value=coupon.PointsValue)
			$rdata = array();
			$sdata = array();
			$tdata = array();
			
			//exchange pts
			$rdata = $this->update_redeemed_flag($pdata,'REDEEMED_AND_CONVERTED');
			
			
			if($pdata["value"]>0)
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
			$retv['generated_coupon'] = $avail['data']['code'];
			
			// expose rewwards list details
			$details = $this->getCouponDetails($coupon_id, $qrlink, $pdata["qr_code"]);
			
			// $retv["result"]      = array();
			$retv["result"]      = array($details);
			
			// $retv["result"]      = array('generated_coupon' => $avail['data']['code']);
			/*
			//just in case			
			if('show' == 'show')
				$retv['results']     = array('generated_coupon'=> $avail['data']['code'],
											 'points_log'      => $tdata, 
											 'customer_points' => $sdata);
													
			*/
			//give it back
			return $retv;

	}


}
?>
