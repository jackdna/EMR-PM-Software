<?php

/*** Created for Michigan ***/

$sql = "SELECT `sch_app_id` FROM `superbill` WHERE `idSuperBill`=".$hl7_SuperBill_id." AND `sch_app_id` > 0 AND `test_id`>0";
$resp = imw_query($sql);

if( $resp && imw_num_rows($resp) > 0 )
{
    $hl7_appt_id = imw_fetch_assoc($resp);
    $hl7_appt_id = $hl7_appt_id['sch_app_id'];

    $sql_appt = "SELECT `sa_app_start_date`, `sa_doctor_id` FROM `schedule_appointments` WHERE `id` = ".$hl7_appt_id;
    $resp = imw_query($sql_appt);

    if( $resp && imw_num_rows($resp) > 0 )
    {
        $resp = imw_fetch_assoc($resp);

        $appt_date = $resp['sa_app_start_date'];
        $appt_physician = $resp['sa_doctor_id'];


        $testingPhysicianIds = array(20, 124, 125, 127, 128, 129, 130, 152, 153);

        if( in_array((int)$appt_physician, $testingPhysicianIds, true) )
        {
            $non_test_appt_flag = false;
            
            $sql_appt_list = "SELECT `sa_doctor_id` FROM `schedule_appointments` WHERE `sa_app_start_date` = '".$appt_date."' AND `sa_patient_id`= ".$_SESSION['patient']." AND `sa_patient_app_status_id` != 18";
            $resp_appt_list = imw_query($sql_appt_list);

            if( $resp_appt_list && imw_num_rows($resp_appt_list) )
            {
                while( $appt_resp_row = imw_fetch_assoc($resp_appt_list) )
                {
                    if( !in_array((int)$appt_resp_row['sa_doctor_id'], $testingPhysicianIds, true) )
                    {
                        $non_test_appt_flag = true;
                        break;
                    }
                }
            }

            if( $non_test_appt_flag === false )
            {
                require_once( dirname(__FILE__).'/../../../hl7sys/old/CLS_makeHL7.php');
		$makeHL7    =   new makeHL7;
                
                $makeHL7->log_HL7_message($hl7_SuperBill_id,'Detailed Financial Transaction','TEST_SUP_DFT');
            }
        }
    }
}
?>
