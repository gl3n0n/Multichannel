<?php
class PointsList
{
        public $conn;

        public function PointsList($conn) 
        {
            $this->conn       = $conn;
            $this->table_name = 'customer_subscriptions';
        }
	

	public function do_update_points($pdata=null)
	{
		    
			//fmt
			$client_id      = addslashes($pdata["client_id"  ]);
			$customer_id    = addslashes($pdata["customer_id"]);
			$points_id      = addslashes($pdata["points_id"  ]);
			$brand_id       = addslashes($pdata["brand_id"   ]);
			$campaign_id    = addslashes($pdata["campaign_id"]);
			$value          = addslashes($pdata["value"      ]);
			$action         = addslashes($pdata["action"     ]);
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv          = array();
			$retv["result"]= array();
			$retv["status"]= 0;
		        $counter       = 0;
		        $sublist       = array();
		        
		        $subscription_ids = array();
		        $points_ids       = array();
			
			//fmt sql
			$query      = "
			SELECT  DISTINCT
				cust.SubscriptionId,
				cust.CustomerId,
				cust.PointsId,
				cust.ClientId,
				cust.BrandId,
				cust.CampaignId,
				act.ActiontypeId,
				cust.Status as Customer_Subscriptions_Status,
				cusm.Status as Customer_Status,
				(
					 select clnt.Status FROM
					 clients clnt
					 where
						clnt.ClientId = cust.ClientId
					 LIMIT 1
				) as Client_Status,
				(
					 select brnd.Status FROM
					 brands brnd
					 where
						    brnd.BrandId  = cust.BrandId
					 LIMIT 1
				) as Brand_Status,
				(
					 select 
					 	( CURDATE() BETWEEN brnd.DurationFrom AND brnd.DurationTo )
					 FROM
					 brands brnd
					 where
						brnd.BrandId = cust.BrandId
					 LIMIT 1
				) as Brand_Date_Range,
				(
					 select camp.Status FROM
					 campaigns camp
					 where
						camp.CampaignId = cust.CampaignId
					 LIMIT 1
				) as Campaign_Status,				
				(
					 select 
					 	( CURDATE() BETWEEN camp.DurationFrom AND camp.DurationTo )
					 FROM
					 campaigns camp
					 where
						camp.CampaignId = cust.CampaignId
					 LIMIT 1
				) as Campaign_Date_Range,
				(
				  select chan.Status
				  from
				  channels chan
				  where
				  	  chan.ClientId   = cust.ClientId
				      and chan.BrandId    = cust.BrandId
				      and chan.CampaignId = cust.CampaignId
				  limit 1
				) as Channel_Status,
				(
				  select chan.ChannelId
				  from
				  channels chan
				  where
				  	  chan.ClientId   = cust.ClientId
				      and chan.BrandId    = cust.BrandId
				      and chan.CampaignId = cust.CampaignId
				  limit 1
				) as ChannelId,				
				(
				  select ( CURDATE() BETWEEN chan.DurationFrom AND chan.DurationTo )
				  from
				  channels chan
				  where
				  	  chan.ClientId   = cust.ClientId
				      and chan.BrandId    = cust.BrandId
				      and chan.CampaignId = cust.CampaignId
				  limit 1
				) as Channel_Date_Range,
				IFNULL((
					select sum(IFNULL(c.Balance,0))
					from
					 customer_points c
					 where
					     c.SubscriptionId = cust.SubscriptionId
					 and c.PointsId       = cust.PointsId
				),0) as Customer_Points_Balance	,
				act.PointsAction,
				act.PointsCapping,
				act.PointsLimit,
				(CURDATE() BETWEEN  act.StartDate AND act.EndDate) as ActionType_Date_Range,
				act.Status as ActionType_Status,
				( select sum(ifnull(h.Value,0))
					from
					points_log h
					where 1=1
						and h.CustomerId     = cust.CustomerId
						and h.SubscriptionId = cust.SubscriptionId
						and h.ClientId       = cust.ClientId
						and h.BrandId        = cust.BrandId
						and h.CampaignId     = cust.CampaignId
						and h.PointsId       = cust.PointsId
						and h.ActiontypeId   = act.ActiontypeId
						and h.ChannelId      IN (select chan.ChannelId
								from
								channels chan
								where
								chan.ClientId       = cust.ClientId
								and chan.BrandId    = cust.BrandId
								and chan.CampaignId = cust.CampaignId 
						)
				) as All_Log_Points,
				( select sum(ifnull(h.Value,0))
					from
					points_log h
					where 1=1
						and h.CustomerId     = cust.CustomerId
						and h.SubscriptionId = cust.SubscriptionId
						and h.ClientId       = cust.ClientId
						and h.BrandId        = cust.BrandId
						and h.CampaignId     = cust.CampaignId
						and h.PointsId       = cust.PointsId
						and h.ActiontypeId   = act.ActiontypeId
						and h.DateCreated   >= CURDATE()
						and h.ChannelId      IN (select chan.ChannelId
								from
								channels chan
								where
								chan.ClientId       = cust.ClientId
								and chan.BrandId    = cust.BrandId
								and chan.CampaignId = cust.CampaignId )
				) as Today_Log_Points				
			FROM
				customer_subscriptions cust,
				customers  cusm,
				action_type act
			WHERE
			1=1
				AND cust.ClientId       = '$client_id'
				AND cust.CustomerId     = '$customer_id'
				AND cust.CustomerId     = cusm.CustomerId
				AND cust.ClientId       = cusm.ClientId
				AND cust.PointsId       = act.PointsId
				AND cust.ClientId       = act.ClientId
				AND cust.PointsId       = '$points_id'
				AND cust.BrandId        = '$brand_id'
				AND cust.CampaignId     = '$campaign_id'
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

			//
			$allpoints = 0;
			
			//get all
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			
			//do it
			if($row != null)
			{
				$counter++;
			
				//set results
				

				/*
				tables affected:
				    customer_subscriptions (lookup)
			
				expected output
				200        OK
				401        Record Not Found (not currently subscribed)
				403        Inactive Channel (out of range, channel.DurationFrom and channel.DurationTo, or channel.Status != ACTIVE)
				405        Inactive Campaign (out of range, campaign.DurationFrom and campaign.DurationTo, or campaign.Status != ACTIVE)
				406        Inactive Brands (out of range, brands.DurationFrom and brands.DurationTo, or brands.Status != ACTIVE)
				407        Inactive Client (client.Status != ACTIVE)
				408        Inactive Customer (customers.Status != ACTIVE)
				*/			

				//402        Already expired.
				if ( $row["customer_subscriptions_status"]  != 'ACTIVE') 
				{
					$retv['result_code'] = 402;
					$retv['error_txt']   = 'Subscriptions already expired.';
					$retv["result"]      = null;
					//give it back
					return $retv;
				}
				//403        Inactive Channel (Channel.Status != ACTIVE)
				if ( $row["channel_status"]  != 'ACTIVE' or $row["channel_date_range"]  != '1')
				{
					$retv['result_code'] = 403;
					$retv['error_txt']   = 'Inactive Channel.';
					$retv["result"]      = null;
					//give it back
					return $retv;
				}
				//405        Inactive Campaign (Campaign.Status != ACTIVE)
				if ( $row["campaign_status"]  != 'ACTIVE' or $row["campaign_date_range"]  != '1')
				{
					$retv['result_code'] = 405;
					$retv['error_txt']   = 'Inactive Campaign.';
					$retv["result"]      = null;
					//give it back
					return $retv;
				}
				//406        Inactive Brand (brand.Status != ACTIVE)
				if ( $row["brand_status"]  != 'ACTIVE' or $row["brand_date_range"]  != '1')
				{
					$retv['result_code'] = 406;
					$retv['error_txt']   = 'Inactive Brand.';
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
				
				//409        No-Balance
				if (@preg_match("/^(CLAIM)$/i",$action) and $row["customer_points_balance"]  < $value)			   
				{
					$retv['result_code'] = 409;
					$retv['error_txt']   = 'Insufficient Balance.';
					$retv["result"]      = null;
					//give it back
					return $retv;
				}

                                //409        No-Balance
                                if (@preg_match("/^(DEDUCT)$/i",$action) and $row["customer_points_balance"]  < $value)
                                {
                                        $retv['result_code'] = 409;
                                        $retv['error_txt']   = 'Insufficient Balance.';
                                        $retv["result"]      = null;
                                        //give it back
                                        return $retv;
                                }


				//410        No-Balance
				if ( @preg_match("/^(ADD)$/i",$action)   and
				     ( $row["pointscapping"]  == 'DAILY' and
				       $row["pointslimit"  ]       > 0        and
				       $row["today_log_points"  ]  > $row["pointslimit"] 
				     )
				    )			   
				{
					$retv['result_code'] = 410;
					$retv['error_txt']   = 'Max Limit Reached.';
					$retv["result"]      = null;
					//give it back
					return $retv;
				}

				//good
				$pdata["subscription_id"]  = $row["subscriptionid"];
				$pdata["actiontype_id"]    = $row["actiontypeid"];
				$pdata["channel_id"]       = $row["channelid"];
				
				//1row
			}//while row
			
			
			//401        Record Not Found (not currently subscribed)
			if ($counter <= 0) {
				$retv['result_code'] = 401;
				$retv['error_txt']   = 'Record Not Found (Not currently Subscribed)';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}

				
			//add or minus-pts
			$ret_a           = $this->save_points_log($pdata);
			$ret_b           = $this->save_customer_points($pdata);
			$retv["balance"] = $ret_b["newbalance"];
			
			//nice
			$retv["status"]      = $counter;
			$retv['result_code'] = 200;
			$retv['error_txt']   = 'Success';
			$retv["result"]      = array();
				
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
		$action          = addslashes($pdata["action"]  );

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
		$balstr  = "$value";
		$valsql  = " Total = (Total    + '$value'), ";
		if(@preg_match("/(CLAIM)/i",$action))	
		{
			$balstr  = "-$value";
			$balsql  = " Used = (Used    + '$value'), ";
			$usedcol = " Used,         ";
			$usedval = " '$value',     ";
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
			$data['save_customer_points']  = $res;
		}

		debug("SQL> $query;");
		
		//get new balance
		$query      = "
			SELECT  SUM(IFNULL(balance, 0)) as balance
			FROM
				customer_points
			WHERE
				1=1
				AND SubscriptionId = '$subscription_id'
				AND PointsId       = '$points_id'
			LIMIT 1
		";
		
		debug("SQL> $query;");
		
		//run
		$newbalance = 0;
		$res = $this->conn->query($query);
		if (PEAR::isError($res)) {
			//new
			$newbalance   = 0;
		}

		//get it
		$row                = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$data['newbalance'] = $row["balance"];
		
		
		
		//give it back
		return $data;
	}


	public function lock_tab($tab=null)
	{

			//fmt
			$query = " LOCK TABLES $tab WRITE ;";
			
			//run
			$row = $this->conn->exec($query);
			
			//give it back
			return $row;
	}

	public function unlock_tab($tab=null)
	{

			//fmt
			$query = " UNLOCK TABLES ;";
			
			//run
			$row = $this->conn->exec($query);
			
			//give it back
			return $row;
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
			$value           = addslashes($pdata["value" ]  );
			$action          = addslashes($pdata["action"]  );
			$bal             = (@preg_match("/CLAIM/i",$action)) ? ("-$value") : ("$value");
			
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
				'MANUAL-$action',        
				'$bal',          
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



}
?>
