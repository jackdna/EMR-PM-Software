<?php
//OperativeNote.php

class OperativeNote {
	private $pid, $fid;
	
	public function __construct($pid="",$fid=""){
		$this->pid = $pid;
		$this->fid = $fid;
		
		
	}
	
	function modifyImgPath4pdf($str){
		$chkstr = '"'.$GLOBALS['rootdir'];
		$repstr = '"'.$GLOBALS['incdir'];
		if(strpos($str, $chkstr)!==false){
			$str = str_replace($chkstr,$repstr,$str);
		}
		return $str;
	}
	function imageResizeSc($width, $height, $target) {
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}
		$width = round($width * $percentage);
		$height = round($height * $percentage);
		return "width=\"$width\" height=\"$height\"";
	}	
	
	function getThisPatientOPnoteMedia(){
		$media = false;
		$media_res = imw_query("SELECT id,source_id FROM ".constant('IMEDIC_SCAN_DB').".media WHERE source='opnote' AND patient_id='".$this->pid."' AND deleted=0");	
		if($media_res && imw_num_rows($media_res)>0){
			$media = array();
			while($rs = imw_fetch_assoc($media_res)){
				$source_id 		= $rs['source_id'];
				$media[$source_id] = $rs['id'];
			}
		}
		return $media;
	}
	
	function getTreeStruct($arrTemp,$flgIco=0){
		$oPnTmp = new PnTemplate;
		$divNaviProgNts = "";
		$mediaOPnote_arr = $this->getThisPatientOPnoteMedia();
		$indx=1;
		foreach($arrTemp as $key => $val){
			$tDate = $key;
			if(count($val) > 0){
				$divNaviProgNts_li = "";
				foreach($val as $key2 => $val2){
					$pnId = $val2[0];
					$tId = $val2[1];
					$opid = $val2[2];
					$tTm = trim($val2[3]);
					$tNameScEMR = trim($val2[4]);
					$tIdScEMR = $val2[5];
					$opNm = "";
					if(!empty($opid)){
						$ousr = new User($opid);
						$arrNm = $ousr->getName("2");					
						$opNm = $arrNm[1]; 
					}
					
					if($tTm != "00:00AM" && $tTm != "00:00PM"){
						$tTm = "($tTm $opNm)";
					}else{
						$tTm = "";
					}
					
					$tName = $oPnTmp->getTempName($tId);
					if(!trim($tName)) { $tName = trim($tNameScEMR); }
					
					if(isset($mediaOPnote_arr[$pnId]) && $mediaOPnote_arr[$pnId] > 0){
						$media_id = $mediaOPnote_arr[$pnId];
						$bull_logo_img = '<span class="glyphicon glyphicon-facetime-video"></span>';
					}else{
						$media_id = 0;						
						$bull_logo_img = '<span class="glyphicon glyphicon-file"></span>';
					}
					
					
					
					$divNaviProgNts_li.="<li class=\"list-group-item\">";
					$divNaviProgNts_li.= "<a href=\"javascript:void(0);\" onclick=\"opnt_showFile('".$pnId."','".$media_id."');\">".$bull_logo_img." ".$tName." ".trim($val2[3])."</a> ";
					
					//test
					if($flgIco == 1){
						$divNaviProgNts_li.= "<a href=\"javascript:void(0);\" onclick=\"opnt_actPnRec('".base64_encode($pnId)."')\"><span class=\"glyphicon glyphicon-share-alt\"  title=\"Restore\"></span></a>";
					}else{
						$divNaviProgNts_li.= "<a href=\"javascript:void(0);\" onclick=\"opnt_delPnRec('".base64_encode($pnId)."')\"><span class=\"glyphicon glyphicon-remove\"  title=\"Delete\"></span></a>";
					}
					//--
					
					$divNaviProgNts_li.="</li>";
					
				}
				
				//--
				if(!empty($divNaviProgNts_li)){
				$divNaviProgNts .= '<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse'.$flgIco.$indx.'">'.$tDate.'</a>
									</h4>
								</div>
								<div id="collapse'.$flgIco.$indx.'" class="panel-collapse collapse">
									<ul class="list-group">
									'.$divNaviProgNts_li.'
									</ul>
								</div>
							</div>
						</div>';
				}		
				//--	
				$indx+=1;				
			}
		}
		return $divNaviProgNts;
	}
	
	
	function load_pt_op_notes(){
	
		//Initialize
		$OBJsmart_tags = new SmartTags;
		$OBJCommonFunction = new CLSCommonFunction;		
		$oPnTmp = new PnTemplate;
		$oPnRep = new PnReports;
		$objParser = new PnTempParser;
		
		//Move to trash etc --
		$delId = $_GET["elem_delId"];
		$op = $_GET["op"];
		if(!empty($op) && !empty($delId)){
			if($op=="dlRprt"){
				if(!empty($_GET["elem_delId"])){
					$delId = base64_decode($_GET["elem_delId"]);
					//delete
					$err = $oPnRep->deleteRecord($delId);
				}
			}else if($op=="ActivateReport"){			
				if(!empty($_GET["elem_delId"])){
					$delId = base64_decode($_GET["elem_delId"]);
					//delete
					$err = $oPnRep->activateRecord($delId);
				}
			}			
		}
		//End Move to trash etc --			
		
		//Patient Id
		$patient_id = $this->pid;
		$opt = new Patient($patient_id);
		$ptName_Id = $opt->getName();
		
		//Todate
		$toDate = date("Y-m-d H:i:s");
		
		//Form ID
		$formId = $this->fid;
		
		if(empty($formId)){ exit("Error01"); } //Please open patient chart note.
		
		//
		$browserIpad = 'no';
		if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
			$browserIpad = 'yes';
		}
		
	
		if($_GET["show_all"]){
			
			//$this->load_pt_consult();
			//Div data Temp ---------
			$divNaviProgNts = "";
			list($arrTemp,$arrTrash) = $oPnRep->getPtReports($patient_id);
			
			//Add Active Folder Here --
			if(count($arrTemp) > 0){
				$divNaviProgNts .= $this->getTreeStruct($arrTemp);
			}
			//Add Active Folder Here --
			
			//Add Trash Folder Here --
			if(count($arrTrash) > 0){
			$divNaviProgNts_trash = $this->getTreeStruct($arrTrash,1);			
			$str_trash='<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse_trash">Trash</a>
									</h4>
								</div>
								<div id="collapse_trash" class="panel-collapse collapse">
									<div class="panel-body">'.$divNaviProgNts_trash.'</div>
								</div>
							</div>
						</div>';
			$divNaviProgNts .= $str_trash;
			}
			//Add Trash Folder Here --
			
			$html_left_pane=$divNaviProgNts;
			
			// if move to trash then update left pan only
			if(!empty($op) && !empty($delId)){//
				echo $html_left_pane;
				exit();
			}
			
			
			include($GLOBALS['fileroot']."/interface/chart_notes/view/pt_op_letter.php");			
			
		}else{	//operative note form
		
		
			$elem_edit_mode="new";
			$elem_edit_id="0";
			
			$status="";			
			$tempId="";
			
			
			//Get Select of template --------
			$arrTmp = $oPnTmp->getProgNtsInfo();
			$strSelect = "<select name=\"elem_tempId\" id=\"elem_tempId\" class=\"form-control minimal\" onchange=\"opnt_loadTemp(this)\">".
						 "<option value=\"0\"></option>";
			if(count($arrTmp) > 0){
				foreach($arrTmp as $key => $val){		
					
					$tId = $val["id"];
					$tNm = $val["name"];		
					$sel = ($tempId == $tId ) ? "selected" : "" ;
					
					$strSelect .= "<option value=\"".$tId."\" ".$sel.">".$tNm."</option>";
				}
			}

			$strSelect .= "</select>";
			//Get Select of template --------
			
			$ouser=new User();
			$select_usr_dropdown = $ouser->getUsersDropDown('hold_to_physician');
			
			///-- //action="pn_save.php"
			include($GLOBALS['fileroot']."/interface/chart_notes/view/op_note.php");
			//---
		
		}//
		
	}
	
	function saveOpNote(){
		$OBJhold_sign = new CLSHoldDocument;
		$oPnTs = new PnTemplate();
		$oPnRep = new PnReports();

		$elem_edit_mode = $_POST["elem_edit_mode"];
		$elem_edit_id = $_POST["elem_edit_id"];
		$elem_patient_id = $_POST["elem_patient_id"];
		$elem_form_id = $_POST["elem_form_id"];
		$elem_status = $_POST["elem_status"];
		$elem_pnData = $_POST["elem_pnData"];
		$elem_signCoords = $_POST["elem_signCoords"];
		$elem_date = $_POST["elem_date"];
		$elem_tempId = $_POST["elem_tempId"];
		$elem_site = $_POST["elem_site"];
		
		if($elem_edit_mode == "delete"){
			
		}else{
			$data = array();
			$data["editId"] = $elem_edit_id;
			$data["ptId"] = $elem_patient_id;
			$data["formId"] = $elem_form_id;
			$data["status"] = !empty($elem_status) ? $elem_status : 0 ;			
		
			$data["txtData"] = $elem_pnData;
			$data["sign"] = $elem_signCoords;
			$data["date"] = $elem_date;
			$data["tempId"] = $elem_tempId;
			$data["opid"] = $_SESSION["authId"];
			
			//Save			
			if($elem_edit_mode == "new" && !$elem_edit_id || $elem_edit_id==0){
				//Insert
				$elem_edit_id = $oPnRep->insertRecord($data);
				/*
				if(isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])){
					$tmpSESSarr = unserialize($_SESSION["test2edit"]);
					$tmpSESSarr['opnote'] = '';
					unset($tmpSESSarr['opnote']);
					$_SESSION["test2edit"] = serialize($tmpSESSarr);
				}
				*/
			}else{
				//Update
				$err = $oPnRep->updateRecord($elem_edit_id, $data);
				/*
				if(isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])){
					$tmpSESSarr = unserialize($_SESSION["test2edit"]);
					$tmpSESSarr['opnote'] = '';
					unset($tmpSESSarr['opnote']);
					$_SESSION["test2edit"] = serialize($tmpSESSarr);
				}*/
				
			}
		}
		/*-----SAVING HOLD SIGNATURE INFO-----*/
		 $OBJhold_sign->section_col = "opnote_id";
		 $OBJhold_sign->section_col_value = $elem_edit_id;
		 $OBJhold_sign->save_hold_sign();
		 /*-----end of HOLD SIGNATURE INFO-----------*/
		
		echo "0";	
	}
	
	function load_file(){		
		if((!isset($_GET["elem_pnRepId"]) || empty($_GET["elem_pnRepId"]) )){
			echo("no report id selected .");
			$flgStoExec = 1;	
		}
	
		if(!isset($flgStoExec) || empty($flgStoExec)){ //$flgStoExec
			//obj Template
			$objPnTemp = new PnTemplate;
			//$objParser = new PnTempParser;
			$oPnRep = new PnReports;			
			
			$elem_edit_id = $_GET["elem_pnRepId"];
			$arrTemp = $oPnRep->getRecordInfo($elem_edit_id);
			
			/****Getting media if available****/
			$media_available=true;
			$media_res = imw_query("SELECT * FROM ".constant('IMEDIC_SCAN_DB').".media WHERE source='opnote' AND source_id='".$elem_edit_id."' AND deleted=0");
			if($media_res && imw_num_rows($media_res)>0){
				$media_available=true;
			}
			/**********************************/
			
			if($arrTemp != false){
	
				$tempId = $arrTemp["tempId"];
				$status = $arrTemp["status"];
				$sc_emr_template_name = trim($arrTemp["sc_emr_template_name"]);
				$sc_emr_operative_report_id = $arrTemp["sc_emr_operative_report_id"];
				$elem_pnData = $arrTemp["txt_data"]; //Template data
				$sc_emr_iasc_appt_id = $arrTemp["sc_emr_iasc_appt_id"];
				
				/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
					//REPLACEING UNCHANGES SMART TAGS WITH NULL
				/*	$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
					if($arr_smartTags){
						foreach($arr_smartTags as $key=>$val){
						  $regpattern=">$val<"; 
						  $elem_pnData = str_ireplace($regpattern, '><', $elem_pnData);
						}	
					}
				*/	//REPLACING SMART TAG OPTONS WITH NON-ANCHOR STRING.
					$regpattern='|<a class="cls_smart_tags_link" href=(.*) id=(.*)>(.*)</a>|U'; 
					$elem_pnData = preg_replace($regpattern, "\\3", $elem_pnData);
					
				/*--SMART TAG REPLACEMENT END--*/
				
				
				/* Change image path to include full path for pdf creation */
				$elem_pnData = $this->modifyImgPath4pdf($elem_pnData);
				//-- --//
				
				$strpixls = $arrTemp["signature"];
				
				if(!empty($tempId)){
					$arrTemp = $objPnTemp->getRecordInfo($tempId);
					$templateName = trim($arrTemp[1]); //Template Name					
				}
			}
			$htmlFolder = "new_html2pdf";
			$htmlFilePth = "new_html2pdf/createPdf.php";
			
			//start code to get opnotes related to scEMR
			if($sc_emr_template_name || $sc_emr_operative_report_id) {
				//$htmlFolder = "html2pdf";
				//$htmlFilePth = "html2pdf/index.php";
				if(constant("IMEDIC_SC")!='' && $sc_emr_operative_report_id) {
					/**
					$scemr_fac_id = "";
					if(trim($sc_emr_iasc_appt_id)) {
						$fac_id_qry = "SELECT sa_facility_id FROM schedule_appointments WHERE id = '".trim($sc_emr_iasc_appt_id)."' LIMIT 0,1";
						$fac_id_res = imw_query($fac_id_qry) or die(imw_error());
						if(imw_num_rows($fac_id_res)>0) {
							$fac_id_row = imw_fetch_assoc($fac_id_res);
							$scemr_fac_id = $fac_id_row["sa_facility_id"];	
						}
					}
					include(dirname(__FILE__)."/../../accounting/connect_sc.php");
					//START IOL SCAN UPLOAD IMAGE
					$ViewOpRoomRecordQry = "SELECT oprm.operatingRoomRecordsId, oprm.iol_ScanUpload, oprm.iol_ScanUpload2 
											FROM ".$dbname.".operativereport oprp 
											INNER JOIN ".$dbname.".operatingroomrecords oprm ON (oprm.confirmation_id = oprp.confirmation_id)
											WHERE  oprp.oprativeReportId = '".$sc_emr_operative_report_id."'
											";
					$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die(imw_error()); 
					$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
					$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes);
					
					$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
					$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
					$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
					**/
					/**
					if($ViewOpRoomRecordNumRow>0) {
						if($iol_ScanUpload!='' || $iol_ScanUpload2!=''){
							$elem_pnData.='<table width="100%"><tr><td class="text_10"><strong>IOL Scanned Image</strong> </td></tr>';
						}
						
						if($iol_ScanUpload!=''){
							$file=fopen($GLOBALS['incdir'].'/common/'.$htmlFolder.'/oproom.jpg','w+');
							fputs($file,$iol_ScanUpload);
							$newSize=' height="100"';
							$priImageSize=array();
							if(file_exists($GLOBALS['incdir'].'/common/'.$htmlFolder.'/oproom.jpg')) {
								$priImageSize = getimagesize($GLOBALS['incdir'].'/common/'.$htmlFolder.'/oproom.jpg');
								if($priImageSize[0] > 395 && $priImageSize[1] < 840){
									$newSize = imageResizeSc(680,400,500);						
									$priImageSize[0] = 500;
								}					
								elseif($priImageSize[1] > 840){
									$newSize = imageResizeSc($priImageSize[0],$priImageSize[1],600);						
									$priImageSize[1] = 600;
								}
								else{					
									$newSize = $priImageSize[3];
								}							
								if($priImageSize[1] > 800 ){					
									echo '<newpage>';												
								}
							}
							$elem_pnData.='<tr><td class="text_10"><img src="oproom.jpg" '.$newSize.'></td></tr>';
						}
					
						if($iol_ScanUpload2!=''){
							$file=fopen($GLOBALS['incdir'].'/common/'.$htmlFolder.'/oproom1.jpg','w+');
							fputs($file,$iol_ScanUpload2);
							$priImageSize=array();
							if(file_exists($GLOBALS['incdir'].'/common/'.$htmlFolder.'/oproom1.jpg')) {
								$priImageSize = getimagesize($GLOBALS['incdir'].'/common/'.$htmlFolder.'/oproom1.jpg');
								$newSize = 'height="100"';
								if($priImageSize[0] > 395 && $priImageSize[1] < 840){
									$newSize = imageResizeSc(680,400,500);						
									$priImageSize[0] = 500;
								}					
								elseif($priImageSize[1] > 840){
									$newSize = imageResizeSc($priImageSize[0],$priImageSize[1],600);						
									$priImageSize[1] = 600;
								}
								else{					
									$newSize = $priImageSize[3];
								}							
								if($priImageSize[1] > 800 ){					
									echo '<newpage>';												
								}
							}
							$elem_pnData.='<tr><td class="text_10"><img src="oproom1.jpg" '.$newSize.'></td></tr>';
						}			
						if($iol_ScanUpload!='' || $iol_ScanUpload2!=''){
							$elem_pnData.='</table>';
						}
					}
					//END IOL SCAN UPLOAD IMAGE
					**/
					//mysql_close($link_ocean);
					//include(dirname(__FILE__)."/../../accounting/connect_sc.php");					
				}//if else
				$elem_pnData = nl2br($elem_pnData);
				$elem_pnData = strip_tags($elem_pnData,' <p> <br> <img> <strong> </strong><table><tr><td>');
				$elem_pnData = str_ireplace("</p><br />","</p>",$elem_pnData);
				
			}
			$elem_pnData = str_ireplace("{PHYSICIAN SIGNATURE}","",$elem_pnData);
			$elem_pnData = str_ireplace("&Acirc;","",$elem_pnData);
			//stop code to get opnotes related to scEMR
			
			//
			$fp = "/tmp/pdffile2.html";
			$oSaveFile = new SaveFile($_SESSION["authId"],1);
			$getFinalHTMLForGivenMR = $oSaveFile->corImgPath4Pdf($elem_pnData);			
			$resp = $oSaveFile->cr_file($fp,$elem_pnData);			
			$printOptionType_v = empty($printOptionType) ? 'l' : 'p';
			
			header("location: ".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?op=".$printOptionType_v."&file_location=".$resp);			
			
		}//End //$flgStoExec
	
	}//End function
}

?>