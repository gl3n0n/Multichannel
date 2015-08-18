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
	LOGF=${ROOTD}/log/schedpost.php-$(date '+%Y-%m-%d').log
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

/usr/bin/php -f ${ROOTD}/schedpost.php  "DAILY" >> $LOGF 2>/dev/null

timeStamp "ret:$?"



timeStamp "==================================="
timeStamp "done  here"



