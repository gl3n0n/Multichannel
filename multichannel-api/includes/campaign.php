<?php
class Campaign
{
        public $conn;

        public function Campaign($conn) 
        {
            $this->conn = $conn;
            $this->table_name = 'campaigns';
        }

		public function retrieve($brand_id, $campaign_id)
        {
		    $query = "SELECT BrandName,CampaignName,campaigns.description as description,campaigns.CampaignId as CampaignId, campaigns.BrandId as BrandId " . 
			         "FROM campaigns join brands on brands.BrandId = campaigns.BrandId";

			$query_keys = array();

			if (!empty($brand_id))
				$query_keys[] = 'campaigns.BrandId = '. $this->conn->quote($brand_id, 'integer');
			if (!empty($campaign_id))
				$query_keys[] = 'campaigns.CampaignId = '. $this->conn->quote($campaign_id, 'integer');

			$query_keys[] = "campaigns.Status = 'ACTIVE'";
			
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
			
			/*$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);*/
			if ($counter == 0)
			{
				return false;
			}

			return $result_array;
		}
}
?>