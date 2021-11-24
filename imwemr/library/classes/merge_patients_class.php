<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
*/

include_once $GLOBALS['srcdir'].'/classes/SaveFile.php';
include_once $GLOBALS['srcdir'].'/classes/common_function.php';
include_once $GLOBALS['srcdir'].'/classes/class.app_base.php';
$app_base_obj = new app_base;


class Merge_patient{
	//Public variables
		public $patient_id = '';
		public $patientFacility = '';
		public $extract_arr = '';
		
	public function __construct($pat_id=0){
		$this->patient_id = $pat_id;
		//Get patient details
		$data_arr[] = $this->get_patient_data($pat_id);

		//Get Primary Insurance
		$data_arr[] = $this->get_primary_ins_data($pat_id);

		//Get patient outstanding balance By Patient and Insurance
		$data_arr[] = $this->get_pt_outstanding_bal($pat_id);

		//Get patient Medical HX status
		$data_arr[] = $this->get_pt_hx_stat($pat_id);

		//Merging multi key arr into one arr
		$this->extract_arr = $this->get_extract_arr($data_arr);
	}
	
	//Copy target file to destination
	public function full_copy( $source, $target ) {	
		if ( is_dir( $source ) ) {				
			@mkdir( $target );
			$d = dir( $source );
			while ( FALSE !== ( $entry = $d->read() ) ) {
				if ( $entry == '.' || $entry == '..' ) {
					continue;
				}
				$Entry = $source . '/' . $entry; 
				if ( is_dir( $Entry ) ) {
					$this->full_copy( $Entry, $target . '/' . $entry );
					continue;
				}
				copy( $Entry, $target . '/' . $entry );
			} 
			$d->close();
		}else {
			copy( $source, $target );
		}
	}
	
	//Get Patient Data
	public function get_patient_data($pid){
		$sql = "select fname, lname, Date_Format(DOB, '%M %d,%Y') as dob, phone_home, street, street2, city, state, postal_code,default_facility from patient_data where id = '".$pid."'";
		$result = imw_query($sql);
		$patient_data = imw_fetch_array($result);
		$full_name  = $patient_data['lname'].', ';
		$full_name .= $patient_data['fname'].' ';
		$full_name .= $patient_data['mname'];
		$dob		= $patient_data["dob"];
		$phone_home = $patient_data["phone_home"];
		$default_facility = $patient_data["default_facility"];

		$qryGetPatientFacility = "select a.pos_facility_id,a.facilityPracCode,b.pos_prac_code from pos_facilityies_tbl a,pos_tbl b where a.pos_id = b.pos_id order by headquarter desc";
		$rsGetPatientFacility = imw_query($qryGetPatientFacility);
		while($rowGetPatientFacility = imw_fetch_array($rsGetPatientFacility)){	
			if($default_facility == $rowGetPatientFacility['pos_facility_id']){
				$this->patientFacility = $rowGetPatientFacility['facilityPracCode']." - ".$rowGetPatientFacility['pos_prac_code'];
				break;
			}
		}
		
		$curentDate = date("Y-m-d"); 
		$qryGetPatientLastAppt  = "SELECT DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format()."') as saAppDate,TIME_FORMAT(sa.sa_app_starttime , '%r' ) as saAppTime ,sa.sa_comments as saAppCom,sp.proc as saProc FROM schedule_appointments sa inner join slot_procedures sp on sp.id = sa.procedureid WHERE sa.sa_app_start_date <= '".$curentDate."' and  sa.sa_patient_id = '".$pid."' and sa.sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa.sa_app_start_date desc limit 1";
		$rsGetPatientLastAppt = imw_query($qryGetPatientLastAppt);
		if($rsGetPatientLastAppt){
			$rowGetPatientLastAppt = imw_fetch_array($rsGetPatientLastAppt);
			$ptLastAddtDate = $rowGetPatientLastAppt['saAppDate'];
			$ptLastAddtComments = $rowGetPatientLastAppt['saAppCom'];
			$ptLastAddtProcedure = $rowGetPatientLastAppt['saProc'];
			$ptLastAddtTime = $rowGetPatientLastAppt['saAppTime'];
		}
		
		//Set address of the patient
		$address = "";
		$address .= ($patient_data["street"]!="" || empty($patient_data["street"])!=true)?$patient_data["street"]." ":"";
		$address .= ($patient_data["street2"]!="" || empty($patient_data["street2"])!=true)?$patient_data["street2"]." ":"";
		$address .= ($patient_data["city"]!="" || empty($patient_data["city"])!=true)?$patient_data["city"]." ":"";
		$address .= ($patient_data["state"]!="" || empty($patient_data["state"])!=true)?$patient_data["state"]." ":"";
		$address .= ($patient_data["postal_code"]!="" || empty($patient_data["postal_code"])!=true)?$patient_data["postal_code"]:"";
		
		$return_arr['id'] = $pid;
		$return_arr['full_name'] = $full_name;
		$return_arr['dob'] = $dob;
		$return_arr['phone_home'] = $phone_home;
		$return_arr['default_facility'] = $default_facility;
		$return_arr['patientFacility'] = $this->patientFacility;
		$return_arr['ptLastAddtDate'] = $ptLastAddtDate;
		$return_arr['ptLastAddtComments'] = $ptLastAddtComments;
		$return_arr['ptLastAddtProcedure'] = $ptLastAddtProcedure;
		$return_arr['ptLastAddtTime'] = $ptLastAddtTime;
		$return_arr['address'] = $address;
		return $return_arr;
	}
	
	//Get primary insurance data
	public function get_primary_ins_data($pid){
		$return_array = array();
		$qryGetPrimaryInsurance = "
			SELECT insurance_data.type, ic.name, insurance_data.policy_number, insurance_data.actInsComp
			FROM insurance_data left join insurance_companies AS ic on ic.id = insurance_data.provider
			WHERE insurance_data.pid =  '".$pid."'
			AND insurance_data.ins_caseid  = (select ins_caseid from insurance_case where patient_id = '".$pid."' and case_status = 'open' and ins_case_type = (select case_id from insurance_case_types where normal = '1'))	
			AND ((insurance_data.type = 'primary') or (insurance_data.type = 'secondary')) order by insurance_data.type
			";
		$rsGetPrimaryInsurance = imw_query($qryGetPrimaryInsurance);
		if($rsGetPrimaryInsurance){
			while($rowGetPrimaryInsurance = imw_fetch_array($rsGetPrimaryInsurance)){
				if($rowGetPrimaryInsurance['type'] == "primary"){
					$return_array['insPriType'] = $rowGetPrimaryInsurance['type'];
					$return_array['insPriCompName'] = $rowGetPrimaryInsurance['name'];
					$return_array['insPriPolicyNo'] = $rowGetPrimaryInsurance['policy_number'];
				}
				elseif($rowGetPrimaryInsurance['type'] == "secondary"){
					$return_array['insSecType'] = $rowGetPrimaryInsurance['type'];
					$return_array['insSecCompName'] = $rowGetPrimaryInsurance['name'];
					$return_array['insSecPolicyNo'] = $rowGetPrimaryInsurance['policy_number'];
				}
			}
			return $return_array;	
		}
	}
	
	//Get patient outstanding balance By Patient and Insurance 
	public function get_pt_outstanding_bal($pid){
		$primarySubmitVal = $secondarySubmitVal = $tertairySubmitVal = $primaryPaidVal = $secondaryPaidVal = $tertiaryPaidVal = "";
		$patinetAccountTransaction = "0";
		$pat_Due_arr = $insurance_Due_arr = array();
		$qryGetPatientDue = "SELECT encounter_id,patientDue as patient_due, insuranceDue as insurance_due, primarySubmit,secondarySubmit,tertairySubmit,primary_paid,secondary_paid,tertiary_paid  FROM patient_charge_list WHERE del_status='0' and patient_id = '".$pid."'";
		$rsGetPatientDue = imw_query($qryGetPatientDue);
		if($rsGetPatientDue){
			if(imw_num_rows($rsGetPatientDue)>0){
				while($rowGetPatientDue = imw_fetch_assoc($rsGetPatientDue)){
					$encounter_id = $rowGetPatientDue["encounter_id"];
					$pat_Due_arr[$encounter_id] = $rowGetPatientDue["patient_due"];
					$insurance_Due_arr[$encounter_id] = $rowGetPatientDue["insurance_due"];	
					$primarySubmitVal = $rowGetPatientDue["primarySubmit"];
					$secondarySubmitVal = $rowGetPatientDue["secondarySubmit"];
					$tertairySubmitVal = $rowGetPatientDue["tertairySubmit"];
					$primaryPaidVal = $rowGetPatientDue["primary_paid"];
					$secondaryPaidVal = $rowGetPatientDue["secondary_paid"];
					$tertiaryPaidVal = $rowGetPatientDue["tertiary_paid"];	
					if(($primarySubmitVal == "1") || ($secondarySubmitVal == "1") || ($tertairySubmitVal == "1") || ($primaryPaidVal == "true") || ($secondaryPaidVal == "true") || ($tertiaryPaidVal == "true")){
						$patinetAccountTransaction = "1";
					}
				}
			}
		}
		$pat_Due = "".show_currency()."".number_format(array_sum($pat_Due_arr), 2);
		$insurance_Due = "".show_currency()."".number_format(array_sum($insurance_Due_arr), 2);
		$return_array['pat_Due'] = $pat_Due;
		$return_array['insurance_Due'] = $insurance_Due;
		$return_array['patinetAccountTransaction'] = $patinetAccountTransaction;
		
		return $return_array;
	}
	
	//Get Pt Medical Hx status
	public function get_pt_hx_stat($pid){
		$blMedHxPerform = false;
		$qryGetPatMedHxOcular = "select ocular_id from ocular where patient_id='".$pid."' limit 1";
		$rsGetPatMedHxOcular = imw_query($qryGetPatMedHxOcular);
		if($rsGetPatMedHxOcular){
			if(imw_num_rows($rsGetPatMedHxOcular)>0){
				$blMedHxPerform = true;
			}
			imw_free_result($rsGetPatMedHxOcular);
		}

		if($blMedHxPerform == false){
			$qryGetPatMedHxGH = "select general_id from general_medicine where patient_id='".$pid."' limit 1";
			$rsGetPatMedHxGH = imw_query($qryGetPatMedHxGH);
			if($rsGetPatMedHxGH){
				if(imw_num_rows($rsGetPatMedHxGH)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxGH);
			}
		}
		if($blMedHxPerform == false){
			$qryGetPatMedHxMed = "select id from lists where pid='".$pid."' and (type='1' or type='4') limit 1";
			$rsGetPatMedHxMed = imw_query($qryGetPatMedHxMed);
			if($rsGetPatMedHxMed){
				if(imw_num_rows($rsGetPatMedHxMed)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxMed);
			}
		}
		if($blMedHxPerform == false){
			$qryGetPatMedHxSXPro = "select id from lists where pid='".$pid."' and (type='5' or type='6') limit 1";
			$rsGetPatMedHxSXPro = imw_query($qryGetPatMedHxSXPro);
			if($rsGetPatMedHxSXPro){
				if(imw_num_rows($rsGetPatMedHxSXPro)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxSXPro);
			}
		}
		if($blMedHxPerform == false){
			$qryGetPatMedHxAllergies = "select id from lists where pid='".$pid."' and (type='3' or type='7') limit 1";
			$rsGetPatMedHxAllergies = imw_query($qryGetPatMedHxAllergies);
			if($rsGetPatMedHxAllergies){
				if(imw_num_rows($rsGetPatMedHxAllergies)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxAllergies);
			}
		}
		if($blMedHxPerform == false){
			$qryGetPatMedHxImmunizations = "select id  from immunizations where patient_id='".$pid."' limit 1";
			$rsGetPatMedHxImmunizations = imw_query($qryGetPatMedHxImmunizations);
			if($rsGetPatMedHxImmunizations){
				if(imw_num_rows($rsGetPatMedHxImmunizations)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxImmunizations);
			}
		}
		if($blMedHxPerform == false){
			$qryGetPatMedHxSocial = "select social_id from social_history where patient_id='".$pid."' limit 1";
			$rsGetPatMedHxSocial = imw_query($qryGetPatMedHxSocial);
			if($rsGetPatMedHxSocial){
				if(imw_num_rows($rsGetPatMedHxSocial)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxSocial);
			}
		}
		if($blMedHxPerform == false){
			$qryGetPatMedHxCCHIS = "select cc_id from chart_left_cc_history  where patient_id  = '".$pid."' limit 1";
			$rsGetPatMedHxCCHIS = imw_query($qryGetPatMedHxCCHIS);
			if($rsGetPatMedHxCCHIS){
				if(imw_num_rows($rsGetPatMedHxCCHIS)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxCCHIS);
			}
		}
		if($blMedHxPerform == false){
			$qryGetPatMedHxVS = "select id from vital_sign_master where patient_id  = '".$pid."' limit 1";
			$rsGetPatMedHxVS = imw_query($qryGetPatMedHxVS);
			if($rsGetPatMedHxVS){
				if(imw_num_rows($rsGetPatMedHxVS)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxVS);
			}
		}
		if($blMedHxPerform == false){
			$qryGetPatMedHxOrderSets  = "select order_set_associate_id  from order_set_associate_chart_notes where patient_id  = '".$pid."' limit 1";
			$rsGetPatMedHxOrderSets = imw_query($qryGetPatMedHxOrderSets);
			if($rsGetPatMedHxOrderSets){
				if(imw_num_rows($rsGetPatMedHxOrderSets)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxOrderSets);
			}
		}
		if($blMedHxPerform == false){
			$qryGetPatMedHxProblemList   = "select id from pt_problem_list where pt_id  = '".$pid."' limit 1";
			$rsGetPatMedHxProblemList = imw_query($qryGetPatMedHxProblemList);
			if($rsGetPatMedHxProblemList){
				if(imw_num_rows($rsGetPatMedHxProblemList)>0){
					$blMedHxPerform = true;
				}
				imw_free_result($rsGetPatMedHxProblemList);
			}
		}
		$intMedHxPerform = 0;
		if($blMedHxPerform == false){
			$intMedHxPerform = 0;
		}
		elseif($blMedHxPerform == true){
			$intMedHxPerform = 1;
		}
		
		$return_array['intMedHxPerform'] = $intMedHxPerform;
		$return_array['blMedHxPerform'] = $blMedHxPerform;
		return $return_array;
	}


	//Converting array into extract format
	public function get_extract_arr($arr){
		$extract_arr = array();
		foreach($arr as $key => $val){
			foreach($val as $key_val => $value){
				$extract_arr[$key_val] = $value;
			}
		}
		return $extract_arr;
	}

	//Returns patient search data
	public function get_patient_details($request){
		$return_array = array();
		global $app_base_obj;
		$val = addslashes(trim($request["val"]));
		$fld = $request["fld"];
		if(empty($val)){
		  $fld = "Nothing";      
		}else{
			if(($fld != "Resp.LN") && ($fld != "Ins.Policy") ){
				$elem_status = $fld;
				$fld = trim($app_base_obj->getFindBy($val));
			}
		}
		
		switch($fld){
		   case "Last":
			   $fld="lname";
		   break;
		   case "LastFirstName":
			   $fld="LastFirstName";
		   break;
		   case "street":
			   $fld="street";
		   break;
		   case "phone":
			   $fld="phone";
		   break;
		   case "First":
			   $fld="fname";
		   break;
		   case "ID":
			   $fld="id";
		   break;
		   case "DOB":
			   $fld="DOB";
		   break;
		   case "SSN":
			   $fld="ss";
		   break;
		   case "Resp.LN":
			   $fld="Resp.LN";
		   break;
		   case "Ins.Policy":
			   $fld="Ins.Policy";
		   break;   
		}
		
		$patientData = $this->search_patient_data($val,$fld,$elem_status);
		$pat_val = false;
		if(count($patientData) == 1){
			foreach($patientData as $obj){
				$state = $obj['state'].' '.$obj['postal_code'];
				$fnameComma='';
				if(trim($obj['fname'])) {
					$commaSep=', ';
				}
				$name = $obj['lname'].$commaSep.$obj['fname'];
				$name .= ' '.$obj['mname'];
				$name2 = addslashes($name); 
				$id = $obj['id'];
				$address = $obj['street'];
				if(trim($obj['street2'])){
					$address .= ', '.$obj['street2'];
				}
				if(trim($obj['city'])){
					$address .= ', '.$obj['city'];
				}
				if(trim($obj['state'])){
					$address .= ', '.$obj['state'];
				}
				$address .= ' '.$obj['postal_code'];
				$phone_home = $obj['phone_home'];
				$pat_val = true;
				$ret_arr_single['name'] = $name;
				$ret_arr_single['name2'] = $name2;
				$ret_arr_single['id'] = $id;
				$ret_arr_single['state'] = $state;
				$ret_arr_single['address'] = $address;
				$ret_arr_single['pat_val'] = $pat_val;
				$ret_arr_single['phone_home'] = $phone_home;
				$return_array_single[] = $ret_arr_single;
			}
			return $return_array_single;
		}else{
			if(count($patientData) > 0){
				foreach($patientData as $obj){
					$state = $obj['state'].' '.$obj['postal_code'];
					$fnameComma='';
					if(trim($obj['fname'])) {
						$commaSep=', ';
					}
					$name = $obj['lname'].$commaSep.$obj['fname'];
					$name .= ' '.$obj['mname'];
					$name2 = addslashes($name); 
					$id = $obj['id'];
					$address = $obj['street'];
					if(trim($obj['street2'])){
						$address .= ', '.$obj['street2'];
					}
					if(trim($obj['city'])){
						$address .= ', '.$obj['city'];
					}
					if(trim($obj['state'])){
						$address .= ', '.$obj['state'];
					}
					$address .= ' '.$obj['postal_code'];
					$phone_home = $obj['phone_home'];
					$ret_arr['name'] = $name;
					$ret_arr['name2'] = $name2;
					$ret_arr['id'] = $id;
					$ret_arr['state'] = $state;
					$ret_arr['address'] = $address;
					$ret_arr['pat_val'] = $pat_val;
					$ret_arr['phone_home'] = $phone_home;
					$return_array[] = $ret_arr;	
				}
				return $return_array;
			}
		}
	}
	
	//Return records for matching with given patient last name or id
	public function search_patient_data($val,$fld,$status="Active"){
		$return_array = array();
		$qry = '';
		if($status==""){
			$status="Active";
		}
		if($fld == "Resp.LN"){
			$qry = "select * from patient_data left join resp_party on
					patient_data.id = resp_party.patient_id where
					resp_party.lname = '$val'";
		}
		else if($fld == "Ins.Policy"){
			$qry = "SELECT
				insurance_data.policy_number,	
				patient_data.fname,patient_data.pid,patient_data.lname,patient_data.postal_code,
				patient_data.street,patient_data.phone_home,patient_data.ss,patient_data.DOB,patient_data.id
				FROM insurance_data 
				INNER JOIN patient_data ON insurance_data.pid = patient_data.id
				WHERE insurance_data.policy_number LIKE '$val%'
				GROUP BY patient_data.id	
				ORDER BY patient_data.fname";
		}
		else{
			if(($fld != 'Nothing') && ($fld != 'LastFirstName') && ($fld != 'phone')){
				$val = ($fld != "id") ? $val."%" : $val;
				$qry = "select * from patient_data where $fld like '$val' 
						AND patientStatus='$status' order by fname";
			}else if($fld == 'LastFirstName'){
				$searchArr = preg_split("/(,|;)/",$val);
				$val1 = trim($searchArr[0]);
				$val2 = trim($searchArr[1]);
				$val3 = trim($searchArr[2]);
				if(empty($val3) == false){
					$qry .= " and sex like '$val3%'";
				}
				$qry = "select * from patient_data where lname like '$val1%' 
						AND fname  like '$val2%' AND patientStatus='$status'  $qry
						order by fname";
			}else if($fld != 'phone'){
				$qry = "select * from patient_data where (phone_home like '$val%' OR phone_biz like '$val%' 
					OR phone_contact like '$val%' OR phone_cell like '$val%')  AND patientStatus='$status' order by fname";            
			}
		}
		$qryId = imw_query($qry);
		if(imw_num_rows($qryId) > 0){
			while($row = imw_fetch_assoc($qryId)){
				$return_array[] = $row;
			}
		}
		return $return_array;
	}
	
	
	//Merging patient data
	public function merge_requested_patients($request){
		extract($request);
		$scan_db_name = IMEDIC_SCAN_DB;
		
		//merging starts here //that condition is checks for the  clicking on the merge button
		if((isset($request["hid_action"]) == true) && ($request["hid_action"] == "save") && ($merge_patient_id > 0) && ($this_patient_id > 0)){

			//getting merging (first patient) patient demograhics data	
			$qryGetDemoDataMerging = "select title as mrTitle,fname as mrFName,lname as mrLName,mname as mrMName,suffix as mrSuffix,street as mrStreet,street2 as mrStreet2,postal_code as mrPastalCode,city as mrCity,
									state as mrState,phone_home as mrPhoneHome,phone_biz as mrPhoneBiz,phone_cell as mrPhoneCell,email as mrEmail,status as mrStatus,sex as mrGerder,ss as mrSS,DOB as mrDOB,
									ado_option as mrAdoOption
									from patient_data where id = '".$merge_patient_id."'";
			$rsGetDemoDataMerging = imw_query($qryGetDemoDataMerging);
			if($rsGetDemoDataMerging){
				if(imw_num_rows($rsGetDemoDataMerging)>0){
					$rowGetDemoDataMerging = imw_fetch_array($rsGetDemoDataMerging);
				}
				imw_free_result($rsGetDemoDataMerging);
			}	
			
			//getting all patient related tables to update patient records in detail tables	
			$sql = "select * from merge_patient_tables";
			$result_select = imw_query($sql);
			//echo imw_num_rows($result_select);
			
			if($result_select !== false){
				if(imw_num_rows($result_select) > 0){
					$del_qry = "delete from recent_users where patient_id  = '".$this_patient_id."'";
					imw_query($del_qry);	
					$sql_insert_merge_audit_master = "insert into merge_audit_master set
								date_time = '".date("Y-m-d H:i:s")."',
								merged_by = '".$_SESSION['authId']."',
								merged_patient_id = '".$merge_patient_id."',
								patient_id = '".$this_patient_id."',
								notes = '".$request['remarks']."';
							";
					$result = imw_query($sql_insert_merge_audit_master);
					$master_id = imw_insert_id();
					
					$qryUpdateMergingPatientChartNoteFinal = "update chart_master_table set finalize = '1',record_validity = '1' where patient_id = '".$merge_patient_id."'";
					imw_query($qryUpdateMergingPatientChartNoteFinal);	
					
					$arrNotUpdateTable = array("resp_party","employer_data","ocular","general_medicine","immunizations","social_history","vital_sign_master");
					if(($request['patinetAccountTransactionStatus1'] == "1") || ($request['patinetAccountTransactionStatus2'] == "1")){
						array_push($arrNotUpdateTable, "creditapplied", "deniedpayment", "patient_charge_list", "patient_charge_list_details", "paymentswriteoff", "paymentscomment","era_835_patient_details","check_in_out_payment_post");
					}
					
					while($row = imw_fetch_array($result_select)){
						if(!in_array($row["table_name"],$arrNotUpdateTable)){
							if(($row["table_name"] == "scans") || ($row["table_name"] == "folder_categories") || ($row["table_name"] == "scan_doc_tbl") || ($row["table_name"] == "idoc_drawing")){
								$column_array = explode(", ",$row["pt_id"]);
								
								//making query to update
								$update_sql = "update ".$scan_db_name.".".$row["table_name"]." set ";
								for($i=0;$i<count($column_array);$i++){
									$update_sql .= $column_array[$i]." = '".$this_patient_id."',";
								}
													
								$update_sql = substr($update_sql, 0, -1)." ";	// that  code is delete the last comma (,) from the query
								$update_sql .= " where  ";
								
								//making query to audit updated records
								$pk_id = "select ".$row["pk_id"]." from ".$scan_db_name.".".$row["table_name"];
								$pk_id .= " where ";
											
								for($i=0;$i<count($column_array);$i++){
									$update_sql .= $column_array[$i]." = '".$merge_patient_id."' and ";
									$pk_id .= $column_array[$i]." = '".$merge_patient_id."' and ";			
								}
							
								$update_sql = substr($update_sql, 0, -4)." "; //that code is delete the last "and" from the query
								$pk_id = substr($pk_id, 0, -4)." ";
								
								//execute query to get updated primary keys
								//$rs = imw_query($pk_id);
								$str_pk_id = "";
								
								while($ro = imw_fetch_array($rs)){
									$str_pk_id.= $ro[0].",";
									//print_r($ro[0]);
									//echo "<br/>";
								}				
								$str_pk_id = substr($str_pk_id, 0, -1);
							
								//updataing patient records - merging patients
								imw_query($update_sql);		
								//echo ($row["database_name"]);
								//query to audit updated primary keys				
								if($str_pk_id != ""){
									$new_query = "insert into merge_audit_details set 
													table_name = '".$row["table_name"]."',
													pk_id_values = '".$str_pk_id."',
													master_id = '".$master_id."',
													database_name = '".$row["database_name"]."'";
									
									imw_query($new_query);
								}	
								
								if($row["table_name"] == "scans"){
									$qryUpdateScan = "UPDATE ".$scan_db_name.".scans SET file_path = REPLACE(file_path,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";							
									$rsUpdateScan = imw_query($qryUpdateScan);							
								}
								elseif($row["table_name"] == "scan_doc_tbl"){
									$qryUpdateScanDocTbl = "UPDATE ".$scan_db_name.".scan_doc_tbl SET file_path = REPLACE(file_path,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";
									$rsUpdateScanDocTbl = imw_query($qryUpdateScanDocTbl);							
								}
								elseif($row["table_name"] == "idoc_drawing"){
									$qryUpdateScanDocTbl = "UPDATE ".$scan_db_name.".idoc_drawing SET drawing_image_path = REPLACE(drawing_image_path,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";
									$rsUpdateScanDocTbl = imw_query($qryUpdateScanDocTbl);							
								}						
							}
							else{
								$column_array = explode(", ",$row["pt_id"]);
								
								//making query to update
								$update_sql = "update ".$row["table_name"]." set ";
								for($i=0;$i<count($column_array);$i++){
									$update_sql .= $column_array[$i]." = '".$this_patient_id."',";
								}
													
								$update_sql = substr($update_sql, 0, -1)." ";	// that  code is delete the last comma (,) from the query
								$update_sql .= " where  ";
							
								//making query to audit updated records
								$pk_id = "select ".$row["pk_id"]." from ".$row["table_name"];
								$pk_id .= " where ";
											
								for($i=0;$i<count($column_array);$i++){
									$update_sql .= $column_array[$i]." = '".$merge_patient_id."' and ";
									$pk_id .= $column_array[$i]." = '".$merge_patient_id."' and ";			
								}
							
								$update_sql = substr($update_sql, 0, -4)." "; //that code is delete the last "and" from the query
								$pk_id = substr($pk_id, 0, -4)." ";
								
								//execute query to get updated primary keys
								$rs = imw_query($pk_id);
								$str_pk_id = "";
								
								while($ro = imw_fetch_array($rs)){
									$str_pk_id.= $ro[0].",";
									//print_r($ro[0]);
									//echo "<br/>";
								}				
								$str_pk_id = substr($str_pk_id, 0, -1);
								
								if($row["table_name"] == "check_in_out_payment"){
									$update_sql .=" and payment_id not in(select check_in_out_payment_id from check_in_out_payment_post where patient_id='".$merge_patient_id."')";
								}
									
								//updataing patient records - merging patients
								imw_query($update_sql);		
								//echo ($row["database_name"]);
								//query to audit updated primary keys				
								if($str_pk_id != ""){
									$new_query = "insert into merge_audit_details set 
													table_name = '".$row["table_name"]."',
													pk_id_values = '".$str_pk_id."',
													master_id = '".$master_id."',
													database_name = '".$row["database_name"]."'";							
									imw_query($new_query);
								}
								if($row["table_name"] == "schedule_appointments"){
									$selectQry = "select CONCAT(fname,mname,lname) as ptname, title from patient_data where id = '".$this_patient_id."'";
									$rsSelectQry = imw_query($selectQry);
									if(imw_num_rows($rsSelectQry) > 0){
										extract(imw_fetch_array($rsSelectQry));
									}						
									$updateQry = "update schedule_appointments set sa_patient_name = '".$ptname."',sa_patient_app_title = '".$title."' where sa_patient_id  = '".$this_patient_id."'";
									imw_query($updateQry);
								}
								elseif($row["table_name"] == "surgery_consent_form_signature"){
									$qryUpdateSCFF = "UPDATE surgery_consent_form_signature SET signature_image_path = REPLACE(signature_image_path,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";							
									$rsUpdateSCFF = imw_query($qryUpdateSCFF);		
								}
								elseif($row["table_name"] == "consent_form_signature"){
									$qryUpdateSCFF = "UPDATE consent_form_signature SET signature_image_path = REPLACE(signature_image_path,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";							
									$rsUpdateSCFF = imw_query($qryUpdateSCFF);		
								}
								elseif($row["table_name"] == "creditapplied"){
									$qryUpdateSCFF = "UPDATE creditapplied SET patient_id_adjust = REPLACE(patient_id_adjust,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";							
									$rsUpdateSCFF = imw_query($qryUpdateSCFF);		
								}
								elseif($row["table_name"] == "surgery_center_pre_op_health_ques"){
									$qryUpdateSCPOHQ = "UPDATE surgery_center_pre_op_health_ques SET witness_sign_image_path = REPLACE(witness_sign_image_path,'".$merge_patient_id."','".$this_patient_id."'), patient_sign_image_path = REPLACE(patient_sign_image_path,'".$merge_patient_id."','".$this_patient_id."') WHERE patient_id = '".$this_patient_id."'";							
									$rsUpdateSCPOHQ = imw_query($qryUpdateSCPOHQ);		
								}
							}
						}
						else{
							if($row["table_name"] == "resp_party"){
								$selectQry = "select id as respId from resp_party where patient_id  = '".$merge_patient_id."'";
								$rsSelectQry = imw_query($selectQry);
								if(imw_num_rows($rsSelectQry) > 0){
									extract(imw_fetch_array($rsSelectQry));
								}
								$new_query = "insert into merge_audit_details set 
												table_name = 'resp_party',
												pk_id_values = '".$respId."',
												master_id = '".$master_id."',
												database_name = '".$row["database_name"]."';";						
								imw_query($new_query);
								$del_qry = "delete from resp_party where patient_id  = '".$merge_patient_id."'";
								imw_query($del_qry);
							}
							elseif($row["table_name"] == "employer_data"){
								$selectQry = "select id as empPkId from employer_data where pid  = '".$merge_patient_id."'";
								$rsSelectQry = imw_query($selectQry);
								if(imw_num_rows($rsSelectQry) > 0){
									extract(imw_fetch_array($rsSelectQry));
								}
								$new_query = "insert into merge_audit_details set 
												table_name = 'employer_data',
												pk_id_values = '".$empPkId."',
												master_id = '".$master_id."',
												database_name = '".$row["database_name"]."';";						
								imw_query($new_query);
								$del_qry = "delete from employer_data where pid = '".$merge_patient_id."'";
								imw_query($del_qry);
							}
						}
					}//while loop
				}else{
					//merge failed
					$return_arr['status']   = 'failed';
					$return_arr['err_msg']   = 'Merge Failed. Please try again later.';
					$return_arr['old_name'] = $request['merge_patient_name'];
					$return_arr['new_name'] = $request['this_patient_name'];
					return $return_arr;
					die();
				}
			}else{
				//merge failed
				$return_arr['status']   = 'failed';
				$return_arr['err_msg']   = 'Merge Failed. Please try again later.';
				$return_arr['old_name'] = $request['merge_patient_name'];
				$return_arr['new_name'] = $request['this_patient_name'];
				return $return_arr;
				die();
			}
			
			//to update patient records in master tabel i.e. patient_data
			$field_name = "id, title, language, financial, fname, lname, mname, suffix, DOB, street, street2, postal_code, city, state, country_code, ss, occupation, phone_home, phone_biz, phone_contact, phone_cell, status, contact_relationship, date, sex, referrer, referrerID, providerID, email, ethnoracial, interpretter, migrantseasonal, family_size, monthly_income, homeless, financial_review, pubpid, pid, genericname1, genericval1, genericname2, genericval2, hipaa_mail, hipaa_voice, squad, fitness, username, password, p_imagename, driving_licence, licence_photo, providerColor, testColor, financial_date, financial_photo, financial_applicant, primary_care, default_facility, created_by, patient_notes, patientStatus, otherPatientStatus, primary_care_id, Sec_HCFA, noBalanceBill, EMR, erx_entry, erx_patient_id, ptInfoCollapseStatus, resPartyCollapseStatus, ptOccCollapseStatus, miscCollapseStatus, athenaID, heard_abt_us, heard_abt_desc, heard_about_us_date, relInfoName1, relInfoPhone1, relInfoReletion1, otherRelInfoReletion1, relInfoComment1, relInfoName2, relInfoPhone2, relInfoReletion2, otherRelInfoReletion2, relInfoComment2, relInfoName3, relInfoPhone3, relInfoReletion3, otherRelInfoReletion3, relInfoComment3, relInfoName4, relInfoPhone4, relInfoReletion4, otherRelInfoReletion4, relInfoComment4, newpatient_notes, scanOperator, scanDate, comments, licenseOperator, licenseDate, licenseComments, reportExemption, race, otherRace, ethnicity, otherEthnicity, emergencyRelationship";
			
			$merge_patient_data_insert = "insert into merge_patient_data (".$field_name.", master_id) select ".$field_name.", '".$master_id."' from patient_data where id = '".$merge_patient_id."'";
			
			imw_query($merge_patient_data_insert);
			
			$del_qry = "delete from patient_data where id = '".$merge_patient_id."'";
			imw_query($del_qry);
			$del_qry = "delete from recent_users where patient_id  = '".$merge_patient_id."'";
			imw_query($del_qry);
			//getting merged (second patient) patient demograhics data	
			$qryGetDemoDataMerged = "select title as mrdTitle,fname as mrdFName,lname as mrdLName,mname as mrdMName,suffix as mrdSuffix,street as mrdStreet,street2 as mrdStreet2,postal_code as mrdPastalCode,city as mrdCity,
									state as mrdState,phone_home as mrdPhoneHome,phone_biz as mrdPhoneBiz,phone_cell as mrdPhoneCell,email as mrdEmail,status as mrdStatus,sex as mrdGerder,ss as mrdSS,DOB as mrdDOB,
									ado_option as mrdAdoOption
									from patient_data where id = '".$this_patient_id."'";
			$rsGetDemoDataMerged = imw_query($qryGetDemoDataMerged);
			if($rsGetDemoDataMerged){
				if(imw_num_rows($rsGetDemoDataMerged)>0){
					$rowGetDemoDataMerged = imw_fetch_array($rsGetDemoDataMerged);
				}
				imw_free_result($rsGetDemoDataMerged);
			}
			
			//storing demographics history
			$arrDemoHistory = array();
			$arrDemoHistoryData = array();
			//1
			if($rowGetDemoDataMerging['mrTitle'] || $rowGetDemoDataMerging['mrFName'] || $rowGetDemoDataMerging['mrLName'] || $rowGetDemoDataMerging['mrMName'] || $rowGetDemoDataMerging['mrSuffix']){
				$arrDemoHistory[] = array("secName" => "patientName","preField" => "prev_title,prev_fname,prev_lname,prev_mname,prev_suffix", "newField" => "new_title,new_fname,new_lname,new_mname,new_suffix");	
			
				$arrTempPreData = array("'".$rowGetDemoDataMerging['mrTitle']."'","'".$rowGetDemoDataMerging['mrFName']."'","'".$rowGetDemoDataMerging['mrLName']."'","'".$rowGetDemoDataMerging['mrMName']."'","'".$rowGetDemoDataMerging['mrSuffix']."'");
				$strPreFieldData = $strNewFieldData = "";
				$strPreFieldData = implode(",",$arrTempPreData);	
				
				$arrTempPreNewData = array("'".$rowGetDemoDataMerged['mrdTitle']."'","'".$rowGetDemoDataMerged['mrdFName']."'","'".$rowGetDemoDataMerged['mrdLName']."'","'".$rowGetDemoDataMerged['mrdMName']."'","'".$rowGetDemoDataMerged['mrdSuffix']."'");
				$strNewFieldData = implode(",",$arrTempPreNewData);
					
				$arrDemoHistoryData[] = array("preFieldData" => $strPreFieldData, "newFieldData" => $strNewFieldData);
			}	
			//1
			//2	
			if($rowGetDemoDataMerging['mrStreet'] || $rowGetDemoDataMerging['mrStreet2'] || $rowGetDemoDataMerging['mrPastalCode'] || $rowGetDemoDataMerging['mrCity'] || $rowGetDemoDataMerging['mrState']){
				$arrDemoHistory[] = array("secName" => "patientAddress","preField" => "prev_street,prev_street2,prev_postal_code,prev_city,prev_state", "newField" => "new_street,new_street2,new_postal_code,new_city,new_state");
				$arrTempPreData = array("'".$rowGetDemoDataMerging['mrStreet']."'","'".$rowGetDemoDataMerging['mrStreet2']."'","'".$rowGetDemoDataMerging['mrPastalCode']."'","'".$rowGetDemoDataMerging['mrCity']."'","'".$rowGetDemoDataMerging['mrState']."'");
				$strPreFieldData = $strNewFieldData = "";
				$strPreFieldData = implode(",",$arrTempPreData);
					
				$arrTempPreNewData = array("'".$rowGetDemoDataMerged['mrdStreet']."'","'".$rowGetDemoDataMerged['mrdStreet2']."'","'".$rowGetDemoDataMerged['mrdPastalCode']."'","'".$rowGetDemoDataMerged['mrdCity']."'","'".$rowGetDemoDataMerged['mrdState']."'");
				$strNewFieldData = implode(",",$arrTempPreNewData);	
				$arrDemoHistoryData[] = array("preFieldData" => $strPreFieldData, "newFieldData" => $strNewFieldData);
			}	
			//2
			//$arrDemoHistoryData[] = array("preFieldData" => $rowGetDemoDataMerging['mrStreet'],$rowGetDemoDataMerging['mrStreet2'],$rowGetDemoDataMerging['mrPastalCode'],$rowGetDemoDataMerging['mrCity'],$rowGetDemoDataMerging['mrState'], "newFieldData" => $rowGetDemoDataMerged['mrdStreet'],$rowGetDemoDataMerged['mrdStreet2'],$rowGetDemoDataMerged['mrdPastalCode'],$rowGetDemoDataMerged['mrdCity'],$rowGetDemoDataMerged['mrdState']);
			//3
			if($rowGetDemoDataMerging['mrPhoneHome'] || $rowGetDemoDataMerging['mrPhoneBiz'] || $rowGetDemoDataMerging['mrPhoneCell'] || $rowGetDemoDataMerging['mrEmail']){
				$arrDemoHistory[] = array("secName" => "patientContact","preField" => "prev_phone_home,prev_phone_biz,prev_phone_cell,prev_email", "newField" => "new_phone_home,new_phone_biz,new_phone_cell,new_email");
			
				$arrTempPreData = array("'".core_phone_unformat($rowGetDemoDataMerging['mrPhoneHome'])."'","'".core_phone_unformat($rowGetDemoDataMerging['mrPhoneBiz'])."'","'".core_phone_unformat($rowGetDemoDataMerging['mrPhoneCell'])."'","'".$rowGetDemoDataMerging['mrEmail']."'");
				$strPreFieldData = $strNewFieldData = "";
				$strPreFieldData = implode(",",$arrTempPreData);
					
				$arrTempPreNewData = array("'".$rowGetDemoDataMerged['mrdPhoneHome']."'","'".$rowGetDemoDataMerged['mrdPhoneBiz']."'","'".$rowGetDemoDataMerged['mrdPhoneCell']."'","'".$rowGetDemoDataMerged['mrdEmail']."'");
				$strNewFieldData = implode(",",$arrTempPreNewData);
					
				$arrDemoHistoryData[] = array("preFieldData" => $strPreFieldData, "newFieldData" => $strNewFieldData);
			}	
			//3
			//$arrDemoHistoryData[] = array("preFieldData" => $rowGetDemoDataMerging['mrPhoneHome'],$rowGetDemoDataMerging['mrPhoneBiz'],$rowGetDemoDataMerging['mrPhoneCell'],$rowGetDemoDataMerging['mrEmail'],$rowGetDemoDataMerging['mrState'], "newFieldData" => $rowGetDemoDataMerged['mrdPhoneHome'],$rowGetDemoDataMerged['mrdPhoneBiz'],$rowGetDemoDataMerged['mrdPhoneCell'],$rowGetDemoDataMerged['mrdEmail'],$rowGetDemoDataMerged['mrdState']);
			//4
			if($rowGetDemoDataMerging['mrStatus']){
				$arrDemoHistory[] = array("secName" => "patientMstatus","preField" => "prev_mstatus", "newField" => "new_mstatus");
				$strPreFieldData = $strNewFieldData = "";
				$arrTempPreData = array("'".$rowGetDemoDataMerging['mrStatus']."'");	
				$strPreFieldData = implode(",",$arrTempPreData);
					
				$arrTempPreNewData = array("'".$rowGetDemoDataMerged['mrdStatus']."'");
				$strNewFieldData = implode(",",$arrTempPreNewData);
				
				$arrDemoHistoryData[] = array("preFieldData" => $strPreFieldData, "newFieldData" => $strNewFieldData);
			}
			//4
			//$arrDemoHistoryData[] = array("preFieldData" => $rowGetDemoDataMerging['mrStatus'], "newFieldData" => $rowGetDemoDataMerged['mrdStatus']);
			//5
			if($rowGetDemoDataMerging['mrGerder']){
				$arrDemoHistory[] = array("secName" => "patientGender","preField" => "prev_sex", "newField" => "new_sex");
				$strPreFieldData = $strNewFieldData = "";
				$arrTempPreData = array("'".$rowGetDemoDataMerging['mrGerder']."'");
			
				$strPreFieldData = implode(",",$arrTempPreData);
					
				$arrTempPreNewData = array("'".$rowGetDemoDataMerged['mrdGerder']."'");
				$strNewFieldData = implode(",",$arrTempPreNewData);
				
				$arrDemoHistoryData[] = array("preFieldData" => $strPreFieldData, "newFieldData" => $strNewFieldData);
			}
			//5
			//$arrDemoHistoryData[] = array("preFieldData" => $rowGetDemoDataMerging['mrGerder'], "newFieldData" => $rowGetDemoDataMerged['mrdGerder']);
			//6
			if($rowGetDemoDataMerging['mrSS']){
				$strPreFieldData = $strNewFieldData = "";
				$arrDemoHistory[] = array("secName" => "patientSS","preField" => "prev_ss", "newField" => "new_ss");	
				$arrTempPreData = array("'".$rowGetDemoDataMerging['mrSS']."'");
				$strPreFieldData = implode(",",$arrTempPreData);
					
				$arrTempPreNewData = array("'".$rowGetDemoDataMerged['mrdSS']."'");
				$strNewFieldData = implode(",",$arrTempPreNewData);
				
				$arrDemoHistoryData[] = array("preFieldData" => $strPreFieldData, "newFieldData" => $strNewFieldData);
			}
			//6
			//$arrDemoHistoryData[] = array("preFieldData" => $rowGetDemoDataMerging['mrSS'], "newFieldData" => $rowGetDemoDataMerged['mrdSS']);
			//7
			if($rowGetDemoDataMerging['mrDOB']){
				$strPreFieldData = $strNewFieldData = "";
				$arrDemoHistory[] = array("secName" => "patientDOB","preField" => "prev_dob", "newField" => "new_dob");	
				$arrTempPreData = array("'".$rowGetDemoDataMerging['mrDOB']."'");
				$strPreFieldData = implode(",",$arrTempPreData);
					
				$arrTempPreNewData = array("'".$rowGetDemoDataMerged['mrdDOB']."'");
				$strNewFieldData = implode(",",$arrTempPreNewData);
				
				$arrDemoHistoryData[] = array("preFieldData" => $strPreFieldData, "newFieldData" => $strNewFieldData);
			}
			//7
			//$arrDemoHistoryData[] = array("preFieldData" => $rowGetDemoDataMerging['mrDOB'], "newFieldData" => $rowGetDemoDataMerged['mrdDOB']);
			//8
			if($rowGetDemoDataMerging['mrAdoOption']){
				$strPreFieldData = $strNewFieldData = "";
				$arrDemoHistory[] = array("secName" => "patientADOopt","preField" => "prev_ado_option", "newField" => "new_ado_option");	
				$arrTempPreData = array("'".$rowGetDemoDataMerging['mrAdoOption']."'");
				$strPreFieldData = implode(",",$arrTempPreData);
				
				
				$arrTempPreNewData = array("'".$rowGetDemoDataMerged['mrdAdoOption']."'");
				$strNewFieldData = implode(",",$arrTempPreNewData);
				
				$arrDemoHistoryData[] = array("preFieldData" => $strPreFieldData, "newFieldData" => $strNewFieldData);
			}
			//8
			//$arrDemoHistoryData[] = array("preFieldData" => $rowGetDemoDataMerging['mrAdoOption'], "newFieldData" => $rowGetDemoDataMerged['mrdAdoOption']);
			
			foreach($arrDemoHistory as $key => $value){
				$qrySaveDemoDataHistory = "insert into patient_previous_data (patient_id,save_date_time,patient_section_name,operator_id,".$arrDemoHistory[$key]['preField'].",".$arrDemoHistory[$key]['newField'].")
												 values('".$this_patient_id."',NOW(),'".$arrDemoHistory[$key]['secName']."','".$_SESSION['authId']."',".$arrDemoHistoryData[$key]['preFieldData'].",".$arrDemoHistoryData[$key]['newFieldData'].")";
				
				$rsSaveDemoDataHistory = imw_query($qrySaveDemoDataHistory);
			}
			
			
			#Directorty :- Root/data/practise/
			#copy patient files - old patient files have not been deleted deliberately
			$dir_name = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/";
			$patient_dir = "PatientId_".$this_patient_id;			
			$merged_patient_dir = "PatientId_".$merge_patient_id;

			if(is_dir($dir_name.$patient_dir) == false){
				mkdir($dir_name.$patient_dir);
			}
			
			if(is_dir($dir_name.$merged_patient_dir)){
				$this->full_copy($dir_name.$merged_patient_dir,$dir_name.$patient_dir);		
			}
			#copy iolink patient files - old patient files have not been deleted deliberately
			$dir_name = $GLOBALS["fileroot"]."/addons/iOLink/";
			$patient_dir = "PatientId_".$this_patient_id;			
			$merged_patient_dir = "PatientId_".$merge_patient_id;
			if(is_dir($dir_name.$patient_dir) == false){
				mkdir($dir_name.$patient_dir);
			}
			
			if(is_dir($dir_name.$merged_patient_dir)){
				$handle = opendir($dir_name.$merged_patient_dir);
				while (false !== ($file = readdir($handle))){
					copy($dir_name.$merged_patient_dir."/".$file, $dir_name.$patient_dir."/".$file);
				}
				closedir($handle);
			}
			$erp_error=array();
			if( isERPPortalEnabled() ) {
				try {
					include_once $GLOBALS['srcdir'].'/erp_portal/patient_summary.php';
					$objPatient_summary = new Patient_summary();
					
					$data=array();
					$data['fromPatientExternalId']=$merge_patient_id;
					$data['toPatientExternalId']=$this_patient_id;
					
					$objPatient_summary->mergePatients($data);
				} catch(Exception $e) {
					$erp_error[]='Unable to connect to ERP Portal';
				}
			}
			
			
			$_SESSION['patient'] = trim($this_patient_id);
			$return_arr['status']   = 'success';
			$return_arr['err_msg']   = '';
			$return_arr['old_name'] = $request['merge_patient_name'];
			$return_arr['new_name'] = $request['this_patient_name'];
			return $return_arr;
		}
	}
	
	
	
}
?>