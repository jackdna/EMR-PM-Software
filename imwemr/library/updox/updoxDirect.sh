#!/bin/bash

#The script must be called through BASH only
if [ ! "$BASH_VERSION" ]
then
	echo "\e[91mThis is a bash script."
	echo "So please call this through bash only.\e[39m"
	exit
fi

# Get valid PHP service name
if [ -z "$1" ]
    then
        echo "Pleae provide PHP DAEMON"
        exit
fi
if [[ $1 != "php" && $1 != "php73" ]]
then
	echo "Current acceptable values for PHP DAEMON are 'php' and 'php73' only"
	exit
fi
PHP_DAEMON=$1

# imwemr Practice name must be supplied by the user.
# User intelligence is expected here to provide valid practice name.
# Because, this is supposed to the executed by the trainned admins only or those who know what exactly they are doing and understand the seriousness if it is done otherwise
if [ -z "$2" ]
    then
        echo "Pleae provide practice name"
        exit
fi
PRACTICENAME=$2

# Operation to be done the particular instance
if [[ $3 != "directDl" && $3 != "directStatusUpd" ]]
then
	echo "Please provide valid updox operation. 'directDl' or 'directStatusUpd'"
	exit
fi
OPERATION=$3

# Action related to the particular script instance
if [[ $4 != "start" && $4 != "stop" && $4 != "status" && $5 != "restart" ]]
then
	echo "Please provide valid action. 'start', 'stop' or 'status'"
	exit
fi
ACTION=$4

SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")
DAEMON="/usr/bin/$1"

INBOUND_DIRECT_RECEIVER="$SCRIPTPATH/inboundDirect.php"
DIRECT_STATUS_UPDATER="$SCRIPTPATH/outboundDirectStatusUpdate.php"

INBOUND_DIRECT_QUEUE="$SCRIPTPATH/../../data/$PRACTICENAME/updoxPendingInboundDirect"
OUTBOUND_STATUS_UPDATE_QUEUE="$SCRIPTPATH/../../data/$PRACTICENAME/updoxPendingStatusUpdate"

#############################################################
###################### HEalthChecks ############################
#############################################################
if [[ ! -e $INBOUND_DIRECT_QUEUE ]]
then
	mkdir -m 700 "$INBOUND_DIRECT_QUEUE"
	chown apache.apache "$INBOUND_DIRECT_QUEUE"
fi

if [[ ! -e $OUTBOUND_STATUS_UPDATE_QUEUE ]]
then
	mkdir -m 700 "$OUTBOUND_STATUS_UPDATE_QUEUE"
	chown apache.apache "$OUTBOUND_STATUS_UPDATE_QUEUE"
fi

#############################################################
###################### Functions ############################
#############################################################

# Start service to monitor direct download queue
# It will process the message as soon as it is received in the queue
directDl() {
	while true
	do
	listOfFiles=`ls -trp "$INBOUND_DIRECT_QUEUE" | grep -v /`
		
		for i in $listOfFiles
		do
			$DAEMON $INBOUND_DIRECT_RECEIVER $PRACTICENAME $i > /dev/null 2>&1
			sleep 1
				unlink "$INBOUND_DIRECT_QUEUE/$i"
		done
		sleep 5
	done
}

# Start service to monitor direct outbound status update queue
# It will start monitoring the status immediately and keep on repeating untill the direct message is dispatched or failed
directStatusUpd() {
	while true
	do
	listOfFiles=`ls -trp "$OUTBOUND_STATUS_UPDATE_QUEUE" | grep -v /`
		
		for i in $listOfFiles
		do
			$DAEMON $DIRECT_STATUS_UPDATER $PRACTICENAME $i > /dev/null 2>&1
			sleep 1
				unlink "$OUTBOUND_STATUS_UPDATE_QUEUE/$i"
		done
		sleep 5
	done
}

#Get Process Id for the operation
PID=0
getPid() {
	PID=`ps -eaf | grep "updoxDirect.sh $PHP_DAEMON $PRACTICENAME $OPERATION start" | grep -v $$ | grep -v grep | awk '{print $2}'`
}
getPid

# Stop Queue processor
stop() {
	if [[ $PID -eq 0 ]]
	then
		echo "$OPERATION not running"
	else
		kill -9 $PID
		echo "$OPERATION not stopped"
	fi
}

# Get status of Queue processor
status() {
	if [[ $PID -eq 0 ]]
	then
		echo "$OPERATION not running"
	else
		echo "$OPERATION is running"
	fi
}


# Code this point forward must be only if called directly through terminal
if [ $SHLVL -gt 2 ]
then
	$OPERATION
	exit
fi


case "$ACTION" in
stop)
        stop
		exit
        ;;
status)
        status
		exit
        ;;
restart)
        stop $1 $2
        sleep 65
        ;;
esac

if [[ $PID -ne 0 ]]
then
	echo "$OPERATION already running"
	exit
fi

nohup ./updoxDirect.sh $PHP_DAEMON $PRACTICENAME $OPERATION $ACTION > /dev/null 2>&1 &
echo "$OPERATION started"