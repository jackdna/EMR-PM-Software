<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php	
/*	
File: CLSAlerts.php
Purpose: Class for alerts 
Access Type: Include 
*/
class CLSAlerts{
	private $patientId;
	private $alertToShowAt;	
	private $alertMsg;	
	private $webRoot;	
	private $patAlertDiv;
	private $adminAlertDiv;
	private $immunizationAlertDiv;
	private $qryGetPatientAlertSelected;
	private $blPatSpecificAlertExits;	
	private $blAdminAlertExits;	
	private $blImmAlertExits;	
	public $call_from_flag;
	
	function __construct() { 
		$this->webRoot = $GLOBALS['webroot'];
		$this->blPatSpecificAlertExits = false;	
		$this->blAdminAlertExits = false;	
		$this->blImmAlertExits = false;
		$this->call_from_flag = "";
		$this->alertMsg = '';
		$this->rootDirSiteCare 		= $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/site_care_plan/";
		$this->imgDirSiteCare 		= $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/site_care_plan/";
		$this->serverPathSiteCare 	= $GLOBALS['php_server']."/data/".constant('PRACTICE_PATH')."/site_care_plan/";
	}
	
	function getPatSpecificAlert($patId,$alertToDisplayAt,$leftMargin = "445px",$topMargin = "100px",$nxtStatus = "no"){	
		$this->alertMsg = '';
		if($patId != "" && empty($patId) == false){
			$this->patientId = $patId;		
			$this->alertToShowAt = $alertToDisplayAt;
			if($_SESSION['alertShowForThisSession']){
				$strCancelIds= $_SESSION['alertShowForThisSession'];$strCancelAlertIds="";
				$arrAlertIds = explode(",",$strCancelIds);
				foreach($arrAlertIds as $key=>$id){
					if($id == "")
					unset($arrAlertIds[$key]);
				}
				$strCancelAlertIds = implode(",",array_unique($arrAlertIds));
				
				if($strCancelAlertIds){
					$qryCancelAlert=" AND alertId NOT IN(".$strCancelAlertIds.")";
				}
			}

			$phrse_display_dt = " AND (alert_disp_date LIKE '%0000%' OR alert_disp_date <= NOW() ) ";

			$qryGetPatientAlertSelected = "select alertContent as patientAlertMsgSelected,alert_to_show_under as alertShowUnder,alertId,alert_showed as alertShowed, alert_disp_date
											from alert_tbl								
											WHERE patient_id  = '".$this->patientId."' and is_deleted = '0'
											$phrse_display_dt
											$qryCancelAlert
											";
			
			$rsGetPatientAlertSelected = imw_query($qryGetPatientAlertSelected);
			$cnGetPatientAlertSelected = imw_num_rows($rsGetPatientAlertSelected);
			if($cnGetPatientAlertSelected>0){
				$cancelAlerts = $patAlertDiv = '';$iCounter=0;
				while($patientAlertRow = imw_fetch_array($rsGetPatientAlertSelected)){$iCounter++;
					if($patientAlertRow['alertShowUnder']){	
						$cancelAlerts.= $patientAlertRow['alertId'].",";
						$patAlertDiv .= "<input type=\"hidden\" name=\"reason_ids[]\" value=\"".$patientAlertRow['alertId']."\">";					
						$arrAlertToShowUnder = array();
						$arrAlertToShowed = array();
						$arrAlertToShowUnder = explode(",",$patientAlertRow['alertShowUnder']);
						$arrAlertToShowed = explode(",",$patientAlertRow['alertShowed']);
						$intChk	= 0;		
						if($arrAlertToShowUnder[0] == "1" && $arrAlertToShowed[0] != "1" && ($this->alertToShowAt =='Chart Note' || $this->alertToShowAt =="patient_specific_chart_note" || $this->alertToShowAt =="patient_specific_chart_note_med_hx")){
							$this->alertMsg.=htmlentities($patientAlertRow['patientAlertMsgSelected']).'<br>'.($iCounter<$cnGetPatientAlertSelected?'<div class="clearfix border-dashed"></div>':'');						
							$intChk++;			
						}
						elseif($arrAlertToShowUnder[1] == "1" && $arrAlertToShowed[1] != "1" && $this->alertToShowAt =='Scheduler'){
							$this->alertMsg.=htmlentities($patientAlertRow['patientAlertMsgSelected']).'<br>'.($iCounter<$cnGetPatientAlertSelected?'<div class="clearfix border-dashed"></div>':'');						
							$intChk++;						
						}	
						elseif($arrAlertToShowUnder[2] == "1" && $arrAlertToShowed[2] != "1" && $this->alertToShowAt =='Accounting'){
							$this->alertMsg.=htmlentities($patientAlertRow['patientAlertMsgSelected']).'<br>'.($iCounter<$cnGetPatientAlertSelected?'<div class="clearfix border-dashed"></div>':'');					
							$intChk++;						
						}	
						if($intChk>0){
							$sel2=imw_query("select id  from alert_tbl_reason where alertId='".$patientAlertRow['alertId']."' and patient_id='".$this->patientId."' and alert_from = '2'");
							if(imw_num_rows($sel2)==0){
								if($this->patientId<>""){
									$insertQuery1="insert into alert_tbl_reason set 
									 patient_id='".$this->patientId."',
									 operator='".$_SESSION['authId']."',
									 form_id='".$patientCurrentFormId."',
									 alert_date=now(),
									 alertId='".$patientAlertRow['alertId']."',
									 alert_from = '2',
									 reason_from='".$this->alertToShowAt."'";
									 $res1=imw_query($insertQuery1);
								}
							}
						}	
					}					
				}				
			}	
			
			
			if($this->alertMsg){		
				/*if($frm=='Accounting'){
					$top_val='2';
				}
				elseif($frm=='patient_specific_chart_note'){
					include_once("../main/main_functions.php");
					$top_val='2';
				}
				elseif($frm=='patient_specific_chart_note_med_hx'){
					include_once("../../main/main_functions.php");
					$top_val='2';
				}	
				else{
					$top_val='100';
					$left_mar='0px';
				}*/
				if($this->alertToShowAt == "Chart Note" || $this->alertToShowAt =="patient_specific_chart_note" || $this->alertToShowAt =="patient_specific_chart_note_med_hx"){
					$patientSpecificFrm = "CN";
					$top_val='100px';
					$left_mar='0px';
				}
				elseif($this->alertToShowAt == "Accounting"){
					$patientSpecificFrm = "AC";
					$top_val='100';
					$left_mar='0px';
				}
				elseif($this->alertToShowAt == "Scheduler"){
					$patientSpecificFrm = "SCH";
					$top_val='80px';
					$left_mar='0px';
				}		
				
				/*$this->patAlertDiv .= "<link type=\"text/css\"  href=\"$this->webRoot/interface/themes/style_sky_blue.css\" rel=\"stylesheet\">";
				$this->patAlertDiv .= "<link type=\"text/css\"  href=\"$this->webRoot/interface/themes/style_patient.css\" rel=\"stylesheet\">";				
				$this->patAlertDiv .= "<script type=\"text/javascript\" src=\"$this->webRoot/interface/common/script_function.js\"></script>";
				*/
				if($nxtStatus == "yes"){
					$mainDiv = "none";
				}
				else{
					$mainDiv = "block";
				}
				
				if($this->call_from_flag == "scheduler"){					
					$this->blPatSpecificAlertExits = true;					
					return wordwrap(nl2br($this->alertMsg), 50, "<br>").'^^^'.$cancelAlerts;
				}else{
					$getPatientName = imw_query("SELECT id,fname, lname, mname FROM patient_data WHERE id = '".$_SESSION['patient']."' ");
					if(imw_num_rows($getPatientName)>0) {					
						$rowGetPatientName = imw_fetch_array($getPatientName);
						$ptId = $rowGetPatientName['id'];
						$ptFName = $rowGetPatientName['fname'];
						$ptLName = $rowGetPatientName['lname'];
						$ptMName = $rowGetPatientName['mname'];		
					}	
					$alertedPatient = $ptLName.', '.$ptFName.' '.$ptMName.' - '.$ptId;
					$this->patAlertDiv .= "<script type=\"text/javascript\">function acknowledged(op,form){op = op || 0; if(document.getElementById('patSpesificDivAlert')){if(op ==1){ $(form).children('#disablePatAlertThisSession').val('yes');}else{	$(form).children('#disablePatAlertThisSession').val('');}$(form).children('#patSpesificDivAlert').css('display','none');}}</script>";
					$this->patAlertDiv .= "<form name=\"chart_alerts_patient_specific\" action=\"$this->webRoot/interface/patient_info/alerts_reason_save.php\" target=\"chart_alerts_patient_specific\" method=\"post\">";
					$this->patAlertDiv .= "<div id=\"patSpesificDivAlert\" onmouseover=\"drag_div_move(this, event)\" onMouseDown=\"drag_div_move(this, event)\"; style=\"display:$mainDiv;  z-index:2000; top:$topMargin; width:400px; left:$leftMargin; position:absolute;cursor:move;\" class=\"confirmTable3 panel panel-success\">";							
					$this->patAlertDiv .= "<div class=\"boxhead panel-heading\">Patient Alert(s) for ".$alertedPatient."</div>";
					$strTemp = $this->webRoot."/library/images/confirmYesNo.gif";
					$this->patAlertDiv .= "<div clasws=\"panel-body\" style=\"max-height:300px; overflow:hidden; overflow-y:auto;\"><div class=\"row pt10\">";
					$this->patAlertDiv .= "<div class=\"col-sm-2 text-center\"><img src=\"$strTemp\" alt=\"Confirm\"></div>";
					$this->patAlertDiv .= "<div id=\"patientAlertMsg\" class=\"col-sm-10\"><p>".wordwrap(nl2br($this->alertMsg),50,'<br>')."</p></div></div>";
					$this->patAlertDiv .= "</div>";
					$this->patAlertDiv .= "<div class=\"panel-footer text-center\" id=\"module_buttons\">";
					$this->patAlertDiv .= "<input type=\"button\" id=\"patAlertDisable\" name=\"patAlertDisable\" value=\"OK\" class=\"btn btn-success\" onClick=\"acknowledged('1', this.form); this.form.submit();\" >&nbsp;";
					$this->patAlertDiv .= "<input type=\"button\" value=\"Remove\" name=\"patAlertAcknowledged\" id=\"patAlertAcknowledged\" class=\"btn btn-danger\" onClick=\"javascript: acknowledged('', this.form); this.form.submit();\" >";			
					$this->patAlertDiv .= "</div>";
					$this->patAlertDiv .= "</div>";								
					$this->patAlertDiv .= "<input type=\"hidden\" name=\"patientSpecificFrm\" value=\"$patientSpecificFrm\">";
					$this->patAlertDiv .= "<input type=\"hidden\" id=\"disablePatAlertThisSession\" name=\"disablePatAlertThisSession\" >";
					$this->patAlertDiv .= "<input type=\"hidden\" name=\"cancel_pt_alert\" id=\"cancel_pt_alert\" value=\"$cancelAlerts\">";
					$this->patAlertDiv .= $patAlertDiv;
					$this->patAlertDiv .= "</form>";
					$this->patAlertDiv .= "<iframe name=\"chart_alerts_patient_specific\"  src=\"\" style=\"display:block;\"  frameborder=\"0\" height=\"0\" width=\"0\"></iframe>";	
					$this->blPatSpecificAlertExits = true;					
					//die($this->patAlertDiv);
					return $this->patAlertDiv;
				}
			}
			else{
				return false;
			}
		}
	}
	
	#function to get admin alert
	function getAdminAlert($patId,$alertToDisplayAt,$patientCurrentFormId = "",$leftMargin = "445px",$topMargin = "100px",$callFrom = "",$nxtStatus = "no",$arr_med_ccda=array(),$arr_allergy=array(),$arr_prob_list=array()){	
		if($patId != "" && empty($patId) == false){			
			$this->patientId = $patId;		
			$this->alertToShowAt = $alertToDisplayAt;	
			$qrySelectSCPReson = "select * from scp_reasons order by scp_id";
			$rsSelectSCPReson = imw_query($qrySelectSCPReson);
			$optionSelReson = "";
			$optionSelReson = "<option value=\"\">Select Reason</option>";
			while($rowSelectSCPReson = imw_fetch_array($rsSelectSCPReson)){
				$SCPResonId = $SCPResonCode = $SCPResonCodeDesc = $SCPValOption = "";
				$SCPResonId = $rowSelectSCPReson['scp_id'];
				$SCPResonCode = $rowSelectSCPReson['reason_code'];
				$SCPResonCodeDesc = $rowSelectSCPReson['reason_desc'];
				$SCPValOption = $SCPResonId."-".$SCPResonCode."-".$SCPResonCodeDesc;
				$optionSelReson .= "<option value=\"$SCPValOption\">$SCPResonCode</option>";
			}
			$optionSelReson .= "<option value=\"other\">Other</option>";			
			
			$msg='';		
			$htmlFormForReasons = "";		
			$qrySelDx = "select pi.dx1,pi.dx2,pi.dx3,pi.dx4,pi.dx5,pi.dx6,pi.dx7,pi.dx8,pi.dx9,
						pi.dx10,pi.dx11,pi.dx12,pi.cptCode  from superbill as sb,procedureinfo as pi where pi.idSuperBill=sb.idSuperBill and sb.postedStatus='0' and sb.del_status='0' and pi.delete_status='0' and
						sb.patientId='".$this->patientId."'";						
			$sel_dx=imw_query($qrySelDx);
			$dx_array=array();
			$cpt_array=array();
			while($row_dx=imw_fetch_array($sel_dx)){
				for($k=1;$k<=12;$k++){
					if($row_dx["dx$k"]!=""){
						$dx_array[]=$row_dx["dx$k"];
						$dx_array[]=substr($row_dx["dx$k"],0,-1)."-";
						$dx_array[]=substr($row_dx["dx$k"],0,-2)."--";
						$dx_array[]=substr($row_dx["dx$k"],0,-3)."-x-";
					}
				}
				/*if($row_dx['dx1']){
					$dx_array[]=$row_dx['dx1'];
				}
				if($row_dx['dx2']){
					$dx_array[]=$row_dx['dx2'];
				}
				if($row_dx['dx3']){
					$dx_array[]=$row_dx['dx3'];
				}
				if($row_dx['dx4']){
					$dx_array[]=$row_dx['dx4'];
				}*/
				if($row_dx['cptCode']){
					$cpt_array[]=$row_dx['cptCode'];
				}
			}
			$cpt_fee_id_arr=array();
			$dx_arr=$snomed_ct_arr = $icd9CodeArr = $icd10CodeArr = array();
			$sel_chlist=imw_query("select  diagnosis_id1,diagnosis_id2,diagnosis_id3,diagnosis_id4,diagnosis_id5,diagnosis_id6,
									diagnosis_id7,diagnosis_id8,diagnosis_id9,diagnosis_id10,diagnosis_id11,diagnosis_id12,procCode 
								  from patient_charge_list_details 
									where del_status='0' and 
								  newBalance>0 and
								  patient_id='".$this->patientId."'");
			while($row_chlist=imw_fetch_array($sel_chlist)){
				for($k=1;$k<=12;$k++){
					if($row_chlist["diagnosis_id$k"]!=""){
						$dx_array[]=$row_chlist["diagnosis_id$k"];
						$dx_array[]=substr($row_chlist["diagnosis_id$k"],0,-1)."-";
						$dx_array[]=substr($row_chlist["diagnosis_id$k"],0,-2)."--";
						$dx_array[]=substr($row_chlist["diagnosis_id$k"],0,-3)."-x-";
					}
				}
				/*if($row_chlist['diagnosis_id1']){
					$dx_array[]=$row_chlist['diagnosis_id1'];
				}
				if($row_chlist['diagnosis_id2']){
					$dx_array[]=$row_chlist['diagnosis_id2'];
				}
				if($row_chlist['diagnosis_id3']){
					$dx_array[]=$row_chlist['diagnosis_id3'];
				}
				if($row_chlist['diagnosis_id4']){
					$dx_array[]=$row_chlist['diagnosis_id4'];
				}*/
				if($row_chlist['procCode']){
					$cpt_fee_id_arr[]=$row_chlist['procCode'];
				}
			}
			//START GET DX-CODE FROM PT PROBLEM LIST 
			$dx_problem_exp_final = array();
			$sqlDxQry = imw_query("SELECT problem_name,ccda_code FROM pt_problem_list where pt_id = '".$this->patientId."' AND status='Active'") or die(imw_error());
			$problemNameArr = $ptSnomedCtCodeArr = array();
			if(imw_num_rows($sqlDxQry)>0){
				while($row_sqlDx=imw_fetch_array($sqlDxQry)){
					$pt_snomed_ct_code = $row_sqlDx['ccda_code'];
					if(strstr($row_sqlDx['problem_name'],"(")){
						$problemNameExp = explode('(',str_replace(')',"",$row_sqlDx['problem_name']));
					}else if(strstr($row_sqlDx['problem_name'],"-")){
						$problemNameExp = explode('-',$row_sqlDx['problem_name']);
					}
					if(is_array($problemNameExp) && trim(end($problemNameExp))!=''){
						$problemCode1  =trim(end($problemNameExp));
						$problemCode2  =substr(trim(end($problemNameExp)),0,-1)."-";
						$problemCode3  =substr(trim(end($problemNameExp)),0,-2)."--";
						$problemCode4  =substr(trim(end($problemNameExp)),0,-3)."-x-";

						$problemNameArr[]=$problemCode1;
						$problemNameArr[]=$problemCode2;
						$problemNameArr[]=$problemCode3;
						$problemNameArr[]=$problemCode4;
						
						$ptSnomedCtCodeArr[$problemCode1] 	= $pt_snomed_ct_code;
						$ptSnomedCtCodeArr[$problemCode2] 	= $pt_snomed_ct_code;
						$ptSnomedCtCodeArr[$problemCode3] 	= $pt_snomed_ct_code;
						$ptSnomedCtCodeArr[$problemCode4] 	= $pt_snomed_ct_code;

						$ptProblemNameArr[$problemCode1] 	= $row_sqlDx['problem_name'];
						$ptProblemNameArr[$problemCode2] 	= $row_sqlDx['problem_name'];
						$ptProblemNameArr[$problemCode3] 	= $row_sqlDx['problem_name'];
						$ptProblemNameArr[$problemCode4] 	= $row_sqlDx['problem_name'];
						
					}
				}		
			 
			}
			$dx_problem_exp_final=array_unique($problemNameArr);
			
		//	print_r($arr_prob_list);
			if(count($arr_prob_list)>0){
				$dx_problem_exp_final=$arr_prob_list;
			}
			//print_r($dx_problem_exp_final);
			//END GET DX-CODE FROM PT PROBLEM LIST

			$cpt_imp_final=implode("','",array_unique($cpt_array));
			$dx_imp_final=implode("','",array_unique(array_merge($dx_array,$dx_problem_exp_final)));
			
			$sel_cpt_id=imw_query("select cpt_fee_id from cpt_fee_tbl where cpt4_code in('$cpt_imp_final') AND delete_status = '0'");
			while($row_cpt_id=imw_fetch_array($sel_cpt_id)){ 
				$cpt_fee_id_arr[]=$row_cpt_id['cpt_fee_id'];
			}
			
			$sel_dx_id=imw_query("select diagnosis_id from diagnosis_code_tbl where dx_code in('$dx_imp_final')");
			while($row_dx_id=imw_fetch_array($sel_dx_id)){ 
				//$dx_arr[]=$row_dx_id['diagnosis_id'];
			}
			//echo "select id as diagnosis_id, icd9 as icd9_code, icd10 as icd10_code from icd10_data where icd10 in('$dx_imp_final') OR icd9 in('$dx_imp_final')<Br>";
			$sel_dx_id=imw_query("select a.id as diagnosis_id, a.icd9 as icd9_code, icd10 as icd10_code, d.snowmed_ct, icd10_desc from icd10_data a LEFT JOIN diagnosis_code_tbl d ON ( d.dx_code = a.icd9 ) where a.icd10 in('$dx_imp_final') OR a.icd9 in('$dx_imp_final')");
			while($row_dx_id=imw_fetch_array($sel_dx_id)){ 
				$dx_arr[]=$row_dx_id['diagnosis_id'];
				$snomed_ct_arr[$row_dx_id['diagnosis_id']] = $row_dx_id['snowmed_ct'];
				$icd9CodeArr[$row_dx_id['icd10_code']] = $row_dx_id['icd9_code'];
				$icd10CodeArr[$row_dx_id['diagnosis_id']] = $row_dx_id['icd10_code'];
				$icd10DescArr[$row_dx_id['diagnosis_id']] = $row_dx_id['icd10_desc'];
			}//echo '<br> dx_imp_final = '.$dx_imp_final.'<br>dx_arr = '.implode(",",$dx_arr);
			//pre($snomed_ct_arr);
			
			
			
			//dx and cpt shown end
				
			//patient information
			$sel_pt=imw_query("select sex,DOB,language,race,ethnicity from patient_data  where id='".$this->patientId."'");
			$row_pt=imw_fetch_array($sel_pt);
			$pt_sex=$row_pt['sex'];
			$pt_dob=$row_pt['DOB'];
			if(strstr($row_pt['language'],'--')){
				$pt_lang_explode=explode("--",$row_pt['language']);
				$pt_languages=trim($pt_lang_explode[0]);
			}else{
				$pt_languages=$row_pt['language'];
			}
			$pt_race=explode(",",$row_pt['race']);
			$pt_ethnicity=explode(",",$row_pt['ethnicity']);
			$pt_race_qry=$pt_ethna_qry="";
			$w=$y=0;
			if(count($pt_race)>0){
				$pt_race_qry.="( ";
				for($r=0;$r<count($pt_race);$r++){
					if($w==0){
						$pt_race_qry.="( pt_race REGEXP '[[:<:]]".$pt_race[$r]."[[:>:]]' )";			
					}else{
						$pt_race_qry.=" OR ( pt_race REGEXP '[[:<:]]".$pt_race[$r]."[[:>:]]' )";			
					}	
					$w++;
				}	
				$pt_race_qry.=" ) OR";
			}
			if(count($pt_ethnicity)>0){
				$pt_ethna_qry.="( ";
				for($t=0;$t<count($pt_ethnicity);$t++){
					if($y==0){
						$pt_ethna_qry.="( pt_ethnicity REGEXP '[[:<:]]".$pt_ethnicity[$t]."[[:>:]]' )";			
					}else{
						$pt_ethna_qry.=" OR ( pt_ethnicity REGEXP '[[:<:]]".$pt_ethnicity[$t]."[[:>:]]' )";			
					}	
					$y++;
				}	
				$pt_ethna_qry.=" ) OR ";
			}
			$age = str_replace(' Yrs.','',show_age($row_pt['DOB']));
			//patient information
			
			//START GET DIABETES TYPE FROM GENERAL HEALTH
			$diabExpFinalArr= $selfDiabTypeArr = $familyDiabTypeArr = array();
			$genHlthQry 	= "SELECT diabetes_values FROM general_medicine WHERE patient_id = '".$this->patientId."'";
			$genHlthRes 	= imw_query($genHlthQry);
			if(imw_num_rows($genHlthRes)>0) {
				$genHlthRow	= imw_fetch_array($genHlthRes);
				$diabetesValues 	= strtolower($genHlthRow["diabetes_values"]);
				list($selfDiabVal,$familyDiabVal) = explode("~|~",$diabetesValues);
				if(trim($selfDiabVal)) {
					$selfDiabTypeArr 	= explode(",",$selfDiabVal);
				}
				if(trim($familyDiabVal)) {
					//$familyDiabTypeArr 	= explode(",",$familyDiabVal); //DO NOT NEED FAMILY INFO
				}
				$diabExpFinalArr		= array_merge($selfDiabTypeArr,$familyDiabTypeArr);
				$diabExpFinalArr		= array_unique($diabExpFinalArr);
			}
			//END GET DIABETES TYPE FROM GENERAL HEALTH
			
			//medication shown start
			$sel_med=imw_query("select title,type,ag_occular_drug,allergy_status,ccda_code from lists   
				where
				pid='".$this->patientId."' AND (allergy_status='Active' OR allergy_status='Administered')");
			$med=$rxNormCodeArr = array();
			$pt_allergies_qry="";
			$f=0;
			while($row_med=imw_fetch_array($sel_med)){
				$med[]=strtolower($row_med['title']);
				$rxNormCodeArr[strtolower($row_med['title'])] = $row_med['ccda_code'];
				if($row_med['type']==7 && $row_med['allergy_status']!="Deleted"){
					if($row_med['title']){
						
						$allergy_tpe_title="";
						$allergy_tpe_title=$row_med['title'];
						if($f==0){
							$pt_allergies_qry="( pt_allergies REGEXP '[[:<:]]".$allergy_tpe_title."[[:>:]]' )";			
						}else{
							$pt_allergies_qry.=" OR ( pt_allergies REGEXP '[[:<:]]".$allergy_tpe_title."[[:>:]]' )";			
						}	
						$f++;
						
					}
				}
			}
			//===========in case of import allergies from CCDA========//
			if(count($arr_allergy)>0){$fd=0;$pt_allergies_qry="";
				foreach($arr_allergy as $allergy_tpe_title){
					if($fd==0){
						$pt_allergies_qry="( pt_allergies REGEXP '[[:<:]]".$allergy_tpe_title."[[:>:]]' )";			
					}else{
						$pt_allergies_qry.=" OR ( pt_allergies REGEXP '[[:<:]]".$allergy_tpe_title."[[:>:]]' )";			
					}	
					$fd++;
				}
			}
			//=========================================================//
			$pt_all_qry=$pt_allergies_qry_full="";
			if($f>0 || $fd>0){
				$pt_all_qry_val="( ".$pt_allergies_qry." )";
			}
			if($pt_allergies_qry){
				$pt_all_qry=$pt_all_qry_val." OR ";
			}
			//$med_exp_final=implode("','",array_unique($med));
			$med_exp_final=array_unique($med);
			//print_r($med_exp_final);
			if(count($arr_med_ccda)>0){
				//$med_exp_final=$arr_med_ccda;
				$med_exp_final=$arr_med_ccda;
			}
			//medication shown end
			//medication shown start
			$sel_main=imw_query("select id from vital_sign_master where patient_id='".$this->patientId."' AND status!=1 order by id desc");
			$row_main=imw_fetch_array($sel_main);
			$mat_id=$row_main['id'];
			
			$sel_sign=imw_query("select vsp.vital_sign_id,vsp.range_vital,vsp.unit  from 
									vital_sign_patient as vsp
									where vsp.vital_master_id ='$mat_id'");
			$sign=array();
			$range=array();
			while($row_sign=imw_fetch_array($sel_sign)){
				$sign_chk=$row_sign['vital_sign_id'];
				$sign[]=$row_sign['vital_sign_id'];
				$range[$sign_chk]=$row_sign['range_vital'];
				$range_unit[$sign_chk]=$row_sign['unit'];
			}
			//medication shown end

			//START GET LAB LOINC CODE FROM ADMIN
			$adminLabQry = "SELECT lab_radiology_name, lab_loinc FROM lab_radiology_tbl ORDER BY lab_radiology_name";
			$adminLabRes=imw_query($adminLabQry);
			if(imw_num_rows($adminLabRes)>0){
				while($adminLabRow = imw_fetch_array($adminLabRes)) {
					$adminLabName = stripslashes($adminLabRow["lab_radiology_name"]);
					$adminLabLoincCodeArr[strtolower($adminLabName)] = $adminLabRow["lab_loinc"];
				}
			}
			//END GET LAB LOINC CODE FROM ADMIN
			
			//c/d ratio shown start
			$sel_main2=imw_query("select cd_val_od ,cd_val_os  from chart_optic where patient_id='".$this->patientId."' order by optic_id  desc");
			$row_main2=imw_fetch_array($sel_main2);
			$od_text=$row_main2['cd_val_od'];
			$os_text=$row_main2['cd_val_os'];
			$ou_text = $od_text."/".$os_text;
			//c/d ratio shown end
	
			//IOP pressure shown start
			$iop_form_qry="";
			if($patientCurrentFormId){
				$iop_form_qry=" AND form_id='".$patientCurrentFormId."' ";	
			}
			$sel_main3=imw_query("select multiple_pressure  from chart_iop where patient_id='".$this->patientId."' ".$iop_form_qry." order by iop_id  desc limit 1");
			$row_main3=imw_fetch_array($sel_main3);
			$multiple_pressure=unserialize($row_main3['multiple_pressure']);
			if($multiple_pressure['multiplePressuer']['elem_appOd']){
				$iopPressure_od=$multiple_pressure['multiplePressuer']['elem_appOd'];
			}
			elseif($multiple_pressure['multiplePressuer']['elem_puffOd']){
				$iopPressure_od=$multiple_pressure['multiplePressuer']['elem_puffOd'];
			}
			elseif($multiple_pressure['multiplePressuer']['elem_appTrgtOd']){
				$iopPressure_od=$multiple_pressure['multiplePressuer']['elem_appTrgtOd'];
			}
			
			if($multiple_pressure['multiplePressuer']['elem_appOs']){
				$iopPressure_os=$multiple_pressure['multiplePressuer']['elem_appOs'];
			}
			elseif($multiple_pressure['multiplePressuer']['elem_puffOs']){
				$iopPressure_os=$multiple_pressure['multiplePressuer']['elem_puffOs'];
			}
			elseif($multiple_pressure['multiplePressuer']['elem_appTrgtOs']){
				$iopPressure_os=$multiple_pressure['multiplePressuer']['elem_appTrgtOs'];
			}
			$iopPressure_ou = "";
			$iopPressure_ou = $iopPressure_od.'/'.$iopPressure_os;
			//IOP pressure shown end
				
			//tests shown start	
			$VFTest = $HRTTest = $OCTTest = $PachyTest = $IVFATest = $FUNDUSTest = $ExternalAnteriorTest = $TopographyTest = $ophthaTest = $cellCountTest = $bScanTest = $otherTest = $labTest = false;
			$sqlGetVFTest = imw_query("SELECT vf_id FROM vf WHERE patientId = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetVFTest)>0){
				$VFTest = true;
			}
			$sqlGetHRTTest = imw_query("SELECT nfa_id FROM nfa WHERE patient_id = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetHRTTest)>0){
				$HRTTest = true;
			}	
			$sqlGetOCTTest = imw_query("SELECT oct_id FROM oct WHERE patient_id = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetOCTTest)>0){
				$OCTTest = true;
			}	
			$sqlGetPachyTest = imw_query("SELECT pachy_id FROM pachy WHERE patientId = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetPachyTest)>0){
				$PachyTest = true;
			}	
			$sqlGetIVFATest = imw_query("SELECT vf_id FROM ivfa WHERE patient_id = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetIVFATest)>0){
				$IVFATest = true;
			}	
			$sqlGetFUNDUSTest = imw_query("SELECT disc_id FROM disc WHERE patientId = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetFUNDUSTest)>0){
				$FUNDUSTest = true;
			}	
			$sqlGetExternalAnteriorTest = imw_query("SELECT disc_id FROM disc_external WHERE patientId = '".$this->patientId."' and purged = '0' AND del_status='0' ");
			if(imw_num_rows($sqlGetExternalAnteriorTest)>0){
				$ExternalAnteriorTest = true;
			}	
			$sqlGetTopographyTest = imw_query("SELECT topo_id FROM topography WHERE patientId = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetTopographyTest)>0){
				$TopographyTest = true;
			}	
			$sqlGetOphthaTest = imw_query("SELECT ophtha_id FROM ophtha WHERE patient_id = '".$this->patientId."' and purged = '0' ");
			if(imw_num_rows($sqlGetOphthaTest)>0){
				$ophthaTest = true;
			}
			$sqlGetCellCountTest = imw_query("SELECT test_cellcnt_id FROM test_cellcnt WHERE patientId = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetCellCountTest)>0){
				$cellCountTest = true;
			}
			$sqlGetAScanTest = imw_query("SELECT surgical_id FROM surgical_tbl WHERE patient_id = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetAScanTest)>0){
				$aScanTest = true;
			}
			$sqlGetBScanTest = imw_query("SELECT test_bscan_id FROM test_bscan WHERE patientId = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetBScanTest)>0){
				$bScanTest = true;
			}
			$sqlGetOtherTest = imw_query("SELECT test_other_id FROM test_other WHERE patientId = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetOtherTest)>0){
				$otherTest = true;
			}
			$sqlGetLabTest = imw_query("SELECT test_labs_id FROM test_labs WHERE patientId = '".$this->patientId."' and purged = '0' AND del_status='0'  ");
			if(imw_num_rows($sqlGetLabTest)>0){
				$labTest = true;
			}
							
			//tests shown start
				
			if($age==""){
				$age=0;
			}$qryCancelAlert="";
			/*if($_SESSION['alertShowForThisSession']){
				$strCancelIds=$_SESSION['alertShowForThisSession'];$strCancelAlertIds="";
				$strCancelAlertIds=(substr($strCancelIds,-1,1)==",")?substr($strCancelIds,0,(strlen($strCancelIds)-1)):$strCancelIds;
				if($strCancelAlertIds){
					$qryCancelAlert=" AND alertId NOT IN(".$strCancelAlertIds.")";
				}
			}*/
			if($_SESSION['alertShowForThisSession']){
				$strCancelIds= $_SESSION['alertShowForThisSession'];$strCancelAlertIds="";
				$arrAlertIds = explode(",",$strCancelIds);
				foreach($arrAlertIds as $key=>$id){
					if($id == "")
					unset($arrAlertIds[$key]);
				}
				$strCancelAlertIds = implode(",",array_unique($arrAlertIds));
				
				if($strCancelAlertIds){
					$qryCancelAlert=" AND alertId NOT IN(".$strCancelAlertIds.")";
				}
			}	
			$pt_lang_qry="";
			$alertQuery = "select alertContent,alertId,dxCodeId,cptCodeId,vitalSignId,vitalSignIdFrom,vitalSignIdTo,medication,diabetes_type,add_tests,frequency_value,frequency_type,scan_path,upload_path,reference,site_care_plan_name,txt_lab_name,txt_lab_criteria,txt_lab_result,alert_type,ageFrom,ageTo,dob_from,dob_to,cdRatio,cd_ratio_to,cd_ratio_from_expr,cd_ratio_to_expr,cdRatio_od_os,iopPressure,iopPressure_Condition,iop_pressure_to_condition,iop_pressure_to,iopPressure_od_os from alert_tbl where 
							(gender='$pt_sex' or gender='Female,Male'  or gender='') 
							and(
								(pt_language REGEXP '[[:<:]]".$pt_languages."[[:>:]]') or (pt_language='')
							)
							and(
								".$pt_ethna_qry." (pt_ethnicity='')
							)
							and(
								".$pt_race_qry." (pt_race='')
							)
							and status='1' 
							and site_care_plan_for = '0' 
														
							and (patient_id = 0) 
							and (alert_created_console = 0)
							and (
								((add_tests ='') or (add_tests !=''))
							)
							and(
								".$pt_all_qry."
								(pt_allergies='')
							)
							 ".$qryCancelAlert." 
							and enable_user_ids REGEXP '[[:<:]]".$_SESSION['authUserID']."[[:>:]]'
								
							";
			$sel=imw_query($alertQuery);						
			$msg='';
			/*
			if($callFrom == ""){
				$uploadDirSiteCare = "../../admin/console/alert/upload/";
			}
			elseif($callFrom == "chartNote"){
				$uploadDirSiteCare = "../admin/console/alert/upload/";
			}else if($callFrom=="ccd_import"){
				$uploadDirSiteCare = "../../../admin/console/alert/upload/";
			}*/
			$uploadDirSiteCare = $this->rootDirSiteCare;
			$qryScpAccess="Select id,scp_status,user_access from scp_access LIMIT 1";
			$resScpAccess=imw_query($qryScpAccess);
			if(imw_num_rows($resScpAccess)>0){
				$rowSCP=imw_fetch_assoc($resScpAccess);
				$scpId=$rowSCP['id'];
				$scpAccess=$rowSCP['scp_status'];
				$scpUsrAccess=$rowSCP['user_access'];
				$scpUsrArr=explode(",",$scpUsrAccess);
				$scrUserRestrict=false;
				if(in_array( $_SESSION['authUserID'],$scpUsrArr)){
					$scrUserRestrict=false;	
				}
			}$cancelAlerts="";
			if($sel && $scpAccess!='de_active'){
				$siteCarePlaneNameAll = "";
				while($row=imw_fetch_array($sel)){
					$dxCodeId=array();
					$cptCodeId=array();
					$med_arr=array();
					$diabetes_type_arr = array();
					$txt_lab_name_arr=array();
					$arrAlertTest = array();
					$vitalSignId="";
					$vitalSignIdFrom="";
					$vitalSignIdTo="";
					$dxCodeId="";
					$cptCodeId="";
					$chk_med="";
					$cdRatio = "";
					$cdRatioOdOs = "";
					$alertTest = "";
					$alertId=$row['alertId'];
					$chk_dxcode=$row['dxCodeId'];
					$chk_cptcode=$row['cptCodeId'];//echo '<br>'.$row['alertId'].'@@'.$row['dxCodeId'];
					$dxCodeId=explode(',',$row['dxCodeId']);
					$cptCodeId=explode(',',$row['cptCodeId']);
					$chk_med=$row['medication'];
					$med_arr=explode(',',strtolower($row['medication']));
					$chk_diabetes_type=$row['diabetes_type'];
					$diabetes_type_arr = explode(',',strtolower($row['diabetes_type']));
					$txt_lab_name=$row['txt_lab_name'];
					$txt_lab_name_arr=explode(',',strtolower($row['txt_lab_name']));
					$txt_lab_criteria=$row['txt_lab_criteria'];
					$txt_lab_result=$row['txt_lab_result'];
					$alertTest = $row['add_tests'];
					$arrAlertTest=explode(',',$alertTest);
					$strFrequencyValue = $row['frequency_value'];
					$arrFrequencyValue = $arrFreValPat = array();
					$arrFrequencyValue = explode("~~",$strFrequencyValue);
					$intFrequencyType = "";
					$intFrequencyType = $row['frequency_type'];
					$intPatFrequenctId = 0;
					$todayDate =  date("Y-m-d");
					$blNewAlertFrequency = false;
					$nextAlertToShow = "";
					$scanPathSiteCare = $uploadPathSiteCare = $siteCareReference = "";
					$scanPathSiteCare = $row['scan_path'];
					$uploadPathSiteCare = $row['upload_path'];
					$siteCareReference = $row['reference'];
					$siteCarePlaneName = $row['site_care_plan_name'];
					$siteCarePlaneNameAll .= $row['site_care_plan_name']." ,";
					$siteCareAlertType=$row['alert_type'];
					$siteCareAlertAgeFrom=$row['ageFrom'];
					$siteCareAlertAgeTo=$row['ageTo'];
					$siteCareAlertDOBFrom=$row['dob_from'];
					$siteCareAlertDOBTo=$row['dob_to'];
					$siteCareCDRatio=$row['cdRatio'];
					$siteCareCDRatioTo=$row['cd_ratio_to'];
					$siteCareCDRatioFromExpr=$row['cd_ratio_from_expr'];
					$siteCareCDRatioToExpr=$row['cd_ratio_to_expr'];
					$siteCareCDRatioSite=$row['cdRatio_od_os']; 

					$iop_pressure_from=$row['iopPressure'];
					$iop_pressure_to=$row['iop_pressure_to'];
					$iop_pressure_FromExpr=$row['iopPressure_Condition'];
					$iop_pressure_ToExpr=$row['iop_pressure_to_condition'];
					$iop_pressure_site=$row['iopPressure_od_os']; 
					$strGetAtFirst = "no";
					/*$qryChkAlertAccFrequency = "select id,frequency,alert_shown_status,date_time,next_frequency_date from patient_frequency where alert_id='".$alertId."' and patient_id='".$this->patientId."' ORDER BY id";
					$rsChkAlertAccFrequency = imw_query($qryChkAlertAccFrequency);
					if($rsChkAlertAccFrequency){						
						if(imw_num_rows($rsChkAlertAccFrequency) == 0){
							foreach($arrFrequencyValue as $key => $val){
								if($val){
									$insertQryPatFrequency = "";
									$insertQryPatFrequency ="insert into patient_frequency (alert_id,patient_id,frequency,frequency_type,alert_shown_status) values ('".$alertId."','".$this->patientId."','".$val."','".$intFrequencyType."','0')";														 
									$rsQryPatFrequenct = imw_query($insertQryPatFrequency);
									if($intPatFrequenctId == 0){
										$intPatFrequenctId = imw_insert_id();
										$blNewAlertFrequency = true;
										$strGetAtFirst = "yes";
										$nextAlertToShow = $todayDate;
									}
								}
							}
						}
						else{	
							$intTempA = 0;						
							$strGetAtFirst = "no";
							while($rowChkAlertAccFrequency = imw_fetch_array($rsChkAlertAccFrequency)){
								$patFreqId 				= $rowChkAlertAccFrequency['id'];
								$patFreqAlertShowSta 	= (int)$rowChkAlertAccFrequency['alert_shown_status'];
								$patFreqVal 			= $rowChkAlertAccFrequency['frequency'];			
								$intPatFrequenctId = $patFreqId;					
								if($patFreqAlertShowSta == 0 && empty($nextAlertToShow) == true && $rowChkAlertAccFrequency['next_frequency_date'] != "0000-00-00" && $blNewAlertFrequency == false){									
									$nextAlertToShow	= $rowChkAlertAccFrequency['next_frequency_date'];										
									$blNewAlertFrequency = true;
									if((int)$intTempA == 0){
										$strGetAtFirst = "yes";
									}
									break;
								}
								$intTempA++;
							}
						}	
						imw_free_result($rsChkAlertAccFrequency);
					}*/
					//echo $nextAlertToShow.'---'.$todayDate.'======='.$intPatFrequenctId;
					//die;
					//$nextAlertToShow 	= trim($nextAlertToShow);
					//$todayDate 			= trim($todayDate);					
					
					//$nextAlertToShow = ($nextAlertToShow) ? $nextAlertToShow : $todayDate ;
					//if($todayDate >= $nextAlertToShow && $intPatFrequenctId > 0){											
						$HaveAert = false;
						if(empty($alertTest)){
							$HaveAert = true;
						}
						else if(in_array("Anterior Photos",$arrAlertTest) && $ExternalAnteriorTest == true){
							$HaveAert = true;
						}
						elseif(in_array("External Photos",$arrAlertTest) && $ExternalAnteriorTest == true){
							$HaveAert = true;
						}
						elseif(in_array("Fundus",$arrAlertTest) && $FUNDUSTest == true){
							$HaveAert = true;
						}
						elseif(in_array("HRT",$arrAlertTest) && $HRTTest == true){
							$HaveAert = true;
						}
						elseif(in_array("IVFA",$arrAlertTest) && $IVFATest == true){
							$HaveAert = true;
						}
						elseif(in_array("OCT",$arrAlertTest) && $OCTTest == true){
							$HaveAert = true;
						}
						elseif(in_array("Pachy",$arrAlertTest) && $PachyTest == true){
							$HaveAert = true;
						}
						elseif(in_array("Topography",$arrAlertTest) && $TopographyTest == true){
							$HaveAert = true;
						}
						elseif(in_array("VF",$arrAlertTest) && $VFTest == true){
							$HaveAert = true;
						}
						elseif(in_array("Ophthalmoscopy",$arrAlertTest) && $ophthaTest == true){
							$HaveAert = true;
						}
						elseif(in_array("Cell Count",$arrAlertTest) && $cellCountTest == true){
							$HaveAert = true;
						}
						elseif(in_array("A/Scan",$arrAlertTest) && $aScanTest == true){
							$HaveAert = true;
						}
						elseif(in_array("B-Scan",$arrAlertTest) && $bScanTest == true){
							$HaveAert = true;
						}
						elseif(in_array("Other",$arrAlertTest) && $otherTest == true){
							$HaveAert = true;
						}
						elseif(in_array("Laboratories",$arrAlertTest) && $labTest == true){
							$HaveAert = true;
						}
						
						$vtial_alert=1;
						$vitalAert = false;
						$lab_name = "";
						$loinc_code = "";
						$loinc_code_exists = false;
						$qryAlertVitalSign="SELECT * FROM alert_vital_sign WHERE alert_id='".$alertId."'";
						$resAlertVitalSign=imw_query($qryAlertVitalSign);
						if(imw_num_rows($resAlertVitalSign)>0){
							while($rowAlertVitalSign=imw_fetch_assoc($resAlertVitalSign)){
							$vitalSignId=$rowAlertVitalSign['vital_sign_id'];
							$vitalSignIdFrom=$rowAlertVitalSign['vital_sign_id_from'];
							$vitalSignIdTo=$rowAlertVitalSign['vital_sign_id_to'];
							$vitalSignIdUnit=$rowAlertVitalSign['unit'];
							$vitalSignFromExpr=$rowAlertVitalSign['vital_sign_from_expr'];
							$vitalSignExpr=$rowAlertVitalSign['vital_sign_to_expr'];
							$expr_cond="";
							
								$vitalAert=false;
								if($vitalSignIdFrom=='' && $vitalSignIdTo==''){
									$vitalAert = true;
								}else if($range[$vitalSignId]>=$vitalSignIdFrom && $range[$vitalSignId]<=$vitalSignIdTo && ($vitalSignFromExpr=='greater_equal' && $vitalSignExpr=="less_equal")){
									$vitalAert = true;
								}else if($range[$vitalSignId]>$vitalSignIdFrom && $range[$vitalSignId]<$vitalSignIdTo && ($vitalSignFromExpr=='greater' && $vitalSignExpr=="less")){
									$vitalAert = true;
								}else if($range[$vitalSignId]>$vitalSignIdFrom && $range[$vitalSignId]<=$vitalSignIdTo && ($vitalSignFromExpr=='greater' && $vitalSignExpr=="less_equal")){
									$vitalAert = true;
								}else if($range[$vitalSignId]>=$vitalSignIdFrom && $range[$vitalSignId]<$vitalSignIdTo && ($vitalSignFromExpr=='greater_equal' && $vitalSignExpr=="less")){
									$vitalAert = true;
								}else if($range[$vitalSignId]<$vitalSignIdFrom && $range[$vitalSignId]<$vitalSignIdTo && ($vitalSignFromExpr=='less' && $vitalSignExpr=="less")){
									$vitalAert = true;
								}else if($range[$vitalSignId]>$vitalSignIdFrom && $range[$vitalSignId]>$vitalSignIdTo && ($vitalSignFromExpr=='greater' && $vitalSignExpr=="greater")){
									$vitalAert = true;
								}else if($range[$vitalSignId]>$vitalSignIdFrom  && ($vitalSignFromExpr=='greater' && $vitalSignIdTo=="")){//$varrrr=$range[$vitalSignId]." less ram ".$vitalSignIdFrom;
									$vitalAert = true;
								}else if($range[$vitalSignId]<$vitalSignIdTo && ($vitalSignExpr=="less" && $vitalSignIdFrom=="")){
									$vitalAert = true;
								}else if($range[$vitalSignId]<$vitalSignIdFrom  && ($vitalSignFromExpr=='less' && $vitalSignIdTo=="")){
									$vitalAert = true;
								}else if($range[$vitalSignId]>$vitalSignIdTo && ($vitalSignExpr=="greater" && $vitalSignIdFrom=="")){
									$vitalAert = true;
								}else if($range[$vitalSignId]>=$vitalSignIdFrom  && ($vitalSignFromExpr=='greater_equal' && $vitalSignIdTo=="")){
									$vitalAert = true;
								}else if($range[$vitalSignId]<=$vitalSignIdTo && ($vitalSignExpr=="less_equal" && $vitalSignIdFrom=="")){
									$vitalAert = true;
								}else if($range[$vitalSignId]==$vitalSignIdFrom  && ($vitalSignFromExpr=='equalsto' && $vitalSignIdTo=="")){
									$vitalAert = true;
								}else if($range[$vitalSignId]==$vitalSignIdTo  && ($vitalSignExpr=='equalsto' && $vitalSignIdFrom=="")){
									$vitalAert = true;
								}
								if($vitalAert==false){break;}
							}
						}else{
							$vitalAert = true;	
						}
						if($vitalAert==false){$vtial_alert=0;}
						//=======================CD RATIO======================================//
						$ouCase=1;
						if(($siteCareCDRatio || $siteCareCDRatioTo) && ($vtial_alert==1)){$vitalAert=false;if($siteCareCDRatioSite==""){$siteCareCDRatioSite="OU";}
							if(($siteCareCDRatioSite=="OD" || $siteCareCDRatioSite=="OU") &&($od_text)){$vitalAert=false;$ouCase=0;
								if($od_text>=$siteCareCDRatio && $od_text<=$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater_equal' && $siteCareCDRatioToExpr=="less_equal")){
										$vitalAert = true;$ouCase=1;
								}else if($od_text>$siteCareCDRatio && $od_text<$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater' && $siteCareCDRatioToExpr=="less")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text>$siteCareCDRatio && $od_text<=$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater' && $siteCareCDRatioToExpr=="less_equal")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text>=$siteCareCDRatio && $od_text<$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater_equal' && $siteCareCDRatioToExpr=="less")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text<$siteCareCDRatio && $od_text<$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='less' && $siteCareCDRatioToExpr=="less")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text>$siteCareCDRatio && $od_text>$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater' && $siteCareCDRatioToExpr=="greater")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text>$siteCareCDRatio  && ($siteCareCDRatioFromExpr=='greater' && $siteCareCDRatioTo=="")){//$varrrr=$od_text." less ram ".$siteCareCDRatio;
									$vitalAert = true;$ouCase=1;
								}else if($od_text<$siteCareCDRatioTo && ($siteCareCDRatioToExpr=="less" && $siteCareCDRatio=="")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text<$siteCareCDRatio  && ($siteCareCDRatioFromExpr=='less' && $siteCareCDRatioTo=="")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text>$siteCareCDRatioTo && ($siteCareCDRatioToExpr=="greater" && $siteCareCDRatio=="")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text>=$siteCareCDRatio  && ($siteCareCDRatioFromExpr=='greater_equal' && $siteCareCDRatioTo=="")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text<=$siteCareCDRatioTo && ($siteCareCDRatioToExpr=="less_equal" && $siteCareCDRatio=="")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text==$siteCareCDRatio  && ($siteCareCDRatioFromExpr=='equalsto' && $siteCareCDRatioTo=="")){
									$vitalAert = true;$ouCase=1;
								}else if($od_text==$siteCareCDRatioTo  && ($siteCareCDRatioToExpr=='equalsto' && $siteCareCDRatio=="")){
									$vitalAert = true;$ouCase=1;
								}
							}
							if(($siteCareCDRatioSite=="OS" || $siteCareCDRatioSite=="OU")&&($os_text)){$vitalAert = false;$ouCase=0;
								if($os_text>=$siteCareCDRatio && $os_text<=$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater_equal' && $siteCareCDRatioToExpr=="less_equal")){
										$vitalAert = true;$ouCase=1;
								}else if($os_text>$siteCareCDRatio && $os_text<=$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater' && $siteCareCDRatioToExpr=="less_equal")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text>=$siteCareCDRatio && $os_text<$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater_equal' && $siteCareCDRatioToExpr=="less")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text>$siteCareCDRatio && $os_text<$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater' && $siteCareCDRatioToExpr=="less")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text<$siteCareCDRatio && $os_text<$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='less' && $siteCareCDRatioToExpr=="less")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text>$siteCareCDRatio && $os_text>$siteCareCDRatioTo && ($siteCareCDRatioFromExpr=='greater' && $siteCareCDRatioToExpr=="greater")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text>$siteCareCDRatio  && ($siteCareCDRatioFromExpr=='greater' && $siteCareCDRatioTo=="")){//$varrrr=$os_text." less ram ".$siteCareCDRatio;
									$vitalAert = true;$ouCase=1;
								}else if($os_text<$siteCareCDRatioTo && ($siteCareCDRatioToExpr=="less" && $siteCareCDRatio=="")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text<$siteCareCDRatio  && ($siteCareCDRatioFromExpr=='less' && $siteCareCDRatioTo=="")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text>$siteCareCDRatioTo && ($siteCareCDRatioToExpr=="greater" && $siteCareCDRatio=="")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text>=$siteCareCDRatio  && ($siteCareCDRatioFromExpr=='greater_equal' && $siteCareCDRatioTo=="")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text<=$siteCareCDRatioTo && ($siteCareCDRatioToExpr=="less_equal" && $siteCareCDRatio=="")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text==$siteCareCDRatio  && ($siteCareCDRatioFromExpr=='equalsto' && $siteCareCDRatioTo=="")){
									$vitalAert = true;$ouCase=1;
								}else if($os_text==$siteCareCDRatioTo  && ($siteCareCDRatioToExpr=='equalsto' && $siteCareCDRatio=="")){
									$vitalAert = true;$ouCase=1;
								}
							}
						}
						if($vitalAert==false){$vtial_alert=0;}
						
						//===================================================================================================//
						
						
						//=======================IOP Pressure======================================//
						$ouIopCase=1;$iop_pressure_OD_OS_os=$iop_pressure_OD_OS_od=0;$iop_od_case=1;$iop_os_case=1;
						if($iop_pressure_from || $iop_pressure_to && ($vtial_alert==1)){$ouIopCase=0;
							$vitalAert=false;if(trim($iop_pressure_site)==""){$iop_pressure_site="OU";}
							if(($iop_pressure_site=="OD" || $iop_pressure_site=="OU" || $iop_pressure_site=="OD_OS" ) && ($iopPressure_od)){$vitalAert=false;$iop_od_case=0;
								if($iopPressure_od>=$iop_pressure_from && $iopPressure_od<=$iop_pressure_to && ($iop_pressure_FromExpr=='greater_equal' && $iop_pressure_ToExpr=="less_equal")){
										$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od>$iop_pressure_from && $iopPressure_od<=$iop_pressure_to && ($iop_pressure_FromExpr=='greater' && $iop_pressure_ToExpr=="less_equal")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od>=$iop_pressure_from && $iopPressure_od<$iop_pressure_to && ($iop_pressure_FromExpr=='greater_equal' && $iop_pressure_ToExpr=="less")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od>$iop_pressure_from && $iopPressure_od<$iop_pressure_to && ($iop_pressure_FromExpr=='greater' && $iop_pressure_ToExpr=="less")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od<$iop_pressure_from && $iopPressure_od<$iop_pressure_to && ($iop_pressure_FromExpr=='less' && $iop_pressure_ToExpr=="less")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od>$iop_pressure_from && $iopPressure_od>$iop_pressure_to && ($iop_pressure_FromExpr=='greater' && $iop_pressure_ToExpr=="greater")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od>$iop_pressure_from  && ($iop_pressure_FromExpr=='greater' && $iop_pressure_to=="")){//$varrrr=$od_text." less ram ".$siteCareCDRatio;
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od<$iop_pressure_to && ($iop_pressure_ToExpr=="less" && $iop_pressure_from=="")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od<$iop_pressure_from  && ($iop_pressure_FromExpr=='less' && $iop_pressure_to=="")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od>$iop_pressure_to && ($iop_pressure_ToExpr=="greater" && $iop_pressure_from=="")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od>=$iop_pressure_from  && ($iop_pressure_FromExpr=='greater_equal' && $iop_pressure_to=="")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od<=$iop_pressure_to && ($iop_pressure_ToExpr=="less_equal" && $iop_pressure_from=="")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od==$iop_pressure_from  && ($iop_pressure_FromExpr=='equalsto' && $iop_pressure_to=="")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}else if($iopPressure_od==$iop_pressure_to  && ($iop_pressure_ToExpr=='equalsto' && $iop_pressure_from=="")){
									$vitalAert = true;$ouIopCase=1;$iop_od_case=1;
								}
							}
							if(($iop_pressure_site=="OS" || $iop_pressure_site=="OU" || $iop_pressure_site=="OD_OS") && ($iopPressure_os)){$vitalAert=false;$ouIopCase=0;$iop_os_case=0;
								if($iopPressure_os>=$iop_pressure_from && $iopPressure_os<=$iop_pressure_to && ($iop_pressure_FromExpr=='greater_equal' && $iop_pressure_ToExpr=="less_equal")){
										$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os>$iop_pressure_from && $iopPressure_os<=$iop_pressure_to && ($iop_pressure_FromExpr=='greater' && $iop_pressure_ToExpr=="less_equal")){$iopppppp="yes ".$iop_pressure_to;
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os>=$iop_pressure_from && $iopPressure_os<$iop_pressure_to && ($iop_pressure_FromExpr=='greater_equal' && $iop_pressure_ToExpr=="less")){$iopppppp="yes ".$iop_pressure_to;
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os>$iop_pressure_from && $iopPressure_os<$iop_pressure_to && ($iop_pressure_FromExpr=='greater' && $iop_pressure_ToExpr=="less")){$iopppppp="yes ".$iop_pressure_to;
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os<$iop_pressure_from && $iopPressure_os<$iop_pressure_to && ($iop_pressure_FromExpr=='less' && $iop_pressure_ToExpr=="less")){
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os>$iop_pressure_from && $iopPressure_os>$iop_pressure_to && ($iop_pressure_FromExpr=='greater' && $iop_pressure_ToExpr=="greater")){
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os>$iop_pressure_from  && ($iop_pressure_FromExpr=='greater' && $iop_pressure_to=="")){//$varrrr=$os_text." less ram ".$siteCareCDRatio;
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os<$iop_pressure_to && ($iop_pressure_ToExpr=="less" && $iop_pressure_from=="")){
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os<$iop_pressure_from  && ($iop_pressure_FromExpr=='less' && $iop_pressure_to=="")){
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os>$iop_pressure_to && ($iop_pressure_ToExpr=="greater" && $iop_pressure_from=="")){
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os>=$iop_pressure_from  && ($iop_pressure_FromExpr=='greater_equal' && $iop_pressure_to=="")){
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os<=$iop_pressure_to && ($iop_pressure_ToExpr=="less_equal" && $iop_pressure_from=="")){
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os==$iop_pressure_from  && ($iop_pressure_FromExpr=='equalsto' && $iop_pressure_to=="")){
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}else if($iopPressure_os==$iop_pressure_to  && ($iop_pressure_ToExpr=='equalsto' && $iop_pressure_from=="")){
									$vitalAert = true;$ouIopCase=1;$iop_os_case=1;
								}
							}
						}
						
						if($vitalAert==false){$vtial_alert=0;}
						
						//====================================================================================================//
						
						if($vtial_alert==1){
							if($siteCareAlertAgeFrom && $siteCareAlertAgeTo ){
								$vitalAert=false;
								if($age >=$siteCareAlertAgeFrom && $age<=$siteCareAlertAgeTo){
									$vitalAert=true;
								}
							}else if($siteCareAlertAgeFrom && $siteCareAlertAgeFrom>0){
								
								$vitalAert=false;
								if($age>= $siteCareAlertAgeFrom ){
									$vitalAert=true;
								}
							}else if($siteCareAlertAgeTo && $siteCareAlertAgeTo>0){$ck="to";
								$vitalAert=false;
								if($age<=$siteCareAlertAgeTo ){
									$vitalAert=true;
								}
							}
						}
						if($vitalAert==false){$vtial_alert=0;}
						if($vtial_alert==1){
							if($siteCareAlertDOBFrom && $siteCareAlertDOBTo ){
								$vitalAert=false;
								if($pt_dob >=$siteCareAlertDOBFrom && $pt_dob<=$siteCareAlertDOBTo){
									$vitalAert=true;
								}
							}else if($siteCareAlertDOBFrom ){
								
								$vitalAert=false;
								if($pt_dob>= $siteCareAlertDOBFrom ){
									$vitalAert=true;
								}
							}else if($siteCareAlertDOBTo){
								$vitalAert=false;
								if($pt_dob<=$siteCareAlertDOBTo){
									$vitalAert=true;
								}
							}
						}
						
						if($ouCase!=1 && $siteCareCDRatioSite=="OU"){$vtial_alert=0;}
						if($iop_pressure_site=="OU" && $ouIopCase!=1){$vtial_alert=0;}
						if($iop_pressure_site=="OU"){
							if($iop_os_case==1 && $iop_od_case==1){
								$vtial_alert=1;
							}else{$vtial_alert=0;}
						}else if($iop_pressure_site=="OD_OS"){
							if($iop_os_case==1 || $iop_od_case==1){
								$vtial_alert=1;
							}else{$vtial_alert=0;}
						}
						//$ramm=" ".$siteCareCDRatioTo." ".$vtial_alert;
						
						
						//LAB TESTS
						if($vtial_alert==1){
							$qryLabAlert="SELECT * from alert_labs where alert_id='".$alertId."'";
							$resLabAlert=imw_query($qryLabAlert);
							if(imw_num_rows($resLabAlert)>0){
								$sqlLabTest = imw_query("select group_concat(lab_test_data_id) AS lab_test_data_id from lab_test_data where lab_status != '3' and lab_patient_id = '".$this->patientId."'");
								if(imw_num_rows($sqlLabTest)>0){$labTestNameArr = array();
									$row_labTest=imw_fetch_array($sqlLabTest);
										$lab_id=$row_labTest['lab_test_data_id'];
										while($rowLabAlert=imw_fetch_assoc($resLabAlert)){
											if($rowLabAlert['lab_name']){
												$lab_txt_whr="";
												if($rowLabAlert['from_creteria'] && $rowLabAlert['from_val']){
													$vitalAert=false;
													$txt_lab_result=$rowLabAlert['from_val'];
													if($rowLabAlert['from_creteria']=='greater'){
														$lab_txt_whr=" and lres.result > $txt_lab_result";
													}else if($rowLabAlert['from_creteria']=='greater_equal'){
														$lab_txt_whr=" and lres.result >= $txt_lab_result";
													}else if($rowLabAlert['from_creteria']=='equalsto'){
														$lab_txt_whr=" and lres.result=$txt_lab_result";
													}else if($rowLabAlert['from_creteria']=='less_equal'){
														$lab_txt_whr=" and lres.result<=$txt_lab_result";
													}else if($rowLabAlert['from_creteria']=='less'){
														$lab_txt_whr=" and lres.result<$txt_lab_result";
													}	
												}
												$lab_txt_to_whr="";
												if($rowLabAlert['to_creteria'] && $rowLabAlert['to_val']){
													$vitalAert=false;
													$txt_lab_result_to=$rowLabAlert['to_val'];
													if($rowLabAlert['to_creteria']=='greater'){
														$lab_txt_to_whr=" and lres.result > $txt_lab_result_to";
													}else if($rowLabAlert['to_creteria']=='greater_equal'){
														$lab_txt_to_whr=" and lres.result >= $txt_lab_result_to";
													}else if($rowLabAlert['to_creteria']=='equalsto'){
														$lab_txt_to_whr=" and lres.result=$txt_lab_result_to";
													}else if($rowLabAlert['to_creteria']=='less_equal'){
														$lab_txt_to_whr=" and lres.result<=$txt_lab_result_to";
													}else if($rowLabAlert['to_creteria']=='less'){
														$lab_txt_to_whr=" and lres.result<$txt_lab_result_to";
													}	
												}
												$qryLab="SELECT service, loinc from lab_observation_requested WHERE service='".$rowLabAlert['lab_name']."' AND del_status!=1  AND lab_test_id IN (".$lab_id.")";
												if($lab_txt_to_whr || $lab_txt_whr){
													$qryLab="SELECT lor.service  as service, lor.loinc from lab_observation_requested as lor inner join lab_observation_result as lres on lor.lab_test_id=lres.lab_test_id WHERE lor.service='".$rowLabAlert['lab_name']."' AND lres.del_status!=1 AND lor.lab_test_id IN (".$lab_id.") ".$lab_txt_whr.$lab_txt_to_whr;	
												}
												$resLab=imw_query($qryLab);
												if(imw_num_rows($resLab)>0){
													while($rowLab = imw_fetch_array($resLab)) {
														if($rowLab["loinc"] && $loinc_code_exists==false) {
															$lab_name = stripslashes($rowLab["service"]);	
															$loinc_code = $rowLab["loinc"];
															$loinc_code_exists = true;
														}
														if(!$lab_name) {
															$lab_name = stripslashes($rowLab["service"]);	
														}
													}
													$vitalAert=true;break;	
												}
											}
										}
											
								}
							}
						}
						
						if($vtial_alert!=1){$vitalAert=false;}
						
						$qryCheckDecline="SELECT id from patient_frequency WHERE alert_id='".$alertId."' AND alert_shown_status=1";
						$resCheckDecline=imw_query($qryCheckDecline);
						if(imw_num_rows($resCheckDecline)>0){
							$vitalAert=false;
						}
						/*echo "<hr>";
						 print_r($med_arr);echo "<hr>";
						 print_r($med_exp_final);echo "<hr>";;
						 print_r($arr_med_ccda);die();*/
						//$vitalAert=true;
						$patCbkCPT = $repCptCode = "";
						/*if($chk_cptcode){
							$repCptCode = str_replace(",","','",$chk_cptcode);
							$qryGetChkbxForCPT = "select cpt_prac_code,cpt_fee_id,cpt_desc from cpt_fee_tbl where cpt_fee_id IN('".$repCptCode."') order by cpt_prac_code asc";
							$rsGetChkbxForCPT = imw_query($qryGetChkbxForCPT);
							$counter = 0;
							if(imw_num_rows($rsGetChkbxForCPT) > 0){
								$patCbkCPT = "<div style=\"width:300px;\">";
								while($rowGetChkbxForCPT = imw_fetch_array($rsGetChkbxForCPT)) {
									$cptDesc = $strCbk = "";
									$cptDesc = $rowGetChkbxForCPT['cpt_desc'];
									$strCbk = "<div align=\"left\"><input type=\"checkbox\" id=\"cbkCPT$counter\" checked name=\"cbkCPT[]\" value='1'\">".$cptDesc."</div>";
									$patCbkCPT .= $strCbk;
									$counter++;
								}
								$patCbkCPT .= "</div>";
							}
						}*/	
						

						
						//LAB TESTS						
						$i=1;$txt_lab_name="";
						//$qryAlertSel1 = "select id  from alert_tbl_reason where  alertId='$alertId' and patient_id='".$this->patientId."' and reason<>'' and patient_frequency_id = '".$intPatFrequenctId."' and alert_from = '1'";
						//$sel1=imw_query($qryAlertSel1);
						//if(imw_num_rows($sel1)==0){					
							$chk_blank_med =true;
							if($this->alertToShowAt=="admin_specific_chart_note_med_hx_cpoe"){
								$chk_blank_med = false;
							}
							if((array_intersect($cptCodeId,$cpt_fee_id_arr) || ($chk_cptcode==""))){
								if((array_intersect($dxCodeId,$dx_arr) || ($chk_dxcode==""))){
									if((array_intersect($med_arr,$med_exp_final) || ($chk_med=="" && $chk_blank_med==true))){
										if((array_intersect($diabetes_type_arr,$diabExpFinalArr) || ($chk_diabetes_type==""))){	
											if(($txt_lab_name=="")){
												if((in_array($vitalSignId,$sign) || ($vitalSignId==''))){									
													if($vitalAert==true && $HaveAert == true){	
														if($this->alertToShowAt==""){
															$this->alertToShowAt="Scheduler";
														}
														##############
														
														//START CODE FOR INFO BUTTON
														$medInfoButtonArr = array();
														if((array_intersect($med_arr,$med_exp_final) && ($chk_med!=""))){
															$med_temp_arr = array_intersect($med_arr,$med_exp_final);
															$medName = strtolower($med_temp_arr[0]);
															if($rxNormCodeArr[$medName]) {
																$medInfoButtonArr[ucfirst($medName)] = '<i class="glyphicon glyphicon-info-sign pointer font-18" title="Medication Info Button" id="info_prob_'.$medName.'" onclick="javascript: var medInfoWin = window.open(\'http://apps2.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c='.$rxNormCodeArr[$medName].'&mainSearchCriteria.v.cs=2.16.840.1.113883.6.88&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en\',\'MedicationList\',\'height=700,width=1000,top=50,left=50,scrollbars=yes\');medInfoWin.focus();"></i>';	
															}
														}
														//pre($dx_arr);
														$snomedCode = $dxId = $icd10Code = $icd9Code = "";
														if((array_intersect($dxCodeId,$dx_arr) || ($chk_dxcode!=""))){
															$dxTempArr 		= array_intersect($dxCodeId,$dx_arr);
															$dxId 			= $dxTempArr[0];
															$icd10Code 		= $icd10CodeArr[$dxId];
															$snomedCode 	= trim($ptSnomedCtCodeArr[$icd10Code]);
															$ptProblemName 	= trim($ptProblemNameArr[$icd10Code]);
															
															if(!$snomedCode) {
																$icd9Code 		= $icd9CodeArr[$icd10Code];	
																$snomedCode 	= trim($ptSnomedCtCodeArr[$icd9Code]);
																$ptProblemName 	= trim($ptProblemNameArr[$icd9Code]);
															}
															if(!$snomedCode) {
																$snomedCode = trim($snomed_ct_arr[$dxId]);
																$ptProblemName 	= trim($icd10DescArr[$dxId]);
															}
															$linkProblemCode = $icd10Code;
															$linkCriteriaVCSNum = "90";
															if($snomedCode) {
																$linkProblemCode = $snomedCode;
																$linkCriteriaVCSNum = "96";
															}
															if($linkProblemCode) {
																$medInfoButtonArr[$ptProblemName] = '<i class="glyphicon glyphicon-info-sign pointer font-18"  title="Problem Info Button" id="info_prob_'.$linkProblemCode.'" onclick="javascript: var probInfoWin = window.open(\'https://apps.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c='.$linkProblemCode.'&mainSearchCriteria.v.cs=2.16.840.1.113883.6.'.$linkCriteriaVCSNum.'&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en\',\'ProblemListInfo\',\'height=700,width=1000,top=50,left=50,scrollbars=yes\');probInfoWin.focus();"></i>';	
															}
														}
														//pre($medInfoButtonArr);
														
														$loinc_code = trim($loinc_code);
														if(!$loinc_code) {
															$loinc_code = $adminLabLoincCodeArr[strtolower($lab_name)];	
														}
														if($loinc_code) {
															$medInfoButtonArr[$lab_name] = '<i class="glyphicon glyphicon-info-sign pointer font-18"  title="Lab Info Button" id="info_lab_'.$loinc_code.'" onclick="javascript: var labInfoWin = window.open(\'https://apps.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c='.$loinc_code.'&mainSearchCriteria.v.cs=2.16.840.1.113883.6.1&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en\',\'LabListInfo\',\'height=700,width=1000,top=50,left=50,scrollbars=yes\');labInfoWin.focus();"></i>';	
														}
														//END CODE FOR INFO BUTTON														
														
														$qryChkAlertAccFrequency = "select id,frequency,alert_shown_status,date_time,next_frequency_date from patient_frequency where alert_id='".$alertId."' and patient_id='".$this->patientId."' ORDER BY id";
														$rsChkAlertAccFrequency = imw_query($qryChkAlertAccFrequency);
														if($rsChkAlertAccFrequency){						
															if(imw_num_rows($rsChkAlertAccFrequency) == 0){
																foreach($arrFrequencyValue as $key => $val){
																	if($val){
																		$insertQryPatFrequency = "";
																		$insertQryPatFrequency ="insert into patient_frequency (alert_id,patient_id,frequency,frequency_type,alert_shown_status) values ('".$alertId."','".$this->patientId."','".$val."','".$intFrequencyType."','0')";														 
																		$rsQryPatFrequenct = imw_query($insertQryPatFrequency);
																		if($intPatFrequenctId == 0){
																			$intPatFrequenctId = imw_insert_id();
																			$blNewAlertFrequency = true;
																			$strGetAtFirst = "yes";
																			$nextAlertToShow = $todayDate;
																		}
																	}
																}
															}
															else{	
																$intTempA = 0;						
																$strGetAtFirst = "no";
																while($rowChkAlertAccFrequency = imw_fetch_array($rsChkAlertAccFrequency)){																
																	$patFreqId 				= $rowChkAlertAccFrequency['id'];
																	$patFreqAlertShowSta 	= (int)$rowChkAlertAccFrequency['alert_shown_status'];
																	$patFreqVal 			= $rowChkAlertAccFrequency['frequency'];			
																	$intPatFrequenctId = $patFreqId;					
																	if($patFreqAlertShowSta == 0 && empty($nextAlertToShow) == true && $blNewAlertFrequency == false){									
																		$nextAlertToShow	= $rowChkAlertAccFrequency['next_frequency_date'];		
																		$nextAlertToShow1	= $rowChkAlertAccFrequency['next_frequency_date'];										
																		$blNewAlertFrequency = true;
																		if((int)$intTempA == 0){
																			$strGetAtFirst = "yes";
																		}
																		break;
																	}
																	$intTempA++;
																}
															}	
															imw_free_result($rsChkAlertAccFrequency);
														}
														//echo "<pre>";
														//print($nextAlertToShow).'pppp';
														//print($nextAlertToShow1);
														
														if(!$nextAlertToShow) { $nextAlertToShow=$nextAlertToShow1;}
														$nextAlertToShow 	= trim($nextAlertToShow);
														$todayDate 			= trim($todayDate);			
														$nextAlertToShow1 = str_replace("-","",$nextAlertToShow);		
														$todayDate1 = str_replace("-","",$todayDate);		
														//echo $todayDate."```".$nextAlertToShow."-----".$intPatFrequenctId;
														//echo "<br>";
														//$nextAlertToShow = ($nextAlertToShow) ? $nextAlertToShow : $todayDate ;
														if($todayDate1 >= $nextAlertToShow1 && $intPatFrequenctId > 0 && $nextAlertToShow1 != ""){
															$qryAlertSel1 = "select id  from alert_tbl_reason where  alertId='$alertId' and patient_id='".$this->patientId."' and reason<>'' and patient_frequency_id = '".$intPatFrequenctId."' and alert_from = '1'";
															$sel1=imw_query($qryAlertSel1);
															if(imw_num_rows($sel1)==0){
																###############3
																if($blNewAlertFrequency == true){
																	$qryNew = "select id  from alert_tbl_reason where  alertId='$alertId' and patient_id='".$this->patientId."' and patient_frequency_id = '".$intPatFrequenctId."' and alert_from = '1'";
																	$sel2=imw_query($qryNew);
																	if(imw_num_rows($sel2)==0){
																		if($this->patientId != ""){
																			$insertQuery1="insert into alert_tbl_reason set 
																			 patient_id='".$this->patientId."',
																			 operator='".$_SESSION['authId']."',
																			 form_id='".$patientCurrentFormId."',
																			 alert_date=now(),
																			 alertId='".$alertId."',
																			 patient_frequency_id = '".$intPatFrequenctId."',
																			 alert_from = '1',
																			 reason_from='".$this->alertToShowAt."'";
																			$res1=imw_query($insertQuery1);															
																		}
																	}						
																}
															}
															/*$msg.=$row['alertContent'].'<br>';												
															$htmlFormForReasons.= "<tr class=\"confirmBackground\">";
															$htmlFormForReasons.= "<input type=\"hidden\" name=\"reason_ids[]\" value=\"".$alertId."\">";
															$htmlFormForReasons.= "<td class=\"text_10b\" valign=\"top\">Reasons for \"".wordwrap($row["alertContent"],50,"<br>")."\"</td>";
															$htmlFormForReasons.= "<td class=\"text_10b\" valign=\"top\">";										
															$htmlFormForReasons.= "<select name=\"reason_sel_".$alertId."\" id=\"reason_sel_".$alertId."\" class=\"text_10\" onChange=\"javascript:loadItsDesc(this,document.getElementById('reason_text_$alertId'));\" >";										
															$htmlFormForReasons.= $optionSelReson;
															$htmlFormForReasons.= "</select>";										
															$htmlFormForReasons.= "<input type=\"text\" style=\"visibility:hidden;\" name=\"reason_text_".$alertId."\" id=\"reason_text_".$alertId."\" class=\"input_text_10\">";
															$htmlFormForReasons.= "</td>";
															$htmlFormForReasons.= "</tr>";												
															*/
															$msgTemp = "";					
															$msgTemp = trim($var_sap.$row['alertContent']);	
															//$medInfoButton = trim(implode(" ",$medInfoButtonArr));											
															$msg.= ("<tr><td style=\"vertical-align:top;padding-left:20px; \">-</td><td><b>".$msgTemp."&nbsp;&nbsp;<b></td></tr>");
															//$msg.= "<br/>";
															$msg.= $patCbkCPT;
															$cancelAlerts.=$alertId.",";
															$siteCareDoc1 = $siteCareDoc2 = $siteCareRefPath = $medInfoButtonShow = "";
															if(count($medInfoButtonArr)>0) {
																$medInfoButtonShow.="<tr>
																						<td colspan=\"2\" style=\"vertical-align:top;padding-left:35px; padding-bottom:5px;\"><b>Info Button - </b>
																							<table cellspacing=\"2\" cellpadding=\"0\" class=\"text_10b\">"; 
																foreach($medInfoButtonArr as $medInfoButtonKey => $medInfoButtonVal) {
																	$medInfoButtonShow .= "		<tr>
																									<td style=\"vertical-align:top;padding-left:35px; padding-bottom:5px;\"><b>&bullet;&nbsp;".$medInfoButtonKey."<b>&nbsp;".$medInfoButtonVal."</td>
																								</tr>";	
																}
																$medInfoButtonShow.="		</table>
																						</td>
																					</tr>";
															}
															if(trim($scanPathSiteCare) != ""){
																if(file_exists($uploadDirSiteCare.$scanPathSiteCare)){
																	$path = $this->imgDirSiteCare.$scanPathSiteCare;
																	$siteCareDoc1 = "<tr><td colspan=\"2\" style=\"vertical-align:top;padding-left:35px; padding-bottom:5px;\">"."<a class=\"text_12b_purple\" onClick=\"openWindowSitecare('$path');\" href=\"javascript:void(0);\">Site Care Document 1</a></td></tr>";
																	if($siteCareAlertType=="doc"){
																		$msg="<img src=".$path." style=\"height:400px; width:400px\">";
																	}	
																}
															}
															if(trim($uploadPathSiteCare) != ""){
																if(file_exists($uploadDirSiteCare.$uploadPathSiteCare)){
																	$path = $this->imgDirSiteCare.$uploadPathSiteCare;
																	$siteCareDoc2 = "<tr><td colspan=\"2\" style=\"vertical-align:top;padding-left:35px; padding-bottom:5px;\">"."<a class=\"text_12b_purple\" onClick=\"openWindowSitecare('$path');\" href=\"javascript:void(0);\">Site Care Document 2</a></td></tr>";
																}
															}
															
															if($siteCareReference){
																$strScheme = parse_url($siteCareReference,0);
																if($strScheme == "http" || $strScheme == "https"){
																	$siteCareRefPath = "<tr><td colspan=\"2\" style=\"vertical-align:top;padding-left:35px; padding-bottom:5px;\">"."<a class=\"text_12b_purple\" onClick=\"openWindowSitecare('$siteCareReference');\" href=\"javascript:void(0);\">Site Care Reference</a></td></tr>";
																}
															}
														
															$msg.= $medInfoButtonShow;
															$msg.= $siteCareDoc1;
															$msg.= $siteCareDoc2;
															$msg.= $siteCareRefPath;
															$htmlFormForReasons.= "<tr class=\"\" style=\"background-color:#FFFFFF;\">";
															$htmlFormForReasons.= "<input type=\"hidden\" name=\"reason_ids[]\" value=\"".$alertId."\">";
															$htmlFormForReasons.= "<input type=\"hidden\" name=\"adminPatFrequenctId_".$alertId."\" id=\"adminPatFrequenctId_".$alertId."\" value=\"$intPatFrequenctId\">";
															$htmlFormForReasons.= "<input type=\"hidden\" name=\"adminPatSiteCarename_".$alertId."\" id=\"adminPatSiteCarename_".$alertId."\" value=\"$siteCarePlaneName\">";													
															$htmlFormForReasons.= "<td class=\"text_10b\" valign=\"top\">Reasons for \"".wordwrap($siteCarePlaneName,50,"<br>")."\"</td>";
															$htmlFormForReasons.= "<td class=\"text_10b\" valign=\"top\">";										
															$htmlFormForReasons.= "<select name=\"reason_sel_".$alertId."\" id=\"reason_sel_".$alertId."\" class=\"selectpicker\" data-title=\"Please Select\" data-width=\"100%\" onChange=\"javascript:loadItsDesc(this,document.getElementById('reason_text_$alertId'));\" >";										
															$htmlFormForReasons.= $optionSelReson;
															$htmlFormForReasons.= "</select>";										
															//$htmlFormForReasons.= "<input type=\"text\" style=\"visibility:hidden;\" name=\"reason_text_".$alertId."\" id=\"reason_text_".$alertId."\" class=\"input_text_10\">";
															$htmlFormForReasons.= "</td>";
															$htmlFormForReasons.= "</tr>";
															$htmlFormForReasons.= "<tr class=\"confirmBackground\">";
															$htmlFormForReasons.= "<td colspan=\"2\" class=\"text_10b\" valign=\"top\">";												
															$htmlFormForReasons.= "<textarea style=\"visibility:hidden;\" name=\"reason_text_".$alertId."\" id=\"reason_text_".$alertId."\" class=\"form-control\" cols=\"64\" rows=\"2\"></textarea>";
															$htmlFormForReasons.= "</td>";
															$htmlFormForReasons.= "</tr>";
														}
													}
												}
											}
										}
									}	
								}	
							}	
						}	
					//}
				//}	
			}
			if($htmlFormForReasons && $msg){
				if($this->alertToShowAt=='Accounting'){
					$top_val='2';
				}
				elseif($this->alertToShowAt=='patient_specific_chart_note'){			
					$top_val='100px';
				}
				elseif($this->alertToShowAt=='patient_specific_chart_note_med_hx'){			
					$top_val='100px';
				}	
				else{
					$top_val='100px';
					$left_mar='0px';
				}	
				
				#alert Msg - start
				/*$this->adminAlertDiv = "<link type=\"text/css\"  href=\"$this->webRoot/interface/themes/style_sky_blue.css\" rel=\"stylesheet\">";
				$this->adminAlertDiv .= "<link type=\"text/css\"  href=\"$this->webRoot/interface/themes/style_patient.css\" rel=\"stylesheet\">";				
				$this->adminAlertDiv .= "<script type=\"text/javascript\" src=\"$this->webRoot/interface/common/script_function.js\"></script>";
				*/
				##Getting combo of procedure
				$procedureCombo = $recallMonthCombo = $recallTxtArea = "";
				$qryGetCbForProcdure = "select id,proc from slot_procedures where proc!='' and (id = procedureId || procedureId =0)  order by proc";
				$rsGetCbForProcdure = imw_query($qryGetCbForProcdure);
				$counter = 0;
				if(imw_num_rows($rsGetCbForProcdure) > 0){
					$procedureCombo = "<select name=\"cbProcedure\" class=\"selectpicker\" data-width=\"100%\" data-size=\"5\" id=\"cbProcedure\"><option value=\"-2\">Site Care Plane</option>";
					while($rowGetCbForProcdure = imw_fetch_array($rsGetCbForProcdure)) {
						$procId = $procName = "";
						$procId = $rowGetCbForProcdure['id'];
						$procName = $rowGetCbForProcdure['proc'];
						$procedureCombo .= "<option value=\"$procId\">$procName</option>";
					}
					$procedureCombo .= "</select>";
				}
				
				$recallMonthCombo = "<select name=\"cbRecallMonth\" id=\"cbRecallMonth\" class=\"selectpicker\" data-width=\"100%\" data-size=\"5\"><option value=\"\">-</option>";
				$i=1;
				while($i < 25){
					$monthVal = "";
					if($i < 10){
						$monthVal = "0".$i;
					}
					else{
						$monthVal = $i;
					}
					$recallMonthCombo .= "<option value=\"$i\">$monthVal</option>";
				$i++;
				}
				$recallMonthCombo .= "</select>";
				$siteCarePlaneNameAll = substr(trim($siteCarePlaneNameAll), 0, -1);	
				$recallTxtArea = "<textarea class=\"form-control\" name=\"txtAreaCommentRecall\" id=\"txtAreaCommentRecall\" cols=\"64\" rows=\"2\">$siteCarePlaneNameAll</textarea>";
				$declineTxtArea = "<textarea class=\"form-control\" name=\"txtAreaCommentDecline\" id=\"txtAreaCommentDecline\" cols=\"64\" rows=\"2\"></textarea>";
				##########

				$this->adminAlertDiv .= "<script type=\"text/javascript\">

											function adminAcknowledged(){if(document.getElementById('adminMainDivAlert')){document.getElementById('adminMainDivAlert').style.display=\"none\";}}
											function adminDisable(){
												if(document.getElementById('adminMsgDivAlert')){
													//alert(top.document.getElementById('alert_mask').style.display);
													if(top.document.getElementById('alert_mask')){
														top.document.getElementById('alert_mask').style.display=\"block\";
													}
													document.getElementById('adminMsgDivAlert').style.display=\"none\";													
													document.getElementById('adminButtonType').value = \"Administered\";
													/*if(document.getElementById('adminMsgResonDivAlert')){
														document.getElementById('adminMsgResonDivAlert').style.display=\"block\";
													}*/													
												}
											}
											function adminDecline(){
												if(document.getElementById('adminMsgDivAlert')){
													document.getElementById('adminMsgDivAlert').style.display=\"none\";	
													document.getElementById('adminButtonType').value = \"Decline\";
													if(document.getElementById('adminMsgDeclineDivAlert')){
														document.getElementById('adminMsgDeclineDivAlert').style.display=\"block\";
													}																																						
												}
											}
											function adminInsertRecall(){
												if(document.getElementById('adminMsgDivAlert')){
													document.getElementById('adminMsgDivAlert').style.display=\"none\";	
													document.getElementById('adminButtonType').value = \"InsertRecall\";												
													if(document.getElementById('adminMsgResonDivAlert')){
														document.getElementById('adminMsgResonDivAlert').style.display=\"none\";
													}
													if(document.getElementById('adminInsertRecallDivAlert')){
														document.getElementById('adminInsertRecallDivAlert').style.display=\"block\";
													}													
												}												
											}
											function insertRecallDone(){
												var alertMsg;
												if(document.getElementById('cbProcedure')){
													if(document.getElementById('cbProcedure').value == \"\"){
														alert('Please Select Procedure for Recall');
														return false;
													}
												}
												if(document.getElementById('cbRecallMonth')){
													if(document.getElementById('cbRecallMonth').selectedIndex == 0){
														alert('Please Select Month for Recall');
														return false;
													}
												}
												document.getElementById('adminButtonType').value = \"InsertRecall\";
												if(top.document.getElementById('alert_mask')){
													top.document.getElementById('alert_mask').style.display=\"block\";
												}
												return true;												
											}
											function adminCancel(){												
												if(document.getElementById('adminMsgDivAlert')){
													document.getElementById('adminMsgDivAlert').style.display=\"none\";													
													document.getElementById('adminButtonType').value = \"Cancel\";
												}
											}
											function adminClose(){
												if(document.getElementById('adminMsgDivAlert')){
													document.getElementById('adminMsgDivAlert').style.display=\"none\";													
												}
											}
											function openWindowSitecare(path){
												window.open(path);
											}
											function skipRecallAdmin(){
												if(document.getElementById('adminInsertRecallDivAlert')){
													document.getElementById('adminInsertRecallDivAlert').style.display=\"none\";
												}												
											}
											function adminReschedule(){
												if(document.getElementById('adminMsgDivAlert')){
													document.getElementById('adminMsgDivAlert').style.display=\"none\";	
													document.getElementById('adminButtonType').value = \"InsertReschedule\";																									
													if(document.getElementById('adminInsertRescheduleDivAlert')){
														document.getElementById('adminInsertRescheduleDivAlert').style.display=\"block\";
													}													
												}	
											}
											function skipRescheduleAdmin(){
												if(document.getElementById('adminInsertRescheduleDivAlert')){
													document.getElementById('adminInsertRescheduleDivAlert').style.display=\"none\";
												}												
											}
											function insertRescheduleDone(){
												//alert(document.getElementById('adminPatFrequenctId').value)												
												if(document.getElementById('txtRescheduleDate')){
													if(document.getElementById('txtRescheduleDate').value == ''){
														alert('Please Enter Reschedule Date');
														return false;
													}
													else{
														document.getElementById('adminButtonType').value = \"InsertReschedule\";		
													}
												}	
												//alert(document.getElementById('adminPatstrGetAtFirst').value);
												return true;
											}
											
											function skipDeclineAdmin(){
												if(document.getElementById('adminMsgDeclineDivAlert')){
													document.getElementById('adminMsgDeclineDivAlert').style.display=\"none\";
												}												
											}
											function insertDeclineDone(){
												//alert(document.getElementById('txtAreaCommentDecline').value)												
												if(document.getElementById('txtAreaCommentDecline')){
													if(document.getElementById('txtAreaCommentDecline').value == ''){
														alert('Please Enter Decline Reason');
														return false;
													}
													else{
														document.getElementById('adminButtonType').value = \"Decline\";	
													}													
												}												
												//alert(document.getElementById('txtAreaCommentDecline').value);
												if(top.document.getElementById('alert_mask')){
													top.document.getElementById('alert_mask').style.display=\"block\";
												}
												return true;
											}
										</script>";
				if($nxtStatus == "yes"){
					$mainDiv = "none";
				}
				else{
					$mainDiv = "block";
				}									
				$this->adminAlertDiv .= "<form name=\"frm_chart_alerts_admin_specific\" id=\"frm_chart_alerts_admin_specific\" action=\"$this->webRoot/interface/patient_info/alerts_reason_save.php\" target=\"frm_chart_alerts_admin_specific\" method=\"post\">";
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"adminButtonType\" id=\"adminButtonType\">";											
				//$this->adminAlertDiv .= "<input type=\"hidden\" name=\"adminPatFrequenctId\" id=\"adminPatFrequenctId\" value=\"$intPatFrequenctId\">";	
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"adminPatstrGetAtFirst\" id=\"adminPatstrGetAtFirst\" value=\"$strGetAtFirst\">";					
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"cancel_pt_alert\" id=\"cancel_pt_alert\" value=\"$cancelAlerts\">";					
				$this->adminAlertDiv .= "<div id=\"adminMainDivAlert\" class=\"\" onmouseover=\"drag_div_move(this, event)\" onMouseDown=\"drag_div_move(this, event)\"; style=\"z-index:2000; display:$mainDiv; border:none; top:$topMargin; width:600px; left:$leftMargin; position:absolute;\">";
				$this->adminAlertDiv .= "<div class=\"row\">";
				$this->adminAlertDiv .= "<div id=\"adminMsgDivAlert\" style=\"display:block;  top:auto; border: 1px solid; border-color:#A6C9DB; background-color:#FFFFFF;\" class=\"confirmTable3 panel panel-success\">";							
				$this->adminAlertDiv .= "<div class=\"boxhead panel-heading\" style=\"line-height: 20px; cursor:move; vertical-align:middle;\">Admin Alert PHMS(s)</div>";
				//$this->adminAlertDiv .= "<iframe name=\"frm_chart_alerts_admin_specific\" ></iframe>";
				$strTemp = $this->webRoot."/library/images/confirmYesNo.gif";
				$this->adminAlertDiv .= "<div class=\"panel-body\">
												<div class=\"col-sm-12\" style = \"overflow: auto; -ms-overflow-x: hidden; max-height: 350px;\">
													<div class=\"row\">";
				$this->adminAlertDiv .= "					<div class=\"col-sm-1 text-center\"><img src=\"$strTemp\" alt=\"Confirm\"></div>";
				//$this->adminAlertDiv .= "<div id=\"adminAlertMsg\" class=\"text_10b\" style=\"text-align:center;\">".wordwrap($msg,50,'<br>')."</div>";
				$this->adminAlertDiv .= "						<div id=\"adminAlertMsg\" class=\"col-sm-11\" style=\"text-align:left;padding-top:10px;\"><table cellspacing=\"2\" cellpadding=\"0\" class=\"text_10b\" >".$msg."</table>
													</div>";
					$this->adminAlertDiv .= "</div>
										</div></div>";
				$this->adminAlertDiv .= "<div class=\"panel-footer text-center\" id=\"module_buttons\">";
				//admin alert main button				
				//$this->adminAlertDiv .= "<span style=\"padding-right:10px;\"><input type=\"button\" value=\"Administer\" name=\"adminAlertAcknowledged\" id=\"adminAlertAcknowledged\" class=\"dff_button\" onClick=\"javascript: adminDisable(); this.form.submit();\" ></span>";							
				$this->adminAlertDiv .= "<input type=\"button\" class=\"btn btn-success\" name=\"adminAlertCancelSkip\" id=\"adminAlertCancelSkip\" value=\"    OK    \" onClick=\"javascript: adminCancel(); this.form.submit();\" >&nbsp;";				
				$this->adminAlertDiv .= "<input type=\"button\" value=\"Decline\" name=\"adminAlertDecline\" id=\"adminAlertDecline\" class=\"btn btn-success\" onClick=\"javascript: adminDecline();\" >&nbsp;";
				//$this->adminAlertDiv .= "<input type=\"button\" value=\"Reschedule\" name=\"adminAlertReschedule\" id=\"adminAlertReschedule\" class=\"dff_button\" onClick=\"javascript: adminReschedule();\" onMouseOver=\"button_over('adminAlertReschedule')\" onMouseOut=\"button_over('adminAlertReschedule','')\">";
				
				$this->adminAlertDiv .= "<input type=\"button\" value=\"Insert Recall\" name=\"adminAlertInsertRecall\" id=\"adminAlertInsertRecall\" class=\"btn btn-success\" onClick=\"javascript: adminInsertRecall();\" >&nbsp;";				

				$this->adminAlertDiv .= "<input type=\"button\" class=\"btn btn-danger\" name=\"adminAlertCancelSkip\" id=\"adminAlertCancelSkip\" value=\"Close\" onClick=\"javascript: adminClose();\" >&nbsp;";				
				
				//$this->adminAlertDiv .= "<input type=\"button\" value=\"Acknowledged\" name=\"adminAlertAcknowledged\" id=\"adminAlertAcknowledged\" class=\"dff_button\" onClick=\"javascript: adminAcknowledged();\" onMouseOver=\"button_over('adminAlertAcknowledged')\" onMouseOut=\"button_over('adminAlertAcknowledged','')\">";			
				//$this->adminAlertDiv .= "<input type=\"button\" value=\"Disable\" name=\"adminAlertDisable\" id=\"adminAlertDisable\" class=\"dff_button\" onClick=\"adminDisable();\" onMouseOver=\"button_over('adminAlertDisable')\" onMouseOut=\"button_over('adminAlertDisable','')\">";
				$this->adminAlertDiv .= "</div>";
				$this->adminAlertDiv .= "</div>";		
				//admin reson div - start					
				/*$this->adminAlertDiv .= "<div id=\"adminMsgResonDivAlert\" style=\"display:none; z-index:1000; top:auto width:auto;\" >";						
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"frm\" value=\"$this->alertToShowAt\">";				
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"patientCurrentFormId\" id=\"patientCurrentFormId\" value=\"$patientCurrentFormId\">";				
				$this->adminAlertDiv .= "<table class=\"confirmTable3\" style=\"top:0px;left:0px;\">";
				$this->adminAlertDiv .= "<tr class=\"confirmBackground\">";
				$this->adminAlertDiv .= "<td  class=\"text_b_w\" colspan=\"2\" style=\"cursor:move;\">Admin Alerts - Decline</td>";
				$this->adminAlertDiv .= "</tr>";
				$this->adminAlertDiv .= $htmlFormForReasons;
				$this->adminAlertDiv .= "<tr class=\"confirmBackground\">";
				$this->adminAlertDiv .= "<td  class=\"txt_10\" colspan=\"2\"><input type=\"button\" class=\"dff_button\" value=\"Administer\" onClick=\"adminAcknowledged(); this.form.submit();\">";
				$this->adminAlertDiv .= "&nbsp;<input type=\"button\" class=\"dff_button\" value=\"Skip\" onClick=\"adminAcknowledged();\"></td>";
				$this->adminAlertDiv .= "</tr>";
				$this->adminAlertDiv .= "</table>";			
				$this->adminAlertDiv .= "</div>";
				*/
				//admin reson div - End
				//admin Decline div - start					
				$this->adminAlertDiv .= "<div id=\"adminMsgDeclineDivAlert\" class=\"panel panel-success\" style=\"display:none; z-index:1000; top:auto width:auto;\" >";						
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"frm\" value=\"$this->alertToShowAt\">";				
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"patientCurrentFormId\" id=\"patientCurrentFormId\" value=\"$patientCurrentFormId\">";				
				$this->adminAlertDiv .= "<div class=\"boxhead panel-heading\">Admin Alerts - Decline</div>";
				$this->adminAlertDiv .= "<div class=\"panel-body\">";
				$this->adminAlertDiv .= "<table class=\"table table-bordered table-condensed\" style=\"top:0px;left:0px;border: 1px solid; border-color:#A6C9DB; background-color:#FFFFFF;\">";
				//$this->adminAlertDiv .= "<tr class=\"confirmBackground\">";
				//$this->adminAlertDiv .= "<td class=\"text_10b\" colspan=\"2\">Decline Reason :</td>";
				//$this->adminAlertDiv .= "<td>$declineTxtArea</td>";
				//$this->adminAlertDiv .= "</tr>";
				$this->adminAlertDiv .= $htmlFormForReasons;	
				$this->adminAlertDiv .= "</table>";			
				$this->adminAlertDiv .= "</div><div class=\"panel-footer\" id=\"module_buttons\">";				
				$this->adminAlertDiv .= "<div class=\"row\">";				
				$this->adminAlertDiv .= "<div class=\"col-sm-12 text-center\">";
				$this->adminAlertDiv .= "<input type=\"button\" class=\"btn btn-success\" value=\"Done\" onClick=\"javascript: var temp = insertDeclineDone(); if(temp == true){document.getElementById('adminMsgDeclineDivAlert').style.display='none'; this.form.submit();}\">&nbsp;<input type=\"button\" class=\"btn btn-success\" value=\"Skip\" onClick=\"skipDeclineAdmin();\">";		
				$this->adminAlertDiv .= "</div>";				
				$this->adminAlertDiv .= "</div>";				
				$this->adminAlertDiv .= "</div></div>";				
				//admin Decline div - End
				//insert recall div	- start				
				$this->adminAlertDiv .= "<div id=\"adminInsertRecallDivAlert\" class=\"panel panel-success\" style=\"display:none; z-index:1000; top:auto width:auto;\" >";						
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"frm\" value=\"$this->alertToShowAt\">";				
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"patientCurrentFormId\" id=\"patientCurrentFormId\" value=\"$patientCurrentFormId\">";
				$this->adminAlertDiv .= "<div class=\"boxhead panel-heading\">Admin Alert - Insert Recall</div>";
				$this->adminAlertDiv .= "<div class=\"panel-body\">";				
				$this->adminAlertDiv .= "<table class=\"table table-bordered table-condensed\" style=\"top:0px;left:0px;border: 1px solid; border-color:#A6C9DB; background-color:#FFFFFF;\">";
				$this->adminAlertDiv .= "<tr class=\"\">";
				$this->adminAlertDiv .= "<td class=\"\">Procedure :</td>";
				$this->adminAlertDiv .= "<td>$procedureCombo</td>";
				$this->adminAlertDiv .= "</tr>";				
				$this->adminAlertDiv .= "<tr class=\"\">";
				$this->adminAlertDiv .= "<td class=\"text_10b\" nowarp>Recall[Month(s) from Today] :</td>";
				$this->adminAlertDiv .= "<td>$recallMonthCombo</td>";
				$this->adminAlertDiv .= "</tr>";				
				$this->adminAlertDiv .= "<tr class=\"\">";
				$this->adminAlertDiv .= "<td class=\"text_10b\">Description :</td>";
				$this->adminAlertDiv .= "<td>$recallTxtArea</td>";
				$this->adminAlertDiv .= "</tr>";			
				$this->adminAlertDiv .= "</table>";
				$this->adminAlertDiv .= "</div><div class=\"panel-footer\" id=\"module_buttons\">";				
				$this->adminAlertDiv .= "<div class=\"row\">";				
				$this->adminAlertDiv .= "<div class=\"col-sm-12 text-center\">";
				$this->adminAlertDiv .= "<input type=\"button\" class=\"btn btn-success\" value=\"Done\" onClick=\"javascript: var temp = insertRecallDone(); if(temp == true){document.getElementById('adminInsertRecallDivAlert').style.display='none'; this.form.submit();}\">&nbsp;<input type=\"button\" class=\"btn btn-success\" value=\"Skip\" onClick=\"skipRecallAdmin();\">";		
				$this->adminAlertDiv .= "</div>";				
				$this->adminAlertDiv .= "</div>";				
				$this->adminAlertDiv .= "</div></div>";				
				//insert recall div	- end
				
				//insert Reschedule div	- start				
				$this->adminAlertDiv .= "<div id=\"adminInsertRescheduleDivAlert\" style=\"display:none; z-index:1000; top:auto width:auto;\" >";						
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"frm\" value=\"$this->alertToShowAt\">";				
				$this->adminAlertDiv .= "<input type=\"hidden\" name=\"patientCurrentFormId\" id=\"patientCurrentFormId\" value=\"$patientCurrentFormId\">";								
				$this->adminAlertDiv .= "<table class=\"confirmTable3\" style=\"top:0px;left:0px;\">";
				$this->adminAlertDiv .= "<tr class=\"confirmBackground\">";
				$this->adminAlertDiv .= "<td class=\"text_b_w\" style=\"cursor:move;\">SCP Reschedule</td>";
				$this->adminAlertDiv .= "</tr>";
				$this->adminAlertDiv .= "<tr class=\"confirmBackground\">";
				$this->adminAlertDiv .= "<td class=\"text_10b\">Reschedule Date</td>";
				$this->adminAlertDiv .= "<td style=\"padding-left:5px;\"><input type=\"text\" id=\"txtRescheduleDate\"  name=\"txtRescheduleDate\" onBlur=\"checkdate(this);\" value=\"\" size=\"12\" class=\"input_text_10\"></td>";
				$this->adminAlertDiv .= "</tr>";								
				$this->adminAlertDiv .= "<tr class=\"confirmBackground\">";
				$this->adminAlertDiv .= "<td colspan=\"2\"><input type=\"button\" class=\"dff_button\" value=\"Done\" onClick=\"javascript: var temp = insertRescheduleDone(); if(temp == true){document.getElementById('adminInsertRescheduleDivAlert').style.display='none'; this.form.submit();}\"><input type=\"button\" class=\"dff_button\" value=\"Skip\" onClick=\"skipRescheduleAdmin();\"></td>";				
				$this->adminAlertDiv .= "</tr>";				
				$this->adminAlertDiv .= "</table>";
				$this->adminAlertDiv .= "</div>";
				//insert Reschedule div	- end
				$this->adminAlertDiv .= "</div></div>";
				$this->adminAlertDiv .= "</form>";
				$this->adminAlertDiv .= "<iframe name=\"frm_chart_alerts_admin_specific\"  src=\"\" style=\"display:none;\"  frameborder=\"0\" height=\"0\" width=\"0\"></iframe>";
				#alert Msg - end*/
				$this->blAdminAlertExits = true;
				return $this->adminAlertDiv;	
			}			
		}	
	}
	#function to get immunization alerts
	function ImmunizationAlerts($patId,$patientCurrentFormId,$callFrom,$nxtStatus = "no"){
		$htmlFormForReasons = "";
		$sql_getImmnzn="SELECT * FROM immunizations, immunization_admin, immunization_dosedetails
							WHERE immunizations.imnzn_id = immunization_admin.imnzn_id
							AND immunizations.immzn_dose_id = immunization_dosedetails.dose_id
							AND immunizations.patient_id ='".$patId."' order by immunizations.imnzn_id";
		$rez_Immnzn=imw_query($sql_getImmnzn) or die(imw_error());				
		$num_rows=0;
		if($rez_Immnzn){
			 $num_rows=imw_num_rows($rez_Immnzn);
			if($num_rows>0){					
				$qrySelectSCPReson = "select * from scp_reasons order by scp_id";
				$rsSelectSCPReson = imw_query($qrySelectSCPReson);
				$optionSelReson = "";
				$optionSelReson = "<option value=\"\">Select Reason</option>";
				while($rowSelectSCPReson = imw_fetch_array($rsSelectSCPReson)){
					$SCPResonId = $SCPResonCode = $SCPResonCodeDesc = $SCPValOption = "";
					$SCPResonId = $rowSelectSCPReson['scp_id'];
					$SCPResonCode = $rowSelectSCPReson['reason_code'];
					$SCPResonCodeDesc = $rowSelectSCPReson['reason_desc'];
					$SCPValOption = $SCPResonId."-".$SCPResonCode."-".$SCPResonCodeDesc;
					$optionSelReson .= "<option value=\"$SCPValOption\">$SCPResonCode</option>";
				}
				$optionSelReson .= "<option value=\"other\">Other</option>";
				$SCPAlertContent = $SCPReference = $SCPScanPath = $SCPUpLoadpath = "";
				$siteCareDoc1 = $siteCareDoc2 = $siteCareRefPath = $strScpAlertId = "";
				if($callFrom == "" || $callFrom != "chartNote"){
					$uploadDirSiteCare = "../../admin/console/alert/upload/";
				}
				elseif($callFrom == "chartNote"){
					$uploadDirSiteCare = "../admin/console/alert/upload/";
				}
				while($resultRow=imw_fetch_array($rez_Immnzn)){
					$qryGetRegImmSCPContant = "select alertId,site_care_plan_name,alertContent,reference,scan_path,upload_path from alert_tbl where registered_immunization_id = '".(int)$resultRow["imnzn_id"]."'";
					$rsGetRegImmSCPContant = imw_query($qryGetRegImmSCPContant);
					$arrScpAlertId = array();
					if($rsGetRegImmSCPContant){
						if(imw_num_rows($rsGetRegImmSCPContant) > 0){													
							$SCPName = "";
							while($rowGetRegImmSCPContant = imw_fetch_array($rsGetRegImmSCPContant)){								
								$SCPReference = $SCPScanPath = $SCPUpLoadpath = "";
								$SCPAlertContent 	.= "<br/>".$rowGetRegImmSCPContant['alertContent'];
								$SCPReference 		= $rowGetRegImmSCPContant['reference'];
								$SCPScanPath 		= $rowGetRegImmSCPContant['scan_path'];
								$SCPUpLoadpath 		= $rowGetRegImmSCPContant['upload_path'];	
								$SCPName 			.= $rowGetRegImmSCPContant['site_care_plan_name']." ,";	
								$strScpAlertId 		= $rowGetRegImmSCPContant['alertId']."~~";	
								//echo $uploadDirSiteCare.$SCPScanPath;
								if(trim($SCPScanPath) != ""){
									if(file_exists($uploadDirSiteCare.$SCPScanPath)){
										$path = $uploadDirSiteCare.$SCPScanPath;
										$siteCareDoc1 .= "<br/>"."<a class=\"text_10b_purpule\" onClick=\"immOpenWindowSitecare('$path');\" href=\"javascript:void(0);\">Site Care Document 1</a>";
									}
									
								}
								if(trim($SCPUpLoadpath) != ""){
									if(file_exists($uploadDirSiteCare.$SCPUpLoadpath)){
										$path = $uploadDirSiteCare.$SCPUpLoadpath;
										$siteCareDoc2 .= "<br/>"."<a class=\"text_10b_purpule\" onClick=\"immOpenWindowSitecare('$path');\" href=\"javascript:void(0);\">Site Care Document 2</a>";
									}
								}
								if($SCPReference){
									$strScheme = parse_url($SCPReference,0);
									if($strScheme == "http" || $strScheme == "https"){
										$siteCareRefPath .= "<br/>"."<a class=\"text_10b_purpule\" onClick=\"immOpenWindowSitecare('$SCPReference');\" href=\"javascript:void(0);\">Site Care Reference</a>";
									}
								}								
							}			
						}
						imw_free_result($rsGetRegImmSCPContant);
					}	
					$numofdoses=(int)$resultRow["imnzn_numberofdoses"];
					$dose_number=(int)$resultRow["dose_number"];
					$immnzn_id=$resultRow["imnzn_id"];
					$dateFirstDoseGiven=$resultRow["administered_date"];
						if($numofdoses>1 && $dose_number==1 && $dateFirstDoseGiven!="0000-00-00" ){
							$getReturnSTRING = $this->getLatestDoseGiven($immnzn_id,$numofdoses,$dateFirstDoseGiven,$patId);
							if($getReturnSTRING!=""){				
								$explodeArray=@explode("---",$getReturnSTRING);
								$getReturnTime=$explodeArray[0];
								$dose_idValue=$explodeArray[1];
								$immnznDoseValue=$explodeArray[1];
								$immnzntypeValue=$resultRow["imnzn_type"];
								$immnznManufacturerValue=$resultRow["imnzn_manufacturer"];
								$immnznNameValue=$resultRow["imnzn_name"];
								if($getReturnTime!=""){
									$timeCurrent=mktime(0,0,0,date("m"),date("d"),date("Y"));
									if($timeCurrent>=$getReturnTime){
										$doseduedatevalue=date("Y-m-d",$getReturnTime);
										$msgalerts.= "<br/>".$resultRow["imnzn_ptalerts"].$SCPAlertContent.$siteCareDoc1.$siteCareDoc2.$siteCareRefPath;//"DOSE -Due date=".date("Y-m-d",$getReturnTime);
										$htmlFormForReasons.="
											<tr class=\"\" style=\"background-color:#FFFFFF;\">
												<input type=\"hidden\" name=\"immnzn_ids[]\" value=\"".$immnzn_id."\">
												<input type=\"hidden\" name=\"doseid_".$immnzn_id."\" value=\"".$dose_idValue."\">
												<input type=\"hidden\" name=\"immnzntype_".$immnzn_id."\" value=\"".$immnzntypeValue."\">
												<input type=\"hidden\" name=\"immnznName_".$immnzn_id."\" value=\"".$immnznNameValue."\">
												<input type=\"hidden\" name=\"immnznManufacturer_".$immnzn_id."\" value=\"".$immnznManufacturerValue."\">
												<input type=\"hidden\" name=\"immnznDose_".$immnzn_id."\" value=\"".$immnznDoseValue."\">
												<input type=\"hidden\" name=\"doseduedate_".$immnzn_id."\" value=\"".$doseduedatevalue."\">	
												<input type=\"hidden\" name=\"scpAlertId_".$immnzn_id."\" value=\"".$strScpAlertId."\">	
												<input type=\"hidden\" name=\"immPatSiteCarename_".$immnzn_id."\" id=\"immPatSiteCarename_".$immnzn_id."\" value=\"$SCPName\">
												<td class=\"text_10b\">Please provide reason for not giving ".$resultRow["immunization_id"]."</td>
												<td class=\"text_10b\">
													<select name=\"reason_sel_".$immnzn_id."\" id=\"reason_sel_".$immnzn_id."\" class=\"text_10\" onChange=\"javascript:loadItsDescImmu(this,document.getElementById('imnznReason_$immnzn_id'));\" >
															$optionSelReson;
													</select>
												</td>												
											</tr>
											<tr class=\"confirmBackground\">
												<td class=\"text_10b\" colspan=\"2\">
													<textarea style=\"visibility:hidden;\" name=\"imnznReason_".$immnzn_id."\" id=\"imnznReason_".$immnzn_id."\" class=\"input_text_10\" cols=\"64\" rows=\"2\"></textarea>													
												</td>
											</tr>
										";
									}
								}
							}
						}
					}
			}
		}
		if(trim($htmlFormForReasons) != ""){
			##Getting combo of procedure
			$procedureCombo = $recallMonthCombo = $recallTxtArea = "";
			$qryGetCbForProcdure = "select id,proc from slot_procedures where proc!='' and (id = procedureId || procedureId =0)  order by proc";
			$rsGetCbForProcdure = imw_query($qryGetCbForProcdure);
			$counter = 0;
			if(imw_num_rows($rsGetCbForProcdure) > 0){
				$procedureCombo = "<select name=\"immCbProcedure\" class=\"text_10\" id=\"immCbProcedure\"><option value=\"-2\">Site Care Plane</option>";
				while($rowGetCbForProcdure = imw_fetch_array($rsGetCbForProcdure)) {
					$procId = $procName = "";
					$procId = $rowGetCbForProcdure['id'];
					$procName = $rowGetCbForProcdure['proc'];
					$procedureCombo .= "<option value=\"$procId\">$procName</option>";
				}
				$procedureCombo .= "</select>";
			}
			
			$recallMonthCombo = "<select name=\"immCbRecallMonth\" id=\"immCbRecallMonth\" class=\"text_10\"><option value=\"\">-</option>";
			$i=1;
			while($i < 25){
				$monthVal = "";
				if($i < 10){
					$monthVal = "0".$i;
				}
				else{
					$monthVal = $i;
				}
				$recallMonthCombo .= "<option value=\"$i\">$monthVal</option>";
			$i++;
			}
			$recallMonthCombo .= "</select>";
			$SCPName = substr(trim($SCPName), 0, -1);		
			$recallTxtArea = "<textarea class=\"text_9\" name=\"immTxtAreaCommentRecall\" id=\"immTxtAreaCommentRecall\" cols=\"64\" rows=\"2\">$SCPName</textarea>";
			$immDeclineTxtArea = "<textarea class=\"text_9\" name=\"immTxtAreaCommentDecline\" id=\"immTxtAreaCommentDecline\" cols=\"64\" rows=\"2\"></textarea>";
			##########

			$this->immunizationAlertDiv .= "<script type=\"text/javascript\">

											function immAcknowledged(){if(document.getElementById('immMainDivAlert')){document.getElementById('immMainDivAlert').style.display=\"none\";}}
											function immDisable(pid,type){
												if(document.getElementById('immMsgDivAlert')){
													document.getElementById('immMsgDivAlert').style.display=\"none\";													
													if(document.getElementById('immMsgResonDivAlert')){
														//document.getElementById('immMsgResonDivAlert').style.display=\"block\";
													}
													if(document.getElementById('immAlertCallFrom')){
														if(document.getElementById('immAlertCallFrom').value == 'chartNote'){																														
															selTabAlerts(pid,type);
														}
													}
																										
												}												
											}
											
											function selTabAlerts(pid,type){												
												if(type == \"Medical_Hx\"){
													//goToTab(pid,'Medical_Hx&medHxTab=immunizations');
													top.core_redirect_to(\"Medical_Hx\",'".$this->webRoot."/interface/Medical_history/index.php?medHxTab=immunizations"."');
												}
											}
											function goToTab(pid,type){	
												var url=\"../main/main_screen.php?imedic_R22_patient=\"+pid+\"&imedic_R2_type=\"+type;
												var options = 'scrollbars=yes,resizable=yes,status=no,toolbar=no,menubar=no,location=no';    
													options += ',width=' + screen.availWidth + ',height=' + screen.availHeight;
													options += ',screenX=0,screenY=0,top=0,left=0,fullscreen=yes';
												var windowfoc=window.open(url,'_top','');
												windowfoc.focus();
											}

											
											function immDecline(){
												if(document.getElementById('immMsgDivAlert')){
													document.getElementById('immMsgDivAlert').style.display=\"none\";	
													document.getElementById('immButtonType').value = \"Decline\";
													if(document.getElementById('immMsgDeclineDivAlert')){
														document.getElementById('immMsgDeclineDivAlert').style.display=\"block\";
													}																																						
												}
											}
											function immInsertRecall(){
												if(document.getElementById('immMsgDivAlert')){
													document.getElementById('immMsgDivAlert').style.display=\"none\";	
													document.getElementById('immButtonType').value = \"InsertRecall\";											
													if(document.getElementById('immMsgResonDivAlert')){
														document.getElementById('immMsgResonDivAlert').style.display=\"none\";
													}
													if(document.getElementById('immInsertRecallDivAlert')){
														document.getElementById('immInsertRecallDivAlert').style.display=\"block\";
													}													
												}												
											}
											function immInsertRecallDone(){
												var alertMsg;
												if(document.getElementById('immCbProcedure')){
													if(document.getElementById('immCbProcedure').value == \"\"){
														alert('Please Select Procedure for Recall');
														return false;
													}
												}
												if(document.getElementById('immCbRecallMonth')){
													if(document.getElementById('immCbRecallMonth').selectedIndex == 0){
														alert('Please Select Month for Recall');
														return false;
													}
												}
												document.getElementById('immButtonType').value = \"InsertRecall\";
												return true;												
											}
											function immCancel(){												
												if(document.getElementById('immMsgDivAlert')){
													document.getElementById('immMsgDivAlert').style.display=\"none\";													
													//document.getElementById('immButtonType').value = \"Cancel\";
												}
											}
											function immOpenWindowSitecare(path){
												window.open(path);
											}
											
											function skipDeclineImm(){												
												if(document.getElementById('immMsgDeclineDivAlert')){
													document.getElementById('immMsgDeclineDivAlert').style.display=\"none\";
												}												
											}
																						
											function immInsertDeclineDone(){
												//alert(document.getElementById('immTxtAreaCommentDecline').value)												
												/*if(document.getElementById('immTxtAreaCommentDecline')){
													if(document.getElementById('immTxtAreaCommentDecline').value == ''){
														alert('Please Enter Decline Reason');
														return false;
													}
													else{
														document.getElementById('immButtonType').value = \"Decline\";	
													}													
												}*/												
												//alert(document.getElementById('immTxtAreaCommentDecline').value);												
												document.getElementById('immButtonType').value = \"Decline\";	
												return true;
											}
										</script>";
			if($nxtStatus == "yes"){
				$mainDiv = "none";
			}
			else{
				$mainDiv = "block";
			}	
			$this->immunizationAlertDiv .= "<form name=\"frm_alerts_immunization\" id=\"frm_alerts_immunization\" action=\"$this->webRoot/interface/patient_info/immunization_alerts_save.php\" target=\"frm_alerts_immunization\" method=\"post\">";
			$this->immunizationAlertDiv .= "<input type=\"hidden\" name=\"immButtonType\" id=\"immButtonType\">";
			$this->immunizationAlertDiv .= "<input type=\"hidden\" name=\"immAlertCallFrom\" id=\"immAlertCallFrom\" value=\"$callFrom\">";														
			$this->immunizationAlertDiv .= "<div id=\"immMainDivAlert\" onmouseover=\"drag_div_move(this, event)\" onMouseDown=\"drag_div_move(this, event)\"; style=\"z-index:2000; display:$mainDiv; top:100px; width:600px; left:200; position:absolute;\">";
			$this->immunizationAlertDiv .= "<div id=\"immMsgDivAlert\" style=\"display:block;  top:auto width:auto; border:1px solid; border-color:#A6C9DB; background-color:#FFFFFF;\" class=\"confirmTable3\">";							
			$this->immunizationAlertDiv .= "<div class=\"boxhead\" style=\"line-height: 20px; cursor:move; vertical-align:middle;\">Immunization Alerts</div>";
			//$this->immunizationAlertDiv .= "<iframe name=\"frm_alerts_immunization\" ></iframe>";
			$strTemp = $this->webRoot."/images/confirmYesNo.gif";
			$this->immunizationAlertDiv .= "<div style=\"height:auto; width:550px;\">";
			$this->immunizationAlertDiv .= "<div style=\"float:left;\"><img src=\"$strTemp\" alt=\"Confirm\"></div>";
			//$this->immunizationAlertDiv .= "<div id=\"adminAlertMsg\" class=\"text_10b\" style=\"text-align:center;\">".wordwrap($msg,50,'<br>')."</div>";
			$this->immunizationAlertDiv .= "<div id=\"immAlertMsg\" class=\"text_10b\" style=\"text-align:center;\">".$msgalerts."</div>";
			$this->immunizationAlertDiv .= "</div>";
			$this->immunizationAlertDiv .= "<div style=\"text-align:center;padding-top:10px;padding-bottom:10px;\">";
			//immunization alert main button				
			$this->immunizationAlertDiv .= "<span style=\"padding-right:10px;\"><input type=\"button\" value=\"Administer\" name=\"immAlertAcknowledged\" id=\"immAlertAcknowledged\" class=\"dff_button\" onClick=\"javascript: immDisable('$patId','Medical_Hx');\" ></span>";							
			$this->immunizationAlertDiv .= "<span style=\"padding-right:10px;\"><input type=\"button\" value=\"Decline\" name=\"immAlertDecline\" id=\"immAlertDecline\" class=\"dff_button\" onClick=\"javascript: immDecline();\" ></span>";
			//$this->immunizationAlertDiv .= "<input type=\"button\" value=\"Reschedule\" name=\"immAlertReschedule\" id=\"immAlertReschedule\" class=\"dff_button\" onClick=\"javascript: immReschedule();\" onMouseOver=\"button_over('immAlertReschedule')\" onMouseOut=\"button_over('immAlertReschedule','')\">";
			$this->immunizationAlertDiv .= "<span style=\"padding-right:10px;\"><input type=\"button\" value=\"Insert Recall\" name=\"immAlertInsertRecall\" id=\"immAlertInsertRecall\" class=\"dff_button\" onClick=\"javascript: immInsertRecall();\" ></span>";				
			$this->immunizationAlertDiv .= "<span style=\"padding-right:10px;\"><input type=\"button\" class=\"dff_button\" name=\"immAlertCancelSkip\" id=\"immAlertCancelSkip\" value=\"Cancel\" onClick=\"immCancel(); this.form.submit();\" ></span></td>";							
			
			$this->immunizationAlertDiv .= "</div>";
			$this->immunizationAlertDiv .= "</div>";		
			//immunization reson div - start					
			/*$this->immunizationAlertDiv .= "<div id=\"immMsgResonDivAlert\" style=\"display:none; z-index:1000; top:auto width:auto;\" >";						
			$this->immunizationAlertDiv .= "<input type=\"hidden\" name=\"frm\" value=\"$this->alertToShowAt\">";				
			$this->immunizationAlertDiv .= "<input type=\"hidden\" name=\"patientCurrentFormId\" id=\"patientCurrentFormId\" value=\"$patientCurrentFormId\">";				
			$this->immunizationAlertDiv .= "<table class=\"confirmTable3\" style=\"top:0px;left:0px;\">";
			$this->immunizationAlertDiv .= "<tr class=\"confirmBackground\">";
			$this->immunizationAlertDiv .= "<td  class=\"text_b_w\" colspan=\"3\" style=\"cursor:move;\">Immunization Alerts - Administer</td>";
			$this->immunizationAlertDiv .= "</tr>";
			$this->immunizationAlertDiv .= $htmlFormForReasons;
			$this->immunizationAlertDiv .= "<tr class=\"confirmBackground\">";
			$this->immunizationAlertDiv .= "<td  class=\"txt_10\" colspan=\"3\"><input type=\"button\" class=\"dff_button\" value=\"Administer\" onClick=\"immAcknowledged(); this.form.submit();\">";
			$this->immunizationAlertDiv .= "&nbsp;<input type=\"button\" class=\"dff_button\" value=\"Skip\" onClick=\"immAcknowledged();\"></td>";
			$this->immunizationAlertDiv .= "</tr>";
			$this->immunizationAlertDiv .= "</table>";			
			$this->immunizationAlertDiv .= "</div>";
			*/
			//immunization reson div - end	
			//immunization Decline div - start					
			$this->immunizationAlertDiv .= "<div id=\"immMsgDeclineDivAlert\" style=\"display:none; z-index:1000; top:auto width:auto;\" >";						
			$this->immunizationAlertDiv .= "<input type=\"hidden\" name=\"frm\" value=\"$this->alertToShowAt\">";				
			$this->immunizationAlertDiv .= "<input type=\"hidden\" name=\"patientCurrentFormId\" id=\"patientCurrentFormId\" value=\"$patientCurrentFormId\">";				
			$this->immunizationAlertDiv .= "<table class=\"\" style=\"top:0px;left:0px;border:1px solid; border-color:#A6C9DB;background-color:#FFFFFF;\">";
			$this->immunizationAlertDiv .= "<tr class=\"\">";
			$this->immunizationAlertDiv .= "<td  class=\"boxhead\" colspan=\"2\" style=\"cursor:move;\">Immunization Alerts - Decline</td>";
			$this->immunizationAlertDiv .= "</tr>";
			//$this->immunizationAlertDiv .= "<tr class=\"confirmBackground\">";
			//$this->immunizationAlertDiv .= "<td class=\"text_10b\">Decline Reason :</td>";
			//$this->immunizationAlertDiv .= "<td>$immDeclineTxtArea</td>";
			//$this->immunizationAlertDiv .= "</tr>";
			$this->immunizationAlertDiv .= $htmlFormForReasons;
			$this->immunizationAlertDiv .= "<tr class=\"\" style=\"background-color:#FFFFFF;\">";				
			$this->immunizationAlertDiv .= "<td colspan=\"2\" style=\"text-align:center;\"><span style=\"padding-right:10px;\"><input type=\"button\" class=\"dff_button\" value=\"Done\" onClick=\"javascript: var temp = immInsertDeclineDone(); if(temp == true){document.getElementById('immMsgDeclineDivAlert').style.display='none'; this.form.submit();}\"></span><input type=\"button\" class=\"dff_button\" value=\"Skip\" onClick=\"skipDeclineImm();\"></td>";				
			$this->immunizationAlertDiv .= "</tr>";	
			$this->immunizationAlertDiv .= "</table>";			
			$this->immunizationAlertDiv .= "</div>";	
			//immunization Decline div - End
			//insert recall div					
			$this->immunizationAlertDiv .= "<div id=\"immInsertRecallDivAlert\" style=\"display:none; z-index:1000; top:auto width:auto;\" >";						
			$this->immunizationAlertDiv .= "<input type=\"hidden\" name=\"frm\" value=\"$this->alertToShowAt\">";				
			$this->immunizationAlertDiv .= "<input type=\"hidden\" name=\"patientCurrentFormId\" id=\"patientCurrentFormId\" value=\"$patientCurrentFormId\">";								
			$this->immunizationAlertDiv .= "<table class=\"\" style=\"top:0px;left:0px;border:1px solid; border-color:#A6C9DB;background-color:#FFFFFF;\">";
			$this->immunizationAlertDiv .= "<tr class=\"\">";
			$this->immunizationAlertDiv .= "<td  class=\"boxhead\" colspan=\"2\" style=\"cursor:move;\">Insert Recall</td>";
			$this->immunizationAlertDiv .= "</tr>";
			$this->immunizationAlertDiv .= "<tr class=\"\">";
			$this->immunizationAlertDiv .= "<td class=\"text_10b\">Procedure :</td>";
			$this->immunizationAlertDiv .= "<td>$procedureCombo</td>";
			$this->immunizationAlertDiv .= "</tr>";	
			
			$this->immunizationAlertDiv .= "<tr class=\"\">";
			$this->immunizationAlertDiv .= "<td class=\"text_10b\" nowarp>Recall[Month(s) from Today] :</td>";
			$this->immunizationAlertDiv .= "<td>$recallMonthCombo</td>";
			$this->immunizationAlertDiv .= "</tr>";
			
			$this->immunizationAlertDiv .= "<tr class=\"\">";
			$this->immunizationAlertDiv .= "<td class=\"text_10b\">Description :</td>";
			$this->immunizationAlertDiv .= "<td>$recallTxtArea</td>";
			$this->immunizationAlertDiv .= "</tr>";
			$this->immunizationAlertDiv .= "<tr class=\"alignCenter\">";
			$this->immunizationAlertDiv .= "<td colspan=\"2\"><span style=\"padding-right:10px;\"><input type=\"button\" class=\"dff_button\" value=\"Done\" onClick=\"javascript: var temp = immInsertRecallDone(); if(temp == true){document.getElementById('immInsertRecallDivAlert').style.display='none'; this.form.submit();}\"></span><input type=\"button\" class=\"dff_button\" value=\"Skip\" onClick=\"skipRecallImm();\"></td>";				
			$this->immunizationAlertDiv .= "</tr>";				
			$this->immunizationAlertDiv .= "</table>";			
			
			$this->immunizationAlertDiv .= "</div>";
			
			$this->immunizationAlertDiv .= "</div>";
			$this->immunizationAlertDiv .= "</form>";
			$this->immunizationAlertDiv .= "<iframe name=\"frm_alerts_immunization\"  src=\"\" style=\"display:none;\"  frameborder=\"0\" height=\"0\" width=\"0\"></iframe>";
			#alert Msg - end*/			
			$this->blImmAlertExits = true;
			return $this->immunizationAlertDiv;		
		}
	}
	
	function getLatestDoseGiven($immnzn_id,$numofdoses,$dateFirstDoseGiven,$patId){
		$DueDateForDose="";
		$sql_getImmnzn="SELECT immunizations.*,immunization_dosedetails.dose_number FROM immunizations, immunization_dosedetails	
						WHERE immunizations.imnzn_id ='".$immnzn_id."' and
						immunizations.immzn_dose_id = immunization_dosedetails.dose_id and immunizations.patient_id ='".$patId."'
						order by immunizations.administered_date DESC limit 0,1";
		$rez_Immnzn=imw_query($sql_getImmnzn) or die(imw_error());						
		$num_rows=0;
		if($rez_Immnzn){
			$num_rows=imw_num_rows($rez_Immnzn);
			if($num_rows>0){
				$resultRow=imw_fetch_array($rez_Immnzn);
				$LastgivenDoseNumber=(int)$resultRow["dose_number"];
				if($LastgivenDoseNumber<$numofdoses){
					$NextDueDoseNumber=$LastgivenDoseNumber+1;
					$selquery="select * from immunization_dosedetails where imnzn_id='".$immnzn_id."' and dose_number='".$NextDueDoseNumber."'";
					$result=imw_query($selquery);
					$returnRow=0;
					if($result){
						$numRows=imw_num_rows($result);
						if($numRows>0){
							$resultArray=imw_fetch_array($result);
							if(is_array($resultArray)){
								$dose_quantity=$resultArray["dose_quantity"];
								$dose_gap=(int)$resultArray["dose_gap"];
								$dose_booster=$resultArray["dose_booster"];
								$dose_id=$resultArray["dose_id"];
								$dose_gapoption=$resultArray["dose_gapoption"];
								//Check Date For Alerts//
									$dateFirstDoseGivenArray=explode("-",$dateFirstDoseGiven);
									if($dose_gapoption=="Days" && $dose_gap>0 ){
										$day=$dateFirstDoseGivenArray[2];
										$month=$dateFirstDoseGivenArray[1];
										$year=$dateFirstDoseGivenArray[0];
										$DueDateForDose=mktime(0,0,0,$month,$day,$year) + ($dose_gap * 86400);
									 }
									 if($dose_gapoption=="Weeks" && $dose_gap>0 ){
										$day=$dateFirstDoseGivenArray[2];
										$month=$dateFirstDoseGivenArray[1];
										$year=$dateFirstDoseGivenArray[0];
										$DueDateForDose=mktime(0,0,0,$month,$day,$year) + ($dose_gap * 7 * 86400);
									 }
									  if($dose_gapoption=="Month" && $dose_gap>0 ){
										$day=$dateFirstDoseGivenArray[2];
										$month=$dateFirstDoseGivenArray[1];
										$year=$dateFirstDoseGivenArray[0];
										$queryOption=$year.$month.",".$dose_gap;
										$selectPeriod=@imw_query("SELECT PERIOD_ADD(".$queryOption.") as YearMonth");
										if($selectPeriod){
											$resRow=imw_fetch_row($selectPeriod);
											$YearMonth=$resRow[0];//200909
											$temp_year=substr($YearMonth,0,strlen($YearMonth)-2);
											$temp_month=substr($YearMonth,-2);
											$DueDateForDose=mktime(0,0,0,$temp_month,$day,$temp_year);
										}
									 }
									  if($dose_gapoption=="Year" && $dose_gap>0 ){
										$day=$dateFirstDoseGivenArray[2];
										$month=$dateFirstDoseGivenArray[1];
										$year=$dateFirstDoseGivenArray[0]+$dose_gap;
										$DueDateForDose=mktime(0,0,0,$month,$day,$year);
									 }
									
								//End Check Date For Alerts//
								$DueDateForDose=$DueDateForDose."---".$dose_id."---".$dose_quantity;
							}
						}
					}
				}
			}
		}
		return $DueDateForDose;
	}
	
	function getNextImm($patId){
		$strFreqPat = $nxtAlertExits = $doseGap = $doseGapOp = $immznAdministeredDate = "";
		$qryGetImmData = "select immunization_id,imnzn_id,immzn_dose_id,administered_date from immunizations where patient_id = '".$patId."' ORDER BY id DESC LIMIT 1";	
		$rsGetImmData = imw_query($qryGetImmData);
		if($rsGetImmData){
			if(imw_num_rows($rsGetImmData) > 0){
				$rowGetImmData = imw_fetch_array($rsGetImmData);
				$imnznName = $rowGetImmData['immunization_id'];
				$imnznId = $rowGetImmData['imnzn_id'];
				$immznDoseId = $rowGetImmData['immzn_dose_id'];
				$immznAdministeredDate = $rowGetImmData['administered_date'];
				$qryGetImmDoseData = "select dose_id,dose_gap,dose_gapoption  from immunization_dosedetails where imnzn_id = '".$imnznId."' ORDER BY dose_id";	
				$rsGetImmDoseData = imw_query($qryGetImmDoseData);
				if($rsGetImmDoseData){
					if(imw_num_rows($rsGetImmDoseData) > 0){
						$blMatchFound = false;
						while($rowGetImmDoseData = imw_fetch_array($rsGetImmDoseData)){
							if($rowGetImmDoseData['dose_id'] == $immznDoseId){
								$blMatchFound = true;
								continue;
							}
							if($blMatchFound == true){
								$doseGap = $rowGetImmDoseData['dose_gap'];
								$doseGapOp = $rowGetImmDoseData['dose_gapoption'];
								$strFreqPat = $rowGetImmDoseData['dose_gap']." ".$rowGetImmDoseData['dose_gapoption'];
								$blMatchFound = false;
							}
						}
					}
					imw_free_result($rsGetImmDoseData);
				}
			}
			imw_free_result($rsGetImmData);
		}
		if($immznAdministeredDate && $immznAdministeredDate != "0000-00-00"){
			$todayDate =  $immznAdministeredDate;		
			$nextAlertToShow = "";
			list($year, $month, $day) = explode('-',$todayDate);		
			if($doseGapOp == "Days"){
				$intDoseGap = (int)$doseGap;				
				$nextAlertToShow = "";
				$nextAlertToShow = date('m-d-Y', mktime(0, 0, 0, $month, $day + $intDoseGap, $year));								
			}		
			elseif($doseGapOp == "Weeks"){
				$nextAlertToShow = "";
				$nextAlertToShow = date('m-d-Y', mktime(0, 0, 0, $month , $day + 7, $year));
			}
			elseif($doseGapOp == "Month"){
				$nextAlertToShow = "";
				$nextAlertToShow = date('m-d-Y', mktime(0, 0, 0, $month + $intDoseGap, $day, $year));
			}
			elseif($doseGapOp == "Year"){
				$nextAlertToShow = "";
				$nextAlertToShow = date('m-d-Y', mktime(0, 0, 0, $month , $day, $year + $intDoseGap));
			}
		
			if($nextAlertToShow){
				list($newMonth, $newDay, $newYear) = explode('-',$nextAlertToShow);	
				$recallDate = $newYear."-".$newMonth."-".$newDay;	
				$procName = $imnznName."-".$recallDate."-"."NXT";
				$strDescriptions = $imnznName." - Immunization Next Recall which schedule on ".trim($nextAlertToShow);
				$qryGetPatAllRecall = "select id from patient_app_recall where patient_id  = '".$patId."' and descriptions = '".$strDescriptions."' and recalldate = '".$recallDate."' and procedure_name = '".$procName."' AND descriptions != 'MUR_PATCH'";
				$rsGetPatAllRecall = imw_query($qryGetPatAllRecall);
				if(imw_num_rows($rsGetPatAllRecall) > 0){
					$strFreqPat = "";
					$nxtAlertExits = "no";
				}
				if($strFreqPat != ""){			
					$nxtAlertExits = "yes";

					$immFreqpatAlertDiv .= "<script type=\"text/javascript\">											

													function immNextNo(){
														if(document.getElementById('adminMainDivAlert')){
															if(document.getElementById('adminMainDivAlert').style.display == \"none\"){
																document.getElementById('adminMainDivAlert').style.display=\"block\";
															}													
														}
														if(document.getElementById('patSpesificDivAlert')){
															if(document.getElementById('patSpesificDivAlert').style.display == \"none\"){
																document.getElementById('patSpesificDivAlert').style.display=\"block\";
															}													
														}
														if(document.getElementById('immMainDivAlert')){
															if(document.getElementById('immMainDivAlert').style.display == \"none\"){
																document.getElementById('immMainDivAlert').style.display=\"block\";
															}													
														}
														if(document.getElementById('immMainDivAlertNxt')){													
															document.getElementById('immMainDivAlertNxt').style.display=\"none\";
														}
													}											
												</script>";
					$immFreqpatAlertDiv .= "<form name=\"frm_alerts_immunization_nxt_freq\" id=\"frm_alerts_immunization_nxt_freq\" action=\"$this->webRoot/interface/patient_info/common/nxt_freq_pat_save.php\" target=\"frm_alerts_immunization_nxt\" method=\"post\">";
					$immFreqpatAlertDiv .= "<input type=\"hidden\" name=\"immButtonTypeNxt\" id=\"immButtonTypeNxt\">";
					$immFreqpatAlertDiv .= "<input type=\"hidden\" name=\"imnznName\" id=\"imnznName\" value=\"$imnznName\">";
					$immFreqpatAlertDiv .= "<input type=\"hidden\" name=\"recalldate\" id=\"recalldate\" value=\"$nextAlertToShow\">";
					$immFreqpatAlertDiv .= "<div id=\"immMainDivAlertNxt\" onmouseover=\"drag_div_move(this, event)\" onMouseDown=\"drag_div_move(this, event)\"; style=\"z-index:2000; top:100px; width:600px; left:200; position:absolute;\">";
					$immFreqpatAlertDiv .= "<div id=\"immMsgDivAlertNxt\" style=\"display:block;  top:auto width:auto; border: 1px solid; border-color:#f3f3f3; background-color:#FFFFFF;\" class=\"confirmTable3\">";							
					$immFreqpatAlertDiv .= "<div class=\"boxhead\" style=\"line-height: 20px; cursor:move; vertical-align:middle;\">Immunization Alerts Next Frequency</div>";
					//$immFreqpatAlertDiv .= "<iframe name=\"frm_alerts_immunization\" ></iframe>";
					$strTemp = $this->webRoot."/images/confirmYesNo.gif";
					$immFreqpatAlertDiv .= "<div style=\"height:auto; width:550px;\">";
					$immFreqpatAlertDiv .= "<div style=\"float:left;\"><img src=\"$strTemp\" alt=\"Confirm\"></div>";
					//$immFreqpatAlertDiv .= "<div id=\"adminAlertMsg\" class=\"text_10b\" style=\"text-align:center;\">".wordwrap($msg,50,'<br>')."</div>";
					$immFreqpatAlertDiv .= "<div id=\"immAlertMsgNxt\" class=\"text_10b\" style=\"text-align:center; padding-top:10px; padding-bottom:10px;\">Patient have Next Immunization on ".$nextAlertToShow.", would you like to enter Recall?</div>";
					$immFreqpatAlertDiv .= "</div>";
					$immFreqpatAlertDiv .= "<div style=\"text-align:center;\">";
					//admin alert main button				
					$immFreqpatAlertDiv .= "<span style=\"padding-right:10px;\"><input type=\"button\" value=\"Yes\" name=\"immAlertNxtFreqOk\" id=\"immAlertNxtFreqOk\" class=\"dff_button\" onClick=\"javascript: immNextNo(); this.form.submit();\" ></span>";							
					$immFreqpatAlertDiv .= "<input type=\"button\" value=\"No\" name=\"immAlertNxtFreqNo\" id=\"immAlertNxtFreqNo\" class=\"dff_button\" onClick=\"javascript: immNextNo();\" >";
					
					$immFreqpatAlertDiv .= "</div>";
					$immFreqpatAlertDiv .= "</div>";						
					
					$immFreqpatAlertDiv .= "</div>";
					$immFreqpatAlertDiv .= "</form>";
					$immFreqpatAlertDiv .= "<iframe name=\"frm_alerts_immunization_nxt\"  src=\"\" style=\"display:none;\"  frameborder=\"0\" height=\"0\" width=\"0\"></iframe>";	
				}
			}
		}
		return array($immFreqpatAlertDiv,$nxtAlertExits);
	}
	
	#function to set divs left margin
	function autoSetDivLeftMargin($adminAlertLeftMargin,$patAlertLeftMargin,$immAlertLeftMargin = "200"){
		//if($this->blAdminAlertExits == true && $this->blPatSpecificAlertExits == true){

			$jsSetWidth = "<script type=\"text/javascript\">			

								if(document.getElementById('adminMainDivAlert')){
									document.getElementById('adminMainDivAlert').style.left = $adminAlertLeftMargin;
								}
								if(document.getElementById('patSpesificDivAlert')){
									document.getElementById('patSpesificDivAlert').style.left = $patAlertLeftMargin;
								}	
								if(document.getElementById('immMainDivAlert')){
									document.getElementById('immMainDivAlert').style.left = $immAlertLeftMargin;
									//alert(document.getElementById('immMainDivAlert').style.left);
								}									
							</script>";
			return 	$jsSetWidth;		
		//}
	}	
	#function to set divs left margin
	function autoSetDivTopMargin($adminAlertTopMargin,$patAlertTopMargin,$immAlertTopMargin = "100"){
		//if($this->blAdminAlertExits == true && $this->blPatSpecificAlertExits == true){

			$jsSetWidth = "<script type=\"text/javascript\">			

								if(document.getElementById('adminMainDivAlert')){
									document.getElementById('adminMainDivAlert').style.top = $adminAlertTopMargin;
								}
								if(document.getElementById('patSpesificDivAlert')){
									document.getElementById('patSpesificDivAlert').style.top = $patAlertTopMargin;
									//alert(document.getElementById('patSpesificDivAlert').style.top);
								}
								if(document.getElementById('immMainDivAlert')){
									document.getElementById('immMainDivAlert').style.top = $immAlertTopMargin;
									//alert(document.getElementById('immMainDivAlert').style.top);
								}								
							</script>";
			return 	$jsSetWidth;		
		//}
	}	
	#function to for JavaScript
	function writeJS(){			

		$javaScript = "<script type=\"text/javascript\">	

						function loadItsDesc(selObj,textObj){
							var val = selObj.value; 
							var arrVal = val.split('-'); 	
							textObj.style.visibility = \"visible\";						
							if(arrVal[2]){
								textObj.value = arrVal[2];								
							}
							else{								
								textObj.value = \"\";
							}
							
						}
						function loadItsDescImmu(selObj,textObj){
							var val = selObj.value; 
							var arrVal = val.split('-'); 	
							textObj.style.visibility = \"visible\";													
							if(arrVal[2]){
								textObj.value = arrVal[2];								
							}
							else if(arrVal[0] == \"\"){
								textObj.value = \"\";
								textObj.style.visibility = \"hidden\";
							}
							else{								
								textObj.value = \"\";
							}
							
						}
						function drag_div_move(ele, ev) {
							 $(ele).draggable();
							 /* var deltaX = ev.clientX - parseInt(ele.style.left);
							  var deltaY = ev.clientY - parseInt(ele.style.top);
							  document.attachEvent(\"onmousemove\", moveHandler);   //Register handler
							  document.attachEvent(\"onmouseup\", upHandler);       //Register handler
							  ev.cancelBubble = true;                             //Prevent bubbling
							  ev.returnValue = false;                             //Prevent action
								
							  //moveHandler: ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
							  //carries out the move when the mouse is dragged
								function moveHandler() {
									e = window.event;                                 // IE event model
									ele.style.left = (e.clientX - deltaX) + \"px\";
									ele.style.top  = (e.clientY - deltaY) + \"px\";
									e.cancelBubble = true;                            //Prevent bubbling
								}
							  //upHandler: ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
							  //Terminates the move and unregisters the handlers when the mouse is up
								function upHandler() {
									e = window.event; // IE event model
									document.detachEvent(\"onmouseup\", upHandler);     //Unregister
									document.detachEvent(\"onmousemove\", moveHandler); //Unregister
									e.cancelBubble = true;//Prevent bubbling
								}*/
							}
						</script>";
		return 	$javaScript;		
	}	
	
	function alertMedications($patId,$alertToDisplayAt,$leftMargin = "445px",$topMargin = "100px",$nxtStatus = "no"){
		$this->patAlertDiv = "";
		$this->alertMsg = '';
		$this->patientId = $patId;		
		$this->alertToShowAt = $alertToDisplayAt;		

		$alertMsg='';
		$medQry="Select DISTINCT(title) from lists WHERE pid='".$patId."' AND allergy_status = 'Active'";
		$medRs = imw_query($medQry);
		while($medRes = imw_fetch_array($medRs)){

			$qry="Select medicine_name, alertmsg FROM medicine_data WHERE medicine_name ='".$medRes['title']."' AND alert=1 AND del_status = '0'";
			$rs=imw_query($qry);
			if(@imw_num_rows($rs)>0){
				$res=imw_fetch_array($rs);
				$this->alertMsg.=$res['medicine_name'].'-'.$res['alertmsg']."<br>";
			}
		}
		
		if($this->alertMsg){
					$this->patAlertDiv .= "<script type=\"text/javascript\">function funOK(){if(document.getElementById('patMedicationDivAlert')){document.getElementById('patMedicationDivAlert').style.display=\"none\";}}</script>";

					$this->patAlertDiv .= "<div id=\"patMedicationDivAlert\" onmouseover=\"drag_div_move(this, event)\" onMouseDown=\"drag_div_move(this, event)\"; style=\"display:$mainDiv;  z-index:2000; top:$topMargin; width:450px; left:$leftMargin; position:absolute; border: 1px solid; \" class=\"panel panel-success\">";	//border-color:#A6C9DB; background-color:#FFFFFF;						
					$this->patAlertDiv .= "<div class=\"panel-heading\" style=\"line-height: 20px; cursor:move; vertical-align:middle;\">Patient Medication Alert(s)</div>";
					$strTemp = $this->webRoot."/images/confirmYesNo.gif";
					$this->patAlertDiv .= "<div class=\"panel-body\" style=\"height:auto; width:444px;\">";
					$this->patAlertDiv .= "<div style=\"float:left;font-size:20px;\" class=\"text-info\"><span class=\"glyphicon glyphicon-info-sign\" ></span></div>"; //<img src=\"$strTemp\" alt=\"Confirm\">
					$this->patAlertDiv .= "<div id=\"patientAlertMsg\" class=\"text_10b\" style=\"text-align:center;padding-top:5px;\">".wordwrap($this->alertMsg,100,'<br>')."</div>";
					$this->patAlertDiv .= "</div>";
					$this->patAlertDiv .= "<div class=\"panel-footer\" style=\"text-align:center;padding-top:20px;padding-bottom:10px;\">";
					$this->patAlertDiv .= "<span style=\"padding-right:10px;\"><input type=\"button\" value=\"OK\" name=\"patAlertOK\" id=\"patAlertOK\" class=\"btn btn-success\" onClick=\"javascript: funOK();\" ></span>";			
					$this->patAlertDiv .= "</div>";
					$this->patAlertDiv .= "</div>";								
					$this->patAlertDiv .= "<input type=\"hidden\" name=\"patientSpecificFrm\" value=\"$patientSpecificFrm\">";				
					$this->patAlertDiv .= "<iframe name=\"chart_alerts_patient_specific\"  src=\"\" style=\"display:block;\"  frameborder=\"0\" height=\"0\" width=\"0\"></iframe>";	
					$this->blPatSpecificAlertExits = true;					
					return $this->patAlertDiv;
		}else{
			return false;
		}

	}
			
}
?>
