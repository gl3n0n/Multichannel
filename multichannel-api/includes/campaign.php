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
	
	
	public function lists($client_id=0,$customer_id=0)
	{
		    
			//fmt
			$client_id  = addslashes($client_id);
			$customer_id= addslashes($customer_id);
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			SELECT 
				map.PointMappingId,
				map.PointsId ,
				map.ClientId ,
				map.BrandId  ,
				map.CampaignId ,
				map.ChannelId,
				pts.Name as PointsName,
				camp.CampaignName,
				brnd.BrandName,
				chn.ChannelName,
				clnt.CompanyName,
				(
				    SELECT COUNT(1)
				    FROM
				      customer_subscriptions sub
				    WHERE
				      1=1
				      AND sub.CustomerId = '$customer_id'
				      AND sub.PointsId   = map.PointsId
				      AND sub.ClientId   = map.ClientId
				      AND sub.BrandId    = map.BrandId
				      AND sub.CampaignId = map.CampaignId
				      
				) as participated
			FROM 
				points_mapping map,
				points    pts,
				campaigns camp,
				brands    brnd,
				clients   clnt,
				channels  chn
			WHERE   1=1
				AND map.ClientId   = '$client_id'
				AND map.Status     = 'ACTIVE'
				AND map.PointsId   = pts.PointsId
				AND map.ClientId   = clnt.ClientId 
				AND map.BrandId    = brnd.BrandId 
				AND map.CampaignId = camp.CampaignId 
				AND map.ChannelId  = chn.ChannelId 
				AND map.ClientId   IN(
					SELECT cust.ClientId
					   FROM customers  cust
					WHERE
					   1=1
					   AND cust.CustomerId  = '$customer_id'
				)
			GROUP BY 
				map.BrandId  ,
				map.CampaignId
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
	    			if($row["participated"] <= 0)
				{
					$result_array[] = $row;
					$counter++;
				}
			}
			//give it back
			return ($counter == 0) ? (false) : ($result_array);
	}



	public function listsCustomerSub($client_id=0,$customer_id=0)
	{
		    
			//fmt
			$client_id  = addslashes($client_id);
			$customer_id= addslashes($customer_id);
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			SELECT 
				map.SubscriptionId,
				map.PointsId ,
				map.ClientId ,
				map.BrandId  ,
				map.CampaignId ,
				camp.CampaignName,
				brnd.BrandName,
				clnt.CompanyName,
				pts.Name as PointsName,
				cust.FirstName,
				cust.LastName,
				cust.Email,
				cust.FBId,
				cust.TwitterHandle
			FROM 
				customer_subscriptions map,
				points     pts,
				campaigns  camp,
				brands     brnd,
				clients    clnt,
				customers  cust
			WHERE   1=1
				AND map.ClientId   = '$client_id'
				AND map.Status     = 'ACTIVE'
				AND map.PointsId   = pts.PointsId
				AND map.ClientId   = clnt.ClientId 
				AND map.BrandId    = brnd.BrandId 
				AND map.CampaignId = camp.CampaignId 
				AND map.CustomerId = cust.CustomerId
				AND map.CustomerId = '$customer_id'
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
				$result_array[] = $row;
				$counter++;
			}
			//give it back
			return ($counter == 0) ? (false) : ($result_array);
	}



	public function participate($client_id=0,
				    $customer_id=0,
				    $points_id=0,
				    $brand_id=0,
				    $campaign_id=0,
				    $created_by=0
	)
	{
		    
			//fmt
			$client_id  = addslashes($client_id);
			$customer_id= addslashes($customer_id);
			$points_id  = addslashes($points_id);
			$brand_id   = addslashes($brand_id);
			$campaign_id= addslashes($campaign_id);
			$created_by = addslashes($created_by);
			$status     = 'ACTIVE';
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$query      = "
			INSERT INTO customer_subscriptions (
				CustomerId  ,
				PointsId    ,
				ClientId    ,
				BrandId     ,
				CampaignId  ,
				Status      ,
				DateCreated ,
				CreatedBy
			)
			VALUES (
				'$customer_id',
				'$points_id',
				'$client_id',
				'$brand_id',
				'$campaign_id',
				'$status',
				Now(),
				'$created_by'
			)
			";
			
			//run
			$res = $this->conn->query($query);

			if (PEAR::isError($res)) {
				return false;
			}
			//get it
			$data                   = array();
			$data['SubscriptionId'] = $this->conn->lastInsertId('customer_subscriptions', 'SubscriptionId');
			
			
			//give it back
			return ($data['SubscriptionId'] == 0) ? (false) : ($data);
	}

}
?>