<?php
class RewardList
{
        public $conn;

        public function RewardList($conn) 
        {
            $this->conn       = $conn;
            $this->table_name = 'coupon';
        }

	
	public function list_of_redeemable_rewards($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv           = array();
			$retv["rewards"]= array();
			$retv["status"] = 0;
			
			$query      = "
				SELECT  
					dtls.RewardConfigId,
					dtls.RewardId,
					dtls.PointsId,
					cust.CustomerId ,
					cust.ClientId   ,
					clnt.CompanyName as ClientName,
					rlist.RewardId  ,
					rlist.Title     ,
					rlist.Image     ,
					dtls.Inventory  ,
					IFNULL(dtls.Value,0) as Value,
					cust.SubscriptionId ,
					( 
					      ( 
						 SELECT 
						     SUM(IFNULL(custp.Balance,0)) 
						 FROM
						    customer_points custp
							WHERE 
						     custp.PointsId   = cust.PointsId
					      ) >= IFNULL(dtls.Value,0)
					) as redeemable,
					(  SELECT 
					     SUM(IFNULL(custp.Balance,0)) 
					 FROM
					    customer_points custp
						WHERE 
					     custp.PointsId   = cust.PointsId
					) as customer_total_points,
					(
						SELECT pts.Name 
						From points pts
						Where pts.PointsId = cust.PointsId
						LIMIT 1
					) as PointsSystemName,
					(
					 SELECT
					 	SUM(IFNULL(plog.Value,0))
					 FROM
					 points_log plog
					 WHERE 1=1
						 and plog.PointsId   = cust.PointsId
						 and plog.CustomerId  = cusm.CustomerId
						 and plog.ClientId    = clnt.ClientId
						 and plog.BrandId     = brnd.BrandId
						 and plog.CampaignId  = camp.CampaignId
					) as PointsSystemValue
				FROM
					customer_subscriptions cust,
					rewards_list rlist,
					reward_details dtls,
					customers  cusm,
					campaigns  camp,
					brands     brnd,
					clients    clnt
				WHERE
				1=1
					AND cust.CustomerId = '$customer_id'
					AND cust.ClientId   = '$client_id'
					AND cust.ClientId   = rlist.ClientId
					AND cust.ClientId   = dtls.ClientId
					AND cust.PointsId   = dtls.PointsId
					AND dtls.RewardId   = rlist.RewardId
					AND rlist.Status  IN ('ACTIVE')
					AND dtls.Status   IN ('ACTIVE')
					AND cusm.Status   IN ('ACTIVE')
					AND camp.Status   IN ('ACTIVE')
					AND brnd.Status   IN ('ACTIVE')
					AND cust.Status   IN ('ACTIVE')
					AND dtls.inventory  > 0
					AND cust.CustomerId = cusm.CustomerId
					AND cust.ClientId   = clnt.ClientId
					AND cust.BrandId    = brnd.BrandId
					AND cust.CampaignId = camp.CampaignId
				GROUP BY 
					cust.PointsId,
					rlist.RewardId	
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
				if( $row["redeemable"] > 0)
				{
					$result_array["rewards"][] = $row;
					$counter++;
				}
			}
			$result_array["totalrows"] = $counter;
			$result_array["status"]    = (($counter>0)?(1):(0));
			//give it back
			return ($counter == 0) ? (false) : ($result_array);
	}

	public function list_of_rewards_available($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv           = array();
			$retv["rewards"]= array();
			$retv["status"] = 0;
			
			$query      = "
				SELECT  
					dtls.RewardConfigId,
					dtls.RewardId,
					dtls.PointsId,
					cust.CustomerId ,
					cust.ClientId   ,
					clnt.CompanyName as ClientName,
					rlist.RewardId  ,
					rlist.Title     ,
					rlist.Image     ,
					dtls.Inventory  ,
					IFNULL(dtls.Value,0) as Value,
					cust.SubscriptionId ,
					( 
					      ( 
						 SELECT 
						     SUM(IFNULL(custp.Balance,0)) 
						 FROM
						    customer_points custp
							WHERE 
						     custp.PointsId   = cust.PointsId
					      ) >= IFNULL(dtls.Value,0)
					) as redeemable,
					(  SELECT 
					     SUM(IFNULL(custp.Balance,0)) 
					   FROM
					    customer_points custp
						WHERE 
					     custp.PointsId   = cust.PointsId
					) as customer_total_points,
					(
						SELECT pts.Name 
						From points pts
						Where pts.PointsId = cust.PointsId
						LIMIT 1
					) as PointsSystemName,
					(
					 SELECT
					 	SUM(IFNULL(plog.Value,0))
					 FROM
					 points_log plog
					 WHERE 1=1
						 and plog.PointsId    = cust.PointsId
						 and plog.CustomerId  = cusm.CustomerId
						 and plog.ClientId    = clnt.ClientId
						 and plog.BrandId     = brnd.BrandId
						 and plog.CampaignId  = camp.CampaignId
					) as PointsSystemValue
				FROM
					customer_subscriptions cust,
					rewards_list rlist,
					reward_details dtls,
					customers  cusm,
					campaigns  camp,
					brands     brnd,
					clients    clnt
				WHERE
				1=1
					AND cust.CustomerId = '$customer_id'
					AND cust.ClientId   = '$client_id'
					AND cust.ClientId   = rlist.ClientId
					AND cust.ClientId   = dtls.ClientId
					AND cust.PointsId   = dtls.PointsId
					AND dtls.RewardId   = rlist.RewardId
					AND rlist.Status  IN ('ACTIVE')
					AND dtls.Status   IN ('ACTIVE')
					AND cusm.Status   IN ('ACTIVE')
					AND camp.Status   IN ('ACTIVE')
					AND brnd.Status   IN ('ACTIVE')
					AND cust.Status   IN ('ACTIVE')
					AND dtls.inventory  > 0
					AND cust.CustomerId = cusm.CustomerId
					AND cust.ClientId   = clnt.ClientId
					AND cust.BrandId    = brnd.BrandId
					AND cust.CampaignId = camp.CampaignId
				GROUP BY 
					cust.PointsId,
					rlist.RewardId
					
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
				if( $row["redeemable"] <= 1 )
				{
					$result_array["rewards"][] = $row;
					$counter++;
				}
			}
			$result_array["totalrows"] = $counter;
			$result_array["status"]    = (($counter>0)?(1):(0));
			//give it back
			return ($counter == 0) ? (false) : ($result_array);
	}
 


	public function list_of_redeemed_rewards($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv           = array();
			$retv["rewards"]= array();
			$retv["status"] = 0;
			
			$query      = "
				SELECT  dtls.PointsId,
					cust.CustomerId ,
					cust.ClientId   ,
					rlist.RewardId  ,
					dtls.RewardConfigId,
					rlist.Title     ,
					rlist.Image     ,
					(
						SELECT pts.Name 
						From points pts
						Where pts.PointsId = cust.PointsId
						LIMIT 1
					) as PointsSystemName,
					rdm.DateRedeemed,
					dtls.Value
				FROM
					customer_subscriptions cust,
					rewards_list rlist,
					reward_details dtls,
					redeemed_reward rdm
				WHERE
				1=1
					AND cust.CustomerId = '$customer_id'
					AND cust.ClientId   = '$client_id'
					AND cust.ClientId   = rlist.ClientId
					AND cust.ClientId   = dtls.ClientId
					AND cust.PointsId   = dtls.PointsId
					AND dtls.RewardId   = rlist.RewardId
					AND rdm.UserId      = '$customer_id'
					AND rlist.Status  IN ('ACTIVE')
					AND dtls.Status   IN ('ACTIVE')
					AND cust.Status   IN ('ACTIVE')
					AND rlist.RewardId  = rdm.RewardId
			";
			$query      = "
				SELECT  distinct
					cust.PointsId,
					cust.CustomerId ,
					cust.ClientId   ,
					(
						select clnt.CompanyName
						from
							clients clnt
						where
							clnt.ClientId = cust.ClientId
						LIMIT 1
					) as ClientName ,
					rlist.RewardId  ,
					rlist.Title     ,
					rlist.Image,
					(
					SELECT pts.Name 
					From points pts
					Where pts.PointsId = cust.PointsId
					LIMIT 1
					) as PointsSystemName,
					rdm.DateRedeemed,
					(select dtls.Value
					from
					reward_details dtls
					where
					  dtls.RewardId = rlist.RewardId 
					  and
					  dtls.ClientId = cust.ClientId
					  and
					  dtls.PointsId = cust.PointsId
					limit 1
					) as Value
				FROM
					customer_subscriptions cust,
					rewards_list rlist,
					redeemed_reward rdm
				WHERE
				1=1
					AND cust.CustomerId = '$customer_id'
					AND cust.ClientId   = '$client_id'
					AND cust.ClientId   = rlist.ClientId
					AND cust.ClientId   = rdm.ClientId
					AND rdm.UserId      = '$customer_id'
					AND rlist.Status  IN ('ACTIVE')
					AND cust.Status   IN ('ACTIVE')
					AND rlist.RewardId  = rdm.RewardId
					AND rdm.PointsId    = cust.PointsId
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
				$result_array["rewards"][] = $row;
				$counter++;
			}
			$result_array["totalrows"] = $counter;
			$result_array["status"]    = (($counter>0)?(1):(0));
			//give it back
			return ($counter == 0) ? (false) : ($result_array);
	}
 




	public function do_redeemed_a_reward($pdata=null)
	{
		    
			//fmt
			$client_id         = addslashes($pdata["client_id"]  );
			$customer_id       = addslashes($pdata["customer_id"]);
			$reward_config_id  = addslashes($pdata["reward_config_id"]  );
			
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
				dtls.RewardConfigId,
				dtls.RewardId,
				IFNULL(dtls.Inventory,0) as Inventory,
				IFNULL(dtls.Value,0) as Value,
				( CURDATE() BETWEEN dtls.StartDate AND dtls.EndDate ) as reward_date_range,
				dtls.Status as Reward_Details_Status,
				cusm.Status as Customer_Status,
				(
				 select clnt.Status FROM
				 clients clnt
				 where
				 	clnt.ClientId = dtls.ClientId
				 LIMIT 1
				) as Client_Status,
				cust.SubscriptionId,
				cust.PointsId,
				act.ActiontypeId,
				cust.BrandId,
				cust.CampaignId,
				(
				  select chan.ChannelId
				  from
				  channels chan
				  where
				  	  chan.ClientId   = dtls.ClientId
				      and chan.BrandId    = cust.BrandId
				      and chan.CampaignId = cust.CampaignId
				  limit 1
				) as ChannelId
			FROM
				reward_details dtls,
				rewards_list rlist,
				customer_subscriptions cust,
				customers  cusm,
				action_type act
			WHERE
			1=1
				AND dtls.ClientId       = '$client_id'
				AND dtls.ClientId       = rlist.ClientId
				AND dtls.RewardId       = rlist.RewardId
				AND dtls.RewardConfigId = '$reward_config_id'
				AND rlist.Status       IN ('ACTIVE')
				AND dtls.Status        IN ('ACTIVE')
				AND dtls.ClientId       = cust.ClientId
				AND cust.CustomerId     = '$customer_id'
				AND cust.CustomerId     = cusm.CustomerId
				AND cust.ClientId       = cusm.ClientId
				AND cust.PointsId       = dtls.PointsId
				AND cust.PointsId       = act.PointsId
				AND cust.ClientId       = act.ClientId
			";
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
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
			{
				$counter++;
			
				//set results
				

				/*
				tables affected:
				    rewards_list (lookup)
				    reward_details (select, substract inventory if successful redeem)
				    customer_subscriptions (lookup)
				    redemeed_reward (insert)

				expected output
				200        OK
				401        Record Not Found (not currently subscribed)
				402        Rewards already expired. (out of range, reward_details.StartDate reward_details.EndDate or reward_details.Status != ACTIVE)
				403        Inactive Channel (out of range, channel.DurationFrom and channel.DurationTo, or channel.Status != ACTIVE)
				405        Inactive Campaign (out of range, campaign.DurationFrom and campaign.DurationTo, or campaign.Status != ACTIVE)
				406        Inactive Brands (out of range, brands.DurationFrom and brands.DurationTo, or brands.Status != ACTIVE)
				407        Inactive Client (client.Status != ACTIVE)
				408        Inactive Customer (customers.Status != ACTIVE)
				409        Insufficient Inventory (reward_details.Inventory = 0)
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
				//402        Rewards already expired. (out of range, reward_details.StartDate reward_details.EndDate or reward_details.Status != ACTIVE)
				if ( $row["reward_date_range"] <= 0 or 
				     $row["reward_details_status"]  != 'ACTIVE'
				    ) 
				{
					$retv['result_code'] = 402;
					$retv['error_txt']   = 'Rewards already expired.';
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
				//409        Insufficient Inventory (reward_details.Inventory = 0)
				if ( $row["inventory"]  <= 0)			   
				{
					$retv['result_code'] = 409;
					$retv['error_txt']   = 'Insufficient Inventory.';
					$retv["result"]      = null;
					//give it back
					return $retv;
				}

				//good
				$pdata["reward_id"]           = $row["rewardid"];
				$pdata["channel_id"]          = $row["channelid"];
				$pdata["brand_id"]            = $row["brandid"];
				$pdata["campaign_id"]         = $row["campaignid"];
				$pdata["points_id"]           = $row["pointsid"];
				$pdata["subscription_id"]     = $row["subscriptionid"];
				$pdata["value"]               = $row["value"];
				$pdata["actiontype_id"]       = $row["actiontypeid"];
				$allpoints                    = $row["value"];

				//doit
				if($doit <= 0)
				{
					$doit++;
					//set flag
					$rdata = array();
					$rdata = $this->update_inventory_flag($pdata);

					//redeemed_reward
					$tdata = array();
					$tdata = $this->save_redeemed_reward($pdata);

					//$ndata = $this->save_points_log($pdata);
				}

				//save it
				$subscription_ids[] = $row["subscriptionid"];
				$points_ids[]       = $row["pointsid"];
				$subkk              = sprintf("%s-%s",$row["subscriptionid"],$row["pointsid"]);
				$sublist[$subkk]    = $pdata;

			}//while row
			
			
			//401        Record Not Found (not currently subscribed)
			if ($counter <= 0) {
				$retv['result_code'] = 401;
				$retv['error_txt']   = 'Record Not Found (Not currently Subscribed)';
				$retv["result"]      = null;
				//give it back
				return $retv;
			}
			else
			{
				
				//minus-pts
				$this->update_customer_points($allpoints,$subscription_ids,$points_ids,$sublist);
			}
			
			//nice
			$retv["status"]      = 1;
			$retv["status"]      = $counter;
			$retv['result_code'] = 200;
			$retv['error_txt']   = 'Success';
			$retv["result"]      = array();
				
			//give it back
			return $retv;

	}



	public function update_customer_points($maxpts=0,$sids=array(),$tids=array(),$pdlist=array())
	{
		if($maxpts > 0)
		{
			$allx = @count($xlist);	
			$subsrciption_ids = @implode(",",$sids);
			$point_ids        = @implode(",",$tids);
			
			//fmt sql
			$sql = "SELECT * 
				FROM customer_points 
				
				WHERE 1=1
				     AND SubscriptionId IN ($subsrciption_ids)	
				     AND PointsId       IN ($point_ids       )	
				ORDER BY CustomerPointId
				";
			$res = $this->conn->query($sql);
			if (PEAR::isError($res)) {
				//give it back
				return false;
			}

			//get all
			$total_pts = $maxpts;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
			{
				//50 > 30
				if($total_pts > $row["balance"])
				{
				    $pts = $row["balance"];
				}
				else
				{
				    $pts = $total_pts;
				}
				//chk if done?
				$total_pts -= $pts;
				if($pts > 0)
				{
					//update it
					$this->save_customer_points(
							array(
							   'id'    => $row["customerpointid"],
							   'value' => $pts,
							)
						);	
						
					//for points log
					$kk             = sprintf("%s-%s",$row["subscriptionid"],$row["pointsid"]);
					$pdata          = $pdlist[$kk];
					$pdata["value"] = $pts;
					$this->save_points_log($pdata);
				}
			}
		}
	}
	
	public function save_customer_points($pdata=null)
	{

			//fmt
			$id              = addslashes($pdata["id"]  );
			$value           = addslashes($pdata["value"]  );
			$created_by      = addslashes($pdata["created_by"]  );
			
			
			$data       = array();
			$retv       = array();
			$data['save_customer_points'] = 0 ;
			
			//fmt sql 
			$query      = "
					UPDATE 
						customer_points 
					SET
						Balance        = (Balance - '$value'),
						Used           = (Used    + '$value'),
						UpdatedBy      = '$created_by',
						DateUpdated    = Now()
					WHERE
						CustomerPointId = '$id'
					LIMIT 1
				";
			//run
			$res = $this->conn->exec($query);
			$data['save_customer_points'] = $res;


			//give it back
			return $data;
	}


	public function save_redeemed_reward($pdata=null)
	{

			//fmt
			$client_id       = addslashes($pdata["client_id"]  );
			$customer_id     = addslashes($pdata["customer_id"]);
			$channel_id      = addslashes($pdata["channel_id"]  );
			$brand_id        = addslashes($pdata["brand_id"]   );
			$campaign_id     = addslashes($pdata["campaign_id"]);
			$reward_id       = addslashes($pdata["reward_id"]  );
			$created_by      = addslashes($pdata["created_by"]  );
			$points_id       = addslashes($pdata["points_id"]  );
			
			
			$retv   = array();
			$retv['save_redeemed_reward'] = 0;
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			INSERT INTO redeemed_reward (
				RewardId     ,
				UserId       ,
				Source       ,
				Action       ,
				DateCreated  ,
				CreatedBy    ,
				DateUpdated  ,
				UpdatedBy    ,
				ClientId     ,
				PointsId     ,
				DateRedeemed 
			)
			VALUES (
				'$reward_id'  ,
				'$customer_id'    ,
				'POINTS'     ,
				'REDEEMED'   ,
				Now()        ,
				'$created_by' ,
				Now()        ,
				'$created_by' ,
				'$client_id'  ,
				'$points_id'   ,
				Now()
			)
			";

			//run
			$row = $this->conn->exec($query);
			//get it
			$retv['RedeemedId']      = $this->conn->lastInsertId('redeemed_reward', 'RedeemedId');
			$retv['save_redeemed_reward'] = $row;

			//give it back
			return $retv;
	}


	public function update_inventory_flag($pdata=null)
	{

			//fmt
			$reward_config_id    = addslashes($pdata["reward_config_id"]  );
			$customer_id         = addslashes($pdata["customer_id"]);
			$created_by          = addslashes($pdata["created_by"]);
			
			$retv   = array();
			$retv['update_inventory_flag'] = 0;
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			UPDATE reward_details
			SET
					Inventory    = (Inventory-1),
					UpdatedBy    = '$created_by',
					DateUpdated  = Now()
			WHERE 
				RewardConfigId       = '$reward_config_id'
			";

			//run
			$row = $this->conn->exec($query);
			//get it
			$retv['update_inventory_flag'] = $row;

			//give it back
			return $retv;
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
				'REDEEMED_REWARD',        
				'-$value',          
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



}
?>
