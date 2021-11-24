<?php

class ChartOOD{
	public $pid, $fid;
	public function __construct($pid, $fid){		
		$this->pid = $pid;
		$this->fid = $fid;
	}
	
	function valNewRecordOod($sel=" * ",$LF="0",$dt=""){
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
			   "INNER JOIN chart_ood ON chart_master_table.id = chart_ood.form_id AND chart_ood.purged='0'   ".
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
		
		// chart_OOD
		$elem_editModeOOD=0;
		$elem_ci_OOD=0;
		$sql="SELECT * FROM chart_ood WHERE form_id='".$elem_formId."' AND patient_id = '".$patient_id."' AND purged = '0' ";
		$row = sqlQuery($sql);
		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			$row = false;
			$elem_ci_OOD=1;
		}
		
		if($row != false){
			$elemOOD = $row["ood"];
			$elem_chng_OOD = $row["statusElem"];
			$elem_editModeOOD=1;
		}

		if(!empty($elemOOD)){
			$arrOOD = unserialize($elemOOD);
		}
		
		$ar=array();
		$ar["elem_ci_OOD"]=$elem_ci_OOD;
		$ar["elemOOD"]=$elemOOD;
		$ar["elem_chng_OOD"]=$elem_chng_OOD;
		$ar["elem_editModeOOD"]=$elem_editModeOOD;
		$ar["arrOOD"]=$arrOOD;
		
		return $ar;
	}
}

?>
