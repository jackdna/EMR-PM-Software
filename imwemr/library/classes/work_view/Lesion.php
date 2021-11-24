<?php

class Lesion extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_lesion";
		$this->examName="Lesion";
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
	
	function get_chart_lesion_info($elem_dos){
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;
		$ar_ret = array();
		$sql = "SELECT * FROM ".$this->tbl." WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' AND purged = '0' ";
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
			$res = $this->getLastRecord(" * ",0,$elem_dos,"chart_lesion");
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}
		
		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){			
			$res = $this->getLastRecord(" * ",1,$elem_dos,"chart_lesion");
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
			$ar_ret["elem_lesionId_LF"]=$ar_ret["elem_lesionId"]=$row["id"];
			$ar_ret["elem_editModeLesion"] = $elem_editMode;

			//NC
			if($elem_editMode==1){
				$ar_ret["elem_ncLesion"]=$row["ncLesion"];				
				$ar_ret["elem_examDateLesion"]=$row["exam_date"];
			}
			
			$lesion_od= stripslashes($row["lesion_od"]);
			$lesion_os= stripslashes($row["lesion_os"]);			
			
			$arr_vals_lesion_od = $oExamXml->extractXmlValue($lesion_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_lesion_od);
			
			$arr_vals_lesion_os = $oExamXml->extractXmlValue($lesion_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_lesion_os);			
			
			$ar_ret["elem_wnlLesion"]=$row["wnlLesion"];		
			$ar_ret["elem_posLesion"]=$row["posLesion"];
			//$ar_ret["lesion"] = $row["lesion"];
			$ar_ret["elem_wnlLesionOd"]=$row["wnlLesionOd"];
			$ar_ret["elem_wnlLesionOs"]=$row["wnlLesionOs"];
			$ar_ret["elem_statusElementsLesion"] = ($elem_editMode==0) ? "" : $row["statusElem"];
			//UT Elems 
			$ar_ret["elem_utElemsLesion"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;
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
		$tmpOd = "elem_chng_div2_Od";
		$tmpOs = "elem_chng_div2_Os";
		$$tmpOd = $_POST[$tmpOd];
		$$tmpOs = $_POST[$tmpOs];
		$arrSe[$tmpOd] = ($$tmpOd == "1") ? "1" : "0";
		$arrSe[$tmpOs] = ($$tmpOs == "1") ? "1" : "0";		
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);
		
		//Lesison -------------
		$wnlLesion = $posLesion = $ncLesion = $wnlLesionOd = $wnlLesionOs ="0";
		//if(!empty($elem_chng_div2_Od) || !empty($elem_chng_div2_Os)){
		//	if(!empty($elem_chng_div2_Od)){
				$menuName = "lesionOd";
				$menuFilePath = $arXmlFiles["lesion"]["od"]; //dirname(__FILE__)."/xml/lesion_od.xml";
				$elem_lesion_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLesionOd = $_POST["elem_wnlLesionOd"];
		//	}

		//	if(!empty($elem_chng_div2_Os)){
				$menuName = "lesionOs";
				$menuFilePath = $arXmlFiles["lesion"]["os"]; //dirname(__FILE__)."/xml/lesion_os.xml";
				$elem_lesion_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLesionOs = $_POST["elem_wnlLesionOs"];
		//	}

			$wnlLesion = (!empty($wnlLesionOd) && !empty($wnlLesionOs)) ? "1" : "0"; //$_POST["elem_wnlLesion"];
			$posLesion=$_POST["elem_posLesion"];
			$ncLesion=$_POST["elem_ncLesion"];
		//}

		//Lesison -------------
		
		$examDate = wv_dt("now"); //$_POST["elem_examDateLids"];
		
		$oUserAp = new UserAp();
		
		//Summary --
		$sumLesionOd = "";
		$sumLesionOs = "";
		$strExamsAllOd = $strExamsAllOs = "";
			
		//Lesion
		$lesion_od= $elem_lesion_od;
		
		$arrTemp = $this->getExamSummary($lesion_od);
		$sumLesionOd = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div2_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Lesion",$arrExmDone_od,$sumLesionOd);
		}

		$lesion_os= $elem_lesion_os;
		$arrTemp = $this->getExamSummary($lesion_os);
		$sumLesionOs = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div2_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Lesion",$arrExmDone_os,$sumLesionOs);
		}

		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsLesion"];
		$elem_utElems_cur = $_POST["elem_utElemsLesion_cur"];
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
			
			$elem_lesion_od = sqlEscStr($elem_lesion_od);
			$elem_lesion_os = sqlEscStr($elem_lesion_os);
			
			//check
			$cQry = "select 
						id,last_opr_id,uid,						
						wnl_value_Lesion,lesion_summary,wnlLesionOd,sumLesionOs,wnlLesionOs,modi_note_LesionArr,
						exam_date
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$last_opr_id = $_SESSION["authId"];
				$elem_editMode =  "0";
			}else{
				$lesionId=$lesionIDExam = $row["id"];
				$elem_editMode =  "1";
				
				//Modifying Notes----------------
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				
				//$modi_note_LesionOd=$owv->getModiNotes($row["lesion_summary"],$row["wnlLesionOd"],$sumLesionOd,$wnlLesionOd,$row["uid"], $row["wnl_value_Lesion"]);
				//$modi_note_LesionOs=$owv->getModiNotes($row["sumLesionOs"],$row["wnlLesionOs"],$sumLesionOs,$wnlLesionOs,$row["uid"], $row["wnl_value_Lesion"]);
					
				$seri_modi_note_LesionArr = $owv->getModiNotesArr($row["lesion_summary"],$sumLesionOd,$last_opr_id,'OD',$row["modi_note_LesionArr"],$row['exam_date']);
				$seri_modi_note_LesionArr = $owv->getModiNotesArr($row["sumLesionOs"],$sumLesionOs,$last_opr_id,'OS',$seri_modi_note_LesionArr,$row['exam_date']);
				
				//Modifying Notes----------------	
					
				
			}
			
			$sql_con = "
				lesion_od='$elem_lesion_od',
				lesion_os='$elem_lesion_os',
				wnlLesion='$wnlLesion',
				posLesion='$posLesion',
				ncLesion='$ncLesion',
				lesion_summary='$sumLesionOd',
				sumLesionOs='$sumLesionOs',
				wnlLesionOd='$wnlLesionOd',
				wnlLesionOs='$wnlLesionOs',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_LesionArr = '".sqlEscStr($seri_modi_note_LesionArr)."'
			";
			
			//
			if($elem_editMode == "0"){
				//WNL
				$wnl_value_Lesion = $this->getExamWnlStr("Lesion");
				
				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',	
					wnl_value_Lesion='".sqlEscStr($wnl_value_Lesion)."', 
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
				 "c7.posLesion, ".
				 "c7.wnlLesion, ".
				 "c7.ncLesion, ".				 
				 "c7.lesion_summary, ".
				 "c7.sumLesionOs, c7.id, ".
				 "c7.wnlLesionOd, ".
				 "c7.wnlLesionOs, ".						 
				 //"c7.modi_note_LesionOd, ".
				 //"c7.modi_note_LesionOs, ".
				 "c7.statusElem AS se_lesion, c7.exam_date, c7.purgerId, c7.purgeTime, ".				 
				 "c7.modi_note_LesionArr, c7.wnl_value_Lesion ".				 
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
			$elem_posLesion=assignZero($row["posLesion"]);
			$elem_wnlLesion=assignZero($row["wnlLesion"]);
			$elem_ncLesion=assignZero($row["ncLesion"]);
			$elem_wnlLesionOd = $row["wnlLesionOd"];
			$elem_wnlLesionOs = $row["wnlLesionOs"];
			$elem_se_lesion = $row["se_lesion"];
			$elem_sumLesionOd = $row["lesion_summary"];
			$elem_sumLesionOs = $row["sumLesionOs"];
			$elem_lesion_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			//$modi_note_LesionOd = $row["modi_note_LesionOd"];
			//$modi_note_LesionOs = $row["modi_note_LesionOs"];
			$modi_note_LesionArr = unserialize($row["modi_note_LesionArr"]);
			$arrHx = array();
			if(count($modi_note_LesionArr)>0 && $row["modi_note_LesionArr"]!='')
			$arrHx['Lesion'] = $modi_note_LesionArr;
			$elem_wnl_value_Lesion=$row["wnl_value_Lesion"];
			
			//---------
			$flgSe_Lesion_Od = $flgSe_Lesion_Os = "0";
			$tmpArrSe=array();
			if(!isset($bgColor_la)){
				if(!empty($elem_se_lesion)){
					$tmpArrSe = $this->se_elemStatus($this->examName,"0",$elem_se_lesion);
					$flgSe_Lesion_Od = $tmpArrSe["2"]["od"];
					$flgSe_Lesion_Os = $tmpArrSe["2"]["os"];
				}
			}else{
				if(!empty($elem_se_La_prev)){
					$tmpArrSe_prev = $this->se_elemStatus($this->examName,"0",$elem_se_lesion_prev);
					$flgSe_Lesion_Od_prev = $tmpArrSe_prev["2"]["od"];
					$flgSe_Lesion_Os_prev = $tmpArrSe_prev["2"]["os"];
				}
			}
			
			//WNL
			//Lesion --
			$wnlString_Lesion = !empty($elem_wnl_value_Lesion) ? $elem_wnl_value_Lesion : $this->getExamWnlStr("Lesion");
			$wnlStringOd_Lesion = $wnlStringOs_Lesion = $wnlString_Lesion;
			
			if(empty($flgSe_Lesion_Od) && empty($flgSe_Lesion_Od_prev) && !empty($elem_wnlLesionOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lesion", "OD"); if(!empty($tmp)){ $wnlStringOd_Lesion = $tmp;}  }
			if(empty($flgSe_Lesion_Os) && empty($flgSe_Lesion_Os_prev) && !empty($elem_wnlLesionOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lesion", "OS"); if(!empty($tmp)){ $wnlStringOs_Lesion = $tmp;}  }
			
			list($elem_sumLesionOd,$elem_sumLesionOs) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Lesion,"wValOs"=>$wnlStringOs_Lesion,
									"wOd"=>$elem_wnlLesionOd,"sOd"=>$elem_sumLesionOd,
									"wOs"=>$elem_wnlLesionOs,"sOs"=>$elem_sumLesionOs));
			//Nochanged
			if(!empty($elem_se_lesion)&&strpos($elem_se_lesion,"=1")!==false){
				$elem_ncLesion=1;
			}						
			
			$arr=array();		
		
			//Lesion
			$arr["subExm"][1] = $oOnload->getArrExms_ms(array("enm"=>"Lesion",
											"sOd"=>$elem_sumLesionOd,"sOs"=>$elem_sumLesionOs,
											"fOd"=>$flgSe_Lesion_Od,"fOs"=>$flgSe_Lesion_Os,"pos"=>$elem_posLesion,
											//"arcJsOd"=>$moeArc["od"]["lesion"],"arcJsOs"=>$moeArc["os"]["lesion"],
											"arcCssOd"=>$flgArcColor["od"]["lesion"],"arcCssOs"=>$flgArcColor["os"]["lesion"],
											//"mnOd"=>$moeMN["od"]["lesion"],"mnOs"=>$moeMN["os"]["lesion"],
											"enm_2"=>"Lesion"));
			//Sub Exam List
			$arr["seList"] = 	array(
							"Lesion"=>array("enm"=>"Lesion","pos"=>$elem_posLesion,
											"wOd"=>$elem_wnlLesionOd,"wOs"=>$elem_wnlLesionOs)
							);
			$arr["bgColor"] = "".$bgColor_lesion;
			$arr["nochange"] = $elem_ncLesion;					
			$arr["examdate"] = $examdate;			
			$arr["exm_flg_se"] = array($flgSe_Lesion_Od,$flgSe_Lesion_Os);
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
				 "c7.posLesion, ".
				 "c7.wnlLesion, ".
				 "c7.ncLesion, ".				 
				 "c7.lesion_summary, ".
				 "c7.sumLesionOs, c7.id, ".
				 "c7.wnlLesionOd, ".
				 "c7.wnlLesionOs, ".						 
				 //"c7.modi_note_LesionOd, ".
				 //"c7.modi_note_LesionOs, ".
				 "c7.statusElem AS se_lesion, c7.exam_date, ".				 
				 "c7.modi_note_LesionArr, c7.wnl_value_Lesion ".				 
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
			$elem_posLesion=assignZero($row["posLesion"]);
			$elem_wnlLesion=assignZero($row["wnlLesion"]);
			$elem_ncLesion=assignZero($row["ncLesion"]);
			$elem_wnlLesionOd = $row["wnlLesionOd"];
			$elem_wnlLesionOs = $row["wnlLesionOs"];
			$elem_se_lesion = $row["se_lesion"];
			$elem_sumLesionOd = $row["lesion_summary"];
			$elem_sumLesionOs = $row["sumLesionOs"];
			$elem_lesion_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			//$modi_note_LesionOd = $row["modi_note_LesionOd"];
			//$modi_note_LesionOs = $row["modi_note_LesionOs"];
			$modi_note_LesionArr = unserialize($row["modi_note_LesionArr"]);
			$arrHx = array();
			if(is_array($modi_note_LesionArr) && count($modi_note_LesionArr)>0 && $row["modi_note_LesionArr"]!='')
			$arrHx['Lesion'] = $modi_note_LesionArr;
			$elem_wnl_value_Lesion=$row["wnl_value_Lesion"];
			
		}
		
		//Previous
		if(empty($elem_lesion_id)){
			$tmp = "";
			
			$tmp .= " c2.posLesion,  ";
			$tmp .= " c2.wnlLesion,  ";
			$tmp .= " c2.wnlLesionOd,  ";
			$tmp .= " c2.wnlLesionOs, ";
			$tmp .= " c2.ncLesion, ";			
			$tmp .= "  c2.lesion_summary, c2.exam_date, ";
			$tmp .= "  c2.wnl_value_Lesion, ";
			$tmp .= "  c2.sumLesionOs, c2.id, c2.statusElem AS se_lesion ";
			
			//$res = valNewRecordL_a($patient_id, $tmp);
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			
			if($row!=false){	
				$elem_sumLesionOd = $row["lesion_summary"];
				$elem_sumLesionOs = $row["sumLesionOs"];
				$elem_posLesion=assignZero($row["posLesion"]);
				$elem_wnlLesion=assignZero($row["wnlLesion"]);
				$elem_ncLesion_prev=assignZero($row["ncLesion"]);
				$elem_wnlLesionOd=assignZero($row["wnlLesionOd"]);
				$elem_wnlLesionOs=assignZero($row["wnlLesionOs"]);
				$examdate = wv_formatDate($row["exam_date"]);
				$elem_wnl_value_Lesion=$row["wnl_value_Lesion"];
				$elem_se_lesion_prev = $row["se_lesion"];
			}
			//BG
			$bgColor_lesion = "bgSmoke";
		}
		
		//---------
		$flgSe_Lesion_Od = $flgSe_Lesion_Os = "0";
		$tmpArrSe=array();
		if(!isset($bgColor_la)){
			if(!empty($elem_se_lesion)){
				$tmpArrSe = $this->se_elemStatus($this->examName,"0",$elem_se_lesion);
				$flgSe_Lesion_Od = $tmpArrSe["2"]["od"];
				$flgSe_Lesion_Os = $tmpArrSe["2"]["os"];
			}
		}else{
			if(!empty($elem_se_La_prev)){
				$tmpArrSe_prev = $this->se_elemStatus($this->examName,"0",$elem_se_lesion_prev);
				$flgSe_Lesion_Od_prev = $tmpArrSe_prev["2"]["od"];
				$flgSe_Lesion_Os_prev = $tmpArrSe_prev["2"]["os"];
			}
		}
		
		//WNL
		//Lesion --
		$wnlString_Lesion = !empty($elem_wnl_value_Lesion) ? $elem_wnl_value_Lesion : $this->getExamWnlStr("Lesion");
		$wnlStringOd_Lesion = $wnlStringOs_Lesion = $wnlString_Lesion;
		
		if(empty($flgSe_Lesion_Od) && empty($flgSe_Lesion_Od_prev) && !empty($elem_wnlLesionOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lesion", "OD"); if(!empty($tmp)){ $wnlStringOd_Lesion = $tmp;}  }
		if(empty($flgSe_Lesion_Os) && empty($flgSe_Lesion_Os_prev) && !empty($elem_wnlLesionOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lesion", "OS"); if(!empty($tmp)){ $wnlStringOs_Lesion = $tmp;}  }
		
		list($elem_sumLesionOd,$elem_sumLesionOs) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_Lesion,"wValOs"=>$wnlStringOs_Lesion,
								"wOd"=>$elem_wnlLesionOd,"sOd"=>$elem_sumLesionOd,
								"wOs"=>$elem_wnlLesionOs,"sOs"=>$elem_sumLesionOs));
								
		//Archive LA -----
		if($bgColor_lesion != "bgSmoke"){
			$arrDivArcCmn=array();
			$oChartRecArc->setChkTbl($this->tbl);
			$arrInpArc = array(
						"elem_sumOdLesion"=>array("lesion_summary",$elem_sumLesionOd,"","wnlLesionOd",$wnlString_Lesion,$modi_note_LesionOd),
						"elem_sumOsLesion"=>array("sumLesionOs",$elem_sumLesionOs,"","wnlLesionOs",$wnlString_Lesion,$modi_note_LesionOs)

						);
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);			
			
			//Lesion -
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdLesion"])){
				//echo $arTmpRecArc["div"]["elem_sumOdLesion"];
				$arrDivArcCmn["Lesion"]["OD"]=$arTmpRecArc["div"]["elem_sumOdLesion"];					
				$moeArc["od"]["lesion"] = $arTmpRecArc["js"]["elem_sumOdLesion"];
				$flgArcColor["od"]["lesion"] = $arTmpRecArc["css"]["elem_sumOdLesion"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdLesion"])) $elem_sumLesionOd = $arTmpRecArc["curText"]["elem_sumOdLesion"];
			}else{
				$moeArc["od"]["lesion"]=$flgArcColor["od"]["lesion"]="";
			}
			//OS
			if(!empty($arTmpRecArc["div"]["elem_sumOsLesion"])){
				//echo $arTmpRecArc["div"]["elem_sumOsLesion"];
				$arrDivArcCmn["Lesion"]["OS"]=$arTmpRecArc["div"]["elem_sumOsLesion"];
				$moeArc["os"]["lesion"] = $arTmpRecArc["js"]["elem_sumOsLesion"];
				$flgArcColor["os"]["lesion"] = $arTmpRecArc["css"]["elem_sumOsLesion"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsLesion"])) $elem_sumLesionOs = $arTmpRecArc["curText"]["elem_sumOsLesion"];
			}else{
				$moeArc["os"]["lesion"]=$flgArcColor["os"]["lesion"]="";
			}
			//Lesion -
			
		}
		
		//Nochanged
		if(!empty($elem_se_lesion)&&strpos($elem_se_lesion,"=1")!==false){
			$elem_ncLesion=1;
		}
		
		//Modified Notes ----
		/*
		//if Edit is not Done && modified Notes exists
		//lesion
		if(!empty($modi_note_LesionOd) && empty($moeArc["od"]["lesion"])){ //Od
			list($moeMN["od"]["lesion"],$tmpDiv)=$oOnload->getModiNoteConDiv("elem_sumOdLesion", $modi_note_LesionOd);
			//echo $tmpDiv;
			$arrDivArcCmn["Lesion"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["lesion"]="";
		}
		if(!empty($modi_note_LesionOs) && empty($moeArc["os"]["lesion"])){ //Os
			list($moeMN["os"]["lesion"],$tmpDiv)=$oOnload->getModiNoteConDiv("elem_sumOsLesion", $modi_note_LesionOs);
			//echo $tmpDiv;
			$arrDivArcCmn["Lesion"]["OS"]=$tmpDiv;
		}else{
			$moeMN["os"]["lesion"]="";
		}
		*/
		//Modified Notes ----
		
		$arr=array();		
		
		//Lesion
		$arr["subExm"][1] = $oOnload->getArrExms_ms(array("enm"=>"Lesion",
										"sOd"=>$elem_sumLesionOd,"sOs"=>$elem_sumLesionOs,
										"fOd"=>$flgSe_Lesion_Od,"fOs"=>$flgSe_Lesion_Os,"pos"=>$elem_posLesion,
										//"arcJsOd"=>$moeArc["od"]["lesion"],"arcJsOs"=>$moeArc["os"]["lesion"],
										"arcCssOd"=>$flgArcColor["od"]["lesion"],"arcCssOs"=>$flgArcColor["os"]["lesion"],
										//"mnOd"=>$moeMN["od"]["lesion"],"mnOs"=>$moeMN["os"]["lesion"],
										"enm_2"=>"Lesion"));
		//Sub Exam List
		$arr["seList"] = 	array(
						"Lesion"=>array("enm"=>"Lesion","pos"=>$elem_posLesion,
										"wOd"=>$elem_wnlLesionOd,"wOs"=>$elem_wnlLesionOs)
						);
		$arr["bgColor"] = "".$bgColor_lesion;
		$arr["nochange"] = $elem_ncLesion;					
		$arr["examdate"] = $examdate;		
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_Lesion_Od,$flgSe_Lesion_Os);
		
		//$arr["arrDivArcCmn"] = $arrDivArcCmn;
		$arr["arrHx"] = $arrHx;
		
		return $arr; 
	}
	
	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		//WNL		
		$wnl_value_Lesion = $this->getExamWnlStr("Lesion");		
			
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_Lesion)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_Lesion)."') ";
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
					"ncLesion,".
					"ncLesion_od,".
					"ncLesion_os,".
					"wnl_value_Lesion";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,"",'id');
	}
	
	function save_wnl($op="get", $arv=array()){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$exmEye = $_POST["elem_exmEye"];
		if($op=="get"){
			$ar_ret=array();
			$wnl_value_Lesion="";
			$wnl_value_Lesion_phrase="";
			$flgCarry=0;
			
			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}
			
			$cQry = "select 
					wnlLesion, 
					wnlLesionOd, 
					wnlLesionOs, 
					posLesion,
					lesion_summary,
					sumLesionOs,
					statusElem as statusElemLesion, wnl_value_Lesion
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
			if((!empty($statusElemLesion)&&strpos($statusElemLesion,"0")===false)||
				(empty($lesion_summary) && empty($wnlLesionOd))||
				(empty($sumLesionOs) && empty($wnlLesionOs))
				 ){
			//Toggle Lesion
			list($wnlLesionOd,$wnlLesionOs,$wnlLesion) =
									$oWv->toggleWNL($posLesion,$lesion_summary,$sumLesionOs,
												$wnlLesionOd,$wnlLesionOs,$wnlLesion,$exmEye);
			}
			//Toggle --
			
			//Status
			$statusElem_LA_prev=$statusElemLesion;
			$statusElemLesion = $this->setEyeStatus($this->examName, $exmEye,$statusElemLesion,0);		
			
			//getWnlValue		
			if(empty($wnl_value_Lesion)){
				$wnl_value_Lesion=$this->getExamWnlStr("Lesion");
				$wnl_value_Lesion_phrase = ", wnl_value_Lesion='".sqlEscStr($wnl_value_Lesion)."' ";				
			}		
			
			$sql = "UPDATE ".$this->tbl." SET
				 wnlLesion='".$wnlLesion."',
				 wnlLesionOd='".$wnlLesionOd."',
				 wnlLesionOs='".$wnlLesionOs."',
				 exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
				 statusElem='".$statusElemLesion."'
				  ".$wnl_value_Lesion_phrase." "."
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
					$ignoreFlds .= "lesion_summary,
								lesion_od,
								wnlLesionOd,
								ncLesion_od,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }			
				}else if($_POST["site"]=="OD"){ 
					$ignoreFlds .= "lesion_os,sumLesionOs,
								wnlLesionOs,
								ncLesion_os,
								"; 
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }				
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posLesion,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_Lesion,ut_elem,";}
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
						"ncLesion,".
						"ncLesion_od,".
						"ncLesion_os,".						
						"wnl_value_Lesion";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
		}	
	}
	
	function isNoChanged(){
		$res= $this->getRecord("ncLesion,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncLesion"]) ){
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
		$elem_ncLesion="0";
		$elem_ncLesion_od="0";
		$elem_ncLesion_os="0";
		
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
				$elem_ncLesion="1";
				$elem_ncLesion_od="1";
				$elem_ncLesion_os="1";
			}else if($exmEye=="OD"){				
				$elem_ncLesion_od="1";
			}else if($exmEye=="OS"){
				$elem_ncLesion_os="1";
			}
		}			
		// ---
		
		//Get status string --
		$statusElem="";
		if($elem_ncLesion_od==1||$elem_ncLesion_os==1){$statusElem=$this->se_elemStatus($this->examName,"1","",$elem_ncLesion_od,$elem_ncLesion_os,0);}
		//Get status--
		
		//
		$sql = "UPDATE ".$this->tbl."
			  SET
			  ncLesion = '".$elem_ncLesion."',
			  ncLesion_od='".$elem_ncLesion_od."', ncLesion_os='".$elem_ncLesion_os."',			  
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);
	}
	
	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "lesion_od";
		$arr["db"]["xmlOs"] = "lesion_os";
		$arr["db"]["wnlSE"] = "wnlLesion";
		$arr["db"]["wnlOd"] = "wnlLesionOd";
		$arr["db"]["wnlOs"] = "wnlLesionOs";
		$arr["db"]["posSE"] = "posLesion";
		$arr["db"]["summOd"] = "lesion_summary";
		$arr["db"]["summOs"] = "sumLesionOs";
		$arr["divSe"] = "2";
		
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