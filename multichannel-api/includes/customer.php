<?php
class Customer {
        public $customer_id;
        public $conn;

        public function Customer($conn, $customer_id)  
        {
            $this->customer_id = $customer_id;
            $this->conn = $conn;
            $this->table_name = 'customers';
        }

		public function isAllowed($brand_id, $campaign_id, $channel_id, $client_id)
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

			return true;
        }

		/*public function isAllowed($brand_id, $campaign_id, $channel_id, $client_id)
        {
            $query_keys = array();
            $curdate = date("Y-m-d H:i:s");
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
            //$query_keys[] = '`DurationFrom` <= '. $this->conn->quote($curdate, 'text');
            $query_keys[] = '`DurationTo` >= '. $this->conn->quote($curdate, 'text');

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
                return false;
            else
                return true;
        }*/

		public function add($first_name, $middle_name, $last_name, $gender, $birthdate,
							$address, $status, $fb_id, $twitter_handle, $email, $contact_number,$client_id)
		{
			$curdate = date('Y-m-d H:i:s');
			$types = array('text','text','text','text','text','text','text','text','text','text','timestamp', 'integer');

			$fields_values = array(
				'FirstName' => $first_name,
				'LastName' => $last_name,
				'MiddleName' => $middle_name,
				'Gender' => $gender,
				'ContactNumber' => $contact_number,
				'Address' => $address,
				'Email' => $email,
				'Status' => $status,
				'FBId' => $fb_id,
				'TwitterHandle' => $twitter_handle,
				'DateCreated' => $curdate,
				'ClientId' => $client_id,
			);

			$select_query = "SELECT * FROM customers WHERE Email = " . $this->conn->quote($email) . " OR FBId = " . $this->conn->quote($fb_id);
			$select_res = $this->conn->query($select_query);
			if (PEAR::isError($select_res)){
				return false;
			}

			$row_select = $select_res->fetchRow(MDB2_FETCHMODE_ASSOC);

			if ($row_select){
				if ($row_select["fbid"] == $fb_id)
				{
					return array("EXISTS_FBID");
				}
				else
				{
					return array("EXISTS_EMAIL");
				}
			}

			$affectedRows = $this->conn->extended->autoExecute($this->table_name, $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

			if (PEAR::isError($affectedRows)) {
				return false;
			}

			$cust_id = $this->conn->quote($this->conn->lastInsertId($this->table_name, 'CustomerId'), 'integer');
			$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'CustomerId = '. $cust_id, null, true, null);
				
			if (PEAR::isError($res)) {
				return false;
			}

			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (sizeof($row) == 0)
			{
				return false;
			}

			return $row;
		}
		
		public function subscribe($channel_id, $campaign_id, $brand_id, $status, $client_id)
		{
			$tbl = "customer_subscriptions";
			$curdate = date('Y-m-d H:i:s');
			$types = array('integer','integer','integer','integer','text','integer');

			$fields_values = array(
				'CustomerId' => $this->customer_id,
				'ChannelId' => $channel_id,
				'CampaignId' => $campaign_id,
				'BrandId' => $brand_id,
				'ClientId' => $client_id,
				'Status' => $status,
				'DateCreated' => $curdate,
			);
			
			$isExisting = $this->conn->extended->autoExecute($tbl, null, MDB2_AUTOQUERY_SELECT, 
															 'CustomerId = ' . $this->conn->quote($this->customer_id, 'integer') .
															 ' AND ChannelId = ' . $this->conn->quote($channel_id, 'integer') .
															 ' AND CampaignId = ' . $this->conn->quote($campaign_id, 'integer') .
															 ' AND ClientId = ' . $this->conn->quote($client_id, 'integer') .
															 ' AND BrandId = ' . $this->conn->quote($brand_id, 'integer'),
															 null, true, null);

			if (PEAR::isError($isExisting)) {
				return false;
			}
			
			$exists = $isExisting->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (sizeof($exists) > 0)
			{
				return array("EXISTS");
			}

			$affectedRows = $this->conn->extended->autoExecute($tbl, $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

			if (PEAR::isError($affectedRows)) {
				return false;
			}

			$subs_id = $this->conn->quote($this->conn->lastInsertId($tbl, 'SubscriptionId'), 'integer');
			$res = $this->conn->extended->autoExecute($tbl, null, MDB2_AUTOQUERY_SELECT, 'SubscriptionId = '. $subs_id, null, true, null);
				
			if (PEAR::isError($res)) {
				return false;
			}
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (sizeof($row) == 0)
			{
				return false;
			}
			
			$types_cust_pts = array('integer', 'timestamp');
			$fields_values_cust_pts = array(
				'SubscriptionId' => $subs_id,
				'DateCreated' => $curdate,
			);
			
			$affectedRows_cust_pts = $this->conn->extended->autoExecute("customer_points", $fields_values_cust_pts, MDB2_AUTOQUERY_INSERT, null, null, true, $types_cust_pts);

			if (PEAR::isError($affectedRows_cust_pts)) {
				return false;
			}
			
			$isInsertedInCustPoints = $this->conn->extended->autoExecute("customer_points", null, MDB2_AUTOQUERY_SELECT, 
															 'SubscriptionId = ' . $subs_id,
															 null, true, null);
			if (PEAR::isError($isInsertedInCustPoints)) {
				return false;
			}
			
			$the_cust_points_entry = $isInsertedInCustPoints->fetchRow(MDB2_FETCHMODE_ASSOC);
			
			if ($subs_id != $the_cust_points_entry["subscriptionid"])
			{
				return false;
			}

			return $row;
		}

		public function retrieve($fb_id, $email)
        {
			$query_keys = array();

			if (!empty($this->customer_id))
				$query_keys[] = 'CustomerId = '. $this->conn->quote($this->customer_id, 'integer');
			if (!empty($fb_id))
				$query_keys[] = 'FBId = '. $this->conn->quote($fb_id, 'text');
			if (!empty($email))
				$query_keys[] = 'Email = '. $this->conn->quote($email, 'text');

			$query_keys[] = "Status = 'ACTIVE'";
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			$query_string .= ' LIMIT 1';

			$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);
			
			if (PEAR::isError($res)) {
                return false;
            }

			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (sizeof($row) == 0)
			{
				return false;
			}

			return $row;
		}

		public function update($first_name, $middle_name, $last_name, $gender, $birthdate,
							   $address, $status, $fb_id, $twitter_handle, $email, $contact_number, $client_id)
		{
			$query_keys = array();

			if (!empty($this->customer_id))
				$query_keys[] = 'CustomerId = '. $this->conn->quote($this->customer_id, 'integer');
			if (!empty($client_id))
				$query_keys[] = 'ClientId = '. $this->conn->quote($client_id, 'integer');
			
			//$query_keys[] = "Status = 'ACTIVE'";
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			$query_string .= ' LIMIT 1';

			// Prepare values
			$fields_values = array();
			$table_fields = array();
			$types = array();

			if (!empty($fb_id))
			{
				$fields_values['FBId'] = $fb_id;
				array_push($types,'text');
				array_push($table_fields,'FBId');
			}
			if (!empty($email))
			{
				$fields_values['Email'] = $email;
				array_push($types,'text');
				array_push($table_fields,'Email');
			}
			if (!empty($first_name))
			{
				$fields_values['FirstName'] = $first_name;
				array_push($types,'text');
				array_push($table_fields,'FirstName');
			}
			if (!empty($last_name))
			{
				$fields_values['LastName'] = $last_name;
				array_push($types,'text');
				array_push($table_fields,'LastName');
			}
			if (!empty($middle_name))
			{
				$fields_values['MiddleName'] = $middle_name;
				array_push($types,'text');
				array_push($table_fields,'MiddleName');
			}
			if (!empty($gender))
			{
				$fields_values['Gender'] = $gender;
				array_push($types,'text');
				array_push($table_fields,'Gender');
			}
			if (!empty($birthdate))
			{
				$fields_values['Birthdate'] = $birthdate;
				array_push($types,'text');
				array_push($table_fields,'Birthdate');
			}
			
			if (!empty($address))
			{
				$fields_values['Address'] = $address;
				array_push($types,'text');
				array_push($table_fields,'Address');
			}
			if (!empty($status))
			{
				$fields_values['Status'] = $status;
				array_push($types,'text');
				array_push($table_fields,'Status');
			}
			if (!empty($fb_id))
			{
				$fields_values['FBId'] = $fb_id;
				array_push($types,'text');
				array_push($table_fields,'FBId');
			}
			if (!empty($twitter_handle))
			{
				$fields_values['TwitterHandle'] = $twitter_handle;
				array_push($types,'text');
				array_push($table_fields,'TwitterHandle');
			}
			if (!empty($contact_number))
			{
				$fields_values['ContactNumber'] = $contact_number;
				array_push($types,'text');
				array_push($table_fields,'ContactNumber');
			}
            if (!empty($status))
			{
				$fields_values['Status'] = $fb_id;
				array_push($types,'text');
				array_push($table_fields,'Status');
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
		
		public function retrieve_subscriptions($client_id, $brand_id, $channel_id, $campaign_id, $client_id)
        {
			$query = "SELECT CompanyName, BrandName, ChannelName, CampaignName, clients.ClientId, 	clients.CompanyName as company_name, campaigns.description as description, campaigns.CampaignId as CampaignId, " . 
					 "brands.BrandId as BrandId, channels.ChannelId as ChannelId FROM customer_subscriptions join brands on brands.BrandId = customer_subscriptions.BrandId " . 
					 "join clients on clients.ClientId = brands.ClientId " . 
					 "join campaigns on campaigns.CampaignId = customer_subscriptions.CampaignId " . 
					 "join channels on channels.ChannelId = customer_subscriptions.ChannelId";

			$query_keys = array();
			if (!empty($client_id))
                $query_keys[] = 'customer_subscriptions.ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($this->customer_id))
				$query_keys[] = 'customer_subscriptions.CustomerId = '. $this->conn->quote($this->customer_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'customer_subscriptions.BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'customer_subscriptions.ChannelId = '. $this->conn->quote($channel_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'customer_subscriptions.CampaignId = '. $this->conn->quote($campaign_id, 'integer');

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

			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
				$result_array[] = $row;
				$counter++;
			}

			if ($counter == 0)
			{
				return false;
			}

			return $result_array;
		}
}
?>

