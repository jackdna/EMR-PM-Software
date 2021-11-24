<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
	include_once("insurance_default_case.php"); //$defaultCaseName IS SET FROM imwemr IN THIS FILE
	include('connect_imwemr.php'); //imwemr connection
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	//$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
	$sa_patient_id='';
	//START GET PATIENT-ID IN CASE OF RESYNCRONIZATION
	$getSqlAppointmentsQry			= "SELECT * FROM schedule_appointments 
										WHERE iolink_iosync_waiting_id = '".$patient_in_waiting_id."'
										AND iolink_iosync_waiting_id != ''
										AND iolink_iosync_waiting_id != '0'
										";//AND sa_app_start_date='".$patient_dos_temp."'
	$getSqlAppointmentsRes 			= imw_query($getSqlAppointmentsQry) or die($getSqlAppointmentsQry.imw_error());		
	$getSqlAppointmentsNumRow 		= imw_num_rows($getSqlAppointmentsRes);				
	$sa_comments="";
	$sa_doctor_id					= "";
	$prev_procedureid				= "";
	if($getSqlAppointmentsNumRow>0) {
		$getSqlAppointmentsRow 		= imw_fetch_array($getSqlAppointmentsRes);
		$sa_patient_id 				= $getSqlAppointmentsRow['sa_patient_id'];
		$sa_comments 				= trim($getSqlAppointmentsRow['sa_comments']);
		$sa_doctor_id				= trim($getSqlAppointmentsRow['sa_doctor_id']);
		$prev_procedureid			= trim($getSqlAppointmentsRow['procedureid']);
	}
	//END GET PATIENT-ID IN CASE OF RESYNCRONIZATION
	
	//GET DOCTOR ID
	$andSurgeonNameQry = " AND  fname = '".addslashes($surgeon_fname)."' AND mname = '".addslashes($surgeon_mname)."' AND lname = '".addslashes($surgeon_lname)."' ";
	if(constant('CHECK_USER_NPI')=='YES') {
		$andSurgeonNameQry 			= " AND user_npi = '".$surgeon_npi."' AND user_npi != '' AND user_npi != '0' ";
	}
	$surgeonNameQry 				= "SELECT id FROM users WHERE delete_status='0' AND user_type = '1' ".$andSurgeonNameQry;
	$surgeonNameRes 				= imw_query($surgeonNameQry) or die($surgeonNameQry.imw_error());
	$surgeonNameNumRow 				= imw_num_rows($surgeonNameRes);
	
	if($surgeonNameNumRow>0) {
		$surgeonNameRow 			= imw_fetch_array($surgeonNameRes);
		if(!$sa_doctor_id) {
			$sa_doctor_id			= $surgeonNameRow['id'];
		}
	}else {
		//DO NOT INSERT NEW DOCTOR
	}
	//END GET DOCTOR ID

	//START GET PATIENT-ID FOR imwemr
	$imedic_patient_sex='';
	if($patient_sex=='m') { 
		$imedic_patient_sex='Male'; 
	}else if($patient_sex=='f') {
		$imedic_patient_sex='Female'; 
	}
	$drOfficePatientIdVal='0';
	if($drOfficePatientId) {
		$drOfficePatientIdVal = 'drOffice'.$drOfficePatientId;
	}
	unset($patientDataArr);
	$patientDataArr['title']		= addslashes($patient_title);
	$patientDataArr['fname']		= addslashes($patient_first_name);
	$patientDataArr['mname']		= addslashes($patient_middle_name);
	$patientDataArr['lname']		= addslashes($patient_last_name);
	$patientDataArr['suffix']		= addslashes($patient_suffix);
	$patientDataArr['DOB']			= $patient_dob_temp;
	$patientDataArr['sex']			= $imedic_patient_sex; 
	$patientDataArr['street']		= addslashes($patient_address1);
	$patientDataArr['street2']		= addslashes($patient_address2);
	$patientDataArr['city']			= addslashes($patient_city);
	$patientDataArr['state']		= addslashes($patient_state);
	$patientDataArr['postal_code']	= $patient_zip;
	$patientDataArr['phone_home']	= $patient_home_phone;
	$patientDataArr['phone_biz']	= $patient_work_phone;
	$patientDataArr['idoc_iolink_patient_id']		= $drOfficePatientIdVal;
	$patientDataArr['language']		= trim(addslashes($patient_language));
	$patientDataArr['race']			= trim(addslashes($patient_race));
	$patientDataArr['ethnicity']	= trim(addslashes($patient_ethnicity));
	
	//START GET PATIENT-ID IF NOT EXIST
	if(!$sa_patient_id) {
		$chkPatientAthenaIdQry 		= "SELECT id FROM patient_data WHERE 
										idoc_iolink_patient_id='".$drOfficePatientIdVal."' 
											AND idoc_iolink_patient_id!=''
											AND idoc_iolink_patient_id!='0'  
											AND idoc_iolink_patient_id!='drOffice'";
		$chkPatientAthenaIdRes 		= imw_query($chkPatientAthenaIdQry) or die($chkPatientAthenaIdQry.imw_error());
		$chkPatientAthenaIdNumRow 	= imw_num_rows($chkPatientAthenaIdRes);
		if($chkPatientAthenaIdNumRow <= 0) {
			$chkPatientAthenaIdQry 		= "SELECT id FROM patient_data WHERE 
												fname 			= '".addslashes($patient_first_name)."' 
												AND lname		= '".addslashes($patient_last_name)."' 
												AND DOB 		= '".$patient_dob_temp."' 
												AND postal_code = '".addslashes($patient_zip)."'";	
			$chkPatientAthenaIdRes 		= imw_query($chkPatientAthenaIdQry) or die($chkPatientAthenaIdQry.imw_error());
			$chkPatientAthenaIdNumRow 	= imw_num_rows($chkPatientAthenaIdRes);
		}
		if($chkPatientAthenaIdNumRow>0) {
			$chkPatientAthenaIdRow	= imw_fetch_array($chkPatientAthenaIdRes);
			$sa_patient_id 			= $chkPatientAthenaIdRow['id'];
		}	
	}
	//END GET PATIENT-ID IF NOT EXIST
	if($sa_patient_id) {//IF RESYNCRONIZE CASE THEN UPDATE PATIENT-RECORD
		
		//START SEND PATIENT IMAGE 
		$imwPtSavePath	= putPtImgContentToImw($patient_image_path,$patient_id,$sa_patient_id);
		if($imwPtSavePath) {
			$patientDataArr['p_imagename']	= trim($imwPtSavePath);
		}
		//END SEND PATIENT IMAGE TO
		
		//UPDATE RECORD
		$objManageData->updateRecords($patientDataArr, 'patient_data','id',$sa_patient_id);
		
		//START CODE TO UPDATE PATIENT-NAME IN SCH-APPOINTMENT
		$ptMdlNmeFrstLtr='';
		if($patient_middle_name) {  $ptMdlNmeFrstLtr = ' '.substr($patient_middle_name,0,1);}// FIRSTNAME MIDDLENAME (1CHAR) LASTNAME		
		$imw_sch_pt_name = trim($patient_last_name.', '.$patient_first_name.$ptMdlNmeFrstLtr);
		
		unset($arrayPtNmeSchAppt);
		$arrayPtNmeSchAppt['sa_patient_name'] = addslashes($imw_sch_pt_name);
		$objManageData->updateRecords($arrayPtNmeSchAppt, 'schedule_appointments','sa_patient_id',$sa_patient_id);
		//END CODE TO UPDATE PATIENT-NAME IN SCH-APPOINTMENT
		
	}else{	
		$getPatientMaxIdQry 			= "SELECT MAX(id) AS maxPatientId FROM patient_data";
		$getPatientMaxIdRes 			= imw_query($getPatientMaxIdQry) or die($getPatientMaxIdQry.imw_error());
		$getPatientMaxIdNumRow 			= imw_num_rows($getPatientMaxIdRes);
		$maxPatientIdNew=1;//BY DEFAULT
		if($getPatientMaxIdNumRow>0) {
			$getPatientMaxIdRow 		= imw_fetch_array($getPatientMaxIdRes);
			$maxPatientId				= $getPatientMaxIdRow['maxPatientId'];
			$maxPatientIdNew			= $maxPatientId+1;
		}
		
		$patientDataArr['id']			= $maxPatientIdNew;
		$patientDataArr['pid']			= $maxPatientIdNew;
		$patientDataArr['providerID'] 	= $sa_doctor_id;
		
		//START SEND PATIENT IMAGE 
		$imwPtSavePath	= putPtImgContentToImw($patient_image_path,$patient_id,$maxPatientIdNew);
		if($imwPtSavePath) {
			$patientDataArr['p_imagename']	= trim($imwPtSavePath);
		}
		//END SEND PATIENT IMAGE	
		
		//ADD RECORD
		$sa_patient_id=$objManageData->addRecords($patientDataArr, 'patient_data');
		//$sa_patient_id=$maxPatientIdNew;
	}
	//END GET PATIENT-ID FOR imwemr
	
	//Update Patient Religion in iDOC Misc. table
	if( $sa_patient_id ) { 
		$qryR = "select id From custom_fields where control_lable = 'religion' "; 
		$sqlR = imw_query($qryR) or die(imw_error());
		if( imw_num_rows($sqlR) > 0 ) {
			$resR = imw_fetch_assoc($sqlR);
			$qryR2 = "select id From patient_custom_field where patient_id = ".(int)$sa_patient_id." and admin_control_id = ".(int)$resR['id']." ";
			$sqlR2 = imw_query($qryR2) or die(imw_error());
			if( imw_num_rows($sqlR2) > 0 ) {
				$resR2 = imw_fetch_assoc($sqlR2);
				$rec_id = $resR2['id'];

				$uQry = "Update patient_custom_field Set patient_control_value = '".addslashes($patient_religion)."', modified_by = '0', modified_date_time = '".date('Y-m-d H:i:s')."'  Where patient_id = ".(int)$sa_patient_id." and admin_control_id = ".(int)$resR['id']." and id = ".(int)$rec_id." ";
			}
			else {
				$uQry = "Insert into patient_custom_field Set patient_control_value = '".addslashes($patient_religion)."', patient_id = ".(int)$sa_patient_id.", admin_control_id = ".(int)$resR['id'].", created_by = '0', created_date_time = '".date('Y-m-d H:i:s')."' ";
			}
			$uSql = imw_query($uQry) or die(imw_error());
		}
	}
	//Update Patient Religion in iDOC Misc. table

	//START GET INSURANCE-CASE
	include("common/conDb.php");
	
	/*Update imwPatient Id in iolink*/
	if($sa_patient_id && $patient_id){
		$objManageData->updateRecords(array('imwPatientId'=>$sa_patient_id), 'patient_data_tbl','patient_id',$patient_id);
	}
	
	$insuranceCaseQry 			= "SELECT * FROM iolink_insurance_case WHERE patient_id='".$patient_id."'";
	$insuranceCaseRes 			= imw_query($insuranceCaseQry) or die(imw_error());
	$insuranceCaseNumRow 		= imw_num_rows($insuranceCaseRes);
	if($insuranceCaseNumRow>0) {
		while($insuranceCaseRow = imw_fetch_array($insuranceCaseRes)) {
			include("common/conDb.php");
			$ins_caseid 		= $insuranceCaseRow['ins_caseid'];
			$ins_case_name 		= stripslashes($insuranceCaseRow['ins_case_name']);
			//$ins_case_type 	= $insuranceCaseRow['ins_case_type'];
			$start_date 		= stripslashes($insuranceCaseRow['start_date']);
			$end_date 			= stripslashes($insuranceCaseRow['end_date']);
			//$case_id 			= $insuranceCaseRow['case_id'];
			$case_status 		= $insuranceCaseRow['case_status'];
			$case_name 			= $insuranceCaseRow['case_name'];
			$vision 			= $insuranceCaseRow['vision'];
			$normal 			= $insuranceCaseRow['normal'];
			
			include('connect_imwemr.php'); //imwemr connection
			$imedic_ins_caseTypeId='';
			$imedicInsCaseId='';
			$chkInsuranceCaseNameQry 		= "SELECT * FROM insurance_case_types WHERE case_name='".$case_name."' AND vision='".$vision."' AND normal='".$normal."' LIMIT 0,1";
			$chkInsuranceCaseNameRes 		= imw_query($chkInsuranceCaseNameQry) or die(imw_error());
			$chkInsuranceCaseNameNumRow 	= imw_num_rows($chkInsuranceCaseNameRes);
			if($chkInsuranceCaseNameNumRow<=0) {
				//GET DEFAULT CASE FROM imwemr
				$chkInsuranceCaseNameQry 	= "SELECT * FROM insurance_case_types WHERE case_name='".$defaultCaseName."'";
				$chkInsuranceCaseNameRes 	= imw_query($chkInsuranceCaseNameQry) or die(imw_error());
				$chkInsuranceCaseNameNumRow = imw_num_rows($chkInsuranceCaseNameRes);
			}
			if($chkInsuranceCaseNameNumRow>0) {
				$chkInsuranceCaseNameRow 	= imw_fetch_array($chkInsuranceCaseNameRes);
				$imedic_ins_caseTypeId 		= $chkInsuranceCaseNameRow['case_id'];
				$imedic_ins_caseName 		= $chkInsuranceCaseNameRow['case_name'];
				
				unset($arrayInsCaseRecord);
				$arrayInsCaseRecord['ins_case_name'] 	= addslashes($imedic_ins_caseName);
				$arrayInsCaseRecord['ins_case_type']	= $imedic_ins_caseTypeId;
				$arrayInsCaseRecord['patient_id'] 		= $sa_patient_id;
				$arrayInsCaseRecord['start_date'] 		= addslashes($start_date);
				$arrayInsCaseRecord['end_date'] 		= addslashes($end_date);
				//$arrayInsCaseRecord['case_id'] 		= addslashes($imedic_sch_patient_name);
				$arrayInsCaseRecord['case_status'] 		= addslashes($case_status);
				$arrayInsCaseRecord['athenaID'] 		= $ins_caseid;
				
				$chkInsCaseIdQry 		= "SELECT ins_caseid FROM insurance_case WHERE ins_case_type='".$imedic_ins_caseTypeId."' AND patient_id='".$sa_patient_id."' AND case_status='".addslashes($case_status)."'";
				$chkInsCaseIdRes 		= imw_query($chkInsCaseIdQry) or die(imw_error());
				$chkInsCaseIdNumRow 	= imw_num_rows($chkInsCaseIdRes);
				if($chkInsCaseIdNumRow>0) {
					$chkInsCaseIdRow 	= imw_fetch_array($chkInsCaseIdRes);
					$imedicInsCaseId 	= $chkInsCaseIdRow['ins_caseid'];
					$objManageData->updateRecords($arrayInsCaseRecord, 'insurance_case','ins_caseid',$imedicInsCaseId);
				}else {
					$imedicInsCaseId	= $objManageData->addRecords($arrayInsCaseRecord, 'insurance_case');
				}
			}
			if($imedicInsCaseId) {
				include("common/conDb.php");
				$insuranceDataQry 		= "SELECT * FROM insurance_data WHERE patient_id='".$patient_id."' AND ins_caseid='".$ins_caseid."' AND actInsComp='1' ORDER BY id";
				$insuranceDataRes 		= imw_query($insuranceDataQry) or die(imw_error());
				$insuranceDataNumRow 	= imw_num_rows($insuranceDataRes);
				if($insuranceDataNumRow>0) {
					while($insuranceDataRow 		= imw_fetch_array($insuranceDataRes)) {
						include("common/conDb.php");
						unset($arrayInsDataRecord);//$iolinkIns
						
						$iolinkInsId 				= $insuranceDataRow['id'];
						$iolinkInsType 				= $insuranceDataRow['type'];
						$iolinkInsactInsComp 		= $insuranceDataRow['actInsComp'];
						$iolinkInsProviderName 		= $insuranceDataRow['ins_provider'];
						$iolinkInsInHouseCode 		= $insuranceDataRow['ins_in_house_code'];
						
						include('connect_imwemr.php'); //imwemr connection
						$andInHouseCodeQry = "";
						if(trim($iolinkInsInHouseCode)) { $andInHouseCodeQry = " AND in_house_code = '".addslashes($iolinkInsInHouseCode)."' "; }
						$insCompQry 				= "select id from insurance_companies 
														where  name = '".addslashes($iolinkInsProviderName)."' AND ins_del_status = '0' ".$andInHouseCodeQry." ORDER BY id";
						$rsInsCompQry 				= imw_query($insCompQry) or die(imw_error());	
						$numRowrInsCompQry 			= imw_num_rows($rsInsCompQry);				
						$insProviderId				='';
						if($numRowrInsCompQry > 0){
							$insDetails 			= imw_fetch_array($rsInsCompQry);
							$insProviderId 			= $insDetails['id'];
						}
						include("common/conDb.php");
						unset($inCompleteInsStatusArr);
						if($insProviderId) {
							//INSURANCE SCAN-IMAGES
							$scan_card 				= $insuranceDataRow['scan_card'];
							$scan_card2		 		= $insuranceDataRow['scan_card2'];
							
							$insuranceDataScanCard1 = getInsScanContentForImw($scan_card);
							$insuranceDataScanCard2 = getInsScanContentForImw($scan_card2);
							
							$insuranceDataScanCard1 = base64_decode($insuranceDataScanCard1);
							$insuranceDataScanCard2 = base64_decode($insuranceDataScanCard2);
							
							$scan_card 				= putInsImgContentToImw($scan_card,$insuranceDataScanCard1,$sa_patient_id,$imwDirectoryName); 
							$scan_card2 			= putInsImgContentToImw($scan_card2,$insuranceDataScanCard2,$sa_patient_id,$imwDirectoryName); 
							//INSURANCE SCAN IMAGES
							$authRequired			= "";
							if(trim($insuranceDataRow['authorization_number'])) {
								$authRequired		= 'Yes';	
							}
							
							$arrayInsDataRecord['type'] 					=  $insuranceDataRow['type'];
							$arrayInsDataRecord['provider'] 				=  $insProviderId;
							$arrayInsDataRecord['plan_name'] 				=  $insuranceDataRow['plan_name'];
							$arrayInsDataRecord['policy_number'] 			=  $insuranceDataRow['policy'];
							$arrayInsDataRecord['group_number'] 			=  $insuranceDataRow['group_name'];
							$arrayInsDataRecord['subscriber_lname'] 		=  $insuranceDataRow['lname'];
							$arrayInsDataRecord['subscriber_mname'] 		=  $insuranceDataRow['mname'];
							$arrayInsDataRecord['subscriber_fname'] 		=  $insuranceDataRow['fname'];
							$arrayInsDataRecord['subscriber_relationship'] 	=  $insuranceDataRow['sub_relation'];
							$arrayInsDataRecord['subscriber_ss'] 			=  $insuranceDataRow['ssn'];
							$arrayInsDataRecord['subscriber_DOB'] 			=  $insuranceDataRow['dob'];
							
							if( 	 trim($insuranceDataRow['address1']) && trim($insuranceDataRow['zip_code']) 
									&& trim($insuranceDataRow['city']) && trim($insuranceDataRow['state'])
									&& trim($insuranceDataRow['home_phone']) )
							{
								$arrayInsDataRecord['subscriber_street'] 		=  $insuranceDataRow['address1'];
								$arrayInsDataRecord['subscriber_street_2'] 		=  $insuranceDataRow['address2'];
								$arrayInsDataRecord['subscriber_postal_code'] =  $insuranceDataRow['zip_code'];
								$arrayInsDataRecord['subscriber_city'] 			=  $insuranceDataRow['city'];
								$arrayInsDataRecord['subscriber_state'] 		=  $insuranceDataRow['state'];
								$arrayInsDataRecord['subscriber_phone'] 		=  $insuranceDataRow['home_phone'];
								$arrayInsDataRecord['subscriber_biz_phone'] 	=  $insuranceDataRow['work_phone'];
								$arrayInsDataRecord['subscriber_mobile'] 		=  $insuranceDataRow['mbl_phone'];
							}
							
							$arrayInsDataRecord['copay'] 					=  $insuranceDataRow['copay'];
							$arrayInsDataRecord['pid'] 						=  $sa_patient_id;
							$arrayInsDataRecord['subscriber_sex'] 			=  $insuranceDataRow['gender'];
							$arrayInsDataRecord['scan_card'] 				=  addslashes($scan_card);
							$arrayInsDataRecord['scan_label'] 				=  $insuranceDataRow['scan_label'];
							$arrayInsDataRecord['effective_date'] 			=  $insuranceDataRow['active_date'];
							$arrayInsDataRecord['expiration_date'] 			=  $insuranceDataRow['expiry_Date'];
							$arrayInsDataRecord['ins_caseid'] 				=  $imedicInsCaseId;
							$arrayInsDataRecord['claims_adjustername'] 		=  $insuranceDataRow['claims_adjustername'];
							$arrayInsDataRecord['claims_adjusterphone'] 	=  $insuranceDataRow['claims_adjusterphone'];
							$arrayInsDataRecord['Sec_HCFA'] 				=  $insuranceDataRow['Sec_HCFA'];
							$arrayInsDataRecord['newComDate'] 				=  $insuranceDataRow['newComDate'];
							$arrayInsDataRecord['actInsComp'] 				=  $insuranceDataRow['actInsComp'];
							$arrayInsDataRecord['actInsCompDate'] 			=  $insuranceDataRow['actInsCompDate'];
							$arrayInsDataRecord['scan_card2'] 				=  addslashes($scan_card2);
							$arrayInsDataRecord['scan_label2'] 				=  $insuranceDataRow['scan_label2'];
							$arrayInsDataRecord['cardscan_date'] 			=  $insuranceDataRow['cardscan_date'];
							$arrayInsDataRecord['cardscan_comments'] 		=  $insuranceDataRow['cardscan_comments'];
							$arrayInsDataRecord['cardscan1_datetime'] 		=  $insuranceDataRow['cardscan1_datetime'];
							$arrayInsDataRecord['self_pay_provider'] 		=  $insuranceDataRow['self_pay_provider'];
							$arrayInsDataRecord['auth_required'] 			=  $authRequired;
							
							
							include('connect_imwemr.php'); //imwemr connection
							$chkInsDataQry 				= "SELECT id FROM insurance_data WHERE type='".$iolinkInsType."' AND pid='".$sa_patient_id."' AND ins_caseid='".$imedicInsCaseId."' AND actInsComp='".$iolinkInsactInsComp."'";
							$chkInsDataRes 				= imw_query($chkInsDataQry) or die(imw_error().$chkInsDataQry);
							$chkInsDataNumRow 			= imw_num_rows($chkInsDataRes);
							if($chkInsDataNumRow>0) {
								$chkInsDataRow			= imw_fetch_array($chkInsDataRes);
								$insuranceAutoIncrId	= $chkInsDataRow['id'];
								$objManageData->updateRecords($arrayInsDataRecord, 'insurance_data','id',$insuranceAutoIncrId);
							}else {
								$insuranceAutoIncrId 	= $objManageData->addRecords($arrayInsDataRecord, 'insurance_data');
							}
							
							
							if(trim($insuranceDataRow['authorization_number'])) {
								$insTypeNum = '';
								switch($insuranceDataRow['type']) {
									case 'primary':		$insTypeNum = '1';break;
									case 'secondary':	$insTypeNum = '2';break;
									case 'tertiary':	$insTypeNum = '3';break;	
								}
								$authNumArr = explode(",",$insuranceDataRow['authorization_number']);
								foreach($authNumArr as $authNumber) {								
									unset($arrayAuthNumberRecord);
									$arrayAuthNumberRecord['patient_id'] 		=  $sa_patient_id;
									$arrayAuthNumberRecord['auth_name'] 		=  trim($authNumber);
									$arrayAuthNumberRecord['auth_operator'] 	=  '1';
									$arrayAuthNumberRecord['cur_date'] 			=  date('Y-m-d');
									$arrayAuthNumberRecord['ins_case_id'] 		=  $imedicInsCaseId;
									$arrayAuthNumberRecord['ins_provider'] 		=  $insProviderId;
									$arrayAuthNumberRecord['ins_data_id'] 		=  $insuranceAutoIncrId;
									$arrayAuthNumberRecord['ins_type'] 			=  $insTypeNum;
									$arrayAuthNumberRecord['auth_status'] 		=  '0'; //0-Active, 1-Delete
				
									$chkptAuthQry 				= "SELECT a_id FROM patient_auth WHERE auth_name='".trim($authNumber)."' AND ins_data_id='".$insuranceAutoIncrId."' AND patient_id='".$sa_patient_id."'";
									$chkptAuthRes 				= imw_query($chkptAuthQry) or die(imw_error().$chkptAuthQry);
									$chkptAuthNumRow 			= imw_num_rows($chkptAuthRes);
									if($chkptAuthNumRow>0) {
										$chkptAuthRow			= imw_fetch_array($chkptAuthRes);
										$a_id	= $chkptAuthRow['a_id'];
										$objManageData->updateRecords($arrayAuthNumberRecord, 'patient_auth','a_id',$a_id);
									}else {
										$objManageData->addRecords($arrayAuthNumberRecord, 'patient_auth');
									}								
								}
							}
							$inCompleteInsStatusArr['inCompleteInsStatus']='';
						}else if(!$insProviderId) {
							$inCompleteInsStatusArr['inCompleteInsStatus']='Yes';
						}
						//CODE TO SET INCOMPLETE STATUS
						include("common/conDb.php");
						$objManageData->updateRecords($inCompleteInsStatusArr, 'insurance_data','id',$iolinkInsId);					
					}
				}
				include("common/conDb.php");
				$insuranceScanDocQry 				= "SELECT * FROM iolink_insurance_scan_documents WHERE patient_id='".$patient_id."' AND ins_caseid='".$ins_caseid."'";
				$insuranceScanDocRes 				= imw_query($insuranceScanDocQry) or die(imw_error());
				$insuranceScanDocNumRow 			= imw_num_rows($insuranceScanDocRes);
				if($insuranceScanDocNumRow>0) {
					while($insuranceScanDocRow 		= imw_fetch_array($insuranceScanDocRes)) {
						include("common/conDb.php");
						$iolinkScanDocInsType 		= $insuranceScanDocRow['type'];
						
						//INSURANCE SCAN DOC-IMAGES
						$scanDoc_card 				= $insuranceScanDocRow['scan_card'];
						$scanDoc_card2 				= $insuranceScanDocRow['scan_card2'];
						
						$insuranceScanDocScanCard1 	= getInsScanContentForImw($scanDoc_card);
						$insuranceScanDocScanCard2 	= getInsScanContentForImw($scanDoc_card2);
						
						$insuranceScanDocScanCard1 	= base64_decode($insuranceScanDocScanCard1);
						$insuranceScanDocScanCard2 	= base64_decode($insuranceScanDocScanCard2);
						
						$scanDoc_card 				= putInsImgContentToImw($scanDoc_card,$insuranceScanDocScanCard1,$sa_patient_id,$imwDirectoryName); 
						$scanDoc_card2 				= putInsImgContentToImw($scanDoc_card2,$insuranceScanDocScanCard2,$sa_patient_id,$imwDirectoryName); 
						//INSURANCE SCAN DOC-IMAGES
							
						unset($arrayInsScanDocRecord);
						$arrayInsScanDocRecord['type'] 				=  $iolinkScanDocInsType;
						$arrayInsScanDocRecord['ins_caseid'] 		=  $imedicInsCaseId;
						$arrayInsScanDocRecord['patient_id'] 		=  $sa_patient_id;
						$arrayInsScanDocRecord['scan_card'] 		=  $scanDoc_card;
						$arrayInsScanDocRecord['scan_label'] 		=  $insuranceScanDocRow['scan_label'];
						$arrayInsScanDocRecord['scan_card2'] 		=  $scanDoc_card2;
						$arrayInsScanDocRecord['scan_label2'] 		=  $insuranceScanDocRow['scan_label2'];
						$arrayInsScanDocRecord['created_date'] 		=  $insuranceScanDocRow['created_date'];
						$arrayInsScanDocRecord['document_status'] 	=  $insuranceScanDocRow['document_status'];
						$arrayInsScanDocRecord['cardscan_date'] 	=  $insuranceScanDocRow['cardscan_date'];
						$arrayInsScanDocRecord['cardscan_comments'] =  $insuranceScanDocRow['cardscan_comments'];
						$arrayInsScanDocRecord['cardscan1_date'] 	=  $insuranceScanDocRow['cardscan1_date'];
						
						include('connect_imwemr.php'); //imwemr connection
						$chkInsScanDocQry 		= "SELECT scan_documents_id FROM insurance_scan_documents WHERE type='".$iolinkScanDocInsType."' AND patient_id='".$sa_patient_id."' AND ins_caseid='".$imedicInsCaseId."'";
						$chkInsScanDocRes 		= imw_query($chkInsScanDocQry) or die(imw_error().$chkInsScanDocQry);
						$chkInsScanDocNumRow 	= imw_num_rows($chkInsScanDocRes);
						if($chkInsScanDocNumRow>0) {
							$chkInsScanDocRow	= imw_fetch_array($chkInsScanDocRes);
							$insuranceScanDocAutoIncrId	= $chkInsScanDocRow['scan_documents_id'];
							$objManageData->updateRecords($arrayInsScanDocRecord, 'insurance_scan_documents','scan_documents_id',$insuranceScanDocAutoIncrId);
						}else {
							$insuranceScanDocAutoIncrId = $objManageData->addRecords($arrayInsScanDocRecord, 'insurance_scan_documents');
						}
					}
				}
				
			}
		}
	}
	include('connect_imwemr.php'); //imwemr connection
	
	//END GET INSURANCE-CASE
	
	//START GET FACILITY-ID
	/*
	$facilityType			=1;
	$imedic_facility_id		='';//INITIALIZE VARIABLE
	$getFacilityIdQry 		= "SELECT id FROM `facility` WHERE facility_type='".$facilityType."'";
	$getFacilityIdRes 		= imw_query($getFacilityIdQry) or die($getFacilityIdQry.imw_error());
	$getFacilityIdNumRow 	= imw_num_rows($getFacilityIdRes);
	if($getFacilityIdNumRow>0) {
		$getFacilityIdRow 	= imw_fetch_array($getFacilityIdRes);
		$imedic_facility_id = $getFacilityIdRow['id'];
	}
	*/
	if(trim($iasc_facility_id)) {
		$imwFacilityIdNew = $iasc_facility_id;	
	}else if(trim($_SESSION['iolink_iasc_facility_id'])) {
		$imwFacilityIdNew = $_SESSION['iolink_iasc_facility_id'];
	}
	if(!$imwFacilityIdNew) {
		$imwFacilityIdNew = '1';
	}
	//END GET FACILITY-ID
	//START GET TIME SLOT INTERVAL FROM common/conDb.php
	if(!$constantImwSlotMinute) {
		$constantImwSlotMinute='5';
	}
	//END GET TIME SLOT INTERVAL FROM common/conDb.php
	
	//START GET PROCEDURE-ID
	$imedic_procedureid		= '';//INITIALIZE VARIABLE
	$imedic_sec_procedureid	= '';//INITIALIZE VARIABLE
	$imedic_ter_procedureid	= '';//INITIALIZE VARIABLE
	$imedic_duration		= '';//INITIALIZE VARIABLE
	$imedic_endtime			= '';//INITIALIZE VARIABLE
	$imedic_appt_start_time	= '';//INITIALIZE VARIABLE
	
	$imedicDurationNumRow = 0;
	$proc_sa_doctor_id = "0";
	if($sa_doctor_id) { 
		$proc_sa_doctor_id = $sa_doctor_id;
	}
	//PRIMARY PROCEDURE
	if($patient_prim_proc) {
		$procExactMatchWithAlias	= true;
		$andAcronymQry 				= trim($patient_prim_proc_alias) ? " AND acronym!='' " : "";
		$imedicProcedureidQry 		= "SELECT id,proc_time FROM `slot_procedures` WHERE  active_status = 'yes' AND (doctor_id='".$proc_sa_doctor_id."' OR doctor_id='0') AND ((proc = '".addslashes($patient_prim_proc)."' AND proc!='') AND (acronym = '".addslashes($patient_prim_proc_alias)."' ".$andAcronymQry."))  ORDER BY doctor_id DESC LIMIT 0,1";
		$imedicProcedureidRes 		= imw_query($imedicProcedureidQry) or die($imedicProcedureidQry.imw_error());
		$imedicProcedureidNumRow 	= imw_num_rows($imedicProcedureidRes);
		if($imedicProcedureidNumRow<=0) {
			if(trim($patient_prim_proc_alias)!="") {
				$procExactMatchWithAlias = false;
			}
			$imedicProcedureidQry 	= "SELECT id,proc_time FROM `slot_procedures` WHERE  active_status = 'yes' AND (doctor_id='".$proc_sa_doctor_id."' OR doctor_id='0') AND ((proc = '".addslashes($patient_prim_proc)."' AND proc!='') OR (acronym = '".addslashes($patient_prim_proc)."' AND acronym!=''))  ORDER BY doctor_id DESC LIMIT 0,1";
			$imedicProcedureidRes 	= imw_query($imedicProcedureidQry) or die($imedicProcedureidQry.imw_error());
			$imedicProcedureidNumRow= imw_num_rows($imedicProcedureidRes);
		}
		if($imedicProcedureidNumRow<=0) {
			$imedicProcedureidQry 	= "SELECT id,proc_time FROM `slot_procedures` WHERE active_status = 'yes' AND (doctor_id='".$proc_sa_doctor_id."' OR doctor_id='0') AND ((proc = '".addslashes($patient_prim_proc_alias)."' AND proc!='') OR (acronym = '".addslashes($patient_prim_proc_alias)."' AND acronym!=''))  ORDER BY doctor_id DESC LIMIT 0,1";
			$imedicProcedureidRes 	= imw_query($imedicProcedureidQry) or die($imedicProcedureidQry.imw_error());
			$imedicProcedureidNumRow= imw_num_rows($imedicProcedureidRes);
		}
		if($imedicProcedureidNumRow>0) {
			$imedicProcedureidRow 	= imw_fetch_array($imedicProcedureidRes);
			$imedic_procedureid 	= $imedicProcedureidRow['id'];
			$imedic_proc_time 		= $imedicProcedureidRow['proc_time'];//ID ENTERED TO GET MINUTES FOR DURATION
			
			//START GET DURATION AND END TIME
			$imedicDurationQry 		= "SELECT times FROM `slot_procedures` WHERE id='".$imedic_proc_time."'";
			$imedicDurationRes 		= imw_query($imedicDurationQry) or die($imedicDurationQry.imw_error());
			$imedicDurationNumRow 	= imw_num_rows($imedicDurationRes);
			if($imedicDurationNumRow>0) {
				$imedicDurationRow 	= imw_fetch_array($imedicDurationRes);
				$imedicTimeInMinute = $imedicDurationRow['times'];
				$imedic_duration 	= $imedicTimeInMinute*60;
				/*
				if($surgery_time != 0 || $surgery_time!='00:00:00') {
					$surgery_timeExplode= explode(":",$surgery_time);
					$imedic_endtime 	= date("H:i:s",mktime($surgery_timeExplode[0],$surgery_timeExplode[1]+$imedicTimeInMinute,$surgery_timeExplode[2],0,0,0));
				}*/
			}
			if(constant("SYNC_PROC_WITH_IMW")=="YES") {
			//	if($procExactMatchWithAlias == false) {
					$saveProcQry 			= "UPDATE `slot_procedures` SET proc = '".addslashes($patient_prim_proc)."', acronym = '".addslashes($patient_prim_proc_alias)."' WHERE id='".$imedic_procedureid."' ";
					$saveProcRes			= imw_query($saveProcQry) or die($saveProcQry.imw_error());
			//	}
			}
			//END GET DURATION AND END TIME
		}else {
			if(constant("SYNC_PROC_WITH_IMW")=="YES") {
				//START CODE TO INSERT PROCEDURE IN IMW (BASED ON GLOBAL CONFIGURATION)
				$imedicTimeInMinute		= trim($constantImwSlotMinute) ? trim($constantImwSlotMinute) : 10;
				$procTimeQry 			= "SELECT id FROM `slot_procedures` WHERE times='".$imedicTimeInMinute."' ORDER BY id LIMIT 0,1";
				$procTimeRes 			= imw_query($procTimeQry) or die($procTimeQry.imw_error());
				if(imw_num_rows($procTimeRes)>0) {
					$procTimeRow 		= imw_fetch_array($procTimeRes);
					$imedic_proc_time 	= $procTimeRow['id'];//ID ENTERED TO GET MINUTES FOR DURATION
				}else {
					$insProcTimeQry 	= "INSERT INTO `slot_procedures` SET times='".$imedicTimeInMinute."'";
					$insProcTimeRes		= imw_query($insProcTimeQry) or die($insProcTimeQry.imw_error());
					$imedic_proc_time 	= imw_insert_id();
				}
				$imedic_duration 		= $imedicTimeInMinute*60;
				$insProcQry 			= "INSERT INTO `slot_procedures` SET proc_time = '".$imedic_proc_time."', active_status = 'yes', proc = '".addslashes($patient_prim_proc)."', acronym = '".addslashes($patient_prim_proc_alias)."', user_group = '8' ";
				// added default user group to 8 (Surgical Coordinator) in slot_procedures 
				$insProcTimeRes			= imw_query($insProcQry) or die($insProcQry.imw_error());
				$imedic_procedureid		= imw_insert_id();
				//END CODE TO INSERT PROCEDURE IN IMW (BASED ON GLOBAL CONFIGURATION)
			}
		}
	}
	
	//SECONDARY PROCEDURE
	if($patient_sec_proc) {
		$imedicSecProcedureidQry 		= "SELECT id,proc_time FROM `slot_procedures` WHERE  active_status = 'yes' AND (doctor_id='".$proc_sa_doctor_id."' OR doctor_id='0') AND ((proc = '".addslashes($patient_sec_proc)."' AND proc!='') OR (acronym = '".addslashes($patient_sec_proc)."' AND acronym!=''))  ORDER BY doctor_id DESC LIMIT 0,1";
		$imedicSecProcedureidRes 		= imw_query($imedicSecProcedureidQry) or die($imedicSecProcedureidQry.imw_error());
		$imedicSecProcedureidNumRow 	= imw_num_rows($imedicSecProcedureidRes);
		if($imedicSecProcedureidNumRow<=0) {
			$imedicSecProcedureidQry 		= "SELECT id,proc_time FROM `slot_procedures` WHERE active_status = 'yes' AND (doctor_id='".$proc_sa_doctor_id."' OR doctor_id='0') AND ((proc = '".addslashes($patient_sec_proc_alias)."' AND proc!='') OR (acronym = '".addslashes($patient_sec_proc_alias)."' AND acronym!=''))  ORDER BY doctor_id DESC LIMIT 0,1";
			$imedicSecProcedureidRes 		= imw_query($imedicSecProcedureidQry) or die($imedicSecProcedureidQry.imw_error());
			$imedicSecProcedureidNumRow 	= imw_num_rows($imedicSecProcedureidRes);
		}
		if($imedicSecProcedureidNumRow>0) {
			$imedicSecProcedureidRow 	= imw_fetch_array($imedicSecProcedureidRes);
			$imedic_sec_procedureid 	= $imedicSecProcedureidRow['id'];
		}
	}
	
	//TERTIARY PROCEDURE
	if($patient_ter_proc) {
		$imedicTerProcedureidQry 		= "SELECT id,proc_time FROM `slot_procedures` WHERE  active_status = 'yes' AND (doctor_id='".$proc_sa_doctor_id."' OR doctor_id='0') AND ((proc = '".addslashes($patient_ter_proc)."' AND proc!='') OR (acronym = '".addslashes($patient_ter_proc)."' AND acronym!=''))  ORDER BY doctor_id DESC LIMIT 0,1";
		$imedicTerProcedureidRes 		= imw_query($imedicTerProcedureidQry) or die($imedicTerProcedureidQry.imw_error());
		$imedicTerProcedureidNumRow 	= imw_num_rows($imedicTerProcedureidRes);
		if($imedicTerProcedureidNumRow<=0) {
			$imedicTerProcedureidQry 		= "SELECT id,proc_time FROM `slot_procedures` WHERE active_status = 'yes' AND (doctor_id='".$proc_sa_doctor_id."' OR doctor_id='0') AND ((proc = '".addslashes($patient_ter_proc_alias)."' AND proc!='') OR (acronym = '".addslashes($patient_ter_proc_alias)."' AND acronym!=''))  ORDER BY doctor_id DESC LIMIT 0,1";
			$imedicTerProcedureidRes 		= imw_query($imedicTerProcedureidQry) or die($imedicTerProcedureidQry.imw_error());
			$imedicTerProcedureidNumRow 	= imw_num_rows($imedicTerProcedureidRes);
		}
		if($imedicTerProcedureidNumRow>0) {
			$imedicTerProcedureidRow 	= imw_fetch_array($imedicTerProcedureidRes);
			$imedic_ter_procedureid 	= $imedicTerProcedureidRow['id'];
		}
	}
	
	//END GET PROCEDURE-ID	
	
	//START CODE TO AUTO ALLOCATE SURGERY TIME (IF NOT EXIST)
	$autoAllocSTime=false;
	if($surgery_time != 0 || $surgery_time!='00:00:00') {
		//DO NOTHING
	}else {
		
		if($sa_doctor_id) {
			
			$scTmpId = getiAscOfficeTimings($patient_dos_temp,$imwFacilityIdNew,$sa_doctor_id, '');
			if(!$scTmpId) { $scTmpId='0';}
			$provSchTmpQry 					= "SELECT morning_start_time,morning_end_time,fldLunchStTm,fldLunchEdTm 
												FROM schedule_templates 
												WHERE id IN(".$scTmpId.")
												ORDER BY id DESC
											  ";
			//$incompleteRec .='hlo '.$provSchTmpQry;
			$provSchTmpRes 					= imw_query($provSchTmpQry) or die($provSchTmpQry.imw_error());			
			$provSchTmpResNumRow 			= imw_num_rows($provSchTmpRes);
			if($provSchTmpResNumRow>0) {
				$provSchTmpResRow 			= imw_fetch_array($provSchTmpRes);
				$schTmp_morning_start_time 	= $provSchTmpResRow['morning_start_time'];
				$schTmp_morning_end_time 	= $provSchTmpResRow['morning_end_time'];
				$schTmp_fldLunchStTm 		= $provSchTmpResRow['fldLunchStTm'];
				$schTmp_fldLunchEdTm 		= $provSchTmpResRow['fldLunchEdTm'];
				
				$i_schTmp 					= $schTmp_morning_start_time;
				while($i_schTmp<=$schTmp_morning_end_time){
					
					$i_schTmpExplode 		= explode(':',$i_schTmp);
					$i_schTmpHr 			= $i_schTmpExplode[0];
					$i_schTmpMin 			= $i_schTmpExplode[1];
					$i_schTmpSec 			= $i_schTmpExplode[2];
					$i_schEndTimeTmp		= "";
					
					$andSchApptTmQry 	= " AND sa_doctor_id = '".$sa_doctor_id."' AND sa_app_starttime='".$i_schTmp."' ";
					if($imedicTimeInMinute) {
						$i_schEndTimeTmp 	= date("H:i:s",mktime($i_schTmpExplode[0],$i_schTmpExplode[1]+$imedicTimeInMinute,$i_schTmpExplode[2],0,0,0));
						//$andSchApptTmQry 	= " AND sa_doctor_id = '".$sa_doctor_id."' AND ((sa_app_starttime='".$i_schTmp."') OR (sa_app_endtime!='".$i_schTmp."' AND (( '".$i_schEndTimeTmp."' BETWEEN sa_app_starttime AND sa_app_endtime) OR  '".$i_schTmp."' BETWEEN sa_app_starttime AND sa_app_endtime))) ";
						$andSchApptTmQry 	= " AND sa_doctor_id = '".$sa_doctor_id."' AND ((sa_app_starttime='".$i_schTmp."') OR (sa_app_starttime!='".$i_schEndTimeTmp."' AND ( '".$i_schEndTimeTmp."' BETWEEN sa_app_starttime AND sa_app_endtime)) OR (sa_app_endtime!='".$i_schTmp."' AND ('".$i_schTmp."' BETWEEN sa_app_starttime AND sa_app_endtime))) ";
					}
					if($i_schTmp<$schTmp_fldLunchStTm || $i_schTmp>$schTmp_fldLunchEdTm) {
						$chkSurgeryTmQry 	= "SELECT id FROM schedule_appointments WHERE sa_app_start_date='".$patient_dos_temp."' AND sa_patient_app_status_id NOT IN(18,201) ".$andSchApptTmQry;
						$chkSurgeryTmRes 	= imw_query($chkSurgeryTmQry) or die($chkSurgeryTmQry.imw_error());			
						//echo 'num='.@imw_num_rows($chkSurgeryTmRes);
						if(@imw_num_rows($chkSurgeryTmRes)<=0) {
							$surgery_time	= $i_schTmp;
							$autoAllocSTime = true;
							break;
						}
					}
					
					$i_schTmp 				= date('H:i:s',mktime($i_schTmpExplode[0],$i_schTmpExplode[1]+$constantImwSlotMinute,$i_schTmpExplode[2],0,0,0));
				}
			}	
		}
	}
	//END CODE TO AUTO ALLOCATE SURGERY TIME (IF NOT EXIST)
	
	//START GET DURATION AND END TIME
	if($imedicDurationNumRow>0) {
		if($surgery_time != 0 || $surgery_time!='00:00:00') {
			$surgery_timeExplode= explode(":",$surgery_time);
			$imedic_endtime 	= date("H:i:s",mktime($surgery_timeExplode[0],$surgery_timeExplode[1]+$imedicTimeInMinute,$surgery_timeExplode[2],0,0,0));
		}
	}
	//END GET DURATION AND END TIME
	
	//START GET PATIENT-SITE
	$siteOsOdOu			='';//INITIALIZE VARIABLE
	$patient_site_imw	='';
	if($patient_site) {
		$patient_site_imw=$patient_site;
		if(trim($patient_site)=='both') {
			$patient_site_imw='bilateral';
		}
		$patient_site_imw = ucwords(strtolower($patient_site_imw));
		if($patient_site=='left') {
			$siteOsOdOu='OS';
		}else if($patient_site=='right') {
			$siteOsOdOu='OD';
		}else if($patient_site=='both') {
			$siteOsOdOu='OU';
		}else if($patient_site=='left upper lid') {
			$siteOsOdOu='LUL';
		}else if($patient_site=='left lower lid') {
			$siteOsOdOu='LLL';
		}else if($patient_site=='right upper lid') {
			$siteOsOdOu='RUL';
		}else if($patient_site=='right lower lid') {
			$siteOsOdOu='RLL';
		}else if($patient_site=='bilateral upper lid') {
			$siteOsOdOu='BUL';
		}else if($patient_site=='bilateral lower lid') {
			$siteOsOdOu='BLL';
		}
	}
	//END GET PATIENT-SITE
	
	//START ATTACH SITE WITH COMMENT ALSO 
	/*
	if($siteOsOdOu) {
		$comment = trim($comment.' '.$siteOsOdOu);
	}*/
	//END ATTACH SITE WITH COMMENT ALSO 
	
	//START GET PATIENT NAME FOR IMW-APPOINTMENT
	$patientMiddleNameFirstLetter='';
	if($patient_middle_name) {  $patientMiddleNameFirstLetter = ' '.substr($patient_middle_name,0,1);}
	$imedic_sch_patient_name = trim($patient_last_name.', '.$patient_first_name.$patientMiddleNameFirstLetter);
	//END GET PATIENT NAME FOR IMW-APPOINTMENT
	
	//START CODE TO GET TEMPLATE-ID FOR iASC
	$schTemplateId			= '0';
	if($sa_doctor_id) {	
		list($y, $m, $d) 	= explode("-", $patient_dos_temp);
		$week 				= ceil($d/7);
		$intTimeStamp 		= mktime(0, 0, 0, $m, $d, $y);
		$weekDay 			= date("N", $intTimeStamp);
		$strQryCheck 		= "SELECT st.id 
								FROM schedule_templates st 
								LEFT JOIN provider_schedule_tmp pst ON st.id = pst.sch_tmp_id 
								WHERE pst.provider = '".$sa_doctor_id."'  
								AND '".$patient_dos_temp."' >= pst.today_date  
								AND pst.del_status = '0' 
								AND pst.week$week = '".$weekDay."'  
								AND pst.facility = '".$imwFacilityIdNew."' LIMIT 1";
		$resQryCheck 		= imw_query($strQryCheck) or $msg_info[] = imw_error();
		$checkNumRow 		= imw_num_rows($resQryCheck);
		
		if($checkNumRow>0) {
			$arrQryCheck 	= imw_fetch_array($resQryCheck);
			$schTemplateId 	= $arrQryCheck["id"];	
		}
	
		//START CODE TO CHECK IF SURGEON IS AVAILABLE ON SPECIFIED DATE OF SURGERY
		$boolBookPt = '';
		$surgeonAvail = month_scheduleprovider_Highlight($patient_dos_temp,'',$sa_doctor_id,$imwFacilityIdNew,'iosync');
		if($surgeonAvail=='yes' || constant("STOP_CHECK_IMW_SCHEDULE")=="YES") {
			$boolBookPt = 'yes';
		}
		//END CODE TO CHECK IF SURGEON IS AVAILABLE ON SPECIFIED DATE OF SURGERY
	
	
	}
	//END CODE TO GET TEMPLATE-ID FOR iASC
	include __DIR__.'/connect_imwemr.php';

	unset($arrayImwSchApptRecord);
	$arrayImwSchApptRecord['sa_aid'] 					= '';//OPTIONAL
	$arrayImwSchApptRecord['sa_doctor_id']				= $sa_doctor_id;//REQUIRED
	$arrayImwSchApptRecord['sa_test_id'] 				= '';//OPTIONAL
	$arrayImwSchApptRecord['sa_patient_id'] 			= $sa_patient_id;//REQUIRED
	$arrayImwSchApptRecord['sa_patient_name'] 			= addslashes($imedic_sch_patient_name);// FIRSTNAME MIDDLENAME (1CHAR) LASTNAME
	$arrayImwSchApptRecord['sa_patient_app_title'] 		= addslashes($patient_title);
	$arrayImwSchApptRecord['sa_catid'] 					= '';//OPTIONAL BLANK
	$arrayImwSchApptRecord['procedureid'] 				= $imedic_procedureid;//REQUIRED
	$arrayImwSchApptRecord['sec_procedureid'] 			= $imedic_sec_procedureid;//REQUIRED
	$arrayImwSchApptRecord['tertiary_procedureid'] 		= $imedic_ter_procedureid;//REQUIRED
	$arrayImwSchApptRecord['case_type_id'] 				= $imedicInsCaseId;//OPTIONAL BLANK
	$arrayImwSchApptRecord['checked'] 					= '';//OPTIONAL BLANK
	$arrayImwSchApptRecord['sa_madeby'] 				= 'iolink';//LOGIN OPERATOR NAME
	$arrayImwSchApptRecord['procedure_site'] 			= $patient_site_imw;//OPTIONAL
	$arrayImwSchApptRecord['sch_template_id'] 			= $schTemplateId;//REQUIRED FOR PRINTING IN iASC
	//$arrayImwSchApptRecord['iolinkPatientWtId']		= $patient_in_waiting_id;
	//$arrayImwSchApptRecord['iolinkPatientId']			= $patient_id;
	$arrayImwSchApptRecord['iolink_iosync_waiting_id']	= $patient_in_waiting_id;
	
	
	$chkSqlAppointmentsQry="SELECT * FROM schedule_appointments 
							WHERE iolink_iosync_waiting_id = '".$patient_in_waiting_id."'
							AND iolink_iosync_waiting_id != ''
							";//AND sa_app_start_date='".$patient_dos_temp."'
	$chkSqlAppointmentsRes 		= imw_query($chkSqlAppointmentsQry) or die($chkSqlAppointmentsQry.imw_error());		
	$chkSqlAppointmentsNumRow 	= imw_num_rows($chkSqlAppointmentsRes);
	
	if($chkSqlAppointmentsNumRow<=0 && constant("EXTERNAL_SCH_SEARCH")=="YES") {
		$chkSqlAppointmentsQry="SELECT * FROM schedule_appointments 
								WHERE athenaID = '".$idoc_sch_athena_id."'
								AND athenaID != ''
								AND athenaID != '0'
								";//AND sa_app_start_date='".$patient_dos_temp."'
		$chkSqlAppointmentsRes 		= imw_query($chkSqlAppointmentsQry) or die($chkSqlAppointmentsQry.imw_error());		
		$chkSqlAppointmentsNumRow 	= imw_num_rows($chkSqlAppointmentsRes);
	}
	
	$imedic_appt_id='';
	if($sa_doctor_id && $imedic_procedureid && ($surgery_time != 0 || $surgery_time!='00:00:00') && $boolBookPt=='yes'){ 
		
		//START CODE TO UPDATE SURGERY TIME FOR BOOKING SHEET IN IOLINK(IF SURGERY TIME IS AUTO-ALLOCATED)
		if($autoAllocSTime==true) {
			include("common/conDb.php");
			unset($arrayPtInWaiting);
			$arrayPtInWaiting['surgery_time']=$surgery_time;
			$objManageData->updateRecords($arrayPtInWaiting, 'patient_in_waiting_tbl','patient_in_waiting_id',$patient_in_waiting_id);
			include('connect_imwemr.php');
		}
		//END CODE TO UPDATE SURGERY TIME FOR BOOKING SHEET IN IOLINK(IF SURGERY TIME IS AUTO-ALLOCATED)
		
		//START ADD/UPDATE SCHEDULE-APPOINTMENT
		$iolink_sa_is_modify='0';
		if($chkSqlAppointmentsNumRow>0) {
			$iolink_sa_is_modify='';
			$chkSqlAppointmentsRow 	= imw_fetch_array($chkSqlAppointmentsRes);
			$imedic_appt_id = $chkSqlAppointmentsRow['id'];
			$imedic_appt_start_time = $chkSqlAppointmentsRow['sa_app_starttime'];
			if(isset($chkSqlAppointmentsRow['iolink_sa_is_modify'])){
				$iolink_sa_is_modify = $chkSqlAppointmentsRow['iolink_sa_is_modify'];
			}
		}
		/*
		if(($comment && !$sa_comments) || $iolink_sa_is_modify=='0') {
			$arrayImwSchApptRecord['sa_comments'] 				= addslashes($comment);
		}else if($comment && $sa_comments && ($comment != $sa_comments)) {
			$incompleteComment.=$patient_last_name.', '.$patient_first_name.'@@';
		}*/
			//if($iAscSyncroCount=='0') {//ADD COMMENT ONLY ONCE
				$arrayImwSchApptRecord['sa_comments'] 			= addslashes($comment);
			//}
		//if($iolink_sa_is_modify=='0') { //IF APPT. NOT MODIFIED IN imwemr, only then update these fields otherwise only add these fields.
			$arrayImwSchApptRecord['sa_patient_app_status_id'] 	= '';//OPTIONAL INSERT CASE AND NOT IN CASE OF UPDATE
			$arrayImwSchApptRecord['status_date'] 				= date('Y-m-d');//CURRENT DATE INSERT CASE AND NOT IN CASE OF UPDATE
			$arrayImwSchApptRecord['sa_app_time'] 				= date('Y-m-d H:i:s');//CURRENT DATETIME INSERT CASE AND NOT IN CASE OF UPDATE
			$arrayImwSchApptRecord['status_update_operator_id'] = '';//OPTIONAL INSERT CASE AND NOT IN CASE OF UPDATE
			
			$arrayImwSchApptRecord['sa_app_room'] 				= '';//OPTIONAL
			
			
			$arrayImwSchApptRecord['sa_app_starttime'] 			= $surgery_time;//REQUIRED
			if(trim($imedic_endtime) && $imedic_endtime !='00:00:00' && ($prev_procedureid != $imedic_procedureid || strtotime($surgery_time)!=strtotime($imedic_appt_start_time))) {
				$arrayImwSchApptRecord['sa_app_endtime'] 		= $imedic_endtime;//REQUIRED
				$arrayImwSchApptRecord['sa_app_duration'] 		= $imedic_duration;//REQUIRED procedure times in seconds
			}
			
			$arrayImwSchApptRecord['sa_facility_id'] 			= $imwFacilityIdNew;//REQUIRED
			$arrayImwSchApptRecord['sa_app_all_day'] 			= '';//OPTIONAL BLANK
			$arrayImwSchApptRecord['sa_app_start_date'] 		= $patient_dos_temp;// DOS FROM imwSync.php
			$arrayImwSchApptRecord['sa_app_end_date'] 			= $patient_dos_temp;// DOS FROM imwSync.php
			$arrayImwSchApptRecord['sa_app_recurr'] 			= '';//OPTIONAL BLANK
			$arrayImwSchApptRecord['sa_app_recurr_type'] 		= '';//OPTIONAL BLANK
			$arrayImwSchApptRecord['sa_app_recurr_freq'] 		= '';//OPTIONAL BLANK
			$arrayImwSchApptRecord['pick_up_time'] 				= addslashes($pickup_time);//OPTIONAL
			$arrayImwSchApptRecord['arrival_time'] 				= addslashes($arrival_time);//OPTIONAL
			
			if(constant("EXTERNAL_SCH_SEARCH")=="YES") {
				$arrayImwSchApptRecord['athenaID'] 				= $idoc_sch_athena_id;//OPTIONAL
			}
			//$arrayImwSchApptRecord['procedure_site'] 			= $patient_site_imw;//OPTIONAL
		//}
		if($chkSqlAppointmentsNumRow>0) {	
			$objManageData->updateRecords($arrayImwSchApptRecord, 'schedule_appointments','id',$imedic_appt_id);
		}else{
			$objManageData->addRecords($arrayImwSchApptRecord, 'schedule_appointments');
		}
		//END ADD/UPDATE SCHEDULE-APPOINTMENT	
		
		//START CODE TO UPDATE patient_in_waiting TABLE AS IT IS SYCRONIZED WITH IASC
		include("common/conDb.php");
		$iolink_iAscStatusDateTime = date('Y-m-d H:i:s');
		$iAscSyncroCountPlusOne = $iAscSyncroCount+1;
		$iolinkUpdatePatientInWaitingTblQry = "UPDATE patient_in_waiting_tbl SET 
												iAscSyncroStatus='Syncronized',
												iAscSyncroStatusDateTime='".$iolink_iAscStatusDateTime."',
												iAscReSyncroStatus='',
												iAscSyncroCount='".$iAscSyncroCountPlusOne."',
												reSyncroVia=''
												WHERE patient_in_waiting_id='".$patient_in_waiting_id."'";
		$iolinkUpdatePatientInWaitingTblRes = imw_query($iolinkUpdatePatientInWaitingTblQry) or die(imw_error()); 
		
		
		imw_close($link);
		include('connect_imwemr.php');
		//END CODE TO UPDATE patient_in_waiting TABLE AS IT IS SYCRONIZED WITH IASC
	}else {
		//START CODE TO DISPLAY INCOMPLETE RECORD OF PATIENT
		$incompleteRec.='~~';
		$incompleteRec.=$patient_last_name.', '.$patient_first_name.' - Check ';
		if(!$sa_doctor_id) {
			$incompleteRec.='Surgeon';
		}else if($sa_doctor_id && $boolBookPt!='yes') {
			$incompleteRec.='Surgeon Availability in iASC';
		}
		if(!$imedic_procedureid) {
			if($sa_doctor_id && $boolBookPt=='yes') { 
				$incompleteRec.='Procedure'; 
			}else if($surgery_time == 0 || $surgery_time=='00:00:00') {
				$incompleteRec.=', Procedure';
			}else {
				$incompleteRec.=' and Procedure';
			}			
		}
		if($surgery_time != 0 || $surgery_time!='00:00:00') {
			//DO NOTHING
		}else {
			if($sa_doctor_id && $imedic_procedureid && $boolBookPt=='yes') { $incompleteRec.='Surgery Time'; 
			}else {$incompleteRec.=' and Surgery Time';
			}
		}
		
		//END CODE TO DISPLAY INCOMPLETE RECORD OF PATIENT
	}
	imw_close($link_imwemr); //CLOSE imwemr connection
  //SURGERYCENTER CONNECTION	
?>