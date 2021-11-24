<?php

class Iris extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_iris";
		$this->examName="Iris";
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
			$ar_ret["elem_sleId_LF"]=$ar_ret["elem_sleId"]=$id;
			$ar_ret["elem_editModeIris"] = $elem_editMode;
			//$elem_formId=$form_id;
			//$elem_patientId=$patient_id;
			//NC
			if($elem_editMode==1){
				$ar_ret["elem_ncIris"] = $ncIris;
				$ar_ret["elem_examDate"]=$exam_date;
			}
			
			$iris_od= stripslashes($iris_pupil_od);
			$iris_os= stripslashes($iris_pupil_os);
			
			$arr_vals_iris_od = $oExamXml->extractXmlValue($iris_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_iris_od);
			$arr_vals_iris_os = $oExamXml->extractXmlValue($iris_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_iris_os);
			
			$ar_ret["elem_wnlIris"] = $wnlIris;
			$ar_ret["elem_posIris"] = $posIris;
			$ar_ret["elem_wnlIrisOd"] = $wnlIrisOd;
			$ar_ret["elem_wnlIrisOs"] = $wnlIrisOs;
			$ar_ret["elem_statusElementsIris"] = ($elem_editMode==0) ? "" : $statusElem;
			$ar_ret["elem_penLight"]=$pen_light;
			
			//UT Elems //($elem_editMode==1) ?
			$ar_ret["elem_utElemsIris"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;  
			
			$ar_ret["iris_pupil_od_summary"] = $iris_pupil_od_summary;
			$ar_ret["iris_pupil_os_summary"] = $iris_pupil_os_summary;			
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
		$tmpd = "elem_chng_div4_Od";
		$tmps = "elem_chng_div4_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = $$tmpd;
		$arrSe[$tmps] = $$tmps;		
		
		//
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);	
		
		//iris ------------
		$wnlIrisOd = $wnlIrisOs = $wnlIris = $posIris = $ncIris = "0";
		//if(!empty($elem_chng_div4_Od) || !empty($elem_chng_div4_Os)){
			//if(!empty($elem_chng_div4_Od)){
				$menuName = "irisOd";
				$menuFilePath = $arXmlFiles["iris"]["od"]; //dirname(__FILE__)."/xml/iris_od.xml";
				$elem_iris_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlIrisOd = $_POST["elem_wnlIrisOd"];
			//}
			//if(!empty($elem_chng_div4_Os)){
				$menuName = "irisOs";
				$menuFilePath = $arXmlFiles["iris"]["os"]; //dirname(__FILE__)."/xml/iris_os.xml";
				$elem_iris_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlIrisOs = $_POST["elem_wnlIrisOs"];
			//}

			$wnlIris = (!empty($wnlIrisOd) && !empty($wnlIrisOs)) ? "1" : "0"; //  $_POST["elem_wnlIris"];
			$posIris = $_POST["elem_posIris"];
			$ncIris = $_POST["elem_ncIris"];
		//}
		//iris ------------

		//SLE ------------
		$examDate = wv_dt("now");
		$pen_light = $_POST["elem_penLight"];
		
		//SLE ------------
		
		//
		$oUserAp = new UserAp();
		
		//Summary --
		$strExamsAllOd = $strExamsAllOs = "";
		$iris_pupil_od_summary = "";
		$iris_pupil_os_summary = "";
		
		$iris_pupil_od = $elem_iris_od; //IrisOd
		
		$arrTemp = $this->getExamSummary($iris_pupil_od);
		$iris_pupil_od_summary = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div4_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Iris",$arrExmDone_od,$iris_pupil_od_summary);
		}
		$iris_pupil_os = $elem_iris_os; //IrisOs
		
		$arrTemp = $this->getExamSummary($iris_pupil_os);
		$iris_pupil_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div4_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Iris",$arrExmDone_os,$iris_pupil_os_summary);
		}		
		
		//Summary --
		
		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsIris"];
		$elem_utElems_cur = $_POST["elem_utElemsIris_cur"];
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

			$elem_iris_od = sqlEscStr($elem_iris_od);
			$elem_iris_os = sqlEscStr($elem_iris_os);
			
			//check
			$cQry = "select 
					id,last_opr_id,uid,					
					iris_pupil_od_summary, iris_pupil_os_summary, wnlIrisOd, wnlIrisOs, modi_note_IrisArr, wnl_value_Iris,
					exam_date				
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			
			if($row == false){
				$elem_editMode = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$irisId = $irisIDExam = $row["id"];
				$elem_editMode = "1";
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				//Modifying Notes----------------
				
				$seri_modi_note_IrisArr = $owv->getModiNotesArr($row["iris_pupil_od_summary"],$iris_pupil_od_summary,$last_opr_id,'OD',$row["modi_note_IrisArr"],$row['exam_date']);
				$seri_modi_note_IrisArr = $owv->getModiNotesArr($row["iris_pupil_os_summary"],$iris_pupil_os_summary,$last_opr_id,'OS',$seri_modi_note_IrisArr,$row['exam_date']);
				
				//Modifying Notes----------------					
			}
			
			//
			$sql_con = "
				iris_pupil_od = '".$elem_iris_od."',
				iris_pupil_os = '".$elem_iris_os."',
				iris_pupil_od_summary = '".$iris_pupil_od_summary."',
				iris_pupil_os_summary = '".$iris_pupil_os_summary."',
				wnlIris='".$wnlIris."',
				posIris='".$posIris."',
				ncIris='".$ncIris."',
				wnlIrisOd='".$wnlIrisOd."',
				wnlIrisOs='".$wnlIrisOs."',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				pen_light = '".$pen_light."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_IrisArr = '".sqlEscStr($seri_modi_note_IrisArr)."'
			";
			
			if($elem_editMode == "0"){
				//
				$wnl_value_Iris = $this->getExamWnlStr("Iris & Pupil");
				
				// Insert
				$sql1 = "insert into ".$this->tbl."
							SET							
							form_id = '".$elem_formId."',
							patient_id='".$patientid."',
							exam_date='".$examDate."',							
							wnl_value_Iris='".sqlEscStr($wnl_value_Iris)."',
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
				$insertId = $irisIds;					
				
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
		
		//			
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
			"c10.posIris, ".
			"c10.wnlIris, ".
			"c10.ncIris, ".
			"c10.iris_pupil_od_summary, ".
			"c10.iris_pupil_os_summary,".
			"c10.id, ".
			"c10.wnlIrisOd,c10.wnlIrisOs,".
			"c10.wnl_value_Iris, ".			
			"c10.pen_light, c10.exam_date, ".
			"c10.statusElem AS se_sle, c10.modi_note_IrisArr,
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
			$elem_posIris = assignZero($row["posIris"]);
			$elem_wnlIris = assignZero($row["wnlIris"]);
			$elem_ncIris = assignZero($row["ncIris"]);
			$elem_wnlIrisOd = assignZero($row["wnlIrisOd"]);
			$elem_wnlIrisOs = assignZero($row["wnlIrisOs"]);
			$elem_se_iris = $row["se_sle"];			
			$elem_wnl_value_Iris = $row["wnl_value_Iris"];
			
			$elem_iris_pupil_od_summary = $row["iris_pupil_od_summary"];
			$elem_iris_pupil_os_summary = $row["iris_pupil_os_summary"];
			
			$elem_iris_id = $row["id"];
			$elem_pen_light = $row["pen_light"];
			$examdate = wv_formatDate($row["exam_date"]);			
			$modi_note_IrisArr = unserialize($row["modi_note_IrisArr"]);			
			$arrHx = array();
			if(is_array($modi_note_IrisArr) && count($modi_note_IrisArr)>0 && $row["modi_note_IrisArr"]!='')
			$arrHx['Iris & Pupil'] = $modi_note_IrisArr;
			
		}

		//Previous 
		if(empty($elem_iris_id)){

			$tmp = "";			
			$tmp .= " c2.posIris, ";
			$tmp .= " c2.wnlIris, ";			
			$tmp .= " c2.wnlIrisOd,c2.wnlIrisOs, ";
			$tmp .= " c2.ncIris, ";			
			$tmp .= " c2.iris_pupil_od_summary,  ";
			$tmp .= " c2.wnl_value_Iris,  ";
			$tmp .= " c2.iris_pupil_os_summary, c2.exam_date,
						c2.pen_light, c2.id, 
						c2.statusElem AS se_sle ";
			
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordSle($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				$elem_iris_pupil_od_summary = $row["iris_pupil_od_summary"];
				$elem_iris_pupil_os_summary = $row["iris_pupil_os_summary"];
				$elem_posIris = assignZero($row["posIris"]);
				$elem_wnlIris = assignZero($row["wnlIris"]);
				$elem_ncIris = assignZero($row["ncIris"]);
				$elem_wnlIrisOd = assignZero($row["wnlIrisOd"]);
				$elem_wnlIrisOs = assignZero($row["wnlIrisOs"]);
				$elem_wnl_value_Iris = $row["wnl_value_Iris"];
				$elem_pen_light = $row["pen_light"];
				$examdate = wv_formatDate($row["exam_date"]);
				$elem_se_iris_prev = $row["se_sle"];
				
			}
			//BG
			$bgColor_SLE = "bgSmoke";
		}
		
		//---------

		//is Change is made in new chart -----
		$flgSe_Iris_Od = $flgSe_Iris_Os = "0";
		if(!isset($bgColor_SLE)){
			if(!empty($elem_se_iris)){
				$tmpArrSe = $this->se_elemStatus("SLE","0",$elem_se_iris);
				$flgSe_Iris_Od = $tmpArrSe["4"]["od"];
				$flgSe_Iris_Os = $tmpArrSe["4"]["os"];				
			}
		}else{
			if(!empty($elem_se_iris_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("SLE","0",$elem_se_iris_prev);
				$flgSe_Iris_Od_prev = $tmpArrSe_prev["4"]["od"];
				$flgSe_Iris_Os_prev = $tmpArrSe_prev["4"]["os"];
				
			}
		}
		//is Change is made in new chart -----		

		//WNL

		//Iris & Pupil --
		$wnlString_Iris = !empty($elem_wnl_value_Iris) ? $elem_wnl_value_Iris : $this->getExamWnlStr("Iris & Pupil");
		$wnlStringOd_Iris = $wnlStringOs_Iris = $wnlString_Iris; 
		
		if(empty($flgSe_Iris_Od) && empty($flgSe_Iris_Od_prev) && !empty($elem_wnlIrisOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Iris & Pupil", "OD"); if(!empty($tmp)){ $wnlStringOd_Iris = $tmp;}  }
		if(empty($flgSe_Iris_Os) && empty($flgSe_Iris_Os_prev) && !empty($elem_wnlIrisOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Iris & Pupil", "OS"); if(!empty($tmp)){ $wnlStringOs_Iris = $tmp;}  }
		
		list($elem_iris_pupil_od_summary,$elem_iris_pupil_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Iris,"wValOs"=>$wnlStringOs_Iris,
																	"wOd"=>$elem_wnlIrisOd,"sOd"=>$elem_iris_pupil_od_summary,
																	"wOs"=>$elem_wnlIrisOs,"sOs"=>$elem_iris_pupil_os_summary));

		//Nochanged
		if(!empty($elem_se_iris)&&strpos($elem_se_iris,"=1")!==false){
			$elem_noChangeIris=1;
		}
		
		//Archived SLE --
		if($bgColor_SLE != "bgSmoke"){
		
		$arrDivArcCmn=array();
		$oChartRecArc->setChkTbl($this->tbl);
		$arrInpArc = array( "elem_sumOdIris"=>array("iris_pupil_od_summary",$elem_iris_pupil_od_summary,"smof","wnlIrisOd",$wnlString_Iris,$modi_note_IrisOd),
			"elem_sumOsIris"=>array("iris_pupil_os_summary",$elem_iris_pupil_os_summary,"smof","wnlIrisOs",$wnlString_Iris,$modi_note_IrisOs)			
							);
		$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);

		//Iris --
		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdIris"])){
			//echo $arTmpRecArc["div"]["elem_sumOdIris"];
			$arrDivArcCmn["Iris & Pupil"]["OD"]=$arTmpRecArc["div"]["elem_sumOdIris"];
			$moeArc["od"]["Iris"] = $arTmpRecArc["js"]["elem_sumOdIris"];
			$flgArcColor["od"]["Iris"] = $arTmpRecArc["css"]["elem_sumOdIris"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdIris"])) 
				$elem_iris_pupil_od_summary = $arTmpRecArc["curText"]["elem_sumOdIris"];
		}else{
			$moeArc["od"]["Iris"] = $flgArcColor["od"]["Iris"]="";
		}
		//OS
		if(!empty($arTmpRecArc["div"]["elem_sumOsIris"])){
			//echo $arTmpRecArc["div"]["elem_sumOsIris"];
			$arrDivArcCmn["Iris & Pupil"]["OS"]=$arTmpRecArc["div"]["elem_sumOsIris"];
			$moeArc["os"]["Iris"] = $arTmpRecArc["js"]["elem_sumOsIris"];
			$flgArcColor["os"]["Iris"] = $arTmpRecArc["css"]["elem_sumOsIris"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOsIris"])) 
				$elem_iris_pupil_os_summary = $arTmpRecArc["curText"]["elem_sumOsIris"];
		}else{
			$moeArc["os"]["Iris"] = $flgArcColor["os"]["Iris"]="";
		}
		//Iris --	
		
		}//
		
		//Archived SLE --		
		
		$arr=array();
		
		//if(in_array("Iris & Pupil",$arrTempProc) || in_array("All",$arrTempProc)){	
		//Iris & Pupil
		$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Iris & Pupil",
											"sOd"=>$elem_iris_pupil_od_summary,"sOs"=>$elem_iris_pupil_os_summary,
											"fOd"=>$flgSe_Iris_Od,"fOs"=>$flgSe_Iris_Os,"pos"=>$elem_posIris,
											//"arcJsOd"=>$moeArc["od"]["Iris"],"arcJsOs"=>$moeArc["os"]["Iris"],
											"arcCssOd"=>$flgArcColor["od"]["Iris"],"arcCssOs"=>$flgArcColor["os"]["Iris"],
											//"mnOd"=>$moeMN["od"]["Iris"],"mnOs"=>$moeMN["os"]["Iris"],
											"enm_2"=>"Iris"));
		//}		
		
		//Sub Exam List
		$arr["seList"] = 	array("Iris"=>array("enm"=>"Iris & Pupil","pos"=>$elem_posIris,
						"wOd"=>$elem_wnlIrisOd,"wOs"=>$elem_wnlIrisOs)					
					);
		$arr["nochange"] = $elem_noChangeIris;		
		$arr["bgColor"] = "".$bgColor_SLE;
		$arr["penLight"] = $elem_pen_light;		
		$arr["examdate"] = $examdate;		
		$arr["moeMN"] = $moeMN;			
		$arr["exm_flg_se"] = array($flgSe_Iris_Od,$flgSe_Iris_Os);
		
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
			"c10.posIris, ".
			"c10.wnlIris, ".
			"c10.ncIris, ".
			"c10.iris_pupil_od_summary, ".
			"c10.iris_pupil_os_summary,".
			"c10.id, ".
			"c10.wnlIrisOd,c10.wnlIrisOs,".
			"c10.wnl_value_Iris, ".			
			"c10.pen_light, c10.exam_date, ".
			"c10.statusElem AS se_sle, c10.modi_note_IrisArr,
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
			$elem_posIris = assignZero($row["posIris"]);
			$elem_wnlIris = assignZero($row["wnlIris"]);
			$elem_ncIris = assignZero($row["ncIris"]);
			$elem_wnlIrisOd = assignZero($row["wnlIrisOd"]);
			$elem_wnlIrisOs = assignZero($row["wnlIrisOs"]);
			$elem_se_iris = $row["se_sle"];			
			$elem_wnl_value_Iris = $row["wnl_value_Iris"];
			
			$elem_iris_pupil_od_summary = $row["iris_pupil_od_summary"];
			$elem_iris_pupil_os_summary = $row["iris_pupil_os_summary"];
			
			$elem_iris_id = $row["id"];
			$elem_pen_light = $row["pen_light"];
			$examdate = wv_formatDate($row["exam_date"]);			
			$modi_note_IrisArr = unserialize($row["modi_note_IrisArr"]);			
			$arrHx = array();
			if(count($modi_note_IrisArr)>0 && $row["modi_note_IrisArr"]!='')
			$arrHx['Iris & Pupil'] = $modi_note_IrisArr;
			
			//is Change is made in new chart -----
			$flgSe_Iris_Od = $flgSe_Iris_Os = "0";
			if(!isset($bgColor_SLE)){
				if(!empty($elem_se_iris)){
					$tmpArrSe = $this->se_elemStatus("SLE","0",$elem_se_iris);
					$flgSe_Iris_Od = $tmpArrSe["4"]["od"];
					$flgSe_Iris_Os = $tmpArrSe["4"]["os"];				
				}
			}else{
				if(!empty($elem_se_iris_prev)){
					$tmpArrSe_prev = $this->se_elemStatus("SLE","0",$elem_se_iris_prev);
					$flgSe_Iris_Od_prev = $tmpArrSe_prev["4"]["od"];
					$flgSe_Iris_Os_prev = $tmpArrSe_prev["4"]["os"];
					
				}
			}
			//is Change is made in new chart -----		

			//WNL

			//Iris & Pupil --
			$wnlString_Iris = !empty($elem_wnl_value_Iris) ? $elem_wnl_value_Iris : $this->getExamWnlStr("Iris & Pupil");
			$wnlStringOd_Iris = $wnlStringOs_Iris = $wnlString_Iris; 
			
			if(empty($flgSe_Iris_Od) && empty($flgSe_Iris_Od_prev) && !empty($elem_wnlIrisOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Iris & Pupil", "OD"); if(!empty($tmp)){ $wnlStringOd_Iris = $tmp;}  }
			if(empty($flgSe_Iris_Os) && empty($flgSe_Iris_Os_prev) && !empty($elem_wnlIrisOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Iris & Pupil", "OS"); if(!empty($tmp)){ $wnlStringOs_Iris = $tmp;}  }
			
			list($elem_iris_pupil_od_summary,$elem_iris_pupil_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Iris,"wValOs"=>$wnlStringOs_Iris,
																		"wOd"=>$elem_wnlIrisOd,"sOd"=>$elem_iris_pupil_od_summary,
																		"wOs"=>$elem_wnlIrisOs,"sOs"=>$elem_iris_pupil_os_summary));

			//Nochanged
			if(!empty($elem_se_iris)&&strpos($elem_se_iris,"=1")!==false){
				$elem_noChangeIris=1;
			}
			
			$arr=array();
			
			if(in_array("Iris & Pupil",$arrTempProc) || in_array("All",$arrTempProc)){	
			//Iris & Pupil
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Iris & Pupil",
												"sOd"=>$elem_iris_pupil_od_summary,"sOs"=>$elem_iris_pupil_os_summary,
												"fOd"=>$flgSe_Iris_Od,"fOs"=>$flgSe_Iris_Os,"pos"=>$elem_posIris,
												//"arcJsOd"=>$moeArc["od"]["Iris"],"arcJsOs"=>$moeArc["os"]["Iris"],
												"arcCssOd"=>$flgArcColor["od"]["Iris"],"arcCssOs"=>$flgArcColor["os"]["Iris"],
												//"mnOd"=>$moeMN["od"]["Iris"],"mnOs"=>$moeMN["os"]["Iris"],
												"enm_2"=>"Iris"));
			}		
			
			//Sub Exam List
			$arr["seList"] = 	array("Iris"=>array("enm"=>"Iris & Pupil","pos"=>$elem_posIris,
							"wOd"=>$elem_wnlIrisOd,"wOs"=>$elem_wnlIrisOs)					
						);
			$arr["nochange"] = $elem_noChangeIris;		
			$arr["bgColor"] = "".$bgColor_SLE;
			$arr["penLight"] = $elem_pen_light;		
			$arr["examdate"] = $examdate;		
			$arr["moeMN"] = $moeMN;			
			$arr["exm_flg_se"] = array($flgSe_Iris_Od,$flgSe_Iris_Os);
			
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
		$wnl_value_Iris = $this->getExamWnlStr("Iris & Pupil");		
		
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_Iris)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_Iris)."') ";
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
					"ncIris,".
					"ncIris_od,".
					"ncIris_os,".
					"wnl_value_Iris";
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
			$wnl_value_Iris="";
			$wnl_value_Iris_phrase="";
			$flgCarry=0;	
			
			//Fundus
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}
			
			$cQry = "select 
					wnlIris, ".					
					"wnlIrisOd,wnlIrisOs,
					".					
					"posIris, ".					
					"iris_pupil_od_summary,	iris_pupil_os_summary,						
					".					
					"statusElem, uid, wnl_value_Iris, 
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
			$elem_wnlIrisOd = $wnlIrisOd;
			$elem_wnlIrisOs = $wnlIrisOs;
			$elem_wnlIris = $wnlIris;
		
			$oWv = new WorkView();
			
			if(in_array("Iris & Pupil",$arrTempProc)||in_array("All",$arrTempProc)){
			if((!empty($statusElem)&&strpos($statusElem,"0")===false)||
				(empty($iris_pupil_od_summary) && empty($elem_wnlIrisOd))||
				(empty($iris_pupil_os_summary) && empty($elem_wnlIrisOs))
				){
			//Toggle Iris
			list($elem_wnlIrisOd,$elem_wnlIrisOs,$elem_wnlIris) =
									$oWv->toggleWNL($posIris,$iris_pupil_od_summary,$iris_pupil_os_summary,
													$elem_wnlIrisOd,$elem_wnlIrisOs,$elem_wnlIris,$exmEye);
			}
			}
			
			//Status
			$statusElem_prev=$statusElem;
			$statusElem = $this->setEyeStatus($w, $exmEye,$statusElem,0);
			
			if(empty($wnl_value_Iris)){
				$wnl_value_Iris=$this->getExamWnlStr("Iris");
				$wnl_value_Iris_phrase = ", wnl_value_Iris='".sqlEscStr($wnl_value_Iris)."' ";
			}
			
			//Fundus
			$sql = "UPDATE ".$this->tbl." SET  
				  wnlIris='".$elem_wnlIris."', ".
				  "wnlIrisOd='".$elem_wnlIrisOd."',
				  wnlIrisOs='".$elem_wnlIrisOs."',				  
				  ".
				  " exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				  statusElem='".$statusElem."'
				  ".				  
				  "				  
				  ";			
			$sql .= " ".$wnl_value_Iris_phrase." ";
			
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
					$ignoreFlds .= "iris_pupil_od_summary,
								iris_pupil_od,
								wnlIrisOd,
								ncIris_od,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "iris_pupil_os,iris_pupil_os_summary,
								wnlIrisOs,
								ncIris_os,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posIris,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Iris,ut_elem,";}
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
						"ncIris,".
						"ncIris_od,".
						"ncIris_os,".
						"wnl_value_Iris";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}
	}
	
	function isNoChanged(){
		$res= $this->getRecord("ncIris,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncIris"]) ){
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
		$elem_ncIris="0";			
		$elem_ncIris_od="0";			
		$elem_ncIris_os="0";
		
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
				$elem_ncIris="1";
				$elem_ncIris_od="1";
				$elem_ncIris_os="1";
			}else if($exmEye=="OD"){				
				$elem_ncIris_od="1";
			}else if($exmEye=="OS"){
				$elem_ncIris_os="1";
			}
		}			
		// ---
		
		//Get status string --
		$statusElem="";
		if($elem_ncIris_od==1||$elem_ncIris_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncIris_od,$elem_ncIris_os,0);}
		//Get status--		
		
		//
		$sql = "UPDATE ".$this->tbl."
			  SET			  
			  ncIris = '".$elem_ncIris."',
			  ncIris_od='".$elem_ncIris_od."', ncIris_os='".$elem_ncIris_os."',			  
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}
	
	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "iris_pupil_od";
		$arr["db"]["xmlOs"] = "iris_pupil_os";
		$arr["db"]["wnlSE"] = "wnlIris";
		$arr["db"]["wnlOd"] = "wnlIrisOd";
		$arr["db"]["wnlOs"] = "wnlIrisOs";
		$arr["db"]["posSE"] = "posIris";
		$arr["db"]["summOd"] = "iris_pupil_od_summary";
		$arr["db"]["summOs"] = "iris_pupil_os_summary";
		$arr["divSe"] = "4";
		
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