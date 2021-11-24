<?php
class VisLasik extends ChartNote{
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_vis_lasik";
		$this->examName="VisionLasik";
	}
	
	function getLastRecord($sel=" * ",$LF="0",$dt="", $a="", $b="", $c=""  ){
		return parent::getLastRecord($this->tbl,"form_id",$LF,$sel,$dt);
	}
	
	function getLasikSection(){
		global $elem_statusElements, $arrTempProc, $dos_ymd, $ctmpLasik;
		$ret = array();
		$todate = date('m-d-Y');
		
		//variables
		$patient_id = $this->pid;
		$form_id = $this->fid;
		//check empty
		if(empty($patient_id) && empty($form_id)){ return "" ; }
		
		
		//Get Form id based on patient id
		$sql = "SELECT * FROM chart_vis_lasik WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ";
		$row = sqlQuery($sql);
		if(($row == false)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
			// New
			$elem_editModeVis = "0";
			$vision_edid = "";
			//New Records
			//$row = valuesNewRecordsVision($patient_id);
			$res = $this->getLastRecord(" * ",0,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
			if($res!=false){$row=$res;}else{$row=false;}
			$isNewRecord = true;
		}else{
			// Update
			$elem_editModeVis = "1";
			$elem_lasikId = $row["id"];
			//Default
			if(isset($_POST["defaultValsVis"]) && ($_POST["defaultValsVis"] == 1)){
				//New Records
				//$row = valuesNewRecordsVision($patient_id);
				$res = $this->getLastRecord(" * ",1,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
				if($res!=false){$row=$res;}else{$row=false;}
				$isNewRecord = true;
			}
		}
		
		if($row != false){			
			$ret["el_lasik_trgt_method"] =  $row["method"];
			$ret["el_visLasikTrgtDate"] = wv_formatDate($row["date_lasik"]);
			$ret["el_visLasikTrgtTime"] = $row["time_lasik"];		
			$ret["el_lasik_trgt_intervention"] = $row["intervention"];
			$ret["el_visLasikTrgtMicKera"] = $row["microkeratome"];
			$ret["el_lasik_trgt_Excimer"] = $row["laser_excimer"];
			$ret["el_lasik_trgt_mode"] = $row["laser_mode"];
			$ret["el_lasik_trgt_opti_zone"] = $row["laser_optical_zone"];			
			$el_vis_target = $row["target"];
			$el_vis_laser = $row["laser"];
		}
		
		if(!empty($el_vis_target)){
			$ar_vis_target = json_decode($el_vis_target, true);
			$ret["el_visLasikTrgtOdS"] = $ar_vis_target["Od"]["S"]; 
			$ret["el_visLasikTrgtOdC"] = $ar_vis_target["Od"]["C"]; 
			$ret["el_visLasikTrgtOdA"] = $ar_vis_target["Od"]["A"];			
			$ret["el_visLasikTrgtOsS"] = $ar_vis_target["Os"]["S"]; 
			$ret["el_visLasikTrgtOsC"] = $ar_vis_target["Os"]["C"]; 
			$ret["el_visLasikTrgtOsA"] = $ar_vis_target["Os"]["A"];
			$ret["el_visLasikTrgtDesc"] = $ar_vis_target["Desc"];
		}
		
		if(!empty($el_vis_laser)){
			$ar_vis_laser = json_decode($el_vis_laser, true);
			$ret["el_visLasikLsrOdS"] = $ar_vis_laser["Od"]["S"]; 
			$ret["el_visLasikLsrOdC"] = $ar_vis_laser["Od"]["C"]; 
			$ret["el_visLasikLsrOdA"] = $ar_vis_laser["Od"]["A"];			
			$ret["el_visLasikLsrOsS"] = $ar_vis_laser["Os"]["S"]; 
			$ret["el_visLasikLsrOsC"] = $ar_vis_laser["Os"]["C"]; 
			$ret["el_visLasikLsrOsA"] = $ar_vis_laser["Os"]["A"];
			$ret["el_visLasikLsrDesc"] = $ar_vis_laser["Desc"];
		}
		
		if(empty($ret["el_visLasikTrgtDate"]) || strpos($ret["el_visLasikTrgtDate"],"0000")!=false){ $ret["el_visLasikTrgtDate"]=""; }
		
		
		return $ret;
		
	}
	
	static function getArrDropDown(){
		$arr = array();
		
		//
		$sql = "SELECT op_name, op_type FROM chart_vis_lasik_options WHERE del_by='0' ORDER BY op_name ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$op_nm = $row["op_name"];			
			if($row["op_type"] == "Laser Mode"){
				$arr["Mode"][] = array($op_nm, $arrEmpty,$op_nm);
			}else if($row["op_type"] == "Laser Excimer"){
				$arr["Excimer"][] = array($op_nm, $arrEmpty,$op_nm);
			}
		}
		
		$arr["Mode"][] = array("Other", $arrEmpty,"");
		$arr["Excimer"][] = array("Other", $arrEmpty,"");
		
		return $arr;
	}
	
	function get_menu_html(){
		$ar = array();
		$wh = $_GET["wh"];
		$mid = $_GET["mid"];
		$eid = $_GET["eid"];
		$arr_dd_menu = VisLasik::getArrDropDown();
		if($wh == "menu_Lasik_trgt_Excimer"){
			$ar = $arr_dd_menu["Excimer"];	
		}else if($wh == "menu_Lasik_trgt_mode"){
			$ar = $arr_dd_menu["Mode"];
		}
		
		$s = wv_get_simple_menu($ar,$mid,$eid,2);
		echo $s;
	
	}
	
}

?>