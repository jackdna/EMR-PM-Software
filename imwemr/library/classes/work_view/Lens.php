<?php

class Lens extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_lens";
		$this->examName="Lens";
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
		
		$sql = "SELECT * FROM ".$this->tbl." WHERE form_Id = '$form_id' AND patient_id ='".$patient_id."' AND purged = '0' ";		
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
			//$res = valNewRecordSle($patient_id);	
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$blDrwaingGray = true;	
			$myflag=true;		
		}

		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			//$res = valNewRecordSle($patient_id, " * ", "1");
			$res = $this->getLastRecord(" * ",1,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}
		
		if($row!=false){
		//if(@mysql_num_rows($res)>0){
			//extract(sqlFetchArray($res));
			extract($row);
			
			if($myflag){
				$elem_editMode=0;
				// As last record form_id overwrite current form_id, so retore current form_id
				$form_id = $form_id_cur;		
			}else{
				$elem_editMode=1;
			}
			$ar_ret["elem_lensId_LF"]=$ar_ret["elem_lensId"]=$id;
			$ar_ret["elem_editModeLens"] = $elem_editMode;
			//$elem_formId=$form_id;
			//$elem_patientId=$patient_id;
			//NC
			if($elem_editMode==1){
				$ar_ret["elem_ncLens"] = $ncLens;
				$ar_ret["elem_examDateLens"]=$exam_date;
			}
			
			$lens_od= stripslashes($lens_od);
			$lens_os= stripslashes($lens_os);
			
			$arr_vals_lens_od = $oExamXml->extractXmlValue($lens_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_lens_od);
			$arr_vals_lens_os = $oExamXml->extractXmlValue($lens_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_lens_os);			
			
			$ar_ret["elem_wnlLens"] = $wnlLens;
			$ar_ret["elem_posLens"] = $posLens;
			$ar_ret["elem_wnlLensOd"] = $wnlLensOd;
			$ar_ret["elem_wnlLensOs"] = $wnlLensOs;
			$ar_ret["elem_statusElementsLens"] = ($elem_editMode==0) ? "" : $statusElem;
			$ar_ret["elem_penLight"]=$pen_light;
			
			//UT Elems //($elem_editMode==1) ?
			$ar_ret["elem_utElemsLens"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;
			
			$ar_ret["lens_od_summary"]=$lens_od_summary;
			$ar_ret["lens_os_summary"]=$lens_os_summary;
		}
		return $ar_ret;
	}
	
	function remDecentered($str){	
		$a="IOL in good position De-centered";
		if(trim($str)==$a || strpos($str,$a)!==false){  $str = str_replace($a,"IOL in good position",$str); }		
		return $str;
	}

	public function save_form(){		
		$elem_formId = $formId = $this->fid;
		$patientid = $this->pid;
		$oChartNoteSaver = new ChartNoteSaver($patientid, $formId);
		$oExamXml = new ExamXml();	
		$arXmlFiles = $oExamXml->getExamXmlFiles("SLE");
		
		$arrSe = array();		
		$tmpd = "elem_chng_div5_Od";
		$tmps = "elem_chng_div5_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = $$tmpd;
		$arrSe[$tmps] = $$tmps;		
		
		//
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);
		
		//MFocal
		if(!empty($_POST["elem_mfocalOd_pciol_other"]))$_POST["elem_mfocalOd_pciol_opts"]=$_POST["elem_mfocalOd_pciol_other"];
		if(!empty($_POST["elem_mfocalOs_pciol_other"]))$_POST["elem_mfocalOs_pciol_opts"]=$_POST["elem_mfocalOs_pciol_other"];
		//--
		
		//IOL axis
		if(!empty($_POST["elem_iolAxisOd_pciol"])){  $_POST["elem_iolAxisOd_pciol"] = "IOL Axis ". $_POST["elem_iolAxisOd_pciol"] . " degrees" ; }
		if(!empty($_POST["elem_iolAxisOs_pciol"])){  $_POST["elem_iolAxisOs_pciol"] = "IOL Axis ". $_POST["elem_iolAxisOs_pciol"] . " degrees" ; }
	
		//Lens ------------
		$wnlLensOd = $wnlLensOs = $wnlLens = $posLens = $ncLens = "0";
		//if(!empty($elem_chng_div5_Od) || !empty($elem_chng_div5_Os)){
			//if(!empty($elem_chng_div5_Od)){
				$menuName = "lensOd";
				$menuFilePath = $arXmlFiles["lens"]["od"]; //dirname(__FILE__)."/xml/lens_od.xml";
				$elem_lens_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLensOd = $_POST["elem_wnlLensOd"];
			//}

			//if(!empty($elem_chng_div5_Os)){
				$menuName = "lensOs";
				$menuFilePath = $arXmlFiles["lens"]["os"]; //dirname(__FILE__)."/xml/lens_os.xml";
				$elem_lens_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLensOs = $_POST["elem_wnlLensOs"];
			//}

			$wnlLens = (!empty($wnlLensOd) && !empty($wnlLensOs)) ? "1" : "0"; // $_POST["elem_wnlLens"];
			$posLens = $_POST["elem_posLens"];
			$ncLens = $_POST["elem_ncLens"];
		//}
		//Lens ------------

		//SLE ------------
		$examDate = wv_dt("now");
		$pen_light = $_POST["elem_penLight"];
		
		//
		$oUserAp = new UserAp();
		
		//Summary --
		$strExamsAllOd = $strExamsAllOs = "";
		$lens_od_summary = "";
		$lens_os_summary = "";

		$lens_od = $elem_lens_od; // Lens Od
		
		$arrTemp = $this->getExamSummary($lens_od);
		$lens_od_summary = $this->remDecentered($arrTemp["Summary"]);
		
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div5_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Lens",$arrExmDone_od,$lens_od_summary);	
		}
		$lens_os = $elem_lens_os; // Lens Os
		
		$arrTemp = $this->getExamSummary($lens_os);
		$lens_os_summary = $this->remDecentered($arrTemp["Summary"]);
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div5_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Lens",$arrExmDone_os,$lens_os_summary);
		}
		//Summary --
		
		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsLens"];
		$elem_utElems_cur = $_POST["elem_utElemsLens_cur"];
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
			
			$elem_lens_od = sqlEscStr($elem_lens_od);
			$elem_lens_os = sqlEscStr($elem_lens_os);
			
			//check
			$cQry = "select 
					id,last_opr_id,uid,
					lens_od_summary, lens_os_summary, wnlLensOd, wnlLensOs, modi_note_LensArr, wnl_value_Lens,exam_date				
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			
			if($row == false){
				$elem_editMode = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$lensId = $lensIDExam = $row["id"];
				$elem_editMode = "1";
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				//Modifying Notes----------------				
				$seri_modi_note_LensArr = $owv->getModiNotesArr($row["lens_od_summary"],$lens_od_summary,$last_opr_id,'OD',$row["modi_note_LensArr"],$row['exam_date']);
				$seri_modi_note_LensArr = $owv->getModiNotesArr($row["lens_os_summary"],$lens_os_summary,$last_opr_id,'OS',$seri_modi_note_LensArr,$row['exam_date']);
				//Modifying Notes----------------					
			}
			
			//
			$sql_con = "
				lens_od = '".$elem_lens_od."',
				lens_os = '".$elem_lens_os."',
				lens_od_summary='".$lens_od_summary."',
				lens_os_summary='".$lens_os_summary."',
				wnlLens='".$wnlLens."',
				posLens='".$posLens."',
				ncLens='".$ncLens."',
				wnlLensOd='".$wnlLensOd."',
				wnlLensOs='".$wnlLensOs."',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				pen_light = '".$pen_light."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_LensArr = '".sqlEscStr($seri_modi_note_LensArr)."'
			";
			
			if($elem_editMode == "0"){
				//
				$wnl_value_Lens = $this->getExamWnlStr("Lens");
			
				// Insert
				$sql1 = "insert into ".$this->tbl."
							SET
							form_id = '".$elem_formId."',
							patient_id='".$patientid."',
							exam_date='".$examDate."',
							wnl_value_Lens='".sqlEscStr($wnl_value_Lens)."',
							";
							//print '<pre>';
							//echo $sql;
				$sql = $sql1. $sql_con;				
				$insertId = sqlInsert($sql);			
			}
			else if($elem_editMode == "1"){
				//Update
				$sql1 = "UPDATE ".$this->tbl." SET ";
				$sql2 = " WHERE form_id='".$formId."' AND patient_id='".$patientid."' AND purged = '0' ";
				$sql = $sql1. $sql_con. $sql2; 
				$res = sqlQuery($sql);
				$insertId = $lensId;				
			}
			
			//exit($sql);
			
			// Make chart notes valid
			$this->makeChartNotesValid();			

			//Set Change Date Arc Rec --
			$oChartNoteSaver->setChangeDtArcRec($this->tbl);
			//Set Change Date Arc Rec --			
		}
		
		//
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
			"c10.posLens, ".
			"c10.wnlLens, ".
			"c10.ncLens, ".
			"c10.lens_od_summary, ".
			"c10.lens_os_summary, c10.id, ".
			"c10.wnlLensOd,".
			"c10.wnlLensOs, ".				
			"c10.wnl_value_Lens, ".			
			"c10.pen_light, c10.exam_date, ".
			"c10.statusElem AS se_sle, c10.modi_note_LensArr,
			c10.last_opr_id AS last_opr_id_sle
			 ".
			"FROM chart_master_table c1 ".
			"INNER JOIN ".$this->tbl." c10 ON c10.form_id = c1.id AND c10.purged='0'  ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ";
		$row = sqlQuery($sql);
		//$row=$oSLE->sqlExe($sql);
		//------ Check in archive tables for chart_slit_lamp_exam/chart_la/chart_rv ---------
		if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
			if($row==false){
				$sql = str_replace($this->tbl,$this->tbl."_archive",$sql);
				$row = sqlQuery($sql);
			}	
		}
		//-------- End ----------------------------------------------------------------------------------
		if($row != false){
			//SLE
			$elem_posLens = assignZero($row["posLens"]);
			$elem_wnlLens = assignZero($row["wnlLens"]);
			$elem_ncLens = assignZero($row["ncLens"]);
			$elem_wnlLensOd = assignZero($row["wnlLensOd"]);
			$elem_wnlLensOs = assignZero($row["wnlLensOs"]);
			$elem_se_lens = $row["se_sle"];			
			$elem_wnl_value_Lens = $row["wnl_value_Lens"];			
			$elem_lens_od_summary = $row["lens_od_summary"];
			$elem_lens_os_summary = $row["lens_os_summary"];
			$elem_lens_id = $row["id"];
			$elem_pen_light = $row["pen_light"];
			$examdate = wv_formatDate($row["exam_date"]);			
			$modi_note_LensArr = unserialize($row["modi_note_LensArr"]);			
			$arrHx = array();			
			if(is_array($modi_note_LensArr) && count($modi_note_LensArr)>0 && $row["modi_note_LensArr"]!='')
			$arrHx['Lens'] = $modi_note_LensArr;
		}

		//Previous 
		if(empty($elem_lens_id)){

			$tmp = "";
			$tmp .= " c2.posLens, ";
			$tmp .= " c2.wnlLens, ";			
			$tmp .= " c2.wnlLensOd,
					c2.wnlLensOs, ";
			$tmp .= " c2.ncLens, ";			
			$tmp .= " c2.lens_od_summary, ";
			$tmp .= " c2.wnl_value_Lens, ";
			$tmp .= "  c2.exam_date,
						c2.lens_os_summary, c2.pen_light, c2.id, 
						c2.statusElem AS se_sle ";
			
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordSle($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				$elem_lens_od_summary = $row["lens_od_summary"];
				$elem_lens_os_summary = $row["lens_os_summary"];
				$elem_posLens = assignZero($row["posLens"]);
				$elem_wnlLens = assignZero($row["wnlLens"]);
				$elem_ncLens = assignZero($row["ncLens"]);
				$elem_wnlLensOd = assignZero($row["wnlLensOd"]);
				$elem_wnlLensOs = assignZero($row["wnlLensOs"]);
				$elem_wnl_value_Lens = $row["wnl_value_Lens"];
				$elem_pen_light = $row["pen_light"];
				$examdate = wv_formatDate($row["exam_date"]);
				$elem_se_lens_prev = $row["se_sle"];				
			}
			//BG
			$bgColor_SLE = "bgSmoke";
		}
		
		//---------

		//is Change is made in new chart -----
		$flgSe_Lens_Od = $flgSe_Lens_Os = "0";
		if(!isset($bgColor_SLE)){
			if(!empty($elem_se_lens)){
				$tmpArrSe = $this->se_elemStatus("SLE","0",$elem_se_lens);
				$flgSe_Lens_Od = $tmpArrSe["5"]["od"];
				$flgSe_Lens_Os = $tmpArrSe["5"]["os"];				
			}
		}else{
			if(!empty($elem_se_lens_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("SLE","0",$elem_se_lens_prev);
				$flgSe_Lens_Od_prev = $tmpArrSe_prev["5"]["od"];
				$flgSe_Lens_Os_prev = $tmpArrSe_prev["5"]["os"];				
			}
		}
		//is Change is made in new chart -----
		
		
		//WNL		
		//Lens --
		$wnlString_Lens = !empty($elem_wnl_value_Lens) ? $elem_wnl_value_Lens : $this->getExamWnlStr("Lens");
		$wnlStringOd_Lens = $wnlStringOs_Lens = $wnlString_Lens; 
		
		if(empty($flgSe_Lens_Od) && empty($flgSe_Lens_Od_prev) && !empty($elem_wnlLensOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lens", "OD"); if(!empty($tmp)){ $wnlStringOd_Lens = $tmp;}  }
		if(empty($flgSe_Lens_Os) && empty($flgSe_Lens_Os_prev) && !empty($elem_wnlLensOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lens", "OS"); if(!empty($tmp)){ $wnlStringOs_Lens = $tmp;}  }
		
		list($elem_lens_od_summary,$elem_lens_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Lens,"wValOs"=>$wnlStringOs_Lens,
																	"wOd"=>$elem_wnlLensOd,"sOd"=>$elem_lens_od_summary,
																	"wOs"=>$elem_wnlLensOs,"sOs"=>$elem_lens_os_summary));

		//Nochanged
		if(!empty($elem_se_lens)&&strpos($elem_se_lens,"=1")!==false){
			$elem_noChangeLens=1;
		}
		
		//Archived SLE --
		if($bgColor_SLE != "bgSmoke"){
		
		$arrDivArcCmn=array();
		$oChartRecArc->setChkTbl($this->tbl);
		$arrInpArc = array("elem_sumOdLens"=>array("lens_od_summary",$elem_lens_od_summary,"smof","wnlLensOd",$wnlString_Lens,$modi_note_LensOd),
			"elem_sumOsLens"=>array("lens_os_summary",$elem_lens_os_summary,"smof","wnlLensOs",$wnlString_Lens,$modi_note_LensOs)
							);
		$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
		
		//Lens --
		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdLens"])){
			//echo $arTmpRecArc["div"]["elem_sumOdLens"];
			$arrDivArcCmn["Lens"]["OD"]=$arTmpRecArc["div"]["elem_sumOdLens"];
			$moeArc["od"]["Lens"] = $arTmpRecArc["js"]["elem_sumOdLens"];
			$flgArcColor["od"]["Lens"] = $arTmpRecArc["css"]["elem_sumOdLens"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdLens"])) 
				$elem_lens_od_summary = $arTmpRecArc["curText"]["elem_sumOdLens"];
		}else{
			$moeArc["od"]["Lens"] = $flgArcColor["od"]["Lens"]="";
		}
		//OS
		if(!empty($arTmpRecArc["div"]["elem_sumOsLens"])){
			//echo $arTmpRecArc["div"]["elem_sumOsLens"];
			$arrDivArcCmn["Lens"]["OS"]=$arTmpRecArc["div"]["elem_sumOsLens"];
			$moeArc["os"]["Lens"] = $arTmpRecArc["js"]["elem_sumOsLens"];
			$flgArcColor["os"]["Lens"] = $arTmpRecArc["css"]["elem_sumOsLens"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOsLens"])) 
				$elem_lens_os_summary = $arTmpRecArc["curText"]["elem_sumOsLens"];
		}else{
			$moeArc["os"]["Lens"] = $flgArcColor["os"]["Lens"]="";
		}
		//Lens --
		
		}//
		
		//Archived SLE --
		
		$arr=array();
		
		//if(in_array("Lens",$arrTempProc) || in_array("All",$arrTempProc)){
		//Lens
		$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Lens",
											"sOd"=>$elem_lens_od_summary,"sOs"=>$elem_lens_os_summary,
											"fOd"=>$flgSe_Lens_Od,"fOs"=>$flgSe_Lens_Os,"pos"=>$elem_posLens,
											//"arcJsOd"=>$moeArc["od"]["Lens"],"arcJsOs"=>$moeArc["os"]["Lens"],
											"arcCssOd"=>$flgArcColor["od"]["Lens"],"arcCssOs"=>$flgArcColor["os"]["Lens"],
											//"mnOd"=>$moeMN["od"]["Lens"],"mnOs"=>$moeMN["os"]["Lens"],
											"enm_2"=>"Lens"));
		//}	
		
		//Sub Exam List
		$arr["seList"] = 	array("Lens"=>array("enm"=>"Lens","pos"=>$elem_posLens,
						"wOd"=>$elem_wnlLensOd,"wOs"=>$elem_wnlLensOs)					
					);
		$arr["nochange"] = $elem_noChangeLens;		
		$arr["bgColor"] = "".$bgColor_SLE;
		$arr["penLight"] = $elem_pen_light;		
		$arr["examdate"] = $examdate;		
		$arr["moeMN"] = $moeMN;			
		$arr["exm_flg_se"] = array($flgSe_Lens_Od,$flgSe_Lens_Os);
		
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
			"c10.posLens, ".
			"c10.wnlLens, ".
			"c10.ncLens, ".
			"c10.lens_od_summary, ".
			"c10.lens_os_summary, c10.id, ".
			"c10.wnlLensOd,".
			"c10.wnlLensOs, ".				
			"c10.wnl_value_Lens, ".			
			"c10.pen_light, c10.exam_date, ".
			"c10.statusElem AS se_sle, c10.modi_note_LensArr,
			c10.last_opr_id AS last_opr_id_sle, c10.purgerId, c10.purgeTime, c10.purgerId AS purgerId_sle, c10.purgeTime AS purgeTime_sle
			 ".
			"FROM chart_master_table c1 ".
			"INNER JOIN ".$this->tbl." c10 ON c10.form_id = c1.id AND c10.purged!='0'  ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ";
		
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
			//SLE
			$elem_posLens = assignZero($row["posLens"]);
			$elem_wnlLens = assignZero($row["wnlLens"]);
			$elem_ncLens = assignZero($row["ncLens"]);
			$elem_wnlLensOd = assignZero($row["wnlLensOd"]);
			$elem_wnlLensOs = assignZero($row["wnlLensOs"]);
			$elem_se_lens = $row["se_sle"];			
			$elem_wnl_value_Lens = $row["wnl_value_Lens"];			
			$elem_lens_od_summary = $row["lens_od_summary"];
			$elem_lens_os_summary = $row["lens_os_summary"];
			$elem_lens_id = $row["id"];
			$elem_pen_light = $row["pen_light"];
			$examdate = wv_formatDate($row["exam_date"]);			
			$modi_note_LensArr = unserialize($row["modi_note_LensArr"]);			
			$arrHx = array();			
			if(count($modi_note_LensArr)>0 && $row["modi_note_LensArr"]!='')
			$arrHx['Lens'] = $modi_note_LensArr;
			
			//is Change is made in new chart -----
			$flgSe_Lens_Od = $flgSe_Lens_Os = "0";
			if(!isset($bgColor_SLE)){
				if(!empty($elem_se_lens)){
					$tmpArrSe = $this->se_elemStatus("SLE","0",$elem_se_lens);
					$flgSe_Lens_Od = $tmpArrSe["5"]["od"];
					$flgSe_Lens_Os = $tmpArrSe["5"]["os"];				
				}
			}else{
				if(!empty($elem_se_Sle_prev)){
					$tmpArrSe_prev = $this->se_elemStatus("SLE","0",$elem_se_Sle_prev);
					$flgSe_Lens_Od_prev = $tmpArrSe_prev["5"]["od"];
					$flgSe_Lens_Os_prev = $tmpArrSe_prev["5"]["os"];				
				}
			}
			//is Change is made in new chart -----		

			//WNL		
			//Lens --
			$wnlString_Lens = !empty($elem_wnl_value_Lens) ? $elem_wnl_value_Lens : $this->getExamWnlStr("Lens");
			$wnlStringOd_Lens = $wnlStringOs_Lens = $wnlString_Lens; 
			
			if(empty($flgSe_Lens_Od) && empty($flgSe_Lens_Od_prev) && !empty($elem_wnlLensOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lens", "OD"); if(!empty($tmp)){ $wnlStringOd_Lens = $tmp;}  }
			if(empty($flgSe_Lens_Os) && empty($flgSe_Lens_Os_prev) && !empty($elem_wnlLensOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lens", "OS"); if(!empty($tmp)){ $wnlStringOs_Lens = $tmp;}  }
			
			list($elem_lens_od_summary,$elem_lens_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Lens,"wValOs"=>$wnlStringOs_Lens,
																		"wOd"=>$elem_wnlLensOd,"sOd"=>$elem_lens_od_summary,
																		"wOs"=>$elem_wnlLensOs,"sOs"=>$elem_lens_os_summary));

			//Nochanged
			if(!empty($elem_se_lens)&&strpos($elem_se_lens,"=1")!==false){
				$elem_noChangeLens=1;
			}
			
			$arr=array();
			
			if(in_array("Lens",$arrTempProc) || in_array("All",$arrTempProc)){
			//Lens
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Lens",
												"sOd"=>$elem_lens_od_summary,"sOs"=>$elem_lens_os_summary,
												"fOd"=>$flgSe_Lens_Od,"fOs"=>$flgSe_Lens_Os,"pos"=>$elem_posLens,
												//"arcJsOd"=>$moeArc["od"]["Lens"],"arcJsOs"=>$moeArc["os"]["Lens"],
												"arcCssOd"=>$flgArcColor["od"]["Lens"],"arcCssOs"=>$flgArcColor["os"]["Lens"],
												//"mnOd"=>$moeMN["od"]["Lens"],"mnOs"=>$moeMN["os"]["Lens"],
												"enm_2"=>"Lens"));
			}	
			
			//Sub Exam List
			$arr["seList"] = 	array("Lens"=>array("enm"=>"Lens","pos"=>$elem_posLens,
							"wOd"=>$elem_wnlLensOd,"wOs"=>$elem_wnlLensOs)					
						);
			$arr["nochange"] = $elem_noChangeLens;		
			$arr["bgColor"] = "".$bgColor_SLE;
			$arr["penLight"] = $elem_pen_light;		
			$arr["examdate"] = $examdate;		
			$arr["moeMN"] = $moeMN;			
			$arr["exm_flg_se"] = array($flgSe_Lens_Od,$flgSe_Lens_Os);
			
			//$arr["arrDivArcCmn"] = $arrDivArcCmn;
			$arr["arrHx"] = $arrHx;
			$arr["purgerId"] = $row["purgerId_sle"];
			$arr["purgeTime"] = $row["purgeTime_sle"];
			
			$arPurge[] = $arr;
		}
		return $arPurge;
	}

	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		//WNL
		$wnl_value_Lens = $this->getExamWnlStr("Lens");		
		
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_Lens)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_Lens)."') ";
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
					"ncLens,".
					"ncLens_od,".
					"ncLens_os,".
					"wnl_value_Lens";
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
			//			
			$wnl_value_Lens="";
			$wnl_value_Lens_phrase="";
			$flgCarry=0;	
			
			//Fundus
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}
			
			$cQry = "select 
					wnlLens, ".					
					"wnlLensOd,wnlLensOs,
					".					
					"posLens, ".					
					"lens_od_summary,lens_os_summary,						
					".					
					"statusElem, uid, wnl_value_Lens, 
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
			$elem_wnlLensOd = $wnlLensOd;
			$elem_wnlLensOs = $wnlLensOs;
			$elem_wnlLens = $wnlLens;
		
			$oWv = new WorkView();
			
			if(in_array("Lens",$arrTempProc)||in_array("All",$arrTempProc)){
			if((!empty($statusElem)&&strpos($statusElem,"0")===false)||
				(empty($lens_od_summary) && empty($elem_wnlLensOd))||
				(empty($lens_os_summary) && empty($elem_wnlLensOs))
				){
			//Toggle Lens
			list($elem_wnlLensOd,$elem_wnlLensOs,$elem_wnlLens) =
									$oWv->toggleWNL($posLens,$lens_od_summary,$lens_os_summary,
													$elem_wnlLensOd,$elem_wnlLensOs,$elem_wnlLens,$exmEye);
			}
			}
			
			//Status
			$statusElem_prev=$statusElem;
			$statusElem = $this->setEyeStatus($w, $exmEye,$statusElem,0);
			
			if(empty($wnl_value_Lens)){
				$wnl_value_Lens=$this->getExamWnlStr("Lens");
				$wnl_value_Lens_phrase = ", wnl_value_Lens='".sqlEscStr($wnl_value_Lens)."' ";
			}
			
			//Fundus
			$sql = "UPDATE ".$this->tbl." SET  
				  wnlLens='".$elem_wnlLens."', ".
				  "wnlLensOd='".$elem_wnlLensOd."',
				  wnlLensOs='".$elem_wnlLensOs."',				  
				  ".
				  " exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				  statusElem='".$statusElem."'
				  ".				  
				  "				  
				  ";			
			$sql .= " ".$wnl_value_Lens_phrase." ";
			
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
					$ignoreFlds .= "lens_od_summary,
								lens_od,
								wnlLensOd,
								ncLens_od,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "lens_os,lens_os_summary,
								wnlLensOs,
								ncLens_os,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posLens,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Lens,ut_elem,";}
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
						"ncLens,".
						"ncLens_od,".
						"ncLens_os,".
						"wnl_value_Lens";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}
	}
	
	function isNoChanged(){
		$res= $this->getRecord("ncLens,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncLens"]) ){
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
		$elem_ncLens="0";			
		$elem_ncLens_od="0";			
		$elem_ncLens_os="0";
		
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
				$elem_ncLens="1";
				$elem_ncLens_od="1";
				$elem_ncLens_os="1";
			}else if($exmEye=="OD"){				
				$elem_ncLens_od="1";
			}else if($exmEye=="OS"){
				$elem_ncLens_os="1";
			}
		}			
		// ---
		
		//Get status string --
		$statusElem="";
		if($elem_ncLens_od==1||$elem_ncLens_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncLens_od,$elem_ncLens_os,0);}
		//Get status--		
		
		//
		$sql = "UPDATE ".$this->tbl."
			  SET			  
			  ncLens = '".$elem_ncLens."',
			  ncLens_od='".$elem_ncLens_od."', ncLens_os='".$elem_ncLens_os."',			  
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}
	
	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "lens_od";
		$arr["db"]["xmlOs"] = "lens_os";
		$arr["db"]["wnlSE"] = "wnlLens";
		$arr["db"]["wnlOd"] = "wnlLensOd";
		$arr["db"]["wnlOs"] = "wnlLensOs";
		$arr["db"]["posSE"] = "posLens";
		$arr["db"]["summOd"] = "lens_od_summary";
		$arr["db"]["summOs"] = "lens_os_summary";
		$arr["divSe"] = "5";
		
		$oExamXml = new ExamXml();
		$tmp = $oExamXml->getExamXmlFiles("SLE", $this->examName);
		$arr["xmlFile"]["OD"] = $tmp["od"];
		$arr["xmlFile"]["OS"] = $tmp["os"];
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