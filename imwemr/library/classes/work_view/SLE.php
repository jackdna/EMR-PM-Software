<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: SLE.php
Coded in PHP7
Purpose: This class file provides functions for Slit Lamp Exam.
Access Type : Include file
*/
?>
<?php
//SLE.php
class SLE extends ChartNote{
	public $examName;
	private $tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_slit_lamp_exam";
		$this->examName="SLE";
	}

	function isRecordExists($a="",$b="",$c="",$d=""){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		
		$oConjunctiva = new Conjunctiva($patient_id, $form_id);
		$oCornea = new Cornea($patient_id, $form_id);
		$oAntChamber = new AntChamber($patient_id, $form_id);
		$oIris = new Iris($patient_id, $form_id);
		$oLens = new Lens($patient_id, $form_id);
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		
		if($oConjunctiva->isRecordExists() || $oCornea->isRecordExists() || $oAntChamber->isRecordExists() || 
			$oIris->isRecordExists() || $oLens->isRecordExists() || $oChartDraw->isRecordExists()){
			return true;
		}
		return false;
	}

	function carryForward(){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		
		$oConjunctiva = new Conjunctiva($patient_id, $form_id); $oConjunctiva->carryForward();
		$oCornea = new Cornea($patient_id, $form_id); $oCornea->carryForward();
		$oAntChamber = new AntChamber($patient_id, $form_id); $oAntChamber->carryForward();
		$oIris = new Iris($patient_id, $form_id); $oIris->carryForward();
		$oLens = new Lens($patient_id, $form_id); $oLens->carryForward();
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName); $oChartDraw->carryForward();
		
	}

	function getSubExamInfo($subExm){
		$arr=array();
		switch($subExm){
			case "Conj":
				$arr["db"]["xmlOd"] = "conjunctiva_od";
				$arr["db"]["xmlOs"] = "conjunctiva_os";
				$arr["db"]["wnlSE"] = "wnlConj";
				$arr["db"]["wnlOd"] = "wnlConjOd";
				$arr["db"]["wnlOs"] = "wnlConjOs";
				$arr["db"]["posSE"] = "posConj";
				$arr["db"]["summOd"] = "conjunctiva_od_summary";
				$arr["db"]["summOs"] = "conjunctiva_os_summary";
				$arr["divSe"] = "1";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/conjunctiva_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/conjunctiva_os.xml";
			break;

			case "Corn":
			case "Cornea":
				$arr["db"]["xmlOd"] = "cornea_od";
				$arr["db"]["xmlOs"] = "cornea_os";
				$arr["db"]["wnlSE"] = "wnlCorn";
				$arr["db"]["wnlOd"] = "wnlCornOd";
				$arr["db"]["wnlOs"] = "wnlCornOs";
				$arr["db"]["posSE"] = "posCorn";
				$arr["db"]["summOd"] = "cornea_od_summary";
				$arr["db"]["summOs"] = "cornea_os_summary";
				$arr["divSe"] = "2";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/cornea_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/cornea_os.xml";
			break;

			case "Ant":
			case "AntChamber":
				$arr["db"]["xmlOd"] = "anf_chamber_od";
				$arr["db"]["xmlOs"] = "anf_chamber_os";
				$arr["db"]["wnlSE"] = "wnlAnt";
				$arr["db"]["wnlOd"] = "wnlAntOd";
				$arr["db"]["wnlOs"] = "wnlAntOs";
				$arr["db"]["posSE"] = "posAnt";
				$arr["db"]["summOd"] = "anf_chamber_od_summary";
				$arr["db"]["summOs"] = "anf_chamber_os_summary";
				$arr["divSe"] = "3";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/antChamber_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/antChamber_os.xml";
			break;

			case "Iris":
				$arr["db"]["xmlOd"] = "iris_pupil_od";
				$arr["db"]["xmlOs"] = "iris_pupil_os";
				$arr["db"]["wnlSE"] = "wnlIris";
				$arr["db"]["wnlOd"] = "wnlIrisOd";
				$arr["db"]["wnlOs"] = "wnlIrisOs";
				$arr["db"]["posSE"] = "posIris";
				$arr["db"]["summOd"] = "iris_pupil_od_summary";
				$arr["db"]["summOs"] = "iris_pupil_os_summary";
				$arr["divSe"] = "4";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/iris_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/iris_os.xml";
			break;

			case "Lens":
				$arr["db"]["xmlOd"] = "lens_od";
				$arr["db"]["xmlOs"] = "lens_os";
				$arr["db"]["wnlSE"] = "wnlLens";
				$arr["db"]["wnlOd"] = "wnlLensOd";
				$arr["db"]["wnlOs"] = "wnlLensOs";
				$arr["db"]["posSE"] = "posLens";
				$arr["db"]["summOd"] = "lens_od_summary";
				$arr["db"]["summOs"] = "lens_os_summary";
				$arr["divSe"] = "5";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/lens_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/lens_os.xml";
			break;

		}
		
		//--
		if($subExm=="Conj" || $subExm=="Corn" || $subExm=="Cornea" || $subExm=="Ant" || $subExm=="AntChamber" || $subExm=="Iris" || $subExm=="Lens"){			
			if($subExm=="Conj"){ $subExm="conjunctiva"; }
			else if($subExm=="Ant"){ $subExm="antChamber"; }
			$oExamXml = new ExamXml();
			$tmp = $oExamXml->getExamXmlFiles("SLE", $subExm);
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
			case "Conj":
				$oConjunctiva = new Conjunctiva($patient_id, $form_id);
				$oConjunctiva->smartChart($arr); 	
			break;
			case "Cornea":
				$oCornea = new Cornea($patient_id, $form_id);
				$oCornea->smartChart($arr);
			break;
			case "AntChamber":
				$oAntChamber = new AntChamber($patient_id, $form_id);
				$oAntChamber->smartChart($arr);
			break;
			case "Iris":
				$oIris = new Iris($patient_id, $form_id);
				$oIris->smartChart($arr);
			break;
			case "Lens":
				$oLens = new Lens($patient_id, $form_id);
				$oLens->smartChart($arr);
			break;			
		}
	}
	
	//Reset
	function resetVals(){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		
		$oConjunctiva = new Conjunctiva($patient_id, $form_id);
		$oConjunctiva->resetVals(); 	
		
		$oCornea = new Cornea($patient_id, $form_id);
		$oCornea->resetVals();		
		
		$oAntChamber = new AntChamber($patient_id, $form_id);
		$oAntChamber->resetVals();		
		
		$oIris = new Iris($patient_id, $form_id);
		$oIris->resetVals();	

		$oLens = new Lens($patient_id, $form_id);
		$oLens->resetVals();	
		
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$oChartDraw->resetVals();		
	}
	
	function save_wnl(){
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
		
		$arrAlign = $arConjunctiva =  $arCornea = $arAntChamber = $arIris = $arLens = array();
		
		if(in_array("Conjunctiva",$arrTempProc)||in_array("All",$arrTempProc)){
			$oConjunctiva = new Conjunctiva($patient_id, $form_id);
			$arConjunctiva = $oConjunctiva->save_wnl("get"); 
			extract($arConjunctiva);
			$arrAlign[] = array($posConj,$wnlConjOd,$wnlConjOs,$conjunctiva_od_summary,$conjunctiva_os_summary);
		}
		if(in_array("Cornea",$arrTempProc)||in_array("All",$arrTempProc)){
			$oCornea = new Cornea($patient_id, $form_id);
			$arCornea = $oCornea->save_wnl("get"); 
			extract($arCornea);
			$arrAlign[] =array($posCorn,$wnlCornOd,$wnlCornOs,$cornea_od_summary,$cornea_os_summary);
		}
		if(in_array("Ant. Chamber",$arrTempProc)||in_array("All",$arrTempProc)){
			$oAntChamber = new AntChamber($patient_id, $form_id);
			$arAntChamber = $oAntChamber->save_wnl("get"); 
			extract($arAntChamber);
			$arrAlign[] =array($posAnt,$wnlAntOd,$wnlAntOs,$anf_chamber_od_summary,$anf_chamber_os_summary);
		}
		if(in_array("Iris & Pupil",$arrTempProc)||in_array("All",$arrTempProc)){
			$oIris = new Iris($patient_id, $form_id);
			$arIris = $oIris->save_wnl("get"); 
			extract($arIris);
			$arrAlign[] =array($posIris,$wnlIrisOd,$wnlIrisOs,$iris_pupil_od_summary,$iris_pupil_os_summary);
		}
		if(in_array("Lens",$arrTempProc)||in_array("All",$arrTempProc)){
			$oLens = new Lens($patient_id, $form_id);
			$arLens = $oLens->save_wnl("get"); 
			extract($arLens);	
			$arrAlign[] =array($posLens,$wnlLensOd,$wnlLensOs,$lens_od_summary,$lens_os_summary);
		}
		
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		
		//
		$oWv = new WorkView();
		//Check For Alingment of WNL Values --
		//array($posDraw,$elem_wnlDrawLaOd,$elem_wnlDrawLaOs,$la_drawing,$la_drawing)		
		$arrAlign = $oWv->alignWnlVals($arrAlign,$exmEye);

		if(!empty($arrAlign["od"])){	
			$arConjunctiva["wnlConj"]=$arConjunctiva["wnlConjOd"]="0";
			$arCornea["wnlCorn"]=$arCornea["wnlCornOd"]="0";
			$arAntChamber["wnlAnt"]=$arAntChamber["wnlAntOd"]="0";
			$arIris["wnlIris"]=$arIris["wnlIrisOd"]="0";
			$arLens["wnlLens"]=$arLens["wnlLensOd"]="0";
			
		}
		if(!empty($arrAlign["os"])){ 
			$arConjunctiva["wnlConj"]=$arConjunctiva["wnlConjOs"]="0"; 
			$arCornea["wnlCorn"]=$arCornea["wnlCornOs"]="0";
			$arAntChamber["wnlAnt"]=$arAntChamber["wnlAntOs"]="0";
			$arIris["wnlIris"]=$arIris["wnlIrisOs"]="0";
			$arLens["wnlLens"]=$arLens["wnlLensOs"]="0";
			
		}
		
		//Check For Alingment of WNL Values --
		
		if(in_array("Conjunctiva",$arrTempProc)||in_array("All",$arrTempProc)){
			$oConjunctiva->save_wnl("set", $arConjunctiva); 
		}
		if(in_array("Cornea",$arrTempProc)||in_array("All",$arrTempProc)){
			$oCornea->save_wnl("set", $arCornea);
		}
		if(in_array("Ant. Chamber",$arrTempProc)||in_array("All",$arrTempProc)){
			$oAntChamber->save_wnl("set", $arAntChamber);
		}
		if(in_array("Iris & Pupil",$arrTempProc)||in_array("All",$arrTempProc)){
			$oIris->save_wnl("set", $arIris);
		}
		if(in_array("Lens",$arrTempProc)||in_array("All",$arrTempProc)){
			$oLens->save_wnl("set", $arLens);
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
		
		if(in_array("Conjunctiva",$arrTempProc)||in_array("All",$arrTempProc)){
			$oConjunctiva = new Conjunctiva($patient_id, $form_id);
			$oConjunctiva->save_no_change();
		}
		if(in_array("Cornea",$arrTempProc)||in_array("All",$arrTempProc)){
			$oCornea = new Cornea($patient_id, $form_id);
			$oCornea->save_no_change();
		}
		if(in_array("Ant. Chamber",$arrTempProc)||in_array("All",$arrTempProc)){
			$oAntChamber = new AntChamber($patient_id, $form_id);
			$oAntChamber->save_no_change();
		}
		if(in_array("Iris & Pupil",$arrTempProc)||in_array("All",$arrTempProc)){
			$oIris = new Iris($patient_id, $form_id);
			$oIris->save_no_change();
		}
		if(in_array("Lens",$arrTempProc)||in_array("All",$arrTempProc)){
			$oLens = new Lens($patient_id, $form_id);
			$oLens->save_no_change();
		}
		
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$oChartDraw->save_no_change();
		
	}
	
}

?>