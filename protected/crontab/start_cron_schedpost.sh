#!/bin/bash
###########################################################
# Filename:
#
# Desc    :
#
# Date    : 2015-04-02
#
#
# By      : bayugitus
#
#
###########################################################
umask 002




###########################################################
# FUNCTIONS
###########################################################
function _init()
{
	ROOTD="/var/www/html/multichannel/protected/crontab"
	LOGF=${ROOTD}/log/cron_parse_csv.php-$(date '+%Y-%m-%d').log
}

function timeStamp()
{
     local pid=$(printf "%05d" $$)
     echo "[$(date)] - info - $*" >> ${LOGF}

}

###########################################################








###########################################################
# MAIN ENTRY
###########################################################

_init


#move 2 parent'shouse
cd $ROOTD

timeStamp "start"
timeStamp "==================================="

php -f ${ROOTD}/cron_parse_csv.php  "DAILY" >> $LOGF 2>/dev/null

timeStamp "ret:$?"



timeStamp "==================================="
timeStamp "done  here"



