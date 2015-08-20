<?php
require_once('../includes/points.php');

class RedeemCoupon {
        public $coupon_id;
        public $conn;

        public function RedeemCoupon($conn, $coupon_id)
        {
            $this->coupon_id = $coupon_id;
            $this->conn = $conn;
            $this->table_name = 'generated_coupons';
        }

               public function isValidCoupon()
               {
                       $query_keys = array();

                       if (!empty($this->coupon_id))
                               $query_keys[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');

                       $query_keys[] = "Status = 'ACTIVE' AND Quantity > 0";

                       if (sizeof($query_keys) == 0)
                               $query_string = null;
                       else
                               $query_string = implode(' AND ', $query_keys);

                       $query_string .= ' LIMIT 1';
                       $res = $this->conn->extended->autoExecute('coupon', null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, null);

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

                public function insert($client_id, $brand_id, $campaign_id, $channel_id, $customer_id, $date_redeemed)
                {
                        $types = array('integer','integer','integer','integer','integer','integer', 'timestamp');


                        if (empty($date_redeemed) || !preg_match(DATETIME_REGEX, $date_redeemed))
                        {
                                $date_redeemed = date('Y-m-d H:i:s');
                        }

                        $fields_values = array(
                                'ClientId' => $client_id,
                                'CouponId' => $this->coupon_id,
                                'CustomerId' => $customer_id,
                                'BrandId' => $brand_id,
                                'CampaignId' => $campaign_id,
                                'ChannelId' => $channel_id,
                                'DateRedeemed' => $date_redeemed,
                        );

                        $affectedRows = $this->conn->extended->autoExecute($this->table_name, $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

                        if (PEAR::isError($affectedRows)) {
                                return false;
                        }

                        $res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'RedeemedCouponId = '. $this->conn->quote($this->conn->lastInsertId($this->table_name, 'RedeemedCouponId'), 'integer'), null, true, null);

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

				public function isOverTheLimit($coupon_id, $customer_id)
				{
					$query = "SELECT count(1) as count FROM generated_coupons";
					
					if (!empty($this->coupon_id))
						$query_keys[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');
					if (!empty($customer_id))
						$query_keys[] = 'CustomerId = '. $this->conn->quote($customer_id, 'integer');

					$query_keys[] = 'Status = "REDEEMED"';
						
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
					
					$query2 = "SELECT LimitPerUser FROM coupon";
					if (!empty($this->coupon_id))
						$query_keys2[] = 'CouponId = '. $this->conn->quote($this->coupon_id, 'integer');
					if (sizeof($query_keys2) == 0)
						$query_string2 = null;
					else
						$query_string2 = implode(' AND ', $query_keys2);

					$query2 .= " WHERE " . $query_string2;
					
					$res2 = $this->conn->query($query2);
					if (PEAR::isError($res2)) {
						return false;
					}
					$row2 = $res2->fetchRow(MDB2_FETCHMODE_ASSOC);
					if ($row['count'] >= $row2['limitperuser'])
					{
						return true;
					}
					
					return false;
				}

				/*public function deductQuantity($client_id, $brand_id, $campaign_id, $channel_id, $customer_id)
                {
					$query = "UPDATE coupon SET Quantity = Quantity - 1";
					
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

					$query .= " WHERE " . $query_string;

					$res = $this->conn->query($query);

					if (PEAR::isError($res)) {
						return false;
					}

					return true;
				}*/

				public function retrieve($customer_id, $client_id, $brand_id, $campaign_id, $channel_id, $generated_coupon_id)
				{
					//$query = "SELECT BrandName, redeemed_coupon.CouponId, Code, Type, TypeId, Source, ExpiryDate, coupon.Status, coupon.ClientId, coupon.BrandId, coupon.ChannelId, coupon.CampaignId, DateRedeemed FROM coupon join redeemed_coupon ON coupon.CouponId = redeemed_coupon.CouponId join brands on coupon.BrandId = brands.BrandId";
					$query = "SELECT coupon_mapping.CouponMappingId as CouponMappingId, FirstName, MiddleName, LastName, Email,BrandName, generated_coupons.GeneratedCouponId, generated_coupons.CustomerId as CustomerId, generated_coupons.CouponId as CouponId, generated_coupons.Code as Code, coupon.Type, TypeId, Source, ExpiryDate, coupon.Status, coupon_mapping.ClientId, coupon_mapping.BrandId, coupon_mapping.ChannelId, coupon_mapping.CampaignId, campaigns.CampaignName as CampaignName, channels.ChannelName as ChannelName, DateRedeemed FROM coupon join generated_coupons ON coupon.CouponId = generated_coupons.CouponId join coupon_mapping on coupon_mapping.CouponMappingId = generated_coupons.CouponMappingId join brands on coupon_mapping.BrandId = brands.BrandId join customers on customers.CustomerId = generated_coupons.CustomerId join campaigns on campaigns.CampaignId = coupon_mapping.CampaignId join channels on channels.ChannelId = coupon_mapping.ChannelId";
											  
					$query_keys = array();

					if (!empty($this->coupon_id))
						$query_keys[] = 'generated_coupons.CouponId = '. $this->conn->quote($this->coupon_id, 'integer');
					if (!empty($customer_id))
						$query_keys[] = 'generated_coupons.CustomerId = '. $this->conn->quote($customer_id, 'integer');
					if (!empty($client_id))
						$query_keys[] = 'coupon_mapping.ClientId = '. $this->conn->quote($client_id, 'integer');
					if (!empty($brand_id))
						$query_keys[] = 'coupon_mapping.BrandId = '. $this->conn->quote($brand_id, 'integer');
					if (!empty($campaign_id))
						$query_keys[] = 'coupon_mapping.CampaignId = '. $this->conn->quote($campaign_id, 'integer');
					if (!empty($channel_id))
						$query_keys[] = 'coupon_mapping.ChannelId = '. $this->conn->quote($channel_id, 'integer');
					if (!empty($generated_coupon_id))
						$query_keys[] = 'generated_coupons.GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer');
					
					$query_keys[] = "generated_coupons.Status = 'REDEEMED'";
					
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

				public function redeem($generated_coupon_id, $customer_id, $coupon_mapping_id)
				{
					$query2 = "SELECT Status FROM generated_coupons";
					if (!empty($generated_coupon_id))
						$query_keys2[] = 'GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer');
					if (sizeof($query_keys2) == 0)
						$query_string2 = null;
					else
						$query_string2 = implode(' AND ', $query_keys2);

					$query2 .= " WHERE " . $query_string2;

					$res2 = $this->conn->query($query2);
					if (PEAR::isError($res2)) {
						return false;
					}
					$row2 = $res2->fetchRow(MDB2_FETCHMODE_ASSOC);
					if ($row2['status'] == "REDEEMED")
					{
						return array("ALREADY_REDEEMED");
					}

					$query_keys = array();

					if (!empty($generated_coupon_id))
						$query_keys[] = 'GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer');
					
					if (sizeof($query_keys) == 0)
						$query_string = null;
					else
						$query_string = implode(' AND ', $query_keys);
						
					// Prepare values
					$fields_values = array();

					$fields_values['CustomerId'] = $this->conn->quote($customer_id, 'integer');
					$fields_values['CouponMappingId'] = $this->conn->quote($coupon_mapping_id, 'integer');
					$fields_values['Status'] = "REDEEMED";
					$fields_values['DateRedeemed'] = date('Y-m-d H:i:s');
					$types = array('integer','integer','text','timestamp');

					$affectedRows = $this->conn->extended->autoExecute($this->table_name, $fields_values, MDB2_AUTOQUERY_UPDATE, $query_string, null, true, $types);

					//print_r($affectedRows);
					if (PEAR::isError($affectedRows)) {
						return false;
					}

					$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer') . ' AND Status = "REDEEMED"', null, true, null);

					if (PEAR::isError($res)) {
						return false;
					}

					$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

					if (null == $row || sizeof($row) == 0)
					{
						return false;
					}

					return $row;
				}

				public function redeemOnPoints($generated_coupon_id, $customer_id, $coupon_mapping_id)
				{
					$query2 = "SELECT Status FROM generated_coupons";
					if (!empty($generated_coupon_id))
						$query_keys2[] = 'GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer');
					if (sizeof($query_keys2) == 0)
						$query_string2 = null;
					else
						$query_string2 = implode(' AND ', $query_keys2);

					$query2 .= " WHERE " . $query_string2;

					$res2 = $this->conn->query($query2);
					if (PEAR::isError($res2)) {
						return false;
					}
					$row2 = $res2->fetchRow(MDB2_FETCHMODE_ASSOC);
					if ($row2['status'] == "REDEEMED")
					{
						return array("ALREADY_REDEEMED");
					}

					$query_keys = array();

					if (!empty($generated_coupon_id))
						$query_keys[] = 'GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer');
					
					if (sizeof($query_keys) == 0)
						$query_string = null;
					else
						$query_string = implode(' AND ', $query_keys);

					// Check if there are enough points to cater this request	
					$query_details = "SELECT CouponId,ClientId,BrandId,CampaignId,ChannelId from coupon_mapping WHERE CouponMappingId = " . $this->conn->quote($coupon_mapping_id, 'integer');
					$result_details = $this->conn->query($query_details);

					if (PEAR::isError($result_details))
					{
						return false;
					}
					$details = $result_details->fetchRow(MDB2_FETCHMODE_ASSOC);					
					
					
					$query_sub_id =  "SELECT customer_subscriptions.SubscriptionId,Balance from customer_points join customer_subscriptions on customer_subscriptions.SubscriptionId = customer_points.SubscriptionId WHERE ClientId = " . $details["clientid"] . " AND BrandId = " . $details["brandid"] . " AND CampaignId = " . $details["campaignid"] . " AND ChannelId = " . $details["channelid"] . " AND CustomerId = " . $this->conn->quote($customer_id, 'integer') . "";
					$result_sub_id = $this->conn->query($query_sub_id);

					if (PEAR::isError($result_sub_id))
					{
						return false;
					}
					$sub_id = $result_sub_id->fetchRow(MDB2_FETCHMODE_ASSOC);
					// Checks if the customer is subscribed to a promo, if not return an error.
					if (null == $sub_id || sizeof($sub_id) == 0)
					{
						return array("SUBSCRIPTION_NOT_FOUND");
					}
					
					$query_coupon_value = "SELECT PointsRequired FROM points_to_coupon WHERE Status = 'ACTIVE' AND CouponId = " . $details["couponid"];
					//echo $query_coupon_value;
					$result_coupon_value = $this->conn->query($query_coupon_value);
					if (PEAR::isError($result_coupon_value))
					{
						return false;
					}
					$coupon_value = $result_coupon_value->fetchRow(MDB2_FETCHMODE_ASSOC);
					if (null == $coupon_value || sizeof($coupon_value) == 0)
					{
						return array("CONFIG_NOT_FOUND");
					}
					
					// Check if Balance is > than coupon value.
					if ($sub_id["balance"] >= $coupon_value["pointsrequired"])
					{
						//$new_balance = ($sub_id["balance"] - $coupon_value["pointsrequired"]);
						$the_balance = $coupon_value["pointsrequired"];
						// proceed
						$query_pts = "UPDATE customer_points set Balance = Balance - $the_balance, Used = Used + $the_balance, Total = Balance + Used";
						if (!empty($sub_id["subscriptionid"]))
							$query_keys_pts[] = 'SubscriptionId = '. $this->conn->quote($sub_id["subscriptionid"], 'integer');
						
						if (sizeof($query_keys_pts) == 0)
							$query_string_pts = null;
						else
							$query_string_pts = implode(' AND ', $query_keys_pts);

						
						$query_pts .= " WHERE " . $query_string_pts;
						
						//echo $query_pts;
						$res = $this->conn->query($query_pts);

						if (PEAR::isError($res)) {
							return false;
						}
					}
					else
					{
						return array("INSUFICENT_BAL");
					}
						
					// Prepare values
					$fields_values = array();

					$fields_values['CustomerId'] = $this->conn->quote($customer_id, 'integer');
					$fields_values['CouponMappingId'] = $this->conn->quote($coupon_mapping_id, 'integer');
					$fields_values['Status'] = "REDEEMED";
					$fields_values['DateRedeemed'] = date('Y-m-d H:i:s');
					$types = array('integer','integer','text','timestamp');

					$affectedRows = $this->conn->extended->autoExecute($this->table_name, $fields_values, MDB2_AUTOQUERY_UPDATE, $query_string, null, true, $types);

					//print_r($affectedRows);
					if (PEAR::isError($affectedRows)) {
						return false;
					}

					$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer') . ' AND Status = "REDEEMED"', null, true, null);

					if (PEAR::isError($res)) {
						return false;
					}

					$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

					if (null == $row || sizeof($row) == 0)
					{
						return false;
					}
					
					/*
					$row4 = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

					if (null == $row4 || sizeof($row4) == 0)
					{
						return false;
					}*/

					// Insert to Points log
					$types = array('integer','integer','integer','integer','integer','integer', 'integer','timestamp');

					$curdate = date('Y-m-d H:i:s');

					$fields_values = array(
							'ClientId' => $details["clientid"],
							'CustomerId' => $this->conn->quote($customer_id, 'integer'),
							'BrandId' => $details["brandid"],
							'CampaignId' => $details["campaignid"],
							'ChannelId' => $details["channelid"],
							'SubscriptionId' => $this->conn->quote($sub_id["subscriptionid"], 'integer'),
							'Points' => ($the_balance * -1),
							'DateCreated' => $curdate
					);

					$affectedRows = $this->conn->extended->autoExecute("points_log", $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);
					//var_dump($affectedRows);
					if (PEAR::isError($affectedRows)) {
							return false;
					}
				
					
					$row["balance"] = $new_balance;
					return $row;
				}
				
				public function couponToPoints($generated_coupon_id, $customer_id, $coupon_mapping_id)
				{
					$query2 = "SELECT Status FROM generated_coupons";
					if (!empty($generated_coupon_id))
						$query_keys2[] = 'GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer');
					if (sizeof($query_keys2) == 0)
						$query_string2 = null;
					else
						$query_string2 = implode(' AND ', $query_keys2);

					$query2 .= " WHERE " . $query_string2;

					$res2 = $this->conn->query($query2);
					if (PEAR::isError($res2)) {
						return false;
					}

					$row2 = $res2->fetchRow(MDB2_FETCHMODE_ASSOC);
					if ($row2['status'] == "REDEEMED_AND_CONVERTED")
					{
						return array("ALREADY_CONVERTED");
					}
					else if ($row2['status'] != "REDEEMED")
					{
						return array("NOT_REDEEMED");
					}

					$query_keys = array();

					if (!empty($generated_coupon_id))
						$query_keys[] = 'GeneratedCouponId = '. $this->conn->quote($generated_coupon_id, 'integer');
					
					if (sizeof($query_keys) == 0)
						$query_string = null;
					else
						$query_string = implode(' AND ', $query_keys);

					// Check if there are enough points to cater this request	
					$query_details = "SELECT CouponId,ClientId,BrandId,CampaignId,ChannelId from coupon_mapping WHERE CouponMappingId = " . $this->conn->quote($coupon_mapping_id, 'integer');
					$result_details = $this->conn->query($query_details);

					if (PEAR::isError($result_details))
					{
						return array("ERROR");
					}
					$details = $result_details->fetchRow(MDB2_FETCHMODE_ASSOC);
					if (null == $details || sizeof($details) == 0)
					{
						return array("SUBSCRIPTION_NOT_FOUND");
					}					

					$query_sub_id =  "SELECT customer_subscriptions.SubscriptionId,Balance from customer_points join customer_subscriptions on customer_subscriptions.SubscriptionId = customer_points.SubscriptionId WHERE ClientId = " . $details["clientid"] . " AND BrandId = " . $details["brandid"] . " AND CampaignId = " . $details["campaignid"] . " AND ChannelId = " . $details["channelid"] . " AND CustomerId = " . $this->conn->quote($customer_id, 'integer') . "";
					$result_sub_id = $this->conn->query($query_sub_id);

					if (PEAR::isError($result_sub_id))
					{
						return array("ERROR");
					}
					$sub_id = $result_sub_id->fetchRow(MDB2_FETCHMODE_ASSOC);
					// Checks if the customer is subscribed to a promo, if not return an error.
					if (null == $sub_id || sizeof($sub_id) == 0)
					{
						return array("SUBSCRIPTION_NOT_FOUND");
					}
					
					$query_coupon_value = "SELECT PointsValue FROM coupon_to_points WHERE Status = 'ACTIVE' AND CouponId = " . $details["couponid"];
					//echo $query_coupon_value;
					$result_coupon_value = $this->conn->query($query_coupon_value);
					if (PEAR::isError($result_coupon_value))
					{
						return array("ERROR");
					}
					$coupon_value = $result_coupon_value->fetchRow(MDB2_FETCHMODE_ASSOC);
					if (null == $coupon_value || sizeof($coupon_value) == 0)
					{
						return array("CONFIG_NOT_FOUND");
					}
					
					// Add points
					$new_balance = ($sub_id["balance"] + $coupon_value["pointsvalue"]);
					// proceed
					$query_pts = "UPDATE customer_points set Balance = Balance + " . $coupon_value["pointsvalue"] . ", Total = Total + " . $coupon_value["pointsvalue"];
					if (!empty($sub_id["subscriptionid"]))
						$query_keys_pts[] = 'SubscriptionId = '. $this->conn->quote($sub_id["subscriptionid"], 'integer');
					
					if (sizeof($query_keys_pts) == 0)
						$query_string_pts = null;
					else
						$query_string_pts = implode(' AND ', $query_keys_pts);

					
					$query_pts .= " WHERE " . $query_string_pts;
					
					//echo $query_pts;
					$res = $this->conn->query($query_pts);

					if (PEAR::isError($res)) {
						return array("ERROR");
					}
					
					$update_gen_coupons_query = "UPDATE generated_coupons SET status = 'REDEEMED_AND_CONVERTED' WHERE GeneratedCouponId = " . $this->conn->quote($generated_coupon_id, 'integer');
					$result_gen_coupons = $this->conn->query($update_gen_coupons_query);
					if (PEAR::isError($result_gen_coupons))
					{
						return array("ERROR");
					}
					
					// Insert to Points log
					$types = array('integer','integer','integer','integer','integer','integer', 'integer','timestamp');

					$curdate = date('Y-m-d H:i:s');

					$fields_values = array(
							'ClientId' => $details["clientid"],
							'CustomerId' => $this->conn->quote($customer_id, 'integer'),
							'BrandId' => $details["brandid"],
							'CampaignId' => $details["campaignid"],
							'ChannelId' => $details["channelid"],
							'SubscriptionId' => $this->conn->quote($sub_id["subscriptionid"], 'integer'),
							'Points' => $coupon_value["pointsvalue"],
							'DateCreated' => $curdate
					);

					$affectedRows = $this->conn->extended->autoExecute("points_log", $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);
					//var_dump($affectedRows);
					if (PEAR::isError($affectedRows)) {
							return false;
					}

					/*$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'RedeemedCouponId = '. $this->conn->quote($this->conn->lastInsertId($this->table_name, 'RedeemedCouponId'), 'integer'), null, true, null);

					if (PEAR::isError($res)) {
						return false;
					}*/
					
					
					$query_sub_id =  "SELECT Balance from customer_points join customer_subscriptions on customer_subscriptions.SubscriptionId = customer_points.SubscriptionId WHERE ClientId = " . $details["clientid"] . " AND BrandId = " . $details["brandid"] . " AND CampaignId = " . $details["campaignid"] . " AND ChannelId = " . $details["channelid"] . " AND CustomerId = " . $this->conn->quote($customer_id, 'integer') . "";
					$result_sub_id = $this->conn->query($query_sub_id);

					if (PEAR::isError($result_sub_id))
					{
						return array("ERROR");
					}
					$sub_id = $result_sub_id->fetchRow(MDB2_FETCHMODE_ASSOC);
					// Checks if the customer is subscribed to a promo, if not return an error.
					if (null == $sub_id || sizeof($sub_id) == 0)
					{
						return array("SUBSCRIPTION_NOT_FOUND");
					}

					$row["balance"] = $sub_id["balance"];
					return $row;
				}
}
?>
