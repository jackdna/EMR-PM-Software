<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: CLSDrawingData.php
Coded in PHP7
Purpose: This class provides functions for creating drawing images, saving in database etc.
Access Type : Include file
*/
?>
<?php
class CLSDrawingData{
	function getHTMLDrawingDispStatus(){
		$blEnableHTMLDrawing = false;	
		$strUserAgent = $_SERVER['HTTP_USER_AGENT'];
		if(stristr($strUserAgent, 'Safari') == true||stristr($strUserAgent, 'iPad') == true) {
			$blEnableHTMLDrawing = true;
		}else	if(stristr($_SERVER['HTTP_USER_AGENT'],"rv:11") !== false){
			$blEnableHTMLDrawing = true;
		}elseif(stristr($strUserAgent, 'MSIE') == true){
			$pos = strpos($strUserAgent, 'MSIE');
			(int)substr($strUserAgent,$pos + 5, 3);
			if((int)substr($strUserAgent,$pos + 5, 3) > 8){
				$blEnableHTMLDrawing = true;
			}
		}
		return $blEnableHTMLDrawing;
	}
	
	function rem_prac_pth($str){
		$tmp = "/".PRACTICE_PATH."/data/".PRACTICE_PATH;
		if(stripos($str, $tmp)!==false){ $str = str_ireplace($tmp,"",$str);  }
		else{
			$tmp = "/data/".PRACTICE_PATH;
			if(stripos($str, $tmp)!==false){ $str = str_ireplace($tmp,"",$str);  }
		}
		return $str;
	}
	
	function insertDrawingData($arrDrawingData){
		if($arrDrawingData["drawingFor"]=="FundusExam"){ $arrDrawingData["drawingFor"]="Fundus_Exam"; }
		//pre($arrDrawingData, 1);		
		$intNewDrawingId = 0;
		$r = $g = $b = $a = $canvasBackImage = $rqHidDrawingTestImageP = "";
		$imagePath = $arrDrawingData["imagePath"];			
		$r = $arrDrawingData["hidRedPixel"];
		$g = $arrDrawingData["hidGreenPixel"];
		$b = $arrDrawingData["hidBluePixel"];
		$a = $arrDrawingData["hidAlphaPixel"];
		$canvasBackImage = $arrDrawingData["hidImageCss"];
		$rqHidDrawingTestImageP = $arrDrawingData['hidDrawingTestImageP'];
		$rqHidDrawingTestImageP = $this->rem_prac_pth($rqHidDrawingTestImageP);		
		$patientDir = "/PatientId_".$arrDrawingData["patId"]."";			
		$idocDrawingDirName = "/idoc_drawing";
		$idocDrawingCanvasDirName = "/canvas_drawing_image";
		if(is_dir($imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName) == false){
			mkdir($imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName, 0777, true);
		}
		
		if(!empty($arrDrawingData['hidCanvasImgData'])||!empty($rqHidDrawingTestImageP)){
		$canvasImgData = str_replace("data:image/png;base64,","",$arrDrawingData["hidCanvasImgData"]);
		
		$drawingFileName = $arrDrawingData["drawingFileName"];		
		$drawingFilePath = $imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName.$drawingFileName;
		
		//Thumbimg
		$drawingFileName_s = str_replace(".png","_s.png",$drawingFileName);
		$drawingFilePath_s = $imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName.$drawingFileName_s;		
		
		file_put_contents($drawingFilePath, base64_decode($canvasImgData));		
		
		if((empty($canvasBackImage) == false) && ($canvasBackImage != "imgDB")){
			switch($canvasBackImage){
				case "imgFaceCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/face.jpg";
				break;
				case "imgOpticalCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/optical.jpg";
				break;
				case "imgLaCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/la.jpg";
				break;
				case "imgOnMaCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/bg_on_ma.jpg";
				break;
				case "imgOphthaCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/ophtha.jpg";
				break;
				case "imgPicConCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/pic-con.jpg";
				break;
				case "imgGonioCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/gonio.jpg";
				break;
				case "imgEOMCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/eom.jpg";
				break;
				case "imgCorneaCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/cornea.jpg";
				break;
				case "imgCorneaEyeCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/cornea_eye.jpg";
				break;
				case "imgEOM2Canvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/eom_2.jpg";
				break;
				case "imgLidsAndLacrimalCanvas":
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/lids_and_lacrimal.jpg";
				break;
			}
		}
		elseif(empty($rqHidDrawingTestImageP) == false){
			$backImg = $imagePath.$rqHidDrawingTestImageP;
		}
		
		if(empty($backImg)){
			$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/white.png";
		}
		
		//*
		if(empty($backImg) == false){
			//$bakImgResource = imagecreatefromstring(file_get_contents($backImg));
			$bakImgResource = (strpos($backImg,".png")!==false) ? imagecreatefrompng($backImg): imagecreatefromjpeg($backImg); 
			$canvasImgResource = imagecreatefrompng($drawingFilePath);										
			imagecopy($bakImgResource, $canvasImgResource, 0, 0, 0, 0, 730, 465);
			//imagepng($bakImgResource, $drawingFilePath);   
			imagepng($bakImgResource, $drawingFilePath_s);
			$this->resizeDrawing($arrDrawingData["patId"], $drawingFilePath_s);
		}
		//*/
			//
			$drawing_image_path = $patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName.$drawingFileName;
			
		}else{
			
			$drawing_image_path = "";
		
		}//
		
		//Clean Data
		$arrDrawingData["hidImagesData"] = $this->cleanTxt($arrDrawingData["hidImagesData"]);
		
		$row_visit_dos = $this->getVisitDOS($arrDrawingData["formId"]);
		
		$drawingImageData = "";
		$qryInsertDrawing = "insert into ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
						(".
						//"red_pixel, green_pixel, blue_pixel, alpha_pixel, ".
						" toll_image, drawing_for, drawing_for_master_id, row_created_by, row_created_date_time, patient_id, 
						patient_form_id, patient_test_name, patient_test_id, patient_test_image_path, drawing_image_path, drawing_images_data_points,
						row_visit_dos, drwNE, 
						drw_data_json)
						 values (".
						 //"'".$r."', '".$g."', '".$b."', '".$a."', ".
						 " '".$canvasBackImage."', '".$arrDrawingData["drawingFor"]."', '".$arrDrawingData["drawingForMasterId"]."', 
						 '".$_SESSION["authId"]."', '".wv_dt('now')."', '".$arrDrawingData["patId"]."', '".$arrDrawingData["formId"]."', '".$arrDrawingData["hidDrawingTestName"]."', 
						 '".$arrDrawingData["hidDrawingTestId"]."', '".$rqHidDrawingTestImageP."', 
						 '".$drawing_image_path."', 
						 '".addslashes($arrDrawingData["hidImagesData"])."',
						 '".$row_visit_dos."', '".$arrDrawingData["drwNE"]."', 
						 '".sqlEscStr($arrDrawingData["hidDrwDataJson"])."')";
		$intNewDrawingId = sqlInsert($qryInsertDrawing);
		if($intNewDrawingId > 0){
			//$intNewDrawingId = mysql_insert_id();
			if($intNewDrawingId > 0){
				$strSelAppletField = "";
				$strAppletData = "";
				/*switch ($arrDrawingData["examMasterTable"]):
					case "chart_eom":
						$strSelAppletField = "eomDrawing_2";
						break;
					case "chart_la":
						$strSelAppletField = "la_drawing";
						break;
					case "chart_gonio":
						$strSelAppletField = "gonio_od_drawing";
						break;
					case "chart_slit_lamp_exam":
						$strSelAppletField = "conjunctiva_od_drawing";
						break;
					case "chart_rv":
						$strSelAppletField = "od_drawing";
						break;
				endswitch;
				
				$blAppletHaveData = false;
				if(empty($strSelAppletField) == false){
					$selAppletData = "select ".$strSelAppletField." from ".$arrDrawingData["examMasterTable"]." where ".$arrDrawingData["examMasterTablePriColumn"]." = '".$arrDrawingData["drawingForMasterId"]."'";
					$rsAppletData = mysql_query($selAppletData);
					if($rsAppletData){
						if(mysql_num_rows($rsAppletData) > 0){
							$rowAppletData = mysql_fetch_row($rsAppletData);
							if(empty($rowAppletData[0]) == false){
								$blAppletHaveData = true;
							}
						}
						mysql_free_result($rsAppletData);
					}
				}
				
				if($blAppletHaveData == true){
					if (file_exists($imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName.$drawingFileName)) {																								
						$data = base64_decode($canvasImgData);
						$image = imagecreatefromstring($data);
						$contents = "";
						ob_start();
						imagejpeg($image);
						$contents = ob_get_contents();
						ob_end_clean();							
						$drawingImageData = addslashes(base64_encode($contents));							
						imagedestroy($image);
					}
					switch ($arrDrawingData["examMasterTable"]):
						case "chart_eom":
							$strAppletData = " ,eomDrawing_2 = '$drawingImageData' ";
							break;
						case "chart_la":
							$strAppletData = " ,la_drawing = '$drawingImageData' ";
							break;
						case "chart_gonio":
							$strAppletData = " ,gonio_od_drawing = '$drawingImageData' ";
							break;
						case "chart_slit_lamp_exam":
							$strAppletData = " ,conjunctiva_od_drawing = '$drawingImageData' ";
							break;
						case "chart_rv":
							$strAppletData = " ,od_drawing = '$drawingImageData' ";
							break;
					endswitch;
				}*/
				

				//$qryUpdateSLETab = "update ".$arrDrawingData["examMasterTable"]." set idoc_drawing_id = '".$intNewDrawingId."' ".$strAppletData." where ".$arrDrawingData["examMasterTablePriColumn"]." = '".$arrDrawingData["drawingForMasterId"]."'";
				//$rsUpdateSLETab = mysql_query($qryUpdateSLETab);
				//sleep(2);
			}
		}
	}
	
	function isDrawingB2CurVisit($hidDrawingId, $formId){ //isDrawingBelong2PrevVisit
		$ret = 0;
		if(!empty($hidDrawingId) && !empty($formId)){
			$sql1 = "select id, drawing_image_path as drawingImgPath from ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
					where id = '".$hidDrawingId."'  AND patient_form_id='".$formId."' ";
			$res = sqlStatement($sql1);
			if(imw_num_rows($res) > 0){
				$ret = 1;
			}
		}
		return $ret;
	}
	
	
	function updateDrawingData($arrDrawingData){
		if($arrDrawingData["drawingFor"]=="FundusExam"){ $arrDrawingData["drawingFor"]="Fundus_Exam"; }
		//pre($arrDrawingData, 1);exit();	
		//Dos
		$row_visit_dos = $this->getVisitDOS($arrDrawingData["formId"]);
		
		$r = $g = $b = $a = $canvasBackImage = $backImg = $rqHidDrawingTestImageP = $dbDrawingImgPath = "";
		$qryChkHTMLDrawing = "select id, drawing_image_path as drawingImgPath from ".constant("IMEDIC_SCAN_DB").".idoc_drawing where id = '".$arrDrawingData["hidDrawingId"]."' AND  patient_form_id = '".$arrDrawingData["formId"]."' ";
		$rsChkHTMLDrawing = sqlStatement($qryChkHTMLDrawing);
		$flg_update_record=0;
		if(imw_num_rows($rsChkHTMLDrawing) > 0){
		//if($rowChkHTMLDrawing!=false){
			$rowChkHTMLDrawing = sqlFetchArray($rsChkHTMLDrawing);
			$dbDrawingImgPath = $rowChkHTMLDrawing["drawingImgPath"];
			$flg_update_record=1;
			/*if(empty($arrDrawingData["hidDrawingId"])){
				$arrDrawingData["hidDrawingId"] = $rowChkHTMLDrawing["id"];
			}*/
		}
		
		$imagePath = $arrDrawingData["imagePath"];
		$r = $arrDrawingData["hidRedPixel"];
		$g = $arrDrawingData["hidGreenPixel"];
		$b = $arrDrawingData["hidBluePixel"];
		$a = $arrDrawingData["hidAlphaPixel"];
		$rqHidDrawingTestImageP = $arrDrawingData['hidDrawingTestImageP'];		
		$rqHidDrawingTestImageP = $this->rem_prac_pth($rqHidDrawingTestImageP);
		$canvasBackImage = $arrDrawingData["hidImageCss"];
		$patientDir = "/PatientId_".$arrDrawingData["patId"]."";
		$idocDrawingDirName = "/idoc_drawing";
		$idocDrawingCanvasDirName = "/canvas_drawing_image";
		if(is_dir($imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName) == false){
			mkdir($imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName, 0777, true);
		}
		
		if($flg_update_record==1){//work in case of update only : Block4 update
		
		if((isset($arrDrawingData["hidImgDataFileName"]) == true) && (empty($arrDrawingData["hidImgDataFileName"]) == false)){
			unlink($imagePath.$arrDrawingData["hidImgDataFileName"]);
		}
		
		
		//Delete ---
		if($r=="DELETE" && !empty($arrDrawingData["hidDrawingId"])){
			/*$sql = "DELETE FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing
					where id = '".$arrDrawingData["hidDrawingId"]."'";
			*/
			$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing
						SET deletedby='".$_SESSION["authId"]."'
						where id = '".$arrDrawingData["hidDrawingId"]."'  AND patient_id='".$arrDrawingData["patId"]."' ";
			$row = sqlQuery($sql);
			return;
		}
		//Delete ---
		
		if(empty($dbDrawingImgPath) == false){
			if(file_exists($imagePath.$dbDrawingImgPath) == true){
				$arrFilePathInfo = pathinfo($imagePath.$dbDrawingImgPath);
				unlink($imagePath.$dbDrawingImgPath);
				
				//thumb
				$dbDrawingImgPath_s=str_replace(".png", "_s.png", $dbDrawingImgPath);
				if(file_exists($imagePath.$dbDrawingImgPath_s)){unlink($imagePath.$dbDrawingImgPath_s);}
				$dbDrawingImgPath_b=str_replace(".png", "_b.png", $dbDrawingImgPath);
				if(file_exists($imagePath.$dbDrawingImgPath_b)){unlink($imagePath.$dbDrawingImgPath_b);}
				//--
				
				if(file_exists($arrFilePathInfo["dirname"]."/".$arrFilePathInfo["filename"].".jpg") == true){
					unlink($arrFilePathInfo["dirname"]."/".$arrFilePathInfo["filename"].".jpg");
				}
			}
		}
		
		}//End :Block4 update
		
		if(!empty($arrDrawingData['hidCanvasImgData'])||!empty($rqHidDrawingTestImageP)){
			$canvasImgData = str_replace("data:image/png;base64,","",$arrDrawingData['hidCanvasImgData']);
			
			$drawingFileName = $arrDrawingData["drawingFileName"];
			$drawingFilePath = $imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName.$drawingFileName;

			//Thumbimg
			$drawingFileName_s = str_replace(".png","_s.png",$drawingFileName);
			$drawingFilePath_s = $imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName.$drawingFileName_s;
			
			
			$chkOp_tmp = file_put_contents($drawingFilePath, base64_decode($canvasImgData));
			/*
			if($chkOp_tmp === false){
				echo "File Op Failed<br>".$drawingFilePath."<br>".$canvasImgData."<br>".base64_decode($canvasImgData);				
			}else{
				echo "File Saved<br>".$drawingFilePath."<br>".$canvasImgData."<br>".base64_decode($canvasImgData);				
			}
			exit();
			*/
			if((empty($canvasBackImage) == false) && ($canvasBackImage != "imgDB")){
				switch($canvasBackImage){
					case "imgFaceCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/face.jpg";
					break;
					case "imgOpticalCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/optical.jpg";
					break;
					case "imgLaCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/la.jpg";
					break;
					case "imgOnMaCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/bg_on_ma.jpg";
					break;
					case "imgOphthaCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/ophtha.jpg";
					break;
					case "imgPicConCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/pic-con.jpg";
					break;
					case "imgGonioCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/gonio.jpg";
					break;
					case "imgEOMCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/eom.jpg";
					break;
					case "imgCorneaCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/cornea.jpg";
					break;
					case "imgCorneaEyeCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/cornea_eye.jpg";
					break;
					case "imgEOM2Canvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/eom_2.jpg";
					break;
					case "imgLidsAndLacrimalCanvas":
						$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/lids_and_lacrimal.jpg";
					break;
				}
			}
			elseif(empty($rqHidDrawingTestImageP) == false){
				$backImg = $imagePath.$rqHidDrawingTestImageP;
			}
			
			if(empty($backImg)){
				$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/white.png";
			}
			
			//*
			if(empty($backImg) == false){
				
				//$bakImgResource = imagecreatefromstring(file_get_contents($backImg));
				$bakImgResource = (strpos($backImg,".png")!==false) ? imagecreatefrompng($backImg): imagecreatefromjpeg($backImg); 
				$canvasImgResource = imagecreatefrompng($drawingFilePath);										
				imagecopy($bakImgResource, $canvasImgResource, 0, 0, 0, 0, 730, 465);
				//imagepng($bakImgResource, $drawingFilePath);   			
				imagepng($bakImgResource, $drawingFilePath_s);   			
				$this->resizeDrawing($arrDrawingData["patId"],$drawingFilePath_s);
			}
			//*/
			
			//
			$drawing_image_path = $patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName.$drawingFileName;
			
		}else{
			
			$drawing_image_path = "";
		
		}//
		
		//
		//WNL, NC logic should apply to all drawings not just Fundus.  Basically all drawings functionality should be like Fundus.
		//
		//Check Fundus for date change
		//if($arrDrawingData["drawingFor"] == "Fundus_Exam"){
			$phaseDtChange = " row_created_by=IF(st_carryfwd='1', '".$_SESSION["authId"]."', row_created_by), 
						   row_created_date_time=IF(st_carryfwd='1', '".wv_dt('now')."', row_created_date_time),
						   row_visit_dos=IF(st_carryfwd='1', '".$row_visit_dos."', row_visit_dos), ";
		//}else{
		//	$phaseDtChange = "";
		//}
		
		//do not set modify data
		if($arrDrawingData["flgDoNotSetModify"] == "1"){
			$phaseModifydata = "";
		}else{			
			$phaseModifydata = " row_modify_by = '".$_SESSION["authId"]."', row_modify_date_time = '".wv_dt('now')."', 	";
		}		
		
		//Clean Data
		$arrDrawingData["hidImagesData"] = $this->cleanTxt($arrDrawingData["hidImagesData"]);
		
		//if($rsChkHTMLDrawing){
			if(imw_num_rows($rsChkHTMLDrawing) > 0){
			
				//do not update  in case of DrawPane
				//drawing_for
				//drawing_for_master_id 
				//drawing_for = '".$arrDrawingData["drawingFor"]."', 
				//drawing_for_master_id = '".$arrDrawingData["drawingForMasterId"]."',
				$phraseForMasterId="";
				if($arrDrawingData["drawingFor"] != "DrawPane"){
					$phraseForMasterId = ", drawing_for = '".$arrDrawingData["drawingFor"]."', drawing_for_master_id = '".$arrDrawingData["drawingForMasterId"]."'   ";
				}
			
				$qryInsertDrawing = "update ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
								set ".
								//" red_pixel = '".$r."', green_pixel = '".$g."', blue_pixel = '".$b."', alpha_pixel = '".$a."', ".
							 " toll_image = '".$canvasBackImage."', 
							 ".$phaseModifydata."
							 patient_test_name = '".$arrDrawingData['hidDrawingTestName']."', patient_test_id = '".$arrDrawingData['hidDrawingTestId']."', 
							 patient_test_image_path = '".$rqHidDrawingTestImageP."', 
							 drawing_image_path = '".$drawing_image_path."',
							 drawing_images_data_points = '".addslashes($arrDrawingData['hidImagesData'])."',
							 ".$phaseDtChange."
							 st_carryfwd='0', row_visit_dos='".$row_visit_dos."',
							 drwNE='".$arrDrawingData["drwNE"]."',
							 drw_data_json='".sqlEscStr($arrDrawingData["hidDrwDataJson"])."'
							 ".$phraseForMasterId."						 
							 where id = '".$arrDrawingData["hidDrawingId"]."'";				
				$rsInsertDrawing = sqlQuery($qryInsertDrawing);
				$drawingId=$arrDrawingData["hidDrawingId"];
				
				if($rsInsertDrawing){
					$intNewDrawingId = $arrDrawingData["hidDrawingId"];
					if($intNewDrawingId > 0){
						$strSelAppletField = "";
						$strAppletData = "";
						
						//---
						///*
						if($arrDrawingData["attach2Exam"] == "1" && $arrDrawingData["drawingFor"] != "DrawPane"){						
							
							$this->updateChartExamStatus($arrDrawingData);
							
						}
						//*/
						//----	
						
						
						/*switch ($arrDrawingData["examMasterTable"]):
							case "chart_eom":
								$strSelAppletField = "eomDrawing_2";
								break;
							case "chart_la":
								$strSelAppletField = "la_drawing";
								break;
							case "chart_gonio":
								$strSelAppletField = "gonio_od_drawing";
								break;
							case "chart_slit_lamp_exam":
								$strSelAppletField = "conjunctiva_od_drawing";
								break;
							case "chart_rv":
								$strSelAppletField = "od_drawing";
								break;
						endswitch;
				
						$blAppletHaveData = false;
						if(empty($strSelAppletField) == false){
							$selAppletData = "select ".$strSelAppletField." from ".$arrDrawingData["examMasterTable"]." where ".$arrDrawingData["examMasterTablePriColumn"]." = '".$arrDrawingData["drawingForMasterId"]."'";
							$rsAppletData = mysql_query($selAppletData);
							if($rsAppletData){
								if(mysql_num_rows($rsAppletData) > 0){
									$rowAppletData = mysql_fetch_row($rsAppletData);
									if(empty($rowAppletData[0]) == false){
										$blAppletHaveData = true;
									}
								}
								mysql_free_result($rsAppletData);
							}
						}						
						
						if($blAppletHaveData == true){							
							if (file_exists($imagePath.$patientDir.$idocDrawingDirName.$idocDrawingCanvasDirName.$drawingFileName)) {																								
								$image = imagecreatefromstring(file_get_contents($drawingFilePath));
								//$data = base64_decode($canvasImgData);
								//$image = imagecreatefromstring($data);
								$contents = "";
								ob_start();
								imagepng($image);
								$contents = ob_get_contents();
								ob_end_clean();							
								$drawingImageData = base64_encode($contents);							
								imagedestroy($image);
							}
						
							switch ($arrDrawingData["examMasterTable"]):
								case "chart_eom":
									$strAppletData = " ,eomDrawing_2 = '$drawingImageData' ";
									break;
								case "chart_la":
									$strAppletData = " ,la_drawing = '$drawingImageData' ";
									break;
								case "chart_gonio":
									$strAppletData = " ,gonio_od_drawing = '$drawingImageData' ";
									break;
								case "chart_slit_lamp_exam":
									$strAppletData = " ,conjunctiva_od_drawing = '$drawingImageData' ";
									break;
								case "chart_rv":
									$strAppletData = " ,od_drawing = '$drawingImageData' ";
									break;
							endswitch;
						}*/
						/*$strIDocId = "";
						$qryGetIDocIdInMasetr = "select idoc_drawing_id from ".$arrDrawingData["examMasterTable"]." where ".$arrDrawingData["examMasterTablePriColumn"]." = '".$arrDrawingData["drawingForMasterId"]."'";
						$rsGetIDocIdInMasetr = mysql_query($qryGetIDocIdInMasetr);
						if(mysql_num_rows($rsGetIDocIdInMasetr) > 0){
							$rowGetIDocIdInMasetr = mysql_fetch_row($rsGetIDocIdInMasetr);
							$strIDocId = "";
							$strIDocId = $rowGetIDocIdInMasetr[0];
						}
						$newIDocId = "";
						if($strIDocId == "0"){
							$newIDocId = $intNewDrawingId;
						}
						else{
							$newIDocId = $strIDocId.",".$intNewDrawingId;
						}
						
						$qryUpdateIopGonioTab = "update ".$arrDrawingData["examMasterTable"]." set idoc_drawing_id = '".$newIDocId."' ".$strAppletData." where ".$arrDrawingData["examMasterTablePriColumn"]." = '".$arrDrawingData["drawingForMasterId"]."'";
						$rsUpdateIopGonioTab = mysql_query($qryUpdateIopGonioTab);
						//sleep(5);*/
						
					}
				}
			}
			elseif(imw_num_rows($rsChkHTMLDrawing) == 0){
				$qryInsertDrawing = "insert into ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
						( ".
						//"red_pixel, green_pixel, blue_pixel, alpha_pixel, ".
						"toll_image, drawing_for, drawing_for_master_id, row_created_by, row_created_date_time, patient_id, 
						patient_form_id, patient_test_name, patient_test_id, patient_test_image_path, drawing_image_path, drawing_images_data_points,
						row_visit_dos, drwNE, drw_data_json)
						 values ( ".
						 //"'".$r."', '".$g."', '".$b."', '".$a."', ".
						 "'".$canvasBackImage."', '".$arrDrawingData["drawingFor"]."', '".$arrDrawingData["drawingForMasterId"]."', 
						 '".$_SESSION["authId"]."', '".wv_dt('now')."', '".$arrDrawingData["patId"]."', 
						 '".$arrDrawingData["formId"]."', '".$arrDrawingData["hidDrawingTestName"]."', '".$arrDrawingData["hidDrawingTestId"]."', 
						 '".$rqHidDrawingTestImageP."', 
						 '".$drawing_image_path."', 
						 '".addslashes($arrDrawingData["hidImagesData"])."', '".$row_visit_dos."', '".$arrDrawingData["drwNE"]."', 
						 '".sqlEscStr($arrDrawingData["hidDrwDataJson"])."' )";
				$intNewDrawingId = sqlInsert($qryInsertDrawing);
				//if($rsInsertDrawing){
				if($intNewDrawingId>0){
					//$intNewDrawingId = mysql_insert_id();
					$drawingId=$intNewDrawingId;
					
					if($intNewDrawingId > 0){
					
						//---
						///*
						if($arrDrawingData["attach2Exam"] == "1" && $arrDrawingData["drawingFor"] == "DrawPane"){							
						
							$arrDrawingData["insertId"] = $intNewDrawingId;
							$arrDrawingData["canvasBackImage"] = $canvasBackImage;
							
							$this->attach2PtExam($arrDrawingData);
							
						}
						
						//*/
						//----				
					
					
						/*$strIDocId = "";
						$qryGetIDocIdInMasetr = "select idoc_drawing_id from ".$arrDrawingData["examMasterTable"]." where ".$arrDrawingData["examMasterTablePriColumn"]." = '".$arrDrawingData["drawingForMasterId"]."'";
						$rsGetIDocIdInMasetr = mysql_query($qryGetIDocIdInMasetr);
						if(mysql_num_rows($rsGetIDocIdInMasetr) > 0){
							$rowGetIDocIdInMasetr = mysql_fetch_row($rsGetIDocIdInMasetr);
							$strIDocId = "";
							$strIDocId = $rowGetIDocIdInMasetr[0];
						}
						$newIDocId = "";
						if($strIDocId == "0"){
							$newIDocId = $intNewDrawingId;
						}
						else{
							$newIDocId = $strIDocId.",".$intNewDrawingId;
						}
						
						$qryUpdateIopGonioTab = "update ".$arrDrawingData["examMasterTable"]." set idoc_drawing_id = '".$newIDocId."' where ".$arrDrawingData["examMasterTablePriColumn"]." = '".$arrDrawingData["drawingForMasterId"]."'";
						$rsUpdateIopGonioTab = mysql_query($qryUpdateIopGonioTab);
						//sleep(5);*/
					}
				}		
			}
		//}
		return $drawingId;	
	}
	
	
	function updateChartExamStatus($arr){
		
		$tmp_drawingFor=$arr["drawingFor"];
		$tmp_drawingForMasterId=$arr["drawingForMasterId"];
		$tmp_formId = $arr["formId"];
		$tmp_ptId = $arr["patId"];
		
		if(!empty($tmp_formId) && !empty($tmp_drawingFor) && !empty($tmp_ptId) && !empty($tmp_drawingForMasterId)){
			if($tmp_drawingFor == "EOM"){
				
				$sql = "select eom_id,statusElem FROM chart_eom WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' ";
				$row=sqlQuery($sql);
				//exit($sql);
				if($row != false){
					$id = $row["eom_id"];					
					$statusElem = $row["statusElem"];					
					
					if(!empty($statusElem)){
						if(strpos($statusElem, "3=1")===false){
							$statusElem = str_replace(array("elem_chng_divEom3=0",",,",", ,"),"", $statusElem);						
						}				
					}
					if(!empty($statusElem)){ $statusElem.=",";  }
					
					$statusElem .= "elem_chng_divEom3=1";
					$statusElem = str_replace(array(",,",", ,"),"", $statusElem);//rem comma
					
					//attached to la
					$sql = "UPDATE chart_eom SET statusElem='".sqlEscStr($statusElem)."'  WHERE eom_id='".$id."'  ";
					$row=sqlQuery($sql);					
				}
				
			}else if($tmp_drawingFor == "EXTERNAL"){
				$sql = "select ee_id,statusElem FROM chart_external_exam WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' ";
				$row=sqlQuery($sql);
				//exit($sql);
				if($row != false){
					$id = $row["ee_id"];					
					$statusElem = $row["statusElem"];					
					
					if(!empty($statusElem)){
						if(strpos($statusElem, "Draw_Od=1")===false){//elem_chng_divDraw_Od=1,elem_chng_divDraw_Os=1
							$statusElem = str_replace(array("elem_chng_divDraw_Od=0",",,",", ,"),"", $statusElem);						
						}
						if(strpos($statusElem, "Draw_Os=1")===false){//elem_chng_divDraw_Od=1,elem_chng_divDraw_Os=1
							$statusElem = str_replace(array("elem_chng_divDraw_Os=0",",,",", ,"),"", $statusElem);						
						}						
					}
					if(!empty($statusElem)){ $statusElem.=",";  }
					
					$statusElem .= "elem_chng_divDraw_Od=1,elem_chng_divDraw_Os=1";
					$statusElem = str_replace(array(",,",", ,"),"", $statusElem);//rem comma
					
					//attached to la
					$sql = "UPDATE chart_external_exam SET statusElem='".sqlEscStr($statusElem)."'  WHERE ee_id='".$id."'  ";
					$row=sqlQuery($sql);					
				}
				
			}else if($tmp_drawingFor == "LA"){
				$sql = "select id as la_id, statusElem FROM chart_drawings WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' AND exam_name='LA' ";
				$row=sqlQuery($sql);
				//exit($sql);
				if($row != false){
					$id = $row["la_id"];					
					$statusElem = $row["statusElem"];				
					
					if(!empty($statusElem)){
						if(strpos($statusElem, "5_Od=1")===false){
							$statusElem = str_replace(array("elem_chng_div5_Od=0",",,",", ,"),"", $statusElem);						
						}
						if(strpos($statusElem, "5_Os=1")===false){
							$statusElem = str_replace(array("elem_chng_div5_Os=0",",,",", ,"),"", $statusElem);						
						}					
					}
					if(!empty($statusElem)){ $statusElem.=",";  }
					
					$statusElem .= "elem_chng_div5_Os=1";
					$statusElem .= ",elem_chng_div5_Od=1";
					$statusElem = str_replace(array(",,",", ,"),"", $statusElem);
					
					//attached to la
					$sql = "UPDATE chart_drawings SET  statusElem='".sqlEscStr($statusElem)."'  WHERE id='".$id."'  ";
					$row=sqlQuery($sql);					
				}
			
			}else if($tmp_drawingFor == "IOP_GONIO"){
				$sql = "select gonio_id, statusElem FROM chart_gonio WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' ";
				$row=sqlQuery($sql);
				//exit($sql);
				if($row != false){
					$id = $row["gonio_id"];					
					$statusElem = $row["statusElem"];				
					
					if(!empty($statusElem)){	//elem_chng_divIop3_Od=1,elem_chng_divIop3_Os=1,
						if(strpos($statusElem, "3_Od=1")===false){
							$statusElem = str_replace(array("elem_chng_divIop3_Od=0",",,",", ,"),"", $statusElem);						
						}					
						if(strpos($statusElem, "3_Os=1")===false){
							$statusElem = str_replace(array("elem_chng_divIop3_Os=0",",,",", ,"),"", $statusElem);
						}					
					}
					if(!empty($statusElem)){ $statusElem.=",";  }
					
					$statusElem .= "elem_chng_divIop3_Od=1,elem_chng_divIop3_Os=1";
					$statusElem = str_replace(array(",,",", ,"),"", $statusElem);
					
					//attached to la
					$sql = "UPDATE chart_gonio SET statusElem='".sqlEscStr($statusElem)."'  WHERE gonio_id='".$id."'  ";
					$row=sqlQuery($sql);					
					
				}
			
			}else if($tmp_drawingFor == "SLE"){
				$sql = "select id as sle_id, statusElem FROM chart_drawings WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' AND exam_name='SLE' ";
				$row=sqlQuery($sql);
				//exit($sql);
				if($row != false){
					$id = $row["sle_id"];					
					$statusElem = $row["statusElem"];
					
					if(!empty($statusElem)){	//elem_chng_div3_Od=1,elem_chng_div3_Os=1,
						if(strpos($statusElem, "6_Od=1")===false){
							$statusElem = str_replace(array("elem_chng_div6_Od=0",",,",", ,"),"", $statusElem);						
						}					
						if(strpos($statusElem, "6_Os=1")===false){
							$statusElem = str_replace(array("elem_chng_div6_Os=0",",,",", ,"),"", $statusElem);
						}					
					}
					if(!empty($statusElem)){ $statusElem.=",";  }
					
					$statusElem .= "elem_chng_div6_Od=1,elem_chng_div6_Os=1";
					$statusElem = str_replace(array(",,",", ,"),"", $statusElem);
					
					//attached to la
					$sql = "UPDATE chart_drawings SET  statusElem='".sqlEscStr($statusElem)."'  WHERE id='".$id."'  ";
					$row=sqlQuery($sql);				
					
				}
			}else if($tmp_drawingFor == "Fundus_Exam"){			
				$sql = "select id as rv_id, statusElem FROM chart_drawings WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' AND exam_name='FundusExam' ";
				$row=sqlQuery($sql);
				//exit($sql);
				if($row != false){
					$id = $row["rv_id"];					
					$statusElem = $row["statusElem"];					
					
					if(!empty($statusElem)){	//elem_chng_div5_Od=1,elem_chng_div5_Os=1,
						if(strpos($statusElem, "5_Od=1")===false){
							$statusElem = str_replace(array("elem_chng_div5_Od=0",",,",", ,"),"", $statusElem);						
						}					
						if(strpos($statusElem, "5_Os=1")===false){
							$statusElem = str_replace(array("elem_chng_div5_Os=0",",,",", ,"),"", $statusElem);
						}					
					}
					if(!empty($statusElem)){ $statusElem.=",";  }
					
					$statusElem .= "elem_chng_div5_Od=1,elem_chng_div5_Os=1";
					$statusElem = str_replace(array(",,",", ,"),"", $statusElem);
					
					//attached to la
					$sql = "UPDATE chart_drawings SET statusElem='".sqlEscStr($statusElem)."'  WHERE id='".$id."'  ";
					$row=sqlQuery($sql);					
					
				}
				
			}		
		}		
	}
	
	
	function attach2PtExam($arr){
		
		
		if(empty($arr["insertId"])){  return ""; }
		
		//
		if($arr["canvasBackImage"] == "imgLidsAndLacrimalCanvas"){
			//
			
			//obj
			$oLA = new LA($arr["patId"],$arr["formId"]);
			if(!$oLA->isRecordExists()){	$oLA->carryForward();	}
			$oLA->attachDraw2PtExam($arr["insertId"]);
		
		
		
		}else if($arr["canvasBackImage"] == "imgEOMCanvas"){
			
			$oEOM= new EOM($arr["patId"],$arr["formId"]);				
			if(!$oEOM->isRecordExists()){	$oEOM->carryForward();	}
		
			$sql = "select * FROM chart_eom WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' ";
			$row=sqlQuery($sql);
			//exit($sql);
			if($row != false){
				$id = $row["eom_id"];
				$idoc_drawing_id = $row["idoc_drawing_id"];
				$statusElem = $row["statusElem"];
				
				if(!empty($idoc_drawing_id)){ $idoc_drawing_id .= ","; }
				$idoc_drawing_id .= $arr["insertId"];
				
				if(!empty($statusElem)){
					if(strpos($statusElem, "3=1")===false){
						$statusElem = str_replace(array("elem_chng_divEom3=0",",,",", ,"),"", $statusElem);						
					}				
				}
				if(!empty($statusElem)){ $statusElem.=",";  }
				
				$statusElem .= "elem_chng_divEom3=1";
				
				//attached to la
				$sql = "UPDATE chart_eom SET idoc_drawing_id = '".$idoc_drawing_id."', statusElem='".sqlEscStr($statusElem)."'  WHERE eom_id='".$id."'  ";
				$row=sqlQuery($sql);
				
				//update idocdrawing
				$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
						SET drawing_for='EOM', drawing_for_master_id='".$id."'
						WHERE id = '".$arr["insertId"]."' ";
				$row=sqlQuery($sql);				
			}			
		
		}else if($arr["canvasBackImage"] == "imgPicConCanvas"){
			
			$oSLE = new SLE($arr["patId"],$arr["formId"]);
			if(!$oSLE->isRecordExists()){	$oSLE->carryForward();	}
			
			$sql = "select * FROM chart_drawings WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' AND exam_name='SLE' ";
			$row=sqlQuery($sql);
			//exit($sql);
			if($row != false){
				$id = $row["id"];
				$idoc_drawing_id = $row["idoc_drawing_id"];
				$statusElem = $row["statusElem"];
				
				if(!empty($idoc_drawing_id)){ $idoc_drawing_id .= ","; }
				$idoc_drawing_id .= $arr["insertId"];
				
				if(!empty($statusElem)){	//elem_chng_div3_Od=1,elem_chng_div3_Os=1,
					if(strpos($statusElem, "6_Od=1")===false){
						$statusElem = str_replace(array("elem_chng_div6_Od=0",",,",", ,"),"", $statusElem);						
					}					
					if(strpos($statusElem, "6_Os=1")===false){
						$statusElem = str_replace(array("elem_chng_div6_Os=0",",,",", ,"),"", $statusElem);
					}					
				}
				if(!empty($statusElem)){ $statusElem.=",";  }
				
				$statusElem .= "elem_chng_div6_Od=1,elem_chng_div6_Os=1";
				
				//attached to la
				$sql = "UPDATE chart_drawings SET idoc_drawing_id = '".$idoc_drawing_id."', statusElem='".sqlEscStr($statusElem)."'  WHERE id='".$id."'  ";
				$row=sqlQuery($sql);
				
				//update idocdrawing
				$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
						SET drawing_for='SLE', drawing_for_master_id='".$id."'
						WHERE id = '".$arr["insertId"]."' ";
				$row=sqlQuery($sql);				
			}
			
			
			
		}else if($arr["canvasBackImage"] == "imgGonioCanvas"){
			//
			
			//Oject
			$oGonio = new Gonio($arr["patId"],$arr["formId"]);
			if(!$oGonio->isRecordExists()){	$oGonio->carryForward();	}
		
			$sql = "select * FROM chart_gonio WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' ";
			$row=sqlQuery($sql);
			//exit($sql);
			if($row != false){
				$id = $row["gonio_id"];
				$idoc_drawing_id = $row["idoc_drawing_id"];
				$statusElem = $row["statusElem"];
				
				if(!empty($idoc_drawing_id)){ $idoc_drawing_id .= ","; }
				$idoc_drawing_id .= $arr["insertId"];
				
				if(!empty($statusElem)){	//elem_chng_divIop3_Od=1,elem_chng_divIop3_Os=1,
					if(strpos($statusElem, "3_Od=1")===false){
						$statusElem = str_replace(array("elem_chng_divIop3_Od=0",",,",", ,"),"", $statusElem);						
					}					
					if(strpos($statusElem, "3_Os=1")===false){
						$statusElem = str_replace(array("elem_chng_divIop3_Os=0",",,",", ,"),"", $statusElem);
					}					
				}
				if(!empty($statusElem)){ $statusElem.=",";  }
				
				$statusElem .= "elem_chng_divIop3_Od=1,elem_chng_divIop3_Os=1";
				
				//attached to la
				$sql = "UPDATE chart_gonio SET idoc_drawing_id = '".$idoc_drawing_id."', statusElem='".sqlEscStr($statusElem)."'  WHERE gonio_id='".$id."'  ";
				$row=sqlQuery($sql);
				
				//update idocdrawing
				$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
						SET drawing_for='IOP_GONIO', drawing_for_master_id='".$id."'
						WHERE id = '".$arr["insertId"]."' ";
				$row=sqlQuery($sql);				
			}
		
		}else if($arr["canvasBackImage"] == "imgLaCanvas"){
			
			
			$oFE = new FundusExam($arr["patId"],$arr["formId"]);
			if(!$oFE->isRecordExists()){	$oFE->carryForward();	}
			$oON = new OpticNerve($arr["patId"],$arr["formId"]);
			if(!$oON->isRecordExists()){	$oON->carryForward();	}
			
			$sql = "select * FROM chart_drawings WHERE form_id = '".$arr["formId"]."' AND patient_id='".$arr["patId"]."' AND purged='0' AND exam_name='FundusExam'  ";
			$row=sqlQuery($sql);
			//exit($sql);
			if($row != false){
				$id = $row["id"];
				$idoc_drawing_id = $row["idoc_drawing_id"];
				$statusElem = $row["statusElem"];
				
				if(!empty($idoc_drawing_id)){ $idoc_drawing_id .= ","; }
				$idoc_drawing_id .= $arr["insertId"];
				
				if(!empty($statusElem)){	//elem_chng_div5_Od=1,elem_chng_div5_Os=1,
					if(strpos($statusElem, "5_Od=1")===false){
						$statusElem = str_replace(array("elem_chng_div5_Od=0",",,",", ,"),"", $statusElem);						
					}					
					if(strpos($statusElem, "5_Os=1")===false){
						$statusElem = str_replace(array("elem_chng_div5_Os=0",",,",", ,"),"", $statusElem);
					}					
				}
				if(!empty($statusElem)){ $statusElem.=",";  }
				
				$statusElem .= "elem_chng_div5_Od=1,elem_chng_div5_Os=1";
				
				//attached to la
				$sql = "UPDATE chart_drawings SET idoc_drawing_id = '".$idoc_drawing_id."', statusElem='".sqlEscStr($statusElem)."'  WHERE id='".$id."'  ";
				$row=sqlQuery($sql);
				
				//update idocdrawing
				$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
						SET drawing_for='Fundus_Exam', drawing_for_master_id='".$id."'
						WHERE id = '".$arr["insertId"]."' ";
				$row=sqlQuery($sql);				
			}		
			
		}	
	
	} 
	
	
	//
	function getHTMLDrawingData_4PtExamNDOS($patient_id, $form_id, $exam, $arrExistingids=array()){		
		
		$arrExamBackground = array(
			"LA"=>"imgLidsAndLacrimalCanvas",
			"EOM"=>"imgEOMCanvas",
			"External"=>"imgPicConCanvas",
			"Gonio"=>"imgGonioCanvas",
			"SLE"=>"imgPicConCanvas",
			"Fundus"=>"imgLaCanvas"	
		);
		
		$drawbackground = $arrExamBackground[$exam];
		$ret = array();
		if(!empty($patient_id) && !empty($form_id) && !empty($drawbackground)){			
			$ret = $this->getHTMLDrawingData_PtChart($patient_id,$form_id, $drawbackground);
		}	
		
		//merge arrays		
		if(count($ret)>0){
			
			$len = count($ret[0]);
			
			for($i=0; $i<$len; $i++){
				
				if(in_array($ret[0][$i], $arrExistingids[0])){  continue; }
				
				$arrExistingids[0][] = $ret[0][$i];
				$arrExistingids[1][] = $ret[1][$i];
				$arrExistingids[2][] = $ret[2][$i];
				$arrExistingids[3][] = $ret[3][$i];
				$arrExistingids[4][] = $ret[4][$i];
				$arrExistingids[5][] = $ret[5][$i];
				$arrExistingids[6][] = $ret[6][$i];			
			
			}			
		}
		
		return $arrExistingids;
	}
	
	function getHTMLDrawingData_PtChart($patient_id,$form_id, $drawbackground=""){
		$arrReturn = array();
		$dbdrawID = $dbRedPixel = $dbGreenPixel = $dbBluePixel = $dbAlphaPixel = $dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $canvasDataFileNameDB = $dbCanvasImageDataPoint = "";
		$arrDBdrawID = $arrDBRedPixel = $arrDBGreenPixel = $arrDBBluePixel = $arrDBAlphaPixel = $arrDBTollImage = $arrDBPatTestName = $arrDBPatTestId = $arrDBTestImg = array();
		$arrDBCanvasDataFileNameDB = $arrDBCanvasImageDataPoint = $arrDBImgDB = array();
		
		$phraseBackground="";
		if(!empty($drawbackground)){
			$phraseBackground = " AND toll_image='".$drawbackground."' AND  drawing_for='DrawPane' ";
		}
		
		$qryGetHTMLDrawing = "select id as drawID, toll_image as tollImage, patient_test_name as patTestName, patient_test_id as patTestId, patient_test_image_path as testImg,
								drwNE	
								from ".constant("IMEDIC_SCAN_DB").".idoc_drawing where patient_id='".$patient_id."' AND patient_form_id='".$form_id."' AND deletedby ='0' ".
								$phraseBackground." ;";
		$rsGetHTMLDrawing = sqlStatement($qryGetHTMLDrawing);	
		
		//CHECK ARCHIVE DB TBL --
		if($rsGetHTMLDrawing!=false){
			if(imw_num_rows($rsGetHTMLDrawing) == 0 && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){					
				$qryGetHTMLDrawing = "select id as drawID, toll_image as tollImage, patient_test_name as patTestName, patient_test_id as patTestId, patient_test_image_path as testImg,
									drwNE
									from ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing where patient_id='".$patient_id."' AND patient_form_id='".$form_id."' AND deletedby ='0' ".
									$phraseBackground." ;";
				$rsGetHTMLDrawing = sqlStatement($qryGetHTMLDrawing);					
			}
		}
		//CHECK ARCHIVE DB TBL --
		
		if($rsGetHTMLDrawing){
			if(imw_num_rows($rsGetHTMLDrawing) > 0){
				$oSaveFile = new SaveFile();
				$path = $oSaveFile->getUploadDirPath(1);
				for($ai=1;$rowGetHTMLDrawing = sqlFetchArray($rsGetHTMLDrawing);$ai++){
					$dbdrawID = $dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $canvasDataFileNameDB = $dbCanvasImageDataPoint = "";
					$dbdrawID = $rowGetHTMLDrawing["drawID"];
					$dbTollImage = $rowGetHTMLDrawing["tollImage"];
					$dbPatTestName = $rowGetHTMLDrawing["patTestName"];
					$dbPatTestId = $rowGetHTMLDrawing["patTestId"];
					$dbTestImg = $rowGetHTMLDrawing["testImg"];
					if((empty($dbTestImg) == false) && ($dbTollImage == "imgDB")){
						
						$imgDB = ".imgDB{background:url(".$path.$dbTestImg.");}";
					}
					$dbdrwNE = $rowGetHTMLDrawing["drwNE"];
					$arrDBdrawID[] = $dbdrawID;
					$arrDBTollImage[] = $dbTollImage;
					$arrDBPatTestName[] = $dbPatTestName;
					$arrDBPatTestId[] = $dbPatTestId;
					$arrDBTestImg[] = $dbTestImg;
					$arrDBImgDB[] = $imgDB;
					$arrDBdrwNE[] = $dbdrwNE;
				}
			}
			//@mysql_free_result($rsGetCanvasData);
		}
		//$arrReturn = array($dbTollImage, $dbPatTestName, $dbPatTestId, $dbTestImg, $imgDB);
		$arrReturn = array($arrDBdrawID, $arrDBTollImage, $arrDBPatTestName, $arrDBPatTestId, $arrDBTestImg, $arrDBImgDB,$arrDBdrwNE);
		return $arrReturn;
		
	}
	
	function getHTMLDrawingData($dbIdocDrawingId, $op = 0){
		$arrReturn = array();
		$dbdrawID = $dbRedPixel = $dbGreenPixel = $dbBluePixel = $dbAlphaPixel = $dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $canvasDataFileNameDB = $dbCanvasImageDataPoint = $db_drw_data_json = "";
		$arrDBdrawID = $arrDBRedPixel = $arrDBGreenPixel = $arrDBBluePixel = $arrDBAlphaPixel = $arrDBTollImage = $arrDBPatTestName = $arrDBPatTestId = $arrDBTestImg = array();
		$arrDBCanvasDataFileNameDB = $arrDBCanvasImageDataPoint = $arrDBImgDB = $arrDBdrw_data_json =  array();
		if($op == 0){
			$qryGetHTMLDrawing = "select red_pixel as redPixel, green_pixel as greenPixel, blue_pixel as bluePixel, alpha_pixel as alphaPixel, toll_image as tollImage,
									patient_test_name as patTestName, patient_test_id as patTestId, patient_test_image_path as testImg, 
									drawing_image_path as canvasDataImgFileName, drawing_images_data_points as canvasImageDataPoint,
									drwNE, drw_data_json
									 from ".constant("IMEDIC_SCAN_DB").".idoc_drawing where id = '".$dbIdocDrawingId."' AND deletedby ='0' ;";
			$rsGetHTMLDrawing = sqlStatement($qryGetHTMLDrawing);
			
			//CHECK ARCHIVE DB TBL --
			if($rsGetHTMLDrawing){
				if(imw_num_rows($rsGetHTMLDrawing) == 0 && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){ //Check Archive Table					
					$qryGetHTMLDrawing = "select red_pixel as redPixel, green_pixel as greenPixel, blue_pixel as bluePixel, alpha_pixel as alphaPixel, toll_image as tollImage,
										patient_test_name as patTestName, patient_test_id as patTestId, patient_test_image_path as testImg, 
										drawing_image_path as canvasDataImgFileName, drawing_images_data_points as canvasImageDataPoint,
										drwNE, drw_data_json
										 from ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing where id = '".$dbIdocDrawingId."' AND deletedby ='0' ;";
					$rsGetHTMLDrawing = sqlStatement($qryGetHTMLDrawing);	
				}
			}
			//CHECK ARCHIVE DB TBL --
			
			
			if($rsGetHTMLDrawing){
				if(imw_num_rows($rsGetHTMLDrawing) > 0){
					$oSaveFile = new SaveFile();
					$path = $oSaveFile->getUploadDirPath(1);
					$rowGetHTMLDrawing = sqlFetchArray($rsGetHTMLDrawing);
					$dbRedPixel = $rowGetHTMLDrawing["redPixel"];
					$dbGreenPixel = $rowGetHTMLDrawing["greenPixel"];
					$dbBluePixel = $rowGetHTMLDrawing["bluePixel"];
					$dbAlphaPixel = $rowGetHTMLDrawing["alphaPixel"];
					$dbTollImage = $rowGetHTMLDrawing["tollImage"];				
					$dbPatTestName = $rowGetHTMLDrawing["patTestName"];
					$dbPatTestId = $rowGetHTMLDrawing["patTestId"];
					$dbTestImg = $rowGetHTMLDrawing["testImg"];
					$canvasDataFileNameDB = $rowGetHTMLDrawing["canvasDataImgFileName"];
					$dbCanvasImageDataPoint = $rowGetHTMLDrawing["canvasImageDataPoint"];
					if((empty($dbTestImg) == false) && ($dbTollImage == "imgDB")){
						$imgDB = ".imgDB{background:url(".$path.$dbTestImg.");}";
					}
					$dbdrwNE = $rowGetHTMLDrawing["drwNE"];
					$db_drw_data_json = $rowGetHTMLDrawing["drw_data_json"];
				}
				//@mysql_free_result($rsGetCanvasData);
			}
			
			$arrReturn = array($dbRedPixel, $dbGreenPixel, $dbBluePixel, $dbAlphaPixel, $dbTollImage, 
							$dbPatTestName, $dbPatTestId, $dbTestImg, $canvasDataFileNameDB, 
							$dbCanvasImageDataPoint, $imgDB, $dbdrwNE,$db_drw_data_json);
		}
		elseif($op == 1){
			$qryGetHTMLDrawing = "select id as drawID, toll_image as tollImage, patient_test_name as patTestName, patient_test_id as patTestId, patient_test_image_path as testImg,
									drwNE, drw_data_json	
									from ".constant("IMEDIC_SCAN_DB").".idoc_drawing where id IN(".$dbIdocDrawingId.") AND deletedby ='0'  ;";
			$rsGetHTMLDrawing = sqlStatement($qryGetHTMLDrawing);
			
			//CHECK ARCHIVE DB TBL --
			if($rsGetHTMLDrawing){
				if(imw_num_rows($rsGetHTMLDrawing) == 0 && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){					
					$qryGetHTMLDrawing = "select id as drawID, toll_image as tollImage, patient_test_name as patTestName, patient_test_id as patTestId, patient_test_image_path as testImg,
										drwNE, drw_data_json
										from ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing where id IN(".$dbIdocDrawingId.") AND deletedby ='0'  ;";
					$rsGetHTMLDrawing = sqlStatement($qryGetHTMLDrawing);					
				}
			}
			//CHECK ARCHIVE DB TBL --
			
			if($rsGetHTMLDrawing){
				if(imw_num_rows($rsGetHTMLDrawing) > 0){
					$oSaveFile = new SaveFile();
					$path = $oSaveFile->getUploadDirPath(1);
					
					while($rowGetHTMLDrawing = sqlFetchArray($rsGetHTMLDrawing)){
						$dbdrawID = $dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $canvasDataFileNameDB = $dbCanvasImageDataPoint = "";
						$dbdrawID = $rowGetHTMLDrawing["drawID"];
						$dbTollImage = $rowGetHTMLDrawing["tollImage"];
						$dbPatTestName = $rowGetHTMLDrawing["patTestName"];
						$dbPatTestId = $rowGetHTMLDrawing["patTestId"];
						$dbTestImg = $rowGetHTMLDrawing["testImg"];
						if((empty($dbTestImg) == false) && ($dbTollImage == "imgDB")){
							$imgDB = ".imgDB{background:url(".$path.$dbTestImg.");}";
						}
						$dbdrwNE = $rowGetHTMLDrawing["drwNE"];
						$db_drw_data_json = $rowGetHTMLDrawing["drw_data_json"];
						
						$arrDBdrawID[] = $dbdrawID;
						$arrDBTollImage[] = $dbTollImage;
						$arrDBPatTestName[] = $dbPatTestName;
						$arrDBPatTestId[] = $dbPatTestId;
						$arrDBTestImg[] = $dbTestImg;
						$arrDBImgDB[] = $imgDB;
						$arrDBdrwNE[] = $dbdrwNE;
						$arrDBdrw_data_json[] = $db_drw_data_json;
					}
				}
				//@mysql_free_result($rsGetCanvasData);
			}
			//$arrReturn = array($dbTollImage, $dbPatTestName, $dbPatTestId, $dbTestImg, $imgDB);
			$arrReturn = array($arrDBdrawID, $arrDBTollImage, $arrDBPatTestName, $arrDBPatTestId, $arrDBTestImg, $arrDBImgDB,$arrDBdrwNE, $arrDBdrw_data_json);
		}
		return $arrReturn;
	}
	
	function getIDOCdrawingsImage($sectionName,$primaryId,$flgMani="1",$flgGetDraw="0"){
		if($sectionName == "RV"){
			$sectionName = "Fundus_Exam";
		}else if($sectionName == "Gonio"){
			$sectionName = "IOP_GONIO";
		}
		
		$returnStrImagePath="";
	 	$qryGetDrawing = "SELECT red_pixel, green_pixel, blue_pixel, drawing_images_data_points, drawing_image_path,
						row_modify_by,						
						DATE_FORMAT(row_modify_date_time,'%m-%d-%y') AS drawLMB,
						DATE_FORMAT(row_created_date_time,'%m-%d-%y') AS drawDate,
						DATE_FORMAT(row_visit_dos,'%m-%d-%y') AS drawDOS 		
						FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing where drawing_for='".$sectionName."' and 
						drawing_for_master_id='".$primaryId."' AND deletedby='0' LIMIT 1";
		$rsGetDrawing = sqlStatement($qryGetDrawing);		
		
		if($rsGetDrawing){		
			$IDOCdrawingQueryNumRow = imw_num_rows($rsGetDrawing);
			
			//CHECK ARCHIVE DB TBL --
			if($IDOCdrawingQueryNumRow==0){
				//--
				if(!empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){ 
					$qryGetDrawing = "SELECT red_pixel, green_pixel, blue_pixel, drawing_images_data_points, drawing_image_path,
								row_modify_by,						
								DATE_FORMAT(row_modify_date_time,'%m-%d-%y') AS drawLMB,
								DATE_FORMAT(row_created_date_time,'%m-%d-%y') AS drawDate,
								DATE_FORMAT(row_visit_dos,'%m-%d-%y') AS drawDOS 		
								FROM ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing where drawing_for='".$sectionName."' and 
								drawing_for_master_id='".$primaryId."' AND deletedby='0' LIMIT 1";								
					$rsGetDrawing = sqlStatement($qryGetDrawing);
					if($rsGetDrawing){	$IDOCdrawingQueryNumRow = imw_num_rows($rsGetDrawing); }
				}
				//--
			}			
			//CHECK ARCHIVE DB TBL --
			
			if($sectionName=="Fundus_Exam"){
			//exit($qryGetDrawing);
			}
			
			
			if(imw_num_rows($rsGetDrawing) > 0){		
				$rowGetDrawing = sqlFetchArray($rsGetDrawing);
				//Check Drawing
				$strRED = $strGreen = $strBlue = $strDrawingImagesDataPoints = "";
				if(!empty($rowGetDrawing["red_pixel"])){ $strRED = str_replace('0,','',$rowGetDrawing["red_pixel"]);}
				if(!empty($rowGetDrawing["green_pixel"])){ $strGreen = str_replace('0,','',$rowGetDrawing["green_pixel"]);}
				if(!empty($rowGetDrawing["blue_pixel"])){ $strBlue = str_replace('0,','',$rowGetDrawing["blue_pixel"]);}
				$strDrawingImagesDataPoints = $rowGetDrawing["drawing_images_data_points"];
				$drawing_image_path=$rowGetDrawing["drawing_image_path"];
				
				if(((empty($strRED) == false) || (empty($strGreen) == false) || (empty($strBlue) == false)) 
					|| (empty($strDrawingImagesDataPoints) == false&&$strDrawingImagesDataPoints!="$~$") 
					|| !empty($flgGetDraw) 
					|| !empty($drawing_image_path)
					){ //
					
					$osavef= new SaveFile();
					$up_dir_path = $osavef->getUploadDirPath();
					
					$strImagePath = $rowGetDrawing["drawing_image_path"];
					$strImagePath = str_replace("/", "\\", $strImagePath);
					if(!empty($strImagePath)){$strImagePathReal = $up_dir_path.$strImagePath;}					
					$strImagePathReal = str_ireplace("\\", "/", $strImagePathReal);	
					if(file_exists($strImagePathReal)){	
					
						//*thumb
						$tmp = str_replace(".png","_b.png", $drawing_image_path);
						if(!empty($tmp)){ $tmp = $up_dir_path.$tmp; }
						if(file_exists($tmp)){
							$strImagePathReal=$tmp;
						}						
						//*/
					
						if($flgMani == "1"){
							$returnStrImagePath = $strImagePathReal;
						}else if($flgMani == "2"){ //return array with path and date
							$returnStrImagePath = $strImagePathReal;
							//$drawDate = $rowGetDrawing["drawDate"];
							$drawDate = $rowGetDrawing["drawDOS"];
							
							$returnStrImagePath = array($returnStrImagePath,$drawDate,$rowGetDrawing["row_modify_by"],$rowGetDrawing["drawLMB"]);
						}else{
							$returnStrImagePath = $strImagePathReal;
						}
					}
				}			
			} 
		}		
		return $returnStrImagePath;
	}

	function isExamDrawingExits($pid,$formId,$examId,$exam,$flg=""){
		if($exam == "RV"){
			$exam = "Fundus_Exam";
		}else if($exam == "Gonio"){
			$exam = "IOP_GONIO";
		}
	
		$ret = 0;
		$sql = "SELECT red_pixel, green_pixel, blue_pixel, row_created_date_time, row_visit_dos, drawing_images_data_points,
				drawing_image_path
				FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
				WHERE LOWER(drawing_for) = LOWER('".$exam."') 
				AND drawing_for_master_id = '".$examId."' AND patient_id = '".$pid."' AND patient_form_id = '".$formId."'  AND deletedby='0' ";
		$row = sqlQuery($sql);
		/*##
		if($pid==1000652||$pid==55602){
			echo "<br>".$sql;
			echo "HELOOOOO";
		}
		##
		*/
		
		//CHECK ARCHIVE DB TBL --
		if($row==false && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){
			$sql = "SELECT red_pixel, green_pixel, blue_pixel, row_created_date_time, row_visit_dos, drawing_images_data_points,
				drawing_image_path
				FROM ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing 
				WHERE LOWER(drawing_for) = LOWER('".$exam."') 
				AND drawing_for_master_id = '".$examId."' AND patient_id = '".$pid."' AND patient_form_id = '".$formId."'  AND deletedby='0' ";
			$row = sqlQuery($sql);
		}
		//CHECK ARCHIVE DB TBL --
		
		if($row != false){
		
			//echo "HELLO222";
			
			$strRED = $strGreen = $strBlue = "";
			if(!empty($row["red_pixel"])){ $strRED = str_replace('0,','',$row["red_pixel"]);}
			if(!empty($row["green_pixel"])){ $strGreen = str_replace('0,','',$row["green_pixel"]);}
			if(!empty($row["blue_pixel"])){ $strBlue = str_replace('0,','',$row["blue_pixel"]);}
			$strDrawingImagesDataPoints = trim($row["drawing_images_data_points"]);
			$drawing_image_path=trim($row["drawing_image_path"]);	
			
			if(!empty($strRED)||!empty($strGreen)||!empty($strBlue)			
				|| (!empty($strDrawingImagesDataPoints) && $strDrawingImagesDataPoints!="$~$")
				|| !empty($drawing_image_path)
				) {
				$ret = 1;
				if($flg=="date"){
					//$ret = $row["row_created_date_time"];
					$ret = $row["row_visit_dos"];
				}
				
				//echo "--- HELLO --- ";
				
			}
			/*else{				
				
				##
				if($pid==1000652||$pid==55602){
					echo "<br>".$strRED." - ".$strGreen." - ".$strBlue." - ".$strDrawingImagesDataPoints;
				}
				##
			}*/			
			
		}
		return $ret;
	}

	function carryForward($arrIN, $flgchangeDt=0){

		$pid=$arrIN["pid"];
		$formId=$arrIN["formId"];
		$examId=$arrIN["examId"];
		$exam=$arrIN["exam"];			
		$examIdLF=$arrIN["examIdLF"];
		$strDrwIdsLF=$arrIN["strDrwIdsLF"];		
		$strDrwIdsLF_Phrase = (!empty($strDrwIdsLF)) ? "AND id IN (".$strDrwIdsLF.")" : "" ;
		if($exam == "FundusExam"){ $exam="Fundus_Exam"; }	
		
		$sql = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing
				WHERE LOWER(drawing_for) =LOWER('".$exam."')
				AND drawing_for_master_id ='".$examIdLF."' 
				AND patient_id = '".$pid."' ".
				" AND deletedby='0' ".
				$strDrwIdsLF_Phrase.
				" ORDER BY id ";		
		$rez = sqlStatement($sql);
		
		//CHECK ARCHIVE DB TBL ---
		if(imw_num_rows($rez)<=0 && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){
			$sql = "SELECT * FROM ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing
				WHERE LOWER(drawing_for) =LOWER('".$exam."')
				AND drawing_for_master_id ='".$examIdLF."' 
				AND patient_id = '".$pid."'".
				" AND deletedby='0' ".
				$strDrwIdsLF_Phrase.
				" ORDER BY id ";		
			$rez = sqlStatement($sql);
		}
		//CHECK ARCHIVE DB TBL --		
		
		$strInsertId = "";
		
		for($ii=1; $row=sqlFetchArray($rez); $ii++){
			$prv_id = $row["id"];
			$red_pixel=$row["red_pixel"];
			$green_pixel=$row["green_pixel"];
			$blue_pixel=$row["blue_pixel"];
			$alpha_pixel=$row["alpha_pixel"];
			$toll_image=$row["toll_image"];
			$drawing_for=$row["drawing_for"];
			$drawing_for_master_id=$examId; //$row[""];
			//$drawing_image_path=$row[""];
			$prv_row_created_by=$row["row_created_by"];
			$prv_row_created_date_time=$row["row_created_date_time"];
			$prv_row_visit_dos=$row["row_visit_dos"];
			$prv_drwNE=$row["drwNE"];
			//$row_modify_by=$row["row_modify_by"];
			//$row_modify_date_time=$row["row_modify_date_time"];
			$patient_test_name=$row["patient_test_name"];
			$patient_test_id=$row["patient_test_id"];
			$drw_data_json=sqlEscStr($row["drw_data_json"]);
			
			//$patient_id=$pid;
			//$patient_form_id=$formId;
			$patient_test_image_path=$row["patient_test_image_path"];
			$drawing_images_data_points=$this->cleanTxt($row["drawing_images_data_points"]);
			
			$drawing_image_path ="";	
			$drawing_image_path = $row["drawing_image_path"];
			$destpath="";	
			if(!empty($drawing_image_path)){
				//$pth=$GLOBALS['incdir']."/main/uploaddir";
				$oSaveFile = new SaveFile();
				$pth = $oSaveFile->getUploadDirPath();
				if(file_exists($pth.$drawing_image_path)){
					$destpath = "/PatientId_".$pid."/idoc_drawing/canvas_drawing_image/".
							$exam."_idoc_drawing_".$ii."_".date("YmdHsi")."_".session_id().".png";
					copy($pth.$drawing_image_path,$pth.$destpath);
					
					//with background
					$drawing_image_path_b = str_replace(".png","_b.png",$pth.$drawing_image_path);	
					if(file_exists($drawing_image_path_b)){
						$destpath_b = str_replace(".png","_b.png",$destpath);
						copy($drawing_image_path_b, $pth.$destpath_b);
					}
					
					//thumb					
					$drawing_image_path_s = str_replace(".png","_s.png",$pth.$drawing_image_path);	
					if(file_exists($drawing_image_path_s)){
						$destpath_s = str_replace(".png","_s.png",$destpath);
						copy($drawing_image_path_s, $pth.$destpath_s);
					}
				}
			}			
			
			//
			//WNL, NC logic should apply to all drawings not just Fundus.  Basically all drawings functionality should be like Fundus.
			//
			///For Fundus do not change Done Date
			if($flgchangeDt==0){ //$drawing_for == "Fundus_Exam"&&
				$row_created_by=$prv_row_created_by;
				$row_created_date_time=$prv_row_created_date_time;
				$row_visit_dos=$prv_row_visit_dos;
			}else{
				$row_created_by=$_SESSION["authId"];
				$row_created_date_time=date("Y-m-d H:i:s");
				$row_visit_dos=$this->getVisitDOS($formId);
			}
			
			$sql = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing
					WHERE patient_id='".$pid."'
					AND patient_form_id='".$formId."'
					AND LOWER(drawing_for)=LOWER('".$drawing_for."') 
					AND carryfwd_id = '".$prv_id."'
					";
			$row = sqlQuery($sql);
			
			//CHECK ARCHIVE DB TBL --
			if($row==false && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){
				
				$sql = "SELECT * FROM ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing
					WHERE patient_id='".$pid."'
					AND patient_form_id='".$formId."'
					AND LOWER(drawing_for)=LOWER('".$drawing_for."') 
					AND carryfwd_id = '".$prv_id."'
					";
				$row = sqlQuery($sql);
				
			}
			//CHECK ARCHIVE DB TBL --
			
			
			if($row!=false){
				
				if(!empty($row["id"])){ $strInsertId.=$row["id"].","; }
				$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing
						SET ".
						/*
						"red_pixel='".$red_pixel."',
						green_pixel='".$green_pixel."',
						blue_pixel='".$blue_pixel."',
						alpha_pixel='".$alpha_pixel."', ".
						*/
						" toll_image='".$toll_image."',						
						drawing_for_master_id='".$drawing_for_master_id."',
						drawing_image_path='".$destpath."',
						row_created_by='".$row_created_by."',
						row_created_date_time='".$row_created_date_time."',
						row_modify_by='',
						row_modify_date_time='',
						patient_test_name='".$patient_test_name."',
						patient_test_id='".$patient_test_id."',						
						patient_test_image_path='".$patient_test_image_path."',
						drawing_images_data_points='".$drawing_images_data_points."',
						row_visit_dos='".$row_visit_dos."',
						st_carryfwd='1',
						drwNE = '".$prv_drwNE."',
						drw_data_json = '".$drw_data_json."'
						WHERE patient_id='".$pid."'
						AND patient_form_id='".$formId."'
						AND LOWER(drawing_for)=LOWER('".$drawing_for."')
						AND carryfwd_id = '".$prv_id."'
						";
				$res=sqlQuery($sql);
			
			}
			else{
				$sql = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".idoc_drawing
						SET
						id=NULL, ".
						/*
						"red_pixel='".$red_pixel."',
						green_pixel='".$green_pixel."',
						blue_pixel='".$blue_pixel."',
						alpha_pixel='".$alpha_pixel."', ".
						*/
						" toll_image='".$toll_image."',
						drawing_for='".$drawing_for."',
						drawing_for_master_id='".$drawing_for_master_id."',
						drawing_image_path='".$destpath."',
						row_created_by='".$row_created_by."',
						row_created_date_time='".$row_created_date_time."',
						row_modify_by='',
						row_modify_date_time='',
						patient_test_name='".$patient_test_name."',
						patient_test_id='".$patient_test_id."',
						patient_id='".$pid."',
						patient_form_id='".$formId."',
						patient_test_image_path='".$patient_test_image_path."',
						drawing_images_data_points='".$drawing_images_data_points."',
						row_visit_dos='".$row_visit_dos."',
						st_carryfwd='1',
						drwNE = '".$prv_drwNE."',
						drw_data_json = '".$drw_data_json."',
						carryfwd_id = '".$prv_id."'	
						";
				$insertId=sqlInsert($sql);
				if(!empty($insertId)){ $strInsertId.=$insertId.","; }
			}
			
		}

		if(!empty($strInsertId)){			
			
			if(!empty($strDrwIdsLF_Phrase)){
				//GEt Already existing docdrawingids			
				$sql = "SELECT idoc_drawing_id FROM ".$arrIN["examMasterTable"]." ".
						" where ".$arrIN["examMasterTablePriColumn"]." = '".$examId."' ";
				$row = sqlQuery($sql);
				if($row!=false){
					$idoc_drawing_id = $row["idoc_drawing_id"];
					if(!empty($idoc_drawing_id)){
						$idoc_drawing_id_arr = explode(",",$idoc_drawing_id);
						$ln_tmp = count($idoc_drawing_id_arr);
						for($i=0;$i<$ln_tmp;$i++){
							$tmp = trim($idoc_drawing_id_arr[$i]);
							if(!empty($tmp) && strpos($strInsertId,$tmp)===false){
								$strInsertId.=$tmp.",";							
							}
						}					
					}				
				}
			}	
			
			//Delete--
			$strInsertId = trim($strInsertId);
			$strInsertId = trim($strInsertId, ",");
			if(!empty($examId) && !empty($examIdLF) && !empty($strInsertId)){
				//check if drwing exists for same exam
				$sql = " UPDATE ".constant("IMEDIC_SCAN_DB").".`idoc_drawing` SET deletedby='".$_SESSION["authId"]."' WHERE `drawing_for_master_id`='".$examId."' AND LOWER(drawing_for) =LOWER('".$exam."') AND deletedby = '0' AND id NOT IN (".$strInsertId.") ";
				$row = sqlQuery($sql);
			}
			//Delete--
			
			$strInsertId = rtrim($strInsertId, ", ");			
			
			$sql = "update ".$arrIN["examMasterTable"]." set idoc_drawing_id = '".$strInsertId."' 
					where ".$arrIN["examMasterTablePriColumn"]." = '".$examId."'";
			$rs = sqlQuery($sql);
			
		}
	}

	function resetVals($arrIN){
		$pid=$arrIN["pid"];
		$formId=$arrIN["formId"];
		$examId=$arrIN["examId"];
		$exam=$arrIN["exam"];			
		$tblNm=$arrIN["examMasterTable"];
		$tblPK=$arrIN["examMasterTablePriColumn"];
		$row_visit_dos = $this->getVisitDOS($formId);
		if($exam == "FundusExam"){ $exam="Fundus_Exam"; }
		//
		//WNL, NC logic should apply to all drawings not just Fundus.  Basically all drawings functionality should be like Fundus.
		//
		//Check Fundus for date change
		///if($exam == "Fundus_Exam"){
			$phaseDtChange = " row_created_by=IF(st_carryfwd='1', '".$_SESSION["authId"]."', row_created_by), 
						   row_created_date_time=IF(st_carryfwd='1', '".wv_dt('now')."', row_created_date_time), 
						   row_visit_dos=IF(st_carryfwd='1', '".$row_visit_dos."', row_visit_dos), 
						   ";
		//}else{
		//	$phaseDtChange = "";
		//}
		
		$sql = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing
				WHERE patient_id='".$pid."'
				AND patient_form_id='".$formId."'
				AND LOWER(drawing_for)=LOWER('".$exam."')  AND deletedby='0'  ";
		$row = sqlQuery($sql);
		
		//CHECK ARCHIVE DB TBL --
		if($row==false && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){
			$sql = "SELECT * FROM ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing
				WHERE patient_id='".$pid."'
				AND patient_form_id='".$formId."'
				AND LOWER(drawing_for)=LOWER('".$exam."') AND deletedby='0'  ";
			$row = sqlQuery($sql);			
		}		
		//CHECK ARCHIVE DB TBL --
		
		
		if($row!=false){
			$insertId=$row["id"];
			$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing
					SET
					red_pixel='',
					green_pixel='',
					blue_pixel='',
					alpha_pixel='',
					toll_image='',						
					
					drawing_image_path='',
					row_modify_by='".$_SESSION["authId"]."',
					row_modify_date_time='".date("Y-m-d H:i:s")."',
					patient_test_name='".$patient_test_name."',
					patient_test_id='".$patient_test_id."',						
					patient_test_image_path='".$patient_test_image_path."',
					drawing_images_data_points='".$drawing_images_data_points."',
					".$phaseDtChange."
					st_carryfwd='0',
					drwNE='',drw_data_json=''	
					WHERE patient_id='".$pid."'
					AND patient_form_id='".$formId."'
					AND LOWER(drawing_for)=LOWER('".$exam."')
					";
			$res=sqlQuery($sql);
			
			
			//$sql = "update ".$arrIN["examMasterTable"]." set idoc_drawing_id = '".$insertId."' 
			//		where ".$arrIN["examMasterTablePriColumn"]." = '".$examId."'";
			//$rs = sqlQuery($sql);
		}
	}
	
	/////////////////////////Function By Ram To Include IDOC images in Printing Section//////////////////////
	function getIDOCdrawingsBySection($sectionName,$primaryId,$flgMani="1"){
		if($sectionName=="RV"){
			$sectionName="Fundus_Exam";
		}
		
		global $objImageManipulation;
		$returnStrImagePath="";
		$IDOCdrawingQuery="SELECT red_pixel, drawing_image_path, 		
							DATE_FORMAT(row_created_date_time,'%m-%d-%y') AS drawDate ,
							DATE_FORMAT(row_visit_dos,'%m-%d-%y') AS drawDateDos 
							FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
							where drawing_for='".$sectionName."' and drawing_for_master_id='".$primaryId."' AND deletedby='0'  ";		
		$IDOCdrawingQueryRes=sqlStatement($IDOCdrawingQuery);
		
		//CHECK ARCHIVE DB TBL --
		if($IDOCdrawingQueryRes){			
			$IDOCdrawingQueryNumRow=imw_num_rows($IDOCdrawingQueryRes);
			if($IDOCdrawingQueryNumRow==0 && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){
				$IDOCdrawingQuery="SELECT red_pixel, drawing_image_path, 		
								DATE_FORMAT(row_created_date_time,'%m-%d-%y') AS drawDate ,
								DATE_FORMAT(row_visit_dos,'%m-%d-%y') AS drawDateDos 
								FROM ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing 
								where drawing_for='".$sectionName."' and drawing_for_master_id='".$primaryId."' AND deletedby='0'  ";		
				$IDOCdrawingQueryRes=sqlStatement($IDOCdrawingQuery);
			}
		}	
		//CHECK ARCHIVE DB TBL --		
		
		
		if($IDOCdrawingQueryRes){
			
			$IDOCdrawingQueryNumRow=imw_num_rows($IDOCdrawingQueryRes);
			if($IDOCdrawingQueryNumRow>0){
				
				$resDrawingArray=sqlFetchArray($IDOCdrawingQueryRes);
				if(empty($resDrawingArray["red_pixel"]) == false||!empty($resDrawingArray["drawing_image_path"])){
					
					$oSaveFile = new SaveFile();
					$path = $oSaveFile->getUploadDirPath();
				
					$lamDrwingImagePath=$resDrawingArray["drawing_image_path"];
					$lamDrwingImage = realpath($path.$lamDrwingImagePath);
					
					if(file_exists($lamDrwingImage)){
						if($flgMani=="1"){
							$objImageManipulation->load($lamDrwingImage);
							$arrPathInfo = pathinfo($lamDrwingImage);
							$strFilenameJPG = $arrPathInfo["dirname"]."\\".$arrPathInfo["filename"].".jpg";
							$objImageManipulation->save($strFilenameJPG);
							$returnStrImagePath = $strFilenameJPG;
						}else if($flgMani=="2"){ //return array with path and date
							$returnStrImagePath=$lamDrwingImage;
							//$drawDate=$resDrawingArray["drawDate"];drawDateDos
							$drawDate=$resDrawingArray["drawDateDos"];
							$returnStrImagePath=array($returnStrImagePath,$drawDate);
						}else{
							$returnStrImagePath=$lamDrwingImage;
						}
					}
				}			
			} 
		}
		return $returnStrImagePath;
	}
	/////////////////////////Function By Ram To Include IDOC images in Printing Section//////////////////////
	
	// Clear  defaultText //Please Enter Your Text on Drawing.......//
	function cleanTxt($str){		
		if(!empty($str)){
			$str=str_replace("Please Enter Your Text on Drawing.......","",$str);
		}
		return $str;
	}
	//

	//GET DocIds of images of exam from Form Id and patient Id
	//exam name, exam id
	function getExamDocids($arin){
		
		$patientid=$arin[0];
		$elem_formId=$arin[1];
		$mstrid= $arin[2];
		$examnm = $arin[3];	
		$recordModiDateDrawing=$arin[4];
		
		if($examnm == "FundusExam"){ $examnm="Fundus_Exam"; }
		
		$dbField="modi_note_Draw";
		//if($examnm=="EOM"){
		//	$dbField="modi_note";
		//}
		
		$arrIDocId = array();
		$strIDocId = "";
		$str_row_modify_Draw="";
		$qryGetIDocIdInMasetr = "select id,row_modify_by, DATE_FORMAT(row_modify_date_time,'%m-%d-%y %H:%i') as modidate 
							from ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
							where drawing_for = '".$examnm."' 
							and drawing_for_master_id = '".$mstrid."' 
							and patient_id = '".$patientid."' and patient_form_id = '".$elem_formId."' ";
		$rsGetIDocIdInMasetr = sqlStatement($qryGetIDocIdInMasetr);
		
		//CHECK ARCHIVE DB TBL --
		if($rsGetIDocIdInMasetr){
			$tmp =imw_num_rows($rsGetIDocIdInMasetr);
			if($tmp==0 && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){
				$qryGetIDocIdInMasetr = "select id,row_modify_by, DATE_FORMAT(row_modify_date_time,'%m-%d-%y %H:%i') as modidate 
								from ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing 
								where drawing_for = '".$examnm."' 
								and drawing_for_master_id = '".$mstrid."' 
								and patient_id = '".$patientid."' and patient_form_id = '".$elem_formId."' ";
				$rsGetIDocIdInMasetr = sqlStatement($qryGetIDocIdInMasetr);
			}
		}
		//CHECK ARCHIVE DB TBL --		
		
		if(imw_num_rows($rsGetIDocIdInMasetr) > 0){
			while($rowGetIDocIdInMasetr = sqlFetchArray($rsGetIDocIdInMasetr)){
				$arrIDocId[] = $rowGetIDocIdInMasetr["id"];
				
				//GEt Drawing Modified Date
				if(!empty($recordModiDateDrawing) && $recordModiDateDrawing==$rowGetIDocIdInMasetr["id"]&&!empty($rowGetIDocIdInMasetr["row_modify_by"])){
					$str_row_modify_Draw=$this->getModiNotes_drawing($rowGetIDocIdInMasetr["row_modify_by"],$rowGetIDocIdInMasetr["modidate"],$dbField);
					$str_row_modify_Draw=", ".$str_row_modify_Draw;								
				}
				
			}
		}
		if(count($arrIDocId) > 0){
			$strIDocId = implode(",", $arrIDocId);
		}
		
		return array($strIDocId, $str_row_modify_Draw);
	}
	
	function getVisitDOS($id){
		$dt="";
		if(!empty($id)){
			$sql = "SELECT date_of_service FROM chart_master_table WHERE id = '".$id."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$dt = $row["date_of_service"];
			}
		}
		return $dt;
	}
	
	function resizeDrawing($pid, $filename){
		//include_once($GLOBALS['incdir']."/../common/SaveFile.php");
		$osf = new SaveFile($pid);
		
		$filename_2 = str_replace(".png","_s.png",$filename);
		$filename_3 = str_replace("_s.png","_b.png",$filename);
		
		$str = $osf->createThumbs($filename, $filename_2, 265, 169); //120, 75
		
		//@unlink($filename);
		
		if(file_exists($filename_2)){
			
			rename($filename, $filename_3);
			
			rename($filename_2,$filename);			
		}		
	}	
	
	function getDrawingTemplates($patId,$formId,$examId,$enm){	
	
		$rqExamId = (int)$examId;
		$rqFormId = (int)$formId;
		$rqScanUploadFor = trim($enm);
		//$rqCanvasId = trim($canvasId);

		//$patId = $_SESSION["patient"];
		
		$arrRet_chk = array();
		$arrRet = array();
	
		//include_once($GLOBALS['incdir']."/../common/SaveFile.php");
		//include_once($GLOBALS['incdir']."/CLSImageManipulation.php");
		
		//$osf = new SaveFile($patId);
		
		if(!empty($patId) && !empty($formId) && !empty($enm)){ ///&& !empty($examId)
		
		$strPhrase = "";
		if(!empty($examId)){
			$strPhrase .= " sc.test_id = '".$rqExamId."' and ";
		}
		if(!empty($enm) && $enm != "DRAWPANE_DSU"){
			$strPhrase .= " sc.image_form = '".$rqScanUploadFor."' and  ";
		}	
		
		//Get scans docs uploaded templates		
		
		$strTrFile = $strIframeFilePath = $dbPatName = $dbPatMname = "";
		//$upLoadPath = realpath($GLOBALS['incdir']."/../../main/uploaddir");			
		//$imgUpLoadPath = $GLOBALS['webroot']."/interface/main/uploaddir";
		
		$oSaveFile = new SaveFile();
		$upLoadPath = $oSaveFile->getUploadDirPath();
		$imgUpLoadPath = $oSaveFile->getUploadDirPath(1);
		
		$qryGetData = "select sc.scan_id as scanId, sc.image_name as docName, DATE_FORMAT(sc.created_date, '%m-%d-%Y') as docDate, sc.file_type as docType, sc.file_path as docPath,
						concat(pd.lname,', ',pd.fname) as patName , pd.mname as patMname
						from ".constant("IMEDIC_SCAN_DB").".scans sc 
						LEFT JOIN patient_data pd on pd.id = sc.patient_id
						where ".
						"( draw_template=1  ".
						"
						OR
						(
						".$strPhrase."
						sc.form_id = '".$rqFormId."' and 
						sc.patient_id = '".$patId."' 
						)
						) AND
						".
						"
						sc.status = '0'
						";
		
		$rsGetData = sqlStatement($qryGetData);
		if($rsGetData){
			if(imw_num_rows($rsGetData) > 0){
				$objSaveFile = new SaveFile($patId);
				$objImageManipulation = new CLSImageManipulation();
				
				$patientDir = "/PatientId_".$patId."";				
				$idocDrawingDirName = "/idoc_drawing/scan_upload";				
				$resizeImage = "/resize";
				//*
				if(is_dir($upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage) == false){
					mkdir($upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage, 0777, true);
				}
				
				if(is_dir($upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage."/thumb") == false){
					mkdir($upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage."/thumb", 0777, true);
				}				
				//*/
				
				$i = 1;
				while($rowGetData =  sqlFetchArray($rsGetData)){
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
					$strPathForCanvas = $imgUpLoadPath.$dbDocPath;
					
					if(file_exists($dbDocPathFull) && $dbDocType != "application/pdf"){
						
						//File in resize and thumb
						$dbDocPath_resize = str_replace("scan_upload/","scan_upload/resize/",$dbDocPath);
						$dbDocPathFull_resize = $upLoadPath.$dbDocPath_resize;
						
						if(file_exists($dbDocPathFull_resize)){
							$newResizeFileNameForDB = $dbDocPath_resize;
							$newResizeFileName = $dbDocPathFull_resize;
						}else{
							//path to img
							$newResizeFileNameForDB = $patientDir.$idocDrawingDirName.$resizeImage."/".$dbDocName;
							$newResizeFileName = $upLoadPath.$newResizeFileNameForDB;						
							//$newResizeFileNamePath = $objSaveFile->getFilePath($newResizeFileNameForDB, "w");
							
							//check if duplicate
							if(in_array($dbDocPath, $arrRet_chk)){ continue; }
							
							if(!file_exists($newResizeFileName)){	//if Not exists					
								$objImageManipulation->load($dbDocPathFull);
								$objImageManipulation->resize(730, 465);
								$objImageManipulation->save($newResizeFileName);
							}
						}
						
						//File in resize and thumb
						$dbDocPath_thumb = str_replace("scan_upload/","scan_upload/resize/thumb/",$dbDocPath);
						$dbDocPathFull_thumb = $upLoadPath.$dbDocPath_thumb;						
						if(file_exists($dbDocPathFull_thumb)){
							$newResizeFileNameForDB_T = $dbDocPath_thumb;
							$newResizeFileName_T = $dbDocPathFull_thumb;
						}else{
							//path to thumb
							$newResizeFileNameForDB_T = $patientDir.$idocDrawingDirName.$resizeImage."/thumb/".$dbDocName;
							$newResizeFileName_T = $upLoadPath.$newResizeFileNameForDB_T;						
							if(!file_exists($newResizeFileName_T)){
								//create thumb
								$objImageManipulation->load($dbDocPathFull);
								$objImageManipulation->resize(24, 24);
								$objImageManipulation->save($newResizeFileName_T);
							}
						}					
						
						$arrRet_chk[]=$dbDocPath;
						
						$newResizeFileNamePath = $objSaveFile->getFilePath($newResizeFileNameForDB, "w");
						$newResizeFileNamePath_T = $objSaveFile->getFilePath($newResizeFileNameForDB_T, "w");
						$arrRet[]=array($newResizeFileNamePath,$newResizeFileNamePath_T,$dbDocName,$dbScanId,$dbdrwNE);
					
						/*
						$newResizeFileName = $upLoadPath.$patientDir.$idocDrawingDirName.$resizeImage."/".$dbDocName;
						$newResizeFileNameForDB = $patientDir.$idocDrawingDirName.$resizeImage."/".$dbDocName;
						
						$newResizeFileNamePath = $objSaveFile->getFilePath($patientDir.$idocDrawingDirName.$resizeImage."/".$dbDocName, "w");
						if(file_exists($newResizeFileName) == false){
							$objImageManipulation->load($dbDocPathFull);
							$objImageManipulation->resize(730, 465);
							$objImageManipulation->save($newResizeFileName);
						}
						//*/
						/*	
						$id = "";
						$id = "divInner".$i;
						$border = "";
						if($i == 1){
							$border = "border: 2px solid;";
						}
							
						$strTrFile .= "<div id=".$id." style=\"text-align:center; ".$border." \">";
						if($dbDocType != "application/pdf"){
							if($strIframeFilePath == ""){
								 $strIframeFilePath = $dbDocPathFull;
								 $strPathForCanvasIframe = $newResizeFileNamePath;
								 $strDBDocPath = $newResizeFileNameForDB;
							}
							$strTrFile .= "<img src=\"$dbDocPathFull\" onClick=\"setIfamePath('$dbDocPathFull','".$id."','$newResizeFileNamePath', '$newResizeFileNameForDB')\" align=\"absmiddle\" class=\"imgTumbFixDimention\" \>";
						}
						$strTrFile .= "</div>";
						if($dbDocDate != "00-00-0000"){
							$strTrFile .= "<div id='dateDiv".$i."' class=\"text12\" style=\"text-align:center;  background-color:#f3f3f3;\">
												<span>".$dbDocDate."</span><span class=\"icon_delete\" onClick=\"deleteDoc('".$dbScanId."');\"></span>
										</div>
										";
						}
						*/
					}
					$i++;
				}
			}
			//mysql_free_result($rsGetData);
		}
		}//
		
		//get image from chart_drawing_templates
		$sql ="SELECT id, name FROM chart_drawing_templates WHERE delete_status='0' ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$name=$row["name"];
			$arrRet[]=array("","",$name,$row["id"]);
		}
		
		
		return $arrRet;
		//--
		
	}
	
	
	//get patient drawings for a DOS
	function getPtChartDrawings($patientid, $elem_formId){
		if(empty($patientid) || empty($elem_formId)){ return 0; }		
		
		$str_idoc_drawing_id="";
		$arr_flg_Prv_Drw=array();
		
		$arr_flg_Drw_grey = array(); //background color		
		
		//chart notes
		//include_once($GLOBALS['srcdir']."/classes/work_view/ChartNote.php");
		//EOM		
		//include_once($GLOBALS['srcdir']."/classes/work_view/EOM.php");	
		
		$oEOM = new EOM($patientid, $elem_formId);
		$sql = "SELECT idoc_drawing_id, statusElem FROM chart_eom WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0' ";
		$row=sqlQuery($sql);
		if($row != false){		
			if(!empty($row["idoc_drawing_id"])){
				$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";			
				
				if(strpos($row["statusElem"], "elem_chng_divEom3=1")===false){
					$arr_flg_Drw_grey[] = $row["idoc_drawing_id"];
				}				
				
			}
		}else{
			//			
			$tmp = " idoc_drawing_id ";
			$elem_dos=$oEOM->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $oEOM->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			if($row!=false){
				if(!empty($row["idoc_drawing_id"])){
					$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";			
				}
			}
		}		
		
		//External
		//include_once($GLOBALS['srcdir']."/classes/work_view/ExternalExam.php");
		$oEE = new ExternalExam($patientid, $elem_formId);
		$sql = "SELECT idoc_drawing_id, statusElem FROM chart_external_exam WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0'  ";
		$row=sqlQuery($sql);
		if($row != false){
			if(!empty($row["idoc_drawing_id"])){
				$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";			
				
				//elem_chng_divDraw_Od=1,elem_chng_divDraw_Os=1
				if(strpos($row["statusElem"], "elem_chng_divDraw_Od=1")===false && strpos($row["statusElem"], "elem_chng_divDraw_Os=1")===false){
					$arr_flg_Drw_grey[] = $row["idoc_drawing_id"];
				}
			}
		}else{
			//			
			$tmp = " idoc_drawing_id ";
			$elem_dos=$oEE->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $oEE->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			if($row!=false){
				if(!empty($row["idoc_drawing_id"])){
					$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";			
				}
			}
		}		
		
		//LA
		//include_once($GLOBALS['srcdir']."/classes/work_view/LA.php");
		$oLA = new LA($patientid, $elem_formId);
		list($tmp_idoc_drawing_id, $tmp_flg_Drw_grey) = $oLA->getIDocDrawId();		
		if(!empty($tmp_idoc_drawing_id)){	$str_idoc_drawing_id .= $tmp_idoc_drawing_id; }
		if(!empty($tmp_flg_Drw_grey)){	$arr_flg_Drw_grey[] = $tmp_flg_Drw_grey; }		
		
		//Gonio
		//include_once($GLOBALS['srcdir']."/classes/work_view/Gonio.php");
		$oGonio = new Gonio($patientid, $elem_formId);
		$sql = "SELECT idoc_drawing_id, statusElem FROM chart_gonio WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0'  ";
		$row=sqlQuery($sql);
		if($row != false){
			if(!empty($row["idoc_drawing_id"])){
				$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";		
				
				//elem_chng_divIop3_Od=1,elem_chng_divIop3_Os=1
				if(strpos($row["statusElem"], "elem_chng_divIop3_Od=1")===false && strpos($row["statusElem"], "elem_chng_divIop3_Os=1")===false){
					$arr_flg_Drw_grey[] = $row["idoc_drawing_id"];
				}
			}
		}else{
			//			
			$tmp = " idoc_drawing_id ";
			$elem_dos=$oGonio->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $oGonio->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			if($row!=false){
				if(!empty($row["idoc_drawing_id"])){
					$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";			
				}
			}
		}
		
		//SLE
		//include_once($GLOBALS['srcdir']."/classes/work_view/SLE.php");
		$oSLE = new SLE($patientid, $elem_formId);
		$sql = "SELECT idoc_drawing_id, statusElem FROM chart_drawings WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0' AND exam_name='SLE'  ";
		$row=sqlQuery($sql);
		if($row != false){
			if(!empty($row["idoc_drawing_id"])){
				$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";	
				
				//elem_chng_div6_Od=1,elem_chng_div6_Os=1
				if(strpos($row["statusElem"], "elem_chng_div6_Od=1")===false && strpos($row["statusElem"], "elem_chng_div6_Os=1")===false){
					$arr_flg_Drw_grey[] = $row["idoc_drawing_id"];
				}
			}
		}else{
			//			
			$tmp = " idoc_drawing_id ";
			$elem_dos=$oSLE->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $oSLE->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			if($row!=false){
				if(!empty($row["idoc_drawing_id"])){
					$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";			
				}
			}
		}
		
		//Fundus
		//include_once($GLOBALS['srcdir']."/classes/work_view/FundusExam.php");
		$oFundus = new FundusExam($patientid, $elem_formId);
		$sql = "SELECT idoc_drawing_id, statusElem FROM chart_drawings WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0' AND exam_name='FundusExam'  ";
		$row=sqlQuery($sql);
		if($row != false){
			if(!empty($row["idoc_drawing_id"])){
				$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";		
				
				//elem_chng_div5_Od=1,elem_chng_div5_Os=1
				if(strpos($row["statusElem"], "elem_chng_div5_Od=1")===false && strpos($row["statusElem"], "elem_chng_div5_Os=1")===false){
					$arr_flg_Drw_grey[] = $row["idoc_drawing_id"];
				}
			}
		}else{
			//			
			$tmp = " idoc_drawing_id ";
			$elem_dos=$oFundus->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $oFundus->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			if($row!=false){
				if(!empty($row["idoc_drawing_id"])){
					$str_idoc_drawing_id .= $row["idoc_drawing_id"].",";			
				}
			}
		}		
		
		//get Other Drawings : drawPane --
		
		
		
		
		//
		//return array($str_idoc_drawing_id);
		
		$idoc_drawing_id = explode(",", $str_idoc_drawing_id);
		if(count($idoc_drawing_id)>0){ //remove empty
			$idoc_drawing_id = array_values( array_filter($idoc_drawing_id) );
		}
		
		if(count($idoc_drawing_id)>0){	
			
			$strdrawIds = implode(",", $idoc_drawing_id);
		
			//*
			$dbField="modi_note_Draw";
			//if($examnm=="EOM"){
			//	$dbField="modi_note";
			//}
			
			$examnm=" 'EOM','IOP_GONIO','Fundus_Exam','EXTERNAL','SLE','LA','DrawPane' ";
			$arrIDocId = array();
			$strIDocId = "";
			$strIDocPath = $arrIDocPath_ID = array();
			$str_row_modify_Draw="";
			$qryGetIDocIdInMasetr = "select id,row_modify_by, DATE_FORMAT(row_modify_date_time,'%m-%d-%y %H:%i') as modidate,
								red_pixel, green_pixel, blue_pixel, drawing_images_data_points, drawing_image_path,
								DATE_FORMAT(row_modify_date_time,'%m-%d-%y') AS drawLMB,
								DATE_FORMAT(row_created_date_time,'%m-%d-%y') AS drawDate,
								DATE_FORMAT(row_visit_dos,'%m-%d-%y') AS drawDOS, patient_form_id 		
								from ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
								where (id IN (".$strdrawIds.") OR drawing_for='DrawPane') AND  	
								patient_id = '".$patientid."' AND deletedby ='0'  
								ORDER BY id DESC
								"; //and patient_form_id = '".$elem_formId."'
			$rsGetIDocIdInMasetr = sqlStatement($qryGetIDocIdInMasetr);		
			//return array($qryGetIDocIdInMasetr);
			
			//CHECK ARCHIVE DB TBL --
			if($rsGetIDocIdInMasetr){
				$tmp =imw_num_rows($rsGetIDocIdInMasetr);
				if($tmp==0 && !empty($GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"])){
					$qryGetIDocIdInMasetr = "select id,row_modify_by, DATE_FORMAT(row_modify_date_time,'%m-%d-%y %H:%i') as modidate,
										red_pixel, green_pixel, blue_pixel, drawing_images_data_points, drawing_image_path,
										DATE_FORMAT(row_modify_date_time,'%m-%d-%y') AS drawLMB,
										DATE_FORMAT(row_created_date_time,'%m-%d-%y') AS drawDate,
										DATE_FORMAT(row_visit_dos,'%m-%d-%y') AS drawDOS , patient_form_id  		
									from ".$GLOBALS["IMEDIC_SCAN_DB_ARCHIVE"].".idoc_drawing 
									where (id IN (".$strdrawIds.") OR drawing_for='DrawPane' ) AND 
									patient_id = '".$patientid."' AND deletedby ='0'  
									ORDER BY id DESC
									"; //and patient_form_id = '".$elem_formId."'
					$rsGetIDocIdInMasetr = sqlStatement($qryGetIDocIdInMasetr);
				}
			}
			//CHECK ARCHIVE DB TBL --		
			
			if(imw_num_rows($rsGetIDocIdInMasetr) > 0){
				while($rowGetDrawing = sqlFetchArray($rsGetIDocIdInMasetr)){
					$arrIDocId[] = $rowGetDrawing["id"];
					
					//GEt Drawing Modified Date
					if(!empty($recordModiDateDrawing) && $recordModiDateDrawing==$rowGetDrawing["id"]&&!empty($rowGetDrawing["row_modify_by"])){
						$str_row_modify_Draw=getModiNotes_drawing($rowGetDrawing["row_modify_by"],$rowGetDrawing["modidate"],$dbField);
						$str_row_modify_Draw=", ".$str_row_modify_Draw;								
					}
					
					//--
					//Check Drawing
					$strRED = $strGreen = $strBlue = $strDrawingImagesDataPoints = "";
					if(!empty($rowGetDrawing["red_pixel"])){ $strRED = str_replace('0,','',$rowGetDrawing["red_pixel"]);}
					if(!empty($rowGetDrawing["green_pixel"])){ $strGreen = str_replace('0,','',$rowGetDrawing["green_pixel"]);}
					if(!empty($rowGetDrawing["blue_pixel"])){ $strBlue = str_replace('0,','',$rowGetDrawing["blue_pixel"]);}
					$strDrawingImagesDataPoints = $rowGetDrawing["drawing_images_data_points"];
					$drawing_image_path=$rowGetDrawing["drawing_image_path"];
					
					if(((empty($strRED) == false) || (empty($strGreen) == false) || (empty($strBlue) == false)) 
						|| (empty($strDrawingImagesDataPoints) == false&&$strDrawingImagesDataPoints!="$~$") 
						|| !empty($flgGetDraw) 
						|| !empty($drawing_image_path)
						){ //	 			
						//
						$oSaveFile = new SaveFile();
						$path = $oSaveFile->getUploadDirPath(1);
						
						$strImagePath = $rowGetDrawing["drawing_image_path"];
						$strImagePath = str_replace("/", "\\", $strImagePath);
						$strImagePathReal = $path.$strImagePath;
						$strImagePathReal = str_ireplace("\\", "/", $strImagePathReal);
						if(file_exists($strImagePathReal)){	
						
							//*thumb
							$tmp = str_replace(".png","_b.png", $drawing_image_path);
							$tmp = $path.$tmp;
							if(file_exists($tmp)){
								$strImagePathReal=$tmp;
							}						
							//*-/
						
							/*
							if($flgMani == "1"){
								$strIDocPath[] = $strImagePathReal;
								
							}else if($flgMani == "2"){ //return array with path and date
							*/
								$returnStrImagePath = $strImagePathReal;
								//$drawDate = $rowGetDrawing["drawDate"];
								$drawDate = $rowGetDrawing["drawDOS"];
								
								$strIDocPath[] = array($returnStrImagePath,$drawDate,$rowGetDrawing["row_modify_by"],$rowGetDrawing["drawLMB"]);
							/*	
							}else{
								$strIDocPath[] = $strImagePathReal;
							}
							*/
							
							$arrIDocPath_ID[] = $rowGetDrawing["id"];
							$arr_flg_Prv_Drw[] = ((!empty($rowGetDrawing["patient_form_id"]) && $rowGetDrawing["patient_form_id"] != $elem_formId) || in_array($rowGetDrawing["id"], $arr_flg_Drw_grey)) ? 1 : 0;
							
						}
					}			
					
					//--
				}
			}
			if(count($arrIDocId) > 0){
				$strIDocId = implode(",", $arrIDocId);
			}
		
		}//
		else{
			$strIDocId=$str_row_modify_Draw= $strIDocPath="";
			//"";
		}
		
		return array($strIDocId, $str_row_modify_Draw, $strIDocPath, $arrIDocPath_ID, $arr_flg_Prv_Drw);
		//*/
	}	
	
	function checkDrawing4ExamNCarryFwd($idocid, $patientid, $formid){
		$masterId=0;
		$chart_exam = "";
		$idocid_new = "";
		//check if docid belong to current formId or not	
		$sql = "select id, drawing_image_path as drawingImgPath, drawing_for, patient_form_id, patient_id   from ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
					where id = '".$idocid."' AND patient_id='".$patientid."'  ";
		$row=sqlQuery($sql);
		if($row != false){
			$tmp_idocid = $row["id"];
			$tmp_patient_form_id = $row["patient_form_id"];
			$tmp_patient_id = $row["patient_id"];
			$tmp_drawing_for = $row["drawing_for"];
			
			//check by maching formid 
			if($formid==$tmp_patient_form_id){
				//Do Nothing and drwing belongs current dos			
			}else if(!empty($tmp_drawing_for)){
				//drawing do not belong to current visit so check if drawing belong to any chart note exam, if yes then check if exam exists, else carried forward that exam
				if($tmp_drawing_for == "EOM"){
					//EOM		
					//include_once($GLOBALS['srcdir']."/classes/work_view/EOM.php");			
					$oEOM = new EOM($patientid, $formid);
					if(!$oEOM->isRecordExists()){ $oEOM->carryForward(); }
					//get Exam ids
					$sql = "SELECT eom_id FROM chart_eom WHERE patient_id = '".$patientid."' AND form_id='".$formid."' AND purged='0' ";
					$row=sqlQuery($sql);
					if($row!=false){
						$masterId = $row["eom_id"];
						$chart_exam=$tmp_drawing_for;
					}
				}else if($tmp_drawing_for == "EXTERNAL"){
					//include_once($GLOBALS['srcdir']."/classes/work_view/ExternalExam.php");
					$oEE=new ExternalExam($patientid, $formid);
					if(!$oEE->isRecordExists()){	$oEE->carryForward();	}
					//get Exam ids
					$sql = "SELECT ee_id FROM chart_external_exam WHERE patient_id = '".$patientid."' AND form_id='".$formid."' AND purged='0' ";
					$row=sqlQuery($sql);
					if($row!=false){
						$masterId = $row["ee_id"];
						$chart_exam=$tmp_drawing_for;
					}
				}else if($tmp_drawing_for == "LA"){
					//include_once($GLOBALS['srcdir']."/classes/work_view/LA.php");
					$oLA=new LA($patientid, $formid);
					if(!$oLA->isRecordExists()){	$oLA->carryForward(); 	}
					//get Exam ids
					$sql = "SELECT id as la_id FROM chart_drawings WHERE patient_id = '".$patientid."' AND form_id='".$formid."' AND purged='0' AND exam_name='LA' ";
					$row=sqlQuery($sql);
					if($row!=false){
						$masterId = $row["la_id"];
						$chart_exam=$tmp_drawing_for;
					}
				}else if($tmp_drawing_for == "IOP_GONIO"){
					//include_once($GLOBALS['srcdir']."/classes/work_view/Gonio.php");
					$oGonio=new Gonio($patientid, $formid);
					if(!$oGonio->isRecordExists()){	$oGonio->carryForward();	}
					//get Exam ids
					$sql = "SELECT gonio_id FROM chart_gonio WHERE patient_id = '".$patientid."' AND form_id='".$formid."' AND purged='0' ";
					$row=sqlQuery($sql);
					if($row!=false){
						$masterId = $row["gonio_id"];
						$chart_exam=$tmp_drawing_for;
					}
				}else if($tmp_drawing_for == "SLE"){
					//include_once($GLOBALS['srcdir']."/classes/work_view/SLE.php");
					$oSLE=new SLE($patientid, $formid);
					if(!$oSLE->isRecordExists()){	$oSLE->carryForward();	}
					//get Exam ids
					$sql = "SELECT id as sle_id FROM chart_drawings WHERE patient_id = '".$patientid."' AND form_id='".$formid."' AND purged='0' AND exam_name='SLE' ";
					$row=sqlQuery($sql);
					if($row!=false){
						$masterId = $row["sle_id"];
						$chart_exam=$tmp_drawing_for;
					}
				}else if($tmp_drawing_for == "Fundus_Exam"){
					//include_once($GLOBALS['srcdir']."/classes/work_view/FundusExam.php");
					//include_once($GLOBALS['srcdir']."/classes/work_view/OpticNerve.php");
					$oFE=new FundusExam($patientid, $formid);
					if(!$oFE->isRecordExists()){	$oFE->carryForward();	}
					$oON=new OpticNerve($patientid, $formid);
					if(!$oON->isRecordExists()){	$oON->carryForward();	}
					//get Exam ids
					$sql = "SELECT id as rv_id FROM chart_drawings WHERE patient_id = '".$patientid."' AND form_id='".$formid."' AND purged='0' and exam_name='FundusExam' ";
					$row=sqlQuery($sql);
					if($row!=false){
						$masterId = $row["rv_id"];
						$chart_exam=$tmp_drawing_for;
					}					
				}else{
					//
					$masterId = 0;
					$chart_exam="DrawPane";
				}
			}		
		}		
		
		//get new drawing id  of currently edit drawing after carried forwd  above
		if(!empty($masterId) && !empty($chart_exam)){
			$sql = "select id, drawing_image_path as drawingImgPath, drawing_for, patient_form_id, patient_id   from ".constant("IMEDIC_SCAN_DB").".idoc_drawing 
					where  patient_id='".$patientid."' AND drawing_for_master_id='".$masterId."' AND drawing_for='".$chart_exam."' AND carryfwd_id='".$idocid."' AND deletedby='0'  ";
			$row=sqlQuery($sql);
			if($row != false){
				$idocid_new = $row["id"];
			}
		}
		
		return array($chart_exam, $masterId, $idocid_new);
		
	}
	
	
	function drwico_getDrwIcon(){

		//
		$oSaveFile = new SaveFile();
		$path_w = $oSaveFile->getUploadDirPath(1);
		$path = $oSaveFile->getUploadDirPath();

		$uploaddir_w = $path_w."/drawicon";
		$uploaddir_inc = $path."/drawicon";
		$uploaddir_inc_l = $uploaddir_inc."/L";		
		
		$str="";
		$arrDrwIcon = array();
		$sql = "SELECT * FROM chart_drawicon ORDER BY id, drwico_name ";
		$rez = sqlStatement($sql);
		
		$flgDiv=0;
		$flgDivOpen=0;
		for($i=0; $row = sqlFetchArray($rez); $i++){
			$name = $nameVar = $row["drwico_name"];
			$path = $row["drwico_path"];
			$symptom = $row["drwico_symptom"];			
			if(!file_exists($uploaddir_inc.$path) || !file_exists($uploaddir_inc_l.$path)){continue;}
			
			$arrDrwIcon[$name]["name"] = $name;
			$arrDrwIcon[$name]["symptom"] = !empty($symptom) ? $symptom : "" ;
			$arrDrwIcon[$name]["path"] = $uploaddir_w.$path;			
			
			//--
			if($flgDivOpen==0){ $str .= "<div>";}
			$flgDivOpen+=1;
			
			$strclick=" onClick=\"setEvent('funDrawImage', '".$nameVar."', '0', this);\" ";
			//if(isset($GLOBALS['New_Drawing_Demo'])&&$GLOBALS['New_Drawing_Demo']==1){
				$strclick="  ";
			//}
			
			$str .= "<span class=\"toolIcon tool right_tools ".$nameVar."T\" style=\"background:url('".$arrDrwIcon[$name]["path"]."'); \" title=\"".$name."\" ".$strclick." ></span> ";//
			
			if($flgDivOpen==2){ $str .= "</div>";$flgDivOpen=0; }
			
			//--
			
		}
		
		if($flgDivOpen>0 && !empty($str)){
			$str .= "</div>";
		}
		
		//echo $str;		
		
		return array($arrDrwIcon, $str);
	}	
	
	function deleteNoSavedDrwing($arin, $ardwids){
		
		$patientid=$arin[0];
		$elem_formId=$arin[1];
		$mstrid= $arin[2];
		$examnm = $arin[3];
		if(count($ardwids)>0){
			$strids = implode(",", $ardwids);			
			$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing
						SET					
						deletedby='".$_SESSION["authId"]."'
						WHERE patient_id='".$patientid."'
						AND patient_form_id='".$elem_formId."'
						AND drawing_for_master_id='".$mstrid."'
						AND LOWER(drawing_for)=LOWER('".$examnm."')
						AND id NOT IN (".$strids.")
						";
			$res=sqlQuery($sql);		
		}
	}
	
	//Function check applet changes
	function isAppletModified($sign,$bag="0")
	{
		$sign = trim($sign);

		if(empty($sign)){
			$ret = false;
		}else if(!empty($sign) && ((strpos($sign, "/") !== false) ||
			(strpos($sign, "-") === false) || (strpos($sign, ":") === false) || (strpos($sign, ";") === false)) ){
			// for new applet image string, return true
			$ret = true;
		}else{
			$signLength = strlen($sign);
			$bag = ($bag == "0") ? "0-:;" : $bag;
			$ret = false;
			for($i=0;$i<$signLength;$i++)
			{
				$signChar =  substr($sign,$i,1);

				$pos = strpos($bag, $signChar);
				if($pos === false)
				{
					//return true;
					$ret = true;
					break;
				}
			}
		}

		return $ret;
	}
	
	function getModiNotes_drawing($uid,$dt,$dbfield){
		$ouser = new User($uid);		
		$str_row_modify_Draw="<div>Drawing modified by ".$ouser->getName(1)." on ".$dt."</div>";
		$tmp=" $dbfield = CONCAT('".sqlEscStr($str_row_modify_Draw)."',$dbfield) ";
		return $tmp;
	}
	
	function load_drawing_data(){
		$arrRet=array();
		$dbIdocDrawingId = trim($_REQUEST["id"]);

		$fid=$_REQUEST["fid"]; 
		$pid=$_REQUEST["pid"]; 
		$eid=$_REQUEST["eid"]; 
		$enm=$_REQUEST["enm"];

		if(!empty($dbIdocDrawingId)){
			$arr_dbIdocDrawingId = explode(",",$dbIdocDrawingId);
		}
		
		if(is_array($arr_dbIdocDrawingId) && count($arr_dbIdocDrawingId)>0){	
	
			//$OBJDrawingData = new CLSDrawingData();	
			
			foreach($arr_dbIdocDrawingId as $key=>$val){
			
			$dbIdocDrawingId = 0;
			$dbIdocDrawingId = $val;

			if((int)$dbIdocDrawingId > 0){
				
				$strCanvasWNL = "yes";
				$dbRedPixel = $dbGreenPixel = $dbBluePixel = $dbAlphaPixel = $dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $canvasDataFileNameDB = $dbCanvasImageDataPoint = $imgDB = $drwNE = $drw_data_json = "";
				list($dbRedPixel, $dbGreenPixel, $dbBluePixel, $dbAlphaPixel, $dbTollImage, $dbPatTestName, $dbPatTestId, $dbTestImg, $canvasDataFileNameDB, $dbCanvasImageDataPoint, $imgDB, $drwNE, $drw_data_json) = $this->getHTMLDrawingData($dbIdocDrawingId);

				$dbRedPixelWNL = $dbGreenPixelWNL = $dbBluePixelWNL = $dbAlphaPixelWNL = "";
				if(empty($dbRedPixel) == false){
					$dbRedPixelWNL = str_replace('0,', '', $dbRedPixel);
				}
				if(empty($dbGreenPixel) == false){
					$dbGreenPixelWNL = str_replace('0,', '', $dbGreenPixel);
				}
				if(empty($dbBluePixel) == false){
					$dbBluePixelWNL = str_replace('0,', '', $dbBluePixel);
				}
				if(empty($dbAlphaPixel) == false){
					$dbAlphaPixelWNL = str_replace('0,', '', $dbAlphaPixel);
				}
				$drw_data_json_t=($drw_data_json=='{"objects":[],"background":""}') ? "" : "".$drw_data_json; 
				if(((empty($dbRedPixelWNL) == false) && (empty($dbGreenPixelWNL) == false) && (empty($dbBluePixelWNL) == false) && (empty($dbAlphaPixelWNL) == false)) || (empty($dbCanvasImageDataPoint) == false&&$dbCanvasImageDataPoint!="$~$")||!empty($canvasDataFileNameDB)||!empty($drw_data_json_t)){
					$strCanvasWNL = "no";
				}
				
				//UploadDir
				$oSaveFile = new SaveFile();
				$path = $oSaveFile->getUploadDirPath();
				$path_w = $oSaveFile->getUploadDirPath(1);
				
				
				//
				if(!empty($canvasDataFileNameDB)){
					$canvasDataFileNameDB_fullpath = $path.$canvasDataFileNameDB;			
					if(file_exists($canvasDataFileNameDB_fullpath)){
						
						// copy file --
							if(empty($drw_data_json)){
								$canvasDataFileNameDB_dup = str_replace(array(".png",".jpg"),array("_dup.png","_dup.jpg"), $canvasDataFileNameDB);
								$canvasDataFileNameDB_dup_fullpath = $path.$canvasDataFileNameDB_dup;
								if(!file_exists($canvasDataFileNameDB_dup_fullpath)){
									if(copy($canvasDataFileNameDB_fullpath,$canvasDataFileNameDB_dup_fullpath)){							
										$canvasDataFileNameDB = $canvasDataFileNameDB_dup;
										
									}else{
										//pending to do
									}
								}else{						
									$canvasDataFileNameDB = $canvasDataFileNameDB_dup;
								}
							}
						//--
						
						//$canvasDataFileNameDB=checkUrl4Remote($path_w.$canvasDataFileNameDB);
						$canvasDataFileNameDB=$path_w.$canvasDataFileNameDB;
					}else{
						$canvasDataFileNameDB="";
					}
					
					
					//Move file to remote server to load in canvas --			
					if($zOnParentServer==1){				
						/*
						if(file_exists($canvasDataFileNameDB_fullpath)){
							$strImgBinary4Remote = file_get_contents($canvasDataFileNameDB_fullpath); //"FILE EXISTS:".$canvasDataFileNameDB_fullpath;//					
						}else{
							$strImgBinary4Remote = "NO FILE at ".$canvasDataFileNameDB_fullpath;
						}
						*/
						/*
						include(dirname(__FILE__)."/../../../remote_sync/classes/ChartNoteRemoteHandler.php");	
						$oChartNoteRemoteHandler = new ChartNoteRemoteHandler();
						$oServer = $oChartNoteRemoteHandler->getServerInfo($GLOBALS['LOCAL_SERVER']);
						$srcUri = $canvasDataFileNameDB;
						$desUri = $oServer["url"]."/remote_sync/tmp/".basename($srcUri);
						$flgDone = $oChartNoteRemoteHandler->copyFile($srcUri,$desUri);
						if(empty($flgDone)){	$canvasDataFileNameDB="";}else{$canvasDataFileNameDB=$desUri;}				
						*/
						
					}
					//Move file to remote server to load in canvas --			
					
				}		
				//$desUri = print_r($GLOBALS,1);
				$arrRet[] = $dbRedPixel."`~`!@`~`".$dbGreenPixel."`~`!@`~`".$dbBluePixel."`~`!@`~`".$dbAlphaPixel."`~`!@`~`".$dbTollImage."`~`!@`~`".$dbPatTestName."`~`!@`~`".$dbPatTestId."`~`!@`~`".$dbTestImg."`~`!@`~`".$canvasDataFileNameDB."`~`!@`~`".$dbCanvasImageDataPoint."`~`!@`~`".$imgDB."`~`!@`~`".$strCanvasWNL."`~`!@`~`".$dbIdocDrawingId."`~`!@`~`".$drwNE."`~`!@`~`".$strImgBinary4Remote."`~`!@`~`".$drw_data_json; 	//."`~`!@`~`".$srcUri."`~`!@`~`".$desUri
			}
			
			}
		}

		//Drawing templates
		$arrRetTempates=array();
		//$OBJDrawingData2 = new CLSDrawingData();	
		$arrRetTempates=$this->getDrawingTemplates($pid,$fid,$eid,$enm);	


		$arrRet_Bg =array("drw"=>$arrRet,"drwtemp"=>$arrRetTempates);
		echo json_encode($arrRet_Bg);
		
	}
}
?>