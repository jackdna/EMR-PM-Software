<?php
//ChartNote
//Constants
define("PTHISTORY", "The Patient has a history of");
define("PTMEDUNKNOWN", "No significant past ocular history");

class CcHx{
	private $pid, $fid;	
	
	public function __construct($pid, $fid){
		$this->fid = $fid;
		$this->pid = $pid; 
	}
	
	function getValforNewRecord($sel=" * ",$LF="0"){
		global $cryfwd_form_id;
		
		//DOS BASED carry forward
		$dt ="";
		if(!empty($cryfwd_form_id)){
			$dt = " AND (chart_master_table.id <=  '".$cryfwd_form_id."' ) ";
		}
		
		//CHECK IF OLD RECORDS OF THIS PATIENT EXISTS
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		$sql = "SELECT ".$sel." FROM chart_master_table ".
			   "INNER JOIN chart_left_cc_history ON chart_master_table.id = chart_left_cc_history.form_id ".
			   "WHERE chart_master_table.patient_id = '".$this->pid."' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			   "AND chart_master_table.record_validity = '1' ".
			   $LF.
			   $dt.
			   "ORDER BY update_date DESC, chart_master_table.id DESC LIMIT 0,1 ";	
		$res = sqlQuery($sql);
		return $res;
	}
	
	function getCcHx1stLine(){
		$text_data_1=$text_data = "";
		
		$oPt = new Patient($this->pid);
		$arPtInfo = $oPt->getPtInfo();
		
		if(count($arPtInfo)>0){
			$sex=strtolower($arPtInfo['sex']);
			$dob=$arPtInfo['DOB'];
			$age = get_age($dob);		
			$text_data=PTHISTORY." \r";
			//$text_data_1="A $age $sex with chief complaint of \r";//$eyeProbs$rvs";		
			$text_data_1="A $age old $sex patient \r";	
		
		}
		
		return array($text_data,$text_data_1);
	}
	
	
	
	function isEyeProbChanged($eyeProbsCur, $elem_acutePrev){
		$ret = true;
		$elem_acutePrev = str_replace(array("\n","\r"), "", $elem_acutePrev);
		$eyeProbsCur = str_replace(array("\n","\r"), "", $eyeProbsCur);
		if(!empty($elem_acutePrev) ){
			$ret = ($elem_acutePrev == $eyeProbsCur) ? false : true;
		}else{
			//check last entered
			$sql = "SELECT acuteEyeProbs FROM chart_left_cc_history
					WHERE patient_id='".$this->pid."'
					AND acuteEyeProbs != ''
					ORDER BY form_id DESC LIMIT 0,1 ";
			$row = sqlQuery($sql);
			if($row != false){
				$elem_acutePrev = stripslashes($row["acuteEyeProbs"]);
				$elem_acutePrev = str_replace(array("\n","\r"), "", $elem_acutePrev);
				$ret = ($elem_acutePrev == $eyeProbsCur) ? false : true;
			}
		}
		return $ret;
	}
	
	function getPtOcuHxMedHx_init($elem_acuteProbs,$elem_acuteProbsPrev){
		list($text_data,$text_data_cc) = $this->getCcHx1stLine();
		//
		$oMedHx = new MedHx($this->pid);
		list($eyeProbs, $chronicProbs, $floatersYesNo,$flashYesNo,$str_chronicProbs) = $oMedHx->getOcularEyeInfo("0Empty0");
		$rvs = $oMedHx->getGenMedInfo();

		$text_data .= $chronicProbs;
		$elem_acuteProbs = ( $this->isEyeProbChanged($eyeProbs, $elem_acuteProbsPrev)) ? addslashes("".$eyeProbs) : "";
		$text_data .= $elem_acuteProbs; // Add Eye Problems

		$text_data .= "\n".$rvs; // diebates
		$text_data = html_entity_decode($text_data);	
		//:: this is moved to below after assessment & plan
		//$text_data .= ($text_dataTmp != "") ? "\n".$text_dataTmp : ""; // Add Prev. Assessments and plans 
		//$elem_ccHx = preg_replace("/(\s\s)/", " ", trim($text_data)); // remove double spaces;
		$elem_ccHx = str_replace(array("  ","\n\n"),array(" ","\n"),trim($text_data));
		$elem_ccompliant = str_replace(array("  ","\n\n"),array(" ","\n"),trim($text_data_cc));

		return array($elem_ccHx,$elem_ccompliant, $str_chronicProbs);
	
	}

	function crrct8Issue($str){
		if($str != ""){
			if(preg_match("/^A\s(8|18|8[0-9])/", $str)){
				$str = preg_replace("/^A/", "An", $str);
			}
		}
		return $str;
	}
	
	function getFormInfo($finalize_flag=0){
		
		$providerId = $_SESSION['authId'];
		$user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"] ;
		
		//Poor view, Phthisis, Prosthesis--
		//$elem_phth_pros_checked="";
		//$elem_phthisis_checked="";
		//$elem_prosthesis_checked="";
		$elem_eyePrPh="";
		$elem_phth_pros="";
		$elem_curset_phth_pros="";	
		//--
		
		//Dominant --
		$elem_dominantCc="";
		//	
		//Color--
		$elem_eyeColorCc="";
		//
		//Neuro/Psych--
		$elem_neuroPsych="AAOx3";
		//
		//Pain level --
		$elem_painCc="";
		//
	
	
	
		// chart_left_cc_history ---------
		$sql = "SELECT * FROM chart_left_cc_history WHERE patient_id = '".$this->pid."' AND form_id = '".$this->fid."' ";
		$row = sqlQuery($sql);
		if(($row == false) && ($finalize_flag == 0)){
			// New
			$elem_ccId = "0";
			$elem_editModeCc = "0";
			// getPast Finalized
			$row = $this->getValforNewRecord();

		}else{
			// Update
			$elem_ccId = $row["cc_id"];
			$elem_editModeCc = "1";
		}
		if($row != false){
			//$elem_dos = ($elem_ccId != "0" && $row["date_of_service"] != "0000-00-00") ? FormatDate_show($row["date_of_service"]) : $elem_dos ;
			$elem_ccHx = ($elem_ccId != "0") ? stripslashes($row["reason"]) : "" ;
			$elem_curset_phth_pros = ($elem_ccId != "0") ? $row["phth_pros_set_curr"] : "0" ;
			$elem_acuteProbsPrev = ($elem_ccId == "0") ? stripslashes($row["acuteEyeProbs"]) : "" ;
			$elem_phthisisOd = $row["phthisisOd"];
			$elem_phthisisOs = $row["phthisisOs"];
			$elem_prosthesisOd = $row["prosthesisOd"];
			$elem_prosthesisOs = $row["prosthesisOs"];
			$elem_phth_pros = $row["phth_pros"];
			$elem_eyePrPh = $row["eyePrPh"];
			$elem_neuroPsych = ($elem_ccId != "0") ? $row["neuroPsych"] : "AAOx3" ; //By default, all new encounters of an existing or new Pt it is AAOx3
			$elem_pro_id = ($elem_ccId != "0") ? $row["pro_id"] : "" ;
			$elem_acuteProbs = $row["acuteEyeProbs"];
			$elem_cosigner_id = ($elem_ccId != "0") ? $row["cosigner_id"] : "0" ;
			$elem_dominantCc = $row["dominant"];
			$elem_eyeColorCc = $row["eyeColor"];
			$elem_ccompliant = stripslashes($row["ccompliant"]);
			$elem_painCc = ($elem_ccId != "0") ? $row["painCc"] : "" ;
			$el_chronicProbs = ($elem_ccId != "0") ? $row["chronic_probs"] : "" ;
		}

		//Set Default value 0 elem_curset_phth_pros
		if(!isset($elem_curset_phth_pros) || empty($elem_curset_phth_pros)){
			$elem_curset_phth_pros = "0";
		}
		
		//convert spl characters
		$elem_ccompliant = checkHPIFormatChars($elem_ccompliant, "de");//decode
		
		//--
		// Check CC history value
		if(($elem_ccHx == "") && ($finalize_flag == 0) && ($elem_ccId == "0")){
			/*
			list($text_data,$text_data_cc) = getCcHx1stLine($patient_id);
			list($eyeProbs, $chronicProbs) = getOcularEyeInfo($patient_id);
			$rvs = getGenMedInfo($patient_id);

			$text_data .= $chronicProbs;
			$elem_acuteProbs = (isEyeProbChanged($patient_id, $eyeProbs, $elem_acuteProbsPrev)) ? addslashes("".$eyeProbs) : "";
			$text_data .= $elem_acuteProbs; // Add Eye Problems

			$text_data .= "\n".$rvs; // diebates
			//:: this is moved to below after assessment & plan
			//$text_data .= ($text_dataTmp != "") ? "\n".$text_dataTmp : ""; // Add Prev. Assessments and plans 
			//$elem_ccHx = preg_replace("/(\s\s)/", " ", trim($text_data)); // remove double spaces;
			$elem_ccHx = str_replace(array("  ","\n\n"),array(" ","\n"),trim($text_data));
			$elem_ccompliant = str_replace(array("  ","\n\n"),array(" ","\n"),trim($text_data_cc));
			*/
			//
			list($elem_ccHx,$elem_ccompliant,$el_chronicProbs)=$this->getPtOcuHxMedHx_init($elem_acuteProbs,$elem_acuteProbsPrev);
			/*$strCessCons = getSocialHx();
			if(!empty($strCessCons)){
				$elem_ccHx=trim($elem_ccHx."\n".$strCessCons);
			}*/
			
		 }else{
			//echo "HELLO";
			//echo	"<br><br>".$elem_ccHx = "\nDiabetes ldjfgdfj \n";
			//Check Ocular Eye values: if changed in MedHx then add.
			//if($finalize_flag == 0 || $isReviewable == true) {
			if($finalize_flag == 0) {
				//Check New Patient save with no pt history
				if(strtoupper(trim($elem_ccHx)) == strtoupper(PTMEDUNKNOWN)){
					//Get all saved values from Med Hx
					list($elem_ccHx_tmp,$elem_ccompliant_tmp,$el_chronicProbs)=$this->getPtOcuHxMedHx_init($elem_acuteProbs,$elem_acuteProbsPrev);
					if(strtoupper(trim($elem_ccHx_tmp))!= strtoupper(PTHISTORY)){
						$elem_ccHx = $elem_ccHx_tmp;
					}		
					
				}else{	
					$oMedHx = new MedHx($this->pid);
					//get Diabtese info
					$strDiab = $oMedHx->getGenMedInfo(true);
					if( !empty($strDiab) && strpos($elem_ccHx,$strDiab) === false  ){
						$elem_ccHx .= "\n".$strDiab;
					}	
					/*else{
						echo "<br>DO<br>";
						if(preg_match("/\\nDiabetes\s*\d\w\s*\\n/g",$elem_ccHx)){
							//$elem_ccHx = preg_replace("/\\nDiabetes(\s*\(\d\w\)\s*\\r*)*\\n/g", $strDiab, $elem_ccHx);
							echo "<br>MATCHED";
						}else{
							echo "<br>Not MATCHED";
						}
					}*/			
					
					list($elem_ccHx,$el_chronicProbs) = $oMedHx->getOcularEyeInfo($elem_ccHx, $el_chronicProbs);	
					
				}
			}
		 }
		//exit("<br><br>".$elem_ccHx);
		//
		//--
		
		// correct A 8 issue
		$elem_ccHx = $this->crrct8Issue(trim($elem_ccHx));
		
		//---
		///Correct CC format: can move to save portion after some time
		$ptrn = "\s*(A|An)((\s*[0-9]{1,3}\s*(Yr\.|Yrs\.|Year|Years|Months|Days|Mon\.))?(\s*Old)?)?\s*(Male|Female)?\s*(with\s*history\s*of|patient|with\s*chief\s*complaint\s*of)\s*";
		if(!preg_match("/".$ptrn."\r/i",$elem_ccompliant)){//Not Matched
			if(preg_match("/".$ptrn."/i",$elem_ccompliant,$tmpMatch)){
				$elem_ccompliant = str_replace($tmpMatch[0],$tmpMatch[0]."\r",$elem_ccompliant);
				$elem_ccompliant=str_replace("\n\r","\r",$elem_ccompliant);
			}
		}
		//--
		
		//Check Duplicate Cosigner of CCHx
		if(!empty($elem_pro_id) && $elem_pro_id == $elem_cosigner_id){
			$elem_cosigner_id = "";
		}
		
		//--
		//Default Cc Operator
		if(empty($elem_pro_id) && (in_array($user_type, $GLOBALS['arrValidCNPhy']) || in_array($user_type, $GLOBALS['arrValidCNTech']))){
			$elem_pro_id = $providerId;
		}
		//Name CC Operator
		if(!empty($elem_pro_id)){
			$oUsr = new User($elem_pro_id);
			$elem_pro_name = $oUsr->getName(7);
		}else{
			$elem_pro_name = "";
		}
		//Name CC Hx Cosigner
		if(!empty($elem_cosigner_id)){
			$oUsr = new User($elem_cosigner_id);
			$elem_cosigner_name = $oUsr->getName(7);
		}else{
			$elem_cosigner_name = "";
		}
		//--
		
		//Archive CC --
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$providerId);
		if($oChartRecArc->isArchived()){
			$oChartRecArc->setChkTbl("chart_left_cc_history");
			$arTmpRecArc = $oChartRecArc->getArcRec(array("elem_ccHx"=> array("reason", $elem_ccHx, "styleCcHxArc")));
			if(!empty($arTmpRecArc["div"]["elem_ccHx"])){
				$elem_ccHx_htm = $arTmpRecArc["div"]["elem_ccHx"];
				$elem_ccHx_js = $arTmpRecArc["js"]["elem_ccHx"];
				$elem_ccHx_css = $arTmpRecArc["css"]["elem_ccHx"];
				if(!empty($arTmpRecArc["curText"]["elem_ccHx"])) $elem_ccHx = $arTmpRecArc["curText"]["elem_ccHx"];
			}else{
				$elem_ccHx_htm=$elem_ccHx_js=$elem_ccHx_css="";
			}
		}
		//Archive CC --
	
		//Neuro/psych drop down
		$data_neuro_htm="";
		$ar_np = array("Agitated", "AAOx3", "Confused", "Flat", "Cognitive Impairment", "Too Young (Pediatric Patient)", "Uncooperative");
		foreach($ar_np as $k => $v){
			$tmp = (!empty($elem_neuroPsych) && $v == $elem_neuroPsych) ? " class='active' " : "" ;
			$data_neuro_htm .= "<li ".$tmp." ><a class=\"dropdown-item\" href=\"#\">".$v."</a></li>";
		}

		
		$arr=array();
		$arr["data_neuro_htm"] = $data_neuro_htm;
		if(isset($elem_ccId)){ $arr["elem_ccId"] = $elem_ccId ;}
		if(isset($elem_editModeCc)){ $arr["elem_editModeCc"] = $elem_editModeCc ;}
		if(isset($elem_ccHx)){ $arr["elem_ccHx"] = $elem_ccHx  ;}
		if(isset($elem_curset_phth_pros)){ $arr["elem_curset_phth_pros"] = $elem_curset_phth_pros ;}
		if(isset($elem_acuteProbsPrev)){ $arr["elem_acuteProbsPrev"] = $elem_acuteProbsPrev  ;}
		if(isset($elem_phthisisOd)){ $arr["elem_phthisisOd"] = $elem_phthisisOd ;}
		if(isset($elem_phthisisOs)){ $arr["elem_phthisisOs"] = $elem_phthisisOs ;}
		if(isset($elem_prosthesisOd)){ $arr["elem_prosthesisOd"] = $elem_prosthesisOd ;}
		if(isset($elem_prosthesisOs)){ $arr["elem_prosthesisOs"] = $elem_prosthesisOs ;}
		if(isset($elem_phth_pros)){ $arr["elem_phth_pros"] = $elem_phth_pros ;}
		if(isset($elem_eyePrPh)){ $arr["elem_eyePrPh"] = $elem_eyePrPh ;}
		if(isset($elem_neuroPsych)){ $arr["elem_neuroPsych"] = $elem_neuroPsych ;}
		if(isset($elem_pro_id)){ $arr["elem_pro_id"] = $elem_pro_id  ;}
		if(isset($elem_acuteProbs)){ $arr["elem_acuteProbs"] = $elem_acuteProbs ;}
		if(isset($elem_cosigner_id)){ $arr["elem_cosigner_id"] = $elem_cosigner_id  ;}
		if(isset($elem_dominantCc)){ $arr["elem_dominantCc"] = $elem_dominantCc ;}
		if(isset($elem_eyeColorCc)){ $arr["elem_eyeColorCc"] = $elem_eyeColorCc ;}
		if(isset($elem_ccompliant)){ $arr["elem_ccompliant"] = $elem_ccompliant ;}
		if(isset($elem_painCc)){ $arr["elem_painCc"] = $elem_painCc  ;}
		if(isset($elem_pro_name)){ $arr["elem_pro_name"] = $elem_pro_name  ;}
		if(isset($elem_cosigner_name)){ $arr["elem_cosigner_name"] = $elem_cosigner_name  ;}		
		if(isset($elem_ccHx_htm)){ $arr["elem_ccHx_htm"] = $elem_ccHx_htm  ;}
		if(isset($elem_ccHx_js)){ $arr["elem_ccHx_js"] = $elem_ccHx_js  ;}
		if(isset($elem_ccHx_css)){ $arr["elem_ccHx_css"] = $elem_ccHx_css  ;}
		if(isset($elem_ccHx_css)){ $arr["elem_ccHx_css"] = $elem_ccHx_css  ;}	
		if(isset($el_chronicProbs)){ $arr["el_chronicProbs"] = $el_chronicProbs  ;}
		
		return $arr;
	
	}
	
	function getValforNewRecord_OcuHx($sel=" * ",$LF="0"){
		global $cryfwd_form_id;
		
		//DOS BASED carry forward
		$dt ="";
		if(!empty($cryfwd_form_id)){
			$dt = " AND (chart_master_table.id <=  '".$cryfwd_form_id."' ) ";
		}
		
		//CHECK IF OLD RECORDS OF THIS PATIENT EXISTS
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		$sql = "SELECT ".$sel." FROM chart_master_table ".
			   "INNER JOIN chart_left_provider_issue ON chart_master_table.id = chart_left_provider_issue.form_id ".
			   "WHERE chart_master_table.patient_id = '".$this->pid."' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			   "AND chart_master_table.record_validity = '1' ".
			   $LF.
			   $dt.
			   "ORDER BY update_date DESC, chart_master_table.id DESC LIMIT 0,1 ";
		$res = sqlQuery($sql);
		return $res;
	}
	
	function getChartOcuHxInfo($finalize_flag=0){
		//default
		$strOcuMed2 = $octMeds = $complaint1StrDB = 
		$complaint2StrDB = $complaint3StrDB = $complaintHeadDB = $selectedHeadDB = $titleHeadDB = "";
		
		$sql = "SELECT ".
				"pr_is_id, ocularMeds, complaint1Str, complaint2Str, complaint3Str, complaintHead, selectedHeadText, titleHead ".
				"FROM chart_left_provider_issue ".
				"WHERE patient_id = '".$this->pid."' AND form_id = '".$this->fid."' ";
		$row = sqlQuery($sql);
		if(($row == false) && ($finalize_flag == 0)){
			// New
			$elem_prIsId = "0";
			$elem_editModePrIs = "0";
			// Past
			//$row = valNewRecordLeftView($patient_id);
			$row = $this->getValforNewRecord_OcuHx();
		}else{
			// Update
			$elem_prIsId = $row["pr_is_id"];
			$elem_editModePrIs = "1";
		}
		if($row != false){
			
			if($elem_editModePrIs == "1"){
				$complaint1StrDB = jsEscape($row["complaint1Str"]);
				$complaint2StrDB = jsEscape($row["complaint2Str"]);
				$complaint3StrDB = jsEscape($row["complaint3Str"]);
				$complaintHeadDB = jsEscape($row["complaintHead"]);
				$selectedHeadDB = jsEscape($row["selectedHeadText"]);
				$titleHeadDB = jsEscape($row["titleHead"]);
			}

			//Ocular Meds
			//$sepOctMeds = "<+OMeds&%+>";
			$octMeds = stripslashes($row["ocularMeds"]);	

			
		}
		
		
		
		
		$ar=array();
		if(isset($elem_prIsId)){$ar["elem_prIsId"]=$elem_prIsId;}
		if(isset($elem_editModePrIs)){$ar["elem_editModePrIs"]=$elem_editModePrIs;}
		if(isset($elem_prIsId)){$ar["elem_prIsId"]=$elem_prIsId;}
		if(isset($elem_editModePrIs)){$ar["elem_editModePrIs"]=$elem_editModePrIs;}
		if(isset($complaint1StrDB)){$ar["complaint1StrDB"]=$complaint1StrDB;}
		if(isset($complaint2StrDB)){$ar["complaint2StrDB"]=$complaint2StrDB;}
		if(isset($complaint3StrDB)){$ar["complaint3StrDB"]=$complaint3StrDB;}
		if(isset($complaintHeadDB)){$ar["complaintHeadDB"]=$complaintHeadDB;}
		if(isset($selectedHeadDB)){$ar["selectedHeadDB"]=$selectedHeadDB;}
		if(isset($titleHeadDB)){$ar["titleHeadDB"]=$titleHeadDB;}
		if(isset($octMeds)){$ar["octMeds"]=$octMeds;}
		return ($ar);
		
	}
	
	function separateOtherRvs($str,$retStr=0){
		$sep = "<+O@#+>";
		if($retStr == 1){
			$ret = (!empty($str)) ? trim(str_replace($sep, ",", $str)) : "" ;
			return (!empty($ret) && ($ret != ",")) ? $ret : "";
		}else {
			$arr=array();
			$elem_other="";
			if(!empty($str)){
				$tmp = explode($sep,$str);
				$arr = explode(",",$tmp[0]);
				$elem_other = $tmp[1];
			}
			return array($arr,$elem_other);
		}
	}
	
	function getRVSSummary(){
		//--
		$finalize_flag=$_POST["finalize_flag"];
		$form_id=$_POST["form_id"];
		$patient_id=$_SESSION["patient"];		
		if(empty($form_id)||empty($patient_id)){exit;}

		//chart_left_provider_issue ------
		//default
		$arr_vpDis=$arr_vpMidDis=$arr_vpNear=$arr_vpGlare=$arr_vpGlare=$arr_vpOther=$arr_irrLidsExt=$arr_irrOcu=array();
		$arr_postSegFloat=$arr_postSegFL=$arr_postSegAmsler=$arr_neuroDblVis=$arr_neuroTAS=$arr_neuroVisLoss=array();
		$arr_neuroHeadaches=$arr_neuroMigHead=$arr_neuroMigHeadAura=array();
		$arr_fuPostOp=$arr_fuFollowUp=array();
		$elem_vpDisOther=$elem_vpMidDisOther=$elem_vpNearOther=$elem_vpOtherOther=$vpComment=$vpDate=$elem_irrLidsExtOther="";
		$elem_irrOcuItchingType=$elem_irrOcuPresSensType=$elem_irrOcuOther=$elem_postSegSpots=$elem_postSegFloatCobwebs="";
		$elem_postSegFloatBlackSpots=$elem_postSegFLSparks=$elem_postSegFLBolts=$elem_postSegFLArcs=$elem_postSegFLStrobe="";
		$elem_postSegAmslerOther=$elem_neuroTASOther=$elem_neuroVisLossOther=$elem_neuroMigHeadAuraOther=$elem_neuroOther="";
		$elem_fuPostOp_other=$elem_fuFollowUp_other="";

		$sql = "SELECT * FROM chart_left_provider_issue WHERE patient_id = '".$this->pid."' AND form_id = '".$this->fid."' ";
		$row = sqlQuery($sql);
		if(($row == false) && ($finalize_flag == 0)){
			// New
			$elem_prIsId = "0";
			$elem_editModePrIs = "0";
			// Past
			$row = $this->getValforNewRecord_OcuHx();
		}else{
			// Update
			$elem_prIsId = $row["pr_is_id"];
			$elem_editModePrIs = "1";
		}
		if($row != false){
			//Rvs
			if($elem_editModePrIs == "1"){
				$complaint1StrDB = $row["complaint1Str"];
				$complaint2StrDB = $row["complaint2Str"];
				$complaint3StrDB = $row["complaint3Str"];
				$complaintHeadDB = $row["complaintHead"];
				$selectedHeadDB = $row["selectedHeadText"];
				$titleHeadDB = $row["titleHead"];
				$lidsYesNo = trim($row["lidsYesNo"]);
				$noFlashing = $row["noFlashing"];
				$noFloaters = $row["noFloaters"];
				$vpDistance = $row["vpDistance"];
				$THAYesNo = $row["THAYesNo"];

				list($arr_vpDis,$elem_vpDisOther) = $this->separateOtherRvs($vpDistance);

				$vpMidDistance = $row["vpMidDistance"];
				list($arr_vpMidDis,$elem_vpMidDisOther) = $this->separateOtherRvs($vpMidDistance);

				$vpNear = $row["vpNear"];
				list($arr_vpNear,$elem_vpNearOther) = $this->separateOtherRvs($vpNear);

				$vpGlare = $row["vpGlare"];
				$arr_vpGlare = explode(",",$row["vpGlare"]);

				$vpOther = $row["vpOther"];
				list($arr_vpOther,$elem_vpOtherOther) = $this->separateOtherRvs($vpOther);

				$irrLidsExternal = $row["irrLidsExternal"];
				list($arr_irrLidsExt,$elem_irrLidsExtOther) = $this->separateOtherRvs($irrLidsExternal);

				$sepType="<+type%$+>";
				$irrOcular = $row["irrOcular"];
				$arrTemp = explode($sepType,$irrOcular);
				$irrOcularTemp=$arrTemp[0];
				$elem_irrOcuItchingType=$arrTemp[1];
				$elem_irrOcuPresSensType=$arrTemp[2];
				list($arr_irrOcu,$elem_irrOcuOther) = $this->separateOtherRvs($irrOcularTemp);

				$psSpots = $row["psSpots"];
				$elem_postSegSpots = $psSpots;

				$sepFloat="<+Float*&+>";
				$psFloaters = $row["psFloaters"];
				$arrTemp = explode($sepFloat,$psFloaters);
				$arr_postSegFloat=explode(",",$arrTemp[0]);
				$elem_postSegFloatCobwebs=$arrTemp[1];
				$elem_postSegFloatBlackSpots=$arrTemp[2];

				$sepFL = "<+FL@^+>";
				$psFlashingLights = $row["psFlashingLights"];
				$arrTemp = explode($sepFL,$psFlashingLights);
				$arr_postSegFL=explode(",",$arrTemp[0]);
				$elem_postSegFLSparks=$arrTemp[1];
				$elem_postSegFLBolts=$arrTemp[2];
				$elem_postSegFLArcs=$arrTemp[3];
				$elem_postSegFLStrobe=$arrTemp[4];

				$psAmslerGrid = $row["psAmslerGrid"];
				list($arr_postSegAmsler,$elem_postSegAmslerOther) = $this->separateOtherRvs($psAmslerGrid);

				$neuroDblVision = $row["neuroDblVision"];
				$arr_neuroDblVis = explode(",",$neuroDblVision);

				$neuroTempArtSymp = $row["neuroTempArtSymp"];
				list($arr_neuroTAS,$elem_neuroTASOther) = $this->separateOtherRvs($neuroTempArtSymp);
				$neuroVisionLoss = $row["neuroVisionLoss"];
				list($arr_neuroVisLoss,$elem_neuroVisLossOther) = $this->separateOtherRvs($neuroVisionLoss);
				$neuroHeadaches = $row["neuroHeadaches"];
				$arr_neuroHeadaches = explode(",",$neuroHeadaches);
				$sepMig = "<+Mig&$+>";
				$neuroMigHead = $row["neuroMigHead"];
				$arrTemp = explode($sepMig,$neuroMigHead);
				list($arr_neuroMigHead,$elem_neuroMigHeadOther) =  $this->separateOtherRvs($arrTemp[0]);
				list($arr_neuroMigHeadAura,$elem_neuroMigHeadAuraOther) =  $this->separateOtherRvs($arrTemp[1]);

				$rvspostop = $row["rvspostop"];
				list($arr_fuPostOp,$elem_fuPostOp_other) = $this->separateOtherRvs($rvspostop);
				$rvsfollowup = $row["rvsfollowup"];
				list($arr_fuFollowUp,$elem_fuFollowUp_other) = $this->separateOtherRvs($rvsfollowup);

				$neuroOther = $row["neuroOther"];
				$elem_neuroOther = $neuroOther;

				//PtInfo Desc
				//$elem_ptinfoDesc = $row["ptinfoDesc"];
				$vpComment = stripslashes($row["vpComment"]);
				$vpDate = wv_formatDate($row["vpDate"]);

			}
		}
		
		//HPI Elements
		$ar_wv_hpi_opts=array();
		$ohpi = new HPI();
		$ar_wv_hpi_val=array(	"Vision Problem"=>array("Distance"=>$arr_vpDis, "Near"=>$arr_vpNear, "Glare"=>$arr_vpGlare, "Mid Distance"=>$arr_vpMidDis, "Other"=>$arr_vpOther),
						"Irritation"=>array("Lids - External"=>$arr_irrLidsExt, "Ocular"=>$arr_irrOcu),
						"Post Segment"=>array("Flashing Lights"=>$arr_postSegFL, "Floaters"=>$arr_postSegFloat, "Amsler Grid"=>$arr_postSegAmsler),
						"Neuro"=>array("Double Vision"=>$arr_neuroDblVis, "Temporal Arteritis Symptoms"=>$arr_neuroTAS, "Headaches"=>$arr_neuroHeadaches, "Migraine Headaches"=>$arr_neuroMigHead, "Loss of Vision"=>$arr_neuroVisLoss),
						"Follow-up"=>array("Post-op"=>$arr_fuPostOp, "Follow-up"=>$arr_fuFollowUp)							
						);
		$ar_wv_hpi_opts = $ohpi->get_wv_hpi($ar_wv_hpi_val);
		//HPI Elements		
		
		$tmp = str_replace("\x", "\\x", $GLOBALS['incdir']);
		include($tmp."/chart_notes/rvs_2.php");
		$out2 = ob_get_contents();
		ob_end_clean();
		return $out2; 
		
		//--
	}
	
	static function refineCcHxStr($reason){
		//Reason with out initial string (need to do in javascript also. pending now)
		$ptrn = "/\s*A\s*(\d)+\s*(years|months|days)\s*old\s*(Male|Female)\s*with\s*history\s*of\s*(\r)?\s*/";		
		if(preg_match($ptrn,$reason)){
			$reason = trim(preg_replace($ptrn,"",$reason));
		}
		return $reason;
	}
	
	static function getRvsDoneLevel_php($str){		
		$arrLoc = array("Right Eye","Left Eye","Both Eyes","Right Eyelids","Left Eyelids","RUL","RLL","LUL","LLL","Peripheral vision","Paraxial vision","Central vision",
						"Head","left side of head","right side of head","left side of face","right side of face","fore head","scalp","selLocOpt", "selLocOther");
		$arrQuality = array("Stabbing","Dull","Aching","Doing well","Feels improvement","Tolerating","Quality~Worsening","Quality~Stable","Resolved","No changes noted","New",
						"Quality~Increased","Quality~Decreased","Initially improved then worsened",
						"Quality~Burning","distorted","dry","foggy","ghosting","Quality~glare","hazy","Quality~itching","pressure","scratchy","sharp","throbbing","watery",
						"Quality~patient unsure","since surgery","since birth","since childhood","since last visit","many years","otherQty");
		$arrSeverity = array("Mild","Moderate","Severe","None",'Severity~Intermittent','Severity~Constant',"Decreased","Increased","Worsening","scale1To10","rvs_other_svrity");
		//$arrDuration = array("selectNo-","selectDate-");
		//$arrTimeOnset = array("Sudden","Gradual","Constant","Comes and Goes","Patient unsure","otherTiming","onSetDate","elem_sp_surtype","elem_sp_surdate");
		$arrTimeOnset = array("selectNo-","selectDate-","elem_sp_surtype","elem_sp_surdate","other_onset","Many years", "Patient unsure", "Since surgery", "Since birth","Since childhood","Since last visit");
		$arrContext = array("Reading","driving","outside","inside","otherContext");
		$arrModFact = array("MF~inside","outdoors","night","distance vision than near","near vision than distance",
						"when transition from dark to light","when transition from light to dark","in dim light","early in the day",
						"late in the day","with intensive visual activity (readingcomma computer)",
						"with daily activities","makesBetter-", "makesWorse-","otherFactors","rvs_painrelievedby");
		$arrAssocSign = array("Pain","Itching","Burning","Headache","Redness","Light sensitivity","Irritation","Blurry Vision","Halos","Foreign body sensation",
							"Sharp",'ASAS~Dull',"Ache","Headaches",
						"Eye pain","double vision","flashes","floater","ocular redness","glare","dizziness or light-headedness","weakness","tearing",
						"high ocular pressure","none","otherSymptoms");
		$arrDoe=array("DoE~Intermittent","DoE~Uncertain","selectDoE","Sudden","Gradual","Constant","otherDoe","onSetDate");
		$arrVis=array("Blurry","Improved","Worse","Stable","rvsdet_otherVision");
		$arrDip=array("Dip~None","Dip~Mild","Dip~Improved","Dip~Worse");
		$arrMed=array("is following medication instructions","is not taking medications","ran out of meds","has finished meds as instructed","needs med refill","rvs_ranoutmeds","rvs_needs_med_refill");		
		$arrCI = array("otherFollowCareInstruct");		
		$arrother=array("rvs_followup_detail_other");		
		$arrParNeg = array("no eye Pain","no Itching","no Tearing","no Flashes","no floaters","no Glare",
						"no Red Eye","no Headache","no Dryness","no Distortion","no Change in Amsler Grid",
						"no Ocular Trauma",
						"no Pain with Eye Movement",
						"no Loss of Consciousness",
						"no Visual Phenomenon",
						"no Pain or Tearing",
						"no ShadowcommaCurtain or Veil",
						"no Flashescommafloatercommashadowcommacurtain or Veil",
						"no FevercommaWeight LosscommaScalp TendernesscommaHeadache or Jaw Claudication","other_par_neg");
		
		
		//$arrAll = array_merge($arrLoc,$arrQuality,$arrSeverity,$arrDuration,$arrTimeOnset,$arrContext,$arrModFact,$arrAssocSign);//$arrDuration,
		//$arrVis,$arrDip,$arrCI,$arrother,$arrParNeg
		$arrAll = array($arrLoc,$arrQuality,$arrSeverity,$arrTimeOnset,$arrContext,$arrModFact,$arrAssocSign,$arrDoe,$arrMed);
		
		$ret=1;
		$cntr=0;
		if(trim($str) != ""){			
			$len = count($arrAll);			
			for($i=0;$i<$len;$i++){				
				$inArr=$arrAll[$i];
				foreach($inArr as $key=> $val){
					if((strpos($str,$val) !== false)){
						$cntr+=1;
						break;
					}
				}
				
				if($cntr>=4){
					break;
				}
			}
		}
		$ret = ($cntr >= 4 ) ? 3 : 2;
		return $ret;
	}
	
	function get_cc_hx_popup(){
		$str="";
		$check_data = "select DATE_FORMAT(date_of_service,'".get_sql_date_format('','y')."') AS date_of_service2, 
				chart_left_cc_history.date_of_service,
				chart_left_cc_history.reason,
				chart_left_cc_history.ccompliant,
				chart_left_cc_history.pro_id,
				chart_left_cc_history.cosigner_id
				from chart_left_cc_history  ".
				"where patient_id = '".$this->pid."' ".
				" order by date_of_service DESC, form_id DESC ";
		$checkSql = sqlStatement($check_data);
		$checkrows = imw_num_rows($checkSql);
		if($checkrows>0){ 
			while($checkl=sqlFetchArray($checkSql)){
				$tmp= trim($checkl['ccompliant']."\n".$checkl['reason']);
				$name=nl2br($tmp);
				$begdate=(!empty($checkl['date_of_service2']) && ($checkl['date_of_service2'] != "00-00-00")) ? $checkl['date_of_service2'] : "";
				$begdate2=(!empty($checkl['date_of_service']) && ($checkl['date_of_service'] != "0000-00-00")) ? wv_formatDate($checkl['date_of_service']) : "";				
				
				$nmTech =  "";
				if(!empty($checkl["pro_id"])){
					$ousr = new User($checkl["pro_id"]);
					$nmTech = $ousr->getName(3);					
				}				
				
				$nmCoTech = "";
				if(!empty($checkl["cosigner_id"])){
					$ousr = new User($checkl["cosigner_id"]);
					$nmCoTech = $ousr->getName(3);					
				}
				
				$str.="
					<tr valign=\"top\">
						<td nowrap title=\"".$begdate2."\">".$begdate."</td>
						<td >".$name."</td>
						<td nowrap>".$nmTech."</td>
						<td nowrap>".$nmCoTech."</td>
					</tr>
				";
				
			
			}			
		}
		
		//
		if(!empty($str)){
			$str="<table class=\"table table-bordered table-striped\">".
				"<tr>
				<th  >DOS</th>
				<th >Reason</th>
				<th  nowrap>Tech</th>
				<th  nowrap>Co-Tech</th></tr>".
				$str."</table>";
		}else{
			$str="No record found.";
		}		
		
		$str = "<div id=\"cchx123\" class=\"table-responsive\">".$str."</div>";
		
		echo $str;
	
	}
	
	function genHealthReviewd($finalize_flag){
		$arr=array();
		$pid=$this->pid; //$_SESSION["patient"];
		$mId = $_POST["elem_mId"];
		$oMedHx = new MedHx($pid);
		$oMedHx->setFormId($this->fid);
		$tmp = $oMedHx->setPtLExam($mId);
		$msg = $_POST["elem_owner_msg"];
		
		if(!empty($msg)){
			//change Ownership
			if(!empty($_SESSION["authId"])){
				$new_pro_id=$_SESSION["authId"];
				$oUser = new User($new_pro_id);
				$new_pro_id_type = $oUser->getUType(1);
				$pro_id_2 = 0;  $cosigner_id_2 = 0;
				$sql ="SELECT pro_id, cosigner_id FROM chart_left_cc_history WHERE form_id='".$mId."' AND patient_id='".$pid."' ";
				$row = sqlQuery($sql);				
				if($row != false){
					$pro_id = $row["pro_id"];
					$cosigner_id = $row["cosigner_id"];
					
					if(!empty($pro_id)){
						$oUser = new User($pro_id);
						$pro_id_type = $oUser->getUType(1);
						if($new_pro_id==$pro_id){$pro_id=0;$pro_id_type =0;}
					}
					if(!empty($cosigner_id)){
						$oUser = new User($cosigner_id);
						$cosigner_id_type = $oUser->getUType(1);
						if($new_pro_id==$cosigner_id){$cosigner_id=0;$cosigner_id_type=0;}
						if(!empty($cosigner_id) && $pro_id==$cosigner_id){$cosigner_id=0;$cosigner_id_type=0;}
					}

					// 1 --
					if(!empty($new_pro_id) && $new_pro_id_type==1){ //pro id
						if(empty($pro_id_2)){ $pro_id_2 = $new_pro_id; }
						else if(empty($cosigner_id_2)){ $cosigner_id_2 = $new_pro_id; }	
					}
					
					if(!empty($pro_id) && $pro_id_type==1){ //pro id
						if(empty($pro_id_2)){ $pro_id_2 = $pro_id; }
						else if(empty($cosigner_id_2)){ $cosigner_id_2 = $pro_id; }	
					}
					
					if(!empty($cosigner_id) && $cosigner_id_type==1){ //pro id
						if(empty($pro_id_2)){ $pro_id_2 = $cosigner_id; }
						else if(empty($cosigner_id_2)){ $cosigner_id_2 = $cosigner_id; }	
					}
					
					// 3 ----
					if(!empty($new_pro_id) && $new_pro_id_type==3){ //pro id
						if(empty($pro_id_2)){ $pro_id_2 = $new_pro_id; }
						else if(empty($cosigner_id_2)){ $cosigner_id_2 = $new_pro_id; }	
					}
					
					if(!empty($pro_id) && $pro_id_type==3){ //pro id
						if(empty($pro_id_2)){ $pro_id_2 = $pro_id; }
						else if(empty($cosigner_id_2)){ $cosigner_id_2 = $pro_id; }	
					}
					
					if(!empty($cosigner_id) && $cosigner_id_type==3){ //pro id
						if(empty($pro_id_2)){ $pro_id_2 = $cosigner_id; }
						else if(empty($cosigner_id_2)){ $cosigner_id_2 = $cosigner_id; }	
					}
					
					//chart_left_cc_history
					$sql = "UPDATE chart_left_cc_history SET pro_id='".$pro_id_2."', cosigner_id='".$cosigner_id_2."' WHERE form_id='".$mId."' AND patient_id='".$pid."' ";
					$row = sqlQuery($sql);					
				}else{
					$pro_id_2 = $new_pro_id;  
				}
				
				//chart_left_provider_issue
				$sql = "UPDATE chart_left_provider_issue SET uid='".$new_pro_id."' WHERE form_id='".$mId."' AND patient_id='".$pid."' ";
				$row = sqlQuery($sql);
			}
		}
		//
		$arr["tmp"]=$tmp;
		if(!empty($pro_id_2)){
			$ousr = new User($pro_id_2);
			$arr["proid"]=$pro_id_2;				
			$arr["pro_nm"]=$ousr->getName(7); 
			$arr["pro_nm"] = str_replace("&nbsp;"," ",$arr["pro_nm"]);	
		}
		if(!empty($cosigner_id_2)){
			$ousr = new User($cosigner_id_2);
			$arr["cosignid"] = $cosigner_id_2;			
			$arr["cosign_nm"]=$ousr->getName(7); 
			$arr["cosign_nm"] = str_replace("&nbsp;"," ",$arr["cosign_nm"]);
		}
		
		//status MedHX
		//Med Hx Status
		$o_core_notifications = new core_notifications();
		$noti_genhealth = $o_core_notifications->get_genhealth_noti();
		$arr["noti_genhealth"] = $noti_genhealth;
		
		echo json_encode($arr);
	}
	
	
}



?>