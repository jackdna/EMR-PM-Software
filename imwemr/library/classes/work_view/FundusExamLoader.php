<?php
class FundusExamLoader extends FundusExam{
	private $oOnload;
	public function __construct($pid, $fid){
		$this->oOnload =  new Onload();
		parent::__construct($pid,$fid);
	}

	function getPurgedHtm(){
		$patient_id = $this->pid;
		$form_id = $this->fid;
		$htmPurge = "";
		$oOptNrv = new OpticNerve($patient_id, $form_id);
		$ar_optic_nrv_all = $oOptNrv->getPurgedExm();

		$oVitreous = new Vitreous($patient_id, $form_id);
		$ar_vitreous_all = $oVitreous->getPurgedExm();

		$oMac = new Macula($patient_id, $form_id);
		$ar_macula_all = $oMac->getPurgedExm();

		$oBV = new BloodVessels($patient_id, $form_id);
		$ar_bv_all = $oBV->getPurgedExm();

		$oPeriphery = new Periphery($patient_id, $form_id);
		$ar_periphery_all = $oPeriphery->getPurgedExm();

		$oRetinalExam = new RetinalExam($patient_id, $form_id);
		$ar_retinal_exm_all = $oRetinalExam->getPurgedExm();

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$ar_chart_draw_all = $oChartDraw->getPurgedExm();

		//
		$len = count($ar_retinal_exm_all);

		//
		for($i=0; $i<$len; $i++){

		$ar_optic_nrv = $ar_optic_nrv_all[$i];
		$ar_vitreous = $ar_vitreous_all[$i];
		$ar_macula = $ar_macula_all[$i];
		$ar_bv = $ar_bv_all[$i];
		$ar_periphery = $ar_periphery_all[$i];
		$ar_retinal_exm = $ar_retinal_exm_all[$i];
		$ar_chart_draw = $ar_chart_draw_all[$i];

		// FUNDUS --
		$subExm = array_merge($ar_optic_nrv["subExm"], $ar_vitreous["subExm"], $ar_macula["subExm"], $ar_bv["subExm"], $ar_periphery["subExm"], $ar_retinal_exm["subExm"], $ar_chart_draw["subExm"]);
		$seList = array_merge($ar_optic_nrv["seList"], $ar_vitreous["seList"], $ar_macula["seList"], $ar_bv["seList"], $ar_periphery["seList"], $ar_retinal_exm["seList"], $ar_chart_draw["seList"]);
		$elem_noChange = (!empty($ar_optic_nrv["nochange"]) || !empty($ar_vitreous["nochange"]) || !empty($ar_macula["nochange"]) || !empty($ar_bv["nochange"]) || !empty($ar_periphery["nochange"]) || !empty($ar_retinal_exm["nochange"]) || !empty($ar_chart_draw["nochange"])) ? "1" : "0";
		$bgColor = (!empty($ar_optic_nrv["bgColor"]) && !empty($ar_vitreous["bgColor"]) && !empty($ar_macula["bgColor"]) && !empty($ar_bv["bgColor"]) && !empty($ar_periphery["bgColor"]) && !empty($ar_retinal_exm["bgColor"]) && !empty($ar_chart_draw["bgColor"])) ? "bgSmoke" : "" ;
		$examdate = $ar_chart_draw["examdate"]; //draw date
		$drawdocId = $ar_chart_draw["drawdocId"];
		$drawapp = $ar_chart_draw["drawapp"];
		$drawSE =$ar_chart_draw["drawSE"];
		$flgGetDraw =$ar_chart_draw["flgGetDraw"];
		$purgerId = $ar_retinal_exm["purgerId"];
		$purgeTime = $ar_retinal_exm["purgeTime"];
		//--
		//--

		$arr=array();
		$arr["ename"] = "Fundus";
		$arr["subExm"] = $subExm;
		$arr["seList"] = $seList;
		$arr["nochange"] = $elem_noChange;
		$arr["oe"] = $oneeye;
		//$arr["desc"] = $elem_txtDesc_la;
		$arr["bgColor"] = "".$bgColor;
		$arr["drawdocId"] = $drawdocId;
		$arr["drawapp"] = $ardrawApp;
		$arr["drawSE"] = $drawSE;
		$arr["examdate"] = $examdate;
		$arr["purgerId"] = $purgerId;
		$arr["purgeTime"] = $purgeTime;
		$arr["flgGetDraw"] = $flgGetDraw;
		$arr["exm_flg_se"] = array($flgSe_RV_Od,$flgSe_RV_Os);
		$htmPurge .= $this->oOnload->getSummaryHTML_purged($arr);

		}

		return $htmPurge;

	}

	function getWorkViewSummery($post){

		$oneeye = $post["oe"]; //one eye

		$patient_id = $this->pid;
		$form_id = $this->fid;

		//Get Template Procedures ---
		$arrTempProc=array("All");
		if(isset($post["artemp"])&&!empty($post["artemp"])){
			$arrTempProc=$post["artemp"];
		}
		//Get Template Procedures ---

		//Get Exam info --
		$oOptNrv = new OpticNerve($patient_id, $form_id);
		$ar_optic_nrv = $oOptNrv->getWorkViewSummery($post);

		$oVitreous = new Vitreous($patient_id, $form_id);
		$ar_vitreous = $oVitreous->getWorkViewSummery($post);

		//$ar_macula["subExm"] = $ar_periphery["subExm"] = $ar_blood_vessels["subExm"] =
		//$ar_macula["seList"] = $ar_periphery["seList"] = $ar_blood_vessels["seList"] =
		//$ar_macula["arrHx"] = $ar_periphery["arrHx"] = $ar_blood_vessels["arrHx"] = array();

		$oMac = new Macula($patient_id, $form_id);
		$ar_macula = $oMac->getWorkViewSummery($post);

		$oPeriphery = new Periphery($patient_id, $form_id);
		$ar_periphery = $oPeriphery->getWorkViewSummery($post);

		$oBV = new BloodVessels($patient_id, $form_id);
		$ar_blood_vessels = $oBV->getWorkViewSummery($post);

		$oRetinalExam = new RetinalExam($patient_id, $form_id);
		$ar_retinal_exm = $oRetinalExam->getWorkViewSummery($post);

		$oChartDraw = new ChartDraw($patient_id, $form_id, $this->examName);
		$ar_chart_draw = $oChartDraw->getWorkViewSummery($post);

		// Fundus --
		$subExm = array(); $arrHx = array();
		if(in_array("Opt. Nev",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_optic_nrv["subExm"]);
			$arrHx = array_merge($arrHx, $ar_optic_nrv["arrHx"]);
		}
		if(in_array("Vitreous",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_vitreous["subExm"]);
			$arrHx = array_merge($arrHx, $ar_vitreous["arrHx"]);
		}

		if(in_array("Macula",$arrTempProc) ||  in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_macula["subExm"]);
			$arrHx = array_merge($arrHx, $ar_macula["arrHx"]);
		}

		if(in_array("Blood Vessels",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_blood_vessels["subExm"]);
			$arrHx = array_merge($arrHx, $ar_blood_vessels["arrHx"]);
		}

		if(in_array("Periphery",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_periphery["subExm"]);
			$arrHx = array_merge($arrHx, $ar_periphery["arrHx"]);
		}

		//if($ar_macula["elem_bv_version"]=="old" || $ar_periphery["elem_peri_version"]=="old"){ //|| $ar_blood_vessels["elem_macula_version"]=="old"



		//}else{
			if(in_array("Retinal Exam",$arrTempProc) || in_array("All",$arrTempProc)){
				$subExm = array_merge($subExm, $ar_retinal_exm["subExm"]);
				$arrHx = array_merge($arrHx, $ar_retinal_exm["arrHx"]);
			}
		//}

		if(in_array("DrawFundus",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_chart_draw["subExm"]);
		}

		$seList = array_merge($ar_optic_nrv["seList"], $ar_macula["seList"], $ar_periphery["seList"], $ar_blood_vessels["seList"], $ar_vitreous["seList"], $ar_retinal_exm["seList"],  $ar_chart_draw["seList"]);
		$elem_noChange = (!empty($ar_optic_nrv["nochange"]) || !empty($ar_macula["nochange"]) || !empty($ar_periphery["nochange"]) || !empty($ar_blood_vessels["nochange"]) || !empty($ar_retinal_exm["nochange"]) || !empty($ar_vitreous["nochange"]) || !empty($ar_chart_draw["nochange"])) ? "1" : "0";
		$bgColor = (!empty($ar_optic_nrv["bgColor"]) && !empty($ar_macula["bgColor"])  && !empty($ar_periphery["bgColor"]) && !empty($ar_blood_vessels["bgColor"]) && !empty($ar_retinal_exm["bgColor"]) && !empty($ar_vitreous["bgColor"]) && !empty($ar_chart_draw["bgColor"])) ? "bgSmoke" : "" ;
		// && !empty($ar_blood_vessels["bgColor"])
		$examdate = $ar_chart_draw["examdate"]; //draw date
		$drawdocId = $ar_chart_draw["drawdocId"];
		$drawapp = $ar_chart_draw["drawapp"];
		$drawSE =$ar_chart_draw["drawSE"];
		$flgGetDraw =$ar_chart_draw["flgGetDraw"];
		$draw_type = $ar_chart_draw["draw_type"];
		$lens_used = $ar_retinal_exm["lens_used"];

		$elem_periNotExamined = $ar_retinal_exm["elem_periNotExamined"];
		$elem_peri_ne_eye = $ar_retinal_exm["elem_peri_ne_eye"];
		$elem_rvcd = $ar_optic_nrv["elem_rvcd"];

		if($post["webservice"] != "1"){

			list($moeMN,$tmpDiv) = $this->oOnload->mkDivArcCmnNew($this->examName,array(),$arrHx);
			$echo.= $tmpDiv;
		}
		//--

		$arr=array();
		$arr["ename"] = "Fundus";
		$arr["subExm"] = $subExm;
		$arr["seList"] = $seList;
		$arr["nochange"] = $elem_noChange;
		$arr["oe"] = $oneeye;
		$arr["desc"] = $elem_txtDesc;
		$arr["bgColor"] = "".$bgColor;
		$arr["drawdocId"] = $drawdocId;
		$arr["drawapp"] = $ardrawApp;
		$arr["drawSE"] = $drawSE;
		$arr["examdate"] = $examdate;
		$arr["htmPurge"] = $this->getPurgedHtm();
		$arr["moeMN"] = $moeMN;
		$arr["flgGetDraw"] = $flgGetDraw;
		$arr["exm_flg_se"] = array($flgSe_RV_Od,$flgSe_RV_Os); //??
		$arr["elem_periNotExamined"] = $elem_periNotExamined;
		$arr["elem_peri_ne_eye"] = $elem_peri_ne_eye;//Send if Not Prev value
		$arr["elem_rvcd"] = $elem_rvcd;
		$arr["draw_type"] = $draw_type;
		$arr["lens_used"] = $lens_used;

		//echo "<pre>";
		//print_r($arr);
		//exit();


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

		//obj work view
		$owv = new WorkView();
		$arrMeasure=array("Absent","Present","Mild","Moderate","Severe");
		$myflag=false;
		$elem_editMode="0";
		$elem_opticId="";
		$elem_wnl=0;
		$elem_wnlRV=0;
		$elem_examDate=$elem_examDateOptic=date("Y-m-d H:i:s");
		$tabId = $_GET['tabId'];
		$elem_isPositive = "0";
		$elem_isPositiveRV = "0";
		$elem_patientId=$patient_id;

		$elem_wnlVitreous = "0";
		$elem_wnlRetinal = "0";

		$elem_wnlMacula = "0";
		$elem_wnlPeri = "0";
		$elem_wnlBV = "0";

		$elem_wnlDraw = "0";
		$elem_wnlOptic = "0";

		$elem_posVitreous = "0";
		$elem_posRetinal = "0";

		$elem_posMacula = "0";
		$elem_posPeri = "0";
		$elem_posBV = "0";

		$elem_posDraw = "0";
		$elem_posOptic = "0";

		$elem_ncVitreous = "0";
		$elem_ncRetinal = "0";

		$elem_ncMacula = "0";
		$elem_ncPeri = "0";
		$elem_ncBV = "0";

		$elem_ncDraw = "0";
		$elem_ncOptic = "0";
		$elem_noChange=0;
		$elem_noChangeRv=0;

		$elem_wnlVitreousOd="0";
		$elem_wnlVitreousOs="0";
		$elem_wnlRetinalOd="0";
		$elem_wnlRetinalOs="0";

		$elem_wnlMaculaOd="0";
		$elem_wnlMaculaOs="0";
		$elem_wnlPeriOd="0";
		$elem_wnlPeriOs="0";
		$elem_wnlBVOd="0";
		$elem_wnlBVOs="0";

		$elem_wnlDrawOd="0";
		$elem_wnlDrawOs="0";
		$elem_wnlOpticOd=0;
		$elem_wnlOpticOs=0;
		$elem_chartTemplateId = 0;
		$elem_retina_version="new";
		$elem_peri_ne_eye="";
		$elem_statusElements = "";

		//Dis None
		$cls_PeriDeg=$cls_VasOcc=$cls_DRRet=$cls_WAMD=$cls_CD = $cls_ARMD = $cls_macARMD = $cls_periPDeg = $cls_DRBV = $cls_bvVasOcc = "";
		$arow_PeriDeg=$arow_VasOcc=$arow_DRRet=$arow_WAMD=$arow_CD = $arow_ARMD = $arow_macARMD = $arow_periPDeg = $arow_DRBV = $arow_bvVasOcc = "glyphicon-menu-down";
		$blDrwaingGray = false;
		$html_entity_show="glyphicon-menu-up";

		//DOS
		$elem_dos=$this->getDos(1);

		//Get Exam info --
		$oOptNrv = new OpticNerve($patient_id, $elem_formId);
		$ar_optic_nrv = $oOptNrv->get_chart_info($elem_dos);
		extract($ar_optic_nrv);

		$oMac = new Macula($patient_id, $elem_formId);
		$ar_macula = $oMac->get_chart_info($elem_dos);
		extract($ar_macula);

		$oPeriphery = new Periphery($patient_id, $elem_formId);
		$ar_periphery = $oPeriphery->get_chart_info($elem_dos);
		extract($ar_periphery);

		$oBV = new BloodVessels($patient_id, $elem_formId);
		$ar_blood_vessels = $oBV->get_chart_info($elem_dos);
		extract($ar_blood_vessels);

		$oRetinalExam = new RetinalExam($patient_id, $elem_formId);
		$ar_retinal_exm = $oRetinalExam->get_chart_info($elem_dos);
		extract($ar_retinal_exm);

		$oVitreous = new Vitreous($patient_id, $elem_formId);
		$ar_vitreous = $oVitreous->get_chart_info($elem_dos);
		extract($ar_vitreous);

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$ar_chart_draw = $oChartDraw->get_chart_draw_info($elem_dos);
		extract($ar_chart_draw);

		//Combine
		$elem_statusElements .= $elem_statusElements_optic;
		$elem_statusElements .= $elem_statusElementsMacula;
		$elem_statusElements .= $elem_statusElementsPeri;
		$elem_statusElements .= $elem_statusElementsBV;
		$elem_statusElements .= $elem_statusElementsRetinal;
		$elem_statusElements .= $elem_statusElementsVitreous;
		$elem_statusElements .= $elem_statusElementsDraw;
		$elem_utElems .= $elem_utElemsOptic;
		$elem_utElems .= $elem_utElemsMacula;
		$elem_utElems .= $elem_utElemsPeri;
		$elem_utElems .= $elem_utElemsBV;
		$elem_utElems .= $elem_utElemsRetinal;
		$elem_utElems .= $elem_utElemsVitreous;
		$elem_utElems .= $elem_utElemsDraw;
		$elem_editMode = (!empty($elem_editModeVitreous)) ? "1" : "0";

		//exit($elem_statusElements);

		//-------------------

		//
		//$elem_retina_version="old";
		if($elem_retina_version=="old"){
			$arrTabs = array("6"=>"Optic Nerve","2"=>"Macula","1"=>"Vitreous","3"=>"Periphery","4"=>"Blood Vessels","5"=>"Drawing");
		}else{
			$arrTabs = array("6"=>"Optic Nerve","1"=>"Vitreous","2"=>"Macula", "4"=>"Vessels", "3"=>"Periphery", "7"=>"Retinal Exam","5"=>"Drawing", "8"=>"Draw ON", "9"=>"Draw MA");
		}

		//Get Template Procedures ---

		$arrTempProc=array("All");
		$oChartTemp = new ChartTemp();
		$elem_chartTemplateId = $oChartTemp->getChartTempId($patient_id,$elem_formId);
		if(!empty($elem_chartTemplateId)){
			$tmp = $oChartTemp->getTempInfo($elem_chartTemplateId);
			if(!empty($tmp[1])){
				$elem_chartTempName = $tmp[1];
				//$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array();
				//check for logged in user : physician or technician and chart finalized
				//Please remember Scribe are same as Physician i.e. their view should be same as Physician View: userid = 13
				//Can see physician view
				if( in_array($logged_user_type, $GLOBALS['arrValidCNPhy']) || ($finalize_flag == 1 && $isReviewable==false) || $logged_user_type == 13 || !empty($_SESSION["flg_phy_view"])){
					$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array(); //Phy
				}else{
					$arrTempProc = (!empty($tmp[4])) ? explode(",", stripslashes($tmp[4])) : array(); //Tech
				}
			}

			//Drawing Stop
			if(isset($arrTempProc) && !in_array("DrawFundus",$arrTempProc) && !in_array("All",$arrTempProc)){
				$blEnableHTMLDrawing=false;
			}
		}

		//Get Template Procedures ---

		//Set Change Indicator values -----
		//$elem_statusElements = $elem_statusElements_rv."".$elem_statusElements_optic;
		for($i=1;$i<=7;$i++){

			$nmIdOd = "elem_chng_div".$i."_Od";
			$nmIdOs = "elem_chng_div".$i."_Os";

			//Od
			if(!empty($elem_statusElements) && (strpos($elem_statusElements,"".$nmIdOd."=1") !== false)){
				$$nmIdOd="1";
			}else{
				$$nmIdOd="0";
			}

			//Os
			if(!empty($elem_statusElements) && (strpos($elem_statusElements,"".$nmIdOs."=1") !== false)){
				$$nmIdOs="1";
			}else{
				$$nmIdOs="0";
			}

		}
		//Set Change Indicator values -----
		// Change Indicate Elements -----------------
		$strPtrnGray = "";
		$elem_changeInd = "";
		for($i=1;$i<=7;$i++){
			$nmIdOd = "elem_chng_div".$i."_Od";
			$nmIdOs = "elem_chng_div".$i."_Os";
			$elem_changeInd .= "<input type=\"hidden\" name=\"".$nmIdOd."\" id=\"".$nmIdOd."\" value=\"".$$nmIdOd."\">".
							  "<input type=\"hidden\" name=\"".$nmIdOs."\" id=\"".$nmIdOs."\" value=\"".$$nmIdOs."\">";
			//
			if(empty($$nmIdOd) && empty($$nmIdOs)){
				$strPtrnGray .= "#div".$i." :input,";
			}else{
				if(empty($$nmIdOd)){
					$strPtrnGray .= "#div".$i." :input[name*='Od_'],#div".$i." :input[name$=Od],";
				}else if(empty($$nmIdOs)){
					$strPtrnGray .= "#div".$i." :input[name*='Os_'],#div".$i." :input[name$=Os],";
				}
			}
		}

		if(!empty($strPtrnGray) && (preg_match("/,$/",$strPtrnGray))){
			$strPtrnGray = preg_replace("/,$/","",$strPtrnGray);
		}
		// Change Indicate Elements -----------------

		//Set Links Colors for sub Exams --
		if($elem_retina_version == "new"){

			if(!empty($retinal_od_summary) || !empty($retinal_os_summary)){
				//$arDryArmd = array("Dry AMD","Drusen","RPE Changes","Geographic Atrophy","Retinal Pigment Epithelial Detachment");
				//$arWetArmd = array("Wet AMD","CNVM","SRH","Retinal Pigment Epithelial Detachment","Subretinal Fluid");
				$arAmd = array("AMD","Drusen","RPE Changes","Geographic Atrophy","Retinal Pigment Epithelial Detachment", "CNVM", "SRH", "Subretinal Fluid");
				$arDr = array("DR","NPDR", "Diabetic macular edema", "Hard Exudate", "Cotton Wool Spots", "Focal Laser","PRP", "Neovascularization" ); //,"Subretinal Fluid"
				$arVasOcc = array("Vascular Occlusion", "BRAO", "CRAO", "BRVO", "CRVO");
				$arPeriDeg = array("Atrophic changes", "Equatorial Drusen", "Lattice Degeneration", "Reticular Changes", "Retinoschisis", "WWP");

				if(!empty($retinal_od_summary)){
					$tmp = str_replace("Peripheral Neovascularization","",$retinal_od_summary); //Confiction removal of Neovascularization
					$stArmd_od = ($owv->hasArrVal($tmp,$arAmd) != false) ? "sbGrpDone" : "";
					//$st_WAMD_od = (hasArrVal($tmp,$arWetArmd) != false) ? "sbGrpDone" : "";
					$stDrR_od = ($owv->hasArrVal($tmp,$arDr) != false) ? "sbGrpDone" : "";
					$st_VasOcc_od = ($owv->hasArrVal($tmp,$arVasOcc) != false) ? "sbGrpDone" : "";
					$st_PeriDeg_od = ($owv->hasArrVal($tmp,$arPeriDeg) != false) ? "sbGrpDone" : "";
				}
				if(!empty($retinal_os_summary)){
					$tmp = str_replace("Peripheral Neovascularization","",$retinal_os_summary); //
					$stArmd_os = ($owv->hasArrVal($tmp,$arAmd) != false) ? "sbGrpDone" : "";
					//$st_WAMD_os = (hasArrVal($tmp,$arWetArmd) != false) ? "sbGrpDone" : "";
					$stDrR_os = ($owv->hasArrVal($tmp,$arDr) != false) ? "sbGrpDone" : "";
					$st_VasOcc_os = ($owv->hasArrVal($tmp,$arVasOcc) != false) ? "sbGrpDone" : "";
					$st_PeriDeg_os = ($owv->hasArrVal($tmp,$arPeriDeg) != false) ? "sbGrpDone" : "";
				}

				//echo "<br/>$retinal_od_summary<br/>";
				//echo "<br/>$retinal_os_summary<br/>";
				//echo "<br/>  $stArmd_os  -  $stArmd_od <br/>";

				//
				if($stArmd_od == "sbGrpDone" || $stArmd_os == "sbGrpDone"){
					$cls_ARMD = "sbGrpOpen";
					$arow_ARMD = $html_entity_show;
				}
				//
				/*if($st_WAMD_od == "sbGrpDone" || $st_WAMD_os == "sbGrpDone"){
					$cls_WAMD = "sbGrpOpen";
					$arow_WAMD = $html_entity_show;
				}*/
				//
				if($stDrR_od == "sbGrpDone" || $stDrR_os == "sbGrpDone"){
					$cls_DRRet = "sbGrpOpen";
					$arow_DRRet = $html_entity_show;
				}
				//
				if($st_VasOcc_od == "sbGrpDone" || $st_VasOcc_os == "sbGrpDone"){
					$cls_VasOcc = "sbGrpOpen";
					$arow_VasOcc = $html_entity_show;
				}
				//
				if($st_PeriDeg_od == "sbGrpDone" || $st_PeriDeg_os == "sbGrpDone"){
					$cls_PeriDeg = "sbGrpOpen";
					$arow_PeriDeg = $html_entity_show;
				}
			}

			//
			if(!empty($macula_od_summary) || !empty($macula_os_summary)){
				$stArmd_od = $stArmd_os = "";

				$arAmd = array("AMD","RPE Changes","Geographic Atrophy", "CNVM", "SRH", "Subretinal Fluid");

				$tmp = trim($elem_macOd_armd_drusen_neg.$elem_macOd_armd_drusen_T.
										$elem_macOd_armd_drusen_pos1.$elem_macOd_armd_drusen_pos2.
										$elem_macOd_armd_drusen_pos3.$elem_macOd_armd_drusen_pos4.
										$elem_macOd_armd_drusen_F.$elem_macOd_armd_drusen_foveal.
										$elem_macOd_armd_drusen_hard.$elem_macOd_armd_drusen_soft.

										$elem_macOs_armd_drusen_neg.$elem_macOs_armd_drusen_T.
										$elem_macOs_armd_drusen_pos1.$elem_macOs_armd_drusen_pos2.
										$elem_macOs_armd_drusen_pos3.$elem_macOs_armd_drusen_pos4.
										$elem_macOs_armd_drusen_F.$elem_macOs_armd_drusen_foveal.
										$elem_macOs_armd_drusen_hard.$elem_macOs_armd_drusen_soft	);

				if(!empty($tmp)){
					$arAmd[] = "Drusen";
				}

				$tmp = trim($elem_macOd_armd_rped_Absent.$elem_macOd_armd_rped_Present.
										$elem_macOs_armd_rped_Absent.$elem_macOs_armd_rped_Present);
				if(!empty($tmp)){
						$arAmd[] = "Retinal Pigment Epithelial Detachment";
				}

				if(!empty($macula_od_summary)){
					$stArmd_od = ($owv->hasArrVal($macula_od_summary,$arAmd) != false) ? "sbGrpDone" : "";
					//$stDr_od = ($owv->hasArrVal($macula_od_summary,$arDr) != false) ? "sbGrpDone" : "";
				}
				if(!empty($macula_os_summary)){
					$stArmd_os = ($owv->hasArrVal($macula_os_summary,$arAmd) != false) ? "sbGrpDone" : "";
					//$stDr_os = ($owv->hasArrVal($macula_os_summary,$arDr) != false) ? "sbGrpDone" : "";
				}

				//
				if($stArmd_od == "sbGrpDone" || $stArmd_os == "sbGrpDone"){
					$cls_macARMD = "sbGrpOpen";
					$arow_macARMD = $html_entity_show;
				}
			}

			//
			if(!empty($periphery_od_summary) || !empty($periphery_os_summary)){

				//$arDrP = array("DR","BDR","Exudate","Cotton Wool Spots","Focal Scar","PRP Scar","PDR(Neovascularization)");
				//$arPd = array("Atrophic-Changes","Drusen","Lattice");
				$arPeriDeg = array("Atrophic changes", "Equatorial Drusen", "Lattice Degeneration", "Reticular Changes", "Retinoschisis", "WWP");

				if(!empty($periphery_od_summary)){
					$st_PeriDeg_od = ($owv->hasArrVal($periphery_od_summary,$arPeriDeg) != false) ? "sbGrpDone" : "";
				}

				if(!empty($periphery_os_summary)){
					$st_PeriDeg_os = ($owv->hasArrVal($periphery_od_summary,$arPeriDeg) != false) ? "sbGrpDone" : "";
				}

				//
				if($st_PeriDeg_od == "sbGrpDone" || $st_PeriDeg_os == "sbGrpDone"){
					$cls_periPDeg = "sbGrpOpen";
					$arow_periPDeg = $html_entity_show;
				}

			}

			//Bood Vessels
			if(!empty($blood_vessels_od_summary) || !empty($blood_vessels_os_summary)){
				$arDr = array("DR","NPDR", "Diabetic macular edema", "Hard Exudate", "Cotton Wool Spots", "Focal Laser","PRP", "Neovascularization" ); //,"Subretinal Fluid"
				$arVasOcc = array("Vascular Occlusion", "BRAO", "CRAO", "BRVO", "CRVO");

				if(!empty($blood_vessels_od_summary)){
					$stDrR_od = ($owv->hasArrVal($blood_vessels_od_summary,$arDr) != false) ? "sbGrpDone" : "";
					$st_VasOcc_od = ($owv->hasArrVal($blood_vessels_od_summary,$arVasOcc) != false) ? "sbGrpDone" : "";
				}

				if(!empty($blood_vessels_os_summary)){
					$stDrR_os = ($owv->hasArrVal($blood_vessels_os_summary,$arDr) != false) ? "sbGrpDone" : "";
					$st_VasOcc_os = ($owv->hasArrVal($blood_vessels_os_summary,$arVasOcc) != false) ? "sbGrpDone" : "";
				}

				//
				if($stDrR_od == "sbGrpDone" || $stDrR_os == "sbGrpDone"){
					$cls_DRBV = "sbGrpOpen";
					$arow_DRBV = $html_entity_show;
				}
				//
				if($st_VasOcc_od == "sbGrpDone" || $st_VasOcc_os == "sbGrpDone"){
					$cls_bvVasOcc = "sbGrpOpen";
					$arow_bvVasOcc = $html_entity_show;
				}
			}

		}else if($elem_retina_version == "old"){


		} // OLD

		$cls_CD = "sbGrpOpen";
		$arow_CD = $html_entity_show;

		//Set Links Colors for sub Exams --

		//GetExamExtension --
		$arr_exm_ext_htm = array();
		$arr_exm_ext = $oExamXml->get_exam_extension("Fundus");
		if(count($arr_exm_ext)>0){
			foreach($arr_exm_ext as $k_exm => $v_tab){
				if(count($v_tab)>0){
					foreach($v_tab as $k_exm_find => $v_file){
						if(count($v_file)>0){
							foreach($v_file as $k_tmp => $exmext_file_path){

								ob_start();
								include($exmext_file_path);
								$tmp = ob_get_contents();
								ob_end_clean();
								$ptmp="";
								if(isset($arr_exm_ext_htm[$k_exm][$k_exm_find])&&!empty($arr_exm_ext_htm[$k_exm][$k_exm_find])){
									$ptmp = $arr_exm_ext_htm[$k_exm][$k_exm_find];
								}
								$arr_exm_ext_htm[$k_exm][$k_exm_find] = $ptmp.$tmp;

							}
						}
					}
				}
			}
		}
		//End GetExamExtension --

		//defualt Tab --
		$defTabKey = "6";
		if(isset($_GET["pg"]) && !empty($_GET["pg"])){
			if($_GET["pg"]=="Vitreous"){
				$defTabKey = "1";
			}else if($_GET["pg"]=="Macula"){
				$defTabKey = "2";
			}else if($_GET["pg"]=="Periphery"){
				$defTabKey = "3";
			}else if($_GET["pg"]=="bv"){
				$defTabKey = "4";
			}else if($_GET["pg"]=="ret"){
				$defTabKey = "7";
			}else if($_GET["pg"]=="Drawing"){
				$defTabKey = "5";
			}
		}

		//defualt Tab --

		//draw
		$intDrawingFormId = $elem_formId;
		$intDrawingExamId = $elem_drawId;
		$strScanUploadfor = "FUNDUS_DSU";

		//Lens Used
		$oAdmn = new Admn();
		$arr_lens_used = $oAdmn->get_lens_used();

		##
		header('Content-Type: text/html; charset=utf-8');
		##
		$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/fundus.php";
		include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");

	}
}
?>
