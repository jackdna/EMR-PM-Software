<?php

include_once __DIR__ . '/../../common/conDb.php';

/**
 * Log storage based on Time stamp
 */
$log_dir = date('Y_m_d');
$log_file = date('YmdHis');


/**
 * Message Logging
 */
$log_path = dirname(__FILE__).'/../../admin/pdfFiles';
$log_path = realpath($log_path);
$log_path = $log_path.'/sync_api_log/';
$log_directory = $log_path.$log_dir;


/**
 * Create Log directory if it does not exists
 */
if( !is_dir($log_directory) )
{
    mkdir($log_directory, 0700, true);
}


/**
 * Generate Array of modifiers
 */
$modifiers = array();
$modQry = "SELECT * FROM modifiers ORDER BY modifierId ASC";

$modRes = imw_query($modQry);

if( imw_num_rows($modRes)>0)
{
    while( $modRow = imw_fetch_assoc($modRes) )
    {
		$mod_prac_code = $modRow['practiceCode'];
		$mod_description = $modRow['description'];
		$modifiers[$mod_prac_code] = $mod_description;
	}
}


/**
 * Getting Patient Confirmation ID from iASCEMR
 */
if(isset($unfinalize_confirmation_id) && $unfinalize_confirmation_id!='')
{
    $sc_confirmation_id = $unfinalize_confirmation_id;
}
else{
    $sc_confirmation_id = $pConfId;
}

/**
 * Fetch the charges
 */
$cpt_group_arr = array("1"=>"Anesthesia", "2"=>"Practice", "3"=>"Facility");
$qry = "SELECT
            sb.superbill_id, sb.bill_user_type, sb.cpt_code, sb.quantity, sb.modifier1, sb.modifier2, sb.modifier3, sb.dxcode_icd10, ds.dischargeSummarySheetId, ds.confirmation_id, ds.procedures_name, ds.procedures_code_name, ds.icd10_code, ds.icd10_name, ds.comment, wt.patient_in_waiting_id, wt.idoc_sch_athena_id, pc.dos
        FROM
            superbill_tbl sb
        INNER JOIN dischargesummarysheet ds ON (ds.confirmation_id = sb.confirmation_id AND ds.form_status = 'completed' AND ds.cpt_inte_sync_status = '0')
        INNER JOIN patientconfirmation pc ON (pc.patientConfirmationId = sb.confirmation_id)
        INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.dos = pc.dos)
        INNER JOIN patient_in_waiting_tbl wt ON (wt.patient_in_waiting_id = st.iolink_patient_in_waiting_id AND wt.idoc_sch_athena_id != '')
        WHERE
            sb.deleted = '0' AND sb.bill_user_type IN(2, 3) AND sb.confirmation_id = $sc_confirmation_id
        GROUP BY
            sb.superbill_id
        ORDER BY
            pc.dos ASC,
            pc.patientConfirmationId ASC,
			sb.bill_user_type ASC";

$res = imw_query($qry);

if( imw_num_rows($res) > 0 )
{
	/** Message wrapper */
	$data = array();

    while( $row = imw_fetch_array($res) )
    {
		$dischargeSummarySheetId 	= $row["dischargeSummarySheetId"];
		$confirmation_id 			= $row["confirmation_id"];
		$bill_user_type 			= $row["bill_user_type"];
		$cpt_code					= $row["cpt_code"];
		$dxcode_icd10				= $row["dxcode_icd10"];
		$dxcode_icd10_arr			= explode(",",$dxcode_icd10);
		$quantity 					= $row["quantity"];
		$modifier1 					= $row["modifier1"];
		$modifier2 					= $row["modifier2"];
		$modifier3 					= $row["modifier3"];
		$procedures_name 			= $row["procedures_name"];
		$procedures_name_arr 		= explode("!,!",$procedures_name);
		$procedures_code_name 		= $row["procedures_code_name"];
		$procedures_code_name_arr 	= explode("##",$procedures_code_name);
		$icd10_code 				= $row["icd10_code"];
		$icd10_code_arr 			= explode(",",$icd10_code);
		$icd10_name 				= $row["icd10_name"];
		$icd10_name_arr 			= explode("@@",$icd10_name);
		$comment 					= $row["comment"];
		$dos 						= $row["dos"];
		$inte_appt_id 				= $row["idoc_sch_athena_id"];
		$iasclink_appt_id			= $row["patient_in_waiting_id"];
		
		$pcn_arr = array();
		foreach($procedures_code_name_arr as $pcn_key => $pcn_val) {
			$pcn_arr[$pcn_val] = $procedures_name_arr[$pcn_key];
		}
		$icd_arr = array();

		foreach($icd10_code_arr as $icd_key => $icd_val) {
			$icd_arr[$icd_val] = $icd10_name_arr[$icd_key];
		}
		$cpt_code_name = $pcn_arr[$cpt_code];
		
		$dxVal = array();
		
		foreach($dxcode_icd10_arr as $dxicd_val)
		{
			$dxentry = array();
			$dxentry['DiagnosticType'] = 'ICD10';
			$dxentry['DiagnosticCode'] = $dxicd_val;
			$dxentry['DiagnosticDescription'] = $icd_arr[$dxicd_val];
			array_push($dxVal, $dxentry);
		}
		$cpt_group = $cpt_group_arr[$bill_user_type];
		
		if($tmp_confirmation_id != $confirmation_id) {
			
			$data["Charges"] = array("CptCodes"=>array());
			
			$chargePointer = &$data["Charges"]["CptCodes"];
			$chargePointer = array();

			$data["ExternalId"]				= $inte_appt_id;
			$data["DOS"]						= $dos;
			$data["iasclink_appt_id"]			= $iasclink_appt_id;
			$data["charges_confirmation_id"]	= $confirmation_id;
			$data["charges_id_primary"]		= $dischargeSummarySheetId;
			$data["Comments"]					= $comment;
		}

		$modifier_arr = array();

		$dxentry = array();
		$dxentry['DiagnosticType'] = 'ICD10';
		$dxentry['DiagnosticCode'] = $dxicd_val;
		$dxentry['DiagnosticDescription'] = 'ICD10';
		$dxentry['DiagnosticType'] = $icd_arr[$dxicd_val];
		
		$modVal = array();
		if(trim($modifier1)) {
			$modentry =array();
			$modentry["modifier"] = $modifier1;
			$modentry["description"] = $adm_mod_arr[$modifier1];
			array_push($modVal, $modentry);
		}
		if(trim($modifier2)) {
			$modentry =array();
			$modentry["modifier"] = $modifier2;
			$modentry["description"] = $adm_mod_arr[$modifier2];
			array_push($modVal, $modentry);
		}
		if(trim($modifier3)) {
			$modentry =array();
			$modentry["modifier"] = $modifier3;
			$modentry["description"] = $adm_mod_arr[$modifier3];
			array_push($modVal, $modentry);
		}
		
		$chargeEntry = array();
		$chargeEntry['CptGroup'] = $cpt_group;
		$chargeEntry['CptCode'] = $cpt_code;
		$chargeEntry['Units'] = $quantity;
		$chargeEntry['modifiers'] = $modVal;
		$chargeEntry['DiagnosticCodes'] = $dxVal;
		
		array_push($chargePointer, $chargeEntry);
		
		$tmp_confirmation_id = $confirmation_id;
	}


	/** Log the messge in files/queue */

	$log_file = $log_directory.'/charge_'.$log_file.'_'.$sc_confirmation_id.'.log';
    
	$messageData = array('api_charges_data'=>$messageData);
	$messageDataJSon = json_encode($data);

	file_put_contents($log_file, $messageDataJSon, FILE_APPEND);
	chmod($log_file, 0600);

	/**
	 * Mark the Charge entry and logged for sending
	 */
	$updtChargesQry = "UPDATE dischargesummarysheet 
						SET cpt_inte_sync_status = '1', cpt_inte_sync_date_time = '".date("Y-m-d H:i:s")."'
						WHERE confirmation_id = '".$sc_confirmation_id."'";
	
	$updtChargesRes = imw_query($updtChargesQry);

	/**
	 * Set Flag Status
	 */
	file_put_contents($log_path.'/flag.txt', 1);
}
