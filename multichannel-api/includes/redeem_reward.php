<?php
class RedeemReward {
                public $reward_id;
        public $conn;

        public function RedeemReward($conn, $reward_id)
        {
            $this->reward_id = $reward_id;
            $this->conn = $conn;
            $this->table_name = 'redeemed_reward';
        }

                public function isValidReward()
                {
                        $query_keys = array();

                        if (!empty($this->reward_id))
                                $query_keys[] = 'RewardId = '. $this->conn->quote($this->reward_id, 'integer');

                        $query_keys[] = "Status = 'ACTIVE'";

                        if (sizeof($query_keys) == 0)
                                $query_string = null;
                        else
                                $query_string = implode(' AND ', $query_keys);

                        $query_string .= ' LIMIT 1';


                        $res = $this->conn->extended->autoExecute('rewards_list', null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);

                        if (PEAR::isError($res)) {
							return false;
						}

                        $row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

                        if (null == $row || sizeof($row) == 0)
                        {
                                return false;
                        }

                        return true;
                }

                public function insert($client_id, $brand_id, $campaign_id, $channel_id, $user_id,
                                                                 $source, $action, $date_redeemed, $reward_config_id, $new_inventory)
                {
					$types = array('integer','integer','integer','integer','integer','integer','text','text','timestamp');


					if (empty($date_redeemed) || !preg_match(DATETIME_REGEX, $date_redeemed))
					{
							$date_redeemed = date('Y-m-d H:i:s');
					}

					$fields_values = array(
							'ClientId' => $client_id,
							'RewardId' => $this->reward_id,
							'UserId' => $user_id,
							'BrandId' => $brand_id,
							'CampaignId' => $campaign_id,
							'ChannelId' => $channel_id,
							'Source' => $source,
							'Action' => $action,
							'DateRedeemed' => $date_redeemed,
					);
		

			//if ($affected)
			//{
					$affectedRows = $this->conn->extended->autoExecute($this->table_name, $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

					if (PEAR::isError($affectedRows)) {
						return false;
					}

					$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'RedeemedId = '. $this->conn->quote($this->conn->lastInsertId($this->table_name, 'RedeemedId'), 'integer'), null, true, null);

					if (PEAR::isError($res)) {
									return false;
								}

					$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

					if (null == $row || sizeof($row) == 0)
					{
						return array("NOTINSERTED");
					}
						
		//}
					return $row;
                }

				public function retrieveRedeemed($user_id,$client_id, $brand_id, $campaign_id, $channel_id)
				{
					//$query = "SELECT RedeemedId,RewardId,UserId,Source,Action,clients.ClientId,brands.brandid,campaigns.campaignid,channels.channelid, dateredeemed, companyname,brandname, campaignname,channelname FROM redeemed_reward join brands on brands.brandid = redeemed_reward.brandid join clients on redeemed_reward.clientid = clients.clientid join campaigns on redeemed_reward.campaignid = campaigns.campaignid join channels on redeemed_reward.channelid = channels.channelid";
					$query = "SELECT reward_details.value,rewards_list.title,rewards_list.description as description,RedeemedId,redeemed_reward.RewardId,UserId,Source,Action,clients.ClientId,brands.brandid,campaigns.campaignid,channels.channelid, dateredeemed, companyname,brandname, campaignname,channelname FROM redeemed_reward join brands on brands.brandid = redeemed_reward.brandid join clients on redeemed_reward.clientid = clients.clientid join campaigns on redeemed_reward.campaignid = campaigns.campaignid join channels on redeemed_reward.channelid = channels.channelid join rewards_list on redeemed_reward.RewardId = rewards_list.rewardid join reward_details on redeemed_reward.rewardid = reward_details.rewardid";
					$query = "SELECT reward_details.value,rewards_list.title,rewards_list.description as description,RedeemedId,redeemed_reward.RewardId,UserId,Source,Action,clients.ClientId,brands.brandid,campaigns.campaignid,channels.channelid, dateredeemed, companyname,brandname, campaignname,channelname FROM redeemed_reward join brands on brands.brandid = redeemed_reward.brandid join clients on redeemed_reward.clientid = clients.clientid join campaigns on redeemed_reward.campaignid = campaigns.campaignid join channels on redeemed_reward.channelid = channels.channelid join rewards_list on redeemed_reward.RewardId = rewards_list.rewardid join reward_details on (redeemed_reward.rewardid = reward_details.rewardid AND reward_details.clientid = redeemed_reward.clientid AND reward_details.brandid = redeemed_reward.brandid AND reward_details.campaignid = redeemed_reward.campaignid AND reward_details.channelid = redeemed_reward.channelid)";
											  
					$query_keys = array();

					if (!empty($this->reward_id))
						$query_keys[] = 'redeemed_reward.RewardId = '. $this->conn->quote($this->reward_id, 'integer');
					if (!empty($user_id))
						$query_keys[] = 'redeemed_reward.UserId = '. $this->conn->quote($user_id, 'integer');
					if (!empty($client_id))
						$query_keys[] = 'redeemed_reward.ClientId = '. $this->conn->quote($client_id, 'integer');
					if (!empty($brand_id))
						$query_keys[] = 'redeemed_reward.BrandId = '. $this->conn->quote($brand_id, 'integer');
					if (!empty($campaign_id))
						$query_keys[] = 'redeemed_rewardCampaignId = '. $this->conn->quote($campaign_id, 'integer');
					if (!empty($channel_id))
						$query_keys[] = 'redeemed_rewardChannelId = '. $this->conn->quote($channel_id, 'integer');
					
					//$query_keys[] = "redeemed_rewards.Status = 'ACTIVE'";
					
					if (sizeof($query_keys) == 0)
						$query_string = null;
					else
						$query_string = implode(' AND ', $query_keys);

					$query .= " WHERE " . $query_string  . " ORDER by DateRedeemed ASC";

					//echo $query;
					$res = $this->conn->query($query);
			
					if (PEAR::isError($res)) {
						return false;
					}

					$result_array = array();
					$counter = 0;
					while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
					{				
						$result_array[] = $row;
						$counter++;
					}
					
					/*$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);*/
					if ($counter == 0)
					{
						return false;
					}

					return $result_array;
				}

				public function retrieve($user_id,$client_id, $brand_id, $campaign_id, $channel_id)
				{
					$query = "SELECT CompanyName,BrandName,RedeemedId,rewards_list.Description as Description, rewards_list.Title as Title, reward_details.RewardId as RewardId,UserId,reward_details.ClientId as ClientId, reward_details.BrandId as BrandId, ".
					         "reward_details.CampaignId as CampaignId, reward_details.ChannelId as ChannelId, DateRedeemed,Source,Action,reward_details.Value as Value FROM reward_details join redeemed_reward ".
							 "ON redeemed_reward.RewardId = reward_details.RewardId join brands on reward_details.BrandId = brands.BrandId join clients on clients.ClientId = reward_details.ClientId join rewards_list on rewards_list.RewardId = reward_details.RewardId";
											  
					$query_keys = array();

					if (!empty($this->reward_id))
						$query_keys[] = 'reward_details.RewardId = '. $this->conn->quote($this->reward_id, 'integer');
					if (!empty($user_id))
						$query_keys[] = 'UserId = '. $this->conn->quote($user_id, 'integer');
					if (!empty($client_id))
						$query_keys[] = 'reward_details.ClientId = '. $this->conn->quote($client_id, 'integer');
					if (!empty($brand_id))
						$query_keys[] = 'reward_details.BrandId = '. $this->conn->quote($brand_id, 'integer');
					if (!empty($campaign_id))
						$query_keys[] = 'reward_details.CampaignId = '. $this->conn->quote($campaign_id, 'integer');
					if (!empty($channel_id))
						$query_keys[] = 'reward_details.ChannelId = '. $this->conn->quote($channel_id, 'integer');
					
					$query_keys[] = "reward_details.Status = 'ACTIVE'";
					
					if (sizeof($query_keys) == 0)
						$query_string = null;
					else
						$query_string = implode(' AND ', $query_keys);

					$query .= " WHERE " . $query_string  . " ORDER by DateRedeemed ASC";

					echo $query;
					$res = $this->conn->query($query);
			
					if (PEAR::isError($res)) {
						return false;
					}

					$result_array = array();
					$counter = 0;
					while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
					{				
						$result_array[] = $row;
						$counter++;
					}
					
					/*$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);*/
					if ($counter == 0)
					{
						return false;
					}

					return $result_array;
				}
				
				public function subtract_inventory($reward_config_id, $new_inventory)
				{
					$table_name = 'reward_details';
					$query_keys = array();
					$query_keys[] = 'RewardConfigId = '. $this->conn->quote($reward_config_id, 'integer');
					
					if (sizeof($query_keys) == 0)
						$query_string = null;
					else
						$query_string = implode(' AND ', $query_keys);
						
					$fields_values = array();
					$fields_values['Inventory'] = $new_inventory;
					$types = array('integer');
					
					//$affectedRows = $this->conn->extended->autoExecute($table_name, $fields_values, MDB2_AUTOQUERY_UPDATE, $query_string, null, true, $types);

					$query = "UPDATE $table_name SET Inventory = GREATEST(0, Inventory - 1) WHERE Inventory > 0 ".$query_string;
					$affectedRows = $this->conn->exec($query);

					if (PEAR::isError($affectedRows)) {
						return false;
					}
					if ($affectedRows == 0)
						return false;

					return true;
				}
				
		public function getSubscriptionId($customer_id, $client_id, $brand_id, $campaign_id, $channel_id)
		{
			$query = "select SubscriptionId from reward_details join customer_subscriptions on reward_details.BrandId = customer_subscriptions.BrandId AND reward_details.ClientId = customer_subscriptions.ClientId AND reward_details.CampaignId = customer_subscriptions.CampaignId AND reward_details.ChannelId = customer_subscriptions.ChannelId ";
											  
			$query_keys = array();

			if (!empty($this->reward_id))
				$query_keys[] = 'reward_details.RewardId = '. $this->conn->quote($this->reward_id, 'integer');
			if (!empty($customer_id))
				$query_keys[] = 'customer_subscriptions.CustomerId = '. $this->conn->quote($customer_id, 'integer');
			if (!empty($client_id))
				$query_keys[] = 'customer_subscriptions.ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'customer_subscriptions.BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'customer_subscriptions.CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'customer_subscriptions.ChannelId = '. $this->conn->quote($channel_id, 'integer');
			
			$query_keys[] = "customer_subscriptions.Status = 'ACTIVE'";
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			$query .= "WHERE " . $query_string . " LIMIT 1";
			
			//echo $query;

			$res = $this->conn->query($query);
	
			if (PEAR::isError($res)) {
				return false;
			}

			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			return $row['subscriptionid'];
		}
}
?>
