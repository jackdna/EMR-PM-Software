<?php
/*
File: CLS_notifications.php
Coded in PHP 7
Purpose: Contains functions used in iconbar notifications, icon status color change.
Access Type: Include file 
*/

require_once("msgConsole.php");
class core_notifications{
	private $authId, $patient_id, $user_type, $resultset, $now, $queryPart1, $queryPart2, $msgConsoleObj;
	function __construct(){ //constructor
		$this->authId 			= intval($_SESSION['authId']);
		$this->patient_id 		= intval($_SESSION['patient']);
		$this->user_type		= intval($_SESSION['logged_user_type']);
		$this->resultset		= $this->check_record_exists();
		$this->now				= date('Y-m-d H:i:s');
		$this->msgConsoleObj 	= new msgConsole();
		$this->msgConsoleObj->callFrom = 'iconbar';
		
		$this->queryPart1		= "last_updated_by='".$this->authId."', last_updated_on='".$this->now."'";
		$this->queryPart2 = "patient_id='".$this->patient_id."', last_updated_by='".$this->authId."', last_updated_on='".$this->now."'";
		//pre($_SESSION,1);
	}
	
	function check_record_exists(){
		$rs = false;
		$q = "SELECT * FROM notification_status WHERE patient_id='".$this->patient_id."' LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
		}
		return $rs;	
	}
	
	function get_notification_status(){
		$this->update_all_notifications();
		$rs = $this->check_record_exists();

		/*--SPECIAL CHANGES FOR TEST-EYE ICON---*/
		if(strlen(trim($rs['testseye']))>5 && trim($rs['testseye'])!='0'){
			$tests_serialized_str = $rs['testseye'];
			$tests_array_status = $this->str2arr($tests_serialized_str);
			$rs['testseye'] = $tests_array_status[$this->authId];
			/*if(!in_array($this->user_type,array('1','11','12','19')) && $rs['testseye']=='2'){
				$rs['testseye']=1;
			}*/
		}
		/*--SPECIAL CHANGES FOR CONSULT/OPNOTE ICON---*/
		if(strlen(trim($rs['consult']))>5 && trim($rs['consult'])!='0'){
			$consult_serialized_str = $rs['consult'];
			$consult_array_status = $this->str2arr($consult_serialized_str);
			$rs['consult'] = $consult_array_status[$this->authId];
		}
		if(strlen(trim($rs['opnote']))>5 && trim($rs['opnote'])!='0'){
			$opnote_serialized_str = $rs['opnote'];
			$opnote_array_status = $this->str2arr($opnote_serialized_str);
			$rs['opnote'] = $opnote_array_status[$this->authId];
		}
		
		/*---SPECIAL CHNAGES FOR vs IF USER IF NOT PHYSICIAN--*/
		if($this->user_type != '1' && $rs['vs']=='2'){
			$rs['vs']=1;
		}
		
		/*---SPECIAL CHNAGES FOR Sx IF USER IF NOT PHYSICIAN--*/
		if($this->user_type != '1' && $rs['sx']=='2'){
			$rs['sx']=1;
		}
				
		/*---SPECIAL CHNAGES FOR medHx IF USER IF NOT PHYSICIAN--*/
		if(strlen(trim($rs['medHx']))>5 && trim($rs['medHx'])!='0'){
			$medHx_serialized_str = $rs['medHx'];
			$medHx_array_status = $this->str2arr($medHx_serialized_str);
			$rs['medHx'] = $medHx_array_status[$this->authId];
			if(!in_array($this->user_type,array('1','11','12','19')) && $rs['medHx']=='2'){
				$rs['medHx']=1;
			}
		}		
	
		return $rs;
	}
		
	function set_notification_status($section){
		if(intval($this->patient_id)<=0){return;}
		$this->resultset = $this->check_record_exists();
		switch($section){
			case 'testseye':
				$this->set_testseye_status();
				break;
			case 'consult':
			case 'opnote':
				$this->set_doc_hold_status($section);
				break;
			case 'sx':
				$this->update_sxicon_status();
				break;
			case 'vs':
				$this->update_vitalSign_status();
				break;
			case 'medhx':
				$this->update_medHx_status();
				break;
			case 'scanDocs':
				$this->set_scan_status();
				break;
		}
	}
	
	function update_all_notifications(){
		$arr_sections = array('testseye','sx','medhx','consult','opnote','scanDocs');
		foreach($arr_sections as $section){
			$this->set_notification_status($section);
		}
	}
	
	function set_testseye_status(){
		$iconStatus=0; /*white*/
		$pendingTests = $this->msgConsoleObj->get_tests_tasks('tests');
		if($pendingTests && is_array($pendingTests) && count($pendingTests)>0){
				$iconStatus=1; /*green*/
				foreach($pendingTests as $arrTestRecord){
					if($arrTestRecord['phyName'] == "" || $arrTestRecord['phyName'] == "0"){
						$iconStatus=2; /*yellow*/
						break;
					}
				}
		}
		
		/* GET THE EXISTING VALUE AND SET NEW STATUS */
		if($this->resultset && strlen($this->resultset['testseye'])>5){
			$serialized_str = $this->resultset['testseye'];
			$array_status = $this->str2arr($serialized_str);
		}else{
			$array_status = array();
		}
		$array_status[$this->authId] = $iconStatus;
		$serialized_str = $this->arr2str($array_status);
		
		if($this->resultset){/*if patient's record exists*/
			$q = "UPDATE notification_status SET testseye = '".$serialized_str."', ".$this->queryPart1." 
				  WHERE patient_id='".$this->patient_id."'";
		}else{/*if patient's record NOT exists*/
			$q = "INSERT INTO notification_status SET testseye = '".$serialized_str."', ".$this->queryPart2;
		}
		$res = imw_query($q);
	}
	
	function set_doc_hold_status($section, $holdDocs_id = ''){
		if($holdDocs_id!=''){
			//GETTING PATIENT IDs WITH DOC_ID
			$q0="SELECT GROUP_CONCAT(patient_id) AS patient_ids FROM consent_hold_sign 
				 WHERE ".$section."_id IN ($holdDocs_id) AND physician_id = '$this->authId' 
				 GROUP BY physician_id";
			$res0 = imw_query($q0);
			if($res0 && imw_num_rows($res0)==1){
				$rs0 = imw_fetch_assoc($res0);
				$patients = $rs0['patient_ids'];
				$arr_PtIDs = explode(',',$patients);
			}
		}else{
			$arr_PtIDs = array($this->patient_id);
		}
		foreach($arr_PtIDs as $ptid){
			$this->patient_id = $ptid;
			$iconStatus=0; //white
			if($section=='consult'){
				$q1 = "SELECT patient_consult_id FROM patient_consult_letter_tbl where patient_id = '$this->patient_id' 
					   AND status = '0' LIMIT 0,1";
				$res1 = imw_query($q1);
				if($res1 && imw_num_rows($res1)==1){
					$iconStatus=1; //green
				}
			}elseif($section=='opnote'){
				$q1 = "SELECT pn_rep_id FROM pn_reports where patient_id = '$this->patient_id' and status = '0'";
					$res1 = imw_query($q1);
					if($res1 && imw_num_rows($res1)>0){
						$iconStatus=1; //green
					}
			}else{
				return;	
			}

			$q1="SELECT id FROM consent_hold_sign WHERE patient_id='$this->patient_id' AND ".$section."_id != 0 
				 AND physician_id = '$this->authId' AND signed = 0 LIMIT 0,1";
			$res1 = imw_query($q1);
			if($res1 && imw_num_rows($res1)==1){
				$iconStatus=2; //yellow
			}
	
			if($this->resultset){
				$serialized_str = $this->resultset[$section];
				$array_status = $this->str2arr($serialized_str);
			}else{
				$array_status = array();
			}
			$array_status[$this->authId] = $iconStatus;
			$serialized_str = $this->arr2str($array_status);
					
			if($this->resultset){
				$q = "UPDATE notification_status SET ".$section." = '".$serialized_str."', ".$this->queryPart1." 
					  WHERE patient_id='".$this->patient_id."'";
			}else{
				$q = "INSERT INTO notification_status SET ".$section." = '".$serialized_str."', ".$this->queryPart2;
			}
			$res = imw_query($q);
		}
	}
	
	function set_switched_hold($to_phy_id,$section){
		if($this->resultset){
			$serialized_str = $this->resultset[$section];
			$array_status = $this->str2arr($serialized_str);
		}else{
			$array_status = array();
		}
		$array_status[$to_phy_id] = 2;
		$serialized_str = $this->arr2str($array_status);
		
		if($this->resultset){
			$q = "UPDATE notification_status SET ".$section." = '".$serialized_str."', ".$this->queryPart1." 
				  WHERE patient_id='".$this->patient_id."'";
		}else{
			$q = "INSERT INTO notification_status SET ".$section." = '".$serialized_str."', ".$this->queryPart2;
		}
		$res = imw_query($q);	
	}
	
	function str2arr($str){
		$arr = unserialize(html_entity_decode($str));
		return $arr;
	}
	
	function arr2str($arr){
		$str = htmlentities(serialize($arr));
		return $str;
	}
	
	function update_vitalSign_status($iconStatus=''){
		if($iconStatus==''){
			$query_vs = "SELECT * FROM vital_sign_master WHERE patient_id = '".$this->patient_id."' 
							AND status='0' ORDER BY created_on DESC LIMIT 0,1";
			$result_vs = imw_query($query_vs);
			if($result_vs && imw_num_rows($result_vs)==1){
				$iconStatus=1;
				while($rs_vs = imw_fetch_assoc($result_vs)){
					$phy_reviewed = $rs_vs['phy_reviewed'];
					$phy_reviewed_date = $rs_vs['phy_reviewed_date'];
					if($phy_reviewed=='0' && $phy_reviewed_date=='0000-00-00 00:00:00'){
						$iconStatus=2;
						continue;
					}
				}
			}
		}
		if($this->resultset){
			$q = "UPDATE notification_status SET vs = '".$iconStatus."', ".$this->queryPart1." 
				  WHERE patient_id='".$this->patient_id."'";
		}else{
			$q = "INSERT INTO notification_status SET vs = '".$iconStatus."', ".$this->queryPart2;
		}
		$res = imw_query($q);
	}
	
	function update_sxicon_status($iconStatus=''){
		if($iconStatus==''){
			$query_rvwDate = "SELECT created_date FROM patient_last_examined ple 
							JOIN users u ON (u.id = ple.operator_id) 
							WHERE ple.section_name IN ('Sx/Procedure','complete') 
							AND ple.patient_id = '".$this->patient_id."' 
							AND ple.save_or_review = '2' 
							AND u.user_type IN ('1','12') 
							ORDER BY created_date DESC 
							LIMIT 0,1";
			$result_rvwDate = imw_query($query_rvwDate);
			if($result_rvwDate && imw_num_rows($result_rvwDate)==1){
				$rs_rvwDate = imw_fetch_array($result_rvwDate);
				$sx_review_date = $rs_rvwDate['created_date'];
			}				
	
			$query_ocusx = "SELECT date FROM lists 
							WHERE pid = '".$this->patient_id."' 
							AND type IN ('5','6') 
							AND allergy_status != 'Deleted' 
							ORDER BY date DESC 
							LIMIT 0,1";
			$result_ocusx = imw_query($query_ocusx);
			if($result_ocusx && imw_num_rows($result_ocusx)==1){
				$rs_ocusx = imw_fetch_array($result_ocusx);
				$ocusx_date = $rs_ocusx['date'];
			}
			
			$iconStatus=0;
			if(isset($ocusx_date) && $ocusx_date != '' && $ocusx_date != 'NULL' && $ocusx_date != '0000-00-00 00:00:00'){
				$iconStatus=1;
				if(isset($sx_review_date) && $sx_review_date != '' && $sx_review_date != 'NULL' && $sx_review_date != '0000-00-00 00:00:00'){
					//converting dates to timestamp. 
					$str_ocusx_date = intval(strtotime($ocusx_date));
					$str_sx_review_date = intval(strtotime($sx_review_date));
					if ($str_sx_review_date < $str_ocusx_date){
						$iconStatus=2;
					}
				}else if(!isset($sx_review_date)){
					$iconStatus=2;					
				}
			}	
		}
		if($this->resultset){
			$q = "UPDATE notification_status SET sx = '".$iconStatus."', ".$this->queryPart1." 
				  WHERE patient_id='".$this->patient_id."'";
		}else{
			$q = "INSERT INTO notification_status SET sx = '".$iconStatus."', ".$this->queryPart2;
		}
		$res = imw_query($q);
	}
	
	function update_medHx_status($iconStatus=''){
		if($iconStatus==''){
			$query_medhx = "SELECT ple.patient_last_examined_id, ple.save_or_review, ple.operator_id, 
							u.user_type FROM patient_last_examined ple  
						LEFT JOIN users u ON (ple.operator_id=u.id) WHERE ple.patient_id = '$this->patient_id' 
							ORDER BY patient_last_examined_id DESC LIMIT 0,1";				
			$result_medhx = imw_query($query_medhx);
			if($result_medhx && imw_num_rows($result_medhx)>0){
				$iconStatus=1;
				$rd_medhx = imw_fetch_assoc($result_medhx);
				if($rd_medhx['operator_id'] != $this->authId){//in_array($rd_medhx['user_type'],array('1','11','12','19')) && 
					$iconStatus=2;
				}
			}else{
				$iconStatus=0;
			}
		}
		
		if($this->resultset && strlen($this->resultset['medHx'])>3){
			$serialized_str = $this->resultset['medHx'];
			$array_status = $this->str2arr($serialized_str);
		}else{
			$array_status = array();
		}
		$array_status[$this->authId] = $iconStatus;
		$serialized_str = $this->arr2str($array_status);
		
		
		if($this->resultset){
			$q = "UPDATE notification_status SET medHx = '".$serialized_str."', ".$this->queryPart1." 
				  WHERE patient_id='".$this->patient_id."'";
		}else{
			$q = "INSERT INTO notification_status SET medHx = '".$serialized_str."', ".$this->queryPart2;
		}
		$res = imw_query($q);			
	}
	
	function set_scan_status($iconStatus='')
	{
		if($iconStatus==''){
			$iconStatus = 0;
			$qryChkAnyDocExists = "SELECT sdt.scan_doc_id as scan_doc_id FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt 
						JOIN ".constant("IMEDIC_SCAN_DB").".folder_categories fc ON (sdt.folder_categories_id = fc.folder_categories_id) 
						WHERE sdt.patient_id = '".$this->patient_id."'".
						" AND ((".constant("IMEDIC_SCAN_DB").".fc.patient_id = '".$this->patient_id."') OR (".constant("IMEDIC_SCAN_DB").".fc.patient_id = 0)) ".
						"LIMIT 0,1";
			$result_docexists = imw_query($qryChkAnyDocExists);
			if($result_docexists && imw_num_rows($result_docexists)>0){
				$iconStatus = 1;
				$str_scndocID_arr = array();
				while($rs_docexists = imw_fetch_array($result_docexists)){
							$str_scndocID_arr[] = $rs_docexists['scan_doc_id'];
				}
				$str_scndocID = implode(',',$str_scndocID_arr);
				/*--CHECKING IF CURRENT OPERATOR is physician and MATCHED WITH SCAN TASK PROVIDER--*/
				if(intval($this->user_type)==1){
					
					$query_scndocprovier = "SELECT scan_doc_id FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl 
											WHERE (task_physician_id='".$this->authId."' OR task_physician_id=0)
											AND scan_doc_id IN ($str_scndocID)
											AND patient_id = '".$this->patient_id."'";
					$result_scndocprovider = imw_query($query_scndocprovier);
					if($result_scndocprovider && imw_num_rows($result_scndocprovider)>0){
						$iconStatus = 2;
						$count_scndocprovider = imw_num_rows($result_scndocprovider);
						$str_scndocID = '';
						$str_scndocID_arr = array();
						while($rs_scndocprovider = imw_fetch_array($result_scndocprovider)){
							$str_scndocID_arr[] = $rs_scndocprovider['scan_doc_id'];
						}
						$str_scndocID = implode(',',$str_scndocID_arr);
						//-- checking all scandocs are reviewed by physician or not---.
						$query_viewlog = "SELECT id FROM provider_view_log_tbl 
										WHERE patient_id = '".$this->patient_id."' 
										AND provider_id = '".$this->authId."' 
										AND scan_doc_id IN (".$str_scndocID.") 
										AND section_name='scan'
										GROUP BY scan_doc_id 
										HAVING COUNT(*)>0
										";
						$result_viewlog = imw_query($query_viewlog);
						if($result_viewlog && imw_num_rows($result_viewlog) == $count_scndocprovider){
							$iconStatus = 1;
						}else{
							$iconStatus = 2;
						}
					}
				}
			}
		}

		if($this->resultset){
			$q = "UPDATE notification_status SET scanDocs = '".$iconStatus."', ".$this->queryPart1." 
				  WHERE patient_id='".$this->patient_id."'";
		}else{
			$q = "INSERT INTO notification_status SET scanDocs = '".$iconStatus."', ".$this->queryPart2;
		}

		$res = imw_query($q);		
	}
	
	function set_pvc_status($iconStatus='')
	{
		$iconStatus=0;
		$query_pvc = "SELECT user_message_id FROM user_messages WHERE patientId = '".$this->patient_id."' AND Pt_Communication='1' and message_status = '0' LIMIT 0,1";
		$result_pvc = imw_query($query_pvc);
		if($result_pvc && imw_num_rows($result_pvc)>0){
			$iconStatus=1;
		}
		
		$query_pvcRVW = "SELECT user_message_id FROM user_messages WHERE patientId = '".$this->patient_id."' AND Pt_Communication='1' AND message_status = '0' AND review_by = 0 LIMIT 0,1";
		$result_pvcRVW = imw_query($query_pvcRVW);
		if($result_pvcRVW && imw_num_rows($result_pvcRVW)>0){
			$iconStatus=2;
		}				
		
		if($this->resultset){
			$q = "UPDATE notification_status SET pvc = '".$iconStatus."', ".$this->queryPart1." 
				  WHERE patient_id='".$this->patient_id."'";
		}else{
			$q = "INSERT INTO notification_status SET pvc = '".$iconStatus."', ".$this->queryPart2;
		}

		$res = imw_query($q);				
	}
	
	function set_pt_specific_alert($iconStatus='')
	{
		$iconStatus=0;
		$query_ptalert = "SELECT alertContent ,alert_to_show_under, alertId, alert_showed FROM alert_tbl WHERE patient_id='".$this->patient_id."' 
		AND is_deleted = '0'";
		$result_ptalert = imw_query($query_ptalert);
		if($result_ptalert && imw_num_rows($result_ptalert)>0){
			$iconStatus=1;
			while($rs_ptalert = imw_fetch_assoc($result_ptalert)){
				$al_showed = $rs_ptalert['alert_showed'];
				if(!stristr($al_showed,'1')){
					$iconStatus=2;
					continue;
				}
			}
		}

		if($this->resultset){
			$q = "UPDATE notification_status SET ptAlert = '".$iconStatus."', ".$this->queryPart1." 
				  WHERE patient_id='".$this->patient_id."'";
		}else{
			$q = "INSERT INTO notification_status SET ptAlert = '".$iconStatus."', ".$this->queryPart2;
		}

		$res = imw_query($q);
	}
	
	function get_review_array_allergies_delete($med_id,$medName,$opreaterId,$action){
		$arrReview_Allergies_Delete = array();
		$arrReview_Allergies_Delete[] = array(
			"Pk_Id" => $med_id,		
			"Table_Name" => "lists",												
			"Field_Text" => "Patient Allergie",								
			"Operater_Id" => $opreaterId,					
			"Action"=> $action,						
			"Old_Value" => $medName			
		);				
		return $arrReview_Allergies_Delete;
	}
	
	function get_genhealth_noti(){
		$this->update_all_notifications();
		$arr_notifications = $this->get_notification_status();
		//MEDICAL HISTORY (stethoscope)--------index (7)
		$medhx_img = ''; //'icon24 icon24_normal mt4';
		if($arr_notifications['medHx']=='1'){
			$medhx_img = 'cbgreen'; //'icon24 icon24_greenbg mt4';Green
		}else if($arr_notifications['medHx']=='2'){
			$medhx_img = 'cborange'; //'icon24 icon24_orangebg mt4';Orange
		}
		return $medhx_img;	
	}

}//end of class tag.
?>