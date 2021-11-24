<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
set_time_limit(900);
include_once(dirname(__FILE__).'/chart_notes.php');
class chart_cc_hx_ocular extends chart_notes{	
	public function __construct($patient,$form_id){
		parent::__construct($patient);
		$this->form_id = $form_id;
	}
	
	function get_cc_hx_ocular(){
		$return = array();
		if($this->form_id!=""){			
			$result = $this->get_form_cc_hx();
			$return['chief_compliant'] = "".$result[0]['ccompliant'];
			$return['cc_hx'] =  "".$result[0]['reason'];
			$return['Neuro/Psych'] =  "".$result[0]['neuroPsych'];
			$return['Color'] =  "".$result[0]['eyeColor'];
			$return['Dominant'] =  "".$result[0]['dominant'];
			$return['PoorView_Phthisis_Prosthesis'] =  "".$result[0]['phth_pros'];
			$return['PoorView_Phthisis_Prosthesis_Eye'] =  "".$result[0]['eyePrPh'];
			$return['Pain_Level'] =  "".$result[0]['painCc'];
			
		}
		if($this->finalized == 0 && $return['cc_hx'] == "" && $return['cc_id'] == 0){			
			$elem_acuteProbs = $result[0]['acuteEyeProbs'];
			$elem_acuteProbsPrev = ($return['cc_id'] == 0)?stripslashes($result[0]['acuteEyeProbs']):"";
			$return = $this->get_medical_cc_hx($elem_acuteProbs,$elem_acuteProbsPrev);
		}
		$return['ocular_meds'] 	=   $this->get_medi_grid();
		$return['chart_data'] 	= 	$this->get_chart_details();
		return $return;
	}
	function get_form_cc_hx(){
		$this->db_obj->qry = "SELECT * FROM chart_left_cc_history WHERE patient_id = '".$this->patient."' AND form_id = '".$this->form_id."' ";
		$result = $this->db_obj->get_resultset_array();	
		if(count($result)<=0 && ($this->finalized == 0)){
			// New
			$result[0]["cc_id"] = "0";
			$elem_editModeCc = "0";
			// getPast Finalized
			$result = $this->valNewRecordCcHis();
		
		}else{
			// Update
			$elem_ccId = $result[0]["cc_id"];
			$elem_editModeCc = "1";
		}
		return $result;
	}
	function valNewRecordCcHis($sel=" * ",$LF="0"){
	//CHECK IF OLD RECORDS OF THIS PATIENT EXISTS
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		$this->db_obj->qry = "SELECT ".$sel." FROM chart_master_table 
			   		INNER JOIN chart_left_cc_history ON chart_master_table.id = chart_left_cc_history.form_id 
			   		WHERE chart_master_table.patient_id = '".$this->patient."' 
						AND chart_master_table.delete_status='0' 
						AND chart_master_table.purge_status='0' 
			   			AND chart_master_table.record_validity = '1' ".
			   		$LF.
			   		"ORDER BY update_date DESC, chart_master_table.id DESC 
					LIMIT 0,1 ";	
		$result = $this->db_obj->get_resultset_array();	
		return $result;
	}
	function get_medical_cc_hx($elem_acuteProbs,$elem_acuteProbsPrev){
		list($text_data,$text_data_cc) = $this->getCcHx1stLine();
		list($eyeProbs, $chronicProbs) = $this->getOcularEyeInfo("0Empty0");
		$rvs = $this->getGenMedInfo();
	
		$text_data .= $chronicProbs;
		$elem_acuteProbs = ($this->isEyeProbChanged( $eyeProbs, $elem_acuteProbsPrev)) ? addslashes("".$eyeProbs) : "";
		$text_data .= $elem_acuteProbs; // Add Eye Problems
	
		$text_data .= "\n".$rvs; // diebates
		$text_data = html_entity_decode($text_data);	
		$elem_ccHx = str_replace(array("  ","\n\n"),array(" ","\n"),trim($text_data));
		$elem_ccompliant = str_replace(array("  ","\n\n"),array(" ","\n"),trim($text_data_cc));
	
		return array("cc_hx"=>"".$elem_ccHx,"chief_compliant"=>"".$elem_ccompliant);
	}
	function getCcHx1stLine(){
		$text_data_cc=$text_data = "";
		$this->db_obj->qry = "select sex,DOB,providerID from patient_data where id=".$this->patient;
		$result = $this->db_obj->get_resultset_array();	
		if(count($result)>0){
			$sex=strtolower($result[0]['sex']); //CC:“Male/Female” in CC & History should be lower case i.e. “male/female”
			$dob=$result[0]['DOB'];
			$defaul_provider = $result[0]['providerID'];
			$age = $this->getAge($dob);
			$text_data=PTHISTORY." \r";
			$text_data_cc="A $age old $sex patient \r";
		}
		return array("".$text_data,"".$text_data_cc);
	}
	function get_cc_history(){
		list($eyeProbs, $chronicProbs) = $this->getOcularEyeInfo("0Empty0");
		$rvs = $this->getGenMedInfo();
		

		$text_data .= $chronicProbs;
		$elem_acuteProbs = ($this->isEyeProbChanged($eyeProbs, $elem_acuteProbsPrev)) ? addslashes("".$eyeProbs) : "";
		$text_data .= $elem_acuteProbs; // Add Eye Problems
	
		$text_data .= "\n".$rvs; // diebates
		$text_data = html_entity_decode($text_data);	
		//:: this is moved to below after assessment & plan
		//$text_data .= ($text_dataTmp != "") ? "\n".$text_dataTmp : ""; // Add Prev. Assessments and plans 
		//$elem_ccHx = preg_replace("/(\s\s)/", " ", trim($text_data)); // remove double spaces;
		$elem_ccHx = "".str_replace(array("  ","\n\n"),array(" ","\n"),trim($text_data));
		return array($elem_ccHx);
	}
	function getOcularEyeInfo($strCcHx){

	$eyeProbs="";
	$chronicProbs = "";
	//Ocular
	$this->db_obj->qry = "SELECT any_conditions_you, 
								chronicDesc, 
								any_conditions_others_you, 
								OtherDesc, 
								eye_problems,
								eye_problems_other 
						FROM ocular 
						WHERE patient_id='".$this->patient."' ";
	$result = $this->db_obj->get_resultset_array();	
	if(count($result)>0){

		//desc
		$strSep="~!!~~";
		$strSep2=":*:";
		$strDesc = $result[0]["chronicDesc"];
		$strDesc =$this-> get_set_pat_rel_values_retrive($strDesc,"pat");
		
		$arrChronicDesc=array();
		if(!empty($strDesc)){
			$arrDescTmp = explode($strSep, $strDesc);
			if(count($arrDescTmp) > 0){
				foreach($arrDescTmp as $key => $val){
					$arrTmp = explode($strSep2,$val);
					$arrChronicDesc[$arrTmp[0]] = $arrTmp[1];
				}
			}
		}		

		$strAnyConditionsYou = $result[0]["any_conditions_you"];
		$strAnyConditionsYou = $this->get_set_pat_rel_values_retrive($strAnyConditionsYou,"pat");

		$any_conditions_u1_arr=explode(" ",trim(str_replace(","," ",$strAnyConditionsYou)));
		//for($epr=0;$epr<=sizeof($any_conditions_u1_arr);$epr++){
			if(in_array("1", $any_conditions_u1_arr)){
				$sTmp = (!empty($arrChronicDesc[1])) ? " - ".$arrChronicDesc[1] : "";
				$sTmp2 ="Dry Eyes";
				if($strCcHx=="0Empty0"){
					$chronicProbs.="\n".$sTmp2.$sTmp;
				}else if(strpos($strCcHx,$sTmp2)===false){
					$strCcHx.="\n".$sTmp2.$sTmp;
				}
				//$elem_glucoma = "1";
			}
			if(in_array("2", $any_conditions_u1_arr)){
				$sTmp = (!empty($arrChronicDesc[2])) ? " - ".$arrChronicDesc[2] : "";
				$sTmp2 ="Macular Degeneration";
				if($strCcHx=="0Empty0"){
					$chronicProbs.="\n".$sTmp2.$sTmp;
				}else if(strpos($strCcHx,$sTmp2)===false){
					$strCcHx.="\n".$sTmp2.$sTmp;
				}
				//$elem_macDeg = "1";
			}
			if(in_array("3", $any_conditions_u1_arr)){
				$sTmp = (!empty($arrChronicDesc[3])) ? " - ".$arrChronicDesc[3] : "";
				$sTmp2 ="Glaucoma";
				if($strCcHx=="0Empty0"){
					$chronicProbs.="\n".$sTmp2.$sTmp;
				}else if(strpos($strCcHx,$sTmp2)===false){
					$strCcHx.="\n".$sTmp2.$sTmp;
				}
				//$elem_glucoma = "1";
			}
			if(in_array("4", $any_conditions_u1_arr)){
				$sTmp = (!empty($arrChronicDesc[4])) ? " - ".$arrChronicDesc[4] : "";
				$sTmp2 ="Retinal Detachment";
				if($strCcHx=="0Empty0"){
					$chronicProbs.="\n".$sTmp2.$sTmp;
				}else if(strpos($strCcHx,$sTmp2)===false){
					$strCcHx.="\n".$sTmp2.$sTmp;
				}
				//$elem_rDetach = "1";
			}
			if(in_array("5", $any_conditions_u1_arr)){
				$sTmp = (!empty($arrChronicDesc[5])) ? " - ".$arrChronicDesc[5] : "";
				$sTmp2 ="Cataracts";
				if($strCcHx=="0Empty0"){
					$chronicProbs.="\n".$sTmp2.$sTmp;
				}else if(strpos($strCcHx,$sTmp2)===false){
					$strCcHx.="\n".$sTmp2.$sTmp;
				}
				//$elem_cataracts = "1";
			}
			if(in_array("6", $any_conditions_u1_arr)){
				$sTmp = (!empty($arrChronicDesc[6])) ? " - ".$arrChronicDesc[6] : "";
				$sTmp2 ="Keratoconus";
				if($strCcHx=="0Empty0"){
					$chronicProbs.="\n".$sTmp2.$sTmp;
				}else if(strpos($strCcHx,$sTmp2)===false){
					$strCcHx.="\n".$sTmp2.$sTmp;
				}
				//$elem_cataracts = "1";
			}

		//}		
		
		
		$strOtherDesc = "";		
		if(!empty($arrChronicDesc["other"])){
			
			$strOtherDesc = $arrChronicDesc["other"];
			$sTmp2 =trim("".$strOtherDesc);
			$sTmp2 = $this->str_replace_html_chars($sTmp2);
			
			if($strCcHx=="0Empty0"){				
				$chronicProbs.="\n".$sTmp2;
			}else if(strpos($strCcHx,$sTmp2)===false){				
				$strCcHx.="\n".$sTmp2;
			}
			
		}else{		
			$strOtherDesc = $result[0]["OtherDesc"];
			$strOtherDesc = $this->get_set_pat_rel_values_retrive($strOtherDesc,"pat");			
			if(($result[0]["any_conditions_others_you"] == "1") && !empty($strOtherDesc)){
				$sTmp = (!empty($arrChronicDesc[$arrTmp["other"]])) ? " - ".$arrChronicDesc[$arrTmp["other"]] : "";
				$sTmp2 =trim("".$strOtherDesc.$sTmp);				
				$sTmp2 = $this->str_replace_html_chars($sTmp2);
				
				if($strCcHx=="0Empty0"){
					$chronicProbs.="\n".$sTmp2;
				}else if(strpos($strCcHx,$sTmp2)===false){
					$strCcHx.="\n".$sTmp2;
				}				
			}
		}
		
		
		$eye_problems_arr=explode(" ",trim(str_replace(","," ",$result[0]["eye_problems"])));
			if(in_array("1",$eye_problems_arr)){
				$eyeProbs.= "\nBlurred or Poor Vision";
			}
			if(in_array("2",$eye_problems_arr)){
				$eyeProbs.="\nPoor night vision";
			}
			if(in_array("3",$eye_problems_arr)){
				$eyeProbs.="\nGritty Sensation";
			}
			if(in_array("4",$eye_problems_arr)){
				$eyeProbs.="\nTrouble Reading Signs";
			}
			if(in_array("5",$eye_problems_arr)){
				$eyeProbs.="\nGlare From Lights";
			}
			if(in_array("6",$eye_problems_arr)){
				$eyeProbs.="\nTearing";
			}
			if(in_array("7",$eye_problems_arr)){
				$eyeProbs.="\nPoor Depth Perception";
			}
			if(in_array("8",$eye_problems_arr)){
				$eyeProbs.="\nHalos Around Lights";
			}
			if(in_array("9",$eye_problems_arr)){
				$eyeProbs.="\nItching/Burning";
			}
			if(in_array("10",$eye_problems_arr)){
				$eyeProbs.="\nTrouble Identifying Colors";
			}
			if(in_array("11",$eye_problems_arr)){
				$eyeProbs.="\nSpots/Floaters";
			}
			if(in_array("12",$eye_problems_arr)){
				$eyeProbs.="\nEye Pain";
			}
			if(in_array("13",$eye_problems_arr)){
				$eyeProbs.="\nDouble Vision";
			}
			if(in_array("14",$eye_problems_arr)){
				$eyeProbs.="\nSee Light Flashes";
			}
			if(in_array("15",$eye_problems_arr)){
				$eyeProbs.="\nRed eyes";
			}
			if(!empty($result[0]["eye_problems_other"])){
				$eyeProbs.="\n".$result[0]["eye_problems_other"];
			}
	}
	if($strCcHx=="0Empty0"){
		return array("".$eyeProbs,"".$chronicProbs);
	}else{
		return "".$strCcHx; 
	}
	}
	function getGenMedInfo($flagDiab=false){
		$delimiter = '~|~';
		$rvs = "";
		$this->db_obj->qry= "select any_conditions_you,any_conditions_relative,desc_u,desc_r,diabetes_values from general_medicine where patient_id='".$this->patient."' ";
		$result = $this->db_obj->get_resultset_array();	
		if(count($result)>0){
			$any_conditions_u1_arr1=explode(" ",trim(str_replace(","," ",$result[0]["any_conditions_you"])));
			if(in_array("3",$any_conditions_u1_arr1)){
				$strDiabetesIdTxtPat =  $this->get_set_pat_rel_values_retrive($result[0]["diabetes_values"],'pat',$delimiter);				
				$rvs.="Diabetes ".$strDiabetesIdTxtPat;
				$strDiabetesTxtPat = "";
				$strDiabetesTxtPat = $this->get_set_pat_rel_values_retrive($result[0]["desc_u"],"pat");
				$ptInfoDiaDesc=(!empty($strDiabetesTxtPat))? trim($ptInfoDiaDesc." ".stripslashes($strDiabetesTxtPat)) : "";
				if(!empty($ptInfoDiaDesc)) $rvs = $rvs." ".$ptInfoDiaDesc;
				$rvs = ($flagDiab) ? "".$ptInfoDiaDesc : $rvs;
			}
		}
		return $rvs;
	}
	function isEyeProbChanged($eyeProbsCur, $elem_acutePrev){
		$ret = true;
		$elem_acutePrev = str_replace(array("\n","\r"), "", $elem_acutePrev);
		$eyeProbsCur = str_replace(array("\n","\r"), "", $eyeProbsCur);
		if(!empty($elem_acutePrev) ){
			$ret = ($elem_acutePrev == $eyeProbsCur) ? false : true;
		}else{
			//check last entered
			$this->db_obj->qry = "SELECT acuteEyeProbs FROM chart_left_cc_history
					WHERE patient_id='".$this->patient."'
					AND acuteEyeProbs != ''
					ORDER BY form_id DESC LIMIT 0,1 ";
			$result = $this->db_obj->get_resultset_array();	
			if(count($result)>0){
				$elem_acutePrev = stripslashes($result[0]["acuteEyeProbs"]);
				$elem_acutePrev = str_replace(array("\n","\r"), "", $elem_acutePrev);
				$ret = ($elem_acutePrev == $eyeProbsCur) ? false : true;
			}
		}
		return $ret;
	}
	function get_medi_grid(){
		$return = array();
		if($this->form_id!="" && $this->finalized == 1){
			$return  = $this->get_chart_ocular_meds();
		}
		if($this->finalized == 0){
			$return  = $this->get_ocular_meds();
		}
		return $return;
	}
	function get_ocular_meds(){
		$return = array();
		$arrSitesData = array(1=>'OS',2=>'OD',3=>'OU',4=>'PO');
		$this->db_obj->qry = "SELECT title, 
									destination, 
									type, sig, 
									DATE_FORMAT(date,'%Y-%m-%d') as 'eDate', 
									med_comments, 
									sites, 
									compliant ,
									allergy_status
								FROM lists 
								WHERE pid='".$this->patient."' 
									AND type='4' 
									AND allergy_status = 'Active' 
								ORDER BY begdate DESC";
		$result = $this->db_obj->get_resultset_array();	
		foreach($result as $row){
			$arr = array();
			$arr['title'] 		= "".$row['title'];
			$arr['destination'] = "".$row['destination'];
			$arr['sites'] 		= "".$arrSitesData[$row['sites']];
			$arr['sig'] 		= "".$row['sig'];
			$arr['med_comments'] = "".$row['med_comments'];
			if(strtoupper($row['allergy_status']) == 'ACTIVE' || strtoupper($row['allergy_status']) == 'RENEW'){
				$arr['color'] = "green";	// GREEN TEXT COLOR
			}
			else{
				$arr['color'] = "red";	// RED TEXT COLOR
			}
			$curDate = date('Y-m-d',strtotime($row['eDate']));
			if(strtotime($curDate) == strtotime(date('Y-m-d'))){
				$arr['color'] = "blue";		// BLUE TEXT COLOR
			}
			$return[] = $arr;
		}
		return $return;
	}
	function get_chart_ocular_meds(){
		$return = array();
		$arrSitesData = array(1=>'OS',2=>'OD',3=>'OU',4=>'PO');
		$this->db_obj->qry  = "select lists 
							from  
							chart_genhealth_archive 
							where patient_id='".$this->patient."' and
							form_id = '".$this->form_id."'";
		$result = $this->db_obj->get_resultset_array();
		if(count($result) > 0){
			$arrLists = unserialize($result[0]["lists"]);					
			$arrOcuMed = $arrLists[4];
			foreach($arrOcuMed as $med){
				if($med['allergy_status']!='Active'){continue;}	
				$arr = array();
				$arr['title'] 		= "".$med['title'];
				$arr['destination'] = "".$med['destination'];
				$arr['sites'] 		= "".$arrSitesData[$med['sites']];
				$arr['sig'] 		= "".$med['sig'];
				$arr['med_comments'] = "".$med['med_comments'];
				if(strtoupper($med['allergy_status']) == 'ACTIVE' || strtoupper($med['allergy_status']) == 'RENEW'){
					$arr['color'] = "green";	// GREEN TEXT COLOR
						
				}
				else{
					$arr['color'] = "red";	// RED TEXT COLOR
				}
				$curDate = date('Y-m-d',strtotime($med['date']));
				if(strtotime($curDate) == strtotime(date('Y-m-d'))){
					$arr['color'] = "blue";		// BLUE TEXT COLOR
				}
				$return[] = $arr;
				/*$med_name = ucfirst($med['title']);
				$site = $arrSitesData[$med['sites']];
				$sig = $med['sig'];
				$comments = $med['med_comments'];
				$return[] = $med_name." ".$med['destination']."".$row['destination']." ".$site.' '.$sig.";".$comments;*/
			}
		}
		return $return;
	}
}

?>