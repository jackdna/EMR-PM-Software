<?php

class Lids extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_lids";
		$this->examName="Lids";
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
	
	//chart_lids
	function get_chart_lids_info($elem_dos){
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;
		
		$ar_ret = array();
		$sql = "SELECT * FROM chart_lids WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' AND purged = '0' ";
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
			$res = $this->getLastRecord(" * ",0,$elem_dos,"chart_lids");
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}
		
		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){			
			$res = $this->getLastRecord(" * ",1,$elem_dos,"chart_lids");
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
			$ar_ret["elem_lidsId_LF"]=$ar_ret["elem_lidsId"]=$row["id"];
			$ar_ret["elem_editModeLids"] = $elem_editMode;
			
			//NC
			if($elem_editMode==1){				
				$ar_ret["elem_ncLids"]=$row["ncLids"];				
				$ar_ret["elem_examDateLids"]=$row["exam_date"];
			}			

			$lid_od= stripslashes($row["lid_od"]);
			$lid_os= stripslashes($row["lid_os"]);			
			
			$arr_vals_lid_od = $oExamXml->extractXmlValue($lid_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_lid_od);
			//extract($arr_vals_lid_od);
			$arr_vals_lid_os = $oExamXml->extractXmlValue($lid_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_lid_os);
			//extract($arr_vals_lid_os);			
			
			
			$ar_ret["elem_wnlLids"]=$row["wnlLids"];			
			$ar_ret["elem_posLids"]=$row["posLids"];
			$ar_ret["elem_wnlLidsOd"]=$row["wnlLidsOd"];
			$ar_ret["elem_wnlLidsOs"]=$row["wnlLidsOs"];
			
			$ar_ret["elem_statusElementsLids"] = ($elem_editMode==0) ? "" : $row["statusElem"];
			
			//UT Elems 
			$ar_ret["elem_utElemsLids"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;  
			
			//
			$ar_ret["lid_conjunctiva_summary"]=$row["lid_conjunctiva_summary"];
			$ar_ret["sumLidsOs"]=$row["sumLidsOs"];		
			
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
		$tmpOd = "elem_chng_div1_Od";
		$tmpOs = "elem_chng_div1_Os";
		$$tmpOd = $_POST[$tmpOd];
		$$tmpOs = $_POST[$tmpOs];
		$arrSe[$tmpOd] = ($$tmpOd == "1") ? "1" : "0";
		$arrSe[$tmpOs] = ($$tmpOs == "1") ? "1" : "0";		
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);
		
		
		//Lids----------------
		$wnlLids =$posLids=$ncLids=$wnlLidsOd = $wnlLidsOs ="0";
		//if(!empty($elem_chng_div1_Od) || !empty($elem_chng_div1_Os)){
		//	if(!empty($elem_chng_div1_Od)){
				$menuName = "lidsOd";
				$menuFilePath = $arXmlFiles["lids"]["od"]; //dirname(__FILE__)."/xml/lids_od.xml";
				$elem_lid_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLidsOd = $_POST["elem_wnlLidsOd"];
		//	}

		//	if(!empty($elem_chng_div1_Os)){
				$menuName = "lidsOs";
				$menuFilePath = $arXmlFiles["lids"]["os"]; //dirname(__FILE__)."/xml/lids_os.xml";
				$elem_lid_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLidsOs = $_POST["elem_wnlLidsOs"];
		//	}

			$wnlLids = (!empty($wnlLidsOd) && !empty($wnlLidsOs)) ? "1" : "0";     //$_POST["elem_wnlLids"];
			$posLids=$_POST["elem_posLids"];
			$ncLids=$_POST["elem_ncLids"];
		//}

		//Lids----------------
		
		$examDate = wv_dt("now"); //$_POST["elem_examDateLids"];
		
		$oUserAp = new UserAp();
		//Summary --
		$sumLidsOd = $sumLidsOs = "";
		$strExamsAllOd = $strExamsAllOs = "";
		
		//Lids
		$lid_od= $elem_lid_od;
		
		$arrTemp = $this->getExamSummary($lid_od);
		$sumLidsOd = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div1_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Lids",$arrExmDone_od,$sumLidsOd);
		}
		
		$lid_os= $elem_lid_os;
		$arrTemp = $this->getExamSummary($lid_os);
		$sumLidsOs = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div1_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Lids",$arrExmDone_os,$sumLidsOs);
		}	
		
		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsLids"];
		$elem_utElems_cur = $_POST["elem_utElemsLids_cur"];
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
			$elem_lid_od = sqlEscStr($elem_lid_od);
			$elem_lid_os = sqlEscStr($elem_lid_os);
			
			//check
			$cQry = "select 
						id,last_opr_id,uid,
						lid_conjunctiva_summary,wnlLidsOd,sumLidsOs,wnlLidsOs,modi_note_LidsArr,wnl_value_Lids,
						exam_date
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$last_opr_id = $_SESSION["authId"];
				$elem_editMode =  "0";
			}else{
				$lidsId=$lidsIDExam = $row["id"];
				$elem_editMode =  "1";
				
				//Modifying Notes----------------
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				//$modi_note_LidsOd=$owv->getModiNotes($row["lid_conjunctiva_summary"],$row["wnlLidsOd"],$sumLidsOd,$wnlLidsOd,$row["uid"],$row["wnl_value_Lids"]);
				//$modi_note_LidsOs=$owv->getModiNotes($row["sumLidsOs"],$row["wnlLidsOs"],$sumLidsOs,$wnlLidsOs,$row["uid"], $row["wnl_value_Lids"]);
				$seri_modi_note_LidsArr = $owv->getModiNotesArr($row["lid_conjunctiva_summary"],$sumLidsOd,$last_opr_id,'OD',$row["modi_note_LidsArr"],$row['exam_date']);
				$seri_modi_note_LidsArr = $owv->getModiNotesArr($row["sumLidsOs"],$sumLidsOs,$last_opr_id,'OS',$seri_modi_note_LidsArr,$row['exam_date']);
					
				//Modifying Notes----------------
			}
			
			//
			$sql_con = "
				lid_od='$elem_lid_od',
				lid_os='$elem_lid_os',
				wnlLids='$wnlLids',
				posLids='$posLids',
				ncLids='$ncLids',
				lid_conjunctiva_summary='$sumLidsOd',
				sumLidsOs='$sumLidsOs',
				wnlLidsOd='$wnlLidsOd',
				wnlLidsOs='$wnlLidsOs',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_LidsArr = '".sqlEscStr($seri_modi_note_LidsArr)."'
			";		
			
			//
			if($elem_editMode == "0"){
				//WNL
				$wnl_value_Lids = $this->getExamWnlStr("Lids");
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',	
					wnl_value_Lids='".sqlEscStr($wnl_value_Lids)."', 
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
				$insertId = $lidsId;
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

	}//end function
	
	function getPurgedExm(){
		$oOnload =  new Onload();
		$arPurge = array();
		$sql = "SELECT ".				
				 "c7.posLids, ".
				 "c7.wnlLids, ".
				 "c7.ncLids, ".				 
				 "c7.lid_conjunctiva_summary, ".				 
				 "c7.sumLidsOs, c7.id, ".
				 "c7.wnlLidsOd, ".
				 "c7.wnlLidsOs, ".
				 //"c7.modi_note_LidsOd,  ".
				 //"c7.modi_note_LidsOs,  ".				 
				 "c7.statusElem AS se_lids, c7.exam_date, c7.purgerId, c7.purgeTime, ".
				 "c7.modi_note_LidsArr, c7.wnl_value_Lids ".				 
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
			$elem_posLids=assignZero($row["posLids"]);
			$elem_wnlLids=assignZero($row["wnlLids"]);
			$elem_ncLids=assignZero($row["ncLids"]);
			$elem_wnlLidsOd = $row["wnlLidsOd"];
			$elem_wnlLidsOs = $row["wnlLidsOs"];
			$elem_se_lids = $row["se_lids"];
			$elem_sumLidsOd = $row["lid_conjunctiva_summary"];
			$elem_sumLidsOs = $row["sumLidsOs"];
			$elem_lids_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			
			//$modi_note_LidsOd = $row["modi_note_LidsOd"];
			//$modi_note_LidsOs = $row["modi_note_LidsOs"];
			
			$modi_note_LidsArr = unserialize($row["modi_note_LidsArr"]);
			$arrHx = array();
			if(count($modi_note_LidsArr)>0 && $row["modi_note_LidsArr"]!='')
			$arrHx['Lids']	= $modi_note_LidsArr;
			$elem_wnl_value_Lids=$row["wnl_value_Lids"];
			
			//---------
			$flgSe_Lids_Od = $flgSe_Lids_Os = "0";
			$tmpArrSe=array();
			if(!isset($bgColor_lids)){
				if(!empty($elem_se_lids)){
					$tmpArrSe = $this->se_elemStatus($this->examName,"0",$elem_se_lids);
					$flgSe_Lids_Od = $tmpArrSe["1"]["od"];
					$flgSe_Lids_Os = $tmpArrSe["1"]["os"];
				}
			}else{
				if(!empty($elem_se_lids_prev)){
					$tmpArrSe_prev = $this->se_elemStatus($this->examName,"0",$elem_se_lids_prev);
					$flgSe_Lids_Od_prev = $tmpArrSe_prev["1"]["od"];
					$flgSe_Lids_Os_prev = $tmpArrSe_prev["1"]["os"];
				}
			}
			
			//WNL
			//Lids --
			$wnlString_Lids = !empty($elem_wnl_value_Lids) ? $elem_wnl_value_Lids : $this->getExamWnlStr("Lids");
			$wnlStringOd_Lids = $wnlStringOs_Lids = $wnlString_Lids;
			
			if(empty($flgSe_Lids_Od) && empty($flgSe_Lids_Od_prev) && !empty($elem_wnlLidsOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lids", "OD"); if(!empty($tmp)){ $wnlStringOd_Lids = $tmp;}  }
			if(empty($flgSe_Lids_Os) && empty($flgSe_Lids_Os_prev) && !empty($elem_wnlLidsOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lids", "OS"); if(!empty($tmp)){ $wnlStringOs_Lids = $tmp;}  }
			
			list($elem_sumLidsOd,$elem_sumLidsOs) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Lids,"wValOs"=>$wnlStringOs_Lids,
									"wOd"=>$elem_wnlLidsOd,"sOd"=>$elem_sumLidsOd,
									"wOs"=>$elem_wnlLidsOs,"sOs"=>$elem_sumLidsOs));
									
			//Nochanged
			if(!empty($elem_se_lids)&&strpos($elem_se_lids,"=1")!==false){
				$elem_ncLids=1;
			}
			
			$arr=array();
		
			//Lids
			$arr["subExm"][0] = $oOnload->getArrExms_ms(array("enm"=>"Lids",
											"sOd"=>$elem_sumLidsOd,"sOs"=>$elem_sumLidsOs,
											"fOd"=>$flgSe_Lids_Od,"fOs"=>$flgSe_Lids_Os,"pos"=>$elem_posLids,
											//"arcJsOd"=>$moeArc["od"]["lid"],"arcJsOs"=>$moeArc["os"]["lid"],
											"arcCssOd"=>$flgArcColor["od"]["lid"],"arcCssOs"=>$flgArcColor["os"]["lid"],
											//"mnOd"=>$moeMN["od"]["lid"],"mnOs"=>$moeMN["os"]["lid"],
											"enm_2"=>"Lids"));
			//Sub Exam List
			$arr["seList"] = array("Lids"=>array("enm"=>"Lids","pos"=>$elem_posLids,
											"wOd"=>$elem_wnlLidsOd,"wOs"=>$elem_wnlLidsOs));
			
			$arr["bgColor"] = "".$bgColor_lids;
			$arr["nochange"] = $elem_ncLids;
			$arr["examdate"] = $examdate;			
			$arr["exm_flg_se"] = array($flgSe_Lids_Od,$flgSe_Lids_Os);
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
				 "c7.posLids, ".
				 "c7.wnlLids, ".
				 "c7.ncLids, ".				 
				 "c7.lid_conjunctiva_summary, ".				 
				 "c7.sumLidsOs, c7.id, ".
				 "c7.wnlLidsOd, ".
				 "c7.wnlLidsOs, ".
				 //"c7.modi_note_LidsOd,  ".
				 //"c7.modi_note_LidsOs,  ".				 
				 "c7.statusElem AS se_lids, c7.exam_date, ".
				 "c7.modi_note_LidsArr, c7.wnl_value_Lids ".				 
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
			$elem_posLids=assignZero($row["posLids"]);
			$elem_wnlLids=assignZero($row["wnlLids"]);
			$elem_ncLids=assignZero($row["ncLids"]);
			$elem_wnlLidsOd = $row["wnlLidsOd"];
			$elem_wnlLidsOs = $row["wnlLidsOs"];
			$elem_se_lids = $row["se_lids"];
			$elem_sumLidsOd = $row["lid_conjunctiva_summary"];
			$elem_sumLidsOs = $row["sumLidsOs"];
			$elem_lids_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			
			//$modi_note_LidsOd = $row["modi_note_LidsOd"];
			//$modi_note_LidsOs = $row["modi_note_LidsOs"];
			
			$modi_note_LidsArr = unserialize($row["modi_note_LidsArr"]);
			$arrHx = array();
			if(is_array($modi_note_LidsArr) && count($modi_note_LidsArr)>0 && $row["modi_note_LidsArr"]!='')
			$arrHx['Lids']	= $modi_note_LidsArr;
			$elem_wnl_value_Lids=$row["wnl_value_Lids"];
			
		}
		
		//Previous
		if(empty($elem_lids_id)){
			$tmp = "";
			
			$tmp .= " c2.posLids, ";
			$tmp .= " c2.wnlLids, ";
			$tmp .= " c2.wnlLidsOd, ";
			$tmp .= " c2.wnlLidsOs, ";
			$tmp .= " c2.ncLids, ";			
			$tmp .= " c2.lid_conjunctiva_summary, c2.exam_date, ";
			$tmp .= "  c2.wnl_value_Lids, ";
			$tmp .= " c2.sumLidsOs, c2.id, c2.statusElem AS se_lids ";
			
			//$res = valNewRecordL_a($patient_id, $tmp);
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			
			if($row!=false){	
				$elem_sumLidsOd = $row["lid_conjunctiva_summary"];
				$elem_sumLidsOs = $row["sumLidsOs"];
				$elem_posLids=assignZero($row["posLids"]);
				$elem_wnlLids=assignZero($row["wnlLids"]);
				$elem_ncLids_prev=assignZero($row["ncLids"]);
				$elem_wnlLidsOd=assignZero($row["wnlLidsOd"]);
				$elem_wnlLidsOs=assignZero($row["wnlLidsOs"]);
				$examdate = wv_formatDate($row["exam_date"]);
				$elem_wnl_value_Lids=$row["wnl_value_Lids"];
				$elem_se_lids_prev = $row["se_lids"];
			}
			//BG
			$bgColor_lids = "bgSmoke";
		}
		
		//---------
		$flgSe_Lids_Od = $flgSe_Lids_Os = "0";
		$tmpArrSe=array();
		if(!isset($bgColor_lids)){
			if(!empty($elem_se_lids)){
				$tmpArrSe = $this->se_elemStatus($this->examName,"0",$elem_se_lids);
				$flgSe_Lids_Od = $tmpArrSe["1"]["od"];
				$flgSe_Lids_Os = $tmpArrSe["1"]["os"];
			}
		}else{
			if(!empty($elem_se_lids_prev)){
				$tmpArrSe_prev = $this->se_elemStatus($this->examName,"0",$elem_se_lids_prev);
				$flgSe_Lids_Od_prev = $tmpArrSe_prev["1"]["od"];
				$flgSe_Lids_Os_prev = $tmpArrSe_prev["1"]["os"];
			}
		}
		
		//WNL
		//Lids --
		$wnlString_Lids = !empty($elem_wnl_value_Lids) ? $elem_wnl_value_Lids : $this->getExamWnlStr("Lids");
		$wnlStringOd_Lids = $wnlStringOs_Lids = $wnlString_Lids;
		
		if(empty($flgSe_Lids_Od) && empty($flgSe_Lids_Od_prev) && !empty($elem_wnlLidsOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lids", "OD"); if(!empty($tmp)){ $wnlStringOd_Lids = $tmp;}  }
		if(empty($flgSe_Lids_Os) && empty($flgSe_Lids_Os_prev) && !empty($elem_wnlLidsOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lids", "OS"); if(!empty($tmp)){ $wnlStringOs_Lids = $tmp;}  }
		
		list($elem_sumLidsOd,$elem_sumLidsOs) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Lids,"wValOs"=>$wnlStringOs_Lids,
								"wOd"=>$elem_wnlLidsOd,"sOd"=>$elem_sumLidsOd,
								"wOs"=>$elem_wnlLidsOs,"sOs"=>$elem_sumLidsOs));
		//Archive LA -----
		if($bgColor_lids != "bgSmoke"){
			$arrDivArcCmn=array();
			$oChartRecArc->setChkTbl($this->tbl);
			$arrInpArc = array("elem_sumOdLids"=>array("lid_conjunctiva_summary",$elem_sumLidsOd,"","wnlLidsOd",$wnlString_Lids,$modi_note_LidsOd),
							"elem_sumOsLids"=>array("sumLidsOs",$elem_sumLidsOs,"","wnlLidsOs",$wnlString_Lids,$modi_note_LidsOs)
							);
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
			//Lids -
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdLids"])){
				//echo $arTmpRecArc["div"]["elem_sumOdLids"];
				$arrDivArcCmn["Lids"]["OD"]=$arTmpRecArc["div"]["elem_sumOdLids"];
				$moeArc["od"]["lid"] = $arTmpRecArc["js"]["elem_sumOdLids"];
				$flgArcColor["od"]["lid"] = $arTmpRecArc["css"]["elem_sumOdLids"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdLids"])) $elem_sumLidsOd = $arTmpRecArc["curText"]["elem_sumOdLids"];				
			}else{
				$moeArc["od"]["lid"]=$flgArcColor["od"]["lid"]="";
			}
			//OS
			if(!empty($arTmpRecArc["div"]["elem_sumOsLids"])){
				//echo $arTmpRecArc["div"]["elem_sumOsLids"];
				$arrDivArcCmn["Lids"]["OS"]=$arTmpRecArc["div"]["elem_sumOsLids"];
				$moeArc["os"]["lid"] = $arTmpRecArc["js"]["elem_sumOsLids"];
				$flgArcColor["os"]["lid"] = $arTmpRecArc["css"]["elem_sumOsLids"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsLids"])) $elem_sumLidsOs = $arTmpRecArc["curText"]["elem_sumOsLids"];				
			}else{
				$moeArc["os"]["lid"]=$flgArcColor["os"]["lid"]="";
			}
			//Lids -
		}
		
		//Nochanged
		if(!empty($elem_se_lids)&&strpos($elem_se_lids,"=1")!==false){
			$elem_ncLids=1;
		}
		
		//Modified Notes ----
		/*
		//if Edit is not Done && modified Notes exists
		if(!empty($modi_note_LidsOd) && empty($moeArc["od"]["lid"])){ //Od
			list($moeMN["od"]["lid"],$tmpDiv)=$oOnload->getModiNoteConDiv("elem_sumOdLids", $modi_note_LidsOd);
			//echo $tmpDiv;
			$arrDivArcCmn["Lids"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["lid"]="";
		}
		if(!empty($modi_note_LidsOs) && empty($moeArc["os"]["lid"])){ //Os
			list($moeMN["os"]["lid"],$tmpDiv)=$oOnload->getModiNoteConDiv("elem_sumOsLids", $modi_note_LidsOs);
			//echo $tmpDiv;
			$arrDivArcCmn["Lids"]["OS"]=$tmpDiv;
		}else{
			$moeMN["os"]["lid"]="";
		}
		*/
		//Modified Notes ----		
		
		$arr=array();
		
		//Lids
		$arr["subExm"][0] = $oOnload->getArrExms_ms(array("enm"=>"Lids",
										"sOd"=>$elem_sumLidsOd,"sOs"=>$elem_sumLidsOs,
										"fOd"=>$flgSe_Lids_Od,"fOs"=>$flgSe_Lids_Os,"pos"=>$elem_posLids,
										//"arcJsOd"=>$moeArc["od"]["lid"],"arcJsOs"=>$moeArc["os"]["lid"],
										"arcCssOd"=>$flgArcColor["od"]["lid"],"arcCssOs"=>$flgArcColor["os"]["lid"],
										//"mnOd"=>$moeMN["od"]["lid"],"mnOs"=>$moeMN["os"]["lid"],
										"enm_2"=>"Lids"));
		//Sub Exam List
		$arr["seList"] = array("Lids"=>array("enm"=>"Lids","pos"=>$elem_posLids,
										"wOd"=>$elem_wnlLidsOd,"wOs"=>$elem_wnlLidsOs));
		
		$arr["bgColor"] = "".$bgColor_lids;
		$arr["nochange"] = $elem_ncLids;
		$arr["examdate"] = $examdate;		
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_Lids_Od,$flgSe_Lids_Os);
		
		//$arr["arrDivArcCmn"] = $arrDivArcCmn;
		$arr["arrHx"] = $arrHx;		
		//---------
		
		return $arr;
	}
	
	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		//WNL
		$wnl_value_Lids = $this->getExamWnlStr("Lids");		
		
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_Lids)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_Lids)."') ";
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
					"ncLids,".
					"ncLids_od,".
					"ncLids_os,".
					"wnl_value_Lids";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,"",'id');
	}

	function save_wnl($op="get", $arv=array()){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$exmEye = $_POST["elem_exmEye"];
		if($op=="get"){
			$ar_ret=array();
			$wnl_value_Lids="";
			$wnl_value_Lids_phrase="";
			$flgCarry=0;
			
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}
			
			$cQry = "select 
					wnlLids , 
					wnlLidsOd , 
					wnlLidsOs, 
					posLids,
					lid_conjunctiva_summary,
					sumLidsOs,
					statusElem as statusElemLids , wnl_value_Lids
					FROM ".$this->tbl." WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
			}else{
				$ar_ret=$row;			
			}
		
			return $ar_ret;			
		}else if($op=="set" && count($arv)){
			extract($arv);
			
			//
			$oWv = new WorkView();		
			
			//Toggle --
			if((!empty($statusElemLids)&&strpos($statusElemLids,"0")===false)||
				(empty($lid_conjunctiva_summary) && empty($wnlLidsOd))||
				(empty($sumLidsOs) && empty($wnlLidsOs))
				 ){
			//Toggle Lids
			list($wnlLidsOd,$wnlLidsOs,$wnlLids) =
						$oWv->toggleWNL($posLids,$lid_conjunctiva_summary,$sumLidsOs,$wnlLidsOd,$wnlLidsOs,$wnlLids,$exmEye);
			}
			
			//Status
			$statusElem_LA_prev=$statusElemLids;
			$statusElemLids = $this->setEyeStatus($this->examName, $exmEye,$statusElemLids,0);
			
			//getWnlValue
			if(empty($wnl_value_Lids)){
				$wnl_value_Lids=$this->getExamWnlStr("Lids");
				$wnl_value_Lids_phrase = ", wnl_value_Lids='".sqlEscStr($wnl_value_Lids)."' ";				
			}
			
			$sql = "UPDATE ".$this->tbl." SET
				 wnlLids='".$wnlLids."',
				 wnlLidsOd='".$wnlLidsOd."',
				 wnlLidsOs='".$wnlLidsOs."',
				 exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
				 statusElem='".$statusElemLids."'
				".$wnl_value_Lids_phrase." "."
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
					$ignoreFlds .= "lid_conjunctiva_summary,
								lid_od,
								wnlLidsOd,
								ncLids_od,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "lid_os,sumLidsOs,
								wnlLidsOs,
								ncLids_os,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posLids,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Lids,ut_elem,";}
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
						"ncLids,".
						"ncLids_od,".
						"ncLids_os,".
						"wnl_value_Lids";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}	
	}
	
	function isNoChanged(){
		$res= $this->getRecord("ncLids,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncLids"]) ){
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
		$elem_ncLids="0";
		$elem_ncLids_od="0";
		$elem_ncLids_os="0";
		
		//
		$oLA=$this; 
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
				$elem_ncLids="1";
				$elem_ncLids_od="1";
				$elem_ncLids_os="1";
			}else if($exmEye=="OD"){				
				$elem_ncLids_od="1";
			}else if($exmEye=="OS"){
				$elem_ncLids_os="1";
			}
		}			
		// ---
		
		//Get status string --
		$statusElem="";
		if($elem_ncLids_od==1||$elem_ncLids_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncLids_od,$elem_ncLids_os,0);}
		//Get status--
		
		//
		$sql = "UPDATE ".$this->tbl."
			  SET			  
			  ncLids = '".$elem_ncLids."',
			  ncLids_od='".$elem_ncLids_od."', ncLids_os='".$elem_ncLids_os."',			  
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);	
	}
	
	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "lid_od";
		$arr["db"]["xmlOs"] = "lid_os";
		$arr["db"]["wnlSE"] = "wnlLids";
		$arr["db"]["wnlOd"] = "wnlLidsOd";
		$arr["db"]["wnlOs"] = "wnlLidsOs";
		$arr["db"]["posSE"] = "posLids";
		$arr["db"]["summOd"] = "lid_conjunctiva_summary";
		$arr["db"]["summOs"] = "sumLidsOs";
		$arr["divSe"] = "1";
		//--
		
		$oExamXml = new ExamXml();
		$tmp = $oExamXml->getExamXmlFiles("LA", $this->examName);
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