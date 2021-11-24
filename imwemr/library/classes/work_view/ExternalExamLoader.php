<?php
class ExternalExamLoader extends ExternalExam{
	private $oOnload;
	public function __construct($pid, $fid){
		$this->oOnload =  new Onload();
		parent::__construct($pid,$fid);	
	}
	function getPurgedHtm(){
		$htmPurge="";
		$sql ="SELECT ".
			"c4.isPositive, c4.posEe AS flagPosEe, c4.posDraw, c4.wnl, c4.wnlEe AS flagWnlEe, c4.wnlDraw, ".
			"c4.examined_no_change, c4.ncEe AS chEe, c4.ncDraw, ".
			"c4.ee_drawing, c4.external_exam_summary, ".
			"c4.sumOsEE, c4.ee_id, ".
			"c4.wnlEeOd, c4.wnlEeOs, c4.descExternal, ".
			"c4.statusElem AS se_ee, c4.purgerId, c4.purgeTime, ".
			"c4.ee_drawing, c4.idoc_drawing_id, c4.wnl_value ".
			"FROM chart_master_table c1 ".
			"INNER JOIN chart_external_exam c4 ON c4.form_id = c1.id AND c4.purged!='0'  ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
			"ORDER BY c4.purgeTime DESC";			
		$rz = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rz);$i++){
			$elem_noChangeEe=assignZero($row["chEe"]);
			$elem_wnlEe=assignZero($row["flagWnlEe"]);
			$elem_posEe=assignZero($row["flagPosEe"]);
			$elem_wnlEeOd=assignZero($row["wnlEeOd"]);
			$elem_wnlEeOs=assignZero($row["wnlEeOs"]);
			$elem_wnl_value=$row["wnl_value"];
			$elem_se_Ee = $row["se_ee"];
			$elem_eeDraw = $this->isAppletDrawn($row["ee_drawing"]);
			$elem_sumOdEE = $row["external_exam_summary"];
			$elem_sumOsEE = $row["sumOsEE"];
			$elem_ee_id = $row["ee_id"];
			$elem_txtDesc_ee = stripslashes(trim($row["descExternal"]));
			
			if(!empty($row["idoc_drawing_id"])) {
				$drawdocId = $elem_ee_id;
			}elseif( !empty($row["ee_drawing"]) ){
				$ardrawApp = array($row["ee_drawing"]);
			}

			
			//Wnl
			$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("External"); //WNL
			list($elem_sumOdEE,$elem_sumOsEE) = $this->oOnload->setWnlValuesinSumm(array("wVal"=>$wnlString,
						"wOd"=>$elem_wnlEeOd,"sOd"=>$elem_sumOdEE,
						"wOs"=>$elem_wnlEeOs,"sOs"=>$elem_sumOsEE));
			
			//Color
			$flgSe_Ee_Od = $flgSe_Ee_Os = "";
			$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
			if(!isset($bgColor_external)){
				if(!empty($elem_se_Ee)){
					$tmpArrSe = $this->se_elemStatus("EXTERNAL","0",$elem_se_Ee);
					$flgSe_Ee_Od = $tmpArrSe["Con"]["od"];
					$flgSe_Ee_Os = $tmpArrSe["Con"]["os"];
					$flgSe_Draw_Od=$tmpArrSe["Draw"]["od"];
					$flgSe_Draw_Os=$tmpArrSe["Draw"]["os"];
				}
			}
			
			//Nochanged
			if(!empty($elem_se_Ee)&&strpos($elem_se_Ee,"=1")!==false){
				$elem_noChangeEe=1;
			}
			
			//---------

			$arr=array();
			$arr["ename"] = "External"; 
			$arr["subExm"][0] = $this->oOnload->getArrExms_ms(array("enm"=>"External",
												"sOd"=>$elem_sumOdEE,"sOs"=>$elem_sumOsEE,
												"fOd"=>$flgSe_Ee_Od,"fOs"=>$flgSe_Ee_Os,"pos"=>$elem_posEe,
												"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
												"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
												));
			$arr["nochange"] = $elem_noChangeEe;
			$arr["oe"] = $oneeye;
			$arr["desc"] = $elem_txtDesc_ee;
			$arr["bgColor"] = "".$bgColor_external;
			$arr["purgerId"] = $row["purgerId"];
			$arr["purgeTime"] = $row["purgeTime"];
			$arr["exm_flg_se"] = array($flgSe_EE_Od,$flgSe_EE_Os);
			$arr["drawdocId"] = $drawdocId;
			$arr["drawapp"] = $ardrawApp;
			$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
			$htmPurge .= $this->oOnload->getSummaryHTML_purged($arr);
		}
		
		//---------
		return $htmPurge;
		
	}
	function getWorkViewSummery($post){
	
		$oneeye = $post["oe"]; //one eye
	
		//object Chart Rec Archive --
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION['authId']);
		//---
		$echo="";
		
		
		$sql ="SELECT ".
			"c4.isPositive, c4.posEe AS flagPosEe, c4.posDraw, c4.wnl, c4.wnlEe AS flagWnlEe, c4.wnlDraw, ".
			"c4.examined_no_change, c4.ncEe AS chEe, c4.ncDraw, ".
			"c4.ee_drawing, c4.external_exam_summary, ".
			"c4.sumOsEE, c4.ee_id, ".
			"c4.wnlEeOd, c4.wnlEeOs, c4.descExternal, c4.modi_note_od, c4.modi_note_os, c4.modi_note_Draw, ".
			"c4.statusElem AS se_ee, c4.modi_note_Arr, ".
			"c4.ee_drawing, c4.idoc_drawing_id, c4.wnl_value ".
			"FROM chart_master_table c1 ".
			"LEFT JOIN chart_external_exam c4 ON c4.form_id = c1.id AND c4.purged='0'  ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ";			
		$row = sqlQuery($sql);
					
		if($row != false){
			$elem_noChangeEe=assignZero($row["chEe"]);
			$elem_wnlEe=assignZero($row["flagWnlEe"]);
			$elem_posEe=assignZero($row["flagPosEe"]);
			$elem_wnlEeOd=assignZero($row["wnlEeOd"]);
			$elem_wnlEeOs=assignZero($row["wnlEeOs"]);
			$elem_se_Ee = $row["se_ee"];
			$elem_eeDraw = $this->isAppletDrawn($row["ee_drawing"]);
			$elem_sumOdEE = $row["external_exam_summary"];
			$elem_sumOsEE = $row["sumOsEE"];
			$elem_ee_id = $row["ee_id"];
			$elem_txtDesc_ee = stripslashes(trim($row["descExternal"]));
			
			$elem_wnlDraw=$row["wnlDraw"];
			$elem_ncDraw=$row["ncDraw"];
			$elem_wnl_value=$row["wnl_value"];
			
			$mnOd = $row["modi_note_od"];
			$mnOs = $row["modi_note_os"];
			$modi_note_Draw= $row["modi_note_Draw"];
			if($row["modi_note_Arr"]!='')
			$arrHx['External'] = unserialize($row["modi_note_Arr"]);
			
			if(!empty($row["idoc_drawing_id"])) {
				$drawdocId = $elem_ee_id;
			}elseif( !empty($row["ee_drawing"]) ){
				$ardrawApp = array($row["ee_drawing"]);
			}
			
		}

		//Previous
		if(empty($elem_ee_id)){
			$tmp = "";
			$tmp = " c2.isPositive, c2.posEe AS flagPosEe, 
					 c2.wnl, c2.wnlEe AS flagWnlEe, c2.examined_no_change, c2.ncEe  AS chEe,c2.wnlDraw,c2.ncDraw, ";
			$tmp .= " c2.wnlEeOd, c2.wnlEeOs, c2.descExternal, ";
			$tmp .= " c2.ee_drawing, c2.external_exam_summary, 
						c2.sumOsEE, c2.ee_id, ".
						"c2.ee_drawing, c2.idoc_drawing_id, c2.wnl_value, ".
						"c2.statusElem AS se_ee ";

			//$res = valNewRecordExter($patient_id, $tmp);
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			
			if($row!=false){
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				$elem_sumOdEE = $row["external_exam_summary"];
				$elem_sumOsEE = $row["sumOsEE"];
				$elem_wnlEe=assignZero($row["flagWnlEe"]);
				$elem_posEe=assignZero($row["flagPosEe"]);
				$elem_eeDraw = $this->isAppletDrawn($row["ee_drawing"]);
				$elem_wnlEeOd=assignZero($row["wnlEeOd"]);
				$elem_wnlEeOs=assignZero($row["wnlEeOs"]);
				$elem_txtDesc_ee = stripslashes($row["descExternal"]);
				$elem_wnlDraw=$row["wnlDraw"];
				//$elem_ncDraw=$row["ncDraw"];
				$elem_wnl_value=$row["wnl_value"];
				$elem_se_Ee_prev = $row["se_ee"];
				
				if(!empty($row["idoc_drawing_id"])) {
					$drawdocId = $row["ee_id"];
				}elseif( !empty($row["ee_drawing"]) ){
					$ardrawApp = array($row["ee_drawing"]);
				}
				
			}
			//BG
			$bgColor_external = "bgSmoke";
		}			
		
		//Color
		$flgSe_Ee_Od = $flgSe_Ee_Os = "";
		$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
		if(!isset($bgColor_external)){
			if(!empty($elem_se_Ee)){
				$tmpArrSe = $this->se_elemStatus("EXTERNAL","0",$elem_se_Ee);
				$flgSe_Ee_Od = $tmpArrSe["Con"]["od"];
				$flgSe_Ee_Os = $tmpArrSe["Con"]["os"];
				$flgSe_Draw_Od=$tmpArrSe["Draw"]["od"];
				$flgSe_Draw_Os=$tmpArrSe["Draw"]["os"];
				
			}
		}else{
			if(!empty($elem_se_Ee_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("EXTERNAL","0",$elem_se_Ee_prev);
				$flgSe_Ee_Od_prev = $tmpArrSe_prev["Con"]["od"];
				$flgSe_Ee_Os_prev = $tmpArrSe_prev["Con"]["os"];
				$flgSe_Draw_Od_prev=$tmpArrSe_prev["Draw"]["od"];
				$flgSe_Draw_Os_prev=$tmpArrSe_prev["Draw"]["os"];
			}
		}
		
		//Wnl
		$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("External");
		$wnlStringOd = $wnlStringOs = $wnlString; 
		
		if(empty($flgSe_Ee_Od) && empty($flgSe_Ee_Od_prev) && !empty($elem_wnlEeOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("External", "OD"); if(!empty($tmp)){ $wnlStringOd = $tmp;}  }
		if(empty($flgSe_Ee_Os) && empty($flgSe_Ee_Os_prev) && !empty($elem_wnlEeOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("External", "OS"); if(!empty($tmp)){ $wnlStringOs = $tmp;}  }
		
		list($elem_sumOdEE,$elem_sumOsEE) = $this->oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd,"wValOs"=>$wnlStringOs,
					"wOd"=>$elem_wnlEeOd,"sOd"=>$elem_sumOdEE,
					"wOs"=>$elem_wnlEeOs,"sOs"=>$elem_sumOsEE));
		
		//Nochanged
		if(!empty($elem_se_Ee)&&strpos($elem_se_Ee,"=1")!==false){
			$elem_noChangeEe=1;
		}			
		
		//Archive External --
		if($bgColor_external != "bgSmoke"){
		
		$arrDivArcCmn=array();
		$oChartRecArc->setChkTbl("chart_external_exam");
		$arrInpArc = array("elem_sumOdExternal"=>array("external_exam_summary",$elem_sumOdEE,"","wnlEeOd",$wnlString,$mnOd),
							"elem_sumOsExternal"=>array("sumOsEE",$elem_sumOsEE,"","wnlEeOs",$wnlString,$mnOs));
		$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdExternal"])){
			//echo $arTmpRecArc["div"]["elem_sumOdExternal"];
			$arrDivArcCmn["External"]["OD"]= $arTmpRecArc["div"]["elem_sumOdExternal"];
			$moeArc_od = $arTmpRecArc["js"]["elem_sumOdExternal"];
			$flgArcColor_od = $arTmpRecArc["css"]["elem_sumOdExternal"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdExternal"])) $elem_sumOdEE = $arTmpRecArc["curText"]["elem_sumOdExternal"];
		}else{
			$moeArc_od=$flgArcColor_od="";
		}
		//OS
		if(!empty($arTmpRecArc["div"]["elem_sumOsExternal"])){
			//echo $arTmpRecArc["div"]["elem_sumOsExternal"];
			$arrDivArcCmn["External"]["OS"]= $arTmpRecArc["div"]["elem_sumOsExternal"];
			$moeArc_os = $arTmpRecArc["js"]["elem_sumOsExternal"];
			$flgArcColor_os = $arTmpRecArc["css"]["elem_sumOsExternal"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOsExternal"])) $elem_sumOsEE = $arTmpRecArc["curText"]["elem_sumOsExternal"];
		}else{
			$moeArc_os = $flgArcColor_os = "";
		}
		
		}//
		//Archive External --					
		
		//Purged --------
			$htmPurge = $this->getPurgedHtm();
		//Purged --------
		
		//Modified Notes ----
		//if Edit is not Done && modified Notes exists
		if(!empty($mnOd) && empty($moeArc_od)){ //Od
			list($moeMN_od,$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOdExternal", $mnOd);
			//echo $tmpDiv;
			$arrDivArcCmn["External"]["OD"]=$tmpDiv;
		}else{
			$moeMN_od="";
		}
		if(!empty($mnOs) &&empty($moeArc_os)){ //Os
			list($moeMN_os,$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOsExternal", $mnOs);
			//echo $tmpDiv;
			$arrDivArcCmn["External"]["OS"]=$tmpDiv;
		}else{
			$moeMN_os="";
		}			
		
		//Drawing
		if(!empty($modi_note_Draw)){ //Os
			list($moeMN["od"]["draw"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_drawOdEE", $modi_note_Draw);
			//echo $tmpDiv;
			$arrDivArcCmn["Drawing"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["draw"]="";
		}
		//Modified Notes ----			
		
		//create common div and echo ---
		//list($moeMN,$tmpDiv) = mkDivArcCmn("External",$arrDivArcCmn);
		if($post["webservice"] != "1"){
		list($moeMN,$tmpDiv) = $this->oOnload->mkDivArcCmnNew("External",$arrDivArcCmn,$arrHx);
		}
		$echo.= $tmpDiv;
		//echo "<xmp>CHECKKKKK: ".$tmpDiv."</xmp>";			
		
		//---------

		$arr=array();
		$arr["ename"] = "External"; 
		$arr["subExm"][0] = $this->oOnload->getArrExms_ms(array("enm"=>"External",
											"sOd"=>$elem_sumOdEE,"sOs"=>$elem_sumOsEE,
											"fOd"=>$flgSe_Ee_Od,"fOs"=>$flgSe_Ee_Os,"pos"=>$elem_posEe,
											//"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
											"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
											//"mnOd"=>$moeMN_od,"mnOs"=>$moeMN_os
											));
		$arr["nochange"] = $elem_noChangeEe;
		$arr["oe"] = $oneeye;
		$arr["desc"] = $elem_txtDesc_ee;
		$arr["bgColor"] = "".$bgColor_external;
		$arr["htmPurge"] = $htmPurge;
		$arr["moeMN"] = $moeMN;
		$arr["flgGetDraw"] = $this->oOnload->onwv_isDrawingChanged(array($elem_wnlDraw,$elem_wnlDraw,$elem_ncDraw,$elem_ncDraw));
		$arr["exm_flg_se"] = array($flgSe_Ee_Od,$flgSe_Ee_Os);
		
		$arr["drawdocId"] = $drawdocId;
		$arr["drawapp"] = $ardrawApp;
		$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
		$arr["elem_hidden_fields"] = array("elem_se_Ee"=>$elem_se_Ee, "elem_posEe"=>$elem_posEe, 
									"elem_wnlEe"=>$elem_wnlEe, "elem_wnlEeOd"=>$elem_wnlEeOd, "elem_wnlEeOs"=>$elem_wnlEeOs);
							
		if($post["webservice"] == "1"){
			$echo ="";
			$str = $this->oOnload->getSummaryHTML_appwebservice($arr);			
		}else{			
			$str = $this->oOnload->getSummaryHTML($arr);			
		}
		
		//---------

		$echo.= $str;
		return $echo;

	}
	
	function load_exam($finalize_flag = 0){
		$oWv = new WorkView();
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;
		
		$OBJDrawingData = new CLSDrawingData();
		$blEnableHTMLDrawing = false;
		$blEnableHTMLDrawing = $OBJDrawingData->getHTMLDrawingDispStatus();
		$drawCntlNum=2; //This setting will decide number of drawing instances		
		list($idoc_arrDrwIcon, $idoc_htmlDrwIco) = $OBJDrawingData->drwico_getDrwIcon();	
		
		//Is Reviewable
		$isReviewable = $this->isChartReviewable($_SESSION["authId"]);
		
		//
		$elem_per_vo =  ChartPtLock::is_viewonly_permission();
		$ProClr = User::getProviderColors();//
		//$logged_user_type = $_SESSION["logged_user_type"];
		$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
		
		//default
		$arrTabs = array("Con"=>"External","Draw"=>"Drawing");
		$elem_editMode="0";
		$elem_eeId="";
		$elem_wnl=0;
		$elem_examDate=date("Y-m-d H:i:s");
		$myflag=false;
		$elem_isPositive=0;
		$elem_noChange=0;
		$elem_wnlEeOd=0;
		$elem_wnlEeOs=0;

		//
		$elem_wnlEe = "0";
		$elem_wnlDraw = "0";
		$elem_posEe = "0";
		$elem_posDraw = "0";
		$elem_ncEe = "0";
		$elem_ncDraw = "0";
		$elem_wnlDrawOd = "0";
		$elem_wnlDrawOs = "0";
		
		$cls_Trauma = "";
		$arow_Trauma = $html_entity_hide;
		
		//obj
		//DOS
		$elem_dos=$this->getDos(1);
		
		// Extract Values
		$sql = "SELECT * FROM chart_external_exam WHERE form_id  = '".$form_id."' AND patient_id='".$patient_id."' AND purged = '0' ";
		$row = sqlQuery($sql);		
		if($row==false){
		//if(mysql_num_rows($res)<=0){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
			//$res = valNewRecordExter($patient_id);
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
		}
		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			//$res = valNewRecordExter($patient_id, " * ", "1");
			$res = $this->getLastRecord(" * ",1,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
		}
		
		if($row!=false){
		//for($i=0;$row=sqlFetchArray($res);$i++){
			if($myflag){
				$elem_editMode=0;
				
			}else{
				$elem_editMode=1;
				$elem_examDate=$row["exam_date"];
			}
			$elem_eeId_LF =$elem_eeId=$row["ee_id"];
			//$elem_formId=$row["form_id"];
			$elem_patientId=$patient_id;//$row["patient_id"];
			
			$elem_wnl=$row["wnl"];
			$elem_notApplicable=$row["not_applicable"];
			if($elem_editMode==1){
				$elem_noChange=$row["examined_no_change"];
				$elem_ncEe = $row["ncEe"];
				$elem_ncDraw = $row["ncDraw"];
			}
			$elem_externalOdDrawing=$row["ee_drawing"];
			//$elem_externalOdDrawing="";
			$elem_eeDesc_od=$row["ee_desc"];
			$external_exam=stripslashes($row["external_exam"]);
			$external_exam_os= stripslashes($row["external_exam_os"]);
			$custom_field =  stripslashes($row["custom_field"]);
			
			/*
			$arrExternalExam = getXmlMenuArray($external_exam);
			$retVOd = getXmlValuesExtracted($arrExternalExam);
			$arrExternalExamOs = getXmlMenuArray($external_exam_os);
			$retVOs = getXmlValuesExtracted($arrExternalExamOs);
			*/

			$arr_vals_ee_od = $oExamXml->extractXmlValue($external_exam);
			extract($arr_vals_ee_od);			
			$arr_vals_ee_os = $oExamXml->extractXmlValue($external_exam_os);
			extract($arr_vals_ee_os);
			
			$elem_isPositive = $row["isPositive"];	
			$elem_wnlEeOd = $row["wnlEeOd"];
			$elem_wnlEeOs = $row["wnlEeOs"];
			$elem_descExternal = $row["descExternal"];
			$elem_statusElements = ($elem_editMode==0) ? "" : $row["statusElem"];

			$external_exam_summary = trim($row["external_exam_summary"]);
			$sumOsEE = trim($row["sumOsEE"]);
			
			$elem_wnlEe = $row["wnlEe"];
			$elem_wnlDraw = $row["wnlDraw"];
			$elem_posEe = $row["posEe"];
			$elem_posDraw = $row["posDraw"];
			$elem_wnlDrawOd = $row["wnlDrawOd"];
			$elem_wnlDrawOs = $row["wnlDrawOs"];	
			$elem_eeDesc_os = $row["ee_desc_os"];	
			$dbIdocDrawingId = $row["idoc_drawing_id"];
			$intDrawingInsertUpdateFrom = $row["drawing_insert_update_from"];
			//UT Elems 
			$elem_utElems = ($elem_editMode==1) ? $row["ut_elem"] : "" ;  
			if($dbIdocDrawingId != ""){
				$arrDrwaingData = array();
				$arrDrwaingData = $OBJDrawingData->getHTMLDrawingData($dbIdocDrawingId, 1);
				//pre($arrDrwaingData,1);
				//$dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $imgDB = "";
				//list($dbTollImage, $dbPatTestName, $dbPatTestId, $dbTestImg, $imgDB) = $OBJDrawingData->getHTMLDrawingData($dbIdocDrawingId, 1);
			}
			
		}
		
		//Set Change Indicator values -----
		$arrCI = array("elem_chng_divCon_Od","elem_chng_divCon_Os","elem_chng_divDraw_Od","elem_chng_divDraw_Os");
		for($i=0;$i<4;$i++){
			$tmp = $arrCI[$i];
			if(!empty($elem_statusElements) && (strpos($elem_statusElements,"".$arrCI[$i]."=1") !== false)){				
				$$tmp="1";
			}else{
				$$tmp="0";
			}
		}
		//Set Change Indicator values -----
		
		//Set Links Colors for sub Exams --
		$cls_7thNerveParesis = $cls_5thNerve = $cls_Trauma = "sbGrpOpen";
		$arow_7thNerveParesis =$arow_5thNerve=$arow_Trauma = " glyphicon-menu-up";
		//--		
		
		//Decide Applet
		$newApplet = true;
		
		//view only
		$optlock = new ChartPtLock();
		$elem_per_vo = $optlock->is_viewonly_permission();
		
		//Drawings
		$intDrawingFormId = $form_id;
		$intDrawingExamId = $elem_eeId;
		$strScanUploadfor = "EXTERNAL_DSU";
		
		
		##
		header('Content-Type: text/html; charset=utf-8');
		##
		$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/external.php";
		include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");	
	}	
}
?>