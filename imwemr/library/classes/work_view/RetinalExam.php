<?php

class RetinalExam extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_retinal_exam";
		$this->examName="RetinalExam";
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

		$sql = "SELECT * FROM ".$this->tbl." WHERE form_id = '".$elem_formId."' AND patient_id='".$patient_id."' AND purged = '0' ";
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
				$ar_ret["elem_examDateRetinal"]=$exam_date;
			}
			$ar_ret["elem_retinalId_LF"] = $ar_ret["elem_retinalId"]=$id;
			$ar_ret["elem_editModeRetinal"] = $elem_editMode;

			$retinal_od= stripslashes($retinal_od);
			$retinal_os= stripslashes($retinal_os);

			$arr_vals_retinal_od = $oExamXml->extractXmlValue($retinal_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_retinal_od);
			//extract($arr_vals_retinal_od);
			$arr_vals_retinal_os = $oExamXml->extractXmlValue($retinal_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_retinal_os);
			//extract($arr_vals_retinal_os);

			$ar_ret["elem_wnlRetinal"] = $wnlRetinal;
			$ar_ret["elem_posRetinal"] = $posRetinal;
			$ar_ret["elem_ncRetinal"] =($elem_editMode==1) ? $ncRetinal : 0 ;

			$ar_ret["elem_wnlRetinalOd"]= $wnlRetinalOd;
			$ar_ret["elem_wnlRetinalOs"]= $wnlRetinalOs;

			$ar_ret["elem_statusElementsRetinal"] = ($elem_editMode==0) ? "" : $statusElem;

			$ar_ret["elem_periNotExamined"]=($elem_editMode==1) ? $row["periNotExamined"] : "";
			$ar_ret["elem_peri_ne_eye"]=($elem_editMode==1) ? $row["peri_ne_eye"] : "" ;
			$ar_ret["el_lens_used"]= $row["lens_used"] ;

			//UT Elems //($elem_editMode==1) ? : "";
			$ar_ret["elem_utElemsRetinal"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;

			$ar_ret["retinal_od_summary"] = trim($retinal_od_summary);
			$ar_ret["retinal_os_summary"] = trim($retinal_os_summary);

			//Emergency  note --
			$ar_ret["elem_emerstt_lvlSeverityRetFind"] = $emerstt_lvlSeverityRetFind;
			$ar_ret["elem_emerstt_macEdFind"] = $emerstt_macEdFind;
			$ar_ret["elem_emerstt_comm_p2p"] = $emerstt_comm_p2p;
			$ar_ret["elem_emerstt_lvlSeverity"] = $emerstt_lvlSeverity;

			//Emergency  note --

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
		$tmpd = "elem_chng_div7_Od";
		$tmps = "elem_chng_div7_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = ($$tmpd == "1") ? "1" : "0";
		$arrSe[$tmps] = ($$tmps == "1") ? "1" : "0";
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);

		//Retinal -----------
		$wnlRetinalOd = $wnlRetinalOs = $wnlRetinal = $posRetinal = $ncRetinal = "0";
		//if(!empty($elem_chng_div1_Od) || !empty($elem_chng_div1_Os)){
		//	if(!empty($elem_chng_div1_Od)){
				$menuName = "RetinalOd";
				$menuFilePath = $arXmlFiles["retinal"]["od"]; //dirname(__FILE__)."/xml/retinal_od.xml";
				$elem_retinal_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlRetinalOd = $_POST["elem_wnlRetinalOd"];
		//	}

		//	if(!empty($elem_chng_div1_Os)){
				$menuName = "RetinalOs";
				$menuFilePath = $arXmlFiles["retinal"]["os"]; //dirname(__FILE__)."/xml/retinal_os.xml";
				$elem_retinal_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlRetinalOs = $_POST["elem_wnlRetinalOs"];
		//	}

			$wnlRetinal = (!empty($wnlRetinalOd) && !empty($wnlRetinalOs)) ? "1" : "0"; // $_POST["elem_wnlVitreous"];
			$posRetinal = $_POST["elem_posRetinal"];
			$ncRetinal = $_POST["elem_ncRetinal"];
		//}
		//Retinal -----------

		$periNotExamined  = $_POST["elem_periNotExamined"];
		$peri_ne_eye = (!empty($periNotExamined)) ? $_POST["elem_peri_ne_eye"] : "" ;

		$lensUsed  = $_POST["el_lens_used"];

		$examDate = wv_dt("now"); //$_POST["elem_examDate"];
		$oUserAp = new UserAp();

		// Summary --
		$strExamsAllOd = $strExamsAllOs = "";
		$elem_retinal_os_summary = $elem_retinal_od_summary = "";
		//Retinal
		$retinal_od = $elem_retinal_od;
		$arrTemp = $this->getExamSummary($retinal_od);
		$elem_retinal_od_summary = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div7_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Retinal Exam",$arrExmDone_od,$elem_retinal_od_summary);
		}
		$retinal_os = $elem_retinal_os;
		$arrTemp = $this->getExamSummary($retinal_os);
		$elem_retinal_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div7_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Retinal Exam",$arrExmDone_os,$elem_retinal_os_summary);
		}
		//--

		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsRetinal"];
		$elem_utElems_cur = $_POST["elem_utElemsRetinal_cur"];
		$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);
		//ut_elems ----------------------

		//Emergency Notes --
		$emerstt_lvlSeverityRetFind = "";
		$emerstt_macEdFind = "";
		$emerstt_lvlSeverity = "";
		$emerstt_comm_p2p = $_POST["elem_emerstt_comm_p2p_nodone"];
		if(trim($emerstt_comm_p2p) == "Not done"){
			if(!empty($_POST["elem_emerstt_macEdFind_absent"])) { $emerstt_macEdFind = $_POST["elem_emerstt_macEdFind_absent"];}
			else if(!empty($_POST["elem_emerstt_macEdFind_present"])) { $emerstt_macEdFind = $_POST["elem_emerstt_macEdFind_present"];}
			if(!empty($_POST["elem_emerstt_lvlSeverityRetFind"])) {	$emerstt_lvlSeverityRetFind = $_POST["elem_emerstt_lvlSeverityRetFind"];	}
			if(!empty($_POST["elem_emerstt_lvlSeverity"])) {	$emerstt_lvlSeverity = $_POST["elem_emerstt_lvlSeverity"];	}
		}

		//Emergency Notes --

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

			$elem_retinal_od = sqlEscStr($elem_retinal_od);
			$elem_retinal_os = sqlEscStr($elem_retinal_os);

			//check
			$cQry = "select
						id, last_opr_id, uid,
						retinal_od_summary, retinal_os_summary, wnlRetinalOd, wnlRetinalOs, modi_note_retinalArr, wnl_value_RetinalExam,
						exam_date
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$elem_editMode = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$retinalId=$retinalIDExam = $row["id"];
				$elem_editMode = "1";
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				//Modifying Notes----------------
				$seri_modi_note_retinalArr = $owv->getModiNotesArr($row["retinal_od_summary"],$elem_retinal_od_summary,$last_opr_id,'OD',$row["modi_note_retinalArr"],$row['exam_date']);
				$seri_modi_note_retinalArr = $owv->getModiNotesArr($row["retinal_os_summary"],$elem_retinal_os_summary,$last_opr_id,'OS',$seri_modi_note_retinalArr,$row['exam_date']);
				//Modifying Notes----------------
			}

			//
			$sql_con = "
				retinal_od = '".$elem_retinal_od."',
				retinal_os = '".$elem_retinal_os."',
				retinal_od_summary = '".$elem_retinal_od_summary."',
				retinal_os_summary = '".$elem_retinal_os_summary."',
				wnlRetinal='".$wnlRetinal."',
				posRetinal='".$posRetinal."',
				ncRetinal='".$ncRetinal."',
				wnlRetinalOd='".$wnlRetinalOd."',
				wnlRetinalOs='".$wnlRetinalOs."',
				periNotExamined = '".$periNotExamined."',
				peri_ne_eye = '".$peri_ne_eye."',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				ut_elem = '".sqlEscStr($ut_elem)."',
				last_opr_id = '".$last_opr_id."',
				emerstt_lvlSeverityRetFind = '".$emerstt_lvlSeverityRetFind."',
				emerstt_macEdFind = '".$emerstt_macEdFind."',
				emerstt_comm_p2p = '".$emerstt_comm_p2p."',
				emerstt_lvlSeverity = '".$emerstt_lvlSeverity."',
				modi_note_retinalArr = '".sqlEscStr($seri_modi_note_retinalArr)."',
				lens_used = '".$lensUsed."'
			";

			//
			if($elem_editMode == "0"){
				//WNL
				$wnl_value_RetinalExam = $this->getExamWnlStr("Retinal Exam");
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',
					wnl_value_RetinalExam='".sqlEscStr($wnl_value_RetinalExam)."',
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
				$insertId = $retinalId;
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
			"c9.wnlRetinal, ".
			"c9.posRetinal, ".
			"c9.ncRetinal, ".
			"c9.retinal_od_summary, ".
			"c9.retinal_os_summary, ".
			"c9.id, ".
			"c9.wnlRetinalOd, ".
			"c9.wnlRetinalOs, ".
			"c9.periNotExamined, c9.peri_ne_eye, ".
			"c9.wnl_value_RetinalExam, ".
			"c9.exam_date AS exam_date_RV, ".
			"c9.statusElem AS se_rv, ".
			"c9.lens_used, ".
			"c9.modi_note_retinalArr  ".
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

			$elem_noChangeRv=assignZero($row["chRv"]);
			//Rv
			$elem_se_Rv = $row["se_rv"];
			$elem_rv_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date_RV"]);
			$modi_note_retinalArr  = unserialize($row["modi_note_retinalArr"]);

				$arrHx = array();
				if(is_array($modi_note_retinalArr) && count($modi_note_retinalArr)>0 && $row["modi_note_retinalArr"]!='')
				$arrHx['Retinal'] = $modi_note_retinalArr;

			//wnl
			$elem_wnl_value_RetinalExam = $row["wnl_value_RetinalExam"];
			//New
			$elem_retina_version="new";
			$modi_note_RetinalOd=$row["modi_note_RetinalOd"];
			$modi_note_RetinalOs=$row["modi_note_RetinalOs"];
			//
			$elem_periNotExamined = $row["periNotExamined"];
			$elem_peri_ne_eye = $row["peri_ne_eye"];
			$elem_retinal_os_summary = $row["retinal_os_summary"];
			$elem_wnlRetinal = assignZero($row["wnlRetinal"]);
			$elem_posRetinal = assignZero($row["posRetinal"]);
			$elem_ncRetinal = assignZero($row["ncRetinal"]);
			$elem_wnlRetinalOd = assignZero($row["wnlRetinalOd"]);
			$elem_wnlRetinalOs = assignZero($row["wnlRetinalOs"]);
			$elem_retinal_od_summary = $row["retinal_od_summary"];
			$el_lens_used = $row["lens_used"];


		}

		if(empty($elem_rv_id)){

			$tmp = "c2.wnlRetinal, c2.posRetinal, c2.ncRetinal, c2.retinal_od_summary, c2.retinal_os_summary,
					c2.id, c2.wnlRetinalOd, c2.wnl_value_RetinalExam,
					c2.wnlRetinalOs, c2.exam_date AS exam_date_RV,
					c2.periNotExamined, c2.peri_ne_eye, c2.lens_used,
					c2.statusElem AS se_rv
					";
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordR_v($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				$examdate = wv_formatDate($row["exam_date_RV"]);

				//wnl
				$elem_wnl_value_RetinalExam = $row["wnl_value_RetinalExam"];
				$elem_se_Rv_prev = $row["se_rv"];
				//retinal exam
				$elem_retina_version="new";
				$elem_retinal_od_summary = $row["retinal_od_summary"];
				$elem_retinal_os_summary = $row["retinal_os_summary"];
				$elem_wnlRetinal = assignZero($row["wnlRetinal"]);
				$elem_wnlRetinalOd = assignZero($row["wnlRetinalOd"]);
				$elem_wnlRetinalOs = assignZero($row["wnlRetinalOs"]);
				$elem_posRetinal = assignZero($row["posRetinal"]);
				$elem_ncRetinal = assignZero($row["ncRetinal"]);
				$elem_periNotExamined = $row["periNotExamined"];
				$elem_peri_ne_eye = $row["peri_ne_eye"];
				$el_lens_used = $row["lens_used"];

			}

			if(empty($elem_retinal_od_summary) && empty($elem_retinal_od_summary) && empty($elem_wnlRetinalOd) && empty($elem_wnlRetinalOs) && empty($elem_posRetinal)){

				global $ar_macula, $ar_periphery, $ar_blood_vessels;



				if(!empty($ar_macula["elem_retinal_od_summary"]) || !empty($ar_macula["elem_retinal_os_summary"]) ||
					!empty($ar_macula["elem_wnlRetinal"]) || !empty($ar_macula["elem_posRetinal"]) ||
					!empty($ar_periphery["elem_retinal_od_summary"]) || !empty($ar_periphery["elem_retinal_os_summary"]) ||
					!empty($ar_periphery["elem_wnlRetinal"]) || !empty($ar_periphery["elem_posRetinal"]) ||
					!empty($ar_blood_vessels["elem_retinal_od_summary"]) || !empty($ar_blood_vessels["elem_retinal_os_summary"]) ||
					!empty($ar_blood_vessels["elem_wnlRetinal"]) || !empty($ar_blood_vessels["elem_posRetinal"])
				){
					//New version add summaries  of Macula, Periphery and BV to Retinal exam---
					$elem_retina_version="new";
					$elem_retinal_od_summary = trim("".$ar_macula["elem_retinal_od_summary"]."\n".$ar_periphery["elem_retinal_od_summary"]."\n".$ar_blood_vessels["elem_retinal_od_summary"]);
					$elem_retinal_os_summary = trim("".$ar_macula["elem_retinal_os_summary"]."\n".$ar_periphery["elem_retinal_os_summary"]."\n".$ar_blood_vessels["elem_retinal_os_summary"]);

					if(!empty($ar_macula["elem_posRetinal"]) || !empty($ar_periphery["elem_posRetinal"]) || !empty($ar_blood_vessels["elem_posRetinal"])){
						$elem_posRetinal="1";
					}else	if(!empty($ar_macula["elem_wnlRetinal"]) && !empty($ar_periphery["elem_wnlRetinal"]) && !empty($ar_blood_vessels["elem_wnlRetinal"])  ){
							$elem_wnlRetinal = $elem_wnlRetinalOd = $elem_wnlRetinalOs = "1";
					}
					//New version add summaries  of Macula, Periphery and BV to Retinal exam---
				}
			}
			//BG
			$bgColor_RV = "bgSmoke";
		}

		//is Change is made in new chart -----
		$flgSe_Retinal_Od = $flgSe_Retinal_Os = "0";
		if(!isset($bgColor_RV)){
			if(!empty($elem_se_Rv)){
				$tmpArrSe = $this->se_elemStatus("RV","0",$elem_se_Rv,0,0,1,$elem_retina_version); //working19
				$flgSe_Retinal_Od = $tmpArrSe["7"]["od"];
				$flgSe_Retinal_Os = $tmpArrSe["7"]["os"];
			}
		}else{
			if(!empty($elem_se_Rv_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("RV","0",$elem_se_Rv_prev,0,0,1,$elem_retina_version); //working19
				$flgSe_Retinal_Od_prev = $tmpArrSe_prev["7"]["od"];
				$flgSe_Retinal_Os_prev = $tmpArrSe_prev["7"]["os"];
			}
		}

		$wnlText_RetinalOd = $wnlText_RetinalOs = $wnlString = !empty($elem_wnl_value_RetinalExam) ? $elem_wnl_value_RetinalExam." " : $this->getExamWnlStr("Retinal Exam")." ";//"Macula normal, vessels normal course and caliber, ";

		if(empty($flgSe_Retinal_Od) && empty($flgSe_Retinal_Od_prev) && !empty($elem_wnlRetinalOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Retinal Exam", "OD"); if(!empty($tmp)){ $wnlText_RetinalOd = $tmp;}  }
		if(empty($flgSe_Retinal_Os) && empty($flgSe_Retinal_Os_prev) && !empty($elem_wnlRetinalOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Retinal Exam", "OS"); if(!empty($tmp)){ $wnlText_RetinalOs = $tmp;}  }


		//*//16-09-2015: For WNL, please remove this condition of static appending text. Arun says, they should use template now
		if(isset($GLOBALS["ADD_PERI_WNL_2_RETINAL"])&&!empty($GLOBALS["ADD_PERI_WNL_2_RETINAL"])){
		if($elem_periNotExamined!=1){
			$wnlText_RetinalOd .= "periphery normal";
			$wnlText_RetinalOs .= "periphery normal";
		}else{
			//$wnlText_Retinal .= "periphery not examined";

			if($elem_peri_ne_eye=="OU"){
				$wnlText_RetinalOd .= "periphery not examined";
				$wnlText_RetinalOs .= "periphery not examined";
			}else if($elem_peri_ne_eye=="OD"){
				$wnlText_RetinalOd .= "periphery not examined";
				$wnlText_RetinalOs .= "periphery normal";
			}else if($elem_peri_ne_eye=="OS"){
				$wnlText_RetinalOs .= "periphery not examined";
				$wnlText_RetinalOd .= "periphery normal";
			}

		}
		$wnlText_RetinalOd = trim($wnlText_RetinalOd);
		$wnlText_RetinalOs = trim($wnlText_RetinalOs);
		}

		//Add periphery in summary
		if(!empty($elem_periNotExamined)&&!empty($elem_peri_ne_eye)){
			//if(!isset($bgColor_RV)){$elem_posRetinal=1;}
			if($elem_peri_ne_eye=="OU" || $elem_peri_ne_eye=="OD"){
				//if(!isset($bgColor_RV)){$flgSe_Retinal_Od=1;}
				if(!empty($elem_retinal_od_summary)){ $elem_retinal_od_summary.="; "; }
				$elem_retinal_od_summary.= "Periphery not examined";
			}
			if($elem_peri_ne_eye=="OU" || $elem_peri_ne_eye=="OS"){
				//if(!isset($bgColor_RV)){$flgSe_Retinal_Os=1;}
				if(!empty($elem_retinal_os_summary)){ $elem_retinal_os_summary.="; "; }
				$elem_retinal_os_summary.= "Periphery not examined";
			}
		}

		//Retina --
		list($elem_retinal_od_summary,$elem_retinal_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlText_RetinalOd, "wValOs"=>$wnlText_RetinalOs,
																	"wOd"=>$elem_wnlRetinalOd,"sOd"=>$elem_retinal_od_summary,
																	"wOs"=>$elem_wnlRetinalOs,"sOs"=>$elem_retinal_os_summary));

		//Nochanged
		if(!empty($elem_se_Rv)&&strpos($elem_se_Rv,"=1")!==false){
			$elem_noChangeRv=1;
		}

		$arrDivArcCmn=array();

		if($bgColor_RV != "bgSmoke"){
			$oChartRecArc->setChkTbl("".$this->tbl);
			$arrInpArc = array(		"elem_sumOdRetinal"=>array("retinal_od_summary",$elem_retinal_od_summary,"smof","wnlRetinalOd",$wnlText_RetinalOd,$modi_note_RetinalOd),
								"elem_sumOsRetinal"=>array("retinal_os_summary",$elem_retinal_os_summary,"smof","wnlRetinalOs",$wnlText_RetinalOs,$modi_note_RetinalOs)

								);

			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);

			//Ret --
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdRetinal"])){
				//echo $arTmpRecArc["div"]["elem_sumOdRetinal"];
				$arrDivArcCmn["Retinal"]["OD"]=$arTmpRecArc["div"]["elem_sumOdRetinal"];
				$moeArc["od"]["Ret"] = $arTmpRecArc["js"]["elem_sumOdRetinal"];
				$flgArcColor["od"]["Ret"] = $arTmpRecArc["css"]["elem_sumOdRetinal"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdRetinal"]))
					$elem_vitreous_od_summary = $arTmpRecArc["curText"]["elem_sumOdRetinal"];
			}
			//Os
			if(!empty($arTmpRecArc["div"]["elem_sumOsRetinal"])){
				//echo $arTmpRecArc["div"]["elem_sumOsRetinal"];
				$arrDivArcCmn["Retinal"]["OS"]=$arTmpRecArc["div"]["elem_sumOsRetinal"];
				$moeArc["os"]["Ret"] = $arTmpRecArc["js"]["elem_sumOsRetinal"];
				$flgArcColor["os"]["Ret"] = $arTmpRecArc["css"]["elem_sumOsRetinal"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsRetinal"]))
					$elem_retinal_os_summary = $arTmpRecArc["curText"]["elem_sumOsRetinal"];
			}
			//Ret --

		}

		$arr=array();

		//if(in_array("Retinal Exam",$arrTempProc) || in_array("All",$arrTempProc)){
		//Retinal
		$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Retinal Exam",
											"sOd"=>$elem_retinal_od_summary,"sOs"=>$elem_retinal_os_summary,
											"fOd"=>$flgSe_Retinal_Od,"fOs"=>$flgSe_Retinal_Os,"pos"=>$elem_posRetinal,
											//"arcJsOd"=>$moeArc["od"]["Ret"],"arcJsOs"=>$moeArc["os"]["Ret"],
											"arcCssOd"=>$flgArcColor["os"]["Ret"],"arcCssOs"=>$flgArcColor["os"]["Ret"],
											"elem_periNotExamined"=>$elem_periNotExamined,
											"elem_peri_ne_eye"=>(!isset($bgColor_RV) && !empty($elem_se_Rv) && strpos($elem_se_Rv,"=1")!==false) ? $elem_peri_ne_eye : "",
											//"mnOd"=>$moeMN["od"]["Ret"],"mnOs"=>$moeMN["os"]["Ret"],
											"enm_2"=>"Ret"));
		//}

		$arr["seList"]["Ret"]=array("enm"=>"Retinal Exam","pos"=>$elem_posRetinal,
								"wOd"=>$elem_wnlRetinalOd,"wOs"=>$elem_wnlRetinalOs);

		$arr["bgColor"] = "".$bgColor_RV;
		$arr["nochange"] = $elem_ncMacula;
		$arr["examdate"] = $examdate;
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_Retinal_Od,$flgSe_Retinal_Os);
		//$arr["elem_periNotExamined"] = $elem_periNotExamined;
		//$arr["elem_peri_ne_eye"] = (!isset($bgColor_RV)) ? $elem_peri_ne_eye : "" ;///Send if Not Prev value
		$arr["lens_used"] = $el_lens_used;

		//echo "<pre>";
		//print_r($arr);
		//exit();

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
			"c9.wnlRetinal,".
			"c9.posRetinal,".
			"c9.ncRetinal,".
			"c9.retinal_od_summary, c9.retinal_os_summary, ".
			"c9.id, ".
			"c9.wnlRetinalOd, c9.wnlRetinalOs, ".
			"c9.periNotExamined, c9.peri_ne_eye, ".
			"c9.wnl_value_RetinalExam, ".
			"c9.exam_date AS exam_date_RV, ".
			"c9.statusElem AS se_rv, c9.purgerId, c9.purgeTime, c9.purgerId AS purgerId_rv, c9.purgeTime AS purgeTime_rv ".
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
			$elem_noChangeRv=assignZero($row["chRv"]);
			//Rv
			$elem_se_Rv = $row["se_rv"];
			$elem_rv_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date_RV"]);
			$modi_note_retinalArr  = unserialize($row["modi_note_retinalArr"]);

				$arrHx = array();
				if(count($modi_note_retinalArr)>0 && $row["modi_note_retinalArr"]!='')
				$arrHx['Retinal'] = $modi_note_retinalArr;

			//wnl
			$elem_wnl_value_RetinalExam = $row["wnl_value_RetinalExam"];
			//New
			$elem_retina_version="new";
			$modi_note_RetinalOd=$row["modi_note_RetinalOd"];
			$modi_note_RetinalOs=$row["modi_note_RetinalOs"];
			//
			$elem_periNotExamined = $row["periNotExamined"];
			$elem_peri_ne_eye = $row["peri_ne_eye"];
			$elem_retinal_os_summary = $row["retinal_os_summary"];
			$elem_wnlRetinal = assignZero($row["wnlRetinal"]);
			$elem_posRetinal = assignZero($row["posRetinal"]);
			$elem_ncRetinal = assignZero($row["ncRetinal"]);
			$elem_wnlRetinalOd = assignZero($row["wnlRetinalOd"]);
			$elem_wnlRetinalOs = assignZero($row["wnlRetinalOs"]);
			$elem_retinal_od_summary = $row["retinal_od_summary"];

			//is Change is made in new chart -----
			$flgSe_Retinal_Od = $flgSe_Retinal_Os = "0";
			if(!empty($elem_se_Rv)){
				$tmpArrSe = $this->se_elemStatus("RV","0",$elem_se_Rv,0,0,1,$elem_retina_version); //working19
				$flgSe_Retinal_Od = $tmpArrSe["7"]["od"];
				$flgSe_Retinal_Os = $tmpArrSe["7"]["os"];
			}

			$wnlText_RetinalOd = $wnlText_RetinalOs = $wnlString = !empty($elem_wnl_value_RetinalExam) ? $elem_wnl_value_RetinalExam." " : $this->getExamWnlStr("Retinal Exam")." ";//"Macula normal, vessels normal course and caliber, ";

			if(empty($flgSe_Retinal_Od) && empty($flgSe_Retinal_Od_prev) && !empty($elem_wnlRetinalOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Retinal Exam", "OD"); if(!empty($tmp)){ $wnlText_RetinalOd = $tmp;}  }
			if(empty($flgSe_Retinal_Os) && empty($flgSe_Retinal_Os_prev) && !empty($elem_wnlRetinalOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Retinal Exam", "OS"); if(!empty($tmp)){ $wnlText_RetinalOs = $tmp;}  }


			//*//16-09-2015: For WNL, please remove this condition of static appending text. Arun says, they should use template now
			if(isset($GLOBALS["ADD_PERI_WNL_2_RETINAL"])&&!empty($GLOBALS["ADD_PERI_WNL_2_RETINAL"])){
			if($elem_periNotExamined!=1){
				$wnlText_RetinalOd .= "periphery normal";
				$wnlText_RetinalOs .= "periphery normal";
			}else{
				//$wnlText_Retinal .= "periphery not examined";

				if($elem_peri_ne_eye=="OU"){
					$wnlText_RetinalOd .= "periphery not examined";
					$wnlText_RetinalOs .= "periphery not examined";
				}else if($elem_peri_ne_eye=="OD"){
					$wnlText_RetinalOd .= "periphery not examined";
					$wnlText_RetinalOs .= "periphery normal";
				}else if($elem_peri_ne_eye=="OS"){
					$wnlText_RetinalOs .= "periphery not examined";
					$wnlText_RetinalOd .= "periphery normal";
				}

			}
			}

			//Add periphery in summary
			if(!empty($elem_periNotExamined)&&!empty($elem_peri_ne_eye)){
				//if(!isset($bgColor_RV)){$elem_posRetinal=1;}
				if($elem_peri_ne_eye=="OU" || $elem_peri_ne_eye=="OD"){
					//if(!isset($bgColor_RV)){$flgSe_Retinal_Od=1;}
					if(!empty($elem_retinal_od_summary)){ $elem_retinal_od_summary.="; "; }
					$elem_retinal_od_summary.= "Periphery not examined";
				}
				if($elem_peri_ne_eye=="OU" || $elem_peri_ne_eye=="OS"){
					//if(!isset($bgColor_RV)){$flgSe_Retinal_Os=1;}
					if(!empty($elem_retinal_os_summary)){ $elem_retinal_os_summary.="; "; }
					$elem_retinal_os_summary.= "Periphery not examined";
				}
			}

			//Retina --
			list($elem_retinal_od_summary,$elem_retinal_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlText_RetinalOd, "wValOs"=>$wnlText_RetinalOs,
																		"wOd"=>$elem_wnlRetinalOd,"sOd"=>$elem_retinal_od_summary,
																		"wOs"=>$elem_wnlRetinalOs,"sOs"=>$elem_retinal_os_summary));

			//Nochanged
			if(!empty($elem_se_Rv)&&strpos($elem_se_Rv,"=1")!==false){
				$elem_noChangeRv=1;
			}

			$arr=array();

			if(in_array("Retinal Exam",$arrTempProc) || in_array("All",$arrTempProc)){
			//Retinal
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Retinal Exam",
												"sOd"=>$elem_retinal_od_summary,"sOs"=>$elem_retinal_os_summary,
												"fOd"=>$flgSe_Retinal_Od,"fOs"=>$flgSe_Retinal_Os,"pos"=>$elem_posRetinal,
												//"arcJsOd"=>$moeArc["od"]["Ret"],"arcJsOs"=>$moeArc["os"]["Ret"],
												"arcCssOd"=>$flgArcColor["os"]["Ret"],"arcCssOs"=>$flgArcColor["os"]["Ret"],
												//"mnOd"=>$moeMN["od"]["Ret"],"mnOs"=>$moeMN["os"]["Ret"],
												"enm_2"=>"Ret"));
			}

			$arr["seList"]["Ret"]=array("enm"=>"Retinal Exam","pos"=>$elem_posRetinal,
									"wOd"=>$elem_wnlRetinalOd,"wOs"=>$elem_wnlRetinalOs);

			$arr["bgColor"] = "".$bgColor_RV;
			$arr["nochange"] = $elem_ncMacula;
			$arr["examdate"] = $examdate;
			$arr["moeMN"] = $moeMN;
			$arr["exm_flg_se"] = array($flgSe_Retinal_Od,$flgSe_Retinal_Os);
			$arr["elem_periNotExamined"] = $elem_periNotExamined;
			$arr["elem_peri_ne_eye"] = (!isset($bgColor_RV)) ? $elem_peri_ne_eye : "" ;///Send if Not Prev value

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
		$wnl_value_RetinalExam = $this->getExamWnlStr("Retinal Exam");

		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_RetinalExam)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_RetinalExam)."') ";
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
					"ncRetinal,".
					"ncRetinal_od,".
					"ncRetinal_os,".
					"wnl_value_RetinalExam";
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
			$elem_retina_version="new";
			$wnl_value_RetinalExam="";
			$wnl_value_RetinalExam_phrase="";
			$flgCarry=0;

			//Fundus
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}

			$cQry = "select
					wnlRetinal, ".
				"wnlRetinalOd,wnlRetinalOs,".
				"posRetinal, ".
				"retinal_od_summary,retinal_os_summary,".
				"statusElem AS statusElem_fundus, uid, wnl_value_RetinalExam,
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
			$elem_wnlRetinalOd = $wnlRetinalOd;
			$elem_wnlRetinalOs = $wnlRetinalOs;
			$elem_wnlRetinal = $wnlRetinal;

			//
			$oWv = new WorkView();

			if(in_array("Macula",$arrTempProc) || in_array("Periphery",$arrTempProc) || in_array("Blood Vessels",$arrTempProc) || in_array("Retinal Exam",$arrTempProc) ||in_array("All",$arrTempProc)){
			if((!empty($statusElem_fundus)&&strpos($statusElem_fundus,"0")===false)||
				(empty($retinal_od_summary)&&empty($elem_wnlRetinalOd))||
				(empty($retinal_os_summary)&&empty($elem_wnlRetinalOs))
				 ){
			//Toggle Vitreous
			list($elem_wnlRetinalOd,$elem_wnlRetinalOs,$elem_wnlRetinal) =
									$oWv->toggleWNL($posRetinal,$retinal_od_summary,$retinal_os_summary,
													$elem_wnlRetinalOd,$elem_wnlRetinalOs,$elem_wnlRetinal,$exmEye);
			}
			}

			//Status
			$statusElem_fundus_prev=$statusElem_fundus;
			$statusElem_fundus = $this->setEyeStatus($w, $exmEye,$statusElem_fundus,0,$elem_retina_version);

			if(empty($wnl_value_RetinalExam)){
				$wnl_value_RetinalExam=$this->getExamWnlStr("Retinal Exam");
				$wnl_value_RetinalExam_phrase = ", wnl_value_RetinalExam='".sqlEscStr($wnl_value_RetinalExam)."' ";
			}

			//Fundus
			$sql = "UPDATE ".$this->tbl." SET ".
				  " exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				  statusElem='".$statusElem_fundus."',
				  " ;
			$sql .=  "wnlRetinal='".$elem_wnlRetinal."',
				  wnlRetinalOd='".$elem_wnlRetinalOd."',
				  wnlRetinalOs='".$elem_wnlRetinalOs."'
				  ";
			$sql .= " ".$wnl_value_RetinalExam_phrase." ";

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
					$ignoreFlds .= "retinal_od_summary,
								retinal_od,
								wnlRetinalOd,
								ncRetinal_od,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }
				}else if($_POST["site"]=="OD"){
					$ignoreFlds .= "retinal_os,retinal_os_summary,
								wnlRetinalOs,
								ncRetinal_os,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posRetinal,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_RetinalExam,ut_elem,";}
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
						"ncRetinal,".
						"ncRetinal_od,".
						"ncRetinal_os,".
						"wnl_value_RetinalExam";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values
			}
		}
	}

	function isNoChanged(){
		$res= $this->getRecord("ncRetinal,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncRetinal"]) ){
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
		$elem_ncRetinal="0";
		$elem_ncRetinal_od="0";
		$elem_ncRetinal_os="0";

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
				$elem_ncRetinal="1";
				$elem_ncRetinal_od="1";
				$elem_ncRetinal_os="1";
			}else if($exmEye=="OD"){
				$elem_ncRetinal_od="1";
			}else if($exmEye=="OS"){
				$elem_ncRetinal_os="1";
			}
		}
		// ---

		//Get status string --
		$statusElem="";
		if($elem_ncRetinal_od==1||$elem_ncRetinal_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncRetinal_od,$elem_ncRetinal_os,0);}
		//Get status--

		//
		$sql = "UPDATE ".$this->tbl."
			  SET
			  ncRetinal = '".$elem_ncRetinal."',
			  ncRetinal_od='".$elem_ncRetinal_od."', ncRetinal_os='".$elem_ncRetinal_os."',
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";

		//echo "CHK: ".$exmEye." - ".$sql;
		//exit();

		$res = sqlQuery($sql);
	}

	function savePeriphery(){
		$patientId = $this->pid; //$_SESSION["patient"];
		$form_id = $this->fid; //$_REQUEST["fid"];
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
			$statusElem = $this->setEyeStatus("RetinalExam", $pne_i, $statusElem);

			$sql = "UPDATE ".$this->tbl." SET
				exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."' ";
			$sql .=", ut_elem = CONCAT(ut_elem,\"|".$logged_user_type."@elem_periNotExamined,elem_peri_ne_eye,|\"),
				periNotExamined = '".$pne."',
				peri_ne_eye = '".$pne_i."', statusElem = '".$statusElem."' ";
			$sql .="WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
			$res = sqlQuery($sql);
		}
	}

	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "retinal_od";
		$arr["db"]["xmlOs"] = "retinal_os";
		$arr["db"]["wnlSE"] = "wnlRetinal";
		$arr["db"]["wnlOd"] = "wnlRetinalOd";
		$arr["db"]["wnlOs"] = "wnlRetinalOs";
		$arr["db"]["posSE"] = "posRetinal";
		$arr["db"]["summOd"] = "retinal_od_summary";
		$arr["db"]["summOs"] = "retinal_os_summary";
		$arr["divSe"] = "7";
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
