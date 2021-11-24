<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(1200);
ini_set('memory_limit', '1024M');
include_once("common/conDb.php");
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

$rootServerPath = $_SERVER["DOCUMENT_ROOT"];
$outbound_dir_name = "admin/pdfFiles/outbound_vcna";
$archive_dir_name = "admin/pdfFiles/outbound_archive";
$sftp_dir_name = VCNA_LOC_CODE;
$inbound_dir_name = "inbound_vcna";


// creating outbound VCNA directory if not exists
if(!is_dir($rootServerPath."/".$surgeryCenterDirectoryName."/".$outbound_dir_name)){		
	mkdir($rootServerPath."/".$surgeryCenterDirectoryName."/".$outbound_dir_name);
}

// creating outbound VCNA archive directory if not exists
if(!is_dir($rootServerPath."/".$surgeryCenterDirectoryName."/".$archive_dir_name)){		
	mkdir($rootServerPath."/".$surgeryCenterDirectoryName."/".$archive_dir_name);
}

// Log File Name
$log_file_name=$rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.$outbound_dir_name.'/log.txt';

if( constant("VCNA_EXPORT_ENABLE") <> 'YES')
{
	$fObj = fopen($log_file_name,'a+');
	$loginMsg = "Log started Date Time ".date("m-d-Y h:i:s A");
	$erroMsg = "VCNA Export Not enabled !!!";
	fwrite($fObj, $loginMsg." \n");
	fwrite($fObj, $erroMsg." \n\r");
	fclose($fObj);
	die;
}

if( empty($sftp_dir_name) ) {
	$fObj = fopen($log_file_name,'a+');
	$loginMsg = "Log started Date Time ".date("m-d-Y h:i:s A");
	$erroMsg = "VCNA Location code missing !!!";
	fwrite($fObj, $loginMsg." \n");
	fwrite($fObj, $erroMsg." \n\r");
	fclose($fObj);
	die;
}

$sel_date = (isset($_REQUEST["sel_date"]) && $_REQUEST["sel_date"] <> '0000-00-00') ? $_REQUEST["sel_date"] : '';
if(!$sel_date) { 
	$r = $objManageData->loadSettings('finalizeDays');
	$f_days = (int) $r['finalizeDays'];
	if( $f_days) $sel_date = date('Y-m-d', strtotime('-'.$f_days.' days', strtotime(date('Y-m-d'))));
	else $sel_date = date('Y-m-d');
}
$showDate = date("m-d-Y",strtotime($sel_date));

// Start Collecting supplies categories 
$suppArr = array();
$q =imw_query("select TRIM(p.name) as supp_name, TRIM(sc.name) as supp_cat_name from predefine_suppliesused p join supply_categories sc on p.cat_id = sc.id ") or die('Error at Line No.('.(__LINE__).') : '.imw_error());
while($r = imw_fetch_assoc($q)){
	$suppArr[$r['supp_name']] = $r['supp_cat_name'];
}

// Start Collecting Procedure  
$procArr = array();
$qProcInfo=imw_query("select p.procedureId as proc_id, trim(p.name) as proc_name, p.code as proc_code, trim(p.procedureAlias) as proc_alias, p.catId as cat_id, trim(pc.name) as cat_name, pc.isMisc as is_misc, pc.isInj as is_inj from procedures p join procedurescategory pc on p.catId = pc.proceduresCategoryId ORDER BY p.code ASC, p.name ASC ") or die('Error at Line No.('.(__LINE__).') : '.imw_error());
while($procInfo = imw_fetch_assoc($qProcInfo)){
	$procArr[$procInfo['proc_id']] = $procInfo;
}

// Start Collecting ICD9 Codes 
$icd9Arr = array();
$qIcd9=imw_query("select diag_id, diag_code from diagnosis_tbl dt ") or die('Error at Line No.('.(__LINE__).') : '.imw_error());

while($rIcd9 = imw_fetch_assoc($qIcd9)){
	list($icd9_code,$icd9_desc) = explode(',',$rIcd9['diag_code']);
	$icd9Arr[$rIcd9['diag_id']] = $icd9_code;
}

function get_proc_detail($proc_id,$proc_name){
	global $procArr;
	$proc_id = (int) $proc_id;
	if( $proc_id > 0) {
		$proc_info = $procArr[$proc_id];
		if( !empty($proc_name) && $proc_info['proc_name'] <> trim($proc_name))
			$proc_info['proc_name'] = $proc_name;
		
		return $proc_info;
	}
	return false;	
}

function upload_file_curl($src_dir,$loc_code,$file_name){
	
	if( !$loc_code || !$file_name ||  !file_exists($src_dir.$file_name) ) 
		return false;
	
	$url = 'https://imwdemo.mednetworx.com/vcna_demo/up.php';
	$filename = $loc_code."/".$file_name;
	$filedata = file_get_contents($src_dir.$file_name);
	$filesize = sizeof($filedata);
	
	if ($filedata != '') {
			$headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
			$postfields = array("filedata" => "$filedata", "filename" => $filename);
			$ch = curl_init($url);
			$options = array(
					CURLOPT_HEADER => true,
					CURLOPT_POST => 1,
					CURLOPT_HTTPHEADER => $headers,
					CURLOPT_POSTFIELDS => $postfields,
					CURLOPT_INFILESIZE => $filesize,
					CURLOPT_RETURNTRANSFER => true
			); // cURL options
			curl_setopt_array($ch, $options);
			$data = curl_exec($ch);
			if(!curl_errno($ch)) { 
				$info = curl_getinfo($ch);
				if ($info['http_code'] == 200)
					return true;
			}
			curl_close($ch);
	}
	
	return false;
}


//pc.tertiary_site as ter_site_id,
// start getting appointment data
$qry = "SELECT pc.patientConfirmationId as appt_id, st.patient_status as appt_status, pc.ascId as asc_id, pc.patientId as patient_id,
							 pc.dos, pc.surgery_time, users.usersId as surgeon_id, concat(users.lname,', ',users.fname,if(TRIM(users.mname) <> '',' ',''),TRIM(users.mname)) as surgeon_name,
							 TRIM(users.lname) as surgeon_lname,TRIM(users.fname) as surgeon_fname,TRIM(users.mname) as surgeon_mname,
							 pc.anes_NA as anes_na, ua.usersId as anes_id, concat(ua.lname,', ',ua.fname,if(TRIM(ua.mname) <> '',' ',''),TRIM(ua.mname)) as anes_name,
							 TRIM(ua.lname) as anes_lname,TRIM(ua.fname) as anes_fname,TRIM(ua.mname) as anes_mname,
							 pc.patient_primary_procedure_id as pri_proc_id, pc.patient_primary_procedure as pri_proc_name,
							 pc.patient_secondary_procedure_id as sec_proc_id, pc.patient_secondary_procedure as sec_proc_name,
							 pc.patient_tertiary_procedure_id as ter_proc_id, pc.patient_tertiary_procedure as ter_proc_name,
							 pc.cost_procedure_id as cost_proc_id, pc.cost_procedure_name as cost_proc_name,
							 pc.supply_cost, pc.labor_cost, pc.surgeon_time_in_mins, pc.surgery_time_in_mins,
							 pc.site as pri_site_id, pc.secondary_site as sec_site_id, 
							 st.checked_in_time, st.checked_out_time, orr.model,  
							 if(p.catId = 2, TIME(lpt.proc_start_time),if(pc.prim_proc_is_misc,inj.startTime,orr.surgeryTimeIn)) as surgery_start_time,
							 if(p.catId = 2, TIME(lpt.proc_end_time),if(pc.prim_proc_is_misc,inj.endTime,orr.surgeryTimeOut)) as surgery_end_time,
							 if(p.catId = 2, TIME(lpt.proc_start_time),if(pc.prim_proc_is_misc,inj.startTime,orr.surgeryStartTime)) as surgeon_start_time,
							 if(p.catId = 2, TIME(lpt.proc_end_time),if(pc.prim_proc_is_misc,inj.endTime,orr.surgeryEndTime)) as surgeon_end_time,
							 vs.vision_20_40, vs.complication, la.startTime as anes_start_time, la.stopTime as anes_stop_time,
							 ds.procedures_name, ds.procedures_code, ds.diag_ids as icd9_ids, ds.diag_names as icd9_names, ds.icd10_code, ds.icd10_name 
					FROM patientconfirmation pc
					INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.patient_status!='Canceled')
					INNER JOIN users ON(users.usersId = pc.surgeonId)
					INNER JOIN dischargesummarysheet ds ON(ds.confirmation_id=pc.patientConfirmationId AND (ds.form_status='completed' OR ds.form_status='not completed'))
					
					LEFT JOIN procedures p On pc.patient_primary_procedure_id = p.procedureid
					LEFT JOIN users ua ON(ua.usersId = pc.anesthesiologist_id)
					
					LEFT JOIN operatingroomrecords orr ON pc.patientConfirmationId=orr.confirmation_id
					LEFT JOIN laser_procedure_patient_table lpt ON pc.patientConfirmationId=lpt.confirmation_id 
					LEFT JOIN injection inj ON pc.patientConfirmationId=inj.confirmation_id   
					LEFT JOIN vision_success vs ON pc.patientConfirmationId = vs.confirmation_id
					LEFT JOIN localanesthesiarecord la ON pc.patientConfirmationId = la.confirmation_id
					LEFT JOIN (SELECT count(superbill_id) as SB_COUNT, confirmation_id From superbill_tbl GROUP BY confirmation_id HAVING count(superbill_id) > 0 ) SB ON SB.confirmation_id = pc.patientConfirmationId
					WHERE pc.cost_procedure_id > 0 
					AND pc.vcna_export_status <> 1
					AND pc.finalize_status='true'
					AND pc.surgeonId <> 0
					AND pc.dos = '".$sel_date."'
					AND SB.SB_COUNT > 0 
					ORDER BY users.usersId ASC, pc.dos ASC, ds.dischargeSummarySheetId DESC";


$sql = imw_query($qry) or die ('Error found at line no. '.(__LINE__).': '.imw_error());
$cnt = imw_num_rows($sql);
$json_data = array();
while( $row = imw_fetch_assoc($sql) ){
	
	array_map('stripslashes',$row);
	
	if( !array_key_exists($row['dos'],$json_data)) $json_data[$row['dos']];
	
	$confirm_id = $row['appt_id'];
	
	// start getting procedures details
	$row['pri_proc'] = get_proc_detail($row['pri_proc_id'],$row['pri_proc_name']);
	$row['sec_proc'] = get_proc_detail($row['sec_proc_id'],$row['sec_proc_name']);
	$row['ter_proc'] = get_proc_detail($row['ter_proc_id'],$row['ter_proc_name']);
	$row['cost_proc'] = get_proc_detail($row['cost_proc_id'],$row['cost_proc_name']);
	// End getting procedures details
	
	
	// Start collecting discharge procedure data
	$discharge_data = array();
	$disCodeArr = explode(',',$row['procedures_code']);
	$disNameArr = explode('!,!',$row['procedures_name']);
	if( is_array($disCodeArr) && count($disCodeArr) > 0 ){
		foreach($disCodeArr as $k => $d) {
			$discharge_data[] = get_proc_detail($d,$disNameArr[$k]);
		}
	}
	// End collecting discharge procedure data
	
	
	// ICD9 codes
	if( $row['icd9_ids']) {
		$icd9IdArr = explode(',',$row['icd9_ids']);
		$icd9CodeArr = array();
		if( is_array($icd9IdArr) && count($icd9IdArr) > 0 ){
			foreach($icd9IdArr as $i){
				$icd9CodeArr[] = $icd9Arr[$i];
			}
		}
		$icd9CodeStr = implode(',',$icd9CodeArr);
		$row['icd9_code'] = $icd9CodeStr;
		$row['icd9_code_name'] = $row['icd9_names'];
		$row['icd_code_type'] = 'icd9';
	} 
	else {
		$row['icd_code_type'] = 'icd10';
	}
	
	// Start collecting supplies data for each appointment
	/*$q = "Select * From operatingroomrecords_supplies Where confirmation_id = '".$confirm_id."' And displayStatus = 1 AND suppChkStatus=1";
	$s = imw_query($q) or die ('Error found at line no. '.(__LINE__).': '.imw_error());
	$supplies_data = array();
	$model_data = array();
	while( $r = imw_fetch_assoc($s)) {
		$name = trim($r['suppName']);
		if( $r['suppQtyDisplay'] && $r['suppChkStatus']) $qty = (int) str_replace('X','',$r['suppList']);
		elseif(!$r['suppQtyDisplay'] && $r['suppChkStatus'] ) $qty = 1;
		//else $qty = 0;
		
		$supp_cat = $suppArr[$name];
		if( $)
		$supplies_data[] = array('name' => $name, 'qty' => $qty, 'cost' => '', 'cat_name' => $supp_cat);
	}*/
	
	// Start Collecting model data
	/*$model = $objManageData->relpaceNewLine($row['model']);
	if(stripos($model,'~~')!==false) {
		$modelArr = explode('~~',$model);
	}else {
		$modelArr = explode('\n',$model);
	}
	$model_data = array();
	foreach($modelArr as $model_name) {
		$model_name 	= trim($model_name);
		if( $model_name ) $model_data[] = array('name' => $model_name, 'qty' => 1);
	}*/
	
	// start collecting supply and model data from surgery cost table
	$q = "Select item_type, item_name, item_cost, item_qty, item_total_cost From surgery_cost Where confirmation_id = '".$confirm_id."' And item_type !='Labor' ";
	$s = imw_query($q) or die ('Error found at line no. '.(__LINE__).': '.imw_error());
	$supplies_data = array();
	$model_data = array();
	while( $r = imw_fetch_assoc($s)) {
		$name = trim($r['item_name']);
		if( $r['item_type'] == 'Supply Used') {
			$supp_cat = $suppArr[$name];
			$supplies_data[] = array('item_name' => $name, 'item_qty' => $r['item_qty'], 'item_cost' => $r['item_cost'], 'total_cost' => $r['item_total_cost'],  'cat_name' => $supp_cat);
		}
		else if( $r['item_type'] == 'Model') 
			$model_data[] = array('item_name' => $name, 'item_qty' => $r['item_qty'], 'item_cost' => $r['item_cost'], 'total_cost' => $r['item_total_cost']);
	}
	
	
	
	// Start Collecting Medication data from Health Questionnaire
	$med_data = array();
	//prescription_medication_last_dose_taken as last_taken
	$f = 'prescription_medication_name as med_name, prescription_medication_desc as dosage, prescription_medication_sig as sig ';
	$q = "Select ".$f." From patient_prescription_medication_healthquest_tbl Where confirmation_id = ".$confirm_id." ";
	$s = imw_query($q) or  die ('Error found at line no. '.(__LINE__).': '.imw_error());
	$c = imw_num_rows($s);
	if( $c > 0 ) {
		while( $r = imw_fetch_assoc($s) ) {
			$med_data[] = array('med_name'=>$r['med_name'],'dosage'=>$r['dosage'],'sig'=>$r['sig']);
		}
	}
	
	// Start Collecting Allergies Data
	$allergies_data = array();
	$f = 'allergy_name as name, reaction_name as reaction';
	$q = "Select ".$f." From patient_allergies_tbl Where patient_confirmation_id = ".$confirm_id." ";
	$s = imw_query($q) or  die ('Error found at line no. '.(__LINE__).': '.imw_error());
	$c = imw_num_rows($s);
	if( $c > 0 ) {
		while( $r = imw_fetch_assoc($s) ) {
			$allergies_data[] = array('name'=>$r['name'],'reaction'=>$r['reaction']);
		}
	}
	
	
	// JSON array to merge all data values
	$json_data[$row['dos']][$confirm_id]['encounter_data'] = $row;
	$json_data[$row['dos']][$confirm_id]['discharge_proc_data'] = $discharge_data;
	$json_data[$row['dos']][$confirm_id]['supplies_data'] = $supplies_data;
	$json_data[$row['dos']][$confirm_id]['model_data'] = $model_data;
	$json_data[$row['dos']][$confirm_id]['med_data'] = $med_data;
	$json_data[$row['dos']][$confirm_id]['allergies_data'] = $allergies_data;	
}


// Start creating JSON data file for each DOS
$arrDos = array_keys($json_data);
$arrConfIds = array();
if( is_array($arrDos) && count($arrDos > 0 ) ){
	foreach($arrDos as $dos ){
		// json file name
		$file_name  = 'vcna_info_'.str_replace('-','_',$dos);
		$file_path = $rootServerPath."/".$surgeryCenterDirectoryName."/".$outbound_dir_name.'/'.$file_name.'.json';
		
		// unlink file if exists
		if(file_exists($file_path)) { @unlink($file_path); }
		
		// open file
		$fObj = fopen($file_path,'w');
		//fwrite($fObj, json_encode($json_data[$dos],JSON_PRETTY_PRINT));
		fwrite($fObj, json_encode($json_data[$dos]));
		fclose($fObj);
		
		$arrConfIds[$file_name] = array_keys($json_data[$dos]);
		
		// Updating in database
		$ptIds = implode(',',$arrConfIds[$file_name]);
		if(!$ptIds) $ptIds = 0;
		
		$q = "update patientconfirmation set vcna_export_status = 1 where patientConfirmationId In (".$ptIds.") ";
		$s = imw_query($q) or die(imw_error());
		$a = imw_affected_rows();
		
		
		// Start uploading file to VCNA server.
		
		if( upload_file_curl($rootServerPath."/".$surgeryCenterDirectoryName."/".$outbound_dir_name.'/',$sftp_dir_name,$file_name.'.json') )
		{
			// move file to archive dir
			$src_path = $rootServerPath."/".$surgeryCenterDirectoryName."/".$outbound_dir_name.'/'.$file_name.'.json';
			$dest_path = $rootServerPath."/".$surgeryCenterDirectoryName."/".$archive_dir_name.'/'.$file_name.'.json';
		
			if( file_exists($src_path) ) $fm = rename($src_path,$dest_path);
		}
		
	}
}

// End creating JSON data file for each DOS

$loginMsg = "Log started Date Time for DOS ".$showDate." is: ".date("m-d-Y h:i:s A");
$schApptMsg = "Total number of appointment for VCNA is ".$cnt;

// Open and write log in information into log file
$fObj = fopen($log_file_name,'a+');
fwrite($fObj, $loginMsg." \n");
fwrite($fObj, $schApptMsg." \n\r");
$msgInfoArr = array();
$msgInfoArr[] = $loginMsg;
$msgInfoArr[] = $schApptMsg;
?>