<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	
	include("common/conDb.php");  //SURGERYCENTER CONNECTION	
	//$imwFacilityId = $constantImwFacilityId;
	$surgeryCenterFacID = $_SESSION['facility'];
	$imwFacilityId = $_SESSION['iasc_facility_id'];
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	$injMiscArr = $objManageData->getInjMiscProc();
	
	imw_close($link); //CLOSE SURGERYCENTER CONNECTION
	
	include('connect_imwemr.php'); // imwemr connection
	
	
	if(count($imwSchStatusIdArr)<=0) {
		$imwSchStatusIdArr = array();
		$imwSchStatusQry = "SELECT id, status_name, status FROM ".$imw_db_name.".schedule_status ORDER BY id";
		$imwSchStatusRes = imw_query($imwSchStatusQry) or die($imwSchStatusQry.' Error found @ Line No. '.(__LINE__).': '.imw_error());				
		if(imw_num_rows($imwSchStatusRes)>0) {
			while($imwSchStatusRow 	= imw_fetch_assoc($imwSchStatusRes)) {
				$imwSchStatusId 	= $imwSchStatusRow["id"];
				$imwSchStatusName 	= trim(ucwords(strtolower(stripslashes($imwSchStatusRow["status_name"]))));
				$imwSchActiveStatus	= stripslashes($imwSchStatusRow["status"]);
				$imwSchStatusIdArr[$imwSchStatusName] = $imwSchStatusId;	
			}
		}
	}
	
	
	$patient_dos = $selected_date;
	
	$appt_dos = $patient_dos;
	if($appt_dos=="") {
		$appt_dos = date("Y-m-d");
	}
	$today_imw_date = date("Y-m-d");
	$constantImwFacilityIdQry = "";
	if($_SESSION['iasc_facility_id']) {
		$constantImwFacilityIdQry = " AND sa_facility_id IN(".$_SESSION['iasc_facility_id'].") ";	
	}
	$sqlAppointmentsQry="SELECT * FROM schedule_appointments 
						WHERE sa_app_start_date = '".$appt_dos."'
						AND sa_patient_app_status_id NOT IN(201)
						".$constantImwFacilityIdQry."
						ORDER BY sa_app_start_date, sa_app_time";
						
	
	$sqlAppointmentsRes = imw_query($sqlAppointmentsQry) or die(imw_error());
	$sqlAppointmentsNumRow = imw_num_rows($sqlAppointmentsRes);
	if($sqlAppointmentsNumRow>0) {
		while($sqlAppointmentsRow = imw_fetch_array($sqlAppointmentsRes)) {
			include('connect_imwemr.php'); // imwemr connection
			$appt_id 	       			= $sqlAppointmentsRow['id'];
			$iasc_facility_id  			= $sqlAppointmentsRow['sa_facility_id']; //facility id
			$appt_date_of_surgery	   	= $sqlAppointmentsRow['sa_app_start_date'];
			$sa_doctor_id	   			= ((int)$sqlAppointmentsRow['facility_type_provider']!=0)?$sqlAppointmentsRow['facility_type_provider']:$sqlAppointmentsRow['sa_doctor_id']; //Surgeon ID
			$sa_app_starttime  			= $sqlAppointmentsRow['sa_app_starttime']; //Surgery Time
			$sa_patient_id	   			= $sqlAppointmentsRow['sa_patient_id']; //PatientId
			$procedureid	   			= $sqlAppointmentsRow['procedureid']; //ProcedureId
			$comment					= stripslashes($sqlAppointmentsRow['sa_comments']); //Comment
			$patient_status_id			= $sqlAppointmentsRow['sa_patient_app_status_id']; //PATIENT STATUS ID
			$iolink_iosync_waiting_id	= $sqlAppointmentsRow['iolink_iosync_waiting_id']; //patient_in_waiting_id
			//SET PATIENT STATUS
			$patient_status='Scheduled';
			if($patient_status_id=='18') {
				$patient_status='Canceled';
			}elseif($patient_status_id=='3') {
				$patient_status='No Show';
			}elseif($patient_status_id==$imwSchStatusIdArr['Aborted Surgery']) {
				$patient_status='Aborted Surgery';
			}
			//END SET PATIENT STATUS
			//added by amit on 13-06-09 for synch with imwemr
			$pickup_time = "";
			if(isset($sqlAppointmentsRow['pick_up_time'])){
				$pickup_time = addslashes($sqlAppointmentsRow['pick_up_time']);
			}
			$arrival_time = "";
			if(isset($sqlAppointmentsRow['arrival_time'])){
				$arrival_time = addslashes($sqlAppointmentsRow['arrival_time']);
			}
			if(isset($sqlAppointmentsRow['sec_procedureid'])){
				$sec_procedureid = addslashes($sqlAppointmentsRow['sec_procedureid']);
			}
			if(isset($sqlAppointmentsRow['tertiary_procedureid'])){
				$ter_procedureid = addslashes($sqlAppointmentsRow['tertiary_procedureid']);
			}				
			
			$surgeonNameQry = "select * from users where id='".$sa_doctor_id."'";
			$surgeonNameRes = imw_query($surgeonNameQry) or die($surgeonNameQry.imw_error());
			$surgeonNameNumRow = imw_num_rows($surgeonNameRes);
			
			
			//START INITIALIZE VARIABLES OF SURGEON
			$surgeonFirstName    = '';
			$surgeonMiddleName   = '';
			$surgeonLastName     = '';
			
			//END INITIALIZE VARIABLES OF SURGEON
			if($surgeonNameNumRow>0) {
				$surgeonNameRow = imw_fetch_array($surgeonNameRes);
				$surgeonFirstName    = $surgeonNameRow['fname']; //Surgeon First Name
				$surgeonMiddleName   = $surgeonNameRow['mname']; //Surgeon Middle Name
				$surgeonLastName     = $surgeonNameRow['lname']; //Surgeon Last Name
				$surgeonNPI     	 = trim($surgeonNameRow['user_npi']); //Surgeon NPI
			}
			$patientNameQry = "select * from patient_data where id='".$sa_patient_id."'";
			$patientNameRes = imw_query($patientNameQry) or die(imw_error());
			$patientNameNumRow = imw_num_rows($patientNameRes);
			
			//START INITIALIZE VARIABLES OF PATIENT PERSONAL INFO
			$imwPatient_first_name    	= 	"";
			$imwPatient_middle_name   	= 	"";
			$imwPatient_last_name     	= 	"";
			$date_of_birth_patient 			= 	"";
			$sex    	   					= 	"";
			$address_line_1					= 	"";
			$address_line_2 				= 	"";
			$city 							= 	"";
			$state    						= 	"";
			$zip   							= 	"";
			$home_phone    					= 	"";
			$day_phone 						= 	"";
			$language 						= 	"";
			$race 							= 	"";
			$ethnicity 						= 	"";
			$religion 						= 	"";
			//END INITIALIZE VARIABLES OF PATIENT PERSONAL INFO
			if($patientNameNumRow>0) {
				$patientNameRow = imw_fetch_array($patientNameRes);
				
				$imwPatient_first_name    	= $patientNameRow['fname']; //Patient First Name
				$imwPatient_middle_name   	= $patientNameRow['mname']; //Patient Middle Name
				$imwPatient_last_name     	= $patientNameRow['lname']; //Patient Last Name
				$date_of_birth_patient 			= $patientNameRow['DOB'];
				$sex    	   					= $patientNameRow['sex'];
				$address_line_1					= $patientNameRow['street'];
				$address_line_2 				= $patientNameRow['street2'];
				$city 							= addslashes($patientNameRow['city']);
				$state    						= addslashes($patientNameRow['state']);
				$zip   							= $patientNameRow['postal_code'];
				$home_phone    					= $patientNameRow['phone_home'];
				$day_phone 						= $patientNameRow['phone_biz'];
				$language 						= $patientNameRow['language'];
				$race 							= $patientNameRow['race'];
				$ethnicity 						= $patientNameRow['ethnicity'];
				$phone_cell		 				= $patientNameRow['phone_cell'];
				$preferr_contact				= $patientNameRow['preferr_contact'];
				
				//START CODE TO REPLACE PREFERRED CONTACT IN HOME PHONE
				if($preferr_contact == '1' && trim($day_phone)) {
					$home_phone = $day_phone;	
				}else if($preferr_contact == '2' && trim($phone_cell)) {
					$home_phone = $phone_cell;	
				}
				//END CODE TO REPLACE PREFERRED CONTACT IN HOME PHONE
				
				//Get religion field value from Misc.
				$qryR = "select id From custom_fields where control_lable = 'religion' "; 
				$sqlR = imw_query($qryR) or die(imw_error());
				if( imw_num_rows($sqlR) > 0 ) {
					$resR = imw_fetch_assoc($sqlR);
					$qryR2 = "select TRIM(patient_control_value) as religion_val  From patient_custom_field where patient_id = ".(int)$sa_patient_id." and admin_control_id = ".(int)$resR['id']." ";
					$sqlR2 = imw_query($qryR2) or die(imw_error());
					if( imw_num_rows($sqlR2) > 0 ) {
						$resR2 = imw_fetch_assoc($sqlR2);
						$religion = $resR2['religion_val'];
					}
				}
			}
			//$procedureNameQry = "select * from slot_procedures where id='".$procedureid."'";
			$procedureNameQry 	= "SELECT prim_proc.proc AS pri_proc,prim_proc.acronym AS pri_acronym, sec_proc.proc AS sec_proc, sec_proc.acronym AS sec_acronym, ter_proc.proc AS ter_proc, ter_proc.acronym AS ter_acronym
									FROM (
									
									SELECT proc,acronym
									FROM slot_procedures
									WHERE id ='".$procedureid."'
									) AS prim_proc, (
									
									SELECT IF(count(id)=1,proc,'') as proc, IF(count(id)=1,acronym,'') as acronym 
									FROM slot_procedures
									WHERE id = '".$sec_procedureid."'
									) AS sec_proc, (
									
									SELECT IF(count(id)=1,proc,'') as proc, IF(count(id)=1,acronym,'') as acronym 
									FROM slot_procedures
									WHERE id = '".$ter_procedureid."'
									) AS ter_proc";		
			$procedureNameRes = imw_query($procedureNameQry) or die(imw_error());
			$procedureNameNumRow = imw_num_rows($procedureNameRes);
			
			$procedureName   = "";
			$site = strtolower(addslashes($sqlAppointmentsRow['procedure_site']));
			if($site=='bilateral') 	{ $site='both';}
			$confSiteNo=$objManageData->getSiteNo($site);
			
			$sec_site = strtolower(addslashes($sqlAppointmentsRow['procedure_sec_site']));
			if($sec_site=='bilateral') 	{ $sec_site='both';}
			$sec_confSiteNo=$objManageData->getSiteNo($sec_site);
			
			$ter_site = strtolower(addslashes($sqlAppointmentsRow['procedure_ter_site']));
			if($ter_site=='bilateral') 	{ $ter_site='both';}
			$ter_confSiteNo=$objManageData->getSiteNo($ter_site);
			
			/*if(isset($sqlAppointmentsRow['procedure_site'])){
				$site = strtolower(addslashes($sqlAppointmentsRow['procedure_site']));
				if($site=='bilateral') 	{ $site='both';}
				
				if($site=='left') 		{ $confSiteNo=1;
				}else if($site=='right'){ $confSiteNo=2;
				}else if($site=='both') { $confSiteNo=3;
				}else if($site=='left upper lid')  { $confSiteNo=4;
				}else if($site=='left lower lid')  { $confSiteNo=5;
				}else if($site=='right upper lid') { $confSiteNo=6;
				}else if($site=='right lower lid') { $confSiteNo=7;
				}else if($site=='bilateral upper lid') { $confSiteNo=8;
				}else if($site=='bilateral lower lid') { $confSiteNo=9;
				}
			}*/

			$procedureBasedSite='';
			$procedureBasedConfSiteNo='';
			if($procedureNameNumRow>0) {
				$procedureNameRow = imw_fetch_array($procedureNameRes);
				
				$procedureName    		= trim($procedureNameRow['pri_proc']); //Procedure Name
				$procedureAcronym 		= trim($procedureNameRow['pri_acronym']); //Procedure Acronymn
				$secProcedureName 		= trim($procedureNameRow['sec_proc']); //Sec Procedure Name
				$secProcedureAcronym 	= trim($procedureNameRow['sec_acronym']); //Sec Procedure Acronymn
				$terProcedureName 		= trim($procedureNameRow['ter_proc']); //Ter Procedure Name
				$terProcedureAcronym 	= trim($procedureNameRow['ter_acronym']); //Ter Procedure Acronymn
				//$site = "";
				
				$procSiteArr = $objManageData->procedureBaseSiteNo($procedureName);
				$procedureBasedSite = $procSiteArr['site'];
				$procedureBasedConfSiteNo = $procSiteArr['site_no'];
				$procedureName = 	$procSiteArr['proc_name'];
				
				$secProcSiteArr = $objManageData->procedureBaseSiteNo($secProcedureName);
				$secProcedureBasedSite = $secProcSiteArr['site'];
				$secProcedureBasedConfSiteNo = $secProcSiteArr['site_no'];
				$secProcedureName = 	$secProcSiteArr['proc_name'];
				
				$terProcSiteArr = $objManageData->procedureBaseSiteNo($terProcedureName);
				$terProcedureBasedSite = $terProcSiteArr['site'];
				$terProcedureBasedConfSiteNo = $terProcSiteArr['site_no'];
				$terProcedureName = 	$terProcSiteArr['proc_name'];
				
			}
			if($site=='') { //IF SITE IS STILL BLANK THEN SET IT ON PROCEDURE BASED	
				$site 		= $procedureBasedSite;
				$confSiteNo = $procedureBasedConfSiteNo;
			} 
			
			if($sec_site=='') { //IF SITE IS STILL BLANK THEN SET IT ON PROCEDURE BASED	
				$sec_site 		= $secProcedureBasedSite;
				$sec_confSiteNo = $secProcedureBasedConfSiteNo;
			} 
			
			if($ter_site=='') { //IF SITE IS STILL BLANK THEN SET IT ON PROCEDURE BASED	
				$ter_site 		= $terProcedureBasedSite;
				$ter_confSiteNo = $terProcedureBasedConfSiteNo;
			} 
			
			imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
			include("common/conDb.php");  //SURGERYCENTER CONNECTION	
			
			//START TO FILL DATA INTO STUB TABLE FROM IMW
			
			//if($patient_dos >= $today_imw_date) {
				if($sex=='Male') {
					$sex = 'm';
				}else if($sex=='Female') {
					$sex = 'f';
				}
				//SET PATIENT NAME ACCORDING TO REQUIREMENT
				$imwPatient_first_name 		= str_replace("'","",$imwPatient_first_name);
				$imwPatient_middle_name 	= str_replace("'","",$imwPatient_middle_name);
				$imwPatient_last_name 		= str_replace("'","",$imwPatient_last_name);
				
				$imwPatient_first_name 		= addslashes($imwPatient_first_name);
				if($imwPatient_middle_name){
					$imwPatient_middle_name = addslashes($imwPatient_middle_name);
				}
				$imwPatient_last_name 		= addslashes($imwPatient_last_name);
				//END SET PATIENT NAME ACCORDING TO REQUIREMENT 
				
				//CHECK IF PATIENT ALREADY EXIST (BASED ON NUMBER OF ROWS)
							//AND patient_middle_name = '$imwPatient_middle_name'
				
				$chk_save_appt_id_qry 	 = "select * from stub_tbl 
											WHERE appt_id 	= '".$appt_id."'
											AND dos = '".$appt_date_of_surgery."'
											";
				/*
				$chk_save_appt_id_qry 	 = "select * from stub_tbl WHERE 
												imwPatientId = '$sa_patient_id'
											 AND dos = '$appt_date_of_surgery'
											";
				
				$chk_save_appt_id_qry = "select * from stub_tbl WHERE 
											patient_first_name = '".$imwPatient_first_name."'
										 AND patient_last_name = '".$imwPatient_last_name."'
										 AND patient_dob = '".$date_of_birth_patient."'
										 AND patient_zip = '".$zip."'
										 AND dos = '".$appt_date_of_surgery."'
									   ";
				*/
				$chk_save_appt_id_res 	 = imw_query($chk_save_appt_id_qry);
				$chk_save_appt_id_NumRow = imw_num_rows($chk_save_appt_id_res);
				//CHECK IF PATIENT ALREADY EXIST (BASED ON NUMBER OF ROWS)
				
				
				//SET SURGEON NAME ACCORDING TO REQUIREMENT
				
				$surgeonFirstName 		= str_replace("'","",$surgeonFirstName);
				$surgeonMiddleName 		= str_replace("'","",$surgeonMiddleName);
				$surgeonLastName 		= str_replace("'","",$surgeonLastName);
									
				$surgeonFirstName 		= addslashes($surgeonFirstName);
				
				$surgeonMiddleNameConfirm='';
				if($surgeonMiddleName){
					$surgeonMiddleName 	= addslashes($surgeonMiddleName);
				}
				$surgeonLastName 		= addslashes($surgeonLastName);
				
				//GET SURGEON ID
				$confirmSurgeonWhrQry = " AND fname	='".$surgeonFirstName."' AND mname	='".$surgeonMiddleName."' AND lname	='".$surgeonLastName."'";		
				if(constant("CHECK_USER_NPI")=="YES") {
					if($surgeonNPI) {
						$confirmSurgeonWhrQry = " AND npi = '".$surgeonNPI."'  AND npi != ''   AND npi != '0' ";		
					}
				}
				$confirmSurgeonIdQry = "SELECT usersId,fname,mname,lname FROM users WHERE fname  !='' AND deleteStatus != 'Yes' AND user_type='Surgeon' ".$confirmSurgeonWhrQry;
				$confirmSurgeonIdRes = imw_query($confirmSurgeonIdQry) or die(imw_error());
				$confirmSurgeonIdNumRow = imw_num_rows($confirmSurgeonIdRes);
				$ConfirmationNxtGnSrgnId='';
				if($confirmSurgeonIdNumRow>0) {
					$confirmSurgeonIdRow = imw_fetch_array($confirmSurgeonIdRes);
					$ConfirmationNxtGnSrgnId= $confirmSurgeonIdRow['usersId'];
					$surgeonFirstName 		= addslashes($confirmSurgeonIdRow['fname']);
					$surgeonMiddleName 		= addslashes($confirmSurgeonIdRow['mname']);
					$surgeonLastName 		= addslashes($confirmSurgeonIdRow['lname']);
				}
				//END GET SURGEON ID
				$confimSurgeonName='';
				if(trim($surgeonMiddleName)){
					$surgeonMiddleNameConfirm = ' '.$surgeonMiddleName;
				}
				$confimSurgeonName = $surgeonFirstName.$surgeonMiddleNameConfirm.' '.$surgeonLastName;
				//END SET SURGEON NAME ACCORDING TO REQUIREMENT
				
				$address_line_1 		= addslashes($address_line_1);
				if($address_line_2) {
					$address_line_2		= addslashes($address_line_2);
				}
				$comment 				= addslashes($comment);
				
				//GET PROCEDURE ID 
				$confirmNxtGnProcedureId='';
				
				$procedureNameConfirmation=$procedureName;
				if($procedureName) {
					if(strpos($procedureName, 'Left Eye') !== false){
						$procedureNameExplode  = explode('Left Eye',$procedureName);
						$procedureNameConfirmation = trim(trim($procedureNameExplode[0]).' '.trim($procedureNameExplode[1]));
						//$site = 'left';
						//$confSiteNo=1;
					}
					if(strpos($procedureName, 'Right Eye') !== false){
						$procedureNameExplode  = explode('Right Eye',$procedureName);
						$procedureNameConfirmation = trim(trim($procedureNameExplode[0]).' '.trim($procedureNameExplode[1]));
						//$site = 'right';
						//$confSiteNo=2;
					}
					if(strpos($procedureName, 'Both Eye') !== false){
						$procedureNameExplode  = explode('Both Eye',$procedureName);
						$procedureNameConfirmation = trim(trim($procedureNameExplode[0]).' '.trim($procedureNameExplode[1]));
						//$site = 'both';
						//$confSiteNo=3;
					}
					
				}
				$secProcedureNameConfirmation=$secProcedureName;
				$terProcedureNameConfirmation=$terProcedureName;
				$secProcedureNameConfirmation = str_ireplace("Left Eye","",$secProcedureNameConfirmation);
				$secProcedureNameConfirmation = str_ireplace("Right Eye","",$secProcedureNameConfirmation);
				$secProcedureNameConfirmation = str_ireplace("Both Eye","",$secProcedureNameConfirmation);
				$secProcedureNameConfirmation = trim($secProcedureNameConfirmation);
				
				$terProcedureNameConfirmation = str_ireplace("Left Eye","",$terProcedureNameConfirmation);
				$terProcedureNameConfirmation = str_ireplace("Right Eye","",$terProcedureNameConfirmation);
				$terProcedureNameConfirmation = str_ireplace("Both Eye","",$terProcedureNameConfirmation);
				$terProcedureNameConfirmation = trim($terProcedureNameConfirmation);

				//$confirmNxtGnProcedureIdQry = "select `procedureId`,`name` from procedures where name='".$procedureNameConfirmation."' OR procedureAlias = '".$procedureNameConfirmation."'";
				
				$confirmNxtGnProcedureIdQry = "SELECT prim_proc.procedureId AS pri_proc_id,prim_proc.procName AS pri_proc_name, sec_proc.procedureId AS sec_proc_id,sec_proc.procName AS sec_proc_name, ter_proc.procedureId AS ter_proc_id,ter_proc.procName AS ter_proc_name 
												FROM (
												SELECT procedureId,name as procName 
												FROM procedures
												WHERE del_status!='yes' AND TRIM(name)!='' AND (TRIM(name) IN('".addslashes($procedureNameConfirmation)."') OR (TRIM(procedureAlias) IN('".addslashes($procedureNameConfirmation)."') AND TRIM(procedureAlias)!=''))
												) AS prim_proc, (
												
												SELECT IF(count(name)=1,procedureId,'') as procedureId, IF(count(name)=1,name,'') as procName 
												FROM procedures
												WHERE del_status!='yes' AND TRIM(name)!='' AND (TRIM(name) IN('".addslashes($secProcedureNameConfirmation)."','".addslashes($secProcedureAcronym)."') OR (TRIM(procedureAlias) IN('".addslashes($secProcedureNameConfirmation)."','".addslashes($secProcedureAcronym)."') AND TRIM(procedureAlias)!=''))
												) AS sec_proc, (
												
												SELECT IF(count(name)=1,procedureId,'') as procedureId, IF(count(name)=1,name,'') as procName 
												FROM procedures
												WHERE del_status!='yes' AND TRIM(name)!='' AND (TRIM(name) IN('".addslashes($terProcedureNameConfirmation)."','".addslashes($terProcedureAcronym)."') OR (TRIM(procedureAlias) IN('".addslashes($terProcedureNameConfirmation)."','".addslashes($terProcedureAcronym)."') AND TRIM(procedureAlias)!=''))
												) AS ter_proc
												";					
				$confirmNxtGnProcedureIdRes = imw_query($confirmNxtGnProcedureIdQry) or die($confirmNxtGnProcedureIdQry.imw_error());
				$confirmNxtGnProcedureIdNumRow = imw_num_rows($confirmNxtGnProcedureIdRes);
				if($confirmNxtGnProcedureIdNumRow<=0) {
					$confirmNxtGnProcedureIdQry = "SELECT prim_proc.procedureId AS pri_proc_id,prim_proc.procName AS pri_proc_name, sec_proc.procedureId AS sec_proc_id,sec_proc.procName AS sec_proc_name, ter_proc.procedureId AS ter_proc_id,ter_proc.procName AS ter_proc_name 
													FROM (
													SELECT procedureId,name as procName
													FROM procedures
													WHERE del_status!='yes' AND TRIM(name)!='' AND (TRIM(name) IN('".addslashes($procedureAcronym)."') OR (TRIM(procedureAlias) IN('".addslashes($procedureAcronym)."') AND TRIM(procedureAlias)!=''))
													) AS prim_proc, (
													
													SELECT IF(count(name)=1,procedureId,'') as procedureId, IF(count(name)=1,name,'') as procName 
													FROM procedures
													WHERE del_status!='yes' AND TRIM(name)!='' AND (TRIM(name) IN('".addslashes($secProcedureNameConfirmation)."','".addslashes($secProcedureAcronym)."') OR (TRIM(procedureAlias) IN('".addslashes($secProcedureNameConfirmation)."','".addslashes($secProcedureAcronym)."') AND TRIM(procedureAlias)!=''))
													) AS sec_proc, (
													
													SELECT IF(count(name)=1,procedureId,'') as procedureId, IF(count(name)=1,name,'') as procName 
													FROM procedures
													WHERE del_status!='yes' AND TRIM(name)!='' AND (TRIM(name) IN('".addslashes($terProcedureNameConfirmation)."','".addslashes($terProcedureAcronym)."') OR (TRIM(procedureAlias) IN('".addslashes($terProcedureNameConfirmation)."','".addslashes($terProcedureAcronym)."') AND TRIM(procedureAlias)!=''))
													) AS ter_proc
													";					
					$confirmNxtGnProcedureIdRes 	= imw_query($confirmNxtGnProcedureIdQry) or die($confirmNxtGnProcedureIdQry.imw_error());
					$confirmNxtGnProcedureIdNumRow 	= imw_num_rows($confirmNxtGnProcedureIdRes);
				}
				$confirmInjMisc = "";
				if($confirmNxtGnProcedureIdNumRow>0) {
					$confirmNxtGnProcedureIdRow		= imw_fetch_array($confirmNxtGnProcedureIdRes);
					$confirmNxtGnProcedureId 		= $confirmNxtGnProcedureIdRow['pri_proc_id'];
					$confirmNxtGnSecProcedureId 	= $confirmNxtGnProcedureIdRow['sec_proc_id'];
					$confirmNxtGnTerProcedureId 	= $confirmNxtGnProcedureIdRow['ter_proc_id'];
					
					$confirmInjMisc					= $injMiscArr[$confirmNxtGnProcedureId];
					$procedureNameConfirmation 		= $confirmNxtGnProcedureIdRow['pri_proc_name'];
					$procedureName 					= $confirmNxtGnProcedureIdRow['pri_proc_name'];
					
					if(trim($confirmNxtGnProcedureIdRow['sec_proc_name'])) {
						$secProcedureNameConfirmation 	= $confirmNxtGnProcedureIdRow['sec_proc_name'];
						$secProcedureName 				= $confirmNxtGnProcedureIdRow['sec_proc_name'];
					}
					
					if(trim($confirmNxtGnProcedureIdRow['ter_proc_name'])) {
						$terProcedureNameConfirmation 	= $confirmNxtGnProcedureIdRow['ter_proc_name'];
						$terProcedureName 				= $confirmNxtGnProcedureIdRow['ter_proc_name'];
					}
						
				}
				
				$boolModify = true;
				if(constant('PROCEDURE_VERIFY')=='YES' && !$confirmNxtGnProcedureId) {
					$boolModify = false;	
				}
				//END GET PROCEDURE ID
				
				//START GET PATIENT-ID FROM STUB-TABLE
				$patient_id_stub='';
				if($chk_save_appt_id_NumRow > 0) {
					$chk_save_appt_id_Row = imw_fetch_array($chk_save_appt_id_res);
					$patient_id_stub=$chk_save_appt_id_Row['patient_id_stub'];
				}
				//END GET PATIENT-ID FROM STUB-TABLE
				
				//START GETTING patient_in_waiting_id FROM IOLINK
				/*
				$bookedPatientInWatingId='';
				$chkPatientIdDataWaiting='';
				
				if($patient_id_stub) {
					$chkPatientDataTblWaitingQry = "SELECT * FROM patient_data_tbl 
												  WHERE patient_id	='".$patient_id_stub."'";
				}else {
					$chkPatientDataTblWaitingQry = "SELECT * FROM patient_data_tbl 
												  WHERE patient_fname	='".$imwPatient_first_name."'
												  AND patient_lname		='".$imwPatient_last_name."'
												  AND date_of_birth		='".$date_of_birth_patient."'
												  AND zip				='".$zip."'
												  AND patient_id	   !=''
											";
				}
				$chkPatientDataTblWaitingRes 	= imw_query($chkPatientDataTblWaitingQry) or die(imw_error());
				$chkPatientDataTblWaitingNumRow = imw_num_rows($chkPatientDataTblWaitingRes);
				if($chkPatientDataTblWaitingNumRow>0) {
					
					$chkPatientDataTblWaitingRow= imw_fetch_array($chkPatientDataTblWaitingRes);
					$chkPatientIdDataWaiting 	= $chkPatientDataTblWaitingRow['patient_id'];
					
					$chkBookedPatientQry = "SELECT patient_in_waiting_id FROM `patient_in_waiting_tbl`
											WHERE patient_id='".$chkPatientIdDataWaiting."'
											  AND patient_id!=''
											  AND patient_status='Scheduled'
											  AND dos='".$appt_date_of_surgery."'
											";
					$chkBookedPatientRes 	= imw_query($chkBookedPatientQry) or die(imw_error());					
					$chkBookedPatientNumRow = imw_num_rows($chkBookedPatientRes);
					if($chkBookedPatientNumRow>0) {
						$chkBookedPatientRow= imw_fetch_array($chkBookedPatientRes);
						$bookedPatientInWatingId = $chkBookedPatientRow['patient_in_waiting_id'];
					}
				}
				*/
				//END GETTING patient_in_waiting_id FROM IOLINK							
				
				
				
				if($chk_save_appt_id_NumRow > 0) {
					
					//START CODE, DO NOT UPDATE RECORD IF ASC-ID IS ALLOCATED TO PATIENT ON SPECIFIC DATE 
					
					$pConfId=$chk_save_appt_id_Row['patient_confirmation_id'];
					$comment_modified_status = $chk_save_appt_id_Row['comment_modified_status'];
					$updtCmntQry = "";
					if($comment_modified_status == '0') {
						$updtCmntQry = " comment 					= '".$comment."', ";	
					}
					$chkAscIdExistQry = "SELECT patientConfirmationId FROM `patientconfirmation`
											WHERE `patientConfirmationId` ='$pConfId'
											AND patientConfirmationId != ''
											AND (`ascId` != '' OR `ascId` != '0')
										";		
					
					$chkAscIdExistRes 	= imw_query($chkAscIdExistQry);
					$chkAscIdExistNumRow= imw_num_rows($chkAscIdExistRes);
					//END CODE, DO NOT UPDATE RECORD IF ASC-ID IS ALLOCATED TO PATIENT ON SPECIFIC DATE 
					
					$save_appt_id_qry='';
					$savePatientDataQry='';
					$savePatientConfirmQry='';
					if($chkAscIdExistNumRow) {
						//DO NOT UPDATE RECORD IF ASC-ID IS ALLOCATED TO PATIENT ON SPECIFIC DATE 
					}else {
						$save_appt_id_qry = "update stub_tbl SET 
												appt_id 					= '$appt_id',
												dos 						= '$appt_date_of_surgery',
												surgeon_fname 				= '$surgeonFirstName',
												surgeon_mname 				= '$surgeonMiddleName',
												surgeon_lname 				= '$surgeonLastName',
												surgery_time 				= '$sa_app_starttime',
												pickup_time 				= '$pickup_time',
												arrival_time 				= '$arrival_time',
												patient_first_name 			= '$imwPatient_first_name',
												patient_middle_name 		= '$imwPatient_middle_name',
												patient_last_name 			= '$imwPatient_last_name',
												patient_dob 				= '$date_of_birth_patient',
												patient_sex 				= '$sex',
												patient_street1 			= '$address_line_1',
												patient_street2 			= '$address_line_2',
												patient_city 				= '$city',
												patient_state 				= '$state',
												patient_zip 				= '$zip',
												patient_home_phone 			= '$home_phone',
												patient_work_phone 			= '$day_phone',
												patient_primary_procedure 	= '".addslashes($procedureName)."',
												patient_secondary_procedure = '".addslashes($secProcedureName)."',
												patient_tertiary_procedure 	= '".addslashes($terProcedureName)."',
												patient_status 				= '$patient_status',
												site 						= '$site',
												stub_secondary_site = '$sec_site',
												stub_tertiary_site 	= '$ter_site',
												".$updtCmntQry."												
												patient_language 			= '".$language."',
												patient_race 				= '".$race."',
												patient_religion 			= '".$religion."',
												patient_ethnicity 			= '".$ethnicity."',
												iolink_patient_in_waiting_id= '".$iolink_iosync_waiting_id."',
												iasc_facility_id			= '".$iasc_facility_id."',
												imwPatientId 			= '$sa_patient_id'
												 WHERE appt_id = '".$appt_id."'
												 AND dos = '".$appt_date_of_surgery."'
												 
												 
												";
												/*
												 WHERE imwPatientId = '$sa_patient_id'
												 AND dos = '$appt_date_of_surgery'
												 
												 WHERE patient_first_name = '".$imwPatient_first_name."'
												 AND patient_last_name = '".$imwPatient_last_name."'
												 AND patient_dob = '".$date_of_birth_patient."'
												 AND patient_zip = '".$zip."'
												 AND dos = '".$appt_date_of_surgery."'
												*/
						$savePatientDataQry = "update patient_data_tbl SET
												patient_fname 		= '$imwPatient_first_name',
												patient_mname 		= '$imwPatient_middle_name',
												patient_lname 		= '$imwPatient_last_name',
												date_of_birth 		= '$date_of_birth_patient',
												sex 				= '$sex',
												street1 			= '$address_line_1',
												street2 			= '$address_line_2',
												city 				= '".$city."',
												state 				= '$state',
												zip 				= '$zip',
												homePhone 			= '$home_phone',
												workPhone 			= '$day_phone',
												language 			= '".$language."',
												race 				= '".$race."',
												religion 			= '".$religion."',
												ethnicity 			= '".$ethnicity."',
												imwPatientId 	= '$sa_patient_id'
												WHERE imwPatientId = '$sa_patient_id'
												  AND imwPatientId != ''
												 
											  ";
												/*
												 WHERE patient_id = '".$chkPatientIdDataWaiting."'
												 AND patient_id != ''
												*/  
						
						$savePatientConfirmQry = "update patientconfirmation SET 
													dos 							= '".$appt_date_of_surgery."',
													surgeon_name 					= '".$confimSurgeonName."',
													surgeonId 						= '".$ConfirmationNxtGnSrgnId."',
													surgery_time 					= '".$sa_app_starttime."',
													pickup_time  					= '".$pickup_time."',
													arrival_time 					= '".$arrival_time."',
													patient_primary_procedure 		= '".addslashes($procedureNameConfirmation)."',
													patient_primary_procedure_id 	= '".$confirmNxtGnProcedureId."',
													patient_secondary_procedure 	= '".addslashes($secProcedureNameConfirmation)."',
													patient_secondary_procedure_id 	= '".$confirmNxtGnSecProcedureId."',
													patient_tertiary_procedure 		= '".addslashes($terProcedureNameConfirmation)."',
													patient_tertiary_procedure_id 	= '".$confirmNxtGnTerProcedureId."',
													site 							= '".$confSiteNo."',
													secondary_site 		= '".$sec_confSiteNo."',
													tertiary_site 		= '".$ter_confSiteNo."',
													prim_proc_is_misc 		= '".$confirmInjMisc."',
													imwPatientId 			= '".$sa_patient_id."'
													 WHERE patientConfirmationId	= '".$pConfId."'
												";
													 /*
													 WHERE imwPatientId 			= '".$sa_patient_id."'
													 AND imwPatientId 			!= ''
													 AND dos 						= '".$appt_date_of_surgery."'
													 
													 WHERE patientId = '".$chkPatientIdDataWaiting."'
													 AND patientId != ''
													 AND dos = '$appt_date_of_surgery'
													 */
					}
				}else {	
					$save_appt_id_qry = "insert into stub_tbl SET 
											appt_id 					= '$appt_id',
											dos			 				= '$appt_date_of_surgery',
											surgeon_fname 				= '$surgeonFirstName',
											surgeon_mname 				= '$surgeonMiddleName',
											surgeon_lname 				= '$surgeonLastName',
											surgery_time 				= '$sa_app_starttime',
											pickup_time 				= '$pickup_time',
											arrival_time 				= '$arrival_time',
											patient_first_name 			= '$imwPatient_first_name',
											patient_middle_name 		= '$imwPatient_middle_name',
											patient_last_name 			= '$imwPatient_last_name',
											patient_dob 				= '$date_of_birth_patient',
											patient_sex 				= '$sex',
											patient_street1 			= '$address_line_1',
											patient_street2 			= '$address_line_2',
											patient_city 				= '$city',
											patient_state 				= '$state',
											patient_zip 				= '$zip',
											patient_home_phone 			= '$home_phone',
											patient_work_phone 			= '$day_phone',
											patient_primary_procedure 	= '".addslashes($procedureName)."',
											patient_secondary_procedure = '".addslashes($secProcedureName)."',
											patient_tertiary_procedure 	= '".addslashes($terProcedureName)."',
											patient_status 				= '$patient_status',
											site 						= '$site',
											stub_secondary_site = '$sec_site',
											stub_tertiary_site 	= '$ter_site',
											comment 					= '$comment',
											patient_language 			= '".$language."',
											patient_race 				= '".$race."',
											patient_religion 			= '".$religion."',
											patient_ethnicity 			= '".$ethnicity."',
											iolink_patient_in_waiting_id= '".$iolink_iosync_waiting_id."',
											iasc_facility_id			= '".$iasc_facility_id."',
											imwPatientId 			= '$sa_patient_id'
											";
				}
				
				if($save_appt_id_qry && $boolModify==true) {	
					if ($imwPatient_first_name != '' && $date_of_birth_patient != '') {
						$save_appt_id_res = imw_query($save_appt_id_qry) or die(imw_error());
						
						if($savePatientDataQry) {
							$savePatientDataRes = imw_query($savePatientDataQry) or die(imw_error());
						}
						if($savePatientConfirmQry) {
							$savePatientConfirmRes = imw_query($savePatientConfirmQry) or die(imw_error());
						}
						
					}
				}	
			//}	
			//END TO FILL DATA INTO STUB TABLE FROM IMW
			imw_close($link); //CLOSE IMWEMR CONNECTION
		}
	}	

	include("common/conDb.php");  //SURGERYCENTER CONNECTION	
	//SET CURRENT DATE STATUS = YES
	if($patient_dos==date("Y-m-d") && $boolModify==true) {
		$chk_CurDateTableQry = "select * from cur_date_tbl_status where sqlServerDate ='$patient_dos'";
		$chk_CurDateTableRes = imw_query($chk_CurDateTableQry);
		$chk_CurDateTableNumRow = imw_num_rows($chk_CurDateTableRes);
		if($chk_CurDateTableNumRow>0) {
			$insCurDateTableQry = "update cur_date_tbl_status set sqlDateStatus = 'yes' where sqlServerDate ='$patient_dos'";
		}else {
			$insCurDateTableQry = "insert into cur_date_tbl_status set sqlServerDate ='$patient_dos', sqlDateStatus = 'yes'";
		}
		$insCurDateTableRes = imw_query($insCurDateTableQry) or die(imw_error());
	}
	//END SET CURRENT DATE STATUS = YES 
	
?>