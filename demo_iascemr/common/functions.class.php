<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
	session_start();
	
	require_once('conDb.php'); 
	
	require_once ('../admin/classObjectFunction.php');
	
	class Functions extends manageData
	{
		
		Public $returnData 	=		array();
		/************************************************************
		*																							
		*			Function:	RegisterUser														
		*																							
		*			Params	:	SID	-  Stub ID													
		*			SDate- Selected data for scheduled Records		
		*															
		*															
		************************************************************/
		Public function RegisterUser($SID,$SDate)	
		{
			$patientStub	=	$this->getExtractRecord('stub_tbl', 'stub_id', $SID, '*');
			
			$patient_id		=	$patientStub["patient_id_stub"];		//	Patient ID in stub Table
			$patientZip 	=	$patientStub["patient_zip"];			//	Patiend Zip Code in stub Table 
			
			$arrayPatientRecord['patient_fname'] 	= addslashes($patientStub["patient_first_name"]);
			$arrayPatientRecord['patient_mname'] 	= addslashes($patientStub["patient_middle_name"]);
			$arrayPatientRecord['patient_lname'] 	= addslashes($patientStub["patient_last_name"]);
			$arrayPatientRecord['street1'] 			= addslashes($patientStub["patient_street1"]);
			$arrayPatientRecord['street2'] 			= addslashes($patientStub["patient_street2"]);
			$arrayPatientRecord['city'] 			= $patientStub["patient_city"];
			$arrayPatientRecord['state'] 			= $patientStub["patient_state"];
			$arrayPatientRecord['zip'] 				= $patientStub["patient_zip"];
			$arrayPatientRecord['date_of_birth'] 	= $patientStub["patient_dob"];
			$arrayPatientRecord['sex'] 				= $patientStub["patient_sex"];
			$arrayPatientRecord['homePhone'] 		= $patientStub["patient_home_phone"];
			$arrayPatientRecord['workPhone'] 		= $patientStub["patient_work_phone"];
			$arrayPatientRecord['imwPatientId'] 	= $patientStub["imwPatientId"];
			
			$patientData	=	array();
	
			if($patient_id)
			{
				$patientData	=	$this->getExtractRecord('patient_data_tbl', 'patient_id', $patient_id, 'patient_id');
			}
			else
			{
				$patientData	=	$this->getRecord('patient_data_tbl', array('patient_id'), array(
																								'patient_name = '	=>addslashes($patientStub["patient_first_name"]),
																								'patient_lname = '	=>addslashes($patientStub["patient_last_name"]),
																								'zip = '			=>addslashes($patientZip),
																								'date_of_birth = '	=>addslashes($patientStub["patient_dob"])
																								)
													);
					
			}
			
			
			if(is_array($patientData) && count($patientData) > 0)
			{
				
				//$patientDataRow = imw_fetch_array($patientMatchQry);
				$res	=	$this->updateRecords($arrayPatientRecord, 'patient_data_tbl', 'patient_id', $patientData["patient_id"]);
				$insertPatientDataId = $patientData["patient_id"];
			
			}
			else
			{
					
	
				
				$insertPatientDataId = $this->addRecords($arrayPatientRecord, 'patient_data_tbl');
				
				//INSERT DEFAULT ENTRIES IN SCAN DOCUMENT TABLE 
				$formFolderArr = array('Pt. Info', 'Clinical');
				
				foreach($formFolderArr as $formFolder)
				{
					$formData	=	array();
					$formData	=	$this->getRecord('scan_documents', array('*'), array(
																								'document_name = '	=>	$formFolder,
																								'patient_id = '		=>	$insertPatientDataId,
																								'confirmation_id = '=>	0,
																								'dosOfScan = '		=>	$SDate,
																								'stub_id = '		=>	$SID
																						)
													);				
					
					if(is_array($formData) && count($formData) == 0)
					{
					
						unset($arrayScanRecord);
						$arrayScanRecord['patient_id']		=	$insertPatientDataId;
						$arrayScanRecord['document_name']	=	$formFolder;
						$arrayScanRecord['dosOfScan']		=	$SDate;
						$arrayScanRecord['stub_id']			=	$SID;
						 
						$insertScanId = $this->addRecords($arrayScanRecord, 'scan_documents');
						
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
						$document_encounter	=	($formFolder == 'Pt. Info') ? 'pt_info_1' : (($formFolder=='Clinical') ? 'clinical_1' : '' );
						
						
						unset($arrayScanRecord);
						$arrayScanRecord['patient_id'] 			=	$insertPatientDataId;
						$arrayScanRecord['document_name']		=	$formFolder;
						$arrayScanRecord['document_id']			=	$insertScanId;
						$arrayScanRecord['document_date_time']	=	date('Y-m-d H:i:s');
						$arrayScanRecord['document_file_name']	=	'home_inner_front.php';
						$arrayScanRecord['document_encounter']	=	$document_encounter;
						
						$inserIdScanLogTbl = $this->addRecords($arrayScanRecord, 'scan_log_tbl');
						//TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
						
					}
					
					
				}//END INSERT DEFAULT ENTRIES IN SCAN DOCUMENT TABLE
				
			
			}
			
			//START CODE TO UPDATE PATIENT-ID IN STUB-TABLE
			if($insertPatientDataId) {
				unset($arrayStubRecord);
				$arrayStubRecord['patient_id_stub']	=	$insertPatientDataId;
				$this->updateRecords($arrayStubRecord, 'stub_tbl', 'stub_id', $SID);
			}
			//END CODE TO UPDATE PATIENT-ID IN STUB-TABLE
			
			$patientStub	=	$this->getExtractRecord('stub_tbl', 'stub_id', $SID, '*');
			
			
			
			return array(
							'PID' => $patientStub["patient_id_stub"],
							'NID' => $patientStub["imwPatientId"]
						);
		
		
		
		}//End Function ******* Register User ******* Here
		
		
		
		Public function ProgressNotesAction($action, $confirmation_id, $asc_id, $note_id, $login_user_type, $login_user_id, $text_note )
		{
				if($action == 'insert')
				{
					return $this->AddProgressNotes($confirmation_id, $asc_id, $login_user_type, $login_user_id, $text_note);
				}
				
				if($action == 'edit')
				{
					return $this->EditProgressNotes($confirmation_id, $asc_id, $login_user_type, $login_user_id, $text_note, $note_id);
				}
				
				
				if($action == 'delete')
				{
					return $this->DeleteProgressNotes($note_id);
				}
				
				return false ;
			
		}
		
		Public function ProgressNotesCount( $where )
		{
			return $this->getRowCount('tblprogress_report', $where);
		}
		
		Private function AddProgressNotes($confirmation_id, $asc_id, $user_type, $user_id, $text_note)
		{
				$insertRecords		=	array();
				$insertRecords	['txtNote'] 				=	addslashes(nl2br($text_note));
				$insertRecords	['usersId']					=	$user_id;
				$insertRecords	['asc_id']					=	$asc_id;
				$insertRecords	['dtDateTime']				=	date('Y-m-d');
				$insertRecords	['tTime']					=	date('H:i:s');
				$insertRecords	['confirmation_id']			=	$confirmation_id;
				$insertRecords	['userType']				=	$user_type;
						
				$insert_id		=		$this->addRecords($insertRecords, 'tblprogress_report');
				
				return ($insert_id > 0) ? true : false; 
				
		}
		
		Private function EditProgressNotes($confirmation_id, $asc_id, $user_type, $user_id, $text_note, $note_id)
		{
				$updateRecords		=	array();
				$updateRecords	['txtNote'] 				=	addslashes(nl2br($text_note));
				$updateRecords	['usersId']					=	$user_id;
				$updateRecords	['asc_id']					=	$asc_id;
				$updateRecords	['dtDateTime']				=	date('Y-m-d');
				$updateRecords	['tTime']					=	date('H:i:s');
				$updateRecords	['confirmation_id']			=	$confirmation_id;
				$updateRecords	['userType']				=	$user_type;
						
				$updateStatus		=		$this->UpdateRecord($updateRecords, 'tblprogress_report', 'intProgressID', $note_id );
				
				return $updateStatus 	?	true	:	false		;
				
		}
		
		Private function DeleteProgressNotes($note_id)
		{
				$deleteStatus	=	$this-> DeleteRecord('tblprogress_report', 'intProgressID', $note_id)	;
				
				return $deleteStatus 	?	true	:	false		;
				
		}
		
		/* **************************************
		*
		* 		Unfinalize Chart Note
		* 		Params :-
		* 		$pConfirmId - Patient Confirmation ID;
		*
		****************************************/
	
		Public function	unfinalizeChart($pConfirmId)
		{
				$row		=	$this->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId, 'patientConfirmationId')	;
				if($row['patientConfirmationId'] == $pConfirmId)
				{
					$insertRecords		=	array();
					$insertRecords['patient_confirmation_id']		=	$pConfirmId;
					$insertRecords['finalize_action']						=	'unfinalize';
					$insertRecords['finalize_action_script	']		=	'manual';
					$insertRecords['finalize_action_type']			=	'revised';
					$insertRecords['finalize_action_user_id']		=	$_SESSION['loginUserId'];
					$insertRecords['finalize_action_datetime']	=	date('Y-m-d H:i:s' );
					
					$insert_id		=		$this->addRecords($insertRecords, 'finalize_history');
				
					if($insert_id)
					{
						$updateRecords		=	array();
						$updateRecords['finalize_status'] 	=	'';
						if( constant("VCNA_EXPORT_ENABLE") == 'YES') 
							$updateRecords['vcna_export_status'] 	=	'0';
						$updateStatus		=		$this->UpdateRecord($updateRecords, 'patientconfirmation', 'patientConfirmationId', $pConfirmId );	
						
						if($updateStatus)
						{
							return true;	
						}
						else
						{
							$this->DeleteRecord('finalize_history','finalize_history_id',$insert_id);	
						}
					}
				
				}
				return false;
		}
		
		
		
		/* **************************************
		*
		* 		Unfinalize History
		* 		Params :-
		* 		$pConfirmId - Patient Confirmation ID;
		*
		****************************************/
		
		Public function	unfinalizeHistory($pConfirmId)
		{
				unset($this->returnData);
				$pconf	=	$this->getRowRecord('patientconfirmation',  'patientConfirmationId', $pConfirmId, 'patientConfirmationId', 'DESC', 'patientId,finalize_status,dos');	
				$patient	=	$this->getRowRecord('patient_data_tbl',  'patient_id', $pconf->patientId, 'patient_id', 'DESC', ' patient_fname,patient_mname,patient_lname ');	
				
				$this->returnData['finalize_status']  =	empty($pconf->finalize_status) 	?	'Unfinalized'	:	'Finalized';
				$this->returnData['patient_name']	  =	'';	
				$this->returnData['patient_name']	 .=	!empty($patient->patient_lname)	?	$patient->patient_lname		:	''	;
				$this->returnData['patient_name']	 .=	(!empty($patient->patient_lname) && !empty($patient->patient_fname) ) ?	', '	:	''	;
				$this->returnData['patient_name']	 .=	!empty($patient->patient_fname)	?	$patient->patient_fname		:	''	;
				$this->returnData['patient_name']	 .=	!empty($patient->patient_mname)?	'&nbsp;'. $patient->patient_mname:	''	;
				
				$this->returnData['dos']					 =		' - '.date('m-d-Y', strtotime($pconf->dos));
				$this->returnData['finalize_history']	 =	 array();
				$FHistory	=	$this->getArrayRecords('finalize_history', 'patient_confirmation_id', $pConfirmId,  'finalize_history_id', 'DESC');
				
				
				if( count($FHistory) > 0 )
				{
						foreach($FHistory as $key => $row)
						{
							$userDetail		=	'';
							$user			  =	$this->getRowRecord('users',  'usersId', $row->finalize_action_user_id, 'usersId', 'DESC', 'fname,mname,lname,user_type ');	
							//$userDetail	 .=	!empty($user->userTitle) ? $user->userTitle . '&nbsp;' : '' ;
							
							$userDetail	 .=	!empty($user->lname)		?	$user->lname 	:	''	;	
							$userDetail	 .=	(!empty($user->fname) && !empty($user->lname))	?	", " : '' ;
							$userDetail	 .=	!empty($user->fname)		?	$user->fname 		: ''  ;
							$userDetail	 .=	!empty($user->mname)	?	'&nbsp;' . $user->mname  : '' ;
							
							$userDetail	 .=	!empty($userDetail)			?	'&nbsp;(' . $user->user_type . ')'		:	$user->user_type ;
							$this->returnData['finalize_history'][]		=		array(
																									'id' => $row->finalize_history_id,
																									'action' => ucwords($row->finalize_action),
																									'action_mode' => ($row->finalize_action_script == 'auto'   ? ' Auto '.ucwords($row->finalize_action) : 'Manually '.ucwords($row->finalize_action).' ' ),
																									'action_type' => ucwords($row->finalize_action_type == 'revised' ? 'yes' : 'no' ),
																									'user' => $userDetail,
																									'date' => date('m-d-Y',strtotime($row->finalize_action_datetime)),
																									'time' => date('h:i A',strtotime($row->finalize_action_datetime))
																								);		
						
						}
						
						return true;
				}
				return false;
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	}
	
	
	
		
?>