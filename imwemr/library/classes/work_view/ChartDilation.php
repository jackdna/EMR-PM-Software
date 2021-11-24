<?php

class ChartDilation{
	public $pid, $fid;
	public function __construct($pid, $fid){		
		$this->pid = $pid;
		$this->fid = $fid;
	}
	
	function valNewRecordDialation($sel=" * ",$LF="0" ,$dt=""){
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		
		if(!empty($dt)&&$dt!="0000-00-00"){ 
			$tmp="";
			if(!empty($this->fid)){				
				$dt=" AND (chart_master_table.date_of_service <=  '".$dt."' AND chart_master_table.id < '".$this->fid."' ) ";
			}else{
				$dt = " AND (chart_master_table.date_of_service <  '".$dt."' ) "; 
			}
		}else{
			$dt ="";
		}
		
		global $cryfwd_form_id;
		//DOS BASED carry forward
		if(!empty($cryfwd_form_id) ){
			$dt = " AND (chart_master_table.id <=  '".$cryfwd_form_id."' ) ";
		}
		
		$sql = "SELECT ".$sel." FROM chart_master_table ".
			   "INNER JOIN chart_dialation ON chart_master_table.id = chart_dialation.form_id AND chart_dialation.purged='0'   ".
			   "WHERE chart_master_table.patient_id = '".$this->pid."' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			   "AND chart_master_table.record_validity = '1' ".
			   $LF.$dt.
			   "ORDER BY update_date DESC, chart_master_table.id DESC LIMIT 0,1 ";
		$res = sqlStatement($sql);
		return $res;
	}
	function getFormInfo(){
		$elem_formId = $this->fid;
		$patient_id = $this->pid;
		$elem_ci_dilation=0;
		//Dialation
		$sql = "SELECT * FROM chart_dialation WHERE form_Id = '".$elem_formId."' AND patient_id='".$patient_id."' AND purged = '0' ";
		$res = imw_query($sql);
		
		//Previous
		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			$res =null;
			$elem_ci_dilation=1;
		}
		
		$elem_editModeDilate=0;
		$arrPermissionBy=array();
		$permissionby_other="";
		if(imw_num_rows($res)>0){
			extract(sqlFetchArray($res));
			$elem_editModeDilate=1;
			$elem_revEyes = $rev_eyes;
			$elem_noDilation=$noDilation;
			$elem_dilation = $dilation;
			$elem_permiBy= $permiBy;
			$elem_chng_dilation = $statusElem;
			if($elem_editModeDilate==1){$elem_utElems .= $ut_elem;}	
		}
		
		if(!empty($elem_dilation)){
			$arrDilation=unserialize($elem_dilation);
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
		}

		if(!empty($elem_permiBy)){
			$tmp = explode("~!!~",$elem_permiBy);	
			if(!empty($tmp[1])) $permissionby_other=$tmp[1];
			if(!empty($tmp[0])) $arrPermissionBy = explode(",",$tmp[0]);

		}
		
		$arr = array();
		$arr["elem_ci_dilation"] = $elem_ci_dilation;
		$arr["elem_editModeDilate"] = $elem_editModeDilate;
		$arr["arrPermissionBy"] = $arrPermissionBy;
		$arr["permissionby_other"] = $permissionby_other;
		$arr["elem_revEyes"] = $elem_revEyes;
		
		$arr["elem_noDilation"] = $elem_noDilation;
		$arr["elem_dilation"] = $elem_dilation;
		$arr["elem_permiBy"] = $elem_permiBy;
		$arr["elem_chng_dilation"] = $elem_chng_dilation;
		$arr["elem_utElems_dilation"] = $elem_utElems;
		
		$arr["arrDilation"] = $arrDilation;
		
		$arr["sideIop"] = $sideIop;
		$arr["eyeSide"] = $eyeSide;
		$arr["dilated_mm"] = $dilated_mm;
		$arr["warned_n_advised"] = $warned_n_advised;
		
		$arr["patient_not_driving"] = $patient_not_driving;
		$arr["patientAllergic"] = $patientAllergic;
		$arr["allergicComments"] = $allergicComments;
		$arr["unableDilation"] = $unableDilation;
		$arr["unableDilateComments"] = $unableDilateComments;
		
		return $arr;
	}
	
	function setDialationMsg(){
		$patientId = $this->pid;
		$msg = "A/P Dilated";
		$sql = "UPDATE patient_location ".
			 "SET doctor_mess = '".$msg."', ". //CONCAT('".$msg."',TIME_FORMAT(CURTIME(), '%l:%i %p')),
			 "ready4DrId='0', ".
			 "cur_time = '".date('Y-m-d H:i:s')."',".
			 "pt_with = '0' ".
			 "WHERE patientId='".$patientId."' AND cur_date='".date('Y-m-d')."'";
		$row = sqlQuery($sql);
	}
}

?>