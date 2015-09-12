<?php
class CouponList
{
        public $conn;

        public function CouponList($conn) 
        {
            $this->conn       = $conn;
            $this->table_name = 'coupon';
        }

	
	public function list_of_available_coupon($pdata=null)
	{
		    
			//fmt
			$client_id  = addslashes($pdata["client_id"]  );
			$customer_id= addslashes($pdata["customer_id"]);
			$qrlink     = addslashes($pdata["qrlink"]);
			
			//sql -> PointsId | ClientId | BrandId | CampaignId | ChannelId
			$retv          = array();
			$retv["coupon"]= array();
			
			$query      = "
			SELECT  gen.Code,
			        CONCAT('$qrlink',gen.GeneratedCouponId,'.png') as qr_code,
				CONCAT(cust.FirstName,' ' ,cust.LastName) as CustomerName,
				sub.ClientId ,
				clnt.CompanyName,
				sub.BrandId  ,
				brnd.BrandName,
				sub.CampaignId ,
				camp.CampaignName
			FROM 
				customer_subscriptions sub,
				coupon map,
				generated_coupons gen,
				customers  cust,
				campaigns  camp,
				brands     brnd,
				clients    clnt
			WHERE   1=1
				AND sub.ClientId   = '$client_id'
				AND sub.CustomerId = '$customer_id'
				AND sub.PointsId   = map.PointsId
				AND sub.ClientId   = map.ClientId
				AND sub.Status     = 'ACTIVE'
				AND gen.Status     = 'PENDING'
				AND sub.PointsId   = gen.PointsId
				AND map.CouponId   = gen.CouponId
				AND sub.CustomerId = cust.CustomerId
				AND sub.ClientId  = clnt.ClientId
				AND sub.BrandId   = brnd.BrandId
				AND sub.CampaignId= camp.CampaignId
				
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
				$result_array["coupon"][] = $row;
				$counter++;
			}
			$result_array["totalrows"] = $counter;
			$result_array["status"]    = (($counter>0)?(1):(0));
			//give it back
			return ($counter == 0) ? (false) : ($result_array);
	}

 

}
?>