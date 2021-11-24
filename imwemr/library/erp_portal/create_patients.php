<?php 
include_once("../../config/globals.php");

$index = $_REQUEST['index'] ?? 0;
$page = $_REQUEST['page'] ?? 1;
$length = 10;
$response = array('last_page' => true, 'msg' => '');

$qryERPTotal = "Select id From patient_data Where (erp_patient_id = '' OR erp_patient_id IS NULL OR erp_patient_id = '0' ) ORDER BY id ASC ";
$sqlERPTotal = imw_query($qryERPTotal) ;
$cntERPTotal = imw_num_rows($sqlERPTotal);

$qryERP = "Select id From patient_data Where (erp_patient_id = '' OR erp_patient_id IS NULL OR erp_patient_id = '0' ) ORDER BY id ASC LIMIT 0, $length ";
$sqlERP = imw_query($qryERP) ;
$cntERP = imw_num_rows($sqlERP);
$counter=0;
$msg_info=array();
$erp_error=array();
if($sqlERP && $cntERP ){

    require_once(dirname(__FILE__) . "/../../library/erp_portal/patients.php");
    $objPt = new Patients();
    $next_index = ($cntERP < $length) ? $index + $cntERP : $index+$length;
    $last_page = ($cntERP < $length || $cntERP == $cntERPTotal) ? true : false;
    while( $resERP = imw_fetch_assoc($sqlERP) ) {
        try {
			$pid = $resERP['id'];
			// send request to Save/Update patient data at Eye Reach Patient API
			if($objPt) {
				$result = $objPt->addUpdatePatient($pid);
			}

			$counter++;
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
        
    }

    if(count($msg_info)>0){
        // 
    } else {
        $response = array('last_page' => $last_page, 'next_index' => $next_index , 'next_page' => ($page+1), 'msg' => ($next_index)." Patient(s) uploaded to imwemr Portal");
    }

}
else
{
    $response = array('last_page' => true, 'msg' => "All Patient(s) already uploaded to imwemr Portal");
}

echo json_encode($response);
die;

?>