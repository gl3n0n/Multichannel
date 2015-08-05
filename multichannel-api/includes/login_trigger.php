<?php
class LoginTrigger {
        public $trigger_id;
        public $conn;

        public function LoginTrigger($conn, $trigger_id) 
        {
            $this->trigger_id = $trigger_id;
            $this->conn = $conn;
            $this->table_name = 'login_trigger';
        }

		public function add($client_id, $brand_id, $campaign_id, $channel_id,
						   $trigger_parameter, $start_date, $end_date, $trigger_reward, $created_by)
		{
			$curdate = date('Y-m-d H:i:s');
			$types = array('integer','integer','integer','integer','text','date','date','text','timestamp','integer','timestamp','integer');

			$fields_values = array(
				'ClientId' => $client_id,
				'BrandId' => $brand_id,
				'CampaignId' => $campaign_id,
				'ChannelId' => $channel_id,
				'TriggerParameter' => $trigger_parameter,
				'StartDate' => $start_date,
				'EndDate' => $end_date,
				'TriggerReward' => $trigger_reward,
				'DateCreated' => $curdate,
				'CreatedBy' => $created_by,
				'DateUpdated' => $curdate,
				'UpdatedBy' => $created_by,
			);

			$affectedRows = $this->conn->extended->autoExecute($this->table_name, $fields_values, MDB2_AUTOQUERY_INSERT, null, null, true, $types);

			if (PEAR::isError($affectedRows)) {
				return false;
			}

			$trig_id = $this->conn->quote($this->conn->lastInsertId($this->table_name, 'TriggerId'), 'integer');
			$res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'TriggerId = '. $trig_id, null, true, null);
				
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

		public function retrieve($client_id, $brand_id, $campaign_id, $channel_id)
        {
			$query_keys = array();

			if (!empty($this->trigger_id))
				$query_keys[] = 'TriggerId = '. $this->conn->quote($this->trigger_id, 'integer');
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

		public function update($client_id, $brand_id, $campaign_id, $channel_id,
							   $trigger_parameter, $start_date, $end_date, $trigger_reward, $updated_by)
		{
			$query_keys = array();

			if (!empty($this->trigger_id))
				$query_keys[] = 'TriggerId = '. $this->conn->quote($this->trigger_id, 'integer');

			if (sizeof($query_keys) == 0)
				$query_string = null;
			else
				$query_string = implode(' AND ', $query_keys);

			// Prepare values
			$fields_values = array();
			$table_fields = array('CustomerId');
			$types = array();

			if (!empty($brand_id))
			{
				$fields_values['BrandId'] = $brand_id;
				array_push($types,'integer');
				array_push($table_fields,'BrandId');
			}
			if (!empty($campaign_id))
			{
				$fields_values['CampaignId'] = $campaign_id;
				array_push($types,'integer');
				array_push($table_fields,'CampaignId');
			}
			if (!empty($client_id))
			{
				$fields_values['ClientId'] = $client_id;
				array_push($types,'integer');
				array_push($table_fields,'ClientId');
			}
			if (!empty($channel_id))
			{
				$fields_values['ChannelId'] = $channel_id;
				array_push($types,'integer');
				array_push($table_fields,'ChannelId');
			}

			if (!empty($trigger_parameter))
			{
				$fields_values['TriggerParameter'] = $trigger_parameter;
				array_push($types,'text');
				array_push($table_fields,'TriggerParameter');
			}
			if (!empty($start_date))
			{
				$fields_values['StartDate'] = $start_date;
				array_push($types,'timestamp');
				array_push($table_fields,'StartDate');
			}
			if (!empty($end_date))
			{
				$fields_values['EndDate'] = $end_date;
				array_push($types,'timestamp');
				array_push($table_fields,'EndDate');
			}
			if (!empty($trigger_reward))
			{
				$fields_values['TriggerReward'] = $trigger_reward;
				array_push($types,'text');
				array_push($table_fields,'TriggerReward');
			}
			if (!empty($updated_by))
			{
				$fields_values['UpdatedBy'] = $updated_by;
				array_push($types,'text');
				array_push($table_fields,'UpdatedBy');
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
}
?>
