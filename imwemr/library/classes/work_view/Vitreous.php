<?php

class Vitreous extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_vitreous";
		$this->examName="Vitreous";
	}
	
	function isRecordExists($a="",$b="",$c="",$d=""){
		return parent::isRecordExists($this->tbl);
	}

	function getRecord($sel=" * ",$a="",$b="",$c="",$d=""){
		return parent::getRecord($this->tbl,$sel);
	}

	function getLastRecord($sel=" * ",$LF="0",$dt="", $tbl="", $b="", $c="" ){
		if(empty($tbl)){ $tbl = $this->tbl; }
		return parent::getLastRecord($tbl,"form_id",$LF,$sel,$dt);
	}
	
	//chart_
	function get_chart_info($elem_dos){
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;
		
		$ar_ret = array();
		
		$sql = "SELECT * FROM ".$this->tbl."  WHERE form_id = '".$elem_formId."' AND patient_id='".$patient_id."' AND purged = '0' ";		
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
		//if(mysql_num_rows($res)<=0){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
			//$res = valNewRecordR_v($patient_id);	
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$blDrwaingGray = true;	
			$myflag=true;
		}
		
		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){			
			$res = $this->getLastRecord(" * ",1,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}
		
		if($row!=false){
		//if(@mysql_num_rows($res)>0){
			//extract(mysql_fetch_array($res));
			extract($row);
			
			if($myflag){		
				$elem_editMode=0;
			}else{		
				$elem_editMode=1;				
			}
			$ar_ret["elem_vitId_LF"] = $ar_ret["elem_vitId"]=$id;	
			$ar_ret["elem_editModeVitreous"] = $elem_editMode;	
			
			//NC
			if($elem_editMode==1){
				$ar_ret["elem_examDateVitreous"]=$row["exam_date"];
			}			
			
			$vitreous_od= stripslashes($vitreous_od);
			$vitreous_os= stripslashes($vitreous_os);
			
			$arr_vals_vitreous_od = $oExamXml->extractXmlValue($vitreous_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_vitreous_od);
			//extract($arr_vals_vitreous_od);
			
			$arr_vals_vitreous_os = $oExamXml->extractXmlValue($vitreous_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_vitreous_os);
			//extract($arr_vals_vitreous_os);
			
			$ar_ret["elem_wnlVitreous"] = $wnlVitreous;			
			$ar_ret["elem_posVitreous"] = $posVitreous;			
			
			$ar_ret["elem_ncVitreous"] =($elem_editMode==1) ? $ncVitreous : 0 ;			
			
			$ar_ret["elem_wnlVitreousOd"]= $wnlVitreousOd;
			$ar_ret["elem_wnlVitreousOs"]= $wnlVitreousOs;
			
			$ar_ret["elem_statusElementsVitreous"] = ($elem_editMode==0) ? "" : $statusElem;
			
			//UT Elems
			$ar_ret["elem_utElemsVitreous"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;
			
			//
			$ar_ret["vitreous_od_summary"]=$row["vitreous_od_summary"];
			$ar_ret["vitreous_os_summary"]=$row["vitreous_os_summary"];
			
		}
		return $ar_ret;
	}
	
	public function save_form(){
		$elem_formId = $formId = $this->fid;
		$patientid = $this->pid;
		$oChartNoteSaver = new ChartNoteSaver($patientid, $formId);
		$oExamXml = new ExamXml();	
		$arXmlFiles = $oExamXml->getExamXmlFiles("Fundus");
		
		//GetChangeIndicator
		$arrSe = array();
		$tmpd = "elem_chng_div1_Od";
		$tmps = "elem_chng_div1_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = ($$tmpd == "1") ? "1" : "0";
		$arrSe[$tmps] = ($$tmps == "1") ? "1" : "0";		
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);
		
		//Viterous -----------
		$wnlVitreousOd = $wnlVitreousOs = $wnlVitreous = $posVitreous = $ncVitreous = "0";
		//if(!empty($elem_chng_div1_Od) || !empty($elem_chng_div1_Os)){
		//	if(!empty($elem_chng_div1_Od)){
				$menuName = "ViterousOd";
				$menuFilePath = $arXmlFiles["vitreous"]["od"]; //dirname(__FILE__)."/xml/vitreous_od.xml";
				$elem_vitreous_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlVitreousOd = $_POST["elem_wnlVitreousOd"];

		//	}

		//	if(!empty($elem_chng_div1_Os)){
				$menuName = "ViterousOs";
				$menuFilePath = $arXmlFiles["vitreous"]["os"]; //dirname(__FILE__)."/xml/vitreous_os.xml";
				$elem_vitreous_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlVitreousOs = $_POST["elem_wnlVitreousOs"];
		//	}

			$wnlVitreous = (!empty($wnlVitreousOd) && !empty($wnlVitreousOs)) ? "1" : "0"; // $_POST["elem_wnlVitreous"];
			$posVitreous = $_POST["elem_posVitreous"];
			$ncVitreous = $_POST["elem_ncVitreous"];
		//}
		//Viterous -----------
		
		$examDate = wv_dt("now"); //$_POST["elem_examDateLids"];
		
		$oUserAp = new UserAp();
		
		// Summary --
		$strExamsAllOd = $strExamsAllOs = "";
		$elem_vitreous_od_summary = $elem_vitreous_os_summary = "";
		
		$vitreous_od = $elem_vitreous_od;			
		$arrTemp = $this->getExamSummary($vitreous_od);
		$elem_vitreous_od_summary = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div1_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Vitreous",$arrExmDone_od,$elem_vitreous_od_summary);
		}
		$vitreous_os = $elem_vitreous_os;			
		$arrTemp = $this->getExamSummary($vitreous_os);
		$elem_vitreous_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div1_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Vitreous",$arrExmDone_os,$elem_vitreous_os_summary);
		}
		
		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsVitreous"];
		$elem_utElems_cur = $_POST["elem_utElemsVitreous_cur"];
		$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);
		//ut_elems ----------------------
		
		//Purge
		if(!empty($_POST["elem_purged"])){			
			//Update
			$sql = "UPDATE ".$this->tbl."
				  SET
				  purged=id,
				  purgerId='".$_SESSION["authId"]."',
				  purgetime='".wv_dt('now')."'
				  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
				";
			$row = sqlQuery($sql);			
		}else{
			$owv = new WorkView();			
			$elem_vitreous_od = sqlEscStr($elem_vitreous_od);
			$elem_vitreous_os = sqlEscStr($elem_vitreous_os);
			
			//check
			$cQry = "select 
						id, last_opr_id, uid,
						vitreous_od_summary, vitreous_os_summary, wnlVitreousOd, wnlVitreousOs, modi_note_vitreousArr, wnl_value_Vitreous,
						exam_date						
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$elem_editMode = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$vitId=$vitIDExam = $row["id"];
				$elem_editMode = "1";
				//Modifying Notes----------------
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				
				$seri_modi_note_vitreousArr = $owv->getModiNotesArr($row["vitreous_od_summary"],$elem_vitreous_od_summary,$last_opr_id,'OD',$row["modi_note_vitreousArr"],$row['exam_date']);
				$seri_modi_note_vitreousArr = $owv->getModiNotesArr($row["vitreous_os_summary"],$elem_vitreous_os_summary,$last_opr_id,'OS',$seri_modi_note_vitreousArr,$row['exam_date']);	
				
				//Modifying Notes----------------
					
			}
			
			//
			$sql_con = "
				vitreous_od = '".$elem_vitreous_od."',
				vitreous_os = '".$elem_vitreous_os."',
				vitreous_od_summary = '".$elem_vitreous_od_summary."',
				vitreous_os_summary = '".$elem_vitreous_os_summary."',
				wnlVitreous='".$wnlVitreous."',
				posVitreous='".$posVitreous."',
				ncVitreous='".$ncVitreous."',
				wnlVitreousOd='".$wnlVitreousOd."',
				wnlVitreousOs='".$wnlVitreousOs."',	
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_vitreousArr = '".sqlEscStr($seri_modi_note_vitreousArr)."'
			";		
			
			//
			if($elem_editMode == "0"){
				//WNL
				$wnl_value_Vitreous = $this->getExamWnlStr("Vitreous");
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',	
					wnl_value_Vitreous='".sqlEscStr($wnl_value_Vitreous)."', 
					exam_date='$examDate',	
					 ";
				$sql = $sql1. $sql_con;
				$insertId = sqlInsert($sql);
				
			}else if($elem_editMode == "1"){
				//Update
				$sql1 = "UPDATE ".$this->tbl." SET ";
				$sql2 = " WHERE form_id='".$formId."' AND patient_id='".$patientid."' AND purged = '0' ";
				$sql = $sql1. $sql_con. $sql2; 
				$res = sqlQuery($sql);
				$insertId = $vitId;
			}
			
			// Make chart notes valid
			$this->makeChartNotesValid();
			
			//Set Change Date Arc Rec --
			$oChartNoteSaver->setChangeDtArcRec($this->tbl);
			//Set Change Date Arc Rec --
			
		}
		
		//combine
		$strExamsAll = $oChartNoteSaver->combineExamFindings($strExamsAllOd, $strExamsAllOs);
		$strExamsAll = $oChartNoteSaver->makeArrString($strExamsAll);		
		
		return $strExamsAll;
	}

	function getWorkViewSummery($post){	
		
		$oOnload =  new Onload();
		//object Chart Rec Archive --
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION['authId']);
		
		//Get Template Procedures ---
		$arrTempProc=array("All");
		if(isset($post["artemp"])&&!empty($post["artemp"])){
			$arrTempProc=$post["artemp"];
		}			
		//Get Template Procedures ---
		
		$sql ="SELECT ".
			//c9-chart_rv-------			
			"c9.wnlVitreous, ".
			"c9.posVitreous, ".
			"c9.ncVitreous, ".
			"c9.vitreous_od_summary, ".
			"c9.vitreous_os_summary, ".
			"c9.id, ".
			"c9.wnlVitreousOd, ".
			"c9.wnlVitreousOs, ".			
			"c9.wnl_value_Vitreous, ".
			"c9.exam_date, ".
			"c9.statusElem AS se_vit, ".
			"c9.modi_note_vitreousArr  ".
			"FROM chart_master_table c1 ".			
			"INNER JOIN ".$this->tbl." c9 ON c9.form_id = c1.id AND c9.purged='0'  ".
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
			//Rv
			$elem_wnlVitreous = assignZero($row["wnlVitreous"]);			
			$elem_posVitreous = assignZero($row["posVitreous"]);
			$elem_ncVitreous = assignZero($row["ncVitreous"]);
			$elem_wnlVitreousOd = assignZero($row["wnlVitreousOd"]);
			$elem_wnlVitreousOs = assignZero($row["wnlVitreousOs"]);
			$elem_se_vit = $row["se_vit"];
			$elem_vitreous_od_summary = $row["vitreous_od_summary"];				
			$elem_vitreous_os_summary = $row["vitreous_os_summary"];
			$elem_vit_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			$modi_note_vitreousArr = unserialize($row["modi_note_vitreousArr"]);
				$arrHx = array();
				if(is_array($modi_note_vitreousArr) && count($modi_note_vitreousArr)>0 && $row["modi_note_vitreousArr"]!='')
				$arrHx['Vitreous']	= $modi_note_vitreousArr;
				
			//wnl
			$elem_wnl_value_Vitreous = $row["wnl_value_Vitreous"];			
			
			
			//
			$elem_periNotExamined = $row["periNotExamined"];
			$elem_peri_ne_eye = $row["peri_ne_eye"];
		}
		
		if(empty($elem_vit_id)){
			$tmp = " c2.wnlVitreous, c2.posVitreous,c2.ncVitreous, c2.vitreous_od_summary,
					c2.vitreous_os_summary, c2.id, c2.wnlVitreousOd, c2.wnl_value_Vitreous,
					c2.wnlVitreousOs, c2.exam_date ,					
					c2.statusElem AS se_vit
					";
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordR_v($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				$elem_vitreous_od_summary = $row["vitreous_od_summary"];					
				$elem_vitreous_os_summary = $row["vitreous_os_summary"];					
				$elem_wnlVitreous = assignZero($row["wnlVitreous"]);					
				$elem_wnlVitreousOd = assignZero($row["wnlVitreousOd"]);					
				$elem_wnlVitreousOs = assignZero($row["wnlVitreousOs"]);					
				$elem_posVitreous = assignZero($row["posVitreous"]);
				$elem_ncVitreous = assignZero($row["ncVitreous"]);
				$elem_wnlRv=assignZero($row["flagWnlRv"]);
				$elem_posRv=assignZero($row["flagPosRv"]);
				$examdate = wv_formatDate($row["exam_date"]);
				//wnl
				$elem_wnl_value_Vitreous = $row["wnl_value_Vitreous"];	
				$elem_se_vit_prev = $row["se_vit"];
			}
			
			//BG
			$bgColor_RV = "bgSmoke";
		}
		
		//is Change is made in new chart -----
		$flgSe_Vitreous_Od = $flgSe_Vitreous_Os = "0";
		if(!isset($bgColor_RV)){
			if(!empty($elem_se_vit)){
				$tmpArrSe = $this->se_elemStatus("RV","0",$elem_se_vit,0,0,1,$elem_retina_version); //working19
				$flgSe_Vitreous_Od = $tmpArrSe["1"]["od"];
				$flgSe_Vitreous_Os = $tmpArrSe["1"]["os"];
			}
		}else{
			if(!empty($elem_se_vit_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("RV","0",$elem_se_vit_prev,0,0,1,$elem_retina_version); //working19
				$flgSe_Vitreous_Od_prev = $tmpArrSe_prev["1"]["od"];
				$flgSe_Vitreous_Os_prev = $tmpArrSe_prev["1"]["os"];
			}
		}		
		
		//Vitreous --
		$wnlString_Vitreous = !empty($elem_wnl_value_Vitreous) ? $elem_wnl_value_Vitreous : $this->getExamWnlStr("Vitreous"); //"Clear"
		$wnlStringOd_Vitreous = $wnlStringOs_Vitreous = $wnlString_Vitreous;
		
		if(empty($flgSe_Vitreous_Od) && empty($flgSe_Vitreous_Od_prev) && !empty($elem_wnlVitreousOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Vitreous", "OD"); if(!empty($tmp)){ $wnlStringOd_Vitreous = $tmp;}  }
		if(empty($flgSe_Vitreous_Os) && empty($flgSe_Vitreous_Os_prev) && !empty($elem_wnlVitreousOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Vitreous", "OS"); if(!empty($tmp)){ $wnlStringOs_Vitreous = $tmp;}  }
		
		list($elem_vitreous_od_summary,$elem_vitreous_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Vitreous,"wValOs"=>$wnlStringOs_Vitreous,
																	"wOd"=>$elem_wnlVitreousOd,"sOd"=>$elem_vitreous_od_summary,
																	"wOs"=>$elem_wnlVitreousOs,"sOs"=>$elem_vitreous_os_summary));
																	
		//Nochanged
		if(!empty($elem_se_vit)&&strpos($elem_se_vit,"=1")!==false){
			$elem_noChangeVit=1;
		}

		$arrDivArcCmn=array();
		
		if($bgColor_RV != "bgSmoke"){
			$oChartRecArc->setChkTbl("".$this->tbl);
			$arrInpArc = array("elem_sumOdVitreous"=>array("vitreous_od_summary",$elem_vitreous_od_summary,"smof","wnlVitreousOd",$wnlString_Vitreous,$modi_note_VitreousOd),
								"elem_sumOsVitreous"=>array("vitreous_os_summary",$elem_vitreous_os_summary,"smof","wnlVitreousOs",$wnlString_Vitreous,$modi_note_VitreousOs)
								
								);
			
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
			//Vit --
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdVitreous"])){
				//echo $arTmpRecArc["div"]["elem_sumOdVitreous"];
				$arrDivArcCmn["Vitreous"]["OD"]=$arTmpRecArc["div"]["elem_sumOdVitreous"];
				$moeArc["od"]["Vit"] = $arTmpRecArc["js"]["elem_sumOdVitreous"];
				$flgArcColor["od"]["Vit"] = $arTmpRecArc["css"]["elem_sumOdVitreous"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdVitreous"])) 
					$elem_vitreous_od_summary = $arTmpRecArc["curText"]["elem_sumOdVitreous"];
			}
			//Os
			if(!empty($arTmpRecArc["div"]["elem_sumOsVitreous"])){
				//echo $arTmpRecArc["div"]["elem_sumOsVitreous"];
				$arrDivArcCmn["Vitreous"]["OS"]=$arTmpRecArc["div"]["elem_sumOsVitreous"];
				$moeArc["os"]["Vit"] = $arTmpRecArc["js"]["elem_sumOsVitreous"];
				$flgArcColor["os"]["Vit"] = $arTmpRecArc["css"]["elem_sumOsVitreous"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsVitreous"])) 
					$elem_vitreous_os_summary = $arTmpRecArc["curText"]["elem_sumOsVitreous"];
			}
			//Vit --	
		
		}
		
		$arr=array();
		
		//if(in_array("Vitreous",$arrTempProc) || in_array("All",$arrTempProc)){
		//Vitreous
		$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Vitreous",
											"sOd"=>$elem_vitreous_od_summary,"sOs"=>$elem_vitreous_os_summary,
											"fOd"=>$flgSe_Vitreous_Od,"fOs"=>$flgSe_Vitreous_Os,"pos"=>$elem_posVitreous,
											//"arcJsOd"=>$moeArc["od"]["Vit"],"arcJsOs"=>$moeArc["os"]["Vit"],
											"arcCssOd"=>$flgArcColor["os"]["Vit"],"arcCssOs"=>$flgArcColor["os"]["Vit"],
											//"mnOd"=>$moeMN["od"]["Vit"],"mnOs"=>$moeMN["os"]["Vit"],
											"enm_2"=>"Vit"));

		//}
		
		//Sub Exam List
		$arr["seList"] = 	array("Vit"=>array("enm"=>"Vitreous","pos"=>$elem_posVitreous,
							"wOd"=>$elem_wnlVitreousOd,"wOs"=>$elem_wnlVitreousOs));
							
		$arr["bgColor"] = "".$bgColor_RV;
		$arr["nochange"] = $elem_ncVitreous;
		$arr["examdate"] = $examdate;
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_Vitreous_Od,$flgSe_Vitreous_Os);
		
		//$arr["arrDivArcCmn"] = $arrDivArcCmn;
		$arr["arrHx"] = $arrHx;
		//---------
		
		return $arr;
	}
	
	function getPurgedExm(){
	
		//Get Template Procedures ---
		$arrTempProc=array("All");
		if(isset($post["artemp"])&&!empty($post["artemp"])){
			$arrTempProc=$post["artemp"];
		}			
		//Get Template Procedures ---
	
		$oOnload =  new Onload();
		$arPurge = array();
		$sql ="SELECT ".			
			//c9-chart_rv-------			
			"c9.wnlVitreous,".			
			"c9.posVitreous,".
			"c9.ncVitreous,".
			"c9.vitreous_od_summary, c9.vitreous_os_summary, ".
			"c9.id, ".
			"c9.wnlVitreousOd, c9.wnlVitreousOs, ".
			"c9.wnl_value_Vitreous, ".
			"c9.exam_date AS exam_date_RV, ".
			"c9.statusElem AS se_vit, c9.purgerId, c9.purgeTime, c9.purgerId AS purgerId_rv, c9.purgeTime AS purgeTime_rv ".
			"FROM chart_master_table c1 ".			
			"INNER JOIN ".$this->tbl." c9 ON c9.form_id = c1.id AND c9.purged!='0'  ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
			"ORDER BY purgeTime_rv DESC ";
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
			//Rv
			$elem_wnlVitreous = assignZero($row["wnlVitreous"]);			
			$elem_posVitreous = assignZero($row["posVitreous"]);
			$elem_ncVitreous = assignZero($row["ncVitreous"]);
			$elem_wnlVitreousOd = assignZero($row["wnlVitreousOd"]);
			$elem_wnlVitreousOs = assignZero($row["wnlVitreousOs"]);
			$elem_se_vit = $row["se_vit"];
			$elem_vitreous_od_summary = $row["vitreous_od_summary"];				
			$elem_vitreous_os_summary = $row["vitreous_os_summary"];
			$elem_vit_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			$modi_note_vitreousArr = unserialize($row["modi_note_vitreousArr"]);
				$arrHx = array();
				if(count($modi_note_vitreousArr)>0 && $row["modi_note_vitreousArr"]!='')
				$arrHx['Vitreous']	= $modi_note_vitreousArr;
				
			//wnl
			$elem_wnl_value_Vitreous = $row["wnl_value_Vitreous"];			
			
			
			//
			$elem_periNotExamined = $row["periNotExamined"];
			$elem_peri_ne_eye = $row["peri_ne_eye"];
			
			//is Change is made in new chart -----
			$flgSe_Vitreous_Od = $flgSe_Vitreous_Os = "0";
			if(!empty($elem_se_vit)){
				$tmpArrSe = $this->se_elemStatus("RV","0",$elem_se_vit,0,0,1,$elem_retina_version); //working19
				$flgSe_Vitreous_Od = $tmpArrSe["1"]["od"];
				$flgSe_Vitreous_Os = $tmpArrSe["1"]["os"];
			}	
			
			//Vitreous --
			$wnlString_Vitreous = !empty($elem_wnl_value_Vitreous) ? $elem_wnl_value_Vitreous : $this->getExamWnlStr("Vitreous"); //"Clear"
			$wnlStringOd_Vitreous = $wnlStringOs_Vitreous = $wnlString_Vitreous;
			
			if(empty($flgSe_Vitreous_Od) && empty($flgSe_Vitreous_Od_prev) && !empty($elem_wnlVitreousOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Vitreous", "OD"); if(!empty($tmp)){ $wnlStringOd_Vitreous = $tmp;}  }
			if(empty($flgSe_Vitreous_Os) && empty($flgSe_Vitreous_Os_prev) && !empty($elem_wnlVitreousOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Vitreous", "OS"); if(!empty($tmp)){ $wnlStringOs_Vitreous = $tmp;}  }
			
			list($elem_vitreous_od_summary,$elem_vitreous_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Vitreous,"wValOs"=>$wnlStringOs_Vitreous,
																		"wOd"=>$elem_wnlVitreousOd,"sOd"=>$elem_vitreous_od_summary,
																		"wOs"=>$elem_wnlVitreousOs,"sOs"=>$elem_vitreous_os_summary));
			//Nochanged
			if(!empty($elem_se_vit)&&strpos($elem_se_vit,"=1")!==false){
				$elem_noChangeVit=1;
			}															
			
			$arr=array();
		
			if(in_array("Vitreous",$arrTempProc) || in_array("All",$arrTempProc)){
			//Vitreous
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Vitreous",
												"sOd"=>$elem_vitreous_od_summary,"sOs"=>$elem_vitreous_os_summary,
												"fOd"=>$flgSe_Vitreous_Od,"fOs"=>$flgSe_Vitreous_Os,"pos"=>$elem_posVitreous,
												//"arcJsOd"=>$moeArc["od"]["Vit"],"arcJsOs"=>$moeArc["os"]["Vit"],
												"arcCssOd"=>$flgArcColor["os"]["Vit"],"arcCssOs"=>$flgArcColor["os"]["Vit"],
												//"mnOd"=>$moeMN["od"]["Vit"],"mnOs"=>$moeMN["os"]["Vit"],
												"enm_2"=>"Vit"));

			}
			
			//Sub Exam List
			$arr["seList"] = 	array("Vit"=>array("enm"=>"Vitreous","pos"=>$elem_posVitreous,
								"wOd"=>$elem_wnlVitreousOd,"wOs"=>$elem_wnlVitreousOs));								
			$arr["bgColor"] = "";
			$arr["nochange"] = $elem_ncVitreous;
			$arr["examdate"] = $examdate;		
			$arr["moeMN"] = $moeMN;
			$arr["exm_flg_se"] = array($flgSe_Vitreous_Od,$flgSe_Vitreous_Os);
			
			$arr["purgerId"] = $row["purgerId_rv"];
			$arr["purgeTime"] = $row["purgeTime_rv"];		
			//---------
			
			//echo "<pre>";
			//print_r($arr);
			//exit();
			
			
			$arPurge[] = $arr;
		}
		return $arPurge;
	}
	
	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		//WNL		
		$wnl_value_Vitreous = $this->getExamWnlStr("Vitreous");		
		
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_Vitreous)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_Vitreous)."') ";
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
					"ncVitreous,".
					"ncVitreous_od,".
					"ncVitreous_os,".
					"wnl_value_Vitreous";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,"",'id');
	}
	
	function save_wnl($op="get", $arv=array()){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$exmEye = $_POST["elem_exmEye"];
		
		$w = strtoupper($_POST["w"]);
		//Get Template Procedures ---
		$arrTempProc=array("All");
		if(isset($_POST["artemp"])&&!empty($_POST["artemp"])){
			//$arrTempProc=$_POST["artemp"];
			$arrTempProc = json_decode(gzuncompress(base64_decode($_POST["artemp"])));
		}			
		//Get Template Procedures ---
		
		if($op=="get"){
			$ar_ret=array();
			//FUNDUS			
			$elem_retina_version="new";
			$wnl_value_Vitreous="";
			$wnl_value_Vitreous_phrase="";
			$flgCarry=0;	
			
			//Fundus
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}
			
			$cQry = "select 
					wnlVitreous, ".					
					"wnlVitreousOd,wnlVitreousOs,
					".					
					"posVitreous, ".					
					"vitreous_od_summary,vitreous_os_summary,						
					".					
					"statusElem AS statusElem_fundus, uid, wnl_value_Vitreous, 
					id
					FROM ".$this->tbl." WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){				
			}else{
				$ar_ret=$row;
			}
			return $ar_ret;			
		}else if($op=="set" && count($arv)){
			extract($arv);
			$elem_wnlVitreousOd = $wnlVitreousOd;
			$elem_wnlVitreousOs = $wnlVitreousOs;
			$elem_wnlVitreous = $wnlVitreous;
		
			$oWv = new WorkView();
			
			if(in_array("Vitreous",$arrTempProc)||in_array("All",$arrTempProc)){
				if((!empty($statusElem_fundus)&&strpos($statusElem_fundus,"0")===false)||	
					(empty($vitreous_od_summary) && empty($elem_wnlVitreousOd))||
					(empty($vitreous_os_summary) && empty($elem_wnlVitreousOs))
					 ){	 
				//Toggle Vitreous
				list($elem_wnlVitreousOd,$elem_wnlVitreousOs,$elem_wnlVitreous) =
										$oWv->toggleWNL($posVitreous,$vitreous_od_summary,$vitreous_os_summary,
														$elem_wnlVitreousOd,$elem_wnlVitreousOs,$elem_wnlVitreous,$exmEye);
				}
			}
			
			//Status
			$statusElem_fundus_prev=$statusElem_fundus;
			$statusElem_fundus = $this->setEyeStatus($w, $exmEye,$statusElem_fundus,0,$elem_retina_version);
			
			if(empty($wnl_value_Vitreous)){
				$wnl_value_Vitreous=$this->getExamWnlStr("Vitreous");
				$wnl_value_Vitreous_phrase = ", wnl_value_Vitreous='".sqlEscStr($wnl_value_Vitreous)."' ";
			}
			
			//Fundus
			$sql = "UPDATE ".$this->tbl." SET  
				  wnlVitreous='".$elem_wnlVitreous."', ".
				  "wnlVitreousOd='".$elem_wnlVitreousOd."',
				  wnlVitreousOs='".$elem_wnlVitreousOs."',				  
				  ".
				  " exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				  statusElem='".$statusElem_fundus."'
				  ".				  
				  "				  
				  ";			
			$sql .= " ".$wnl_value_Vitreous_phrase." ";
			
			$sql .=  "WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";			
			$res = sqlQuery($sql);
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
			$ignoreFlds = "form_id,exam_date,uid,patient_id,";
			if(!empty($Id)){
			
				if(empty($_POST["site"]) || $_POST["site"]=="OU"){ $statusElem = "";  }
				if($_POST["site"]=="OS"){ 
					$ignoreFlds .= "vitreous_od_summary,
								vitreous_od,
								wnlVitreousOd,
								ncVitreous_od,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "vitreous_os,vitreous_os_summary,
								wnlVitreousOs,
								ncVitreous_os,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posVitreous,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Vitreous,ut_elem,";}
				}
				$ignoreFlds = trim($ignoreFlds,",");			
			
				$this->resetValsExe($this->tbl,$Id,$ignoreFlds);
				$this->setStatus($statusElem,$this->tbl);
			}
		//}else{
			//
		//	$this->insertNew();
		//}
		
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
						"ncVitreous,".
						"ncVitreous_od,".
						"ncVitreous_os,".
						"wnl_value_Vitreous";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}	
	}
	
	function isNoChanged(){
		$res= $this->getRecord("ncVitreous,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncVitreous"]) ){
				return true;
			}		
		}
		return false;		
	}
	
	function save_no_change(){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$w = strtoupper($_POST["w"]);
		$exmEye = $_POST["elem_exmEye"];
		
		$tmpNC=0;
		$elem_ncVitreous="0";			
		$elem_ncVitreous_od="0";			
		$elem_ncVitreous_os="0";
		
		//
		$oFE=$this; //new FundusExam($patientId,$form_id);
		if(!$oFE->isRecordExists()){
			$oFE->carryForward();
			$tmpNC=1;
		}else if(!$oFE->isNoChanged()){
			$tmpNC=1;
		}else{
			$oFE->set2PrvVals();
			$tmpNC=0;
		}
		
		// ---
		//Set NC
		if($tmpNC==1){
			if($exmEye=="OU"){				
				$elem_ncVitreous="1";
				$elem_ncVitreous_od="1";
				$elem_ncVitreous_os="1";
			}else if($exmEye=="OD"){				
				$elem_ncVitreous_od="1";
			}else if($exmEye=="OS"){
				$elem_ncVitreous_os="1";
			}
		}			
		// ---
		
		//Get status string --
		$statusElem="";
		if($elem_ncVitreous_od==1||$elem_ncVitreous_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncVitreous_od,$elem_ncVitreous_os,0);}
		//Get status--		
		
		//
		$sql = "UPDATE ".$this->tbl."
			  SET			  
			  ncVitreous = '".$elem_ncVitreous."',
			  ncVitreous_od='".$elem_ncVitreous_od."', ncVitreous_os='".$elem_ncVitreous_os."',			  
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}
	
	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "vitreous_od";
		$arr["db"]["xmlOs"] = "vitreous_os";
		$arr["db"]["wnlSE"] = "wnlVitreous";
		$arr["db"]["wnlOd"] = "wnlVitreousOd";
		$arr["db"]["wnlOs"] = "wnlVitreousOs";
		$arr["db"]["posSE"] = "posVitreous";
		$arr["db"]["summOd"] = "vitreous_od_summary";
		$arr["db"]["summOs"] = "vitreous_os_summary";
		$arr["divSe"] = "1";
		//--
		
		$oExamXml = new ExamXml();
		$tmp = $oExamXml->getExamXmlFiles("Fundus", $this->examName);
		$arr["xmlFile"]["OD"] = $tmp["od"];
		$arr["xmlFile"]["OS"] = $tmp["os"];
		
		//--
		
		return $arr;
	}
	
	function smartChart($arr){
		if(!$this->isRecordExists()){
			//In
			$this->carryForward();
		}
		$arrSEInfo = $this->getSubExamInfo();
		$res=$this->getRecord();
		if($res!=false){
			$xmlOd = $res[$arrSEInfo["db"]["xmlOd"]];
			$xmlOs = $res[$arrSEInfo["db"]["xmlOs"]];
			$wnlSE = $res[$arrSEInfo["db"]["wnlSE"]];
			$wnlOd=$res[$arrSEInfo["db"]["wnlOd"]];
			$wnlOs=$res[$arrSEInfo["db"]["wnlOs"]];
			$posSE=$res[$arrSEInfo["db"]["posSE"]];
			$statusElem = $res["statusElem"];	
			$ut_elem=$res["ut_elem"];

		}else{
			$desc = "";
		}
		
		//
		$idSE = $arrSEInfo["divSe"];
		$statusElemCur=$this->getCurStatusFromPost($arr, "elem_chng_div".$idSE."_Od", "elem_chng_div".$idSE."_Os", $statusElem);
		
		//Edit Xml --
		$arrIn["xOd"]=$xmlOd;
		$arrIn["xOs"]=$xmlOs;
		$arrIn["xFileOd"]=$arrSEInfo["xmlFile"]["OD"];
		$arrIn["xFileOs"]=$arrSEInfo["xmlFile"]["OS"];
		$arrIn["arrSc"] = $arr;
		$arrIn["ut_elem"] = $ut_elem;
		$arrOut= $this->editXml($arrIn);

		$xmlOd=$arrOut["xOd"];
		$xmlOs=$arrOut["xOs"];
		$siteExm=$arrOut["siteExm"];
		$desc.=$arrOut["desc"];
		$sumOd = $arrOut["sumOd"];
		$sumOs = $arrOut["sumOs"];
		$arrElemEd=$arrOut["elemEd"];
		$ut_elem = $arrOut["ut_elem"];
		//Edit Xml --

		//Set WNL --
		list($wnlSE,$wnlOd,$wnlOs,$pos,$wnl,$posSE) = $this->setExmWnlPos($sumOd,$sumOs,$wnlSE,$wnlOd,$wnlOs,$pos,$wnl,$posSE);
		//Set WNL --

		//Set Status Fields--
		if(!empty($statusElemCur)){ 
			$statusElem=$statusElemCur;			
		}else{		
			$statusElem = $this->setStatusElem($arrSEInfo["divSe"],$statusElem,$sumOd,$sumOs,$siteExm);
		}
		//Set Status Fields--
		
		//UTteleme --
		$elem_utElems_cur="";
		if(count($arrElemEd)>0){
			$elem_utElems_cur=implode(",", $arrElemEd);
			$elem_utElems_cur=$elem_utElems_cur.",";
		}
		$ut_elem = $this->getUTElemString($ut_elem,$elem_utElems_cur);
		//UTteleme --		

		//Update Records--
		$sql="UPDATE ".$this->tbl." ".
			"SET ".
			"exam_date = '".wv_dt("now")."', ".		

			$arrSEInfo["db"]["xmlOd"]."  = '".sqlEscStr($xmlOd)."', ".
			$arrSEInfo["db"]["xmlOs"]." = '".sqlEscStr($xmlOs)."', ".
			$arrSEInfo["db"]["summOd"]." = '".sqlEscStr($sumOd)."', ".
			$arrSEInfo["db"]["summOs"]." = '".sqlEscStr($sumOs)."', ".
			$arrSEInfo["db"]["wnlOd"]."='".$wnlOd."', ".
			$arrSEInfo["db"]["wnlOs"]."='".$wnlOs."', ".
			$arrSEInfo["db"]["wnlSE"]."='".$wnlSE."', ".
			$arrSEInfo["db"]["posSE"]."='".$posSE."', ".
			
			"uid = '".$this->uid."', ".
			"statusElem = '".sqlEscStr($statusElem)."', ".
			"ut_elem = '".sqlEscStr($ut_elem)."' ".
			"WHERE form_id = '".$this->fid."' AND patient_id = '".$this->pid."' AND purged = '0' ";
		$res=sqlQuery($sql);	
	}
}

?>