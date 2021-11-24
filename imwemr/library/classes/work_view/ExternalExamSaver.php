<?php

class ExternalExamSaver extends ChartNoteSaver{
	private $arG; 
	private $oexternal;
	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
		$this->arG = array();
		$this->oexternal = new ExternalExam($pid,$fid);
	}
	
	function getExamSummary($xmlStr){
		$dom=$this->getDom($xmlStr,"");		
		return $this->oexternal->getDomSum($dom,"1");
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
			//Check if Chart is not Finalized or User is Finalizer		
			$OBJDrawingData = new CLSDrawingData();
			$objImageManipulation = new CLSImageManipulation();
			$oSaveFile = new SaveFile($patientid);
			$oExamXml = new ExamXml();	
			$arXmlFiles = $oExamXml->getExamXmlFiles("ExternalExam");
			
			$elem_chng_divCon_Od = $_POST["elem_chng_divCon_Od"];
			$elem_chng_divCon_Os = $_POST["elem_chng_divCon_Os"];
			$elem_chng_divDraw_Od = $_POST["elem_chng_divDraw_Od"];
			$elem_chng_divDraw_Os = $_POST["elem_chng_divDraw_Os"];
			
			$arrSe = array("elem_chng_divCon_Od"=>$elem_chng_divCon_Od,"elem_chng_divCon_Os"=>$elem_chng_divCon_Os,
				"elem_chng_divDraw_Od"=>$elem_chng_divDraw_Od,"elem_chng_divDraw_Os"=>$elem_chng_divDraw_Os);
			$statusElem = $this->getStrSe($arrSe);
			
			// xmls
			$wnlEeOd = $wnlEeOs = $isPositive = $wnl = $noChange = $wnlDrawOd=$wnlDrawOs=$posEe=$posDraw=$wnlEe=$wnlDraw=$ncEe=$ncDraw= "0";
			
			
			$menuName = "externalOd";
			$menuFilePath = $arXmlFiles["od"];
			$elem_external_exam = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
			$wnlEeOd = $_POST["elem_wnlEeOd"];
			
			$menuName = "externalOs";
			$menuFilePath = $arXmlFiles["os"];
			$elem_external_exam_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
			$wnlEeOs = $_POST["elem_wnlEeOs"];
			
			$isPositive = $_POST["elem_isPositive"];
			$wnl = $_POST["elem_wnl"];
			$noChange = $_POST["elem_noChange"];
			
			$elem_saveForm = $_POST["elem_saveForm"];
			//$elem_editMode = $_POST["elem_editMode"];
			$eeId = $_POST["elem_eeId"];
			$examDate = $_POST["elem_examDate"];
			$notApplicable = $_POST["elem_notApplicable"];
			$descExternal = sqlEscStr($_POST["elem_descExternal"]);
			$eeDesc = sqlEscStr($_POST["elem_eeDesc_od"]);
			$eeDesc_os = sqlEscStr($_POST["elem_eeDesc_os"]);
			$eeDrawing = $_POST["elem_externalOdDrawing"];
			
			$ncEe=$_POST["elem_ncEe"];
			$ncDraw=$_POST["elem_ncDraw"];
			$wnlEe=$_POST["elem_wnlEe"];
			$wnlDraw=$_POST["elem_wnlDraw"];
			$posEe=$_POST["elem_posEe"];
			$posDraw=$_POST["elem_posDraw"];
			$wnlDrawOd=$_POST["elem_wnlDrawOd"];
			$wnlDrawOs=$_POST["elem_wnlDrawOs"];
			
			//
			$oUserAp = new UserAp();
			
			//Summary --
			$sumOdEE = $sumOsEE = $strExamsAllOd = $strExamsAllOs = "";
			$arrTemp = $this->getExamSummary($elem_external_exam);			
			$elem_external_exam_summary = $arrTemp["Summary"];
			$arrExmDone_od = $arrTemp["ExmDone"];
			if(!empty($elem_chng_divCon_Od)){
				$strExamsAllOd .= $oUserAp->refineByConsoleSymp("External",$arrExmDone_od,$elem_external_exam_summary);
			}
			
			$arrTemp = $this->getExamSummary($elem_external_exam_os);
			$sumOsEE = $arrTemp["Summary"];
			$arrExmDone_os = $arrTemp["ExmDone"];
			if(!empty($elem_chng_divCon_Os)){
				$strExamsAllOs .= $oUserAp->refineByConsoleSymp("External",$arrExmDone_os,$sumOsEE);
			}
			//Summary --
			
			//ut_elems ----------------------
			$elem_utElems = $_POST["elem_utElems"];
			$elem_utElems_cur = $_POST["elem_utElems_cur"];
			$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);
			//ut_elems ----------------------
			
			//Purge
		
			//Purge
			if(!empty($_POST["elem_purged"])){
				//$purgePhrse = " , purged = pupil_id ";
				//Update
				$sql = "UPDATE chart_external_exam
					  SET
					  purged=ee_id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";
				$row = sqlQuery($sql);
				
			}else{
				$owv = new WorkView();
				$elem_external_exam = sqlEscStr($elem_external_exam);
				$elem_external_exam_os = sqlEscStr($elem_external_exam_os);

				//check
				$cQry = "select last_opr_id,uid,ee_id,external_exam_summary,sumOsEE,wnlEeOd,wnlEeOs,wnl_value, modi_note_Arr,exam_date FROM chart_external_exam WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged='0' ";
				$row = sqlQuery($cQry);
				
				if($row == false){
					$last_opr_id = $_SESSION["authId"];
					$elem_editMode = "0";
				}else{
					$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
					$eeId=$eeIDExam = $row["ee_id"];
					$elem_editMode = "1";
					
					//Modifying Notes----------------
						$modi_note_od=$owv->getModiNotes($row["external_exam_summary"],$row["wnlEeOd"],$elem_external_exam_summary,$wnlEeOd,$row["uid"], $row["wnl_value"]);
						$modi_note_os=$owv->getModiNotes($row["sumOsEE"],$row["wnlEeOs"],$sumOsEE,$wnlEeOs,$row["uid"],$row["wnl_value"]);
					//Modifying Notes----------------			
					$seri_modi_note_externalArr = $owv->getModiNotesArr($row["external_exam_summary"],$elem_external_exam_summary,$last_opr_id,'OD',$row["modi_note_Arr"],$row['exam_date']);
					$seri_modi_note_externalArr = $owv->getModiNotesArr($row["sumOsEE"],$sumOsEE,$last_opr_id,'OS',$seri_modi_note_externalArr,$row['exam_date']);	
				}
				
				if($elem_editMode == "0"){
					//save wnl text	
					$wnl_value = $this->getExamWnlStr("External");
					// Insert
					$sql = "INSERT INTO chart_external_exam ".
						 "(ee_id, external_exam, external_exam_summary, ".
						 " ee_desc, ";
							if(empty($_REQUEST["hidBlEnHTMLDrawing"]) == true){
								$sql .= "ee_drawing,";
								$sql .= "drawing_insert_update_from,";
							}
							else{
								$sql .= "drawing_insert_update_from,";
							} 
						 $sql .= "form_id, ".
						 " patient_id, examined_no_change, exam_date, ".
						 " not_applicable, wnl, external_exam_os, isPositive, ".
						 " sumOsEE, wnlEeOd, wnlEeOs, descExternal,uid, ".
						 " ncEe,ncDraw,wnlEe,wnlDraw,posEe,posDraw,wnlDrawOd,wnlDrawOs, ee_desc_os, ".
						 " statusElem, ut_elem, ".
						 " last_opr_id, wnl_value ".
						 ")".
						 "VALUES ".
						 "(NULL, '".$elem_external_exam."', '".$elem_external_exam_summary."', ".
						 " '".$eeDesc."', ";
							if(empty($_REQUEST["hidBlEnHTMLDrawing"]) == true){
								$sql .= "'".$eeDrawing."',";
								$sql .= "'0',";
							}
							else{
								$sql .= "'1',";
							} 
						 $sql .= "'".$elem_formId."', ".
						 " '".$patientid."', '".$noChange."', '".$examDate."', ".
						 " '".$notApplicable."', '".$wnl."', '".$elem_external_exam_os."', '".$isPositive."', ".
						 " '".$sumOsEE."', '".$wnlEeOd."', '".$wnlEeOs."', ".
						 " '".$descExternal."','".$_SESSION["authId"]."', ".
						 " '".$ncEe."','".$ncDraw."','".$wnlEe."','".$wnlDraw."','".$posEe."','".$posDraw."','".$wnlDrawOd."','".$wnlDrawOs."', '".$eeDesc_os."', ".
						 " '".$statusElem."', '".$ut_elem."', ".
						 " '".$last_opr_id."', '".sqlEscStr($wnl_value)."' ".
						 ")";
					$insertId = sqlInsert($sql);
					
					//$flagCFD=1;
					//$flagCFD_drw1=0;
					$arrCFD_ids=array();
					if(isset($_REQUEST["hidBlEnHTMLDrawing"]) == true && empty($_REQUEST["hidBlEnHTMLDrawing"]) == false && $_REQUEST["hidBlEnHTMLDrawing"] == "1" && (int)$insertId > 0){
						for($intTempDrawCount = 0; $intTempDrawCount < 25; $intTempDrawCount++){
							if($_REQUEST["hidDrawingChangeYesNo".$intTempDrawCount] == "yes"){	
								$arrDrawingData = array();	
								$arrDrawingData["imagePath"] = $oSaveFile->getUploadDirPath();
								$arrDrawingData["hidRedPixel"] = $_REQUEST["hidRedPixel".$intTempDrawCount];
								$arrDrawingData["hidGreenPixel"] = $_REQUEST["hidGreenPixel".$intTempDrawCount];
								$arrDrawingData["hidBluePixel"] = $_REQUEST["hidBluePixel".$intTempDrawCount];
								$arrDrawingData["hidAlphaPixel"] = $_REQUEST["hidAlphaPixel".$intTempDrawCount];
								$arrDrawingData["hidImageCss"] = $_REQUEST["hidImageCss".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestImageP"] = $_REQUEST["hidDrawingTestImageP".$intTempDrawCount];
								$arrDrawingData["patId"] = $patientid;
								$arrDrawingData["hidCanvasImgData"] = $_REQUEST["hidCanvasImgData".$intTempDrawCount];
								$drawingFileName = "/EXTERNAL_idoc_drawing_".date("YmdHsi")."_".session_id()."_".$intTempDrawCount.".png";
								$arrDrawingData["drawingFileName"] = $drawingFileName;
								$arrDrawingData["drawingFor"] = "EXTERNAL";
								$arrDrawingData["drawingForMasterId"] = $insertId;
								$arrDrawingData["formId"] = $elem_formId;
								$arrDrawingData["hidDrawingTestName"] = $_REQUEST["hidDrawingTestName".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestId"] = $_REQUEST["hidDrawingTestId".$intTempDrawCount];
								$arrDrawingData["hidImagesData"] = $_REQUEST["hidImagesData".$intTempDrawCount];
								$arrDrawingData["hidDrawingId"] = (int)$_REQUEST["hidExternalDrawingId".$intTempDrawCount];
								$arrDrawingData["examMasterTable"] = "chart_external_exam";
								$arrDrawingData["examMasterTablePriColumn"] = "ee_id";
								$arrDrawingData["drwNE"] = $_REQUEST["elem_drwNE".$intTempDrawCount];
								$arrDrawingData["hidDrwDataJson"] = $_REQUEST["hidDrwDataJson".$intTempDrawCount];
								//pre($arrDrawingData);
								$OBJDrawingData->insertDrawingData($arrDrawingData);
								//$flagCFD=0;						
							}else{
							
								//Check old drawing exists for carry forward
								if(!empty($_POST["hidExternalDrawingId".$intTempDrawCount])){
									$arrCFD_ids[] = $_POST["hidExternalDrawingId".$intTempDrawCount];
									//$flagCFD_drw1=1;
								}
							
							}
							
						}
						$arrIDocId = array();
						$strIDocId = "";
						$qryGetIDocIdInMasetr = "select id from ".constant("IMEDIC_SCAN_DB").".idoc_drawing where drawing_for = 'EXTERNAL' and drawing_for_master_id = '".$insertId."' 
													and patient_id = '".$patientid."' and patient_form_id = '".$elem_formId."' ";
						$rsGetIDocIdInMasetr = imw_query($qryGetIDocIdInMasetr);
						if(imw_num_rows($rsGetIDocIdInMasetr) > 0){
							while($rowGetIDocIdInMasetr = imw_fetch_array($rsGetIDocIdInMasetr)){
								$arrIDocId[] = $rowGetIDocIdInMasetr["id"];
							}
						}
						if(count($arrIDocId) > 0){
							$strIDocId = implode(",", $arrIDocId);
						}
						$qryUpdateExternalIDoc = "update chart_external_exam set idoc_drawing_id = '".$strIDocId."' where ee_id = '".$insertId."' ";
						$rsUpdateExternalIDoc = imw_query($qryUpdateExternalIDoc);
								
					}
					
					//if(!empty($_POST["elem_eeId_LF"])&&!empty($flagCFD_drw1) && $flagCFD==1){ // Check if Last visit Drawing exists
					if(!empty($_POST["elem_eeId_LF"]) && count($arrCFD_ids)>0){ // Check if Last visit Drawing exists
						// Carry Forward iDOC Draw : This is done because drawing is not saved when not touched but we need to carry forward to display.Drawing status will be grey.			
						$arrIN = array();
						$arrIN["pid"]=$patientid;
						$arrIN["formId"]=$elem_formId;
						$arrIN["examId"]=$insertId;
						$arrIN["exam"]="EXTERNAL";
						$arrIN["examIdLF"]=$_POST["elem_eeId_LF"];
						$arrIN["strDrwIdsLF"]=implode(",", $arrCFD_ids);
						$arrIN["examMasterTable"]="chart_external_exam";
						$arrIN["examMasterTablePriColumn"]="ee_id";
						$OBJDrawingData->carryForward($arrIN);
						//
					}				
				
				}else if($elem_editMode == "1"){
					//Update
					$sql = "UPDATE chart_external_exam ".
						 "SET ".
						 "external_exam = '".$elem_external_exam."', external_exam_summary='".$elem_external_exam_summary."', ".
						 "ee_desc='".$eeDesc."',"; 
							if(empty($_REQUEST["hidBlEnHTMLDrawing"]) == true){
								$sql .= "ee_drawing='".$eeDrawing."',";
								$sql .= "drawing_insert_update_from='0',";
							}
							else{
								$sql .= "drawing_insert_update_from='1',";
							}
						$sql .=  "examined_no_change='".$noChange."', ".
						 "exam_date='".$examDate."', not_applicable='".$notApplicable."', wnl='".$wnl."', ".
						 "external_exam_os='".$elem_external_exam_os."', isPositive='".$isPositive."', ".
						 "sumOsEE='".$sumOsEE."', ".
						 "wnlEeOd='".$wnlEeOd."', ".
						 "wnlEeOs='".$wnlEeOs."', ".
						 "descExternal='".$descExternal."', ".
						 "uid = '".$_SESSION["authId"]."', ".
						 "ncEe='".$ncEe."',ncDraw='".$ncDraw."',wnlEe='".$wnlEe."',wnlDraw='".$wnlDraw."', ".
						 "posEe='".$posEe."',posDraw='".$posDraw."',wnlDrawOd='".$wnlDrawOd."',wnlDrawOs='".$wnlDrawOs."', ee_desc_os='".$eeDesc_os."', ".
						 "statusElem = '".$statusElem."', ut_elem = '".$ut_elem."', ".
						 /*"modi_note_od = CONCAT('".mysql_real_escape_string($modi_note_od)."',modi_note_od), ".
						 "modi_note_os = CONCAT('".mysql_real_escape_string($modi_note_os)."',modi_note_os), ".*/
						 "modi_note_Arr = '".sqlEscStr($seri_modi_note_externalArr)."', ".
						 "last_opr_id = '".$last_opr_id."'".
						 "WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
					
					$res = sqlQuery($sql);
					$insertId = $eeId;
					if($eeIDExam > 0){
						$insertId = $eeIDExam;
					}
					
					//
					if(isset($_REQUEST["hidBlEnHTMLDrawing"]) == true && empty($_REQUEST["hidBlEnHTMLDrawing"]) == false && $_REQUEST["hidBlEnHTMLDrawing"] == "1" && (int)$insertId > 0){
						$recordModiDateDrawing=0;
						$arr_updrw_ids=array();
						for($intTempDrawCount = 0; $intTempDrawCount < 25; $intTempDrawCount++){
							if($_REQUEST["hidDrawingChangeYesNo".$intTempDrawCount] == "yes"){
								$arrDrawingData = array();
								$arrDrawingData["imagePath"] = $oSaveFile->getUploadDirPath();
								$arrDrawingData["hidRedPixel"] = $_REQUEST["hidRedPixel".$intTempDrawCount];
								$arrDrawingData["hidGreenPixel"] = $_REQUEST["hidGreenPixel".$intTempDrawCount];
								$arrDrawingData["hidBluePixel"] = $_REQUEST["hidBluePixel".$intTempDrawCount];
								$arrDrawingData["hidAlphaPixel"] = $_REQUEST["hidAlphaPixel".$intTempDrawCount];
								$arrDrawingData["hidImageCss"] = $_REQUEST["hidImageCss".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestImageP"] = $_REQUEST["hidDrawingTestImageP".$intTempDrawCount];
								$arrDrawingData["patId"] = $patientid;
								$arrDrawingData["hidCanvasImgData"] = $_REQUEST["hidCanvasImgData".$intTempDrawCount];
								$drawingFileName = "/EXTERNAL_idoc_drawing_".date("YmdHsi")."_".session_id()."_".$intTempDrawCount.".png";
								$arrDrawingData["drawingFileName"] = $drawingFileName;
								$arrDrawingData["drawingFor"] = "EXTERNAL";
								$arrDrawingData["drawingForMasterId"] = $insertId;
								$arrDrawingData["formId"] = $elem_formId;
								$arrDrawingData["hidDrawingTestName"] = $_REQUEST["hidDrawingTestName".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestId"] = $_REQUEST["hidDrawingTestId".$intTempDrawCount];
								$arrDrawingData["hidImagesData"] = $_REQUEST["hidImagesData".$intTempDrawCount];
								$arrDrawingData["hidDrawingId"] = (int)$_REQUEST["hidExternalDrawingId".$intTempDrawCount];
								$arrDrawingData["examMasterTable"] = "chart_external_exam";
								$arrDrawingData["examMasterTablePriColumn"] = "ee_id";
								$arrDrawingData["drwNE"] = $_REQUEST["elem_drwNE".$intTempDrawCount];
								$arrDrawingData["hidDrwDataJson"] = $_REQUEST["hidDrwDataJson".$intTempDrawCount];
								//pre($arrDrawingData,1);
								$arrDrawingData["hidDrawingId"] = $OBJDrawingData->updateDrawingData($arrDrawingData);
								
								if(!empty($arrDrawingData["hidDrawingId"])){
									$recordModiDateDrawing=$arrDrawingData["hidDrawingId"];
									$arr_updrw_ids[]=$arrDrawingData["hidDrawingId"];
								}						
							}else{
								if(!empty($_POST["hidExternalDrawingId".$intTempDrawCount])){
									$arr_updrw_ids[] = $_POST["hidExternalDrawingId".$intTempDrawCount];							
								}
							}
						}
						
						//delete records of previous visit if any --
						$OBJDrawingData->deleteNoSavedDrwing(array($patientid, $elem_formId,$insertId,"EXTERNAL"), $arr_updrw_ids);
						//--				
						
						//form Id
						list($strIDocId, $str_row_modify_Draw)=$OBJDrawingData->getExamDocids(array($patientid, $elem_formId,$insertId,"EXTERNAL",$recordModiDateDrawing));
						
						$qryUpdateExternalIDoc = "update chart_external_exam set idoc_drawing_id = '".$strIDocId."' ".$str_row_modify_Draw." where ee_id = '".$insertId."' ";
						$rsUpdateExternalIDoc = imw_query($qryUpdateExternalIDoc);
						
					}
					
				}
				
				// Make chart notes valid
				$this->makeChartNotesValid();
				
				//Set Change Date Arc Rec --
				$this->setChangeDtArcRec("chart_external_exam");
				//Set Change Date Arc Rec --			
			}
			
			//combine
			$strExamsAll = $this->combineExamFindings($strExamsAllOd, $strExamsAllOs);
			$strExamsAll = $this->makeArrString($strExamsAll);	
			
			//			
			$arrRet["Exam"] = "Ee";
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