<?php

//gtalk
include_once('init.php');


debug("cron_parse_csv() : start!!!!");

//lock
$gPORT_LOCKER = new PortLocker(17897);
if(! $gPORT_LOCKER->lock())
{
	debug("cron_parse_csv() : an instance still running!");
	exit;
}


debug("cron_parse_csv() : an instance will run now!");

$mode  = trim($ARGV[1]);
//get settings
$pdata = get_sched_post($mode);


//free
$gPORT_LOCKER->unlock();


//free
free_up();
debug("cron_parse_csv() : done!!!!");
?>