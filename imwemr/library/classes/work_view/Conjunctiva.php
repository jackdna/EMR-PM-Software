<?php

class Conjunctiva extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_conjunctiva";
		$this->examName="Conjunctiva";
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
		
		$sql = "SELECT * FROM ".$this->tbl." WHERE form_Id = '".$form_id."' AND patient_id ='".$patient_id."' AND purged = '0' ";		
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
			$ar_ret["elem_conjId_LF"]=$ar_ret["elem_conjId"]=$id;
			$ar_ret["elem_editModeConj"] = $elem_editMode;
			//$elem_formId=$form_id;
			//$elem_patientId=$patient_id;
			//NC
			if($elem_editMode==1){				
				$ar_ret["elem_ncConj"] = $ncConj;				
				$ar_ret["elem_examDateConj"]=$exam_date;
			}
			
			$conjunctiva_od= stripslashes($conjunctiva_od);
			$conjunctiva_os= stripslashes($conjunctiva_os);			
			
			$arr_vals_conjunctiva_od = $oExamXml->extractXmlValue($conjunctiva_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_conjunctiva_od);
			$arr_vals_conjunctiva_os = $oExamXml->extractXmlValue($conjunctiva_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_conjunctiva_os);
			
			$ar_ret["elem_wnlConj"] = $wnlConj;
			$ar_ret["elem_posConj"] = $posConj;
			$ar_ret["elem_wnlConjOd"] = $wnlConjOd;
			$ar_ret["elem_wnlConjOs"] = $wnlConjOs;
			$ar_ret["elem_statusElementsConj"] = ($elem_editMode==0) ? "" : $statusElem;
			$ar_ret["elem_penLight"]=$pen_light;
			
			//UT Elems //($elem_editMode==1) ?
			$ar_ret["elem_utElemsConj"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;

			$ar_ret["conjunctiva_od_summary"] = $conjunctiva_od_summary;
			$ar_ret["conjunctiva_od_summary"] = $conjunctiva_od_summary;			
		}
		return $ar_ret;
	}

	public function save_form(){
		$elem_formId = $formId = $this->fid;
		$patientid = $this->pid;
		$oChartNoteSaver = new ChartNoteSaver($patientid, $formId);
		$oExamXml = new ExamXml();	
		$arXmlFiles = $oExamXml->getExamXmlFiles("SLE");
		
		$arrSe = array();
		$tmpd = "elem_chng_div1_Od";
		$tmps = "elem_chng_div1_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = $$tmpd;
		$arrSe[$tmps] = $$tmps;
		
		//
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);
	
		//Conjuctiva ------------
		$wnlConj = $posConj = $ncConj = $wnlConjOd = $wnlConjOs = "0";
		//if(!empty($elem_chng_div1_Od) || !empty($elem_chng_div1_Os)){
			//if(!empty($elem_chng_div1_Od)){
				$menuName = "conjuctivaOd";
				$menuFilePath = $arXmlFiles["conjunctiva"]["od"]; //dirname(__FILE__)."/xml/conjunctiva_od.xml";
				$elem_conjuctiva_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlConjOd = $_POST["elem_wnlConjOd"];
			//}

			//if(!empty($elem_chng_div1_Os)){
				$menuName = "conjuctivaOs";
				$menuFilePath = $arXmlFiles["conjunctiva"]["os"]; //dirname(__FILE__)."/xml/conjunctiva_os.xml";
				$elem_conjuctiva_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlConjOs = $_POST["elem_wnlConjOs"];
			//}

			$wnlConj = (!empty($wnlConjOd) && !empty($wnlConjOs)) ? "1" : "0"; // $_POST["elem_wnlConj"];
			$posConj = $_POST["elem_posConj"];
			$ncConj = $_POST["elem_ncConj"];
		//}
		//Conjuctiva ------------	
		
		//SLE ------------
		$examDate = wv_dt("now");
		$pen_light = $_POST["elem_penLight"];		
		//
		$oUserAp = new UserAp();
		
		//Summary --
		$strExamsAllOd = $strExamsAllOs = "";
		$conjunctiva_od_summary =  "";
		$conjuctiva_os_summary =  "";

		$conjunctiva_od = $elem_conjuctiva_od; //Conjuctiva Od
		
		$arrTemp = $this->getExamSummary($conjunctiva_od);
		$conjunctiva_od_summary = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div1_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Conjuctiva",$arrExmDone_od,$conjunctiva_od_summary);
		}

		$conjunctiva_os = $elem_conjuctiva_os; //Conjuctiva Os
		
		$arrTemp = $this->getExamSummary($conjunctiva_os);
		$conjunctiva_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div1_Os"])){
		$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Conjuctiva",$arrExmDone_os,$conjunctiva_os_summary);
		}		
		
		//Summary --
		
		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsConj"];
		$elem_utElems_cur = $_POST["elem_utElemsConj_cur"];
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
			
			$elem_conjuctiva_od = sqlEscStr($elem_conjuctiva_od);
			$elem_conjuctiva_os = sqlEscStr($elem_conjuctiva_os);
			
			//check
			$cQry = "select 
					id,last_opr_id,uid,
					conjunctiva_od_summary, conjunctiva_os_summary, wnlConjOd, wnlConjOs, modi_note_ConjArr, wnl_value_Conjunctiva,
					exam_date				
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			
			if($row == false){
				$elem_editMode = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$conjId = $conjIDExam = $row["id"];
				$elem_editMode = "1";
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				//Modifying Notes----------------
				
				$seri_modi_note_ConjArr = $owv->getModiNotesArr($row["conjunctiva_od_summary"],$conjunctiva_od_summary,$last_opr_id,'OD',$row["modi_note_ConjArr"],$row['exam_date']);
				$seri_modi_note_ConjArr = $owv->getModiNotesArr($row["conjunctiva_os_summary"],$conjunctiva_os_summary,$last_opr_id,'OS',$seri_modi_note_ConjArr,$row['exam_date']);
				
				//Modifying Notes----------------					
			}
			
			//
			$sql_con = "
				conjunctiva_od = '".$elem_conjuctiva_od."',
				conjunctiva_os = '".$elem_conjuctiva_os."',
				conjunctiva_od_summary='".$conjunctiva_od_summary."',
				conjunctiva_os_summary='".$conjunctiva_os_summary."',
				wnlConj='".$wnlConj."',
				posConj='".$posConj."',
				ncConj='".$ncConj."',
				wnlConjOd='".$wnlConjOd."',
				wnlConjOs='".$wnlConjOs."',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				pen_light = '".$pen_light."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_ConjArr = '".sqlEscStr($seri_modi_note_ConjArr)."'
			";
			
			if($elem_editMode == "0"){
				//
				$wnl_value_Conjunctiva = $this->getExamWnlStr("Conjunctiva");				
			
				// Insert
				$sql1 = "insert into ".$this->tbl."
							SET							
							form_id = '".$elem_formId."',
							patient_id='".$patientid."',
							exam_date='".$examDate."',
							wnl_value_Conjunctiva='".sqlEscStr($wnl_value_Conjunctiva)."',
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
				$insertId = $conjId;				
			}
			
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
			"c10.posConj,".
			"c10.wnlConj,".
			"c10.ncConj,".
			"c10.conjunctiva_od_summary,  ".
			"c10.conjunctiva_os_summary, ".
			"c10.id, ".
			"c10.wnlConjOd,c10.wnlConjOs, ".
			"c10.wnl_value_Conjunctiva, ".
			"c10.pen_light, c10.exam_date, ".
			"c10.statusElem AS se_sle, c10.modi_note_ConjArr, 
			c10.last_opr_id AS last_opr_id_sle ".
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
			$elem_posConj = assignZero($row["posConj"]);
			$elem_wnlConj = assignZero($row["wnlConj"]);
			$elem_ncConj = assignZero($row["ncConj"]);
			
			$elem_wnlConjOd = assignZero($row["wnlConjOd"]);
			$elem_wnlConjOs = assignZero($row["wnlConjOs"]);
			$elem_se_conj = $row["se_sle"];
			
			$elem_wnl_value_Conjunctiva = $row["wnl_value_Conjunctiva"];
			
			
			$elem_conjunctiva_od_summary = $row["conjunctiva_od_summary"];
			$elem_conjunctiva_os_summary = $row["conjunctiva_os_summary"];
			
			$elem_conj_id = $row["id"];
			$elem_pen_light = $row["pen_light"];
			$examdate = wv_formatDate($row["exam_date"]);
			
			$modi_note_ConjOd=$row["modi_note_ConjOd"];
			$modi_note_ConjOs=$row["modi_note_ConjOs"];
			
			$modi_note_ConjArr = unserialize($row["modi_note_ConjArr"]);
			
			$arrHx = array();
			if(is_array($modi_note_ConjArr) && count($modi_note_ConjArr)>0 && $row["modi_note_ConjArr"]!='')
			$arrHx['Conjunctiva']	= $modi_note_ConjArr;
		}

		//Previous 
		if(empty($elem_conj_id)){

			$tmp = "";			
			$tmp .= " c2.posConj, ";
			$tmp .= " c2.wnlConj, ";
			$tmp .= " c2.wnlConjOd,c2.wnlConjOs, ";
			$tmp .= " c2.ncConj, ";
			$tmp .= " c2.conjunctiva_od_summary,  ";
			$tmp .= " c2.wnl_value_Conjunctiva, ";
			$tmp .= " c2.conjunctiva_os_summary,  c2.exam_date,
						c2.pen_light, c2.id, 
						c2.statusElem AS se_sle ";
			
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordSle($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				$elem_conjunctiva_od_summary = $row["conjunctiva_od_summary"];
				$elem_conjunctiva_os_summary = $row["conjunctiva_os_summary"];
				$elem_posConj = assignZero($row["posConj"]);
				$elem_wnlConj = assignZero($row["wnlConj"]);
				$elem_ncConj = assignZero($row["ncConj"]);
				$elem_ncCorn = assignZero($row["ncCorn"]);				
				$elem_wnlConjOd = assignZero($row["wnlConjOd"]);
				$elem_wnlConjOs = assignZero($row["wnlConjOs"]);				
				$elem_wnl_value_Conjunctiva = $row["wnl_value_Conjunctiva"];				
				$elem_pen_light = $row["pen_light"];
				$examdate = wv_formatDate($row["exam_date"]);
				$elem_se_conj_prev = $row["se_sle"];
				
			}
			//BG
			$bgColor_SLE = "bgSmoke";
		}
		
		//---------

		//is Change is made in new chart -----
		$flgSe_Conj_Od = $flgSe_Conj_Os = "0";
		if(!isset($bgColor_SLE)){
			if(!empty($elem_se_conj)){
				$tmpArrSe = $this->se_elemStatus("SLE","0",$elem_se_conj);
				$flgSe_Conj_Od = $tmpArrSe["1"]["od"];
				$flgSe_Conj_Os = $tmpArrSe["1"]["os"];				
			}
		}else{
			if(!empty($elem_se_conj_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("SLE","0",$elem_se_conj_prev);
				$flgSe_Conj_Od_prev = $tmpArrSe_prev["1"]["od"];
				$flgSe_Conj_Os_prev = $tmpArrSe_prev["1"]["os"];				
			}
		}
		//is Change is made in new chart -----		

		//WNL
		//Conjunctiva --
		$wnlString_Conjunctiva = !empty($elem_wnl_value_Conjunctiva) ? $elem_wnl_value_Conjunctiva : $this->getExamWnlStr("Conjunctiva");
		$wnlStringOd_Conjunctiva = $wnlStringOs_Conjunctiva = $wnlString_Conjunctiva; 
		
		if(empty($flgSe_Conj_Od) && empty($flgSe_Conj_Od_prev) && !empty($elem_wnlConjOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Conjunctiva", "OD"); if(!empty($tmp)){ $wnlStringOd_Conjunctiva = $tmp;}  }
		if(empty($flgSe_Conj_Os) && empty($flgSe_Conj_Os_prev) && !empty($elem_wnlConjOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Conjunctiva", "OS"); if(!empty($tmp)){ $wnlStringOs_Conjunctiva = $tmp;}  }
		
		list($elem_conjunctiva_od_summary,$elem_conjunctiva_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Conjunctiva,"wValOs"=>$wnlStringOs_Conjunctiva,
																	"wOd"=>$elem_wnlConjOd,"sOd"=>$elem_conjunctiva_od_summary,
																	"wOs"=>$elem_wnlConjOs,"sOs"=>$elem_conjunctiva_os_summary));
		

		//Nochanged
		if(!empty($elem_se_conj)&&strpos($elem_se_conj,"=1")!==false){
			$elem_noChangeConj=1;
		}
		
		//Archived SLE --
		if($bgColor_SLE != "bgSmoke"){
		
		$arrDivArcCmn=array();
		$oChartRecArc->setChkTbl($this->tbl);
		$arrInpArc = array("elem_sumOdConj"=>array("conjunctiva_od_summary",$elem_conjunctiva_od_summary,"smof","wnlConjOd",$wnlString_Conjunctiva,$modi_note_ConjOd),
						"elem_sumOsConj"=>array("conjunctiva_os_summary",$elem_conjunctiva_os_summary,"smof","wnlConjOs",$wnlString_Conjunctiva,$modi_note_ConjOs)
							);
		$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
		//Conj --
		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdConj"])){
			//echo $arTmpRecArc["div"]["elem_sumOdConj"];
			$arrDivArcCmn["Conjunctiva"]["OD"]=$arTmpRecArc["div"]["elem_sumOdConj"];
			$moeArc["od"]["Conj"] = $arTmpRecArc["js"]["elem_sumOdConj"];
			$flgArcColor["od"]["Conj"] = $arTmpRecArc["css"]["elem_sumOdConj"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdConj"])) 
				$elem_conjunctiva_od_summary = $arTmpRecArc["curText"]["elem_sumOdConj"];
		}else{
			$moeArc["od"]["Conj"] = $flgArcColor["od"]["Conj"]="";
		}
		//OS
		if(!empty($arTmpRecArc["div"]["elem_sumOsConj"])){
			//echo $arTmpRecArc["div"]["elem_sumOsConj"];
			$arrDivArcCmn["Conjunctiva"]["OS"]=$arTmpRecArc["div"]["elem_sumOsConj"];
			$moeArc["os"]["Conj"] = $arTmpRecArc["js"]["elem_sumOsConj"];
			$flgArcColor["os"]["Conj"] = $arTmpRecArc["css"]["elem_sumOsConj"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOsConj"])) 
				$elem_conjunctiva_os_summary = $arTmpRecArc["curText"]["elem_sumOsConj"];
		}else{
			$moeArc["os"]["Conj"] = $flgArcColor["os"]["Conj"]="";
		}
		//Conj --		
		
		}//
		
		//Archived SLE --		
		
		$arr=array();
		
		//if(in_array("Conjunctiva",$arrTempProc) || in_array("All",$arrTempProc)){
		//Conjunctiva
		$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Conjunctiva",
											"sOd"=>$elem_conjunctiva_od_summary,"sOs"=>$elem_conjunctiva_os_summary,
											"fOd"=>$flgSe_Conj_Od,"fOs"=>$flgSe_Conj_Os,"pos"=>$elem_posConj,
											//"arcJsOd"=>$moeArc["od"]["Conj"],"arcJsOs"=>$moeArc["os"]["Conj"],
											"arcCssOd"=>$flgArcColor["od"]["Conj"],"arcCssOs"=>$flgArcColor["os"]["Conj"],
											//"mnOd"=>$moeMN["od"]["Conj"],"mnOs"=>$moeMN["os"]["Conj"],
											"enm_2"=>"Conj"));
		//}
		
		//Sub Exam List
		$arr["seList"] = 	array("Conj"=>array("enm"=>"Conjunctiva","pos"=>$elem_posConj,
						"wOd"=>$elem_wnlConjOd,"wOs"=>$elem_wnlConjOs)					
					);
		$arr["bgColor"] = "".$bgColor_SLE;			
		$arr["nochange"] = $elem_noChangeConj;
		$arr["penLight"] = $elem_pen_light;
		$arr["examdate"] = $examdate;		
		$arr["moeMN"] = $moeMN;			
		$arr["exm_flg_se"] = array($flgSe_Conj_Od,$flgSe_Conj_Os);
		$arr["arrHx"] = $arrHx;	
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
			"c10.posConj,".
			"c10.wnlConj,".
			"c10.ncConj,".
			"c10.conjunctiva_od_summary,  ".
			"c10.conjunctiva_os_summary, ".
			"c10.id, ".
			"c10.wnlConjOd,c10.wnlConjOs, ".
			"c10.wnl_value_Conjunctiva, ".
			"c10.pen_light, c10.exam_date, ".
			"c10.statusElem AS se_sle, c10.modi_note_ConjArr, 
			c10.last_opr_id AS last_opr_id_sle, c10.purgerId, c10.purgeTime, c10.purgerId AS purgerId_sle, c10.purgeTime AS purgeTime_sle ".
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
			$elem_posConj = assignZero($row["posConj"]);
			$elem_wnlConj = assignZero($row["wnlConj"]);
			$elem_ncConj = assignZero($row["ncConj"]);
			
			$elem_wnlConjOd = assignZero($row["wnlConjOd"]);
			$elem_wnlConjOs = assignZero($row["wnlConjOs"]);
			$elem_se_conj = $row["se_sle"];
			
			$elem_wnl_value_Conjunctiva = $row["wnl_value_Conjunctiva"];
			
			
			$elem_conjunctiva_od_summary = $row["conjunctiva_od_summary"];
			$elem_conjunctiva_os_summary = $row["conjunctiva_os_summary"];
			
			$elem_conj_id = $row["id"];
			$elem_pen_light = $row["pen_light"];
			$examdate = wv_formatDate($row["exam_date"]);
			
			$modi_note_ConjOd=$row["modi_note_ConjOd"];
			$modi_note_ConjOs=$row["modi_note_ConjOs"];
			
			$modi_note_ConjArr = unserialize($row["modi_note_ConjArr"]);
			
			$arrHx = array();
			if(count($modi_note_ConjArr)>0 && $row["modi_note_ConjArr"]!='')
			$arrHx['Conjunctiva']	= $modi_note_ConjArr;
			
			
			//is Change is made in new chart -----
			$flgSe_Conj_Od = $flgSe_Conj_Os = "0";
			if(!isset($bgColor_SLE)){
				if(!empty($elem_se_conj)){
					$tmpArrSe = $this->se_elemStatus("SLE","0",$elem_se_conj);
					$flgSe_Conj_Od = $tmpArrSe["1"]["od"];
					$flgSe_Conj_Os = $tmpArrSe["1"]["os"];				
				}
			}else{
				if(!empty($elem_se_conj_prev)){
					$tmpArrSe_prev = $this->se_elemStatus("SLE","0",$elem_se_conj_prev);
					$flgSe_Conj_Od_prev = $tmpArrSe_prev["1"]["od"];
					$flgSe_Conj_Os_prev = $tmpArrSe_prev["1"]["os"];				
				}
			}
			//is Change is made in new chart -----		

			//WNL
			//Conjunctiva --
			$wnlString_Conjunctiva = !empty($elem_wnl_value_Conjunctiva) ? $elem_wnl_value_Conjunctiva : $this->getExamWnlStr("Conjunctiva");
			$wnlStringOd_Conjunctiva = $wnlStringOs_Conjunctiva = $wnlString_Conjunctiva; 
			
			if(empty($flgSe_Conj_Od) && empty($flgSe_Conj_Od_prev) && !empty($elem_wnlConjOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Conjunctiva", "OD"); if(!empty($tmp)){ $wnlStringOd_Conjunctiva = $tmp;}  }
			if(empty($flgSe_Conj_Os) && empty($flgSe_Conj_Os_prev) && !empty($elem_wnlConjOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Conjunctiva", "OS"); if(!empty($tmp)){ $wnlStringOs_Conjunctiva = $tmp;}  }
			
			list($elem_conjunctiva_od_summary,$elem_conjunctiva_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Conjunctiva,"wValOs"=>$wnlStringOs_Conjunctiva,
																		"wOd"=>$elem_wnlConjOd,"sOd"=>$elem_conjunctiva_od_summary,
																		"wOs"=>$elem_wnlConjOs,"sOs"=>$elem_conjunctiva_os_summary));
			//Nochanged
			if(!empty($elem_se_conj)&&strpos($elem_se_conj,"=1")!==false){
				$elem_noChangeConj=1;
			}
			
			$arr=array();
			
			if(in_array("Conjunctiva",$arrTempProc) || in_array("All",$arrTempProc)){
			//Conjunctiva
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Conjunctiva",
												"sOd"=>$elem_conjunctiva_od_summary,"sOs"=>$elem_conjunctiva_os_summary,
												"fOd"=>$flgSe_Conj_Od,"fOs"=>$flgSe_Conj_Os,"pos"=>$elem_posConj,
												//"arcJsOd"=>$moeArc["od"]["Conj"],"arcJsOs"=>$moeArc["os"]["Conj"],
												"arcCssOd"=>$flgArcColor["od"]["Conj"],"arcCssOs"=>$flgArcColor["os"]["Conj"],
												//"mnOd"=>$moeMN["od"]["Conj"],"mnOs"=>$moeMN["os"]["Conj"],
												"enm_2"=>"Conj"));
			}
			
			//Sub Exam List
			$arr["seList"] = 	array("Conj"=>array("enm"=>"Conjunctiva","pos"=>$elem_posConj,
							"wOd"=>$elem_wnlConjOd,"wOs"=>$elem_wnlConjOs)					
						);
			$arr["bgColor"] = "".$bgColor_SLE;			
			$arr["nochange"] = $elem_noChangeConj;
			$arr["penLight"] = $elem_pen_light;
			$arr["examdate"] = $examdate;		
			$arr["moeMN"] = $moeMN;			
			$arr["exm_flg_se"] = array($flgSe_Conj_Od,$flgSe_Conj_Os);
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
		$wnl_value_Conjunctiva = $this->getExamWnlStr("Conjunctiva");		
		
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_Conjunctiva)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_Conjunctiva)."') ";
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
					"ncConj,".
					"ncConj_od,".
					"ncConj_os,".
					"wnl_value_Conjunctiva";
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
			$wnl_value_Conjunctiva="";
			$wnl_value_Conjunctiva_phrase="";
			$flgCarry=0;	
			
			//Fundus
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}
			
			$cQry = "select 
					wnlConj, ".					
					"wnlConjOd,wnlConjOs, ".					
					"posConj, ".					
					"conjunctiva_od_summary,conjunctiva_os_summary, ".					
					"statusElem, uid, wnl_value_Conjunctiva, 
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
			$elem_wnlConjOd = $wnlConjOd;
			$elem_wnlConjOs = $wnlConjOs;
			$elem_wnlConj = $wnlConj;
		
			$oWv = new WorkView();
			
			if(in_array("Conjunctiva",$arrTempProc)||in_array("All",$arrTempProc)){
			if((!empty($statusElem)&&strpos($statusElem,"0")===false)||
				(empty($conjunctiva_od_summary) && empty($elem_wnlConjOd))||
				(empty($conjunctiva_os_summary) && empty($elem_wnlConjOs))
				){
				
			//Toggle Conj
			list($elem_wnlConjOd,$elem_wnlConjOs,$elem_wnlConj) =
									$oWv->toggleWNL($posConj,$conjunctiva_od_summary,$conjunctiva_os_summary,
													$elem_wnlConjOd,$elem_wnlConjOs,$elem_wnlConj,$exmEye);
			}										
			}
			
			//Status
			$statusElem_SLE_prev=$statusElem;
			$statusElem = $this->setEyeStatus($w, $exmEye,$statusElem,0);
			
			if(empty($wnl_value_Conjunctiva)){
				$wnl_value_Conjunctiva=$this->getExamWnlStr("Conjunctiva",$patientId, $form_id);
				$wnl_value_Conjunctiva_phrase = ", wnl_value_Conjunctiva='".sqlEscStr($wnl_value_Conjunctiva)."' ";				
			}
			
			//Fundus
			$sql = "UPDATE ".$this->tbl." SET  
				  wnlConj='".$elem_wnlConj."', ".
				  "wnlConjOd='".$elem_wnlConjOd."',
				  wnlConjOs='".$elem_wnlConjOs."',				  
				  ".
				  " exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				  statusElem='".$statusElem."'
				  ".				  
				  "				  
				  ";			
			$sql .= " ".$wnl_value_Conjunctiva_phrase." ";
			
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
					$ignoreFlds .= "conjunctiva_od_summary,
								conjunctiva_od,
								wnlConjOd,
								ncConj_od,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "conjunctiva_os,conjunctiva_os_summary,
								wnlConjOs,
								ncConj_os,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posConj,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Conjunctiva,ut_elem,";}
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
						"ncConj,".
						"ncConj_od,".
						"ncConj_os,".
						"wnl_value_Conj";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}
	}
	
	function isNoChanged(){
		$res= $this->getRecord("ncConj,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncConj"]) ){
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
		$elem_ncConj="0";			
		$elem_ncConj_od="0";			
		$elem_ncConj_os="0";
		
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
				$elem_ncConj="1";
				$elem_ncConj_od="1";
				$elem_ncConj_os="1";
			}else if($exmEye=="OD"){				
				$elem_ncConj_od="1";
			}else if($exmEye=="OS"){
				$elem_ncConj_os="1";
			}
		}			
		// ---
		
		//Get status string --
		$statusElem="";
		if($elem_ncConj_od==1||$elem_ncConj_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncConj_od,$elem_ncConj_os,0);}
		//Get status--		
		
		//
		$sql = "UPDATE ".$this->tbl."
			  SET			  
			  ncConj = '".$elem_ncConj."',
			  ncConj_od='".$elem_ncConj_od."', ncConj_os='".$elem_ncConj_os."',			  
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}
	
	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "conjunctiva_od";
		$arr["db"]["xmlOs"] = "conjunctiva_os";
		$arr["db"]["wnlSE"] = "wnlConj";
		$arr["db"]["wnlOd"] = "wnlConjOd";
		$arr["db"]["wnlOs"] = "wnlConjOs";
		$arr["db"]["posSE"] = "posConj";
		$arr["db"]["summOd"] = "conjunctiva_od_summary";
		$arr["db"]["summOs"] = "conjunctiva_os_summary";
		$arr["divSe"] = "1";
		
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