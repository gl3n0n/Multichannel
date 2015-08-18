<?php
/**
|	@Filename	:	cfg.php
|	@Description	:	all global vars
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




// prod or sandbox
$gDev     = 1;
$gToday   = date("Y-m-d");
$gAppName = "MULTI_CHANNEL_SCHEDPOST";
$gDebug   = 0;
$gLogDebug= 1;

/*
|    1 = sandbox    or devel
|    0 = production or live
*/

/*

//local
$db['default']['hostname'] = "localhost";
$db['default']['username'] = "chwens_uno";
$db['default']['password'] = "uno888";
$db['default']['database'] = "chwens_uno";
*/

if(1 == $gDev)
{
	//sandbox
	define('WEBLOG' , "log/$gToday.$gAppName.log");	
	$DBOPTS['dbhost'] = "localhost";
	$DBOPTS['dbuser'] = "multichannel";
	$DBOPTS['dbpass'] = "multichannel";
	$DBOPTS['dbname'] = "multichannel";

	
}
else
{
	//prod
	define('WEBLOG' , "log/$gToday.$gAppName.log");	
	$DBOPTS['dbhost'] = "localhost";
	$DBOPTS['dbuser'] = "multichannel";
	$DBOPTS['dbpass'] = "multichannel";
	$DBOPTS['dbname'] = "multichannel";

	
}

?>