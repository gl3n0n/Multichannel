<?php

//gtalk
include_once('init.php');


debug("sched_post() : start!!!!");

//lock
$gPORT_LOCKER = new PortLocker(17897);
if(! $gPORT_LOCKER->lock())
{
	debug("sched_post() : an instance still running!");
	exit;
}


debug("sched_post() : an instance will run now!".@var_export($argv,true));

$mode  = trim($argv[1]);
//get settings
$pdata = get_sched_post($mode);


//free
$gPORT_LOCKER->unlock();


//free
free_up();
debug("sched_post() : done!!!!");
?>