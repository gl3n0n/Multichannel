<?php
class Reward {
		public $reward_id;
        public $conn;

        public function Reward($conn, $reward_id) 
        {
            $this->reward_id = $reward_id;
            $this->conn = $conn;
            $this->table_name = 'rewards_list';
        }
		
		public function retrieve_rlist($RewardId)
		{
			$table_name = 'rewards_list';
			$res = $this->conn->extended->autoExecute($table_name, null, MDB2_AUTOQUERY_SELECT, 'RewardId = '.$RewardId, null, true, null);
			if (PEAR::isError($res)) {
                return false;
            }
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			return $row;
		}

		public function retrieve($client_id, $brand_id, $campaign_id, $channel_id)
        {
			$query_keys = array();

			if (!empty($this->reward_id))
				$query_keys[] = 'RewardId = '. $this->conn->quote($this->reward_id, 'integer');
			if (!empty($client_id))
				$query_keys[] = 'ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'BrandId = '. $this->conn->quote($brand_id, 'integer');
	
			if (!empty($campaign_id))
				$query_keys[] = 'CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'ChannelId = '. $this->conn->quote($channel_id, 'integer');

			$query_keys[] = "Inventory > 0";
			$query_keys[] = "Status = 'ACTIVE'";
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);
				

			$res = $this->conn->extended->autoExecute("reward_details", null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);

			if (PEAR::isError($res)) {
                return false;
            }
			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
				$rlist = $this->retrieve_rlist($row['rewardid']);
				$row['title'] = $rlist['title'];
				$row['description'] = $rlist['description'];
				$row['value'] = $row['value'];
				$row['rewardconfigid'] = $row['rewardconfigid'];
				$row['currentinventory'] = $row['inventory'];
	
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
		
		public function update($client_id, $brand_id, $campaign_id, $channel_id, $updated_by,
								$date_from, $date_to, $title, $description, $image, $quantity)
		{
			$query_keys = array();

			if (!empty($this->reward_id))
				$query_keys[] = 'RewardId = '. $this->conn->quote($this->reward_id, 'integer');
			/*if (!empty($client_id))
				$query_keys[] = 'ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'ChannelId = '. $this->conn->quote($channel_id, 'integer');
			*/
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);
				
			// Prepare values
			$fields_values = array();

                        $fields_values['ClientId'] = $client_id;
$types = array('integer');
$fields_values['BrandId'] = $brand_id;
$types = array('integer');
$fields_values['CampaignId'] = $campaign_id;
$types = array('integer');
$fields_values['ChannelId'] = $channel_id;
$types = array('integer');
			$fields_values['UpdatedBy'] = $updated_by;
			$types = array('text');
			
			if (!empty($date_from))
			{
				$fields_values['DateFrom'] = $date_from;
				array_push($types,'date');
			}
			if (!empty($date_to))
			{
				$fields_values['DateTo'] = $date_to;
				array_push($types,'date');
			}
			
			if (!empty($title))
			{
				$fields_values['Title'] = $title;
				array_push($types,'text');
			}
			if (!empty($description))
			{
				$fields_values['Description'] = $description;
				array_push($types,'text');
			}
			if (!empty($image))
			{
				$fields_values['Image'] = $image;
				array_push($types,'text');
			}
			if (!empty($quantity))
			{
				$fields_values['Quantity'] = $quantity;
				array_push($types,'integer');
			}

			$affectedRows = $this->conn->extended->autoExecute($this->table_name, $fields_values, MDB2_AUTOQUERY_UPDATE, $query_string, null, true, $types);

			if (PEAR::isError($affectedRows)) {
				return false;
			}

			$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);

			if (PEAR::isError($res)) {
                return false;
            }

			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (sizeof($row) == 0)
			{
				return array("NOTFOUND");
			}

			return $row;
		}
		
		
		public function retrieveRedeemable($customer_id, $client_id, $brand_id, $channel_id, $campaign_id) 
		{
			$query = "SELECT balance,rewardconfigid, rewards_list.rewardid, inventory, rewards_list.Title as Title, rewards_list.description, BrandName, customer_subscriptions.BrandId, CompanyName, CampaignName, customer_subscriptions.campaignid, ChannelName, customer_subscriptions.channelid, customer_subscriptions.ClientId as clientid, rewards_list.Description as Description, reward_details.value, reward_details.inventory FROM customer_subscriptions JOIN reward_details on customer_subscriptions.clientid = reward_details.clientid AND customer_subscriptions.campaignid = reward_details.campaignid AND customer_subscriptions.brandid = reward_details.brandid AND customer_subscriptions.channelid = reward_details.channelid JOIN customer_points on customer_subscriptions.subscriptionid = customer_points.subscriptionid join rewards_list on rewards_list.rewardid = reward_details.rewardid join brands on customer_subscriptions.brandid = brands.brandid join clients on clients.clientid = customer_subscriptions.clientid join campaigns on campaigns.campaignid = customer_subscriptions.campaignid join channels on channels.channelid = customer_subscriptions.channelid WHERE inventory > 0 AND Balance >= reward_details.value AND reward_details.Availability <= NOW() AND  ";
//AND DateFrom <= NOW() AND DateTo >= NOW() AND inventory >= 1 AND ";
			
			$query_keys = array();
			if (!empty($client_id))
                $query_keys[] = 'customer_subscriptions.ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($customer_id))
				$query_keys[] = 'customer_subscriptions.CustomerId = '. $this->conn->quote($customer_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'customer_subscriptions.BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'customer_subscriptions.ChannelId = '. $this->conn->quote($channel_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'customer_subscriptions.CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			$query .= $query_string;
			//echo $query;

			$res = $this->conn->query($query);
			
			
			if (PEAR::isError($res)) {
                return false;
            }

			$result_array = array();
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
				$result_array[] = $row;
			}

			return $result_array;
		}
		
		public function retrieveAvailable($customer_id, $client_id, $brand_id, $channel_id, $campaign_id) 
		{
			$query = "SELECT balance,rewardconfigid, rewards_list.rewardid, inventory, rewards_list.Title as Title, rewards_list.description, BrandName, customer_subscriptions.BrandId, CompanyName, CampaignName, customer_subscriptions.campaignid, ChannelName, customer_subscriptions.channelid, customer_subscriptions.ClientId as clientid, rewards_list.Description as Description, reward_details.value FROM customer_subscriptions JOIN reward_details on customer_subscriptions.clientid = reward_details.clientid AND customer_subscriptions.campaignid = reward_details.campaignid AND customer_subscriptions.brandid = reward_details.brandid AND customer_subscriptions.channelid = reward_details.channelid JOIN customer_points on customer_subscriptions.subscriptionid = customer_points.subscriptionid join rewards_list on rewards_list.rewardid = reward_details.rewardid join brands on customer_subscriptions.brandid = brands.brandid join clients on clients.clientid = customer_subscriptions.clientid join campaigns on campaigns.campaignid = customer_subscriptions.campaignid join channels on channels.channelid = customer_subscriptions.channelid WHERE reward_details.Availability <= NOW() AND inventory >= 1 AND ";

//WHERE DateFrom <= NOW() AND DateTo >= NOW() AND inventory >= 1 AND ";
			
			$query_keys = array();

			if (!empty($client_id))
                $query_keys[] = 'customer_subscriptions.ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($customer_id))
				$query_keys[] = 'customer_subscriptions.CustomerId = '. $this->conn->quote($customer_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'customer_subscriptions.BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'customer_subscriptions.ChannelId = '. $this->conn->quote($channel_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'customer_subscriptions.CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			$query .= $query_string;
			//echo $query;

			$res = $this->conn->query($query);
			
			
			if (PEAR::isError($res)) {
                return false;
            }

			$result_array = array();
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
				$result_array[] = $row;
			}

			return $result_array;
		}
}
?>
