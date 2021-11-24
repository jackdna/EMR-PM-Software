<?php

class PupilSaver extends ChartNoteSaver{
	private $arG; 
	private $opupil;	
	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
		$this->arG = array();
		$this->opupil = new Pupil($pid,$fid);	
	}
	
	function getExamSummary($xmlStr){
		$dom=$this->getDom($xmlStr,"");		
		return $this->opupil->getDomSum($dom,"1");
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
			//
			$oExamXml = new ExamXml();
		
			$elem_chng_divPupil_Od = $_POST["elem_chng_divPupil_Od"];
			$elem_chng_divPupil_Os = $_POST["elem_chng_divPupil_Os"];
			
			$arrSe = array("elem_chng_divPupil_Od"=>$elem_chng_divPupil_Od,"elem_chng_divPupil_Os"=>$elem_chng_divPupil_Os);
			$statusElem = $this->getStrSe($arrSe);
			
			if(trim($_POST["elem_pupilDescOd"])=="Comments:")$_POST["elem_pupilDescOd"]="";
			if(trim($_POST["elem_pupilDescOs"])=="Comments:")$_POST["elem_pupilDescOs"]="";
			
			$elem_saveForm = $_POST["elem_saveForm"];
			//$elem_editMode = $_POST["elem_editMode"];
			$pupilId = $_POST["elem_pupilId"];
			$patientId = $patientid;
			$examDate = $_POST["elem_examDate"];
			$descPupil = sqlEscStr($_POST["elem_descPupil"]);
			
			$wnl = $_POST["elem_wnl"];
			$notApplicable = $_POST["elem_notApplicable"];
			$noChange = $_POST["elem_noChange"];
			$perrla = !empty($_POST["elem_perrla"]) ? $_POST["elem_perrla"] : "0";
			$isPositive = $_POST["elem_isPositive"];
			
			$other_values="";
			$arr_other_val = array();
			$arr_other_val["elem_pharmadilated"]=$_POST["elem_pharmadilated"];
			$arr_other_val["elem_pharmadilated_eye"]=$_POST["elem_pharmadilated_eye"];
			$other_values=serialize($arr_other_val);
			$other_values = sqlEscStr($other_values);
			
			//
			$arXmlFiles = $oExamXml->getExamXmlFiles("Pupil");
			
			$menuName = "pupilOd";
			$menuFilePath = $arXmlFiles["od"];
			$elem_pupil_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
			$wnlPupilOd = $_POST["elem_wnlPupilOd"];
			
			//---
			$strDilated = $strDilatedOd = $strDilatedOs = "";
			for($i=1;$i<=8;$i++){
				$var = "elem_dilatedOd_mm".$i;
				$var2 = "elem_dilatedOs_mm".$i;
				if(!empty($_POST[$var])){
					$strDilatedOd .= $_POST[$var].",";
				}
				if(!empty($_POST[$var2])){
					$strDilatedOs .= $_POST[$var2].",";
				}				
			}
			if(!empty($strDilatedOd) || !empty($strDilatedOs)){
				$strDilated = $strDilatedOd."~!!~".$strDilatedOs;
			}
			//---
			
			$menuName = "pupilOs";
			$menuFilePath = $arXmlFiles["os"];
			$elem_pupil_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
			$wnlPupilOs = $_POST["elem_wnlPupilOs"];
			
			//
			$oUserAp = new UserAp();
			
			// Summary --
			$pupil_od_result = $pupil_os_result = $strExamsAllOd= $strExamsAllOs = "";
			
			$arrTemp = $this->getExamSummary($elem_pupil_od);
			$pupil_od_result = $arrTemp["Summary"];
			$arrExmDone_od = $arrTemp["ExmDone"];
			
			if(!empty($elem_chng_divPupil_Od)){				
				$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Pupil",$arrExmDone_od, $pupil_od_result);		
			}
			
			$arrTemp = $this->getExamSummary($elem_pupil_os);
			$pupil_os_result = $arrTemp["Summary"];
			$arrExmDone_os = $arrTemp["ExmDone"];
			
			if(!empty($elem_chng_divPupil_Os)){
				$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Pupil",$arrExmDone_os, $pupil_os_result);
			}
			
			//ut_elems ----------------------
			$elem_utElems = $_POST["elem_utElems"];
			$elem_utElems_cur = $_POST["elem_utElems_cur"];
			$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);
			//ut_elems ----------------------
			
			//Purge
		
			if(!empty($_POST["elem_purged"])){
				//$purgePhrse = " , purged = pupil_id ";
				//Update
				$sql = "UPDATE chart_pupil
					  SET
					  purged=pupil_id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE formId='".$elem_formId."' AND patientId='".$patientid."' AND purged = '0'
					";			
				$row = sqlQuery($sql);
				
			}else{
			
				$owv = new WorkView();
				$elem_pupil_od = sqlEscStr($elem_pupil_od);
				$elem_pupil_os = sqlEscStr($elem_pupil_os);
				
				//check
				$cQry = "select last_opr_id,uid,sumOdPupil,wnlPupilOd,wnl_value,sumOsPupil,wnlPupilOs,modi_note_Arr,examDate  FROM chart_pupil WHERE formId='".$elem_formId."' AND patientId='".$patientid."' AND purged = '0' ";
				$row = sqlQuery($cQry);
				if($row == false){
					$elem_editMode  =  "0";
				}else{
					$elem_editMode  =  "1";
					$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
					
					//Modifying Notes----------------
					$modi_note_od=$owv->getModiNotes($row["sumOdPupil"],$row["wnlPupilOd"],$pupil_od_result,$wnlPupilOd,$row["uid"],$row["wnl_value"]);
					$modi_note_os=$owv->getModiNotes($row["sumOsPupil"],$row["wnlPupilOs"],$pupil_os_result,$wnlPupilOs,$row["uid"],$row["wnl_value"]);
					
					$seri_modi_note_pupilArr = $owv->getModiNotesArr($row["sumOdPupil"],$pupil_od_result,$last_opr_id,'OD',$row["modi_note_Arr"],$row['examDate']);
					$seri_modi_note_pupilArr = $owv->getModiNotesArr($row["sumOsPupil"],$pupil_os_result,$last_opr_id,'OS',$seri_modi_note_pupilArr,$row['examDate']);
					//Modifying Notes----------------
				}
				
				if($elem_editMode == "0"){
					//save  wnl value
					$wnl_value = $this->getExamWnlStr("Pupil");//PERRLA, -ve APD
				
					// Insert
					$sql = "INSERT INTO chart_pupil ".
						 "(pupil_id, ".
						 " formId, ".
						 " patientId, examinedNoChange,examDate, ".
						 " notApplicable, wnl, perrla, pupilOd, pupilOs, isPositive, ".
						 " sumOdPupil, sumOsPupil,wnlPupilOd, wnlPupilOs, ".
						 " descPupil, uid, statusElem, other_values, ut_elem, ".
						 " modi_note_Arr, last_opr_id, wnl_value ".
						 ")".
						 "VALUES ".
						 "(NULL, '".$elem_formId."', '".$patientid."', ".
						 " '".$noChange."', '".$examDate."', '".$notApplicable."', ".
						 " '".$wnl."', '".$perrla."', '".$elem_pupil_od."', ".
						 " '".$elem_pupil_os."', '".$isPositive."', ".
						 " '".$pupil_od_result."', '".$pupil_os_result."', '".$wnlPupilOd."', '".$wnlPupilOs."', ".
						 " '".$descPupil."', '".$_SESSION["authId"]."', '".$statusElem."', ".
						 " '".$other_values."', '".$ut_elem."', ".
						 " '".sqlEscStr($seri_modi_note_pupilArr)."', '".$last_opr_id."', '".sqlEscStr($wnl_value)."' ".
						 ")";
					$insertId = sqlInsert($sql);

				}else if($elem_editMode == "1"){
				
					//Update
					$sql = "UPDATE chart_pupil
						 SET
						  examinedNoChange='".$noChange."',
						  examDate='".$examDate."',
						  notApplicable='".$notApplicable."',
						  wnl='".$wnl."',
						  perrla='".$perrla."',
						  pupilOd='".$elem_pupil_od."',
						  pupilOs='".$elem_pupil_os."',
						  isPositive='".$isPositive."',
						  sumOdPupil='".$pupil_od_result."',
						  sumOsPupil='".$pupil_os_result."',
						  wnlPupilOd='".$wnlPupilOd."',
						  wnlPupilOs='".$wnlPupilOs."',
						  descPupil = '".$descPupil."',
						  uid = '".$_SESSION["authId"]."',
						  statusElem = '".$statusElem."',
						  other_values = '".$other_values."', 
						  ut_elem = '".$ut_elem."', ".
						  /*"modi_note_od = CONCAT('".sqlEscStr($modi_note_od)."',modi_note_od),
						  modi_note_os = CONCAT('".sqlEscStr($modi_note_os)."',modi_note_os), ".*/
						  "modi_note_Arr = '".sqlEscStr($seri_modi_note_pupilArr)."',last_opr_id = '".$last_opr_id."'
						   ".
						  "WHERE formId = '".$elem_formId."' AND patientId='".$patientid."' AND purged = '0' ";
					$res = sqlQuery($sql);
					$insertId = $pupilId;
				}
				
				// Make chart notes valid
				$this->makeChartNotesValid();
				
				//Set Change Date Arc Rec --
				$this->setChangeDtArcRec("chart_pupil");
				
				//check
				if(isset($strDilated) && !empty($strDilated)){
					$cQry = "select * FROM chart_dialation WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
					$row = sqlQuery($cQry);
					if( $row !=  false ){
						$sql = "UPDATE chart_dialation SET dilated_mm='".$strDilated."' 
								WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
						$res = sqlQuery($sql);
					}else{
						$sql= "INSERT INTO chart_dialation(dia_id,patient_id,form_id,dilated_mm) VALUES(NULL,'".$patientid."', '".$elem_formId."','".$strDilated."') ";
						$res = sqlQuery($sql);
					}
				}
			
			}//End Purge check
			
			//combine
			$strExamsAll = $this->combineExamFindings($strExamsAllOd, $strExamsAllOs);
			$strExamsAll = $this->makeArrString($strExamsAll);
			
			//			
			$arrRet["Exam"] = "Pupil";
			$arrRet["isPositive"] = $isPositive;
			$arrRet["wnl"] = $wnl;
			$arrRet["NC"] = $noChange;
			$arrRet["Draw"] = "null";
			$arrRet["arExamDone"] = $strExamsAll;			
			$arrRet["AddExam"] = $elem_editMode;
			$arrRet["FormId"] = $elem_formId;		

		}//
		
		//
		echo json_encode($arrRet);
	}
}

?>