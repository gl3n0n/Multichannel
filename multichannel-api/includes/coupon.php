<?php
class Coupon {
        public $coupon_id;
        public $conn;
		public $default_code_length = 15;

        public function Coupon($conn, $coupon_id) 
        {
            $this->coupon_id = $coupon_id;
            $this->conn = $conn;
            $this->table_name = 'coupon';
        }

		public function retrieve($client_id, $brand_id, $campaign_id, $channel_id, $status)
        {
			$query_keys = array();

			if (!empty($this->coupon_id))
				$query_keys[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');
			if (!empty($client_id))
				$query_keys[] = 'ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'ChannelId = '. $this->conn->quote($channel_id, 'integer');

			if ($status != "PENDING")
			{
				$query_keys[] = "Status = 'ACTIVE'";
			}
			else
			{
				$query_keys[] = "Status = 'PENDING'";
			}

			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);
				
			$query_string = $query_string . " ORDER BY CouponId ASC";

			$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);
			
			if (PEAR::isError($res)) {
                return false;
            }

			/*$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (sizeof($row) == 0)
			{
				return false;
			}

			return $row;*/
			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {				
				// get details
				$query2 = "SELECT coupon_mapping.ClientId as ClientId,brands.BrandId as BrandId, campaigns.CampaignId as CampaignId, channels.ChannelId as ChannelId,BrandName,ChannelName,CampaignName from coupon join coupon_mapping on coupon.CouponId = coupon_mapping.CouponId join brands on brands.BrandId = coupon_mapping.BrandId join campaigns on campaigns.CampaignId = coupon_mapping.CampaignId join channels on channels.ChannelId = coupon_mapping.ChannelId WHERE coupon.CouponId = " . $row["couponid"];
				$res2 = $this->conn->query($query2);
				
				if (PEAR::isError($res2))
				{
					return false;
				}
				$tmp_brands = array();
				$tmp_channels = array();
				$tmp_campaigns = array();
				while ($row2 = $res2->fetchRow(MDB2_FETCHMODE_ASSOC))
				{
					$tmp_brands[] = $row2["brandname"];
					$tmp_channels[] = $row2["channelname"];
					$tmp_campaigns[] = $row2["campaignname"];
				}
				// get unique
				$tmp_brands = array_unique($tmp_brands);
				$tmp_channels = array_unique($tmp_channels);
				$tmp_campaigns = array_unique($tmp_campaigns);
				$tmp_brands_str = "";
				$tmp_channels_str = "";
				$tmp_campaigns_str = "";
				
				foreach ($tmp_brands as &$tmp_brand) {
					$tmp_brands_str = $tmp_brands_str . $tmp_brand . ", ";
				}
				
				foreach ($tmp_channels as &$tmp_channel) {
					$tmp_channels_str = $tmp_channels_str . $tmp_channel . ", ";
				}
				
				foreach ($tmp_campaigns as &$tmp_campaign) {
					$tmp_campaigns_str = $tmp_campaigns_str . $tmp_campaign . ", ";
				}

				// remove trailing commas
				$tmp_brands_str = rtrim($tmp_brands_str, ", ");
				$tmp_channels_str = rtrim($tmp_channels_str, ", ");
				$tmp_campaigns_str = rtrim($tmp_campaigns_str, ", ");
				
				$row["brandnames"] = $tmp_brands_str;
				$row["channelnames"] = $tmp_channels_str;
				$row["campaignnames"] = $tmp_campaigns_str;
				$result_array[] = $row;
				$counter++;
			}

			if ($counter == 0)
			{
				return false;
			}

			return $result_array;
		}
		
		public function retrievePendingEdit($client_id, $brand_id, $campaign_id, $channel_id, $status)
        {
			$query_keys = array();

			if (!empty($this->coupon_id))
				$query_keys[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');
			if (!empty($client_id))
				$query_keys[] = 'ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'ChannelId = '. $this->conn->quote($channel_id, 'integer');

			$query_keys[] = "Status = 'ACTIVE'";
			$query_keys[] = "edit_flag = '1'";

			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);
				
			$query_string = $query_string . " ORDER BY CouponId ASC";

			$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);
			
			if (PEAR::isError($res)) {
                return false;
            }

			/*$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (sizeof($row) == 0)
			{
				return false;
			}

			return $row;*/
			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {				
				// get details
				$query2 = "SELECT coupon_mapping.ClientId as ClientId,brands.BrandId as BrandId, campaigns.CampaignId as CampaignId, channels.ChannelId as ChannelId,BrandName,ChannelName,CampaignName from coupon join coupon_mapping on coupon.CouponId = coupon_mapping.CouponId join brands on brands.BrandId = coupon_mapping.BrandId join campaigns on campaigns.CampaignId = coupon_mapping.CampaignId join channels on channels.ChannelId = coupon_mapping.ChannelId WHERE coupon.CouponId = " . $row["couponid"];
				$res2 = $this->conn->query($query2);
				
				if (PEAR::isError($res2))
				{
					return false;
				}
				$tmp_brands = array();
				$tmp_channels = array();
				$tmp_campaigns = array();
				while ($row2 = $res2->fetchRow(MDB2_FETCHMODE_ASSOC))
				{
					$tmp_brands[] = $row2["brandname"];
					$tmp_channels[] = $row2["channelname"];
					$tmp_campaigns[] = $row2["campaignname"];
				}
				// get unique
				$tmp_brands = array_unique($tmp_brands);
				$tmp_channels = array_unique($tmp_channels);
				$tmp_campaigns = array_unique($tmp_campaigns);
				$tmp_brands_str = "";
				$tmp_channels_str = "";
				$tmp_campaigns_str = "";
				
				foreach ($tmp_brands as &$tmp_brand) {
					$tmp_brands_str = $tmp_brands_str . $tmp_brand . ", ";
				}
				
				foreach ($tmp_channels as &$tmp_channel) {
					$tmp_channels_str = $tmp_channels_str . $tmp_channel . ", ";
				}
				
				foreach ($tmp_campaigns as &$tmp_campaign) {
					$tmp_campaigns_str = $tmp_campaigns_str . $tmp_campaign . ", ";
				}

				
				// remove trailing commas
				$tmp_brands_str = rtrim($tmp_brands_str, ", ");
				$tmp_channels_str = rtrim($tmp_channels_str, ", ");
				$tmp_campaigns_str = rtrim($tmp_campaigns_str, ", ");
				
				$row["brandnames"] = $tmp_brands_str;
				$row["channelnames"] = $tmp_channels_str;
				$row["campaignnames"] = $tmp_campaigns_str;
				$result_array[] = $row;
				$counter++;
			}

			if ($counter == 0)
			{
				return false;
			}

			return $result_array;
		}

		public function retrieve_generated_specific($generated_coupon_id)
		{
			$query_keys = array();

			if (!empty($generated_coupon_id))
				$query_keys[] = 'GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer');
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);
				
			$res = $this->conn->extended->autoExecute("generated_coupons", null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);
			
			if (PEAR::isError($res)) {
                return false;
            }

			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (sizeof($row) == 0)
			{
				return false;
			}
			$result_array = array();
			$result_array[] = $row;

			return $result_array;
		}

		public function retrieve_generated()
        {
			$query_keys = array();

			if (!empty($this->coupon_id))
				$query_keys[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');

			$query_keys[] = "Status = 'PENDING'";

			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);
				
			$query_string = $query_string . " ORDER BY GeneratedCouponId ASC";

			$res = $this->conn->extended->autoExecute("generated_coupons", null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);
			
			if (PEAR::isError($res)) {
                return false;
            }

			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {				
				// query options on claiming
				$query2 = "SELECT coupon_mapping.CouponMappingId,coupon_mapping.ClientId as ClientId,brands.BrandId as BrandId, campaigns.CampaignId as CampaignId, channels.ChannelId as ChannelId,BrandName,ChannelName,CampaignName from generated_coupons join coupon_mapping on generated_coupons.CouponId = coupon_mapping.CouponId join brands on brands.BrandId = coupon_mapping.BrandId join campaigns on campaigns.CampaignId = coupon_mapping.CampaignId join channels on channels.ChannelId = coupon_mapping.ChannelId WHERE GeneratedCouponId = " . $row["generatedcouponid"];
				$res2 = $this->conn->query($query2);
				
				if (PEAR::isError($res2))
				{
					return false;
				}

				$options = array();
				while ($row2 = $res2->fetchRow(MDB2_FETCHMODE_ASSOC))
				{
					$options[] = $row2;
				}
			
				$row["redeem_options"] = $options;
				$result_array[] = $row;
				$counter++;
			}

			if ($counter == 0)
			{
				return false;
			}

			return $result_array;
		}
		
		public function edit_retrieve_generated()
        {
			$query_keys = array();

			if (!empty($this->coupon_id))
				$query_keys[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');


			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);
				
			$query_string = $query_string . " ORDER BY GeneratedCouponId ASC";

			$res = $this->conn->extended->autoExecute("generated_coupons", null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);
			
			if (PEAR::isError($res)) {
                return false;
            }

			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
			{
				$options[] = $row['code'];
			}
			
			return $options;
		}

		public function update($client_id, $brand_id, $campaign_id, $channel_id, $code,
							   $type, $type_id, $source, $image, $quantity, $limit_per_user, $expiry_date, $status, $edit_flag)
		{
			$query_keys = array();

			if (!empty($this->coupon_id))
				$query_keys[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');
			if (!empty($client_id))
				$query_keys[] = 'ClientId = '. $this->conn->quote($client_id, 'integer');
			if (!empty($brand_id))
				$query_keys[] = 'BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'CampaignId = '. $this->conn->quote($campaign_id, 'integer');
			if (!empty($channel_id))
				$query_keys[] = 'ChannelId = '. $this->conn->quote($channel_id, 'integer');
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);
				
			// Prepare values
			$fields_values = array();

			$fields_values['UpdatedBy'] = $updated_by;
			$types = array('text');
			
			if (!empty($type))
			{
				$fields_values['Type'] = $type;
				array_push($types,'text');
			}
			if (!empty($type_id))
			{
				$fields_values['TypeId'] = $type_id;
				array_push($types,'text');
			}
			
			if (!empty($source))
			{
				$fields_values['Source'] = $source;
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
			if (!empty($limit_per_user))
			{
				$fields_values['LimitPerUser'] = $limit_per_user;
				array_push($types,'text');
			}
			if (!empty($expiry_date))
			{
				$fields_values['ExpiryDate'] = $expiry_date;
				array_push($types,'date');
			}
			if (!empty($code))
			{
				$fields_values['Code'] = $code;
				array_push($types,'text');
			}
			if (!empty($status))
			{
				$fields_values['Status'] = $status;
				array_push($types,'text');
			}
			
			$fields_values['edit_flag'] = '0';
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
		
		public function insert($client_id, $brand_id, $campaign_id, $channel_id, $created_by, $code,
							   $type, $type_id, $source, $image, $quantity, $limit_per_user, $expiry_date, $status)
		{
			$curdate = date('Y-m-d H:i:s');
			$types = array('integer','integer','integer','integer','text','text','text','text','text','text', 'integer', 'integer', 'timestamp','text','timestamp','text','timestamp','text');

			if ($status != "ACTIVE")
			{
				$status = "PENDING";
			}

			$fields_values = array(
				'ClientId' => $client_id,
				'BrandId' => $brand_id,
				'CampaignId' => $campaign_id,
				'ChannelId' => $channel_id,
				'Code' => $code,
				'Type' => $type,
				'TypeId' => $type_id,
				'Source' => $source,
				'Image' => $image,
				'Quantity' => $quantity,
				'LimitPerUser' => $limit_per_user,
				'ExpiryDate' => $expiry_date,
				'Status' => $status,
				'DateCreated' => $curdate,
				'CreatedBy' => $created_by,
				'DateUpdated' => $curdate,
				'UpdatedBy' => $created_by,
			);

			$affectedRows = $this->conn->extended->autoExecute($this->table_name, $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

			if (PEAR::isError($affectedRows)) {
				return false;
			}

			$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'CouponId = '. $this->conn->quote($this->conn->lastInsertId($this->table_name, 'CouponId'), 'integer'), null, true, null);

			if (PEAR::isError($res)) {
                return false;
            }

			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (null == $row || sizeof($row) == 0)
			{
				return array("NOTINSERTED");
			}

			return $row;
		}

		public function generate()
		{
			$query = "SELECT * FROM coupon";
					
			if (!empty($this->coupon_id))
				$query_keys[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');
				
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			$query .= " WHERE " . $query_string;

			$res = $this->conn->query($query);
			if (PEAR::isError($res)) {
				return false;
			}

			$coupon_details = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (null == $coupon_details || sizeof($coupon_details) == 0)
			{
				return array("INVALID");
			}
			
			if ("ACTIVE" == $coupon_details['status'])
			{
				return array("GENERATED_ALREADY");
			}
			
			// GENERATE CODES
			
			// generate from csv
			$the_codes = array();
			$the_quantity = $coupon_details['quantity'];

			if (!empty($coupon_details['file']))
			{
				$file = fopen($coupon_details['file'],"r");
				if($file)
				{
					/*while(!feof($file))
					{
					  $tmp_arr = fgetcsv($file);
					  foreach ($tmp_arr as &$a_code)
					  {
						if(!empty($a_code) && null != ($a_code))
						{
							$the_codes[] = $a_code;
						}
					  }
					}*/
					while (($a_code = fgets($file)) !== false) {
						// trim spaces
						$a_code = trim($a_code);
						// check if null or empty
						if(!empty($a_code) && null != ($a_code))
						{
							// check if already there in array
							if (!in_array($a_code, $the_codes))
							{
								$the_codes[] = $a_code;
							}
							
						}
					}

					fclose($file);
				}
				else
				{
					return array("FILENOTFOUND");
				}		

				//$the_codes = array_unique($the_codes);
				//print_r($the_codes);
				shuffle($the_codes);
				$the_quantity = count($the_codes);
			}
			// system generated
			else
			{
				$len = $coupon_details["codelength"];
				if ($coupon_details["codelength"] <= 0)
				{
					$len = $this->default_code_length;
				}

				for ($counter = 0; $counter < $the_quantity; $counter++)
				{
					switch ($coupon_details['type'])
					{
						case 'ALPHA-NUMERIC':
							$the_codes[] = $this->generateUniqueAlphanumeric($len);
							break;
						case 'ALPHA':
							$the_codes[] = $this->generateUniqueAlpha($len);
							break;
						case 'NUMERIC':
							$the_codes[] = $this->generateUniqueNumeric($len);
							break;
						default:
							$the_codes[] = $this->generateUniqueAlphanumeric($len);
					}
					
				}
			}

			$generated_coupons_tbl_name = 'generated_coupons';
			
			// generated_coupons table
			$curdate = date('Y-m-d H:i:s');
			for ($i = 1; $i <= $the_quantity; $i++)
			{
				$types = array('integer','text','timestamp');
				$fields_values = array(
					'CouponId' => $coupon_details['couponid'],
					'Code' => $the_codes[$i-1],
					'DateCreated' => $curdate
				);
				
				$affectedRows = $this->conn->extended->autoExecute($generated_coupons_tbl_name, $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

				if (PEAR::isError($affectedRows)) {
					return false;
				}

				$res = $this->conn->extended->autoExecute($generated_coupons_tbl_name, null, MDB2_AUTOQUERY_SELECT, 'GeneratedCouponId = '. $this->conn->quote($this->conn->lastInsertId($generated_coupons_tbl_name, 'GeneratedCouponId'), 'integer'), null, true, null);

				if (PEAR::isError($res)) {
					return false;
				}

				$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

				if (null == $row || sizeof($row) == 0)
				{
					return array("NOTGENERATED");
				}				
			}

			$result_arr = array();
			if ($this->update($client_id, $brand_id, $campaign_id, $channel_id, null,
						  null, null, null, null, null, null, null, 'ACTIVE'))
			{
				$result_arr["generated_count"] = sizeof($the_codes);
				return $result_arr;
			}
			else
				return false;
		}
		
		public function regenerate()
		{
			$query = "SELECT * FROM coupon";
			if (!empty($this->coupon_id))
				$query_keys[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');
				
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			$query .= " WHERE " . $query_string;

			$res = $this->conn->query($query);
			if (PEAR::isError($res)) {
				return false;
			}

			$coupon_details = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if (null == $coupon_details || sizeof($coupon_details) == 0)
			{
				return array("INVALID");
			}
			
			if ("PENDING" == $coupon_details['status'])
			{
				return array("STILL_PENDING");
			}
			
			if ("0" == $coupon_details['edit_flag'])
			{
				return array("EDITED_ALREADY");
			}
			
			// GENERATE CODES
			
			// generate from csv
			$the_codes = array();
			$the_quantity = $coupon_details['quantity'];

			if (!empty($coupon_details['file']))
			{
			// get list of generated codes
			$existing_codes = $this->edit_retrieve_generated();
				$file = fopen($coupon_details['file'],"r");
				if($file)
				{

					while (($a_code = fgets($file)) !== false) {
						// trim spaces
						$a_code = trim($a_code);
						// check if null or empty
						if(!empty($a_code) && null != ($a_code))
						{
							// check if already there in array
							if (!in_array($a_code, $the_codes))
							{
								$the_codes[] = $a_code;
							}
						}
					}
					
					// compare existing and new codes
					$diff1 = array_diff($the_codes, $existing_codes);
					//echo '<pre>';
					//print_r($the_codes);
					//print_r($existing_codes);
					//print_r($diffs);
					//exit();
					fclose($file);
				}
				else
				{
					return array("FILENOTFOUND");
				}		
				shuffle($diff1);
				$the_quantity = count($diff1);
				$addcounter = count($diff1);
				$totalAdd = $the_quantity;
			}
			// system generated
			else
			{
				//get current codes
				$existing_codes = $this->edit_retrieve_generated();
				
				if (count($existing_codes) >= $coupon_details['quantity'])
				{
					return array("LESSTHAN_CURRENT");
				}
				
				$addcounter = (int)$coupon_details['quantity'] -  count($existing_codes);
				
				
				$len = $coupon_details["codelength"];
				if ($coupon_details["codelength"] <= 0)
				{
					$len = $this->default_code_length;
				}
				
				

				for ($counter = 0; $counter < $the_quantity; $counter++)
				{
					switch ($coupon_details['type'])
					{
						case 'ALPHA-NUMERIC':
							$the_codes[] = $this->generateUniqueAlphanumeric($len);
							break;
						case 'ALPHA':
							$the_codes[] = $this->generateUniqueAlpha($len);
							break;
						case 'NUMERIC':
							$the_codes[] = $this->generateUniqueNumeric($len);
							break;
						default:
							$the_codes[] = $this->generateUniqueAlphanumeric($len);
					}
					
				}
				$diff1 = array_diff($the_codes, $existing_codes);
				$totalAdd = $addcounter;
			}

			$generated_coupons_tbl_name = 'generated_coupons';
			
			
			
			// generated_coupons table
			$curdate = date('Y-m-d H:i:s');
			for ($i = 1; $i <= $addcounter; $i++)
			{
				$types = array('integer','text','timestamp');
				$fields_values = array(
					'CouponId' => $coupon_details['couponid'],
					'Code' => $diff1[$i-1],
					'DateCreated' => $curdate
				);
				
				$affectedRows = $this->conn->extended->autoExecute($generated_coupons_tbl_name, $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

				if (PEAR::isError($affectedRows)) {
					return false;
				}

				$res = $this->conn->extended->autoExecute($generated_coupons_tbl_name, null, MDB2_AUTOQUERY_SELECT, 'GeneratedCouponId = '. $this->conn->quote($this->conn->lastInsertId($generated_coupons_tbl_name, 'GeneratedCouponId'), 'integer'), null, true, null);

				if (PEAR::isError($res)) {
					return false;
				}

				$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

				if (null == $row || sizeof($row) == 0)
				{
					return array("NOTGENERATED");
				}	
				$diffs = $addcounter; 
			}

			$result_arr = array();
			if ($this->update($client_id, $brand_id, $campaign_id, $channel_id, null,
						  null, null, null, null, null, null, null, 'ACTIVE', '0'))
			{
				// $result_arr["generated_count"] = sizeof($diffs);
				$result_arr["generated_count"] = $totalAdd;
				return $result_arr;
			}
			else
				return false;
		}
		
		function generateUniqueAlphanumeric($number)
		{
			$arr = array('a', 'b', 'c', 'd', 'e', 'f',
						 'g', 'h', 'i', 'j', 'k', 'l',
						 'm', 'n', 'o', 'p', 'r', 's',
						 't', 'u', 'v', 'x', 'y', 'z',
						 'A', 'B', 'C', 'D', 'E', 'F',
						 'G', 'H', 'I', 'J', 'K', 'L',
						 'M', 'N', 'O', 'P', 'R', 'S',
						 'T', 'U', 'V', 'X', 'Y', 'Z',
						 '1', '2', '3', '4', '5', '6',
						 '7', '8', '9', '0');
			$token = "";
			for ($i = 0; $i < $number; $i++) {
				$index = rand(0, count($arr) - 1);
				$token .= $arr[$index];
			}

			return $token;
		}
		
		function generateUniqueNumeric($number)
		{
			$arr = array('1', '2', '3', '4', '5', '6',
						 '7', '8', '9', '0');
			$token = "";
			for ($i = 0; $i < $number; $i++) {
				$index = rand(0, count($arr) - 1);
				$token .= $arr[$index];
			}

			return $token;
		}

		function generateUniqueAlpha($number)
		{
			$arr = array('a', 'b', 'c', 'd', 'e', 'f',
						 'g', 'h', 'i', 'j', 'k', 'l',
						 'm', 'n', 'o', 'p', 'r', 's',
						 't', 'u', 'v', 'x', 'y', 'z',
						 'A', 'B', 'C', 'D', 'E', 'F',
						 'G', 'H', 'I', 'J', 'K', 'L',
						 'M', 'N', 'O', 'P', 'R', 'S',
						 'T', 'U', 'V', 'X', 'Y', 'Z');
			$token = "";
			for ($i = 0; $i < $number; $i++) {
				$index = rand(0, count($arr) - 1);
				$token .= $arr[$index];
			}

			return $token;
		}
}
?>