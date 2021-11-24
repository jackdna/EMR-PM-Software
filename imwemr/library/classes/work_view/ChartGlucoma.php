<?php
include_once $GLOBALS['srcdir']."/classes/work_view/wv_functions.php";
include_once $GLOBALS['srcdir']."/classes/work_view/ChartAP.php";

class ChartGlucoma{
	public $pid;
	public function __construct($pid){
		$this->pid = $pid;
	}

	function getPatientStatus($id){
	  $retVal = "";
	  $sql = "SELECT patientStatus FROM patient_data WHERE id = '".$id."' ";
	  $row = sqlQuery($sql);
	  if($row != false){
		 $retVal = $row["patientStatus"];
	  }
	  return $retVal;
	}

	function hasPtGLProc($patient_id){
		$ret=0;
		$qryPatientProc="Select cp.id from chart_procedures as cp inner join chart_master_table as cmt on (cmt.id=cp.form_id and cmt.patient_id=cp.patient_id)
						INNER join operative_procedures op ON op.procedure_id=cp.proc_id
					WHERE cp.patient_id='".$patient_id."' and (cp.site IN ('OU','OD','OS') OR cp.lids_opts!='') and cp.proc_note_masterId='0' AND cmt.finalize='1' AND op.del_status!='1' and op.ret_gl=2 order by cmt.date_of_service DESC";
		$resPatientProc = imw_query($qryPatientProc);
		if(imw_num_rows($resPatientProc)>0){
			$ret=1;
		}
		return $ret;
	}

	function getDateOfServiceFullYear($formId){
		$sql = "SELECT ".
			   "DATE_FORMAT(chart_master_table.date_of_service, '".get_sql_date_format()."') AS date_of_service ".
			   "FROM chart_master_table ".
			   "WHERE chart_master_table.id = '$formId'";
		$row = sqlQuery($sql);
		if($row != false ){
			return $row["date_of_service"];
		}
		else{
			$sql = "SELECT ".
				   "DATE_FORMAT(chart_master_table.update_date, '".get_sql_date_format()."') AS date_of_service ".
				   "FROM chart_master_table ".
				   "WHERE chart_master_table.id = '$formId' ";
			$row = sqlQuery($sql);
			if($row != false )
			{
				return $row["date_of_service"];
			}
		}
	}

	function getGFSMedication($patientId, $formId){
		//Dos
		$dos = getDateFormatDB($this->getDateOfServiceFullYear($formId));

		//Prescription
		$strPres="";
		$sql = "select drug, start_date from prescriptions where patient_id='".$patientId."' and (date_added='".$dos."' OR date_modified='".$dos."' OR start_date='".$dos."') ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$drug = $row["drug"];
			$stDate = $row["start_date"];
			if(!empty($drug) && !empty($stDate) && ($stDate != "0000-00-00")){
				$strPres .=$drug." (".get_date_format($stDate)."), ";
			}
		}

		//Sx
		$arr = array();
		$strSx = "";
		$sql = "SELECT ".
			 "catIol,catIolEye,yagCap,yagCapEye,slt,sltEye,pi,piEye,retinalLaser,retinalLaserEye ".
			 "FROM schedule WHERE form_Id = '".$formId."' and patient_id = '".$patientId."' ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$tCat = (!empty($row["catIol"])) ? $row["catIol"]." - ".$row["catIolEye"] : "";
			$tYag = (!empty($row["yagCap"])) ? $row["yagCap"]." - ".$row["yagCapEye"] : "";
			$tSlt = (!empty($row["slt"])) ? $row["slt"]." - ".$row["sltEye"] : "";
			$tPi = (!empty($row["pi"])) ? $row["pi"]." - ".$row["piEye"] : "";
			$tRL = (!empty($row["retinalLaser"])) ? $row["retinalLaser"]." - ".$row["retinalLaserEye"] : "";

			if(!empty($tCat) && (!in_array($tCat,$arr))){
				$strSx .= "".$tCat.", ";
				$arr[] = $tCat;
			}

			if(!empty($tYag) && (!in_array($tYag,$arr))){
				$strSx .= "".$tYag.", ";
				$arr[] = $tYag;
			}

			if(!empty($tSlt) && (!in_array($tSlt,$arr))){
				$strSx .= "".$tSlt.", ";
				$arr[] = $tSlt;
			}

			if(!empty($tPi) && (!in_array($tPi,$arr))){
				$strSx .= "".$tPi.", ";
				$arr[] = $tPi;
			}

			if(!empty($tRL) && (!in_array($tRL,$arr))){
				$strSx .= "".$tRL.", ";
				$arr[] = $tRL;
			}
		}

		$str = "";
		if(!empty($strPres)){
			$str = $strPres;
		}
		if(!empty($strSx)){
			$str .= "".$strSx;
		}

		//Remove Last
		$strSx = (!empty($strSx)) ? substr($strSx, 0, -2)." " : "";

		return $str;
	}

	function insertGlucomaReading($arr){
		if(count($arr) > 0){
			$el_dos = $arr["elem_date"];
			$elem_patientId = $arr["elem_patientId"];
			$elem_formId = $arr["elem_formId"];

			//check record with 0 formId and date_of_service. if found then update that record.
			$flg_update=0;
			$sql = "SELECT * FROM glaucoma_past_readings WHERE dateReading='".$el_dos."' AND patientId='".$elem_patientId."' AND (formId='0' OR formId='".$elem_formId."') ORDER BY formId desc LIMIT 0,1    ";

			$row = sqlQuery($sql);
			if($row!=false){
				//update
					$flg_update=1;
					$insertIdGlucoma=$row["id"];
					if(empty($arr["elem_taOd"]) && !empty($row["taOd"])){$arr["elem_taOd"] = $row["taOd"];}
					if(empty($arr["elem_taOs"]) && !empty($row["taOs"])){$arr["elem_taOs"] = $row["taOs"];}
					if(empty($arr["elem_tpOd"]) && !empty($row["tpOd"])){$arr["elem_tpOd"] = $row["tpOd"];}
					if(empty($arr["elem_tpOs"]) && !empty($row["tpOs"])){$arr["elem_tpOs"] = $row["tpOs"];}
					if(empty($arr["elem_txOd"]) && !empty($row["txOd"])){$arr["elem_txOd"] = $row["txOd"];}
					if(empty($arr["elem_txOs"]) && !empty($row["txOs"])){$arr["elem_txOs"] = $row["txOs"];}
					if(empty($arr["elem_ttOd"]) && !empty($row["ttOd"])){$arr["elem_ttOd"] = $row["ttOd"];}
					if(empty($arr["elem_ttOs"]) && !empty($row["ttOs"])){$arr["elem_ttOs"] = $row["ttOs"];}
					if(empty($arr["elem_ta_time"]) && !empty($row["ta_time"])){$arr["elem_ta_time"] = $row["ta_time"];}
					if(empty($arr["elem_tp_time"]) && !empty($row["tp_time"])){$arr["elem_tp_time"] = $row["tp_time"];}
					if(empty($arr["elem_tx_time"]) && !empty($row["tx_time"])){$arr["elem_tx_time"] = $row["tx_time"];}
					if(empty($arr["elem_tt_time"]) && !empty($row["tt_time"])){$arr["elem_tt_time"] = $row["tt_time"];}
					if(empty($arr["elem_vfOdSummary"]) && !empty($row["vfOdSummary"])){$arr["elem_vfOdSummary"] = $row["vfOdSummary"];}
					if(empty($arr["elem_vfOsSummary"]) && !empty($row["vfOsSummary"])){$arr["elem_vfOsSummary"] = $row["vfOsSummary"];}
					if(empty($arr["elem_nfaOd"]) && !empty($row["nfaOdSummary"])){$arr["elem_nfaOd"] = $row["nfaOdSummary"]; }
					if(empty($arr["elem_nfaOs"]) && !empty($row["nfaOsSummary"])){$arr["elem_nfaOs"] = $row["nfaOsSummary"];}
					if(empty($arr["elem_cdOd"]) && !empty($row["cdOd"])){$arr["elem_cdOd"] = $row["cdOd"];}
					if(empty($arr["elem_cdOs"]) && !empty($row["cdOs"])){$arr["elem_cdOs"] = $row["cdOs"];}
					if(empty($arr["elem_gonioOdSummary"]) && !empty($row["gonioOdSummary"])){$arr["elem_gonioOdSummary"] = $row["gonioOdSummary"];}
					if(empty($arr["elem_gonioOsSummary"]) && !empty($row["gonioOsSummary"])){$arr["elem_gonioOsSummary"] = $row["gonioOsSummary"];}
					if(empty($arr["elem_medication"]) && !empty($row["medication"])){$arr["elem_medication"] = $row["medication"];}
					if(empty($arr["elem_pachyOdReads"]) && !empty($row["pachyOdReads"])){$arr["elem_pachyOdReads"] = $row["pachyOdReads"];}
					if(empty($arr["elem_pachyOdAvg"]) && !empty($row["pachyOdAvg"])){$arr["elem_pachyOdAvg"] = $row["pachyOdAvg"];}
					if(empty($arr["elem_pachyOdCorr"]) && !empty($row["pachyOdCorr"])){$arr["elem_pachyOdCorr"] = $row["pachyOdCorr"];}
					if(empty($arr["elem_pachyOsReads"]) && !empty($row["pachyOsReads"])){$arr["elem_pachyOsReads"] = $row["pachyOsReads"];}
					if(empty($arr["elem_pachyOsAvg"]) && !empty($row["pachyOsAvg"])){$arr["elem_pachyOsAvg"] = $row["pachyOsAvg"];}
					if(empty($arr["elem_pachyOsCorr"]) && !empty($row["pachyOsCorr"])){$arr["elem_pachyOsCorr"] = $row["pachyOsCorr"];}
					if(empty($arr["elem_diskPhotoOd"]) && !empty($row["diskPhotoOd"])){$arr["elem_diskPhotoOd"] = $row["diskPhotoOd"];}
					if(empty($arr["elem_diskPhotoOs"]) && !empty($row["diskPhotoOs"])){$arr["elem_diskPhotoOs"] = $row["diskPhotoOs"];}

					if(empty($arr["elem_diskFundus"]) && !empty($row["diskFundus"])){$arr["elem_diskFundus"] = $row["diskFundus"];}
					if(empty($arr["treatmentChange"]) && !empty($row["treatmentChange"])){$arr["treatmentChange"] = $row["treatmentChange"];}
					if(empty($arr["elem_diagnosisDate"]) && !empty($row["diagnosisDate"])){$arr["elem_diagnosisDate"] = $row["diagnosisDate"];}
					if(empty($arr["elem_highTaOdDate"]) && !empty($row["highTaOdDate"])){$arr["elem_highTaOdDate"] = $row["highTaOdDate"];}
					if(empty($arr["elem_highTaOsDate"]) && !empty($row["highTaOsDate"])){$arr["elem_highTaOsDate"] = $row["highTaOsDate"];}
					if(empty($arr["elem_highTpOdDate"]) && !empty($row["highTpOdDate"])){$arr["elem_highTpOdDate"] = $row["highTpOdDate"];}
					if(empty($arr["elem_highTpOsDate"]) && !empty($row["highTpOsDate"])){$arr["elem_highTpOsDate"] = $row["highTpOsDate"];}
					if(empty($arr["elem_highTxOdDate"]) && !empty($row["highTxOdDate"])){$arr["elem_highTxOdDate"] = $row["highTxOdDate"];}
					if(empty($arr["elem_highTxOsDate"]) && !empty($row["highTxOsDate"])){$arr["elem_highTxOsDate"] = $row["highTxOsDate"];}
					if(empty($arr["elem_highTtOdDate"]) && !empty($row["highTtOdDate"])){$arr["elem_highTtOdDate"] = $row["highTtOdDate"];}
					if(empty($arr["elem_highTtOsDate"]) && !empty($row["highTtOsDate"])){$arr["elem_highTtOsDate"] = $row["highTtOsDate"];}
					if(empty($arr["elem_vfDate"]) && !empty($row["vfDate"])){$arr["elem_vfDate"] = $row["vfDate"];}
					if(empty($arr["elem_nfaDate"]) && !empty($row["nfaDate"])){$arr["elem_nfaDate"] = $row["nfaDate"];}
					if(empty($arr["elem_gonioDate"]) && !empty($row["gonioDate"])){$arr["elem_gonioDate"] = $row["gonioDate"];}
					if(empty($arr["elem_pachyDate"]) && !empty($row["pachyDate"])){$arr["elem_pachyDate"] = $row["pachyDate"];}
					if(empty($arr["elem_diskPhotoDate"]) && !empty($row["diskPhotoDate"])){$arr["elem_diskPhotoDate"] = $row["diskPhotoDate"];}
					if(empty($arr["elem_cdDate"]) && !empty($row["cdDate"])){$arr["elem_cdDate"] = $row["cdDate"];}
					if(empty($arr["elem_cee"]) && !empty($row["cee"])){$arr["elem_cee"] = $row["cee"];}
					if(empty($arr["elem_ceeDate"]) && !empty($row["ceeDate"])){$arr["elem_ceeDate"] = $row["ceeDate"];}
					if(empty($arr["elem_ceeNotes"]) && !empty($row["ceeNotes"])){$arr["elem_ceeNotes"] = $row["ceeNotes"];}

					if(empty($arr["elem_va_od_summary"]) && !empty($row["va_od_summary"])){$arr["elem_va_od_summary"] = $row["va_od_summary"];}
					if(empty($arr["elem_va_os_summary"]) && !empty($row["va_os_summary"])){$arr["elem_va_os_summary"] = $row["va_os_summary"];}
					if(empty($arr["elem_va_od_summary_2"]) && !empty($row["va_od_summary_2"])){$arr["elem_va_od_summary_2"] = $row["va_od_summary_2"];}
					if(empty($arr["elem_va_os_summary_2"]) && !empty($row["va_os_summary_2"])){$arr["elem_va_os_summary_2"] = $row["va_os_summary_2"];}
					if(empty($arr["elem_va_od_summary_3"]) && !empty($row["va_od_summary_3"])){$arr["elem_va_od_summary_3"] = $row["va_od_summary_3"];}
					if(empty($arr["elem_va_os_summary_3"]) && !empty($row["va_os_summary_3"])){$arr["elem_va_os_summary_3"] = $row["va_os_summary_3"];}
					if(empty($arr["elem_gonio_od_summary"]) && !empty($row["gonio_od_summary"])){$arr["elem_gonio_od_summary"] = $row["gonio_od_summary"];}
					if(empty($arr["elem_gonio_os_summary"]) && !empty($row["gonio_os_summary"])){$arr["elem_gonio_os_summary"] = $row["gonio_os_summary"];}
					if(empty($arr["elem_sle_od_summary"]) && !empty($row["sle_od_summary"])){$arr["elem_sle_od_summary"] = $row["sle_od_summary"];}
					if(empty($arr["elem_sle_os_summary"]) && !empty($row["sle_os_summary"])){$arr["elem_sle_os_summary"] = $row["sle_os_summary"];}
					if(empty($arr["elem_fundus_od_summary"]) && !empty($row["fundus_od_summary"])){$arr["elem_fundus_od_summary"] = $row["fundus_od_summary"];}
					if(empty($arr["elem_fundus_os_summary"]) && !empty($row["fundus_os_summary"])){$arr["elem_fundus_os_summary"] = $row["fundus_os_summary"];}
					if(empty($arr["elem_fundus_od_cd_ratio"]) && !empty($row["fundus_od_cd_ratio"])){$arr["elem_fundus_od_cd_ratio"] = $row["fundus_od_cd_ratio"];}
					if(empty($arr["elem_fundus_os_cd_ratio"]) && !empty($row["fundus_os_cd_ratio"])){$arr["elem_fundus_os_cd_ratio"] = $row["fundus_os_cd_ratio"];}
					if(empty($arr["elem_assessment"]) && !empty($row["assessment"])){$arr["elem_assessment"] = $row["assessment"];}
					if(empty($arr["elem_plan"]) && !empty($row["plan"])){$arr["elem_plan"] = $row["plan"];}

					if(empty($arr["ocular_med"]) && !empty($row["ocular_med"])){$arr["ocular_med"] = $row["ocular_med"];}

			}

			$elem_date = imw_real_escape_string($arr["elem_date"]);
			$elem_taOd = imw_real_escape_string($arr["elem_taOd"]);
			$elem_taOs = imw_real_escape_string($arr["elem_taOs"]);
			$elem_tpOd = $arr["elem_tpOd"];
			$elem_tpOs = $arr["elem_tpOs"];
			$elem_txOd = imw_real_escape_string($arr["elem_txOd"]);
			$elem_txOs = imw_real_escape_string($arr["elem_txOs"]);
			$elem_ttOd = imw_real_escape_string($arr["elem_ttOd"]);
			$elem_ttOs = imw_real_escape_string($arr["elem_ttOs"]);
			$elem_ta_time = imw_real_escape_string($arr["elem_ta_time"]);
			$elem_tp_time = imw_real_escape_string($arr["elem_tp_time"]);
			$elem_tx_time = imw_real_escape_string($arr["elem_tx_time"]);
			$elem_tt_time = imw_real_escape_string($arr["elem_tt_time"]);

			$elem_vfOdSummary = imw_real_escape_string($arr["elem_vfOdSummary"]);
			$elem_vfOsSummary = imw_real_escape_string($arr["elem_vfOsSummary"]);
			$elem_nfaOd = imw_real_escape_string($arr["elem_nfaOd"]);
			$elem_nfaOs = imw_real_escape_string($arr["elem_nfaOs"]);
			$elem_cdOd = imw_real_escape_string($arr["elem_cdOd"]);
			$elem_cdOs = imw_real_escape_string($arr["elem_cdOs"]);
			$elem_gonioOdSummary = imw_real_escape_string($arr["elem_gonioOdSummary"]);
			$elem_gonioOsSummary = imw_real_escape_string($arr["elem_gonioOsSummary"]);
			$elem_medication = imw_real_escape_string($arr["elem_medication"]);

			$elem_pachyOdReads = imw_real_escape_string($arr["elem_pachyOdReads"]);
			$elem_pachyOdAvg = imw_real_escape_string($arr["elem_pachyOdAvg"]);
			$elem_pachyOdCorr = imw_real_escape_string($arr["elem_pachyOdCorr"]);
			$elem_pachyOsReads = imw_real_escape_string($arr["elem_pachyOsReads"]);
			$elem_pachyOsAvg = imw_real_escape_string($arr["elem_pachyOsAvg"]);
			$elem_pachyOsCorr = imw_real_escape_string($arr["elem_pachyOsCorr"]);
			$elem_diskPhotoOd = imw_real_escape_string($arr["elem_diskPhotoOd"]);
			$elem_diskPhotoOs = imw_real_escape_string($arr["elem_diskPhotoOs"]);

			$elem_diskFundus = imw_real_escape_string($arr["elem_diskFundus"]);
			$treatmentChange = imw_real_escape_string($arr["treatmentChange"]);
			$elem_timeReading = date("h:i A");
			$elem_diagnosisDate = $arr["elem_diagnosisDate"];
			$elem_highTaOdDate = $arr["elem_highTaOdDate"];
			$elem_highTaOsDate = $arr["elem_highTaOsDate"];
			$elem_highTpOdDate = $arr["elem_highTpOdDate"];
			$elem_highTpOsDate = $arr["elem_highTpOsDate"];
			$elem_highTxOdDate = $arr["elem_highTxOdDate"];
			$elem_highTxOsDate = $arr["elem_highTxOsDate"];
			$elem_highTtOdDate = $arr["elem_highTtOdDate"];
			$elem_highTtOsDate = $arr["elem_highTtOsDate"];
			$elem_vfDate = $arr["elem_vfDate"];
			$elem_nfaDate = $arr["elem_nfaDate"];
			$elem_gonioDate = $arr["elem_gonioDate"];
			$elem_pachyDate = $arr["elem_pachyDate"];
			$elem_diskPhotoDate = $arr["elem_diskPhotoDate"];
			$elem_cdDate = $arr["elem_cdDate"];
			$elem_cee = imw_real_escape_string($arr["elem_cee"]);
			$elem_ceeDate = $arr["elem_ceeDate"];
			$elem_ceeNotes = imw_real_escape_string($arr["elem_ceeNotes"]);
			$elem_timeReadingMil = $this->chTimeFormat($elem_timeReading);
			$elem_va_od_summary = imw_real_escape_string($arr["elem_va_od_summary"]);
			$elem_va_os_summary = imw_real_escape_string($arr["elem_va_os_summary"]);
			$elem_va_od_summary_2 = imw_real_escape_string($arr["elem_va_od_summary_2"]);
			$elem_va_os_summary_2 = imw_real_escape_string($arr["elem_va_os_summary_2"]);
			$elem_va_od_summary_3 = imw_real_escape_string($arr["elem_va_od_summary_3"]);
			$elem_va_os_summary_3 = imw_real_escape_string($arr["elem_va_os_summary_3"]);

			$elem_gonio_od_summary = imw_real_escape_string($arr["elem_gonio_od_summary"]);
			$elem_gonio_os_summary = imw_real_escape_string($arr["elem_gonio_os_summary"]);
			$elem_sle_od_summary = imw_real_escape_string($arr["elem_sle_od_summary"]);
			$elem_sle_os_summary = imw_real_escape_string($arr["elem_sle_os_summary"]);
			$elem_fundus_od_summary = imw_real_escape_string($arr["elem_fundus_od_summary"]);
			$elem_fundus_os_summary = imw_real_escape_string($arr["elem_fundus_os_summary"]);
			$elem_fundus_od_cd_ratio = imw_real_escape_string($arr["elem_fundus_od_cd_ratio"]);
			$elem_fundus_os_cd_ratio = imw_real_escape_string($arr["elem_fundus_os_cd_ratio"]);

			$elem_assessment = imw_real_escape_string($arr["elem_assessment"]);
			$elem_plan = imw_real_escape_string($arr["elem_plan"]);

			$elem_oct_id=$arr["oct_id"];
			$elem_vf_id=$arr["vf_id"];
			$elem_hrt_id=$arr["hrt_id"];
			$elem_med_detail=imw_real_escape_string($arr["med_detail"]);
			$ocular_med=imw_real_escape_string($arr["ocular_med"]);

			if($flg_update==1){
				//UPDATE
				$sql = "UPDATE glaucoma_past_readings SET
						taOd='".$elem_taOd."', taOs='".$elem_taOs."', tpOd='".$elem_tpOd."', tpOs='".$elem_tpOs."', txOd='".$elem_txOd."', txOs='".$elem_txOs."',
						ttOd='".$elem_ttOd."', ttOs='".$elem_ttOs."', ta_time='".$elem_ta_time."',tp_time='".$elem_tp_time."',tx_time='".$elem_tx_time."', tt_time='".$elem_tt_time."',
					vfOdSummary='".$elem_vfOdSummary."', vfOsSummary='".$elem_vfOsSummary."', nfaOdSummary='".$elem_nfaOd."', nfaOsSummary='".$elem_nfaOs."',
					cdOd='".$elem_cdOd."', cdOs='".$elem_cdOs."',
					gonioOdSummary='".$elem_gonioOdSummary."', gonioOsSummary='".$elem_gonioOsSummary."',	medication='".$elem_medication."',
					formId='".$elem_formId."',
					pachyOdReads='".$elem_pachyOdReads."',pachyOdAvg='".$elem_pachyOdAvg."',pachyOdCorr='".$elem_pachyOdCorr."',
					pachyOsReads='".$elem_pachyOsReads."',pachyOsAvg='".$elem_pachyOsAvg."',pachyOsCorr='".$elem_pachyOsCorr."',
					diskPhotoOd='".$elem_diskPhotoOd."',diskPhotoOs='".$elem_diskPhotoOs."',
					vfOd='".$elem_vfOd."',vfOs='".$elem_vfOs."',scanOd='".$elem_scanOd."',scanOs='".$elem_scanOs."',
					diskFundus='".$elem_diskFundus."',treatmentChange='".$treatmentChange."',
					diagnosisDate='".$elem_diagnosisDate."',highTaOdDate='".$elem_highTaOdDate."',
					highTaOsDate='".$elem_highTaOsDate."',highTpOdDate='".$elem_highTpOdDate."',
					highTpOsDate='".$elem_highTpOsDate."',highTxOdDate='".$elem_highTxOdDate."',
					highTxOsDate='".$elem_highTxOsDate."',
					highTtOdDate='".$elem_highTtOdDate."', highTtOsDate='".$elem_highTtOsDate."',
					vfDate='".$elem_vfDate."',
					nfaDate='".$elem_nfaDate."',gonioDate='".$elem_gonioDate."',
					pachyDate='".$elem_pachyDate."',diskPhotoDate='".$elem_diskPhotoDate."',
					cdDate='".$elem_cdDate."',cee='".$elem_cee."',ceeDate='".$elem_ceeDate."',ceeNotes='".$elem_ceeNotes."',
					time_read_mil='".$elem_timeReadingMil."',
					va_od_summary='".$elem_va_od_summary."',va_os_summary='".$elem_va_os_summary."',
					va_od_summary_2='".$elem_va_od_summary_2."',va_os_summary_2='".$elem_va_os_summary_2."',
					va_od_summary_3='".$elem_va_od_summary_3."',va_os_summary_3='".$elem_va_os_summary_3."',
					gonio_od_summary='".$elem_gonio_od_summary."',gonio_os_summary='".$elem_gonio_os_summary."',
					sle_od_summary='".$elem_sle_od_summary."',sle_os_summary='".$elem_sle_os_summary."',
					fundus_od_summary='".$elem_fundus_od_summary."',fundus_os_summary='".$elem_fundus_os_summary."',
					fundus_od_cd_ratio='".$elem_fundus_od_cd_ratio."',fundus_os_cd_ratio='".$elem_fundus_os_cd_ratio."',
					assessment='".$elem_assessment."',plan='".$elem_plan."',glucoma_med='".$elem_med_detail."',ocular_med='".$ocular_med."'

						WHERE id = '".$insertIdGlucoma."'
					";

				$ins = sqlQuery($sql);

			}else{


			/// INSERT
			$sql = "INSERT INTO
					glaucoma_past_readings
					(id, dateReading, timeReading, taOd, taOs, tpOd, tpOs, txOd, txOs, ttOd, ttOs, ta_time,tp_time,tx_time, tt_time,
					vfOdSummary, vfOsSummary, nfaOdSummary, nfaOsSummary, cdOd, cdOs,
					gonioOdSummary, gonioOsSummary,	medication,
					patientId,formId,
					pachyOdReads,pachyOdAvg,pachyOdCorr,
					pachyOsReads,pachyOsAvg,pachyOsCorr,
					diskPhotoOd,diskPhotoOs,
					vfOd,vfOs,scanOd,scanOs,
					diskFundus,treatmentChange,
					diagnosisDate,highTaOdDate,
					highTaOsDate,highTpOdDate,
					highTpOsDate,highTxOdDate,
					highTxOsDate,
					highTtOdDate, highTtOsDate,
					vfDate,
					nfaDate,gonioDate,
					pachyDate,diskPhotoDate,
					cdDate,cee,ceeDate,ceeNotes,
					time_read_mil,
					va_od_summary,va_os_summary,va_od_summary_2,va_os_summary_2,va_od_summary_3,va_os_summary_3,
					gonio_od_summary,gonio_os_summary,sle_od_summary,sle_os_summary,
					fundus_od_summary,fundus_os_summary,fundus_od_cd_ratio,fundus_os_cd_ratio,assessment,plan,glucoma_med,ocular_med
					)
					VALUES
					(NULL, '".$elem_date."', '".$elem_timeReading."', '".$elem_taOd."', '".$elem_taOs."', '".$elem_tpOd."', '".$elem_tpOs."', '".$elem_txOd."', '".$elem_txOs."', '".$elem_ttOd."', '".$elem_ttOs."', '".$elem_ta_time."', '".$elem_tp_time."', '".$elem_tx_time."', '".$elem_tt_time."',
					'".$elem_vfOdSummary."', '".$elem_vfOsSummary."', '".$elem_nfaOd."', '".$elem_nfaOs."', '".$elem_cdOd."', '".$elem_cdOs."',
					'".$elem_gonioOdSummary."', '".$elem_gonioOsSummary."',	'".$elem_medication."',
					'".$elem_patientId."', '".$elem_formId."',
					'".$elem_pachyOdReads."','".$elem_pachyOdAvg."','".$elem_pachyOdCorr."',
					'".$elem_pachyOsReads."','".$elem_pachyOsAvg."','".$elem_pachyOsCorr."',
					'".$elem_diskPhotoOd."','".$elem_diskPhotoOs."',
					'".$elem_vfOd."','".$elem_vfOs."','".$elem_scanOd."','".$elem_scanOs."',
					'".$elem_diskFundus."','".$treatmentChange."',
					'".$elem_diagnosisDate."','".$elem_highTaOdDate."',
					'".$elem_highTaOsDate."','".$elem_highTpOdDate."',
					'".$elem_highTpOsDate."','".$elem_highTxOdDate."',
					'".$elem_highTxOsDate."',
					'".$elem_highTtOsDate."','".$elem_highTtOdDate."',
					'".$elem_vfDate."',
					'".$elem_nfaDate."','".$elem_gonioDate."',
					'".$elem_pachyDate."','".$elem_diskPhotoDate."',
					'".$elem_cdDate."','".$elem_cee."','".$elem_ceeDate."','".$elem_ceeNotes."',
					'".$elem_timeReadingMil."','".$elem_va_od_summary."','".$elem_va_os_summary."',
					'".$elem_va_od_summary_2."','".$elem_va_os_summary_2."','".$elem_va_od_summary_3."','".$elem_va_os_summary_3."',
					'".$elem_gonio_od_summary."','".$elem_gonio_os_summary."','".$elem_sle_od_summary."',
					'".$elem_sle_os_summary."','".$elem_fundus_od_summary."','".$elem_fundus_os_summary."',
					'".$elem_fundus_od_cd_ratio."','".$elem_fundus_os_cd_ratio."','".$elem_assessment."','".$elem_plan."','".$elem_med_detail."','".$ocular_med."'
					)
				   ";

			$insertIdGlucoma = sqlInsert($sql);
			}

			if($elem_oct_id>0){
				imw_query("insert into glaucoma_past_test set test_type='oct',test_id='$elem_oct_id',glaucoma_past_id='$insertIdGlucoma'");
			}
			if($elem_vf_id>0){
				imw_query("insert into glaucoma_past_test set test_type='vf',test_id='$elem_vf_id',glaucoma_past_id='$insertIdGlucoma'");
			}
			if($elem_hrt_id>0){
				imw_query("insert into glaucoma_past_test set test_type='hrt',test_id='$elem_hrt_id',glaucoma_past_id='$insertIdGlucoma'");
			}
			return $insertIdGlucoma;
		}
	}


	function chTimeFormat($str){
		if(preg_match("/[0-9]{2}\:[0-9]{2}\s(PM|AM)/i",$str)){
			$tmp = $str;
			$tmp = str_replace(array("PM","AM"),"",$tmp);
			$tmp = trim($tmp);
			$arrtmp = explode(":",$tmp);
			$hr = trim($arrtmp[0]);
			$min = trim($arrtmp[1]);

			if(strpos($str,"PM") !== false && ($hr != 12)){
				$hr = $hr + 12;
			}else if(strpos($str,"AM") !== false && ($hr == 12)){
				$hr = "00";
			}

			$mil="";
			$mil=$hr.":".$min;
			return $mil;
		}
	}

	function getChartNotesReading($patient_id){
		//GET ids recorded in Past Recorded
		$strFormId = "";
		$sql = "SELECT formId
				FROM glaucoma_past_readings
				WHERE patientId = '".$patient_id."' ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++)
		{
			$strFormId .= $row["formId"].", ";
		}
		$strFormId = (!empty($strFormId))? substr(trim($strFormId),0,-1) : $strFormId;

		//
		$sql = "SELECT ".
				"
				chart_iop.puff,
				chart_iop.puff_od,
				chart_iop.puff_os_1,
				chart_iop.applanation,
				chart_iop.app_od,
				chart_iop.app_os_1,
				chart_iop.tx,
				chart_iop.tx_od,
				chart_iop.tx_os,
				".
				"chart_iop.multiple_pressure,
				chart_iop.exam_date AS examDateIop, ".

				"chart_gonio.gonio_od_summary,
				chart_gonio.gonio_os_summary,
				chart_gonio.examDateGonio, ";

		$sql .=	"vf.descOd As vf_od_summary, ".
				"vf.descOs As vf_os_summary, ".
				"vf.examDate AS examDateVf, ".
				"nfa.descOd As scan_od_summary, ".
				"nfa.descOs As scan_os_summary, ".
				"nfa.examDate AS examDateNfa, ".
				"ivfa.disc_od_summary AS ivfaDiscOdSumm, ".
				"ivfa.disc_os_summary AS ivfaDiscOsSumm, ".
				"ivfa.retina_od_summary AS ivfaRetinaOdSumm, ".
				"ivfa.retina_os_summary AS ivfaRetinaOsSumm, ".
				"ivfa.macula_od_summary AS ivfaMaculaOdSumm, ".
				"ivfa.macula_os_summary AS ivfaMaculaOsSumm, ".
				"ivfa.exam_date AS ivfaExamDate, ".
				"disc.discOdSummary AS discDiscOdSumm, ".
				"disc.discOsSummary AS discDiscOsSumm, ".
				"disc.retinaOdSummary AS discRetinaOdSumm, ".
				"disc.retinaOsSummary AS discRetinaOsSumm, ".
				"disc.maculaOdSummary AS discMaculaOdSumm, ".
				"disc.maculaOsSummary AS discMaculaOsSumm, ".
				"disc.fundusDiscPhoto, ".
				"disc.photoEye, ".
				"disc.examDate AS discExamDate, ".
				"pachy.pachy_od_readings, ".
				"pachy.pachy_od_average, ".
				"pachy.pachy_od_correction_value, ".
				"pachy.pachy_os_readings, ".
				"pachy.pachy_os_average, ".
				"pachy.pachy_os_correction_value, ".
				"pachy.examDate AS pachyExamDate, ";

		$sql .=	"chart_optic.od_text,
				chart_optic.os_text,
				chart_optic.cd_val_od,chart_optic.cd_val_os,
				chart_optic.exam_date AS examDateChartOptic,
				chart_master_table.date_of_service,
				chart_master_table.id AS form_id
				FROM chart_master_table
				LEFT JOIN chart_iop ON chart_iop.form_id = chart_master_table.id AND chart_iop.purged='0'
				LEFT JOIN chart_gonio ON chart_gonio.form_id = chart_master_table.id AND chart_gonio.purged='0'
				LEFT JOIN ivfa ON ivfa.form_id = chart_master_table.id ".

				"LEFT JOIN disc ON disc.formId = chart_master_table.id ".
				"LEFT JOIN nfa ON nfa.form_id = chart_master_table.id ".
				"LEFT JOIN pachy ON pachy.formId = chart_master_table.id ".
				"LEFT JOIN vf ON vf.formId = chart_master_table.id ".

				"LEFT JOIN chart_optic ON chart_optic.form_id = chart_master_table.id AND chart_optic.purged='0'
				LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id
				WHERE chart_master_table.patient_id = '".$patient_id."'
				AND chart_master_table.finalize = '1'
				AND chart_master_table.delete_status = '0'
				";

		$sql .= (!empty($strFormId)) ? "AND chart_master_table.id NOT IN (".$strFormId.") " : "";

		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++)
		{
			$add_form_id=$row["form_id"];
			$arr = array();
			$arr["elem_date"]=get_date_format($row["date_of_service"],'','mm-dd-yyyy');

			if(empty($row["multiple_pressure"])){
				$arr["elem_taOd"]="";
				$arr["elem_taOs"]="";
				$arr["elem_tpOd"]="";
				$arr["elem_tpOs"]="";
				$arr["elem_txOd"]="";
				$arr["elem_txOs"]="";
				$arr["elem_ta_time"]="";
				$arr["elem_tp_time"]="";
				$arr["elem_tx_time"]="";

				//Old Fields
				if($row["applanation"] == "1")
				{
					$arr["elem_taOd"]=$row["app_od"];
					$arr["elem_taOs"]=$row["app_os_1"];
				}
				if($row["puff"] == "1")
				{
					$arr["elem_tpOd"]=$row["puff_od"];
					$arr["elem_tpOs"]=$row["puff_os_1"];
				}
				if($row["tx"] == "1")
				{
					$arr["elem_txOd"]=$row["tx_od"];
					$arr["elem_txOs"]=$row["tx_os"];
				}

			}else{

				//New Fields
				$arr["elem_taOd"]="";
				$arr["elem_taOs"]="";
				$arr["elem_tpOd"]="";
				$arr["elem_tpOs"]="";
				$arr["elem_txOd"]="";
				$arr["elem_txOs"]="";
				$arr["elem_ta_time"]="";
				$arr["elem_tp_time"]="";
				$arr["elem_tx_time"]="";

				$arrMiop = $this->getMIop($row["multiple_pressure"], "GFS");
				$multiple_pressure_arr=array();
				$multiple_pressure_arr=unserialize($row["multiple_pressure"]);
				if($arrMiop["applanation"] = 1){
					$arr["elem_taOd"]=$arrMiop["app_od"];
					$arr["elem_taOs"]=$arrMiop["app_os_1"];
					$arr["elem_ta_time"]=$multiple_pressure_arr["multiplePressuer"]["elem_appTime"];
				}

				if($arrMiop["puff"] == "1")
				{
					$arr["elem_tpOd"]=$arrMiop["puff_od"];
					$arr["elem_tpOs"]=$arrMiop["puff_os_1"];
					$arr["elem_tp_time"]=$multiple_pressure_arr["multiplePressuer"]["elem_puffTime"];
				}

				if($arrMiop["tx"] == "1")
				{
					$arr["elem_txOd"]=$arrMiop["tx_od"];
					$arr["elem_txOs"]=$arrMiop["tx_os"];
					$arr["elem_tx_time"]=$multiple_pressure_arr["multiplePressuer"]["elem_xTime"];
				}

				if($arrMiop["tt"] == "1")
				{
					$arr["elem_ttOd"]=$arrMiop["tt_od"];
					$arr["elem_ttOs"]=$arrMiop["tt_os"];
					$arr["elem_tt_time"]=$multiple_pressure_arr["multiplePressuer"]["elem_ttTime"];
				}
			}

			$arr["elem_vfOdSummary"]= $row["vf_od_summary"];
			$arr["elem_vfOsSummary"]=$row["vf_os_summary"];

			$arr["elem_nfaOd"]=$row["scan_od_summary"];
			$arr["elem_nfaOs"]=$row["scan_os_summary"];

			$arr["elem_cdOd"]=(!empty($row["cd_val_od"])) ? $row["cd_val_od"] : $row["od_text"];
			$arr["elem_cdOs"]=(!empty($row["cd_val_os"])) ? $row["cd_val_os"] : $row["os_text"];

			$arr["elem_gonioOdSummary"]=$row["gonio_od_summary"]; //(!empty($row["gonio_od_summary"])) ? "Done" : "Empty"; //
			$arr["elem_gonioOsSummary"]=$row["gonio_os_summary"]; //(!empty($row["gonio_os_summary"])) ? "Done" : "Empty"; //

			//Medication
			$arr["elem_medication"]= $this->getGFSMedication($patient_id,$row["form_id"]);

			$arr["elem_patientId"]=$patient_id;
			$arr["elem_formId"]=$row["form_id"];

			$arr["elem_pachyOdReads"]=$row["pachy_od_readings"];
			$arr["elem_pachyOdAvg"]=$row["pachy_od_average"];
			$arr["elem_pachyOdCorr"]=$row["pachy_od_correction_value"];
			$arr["elem_pachyOsReads"]=$row["pachy_os_readings"];
			$arr["elem_pachyOsAvg"]=$row["pachy_os_average"];
			$arr["elem_pachyOsCorr"]=$row["pachy_os_correction_value"];

			$arr["elem_diskPhotoOd"]=(($row["fundusDiscPhoto"] == "1") && (($row["photoEye"] == "OU") || ($row["photoEye"] == "OD"))) ? "Done" : "Empty";
			$arr["elem_diskPhotoOs"]=(($row["fundusDiscPhoto"] == "1") && (($row["photoEye"] == "OU") || ($row["photoEye"] == "OS"))) ? "Done" : "Empty";

			$arr["elem_vfOd"]=$row["vf_od"];
			$arr["elem_vfOs"]=$row["vf_os"];
			$arr["elem_scanOd"]=$row["scan_od"];
			$arr["elem_scanOs"]=$row["scan_os"];
			$arr["elem_diskFundus"]=($row["fundusDiscPhoto"] == "1") ? "Disc" : "";

			if(get_number($row["elem_diagnosisDate"]) != 0)
			$arr["elem_diagnosisDate"] = get_date_format(date('Y-m-d', strtotime($row["elem_diagnosisDate"])));

			if(get_number($row["examDateIop"]) != 0){
			$row["examDateIop"] = date('Y-m-d', strtotime($row["examDateIop"]));
			$arr["elem_highTaOdDate"] = get_date_format($row["examDateIop"],'','mm-dd-yyyy');
			$arr["elem_highTaOsDate"] = get_date_format($row["examDateIop"],'','mm-dd-yyyy');
			$arr["elem_highTpOdDate"] = get_date_format($row["examDateIop"],'','mm-dd-yyyy');
			$arr["elem_highTpOsDate"] = get_date_format($row["examDateIop"],'','mm-dd-yyyy');
			$arr["elem_highTxOdDate"] = get_date_format($row["examDateIop"],'','mm-dd-yyyy');
			$arr["elem_highTxOsDate"] = get_date_format($row["examDateIop"],'','mm-dd-yyyy');
			$arr["elem_highTtOdDate"] = get_date_format($row["examDateIop"],'','mm-dd-yyyy');
			$arr["elem_highTtOsDate"] = get_date_format($row["examDateIop"],'','mm-dd-yyyy');
			}
			$arr["elem_vfDate"] = get_date_format($row["examDateVf"],'','mm-dd-yyyy');
			$arr["elem_nfaDate"] = get_date_format($row["examDateNfa"],'','mm-dd-yyyy');
			$arr["elem_pachyDate"] = get_date_format($row["pachyExamDate"],'','mm-dd-yyyy');
			$arr["elem_diskPhotoDate"] = get_date_format($row["discExamDate"],'','mm-dd-yyyy');
			$arr["elem_cdDate"] = get_date_format($row["examDateChartOptic"],'','mm-dd-yyyy');
			if(get_number($row["examDateGonio"]) != 0)
			$arr["elem_gonioDate"] = get_date_format(date('Y-m-d', strtotime($row["examDateGonio"])),'','mm-dd-yyyy');

			$q = "SELECT
			c3.sel_od AS vis_dis_od_sel_1, c3.txt_od AS vis_dis_od_txt_1, c3.sel_os AS vis_dis_os_sel_1, c3.txt_os AS vis_dis_os_txt_1,
			c4.sel_od AS vis_dis_od_sel_2, c4.txt_od AS vis_dis_od_txt_2, c4.sel_os AS vis_dis_os_sel_2, c4.txt_os AS vis_dis_os_txt_2,
			c5.sel_od AS vis_dis_od_sel_3, c5.txt_od AS vis_dis_od_txt_3, c5.sel_os AS vis_dis_os_sel_3, c5.txt_os AS vis_dis_os_txt_3
			FROM chart_vis_master c2
			LEFT JOIN chart_acuity c3 ON c3.id_chart_vis_master = c2.id AND c3.sec_indx = '1' AND c3.sec_name = 'Distance'
			LEFT JOIN chart_acuity c4 ON c4.id_chart_vis_master = c2.id AND c4.sec_indx = '2' AND c4.sec_name = 'Distance'
			LEFT JOIN chart_acuity c5 ON c5.id_chart_vis_master = c2.id AND c5.sec_indx = '3' AND c5.sec_name = 'Distance'
			WHERE c2.form_id = '$add_form_id' AND patient_id='$patient_id'
			";
			$sql_va = imw_query($q);
			$row_va = imw_fetch_array($sql_va);
			if($row_va['vis_dis_od_sel_1']=='CC'){
				$arr["elem_va_od_summary"]=$row_va["vis_dis_od_txt_1"];
			}
			if($row_va['vis_dis_os_sel_1']=='CC'){
				$arr["elem_va_os_summary"]=$row_va["vis_dis_os_txt_1"];
			}
			if($row_va['vis_dis_od_sel_2']=='CC'){
				$arr["elem_va_od_summary_2"]=$row_va["vis_dis_od_txt_2"];
			}
			if($row_va['vis_dis_os_sel_2']=='CC'){
				$arr["elem_va_os_summary_2"]=$row_va["vis_dis_os_txt_2"];
			}
			if($row_va['vis_dis_od_sel_3']=='CC'){
				$arr["elem_va_od_summary_3"]=$row_va["vis_dis_od_txt_3"];
			}
			if($row_va['vis_dis_os_sel_3']=='CC'){
				$arr["elem_va_os_summary_3"]=$row_va["vis_dis_os_txt_3"];
			}

			$sql_gonio = imw_query("select gonio_od_summary,gonio_os_summary from chart_gonio where form_id='$add_form_id' and patient_id='$patient_id'");
			$row_gonio = imw_fetch_array($sql_gonio);
			$arr["elem_gonio_od_summary"]=$row_gonio["gonio_od_summary"];
			$arr["elem_gonio_os_summary"]=$row_gonio["gonio_os_summary"];

			$sql_sle = imw_query("select conjunctiva_od_summary,conjunctiva_os_summary from chart_conjunctiva where form_id='$add_form_id' and patient_id='$patient_id'");
			$row_sle = imw_fetch_array($sql_sle);
			$arr["elem_sle_od_summary"]=$row_sle["conjunctiva_od_summary"];
			$arr["elem_sle_os_summary"]=$row_sle["conjunctiva_os_summary"];

			$sql_fundus=imw_query("select optic_nerve_od_summary,optic_nerve_os_summary,cdr_od_summary,cdr_os_summary from chart_optic where form_id='$add_form_id' and patient_id='$patient_id'");
			$row_fundus=imw_fetch_array($sql_fundus);
			$arr["elem_fundus_od_summary"]=$row_fundus["optic_nerve_od_summary"];
			$arr["elem_fundus_os_summary"]=$row_fundus["optic_nerve_os_summary"];
			$arr["elem_fundus_od_cd_ratio"]=$row_fundus["cdr_od_summary"];
			$arr["elem_fundus_os_cd_ratio"]=$row_fundus["cdr_os_summary"];

			$sql_ap=imw_query("select assess_plan from chart_assessment_plans where form_id='$add_form_id' and patient_id='$patient_id'");
			$row_ap=imw_fetch_array($sql_ap);

			//Set Assess Plan Resolve NE Value FROM xml ---
			$strXml = stripslashes($row_ap["assess_plan"]);
			$oChartApXml = new ChartAP($patient_id,$add_form_id);
			//$arrApVals = $oChartApXml->getVal_Str($strXml);
			$arrApVals = $oChartApXml->getVal();
			$arrAp = $arrApVals["data"]["ap"];
			//print_r($arrAp);

			$lenAssess = count($arrAp);
			for($i=0;$i<$lenAssess;$i++){
				$j=$i+1;
				$elem_assessment = "txt_assessment_".$j;
				$$elem_assessment = $arrAp[$i]["assessment"];
				$elem_plan = "ta_plan_notes_".$j;
				$$elem_plan = $arrAp[$i]["plan"];
				$elem_resolve = "check_plan_resolve_".$j;
				$$elem_resolve = $arrAp[$i]["resolve"];
				$no_change_Assess = "no_change_".$j;
				$$no_change_Assess = $arrAp[$i]["ne"];
			}
			$ass_arr=array();
			$plan_arr=array();
			 if($lenAssess>0){
				for($j=1,$i=0;$i<$lenAssess;$i++,$j++){
					$tmpAssess = "txt_assessment_".$j;
					$tmpPlan = "ta_plan_notes_".$j;
					if($$tmpAssess!=""){
						$ass_arr[]=	"<div style='float:left; font-weight:bold'>".$j.".&nbsp;</div><div style='padding-left:20px;'>".$$tmpAssess."</div>";
					}
					if($$tmpPlan!=""){
						$plan_arr[]="<div style='float:left; font-weight:bold'>".$j.".&nbsp;</div><div style='padding-left:20px;'>".$$tmpPlan."</div>";
					}
				}
			}
			$arr["elem_assessment"]=implode('<div style="border-bottom:1px solid #CCE6FF; width:100%; height:4px;">&nbsp;</div>',$ass_arr);
			$arr["elem_plan"]=implode('<div style="border-bottom:1px solid #CCE6FF; width:100%; height:4px;">&nbsp;</div>',$plan_arr);

			$sql_oct=imw_query("select oct_id from oct where form_id='$add_form_id' and patient_id='$patient_id'");
			$row_oct=imw_fetch_array($sql_oct);
			$arr["oct_id"]=$row_oct["oct_id"];

			$sql_vf=imw_query("select vf_id from vf where formId='$add_form_id' and patientId='$patient_id'");
			$row_vf=imw_fetch_array($sql_vf);
			$arr["vf_id"]=$row_vf["vf_id"];

			$sql_hrt=imw_query("select nfa_id from nfa where form_id='$add_form_id' and patient_id='$patient_id'");
			$row_hrt=imw_fetch_array($sql_hrt);
			$arr["hrt_id"]=$row_hrt["nfa_id"];

			$sql_med=imw_query("select lists from chart_genhealth_archive where form_id='$add_form_id' and patient_id='$patient_id'");
			$row_med=imw_fetch_array($sql_med);
			$list_arr=unserialize($row_med["lists"]);
			$glucoma_med_arr=array();
			$past_glucoma_med_arr=array();
			if(count($list_arr[4])>0){
				$dt_lists_qry = "SELECT title FROM lists WHERE pid = '".$this->pid."' and allergy_status = 'Discontinue'";
				$dt_lists_qry_obj = imw_query($dt_lists_qry);
				$dt_lists_arr = array();
				while($dt_lists_data = imw_fetch_assoc($dt_lists_qry_obj))
				{
					$dt_lists_arr[] = $dt_lists_data["title"];
				}

				$sql_med=imw_query("select medicine_name from medicine_data where glucoma='1' and del_status = '0'");
				while($row_med=imw_fetch_array($sql_med)){
					$glucoma_med_arr[]=$row_med["medicine_name"];
				}
			}
			$past_glucoma_allergies_id_arr=array();
			$past_glucoma_allergies_title_arr=array();
			$past_glucoma_allergies_date_arr=array();
			$past_glucoma_allergies_date_arr=array();
			for($k=0;$k<=count($list_arr[7]);$k++){
				if($list_arr[7][$k]['type']==7 && $list_arr[7][$k]['ag_occular_drug']=='fdbATDrugName'){
					$past_glucoma_allergies_id_arr[$list_arr[7][$k]['title']]=$list_arr[7][$k]['id'];
					$past_glucoma_allergies_title_arr[$list_arr[7][$k]['title']]=$list_arr[7][$k]['title'];
					$past_glucoma_allergies_date_arr[$list_arr[7][$k]['title']]=get_date_format($list_arr[7][$k]['begdate']);
					$past_glucoma_allergies_comment_arr[$list_arr[7][$k]['title']]=$list_arr[7][$k]['comments'];
				}
			}
			$ocular_med_arr=array();
			for($k=0;$k<=count($list_arr[4]);$k++){
					if($list_arr[4][$k]['type']==4 && $list_arr[4][$k]['allergy_status']=='Active' && ($list_arr[4][$k]['enddate'] >= date('Y-m-d') || $list_arr[4][$k]['enddate'] == '0000-00-00' || $list_arr[4][$k]['enddate'] == '' || $list_arr[4][$k]['enddate'] == NULL)){
						if(in_array($list_arr[4][$k]['title'],$glucoma_med_arr)){
							if(in_array($list_arr[4][$k]['title'],$past_glucoma_allergies_title_arr)){
								if($arr["elem_date"]>=$past_glucoma_allergies_date_arr[$list_arr[4][$k]['title']]){
									$past_glucoma_med_arr[]='<font color="red">'.$list_arr[4][$k]['title'].'</font>';
								}else{
									$med_col="";
									if($list_arr[4][$k]['sites']==1){
										//OS
										$med_col="green";
									}
									if($list_arr[4][$k]['sites']==2){
										//OD
										$med_col="blue";
									}
									if($list_arr[4][$k]['sites']==3){
										//OU
										$med_col="black";
									}
									if($list_arr[4][$k]['sites']==4){
										//PO
										$med_col="black";
									}
									$past_glucoma_med_arr[]='<font color="'.$med_col.'">'.$list_arr[4][$k]['title'].'</font>';
								}
							}else{
								$med_col="";
								if($list_arr[4][$k]['sites']==1){
									//OS
									$med_col="green";
								}
								if($list_arr[4][$k]['sites']==2){
									//OD
									$med_col="blue";
								}
								if($list_arr[4][$k]['sites']==3){
									//OU
									$med_col="black";
								}
								if($list_arr[4][$k]['sites']==4){
									//PO
									$med_col="black";
								}
								$past_glucoma_med_arr[]='<font color="'.$med_col.'">'.$list_arr[4][$k]['title'].'</font>';
							}
					}else{
						if($list_arr[4][$k]['sites'] == 3){
							$site = "OU";
						}elseif($list_arr[4][$k]['sites'] == 2){
							$site = "OD";
						}elseif($list_arr[4][$k]['sites'] == 1){
							$site = "OS";
						}elseif($list_arr[4][$k]['sites'] == 4){
							$site = "PO";
						}else $site = '';
						$ocular_med_arr[] = $list_arr[4][$k]['title']." ".$site;
					}
				}
			}
			$arr["med_detail"]=implode(', ',$past_glucoma_med_arr);
			$arr["ocular_med"]=implode(', ',$ocular_med_arr);
			// INSERT Readings
			$this->insertGlucomaReading($arr);
		}
	}


	function getMIop($str, $callfrom=""){
		$arr=array();
		$strAll="";
		$tmpiop = unserialize($str);

		$len = count($tmpiop);
		for($k=0;$k<$len;$k++){

			$iter = ($k==0) ? "" : $k+1;
			$iter1 = ($iter=="") ? "" : $k;
			//Ta
			if($tmpiop["multiplePressuer".$iter]["elem_applanation".$iter1] == 1){

				$tta_od = $tmpiop["multiplePressuer".$iter]["elem_appOd".$iter1];
				$tta_os = $tmpiop["multiplePressuer".$iter]["elem_appOs".$iter1];
				$tta_time = $tmpiop["multiplePressuer".$iter]["elem_appTime".$iter1];

				if(!empty($tta_od) || !empty($tta_os)){
					$arr["applanation"]="1";
					if($callfrom=="GFS"){
					if(!empty($tta_od)){$arr["app_od"] = $tta_od;}
					if(!empty($tta_os)){$arr["app_os_1"] = $tta_os;}
					}else{
					$arr["app_od"] = (!empty($tta_od)) ? $tta_od : "";
					$arr["app_os_1"] = (!empty($tta_os)) ? $tta_os : "";
					}
					$arr["app_time"] = (!empty($tta_time)) ? $tta_time : "";
					$strAll .= "T<sub><b>A</b></sub>:<span class=\"pag_iop\">".$arr["app_od"]."</span>, <span class=\"pag_iop\">".$arr["app_os_1"]."</span> <span class=\"pag_iopT pag_iop \">".$arr["app_time"]."</span><br/>";
				}
			}

			//Tp
			if($tmpiop["multiplePressuer".$iter]["elem_puff".$iter1] == 1){

				$ttp_od = $tmpiop["multiplePressuer".$iter]["elem_puffOd".$iter1];
				$ttp_os = $tmpiop["multiplePressuer".$iter]["elem_puffOs".$iter1];
				$ttp_time = $tmpiop["multiplePressuer".$iter]["elem_puffTime".$iter1];

				if(!empty($ttp_od) || !empty($ttp_os)){
					$arr["puff"]="1";

					if($callfrom=="GFS"){
					if(!empty($ttp_od)){ $arr["puff_od"] =  $ttp_od ;}
					if(!empty($ttp_os)){$arr["puff_os_1"] =  $ttp_os ;}
					}else{
					$arr["puff_od"] = (!empty($ttp_od)) ? $ttp_od : "";
					$arr["puff_os_1"] = (!empty($ttp_os)) ? $ttp_os : "";
					}
					$arr["puff_time"] = (!empty($ttp_time)) ? $ttp_time : "";
					$strAll .= "T<sub><b>P</b></sub>:<span class=\"pag_iop\">".$arr["puff_od"]."</span>, <span class=\"pag_iop\">".$arr["puff_os_1"]."</span> <span class=\"pag_iopT pag_iop \">".$arr["puff_time"]."</span><br/>";
				}
			}

			//Tx
			if($tmpiop["multiplePressuer".$iter]["elem_tx".$iter1] == 1){

				$ttp_od = $tmpiop["multiplePressuer".$iter]["elem_appTrgtOd".$iter1];
				$ttp_os = $tmpiop["multiplePressuer".$iter]["elem_appTrgtOs".$iter1];
				$ttp_time = $tmpiop["multiplePressuer".$iter]["elem_xTime".$iter1];

				if(!empty($ttp_od) || !empty($ttp_os)){
					$arr["tx"]="1";
					if($callfrom=="GFS"){
					if(!empty($ttp_od)){ $arr["tx_od"] =  $ttp_od ;}
					if(!empty($ttp_os)){$arr["tx_os"] =  $ttp_os ;}
					}else{
					$arr["tx_od"] = (!empty($ttp_od)) ? $ttp_od : "";
					$arr["tx_os"] = (!empty($ttp_os)) ? $ttp_os : "";
					}
					$arr["tx_time"] = (!empty($ttp_time)) ? $ttp_time : "";
					$strAll .= "T<sub><b>X</b></sub>:<span class=\"pag_iop\">".$arr["tx_od"]."</span>, <span class=\"pag_iop\">".$arr["tx_os"]."</span> <span class=\"pag_iopT pag_iop \">".$arr["tx_time"]."</span><br/>";
				}
			}

			//Tt
			if($tmpiop["multiplePressuer".$iter]["elem_tt".$iter1] == 1){

				$ttp_od = $tmpiop["multiplePressuer".$iter]["elem_tactTrgtOd".$iter1];
				$ttp_os = $tmpiop["multiplePressuer".$iter]["elem_tactTrgtOs".$iter1];
				$ttp_time = $tmpiop["multiplePressuer".$iter]["elem_ttTime".$iter1];

				if(!empty($ttp_od) || !empty($ttp_os)){
					$arr["tt"]="1";
					if($callfrom=="GFS"){
					if(!empty($ttp_od)){ $arr["tt_od"] =  $ttp_od ;}
					if(!empty($ttp_os)){$arr["tt_os"] =  $ttp_os ;}
					}else{
					$arr["tt_od"] = (!empty($ttp_od)) ? $ttp_od : "";
					$arr["tt_os"] = (!empty($ttp_os)) ? $ttp_os : "";
					}
					$arr["tt_time"] = (!empty($ttp_time)) ? $ttp_time : "";
					$strAll .= "T<sub><b>t</b></sub>:<span class=\"pag_iop\">".$arr["tt_od"]."</span>, <span class=\"pag_iop\">".$arr["tt_os"]."</span> <span class=\"pag_iopT pag_iop \">".$arr["tt_time"]."</span><br/>";
				}
			}
		}

		//Join All summary --
		if($strAll!=""){
			$arr["iop"]=$strAll;
		}
		return $arr;
	}

	function getPatientInfo($id){
		$str = "";
		$sql = "SELECT
				id,title,
				fname,lname,mname,
				DOB
				FROM patient_data
				WHERE id = '".$id."' ";
		$row = sqlQuery($sql);
		if($row != false)
		{
			$patientName = "";
			$patientName .= (!empty($row["title"])) ? $row["title"]." " : "";
			$patientName .= (!empty($row["fname"])) ? $row["fname"] : "";
			$patientName .= (!empty($row["lname"])) ? " ".$row["lname"] : "";
			$patientName .= (!empty($row["mname"])) ? " ".substr($row["mname"],0,1)."." : "";
			$pId = $row["id"];
			if(!empty($row["DOB"])){
				$dob = $row["DOB"];
				$age = get_age($dob);
			}
			$str = $patientName." - ".$pId."&nbsp;&nbsp;&nbsp;";
			$str .= (!empty($row["DOB"])) ? "DOB: ".get_date_format($dob)." (".$age.")" : "" ;
		}
		return $str;
	}

	function refineMenuValue($str){
	   $ret = "";
		if(!empty($str)){
			switch($str){
				case "Normal":
				$ret = "NL";
				break;

				case "Border Line":
				$ret = "BL";
				break;

				case "Increase Abnormal":
				$ret = "<span class=\"spechar\">&#8593;</span>Abn";
				break;

				case "Decrease Abnormal":
				$ret = "<span class=\"spechar\">&#8595;</span>Abn";
				break;

				case "No Change Abnormal":
				$ret = "<span class=\"spechar\">&#916;</span>NC";
				break;

				case "Abnormal":
				$ret = "Abn";
				break;

				case "Empty":
				$ret = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				break;

				case "PS":
				$ret = "Rel";
				break;

				case "Stable":
				$ret = "St";
				break;

				default:
				$ret = "";
				break;
			}
		}
	  return $ret;
	}

	function checkWrongDate($str){
		if(preg_replace('/[^0-9]/','',$str) == "00000000"){
			return "";
		}
		return $str;
	}

	function getIopTargets($patientId){
		$targetTpOd = $targetTpOs = $targetTaOd = $targetTaOs = "";
		$sql = "SELECT
			  chart_master_table.id,
			  chart_iop.iop_id,
			  chart_iop.puff_trgt_od,
			  chart_iop.puff_trgt_os,
			  chart_iop.app_trgt_od,
			  chart_iop.app_trgt_os,
			  chart_iop.trgtOd,
			  chart_iop.trgtOs,
			  chart_iop.exam_date
			  FROM chart_master_table
			  INNER JOIN chart_iop ON chart_iop.form_id = chart_master_table.id
			  WHERE chart_master_table.patient_id='".$patientId."'
			  AND chart_master_table.delete_status = '0'
			  AND ((chart_iop.puff_trgt_od != '') OR (chart_iop.puff_trgt_os != '') OR
				  (chart_iop.app_trgt_od != '') OR (chart_iop.app_trgt_os != '') OR
				  (chart_iop.trgtOd != '') OR (chart_iop.trgtOs != '') )
			  ORDER BY chart_master_table.update_date DESC, chart_master_table.id DESC
			  LIMIT 0,1
			";
		$row = sqlQuery($sql);
		if($row != false)
		{
			$targetTpOd = !empty($row["puff_trgt_od"]) ? $row["puff_trgt_od"] :$targetTpOd ;
			$targetTpOs = !empty($row["puff_trgt_os"]) ? $row["puff_trgt_os"] : $targetTpOs;
			$targetTaOd = !empty($row["app_trgt_od"]) ? $row["app_trgt_od"] : $targetTaOd;
			$targetTaOs = !empty($row["app_trgt_os"]) ? $row["app_trgt_os"] : $targetTaOs;
			$trgtOd = !empty($row["trgtOd"]) ? $row["trgtOd"] : $targetTaOd;
			$trgtOs = !empty($row["trgtOs"]) ? $row["trgtOs"] : $targetTaOs;
		}

		//return array($targetTaOd,$targetTaOs,$targetTpOd,$targetTpOs);
		return array($trgtOd,$trgtOs);
	}

	function getMenuValue($str){
		$retStr = "";
		$arrCheck = array("Normal","Border Line", "PS", "Increase Abnormal", "Decrease Abnormal", "No Change Abnormal", "Abnormal");
		if(!empty($str)){
		    foreach($arrCheck as $key => $val){
			    if(strpos($str,$val) !== false){
				    $retStr = $val;
				    break;
			    }
		    }
                }
		return $retStr;
	}

	function getIntialTop($flg_pachy=0){
		$patient_id=$this->pid;
		$highTaOd = $highTaOs = $highTpOd = $highTpOs = $highTxOd = $highTxOs = NULL;
		$arrInitialTop = array();
		$sql = "SELECT
			  glaucoma_past_readings.*,
			  SUBSTRING_INDEX(dateReading,'-',-1) AS strYear,
			  IF(dateReading REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$',CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(dateReading,'-',2),'-',-1) AS SIGNED),0) AS strDate,
			  IF(dateReading REGEXP '^[0-9]{4}$',0,CAST(SUBSTRING_INDEX(dateReading,'-',1) AS SIGNED)) AS strMonth
			  FROM glaucoma_past_readings
			  WHERE patientId ='".$patient_id."'
			  ORDER BY strYear DESC, strMonth DESC, strDate DESC, time_read_mil DESC, id DESC
			 ";

		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++)
		{
			//Pachy
			$tmpOd = trim($row["pachyOdCorr"]);
			$tmpOs = trim($row["pachyOsCorr"]);
			if(!empty($tmpOd) || !empty($tmpOs) || !empty($row["pachyOdReads"]) || !empty($row["pachyOsReads"]))
			{
				$date = wv_formatDate($row["pachyDate"],0,0,"",'mm-dd-yyyy');
				$arrInitialTop["Pachy"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"],
												  "od" => array("Read" => $row["pachyOdReads"], "Avg" => $row["pachyOdAvg"],
												  "Corr" => $row["pachyOdCorr"]), "os" => array("Read" => $row["pachyOsReads"],
												  "Avg" => $row["pachyOsAvg"], "Corr" => $row["pachyOsCorr"]));
			}

			if(empty($flg_pachy)){ //if all

				// High Ta Od
				if(($row["taOd"] > $highTaOd) || (($highTaOd == NULL) && !empty($row["taOd"])))
				{
					$date = wv_formatDate($row["highTaOdDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["HighTaOd"] = array("id" => $row["id"],"od" => $row["taOd"],
													  "os" => $row["taOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTaOd = $row["taOd"];
				}
				// High Ta Os
				if(($row["taOs"] > $highTaOs) || (($highTaOs == NULL) && !empty($row["taOs"])))
				{
					$date = wv_formatDate($row["highTaOsDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["HighTaOs"] = array("id" => $row["id"],"od" => $row["taOd"],
														"os" => $row["taOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTaOs = $row["taOs"];
				}
				// High Tp Od
				if(($row["tpOd"] > $highTpOd) || (($highTpOd == NULL) && !empty($row["tpOd"])))
				{
					$date = wv_formatDate($row["highTpOdDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["HighTpOd"] = array("id" => $row["id"],"od" => $row["tpOd"],
														"os" => $row["tpOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTpOd = $row["tpOd"];
				}
				// High Tp Os
				if(($row["tpOs"] > $highTpOs) || (($highTpOs == NULL) && !empty($row["tpOs"])))
				{
					$date = wv_formatDate($row["highTpOsDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["HighTpOs"] = array("id" => $row["id"],"od" => $row["tpOd"],
														"os" => $row["tpOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTpOs = $row["tpOs"];
				}
				// High Tx Od
				if(($row["txOd"] > $highTxOd) || (($highTxOd == NULL) && !empty($row["txOd"])))
				{
					$date = wv_formatDate($row["highTxOdDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["HighTxOd"] = array("id" => $row["id"],"od" => $row["txOd"],
														"os" => $row["txOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTxOd = $row["txOd"];
				}
				// High Tx Os
				if(($row["txOs"] > $highTxOs) || (($highTxOs == NULL) && !empty($row["txOs"])))
				{
					$date = wv_formatDate($row["highTxOsDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["HighTxOs"] = array("id" => $row["id"],"od" => $row["txOd"],
														"os" => $row["txOs"], "date" => $date,"new"=>$row["rec_status"]);
					$highTxOs = $row["txOs"];
				}


				// VF
				if(!empty($row["vfOdSummary"]) || !empty($row["vfOsSummary"]))
				{
					$date = wv_formatDate($row["vfDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["VF"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"],
													"od" => $this->getMenuValue($row["vfOdSummary"]),
													"os" => $this->getMenuValue($row["vfOsSummary"]));
				}
				// NFA
				if(!empty($row["nfaOdSummary"]) || !empty($row["nfaOsSummary"]))
				{
					$date = wv_formatDate($row["nfaDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["NFA"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"],
													"od" => $this->getMenuValue($row["nfaOdSummary"]),
													"os" => $this->getMenuValue($row["nfaOsSummary"]));
				}

				// Gonio
				if((!empty($row["gonioOdSummary"]) && ($row["gonioOdSummary"] != "Empty")) ||
					(!empty($row["gonioOsSummary"]) && ($row["gonioOsSummary"] != "Empty")))
				{
					$date = wv_formatDate($row["gonioDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["Gonio"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"],
													  "od" => $row["gonioOdSummary"], "os" => $row["gonioOsSummary"]);
				}

				//Disk Photo
				if(($row["diskPhotoOd"] == "Done") || ($row["diskPhotoOs"] == "Done"))
				{
					$date = wv_formatDate($row["diskPhotoDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["Disk"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"],
													  "od" =>$row["diskPhotoOd"] , "os" =>$row["diskPhotoOs"] );
				}
				//CD
				if(!empty($row["cdOd"]) || !empty($row["cdOs"]))
				{
					$date = wv_formatDate($row["cdDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["CD"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"],
													  "od" =>$row["cdOd"] , "os" =>$row["cdOs"] );
				}
				//CEE
				if(!empty($row["cee"]))
				{
					$date = wv_formatDate($row["ceeDate"],0,0,"",'mm-dd-yyyy');
					$arrInitialTop["CEE"][] = array("id" => $row["id"], "date" => $date,"new"=>$row["rec_status"],
													  "cee" => $row["cee"],"notes"=>$row["ceeNotes"]);
				}
			}
		}
		return $arrInitialTop;
	}

	function getIopTrgtDef($pId,$formId=0,$strict=0){
		$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='".$formId."' ";
		$row = sqlQuery($sql);
		if( ($row == false) && ($formId != 0) && ($strict == 0) ){
			$sql = "SELECT * FROM tbl_def_val WHERE ptId='".$pId."' AND form_id='0' ";
			$row = sqlQuery($sql);
		}
		return $row;
	}

	function isChartOpened($pid){
		$sql = "SELECT id FROM chart_master_table
				WHERE patient_id='".$pid."' AND finalize='0' AND delete_status='0'
				ORDER BY id DESC LIMIT 0,1 ";
		$row = sqlQuery($sql);
		if($row != false){
			return $row["id"];
		}
		return false;
	}

	function setDefPachyVals($formId=0){
		$elem_pachy_od_readings=$elem_pachy_od_average=$elem_pachy_od_correction_value="";
		$elem_pachy_os_readings=$elem_pachy_os_average=$elem_pachy_os_correction_value="";

		if(!empty($formId)){
			//
			$sql="SELECT reading_od,avg_od,cor_val_od,reading_os,avg_os,cor_val_os,cor_date
				  FROM chart_correction_values WHERE patient_id='".$this->pid."' AND form_id='".$formId."'  ";
			$row=sqlQuery($sql);
			if($row!=false){
				$elem_pachy_od_readings=$row["reading_od"];
				$elem_pachy_od_average=$row["avg_od"];
				$elem_pachy_od_correction_value=trim($row["cor_val_od"]);
				$elem_pachy_os_readings=$row["reading_os"];
				$elem_pachy_os_average=$row["avg_os"];
				$elem_pachy_os_correction_value=trim($row["cor_val_os"]);
				if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = get_date_format($row["cor_date"]);
			}

			//Pachy
			if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
				$sql = "SELECT pachy_od_readings,pachy_od_average,pachy_od_correction_value,
						pachy_os_readings,pachy_os_average,pachy_os_correction_value,examDate
						FROM pachy WHERE formId='".$formId."' AND patientId='".$this->pid."' ";
				$row=sqlQuery($sql);
				if($row!=false){
					$elem_pachy_od_readings=$row["pachy_od_readings"];
					$elem_pachy_od_average=$row["pachy_od_average"];
					$elem_pachy_od_correction_value=trim($row["pachy_od_correction_value"]);

					$elem_pachy_os_readings=$row["pachy_os_readings"];
					$elem_pachy_os_average=$row["pachy_os_average"];
					$elem_pachy_os_correction_value=trim($row["pachy_os_correction_value"]);
					if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = get_date_format($row["examDate"]);
				}
				//
			}

			//A/scan
			if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
				$sql = "SELECT pachymetryValOD,pachymetryCorrecOD,
						pachymetryValOS,pachymetryCorrecOS,examDate
						FROM surgical_tbl WHERE form_id='".$formId."' AND patient_id='".$this->pid."' ";
				$row=sqlQuery($sql);
				if($row!=false){
					$elem_pachy_od_readings=$row["pachymetryValOD"];
					$elem_pachy_od_correction_value=trim($row["pachymetryCorrecOD"]);

					$elem_pachy_os_readings=$row["pachymetryValOS"];
					$elem_pachy_os_correction_value=trim($row["pachymetryCorrecOS"]);
					if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = get_date_format($row["examDate"]);
				}
			}
		}

		//if Empty , Get Values from glucoma main
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){

			$elem_activate = "1";

			$sql = "SELECT
					glucomaId,activate,
					datePachy,
					pachyOdReads,pachyOdAvg,pachyOdCorr,
					pachyOsReads,pachyOsAvg,pachyOsCorr,activate
				  FROM glucoma_main
				  WHERE patientId = '".$this->pid."'
				  AND activate = '1' ";

			$row=sqlQuery($sql);
			if($row != false && !empty($row["datePachy"]) && $row["datePachy"] != "0000-00-00" && ($row["pachyOdReads"]!=""||$row["pachyOsReads"]!="")){
					$elem_pachy_od_readings = $row["pachyOdReads"];
					$elem_pachy_od_average = $row["pachyOdAvg"];
					$elem_pachy_od_correction_value = $row["pachyOdCorr"];
					$elem_pachy_os_readings = $row["pachyOsReads"];
					$elem_pachy_os_average = $row["pachyOsAvg"];
					$elem_pachy_os_correction_value = $row["pachyOsCorr"];
					$elem_cor_date =  get_date_format($row["datePachy"],'mm-dd-yyyy');
					$elem_activate = $row["activate"];
					$elem_glucomaId = $row["glucomaId"];
			}

			if($elem_activate!=-1){
				$arrInitialTop = $this->getIntialTop($this->pid);
				$lenInitialTop = count($arrInitialTop);

				if((empty($elem_pachy_od_readings) && empty($elem_pachy_od_correction_value) &&
					empty($elem_pachy_os_readings) && empty($elem_pachy_os_correction_value)
					) || empty($elem_glucomaId) || empty($arrInitialTop["Pachy"][0]["new"]))
				{

					if(isset($arrInitialTop["Pachy"][0])){
						$elem_cor_date = $arrInitialTop["Pachy"][0]["date"];
						$elem_pachy_od_readings = $arrInitialTop["Pachy"][0]["od"]["Read"];
						$elem_pachy_od_average = $arrInitialTop["Pachy"][0]["od"]["Avg"];
						$elem_pachy_od_correction_value = $arrInitialTop["Pachy"][0]["od"]["Corr"];
						$elem_pachy_os_readings = $arrInitialTop["Pachy"][0]["os"]["Read"];
						$elem_pachy_os_average = $arrInitialTop["Pachy"][0]["os"]["Avg"];
						$elem_pachy_os_correction_value = $arrInitialTop["Pachy"][0]["os"]["Corr"];
					}
				}
			}
		}

		//if Empty , Get Values from previous visit
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){

			$row = $this->valuesNewRecordsCorrectionValues($this->pid);
			if($row != false){
				$elem_pachy_od_readings = $row["reading_od"];
				$elem_pachy_od_average = $row["avg_od"];
				$elem_pachy_od_correction_value = $row["cor_val_od"];
				$elem_pachy_os_readings = $row["reading_os"];
				$elem_pachy_os_average = $row["avg_os"];
				$elem_pachy_os_correction_value = $row["cor_val_os"];
				if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = get_date_format($row["cor_date"]);
			}

		}

		//Pachy
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){

			$sql = "SELECT pachy_od_readings,pachy_od_average,pachy_od_correction_value,
					pachy_os_readings,pachy_os_average,pachy_os_correction_value,examDate
					FROM pachy
					WHERE patientId='".$this->pid."'
					AND (pachy_od_correction_value!='' OR pachy_os_correction_value!='')
					ORDER BY examDate DESC, pachy_id DESC
					LIMIT 0,1
					";
			$row=sqlQuery($sql);
			if($row!=false){
				$elem_pachy_od_readings=$row["pachy_od_readings"];
				$elem_pachy_od_average=$row["pachy_od_average"];
				$elem_pachy_od_correction_value=trim($row["pachy_od_correction_value"]);
				$elem_pachy_os_readings=$row["pachy_os_readings"];
				$elem_pachy_os_average=$row["pachy_os_average"];
				$elem_pachy_os_correction_value=trim($row["pachy_os_correction_value"]);
				if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = get_date_format($row["examDate"]);
			}
		}

		//A/Scan
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
			$sql = "SELECT pachymetryValOD,pachymetryCorrecOD,
					pachymetryValOS,pachymetryCorrecOS,examDate
					FROM surgical_tbl WHERE patient_id='".$this->pid."'
					AND (pachymetryCorrecOD!='' OR pachymetryCorrecOS!='')
					ORDER BY examDate DESC, surgical_id DESC
					LIMIT 0,1
					";
			$row=sqlQuery($sql);
			if($row!=false){
				$elem_pachy_od_readings=$row["pachymetryValOD"];
				$elem_pachy_od_average="";
				$elem_pachy_od_correction_value=trim($row["pachymetryCorrecOD"]);
				$elem_pachy_os_readings=$row["pachymetryValOS"];
				$elem_pachy_os_average="";
				$elem_pachy_os_correction_value=trim($row["pachymetryCorrecOS"]);
				if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = get_date_format($row["examDate"]);
			}
		}

		//pt past diag
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
			$sql = "SELECT pachy FROM chart_ptPastDiagnosis WHERE patient_id='".$this->pid."' ";
			$row=sqlQuery($sql);
			if($row!=false){
				$pachy = unserialize($row["pachy"]);

				$elem_pachy_od_readings=$pachy["od_readings"];
				$elem_pachy_od_average=$pachy["od_average"];
				$elem_pachy_od_correction_value=trim($pachy["od_correction_value"]);
				$elem_pachy_os_readings=$pachy["os_readings"];
				$elem_pachy_os_average=$pachy["os_average"];
				$elem_pachy_os_correction_value=trim($pachy["os_correction_value"]);
				if(!empty($pachy["cor_date"]) && ($pachy["cor_date"] != "0000-00-00") && $pachy["cor_date"] != "12-31-1969" && $pachy["cor_date"] != "12/31/1969") $elem_cor_date = $pachy["cor_date"];

			}
		}

		return array($elem_pachy_od_readings,$elem_pachy_od_average,$elem_pachy_od_correction_value,$elem_pachy_os_readings,$elem_pachy_os_average,$elem_pachy_os_correction_value,$elem_cor_date);
	}

	function valuesNewRecordsCorrectionValues($patient_id,$sel=" * ",$LF="0"){
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		$qry = "SELECT ".$sel." FROM chart_master_table ".
			  "INNER JOIN chart_correction_values ON chart_master_table.id = chart_correction_values.form_id ".
			  "WHERE chart_master_table.patient_id = '".$patient_id."' ".
			  "AND chart_master_table.record_validity = '1' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			  $LF.
			  "ORDER BY chart_master_table.create_dt DESC, chart_master_table.id DESC ".
			  "LIMIT 0,1 ";
		$row = sqlQuery($qry);
		return $row;
	}

	function isHtml5OK(){
		$ret=0;
		$strUserAgent = $_SERVER['HTTP_USER_AGENT'];
		if(stristr($strUserAgent, 'Safari') == true || stristr($strUserAgent, 'Chrome') == true ) {
			$ret=1;
		}elseif(stristr($strUserAgent, 'MSIE') == true){
			$pos = strpos($strUserAgent, 'MSIE');
			(int)substr($strUserAgent,$pos + 5, 3);
			if((int)substr($strUserAgent,$pos + 5, 3) > 8){
				$ret=1;
			}
		}else if(stristr($_SERVER['HTTP_USER_AGENT'],"rv:11") !== false){ //IE 11
			$ret=1;
		}
		return $ret;
	}

	function replace_spl_chr($val){
		$str_ret=str_replace('“','�',$val);
		$str_ret=str_replace('”', '�',$str_ret);
		$str_ret=str_replace('’', '�',$str_ret);
		$str_ret=str_replace('‘', '�',$str_ret);
		$str_ret=str_replace('—', '�',$str_ret);
		$str_ret=str_replace('–', '�',$str_ret);
		$str_ret=str_replace('•', '-',$str_ret);
		$str_ret=str_replace('…', '�',$str_ret);
		return $str_ret;
	}

	function arr_replace($srch,$rep,$arr){
		$len = count($arr);
		for($i=0;$i<$len;$i++){
			if(trim($arr[$i]) == $srch){
				$arr[$i] = $rep;
			}
		}
		return $arr;
	}

	function getGlaucomaPastReadings($patient_id){
		$sql = "SELECT
			  c1.*,
			  c2.purge_status,
			  SUBSTRING_INDEX(dateReading,'-',-1) AS strYear,
			  IF(dateReading REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}$',CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(dateReading,'-',2),'-',-1) AS SIGNED),0) AS strDate,
			  IF(dateReading REGEXP '^[0-9]{4}$',0,CAST(SUBSTRING_INDEX(dateReading,'-',1) AS SIGNED)) AS strMonth
			  FROM glaucoma_past_readings c1
			  LEFT JOIN chart_master_table c2 ON c2.id = c1.formId
			  WHERE patientId ='".$patient_id."'
			  AND (c2.delete_status='0' OR c2.delete_status IS NULL OR c2.delete_status='')  AND (c2.purge_status= '0' OR c2.purge_status IS NULL OR c2.purge_status = '')
			  ORDER BY strYear DESC, strMonth DESC, strDate DESC, time_read_mil DESC, c1.id DESC
			 ";

		return sqlStatement($sql);
	}

	function make_military_time($time){
		return date('Hi', strtotime($time));
	}
	/*
	function get_graph_data($request){
		$pId = $this->pid;
		$elem_opts=$request["elem_opts"];

		$series = array();
		$seriesName = array();
		$axisName = array("Date", "IOP");
		$graphTitle = " IOP Values ";

		$seriesColor = array();

		$arr_ta_od=$arr_ta_os=$arr_tp_od=$arr_tp_os=$arr_tx_od=$arr_tx_os=$arr_tt_od=$arr_tt_os=$arr_dates=array();

		$sql = "SELECT ".
				"c2.puff,c2.puff_od,c2.puff_os_1, ".
				"c2.applanation,c2.app_od,c2.app_os_1, ".
				"c2.tx,c2.tx_od,c2.tx_os,c2.fieldCount, ".
				"c2.multiple_pressure, ".
				"c2.iop_id, ".
				//"c3.date_of_service, ".
				"c1.date_of_service, ".
				"c1.create_dt,c1.update_date, c1.id ".
			   "FROM chart_master_table c1 ".
			   "LEFT JOIN chart_iop c2 ON c2.form_id=c1.id ".
			  // "LEFT JOIN chart_left_cc_history c3 ON c3.form_id=c1.id ".
			   "WHERE c1.patient_id='".$pId."' ".
			   //"ORDER BY IFNULL(c3.date_of_service,c1.create_dt), c1.id ";
			   "ORDER BY c1.date_of_service, c1.id ";

		$rez=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){

			if(empty($row["multiple_pressure"])){
				$arrMP["multiplePressuer"]["elem_applanation"] = $row["applanation"];
				$arrMP["multiplePressuer"]["elem_appOd"] = $row["app_od"];
				$arrMP["multiplePressuer"]["elem_appOs"] = $row["app_os_1"];

				$arrMP["multiplePressuer"]["elem_puff"] = $row["puff"];
				$arrMP["multiplePressuer"]["elem_puffOd"] = $row["puff_od"];
				$arrMP["multiplePressuer"]["elem_puffOs"] = $row["puff_os_1"];

				$arrMP["multiplePressuer"]["elem_tx"] = $row["tx"];
				$arrMP["multiplePressuer"]["elem_appTrgtOd"] = $row["tx_od"];
				$arrMP["multiplePressuer"]["elem_appTrgtOs"] = $row["tx_os"];
				$fieldCount="0";
			}else{
				$arrMP=unserialize($row["multiple_pressure"]);
				$fieldCount=$row["fieldCount"];
			}

			$ta_od=$ta_os=$tp_od=$tp_os=$tx_od=$tx_os=$tt_od=$tt_os=0;
			$dos=$row["date_of_service"];
			if(empty($dos))$dos=$row["create_dt"];
			if(empty($dos))$dos=$row["update_date"];

			//Loop values
			$arrFC = explode(",",$fieldCount);
			$lenFC = count($arrFC);

			for($cnt=0,$j=1;$j<=$lenFC;$j++,$cnt++){

				$indx=$indx2="";
				if($j>1){
					$indx = $arrFC[$cnt];
					$indx2=$j;
				}


				$v_Ta=$arrMP["multiplePressuer".$indx2]["elem_applanation".$indx];
				if(!empty($v_Ta)){
					$v_Od=$arrMP["multiplePressuer".$indx2]["elem_appOd".$indx];
					$v_Os=$arrMP["multiplePressuer".$indx2]["elem_appOs".$indx];

					if(!empty($v_Od)){
						$ta_od=wv_getNumber($v_Od);
					}

					if(!empty($v_Os)){
						$ta_os=wv_getNumber($v_Os);
					}
				}

				$v_Tp=$arrMP["multiplePressuer".$indx2]["elem_puff".$indx];
				if(!empty($v_Tp)){
					$v_Od=$arrMP["multiplePressuer".$indx2]["elem_puffOd".$indx];
					$v_Os=$arrMP["multiplePressuer".$indx2]["elem_puffOs".$indx];

					if(!empty($v_Od)){
						$tp_od=wv_getNumber($v_Od);
					}

					if(!empty($v_Os)){
						$tp_os=wv_getNumber($v_Os);
					}
				}

				$v_Tx=$arrMP["multiplePressuer".$indx2]["elem_tx".$indx];
				if(!empty($v_Tx)){
					$v_Od=$arrMP["multiplePressuer".$indx2]["elem_appTrgtOd".$indx];
					$v_Os=$arrMP["multiplePressuer".$indx2]["elem_appTrgtOs".$indx];

					if(!empty($v_Od)){
						$tx_od=wv_getNumber($v_Od);
					}

					if(!empty($v_Os)){
						$tx_os=wv_getNumber($v_Os);
					}
				}

				$v_Tt=$arrMP["multiplePressuer".$indx2]["elem_tt".$indx];
				if(!empty($v_Tt)){
					$v_Od=$arrMP["multiplePressuer".$indx2]["elem_tactTrgtOd".$indx];
					$v_Os=$arrMP["multiplePressuer".$indx2]["elem_tactTrgtOs".$indx];

					if(!empty($v_Od)){
						$tt_od=wv_getNumber($v_Od);
					}

					if(!empty($v_Os)){
						$tt_os=wv_getNumber($v_Os);
					}
				}

			}

			if(!empty($ta_od)||!empty($ta_os)||!empty($tp_od)||!empty($tp_os)||!empty($tx_od)||!empty($tx_os)||!empty($tt_od)||!empty($tt_os)){

				if(strpos($elem_opts,"TAOD")!==false || $elem_opts=="All"){
					$arr_ta_od[]=$ta_od;
				}
				if(strpos($elem_opts,"TAOS")!==false || $elem_opts=="All"){
					$arr_ta_os[]=$ta_os;
				}
				if(strpos($elem_opts,"TPOD")!==false || $elem_opts=="All"){
					$arr_tp_od[]=$tp_od;
				}
				if(strpos($elem_opts,"TPOS")!==false || $elem_opts=="All"){
					$arr_tp_os[]=$tp_os;
				}
				if(strpos($elem_opts,"TXOD")!==false || $elem_opts=="All"){
					$arr_tx_od[]=$tx_od;
				}
				if(strpos($elem_opts,"TXOS")!==false || $elem_opts=="All"){
					$arr_tx_os[]=$tx_os;
				}
				if(strpos($elem_opts,"TTOD")!==false || $elem_opts=="All"){
					$arr_tt_od[]=$tt_od;
				}
				if(strpos($elem_opts,"TTOS")!==false || $elem_opts=="All"){
					$arr_tt_os[]=$tt_os;
				}
				$arr_dates[] = get_date_format($dos);
			}
		}

		if(count($arr_ta_od)>0){
			$series[] = $arr_ta_od;
			$seriesName [] = "TA OD";
			$seriesColor [] = array(0,0,205);
			$ckd_taod="checked=\"checked\"";
		}

		if(count($arr_ta_os)>0){
			$series[] = $arr_ta_os;
			$seriesName [] = "TA OS";
			$seriesColor [] = array(34,139,34);
			$ckd_taos="checked=\"checked\"";
		}
		if(count($arr_tp_od)>0){
			$series[] = $arr_tp_od;
			$seriesName [] = "TP OD";
			$seriesColor [] = array(255,185,15);
			$ckd_tpod="checked=\"checked\"";
		}
		if(count($arr_tp_os)>0){
			$series[] = $arr_tp_os;
			$seriesName [] = "TP OS";
			$seriesColor [] = array(255,0,0);
			$ckd_tpos="checked=\"checked\"";
		}
		if(count($arr_tx_od)>0){
			$series[] = $arr_tx_od;
			$seriesName [] = "TX OD";
			$seriesColor [] = array(160,32,240);
			$ckd_txod="checked=\"checked\"";
		}
		if(count($arr_tx_os)>0){
			$series[] = $arr_tx_os;
			$seriesName [] = "TX OS";
			$seriesColor [] = array(30,144,255);
			$ckd_txos="checked=\"checked\"";
		}
		if(count($arr_tt_od)>0){
			$series[] = $arr_tt_od;
			$seriesName [] = "TT OD";
			$seriesColor [] = array(130,56,140);
			$ckd_ttod="checked=\"checked\"";
		}
		if(count($arr_tt_os)>0){
			$series[] = $arr_tt_os;
			$seriesName [] = "TT OS";
			$seriesColor [] = array(230,44,55);
			$ckd_ttos="checked=\"checked\"";
		}

		if(count($series)>0){
			$series[] = $arr_dates;	//Dates

			$len = count($series);
			$absLabel = "Serie".$len;

		}else{

			$msg='Graph can not created becuase of insufficient data.';
		}


		if( $len > 0 ){
			$line_chart_data = $this->line_chart($seriesName,$series);

			$line_pay_graph_var_arr_js=json_encode($line_chart_data['line_pay_graph_var_detail']);
			$line_payment_tot_arr_js=json_encode($line_chart_data['line_payment_tot_detail']);
		}


		$ajax_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr_js;
		$ajax_arr['line_payment_tot_detail']=$line_payment_tot_arr_js;
		return $ajax_arr;
	}

	function line_chart($graph_name,$graph_data){
		$key_i=0;$kk=0;

		foreach($graph_data[8] as $key=>$val){
			$line_payment_tot_arr[$key]["category"]=$val;
		}

		foreach($graph_data as $key=>$val){
			if($key!=8){
				$key_i++;
				$title="";
				$title=$graph_name[$key];
				$line_pay_graph_var_arr[]=array("alphaField"=> "C",
					"balloonText"=> "[[title]] of [[category]]: [[value]]",
					"bullet"=> "round",
					"bulletField"=> "C",
					"bulletSizeField"=> "C",
					"closeField"=> "C",
					"colorField"=> "C",
					"customBulletField"=> "C",
					"dashLengthField"=> "C",
					"descriptionField"=> "C",
					"errorField"=> "C",
					"fillColorsField"=> "C",
					"gapField"=> "C",
					"highField"=> "C",
					"id"=> "AmGraph-$key_i",
					"labelColorField"=> "C",
					"lineColorField"=> "C",
					"lowField"=> "C",
					"openField"=> "C",
					"patternField"=> "C",
					"title"=> $title,
					"valueField"=> "column-$key_i",
					"xField"=> "C",
					"yField"=> "C");

				foreach($graph_data[$key] as $key2=>$val2){
					if($graph_data[$key][$key2]>0){
						$line_payment_tot_arr[$key2]["column-".$key_i]=$graph_data[$key][$key2];
					}
					$kk++;
				}
			}
		}

		$return_arr['line_payment_tot_detail']=$line_payment_tot_arr;
		$return_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr;
		return $return_arr;
	}
	*/
	function remIopTrgtDefVal($trgtOd,$trgtOs,$pId,$formId=0){
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
				$sql = "UPDATE tbl_def_val SET iopTrgtOd='".$trgtOd."', iopTrgtOs='".$trgtOs."' WHERE ptId='".$pId."' AND form_id='".$formId."' ";
				$res = sqlQuery($sql);

				//Update zero
				if(!empty($formId)){
					$sql = "UPDATE tbl_def_val SET iopTrgtOd='".$trgtOd."', iopTrgtOs='".$trgtOs."' WHERE ptId='".$pId."' AND form_id='0' ";
					$res = sqlQuery($sql);
				}
			}
		}
	}

	function saveIopTrgt($trgtOd,$trgtOs,$pId,$formId=0){
		if( !empty($pId) ){
			$row = $this->getIopTrgtDef($pId,$formId,1);
			if($row != false){
				$sql = "UPDATE tbl_def_val SET iopTrgtOd='".$trgtOd."', iopTrgtOs='".$trgtOs."' WHERE ptId='".$pId."' AND form_id='".$formId."' ";
				$res = sqlQuery($sql);
			}else{
				if(!empty($trgtOd) || !empty($trgtOs)){
					$sql = "INSERT INTO tbl_def_val(tbl_def_val_id, iopTrgtOd, iopTrgtOs, ptId, form_id)  ".
						 "VALUES(NULL, '".$trgtOd."', '".$trgtOs."', '".$pId."', '".$formId."' ) ";
					$res = sqlQuery($sql);
				}
			}

			//Update zero
			if(!empty($formId)){
				$this->saveIopTrgt($trgtOd,$trgtOs,$pId,0);
			}
		}
	}

	function rem_form_in_glucoma($fid){
		$sql = "UPDATE glaucoma_past_readings SET formId='0' WHERE patientId='".$this->pid."' AND formId='".$fid."' ";
		$res = sqlQuery($sql);
	}

	function getTMax(){
		$ret = array();
		if(empty($this->pid)){
			return $ret;
		}
		//
		$tod="0"; $tos="0";
		$tod_dt=""; $tos_dt="";
		$sql = "SELECT c2.puff, c2.puff_od, c2.puff_os_1,
									c2.applanation, c2.app_od, c2.app_os_1,
									c2.tx, c2.tx_od, c2.tx_os,
									c2.multiple_pressure, c2.exam_date AS examDateIop
						FROM chart_master_table c1
						INNER JOIN	chart_iop c2 ON c1.id = c2.form_id
						WHERE c1.patient_id = '".$this->pid."'
						AND c1.delete_status='0' AND c1.purge_status='0' AND c2.purged='0'
						ORDER BY date_of_service , id
						";
		$res = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($res); $i++){
			$iop_dt = $row["examDateIop"];
			$ar_mul = !empty($row["multiple_pressure"]) ? unserialize($row["multiple_pressure"]) : array() ;
			if(is_array($ar_mul) && count($ar_mul)>0){

				foreach($ar_mul as $k => $mul){
					if(count($mul)>0){
						$firstKey = array_key_first($mul);

						//
						$getNum = preg_replace("/[A-Za-z_]/", "", $firstKey) ;
						if(!empty($getNum)){
							$getNum =$getNum;
						}else{
							$getNum ="";
						}

						if(!empty($mul["elem_applanation".$getNum])){
							if(!empty($mul["elem_appOd".$getNum]) && $mul["elem_appOd".$getNum] > $tod){
								$tod = $mul["elem_appOd".$getNum];
								$tod_dt = $iop_dt;
							}
							if(!empty($mul["elem_appOs".$getNum]) && $mul["elem_appOs".$getNum] > $tos){
								$tos = $mul["elem_appOs".$getNum];
								$tos_dt = $iop_dt;
							}
						}

						if(!empty($mul["elem_puff".$getNum])){
							if(!empty($mul["elem_puffOd".$getNum]) && $mul["elem_puffOd".$getNum] > $tod){
								$tod = $mul["elem_puffOd".$getNum];
								$tod_dt = $iop_dt;
							}
							if(!empty($mul["elem_puffOs".$getNum]) && $mul["elem_puffOs".$getNum] > $tos){
								$tos = $mul["elem_puffOs".$getNum];
								$tos_dt = $iop_dt;
							}
						}

						if(!empty($mul["elem_tx".$getNum])){
							if(!empty($mul["elem_appTrgtOd".$getNum]) && $mul["elem_appTrgtOd".$getNum] > $tod){
								$tod = $mul["elem_appTrgtOd".$getNum];
								$tod_dt = $iop_dt;
							}
							if(!empty($mul["elem_appTrgtOs".$getNum]) && $mul["elem_appTrgtOs".$getNum] > $tos){
								$tos = $mul["elem_appTrgtOs".$getNum];
								$tos_dt = $iop_dt;
							}
						}

						if(!empty($mul["elem_tt".$getNum])){
							if(!empty($mul["elem_tactTrgtOd".$getNum]) && $mul["elem_tactTrgtOd".$getNum] > $tod){
								$tod = $mul["elem_tactTrgtOd".$getNum];
								$tod_dt = $iop_dt;
							}
							if(!empty($mul["elem_tactTrgtOs".$getNum]) && $mul["elem_tactTrgtOs".$getNum] > $tos){
								$tos = $mul["elem_tactTrgtOs".$getNum];
								$tos_dt = $iop_dt;
							}
						}

					}
				}

			}else{

				if(!empty($row["puff"])){
					if(!empty($row["puff_od"]) && $row["puff_od"] > $tod){
						$tod = $row["puff_od"];
						$tod_dt = $iop_dt;
					}
					if(!empty($row["puff_os_1"]) && $row["puff_os_1"] > $tos){
						$tos = $row["puff_os_1"];
						$tos_dt = $iop_dt;
					}
				}

				if(!empty($row["applanation"])){
					if(!empty($row["app_od"]) && $row["app_od"] > $tod){
						$tod = $row["app_od"];
						$tod_dt = $iop_dt;
					}
					if(!empty($row["app_os_1"]) && $row["app_os_1"] > $tos){
						$tos = $row["app_os_1"];
						$tos_dt = $iop_dt;
					}
				}

				if(!empty($row["tx"])){
					if(!empty($row["tx_od"]) && $row["tx_od"] > $tod){
						$tod = $row["tx_od"];
						$tod_dt = $iop_dt;
					}
					if(!empty($row["tx_os"]) && $row["tx_os"] > $tos){
						$tos = $row["tx_os"];
						$tos_dt = $iop_dt;
					}
				}

			}
		}

		//
		$ret = array("od"=>$tod, "od_tm"=>$tod_dt, "os"=>$tos, "os_tm"=>$tos_dt);
		return $ret;
	}

	function get_gonio_info(){
			$id = trim($_GET["id"]);
			$drawing_image_path_web="";
			$is_editable = 0;
			$form_id = 0;
			if(!empty($id)){
				$sql = "SELECT c1.form_id, c2.patient_id, c1.statusElem, c1.idoc_drawing_id, c2.finalize FROM chart_gonio c1 ".
								"INNER JOIN chart_master_table c2 ON c1.form_id = c2.id ".
							 	"where c1.gonio_id = '".$id."' ";
				$row = sqlQuery($sql);
				if($row!=false){
					$form_id = $row["form_id"];
					$pt_id = $row["patient_id"];
					$statusElem = $row["statusElem"];
					$idoc_drawing_id = $row["idoc_drawing_id"];
					$is_editable = !empty($row["finalize"]) ? 0 : 1 ;
					if(!empty($idoc_drawing_id) && (strpos($statusElem, "3_Os=1")!==false||strpos($statusElem, "3_Od=1")!==false)){
							$sql = "SELECT drawing_image_path FROM ".constant("IMEDIC_SCAN_DB").".idoc_drawing
											WHERE id IN (".$idoc_drawing_id.") AND drawing_for='IOP_GONIO' AND deletedby='0' ";
							$row = sqlQuery($sql);
							if($row!=false){
								$drawing_image_path = $row["drawing_image_path"];
								if(!empty($drawing_image_path)){
										$drawing_image_path=str_replace(".png", "_b.png", $drawing_image_path);
										$oSaveFile = new SaveFile($pt_id);
										$drawing_image_path_inc = $oSaveFile->getFilePath($drawing_image_path, "i");
										if(file_exists($drawing_image_path_inc)){
											$drawing_image_path_web = $oSaveFile->getFilePath($drawing_image_path, "w");
										}
								}
							}
					}
				}
			}

			//check form //
			if(!empty($is_editable) && (!isset($_SESSION["form_id"]) || $_SESSION["form_id"]!=$form_id)){
				$is_editable=0;
			}

			echo json_encode(array("file_path"=>$drawing_image_path_web, "is_edit"=>$is_editable));
	}
}
?>
