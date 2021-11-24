<?php

class Periphery extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_periphery";
		$this->examName="Periphery";
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
				$ar_ret["elem_examDatePeri"]=$exam_date;
			}
			$ar_ret["elem_periId_LF"] = $ar_ret["elem_periId"]=$id;
			$ar_ret["elem_statusElementsPeri"] = ($elem_editMode==0) ? "" : $statusElem;
			$ar_ret["elem_editModePeri"] = $elem_editMode;

			//UT Elems //($elem_editMode==1) ? : "";
			$ar_ret["elem_utElemsPeri"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;

			$periphery_od= stripslashes($periphery_od);
			$periphery_os= stripslashes($periphery_os);

			$arr_vals_periphery_od = $oExamXml->extractXmlValue($periphery_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_periphery_od);
			//extract($arr_vals_periphery_od);
			$arr_vals_periphery_os = $oExamXml->extractXmlValue($periphery_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_periphery_os);
			//extract($arr_vals_periphery_os);

			$ar_ret["elem_wnlPeri"] = $wnlPeri;
			$ar_ret["elem_posPeri"] = $posPeri;
			$ar_ret["elem_ncPeri"] = ($elem_editMode==1) ? $ncPeri : 0 ;
			$ar_ret["elem_wnlPeriOd"]= $wnlPeriOd;
			$ar_ret["elem_wnlPeriOs"]= $wnlPeriOs;
			$ar_ret["periphery_od_summary"] = stripslashes($periphery_od_summary);
			$ar_ret["periphery_os_summary"] = stripslashes($periphery_os_summary);
			$ar_ret["elem_periNotExamined_peri"]=($elem_editMode==1) ? $row["periNotExamined"] : "";
			$ar_ret["elem_peri_ne_eye_peri"]=($elem_editMode==1) ? $row["peri_ne_eye"] : "" ;

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
		$tmpd = "elem_chng_div3_Od";
		$tmps = "elem_chng_div3_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = ($$tmpd == "1") ? "1" : "0";
		$arrSe[$tmps] = ($$tmps == "1") ? "1" : "0";
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);

		//Peri -----------
		$wnlPeriOd = $wnlPeriOs = $wnlPeri = $posPeri = $ncPeri = "0";
		//if(!empty($elem_chng_div3_Od) || !empty($elem_chng_div3_Os)){
		//	if(!empty($elem_chng_div3_Od)){
				$menuName = "PeripheryOd";
				$menuFilePath = $arXmlFiles["periphery"]["od"]; //dirname(__FILE__)."/xml/periphery_od.xml";
				$elem_periphery_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlPeriOd = $_POST["elem_wnlPeriOd"];
		//	}

		//	if(!empty($elem_chng_div3_Os)){
				$menuName = "PeripheryOs";
				$menuFilePath = $arXmlFiles["periphery"]["os"]; //dirname(__FILE__)."/xml/periphery_os.xml";
				$elem_periphery_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlPeriOs = $_POST["elem_wnlPeriOs"];
		//	}

			$wnlPeri = (!empty($wnlPeriOd) && !empty($wnlPeriOs)) ? "1" : "0"; // $_POST["elem_wnlPeri"];
			$posPeri = $_POST["elem_posPeri"];
			$ncPeri = $_POST["elem_ncPeri"];
			$periNotExamined  = $_POST["elem_periNotExamined_peri"];
			$peri_ne_eye = (!empty($periNotExamined)) ? $_POST["elem_peri_ne_eye_peri"] : "" ;

		//}
		//Peri -----------

		$examDate = wv_dt("now"); //$_POST["elem_examDate"];
		$oUserAp = new UserAp();

		// Summary --
		$strExamsAllOd = $strExamsAllOs = "";
		$elem_periphery_od_summary = $elem_periphery_od_summary = "";
		$periphery_od = $elem_periphery_od;

		$arrTemp = $this->getExamSummary($periphery_od);
		$elem_periphery_od_summary = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div3_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Periphery",$arrExmDone_od,$elem_periphery_od_summary);
		}
		$periphery_os = $elem_periphery_os;

		$arrTemp = $this->getExamSummary($periphery_os);
		$elem_periphery_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div3_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Periphery",$arrExmDone_os,$elem_periphery_os_summary);
		}

		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsPeri"];
		$elem_utElems_cur = $_POST["elem_utElemsPeri_cur"];
		$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);
		//ut_elems ----------------------

		//Purge
		if(!empty($_POST["elem_purged"])){
			//$purgePhrse = " , purged = pupil_id ";
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

			$elem_periphery_od = sqlEscStr($elem_periphery_od);
			$elem_periphery_os = sqlEscStr($elem_periphery_os);

			//check
			$cQry = "select
						id, last_opr_id, uid,
						periphery_od_summary, periphery_os_summary, wnlPeriOd, wnlPeriOs, wnl_value_Peri,exam_date

					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$elem_editMode = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$periId=$periIDExam = $row["id"];
				$elem_editMode = "1";
				//Modifying Notes----------------
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				$seri_modi_note_periArr = $owv->getModiNotesArr($row["periphery_os_summary"],$elem_periphery_od_summary,$last_opr_id,'OD',$row["modi_note_periArr"],$row['exam_date']);
				$seri_modi_note_periArr = $owv->getModiNotesArr($row["periphery_os_summary"],$elem_periphery_os_summary,$last_opr_id,'OS',$seri_modi_note_periArr,$row['exam_date']);
				//Modifying Notes----------------
			}

			//
			$sql_con = "
				periphery_od = '".$elem_periphery_od."',
				periphery_os = '".$elem_periphery_os."',
				periphery_od_summary = '".$elem_periphery_od_summary."',
				periphery_os_summary = '".$elem_periphery_os_summary."',
				wnlPeri='".$wnlPeri."',
				posPeri='".$posPeri."',
				ncPeri='".$ncPeri."',
				wnlPeriOd='".$wnlPeriOd."',
				wnlPeriOs='".$wnlPeriOs."',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_periArr = '".sqlEscStr($seri_modi_note_periArr)."',
				periNotExamined = '".$periNotExamined."',
				peri_ne_eye = '".$peri_ne_eye."'
			";

			//
			if($elem_editMode == "0"){
				//WNL
				$wnl_value_Peri = $this->getExamWnlStr("Periphery");
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',
					wnl_value_Peri='".sqlEscStr($wnl_value_Peri)."',
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
				$insertId = $periId;
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
		$elem_peri_version="new";

		$sql ="SELECT ".
			//c9-chart_rv-------
			"c9.id, ".
			"c9.wnl_value_Peri, ".
			//Old Data --
			"c9.wnlPeri, ".
			"c9.posPeri, ".
			"c9.ncPeri, ".
			"c9.periphery_od_summary,  ".
			"c9.periphery_os_summary,  ".
			"c9.wnlPeriOd, ".
			"c9.wnlPeriOs, ".
			//Old Data --
			"c9.periNotExamined, c9.peri_ne_eye, ".
			"c9.exam_date AS exam_date_RV, c9.modi_note_periArr, ".
			"c9.statusElem AS se_rv ".
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

			$elem_se_Rv = $row["se_rv"];
			$elem_rv_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date_RV"]);

			$arrHx = array();
			$modi_note_PeriArr  = unserialize($row["modi_note_periArr"]);
			if(is_array($modi_note_PeriArr) && count($modi_note_PeriArr)>0 && $row["modi_note_periArr"]!=''){
				$arrHx['Peri'] = $modi_note_PeriArr;
			}

			//wnl
			$elem_wnl_value_Peri = $row["wnl_value_Peri"];

			/*if( !empty($row["periphery_od_summary"]) ||
				 !empty($row["periphery_os_summary"]) ||
				 !empty($row["wnlPeriOd"]) ||
				 !empty($row["wnlPeriOs"]) ||
				 !empty($row["posPeri"])  ){*/

				//Old
				//$elem_peri_version="old";
				$elem_periNotExamined_peri = $row["periNotExamined"];
				$elem_peri_ne_eye_peri = $row["peri_ne_eye"];
				$elem_wnlPeri = assignZero($row["wnlPeri"]);
				$elem_posPeri = assignZero($row["posPeri"]);
				$elem_ncPeri = assignZero($row["ncPeri"]);
				$elem_wnlPeriOd = assignZero($row["wnlPeriOd"]);
				$elem_wnlPeriOs = assignZero($row["wnlPeriOs"]);
				$elem_periphery_od_summary = $row["periphery_od_summary"];
				$elem_periphery_os_summary = $row["periphery_os_summary"];

			//}
		}

		if(empty($elem_rv_id)){
			$tmp = "c2.wnlPeri,c2.posPeri,c2.ncPeri,c2.periphery_od_summary,
					c2.periphery_os_summary,
					c2.id, c2.wnlPeriOd, c2.wnl_value_Peri, c2.wnlPeriOs,
					c2.exam_date AS exam_date_RV, c2.periNotExamined, c2.peri_ne_eye,
					c2.statusElem AS se_rv
					";
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordR_v($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				$elem_periphery_od_summary = $row["periphery_od_summary"];
				$elem_periphery_os_summary = $row["periphery_os_summary"];
				$elem_wnlPeri = assignZero($row["wnlPeri"]);
				$elem_wnlPeriOd = assignZero($row["wnlPeriOd"]);
				$elem_wnlPeriOs = assignZero($row["wnlPeriOs"]);
				$elem_posPeri = assignZero($row["posPeri"]);
				$elem_ncPeri = assignZero($row["ncPeri"]);
				$examdate = wv_formatDate($row["exam_date_RV"]);

				//wnl
				$elem_wnl_value_Peri = $row["wnl_value_Peri"];
				$elem_se_Rv_prev = $row["se_rv"];
				$elem_periNotExamined_peri = $row["periNotExamined"];
				$elem_peri_ne_eye_peri = $row["peri_ne_eye"];

			}

			//BG
			$bgColor_RV = "bgSmoke";
		}

		//is Change is made in new chart -----
		$flgSe_Peri_Od = $flgSe_Peri_Os = "0";
		if(!isset($bgColor_RV)){
			if(!empty($elem_se_Rv)){
				$tmpArrSe = $this->se_elemStatus("RV","0",$elem_se_Rv,0,0,1,$elem_peri_version); //working19
				$flgSe_Peri_Od = $tmpArrSe["3"]["od"];
				$flgSe_Peri_Os = $tmpArrSe["3"]["os"];
			}
		}else{
			if(!empty($elem_se_Rv_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("RV","0",$elem_se_Rv_prev,0,0,1,$elem_peri_version); //working19
				$flgSe_Peri_Od_prev = $tmpArrSe_prev["3"]["od"];
				$flgSe_Peri_Os_prev = $tmpArrSe_prev["3"]["os"];
			}
		}

		//Periphery --
		$wnlString_peri = !empty($elem_wnl_value_Peri) ? $elem_wnl_value_Peri : $this->getExamWnlStr("Periphery");
		$wnlStringOd_peri = $wnlStringOs_peri = $wnlString_peri;

		if(empty($flgSe_Peri_Od) && empty($flgSe_Peri_Od_prev) && !empty($elem_wnlPeriOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Periphery", "OD"); if(!empty($tmp)){ $wnlStringOd_peri = $tmp;}  }
		if(empty($flgSe_Peri_Os) && empty($flgSe_Peri_Os_prev) && !empty($elem_wnlPeriOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Periphery", "OS"); if(!empty($tmp)){ $wnlStringOs_peri = $tmp;}  }

		//*//16-09-2015: For WNL, please remove this condition of static appending text. Arun says, they should use template now
		if(isset($GLOBALS["ADD_PERI_WNL_2_RETINAL"])&&!empty($GLOBALS["ADD_PERI_WNL_2_RETINAL"])){
		if($elem_periNotExamined_peri!=1){
			$wnlStringOd_peri .= " periphery normal";
			$wnlStringOs_peri .= " periphery normal";
		}else{
			//$wnlText_Retinal .= "periphery not examined";
			if($elem_peri_ne_eye_peri=="OU"){
				$wnlStringOd_peri .= " periphery not examined";
				$wnlStringOs_peri .= " periphery not examined";
			}else if($elem_peri_ne_eye_peri=="OD"){
				$wnlStringOd_peri .= " periphery not examined";
				$wnlStringOs_peri .= " periphery normal";
			}else if($elem_peri_ne_eye_peri=="OS"){
				$wnlStringOs_peri .= " periphery not examined";
				$wnlStringOd_peri .= " periphery normal";
			}
		}
		$wnlStringOd_peri = trim($wnlStringOd_peri);
		$wnlStringOs_peri = trim($wnlStringOs_peri);
		}

		//Add periphery in summary
		if(!empty($elem_periNotExamined_peri)&&!empty($elem_peri_ne_eye_peri)){
			//if(!isset($bgColor_RV)){$elem_posRetinal=1;}
			if($elem_peri_ne_eye_peri=="OU" || $elem_peri_ne_eye_peri=="OD"){
				//if(!isset($bgColor_RV)){$flgSe_Retinal_Od=1;}
				if(!empty($elem_periphery_od_summary)){ $elem_periphery_od_summary.="; "; }
				$elem_periphery_od_summary.= "Periphery not examined";
			}
			if($elem_peri_ne_eye_peri=="OU" || $elem_peri_ne_eye_peri=="OS"){
				//if(!isset($bgColor_RV)){$flgSe_Retinal_Os=1;}
				if(!empty($elem_periphery_os_summary)){ $elem_periphery_os_summary.="; "; }
				$elem_periphery_os_summary.= "Periphery not examined";
			}
		}

		list($elem_periphery_od_summary,$elem_periphery_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_peri,"wValOs"=>$wnlStringOs_peri,
																	"wOd"=>$elem_wnlPeriOd,"sOd"=>$elem_periphery_od_summary,
																	"wOs"=>$elem_wnlPeriOs,"sOs"=>$elem_periphery_os_summary));
		//Nochanged
		if(!empty($elem_se_Rv)&&strpos($elem_se_Rv,"=1")!==false){
			$elem_noChangePeri=1;
		}

		$arrDivArcCmn=array();

		if($bgColor_RV != "bgSmoke"){
			$oChartRecArc->setChkTbl("".$this->tbl);
			$arrInpArc = array(
								"elem_sumOdPeriphery"=>array("periphery_od_summary",$elem_periphery_od_summary,"smof","wnlPeriOd",$wnlString_peri,$modi_note_PeriOd),
								"elem_sumOsPeriphery"=>array("periphery_os_summary",$elem_periphery_os_summary,"smof","wnlPeriOs",$wnlString_peri,$modi_note_PeriOs)
								);

			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);

			//Peri --
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdPeriphery"])){
				//echo $arTmpRecArc["div"]["elem_sumOdPeriphery"];
				$arrDivArcCmn["Periphery"]["OD"]=$arTmpRecArc["div"]["elem_sumOdPeriphery"];
				$moeArc["od"]["Peri"] = $arTmpRecArc["js"]["elem_sumOdPeriphery"];
				$flgArcColor["od"]["Peri"] = $arTmpRecArc["css"]["elem_sumOdPeriphery"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdPeriphery"]))
					$elem_periphery_od_summary = $arTmpRecArc["curText"]["elem_sumOdPeriphery"];
			}
			//OS
			if(!empty($arTmpRecArc["div"]["elem_sumOsPeriphery"])){
				//echo $arTmpRecArc["div"]["elem_sumOsPeriphery"];
				$arrDivArcCmn["Periphery"]["OS"]=$arTmpRecArc["div"]["elem_sumOsPeriphery"];
				$moeArc["os"]["Peri"] = $arTmpRecArc["js"]["elem_sumOsPeriphery"];
				$flgArcColor["os"]["Peri"] = $arTmpRecArc["css"]["elem_sumOsPeriphery"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsPeriphery"]))
					$elem_periphery_os_summary = $arTmpRecArc["curText"]["elem_sumOsPeriphery"];
			}
			//Peri --

		}

		$arr=array();

		if(in_array("Periphery",$arrTempProc) || in_array("All",$arrTempProc)){
		//Periphery
		$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Periphery",
											"sOd"=>$elem_periphery_od_summary,"sOs"=>$elem_periphery_os_summary,
											"fOd"=>$flgSe_Peri_Od,"fOs"=>$flgSe_Peri_Os,"pos"=>$elem_posPeri,
											//"arcJsOd"=>$moeArc["od"]["Peri"],"arcJsOs"=>$moeArc["os"]["Peri"],
											"arcCssOd"=>$flgArcColor["od"]["Peri"],"arcCssOs"=>$flgArcColor["os"]["Peri"],
											"elem_periNotExamined"=>$elem_periNotExamined_peri,
											"elem_peri_ne_eye"=>(!isset($bgColor_RV) && !empty($elem_se_Rv) && strpos($elem_se_Rv,"=1")!==false) ? $elem_peri_ne_eye_peri : "",
											//"mnOd"=>$moeMN["od"]["Peri"],"mnOs"=>$moeMN["os"]["Peri"],
											"enm_2"=>"Peri"));

		}

		$arr["seList"]["Peri"]=array("enm"=>"Periphery","pos"=>$elem_posPeri,
							"wOd"=>$elem_wnlPeriOd,"wOs"=>$elem_wnlPeriOs);

		$arr["bgColor"] = "".$bgColor_RV;
		$arr["nochange"] = $elem_ncPeri;
		$arr["examdate"] = $examdate;
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_Peri_Od,$flgSe_Peri_Os);
		//$arr["elem_peri_version"]=$elem_peri_version;
		//$arr["arrDivArcCmn"] = $arrDivArcCmn;
		$arr["arrHx"] = $arrHx;

		//$arr["elem_posPeri"] = $elem_posPeri;
		//$arr["elem_wnlPeri"] = $elem_wnlPeri;
		//$arr["elem_periphery_od_summary"] = $elem_periphery_od_summary;
		//$arr["elem_periphery_os_summary"] = $elem_peripheryl_os_summary;

		//---------

		//echo "<pre>";
		//print_r($arr);
		//exit();

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
			"c9.wnlPeri,".
			"c9.posPeri,".
			"c9.ncPeri,".
			"c9.periphery_od_summary, c9.periphery_os_summary, ".
			"c9.id, ".
			"c9.wnlPeriOd, c9.wnlPeriOs, ".
			"c9.wnl_value_Peri, ".
			"c9.exam_date AS exam_date_RV, ".
			"c9.statusElem AS se_peri, c9.purgerId, c9.purgeTime, c9.purgerId AS purgerId_rv, c9.purgeTime AS purgeTime_rv ".
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
			$elem_wnlMacula = assignZero($row["wnlPeri"]);
			$elem_posMacula = assignZero($row["posPeri"]);
			$elem_ncMacula = assignZero($row["ncPeri"]);
			$elem_wnlMaculaOd = assignZero($row["wnlPeriOd"]);
			$elem_wnlMaculaOs = assignZero($row["wnlPeriOs"]);
			$elem_se_peri = $row["se_peri"];
			$elem_periphery_od_summary = $row["periphery_od_summary"];
			$elem_periphery_os_summary = $row["periphery_os_summary"];
			$elem_peri_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			$modi_note_periArr = unserialize($row["modi_note_periArr"]);
				$arrHx = array();
				if(count($modi_note_periArr)>0 && $row["modi_note_periArr"]!='')
				$arrHx['Peri']	= $modi_note_periArr;

			//wnl
			$elem_wnl_value_Peri = $row["wnl_value_Peri"];

			//is Change is made in new chart -----
			$flgSe_Peri_Od = $flgSe_Peri_Os = "0";
			if(!empty($elem_se_peri)){
				$tmpArrSe = $this->se_elemStatus("RV","0",$elem_se_peri,0,0,1,$elem_retina_version); //working19
				$flgSe_Peri_Od = $tmpArrSe["2"]["od"];
				$flgSe_Peri_Os = $tmpArrSe["2"]["os"];
			}

			//Periphery --
			$wnlString_Peri = !empty($elem_wnl_value_Peri) ? $elem_wnl_value_Peri : $this->getExamWnlStr("Periphery"); //"Clear"
			$wnlStringOd_Peri = $wnlStringOs_Peri = $wnlString_Peri;

			if(empty($flgSe_Peri_Od) && empty($flgSe_Peri_Od_prev) && !empty($elem_wnlPeriOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Periphery", "OD"); if(!empty($tmp)){ $wnlStringOd_Peri = $tmp;}  }
			if(empty($flgSe_Peri_Os) && empty($flgSe_Peri_Os_prev) && !empty($elem_wnlPeriOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Periphery", "OS"); if(!empty($tmp)){ $wnlStringOs_Peri = $tmp;}  }

			list($elem_periphery_od_summary,$elem_periphery_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Peri,"wValOs"=>$wnlStringOs_Peri,
																		"wOd"=>$elem_wnlPeriOd,"sOd"=>$elem_periphery_od_summary,
																		"wOs"=>$elem_wnlPeriOs,"sOs"=>$elem_periphery_os_summary));
			//Nochanged
			if(!empty($elem_se_peri)&&strpos($elem_se_peri,"=1")!==false){
				$elem_noChangePeri=1;
			}

			$arr=array();

			if(in_array("Periphery",$arrTempProc) || in_array("All",$arrTempProc)){
			//Macula
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Periphery",
												"sOd"=>$elem_periphery_od_summary,"sOs"=>$elem_periphery_os_summary,
												"fOd"=>$flgSe_Peri_Od,"fOs"=>$flgSe_Peri_Os,"pos"=>$elem_posPeri,
												"arcCssOd"=>$flgArcColor["os"]["Peri"],"arcCssOs"=>$flgArcColor["os"]["Peri"],
												"enm_2"=>"Peri"));

			}

			//Sub Exam List
			$arr["seList"] = 	array("Peri"=>array("enm"=>"Periphery","pos"=>$elem_posPeri,
								"wOd"=>$elem_wnlPeriOd,"wOs"=>$elem_wnlPeriOs));
			$arr["bgColor"] = "";
			$arr["nochange"] = $elem_ncPeri;
			$arr["examdate"] = $examdate;
			$arr["moeMN"] = $moeMN;
			$arr["exm_flg_se"] = array($flgSe_Peri_Od,$flgSe_Peri_Os);
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
		$wnl_value_Peri = $this->getExamWnlStr("Periphery");

		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_Peri)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_Peri)."') ";
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
					"ncPeri,".
					"ncPeri_od,".
					"ncPeri_os,".
					"wnl_value_Peri";
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
			$wnl_value_Peri="";
			$wnl_value_Peri_phrase="";
			$flgCarry=0;

			//Fundus
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}

			$cQry = "select
					wnlPeri, ".
					"wnlPeriOd,wnlPeriOs,
					".
					"posPeri, ".
					"periphery_od_summary,periphery_os_summary,
					".
					"statusElem AS statusElem_fundus, uid, wnl_value_Peri,
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
			$elem_wnlPeriOd = $wnlPeriOd;
			$elem_wnlPeriOs = $wnlPeriOs;
			$elem_wnlPeri = $wnlPeri;

			$oWv = new WorkView();

			if(in_array("Periphery",$arrTempProc)||in_array("All",$arrTempProc)){
				if((!empty($statusElem_fundus)&&strpos($statusElem_fundus,"0")===false)||
					(empty($periphery_od_summary) && empty($elem_wnlPeriOd))||
					(empty($periphery_os_summary) && empty($elem_wnlPeriOs))
					 ){
				//Toggle Peri
				list($elem_wnlPeriOd,$elem_wnlPeriOs,$elem_wnlPeri) =
										$oWv->toggleWNL($posPeri,$periphery_od_summary,$periphery_os_summary,
														$elem_wnlPeriOd,$elem_wnlPeriOs,$elem_wnlPeri,$exmEye);
				}
			}

			//Status
			$statusElem_fundus_prev=$statusElem_fundus;
			$statusElem_fundus = $this->setEyeStatus($w, $exmEye,$statusElem_fundus,0,$elem_retina_version);

			if(empty($wnl_value_Peri)){
				$wnl_value_Peri=$this->getExamWnlStr("Periphery");
				$wnl_value_Peri_phrase = ", wnl_value_Peri='".sqlEscStr($wnl_value_Peri)."' ";
			}

			//Fundus
			$sql = "UPDATE ".$this->tbl." SET
				  wnlPeri='".$elem_wnlPeri."', ".
				  "wnlPeriOd='".$elem_wnlPeriOd."',
				  wnlPeriOs='".$elem_wnlPeriOs."',
				  ".
				  " exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				  statusElem='".$statusElem_fundus."'
				  ".
				  "
				  ";
			$sql .= " ".$wnl_value_Peri_phrase." ";

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
					$ignoreFlds .= "periphery_od_summary,
								periphery_od,
								wnlPeriOd,
								ncPeri_od,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }
				}else if($_POST["site"]=="OD"){
					$ignoreFlds .= "periphery_os,periphery_os_summary,
								wnlPeriOs,
								ncPeri_os,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posPeri,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Peri,ut_elem,";}
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
						"ncPeri,".
						"ncPeri_od,".
						"ncPeri_os,".
						"wnl_value_Peri";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values
			}
		}
	}

	function isNoChanged(){
		$res= $this->getRecord("ncPeri,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncPeri"]) ){
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
		$elem_ncPeri="0";
		$elem_ncPeri_od="0";
		$elem_ncPeri_os="0";

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
				$elem_ncPeri="1";
				$elem_ncPeri_od="1";
				$elem_ncPeri_os="1";
			}else if($exmEye=="OD"){
				$elem_ncPeri_od="1";
			}else if($exmEye=="OS"){
				$elem_ncPeri_os="1";
			}
		}
		// ---

		//Get status string --
		$statusElem="";
		if($elem_ncPeri_od==1||$elem_ncPeri_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncPeri_od,$elem_ncPeri_os,0);}
		//Get status--

		//
		$sql = "UPDATE ".$this->tbl."
			  SET
			  ncPeri = '".$elem_ncPeri."',
			  ncPeri_od='".$elem_ncPeri_od."', ncPeri_os='".$elem_ncPeri_os."',
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}

	function savePeriphery(){
		$patientId = $this->pid;
		$form_id = $this->fid;
		$pne = $_GET["pne"];
		$pne_i = $_GET["pne_i"];
		//Test
		$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
		if(!empty($patientId) && !empty($form_id)){

			//
			$statusElem = "";
			$res= $this->getRecord("statusElem");
			if($res!=false){
				if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
					$statusElem = $res["statusElem"];
				}
			}
			$statusElem = $this->setEyeStatus("Periphery", $pne_i, $statusElem);

			$sql = "UPDATE ".$this->tbl." SET
				exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."' ";
			$sql .=", ut_elem = CONCAT(ut_elem,\"|".$logged_user_type."@elem_periNotExamined_peri,elem_peri_ne_eye_peri,|\"),
				periNotExamined = '".$pne."',
				peri_ne_eye = '".$pne_i."', statusElem = '".$statusElem."'	";
			$sql .="WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
			$res = sqlQuery($sql);
		}
	}

	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "periphery_od";
		$arr["db"]["xmlOs"] = "periphery_os";
		$arr["db"]["wnlSE"] = "wnlPeri";
		$arr["db"]["wnlOd"] = "wnlPeriOd";
		$arr["db"]["wnlOs"] = "wnlPeriOs";
		$arr["db"]["posSE"] = "posPeri";
		$arr["db"]["summOd"] = "periphery_od_summary";
		$arr["db"]["summOs"] = "periphery_os_summary";
		$arr["divSe"] = "3";
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
