<?php
/**
|	@Filename	:	misc.php
|	@Description	:	all important methods/subs
|                               
|	@Date		:	2009-04-25
|	@Ver		:	Ver 0.01
|	@Author		:	bayugyug@gmail.com
|
|
|       @Modified Date  :
|       @Modified By    :
|    
**/





function doIt()
{
	//globals here
	global $gSqlDb;

	//fmt-params

	//select
	$sql = " CALL sp_generate_schedpost ";
	$res = $gSqlDb->query($sql, "doIt() : ERROR : $sql");

	//total-rows
	$is_ok = $gSqlDb->numRows($res);
	
	//get data
	if($is_ok>0)
	{
		debug("doIt() : INFO : STORED-PROC[ $sql => $is_ok ]");
	}
	$data  = array();
	$sdata = array('exists' => intval($is_ok));
	

	//select
	$sql = " SELECT * FROM push_log WHERE status = 0 ";
	$res = $gSqlDb->query($sql, "doIt() : ERROR : $sql");

	//total-rows
	$is_ok = $gSqlDb->numRows($res);

	debug("doIt() : INFO : get-all[ $sql => $is_ok ]");
	
	//get data
	if($is_ok>0)
	{
		while($strow = $gSqlDb->getAssoc($res))
		{

			$vret =	update_stats($strow["id"],$strow["status"]+1);
			$uret = processIt($strow);
			$vret =	update_stats($strow["id"],$strow["status"]+2);
		}

	}
	
	
	//free-up
	if($res) $gSqlDb->free($res);
	
	//give it back ;-)
	return $sdata;
	
}


//get data
function processIt($pdata=array())
{
	//globals here
	global $gSqlDb;

	//fmt-params
	debug("processIt() : INFO : dat> ".@var_export($pdata,1));
	
	if(1){
		$ret = utils_mail_send(MULTI_REPLY,
				$pdata["email_address"], 
				MULTI_SUBJECT, 
				$pdata["msg"]);

		debug("processIt() : mail> $ret");
	}


	//give it back ;-)
	return $ret;
	
}

 


//upd8 it
function update_stats($id=0,$st=9)
{
	//globals here
	global $gSqlDb;
	

	//fmt-params
	$id    = addslashes(trim($id));
	$st    = addslashes(trim($st));

	//exec
	$sql = "UPDATE push_log
		SET 
			status    = '$st',
			dt_sent   = Curdate(),
			tm_sent   = Now()
		WHERE 
			id = '$id' 
		LIMIT 1";
		  
		  
	$res   = $gSqlDb->exec($sql, "update_stats() : ERROR : $sql");
	$is_ok = $gSqlDb->updRows($res);

	debug("update_stats() : INFO : [ $sql => $res => $is_ok ]");

	//free-up
	if($res) $gSqlDb->free($res);

	
	//give it back ;-)
	return $is_ok;
	
}





//free
function free_up()
{
	//globals here
	global $gSqlDb;
	
	//free
	if($gSqlDb)     $gSqlDb->close();
	
	debug("free_up() : INFO : [ free! ]");
	
	//give it back ;-)
}

//msg
function status_msg($st='', $msg='')
{
	debug("status_msg() : INFO : [ $st : $msg ]");
	
	echo strtoupper($st) . " : $msg";
}
?>
