#!/bin/bash
SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT")
cd $SCRIPTPATH
/usr/bin/php ../patient_referral_3.php $1
/usr/bin/php ../patient_referral_cron_additional.php $1
/usr/bin/php ../ecp_billing_additional.php $1
/usr/bin/php ../bennet_appointment_extract.php $1
