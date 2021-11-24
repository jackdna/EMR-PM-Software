<?php

class BloodVessels extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_blood_vessels";
		$this->examName="Vessels";
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
				$ar_ret["elem_examDate"]=$exam_date;
			}

			$ar_ret["elem_bvId_LF"] = $ar_ret["elem_bvId"]=$id;
			$ar_ret["elem_statusElementsBV"] = ($elem_editMode==0) ? "" : $statusElem;
			$ar_ret["elem_editModeBV"] = $elem_editMode;

			//UT Elems //($elem_editMode==1) ? : "";
			$ar_ret["elem_utElemsBV"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;

			$blood_vessels_od= stripslashes($blood_vessels_od);
			$blood_vessels_os= stripslashes($blood_vessels_os);

			$arr_vals_blood_vessels_od = $oExamXml->extractXmlValue($blood_vessels_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_blood_vessels_od);
			//extract($arr_vals_blood_vessels_od);
			$arr_vals_blood_vessels_os = $oExamXml->extractXmlValue($blood_vessels_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_blood_vessels_os);
			//extract($arr_vals_blood_vessels_os);

			$ar_ret["elem_wnlBV"] = $wnlBV;
			$ar_ret["elem_posBV"] = $posBV;
			$ar_ret["elem_ncBV"] = ($elem_editMode==1) ? $ncBV : 0 ;
			$ar_ret["elem_wnlBVOd"]= $wnlBVOd;
			$ar_ret["elem_wnlBVOs"]= $wnlBVOs;

			$ar_ret["blood_vessels_od_summary"] = stripslashes($blood_vessels_od_summary);
			$ar_ret["blood_vessels_os_summary"] = stripslashes($blood_vessels_os_summary);

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
		$tmpd = "elem_chng_div4_Od";
		$tmps = "elem_chng_div4_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = ($$tmpd == "1") ? "1" : "0";
		$arrSe[$tmps] = ($$tmps == "1") ? "1" : "0";
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);

		//BV -----------
		$wnlBVOd = $wnlBVOs = $wnlBV = $posBV = $ncBV = "0";
		//if(!empty($elem_chng_div4_Od) || !empty($elem_chng_div4_Os)){
		//	if(!empty($elem_chng_div4_Od)){
				$menuName = "BloodVesselsOd";
				$menuFilePath = $arXmlFiles["bloodVessels"]["od"]; //dirname(__FILE__)."/xml/bloodVessels_od.xml";
				$elem_bloodVessels_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlBVOd = $_POST["elem_wnlBVOd"];
		//	}

		//	if(!empty($elem_chng_div4_Os)){
				$menuName = "BloodVesselsOs";
				$menuFilePath = $arXmlFiles["bloodVessels"]["os"]; //dirname(__FILE__)."/xml/bloodVessels_os.xml";
				$elem_bloodVessels_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlBVOs = $_POST["elem_wnlBVOs"];
		//	}

			$wnlBV = (!empty($wnlBVOd) && !empty($wnlBVOs)) ? "1" : "0"; //$_POST["elem_wnlBV"];
			$posBV = $_POST["elem_posBV"];
			$ncBV = $_POST["elem_ncBV"];
		//}
		//BV -----------

		$examDate = wv_dt("now"); //$_POST["elem_examDate"];
		$oUserAp = new UserAp();

		// Summary --
		$strExamsAllOd = $strExamsAllOs = "";
		$elem_blood_vessels_od_summary = $elem_blood_vessels_os_summary = "";

		$blood_vessels_od = $elem_bloodVessels_od;

		$arrTemp = $this->getExamSummary($blood_vessels_od);
		$elem_blood_vessels_od_summary = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div4_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Vessels",$arrExmDone_od,$elem_blood_vessels_od_summary);
		}
		$blood_vessels_os = $elem_bloodVessels_os;

		$arrTemp = $this->getExamSummary($blood_vessels_os);
		$elem_blood_vessels_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div4_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Vessels",$arrExmDone_os,$elem_blood_vessels_os_summary);
		}

		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsBV"];
		$elem_utElems_cur = $_POST["elem_utElemsBV_cur"];
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
			$elem_bloodVessels_od = sqlEscStr($elem_bloodVessels_od);
			$elem_bloodVessels_os = sqlEscStr($elem_bloodVessels_os);

			//check
			$cQry = "select
						id, last_opr_id, uid,
						blood_vessels_od_summary, blood_vessels_os_summary, wnlBVOd, wnlBVOs, wnl_value_BV,
						exam_date
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$elem_editMode = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$bvId=$bvIDExam = $row["id"];
				$elem_editMode = "1";
				//Modifying Notes----------------
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				$seri_modi_note_bvArr = $owv->getModiNotesArr($row["blood_vessels_od_summary"],$elem_blood_vessels_od_summary,$last_opr_id,'OD',$row["modi_note_bvArr"],$row['exam_date']);
				$seri_modi_note_bvArr = $owv->getModiNotesArr($row["blood_vessels_os_summary"],$elem_blood_vessels_os_summary,$last_opr_id,'OS',$seri_modi_note_bvArr,$row['exam_date']);
				//Modifying Notes----------------
			}

			//
			$sql_con = "
				blood_vessels_od = '".$elem_bloodVessels_od."',
				blood_vessels_os = '".$elem_bloodVessels_os."',
				blood_vessels_od_summary = '".$elem_blood_vessels_od_summary."',
				blood_vessels_os_summary = '".$elem_blood_vessels_os_summary."',
				wnlBV='".$wnlBV."',
				posBV='".$posBV."',
				ncBV='".$ncBV."',
				wnlBVOd='".$wnlBVOd."',
				wnlBVOs='".$wnlBVOs."',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_bvArr = '".sqlEscStr($seri_modi_note_bvArr)."'
			";

			//
			if($elem_editMode == "0"){
				//WNL
				$wnl_value_BV = $this->getExamWnlStr("Blood Vessels");
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',
					wnl_value_BV='".sqlEscStr($wnl_value_BV)."',
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
				$insertId = $bvId;
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
		$elem_bv_version="new";

		$sql ="SELECT ".
			//c9-chart_rv-------
			"c9.id, ".
			"c9.wnl_value_BV, ".
			//Old Data --
			"c9.wnlBV, ".
			"c9.posBV, ".
			"c9.ncBV, ".
			"c9.blood_vessels_od_summary, ".
			"c9.blood_vessels_os_summary, ".
			"c9.wnlBVOd, ".
			"c9.wnlBVOs, ".
			//Old Data --
			"c9.exam_date AS exam_date_bv, c9.modi_note_bvArr, ".
			"c9.statusElem AS se_bv ".
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
			$elem_se_bv = $row["se_bv"];

			$elem_bv_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date_bv"]);

			//wnl
			$elem_wnl_value_BV = $row["wnl_value_BV"];

			/*if( !empty($row["blood_vessels_od_summary"]) ||
				 !empty($row["blood_vessels_os_summary"]) ||
				 !empty($row["wnlBVOd"]) ||
				 !empty($row["wnlBVOs"]) ||
				 !empty($row["posBV"]) ){*/

				//Old
				//$elem_bv_version="old";
				$elem_wnlBV = assignZero($row["wnlBV"]);
				$elem_posBV = assignZero($row["posBV"]);
				$elem_ncBV = assignZero($row["ncBV"]);
				$elem_wnlBVOd = assignZero($row["wnlBVOd"]);
				$elem_wnlBVOs = assignZero($row["wnlBVOs"]);
				$elem_blood_vessels_od_summary = $row["blood_vessels_od_summary"];
				$elem_blood_vessels_os_summary = $row["blood_vessels_os_summary"];

				$modi_note_bvArr  = unserialize($row["modi_note_bvArr"]);

				$arrHx = array();
				if(is_array($modi_note_bvArr) && count($modi_note_bvArr)>0 && $row["modi_note_bvArr"]!='')
				$arrHx['Vessels'] = $modi_note_bvArr;

			//}
		}

		if(empty($elem_bv_id)){
			$tmp = "
				c2.wnlBV, c2.posBV, c2.ncBV,
				c2.blood_vessels_od_summary,
				c2.blood_vessels_os_summary,
				c2.id, c2.wnlBVOd, c2.wnl_value_BV, c2.wnlBVOs,
				c2.exam_date AS exam_date_RV,
				c2.statusElem AS se_rv
			";
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordR_v($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{

				$elem_blood_vessels_od_summary = $row["blood_vessels_od_summary"];
				$elem_blood_vessels_os_summary = $row["blood_vessels_os_summary"];
				$elem_wnlBV = assignZero($row["wnlBV"]);
				$elem_wnlBVOd = assignZero($row["wnlBVOd"]);
				$elem_wnlBVOs = assignZero($row["wnlBVOs"]);
				$elem_posBV = assignZero($row["posBV"]);
				$elem_ncBV = assignZero($row["ncBV"]);
				$examdate = wv_formatDate($row["exam_date_RV"]);
				//wnl
				$elem_wnl_value_BV = $row["wnl_value_BV"];
				$elem_se_bv_prev = $row["se_rv"];
			}

			//BG
			$bgColor_RV = "bgSmoke";
		}

		//is Change is made in new chart -----
		$flgSe_BV_Od = $flgSe_BV_Os = "0";
		if(!isset($bgColor_RV)){
			if(!empty($elem_se_bv)){
				$tmpArrSe = $this->se_elemStatus("RV","0",$elem_se_bv,0,0,1,$elem_bv_version); //working19
				$flgSe_BV_Od = $tmpArrSe["4"]["od"];
				$flgSe_BV_Os = $tmpArrSe["4"]["os"];
			}
		}else{
			if(!empty($elem_se_bv_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("RV","0",$elem_se_bv_prev,0,0,1,$elem_bv_version); //working19
				$flgSe_BV_Od_prev = $tmpArrSe_prev["4"]["od"];
				$flgSe_BV_Os_prev = $tmpArrSe_prev["4"]["os"];
			}
		}

		//Blood Vessels --
		$wnlString_bv = !empty($elem_wnl_value_BV) ? $elem_wnl_value_BV : $this->getExamWnlStr("Blood Vessels");
		$wnlStringOd_bv = $wnlStringOs_bv = $wnlString_bv;

		if(empty($flgSe_BV_Od) && empty($flgSe_BV_Od_prev) && !empty($elem_wnlBVOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Blood Vessels", "OD"); if(!empty($tmp)){ $wnlStringOd_bv = $tmp;}  }
		if(empty($flgSe_BV_Os) && empty($flgSe_BV_Os_prev) && !empty($elem_wnlBVOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Blood Vessels", "OS"); if(!empty($tmp)){ $wnlStringOs_bv = $tmp;}  }

		list($elem_blood_vessels_od_summary,$elem_blood_vessels_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_bv,"wValOs"=>$wnlStringOs_bv,
																	"wOd"=>$elem_wnlBVOd,"sOd"=>$elem_blood_vessels_od_summary,
																	"wOs"=>$elem_wnlBVOs,"sOs"=>$elem_blood_vessels_os_summary));

		//Nochanged
		if(!empty($elem_se_Rv)&&strpos($elem_se_Rv,"=1")!==false){
			$elem_ncBV=1;
		}

		$arrDivArcCmn=array();

		if($bgColor_RV != "bgSmoke"){
			$oChartRecArc->setChkTbl("".$this->tbl);
			$arrInpArc = array(
								"elem_sumOdBV"=>array("blood_vessels_od_summary",$elem_blood_vessels_od_summary,"smof","wnlBVOd",$wnlString_bv,$modi_note_BVOd),
								"elem_sumOsBV"=>array("blood_vessels_os_summary",$elem_blood_vessels_os_summary,"smof","wnlBVOs",$wnlString_bv,$modi_note_BVOs)
								//old
								);

			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);

			//BV --
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdBV"])){
				//echo $arTmpRecArc["div"]["elem_sumOdBV"];
				$arrDivArcCmn["Blood Vessels"]["OD"]=$arTmpRecArc["div"]["elem_sumOdBV"];
				$moeArc["od"]["BV"] = $arTmpRecArc["js"]["elem_sumOdBV"];
				$flgArcColor["od"]["BV"] = $arTmpRecArc["css"]["elem_sumOdBV"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdBV"]))
					$elem_blood_vessels_od_summary = $arTmpRecArc["curText"]["elem_sumOdBV"];
			}
			//OS
			if(!empty($arTmpRecArc["div"]["elem_sumOsBV"])){
				//echo $arTmpRecArc["div"]["elem_sumOsBV"];
				$arrDivArcCmn["Blood Vessels"]["OS"]=$arTmpRecArc["div"]["elem_sumOsBV"];
				$moeArc["os"]["BV"] = $arTmpRecArc["js"]["elem_sumOsBV"];
				$flgArcColor["os"]["BV"] = $arTmpRecArc["css"]["elem_sumOsBV"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsBV"]))
					$elem_blood_vessels_os_summary = $arTmpRecArc["curText"]["elem_sumOsBV"];
			}
			//BV --

		}


		$arr=array();
		if(in_array("Blood Vessels",$arrTempProc) || in_array("All",$arrTempProc)){
		//Blood Vessels
		$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Vessels",
											"sOd"=>$elem_blood_vessels_od_summary,"sOs"=>$elem_blood_vessels_os_summary,
											"fOd"=>$flgSe_BV_Od,"fOs"=>$flgSe_BV_Os,"pos"=>$elem_posBV,
											//"arcJsOd"=>$moeArc["od"]["BV"],"arcJsOs"=>$moeArc["os"]["BV"],
											"arcCssOd"=>$flgArcColor["od"]["BV"],"arcCssOs"=>$flgArcColor["os"]["BV"],
											//"mnOd"=>$moeMN["od"]["BV"],"mnOs"=>$moeMN["os"]["BV"],
											"enm_2"=>"BV"));
		}

		//Sub Exam List
		$arr["seList"] = 	array();
		$arr["seList"]["BV"]=array("enm"=>"Vessels","pos"=>$elem_posBV,
							"wOd"=>$elem_wnlBVOd,"wOs"=>$elem_wnlBVOs);

		$arr["bgColor"] = "".$bgColor_RV;
		$arr["nochange"] = $elem_ncBV;
		$arr["examdate"] = $examdate;
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_BV_Od,$flgSe_BV_Os);
		$arr["elem_bv_version"]=$elem_bv_version;
		//$arr["arrDivArcCmn"] = $arrDivArcCmn;
		$arr["arrHx"] = $arrHx;



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
			"c9.wnlBV,".
			"c9.posBV,".
			"c9.ncBV,".
			"c9.blood_vessels_od_summary, c9.blood_vessels_os_summary, ".
			"c9.id, ".
			"c9.wnlBVOd, c9.wnlBVOs, ".
			"c9.wnl_value_BV, ".
			"c9.exam_date AS exam_date_RV, ".
			"c9.statusElem AS se_bv, c9.purgerId, c9.purgeTime, c9.purgerId AS purgerId_rv, c9.purgeTime AS purgeTime_rv ".
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
			$elem_wnlBV = assignZero($row["wnlBV"]);
			$elem_posBV = assignZero($row["posBV"]);
			$elem_ncBV = assignZero($row["ncBV"]);
			$elem_wnlBVOd = assignZero($row["wnlBVOd"]);
			$elem_wnlBVOs = assignZero($row["wnlBVOs"]);
			$elem_se_bv = $row["se_bv"];
			$elem_blood_vessels_od_summary = $row["blood_vessels_od_summary"];
			$elem_blood_vessels_os_summary = $row["blood_vessels_os_summary"];
			$elem_bv_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			$modi_note_bvArr = unserialize($row["modi_note_bvArr"]);
				$arrHx = array();
				if(count($modi_note_bvArr)>0 && $row["modi_note_bvArr"]!='')
				$arrHx['BV']	= $modi_note_bvArr;

			//wnl
			$elem_wnl_value_BV = $row["wnl_value_BV"];

			//is Change is made in new chart -----
			$flgSe_BV_Od = $flgSe_BV_Os = "0";
			if(!empty($elem_se_bv)){
				$tmpArrSe = $this->se_elemStatus("RV","0",$elem_se_bv,0,0,1,$elem_retina_version); //working19
				$flgSe_BV_Od = $tmpArrSe["4"]["od"];
				$flgSe_BV_Os = $tmpArrSe["4"]["os"];
			}

			//BV --
			$wnlString_BV = !empty($elem_wnl_value_BV) ? $elem_wnl_value_BV : $this->getExamWnlStr("Blood Vessels"); //"Clear"
			$wnlStringOd_BV = $wnlStringOs_BV = $wnlString_BV;

			if(empty($flgSe_BV_Od) && empty($flgSe_BV_Od_prev) && !empty($elem_wnlBVOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Blood Vessels", "OD"); if(!empty($tmp)){ $wnlStringOd_BV = $tmp;}  }
			if(empty($flgSe_BV_Os) && empty($flgSe_BV_Os_prev) && !empty($elem_wnlBVOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Blood Vessels", "OS"); if(!empty($tmp)){ $wnlStringOs_BV = $tmp;}  }

			list($elem_blood_vessels_od_summary,$elem_blood_vessels_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_BV,"wValOs"=>$wnlStringOs_BV,
																		"wOd"=>$elem_wnlBVOd,"sOd"=>$elem_blood_vessels_od_summary,
																		"wOs"=>$elem_wnlBVOs,"sOs"=>$elem_blood_vessels_os_summary));
			//Nochanged
			if(!empty($elem_se_bv)&&strpos($elem_se_bv,"=1")!==false){
				$elem_noChangeBV=1;
			}

			$arr=array();

			if(in_array("Blood Vessels",$arrTempProc) || in_array("All",$arrTempProc)){
			//BV
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Vessels",
												"sOd"=>$elem_blood_vessels_od_summary,"sOs"=>$elem_blood_vessels_os_summary,
												"fOd"=>$flgSe_BV_Od,"fOs"=>$flgSe_BV_Os,"pos"=>$elem_posBV,
												"arcCssOd"=>$flgArcColor["os"]["BV"],"arcCssOs"=>$flgArcColor["os"]["BV"],
												"enm_2"=>"BV"));

			}

			//Sub Exam List
			$arr["seList"] = 	array("BV"=>array("enm"=>"Vessels","pos"=>$elem_posBV,
								"wOd"=>$elem_wnlBVOd,"wOs"=>$elem_wnlBVOs));
			$arr["bgColor"] = "";
			$arr["nochange"] = $elem_ncBV;
			$arr["examdate"] = $examdate;
			$arr["moeMN"] = $moeMN;
			$arr["exm_flg_se"] = array($flgSe_BV_Od,$flgSe_BV_Os);

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
		$wnl_value_BV = $this->getExamWnlStr("Blood Vessels");

		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_BV)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_BV)."') ";
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
					"ncBV,".
					"ncBV_od,".
					"ncBV_os,".
					"wnl_value_BV";
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
			$wnl_value_BV="";
			$wnl_value_BV_phrase="";
			$flgCarry=0;

			//Fundus
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}

			$cQry = "select
					wnlBV, ".
					"wnlBVOd,wnlBVOs,
					".
					"posBV, ".
					"blood_vessels_od_summary,blood_vessels_os_summary,
					".
					"statusElem AS statusElem_fundus, uid, wnl_value_BV,
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
			$elem_wnlBVOd = $wnlBVOd;
			$elem_wnlBVOs = $wnlBVOs;
			$elem_wnlBV = $wnlBV;

			$oWv = new WorkView();

			if(in_array("Blood Vessels",$arrTempProc)||in_array("All",$arrTempProc)){
				if((!empty($statusElem_fundus)&&strpos($statusElem_fundus,"0")===false)||
					(empty($blood_vessels_od_summary) && empty($elem_wnlBVOd))||
					(empty($blood_vessels_os_summary) && empty($elem_wnlBVOs))
					 ){
				//Toggle BV
				list($elem_wnlBVOd,$elem_wnlBVOs,$elem_wnlBV) =
										$oWv->toggleWNL($posBV,$blood_vessels_od_summary,$blood_vessels_os_summary,
														$elem_wnlBVOd,$elem_wnlBVOs,$elem_wnlBV,$exmEye);
				}
			}

			//Status
			$statusElem_fundus_prev=$statusElem_fundus;
			$statusElem_fundus = $this->setEyeStatus($w, $exmEye,$statusElem_fundus,0,$elem_retina_version);

			if(empty($wnl_value_BV)){
				$wnl_value_BV=$this->getExamWnlStr("Blood Vessels");
				$wnl_value_BV_phrase = ", wnl_value_BV='".sqlEscStr($wnl_value_BV)."' ";
			}

			//Fundus
			$sql = "UPDATE ".$this->tbl." SET
				  wnlBV='".$elem_wnlBV."', ".
				  "wnlBVOd='".$elem_wnlBVOd."',
				  wnlBVOs='".$elem_wnlBVOs."',
				  ".
				  " exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				  statusElem='".$statusElem_fundus."'
				  ".
				  "
				  ";
			$sql .= " ".$wnl_value_BV_phrase." ";

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
					$ignoreFlds .= "blood_vessels_od_summary,
								blood_vessels_od,
								wnlBVOd,
								ncBV_od,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }
				}else if($_POST["site"]=="OD"){
					$ignoreFlds .= "blood_vessels_os, blood_vessels_os_summary,
								wnlBVOs,
								ncBV_os,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posBV,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_BV, ut_elem,";}
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
						"ncBV,".
						"ncBV_od,".
						"ncBV_os,".
						"wnl_value_BV";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values
			}
		}
	}

	function isNoChanged(){
		$res= $this->getRecord("ncBV,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncBV"]) ){
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
		$elem_ncBV="0";
		$elem_ncBV_od="0";
		$elem_ncBV_os="0";

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
				$elem_ncBV="1";
				$elem_ncBV_od="1";
				$elem_ncBV_os="1";
			}else if($exmEye=="OD"){
				$elem_ncBV_od="1";
			}else if($exmEye=="OS"){
				$elem_ncBV_os="1";
			}
		}
		// ---

		//Get status string --
		$statusElem="";
		if($elem_ncBV_od==1||$elem_ncBV_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncBV_od,$elem_ncBV_os,0);}
		//Get status--

		//
		$sql = "UPDATE ".$this->tbl."
			  SET
			  ncBV = '".$elem_ncBV."',
			  ncBV_od='".$elem_ncBV_od."', ncBV_os='".$elem_ncBV_os."',
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}

	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "blood_vessels_od";
		$arr["db"]["xmlOs"] = "blood_vessels_os";
		$arr["db"]["wnlSE"] = "wnlBV";
		$arr["db"]["wnlOd"] = "wnlBVOd";
		$arr["db"]["wnlOs"] = "wnlBVOs";
		$arr["db"]["posSE"] = "posBV";
		$arr["db"]["summOd"] = "blood_vessels_od_summary";
		$arr["db"]["summOs"] = "blood_vessels_os_summary";
		$arr["divSe"] = "4";
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
