<?php

class LidPos extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_lid_pos";
		$this->examName="LidPos";
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

	function get_chart_lid_pos_info($elem_dos){
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;
		$ar_ret = array();
		$sql = "SELECT * FROM ".$this->tbl."  WHERE form_id='$form_id' AND patient_id='".$patient_id."' AND purged = '0' ";
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
			$res = $this->getLastRecord(" * ",0,$elem_dos,"chart_lid_pos");
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}

		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			$res = $this->getLastRecord(" * ",1,$elem_dos,"chart_lid_pos");
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
			$ar_ret["elem_lidPosId_LF"]=$ar_ret["elem_lidPosId"]=$row["id"];
			$ar_ret["elem_editModeLidPos"] = $elem_editMode;

			//NC
			if($elem_editMode==1){
				$ar_ret["elem_ncLidPos"]=$row["ncLidPos"];
				$ar_ret["elem_examDateLidPos"]=$row["exam_date"];
			}

			$lidposition_od= stripslashes($row["lidposition_od"]);
			$lidposition_os= stripslashes($row["lidposition_os"]);

			$arr_vals_lidposition_od = $oExamXml->extractXmlValue($lidposition_od);
			$ar_ret = array_merge($ar_ret, $arr_vals_lidposition_od);

			$arr_vals_lidposition_os = $oExamXml->extractXmlValue($lidposition_os);
			$ar_ret = array_merge($ar_ret, $arr_vals_lidposition_os);

			$ar_ret["elem_wnlLidPos"]=$row["wnlLidPos"];
			$ar_ret["elem_posLidPos"]=$row["posLidPos"];
			$ar_ret["lid_deformity_position"] = $row["lid_deformity_position"];
			$ar_ret["elem_wnlLidPosOd"]=$row["wnlLidPosOd"];
			$ar_ret["elem_wnlLidPosOs"]=$row["wnlLidPosOs"];
			$ar_ret["elem_statusElementsLidPos"] = ($elem_editMode==0) ? "" : $row["statusElem"];

			//UT Elems
			$ar_ret["elem_utElemsLidPos"] = ($elem_editMode==1) ? $row["ut_elem"] : "" ;

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
		$tmpOd = "elem_chng_div3_Od";
		$tmpOs = "elem_chng_div3_Os";
		$$tmpOd = $_POST[$tmpOd];
		$$tmpOs = $_POST[$tmpOs];
		$arrSe[$tmpOd] = ($$tmpOd == "1") ? "1" : "0";
		$arrSe[$tmpOs] = ($$tmpOs == "1") ? "1" : "0";
		$statusElem = $oChartNoteSaver->getStrSe($arrSe);

		//LidPos--------------
		$wnlLidPos = $posLidPos = $ncLidPos = $wnlLidPosOd = $wnlLidPosOs = "0";
		//if(!empty($elem_chng_div3_Od) || !empty($elem_chng_div3_Os)){
		//	if(!empty($elem_chng_div3_Od)){
				$menuName = "lidpositionOd";
				$menuFilePath = $arXmlFiles["lidpos"]["od"]; //dirname(__FILE__)."/xml/lidposition_od.xml";
				$elem_lidposition_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLidPosOd = $_POST["elem_wnlLidPosOd"];
		//	}

		//	if(!empty($elem_chng_div3_Os)){
				$menuName = "lidpositionOs";
				$menuFilePath = $arXmlFiles["lidpos"]["os"]; //dirname(__FILE__)."/xml/lidposition_os.xml";
				$elem_lidposition_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
				$wnlLidPosOs = $_POST["elem_wnlLidPosOs"];
		///	}

			$wnlLidPos = (!empty($wnlLidPosOd) && !empty($wnlLidPosOs)) ? "1" : "0"; //$_POST["elem_wnlLidPos"];
			$posLidPos=$_POST["elem_posLidPos"];
			$ncLidPos=$_POST["elem_ncLidPos"];
		//}
		//LidPos--------------

		$examDate = wv_dt("now"); //$_POST["elem_examDateLids"];

		//
		$oUserAp = new UserAp();

		//Summary --
		$sumLidPosOd =  "";
		$sumLidPosOs =  "";
		$strExamsAllOd = $strExamsAllOs = "";

		//lidPos
		$lidposition_od = $elem_lidposition_od;

		$arrTemp = $this->getExamSummary($lidposition_od);
		$sumLidPosOd = $arrTemp["Summary"];
		$arrExmDone_od = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div3_Od"])){
			$strExamsAllOd .= $oUserAp->refineByConsoleSymp("LidPos",$arrExmDone_od,$sumLidPosOd);
		}

		$lidposition_os = $elem_lidposition_os;

		$arrTemp = $this->getExamSummary($lidposition_os);
		$sumLidPosOs = $arrTemp["Summary"];
		$arrExmDone_os = $arrTemp["ExmDone"];
		if(!empty($arrSe["elem_chng_div3_Os"])){
			$strExamsAllOs .= $oUserAp->refineByConsoleSymp("LidPos",$arrExmDone_os,$sumLidPosOs);
		}
		//Summary --

		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElemsLidPos"];
		$elem_utElems_cur = $_POST["elem_utElemsLidPos_cur"];
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

			$elem_lidposition_od = sqlEscStr($elem_lidposition_od);
			$elem_lidposition_os = sqlEscStr($elem_lidposition_os);

			//check
			$cQry = "select
						id,last_opr_id,uid,
						lid_deformity_position_summary,wnlLidPosOd,sumLidPosOs,wnlLidPosOs,modi_note_LidPosArr,wnl_value_LidPos,
						exam_date
					FROM ".$this->tbl." WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
			$row = sqlQuery($cQry);
			if($row == false){
				$last_opr_id = $_SESSION["authId"];
				$elem_editMode =  "0";
			}else{
				$lidposId=$lidposIDExam = $row["id"];
				$elem_editMode =  "1";

				//Modifying Notes----------------
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				//$modi_note_LidPosOd=$owv->getModiNotes($row["lid_deformity_position_summary"],$row["wnlLidPosOd"],$sumLidPosOd,$wnlLidPosOd,$row["uid"], $row["wnl_value_LidPos"]);
				//$modi_note_LidPosOs=$owv->getModiNotes($row["sumLidPosOs"],$row["wnlLidPosOs"],$sumLidPosOs,$wnlLidPosOs,$row["uid"], $wnlString_LidPos, $row["wnl_value_LidPos"]);
				$seri_modi_note_LidPosArr = $owv->getModiNotesArr($row["lid_deformity_position_summary"],$sumLidPosOd,$last_opr_id,'OD',$row["modi_note_LidPosArr"],$row['exam_date']);
				$seri_modi_note_LidPosArr = $owv->getModiNotesArr($row["sumLidPosOs"],$sumLidPosOs,$last_opr_id,'OS',$seri_modi_note_LidPosArr,$row['exam_date']);
				//Modifying Notes----------------

			}

			//
			$sql_con = "
				lidposition_od='$elem_lidposition_od',
				lidposition_os='$elem_lidposition_os',
				wnlLidPos='$wnlLidPos',
				posLidPos='$posLidPos',
				ncLidPos='$ncLidPos',
				lid_deformity_position_summary='$sumLidPosOd',
				sumLidPosOs='$sumLidPosOs',
				wnlLidPosOd='$wnlLidPosOd',
				wnlLidPosOs='$wnlLidPosOs',
				uid = '".$_SESSION["authId"]."',
				statusElem = '".$statusElem."',
				ut_elem = '".$ut_elem."',
				last_opr_id = '".$last_opr_id."',
				modi_note_LidPosArr = '".sqlEscStr($seri_modi_note_LidPosArr)."'

			";


			//
			if($elem_editMode == "0"){
				//WNL
				$wnl_value_LidPos = $this->getExamWnlStr("Lid Position");

				// Insert
				$sql1 = "INSERT INTO ".$this->tbl."
					 set
					form_id='".$formId."',
					patient_id='".$patientid."',
					wnl_value_LidPos='".sqlEscStr($wnl_value_LidPos)."',
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
				$insertId = $lidposId;
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
				 "c7.posLidPos,  ".
				 "c7.wnlLidPos,  ".
				 "c7.ncLidPos, ".
				 " c7.lid_deformity_position_summary, ".
				 " c7.sumLidPosOs,  c7.id, ".
				 " c7.wnlLidPosOd,  ".
				 " c7.wnlLidPosOs,  ".
				 //" c7.modi_note_LidPosOd,  ".
				 //" c7.modi_note_LidPosOs,  ".
				 "c7.statusElem AS se_lidpos, c7.exam_date, c7.purgerId, c7.purgeTime, ".
				 "c7.modi_note_LidPosArr, c7.wnl_value_LidPos ".
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
			$elem_posLidPos=assignZero($row["posLidPos"]);
			$elem_wnlLidPos=assignZero($row["wnlLidPos"]);
			$elem_ncLidPos=assignZero($row["ncLidPos"]);
			$elem_wnlLidPosOd = $row["wnlLidPosOd"];
			$elem_wnlLidPosOs = $row["wnlLidPosOs"];
			$elem_se_lidpos = $row["se_lidpos"];
			$elem_sumLidPosOd = $row["lid_deformity_position_summary"];
			$elem_sumLidPosOs = $row["sumLidPosOs"];
			$elem_lidpos_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			//$modi_note_LidPosOd = $row["modi_note_LidPosOd"];
			//$modi_note_LidPosOs = $row["modi_note_LidPosOs"];
			$modi_note_LidPosArr = unserialize($row["modi_note_LidPosArr"]);
			$arrHx = array();
			if(count($modi_note_LidPosArr)>0 && $row["modi_note_LidPosArr"]!='')
			$arrHx['Lid Position'] = $modi_note_LidPosArr;
			$elem_wnl_value_LidPos=$row["wnl_value_LidPos"];

			//---------
			$flgSe_LidPos_Od = $flgSe_LidPos_Os = "0";
			$tmpArrSe=array();
			if(!isset($bgColor_lidpos)){
				if(!empty($elem_se_lidpos)){
					$tmpArrSe = $this->se_elemStatus($this->examName,"0",$elem_se_lidpos);
					$flgSe_LidPos_Od = $tmpArrSe["3"]["od"];
					$flgSe_LidPos_Os = $tmpArrSe["3"]["os"];
				}
			}else{
				if(!empty($elem_se_lidpos_prev)){
					$tmpArrSe_prev = $this->se_elemStatus($this->examName,"0",$elem_se_lidpos_prev);
					$flgSe_LidPos_Od_prev = $tmpArrSe_prev["3"]["od"];
					$flgSe_LidPos_Os_prev = $tmpArrSe_prev["3"]["os"];
				}
			}

			//WNL
			//LidPos --
			$wnlString_LidPos = !empty($elem_wnl_value_LidPos) ? $elem_wnl_value_LidPos : $this->getExamWnlStr("Lid Position", $patient_id, $form_id);
			$wnlStringOd_LidPos = $wnlStringOs_LidPos = $wnlString_LidPos;

			if(empty($flgSe_LidPos_Od) && empty($flgSe_LidPos_Od_prev) && !empty($elem_wnlLidPosOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lid Position", "OD"); if(!empty($tmp)){ $wnlStringOd_LidPos = $tmp;}  }
			if(empty($flgSe_LidPos_Os) && empty($flgSe_LidPos_Os_prev) && !empty($elem_wnlLidPosOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lid Position", "OS"); if(!empty($tmp)){ $wnlStringOs_LidPos = $tmp;}  }

			list($elem_sumLidPosOd,$elem_sumLidPosOs) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_LidPos,"wValOs"=>$wnlStringOs_LidPos,
									"wOd"=>$elem_wnlLidPosOd,"sOd"=>$elem_sumLidPosOd,
									"wOs"=>$elem_wnlLidPosOs,"sOs"=>$elem_sumLidPosOs));

			//Nochanged
			if(!empty($elem_se_lidpos)&&strpos($elem_se_lidpos,"=1")!==false){
				$elem_ncLidPos=1;
			}

			$arr=array();
			//Lid Position
			$arr["subExm"][2] = $oOnload->getArrExms_ms(array("enm"=>"Lid Position",
										"sOd"=>$elem_sumLidPosOd,"sOs"=>$elem_sumLidPosOs,
										"fOd"=>$flgSe_LidPos_Od,"fOs"=>$flgSe_LidPos_Os,"pos"=>$elem_posLidPos,
										//"arcJsOd"=>$moeArc["od"]["lidPosition"],"arcJsOs"=>$moeArc["os"]["lidPosition"],
										"arcCssOd"=>$flgArcColor["od"]["lidPosition"],"arcCssOs"=>$flgArcColor["os"]["lidPosition"],
										//"mnOd"=>$moeMN["od"]["lidPosition"],"mnOs"=>$moeMN["os"]["lidPosition"],
										"enm_2"=>"LidPos"));
			//Sub Exam List
			$arr["seList"] = 	array("LidPos"=>array("enm"=>"Lid Position","pos"=>$elem_posLidPos,
											"wOd"=>$elem_wnlLidPosOd,"wOs"=>$elem_wnlLidPosOs)
							);
			$arr["bgColor"] = "".$bgColor_lidpos;
			$arr["nochange"] = $elem_ncLidPos;
			$arr["examdate"] = $examdate;
			$arr["exm_flg_se"] = array($flgSe_LA_Od,$flgSe_LA_Os);
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

		$echo="";
		$sql = "SELECT ".
				 "c7.posLidPos,  ".
				 "c7.wnlLidPos,  ".
				 "c7.ncLidPos, ".
				 " c7.lid_deformity_position_summary, ".
				 " c7.sumLidPosOs,  c7.id, ".
				 " c7.wnlLidPosOd,  ".
				 " c7.wnlLidPosOs,  ".
				 //" c7.modi_note_LidPosOd,  ".
				 //" c7.modi_note_LidPosOs,  ".
				 "c7.statusElem AS se_lidpos, c7.exam_date, ".
				 "c7.modi_note_LidPosArr, c7.wnl_value_LidPos ".
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
			$elem_posLidPos=assignZero($row["posLidPos"]);
			$elem_wnlLidPos=assignZero($row["wnlLidPos"]);
			$elem_ncLidPos=assignZero($row["ncLidPos"]);
			$elem_wnlLidPosOd = $row["wnlLidPosOd"];
			$elem_wnlLidPosOs = $row["wnlLidPosOs"];
			$elem_se_lidpos = $row["se_lidpos"];
			$elem_sumLidPosOd = $row["lid_deformity_position_summary"];
			$elem_sumLidPosOs = $row["sumLidPosOs"];
			$elem_lidpos_id = $row["id"];
			$examdate = wv_formatDate($row["exam_date"]);
			//$modi_note_LidPosOd = $row["modi_note_LidPosOd"];
			//$modi_note_LidPosOs = $row["modi_note_LidPosOs"];
			$modi_note_LidPosArr = unserialize($row["modi_note_LidPosArr"]);
			$arrHx = array();
			if(is_array($modi_note_LidPosArr) && count($modi_note_LidPosArr)>0 && $row["modi_note_LidPosArr"]!='')
			$arrHx['Lid Position'] = $modi_note_LidPosArr;
			$elem_wnl_value_LidPos=$row["wnl_value_LidPos"];
		}

		//Previous
		if(empty($elem_lidpos_id)){
			$tmp = "";

			$tmp .= " c2.posLidPos, ";
			$tmp .= " c2.wnlLidPos,  ";
			$tmp .= " c2.wnlLidPosOd, ";
			$tmp .= " c2.wnlLidPosOs, ";
			$tmp .= " c2.ncLidPos, ";

			$tmp .= " c2.lid_deformity_position_summary, c2.exam_date, ";
			$tmp .= " c2.wnl_value_LidPos,   ";
			$tmp .= " c2.sumLidPosOs, c2.id, c2.statusElem AS se_lidpos ";

			//$res = valNewRecordL_a($patient_id, $tmp);
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
				$elem_sumLidPosOd = $row["lid_deformity_position_summary"];
				$elem_sumLidPosOs = $row["sumLidPosOs"];
				$elem_posLidPos=assignZero($row["posLidPos"]);
				$elem_wnlLidPos=assignZero($row["wnlLidPos"]);
				$elem_ncLidPos_prev=assignZero($row["ncLidPos"]);
				$elem_wnlLidPosOd=assignZero($row["wnlLidPosOd"]);
				$elem_wnlLidPosOs=assignZero($row["wnlLidPosOs"]);
				$examdate = wv_formatDate($row["exam_date"]);
				$elem_wnl_value_LidPos=$row["wnl_value_LidPos"];
				$elem_se_lidpos_prev = $row["se_lidpos"];
			}
			//BG
			$bgColor_lidpos = "bgSmoke";
		}

		//---------
		$flgSe_LidPos_Od = $flgSe_LidPos_Os = "0";
		$tmpArrSe=array();
		if(!isset($bgColor_lidpos)){
			if(!empty($elem_se_lidpos)){
				$tmpArrSe = $this->se_elemStatus($this->examName,"0",$elem_se_lidpos);
				$flgSe_LidPos_Od = $tmpArrSe["3"]["od"];
				$flgSe_LidPos_Os = $tmpArrSe["3"]["os"];
			}
		}else{
			if(!empty($elem_se_lidpos_prev)){
				$tmpArrSe_prev = $this->se_elemStatus($this->examName,"0",$elem_se_lidpos_prev);
				$flgSe_LidPos_Od_prev = $tmpArrSe_prev["3"]["od"];
				$flgSe_LidPos_Os_prev = $tmpArrSe_prev["3"]["os"];
			}
		}

		//WNL
		//LidPos --
		$wnlString_LidPos = !empty($elem_wnl_value_LidPos) ? $elem_wnl_value_LidPos : $this->getExamWnlStr("Lid Position", $patient_id, $form_id);
		$wnlStringOd_LidPos = $wnlStringOs_LidPos = $wnlString_LidPos;

		if(empty($flgSe_LidPos_Od) && empty($flgSe_LidPos_Od_prev) && !empty($elem_wnlLidPosOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Lid Position", "OD"); if(!empty($tmp)){ $wnlStringOd_LidPos = $tmp;}  }
		if(empty($flgSe_LidPos_Os) && empty($flgSe_LidPos_Os_prev) && !empty($elem_wnlLidPosOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Lid Position", "OS"); if(!empty($tmp)){ $wnlStringOs_LidPos = $tmp;}  }

		list($elem_sumLidPosOd,$elem_sumLidPosOs) = $oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd_LidPos,"wValOs"=>$wnlStringOs_LidPos,
								"wOd"=>$elem_wnlLidPosOd,"sOd"=>$elem_sumLidPosOd,
								"wOs"=>$elem_wnlLidPosOs,"sOs"=>$elem_sumLidPosOs));

		//Archive LA -----
		if($bgColor_lidpos != "bgSmoke"){
			$arrDivArcCmn=array();
			$oChartRecArc->setChkTbl($this->tbl);
			$arrInpArc = array(
				"elem_sumOdLidPos"=>array("lid_deformity_position_summary",$elem_sumLidPosOd,"","wnlLidPosOd",$wnlString_LidPos,$modi_note_LidPosOd),
				"elem_sumOsLidPos"=>array("sumLidPosOs",$elem_sumLidPosOs,"","wnlLidPosOs",$wnlString_LidPos,$modi_note_LidPosOs),
			);
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
			//Lid Pos -
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdLidPos"])){
				//echo $arTmpRecArc["div"]["elem_sumOdLidPos"];
				$arrDivArcCmn["Lid Position"]["OD"]=$arTmpRecArc["div"]["elem_sumOdLidPos"];
				$moeArc["od"]["lidPosition"] = $arTmpRecArc["js"]["elem_sumOdLidPos"];
				$flgArcColor["od"]["lidPosition"] = $arTmpRecArc["css"]["elem_sumOdLidPos"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdLidPos"])) $elem_sumLidPosOd = $arTmpRecArc["curText"]["elem_sumOdLidPos"];
			}else{
				$moeArc["od"]["lidPosition"]=$flgArcColor["od"]["lidPosition"]="";
			}
			//OS
			if(!empty($arTmpRecArc["div"]["elem_sumOsLidPos"])){
				//echo $arTmpRecArc["div"]["elem_sumOsLidPos"];
				$arrDivArcCmn["Lid Position"]["OS"]=$arTmpRecArc["div"]["elem_sumOsLidPos"];
				$moeArc["os"]["lidPosition"] = $arTmpRecArc["js"]["elem_sumOsLidPos"];
				$flgArcColor["os"]["lidPosition"] = $arTmpRecArc["css"]["elem_sumOsLidPos"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsLidPos"])) $elem_sumLidPosOs = $arTmpRecArc["curText"]["elem_sumOsLidPos"];
			}else{
				$moeArc["os"]["lidPosition"]=$flgArcColor["os"]["lidPosition"]="";
			}
			//Lid Pos -
		}
		//Archive LA -----

		//Nochanged
		if(!empty($elem_se_lidpos)&&strpos($elem_se_lidpos,"=1")!==false){
			$elem_ncLidPos=1;
		}

		//Modified Notes ----
		/*
		//if Edit is not Done && modified Notes exists
		//lidpos
		if(!empty($modi_note_LidPosOd) && empty($moeArc["od"]["lidPosition"])){ //Od
			list($moeMN["od"]["lidPosition"],$tmpDiv)=$oOnload->getModiNoteConDiv("elem_sumOdLidPos", $modi_note_LidPosOd);
			//echo $tmpDiv;
			$arrDivArcCmn["Lid Position"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["lidPosition"]="";
		}
		if(!empty($modi_note_LidPosOs) && empty($moeArc["os"]["lidPosition"])){ //Os
			list($moeMN["os"]["lidPosition"],$tmpDiv)=$oOnload->getModiNoteConDiv("elem_sumOsLidPos", $modi_note_LidPosOs);
			//echo $tmpDiv;
			$arrDivArcCmn["Lid Position"]["OS"]=$tmpDiv;
		}else{
			$moeMN["os"]["lidPosition"]="";
		}
		*/
		//Modified Notes ----

		$arr=array();
		//Lid Position
		$arr["subExm"][2] = $oOnload->getArrExms_ms(array("enm"=>"Lid Position",
									"sOd"=>$elem_sumLidPosOd,"sOs"=>$elem_sumLidPosOs,
									"fOd"=>$flgSe_LidPos_Od,"fOs"=>$flgSe_LidPos_Os,"pos"=>$elem_posLidPos,
									//"arcJsOd"=>$moeArc["od"]["lidPosition"],"arcJsOs"=>$moeArc["os"]["lidPosition"],
									"arcCssOd"=>$flgArcColor["od"]["lidPosition"],"arcCssOs"=>$flgArcColor["os"]["lidPosition"],
									//"mnOd"=>$moeMN["od"]["lidPosition"],"mnOs"=>$moeMN["os"]["lidPosition"],
									"enm_2"=>"LidPos"));
		//Sub Exam List
		$arr["seList"] = 	array("LidPos"=>array("enm"=>"Lid Position","pos"=>$elem_posLidPos,
										"wOd"=>$elem_wnlLidPosOd,"wOs"=>$elem_wnlLidPosOs)
						);
		$arr["bgColor"] = "".$bgColor_lidpos;
		$arr["nochange"] = $elem_ncLidPos;
		$arr["examdate"] = $examdate;
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_LA_Od,$flgSe_LA_Os);

		//$arr["arrDivArcCmn"] = $arrDivArcCmn;
		$arr["arrHx"] = $arrHx;
		return $arr;
	}

	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		//WNL
		$wnl_value_LidPos = $this->getExamWnlStr("Lid Position");

		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id, exam_date, uid, wnl_value_LidPos)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".wv_dt("now")."','".$this->uid."', '".sqlEscStr($wnl_value_LidPos)."') ";
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
					"ncLidPos,".
					"ncLidPos_od,".
					"ncLidPos_os,".
					"wnl_value_LidPos";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,"",'id');
	}

	function save_wnl($op="get", $arv=array()){
		$form_id = $this->fid;
		$patientId = $this->pid;
		$exmEye = $_POST["elem_exmEye"];
		if($op=="get"){
			$wnl_value_LidPos="";
			$wnl_value_LidPos_phrase="";
			$flgCarry=0;

			if(!$this->isRecordExists()){
				$this->carryForward();
				$flgCarry=1;
			}

			$cQry = "select
					wnlLidPos,
					wnlLidPosOd,
					wnlLidPosOs,
					posLidPos,
					lid_deformity_position_summary,
					sumLidPosOs,
					statusElem as statusElemLidPos, wnl_value_LidPos
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
			if((!empty($statusElemLidPos)&&strpos($statusElemLidPos,"0")===false)||
				(empty($lid_deformity_position_summary) && empty($wnlLidPosOd))||
				(empty($sumLidPosOs) && empty($wnlLidPosOs))
				 ){
			//Toggle LidPos
			list($wnlLidPosOd,$wnlLidPosOs,$wnlLidPos) =
									$oWv->toggleWNL($posLidPos,$lid_deformity_position_summary,$sumLidPosOs,
												$wnlLidPosOd,$wnlLidPosOs,$wnlLidPos,$exmEye);
			}

			//Status
			$statusElem_LA_prev=$statusElemLidPos;
			$statusElemLidPos = $this->setEyeStatus($this->examName, $exmEye,$statusElemLidPos,0);
			//Toggle --

			//getWnlValue
			if(empty($wnl_value_LidPos)){
				$wnl_value_LidPos=$this->getExamWnlStr("Lid Position");
				$wnl_value_LidPos_phrase = ", wnl_value_LidPos='".sqlEscStr($wnl_value_LidPos)."' ";
			}

			$sql = "UPDATE ".$this->tbl." SET
				 wnlLidPos='".$wnlLidPos."',
				 wnlLidPosOd='".$wnlLidPosOd."',
				 wnlLidPosOs='".$wnlLidPosOs."',
				 exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
				 statusElem='".$statusElemLidPos."'
				 ".$wnl_value_LidPos_phrase." "."
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
					$ignoreFlds .= "lid_deformity_position_summary,
								lidposition_od,
								wnlLidPosOd,
								ncLidPos_od,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Os=1","_Os=0",$statusElem);  }
				}else if($_POST["site"]=="OD"){
					$ignoreFlds .= "lidposition_os,
								wnlLidPosOs,
								ncLidPos_os,
								";
					if(!empty($statusElem)){ $statusElem = str_replace("_Od=1","_Od=0",$statusElem);  }
				}
				if($_POST["site"]=="OD" || $_POST["site"]=="OS"){
					$ignoreFlds .= "posLidPos,";
					if($is_cryfd==0){$ignoreFlds .= "wnl_value_LidPos,ut_elem,";}
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
						"ncLidPos,".
						"ncLidPos_od,".
						"ncLidPos_os,".
						"wnl_value_LidPos";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values
			}
		}
	}

	function isNoChanged(){
		$res= $this->getRecord("ncLidPos,statusElem");
		if($res!=false){
			if( !empty($res["statusElem"]) && strpos($res["statusElem"],"=1")!==false){
				return true;
			}
			if( !empty($res["ncLidPos"]) ){
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
		$elem_ncLidPos="0";
		$elem_ncLidPos_od="0";
		$elem_ncLidPos_os="0";

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
				$elem_ncLidPos="1";
				$elem_ncLidPos_od="1";
				$elem_ncLidPos_os="1";
			}else if($exmEye=="OD"){
				$elem_ncLidPos_od="1";
			}else if($exmEye=="OS"){
				$elem_ncLidPos_os="1";
			}
		}
		// ---

		//Get status string --
		$statusElem="";
		if($elem_ncLidPos_od==1||$elem_ncLidPos_os==1){$statusElem=$this->se_elemStatus($w,"1","",$elem_ncLidPos_od,$elem_ncLidPos_os,0);}
		//Get status--

		//
		$sql = "UPDATE ".$this->tbl."
			  SET
			  ncLidPos = '".$elem_ncLidPos."',
			  ncLidPos_od = '".$elem_ncLidPos_od."', ncLidPos_os = '".$elem_ncLidPos_os."',
			  exam_date='".wv_dt("now")."', uid='".$_SESSION["authId"]."',
			  statusElem='".$statusElem."'
			 WHERE form_id='".$form_id."' AND patient_id='".$patientId."'  AND purged = '0' ";
		$res = sqlQuery($sql);

	}

	function getSubExamInfo(){
		$arr=array();
		$arr["db"]["xmlOd"] = "lidposition_od";
		$arr["db"]["xmlOs"] = "lidposition_os";
		$arr["db"]["wnlSE"] = "wnlLidPos";
		$arr["db"]["wnlOd"] = "wnlLidPosOd";
		$arr["db"]["wnlOs"] = "wnlLidPosOs";
		$arr["db"]["posSE"] = "posLidPos";
		$arr["db"]["summOd"] = "lid_deformity_position_summary";
		$arr["db"]["summOs"] = "sumLidPosOs";
		$arr["divSe"] = "3";

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
