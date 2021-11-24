<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: FundusExam.php
Coded in PHP7
Purpose: This class provides Fundus Exam related chart note functions.
Access Type : Include file
*/
?>
<?php
//FundusExam.php
class FundusExam extends ChartNote{
	public $examName;
	private $tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_rv";
		$this->examName="FundusExam";
	}

	function isRecordExists($a="",$b="",$c="",$d=""){
		$form_id = $this->fid;
		$patient_id = $this->pid;

		//Optic
		$oON=new OpticNerve($patientId,$form_id);
		$oVitreous = new Vitreous($patientId, $form_id);
		$oMacula = new Macula($patientId, $form_id);
		$oBV = new BloodVessels($patientId, $form_id);
		$oPeriphery = new Periphery($patientId, $form_id);
		$oRetinalExam = new RetinalExam($patientId, $form_id);
		$oChartDraw = new ChartDraw($patientId, $form_id,$this->examName);
		if($oON->isRecordExists() || $oVitreous->isRecordExists() || $oMacula->isRecordExists() || $oBV->isRecordExists() || $oPeriphery->isRecordExists() || $oRetinalExam->isRecordExists() || $oChartDraw->isRecordExists()){
			return true;
		}
		return false;
	}

	function carryForward(){
		$form_id = $this->fid;
		$patientId = $this->pid;

		$oON=new OpticNerve($patientId,$form_id);
		if(!$oON->isRecordExists()){	$oON->carryForward();	}

		$oVitreous = new Vitreous($patientId, $form_id);
		if(!$oVitreous->isRecordExists()){
				$oVitreous->carryForward();
		}

		$oMacula = new Macula($patientId, $form_id);
		if(!$oMacula->isRecordExists()){
				$oMacula->carryForward();
		}

		$oBV = new BloodVessels($patientId, $form_id);
		if(!$oBV->isRecordExists()){
				$oBV->carryForward();
		}

		$oPeriphery = new Periphery($patientId, $form_id);
		if(!$oPeriphery->isRecordExists()){
				$oPeriphery->carryForward();
		}

		$oRetinalExam = new RetinalExam($patientId, $form_id);
		if(!$oRetinalExam->isRecordExists()){
			$oRetinalExam->carryForward();
		}

		$oChartDraw = new ChartDraw($patientId, $form_id,$this->examName);
		if(!$oChartDraw->isRecordExists()){
			$oChartDraw->carryForward();
		}
	}

	function getSubExamInfo($subExm){
		$arr=array();
		switch($subExm){
			case "Vit":
			case "Vitreous":
				$arr["db"]["xmlOd"] = "vitreous_od";
				$arr["db"]["xmlOs"] = "vitreous_os";
				$arr["db"]["wnlSE"] = "wnlVitreous";
				$arr["db"]["wnlOd"] = "wnlVitreousOd";
				$arr["db"]["wnlOs"] = "wnlVitreousOs";
				$arr["db"]["posSE"] = "posVitreous";
				$arr["db"]["summOd"] = "vitreous_od_summary";
				$arr["db"]["summOs"] = "vitreous_os_summary";
				$arr["divSe"] = "1";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/vitreous_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/vitreous_os.xml";
			break;

			case "Macula":
				$arr["db"]["xmlOd"] = "macula_od";
				$arr["db"]["xmlOs"] = "macula_os";
				$arr["db"]["wnlSE"] = "wnlMacula";
				$arr["db"]["wnlOd"] = "wnlMaculaOd";
				$arr["db"]["wnlOs"] = "wnlMaculaOs";
				$arr["db"]["posSE"] = "posMacula";
				$arr["db"]["summOd"] = "macula_od_summary";
				$arr["db"]["summOs"] = "macula_os_summary";
				$arr["divSe"] = "2";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/macula_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/macula_os.xml";
			break;

			case "Peri":
				$arr["db"]["xmlOd"] = "periphery_od";
				$arr["db"]["xmlOs"] = "periphery_os";
				$arr["db"]["wnlSE"] = "wnlPeri";
				$arr["db"]["wnlOd"] = "wnlPeriOd";
				$arr["db"]["wnlOs"] = "wnlPeriOs";
				$arr["db"]["posSE"] = "posPeri";
				$arr["db"]["summOd"] = "periphery_od_summary";
				$arr["db"]["summOs"] = "periphery_os_summary";
				$arr["divSe"] = "3";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/periphery_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/periphery_os.xml";
			break;

			case "Vessels":
			case "BV":
				$arr["db"]["xmlOd"] = "blood_vessels_od";
				$arr["db"]["xmlOs"] = "blood_vessels_os";
				$arr["db"]["wnlSE"] = "wnlBV";
				$arr["db"]["wnlOd"] = "wnlBVOd";
				$arr["db"]["wnlOs"] = "wnlBVOs";
				$arr["db"]["posSE"] = "posBV";
				$arr["db"]["summOd"] = "blood_vessels_od_summary";
				$arr["db"]["summOs"] = "blood_vessels_os_summary";
				$arr["divSe"] = "4";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/bloodVessels_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/bloodVessels_os.xml";
			break;

			case "Ret":
			case "Retinal":
				$arr["db"]["xmlOd"] = "retinal_od";
				$arr["db"]["xmlOs"] = "retinal_os";
				$arr["db"]["wnlSE"] = "wnlRetinal";
				$arr["db"]["wnlOd"] = "wnlRetinalOd";
				$arr["db"]["wnlOs"] = "wnlRetinalOs";
				$arr["db"]["posSE"] = "posRetinal";
				$arr["db"]["summOd"] = "retinal_od_summary";
				$arr["db"]["summOs"] = "retinal_os_summary";
				$arr["divSe"] = "7";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/retinal_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/retinal_os.xml";
			break;
			case "CD":
				$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/cd_od.xml";
				$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/cd_os.xml";
			break;

		}

		//--
		if($subExm=="Vit" || $subExm=="Vitreous" || $subExm=="Ret" || $subExm=="Retinal" || $subExm=="Macula" ||
			$subExm=="Periphery" || $subExm=="Peri" || $subExm=="BV" || $subExm=="Vessels"){
			if($subExm=="Vit"){ $subExm="Vitreous"; }
			else if($subExm=="Ret"){ $subExm="Retinal"; }
			else if($subExm=="Peri"){ $subExm="Periphery"; }
			else if($subExm=="BV" || $subExm=="Vessels"){ $subExm="Vessels"; }
			$oExamXml = new ExamXml();
			$tmp = $oExamXml->getExamXmlFiles("Fundus", $subExm);
			$arr["xmlFile"]["OD"] = $tmp["od"];
			$arr["xmlFile"]["OS"] = $tmp["os"];
		}
		//--

		return $arr;
	}

	function smartChart($arr,$subExm){
		$form_id = $this->fid;
		$patient_id = $this->pid;

		if(!$this->isRecordExists()){
			$this->carryForward();
		}

		switch($subExm){
			case "Optic":
				$oOptic = new OpticNerve($patient_id,$form_id);
				$oOptic->smartChart($arr);
			break;
			case "Vit":
				$oVitreous = new Vitreous($patient_id, $form_id);
				$oVitreous->smartChart($arr);
			break;
			case "Macula":
				$oMacula = new Macula($patient_id, $form_id);
				$oMacula->smartChart($arr);
			break;
			case "BV":
				$oBV = new BloodVessels($patient_id, $form_id);
				$oBV->smartChart($arr);
			break;
			case "Peri":
				$oPeriphery = new Periphery($patient_id, $form_id);
				$oPeriphery->smartChart($arr);
			break;
			case "Retinal":
				$oRetinalExam = new RetinalExam($patient_id, $form_id);
				$oRetinalExam->smartChart($arr);
			break;
		}
	}

	//Reset
	function resetVals(){
		$form_id = $this->fid;
		$patientId = $this->pid;

		$oON=new OpticNerve($patientId,$form_id);
		$oON->resetVals();

		$oVitreous = new Vitreous($patientId, $form_id);
		$oVitreous->resetVals();

		$oMacula = new Macula($patientId, $form_id);
		$oMacula->resetVals();

		$oBV = new BloodVessels($patientId, $form_id);
		$oBV->resetVals();

		$oPeriphery = new Periphery($patientId, $form_id);
		$oPeriphery->resetVals();

		$oRetinalExam = new RetinalExam($patientId, $form_id);
		$oRetinalExam->resetVals();

		$oChartDraw = new ChartDraw($patientId, $form_id,$this->examName);
		$oChartDraw->resetVals();
	}

	//function --
	function getDiabetesFromFundus($sum_od, $sum_os, $dm){
		$arr_sum_od_tmp=array("Diabetes"=>"", "NPDR"=>"", "Diabetic macular edema"=>"", "Neovascularization"=>"");
		$arr_sum_os_tmp=array("Diabetes"=>"", "NPDR"=>"", "Diabetic macular edema"=>"", "Neovascularization"=>"");
		//type
		$dm = ($dm == "DM Type 1") ? "1" : "2";
		$code = $dm -1;

		if(!empty($sum_od)){
			$arr_sum_od = explode(";", $sum_od);
			foreach($arr_sum_od as $key => $val){
				if(strpos($val, "Diabetes")!==false ){
					$arr_sum_od_tmp["Diabetes"] = $val;
				}
				if( strpos($val, "NPDR")!==false ){
					$arr_sum_od_tmp["NPDR"] = $val;
				}
				if( strpos($val, "Diabetic macular edema")!==false ){
					$arr_sum_od_tmp["Diabetic macular edema"] = $val;
				}
				if( strpos($val, "Neovascularization")!==false ){
					$arr_sum_od_tmp["Neovascularization"] = $val;
				}
			}
		}

		if(!empty($sum_os)){
			$arr_sum_os = explode(";", $sum_os);
			foreach($arr_sum_os as $key => $val){
				if(strpos($val, "Diabetes")!==false ){
					$arr_sum_os_tmp["Diabetes"] = $val;
				}
				if( strpos($val, "NPDR")!==false ){
					$arr_sum_os_tmp["NPDR"] = $val;
				}
				if( strpos($val, "Diabetic macular edema")!==false ){
					$arr_sum_os_tmp["Diabetic macular edema"] = $val;
				}
				if( strpos($val, "Neovascularization")!==false ){
					$arr_sum_os_tmp["Neovascularization"] = $val;
				}
			}
		}

		// OD --
		if(strpos($sum_od, "No Retinopathy Diabetes")!==false ){
			$arrRet["od"]["assess"] = "Diabetes Type ".$dm." No retinopathy";
			$arrRet["od"]["assess_code"] = "E1".$code.".9";
		}else if( (strpos($sum_od, "No Retinopathy Diabetes")===false &&
						strpos($sum_od, "NPDR")===false &&
						strpos($sum_od, "Diabetic macular edema")===false &&
						strpos($sum_od, "Neovascularization")===false
						)){
			$arrRet["od"]["show_pop_up"] = "1";
		}else if(strpos($sum_od, "NPDR")===false  ){
			$arrRet["od"]["show_pop_up"] = "1";
		}else if( ((strpos($sum_od, "NPDR")!==false && strpos($sum_od, "Absent NPDR")===false && strpos($sum_od, "Diabetic macular edema")===false)  )
					){
			$arrRet["od"]["show_pop_up"] = "1";

			//display pop up with level of NPDR: pending
			$arrRet["od"]["show_pop_up_h_option"] = "";
			if(strpos($arr_sum_od_tmp["NPDR"],"Mild")!==false){
				$arrRet["od"]["show_pop_up_h_option"] = "Mild";
			}else if(strpos($arr_sum_od_tmp["NPDR"],"Moderate")!==false){
				$arrRet["od"]["show_pop_up_h_option"] = "Moderate";
			}else if(strpos($arr_sum_od_tmp["NPDR"],"Severe")!==false){
				$arrRet["od"]["show_pop_up_h_option"] = "Severe";
			}

		}else if( strpos($sum_od, "Absent NPDR")!==false && strpos($sum_od, "Absent Neovascularization")!==false && strpos($sum_od, "Absent Diabetic macular edema")!==false ){
			$arrRet["od"]["assess"] = "DM Type ".$dm." No retinopathy";
			$arrRet["od"]["assess_code"] = "E1".$code.".9";
		}else if(
				((strpos($arr_sum_od_tmp["NPDR"],"Mild")!==false && strpos($arr_sum_od_tmp["Diabetic macular edema"],"Absent")!==false && strpos($arr_sum_od_tmp["Neovascularization"],"Absent")!==false)
				)
				){
			$arrRet["od"]["assess"] = "DM Type ".$dm." Mild without ME";
			$arrRet["od"]["assess_code"] = "E1".$code.".329";
		}else if(
				((strpos($arr_sum_od_tmp["NPDR"],"Mild")!==false && strpos($arr_sum_od_tmp["Diabetic macular edema"],"Present")!==false && strpos($arr_sum_od_tmp["Neovascularization"],"Absent")!==false)
				)
				){
			$arrRet["od"]["assess"] = "DM Type ".$dm." Mild with ME";
			$arrRet["od"]["assess_code"] = "E1".$code.".321";
		}else if(
				((strpos($arr_sum_od_tmp["NPDR"],"Moderate")!==false && strpos($arr_sum_od_tmp["Diabetic macular edema"],"Absent")!==false && strpos($arr_sum_od_tmp["Neovascularization"],"Absent")!==false)
				)
				){
			$arrRet["od"]["assess"] = "DM Type ".$dm." Mod without ME";
			$arrRet["od"]["assess_code"] = "E1".$code.".339";
		}else if(
				((strpos($arr_sum_od_tmp["NPDR"],"Moderate")!==false && strpos($arr_sum_od_tmp["Diabetic macular edema"],"Present")!==false && strpos($arr_sum_od_tmp["Neovascularization"],"Absent")!==false)
				)
				){
			$arrRet["od"]["assess"] = "DM Type ".$dm." Mod with ME";
			$arrRet["od"]["assess_code"] = "E1".$code.".331";
		}else if(
				((strpos($arr_sum_od_tmp["NPDR"],"Severe")!==false && strpos($arr_sum_od_tmp["Diabetic macular edema"],"Absent")!==false && strpos($arr_sum_od_tmp["Neovascularization"],"Absent")!==false)
				)
				){
			$arrRet["od"]["assess"] = "DM Type ".$dm." Severe without ME";
			$arrRet["od"]["assess_code"] = "E1".$code.".339";
		}else if(
				((strpos($arr_sum_od_tmp["NPDR"],"Severe")!==false && strpos($arr_sum_od_tmp["Diabetic macular edema"],"Present")!==false && strpos($arr_sum_od_tmp["Neovascularization"],"Absent")!==false)
				)
				){
			$arrRet["od"]["assess"] = "DM Type ".$dm." Severe with ME";
			$arrRet["od"]["assess_code"] = "E1".$code.".331";
		}else if(
				((strpos($arr_sum_od_tmp["Diabetic macular edema"],"Absent")!==false && strpos($arr_sum_od_tmp["Neovascularization"],"Present")!==false)
				)
				){
			$arrRet["od"]["assess"] = "DM Type ".$dm." Proliferative without ME";
			$arrRet["od"]["assess_code"] = "E1".$code.".359";
		}else if(
				((strpos($arr_sum_od_tmp["Diabetic macular edema"],"Present")!==false && strpos($arr_sum_od_tmp["Neovascularization"],"Present")!==false)
				)
				){
			$arrRet["od"]["assess"] = "DM Type ".$dm." Proliferative with ME";
			$arrRet["od"]["assess_code"] = "E1".$code.".351";
		}else{
			$arrRet["od"]["show_pop_up"] = "1";
		}

		// OS--
		if((strpos($sum_os, "No Retinopathy Diabetes")!==false) ){
			$arrRet["os"]["assess"] = "Diabetes Type ".$dm." No retinopathy";
			$arrRet["os"]["assess_code"] = "E1".$code.".9";
		}else if( ( strpos($sum_os, "No Retinopathy Diabetes")===false &&
						strpos($sum_os, "NPDR")===false &&
						strpos($sum_os, "Diabetic macular edema")===false &&
						strpos($sum_os, "Neovascularization")===false
						)){
			$arrRet["os"]["show_pop_up"] = "1";
		}else if(strpos($sum_os, "NPDR")===false ){
			$arrRet["os"]["show_pop_up"] = "1";
		}else if( ((strpos($sum_os, "NPDR")!==false && strpos($sum_os, "Absent NPDR")===false && strpos($sum_os, "Diabetic macular edema")===false) )
					){
			$arrRet["os"]["show_pop_up"] = "1";

			//display pop up with level of NPDR: pending
			$arrRet["os"]["show_pop_up_h_option"] = "";
			if(strpos($arr_sum_od_tmp["NPDR"],"Mild")!==false){
				$arrRet["os"]["show_pop_up_h_option"] = "Mild";
			}else if(strpos($arr_sum_od_tmp["NPDR"],"Moderate")!==false){
				$arrRet["os"]["show_pop_up_h_option"] = "Moderate";
			}else if(strpos($arr_sum_od_tmp["NPDR"],"Severe")!==false){
				$arrRet["os"]["show_pop_up_h_option"] = "Severe";
			}

		}else if(  strpos($sum_os, "Absent NPDR")!==false && strpos($sum_os, "Absent Neovascularization")!==false && strpos($sum_os, "Absent Diabetic macular edema")!==false ){
			$arrRet["os"]["assess"] = "DM Type ".$dm." No retinopathy";
			$arrRet["os"]["assess_code"] = "E1".$code.".9";
		}else if(
				(
				(strpos($arr_sum_os_tmp["NPDR"],"Mild")!==false && strpos($arr_sum_os_tmp["Diabetic macular edema"],"Absent")!==false && strpos($arr_sum_os_tmp["Neovascularization"],"Absent")!==false))
				){
			$arrRet["os"]["assess"] = "DM Type ".$dm." Mild without ME";
			$arrRet["os"]["assess_code"] = "E1".$code.".329";
		}else if(
				(
				(strpos($arr_sum_os_tmp["NPDR"],"Mild")!==false && strpos($arr_sum_os_tmp["Diabetic macular edema"],"Present")!==false && strpos($arr_sum_os_tmp["Neovascularization"],"Absent")!==false))
				){
			$arrRet["os"]["assess"] = "DM Type ".$dm." Mild with ME";
			$arrRet["os"]["assess_code"] = "E1".$code.".321";
		}else if(
				(
				(strpos($arr_sum_os_tmp["NPDR"],"Moderate")!==false && strpos($arr_sum_os_tmp["Diabetic macular edema"],"Absent")!==false && strpos($arr_sum_os_tmp["Neovascularization"],"Absent")!==false))
				){
			$arrRet["os"]["assess"] = "DM Type ".$dm." Mod without ME";
			$arrRet["os"]["assess_code"] = "E1".$code.".339";
		}else if(
				(
				(strpos($arr_sum_os_tmp["NPDR"],"Moderate")!==false && strpos($arr_sum_os_tmp["Diabetic macular edema"],"Present")!==false && strpos($arr_sum_os_tmp["Neovascularization"],"Absent")!==false))
				){
			$arrRet["os"]["assess"] = "DM Type ".$dm." Mod with ME";
			$arrRet["os"]["assess_code"] = "E1".$code.".331";
		}else if(
				(
				(strpos($arr_sum_os_tmp["NPDR"],"Severe")!==false && strpos($arr_sum_os_tmp["Diabetic macular edema"],"Absent")!==false && strpos($arr_sum_os_tmp["Neovascularization"],"Absent")!==false))
				){
			$arrRet["os"]["assess"] = "DM Type ".$dm." Severe without ME";
			$arrRet["os"]["assess_code"] = "E1".$code.".339";
		}else if(
				(
				(strpos($arr_sum_os_tmp["NPDR"],"Severe")!==false && strpos($arr_sum_os_tmp["Diabetic macular edema"],"Present")!==false && strpos($arr_sum_os_tmp["Neovascularization"],"Absent")!==false))
				){
			$arrRet["os"]["assess"] = "DM Type ".$dm." Severe with ME";
			$arrRet["os"]["assess_code"] = "E1".$code.".331";
		}else if(
				(
				(strpos($arr_sum_os_tmp["Diabetic macular edema"],"Absent")!==false && strpos($arr_sum_os_tmp["Neovascularization"],"Present")!==false))
				){
			$arrRet["os"]["assess"] = "DM Type ".$dm." Proliferative without ME";
			$arrRet["os"]["assess_code"] = "E1".$code.".359";
		}else if(
				(
				(strpos($arr_sum_os_tmp["Diabetic macular edema"],"Present")!==false && strpos($arr_sum_os_tmp["Neovascularization"],"Present")!==false))
				){
			$arrRet["os"]["assess"] = "DM Type ".$dm." Proliferative with ME";
			$arrRet["os"]["assess_code"] = "E1".$code.".351";
		}else{
			$arrRet["os"]["show_pop_up"] = "1";
		}

		//check eye wise and select sever
		$v_od_assss=$arrRet["od"]["assess"];
		$v_od_assss_code=$arrRet["od"]["assess_code"];
		$v_od_show_pop=$arrRet["od"]["show_pop_up"];
		$v_od_show_h = $arrRet["od"]["show_pop_up_h_option"];

		$v_os_assss=$arrRet["os"]["assess"];
		$v_os_assss_code=$arrRet["os"]["assess_code"];
		$v_os_show_pop=$arrRet["os"]["show_pop_up"];
		$v_os_show_h = $arrRet["os"]["show_pop_up_h_option"];

		$arrRet=array();

		if($v_od_show_pop == "1" || $v_os_show_pop == "1"){
			$arrRet["show_pop_up"]="1";
			$arrRet["show_pop_up_h_option"]="";
			if($v_od_show_h=="Severe" || $v_os_show_h=="Severe"){
				$arrRet["show_pop_up_h_option"]="Severe";
			}else if($v_od_show_h=="Moderate" || $v_os_show_h=="Moderate"){
				$arrRet["show_pop_up_h_option"]="Mod";
			}else if($v_od_show_h=="Mild" || $v_os_show_h=="Mild"){
				$arrRet["show_pop_up_h_option"]="Mild";
			}

		}else{
			$sever_cd="";
			$sever_assss="";

			if($v_od_assss_code != "E1".$code.".9" && $v_os_assss_code != "E1".$code.".9"){
				$v_od_assss_code_tmp = str_replace("E1".$code.".3","", $v_od_assss_code);
				$v_os_assss_code_tmp = str_replace("E1".$code.".3","", $v_os_assss_code);

				$v_diff=abs($v_od_assss_code_tmp-$v_os_assss_code_tmp);

				//if diff is less then 8 bcz 51 is sever than 59 but 51 is sever then 41
				if($v_diff==8){
					if($v_od_assss_code_tmp<$v_os_assss_code_tmp){
						$sever_cd=$v_od_assss_code;
						$sever_assss=$v_od_assss;
					}else{
						$sever_cd=$v_os_assss_code;
						$sever_assss=$v_os_assss;
					}
				}else{
					if($v_od_assss_code_tmp>$v_os_assss_code_tmp){
						$sever_cd=$v_od_assss_code;
						$sever_assss=$v_od_assss;
					}else{
						$sever_cd=$v_os_assss_code;
						$sever_assss=$v_os_assss;
					}
				}

			}else{
				if($v_od_assss_code != "E1".$code.".9"){
					$sever_cd=$v_od_assss_code;
					$sever_assss=$v_od_assss;
				}else{
					$sever_cd=$v_os_assss_code;
					$sever_assss=$v_os_assss;
				}
			}
			$arrRet["assess"] = $sever_assss;
			$arrRet["assess_code"] = $sever_cd;
		}

		return $arrRet;
	}
	//function --

	function check_diabetese(){
		$retArr = array();

		$patient_id = $this->pid; //$_SESSION["patient"];
		$form_id = $this->fid; //$_SESSION["form_id"];
		$pid = $patient_id;
		/*
		if(empty($form_id) && isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
			$form_id = $_SESSION["finalize_id"];
		}*/

		//--
		$flgShowOldPop=0;
		$ocn = new ChartNote($pid, $form_id);
		$dos_ymd = $ocn->getDos(1);
		if($dos_ymd<"2016-10-01"){ $flgShowOldPop=1;  }
		$retArr["flgShowOldPop"] = $flgShowOldPop;
		//--


		//
		$get_diabetes_assess = $_GET["get_diabetes_assess"];
		$set_diabetes_type = $_GET["set_diabetes_type"];
		$get_diabetes_assess_with_com = urldecode($_GET["as"]);
		$dm_asmt_take = $_GET["dm_asmt_take"];
		$z_flg_diab_sb = $_GET["z_flg_diab_sb"];

		if(!empty($set_diabetes_type)){

			$tmp_diabetes_values=$tmp_any_conditions_you="";
			$sql = "SELECT any_conditions_you, diabetes_values FROM general_medicine GM WHERE GM.patient_id  = '".$patient_id."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$tmp_diabetes_values=$row["diabetes_values"];
				$tmp_any_conditions_you=$row["any_conditions_you"];
			}
			//
			if(!empty($tmp_diabetes_values) && strpos($tmp_diabetes_values, $set_diabetes_type)===false){
				$tmp_diabetes_values = $set_diabetes_type.", ".$tmp_diabetes_values;
			}else if(empty($tmp_diabetes_values)){$tmp_diabetes_values = $set_diabetes_type.", ";}
			//
			if(!empty($tmp_any_conditions_you) && strpos($tmp_any_conditions_you, "3,")===false){
				$tmp_any_conditions_you = "3,".$tmp_any_conditions_you;
			}else if(empty($tmp_any_conditions_you)){$tmp_any_conditions_you = "3,";}

			$sql = "UPDATE general_medicine
					SET diabetes_values='".$tmp_diabetes_values."',
					any_conditions_you='".$tmp_any_conditions_you."'
					WHERE patient_id='".$patient_id."'  ";
			$row = sqlQuery($sql);
		}

		//--
		// 1
		$diabetes_values="";
		$sql = "SELECT any_conditions_you, diabetes_values FROM general_medicine GM WHERE GM.patient_id  = '".$patient_id."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			if(strpos($row["any_conditions_you"],"3")!==false){
				$tmp = explode("~|~",$row["diabetes_values"]);
				$diabetes_values = $tmp[0];
			}
		}

		// assessment + db diabetes: if input from assessment it will prevail
		if(!empty($get_diabetes_assess)){
			$diabetes_values =$get_diabetes_assess;//$diabetes_values.";"
		}

		//
		$pt_diabetes_value="";
		$diabetes_values=trim($diabetes_values);
		if(!empty($diabetes_values)){
			//stripos($diabetes_values,"DM Type 2")!==false
			if(preg_match("/^((diabetes|diabetes\s+mellitus|dm)\s+type\s+(2))/i",$diabetes_values)||$diabetes_values == "diabetes 2"||$diabetes_values == "dm 2"||$diabetes_values == "diabetes type 2 no retinopathy"){
				$pt_diabetes_value="DM Type 2";
			}else if(preg_match("/^((diabetes|diabetes\s+mellitus|dm)\s+type\s+(1))/i",$diabetes_values)
				||$diabetes_values == "diabetes 1"||$diabetes_values == "dm 1"||$diabetes_values == "diabetes type 1 no retinopathy"){
				$pt_diabetes_value="DM Type 1";
			}else if(strpos($diabetes_values,"Diet")!==false||strpos($diabetes_values,"NIDDM")!==false||strpos($diabetes_values,"IDDM")!==false){
				//4.	If under General Health/Medical Conditions NIDDM, Diet OR IDDM is checked but DM Type 1 or DM Type 2 IS NOT CHECKED, then present DIABETES TYPE POPUP (see below)
				$retArr["msg"]= "DIABETES TYPE POPUP";
				$retArr["html"]= "<div id=\"dialog-msg-diab\" title=\"DIABETES TYPE POPUP\">
								<p>
									<table cellpadding=\"2\" width=\"100%\" >
										<tr><td style=\"color:purple; cursor:pointer;\" align=\"center\" onclick=\"sb_checkDiabetes('DM Type 1')\" >DM Type 1</td>
											<td style=\"color:purple; cursor:pointer;\" align=\"center\" onclick=\"sb_checkDiabetes('DM Type 2')\" >DM Type 2</td>
										</tr>
									</table>
								</p>
								</div>
								";
			}
		}

		// 2
		if(!empty($pt_diabetes_value)){

			$status_elem=$retina_od_sum=$retina_os_sum= $wnlRetinalOd = $wnlRetinalOs ="";
			$sql = "SELECT statusElem, retinal_od_summary, retinal_os_summary,wnlRetinal, wnlRetinalOd, wnlRetinalOs
					FROM chart_retinal_exam WHERE patient_id = '".$patient_id."' AND form_id='".$form_id."' AND purged='0'  ";
			$row = sqlQuery($sql);
			if($row!=false){
				$status_elem = $row["statusElem"];
				$retina_od_sum = (strpos($status_elem, "div7_Od=1")!==false) ? $row["retinal_od_summary"] : "" ;
				$retina_os_sum = (strpos($status_elem, "div7_Os=1")!==false) ? $row["retinal_os_summary"] : "" ;

				$wnlRetinalOd = (strpos($status_elem, "div7_Od=1")!==false) ? $row["wnlRetinalOd"] : "" ;
				$wnlRetinalOs = (strpos($status_elem, "div7_Os=1")!==false) ? $row["wnlRetinalOs"] : "" ;
			}

			//if(!empty($retina_od_sum) || !empty($retina_os_sum) || $wnlRetinalOd || $wnlRetinalOs ){

				$arrDiabInfo = $this->getDiabetesFromFundus($retina_od_sum, $retina_os_sum, $pt_diabetes_value);

				//
				/* Automated Diabetes coding should have been totally disabed with 2017 icd 10 coding update. */
				if(!empty($arrDiabInfo["assess"]) && !empty($arrDiabInfo["assess_code"])){
					if($flgShowOldPop==1){
						$retArr["assess"] = $arrDiabInfo["assess"];
						$retArr["assess_code"] = $arrDiabInfo["assess_code"];
					}else{
						if($z_flg_diab_sb==2||$z_flg_diab_sb==1){ //when user click on assessment or when user clicked on superbill
							$arrDiabInfo["show_pop_up"] = "1"; //show pop up if DOS is after 30 SEPT 2016 and fundus qualify for code
						}
					}
				}

				if($arrDiabInfo["show_pop_up"] == "1"){

					$retArr["show_pop_up_h_option"]=$arrDiabInfo["show_pop_up_h_option"];

					//get comments + modifiers
					$dm_asmt=$get_diabetes_assess_with_com;
					$dm_cm="";
					$dm_modifier="";
					if(!empty($get_diabetes_assess_with_com) && strpos($get_diabetes_assess_with_com,";")!==false){ $ar_dm_cm=explode(";",$get_diabetes_assess_with_com);
						//if(!empty($ar_dm_cm[1])){$dm_cm=$ar_dm_cm[1];}
						$dm_asmt = $ar_dm_cm[0];
						$ar_dm_cm_ln = count($ar_dm_cm);
						if($ar_dm_cm_ln>1){//
							$dm_cm = trim($ar_dm_cm[$ar_dm_cm_ln-1]);
						}

						if(!empty($dm_cm)){
							$mod_opts = array("mild", "mod", "severe", "proliferative", "without", "with",  "macula",  "rheg", "stable", "both", "right", "left", "pdr", "not", "trd", "det", "me", ",");
							$dm_cm_t = str_replace($mod_opts, "", $dm_cm);
							$dm_cm_t = trim($dm_cm_t);
							if(empty($dm_cm_t)){ $dm_modifier=$dm_cm; $dm_cm = ""; }else{   }
						}

						if(empty($dm_modifier)){
							if($ar_dm_cm_ln>1){//
								$dm_modifier = $ar_dm_cm[1]; //
							}
						}
					}

					//set Modifier
					$ar_dm_modifier[0]="";
					$ar_dm_modifier[1]="";
					if(!empty($dm_modifier)){
						$ar_dm_modifier = explode(",", $dm_modifier);
						$ar_dm_modifier_len = count($ar_dm_modifier);
						/*
						if(count($ar_dm_modifier)>0){
							$ar_def_opts=array("Mild","Mod","Severe","Proliferative","with me","without me","trd macula","trd not macula","trd + rheg det",
											"stable pdr","right","left","both");
							foreach($ar_dm_modifier as $k_adm => $v_adm){
								foreach($ar_def_opts as $k_ado => $v_ado){
									if(strpos($v_adm, $v_ado)!==false){

									}
								}
							}
						}
						*/

						//$dm_cm .= "MOD:".$dm_modifier;

					}




					//$dm_cm=$pt_diabetes_value;

					$tmp_dm = ($pt_diabetes_value=="DM Type 1") ? "1" : "2";
					$tmp_code = $tmp_dm-1;
						$retArr["msg"] = "DIABETES TYPE ".$tmp_dm." POPUP";
						$retArr["html"]= "<div id=\"dialog-msg-diab\" title=\"DIABETES TYPE ".$tmp_dm." POPUP\" data-dxcode=\"E1".$tmp_code.".3---\" data-assess=\"DM Type ".$tmp_dm."\" >
										<p>";

						if(!empty($flgShowOldPop)){	//before 1 oct 2016
						$retArr["html"].="<table cellpadding=\"2\" >
											<tr><td style=\"font-weight:bold;\" id=\"dbts_icd10_1\" colspan=\"4\">Diabetes Type ".$tmp_dm." No retinopathy</td></tr>
											<tr><td onclick=\"sb_checkDiabetes('',this,1,1)\" colspan=\"4\">E1".$tmp_code.".9</td></tr>
											<tr><td style=\"font-weight:bold;\" id=\"dbts_icd10_2\">DM Type ".$tmp_dm." Mild with ME</td><td style=\"font-weight:bold;\" id=\"dbts_icd10_4\">DM Type ".$tmp_dm." Mod with ME</td><td style=\"font-weight:bold;\" id=\"dbts_icd10_6\">DM Type ".$tmp_dm." Severe with ME</td><td style=\"font-weight:bold;\" id=\"dbts_icd10_8\">DM Type ".$tmp_dm." Proliferative with ME</td></tr>
											<tr><td onclick=\"sb_checkDiabetes('',this,2,1)\">E1".$tmp_code.".321</td><td  onclick=\"sb_checkDiabetes('',this,4,1)\">E1".$tmp_code.".331</td><td  onclick=\"sb_checkDiabetes('',this,6,1)\">E1".$tmp_code.".341</td><td  onclick=\"sb_checkDiabetes('',this,8,1)\">E1".$tmp_code.".351</td></tr>
											<tr><td style=\"font-weight:bold;\" id=\"dbts_icd10_3\">DM Type ".$tmp_dm." Mild without ME</td><td style=\"font-weight:bold;\" id=\"dbts_icd10_5\">DM Type ".$tmp_dm." Mod without ME</td><td style=\"font-weight:bold;\" id=\"dbts_icd10_7\">DM Type ".$tmp_dm." Severe without ME</td><td style=\"font-weight:bold;\" id=\"dbts_icd10_9\">DM Type ".$tmp_dm." Proliferative without ME</td></tr>
											<tr><td onclick=\"sb_checkDiabetes('',this,3,1)\">E1".$tmp_code.".329</td><td  onclick=\"sb_checkDiabetes('',this,5,1)\">E1".$tmp_code.".339</td><td  onclick=\"sb_checkDiabetes('',this,7,1)\">E1".$tmp_code.".349</td><td  onclick=\"sb_checkDiabetes('',this,9,1)\">E1".$tmp_code.".359</td></tr>
											<tr><td colspan=\"4\"><textarea id=\"ta_dbts_icd10_cm\" style=\"width:100%;height:40px;\">".$dm_cm."</textarea></td></tr>
										</table>";
						}else{	//after 1 oct 2016

						$retArr["html"].="<table cellpadding=\"2\">
											<tr><td ".(stripos($dm_asmt,"Diabetes Type ".$tmp_dm." No retinopathy")!==false ? " class=\"highlight\" " : "")." style=\"font-weight:bold;\" id=\"dbts_icd10_1\" onclick=\"sb_checkDiabetes('',this,1,1)\" >Diabetes Type ".$tmp_dm." No retinopathy</td></tr>
											<tr><td id=\"dbts_icd10_dx_1\" >E1".$tmp_code.".9</td></tr>
											</table>";

						$retArr["html"].="<table id=\"tbl_dbts_1\" cellpadding=\"2\" style=\"width:100%;\">
										<tr>
											<td style=\"font-weight:bold;width:30%;\" >Laterality</td>
											<td style=\"font-weight:bold;width:30%;\" >Severity</td>
											<td style=\"font-weight:bold;width:30%;\" >Edema</td>
										</tr>

										<tr valign=\"top\">
										<td>
											<table >
											<tr><td ".(stripos($ar_dm_modifier[0],"Right")!==false ? " class=\"highlight\" " : "")." data-md=\"l1\" onclick=\"sb_checkDiabetes('',this,'l1',1)\">Right</td></tr>
											<tr><td ".(stripos($ar_dm_modifier[0],"Left")!==false ? " class=\"highlight\" " : "")." data-md=\"l2\" onclick=\"sb_checkDiabetes('',this,'l2',1)\">Left</td></tr>
											<tr><td ".(stripos($ar_dm_modifier[0],"Both")!==false ? " class=\"highlight\" " : "")." data-md=\"l3\" onclick=\"sb_checkDiabetes('',this,'l3',1)\">Both</td></tr>
											</table>
										</td>

										<td >
											<table >
												<tr><td ".(stripos($ar_dm_modifier[0],"Mild")!==false ? " class=\"highlight\" " : "")." data-md=\"s2\" onclick=\"sb_checkDiabetes('',this,'s2',1)\">Mild</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[0],"Mod")!==false ? " class=\"highlight\" " : "")." data-md=\"s3\" onclick=\"sb_checkDiabetes('',this,'s3',1)\">Mod</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[0],"Severe")!==false ? " class=\"highlight\" " : "")." data-md=\"s4\" onclick=\"sb_checkDiabetes('',this,'s4',1)\">Severe</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[0],"Proliferative")!==false ? " class=\"highlight\" " : "")." data-md=\"s5\" onclick=\"sb_checkDiabetes('',this,'s5',1)\">Proliferative</td></tr>
											</table>
										</td>

										<td>
											<table id=\"tbl_wo_me\" style=\"display:block;\">
												<tr><td ".(stripos($ar_dm_modifier[0],"With ME")!==false ? " class=\"highlight\" " : "")." data-md=\"e1\" onclick=\"sb_checkDiabetes('',this,'e1',1)\">With ME</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[0],"Without ME")!==false ? " class=\"highlight\" " : "")." data-md=\"e9\" onclick=\"sb_checkDiabetes('',this,'e9',1)\">Without ME</td></tr>
											</table>
											<table id=\"tbl_trd\" style=\"display:".(stripos($ar_dm_modifier[0],"Proliferative")!==false ? "block" : "none").";\">
												<tr><td ".(stripos($ar_dm_modifier[0],"TRD Macula")!==false ? " class=\"highlight\" " : "")." data-md=\"e2\" onclick=\"sb_checkDiabetes('',this,'e2',1)\">TRD Macula</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[0],"TRD Not Macula")!==false ? " class=\"highlight\" " : "")." data-md=\"e3\" onclick=\"sb_checkDiabetes('',this,'e3',1)\">TRD Not Macula</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[0],"RHEG DET")!==false ? " class=\"highlight\" " : "")." data-md=\"e4\" onclick=\"sb_checkDiabetes('',this,'e4',1)\">TRD + RHEG DET</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[0],"STABLE PDR")!==false ? " class=\"highlight\" " : "")." data-md=\"e5\" onclick=\"sb_checkDiabetes('',this,'e5',1)\">STABLE PDR</td></tr>
											</table>
										</td>
										</tr>
										</table>";

						//second
						if($ar_dm_modifier_len>1 || (stripos($ar_dm_modifier[0],"Right")!==false) || stripos($ar_dm_modifier[0],"Left")!==false ){

							$retArr["html"].="<table id=\"tbl_dbts_2\" cellpadding=\"2\" style=\"width:100%;\">
										<tr>
											<td style=\"font-weight:bold;width:30%;\" >Laterality</td>
											<td style=\"font-weight:bold;width:30%;\" >Staging</td>
											<td style=\"font-weight:bold;width:30%;\" >Edema</td>
										</tr>

										<tr valign=\"top\">

										<td>
											<table >
											<tr><td ".(stripos($ar_dm_modifier[1],"Right")!==false ? " class=\"highlight\" " : "")." data-md=\"l1\" onclick=\"sb_checkDiabetes('',this,'l1',1)\">Right</td></tr>
											<tr><td ".(stripos($ar_dm_modifier[1],"Left")!==false ? " class=\"highlight\" " : "")." data-md=\"l2\" onclick=\"sb_checkDiabetes('',this,'l2',1)\">Left</td></tr>
											<tr><td ".(stripos($ar_dm_modifier[1],"Both")!==false ? " class=\"highlight\" " : "")." data-md=\"l3\" onclick=\"sb_checkDiabetes('',this,'l3',1)\">Both</td></tr>
											</table>
										</td>

										<td >
											<table >
												<tr><td ".(stripos($ar_dm_modifier[1],"Mild")!==false ? " class=\"highlight\" " : "")." data-md=\"s2\" onclick=\"sb_checkDiabetes('',this,'s2',1)\">Mild</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[1],"Mod")!==false ? " class=\"highlight\" " : "")." data-md=\"s3\" onclick=\"sb_checkDiabetes('',this,'s3',1)\">Mod</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[1],"Severe")!==false ? " class=\"highlight\" " : "")." data-md=\"s4\" onclick=\"sb_checkDiabetes('',this,'s4',1)\">Severe</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[1],"Proliferative")!==false ? " class=\"highlight\" " : "")." data-md=\"s5\" onclick=\"sb_checkDiabetes('',this,'s5',1)\">Proliferative</td></tr>
											</table>
										</td>

										<td>
											<table id=\"tbl_wo_me\" style=\"display:block;\">
												<tr><td ".(stripos($ar_dm_modifier[1],"With ME")!==false ? " class=\"highlight\" " : "")." data-md=\"e1\" onclick=\"sb_checkDiabetes('',this,'e1',1)\">With ME</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[1],"Without ME")!==false ? " class=\"highlight\" " : "")." data-md=\"e9\" onclick=\"sb_checkDiabetes('',this,'e9',1)\">Without ME</td></tr>
											</table>
											<table id=\"tbl_trd\" style=\"display:".(stripos($ar_dm_modifier[1],"Proliferative")!==false ? "block" : "none").";\">
												<tr><td ".(stripos($ar_dm_modifier[1],"TRD Macula")!==false ? " class=\"highlight\" " : "")." data-md=\"e2\" onclick=\"sb_checkDiabetes('',this,'e2',1)\">TRD Macula</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[1],"TRD Not Macula")!==false ? " class=\"highlight\" " : "")." data-md=\"e3\" onclick=\"sb_checkDiabetes('',this,'e3',1)\">TRD Not Macula</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[1],"RHEG DET")!==false ? " class=\"highlight\" " : "")." data-md=\"e4\" onclick=\"sb_checkDiabetes('',this,'e4',1)\">TRD + RHEG DET</td></tr>
												<tr><td ".(stripos($ar_dm_modifier[1],"STABLE PDR")!==false ? " class=\"highlight\" " : "")." data-md=\"e5\" onclick=\"sb_checkDiabetes('',this,'e5',1)\">STABLE PDR</td></tr>
											</table>
										</td>
										</tr>
										</table>";

						}

						//Taking
						$retArr["html"].="<table cellpadding=\"2\" style=\"width:100%;\">
										<tr>
											<td style=\"font-weight:bold;width:30%;\">Taking</td>
											<td style=\"width:30%;\" ".(stripos($dm_asmt_take,"t1")!==false ? " class=\"highlight\" " : "")." data-md=\"t1\" data-asmt=\"Long term (current) use of insulin\" data-dx=\"Z79.4\" onclick=\"sb_checkDiabetes('',this,'t1',1)\">Insulin</td>
											<td style=\"width:30%;\" ".(stripos($dm_asmt_take,"t2")!==false ? " class=\"highlight\" " : "")." data-md=\"t2\" data-asmt=\"Long term (current) use of oral hypoglycemic\" data-dx=\"Z79.84\" onclick=\"sb_checkDiabetes('',this,'t2',1)\">Oral antidiabetic</td>
											</tr>
										</table>";

						$retArr["html"].="<table cellpadding=\"2\" style=\"width:100%;\">
										<tr><td colspan=\"4\"><textarea id=\"ta_dbts_icd10_cm\" style=\"width:100%;height:40px;\">".$dm_cm."</textarea></td></tr>
										</table>";
						}//end else old pop up
						$retArr["html"].=	"</p>
										</div>";

				}

			//}

			//Since patient has Type 1 DM�then DM Typ1 should appear automatically in the assessment line EVEN if nothing else is checked in the retinal exam.
			if(!isset($retArr["assess"]) || empty($retArr["assess"])){
				$retArr["assess"] = $pt_diabetes_value;
			}
		}

		echo json_encode($retArr);
	}

	function save_wnl($elem_wnlOptic=0){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		$exmEye = $_POST["elem_exmEye"];

		//Get Template Procedures ---
		$arrTempProc=array("All");
		if(isset($_POST["artemp"])&&!empty($_POST["artemp"])){
			//$arrTempProc=$_POST["artemp"];
			$arrTempProc = json_decode(gzuncompress(base64_decode($_POST["artemp"])));
		}
		//Get Template Procedures ---

		$arrAlign = $arOptNrv =  $arVitreous = $arMacula = $arBV = $arPeriphery = $arRetinalExam = array();

		if(in_array("Opt. Nev",$arrTempProc)||in_array("All",$arrTempProc)){
			$oOptNrv = new OpticNerve($patient_id, $form_id);
			$arOptNrv = $oOptNrv->save_wnl("get");
			extract($arOptNrv);
			$arrAlign[] = array($posOptic,$wnlOpticOd,$wnlOpticOs,$optic_nerve_od_summary,$optic_nerve_os_summary);
		}

		if(in_array("Vitreous",$arrTempProc)||in_array("All",$arrTempProc)){
			$oVitreous = new Vitreous($patient_id, $form_id);
			$arVitreous = $oVitreous->save_wnl("get");
			extract($arVitreous);
			$arrAlign[] = array($posVitreous,$wnlVitreousOd,$wnlVitreousOs,$vitreous_od_summary,$vitreous_os_summary);
		}

		if(in_array("Macula",$arrTempProc)||in_array("All",$arrTempProc)){
			$oMacula = new Macula($patient_id, $form_id);
			$arMacula = $oMacula->save_wnl("get");
			extract($arMacula);
			$arrAlign[] = array($posMacula,$wnlMaculaOd,$wnlMaculaOs,$macula_od_summary,$macula_os_summary);
		}

		if(in_array("Blood Vessels",$arrTempProc)||in_array("All",$arrTempProc)){
			$oBV = new BloodVessels($patient_id, $form_id);
			$arBV = $oBV->save_wnl("get");
			extract($arBV);
			$arrAlign[] = array($posBV,$wnlBVOd,$wnlBVOs,$blood_vessels_od_summary,$blood_vessels_os_summary);
		}

		if(in_array("Periphery",$arrTempProc)||in_array("All",$arrTempProc)){
			$oPeriphery = new Periphery($patient_id, $form_id);
			$arPeriphery = $oPeriphery->save_wnl("get");
			extract($arPeriphery);
			$arrAlign[] = array($posPeri,$wnlPeriOd,$wnlPeriOs,$periphery_od_summary,$periphery_os_summary);
		}

		if(in_array("Retinal Exam",$arrTempProc) ||in_array("All",$arrTempProc)){
			$oRetinalExam = new RetinalExam($patient_id, $form_id);
			$arRetinalExam = $oRetinalExam->save_wnl("get");
			extract($arRetinalExam);
			$arrAlign[] = array($posRetinal,$wnlRetinalOd,$wnlRetinalOs,$retinal_od_summary,$retinal_os_summary);
		}

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);

		//
		$oWv = new WorkView();
		//Check For Alingment of WNL Values --
		//array($posDraw,$elem_wnlDrawLaOd,$elem_wnlDrawLaOs,$la_drawing,$la_drawing)
		$arrAlign = $oWv->alignWnlVals($arrAlign,$exmEye);

		if(!empty($arrAlign["od"])){
			$arOptNrv["wnl_optic"]=$arOptNrv["wnlOpticOd"]="0";
			$arVitreous["wnlVitreous"]=$arVitreous["wnlVitreousOd"]="0";
			$arMacula["wnlMacula"]=$arMacula["wnlMaculaOd"]="0";
			$arBV["wnlBV"]=$arBV["wnlBVOd"]="0";
			$arPeriphery["wnlPeri"]=$arPeriphery["wnlPeriOd"]="0";
			$arRetinalExam["wnlRetinal"]=$arRetinalExam["wnlRetinalOd"]="0";

		}
		if(!empty($arrAlign["os"])){
			$arOptNrv["wnl_optic"]=$arOptNrv["wnlOpticOs"]="0";
			$arVitreous["wnlVitreous"]=$arVitreous["wnlVitreousOs"]="0";
			$arMacula["wnlMacula"]=$arMacula["wnlMaculaOs"]="0";
			$arBV["wnlBV"]=$arBV["wnlBVOs"]="0";
			$arPeriphery["wnlPeri"]=$arPeriphery["wnlPeriOs"]="0";
			$arRetinalExam["wnlRetinal"]=$arRetinalExam["wnlRetinalOs"]="0";

		}

		//Check For Alingment of WNL Values --

		if(in_array("Opt. Nev",$arrTempProc)||in_array("All",$arrTempProc)){
			$oOptNrv->save_wnl("set", $arOptNrv);
		}

		if(in_array("Vitreous",$arrTempProc)||in_array("All",$arrTempProc)){
			$oVitreous->save_wnl("set", $arVitreous);
		}

		if(in_array("Macula",$arrTempProc)||in_array("All",$arrTempProc)){
			$oMacula->save_wnl("set", $arMacula);
		}

		if(in_array("Blood Vessels",$arrTempProc)||in_array("All",$arrTempProc)){
			$oBV->save_wnl("set", $arBV);
		}

		if(in_array("Periphery",$arrTempProc)||in_array("All",$arrTempProc)){
			$oPeriphery->save_wnl("set", $arPeriphery);
		}

		if(in_array("Retinal Exam",$arrTempProc) ||in_array("All",$arrTempProc)){
			$oRetinalExam->save_wnl("set", $arRetinalExam);
			$oChartDraw->save_wnl();
		}
	}

	function save_no_change(){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		$exmEye = $_POST["elem_exmEye"];

		//Get Template Procedures ---
		$arrTempProc=array("All");
		if(isset($_POST["artemp"])&&!empty($_POST["artemp"])){
			//$arrTempProc=$_POST["artemp"];
			$arrTempProc = json_decode(gzuncompress(base64_decode($_POST["artemp"])));
		}
		//Get Template Procedures ---

		$arrAlign = $arOptNrv =  $arVitreous = $arPeriphery = $arMacula = $arBV = $arRetinalExam = array();

		if(in_array("Opt. Nev",$arrTempProc)||in_array("All",$arrTempProc)){
			$oOptNrv = new OpticNerve($patient_id, $form_id);
			$oOptNrv->save_no_change();
		}

		if(in_array("Vitreous",$arrTempProc)||in_array("All",$arrTempProc)){
			$oVitreous = new Vitreous($patient_id, $form_id);
			$oVitreous->save_no_change();
		}

		if(in_array("Macula",$arrTempProc)||in_array("All",$arrTempProc)){
			$oMacula = new Macula($patient_id, $form_id);
			$oMacula->save_no_change();
		}

		if(in_array("Blood Vessels",$arrTempProc)||in_array("All",$arrTempProc)){
			$oBV = new BloodVessels($patient_id, $form_id);
			$oBV->save_no_change();
		}

		if(in_array("Periphery",$arrTempProc)||in_array("All",$arrTempProc)){
			$oPeriphery = new Periphery($patient_id, $form_id);
			$oPeriphery->save_no_change();
		}

		if(in_array("Retinal Exam",$arrTempProc) ||in_array("All",$arrTempProc)){
			$oRetinalExam = new RetinalExam($patient_id, $form_id);
			$oRetinalExam->save_no_change();

		}

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$oChartDraw->save_no_change();
	}


	function save_peri_cd(){
		$patientId = $this->pid; //$_SESSION["patient"];
		$form_id = $this->fid; //$_REQUEST["fid"];
		if(!empty($patientId) && !empty($form_id)){
			if(!$this->isRecordExists()){
				$this->carryForward();
			}

			if($_REQUEST["elem_saveForm"] == "saveCDRV"){
				$oON = new OpticNerve($patientId, $form_id);
				$oON->saveCDRV();
			}else if($_REQUEST["elem_saveForm"] == "SavePeriphery"){
				if($_REQUEST["ex"] == "Peri"){
					$oPeriphery = new Periphery($patientId, $form_id);
					$oPeriphery->savePeriphery();
				}else{
					$oRetinalExam = new RetinalExam($patientId, $form_id);
					$oRetinalExam->savePeriphery();
				}
			}
		}
		echo "0";
	}

}

?>
