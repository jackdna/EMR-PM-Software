<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: LA.php
Coded in PHP7
Purpose: This class provides LA exam related functions.
Access Type : Include file
*/
?>
<?php
//LA.php
class LA extends ChartNote{
	public $examName;
	private $tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_la";
		$this->examName="LA";
	}

	function isRecordExists($a="",$b="",$c="",$d=""){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		
		$oLids = new Lids($patient_id, $form_id);
		$oLesion = new Lesion($patient_id, $form_id);
		$oLidPos = new LidPos($patient_id, $form_id);
		$oLacSys = new LacSys($patient_id, $form_id);
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		
		if($oLids->isRecordExists() || $oLids->isRecordExists() || $oLids->isRecordExists() || $oLids->isRecordExists() || $oLids->isRecordExists()){
			return true;
		}
		return false;
	}

	function carryForward(){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		
		$oLids = new Lids($patient_id, $form_id); $oLids->carryForward();
		$oLesion = new Lesion($patient_id, $form_id); $oLesion->carryForward();
		$oLidPos = new LidPos($patient_id, $form_id); $oLidPos->carryForward();
		$oLacSys = new LacSys($patient_id, $form_id); $oLacSys->carryForward();
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName); $oChartDraw->carryForward();
		
	}

	function getSubExamInfo($subExm){
		$arr=array();
		switch($subExm){
			case "Lids":
				$arr["db"]["xmlOd"] = "lid_od";
				$arr["db"]["xmlOs"] = "lid_os";
				$arr["db"]["wnlSE"] = "wnlLids";
				$arr["db"]["wnlOd"] = "wnlLidsOd";
				$arr["db"]["wnlOs"] = "wnlLidsOs";
				$arr["db"]["posSE"] = "posLids";
				$arr["db"]["summOd"] = "lid_conjunctiva_summary";
				$arr["db"]["summOs"] = "sumLidsOs";
				$arr["divSe"] = "1";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/lids_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/lids_os.xml";				
			break;

			case "Lesion":
				$arr["db"]["xmlOd"] = "lesion_od";
				$arr["db"]["xmlOs"] = "lesion_os";
				$arr["db"]["wnlSE"] = "wnlLesion";
				$arr["db"]["wnlOd"] = "wnlLesionOd";
				$arr["db"]["wnlOs"] = "wnlLesionOs";
				$arr["db"]["posSE"] = "posLesion";
				$arr["db"]["summOd"] = "lesion_summary";
				$arr["db"]["summOs"] = "sumLesionOs";
				$arr["divSe"] = "2";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/lesion_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/lesion_os.xml";
			break;

			case "LidPos":
				$arr["db"]["xmlOd"] = "lidposition_od";
				$arr["db"]["xmlOs"] = "lidposition_os";
				$arr["db"]["wnlSE"] = "wnlLidPos";
				$arr["db"]["wnlOd"] = "wnlLidPosOd";
				$arr["db"]["wnlOs"] = "wnlLidPosOs";
				$arr["db"]["posSE"] = "posLidPos";
				$arr["db"]["summOd"] = "lid_deformity_position_summary";
				$arr["db"]["summOs"] = "sumLidPosOs";
				$arr["divSe"] = "3";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/lidposition_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/lidposition_os.xml";
			break;

			case "LacSys":
				$arr["db"]["xmlOd"] = "lacrimal_od";
				$arr["db"]["xmlOs"] = "lacrimal_os";
				$arr["db"]["wnlSE"] = "wnlLacSys";
				$arr["db"]["wnlOd"] = "wnlLacSysOd";
				$arr["db"]["wnlOs"] = "wnlLacSysOs";
				$arr["db"]["posSE"] = "posLacSys";
				$arr["db"]["summOd"] = "lacrimal_system_summary";
				$arr["db"]["summOs"] = "sumLacOs";
				$arr["divSe"] = "4";
				//$arr["xmlFile"]["OD"] = $GLOBALS['incdir']."/chart_notes/xml/lacrimalSys_od.xml";
				//$arr["xmlFile"]["OS"] = $GLOBALS['incdir']."/chart_notes/xml/lacrimalSys_os.xml";
			break;

		}
		
		//--
		if($subExm=="Lids" || $subExm=="Lesion" || $subExm=="LidPos" || $subExm=="LacSys"){
			$oExamXml = new ExamXml();
			$tmp = $oExamXml->getExamXmlFiles("LA", $subExm);
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
			case "Lids":
				$oLids = new Lids($patient_id, $form_id);
				$oLids->smartChart($arr); 	
			break;

			case "Lesion":
				$oLesion = new Lesion($patient_id, $form_id);
				$oLesion->smartChart($arr);	
			break;

			case "LidPos":
				$oLidPos = new LidPos($patient_id, $form_id);
				$oLidPos->smartChart($arr);	
			break;

			case "LacSys":
				$oLacSys = new LacSys($patient_id, $form_id);
				$oLacSys->smartChart($arr);
			break;

		}
	}

	//Reset
	function resetVals(){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		$exmEye = $_POST["elem_exmEye"];
		
		$oLids = new Lids($patient_id, $form_id);
		$oLids->resetVals(); 	
		
		$oLesion = new Lesion($patient_id, $form_id);
		$oLesion->resetVals();		
		
		$oLidPos = new LidPos($patient_id, $form_id);
		$oLidPos->resetVals();		
		
		$oLacSys = new LacSys($patient_id, $form_id);
		$oLacSys->resetVals();		
		
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$oChartDraw->resetVals();
	}	
	
	function save_wnl(){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		$exmEye = $_POST["elem_exmEye"];
		
		$oLids = new Lids($patient_id, $form_id);
		$arLids = $oLids->save_wnl("get"); 
		extract($arLids);
		
		$oLesion = new Lesion($patient_id, $form_id);
		$arLesion = $oLesion->save_wnl("get");	
		extract($arLesion);	
		
		$oLidPos = new LidPos($patient_id, $form_id);
		$arLidPos = $oLidPos->save_wnl("get");
		extract($arLidPos);	
		
		$oLacSys = new LacSys($patient_id, $form_id);
		$arLacSys = $oLacSys->save_wnl("get");	
		extract($arLacSys);	
		
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);		

		//
		$oWv = new WorkView();
		//Check For Alingment of WNL Values --
		//array($posDraw,$elem_wnlDrawLaOd,$elem_wnlDrawLaOs,$la_drawing,$la_drawing)
		$arrAlign = array(
					array($posLids,$wnlLidsOd,$wnlLidsOs,$lid_conjunctiva_summary,$sumLidsOs),
					array($posLesion,$wnlLesionOd,$wnlLesionOs,$lesion_summary,$sumLesionOs),
					array($posLidPos,$wnlLidPosOd,$wnlLidPosOs,$lid_deformity_position_summary,$sumLidPosOs),
					array($posLacSys,$wnlLacSysOd,$wnlLacSysOs,$lacrimal_system_summary,$sumLacOs)
				);
		$arrAlign = $oWv->alignWnlVals($arrAlign,$exmEye);

		if(!empty($arrAlign["od"])){	
			$arLids["wnlLids"]=$arLids["wnlLidsOd"]="0";
			$arLesion["wnlLesion"]=$arLesion["wnlLesionOd"]="0";
			$arLidPos["wnlLidPos"]=$arLidPos["wnlLidPosOd"]="0";
			$arLacSys["wnlLacSys"]=$arLacSys["wnlLacSysOd"]="0";	
		}
		if(!empty($arrAlign["os"])){ 
			$arLids["wnlLids"]=$arLids["wnlLidsOs"]="0"; 
			$arLesion["wnlLesion"]=$arLesion["wnlLesionOs"]="0";
			$arLidPos["wnlLidPos"]=$arLidPos["wnlLidPosOs"]="0";
			$arLacSys["wnlLacSys"]=$arLacSys["wnlLacSysOs"]="0";	
		}
		
		//Check For Alingment of WNL Values --
		
		$oLids->save_wnl("set", $arLids); 
		$oLesion->save_wnl("set", $arLesion); 
		$oLidPos->save_wnl("set", $arLidPos); 
		$oLacSys->save_wnl("set", $arLacSys); 
		$oChartDraw->save_wnl(); 
	}
	
	
	
	function save_no_change(){
		$form_id = $this->fid;
		$patient_id = $this->pid;
		$exmEye = $_POST["elem_exmEye"];
		
		$oLids = new Lids($patient_id, $form_id);
		$oLids->save_no_change(); 	
		
		$oLesion = new Lesion($patient_id, $form_id);
		$oLesion->save_no_change();		
		
		$oLidPos = new LidPos($patient_id, $form_id);
		$oLidPos->save_no_change();		
		
		$oLacSys = new LacSys($patient_id, $form_id);
		$oLacSys->save_no_change();		
		
		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$oChartDraw->save_no_change();	
	
	}
	
	function attachDraw2PtExam($drwId){
		$oChartDraw = new ChartDraw($this->pid, $this->fid,$this->examName);
		$oChartDraw->attachDraw2PtExam($drwId);
	}
	
	function getIDocDrawId(){
		$oChartDraw = new ChartDraw($this->pid, $this->fid,$this->examName);
		return $oChartDraw->getIDocDrawId();
	}
	
}

?>