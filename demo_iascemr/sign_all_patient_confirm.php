<?php


		//GET VALUE FROM STUB TABLE
		$stubTableDetails = $objManageData->getRowRecord('stub_tbl', 'stub_id', $stub_id);
		$patient_first_name = $stubTableDetails->patient_first_name;
		$patient_middle_name = $stubTableDetails->patient_middle_name;
		$patient_last_name = $stubTableDetails->patient_last_name;
		$patient_sex = $stubTableDetails->patient_sex;
		$patient_site = $stubTableDetails->site;
		$surgeon_fname = trim($stubTableDetails->surgeon_fname);
		$surgeon_mname = trim($stubTableDetails->surgeon_mname);
		$surgeon_lname = trim($stubTableDetails->surgeon_lname);
		
		if($surgeon_mname) { $surgeon_mname = ' '.$surgeon_mname; }
		$surgeon_name = $surgeon_fname.$surgeon_mname.' '.$surgeon_lname;
		
		$surgery_time = $stubTableDetails->surgery_time;
		$pickup_time =  $stubTableDetails->pickup_time;
		$arrival_time =  $stubTableDetails->arrival_time;
		$patient_address1 = $stubTableDetails->patient_street1;
		$patient_address2 = $stubTableDetails->patient_street2;
		$patient_home_phone = $stubTableDetails->patient_home_phone;
		$patient_work_phone = $stubTableDetails->patient_work_phone;
		$patient_city = $stubTableDetails->patient_city;
		$patient_state = $stubTableDetails->patient_state;
		$patient_zip = $stubTableDetails->patient_zip;
		$imwPatientId = $stubTableDetails->imwPatientId;
		$patient_language = trim(stripslashes($stubTableDetails->patient_language));
		$patient_race = trim(stripslashes($stubTableDetails->patient_race));
		$patient_ethnicity = trim(stripslashes($stubTableDetails->patient_ethnicity));
		$patient_dob_temp = $stubTableDetails->patient_dob;
		$patient_prim_proc = trim(stripslashes($stubTableDetails->patient_primary_procedure));
		$patient_sec_proc = trim(stripslashes($stubTableDetails->patient_secondary_procedure));
		$patient_ter_proc = trim(stripslashes($stubTableDetails->patient_tertiary_procedure));
		
		$patient_anes_fname = $stubTableDetails->anesthesiologist_fname;
		$patient_anes_mname = $stubTableDetails->anesthesiologist_mname;
		$patient_anes_lname = $stubTableDetails->anesthesiologist_lname;
	
		if($patient_anes_mname) {
			$patient_anes_mname = ' '.$patient_anes_mname;
		}
		$patient_anes_name = $patient_anes_fname.$patient_anes_mname.' '.$patient_anes_lname;
		
		$patient_nurse_fname = $stubTableDetails->confirming_nurse_fname;
		$patient_nurse_mname = $stubTableDetails->confirming_nurse_mname;
		$patient_nurse_lname = $stubTableDetails->confirming_nurse_lname;
	
		if($patient_nurse_mname) {
			$patient_nurse_mname = ' '.$patient_nurse_mname;
		}
		$conf_nurse = $patient_nurse_fname.$patient_nurse_mname.' '.$patient_nurse_lname;
		
		$patient_confirmation_id = $stubTableDetails->patient_confirmation_id;
		$patient_id_stub = $stubTableDetails->patient_id_stub;
		
		$patient_dos_temp = $stubTableDetails->dos;
		
		$assist_by_trans = $stubTableDetails->assisted_by_translator;
	
	//START TO REDIRECT SCHEDULED PATIENT TO CHART NOTES
	
		// INSERT PATIENT DATA TABLE FROM STUB TABLE
			$arrayScheduledPatientRecord['patient_fname'] = addslashes($patient_first_name);
			$arrayScheduledPatientRecord['patient_mname'] = addslashes($patient_middle_name);
			$arrayScheduledPatientRecord['patient_lname'] = addslashes($patient_last_name);
			$arrayScheduledPatientRecord['street1'] = addslashes($patient_address1);
			$arrayScheduledPatientRecord['street2'] = addslashes($patient_address2);
			$arrayScheduledPatientRecord['city'] = addslashes($patient_city);
			$arrayScheduledPatientRecord['state'] = $patient_state;
			$arrayScheduledPatientRecord['zip'] = $patient_zip;
			$arrayScheduledPatientRecord['date_of_birth'] = $patient_dob_temp;
			$arrayScheduledPatientRecord['sex'] = $patient_sex;
			$arrayScheduledPatientRecord['homePhone'] = $patient_home_phone;
			$arrayScheduledPatientRecord['workPhone'] = $patient_work_phone;
			$arrayScheduledPatientRecord['language'] = $patient_language;
			$arrayScheduledPatientRecord['race'] = $patient_race;
			$arrayScheduledPatientRecord['ethnicity'] = $patient_ethnicity;
			
		$patient_language = $stubTableDetails->patient_language;
		$patient_race = $stubTableDetails->patient_race;
		$patient_ethnicity = $stubTableDetails->patient_ethnicity;
			
			$arrayScheduledPatientRecord['imwPatientId'] = $imwPatientId;
			
			
		//START CHECK IF PATIENT EXISTS THEN ALLOCATE PREVIOUS PATIENT ID ELSE INSERT NEW ENTRY OF PATIENT WITH NEW PATIENT ID
			
			if($patient_id_stub) {
				$scheduledPatientMatchStr = "SELECT patient_id FROM patient_data_tbl 
										WHERE patient_id = '".$patient_id_stub."'
										";
			}else {
				$scheduledPatientMatchStr = "SELECT patient_id FROM patient_data_tbl 
										WHERE imwPatientId = '".$imwPatientId."' and imwPatientId!=''";

				/*
				$scheduledPatientMatchStr = "SELECT patient_id FROM patient_data_tbl 
										WHERE patient_fname = '".addslashes($patient_first_name)."'
										AND patient_lname = '".addslashes($patient_last_name)."'
										AND zip 		  = '".addslashes($patient_zip)."'
										AND date_of_birth = '".$patient_dob_temp."'";
				*/
			}
			$scheduledPatientMatchQry = imw_query($scheduledPatientMatchStr);
			$scheduledPatientMatchRows = imw_num_rows($scheduledPatientMatchQry);
			if($scheduledPatientMatchRows<=0){
				$scheduledPatientMatchStr = "SELECT patient_id FROM patient_data_tbl 
										WHERE 	patient_fname = '".addslashes($patient_first_name)."'
										AND 	patient_lname = '".addslashes($patient_last_name)."'
										AND 	zip 		  = '".addslashes($patient_zip)."'
										AND 	date_of_birth = '".$patient_dob_temp."'";
				$scheduledPatientMatchQry 	= imw_query($scheduledPatientMatchStr);
				$scheduledPatientMatchRows 	= imw_num_rows($scheduledPatientMatchQry);
			}
			if($scheduledPatientMatchRows>0){
				$scheduledPatientDataRow 	= imw_fetch_array($scheduledPatientMatchQry);
				$insertPatientDataId 		= $scheduledPatientDataRow["patient_id"];
				$objManageData->updateRecords($arrayScheduledPatientRecord, 'patient_data_tbl', 'patient_id', $scheduledPatientDataRow["patient_id"]);
				
			}else {
				$insertPatientDataId = $objManageData->addRecords($arrayScheduledPatientRecord, 'patient_data_tbl');
			}
			
			//START CODE TO UPDATE PATIENT-ID IN STUB-TABLE
			if($insertPatientDataId) {
					unset($arrayStubRecord);
					$arrayStubRecord['patient_id_stub']=$insertPatientDataId;
					$objManageData->updateRecords($arrayStubRecord, 'stub_tbl', 'stub_id', $stub_id);
			}
			//END CODE TO UPDATE PATIENT-ID IN STUB-TABLE
			
		//END CHEK IF PATIENT EXISTS THEN ALLOCATE PREVIOUS PATIENT ID ELSE INSERT NEW ENTRY OF PATIENT WITH NEW PATIENT ID	
	
	
	//START INSERT/UPDATE IN CONFIRMATION TABLE
		
		//GET primary procedure id
			if($patient_prim_proc) {
				if(strpos($patient_prim_proc, 'Right Eye') !== false){
					$patient_prim_procExplode  = explode('Right Eye',$patient_prim_proc);
					$patient_prim_proc = trim(trim($patient_prim_procExplode[0]).' '.trim($patient_prim_procExplode[1]));
					$patient_site = "right";
				}
				if(strpos($patient_prim_proc, 'Left Eye') !== false){
					$patient_prim_procExplode  = explode('Left Eye',$patient_prim_proc);
					$patient_prim_proc = trim(trim($patient_prim_procExplode[0]).' '.trim($patient_prim_procExplode[1]));
					$patient_site = "left";
				}
				if(strpos($patient_prim_proc, 'Both Eye') !== false){
					$patient_prim_procExplode  = explode('Both Eye',$patient_prim_proc);
					$patient_prim_proc = trim(trim($patient_prim_procExplode[0]).' '.trim($patient_prim_procExplode[1]));
					$patient_site = "both";
				}
				$primaryprocedure = imw_query("select P.`procedureId`,P.`name`,P.`catId`, PC.isMisc, PC.isInj from procedures P Left Join procedurescategory PC On P.catId=PC.proceduresCategoryId where P.del_status!='yes' and ((P.name='".addslashes($patient_prim_proc)."' AND P.name!='') OR (P.procedureAlias = '".addslashes($patient_prim_proc)."' AND P.procedureAlias!=''))");
				if(imw_num_rows($primaryprocedure)>0) {
					$primary_proc_data=imw_fetch_array($primaryprocedure);
					$PrimaryProcedure_id = $primary_proc_data['procedureId'];
					$patient_prim_proc = $primary_proc_data['name'];
					$primaryProcedureCatId = $primary_proc_data['catId'];
					$primaryProcedureCatIsMisc	=	'';
					if($primary_proc_data['isInj'])				$primaryProcedureCatIsMisc	=	'injection';
					elseif($primary_proc_data['isMisc'])	$primaryProcedureCatIsMisc	=	'misc';
					
				}
			}
				
		//END GET primary procedure id
		
		//GET secondary and tertiary procedure id	
			if($patient_sec_proc) {
				$secondaryprocedure=imw_query("select `procedureId`,`name` from procedures where del_status!='yes' and ((name='".addslashes($patient_sec_proc)."' AND name!='') OR (procedureAlias = '".addslashes($patient_sec_proc)."' AND procedureAlias!=''))");
				if(imw_num_rows($secondaryprocedure)>0) {
					$secondary_proc_data=imw_fetch_array($secondaryprocedure);
					$SecondaryProcedure_id = $secondary_proc_data['procedureId'];
					$patient_sec_proc = $secondary_proc_data['name'];
				}
			}	
			if($patient_ter_proc) {
				$tertiaryprocedure=imw_query("select `procedureId`,`name` from procedures where del_status!='yes' and ((name='".addslashes($patient_ter_proc)."' AND name!='') OR (procedureAlias = '".addslashes($patient_ter_proc)."' AND procedureAlias!=''))");
				if(imw_num_rows($tertiaryprocedure)>0) {
					$tertiary_proc_data=imw_fetch_array($tertiaryprocedure);
					$TertiaryProcedure_id = $tertiary_proc_data['procedureId'];
					$patient_ter_proc = $tertiary_proc_data['name'];
				}
			}	
		//END GET secondary and tertiary id			
	
		//GET surgeon id	
			//$getSurgeonIdQry=imw_query("select * from `users` where fname='".addslashes($surgeon_fname)."' AND mname='".addslashes($surgeon_mname)."' AND lname='".addslashes($surgeon_lname)."'");
			$getSurgeonIdQry=imw_query("select usersId from `users` where fname='".addslashes($surgeon_fname)."' AND lname='".addslashes($surgeon_lname)."' AND deleteStatus != 'Yes'");
			if(imw_num_rows($getSurgeonIdQry)>0) {
				$getSurgeonIdRow=imw_fetch_array($getSurgeonIdQry);
				$surgeon_id = $getSurgeonIdRow['usersId'];
			}	
		//END GET surgeon id			
		
		//GET anesthesiologist id	
			//$getAnesthesiologistIdQry=imw_query("select * from `users` where fname='".addslashes($patient_anes_fname)."' AND mname='".addslashes($patient_anes_mname)."' AND lname='".addslashes($patient_anes_lname)."'");
			$getAnesthesiologistIdQry=imw_query("select usersId from `users` where fname='".addslashes($patient_anes_fname)."' AND lname='".addslashes($patient_anes_lname)."' AND deleteStatus != 'Yes'");
			if(imw_num_rows($getAnesthesiologistIdQry)>0) {
				$getAnesthesiologistIdRow=imw_fetch_array($getAnesthesiologistIdQry);
				$anes_id = $getAnesthesiologistIdRow['usersId'];
			}	
		//END GET anesthesiologist id			
	
		//GET nurse id	
			$getNurseIdQry=imw_query("select usersId from `users` where fname='".addslashes($patient_nurse_fname)."' AND mname='".addslashes($patient_nurse_mname)."' AND lname='".addslashes($patient_nurse_lname)."' AND deleteStatus != 'Yes'");
			if(imw_num_rows($getNurseIdQry)>0) {
				$getNurseIdRow=imw_fetch_array($getNurseIdQry);
				$confNurse_id = $getNurseIdRow['usersId'];
			}	
		//END GET nurse id			

		//APPLYING NUMBERS TO PATIENT SITE
			$patient_site_no='';
			if($patient_site == "left") {
				$patient_site_no = 1;
			}else if($patient_site == "right") {
				$patient_site_no = 2;
			}else if($patient_site == "both") {
				$patient_site_no = 3;
			}else if($patient_site == "left upper lid") {
				$patient_site_no = 4;
			}else if($patient_site == "left lower lid") {
				$patient_site_no = 5;
			}else if($patient_site == "right upper lid") {
				$patient_site_no = 6;
			}else if($patient_site == "right lower lid") {
				$patient_site_no = 7;
			}else if($patient_site == "bilateral upper lid") {
				$patient_site_no = 8;
			}else if($patient_site == "bilateral lower lid") {
				$patient_site_no = 9;
			}
		//END APPLYING NUMBERS TO PATIENT SITE
			unset($arrayRecord);
			$arrayRecord['patientId'] = $insertPatientDataId;
			$arrayRecord['dos'] = $patient_dos_temp;
			$arrayRecord['surgery_time'] = $surgery_time;
			$arrayRecord['pickup_time'] = $pickup_time;
			$arrayRecord['arrival_time'] = $arrival_time;
			//$arrayRecord['ascId'] = $ascId;
			$arrayRecord['assist_by_translator'] = $assist_by_trans;
			$arrayRecord['patient_primary_procedure'] = addslashes($patient_prim_proc);
			$arrayRecord['patient_primary_procedure_id'] = $PrimaryProcedure_id;
			$arrayRecord['prim_proc_is_misc'] = $primaryProcedureCatIsMisc;
			$arrayRecord['patient_secondary_procedure'] =  addslashes($patient_sec_proc);
			$arrayRecord['patient_secondary_procedure_id'] = $SecondaryProcedure_id;
			$arrayRecord['patient_tertiary_procedure'] =  addslashes($patient_ter_proc);
			$arrayRecord['patient_tertiary_procedure_id'] = $TertiaryProcedure_id;
			$arrayRecord['site'] = $patient_site_no;
			$arrayRecord['zip'] = $patient_zip;
			$arrayRecord['surgeonId'] = $surgeon_id;
			$arrayRecord['surgeon_name'] = addslashes($surgeon_name);
			$arrayRecord['anesthesiologist_name'] = addslashes($patient_anes_name);
			$arrayRecord['anesthesiologist_id'] = $anes_id;			
			$arrayRecord['confirm_nurse'] = addslashes($conf_nurse);
			$arrayRecord['nurseId'] = $confNurse_id;
			$arrayRecord['patientStatus'] = 'Scheduled';
			$arrayRecord['dateConfirmation'] = date("Y-m-d H:i:s");
			$arrayRecord['imwPatientId'] = $imwPatientId;
			
				//CHECK IF PATIENT ALREADY CONFIRMED (IF NOT THEN INSERT NEW ENTRY TO CONFIRM PATIENT)
					$chkPatientAlreadyConfirmedQry = "SELECT pc.patientConfirmationId FROM patientconfirmation pc 
													  INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.stub_id = '".$stub_id."')
													  WHERE pc.patientId='$insertPatientDataId'";
					$chkPatientAlreadyConfirmedRes = imw_query($chkPatientAlreadyConfirmedQry) or die(imw_error());
					$chkPatientAlreadyConfirmedNumRow = imw_num_rows($chkPatientAlreadyConfirmedRes);
					if($chkPatientAlreadyConfirmedNumRow>0) {
						
						$chkPatientAlreadyConfirmedRow = imw_fetch_array($chkPatientAlreadyConfirmedRes);
						$insertConfirmationId = $chkPatientAlreadyConfirmedRow['patientConfirmationId'];
						//$objManageData->updateRecords($arrayRecord, 'patientconfirmation', 'patientConfirmationId', $insertConfirmationId);
					
					}else { //(IF NOT ALREADY CONFIRMED THEN INSERT NEW ENTRY TO CONFIRM PATIENT)
				
						$insertConfirmationId = $objManageData->addRecords($arrayRecord, 'patientconfirmation');
					}
				//CHECK IF PATIENT ALREADY CONFIRMED
		//END INSERT/UPDATE IN CONFIRMATION TABLE
	
		//INSERT CONFIRMATION_ID AND PATIENT_ID IN  left_navigation_forms TABLE 	
			$chk_left_menu_ins_query=imw_query("select `id` from `left_navigation_forms` where confirmationId='".$insertConfirmationId."' AND patient_id='".$insertPatientDataId."'");
			if(imw_num_rows($chk_left_menu_ins_query)>0) {
				//DO NOTHING
			}else {
				$left_menu_ins_query = "insert into left_navigation_forms set confirmationId = '$insertConfirmationId', patient_id = '$insertPatientDataId'";
				$left_menu_ins_res = 	imw_query($left_menu_ins_query) or die(imw_error());		
			}
		//END INSERT CONFIRMATION_ID AND PATIENT_ID IN left_navigation_forms TABLE
		
		
		// UPDATE IN STUB TABLE 
			$update_stub_status_qry = "update `stub_tbl` set 
										patient_confirmation_id = '$insertConfirmationId'
										WHERE stub_id = '".$stub_id."'";
			$update_stub_status_res = 	imw_query($update_stub_status_qry) or die(imw_error());	
		
		// UPDATE SCAN DOCUMENTS, SCAN UPLOAD 
			//echo $update_scan_upload_qry = "UPDATE scan_upload_tbl SET 	confirmation_id = '".$insertConfirmationId."' WHERE (patient_id = '".$insertPatientDataId."' and confirmation_id ='0')";
			
			$updateScanUploadQry = "update `scan_upload_tbl` set confirmation_id = '".$insertConfirmationId."' 
										 WHERE patient_id='".$insertPatientDataId."' AND confirmation_id='0' AND dosOfScan = '$patient_dos_temp' AND stub_id = '".$stub_id."' AND stub_id != '0'";
			$updateScanUploadRes = imw_query($updateScanUploadQry) or die(imw_error());										
			
			
		//END UPDATE SCAN DOCUMENTS, SCAN UPLOAD
		
		
		//INSERT NEW ENTRY OF SCAN DOCUMENT WITH PATIENT ID
			$chk_insert_scan_document_qry1 = "select document_id from scan_documents where document_name = 'Pt. Info' AND patient_id = '$insertPatientDataId' AND confirmation_id = '0' AND dosOfScan = '$patient_dos_temp' AND stub_id = '".$stub_id."' AND stub_id != '0'";
			$chk_insert_scan_document_res1 = imw_query($chk_insert_scan_document_qry1) or die(imw_error());
			$chk_insert_scan_document_numrow1 = imw_num_rows($chk_insert_scan_document_res1);
			if($chk_insert_scan_document_numrow1>0) {
			
				$update_scan_document_qry1 = "update `scan_documents` set 
											confirmation_id = '$insertConfirmationId' 
											WHERE patient_id = '$insertPatientDataId'
											AND document_name = 'Pt. Info'
											AND confirmation_id = '0'
											AND dosOfScan = '$patient_dos_temp'
											AND stub_id = '".$stub_id."' AND stub_id != '0'
											";
				$update_scan_document_res1 = imw_query($update_scan_document_qry1) or die(imw_error());										
			
			}else {	
				$chk_update_scan_document_qry1 = "select document_id from scan_documents where document_name = 'Pt. Info' AND patient_id = '".$insertPatientDataId."' AND confirmation_id = '".$insertConfirmationId."'  AND stub_id = '".$stub_id."' AND stub_id != '0'";
				$chk_update_scan_document_res1 = imw_query($chk_update_scan_document_qry1) or die(imw_error());
				$chk_update_scan_document_numrow1 = imw_num_rows($chk_update_scan_document_res1);
				if($chk_update_scan_document_numrow1<=0) {
				
					$insert_scan_document_qry1 = "insert into `scan_documents` set 
												document_name = 'Pt. Info',
												patient_id = '$insertPatientDataId',
												dosOfScan = '$patient_dos_temp',
												confirmation_id = '$insertConfirmationId', 
												stub_id = '".$stub_id."'
												";
					$insert_scan_document_res1 = imw_query($insert_scan_document_qry1) or die(imw_error());
				
					//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
					$insert_scan_log_qry1 = "insert into `scan_log_tbl` set 
												document_id = '".imw_insert_id()."',
												document_name = 'Pt. Info',
												patient_id = '$insertPatientDataId',
												confirmation_id = '$insertConfirmationId' ,
												document_date_time = '".date('Y-m-d H:i:s')."',
												document_file_name = 'patient_without_confirm.php',
												document_encounter = 'pt_info_1',
												stub_id = '".$stub_id."'
												";
					$insert_scan_log_res1 = imw_query($insert_scan_log_qry1) or die(imw_error());
					//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
				}
			}
			$chk_insert_scan_document_qry2 = "select document_id from scan_documents where document_name = 'Clinical' AND patient_id = '$insertPatientDataId' AND confirmation_id = '0' AND dosOfScan = '$patient_dos_temp'";
			$chk_insert_scan_document_res2 = imw_query($chk_insert_scan_document_qry2) or die(imw_error());
			$chk_insert_scan_document_numrow2 = imw_num_rows($chk_insert_scan_document_res2);
			if($chk_insert_scan_document_numrow2>0) {
										
				$update_scan_document_qry2 = "update `scan_documents` set 
											confirmation_id = '$insertConfirmationId' 
											WHERE patient_id = '$insertPatientDataId'
											AND document_name = 'Clinical'
											AND confirmation_id = '0'
											AND dosOfScan = '$patient_dos_temp'
											AND stub_id = '".$stub_id."' AND stub_id != '0'
											";
				$update_scan_document_res2 = imw_query($update_scan_document_qry2) or die(imw_error());										
				
			}else {	
				
				$chk_update_scan_document_qry2 = "select document_id from scan_documents where document_name = 'Clinical' AND patient_id = '".$insertPatientDataId."' AND confirmation_id = '".$insertConfirmationId."'";
				$chk_update_scan_document_res2 = imw_query($chk_update_scan_document_qry2) or die(imw_error());
				$chk_update_scan_document_numrow2 = imw_num_rows($chk_update_scan_document_res2);
				if($chk_update_scan_document_numrow2<=0) {
				
					$insert_scan_document_qry2 = "insert into `scan_documents` set 
												document_name = 'Clinical',
												patient_id = '$insertPatientDataId',
												dosOfScan = '$patient_dos_temp',
												confirmation_id = '$insertConfirmationId',
												stub_id = '".$stub_id."' 
												";
												
					$insert_scan_document_res2 = imw_query($insert_scan_document_qry2) or die(imw_error());
					//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
					$insert_scan_log_qry2 = "insert into `scan_log_tbl` set 
												document_id = '".imw_insert_id()."',
												document_name = 'Clinical',
												patient_id = '$insertPatientDataId',
												confirmation_id = '$insertConfirmationId',
												document_date_time = '".date('Y-m-d H:i:s')."',
												document_file_name = 'patient_without_confirm.php',
												document_encounter = 'clinical_1',
												stub_id = '".$stub_id."'
												";
					$insert_scan_log_res2 = imw_query($insert_scan_log_qry2) or die(imw_error());
					//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
				}
			}	
			$chk_insert_scan_document_qry3 = "select document_id from scan_documents where document_name = 'IOL' AND patient_id = '$insertPatientDataId' AND confirmation_id = '0' AND dosOfScan = '$patient_dos_temp'";
			$chk_insert_scan_document_res3 = imw_query($chk_insert_scan_document_qry3) or die(imw_error());
			$chk_insert_scan_document_numrow3 = imw_num_rows($chk_insert_scan_document_res3);
			if($chk_insert_scan_document_numrow3>0) {
										
				$update_scan_document_qry3 = "update `scan_documents` set 
											confirmation_id = '$insertConfirmationId' 
											WHERE patient_id = '$insertPatientDataId'
											AND document_name = 'IOL'
											AND confirmation_id = '0'
											AND dosOfScan = '$patient_dos_temp'
											AND stub_id = '".$stub_id."' AND stub_id != '0'
											";
				$update_scan_document_res3 = imw_query($update_scan_document_qry3) or die(imw_error());										
				
			}else {	
				
				$chk_update_scan_document_qry3 = "select document_id from scan_documents where document_name = 'IOL' AND patient_id = '".$insertPatientDataId."' AND confirmation_id = '".$insertConfirmationId."'";
				$chk_update_scan_document_res3 = imw_query($chk_update_scan_document_qry3) or die(imw_error());
				$chk_update_scan_document_numrow3 = imw_num_rows($chk_update_scan_document_res3);
				if($chk_update_scan_document_numrow3<=0) {
				
					$insert_scan_document_qry3 = "insert into `scan_documents` set 
												document_name = 'IOL',
												patient_id = '$insertPatientDataId',
												dosOfScan = '$patient_dos_temp',
												confirmation_id = '$insertConfirmationId',
												stub_id = '".$stub_id."' 
												";
												
					$insert_scan_document_res3 = imw_query($insert_scan_document_qry3) or die(imw_error());
					//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
					$insert_scan_log_qry3 = "insert into `scan_log_tbl` set 
												document_id = '".imw_insert_id()."',
												document_name = 'IOL',
												patient_id = '$insertPatientDataId',
												confirmation_id = '$insertConfirmationId',
												document_date_time = '".date('Y-m-d H:i:s')."',
												document_file_name = 'patient_without_confirm.php',
												document_encounter = 'iol_1',
												stub_id = '".$stub_id."'
												";
					$insert_scan_log_res3 = imw_query($insert_scan_log_qry3) or die(imw_error());
					//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
				}
			}	
			
			$scnFolderArr = array('H&P','EKG','Health Questionnaire','Ocular Hx','Consent');
			foreach($scnFolderArr as $scnFldNme) {
				$update_scan_document_qry3 = "update `scan_documents` set 
											confirmation_id = '".$insertConfirmationId."' 
											WHERE patient_id = '".$insertPatientDataId."'
											AND document_name = '".$scnFldNme."'
											AND confirmation_id = '0'
											AND dosOfScan = '".$patient_dos_temp."'
											AND stub_id = '".$stub_id."' AND stub_id != '0'
											";
				$update_scan_document_res3 = imw_query($update_scan_document_qry3) or die(imw_error());										
			}
		//END INSERT NEW ENTRY OF SCAN DOCUMENT WITH PATIENT ID
	
	
		// UPDATE EPOST-IT
			$update_epost_qry = "update `eposted` set 
										patient_conf_id = '$insertConfirmationId' 
										WHERE patient_id = '$insertPatientDataId'
										AND patient_conf_id = '0'
										AND stub_id = '".$stub_id."' AND stub_id != '0'
										";
			$update_epost_res = imw_query($update_epost_qry) or die(imw_error());										
		//END UPDATE EPOST-IT
	//END TO REDIRECT SCHEDULED PATIENT TO CHART NOTES
	
	$pConfId				=	$insertConfirmationId ;
	$blankInsertQry	= "insert into laser_procedure_patient_table set confirmation_id = '".$pConfId."', form_status= ''"; 
	$blankInsertRes	= imw_query($blankInsertQry) or die(imw_error());
	
	$blankInsertQry = "insert into preopphysicianorders set patient_confirmation_id = '".$pConfId."', form_status= ''"; 
	$blankInsertRes = imw_query($blankInsertQry) or die(imw_error());
	
	$blankInsertQry = "insert into postopphysicianorders set patient_confirmation_id = '".$pConfId."', form_status= ''"; 
	$blankInsertRes = imw_query($blankInsertQry) or die(imw_error());
	
	$blankInsertQry = "insert into preopnursingrecord set confirmation_id = '".$pConfId."', form_status= ''"; 
	$blankInsertRes = imw_query($blankInsertQry) or die(imw_error());
	
	


?>