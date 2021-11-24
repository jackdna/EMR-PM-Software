<?php

class SLESaver extends ChartNoteSaver{
	private $arG; 

	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
		$this->arG = array();
		$this->examName="SLE";
	}
	
	public function save_form(){
		$patientid = $this->pid;
		$elem_formId = $this->fid;
		$arrRet=array();
		
		//check
		if(empty($patientid) || empty($elem_formId)){ echo json_encode($arrRet); exit();  }
		
		//Check if Chart is not Finalized or User is Finalizer	
		
		if(!$this->checkFinalizer()){			
			/*echo "<script>window.close();</script>";*/
			//exit();
		}else{			
		
			//--
			$strExamsAll=array();
			
			$oConjunctiva = new Conjunctiva($patientid, $elem_formId);
			$tmp = $oConjunctiva->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);
			$oCornea = new Cornea($patientid, $elem_formId);
			$tmp = $oCornea->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);
			$oAntChamber = new AntChamber($patientid, $elem_formId);
			$tmp = $oAntChamber->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);
			$oIris = new Iris($patientid, $elem_formId);
			$tmp = $oIris->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);
			$oLens = new Lens($patientid, $elem_formId);
			$tmp = $oLens->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);			
			$oChartDraw = new ChartDraw($patientid, $elem_formId, $this->examName);
			$tmp = $oChartDraw->save_form();
			
			//--
			
			//			
			$arrRet["Exam"] = "Sle";			
			$arrRet["arExamDone"] = $strExamsAll;
			//------------
		
			//Check if Chart is not Finalized or User is Finalizer		
			$OBJDrawingData = new CLSDrawingData();
			$objImageManipulation = new CLSImageManipulation();
			$oSaveFile = new SaveFile($patientid);
			$oExamXml = new ExamXml();	
			$arXmlFiles = $oExamXml->getExamXmlFiles("SLE");
			
							
		}	
		
		//
		echo json_encode($arrRet);
	}	
}
?>