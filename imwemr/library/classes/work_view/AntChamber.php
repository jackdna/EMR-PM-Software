<?php

class AntChamber extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_ant_chamber";
		$this->examName="AntChamber";
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
			$ar_ret["elem_antId_LF"]=$ar_ret["elem_antId"]=$id;
			$ar_ret["elem_editModeAnt"] = $elem_editMode;
			//$elem_formId=$form_id;
			//$elem_patientId=$patient_id;
			//NC
			if($elem_editMode==1){
				$ar_ret["elem_ncAnt"] = $ncAnt;
				$ar_ret["elem_examDate"]=$exam_date;
			}
			
			$anf_chamber_od= stripslashes($anf_chamber_od);
			$anf_chamber_os= stripslashes($anf_chamber_os);
			
			$arr_vals_anf_chamber_od = $oExamXml->extractXmlValue($anf_chamber_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_anf_chamber_od);
			$arr_vals_anf_chamber_os = $oExamXml->extractXmlValue($anf_chamber_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_anf_chamber_os);
			
			$ar_ret["elem_wnlAnt"] = $wnlAnt;
			$ar_ret["elem_posAnt"] = $posAnt;
			$ar_ret["elem_wnlAntOd"] = $wnlAntOd;
			$ar_ret["elem_wnlAntOs"] = $wnlAntOs;
			$ar_ret["elem_statusElementsAnt"] = ($elem_editMode==0) ? "" : $statusElem;
			$ar_ret["elem_penLight"]=$pen_light;
			
			//UT Elems //($elem_editMode==1) ?
			$ar_ret["elem_utElemsAnt"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;  
			
			$ar_ret["anf_chamber_od_summary"]=$anf_chamber_od_summary;
			$ar_ret["anf_chamber_os_summary"]=$anf_chamber_os_summary;
			
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
		$tmpd = "elem_chng_div3_Od";
		$tmps = "elem_chng_div3_Os";
		$$tmpd = $_POST[$tmpd];
		$$tmps = $_POST[$tmps];
		$arrSe[$tmpd] = $$tmpd;
		$arrSe[$tmps] = $$tmps;		
		
		//
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);		

		//antChamber ------------
		$wnlAntOd = $wnlAntOs = $wnlAnt = $posAnt = $ncAnt = "0";
		//if(!empty($elem_chng_div3_Od) || !empty($elem_chng_div3_Os)){
			//if(!empty($elem_chng_div2_Od)){
				$menuName = "antChamberOd";
				$menuFilePath = $arXmlFiles["antChamber"]["od"]; //dirname(__FILE__)."/xml/antChamber_od.xml";
				$elem_antChamber_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlAntOd = $_POST["elem_wnlAntOd"];
			//}

			//if(!empty($elem_chng_div2_Os)){
				$menuName = "antChamberOs";
				$menuFilePath = $arXmlFiles["antChamber"]["os"]; //dirname(__FILE__)."/xml/antChamber_os.xml";
				$elem_antChamber_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlAntOs = $_POST["elem_wnlAntOs"];
			//}

			$wnlAnt = (!empty($wnlAntOd) && !empty($wnlAntOs)) ? "1" : "0"; // $_POST["elem_wnlAnt"];
			$posAnt = $_POST["elem_posAnt"];
			$ncAnt = $_POST["elem_ncAnt"];
		//}

		//antChamber ------------		
		
		//SLE ------------
		$examDate = wv_dt("now");
		$pen_light = $_POST["elem_penLight"];		
		
		//
		$oUserAp = new UserAp();
		
		//Summary --
		$strExamsAllOd = $strExamsAllOs = "";
		$anf_chamber_od_summary = "";
		$anf_chamber_os_summary = "";
		
		$anf_chamber_od = $elem_antChamber_od; // Ant Od
		
		$arrTemp = $this->getExamSummary($anf_chamber_od);
		$anf_chamber_od_summary = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div3_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Ant. Chamber",$arrExmDone_od,$anf_chamber_od_summary);
		}
		$anf_chamber_os = $elem_antChamber_os; //Ant Os
		
		$arrTemp = $this->getExamSummary($anf_chamber_os);
		$anf_chamber_os_summary = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div3_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Ant. Chamber",$arrExmDone_os,$anf_chamber_os_summary);
		}
		
		//Summary --
		
		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsAnt"];
		$elem_utElems_cur = $_POST["elem_utElemsAnt_cur"];
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

			$elem_antChamber_od = sqlEscStr($elem_antChamber_od);
			$elem_antChamber_os = sqlEscStr($elem_antChamber_os);			
			
			//check
			$cQry = "select 
					id,last_opr_id,uid,
					anf_chamber_od_summary, anf_chamber_os_summary, wnlAntOd, wnlAntOs, modi_note_AntArr, wnl_value_Ant,
					exam_date				
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			
			if($row == false){
				$elem_editMode = "0";
				$last_opr_id = $_SESSION["authId"];
			}else{
				$antId = $antIDExam = $row["id"];
				$elem_editMode = "1";
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				//Modifying Notes----------------
				$seri_modi_note_AntArr = $owv->getModiNotesArr($row["anf_chamber_od_summary"],$anf_chamber_od_summary,$last_opr_id,'OD',$row["modi_note_AntArr"],$row['exam_date']);
				$seri_modi_note_AntArr = $owv->getModiNotesArr($row["anf_chamber_os_summary"],$anf_chamber_os_summary,$last_opr_id,'OS',$seri_modi_note_AntArr,$row['exam_date']);
				
				//Modifying Notes----------------					
			}
			
			//
			$sql_con = "
				anf_chamber_od = '".$elem_antChamber_od."',
				anf_chamber_os = '".$elem_antChamber_os."',
				anf_chamber_od_summary = '".$anf_chamber_od_summary."',
				anf_chamber_os_summary = '".$anf_chamber_os_summary."',
				wnlAnt='".$wnlAnt."',
				posAnt='".$posAnt."',
				ncAnt='".$ncAnt."',
				wnlAntOd='".$wnlAntOd."',
				wnlAntOs='".$wnlAntOs."',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				pen_light = '".$pen_light."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_AntArr = '".sqlEscStr($seri_modi_note_AntArr)."'
				";
			
			
			if($elem_editMode == "0"){
				//				
				$wnl_value_Ant = $this->getExamWnlStr("Ant. Chamber");				
			
				// Insert
				$sql1 = "insert into ".$this->tbl."
					SET
					form_id = '".$elem_formId."',
					patient_id='".$patientid."',
					exam_date='".$examDate."',
					wnl_value_Ant='".sqlEscStr($wnl_value_Ant)."',
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
				$insertId = $antId;					
				
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
			"c10.posAnt,".
			"c10.wnlAnt,".
			"c10.ncAnt,".
			"c10.anf_chamber_od_summary, ".
			"c10.anf_chamber_os_summary, ".
			"c10.id, ".
			"c10.wnlAntOd,c10.wnlAntOs, ".
			"c10.wnl_value_Ant, ".
			"c10.pen_light, c10.exam_date, ".
			"c10.statusElem AS se_sle, c10.modi_note_AntArr, 
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
			$elem_posAnt = assignZero($row["posAnt"]);
			$elem_wnlAnt = assignZero($row["wnlAnt"]);
			$elem_ncAnt = assignZero($row["ncAnt"]);
			$elem_wnlAntOd = assignZero($row["wnlAntOd"]);
			$elem_wnlAntOs = assignZero($row["wnlAntOs"]);
			$elem_se_ant = $row["se_sle"];			
			$elem_wnl_value_Ant = $row["wnl_value_Ant"];
			$elem_anf_chamber_od_summary = $row["anf_chamber_od_summary"];
			$elem_anf_chamber_os_summary = $row["anf_chamber_os_summary"];
			$elem_ant_id = $row["id"];
			$elem_pen_light = $row["pen_light"];
			$examdate = wv_formatDate($row["exam_date"]);
			$modi_note_AntArr = unserialize($row["modi_note_AntArr"]);
			$arrHx = array();
			if(is_array($modi_note_AntArr) && count($modi_note_AntArr)>0 && $row["modi_note_AntArr"]!='')
			$arrHx['Ant Chamber'] = $modi_note_AntArr;
		}

		//Previous 
		if(empty($elem_ant_id)){

			$tmp = "";			
			$tmp .= " c2.posAnt, ";
			$tmp .= " c2.wnlAnt, ";
			$tmp .= " c2.wnlAntOd,c2.wnlAntOs, ";
			$tmp .= " c2.ncAnt, ";
			$tmp .= " c2.anf_chamber_od_summary,  ";
			$tmp .= " c2.wnl_value_Ant, ";
			$tmp .= " c2.anf_chamber_os_summary, c2.exam_date,
						c2.pen_light, c2.id, 
						c2.statusElem AS se_sle ";
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordSle($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				$elem_anf_chamber_od_summary = $row["anf_chamber_od_summary"];
				$elem_anf_chamber_os_summary = $row["anf_chamber_os_summary"];
				$elem_posAnt = assignZero($row["posAnt"]);
				$elem_wnlAnt = assignZero($row["wnlAnt"]);
				$elem_ncAnt = assignZero($row["ncAnt"]);
				$elem_wnlAntOd = assignZero($row["wnlAntOd"]);
				$elem_wnlAntOs = assignZero($row["wnlAntOs"]);
				$elem_wnl_value_Ant = $row["wnl_value_Ant"];
				$elem_pen_light = $row["pen_light"];
				$examdate = wv_formatDate($row["exam_date"]);
				$elem_se_ant_prev = $row["se_sle"];
			}
			//BG
			$bgColor_SLE = "bgSmoke";
		}
		
		//---------

		//is Change is made in new chart -----
		$flgSe_Ant_Od = $flgSe_Ant_Os = "0";
		if(!isset($bgColor_SLE)){
			if(!empty($elem_se_ant)){
				$tmpArrSe = $this->se_elemStatus("SLE","0",$elem_se_ant);
				$flgSe_Ant_Od = $tmpArrSe["3"]["od"];
				$flgSe_Ant_Os = $tmpArrSe["3"]["os"];
			}
		}else{
			if(!empty($elem_se_ant_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("SLE","0",$elem_se_ant_prev);
				$flgSe_Ant_Od_prev = $tmpArrSe_prev["3"]["od"];
				$flgSe_Ant_Os_prev = $tmpArrSe_prev["3"]["os"];				
			}
		}
		//is Change is made in new chart -----

		//echo "<br>".$flgSe_Ant_Od." - ".$flgSe_Ant_Os."<br>";

		//WNL
		
		//Ant. Chamber --
		$wnlString_Ant = !empty($elem_wnl_value_Ant) ? $elem_wnl_value_Ant : $this->getExamWnlStr("Ant. Chamber");
		$wnlStringOd_Ant = $wnlStringOs_Ant = $wnlString_Ant; 
		
		if(empty($flgSe_Ant_Od) && empty($flgSe_Ant_Od_prev) && !empty($elem_wnlAntOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Ant. Chamber", "OD"); if(!empty($tmp)){ $wnlStringOd_Ant = $tmp;}  }
		if(empty($flgSe_Ant_Os) && empty($flgSe_Ant_Os_prev) && !empty($elem_wnlAntOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Ant. Chamber", "OS"); if(!empty($tmp)){ $wnlStringOs_Ant = $tmp;}  }
		
		list($elem_anf_chamber_od_summary,$elem_anf_chamber_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Ant,"wValOs"=>$wnlStringOs_Ant,
																	"wOd"=>$elem_wnlAntOd,"sOd"=>$elem_anf_chamber_od_summary,
																	"wOs"=>$elem_wnlAntOs,"sOs"=>$elem_anf_chamber_os_summary));
		//Nochanged
		if(!empty($elem_se_ant)&&strpos($elem_se_ant,"=1")!==false){
			$elem_noChangeAnt=1;
		}
		
		//Archived SLE --
		if($bgColor_SLE != "bgSmoke"){
		
		$arrDivArcCmn=array();
		$oChartRecArc->setChkTbl($this->tbl);
		$arrInpArc = array(
			"elem_sumOdAnt"=>array("anf_chamber_od_summary",$elem_anf_chamber_od_summary,"smof","wnlAntOd",$wnlString_Ant,$modi_note_AntOd),
			"elem_sumOsAnt"=>array("anf_chamber_os_summary",$elem_anf_chamber_os_summary,"smof","wnlAntOs",$wnlString_Ant,$modi_note_AntOs)
							);
		$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
		//Ant Chamber--
		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdAnt"])){
			//echo $arTmpRecArc["div"]["elem_sumOdAnt"];
			$arrDivArcCmn["Ant. Chamber"]["OD"]=$arTmpRecArc["div"]["elem_sumOdAnt"];
			$moeArc["od"]["Ant"] = $arTmpRecArc["js"]["elem_sumOdAnt"];
			$flgArcColor["od"]["Ant"] = $arTmpRecArc["css"]["elem_sumOdAnt"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdAnt"])) 
				$elem_anf_chamber_od_summary = $arTmpRecArc["curText"]["elem_sumOdAnt"];
		}else{
			$moeArc["od"]["Ant"] = $flgArcColor["od"]["Ant"]="";
		}
		//OS
		if(!empty($arTmpRecArc["div"]["elem_sumOsAnt"])){
			//echo $arTmpRecArc["div"]["elem_sumOsAnt"];
			$arrDivArcCmn["Ant. Chamber"]["OS"]=$arTmpRecArc["div"]["elem_sumOsAnt"];
			$moeArc["os"]["Ant"] = $arTmpRecArc["js"]["elem_sumOsAnt"];
			$flgArcColor["os"]["Ant"] = $arTmpRecArc["css"]["elem_sumOsAnt"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOsAnt"])) 
				$elem_anf_chamber_od_summary = $arTmpRecArc["curText"]["elem_sumOsAnt"];
		}else{
			$moeArc["os"]["Ant"] = $flgArcColor["os"]["Ant"]="";
		}
		//Ant Chamber--
		
		}//
		
		//Archived SLE --
		
		$arr=array();
		
		
		//if(in_array("Ant. Chamber",$arrTempProc) || in_array("All",$arrTempProc)){
		//Ant. Chamber
		$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Ant. Chamber",
											"sOd"=>$elem_anf_chamber_od_summary,"sOs"=>$elem_anf_chamber_os_summary,
											"fOd"=>$flgSe_Ant_Od,"fOs"=>$flgSe_Ant_Os,"pos"=>$elem_posAnt,
											//"arcJsOd"=>$moeArc["od"]["Ant"],"arcJsOs"=>$moeArc["os"]["Ant"],
											"arcCssOd"=>$flgArcColor["od"]["Ant"],"arcCssOs"=>$flgArcColor["os"]["Ant"],
											//"mnOd"=>$moeMN["od"]["Ant"],"mnOs"=>$moeMN["os"]["Ant"],
											"enm_2"=>"Ant"));
		//}									
		
		//Sub Exam List
		$arr["seList"] = 	array(
					"Ant"=>array("enm"=>"Ant. Chamber","pos"=>$elem_posAnt,
						"wOd"=>$elem_wnlAntOd,"wOs"=>$elem_wnlAntOs)
					
					);
		$arr["nochange"] = $elem_noChangeAnt;		
		$arr["bgColor"] = "".$bgColor_SLE;
		$arr["penLight"] = $elem_pen_light;		
		$arr["examdate"] = $examdate;		
		$arr["moeMN"] = $moeMN;			
		$arr["exm_flg_se"] = array($flgSe_Ant_Od,$flgSe_Ant_Os);
		
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
			"c10.posAnt,".
			"c10.wnlAnt,".
			"c10.ncAnt,".
			"c10.anf_chamber_od_summary, ".
			"c10.anf_chamber_os_summary, ".
			"c10.id, ".
			"c10.wnlAntOd,c10.wnlAntOs, ".
			"c10.wnl_value_Ant, ".
			"c10.pen_light, c10.exam_date, ".
			"c10.statusElem AS se_sle, c10.modi_note_AntArr, 
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
			$elem_posAnt = assignZero($row["posAnt"]);
			$elem_wnlAnt = assignZero($row["wnlAnt"]);
			$elem_ncAnt = assignZero($row["ncAnt"]);
			$elem_wnlAntOd = assignZero($row["wnlAntOd"]);
			$elem_wnlAntOs = assignZero($row["wnlAntOs"]);
			$elem_se_ant = $row["se_sle"];			
			$elem_wnl_value_Ant = $row["wnl_value_Ant"];
			$elem_anf_chamber_od_summary = $row["anf_chamber_od_summary"];
			$elem_anf_chamber_os_summary = $row["anf_chamber_os_summary"];
			$elem_ant_id = $row["id"];
			$elem_pen_light = $row["pen_light"];
			$examdate = wv_formatDate($row["exam_date"]);
			$modi_note_AntArr = unserialize($row["modi_note_AntArr"]);
			$arrHx = array();
			if(count($modi_note_AntArr)>0 && $row["modi_note_AntArr"]!='')
			$arrHx['Ant Chamber'] = $modi_note_AntArr;
			
			//is Change is made in new chart -----
			$flgSe_Ant_Od = $flgSe_Ant_Os = "0";
			if(!isset($bgColor_SLE)){
				if(!empty($elem_se_ant)){
					$tmpArrSe = $this->se_elemStatus("SLE","0",$elem_se_ant);
					$flgSe_Ant_Od = $tmpArrSe["3"]["od"];
					$flgSe_Ant_Os = $tmpArrSe["3"]["os"];
				}
			}else{
				if(!empty($elem_se_ant_prev)){
					$tmpArrSe_prev = $this->se_elemStatus("SLE","0",$elem_se_ant_prev);
					$flgSe_Ant_Od_prev = $tmpArrSe_prev["3"]["od"];
					$flgSe_Ant_Os_prev = $tmpArrSe_prev["3"]["os"];				
				}
			}
			//is Change is made in new chart -----

			//echo "<br>".$flgSe_Ant_Od." - ".$flgSe_Ant_Os."<br>";

			//WNL
			
			//Ant. Chamber --
			$wnlString_Ant = !empty($elem_wnl_value_Ant) ? $elem_wnl_value_Ant : $this->getExamWnlStr("Ant. Chamber");
			$wnlStringOd_Ant = $wnlStringOs_Ant = $wnlString_Ant; 
			
			if(empty($flgSe_Ant_Od) && empty($flgSe_Ant_Od_prev) && !empty($elem_wnlAntOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Ant. Chamber", "OD"); if(!empty($tmp)){ $wnlStringOd_Ant = $tmp;}  }
			if(empty($flgSe_Ant_Os) && empty($flgSe_Ant_Os_prev) && !empty($elem_wnlAntOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Ant. Chamber", "OS"); if(!empty($tmp)){ $wnlStringOs_Ant = $tmp;}  }
			
			list($elem_anf_chamber_od_summary,$elem_anf_chamber_os_summary) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Ant,"wValOs"=>$wnlStringOs_Ant,
																		"wOd"=>$elem_wnlAntOd,"sOd"=>$elem_anf_chamber_od_summary,
																		"wOs"=>$elem_wnlAntOs,"sOs"=>$elem_anf_chamber_os_summary));
			//Nochanged
			if(!empty($elem_se_ant)&&strpos($elem_se_ant,"=1")!==false){
				$elem_noChangeAnt=1;
			}
			
			$arr=array();
			
			
			if(in_array("Ant. Chamber",$arrTempProc) || in_array("All",$arrTempProc)){
			//Ant. Chamber
			$arr["subExm"][] = $oOnload->getArrExms_ms(array("enm"=>"Ant. Chamber",
												"sOd"=>$elem_anf_chamber_od_summary,"sOs"=>$elem_anf_chamber_os_summary,
												"fOd"=>$flgSe_Ant_Od,"fOs"=>$flgSe_Ant_Os,"pos"=>$elem_posAnt,
												//"arcJsOd"=>$moeArc["od"]["Ant"],"arcJsOs"=>$moeArc["os"]["Ant"],
												"arcCssOd"=>$flgArcColor["od"]["Ant"],"arcCssOs"=>$flgArcColor["os"]["Ant"],
												//"mnOd"=>$moeMN["od"]["Ant"],"mnOs"=>$moeMN["os"]["Ant"],
												"enm_2"=>"Ant"));
			}									
			
			//Sub Exam List
			$arr["seList"] = 	array(
						"Ant"=>array("enm"=>"Ant. Chamber","pos"=>$elem_posAnt,
							"wOd"=>$elem_wnlAntOd,"wOs"=>$elem_wnlAntOs)
						
						);
			$arr["nochange"] = $elem_noChangeAnt;		
			$arr["bgColor"] = "".$bgColor_SLE;
			$arr["penLight"] = $elem_pen_light;		
			$arr["examdate"] = $examdate;		
			$arr["moeMN"] = $moeMN;			
			$arr["exm_flg_se"] = array($flgSe_Ant_Od,$flgSe_Ant_Os);
			
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
		$wnl_value_Ant = $this->getExamWnlStr("Ant. Chamber");		
		
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_Ant)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_Ant)."') ";
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
					"ncAnt,".
					"ncAnt_od,".
					"ncAnt_os,".
					"wnl_value_Ant";
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
			$wnl_value_Ant="";
			$wnl_value_Ant_phrase="";
			$flgCarry=0;	
			
			//Fundus
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}
			
			$cQry = "select 
					wnlAnt, ".					
					"wnlAntOd,wnlAntOs,
					".					
					"posAnt, ".					
					"anf_chamber_od_summary,anf_chamber_os_summary,						
					".					
					"statusElem, uid, wnl_value_Ant, 
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
			$elem_wnlAntOd = $wnlAntOd;
			$elem_wnlAntOs = $wnlAntOs;
			$elem_wnlAnt = $wnlAnt;
		
			$oWv = new WorkView();
			
			if(in_array("Ant. Chamber",$arrTempProc)||in_array("All",$arrTempProc)){
			if((!empty($statusElem)&&strpos($statusElem,"0")===false)||
				(empty($anf_chamber_od_summary) && empty($elem_wnlAntOd))||
				(empty($anf_chamber_os_summary) && empty($elem_wnlAntOs))
				){
			//Toggle Ant
			list($elem_wnlAntOd,$elem_wnlAntOs,$elem_wnlAnt) =
									$oWv->toggleWNL($posAnt,$anf_chamber_od_summary,$anf_chamber_os_summary,
													$elem_wnlAntOd,$elem_wnlAntOs,$elem_wnlAnt,$exmEye);
			}
			}
			
			//Status
			$statusElem_prev=$statusElem;
			$statusElem = $this->setEyeStatus($w, $exmEye,$statusElem,0);
			
			if(empty($wnl_value_Ant)){
				$wnl_value_Ant=$this->getExamWnlStr("Ant. Chamber");
				$wnl_value_Ant_phrase = ", wnl_value_Ant='".sqlEscStr($wnl_value_Ant)."' ";
			}
			
			//Fundus
			$sql = "UPDATE ".$this->tbl." SET  
				  wnlAnt='".$elem_wnlAnt."', ".
				  "wnlAntOd='".$elem_wnlAntOd."',
				  wnlAntOs='".$elem_wnlAntOs."',				  
				  ".
				  " exam_date='".date("Y-m-d H:i:s")."', uid='".$_SESSION["authId"]."',
				  statusElem='".$statusElem."'
				  ".				  
				  "				  
				  ";			
			$sql .= " ".$wnl_value_Ant_phrase." ";
			
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
					$ignoreFlds .= "anf_chamber_od_summary,
								anf_chamber_od,
								wnlAntOd,
								ncAnt_od,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "anf_chamber_os,anf_chamber_os_summary,
								wnlAntOs,
								ncAnt_os,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posAnt,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Ant,ut_elem,";}
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
						"ncAnt,".
						"ncAnt_od,".
						"ncAnt_os,".
						"wnl_value_Ant";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}
	}
	
	function isNoChanged(){
		$res= $this->getRecord("ncAnt,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncAnt"]) ){
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
		$elem_ncAnt="0";			
		$elem_ncAnt_od="0";			
		$elem_ncAnt_os="0";
		
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
				$elem_ncAnt="1";
				$elem_ncAnt_od="1";
				$elem_ncAnt_os="1";
			}else if($exmEye=="OD"){				
				$elem_ncAnt_od="1";
			}else if($exmEye=="OS"){
				$elem_ncAnt_os="1";
			}
		}			
		// ---
		
		//Get status string --
		$statusElem="";
		if($elem_ncAnt_od==1||$elem_ncAnt_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncAnt_od,$elem_ncAnt_os,0);}
		//Get status--		
		
		//
		$sql = "UPDATE ".$this->tbl."
			  SET			  
			  ncAnt = '".$elem_ncAnt."',
			  ncAnt_od='".$elem_ncAnt_od."', ncAnt_os='".$elem_ncAnt_os."',			  
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}
	
	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "anf_chamber_od";
		$arr["db"]["xmlOs"] = "anf_chamber_os";
		$arr["db"]["wnlSE"] = "wnlAnt";
		$arr["db"]["wnlOd"] = "wnlAntOd";
		$arr["db"]["wnlOs"] = "wnlAntOs";
		$arr["db"]["posSE"] = "posAnt";
		$arr["db"]["summOd"] = "anf_chamber_od_summary";
		$arr["db"]["summOs"] = "anf_chamber_os_summary";
		$arr["divSe"] = "3";
		
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