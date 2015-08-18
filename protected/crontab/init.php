<?php
@error_reporting(E_ALL & ~( E_STRICT|E_NOTICE|E_DEPRECATED|E_USER_DEPRECATED ));
/**
|	@Filename	:	const.php
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


define('CSV_DIR','/home/chwens/public_html/unov2/gui/userfiles/csv-dst');
define('LIB_DIR','/home/chwens/public_html/unov2/apps/helpers');
define('LIB_CRN','/home/chwens/public_html/unov2/apps/crontab');
define('LIB_PHP','/home/chwens/php');


ini_set("include_path", LIB_PHP .':'. 
			ini_get("include_path") .':'.
			dirname(__FILE__).'/com.php.utils.libs:'.
			LIB_DIR. '/PHPExcel:'.
			LIB_DIR. '/phpgraphlib');
			
//misc
include_once('const.php');
include_once('cfg.php');
include_once('misc.php');

//libs
include_once('com.utils.init.php');


include_once LIB_DIR.'/PHPExcel/IOFactory.php';



//-----
//@misc
//-----


//init dbs here
global $gSqlDb,$DBOPTS;
$gSqlDb = new mySqlDbh2($DBOPTS);
$gSqlDb->dbh();


//logger-formatting
$gLoggerConf = array('append' => true,'mode' => 0666, 'timeFormat' => '%Y%m%d %H:%M:%S');

?>