<?php
include_once('debugger.php');


$dmsg1 = trim($_SERVER["SCRIPT_NAME"]  );
$dmsg2 = trim($_SERVER["REQUEST_URI"]  );
$dmsg3 = trim($_SERVER["QUERY_STRING"] );
$dmsg4 = @var_export($_REQUEST,1);

//log
debug("Start .... ");
debug(sprintf('raw:1> %s', $dmsg1));
debug(sprintf('raw:2> %s', $dmsg2));
debug(sprintf('raw:3> %s', $dmsg3));
debug(sprintf('raw:4> %s', $dmsg4));
debug(sprintf('POST > %s', @var_export($_POST,1)));
debug(sprintf('GET  > %s', @var_export($_GET,1)));
debug("Done  .... ");

class ApiToken
{
	public $conn;

	public function ApiToken($conn) 
	{
		$this->conn       = $conn;
		$this->table_name = 'clients';
		
		//this is root-token-id: 0d754337454497646fea936fcd4695cdbaffb2626624a8eff4262457aef1977e
		$this->token      =  @hash('sha256', md5('#!/usr/local/bin/yuicon/multi-channel-api'));
	}
	
	public function is_valid_token()
	{
		    
			//fmt
			$client_id  = addslashes(trim($_REQUEST["clientid"]  ));
			$customer_id= addslashes(trim($_REQUEST["customerid"]));
			$api_token  = addslashes(trim($_REQUEST["apitoken"]  ));

			//res
			$retv          = array();
			$retv["status"]= 0;

			//root token, pls make it sure, only the online-CMS knows this token
			if(strlen($api_token) > 0 and $this->token === $api_token)
			{
				$retv["status"] = 1;
				return $retv;
			}
			//root token, pls make it sure, only the online-CMS knows this token
			if(0)
			{
				if($_SERVER["SERVER_ADDR"] === $_SERVER["REMOTE_ADDR"])
				{
					$retv["status"] = 1;
					return $retv;
				}
			}
			
			
			//try			
			$tsql = " AND c.ClientId = '$client_id' ";
			if($client_id <= 0)
			{
				//use customer
				$tsql = " AND c.ClientId IN (
							SELECT u.ClientId
							FROM
								customers u
							WHERE
							    u.CustomerId = '$customer_id'
						) ";					
			}
			
			//fmt sql	
			$query      = "
				SELECT c.*
				FROM clients c
				WHERE
					1=1
				    AND c.ApiToken = '$api_token'
					$tsql
			";

			debug("TOKEN-API: $query;");

			//customer
			$retv["customer"] = $this->getCustomerData($customer_id);
			
			//run
			$res = $this->conn->query($query);
			if (PEAR::isError($res)) {
				return $retv;
			}
			
			$counter = 0;
			while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
			{				
				$retv["data"][] = $row;
				$counter++;
			}
			//status
			$retv["status"]    = (($counter>0)?(1):(0));
		
			//get if customer-is-ACTIVE
			$cdata = array();
			
			//give it back
			return $retv;
	}
	
	function getCustomerData($id=0)
	{
			//fmt
			$id             = addslashes($id);
			//fmt sql	
			$query      = "
				SELECT c.*
				FROM customers c
				WHERE
					1=1
				    AND c.CustomerId = '$id'
			";

			debug("getCustomerData: $query;");

			//run
			$res = $this->conn->query($query);
			if (PEAR::isError($res)) {
				return 0;
			}
			
			//get data
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$dmp = @var_export($row,1);
			
			debug("getCustomerData: [$dmp;]");
			
			//give it back
			return ($row['status'] == 'ACTIVE')?(1):(0);
	}

}
?>
