<?php

class RefSurgSaver extends ChartNoteSaver{
	private $arG; 
	private $orefsurg;
	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
		$this->arG = array();
		$this->orefsurg = new RefSurg($pid,$fid);
	}
	
	function getExamSummary($xmlStr){
		$dom=$this->getDom($xmlStr,"");		
		return $this->orefsurg->getDomSum($dom,"1");
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
			$oSaveFile = new SaveFile($patientid);
			$oExamXml = new ExamXml();	
			$arXmlFiles = $oExamXml->getExamXmlFiles("RefSurg");
		
			$elem_chng_div1_Od = $_POST["elem_chng_div1_Od"];
			$elem_chng_div1_Os = $_POST["elem_chng_div1_Os"];

			$arrSe = array("elem_chng_div1_Od"=>$elem_chng_div1_Od,"elem_chng_div1_Os"=>$elem_chng_div1_Os);
			$statusElem = $this->getStrSe($arrSe);
			$mode_ref=$_POST["elem_mode_refsx"];
			
			//Empty Fields as per mode --
			if($mode_ref=="Custom"){
				$arrT=array("elem_trgtRef","elem_sphericalEq","elem_adjstppp",
							"elem_capDia","elem_capthick","elem_ablation","elem_RSB",
							"elem_laserEntry","elem_phypadjst","elem_phydioadjst",
							"elem_laserEnPhypadjst","elem_laserEnPhydioadjst",
							"elem_mr_s","elem_mr_c","elem_mr_a","elem_corr");
			}else{
				$arrT=array("elem_CLE");
			}
			foreach($arrT as $key=>$val){
				$_POST["".$val."Od"]="";
				$_POST["".$val."Os"]="";
			}
			//Empty Fields as per mode --
			
			// xmls
			$wnlRefSurgOd = $wnlRefSurgOs = $isPositive = $wnl = "0";
			//if(!empty($elem_chng_divCon_Od) || !empty($elem_chng_divCon_Os)){
			//	if(!empty($elem_chng_divCon_Od)){
					$menuName = "refSurgOd";
					$menuFilePath = $arXmlFiles["od"]; //dirname(__FILE__)."/xml/ref_surg_od.xml";
					$refSurgOd = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
					$wnlRefSurgOd = $_POST["elem_wnlRefSurgOd"];
			//	}

			//	if(!empty($elem_chng_divCon_Os)){
					$menuName = "refSurgOs";
					$menuFilePath = $arXmlFiles["os"]; //dirname(__FILE__)."/xml/ref_surg_os.xml";
					$refSurgOs = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
					$wnlRefSurgOs = $_POST["elem_wnlRefSurgOs"];
			
			$id_ref_surg = $_POST["elem_refSurgId"];
			$form_id = $_POST["elem_formId"];
			$patient_id = $_POST["elem_patientId"];
			$exam_date = $_POST["elem_examDate"];
			$wnl = $_POST["elem_wnl"];
			$descRefSurg = sqlEscStr($_POST["elem_descRefSurg"]);		
			$isPositive = $_POST["elem_isPositive"];
			$examined_no_change = $_POST["elem_examined_no_change"];
			$not_applicable=$other_values="";
			
			//
			$oUserAp = new UserAp();
			
			//Summary --
			$sumOdRefSurg = $sumOsRefSurg = $strExamsAllOd = $strExamsAllOs = "";
			$refSurgOd = $refSurgOd;
			
			$arrTemp = $this->getExamSummary($refSurgOd);
			$sumOdRefSurg = $arrTemp["Summary"];
			$arrExmDone_od = $arrTemp["ExmDone"];
			if(!empty($elem_chng_div1_Od)){
				$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Refractive Surgery",$arrExmDone_od,$sumOdRefSurg);
			}
			$refSurgOs = $refSurgOs;		
			$arrTemp = $this->getExamSummary($refSurgOs);
			$sumOsRefSurg = $arrTemp["Summary"];
			$arrExmDone_os = $arrTemp["ExmDone"];
			if(!empty($elem_chng_div1_Os)){
				$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Refractive Surgery",$arrExmDone_os,$sumOsRefSurg);
			}
			//Summary --
			
			//Positive ---
			if(!empty($sumOdRefSurg)||!empty($sumOsRefSurg)){$isPositive = 1;}else{$isPositive = 0;}
			//Positive--
			
			//ut_elems ----------------------
			$elem_utElems = $_POST["elem_utElems"];
			$elem_utElems_cur = $_POST["elem_utElems_cur"];
			$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);
			//ut_elems ----------------------
			
			//Purge
			if(!empty($_POST["elem_purged"])){
				//$purgePhrse = " , purged = pupil_id ";
				//Update
				$sql = "UPDATE chart_ref_surgery
					  SET
					  purged=id_ref_surg,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";			
				$row = sqlQuery($sql);
				
			}else{
				$owv = new WorkView();
				$refSurgOd = sqlEscStr($refSurgOd);
				$refSurgOs = sqlEscStr($refSurgOs);

				
				//check
				$cQry = "select * FROM chart_ref_surgery WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
				$row = sqlQuery($cQry);
				if($row == false){
					$elem_editMode = "0";
				}else{
					$elem_editMode = "1";
					
					//Modifying Notes----------------
					$modi_note_RefSurgOd=$owv->getModiNotes($row["sumOdRefSurg"],$row["wnlRefSurgOd"],$sumOdRefSurg,$wnlRefSurgOd,$row["uid"]);
					$modi_note_RefSurgOs=$owv->getModiNotes($row["sumOsRefSurg"],$row["wnlRefSurgOd"],$sumOsRefSurg,$wnlRefSurgOs,$row["uid"]);				
					//Modifying Notes----------------				
					
				}
				
				if($elem_editMode == "0"){
					$sql = "INSERT INTO chart_ref_surgery(
							id_ref_surg,form_id,patient_id,
							examined_no_change,not_applicable,
							exam_date,wnl,wnlRefSurgOd,wnlRefSurgOs,
							isPositive,refSurgOd,refSurgOs,
							sumOdRefSurg,sumOsRefSurg,
							descRefSurg,uid,
							statusElem,other_values,mode_refsx, ut_elem
							)
						    VALUES
							(NULL,'".$form_id."','".$patient_id."',
							'".$examined_no_change."','".$not_applicable."',
							'".$exam_date."','".$wnl."','".$wnlRefSurgOd."','".$wnlRefSurgOs."',
							'".$isPositive."','".$refSurgOd."','".$refSurgOs."',
							'".$sumOdRefSurg."','".$sumOsRefSurg."',
							'".$descRefSurg."','".$_SESSION["authId"]."',
							'".$statusElem."','".$other_values."','".$mode_ref."', '".$ut_elem."' )
							";
					$insertId = sqlInsert($sql);

				}else {
					$sql = "UPDATE chart_ref_surgery
							SET
							examined_no_change='".$examined_no_change."',
							not_applicable='".$not_applicable."',
							exam_date='".$exam_date."',
							wnl='".$wnl."',
							wnlRefSurgOd='".$wnlRefSurgOd."',
							wnlRefSurgOs='".$wnlRefSurgOs."',
							isPositive='".$isPositive."',
							refSurgOd='".$refSurgOd."',
							refSurgOs='".$refSurgOs."',
							sumOdRefSurg='".$sumOdRefSurg."',
							sumOsRefSurg='".$sumOsRefSurg."',
							descRefSurg='".$descRefSurg."',
							uid='".$_SESSION["authId"]."',
							statusElem='".$statusElem."',
							other_values='".$other_values."',
							mode_refsx='".$mode_ref."',
							ut_elem = '".$ut_elem."',
							modi_note_RefSurgOd = '".$modi_note_RefSurgOd."',
							modi_note_RefSurgOs = '".$modi_note_RefSurgOs."'
							WHERE patient_id='".$patient_id."' AND form_id='".$form_id."' AND purged='0'
							";
					$res = sqlQuery($sql);
				}
				
				// Make chart notes valid
				$this->makeChartNotesValid();

				//Set Change Date Arc Rec --
				$this->setChangeDtArcRec("chart_ref_surgery");
				//Set Change Date Arc Rec --
			}//
			
			//combine
			$strExamsAll = $this->combineExamFindings($strExamsAllOd, $strExamsAllOs);	
			$strExamsAll = $this->makeArrString($strExamsAll);
			
			//			
			$arrRet["Exam"] = "ref_surg";
			$arrRet["isPositive"] = $isPositive;
			$arrRet["wnl"] = $wnl;
			$arrRet["NC"] = $noChange;
			$arrRet["Draw"] = "".$fDraw."";
			$arrRet["arExamDone"] = $strExamsAll;	
			$arrRet["AddExam"] = $elem_editMode;
			$arrRet["FormId"] = $elem_formId;
		}	
		
		//
		echo json_encode($arrRet);
	}	
}
?>