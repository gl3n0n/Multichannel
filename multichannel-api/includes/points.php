<?php
class Points {
        public $conn;
		public $subscription_id;

        public function Points($conn, $subscription_id) 
        {
            $this->conn = $conn;
			$this->subscription_id = $subscription_id;
        }

		public function isAllowed($brand_id, $campaign_id, $channel_id, $client_id, $points_id)
        {
			// Check if Client is valid
			$client_check_query = "SELECT * FROM clients WHERE Status = 'ACTIVE' AND ClientId = " . $this->conn->quote($client_id, 'integer') . " LIMIT 1";
			$client_check_res = $this->conn->query($client_check_query);

			if (PEAR::isError($client_check_res)) {
				return false;
			}

			$client_check_row = $client_check_res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (0 >= sizeof($client_check_row))
			{
				return array("INVALID_CLIENT");
			}
			
			$curdate = date("Y-m-d H:i:s");
			// Check if brand is valid
			$brand_check_query = "SELECT * FROM brands WHERE Status = 'ACTIVE' AND BrandId = " . $this->conn->quote($brand_id, 'integer') . 
								 " AND DurationFrom <= " . $this->conn->quote($curdate, 'timestamp') . 
								 " AND DurationTo >= " . $this->conn->quote($curdate, 'timestamp') . " LIMIT 1";

			$brand_check_res = $this->conn->query($brand_check_query);
			if (PEAR::isError($brand_check_res)) {
				return false;
			}

			$brand_check_row = $brand_check_res->fetchRow(MDB2_FETCHMODE_ASSOC);

			if (0 >= sizeof($brand_check_row))
			{
				return array("INVALID_BRAND");
			}
			
			// Check if campaign is valid
			$campaign_check_query = "SELECT * FROM campaigns WHERE Status = 'ACTIVE' AND CampaignId = " . $this->conn->quote($campaign_id, 'integer') . 
								 " AND DurationFrom <= " . $this->conn->quote($curdate, 'timestamp') . 
								 " AND DurationTo >= " . $this->conn->quote($curdate, 'timestamp') . " LIMIT 1";

			$campaign_check_res = $this->conn->query($campaign_check_query);
			if (PEAR::isError($campaign_check_res)) {
				return false;
			}

			$campaign_check_row = $campaign_check_res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (0 >= sizeof($campaign_check_row))
			{
				return array("INVALID_CAMPAIGN");
			}

			// Check if channel is valid
			$channel_check_query = "SELECT * FROM channels WHERE Status = 'ACTIVE' AND ChannelId = " . $this->conn->quote($channel_id, 'integer') . 
								 " AND DurationFrom <= " . $this->conn->quote($curdate, 'timestamp') . 
								 " AND DurationTo >= " . $this->conn->quote($curdate, 'timestamp') . " LIMIT 1";

			$channel_check_res = $this->conn->query($channel_check_query);
			if (PEAR::isError($channel_check_res)) {
				return false;
			}

			$channel_check_row = $channel_check_res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (0 >= sizeof($channel_check_row))
			{
				return array("INVALID_CHANNEL");
			}
		
			// Check if action is valid
			$points_check_query = "SELECT * FROM points WHERE Status = 'ACTIVE' AND PointsId = " . $this->conn->quote($points_id, 'integer') . 
								 " AND `From` <= " . $this->conn->quote($curdate, 'timestamp') . 
								 " AND `To` >= " . $this->conn->quote($curdate, 'timestamp') . " LIMIT 1";

			$points_check_res = $this->conn->query($points_check_query);
			if (PEAR::isError($points_check_res)) {
				return false;
			}

			$points_check_row = $points_check_res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (0 >= sizeof($points_check_row))
			{
				return array("INVALID_POINTS");
			}
            /*$query_keys = array();
            
            if (!empty($channel_id))
            {
                $tbl = 'channels';
                $query_keys[] = 'ChannelId = '. $this->conn->quote($channel_id, 'integer');
            }
            else if (!empty($campaign_id))
            {
                $tbl = 'campaigns';
                $query_keys[] = 'CampaignId = '. $this->conn->quote($campaign_id, 'integer');
            }
            else if (!empty($brand_id))
            {
                $tbl = 'brands';
                $query_keys[] = 'BrandId = '.$this->conn->quote($brand_id, 'integer');
            }
			else
            {
                $tbl = 'clients';
                $query_keys[] = 'ClientId = '.$this->conn->quote($client_id, 'integer');
            }

            $query_keys[] = "Status = 'ACTIVE'";
            $query_keys[] = '`DurationFrom` <= '. $this->conn->quote($curdate, 'timestamp');
            $query_keys[] = '`DurationTo` >= '. $this->conn->quote($curdate, 'timestamp');

            $result_types = array(
                    'count(1) as cnt' => 'integer'
                );

            $query_string = implode(' AND ', $query_keys);
            $res = $this->conn->extended->autoExecute($tbl, null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, $result_types);
            if (PEAR::isError($res)) {
                return false;
            }
            $cnt = 0;
            while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
                $cnt = $row['cnt'];
            }
            if ($cnt == 0)
                return array("INVALID_PROMO");
            else
                return true;
			*/
			return true;
        }

		public function inquire($customer_id, $brand_id, $campaign_id, $channel_id, $client_id)
		{
			$query = "SELECT CustomerPointId, customer_subscriptions.SubscriptionId, Balance,Used,Total,CustomerId,customer_subscriptions.BrandId as BrandId,CampaignId," . 
					 "ChannelId,customer_subscriptions.Status as Status FROM customer_points join customer_subscriptions on " . 
					 "customer_points.SubscriptionId = customer_subscriptions.SubscriptionId join brands on customer_subscriptions.BrandId = brands.BrandId";
											  
			$query_keys = array();

			if (!empty($this->subscription_id))
				$query_keys[] = 'customer_subscriptions.SubscriptionId = '. $this->conn->quote($this->subscription_id, 'integer');
			if (!empty($customer_id))
				$query_keys[] = 'CustomerId = '. $this->conn->quote($customer_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'customer_subscriptions.BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'ChannelId = '. $this->conn->quote($channel_id, 'integer');
			if (!empty($client_id))
				$query_keys[] = 'customer_subscriptions.ClientId = '. $this->conn->quote($client_id, 'integer');
			
			$query_keys[] = "customer_subscriptions.Status = 'ACTIVE'";
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			$query .= " WHERE " . $query_string;
				

			$res = $this->conn->query($query);

			if (PEAR::isError($res)) {
				return false;
			}

			$total_points = 0;
			$i = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
				$i++;
				$total_points = $total_points + $row['balance'];
			}

			if ($i == 0 )
			{
				return false;
			}
			$result_arr['balance'] = $total_points;

			return $result_arr;
		}
		
		public function getPointIdInfo($pointlogid)
		{
			$query1 = "SELECT a.value as value FROM points a, points_log b ";
			$query_keys1 = array();
			$query_keys1[] = 'a.PointsId = b.PointsId';
			$query_keys1[] = 'b.PointLogId = '. $this->conn->quote($pointlogid, 'integer');
			if (sizeof($query_keys1) == 0)
				$query_string1 = null;
			else
				$query_string1 = implode(' AND ', $query_keys1);
				
			$query1 .= " WHERE " . $query_string1  . " LIMIT 1";
			
			$res = $this->conn->query($query1);

			if (PEAR::isError($res)) {
				return false;
			}

			$row1 = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

			if (sizeof($row1) == 0)
			{
				return false;
			}
			
			$points = $row1["value"];
			return $points;
			
		}

		public function add($customer_id, $brand_id, $campaign_id, $channel_id, $action, $points_id, $client_id)
		{
			// Check capping and limit
			$query1 = "SELECT PointCapping, PointsLimit, Value FROM points ";
			
			$query_keys1 = array();

			if (!empty($points_id))
				$query_keys1[] = 'PointsId = '. $this->conn->quote($points_id, 'integer');
			if (!empty($client_id))
				$query_keys1[] = 'ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($brand_id))
				$query_keys1[] = 'BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($campaign_id))
				$query_keys1[] = 'CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			if (!empty($channel_id))
				$query_keys1[] = 'ChannelId = '. $this->conn->quote($channel_id, 'integer');
			/*if (!empty($action))
				$query_keys1[] = 'PointAction = '. $this->conn->quote($action, 'text');*/
			
			$query_keys1[] = "Status = 'ACTIVE'";
			
			if (sizeof($query_keys1) == 0)
				$query_string1 = null;
			else
				$query_string1 = implode(' AND ', $query_keys1);

			$query1 .= " WHERE " . $query_string1  . " LIMIT 1";
			$res = $this->conn->query($query1);

			if (PEAR::isError($res)) {
				return false;
			}

			$row1 = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

			if (sizeof($row1) == 0)
			{
				return false;
			}
			
			$points = $row1["value"];
			// So if the data exists then continue
			if ($row1["pointcapping"] == "DAILY")
			{
				// Do shit
				$query_cap = "SELECT SUM(Value) as Total_For_Today FROM points join points_log on points.PointsId = points_log.PointsId ".
				             "WHERE DATE(points_log.DateCreated)= CURDATE() and SubscriptionId = ". $this->conn->quote($this->subscription_id, 'integer') . 
							 " and points_log.PointsId = " . $this->conn->quote($points_id, 'integer');
							 

				$res_cap = $this->conn->query($query_cap);
	
				if (PEAR::isError($res_cap)) {
					return false;
				}

				$check_point = $this->inquire($customer_id, $brand_id, $campaign_id, $channel_id, $client_id);
				$row_cap = $res_cap->fetchRow(MDB2_FETCHMODE_ASSOC);
				if ($row1['pointslimit'] != 0)
				{
					if($row_cap && ($row_cap["total_for_today"] + $points)> ($row1['pointslimit']))
					{
						return array("MAX");
					}
				}
			}
			else
			{
				$query_cap = "SELECT SUM(Value) as TotalPoints FROM points join points_log on points.PointsId = points_log.PointsId ".
				             "WHERE SubscriptionId = ". $this->conn->quote($this->subscription_id, 'integer') . 
							 " and points_log.PointsId = " . $this->conn->quote($points_id, 'integer');

				$res_cap = $this->conn->query($query_cap);
	
				if (PEAR::isError($res_cap)) {
					return false;
				}

				$check_point = $this->inquire($customer_id, $brand_id, $campaign_id, $channel_id, $client_id);
				$row_cap = $res_cap->fetchRow(MDB2_FETCHMODE_ASSOC);
				//print_r($row_cap);
				if ($row1['pointslimit'] != 0)
				{
					if($row_cap && ($row_cap["totalpoints"] + $points)> ($row1['pointslimit']))
					{
						return array("MAX2");
					}
				}
			}

			// Update points
			$types = array('integer','integer','integer','integer','integer','date');
			$currdate = date('Y-m-d H:i:s');

			$fields_values = array(
					'CustomerId' => $customer_id,
					'SubscriptionId' => $this->subscription_id,
					'ClientId' => $client_id,
					'BrandId' => $brand_id,
					'CampaignId' => $campaign_id,
					'ChannelId' => $channel_id,
					'PointsId' => $points_id,
					'DateCreated' => $currdate
			);

			$affectedRows = $this->conn->extended->autoExecute("points_log", $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

			if (PEAR::isError($affectedRows)) {
					return false;
			}

			$res = $this->conn->extended->autoExecute("points_log", null, MDB2_AUTOQUERY_SELECT, 'PointLogId = '. $this->conn->quote($this->conn->lastInsertId("points_log", 'PointLogId'), 'integer'), null, true, null);

			if (PEAR::isError($res)) {
				return false;
			}

			$row2 = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

			if (null == $row2 || sizeof($row2) == 0)
			{
					return array("NOTINSERTED");
			}

			// NOW, UPDATE customer_points because it should have been inserted on Campaign Reg!
			if ($points >= 0)
				$query3 = "UPDATE customer_points set Balance = Balance + $points, Total = Total + $points";
			//else
				//$query3 = "UPDATE customer_points set Balance = Balance + $points, Used = Used + $points";

			if (!empty($this->subscription_id))
				$query_keys3[] = 'SubscriptionId = '. $this->conn->quote($this->subscription_id, 'integer');
			
			if (sizeof($query_keys3) == 0)
				$query_string3 = null;
			else
				$query_string3 = implode(' AND ', $query_keys3);

			
			$query3 .= " WHERE " . $query_string3;

			$res3 = $this->conn->query($query3);

			if (PEAR::isError($res3)) {
				return false;
			}
			$row3 = $res3->fetchRow(MDB2_FETCHMODE_ASSOC);
			// TODO: Must check here if really inserted.
			//print_r($row3);
			/*if (sizeof($row3) == 0)
			{
				return false;
			}*/

			$check_point = $this->inquire($customer_id, $brand_id, $campaign_id, $channel_id, $client_id);
			$success_array = array('balance' => $check_point['balance']);
			return $success_array;
			
		}
		
		public function delPointlogId($pointlogid)
		{
			$query1 = "DELETE FROM points_log ";
			$query_keys1 = array();
			$query_keys1[] = 'PointLogId = '. $this->conn->quote($pointlogid, 'integer');
			if (sizeof($query_keys1) == 0)
				$query_string1 = null;
			else
				$query_string1 = implode(' AND ', $query_keys1);
				
			$query1 .= " WHERE " . $query_string1  . " LIMIT 1";
			
			$res = $this->conn->query($query1);

			if (PEAR::isError($res)) {
				return false;
			}
		}
		
		public function subtractClaimPoints($points,$customer_id, $brand_id, $campaign_id, $channel_id, $client_id)
		{
			$check_point = $this->inquire($customer_id, $brand_id, $campaign_id, $channel_id, $client_id);
			if(!$check_point['balance'] || ($check_point['balance'] + $points) < 0)
			{
				return array("MIN");
			}

			$query = "UPDATE customer_points set Balance = Balance $points, Total = Total - $points";
			if (!empty($this->subscription_id))
				$query_keys[] = 'SubscriptionId = '. $this->conn->quote($this->subscription_id, 'integer');
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			
			$query .= " WHERE " . $query_string;
			$res = $this->conn->query($query);

			if (PEAR::isError($res)) {
				return false;
			}
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			// TODO: Must check here if really inserted.
			// remove points log

			$success_array = array('balance' => $check_point['balance'] - $points);
			return $success_array;
		}
		
		public function subtract($pointlogid, $points)
		{
			$check_point = $this->inquire($customer_id, $brand_id, $campaign_id, $channel_id, $client_id);

			if(!$check_point['balance'] || ($check_point['balance'] + $points) < 0)
			{
				return array("MIN");
			}

			$query = "UPDATE customer_points set Balance = Balance - $points, Total = Total - $points";
			
			$query_keys = array();

			if (!empty($this->subscription_id))
				$query_keys[] = 'SubscriptionId = '. $this->conn->quote($this->subscription_id, 'integer');
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			
			$query .= " WHERE " . $query_string;
			// echo $query;
			// exit();
			$res = $this->conn->query($query);

			if (PEAR::isError($res)) {
				return false;
			}
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			// TODO: Must check here if really inserted.
			// remove points log
			$this->delPointlogId($pointlogid);

			$success_array = array('balance' => $check_point['balance'] - $points);
			return $success_array;
		}
    }
?>

