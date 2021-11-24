<?php

class ChartNoteSaver extends ChartNote{

	private $arG;

	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
		$this->arG = array();
	}

	function getStrSe($arr){
		$str = "";
		if(count($arr)){
			foreach($arr as $key => $val){
				$str .= $key."=".$val.",";
			}
		}
		return $str;
	}

	function makeArrString($str){
		//$str = substr($str,0,-4);
		$arr = explode("~!!~", $str);
		$arr = array_filter($arr);
		$arr = array_unique($arr);
		//$strTmp = (count($arr)>0) ? "'".implode("','",$arr)."'" : "";
		return $arr;
	}

	function sepLidsOpts($str){
		$arr = explode("~L~", $str);
		return $arr;
	}

	function combineExamFindings($strod, $stros){
		$sep_f= "~!!~";
		$arstrod = explode($sep_f, $strod);
		$arstros = explode($sep_f, $stros);

		$ar_symp=$ar_sev=$ar_loc=array();
		$ar_symp_sev=array(); $ar_symp_lids_op=array();

		$ouap = new UserAp(); //

		$lenod = count($arstrod);
		$lenos = count($arstros);
		$len = ($lenod>$lenos)?$lenod:$lenos;
		for($i=0;$i<$len;$i++){
			$tmpod = $arstrod[$i];
			$tmpos = $arstros[$i];

			list($tmpod, $t_lids_od) = $this->sepLidsOpts($tmpod);
			list($tmpos, $t_lids_os) = $this->sepLidsOpts($tmpos);

			if(!empty($tmpod)){
				list($symp_od, $sev_od, $loc_od) = $ouap->sepSevLocFromSymp($tmpod);
				$ar_symp["symp"]["OD"][]=$symp_od;
				$ar_symp["sev"]["OD"][]=$sev_od;
				$ar_symp["loc"]["OD"][]=$loc_od;
				if(!empty($sev_od)){ $ar_symp_sev[$symp_od][] = $sev_od; $ar_sev[$symp_od]["od"][]=$sev_od;  }
				if(!empty($t_lids_od)){ $ar_symp_lids_op[$symp_od][] = $t_lids_od;}
			}

			if(!empty($tmpos)){
				list($symp_os, $sev_os, $loc_os) = $ouap->sepSevLocFromSymp($tmpos);
				$ar_symp["symp"]["OS"][]=$symp_os;
				$ar_symp["sev"]["OS"][]=$sev_os;
				$ar_symp["loc"]["OS"][]=$loc_os;
				if(!empty($sev_os)){ $ar_symp_sev[$symp_os][] = $sev_os; $ar_sev[$symp_os]["os"][]=$sev_os;  }
				if(!empty($t_lids_os)){ $ar_symp_lids_op[$symp_os][] = $t_lids_os;}
			}
		}

		//--
		$arr_combine=array();$arr_comb_u=array();
		$arr_Tmp=array(); $arr_Tmp_u=array();
		for($i=0;$i<$len;$i++){
			$tmp =$sev_t=$loc_t=$site="";
			$tmp = $ar_symp["symp"]["OD"][$i];
			if(!empty($tmp)){
				if(!in_array($tmp, $arr_Tmp_u)){
				$site="OD";
				if(isset($ar_symp["symp"]["OS"]) && in_array($tmp, $ar_symp["symp"]["OS"])){	$site="OU";	}
				if(isset($ar_symp_lids_op[$tmp]) && count($ar_symp_lids_op[$tmp])>0){
					$t_site_2="";
					foreach($ar_symp_lids_op[$tmp] as $k_site => $v_site){
						if(!empty($v_site)){ if(!empty($t_site_2)){ $t_site_2.=","; }  $t_site_2.=$v_site;  }
					}
					if(!empty($t_site_2)){ $site=$t_site_2;  }
				}

				//
				if(count($ar_symp_sev[$tmp])>0){  $ar_symp_sev[$tmp]=array_unique($ar_symp_sev[$tmp]); $sev_t=implode(",",$ar_symp_sev[$tmp]); }
				$loc_t=$ar_symp["loc"]["OD"][$i];

				if(!empty($tmp)){
					$arr_Tmp[]=array("tmp"=>$tmp, "sev_t"=>$sev_t, "loc_t"=>$loc_t, "site"=>$site);
					$arr_Tmp_u[] = $tmp;
				}
				}
			}

			//else{
				$tmp = $ar_symp["symp"]["OS"][$i];

				if(!empty($tmp)){

					//if already add
					if(!in_array($tmp, $arr_Tmp_u)){

					$site="OS";
					if(in_array($tmp, $ar_symp["symp"]["OD"])){	$site="OU";	}
					if(isset($ar_symp_lids_op[$tmp]) && count($ar_symp_lids_op[$tmp])>0){
						$t_site_2="";
						foreach($ar_symp_lids_op[$tmp] as $k_site => $v_site){
							if(!empty($v_site)){ if(!empty($t_site_2)){ $t_site_2.=","; }  $t_site_2.=$v_site;  }
						}
						if(!empty($t_site_2)){ $site=$t_site_2;  }
					}

					//
					if(count($ar_symp_sev[$tmp])>0){ $ar_symp_sev[$tmp]=array_unique($ar_symp_sev[$tmp]); $sev_t=implode(",",$ar_symp_sev[$tmp]); }
					$loc_t=$ar_symp["loc"]["OS"][$i];

					if(!empty($tmp)){
						$arr_Tmp[]=array("tmp"=>$tmp, "sev_t"=>$sev_t, "loc_t"=>$loc_t, "site"=>$site);
						$arr_Tmp_u[] = $tmp;
					}

					}
				}
			//}

		}

		//
		if(count($arr_Tmp)>0){

		foreach($arr_Tmp as $kk => $arval){ //

			$tmp = $arval["tmp"];
			$sev_t = $arval["sev_t"];
			$loc_t = $arval["loc_t"];
			$site = $arval["site"];

			//
			if(empty($tmp)){continue;}

			if(!in_array($tmp,$arr_comb_u)){

				//loop severity to check its eye
				if(!empty($sev_t)){
					$str_t_sev_l2="";
					$ar_t_sev_l2=array();
					$ar_t_sev=explode(",", $sev_t);
					if(count($ar_t_sev)>0){
						foreach($ar_t_sev as $ks=>$vs){
							$tsite="";
							if(in_array($vs,$ar_sev[$tmp]["od"])&&in_array($vs,$ar_sev[$tmp]["os"])){
								$tsite="OU";
							}elseif(in_array($vs,$ar_sev[$tmp]["od"])){
								$tsite="OD";
							}else if(in_array($vs,$ar_sev[$tmp]["os"])){
								$tsite="OS";
							}
							$ar_t_sev_l2[]=$tsite;
						}
						$str_t_sev_l2=implode(",",$ar_t_sev_l2);
					}
				}

				$str_combine="";
				$str_combine=$tmp."!~!".$sev_t."~!~".$loc_t."~^~".$str_t_sev_l2."~*~".$site;
				$arr_combine[]=$str_combine;
				$arr_comb_u[]=$tmp;
			}
		}
		}
		//--

		$str_combine="";
		if(count($arr_combine)>0){
			$str_combine=implode($sep_f, $arr_combine);
		}

		return $str_combine;
	}

	//
	function resetWNLValuesInPtChart($elem_templateId){

		$pid=$this->pid;
		$fid=$this->fid;


		$chart_db_tables=array("amsler_grid", "chart_cvf", "chart_pupil", "chart_external_exam",
							"chart_lids", "chart_lesion", "chart_lac_sys", "chart_lid_pos",
							"chart_gonio",
							"chart_conjunctiva", "chart_cornea", "chart_ant_chamber", "chart_iris", "chart_lens",
							"chart_vitreous","chart_retinal_exam", "chart_blood_vessels", "chart_periphery", "chart_macula","chart_optic");
		foreach($chart_db_tables as $key=>$tbl){

			$pIdV="patient_id";
			$fidV="form_id";

			if($tbl=="chart_cvf" || $tbl=="chart_pupil"){
				$pIdV="patientId";
				$fidV="formId";
			}


			$sql = "SELECT count(*) as num FROM ".$tbl." WHERE ".$pIdV."='".$pid."' AND ".$fidV."='".$fid."' ";
			$row = sqlQuery($sql);
			if($row!=false && $row["num"]>0){

				$wnl_phrase = "";
				switch($tbl){
					case "amsler_grid":
						$wnl_value=$this->getExamWnlStr("Amsler Grid");
						$wnl_phrase = " wnl_value='".sqlEscStr($wnl_value)."' ";
					break;
					case "chart_cvf":
						//WNL
						$wnl_value=$this->getExamWnlStr("CVF");
						$wnl_phrase = " wnl_value='".sqlEscStr($wnl_value)."' ";
					break;
					case "chart_pupil":
						$wnl_value=$this->getExamWnlStr("Pupil");
						$wnl_phrase = " wnl_value='".sqlEscStr($wnl_value)."' ";
					break;
					case "chart_external_exam":
						$wnl_value=$this->getExamWnlStr("External");
						$wnl_phrase = " wnl_value='".sqlEscStr($wnl_value)."' ";
					break;

					//"chart_lids", "chart_lesion", "chart_lac_sys", "chart_lid_pos",
					case "chart_lids":
						$wnl_value_Lids=$this->getExamWnlStr("Lids");
						$wnl_phrase = " wnl_value_Lids='".sqlEscStr($wnl_value_Lids)."' ";
					break;
					case "chart_lesion":
						$wnl_value_Lesion=$this->getExamWnlStr("Lesion");
						$wnl_phrase = " wnl_value_Lesion='".sqlEscStr($wnl_value_Lesion)."' ";
					break;
					case "chart_lid_pos":
						$wnl_value_LidPos=$this->getExamWnlStr("Lid Position");
						$wnl_phrase = " wnl_value_LidPos='".sqlEscStr($wnl_value_LidPos)."' ";
					break;
					case "chart_lac_sys":
						$wnl_value_LacSys=$this->getExamWnlStr("Lacrimal System");
						$wnl_phrase = " wnl_value_LacSys='".sqlEscStr($wnl_value_LacSys)."' ";
						//$wnl_phrase=$wnl_value_Lids_phrase.$wnl_value_Lesion_phrase.$wnl_value_LidPos_phrase.$wnl_value_LacSys_phrase;
					break;
					case "chart_gonio":
						$wnl_value = $this->getExamWnlStr("Gonio", $pid, $fid);
						$wnl_phrase = " wnl_value='".sqlEscStr($wnl_value)."' ";
					break;

					case "chart_conjunctiva":
						$wnl_value_Conjunctiva=$this->getExamWnlStr("Conjunctiva");
						$wnl_value = " wnl_value_Conjunctiva='".sqlEscStr($wnl_value_Conjunctiva)."' ";
					break;
					case "chart_cornea":
						$wnl_value_Cornea=$this->getExamWnlStr("Cornea");
						$wnl_value = ", wnl_value_Cornea='".sqlEscStr($wnl_value_Cornea)."' ";
					break;
					case "chart_ant_chamber":
						$wnl_value_Ant=$this->getExamWnlStr("Ant. Chamber");
						$wnl_value = ", wnl_value_Ant='".sqlEscStr($wnl_value_Ant)."' ";
					break;
					case "chart_iris":
						$wnl_value_Iris=$this->getExamWnlStr("Iris & Pupil");
						$wnl_value = ", wnl_value_Iris='".sqlEscStr($wnl_value_Iris)."' ";
					break;
					case "chart_lens":
						$wnl_value_Lens=$this->getExamWnlStr("Lens");
						$wnl_value = ", wnl_value_Lens='".sqlEscStr($wnl_value_Lens)."' ";
						//$wnl_phrase=$wnl_value_Conjunctiva_phrase.$wnl_value_Cornea_phrase.$wnl_value_Ant_phrase.$wnl_value_Iris_phrase.$wnl_value_Lens_phrase;
					break;
					//
					case "chart_vitreous":
						$wnl_value_Vitreous=$this->getExamWnlStr("Vitreous");
						$wnl_phrase = " wnl_value_Vitreous='".sqlEscStr($wnl_value_Vitreous)."' ";
					break;
					case "chart_macula":
						$wnl_value_Macula=$this->getExamWnlStr("Macula");
						$wnl_phrase = " wnl_value_Macula='".sqlEscStr($wnl_value_Macula)."' ";
					break;
					case "chart_periphery":
						$wnl_value_Peri=$this->getExamWnlStr("Periphery");
						$wnl_phrase = " wnl_value_Peri='".sqlEscStr($wnl_value_Peri)."' ";
					break;
					case "chart_blood_vessels":
						$wnl_value_BV=$this->getExamWnlStr("Blood Vessels");
						$wnl_phrase = " wnl_value_BV='".sqlEscStr($wnl_value_BV)."' ";
					break;
					case "chart_retinal_exam":
						$wnl_value_RetinalExam=$this->getExamWnlStr("Retinal Exam");
						$wnl_phrase = " wnl_value_RetinalExam='".sqlEscStr($wnl_value_RetinalExam)."' ";
						//$wnl_phrase= " ".$wnl_value_Vitreous_phrase." ".$wnl_value_Macula_phrase." ".$wnl_value_Peri_phrase." ".$wnl_value_BV_phrase." ".$wnl_value_RetinalExam_phrase." ";
					break;
					case "chart_optic":
						$wnl_value_Optic=$this->getExamWnlStr("Optic Nerve");
						$wnl_phrase = " wnl_value_Optic='".sqlEscStr($wnl_value_Optic)."' ";
					break;
					case "chart_eom":
						$wnl_value=$this->getExamWnlStr("EOM");
						$wnl_phrase = " wnl_value='".sqlEscStr($wnl_value)."' ";
					break;
				}

				if(!empty($wnl_phrase)){
					$sql  = "UPDATE ".$tbl."  SET ".$wnl_phrase." WHERE ".$pIdV."='".$pid."' AND ".$fidV."='".$fid."'  ";
					$row2=sqlQuery($sql);
				}
			}
		}
	}

	function setTemplateId($elem_templateId){
		if(!empty($this->fid) && is_numeric($this->fid)){
			//Set Template id
			$sql = "UPDATE chart_master_table SET templateId = '".$elem_templateId."' WHERE id = '".$this->fid."' ";
			$row = sqlQuery($sql);
			//reset WNL Default values
			$this->resetWNLValuesInPtChart($elem_templateId);
		}
	}

	function mkNewChart(){
		$memo = $_REQUEST['memo'];
		$_SESSION["finalize_id"] = "";
		$_SESSION["finalize_id"] = NULL;
		unset($_SESSION["finalize_id"]);

		$_SESSION["scanned_chart_image"] = "";
		$_SESSION["scanned_chart_image"] = NULL;
		unset($_SESSION["scanned_chart_image"]);

		$_SESSION["encounter_id"] = "";
		unset($_SESSION["encounter_id"]);

		$patient_id = $this->pid;
		//Template Id
		//$tempId = (!empty($_REQUEST["elem_tempId"])) ? $_REQUEST["elem_tempId"] : 0;
		//Template Id
		if(!empty($_REQUEST["elem_tempId"])){
			$tempId = $_REQUEST["elem_tempId"];
		}

		//obj chart template
		$oChartTemp =  new ChartTemp();

		if($tempId == "GETFORMSETTINGS"){ //Get from Settings in phy console
			$tempId="";
			$tempId = $oChartTemp->getUserDefTempId();
		}

		if(empty($tempId)){
			//get Comprehensive id
			//include(dirname(__FILE__)."/common/ChartTemp.php");
			$tempId = $oChartTemp->getIdFromName("Comprehensive");
			if($tempId===false||$tempId==""){ $tempId = 0; }
		}

		//cary form id
		$carry_form_id = $_REQUEST['carry_form_id'];
		if(empty($carry_form_id)){  $carry_form_id="0"; }


		//Check for Active Records latest
		$sql = "SELECT chart_master_table.* FROM chart_master_table ".
			  // "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
			   "WHERE chart_master_table.patient_id = '".$patient_id."' ".
			   "AND chart_master_table.finalize = '0' ".
			   "AND chart_master_table.delete_status = '0' ".
			   "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ".
			   //"ORDER BY IFNULL(chart_left_cc_history.date_of_service,chart_master_table.create_dt) DESC, chart_master_table.id DESC ".
			   //"ORDER BY chart_master_table.update_date DESC, chart_master_table.id DESC ".
			   "LIMIT 0,1 ";
		$row = sqlQuery($sql);
		if($row != false){ // Active Chart Notes
			//Test
			$_SESSION["form_id"] = $row["id"];
		}else{
			/*
			$encounterId = getEncounterId();
			$cn_providerId = $oPtfun->getPro4CnIn();
			$sql = "INSERT INTO chart_master_table (id, patient_id, update_date, finalize,
								encounterId, isSuperBilled,providerId,releaseNumber, create_dt, create_by) ".
					"VALUES (NULL, '".$patient_id."', NOW(), '0', '".$encounterId."', '0', '".$cn_providerId."', '1',
									NOW(),'".$_SESSION["authId"]."' ) ";
			$form_id = sqlInsert($sql);
			*/

			// Create chart
			$oPt = new Patient($patient_id);
			list($form_id,$elem_dos, $encounterId) = $oPt->createNewChart($tempId,$memo, $carry_form_id);
			//$form_id = $oChrtFun->getFormId();
			$_SESSION["form_id"] = $form_id;

			//---

			//$dos = $oChrtFun->getDos(1);
			//insert_remote_sync($patient_id, $dos, $form_id, "CHART_NOTE");

			//---

		}

	}

	function ut_removeEomFields($ut_elem){
		$tmpElem2Clear = array("elem_stereo_SecondsArc","elem_color_sign_od",
							"elem_color_od_1","elem_color_od_2","elem_color_sign_os",
							"elem_color_os_1","elem_color_os_2","elem_w4dot_distance",
							"elem_w4dot_near","elem_comm_w4Dot","elem_comm_colorVis");
		foreach($tmpElem2Clear as $key=>$val){
			$ut_elem=str_replace(array($val.",",$val),"",$ut_elem);
		}
		return $ut_elem;
	}

	//
	function closeThisPatientWorkView($flg=""){

		//pt monitor
		patient_monitor_daily("CHART_CLOSE");

		//Release Lock
		if(isset($_SESSION["patient"]) && !empty($_SESSION["patient"])){
			$oPtLock = new ChartPtLock($_SESSION["authId"], $_SESSION["patient"]);
			$oPtLock->releaseUsersPastPt();
		}

		// Clear Patient
		//Clear any finalized_id of Chart notes if exists
		if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
			$_SESSION["finalize_id"] = "";
			$_SESSION["finalize_id"] = NULL;
			unset($_SESSION["finalize_id"]);
		}
		//Clear any form_id of Chart notes if exists
		if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){
			$_SESSION["form_id"] = "";
			$_SESSION["form_id"] = NULL;
			unset($_SESSION["form_id"]);
		}
		//Clear any Patient of Chart notes if exists
		if(isset($_SESSION["patient"]) && !empty($_SESSION["patient"])){
			$_SESSION['patient'] = "";
			$_SESSION["patient"] = NULL;
			unset($_SESSION["patient"]);
		}
		//Clear --

		//Close Window
		if($flg=="savemaintable"){
		$arrRet = array();
		$arrRet["closePtChart"]="1";
		echo json_encode($arrRet);
		}else{

		echo "<html><body>
				<script>
				top.core_redirect_to('Work_View','','1');
				top.clean_patient_session();

				</script>
			  </body></html>";

		}
		//header("location: ".$GLOBALS["rootdir"]."/core/index.php?pg=default-page");
		//exit();

	}
	function setChangeDtArcRec($tblNm){
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION["authId"]);
		$oChartRecArc->setChangeDt($tblNm);
	}

	function saveCCHX(){

		//chart_left_cc_history
		$cc_id = $_POST["elem_ccId"];
		$date_of_service = $this->arG["date_of_service"];
		$reason = sqlEscStr($_POST["elem_ccHx"]);
		//$pro_id = $_POST["elem_physicianId"];
		//$form_id=$_POST["elem_formId"];

		/*
		$phthisisOd = $_POST["elem_phthisisOd"];
		$phthisisOs = $_POST["elem_phthisisOs"];
		$prosthesisOd = $_POST["elem_prosthesisOd"];
		$prosthesisOs = $_POST["elem_prosthesisOs"];
		*/
		$phth_pros = $_POST["elem_phth_pros"];
		$eyePrPh = $_POST["elem_eyePrPh"];
		$acuteEyeProbs = sqlEscStr($_POST["elem_acuteProbs"]);

		$neuroPsych = sqlEscStr($_POST["elem_neuroPsych"]);
		$elem_editModeCc = $_POST["elem_editModeCc"];
		$pro_id = $_POST["elem_pro_id"];
		$cosigner_id = $_POST["elem_cosigner_id"];
		$curset_phth_pros = sqlEscStr($_POST["elem_curset_phth_pros"]);
		$dominantCc = sqlEscStr($_POST["elem_dominantCc"]);
		$eyeColorCc = sqlEscStr($_POST["elem_eyeColorCc"]);
		$ccompliant = $_POST["elem_ccompliant"];
		$paincc = sqlEscStr($_POST["elem_painCc"]);
		$chronicProbs = sqlEscStr($_POST["el_chronicProbs"]);

		//convert spl characters
		$ccompliant = checkHPIFormatChars($ccompliant, "en");//encode
		$ccompliant = sqlEscStr($ccompliant);

		// double check prosthesis
		if(!empty($phth_pros) && empty($eyePrPh)){
			$phth_pros = "";
		}

		//Check Duplicate ProId and CosignerId
		if(!empty($pro_id) && ($pro_id == $cosigner_id)){
			$cosigner_id = "";
		}

		$patient_id = $this->pid;
		$form_id = $this->fid;

		//check
		$cQry = "select * FROM chart_left_cc_history WHERE form_id='".$this->fid."' AND patient_id='".$this->pid."' ";
		$row = sqlQuery($cQry);
		$elem_editModeCc = ($row == false) ? "0" : "1";
		if($elem_editModeCc == "0"){

			if(!empty($this->pid)&&!empty($this->fid)){

			$sql = "INSERT INTO chart_left_cc_history ".
				 "( ".
				 "cc_id, date_of_service,time_of_service, reason, pro_id, patient_id, form_id, ".
				 "phthisisOd, phthisisOs, prosthesisOd, prosthesisOs, neuroPsych, phth_pros, eyePrPh, ".
				 "acuteEyeProbs,cosigner_id, modi_date,phth_pros_set_curr, ".
				 "dominant,eyeColor,ccompliant,paincc,func_status, chronic_probs ".
				 ") ".
				 "VALUES ".
				 "( ".
				 "NULL, '".$date_of_service."', CURTIME(), '".$reason."', ".
				 "'".$pro_id."', '".$patient_id."', '".$form_id."', ".
				 "'".$phthisisOd."', '".$phthisisOs."', '".$prosthesisOd."', ".
				 "'".$prosthesisOs."', '".$neuroPsych."', '".$phth_pros."', '".$eyePrPh."', ".
				 "'".$acuteEyeProbs."','".$cosigner_id."','".date("Y-m-d H:i:s")."','".$curset_phth_pros."', ".
				 "'".$dominantCc."','".$eyeColorCc."', '".$ccompliant."', '".$paincc."', '".$func_status."', '".$chronicProbs."' ".
				 ") ";
			$inId = sqlInsert($sql);

			}

		}else{
			$sql = "UPDATE chart_left_cc_history ".
				 "SET ".
				 "date_of_service='".$date_of_service."', ".
				 "reason='".$reason."', ".
				 "pro_id='".$pro_id."', ".
				 "phthisisOd='".$phthisisOd."', ".
				 "phthisisOs='".$phthisisOs."', ".
				 "prosthesisOd='".$prosthesisOd."', ".
				 "prosthesisOs='".$prosthesisOs."', ".
				 "phth_pros='".$phth_pros."', ".
				 "eyePrPh='".$eyePrPh."', ".
				 "neuroPsych='".$neuroPsych."', ".
				 "acuteEyeProbs='".$acuteEyeProbs."', ".
				 "cosigner_id='".$cosigner_id."', ".
				 "modi_date='".date("Y-m-d H:i:s")."', ".
				 "phth_pros_set_curr='".$curset_phth_pros."', ".
				 "dominant='".$dominantCc."', ".
				 "eyeColor='".$eyeColorCc."', ".
				 "ccompliant='".$ccompliant."', ".
				 "paincc='".$paincc."', ".
				 "func_status='".$func_status."', ".
				 "chronic_probs='".$chronicProbs."' ".
				 "WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
			$res = sqlQuery($sql);
			$inId = $row['cc_id'];
		}

		//Start Audit
		$patientCcHistoryFields = make_field_type_array("chart_left_cc_history");
		if($patientCcHistoryFields == 1146){
			$patientCcHistoryError = "Error : Table 'patient_data' doesn't exist";
		}
		$table = array("chart_left_cc_history");
		$error = array($patientCcHistoryError);
		$mergedArray = merging_array($table,$error);

		$opreaterId = $_SESSION['authId'];
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$arrAuditTrail= array();
		$action="update";
		$arrAuditTrail [] =
			array(
					"Pk_Id"=> $inId,
					"Table_Name"=>"chart_left_cc_history",
					"Data_Base_Field_Name"=> "ccompliant" ,
					"Data_Base_Field_Type"=> fun_get_field_type($patientCcHistoryFields,"ccompliant") ,
					"Filed_Label"=> "elem_ccompliant",
					"Filed_Text"=> "Chief Complaint",
					"Action"=> $action,
					"Operater_Id"=> $opreaterId,
					"Operater_Type"=> getOperaterType($opreaterId) ,
					"IP"=> $ip,
					"MAC_Address"=> $_REQUEST['macaddrs'],
					"URL"=> $URL,
					"Browser_Type"=> $browserName,
					"OS"=> $os,
					"Machine_Name"=> $machineName,
					"pid"=> $_SESSION['patient'],
					"Category"=> "patient_info-medical_history",
					"Category_Desc"=> "CC_HX",
					"Old_Value"=> addcslashes(addslashes($row['ccompliant']),"\0..\37!@\177..\377"),
					"New_Value"=> addslashes(trim($ccompliant),"\0..\37!@\177..\377"),
				);
		$arrAuditTrail [] =
			array(
					"Pk_Id"=> $inId,
					"Table_Name"=>"chart_left_cc_history",
					"Data_Base_Field_Name"=> "reason" ,
					"Data_Base_Field_Type"=> fun_get_field_type($patientCcHistoryFields,"reason") ,
					"Filed_Label"=> "elem_ccHx",
					"Filed_Text"=> "CC - History",
					"pid"=> $_SESSION['patient'],
					"Category"=> "patient_info-medical_history",
					"Category_Desc"=> "CC_HX",
					"Old_Value"=> addcslashes(addslashes($row['reason']),"\0..\37!@\177..\377"),
					"New_Value"=> addslashes(trim($reason),"\0..\37!@\177..\377"),
				);

		$policyStatus = 0;
		$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
		if($policyStatus == 1){
			auditTrail($arrAuditTrail,$mergedArray,0,0,0);
		}
		//End Audit

		//Set Change Date Arc Rec --
		$this->setChangeDtArcRec("chart_left_cc_history");
		//Set Change Date Arc Rec --

	}

	function saveProviderIssue(){

		$patient_id = $this->pid;
		$form_id = $this->fid;


		//chart_left_provider_issue
		$sep = "<+O@#+>";
		//$patient_id=$_POST["elem_patientId"];
		//$form_id=$_POST["elem_formId"];
		$pr_is_id = $_POST["elem_prIsId"];
		$elem_editModePrIs = $_POST["elem_editModePrIs"];

		$vpDistance = (is_array($_POST["elem_vpDis"])) ? addslashes(implode(",", $_POST["elem_vpDis"])) : "" ;
		$elem_vpDisOther= addslashes($_POST["elem_vpDisOther"]);
		$vpDistance = $vpDistance.$sep.$elem_vpDisOther;

		$vpMidDistance= (is_array($_POST["elem_vpMidDis"])) ? addslashes(implode(",",$_POST["elem_vpMidDis"])) : "" ;
		$elem_vpMidDisOther= addslashes($_POST["elem_vpMidDisOther"]);
		$vpMidDistance = $vpMidDistance.$sep.$elem_vpMidDisOther;

		$vpNear= (is_array($_POST["elem_vpNear"])) ? addslashes(implode(",",$_POST["elem_vpNear"])) : "" ;
		$elem_vpNearOther= addslashes($_POST["elem_vpNearOther"]);
		$vpNear=$vpNear.$sep.$elem_vpNearOther;

		$vpGlare= (is_array($_POST["elem_vpGlare"])) ? addslashes(implode(",",$_POST["elem_vpGlare"])) : "" ;

		$vpOther= (is_array($_POST["elem_vpOther"])) ? addslashes(implode(",",$_POST["elem_vpOther"])) : "" ;
		$elem_vpOtherOther= addslashes($_POST["elem_vpOtherOther"]);
		$vpOther=$vpOther.$sep.$elem_vpOtherOther;
		//START
		$vpComment= addslashes($_POST["vpComment"]);
		$vpDate= wv_formatDate($_POST["vpDate"],0,0, "insert");
		//END
		$irrLidsExternal= (is_array($_POST["elem_irrLidsExt"])) ? addslashes(implode(",",$_POST["elem_irrLidsExt"])) : "" ;
		$elem_irrLidsExtOther= addslashes($_POST["elem_irrLidsExtOther"]);
		$irrLidsExternal=$irrLidsExternal.$sep.$elem_irrLidsExtOther;

		$sepType="<+type%$+>";
		$irrOcular= (is_array($_POST["elem_irrOcu"])) ? addslashes(implode(",",$_POST["elem_irrOcu"])) : "" ;
		$elem_irrOcuItchingType= addslashes($_POST["elem_irrOcuItchingType"]);
		$elem_irrOcuPresSensType= addslashes($_POST["elem_irrOcuPresSensType"]);
		$elem_irrOcuOther= addslashes($_POST["elem_irrOcuOther"]);
		$irrOcular=$irrOcular.$sep.$elem_irrOcuOther.$sepType.$elem_irrOcuItchingType.$sepType.$elem_irrOcuPresSensType;

		$psSpots= $_POST["elem_postSegSpots"];
		//$elem_postSegSpots= $_POST["elem_postSegSpots"];
		$sepFloat="<+Float*&+>";
		$psFloaters= (is_array($_POST["elem_postSegFloat"])) ? addslashes(implode(",",$_POST["elem_postSegFloat"])) : "" ;
		$elem_postSegFloatCobwebs= addslashes($_POST["elem_postSegFloatCobwebs"]);
		$elem_postSegFloatBlackSpots= addslashes($_POST["elem_postSegFloatBlackSpots"]);
		$psFloaters=$psFloaters.$sepFloat.$elem_postSegFloatCobwebs.$sepFloat.$elem_postSegFloatBlackSpots;

		$sepFL = "<+FL@^+>";
		$psFlashingLights= (is_array($_POST["elem_postSegFL"])) ? addslashes(implode(",",$_POST["elem_postSegFL"])) : "" ;
		$elem_postSegFLSparks= addslashes($_POST["elem_postSegFLSparks"]);
		$elem_postSegFLBolts= addslashes($_POST["elem_postSegFLBolts"]);
		$elem_postSegFLArcs= addslashes($_POST["elem_postSegFLArcs"]);
		$elem_postSegFLStrobe= addslashes($_POST["elem_postSegFLStrobe"]);
		$psFlashingLights=$psFlashingLights.$sepFL.$elem_postSegFLSparks.$sepFL.$elem_postSegFLBolts.$sepFL.$elem_postSegFLArcs;
		$psFlashingLights=$psFlashingLights.$sepFL.$elem_postSegFLStrobe;

		$psAmslerGrid= (is_array($_POST["elem_postSegAmsler"])) ? addslashes(implode(",",$_POST["elem_postSegAmsler"])) : "" ;
		$elem_postSegAmslerOther= addslashes($_POST["elem_postSegAmslerOther"]);
		$psAmslerGrid=$psAmslerGrid.$sep.$elem_postSegAmslerOther;

		$neuroDblVision= (is_array($_POST["elem_neuroDblVis"])) ? addslashes(implode(",",$_POST["elem_neuroDblVis"])) : "" ;

		$neuroTempArtSymp= (is_array($_POST["elem_neuroTAS"])) ? addslashes(implode(",",$_POST["elem_neuroTAS"])) : "" ;
		$elem_neuroTASOther= addslashes($_POST["elem_neuroTASOther"]);
		$neuroTempArtSymp=$neuroTempArtSymp.$sep.$elem_neuroTASOther;

		$neuroVisionLoss= (is_array($_POST["elem_neuroVisLoss"])) ? addslashes(implode(",",$_POST["elem_neuroVisLoss"])) : "" ;
		$elem_neuroVisLossOther= addslashes($_POST["elem_neuroVisLossOther"]);
		$neuroVisionLoss=$neuroVisionLoss.$sep.$elem_neuroVisLossOther;

		$neuroHeadaches= (is_array($_POST["elem_neuroHeadaches"])) ? addslashes(implode(",",$_POST["elem_neuroHeadaches"])) : "" ;

		$sepMig = "<+Mig&$+>";
		$neuroMigHead= (is_array($_POST["elem_neuroMigHead"])) ? addslashes(implode(",",$_POST["elem_neuroMigHead"])) : "" ;
		$elem_neuroMigHeadOther = addslashes($_POST["elem_neuroMigHeadOther"]);
		$neuroMigHead=$neuroMigHead.$sep.$elem_neuroMigHeadOther;

		$arr_neuroMigHeadAura= (is_array($_POST["elem_neuroMigHeadAura"])) ? addslashes(implode(",",$_POST["elem_neuroMigHeadAura"])) : "" ;
		$elem_neuroMigHeadAuraOther= addslashes($_POST["elem_neuroMigHeadAuraOther"]);
		$arr_neuroMigHeadAura=$arr_neuroMigHeadAura.$sep.$elem_neuroMigHeadAuraOther;

		$neuroMigHead = $neuroMigHead.$sepMig.$arr_neuroMigHeadAura;

		$neuroOther= addslashes($_POST["elem_neuroOther"]);

		$rvspostop= (is_array($_POST["elem_fuPostOp"])) ? addslashes(implode(",",$_POST["elem_fuPostOp"])) : "" ;
		$elem_fuPostOp_other = addslashes($_POST["elem_fuPostOp_other"]);
		$rvspostop=$rvspostop.$sep.$elem_fuPostOp_other;

		$rvsfollowup= (is_array($_POST["elem_fuFollowUp"])) ? addslashes(implode(",",$_POST["elem_fuFollowUp"])) : "" ;
		$elem_fuFollowUp_other = addslashes($_POST["elem_fuFollowUp_other"]);
		$rvsfollowup=$rvsfollowup.$sep.$elem_fuFollowUp_other;

//Uncommented on 26-12-2012//*---THIS CODE SAVE MEDICATION GRID. COMMENTED AFTER SPECS DISCUSSED ON 15-OCT-2012.

		//Ocular Medication
		if(!isset($_REQUEST['memo']) || empty($_REQUEST['memo'])){//check for Not memo chart --
			$oMedHx = new MedHx($this->pid);
			$arrOcuMed = $oMedHx->getOcularMedication("ocular");
			$arrCheck = $arrOcuMed[0];
			$arrCheckD = $arrOcuMed[1];
			$arrCheckSite = $arrOcuMed[5];
			$arrCheckTitleSite = $arrOcuMed[8];

			/*print_r($arrCheck);
			echo "<br/>";
			print_r($arrCheckD);
			echo "<br/>";
			print_r($arrCheckSite);
			echo "<br/>";
			*/

			$arrNewOM=array();
			$arrUpdOM=array();
			$arrDelOM=array();
			$arrCurOM=array();
			$arrGridOcularMeds =array();

			//objChartRec
			$objChartRec = new ChartRecArc($patient_id, $form_id, $_SESSION["authId"]);

			$arrSites = array('OS'=>'1', 'OD'=>'2', 'OU'=>'3', 'PO'=>'4');

			$sepOctMeds = "<+OMeds&%+>";
			$str="";	$j=0;
			for($i=1;$i<=16;$i++){
				$tmp ="";
				$part=$title=$type=$destination=$sig=$med_comments=$site=$dosg='';
				$med=$strData=$sites='';

				$tmp = trim($_POST["elem_ocMeds".$i]);
				if(!empty($tmp)){
					$part = $oMedHx->checkValidOcuMed($tmp,1);
					if($part!=false){

						$strData = $part["compOcuMed"];

						$title = $part["med"];
						$dosg = $part["dosg"];
						$sig = $part["sig"];
						$med_comments = $part["comment"];
						$site = $part["site"];
						$sites = $arrSites[$site];

						if(empty($site)){ continue; }//Do not Save without Site values

					}

					/*
					//$str = $str.$sepOctMeds.$tmp;
					//$tmp = addslashes($tmp);
					$part =explode(";",$tmp);

					$pattern = '/ od| os| ou| po| OD| OS| OU| PO/';
					preg_match($pattern, $part[0], $matches);

					if(sizeof($matches)>0){
						$site = trim($matches[0]);

						$med = explode($site,$part[0]);
						$title = addslashes(trim($med[0]));

						$sites = $arrSites[strtoupper($site)];
						$strData = $title.' '.strtoupper($site);

						if($med[1]!=''){
							$sig = addslashes(trim($med[1]));
							$strData.= ' '.trim($med[1]);
						}
						if($part[1]!=''){
							$med_comments= addslashes(trim($part[1]));
							$strData.= '; '.$med_comments;
						}
					}else{
						$title = addslashes(trim($part[0]));
						$sites = $arrSites['OU'];
						$strData = $title.' OU';
						if($part[1]!=''){
							$med_comments= addslashes(trim($part[1]));
							$strData.=' ;'.$med_comments;
						}
					}
					*/

					// make str for chart_left_provider_issue table
					if($strData!=''){
						$str = ($i == 1) ? $strData : $str.$sepOctMeds.$strData;
					}

					//$arrCurOM[]= strtolower($title);
					$arrCurOM[]= strtolower($title." ".$sites);
					$arrhidOcularMeds = explode(",",$_REQUEST['hidOcularMeds']);
					$arrGridOcularMeds[] = array('title'=>$title,'type'=>4,'sig'=>$sig,'med_comments'=>$med_comments,'sites'=>$sites,'destination'=> $dosg,'compliant'=>1,'allergy_status'=>'Active');

					//print_r($arrhidOcularMeds);
					//exit();

					$tmp_flgInsert=0;
					$tmpChk_title = trim($title." ".$sites);
					$tmp_titleCheck = array_lsearch($tmpChk_title,$arrCheckTitleSite);
					if(!$tmp_titleCheck){
						$tmp_flgInsert=1;
					}

					if( !empty($title) && $tmp_flgInsert){
						if((!empty($_SESSION["finalize_id"]) || $objChartRec->getArcRecId(1)) && array_lsearch($title,$arrhidOcularMeds)){
							$arrNewOM[$j]['type']= 4;
							$arrNewOM[$j]['title']= $title;
							$arrNewOM[$j]['sig']= $sig;
							$arrNewOM[$j]['med_comments']= $med_comments;
							$arrNewOM[$j]['sites']= $sites;
							$arrNewOM[$j]['dosg']= $dosg;
							$arrCheck[] = $title;		// for make Status to Delete
						}else if(empty($_SESSION["finalize_id"]) && !$objChartRec->getArcRecId(1)){
							$arrNewOM[$j]['type']= 4;
							$arrNewOM[$j]['title']= $title;
							$arrNewOM[$j]['sig']= $sig;
							$arrNewOM[$j]['med_comments']= $med_comments;
							$arrNewOM[$j]['sites']= $sites;
							$arrNewOM[$j]['dosg']= $dosg;
							$arrCheck[] = $title;		// for make Status to Delete
						}
					}else if( !empty($title) && array_lsearch($title,$arrCheck)){
								if((!empty($_SESSION["finalize_id"]) || $objChartRec->getArcRecId(1)) && array_lsearch($title,$arrhidOcularMeds)){
								$arrUpdOM[$j]['type']= 4;
								$arrUpdOM[$j]['title']= $title;
								$arrUpdOM[$j]['sig']= $sig;
								$arrUpdOM[$j]['med_comments']= $med_comments;
								$arrUpdOM[$j]['sites']= $sites;
								$arrUpdOM[$j]['dosg']= $dosg;
								$qry = "SELECT compliant,DATE_FORMAT(date,'%Y-%m-%d') AS date FROM lists
										WHERE title = '".$title."'
											   and pid = '".$patient_id."'
											   and sig = '".$sig."'
											   and med_comments = '".$med_comments."'
											   and sites = '".$sites."'
											   and type = '4'
											   and allergy_status = 'Active'
										";
								$row_list = sqlQuery($qry);
								if(!$row_list['compliant']){
									$str .= ' compliant:0';
								}
								$str .= ' date:'.$row_list['date'].'';
							}else if(empty($_SESSION["finalize_id"]) && !$objChartRec->getArcRecId(1)){
								$arrUpdOM[$j]['type']= 4;
								$arrUpdOM[$j]['title']= $title;
								$arrUpdOM[$j]['sig']= $sig;
								$arrUpdOM[$j]['med_comments']= $med_comments;
								$arrUpdOM[$j]['sites']= $sites;
								$arrUpdOM[$j]['dosg']= $dosg;
								$qry = "SELECT compliant,DATE_FORMAT(date,'%Y-%m-%d') AS date FROM lists
										WHERE title = '".$title."'
											   and pid = '".$patient_id."'
											   and sig = '".$sig."'
											   and med_comments = '".$med_comments."'
											   and sites = '".$sites."'
											   and type = '4'
											   and allergy_status = 'Active'
										";
								$row_list = sqlQuery($qry);
								if(!$row_list['compliant']){
									$str .= ' compliant:0';
								}
								$str .= ' date:'.$row_list['date'].'';
							}
					}
				}
				$j++;
			}
			$ocularMeds = addslashes($str);

			//Insert in Pt Info
			/*
			echo "<br/>";
			print_r($arrNewOM);

			echo "<br/>";
			print_r($arrUpdOM);
			*/

			//if(count($arrNewOM) && empty($_SESSION["finalize_id"]) && !$objChartRec->getArcRecId(1)){
			if(count($arrNewOM)){/*
				$dt = explode("-",$_REQUEST['elem_dos']); // Existing Format m-d-Y
				$begdate = $dt[2].'-'.$dt[0].'-'.$dt[1];
				$sql = "INSERT INTO lists(id, date,type, title, sig, med_comments, sites, pid,allergy_status, compliant,destination) values ";
				foreach($arrNewOM as $key => $val){
					if(!empty($val['title'])){
						$sql .= "(NULL, NOW(), '".$val['type']."', '".$val['title']."', '".$val['sig']."','".$val['med_comments']."', '".$val['sites']."', '".$patient_id."','Active','1','".$val['dosg']."'),";
					}
				}

				$sql = substr($sql,0,-1);
				$res = sqlQuery($sql);
			*/}
			//Update ocular grid meds from workview in lists table
			//Insert in Pt Info
			//if(count($arrUpdOM) && empty($_SESSION["finalize_id"]) && !$objChartRec->getArcRecId(1)){//pre($arrUpdOM);
			if(count($arrUpdOM)){
				$dt = explode("-",$_REQUEST['elem_dos']); // Existing Format m-d-Y
				$begdate = $dt[2].'-'.$dt[0].'-'.$dt[1];
				foreach($arrUpdOM as $key => $val){

					//Eye--
					$phrEye="";
					if(!empty($val['sites'])){
						$phrEye=" sites = '".$val['sites']."', ";

						//Check med with Eye
						$sql = "SELECT count(*) AS num FROM lists
									WHERE title = '".$val['title']."'
									   and pid = '".$patient_id."'
									   and type = '".$val['type']."'
									   and allergy_status = 'Active'
									   AND sites = '".$val['sites']."'
								";
						$row = sqlQuery($sql);
						if($row != false && $row["num"]==1){
							$whPhr = " AND sites = '".$val['sites']."' ";
						}
					}
					//Eye--

					//Sig --
					if(empty($whPhr) && !empty($val['sig'])){
						//Check med with Sig
						$sql = "SELECT count(*) AS num FROM lists
									WHERE title = '".$val['title']."'
									   and pid = '".$patient_id."'
									   and type = '".$val['type']."'
									   and allergy_status = 'Active'
									   AND sig  = '".$val['sig']."'
								";
						$row = sqlQuery($sql);
						if($row != false && $row["num"]==1){
							$whPhr = " AND sig  = '".$val['sig']."' ";
						}
					}
					//Sig --

					//dosg--
					if(empty($whPhr) && !empty($val['dosg'])){
						//Check med with dosg
						$sql = "SELECT count(*) AS num FROM lists
									WHERE title = '".$val['title']."'
									   and pid = '".$patient_id."'
									   and type = '".$val['type']."'
									   and allergy_status = 'Active'
									   AND destination = '".$val['dosg']."'
								";
						$row = sqlQuery($sql);
						if($row != false && $row["num"]==1){
							$whPhr = " AND destination = '".$val['dosg']."' ";
						}
					}
					//dosg--

					//med_comm--
					if(empty($whPhr) && !empty($val['med_comments'])){
						//Check med with med_comments
						$sql = "SELECT count(*) AS num FROM lists
									WHERE title = '".$val['title']."'
									   and pid = '".$patient_id."'
									   and type = '".$val['type']."'
									   and allergy_status = 'Active'
									   AND med_comments = '".$val['med_comments']."'
								";
						$row = sqlQuery($sql);
						if($row != false && $row["num"]==1){
							$whPhr = " AND med_comments = '".$val['med_comments']."' ";
						}
					}
					//med_comm--

					/*$sql = "UPDATE lists SET
								".$phrEye."
								sig  = '".$val['sig']."',
								med_comments = '".$val['med_comments']."',
								destination = '".$val['dosg']."'
								WHERE title = '".$val['title']."'
									   and pid = '".$patient_id."'
									   and type = '".$val['type']."'
									   and allergy_status = 'Active'
									   $whPhr
									LIMIT 1
								";
					//echo "<br/>".$sql;
					$res = sqlQuery($sql);*/
				}
			}

			//exit();

			//Delete
			//$arrDelOM = array_diff($arrCheck, $arrCurOM);
			if(count($arrCheck) > 0 && empty($_SESSION["finalize_id"]) && !$objChartRec->getArcRecId(1)){
				foreach($arrCheck as $key => $val){
					if( !empty($val)){
						$tmp_sites = $arrCheckSite[$key];
						$tmp_val = trim($val." ".$tmp_sites);
						if(!array_lsearch($tmp_val,$arrCurOM) && !empty($tmp_sites)){
							$val = strtolower($val);
							/*$sql = "UPDATE lists SET allergy_status = 'Deleted'
									WHERE type!='' AND type>0 AND pid='".$patient_id."' AND LOWER(title)='".addslashes($val)."'
									AND sites = '".$tmp_sites."' ";
							$res = sqlQuery($sql);*/
						}
					}
				}
			}

		}//check for Not memo chart --
		//* -End Ocular medication- */

		/*
		// Medical Hx.
		$medicalhx_htn = $_POST["elem_htn"];
		$medicalhx_hear = $_POST["elem_heart"];
		$medicalhx_lungs = $_POST["elem_lungs"];
		$medicalhx_neuro = $_POST["elem_neuro"];
		$medicalhx_other = addslashes($_POST["elem_medHxOther"]);
		$medicalhx_id = $_POST["elem_dmOP"];
		$dateDm = $_POST["elem_dmDate"];
		//Ocu Hx.
		$ocularhx_lens = $_POST["elem_cl"];
		$ocularhx_glaucoma = $_POST["elem_glucoma"];
		$ocularhx_sx = $_POST["elem_sxLaser"];
		$ocularhx_glasses = $_POST["elem_glasses"];
		$ocularhx_fhx_ret = $_POST["elem_rDetach"];
		$ocularhx_fhx_mac = $_POST["elem_macDeg"];
		$ocularhx_fhx_cat = $_POST["elem_cataracts"];
		$ocularhx_fhx_bli = $_POST["elem_blindness"];
		$ocularhx_other = addslashes($_POST["elem_ocuHxOther"]);
		*/
		$ptinfoDesc = "";//addslashes($_POST["elem_ptinfoDesc"]);

		$complaint1Text = sqlEscStr($_POST['complaint1Text']);
		$complaint2Text = sqlEscStr($_POST['complaint2Text']);
		$complaint3Text = sqlEscStr($_POST['complaint3Text']);
		$complaintHead = sqlEscStr($_POST['complaintHeadText']);
		$selectedHead = sqlEscStr($_POST['selectedHeadText']);
		$titleHead = sqlEscStr($_POST['titleHeadText']);
		$lidsYesNo = sqlEscStr($_POST['lidsItchingYesNo']);
		$noFlashing = sqlEscStr($_POST['noFlashing']);
		$noFloaters = sqlEscStr($_POST['noFloaters']);
		$THAYesNo = sqlEscStr($_POST['THAYesNo']);


		//check
		$cQry = "select * FROM chart_left_provider_issue WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
		$row = sqlQuery($cQry);
		$elem_editModePrIs = ($row == false) ? "0" : "1";

		if($elem_editModePrIs == "0"){
			$sql = "INSERT INTO chart_left_provider_issue ".
				 "(".
				 "pr_is_id, patient_id, form_id, ".
				 "medicalhx_other, medicalhx_htn, medicalhx_hear, medicalhx_lungs, medicalhx_neuro, medicalhx_id, dateDm, ".
				 "ocularhx_lens, ocularhx_glaucoma, ocularhx_sx, ocularhx_glasses, ocularhx_fhx_ret, ocularhx_fhx_mac, ocularhx_fhx_cat, ocularhx_fhx_bli, ocularhx_other, ".
				 "vpDistance,vpMidDistance,vpNear,vpGlare,vpOther,irrLidsExternal, ".
				 "irrOcular,psSpots,psFloaters,psFlashingLights,psAmslerGrid,neuroDblVision, ".
				 "neuroTempArtSymp,neuroVisionLoss,neuroHeadaches,neuroMigHead,neuroOther, ".
				 "ocularMeds,complaint1Str,complaint2Str,complaint3Str,complaintHead,selectedHeadText,titleHead, lidsYesNo, noFlashing, noFloaters, THAYesNo, ".
				 "ptinfoDesc,vpComment,vpDate,uid, ".
				 "rvspostop,rvsfollowup ".
				 ")".
				 "VALUES ".
				 "( ".
				 "NULL, '".$patient_id."', '".$form_id."', ".
				 "'".$medicalhx_other."', '".$medicalhx_htn."', '".$medicalhx_hear."', '".$medicalhx_lungs."', '".$medicalhx_neuro."', '".$medicalhx_id."', '".$dateDm."', ".
				 "'".$ocularhx_lens."', '".$ocularhx_glaucoma."', '".$ocularhx_sx."', '".$ocularhx_glasses."', '".$ocularhx_fhx_ret."', '".$ocularhx_fhx_mac."', '".$ocularhx_fhx_cat."', '".$ocularhx_fhx_bli."', '".$ocularhx_other."', ".
				 "'".$vpDistance."', '".$vpMidDistance."', '".$vpNear."', '".$vpGlare."', '".$vpOther."', '".$irrLidsExternal."', ".
				 "'".$irrOcular."', '".$psSpots."', '".$psFloaters."', '".$psFlashingLights."', '".$psAmslerGrid."', '".$neuroDblVision."', ".
				 "'".$neuroTempArtSymp."', '".$neuroVisionLoss."', '".$neuroHeadaches."', '".$neuroMigHead."', '".$neuroOther."', ".
				 "'".$ocularMeds."','".$complaint1Text."','".$complaint2Text."','".$complaint3Text."', ".
				 "'".$complaintHead."','".$selectedHead."','".$titleHead."','".$lidsYesNo."','".$noFlashing."', ".
				 "'".$noFloaters."','".$THAYesNo."', ".
				 "'".$ptinfoDesc."','".$vpComment."','".$vpDate."', '".$_SESSION["authId"]."', ".
				 "'".$rvspostop."','".$rvsfollowup."' ".
				 ") ";
			$res = sqlInsert($sql);
		}else{
			$sql = "UPDATE chart_left_provider_issue ".
				 "SET ";
				 //"medicalhx_other='".$medicalhx_other."', ".
				 //"medicalhx_htn='".$medicalhx_htn."', ".
				 //"medicalhx_hear='".$medicalhx_hear."', ".
				 //"medicalhx_lungs='".$medicalhx_lungs."', ".
				 //"medicalhx_neuro='".$medicalhx_neuro."', ".
				 //"medicalhx_id='".$medicalhx_id."', ".
				 //"dateDm='".$dateDm."', ".
				 //"ocularhx_lens='".$ocularhx_lens."', ".
				 //"ocularhx_glaucoma='".$ocularhx_glaucoma."', ".
				 //"ocularhx_sx='".$ocularhx_sx."', ".
				 //"ocularhx_glasses='".$ocularhx_glasses."', ".
				 //"ocularhx_fhx_ret='".$ocularhx_fhx_ret."', ".
				 //"ocularhx_fhx_mac='".$ocularhx_fhx_mac."', ".
				 //"ocularhx_fhx_cat='".$ocularhx_fhx_cat."', ".
				 //"ocularhx_fhx_bli='".$ocularhx_fhx_bli."', ".
				 //"ocularhx_other='".$ocularhx_other."', ".
			if($_POST["elem_rvsIndicator"]=="1"){ //Check if RVS Loaded
			$sql .="vpDistance='".$vpDistance."', ".
				 "vpMidDistance='".$vpMidDistance."', ".
				 "vpNear='".$vpNear."', ".
				 "vpGlare='".$vpGlare."', ".
				 "vpOther='".$vpOther."', ".
				 "irrLidsExternal='".$irrLidsExternal."', ".
				 "irrOcular='".$irrOcular."', ".
				 "psSpots='".$psSpots."', ".
				 "psFloaters='".$psFloaters."', ".
				 "psFlashingLights='".$psFlashingLights."', ".
				 "psAmslerGrid='".$psAmslerGrid."', ".
				 "neuroDblVision='".$neuroDblVision."', ".
				 "neuroTempArtSymp='".$neuroTempArtSymp."', ".
				 "neuroVisionLoss='".$neuroVisionLoss."', ".
				 "neuroHeadaches='".$neuroHeadaches."', ".
				 "neuroMigHead='".$neuroMigHead."', ".
				 "neuroOther='".$neuroOther."', ".
				 "complaint1Str='".$complaint1Text."', ".
				 "complaint2Str='".$complaint2Text."', ".
				 "complaint3Str='".$complaint3Text."', ".
				 "complaintHead='".$complaintHead."', ".
				 "selectedHeadText='".$selectedHead."', ".
				 "titleHead ='".$titleHead."', ".
				 "lidsYesNo ='".$lidsYesNo."', ".
				 "noFlashing ='".$noFlashing."', ".
				 "noFloaters ='".$noFloaters."', ".
				 "THAYesNo ='".$THAYesNo."', ".
				 "rvspostop='".$rvspostop."', ".
				 "rvsfollowup='".$rvsfollowup."', ".
				 "vpComment='".$vpComment."', ".
				 "vpDate='".$vpDate."', ";
			}

			$sql .="ocularMeds='".$ocularMeds."', ".
				 "ptinfoDesc='".$ptinfoDesc."', ".
				 "uid = '".$_SESSION["authId"]."' ".

				 "WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
			$res=sqlQuery($sql);
		}

		//
		$this->archiveChartGenHx($arrGridOcularMeds);

	}

	function saveLasik(){
		$patient_id = $this->pid;
		$form_id = $this->fid;

		if(!empty($_POST["el_lasik_userid"])){

		$tmp_lasik_trgt_method = sqlEscStr($_POST["el_lasik_trgt_method"]);
		$tmp_visLasikTrgtDate = wv_formatDate($_POST["el_visLasikTrgtDate"],0,0,'insert');

		$tmp_visLasikTrgtTime = sqlEscStr($_POST["el_visLasikTrgtTime"]);
		$tmp_lasik_trgt_intervention = sqlEscStr($_POST["el_lasik_trgt_intervention"]);
		$tmp_visLasikTrgtMicKera = sqlEscStr($_POST["el_visLasikTrgtMicKera"]);
		$tmp_lasik_trgt_Excimer = sqlEscStr($_POST["el_lasik_trgt_Excimer"]);
		$tmp_lasik_trgt_mode = sqlEscStr($_POST["el_lasik_trgt_mode"]);
		$tmp_lasik_trgt_opti_zone = sqlEscStr($_POST["el_lasik_trgt_opti_zone"]);
		$tmp_lasik_userid = $_POST["el_lasik_userid"];

		$tmp_lasik_trgt="";
		$tmp_visLasikTrgt["Od"]["S"] = $_POST["el_visLasikTrgtOdS"];
		$tmp_visLasikTrgt["Od"]["C"] = $_POST["el_visLasikTrgtOdC"];
		$tmp_visLasikTrgt["Od"]["A"] = $_POST["el_visLasikTrgtOdA"];
		$tmp_visLasikTrgt["Os"]["S"] = $_POST["el_visLasikTrgtOsS"];
		$tmp_visLasikTrgt["Os"]["C"] = $_POST["el_visLasikTrgtOsC"];
		$tmp_visLasikTrgt["Os"]["A"] = $_POST["el_visLasikTrgtOsA"];
		$tmp_visLasikTrgt["Desc"] = $_POST["el_visLasikTrgtDesc"];
		$tmp_lasik_trgt = json_encode($tmp_visLasikTrgt);
		$tmp_lasik_trgt = sqlEscStr($tmp_lasik_trgt);

		$tmp_lasik_lsr="";
		$tmp_visLasikLsr["Od"]["S"] = $_POST["el_visLasikLsrOdS"];
		$tmp_visLasikLsr["Od"]["C"] = $_POST["el_visLasikLsrOdC"];
		$tmp_visLasikLsr["Od"]["A"] = $_POST["el_visLasikLsrOdA"];
		$tmp_visLasikLsr["Os"]["S"] = $_POST["el_visLasikLsrOsS"];
		$tmp_visLasikLsr["Os"]["C"] = $_POST["el_visLasikLsrOsC"];
		$tmp_visLasikLsr["Os"]["A"] = $_POST["el_visLasikLsrOsA"];
		$tmp_visLasikLsr["Desc"] = $_POST["el_visLasikLsrDesc"];
		$tmp_lasik_lsr = json_encode($tmp_visLasikLsr);
		$tmp_lasik_lsr = sqlEscStr($tmp_lasik_lsr);

		//check
		$cQry = "select id FROM chart_vis_lasik WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
		$row = sqlQuery($cQry);
		$elem_editModeVis = ($row == false) ? "0" : "1";
		if($elem_editModeVis == "0"){
			$sql = "INSERT INTO chart_vis_lasik SET
				patient_id='".$patient_id."',
				form_id='".$form_id."', ";
			$sql_w = "";

		}else{
			$id = $row["id"];
			$sql = "UPDATE chart_vis_lasik SET ";
			$sql_w = "WHERE id='".$id."' ";
		}

		$sql_con = "
			method='".$tmp_lasik_trgt_method."',
			date_lasik='".$tmp_visLasikTrgtDate."',
			time_lasik='".$tmp_visLasikTrgtTime."',
			intervention='".$tmp_lasik_trgt_intervention."',
			microkeratome='".$tmp_visLasikTrgtMicKera."',
			laser_excimer='".$tmp_lasik_trgt_Excimer."',
			laser_mode='".$tmp_lasik_trgt_mode."',
			laser_optical_zone='".$tmp_lasik_trgt_opti_zone."',
			target='".$tmp_lasik_trgt."',
			laser='".$tmp_lasik_lsr."',
			user_id='".$tmp_lasik_userid."',
			date_op='".wv_dt('now')."'
		";

		//
		$sql = $sql.$sql_con.$sql_w;
		$row = sqlQuery($sql);

		}
	}

	function save_distance($vid){
		//
		if(!empty($vid)){
			$sql_in="";
			for($i=1;$i<=6;$i++){
				if($i<=4){
					$sec = ($i==3) ? "Ad. Acuity" : "Distance";
					$sec_num = $i;
					$snellen = ($i==1) ? $_POST["elem_visSnellan"] : "" ;
					if($i>=3){
						$sel_od = isset($_POST["elem_visDisOdSel".$i]) ? $_POST["elem_visDisOdSel".$i] : $_POST["elem_visDisOuSel".$i] ;
						if($i==3){
							$sel_os = isset($_POST["elem_visDisOsSel".$i]) ? $_POST["elem_visDisOsSel".$i] : $_POST["elem_visDisOuSel".$i] ;
						}else{
							$sel_os = $sel_od;
						}
					}else{
						$sel_od=$_POST["elem_visDisOdSel".$i];
						$sel_os=$_POST["elem_visDisOsSel".$i];
					}
					$txt_od=sqlEscStr($_POST["elem_visDisOdTxt".$i]);
					$txt_os=sqlEscStr($_POST["elem_visDisOsTxt".$i]);
					$sel_ou=$_POST["elem_visDisOuSel".$i];
					$txt_ou=sqlEscStr($_POST["elem_visDisOuTxt".$i]);
					$ex_desc="";
					if($i==1){
					$ex_desc = (trim($_POST["elem_disDesc"]) != trim($_POST["elem_disDescLF"])) ? sqlEscStr($_POST["elem_disDesc"]) : "" ;
					}else if($i==3 || $i==4){
					$ex_desc =sqlEscStr($_POST["elem_visDisAct".$i]);
					}
				}else{
					$sec = "Near";
					$sec_num = $i-4;
					//$vis_near=$_POST["elem_visNear"];
					$sel_od=$_POST["elem_visNearOdSel".$sec_num];
					$txt_od=sqlEscStr($_POST["elem_visNearOdTxt".$sec_num]);
					$sel_os=$_POST["elem_visNearOsSel".$sec_num];
					$txt_os=sqlEscStr($_POST["elem_visNearOsTxt".$sec_num]);
					$sel_ou=$_POST["elem_visNearOuSel".$sec_num];
					$txt_ou=sqlEscStr($_POST["elem_visNearOuTxt".$sec_num]);
					if($sec_num==1){
					$ex_desc = sqlEscStr($_POST["elem_visNearDesc"]);
					$snellen = sqlEscStr($_POST["elem_visSnellan_near"]);
					}else{ $snellen = ""; $ex_desc = ""; }
				}

				$sql = "SELECT * FROM chart_acuity where sec_name = '".$sec."' AND id_chart_vis_master = '".$vid."' AND sec_indx='".$sec_num."' ";
				$row = sqlQuery($sql);
				if($row==false){ //INSERT
					if( !empty($snellen) || !empty($ex_desc) || !empty($sel_od) || !empty($txt_od) || !empty($sel_os) || !empty($txt_os) ||
						!empty($sel_ou) || !empty($txt_ou) ){
						$sql_in .= "(NULL, '".$vid."', '".wv_dt("now")."', '".$_SESSION["authId"]."', '".$sec."', '".$sec_num."', '".$snellen."', '".$ex_desc."',
										'".$sel_od."', '".$txt_od."', '".$sel_os."', '".$txt_os."', '".$sel_ou."', '".$txt_ou."' ),";
					}
				}else{
					//UPDATE
					$id = $row["id"];
					$sql = "UPDATE chart_acuity SET uid='".$_SESSION["authId"]."', exam_date='".wv_dt("now")."', snellen='".$snellen."', ex_desc='".$ex_desc."',
								sel_od='".$sel_od."', txt_od='".$txt_od."', sel_os='".$sel_os."', txt_os='".$txt_os."', sel_ou='".$sel_ou."', txt_ou='".$txt_ou."'
							WHERE id='".$id."' ";
					sqlQuery($sql);
				}
			}

			if(!empty($sql_in)){
				$sql_in = trim($sql_in,",");
				$sql_in = "INSERT chart_acuity(id, id_chart_vis_master, exam_date, uid, sec_name, sec_indx, snellen, ex_desc,
											sel_od, txt_od, sel_os, txt_os, sel_ou, txt_ou )
								VALUES ".$sql_in;
				sqlQuery($sql_in);
			}
		}
	}

	function save_pam($vid){
		//PAM
		$pam = $_POST["elem_visPam"];
		$sel1='SC';
		$sel2=sqlEscStr($_POST["elem_visPamOdSel2"]);
		$txt1_od=sqlEscStr($_POST["elem_visPamOdTxt1"]);
		$txt2_od=sqlEscStr($_POST["elem_visPamOdTxt2"]);
		$txt1_os=sqlEscStr($_POST["elem_visPamOsTxt1"]);
		$txt2_os=sqlEscStr($_POST["elem_visPamOsTxt2"]);
		$txt1_ou=sqlEscStr($_POST["elem_visPamOuTxt1"]);
		$txt2_ou=sqlEscStr($_POST["elem_visPamOuTxt2"]);
		$ex_desc = (trim($_POST["elem_pamDesc"]) != trim($_POST["elem_pamDescLF"])) ? sqlEscStr($_POST["elem_pamDesc"]) : "" ;

		$sql = "SELECT * FROM chart_pam where id_chart_vis_master = '".$vid."' ";
		$row = sqlQuery($sql);
		if($row==false){ //INSERT
			if( !empty($txt1_od) || !empty($txt2_od) || !empty($txt1_os) || !empty($txt2_os) || !empty($txt1_ou) || !empty($txt2_ou) ||
				!empty($sel1) || !empty($sel2) || !empty($ex_desc) || !empty($pam) ){
				$sql = "INSERT INTO chart_pam
						 (id, id_chart_vis_master, exam_date, uid, txt1_od, txt2_od, txt1_os, txt2_os, txt1_ou, txt2_ou,
								sel1, sel2, ex_desc, pam)
						VALUES (NULL, '".$vid."', '".wv_dt("now")."', '".$_SESSION["authId"]."', '".$txt1_od."', '".$txt2_od."', '".$txt1_os."', '".$txt2_os."',
								'".$txt1_ou."', '".$txt2_ou."', '".$sel1."', '".$sel2."', '".$ex_desc."', '".$pam."' )";
				sqlQuery($sql);
			}
		}else{
			//UPDATE
			$id = $row["id"];
			$sql = "UPDATE chart_pam SET uid='".$_SESSION["authId"]."', exam_date='".wv_dt("now")."', txt1_od='".$txt1_od."', txt2_od='".$txt2_od."',
						txt1_os='".$txt1_os."', txt2_os='".$txt2_os."', txt1_ou='".$txt1_ou."', txt2_ou='".$txt2_ou."',
						sel1='".$sel1."', sel2='".$sel2."' , ex_desc='".$ex_desc."', pam='".$pam."'
					WHERE id='".$id."' ";
			sqlQuery($sql);
		}
	}

	function save_bat($vid){
		//BAT     --------------------
		$nl_od=sqlEscStr($_POST["elem_visBatNlOd"]);
		$l_od=sqlEscStr($_POST["elem_visBatLowOd"]);
		$m_od=sqlEscStr($_POST["elem_visBatMedOd"]);
		$h_od=sqlEscStr($_POST["elem_visBatHighOd"]);
		$nl_os=sqlEscStr($_POST["elem_visBatNlOs"]);
		$l_os=sqlEscStr($_POST["elem_visBatLowOs"]);
		$m_os=sqlEscStr($_POST["elem_visBatMedOs"]);
		$h_os=sqlEscStr($_POST["elem_visBatHighOs"]);
		$nl_ou=sqlEscStr($_POST["elem_visBatNlOu"]);
		$l_ou=sqlEscStr($_POST["elem_visBatLowOu"]);
		$m_ou=sqlEscStr($_POST["elem_visBatMedOu"]);
		$h_ou=sqlEscStr($_POST["elem_visBatHighOu"]);
		//$vis_bat=sqlEscStr($_POST["elem_mrBat2"]);
		$ex_desc =sqlEscStr( addslashes($_POST["elem_visBatDesc"]));
		//BAT     --------------------

		$sql = "SELECT * FROM chart_bat where id_chart_vis_master = '".$vid."' ";
		$row = sqlQuery($sql);
		if($row==false){ //INSERT
			if( !empty($nl_od) || !empty($l_od) || !empty($m_od) || !empty($h_od) ||
				!empty($nl_os) || !empty($l_os) || !empty($m_os) || !empty($h_os) ||
				!empty($nl_ou) || !empty($l_ou) || !empty($m_ou) || !empty($h_ou) ||
				!empty($ex_desc)
				){
				$sql = "INSERT INTO chart_bat
						 (id, id_chart_vis_master, exam_date, uid,
								nl_od, l_od, m_od, h_od,
								nl_os, l_os, m_os, h_os,
								nl_ou, l_ou, m_ou, h_ou,
								ex_desc)
						VALUES (NULL, '".$vid."', '".wv_dt("now")."', '".$_SESSION["authId"]."',
								'".$nl_od."', '".$l_od."', '".$m_od."', '".$h_od."',
								'".$nl_os."', '".$l_os."', '".$m_os."', '".$h_os."',
								'".$nl_ou."', '".$l_ou."', '".$m_ou."', '".$h_ou."',
								'".$ex_desc."' )";
				sqlQuery($sql);
			}
		}else{
			//UPDATE
			$id = $row["id"];
			$sql = "UPDATE chart_bat SET uid='".$_SESSION["authId"]."', exam_date='".wv_dt("now")."',
						nl_od='".$nl_od."', l_od='".$l_od."', m_od='".$m_od."', h_od='".$h_od."',
						nl_os='".$nl_os."', l_os='".$l_os."', m_os='".$m_os."', h_os='".$h_os."',
						nl_ou='".$nl_ou."', l_ou='".$l_ou."', m_ou='".$m_ou."', h_ou='".$h_ou."',
						ex_desc='".$ex_desc."'
					WHERE id='".$id."' ";
			sqlQuery($sql);
		}
	}

	function save_sca($vid){
		$sql_in = "";
		for($i=1; $i<=4; $i++){
			$sel_od = $sel_os = $ar_ref_place = $ex_desc="";
			if($i==1){
			$sec_name = "AR";
			//AR         -------------------
			$s_od=sqlEscStr($_POST["elem_visArOdS"]);
			$c_od=sqlEscStr($_POST["elem_visArOdC"]);
			$a_od=sqlEscStr($_POST["elem_visArOdA"]);
			$sel_od=$sel_os=$_POST["elem_visArOdSel1"];
			$s_os=sqlEscStr($_POST["elem_visArOsS"]);
			$c_os=sqlEscStr($_POST["elem_visArOsC"]);
			$a_os=sqlEscStr($_POST["elem_visArOsA"]);
			//$sel_os=$_POST["elem_visArOsSel1"];
			$ex_desc = sqlEscStr($_POST["elem_visArDesc"]);
			$ar_ref_place = sqlEscStr($_POST["elem_visArRefPlace"]);
			//AR         -------------------
			}else if($i==2){
			$sec_name = "ARC";
			//Cycloplegic AR  -------------------
			$s_od=sqlEscStr($_POST["elem_visCycArOdS"]);
			$c_od=sqlEscStr($_POST["elem_visCycArOdC"]);
			$a_od=sqlEscStr($_POST["elem_visCycArOdA"]);
			$sel_od=$sel_os=$_POST["elem_visCycArOdSel1"];
			$s_os=sqlEscStr($_POST["elem_visCycArOsS"]);
			$c_os=sqlEscStr($_POST["elem_visCycArOsC"]);
			$a_os=sqlEscStr($_POST["elem_visCycArOsA"]);
			//$sel_os=$_POST["elem_visCycArOsSel1"];
			//$visCycArOuSel1=$_POST["elem_visCycArOuSel1"];
			$ex_desc = sqlEscStr($_POST["elem_visCycArDesc"]);
			//$vis_ar_ref_place = sqlEscStr($_POST["elem_visArRefPlace"]);
			//Cycloplegic AR  -------------------
			}else if($i==3){
			$sec_name = "RETINOSCOPY";
			//Retinoscopy ----------------------
			$s_od =sqlEscStr( $_POST['elem_visExoOdS']);
			$c_od =sqlEscStr( $_POST['elem_visExoOdC']);
			$a_od =sqlEscStr( $_POST['elem_visExoOdA']);
			$s_os =sqlEscStr( $_POST['elem_visExoOsS']);
			$c_os =sqlEscStr( $_POST['elem_visExoOsC']);
			$a_os =sqlEscStr( $_POST['elem_visExoOsA']);
			//$vis_retino_cl = $_POST["elem_retinoCL"];
			//Retinoscopy ----------------------
			}else if($i==4){
			$sec_name = "CYCLOPLEGIC RETINO";
			//Cycloplegic Retino ----------------------
			$s_od =sqlEscStr($_POST['elem_visCycloOdS']);
			$c_od =sqlEscStr($_POST['elem_visCycloOdC']);
			$a_od =sqlEscStr($_POST['elem_visCycloOdA']);
			$s_os =sqlEscStr($_POST['elem_visCycloOsS']);
			$c_os =sqlEscStr($_POST['elem_visCycloOsC']);
			$a_os =sqlEscStr($_POST['elem_visCycloOsA']);
			//Cycloplegic Retino ----------------------
			}

			$sql = "SELECT * FROM chart_sca where id_chart_vis_master = '".$vid."' AND sec_name='".$sec_name."' ";
			$row = sqlQuery($sql);
			if($row==false){ //INSERT
				if( !empty($s_od) || !empty($c_od) || !empty($a_od) || !empty($sel_od) ||
					!empty($s_os) || !empty($c_os) || !empty($a_os) || !empty($sel_os) ||
					!empty($ar_ref_place) || !empty($ex_desc)
					){
					$sql_in .= "(NULL, '".$vid."', '".wv_dt("now")."', '".$_SESSION["authId"]."',
									'".$s_od."', '".$c_od."', '".$a_od."', '".$sel_od."',
									'".$s_os."', '".$c_os."', '".$a_os."', '".$sel_os."',
									'".$ar_ref_place."', '".$ex_desc."', '".$sec_name."'
									),";
				}
			}else{
				//UPDATE
				$id = $row["id"];
				$sql = "UPDATE chart_sca SET uid='".$_SESSION["authId"]."', exam_date='".wv_dt("now")."',
							s_od='".$s_od."', c_od='".$c_od."', a_od='".$a_od."', sel_od='".$sel_od."',
							s_os='".$s_os."', c_os='".$c_os."', a_os='".$a_os."', sel_os='".$sel_os."',
							ar_ref_place='".$ar_ref_place."',
							ex_desc='".$ex_desc."'
						WHERE id='".$id."' ";
				sqlQuery($sql);
			}
		}

		if(!empty($sql_in)){
			$sql_in = trim($sql_in,",");
			$sql_in = "INSERT INTO chart_sca
							 (id, id_chart_vis_master, exam_date, uid,
									s_od, c_od, a_od, sel_od,
									s_os, c_os, a_os, sel_os,
									ar_ref_place, ex_desc, sec_name)
							VALUES ".$sql_in;
			sqlQuery($sql_in);
		}
	}

	function save_exo($vid){
		$pd =sqlEscStr( $_POST['elem_visRetPD']);
		$pd_od =sqlEscStr( $_POST['elem_visRetOd']);
		$pd_os =sqlEscStr( $_POST['elem_visRetOs']);

		$sql = "SELECT * FROM chart_exo where id_chart_vis_master = '".$vid."' ";
		$row = sqlQuery($sql);
		if($row==false){ //INSERT
			if(!empty($pd) || !empty($pd_od) || !empty($pd_os) ){
				$sql = "INSERT INTO chart_exo
						 (id, id_chart_vis_master, exam_date, uid,
								pd, pd_od, pd_os)
						VALUES (NULL, '".$vid."', '".wv_dt("now")."', '".$_SESSION["authId"]."',
								'".$pd."', '".$pd_od."', '".$pd_os."' )";
				sqlQuery($sql);
			}
		}else{
			//UPDATE
			$id = $row["id"];
			$sql = "UPDATE chart_exo SET uid='".$_SESSION["authId"]."', exam_date='".wv_dt("now")."',
						pd='".$pd."', pd_od='".$pd_od."', pd_os='".$pd_os."'
					WHERE id='".$id."' ";
			sqlQuery($sql);
		}
	}

	function save_ak($vid){
		//AK         -------------------
		$k_od=sqlEscStr($_POST["elem_visAkOdK"]);
		$slash_od=sqlEscStr($_POST["elem_visAkOdSlash"]);
		$x_od=sqlEscStr($_POST["elem_visAkOdX"]);
		$k_os=sqlEscStr($_POST["elem_visAkOsK"]);
		$slash_os=sqlEscStr($_POST["elem_visAkOsSlash"]);
		$x_os=sqlEscStr($_POST["elem_visAkOsX"]);
		$k_type = $_POST["elem_kType"];
		//$vis_ar_ak_desc=;
		$ex_desc = sqlEscStr($_POST["elem_visAkDesc"]);
		//AK         -------------------

		$sql = "SELECT * FROM chart_ak where id_chart_vis_master = '".$vid."' ";
		$row = sqlQuery($sql);
		if($row==false){ //INSERT
			if(!empty($k_od) || !empty($slash_od) || !empty($x_od) ||
				!empty($k_os) || !empty($slash_os) || !empty($x_os) ||
				!empty($k_type) || !empty($ex_desc) ){
				$sql = "INSERT INTO chart_ak
						 (id, id_chart_vis_master, exam_date, uid,
								k_od, slash_od, x_od,
								k_os, slash_os, x_os,
								k_type, ex_desc
								)
						VALUES (NULL, '".$vid."', '".wv_dt("now")."', '".$_SESSION["authId"]."',
								'".$k_od."', '".$slash_od."', '".$x_od."',
								'".$k_os."', '".$slash_os."', '".$x_os."',
								'".$k_type."', '".$ex_desc."'
								)";
				sqlQuery($sql);
			}
		}else{
			//UPDATE
			$id = $row["id"];
			$sql = "UPDATE chart_ak SET uid='".$_SESSION["authId"]."', exam_date='".wv_dt("now")."',
						k_od='".$k_od."', slash_od='".$slash_od."', x_od='".$x_od."',
						k_os='".$k_os."', slash_os='".$slash_os."', x_os='".$x_os."',
						k_type='".$k_type."', ex_desc='".$ex_desc."'
					WHERE id='".$id."' ";
			sqlQuery($sql);
		}
	}


	function saveVision(){

		$patient_id = $this->pid;
		$form_id = $this->fid;

		//Other-----------------------
		$vis_statusElements = $_POST["elem_statusElements"];
		$vis_id=$_POST["elem_visId"];
		//$form_id=$_POST["elem_formId"];
		//$patient_id=$_POST["elem_patientId"];
		$exam_date=wv_formatDate($_POST["elem_examDate"],0,0, "insert");
		$examinedNoChange=$_POST["elem_noChangeVision"];
		//Other-----------------------
		//ut_elems ----------------------
		$elem_utElems = $_POST["elem_utElems"];
		$elem_utElems_cur = $_POST["elem_utElems_cur"];
		$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);

		//Copy ut Elem to another and remove EOM fields before save.
		$ut_elem_2=$ut_elem;
		$ut_elem = $this->ut_removeEomFields($ut_elem);
		//ut_elems ----------------------

		//check
		$cQry = "select * FROM chart_vis_master WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
		$row = sqlQuery($cQry);
		$elem_editModeVis = ($row == false) ? "0" : "1";
		if($elem_editModeVis == "0"){
			$sql = "INSERT INTO chart_vis_master (id, patient_id, form_id, status_elements, ut_elem) ".
				"VALUES (NULL, '".$patient_id."', '".$form_id."', '".$vis_statusElements."', '".$ut_elem."' ) ";
			$vis_id = sqlInsert($sql);
			$this->arG["visionInserted"] = $vis_id;
		}else{
			$vis_id = $row["id"];
			$sql = "UPDATE chart_vis_master ".
				"SET ".
				"status_elements='".$vis_statusElements."', ut_elem='".$ut_elem."' ".
				"WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
			$res = sqlQuery($sql);
		}

		//
		if(!empty($vis_id)){
			$this->save_distance($vis_id);
			$this->save_pam($vis_id);
			$this->save_bat($vis_id);
			$this->save_sca($vis_id);
			$this->save_exo($vis_id);
			$this->save_ak($vis_id);
			// MORE MR / PC
			$this->save_pc_mr($vis_id);

			//HL 7
			$this->vis_mr_given_HL7();
		}
	}

	//HL7 processing when MR is given
	function vis_mr_given_HL7(){
		if(isset($this->arG["mrGiven4HL7"]) && count($this->arG["mrGiven4HL7"])>0){
			/*********NEW HL7 ENGINE START************/
			require_once(dirname(__FILE__)."/../../../hl7sys/api/class.HL7Engine.php");
			foreach($this->arG["mrGiven4HL7"] as $givenMR){
				$objHL7Engine = new HL7Engine();
				$objHL7Engine->application_module = 'workview';
				if(!$objHL7Engine->check_new_old_msg_for_same_sourceid($this->fid,'ZMS'))$objHL7Engine->msgSubType = 'add_prescription'; else $objHL7Engine->msgSubType = 'update_prescription';
				$objHL7Engine->source_id = $this->fid;
				$objHL7Engine->patient_id = $this->pid;
				$objHL7Engine->ZMSgivenMR = $givenMR;
				$objHL7Engine->generateHL7();
				unset($objHL7Engine);
			}
			/*********NEW HL7 ENGINE END*************/

			if(defined('HL7_ZMS_GENERATION') && constant('HL7_ZMS_GENERATION') === true){
				require_once(dirname(__FILE__)."/../../../hl7sys/old/CLS_makeHL7.php");
				$makeHL7 = new makeHL7();
				$makeHL7->givenMRs = $this->arG["mrGiven4HL7"];
				$makeHL7->log_HL7_message($this->fid,'ZMS');
			}
		}


	}

	function save_pc_mr($vid){

		$patient_id = $this->pid;
		$form_id = $this->fid;
		$exam_date=wv_formatDate($_POST["elem_examDate"],0,0, "insert");
		$op_id = $_SESSION["authId"];
		$vis_statusElements = $_POST["elem_statusElements"];

		$c=1;
		$sql_in="";
		while($c<50){

			//
			$inx1=""; $inx2="";
			if($c > "1"){
				$inx1="Other";

				if($c > "2"){
					$inx2="_".$c;
				}
			}

			//MR

			//
			$chart_pc_mr_id=0;
			$sql = "SELECT id,strhash FROM chart_pc_mr WHERE id_chart_vis_master='".$vid."' AND ex_number='".$c."' AND ex_type='MR' AND delete_by='0'  ";
			$row = sqlQuery($sql);
			if($row!=false){
				$chart_pc_mr_id = $row["id"];
				$hs_mr3_prv = $row["strhash"];
			}

			$vis_mr_desc = ($_POST["elem_visMrDesc".$inx1."".$inx2] != $_POST["elem_visMrDesc".$inx1."LF".$inx2]) ? $_POST["elem_visMrDesc".$inx1."".$inx2] : "" ;

			$od_txt1 = trim($_POST["elem_visMr".$inx1."OdTxt1".$inx2]); if($od_txt1 == "20/"){ $od_txt1=""; }
			$od_txt2 = trim($_POST["elem_visMr".$inx1."OdTxt2".$inx2]); if($od_txt2 == "20/"){ $od_txt2=""; }
			$ou_txt1 = trim($_POST["elem_visMr".$inx1."OuTxt1".$inx2]); if($ou_txt1 == "20/"){ $ou_txt1=""; }
			$os_txt1 = trim($_POST["elem_visMr".$inx1."OsTxt1".$inx2]); if($os_txt1 == "20/"){ $os_txt1=""; }
			$os_txt2 = trim($_POST["elem_visMr".$inx1."OsTxt2".$inx2]); if($os_txt2 == "20/"){ $os_txt2=""; }
			$od_sel2Vis = trim($_POST["elem_visMr".$inx1."OdSel2Vision".$inx2]); if($od_sel2Vis == "20/"){ $od_sel2Vis=""; }
			$os_sel2Vis = trim($_POST["elem_visMr".$inx1."OsSel2Vision".$inx2]); if($os_sel2Vis == "20/"){ $os_sel2Vis=""; }

			if(!empty($_POST["elem_visMr".$inx1."OdS".$inx2]) || !empty($_POST["elem_visMr".$inx1."OdC".$inx2]) || !empty($_POST["elem_visMr".$inx1."OdA".$inx2]) ||
				!empty($od_txt1) || !empty($_POST["elem_visMr".$inx1."OdSel2".$inx2]) || !empty($od_txt2)	||
				!empty($_POST["elem_visMr".$inx1."OdAdd".$inx2]) ||
				!empty($_POST["elem_visMr".$inx1."OdP".$inx2]) ||
				!empty($_POST["elem_visMr".$inx1."OdPrism".$inx2]) ||
				!empty($_POST["elem_visMr".$inx1."OdSlash".$inx2]) ||
				!empty($_POST["elem_visMr".$inx1."OdSel1".$inx2]) ||
				!empty($od_sel2Vis) ||

				!empty($_POST["elem_visMr".$inx1."OsS".$inx2]) || !empty($_POST["elem_visMr".$inx1."OsC".$inx2]) || !empty($_POST["elem_visMr".$inx1."OsA".$inx2]) ||
				!empty($os_txt1) || !empty($_POST["elem_visMr".$inx1."OsSel2".$inx2]) || !empty($os_txt2) ||
				!empty($_POST["elem_visMr".$inx1."OsAdd".$inx2]) ||
				!empty($_POST["elem_visMr".$inx1."OsP".$inx2]) ||
				!empty($_POST["elem_visMr".$inx1."OsPrism".$inx2]) ||
				!empty($_POST["elem_visMr".$inx1."OsSlash".$inx2]) ||
				!empty($_POST["elem_visMr".$inx1."OsSel1".$inx2]) ||
				!empty($os_sel2Vis) ||

				!empty($ou_txt1) ||
				!empty($_POST["elem_visMrPrismDesc_".$c]) ||
				!empty($_POST["elem_mr_type".$c]) ||
				!empty($vis_mr_desc) ||
				!empty($_POST["elem_mr_pres_dt_".$c]) ||
				!empty($chart_pc_mr_id)

			){

				$providerIdOther =sqlEscStr( $_POST["elem_providerId".$inx1."".$inx2]);

				//if(empty($providerIdOther)){ $c++; continue;  } //

				$mrPrism =sqlEscStr( $_POST["elem_mrPrism".$c]);
				$vis_mr_type=sqlEscStr($_POST["elem_mr_type".$c]);
				$sph["OD"] =sqlEscStr( $_POST["elem_visMr".$inx1."OdS".$inx2]);
				$cyl["OD"] =sqlEscStr( $_POST["elem_visMr".$inx1."OdC".$inx2]);
				$axs["OD"] =sqlEscStr( $_POST["elem_visMr".$inx1."OdA".$inx2]);
				$txt_1["OD"] =sqlEscStr( $od_txt1);
				$sel_2["OD"] =sqlEscStr( $_POST["elem_visMr".$inx1."OdSel2".$inx2]);
				$txt_2["OD"] =sqlEscStr( $od_txt2);
				$ad["OD"] =sqlEscStr( $_POST["elem_visMr".$inx1."OdAdd".$inx2]);
				$sph["OS"] =sqlEscStr( $_POST["elem_visMr".$inx1."OsS".$inx2]);
				$cyl["OS"] =sqlEscStr( $_POST["elem_visMr".$inx1."OsC".$inx2]);
				$axs["OS"] =sqlEscStr( $_POST["elem_visMr".$inx1."OsA".$inx2]);
				$txt_1["OS"] =sqlEscStr( $os_txt1);
				$sel_2["OS"] = $sel_2["OD"]; //sqlEscStr( $_POST["elem_visMrOtherOsSel2_".$c]);
				$txt_2["OS"] =sqlEscStr( $os_txt2);
				$ad["OS"] =sqlEscStr( $_POST["elem_visMr".$inx1."OsAdd".$inx2]);
				$visMrOtherOuTxt1 =sqlEscStr( $ou_txt1);
				$prsm_p["OD"] =sqlEscStr( $_POST["elem_visMr".$inx1."OdP".$inx2]);
				$prism["OD"] =sqlEscStr( $_POST["elem_visMr".$inx1."OdPrism".$inx2]);
				$slash["OD"] =sqlEscStr( $_POST["elem_visMr".$inx1."OdSlash".$inx2]);
				$sel_1["OD"] =sqlEscStr( $_POST["elem_visMr".$inx1."OdSel1".$inx2]);
				$prsm_p["OS"] =sqlEscStr( $_POST["elem_visMr".$inx1."OsP".$inx2]);
				$prism["OS"] =sqlEscStr( $_POST["elem_visMr".$inx1."OsPrism".$inx2]);
				$slash["OS"] =sqlEscStr( $_POST["elem_visMr".$inx1."OsSlash".$inx2]);
				$sel_1["OS"] =sqlEscStr( $_POST["elem_visMr".$inx1."OsSel1".$inx2]);
				$vis_mr_desc =sqlEscStr($vis_mr_desc);

				$mrGLPH =sqlEscStr( $_POST["elem_mrGLPH".$c]);
				$sel2v["OD"] =sqlEscStr( $od_sel2Vis);
				$sel2v["OS"] =sqlEscStr( $os_sel2Vis);
				$visMrCL =sqlEscStr( $_POST["elem_mrCL".$c]);
				$strMrCyclopegic="".$_POST["elem_mrCyclopegic".$c];
				if(!empty($_POST["elem_mrNoneGiven".$c])){ $vis_mr_none_given =sqlEscStr($_POST["elem_mrNoneGiven".$c]);}else{ $vis_mr_none_given = ""; }
				$visMrPrismDesc =sqlEscStr($_POST["elem_visMrPrismDesc_".$c]);
				$vis_mr_pres_dt = wv_formatDate($_POST["elem_mr_pres_dt_".$c],0,0, "insert");

				//Hash --
				$str_hash = "";
				if(strpos($vis_statusElements, "elem_providerId".$inx1."".$inx2."=1") !==false){$str_hash .= $providerIdOther;}
				if(strpos($vis_statusElements, "elem_mrPrism".$c."=1") !==false){$str_hash .= $mrPrism;}
				if(strpos($vis_statusElements, "elem_mr_type".$c."=1") !==false){$str_hash .= $vis_mr_type;}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdS".$inx2."=1") !==false){$str_hash .= $sph["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdC".$inx2."=1") !==false){$str_hash .= $cyl["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdA".$inx2."=1") !==false){$str_hash .= $axs["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdTxt1".$inx2."=1") !==false){$str_hash .= $txt_1["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdSel2".$inx2."=1") !==false){$str_hash .= $sel_2["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdTxt2".$inx2."=1") !==false){$str_hash .= $txt_2["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdAdd".$inx2."=1") !==false){$str_hash .= $ad["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsS".$inx2."=1") !==false){$str_hash .= $sph["OS"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsC".$inx2."=1") !==false){$str_hash .= $cyl["OS"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsA".$inx2."=1") !==false){$str_hash .= $axs["OS"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsTxt1".$inx2."=1") !==false){$str_hash .= $txt_1["OS"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsTxt2".$inx2."=1") !==false){$str_hash .= $txt_2["OS"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsAdd".$inx2."=1") !==false){$str_hash .= $ad["OS"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OuTxt1".$inx2."=1") !==false){$str_hash .= $visMrOtherOuTxt1;}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdP".$inx2."=1") !==false){$str_hash .= $prsm_p["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdPrism".$inx2."=1") !==false){$str_hash .= $prism["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdSlash".$inx2."=1") !==false){$str_hash .= $slash["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdSel1".$inx2."=1") !==false){$str_hash .= $sel_1["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsP".$inx2."=1") !==false){$str_hash .= $prsm_p["OS"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsPrism".$inx2."=1") !==false){$str_hash .= $prism["OS"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsSlash".$inx2."=1") !==false){$str_hash .= $slash["OS"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsSel1".$inx2."=1") !==false){$str_hash .= $sel_1["OS"];}
				if(strpos($vis_statusElements, "elem_visMrDesc".$inx1."".$inx2."=1") !==false){$str_hash .= $vis_mr_desc;}
				if(strpos($vis_statusElements, "elem_mrGLPH".$c."=1") !==false){$str_hash .= $mrGLPH;}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OdSel2Vision".$inx2."=1") !==false){$str_hash .= $sel2v["OD"];}
				if(strpos($vis_statusElements, "elem_visMr".$inx1."OsSel2Vision".$inx2."=1") !==false){$str_hash .= $sel2v["OS"];}
				if(strpos($vis_statusElements, "elem_mrCL".$c."=1") !==false){$str_hash .= $visMrCL;}
				if(strpos($vis_statusElements, "elem_mrCyclopegic".$c."=1") !==false){$str_hash .= $strMrCyclopegic;}
				if(strpos($vis_statusElements, "elem_mrNoneGiven".$c."=1") !==false){$str_hash .= $vis_mr_none_given;}
				if(strpos($vis_statusElements, "elem_visMrPrismDesc_".$c."=1") !==false){$str_hash .= $visMrPrismDesc;}
				if(strpos($vis_statusElements, "elem_mr_pres_dt_".$c."=1") !==false){$str_hash .= $vis_mr_pres_dt;}
				$hs_mr3 = hash("md5",$str_hash);
				$strhash = sqlEscStr($hs_mr3);



				//
				$sql_2 = "
						exam_date='".$exam_date."',
						provider_id='".$providerIdOther."',
						mr_none_given='".$vis_mr_none_given."',
						mr_cyclopegic='".$strMrCyclopegic."',
						mr_pres_date='".$vis_mr_pres_dt."',
							mr_ou_txt_1='".$visMrOtherOuTxt1."',
							mr_type='".$vis_mr_type."',
							ex_desc='".$vis_mr_desc."',
							prism_desc='".$visMrPrismDesc."',
							uid = '".$op_id."',
							strhash='".$strhash."'

						";


				if(!empty($chart_pc_mr_id)){
					//UPDATE
					$sql_1 = " UPDATE chart_pc_mr SET ";
					$sql_3 = " WHERE id='".$chart_pc_mr_id."' ";
					//Sql
					$sql = $sql_1.$sql_2.$sql_3;
					$r = sqlQuery($sql);

					//hl7--
					if(!empty($vis_mr_none_given) && strpos($vis_statusElements, "elem_mr_pres_dt_".$c."=1") !==false &&
									strpos($vis_statusElements, "elem_mrNoneGiven".$c."=1") !==false &&
									strpos($vis_statusElements, "elem_providerId".$inx1."".$inx2."=1") !==false){
						if($hs_mr3_prv != $hs_mr3){$this->arG["mrGiven4HL7"][] = "MR ".$c;}
					}

				}else{
					//Insert
					$sql_1 = "INSERT INTO chart_pc_mr SET  ";
					$sql_2 .= ", id_chart_vis_master='".$vid."', ".
							" ex_type='MR', ".
							" ex_number='".$c."' ".
							"";
					$sql_3 = "";

					//Sql
					$sql = $sql_1.$sql_2.$sql_3;
					$chart_pc_mr_id = sqlInsert($sql);

					//hl7--
					if(!empty($vis_mr_none_given) && strpos($vis_statusElements, "elem_mr_pres_dt_".$c."=1") !==false &&
									strpos($vis_statusElements, "elem_mrNoneGiven".$c."=1") !==false &&
									strpos($vis_statusElements, "elem_providerId".$inx1."".$inx2."=1") !==false){
						$this->arG["mrGiven4HL7"][] = "MR ".$c;
					}

				}

				// chart_pc_mr_value
				if(!empty($chart_pc_mr_id)){
					$ar_site = array("OD", "OS");
					foreach($ar_site as $k => $v){

						$site = $v;
						$chart_pc_mr_values_id = 0 ;
						$sql = " SELECT id FROM chart_pc_mr_values where chart_pc_mr_id = '".$chart_pc_mr_id."' AND site='".$site."'  ";
						$row = sqlQuery($sql);
						if($row!=false){$chart_pc_mr_values_id = $row["id"];}
						$sql_1 = $sql_2 = $sql_3 = "";

						$sql_2 = "
							sph='".$sph[$site]."',
							cyl='".$cyl[$site]."',
							axs='".$axs[$site]."',
							ad='".$ad[$site]."',
							prsm_p='".$prsm_p[$site]."',
							prism='".$prism[$site]."',
							slash='".$slash[$site]."',
							sel_1='".$sel_1[$site]."',
							sel_2='".$sel_2[$site]."',
							txt_1='".$txt_1[$site]."',
							txt_2='".$txt_2[$site]."',
							sel2v='".$sel2v[$site]."'
						";

						if(!empty($chart_pc_mr_values_id)){
							//UPDATE
							$sql_1 = "UPDATE chart_pc_mr_values SET  ";
							$sql_3 =  " WHERE id = '".$chart_pc_mr_values_id."' AND site='".$site."' ";
							//
							$sql = $sql_1.$sql_2.$sql_3;
							$r = sqlQuery($sql);
						}else{
							//INSERT
							/*
							$sql_1 = "INSERT INTO chart_pc_mr_values SET  ";
							$sql_2 .= ", chart_pc_mr_id = '".$chart_pc_mr_id."', site='".$site."' ";
							$sql_3 =  " ";
							//Sql
							$sql = $sql_1.$sql_2.$sql_3;
							$chart_pc_mr_values_id = sqlInsert($sql);
							*/

							$sql_in .= "(NULL, '".$chart_pc_mr_id."', '".$site."',
											'".$sph[$site]."', '".$cyl[$site]."', '".$axs[$site]."', '".$ad[$site]."',
											'".$prsm_p[$site]."', '".$prism[$site]."', '".$slash[$site]."', '".$sel_1[$site]."', '".$sel_2[$site]."',
											'','','','',
											'".$txt_1[$site]."','".$txt_2[$site]."',	'".$sel2v[$site]."'),";


						}
					}
				}
				$c++;

			}else{
				if(!isset($_POST["elem_visMr".$inx1."OdS".$inx2]) && !isset($_POST["elem_visMr".$inx1."OdC".$inx2]) && !isset($_POST["elem_visMr".$inx1."OdA".$inx2]) &&
					!isset($_POST["elem_visMr".$inx1."OsS".$inx2]) && !isset($_POST["elem_visMr".$inx1."OsC".$inx2]) && !isset($_POST["elem_visMr".$inx1."OsA".$inx2]) ){
					break;
				}else{ $c++; }
			}
		}


		//--


		//PC

		//print_r($_POST);

		$inx=1;
		while($inx<50){

			$c = ($inx==1) ? "" : $inx;

			if(isset($_POST["elem_visPcOdS".$c]) && isset($_POST["elem_visPcOdC".$c]) && isset($_POST["elem_visPcOdA".$c]) ||
				isset($_POST["elem_visPcOsS".$c]) && isset($_POST["elem_visPcOsC".$c]) && isset($_POST["elem_visPcOsA".$c])
			){

				//PC  $c     -------------------
				$pc_distance = !empty($_POST["elem_pcDis".$c]) ? "1" : "" ;
				$pc_near=(!empty($_POST["elem_pcNear".$c])) ? $_POST["elem_pcNear".$c] : $_POST["elem_pcOverRef".$c] ;
				if($pc_near=="Near"){ $pc_near="1"; }
				$sel_1["OD"]=sqlEscStr($_POST["elem_visPcOdSel1".$c]);
				$sph["OD"]=sqlEscStr($_POST["elem_visPcOdS".$c]);
				$cyl["OD"]=sqlEscStr($_POST["elem_visPcOdC".$c]);
				$axs["OD"]=sqlEscStr($_POST["elem_visPcOdA".$c]);
				$prsm_p["OD"]=sqlEscStr($_POST["elem_visPcOdP".$c]);
				$prism["OD"]=sqlEscStr($_POST["elem_visPcOdPrism".$c]);
				$slash["OD"]=sqlEscStr($_POST["elem_visPcOdSlash".$c]);
				$sel_2["OD"]=sqlEscStr($_POST["elem_visPcOdSel2".$c]);
				$ad["OD"]=sqlEscStr($_POST["elem_visPcOdAdd".$c]);
				$txt_1["OD"]=sqlEscStr($_POST["elem_visPcOdNearTxt".$c]);
				$ovr_s["OD"]=sqlEscStr($_POST["elem_visPcOdOverrefS".$c]);
				$ovr_c["OD"]=sqlEscStr($_POST["elem_visPcOdOverrefC".$c]);
				$ovr_v["OD"]=sqlEscStr($_POST["elem_visPcOdOverrefV".$c]);
				$ovr_a["OD"]=sqlEscStr($_POST["elem_visPcOdOverrefA".$c]);

				$sel_1["OS"]=sqlEscStr($_POST["elem_visPcOsSel1".$c]);
				$sph["OS"]=sqlEscStr($_POST["elem_visPcOsS".$c]);
				$cyl["OS"]=sqlEscStr($_POST["elem_visPcOsC".$c]);
				$axs["OS"]=sqlEscStr($_POST["elem_visPcOsA".$c]);
				$prsm_p["OS"]=sqlEscStr($_POST["elem_visPcOsP".$c]);
				$prism["OS"]=sqlEscStr($_POST["elem_visPcOsPrism".$c]);
				$slash["OS"]=sqlEscStr($_POST["elem_visPcOsSlash".$c]);
				$sel_2["OS"]=sqlEscStr($_POST["elem_visPcOsSel2".$c]);
				$ad["OS"]=sqlEscStr($_POST["elem_visPcOsAdd".$c]);
				$txt_1["OS"]=sqlEscStr($_POST["elem_visPcOsNearTxt".$c]);
				$ovr_s["OS"]=sqlEscStr($_POST["elem_visPcOsOverrefS".$c]);
				$ovr_c["OS"]=sqlEscStr($_POST["elem_visPcOsOverrefC".$c]);
				$ovr_v["OS"]=sqlEscStr($_POST["elem_visPcOsOverrefV".$c]);
				$ovr_a["OS"]=sqlEscStr($_POST["elem_visPcOsOverrefA".$c]);

				//$prism_pc_3=sqlEscStr($_POST["elem_pcPrism".$c]);
				$vis_pc_desc = ($_POST["elem_visPcDesc".$c] != $_POST["elem_visPcDesc".$c."LF"]) ? sqlEscStr($_POST["elem_visPcDesc".$c]) : "" ;
				$visPcPrismDesc=sqlEscStr($_POST["elem_visPcPrismDesc_".$inx]);
				//PC  $c     -------------------

				//
				$chart_pc_mr_id=0;
				$sql = "SELECT id FROM chart_pc_mr WHERE id_chart_vis_master='".$vid."' AND ex_number='".$inx."' AND ex_type='PC' AND delete_by='0'  ";
				$row = sqlQuery($sql);
				if($row!=false){	$chart_pc_mr_id = $row["id"]; }

				//
				$sql_2 = "
						exam_date='".$exam_date."',
							ex_desc='".$vis_pc_desc."',
							prism_desc='".$visPcPrismDesc."',
							pc_distance='".$pc_distance."',
							pc_near='".$pc_near."',
							uid = '".$op_id."'

						";

				if(!empty($chart_pc_mr_id)){
					//UPDATE
					$sql_1 = " UPDATE chart_pc_mr SET ";
					$sql_3 = " WHERE id='".$chart_pc_mr_id."' ";
					//Sql
					$sql = $sql_1.$sql_2.$sql_3;
					$r = sqlQuery($sql);


				}else{

					if( !empty($sph["OD"]) || !empty($cyl["OD"]) || !empty($axs["OD"]) || !empty($ad["OD"]) ||
						!empty($sph["OS"]) || !empty($cyl["OS"]) || !empty($axs["OS"]) || !empty($ad["OS"]) || !empty($vis_pc_desc) ||
						!empty($pc_distance) || !empty($pc_near)){
						//Insert
						$sql_1 = "INSERT INTO chart_pc_mr SET  ";
						$sql_2 .= ", id_chart_vis_master='".$vid."', ".
								" ex_type='PC', ".
								" ex_number='".$inx."' ".
								"";
						$sql_3 = "";
						//Sql
						$sql = $sql_1.$sql_2.$sql_3;
						$chart_pc_mr_id = sqlInsert($sql);
					}
				}

				// chart_pc_mr_value
				if(!empty($chart_pc_mr_id)){
					$ar_site = array("OD", "OS");
					foreach($ar_site as $k => $v){

						$site = $v;
						$chart_pc_mr_values_id = 0 ;
						$sql = " SELECT id FROM chart_pc_mr_values where chart_pc_mr_id = '".$chart_pc_mr_id."' AND site='".$site."'  ";
						$row = sqlQuery($sql);
						if($row!=false){$chart_pc_mr_values_id = $row["id"];}
						$sql_1 = $sql_2 = $sql_3 = "";

						$sql_2 = "
							sph='".$sph[$site]."',
							cyl='".$cyl[$site]."',
							axs='".$axs[$site]."',
							ad='".$ad[$site]."',
							prsm_p='".$prsm_p[$site]."',
							prism='".$prism[$site]."',
							slash='".$slash[$site]."',
							sel_1='".$sel_1[$site]."',
							sel_2='".$sel_2[$site]."',
							txt_1='".$txt_1[$site]."',
							ovr_s='".$ovr_s[$site]."',
							ovr_c='".$ovr_c[$site]."',
							ovr_v='".$ovr_v[$site]."',
							ovr_a='".$ovr_a[$site]."'
						";

						if(!empty($chart_pc_mr_values_id)){
							//UPDATE
							$sql_1 = "UPDATE chart_pc_mr_values SET  ";
							$sql_3 =  " WHERE id = '".$chart_pc_mr_values_id."' AND site='".$site."' ";
							//
							$sql = $sql_1.$sql_2.$sql_3;
							$r = sqlQuery($sql);
						}else{
							//INSERT
							/*
							$sql_1 = "INSERT INTO chart_pc_mr_values SET  ";
							$sql_2 .= ", chart_pc_mr_id = '".$chart_pc_mr_id."', site='".$site."' ";
							$sql_3 =  " ";
							//Sql
							$sql = $sql_1.$sql_2.$sql_3;
							$chart_pc_mr_values_id = sqlInsert($sql);
							*/

							$sql_in .= "(NULL, '".$chart_pc_mr_id."', '".$site."',
											'".$sph[$site]."', '".$cyl[$site]."', '".$axs[$site]."', '".$ad[$site]."',
											'".$prsm_p[$site]."', '".$prism[$site]."', '".$slash[$site]."', '".$sel_1[$site]."', '".$sel_2[$site]."',
											'".$ovr_s[$site]."','".$ovr_c[$site]."','".$ovr_v[$site]."','".$ovr_a[$site]."',
											'".$txt_1[$site]."','',''),";


						}
					}
				}
				$inx++;

			}else{
				break;
			}
		}

		//--------------

		//$sql_in_1 = "";
		if(!empty($sql_in)){
			$sql_in = trim($sql_in,",");
			$sql_in = "INSERT INTO chart_pc_mr_values (id, chart_pc_mr_id, site,
						sph, cyl, axs, ad,
						prsm_p, prism, slash, sel_1, sel_2,
						ovr_s, ovr_c, ovr_v, ovr_a,
						txt_1, txt_2, sel2v)
					VALUES ".$sql_in;
			imw_query($sql_in);
		}
	}

	function copyFromEvaluations($addNew=0, $clws_id_eval=0){

		$evaluationResult = imw_query("select * from contactlens_evaluations where clws_id='".$_REQUEST['copyFromId']."'");			// Get source sheet evaluations
		//pre("select * from contactlens_evaluations where clws_id='".$_REQUEST['copySheetId']."'");

		if(imw_num_rows($evaluationResult) > 0){
			$fieldArray = array();
			$eyeArray = array("OD", "OS");
			$sclDVAOU = "";
			$sclNVAOU = "";
			$rgpDVAOU = "";
			$rgpNVAOU = "";
			while($fieldRow = imw_fetch_array($evaluationResult)){
				foreach($eyeArray as $eye){
					$fieldArray[] = "CLSLCEvaluationSphere".$eye."='".$fieldRow["CLSLCEvaluationSphere".$eye]."'";
					$fieldArray[] = "CLSLCEvaluationCylinder".$eye."='".$fieldRow["CLSLCEvaluationCylinder".$eye]."'";
					$fieldArray[] = "CLSLCEvaluationAxis".$eye."='".$fieldRow["CLSLCEvaluationAxis".$eye]."'";
					$fieldArray[] = "CLSLCEvaluationDVA".$eye."='".$fieldRow["CLSLCEvaluationDVA".$eye]."'";

					$sclDVAOU = "'".$fieldRow["CLSLCEvaluationDVAOU"]."'";
					$sclNVAOU = "'".$fieldRow["CLSLCEvaluationNVAOU"]."'";

					$rgpDVAOU = "'".$fieldRow["CLRGPEvaluationDVAOU"]."'";
					$rgpNVAOU = "'".$fieldRow["CLRGPEvaluationNVAOU"]."'";

					$fieldArray[] = "CLSLCEvaluationSphereNVA".$eye."='".$fieldRow["CLSLCEvaluationSphereNVA".$eye]."'";
					$fieldArray[] = "CLSLCEvaluationCylinderNVA".$eye."='".$fieldRow["CLSLCEvaluationCylinderNVA".$eye]."'";
					$fieldArray[] = "CLSLCEvaluationAxisNVA".$eye."='".$fieldRow["CLSLCEvaluationAxisNVA".$eye]."'";
					$fieldArray[] = "CLSLCEvaluationNVA".$eye."='".$fieldRow["CLSLCEvaluationNVA".$eye]."'";

					$fieldArray[] = "CLRGPEvaluationSphere".$eye."='".$fieldRow["CLRGPEvaluationSphere".$eye]."'";
					$fieldArray[] = "CLRGPEvaluationCylinder".$eye."='".$fieldRow["CLRGPEvaluationCylinder".$eye]."'";
					$fieldArray[] = "CLRGPEvaluationAxis".$eye."='".$fieldRow["CLRGPEvaluationAxis".$eye]."'";
					$fieldArray[] = "CLRGPEvaluationDVA".$eye."='".$fieldRow["CLRGPEvaluationDVA".$eye]."'";

					$fieldArray[] = "CLRGPEvaluationSphereNVA".$eye."='".$fieldRow["CLRGPEvaluationSphere".$eye]."'";
					$fieldArray[] = "CLRGPEvaluationCylinderNVA".$eye."='".$fieldRow["CLRGPEvaluationCylinder".$eye]."'";
					$fieldArray[] = "CLRGPEvaluationAxisNVA".$eye."='".$fieldRow["CLRGPEvaluationAxis".$eye]."'";
					$fieldArray[] = "CLRGPEvaluationDVA".$eye."='".$fieldRow["CLRGPEvaluationDVA".$eye]."'";
				}
			}
			$fieldArray[] = "CLSLCEvaluationDVAOU=".$sclDVAOU;
			$fieldArray[] = "CLRGPEvaluationDVAOU=".$rgpDVAOU;

			$fieldArray[] = "CLSLCEvaluationNVAOU=".$sclNVAOU;
			$fieldArray[] = "CLRGPEvaluationNVAOU=".$rgpNVAOU;

			/* $clEvalInsertQuery = "insert into contactlens_evaluations set clws_id='".$_REQUEST['copied_to_clws_id']."', ";
			$whereClause = "";
			$clEvalAlreadyExistingResult = imw_query("select id from contactlens_evaluations where clws_id='".$_REQUEST['copied_to_clws_id']."'"); */

			$eval_CLWSID=0;
			if($addNew==1){
				$eval_CLWSID=$clws_id_eval;
			}else{
				$eval_CLWSID=$_REQUEST['clws_id'];
			}
			$clEvalInsertQuery = "update contactlens_evaluations set ".implode(",", $fieldArray)." where clws_id='".$eval_CLWSID."'";
			imw_query($clEvalInsertQuery) or die("Unable to add contact lens evaluations: ".imw_error());
		}
	}

	function saveContactLens(){
		if($_REQUEST["recordSave"]=='saveTrue'){
			// Set prescibed by id in order
			if($_REQUEST["clws_id"]!=''){
				$rs = imw_query("Select print_order_id from clprintorder_master WHERE clws_id='".$_REQUEST["clws_id"]."'");
				if(imw_num_rows($rs) > 0){
					imw_query("update clprintorder_master set prescribed_by='".$_SESSION['authId']."' where clws_id='".$_REQUEST["clws_id"]."'");
				}
			}
			if($_REQUEST['elem_clPopUpSaved']=='' && $_REQUEST['recordSave']=="saveTrue"){
				$sessFormID = $_SESSION['finalize_id'];
				if($_SESSION['form_id'] != ''){
					$sessFormID = $_SESSION['form_id'];
				}

				if($sessFormID!=''){
					$cl_order = (isset($_REQUEST['cl_order']) && $_REQUEST['cl_order']!='') ? 1 : 0;
					imw_query("UPDATE chart_master_table SET cl_order = '".$cl_order ."' WHERE id = '".$sessFormID."'");

					$clForm_id = $sessFormID;
				}
				$delOldVals = 0;	$orderExists=0; $addNew =1; $doChanges=0;


				$clws_type = $_REQUEST['clws_types'];
				$clws_type_arr = explode(',',$_REQUEST['clws_types']);

				$clws_type_dd = $clws_type;

				$old_clws_type = explode(',',$_REQUEST['clws_type_old']);
				$arrSaveDiff = array_diff($clws_type_arr, $old_clws_type);
				if(sizeof($arrSaveDiff)<=0){
					$arrSaveDiff = array_diff($old_clws_type, $clws_type_arr);
				}
				if($_REQUEST['otherSave']!='' && $_REQUEST['otherSaveVal']!= $_REQUEST['otherSave']){
					$clws_type =','.$_REQUEST['otherSaveVal'];
				}
				$clws_type_label = $clws_type;

				$arrCharges_ids = $_REQUEST["clws_charges"];
				$charges_ids = implode(',',$arrCharges_ids);

				// CHECK IF PRINT ORDER EXIST FOR WORKSHEET
				if($_REQUEST["clws_id"]!=''){
					$rs=imw_query("Select print_order_id from clprintorder_master WHERE clws_id='".$_REQUEST["clws_id"]."'");
					if(imw_num_rows($rs) > 0){ $orderExists=1; }
				}

				if(in_array('Current Trial', $clws_type_arr) && $_REQUEST['clws_trial_number']!=$_REQUEST['trial_no_old'])
				{
					if($_REQUEST['clws_trial_number']!=''){
						$doChanges=1;
					}
				}else if($_REQUEST["clws_id"]=='' || ($_REQUEST["clws_id"]!='' && $orderExists==0)){
					$doChanges=1;
				}else if($_REQUEST["clws_id"] > 0){
					$doChanges=1;
				}

				if($doChanges==1){	// SAVE ONLY IF ORDER NOT MADE

			//		if($_REQUEST["worksheetDOS"]){
			//			$dos = $_REQUEST["worksheetDOS"];//Y-m-d
			//		}else{
						$dos = $_REQUEST["chartNoteDOS"];//Y-m-d
			//		}

					$insUpdtQry='INSERT INTO ';
					$whereQry='';
					$newInserted='1';

					$trialInsert=0;
					if(in_array('Current Trial', $clws_type_arr) && $_REQUEST['clws_trial_number']!=$_REQUEST['trial_no_old']){
						$trialInsert = 1;
					}
					if($_REQUEST['clws_id']!='' && $_REQUEST['clws_id'] > 0 && $trialInsert==0 && sizeof($arrSaveDiff)<=0)
					{
						$insUpdtQry = 'UPDATE ';
						$newInserted='0';
						$delOldVals = 1;
						$addNew =0;
						$whereQry=" WHERE clws_id='".$_REQUEST['clws_id']."'";

						//if(!empty($charges_ids))
						//$charges_id = "charges_id='".$charges_ids."',";
						imw_query("Update contactlensmaster SET clws_type='".addslashes($clws_type)."', cl_comment='".addslashes($_REQUEST['cl_comment'])."' , charges_id='".$charges_ids."',cpt_evaluation_fit_refit='".$_REQUEST['cpt_evaluation_fit_refit']."', prescribed_by='".$_SESSION['authId']."' ".$whereQry);

						$clOrderResult = imw_query("select print_order_id from clprintorder_master where clws_id='".$_REQUEST['clws_id']."'");
						if(imw_num_rows($clOrderResult) > 0){
							imw_query("update clprintorder_master set prescribed_by='".$_SESSION['authId']."' where clws_id='".$_REQUEST['clws_id']."'");
						}

					}else{
						//GET CPT EVALUATION FEE
						$charges_cpt=0;
						if(sizeof($arrCharges_ids)>0){
							$rs=imw_query("Select GROUP_CONCAT(cpt_fee_id) as charges_cpt FROM cl_charges WHERE cl_charge_id IN(".$charges_ids.") AND cpt_fee_id>0");
							$res=imw_fetch_array($rs);
							$charges_cpt= trim($res['charges_cpt']);
							if(!$charges_cpt) $charges_cpt = 0;
							//GET ID OF DEFAULT FEE COLUMN
							$rs=imw_query("Select fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)='default'");
							$res=imw_fetch_array($rs);
							$defaultFeeId = $res['fee_table_column_id'];
							if(!empty($charges_cpt)){
								$rs=imw_query("Select SUM(cpt_fee) as cpt_charges FROM cpt_fee_table WHERE cpt_fee_id IN(".$charges_cpt.") AND fee_table_column_id='".$defaultFeeId."'");
								$res=imw_fetch_array($rs);
								$charges_cpt= $res['cpt_charges'];
							}
						}


						$trialno=(in_array('Current Trial', $clws_type_arr))? $_REQUEST["clws_trial_number"] : '0';
						//$trialno=(in_array('Current Trial', $clws_type_arr))? $_REQUEST["clws_trial_number"] : 'NoInsert';

						if($trialno=='0'){
								$arrTempChk = array("SclCylinder","Sclaxis","SclBcurve","SclDiameter","Sclsphere",
								"SclAdd","SclDva","SclNva","SclType","SclColor","RgpPower","RgpCylinder","RgpAxis",
								"RgpBC","RgpDiameter","RgpOZ","RgpCT","RgpColor","RgpLatitude",
								"RgpAdd",
								"RgpDva",
								"RgpNva",
								"RgpType",
								"RgpWarranty",
								"RgpCustomPower",
								"RgpCustomCylinder",
								"RgpCustomAxis",
								"RgpCustomBC",
								"RgpCustom2degree",
								"RgpCustom3degree",
								"RgpCustomPCW",
								"RgpCustomDiameter",
								"RgpCustomOZ",
								"RgpCustomCT",
								"RgpCustomColor",
								"RgpCustomBlend",
								"RgpCustomEdge",
								"RgpCustomLatitude",
								"RgpCustomAdd",
								"RgpCustomDva",
								"RgpCustomNva",
								"RgpCustomType",
								"RgpCustomWarranty");

							//echo $_REQUEST["elem_statusElements"];
							$flg_chkk=0;
							$clSize = ($_REQUEST['txtTotOD']>$_REQUEST['txtTotOS'])? $_REQUEST['txtTotOD']: $_REQUEST['txtTotOS'];

							for($i=1;$i<=$clSize;$i++){
								foreach($arrTempChk as $key=>$val22){

									if(strpos($_REQUEST["elem_statusElements"],$val22."OD".$i."=1")!==false
									|| strpos($_REQUEST["elem_statusElements"],$val22."OS".$i."=1")!==false
									|| strpos($_REQUEST["elem_statusElements"], 'clws_type=1')!==false){
										$flg_chkk=1;
										break;
									}
								}
							}

							//IF COMMENT TOUCHED
							if(strpos($_REQUEST["elem_statusElements"], 'cl_comment=1')!==false){
								$flg_chkk=1;
							}

							if($flg_chkk==0){
								$trialno="NoInsert";
							}
						}

						$ins_sql = $insUpdtQry." `contactlensmaster` set ";
						$ins_sql.="	patient_id='".addslashes($_REQUEST["patient_id"])."',
									provider_id='".addslashes($_REQUEST["provider_id"])."',
									dos='".$dos."',";
						$ins_sql.="
									clws_type='".addslashes($clws_type)."',
									clws_trial_number='".$trialno."',
									CLHXDOS='".$dos."',
									clGrp='OU',
									clws_savedatetime='".wv_dt('now')."',

									cpt_evaluation_fit_refit='".$_REQUEST['cpt_evaluation_fit_refit']."',
									form_id='".$clForm_id."',";  //cpt_evaluation_fit_refit='".$charges_cpt."',
						if(!empty($charges_ids))
						$ins_sql.= "charges_id='".$charges_ids."',";

						$ins_sql.= "cl_comment='".addslashes($_REQUEST['cl_comment'])."'";
						//echo "$ins_sql<br><br>";

						if($trialno!="NoInsert"){ //Checking if inserting with default values
							$insMaster = imw_query($ins_sql) or die(imw_error());
							$clws_id_eval=$clwsID = imw_insert_id();

							if(trim($insUpdtQry) == "INSERT INTO"){
								imw_query("update contactlensmaster set prescribed_by='".$_SESSION['authId']."' where clws_id='".$clwsID."'");

								if(!empty($clwsID)){
									$_REQUEST['hdnCommentId']=0;

									} //Insert Comments in new record when new mastersheet is created.

							}
						} //End
					}
					if($_REQUEST['clws_id']!='' && $delOldVals ==1) {
						$clwsID = $_REQUEST['clws_id'];
						$whereQry="WHERE clws_id='".$clwsID."'";
					}
					$clGrp = ($_POST['clGrp']!='') ? $_POST['clGrp'] : 'OU';
					if($clwsID>0){
						// GET PREVIOUS WORKSHEET DETAIL IDS FOR DELETION
						if(!empty($clwsID)){
						$rs=imw_query("Select id,clType FROM contactlensworksheet_det WHERE clws_id='".$clwsID."'");
							while($res=imw_fetch_array($rs)){
								$arrOldIds['id'][$res['id']] = $res['id'];
								$arrOldIds['clType'][$res['id']] = $res['clType'];
							}
						}

						$inserWDetails=0;
						// ADD OD VALUES
						if($clGrp=='OD' || $clGrp=='OU') {
							$clSize = $_REQUEST['txtTotOD'];
							for($i=1; $i <= $clSize; $i++)
							{
								$_REQUEST['clTypeOD'.$i]."\n";
								$detId='0';
								$qryInitial='Update';
								$clType = $_REQUEST['clTypeOD'.$i];
								if($clType!=''){
									$detId = ($_REQUEST['clDetIdOD'.$i]>0)? $_REQUEST['clDetIdOD'.$i] : $detId;
									$oldCLType=$arrOldIds['clType'][$detId];
									$oldCLType = ($oldCLType==$clType)? '' : $oldCLType;
									$qryWhere=" WHERE id='".$detId."'";
									if($detId!=$arrOldIds['id'][$detId]){
										$qryInitial='Insert INTO';
										$oldCLType='';
										$qryWhere='';
									}elseif($detId==$arrOldIds['id'][$detId]){
										unset($arrOldIds['id'][$detId]);
									}
									$inssql = $qryInitial." contactlensworksheet_det set clws_id ='".$clwsID."', clEye ='OD', clType='".$clType."', ";
									$inssql.=  $this->insertCLValues($clType, 'od', $i, $oldCLType);

									//SCL Drwaings
									if($i==1){
										$inssql.=",
										idoc_drawing_id='".addslashes($_REQUEST["idoc_drawing_id_od"])."',
										corneaSCL_od_desc='".addslashes($_REQUEST["description_A_od"])."',
										corneaSCL_os_desc='".addslashes($_REQUEST["description_B_od"])."'
										";
									}
									$inssql.= $qryWhere;
									//exit;
									$rs=imw_query($inssql)or die($inssql.imw_error());
									if($rs){ $inserWDetails=1; }
								}
							}
						}
						// ADD OS VALUES
						if($clGrp=='OS' || $clGrp=='OU'){
							$clSize = $_REQUEST['txtTotOS'];
							for($i=1; $i <= $clSize; $i++){
								$detId='0';
								$qryInitial='Update';
								$clType = $_REQUEST['clTypeOS'.$i];
								if($clType!=''){
									$detId = ($_REQUEST['clDetIdOS'.$i]>0)? $_REQUEST['clDetIdOS'.$i] : $detId;
									$oldCLType=$arrOldIds['clType'][$detId];
									$oldCLType = ($oldCLType==$clType)? '' : $oldCLType;
									$qryWhere=" WHERE id='".$detId."'";
									if($detId!=$arrOldIds['id'][$detId]){
										$qryInitial='Insert INTO';
										$oldCLType='';
										$qryWhere='';
									}elseif($detId==$arrOldIds['id'][$detId]){
										unset($arrOldIds['id'][$detId]);
									}
									$inssql = $qryInitial." contactlensworksheet_det set clws_id ='".$clwsID."', clEye ='OS', clType='".$clType."', ";
									$inssql.= $this->insertCLValues($clType, 'os', $i, $oldCLType);
									//SCL Drwaings
									if($i==1){
										$inssql.=",
										idoc_drawing_id='".addslashes($_REQUEST["idoc_drawing_id_os"])."',
										corneaSCL_od_desc='".addslashes($_REQUEST["description_A_os"])."',
										corneaSCL_os_desc='".addslashes($_REQUEST["description_B_os"])."'
										";
									}
									$inssql.= $qryWhere;
									$rs=imw_query($inssql)or die($inssql.imw_error());
									if($rs){ $inserWDetails=1; }
								}
							}
						}

						// DELETE OLD VALUES from Worksheet
						if($delOldVals == 1 && $inserWDetails==1 && sizeof($arrOldIds['id'])>0)
						{
							$strOldIds=implode(',', $arrOldIds['id']);
							$delQry = "Delete from contactlensworksheet_det ".$whereQry." AND id IN(".$strOldIds.")";
							imw_query($delQry) or die($delQry.imw_error());
						}

						// INSERT BLANK EVALUATION VALUES SO THAT CAN UPDATE FROM WORKSHEET
						if($addNew==1){
							$evalQry="Insert into contactlens_evaluations SET clws_id='".$clwsID."'";
							$evalRs = imw_query($evalQry);
						}

						/************ Save contact lens evaluations ************/
						$clEvaluationQuery = "select id from contactlens_evaluations where clws_id='".$clwsID."'";
						$clEvaluationResult = imw_query($clEvaluationQuery) or die("Error while updating contact lens evaluations: ".imw_error());
						if(imw_num_rows($clEvaluationResult) > 0){
							$updateQuery = "update contactlens_evaluations set
							CLSLCEvaluationComfortOD='".sqlEscStr($_REQUEST['CLSLCEvaluationComfortOD'])."',
							CLSLCEvaluationMovementOD='".sqlEscStr($_REQUEST['CLSLCEvaluationMovementOD'])."',
							EvaluationRotationOD='".sqlEscStr($_REQUEST['EvaluationRotationOD'])."',
							CLSLCEvaluationCondtionOD='".sqlEscStr($_REQUEST['CLSLCEvaluationCondtionOD'])."',
							CLSLCEvaluationPositionOD='".sqlEscStr($_REQUEST['CLSLCEvaluationPositionOD'])."',
							CLSLCEvaluationPositionOtherOD='".sqlEscStr($_REQUEST['CLSLCEvaluationPositionOtherOD'])."',
							CLSLCEvaluationComfortOS='".sqlEscStr($_REQUEST['CLSLCEvaluationComfortOS'])."',
							CLSLCEvaluationMovementOS='".sqlEscStr($_REQUEST['CLSLCEvaluationMovementOS'])."',
							EvaluationRotationOS='".sqlEscStr($_REQUEST['EvaluationRotationOS'])."',
							CLSLCEvaluationCondtionOS='".sqlEscStr($_REQUEST['CLSLCEvaluationCondtionOS'])."',
							CLSLCEvaluationPositionOS='".sqlEscStr($_REQUEST['CLSLCEvaluationPositionOS'])."',
							CLSLCEvaluationPositionOtherOS='".sqlEscStr($_REQUEST['CLSLCEvaluationPositionOtherOS'])."',
							CLRGPEvaluationComfortOD='".sqlEscStr($_REQUEST['CLRGPEvaluationComfortOD'])."',
							CLRGPEvaluationMovementOD='".sqlEscStr($_REQUEST['CLRGPEvaluationMovementOD'])."',
							CLRGPEvaluationPosBeforeOD='".sqlEscStr($_REQUEST['CLRGPEvaluationPosBeforeOD'])."',
							CLRGPEvaluationPosBeforeOtherOD='".sqlEscStr($_REQUEST['CLRGPEvaluationPosBeforeOtherOD'])."',
							CLRGPEvaluationPosAfterOD='".sqlEscStr($_REQUEST['CLRGPEvaluationPosAfterOD'])."',
							CLRGPEvaluationPosAfterOtherOD='".sqlEscStr($_REQUEST['CLRGPEvaluationPosAfterOtherOD'])."',
							CLRGPEvaluationFluoresceinPatternOD='".sqlEscStr($_REQUEST['CLRGPEvaluationFluoresceinPatternOD'])."',
							CLRGPEvaluationInvertedOD='".sqlEscStr($_REQUEST['CLRGPEvaluationInvertedOD'])."',
							CLRGPEvaluationComfortOS='".sqlEscStr($_REQUEST['CLRGPEvaluationComfortOS'])."',
							CLRGPEvaluationMovementOS='".sqlEscStr($_REQUEST['CLRGPEvaluationMovementOS'])."',
							CLRGPEvaluationPosBeforeOS='".sqlEscStr($_REQUEST['CLRGPEvaluationPosBeforeOS'])."',
							CLRGPEvaluationPosBeforeOtherOS='".sqlEscStr($_REQUEST['CLRGPEvaluationPosBeforeOtherOS'])."',
							CLRGPEvaluationPosAfterOS='".sqlEscStr($_REQUEST['CLRGPEvaluationPosAfterOS'])."',
							CLRGPEvaluationPosAfterOtherOS='".sqlEscStr($_REQUEST['CLRGPEvaluationPosAfterOtherOS'])."',
							CLRGPEvaluationFluoresceinPatternOS='".sqlEscStr($_REQUEST['CLRGPEvaluationFluoresceinPatternOS'])."',
							CLRGPEvaluationInvertedOS='".sqlEscStr($_REQUEST['CLRGPEvaluationInvertedOS'])."'
							where clws_id='".$clwsID."'";
							imw_query($updateQuery) or die("Failed while updating cl evaluations: ".imw_error());


							// Update usage_val, allaround, relenishment, wear scheduler and disinfecting
							$addQryPart='';
							if($_REQUEST['copyFromId'] != "" && $_REQUEST['copyFromId'] > 0){
								$qry="Select usage_val, allaround FROM contactlensmaster WHERE clws_id='".$_REQUEST['copyFromId']."'";
								$rs=imw_query($qry);
								$res=imw_fetch_assoc($rs);
								$addQryPart=",usage_val='".$res['usage_val']."',
								allaround='".$res['allaround']."'";
							}

							imw_query("update contactlensmaster set replenishment='".sqlEscStr($_REQUEST['replenishment1'])."',
							wear_scheduler='".sqlEscStr($_REQUEST['wear_scheduler1'])."',
							disinfecting='".sqlEscStr($_REQUEST['disinfecting1'])."'
							".$addQryPart."
							where clws_id='".$clwsID."'") or die("Failed to update contactlensmaster for replenishment, wear scheduler and disinfecting: ".imw_error());

							$comment = trim(addslashes($_REQUEST['cl_comment']));
							if(is_numeric($_REQUEST['hdnCommentId']) && trim($_REQUEST['hdnCommentId']) > 0){
								imw_query("update cl_comments set comment='".$comment."' where id='".$_REQUEST['hdnCommentId']."' and cl_sheet_id='".$clwsID."'");
							}else{
								imw_query("insert into cl_comments (cl_sheet_id, comment, delete_status) values('".$clwsID."', '".trim(addslashes($_REQUEST['cl_comment']))."', '0')") or die("Error while adding comment: ".imw_error());
							}
						}


						if(($_REQUEST['copyFromId'] != "" && $_REQUEST['copyFromId'] > 0 && $_REQUEST['copyFromId'] != $_REQUEST['clws_id']) || ($newInserted==1 && $_REQUEST["clws_id"]>0)){
							$this->copyFromEvaluations($addNew, $clws_id_eval,$newInserted);
						}
					}
				}
			}
		}
	}

function insertCLValues($clType, $clEye, $i, $oldCLType=''){
		if($clType == 'scl' || $clType == 'prosthesis' || $clType == 'no-cl') {
			if($clEye == 'od'){
				$temp = addslashes($_REQUEST["sclManufactOD"])."-".addslashes($_REQUEST["sclStyleOD"]);
				if($temp == "-"){$temp = '';}

				if(($_REQUEST["SclTypeOD".$i."ID"]=='' || $_REQUEST["SclTypeOD".$i."ID"]==0) && strlen(trim($_REQUEST["SclTypeOD".$i]))>0)
				{
					$_REQUEST["SclTypeOD".$i."ID"]=getManufID($_REQUEST["SclTypeOD".$i]);
				}
				$inssqlVal="
					SclsphereOD='".addslashes($_REQUEST["SclsphereOD".$i])."',
					SclCylinderOD='".addslashes($_REQUEST["SclCylinderOD".$i])."',
					SclaxisOD='".addslashes($_REQUEST["SclaxisOD".$i])."',
					SclBcurveOD='".addslashes($_REQUEST["SclBcurveOD".$i])."',
					SclDiameterOD='".addslashes($_REQUEST["SclDiameterOD".$i])."',
					SclAddOD='".addslashes($_REQUEST["SclAddOD".$i])."',
					SclDvaOD='".addslashes($_REQUEST["SclDvaOD".$i])."',
					SclNvaOD='".addslashes($_REQUEST["SclNvaOD".$i])."',
					SclTypeOD='".addslashes($_REQUEST["SclTypeOD".$i])."',
                    SclColorOD='".addslashes($_REQUEST["SclColorOD".$i])."',
					SclTypeOD_ID='".addslashes($_REQUEST["SclTypeOD".$i."ID"])."'";

					//FOR CLEAR OLD FIELDS
					if($oldCLType!=''){
						//$inssqlVal.=",".clearPrescription('od',$oldCLType);
					}
					$temp = '';
			}else if($clEye == 'os') {
				$temp = addslashes($_REQUEST["sclManufactOS"])."-".addslashes($_REQUEST["sclStyleOS"]);
				if($temp == "-"){$temp = '';}
				if(($_REQUEST["SclTypeOS".$i."ID"]=='' || $_REQUEST["SclTypeOS".$i."ID"]==0) && strlen(trim($_REQUEST["SclTypeOS".$i]))>0)
				{
					$_REQUEST["SclTypeOS".$i."ID"]=getManufID($_REQUEST["SclTypeOS".$i]);
				}
				$inssqlVal="
					SclsphereOS='".addslashes($_REQUEST["SclsphereOS".$i])."',
					SclCylinderOS='".addslashes($_REQUEST["SclCylinderOS".$i])."',
					SclaxisOS='".addslashes($_REQUEST["SclaxisOS".$i])."',
					SclBcurveOS='".addslashes($_REQUEST["SclBcurveOS".$i])."',
					SclDiameterOS='".addslashes($_REQUEST["SclDiameterOS".$i])."',
					SclAddOS='".addslashes($_REQUEST["SclAddOS".$i])."',
					SclDvaOS='".addslashes($_REQUEST["SclDvaOS".$i])."',
					SclNvaOS='".addslashes($_REQUEST["SclNvaOS".$i])."',
					SclTypeOS='".addslashes($_REQUEST["SclTypeOS".$i])."',
                    SclColorOS='".addslashes($_REQUEST["SclColorOS".$i])."',
					SclTypeOS_ID='".addslashes($_REQUEST["SclTypeOS".$i."ID"])."'";

					if($oldCLType!=''){
						//$inssqlVal.=",".clearPrescription('os',$oldCLType);
					}
				$temp = '';
			}
		}
		else if($clType =='rgp' || $clType =='rgp_soft' || $clType =='rgp_hard')
		{
			if($clEye == 'od')
			{
				$temp = addslashes($_REQUEST["rgpManufactOS"])."-".addslashes($_REQUEST["rgpStyleOS"]);
				if($temp == "-"){$temp = '';}
				if(($_REQUEST["RgpTypeOD".$i."ID"]=='' || $_REQUEST["RgpTypeOD".$i."ID"]==0) && strlen(trim($_REQUEST["RgpTypeOD".$i]))>0)
				{
					$_REQUEST["RgpTypeOD".$i."ID"]=getManufID($_REQUEST["RgpTypeOD".$i]);
				}
				$inssqlVal="
					RgpPowerOD='".addslashes($_REQUEST["RgpPowerOD".$i])."',
					RgpCylinderOD='".addslashes($_REQUEST["RgpCylinderOD".$i])."',
					RgpAxisOD='".addslashes($_REQUEST["RgpAxisOD".$i])."',
					RgpBCOD='".addslashes($_REQUEST["RgpBCOD".$i])."',
					RgpDiameterOD='".addslashes($_REQUEST["RgpDiameterOD".$i])."',
					RgpOZOD='".addslashes($_REQUEST["RgpOZOD".$i])."',
					RgpCTOD='".addslashes($_REQUEST["RgpCTOD".$i])."',
					RgpColorOD='".addslashes($_REQUEST["RgpColorOD".$i])."',
					RgpLatitudeOD='".addslashes($_REQUEST["RgpLatitudeOD".$i])."',
					RgpAddOD='".addslashes($_REQUEST["RgpAddOD".$i])."',
					RgpDvaOD='".addslashes($_REQUEST["RgpDvaOD".$i])."',
					RgpNvaOD='".addslashes($_REQUEST["RgpNvaOD".$i])."',
					RgpTypeOD='".addslashes($_REQUEST["RgpTypeOD".$i])."',
					RgpTypeOD_ID='".addslashes($_REQUEST["RgpTypeOD".$i."ID"])."',
					RgpWarrantyOD='".addslashes($_REQUEST["RgpWarrantyOD".$i])."'";

					//FOR CLEAR OLD FIELDS
					if($oldCLType!=''){
					//	$inssqlVal.=",".clearPrescription('od',$oldCLType);
					}
					$temp = '';
			}else if($clEye == 'os'){
				$temp = addslashes($_REQUEST["rgpManufactOS"])."-".addslashes($_REQUEST["rgpStyleOS"]);
				if($temp == "-"){$temp = '';}
				if(($_REQUEST["RgpTypeOS".$i."ID"]=='' || $_REQUEST["RgpTypeOS".$i."ID"]==0) && strlen(trim($_REQUEST["RgpTypeOS".$i]))>0)
				{
					$_REQUEST["RgpTypeOS".$i."ID"]=getManufID($_REQUEST["RgpTypeOS".$i]);
				}
				$inssqlVal="
					RgpPowerOS='".addslashes($_REQUEST["RgpPowerOS".$i])."',
					RgpCylinderOS='".addslashes($_REQUEST["RgpCylinderOS".$i])."',
					RgpAxisOS='".addslashes($_REQUEST["RgpAxisOS".$i])."',
					RgpBCOS='".addslashes($_REQUEST["RgpBCOS".$i])."',
					RgpDiameterOS='".addslashes($_REQUEST["RgpDiameterOS".$i])."',
					RgpOZOS='".addslashes($_REQUEST["RgpOZOS".$i])."',
					RgpCTOS='".addslashes($_REQUEST["RgpCTOS".$i])."',
					RgpColorOS='".addslashes($_REQUEST["RgpColorOS".$i])."',
					RgpLatitudeOS='".addslashes($_REQUEST["RgpLatitudeOS".$i])."',
					RgpAddOS='".addslashes($_REQUEST["RgpAddOS".$i])."',
					RgpDvaOS='".addslashes($_REQUEST["RgpDvaOS".$i])."',
					RgpNvaOS='".addslashes($_REQUEST["RgpNvaOS".$i])."',
					RgpTypeOS='".addslashes($_REQUEST["RgpTypeOS".$i])."',
					RgpTypeOS_ID='".addslashes($_REQUEST["RgpTypeOS".$i."ID"])."',
					RgpWarrantyOS='".addslashes($_REQUEST["RgpWarrantyOS".$i])."'
					";
					//FOR CLEAR OLD FIELDS
					if($oldCLType!=''){
						//$inssqlVal.=",".clearPrescription('os',$oldCLType);
					}
					$temp = '';
			}
		}
		else if($clType == 'cust_rgp')
		{
			if($clEye == 'od')
			{
				$temp = addslashes($_REQUEST["RgpCustomManufTypeOD"])."-".addslashes($_REQUEST["RgpCustomStyleTypeOD"]);
				if($temp == "-"){$temp = '';}
				if(($_REQUEST["RgpCustomTypeOD".$i."ID"]=='' || $_REQUEST["RgpCustomTypeOD".$i."ID"]==0) && strlen(trim($_REQUEST["RgpCustomTypeOD".$i]))>0)
				{
					$_REQUEST["RgpCustomTypeOD".$i."ID"]=getManufID($_REQUEST["RgpCustomTypeOD".$i]);
				}
				$inssqlVal="
					RgpCustomPowerOD='".addslashes($_REQUEST["RgpCustomPowerOD".$i])."',
					RgpCustomCylinderOD='".addslashes($_REQUEST["RgpCustomCylinderOD".$i])."',
					RgpCustomAxisOD='".addslashes($_REQUEST["RgpCustomAxisOD".$i])."',
					RgpCustomBCOD='".addslashes($_REQUEST["RgpCustomBCOD".$i])."',
					RgpCustom2degreeOD='".addslashes($_REQUEST["RgpCustom2degreeOD".$i])."',
					RgpCustom3degreeOD='".addslashes($_REQUEST["RgpCustom3degreeOD".$i])."',
					RgpCustomPCWOD='".addslashes($_REQUEST["RgpCustomPCWOD".$i])."',
					RgpCustomDiameterOD='".addslashes($_REQUEST["RgpCustomDiameterOD".$i])."',
					RgpCustomOZOD='".addslashes($_REQUEST["RgpCustomOZOD".$i])."',
					RgpCustomCTOD='".addslashes($_REQUEST["RgpCustomCTOD".$i])."',
					RgpCustomColorOD='".addslashes($_REQUEST["RgpCustomColorOD".$i])."',
					RgpCustomBlendOD='".addslashes($_REQUEST["RgpCustomBlendOD".$i])."',

					RgpCustomEdgeOD='".addslashes($_REQUEST["RgpCustomEdgeOD".$i])."',
					RgpCustomLatitudeOD='".addslashes($_REQUEST["RgpCustomLatitudeOD".$i])."',
					RgpCustomAddOD='".addslashes($_REQUEST["RgpCustomAddOD".$i])."',
					RgpCustomDvaOD='".addslashes($_REQUEST["RgpCustomDvaOD".$i])."',
					RgpCustomNvaOD='".addslashes($_REQUEST["RgpCustomNvaOD".$i])."',
					RgpCustomTypeOD='".addslashes($_REQUEST["RgpCustomTypeOD".$i])."',
					RgpCustomTypeOD_ID='".addslashes($_REQUEST["RgpCustomTypeOD".$i."ID"])."',
					RgpCustomWarrantyOD='".addslashes($_REQUEST["RgpCustomWarrantyOD".$i])."'
					";
					//FOR CLEAR OLD FIELDS
					if($oldCLType!=''){
					//	$inssqlVal.=",".clearPrescription('od',$oldCLType);
					}
					$temp = '';
			}else if($clEye == 'os'){
					$temp = addslashes($_REQUEST["RgpCustomManufTypeOS"])."-".addslashes($_REQUEST["RgpCustomStyleTypeOD"]);
					if($temp == "-"){$temp = '';}
					if(($_REQUEST["RgpCustomTypeOS".$i."ID"]=='' || $_REQUEST["RgpCustomTypeOS".$i."ID"]==0) && strlen(trim($_REQUEST["RgpCustomTypeOS".$i]))>0)
					{
						$_REQUEST["RgpCustomTypeOS".$i."ID"]=getManufID($_REQUEST["RgpCustomTypeOS".$i]);
					}
					$inssqlVal="
					RgpCustomPowerOS='".addslashes($_REQUEST["RgpCustomPowerOS".$i])."',
					RgpCustomCylinderOS='".addslashes($_REQUEST["RgpCustomCylinderOS".$i])."',
					RgpCustomAxisOS='".addslashes($_REQUEST["RgpCustomAxisOS".$i])."',
					RgpCustomBCOS='".addslashes($_REQUEST["RgpCustomBCOS".$i])."',
					RgpCustom2degreeOS='".addslashes($_REQUEST["RgpCustom2degreeOS".$i])."',
					RgpCustom3degreeOS='".addslashes($_REQUEST["RgpCustom3degreeOS".$i])."',
					RgpCustomPCWOS='".addslashes($_REQUEST["RgpCustomPCWOS".$i])."',
					RgpCustomDiameterOS='".addslashes($_REQUEST["RgpCustomDiameterOS".$i])."',
					RgpCustomOZOS='".addslashes($_REQUEST["RgpCustomOZOS".$i])."',
					RgpCustomCTOS='".addslashes($_REQUEST["RgpCustomCTOS".$i])."',
					RgpCustomColorOS='".addslashes($_REQUEST["RgpCustomColorOS".$i])."',
					RgpCustomBlendOS='".addslashes($_REQUEST["RgpCustomBlendOS".$i])."',

					RgpCustomEdgeOS='".addslashes($_REQUEST["RgpCustomEdgeOS".$i])."',
					RgpCustomLatitudeOS='".addslashes($_REQUEST["RgpCustomLatitudeOS".$i])."',
					RgpCustomAddOS='".addslashes($_REQUEST["RgpCustomAddOS".$i])."',
					RgpCustomDvaOS='".addslashes($_REQUEST["RgpCustomDvaOS".$i])."',
					RgpCustomNvaOS='".addslashes($_REQUEST["RgpCustomNvaOS".$i])."',
					RgpCustomTypeOS='".addslashes($_REQUEST["RgpCustomTypeOS".$i])."',
					RgpCustomTypeOS_ID='".addslashes($_REQUEST["RgpCustomTypeOS".$i."ID"])."',
					RgpCustomWarrantyOS='".addslashes($_REQUEST["RgpCustomWarrantyOS".$i])."'
					";
					//FOR CLEAR OLD FIELDS
					if($oldCLType!=''){
					//	$inssqlVal.=",".clearPrescription('os',$oldCLType);
					}
					$temp = '';
			}
		}
		return $inssqlVal;
	}

function saveStereopsis(){
	$patientid = $this->pid;
	$elem_formId = $this->fid;
	//
	$elem_stereo_SecondsArc=$_POST["elem_stereo_SecondsArc"];
	$elem_utElems_cur = $_POST["elem_utElems_cur"];
	//
	if(strpos($elem_utElems_cur,"elem_stereo_SecondsArc")!==false){
		$ut_elem_cur="elem_stereo_SecondsArc";
	}else{
		$ut_elem_cur="";
	}

	$cQry = "select * FROM chart_steropsis WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0' ";
	$row = sqlQuery($cQry);
	if($row == false){
		$elem_editMode_st = "0";
	}else{
		$elem_editMode_st = "1";
		$ut_elems=$row["ut_elem"];

		//Modifying Notes----------------
		//$modi_note=getModiNotes($row["seconds_of_arc"],0,$elem_stereo_SecondsArc,$elem_wnl,$row["uid"]);
		//Modifying Notes----------------
	}

	//
	$elem_ut_elem = $this->getUTElemString($ut_elems,$ut_elem_cur);

	if($elem_editMode_st == "0"){
		if(!empty($elem_stereo_SecondsArc)){
			$sql = "INSERT INTO chart_steropsis (id, form_id,patient_id, seconds_of_arc, exam_date, uid,ut_elem)
					VALUES(NULL, '".$elem_formId."','".$patientid."', '".$elem_stereo_SecondsArc."', '".wv_dt('now')."', '".$_SESSION["authId"]."', '".$elem_ut_elem."') ";
			$insertId_st = sqlInsert($sql);
		}
	}else if($elem_editMode_st == "1"){
		$sql = "UPDATE chart_steropsis SET
				seconds_of_arc='".$elem_stereo_SecondsArc."',
				exam_date='".wv_dt('now')."',
				uid='".$_SESSION["authId"]."',
				ut_elem='".$elem_ut_elem."'
				WHERE form_id = '".$elem_formId."' AND patient_id='".$patientid."' AND purged='0' ";
		$row=sqlQuery($sql);
	}
}

//Color Vision Test
function saveColorVisionTest($arrColorVis,$elem_utElems_cur){

	$patientid = $this->pid;
	$elem_formId = $this->fid;

	$elem_utElems_cur_icp="";
	if(strpos($elem_utElems_cur,"elem_color_sign_od")!==false){$elem_utElems_cur_icp.="elem_color_sign_od,";}
	if(strpos($elem_utElems_cur,"elem_color_od_1")!==false){$elem_utElems_cur_icp.="elem_color_od_1,";}
	if(strpos($elem_utElems_cur,"elem_color_od_2")!==false){$elem_utElems_cur_icp.="elem_color_od_2,";}
	if(strpos($elem_utElems_cur,"elem_color_sign_os")!==false){$elem_utElems_cur_icp.="elem_color_sign_os,";}
	if(strpos($elem_utElems_cur,"elem_color_os_1")!==false){$elem_utElems_cur_icp.="elem_color_os_1,";}
	if(strpos($elem_utElems_cur,"elem_color_os_2")!==false){$elem_utElems_cur_icp.="elem_color_os_2,";}

	if(strpos($elem_utElems_cur,"elem_color_sign_ou")!==false){$elem_utElems_cur_icp.="elem_color_sign_ou,";}
	if(strpos($elem_utElems_cur,"elem_color_ou_1")!==false){$elem_utElems_cur_icp.="elem_color_ou_1,";}
	if(strpos($elem_utElems_cur,"elem_color_ou_2")!==false){$elem_utElems_cur_icp.="elem_color_ou_2,";}

	if(strpos($elem_utElems_cur,"elem_comm_colorVis")!==false){$elem_utElems_cur_icp.="elem_comm_colorVis,";}

	$cQry = "select * FROM chart_icp_color  WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0' ";
	$row = sqlQuery($cQry);
	if($row == false){
		$elem_editMode = "0";
	}else{
		$elem_editMode = "1";
		$ut_elems=$row["ut_elem"];

	}

	$elem_ut_elem = $this->getUTElemString($ut_elems,$elem_utElems_cur_icp);

	if($elem_editMode == "0"){

		if(!empty($arrColorVis["elem_color_od_1"])||!empty($arrColorVis["elem_color_od_2"])||
			!empty($arrColorVis["elem_color_os_1"]) || !empty($arrColorVis["elem_color_os_1"]) ||
			!empty($arrColorVis["elem_color_ou_1"]) || !empty($arrColorVis["elem_color_ou_1"]) ||
			!empty($arrColorVis["elem_comm_colorVis"])
			){

		$sql = "INSERT INTO chart_icp_color (id, patient_id, form_id, exam_date, uid,
						control_od, cval1_od, cval2_od, control_os, cval1_os, cval2_os,  icp_desc, ut_elem,
						control_ou, cval1_ou, cval2_ou ) VALUES
						(NULL, '".$patientid."', '".$elem_formId."', '".wv_dt('now')."', '".$_SESSION["authId"]."',
							'".$arrColorVis["elem_color_sign_od"]."',
							'".sqlEscStr($arrColorVis["elem_color_od_1"])."',
							'".sqlEscStr($arrColorVis["elem_color_od_2"])."', '".$arrColorVis["elem_color_sign_os"]."',
							'".sqlEscStr($arrColorVis["elem_color_os_1"])."', '".sqlEscStr($arrColorVis["elem_color_os_2"])."',
							'".sqlEscStr($arrColorVis["elem_comm_colorVis"])."', '".$elem_ut_elem."',
							'".$arrColorVis["elem_color_sign_ou"]."',
							'".sqlEscStr($arrColorVis["elem_color_ou_1"])."', '".sqlEscStr($arrColorVis["elem_color_ou_2"])."'
							)";

		$row = sqlInsert($sql);

		}

	}else if($elem_editMode == "1"){

		$sql="UPDATE chart_icp_color SET
				exam_date='".wv_dt('now')."',
				uid='".$_SESSION["authId"]."',
				control_od='".$arrColorVis["elem_color_sign_od"]."',
				cval1_od='".sqlEscStr($arrColorVis["elem_color_od_1"])."',
				cval2_od='".sqlEscStr($arrColorVis["elem_color_od_2"])."',
				control_os='".$arrColorVis["elem_color_sign_os"]."',
				cval1_os='".sqlEscStr($arrColorVis["elem_color_os_1"])."',
				cval2_os='".sqlEscStr($arrColorVis["elem_color_os_2"])."',
				icp_desc='".sqlEscStr($arrColorVis["elem_comm_colorVis"])."',
				ut_elem='".$elem_ut_elem."',
				control_ou='".$arrColorVis["elem_color_sign_ou"]."',
				cval1_ou='".sqlEscStr($arrColorVis["elem_color_ou_1"])."',
				cval2_ou='".sqlEscStr($arrColorVis["elem_color_ou_2"])."'
				WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0'
			";
		sqlQuery($sql);
	}

}

function saveColorVisionTest_st(){
	$elem_utElems_cur = $_POST["elem_utElems_cur"];
	if(strpos($elem_utElems_cur,"elem_color_sign_od")!==false||strpos($elem_utElems_cur,"elem_color_od_1")!==false||
			strpos($elem_utElems_cur,"elem_color_od_2")!==false||strpos($elem_utElems_cur,"elem_color_sign_os")!==false||strpos($elem_utElems_cur,"elem_color_sign_ou")!==false||
			strpos($elem_utElems_cur,"elem_color_os_1")!==false||strpos($elem_utElems_cur,"elem_color_os_2")!==false||
			strpos($elem_utElems_cur,"elem_color_ou_1")!==false||strpos($elem_utElems_cur,"elem_color_ou_2")!==false||
			strpos($elem_utElems_cur,"elem_comm_colorVis")!==false){
				$arrColorVis=array();
				$arrColorVis["elem_color_sign_od"] = $_POST["elem_color_sign_od"];
				$arrColorVis["elem_color_od_1"] = $_POST["elem_color_od_1"];
				$arrColorVis["elem_color_od_2"] = $_POST["elem_color_od_2"];
				$arrColorVis["elem_color_sign_os"] = $_POST["elem_color_sign_os"];
				$arrColorVis["elem_color_os_1"] = $_POST["elem_color_os_1"];
				$arrColorVis["elem_color_os_2"] = $_POST["elem_color_os_2"];
				$arrColorVis["elem_comm_colorVis"] = $_POST["elem_comm_colorVis"];
				$arrColorVis["elem_color_sign_ou"] = $_POST["elem_color_sign_ou"];
				$arrColorVis["elem_color_ou_1"] = $_POST["elem_color_ou_1"];
				$arrColorVis["elem_color_ou_2"] = $_POST["elem_color_ou_2"];

				$this->saveColorVisionTest($arrColorVis,$elem_utElems_cur);
			}

}

function saveW4Dot($arrW4Dot,$elem_utElems_cur){

	$patientid = $this->pid;
	$elem_formId = $this->fid;

	$elem_utElems_cur_w4dot="";
	if(strpos($elem_utElems_cur,"elem_w4dot_distance")!==false){$elem_utElems_cur_w4dot.="elem_w4dot_distance,";}
	if(strpos($elem_utElems_cur,"elem_w4dot_near")!==false){$elem_utElems_cur_w4dot.="elem_w4dot_near,";}
	if(strpos($elem_utElems_cur,"elem_comm_w4Dot")!==false){$elem_utElems_cur_w4dot.="elem_comm_w4Dot,";}

	$cQry = "select * FROM chart_w4dot  WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0' ";
	$row = sqlQuery($cQry);
	if($row == false){
		$elem_editMode = "0";
	}else{
		$elem_editMode = "1";
		$ut_elems=$row["ut_elem"];
	}

	$elem_ut_elem = $this->getUTElemString($ut_elems,$elem_utElems_cur_w4dot);

	if($elem_editMode == "0"){

		if(!empty($arrW4Dot["elem_w4dot_distance"])||!empty($arrW4Dot["elem_w4dot_near"])||
			!empty($arrW4Dot["elem_comm_w4Dot"])
			){

		$sql = "INSERT INTO chart_w4dot (id, patient_id, form_id, exam_date, uid,
						distance, near, desc_w4dot, ut_elem) VALUES
						(NULL, '".$patientid."', '".$elem_formId."', '".wv_dt('now')."', '".$_SESSION["authId"]."',
							'".$arrW4Dot["elem_w4dot_distance"]."',
							'".sqlEscStr($arrW4Dot["elem_w4dot_near"])."',
							'".$arrW4Dot["elem_comm_w4Dot"]."', '".$elem_ut_elem."' )";
		$row = sqlInsert($sql);

		}

	}else if($elem_editMode == "1"){

		$sql="UPDATE chart_w4dot SET
				exam_date='".wv_dt('now')."',
				uid='".$_SESSION["authId"]."',
				distance='".$arrW4Dot["elem_w4dot_distance"]."',
				near='".$arrW4Dot["elem_w4dot_near"]."',
				desc_w4dot='".sqlEscStr($arrW4Dot["elem_comm_w4Dot"])."',
				ut_elem = '".$elem_ut_elem."'
				WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0'
			";
		sqlQuery($sql);
	}

}

function saveW4Dot_st(){
	$elem_utElems_cur = $_POST["elem_utElems_cur"];
	if(strpos($elem_utElems_cur,"elem_w4dot_distance")!==false||strpos($elem_utElems_cur,"elem_w4dot_near")!==false||
		strpos($elem_utElems_cur,"elem_comm_w4Dot")!==false){
		$arrW4Dot=array();
		$arrW4Dot["elem_w4dot_distance"]=$_POST["elem_w4dot_distance"];
		$arrW4Dot["elem_w4dot_near"]=$_POST["elem_w4dot_near"];
		$arrW4Dot["elem_comm_w4Dot"]=sqlEscStr($_POST["elem_comm_w4Dot"]);

		$this->saveW4Dot($arrW4Dot,$elem_utElems_cur);
	}
}

function getAP_prevuser_time($str_as, $arrAPH, $lo_id, $em_dt){ //$arrAnPHxDB, $last_opr_id, $exam_date
	$tmp_modi_by=""; $tmp_time="";

	if(count($arrAPH)>0){
		foreach($arrAPH as $k => $arv){
			$tmp_a = trim($arv["curr"]["Asses"]);
			if($tmp_a == $str_as){
				$tmp_modi_by=$arv["modi_by"];
				$tmp_time=$arv["time"];
			}
		}
	}

	if(empty($tmp_modi_by)){ $tmp_modi_by=$lo_id;  }
	if(empty($tmp_time)){ $tmp_time=$em_dt;  }

	//
	return array($tmp_modi_by, $tmp_time);
}

function saveAssessments(){

	$owv = new WorkView();
	$patient_id=$this->pid;
	$form_id=$this->fid;
	$doctorId = $this->arG["doctorId"];

	$date_of_service = $this->arG["date_of_service"];
	// chart_assessment_plan
	$id = $_POST["elem_assessId"];
	/*
	$plan_resolve_1 = $_POST["elem_resolve1"];
	$assessment_1 = addslashes($_POST["elem_assessment1"]);
	$plan_resolve_2 = $_POST["elem_resolve2"];
	$assessment_2 = addslashes($_POST["elem_assessment2"]);
	$plan_resolve_3 = $_POST["elem_resolve3"];
	$assessment_3 = addslashes($_POST["elem_assessment3"]);
	$plan_resolve_4 = $_POST["elem_resolve4"];
	$assessment_4 = addslashes($_POST["elem_assessment4"]);
	$plan_resolve_5 = $_POST["elem_resolve5"];
	$assessment_5= addslashes($_POST["elem_assessment5"]);
	$no_change_1 = $_POST["no_change_1"];
	$no_change_2 = $_POST["no_change_2"];
	$no_change_3 = $_POST["no_change_3"];
	$no_change_4 = $_POST["no_change_4"];
	$no_change_5 = $_POST["no_change_5"];
	$plan_notes_1 = addslashes($_POST["elem_plan1"]);
	$plan_notes_2 = addslashes($_POST["elem_plan2"]);
	$plan_notes_3 = addslashes($_POST["elem_plan3"]);
	$plan_notes_4 = addslashes($_POST["elem_plan4"]);
	$plan_notes_5 = addslashes($_POST["elem_plan5"]);
	*/
	$follow_up_numeric_value = ""; //$_POST["elem_followUpNumber"];
	$follow_up = ""; //$_POST["elem_followUp"];
	$retina = sqlEscStr($_POST["elem_retina"]);
	$neuro_ophth = sqlEscStr($_POST["elem_neuroOptha"]);
	$doctor_name = sqlEscStr($_POST["elem_doctorName"]);
	$doctorName_id = sqlEscStr($_POST["elem_doctorName_id"]);
	$id_precation = sqlEscStr($_POST["elem_idPrecautions"]);
	$rd_precation = sqlEscStr($_POST["elem_rdPrecautions"]);
	$lid_scrubs_oint = sqlEscStr($_POST["elem_lidScrubs"]);
	$patient_understands = sqlEscStr($_POST["elem_patientUnderstands"]);
	$patient_understands_select = sqlEscStr($_POST["elem_patientUnderstandsSelect"]);
	$plan_notes = trim($_POST["elem_notes"]);
	$elem_pt_goal_str = addslashes(htmlspecialchars($_POST['chart_pt_goals']));
	$elem_pt_health_concern = addslashes(htmlspecialchars($_POST['chart_pt_health_concern']));

	if($plan_notes == "Comments..")$plan_notes = "";

	//if $commentsForPatient not empty, add date and name of uid
	if(!empty($plan_notes)){
		if(trim($_POST["elem_notes"]) != trim($_POST["el_elem_notes_prv"]) ){
			$plan_notes_Dt=date("m-d-y H:i:s");
			$plan_notes_nm = $_SESSION['authUserID'];
		}else{
			$plan_notes_Dt=$_POST["el_elem_notes_Dt_prv"];
			$plan_notes_nm = $_POST["el_elem_notes_nm_prv"];
		}
		if(!empty($plan_notes_Dt) && !empty($plan_notes_nm)){
			$plan_notes = $plan_notes."~||~".$plan_notes_Dt."~||~".$plan_notes_nm;
		}
		$plan_notes = sqlEscStr($plan_notes);
	}
	//--

	//--
	$refer_to = sqlEscStr(trim($_POST["elem_refer_to"]));
	$refer_to_id = sqlEscStr($_POST["elem_refer_to_id"]);
	$refer_code = sqlEscStr(trim($_POST["elem_refer_code"]));
	$transition_reason = sqlEscStr(trim($_POST["elem_transition_reason"]));
	$transition_notes = trim($_POST["elem_transition_notes"]);
	if(!empty($transition_notes)){
		if(trim($_POST["elem_transition_notes"]) != trim($_POST["el_elem_transition_notes_nm_prv"]) ){
			$transition_notes_Dt=date("m-d-y H:i:s");
			$transition_notes_nm = $_SESSION['authUserID'];
		}else{
			$transition_notes_Dt=$_POST["el_elem_notes_Dt_prv"];
			$transition_notes_nm = $_POST["el_elem_transition_notes_nm_prv"];
		}
		if(!empty($transition_notes_Dt) && !empty($transition_notes_nm)){
			$transition_notes = $transition_notes."~||~".$transition_notes_Dt."~||~".$transition_notes_nm;
		}
		$transition_notes = sqlEscStr($transition_notes);
	}
	//--

	//$form_id=$_POST["elem_formId"];
	//$patient_id=$_POST["elem_patientId"];
	$exam_date=wv_formatDate($_POST["elem_examDate"],0,0, "insert");
	$elem_editModeAssess = $_POST["elem_editModeAssess"];
	$followNumber = $_POST["elem_followUpNumber1"];
	$planAll = addslashes($_POST["elem_planAll"]);
	$assessmentAll = addslashes($_POST["elem_assessmentAll"]);
	$continue_meds = $_POST["elem_continue_meds"];
	$followUpVistTypeOther = $_POST["elem_followUpVistTypeOther"];//Array
	$followUpVistType = ""; //(!empty($_POST["elem_followUpVistType"])) ? addslashes($_POST["elem_followUpVistType"]) : $followUpVistTypeOther ;
	$patient_task = sqlEscStr($_POST["patient_task"]);
	$dr_task = sqlEscStr($_POST["dr_task"]);
	$scribedBy = sqlEscStr($_POST["elem_scribedBy"]);
	//Get exml string for Follow up
	$oFu = new Fu($this->pid,$this->fid);
	$strFollowup = $oFu->fu_getXml($_POST["elem_followUpNumber"],$_POST["elem_followUp"],$_POST["elem_followUpVistType"],
								$_POST["elem_followUpVistTypeOther"],$_POST["elem_fuProName"]);
	$commentsForPatient = trim($_POST["commentsForPatient"]);
	$resiHxReviewd = $_POST["elem_resiHxReviewd"];
	$rxhandwritten = $_POST["elem_rxhandwritten"];
	$labhandwritten = $_POST["elem_labhandwritten"];
	$imagehandwritten = $_POST["elem_radhandwritten"];
	$consult_reason = sqlEscStr($_POST["elem_consult_reason"]);
	$surgical_ocular_hx = $_POST["elem_sur_ocu_hx"];

	//if $commentsForPatient not empty, add date and name of uid
	if(!empty($commentsForPatient)){
		if(trim($_POST["commentsForPatient"]) != trim($_POST["el_commentsForPatient_nm_prv"]) ){
			$commentsForPatient_Dt=date("m-d-y H:i:s");
			$commentsForPatient_nm = $_SESSION['authUserID'];
		}else{
			$commentsForPatient_Dt=$_POST["el_commentsForPatient_Dt_prv"];
			$commentsForPatient_nm = $_POST["el_commentsForPatient_nm_prv"];
		}
		if(!empty($commentsForPatient_Dt) && !empty($commentsForPatient_nm)){
			$commentsForPatient = $commentsForPatient."~||~".$commentsForPatient_Dt."~||~".$commentsForPatient_nm;
		}
		$commentsForPatient = sqlEscStr($commentsForPatient);
	}
	//--

	//* // Following values are duplicated in two tables as these are used at many places and difficult to remove at once.
	//$sign_path = sqlEscStr($_POST["elem_sign_path"]);
	//$cosign_path = sqlEscStr($_POST["elem_cosign_path"]);
	//$sign_coords = $_POST["elem_signCoords"];
	if(empty($sign_coords) == false && $sign_coords != '0-0-0:;'){
		$sign_coords_dateTime = date('Y-m-d H:i:s');
	}
	else{
		$sign_coords_dateTime = '';
	}
	//$doctorId = $_POST["elem_physicianId"];
	//elem_signCoordsCosigner
	//$sign_coordsCosigner = $_POST["elem_signCoordsCosigner"];
	if(empty($sign_coordsCosigner) == false && $sign_coordsCosigner != '0-0-0:;'){
		$cosigner_dateTime = date('Y-m-d H:i:s');
	}
	else{
		$cosigner_dateTime = '';
	}
	//$cosigner_id = $_POST["elem_cosignerId"];

	//Double Check for Duplicacy
	if($sign_path==$cosign_path||(!empty($cosigner_dateTime)&&empty($cosign_path))||(!empty($sign_coords_dateTime)&&empty($sign_path))){
		//Check Sign Coords and make signature images
		$oSaveFile = new SaveFile($patient_id);
		//signer
		if(!empty($sign_coords_dateTime)){
			$tmp = $oSaveFile->createSignImages($sign_coords,$form_id,1);
			if(!empty($tmp)){$sign_path=sqlEscStr($tmp);}
		}

		//Cosigner
		if(!empty($cosigner_dateTime)){
			$tmp = $oSaveFile->createSignImages($sign_coordsCosigner,$form_id,2);
			if(!empty($tmp)){$cosign_path=sqlEscStr($tmp);}
		}
	}
	//*/

	//Make Array of assessment and plan elements
	$arrAp_assess=array();
	$arrAp_assess_dxcode=array();
	$arrAp_plan=array();
	$arrAp_resolve=array();
	$arrAp_ne=array();
	$arrAp_eye=array();
	$arrAp_conmed=array();
	$arrAp_prob_list_id=array();
	$arrAp_assess_wodx=array();
	$arrAp_apid=array();
	$arrAp_dx_id=array();

	//get settings for dynamic AP policies
	$oadmn = new Admn();
	$strAPSettings = $oadmn->getAPPolicySettings();
	if(!empty($strAPSettings) && strpos($strAPSettings,"Dynamic")!==false){$flgDynamicAP=1;}else{$flgDynamicAP=0;}

	//Object Pt Problem List
	$oPtProblemList = new PtProblemList($patient_id);
	//Get Visit Problems
	$arrVisitProbList = $oPtProblemList->getVisitProblems($form_id);

	$c=1;

	$elemAp_assess = $_POST["elem_assessment"];
	$elemAp_assess_dxcode = $_POST["elem_assessment_dxcode"];
	$elemAp_plan = $_POST["elem_plan"];
	$elemAp_resolve = (count($_POST["elem_apres"]) > 0) ? $_POST["elem_apres"] : array();
	$elemAp_ne = (count($_POST["elem_apnc"]) > 0) ? $_POST["elem_apnc"] : array();
	$elemAp_Ou = (count($_POST["elem_apOu"]) > 0) ? $_POST["elem_apOu"] : array();
	$elemAp_Od = (count($_POST["elem_apOd"]) > 0) ? $_POST["elem_apOd"] : array();
	$elemAp_Os = (count($_POST["elem_apOs"]) > 0) ? $_POST["elem_apOs"] : array();
	$elem_apConMeds = $_POST["elem_apConMeds"];
	$elem_problist_id_assess = $_POST["elem_problist_id_assess"];
	$el_pt_ap_id = $_POST["el_pt_ap_id"];
	$elem_asmt_dxcode_id = explode("@@", $_POST["elem_asmt_dxcode_id"]);
	$vst_soc = $_POST["el_soc"];
	$soc_desc = sqlEscStr($_POST["el_soc_commnts"]);

	$lenApAsess = count($elemAp_assess);
	$lenApPlan = count($elemAp_plan);
	$lenAp = ($lenApAsess > $lenApPlan) ? $lenApAsess : $lenApPlan;

	$cQry = "select * FROM chart_assessment_plans WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
	$row = sqlQuery($cQry);
	$elem_editModeAssess = ($row == false) ? "0" : "1";
		$arrAnPHxDB = array();
		if($row['modi_note_AssesArr']!='')
		$arrAnPHxDB = unserialize($row['modi_note_AssesArr']);
		else
		$arrAnPHxDB = array();
	$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);

	// Save assessments in db
	$tmp = trim($row['assess_plan']);
	$flg_assess_save_db = (empty($elem_editModeAssess) || empty($tmp)) ? 1 : 0;
	if(!empty($elem_editModeAssess)){ $chart_assessment_plans_id = $row["id"]; }
	//

	$arrAnPHX = array();
	$oChartApXml = new ChartAP($patient_id,$form_id);
	$arrApVals = $oChartApXml->getVal();
	$arrAsses = $arrApVals['data']['ap'];
	//while(true){
	for($i=0;$i<$lenAp;$i++){

		//if(isset($_POST["elem_assessment".$c])){
			//print ("elem_assessment".$c." exists.<br>");
			if($elemAp_assess_dxcode[$i] != ''){
			$tmpAsses_dxcode=$elemAp_assess_dxcode[$i];
			$tmpAsses_dxcode = filter_var($tmpAsses_dxcode, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
			$tmpAsses = trim($elemAp_assess[$i])." (".$elemAp_assess_dxcode[$i].")";
			$tmp_asmt_dxcode_id=!empty($elem_asmt_dxcode_id[$i]) ? $elem_asmt_dxcode_id[$i] : 0 ;
			}else{
			$tmpAsses_dxcode="";
			$tmpAsses = trim($elemAp_assess[$i]);
			$tmp_asmt_dxcode_id=0;
			}
			$tmpPlan = $elemAp_plan[$i];
			$tmpapConMeds = $elem_apConMeds[$i];
			$tmpRes = (in_array($c,$elemAp_resolve)) ? 1 : 0;
			$tmpNc = (in_array($c,$elemAp_ne)) ? 1 : 0;
			$edit = 0;
			$tmpAsses_problistid=$elem_problist_id_assess[$i];
			$tmppt_ap_id=!empty($el_pt_ap_id[$i]) ? $el_pt_ap_id[$i] : 0 ;

			//if($_REQUEST['elem_assessment_typeAhead'.$i] != 1){
				//$arrAssesTmp = explode("(",$elemAp_assess[$i]);
				//$qry = "SELECT * FROM console_to_do WHERE assessment = '".$elemAp_assess[$i]."'";
				/*$arr = explode("[ICD-10: ",$elemAp_assess[$i]);
				$ICD = '';
				$assessment = $arr[0];
				if(count($arr)>1){
					$ICD10_ARR	= explode(",",$arr[1]);
					if(count($ICD10_ARR)>1){
						$ICD	= $ICD10_ARR[0];
					}
				}else{
					$arr = explode("(",$elemAp_assess[$i]);
					$assessment = $arr[0];
					if(count($arr)>1)
					$ICD = trim($arr[1],")");
				}*/
				$assessment = $elemAp_assess[$i];
				$ICD_DX = $elemAp_assess_dxcode[$i];
				if($flgDynamicAP==1){
					$qry = "SELECT * FROM console_to_do WHERE assessment = '".sqlEscStr($assessment)."' AND (dxcode = '".$ICD_DX."' or dxcode_10 like '%".$ICD_DX."%') AND (providerID = '".$_SESSION['authId']."')";
					$res = sqlQuery($qry);
					if($res==false && $tmpAsses!=""){
						$tmp_ICD_type = ($_REQUEST['hid_icd10'] == 1) ? "1" : "0";
						if($tmp_ICD_type==1){
							$ins_dx_code="dxcode_10='".$ICD_DX."'";
						}else{
							$ins_dx_code="dxcode='".$ICD_DX."'";
						}
						sqlQuery("INSERT INTO console_to_do SET providerID = '".$_SESSION['authId']."',assessment = '".sqlEscStr($assessment)."',".$ins_dx_code.",plan = '".sqlEscStr($tmpPlan)."', dynamic_ap=1,ICD_type='".$tmp_ICD_type."',date_time3 ='".date('Y-m-d H:i:s')."' ");
					}
				}
				unset($arr);

			//}
			/*if((isset($arrAnPHxDB[$i]['modiBy'])  && $_SESSION['authId']!=$arrAnPHxDB[$i]['modiBy']) || !isset($arrAnPHxDB[$i]['modiBy']) || $arrAnPHxDB[$i]['modiBy']==''){*/
				if($arrAsses[$i]['assessment'] != $tmpAsses) $edit=1;
				if($arrAsses[$i]['plan'] != $tmpPlan) $edit=1;
				if($arrAsses[$i]['ne'] != $tmpNc) $edit=1;
				if($arrAsses[$i]['resolve'] != $tmpRes) $edit=1;
			//}


			if(in_array($c,$elemAp_Ou)){
				$tmpEye = "OU";
			}else if(in_array($c,$elemAp_Od)){
				$tmpEye = "OD";
			}else if(in_array($c,$elemAp_Os)){
				$tmpEye = "OS";
			}else{
				$tmpEye = "";
			}
			if($arrAsses[$i]['eye'] != $tmpEye && (!empty($tmpAsses) || !empty($tmpPlan))){ $edit=1; }
			if($edit){
				$arrAnPHX['curr']['Asses'] = trim($tmpAsses);
				$arrAnPHX['curr']['Plan'] = trim($tmpPlan);
				$arrAnPHX['curr']['Res'] = trim($tmpRes);
				$arrAnPHX['curr']['NE'] = trim($tmpNc);
				$arrAnPHX['curr']['Site'] = trim($tmpEye);
				$arrAnPHX['modi_by'] = $_SESSION['authId'];
				$arrAnPHX['time'] = date('Y-m-d H:i:s');

				$arrAnPHX['prev']['Asses'] = trim($arrAsses[$i]['assessment']);
				$arrAnPHX['prev']['Plan'] = trim($arrAsses[$i]['plan']);
				$arrAnPHX['prev']['Res'] = trim($arrAsses[$i]['resolve']);
				$arrAnPHX['prev']['NE'] = trim($arrAsses[$i]['ne']);
				$arrAnPHX['prev']['Site'] = trim($arrAsses[$i]['eye']);

				//loop to get Prev User and Time
				list($tmp_preUsr, $tmp_preTime) = $this->getAP_prevuser_time($arrAnPHX['prev']['Asses'], $arrAnPHxDB, $last_opr_id, $exam_date);

				$arrAnPHX['preUsr'] = $tmp_preUsr; //(isset($arrAnPHxDB[$i]["modi_by"]) && !empty($arrAnPHxDB[$i]["modi_by"])) ? $arrAnPHxDB[$i]["modi_by"] : $last_opr_id;
				$arrAnPHX['preTime'] = $tmp_preTime; //(isset($arrAnPHxDB[$i]["time"]) && !empty($arrAnPHxDB[$i]["time"])) ? $arrAnPHxDB[$i]["time"] : date('Y-m-d H:i:s', strtotime($exam_date)) ; //$last_opr_id; // either $exam_date  or loop to modiArr and find time based on index
				if(preg_replace('/\s+/', '',$arrAnPHX['curr']['Asses']) != preg_replace('/\s+/', '',$arrAnPHX['prev']['Asses']) ||
				   preg_replace('/\s+/', '',$arrAnPHX['curr']['Plan']) != preg_replace('/\s+/', '',$arrAnPHX['prev']['Plan']) ||
				   $arrAnPHX['curr']['Res'] != $arrAnPHX['prev']['Res'] ||
				   $arrAnPHX['curr']['NE'] != $arrAnPHX['prev']['NE'] ||
				   $arrAnPHX['curr']['Site'] != $arrAnPHX['prev']['Site']
				   ){
					if(count($arrAnPHX)>0){
						array_push($arrAnPHxDB,$arrAnPHX);
					}
				}
			}

			if(empty($_POST["elem_ischartEdited"])){ //if Chart Note is NOT Edited

			if(empty($tmpAsses) && empty($tmpPlan) && empty($tmpRes) && empty($tmpNc)){

				//remove from problem list
				if(!empty($tmpAsses_problistid)){
					$oPtProblemList->setStatus($tmpAsses_problistid,"Deleted");

					// Delete Problem from DSS
					if (isDssEnable() && array_key_exists('elem_masterFinalize', $_POST) && $_POST['elem_masterFinalize'] == '1') {
						$this->delete_problem_from_vista($tmpAsses_problistid);
					}
				}

				$c++;
				continue;
			}

			}

			$arrAp_assess[] = $tmpAsses; //$_POST["elem_assessment".$c];
			$arrAp_plan[] = $tmpPlan; //$_POST["elem_plan".$c];
			$arrAp_resolve[] = $tmpRes;
				//!empty($_POST["elem_resolve".$c]) ? $_POST["elem_resolve".$c]:0;
			$arrAp_ne[] = $tmpNc;
				//!empty($_POST["no_change_".$c]) ? $_POST["no_change_".$c] : 0;
			$arrAp_eye[] = $tmpEye;
			$arrAp_conmed[] = $tmpapConMeds;
			$arrAp_assess_wodx[] = remSiteDxFromAssessment($tmpAsses);
			$arrAp_apid[] = $tmppt_ap_id;
			$arrAp_dx_id[] = $tmp_asmt_dxcode_id;
			/*
			//Set Schedule Plan
			if(isset($_POST["elem_divTSxId".$c]) && !empty($_POST["elem_divTSxId".$c])){
				$sql = "UPDATE schedule SET pln_id = '".$c."' WHERE id='".$_POST["elem_divTSxId".$c]."'  ";
				$res = sqlQuery($sql);
			}
			*/
			$oChartApXml->ap_resetOrderNum($i+1,$c);

			//Update Pt Problem List
			if(empty($tmpRes)){ // Only unResolved will Enter
				$tmp = $oPtProblemList->isProblemExists($tmpAsses,1); //Check if Problem Exists in List
				//Insert into Pt Problem List
				$arrUp = array();
				$arrUp["problem_name"] = addslashes($tmpAsses);
				$arrUp["onset_date"] = $date_of_service; // DOS in CC & Hx
				$arrUp["comments"] = "";
				$arrUp["status"] = "Active";
				$arrUp["user_id"] = $_SESSION["authId"];
				$arrUp["pt_id"] = $patient_id;
				$arrUp["signerId"] = $doctorId;
				$arrUp["coSignerId"] = $cosigner_id;
				$arrUp["form_id"] = $form_id;
				$arrUp["dx"] = $tmpAsses_dxcode;

				$arrUp['service_eligibility'] = $_POST['service_eligibility'];

				if($tmpAsses!=""){
					$pl_exp_whr="";
					$pl_exp = explode('(',trim($tmpAsses));
					$pl_exp = explode('-',trim($pl_exp[0]));
					$pl_exp_whr=trim($pl_exp[0]);

					$ccda_code_qry=sqlQuery("select snowmed_ct from diagnosis_code_tbl where diag_description='".addslashes($pl_exp_whr)."' order by delete_status asc");
					$ccda_code_row=$ccda_code_qry;
					$arrUp["ccda_code"] = $ccda_code_row['snowmed_ct'];
				}


				if(empty($tmp)&&empty($tmpAsses_problistid)){ // if Not
					$tmpAsses_problistid = $oPtProblemList->insertRec($arrUp);
					$arrUp['lastInsertId'] = $tmpAsses_problistid;

					if (isDssEnable() && array_key_exists('elem_masterFinalize', $_POST) && $_POST['elem_masterFinalize'] == '1') {
						$this->upload_problem_to_vista($arrUp);
					}

				}elseif(!empty($tmpAsses_problistid)||!empty($tmp)){
					if(empty($tmpAsses_problistid)&&!empty($tmp)){ //pastdata
						$tmpAsses_problistid = $tmp;
					}
					$arrUp["id"] = $tmpAsses_problistid;
					$oPtProblemList->updateRec($arrUp);

					if (isDssEnable() && array_key_exists('elem_masterFinalize', $_POST) && $_POST['elem_masterFinalize'] == '1') {
						$this->upload_problem_to_vista($arrUp);
					}
				}

				/*
				else if(!empty($tmp)){
					//Set To resolve
					$oPtProblemList->setStatus($tmp,"Resolved");
				}
				*/
			}else{
				$tmp = $oPtProblemList->isProblemExists($tmpAsses,1); //Check if Problem Exists in List
				if(!empty($tmp)){
					//Set To resolve
					$oPtProblemList->setStatus($tmp,"Resolved");
				}
			}

			//
			$arrAp_prob_list_id[]=$tmpAsses_problistid;

			$c++;
		//}else{
			//print ("<br>elem_assessment".$c." NOT exists.<br>");
		//	break;
		//}
	}

	//--
	//Check Old Problem list of current visit and modify
	if(count($arrVisitProbList)>0){
		foreach($arrVisitProbList as $key => $val){
			if(!in_array_nocase($val,$arrAp_assess_wodx)&&!in_array_nocase($val,$arrAp_assess)){
				$tmp = $oPtProblemList->isProblemExists($val,1); //Check if Problem Exists in List
				if(!empty($tmp)){
					$oPtProblemList->updateVisitProblems($val,$form_id); //Check if Problem Exists in List
				}
			}
		}
	}

	//Get strXML
	$strAPXml="";
	$oChartApXml = new ChartAP($patient_id,$form_id);
	if(empty($flg_assess_save_db)){
	list($strAPXml, $str_modi_note_Asses) = $oChartApXml->getXml(array($arrAp_assess,$arrAp_plan,$arrAp_resolve,$arrAp_ne,$arrAp_eye, $arrAp_conmed,$arrAp_prob_list_id),$_SESSION["authId"],1);
	$strAPXml = sqlEscStr($strAPXml);
	}
	//check

	if($elem_editModeAssess == "0"){
		$last_opr_id = $_SESSION["authId"];
		// Insert
		$sql = "INSERT INTO chart_assessment_plans ".
			 "(".
			 "id, ".
			 "assessment_1, assessment_2, assessment_3, assessment_4, ".
			 "plan_notes, follow_up, follow_up_numeric_value, retina, ".
			 "neuro_ophth, id_precation, lid_scrubs_oint, ".
			 "patient_understands, patient_id, ".
			 "sign_coords,sign_coords_dateTime, ".
			 "form_id, ".
			 "exam_date, rd_precation, doctor_name, ".
			 "patient_understands_select, ".
			 "plan_notes_1, plan_notes_2, plan_notes_3, plan_notes_4, ".
			 "plan_resolve_1, plan_resolve_2, plan_resolve_3, plan_resolve_4, ".
			 "no_change_1, no_change_2, no_change_3, no_change_4,no_change_5,".
			 "doctorId, ".
			 "continue_meds, followUpVistType,patient_task,dr_task,scribedBy,followup, ".
			 "commentsForPatient, ".
			 "sign_coordsCosigner, cosigner_id,cosigner_dateTime, ".
			 "assess_plan, resiHxReviewd, ".
			 "sign_path, cosign_path, ".
			 "rxhandwritten, uid, last_opr_id, ".
			 "labhandwritten, imagehandwritten, ".
			 "consult_reason, surgical_ocular_hx, ".
			 "pt_goal, health_concern, vst_soc, soc_desc, ".
			 "refer_to_id, transition_reason, transition_notes, refer_to, ".
			 "doctorName_id, refer_to_code ".
			 ")".
			 "VALUES ".
			 "(".
			 "NULL, ".
			 "'".$assessment_1."', '".$assessment_2."', '".$assessment_3."', '".$assessmentAll."', ".
			 "'".$plan_notes."', '".$follow_up."', '".$follow_up_numeric_value."', '".$retina."', ".
			 "'".$neuro_ophth."', '".$id_precation."', '".$lid_scrubs_oint."', ".
			 "'".$patient_understands."', '".$patient_id."', ".
			 "'".$sign_coords."', '".$sign_coords_dateTime."', ".
			 "'".$form_id."', ".
			 "'".$exam_date."', '".$rd_precation."', '".$doctor_name."', ".
			 "'".$patient_understands_select."', ".
			 "'".$plan_notes_1."', '".$plan_notes_2."', '".$plan_notes_3."', '".$planAll."', ".
			 "'".$plan_resolve_1."', '".$plan_resolve_2."', '".$plan_resolve_3."', '".$plan_resolve_4."', ".
			 "'".$no_change_1."','".$no_change_2."','".$no_change_3."','".$no_change_4."','".$no_change_5."',".
			 "'".$doctorId."', ".
			 "'".$continue_meds."', '".$followUpVistType."','".$patient_task."','".$dr_task."', ".
			 "'".$scribedBy."', '".$strFollowup."', '".$commentsForPatient."', ".
			 "'".$sign_coordsCosigner."', '".$cosigner_id."', '".$cosigner_dateTime."', ".
			 "'".$strAPXml."', ".
			 "'".$resiHxReviewd."', ".
			 "'".$sign_path."', '".$cosign_path."', ".
			 "'".$rxhandwritten."', '".$_SESSION["authId"]."', '".$last_opr_id."', ".
			 "'".$labhandwritten."', '".$imagehandwritten."', ".
			 "'".$consult_reason."', '".$surgical_ocular_hx."', ".
			 "\"".$elem_pt_goal_str."\", \"".$elem_pt_health_concern."\", \"".$vst_soc."\", \"".$soc_desc."\", ".
			 "'".$refer_to_id."', '".$transition_reason."', '".$transition_notes."', '".$refer_to."', ".
			 "'".$doctorName_id."', '".$refer_code."' ".
			 ")";
		$insertId = sqlInsert($sql);
		$assessment_plan_inserted = $insertId;
		$this->arG["assessment_plan_inserted"] = $assessment_plan_inserted;
		$chart_assessment_plans_id = $assessment_plan_inserted;
	}else{
		if(count($arrAnPHxDB)>0){
			$seri_arrAnPHxDB = serialize($arrAnPHxDB);
		}else{
			$seri_arrAnPHxDB = '';
		}
		//var_dump($blEnableHTMLSignature);
		// Update
		$sql = "UPDATE chart_assessment_plans ".
			 "SET ".
			 "assessment_1 = '".$assessment_1."', ".
			 "assessment_2 = '".$assessment_2."', ".
			 "assessment_3 = '".$assessment_3."', ".
			 "assessment_4 = '".$assessmentAll."', ".
			 "plan_notes = '".$plan_notes."', ".
			 "follow_up = '".$follow_up."', ".
			 "follow_up_numeric_value = '".$follow_up_numeric_value."', ".
			 "retina = '".$retina."', ".
			 "neuro_ophth = '".$neuro_ophth."', ".
			 "id_precation = '".$id_precation."', ".
			 "lid_scrubs_oint = '".$lid_scrubs_oint."', ".
			 "patient_understands = '".$patient_understands."', ".
			 " ";
		if($blEnableHTMLSignature == false){
			$sql .= "sign_coords = '".$sign_coords."',";
		}
		$sql .= "sign_coords_dateTime = '".$sign_coords_dateTime."', ".
			 "exam_date = '".$exam_date."', ".
			 "rd_precation = '".$rd_precation."', ".
			 "doctor_name = '".$doctor_name."', ".
			 "patient_understands_select = '".$patient_understands_select."', ".
			 "plan_notes_1 = '".$plan_notes_1."', ".
			 "plan_notes_2 = '".$plan_notes_2."', ".
			 "plan_notes_3 = '".$plan_notes_3."', ".
			 "plan_notes_4 = '".$planAll."', ".
			 "plan_resolve_1 = '".$plan_resolve_1."', ".
			 "plan_resolve_2 = '".$plan_resolve_2."', ".
			 "plan_resolve_3 = '".$plan_resolve_3."', ".
			 "plan_resolve_4 = '".$plan_resolve_4."', ".
			 "no_change_1 = '".$no_change_1."',".
			 "no_change_2 = '".$no_change_2."',".
			 "no_change_3 = '".$no_change_3."',".
			 "no_change_4 = '".$no_change_4."',".
			 "no_change_5 = '".$no_change_5."',".
			 "doctorId = '".$doctorId."', ".
			 "continue_meds = '".$continue_meds."', ".
			 "patient_task = '".$patient_task."', ".
			 "dr_task = '".$dr_task."', ".
			 "followUpVistType = '".$followUpVistType."', ".
			 "scribedBy = '".$scribedBy."', ".
			 "followup = '".$strFollowup."', ".
			 "commentsForPatient = '".$commentsForPatient."', ";
			if($blEnableHTMLSignature == false){
				$sql .= "sign_coordsCosigner = '".$sign_coordsCosigner."',";
			}
			$sql .= "cosigner_id = '".$cosigner_id."', ".
			 "cosigner_dateTime = '".$cosigner_dateTime."', ".
			 "assess_plan = '".$strAPXml."', ".
			 "resiHxReviewd = '".$resiHxReviewd."', ".
			 "rxhandwritten = '".$rxhandwritten."', ".
			 "labhandwritten = '".$labhandwritten."', ".
			 "imagehandwritten = '".$imagehandwritten."', ".
			 "sign_path = '".$sign_path."', ".
			 "cosign_path = '".$cosign_path."', ".
			 "consult_reason = '".$consult_reason."', ".
			 /*"modi_note_Asses = CONCAT('".sqlEscStr($str_modi_note_Asses)."',modi_note_Asses), ".*/
			 "modi_note_AssesArr = '".sqlEscStr($seri_arrAnPHxDB)."', last_opr_id = '".$last_opr_id."', uid = '".$_SESSION["authId"]."', ".
			 /*"followNumber = '".$followNumber."'".*/
			 "surgical_ocular_hx='".$surgical_ocular_hx."', ".
			 "pt_goal=\"".$elem_pt_goal_str."\", ".
			 "health_concern=\"".$elem_pt_health_concern."\", ".
			 "vst_soc=\"".$vst_soc."\", soc_desc=\"".$soc_desc."\", ".
			 "refer_to_id='".$refer_to_id."', transition_reason='".$transition_reason."', transition_notes='".$transition_notes."', refer_to='".$refer_to."', ".
			 "doctorName_id='".$doctorName_id."', refer_to_code='".$refer_code."' ".
			 "WHERE form_id = '".$form_id."' AND patient_id = '".$patient_id."' ";
		$res = sqlQuery($sql);
	}

	// Save assessments in db --
	if(!empty($flg_assess_save_db)){
		$ar_cur_pt_ap_ids = $oChartApXml->save_pt_assess_plan(array($arrAp_assess,$arrAp_plan,$arrAp_resolve,$arrAp_ne,$arrAp_eye, $arrAp_conmed,$arrAp_prob_list_id, $arrAp_apid, $arrAp_dx_id), $chart_assessment_plans_id);
	}
	//--

	//Set Change Date Arc Rec --
	$this->setChangeDtArcRec("chart_assessment_plans");
	//Set Change Date Arc Rec --

	//
	$this->arG["arrAp_assess"] = $arrAp_assess;
	$this->arG["arrAp_prob_list_id"] = $arrAp_prob_list_id;
	$this->arG["ar_cur_pt_ap_ids"] = $ar_cur_pt_ap_ids;

	//return array($arrAp_assess,$arrAp_prob_list_id);
}

function saveSignatures(){

	$form_id=$this->fid;

	//Signatures -------------------------

	$signatureNum=$_POST["elem_signatureNum"];

	/*
	$sign_coords = $_POST["elem_signCoords"];
	if(empty($sign_coords) == false && $sign_coords != '0-0-0:;'){
		$sign_coords_dateTime = date('Y-m-d H:i:s');
	}
	else{
		$sign_coords_dateTime = '';
	}
	$proId = $_POST["elem_physicianId"];
	$sign_path = sqlEscStr($_POST["elem_sign_path"]);


	//elem_signCoordsCosigner
	$sign_coordsCosigner = $_POST["elem_signCoordsCosigner"];
	if(empty($sign_coordsCosigner) == false && $sign_coordsCosigner != '0-0-0:;'){
		$cosigner_dateTime = date('Y-m-d H:i:s');
	}
	else{
		$cosigner_dateTime = '';
	}
	$cosigner_id = $_POST["elem_cosignerId"];
	$cosign_path = sqlEscStr($_POST["elem_cosign_path"]);
	*/

	//Get Existing Signatures
	$arrExSigns=$arrCurrSigns=array();
	$sql="SELECT pro_id, sign_coords_dateTime, sign_path,sign_type  FROM chart_signatures WHERE form_id='".$form_id."' ORDER BY sign_type ";
	$rez=sqlStatement($sql);
	for($a=0;$row=sqlFetchArray($rez);$a++){
		$tmp = $row["sign_type"];
		$arrExSigns["phy"][$tmp]=$row["pro_id"];
		$arrExSigns["sign_coords_dateTime"][$tmp]=$row["sign_coords_dateTime"];
		$arrExSigns["sign_path"][$tmp]=$row["sign_path"];
	}

	//print_r($arrExSigns["sign_path"]);

	for($a=1;$a<=$signatureNum;$a++){

		/*
		if($a==2){
			$var_signCoods = sqlEscStr($_POST["elem_signCoordsCosigner"]);
			$var_phyId = $_POST["elem_cosignerId"];
			$var_signPath = sqlEscStr($_POST["elem_cosign_path"]);
		}else if($a==1){
			$var_signCoods = sqlEscStr($_POST["elem_signCoords"]);
			$var_phyId = $_POST["elem_physicianId"];
			$var_signPath = sqlEscStr($_POST["elem_sign_path"]);
		}else{
		*/
			$var_signCoods = sqlEscStr($_POST["elem_signCoords".$a]);
			$var_phyId = $_POST["elem_physicianId".$a];
			$var_signPath = sqlEscStr($_POST["elem_sign_path".$a]);
		//}

		//This is for chart_assessment_plans fields--
		//Last login physician will be owner of chart
		if(!empty($_POST["elem_curPhysicianId"])){

			if($var_phyId==$_POST["elem_curPhysicianId"]){
				$sign_path=$var_signPath;
				$sign_coords=$var_signCoods;
				$elem_physicianId=$doctorId=$var_phyId;
				$this->arG["elem_physicianId"] = $elem_physicianId;
				$this->arG["doctorId"] = $doctorId;
			}

		}else	if($a==1){
			//get user type
			$oUser = new User($var_phyId);
			$var_phy_type = $oUser->getUType(); //getUserType($var_phyId);
			if(in_array($var_phy_type, $GLOBALS['arrValidCNPhy'])){ //if user is valid CN phy
				$sign_path=$var_signPath;
				$sign_coords=$var_signCoods;
				$elem_physicianId=$doctorId=$var_phyId;
				$this->arG["elem_physicianId"] = $elem_physicianId;
				$this->arG["doctorId"] = $doctorId;
			}
		}

		/*if($a==2){
			$cosign_path=$var_signPath;
			$sign_coordsCosigner=$var_signCoods;
			$cosigner_id=$var_phyId;
		}*/
		//This is for chart_assessment_plans fields--

		//echo "<br/>".$var_signPath."<br/>".$var_phyId."<br/>".$var_signCoods;

		//print_r($arrExSigns["sign_path"]);

		if(!empty($var_phyId)){ //&& !empty($var_signCoods) && $var_signCoods != '0-0-0:;'

			$sign_coords_dateTime_tmp = '';
			$flg_update=0;
			if((!empty($var_signCoods) && $var_signCoods != '0-0-0:;')||(!empty($var_signPath))){
				if(count($arrExSigns["phy"])<=0 || (count($arrExSigns["phy"])>0 &&  $arrExSigns["phy"][$a]==$var_phyId && $arrExSigns["sign_path"][$a]!=$var_signPath)){
					$sign_coords_dateTime_tmp = date('Y-m-d H:i:s');
				}else if(count($arrExSigns["phy"])>0 && $arrExSigns["phy"][$a]!=$var_phyId && $arrExSigns["sign_path"][$a]!=$var_signPath){
					$tmp = array_search($var_phyId, $arrExSigns["phy"]);
					if($tmp!==false){
						$sign_coords_dateTime_tmp = $arrExSigns["sign_coords_dateTime"][$tmp];
					}
				}
			}else{
				$flg_update=1;
			}

			//Check if new record
			//echo "<br><br><br>".$var_phyId;
			//print_r($arrExSigns["phy"]);
			if(count($arrExSigns["phy"])>0 && in_array($var_phyId,$arrExSigns["phy"])){

				if(!empty($sign_coords_dateTime_tmp)||$flg_update==1){

					//-fill time if it is empty
					if(empty($sign_coords_dateTime_tmp) || strpos($sign_coords_dateTime_tmp,"0000")!==false){
						if((!empty($var_signCoods) && $var_signCoods != '0-0-0:;')||(!empty($var_signPath))){
							$sign_coords_dateTime_tmp = date('Y-m-d H:i:s');
						}
					}
					//-

					//update
					$sql = "UPDATE chart_signatures SET ".
							"sign_coords='".sqlEscStr($var_signCoods)."',
							sign_coords_dateTime='".$sign_coords_dateTime_tmp."',
							sign_path='".$var_signPath."',
							sign_type='".$a."'
							".
							"WHERE form_id='".$form_id."' AND pro_id='".$var_phyId."' ";
					$r=sqlQuery($sql);
					$arrCurrSigns[]=$var_phyId;
				}
			}else{

				if(count($arrCurrSigns)<=0 || !in_array($var_phyId,$arrCurrSigns)){

					//-fill time if it is empty
					if(empty($sign_coords_dateTime_tmp) || strpos($sign_coords_dateTime_tmp,"0000")!==false){
						if((!empty($var_signCoods) && $var_signCoods != '0-0-0:;')||(!empty($var_signPath))){
							$sign_coords_dateTime_tmp = date('Y-m-d H:i:s');
						}
					}
					//-

					//Insert
					$sql="INSERT INTO chart_signatures (id, form_id, pro_id,sign_coords,sign_coords_dateTime,sign_path,sign_type) ".
						"VALUES (NULL, '".$form_id."', '".$var_phyId."', '".sqlEscStr($var_signCoods)."', '".$sign_coords_dateTime_tmp."', '".$var_signPath."' , '".$a."'  )";
					$r=sqlQuery($sql);
					$arrCurrSigns[]=$var_phyId;
				}
			}
		}
	}

	//exit("DONE");

	//Signatures -------------------------


}

function saveMemo(){

	$form_id=$this->fid;
	$patient_id=$this->pid;

	// MEMO
	//$memoText = $_REQUEST['memoText'];
	$memo = $_REQUEST['memo'];
	$visionInserted = $this->arG["visionInserted"];
	$assessment_plan_inserted = $this->arG["assessment_plan_inserted"];

	if($memo){
		//check
		$cQry = "select memo_id FROM memo_tbl WHERE form_id='".$form_id."' AND patient_id = '".$patient_id."' ";
		$row = sqlQuery($cQry);
		$elem_editModeVis = ($row == false) ? "0" : "1";
		if($elem_editModeVis == "0"){
			$memo_id = sqlInsert("INSERT INTO memo_tbl SET
						form_id = '$form_id',
						patient_id = '$patient_id',
						vision_id =  '$visionInserted',
						assessment_plan_id =  '$assessment_plan_inserted',
						memo_text =  '$memoText'");
			//$memo_id = imw_insert_id();
		}else{
			$memo_id = $row["memo_id"];
			$StrQry = sqlQuery("UPDATE memo_tbl SET
						memo_text =  '$memoText'
						WHERE memo_id = '".$memo_id."'");
		}

		//Memo Texts
		$arrCurMemoTxtId=array();
		$cntrMemoTxt = $_POST["elem_cntrTableMemo"];
		for($i=1;$i<=$cntrMemoTxt;$i++){
			$memoText = sqlEscStr($_POST["elem_memoText".$i]);
			$memoDate = wv_formatDate($_POST["elem_memoDate".$i],0,0, "insert"); //getDateFormatDB($_POST["elem_memoDate".$i]);
			$memoProvider = $_POST["elem_memoProvider".$i];
			$memoTextId = $_POST["elem_memoTextId".$i];

			if(!empty($memoText)){

				//get memo text id
				if(empty($memoTextId)){
					$sql = "SELECT memo_text_id FROM chart_memo_text where memo_id='".$memo_id."' AND memo_text='".$memoText."' AND deleted_by='0'  ";
					$row=sqlQuery($sql);
					if($row!=false){
						$memoTextId = $row["memo_text_id"];
					}
				}

				if(!empty($memoTextId)){ // Update
					$sql = "UPDATE chart_memo_text SET memo_id='".$memo_id."', ".
						 "memo_text='".$memoText."', ".
						 "memo_date='".$memoDate."', ".
						 "provider_id='".$memoProvider."' ".
						 "WHERE memo_text_id = '".$memoTextId."' ";
					$res = sqlQuery($sql);
					$arrCurMemoTxtId[]=$memoTextId;
				}else{ //Insert
					$sql = "INSERT INTO chart_memo_text (memo_text_id,memo_id, memo_text, memo_date,provider_id) ".
						 "VALUES (NULL, '".$memo_id."' ,'".$memoText."', '".$memoDate."', '".$memoProvider."') ";
					$memoTextId = sqlInsert($sql);
					$arrCurMemoTxtId[]=$memoTextId;
				}
			}
		}

		//Delete
		$sql = "UPDATE chart_memo_text SET deleted_by='".$_SESSION["authId"]."' WHERE memo_id='".$memo_id."' ";
		if( count($arrCurMemoTxtId) > 0 ){
			$strTmp = "'".implode("', '", $arrCurMemoTxtId)."'";
			$sql .= " AND memo_text_id NOT IN (".$strTmp.") ";
		}
		$res = sqlQuery($sql);
	}
}

function saveMaster(){
	// Chart Master Table
	$date_of_service = $this->arG["date_of_service"];
	$doctorId = $this->arG["doctorId"];
	$id = $_POST["elem_masterId"];
	$finalize = $_POST["elem_masterFinalize"];
	$record_validity = $_POST["elem_masterRecordValidity"];
	$caseId = $_POST["elem_masterCaseId"];
	$this->arG["caseId"] = $caseId;
	if(!empty($doctorId)){
		$providerId =  $doctorId; //Update with Signer
	}else{
		//Feb15,2012: If a Chart note is started by a tech or Scribe then on Save it should go to the Appointee or Primary Physicians console Unfinalized inbox.  If the Physician finalize s the chart note then remove it from his inbox.
		$opt = new Patient($this->pid);
		$providerId=$opt->getPro4CnIn("","",$date_of_service);
		if(empty($providerId)){
			$providerId = $_POST["elem_masterProviderId"];
		}
	}
	//
	if(!empty($providerId)){
		if(empty($this->arG["elem_physicianId"])){ $this->arG["elem_physicianId"] = $providerId; }
		if(empty($this->arG["doctorId"])){ $this->arG["doctorId"] = $providerId; }
	}

	$encounterId = $_POST["elem_masterEncounterId"];
	$this->arG["encounterId"] = $encounterId;
	$ptVisit = $_POST["elem_masterPtVisit"];
	$testing = $_POST["elem_masterTesting"];
	$update_date = $_POST["elem_masterUpdateDate"];
	$finalizerId = $_POST["elem_masterFinalizerId"];
	$elem_masterFinalDate = $_POST["elem_masterFinalDate"];

    //Saving for DSS if visit is service connected eligibility
	$service_eligibility='0';
	if(isDssEnable()) {
		$sql = imw_query("SELECT `service_eligibility` FROM `chart_master_table` WHERE `id` = '".$id."'");
		if(imw_num_rows($sql) > 0) {
			$result = imw_fetch_assoc($sql);
			$service_eligibility = '\''.$result['service_eligibility'].'\'';
		}

	    if(isset($_POST["service_eligibility"]) && $_POST["service_eligibility"]!='') {
	    	parse_str($_POST["service_eligibility"], $sc_option);
	        $service_eligibility = '\''.serialize($sc_option['dss']).'\'';
	    }
	}
    // pre($service_eligibility,1);

	//Check if finalized and date  or finalizerid is empty
	if($finalize == "1"){
		if(empty($elem_masterFinalDate)){$elem_masterFinalDate = date("Y-m-d H:i:s");}
		if(empty($finalizerId)){ $finalizerId=(!empty($_POST["elem_chartOprtrId"])) ? $_POST["elem_chartOprtrId"] : $_SESSION['authId']; }
	}

	//Add Provider Id
	$chkprovIds = $_SESSION["authId"].", ";

	$sql = "UPDATE chart_master_table ".
		 "SET ";
	if($_POST["elem_isFormReviewable"] == "0"){
		$sql .= "update_date = '".$update_date."', ";
	}

	// save dss tiu ifn in db
	if (isDssEnable() && array_key_exists('dssTiuTitle', $_POST)) {
		$sql .= "tiu_ifn = '".$_POST['dssTiuTitle']."', ";
	}
	if (isDssEnable()) {
		$sql .= "service_eligibility=".$service_eligibility.", ";
	}

	$sql .= "finalize = '".$finalize."', ".
		 "record_validity = '1', ".
		 "caseId = '".$caseId."', ".
		 "providerId = '".$providerId."', ".//Update With signer
		 //"encounterId = '".$encounterId."', ".//Do not Update
		 "ptVisit = '".sqlEscStr($ptVisit)."', ".
		 "testing = '".sqlEscStr($testing)."', ".
		 "finalizerId = '".$finalizerId."', ".
		 //"purge_status='".$elem_PurgeStatusField."', ". ??
		 "finalizeDate='".$elem_masterFinalDate."', ".
		 "date_of_service='".$date_of_service."', ".
		 "provIds = CONCAT(provIds, IF(LOCATE('".$chkprovIds."', provIds)>0,'','".$chkprovIds."')) ";
		 //if(isset($_REQUEST['hid_icd10']))
		 //$sql .= ", enc_icd10 = '".$_REQUEST['hid_icd10']."'";
	$sql .=	" WHERE id= '".$id."' ";
	$res = sqlQuery($sql);

	//
	$this->arG["finalize"] = $finalize ;
	$this->arG["finalizerId"] = $finalizerId ;
	$this->arG["elem_masterFinalDate"] = $elem_masterFinalDate ;

}

function HL7Actions(){
	if($_POST['el_btfinalize_pressed'] == "1"){ //CODE BELOW WILL BE EXECUTED IF FINALIZED BUTTON IS PRESSED
		if(constant('PTVISIT_ORU_GENERATION')==true){ //CODE BELOW WILL BE EXECUTED IF PATIENT VISIT ORU MESSAGE IS TURNED ON IN PRACTICE CONFIG FILE
			require_once( dirname(__FILE__).'/../../../hl7sys/old/CLS_makeHL7.php');
			$makeHL7		= new makeHL7;
			$makeHL7->log_HL7_message($this->fid,'PTVISIT_ORU');
		}
	}
}

//Objective Notes
function saveObjNote(){
	$form_id=$this->fid;
	if(isset($_POST["elem_objNotes"])){// is set obj_notes

		$obj_id = "";
		$obj_notes = sqlEscStr($_POST["elem_objNotes"]);

		$sql = "SELECT obj_id FROM chart_objective_notes WHERE obj_form_id = '".$form_id."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$obj_id = (!empty($row["obj_id"])) ? $row["obj_id"] : "";
		}
		//
		if(!empty($obj_id)){
			$sql = "UPDATE chart_objective_notes
					SET obj_notes = '".$obj_notes."'
					WHERE obj_id = '".$obj_id."' ";
			$res = sqlQuery($sql);
		}else{
			$sql = "INSERT INTO chart_objective_notes (obj_id, obj_notes, obj_form_id)
					VALUES (NULL, '".$obj_notes."', '".$form_id."')";
			$res = sqlQuery($sql);
		}
	}

}

function saveSuperBill(){
	$sb_arrMerged=array();
	if(isset($_POST["elem_sb_tsbIds"]) && !empty($_POST["elem_sb_tsbIds"])){
		$sb_arrMerged = $_POST["elem_sb_tsbIds"];
	}

	$flgThisisCN = true;
	$oSuperbillSaver = new SuperbillSaver($this->pid);

	$arIn = $this->arG;
	$arIn["flgThisisCN"] =  $flgThisisCN;
	$arIn["form_id"] = $this->fid;
	$arIn["ret_proc_id"] = 1;

	return $oSuperbillSaver->save($arIn);
	//include($incdir."/chart_notes/superbill_save.php");
}

//--
function mess_sent($mess){
	$pid = $this->pid;
	$doctor_id=$_SESSION['authId'];
	$output="";
	if($mess<>"" && $doctor_id<>""){
		 $qry="select doctor_mess from patient_location where patientId='$pid' and cur_date =CURDATE()";
			$res=sqlStatement($qry);
			if(imw_num_rows($res)<=0){
					$qr="insert into patient_location set
						patientId='$pid',
						doctor_Id='$doctor_id',
						doctor_mess ='$mess',
						cur_time = CURTIME(),
						cur_date =CURDATE()";
						sqlQuery($qr);

			}else{
					$qr="update patient_location set
						patientId='$pid',
						doctor_Id='$doctor_id',
						doctor_mess ='$mess',
						cur_time = CURTIME(),
						cur_date =CURDATE()
						where patientId='$pid'
						and cur_date =CURDATE()
						";
						sqlQuery($qr);
			}
		//Check Mess for dr Initial
		$this->setReady4Dr($mess);
	}
}

function setReady4Dr($msg){

	$oUser = new User();
	$userType = $oUser->getUType(1);

	$msg = strtolower(trim($msg));
	if(!empty($msg) && !empty($_SESSION["patient"])){
		$refId = "";

		//get Facility Id
		$sql = "SELECT sa_facility_id, sa_doctor_id ".
			 "FROM schedule_appointments ".
			 "WHERE ".
			 "CURDATE() BETWEEN schedule_appointments.sa_app_start_date ".
			 "AND schedule_appointments.sa_app_end_date ".
			 "AND (schedule_appointments.status_date=CURDATE() OR schedule_appointments.status_date='0000-00-00') ".
			 "AND sa_patient_id='".$_SESSION["patient"]."'".
			 "ORDER BY schedule_appointments.sa_app_starttime ASC ".
			 "LIMIT 0,1 ";
		$row = sqlQuery($sql);
		if($row != false){
			$facId = $row["sa_facility_id"];
			$saDocId = $row["sa_doctor_id"];
		}


		if( $userType != 1 ){

			if( !empty($facId) ){
				$sql = "SELECT ".
					"DISTINCT schedule_appointments.sa_doctor_id, ".
					"users.fname,users.lname ".
					"FROM schedule_appointments ".
					"INNER JOIN users On users.id = schedule_appointments.sa_doctor_id ".
					"WHERE ".
					"CURDATE() BETWEEN schedule_appointments.sa_app_start_date ".
					"AND schedule_appointments.sa_app_end_date ".
					"AND (schedule_appointments.status_date=CURDATE() OR schedule_appointments.status_date='0000-00-00')  ".
					"AND sa_facility_id='".$facId."' ".
					"ORDER BY schedule_appointments.sa_app_starttime ASC ";
				$res = sqlStatement($sql);
				for($i=0;$row=sqlFetchArray($res);$i++){
					$drId = $row["sa_doctor_id"];
					$fName = !empty($row["fname"]) ? strtolower($row["fname"]) : "" ;
					$lName = !empty($row["lname"]) ? strtolower($row["lname"]) : "" ;
					if(($this->isMsg4DrSec($msg, $fName, $lName))){
						$refId = $drId;
						break;
					}
				}
			}

		}else if( ($userType == 1) && (!empty($saDocId)) ){
			//refer id will be doctor id
			$refId = $saDocId;
		}

		//Set
		if( (!empty($refId) && !empty($_SESSION["patient"])) || ( $userType == 1 ) ){
			$sql =  "UPDATE patient_location SET ";
			$sql .= "ready4DrId = '".$refId."' ";
			$sql .= "WHERE patientId = '".$_SESSION["patient"]."' ".
				  "AND cur_date=CURDATE() ";
			$res = sqlQuery($sql);
		}
	}
}

function isMsg4DrSec($msg, $fName, $lName ){
	if(!empty($fName)){
		$fInt = substr($fName,0,1);
		$lInt = (!empty($lName)) ? substr($lName,0,1) : "" ;
		$drInt = (!empty($lInt)) ? "".$fInt.$lInt."" : "".$fInt."";

		//if( strpos($msg, $drInt) !== false ){
		if(preg_match("/(\b".$drInt."\b)/i", $msg)){
			return true;
		}
	}
	return false;
}

//--

// If Intravitreal Injection is given, then on Finalize, Add that to Medical Hx as Administered.  Date is DOS
function addIntraVitInject($dos){

	$patient_id = $this->pid;
	$form_id = $this->fid;

	//
	if(empty($patient_id) || empty($form_id)){ return; }

	$arr_all=array();
	$sql="SELECT intravit_meds, site, corr_site FROM chart_procedures
			WHERE patient_id='".$patient_id."' AND form_id='".$form_id."' AND intravit_meds!='' AND deleted_by='0'
			ORDER BY `chart_procedures`.`id`  DESC ";
	$rez=sqlStatement($sql);
	for($i=0;$row=sqlFetchArray($rez);$i++){
		$tmp_intravit_meds = $row["intravit_meds"];
		$arr_tmp_intravit_meds=explode("|~|", $tmp_intravit_meds);

		//$arr_all=array_merge($arr_all,$arr_tmp_intravit_meds);
		$tmp_site=(!empty($row["site"])) ? $row["site"] : $row["corr_site"];
		$arr_all[]=array($arr_tmp_intravit_meds,$tmp_site);
	}

	//
	$oMedHx = new MedHx($patient_id);

	if(count($arr_all)>0){
		foreach($arr_all as $key=>$val){
			$ar_meds=$val[0];
			$site=$val[1];

			if(count($ar_meds)>0){
				foreach($ar_meds as $k1 => $v1){
					if(!empty($v1)){

						//Add Ocu Meds--

						$tmpArr=array();

						$order_site_en='';
						if(strpos($site,"OU")!==false){ $order_site_en=3; }
						if(strpos($site,"OD")!==false){ $order_site_en=2; }
						if(strpos($site,"OS")!==false){ $order_site_en=1; }
						if(strpos($site,"PO")!==false){ $order_site_en=4; }

						//it will added sysmic
						$tmpArr["type"] = (!empty($order_site_en) && $order_site_en>=1 && $order_site_en<=4) ? 4 : 1 ;
						$tmpArr["title"] = $v1;
						$tmpArr["sig"] = '';
						$tmpArr["begdate"] = $dos;
						$tmpArr["enddate"] = "";
						$tmpArr["pid"] = $patient_id;
						$tmpArr["destination"] = "";
						$tmpArr["allergy_status"] = "Administered";
						$tmpArr["med_comments"] = "";
						$tmpArr["sites"] = $order_site_en;
						$tmpArr["compliant"] = "";
						$tmpArr["qty"] = '';
						$tmpArr["refills"] = '';
						$tmpArr["ndccode"] = '';


						$oMedHx->ocuMedsSave($tmpArr);

						//Add Ocu Meds--

					}
				}
			}
		}
	}
}

//// AK:> Any procedure done from Work View->Procedure that is not Retina or Botox should go to Medical HX->Surgeries/SX.
function addProceduresInSurgerySx($dos){

	$patient_id = $this->pid;
	$form_id = $this->fid;

	//
	if(empty($patient_id) || empty($form_id)){ return; }

	//

	$oMedHx = new MedHx($patient_id);

	$arr_all=array();
	$sql="SELECT site, corr_site, operative_procedures.procedure_name, operative_procedures.ret_gl FROM chart_procedures
			INNER JOIN operative_procedures ON operative_procedures.procedure_id=chart_procedures.proc_id
			WHERE patient_id='".$patient_id."' AND form_id='".$form_id."' AND chart_procedures.deleted_by='0' ".
			/*"AND operative_procedures.procedure_name NOT LIKE '%Botox%'
			AND operative_procedures.ret_gl!='1' ".*/
			"AND operative_procedures.del_status!='1'
			ORDER BY `chart_procedures`.`id`  DESC ";
	$rez=sqlStatement($sql);
	for($i=0;$row=sqlFetchArray($rez);$i++){
		$tmp_intravit_meds = trim($row["procedure_name"]);
		if(empty($tmp_intravit_meds)){ continue;  }

		//$arr_all=array_merge($arr_all,$arr_tmp_intravit_meds);
		$tmp_site=(!empty($row["site"])) ? $row["site"] : $row["corr_site"];

		$tmp_proc_type="Other";
		if($row["ret_gl"]=="1"){$tmp_proc_type="Ret";}
		else if($row["ret_gl"]=="2"){$tmp_proc_type="GL";}

		$arr_all[]=array($tmp_intravit_meds,$tmp_site, $tmp_proc_type);
	}

	if(count($arr_all)>0){
		foreach($arr_all as $key=>$val){
			$ar_meds=$val[0];
			$site=$val[1];
			$procedure_type=$val[2];

			if(!empty($ar_meds)){
				$v1=$ar_meds;
				//foreach($ar_meds as $k1 => $v1){
					if(!empty($v1)){

						//Add Ocu Meds--

						$tmpArr=array();

						$order_site_en='';
						if(strpos($site,"OU")!==false){ $order_site_en=3; }
						if(strpos($site,"OD")!==false){ $order_site_en=2; }
						if(strpos($site,"OS")!==false){ $order_site_en=1; }
						//if(strpos($site,"PO")!==false){ $order_site_en=4; }

						//it will added sysmic
						$tmpArr["type"] = (!empty($order_site_en) && $order_site_en>=1 && $order_site_en<=3) ? 6 : 5 ;
						$tmpArr["title"] = $v1;
						$tmpArr["sig"] = '';
						$tmpArr["begdate"] = $dos;
						$tmpArr["enddate"] = "";
						$tmpArr["pid"] = $patient_id;
						$tmpArr["destination"] = "";
						$tmpArr["allergy_status"] = "Active";
						$tmpArr["med_comments"] = "";
						$tmpArr["sites"] = $order_site_en;
						$tmpArr["compliant"] = "";
						$tmpArr["qty"] = '';
						$tmpArr["refills"] = '';
						$tmpArr["ndccode"] = '';
						$tmpArr["referredby"] = '';
						$tmpArr["proc_type"] = 'procedure';
						$tmpArr["procedure_type"] = ''.$procedure_type;

						$oMedHx->ocuMedsSave($tmpArr);

						//Add Ocu Meds--

					}
				//}
			}
		}
	}
}

	function save_chart_log(){

		$oClog = new ChartLog($this->pid, $this->fid);
		$oClog->save_log($this->arG["finalize"]);

	}

	//
	function saveWV(){
		$finalize = $_POST["elem_masterFinalize"];
		$finalizedt = $_POST["elem_masterFinalDate"];
		//Check if Chart is not Finalized or User is Finalizer
		if($this->chkFinalizeStatusB4Save($finalize, $finalizedt)||!$this->checkFinalizer()){
			//if Not , stop save
			if($closePtChart == "1"){
				$this->closeThisPatientWorkView("savemaintable");
			}else{
				$arrRet=array();
				$arrRet["chartFinalized"]=1;
				$arrRet["closePtChart"]=1;
				echo json_encode($arrRet);
				//require_once("../../../interface/chart_notes/inc_save_cl.php");
				/*
				echo "<html><body>
					<font color=\"red\"><h1>This chart is Finalized by another User. So you will redirected to Work view in a 2 seconds. </h1></font>
				  <script>
					setTimeout(function(){top.core_redirect_to('".$ptGo2."','','1');}, 10);
				  </script>
				  </body></html>";
				  */
			}

			$flgCheckFinalizer = 1;
			//exit();
		}
		if(isset($flgCheckFinalizer) && !empty($flgCheckFinalizer)){ return ""; }//$flgCheckFinalizer

			$changeChartNotes = "1";
			if(!empty($_SESSION["finalize_id"])){
				$changeChartNotes = ($_POST["elem_isFormReviewable"] == "1") ? "1" : "0";
			}

			if(!isset($changeChartNotes) || empty($changeChartNotes)){ //$changeChartNotes
				return "";
			}
			$date_of_service = wv_formatDate($_POST["elem_dos"],0,0,"insert"); //getDateFormatDB($_POST["elem_dos"]); // Used in Pt Problem List also
			if(empty($date_of_service) || $date_of_service=="0000-00-00"){ $date_of_service=date('Y-m-d'); }
			$this->arG["date_of_service"] = $date_of_service;

			$form_id = $this->fid;
			$elem_utElems_cur = $_POST["elem_utElems_cur"];

			$this->saveCCHX(); //left cc history
			$this->saveProviderIssue(); //left provider issue
			$this->saveContactLens();
			//SAVING CONTACT LENS DATA

			$this->saveVision();
			$this->saveLasik();

			if(strpos($elem_utElems_cur,"elem_stereo_SecondsArc")!==false){
				$this->saveStereopsis();
			}

			//Color Vision --
			$this->saveColorVisionTest_st();
			//Color Vision --

			//W4Dot --
			$this->saveW4Dot_st();
			//W4Dot --

			$this->saveSignatures();

			$this->saveAssessments();

			$this->saveMemo();

			/*
			 * Upload Chart Note PDF data to TW - before finalize
			 * Stop finalize operation if any error encountered from TW
			*/
			if( array_key_exists('elem_masterFinalize', $_POST) && $_POST['elem_masterFinalize'] == '1' )
			{
				//Allscripts - Touch Works Integration Work - Upload Chart Note PDF & save Asessments (Problems) to TouchWorks
				if( is_allscripts() && $this->pid !== '' ){

					$_SESSION['as_log_cn_dos'] = $this->arG["date_of_service"];
					$_SESSION['as_log_cn_id'] = $this->fid;

					$GLOBALS['rethrow'] = true;
					include_once( $GLOBALS['srcdir'].'/allscripts/as_dictionary.php' );
					include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );

					try
					{
						$asPtId = false;
						$asIdSql = "SELECT `as_id`, fname, lname FROM `patient_data` WHERE `id`=".( (int)$this->pid )." AND `External_MRN_4`!=''";
						$asIdSql = imw_query( $asIdSql );
						$asFname = $asLname = '';
						if( $asIdSql && imw_num_rows($asIdSql) > 0 )
						{
							$asPtId1 = imw_fetch_assoc( $asIdSql );
							$asPtId = $asPtId1['as_id'];
							$asFname = $asPtId1['fname'];
							$asLname = $asPtId1['lname'];
						}

						if( $asPtId )
						{
							###################	List Assessment for the Chart Note ###################
							include_once( $GLOBALS['srcdir'].'/classes/work_view/ChartAP.php' );

							/*$cnId = (isset($_SESSION["form_id"]))? $_SESSION["form_id"] : $_SESSION['finalize_id'];*/
							$cnId = $form_id;

							$chartXml = new ChartAP($this->pid, $cnId);
							$savedAssessments = $chartXml->getVal();
							$savedAssessments = $savedAssessments['data']['ap'];
							################### End Assessment Listing for the Chart Note ###################

							/*Fetch Data from AllScripts*/
							$patientObj = new as_patient();
							$twEncounterData1 = array();
							$asDocumentId = '0';
							$getEncId = "SELECT `as_encounterId`, `as_date_time`, `as_document_ids` FROM `chart_master_table` WHERE id='".$cnId."' AND `as_encounterId`!=''";
							$resEncId = imw_query($getEncId);
							if( imw_num_rows($resEncId)>0 )
							{
								$rowEncId = imw_fetch_assoc($resEncId);
								$twEncounterData1['encounterDate'] = $rowEncId['as_date_time'];
								$twEncounterData1['EncounterID'] = $rowEncId['as_encounterId'];
								$asDocumentId = trim($rowEncId['as_document_ids']);
							}
							else
							{
								if( array_key_exists('tw_encounter_id', $_SESSION) && $_SESSION['tw_encounter_id'] !== '' && $_SESSION['tw_encounter_id'] != '0' )
								{
									$twEncounterData1['encounterDate'] = '';
									$twEncounterData1['EncounterID'] = $_SESSION['tw_encounter_id'];
								}
								else
								{
									$twEncounterData = $patientObj->encounter('FORM',$asPtId, $this->arG["date_of_service"]);
									$twEncounterData = $twEncounterData[0];

									$twEncounterData1['encounterDate'] = $twEncounterData->encounterDate;
									$twEncounterData1['EncounterID'] = $twEncounterData->EncounterID;

									$_SESSION['tw_encounter_id'] = $twEncounterData1['EncounterID'];
								}

								$sql = "UPDATE `chart_master_table` SET `as_encounterId`='".$twEncounterData1['EncounterID']."', `as_date_time`='".$twEncounterData1['encounterDate']."' WHERE `id`='".$cnId."'";
								imw_query($sql);
							}

						/*Save New Assessment (Problems) to TW from Assessment*/

							if( $savedAssessments )
							{
								###################	Process Assessment List for the Chart Note ###################
								$patientObj->filterAssessmentProblems($savedAssessments);

								$assessment = false;
								$tempAsId = false;
								foreach($savedAssessments as &$assessment)
								{
									$asId= '';
									$asStatus = 'Active';
									$onsetDate = '';
									$sql = 'SELECT `as_id`, `onset_date`, `status` FROM `pt_problem_list` WHERE `id`='.(int)$assessment['problemId'].' AND `pt_id`='.$this->pid;
									$resp = imw_query($sql);
									if($resp && imw_num_rows($resp)==1)
									{
										$resp = imw_fetch_assoc($resp);
										$asId = $resp['as_id'];
										$asStatus = $resp['status'];
										$onsetDate = $resp['onset_date'];
									}

									$tempAsId = $asId;
									$tempAsId = json_decode($tempAsId);

									foreach( $assessment['dxCode'] as $dxCodeKey => $dxCodeValue )
									{
										if( is_object($tempAsId) && property_exists($tempAsId, $dxCodeValue) )
											$assessment['asId'][$dxCodeKey] = $tempAsId->{$dxCodeValue};
										else
											$assessment['asId'][$dxCodeKey] = '0';
									}

									$assessment['status'] = $asStatus;
									$assessment['onsetDate'] = $onsetDate;
								}
								unset($assessment, $tempAsId);
								###################	End Assessment List processing ###################

								################### Save Problems in TouchWorks ###################
								foreach($savedAssessments as $assessmentData)
								{
									$tempAssessmentData = $assessmentData;

									$twResp = array();
									$imwId = $tempAssessmentData['problemId'];

									do
									{
										$assessmentData['dxCode'] = array_shift($tempAssessmentData['dxCode']);
										$assessmentData['asId'] = array_shift($tempAssessmentData['asId']);

										$problemResp = $patientObj->saveProblem( $assessmentData, $asPtId );

										if( strtolower($problemResp->Status) !== 'success' )
											throw new asException( 'Error', $problemResp->status);

										/*Response ID to be updated in DB*/
										$twResp[$assessmentData['dxCode']] = $problemResp->transid;
									}
									while ( count($tempAssessmentData['dxCode']) > 0 );

									/*Update Problem Id in iDoc if new Problem Dated*/
									if( $imwId )
									{
										$sqlProblemUpdate = "UPDATE `pt_problem_list` SET `as_id`='".json_encode($twResp)."' WHERE id=".$imwId;
										imw_query($sqlProblemUpdate);
									}

									unset($tempAssessmentData, $twResp, $imwId);
								}
								################### End Problems saving in TouchWorks ###################
							}
						/*End Saving New Assessment (Problems) to TW from Assessment*/

							$params = array('cnId'=>$cnId, 'session'=>serialize($_SESSION));
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $GLOBALS['php_server'].'/library/allscripts/create_chartnote_pdf.php');
							curl_setopt($ch, CURLOPT_POST, true);	/*Reset HTTP method to GET*/
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /*Return the response*/
							curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP); /*Set protocol to HTTP if default changed*/
							curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($ch, CURLOPT_HEADER, false); /*Include header in Output/Response*/
							$fileName = curl_exec($ch); /*$data will hold data returned from FramesData API*/
							$error = curl_error($ch);
							curl_close($ch); /*Close curl session/connection*/
							//$fileName = trim($fileName).'.pdf';

							/*Create Procedure Note PDF*/
							include_once( $GLOBALS['srcdir'].'/allscripts/create_procedureNote_pdf.php' );
							$procedure_note_pdf = crate_procedure_note_pdf($this->pid, $cnId);
							// Check as document id for procedure note.
							$asProcDocId = '0';
							$getAsDocId = "SELECT as_document_id FROM chart_procedures WHERE patient_id = ".$this->pid." AND form_id = ".$cnId." ORDER BY id DESC LIMIT 1";
							$resultAsDocId = imw_query($getAsDocId);
							if( imw_num_rows($resultAsDocId) > 0 )
							{
								$rowAsDocId = imw_fetch_assoc($resultAsDocId);
								$asProcDocId = trim($rowAsDocId['as_document_id']);
							}

							if(file_exists($fileName) || file_exists($procedure_note_pdf) ){

								/*Check Document Type*/
								$dictionary = new as_dictionary();
								$document_types = $dictionary->document_types();
								$documentType = '';

								foreach( $document_types as $document )
								{
									$entryCode = trim($document->EntryCode);

									/*Set Document type to the first entry with manifestation Tiff*/
									if(	$documentType == '' &&
										strtolower( trim($document->physicalmanifestationcode) ) == 'tif' &&
										strtolower( trim($document->Active) ) == 'y'
									  )
										$documentType = $entryCode;
									/*Set Document type to the first entry with manifestation Tiff*/

									if( strtolower($entryCode) == 'imw_vn' )
									{
										if( strtolower( trim($document->physicalmanifestationcode) ) == 'tif' &&
											strtolower( trim($document->Active) ) == 'y'
										  )
										{
											$documentType = $entryCode;
											break;
										}
									}
								}

								if( trim($documentType) == '' )
								{
									throw new asException('docType', 'type does not exists');
								}
								/*End Check Document Type*/

								/*Upload the Chart Note document to PDF*/
								if( file_exists($fileName) )
								{
									$pathInfo = pathinfo($fileName, PATHINFO_BASENAME );
									$data = $patientObj->uploaddocument($pathInfo,$fileName,$asFname,$asLname,$asPtId,$twEncounterData1,$documentType,$asDocumentId);

								}

								if( $data !== false )
								{
									if( (string)$data !== (string)$asDocumentId )
									{
										$data = trim($data);
										$sqlDocId = "UPDATE `chart_master_table` SET `as_document_ids`='".$data."' WHERE `id`='".$cnId."'";
										imw_query($sqlDocId);
									}
								}
								else{
									//throw new asException( 'Alert', 'Document not uploaded to Touch Works.' );
									throw new asException('docType', 'Document not uploaded to Touch Works.');
								}

								/*Upload the Chart Note document to PDF*/
								if( file_exists($procedure_note_pdf) )
								{
									$pathInfo = pathinfo( $procedure_note_pdf, PATHINFO_BASENAME );
									$responseProcedureUpload = $patientObj->uploaddocument($pathInfo,$procedure_note_pdf,$asFname,$asLname,$asPtId,$twEncounterData1,$documentType,$asProcDocId);
								}

								if( $responseProcedureUpload !== false )
								{
									if( (string)$responseProcedureUpload !== (string)$asProcDocId )
									{
										$responseProcedureUpload = trim($responseProcedureUpload);
										$sqlDocId = "UPDATE `chart_procedures` SET `as_document_id`='".$responseProcedureUpload."' WHERE patient_id = ".$this->pid." AND form_id = ".$cnId."";
										imw_query($sqlDocId);
									}
								}
								else{
									throw new asException('docType', 'Procedure document not uploaded to Touch Works.');
								}

							}
							else
								throw new asException( 'Alert', 'Unable to create chart Note PDF.' );
						}
					}
					catch( asException $e)
					{
						/*
						 * Unset finalize flag to stop finalize operation
						*/
						$_POST['elem_masterFinalize'] = '0';

						$arrRet = array();
						$arrRet['as_msg'] = $e->getErrorText();

						$pos1 = strpos($arrRet['as_msg'], 'Invalid');
						$pos2 = strpos($arrRet['as_msg'], 'Parameter name');

						if( $e->getErrorType() == 'docType' )
						{
							$arrRet['as_msg'] = "Document type <strong>imwemr Visit Note</strong> does not exists.<br/>Please contact TouchWorks support to create the document type.";
						}
						elseif( $pos2 !== false || $pos1 !== false )
						{
							$arrRet['as_msg'] .= "<br><br>Please contact imwemr support.";
						}
					}

					/*Unset the object*/
					if( isset($patientObj) && is_object($patientObj) )
						unset($patientObj);

					/*Unset TW Logging Session Data*/
					if( array_key_exists('as_log_cn_dos', $_SESSION) )
						unset($_SESSION['as_log_cn_dos']);

					if( array_key_exists('as_log_cn_id', $_SESSION) )
						unset($_SESSION['as_log_cn_id']);
				}
			}
			/*
			 * End - Upload Chart note PDF data to Allscripts
			*/

            /*
             * Start upload ICD10 and CPT Codes to Dss
             * //Superbill upload to VistA that includes ICD 10 Codes, CPT Codes
             */
            if (isDssEnable() && array_key_exists('elem_masterFinalize', $_POST) && $_POST['elem_masterFinalize'] == '1') {
                $cpt_return=array();
                $icd_return=array();
                /*  Start DSS API call to addCPTCodeToVisit to end point ('DSIHTE/PCE_AddCPTCodeToVisit')
                  Start Creating parameter to make DSS API call to upload CPT codes and ICD10 codes to vista * */

                $sqlrs=imw_query('select External_MRN_5 from patient_data where id='.$this->pid.' ');

                $dss_row=imw_fetch_assoc($sqlrs);
                $dss_dfn=$dss_row['External_MRN_5'];

                if ($dss_dfn && $dss_dfn!= NULL && $dss_dfn!='') {
                    $elem_mxSBId = $_POST["elem_mxSBId"];
                    $dss_cptStrArr = array();
                    $dss_cptMdArr=array();
                    for ($i=1; $i<=$elem_mxSBId; $i++) {
                        $varCpt = "elem_cptCode_".$i;
                        $varProcUnits = "elem_procUnits_".$i;
                        if (isset($_POST[$varCpt]) && !empty($_POST[$varCpt])) {

                            $cptCode = sqlEscStr($_POST[$varCpt]);

                            $cpt_sql = "select cpt4_code from cpt_fee_tbl where cpt_prac_code='".$cptCode."' and delete_status=0 ";
                            $cpt_sql_rs = imw_query($cpt_sql);
                            $cpt_code_row = imw_fetch_assoc($cpt_sql_rs);
                            $cpt4_code = $cpt_code_row['cpt4_code'];
                            if (empty($cpt4_code))
                                $cpt4_code = $cptCode;

                            $units = $_POST[$varProcUnits];
                            $units = (empty($units)) ? "1" : $units;


                            for ($j=1; $j<=4; $j++) {
                                if ($j<=3) {
                                    $elemMdCodes = $_POST["elem_modCode_".$i."_".$j];
                                    if (isset($elemMdCodes) && !empty($elemMdCodes)) {
                                        $arrMd[] = $elemMdCodes; //Md
                                    }
                                }
                            }

                            //make array to Check CPT code and its modifiers exists in vista
                            $dss_cptMdArr[$cptCode]=$arrMd;

                            $dssstrMd = implode('^', $arrMd);
                            unset($arrMd);
                            if ($dssstrMd) {
                                $dssstrMd = '^'.$dssstrMd;
                            }

                            //CPTCODE1~CPTIFN1~CPTQUANTITY1^CPT1MODIFIER^CPT1MODIFIER2^etc
                            $dss_cptStrArr[] = $cptCode.'~'.$cpt4_code.'~'.$units.$dssstrMd;
                        }
                    }


                    /** Start Creating parameter to make DSS API call to upload ICD10 codes to vista * */
                    $dss_DxCodes_arr = array();
                    for ($i = 1; $i <= 12; $i++) {
                        $varDxCodes = "elem_dxCode_".$i;
                        if ($_POST[$varDxCodes] != '') {
                            $arrDxCodes[$i] = $_POST[$varDxCodes];

                            //ICDCODE1~ICDIFN1~Primary Flag1;ICDCODE2~ICDIEN2^PRIMARY FLAG2;ICDCODE3~ICDIEN3^PRIMARY FLAG3
                            $dss_DxCodes_arr[] = $_POST[$varDxCodes]; //.'~~'.$i;
                        }
                    }

                    //ICDCODE1~ICDIFN1~Primary Flag1;ICDCODE2~ICDIEN2^PRIMARY FLAG2;ICDCODE3~ICDIEN3^PRIMARY FLAG3
                    $dss_DxCodes = '';
                    if (empty($dss_DxCodes_arr) == false)
                        $dss_DxCodes = implode(';', $dss_DxCodes_arr);
                    /** Ends Creating parameter to make DSS API call to upload ICD10 codes to vista * */

                    // $dss_elem_dos = date('Y-m-d',strtotime( str_replace("-","/",$_POST['elem_dos']) ));
                    // $dss_elem_dos = str_replace("-","/",$_POST['elem_dos']);
                    $dss_elem_dos = date('Y-m-d',strtotime($_POST['elem_dos']));
                    $dss_time=date('H:i');

                    include_once($GLOBALS['srcdir']."/dss_api/dss_enc_visit_notes.php");
                    $objDss_enc_vn = new Dss_enc_visit_notes();

                    $dss_loginDUZ='';
                    if(isset($_SESSION['dss_loginDUZ']) && $_SESSION['dss_loginDUZ']!='')
                        $dss_loginDUZ=$_SESSION['dss_loginDUZ'];

                    $dss_location="62";
                    if(isset($_SESSION['dss_location']) && $_SESSION['dss_location']!='')
                        $dss_location=$_SESSION['dss_location'];


                    // API Call to convert Date time to Fileman format.
                    $fileman_dos = $sbfileman_dos='';
					if(empty($dss_elem_dos) == false || $dss_elem_dos != '') {
                        try
                        {
                            // $dparm=array();
                            // $dparm['date']=$dss_elem_dos;
                            // $dparm['time']=$dss_time;

                            //$date_format = $objDss_enc_vn->MISC_DSICDateConvert($dss_elem_dos);
                            // $date_format1 = $objDss_enc_vn->ConvertDisplayDateTimeToVistADateTime($dparm);
                            // $sbfileman_dos=$fileman_dos = $date_format1['output'];

                            $sbfileman_dos=$fileman_dos = $objDss_enc_vn->convertToFileman($dss_elem_dos.' '.$dss_time);

                            //$sbfileman_dos = $date_format['fileman'];
                        } catch(Exception $e){
                            //$arrRet = array('status'=>'error','data'=>$e->getMessage());
                            $arrRet['dss_error'][]=$e->getMessage();
                        }
                    }

                    if (empty($dss_cptStrArr) == false || $dss_DxCodes != '') {
                        $checkparms=array();
                        $returnCodeArr=array();
                        if(empty($dss_cptMdArr)==false || empty($dss_DxCodes_arr)==false) {
                            $checkparms['cpt']=$dss_cptMdArr;
                            $checkparms['icd10']=$dss_DxCodes_arr;

                            //Check CPT COde and ICD10 code exists in vista
                            $returnCodeArr=$this->check_cpt_and_icd_codes($sbfileman_dos,$objDss_enc_vn,$checkparms);
                        }

                        if(empty($returnCodeArr)==true) {

                            //API Call to get visit and appointment from vista
                            $visit='';
                            if (empty($fileman_dos) == false) {
                                $vdata = array();
                                $vdata['patient'] = $dss_dfn;
                                $vdata['appointment'] = $fileman_dos;
                                $vdata['clinic'] = $dss_location;

                                try
                                {
                                    $newvisit = $objDss_enc_vn->getVisitIFN($vdata);
                                    if(isset($newvisit['visit'])){
                                        $visit=$newvisit['visit'];
                                    }else{
                                        $visit=$newvisit;
                                    }
                                } catch(Exception $e){
                                    $arrRet['dss_error'][]=$e->getMessage();
                                }

                                //$visit=$visit;
                            }

                            if((!isset($newvisit['visit']) && isset($visit['visitIen']) && $visit['visitIen']=='-1' && isset($visit['message']) && $visit['message']=='No Visit ID') || $visit=='') {
                                $cdata = array();
                                $cdata['patient'] = $dss_dfn;
                                $cdata['appointmentDateTime'] = $fileman_dos;
                                $cdata['location'] = $dss_location;
                                $cdata['provider'] = $dss_loginDUZ;

                                try
                                {
                                    $newvisit = $objDss_enc_vn->createNewPCEVisit($cdata);
                                    $visit=$newvisit['visit'];
                                } catch(Exception $e){
                                    $arrRet['dss_error'][]=$e->getMessage();
                                }
                            }



                            $params = array();
                            $params['patient'] = $dss_dfn;
                            $params['visit'] = $visit;
                            $params['apptDateTime'] = $sbfileman_dos;
                            $params['location'] = $dss_location;

                            if (empty($params) == false) {
                                // API Call to upload CPT codes to vista
                                if (empty($dss_cptStrArr) == false) {
                                    $params['cpts'] = $dss_cptStrArr;
                                    try {
                                        $cpt_return = $objDss_enc_vn->addCPTCodeToVisit($params);
                                    } catch(Exception $e){
                                        //$arrRet = array('status'=>'error','data'=>$e->getMessage());
                                        $arrRet['dss_error'][]=$e->getMessage();
                                    }
                                }

                                // API Call to upload ICD10 code to vista
                                if ($dss_DxCodes != '') {
                                    unset($params['cpts']);
                                    $params['icds'] = $dss_DxCodes;
                                    try {
                                        $icd_return = $objDss_enc_vn->addICDCodeToVisit($params);
                                    } catch(Exception $e){
                                        //$arrRet = array('status'=>'error','data'=>$e->getMessage());
                                        $arrRet['dss_error'][]=$e->getMessage();
                                    }
                                }

                            }


                            if((!isset($visit['visitIen']) && !isset($visit['message'])) && $visit!='') {
                                if(!isset($icd_return['resultCode']) && !isset($cpt_return['resultCode']) && isset($icd_return[0]['visit']) && isset($cpt_return[0]['visit']) && $icd_return[0]['visit']!='-1' && $cpt_return[0]['visit']!='-1' && $icd_return[0]['visit'] == $cpt_return[0]['visit']) {
									$arrRet['dss_error'][]='Superbill added to DSS successfully.';
								} else {
									/*
									* Unset finalize flag to stop finalize operation
									*/
									$_POST['elem_masterFinalize'] = '0';

									$arrRet['dss_error'][]='Unable to upload ICD and CPT Codes to VISTA.';
									//echo $visit['message'];
								}
                            } else {
                                /*
                                * Unset finalize flag to stop finalize operation
                                */
                                $_POST['elem_masterFinalize'] = '0';

                                $arrRet['dss_error'][]=$visit['message'];
                                //echo $visit['message'];
                            }


                        } else {
                            /*
                            * Unset finalize flag to stop finalize operation
                            */
                            $_POST['elem_masterFinalize'] = '0';

                            $arrRet['dss_error'][]=$returnCodeArr;
                            //echo ' '.implode('<br>',$returnCodeArr).' ';
                            exit();

                        }

                    }

                    // Send tiu note to dss
                	if($_POST['elem_masterFinalize'] == 1) {
	                    $ptData = "";
						$anpData ="";
						$consult_data = "";

					 	// PATIENT DATA + CHIEF COMPLAINT WORK STARTS HERE
						$ptdataQry = "SELECT pd.id, pd.title, concat(pd.fname,', ',pd.lname) as patientName, pd.DOB, pd.sex,pd.street, pd.street2, pd.city, pd.state, pd.postal_code, pd.phone_home, clc.ccompliant FROM patient_data as pd INNER JOIN chart_left_cc_history as clc ON clc.patient_id = pd.id WHERE clc.form_id = '".$_SESSION['form_id']."' and clc.patient_id = '".$this->pid."'";

						$ptdataRes =imw_query($ptdataQry);
						if(imw_num_rows($ptdataRes)>0)
						{
							$ptdataRow = imw_fetch_assoc($ptdataRes);
							$pt_id = $ptdataRow['id'];
							$pt_title = $ptdataRow['title'];
							$pt_patientName = str_ireplace("'", "`", $ptdataRow['patientName']);
							$pt_DOB = $ptdataRow['DOB'];
							$pt_sex = $ptdataRow['sex'];
							$pt_street = $ptdataRow['street'];
							$pt_street2 = $ptdataRow['street2'];
							$pt_city = $ptdataRow['city'];
							$pt_state = $ptdataRow['state'];
							$pt_postal_code = $ptdataRow['postal_code'];
							$pt_phone_home = $ptdataRow['phone_home'];
							$pt_ccompliant = $ptdataRow['ccompliant'];
							//====\r - BASICALLY USED FOR MAC AND IN CASE OF WINDOW
							//====\n USED TO ADD NEWLINE
							$ptData .= "Patient TIU Details\r\n";
							$ptData .= "\r\nPatient Name: ".$pt_title." ".$pt_patientName." - ".$pt_id;
							$ptData .= "\r\nGender: ".$pt_sex."  DOB: ".date('M d, Y', strtotime($pt_DOB));
							$ptData .= "\r\nAddress: ".$pt_street;
							$ptData .= "\r\nCity/State/Zip: ".$pt_city."  ".$pt_state." - ".$pt_postal_code;
							$ptData .= "\r\nPhone: ".$pt_phone_home;

							if(!empty($pt_ccompliant))
							{

								$placeholders = array('>o','<o','<1', '<2', '<3', '<4','<5', '<6', '<7', '<8','<9','<0');
								$repLacevals = array('&gt;o','&lt;o','&lt;1', '&lt;2', '&lt;3', '&lt;4','&lt;5', '&lt;6', '&lt;7', '&lt;8','&lt;9','&lt;0');
								$pt_ccompliant = str_ireplace($placeholders, $repLacevals,$pt_ccompliant);

								$ptData .= "\r\n\r\nChief Complaint \r\n".$pt_ccompliant;
							}
						}
						//================WORK END==========================

						//====ASSESSMENT AND PLANS DATA WORK START HERE=====
						$anpDataQry = "SELECT cap.patient_id, cap.form_id, cpa.id_chart_ap, cpa.assessment, cpa.plan, cpa.eye FROM chart_pt_assessment_plans as cpa INNER JOIN chart_assessment_plans as cap ON cap.id = cpa.id_chart_ap WHERE cap.form_id = '".$_SESSION['form_id']."' AND cap.patient_id = '".$this->pid."' AND cpa.delete_by = 0";

						$anpDataRes = imw_query($anpDataQry);

						if(imw_num_rows($anpDataRes) > 0){
							$anpData .= "\r\nAssessment and Plan\r\n\r\n";
							while($anpDataRow = imw_fetch_assoc($anpDataRes))
							{
								$anpData .= "Assessment: ".strip_tags($anpDataRow['assessment'])."\r\n";
								$anpData .= "Site: ".strip_tags($anpDataRow['eye'])."\r\n";
								$anpData .= "Plan: ".strip_tags($anpDataRow['plan'])."\r\n\r\n";
							}

							// $placeholders = array('\'','>o','<o','<1', '<2', '<3', '<4','<5', '<6', '<7', '<8','<9','<0');
							// $repLacevals = array('','&gt;o','&lt;o','&lt;1', '&lt;2', '&lt;3', '&lt;4','&lt;5', '&lt;6', '&lt;7', '&lt;8','&lt;9','&lt;0');
							// $anpData = str_ireplace($placeholders,$repLacevals,$anpData);
							$anpData = preg_replace('/[^`~!@$?a-zA-Z0-9_{}: #%\[\]%&\\r\\n\\\\]/s','',$anpData);
						}
						$consult_data = $ptData.$anpData;

						$queryTiuCheck = imw_query("SELECT id, consult_data, status FROM dss_tiu WHERE patient_id = '".$this->pid."' AND form_id = '".$_SESSION['form_id']."' ORDER BY id desc LIMIT 1");
						if(imw_num_rows($queryTiuCheck) == 0) {
							// Create TIU if not exist for the same patient and form id

							$query3 = "INSERT INTO `dss_tiu` SET patient_id = '".$this->pid."',form_id = '".$_SESSION['form_id']."', consult_data = '".$consult_data."', status = 0, created_at = '".date('Y-m-d H:i:s')."'";
							imw_query($query3);
							$last_id = imw_insert_id();

							//================WORK END==========================

							if($last_id != 0 && $last_id != ""){
			                    $vcode = isset($_SESSION['vcode']) ? base64_decode($_SESSION['vcode']) : '';
			                    $tiuTitle = isset($_POST['dssTiuTitle']) ? $_POST['dssTiuTitle'] : '';
			                    $tiu = array(
									'patientDFN' => $dss_dfn,
									'locationIEN' => $dss_location,
									'providerDUZ' => $dss_loginDUZ,
									'noteDateTime' => $fileman_dos,
									'appointmentDT' => $fileman_dos,
									'electronicSignature' => $vcode,
									'title' => $tiuTitle,
			                    );
			 					$err = 0;
								foreach ($tiu as $key => $t) {
								    if(empty($t)) $err++;
								}
								if($err == 0) {
									$query = imw_query("SELECT id, consult_data FROM dss_tiu WHERE patient_id = '".$this->pid."' AND form_id = '".$_SESSION['form_id']."' ORDER BY id desc LIMIT 1");
									if(imw_num_rows($query) > 0) {
										$data = imw_fetch_assoc($query);
										$tiu['texts'] = array($data['consult_data']);
									    try
									    {
											$tiu_record = $objDss_enc_vn->dssCreateTiuRecord($tiu);
											unset($_SESSION['vcode']); // Unset dss e-signature
											if($tiu_record[0]['code'] != -1 && $tiu_record[0]['message'] == 'SUCCESS') {
												// Update the status for tiu note
												imw_query("UPDATE dss_tiu SET status = 1 WHERE id = ".$data['id']."");

												// PCE_SaveEncounterForNote

											} else {
												throw new Exception("TIU creation failed on dss. Please contact developer to verify..");
											}
										} catch(Exception $e) {
											$_POST['elem_masterFinalize'] = '0';
											$arrRet['dss_error'][] = $e->getMessage();
											//echo $e->getMessage();
										}
									}
								} else {
									$_POST['elem_masterFinalize'] = '0';
									$msg="Missing dss required data to create TIU Note. Please contact developer to verify.";
									$arrRet['dss_error'][] = $msg;
									//echo $msg;
								}
							} else {
								$_POST['elem_masterFinalize'] = '0';
								$msg="Tiu data not created for dss. Please contact developer to verify.";
		                        $arrRet['dss_error'][] = $msg;
		                        //echo $msg;
							}
						} else {
							$tiu_edata = imw_fetch_assoc($queryTiuCheck);
							$tid = $tiu_edata['id'];
							if($tiu_edata['status'] == 0) {

								$updateTiuSql = "UPDATE `dss_tiu` SET consult_data = '".$consult_data."', modified_at = '".date('Y-m-d H:i:s')."' WHERE id = ".$tid;
								$r = imw_query($updateTiuSql);

								$vcode = isset($_SESSION['vcode']) ? base64_decode($_SESSION['vcode']) : '';
			                    $tiuTitle = isset($_POST['dssTiuTitle']) ? $_POST['dssTiuTitle'] : '';
			                    $tiu = array(
									'patientDFN' => $dss_dfn,
									'locationIEN' => $dss_location,
									'providerDUZ' => $dss_loginDUZ,
									'noteDateTime' => $fileman_dos,
									'appointmentDT' => $fileman_dos,
									'electronicSignature' => $vcode,
									'title' => $tiuTitle,
			                    );
			 					$err = 0;
								foreach ($tiu as $key => $t) {
								    if(empty($t)) $err++;
								}
								if($err == 0) {
									$query = imw_query("SELECT id, consult_data FROM dss_tiu WHERE patient_id = '".$this->pid."' AND form_id = '".$_SESSION['form_id']."' ORDER BY id desc LIMIT 1");
									if(imw_num_rows($query) > 0) {
										$data = imw_fetch_assoc($query);
										$tiu['texts'] = array($data['consult_data']);
									    try
									    {
											$tiu_record = $objDss_enc_vn->dssCreateTiuRecord($tiu);
											unset($_SESSION['vcode']); // Unset dss e-signature
											if($tiu_record[0]['code'] != -1 && $tiu_record[0]['message'] == 'SUCCESS') {
												// Update the status for tiu note
												imw_query("UPDATE dss_tiu SET status = 1 WHERE id = ".$data['id']."");

												// PCE_SaveEncounterForNote

											} else {
												throw new Exception("TIU creation failed on dss. Please contact developer to verify..");
											}
										} catch(Exception $e) {
											$_POST['elem_masterFinalize'] = '0';
											$arrRet['dss_error'][] = $e->getMessage();
											//echo $e->getMessage();
										}
									}
								} else {
									$_POST['elem_masterFinalize'] = '0';
									$msg="Missing dss required data to create TIU Note. Please contact developer to verify.";
									$arrRet['dss_error'][] = $msg;
									//echo $msg;
								}
							}
						}
						// end tiu note to dss
					}
	            }

            }
            /*
             * End upload ICD10 and CPT Codes to Dss
             */

			$this->saveMaster();
			$finalize = $this->arG["finalize"];
			$finalizerId = $this->arG["finalizerId"];
			$elem_masterFinalDate = $this->arG["elem_masterFinalDate"];
			$this->saveObjNote();

			$sb_arrCurProcIds = $this->saveSuperBill();
			$this->HL7Actions();

			$this->saveRole();

			$this->save_chart_log();

			//All Scripts - Touch Works Integration - Save Charges
			if( is_allscripts() && $this->pid !== '' ){
				$GLOBALS['rethrow'] = true;
				include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );

				try
				{
					$asPtId = ( isset($_SESSION['as_id']) ) ? $_SESSION['as_id'] : false;

					if( $asPtId )
					{
						$patientObj = new as_patient();

						$encounterData = array();

						$getEncId = "SELECT `idSuperBill`, `as_encounter_id`, as_date_time FROM `superbill` WHERE `encounterId`='".$_SESSION["encounter_id"]."'";
						$resEncId = imw_query($getEncId);
						if( imw_num_rows($resEncId)>0 )
						{
							$rowEncId = imw_fetch_assoc($resEncId);
							$encounterData['encounterDate'] = $rowEncId['as_date_time'];
							$encounterData['EncounterID'] = $rowEncId['as_encounter_id'];
							$encounterData['superbill'] = $rowEncId['idSuperBill'];
						}

						if(
							(
								!array_key_exists('EncounterID', $encounterData) || $encounterData['EncounterID'] == ''
							)
							&&(
								array_key_exists('superbill', $encounterData) && $encounterData['superbill'] != ''
							)
						)
						{
							if( array_key_exists('tw_encounter_id', $_SESSION) && $_SESSION['tw_encounter_id'] !== '' && $_SESSION['tw_encounter_id'] != '0' )
							{
								$encounterData['encounterDate'] = '';
								$encounterData['EncounterID'] = $_SESSION['tw_encounter_id'];
							}
							else
							{
								$twEncounterData = $patientObj->encounter('NonAppt',$asPtId);
								$twEncounterData = $twEncounterData[0];

								$encounterData['encounterDate'] = $twEncounterData->encounterDate;
								$encounterData['EncounterID'] = $twEncounterData->EncounterID;

								$_SESSION['tw_encounter_id'] = $encounterData['EncounterID'];
							}

							$sql = "UPDATE `superbill` SET `as_encounter_id`='".$encounterData['EncounterID']."', `as_date_time`='".$encounterData['encounterDate']."' WHERE `idSuperBill`='".$encounterData['superbill']."'";
							imw_query($sql);
						}

						if( array_key_exists('superbill', $encounterData) )
						{

							/*Fetch and Save Charges*/
							$sqlChargesList = "SELECT `pi`.`id`, `pi`.`cptCode`, CONCAT(`pi`.`dx1`, ',', `pi`.`dx2`, ',', `pi`.`dx3`, ',', `pi`.`dx4`, ',', `pi`.`dx5`, ',', `pi`.`dx6`, ',', `pi`.`dx7`, ',', `pi`.`dx8`, ',', `pi`.`dx9`, ',', `pi`.`dx10`, ',', `pi`.`dx11`, ',', `pi`.`dx12`) AS 'dx_codes', `pi`.`units`, `pi`.`modifier1`, `pi`.`modifier2`, `pi`.`modifier3`, `pi`.`as_charge_id`, `cpt`.`cpt4_code` FROM `procedureinfo` `pi` LEFT JOIN `cpt_fee_tbl` `cpt` ON(`pi`.`cptCode` = `cpt`.`cpt_prac_code` AND `cpt`.`delete_status` = 0)   WHERE `pi`.`idSuperBill`='".$encounterData['superbill']."' AND `pi`.`delete_status`=0";
							$respChargesList = imw_query($sqlChargesList);

							if( $respChargesList && imw_num_rows($respChargesList) > 0 )
							{
								while( $chargeRow = imw_fetch_assoc($respChargesList) )
								{
									/*Send CPT Code instead for CPT Prac Code*/
									if( !empty($chargeRow['cpt4_code']) )
									{
										$chargeRow['cptCode'] = $chargeRow['cpt4_code'];
									}

									$modifiers = array();
									if( $chargeRow['modifier1'] != '' )
										array_push( $modifiers, $chargeRow['modifier1'] );
									if( $chargeRow['modifier2'] != '' )
										array_push( $modifiers, $chargeRow['modifier2'] );
									if( $chargeRow['modifier3'] != '' )
										array_push( $modifiers, $chargeRow['modifier4'] );
									$modifiers = implode(',', $modifiers);

									/*Diagnosis Ids*/
									$diagnosisIds = array();
									$dx_codes = explode(',', $chargeRow['dx_codes']);
									foreach($dx_codes as $dx_code)
									{
										if( $dx_code != '' )
										{
											/* Code updated on 12/11/2018 by Pankaj Sood, as per the changes mail 10/11/2018 */
											array_push( $diagnosisIds, 'M^'.$dx_code );
										}
									}
									$diagnosisIds = implode(',', $diagnosisIds);

									try
									{
										$cahrgeResp = $patientObj->saveCharge( $chargeRow['cptCode'], $modifiers, $diagnosisIds, $chargeRow['units'], date('m-d-Y'), $encounterData['EncounterID'], $chargeRow['as_charge_id'] );

										/*Save/Update response Charge ID - Only on successfull Call*/
										$sqlResp = "UPDATE `procedureinfo` SET `as_charge_id`='".$cahrgeResp->ChargeID."' WHERE `id`='".$chargeRow['id']."'";
										imw_query($sqlResp);
									}
									catch( asException $e)
									{
										if( !isset($arrRet) || !is_array($arrRet)  )
												$arrRet = array();

										if( strcasecmp($e->getErrorText(), 'ChargeCodeDE is invalid') == 0 )
										{
											if( !isset($arrRet['as_invalid_cpt']) || !is_array($arrRet['as_invalid_cpt'])  )
												$arrRet['as_invalid_cpt'] = array();

											array_push($arrRet['as_invalid_cpt'], $chargeRow['cptCode']);
										}
										else
										{
											if( !isset($arrRet['as_unsaved_cpt']) || !is_array($arrRet['as_unsaved_cpt'])  )
												$arrRet['as_unsaved_cpt'] = array();

											array_push($arrRet['as_unsaved_cpt'], $chargeRow['cptCode']);
										}
									}
								}
							}
							else
								throw new asException( 'Alert', 'No charges exists to POST.' );
						}
					}

					if( is_array($arrRet) && array_key_exists('as_no_match_dx_codes', $arrRet) )
					{
						$arrRet['as_msg'] = "No match found for the following diagnosis code{{1}} in Touch Works, Please adjust {{2}} manually:<br />";

						$findVals = array('{{1}}', '{{2}}');
						if( count($arrRet['as_no_match_dx_codes']) > 1 )
						{
							$replaceVals = array('s', 'these');
						}
						else
						{
							$replaceVals = array('', 'this');
						}

						$arrRet['as_msg'] = str_replace($findVals, $replaceVals, $arrRet['as_msg']);

						$arrRet['as_msg'] .= implode(', ', $arrRet['as_no_match_dx_codes']).'<br/>';
						unset($arrRet['as_no_match_dx_codes']);
					}

					if( is_array($arrRet) && array_key_exists('as_invalid_cpt', $arrRet) )
					{
						if( !array_key_exists('as_msg', $arrRet) )
							$arrRet['as_msg'] = '';

						$arrRet['as_msg'] .= '<br />Invalid CPT code(s) in TouchWorks:<br />';
						$arrRet['as_msg'] .= implode(', ', $arrRet['as_invalid_cpt']).'<br />';
					}

					if( is_array($arrRet) && array_key_exists('as_unsaved_cpt', $arrRet) )
					{
						if( !array_key_exists('as_msg', $arrRet) )
							$arrRet['as_msg'] = '';

						$arrRet['as_msg'] .= '<br />Erorr in saving CPT code(s) to TouchWorks:<br />';
						$arrRet['as_msg'] .= implode(', ', $arrRet['as_unsaved_cpt']).'<br />';
					}

					if( array_key_exists('as_msg', $arrRet ) )
						$arrRet['as_msg'] = trim($arrRet['as_msg'], '<br />, <br>');

				}
				catch( asException $e)
				{
					if( !isset($arrRet) || !is_array($arrRet || !array_key_exists('as_msg', $arrRet)) )
						$arrRet = array();

					$arrRet['as_msg'] = $e->getErrorText();
				}

				/*Unset the object*/
				if( isset($patientObj) && is_object($patientObj) )
					unset($patientObj);
			}

			//Unset Session if finalized
			if($finalize == "1"){
				//Send Done Message to monitor --------------
				$this->mess_sent("Done");
				//Send Done Message to monitor --------------
 				// Move Order/Order set to Medical Hx -> Medications --
				$oChartOrders = new ChartOrders($this->pid, $this->fid);
				$oChartOrders->moveOrders_OrdersSets2OcuMeds();
				// Move Order/Order set to Medical Hx -> Medications --

				// If Intravitreal Injection is given, then on Finalize, Add that to Medical Hx as Administered.  Date is DOS
				$this->addIntraVitInject($date_of_service);

				// AK:> Any procedure done from Work View->Procedure that is not Retina or Botox should go to Medical HX->Surgeries/SX.
				$this->addProceduresInSurgerySx($date_of_service);

				/*
				 * ERP PATIENT PORTAL CLINICAL SUMMARY API WORK STARTS HERE
				 * THIS WILL EXECUTED ON CHART FINALIZED IF ANY CHANGES DONE INTO CHART
				 * PATIENT_SUMMARY IS API MAIN FILE
				 * isERPPortalEnabled IS FUNCTION CALLED FROM common_functions.php TO CHECK ERP ACCOUNT ENABLE OR DISABLE
				 */
				if(isERPPortalEnabled())
				{
					$xml = "";
					$arrData = array();
					try {
						$query ="SELECT
									pd.id as pat_id,CONCAT(pd.lname,', ',pd.fname) as pat_name,
									cmt.id as form_id,
									cmt.finalizerId,
									cmt.facilityid,
									cmt.erp_chart_id
								FROM
									patient_data pd
									JOIN chart_master_table cmt ON cmt.patient_id = pd.id
								WHERE
									cmt.id='".$this->fid."'
								";
						$arrData = get_array_records_query($query);
						$arrData[0]['ccd_type'] ='ccd'; //STATIC SET DUE TO REQUIREMENT IN CCDA CREATION PHP FILE BELOW


						include_once( $GLOBALS['srcdir'].'/erp_portal/patient_summary.php' ); //API HIT MAIN FILE
						include_once( $GLOBALS['srcdir'].'/erp_portal/create_ccda_r2_xml.php' ); //CCDA CREATION FILE

						$obj_pt_summary = new Patient_summary(array("skip_exception"=>1));

						if( count($arrData) > 0 )
						{
							$erpChartId = $arrData[0]['erp_chart_id'];
							$doctorId	=	$arrData[0]['finalizerId'];
							$locationId =	$arrData[0]['facilityid'];

							$ptSummArr=array();
							$ptSummArr['id']= $erpChartId;
							$ptSummArr['CDAXml']= $xml;
							$ptSummArr['PatientExternalId']=$this->pid;
							$ptSummArr['LocationExternalId']=$locationId;
							$ptSummArr['DoctorExternalId']=$doctorId;
							$ptSummArr['Type']= 'ClinicalSummary';

							$obj_pt_summary->addUpdatePatientSummary($ptSummArr,$this->fid);
						}
					} catch(Exception $e) {
						$arrRet['erp_error'][]='Unable to connect to ERP Portal';
					}
				}

				//set Form Id as Finalize id
				if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){
					$_SESSION["finalize_id"] = $_SESSION["form_id"];

					//Unset formId
					$_SESSION["form_id"] = "";
					$_SESSION["form_id"] = NULL;
					unset($_SESSION["form_id"]);
				}

				//log patient monitor status
				if($date_of_service==date('Y-m-d')){patient_monitor_daily("FINALIZED");}
			}

			$closePtChart = $_POST["elem_closePtChart"];
			$ptGo2 = $_POST["elem_ptGo2"];
			$ptSrchPatient = $_POST["elem_ptSrchPatient"];
			$ptSrchFindBy = $_POST["elem_ptSrchFindBy"];

			if($closePtChart == "1"){
				$arrRet = array();
				$arrRet["closePtChart"]=$closePtChart;
				echo json_encode($arrRet);
			}
			else if(isset($ptGo2) && !empty($ptGo2)){
				//Empty EncounterId session for accounting section --
				$_SESSION['encounter_id']="";
				unset($_SESSION['encounter_id']);

				if($date_of_service==date('Y-m-d')){
					patient_monitor_daily("CHART_CLOSE");
				}

				if($ptGo2 == "ShowPhyView"){
					$owv = new WorkView();
					$owv->enter_phy_view();
				}else	if($ptGo2 == "Search"){
					header("location: ".$GLOBALS['rootdir']."/core/patient_select.php?patient=".$ptSrchPatient."&findBy=".$ptSrchFindBy);
				}else if($ptGo2 == "ChangeTemplate"){
					header("location: ".$GLOBALS['rootdir']."/chart_notes/saveCharts.php?elem_saveForm=Change Chart Notes Template&elem_templateId=".$ptSrchFindBy."&elem_tempFormId=".$form_id);
				}else{

					if($ptGo2 == "Logout"){
						$str_js_func="window.top.logOut();";
					}else if($ptGo2 == "SwitchUser"){
						$str_js_func="top.showSwitchUserForm(); top.core_redirect_to('Work_View','','1');";
					}else{
						$str_js_func="top.core_redirect_to('".$ptGo2."','','1');";
					}

					echo "<html><body><script>".$str_js_func."</script></body></html>";exit();
				}
			}
			else{

				if(isset($_POST["savedby"]) && $_POST["savedby"]=="ajax"){ //WORKING
					//
					list($isReviewable,$isEditable,$iscur_user_vphy) = $this->isChartReviewable($_SESSION["authId"],1);

					$oPt = new Patient($this->pid);
					$elem_activeFormId = $oPt->getPtActiveFormId();

					//Creating an Array for sending back respose
					if( !is_array($arrRet) && !isset($arrRet['as_msg']) )
						$arrRet = array();

					$arrRet["finalize_flag"]=$finalize;
					$arrRet["isReviewable"]= ($isReviewable) ? 1 : 0;
					$arrRet["form_id"]=$this->fid;			
					$arrRet["isEditable"]= ($isEditable) ? 1 : 0 ;
					$arrRet["iscur_user_vphy"]= ($iscur_user_vphy) ? 1 : 0 ;
					$arrRet["purge_status"]=$elem_PurgeStatusField;

					$arrRet["elem_masterIsSuperBilled"]=(!empty($sb_formId)) ? 1 : 0;
					$arrRet["elem_masterFinalizerId"]=$finalizerId;
					$arrRet["elem_masterFinalDate"]=$elem_masterFinalDate;
					$arrRet["elem_activeFormId"]=$elem_activeFormId;
					$arrRet["clws_id"]=$clwsID; //?
					$arrRet["cl_trialno"]=$cl_trialno; //?
					$arrRet["clws_type"]=$clws_type; //?
					$arrRet["clws_type_label"]=$clws_type_label; //?
					$arrRet["clws_type_dd"]=$clws_type_dd; //?

					if(strpos($_POST["elem_masterProvIds"],$_SESSION["authId"].",")===false){
						$arrRet["elem_masterProvIds"]=$_POST["elem_masterProvIds"].$_SESSION["authId"].",";
					}else{
						$arrRet["elem_masterProvIds"]=$_POST["elem_masterProvIds"];
					}
					$arrRet["elem_procIds"]=$sb_arrCurProcIds; //?
					$arrRet["apid"] = array($this->arG["arrAp_assess"], $this->arG["arrAp_prob_list_id"], $this->arG["ar_cur_pt_ap_ids"]);

					echo json_encode($arrRet);

				}else{
					//duker isssue
					echo "<html><body><script>window.location.href='work_view.php?opId=".$elem_editMode."&visId=".$insertId."&memo=".$memo."'</script></body></html>";
				}

			}
	}


//Procedure : finalize
function finalize_ProcedureNote(){
	$sql = "UPDATE chart_procedures SET finalized_status = '1' WHERE patient_id='".$this->pid."' AND form_id='".$this->fid."' ";
	$row = sqlQuery($sql);
}

/*

*/

function editNote(){


	$finalize_id = $_REQUEST["hd_finalize_id"];
	$opCode = $_REQUEST["elem_openForm"];
	$memo = $_REQUEST["memo"];

	// Chart Notes Record Archive
	$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION["authId"]);
	$ret = $oChartRecArc->archive();
	// Chart Notes Record Archive

	if($ret == "1"){
		//There are already Active Records
		echo "<script>
				alert('Please finalize all chart notes first.');
				window.location.replace('work_view.php');
			  </script>";

	}else{

		//--
		$this->finalize_ProcedureNote();
		//--

		$_SESSION["form_id"] = "";
		$_SESSION["form_id"] = NULL;
		unset($_SESSION["form_id"]);

		$_SESSION["scanned_chart_image"] = "";
		$_SESSION["scanned_chart_image"] = NULL;
		unset($_SESSION["scanned_chart_image"]);

		$_SESSION["finalize_id"] = "";
		$_SESSION["finalize_id"] = NULL;
		unset($_SESSION["finalize_id"]);

		//Redirect
		$qStr = $GLOBALS['cndir']."/work_view.php";
		header("Location: ".$qStr);
	}
}

function unfinalizeChartNote(){
	$flgrd=1;
	$finalize_id = $_REQUEST["hd_finalize_id"];
	if(!empty($finalize_id) && !empty($_SESSION["patient"])){
		//$oCN = new ChartNote($_SESSION["patient"], $finalize_id);
		$st = parent::unfinalizeChartNote();
		if($st==0){
			//There are already Active Records
			echo "<script>
				alert('Please finalize all chart notes first.');
				window.location.replace('work_view.php');
			  </script>";
			  $flgrd=0;
		}
	}

	if($flgrd==1){

	$_SESSION["form_id"] = "";
	$_SESSION["form_id"] = NULL;
	unset($_SESSION["form_id"]);

	$_SESSION["scanned_chart_image"] = "";
	$_SESSION["scanned_chart_image"] = NULL;
	unset($_SESSION["scanned_chart_image"]);

	$_SESSION["finalize_id"] = "";
	$_SESSION["finalize_id"] = NULL;
	unset($_SESSION["finalize_id"]);

	$_SESSION['vcode'] = "";
	$_SESSION['vcode'] = NULL;
	unset($_SESSION['vcode']);

	//Rem form in Glucoma
	$oChartGlucoma = new ChartGlucoma($this->pid);
	$oChartGlucoma->rem_form_in_glucoma($this->fid);
	$this->save_chart_log();

	//Redirect
	$qStr = $GLOBALS['cndir']."/work_view.php";
	header("Location: ".$qStr);
	}
}

function refesh_chart_note(){
	//After Save, Response to send ---------------

	$sql = "SELECT finalize, purge_status, finalizeDate, provIds, finalizerId, isSuperBilled  FROM chart_master_table
			WHERE patient_id='".$this->pid."' AND id='".$this->fid."' ";
	$row=sqlQuery($sql);
	if($row!=false){
		$finalize = $row["finalize"];
		$elem_PurgeStatusField = $row["purge_status"];
		$elem_masterFinalDate = $row["finalizeDate"];
		$elem_masterProvIds = $row["provIds"];
		$finalizerId = $row["finalizerId"];
		$elem_masterIsSuperBilled=$row["isSuperBilled"];
	}

	//
	list($isReviewable,$isEditable,$iscur_user_vphy) = $this->isChartReviewable($_SESSION["authId"],1);

	$oPt = new Patient($this->pid);
	$elem_activeFormId = $oPt->getPtActiveFormId();

	//Creating an Array for sending back respose
	$arrRet = array();
	$arrRet["finalize_flag"]=$finalize;
	$arrRet["isReviewable"]= ($isReviewable) ? 1 : 0;
	$arrRet["form_id"]=$elem_formId;
	$arrRet["isEditable"]= ($isEditable) ? 1 : 0 ;
	$arrRet["iscur_user_vphy"]= ($iscur_user_vphy) ? 1 : 0 ;
	$arrRet["purge_status"]=$elem_PurgeStatusField;
	$arrRet["elem_masterIsSuperBilled"]=$elem_masterIsSuperBilled;
	$arrRet["elem_masterFinalizerId"]=$finalizerId;
	$arrRet["elem_masterFinalDate"]=$elem_masterFinalDate;
	$arrRet["elem_activeFormId"]=$elem_activeFormId;
	if(strpos($elem_masterProvIds,$_SESSION["authId"].",")===false){
		$arrRet["elem_masterProvIds"]=$elem_masterProvIds.$_SESSION["authId"].",";
	}else{
		$arrRet["elem_masterProvIds"]=$elem_masterProvIds;
	}

	echo json_encode($arrRet);

	//-------------------------------------------------
}

function audit_trail($ar_info){
	$policyStatus = $_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
	$len = count($ar_info);

	if(is_array($_SESSION['Patient_Viewed']) && $policyStatus == 1 && isset($_SESSION['Patient_Viewed']) === true && $len>0){

		$opreaterId = $_SESSION["authId"];
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);

		$arr = array();
		$arr['Operater_Id'] = $opreaterId;
		$arr['Operater_Type'] = getOperaterType($opreaterId);
		$arr['IP'] = $ip;
		$arr['MAC_Address'] = $_REQUEST['macaddrs'];
		$arr['URL'] = $URL;
		$arr['Browser_Type'] = $browserName;
		$arr['OS'] = $os;
		$arr['Machine_Name'] = $machineName;

		if($len>0){
			$ar_in=array();
			foreach($ar_info as $k => $v){
				if(is_array($v) && count($v)>0){
					$tmp = array_merge($arr, $v);
					$ar_in[$k] = $tmp;
				}
			}
			if(count($ar_in)>0){
				auditTrail($ar_in,$mergedArray=array(),0,0,0);
			}
		}
	}
}

function purge_note(){
	//get Status
	$purge = $_POST["elem_status"];
	$purg_by ="0"; $purg_on = "0000-00-00 00:00:00";
	if($purge=="1"){
		$purg_by = $_SESSION["authId"];
		$purg_on = wv_dt("now");
	}

	if($this->fid!="" && $this->fid!=""){
		$sql = "SELECT id, patient_id, purge_status, purged_by, purged_on  FROM chart_master_table where finalize='1' AND delete_status='0' AND id='".$this->fid."'  ";
		$row = sqlQuery($sql);
		if($row!=false){

			$pid = $row["patient_id"];
			$purge_status_old = $row["purge_status"];
			$purged_by_old = $row["purged_by"];
			$purged_on_old = $row["purged_on"];

			$sql= "UPDATE chart_master_table ".
					 "SET ";
			$sql .= "update_date = '".wv_dt('now')."', ";
			$sql .= "". //"finalize = '1', "."finalizerId = '".$_SESSION["authId"]."', ".
			 "record_validity = '1', ".
			 "purge_status='".$purge."', ".
			 "purged_by='".$purg_by."', ".
			 "purged_on='".$purg_on."' ".
			 " WHERE id= '".$this->fid."' ";
			 $res = sqlQuery($sql);
			if($res){
				$_SESSION["finalize_id"]=$this->fid;
				//echo("Purge Done");
			}else{
				//echo("0");
			}

			//Audit Trail --
			$arr = array();
			$arr[0]['Pk_Id'] = $this->fid;
			$arr[0]['Table_Name'] = 'chart_master_table';
			$arr[0]['Category'] = 'chart_notes';
			$arr[0]['Category_Desc'] = 'Purge Status';
			$arr[0]['Old_Value'] = $purge_status_old;
			$arr[0]['New_Value'] = $purge;
			$arr[0]['pid'] = $pid;
			$arr[0]['Data_Base_Field_Name'] = "purge_status";
			$arr[0]['Data_Base_Field_Type'] = "int";
			$arr[0]['Filed_Label'] = "buttonPurge";
			$arr[0]['Filed_Text'] = (!empty($purg_by)) ? "Purge" : "Undo Purge" ;
			$arr[0]['Action'] = 'update';

			//
			$arr[1]['Pk_Id'] = $this->fid;
			$arr[1]['Table_Name'] = 'chart_master_table';
			$arr[1]['Category'] = 'chart_notes';
			$arr[1]['Category_Desc'] = 'Purged By';
			$arr[1]['Old_Value'] = $purged_by_old;
			$arr[1]['New_Value'] = $purg_by;
			$arr[1]['pid'] = $pid;
			$arr[1]['Data_Base_Field_Name'] = "purged_by";
			$arr[1]['Data_Base_Field_Type'] = "int";
			$arr[1]['Filed_Label'] = "buttonPurge";
			$arr[1]['Filed_Text'] = (!empty($purg_by)) ? "Purge" : "Undo Purge";
			$arr[1]['Action'] = 'update';

			//
			$arr[2]['Pk_Id'] = $this->fid;
			$arr[2]['Table_Name'] = 'chart_master_table';
			$arr[2]['Category'] = 'chart_notes';
			$arr[2]['Category_Desc'] = 'Purged On';
			$arr[2]['Old_Value'] = $purged_on_old;
			$arr[2]['New_Value'] = $purg_on;
			$arr[2]['pid'] = $pid;
			$arr[2]['Data_Base_Field_Name'] = "purged_on";
			$arr[2]['Data_Base_Field_Type'] = "datetime";
			$arr[2]['Filed_Label'] = "buttonPurge";
			$arr[2]['Filed_Text'] = (!empty($purg_by)) ? "Purge" : "Undo Purge";
			$arr[2]['Action'] = 'update';

			//
			$this->audit_trail($arr);
			//Audit Trail --
		}
	}

	$this->refesh_chart_note();
}

function delete_note(){
	if($this->fid!="" && $this->fid!=""){
		$sql = "SELECT id, delete_status, deleted_by, deleted_on, patient_id FROM chart_master_table where finalize='1' AND purge_status='1' AND delete_status='0' AND id='".$this->fid."'  ";
		$row = sqlQuery($sql);
		if($row!=false){

			$pid = $row["patient_id"];
			$delete_status_old = $row["delete_status"];
			$deleted_by_old = $row["deleted_by"];
			$deleted_on_old = $row["deleted_on"];

			$delete_status='1';
			$deleted_by=$_SESSION["authId"];
			$deleted_on=wv_dt("now");

			$sql= "UPDATE chart_master_table ".
					 "SET ";
			$sql .= "update_date = '".wv_dt('now')."', ";
			$sql .= "delete_status = '".$delete_status."', ".
			 "record_validity = '1', ".
			 "deleted_by='".$deleted_by."', ".
			 "deleted_on='".$deleted_on."' ".
			 " WHERE id= '".$this->fid."' ";
			 $res = sqlQuery($sql);

			 //Audit Trail --
			$arr = array();
			$arr[0]['Pk_Id'] = $this->fid;
			$arr[0]['Table_Name'] = 'chart_master_table';
			$arr[0]['Category'] = 'chart_notes';
			$arr[0]['Category_Desc'] = 'Delete Status';
			$arr[0]['Old_Value'] = $delete_status_old;
			$arr[0]['New_Value'] = $delete_status;
			$arr[0]['pid'] = $pid;
			$arr[0]['Data_Base_Field_Name'] = "delete_status";
			$arr[0]['Data_Base_Field_Type'] = "int";
			$arr[0]['Filed_Label'] = "buttonChrtDel";
			$arr[0]['Filed_Text'] = "Delete";
			$arr[0]['Action'] = 'delete';

			//
			$arr[1]['Pk_Id'] = $this->fid;
			$arr[1]['Table_Name'] = 'chart_master_table';
			$arr[1]['Category'] = 'chart_notes';
			$arr[1]['Category_Desc'] = 'Deleted By';
			$arr[1]['Old_Value'] = $deleted_by_old;
			$arr[1]['New_Value'] = $deleted_by;
			$arr[1]['pid'] = $pid;
			$arr[1]['Data_Base_Field_Name'] = "deleted_by";
			$arr[1]['Data_Base_Field_Type'] = "int";
			$arr[1]['Filed_Label'] = "buttonChrtDel";
			$arr[1]['Filed_Text'] = "Delete";
			$arr[1]['Action'] = 'delete';

			//
			$arr[2]['Pk_Id'] = $this->fid;
			$arr[2]['Table_Name'] = 'chart_master_table';
			$arr[2]['Category'] = 'chart_notes';
			$arr[2]['Category_Desc'] = 'Deleted On';
			$arr[2]['Old_Value'] = $deleted_on_old;
			$arr[2]['New_Value'] = $deleted_on;
			$arr[2]['pid'] = $pid;
			$arr[2]['Data_Base_Field_Name'] = "deleted_on";
			$arr[2]['Data_Base_Field_Type'] = "datetime";
			$arr[2]['Filed_Label'] = "buttonChrtDel";
			$arr[2]['Filed_Text'] = "Delete";
			$arr[2]['Action'] = 'delete';

			//
			$this->audit_trail($arr);
			//Audit Trail --


			//--
			$oPt = new Patient($this->pid);
			$lastFormId = $oPt->getPtLastChart();
			if(empty($lastFormId)){
				$_SESSION["finalize_id"]="";
				$_SESSION["form_id"]="";
				unset($_SESSION["finalize_id"]);
				unset($_SESSION["form_id"]);
			}
		}
	}
	echo "0";
}

/*  WNL */
function save_wnl(){
	global $cryfwd_form_id;
	$cryfwd_form_id = $_POST["cryfwd_form_id"];

	$w = strtoupper($_POST["w"]);
	$form_id = $this->fid; //$_POST["elem_formId"];
	$patientId = $this->pid; //$_POST["elem_patientId"];
	$exmEye = $_POST["elem_exmEye"];
	//$statusElem = $_POST["elem_se_change"];
	$flgCarry=0;

	//check
	if(empty($patientId) || empty($form_id)){ echo "0";	$flgStopExec = 1;  }

	//Check if Chart is not Finalized or User is Finalizer
	$oCN= $this; //new ChartNote($patientId,$form_id);
	if(!$oCN->checkFinalizer()){
		echo "0";
		$flgStopExec = 1;
	}
	//Check if Chart is not Finalized or User is Finalizer

	if(!isset($flgStopExec) || empty($flgStopExec)){
		$sql = "";
		if($w == "EOM"){
			$oEOM=new EOM($patientId,$form_id);
			$oEOM->save_wnl();
			$sql = "1";
		}else if($w == "EXTERNAL"){
			$oEE=new ExternalExam($patientId,$form_id);
			$oEE->save_wnl();
			$sql = "1";
		}else if($w == "PUPIL"){
			$oPupil=new Pupil($patientId,$form_id);
			$oPupil->save_wnl();
			$sql = "1";
		}else if($w == "LA"){
			$oLA=new LA($patientId,$form_id);
			$oLA->save_wnl();
			$sql = "1";
		}else if($w == "GONIO"){
		}else if($w == "SLE"){
			$oSLE=new SLE($patientId,$form_id);
			$oSLE->save_wnl();
			$sql = "1";
		}else if($w == "RV"){
			//$oON=new OpticNerve($patientId,$form_id);
			//$wnl_optic = $oON->save_wnl();
			$oFE=new FundusExam($patientId,$form_id);
			$oFE->save_wnl();
			$sql = "1";
		}else if($w == "REF_SURG"){
		}else if($w == "CVF"){
			$oCVF = new CVF($patientId,$form_id);
			$oCVF->save_wnl();
			$sql = "1";
		}else if($w == "AG"){
			$oCVF = new AmslerGrid($patientId,$form_id);
			$oCVF->save_wnl();
			$sql = "1";
		}

		if($sql != ""){
			$this->makeChartNotesValid($form_id);
		}

		$flg = ($sql != "") ? "1"  : "0";
		echo $flg;
	}
}

function save_no_change(){
	$w = strtoupper($_POST["w"]);
	$form_id = $this->fid; //$_POST["elem_formId"];
	$patientId = $this->pid; //$_POST["elem_patientId"];
	$exmEye = $_POST["elem_exmEye"];

	global $cryfwd_form_id;
	$cryfwd_form_id = $_POST["cryfwd_form_id"];

	//check
	if(empty($patientId) || empty($form_id)){ echo "0"; $flgStopExec = 1;  }

	//Check if Chart is not Finalized or User is Finalizer
	$oCN= new ChartNote($patientId,$form_id);
	if(!$oCN->checkFinalizer()){
		echo "0";
		//exit();
		$flgStopExec = 1;
	}
	//Check if Chart is not Finalized or User is Finalizer

	if(!isset($flgStopExec) || empty($flgStopExec)){
		//$statusElem = $_POST["elem_statusElements"];
		$sql="";
		if($w == "VISION"){
		}else if($w == "EOM"){
			$oEOM=new EOM($patientId,$form_id);
			$oEOM->save_no_change();
			$sql = "1";
		}else if($w == "EXTERNAL"){
			$oEE=new ExternalExam($patientId,$form_id);
			$oEE->save_no_change();
			$sql = "1";
		}else if($w == "PUPIL"){
			$oPupil=new Pupil($patientId,$form_id);
			$oPupil->save_no_change();
			$sql = "1";
		}else if($w == "LA"){
			$oLA=new LA($patientId,$form_id);
			$oLA->save_no_change();
			$sql = "1";
		}else if($w == "GONIO"){
			$oGonio=new Gonio($patientId,$form_id);
			$oGonio->save_no_change();
			$sql = "1";
		}else if($w == "SLE"){
			$oSLE=new SLE($patientId,$form_id);
			$oSLE->save_no_change();
			$sql = "1";
		}else if($w == "Optic"){
		}else if($w == "RV"){
			$oFE=new FundusExam($patientId,$form_id);
			$tmpNC=$oFE->save_no_change();
			//$oON=new OpticNerve($patientId,$form_id);
			//$oON->save_no_change($tmpNC);
			$sql = "1";
		}else if($w == "REF_SURG"){
			$oRefSurg = new RefSurg($patientId,$form_id);
			$oRefSurg->save_no_change();
			$sql = "1";
		}else if($w=="CVF"){
			$oCVF = new CVF($patientId,$form_id);
			$oCVF->save_no_change();
			$sql = "1";
		}else if($w=="AG"){
			$oAG = new AmslerGrid($patientId,$form_id);
			$oAG->save_no_change();
			$sql = "1";
		}

		if($sql != ""){
			$this->makeChartNotesValid();
		}

		$flg = ($sql != "") ? "1"  : "0";
		echo $flg;
	}

}

function set_reset_values(){
	global $cryfwd_form_id;
	$section = $_POST["elem_section"];
	$elem_formId = $this->fid; //$_POST["elem_fid"];
	$patientId = $this->pid; //$_SESSION["patient"];
	$cryfwd_form_id = $_POST["cryfwd_form_id"];

	if(empty($elem_formId)||empty($patientId)){echo "1";$flgStopExec=1;}

	if(!isset($flgStopExec) || empty($flgStopExec)){

	if($section=="All"){

		//case "pupil":
			$oPupil=new Pupil($patientId,$elem_formId);
			$oPupil->resetVals();
		//break;

		//case "eom":
			$oEom=new EOM($patientId,$elem_formId);
			$oEom->resetVals();
		//break;

		//case "external":
			$oEe=new ExternalExam($patientId,$elem_formId);
			$oEe->resetVals();
		//break;

		//case "la":
			$oLa=new LA($patientId,$elem_formId);
			$oLa->resetVals();
		//break;

		//case "sle":
			$oSle=new SLE($patientId,$elem_formId);
			$oSle->resetVals();
		//break;

		//case "rv":
			$oRv=new FundusExam($patientId,$elem_formId);
			$oRv->resetVals();
			$oON=new OpticNerve($patientId,$elem_formId);
			$oON->resetVals();
		//break;

		//case "gonio":
			$oGonio = new Gonio($patientId,$elem_formId);
			$oGonio->resetVals();
		//break;

		//case "ref_surg":
			$oRefSurg = new RefSurg($patientId,$elem_formId);
			$oRefSurg->resetVals();
		//break;

	}

	echo "1";

	}//flgStopExec
}

function saveRole(){

	if($_SESSION["logged_user_type"] == "3" || $_SESSION["logged_user_type"] == "13"){
		$oRoleAs = new RoleAs();
		$oRoleAs->save_role($this->fid);
	}
}

function set_crfrwd_id(){
	$set_cf_form_id = $_GET["set_cf_form_id"];
	if(!empty($set_cf_form_id)){
		$sql = "UPDATE chart_master_table SET cryfwd_form_id='".$set_cf_form_id."' WHERE patient_id='".$this->pid."' AND id='".$this->fid."' ";
		$res = sqlQuery($sql);
	}
}

    //Check cpt and icd and cpt modifiers in vista. if (cpt and icd and cpt modifiers) codes exists in vista than insert in log dss_code_dictionary otherwise skipped
    function check_cpt_and_icd_codes( $sbfileman_dos,$objDss_enc_vn, $checkparms=array() ) {
        $checkreturn=$tmpArr = array();
        if(empty($checkparms))return $checkreturn;

        $mod_arr=array();
        $dss_error=array();

        $cpterror=array();
        $icderror=array();
        $moderror=array();

        foreach($checkparms['cpt'] as $cpt=>$modifiers) {
            $cptsql='select id,modifiers from dss_code_dictionary where code="'.$cpt.'" code_type="cpt" ';
            $cptrs=imw_query($cptsql);
            if($cptrs && $cptrs)$cptArr=imw_fetch_assoc($cptrs);

            if($cptArr['id']){
                $cpterror[$cpt]=false;

                if(empty($modifiers)==false){
                    if($cptArr['modifiers']!=''){
                        $vista_mod=explode(',',$cptArr['modifiers']);
                        $mod_arr=array_diff($modifiers, $vista_mod);
                        if(empty($mod_arr)==false) {
                            $mparams=array();
                            $mparams['cptCode']=''.$cpt.'';;
                            //check vista for modifiers => modCode
                            $moderror[$cpt]=$this->checkModifersInVista($objDss_enc_vn,$mparams,$modifiers);
                        }
                    } else {
                        $mparams=array();
                        $mparams['cptCode']=''.$cpt.'';;
                        //check vista for modifiers => modCode
                        $moderror[$cpt]=$this->checkModifersInVista($objDss_enc_vn,$mparams,$modifiers);

                    }

                }

            } else {
                //check vista for cpt  => lexIen
                //check vista for modifiers => modCode
                $params=array();
                $params['search']="".$cpt."";
                $params['application']="CHP";
                $params['maxRecords']="10";
                $params['searchDate']=$sbfileman_dos;
                try {
                    $ret_cpt=$objDss_enc_vn->PCE_ICDLexSearch($params);
                    if(empty($ret_cpt)==false && isset($ret_cpt['lexIen']) && $ret_cpt['lexIen']!='-1') {
                        //insert cpt to log table (dss_code_dictionary)
                        $recpt=$this->insertIcdOrCptCodeDictionary($ret_cpt,'cpt');

                        if(empty($modifiers)==false){
                            $mparams=array();
                            $mparams['cptCode']=''.$cpt.'';
                            //check vista for modifiers => modCode
                            $moderror[$cpt]=$this->checkModifersInVista($objDss_enc_vn,$mparams,$modifiers);
                        }
                        $cpterror[$cpt]=false;
                    } else {
                        $cpterror[$cpt]=true;
                    }
                }  catch(Exception $e){
                    $cpterror[$cpt]=true;
                    //$arrRet['dss_error'][]=$e->getMessage();
                }

            }

        }

        foreach($checkparms['icd10'] as $dx_codes) {
            $icdsql='select id from dss_code_dictionary where code="'.$dx_codes.'" code_type="icd10" ';
            $icdrs=imw_query($icdsql);
            if($icdrs && $icdrs)$icdArr=imw_fetch_assoc($icdrs);
            if($icdArr['id']){
                $icderror[$dx_codes]=false;
            } else {
                //check vista for ICD10  => lexIen
                $params=array();
                $params['search']=''.$dx_codes.'';
                $params['application']="10D";
                $params['maxRecords']="10";
                $params['searchDate']=$sbfileman_dos;
                //check vista for ICD10

                try {
                    $icd10Arr=$objDss_enc_vn->PCE_ICDLexSearch($params);

                    if(empty($icd10Arr)==false) {
                        foreach($icd10Arr as $arr) {

                            if($arr['lexIen']=='-1' && $arr['codeDescription']=='Active 10D code '.$dx_codes.' not found') {
                                $icderror[$dx_codes]=true;
                            } else {
                                $icderror[$dx_codes]=false;
                            }
                        }
                        //insert icd10 to log table (dss_code_dictionary)
                        $icd10=$this->insertIcdOrCptCodeDictionary($icd10Arr,'icd10',$dx_codes);

                    } else {
                        $icderror[$dx_codes]=true;
                    }
                }  catch(Exception $e){
                    $icderror[$dx_codes]=true;
                    //$arrRet['dss_error'][]=$e->getMessage();
                }


            }
        }

        $cpterror=array_filter($cpterror);
        $icderror=array_filter($icderror);
        $moderror=array_filter($moderror);


        $return=array();
        foreach($checkparms['cpt'] as $cpt=>$modifiers) {
            if(empty($cpterror)==false && isset($cpterror[$cpt])) {
                $return[]='CPT code '.$cpt.' does not exists in VISTA.';
            }
            if(empty($moderror)==false && isset($moderror[$cpt])) {
                $return[]='MODIFIERS '.implode(',',$modifiers).' for CPT code '.$cpt.' does not exists in VISTA.';
            }
        }

        foreach($checkparms['icd10'] as $dx_codes) {
            if(empty($icderror)==false && isset($icderror[$dx_codes])) {
                $return[]='ICD10 code '.$dx_codes.' does not exists in VISTA.';
            }
        }

        return $return;

    }

    function insertIcdOrCptCodeDictionary($ret_code,$type,$code=false) {
        if(empty($ret_code)==false && isset($ret_code['lexIen']) && $ret_code['lexIen']!='-1') {
            $sbcode=$ret_code['lexIen'];
            if($code)$sbcode=$code;

            $select_sql='select * from dss_code_dictionary where code="'.$sbcode.'" and code_type="'.$type.'" ';
            $select_rs=imw_query($select_sql);
            if($select_rs && imw_num_rows($select_rs)==0) {
                $sql='insert into dss_code_dictionary set code="'.$sbcode.'", code_type="'.$type.'" ';
                imw_query($sql);
            }
        }

    }

    function checkModifersInVista($objDss_enc_vn,$mparams,$modifiers) {
        $moderror=false;
        try {
            $modreturn=$objDss_enc_vn->PCE_DefaultCPTModifiers($mparams);
            if($modreturn && is_array($modreturn)) {
                $vista_mod=array();
                foreach($modreturn as $row) {
                    $vista_mod[]=$row['modCode'];
                }

                $mod_arr=array_diff($modifiers, $vista_mod);
                if(empty($mod_arr)==false) {
                    $moderror=true;
                } else {
                    $moderror=false;
                }

                $cpt=trim($mparams['cptCode']);
                $vista_mod_str=implode(',',$vista_mod);

                $sql='update dss_code_dictionary set modifiers="'.$vista_mod_str.'" where code="'.$cpt.'" and code_type="cpt" ';
                imw_query($sql);
                //insert modifiers to log table (dss_code_dictionary)
            } else {
                $moderror=true;
                //return error
            }
        }  catch(Exception $e){
            $moderror=true;
            //$arrRet['dss_error'][]=$e->getMessage();
        }
        return $moderror;
    }

    public function upload_problem_to_vista($arrUp)
    {
		// pre($arrUp);

		$arrRet=array();

        $sqlrs=imw_query('select External_MRN_5 from patient_data where id='.$arrUp['pt_id'].' ');

        $dss_row=imw_fetch_assoc($sqlrs);
        $dss_dfn=$dss_row['External_MRN_5'];

        if(empty($dss_dfn)==false && $dss_dfn!=NULL && $dss_dfn!='') {
            try
            {
                include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
                include_once( $GLOBALS['srcdir'].'/dss_api/dss_enc_visit_notes.php' );
                $objDss_mhx = new Dss_medical_hx();
                $objDss_enc = new Dss_enc_visit_notes();

                $patient=$this->get_patient_name($arrUp['pt_id']);
                $patientArr=explode('-',$patient);
                $patientStr=$dss_dfn.'^'.$patientArr[0];
				// pre($patientStr);

                $onset_fileman = $objDss_mhx->convertToFileman($arrUp['onset_date']);
                $onset_external = date('M d Y', strtotime($arrUp['onset_date']));
				// pre($onset_fileman.'--'.$onset_external);

                $current_fileman = $objDss_mhx->convertToFileman(date('Y-m-d'));
                $current_external = date('M d Y');
				// pre($current_fileman.'--'.$current_external);

                $status="A^ACTIVE";
                if(strtolower($arrUp['status'])=='inactive') {
                    $status="I^INACTIVE";
                }
                // pre($status);

                $comment=($arrUp["comments"] && $arrUp["comments"]!='')?array($arrUp["comments"]):array("^");
                $comment_count=count($comment);
            	// pre($comment.'--'.$comment_count);

                // Dss Problem ID
                $ptProbId = $arrUp['id'];
                $dssProblemId = '';
                if(isset($ptProbId) && $ptProbId != "") {
                	// update
                	$sql = "SELECT dss_prblm_id FROM pt_problem_list WHERE id = ".$ptProbId;
                	$query = imw_query($sql);
                	if(imw_num_rows($query) > 0) {
                		$res = imw_fetch_assoc($query);
                		if($res['dss_prblm_id'] != '' && $res['dss_prblm_id'] != 0) {
	                		$dssProblemId = $res['dss_prblm_id'];
                		}
                	}
                }
                $lexiconInfo=$dssProblemId."^".remSiteDxFromAssessment($arrUp['problem_name']);
				// pre($lexiconInfo,1);

                $dss_loginDUZ='';
                if(isset($_SESSION['dss_loginDUZ']) && $_SESSION['dss_loginDUZ']!='')
                    $dss_loginDUZ=$_SESSION['dss_loginDUZ'];
                // pre($dss_loginDUZ);

                $dateLastModified=$current_fileman."^".$current_external;
                if($action=='update') {
                    $dateLastModified="0^";
                }
                // pre($dateLastModified);

                // Service Connected Eligibility
            	$service_eligibility = '';

            	$str_sc = $arrUp['service_eligibility'];
            	if(!empty($str_sc)){
	                parse_str($str_sc,$s_eligibility);
	                // pre($s_eligibility);

	                if($s_eligibility['req_from'] == 'work_view') {
	                	$service_eligibility = $s_eligibility['dss'];
	                }
					// pre($service_eligibility); // Not working
            	} else {
            		$formId = $_SESSION['form_id'];
					$sql = imw_query("SELECT `service_eligibility` FROM `chart_master_table` WHERE `id` = '".$formId."'");
					if(imw_num_rows($sql) > 0) {
						$result = imw_fetch_assoc($sql);
						$service_eligibility = unserialize($result['service_eligibility']);
					} else {
						$service_eligibility = '';
					}
					// pre($service_eligibility,1);
            	}

                $service_connected_opts = array(
					'sc' => "0^NO",
					// 'cv' => "0^NO",
					'ao' => "0^NO",
					'ir' => "0^NO",
					// 'swac' => "0^NO",
					// 'shd' => "0^NO",
					// 'ec' => "0^NO",
					'mst' => "0^NO",
					'hnc' => "0^NO"
				);

                if(!empty($service_eligibility) && sizeof($service_eligibility) > 0){
                	foreach ($service_eligibility as $key => $value) {
                		if(array_key_exists(strtolower($key), $service_connected_opts) && $value == 1) {
            				$service_connected_opts[strtolower($key)] = $value."^YES";
                		}
                	}
                }
                // pre($service_connected_opts,1);

                // Get LEXIEN from DSS
                try{
	                $lexIEN = $objDss_enc->processICD10($arrUp['dx'], $current_fileman);
                } catch(Exception $e) {
                	echo '<script>top.fAlert("'.$e->getMessage().'");</script>';
                	exit;
                }

                $params=array();
                $params['patient']=$patientStr;
                $params['provider']=$dss_loginDUZ;
                $params['vamc']="^";
                $params['diagnosis']=$lexIEN."^".$arrUp['dx'];
                $params['dateLastModified']=$dateLastModified;
                $params['diagDesc']="^".remSiteDxFromAssessment($arrUp['problem_name']);
                $params['currentDate']=$current_fileman."^".$current_external;
                $params['activeStatus']=$status;
                $params['onsetDate']=$onset_fileman."^".$onset_external;
                $params['lexiconInfo']=$lexiconInfo;
                $params['condition']="P";
                $params['currentUser']=$dss_loginDUZ."^";
                $params['recorder']="^";
                $params['treater']="^";
                $params['service']="^";
                $params['dateResolved']="^";
                $params['treatmentClinic']="^";
                $params['recordedDate']=$current_fileman."^".$current_external;
                $params['pge']="0^NO";
                $params['priority']="A^ACUTE";
                $params['comments']=$comment;
                $params['numComments']="$comment_count";
                $params = array_merge($params, $service_connected_opts); // adding service connected options
	            // pre($params);
				// pre($ptProbId .'--'. $dssProblemId);

	            if($ptProbId != '' && $ptProbId != 0 && $dssProblemId != '' && $dssProblemId > 0) {
	            	$params['internalEntryNumber']=$dssProblemId;
	            	//upload Edit problem to vista
                    $arrRet = $objDss_mhx->editPatientProblemList($params);
                    if(isset($arrRet[0]['code']) && $arrRet[0]['code']=='-1')
                        throw new Exception ('Error update : '.$arrRet[0]['desc']);
	            } else {
                    //upload Add problem to vista
                    $ppid = '';
                    if($ptProbId != '' || $ptProbId != 0) {
                    	$ppid = $ptProbId;
                    } elseif ($arrUp['lastInsertId'] != '' || $arrUp['lastInsertId'] != 0) {
                    	$ppid = $arrUp['lastInsertId'];
                    }

                    // pre($arrUp);
                    $arrRet = $objDss_mhx->updatePatientProblemList($params);
                    if(isset($arrRet[0]['code']) && $arrRet[0]['code']=='-1'){
                    	throw new Exception ('Error add : '.$arrRet[0]['desc']);
                    } else {
                    	if($ppid) {
							$sqlUpdate = " UPDATE `pt_problem_list` SET `dss_prblm_id`='".$arrRet[0]['code']."' WHERE `id`=".$ppid." " ;
							// pre($sqlUpdate);
							imw_query($sqlUpdate);
                    	} else {
                    		throw new Exception ('Error update : IMW Patient problem id not found');
                    	}
                    }
                }

            } catch (Exception $e) {
                echo '<script>top.fAlert("'.$e->getMessage().'");</script>';
                die;
            }
        }
    }


	//Returns patient name
	public function get_patient_name($id,$noFac="0")
	{
		$sql = imw_query("SELECT id,fname, lname, mname,default_facility FROM patient_data WHERE id = '$id'");
		while($row = imw_fetch_array($sql)){
			$facility_id_p=$row['default_facility'];
			if($facility_id_p<>"")
			{
				$query = "select facilityPracCode from pos_facilityies_tbl where pos_facility_id='$facility_id_p'";
				$result = imw_query($query);
				$rows = imw_fetch_array($result);
				$patient_facility = "(".$rows['facilityPracCode'].")";
			}
			$sep="-";
			if( $noFac == "1" ){
				$patient_facility = "";
				$sep=" - ";
			}

			if($row != false){
				$ret = $row["fname"]." ".$row["mname"]." ".$row["lname"].$sep.$row["id"]." ".$row['suffix'].$patient_facility;
				return $ret;
			}
			else{
				return false;
			}
		};
	}

	function delete_problem_from_vista($record_id)
	{
		try
		{
			$pt_prob_id = $record_id;
			if($pt_prob_id == '')
				throw new Exception('DSS Error: Patient problem id not available.');

			$dss_loginDUZ='';
			if(isset($_SESSION['dss_loginDUZ']) && $_SESSION['dss_loginDUZ']!='') {
	   			$dss_loginDUZ=$_SESSION['dss_loginDUZ'];
			} else {
				throw new Exception('DSS Error: DSS Login DUZ not allowed to be empty.');
			}

			$dss_prob_IEN = '';
			$sql = imw_query("SELECT dss_prblm_id FROM pt_problem_list WHERE id = ".$pt_prob_id." AND pt_id = ".$this->pid);
			$res = imw_fetch_assoc($sql);
			$dss_prob_IEN = $res['dss_prblm_id'];
			if($dss_prob_IEN == '')
				throw new Exception('DSS Error: Patient problem not having a DSS IEN.');

			include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
			$objDss_mhx = new Dss_medical_hx();

			$params=array();
			$params['internalEntryNumber']=$dss_prob_IEN;
			$params['providerId']=$dss_loginDUZ;
			$params['vamc']="500";
			$params['reason']="no longer problem";

			$resp = $objDss_mhx->deletePatientProblemList($params);

			if($resp[0]['code'] != 1) {
				throw new Exception("DSS Error: Problem is not marked as deleted in the Vista.");
			}
		} catch(Exception $e) {
			echo $e->getMessage();
			exit;
		}
    }
}
?>
