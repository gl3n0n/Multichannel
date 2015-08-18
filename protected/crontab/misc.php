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






//get data
function get_sched_post($mode='DAILY')
{
	//globals here
	global $gSqlDb;

	//fmt-params
	$order    = addslashes(trim($pdata['order']));
	$limit    = addslashes(trim($pdata['limit']));
	$mode     = addslashes(trim($mode));

	//select
	$sql = " SELECT 
			SQL_CALC_FOUND_ROWS 
			* 
		 FROM scheduled_post
		 WHERE 1=1 AND mode = '$mode'
		     $order
		     $limit
	 ";
	$res   = $gSqlDb->query($sql, "get_sched_post() : ERROR : $sql");

	//total-rows
	$is_ok = $gSqlDb->numRows($res);
	$data  = array();
	$sdata = array('exists' => intval($is_ok));
	
	//get data
	if($is_ok>0)
	{
		while($strow = $gSqlDb->getAssoc($res))
		{
		    $data[] = $strow;
		}
	}
	
	//save
	$sdata['data'] = $data;
	
	debug("get_sched_post() : INFO : [ $sql => $is_ok ]");
	
	//free-up
	if($res) $gSqlDb->free($res);
	
	//give it back ;-)
	return $sdata;
	
}

 
//upd8 it
function set_csv_summary($pdata=null)
{
	//globals here
	global $gSqlDb;
	

	//fmt-params
	$id      = addslashes(trim($pdata['id']));
	$status  = addslashes(@intval(trim($pdata['status'])));
	$mesg    = addslashes(trim($pdata['desc']));


	//exec
	$sql = "UPDATE csv_upload 
		SET 
			cron_code = '$status',
			cron_desc = '$mesg',
			cron_date = Now()
		WHERE 
			id = '$id' 
		LIMIT 1";
		  
		  
	$res   = $gSqlDb->exec($sql, "set_csv_summary() : ERROR : $sql");
	$is_ok = $gSqlDb->updRows($res);

	debug("set_csv_summary() : INFO : [ $sql => $res => $is_ok ]");

	//free-up
	if($res) $gSqlDb->free($res);

	
	//give it back ;-)
	return $is_ok;
	
}




//upd8 it
function update_stats($pdata=null)
{
	//globals here
	global $gSqlDb;
	

	//fmt-params
	$id    = addslashes(trim($pdata['id']));
	$tot   = addslashes(@intval(trim($pdata['tot'])));
	$oks   = addslashes(@intval(trim($pdata['oks'])));
	$err   = addslashes(@intval(trim($pdata['err'])));

	//exec
	$sql = "UPDATE csv_upload 
		SET 
			total_rows = '$tot',
			total_oks  = '$oks',
			total_err  = '$err',
			cron_date  = Now()
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