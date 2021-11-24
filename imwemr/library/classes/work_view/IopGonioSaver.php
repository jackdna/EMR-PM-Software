<?php

class IopGonioSaver extends ChartNoteSaver{
	private $arG;

	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
		$this->arG = array();
	}

	function getIopTrgtDef($formId=0,$strict=0){
		$pId=$this->pid;
		$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='".$formId."' ";
		$row = sqlQuery($sql);
		if( ($row == false) && ($formId != 0) && ($strict == 0) ){
			$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='0' ";
			$row = sqlQuery($sql);
		}
		return $row;
	}

	function saveIopTrgt($trgtOd,$trgtOs,$formId=0){
		$pId=$this->pid;
		//$formId=$this->fid;

		if( !empty($pId) ){
			$row = $this->getIopTrgtDef($formId,1);
			if($row != false){
				$sql = "UPDATE tbl_def_val SET iopTrgtOd='".sqlEscStr($trgtOd)."', iopTrgtOs='".sqlEscStr($trgtOs)."' WHERE ptId='".$pId."' AND form_id='".$formId."' ";
				$res = sqlQuery($sql);
			}else{
				if(!empty($trgtOd) || !empty($trgtOs)){
					$sql = "INSERT INTO tbl_def_val(tbl_def_val_id, iopTrgtOd, iopTrgtOs, ptId, form_id)  ".
						 "VALUES(NULL, '".sqlEscStr($trgtOd)."', '".sqlEscStr($trgtOs)."', '".$pId."', '".$formId."' ) ";
					$res = sqlQuery($sql);
				}
			}

			//Update zero
			if(!empty($formId)){
				$this->saveIopTrgt($trgtOd,$trgtOs,0);
			}
		}
	}

	function remIopTrgtDefVal($trgtOd,$trgtOs,$formId=0){
		$pId=$this->pid;
		$trgtOd = trim($trgtOd);
		$trgtOs = trim($trgtOs);
		if(!empty($pId) && empty($trgtOd) && empty($trgtOs)){
			$flgEmp=1;
			if(!empty($formId)){
				//check in IOP
				$cQry = "select trgtOd, trgtOs FROM chart_iop
							WHERE form_id='".$formId."' AND patient_id='".$pId."' AND purged='0' ";
				$row = sqlQuery($cQry);
				if($row != false){
					if(trim($row["trgtOd"])!="" || trim($row["trgtOs"])!=""){
						//do not empty def values;
						$flgEmp=0;
					}
				}
			}
			//VF
			$sql="SELECT iopTrgtOd, iopTrgtOs FROM vf WHERE formId = '".$formId."' AND patientId='".$pId."'";
			$row = sqlQuery($sql);
			if($row != false){
				if(trim($row["iopTrgtOd"])!="" || trim($row["iopTrgtOs"])!=""){
					//do not empty def values;
					$flgEmp=0;
				}
			}

			//NFA
			$sql="SELECT iopTrgtOd, iopTrgtOs FROM nfa WHERE form_id = '".$formId."' AND patient_id='".$pId."'";
			$row = sqlQuery($sql);
			if($row != false){
				if(trim($row["iopTrgtOd"])!="" || trim($row["iopTrgtOs"])!=""){
					//do not empty def values;
					$flgEmp=0;
				}
			}

			if($flgEmp==1){
				$sql = "UPDATE tbl_def_val SET iopTrgtOd='".sqlEscStr($trgtOd)."', iopTrgtOs='".sqlEscStr($trgtOs)."' WHERE ptId='".$pId."' AND form_id='".$formId."' ";
				$res = sqlQuery($sql);

				//Update zero
				if(!empty($formId)){
					$sql = "UPDATE tbl_def_val SET iopTrgtOd='".sqlEscStr($trgtOd)."', iopTrgtOs='".sqlEscStr($trgtOs)."' WHERE ptId='".$pId."' AND form_id='0' ";
					$res = sqlQuery($sql);
				}
			}
		}
	}

	public function get_iop_sum($arr_summ_iop){
		$iop_result_od = $iop_result_os = "";
		if(count($arr_summ_iop)>0){
			foreach($arr_summ_iop as $k_summ_iop => $v_summ_iop){
				$tmp_mthd=$k_summ_iop.": ";
				$tmp=trim($v_summ_iop["sum"]["od"]); //Od
				if(!empty($tmp)){
					$tmp = preg_replace('/;(\&nbsp;)*$/', '', $tmp);// remove last ';'
					$iop_result_od.=$tmp_mthd.$tmp."<br/>";
				}
				$tmp=trim($v_summ_iop["sum"]["os"]); //Os
				if(!empty($tmp)){
					$tmp = preg_replace('/;(\&nbsp;)*$/', '', $tmp);// remove last ';'
					$iop_result_os.=$tmp_mthd.$tmp."<br/>";
				}
				$tmp=trim($v_summ_iop["desc"]); //desc
				if(!empty($tmp)){
					$tmp = preg_replace('/;(\&nbsp;)*$/', '', $tmp); // remove last ';'
					$iop_result_od.="Desc ".$tmp_mthd." ".$tmp."<br/>";
					$iop_result_os.="Desc ".$tmp_mthd." ".$tmp."<br/>";
				}
			}
		}
		return array($iop_result_od, $iop_result_os);
	}

	public function save_form(){
		$patientid = $this->pid;
		$elem_formId = $this->fid;
		$arrRet=array();

		//check
		if(empty($patientid) || empty($elem_formId)){ echo json_encode($arrRet); exit();  }

		//Check if Chart is not Finalized or User is Finalizer

		if(!$this->checkFinalizer()){
			/*echo "<script>window.close();</script>";*/
			//exit();
		}else{
			//Check if Chart is not Finalized or User is Finalizer
			$OBJDrawingData = new CLSDrawingData();
			$objImageManipulation = new CLSImageManipulation();
			$oSaveFile = new SaveFile($patientid);
			$oExamXml = new ExamXml();
			$arXmlFiles = $oExamXml->getExamXmlFiles("Gonio");
			$oAdmn = new Admn();
			$oIop = new ChartIop($patientid, $elem_formId);
			$owv = new WorkView();
			$oUserAp = new UserAp();
			$oPt=new Patient($patientid);

			$elem_ci_iop = $_POST["elem_ci_iop"];
			$elem_ci_dilation = $_POST["elem_ci_dilation"];
			$elem_ci_gonio = $_POST["elem_ci_gonio"];
			$elem_ci_OOD = $_POST["elem_ci_OOD"];

			$elem_saveForm = $_POST["elem_saveForm"];
			//$elem_editMode = $_POST["elem_editMode"];
			$posGonio=$wnlGonio=$wnlGonioOd=$wnlGonioOs=0;
			$posDraw=$wnlDraw=$wnlDrawOd=$wnlDrawOs=0;
			$isPositive = $wnl = $noChange = 0;

			//Purge --
			$flg_purge=$flg_purge_iop=0;

			//Anas _db_options
			$arr_db_drops=$oAdmn->get_drop_options_admin();
			$arr_db_anas = $arr_db_drops["anes"];
			$arr_db_dilate = $arr_db_drops["dilate"];
			$arr_db_ood = $arr_db_drops["ood"];

			//Gonio
			if(isset($_POST["elem_purged"]) && !empty($_POST["elem_purged"])){
				//Update
				$sql = "UPDATE chart_gonio
					  SET
					  purged=gonio_id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";
				$row = sqlQuery($sql);
				$flg_purge=1;
				$arrRet["Exam"] = "Gonio";
			}

			//IOP,OOD,Dilation
			if(isset($_POST["elem_purged_IOP"]) && !empty($_POST["elem_purged_IOP"])){
				//Update IOP
				$sql = "UPDATE chart_iop
					  SET
					  purged=iop_id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";
				$row = sqlQuery($sql);

				//Update Dilation
				$sql = "UPDATE chart_dialation
					  SET
					  purged=dia_id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";
				$row = sqlQuery($sql);

				//Update OOD
				$sql = "UPDATE chart_ood
					  SET
					  purged=ood_id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";
				$row = sqlQuery($sql);

				$flg_purge_iop=1;
				$arrRet["Exam"] = "Gonio";
			}
			//Purge --

			//ut_elems ----------------------

			$elem_utElems = $_POST["elem_utElems"];
			$elem_utElems_cur = $_POST["elem_utElems_cur"];
			$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);

			//ut_elems ----------------------

			//summary  --
			if($elem_ci_iop > 0 && empty($flg_purge_iop)){

				$elem_chng_divIopElem_Od = $_POST["elem_chng_divIopElem_Od"];
				$elem_chng_divIopElem_Os = $_POST["elem_chng_divIopElem_Os"];

				$iopGonId = $_POST["elem_iopGonId"];
				$examDate = $_POST["elem_examDate"];
				$statusElem=$_POST["elem_chng_iop"];

				$squeezing = $_POST["squeezing"];
				$unreliable = $_POST["unreliable"];
				$unable = $_POST["unable"];
				$hold_lids = $_POST["hold_lids"];

				$trgtOd=$trgtOs="";

				//Correction Values
				$od_readings = $_POST["elem_od_readings"];
				$od_average = $_POST["elem_od_average"];
				$od_correction_value = $_POST["elem_od_correction_value"];
				$trgtOd = $_POST["trgtOd"];
				$os_readings = $_POST["elem_os_readings"];
				$os_average = $_POST["elem_os_average"];
				$os_correction_value = $_POST["elem_os_correction_value"];
				$trgtOs = $_POST["trgtOs"];



				$cor_date = ($_POST["elem_cor_date"]);
				$descTa=sqlEscStr($_POST["elem_descTa"]);
				$descTp=sqlEscStr($_POST["elem_descTp"]);
				$descTx=sqlEscStr($_POST["elem_descTx"]);
				$descTt=sqlEscStr($_POST["elem_descTt"]);

				$fieldCount = explode(',',$_POST['fieldsCount']);
				$fieldCount1 = implode(',',$fieldCount);
				//$fieldCount = $_POST['fieldsCount'];
				//$fieldCount1 = $fieldCount;

				$multiarray = array();

				$strSumTa_d=$strSumTp_d=$strSumTx_d=$strSumTt_d="";
				$strSumTa_s=$strSumTp_s=$strSumTx_s=$strSumTt_s="";
				$str_descTa=$str_descTp=$str_descTx=$str_descTt="";
				$arr_summ_iop=array();

				$cnt = 0;
				$len=count($fieldCount);

				//echo "<pre>".$len;

				for($pre=1;$pre<=$len;$pre++){
					$tmp_appOd =  "";
				  $tmp_appOs =  "";

					if($cnt==0){
						$indx =  "";
						$indx_dv =  "";
						$getNum = "";
					}else{
						$indx = $cnt;
						$indx_dv = $pre;
						$getNum = $fieldCount[$cnt];
					}

					if($_POST['elem_descTa'.$getNum] == $_POST['elem_descTa'.$getNum.'Prev']){
			      $_POST['elem_descTa'.$getNum] = "";
			    }

					$appOd = $tmp_appOd = $_POST['elem_appOd'.$getNum];
					$appOs = $tmp_appOs = $_POST['elem_appOs'.$getNum];
					$tmp_appMethod = trim($_POST['elem_appMethod'.$getNum]);
					$tmp_appTime = $_POST['elem_appTime'.$getNum];
					$tmp_descTa = $_POST['elem_descTa'.$getNum];

					$tmp_arr = array();
					$tmp_arr["elem_applanation".$indx] = !empty($tmp_appMethod) ? 1 : 0 ;
					$tmp_arr["elem_appMethod".$indx] = $tmp_appMethod;
					$tmp_arr["elem_appOd".$indx] = $appOd;
					$tmp_arr["elem_appOs".$indx] = $appOs;
					$tmp_arr["elem_appTime".$indx] = $tmp_appTime;
					$tmp_arr["elem_descTa".$indx] = $tmp_descTa;
					$multiarray["multiplePressuer".$indx_dv] = $tmp_arr;

					//Summ Presure values--
			    $arr_summ_iop[$tmp_appMethod]["sum"]["od"].=$oIop->getPrsrSum($tmp_appMethod,$appOd,$trgtOd,$tmp_appTime);
			    $arr_summ_iop[$tmp_appMethod]["sum"]["os"].=$oIop->getPrsrSum($tmp_appMethod,$appOs,$trgtOs,$tmp_appTime);

					if(!empty($tmp_descTa)) {
			      $arr_summ_iop[$tmp_appMethod]["desc"].=$tmp_descTa."; ";
			    }

					$cnt++;
				}

				//echo "<pre>";
				//print_r($multiarray);
				//exit();

				$serialMultipPress = serialize($multiarray);
				$serialMultipPress = sqlEscStr($serialMultipPress);

				//multiple pressure

				//summary --
				$sumOdIop = $sumOsIop = $sumOdGon = $sumOsGon = "";
				//IOP
				$iop_result_os = $iop_result_od = "";
				if($squeezing == '1'){
					$tmp = "Squeezing";
					$iop_result_od .= "Pt.".$tmp."/";
					$iop_result_os .= "Pt.".$tmp."/";
				}
				if($unreliable == '1'){
					$tmp = "Unreliable";
					$iop_result_od .= "Pt.".$tmp."/";
					$iop_result_os .= "Pt.".$tmp."/";
				}
				if($unable=='1'){
					$tmp = "Unable";
					$iop_result_od .= "Pt.".$tmp."/";
					$iop_result_os .= "Pt.".$tmp."/";
				}
				if($hold_lids=='1'){
					$tmp = "Hold Lids";
					$iop_result_od .= "Pt.".$tmp."/";
					$iop_result_os .= "Pt.".$tmp."/";
				}
				if(($trgtOd != '')){
					//$iop_result_od .= (!empty($iop_result_od)) ? " Trgt:".$trgtOd."/ " : "";
					$iop_result_od .= " Trgt:".$trgtOd."/ ";
				}
				if(($trgtOs != '')){
					//$iop_result_os .= (!empty($iop_result_os)) ? " Trgt:".$trgtOs."/ " : "" ;
					$iop_result_os .= " Trgt:".$trgtOs."/ ";
				}
				//Correction Values --
				if( !empty($od_readings) || !empty($od_correction_value)){
					$iop_result_od .= "Pachy: ".$od_readings." ".$od_correction_value;
				}
				if( !empty($od_readings) || !empty($os_correction_value)){
					$iop_result_os .= "Pachy: ".$os_readings." ".$os_correction_value;
				}
				//Correction Values --

				//BR
				$iop_result_od .= (!empty($iop_result_od)) ? "<br>" : "";
				$iop_result_os .= (!empty($iop_result_os)) ? "<br>" : "" ;

				//iopsumm
				$tmp_arsum = $this->get_iop_sum($arr_summ_iop);
				$iop_result_od .= $tmp_arsum[0];
				$iop_result_os .= $tmp_arsum[1];

				/*
				$arrTLbl = array("T<sub>1</sub>: ","T<sub>2</sub>: ","T<sub>3</sub>: ","T<sub>4</sub>: ");
				$arrTOd = array($strSumTa_d,$strSumTp_d,$strSumTx_d,$strSumTt_d);
				$arrTOs = array($strSumTa_s,$strSumTp_s,$strSumTx_s,$strSumTt_s);
				$arrTdesc = array($str_descTa,$str_descTp,$str_descTx,$str_descTt);

				for($i=0;$i<4;$i++){

					$tmp=trim($arrTOd[$i]); //Od
					if(!empty($tmp)){
						$tmp = preg_replace('/;(\&nbsp;)*$/', '', $tmp);// remove last ';'
						$iop_result_od.=$arrTLbl[$i].$tmp."<br/>";
					}
					$tmp=trim($arrTOs[$i]); //Os
					if(!empty($tmp)){
						$tmp = preg_replace('/;(\&nbsp;)*$/', '', $tmp); // remove last ';'
						$iop_result_os.=$arrTLbl[$i].$tmp."<br/>";
					}
					$tmp=trim($arrTdesc[$i]); //desc
					if(!empty($tmp)){
						$tmp = preg_replace('/;(\&nbsp;)*$/', '', $tmp); // remove last ';'
						$iop_result_od.="Desc ".$arrTLbl[$i]." ".$tmp."<br/>";
						$iop_result_os.="Desc ".$arrTLbl[$i]." ".$tmp."<br/>";
					}

				}
				*/
				//End for loop

				$sumOdIop = $iop_result_od;
				$sumOsIop = $iop_result_os;

				$sumOdIop = sqlEscStr($sumOdIop);
				$sumOsIop = sqlEscStr($sumOsIop);

				//Anesthetic --
				$time_up = $_POST["time_up"];
				$dt_up = $_POST["dt_up"];
				$maxAnes = count($time_up);

				foreach($arr_db_anas as $key_da => $val_da){
					$val_da_el_nm=mk_var_nm($val_da,"anes");
					if(isset($_POST[$val_da_el_nm])){
						$$val_da_el_nm = $_POST[$val_da_el_nm];
						if($maxAnes<max($$val_da_el_nm))$maxAnes = max($$val_da_el_nm);
					}else{
						$$val_da_el_nm = array();
					}
				}

				if(isset($_POST["anes_other"])){
					$anes_other = $_POST["anes_other"];
				}else{
					$anes_other = array();
				}

				$spAnesTime="";
				$sumAnes = $sumAnes_od = $sumAnes_os = "";
				$arrAnes=array();
				for($i=0;$i<$maxAnes;$i++){
					$j=$i+1;

					$tmpAnes ="";

					foreach($arr_db_anas as $key_da => $val_da){
						$val_da_el_nm=mk_var_nm($val_da,"anes");
						if(in_array($j,$$val_da_el_nm)) $tmpAnes .=$val_da.",";
					}
					$tmpAnes = trim($tmpAnes,",");
					//--

					$tmp_anes_other = trim($anes_other[$i]);
					if(empty($tmp_anes_other)||($tmp_anes_other=="Other")){
						$tmp_anes_other="";
					}

					if(!empty($tmpAnes)||!empty($tmp_anes_other)){
						$c=count($arrAnes);
						$arrAnes[$c]["anes"]=$tmpAnes;
						$arrAnes[$c]["time"]=$time_up[$c];
						$arrAnes[$c]["dt"]=$dt_up[$c];
						$arrAnes[$c]["other"]=$tmp_anes_other;
						$arrAnes[$c]["eye"]=$_POST["aneseye".$j];
						//tt
						$t = "";
						if(!empty($arrAnes[$c]["anes"])){
							$t .= $arrAnes[$c]["anes"];
						}

						if(!empty($arrAnes[$c]["other"])){
							if(!empty($t)){$t .= ",";}
							$t .= "".$arrAnes[$c]["other"];
						}

						$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
						$t = str_replace(",",", ",$t);

						if($arrAnes[$c]["eye"]!="OS"){
							$tmp = trim($arrAnes[$c]["dt"]);
							if(!empty($tmp)){ $tmp = $tmp." "; }
							$sumAnes_od .= $t." ".$arrAnes[$c]["eye"]." ".$tmp.trim($arrAnes[$c]["time"])."<br/>";
						}
						if($arrAnes[$c]["eye"]!="OD"){
							$tmp = trim($arrAnes[$c]["dt"]);
							if(!empty($tmp)){ $tmp = $tmp." "; }
							$sumAnes_os .= $t." ".$arrAnes[$c]["eye"]." ".$tmp.trim($arrAnes[$c]["time"])."<br/>";
						}

						//time
						$spAnesTime = trim($arrAnes[$c]["eye"]." ".$arrAnes[$c]["time"]);
						//

					}
				}

				$strAnes=serialize($arrAnes);
				//Anesthetic --

				//check iop
				$cQry = "select last_opr_id, uid, sumOdIop, sumOsIop,modi_note_iopArr,sumAnesOd,sumAnesOs,modi_note_AnestheticArr, exam_date   FROM chart_iop WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged='0' ";
				$row = sqlQuery($cQry);

				if($row == false){
					$last_opr_id = $_SESSION["authId"];
					$elem_editMode = "0";
				}else{
					$elem_editMode = "1";
					$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
					//Modifying Notes----------------
					$modi_note_IopOd=$owv->getModiNotes($row["sumOdIop"],0,$sumOdIop,0,$row["uid"]);
					$modi_note_IopOs=$owv->getModiNotes($row["sumOsIop"],0,$sumOsIop,0,$row["uid"]);

					$seri_modi_note_iopArr=$owv->getModiNotesArr($row["sumOdIop"],$sumOdIop,$last_opr_id,'OD',$row["modi_note_iopArr"],$row['exam_date']);
					$seri_modi_note_iopArr=$owv->getModiNotesArr($row["sumOsIop"],$sumOsIop,$last_opr_id,'OS',$seri_modi_note_iopArr,$row['exam_date']);

					$modi_note_AnestheticOd=$owv->getModiNotes($row["sumAnesOd"],0,$sumAnes_od,0,$row["uid"]);
					$modi_note_AnestheticOs=$owv->getModiNotes($row["sumAnesOs"],0,$sumAnes_os,0,$row["uid"]);

					$seri_modi_note_AnestheticArr=$owv->getModiNotesArr($row["sumAnesOd"],$sumAnes_od,$last_opr_id,'OD',$row["modi_note_AnestheticArr"],$row['exam_date']);
					$seri_modi_note_AnestheticArr=$owv->getModiNotesArr($row["sumAnesOs"],$sumAnes_os,$last_opr_id,'OS',$seri_modi_note_AnestheticArr,$row['exam_date']);
					//Modifying Notes----------------
				}

				if($elem_editMode == "0"){	// Insert
					$sql = "INSERT INTO chart_iop set
						  patient_id = '".$patientid."',
						  form_id = '".$elem_formId."',
						  iopGon_od='$elem_iopGon_od',
						  iopGon_os='$elem_iopGon_os',
						  squeezing='$squeezing',
						  unreliable='$unreliable',
						  unable='$unable',
						  trgtOd='".sqlEscStr($trgtOd)."',
						  trgtOs='".sqlEscStr($trgtOs)."',".
						  "tetracaine='',
						  flourocaine='',
						  alcaine='',".

						  "exam_date='$examDate',
						  iop_time = '$time_up',
						  isPositive = '$isPositive',
						  sumOdIop = '$sumOdIop',
						  sumOsIop = '$sumOsIop',".

						  "descTa = '$descTa',
						  descTp = '$descTp',
						  descTx = '$descTx',
						  sideIop = '$sideIop',".

						  "desc_ig = '$desc_ig',".

						  "multiple_pressure = '".$serialMultipPress."',
						   fieldCount = '".$fieldCount1."',
						   anesthetic = '".$strAnes."',
						   uid = '".$_SESSION["authId"]."',
						   hold_lids='".$hold_lids."',
						   statusElem='".$statusElem."',
						   ut_elem = '".$ut_elem."',
						   sumAnesOd = '".$sumAnes_od."',
						   sumAnesOs = '".$sumAnes_os."',
						   spAnesTime = '".$spAnesTime."',
						   last_opr_id = '".$last_opr_id."'
						  ";
					$insertId = sqlInsert($sql);

				}
				else if($elem_editMode == "1"){
					$sql = "UPDATE chart_iop SET
						  iopGon_od='$elem_iopGon_od',
						  iopGon_os='$elem_iopGon_os',
						  squeezing='$squeezing',
						  unreliable='$unreliable',".
						  //"examined_no_change='$noChange',".
						  "unable='$unable',
						  trgtOd='".sqlEscStr($trgtOd)."',
						  trgtOs='".sqlEscStr($trgtOs)."',
						  tetracaine='',
						  flourocaine='',
						  alcaine='',".

						  "wnl='$wnl',".
						  "exam_date='$examDate' ,
						  iop_time = '$time_up',
						  isPositive = '$isPositive',
						  sumOdIop = '$sumOdIop',
						  sumOsIop = '$sumOsIop',".

						  "descTa = '$descTa',
						  descTp = '$descTp',
						  descTx = '$descTx',
						  sideIop = '$sideIop',".

						  "desc_ig = '$desc_ig',".

						  "multiple_pressure = '".$serialMultipPress."',
						   fieldCount = '".$fieldCount1."',
						   anesthetic = '".$strAnes."',
						   uid = '".$_SESSION["authId"]."',
						   hold_lids='".$hold_lids."',
						   statusElem='".$statusElem."',
						   ut_elem = '".$ut_elem."',
						   sumAnesOd = '".$sumAnes_od."',
						   sumAnesOs = '".$sumAnes_os."',
						   spAnesTime = '".$spAnesTime."',
							 ".
						   "modi_note_iopArr = '".sqlEscStr($seri_modi_note_iopArr)."',
						   modi_note_AnestheticArr = '".sqlEscStr($seri_modi_note_AnestheticArr)."',
						   last_opr_id = '".$last_opr_id."'
						  WHERE form_id = '".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";

					//echo ($sql);
					$res = sqlQuery($sql);
					$insertId = $iopGonId;
				}

				//Set Change Date Arc Rec --
				$this->setChangeDtArcRec("chart_iop");
				//Set Change Date Arc Rec --

				//Save IOP Def Vals
				if(!empty($trgtOd) || !empty($trgtOs)){
					$this->saveIopTrgt($trgtOd,$trgtOs,$elem_formId);

					//VF
					//Update VF
					$sql = "UPDATE vf SET iopTrgtOd='".sqlEscStr($trgtOd)."', iopTrgtOs='".sqlEscStr($trgtOs)."'
								WHERE formId = '".$elem_formId."' AND patientId='".$patientid."'  ";
					$res = sqlQuery($sql);

					//NFA
					$sql = "UPDATE nfa SET iopTrgtOd='".sqlEscStr($trgtOd)."', iopTrgtOs='".sqlEscStr($trgtOs)."'
							WHERE form_id = '".$elem_formId."' AND patient_id='".$patientid."'  ";
					$res = sqlQuery($sql);

				}else{//if empty targt values
					$this->remIopTrgtDefVal($trgtOd,$trgtOs,$elem_formId);
				}

				//Correction Values --------
				$oCCorVal = new ChartCorrectionValues($patientid, $elem_formId);
				$arr = array("elem_formId" => $elem_formId, "elem_od_readings" => $od_readings,
							"elem_od_average" => $od_average, "elem_od_correction_value" => $od_correction_value,
							"elem_os_readings" => $os_readings, "elem_os_average" => $os_average,
							"elem_os_correction_value" => $os_correction_value, "patientid" => $patientid,
							"elem_cor_date" => $cor_date
							);
				$oCCorVal->saveCorrectionValues($arr);

				if( !empty($od_correction_value) || !empty($os_correction_value) ){
					//check if Pachy is made and syncronize correction values
					//check
					$sql = "SELECT * FROM pachy WHERE formId='".$elem_formId."' AND patientId='".$patientid."' ";
					$row = sqlQuery($sql);
					if($row != false){
						//Update
						$sql = "UPDATE pachy SET ".
							 "pachy_od_readings = '".$od_readings."', ".
							 "pachy_od_average='".$od_average."', ".
							 "pachy_od_correction_value = '".$od_correction_value."', ".
							 "pachy_os_readings = '".$os_readings."', ".
							 "pachy_os_average = '".$os_average."', ".
							 "pachy_os_correction_value = '".$os_correction_value."' ".
							 "WHERE formId = '".$elem_formId."' AND patientId='".$patientid."' ";
						$row = sqlQuery($sql);
					}

					//chartPtDiag
					$sql = "SELECT * FROM chart_ptPastDiagnosis WHERE patient_id='".$patientid."' ";
					$row = sqlQuery($sql);
					if( $row != false ){
						$ptPastPachy = "";
						$arr=array();
						$arr["od_readings"]=$od_readings;
						$arr["od_average"]=$od_average;
						$arr["od_correction_value"]=$od_correction_value;
						$arr["os_readings"]=$os_readings;
						$arr["os_average"]=$os_average;
						$arr["os_correction_value"]=$os_correction_value;
						$arr["cor_date"]=$cor_date;
						$ptPastPachy = serialize($arr);
						$sql = "UPDATE chart_ptPastDiagnosis SET pachy = '".sqlEscStr($ptPastPachy)."' WHERE patient_id='".$patientid."' ";
						$res = sqlQuery($sql);
					}

					//check if A/Scan is made and syncronize correction values
					//check
					$sql = "SELECT * FROM surgical_tbl WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' ";
					$row = sqlQuery($sql);
					if($row != false){
						$pachymetryValOD = !empty($od_average) ? $od_average : $od_readings ;
						$pachymetryValOS = !empty($os_average) ? $os_average : $os_readings ;

						//Update
						$sql = "UPDATE surgical_tbl SET ".
							 "pachymetryValOD = '".$pachymetryValOD."', ".
							 "pachymetryCorrecOD = '".$od_correction_value."', ".
							 "pachymetryValOS = '".$pachymetryValOS."', ".
							 "pachymetryCorrecOS = '".$os_correction_value."' ".
							 "WHERE form_id = '".$elem_formId."' AND patient_id='".$patientid."' ";
						$row = sqlQuery($sql);
					}

					//check if IOL_Master is made and syncronize correction values
					//check
					$sql = "SELECT iol_master_id FROM iol_master_tbl WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' ";
					$row = sqlQuery($sql);
					if($row != false){
						$pachymetryValOD = !empty($od_average) ? $od_average : $od_readings ;
						$pachymetryValOS = !empty($os_average) ? $os_average : $os_readings ;

						//Update
						$sql = "UPDATE iol_master_tbl SET ".
							 "pachymetryValOD = '".$pachymetryValOD."', ".
							 "pachymetryCorrecOD = '".$od_correction_value."', ".
							 "pachymetryValOS = '".$pachymetryValOS."', ".
							 "pachymetryCorrecOS = '".$os_correction_value."' ".
							 "WHERE form_id = '".$elem_formId."' AND patient_id='".$patientid."' ";
						$row = sqlQuery($sql);
					}

				}
				//Correction Values --------

				//Summery
				$arrRet["Exam"] = "iop";
				/*
				$exm = "Iop";
				$arrRet[$exm]["Exam"] = $exm;
				$arrRet[$exm]["isPositive"] = "0";
				$arrRet[$exm]["wnl"] = "0";
				$arrRet[$exm]["NC"] = "";
				$arrRet[$exm]["Draw"] = "null";
				$arrRet[$exm]["arExamDone"] = "";
				$arrRet[$exm]["AddExam"] = "";
				$arrRet[$exm]["FormId"] = $elem_formId;
				*/

			}//End IOP

			//Gonio
			if($elem_ci_gonio > 0  && empty($flg_purge)){
				//Oject
				$oGonio = new Gonio($patientid,$elem_formId);
				$elem_chng_divIop_Od = $_POST["elem_chng_divIop_Od"];
				$elem_chng_divIop_Os = $_POST["elem_chng_divIop_Os"];
				$elem_chng_divIop3_Od = $_POST["elem_chng_divIop3_Od"];
				$elem_chng_divIop3_Os = $_POST["elem_chng_divIop3_Os"];

				$arrSe = array("elem_chng_divIop_Od"=>$elem_chng_divIop_Od, "elem_chng_divIop_Os"=>$elem_chng_divIop_Os,
								"elem_chng_divIop3_Od"=>$elem_chng_divIop3_Od, "elem_chng_divIop3_Os"=>$elem_chng_divIop3_Os );
				$statusElem = $this->getStrSe($arrSe);

				//Gonio -------
				//if(!empty($elem_chng_divIop_Od) || !empty($elem_chng_divIop_Os)){
					//if(!empty($elem_chng_divIop_Od)){
						$menuName = "iopGonOd";
						$menuFilePath = $arXmlFiles["od"]; //dirname(__FILE__)."/xml/iopGon_od.xml";
						$elem_iopGon_od = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
						$wnlGonioOd = $elem_wnlIopOd=$_POST["elem_wnlGonioOd"];
					//}

					//if(!empty($elem_chng_divIop_Os)){
						$menuName = "iopGonOs";
						$menuFilePath = $arXmlFiles["os"]; //dirname(__FILE__)."/xml/iopGon_os.xml";
						$elem_iopGon_os = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
						$wnlGonioOs = $elem_wnlIopOs=$_POST["elem_wnlGonioOs"];
					//}

					$posGonio=$_POST["elem_posGonio"];
					$wnlGonio=(!empty($wnlGonioOd) && !empty($wnlGonioOs)) ? "1" : "0";
					$noChange = $_POST["elem_ncGonio"];
				//}
				//Gonio -------

				//Draw -------
				//if(!empty($elem_chng_divIop3_Od) || !empty($elem_chng_divIop3_Os)){
				//	if(!empty($elem_chng_divIop3_Od)){
						$wnlDrawOd=$_POST["elem_wnlDrawOd"];
						$Drawing_OD = $_POST["elem_Drawing_OD"];
						$odIopGon = sqlEscStr($_POST["od_iop_gon"]);
				//	}

				//	if(!empty($elem_chng_divIop3_Os)){
						$wnlDrawOs=$_POST["elem_wnlDrawOs"];
						$Drawing_OS = $_POST["elem_Drawing_OS"];
						$osIopGon = sqlEscStr($_POST["os_iop_gon"]);
				//	}

					$posDraw=$_POST["elem_posDraw"];
					$wnlDraw=(!empty($wnlDrawOd) && !empty($wnlDrawOs)) ? "1" : "0"; //$_POST["elem_wnlDraw"];
					$noChange_draw = $_POST["elem_ncDraw"];

				//}
				//Draw -------

				$desc_ig=sqlEscStr($_POST["elem_desc_ig"]);
				$wnl = (!empty($wnlGonio) && !empty($wnlDraw)) ? "1" : "0"; // $_POST["elem_wnl"];
				$isPositive = (!empty($posGonio) || !empty($posDraw)) ? "1" : "0" ; //$_POST["elem_isPositive"];
				$elem_notApplicable = $_POST["elem_notApplicable"];

				//Gonio
				$strExamsAllOd = $strExamsAllOs = "";
				$iop_od = $elem_iopGon_od;
				$arrTemp = $this->getExamSummary($iop_od);
				$sumOdGon = sqlEscStr($arrTemp["Summary"]);
				$arrExmDone_od = $arrTemp["ExmDone"];
				if(!empty($elem_chng_divIop_Od)){
					$strExamsAllOd .= $oUserAp->refineByConsoleSymp("Gonio",$arrExmDone_od,$sumOdGon);
				}

				$iop_os = $elem_iopGon_os;
				$arrTemp = $this->getExamSummary($iop_os);
				$sumOsGon = sqlEscStr($arrTemp["Summary"]);
				$arrExmDone_os = $arrTemp["ExmDone"];
				if(!empty($elem_chng_divIop_Os)){
					$strExamsAllOs .= $oUserAp->refineByConsoleSymp("Gonio",$arrExmDone_os,$sumOsGon);
				}

				//Set Gonio Date
				$examDateGonio="";
				if(!empty($sumOdGon) || !empty($sumOsGon) ){
					$examDateGonio= date('Y-m-d H:i:s');
				}

				//
				$elem_iopGon_od = sqlEscStr($elem_iopGon_od);
				$elem_iopGon_os = sqlEscStr($elem_iopGon_os);

				//check
				$cQry = "select last_opr_id,uid,gonio_id,gonio_od_summary,gonio_os_summary,wnlOd,wnlOs,modi_note_Arr,wnl_value,examDateGonio  FROM chart_gonio WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged='0'  ";
				$row = sqlQuery($cQry);
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				if($row == false){
					$elem_editModeDial = "0";
				}else{
					$insertIdGonio = $goIDExam = $row["gonio_id"];
					$elem_editModeDial = "1";

					//Modifying Notes----------------
						$modi_note_GonioOd=$owv->getModiNotes($row["gonio_od_summary"],$row["wnlOd"],$sumOdGon,$wnlGonioOd,$row["uid"], $row["wnl_value"]);
						$modi_note_GonioOs=$owv->getModiNotes($row["gonio_os_summary"],$row["wnlOs"],$sumOsGon,$wnlGonioOs,$row["uid"], $row["wnl_value"]);

						$seri_modi_note_gonioArr = $owv->getModiNotesArr($row["gonio_od_summary"],$sumOdGon,$last_opr_id,'OD',$row["modi_note_Arr"],$row['examDateGonio']);
						$seri_modi_note_gonioArr = $owv->getModiNotesArr($row["gonio_os_summary"],$sumOsGon,$last_opr_id,'OS',$seri_modi_note_gonioArr,$row['examDateGonio']);
					//Modifying Notes----------------
				}

				if($elem_editModeDial == "0"){ // Insert

					// Save WNL value
					$wnl_value = $this->getExamWnlStr("Gonio");

					$sql = "INSERT INTO chart_gonio ".
						 "(	gonio_id,gonio_od_summary,gonio_os_summary,";
							if(empty($_REQUEST["hidBlEnHTMLDrawing"]) == true){
								$sql .= "gonio_od_drawing,";
								$sql .= "drawing_insert_update_from,";
							}
							else{
								$sql .= "drawing_insert_update_from,";
							}
							$sql .= "gonio_os_drawing,gonio_od_desc,gonio_os_desc,
							form_id,patient_id,examined_no_change,wnl,isPositive,noChange_drawing,
							examDateGonio,wnlDrawOd,wnlDrawOs,posGonio,
							posDraw,wnlGonio,wnlDraw,
							desc_ig,iopGon_od,iopGon_os,wnlOd,wnlOs,uid,statusElem,ut_elem,last_opr_id, wnl_value )".
						"VALUES ".
						"( 	NULL,'".$sumOdGon."','".$sumOsGon."',";
							if(empty($_REQUEST["hidBlEnHTMLDrawing"]) == true){
								$sql .= "'".$Drawing_OD."',";
								$sql .= "'0',";
							}
							else{
								$sql .= "'1',";
							}
							$sql .= "'".$Drawing_OS."','".$odIopGon."','".$osIopGon."',
							'".$elem_formId."','".$patientid."','".$noChange."','".$wnl."','".$isPositive."','".$noChange_draw."',
							'".$examDateGonio."','".$wnlDrawOd."','".$wnlDrawOs."','".$posGonio."',
							'".$posDraw."','".$wnlGonio."','".$wnlDraw."',
							'".$desc_ig."','".$elem_iopGon_od."','".$elem_iopGon_os."','".$wnlGonioOd."','".$wnlGonioOs."',
							'".$_SESSION["authId"]."','".$statusElem."', '".$ut_elem."', '".$last_opr_id."', '".sqlEscStr($wnl_value)."' ".
						")";
					$insertIdGonio = sqlInsert($sql);

					//$flagCFD=1;
					//$flagCFD_drw1=0;
					$arrCFD_ids=array();
					//---Draw
					if(isset($_REQUEST["hidBlEnHTMLDrawing"]) == true && empty($_REQUEST["hidBlEnHTMLDrawing"]) == false && $_REQUEST["hidBlEnHTMLDrawing"] == "1" && (int)$insertIdGonio > 0){
						for($intTempDrawCount = 0; $intTempDrawCount < 2; $intTempDrawCount++){
							if($_REQUEST["hidDrawingChangeYesNo".$intTempDrawCount] == "yes"){
								$arrDrawingData = array();
								$arrDrawingData["imagePath"] = $oSaveFile->getUploadDirPath(); //dirname(__FILE__)."/../main/uploaddir";
								$arrDrawingData["hidRedPixel"] = $_REQUEST["hidRedPixel".$intTempDrawCount];
								$arrDrawingData["hidGreenPixel"] = $_REQUEST["hidGreenPixel".$intTempDrawCount];
								$arrDrawingData["hidBluePixel"] = $_REQUEST["hidBluePixel".$intTempDrawCount];
								$arrDrawingData["hidAlphaPixel"] = $_REQUEST["hidAlphaPixel".$intTempDrawCount];
								$arrDrawingData["hidImageCss"] = $_REQUEST["hidImageCss".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestImageP"] = $_REQUEST["hidDrawingTestImageP".$intTempDrawCount];
								$arrDrawingData["patId"] = $patientid;
								$arrDrawingData["hidCanvasImgData"] = $_REQUEST["hidCanvasImgData".$intTempDrawCount];
								$drawingFileName = "/IOP_Gonio_idoc_drawing_".date("YmdHsi")."_".session_id()."_".$intTempDrawCount.".png";
								$arrDrawingData["drawingFileName"] = $drawingFileName;
								$arrDrawingData["drawingFor"] = "IOP_GONIO";
								$arrDrawingData["drawingForMasterId"] = $insertIdGonio;
								$arrDrawingData["formId"] = $elem_formId;
								$arrDrawingData["hidDrawingTestName"] = $_REQUEST["hidDrawingTestName".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestId"] = $_REQUEST["hidDrawingTestId".$intTempDrawCount];
								$arrDrawingData["hidImagesData"] = $_REQUEST["hidImagesData".$intTempDrawCount];
								$arrDrawingData["hidDrawingId"] = (int)$_REQUEST["hidIOPDrawingId".$intTempDrawCount];
								$arrDrawingData["examMasterTable"] = "chart_gonio";
								$arrDrawingData["examMasterTablePriColumn"] = "gonio_id";
								$arrDrawingData["drwNE"] = $_REQUEST["elem_drwNE".$intTempDrawCount];
								$arrDrawingData["hidDrwDataJson"] = $_REQUEST["hidDrwDataJson".$intTempDrawCount];
								//pre($arrDrawingData);
								$OBJDrawingData->insertDrawingData($arrDrawingData);
								//$flagCFD=0;
							}else{

							//Check old drawing exists for carry forward
							if(!empty($_POST["hidIOPDrawingId".$intTempDrawCount])){
								$arrCFD_ids[] = $_POST["hidIOPDrawingId".$intTempDrawCount];
								//$flagCFD_drw1=1;
							}

							}

						}
						$arrIDocId = array();
						$strIDocId = "";
						$qryGetIDocIdInMasetr = "select id from ".constant("IMEDIC_SCAN_DB").".idoc_drawing where drawing_for = 'IOP_GONIO' and drawing_for_master_id = '".$insertIdGonio."'
													and patient_id = '".$patientid."' and patient_form_id = '".$elem_formId."' ";
						$rsGetIDocIdInMasetr = imw_query($qryGetIDocIdInMasetr);
						if(imw_num_rows($rsGetIDocIdInMasetr) > 0){
							while($rowGetIDocIdInMasetr = imw_fetch_array($rsGetIDocIdInMasetr)){
								$arrIDocId[] = $rowGetIDocIdInMasetr["id"];
							}
						}
						if(count($arrIDocId) > 0){
							$strIDocId = implode(",", $arrIDocId);
						}
						$qryUpdateIOPGonioIDoc = "update chart_gonio set idoc_drawing_id = '".$strIDocId."' where gonio_id = '".$insertIdGonio."' ";
						$rsUpdateIOPGonioIDoc = imw_query($qryUpdateIOPGonioIDoc);
					}

					//if(!empty($_POST["elem_gonioId_LF"])&&!empty($flagCFD_drw1) && $flagCFD==1){ // Check if Last visit Drawing exists
					if(!empty($_POST["elem_gonioId_LF"]) && count($arrCFD_ids)>0){ // Check if Last visit Drawing exists
						// Carry Forward iDOC Draw : This is done because drawing is not saved when not touched but we need to carry forward to display.Drawing status will be grey.
						$arrIN = array();
						$arrIN["pid"]=$patientid;
						$arrIN["formId"]=$elem_formId;
						$arrIN["examId"]=$insertIdGonio;
						$arrIN["exam"]="IOP_GONIO";
						$arrIN["examIdLF"]=$_POST["elem_gonioId_LF"];
						$arrIN["strDrwIdsLF"]=implode(",", $arrCFD_ids);
						$arrIN["examMasterTable"]="chart_gonio";
						$arrIN["examMasterTablePriColumn"]="gonio_id";
						$OBJDrawingData->carryForward($arrIN);
						//
					}
					//---Draw

				}else if($elem_editModeDial == "1"){ //Update
					//$insertIdGonio = $row["gonio_id"];

					$sql = "UPDATE chart_gonio ".
						 "SET ".
						 "
						  iopGon_od='".$elem_iopGon_od."',
						  iopGon_os='".$elem_iopGon_os."',
						  examined_no_change='".$noChange."',
						  wnl='".$wnl."',
						  isPositive = '".$isPositive."',
						  gonio_od_summary = '".$sumOdGon."',
						  gonio_os_summary = '".$sumOsGon."',
						  wnlOd='".$wnlGonioOd."',
						  wnlOs='".$wnlGonioOs."',";
							if(empty($_REQUEST["hidBlEnHTMLDrawing"]) == true){
								$sql .= "gonio_od_drawing = '".$Drawing_OD."',";
								$sql .= "drawing_insert_update_from='0',";
							}
							else{
								$sql .= "drawing_insert_update_from='1',";
							}
						  $sql .= "gonio_od_desc = '".$odIopGon."',
						  gonio_os_desc = '".$osIopGon."',
						  noChange_drawing = '".$noChange_draw."',
						  examDateGonio = '".$examDateGonio."',
						  desc_ig = '".$desc_ig."',
						  wnlDrawOd = '".$wnlDrawOd."',
						  wnlDrawOs = '".$wnlDrawOs."',
						  wnlGonio = '".$wnlGonio."',
						  wnlDraw = '".$wnlDraw."',
						  posGonio = '".$posGonio."',
						  posDraw = '".$posDraw."',
						  uid = '".$_SESSION["authId"]."',
						  statusElem='".$statusElem."',
						  ut_elem = '".$ut_elem."', ".
						 "modi_note_Arr = '".sqlEscStr($seri_modi_note_gonioArr)."', last_opr_id = '".$last_opr_id."'
						  WHERE form_id = '".$elem_formId."' AND patient_id='".$patientid."' AND purged='0' ";
					$res = sqlQuery($sql);
					if($goIDExam > 0){
						$insertIdGonio = $goIDExam;
					}
					if(isset($_REQUEST["hidBlEnHTMLDrawing"]) == true && empty($_REQUEST["hidBlEnHTMLDrawing"]) == false && $_REQUEST["hidBlEnHTMLDrawing"] == "1" && (int)$insertIdGonio > 0){
						$recordModiDateDrawing=0;
						$arr_updrw_ids=array();
						for($intTempDrawCount = 0; $intTempDrawCount < 2; $intTempDrawCount++){
							if($_REQUEST["hidDrawingChangeYesNo".$intTempDrawCount] == "yes"){

								$arrDrawingData = array();
								$arrDrawingData["imagePath"] = $oSaveFile->getUploadDirPath(); //dirname(__FILE__)."/../main/uploaddir";
								$arrDrawingData["hidRedPixel"] = $_REQUEST["hidRedPixel".$intTempDrawCount];
								$arrDrawingData["hidGreenPixel"] = $_REQUEST["hidGreenPixel".$intTempDrawCount];
								$arrDrawingData["hidBluePixel"] = $_REQUEST["hidBluePixel".$intTempDrawCount];
								$arrDrawingData["hidAlphaPixel"] = $_REQUEST["hidAlphaPixel".$intTempDrawCount];
								$arrDrawingData["hidImageCss"] = $_REQUEST["hidImageCss".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestImageP"] = $_REQUEST["hidDrawingTestImageP".$intTempDrawCount];
								$arrDrawingData["patId"] = $patientid;
								$arrDrawingData["hidCanvasImgData"] = $_REQUEST["hidCanvasImgData".$intTempDrawCount];
								$drawingFileName = "/IOP_Gonio_idoc_drawing_".date("YmdHsi")."_".session_id()."_".$intTempDrawCount.".png";
								$arrDrawingData["drawingFileName"] = $drawingFileName;
								$arrDrawingData["drawingFor"] = "IOP_GONIO";
								$arrDrawingData["drawingForMasterId"] = $insertIdGonio;
								$arrDrawingData["formId"] = $elem_formId;
								$arrDrawingData["hidDrawingTestName"] = $_REQUEST["hidDrawingTestName".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestId"] = $_REQUEST["hidDrawingTestId".$intTempDrawCount];
								$arrDrawingData["hidImagesData"] = $_REQUEST["hidImagesData".$intTempDrawCount];
								$arrDrawingData["hidDrawingId"] = (int)$_REQUEST["hidIOPDrawingId".$intTempDrawCount];
								$arrDrawingData["examMasterTable"] = "chart_gonio";
								$arrDrawingData["examMasterTablePriColumn"] = "gonio_id";
								$arrDrawingData["drwNE"] = $_REQUEST["elem_drwNE".$intTempDrawCount];
								$arrDrawingData["hidDrwDataJson"] = $_REQUEST["hidDrwDataJson".$intTempDrawCount];
								//pre($arrDrawingData,1);
								$arrDrawingData["hidDrawingId"] = $OBJDrawingData->updateDrawingData($arrDrawingData);

								if(!empty($arrDrawingData["hidDrawingId"])){
									$recordModiDateDrawing=$arrDrawingData["hidDrawingId"];
									$arr_updrw_ids[]=$arrDrawingData["hidDrawingId"];
								}
							}else{
								if(!empty($_POST["hidIOPDrawingId".$intTempDrawCount])){
									$arr_updrw_ids[] = $_POST["hidIOPDrawingId".$intTempDrawCount];
								}
							}
						}

						//delete records of previous visit if any --
						$OBJDrawingData->deleteNoSavedDrwing(array($patientid, $elem_formId,$insertIdGonio,"IOP_GONIO"), $arr_updrw_ids);
						//--

						//form Id
						list($strIDocId, $str_row_modify_Draw)=$OBJDrawingData->getExamDocids(array($patientid, $elem_formId,$insertIdGonio,"IOP_GONIO",$recordModiDateDrawing));


						$qryUpdateIOPGonioIDoc = "update chart_gonio set idoc_drawing_id = '".$strIDocId."' ".$str_row_modify_Draw." where gonio_id = '".$insertIdGonio."' ";
						$rsUpdateIOPGonioIDoc = imw_query($qryUpdateIOPGonioIDoc);

					}

				}

				//Set Change Date Arc Rec --
				$this->setChangeDtArcRec("chart_gonio");
				//Set Change Date Arc Rec --
				//combine
				$strExamsAll .= $this->combineExamFindings($strExamsAllOd, $strExamsAllOs);
				$strExamsAll = $this->makeArrString($strExamsAll);

				//
				$arrRet["Exam"] = "Gonio";
				$arrRet["arExamDone"] = $strExamsAll;
				/*
				$exm = "Gonio";
				$arrRet[$exm]["Exam"] = "Gonio";
				$arrRet[$exm]["isPositive"] = $isPositive;
				$arrRet[$exm]["wnl"] = $wnl;
				$arrRet[$exm]["NC"] = $noChange;
				$arrRet[$exm]["Draw"] = "null";
				$arrRet[$exm]["arExamDone"] = $strExamsAll;
				$arrRet[$exm]["AddExam"] = $elem_editMode;
				$arrRet[$exm]["FormId"] = $elem_formId;
				*/

			}//End Gonio

			// Dialation
			if($elem_ci_dilation > 0  && empty($flg_purge_iop) ){
				$oCDilation = new ChartDilation($patientid, $elem_formId);

				$elem_sideIop = ""; //$_POST['elem_sideIop'];
				$revEyes = $_POST["elem_revEyes"];
				$noDilation = $_POST["elem_noDilation"];

				$patientwarneds=$_POST["patientwarneds"];
				$patientnot_Driving=$_POST["patientnot_Driving"];
				$patientAllergic=$_POST["patientAllergic"];
				$allergicComments=$_POST["allergicComments"];
				$unableDilation=$_POST["unableDilation"];
				$unableDilateComments=$_POST["unableDilateComments"];


				$Other = $Other;
				$statusElem = $_POST["elem_chng_dilation"];

				//Permision --
				$permiBy=""; $permiBy_sum="";
				if(isset($_POST["permissionby"])){
					$permiBy_sum = $permiBy=implode(",",$_POST["permissionby"]);
				}
				if(!empty($_POST["permissionby_other"])){
					$permiBy=$permiBy."~!!~".$_POST["permissionby_other"];
					if(!empty($permiBy_sum)){ $permiBy_sum = $permiBy_sum.", "; }
					$permiBy_sum = $permiBy_sum.$_POST["permissionby_other"];
				}
				if(!empty($permiBy))$permiBy = sqlEscStr($permiBy);

				//Dilation --
				$sumDilation=$sumDilation_od=$sumDilation_os="";
				$arrDilatInx = array("pheny25","mydiacyl5","mydiacyl1","Cyclogyl","Paremyd");
				$arrDilatVals = array("Phenylephrine 2.5%","Mydriacyl 1/2%","Mydriacyl 1%","Cyclogyl 1%","Paremyd");

				$curtimes = $_POST["curtimes"];
				$curdates = $_POST["curdates"];
				$maxDilation = count($curtimes);

				foreach($arr_db_dilate as $key_da => $val_da){
					$val_da_el_nm=mk_var_nm($val_da,"dltn");
					if(isset($_POST[$val_da_el_nm])){
						$$val_da_el_nm = $_POST[$val_da_el_nm];
						if($maxDilation<max($$val_da_el_nm))$maxDilation = max($$val_da_el_nm);
					}else{
						$$val_da_el_nm = array();
					}
				}

				if(isset($_POST["Other"]) && count($_POST["Other"])>0){
					$Other = $_POST["Other"];
					$tmp = max($Other);
					if($maxDilation<$tmp)$maxDilation = $tmp;
				}else{
					$Other = array();
				}

				$other_desc = $_POST["other_desc"];
				$spDialTime="";
				$arrDilation=array();
				for($i=0;$i<$maxDilation;$i++){
					$j=$i+1;
					$tmp ="";

					foreach($arr_db_dilate as $key_da => $val_da){
						$val_da_el_nm=mk_var_nm($val_da,"dltn");
						if(in_array($j,$$val_da_el_nm)) $tmp .=$val_da.",";
					}

					if(in_array($j,$Other) || !empty($other_desc[$i])) $tmp .="Other,";
					$tmp = trim($tmp,",");


					$arrDilation[$i]["dilate"]=$tmp;
					$arrDilation[$i]["other_desc"]=$other_desc[$i];
					$arrDilation[$i]["time"]=$curtimes[$i];
					$arrDilation[$i]["dt"]=$curdates[$i];
					$arrDilation[$i]["eye"]=$_POST["dileye".$j];

					//Summ--
					$t = "";
					if(!empty($arrDilation[$i]["dilate"])){
						$t = $arrDilation[$i]["dilate"]; // str_replace($arrDilatInx,$arrDilatVals,$arrDilation[$i]["dilate"]);
					}

					if(!empty($arrDilation[$i]["other_desc"])){
						$t = str_replace("Other", "".$arrDilation[$i]["other_desc"],$t);
					}

					if(!empty($t)){
						$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
						$t = str_replace(",",", ",$t);

						$tmp="";
						$tmp = trim($arrDilation[$i]["dt"]);
						if(!empty($tmp)){ $tmp = $tmp." "; }

						//$sumDilation .= $t." ".$tmp.trim($arrDilation[$i]["time"])."<br/>";
						if($arrDilation[$i]["eye"]=="OU" || $arrDilation[$i]["eye"]=="OD"){
							$sumDilation_od .= $t." ".$tmp.trim($arrDilation[$i]["time"])."<br/>";
						}
						if($arrDilation[$i]["eye"]=="OU" || $arrDilation[$i]["eye"]=="OS"){
							$sumDilation_os .= $t." ".$tmp.trim($arrDilation[$i]["time"])."<br/>";
						}
						//Time
						$spDialTime = trim($arrDilation[$i]["eye"].' '.$arrDilation[$i]["time"]);
						//Summ--
						$curtimes_last = $arrDilation[$i]["time"];
					}
				}
				$dilation=serialize($arrDilation);
				$dilation= sqlEscStr($dilation);

				//Summary --
				$tmpV = array("patientwarneds"=>$patientwarneds, "patientnot_Driving"=>$patientnot_Driving, "patientAllergic"=>$patientAllergic,
							"allergicComments"=>$allergicComments, "unableDilation"=>$unableDilation, "unableDilateComments"=>$unableDilateComments,
							"permiBy_sum"=>$permiBy_sum);
				$sumDilation = $this->get_dilation_summ_v2($sumDilation, $tmpV);
				//Summary --

				//
				$allergicComments = sqlEscStr($allergicComments);
				$unableDilateComments = sqlEscStr($unableDilateComments);

				///
				if(!empty($sumDilation_od)){
					$sumDilation_od=$sumDilation_od.$sumDilation;
					$elem_sideIop="OD";
				}
				if(!empty($sumDilation_os)){
					$sumDilation_os=$sumDilation_os.$sumDilation;
					$elem_sideIop=($elem_sideIop=="OD") ? "OU" : "OS";
				}

				// Dial
				if($noDilation==1){
					$spDialTime = "No Dilation";
				}else if($unableDilation==1){
					$spDialTime = "Refuse Dilation";
				}
				//Dilation --

				$arrDilatOPs = array("Well","Poorly");
				$strDilated = "";
				for($j=0;$j<2;$j++){
					$dEye = ($j==0)?"Od" : "Os";

					foreach($arrDilatOPs as $keyOpt => $valOpt){
						$var="elem_dilated".$dEye."_".$valOpt."";
						if(!empty($_POST[$var])){
							$strDilated .= $_POST[$var].",";
						}
					}

					for($i=1;$i<=8;$i++){
						$var = "elem_dilated".$dEye."_".$i."mm";
						if(!empty($_POST[$var])){
							$strDilated .= $_POST[$var].",";
						}
					}
					$strDilated .="~!!~";
				}

				//check
				$cQry = "select last_opr_id,uid,sumDilation_od,sumDilation_os,modi_note_Arr,sumDilation,exam_date   FROM chart_dialation WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged='0' ";

				$row = sqlQuery($cQry);

				if($row == false){
					$elem_editModeDial = "0";
					$last_opr_id = $_SESSION["authId"];
				}else{
					$elem_editModeDial = "1";
					$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
					//Modifying Notes----------------
						if($dilation!=$row["sumDilation"]){
							$modi_note_DilationOd=$owv->getModiNotes($row["sumDilation_od"],0,$sumDilation_od,0,$row["uid"]);
							$modi_note_DilationOs=$owv->getModiNotes($row["sumDilation_os"],0,$sumDilation_os,0,$row["uid"]);

							$seri_modi_note_DilationArr = $owv->getModiNotesArr($row["sumDilation_od"],$sumDilation_od,$last_opr_id,'OD',$row["modi_note_Arr"],$row['exam_date']);
							$seri_modi_note_DilationArr = $owv->getModiNotesArr($row["sumDilation_os"],$sumDilation_os,$last_opr_id,'OS',$seri_modi_note_DilationArr,$row['exam_date']);
						}
					//Modifying Notes----------------
				}

				if($elem_editModeDial == "0"){ // Insert
					$sql = "insert into chart_dialation
						 SET
						 form_id = '".$elem_formId."',
						 patient_id='".$patientid."',
						 patient_not_driving = '".$patientnot_Driving."',
						 patientAllergic = '".$patientAllergic."',
						 allergicComments = '".$allergicComments."',
						 unableDilation = '".$unableDilation."',
						 unableDilateComments = '".$unableDilateComments."',
						 dilated='".$dilateds."',
						 pheny25='".$twopoint."',
						 pheny10='".$tenpoint."',
						 tropicanide='".$Tropicamide."',
						 cyclogel='".$Cyclogyl."',
						 other='".$Other."',
						 dilated_other='',
						 dilated_time='".$curtimes_last."',
						 warned_n_advised='".$patientwarneds."',
						 dilated_mm = '".$strDilated."',
						 eyeSide = '".$elem_sideIop."',
						 rev_eyes = '".$revEyes."',
						 exam_date = CURDATE(),
						 uid = '".$_SESSION["authId"]."',
						 mydiacyl5 = '".$Mydriacyl_05."',
						 noDilation='".$noDilation."',
						 dilation='".$dilation."',
						 permiBy='".$permiBy."',
						 statusElem='".$statusElem."',
						 ut_elem = '".$ut_elem."',
						 sumDilation_od = '".$sumDilation_od."', sumDilation_os = '".$sumDilation_os."',
						 spDialTime = '".$spDialTime."', last_opr_id = '".$last_opr_id."'
						";

					$insertIdDial = sqlInsert($sql);


				}else if($elem_editModeDial == "1"){ //Update
					$sql = "UPDATE chart_dialation ".
						 "SET
						 patient_not_driving = '".$patientnot_Driving."',
						 patientAllergic = '".$patientAllergic."',
						 allergicComments = '".$allergicComments."',
						 unableDilation = '".$unableDilation."',
						 unableDilateComments = '".$unableDilateComments."',
						 dilated='".$dilateds."',
						 pheny25='".$twopoint."',
						 pheny10='".$tenpoint."',
						 tropicanide='".$Tropicamide."',
						 cyclogel='".$Cyclogyl."',
						 other='".$Other."',
						 dilated_other='',
						 dilated_time='".$curtimes_last."',
						 warned_n_advised='".$patientwarneds."',
						 dilated_mm = '".$strDilated."', ".
						 "eyeSide = '".$elem_sideIop."', ".
						 "rev_eyes = '".$revEyes."', ".
						 "exam_date = CURDATE(), ".
						 "mydiacyl5 = '".$Mydriacyl_05."', ".
						 "uid = '".$_SESSION["authId"]."', ".
						 "noDilation='".$noDilation."', ".
						 "dilation='".$dilation."', ".
						 "permiBy='".$permiBy."', ".
						 "statusElem='".$statusElem."', ".
						 "ut_elem = '".$ut_elem."', ".
						 "sumDilation_od = '".$sumDilation_od."', sumDilation_os = '".$sumDilation_os."', ".
						 "spDialTime = '".$spDialTime."',modi_note_Arr = '".sqlEscStr($seri_modi_note_DilationArr)."',
						  last_opr_id = '".$last_opr_id."'".
						 "WHERE form_id = '".$elem_formId."' AND patient_id='".$patientid."' AND purged='0' ";
					$res = sqlQuery($sql);
				}

				// Dialation Time
				$gonioDialTime = (!empty($twopoint) || !empty($Tropicamide) || !empty($Cyclogyl) || !empty($other_desc) || !empty($Mydriacyl_05)) ? $elem_sideIop.' '.$curtimes : "";
				//Set Msg in imedicmonitor
				if( !empty($gonioDialTime) ){
					$oCDilation->setDialationMsg();
				}

				//Set Change Date Arc Rec --
				$this->setChangeDtArcRec("chart_dialation");
				//Set Change Date Arc Rec --

				//Insert dilation status in Pt Monitor
				if(strtolower($spDialTime) == 'no dilation'){
					patient_monitor_daily("NO_DILATION");
				}else if(strtolower($spDialTime) == 'refuse dilation'){
					patient_monitor_daily("REFUSED_DILATION");
				}else{
					patient_monitor_daily("DILATION");
				}

				//Summery
				$arrRet["Exam"] = "Gonio";

				/*
				$exm = "Dilation";
				$arrRet[$exm]["Exam"] = $exm;
				$arrRet[$exm]["isPositive"] = "0";
				$arrRet[$exm]["wnl"] = "0";
				$arrRet[$exm]["NC"] = "";
				$arrRet[$exm]["Draw"] = "null";
				$arrRet[$exm]["arExamDone"] = "";
				$arrRet[$exm]["AddExam"] = "";
				$arrRet[$exm]["FormId"] = $elem_formId;
				*/
			}//End Dilation

			///Chart_OOD
			if($elem_ci_OOD > 0  && empty($flg_purge_iop) ){

				$elem_sideIop = $_POST['elem_sideIop'];
				$sumOOD = $sumOOD_od = $sumOOD_os = "";
				$arrOODInx = array("Piol","Alphagan","Iopidine","Diamox");
				$arrOODVals = array("Pilo 1%","Alphagan P 0.1%","Iopidine 0.5%","Diamox");
				$statusElem = $_POST["elem_chng_OOD"];

				//OOD --
				$curtimes_ood = $_POST["curtimes_ood"];
				$curdates_ood = $_POST["curdates_ood"];
				$maxOOD = count($curtimes_ood);

				foreach($arr_db_ood as $key_da => $val_da){
					$val_da_el_nm=mk_var_nm($val_da,"ood");
					if(isset($_POST[$val_da_el_nm])){
						$$val_da_el_nm = $_POST[$val_da_el_nm];
						if($maxOOD<max($$val_da_el_nm))$maxOOD = max($$val_da_el_nm);
					}else{
						$$val_da_el_nm = array();
					}
				}

				if(isset($_POST["Other_OOD"]) && count($_POST["Other_OOD"])>0){
					$Other_OOD = $_POST["Other_OOD"];
					$tmp = max($Other_OOD);
					if($maxOOD<$tmp)$maxOOD = $tmp;
				}else{
					$Other_OOD = array();
				}

				$other_desc_ood = $_POST["other_desc_ood"];

				$spOODTime="";
				$arrOOD=array();
				for($i=0;$i<$maxOOD;$i++){
					$j=$i+1;
					$tmp ="";
					foreach($arr_db_ood as $key_da => $val_da){
						$val_da_el_nm=mk_var_nm($val_da,"ood");
						if(in_array($j,$$val_da_el_nm)) $tmp .=$val_da.",";
					}
					//--

					if(in_array($j,$Other_OOD) || !empty($other_desc_ood[$i])) $tmp .="Other,";

					$tmp = trim($tmp,",");



					$arrOOD[$i]["ood"]=$tmp;
					$arrOOD[$i]["other_desc"]=$other_desc_ood[$i];
					$arrOOD[$i]["time"]=$curtimes_ood[$i];
					$arrOOD[$i]["dt"]=$curdates_ood[$i];
					$arrOOD[$i]["eye"]=$_POST["oodeye".$j];

					// --
					if((!empty($arrOOD[$i]["ood"])||!empty($arrOOD[$i]["other_desc"]))&&!empty($arrOOD[$i]["time"])){

						$t = "";

						if(!empty($arrOOD[$i]["ood"])){
							$t = $arrOOD[$i]["ood"]; //str_replace($arrOODInx,$arrOODVals,$arrOOD[$i]["ood"]);
						}


						if(!empty($arrOOD[$i]["other_desc"])){
							$t = str_replace("Other", "".$arrOOD[$i]["other_desc"],$t);
						}

						/*
						Format should be
						Drop Name : Site Time
						*/

						$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
						$t = str_replace(",",", ",$t);

						if($arrOOD[$i]["eye"]!="OS"){
							$tmp = "";
							$tmp = trim($arrOOD[$i]["dt"]);
							if(!empty($tmp)){ $tmp = $tmp." "; }
							$sumOOD_od .= $t." ".$arrOOD[$i]["eye"]." ".$tmp.trim($arrOOD[$i]["time"])."<br/>";
						}

						if($arrOOD[$i]["eye"]!="OD"){
							$tmp = "";
							$tmp = trim($arrOOD[$i]["dt"]);
							if(!empty($tmp)){ $tmp = $tmp." "; }
							$sumOOD_os .= $t." ".$arrOOD[$i]["eye"]." ".trim($arrOOD[$i]["time"])."<br/>";
						}

						//Time
						$spOODTime = trim($arrOOD[$i]["eye"].' '.$arrOOD[$i]["time"]);
						//Summ--

					}
					//--
				}
				$OOD=serialize($arrOOD);
				$OOD= sqlEscStr($OOD);

				//check
				$cQry = "select last_opr_id,uid,sumOOD_od,sumOOD_os,modi_note_Arr,exam_date FROM chart_ood WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged='0' ";
				$row = sqlQuery($cQry);
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				if($row == false){
					$elem_editModeOOD = "0";
				}else{
					$elem_editModeOOD = "1";
					//Modifying Notes----------------
						$modi_note_OodOd=$owv->getModiNotes($row["sumOOD_od"],0,$sumOOD_od,0,$row["uid"]);
						$modi_note_OodOs=$owv->getModiNotes($row["sumOOD_os"],0,$sumOOD_os,0,$row["uid"]);

						$seri_modi_note_oodArr = $owv->getModiNotesArr($row["sumOOD_od"],$sumOOD_od,$last_opr_id,'OD',$row["modi_note_Arr"],$row['exam_date']);
						$seri_modi_note_oodArr = $owv->getModiNotesArr($row["sumOOD_os"],$sumOOD_os,$last_opr_id,'OS',$seri_modi_note_oodArr,$row['exam_date']);
					//Modifying Notes----------------
				}

				if($elem_editModeOOD == "0"){ // Insert
					$sql = "INSERT INTO chart_ood (ood_id, patient_id, form_id, ood, statusElem, uid, exam_date,eye,sumOOD_od,sumOOD_os,spOODTime,last_opr_id )
							VALUES (NULL, '".$patientid."', '".$elem_formId."','".$OOD."','".$statusElem."',
							'".$_SESSION["authId"]."',CURDATE(),'".$elem_sideIop."', '".$sumOOD_od."', '".$sumOOD_os."', '".$spOODTime."', '".$last_opr_id."' )";
					$insertIdDial = sqlInsert($sql);

				}else if($elem_editModeOOD == "1"){ //Update
					$sql = "UPDATE chart_ood SET
							ood='".$OOD."',
							statusElem='".$statusElem."',
							uid='".$_SESSION["authId"]."',
							eye='".$elem_sideIop."',
							exam_date=CURDATE(),
							sumOOD_od = '".$sumOOD_od."',
							sumOOD_os = '".$sumOOD_os."',
							spOODTime = '".$spOODTime."', ".
							"last_opr_id = '".$last_opr_id."',modi_note_Arr = '".sqlEscStr($seri_modi_note_oodArr)."'
						 WHERE patient_id='".$patientid."' AND form_id='".$elem_formId."' AND purged='0'
						";
					$res = sqlQuery($sql);
				}

				//Summery
				$arrRet["Exam"] = "Gonio";
				/*
				$exm = "OOD";
				$arrRet[$exm]["Exam"] = $exm;
				$arrRet[$exm]["isPositive"] = "0";
				$arrRet[$exm]["wnl"] = "0";
				$arrRet[$exm]["NC"] = "";
				$arrRet[$exm]["Draw"] = "null";
				$arrRet[$exm]["arExamDone"] = "";
				$arrRet[$exm]["AddExam"] = "";
				$arrRet[$exm]["FormId"] = $elem_formId;
				*/


			}//End OOD

			// Make chart notes valid
			if(($elem_ci_iop > 0) || ($elem_ci_dilation > 0) || ($elem_ci_gonio > 0) || ($elem_ci_OOD > 0)){
				$this->makeChartNotesValid();
			}
		}

		//
		echo json_encode($arrRet);
	}

	function getSummAnes($elem_anesthetic){
		//Anes
		$spAnesTime="";
		$sumAnes = $sumAnes_od = $sumAnes_os = "";
		if(!empty($elem_anesthetic)){

			$arrAnes=unserialize($elem_anesthetic);
			$tmp = count($arrAnes);
			$spAnesTime = $arrAnes[$tmp-1]["eye"]." ".$arrAnes[$tmp-1]["time"];

			// *
			//Test
			for($a=0;$a<$tmp;$a++){
				if((!empty($arrAnes[$a]["anes"])||!empty($arrAnes[$a]["other"]))&&!empty($arrAnes[$a]["time"])){

					$t = "";
					if(!empty($arrAnes[$a]["anes"])){
						$t .= $arrAnes[$a]["anes"];
					}

					if(!empty($arrAnes[$a]["other"])){
						if(!empty($t)){$t .= ",";}
						$t .= "".$arrAnes[$a]["other"];
					}

					$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
					$t = str_replace(",",", ",$t);

					if($arrAnes[$a]["eye"]!="OS"){
						$sumAnes_od .= $t." ".$arrAnes[$a]["eye"]." ".trim($arrAnes[$a]["time"])."<br/>";
					}

					if($arrAnes[$a]["eye"]!="OD"){
						$sumAnes_os .= $t." ".$arrAnes[$a]["eye"]." ".trim($arrAnes[$a]["time"])."<br/>";
					}
				}
			}

			//$sumAnes_od = $sumAnes_os = $sumAnes;
			//Test * /
		}

		return array("od"=>$sumAnes_od, "os"=>$sumAnes_os, "time"=>$spAnesTime);

	}

	function update_anes_time(){
		$elem_formId = $this->fid;
		$elem_patientId = $this->pid;
		$elem_newTime = $_POST["elem_newTime"];

		//check
		$cQry = "select anesthetic,tetracaine,flourocaine,alcaine,iop_time
				FROM chart_iop
				WHERE form_id='".$elem_formId."' AND patient_id='".$elem_patientId."' AND purged='0' ";
		$row = sqlQuery($cQry);

		if($row == false){
			/* NP
			//get Last Id
			$lastId = 0;
			$res = valNewRecordIop($elem_patientId,"chart_iop.iop_id");
			for($i=0;$row=sqlFetchArray($res);$i++){
				$lastId = $row["iop_id"];
			}
			//
			$sql = "INSERT INTO chart_iop (iop_time, form_id, patient_id) VALUES ('".$elem_newTime."','".$elem_formId."','".$elem_patientId."') ";
			$insertId = sqlInsert($sql);
			//
			$test = copyLastIop($insertId,$lastId);
			*/
		}else{
			$strAnes = $row["anesthetic"];
			$tetracaine = $row["tetracaine"];
			$flourocaine = $row["flourocaine"];
			$alcaine = $row["alcaine"];
			$time_up = $row["iop_time"];
			$last_opr_id = $row["uid"];
		}

		if(!empty($strAnes)){
			$arrAnes=unserialize($strAnes);
			$len=count($arrAnes);
			if($len>0){
				$lastAnes = $arrAnes[$len-1]["anes"];
				$lastTime = $elem_newTime;
				//Insert Again
				$arrAnes[$len]["anes"]=$lastAnes;
				$arrAnes[$len]["time"]=$lastTime;
				$arrAnes[$len]["eye"]=$arrAnes[$len-1]["eye"];
			}
		}else{
			$tmp="";
			if($tetracaine == "Tetracaine")$tmp.="Tetracaine,";
			if(($flourocaine == "Flourocaine") || ($flourocaine == "Fluorocaine"))$tmp.="Flourocaine,";
			if($alcaine == "Alcaine")$tmp.="Alcaine";
			$arrAnes[0]["anes"]=$tmp;
			$arrAnes[0]["time"]=$time_up;
			//Insert Again
			$arrAnes[1]=$arrAnes[0];
			$arrAnes[1]["time"]=$elem_newTime;
		}

		if(count($arrAnes)>0){
			$anesthetic = serialize($arrAnes);

			$arTmp = $this->getSummAnes($anesthetic);
			$sumAnesOd = $arTmp["od"];
			$sumAnesOs = $arTmp["os"];
			$spAnesTime = $arTmp["time"];

			$sql = "UPDATE chart_iop
					SET anesthetic='".sqlEscStr($anesthetic)."' ,
					sumAnesOd = '".sqlEscStr($sumAnesOd)."',
					sumAnesOs = '".sqlEscStr($sumAnesOs)."',
					spAnesTime = '".sqlEscStr($spAnesTime)."', statusElem='1', uid = '".$_SESSION["authId"]."',
					last_opr_id = '".$last_opr_id."'
					WHERE form_id='".$elem_formId."' AND patient_id='".$elem_patientId."' AND purged='0' ";
			$res = sqlQuery($sql);
			// make Valid
			$this->makeChartNotesValid();
		}

		echo 0;
	}

	function getSummDilation($elem_dilation,$elem_eyeSide){
		$spDialTime="";
		$sumDilation = $sumDilation_od = $sumDilation_os = "";

		$arrDilatInx = array("pheny25","mydiacyl5","mydiacyl1","Cyclogyl","Paremyd");
		$arrDilatVals = array("Phenylephrine 2.5%","Mydriacyl 1/2%","Mydriacyl 1%","Cyclogyl 1%","Paremyd");

		if(!empty($elem_dilation)){
			$arrDilation=unserialize($elem_dilation);
			$tmp = count($arrDilation);
			for($a=0;$a<$tmp;$a++){
				if((!empty($arrDilation[$a]["dilate"])||!empty($arrDilation[$a]["other_desc"]))&&!empty($arrDilation[$a]["time"])){

					//if(empty($spDialTime)){
						$spDialTime = trim($arrDilation[$a]["eye"].' '.$arrDilation[$a]["time"]);
						//break;
					//}

					$t = "";
					if(!empty($arrDilation[$a]["dilate"])){
						$t = str_replace($arrDilatInx,$arrDilatVals,$arrDilation[$a]["dilate"]);
					}

					if(!empty($arrDilation[$a]["other_desc"])){
						$t = str_replace("Other", "".$arrDilation[$a]["other_desc"],$t);
					}
					$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
					$t = str_replace(",",", ",$t);

					if($arrDilation[$a]["eye"]=="OU" || $arrDilation[$a]["eye"]=="OD"){
						$sumDilation_od .= $t." ".trim($arrDilation[$a]["time"])."<br/>";
					}
					if($arrDilation[$a]["eye"]=="OU" || $arrDilation[$a]["eye"]=="OS"){
						$sumDilation_os .= $t." ".trim($arrDilation[$a]["time"])."<br/>";
					}

				}
			}
		}

		return array("od"=>$sumDilation_od, "os"=>$sumDilation_os, "time"=>$spDialTime);

	}

	function update_dial_time(){
		$elem_formId = $this->fid;
		$elem_patientId = $this->pid;
		$elem_newTime = $_POST["elem_newTime"];
		$elem_newdt = $_POST["elem_newdt"];

		//check
		$cQry = "select
				dilation,pheny25,mydiacyl5,tropicanide,cyclogel,other,dilated_other,dilated_time,
				eyeSide, noDilation, unableDilation,
				warned_n_advised, patient_not_driving, patientAllergic, allergicComments, unableDilateComments, permiBy
				FROM chart_dialation WHERE form_id='".$elem_formId."' AND patient_id='".$elem_patientId."' AND purged='0'  ";

		$row = sqlQuery($cQry);
		if($row == false){
			/*NP
			//get Last Id
			$lastId = 0;
			$row = valNewRecordDialation($elem_patientId,"chart_dialation.dia_id");
			if($row != false){
				$lastId = $row["dia_id"];
			}
			//
			$sql = "INSERT INTO chart_dialation (dilated_time, form_id, patient_id,exam_date,uid) ".
					"VALUES ('".$elem_newTime."','".$elem_formId."','".$elem_patientId."',CURDATE(),'".$_SESSION["authId"]."') ";
			$insertId = sqlInsert($sql);
			//
			$test = copyLastDilation($insertId,$lastId);
			*/
		}else{
			$strDilation = $row["dilation"];
			$pheny25 = $row["pheny25"];
			$mydiacyl5 = $row["mydiacyl5"];
			$tropicanide = $row["tropicanide"];
			$cyclogel = $row["cyclogel"];
			$other = $row["other"];
			$dilated_other = $row["dilated_other"];
			$dilated_time = $row["dilated_time"];
			$elem_eyeSide = $row["eyeSide"];
			$elem_no_dilation = $row["noDilation"];
			$unableDilation = $row["unableDilation"];
			$last_opr_id = $row["uid"];

			$patientwarneds = $row["warned_n_advised"];
			$patientnot_Driving = $row["patient_not_driving"];
			$patientAllergic = $row["patientAllergic"];
			$allergicComments = $row["allergicComments"];
			$unableDilateComments = $row["unableDilateComments"];
			$permiBy_sum = $row["permiBy"];

		}

		if(!empty($strDilation)){
			$arrDilation=unserialize($strDilation);
			$len=count($arrDilation);
			if($len>0){
				$lastDilate = $arrDilation[$len-1]["dilate"];
				$lastOtherDesc = $arrDilation[$len-1]["other_desc"];
				$lastEye = $arrDilation[$len-1]["eye"];
				$lastTime = $elem_newTime;
				//Insert Again
				$arrDilation[$len]["dilate"]=$lastDilate;
				$arrDilation[$len]["other_desc"]=$lastOtherDesc;
				$arrDilation[$len]["time"]=$lastTime;
				$arrDilation[$len]["dt"]=$elem_newdt;
				$arrDilation[$len]["eye"]=$lastEye;
			}
		}else{
			$tmp ="";
			if($pheny25==1) $tmp .="pheny25,";
			if($mydiacyl5==1) $tmp .="mydiacyl5,";
			if($tropicanide==1) $tmp .="mydiacyl1,";
			if($cyclogel==1) $tmp .="Cyclogyl,";
			if($other==1) $tmp .="Other";

			$arrDilation[0]["dilate"]=$tmp;
			$arrDilation[0]["other_desc"]= $dilated_other;
			$arrDilation[0]["time"]=$dilated_time;
			//Insert Again
			$arrDilation[1]=$arrDilation[0];
			$arrDilation[1]["time"]=$elem_newTime;
			$arrDilation[1]["dt"]=$elem_newdt;
		}

		if(count($arrDilation)>0){
			$dilation = serialize($arrDilation);

			//Summary
			$arTmp=$this->getSummDilation($dilation,$elem_eyeSide);

			//summery comments --
			$tmpV = array("patientwarneds"=>$patientwarneds, "patientnot_Driving"=>$patientnot_Driving, "patientAllergic"=>$patientAllergic,
							"allergicComments"=>$allergicComments, "unableDilation"=>$unableDilation, "unableDilateComments"=>$unableDilateComments,
							"permiBy_sum"=>$permiBy_sum);
			$sumDilation_comm = $this->get_dilation_summ_v2("", $tmpV);
			if(!empty($sumDilation_comm)){
				if(!empty($arTmp["od"])){
					//$arTmp["od"].="<br/>";
					$arTmp["od"].=$sumDilation_comm;
				}

				if(!empty($arTmp["os"])){
					//$arTmp["os"].="<br/>";
					$arTmp["os"].=$sumDilation_comm;
				}
			}

			$sumDilation_od = $arTmp["od"];
			$sumDilation_os = $arTmp["os"];
			$spDialTime = $arTmp["time"];

			if($elem_no_dilation==1){
				$spDialTime = "No Dilation";
			}else if($unableDilation==1){
				$spDialTime = "Refuse Dilation";
			}

			$sql = "UPDATE chart_dialation SET dilation='".sqlEscStr($dilation)."',
						sumDilation_od='".sqlEscStr($sumDilation_od)."',
						sumDilation_os='".sqlEscStr($sumDilation_os)."',
						spDialTime='".sqlEscStr($spDialTime)."', statusElem='1', uid='".$_SESSION["authId"]."', last_opr_id='".$last_opr_id."'
					WHERE form_id='".$elem_formId."' AND patient_id='".$elem_patientId."' AND purged='0' ";
			$res = sqlQuery($sql);
		}

		//set dilation msg
		$oCDilation = new ChartDilation($elem_patientId, $elem_formId);
		$oCDilation->setDialationMsg();

		//Insert dilation status in Pt Monitor
		patient_monitor_daily("DILATION");

		// make Valid
		$this->makeChartNotesValid();

		echo "0";
	}

	function getSummOod($ood,$eye){

		//OOD
		$spOODTime="";
		$sumOOD = $sumOOD_od = $sumOOD_os = "";
		$arrOODInx = array("Piol","Alphagan","Iopidine","Diamox");
		$arrOODVals = array("Pilo 1%","Alphagan P 0.1%","Iopidine 0.5%","Diamox");

		if(!empty($ood)){
			$arrOOD=unserialize($ood);
			$tmp = count($arrOOD);
			for($a=0;$a<$tmp;$a++){
				if((!empty($arrOOD[$a]["ood"])||!empty($arrOOD[$a]["other_desc"]))&&!empty($arrOOD[$a]["time"])){

					$t = "";
					if(!empty($arrOOD[$a]["ood"])){
						$t = str_replace($arrOODInx,$arrOODVals,$arrOOD[$a]["ood"]);
					}
					if(!empty($arrOOD[$a]["other_desc"])){
						$t = str_replace("Other", "".$arrOOD[$a]["other_desc"],$t);
					}
					$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
					$t = str_replace(",",", ",$t);
					//$sumOOD .= $t." ".trim($arrOOD[$a]["time"])."<br/>";

					//Time
					$spOODTime = trim($arrOOD[$a]["eye"].' '.$arrOOD[$a]["time"]);
					//Summ--

					if($arrOOD[$a]["eye"]!="OS"){
						$sumOOD_od .= $t." ".$arrOOD[$a]["eye"]." ".trim($arrOOD[$a]["time"])."<br/>";
					}

					if($arrOOD[$a]["eye"]!="OD"){
						$sumOOD_os .= $t." ".$arrOOD[$a]["eye"]." ".trim($arrOOD[$a]["time"])."<br/>";
					}

				}
			}
		}

		/*
		if($eye=="OU"||$eye=="OD"){
			$sumOOD_od=$sumOOD;
		}
		if($eye=="OU"||$eye=="OS"){
			$sumOOD_os=$sumOOD;
		}
		*/

		return array("od"=>$sumOOD_od, "os"=>$sumOOD_os, "time"=>$spOODTime);

		//OOD
	}

	function update_ood_time(){
		$elem_formId = $this->fid;
		$elem_patientId = $this->pid;
		$elem_newTime = $_POST["elem_newTime"];

		//check
		$cQry = "select  ood,eye
				FROM chart_ood
				WHERE form_id='".$elem_formId."' AND patient_id='".$elem_patientId."' AND purged='0' ";
		$row = sqlQuery($cQry);

		if($row == false){

		}else{
			$strOod = $row["ood"];
			$eye = $row["eye"];
			$last_opr_id = $row["uid"];
		}

		if(!empty($strOod)){
			$arrOod=unserialize($strOod);
			$len=count($arrOod);
			if($len>0){
				$lastOod = $arrOod[$len-1]["ood"];
				$lastTime = $elem_newTime;
				//Insert Again
				$arrOod[$len]["ood"]=$lastOod;
				$arrOod[$len]["time"]=$lastTime;
				$arrOod[$len]["eye"]=$arrOod[$len-1]["eye"];
				//$eye = $arrOod[$len]["eye"];
			}
		}

		if(count($arrOod)>0){
			$ood = serialize($arrOod);

			$arTmp = $this->getSummOod($ood,$eye);
			$sumOodOd = $arTmp["od"];
			$sumOodOs = $arTmp["os"];
			$spOodTime = $arTmp["time"];

			$sql = "UPDATE chart_ood
					SET ood='".sqlEscStr($ood)."' ,
					sumOOD_od  = '".sqlEscStr($sumOodOd)."',
					sumOOD_os  = '".sqlEscStr($sumOodOs)."',
					spOODTime  = '".sqlEscStr($spOodTime)."',
					statusElem = '1', uid='".$_SESSION["authId"]."' , last_opr_id='".$last_opr_id."'
					WHERE form_id='".$elem_formId."' AND patient_id='".$elem_patientId."' AND purged='0' ";
			$res = sqlQuery($sql);
			// make Valid
			$this->makeChartNotesValid();
		}

		//print_r($arTmp);
		//echo $sql;

		echo 0;
	}

	function checkNewIOPExists($iop_type,$iop_od,$iop_os,$iop_time,$mulPressureArr){
		$ret = 0;
		$ln = is_array($mulPressureArr) ? count($mulPressureArr) : 0 ;
		if($ln>0){
			for($i=0;$i<=$ln;$i++){
				$sx1=$i==0 ? "" : $i+1;
				$sx2=$i==0 ? "" : $i;
				//if($iop_type=="TA"){
					if(($mulPressureArr["multiplePressuer".$sx1] ["elem_appTime".$sx2] == $iop_time) &&
						($mulPressureArr["multiplePressuer".$sx1] ["elem_appOd".$sx2] == $iop_od) &&
						($mulPressureArr["multiplePressuer".$sx1] ["elem_appOs".$sx2] == $iop_os) &&
						($mulPressureArr["multiplePressuer".$sx1] ["elem_appMethod".$sx2] == $iop_type)
						){
						$ret = 1;
					}
			}
		}

		return $ret;
	}

	function addNewIOP($iop_type,$iop_od,$iop_os,$iop_time,$trgtOd="",$trgtOs="",$mulPressureArr=array(), $ut_elems=""){
		$patientid=$this->pid;
		$elem_formId=$this->fid;
		$oIop = new ChartIop($patientid, $elem_formId);
		$arr_summ_iop=array();

		//
		$elem_applanation = 1;
		$elem_appOd=$iop_od;
		$elem_appOs=$iop_os;
		$elem_appTime=$iop_time;
		$elem_descTa="";
		$elem_appMethod=$iop_type;

		$ut_elem_cur="elem_applanation,elem_appOd,elem_appOs1,elem_appMethod";
		$ut_elem = $this->getUTElemString($ut_elems,$ut_elem_cur);
		//

		$strSumTa_d=$strSumTp_d=$strSumTx_d=$strSumTt_d="";
		$strSumTa_s=$strSumTp_s=$strSumTx_s=$strSumTt_s="";
		$str_descTa=$str_descTp=$str_descTx=$str_descTt="";

		$sx2_flg="";
		$ln = is_array($mulPressureArr) ? count($mulPressureArr) : 0 ;
		if($ln<=0){

			$mulPressureArr=array();
			$mulPressureArr["multiplePressuer"] = array(
				"elem_applanation" => 1,
				"elem_appMethod" => $elem_appMethod,
				"elem_appOd" => $elem_appOd,
				"elem_appOs" => $elem_appOs,
				"elem_appTime" => $elem_appTime,
				"elem_descTa" => $elem_descTa
			);

			//Summ Presure values--
			$arr_summ_iop[$elem_appMethod]["sum"]["od"].=$oIop->getPrsrSum($elem_applanation,$elem_appOd,$trgtOd,$elem_appTime);
			$arr_summ_iop[$elem_appMethod]["sum"]["os"].=$oIop->getPrsrSum($elem_applanation,$elem_appOs,$trgtOs,$elem_appTime);

		}else{

			//loop and find empty space
			$flgAdded=$this->checkNewIOPExists($iop_type,$iop_od,$iop_os,$iop_time,$mulPressureArr);
			for($i=0;$i<=$ln;$i++){
				$sx1=$i==0 ? "" : $i+1;
				$sx2=$i==0 ? "" : $i;

				if($flgAdded==0){

				$sx2_flg=$sx2;

				if($i==$ln){

					$mulPressureArr["multiplePressuer".$sx1] = array(
						"elem_applanation".$sx2 => 1,
						"elem_appMethod".$sx2 => $elem_appMethod,
						"elem_appOd".$sx2 => $elem_appOd,
						"elem_appOs".$sx2 => $elem_appOs,
						"elem_appTime".$sx2 => $elem_appTime,
						"elem_descTa".$sx2 => $elem_descTa
					);

				}else{

					if($flgAdded==0 && empty($mulPressureArr["multiplePressuer".$sx1] ["elem_applanation".$sx2]) &&
						empty($mulPressureArr["multiplePressuer".$sx1] ["elem_appOd".$sx2]) &&
						empty($mulPressureArr["multiplePressuer".$sx1] ["elem_appOs".$sx2]) &&
						empty($mulPressureArr["multiplePressuer".$sx1] ["elem_appMethod".$sx2])
						){

							$mulPressureArr["multiplePressuer".$sx1] ["elem_applanation".$sx2] = 1;
							$mulPressureArr["multiplePressuer".$sx1] ["elem_appOd".$sx2] = $iop_od;
							$mulPressureArr["multiplePressuer".$sx1] ["elem_appOs".$sx2] = $iop_os;
							$mulPressureArr["multiplePressuer".$sx1] ["elem_appTime".$sx2] = $iop_time;
							$mulPressureArr["multiplePressuer".$sx1] ["elem_appMethod".$sx2] = $iop_type;

							$ut_elem_cur="elem_applanation".$sx2_flg.",elem_appOd".$sx2_flg.",elem_appOs1".$sx2_flg."";
							$ut_elem = $this->getUTElemString($ut_elems,$ut_elem_cur);

							$flgAdded=1;

					}

				}//$i==$ln

				}//$flgAdded=1;

				//Summ Presure values--
				$arr_summ_iop[$mulPressureArr["multiplePressuer".$sx1] ["elem_appMethod".$sx2]]["sum"]["od"].=$oIop->getPrsrSum($mulPressureArr["multiplePressuer".$sx1] ["elem_appMethod".$sx2],$mulPressureArr["multiplePressuer".$sx1] ["elem_appOd".$sx2],$trgtOd,$mulPressureArr["multiplePressuer".$sx1] ["elem_appTime".$sx2]);
				$arr_summ_iop[$mulPressureArr["multiplePressuer".$sx1] ["elem_appMethod".$sx2]]["sum"]["os"].=$oIop->getPrsrSum($mulPressureArr["multiplePressuer".$sx1] ["elem_appMethod".$sx2],$mulPressureArr["multiplePressuer".$sx1] ["elem_appOs".$sx2],$trgtOs,$mulPressureArr["multiplePressuer".$sx1] ["elem_appTime".$sx2]);

				if(!empty($mulPressureArr["multiplePressuer".$sx1] ["elem_descTa".$sx2])) {
					$arr_summ_iop[$mulPressureArr["multiplePressuer".$sx1] ["elem_appMethod".$sx2]]["desc"].=$mulPressureArr["multiplePressuer".$sx1]["elem_descTa".$sx2]."; ";
				}

				//--
			}
		}

		//Summary --
		$iop_result_od=$iop_result_os="";
		$tmp_arsum = $this->get_iop_sum($arr_summ_iop);
		$iop_result_od .= $tmp_arsum[0];
		$iop_result_os .= $tmp_arsum[1];

		//Summary --

		return array($mulPressureArr, $ut_elem, $iop_result_od, $iop_result_os);

	}

	function addNewIOPMain($finalize_flag, $iop_type,$iop_od,$iop_os,$iop_time,$iop_dt=""){
		$patient_id = $this->pid;
		$form_id = $this->fid;
		$ochartiop = new ChartIop($patient_id, $form_id);

		$js_reloadIOP="";

		//$oCn = new ChartNote($patient_id, $form_id);

		$flg=1;
		if(!empty($finalize_flag)){
			$flg=0;
			list($isReviewable,$isEditable,$iscur_user_vphy) = $this->isChartReviewable($form_id,$_SESSION["authUserID"],1);
			if(!empty($isReviewable) && !empty($isEditable)){
				$flg=1;
			}
		}

		if($flg==1){

			$exam_date=date("Y-m-d H:i:s");
			if(!empty($iop_dt) && strpos($iop_dt,"0000-00")===false){
				$exam_date=$iop_dt;
			}

			$sql = "SELECT c1.multiple_pressure, c1.trgtOd, c1.trgtOs,
					c1.squeezing, c1.unreliable, c1.unable, c1.hold_lids, c1.ut_elem,
					c2.reading_od, c2.reading_os,
					c2.cor_val_od, c2.cor_val_os
					FROM chart_iop c1
					LEFT JOIN chart_correction_values c2 ON c1.form_id = c2.form_id AND c1.patient_id = c2.patient_id
					WHERE c1.form_id='".$form_id."' AND c1.patient_id='".$patient_id."' ";
			$row=sqlQuery($sql);
			if($row!=false){

				$multiple_pressure = $row["multiple_pressure"];
				$mulPressureArr = unserialize($multiple_pressure);
				$trgtOd=$row["trgtOd"];
				$trgtOs=$row["trgtOs"];
				$squeezing=$row["squeezing"];
				$unreliable=$row["unreliable"];
				$unable=$row["unable"];
				$hold_lids=$row["hold_lids"];
				$od_readings=$row["reading_od"];
				$os_readings=$row["reading_os"];
				$od_correction_value=$row["cor_val_od"];
				$os_correction_value=$row["cor_val_os"];
				$ut_elem=$row["ut_elem"];

				$sumOdIop = $sumOsIop = $iop_result_od = $iop_result_os = "";

				list($mulPressureArr,$ut_elem, $iop_result_od_tmp, $iop_result_os_tmp) = $this->addNewIOP($iop_type,$iop_od,$iop_os,$iop_time,$trgtOd,$trgtOs,$mulPressureArr,$ut_elem);


				$multiple_pressure = serialize($mulPressureArr);
				//Summary--

				if($squeezing == '1'){
					$tmp = "Squeezing";
					$iop_result_od .= "Pt.".$tmp."/";
					$iop_result_os .= "Pt.".$tmp."/";
				}
				if($unreliable == '1'){
					$tmp = "Unreliable";
					$iop_result_od .= "Pt.".$tmp."/";
					$iop_result_os .= "Pt.".$tmp."/";
				}
				if($unable=='1'){
					$tmp = "Unable";
					$iop_result_od .= "Pt.".$tmp."/";
					$iop_result_os .= "Pt.".$tmp."/";
				}
				if($hold_lids=='1'){
					$tmp = "Hold Lids";
					$iop_result_od .= "Pt.".$tmp."/";
					$iop_result_os .= "Pt.".$tmp."/";
				}
				if($trgtOd != ''){
					$iop_result_od .= " Trgt:".$trgtOd."/ ";
				}
				if($trgtOs != ''){
					$iop_result_os .= " Trgt:".$trgtOs."/ ";
				}
				//Correction Values --
				if( !empty($od_readings) || !empty($od_correction_value)){
					$iop_result_od .= "Pachy: ".$od_readings." ".$od_correction_value;
				}
				if( !empty($od_readings) || !empty($os_correction_value)){
					$iop_result_os .= "Pachy: ".$os_readings." ".$os_correction_value;
				}

				if(!empty($iop_result_od)){ $iop_result_od.="<br/>"; }
				if(!empty($iop_result_os)){ $iop_result_os.="<br/>"; }

				if(!empty($iop_result_od_tmp)){
					$iop_result_od.="".$iop_result_od_tmp;
				}

				if(!empty($iop_result_os_tmp)){
					$iop_result_os.="".$iop_result_os_tmp;
				}

				$sumOdIop = $iop_result_od;
				$sumOsIop = $iop_result_os;

				$sumOdIop = sqlEscStr($sumOdIop);
				$sumOsIop = sqlEscStr($sumOsIop);

				$sql = "UPDATE chart_iop SET
						exam_date = '".$exam_date."',
						sumOdIop = '".$sumOdIop."',
						sumOsIop = '".$sumOsIop."',
						uid = '".$_SESSION["authId"]."',
						ut_elem = '".$ut_elem."',
						last_opr_id = '".$_SESSION["authId"]."',
						multiple_pressure = '".sqlEscStr($multiple_pressure)."'
						WHERE form_id = '".$form_id."' AND patient_id = '".$patient_id."'
						";
				$row=sqlQuery($sql);

				//Summary--

				//exit();

			}else{

				//--
				$sumOdIop = $sumOsIop = $iop_result_od = $iop_result_os = "";
				list($mulPressureArr, $ut_elem, $iop_result_od, $iop_result_os) = $this->addNewIOP($iop_type,$iop_od,$iop_os,$iop_time);
				$multiple_pressure = serialize($mulPressureArr);

				//summ--

				$sumOdIop = $iop_result_od;
				$sumOsIop = $iop_result_os;

				$sumOdIop = sqlEscStr($sumOdIop);
				$sumOsIop = sqlEscStr($sumOsIop);

				//summ--

				if(!empty($patient_id) && !empty($form_id)){
					$sql = "INSERT INTO chart_iop SET
							multiple_pressure = '".sqlEscStr($multiple_pressure)."',
							form_id = '".$form_id."',
							patient_id = '".$patient_id."',
							exam_date = '".$exam_date."',
							sumOdIop = '".$sumOdIop."',
							sumOsIop = '".$sumOsIop."',
							uid = '".$_SESSION["authId"]."',
							ut_elem = '".$ut_elem."',
							last_opr_id = '".$_SESSION["authId"]."',
							statusElem = '1'
					";
					$row=sqlQuery($sql);
				}
			}

			//

			//Summery

			$js_reloadIOP="

				try{
				var obj = {'Exam':'Iop',
						'isPositive':0,'wnl':0,
						'NC':'',
						'Draw':null,'mandatory':'iop_inner',
						'FormId':'".$form_id."'};
				if(typeof window.opener.top.fmain.AfterSave != 'undefined'){
					window.opener.top.fmain.AfterSave(obj);
				}
				}catch(e){ }

			";
			//--

		}

		return $js_reloadIOP;

	}

	//Add IOP Function --

	function get_dilation_summ_v2($sumDilation, $tmpV){
		extract($tmpV);

		$permiBy_sum = trim($permiBy_sum); $permiBy_sum = trim($permiBy_sum, "~!!~");
		if(!empty($permiBy_sum)){
			$permiBy_sum = str_replace("~!!~", ",", $permiBy_sum);
			$sumDilation .= "Permission given by ".$permiBy_sum."<br/>";
		}
		if(!empty($patientwarneds)){$sumDilation .= "Patient warned of blurred vision from dilation and offered / advised to wear sunglasses.<br/>";}
		if(!empty($patientnot_Driving)){$sumDilation .= "Patient not driving.<br/>";}
		if(!empty($patientAllergic)){
			$sumDilation .= "Patient allergic to Dilation drops.<br/>";
			if(!empty($allergicComments)){ $sumDilation .= nl2br($allergicComments)."<br/>"; }
		}
		if(!empty($unableDilation)){
			$sumDilation .= "Patient refuses/unable to dilate.<br/>";
			if(!empty($unableDilateComments)){ $sumDilation .= nl2br($unableDilateComments)."<br/>"; }
		}
		return $sumDilation;
	}
}
?>
