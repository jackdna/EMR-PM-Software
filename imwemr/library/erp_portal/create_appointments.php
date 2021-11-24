<?php 
include_once("../../config/globals.php");

$index = $_REQUEST['index'] ? $_REQUEST['index'] : 0;
$page = $_REQUEST['page'] ? $_REQUEST['page'] : 1;
$length = 10;
$response = array('last_page' => true, 'msg' => '');

$qryERPTotal = "Select id From schedule_appointments Where (erp_appt_id = '' OR erp_appt_id IS NULL OR erp_appt_id = '0' ) 
				AND sa_patient_app_status_id NOT IN (203,201,18,19,20) 
				AND sa_patient_id IN(Select id From patient_data Where erp_patient_id != '' ORDER BY id ASC )
				ORDER BY id ASC ";
$sqlERPTotal = imw_query($qryERPTotal) ;
$cntERPTotal = imw_num_rows($sqlERPTotal);

$qryERP = "Select id,sa_patient_id From schedule_appointments Where (erp_appt_id = '' OR erp_appt_id IS NULL OR erp_appt_id = '0' ) 
			AND sa_patient_app_status_id NOT IN (203,201,18,19,20) 
			AND sa_patient_id IN(Select id From patient_data Where erp_patient_id != '' ORDER BY id ASC )
			ORDER BY id ASC LIMIT 0, $length ";
$sqlERP = imw_query($qryERP) ;
$cntERP = imw_num_rows($sqlERP);
$counter=0;
$msg_info=array();
$erp_error=array();
if($sqlERP && $cntERP ){

    require_once(dirname(__FILE__) . "/../../library/erp_portal/appointments.php");
    $objAppointments = new Appointments();
    $next_index = ($cntERP < $length) ? $index + $cntERP : $index+$length;
    $last_page = ($cntERP < $length || $cntERP == $cntERPTotal) ? true : false;
    while( $resERP = imw_fetch_assoc($sqlERP) ) {
        try {
			$appt_id = $resERP['id'];
			$pid = $resERP['sa_patient_id'];
			// send request to Save/Update patient data at Eye Reach Patient API
			if($objAppointments) {
				$result = $objAppointments->addUpdateAppointments($appt_id,$pid);
			}

			$counter++;
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
    }

    if(count($msg_info)>0){
        // 
    } else {
        $response = array('last_page' => $last_page, 'next_index' => $next_index , 'next_page' => ($page+1), 'msg' => ($next_index)." Appointment(s) uploaded to imwemr Portal");
    }

}
else
{
    $response = array('last_page' => true, 'msg' => "All Appointment(s) already uploaded OR Patient(s) not uploaded to imwemr Portal");
}

echo json_encode($response);
die;

?>