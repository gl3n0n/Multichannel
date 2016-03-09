<?php
class PointsActionType
{
        public $conn;

        public function PointsActionType($conn) 
        {
            $this->conn       = $conn;
            $this->table_name = 'points_mapping';
        }

	
	public function list_of_action_pts($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			
			$retv                 = array();
			$retv["totalrows"] = 0;
			$retv["breakdown"]    = array();

			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			SELECT   
				sub.PointsId,
				sub.SubscriptionId,
				typ.ActiontypeId  ,
				typ.Name as ActionName,
				typ.Value         ,
				typ.PointsAction  ,
				typ.PointsCapping ,
				typ.PointsLimit   ,
				typ.StartDate     ,
				typ.EndDate       ,
				cust.FirstName,
				cust.LastName,
				cust.Email,
				cust.FBId,
				cust.TwitterHandle,
				pts.Name as PointsSystemName,
				pmap.ClientId ,
				(
				 select clnt.CompanyName FROM
				 clients clnt
				 where
				 clnt.ClientId = pmap.ClientId
				 LIMIT 1  
				) as CompanyName ,
				sub.CustomerId ,
				pmap.BrandId,
				(
				 select brnd.BrandName FROM
				 brands brnd
				 where
				 brnd.BrandId = pmap.BrandId
				 LIMIT 1  
				) as BrandName,
				pmap.CampaignId ,
				(
				 select camp.CampaignName FROM
				 campaigns camp
				 where
				 camp.CampaignId = pmap.CampaignId
				 LIMIT 1  
				) as CampaignName,
				pmap.ChannelId,
				(
				 select chan.ChannelName FROM
				 channels chan
				 where
				 chan.ChannelId = pmap.ChannelId
				 LIMIT 1  
				) as ChannelName
			FROM
				customer_subscriptions sub,
				points_mapping pmap,
				action_type typ,
				customers  cust,
				points pts
			WHERE   1=1
				AND sub.ClientId   = '$client_id'
				AND sub.CustomerId = '$customer_id'
				AND sub.PointsId   = typ.PointsId
				AND sub.ClientId   = typ.ClientId
				AND sub.PointsId   = pts.PointsId
				AND sub.ClientId   = pts.ClientId
				AND sub.Status     = 'ACTIVE'
				AND pmap.Status    = 'ACTIVE'
				AND typ.Status     = 'ACTIVE'
				AND pts.Status     = 'ACTIVE'
				AND sub.CustomerId = cust.CustomerId
				AND sub.PointsId   = pmap.PointsId
				AND sub.ClientId   = pmap.ClientId
				AND sub.BrandId    = pmap.BrandId
				AND sub.CampaignId = pmap.CampaignId
				AND EXISTS(
							select 1 FROM
								channels chan
							where
							chan.ChannelId = pmap.ChannelId
							and 
							chan.Status    = 'ACTIVE'
					LIMIT 1  
				)
				AND EXISTS(
							 select 1 FROM
							 brands brnd
							 where
							 brnd.BrandId = pmap.BrandId
							 and
							 brnd.Status     = 'ACTIVE'
							 LIMIT 1  
				)
				AND EXISTS(
							 select 1 FROM
							 campaigns camp
							 where
							 camp.CampaignId = pmap.CampaignId
							 and
							 camp.Status     = 'ACTIVE'
							 LIMIT 1  
				)
			";

			debug("SQL> $query;");
			
			//run
			$res = $this->conn->query($query);

			if (PEAR::isError($res)) {
				return false;
			}

			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
	    		{				
				// $result_array[] = $row;
				$retv["breakdown"][] = $row;
				$counter++;
			}
			// $result_array["totalrows"] = $counter;
			$retv["totalrows"] = $counter;
			//give it back
			return ($counter == 0) ? (false) : ($retv);
	}


	public function gain_points($pdata=null)
	{
		    
			//fmt
			$client_id     = addslashes($pdata["client_id"]  );
			$customer_id   = addslashes($pdata["customer_id"]);
			$channel_id    = addslashes($pdata["channel_id"]  );
			$brand_id      = addslashes($pdata["brand_id"]   );
			$campaign_id   = addslashes($pdata["campaign_id"]);
			$actiontype_id = addslashes($pdata["actiontype_id"] );

			//holder			
			$retv         = array();
			$result_array = array();
			$counter      = 0;

			//sql -> CustomerId | ClientId | BrandId | CampaignId | ChannelId |ActiontypeId
			$query      = "
			SELECT
				sub.SubscriptionId,
				typ.ActiontypeId  ,
				typ.Name as ActionName,
				typ.Value         ,
				typ.PointsAction  ,
				typ.PointsCapping ,
				typ.PointsLimit   ,
				typ.PointsId      ,
				typ.Status        ,
				date_format(typ.StartDate,'%Y%m%d') as StartDate,
				date_format(typ.EndDate,  '%Y%m%d') as EndDate,
				pmap.BrandId  ,
				(
				 select brnd.Status FROM
				  brands brnd
				  where
				  brnd.BrandId = pmap.BrandId
				  LIMIT 1  
				) as Brand_Status,
				(
				 select date_format(brnd.DurationFrom,'%Y%m%d') FROM
				  brands brnd
				  where
				  brnd.BrandId = pmap.BrandId
				  LIMIT 1  
				) as Brand_DurationFrom,
				(
				 select date_format(brnd.DurationTo,'%Y%m%d') FROM
				  brands brnd
				  where
				  brnd.BrandId = pmap.BrandId
				  LIMIT 1  
				) as Brand_DurationTo,
				pmap.CampaignId ,
				(
				 select camp.Status FROM
				  campaigns camp
				  where
				  camp.CampaignId = pmap.CampaignId
				  LIMIT 1  
				) as Campaign_Status,
				(
				 select date_format(camp.DurationFrom,  '%Y%m%d') FROM
				  campaigns camp
				  where
				  camp.CampaignId = pmap.CampaignId
				  LIMIT 1  
				) as Campaign_DurationFrom,
				(
				 select date_format(camp.DurationTo,  '%Y%m%d') FROM
				  campaigns camp
				  where
				  camp.CampaignId = pmap.CampaignId
				  LIMIT 1  
				) as Campaign_DurationTo,
				pmap.ChannelId,
				(
				 select chan.Status FROM
				 channels chan
				 where
				 chan.ChannelId = pmap.ChannelId
				 LIMIT 1
				) as Channel_Status ,
				(
				 select date_format(chan.DurationFrom,  '%Y%m%d') FROM
				 channels chan
				 where
				 chan.ChannelId = pmap.ChannelId
				 LIMIT 1
				) as Channel_DurationFrom ,
				(
				 select date_format(chan.DurationTo,  '%Y%m%d')  FROM
				 channels chan
				 where
				 chan.ChannelId = pmap.ChannelId
				 LIMIT 1
				) as Channel_DurationTo ,
				(
				 select clnt.Status FROM
				 clients clnt
				 where
				 clnt.ClientId = pmap.ClientId
				 LIMIT 1
				) as Client_Status ,
				cust.Status as Customer_Status
			FROM
				points pts,
				points_mapping pmap,
				customer_subscriptions sub,
				action_type typ,
				customers  cust
			WHERE   1=1
				AND sub.ClientId     = '$client_id'
				AND sub.CustomerId   = '$customer_id'
				AND pmap.BrandId     = sub.BrandId
				AND pmap.CampaignId  = sub.CampaignId
				AND typ.ActiontypeId = '$actiontype_id'
				AND pmap.BrandId     = '$brand_id'
				AND pmap.CampaignId  = '$campaign_id'
				AND pmap.ChannelId   = '$channel_id'
				AND pmap.PointsId    = sub.PointsId
				AND pmap.ClientId    = sub.ClientId
				AND sub.PointsId     = typ.PointsId
				AND sub.ClientId     = typ.ClientId
				AND sub.PointsId     = pts.PointsId
				AND sub.ClientId     = pts.ClientId
				AND sub.CustomerId   = cust.CustomerId
			";

			debug("SQL> $query;");
			
			$retv["status"]    = 0;

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
			$retv["totalrows"] = $counter;
			
			
			/*
			@EXPECTED OUTPUT:
				200        OK
				401        Record Not Found (not currently subscribed)
				402        Daily Limit exceeded. (if points_mapping.PointsLimit != 0  
						AND points_mapping.PointCapping=DAILY 
						AND total points_log.Value  >= points_mapping.PointsLimit )
				403        Points Limit exceeded. (if points_mapping.PointsLimit != 0  
						AND points_mapping.PointCapping=NONE 
						AND total points_log.Value  >= points_mapping.PointsLimit )
				405        Inactive Point Action (out of range, points_mapping.StartDate 
						and points_mapping.EndDate,
						or points_mapping.Status != ACTIVE)
				406        Inactive Channel (out of range, channel.DurationFrom and channel.DurationTo, 
						or channel.Status != ACTIVE)
				407        Inactive Campaign (out of range, campaign.DurationFrom and campaign.DurationTo, 
						or campaign.Status != ACTIVE)
				408        Inactive Brands (out of range, brands.DurationFrom and brands.DurationTo, 
						or brands.Status != ACTIVE)
				409        Inactive Client (client.Status != ACTIVE)
				410        Inactive Customer (customers.Status != ACTIVE)
			*/

			//401        Record Not Found (not currently subscribed)
			if($counter  == 0)
			{
				$retv['result_code'] = 401;
				$retv['error_txt']   = 'Record Not Found (Not currently Subscribed)';
				//give it back
				return $retv;
			}
			
			//save
			$pdata["points_id"]       = $row["pointsid"];
			$pdata["subscription_id"] = $row["subscriptionid"];
			$logpoints                = $this->getSumValue($pdata);
			
			//402        Daily Limit exceeded. (if points_mapping.PointsLimit != 0  
			//              AND points_mapping.PointCapping=DAILY 
			//              AND total points_log.Value  >= points_mapping.PointsLimit )
			if(
			     $row["pointslimit"]  != 0       and
			     $row["pointcapping"]  = 'DAILY' and
			     $logpoints >= $row["pointslimit"]
			 )
			 {
			 
			 	$retv['result_code'] = 402;
				$retv['error_txt']   = 'Max Limit Reached.';
				//give it back
				return $retv;
			 }
			 
			//403        Points Limit exceeded. (if points_mapping.PointsLimit != 0  
			//AND points_mapping.PointCapping=NONE 
			//AND total points_log.Value  >= points_mapping.PointsLimit )
			if(
			     $row["pointslimit"]  != 0       and
			     $row["pointcapping"]  = 'NONE'  and
			     $logpoints >= $row["pointslimit"]
			 )
			 {
			 
			 	$retv['result_code'] = 402;
				$retv['error_txt']   = 'Points Limit Exceeded.';
				//give it back
				return $retv;
			 }

			//405        Inactive Point Action (out of range, points_mapping.StartDate 
			//and points_mapping.EndDate,
			//or points_mapping.Status != ACTIVE)
			$dit = intval(@date('Ymd'));
			if(
			     $row["status"]  != 'ACTIVE'  and
			     ( ! ( $dit >= $row["startdate"] and $dit <= $row["enddate"]  ) )
			 )
			 {
			 
			 	$retv['result_code'] = 405;
				$retv['error_txt']   = "Inactive Point Action";
				//give it back
				return $retv;
			 }

			//406        Inactive Channel (out of range, channel.DurationFrom and channel.DurationTo, 
			//		or channel.Status != ACTIVE)
			if(
			     $row["channel_status"]  != 'ACTIVE'  and
			     ( ! ( $dit >= $row["channel_durationfrom"] and $dit <= $row["channel_durationto"]  ) )
			 )
			 {

				$retv['result_code'] = 406;
				$retv['error_txt']   = 'Inactive Channel';
				//give it back
				return $retv;
			 }

			
			
			//407        Inactive Campaign (out of range, campaign.DurationFrom and campaign.DurationTo, 
			//		or campaign.Status != ACTIVE)
			if(
			     $row["campaign_status"]  != 'ACTIVE'  and
			     ( ! ( $dit >= $row["campaign_durationfrom"] and $dit <= $row["campaign_durationto"]  ) )
			 )
			 {

				$retv['result_code'] = 407;
				$retv['error_txt']   = 'Inactive Campaign';
				//give it back
				return $retv;
			 }			
			
			//408        Inactive Brands (out of range, brands.DurationFrom and brands.DurationTo, 
			//		or brands.Status != ACTIVE)
			if(
				$row["brand_status"]  != 'ACTIVE'  and
				( ! ( $dit >= $row["brand_durationfrom"] and $dit <= $row["brand_durationto"]  ) )
			)
			{
				$retv['result_code'] = 408;
				$retv['error_txt']   = 'Inactive Brands';
				//give it back
				return $retv;
			}
			
			//409        Inactive Client (client.Status != ACTIVE)
			if($row["client_status"]  != 'ACTIVE'  )
			{
				$retv['result_code'] = 409;
				$retv['error_txt']   = 'Inactive Client';
				//give it back
				return $retv;
			}
			
			//410        Inactive Customer (customers.Status != ACTIVE)
			if($row["customer_status"]  != 'ACTIVE'  )
			{
				$retv['result_code'] = 410;
				$retv['error_txt']   = 'Inactive Customer';
				//give it back
				return $retv;
			}
			
			
			//good
			//points_log (insert, LogType="POINTS", Value=action_type.Value)
			if(0){
			$pdata["channel_id"]  = $row["channelid"] ;
			$pdata["brand_id"]    = $row["brandid"]   ;
			$pdata["campaign_id"] = $row["campaignid"];
			}

			$pdata["value"]       = $row["value"];
			$tdata                = $this->save_points_log($pdata);
			
			//customer_points (insert/update, SubscriptionId, Balance=Balance + action_type.Value, Total = Total + action_type.Value)
			$sdata          = $this->save_customer_points($pdata);
			
			//give it back
			$retv["status"]      = 1;
			$retv['result_code'] = 200;
			$retv['error_txt']   = 'Success';
			$retv['results']     = array('points_log' => $tdata, 'customer_points' => $sdata);
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
				'POINTS',        
				'$value',          
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
				AND PointsId      = '$points_id'
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
			
			
			if($subid <= 0)
			{
				//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
				$query      = "
				INSERT INTO customer_points (
					SubscriptionId ,
					PointsId       ,
					Balance        ,
					Total          ,
					CreatedBy      ,
					DateCreated 
				)
				VALUES (
					'$subscription_id', 
					'$points_id', 
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
						AND PointsId   = '$points_id'
				";
				//run
				$res = $this->conn->exec($query);
				$data['save_customer_points'] = $res;
			}

			//give it back
			return $data;
	}


	
	
	public function getSumValue($pdata=null)
	{

		$client_id       = addslashes($pdata["client_id"]  );
		$customer_id     = addslashes($pdata["customer_id"]);
		$channel_id      = addslashes($pdata["channel_id"]  );
		$brand_id        = addslashes($pdata["brand_id"]   );
		$campaign_id     = addslashes($pdata["campaign_id"]);
		$actiontype_id   = addslashes($pdata["actiontype_id"] );
		$points_id       = addslashes($pdata["points_id"]  );
		$subscription_id = addslashes($pdata["subscription_id"]  );


		//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
		$query      = "
			SELECT  SUM(IFNULL(Value,0)) as total
			FROM
				points_log
			WHERE
			1=1
			AND LogType       = 'POINTS'
			AND CustomerId    = '$customer_id'
			AND PointsId      = '$points_id'
			AND ClientId      = '$client_id'
			AND BrandId       = '$brand_id'
			AND CampaignId    = '$campaign_id'
			AND PointsId      = '$points_id'
			AND ActiontypeId  = '$actiontype_id'
			AND SubscriptionId= '$subscription_id'
		";
		
		$query      = "
			SELECT  SUM(IFNULL(Value,0)) as total
			FROM
				points_log
			WHERE
			1=1
			AND LogType       = 'POINTS'
			AND CustomerId    = '$customer_id'
			AND PointsId      = '$points_id'
			AND ClientId      = '$client_id'
			AND BrandId       = '$brand_id'
			AND CampaignId    = '$campaign_id'
			AND ActiontypeId  = '$actiontype_id'
			AND DateCreated  >= curdate()
		";

		debug("SQL> $query;");

		//run
		$res = $this->conn->query($query);
		if (PEAR::isError($res)) {
			return false;
		}
		
		//get it
		$row     = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		//give it back
		return $row["total"];
	}
	
	
	public function list_of_customer_pts($pdata=null)
	{

			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			$retv                 = array();
			$retv["total_points"] = 0;
			$retv["breakdown"]    = array();
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
				SELECT  
					SUM(IFNULL(pmap.Value,0)) as total,
					sub.PointsId,
					sub.SubscriptionId,
					pmap.CustomerId,
					(
					  select concat(cust.FirstName,' ',cust.LastName)
					  from
					  customers cust
					  where
					  cust.CustomerId = pmap.CustomerId
					) as CustomerName,
					pmap.ClientId,
					(
					 select clnt.CompanyName 
					 from
					 clients clnt
					 where
					  clnt.ClientId = pmap.ClientId
					) as ClientName,
					pts.Name as PointSystemName
			FROM 
				customer_subscriptions sub,
				points_log pmap,
				points pts
			WHERE   1=1
				AND sub.SubscriptionId    = pmap.SubscriptionId
				AND sub.ClientId    = '$client_id'
				AND sub.CustomerId  = '$customer_id'
				AND sub.CustomerId  = pmap.CustomerId 
				AND sub.PointsId    = pmap.PointsId
				AND pts.PointsId    = pmap.PointsId
				AND sub.Status      = 'ACTIVE'
			GROUP BY 
				pmap.PointsId
			";

			$query      = "
				SELECT  
					SUM(IFNULL(pmap.Balance,0)) as total,
					sub.PointsId,
					sub.CustomerId,
					(
					select concat(cust.FirstName,' ',cust.LastName)
					from
					customers cust
					where
					cust.CustomerId = sub.CustomerId
					) as CustomerName,
					sub.ClientId,
					(
					select clnt.CompanyName 
					from
					clients clnt
					where
					clnt.ClientId = sub.ClientId
					) as ClientName,
					pts.Name as PointSystemName
				FROM 
					customer_subscriptions sub,
					customer_points pmap,
					points pts
				WHERE   1=1
					AND sub.SubscriptionId    = pmap.SubscriptionId
					AND sub.ClientId    = '$client_id'
					AND sub.CustomerId  = '$customer_id'
					AND sub.PointsId    = pmap.PointsId
					AND pts.PointsId    = pmap.PointsId
					AND sub.Status      = 'ACTIVE'
				GROUP BY 
					sub.PointsId
			";
			
			debug("SQL> $query;");
			
			//run
			$res = $this->conn->query($query);
			if (PEAR::isError($res)) {
				$retv["status"] = 0;
				return $retv;
			}

			$counter   = 0;
			$total_pts = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
			{				
				//save
				$retv["breakdown"][] = $row;
				$total_pts += $row["total"];
				$counter++;
			}
			
			$retv["totalrows"]    = $counter;
			$retv["total_points"] = $total_pts;
			$retv["status"]       = ($counter>0)?(1):(0);
			
			//give it back
			return $retv;
	}

}
?>
