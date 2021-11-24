<?php

class FundusExamSaver extends ChartNoteSaver{
	private $arG;

	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
		$this->arG = array();
		$this->examName="FundusExam";
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

			$oOptNrv = new OpticNerve($patientid, $elem_formId);
			$tmp = $oOptNrv->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);

			if($_POST["elem_retina_version"] == "old"){
			}else if($_POST["elem_retina_version"] == "new"){

				$oRetinalExam = new RetinalExam($patientid, $elem_formId);
				$tmp = $oRetinalExam->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);

			}

			$oMac = new Macula($patientid, $elem_formId);
			$tmp = $oMac->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);

			$oBV = new BloodVessels($patientid, $elem_formId);
			$tmp = $oBV->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);

			$oPeriphery = new Periphery($patientid, $elem_formId);
			$tmp = $oPeriphery->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);

			$oVitreous = new Vitreous($patientid, $elem_formId);
			$tmp = $oVitreous->save_form(); $strExamsAll = array_merge($strExamsAll, $tmp);

			$oChartDraw = new ChartDraw($patientid, $elem_formId,$this->examName);
			$tmp = $oChartDraw->save_form();
			$ar_drw_inter = $oChartDraw->get_report_interp_v2();
			//--

			//
			$arrRet["Exam"] = "Rv";
			$arrRet["arExamDone"] = $strExamsAll;
			$arrRet["arDrawInter"] = $ar_drw_inter;
			//
		}

		//
		echo json_encode($arrRet);
	}
}
?>
