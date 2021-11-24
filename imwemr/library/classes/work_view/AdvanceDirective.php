<?php
class AdvanceDirective extends Patient{	
	public function __construct($pid){		
		parent::__construct($pid);	
	}
	
	function getADInfo($op=""){
		
		$qry12 = "select ado_option, desc_ado_other_txt from patient_data where id = '".$this->pid."' ";
		$row = sqlQuery($qry12);		
		if($row!=false && !empty($row['ado_option'])){
			
			$ado_display = $row['ado_option'];
			if($ado_display=="Other"){	$ado_display = $row['desc_ado_other_txt']; }						
			
			if(empty($op)){	
			if($ado_display=="" || $ado_display=="NULL" || $ado_display=="No"){ 	$ado_display = "N/A"; 	}
			$ado_display_comp=$ado_display;		
			if($ado_display=="Power of Attorney") $ado_display="POA";
			
			if(strlen($ado_display) >= 6){
				$ado_display = substr($ado_display,0,5);
				$ado_display = $ado_display."..";
			}
			$ado_display = str_replace(' ', '&nbsp;', $ado_display);			
			
			$ret = "";	
			$ret = "<label onclick=\"show_ad_div();\" data-toggle=\"tooltip\" title=\"Advance Directive\"
						id=\"advanceDirectiveLink\" class=\"clickable\" >AD - ".$ado_display."</label>";
			}else if($op == 1){
				$scan_sql = "select scan_id from ".constant("IMEDIC_SCAN_DB").".scans 
							where patient_id = '".$this->pid."' and form_id='0' 
							and image_form = 'ptInfoMedHxGeneralHealth'";
				$query = imw_query($scan_sql);
				$arr = imw_fetch_array($query);
				$scan_id = $arr["scan_id"];			
			
				$ret = array($ado_display, $scan_id);
			}
		}

		return $ret;
	}
	
	function get_adv_directive(){
		list($ado_display, $scan_id) =$this->getADInfo(1);
		
		$tmp_name = "ado_option";
		$tmp_value = $ado_display;
		$menu = wv_get_simple_menu(array("0"=>array("No","","No"), "1"=>array("Living Will","","Living Will"), "2"=>array("Power of Attorney","","Power of Attorney")),"menu_advdirective","ado_option");		
		$hide = (!empty($scan_id)) ? "" : "hidden";
		
		$htm = "
			<!-- Modal -->
			<div id=\"advancedDirectiveModal\" class=\"modal fade\" role=\"dialog\">
			  <div class=\"modal-dialog\">

			    <!-- Modal content-->
			    <div class=\"modal-content\">
			      <div class=\"modal-header\">
				<button type=\"button\" class=\"close\" onclick=\"top.fmain.show_ad_div(1);\" >&times;</button>
				<h4 class=\"modal-title\">Advanced Directive</h4>
			      </div>
			      <div class=\"modal-body\">
					<!-- Inner -->
					<div class=\"input-group\">
						<div class=\"input-group-addon\"><label for=\"".$tmp_name."\">Directive</label></div>
						<input type=\"text\" name=\"".$tmp_name."\" id=\"".$tmp_name."\" value=\"".$tmp_value."\" class=\"form-control\" >
						".$menu."
					</div>
					<!-- Inner -->
			      </div>
			      <div class=\"modal-footer\">
				<button type=\"button\" class=\"btn btn-success\" onclick=\"top.fmain.show_ad_div(2);\">Done</button>
				<button type=\"button\" class=\"btn btn-success\" onclick=\"top.fmain.ado_scan_fun('scan', 'ptInfoMedHxGeneralHealth','".$scan_id."');\">Scan</button>
				<button type=\"button\" class=\"btn btn-success ".$hide."\" onclick=\"top.fmain.ado_scan_fun('preview', 'ptInfoMedHxGeneralHealth','".$scan_id."');\">Preview</button>
				<button type=\"button\" class=\"btn btn-danger\" onclick=\"top.fmain.show_ad_div(1);\">Close</button>
			      </div>
			    </div>

			  </div>
			</div>
		
		";
	
		echo $htm;
	}
	
	function  save(){
		$a = trim($_POST["ado_option"]);
		$b = "";
		if($a!="No" && $a!="Living Will" && $a!="Power of Attorney"){
			$b=$a; $a="Other";
		}		

		if(!empty($a) || !empty($b)) {
			$updquery="update patient_data SET ado_option = '".sqlEscStr($a)."', desc_ado_other_txt = '".sqlEscStr($b)."' where pid ='".$this->pid."'";
			imw_query($updquery);
		}
		
		echo $this->getADInfo();
	} 
	
	
	
	
}
?>