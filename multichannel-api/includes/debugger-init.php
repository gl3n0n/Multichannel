<?php
/**
|
|  @filename    : 
|
|  @description : 
|
|  @version     : 0.001
|
|  @author      : bayugyug@gmail.com
|
|  @date        : 
|
|
|
|  @modified    :
|  @modified-by :
|  @modified-ver:
|
|              
**/



function dumper($msg='')
{
	global $gDebugObj,$gDebug,$gLogDebug,$gToday,$gIPAddress,$gAppName,$gRootDir;
	if($gDebugObj == null)
	{
		// prod or sandbox
		$gToday     = @date("Y-m-d");
		$gRootDir   = "/var/www/html/multichannel-api";
		$gLogDebug  = 1;
		$gIPAddress = strlen(trim($_SERVER["REMOTE_ADDR"]))>0?($_SERVER["REMOTE_ADDR"]):('127.0.0.1');
		$gAppName   = sprintf("%s-DUMPER-%s",gethostname(),$gIPAddress);
		$gWebLog    = sprintf("%s/logs/%s-%s.log",$gRootDir,$gToday,$gAppName);	
		$gDebugObj  = new Debugger($gWebLog);
	}
	//log
	$gDebugObj->debug($msg);
}

function debug($msg='')
{
	dumper($msg);
}

//init here the debugger
global $gDebugObj,$gDebug,$gLogDebug,$gToday,$gIPAddress,$gAppName,$gRootDir;
$gRootDir   = "/var/www/html/multichannel-api";
$gToday     = @date("Y-m-d");
$gDebug     = 1;
$gLogDebug  = 1;
$gIPAddress = strlen(trim($_SERVER["REMOTE_ADDR"]))>0?($_SERVER["REMOTE_ADDR"]):('127.0.0.1');
$gAppName   = sprintf("%s-DUMPER-%s",gethostname(),$gIPAddress);
$gWebLog    = sprintf("%s/logs/%s-%s.log",$gRootDir,$gToday,$gAppName);	
$gDebugObj  = new Debugger($gWebLog);


?>