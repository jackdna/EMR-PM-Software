<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
	session_start();
	require_once('conDb.php'); 
	include_once("../admin/classObjectFunction.php");
	$objManageData = new manageData;

	
	$request				=	isset($_POST['request'])	?	$_POST['request']	:	'view'	;	// Request for -> edit || delete || add || view (default)
	//$showAllApptStatus	=	isset($_POST['AS'])			?	$_POST['AS']		:	''		;	// Application Status
	$patient_id				=	isset($_POST['PID'])		?	$_POST['PID']		:	''		;	// Patient  ID
	$pConfirmId				=	isset($_POST['PCI'])		?	$_POST['PCI']		:	''		;	// Patient Confirmation ID
	$imwPatientId			=	isset($_POST['NID'])		?	$_POST['NID']		:	''		;	// IMW Patient ID
	$ptStubId				=	isset($_POST['SID'])		?	$_POST['SID']		:	''		;	// Patient Stub ID
//$txt_patient_search_id	=	isset($_POST['PS'])			?	$_POST['PS']		:	''		;	// Patient Search ID
	$spanPtAlertId			=	isset($_POST['EID'])		?	$_POST['EID']		:	''		;	// Epost Box ID
	$alert_type				=	isset($_POST['EF'])			?	$_POST['EF']		:	''		;	// EPost For -> alert || epost
	
	$ePostID				=	isset($_POST['ePostID'])	?	$_POST['ePostID']	:	0		;	// EPost ID used to edit and delete epost, 
																									// *must be greater than zero(0)
	$epostText				=	isset($_POST['epostText'])	?	$_POST['epostText']	:	''		;	// EPost Content 
	$chart_notes			=	isset($_POST['chart_notes'])?	$_POST['chart_notes']:	''		;	// EPost Chart Notes 
	$created_operator_id	=	$_SESSION['loginUserId'];
	$modified_operator_id	=	$_SESSION['loginUserId'];
	
	$JSONReturn				=	array();
	
	$dtdate					=	date('Y-m-d');
	$T_time					=	date('H:i:s');
	
	//START GET LOGGED-IN USER NAME
	$usrEpostQry = "SELECT concat(lname,', ',fname) AS usrNme FROM users WHERE usersId = '".$_SESSION['loginUserId']."'";
	$usrEpostRes = imw_query($usrEpostQry) or die ('Error In \"'.$usrEpostQry.'\"<br>' . imw_error());
	if(imw_num_rows($usrEpostRes)>0) {
		$usrEpostRow = imw_fetch_array($usrEpostRes);
		$usrEpostName = $usrEpostRow["usrNme"];	
	}
	//END GET LOGGED-IN USER NAME
	
	if($request	==	'edit')
	{
		$JSONReturn['response']	=	0	;
		
		if(!empty($chart_notes ) && !empty($epostText) && $ePostID > 0  ) 
		{
			
			$arr					=	explode("-",$chart_notes);
			$table_name				=	$arr[0];
			$consent_template_id	=	$arr[1];
			$consentAutoIncId		=	$arr[2];
			
			$query	=	"UPDATE eposted SET epost_data = '".$epostText."', dtdate = '".$dtdate."', T_time = '".$T_time."', table_name = '".$table_name."', consent_template_id = '".$consent_template_id."', consentAutoIncId  = '".$consentAutoIncId."', modified_operator_id  = '".$modified_operator_id."' WHERE epost_id = ".(int) $ePostID ." ";
			//$JSONReturn['rt']	=	$query;
			$sql	=	imw_query($query) or die ('Error In \"'.$query.'\"<br>' . imw_error());
			
			$JSONReturn['response']	=	($sql)	?	1	:	0	;
			
			$eDateTime				=	$dtdate.' '.$T_time;
			
			//$eDateTime			=	date('m/d/Y h:i A', strtotime($eDateTime));
			$eDateTime				=	$objManageData->getFullDtTmFormat($eDateTime);
			
			$JSONReturn['ePost']	=	array('epost_id' => $ePostID, 'eDateTime' => $eDateTime, 'content' => $epostText, 'consent_template_id' => implode("-",$arr), 'created_operator_name' => '', 'modified_operator_name' => $usrEpostName )	; 
			
			$JSONReturn['template']	=	$table_name == 'alert'	?	ucfirst($table_name)	:	'Epost'	;
			
			
		}
		
	}
	elseif($request == 'delete')
	{
		$query	=	"DELETE FROM eposted WHERE epost_id = ".(int) $ePostID." ";
		$sql	=	imw_query($query) or die ('Error In \"'.$query.'\"<br>' . imw_error());
		
		$JSONReturn['response']	=	($sql)	?	1	:	0	;
		
	}
	
	elseif($request == 'add')
	{
		$JSONReturn['response']	=	0	;
		
		if(is_array($chart_notes) && count($chart_notes) > 0 && !empty($epostText) && $ePostID == 0  ) 
		{
			$totalChartNotes = count($chart_notes);
			$counter = 0;
			foreach($chart_notes as $chartNote)
			{
				$arr					=	explode("-",$chartNote);
				$table_name				=	$arr[0];
				$consent_template_id	=	$arr[1];
				$consentAutoIncId		=	$arr[2];
			
				$query		=	"INSERT INTO eposted SET epost_data = '".$epostText."', dtdate = '".$dtdate."', T_time = '".$T_time."', table_name = '".$table_name."', patient_conf_id = '".$pConfirmId."', patient_id = '".$patient_id."', consent_template_id = '".$consent_template_id."', consentAutoIncId  = '".$consentAutoIncId."', stub_id = '".$ptStubId."', created_operator_id  = '".$created_operator_id."', created_date_time = '".$dtdate.' '.$T_time."' ";
			
				$sql		=	imw_query($query) or die ('Error In \"'.$query.'\"<br>' . imw_error());
				
				$last_id	=	imw_insert_id();
				
				if($sql) $counter++;
				
				$eDateTime				=	$dtdate.' '.$T_time;
				
				//$eDateTime			=	date('m/d/Y h:i A', strtotime($eDateTime));
				$eDateTime				=	$objManageData->getFullDtTmFormat($eDateTime);
				
				$template				=	$table_name == 'alert'	?	ucfirst($table_name)	:	'Epost'	;
				
				$JSONReturn['ePost'][]	=	array('epost_id' => $last_id, 'eDateTime' => $eDateTime, 'content' => $epostText, 'consent_template_id' => implode("-",$arr), 'created_operator_name' => $usrEpostName, 'modified_operator_name' => '', 'template' => $template )	;
				
				$JSONReturn['template']	=	'';
			}
			$JSONReturn['response']	=	1 ;
			$JSONReturn['rMessage']	=	$counter . ' submitted out of '.$totalChartNotes . ' requested';
		}
		
	}
	
	elseif ($request == 'disableSxAlert' )
	{
		
		$DateTime				=	$dtdate.' '.$T_time;
		
		$query		=	"Update iolink_patient_alert_tbl SET alert_disabled ='yes', alert_disabled_date_time= '".$DateTime."', disabled_section = 'scheduler_alert_popup' WHERE patient_id = '".$patient_id."'   ";
		
		$sql			=	imw_query($query) or die ('Error In \"'.$query.'\"<br>' . imw_error());
		
		$JSONReturn['response']	=	($sql)	?	1	:	0	;
	}
	
	else
	{
		
		$andStubIdQry = "";
		
	
		if(!$pConfirmId) {
			
			$pConfirmId		=	'0';
			$andStubIdQry	=	" AND stub_id = '".$ptStubId."' AND stub_id != '0' ";
	
		}
	
		
		//GET MULTIPLE CONSENT FORMS
		$consentFormTemplateSelectQry = "select * from `consent_forms_template` order by consent_id";
		$consentFormTemplateSelectRes = imw_query($consentFormTemplateSelectQry) or die(imw_error()); 
		$consentFormTemplateSelectNumRow = imw_num_rows($consentFormTemplateSelectRes);
		$consentFormAliasArr = array();
		$consentFormTemplateSelectConsentId =array();
		
		if($consentFormTemplateSelectNumRow>0) {
			
			while($consentFormTemplateSelectRow = imw_fetch_array($consentFormTemplateSelectRes)) {
			
				$consentFormTemplateSelectConsentAlias = $consentFormTemplateSelectRow['consent_alias'];	
				$consentFormTemplateDeleteStatus = $consentFormTemplateSelectRow['consent_delete_status'];
			
				//DO NOT DISPLAY CONSENT FORM FOR NEW SURGERY IN LEFT SLIDER
				$consentFormTemplateDeleteStatus;
				if($consentFormTemplateDeleteStatus=='true') {
					$consentFormTemplateSelectConsentAlias='';
				}
				//DO NOT DISPLAY CONSENT FORM FOR NEW SURGERY IN LEFT SLIDER
			
				//$consentFormTemplateSelectConsentId[] = $consentFormTemplateSelectRow['consent_id'];
				$consentFormSelectQry = "select * from `consent_multiple_form` where  confirmation_id = '".$pConfirmId."' AND consent_template_id='".$consentFormTemplateSelectRow['consent_id']."' ";
				$consentFormSelectRes = imw_query($consentFormSelectQry) or die(imw_error()); 
				$consentFormSelectNumRow = imw_num_rows($consentFormSelectRes);
				$consentFormSelectRow = imw_fetch_array($consentFormSelectRes);
				$consentFormSelectConsentAlias = $consentFormSelectRow['surgery_consent_alias'];	
				if(!$consentFormSelectConsentAlias) {
					if($consentFormTemplateSelectConsentAlias!='') {
						$consentFormSelectConsentAlias=$consentFormTemplateSelectConsentAlias;
					}
				}
				if($consentFormSelectConsentAlias!='') {
					$consentFormAliasArr[] = $consentFormSelectConsentAlias;
					$consentFormTemplateSelectConsentId[] = $consentFormTemplateSelectRow['consent_id'];
				}	
			}
		}
	
	
		//CODE TO CHECK FOR SCHEDULED PATIENT (IF TEMPLATE EXISTS IN ADMIN OR NOT)
		  
			$chkConsentFormTemplateQry = "select * from `consent_forms_template` where consent_delete_status!='true' order by consent_id";
			$chkConsentFormTemplateRes = imw_query($chkConsentFormTemplateQry) or die(imw_error()); 
			$chkConsentFormTemplateNumRow = imw_num_rows($chkConsentFormTemplateRes);
			$chkConsentFormTemplateConsent_id=array();
			if($chkConsentFormTemplateNumRow>0) {
				while($chkConsentFormTemplateRow = imw_fetch_array($chkConsentFormTemplateRes)) {
					$chkConsentFormTemplateConsent_id[] = $chkConsentFormTemplateRow['consent_id'];
				}
			}		
		//END CODE TO CHECK FOR SCHEDULED PATIENT (IF TEMPLATE EXISTS IN ADMIN OR NOT) 

	
	
		//END GET MULTIPLE CONSENT FORMS
	
	
		//START GET PATIENT DETAIL
			$epostPatientNameTblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
			$epostPatientNameTblRes = imw_query($epostPatientNameTblQry) or die(imw_error());
			$epostPatientNameTblNumRow = imw_num_rows($epostPatientNameTblRes);
			
			if($epostPatientNameTblNumRow > 0) {
				$epostPatientNameTblRow = imw_fetch_array($epostPatientNameTblRes);
				$epostPatientFName = $epostPatientNameTblRow['patient_fname'];
				$epostPatientMName = $epostPatientNameTblRow['patient_mname'];
				$epostPatientLName = $epostPatientNameTblRow['patient_lname'];
	
				if($epostPatientMName) {
					$epostPatientMName = ' '.$epostPatientMName;
				}
				$epostPatientName = $epostPatientLName.', '.$epostPatientFName.$epostPatientMName;
			}
			
			$JSONReturn['PatientName']	=	$epostPatientName;
		//END GET PATIENT DETAIL
		

		
		/**
																	
		* Start Getting Record for chart note 	
		*										
		**/
		
		//Get Multiple Consent Forms
		
		$consentFormTemplateSelectQry = "SELECT * FROM `consent_forms_template` ORDER BY consent_id ";
		$consentFormTemplateSelectRes = imw_query($consentFormTemplateSelectQry) or die(imw_error()); 
		$consentFormTemplateSelectNumRow = imw_num_rows($consentFormTemplateSelectRes);
		$consentFormAliasArr = array();
		if($consentFormTemplateSelectNumRow > 0) 
		{
			while($consentFormTemplateSelectRow = imw_fetch_array($consentFormTemplateSelectRes))
			{
				
				$consentFormTemplateDeleteStatus = $consentFormTemplateSelectRow['consent_delete_status'];
				$consentFormTemplateSelectConsentAlias = ($consentFormTemplateDeleteStatus=='true')	?	''	:	$consentFormTemplateSelectRow['consent_alias'] ;
				//DO NOT DISPLAY CONSENT FORM FOR NEW SURGERY IN LEFT SLIDER
				
				$consentFormSelectQry = "SELECT * FROM `consent_multiple_form` WHERE  confirmation_id = '".$pConfirmId."' AND consent_template_id='".$consentFormTemplateSelectRow['consent_id']."' ";
				$consentFormSelectRes = imw_query($consentFormSelectQry) or die(imw_error()); 
				$consentFormSelectNumRow = imw_num_rows($consentFormSelectRes);
				$consentFormSelectRow = imw_fetch_array($consentFormSelectRes);
				
				$consentFormSelectConsentAlias = $consentFormSelectRow['surgery_consent_alias'];
				
				if(!$consentFormSelectConsentAlias) {
					if($consentFormTemplateSelectConsentAlias!='') {
						$consentFormSelectConsentAlias=$consentFormTemplateSelectConsentAlias;
					}
				}
				
				if($consentFormSelectConsentAlias!='') {
					$consentFormAliasArr[] = $consentFormSelectConsentAlias;
					$consentFormTemplateSelectConsentId[] = $consentFormTemplateSelectRow['consent_id'];
				}	
				
			}
			
		} // End get multiple consent Forms
		
		
		
		
		$chartNotes		=	array() ;
		$chartNotes['alert']	=	'Alert';
		
		if(!$pConfirmId)
		{
			$consentQuery	=	"SELECT * FROM `consent_forms_template` WHERE consent_delete_status != 'true' ORDER BY consent_id ";
			
			$consentSql		=	imw_query($consentQuery) or die(imw_error());
			
			$consentCount	=	imw_num_rows($consentSql) ;
			
			//$consentFormAliasArr = array($light_green);
			if( $consentCount > 0 )
			{
				while( $consentRow	=	imw_fetch_array($consentSql))
				{
						
						$consentID		=	'consent_multiple_form-'.$consentRow['consent_id'];
						$consentAlias	=	$consentRow['consent_alias'];
						$consentAlias	=	str_ireplace("H&P","H&amp;P",$consentAlias);
	
						$chartNotes[$consentID]	=	$consentAlias ;
				
				}
					
			}
			
		}
		
		if($pConfirmId)
		{
			if($consentFormTemplateSelectConsentId)
			{
				$counter	=	0 ;
				
				foreach($consentFormTemplateSelectConsentId as $chkConsentTemplateArrId) 
				{
					
					$chkConsentSurgeryArrQry = "select * from `consent_multiple_form` where  confirmation_id = '".$pConfirmId."' AND consent_template_id='".$chkConsentTemplateArrId."' AND consent_purge_status!='true'  ";
					
					$chkConsentSurgeryArrRes	=	imw_query($chkConsentSurgeryArrQry) or die(imw_error()); 
					$chkConsentSurgeryArrNumRow =	imw_num_rows($chkConsentSurgeryArrRes);
					
					$chkConsentSurgeryArrId		=	'';
					$chkConsentSurgeryArrAlias	=	'';
					
					if($chkConsentSurgeryArrNumRow > 0) {
						
						$chkConsentSurgeryArrRow		=	imw_fetch_array($chkConsentSurgeryArrRes);
						
						$chkConsentSurgeryAutoIncArrId	=	$chkConsentSurgeryArrRow['surgery_consent_id'];
						
						$chkConsentSurgeryArrId			=	$chkConsentSurgeryArrRow['consent_template_id'];
						
						$chkConsentSurgeryArrAlias		=	$chkConsentSurgeryArrRow['surgery_consent_alias'];
						
						if(!$chkConsentSurgeryArrAlias)
						{
							$chkConsentSurgeryArrAlias = $consentFormAliasArr[$counter];
						}
					
					}
					else
					{
							$chkConsentSurgeryArrAlias = $consentFormAliasArr[$counter];
							
					}	
					
					if($chkConsentSurgeryArrAlias) {
							
							$key	=	'consent_multiple_form'.'-'.$chkConsentTemplateArrId.'-'.$chkConsentSurgeryAutoIncArrId ; 
							$value	=	stripslashes($chkConsentSurgeryArrAlias) ; 
							$chartNotes[$key]	=	$value	;
							
					}
					
					$counter++;
					
				}
				
			}
			
		}
		
		/****************************************
		*										*
		* Some Static Chart Notes				*
		*										*
		****************************************/
		$chartNotes['surgical_check_list']			=	'Check List';
		$chartNotes['preophealthquestionnaire']		=	'Pre-Op Health Questionnaire';
		$chartNotes['history_physicial_clearance']	=	'H &amp; P Clearance';
		$chartNotes['preopnursingrecord']			=	'Pre-Op Nursing Record';
		$chartNotes['postopnursingrecord']			=	'Post-Op Nursing Record';
		$chartNotes['preopphysicianorders']			=	'Pre-Op Physician Orders';
		$chartNotes['postopphysicianorders']		=	'Post-Op Physician Orders';
		$chartNotes['localanesthesiarecord']		=	'MAC/Local/Regional Anesthesia Record';
		$chartNotes['preopgenanesthesiarecord']		=	'Pre-Op General Anesthesia Record';
		$chartNotes['genanesthesiarecord']			=	'General Anesthesia Record';
		$chartNotes['genanesthesianursesnotes']		=	'General Anesthesia Nurses Notes';
		$chartNotes['operatingroomrecords']			=	'Operating Room Record';
		$chartNotes['laser_procedure_patient_table']=	'Laser Procedure';
		$chartNotes['operativereport']				=	'Operative Report';
		$chartNotes['dischargesummarysheet']		=	'Discharge Summary Sheet';
		$chartNotes['patient_instruction_sheet']	=	'Instruction Sheet/';
			
			
		$JSONReturn['chartNotes']	=	$chartNotes;	
			
		
		// Start Geeting Records for epost and alerts
		
		$query	=	"SELECT ep.*, IFNULL(concat(usr1.lname,', ',usr1.fname),'') AS created_operator_name, IFNULL(concat(usr2.lname,', ',usr2.fname),'') AS modified_operator_name 
						FROM eposted ep 
						LEFT JOIN users AS usr1 ON (usr1.usersId=ep.created_operator_id)
						LEFT JOIN users AS usr2 ON (usr2.usersId=ep.modified_operator_id)
						WHERE patient_id = '".$patient_id."' AND patient_id != '' 
						AND patient_conf_id = '".$pConfirmId."' AND epost_consent_purge_status !='true' ".$andStubIdQry." 
						ORDER by dtdate desc,T_time desc";	
		
		$sql	=	imw_query($query, $link) or die(imw_error());
		
		$counts	=	imw_num_rows($sql);
	
		$i		=	0; 
		
		$ePostData		=	array();
		$eAlertData		=	array();
		
		while ($row	=	imw_fetch_array($sql))
		{ 
			
			//START CODE TO CHECK TO DISPLAY RECORD
			$consent_template_idShow = $row['consent_template_id'];
			
			$showConfirmedRecord	=	false;
			$showScheduledRecord	= 	false;
			
			if($pConfirmId && (in_array($consent_template_idShow,$consentFormTemplateSelectConsentId) || $consent_template_idShow == '0')) {
				$showConfirmedRecord	=	true	;
				$showScheduledRecord	=	true	;
			}
			if(!$pConfirmId && (in_array($consent_template_idShow,$chkConsentFormTemplateConsent_id) || $consent_template_idShow == '0')) {
				$showConfirmedRecord	=	true;
				$showScheduledRecord	=	true;
			}
			//END CODE TO CHECK TO DISPLAY REOCRD
			
			if($showConfirmedRecord && $showScheduledRecord)
			{
				$DT		=	$row['dtdate'].' '.$row['T_time'];
				//$DT	=	date('m/d/Y h:i A', strtotime($DT));
				$DT		=	$objManageData->getFullDtTmFormat($DT);
				//'eDate' => $row['dtdate'], 'eTime' => $row['T_time'], 
				$consent_template	=	$row['table_name'] . ($row['consent_template_id'] > 0 ? '-'.$row['consent_template_id'] : '' ) . ($row['consentAutoIncId'] > 0 ? '-'.$row['consentAutoIncId'] : '' ) ;
				$data	=	array('epost_id' => $row['epost_id'], 'content' => $row['epost_data'], 'eDateTime' => $DT, 'consent_template_id' => $consent_template, 'created_operator_name' => $row['created_operator_name'], 'modified_operator_name' => $row['modified_operator_name'] ); 
			
				if($row['table_name'] <> 'alert')
					array_push($ePostData,$data);
				else
					array_push($eAlertData,$data);		
			
			}
			
		} // End Getting Records for epost and alerts
		
		$JSONReturn['ePostData']	=	$ePostData;
		$JSONReturn['eAlertData']	=	$eAlertData;
		
		
		// Start Getting Alerts for Patient Sx Alerts 
		$query 	= "SELECT * FROM iolink_patient_alert_tbl WHERE patient_id = '".$patient_id."' AND iosync_status='Syncronized' AND alert_disabled!='yes'";
		$sql			= imw_query($query) or die('Error Found at Line No. ' .(__LINE__) .' : ' .  imw_error() );
		
		$iAlertData		=	array();
		
		if( imw_num_rows($sql) > 0 )
		{
				while($row = imw_fetch_array($sql))	 
				{
					$DT		=	$row['save_date_time'] ;
					//$DT	=	date('m/d/Y h:i A', strtotime($DT));	
					$DT		=	$objManageData->getFullDtTmFormat($DT);
					$data	=	array(	
												'patient_alert_id' => $row['patient_alert_id'],
												'content' => $row['alert_content'],
												'eDateTime' => $DT 
											) ;
					array_push($iAlertData,$data);
				}
				
				
		}
		$JSONReturn['iAlertData']	=	$iAlertData;
		
		
		
	}
	
	echo json_encode($JSONReturn);
		
?>