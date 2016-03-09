<?php
class CustomerInfo
{
        public $conn;

        public function CustomerInfo($conn) 
        {
            $this->conn       = $conn;
            $this->table_name = 'customers';
        }

	
	public function get_info($pdata=null)
	{
		    
			//fmt
			$client_id   = addslashes($pdata["client_id"]  );
			$customer_id = addslashes($pdata["customer_id"]);
			$facebook    = addslashes($pdata["fb_id"]   );
			$twitter_handle    = addslashes($pdata["twitter_handle"]   );
			$email       = addslashes($pdata["email"]      );
			
			
			$retv                 = array();
			$retv["totalrows"]    = 0;
			$retv["breakdown"]    = array();
			$xwhere               = array();
			
			if(strlen($client_id)) 
			{
				$xwhere[] = " AND ClientId = '$client_id' ";
			}
			if(strlen($facebook)) 
			{
				$xwhere[] = " AND FBId     = '$facebook' ";
			}
			if(strlen($twitter_handle)) 
			{
				$xwhere[] = " AND TwitterHandle     = '$twitter_handle' ";
			}
			if(strlen($email)) 
			{
				$xwhere[] = " AND Email    = '$email' ";
			}
			if(strlen($customer_id)) 
			{
				$xwhere[] = " AND CustomerId = '$customer_id' ";
			}
			$more  = @join("\n",$xwhere);
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			SELECT   
				*
			FROM
				customers 
			WHERE   1=1
			    $more
			ORDER BY CustomerId
			";

			//run
			$res = $this->conn->query($query);

			if (PEAR::isError($res)) {
				return false;
			}

			$result_array = array();
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
	    		{				
				$retv["breakdown"][] = $row;
				$counter++;
			}
			
			$retv["totalrows"] = $counter;
			$retv["status"]    = ($counter>0)?(1):(0);
			
			//give it back
			return ($counter == 0) ? (false) : ($retv);
	}


	

}
?>
