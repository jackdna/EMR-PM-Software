<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Billing Functions
 Access Type: Indirect Access.
 
*/


/*****RECORD BATCH FILE log FOR CREATION/DELETION/ARCHIVE/REGENERATE/SUBMISSION***/
function batch_file_log($file_id,$action){
	$action_date=date('Y-m-d H:i:s');
	$operator=$_SESSION['authId'];
	$query="select Batch_file_submitte_id, Interchange_control, encounter_id from batch_file_submitte where Batch_file_submitte_id  = $file_id";
	$result=imw_query($query);
	$row=imw_fetch_assoc($result);
	$ins_query="insert into batch_file_log set batch_file_submitte_id = '".$row['Batch_file_submitte_id']."', Interchange_no = '".$row['Interchange_control']."', encounter_id = '".$row['encounter_id']."', action_date = '$action_date', action = '$action', operator = '$operator'";
	$ins_result=imw_query($ins_query);
	echo imw_error();

}

/*****GET CLAIM CONTROL NUMBER*********/
function billing_global_get_clm_control_num($pt_id,$eid,$ICN_amount,$ins_type='primary'){
	$clm_control_num = false;
	$clm_status_id = "1,19";
	if($ins_type=='secondary'){
		$clm_status_id = "2,20";
	}
	$q = "SELECT e835pd.CLP_payer_claim_control_number AS clm_control_num FROM era_835_patient_details e835pd 
		  JOIN era_835_proc_details e835procd ON (e835pd.ERA_patient_details_id=e835procd.ERA_patient_details_id) 
		  WHERE e835pd.CLP_payer_claim_control_number != '' AND e835pd.CLP_claim_submitter_id='$pt_id' AND e835pd.CLP_claim_status 
		  IN ($clm_status_id) AND e835procd.REF_prov_identifier LIKE '".$eid."MCR%' ORDER BY e835pd.ERA_patient_details_id DESC Limit 0,1";
	$r = imw_query($q);
	if($r && imw_num_rows($r)==1){
		$rs = imw_fetch_assoc($r);
		$clm_control_num = $rs['clm_control_num'];
		$clm_control_num = preg_replace('/[^a-zA-Z0-9]/','',$clm_control_num);
	}
	if($clm_control_num==""){
		$q = "SELECT e835pd.CLP_payer_claim_control_number AS clm_control_num FROM era_835_patient_details e835pd 
		  JOIN era_835_proc_details e835procd ON (e835pd.ERA_patient_details_id=e835procd.ERA_patient_details_id)
		  JOIN era_835_proc_posted e835procdpost on (e835procdpost.era_835_proc_id=e835procd.835_Era_proc_Id)
		  WHERE e835pd.CLP_payer_claim_control_number != '' AND e835pd.CLP_claim_submitter_id='$pt_id' AND e835procdpost.ins_type 
		  IN ($clm_status_id) AND e835procdpost.encounter_id ='".$eid."' ORDER BY e835pd.ERA_patient_details_id DESC Limit 0,1";
		$r = imw_query($q);
		if($r && imw_num_rows($r)==1){
			$rs = imw_fetch_assoc($r);
			$clm_control_num = $rs['clm_control_num'];
			$clm_control_num = preg_replace('/[^a-zA-Z0-9]/','',$clm_control_num);
		}
	}
	return $clm_control_num;
}

/*****GET PATIENT'S INTERCHANGE CONTROL NUMBER/ CLAIM CONTROL NUMBER***/
function get_patient_icn($pt_id,$payment_date,$payer_id,$ICN_amount){
	$icn = false;
	$q = "SELECT e835pd.CLP_payer_claim_control_number AS pat_icn FROM era_835_patient_details e835pd 
		  JOIN era_835_details e835d ON (e835pd.835_Era_Id=e835d.835_Era_Id) 
		  WHERE e835pd.CLP_claim_submitter_id='$pt_id' AND (e835d.chk_issue_EFT_Effective_date='$payment_date' OR e835d.DTM_production_date='$payment_date') 
		  AND e835d.REF_provider_ref_id='$payer_id' Limit 0,1";
	$r = imw_query($q);
	if($r && imw_num_rows($r)==1){
		$rs = imw_fetch_assoc($r);
		$icn = $rs['pat_icn'];
	}else if($r && imw_num_rows($r)==0){
		if(substr($ICN_amount,-3)=='.00'){$ICN_amount = intval($ICN_amount);}
		$q2 = "SELECT e835pd.CLP_payer_claim_control_number AS pat_icn FROM era_835_patient_details e835pd 
			  JOIN era_835_details e835d ON (e835pd.835_Era_Id=e835d.835_Era_Id) 
			  WHERE e835pd.CLP_claim_submitter_id='$pt_id' AND e835pd.CLP_total_claim_charge='$ICN_amount' 
			  AND e835d.REF_provider_ref_id='$payer_id' Limit 0,1";
		$r2 = imw_query($q2);
		if($r2 && imw_num_rows($r2)==1){
			$rs2 = imw_fetch_assoc($r2);
			$icn = $rs2['pat_icn'];
		}	
	}
	$icn = preg_replace('/[^a-zA-Z0-9]/','',$icn);
	return $icn;
}

/*****CLEANING AND PADDING OF CLAIM_CONTROL_NUMBER*****/
function clean_n_padd_claim_control_num($clm_control_num){
	global $billing_global_server_name;
	global $ClearingHouse;
	$clm_control_num = preg_replace('/[^a-zA-Z0-9]/','',$clm_control_num);
	$clm_control_num = str_replace('-','',$clm_control_num);
	$diff = 13 - strlen($clm_control_num);
	$padd = '';
	if($diff > 0){
	//	$padd = str_repeat('0',$diff);
	}
	if(in_array(strtolower($billing_global_server_name),array('shoreline','EyeClinicsMichigan','manahan')) || $ClearingHouse['abbr']=='PI'){
		$padd = '';
	}
	return $clm_control_num.$padd;
}

/*******TO GET EMDEON REPORT NAME WHILE SHOWING IN "GET REPORTS" POPUP*****/
function getValue_fromTextReport($report_data,$column,$delimeter=':'){//to get emdeon report name.
	$return = false;
	$arr_report_data = explode("\n",$report_data);
	for($i==0;$i<count($arr_report_data);$i++){
		if(trim($arr_report_data[$i])==''){
			unset($arr_report_data[$i]);
		}else if(substr(trim($arr_report_data[$i]),0,1) == 'R'){
			$space_place = strpos($arr_report_data[$i]," ");
			if($space_place <= 50 && $space_place > 25){
				$replace_character = substr($arr_report_data[$i],0,$space_place);	
				$arr_report_data[$i] = str_replace($replace_character," ",$arr_report_data[$i]);
			}
			$arr_report_data[$i] = str_replace(' ','',$arr_report_data[$i]);
		//	echo $arr_report_data[$i].'<br>';
		} 
	}
	$findIndexOf = strtoupper($column);$foundAt = -1; $f = 1;
	foreach($arr_report_data as $key=>$val){//echo $key.'=>'.$val.'<br>';
		$f = strpos($val,$findIndexOf);
		if($f === 0 && $foundAt < 0){
			$foundAt = $key;
			$colValString = $arr_report_data[$foundAt];//echo $colValString.'<br>';
			break;
		}else if($f>0 && $foundAt < 0){
			$val = substr($val,$f);
			$foundAt = $key;
			$colValString = $val;//echo $colValString.'<br>';
			break;
		}
	}

	if($foundAt != -1){
		$Arr_colVal = explode($delimeter,$colValString);
		$return = $Arr_colVal['1'];
	}
	return $return;
}

/****CHECK HOW MANY OLD A CLAIM IS. FOR CLAIMS MORE THAN 90 DAYS OLD, SPECIFIC CLAIM INFO****/
function check_claim_old_days($dos){
	$d1 = date('Y-m-d');
	$dDiff = round(abs(strtotime($d1)-strtotime($dos))/86400);
   	return $dDiff;
}

/****IF CLAIM IS 90 DAYS OR MORE OLD, CHECK IF ANY DELAY CODE SET FOR THIS ENCOUNTER****/
function check_db_delay_code($pcl_id){
	$code = '';
	$res = imw_query("SELECT claim_delay_code FROM patient_charge_list WHERE charge_list_id = '".$pcl_id."' AND claim_delay_code > 0 LIMIT 1");
	if($res && imw_num_rows($res)==1){
		$rs = imw_fetch_assoc($res);
		$code = trim($rs['claim_delay_code']);
	}
	return $code;
}

/*****GET CAS CODES TO PUT IN SECONDARY CLALIMS*******/
function getCAScodes($chld_id,$pri_ins_id){
	$CAS_array = array();
	$q1 = "SELECT pcdpi.CAS_type, pcdpi.CAS_code, pcdpi.paidForProc AS amount, 'payment' AS type 
		   FROM patient_charges_detail_payment_info pcdpi 
		   JOIN patient_chargesheet_payment_info pcpi ON (pcpi.payment_id = pcdpi.payment_id) 
		   WHERE pcdpi.charge_list_detail_id='$chld_id' 
		   AND pcdpi.CAS_type != '' AND pcdpi.CAS_code != '' 
		   AND pcdpi.deletePayment = '0' 
		   AND pcdpi.paidBy = 'Insurance' 
		   AND pcpi.insProviderId = '$pri_ins_id' 
		   AND pcpi.paid_by = 'Insurance'";
	$res1 = imw_query($q1);
	if($res1 && imw_num_rows($res1)>0){
		while($rs1 = imw_fetch_assoc($res1)){
			$CAS_array[$chld_id][] = $rs1;
		}
	}
	
	$q2 = "SELECT CAS_type, CAS_code, era_amt AS amount, 'writeoff' AS type, write_off_date AS date 
		   FROM paymentswriteoff 
		   WHERE charge_list_detail_id = '$chld_id' 
		   AND delStatus = '0' 
		   AND CAS_type != '' AND CAS_code != '' 
		   AND write_off_by_id = '$pri_ins_id' 
		   ";
	$res2 = imw_query($q2);
	if($res2 && imw_num_rows($res2)>0){
		while($rs2 = imw_fetch_assoc($res2)){
			$CAS_array[$chld_id][] = $rs2;
		}
	}
	
	$q3 = "SELECT CAS_type, CAS_code, deniedAmount AS amount, 'denied' AS type, deniedDate AS date  
			FROM deniedpayment WHERE deniedById = '$pri_ins_id' 
			AND charge_list_detail_id = '$chld_id' 
			AND CAS_type != '' AND CAS_code != '' 
			AND denialDelStatus = '0' AND status='1'";
	$res3 = imw_query($q3);
	if($res3 && imw_num_rows($res3)>0){
		while($rs3 = imw_fetch_assoc($res3)){
			$CAS_array[$chld_id][] = $rs3;
		}
	}

	return $CAS_array;
}

/****UPDATE CLAIM CONTROL NUMBER IN PATIENT CHARGE LIST********/
function update_claim_control_number($insType,$encId,$ctrlNum){
	$q = "UPDATE patient_charge_list SET ".$insType." = '".$ctrlNum."' WHERE ".$insType."='' AND encounter_id='".$encId."'";
	$res = imw_query($q);
}

/****GET CLIA NUMBER BY FACILITY*****/
function get_CLIA_by_facility_id($fac_id){
	$clia = '';
	$q = "SELECT f.clia FROM facility f JOIN pos_facilityies_tbl pft ON (pft.pos_facility_id = f.fac_prac_code) WHERE pft.pos_facility_id = '$fac_id' AND f.clia!='' ORDER BY f.billing_location DESC LIMIT 0,1";
	$res = imw_query($q);
	if($res && imw_num_rows($res)==1){
		$rs = imw_fetch_assoc($res);
		$clia = trim($rs['clia']);
	}
	return $clia;
}

/****ERA File Format*****/
function change_file_format($file_content){
	$file_content = preg_replace('/[^(\x20-\x7F)]/','',$file_content);
	//--- FILE PATTERNS ----
	$patterns = array('/\*GS\*/','/\*ST\*/','/\*BPR\*/','/\*TRN\*/','/\*N1\*/','/\*N2\*/','/\*N3\*/','/\*N4\*/',
				'/\*PER\*/','/\*LX\*/','/\*CLP\*/','/\*SVC\*/','/\*CAS\*/','/\*NM1\*/','/\*REF\*/',
				'/\*MOA\*/','/\*AMT\*/','/\*DTM\*/','/\*LQ\*/','/\*SE\*/','/\*GE\*/','/\*IEA\*/');
	//---- FILE REPLACEMENTS ---
	$replacements = array('~GS*','~ST*','~BPR*','~TRN*','~N1*','~N2*','~N3*','~N4*','~PER*','~LX*','~CLP*',
					'~SVC*','~CAS*','~NM1*','~REF*','~MOA*','~AMT*','~DTM*','~LQ*','~SE*','~GE*','~IEA*');
					
	$file_content = preg_replace($patterns,$replacements,$file_content);
	$file_content = substr($file_content,0,-1);
	$file_content .= '~';
	return $file_content;
}

/****Add ERA File Data in DB*****/
function put_era_data($fileContents){
	global $fileType;
	global $toDay;
	global $toDayFile;
	global $fileName;
	global $file_size;
	$toDay=date('Y-m-d');
	$startFrom = substr($fileContents, strpos($fileContents, 'TRN'));
	$transactionSeg = substr($startFrom, 0, strpos($startFrom, '~'));
	$transactionSegArr = explode("*", $transactionSeg);
	$trnTypeNumber = $transactionSegArr[2];
	
	$isa_startFrom = substr($fileContents, strpos($fileContents, 'ISA'));
	$isa_transactionSeg = substr($isa_startFrom, 0, strpos($isa_startFrom, '~'));
	$isa_transactionSegArr = explode("*", $isa_transactionSeg);
	$interchange_sender_no = $isa_transactionSegArr[13];
	
	$Duplicte_file=0;
	$getDuplicteChkStr_isa = "SELECT interchange_sender_no FROM era_835_details WHERE interchange_sender_no = '$interchange_sender_no'";
	$getDuplicteChkQry_isa = imw_query($getDuplicteChkStr_isa);
	if(imw_num_rows($getDuplicteChkQry_isa)>0){
		$getDuplicteChkStr = "SELECT TRN_payment_type_number FROM era_835_details WHERE TRN_payment_type_number = '$trnTypeNumber'";
		$getDuplicteChkQry = imw_query($getDuplicteChkStr);
		if(imw_num_rows($getDuplicteChkQry)>0){	
			$Duplicte_file=imw_num_rows($getDuplicteChkQry_isa);
		}
	}
	if($Duplicte_file>0){			
		$electronicFilesTblId = 'File Already Exists.';
	}else{
		//-------------	INSERT ELECTERONIC FILES DETAILS    -----------------//				
		$insertStr = "INSERT INTO electronicfiles_tbl SET
					file_type = '".addslashes(trim($fileType))."',
					file_name = '".addslashes(trim($fileName))."',
					file_contents = '".addslashes(trim($fileContents))."',
					file_size = '".addslashes(trim($file_size))."',
					modified_date = '".addslashes(trim($toDay))."',
					file_temp_name = '".addslashes(trim($fileName))."',
					post_status = 'Not Posted'";
		$insertQry = imw_query($insertStr);
		$insertedId = imw_insert_id();
		$electronicFilesTblId = imw_insert_id();
		if(strlen($insertedId)==1){
			$insertedId = '00'.$insertedId;
		}else if(strlen($insertedId)==2){
			$insertedId = '0'.$insertedId;
		}
		
	}
	return $electronicFilesTblId;
}

/****Explode ERA File*****/
function explodeFunction($str){
	return explode("*", $str);
}

/*Check if era is shared on the server*/
function is_era_shared(){
	return (defined('ERA_SHARE') && ERA_SHARE === true);
}
/*Copy file on ther server*/
function copy_file($source, $destination, $fileName){
	if( !is_dir($destination) ){
		mkdir($destination, 0755, true);
		chown($destination, 'apache');
	}
	
	if(file_exists($source.'/'.$fileName)){
		copy($source.'/'.$fileName, $destination.'/'.$fileName);
	}
}
/*Check if era is to be downloaded from other practice*/
function dl_era(){
	return (defined('ERA_URL') && ERA_URL !== '');
}

/***************/
?>