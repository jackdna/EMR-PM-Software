<?php

class ChartDraw extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid,$examName){
		parent::__construct($pid,$fid);
		$this->tbl="chart_drawings";
		$this->examName=$examName;
	}

	function isRecordExists($a="",$b="",$c="",$d=""){
		return parent::isRecordExists($this->tbl,'form_id',"patient_id",$this->examName);
	}

	function getRecord($sel=" * ",$a="",$b="",$c="",$d=""){
		return parent::getRecord($this->tbl,$sel, 'form_id',"patient_id",$this->examName);
	}

	function getLastRecord($sel=" * ",$LF="0",$dt="", $tbl="", $b="", $c="" ){
		return parent::getLastRecord($this->tbl,"form_id",$LF,$sel,$dt,$this->examName);
	}

	function get_chart_draw_info($elem_dos){
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;
		$ar_ret = array();

		$sql = "SELECT * FROM ".$this->tbl." WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' AND exam_name='".$this->examName."' AND purged = '0' ";
		$row=sqlQuery($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($row==false){
				$sql = str_replace($this->tbl,$this->tbl."_archive",$sql);
				$row = sqlQuery($sql);
			}
		}
		//-------- End ----------------------------------------------------------------------------------

		if($row==false){
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}

		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			$res = $this->getLastRecord(" * ",1,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}

		if($row!=false){
			if($myflag){
				$elem_editMode=0;
			}else{
				$elem_editMode=1;
			}

			//
			$ar_inf = $this->get_exm_drw_vars();
			extract($ar_inf);

			$ar_ret["elem_drawId_LF"]=$ar_ret["elem_drawId"]=$row["id"];
			$ar_ret["elem_editModeDraw"] = $elem_editMode;

			//NC
			if($elem_editMode==1){
				$ar_ret["elem_ncDraw"]=$row["ncDraw"];
				$ar_ret["elem_examDateDraw"]=$row["exam_date"];
			}

			$ar_ret["".$exm_drawing] = $row["exm_drawing"];
			$ar_ret[$txt_odd]=$row['drw_od_txt'];
			$ar_ret[$txt_oss]=$row['drw_os_txt'];
			$ar_ret["elem_wnlDraw"]=$row["wnlDraw"];
			$ar_ret["elem_posDraw"]=$row["posDraw"];
			$ar_ret["elem_wnlDrawOd"]=$row["wnlDrawOd"];
			$ar_ret["elem_wnlDrawOs"]=$row["wnlDrawOs"];
			$ar_ret["elem_statusElementsDraw"] = ($elem_editMode==0) ? "" : $row["statusElem"];
			$ar_ret["dbIdocDrawingId"] = $dbIdocDrawingId = $row["idoc_drawing_id"];
			$ar_ret["strCanvasWNL"] = "yes";
			if($dbIdocDrawingId != ""){
				$arrDrwaingData = array();
				$OBJDrawingData = new CLSDrawingData();
				$ar_ret["arrDrwaingData"] = $OBJDrawingData->getHTMLDrawingData($dbIdocDrawingId, 1);
			}

			//UT Elems
			$ar_ret["elem_utElemsDraw"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;
			$ar_ret["elem_drawType"] = $row["draw_type"];

			//Report and interpretation
			$ar_ret_IR = ($elem_editMode==1) ? $this->get_report_interp($row["id"]) : array() ;
			$ar_ret = array_merge($ar_ret, $ar_ret_IR);
		}

		return $ar_ret;
	}

	function del_inter_report(){
		$ar = array();
		$id = $_POST["id"];
		if(!empty($id)){
			$usrid = $_SESSION["authId"];
			$sql = "UPDATE chart_draw_inter_report SET del_by='".$usrid."', del_on='".wv_dt("now")."' WHERE id='".$id."'  ";
			$row=sqlQuery($sql);

			$ar["res"] = "0";
			$ar["orderby"] = $usrid;
			$ousr = new User($usrid);
			$ar["orderbyNm"] = $ousr->getName(3);
		}
		echo json_encode($ar);
	}

	function get_report_interp_v2(){
		$as =$dx=$dxid=$plan= "";
		$sql = "SELECT c2.assessment, c2.dx, c2.dxid, c2.plan FROM chart_drawings c1
				INNER JOIN chart_draw_inter_report c2 ON c1.id = c2.id_chart_draw
				 WHERE c1.form_id='".$this->fid."' AND c1.patient_id='".$this->pid."' AND c2.del_by = '0' AND c1.purgerId='0' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$as = $row["assessment"];
			$dx= $row["dx"];
			$dxid= $row["dxid"];
			$plan= $row["plan"];
		}

		return array($as,$dx,$plan,$dxid);
	}

	function get_report_interp($id){
		$ret=array();
		$sql = "SELECT order_by,test_type,assessment,dx,plan,id   FROM chart_draw_inter_report WHERE id_chart_draw='".$id."' AND del_by = '0' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$ret["ir_ordered_by"] = $row["order_by"];
			$ret["ir_test_type"] = $row["test_type"];
			$ret["ir_assessment"] = $row["assessment"];
			$ret["ir_dxcd"] = $row["dx"];
			$ret["ir_dxid"] = $row["dxid"];
			$ret["ir_plan"] = $row["plan"];
			$ret["chart_draw_inter_report_id"] = $row["id"];
		}

		if(empty($ret["ir_ordered_by"])){
			$ret["ir_ordered_by"] = $_SESSION["authId"];
		}

		//Add user name
		if(!empty($ret["ir_ordered_by"])){
			$ousr = new User($ret["ir_ordered_by"]);
			$tmp_nm = $ousr->getName(3);
		}
		$ret["ir_ordered_by_name"] = !empty($tmp_nm) ? $tmp_nm : "" ;

		return $ret;
	}

	function print_report_interp($id, $cr_pdf_file=""){
		$print_pg="";
		$sql = "SELECT c1.order_by, c1.test_type, c1.assessment, c1.dx, c1.plan, c1.id,
				c2.patient_id, c2.form_id,
				c3.drawing_image_path
				FROM chart_draw_inter_report c1
				INNER JOIN chart_drawings c2 ON c2.id = c1.id_chart_draw
				INNER JOIN ".constant("IMEDIC_SCAN_DB").".idoc_drawing c3 ON c3.id = c2.idoc_drawing_id
				WHERE c1.id='".$id."' AND c1.del_by = '0' AND c2.purged='0'
				AND c2.exam_name='".$this->examName."' ";
		$row=sqlQuery($sql);
		if($row!=false){
			$ret["ir_ordered_by"] = $row["order_by"];
			$ret["ir_test_type"] = $row["test_type"];
			$ret["ir_assessment"] = $row["assessment"];
			$ret["ir_dxcd"] = $row["dx"];
			$ret["ir_dxid"] = $row["dxid"];
			$ret["ir_plan"] = $row["plan"];
			$ret["patient_id"] = $row["patient_id"];
			$ret["chart_draw_inter_report_id"] = $row["id"];
			$ret["drawing_image_path"] = $row["drawing_image_path"];

			$osf = new SaveFile($ret["patient_id"]);
			$url_image = $osf->getFilePath($ret["drawing_image_path"],'i');
			if(file_exists($url_image)){
				$img_b=str_replace(".png","_b.png",$url_image);
				if( file_exists($img_b) ){
					$url_image=$img_b;
				}else{
					$url_image="";
				}
			}else{ $url_image=""; }

			$ousr = new User($ret["ir_ordered_by"]);
			list($str_pth, $strpix) = $ousr->getSign();
			if(!empty($str_pth)){
				$str_pth = $osf->getFilePath($str_pth,'i');
				if( !file_exists($str_pth) ){ $str_pth=""; }
			}else{
				$str_pth="";
			}

			//exit($url_image);
			
			
			$ret["ir_ordered_by"] = $ousr->getName(3);

			
			$str="";
			$str .=  '<table cellpadding="0" cellspacing="0" >';
			$str .= '<tr><td>Order By</td><td>'.$ret["ir_ordered_by"].'</td></tr>';
			$str .= '<tr><td>Assessment</td><td>'.$ret["ir_assessment"].'</td></tr>';
			$str .= '<tr><td>Dx Code</td><td>'.$ret["ir_dxcd"].'</td></tr>';
			$str .= '<tr><td>Plan</td><td>'.$ret["ir_plan"].'</td></tr>';
			if(!empty($url_image)){
			$str .= '<tr><td colspan="2"><img src="'.$url_image.'" alt="image" width="400" /></td></tr>';
			}
			if(!empty($str_pth)){
			$str .= '<tr><td colspan="2">Signature:</td></tr>';
			$str .= '<tr><td colspan="2"><img src="'.$str_pth.'" alt="image" width="200" /></td></tr>';
			}
			$str .= '</table>';

			$oPrinter = new Printer($this->pid, $this->fid);
			$print_pg = $oPrinter->print_page($str, "Interpretation","print_interpretation","","1",$cr_pdf_file);

		}

		if(!empty($print_pg)){

			if(isset($cr_pdf_file)&&!empty($cr_pdf_file)){
				return file_exists($print_pg) ? $print_pg : "";
			}else{
				header('Location: '.$GLOBALS['rootdir'].'/../library/html_to_pdf/createPdf.php?op=p&file_location='.$print_pg.'');
				exit();
			}
		}else{
			echo "Page Not Found!";
		}
	}

	function save_report_interp($id){
		$ordered_by = $_POST["ir_ordered_by"];
		$test_type = $_POST["ir_test_type"];
		$ar_assessment = $_POST["elem_assessment"];
		$assessment = trim($ar_assessment[0]);
		$ar_dxcd = $_POST["elem_assessment_dxcode"];
		$dxcd = trim($ar_dxcd[0]);
		$dxid = $_POST["ir_dxid"];
		$ar_plan = $_POST["elem_plan"];
		$plan = trim($ar_plan[0]);
		$flg_update = "0";
		$drw_type = $_POST["elem_drawType"];

		if(empty($assessment)){return;} //if Empty return;

		if($drw_type=="8"){ $test_type="Optic Nerve Drawing"; }
		else if($drw_type=="9"){ $test_type="Macula Drawing"; }
		else{ $test_type="Retina Drawing"; }

		$arr_prv = $this->get_report_interp($id);
		if(!empty($arr_prv["chart_draw_inter_report_id"])){
			$sql = "UPDATE chart_draw_inter_report SET ";
			$sql_wh = " WHERE id='".$arr_prv["chart_draw_inter_report_id"]."' ";
			if(trim($assessment)!=trim($arr_prv["ir_assessment"]) || trim($plan)!=trim($arr_prv["ir_plan"]) ||
				 trim($dxcd)!=trim($arr_prv["ir_dxcd"]) || trim($test_type)!=trim($arr_prv["ir_test_type"])){
				$flg_update = "1";
			}

		}else{
			$sql = "INSERT INTO chart_draw_inter_report SET id_chart_draw='".$id."', ";
			$flg_update = "1";
			$sql_wh="";
		}

		if(!empty($flg_update)){
			$order_on = wv_dt("now");
			$sql .= "order_by='".$ordered_by."',
					order_on='".$order_on."',
					test_type='".$test_type."',
					assessment='".sqlEscStr($assessment)."',
					dx='".sqlEscStr($dxcd)."',
					dxid='".sqlEscStr($dxid)."',
					plan='".sqlEscStr($plan)."'
					".$sql_wh ;
			sqlQuery($sql);
		}
	}

	function get_exm_drw_vars(){
		$ret = array();
		$dvid = 0 ;
		if($this->examName == "LA" || $this->examName == "FundusExam"){$dvid = "5" ;}
		else if($this->examName == "SLE"){$dvid = "6" ;}
		else if($this->examName == "Gonio"){$dvid = "Iop3" ;}
		else if($this->examName == "EOM"){$dvid = "1" ;}
		else if($this->examName == "External"){$dvid = "2" ;}
		else if($this->examName == "DrawPane" || $this->examName == "DrawCL"){$dvid = "1" ;}

		$ret["dvid"] = $dvid;
		if($this->examName == "LA"){
			$ret["txt_od"] = "el_la_od";
			$ret["txt_os"] = "el_la_os";
			$ret["txt_odd"] = "el_la_odd";
			$ret["txt_oss"] = "el_la_oss";
			$ret["exm_drawing"] = "elem_laDrawing";
			$ret["elem_drwId_LF"] = "elem_drawId_LF"; //chart drawing id
			$ret["hidDrawingId"] = "hidLADrawingId";
			$ret["enm_2"] = "DrawLa";

		}else if($this->examName == "FundusExam"){
			$ret["txt_od"] = "od_desc";
			$ret["txt_os"] = "os_desc";
			$ret["txt_odd"] = "od_desc";
			$ret["txt_oss"] = "os_desc";
			$ret["exm_drawing"] = "elem_odDrawing";
			$ret["elem_drwId_LF"] = "elem_drawId_LF"; //chart drawing id
			$ret["hidDrawingId"] = "hidFundusDrawingId";
			$ret["enm_2"] = "DrawRv";
		}else if($this->examName == "SLE"){
			$ret["txt_od"] = "cornea_od_desc_1";
			$ret["txt_os"] = "cornea_os_desc";
			$ret["txt_odd"] = "cornea_od_desc_1";
			$ret["txt_oss"] = "cornea_os_desc";
			$ret["exm_drawing"] = "conjunctiva_od_drawing";
			$ret["elem_drwId_LF"] = "elem_drawId_LF"; //chart drawing id
			$ret["hidDrawingId"] = "hidSLEDrawingId";
			$ret["enm_2"] = "DrawSle";
		}

		return $ret;
	}

	public function save_form(){
		if(empty($this->examName)){ return; }
		$elem_formId = $formId = $this->fid;
		$patientid = $this->pid;

		//Check if Chart is not Finalized or User is Finalizer
		$OBJDrawingData = new CLSDrawingData();
		$objImageManipulation = new CLSImageManipulation();
		$oSaveFile = new SaveFile($patientid);

		$oChartNoteSaver = new ChartNoteSaver($patientid, $formId);
		$oExamXml = new ExamXml();
		$arXmlFiles = $oExamXml->getExamXmlFiles($this->examName);
		$ar_inf = $this->get_exm_drw_vars();
		extract($ar_inf);

		//GetChangeIndicator
		$arrSe = array();
		$tmpOd = "elem_chng_div".$dvid."_Od";
		$tmpOs = "elem_chng_div".$dvid."_Os";
		$$tmpOd = $_POST[$tmpOd];
		$$tmpOs = $_POST[$tmpOs];
		$arrSe[$tmpOd] = ($$tmpOd == "1") ? "1" : "0";
		$arrSe[$tmpOs] = ($$tmpOs == "1") ? "1" : "0";
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);

		//Draw ---------------
		$wnlDraw = $posDraw = $ncDraw = $wnlDrawOd = $wnlDrawOs = "0";
		//if(!empty($elem_chng_div5_Od) || !empty($elem_chng_div5_Os)){
		//	if(!empty($elem_chng_div5_Od)){
				$la_od_txt = sqlEscStr($_POST["".$txt_od]);
				$wnlDrawOd = $_POST["elem_wnlDrawOd"];
		//	}
		//	if(!empty($elem_chng_div5_Os)){
				$la_os_txt = sqlEscStr($_POST["".$txt_os]);
				$wnlDrawOs = $_POST["elem_wnlDrawOs"];
		//	}

			$laDrawing = $_POST["".$exm_drawing];
			$wnlDraw = (!empty($wnlDrawOd) && !empty($wnlDrawOs)) ? "1" : "0"; //$_POST["elem_wnlDraw"];
			$posDraw = $_POST["elem_posDraw"];
			$ncDraw = $_POST["elem_ncDraw"];
		//}
		//Draw ---------------

		$examDate = wv_dt("now"); //$_POST["elem_examDateLids"];

		$oUserAp = new UserAp();

		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsDraw"];
		$elem_utElems_cur = $_POST["elem_utElemsDraw_cur"];
		$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);
		//ut_elems ----------------------

		$draw_type = $_POST["elem_drawType"];//

		//Purge
		if(!empty($_POST["elem_purged"])){
			//$purgePhrse = " , purged = pupil_id ";
			//Update
			$sql = "UPDATE ".$this->tbl."
				  SET
				  purged=id,
				  purgerId='".$_SESSION["authId"]."',
				  purgetime='".wv_dt('now')."'
				  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND exam_name='".$this->examName."' AND purged = '0'
				";
			$row = sqlQuery($sql);

		}else{
			$owv = new WorkView();

			//check
			$cQry = "select
						id,last_opr_id,uid,	 exam_date
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND exam_name='".$this->examName."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$last_opr_id = $_SESSION["authId"];
				$elem_editMode =  "0";
			}else{
				$drwId=$drwIDExam = $row["id"];
				$elem_editMode =  "1";
			}

			$sql_con = "";
			if(empty($_REQUEST["hidBlEnHTMLDrawing"]) == true){
				$sql_con .= "exm_drawing='$laDrawing',";
				$sql_con .= "drawing_insert_update_from='0',";
			}
			else{
				$sql_con .= "drawing_insert_update_from='1',";
			}

			$sql_con = "
				 drw_od_txt='$la_od_txt',
				 drw_os_txt='$la_os_txt',
				 wnlDraw='$wnlDraw',
				 posDraw='$posDraw',
				 ncDraw='$ncDraw',
				 wnlDrawOd='$wnlDrawOd',
				 wnlDrawOs='$wnlDrawOs',
				 uid = '".$_SESSION["authId"]."',
				 statusElem = '".$statusElem."',
				 ut_elem = '".$ut_elem."',
				 last_opr_id = '".$_SESSION["authId"]."',
				 draw_type = '".$draw_type."'
				 ";


			//
			if($elem_editMode == "0"){
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',
					exam_date='$examDate',
					exam_name='".$this->examName."',
					 ";
				$sql = $sql1. $sql_con;
				$insertId = sqlInsert($sql);

			}else if($elem_editMode == "1"){
				//Update
				$sql1 = "UPDATE ".$this->tbl." SET ";
				$sql2 = " WHERE form_id='".$formId."' AND patient_id='".$patientid."' AND exam_name='".$this->examName."' AND purged = '0' ";
				$sql = $sql1. $sql_con. $sql2;
				$res = sqlQuery($sql);
				$insertId = $drwId;
			}

			//--
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
						$drawingFileName = "/".$this->examName."_idoc_drawing_".date("YmdHsi")."_".session_id()."_".$intTempDrawCount.".png";
						$arrDrawingData["drawingFileName"] = $drawingFileName;
						$arrDrawingData["drawingFor"] = "".$this->examName;
						$arrDrawingData["drawingForMasterId"] = $insertId;
						$arrDrawingData["formId"] = $formId;
						$arrDrawingData["hidDrawingTestName"] = $_REQUEST["hidDrawingTestName".$intTempDrawCount];
						$arrDrawingData["hidDrawingTestId"] = $_REQUEST["hidDrawingTestId".$intTempDrawCount];
						$arrDrawingData["hidImagesData"] = $_REQUEST["hidImagesData".$intTempDrawCount];
						$arrDrawingData["hidDrawingId"] = (int)$_REQUEST[$hidDrawingId.$intTempDrawCount];
						$arrDrawingData["examMasterTable"] = "".$this->tbl;
						$arrDrawingData["examMasterTablePriColumn"] = "id";
						$arrDrawingData["drwNE"] = $_REQUEST["elem_drwNE".$intTempDrawCount];
						$arrDrawingData["hidDrwDataJson"] = $_REQUEST["hidDrwDataJson".$intTempDrawCount];

						if($elem_editMode == "0"){
							$OBJDrawingData->insertDrawingData($arrDrawingData);
						}else{
							$OBJDrawingData->updateDrawingData($arrDrawingData);
							//
							if(!empty($arrDrawingData["hidDrawingId"])){
								$recordModiDateDrawing=$arrDrawingData["hidDrawingId"];
								$arr_updrw_ids[]=$arrDrawingData["hidDrawingId"];
							}
						}

						//$flagCFD=0;
					}else{
						if($elem_editMode == "0"){
							//Check old drawing exists for carry forward
							if(!empty($_POST[$hidDrawingId.$intTempDrawCount])){
								$arrCFD_ids[] = $_POST[$hidDrawingId.$intTempDrawCount];
								//$flagCFD_drw1=1;
							}
						}else{
							if(!empty($_POST[$hidDrawingId.$intTempDrawCount])){
								$arr_updrw_ids[] = $_POST[$hidDrawingId.$intTempDrawCount];
							}
						}
					}
				}

				if($elem_editMode == "1"){
					//delete records of previous visit if any --
					$OBJDrawingData->deleteNoSavedDrwing(array($patientid, $formId,$insertId,"".$this->examName), $arr_updrw_ids);
					//--
				}

				//form Id
				list($strIDocId, $str_row_modify_Draw)=$OBJDrawingData->getExamDocids(array($patientid, $formId,$insertId,"".$this->examName,$recordModiDateDrawing));

				$qryUpdateLAIDoc = "update chart_drawings set idoc_drawing_id = '".$strIDocId."' ".$str_row_modify_Draw." where id = '".$insertId."' ";
				$raUpdateLAIDoc = imw_query($qryUpdateLAIDoc);
			}

			//print_r($arrCFD_ids);
			//exit();

			if($elem_editMode == "0"){
				//if(!empty($_POST["elem_laId_LF"])&&!empty($flagCFD_drw1) && $flagCFD==1){ // Check if Last visit Drawing exists
				if(!empty($_POST[$elem_drwId_LF]) && count($arrCFD_ids)>0){ // Check if Last visit Drawing exists
					// Carry Forward iDOC Draw : This is done because drawing is not saved when not touched but we need to carry forward to display.Drawing status will be grey.
					$arrIN = array();
					$arrIN["pid"]=$patientid;
					$arrIN["formId"]=$elem_formId;
					$arrIN["examId"]=$insertId;
					$arrIN["exam"]="".$this->examName;
					$arrIN["examIdLF"]=$_POST[$elem_drwId_LF];
					$arrIN["strDrwIdsLF"]=implode(",", $arrCFD_ids);
					$arrIN["examMasterTable"]="".$this->tbl;
					$arrIN["examMasterTablePriColumn"]="id";
					$OBJDrawingData->carryForward($arrIN);
					//
				}
			}

			//--
			if(!empty($insertId)){ //Save Report Interpretation
				$this->save_report_interp($insertId);
			}


		}
		return 0;
	}

	function getPurgedExm(){
		$oOnload =  new Onload();
		$arPurge = array();

		//
		$ar_inf = $this->get_exm_drw_vars();
		extract($ar_inf);

		$echo="";
		$sql = "SELECT ".
				 " c7.posDraw , ".
				 " c7.wnlDraw , ".
				 " c7.ncDraw, ".
				 " c7.exm_drawing,  ".
				 " c7.id, ".
				 " c7.wnlDrawOd , ".
				 " c7.wnlDrawOs , ".
				 " c7.idoc_drawing_id,  ".
				 " c7.statusElem AS se_draw, c7.exam_date, c7.purgerId, c7.purgeTime ".
				"FROM chart_master_table c1 ".
				"INNER JOIN ".$this->tbl." c7 ON c7.form_id = c1.id AND c7.purged!='0' AND c7.exam_name='".$this->examName."'  ".
				"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
				"ORDER BY c7.purgeTime DESC ";
		$rez=sqlStatement($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if(imw_num_rows($rez) <= 0){
				$sql = str_replace($this->tbl,$this->tbl."_archive",$sql);
				$rez=sqlStatement($sql);
			}
		}
		//-------- End ----------------------------------------------------------------------------------
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$elem_posDraw=assignZero($row["posDraw"]);
			$elem_wnlDraw=assignZero($row["wnlDraw"]);
			$elem_ncDraw=assignZero($row["ncDraw"]);
			$elem_wnlDrawOd = $row["wnlDrawOd"];
			$elem_wnlDrawOs = $row["wnlDrawOs"];
			$elem_se_draw = $row["se_draw"];
			$elem_exmDraw = $this->isAppletDrawn($row["exm_drawing"]);
			$elem_drawing_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			if(!empty($row["idoc_drawing_id"])) {
				$drawdocId = $elem_drawing_id;
			}elseif( !empty($row["exm_drawing"]) ){
				$ardrawApp = array($row["exm_drawing"]);
			}

			//--
			$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
			$tmpArrSe=array();
			if(!isset($bgColor_Draw)){
				if(!empty($elem_se_draw)){
					$tmpArrSe = $this->se_elemStatus($enm_2,"0",$elem_se_draw);
					$flgSe_Draw_Od = $tmpArrSe[$dvid]["od"];
					$flgSe_Draw_Os = $tmpArrSe[$dvid]["os"];
				}
			}else{
				if(!empty($elem_se_La_prev)){
					$tmpArrSe_prev = $this->se_elemStatus($enm_2,"0",$elem_se_draw_prev);
					$flgSe_Draw_Od_prev = $tmpArrSe_prev[$dvid]["od"];
					$flgSe_Draw_Os_prev = $tmpArrSe_prev[$dvid]["os"];
				}
			}
			//--


			//WNL

			//Nochanged
			if(!empty($elem_se_draw)&&strpos($elem_se_draw,"=1")!==false){
				$elem_ncDraw=1;
			}

			//
			$arr = array();

			//Drawing
			$arr["subExm"][4] = $oOnload->getArrExms_ms(array("enm"=>"Drawing",
											"sOd"=>$drawdocId,"sOs"=>"",
											"fOd"=>$flgSe_Draw_Od,"fOs"=>$flgSe_Draw_Os,"pos"=>$elem_posDraw,
											"enm_2"=>"".$enm_2));

			//Sub Exam List
			$arr["seList"] = array(
							$enm_2=>array("enm"=>"Drawing","pos"=>$elem_posDraw,
											"wOd"=>$elem_wnlDrawOd,"wOs"=>$elem_wnlDrawOs)
						);

			$arr["bgColor"] = "".$bgColor_Draw;
			$arr["nochange"] = $elem_ncDraw;
			$arr["drawdocId"] = $drawdocId;
			$arr["drawapp"] = $ardrawApp;
			$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
			$arr["examdate"] = $examdate;
			$arr["flgGetDraw"] = $oOnload->onwv_isDrawingChanged(array($elem_wnlDrawOd,$elem_wnlDrawOs,$elem_ncDraw,$elem_ncDraw));
			$arr["exm_flg_se"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
			$arr["purgerId"] = $row["purgerId"];
			$arr["purgeTime"] = $row["purgeTime"];

			$arPurge[] = $arr;
		}

		return $arPurge;
	}

	function getWorkViewSummery($post){
		$oOnload =  new Onload();
		//object Chart Rec Archive --
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION['authId']);

		//
		$ar_inf = $this->get_exm_drw_vars();
		extract($ar_inf);

		$echo="";
		$sql = "SELECT ".
				 " c7.posDraw , ".
				 " c7.wnlDraw , ".
				 " c7.ncDraw, ".
				 " c7.exm_drawing,  ".
				 " c7.id, ".
				 " c7.wnlDrawOd , ".
				 " c7.wnlDrawOs , ".
				 " c7.idoc_drawing_id,  ".
				 " c7.statusElem AS se_draw, c7.exam_date, c7.draw_type ".
				"FROM chart_master_table c1 ".
				"INNER JOIN ".$this->tbl." c7 ON c7.form_id = c1.id AND c7.purged='0' AND c7.exam_name='".$this->examName."'  ".
				"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ";

		$row = sqlQuery($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($row==false){
				$sql = str_replace($this->tbl,$this->tbl."_archive",$sql);
				$row = sqlQuery($sql);
			}
		}
		//-------- End ----------------------------------------------------------------------------------
		if($row != false){
			$elem_posDraw=assignZero($row["posDraw"]);
			$elem_wnlDraw=assignZero($row["wnlDraw"]);
			$elem_ncDraw=assignZero($row["ncDraw"]);
			$elem_wnlDrawOd = $row["wnlDrawOd"];
			$elem_wnlDrawOs = $row["wnlDrawOs"];
			$elem_se_draw = $row["se_draw"];
			$elem_exmDraw = $this->isAppletDrawn($row["exm_drawing"]);
			$elem_drawing_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			if(!empty($row["idoc_drawing_id"])) {
				$drawdocId = $elem_drawing_id;
			}elseif( !empty($row["exm_drawing"]) ){
				$ardrawApp = array($row["exm_drawing"]);
			}
			$elem_draw_type = $row["draw_type"];
		}

		//Previous
		if(empty($elem_drawing_id)){
			$tmp = "";
			$tmp .= "  c2.posDraw , ";
			$tmp .= "  c2.wnlDraw , ";
			$tmp .= "  c2.wnlDrawOd , ";
			$tmp .= " c2.wnlDrawOs , ";
			$tmp .= "  c2.ncDraw, ";
			$tmp .= " c2.exm_drawing, c2.idoc_drawing_id, ";
			$tmp .= " c2.exam_date, ";
			$tmp .= "  c2.id, c2.statusElem AS se_draw, c2.draw_type ";

			//$res = valNewRecordL_a($patient_id, $tmp);
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
				$elem_posDraw=assignZero($row["posDraw"]);
				$elem_wnlDraw=assignZero($row["wnlDraw"]);
				$elem_ncDraw_prev=assignZero($row["ncDraw"]);
				$elem_exmDraw = $this->isAppletDrawn($row["exm_drawing"]);
				$elem_wnlDrawOd=assignZero($row["wnlDrawOd"]);
				$elem_wnlDrawOs=assignZero($row["wnlDrawOs"]);
				$examdate = wv_formatDate($row["exam_date"]);

				if(!empty($row["idoc_drawing_id"])) {
					$drawdocId = $row["id"];
				}elseif( !empty($row["exm_drawing"]) ){
					$ardrawApp = array($row["exm_drawing"]);
				}

				$elem_se_draw_prev = $row["se_draw"];
				$elem_draw_type = $row["draw_type"];
			}
			//BG
			$bgColor_Draw = "bgSmoke";
		}

		//--
		$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
		$tmpArrSe=array();
		if(!isset($bgColor_Draw)){
			if(!empty($elem_se_draw)){
				$tmpArrSe = $this->se_elemStatus($enm_2,"0",$elem_se_draw);
				$flgSe_Draw_Od = $tmpArrSe[$dvid]["od"];
				$flgSe_Draw_Os = $tmpArrSe[$dvid]["os"];
			}
		}else{
			if(!empty($elem_se_La_prev)){
				$tmpArrSe_prev = $this->se_elemStatus($enm_2,"0",$elem_se_draw_prev);
				$flgSe_Draw_Od_prev = $tmpArrSe_prev[$dvid]["od"];
				$flgSe_Draw_Os_prev = $tmpArrSe_prev[$dvid]["os"];
			}
		}
		//--


		//WNL

		//Nochanged
		if(!empty($elem_se_draw)&&strpos($elem_se_draw,"=1")!==false){
			$elem_ncDraw=1;
		}

		//Modified Notes ----
		/*
		//if Edit is not Done && modified Notes exists
		//Drawing
		if(!empty($modi_note_Draw)){ //Os
			list($moeMN["od"]["draw"],$tmpDiv)=$oOnload->getModiNoteConDiv("elem_drawOdLA", $modi_note_Draw);
			//echo $tmpDiv;
			$arrDivArcCmn["Drawing"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["draw"]="";
		}
		*/
		//Modified Notes ----

		//
		$arr = array();

		//Drawing
		$arr["subExm"][4] = $oOnload->getArrExms_ms(array("enm"=>"Drawing",
										"sOd"=>$drawdocId,"sOs"=>"",
										"fOd"=>$flgSe_Draw_Od,"fOs"=>$flgSe_Draw_Os,"pos"=>$elem_posDraw,
										"enm_2"=>"".$enm_2));

		//Sub Exam List
		$arr["seList"] = array(
						$enm_2=>array("enm"=>"Drawing","pos"=>$elem_posDraw,
										"wOd"=>$elem_wnlDrawOd,"wOs"=>$elem_wnlDrawOs)
					);

		$arr["bgColor"] = "".$bgColor_Draw;
		$arr["nochange"] = $elem_ncDraw;
		$arr["drawdocId"] = $drawdocId;
		$arr["drawapp"] = $ardrawApp;
		$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
		$arr["examdate"] = $examdate;
		$arr["moeMN"] = $moeMN;
		$arr["flgGetDraw"] = $oOnload->onwv_isDrawingChanged(array($elem_wnlDrawOd,$elem_wnlDrawOs,$elem_ncDraw,$elem_ncDraw));
		$arr["exm_flg_se"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
		$arr["draw_type"] = $elem_draw_type;

		/*
		echo "<pre>";
		print_r($arr);
		exit();
		*/

		return $arr;
	}

	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){

		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, exam_name)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".$this->examName."') ";
		$return=sqlInsert($sql);

		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.id ","1");
		if($res!=false){
			$Id_LF = $res["id"];
		}
		//Insert
		$insertId = $this->insertNew();
		//CopyLF
		$ignoreFlds = "form_id,exam_date,uid,statusElem,".
					"ncDraw,".
					"ncDraw_od,".
					"ncDraw_os";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,"".$this->examName,'id');
	}

	function save_wnl(){

		if(!$this->isRecordExists()){
			$this->carryForward();
			$flgCarry=1;
		}

	}

	function isNoChanged(){
		$res= $this->getRecord("ncDraw,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncDraw"]) ){
				return true;
			}
		}
		return false;
	}

	function set2PrvVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" id ");
			if($res1!=false){
				$Id = $res1["id"];
			}

			$res = $this->getLastRecord(" c2.id ","1");
			if($res!=false){
				$Id_LF = $res["id"];
			}

			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,statusElem,".
						"ncDraw,".
						"ncDraw_od,".
						"ncDraw_os,".
						"";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds, "".$this->examName,'id');
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values
			}
		}

	}

	function resetVals(){
		$is_cryfd=0;
		if(!$this->isRecordExists()){
			//In
			$this->carryForward();
			$is_cryfd=1;
		}

		//if($this->isRecordExists()){
			$statusElem = "";
			$res1 = $this->getRecord(" id,statusElem ");
			if($res1!=false){
				$Id = $res1["id"];
				if($is_cryfd==0){$statusElem = $res1["statusElem"];}
			}

			//CopyLF
			$ignoreFlds = "form_id,exam_date,uid,patient_id,idoc_drawing_id,exam_name,";
			if(!empty($Id)){

				if(empty($_POST["site"]) || $_POST["site"]=="OU"){ $statusElem = "";  }
				if($_POST["site"]=="OS"){
					$ignoreFlds .= "drw_od_txt, wnlDrawOd,
								ncDraw_od,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }
				}else if($_POST["site"]=="OD"){
					$ignoreFlds .= "drw_os_txt, wnlDrawOs,
								ncDrawLa_os,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posDraw,";
					if($is_cryfd==0){$ignoreFlds .= "ut_elem,";}
				}
				$ignoreFlds = trim($ignoreFlds,",");


				$this->resetValsExe($this->tbl,$Id,$ignoreFlds,"".$this->examName,'id');
				$this->setStatus($statusElem,$this->tbl);
			}
		//}else{
			//
		//	$this->insertNew();
		//}

	}

	function save_no_change(){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];

		//$elem_noChangeLa = $_POST["elem_noChangeLA"];
		$tmpNC=0;
		$elem_ncDrawLa="0";
		$elem_ncDrawLa_od="0";
		$elem_ncDrawLa_os="0";

		//
		$oLA=$this; //new LA($patientId,$form_id);
		if(!$oLA->isRecordExists()){
			$oLA->carryForward();
			$tmpNC=1;
		}else if(!$oLA->isNoChanged()){
			$tmpNC=1;
		}else{
			$oLA->set2PrvVals();
			$tmpNC=0;
		}

	}

	function attachDraw2PtExam($drwId){

		//
		$ar_inf = $this->get_exm_drw_vars();
		extract($ar_inf);

		$sql = "select * FROM ".$this->tbl." WHERE form_id = '".$this->fid."' AND patient_id='".$this->pid."' AND purged='0' AND exam_name='".$this->examName."' ";
		$row=sqlQuery($sql);
		//exit($sql);
		if($row != false){
			$id = $row["id"];
			$idoc_drawing_id = $row["idoc_drawing_id"];
			$statusElem = $row["statusElem"];

			if(!empty($idoc_drawing_id)){ $idoc_drawing_id .= ","; }
			$idoc_drawing_id .= $drwId;

			if(!empty($statusElem)){
				if(strpos($statusElem, $dvid."_Od=1")===false){
					$statusElem = str_replace(array("elem_chng_div".$dvid."_Od=0",",,",", ,"),"", $statusElem);
				}
				if(strpos($statusElem, $dvid."_Os=1")===false){
					$statusElem = str_replace(array("elem_chng_div".$dvid."_Os=0",",,",", ,"),"", $statusElem);
				}
			}
			if(!empty($statusElem)){ $statusElem.=",";  }

			$statusElem .= "elem_chng_div".$dvid."_Os=1";
			$statusElem .= ",elem_chng_div".$dvid."_Od=1";

			//attached to la
			$sql = "UPDATE ".$this->tbl." SET idoc_drawing_id = '".$idoc_drawing_id."', statusElem='".sqlEscStr($statusElem)."'  WHERE id='".$id."'  ";
			$row=sqlQuery($sql);

			//update idocdrawing
			$sql = "UPDATE ".constant("IMEDIC_SCAN_DB").".idoc_drawing
					SET drawing_for='".$this->examName."', drawing_for_master_id='".$id."'
					WHERE id = '".$arr["insertId"]."' ";
			$row=sqlQuery($sql);
		}

	}

	function getIDocDrawId(){

		//
		$ar_inf = $this->get_exm_drw_vars();
		extract($ar_inf);

		$str_idoc_drawing_id = $tmp_flg_Drw_grey = "";

		$sql = "SELECT idoc_drawing_id, statusElem FROM ".$this->tbl." WHERE patient_id='".$this->pid."' AND form_id='".$this->fid."' AND purged='0' AND exam_name='".$this->examName."'   ";
		$row=sqlQuery($sql);
		if($row != false){
			if(!empty($row["idoc_drawing_id"])){
				$str_idoc_drawing_id = $row["idoc_drawing_id"].",";

				if(strpos($row["statusElem"], "elem_chng_div".$dvid."_Od=1")===false && strpos($row["statusElem"], "elem_chng_div".$dvid."_Os=1")===false){
					$tmp_flg_Drw_grey = $row["idoc_drawing_id"];
				}
			}
		}else{
			//
			$tmp = " idoc_drawing_id ";
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			if($row!=false){
				if(!empty($row["idoc_drawing_id"])){
					$str_idoc_drawing_id = $row["idoc_drawing_id"].",";
				}
			}
		}

		return array($str_idoc_drawing_id, $tmp_flg_Drw_grey);
	}

}

?>
