<?php

class Macula extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_macula";
		$this->examName="Macula";
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
				$ar_ret["elem_examDateMacula"]=$exam_date;
			}
			$ar_ret["elem_maculaId_LF"] = $ar_ret["elem_maculaId"]=$rv_id;
			$ar_ret["elem_editModeMacula"] = $elem_editMode;

			$ar_ret["elem_statusElementsMacula"] = ($elem_editMode==0) ? "" : $statusElem;

			//UT Elems //($elem_editMode==1) ? : "";
			$ar_ret["elem_utElemsMacula"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;

			$macula_od= stripslashes($macula_od);
			$macula_os= stripslashes($macula_os);

			$arr_vals_macula_od = $oExamXml->extractXmlValue($macula_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_macula_od);
			//extract($arr_vals_macula_od);
			$arr_vals_macula_os = $oExamXml->extractXmlValue($macula_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_macula_os);
			//extract($arr_vals_macula_os);

			$ar_ret["elem_wnlMacula"] = $wnlMacula;
			$ar_ret["elem_posMacula"] = $posMacula;
			$ar_ret["elem_ncMacula"] = ($elem_editMode==1) ? $ncMacula : 0 ;
			$ar_ret["elem_wnlMaculaOd"]= $wnlMaculaOd;
			$ar_ret["elem_wnlMaculaOs"]= $wnlMaculaOs;

			$ar_ret["macula_od_summary"] = stripslashes($macula_od_summary);
			$ar_ret["macula_os_summary"] = stripslashes($macula_os_summary);

			//Add old summary into comments
			if(!empty($exam_date) && $exam_date<'2020-03-01 00:00:00'){
				if(!empty($macula_od_summary)){
					$tmp = trim($ar_ret["elem_retinalAdOptionsOd"]);
					if(!empty($tmp)){ $tmp .="\n"; }
					$tmp .= $macula_od_summary;
					$ar_ret["elem_maculaAdOptionsOd"] = $tmp;
				}
				if(!empty($macula_os_summary)){
					$tmp = trim($ar_ret["elem_maculaAdOptionsOs"]);
					if(!empty($tmp)){ $tmp .="\n"; }
					$tmp .= $macula_os_summary;
					$ar_ret["elem_maculaAdOptionsOs"] = $tmp;
				}
			}
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
		$tmpd = "elem_chng_div2_Od";
		$tmps = "elem_chng_div2_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = ($$tmpd == "1") ? "1" : "0";
		$arrSe[$tmps] = ($$tmps == "1") ? "1" : "0";
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);

		//Macula -----------
		$wnlMaculaOd = $wnlMaculaOs = $wnlMacula = $posMacula = $ncMacula = "0";
		//if(!empty($elem_chng_div2_Od) || !empty($elem_chng_div2_Os)){
		//	if(!empty($elem_chng_div2_Od)){
				$menuName = "MaculaOd";
				$menuFilePath = $arXmlFiles["macula"]["od"]; //dirname(__FILE__)."/xml/macula_od.xml";
				$elem_macula_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlMaculaOd = $_POST["elem_wnlMaculaOd"];
		//	}

		//	if(!empty($elem_chng_div2_Os)){
				$menuName = "MaculaOs";
				$menuFilePath = $arXmlFiles["macula"]["os"]; //dirname(__FILE__)."/xml/macula_os.xml";
				$elem_macula_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlMaculaOs = $_POST["elem_wnlMaculaOs"];
		//	}

			$wnlMacula = (!empty($wnlMaculaOd) && !empty($wnlMaculaOs)) ? "1" : "0";// $_POST["elem_wnlMacula"];
			$posMacula = $_POST["elem_posMacula"];
			$ncMacula = $_POST["elem_ncMacula"];
		//}
		//Macula -----------

		$examDate = wv_dt("now"); //$_POST["elem_examDate"];
		$oUserAp = new UserAp();

		// Summary --
		$strExamsAllOd = $strExamsAllOs = "";
		$elem_macula_od_summary = $elem_macula_os_summary = "";

		$macula_od = $elem_macula_od;

		$arrTemp = $this->getExamSummary($macula_od);
		$elem_macula_od_summary = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div2_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Macula",$arrExmDone_od,$elem_macula_od_summary);
		}

		$macula_os = $elem_macula_os;

		$arrTemp = $this->getExamSummary($macula_os);
		$elem_macula_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div2_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Macula",$arrExmDone_os,$elem_macula_os_summary);
		}

		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsMacula"];
		$elem_utElems_cur = $_POST["elem_utElemsMacula_cur"];
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
			$elem_macula_od = sqlEscStr($elem_macula_od);
			$elem_macula_os = sqlEscStr($elem_macula_os);

			//check
			$cQry = "select
						id, last_opr_id, uid,
						macula_od_summary, macula_os_summary, wnlMaculaOd, wnlMaculaOs, wnl_value_Macula,exam_date

					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$elem_editMode = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$maculaId=$maculaIDExam = $row["id"];
				$elem_editMode = "1";
				//Modifying Notes----------------
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				$seri_modi_note_maculaArr = $owv->getModiNotesArr($row["macula_od_summary"],$elem_macula_od_summary,$last_opr_id,'OD',$row["modi_note_maculaArr"],$row['exam_date']);
				$seri_modi_note_maculaArr = $owv->getModiNotesArr($row["macula_os_summary"],$elem_macula_os_summary,$last_opr_id,'OS',$seri_modi_note_maculaArr,$row['exam_date']);
				//Modifying Notes----------------
			}

			//
			$sql_con = "
				macula_od = '".$elem_macula_od."',
				macula_os = '".$elem_macula_os."',
				macula_od_summary = '".$elem_macula_od_summary."',
				macula_os_summary = '".$elem_macula_os_summary."',
				wnlMacula='".$wnlMacula."',
				posMacula='".$posMacula."',
				ncMacula='".$ncMacula."',
				wnlMaculaOd='".$wnlMaculaOd."',
				wnlMaculaOs='".$wnlMaculaOs."',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_maculaArr = '".sqlEscStr($seri_modi_note_maculaArr)."'
			";

			//
			if($elem_editMode == "0"){
				//WNL
				$wnl_value_Macula = $this->getExamWnlStr("Macula");
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',
					wnl_value_Macula='".sqlEscStr($wnl_value_Macula)."',
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
				$insertId = $maculaId;
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
			"c9.id, ".
			"c9.wnl_value_Macula, ".
			//Old Data --
			"c9.wnlMacula, ".
			"c9.posMacula, ".
			"c9.ncMacula, ".
			"c9.macula_od_summary, ".
			"c9.macula_os_summary, ".
			"c9.wnlMaculaOd, ".
			"c9.wnlMaculaOs, ".
			//Old Data --
			"c9.exam_date, c9.modi_note_maculaArr, ".
			"c9.statusElem ".
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

			$elem_se_macula = $row["statusElem"];
			$elem_macula_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			//wnl
			$elem_wnl_value_Macula = $row["wnl_value_Macula"];
			/*if(!empty($row["macula_od_summary"]) ||
				!empty($row["macula_os_summary"]) ||
				!empty($row["wnlMaculaOd"]) ||
				!empty($row["wnlMaculaOs"]) ||
				!empty($row["posMacula"]) ){*/

				//Old
				//$elem_macula_version="old";
				$elem_wnlMacula = assignZero($row["wnlMacula"]);
				$elem_posMacula = assignZero($row["posMacula"]);
				$elem_ncMacula = assignZero($row["ncMacula"]);
				$elem_wnlMaculaOd = assignZero($row["wnlMaculaOd"]);
				$elem_wnlMaculaOs = assignZero($row["wnlMaculaOs"]);
				$elem_macula_od_summary = $row["macula_od_summary"];
				$elem_macula_os_summary = $row["macula_os_summary"];
				$modi_note_MaculaOd=$row["modi_note_MaculaOd"];
				$modi_note_MaculaOs=$row["modi_note_MaculaOs"];
			//}

				$modi_note_MaculaArr  = unserialize($row["modi_note_maculaArr"]);

				$arrHx = array();
				if(is_array($modi_note_MaculaArr) && count($modi_note_MaculaArr)>0 && $row["modi_note_MaculaArr"]!='')
				$arrHx['Macula'] = $modi_note_MaculaArr;

		}

		if(empty($elem_macula_id)){
			$tmp = "";

			$tmp .= "c2.wnlMacula,c2.posMacula,c2.ncMacula,c2.macula_od_summary,c2.macula_os_summary,
					c2.id,	c2.wnlMaculaOd, c2.wnl_value_Macula, c2.wnlMaculaOs, c2.exam_date,
					c2.statusElem as se_mac
					";
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordR_v($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{

				$elem_macula_od_summary = $row["macula_od_summary"];
				$elem_macula_os_summary = $row["macula_os_summary"];
				$elem_wnlMacula = assignZero($row["wnlMacula"]);
				$elem_wnlMaculaOd = assignZero($row["wnlMaculaOd"]);
				$elem_wnlMaculaOs = assignZero($row["wnlMaculaOs"]);
				$elem_posMacula = assignZero($row["posMacula"]);
				$elem_ncMacula = assignZero($row["ncMacula"]);
				$examdate = wv_formatDate($row["exam_date"]);
				//wnl
				$elem_wnl_value_Macula = $row["wnl_value_Macula"];
				$elem_se_mac_prev = $row["se_mac"];
			}

			//BG
			$bgColor_macula = "bgSmoke";
		}
		//is Change is made in new chart -----
		$flgSe_Macula_Od = $flgSe_Macula_Os = "0";
		if(!isset($bgColor_macula)){
			if(!empty($elem_se_macula)){
				$tmpArrSe = $this->se_elemStatus("Macula","0",$elem_se_macula,0,0,1,$elem_retina_version);
				$flgSe_Macula_Od = $tmpArrSe["2"]["od"];
				$flgSe_Macula_Os = $tmpArrSe["2"]["os"];
			}
		}else{
			if(!empty($elem_se_macula_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("Macula","0",$elem_se_macula_prev,0,0,1,$elem_retina_version);
				$flgSe_Macula_Od_prev = $tmpArrSe_prev["2"]["od"];
				$flgSe_Macula_Os_prev = $tmpArrSe_prev["2"]["os"];
			}
		}

		//Macula --
		$wnlString_macula = !empty($elem_wnl_value_Macula) ? $elem_wnl_value_Macula : $this->getExamWnlStr("Macula");
		$wnlStringOd_macula = $wnlStringOs_macula = $wnlString_macula;

		if(empty($flgSe_Macula_Od) && empty($flgSe_Macula_Od_prev) && !empty($elem_wnlMaculaOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Macula", "OD"); if(!empty($tmp)){ $wnlStringOd_macula = $tmp;}  }
		if(empty($flgSe_Macula_Os) && empty($flgSe_Macula_Os_prev) && !empty($elem_wnlMaculaOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Macula", "OS"); if(!empty($tmp)){ $wnlStringOs_macula = $tmp;}  }

		list($elem_macula_od_summary,$elem_macula_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_macula,"wValOs"=>$wnlStringOs_macula,
																	"wOd"=>$elem_wnlMaculaOd,"sOd"=>$elem_macula_od_summary,
																	"wOs"=>$elem_wnlMaculaOs,"sOs"=>$elem_macula_os_summary));

		//Nochanged
		if(!empty($elem_se_macula)&&strpos($elem_se_macula,"=1")!==false){
			$elem_ncMacula=1;
		}

		$arrDivArcCmn=array();

		if($bgColor_macula != "bgSmoke"){
			$oChartRecArc->setChkTbl("".$this->tbl);
			$arrInpArc = array(	//old
							"elem_sumOdMacula"=>array("macula_od_summary",$elem_macula_od_summary,"smof","wnlMaculaOd",$wnlString_macula,$modi_note_MaculaOd),
							"elem_sumOsMacula"=>array("macula_os_summary",$elem_macula_os_summary,"smof","wnlMaculaOs",$wnlString_macula,$modi_note_MaculaOs)
							);

			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);

			//Macula --
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdMacula"])){
				//echo $arTmpRecArc["div"]["elem_sumOdMacula"];
				$arrDivArcCmn["Macula"]["OD"]=$arTmpRecArc["div"]["elem_sumOdMacula"];
				$moeArc["od"]["Macula"] = $arTmpRecArc["js"]["elem_sumOdMacula"];
				$flgArcColor["od"]["Macula"] = $arTmpRecArc["css"]["elem_sumOdMacula"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdMacula"]))
					$elem_macula_od_summary = $arTmpRecArc["curText"]["elem_sumOdMacula"];
			}
			//OS
			if(!empty($arTmpRecArc["div"]["elem_sumOsMacula"])){
				//echo $arTmpRecArc["div"]["elem_sumOsMacula"];
				$arrDivArcCmn["Macula"]["OS"]=$arTmpRecArc["div"]["elem_sumOsMacula"];
				$moeArc["os"]["Macula"] = $arTmpRecArc["js"]["elem_sumOsMacula"];
				$flgArcColor["os"]["Macula"] = $arTmpRecArc["css"]["elem_sumOsMacula"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsMacula"]))
					$elem_macula_os_summary = $arTmpRecArc["curText"]["elem_sumOsMacula"];
			}
			//Macula --
		}

		$arr=array();
		if(in_array("Macula",$arrTempProc) ||  in_array("All",$arrTempProc)){
		//Macula
		$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Macula",
											"sOd"=>$elem_macula_od_summary ,"sOs"=>$elem_macula_os_summary,
											"fOd"=>$flgSe_Macula_Od,"fOs"=>$flgSe_Macula_Os,"pos"=>$elem_posMacula,
											//"arcJsOd"=>$moeArc["od"]["Macula"],"arcJsOs"=>$moeArc["os"]["Macula"],
											"arcCssOd"=>$flgArcColor["od"]["Macula"],"arcCssOs"=>$flgArcColor["os"]["Macula"],
											//"mnOd"=>$moeMN["od"]["Macula"],"mnOs"=>$moeMN["os"]["Macula"],
											"enm_2"=>"Mac"));

		}

		//Sub Exam List
		$arr["seList"] = array();
		$arr["seList"]["Mac"]=array("enm"=>"Macula","pos"=>$elem_posMacula,
							"wOd"=>$elem_wnlMaculaOd,"wOs"=>$elem_wnlMaculaOs);
		$arr["bgColor"] = "".$bgColor_macula;
		$arr["nochange"] = $elem_ncMacula;
		$arr["examdate"] = $examdate;
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_Macula_Od,$flgSe_Macula_Os);
		$arr["elem_macula_version"] = $elem_macula_version;
		//$arr["arrDivArcCmn"] = $arrDivArcCmn;
		$arr["arrHx"] = $arrHx;

		$arr["elem_posMacula"] = $elem_posMacula;
		$arr["elem_wnlMacula"] = $elem_wnlMacula;
		$arr["elem_macula_od_summary"] = $elem_macula_od_summary;
		$arr["elem_macula_os_summary"] = $elem_macula_os_summary;
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
			"c9.wnlMacula,".
			"c9.posMacula,".
			"c9.ncMacula,".
			"c9.macula_od_summary, c9.macula_os_summary, ".
			"c9.id, ".
			"c9.wnlMaculaOd, c9.wnlMaculaOs, ".
			"c9.wnl_value_Macula, ".
			"c9.exam_date AS exam_date_RV, ".
			"c9.statusElem AS se_mac, c9.purgerId, c9.purgeTime, c9.purgerId AS purgerId_rv, c9.purgeTime AS purgeTime_rv ".
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
			$elem_wnlMacula = assignZero($row["wnlMacula"]);
			$elem_posMacula = assignZero($row["posMacula"]);
			$elem_ncMacula = assignZero($row["ncMacula"]);
			$elem_wnlMaculaOd = assignZero($row["wnlMaculaOd"]);
			$elem_wnlMaculaOs = assignZero($row["wnlMaculaOs"]);
			$elem_se_mac = $row["se_mac"];
			$elem_macula_od_summary = $row["macula_od_summary"];
			$elem_macula_os_summary = $row["macula_os_summary"];
			$elem_mac_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			$modi_note_maculaArr = unserialize($row["modi_note_maculaArr"]);
				$arrHx = array();
				if(count($modi_note_maculaArr)>0 && $row["modi_note_maculaArr"]!='')
				$arrHx['Macula']	= $modi_note_maculaArr;

			//wnl
			$elem_wnl_value_Macula = $row["wnl_value_Macula"];

			//is Change is made in new chart -----
			$flgSe_Macula_Od = $flgSe_Macula_Os = "0";
			if(!empty($elem_se_mac)){
				$tmpArrSe = $this->se_elemStatus("RV","0",$elem_se_mac,0,0,1,$elem_retina_version); //working19
				$flgSe_Macula_Od = $tmpArrSe["2"]["od"];
				$flgSe_Macula_Os = $tmpArrSe["2"]["os"];
			}

			//Macula --
			$wnlString_Macula = !empty($elem_wnl_value_Macula) ? $elem_wnl_value_Macula : $this->getExamWnlStr("Macula"); //"Clear"
			$wnlStringOd_Macula = $wnlStringOs_Macula = $wnlString_Macula;

			if(empty($flgSe_Macula_Od) && empty($flgSe_Macula_Od_prev) && !empty($elem_wnlMaculaOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Macula", "OD"); if(!empty($tmp)){ $wnlStringOd_Macula = $tmp;}  }
			if(empty($flgSe_Macula_Os) && empty($flgSe_Macula_Os_prev) && !empty($elem_wnlMaculaOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Macula", "OS"); if(!empty($tmp)){ $wnlStringOs_Macula = $tmp;}  }

			list($elem_macula_od_summary,$elem_macula_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Macula,"wValOs"=>$wnlStringOs_Macula,
																		"wOd"=>$elem_wnlMaculaOd,"sOd"=>$elem_macula_od_summary,
																		"wOs"=>$elem_wnlMaculaOs,"sOs"=>$elem_macula_os_summary));
			//Nochanged
			if(!empty($elem_se_mac)&&strpos($elem_se_mac,"=1")!==false){
				$elem_noChangeVit=1;
			}

			$arr=array();

			if(in_array("Macula",$arrTempProc) || in_array("All",$arrTempProc)){
			//Macula
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Macula",
												"sOd"=>$elem_macula_od_summary,"sOs"=>$elem_macula_os_summary,
												"fOd"=>$flgSe_Macula_Od,"fOs"=>$flgSe_Macula_Os,"pos"=>$elem_posMacula,
												"arcCssOd"=>$flgArcColor["os"]["Mac"],"arcCssOs"=>$flgArcColor["os"]["Mac"],
												"enm_2"=>"Mac"));

			}

			//Sub Exam List
			$arr["seList"] = 	array("Mac"=>array("enm"=>"Macula","pos"=>$elem_posMacula,
								"wOd"=>$elem_wnlMaculaOd,"wOs"=>$elem_wnlMaculaOs));
			$arr["bgColor"] = "";
			$arr["nochange"] = $elem_ncMacula;
			$arr["examdate"] = $examdate;
			$arr["moeMN"] = $moeMN;
			$arr["exm_flg_se"] = array($flgSe_Macula_Od,$flgSe_Macula_Os);

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
		$wnl_value_Macula = $this->getExamWnlStr("Macula");

		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_Macula)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_Macula)."') ";
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
					"ncMacula,".
					"ncMacula_od,".
					"ncMacula_os,".
					"wnl_value_Macula";
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
			$wnl_value_Macula="";
			$wnl_value_Macula_phrase="";
			$flgCarry=0;

			//Fundus
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}

			$cQry = "select
					wnlMacula, ".
					"wnlMaculaOd,wnlMaculaOs,
					".
					"posMacula, ".
					"macula_od_summary,macula_os_summary,
					".
					"statusElem AS statusElem_fundus, uid, wnl_value_Macula,
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
			$elem_wnlMaculaOd = $wnlMaculaOd;
			$elem_wnlMaculaOs = $wnlMaculaOs;
			$elem_wnlMacula = $wnlMacula;

			$oWv = new WorkView();

			if(in_array("Macula",$arrTempProc)||in_array("All",$arrTempProc)){
				if((!empty($statusElem_fundus)&&strpos($statusElem_fundus,"0")===false)||
					(empty($macula_od_summary) && empty($elem_wnlMaculaOd))||
					(empty($macula_os_summary) && empty($elem_wnlMaculaOs))
					 ){
				//Toggle Macula
				list($elem_wnlMaculaOd,$elem_wnlMaculaOs,$elem_wnlMacula) =
										$oWv->toggleWNL($posMacula,$macula_od_summary,$macula_os_summary,
														$elem_wnlMaculaOd,$elem_wnlMaculaOs,$elem_wnlMacula,$exmEye);
				}
			}

			//Status
			$statusElem_fundus_prev=$statusElem_fundus;
			$statusElem_fundus = $this->setEyeStatus($w, $exmEye,$statusElem_fundus,0,$elem_retina_version);

			if(empty($wnl_value_Macula)){
				$wnl_value_Macula=$this->getExamWnlStr("Macula");
				$wnl_value_Macula_phrase = ", wnl_value_Macula='".sqlEscStr($wnl_value_Macula)."' ";
			}

			//Fundus
			$sql = "UPDATE ".$this->tbl." SET
				  wnlMacula='".$elem_wnlMacula."', ".
				  "wnlMaculaOd='".$elem_wnlMaculaOd."',
				  wnlMaculaOs='".$elem_wnlMaculaOs."',
				  ".
				  " exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				  statusElem='".$statusElem_fundus."'
				  ".
				  "
				  ";
			$sql .= " ".$wnl_value_Macula_phrase." ";

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
					$ignoreFlds .= "macula_od_summary,
								macula_od,
								wnlMaculaOd,
								ncMacula_od,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }
				}else if($_POST["site"]=="OD"){
					$ignoreFlds .= "macula_os,macula_os_summary,
								wnlMaculaOs,
								ncMacula_os,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posMacula,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Macula,ut_elem,";}
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
						"ncMacula,".
						"ncMacula_od,".
						"ncMacula_os,".
						"wnl_value_Macula";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values
			}
		}
	}

	function isNoChanged(){
		$res= $this->getRecord("ncMacula,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncMacula"]) ){
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
		$elem_ncMacula="0";
		$elem_ncMacula_od="0";
		$elem_ncMacula_os="0";

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
				$elem_ncMacula="1";
				$elem_ncMacula_od="1";
				$elem_ncMacula_os="1";
			}else if($exmEye=="OD"){
				$elem_ncMacula_od="1";
			}else if($exmEye=="OS"){
				$elem_ncMacula_os="1";
			}
		}
		// ---

		//Get status string --
		$statusElem="";
		if($elem_ncMacula_od==1||$elem_ncMacula_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncMacula_od,$elem_ncMacula_os,0);}
		//Get status--

		//
		$sql = "UPDATE ".$this->tbl."
			  SET
			  ncMacula = '".$elem_ncMacula."',
			  ncMacula_od='".$elem_ncMacula_od."', ncMacula_os='".$elem_ncMacula_os."',
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}

	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "macula_od";
		$arr["db"]["xmlOs"] = "macula_os";
		$arr["db"]["wnlSE"] = "wnlMacula";
		$arr["db"]["wnlOd"] = "wnlMaculaOd";
		$arr["db"]["wnlOs"] = "wnlMaculaOs";
		$arr["db"]["posSE"] = "posMacula";
		$arr["db"]["summOd"] = "macula_od_summary";
		$arr["db"]["summOs"] = "macula_os_summary";
		$arr["divSe"] = "2";
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
