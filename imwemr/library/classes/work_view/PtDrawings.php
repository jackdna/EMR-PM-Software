<?php

class PtDrawings extends Patient{
	public $pid, $fid, $finalize_flag;	
	public function __construct($pid, $fid, $flg_final="0" ){
		$this->pid = !empty($pid) ? $pid : $_SESSION["patient"];
		parent::__construct($this->pid);
		$this->fid = $fid;
		$this->finalize_flag = $flg_final;		
	}
	
	function getPtDrawings(){
		$str="";
		$sql="SELECT id,  row_created_date_time FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing  WHERE patient_id='".$this->pid."' AND deletedby='0' ORDER BY id DESC  ";
		$rez=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$dos = wv_formatDate($row["row_created_date_time"],0,1); //DATE_FORMAT(row_created_date_time,'".getSqlDateFormat()." %H-%i-%s') as DOS,			
			$str.= "<div class=\"row\"><label>".($i+1).".</label> <label><a href=\"?elem_action=Drawingpane&id=".$row["id"]."\" class=\"btn btn-link btn-block\">".$dos."</a></label></div>";
		}		
		return $str;
	}
	
	function loadDrawings(){
	
		$flg_cl_drw = $_REQUEST['cl_draw'];	
	
		//return if empty
		if(empty($this->pid)||empty($this->fid)){return;}
		$elem_patientId = $patient_id = $this->pid;
		$elem_formId=$form_id=$this->fid;
		$finalize_flag = $this->finalize_flag;
		
		$id_ed=0;
		if(!empty($flg_cl_drw)){ //Contect lens Drawings
			if(isset($_GET["finalize_id"]) && !empty($_GET["finalize_id"]))
			{
				#form_id
				$elem_formId=$form_id = $_GET["finalize_id"];
				$finalize_flag = 1;
			}

			$idoc_drawing_id=$_REQUEST['idoc_drawing_id'];
			$parentCtrlId=$_REQUEST['parentCtrlId'];
			$parentImgId=$_REQUEST['parentImgId'];
			$eye=$_REQUEST['eye'];
			$sno=$_REQUEST['sno'];
			$od_desc=$_REQUEST['od_desc'];
			$os_desc=$_REQUEST['os_desc'];
			$descriptionA='description_A_'.$eye.$sno;
			$descriptionB='description_B_'.$eye.$sno;
			if(isset($idoc_drawing_id) && !empty($idoc_drawing_id)){
				$id_ed=$idoc_drawing_id;
			}
			$dbTollImage_default="imgCorneaCanvas";
			$stop_dis_prv_drawings=" hidden ";
			$stop_dis_text_box="";
			$examName="DrawCL";
			$hidDrawid="hidDrawCLDrawingId";
		}else{
			$hidDrawid="hidDrawPaneDrawingId";
			$examName="DrawPane";
			$stop_dis_text_box=" hidden ";
			$stop_dis_prv_drawings="";
			$dbTollImage_default="imgPicConCanvas";
			if(isset($_GET["id"]) && !empty($_GET["id"])){
				$id_ed=$_GET["id"];
			}//
		}
		
		
		//
		$ocn = new ChartNote($patient_id, $form_id);
		
		// variable for testing drawing new
		$GLOBALS['New_Drawing_Demo']=1;
		
		$OBJDrawingData = new CLSDrawingData();
		$blEnableHTMLDrawing = false;
		$blEnableHTMLDrawing = $OBJDrawingData->getHTMLDrawingDispStatus();
		$drawCntlNum=2; //This setting will decide number of drawing instances		
		list($idoc_arrDrwIcon, $idoc_htmlDrwIco) = $OBJDrawingData->drwico_getDrwIcon();
		$arrDrwaingData=array();		
		
		//Is Reviewable
		$isReviewable = $ocn->isChartReviewable($_SESSION["authId"]);
		
		//
		$elem_per_vo =  ChartPtLock::is_viewonly_permission();
		$ProClr = User::getProviderColors();//
		//$logged_user_type = $_SESSION["logged_user_type"];
		$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
		
		//default
		$elem_editMode="0";
		$elem_examDate=date("Y-m-d H:i:s");
		$myflag=false;
		
		//--Get patient Drawings --
				
		
		if(isset($id_ed) && !empty($id_ed)){

			//$arrDrwaingData = $OBJDrawingData->getHTMLDrawingData_PtChart($patient_id,$form_id);
			$arrDrwaingData = $OBJDrawingData->getHTMLDrawingData($id_ed,1);

			$tmp = count($arrDrwaingData[0]);
			if($tmp>$drawCntlNum){ $drawCntlNum = $tmp; }

			//echo "<pre>";
			//print_r($arrDrwaingData);

		}//

		//--Get patient Drawings --
		
		//All drawings
		$prv_drwings= "";
		$prv_drwings=$this->getPtDrawings();		
		
		##
		header('Content-Type: text/html; charset=utf-8');
		##
		include($GLOBALS['fileroot']."/interface/chart_notes/view/pt_drawings.php");
	}

	function saveDrawings(){
		$patientid=$this->pid;
		$formid=$this->fid;
		
		//--
		if(isset($_REQUEST["parentCtrlId"])){ //CL DRAWINGS
			$drawingFor="DrawCL";
			$hidDrawid="hidDrawCLDrawingId";
			
		}else{
			$drawingFor="DrawPane";
			$hidDrawid="hidDrawPaneDrawingId";
		}
		//--		
		
		//
		$OBJDrawingData = new CLSDrawingData();
		$objImageManipulation = new CLSImageManipulation();
		$oSaveFile = new SaveFile($patientid);
		
		//--
		if(isset($_REQUEST["hidBlEnHTMLDrawing"]) == true && empty($_REQUEST["hidBlEnHTMLDrawing"]) == false && $_REQUEST["hidBlEnHTMLDrawing"] == "1"){
			$recordModiDateDrawing=0;
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
					$drawingFileName = "/".$drawingFor."_idoc_drawing_".date("YmdHsi")."_".session_id()."_".$intTempDrawCount.".png";
					$arrDrawingData["drawingFileName"] = $drawingFileName;
					$arrDrawingData["drawingFor"] = "".$drawingFor;
					$arrDrawingData["drawingForMasterId"] = 0;
					$arrDrawingData["formId"] = $_REQUEST["elem_formId"];
					$arrDrawingData["hidDrawingTestName"] = $_REQUEST["hidDrawingTestName".$intTempDrawCount];
					$arrDrawingData["hidDrawingTestId"] = $_REQUEST["hidDrawingTestId".$intTempDrawCount];
					$arrDrawingData["hidImagesData"] = $_REQUEST["hidImagesData".$intTempDrawCount];
					$arrDrawingData["hidDrawingId"] = (int)$_REQUEST["".$hidDrawid.$intTempDrawCount];
					$arrDrawingData["examMasterTable"] = "";
					$arrDrawingData["examMasterTablePriColumn"] = "";
					$arrDrawingData["drwNE"] = $_REQUEST["elem_drwNE".$intTempDrawCount];
					$arrDrawingData["attach2Exam"] = "1";
					$arrDrawingData["hidDrwDataJson"] = $_REQUEST["hidDrwDataJson".$intTempDrawCount];
					
					//pre($arrDrawingData,1);
					//echo "<pre>";
					//print_r($arrDrawingData);exit();
					
					//check if drawing belong to previous visit  then insert else update
					if(!empty($arrDrawingData["hidDrawingId"]) && !empty($arrDrawingData["formId"])){						
						//check drawing belong to any exam then carry forward that exam--
						list($tmp_chart_exam, $tmp_master_id, $tmp_hidDrawingId) = $OBJDrawingData->checkDrawing4ExamNCarryFwd($arrDrawingData["hidDrawingId"], $arrDrawingData["patId"], $arrDrawingData["formId"]);
						if(!empty($tmp_chart_exam) && !empty($tmp_master_id) && !empty($tmp_hidDrawingId) && $tmp_chart_exam!=$drawingFor){
							//Exam is carried forward along with all drawings so
							$arrDrawingData["drawingFor"] = $tmp_chart_exam;
							$arrDrawingData["drawingForMasterId"] = $tmp_master_id;
							$arrDrawingData["hidDrawingId"]=$tmp_hidDrawingId;
							$arrDrawingData["flgDoNotSetModify"]="1";
						}else if(!empty($tmp_chart_exam) && $tmp_chart_exam==$drawingFor){
							// carry forward drawpane drawing
							$arrDrawingData["drawingFor"] = $drawingFor;
							$arrDrawingData["drawingForMasterId"] = 0;
							$arrDrawingData["hidDrawingId"]=0;//insert new							
						}
					}					
					//--					
					
					$drawingId=$OBJDrawingData->updateDrawingData($arrDrawingData);
					
					if(!empty($arrDrawingData["hidDrawingId"])){
						$recordModiDateDrawing=$arrDrawingData["hidDrawingId"];
					}						
				}
			}
			
			//form Id
			//list($strIDocId, $str_row_modify_Draw)=$OBJDrawingData->getExamDocids(array($patientid, $formId,$insertId,"LA",$recordModiDateDrawing));
			//$qryUpdateExternalIDoc = "update chart_la set idoc_drawing_id = '".$strIDocId."' ".$str_row_modify_Draw." where la_id = '".$insertId."' ";
			//$rsUpdateExternalIDoc = mysql_query($qryUpdateExternalIDoc);			
			
		}		
		//--
		
		if($drawingFor="DrawCL"){
			
			$parentCtrlId= $_REQUEST['parentCtrlId'];
			$parentImgId= $_REQUEST['parentImgId'];
			$descriptionA= $_REQUEST['descriptionA'];
			$descriptionB= $_REQUEST['descriptionB'];
			$od_desc= $_REQUEST['od_desc'];
			$os_desc= $_REQUEST['os_desc'];
			$hasDrawing='yes';
			if($drawingId<=0 || $drawingId==''){
				$drawingId=$_REQUEST['hidDrawCLDrawingId0'];
			}
			if($_REQUEST["hidDrwDataJson0"]=='' || $_REQUEST["hidDrwDataJson0"]=='{"objects":[],"background":""}'){
				$hasDrawing='no';
			}
			
			echo "<script>
				var parentCtrlId='".$parentCtrlId."';
				var parentImgId='".$parentImgId."';
				var descriptionA='".$descriptionA."';
				var descriptionB='".$descriptionB."';
				var od_desc='".$od_desc."';
				var os_desc='".$os_desc."';
				var drawingId='".$drawingId."';
				var hasDrawing='".$hasDrawing."';
				
				if(parentCtrlId!='' && drawingId>0){
					top.opener.document.getElementById(parentCtrlId).value=".$drawingId.";					
				}
				if(hasDrawing=='yes'){
					top.opener.$(\"#".$parentImgId."\").css({\"border\":\"1px solid red;\"});
					/*top.opener.document.getElementById(parentImgId).src='images/odHasDrawing.png';*/
				}else{
					top.opener.$(\"#".$parentImgId."\").css({\"border\":\"0px\"});
					/*top.opener.document.getElementById(parentImgId).src='images/odDrawing.png';*/
				}
				if(top.opener.document.getElementById(descriptionA)){
					top.opener.document.getElementById(descriptionA).value=od_desc;
				}
				if(top.opener.document.getElementById(descriptionB)){
					top.opener.document.getElementById(descriptionB).value=os_desc;
				}
				
				//try{
				//	if(typeof window.opener.hidDrawCLDrawingId != 'undefined'){					
				//	window.opener.hidDrawCLDrawingId(\"drawingpane\");
				//}
				window.close();
				//}catch(e){ }
			</script>";
			
		}else{		
			echo "<script>
					try{
					if(typeof window.opener.loadExamsSummary != 'undefined'){					
						window.opener.loadExamsSummary(\"drawingpane\");
					}
					window.close();
					}catch(e){ }
				</script>";
		}	
	
	}
	
	function getDrawSUC(){
		$pid = $this->pid;
		$formId=$this->fid;
		$examId=$_GET["examId"];		
		$scanOrUpload=$_GET["scanOrUpload"];
		$scan=$_GET["scan"];
		$scanUploadfor=$_GET["scanUploadfor"];
		$canvasId=$_GET["canvasId"];
		
		//
		global $phpHTTPProtocol, $phpServerPort, $web_root;
		if($phpServerIP != $_SERVER['HTTP_HOST']){
			$phpServerIP = $_SERVER['HTTP_HOST'];
			$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
		}
		
		//
		$phrase_exm_id="";
		if(!empty($examId)){$phrase_exm_id=" sc.test_id = '".$examId."' and ";}		
		//	
		$qryGetSUP = "select sc.scan_id as scanId from ".constant("IMEDIC_SCAN_DB").".scans sc 					
					where ".$phrase_exm_id." sc.form_id = '".$formId."' and sc.image_form = '".$scanUploadfor."' 
					and sc.patient_id = '".$pid."' and sc.status = '0'";
		$rsGetSUP = imw_query($qryGetSUP);
		$flg_show_preview = imw_num_rows($rsGetSUP);
		
		//
		include($GLOBALS['incdir']."/chart_notes/view/scan_upload_drawing.php");
	}


	function upload_idoc_drawings(){
		//print_r($_POST);		
		//print_r($_FILES);		
		
		$rqExamId = $patId = $rqFormId = 0;
		$rqScanUploadFor = "";
		$rqExamId = (int)$_REQUEST['examId'];
		$rqFormId = $this->fid ; //(int)$_REQUEST['formId'];
		$rqScanUploadFor = trim($_REQUEST["scanUploadfor"]);
		$rqCanvasId = trim($_REQUEST["canvasId"]);
		$saveFlg=$_REQUEST["save"];
		$patId = $this->pid;
		$now = wv_dt("now");
		$objSaveFile = new SaveFile($patId);
		//$objImageManipulation = new CLSImageManipulation();
		
		//Inserting if Exam Id is not set
		if(($rqExamId == 0) && (empty($rqScanUploadFor) == false) && (empty($rqFormId) == false) && $rqScanUploadFor!="DrawingPane"){
			$sql = "";
			switch($rqScanUploadFor){
				case "EOM_DSU":					
					$sql = "INSERT INTO chart_eom (eom_id , form_id, patient_id, exam_date) ".
							"VALUES(NULL, '".$rqFormId."', '".$patId."', '".$now."') ";	
					break;
				case "LA_DSU":						
					$sql = "INSERT INTO chart_drawings (id , form_id, patient_id, exam_date, exam_name) ".
							"VALUES(NULL, '".$rqFormId."', '".$patId."', '".$now."', 'LA') ";	
					break;
				case "IOP_GON_DSU":						
					$sql = "INSERT INTO chart_gonio (gonio_id , form_id, patient_id, examDateGonio) ".
							"VALUES(NULL, '".$rqFormId."', '".$patId."', '".$now."') ";	
					break;
				case "SLE_DSU":						
					$sql = "INSERT INTO chart_drawings (id , form_id, patient_id, exam_date, exam_name) ".
							"VALUES(NULL, '".$rqFormId."', '".$patId."', '".$now."', 'SLE') ";
					break;
				case "FUNDUS_DSU":						
					$sql = "INSERT INTO chart_drawings (id, form_id, patient_id, exam_date, exam_name) ".
							"VALUES(NULL, '".$rqFormId."', '".$patId."', '".$now."', 'FundusExam') ";
					break;	
				break;
				case "EXTERNAL_DSU":						
					$sql = "INSERT INTO chart_external_exam (ee_id , form_id, patient_id, exam_date) ".
							"VALUES(NULL, '".$rqFormId."', '".$patId."', '".$now."') ";	
				break;
			}
			if(empty($sql) == false){
				$rsSql = imw_query($sql);
				$rqExamId = imw_insert_id();
			}
		}
		
		//Show the number of files to upload
		$files_to_upload = 1;
		
		//Directory where the uploaded files have to come  
		$upLoadPath = "".$objSaveFile->getUploadDirPath()."/"; //$GLOBALS['fileroot'].'/interface/main/uploaddir/';
		
		// **************** Upload function ********************

		$allowed_ext = "jpeg, jpg, gif, png, pdf";
		$max_size = 1024 * 500; // Max: 500K.
		
		
		//echo  "<br/> ".$_REQUEST['method']." - ".$rqExamId." - ".$rqScanUploadFor." - ".$rqFormId." - ".$rqScanUploadFor." - ".$patId;
		
		if(($_REQUEST['method']) && (empty($rqExamId) == false || $rqScanUploadFor=="DrawingPane") && (empty($rqFormId) == false) && (empty($rqScanUploadFor) == false) && (empty($patId) == false)){
			
			$extensions2type = array(
			    "gif" => IMAGETYPE_GIF ,
			    "jpg" => IMAGETYPE_JPEG ,
			    "jpeg" => IMAGETYPE_JPEG ,
			    "png" => IMAGETYPE_PNG 
			);
			
			//Scan the file
			if($_REQUEST['method'] == "scan"){
				$uploads = false;
				for($i = 0 ; $i < $files_to_upload; $i++){
					if($_FILES['file']['name'][$i]){
						$uploads = true;
						if($_FILES['file']['name'][$i]){
							// ******** CHECK FILE EXTENSION & FILE SIZE*******************
							$extension = pathinfo($_FILES['file']['name'][$i]);					
							$extension = $extension[extension];
							$extensionAccepted = 0;
							$allowed_paths = explode(", ", $allowed_ext);
							for($j = 0; $j < count($allowed_paths); $j++) {
								 if ($allowed_paths[$j] == "$extension") {
									 $extensionAccepted = 1;
								 }
							}
							if(! $extensionAccepted) {
								$message .= $_FILES['file']['name'][$i] . " has invalid extension.<br>";
								continue;
							}
							// donot allow .php
							if(strpos($_FILES['file']['name'][$i],'.php')!==false) {
								$message .= $_FILES['file']['name'][$i] . " has invalid extension 2.<br>";
								continue;
							}
							
							if(!wv_check_mime("img", $_FILES['file']['tmp_name'][$i])){
								$message .= $_FILES['file']['name'][$i] . " is an invalid image.<br>";
								continue;
							}
							
							
							/// Create Directory of site care and upload image
							$patientDir = "PatientId_".$patId."/";
							$idocDrawingDirName = "idoc_drawing/";
							$idocDrawingCanvasSUDirName = "scan_upload/";
							
							if(is_dir($upLoadPath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasSUDirName) == false){
								mkdir($upLoadPath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasSUDirName, 0777, true);
							}
							
							$pathToWrite = $upLoadPath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasSUDirName;
							
							$fileName = $_FILES['file']['name'][$i];
							$fileNameExtIndex = strrpos ( $fileName, "." );
					
							if($fileNameExtIndex === false){
								//Add Ext.
								$fileNameExt = ".jpg";								
							}
							else{
								//separate image name and ext
								$fileNameExt = substr($fileName,$fileNameExtIndex);					
								$fileName = substr($fileName,0,$fileNameExtIndex);						
							}
							
							//Apend timestamp in fileName
							$fileName .= "-".time();
							
							//New File Name
							$fileName = md5($fileName).$fileNameExt;										
							$file_to_upload = $pathToWrite.$fileName;
							$file_pointer = "/".$patientDir.$idocDrawingDirName.$idocDrawingCanvasSUDirName.$fileName;
							
							//move_uploaded_file($_FILES['file']['tmp_name'][$i],$file_to_upload);							
							// echo "\n".$_FILES['file']['tmp_name'][$i] . " | " . $file_to_upload . "<hr>";
							//chmod($file_to_upload,0777);
							$arfile_up = array("name"=>$fileName,"tmp_name"=>$_FILES['file']['tmp_name'][$i],"size"=>$_FILES['file']['size'][$i],"type"=>$_FILES['file']['type'][$i]);
							//$f_name_save=md5($f_name).".".$ext;
							$file_pointer = $objSaveFile->copyfile($arfile_up,"idoc_drawing/scan_upload", $fileName);
							
							$doctitle = $_POST["DocTitle"];
							$saveFlg=0; //
							$qryInsertDSU = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".scans SET
												patient_id = '".$patId."',
												form_id = '".$rqFormId."',
												test_id = '".$rqExamId."',
												image_form = '".$rqScanUploadFor."', 
												image_name = '".$fileName."', 
												file_type = '".$extension."', 
												scan_or_upload='scan', 
												created_date = '".wv_dt('now')."', 
												file_path = '".$file_pointer."',
												draw_template = '".$saveFlg."',
												testing_docscan_operator='".$_SESSION["authId"]."'";
							$rsInsertDSU = imw_query($qryInsertDSU);
							$intNewSacnId = imw_insert_id();
							if($intNewSacnId > 0){
								$objSaveFile = new SaveFile($patId);
								
								$patientDir = "PatientId_".$patId."";				
								$idocDrawingDirName = "/idoc_drawing/scan_upload";				
								$resizeImage = "/resize";
								
								if(is_dir($upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage) == false){
									mkdir($upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage, 0777, true);
								}
								
								if(is_dir($upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage."/thumb") == false){
									mkdir($upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage."/thumb", 0777, true);
								}
								
								$dbDocPathFull = $upLoadPath.$file_pointer;
								//$imgUpLoadPath = $GLOBALS['webroot']."/interface/main/uploaddir";
								//$strPathForCanvas = $imgUpLoadPath.$file_pointer;
								
								if(file_exists($dbDocPathFull) == true){
									$dbDocName = $fileName;
									$newResizeFileName = $upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage."/".$dbDocName;
									$newResizeFileNameForDB = "/".$patientDir.$idocDrawingDirName.$resizeImage."/".$dbDocName;
									$newResizeFileNamePath = $objSaveFile->getFilePath("/".$patientDir.$idocDrawingDirName.$resizeImage."/".$dbDocName, "w");
									if(file_exists($newResizeFileName) == false){
										
										//resize big
										$file_pointer_inc_resize=$objSaveFile->ptDir("idoc_drawing/scan_upload/resize","i");
										$file_pointer_inc_resize.="/".$fileName;							
										$t = $objSaveFile->createThumbs($dbDocPathFull,$file_pointer_inc_resize,730,465);
									
										//$newResizeFileName_T = $upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage."/thumb/".$dbDocName;
										//$newResizeFileNamePath_T = $objSaveFile->getFilePath($patientDir.$idocDrawingDirName.$resizeImage."/thumb/".$dbDocName, "w");
										
										//resize small
										$file_pointer_inc_resize=$objSaveFile->ptDir("idoc_drawing/scan_upload/thumb","i");
										$file_pointer_inc_resize.="/".$fileName;
										$t = $objSaveFile->createThumbs($dbDocPathFull,$file_pointer_inc_resize,24,24);
										
										echo "<script>loadTestImage('".$newResizeFileNamePath."', '".$rqScanUploadFor."', '".$rqExamId."', '".$newResizeFileNameForDB."', '".$rqCanvasId."');</script>";
									
									}
								}								
							}
							$message .= $_FILES['file']['name'][$i]." uploaded.<br>";	
						}
					}
				}
				if(!$uploads)  $message = "No files selected!";
			}
			elseif($_REQUEST['method'] == "upload"){
				$intNewSacnId = 0;
				//Copy File
				$file_pointer = "";
				$f_name = $_FILES['flUpload']["name"];
				$f_type = $_FILES['flUpload']["type"];
				$f_size = $_FILES['flUpload']["size"];
				$f_tmp = $_FILES['flUpload']["tmp_name"];
				$ext = pathinfo($_FILES['flUpload']['name'], PATHINFO_EXTENSION);
				$ext = strtolower($ext);
				if ($f_type == "application/pdf" || ($f_type!="image/pjpeg" && $f_type!="image/jpeg" && $f_type!="image/jpg" && $f_type!="image/png" && $f_type!="image/gif")){
					//continue;			
					
				}else{
					
					if(wv_check_mime("img", $f_tmp)){
					//
					$f_name = str_replace(" ","_", $f_name);
					$f_name_save=md5($f_name).".".$ext;
					$file_pointer = $objSaveFile->copyfile($_FILES['flUpload'],"idoc_drawing/scan_upload", $f_name_save);
					
					$qryInsertDSU = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".scans SET
									patient_id = '".$patId."',
									form_id = '".$rqFormId."',
									test_id = '".$rqExamId."',
									image_form = '".$rqScanUploadFor."', 
									image_name = '".$f_name."', 
									file_type = '".$f_type."', 
									scan_or_upload='upload', 
									created_date = '".$now."',
									doc_upload_date  = '".$now."', 
									file_path = '".$file_pointer."',
									draw_template = '".$saveFlg."',
									operator_id='".$_SESSION["authId"]."'";
					//echo $qryInsertDSU;				
					$rsInsertDSU = imw_query($qryInsertDSU);
					$intNewSacnId = imw_insert_id();
					if($intNewSacnId > 0){						
						$file_pointer_inc = $objSaveFile->getFilePath($file_pointer, "i");	
						if(file_exists($file_pointer_inc)){
							//resize big
							$file_pointer_inc_resize=$objSaveFile->ptDir("idoc_drawing/scan_upload/resize","i");
							$file_pointer_inc_resize.="/".$f_name_save;							
							$t = $objSaveFile->createThumbs($file_pointer_inc,$file_pointer_inc_resize,730,465);
							
							$newResizeFileNamePath = $objSaveFile->getFilePath($file_pointer_inc_resize,"w2");
							$newResizeFileNameForDB = $objSaveFile->getFilePath($file_pointer_inc_resize,"db2");

							//resize small
							$file_pointer_inc_resize=$objSaveFile->ptDir("idoc_drawing/scan_upload/thumb","i");
							$file_pointer_inc_resize.="/".$f_name_save;
							$t = $objSaveFile->createThumbs($file_pointer_inc,$file_pointer_inc_resize,24,24);							
							
							echo "<script>loadTestImage('".$newResizeFileNamePath."', '".$rqScanUploadFor."', '".$rqExamId."', '".$newResizeFileNameForDB."', '".$rqCanvasId."');</script>";
						}
					}
					}//	
				}				
			}
		}
	}
	
	function rem_scan_upload_doc(){
		//Delete
		if(empty($_REQUEST['delScanDoc']) == false && isset($_REQUEST['delScanDoc']) == true){
			$qryDelScan = "update ".constant("IMEDIC_SCAN_DB").".scans set status = '1' where scan_id = '".$_REQUEST['delScanDoc']."'";
			$rsDelScan = imw_query($qryDelScan);				
		}
	}
	
	function get_scan_upload_preview(){		
		
		$rqExamId = $patId = $rqFormId = $rqScanUploadFor = $rqCanvasId = "";
		$rqExamId = (int)$_REQUEST['examId'];
		$rqFormId = (int)$_REQUEST['formId'];
		$rqScanUploadFor = trim($_REQUEST["scanUploadfor"]);
		$rqCanvasId = trim($_REQUEST["canvasId"]);
		
		$patId = $this->pid;
		if((empty($rqExamId) == true) && (empty($patId) == true)){
			exit("Please select patient.");
		}
		
		$strTrFile = $strIframeFilePath = $dbPatName = $dbPatMname = "";
		
		//$imgUpLoadPath = $GLOBALS['webroot']."/interface/main/uploaddir";
		$qryGetData = "select sc.scan_id as scanId, sc.image_name as docName, DATE_FORMAT(sc.created_date, '%m-%d-%Y') as docDate, sc.file_type as docType, sc.file_path as docPath,
						concat(pd.lname,', ',pd.fname) as patName , pd.mname as patMname
						from ".constant("IMEDIC_SCAN_DB").".scans sc 
						LEFT JOIN ".$dbase.".patient_data pd on pd.id = sc.patient_id
						where sc.test_id = '".$rqExamId."'and sc.form_id = '".$rqFormId."' and sc.image_form = '".$rqScanUploadFor."' and sc.patient_id = '".$patId."' and sc.status = '0'
						";
		$rsGetData = imw_query($qryGetData);
		if($rsGetData){				
			if(imw_num_rows($rsGetData) > 0){
				$objSaveFile = new SaveFile($patId);
				$upLoadPath = $objSaveFile->getUploadDirPath();
				$patientDir = "/PatientId_".$patId."";				
				$idocDrawingDirName = "/idoc_drawing/scan_upload";				
				$resizeImage = "/resize";
				$thumbImage = "/thumbnail";
				$i = 1;
				while($rowGetData =  imw_fetch_array($rsGetData)){
					$dbScanId = 0;
					$dbDocName = $dbDocDate = $dbDocType = $dbDocPath = $dbDocPathFull = "";
					$dbScanId = $rowGetData["scanId"];
					$dbDocName = $rowGetData["docName"];
					$dbDocDate = $rowGetData["docDate"];
					$dbDocType = $rowGetData["docType"];
					$dbDocPath = $rowGetData["docPath"];
					if($dbPatName == ""){
						$dbPatName = $rowGetData["patName"];
						$dbPatMname = $rowGetData["patMname"];
					}
					$dbDocPathFull = $upLoadPath.$dbDocPath;
					//$strPathForCanvas = $imgUpLoadPath.$dbDocPath;
					
					if((file_exists($dbDocPathFull) == true) && ($dbDocType != "application/pdf")){
						/*
						
						$newResizeFileNameForDB = $patientDir.$idocDrawingDirName.$resizeImage."/".$dbDocName;
						$newResizeFileNamePath = $objSaveFile->getFilePath($patientDir.$idocDrawingDirName.$resizeImage."/".$dbDocName, "w");
						if(file_exists($newResizeFileName) == false){
							//create thumb
						}
						*/
						$dbDocPathFull_w2 = $objSaveFile->getFilePath($dbDocPathFull, "w2");
						$dbDocPathFull_w = str_replace($idocDrawingDirName,$idocDrawingDirName.$thumbImage, $dbDocPathFull_w2);
						$newResizeFileName = str_replace($idocDrawingDirName,$idocDrawingDirName.$resizeImage, $dbDocPathFull);						
						$newResizeFileNameForDB = str_replace($idocDrawingDirName,$idocDrawingDirName.$resizeImage,$dbDocPath);	
						$newResizeFileNamePath = str_replace($idocDrawingDirName,$idocDrawingDirName.$resizeImage, $dbDocPathFull_w2);
						if(file_exists($newResizeFileName) == false){
							//create thumb
							$file_pointer_inc_resize=$objSaveFile->ptDir("idoc_drawing/scan_upload/resize","i");							
							$t = $objSaveFile->createThumbs($dbDocPathFull,$newResizeFileName,730,465);							
						}
						
						$id = "";
						$id = "divInner".$i;
						$border = "";
						
						$strTrFile .= "<div id=\"".$id."\" class=\"center-block prw_".$dbScanId." \"  >";
						if($dbDocType != "application/pdf"){
							if($strIframeFilePath == ""){
								$strIframeFilePath = $dbDocPathFull_w2;
								$strPathForCanvasIframe = $newResizeFileNamePath;
								$strDBDocPath = $newResizeFileNameForDB;
							}
							$strTrFile .= "<img src=\"$dbDocPathFull_w\" onClick=\"setIfamePath('$dbDocPathFull_w2','".$id."','$newResizeFileNamePath', '$newResizeFileNameForDB')\"  class=\"img-thumbnail\" \>";
						}
						$strTrFile .= "</div>";
						if($dbDocDate != "00-00-0000"){
							$strTrFile .= "<div id='dateDiv".$i."' class=\"center-block prw_".$dbScanId."\" >
											<span>".$dbDocDate."</span><span class=\"glyphicon glyphicon-remove\" onClick=\"deleteDoc('".$dbScanId."');\"></span>
									</div>
									";
						}						
					}
					$i++;	
				}
			}
		}
		
		//
		$zscanOrUpload = "Preview";
		include($GLOBALS['incdir']."/chart_notes/view/scan_upload_drawing.php");
	}

	function get_test_drawings(){
		$rqPatId = 0;
		$rqPatId = $this->pid;
		$rqCanvasId = $_REQUEST["canvasId"];
		$objSaveFile = new SaveFile($rqPatId); 
		
		$getSqlDateFormat= get_sql_date_format();
		$arrTestDesc = array();
		$qryGetTestImages = "select image_name as fileName, image_form as testName, file_path as fileP, DATE_FORMAT(doc_upload_date, '$getSqlDateFormat') as fileUPLDate, 
							DATE_FORMAT(rename_date, '$getSqlDateFormat') as reDate, test_id as performedTestId from ".constant("IMEDIC_SCAN_DB").".scans where patient_id = '".$rqPatId."' 
							and image_form IN('VF', 'VF-GL', 'NFA', 'OCT','OCT-RNFL', 'Pacchy', 'IVFA', 'Disc', 'discExternal', 'Topogrphy', 'CellCount', 'TestLabs', 'Ascan', 'BScan', 'TestOther') 
							and file_type != 'application/pdf' AND file_path != '' 
							ORDER BY testName";
		//echo 	$qryGetTestImages;				
		$rsGetTestImages = imw_query($qryGetTestImages);
		if($rsGetTestImages){
			if(imw_num_rows($rsGetTestImages) > 0){
				$imagePath = $objSaveFile->getUploadDirPath();// "../../main/uploaddir";
				$patientDir = "/PatientId_".$rqPatId."";				
				$idocDrawingDirName = "/idoc_drawing";				
				$resizeImage = "/resize";
				$hndl_resize=$objSaveFile->ptDir($idocDrawingDirName.$resizeImage);
				
				while($rowGetTestImages = imw_fetch_array($rsGetTestImages)){
					$dbFilePAbsolute = $dbFileP = $dbTestDate = $imgFilePath = $filePAbsoluteThumb = $fileNameDB = $newResizeFileName = 
					$newResizeFileNamePath = $newResizeFileNameForDB = "";
					$dbFilePAbsolute = $imagePath.$rowGetTestImages["fileP"];
					if(file_exists($dbFilePAbsolute) == true){
						if($rowGetTestImages["reDate"] != "" && $rowGetTestImages["reDate"] != "00-00-0000"){
							$dbTestDate = $rowGetTestImages["reDate"];
						}
						else{
							$dbTestDate = $rowGetTestImages["fileUPLDate"];
						}
						$fileNameDB = urldecode($rowGetTestImages["fileName"]);
						$pathToImages = "".$rowGetTestImages["fileP"];						
						$loadFile = $objSaveFile->getFilePath($dbFilePAbsolute, "w"); //"../../main/uploaddir".$rowGetTestImages["fileP"];						
						if(empty($fileNameDB) == true){
							$random = rand(1,20)."".rand(21,40);
							$fileNameDB = "/".$random."".time()."-".session_id().".png";
						}
						
						$newResizeFileName = $imagePath.$patientDir.$idocDrawingDirName.$resizeImage."/".$fileNameDB;
						$newResizeFileNameForDB = $patientDir.$idocDrawingDirName.$resizeImage."/".$fileNameDB;
						$newResizeFileNamePath = $objSaveFile->getFilePath($patientDir.$idocDrawingDirName.$resizeImage."/".$fileNameDB, "w");
						if(file_exists($newResizeFileName) == false){
							//$file_pointer_inc_resize=$objSaveFile->ptDir("idoc_drawing/scan_upload/resize","i");							
							$t = $objSaveFile->createThumbs($dbFilePAbsolute,$newResizeFileName,730,465);							
						}
						$imgFilePath = $objSaveFile->getFilePath($pathToImages, "w");
						$pathToImages = $objSaveFile->getFilePath($pathToImages, "i");
						$pathThumb = $objSaveFile->createThumbs($pathToImages, "", 100, 100);
						
						$tempImgWH = "";
						if(is_array($pathThumb) == true){
							$dbFileP = $objSaveFile->getFilePath($rowGetTestImages["fileP"], "w");
							$tempImgWH = "style=\"width:".$pathThumb["imgWidth"]."px; height:".$pathThumb["imgHeight"]."px; border: 2px #CCCCCC solid;\"";
							$filePAbsoluteThumb = $objSaveFile->getFilePath($rowGetTestImages["fileP"], "i");
						}else{
							$filePAbsoluteThumb = $pathThumb;
							$pathThumb = "".$pathThumb;
							$pathThumb = $objSaveFile->getFilePath($pathThumb, "w");
							$dbFileP = "".$pathThumb;
							$tempImgWH = "style=\"border: 2px #CCCCCC solid;\"";
						}
						
						$arrTestDesc[$rowGetTestImages["testName"]][] = array("testName" => $rowGetTestImages["testName"], 
																				"filePath" => $dbFileP, 
																				"testDate" => $dbTestDate, 
																				"imgFilePath" => $imgFilePath, 
																				"filePAbsoluteThumb" => $filePAbsoluteThumb, 
																				"performedTestId" => $rowGetTestImages["performedTestId"], 
																				"performedTestImagePath" => $newResizeFileNameForDB, 
																				"newResizeFileNamePath" => $newResizeFileNamePath,
																				"tempImgWH" => $tempImgWH																				
																			);
						
					
					}
				}				
			}
		}
		if(count($arrTestDesc) > 0){
			$strHTML = "";
			$arrFilePAbsolute = array();
			$arrTestName = array('VF','VF-GL', 'NFA', 'OCT','OCT-RNFL', 'Pacchy', 'IVFA', 'Disc', 'discExternal', 'Topogrphy', 'CellCount', 'TestLabs', 'Ascan', 'BScan', 'TestOther');
			asort($arrTestName);
			$strHTML="";
			foreach($arrTestName as $testKey => $testVal){
				$tmp="";
				foreach($arrTestDesc as $strKey => $arrVal){
					if($strKey == $testVal){
						$blIsFirst = true;
						$totAccsesItem = 1;
						foreach($arrVal as $intKey => $valArr){							
							$onClickFun = "";
							if($rqCanvasId != ""){
								$onClickFun = " onClick=\"loadTestImage('".$valArr["newResizeFileNamePath"]."', '".$strKey."', '".$valArr["performedTestId"]."', '".$valArr["performedTestImagePath"]."', '".$rqCanvasId."');\" ";
							}
							else{
								$onClickFun = " onClick=\"loadTestImage('".$valArr["newResizeFileNamePath"]."', '".$strKey."', '".$valArr["performedTestId"]."', '".$valArr["performedTestImagePath"]."');\" ";
							}
							
							$tmp .= "<div class=\"thumbnail\">
											<a ".$onClickFun." href=\"javascript:void(0);\">
												<img src='".$valArr["filePath"]."' ".$valArr["tempImgWH"]." alt=\"img".$valArr["testDate"]."\" />
												<div class=\"caption text-center\">
													<p>".$valArr["testDate"]."</p>
												</div>
											</a>
										</div>";
							
							$arrFilePAbsoluteThumb[] = $valArr["filePAbsoluteThumb"];							
							$totAccsesItem++;
						}
					}
				}
				
				//
				if(!empty($tmp)){					
					$strHTML .= "<div class=\"panel panel-default\">";	
					$strHTML .= "<div class=\"panel-heading\">".$testVal."</div>";
					$strHTML .= "<div class=\"panel-body\">".$tmp."</div>";
				}				
			}
			
			//
			$t=$strHTML;
			
			$strHTML ="
			<!-- Modal -->
			<div id=\"testImgModal\" class=\"modal fade\" role=\"dialog\">
				<div class=\"modal-dialog modal-sm\">
					<!-- Modal content-->
					<div class=\"modal-content\">
						<div class=\"modal-header\">
						<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
						<h4 class=\"modal-title\">Test Images</h4>
						</div>
						<div class=\"modal-body\">
						".$t."
						</div>
						<div class=\"modal-footer\">
						<button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Close</button>
						</div>
					</div>

				</div>
			</div>
			";
			
			/*
			$strHTML = "<div id=\"divImages\" style=\"border:2px solid #4684ab; height:530px;\" class=\"panel panel-default\">";
			$strHTML .= "<div class=\"panel-heading\">Test Images<span class=\"glyphicon glyphicon-remove pull-right\"></span></div>";
			$strHTML .= "<div class=\"panel-body\" style=\"height:500px; overflow:auto;\">".$t."</div>";
			$strHTML .= "</div>";
			*/
			echo $strHTML."~~".implode(":~:", $arrFilePAbsoluteThumb);		
		}
		else{
			echo "NOIMAGES";
		}
	}//
	
	function del_idoc_drawings(){
		$id = $_REQUEST["hidDrawingId"];
		$patient_id=$this->pid;			
		$form_id = $this->fid;
		if(!empty($id)&&!empty($patient_id)){
			$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing
						SET deletedby='".$_SESSION["authId"]."'
						where id = '".$id."'  AND patient_id='".$patient_id."' AND patient_form_id = '".$form_id."'  ";
			$row = sqlQuery($sql);
		}	
	}	
}

?>