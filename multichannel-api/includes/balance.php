<?php
class Balance {
        public $client_id;
        public $conn;

        public function Balance($conn, $client_id) 
        {
            $this->client_id = $client_id;
            $this->conn = $conn;
            $this->table_name = 'points';
        }

        public function isAllowed($brand_id, $campaign_id, $channel_id)
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
            else
            {
                $tbl = 'brands';
                $query_keys[] = 'BrandId = '.$this->conn->quote($brand_id, 'integer');
            }

            $query_keys[] = "Status = 'ACTIVE'";
            $query_keys[] = '`DurationFrom` <= '. $this->conn->quote($curdate, 'text');
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
        }

        public function inquire($brand_id, $campaign_id, $channel_id, $start_date, $end_date)
        {
            $query_keys = array();

            if (!empty($this->client_id))
                $query_keys[] = 'ClientId = '. $this->conn->quote($this->client_id, 'integer');

            if (!empty($brand_id))
                $query_keys[] = 'BrandId = '. $this->conn->quote($brand_id, 'integer');

            if (!empty($campaign_id))
                $query_keys[] = 'CampaignId = '.$this->conn->quote($campaign_id, 'integer');

            if (!empty($channel_id))
                $query_keys[] = 'ChannelId = '.$this->conn->quote($channel_id, 'integer');

            if (!empty($start_date) && !empty($end_date)) {
                $query_keys[] = '`From` >= '. $this->conn->quote($start_date, 'text');
                $query_keys[] = '`From` <= '. $this->conn->quote($end_date, 'text');
            }

            $query_keys[] = "Status = 'ACTIVE'";

            $result_types = array(
                    'SUM(Value) as total_points' => 'integer'
                );

            if (sizeof($query_keys) == 0)
                $query_string = null;
            else
                $query_string = implode(' AND ', $query_keys);

            $res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, $query_string, null, true, $result_types);

            if (PEAR::isError($res)) {
                return false;
            }

            $total_points = 0;

            while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
                $total_points = $total_points + $row['total_points'];
            }

            return $total_points;
        }

        public function update($brand_id, $campaign_id, $channel_id, $points, $multiplier)
        {
            $table_fields = array('BrandId', 'CampaignId', 'ChannelId', 'ClientId', '`From`', '`To`', 'Value', 'Status', 'DateCreated', 'CreatedBy', 'DateUpdated', 'UpdatedBy');
            $types = array('integer', 'integer', 'integer', 'integer', 'timestamp', 'timestamp', 'integer', 'text', 'timestamp', 'integer', 'timestamp', 'integer');

            $sth = $this->conn->extended->autoPrepare($this->table_name, $table_fields, MDB2_AUTOQUERY_INSERT, null, $types);

            $curdate = date('Y-m-d H:i:s');
            $table_values = array($brand_id, $campaign_id, $channel_id, $this->client_id, $curdate, $curdate, $points, 'ACTIVE', $curdate, 1, $curdate, 1);
            $res =& $sth->execute($table_values);
            if (PEAR::isError($res)) {
                $response['result_code'] = 500;
                $response['error_txt'] = 'Error Updating Balance';
                echo json_encode($response);
                die($res->getMessage());
            }

            $result_types = array(
                    'SUM(Value) as total_points' => 'integer'
                );

            $res = $this->conn->extended->autoExecute($this->table_name, null, MDB2_AUTOQUERY_SELECT, 'BrandId = '. $brand_id . ' AND CampaignId = '. $campaign_id .' AND ChannelId = '. $channel_id . ' AND ClientId = '. $this->client_id, null, true, $result_types);
            if (PEAR::isError($res)) {
                return false;
            }

            $new_points = 0;

            while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
            {
                $new_points = $total_points + $row['total_points'];
            }

            return $new_points;
        }
    }
?>

