<?php
class SLELoader extends SLE{
	private $oOnload;
	public function __construct($pid, $fid){
		$this->oOnload =  new Onload();
		parent::__construct($pid,$fid);
	}
	function getPurgedHtm(){

		$patient_id = $this->pid;
		$form_id = $this->fid;
		$htmPurge = "";

		$oConjunctiva = new Conjunctiva($patient_id, $form_id);
		$ar_chart_conj_all = $oConjunctiva->getPurgedExm();

		$oCornea = new Cornea($patient_id, $form_id);
		$ar_chart_corn_all = $oCornea->getPurgedExm();

		$oAntChamber = new AntChamber($patient_id, $form_id);
		$ar_chart_ant_all = $oAntChamber->getPurgedExm();

		$oIris = new Iris($patient_id, $form_id);
		$ar_chart_iris_all = $oIris->getPurgedExm();

		$oLens = new Lens($patient_id, $form_id);
		$ar_chart_lens_all = $oLens->getPurgedExm();

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$ar_chart_draw_all = $oChartDraw->getPurgedExm();

		//
		$len = count($ar_chart_conj_all);
		//
		for($i=0; $i<$len; $i++){
			$ar_chart_conj = $ar_chart_conj_all[$i];
			$ar_chart_corn = $ar_chart_corn_all[$i];
			$ar_chart_ant = $ar_chart_ant_all[$i];
			$ar_chart_iris = $ar_chart_iris_all[$i];
			$ar_chart_lens = $ar_chart_lens_all[$i];
			$ar_chart_draw = $ar_chart_draw_all[$i];

			$subExm = array();
			$subExm = array_merge($ar_chart_conj["subExm"], $ar_chart_corn["subExm"], $ar_chart_ant["subExm"], $ar_chart_iris["subExm"], $ar_chart_lens["subExm"], $ar_chart_draw["subExm"]);
			$seList = array_merge($ar_chart_conj["seList"], $ar_chart_corn["seList"], $ar_chart_ant["seList"], $ar_chart_iris["seList"], $ar_chart_lens["seList"], $ar_chart_draw["seList"]);
			$elem_noChange = (!empty($ar_chart_conj["nochange"]) || !empty($ar_chart_corn["nochange"]) || !empty($ar_chart_ant["nochange"]) || !empty($ar_chart_iris["nochange"]) || !empty($ar_chart_lens["nochange"]) || !empty($ar_chart_draw["nochange"])) ? "1" : "0";
			$bgColor = (!empty($ar_chart_conj["bgColor"]) && !empty($ar_chart_corn["bgColor"]) && !empty($ar_chart_ant["bgColor"]) && !empty($ar_chart_iris["bgColor"]) && !empty($ar_chart_lens["bgColor"]) && !empty($ar_chart_draw["bgColor"])) ? "bgSmoke" : "" ;
			$examdate = $ar_chart_draw["examdate"]; //draw date
			$drawdocId = $ar_chart_draw["drawdocId"];
			$drawapp = $ar_chart_draw["drawapp"];
			$drawSE =$ar_chart_draw["drawSE"];
			$flgGetDraw =$ar_chart_draw["flgGetDraw"];
			$purgerId = $ar_chart_conj["purgerId"];
			$purgeTime = $ar_chart_conj["purgeTime"];

			$arr=array();
			$arr["ename"] = "SLE";
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
			$arr["moeMN"] = $moeMN;
			$arr["purgerId"] = $purgerId;
			$arr["purgeTime"] = $purgeTime;
			$arr["flgGetDraw"] = $flgGetDraw;
			$arr["exm_flg_se"] = array($flgSe_SLE_Od,$flgSe_SLE_Os);
			$arr["penLight"] = $elem_pen_light;
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

		$oConjunctiva = new Conjunctiva($patient_id, $form_id);
		$ar_chart_conj = $oConjunctiva->getWorkViewSummery($post);

		$oCornea = new Cornea($patient_id, $form_id);
		$ar_chart_corn = $oCornea->getWorkViewSummery($post);

		$oAntChamber = new AntChamber($patient_id, $form_id);
		$ar_chart_ant = $oAntChamber->getWorkViewSummery($post);

		$oIris = new Iris($patient_id, $form_id);
		$ar_chart_iris = $oIris->getWorkViewSummery($post);

		$oLens = new Lens($patient_id, $form_id);
		$ar_chart_lens = $oLens->getWorkViewSummery($post);

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$ar_chart_draw = $oChartDraw->getWorkViewSummery($elem_dos);

		// SLE --
		$subExm = array(); $arrHx = array();
		if(in_array("Conjunctiva",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_chart_conj["subExm"]);
			$arrHx = array_merge($arrHx, $ar_chart_conj["arrHx"]);
		}
		if(in_array("Cornea",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_chart_corn["subExm"]);
			$arrHx = array_merge($arrHx, $ar_chart_corn["arrHx"]);
		}
		if(in_array("Ant. Chamber",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_chart_ant["subExm"]);
			$arrHx = array_merge($arrHx, $ar_chart_ant["arrHx"]);
		}
		if(in_array("Iris & Pupil",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_chart_iris["subExm"]);
			$arrHx = array_merge($arrHx, $ar_chart_iris["arrHx"]);
		}
		if(in_array("Lens",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_chart_lens["subExm"]);
			$arrHx = array_merge($arrHx, $ar_chart_lens["arrHx"]);
		}

		if(in_array("DrawSLE",$arrTempProc) || in_array("All",$arrTempProc)){
			$subExm = array_merge($subExm, $ar_chart_draw["subExm"]);
		}

		$seList = array_merge($ar_chart_conj["seList"], $ar_chart_corn["seList"], $ar_chart_ant["seList"], $ar_chart_iris["seList"], $ar_chart_lens["seList"], $ar_chart_draw["seList"]);
		$elem_noChange = (!empty($ar_chart_conj["nochange"]) || !empty($ar_chart_corn["nochange"]) || !empty($ar_chart_ant["nochange"]) || !empty($ar_chart_iris["nochange"]) || !empty($ar_chart_lens["nochange"]) || !empty($ar_chart_draw["nochange"])) ? "1" : "0";
		$bgColor = (!empty($ar_chart_conj["bgColor"]) && !empty($ar_chart_corn["bgColor"]) && !empty($ar_chart_ant["bgColor"]) && !empty($ar_chart_iris["bgColor"]) && !empty($ar_chart_lens["bgColor"]) && !empty($ar_chart_draw["bgColor"])) ? "bgSmoke" : "" ;
		$examdate = $ar_chart_draw["examdate"]; //draw date
		$drawdocId = $ar_chart_draw["drawdocId"];
		$drawapp = $ar_chart_draw["drawapp"];
		$drawSE =$ar_chart_draw["drawSE"];
		$flgGetDraw =$ar_chart_draw["flgGetDraw"];

		if($post["webservice"] != "1"){

			//echo "<pre>";
			//print_r($arrHx);
			//exit();

			list($moeMN,$tmpDiv) = $this->oOnload->mkDivArcCmnNew($this->examName,array(),$arrHx);
			$echo.= $tmpDiv;
		}
		//--

		$arr=array();
		$arr["ename"] = "SLE";
		$arr["subExm"] = $subExm;
		$arr["seList"] = $seList;
		$arr["nochange"] = $elem_noChange;
		$arr["oe"] = $oneeye;
		$arr["desc"] = $elem_txtDesc_la;
		$arr["bgColor"] = "".$bgColor;
		$arr["drawdocId"] = $drawdocId;
		$arr["drawapp"] = $ardrawApp;
		$arr["drawSE"] = $drawSE;
		$arr["examdate"] = $examdate;
		$arr["htmPurge"] = $this->getPurgedHtm();
		$arr["moeMN"] = $moeMN;
		$arr["flgGetDraw"] = $flgGetDraw;
		$arr["exm_flg_se"] = array($flgSe_SLE_Od,$flgSe_SLE_Os);
		$arr["penLight"] = $elem_pen_light;

		//echo "<pre>";
		//print_r($ar_chart_draw);
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

		//Dis None
		$cls_CorDE = $cls_Dyst = $cls_CornTruma = $cls_Infect = $cls_CornEdma = $cls_PigDepo = $cls_CornSurgry ="";
		$arow_CorDE = $arow_Dyst = $arow_CornTruma = $arow_Infect = $arow_CornEdma = $arow_PigDepo = $arow_CornSurgry = "glyphicon-menu-down";
		$html_entity_show = "glyphicon-menu-up";

		//default
		$myflag=false;
		$arrTabs = array("1"=>"Conjunctiva","2"=>"Cornea","3"=>"Ant. Chamber","4"=>"Iris","5"=>"Lens","6"=>"Drawing");
		$elem_editMode="0";
		$elem_sleId="";
		$elem_wnl=0;
		$elem_examDate=date("Y-m-d H:i:s");
		$patient_not_driving = "0";
		$elem_isPositive = "0";
		$elem_wnlConj = "0";
		$elem_wnlCorn = "0";
		$elem_wnlAnt = "0";
		$elem_wnlIris = "0";
		$elem_wnlLens = "0";
		$elem_wnlDraw = "0";

		$elem_posConj = "0";
		$elem_posCorn = "0";
		$elem_posAnt = "0";
		$elem_posIris = "0";
		$elem_posLens = "0";
		$elem_posDraw = "0";

		$elem_ncConj = "0";
		$elem_ncCorn = "0";
		$elem_ncAnt = "0";
		$elem_ncIris = "0";
		$elem_ncLens = "0";
		$elem_ncDraw = "0";

		$elem_wnlConjOd = "0";
		$elem_wnlConjOs = "0";
		$elem_wnlCornOd = "0";
		$elem_wnlCornOs = "0";
		$elem_wnlAntOd = "0";
		$elem_wnlAntOs = "0";
		$elem_wnlIrisOd = "0";
		$elem_wnlIrisOs = "0";
		$elem_wnlLensOd = "0";
		$elem_wnlLensOs = "0";
		$elem_wnlDrawOd = "0";
		$elem_wnlDrawOs = "0";

		$elem_formId=$form_id;
		$elem_patientId=$patient_id;
		$elem_noChange=0;
		$dbIdocDrawingId = "";
		$elem_penLight=0;
		$elem_chartTemplateId = 0;
		//dis
		$clsDE = $clsDys = $clsTrma = $clsInf = $clsEdma = $clsPd = $clsSy = "dis_none";
		$blDrwaingGray = false;

		//DOS
		$elem_dos=$this->getDos(1);

		// Extract Values
		$oConjunctiva = new Conjunctiva($patient_id, $form_id);
		$ar_chart_conj = $oConjunctiva->get_chart_info($elem_dos);
		extract($ar_chart_conj);

		$oCornea = new Cornea($patient_id, $form_id);
		$ar_chart_corn = $oCornea->get_chart_info($elem_dos);
		extract($ar_chart_corn);

		$oAntChamber = new AntChamber($patient_id, $form_id);
		$ar_chart_ant_cham = $oAntChamber->get_chart_info($elem_dos);
		extract($ar_chart_ant_cham);

		$oIris = new Iris($patient_id, $form_id);
		$ar_chart_iris = $oIris->get_chart_info($elem_dos);
		extract($ar_chart_iris);

		$oLens = new Lens($patient_id, $form_id);
		$ar_chart_lens = $oLens->get_chart_info($elem_dos);
		extract($ar_chart_lens);

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$ar_chart_draw = $oChartDraw->get_chart_draw_info($elem_dos);
		extract($ar_chart_draw);

		//Combine
		$elem_statusElements .= $elem_statusElementsConj;
		$elem_statusElements .= $elem_statusElementsCorn;
		$elem_statusElements .= $elem_statusElementsAnt;
		$elem_statusElements .= $elem_statusElementsIris;
		$elem_statusElements .= $elem_statusElementsLens;
		$elem_statusElements .= $elem_statusElementsDraw;
		$elem_utElems .= $elem_utElemsConj;
		$elem_utElems .= $elem_utElemsCorn;
		$elem_utElems .= $elem_utElemsAnt;
		$elem_utElems .= $elem_utElemsIris;
		$elem_utElems .= $elem_utElemsLens;
		$elem_utElems .= $elem_utElemsDraw;
		$elem_editMode = (!empty($elem_editModeConj)) ? "1" : "0";

		//Get Template Procedures ---

		$arrTempProc=array("All");
		$oChartTemp = new ChartTemp();
		$elem_chartTemplateId = $oChartTemp->getChartTempId($patient_id,$form_id);
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
			if(isset($arrTempProc) && !in_array("DrawSLE",$arrTempProc) && !in_array("All",$arrTempProc)){
				$blEnableHTMLDrawing=false;
			}
		}

		//Get Template Procedures ---

		//Set Change Indicator values -----
		for($i=1;$i<=6;$i++){

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
		for($i=1;$i<=6;$i++){
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

		if(!empty($cornea_od_summary) || !empty($cornea_os_summary)){
		//Cornea
		$arDe = array("Dry Eyes","Dec TBUT","Dec. Tear Lake","SPK","Inc. Tear Lake");
		$arDys = array("Dystrophy","ABMD/MDF","Stromal","Posterior","Pterygium");
		$arTrma = array("Trauma","Abrasion","Irregular Epithelium","Foreign Body","Part. Thickness Laceration","Full Thickness Laceration");
		$arInf = array("Ulcer","Stromal Abscess","HSK","HZK");
		$arEdma = array("Edema","Epithelial (MCE)","Stromal","Folds/Striae");
		$arPd = array("Pigmentary deposits","Vortex","K-Spindle");
		$arSy = array("Surgery");

		if(!empty($cornea_od_summary)){

			$stCorDE_od = ($owv->hasArrVal($cornea_od_summary,$arDe) != false) ? "sbGrpDone" : "";
			$stDyst_od = ($owv->hasArrVal($cornea_od_summary,$arDys) != false) ? "sbGrpDone" : "";
			$stCornTruma_od = ($owv->hasArrVal($cornea_od_summary,$arTrma) != false) ? "sbGrpDone" : "";
			$stInfect_od = ($owv->hasArrVal($cornea_od_summary,$arInf) != false) ? "sbGrpDone" : "";
			$stCornEdma_od = ($owv->hasArrVal($cornea_od_summary,$arEdma) != false) ? "sbGrpDone" : "";
			$stPigDepo_od = ($owv->hasArrVal($cornea_od_summary,$arPd) != false) ? "sbGrpDone" : "";
			$stCornSurgry_od = ($owv->hasArrVal($cornea_od_summary,$arSy) != false) ? "sbGrpDone" : "";
		}

		if(!empty($cornea_os_summary)){
			$stCorDE_os = ($owv->hasArrVal($cornea_os_summary,$arDe) != false) ? "sbGrpDone" : "";
			$stDyst_os = ($owv->hasArrVal($cornea_os_summary,$arDys) != false) ? "sbGrpDone" : "";
			$stCornTruma_os = ($owv->hasArrVal($cornea_os_summary,$arTrma) != false) ? "sbGrpDone" : "";
			$stInfect_os = ($owv->hasArrVal($cornea_os_summary,$arInf) != false) ? "sbGrpDone" : "";
			$stCornEdma_os = ($owv->hasArrVal($cornea_os_summary,$arEdma) != false) ? "sbGrpDone" : "";
			$stPigDepo_os = ($owv->hasArrVal($cornea_os_summary,$arPd) != false) ? "sbGrpDone" : "";
			$stCornSurgry_os = ($owv->hasArrVal($cornea_os_summary,$arSy) != false) ? "sbGrpDone" : "";
		}

		//Display sett
		if($stCorDE_od == "sbGrpDone" || $stCorDE_os == "sbGrpDone"){
			$cls_CorDE = "sbGrpOpen";
			$arow_CorDE = $html_entity_show;
		}

		if($stDyst_od == "sbGrpDone" || $stDyst_os == "sbGrpDone"){
			$cls_Dyst = "sbGrpOpen";
			$arow_Dyst = $html_entity_show;
		}

		if($stCornTruma_od == "sbGrpDone" || $stCornTruma_os == "sbGrpDone"){
			$cls_CornTruma = "sbGrpOpen";
			$arow_CornTruma = $html_entity_show;
		}

		if($stInfect_od == "sbGrpDone" || $stInfect_os == "sbGrpDone"){
			$cls_Infect = "sbGrpOpen";
			$arow_Infect = $html_entity_show;
		}

		if($stCornEdma_od == "sbGrpDone" || $stCornEdma_os == "sbGrpDone"){
			$cls_CornEdma = "sbGrpOpen";
			$arow_CornEdma = $html_entity_show;
		}

		if($stPigDepo_od == "sbGrpDone" || $stPigDepo_os == "sbGrpDone"){
			$cls_PigDepo = "sbGrpOpen";
			$arow_PigDepo = $html_entity_show;
		}

		if($stCornSurgry_od == "sbGrpDone" || $stCornSurgry_os == "sbGrpDone"){
			$cls_CornSurgry = "sbGrpOpen";
			$arow_CornSurgry = $html_entity_show;
		}

		}

		//Bleb
		$cls_ConjBleb = "sbGrpOpen";
		$arow_ConjBleb = $html_entity_show;

		//Set Links Colors for sub Exams --

		//MultiFocal ---
		$clsmfselod=$clsmfselod_othr=$clsmfselos=$clsmfselos_othr="mfcss";
		if(!empty($elem_mfocalOd_pciol)){	$clsmfselod="";	}

		if(!empty($elem_mfocalOs_pciol)){	$clsmfselos="";}
		//MultiFocal ---

		//GetExamExtension --
		$arr_exm_ext_htm = array();
		$arr_exm_ext = $oExamXml->get_exam_extension("SLE");
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
		$defTabKey = "1";
		if(isset($_GET["pg"]) && !empty($_GET["pg"])){
			if($_GET["pg"]=="Conj"){
				$defTabKey = "1";
			}else if($_GET["pg"]=="Cornea"){
				$defTabKey = "2";
			}else if($_GET["pg"]=="AntChamber"){
				$defTabKey = "3";
			}else if($_GET["pg"]=="Iris"){
				$defTabKey = "4";
			}else if($_GET["pg"]=="lens"){
				$defTabKey = "5";
			}else if($_GET["pg"]=="Drawing"){
				$defTabKey = "6";
			}
		}

		//defualt Tab --

		//draw
		$intDrawingFormId = $form_id;
		$intDrawingExamId = $elem_drawId;
		$strScanUploadfor = "SLE_DSU";




		##
		header('Content-Type: text/html; charset=utf-8');
		##
		$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/sle.php";
		include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");
	}
}
?>
