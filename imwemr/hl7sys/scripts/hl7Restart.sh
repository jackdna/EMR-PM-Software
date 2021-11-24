#!/bin/bash
practice=$1
rootDir=imwemr
tsPath=/var/www/html/$rootDir/data/$practice/hl7Flags
currTime=`date +"%s"`
getHl7Time=`cat $tsPath/lastReceivedTime.log`
diffTime=`expr $currTime - $getHl7Time`

if [ ! -d "/var/www/html/$rootDir/config/$1" ] || [ "$1" = '' ]; then
        if [ "$1" = '' ]; then
                echo -e "Please provide a practice name"
        else
                echo "$1 Practice does not exists"
        fi

        exit
fi

if [ $diffTime -gt 600 ]; then
        /usr/bin/hl7receiver2 restart $practice
		echo "service restarted at: "`date` >> $tsPath/restartReceiver.log
		echo `date +"%s"` > $tsPath/lastReceivedTime.log
else
        echo "Exiting scripts."
        exit
fi
