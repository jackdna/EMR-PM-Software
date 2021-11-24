<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> PHMS Class
 Access Type: Indirect Access.
 
*/

include_once $GLOBALS['srcdir'].'/classes/CLSAlerts.php';
$cls_alerts = new CLSAlerts;

class PHMS extends MedicalHistory
{
	public $intTempCounter = 0;
	public $search_by = '';
	public $alertFor = "";
	
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
	}
	
	//Setting default values
	public function set_init($search_by){
		$this->search_by = $search_by;
		$qryGetPlanNameData = "SELECT alertId,site_care_plan_name,site_care_plan_for FROM alert_tbl where alert_created_console = '0' and alertId = '".$search_by."'  ORDER BY alertId";
		$rsGetPlanNameData = imw_query($qryGetPlanNameData);
		if($rsGetPlanNameData){
			if(imw_num_rows($rsGetPlanNameData) > 0){
				$rowGetPlanNameData = imw_fetch_array($rsGetPlanNameData);
				if((int)$rowGetPlanNameData['site_care_plan_for'] == 0){
					$this->alertFor = "PHMS";
				}
				elseif((int)$rowGetPlanNameData['site_care_plan_for'] == 1){
					$this->alertFor = "Immunization";
				}					
			}				
		}
		return $this->search_by;
	}
	
	//Returns PHMS data
	public function get_phms_data(){
		$phms_data = '';
		$qryTemp = $qryTempInternal = "";
		if($this->search_by == "Administered"){
			$qryTempInternal = " and alert_shown_status = '1' and action_perform ='Administered' ";
		}
		elseif($this->search_by == "Declined"){
			$qryTempInternal = " and alert_shown_status = '1' and action_perform ='Decline' ";
		}
		elseif($this->search_by == "Not Due"){
			$qryTempInternal = " and alert_shown_status = '0' ";
		}
		elseif((int)$this->search_by > 0){
			$qryTempInternal = " and alert_id  = '".$this->search_by."' ";
		}
		$qrySelectHMS = "SELECT at.alertId,at.alertContent,at.site_care_plan_for,at.site_care_plan_name, at.alert_created_console, atr.reason, atr.patient_frequency_id ,
								Date_Format(atr.alert_date,'".get_sql_date_format()."') as alertDate , atr.scp_reson_code,
								TIME_FORMAT(atr.alert_date,'%r') as alertTime, CONCAT_WS(',',us.lname,us.fname) as usOprater 
								FROM
								alert_tbl at inner join alert_tbl_reason atr on at.alertId = atr.alertId 
								left join users us on atr.operator = us.id 
								left join patient_frequency pf on pf.id = atr.patient_frequency_id 
								where atr.patient_id='".$this->patient_id."' and  at.alert_created_console = '0' and  atr.alert_from = '1' ".$qryTemp.
								"";							
		$rsSelectHMS = imw_query($qrySelectHMS);
				
		$arrID = array();
		while($rowSelectHMS = imw_fetch_array($rsSelectHMS)){											
			if(!in_array($rowSelectHMS['alertId'],$arrID)){														
				$arrID[] = $rowSelectHMS['alertId'];					
				if((int)$rowSelectHMS['site_care_plan_for'] == 0){						
					$qryGetPatFrequency = "select id,action_perform,bool_reschedule,Date_Format(before_reschedule_date,'".get_sql_date_format()."') as scpOriDatebefResc,frequency,frequency_type,alert_shown_status,Date_Format(date_time,'".get_sql_date_format()."') as scpActionDate,TIME_FORMAT(date_time,'%r') as scpActionTime,date_time as oriDateTime,Date_Format(next_frequency_date,'".get_sql_date_format()."') as scpNxtActionDate  from patient_frequency where alert_id = '".$rowSelectHMS['alertId']."' and patient_id = '".$this->patient_id."' ".$qryTempInternal.
					" ORDER BY scpActionDate DESC, scpActionTime DESC, id ";						
					$rsGetPatFrequency = imw_query($qryGetPatFrequency);
					
					while($rowGetPatFrequency = imw_fetch_array($rsGetPatFrequency)){
						$class = $this->intTempCounter%2==0?"bgcolor" : "";
						$PHMSDate = $PHMSTime = $PHMSType = $PHMSPlanName = $PHMSDetail = $PHMSFreq = $PHMSStatus = $PHMSResonCode = $PHMSReson = $PHMSUser = "";
						if($rowGetPatFrequency['scpActionDate'] >= 1){
							$PHMSDate = $rowGetPatFrequency['scpActionDate'];
						}
						elseif($rowGetPatFrequency['scpNxtActionDate']){
							$PHMSDate = $rowGetPatFrequency['scpNxtActionDate'];
						}
						else{
							$PHMSDate = $rowSelectHMS['alertDate'];
						}
						if($rowGetPatFrequency['oriDateTime'] != "0000-00-00 00:00:00"){
							$PHMSTime = $rowGetPatFrequency['scpActionTime'];
						}
						
						if((int)$rowSelectHMS['site_care_plan_for'] == 1){
							$PHMSType = "Immunization";
						}
						elseif((int)$rowSelectHMS['site_care_plan_for'] == 0){
							$PHMSType = "PHMS";
						}
						
						if($rowSelectHMS['site_care_plan_name']){
							$PHMSPlanName = $rowSelectHMS['site_care_plan_name'];							
						}
						
						if($rowSelectHMS['alertContent']){
							$PHMSDetail = $rowSelectHMS['alertContent'];
						}
						
						if((int)$rowGetPatFrequency['frequency_type'] == 1){
							$PHMSFreqType = " Month";
							$PHMSFreq = $rowGetPatFrequency['frequency'].$PHMSFreqType;						
						}
						elseif((int)$rowGetPatFrequency['frequency_type'] == 2){
							$PHMSFreqType = " Year";
							$PHMSFreq = $rowGetPatFrequency['frequency'].$PHMSFreqType;
						}
						
						if($PHMSFreq == "200 Year"){
							$PHMSFreq = "None";
						}
						
						if((int)$rowGetPatFrequency['alert_shown_status'] == 1){
							if($rowGetPatFrequency['action_perform'] == "Administered"){
								$PHMSStatus = "Administered";
							}
							elseif($rowGetPatFrequency['action_perform'] == "Decline"){
								$PHMSStatus = "Declined";
							}
							elseif($rowGetPatFrequency['action_perform'] == "InsertRecall"){
								$PHMSStatus = "Recall Inserted";
							}
							elseif($rowGetPatFrequency['action_perform'] == "InsertReschedule" && (int)$rowGetPatFrequency['bool_reschedule'] == 1){
								$PHMSStatus = "Reschedule";
							}
						}
						elseif((int)$rowGetPatFrequency['alert_shown_status'] == 0){						
							$PHMSStatus = "Not Due";
						}
						elseif((int)$rowGetPatFrequency['alert_shown_status'] == 3){						
							$PHMSStatus = "Reschedule";
						}
						
						if($rowSelectHMS['scp_reson_code'] && (int)$rowGetPatFrequency['alert_shown_status'] == 1){
							$PHMSResonCode = $rowSelectHMS['scp_reson_code'];
						}
						if($rowSelectHMS['reason'] && (int)$rowGetPatFrequency['alert_shown_status'] == 1){
							if($rowGetPatFrequency['scpOriDatebefResc'] >= 1 || (int)$rowGetPatFrequency['bool_reschedule'] == 1){
								$PHMSReson = "Orignal Date: ".$rowGetPatFrequency['scpOriDatebefResc'];
							}
							else{
								$PHMSReson = $rowSelectHMS['reason'];
							}
						}
						elseif((int)$rowGetPatFrequency['alert_shown_status'] == 0){
							if($rowGetPatFrequency['scpOriDatebefResc'] >= 1 || (int)$rowGetPatFrequency['bool_reschedule'] == 1){
								$PHMSReson = "Orignal Date: ".$rowGetPatFrequency['scpOriDatebefResc'];
								$PHMSStatus = "Reschedule";
							}
						}
						if($rowSelectHMS['usOprater']){
							$PHMSUser = $rowSelectHMS['usOprater'];
						}
						if($PHMSDate >= 1){
						$phms_data .= '<tr class="'.$class.'">
								<td nowrap>'.$PHMSDate.'</td>
								<td nowrap>'.$PHMSTime.'</td>
								<td >'.$PHMSType.'</td>
								<td >'.$PHMSPlanName.'</td>
								<td >'.$PHMSDetail.'</td>
								<td >'.$PHMSFreq.'</td>					
								<td >'.$PHMSStatus.'</td>
								<td >'.$PHMSResonCode.'</td>
								<td >'.$PHMSReson.'</td>
								<td >'.$PHMSUser.'</td>
							</tr>	';
						}
						$this->intTempCounter++;			
					}
				}				
			 }
		}
		return $phms_data;
	}
	
	//Return Immunization data
	public function get_immu_data(){
		$immu_data = '';
		$qryTemp = $qryTempInternal = "";
		if($this->search_by == "Administered"){
			$qryTemp = " and scpStatus = 'Administered' ";					
		}
		elseif($this->search_by == "Declined"){
			$qryTemp = " and scpStatus = 'Decline' ";	
		}				
		
		 $qrySelectImmData = "select date_format(imm.administered_date,'".get_sql_date_format()."') as administeredDate,TIME_FORMAT(imm.administered_time, '%r') as administeredTime,
								imm.note,imm.scp_alert_id,imm.id,imm.immzn_dose_id,imm.scpStatus,CONCAT_WS(',',us.lname,us.fname) as usOprater			 						 			
								from immunizations imm 
								left join users us on imm.administered_by_id = us.id 
								where patient_id='".$this->patient_id."' ".$qryTemp.
								" order by administeredDate DESC, administeredTime DESC, id DESC ";
		$rsSelectImmData = imw_query($qrySelectImmData);
		while($rowSelectImmData = imw_fetch_array($rsSelectImmData)){
			$PHMSDate = $PHMSTime = $PHMSType = $PHMSPlanName = $PHMSDetail = $PHMSFreq = $PHMSStatus = $PHMSResonCode = $PHMSReson = $PHMSUser = "";                    	
			$arrScpAlertId = array();
			if($rowSelectImmData['administeredDate'] >= 1){
				$PHMSDate = $rowSelectImmData['administeredDate'];
			}
			if($rowSelectImmData['administeredTime'] != "00:00:00"){
				$PHMSTime = $rowSelectImmData['administeredTime'];
			}
			$PHMSType = "Immunization";
			if($rowSelectImmData['scp_alert_id'] != ""){					
				$arrScpAlertId = explode("~~",$rowSelectImmData['scp_alert_id']);
				foreach($arrScpAlertId as $key => $val){
					if($val){
						$qryGetScpdData = "select alertId,alertContent,site_care_plan_name,site_care_plan_for from alert_tbl where alertId = '".$val."'";
						$rsGetScpdData = imw_query($qryGetScpdData);
						if($rsGetScpdData){
							if(imw_num_rows($rsGetScpdData) > 0){
								while($rowGetScpdData = imw_fetch_array($rsGetScpdData)){	
									if((int)$rowGetScpdData['site_care_plan_for'] == 1){
										$PHMSType = "Immunization";
									}
									elseif((int)$rowGetScpdData['site_care_plan_for'] == 0){
										$PHMSType = "PHMS";
									}	
									if($rowGetScpdData['site_care_plan_name']){
										$PHMSPlanName .= $rowGetScpdData['site_care_plan_name']."<br\>";
									}
									if($rowGetScpdData['alertContent']){
										$PHMSDetail .= $rowGetScpdData['alertContent']."<br\>";
									}
								}
							}
						}
					}
				}
			}
			$qryGetPatImmFreq = "select dose_gap,dose_gapoption from immunization_dosedetails where dose_id  = '".$rowSelectImmData['immzn_dose_id']."'";
			$rsGetPatImmFreq = imw_query($qryGetPatImmFreq);
			if($rsGetPatImmFreq){
				if(imw_num_rows($rsGetPatImmFreq) > 0){
					while($rowGetPatImmFreq = imw_fetch_array($rsGetPatImmFreq)){	
						$PHMSFreq = $rowGetPatImmFreq['dose_gap']." ".$rowGetPatImmFreq['dose_gapoption'];
					}	
				}
			}
			
			$PHMSStatus = $rowSelectImmData['scpStatus'];
			if($rowSelectImmData['scp_alert_id'] != "" && $rowSelectImmData['scpStatus'] == "Administered"){						
				$qryGetPatImmOverRideReson = "select alert_reason,scp_reson_code from immunizations_alerts where pat_Immu_Id = '".$rowSelectImmData['id']."'";
				$rsGetPatImmOverRideReson = imw_query($qryGetPatImmOverRideReson);
				if($rsGetPatImmOverRideReson){
					if(imw_num_rows($rsGetPatImmOverRideReson) > 0){
						$rowGetPatImmOverRideReson = imw_fetch_array($rsGetPatImmOverRideReson);	
						if($rowGetPatImmOverRideReson['scp_reson_code']){
							$PHMSResonCode = $rowGetPatImmOverRideReson['scp_reson_code'];
						}
					}
				}
			}
			$PHMSReson = $rowSelectImmData['note'];
			if($rowSelectImmData['usOprater']){
				$PHMSUser = $rowSelectImmData['usOprater'];
			}
			
			$class = $this->intTempCounter%2==0?"bgcolor" : "";
			$immu_data .= '<tr height="21" class="'.$class.'">
				<td nowrap>'.$PHMSDate.'</td>
				<td nowrap>'.$PHMSTime.'</td>
				<td >'.$PHMSType.' </td>
				<td >'.$PHMSPlanName.'</td>
				<td >'.$PHMSDetail.'</td>
				<td >'.$PHMSFreq.'</td>					
				<td >'.$PHMSStatus.'</td>
				<td >'.$PHMSResonCode.'</td>
				<td >'.$PHMSReson.'</td>
				<td >'.$PHMSUser.'</td>
			</tr>';
			$this->intTempCounter++;
		}
		return $immu_data;	
	}
	
	//Return Break glass data
	public function get_break_glass_data(){
		$break_glass_data = '';
		$qryGetBreakGlassData = "select rr.access_reason accessReson,date_format(rr.access_date,'".get_sql_date_format()."') as accessDate,
									TIME_FORMAT(rr.access_date, '%r') as accessTime,sr.reason_code masterResonCode,
									CONCAT_WS(',',us.lname,us.fname) as usOprater 
									from restricted_reasons rr left join scp_reasons sr on sr.scp_id = rr.scp_code_id 
									left join users us on us.id = rr.operator_id
									where patient_id='".$this->patient_id."'".
									" order by accessDate DESC, accessTime DESC, reason_id DESC ";
		$rsGetBreakGlassData = imw_query($qryGetBreakGlassData);							
		if($rsGetBreakGlassData){
			if(imw_num_rows($rsGetBreakGlassData) > 0){
				while($rowGetBreakGlassData = imw_fetch_array($rsGetBreakGlassData)){
					$PHMSDate = $PHMSTime = $PHMSType = $PHMSPlanName = $PHMSDetail = $PHMSFreq = $PHMSStatus = $PHMSResonCode = $PHMSReson = $PHMSUser = "";
					if($rowGetBreakGlassData['accessDate'] >= 1){
						$PHMSDate = $rowGetBreakGlassData['accessDate'];
					}
					$PHMSTime = $rowGetBreakGlassData['accessTime'];
					$PHMSType = "Break Glass";
					$PHMSPlanName = "Restricted User - Break glass";
					$PHMSDetail = "";
					$PHMSFreq = "None";
					$PHMSStatus = "Activated";
					$PHMSResonCode = $rowGetBreakGlassData['masterResonCode'];
					$PHMSUser = $rowGetBreakGlassData['usOprater'];
					$PHMSReson = $rowGetBreakGlassData['accessReson'];
					$class = $this->intTempCounter%2==0?"bgcolor" : "";
					$break_glass_data .= '<tr height="21" class="'.$class.'">
						<td nowrap>'.$PHMSDate.'</td>
						<td nowrap>'.$PHMSTime.'</td>
						<td >'.$PHMSType.' </td>
						<td >'.$PHMSPlanName.'</td>
						<td >'.$PHMSDetail.'</td>
						<td >'.$PHMSFreq.'</td>					
						<td >'.$PHMSStatus.'</td>
						<td >'.$PHMSResonCode.'</td>
						<td >'.$PHMSReson.'</td>
						<td >'.$PHMSUser.'</td>
					</tr>';	
					$this->intTempCounter++;
				}						
			}
			imw_free_result($rsGetBreakGlassData);
		}
		return $break_glass_data;
	}
	
	//Return patient docs data
	public function get_patient_doc_data(){
		$patient_doc = '';
		$qryGetPtDocData = "SELECT DATE_FORMAT(dpr.date_time,'".get_sql_date_format()."') as accessDate,TIME_FORMAT(dpr.date_time, '%r') as accessTime,
							dpr.name as planName,CONCAT_WS(',',us.lname,us.fname) as usOprater
							FROM document_patient_rel dpr 
							LEFT JOIN users us ON us.id = dpr.operator_id
							WHERE dpr.p_id ='".$this->patient_id."'
							ORDER BY accessDate DESC, accessTime DESC";
		$rsGetPtDocData = imw_query($qryGetPtDocData);							
		if($rsGetPtDocData){
			if(imw_num_rows($rsGetPtDocData) > 0){
				while($rowGetPtDocData = imw_fetch_array($rsGetPtDocData)){
					$PHMSDate = $PHMSTime = $PHMSType = $PHMSPlanName = $PHMSDetail = $PHMSFreq = $PHMSStatus = $PHMSResonCode = $PHMSReson = $PHMSUser = "";
					if($rowGetPtDocData['accessDate'] >= 1){
						$PHMSDate = $rowGetPtDocData['accessDate'];
					}
					$PHMSTime = $rowGetPtDocData['accessTime'];
					$PHMSType = "Patient Document";
					$PHMSPlanName = $rowGetPtDocData['planName'];
					$PHMSDetail = "";
					$PHMSFreq = "None";
					$PHMSStatus = "";
					$PHMSResonCode = "";
					$PHMSUser = $rowGetPtDocData['usOprater'];
					$PHMSReson = "";
					$class = $this->intTempCounter%2==0?"bgcolor" : "";
					$patient_doc .='
					<tr height="21" class="'.$class.'">
						<td nowrap>'.$PHMSDate.'</td>
						<td nowrap>'.$PHMSTime.'</td>
						<td >'.$PHMSType.' </td>
						<td >'.$PHMSPlanName.'</td>
						<td >'.$PHMSDetail.'</td>
						<td >'.$PHMSFreq.'</td>					
						<td >'.$PHMSStatus.'</td>
						<td >'.$PHMSResonCode.'</td>
						<td >'.$PHMSReson.'</td>
						<td >'.$PHMSUser.'</td>
					</tr>';
					$this->intTempCounter++;
				}						
			}
			imw_free_result($rsGetPtDocData);
		}
		return $patient_doc;
	}
	
	//Get table data to show
	public function get_table_data(){
		$return_data = '';
		//PHMS case
		if($this->search_by == "phms" || $this->search_by == "" || $this->search_by == "0" || $this->alertFor == "PHMS" || $this->search_by == "Administered" || $this->search_by == "Declined" || $this->search_by == "Not Due"){
			$return_data .= $this->get_phms_data();
		}
		
		//Immunization case
		if(($this->search_by == "" || $this->search_by == "immu" || $this->alertFor == "Immunization" || $this->search_by == "Administered" || $this->search_by == "Declined") && ($this->search_by != "Not Due")){
			$return_data .= $this->get_immu_data();
		}
		
		//Break glass data
		$return_data .= $this->get_break_glass_data();
		
		//Patient docs case
		if($this->search_by == "Doc All") {
			$return_data .= $this->get_patient_doc_data();
		}
		
		return $return_data;
	}
	
	public function get_new_dropdown(){
		$qryGetPlanName = "SELECT alertId,site_care_plan_name FROM alert_tbl where alert_created_console = '0' ORDER BY alertId";
		$rsGetPlanName = imw_query($qryGetPlanName);
		if($rsGetPlanName){
			if(imw_num_rows($rsGetPlanName) > 0){
				$sby = isset($_POST['searchby']) ? $_POST['searchby'] : "";
				$phmsPlan = "<optgroup label=\"Plan\"><option value=\"\">All</option>";
				while($rowGetPlanName = imw_fetch_array($rsGetPlanName)){					
					if($sby == $rowGetPlanName['alertId'])$selSearchBy = ' selected="selected"'; else $selSearchBy='';
					$phmsPlan .= "<option value=\"$rowGetPlanName[alertId]\" $selSearchBy>$rowGetPlanName[site_care_plan_name]</option>";			
				}
				$phmsPlan .= "</optgroup>";	
			}
		}			
		$sby = isset($this->search_by) ? $this->search_by : "";
		$selectSearch .= "<optgroup label=\"PHMS Type\"><option value=\"\">All</option>";
		
		if($sby == "immu")$selSearchBy = ' selected="selected"'; else $selSearchBy='';
		$selectSearch .= "<option value=\"immu\" $selSearchBy>Immunization</option>";
		if($sby == "phms")$selSearchBy = ' selected="selected"'; else $selSearchBy='';
		$selectSearch .= "<option value=\"phms\" $selSearchBy>PHMS</option></optgroup>";	
		$selectSearch .= $phmsPlan;	
		
		$selectSearch .= "<optgroup label=\"Status\"><option value=\"\">All</option>";
		if($sby == "Administered")$selSearchBy = ' selected="selected"'; else $selSearchBy='';
		$selectSearch .= "<option value=\"Administered\" $selSearchBy>Administered</option>";
		if($sby == "Declined")$selSearchBy = ' selected="selected"'; else $selSearchBy='';
		$selectSearch .= "<option value=\"Declined\" $selSearchBy>Declined</option>";
		if($sby == "Not Due")$selSearchBy = ' selected="selected"'; else $selSearchBy='';
		$selectSearch .= "<option value=\"Not Due\" $selSearchBy>Not Due</option></optgroup>";	
		
		if($sby == "Doc All")$selSearchBy = ' selected="selected"'; else $selSearchBy='';
		$selectSearch .= "<optgroup label=\"Document\"><option value=\"Doc All\" $selSearchBy>All</option></optgroup>";	
		
		return $selectSearch;
	}
	
	public function set_lab_cls_alerts(){
		global $cls_alerts;
		$return_str= '';
		if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
			$OBJPatSpecificAlert = new CLSAlerts();
			$alertToDisplayAt = "admin_specific_chart_note_med_hx";
			$return_str .= $cls_alerts->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$form_id,"350px");
			$alertToDisplayAt = "patient_specific_chart_note_med_hx";
			$return_str .= $cls_alerts->getPatSpecificAlert($_SESSION['patient'],$alertToDisplayAt,"350px");
			$return_str .= $cls_alerts->autoSetDivLeftMargin("140","265");
			$return_str .= $cls_alerts->autoSetDivTopMargin("250","30");
			$return_str .= $cls_alerts->writeJS();
		}
		return $return_str;
	}
}
?>