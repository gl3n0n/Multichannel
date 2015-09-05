<?php
require_once('../config/constants.php');

class Raffle {
		public $raffle_id;
        public $conn;

        public function Raffle($conn, $raffle_id) 
        {
            $this->raffle_id = $raffle_id;
            $this->conn = $conn;
            $this->table_name = 'raffle';
        }

		public function retrieve($client_id, $brand_id, $campaign_id, $channel_id, $status)
        {
			$query_keys = array();

			if (!empty($this->raffle_id))
				$query_keys[] = 'RaffleId = '. $this->conn->quote($this->raffle_id, 'integer');

			if ($status != "PENDING")
				$query_keys[] = "Status = 'ACTIVE'";
			else
				$query_keys[] = "Status = 'PENDING'";
			
			if($client_id>0)
				$query_keys[] = " ClientId = '".@addslashes(trim($client_id))."' ";
				
				
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

				//echo $query_string;
			$query_string .= ' ORDER BY RaffleId ASC';

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
				$result_array[] = $row;
				$counter++;
			}

			if ($counter == 0)
			{
				return false;
			}

			return $result_array;
		}
		
		public function draw_winner($participants)
		{
			$pre_query = "SELECT * from raffle WHERE RaffleId=" . $this->conn->quote($this->raffle_id, 'integer');
			$pre_res = $this->conn->query($pre_query);
	
			if (PEAR::isError($pre_res)) {
				return array("INVALID");
			}
			$pre_row = $pre_res->fetchRow(MDB2_FETCHMODE_ASSOC);
			
			if (null == $pre_row || sizeof($pre_row) == 0)
			{
				return array("INVALID");
			}

			$coupon_id = $pre_row["couponid"];
			$no_of_winners = $pre_row["noofwinners"];

			$query = "SELECT DISTINCT(Email) FROM (SELECT Email from generated_coupons join customers on generated_coupons.CustomerId = customers.CustomerId";
									  
			$query_keys = array();

			$query_keys[] = 'generated_coupons.CouponId = '. $this->conn->quote($coupon_id, 'integer');

			$query_keys[] = "generated_coupons.Status = 'REDEEMED'";
			
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			if (preg_match(PARTICIPANTS_REGEX, $participants))
			{
				$query .= " WHERE " . $query_string  . " AND customers.CustomerId in ($participants) ORDER BY RAND()) as T1 LIMIT $no_of_winners";
			}
			else
			{
				$query .= " WHERE " . $query_string  . " ORDER BY RAND()) as T1 LIMIT $no_of_winners";
			}

			//echo $query;
			$res = $this->conn->query($query);
	
			if (PEAR::isError($res)) {
				return false;
			}

			$result_array = array();
			$backup_array = array();
			$counter = 0;
			// string for backup winners query
			$query2_clause = "";
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
			{				
				$result_array[] = $row;
				$query2_clause = $query2_clause . "'" . $row["email"] . "',";
				$counter++;
			}

			$query2_clause = rtrim($query2_clause, ",");

			if ($counter == 0)
			{
				return false;
			}

			$query2 = "SELECT DISTINCT(Email) FROM (SELECT Email from generated_coupons join customers on generated_coupons.CustomerId = customers.CustomerId";
									  
			$query_keys2 = array();

			$query_keys2[] = 'generated_coupons.CouponId = '. $this->conn->quote($coupon_id, 'integer');

			$query_keys2[] = "generated_coupons.Status = 'REDEEMED'";
			
			if (sizeof($query_keys2) == 0)
				$query_string2 = null;
			else
				$query_string2 = implode(' AND ', $query_keys2);

			if (preg_match(PARTICIPANTS_REGEX, $participants))
			{
				$query2 .= " WHERE " . $query_string2  . " AND customers.CustomerId in ($participants) and customers.Email not in ($query2_clause) ORDER BY RAND()) as T1 LIMIT $no_of_winners";
			}
			else
			{
				$query2 .= " WHERE " . $query_string2  . " AND customers.Email not in ($query2_clause) ORDER BY RAND()) as T1 LIMIT $no_of_winners";
			}
			
			//echo $query2;
			$res2 = $this->conn->query($query2);
			if (PEAR::isError($res2)) {
				return false;
			}
			while ($row2 = $res2->fetchRow(MDB2_FETCHMODE_ASSOC))
			{				
				$backup_array[] = $row2;
			}
			
			$return_array["winners"] = $result_array;
			$return_array["backup_winners"] = $backup_array;

			return $return_array;
		}
		
		public function update($client_id, $brand_id, $campaign_id, $channel_id, $updated_by,
								$source, $no_of_winners, $draw_date, $status)
		{
			$query_keys = array();

			if (!empty($this->raffle_id))
				$query_keys[] = 'RaffleId = '. $this->conn->quote($this->raffle_id, 'integer');
			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);
				
			// Prepare values
			$fields_values = array();

			$fields_values['UpdatedBy'] = $updated_by;
			$types = array('text');
			
			if (!empty($source))
			{
				$fields_values['Source'] = $source;
				array_push($types,'text');
			}
			if (!empty($no_of_winners))
			{
				$fields_values['NoOfWinners'] = $no_of_winners;
				array_push($types,'integer');
			}
			
			if (!empty($draw_date))
			{
				$fields_values['DrawDate'] = $draw_date;
				array_push($types,'timestamp');
			}
			if (!empty($status))
			{
				$fields_values['Status'] = $status;
				array_push($types,'text');
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

		public function insert($coupon_id, $client_id, $brand_id, $campaign_id, $channel_id, $created_by,
								$source, $no_of_winners, $fda_no, $draw_date, $status)
		{
			$curdate = date('Y-m-d H:i:s');
			if ($status != 'ACTIVE')
				$status = 'PENDING';

			$types = array('integer','text','integer','text','timestamp','text','timestamp','text','timestamp','text');

			$fields_values = array(
				'CouponId' => $coupon_id,
				'Source' => $source,
				'NoOfWinners' => $no_of_winners,
				'FdaNo' => $fda_no,
				'DrawDate' => $draw_date,
				'Status' => $status,
				'DateCreated' => $curdate,
				'CreatedBy' => $created_by,
				'DateUpdated' => $curdate,
				'UpdatedBy' => $created_by,
			);

			$affectedRows = $this->conn->extended->autoExecute($this->table_name, $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

			if (PEAR::isError($affectedRows)) {
			var_dump($affectedRows);
				return false;
			}

			$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'RaffleId = '. $this->conn->quote($this->conn->lastInsertId($this->table_name, 'RaffleId'), 'integer'), null, true, null);

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
}
?>
