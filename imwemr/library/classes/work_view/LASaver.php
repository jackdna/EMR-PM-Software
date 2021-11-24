<?php

class LASaver extends ChartNoteSaver{
	private $arG; 

	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
		$this->arG = array();
		$this->examName="LA";
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
			
			$oLids = new Lids($patientid, $elem_formId);
			$tmp = $oLids->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);
			$oLesion = new Lesion($patientid, $elem_formId);
			$tmp = $oLesion->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);
			$oLidPos = new LidPos($patientid, $elem_formId);
			$tmp = $oLidPos->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);
			$oLacSys = new LacSys($patientid, $elem_formId);
			$tmp = $oLacSys->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);			
			$oChartDraw = new ChartDraw($patientid, $elem_formId, $this->examName);
			$tmp = $oChartDraw->save_form();
			
			//--
			
			//			
			$arrRet["Exam"] = "La";			
			$arrRet["arExamDone"] = $strExamsAll;
		}
		
		//
		echo json_encode($arrRet);
	}	
}
?>