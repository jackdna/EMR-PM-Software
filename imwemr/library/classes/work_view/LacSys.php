<?php

class LacSys extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_lac_sys";
		$this->examName="LacSys";
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
	
	function get_chart_lac_sys_info($elem_dos){
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;
		$ar_ret = array();
		$sql = "SELECT * FROM chart_lac_sys WHERE form_id='$form_id' AND patient_id='".$patient_id."' AND purged = '0' ";
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
			$res = $this->getLastRecord(" * ",0,$elem_dos,"chart_lac_sys");
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}
		
		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){			
			$res = $this->getLastRecord(" * ",1,$elem_dos,"chart_lac_sys");
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}		
		
		if($row!=false){
			
			if($myflag){
				$elem_editMode=0;
			}else{
				$elem_editMode=1;
			}
			$ar_ret["elem_lacSysId_LF"]=$elem_lacSysId=$row["id"];
			$ar_ret["elem_editModeLacSys"] = $elem_editMode;

			//NC
			if($elem_editMode==1){				
				$ar_ret["elem_ncLacSys"]=$row["ncLacSys"];				
				$ar_ret["elem_examDateLacSys"]=$row["exam_date"];
			}
			
			$lacrimalSys_od= stripslashes($row["lacrimal_od"]);
			$lacrimalSys_os= stripslashes($row["lacrimal_os"]);		
			
			$arr_vals_lacrimalSys_od = $oExamXml->extractXmlValue($lacrimalSys_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_lacrimalSys_od);
			
			$arr_vals_lacrimalSys_os = $oExamXml->extractXmlValue($lacrimalSys_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_lacrimalSys_os);			
			
			$ar_ret["elem_wnlLacSys"]=$row["wnlLacSys"];			
			$ar_ret["elem_posLacSys"]=$row["posLacSys"];			
			$ar_ret["lacrimal_system"] = $row["lacrimal_system"];
			
			$ar_ret["elem_wnlLacSysOd"]=$row["wnlLacSysOd"];
			$ar_ret["elem_wnlLacSysOs"]=$row["wnlLacSysOs"];
			$ar_ret["elem_statusElementsLacSys"] = ($elem_editMode==0) ? "" : $row["statusElem"];			
			//UT Elems 
			$ar_ret["elem_utElemsLacSys"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;			
			$ar_ret["lacrimal_system_summary"]=$row["lacrimal_system_summary"];
			$ar_ret["sumLacOs"]=$row["sumLacOs"];
		}
	
		return $ar_ret;
	}	
	
	public function save_form(){
		$elem_formId = $formId = $this->fid;
		$patientid = $this->pid;
		$oChartNoteSaver = new ChartNoteSaver($patientid, $formId);
		$oExamXml = new ExamXml();	
		$arXmlFiles = $oExamXml->getExamXmlFiles("LA");
		
		//GetChangeIndicator
		$arrSe = array();		
		$tmpOd = "elem_chng_div4_Od";
		$tmpOs = "elem_chng_div4_Os";
		$$tmpOd = $_POST[$tmpOd];
		$$tmpOs = $_POST[$tmpOs];
		$arrSe[$tmpOd] = ($$tmpOd == "1") ? "1" : "0";
		$arrSe[$tmpOs] = ($$tmpOs == "1") ? "1" : "0";		
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);
		
		//Lacrimal------------
		$wnlLacSys = $posLacSys = $ncLacSys = $wnlLacSysOd = $wnlLacSysOs = "0";
		//if(!empty($elem_chng_div4_Od) || !empty($elem_chng_div4_Os)){
		//	if(!empty($elem_chng_div4_Od)){
				$menuName = "lacrimalSysOd";
				$menuFilePath = $arXmlFiles["lacSys"]["od"]; //dirname(__FILE__)."/xml/lacrimalSys_od.xml";
				$elem_lacrimalSys_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLacSysOd = $_POST["elem_wnlLacSysOd"];
		//	}

		//	if(!empty($elem_chng_div4_Os)){
				$menuName = "lacrimalSysOs";
				$menuFilePath = $arXmlFiles["lacSys"]["os"]; //dirname(__FILE__)."/xml/lacrimalSys_os.xml";
				$elem_lacrimalSys_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLacSysOs = $_POST["elem_wnlLacSysOs"];
		//	}

			$wnlLacSys = (!empty($wnlLacSysOd) && !empty($wnlLacSysOs)) ? "1" : "0"; //$_POST["elem_wnlLacSys"];
			$posLacSys = $_POST["elem_posLacSys"];
			$ncLacSys = $_POST["elem_ncLacSys"];
		//}
		//Lacrimal------------
		
		$examDate = wv_dt("now"); //$_POST["elem_examDateLids"];	
		
		//
		$oUserAp = new UserAp();
		
		//Summary --
		$sumLacOd = "";
		$sumLacOs = "";
		$strExamsAllOd = $strExamsAllOs = "";
		
		//LacSys
		$lacrimal_od = $elem_lacrimalSys_od;
		
		$arrTemp = $this->getExamSummary($lacrimal_od);
		$sumLacOd = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div4_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("LacSys",$arrExmDone_od,$sumLacOd,$arrConsoleExLower,$arrConsoleEx);
		}

		$lacrimal_os = $elem_lacrimalSys_os;
		
		$arrTemp = $this->getExamSummary($lacrimal_os);
		$sumLacOs = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div4_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("LacSys",$arrExmDone_os,$sumLacOs,$arrConsoleExLower,$arrConsoleEx);
		}			
		//Summary --
		
		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsLacSys"];
		$elem_utElems_cur = $_POST["elem_utElemsLacSys_cur"];
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
			
			$elem_lacrimalSys_od = sqlEscStr($elem_lacrimalSys_od);
			$elem_lacrimalSys_os = sqlEscStr($elem_lacrimalSys_os);
			
			//check
			$cQry = "select 
						id,last_opr_id,uid,
						lacrimal_system_summary,wnlLacSysOd,sumLacOs,wnlLacSysOs,modi_note_LacSysArr, wnl_value_LacSys, exam_date
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$last_opr_id = $_SESSION["authId"];
				$elem_editMode =  "0";
			}else{
				$lacsysId=$lacsysIDExam = $row["id"];
				$elem_editMode =  "1";
				//Modifying Notes----------------
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				//$modi_note_LacSysOd=$owv->getModiNotes($row["lacrimal_system_summary"],$row["wnlLacSysOd"],$sumLacOd,$wnlLacSysOd,$row["uid"], $row["wnl_value_LacSys"]);
				//$modi_note_LacSysOs=$owv->getModiNotes($row["sumLacOs"],$row["wnlLacSysOs"],$sumLacOs,$wnlLacSysOs,$row["uid"], $row["wnl_value_LacSys"]);
					
				$seri_modi_note_LacSysArr = $owv->getModiNotesArr($row["lacrimal_system_summary"],$sumLacOd,$last_opr_id,'OD',$row["modi_note_LacSysArr"],$row['exam_date']);
				$seri_modi_note_LacSysArr = $owv->getModiNotesArr($row["sumLacOs"],$sumLacOs,$last_opr_id,'OS',$seri_modi_note_LacSysArr,$row['exam_date']);	
				//Modifying Notes----------------
			}
			
			//
			$sql_con = "
				lacrimal_od='$elem_lacrimalSys_od',
				lacrimal_os='$elem_lacrimalSys_os',
				wnlLacSys='$wnlLacSys',
				posLacSys='$posLacSys',
				ncLacSys='$ncLacSys',
				lacrimal_system_summary='$sumLacOd',
				sumLacOs='$sumLacOs',
				wnlLacSysOd='$wnlLacSysOd',
				wnlLacSysOs='$wnlLacSysOs',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_LacSysArr = '".sqlEscStr($seri_modi_note_LacSysArr)."'
			";
			
			
			//
			if($elem_editMode == "0"){
				//WNL
				$wnl_value_Lac = $this->getExamWnlStr("Lacrimal System");
				
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',	
					wnl_value_LacSys='".sqlEscStr($wnl_value_Lac)."', 
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
				$insertId = $lacsysId;
			
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
	
	}//End Function
	
	function getPurgedExm(){
		$oOnload =  new Onload();	
		$arPurge = array();
		$sql = "SELECT ".				 
				 "c7.posLacSys,  ".
				 "c7.wnlLacSys,  ".
				 "c7.ncLacSys,  ".
				 
				 
				 "c7.lacrimal_system_summary, ".
				 "c7.sumLacOs, c7.id, ".
				 "c7.wnlLacSysOd,  ".
				 "c7.wnlLacSysOs,  ".						 
				 //"c7.modi_note_LacSysOd, ".
				 //"c7.modi_note_LacSysOs, ".
				 "c7.statusElem AS se_lacsys, c7.exam_date, c7.purgerId, c7.purgeTime, ".				 
				 "c7.modi_note_LacSysArr, c7.wnl_value_LacSys ".
				"FROM chart_master_table c1 ".
				"INNER JOIN ".$this->tbl." c7 ON c7.form_id = c1.id AND c7.purged!='0' ".
				"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
				"ORDER BY c7.purgeTime DESC ";
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
			$elem_posLacSys=assignZero($row["posLacSys"]);
			$elem_wnlLacSys=assignZero($row["wnlLacSys"]);
			$elem_ncLacSys=assignZero($row["ncLacSys"]);
			$elem_wnlLacSysOd = $row["wnlLacSysOd"];
			$elem_wnlLacSysOs = $row["wnlLacSysOs"];
			$elem_se_lacsys = $row["se_lacsys"];
			$elem_sumLacOd = $row["lacrimal_system_summary"];
			$elem_sumLacOs = $row["sumLacOs"];
			$elem_lacsys_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			//$modi_note_LacSysOd = $row["modi_note_LacSysOd"];
			//$modi_note_LacSysOs = $row["modi_note_LacSysOs"];
			$modi_note_LacSysArr = unserialize($row["modi_note_LacSysArr"]);
			$arrHx = array();
			if(count($modi_note_LacSysArr)>0 && $row["modi_note_LacSysArr"]!='')
			$arrHx['Lacrimal System'] = $modi_note_LacSysArr;
			$elem_wnl_value_LacSys=$row["wnl_value_LacSys"];
			
			//---------
			$flgSe_LacSys_Od = $flgSe_LacSys_Os = "0";
			$tmpArrSe=array();
			if(!isset($bgColor_lacsys)){
				if(!empty($elem_se_lacsys)){
					$tmpArrSe = $this->se_elemStatus($this->examName,"0",$elem_se_lacsys);
					$flgSe_LacSys_Od = $tmpArrSe["4"]["od"];
					$flgSe_LacSys_Os = $tmpArrSe["4"]["os"];
				}
			}else{
				if(!empty($elem_se_lacsys_prev)){
					$tmpArrSe_prev = $this->se_elemStatus($this->examName,"0",$elem_se_lacsys_prev);
					$flgSe_LacSys_Od_prev = $tmpArrSe_prev["4"]["od"];
					$flgSe_LacSys_Os_prev = $tmpArrSe_prev["4"]["os"];
				}
			}
			
			//WNL
			//Lac
			$wnlString_Lac = !empty($elem_wnl_value_LacSys) ? $elem_wnl_value_LacSys : $this->getExamWnlStr("Lacrimal System", $patient_id, $form_id);
			$wnlStringOd_Lac = $wnlStringOs_Lac = $wnlString_Lac;
			
			if(empty($flgSe_LacSys_Od) && empty($flgSe_LacSys_Od_prev) && !empty($elem_wnlLacSysOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lacrimal System", "OD"); if(!empty($tmp)){ $wnlStringOd_Lac = $tmp;}  }
			if(empty($flgSe_LacSys_Os) && empty($flgSe_LacSys_Os_prev) && !empty($elem_wnlLacSysOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lacrimal System", "OS"); if(!empty($tmp)){ $wnlStringOs_Lac = $tmp;}  }
			
			list($elem_sumLacOd,$elem_sumLacOs) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Lac,"wValOs"=>$wnlStringOs_Lac,
									"wOd"=>$elem_wnlLacSysOd,"sOd"=>$elem_sumLacOd,
									"wOs"=>$elem_wnlLacSysOs,"sOs"=>$elem_sumLacOs));
			
			//Nochanged
			if(!empty($elem_se_lacsys)&&strpos($elem_se_lacsys,"=1")!==false){
				$elem_ncLacSys=1;
			}
			
			$arr=array();		
			//Lacrimal System
			$arr["subExm"][3] = $oOnload->getArrExms_ms(array("enm"=>"Lacrimal System",
											"sOd"=>$elem_sumLacOd,"sOs"=>$elem_sumLacOs,
											"fOd"=>$flgSe_LacSys_Od,"fOs"=>$flgSe_LacSys_Os,"pos"=>$elem_posLacSys,
											//"arcJsOd"=>$moeArc["od"]["lacrimal"],"arcJsOs"=>$moeArc["os"]["lacrimal"],
											"arcCssOd"=>$flgArcColor["od"]["lacrimal"],"arcCssOs"=>$flgArcColor["os"]["lacrimal"],
											//"mnOd"=>$moeMN["od"]["lacrimal"],"mnOs"=>$moeMN["os"]["lacrimal"],
											"enm_2"=>"LacSys"));
											
			//Sub Exam List
			$arr["seList"] = 	array(
							"LacSys"=>array("enm"=>"Lacrimal System","pos"=>$elem_posLacSys,
											"wOd"=>$elem_wnlLacSysOd,"wOs"=>$elem_wnlLacSysOs)
							);
			$arr["bgColor"] = "".$bgColor_lacsys;
			$arr["nochange"] = $elem_ncLacSys;
			$arr["examdate"] = $examdate;			
			$arr["exm_flg_se"] = array($flgSe_LacSys_Od,$flgSe_LacSys_Os);
			$arr["purgerId"] = $row["purgerId"];
			$arr["purgeTime"] = $row["purgeTime"];
			
			$arPurge[] = $arr;			
		}
		
		return $arPurge;
	}
	
	function getWorkViewSummery($post){
		$oOnload =  new Onload();
		//object Chart Rec Archive --
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION['authId']);
		//---
		$echo="";
		$sql = "SELECT ".				 
				 "c7.posLacSys,  ".
				 "c7.wnlLacSys,  ".
				 "c7.ncLacSys,  ".
				 
				 
				 "c7.lacrimal_system_summary, ".
				 "c7.sumLacOs, c7.id, ".
				 "c7.wnlLacSysOd,  ".
				 "c7.wnlLacSysOs,  ".						 
				 //"c7.modi_note_LacSysOd, ".
				 //"c7.modi_note_LacSysOs, ".
				 "c7.statusElem AS se_lacsys, c7.exam_date, ".				 
				 "c7.modi_note_LacSysArr, c7.wnl_value_LacSys ".
				"FROM chart_master_table c1 ".
				"INNER JOIN ".$this->tbl." c7 ON c7.form_id = c1.id AND c7.purged='0' ".
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
			$elem_posLacSys=assignZero($row["posLacSys"]);
			$elem_wnlLacSys=assignZero($row["wnlLacSys"]);
			$elem_ncLacSys=assignZero($row["ncLacSys"]);
			$elem_wnlLacSysOd = $row["wnlLacSysOd"];
			$elem_wnlLacSysOs = $row["wnlLacSysOs"];
			$elem_se_lacsys = $row["se_lacsys"];
			$elem_sumLacOd = $row["lacrimal_system_summary"];
			$elem_sumLacOs = $row["sumLacOs"];
			$elem_lacsys_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			//$modi_note_LacSysOd = $row["modi_note_LacSysOd"];
			//$modi_note_LacSysOs = $row["modi_note_LacSysOs"];
			$modi_note_LacSysArr = unserialize($row["modi_note_LacSysArr"]);
			$arrHx = array();
			if(is_array($modi_note_LacSysArr) && count($modi_note_LacSysArr)>0 && $row["modi_note_LacSysArr"]!='')
			$arrHx['Lacrimal System'] = $modi_note_LacSysArr;
			$elem_wnl_value_LacSys=$row["wnl_value_LacSys"];
		}
		
		//Previous
		if(empty($elem_lacsys_id)){
			$tmp = "";			
			$tmp .= "  c2.posLacSys,  ";
			$tmp .= "  c2.wnlLacSys,  ";
			$tmp .= "  c2.wnlLacSysOd, ";
			$tmp .= "  c2.wnlLacSysOs, ";
			$tmp .= "  c2.ncLacSys, ";			
			$tmp .= " c2.lacrimal_system_summary,c2.exam_date, ";
			$tmp .= " c2.wnl_value_LacSys,  ";
			$tmp .= " c2.sumLacOs, c2.id, c2.statusElem AS se_lacsys ";
			
			//$res = valNewRecordL_a($patient_id, $tmp);
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			
			if($row!=false){
				$elem_sumLacOd = $row["lacrimal_system_summary"];
				$elem_sumLacOs = $row["sumLacOs"];
				$elem_posLacSys=assignZero($row["posLacSys"]);
				$elem_wnlLacSys=assignZero($row["wnlLacSys"]);
				$elem_ncLacSys_prev=assignZero($row["ncLacSys"]);
				$elem_wnlLacSysOd=assignZero($row["wnlLacSysOd"]);
				$elem_wnlLacSysOs=assignZero($row["wnlLacSysOs"]);
				$examdate = wv_formatDate($row["exam_date"]);
				$elem_wnl_value_LacSys=$row["wnl_value_LacSys"];
				$elem_se_lacsys_prev = $row["se_lacsys"];			
			}
			//BG
			$bgColor_lacsys = "bgSmoke";		
		}
		
		//---------
		$flgSe_LacSys_Od = $flgSe_LacSys_Os = "0";
		$tmpArrSe=array();
		if(!isset($bgColor_lacsys)){
			if(!empty($elem_se_lacsys)){
				$tmpArrSe = $this->se_elemStatus($this->examName,"0",$elem_se_lacsys);
				$flgSe_LacSys_Od = $tmpArrSe["4"]["od"];
				$flgSe_LacSys_Os = $tmpArrSe["4"]["os"];
			}
		}else{
			if(!empty($elem_se_lacsys_prev)){
				$tmpArrSe_prev = $this->se_elemStatus($this->examName,"0",$elem_se_lacsys_prev);
				$flgSe_LacSys_Od_prev = $tmpArrSe_prev["4"]["od"];
				$flgSe_LacSys_Os_prev = $tmpArrSe_prev["4"]["os"];
			}
		}
		
		//WNL
		//Lac
		$wnlString_Lac = !empty($elem_wnl_value_LacSys) ? $elem_wnl_value_LacSys : $this->getExamWnlStr("Lacrimal System", $patient_id, $form_id);
		$wnlStringOd_Lac = $wnlStringOs_Lac = $wnlString_Lac;
		
		if(empty($flgSe_LacSys_Od) && empty($flgSe_LacSys_Od_prev) && !empty($elem_wnlLacSysOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lacrimal System", "OD"); if(!empty($tmp)){ $wnlStringOd_Lac = $tmp;}  }
		if(empty($flgSe_LacSys_Os) && empty($flgSe_LacSys_Os_prev) && !empty($elem_wnlLacSysOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lacrimal System", "OS"); if(!empty($tmp)){ $wnlStringOs_Lac = $tmp;}  }
		
		list($elem_sumLacOd,$elem_sumLacOs) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Lac,"wValOs"=>$wnlStringOs_Lac,
								"wOd"=>$elem_wnlLacSysOd,"sOd"=>$elem_sumLacOd,
								"wOs"=>$elem_wnlLacSysOs,"sOs"=>$elem_sumLacOs));
		
		//Archive LA -----
		if($bgColor_lacsys != "bgSmoke"){
			$arrDivArcCmn=array();
			$oChartRecArc->setChkTbl($this->tbl);
			$arrInpArc = array(
				"elem_sumOdLac"=>array("lacrimal_system_summary",$elem_sumLacOd,"","wnlLacSysOd",$wnlString_Lac,$modi_note_LacSysOd),
				"elem_sumOsLac"=>array("sumLacOs",$elem_sumLacOs,"","wnlLacSysOs",$wnlString_Lac,$modi_note_LacSysOs)
				);
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
			//Lacrimal -
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdLac"])){
				//echo $arTmpRecArc["div"]["elem_sumOdLac"];
				$arrDivArcCmn["Lacrimal System"]["OD"]=$arTmpRecArc["div"]["elem_sumOdLac"];					
				$moeArc["od"]["lacrimal"] = $arTmpRecArc["js"]["elem_sumOdLac"];
				$flgArcColor["od"]["lacrimal"] = $arTmpRecArc["css"]["elem_sumOdLac"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdLac"])) $elem_sumLacOd = $arTmpRecArc["curText"]["elem_sumOdLac"];
			}else{
				$moeArc["od"]["lacrimal"]=$flgArcColor["od"]["lacrimal"]="";
			}
			//OS
			if(!empty($arTmpRecArc["div"]["elem_sumOsLac"])){
				//echo $arTmpRecArc["div"]["elem_sumOsLac"];
				$arrDivArcCmn["Lacrimal System"]["OS"]=$arTmpRecArc["div"]["elem_sumOsLac"];
				$moeArc["os"]["lacrimal"] = $arTmpRecArc["js"]["elem_sumOsLac"];
				$flgArcColor["os"]["lacrimal"] = $arTmpRecArc["css"]["elem_sumOsLac"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsLac"])) $elem_sumLacOs = $arTmpRecArc["curText"]["elem_sumOsLac"];
			}else{
				$moeArc["os"]["lacrimal"]=$flgArcColor["os"]["lacrimal"]="";
			}
			//Lacrimal -
		}
		//Archive LA -----
		
		//Nochanged
		if(!empty($elem_se_lacsys)&&strpos($elem_se_lacsys,"=1")!==false){
			$elem_ncLacSys=1;
		}
		
		//Modified Notes ----
		//if Edit is not Done && modified Notes exists
		/*
		//lacysys
		if(!empty($modi_note_LacSysOd) && empty($moeArc["od"]["lacrimal"])){ //Od
			list($moeMN["od"]["lacrimal"],$tmpDiv)=$oOnload->getModiNoteConDiv("elem_sumOdLac", $modi_note_LacSysOd);
			//echo $tmpDiv;
			$arrDivArcCmn["Lacrimal System"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["lacrimal"]="";
		}
		if(!empty($modi_note_LacSysOs) && empty($moeArc["os"]["lacrimal"])){ //Os
			list($moeMN["os"]["lacrimal"],$tmpDiv)=$oOnload->getModiNoteConDiv("elem_sumOsLac", $modi_note_LacSysOs);
			//echo $tmpDiv;
			$arrDivArcCmn["Lacrimal System"]["OS"]=$tmpDiv;
		}else{
			$moeMN["os"]["lacrimal"]="";
		}
		*/
		//Modified Notes ----
		
		$arr=array();
		
		//Lacrimal System
		$arr["subExm"][3] = $oOnload->getArrExms_ms(array("enm"=>"Lacrimal System",
										"sOd"=>$elem_sumLacOd,"sOs"=>$elem_sumLacOs,
										"fOd"=>$flgSe_LacSys_Od,"fOs"=>$flgSe_LacSys_Os,"pos"=>$elem_posLacSys,
										//"arcJsOd"=>$moeArc["od"]["lacrimal"],"arcJsOs"=>$moeArc["os"]["lacrimal"],
										"arcCssOd"=>$flgArcColor["od"]["lacrimal"],"arcCssOs"=>$flgArcColor["os"]["lacrimal"],
										//"mnOd"=>$moeMN["od"]["lacrimal"],"mnOs"=>$moeMN["os"]["lacrimal"],
										"enm_2"=>"LacSys"));
										
		//Sub Exam List
		$arr["seList"] = 	array(
						"LacSys"=>array("enm"=>"Lacrimal System","pos"=>$elem_posLacSys,
										"wOd"=>$elem_wnlLacSysOd,"wOs"=>$elem_wnlLacSysOs)
						);
		$arr["bgColor"] = "".$bgColor_lacsys;
		$arr["nochange"] = $elem_ncLacSys;
		$arr["examdate"] = $examdate;		
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_LacSys_Od,$flgSe_LacSys_Os);
		$arr["arrHx"] = $arrHx;
		return $arr;
		
	}
	
	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		//WNL		
		$wnl_value_Lac = $this->getExamWnlStr("Lacrimal System");
			
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_LacSys)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."','".sqlEscStr($wnl_value_Lac)."') ";
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
					"ncLacSys,".
					"ncLacSys_od,".
					"ncLacSys_os,".
					"wnl_value_LacSys";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,"",'id');
	}
	
	function save_wnl($op="get", $arv=array()){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$exmEye = $_POST["elem_exmEye"];
		if($op=="get"){
			$wnl_value_LacSys="";
			$wnl_value_LacSys_phrase="";
			$flgCarry=0;
			
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}
			
			$cQry = "select 
					wnlLacSys, 
					wnlLacSysOd, 
					wnlLacSysOs,
					posLacSys,
					lacrimal_system_summary,
					sumLacOs,
					statusElem as statusElemLacSys, wnl_value_LacSys
					FROM ".$this->tbl." WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
			}else{
				$ar_ret = $row;
			}
			return $ar_ret;			
		}else if($op=="set" && count($arv)){
			extract($arv);
			//
			$oWv = new WorkView();		
			
			
			//Toggle --
			if((!empty($statusElemLacSys)&&strpos($statusElemLacSys,"0")===false)||
				(empty($lacrimal_system_summary) && empty($wnlLacSysOd))||
				(empty($sumLacOs) && empty($wnlLacSysOs))
				 ){
			//Toggle LacSys
			list($wnlLacSysOd,$wnlLacSysOs,$wnlLacSys) =
									$oWv->toggleWNL($posLacSys,$lacrimal_system_summary,$sumLacOs,$wnlLacSysOd,
													$wnlLacSysOs,$wnlLacSys,$exmEye);
			}
			
			//Status
			$statusElem_LA_prev=$statusElemLacSys;
			$statusElemLacSys = $this->setEyeStatus($this->examName, $exmEye,$statusElemLacSys,0);
			
			//Toggle --
			
			//getWnlValue		
			if(empty($wnl_value_LacSys)){
				$wnl_value_LacSys=$this->getExamWnlStr("Lacrimal System");
				$wnl_value_LacSys_phrase = ", wnl_value_LacSys='".sqlEscStr($wnl_value_LacSys)."' ";
			}
			
			$sql = "UPDATE ".$this->tbl." SET
				 wnlLacSys='".$wnlLacSys."', 
				 wnlLacSysOd='".$wnlLacSysOd."',
				 wnlLacSysOs='".$wnlLacSysOs."',
				 exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
				 statusElem='".$statusElemLacSys."'			 
				 ".$wnl_value_LacSys_phrase." "."
				 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
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
					$ignoreFlds .= "lacrimal_system_summary,
								lacrimal_od,
								wnlLacSysOd,
								ncLacSys_od,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "lacrimal_os,sumLacOs,
								wnlLacSysOs,
								ncLacSys_os,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posLacSys,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_LacSys,ut_elem,";}
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
						"ncLacSys,".
						"ncLacSys_od,".
						"ncLacSys_os,".						
						"wnl_value_LacSys";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}	
	}
	
	function isNoChanged(){
		$res= $this->getRecord("ncLacSys,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncLacSys"]) ){
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
		
		//$elem_noChangeLa = $_POST["elem_noChangeLA"];	
		$tmpNC=0;
		$elem_ncLacSys="0";
		$elem_ncLacSys_od="0";
		$elem_ncLacSys_os="0";
		
		//
		$oLA=$this; //new LA($patientId,$form_id);
		if(!$oLA->isRecordExists()){
			$oLA->carryForward();
			$tmpNC=1;
		}else if(!$oLA->isNoChanged()){
			$tmpNC=1;
		}else{
			$oLA->set2PrvVals();
			$tmpNC=0;
		}
		
		// ---
		//Set NC
		if($tmpNC==1){
			if($exmEye=="OU"){				
				$elem_ncLacSys="1";
				$elem_ncLacSys_od="1";
				$elem_ncLacSys_os="1";
			}else if($exmEye=="OD"){
				$elem_ncLacSys_od="1";
			}else if($exmEye=="OS"){
				$elem_ncLacSys_os="1";
			}
		}			
		// ---
		
		//Get status string --
		$statusElem="";
		if($elem_ncLacSys_od==1||$elem_ncLacSys_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncLacSys_od,$elem_ncLacSys_os,0);}
		//Get status--
		
		//
		$sql = "UPDATE ".$this->tbl."
			  SET
			  ncLacSys = '".$elem_ncLacSys."',
			  ncLacSys_od = '".$elem_ncLacSys_od."', ncLacSys_os = '".$elem_ncLacSys_os."',			  
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	
	}
	
	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "lacrimal_od";
		$arr["db"]["xmlOs"] = "lacrimal_os";
		$arr["db"]["wnlSE"] = "wnlLacSys";
		$arr["db"]["wnlOd"] = "wnlLacSysOd";
		$arr["db"]["wnlOs"] = "wnlLacSysOs";
		$arr["db"]["posSE"] = "posLacSys";
		$arr["db"]["summOd"] = "lacrimal_system_summary";
		$arr["db"]["summOs"] = "sumLacOs";
		$arr["divSe"] = "4";
		
		$oExamXml = new ExamXml();
		$tmp = $oExamXml->getExamXmlFiles("LA", $this->examName);
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