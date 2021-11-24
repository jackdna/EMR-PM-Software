<?php

class ChartCorrectionValues{
	public $pid, $fid;
	public function __construct($pid, $fid){		
		$this->pid = $pid;
		$this->fid = $fid;
	}
	
	function getFormInfo(){
		$elem_formId=$this->fid;
		$patient_id=$this->pid;
		$sql = "SELECT * FROM chart_correction_values WHERE form_id='".$elem_formId."' AND patient_id='".$patient_id."' ";
		$row = sqlQuery($sql);
		if($row != false){
			$elem_od_readings = $row["reading_od"];
			$elem_od_average = $row["avg_od"];
			$elem_od_correction_value = $row["cor_val_od"];
			$elem_os_readings = $row["reading_os"];
			$elem_os_average = $row["avg_os"];
			$elem_os_correction_value = $row["cor_val_os"];
			if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = wv_formatDate($row["cor_date"]);			
		}else{
			$arr = $this->setDefPachyVals();
			$elem_od_readings = $arr[0];
			$elem_od_average = $arr[1];
			$elem_od_correction_value = $arr[2];
			$elem_os_readings = $arr[3];
			$elem_os_average = $arr[4];
			$elem_os_correction_value = $arr[5];
			$elem_cor_date = $arr[6];
		}
		
		//bug fix here for date issue
		if($elem_cor_date=="12-31-1969" || $elem_cor_date=="12/31/1969"){
			$elem_cor_date = "";
		}
		
		$arr=array();
		$arr["elem_od_readings"]=$elem_od_readings;
		$arr["elem_od_average"]=$elem_od_average;
		$arr["elem_od_correction_value"]=$elem_od_correction_value;
		$arr["elem_os_readings"]=$elem_os_readings;
		$arr["elem_os_average"]=$elem_os_average;
		$arr["elem_os_correction_value"]=$elem_os_correction_value;
		$arr["elem_cor_date"]=$elem_cor_date;
		return $arr; 
	}
	
	function valuesNewRecordsCorrectionValues($sel=" * ",$LF="0")
	{
		$patient_id=$this->pid;
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
	
	function setDefPachyVals(){
		$formId=$this->fid;
		$pid =$this->pid;
		
		$elem_pachy_od_readings=$elem_pachy_od_average=$elem_pachy_od_correction_value="";
		$elem_pachy_os_readings=$elem_pachy_os_average=$elem_pachy_os_correction_value="";

		if(!empty($formId)){
			//
			$sql="SELECT reading_od,avg_od,cor_val_od,reading_os,avg_os,cor_val_os,cor_date
				  FROM chart_correction_values WHERE patient_id='".$pid."' AND form_id='".$formId."'  ";
			$row=sqlQuery($sql);
			if($row!=false){
				$elem_pachy_od_readings=$row["reading_od"];
				$elem_pachy_od_average=$row["avg_od"];
				$elem_pachy_od_correction_value=trim($row["cor_val_od"]);
				$elem_pachy_os_readings=$row["reading_os"];
				$elem_pachy_os_average=$row["avg_os"];
				$elem_pachy_os_correction_value=trim($row["cor_val_os"]);
				if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = wv_formatDate($row["cor_date"]); 
			}		

			//Pachy
			if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){			
				$sql = "SELECT pachy_od_readings,pachy_od_average,pachy_od_correction_value,
						pachy_os_readings,pachy_os_average,pachy_os_correction_value,examDate
						FROM pachy WHERE formId='".$formId."' AND patientId='".$pid."' ";
				$row=sqlQuery($sql);
				if($row!=false){
					$elem_pachy_od_readings=$row["pachy_od_readings"];
					$elem_pachy_od_average=$row["pachy_od_average"];
					$elem_pachy_od_correction_value=trim($row["pachy_od_correction_value"]);

					$elem_pachy_os_readings=$row["pachy_os_readings"];
					$elem_pachy_os_average=$row["pachy_os_average"];
					$elem_pachy_os_correction_value=trim($row["pachy_os_correction_value"]);
					if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = wv_formatDate($row["examDate"]); 
				}
				//
			}

			//A/scan
			if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
				$sql = "SELECT pachymetryValOD,pachymetryCorrecOD,
						pachymetryValOS,pachymetryCorrecOS,examDate
						FROM surgical_tbl WHERE form_id='".$formId."' AND patient_id='".$pid."' ";
				$row=sqlQuery($sql);
				if($row!=false){
					$elem_pachy_od_readings=$row["pachymetryValOD"];
					//$elem_pachy_od_average=$row["pachy_od_average"];
					$elem_pachy_od_correction_value=trim($row["pachymetryCorrecOD"]);

					$elem_pachy_os_readings=$row["pachymetryValOS"];
					//$elem_pachy_os_average=$row["pachy_os_average"];
					$elem_pachy_os_correction_value=trim($row["pachymetryCorrecOS"]);
					if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = wv_formatDate($row["examDate"]); 
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
				  WHERE patientId = '".$pid."' 
				  AND activate = '1' ";
			
			$row=sqlQuery($sql);
			if($row != false && !empty($row["datePachy"]) && $row["datePachy"] != "0000-00-00" && ($row["pachyOdReads"]!=""||$row["pachyOsReads"]!="")){
					$elem_pachy_od_readings = $row["pachyOdReads"];
					$elem_pachy_od_average = $row["pachyOdAvg"];
					$elem_pachy_od_correction_value = $row["pachyOdCorr"];
					$elem_pachy_os_readings = $row["pachyOsReads"];
					$elem_pachy_os_average = $row["pachyOsAvg"];
					$elem_pachy_os_correction_value = $row["pachyOsCorr"];
					$elem_cor_date =  wv_formatDate($row["datePachy"],'mm-dd-yyyy');
					$elem_activate = $row["activate"];
					$elem_glucomaId = $row["glucomaId"];
			}	
			
			if($elem_activate!=-1){
				$oChartGlucoma = new ChartGlucoma($pid);
				$arrInitialTop = $oChartGlucoma->getIntialTop(1);
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
			
			$row=$this->valuesNewRecordsCorrectionValues();
			if($row != false){
				$elem_pachy_od_readings = $row["reading_od"];
				$elem_pachy_od_average = $row["avg_od"];
				$elem_pachy_od_correction_value = $row["cor_val_od"];
				$elem_pachy_os_readings = $row["reading_os"];
				$elem_pachy_os_average = $row["avg_os"];
				$elem_pachy_os_correction_value = $row["cor_val_os"];
				if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = wv_formatDate($row["cor_date"]);
			}

		}
		
		//Pachy
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
			
			$sql = "SELECT pachy_od_readings,pachy_od_average,pachy_od_correction_value,
					pachy_os_readings,pachy_os_average,pachy_os_correction_value,examDate
					FROM pachy 
					WHERE patientId='".$pid."' 
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
				if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = wv_formatDate($row["examDate"]);
			}
		}

		//A/Scan
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
			$sql = "SELECT pachymetryValOD,pachymetryCorrecOD,
					pachymetryValOS,pachymetryCorrecOS,examDate
					FROM surgical_tbl WHERE patient_id='".$pid."' 
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
				if(!empty($row["examDate"]) && ($row["examDate"] != "0000-00-00")) $elem_cor_date = wv_formatDate($row["examDate"]);
			}
		}
		
		//pt past diag
		if($elem_pachy_od_correction_value==""&&$elem_pachy_os_correction_value==""){
			$sql = "SELECT pachy FROM chart_ptPastDiagnosis WHERE patient_id='".$pid."' ";
			$row=sqlQuery($sql);
			if($row!=false){
				$pachy = unserialize($row["pachy"]);
				
				$elem_pachy_od_readings=$pachy["od_readings"];
				$elem_pachy_od_average=$pachy["od_average"];
				$elem_pachy_od_correction_value=trim($pachy["od_correction_value"]);
				$elem_pachy_os_readings=$pachy["os_readings"];
				$elem_pachy_os_average=$pachy["os_average"];
				$elem_pachy_os_correction_value=trim($pachy["os_correction_value"]);
				if(!empty($pachy["cor_date"]) && ($pachy["cor_date"] != "0000-00-00")) $elem_cor_date = wv_formatDate($pachy["cor_date"]);
				
			}
		}		

		return array($elem_pachy_od_readings,$elem_pachy_od_average,$elem_pachy_od_correction_value,
					 $elem_pachy_os_readings,$elem_pachy_os_average,$elem_pachy_os_correction_value,$elem_cor_date);
	}
	//
	function saveCorrectionValues($arr){
		
		//Check
		$sql = "SELECT cor_id FROM chart_correction_values WHERE form_id = '".$arr["elem_formId"]."' AND patient_id='".$arr["patientid"]."' ";
		$row = sqlQuery($sql);
		if($row != false){
			//Update
			$sql = "UPDATE chart_correction_values SET ".
				 "reading_od = '".$arr["elem_od_readings"]."', ".
				 "avg_od = '".$arr["elem_od_average"]."', ".
				 "cor_val_od = '".$arr["elem_od_correction_value"]."', ".
				 "reading_os = '".$arr["elem_os_readings"]."', ".
				 "avg_os='".$arr["elem_os_average"]."', ".
				 "cor_val_os = '".$arr["elem_os_correction_value"]."', ".
				 "cor_date = '".wv_formatDate($arr["elem_cor_date"],0,0,"insert")."', ".
				 "uid='".$_SESSION["authId"]."' ".
				 "WHERE form_id = '".$arr["elem_formId"]."' AND patient_id='".$arr["patientid"]."' ";
			$row = sqlQuery($sql);
		}else{
			if(!empty($arr["elem_od_readings"]) || !empty($arr["elem_od_correction_value"]) ||
				!empty($arr["elem_os_readings"]) || !empty($arr["elem_os_correction_value"]) ){
				//Insert
				$sql= "INSERT INTO chart_correction_values ".
					"(cor_id, patient_id, form_id, cor_date, reading_od, avg_od, cor_val_od, reading_os, avg_os, cor_val_os,uid) ".
					"VALUES ".
					"(NULL, '".$arr["patientid"]."', '".$arr["elem_formId"]."', '".wv_formatDate($arr["elem_cor_date"],0,0,"insert")."', '".$arr["elem_od_readings"]."', '".$arr["elem_od_average"]."', ".
					"'".$arr["elem_od_correction_value"]."', '".$arr["elem_os_readings"]."', '".$arr["elem_os_average"]."', '".$arr["elem_os_correction_value"]."','".$_SESSION["authId"]."' ) ";
				$row = sqlQuery($sql);
			}
		}
	}	
}
?>