<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: Admn.php
Coded in PHP7
Purpose: This class file provides functions to get Admin side values
Access Type : Include file
*/
?>
<?php
//Admn
class Admn{

	function getPracticeBillCode($flgSbWarn=0)
	{
		$sql = "SELECT elem_billingCode,visitcode_warn, del_proc_noti FROM copay_policies WHERE policies_id = '1' ";
		$row = sqlQuery($sql);
		if($row != false)
		{
			$billCode = (!empty($row["elem_billingCode"])) ? $row["elem_billingCode"] : false;
			$visitcode_warn=$row["visitcode_warn"];
			$del_proc_noti=$row["del_proc_noti"];
		}
		if($billCode != false)
		{
			switch($billCode)
			{
				case "Eye Code":
					$billCode = "920";
				break;
				case "E/M Code":
					$billCode = "992";
				break;
			}
		}

		if($billCode == false) $billCode= "" ;
		if(empty($visitcode_warn)){$visitcode_warn=0;}
		if(empty($del_proc_noti)){$del_proc_noti=0;}

		if($flgSbWarn==1){
			return array($billCode, $visitcode_warn, $del_proc_noti);
		}else{
			return $billCode;
		}
	}

///Get Phyisician Setting from admin about Refrection
function cp_getRefSetting(){
	$ret = array();
	$ret[0] = false;
	$ret[1] = false;

	$sql = "SELECT refraction, refractionGiveOnly FROM copay_policies WHERE policies_id = '1' ";
	$row = sqlQuery($sql);
	if($row != false){
		$ret[0] = ($row["refraction"] == "Yes") ? true : false;
		$ret[1] = ($row["refractionGiveOnly"] == "Yes") ? true : false;
	}
	return $ret;
}


//get Visit types
function wv_getPtVisit($id=0,$flgOthr=""){
	$arr=array();
	$where = (!empty($id)) ? "WHERE tech_id='".$id."' " : "";
	$sql= "SELECT DISTINCT ptVisit, tech_id FROM tech_tbl ".$where." ORDER BY ptVisit";
	$rez = sqlStatement($sql);
	for( $i=1;$row=sqlFetchArray($rez);$i++ ){
		if(!empty($row["ptVisit"]) && !empty($row["tech_id"])){
			$arr[$row["tech_id"]] = $row["ptVisit"];
		}
	}
	if(!empty($flgOthr) && !in_array("Other",$arr)){ $arr[0] = "Other"; }
	return $arr;
}

//get Testing types
function wv_getPtTesting($id=0,$flgOthr=""){
	$arr=array();
	$where = (!empty($id)) ? " AND id='".$id."' " : "";
	$sql= "SELECT DISTINCT testing_nm, id FROM testing WHERE del_by='0' ".$where." ORDER BY testing_nm";
	$rez = sqlStatement($sql);
	for( $i=1;$row=sqlFetchArray($rez);$i++ ){
		if(!empty($row["testing_nm"]) && !empty($row["id"])){
			$arr[$row["id"]] = $row["testing_nm"];
		}
	}
	if(!empty($flgOthr) && !in_array("Other",$arr)){ $arr[0] = "Other"; }
	return $arr;
}

// get AP Policy Settings
function getAPPolicySettings(){
	$str="";

	$sql = "SELECT ap_policies FROM chart_admin_settings WHERE id = '1' ";
	$row=sqlQuery($sql);
	if($row!=false){

		$str=$row["ap_policies"];
	}

	return $str;
}

function getFuOptions($id=0,$flgArrOp=0,$srch=""){

	$sql ="SELECT * FROM chart_fu_options ";
	$sql .= (!empty($id)) ? "WHERE fu_id = '".$id."' " : "";
	$sql .= (!empty($srch)&&empty($id)) ? "WHERE optName LIKE '".$srch."%' " : "";
	$sql .="ORDER BY optName ";
	$rez = sqlStatement($sql);

	if($flgArrOp == 1){
		$arr = array();

		for( $i=1;$row=sqlFetchArray($rez);$i++ ){
			$arr[] = stripslashes($row["optName"]);
		}
		return $arr;

	}else{
		return $rez;
	}
}

function insertFuOption($optName){
	$inId = 0;
	if(!empty($optName)){
		$optName = sqlEscStr($optName);
		$sql = "SELECT * FROM chart_fu_options WHERE LCASE(optName) = '".strtolower($optName)."' ";
		$row = sqlQuery($sql);
		if($row == false){
			//Insert true
			$sql = "INSERT INTO chart_fu_options (fu_id, optName) VALUES (NULL, '".$optName."') ";
			$inId = sqlInsert($sql);
		}else{
			//Insert Error
		}
	}
	return $inId;
}

function getSchOptions($id=0){
	$sql ="SELECT * FROM chart_schedule_options ";
	$sql .= (!empty($id)) ? "WHERE sch_opt_id = '".$id."' " : "";
	$sql .="ORDER BY optType, optName ";
	return sqlStatement($sql);
}

function insertSchOption($optName, $optType){
	$inId=0;
	if(!empty($optName) && !empty($optType)){
		$sql = "SELECT sch_opt_id FROM chart_schedule_options WHERE LCASE(optName)='".strtolower($optName)."' ";
		$row = sqlQuery($sql);
		if( $row == false ){
			$sql = "INSERT INTO chart_schedule_options(sch_opt_id, optName, optType) ".
				 "VALUES (NULL, '".sqlEscStr($optName)."', '".$optType."') ";
			$inId = sqlInsert($sql);

		}else{

		}
	}
	return $inId;
}


	function get_sec_copay_policy(){
		$sec_copay_collect_amt=""; $sec_copay_for_ins="";
		$qry = "SELECT sec_copay_collect_amt,sec_copay_for_ins
			FROM
			copay_policies WHERE policies_id='1'";
		$row=sqlQuery($qry);
		if($row==false){
			$sec_copay_collect_amt = $row['sec_copay_collect_amt'];
			$sec_copay_for_ins = $row['sec_copay_for_ins'];
		}

		return array($sec_copay_collect_amt, $sec_copay_for_ins);
	}

	function getServerAbbr(){
		$arr=array();
		$sql = "SELECT * FROM servers ";
		$rez = sqlStatement($sql);
		for($i=0; $row=sqlFetchArray($rez); $i++){
			if(!empty($row["id"])){
				$arr[$row["id"]] = $row["abbre"];
			}
		}
		return $arr;
	}

	/*get Anesthesia options from admin*/
	function get_drop_options_admin($type="", $flgid=""){
		//$ret = array("Fluorocaine22", "Tetracaine", "Alcaine420", "Fluoroscein Strips");

		$ret = array();
		if(!empty($type)){ $sql_type = " AND type='".$type."' ";  }else{ $sql_type=""; }
		$sql = "SELECT name, type,id FROM chart_admn_drop_options WHERE del='0' ".$sql_type." ORDER BY name  ";
		$rez=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			if(!empty($flgid)){
				$ret[$row["type"]][] = array($row["name"], $row["id"]);
			}else{
				$ret[$row["type"]][] = $row["name"];
			}
		}
		return $ret;

	}

	function get_lot_qty(){
		$login_facility_id=$_SESSION['login_facility'];

		if($_SESSION['remote_opt_loc_id']>0){
			$loc_id_imp=$_SESSION['remote_opt_loc_id'];
		}else{
			$loc_id_arr=array();
			$qry_pos_fac=imw_query("select in_location.id as loc_id
								from in_location join facility on in_location.pos=facility.fac_prac_code
								where facility.id='$login_facility_id' group by in_location.id");
			while($row_pos_fac = imw_fetch_array($qry_pos_fac)){
				$loc_id_arr[$row_pos_fac['loc_id']]=$row_pos_fac['loc_id'];
				$_SESSION['remote_opt_loc_id']=$row_pos_fac['loc_id'];
			}
			$loc_id_imp=implode("','",$loc_id_arr);
		}
		$arr_item_thrsh=$arr_threshold=$arr_item_stock=$arr_lot=array();
		$qry_item_lot="SELECT group_concat(lot_no) as lot_no,sum(stock)as stock,item_id FROM in_item_lot_total where item_id!='0' and loc_id in('$loc_id_imp') group by item_id";
		$res_item_lot=imw_query($qry_item_lot);
		while($row_item_lot=imw_fetch_assoc($res_item_lot)){
			$lot_no=$row_item_lot['lot_no'];
			$stock=$row_item_lot['stock'];
			$item_id=$row_item_lot['item_id'];
			if($item_id>0 && $stock>0){
				//$arr_lot[$lot_no][$item_id]=$stock;
				$arr_item_stock[$item_id]=$stock;
				$arr_threshold[]=$item_id;
				$arr_lot[$item_id]=$lot_no;
			}
		}
		//=======Get threshold val======================//
		$arr_threshold_val=array();
		if(count($arr_threshold)>0){
			$item_ids=implode(",",$arr_threshold);
			$qry_theshold="SELECT id,threshold from in_item where id in(".$item_ids.") and threshold>0";
			$res_theshold=imw_query($qry_theshold);
			while($row_theshold=imw_fetch_assoc($res_theshold)){
				$itemid=$row_theshold['id'];
				$threshold=$row_theshold['threshold'];
				$arr_threshold_val[$itemid]=$threshold;
			}
		}
		//==============================================//
		$arr_item_thrsh=array("0"=>$arr_item_stock,"1"=>$arr_threshold_val,"2"=>$arr_lot);
		return $arr_item_thrsh;
	}

	function get_admn_medications(){
		$arr_med=array();
		$qry_med="SELECT medicine_name,opt_med_id FROM medicine_data where opt_med_id!='0' and opt_med_id!=''";
		$res_med=imw_query($qry_med);
		while($row_med=imw_fetch_assoc($res_med)){
			$medicine_name=$row_med['medicine_name'];
			$opt_med_id=$row_med['opt_med_id'];
			$arr_med[$medicine_name]=$opt_med_id;
		}
		return $arr_med;
	}

	function getConsentForms($selId){

		$ret="";
		$ret.="<option value=\"\"></option>";

		//---- get consent category -------
		$arr_consent_category=array();
		$qry1 = "select cat_id FROM consent_category ORDER BY cat_id";
		$rez1 = sqlStatement($qry1);
		for($b=0;$row1=sqlFetchArray($rez1);$b++){
			$cat_id = $row1['cat_id'];
			$arr_consent_category[] = $cat_id;
		}

		//---- get consent forms -------

			$arr_consent=array();
			$qry2 = "select consent_form_id,consent_form_name, cat_id from consent_form where consent_form_status = 'Active' order by consent_form_name ASC";
					//"and cat_id = '$consentCategoryNameId' ".
			$rez2 = sqlStatement($qry2);
			for($a=0;$row2=sqlFetchArray($rez2);$a++){
				$consentFormId = $row2['consent_form_id'];
				$consentFormName = trim(ucwords($row2['consent_form_name']));
				$consentCatId = $row2['cat_id'];
				if(!in_array($consentCatId,$arr_consent_category) && $selId!=$consentFormId) { continue; } //DO NOT SHOW CONSENT OF DELETED CATEGORY
				$c++;
				$arr_consent[$consentFormId]=$consentFormName;
				$sel= (!empty($selId) && $selId==$consentFormId) ? "SELECTED" : "";

				//"consentFormDetails.php?consent_form_id=$consentFormId"
				$ret.="<option value=\"".$consentFormId."\" ".$sel.">".$consentFormName."</option>";
			}

		//}

		return array("0"=>$ret,"1"=>$arr_consent);

	}

	function getProcedures($procid=0, $flgnm=0){
		$ret=""; $sel_proc_nm="";
		$sql="SELECT procedure_name, procedure_id FROM operative_procedures WHERE del_status != '1' ORDER BY procedure_name ";
		$rez=sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$sel="";
			if($procid==$row["procedure_id"]){
				$sel="selected";
				$sel_proc_nm=$row["procedure_name"];
			}
			$ret.="<option value=\"".$row["procedure_id"]."\" ".$sel." >".$row["procedure_name"]."</option>";
		}

		return (!empty($flgnm)) ? array($ret, $sel_proc_nm) : $ret;
	}

	function getBotoxDosages(){
		$arr = "";
		$sql = "SELECT bdos FROM botox_dosages order by bdos ";
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$c = trim($row["bdos"]);
			if(!empty($c)){
				//if(!empty($arr)){ $arr .=","; }  $arr .= "'".$c."'";
				$sel= ($i==1) ? " checked " : "" ;
				$arr .="<div class='radio'><input type='radio' name='elem_btxds_opts' id='elem_btxds_opts_$i' value='".$c."' ".$sel."><label for='elem_btxds_opts_$i'> ".$c."</label></div>";
			}
		}

		//--
		if(!empty($arr)){
			$arr ="<table ><tr><td class='lbl' valign='top'><h3>Units</h3></td></tr><tr><td>".$arr."</td></tr></table>";
		}

		return $arr; //"'1.25','2.50','3.75','5.00','6.25','7.50'";
	}

	function getGroupInfo(){
		$arr=array();
		$sql = "select config_email, config_pwd, config_host from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1";
		$row=sqlQuery($sql);
		if($row!=false){
			$arr['email']=$row["config_email"];
			$arr['pwd']=$row["config_pwd"];
			$arr['host']=$row["config_host"];
		}
		return $arr;
	}

	function getSpecialityOpts(){
		$str_spec_opts = "";
		$sql = "SELECT * FROM admn_speciality WHERE status='0' Order By name ";
		$rez = imw_query($sql);
		while ($row = imw_fetch_assoc($rez)) {

			$sel = (!empty($elem_speciality) && $row["id"] == $elem_speciality) ? " selected " : "";
			$str_spec_opts .= "<option value=\"" . $row["id"] . "\"  " . $sel . " >" . $row["name"] . "</option>";
		}
		return $str_spec_opts;
	}

	// get AP Policy Settings
	function apPolicySettings($op=""){

		if($op=="save"){
			$enableCommApWV = $_POST["elem_enableCommApWV"];
			$enableDynApWV = $_POST["elem_enableDynApWV"];
			$str = "";
			if(!empty($enableCommApWV)){	$str .= "".$enableCommApWV.",";	}
			if(!empty($enableDynApWV)){  $str.= "".$enableDynApWV."";  }

			$sql = "UPDATE chart_admin_settings SET ap_policies='".sqlEscStr($str)."' WHERE id = '1' ";
			$row=sqlQuery($sql);
			echo 'Record Saved Successfully.';
			exit();
		}else{

			$str="";
			$sql = "SELECT ap_policies FROM chart_admin_settings WHERE id = '1' ";
			$row=sqlQuery($sql);
			if($row!=false){
				$str=$row["ap_policies"];
			}

			echo $str;
		}
	}

	function get_tech_mandatory($mstr_visit, $format=""){
		$ret=array();
		if(!empty($mstr_visit)){
			$sel_tech=imw_query("select * from tech_tbl WHERE ptVisit = '".$mstr_visit."' "); //$elem_masterPtVisit
			$tech_row=imw_fetch_assoc($sel_tech);
		}
		$arv=array("ocualr", "general_health", "medication", "surgeries", "allergies", "immunizations", "social",
			"cvf", "visit", "cc_history", "vision", "distance", "near", "ar", "pc", "mr", "cvf_c",
			"amsler_grid", "icp_color_plates", "steroopsis", "diplopia", "retinoscopy", "exophthalmometer",
			"pupil", "eom", "external", "iop");
		foreach($arv as $k => $v){
			$t = (isset($tech_row[$v]) && !empty($tech_row[$v])) ? $tech_row[$v] : "";
			if(!empty($format)){ $ret[$v]= $t;}
			else{	$ret["tm_".$v]=array($t); }
		}

		if(!empty($format)){
			echo json_encode($ret);
		}else{
			return $ret;
		}
	}

	function get_standrad_of_care($id=0, $smry=0, $strdesc=""){
		$phrase= " del_by='0' ";
		if(!empty($id)){
			if( !empty($smry) ){
				$phrase=" id=\"".$id."\" ";
			}else{
				$phrase.= " OR  id=\"".$id."\" ";
			}
		}

		$sql = " SELECT * FROM `admin_soc` where ".$phrase."  ";
		$rez = imw_query($sql);
		while($row = imw_fetch_assoc($rez)){
			$arr[] = $row;
		}

		if(!empty($smry)){ //summary
			$ret = "";
			if(!empty($id)){
				if(!empty($arr[0]["soc"])){
					if(!empty($arr[0]["soc"])){ $ret.="<strong>".$arr[0]["soc"]."</strong>\n"; }
					if(!empty($arr[0]["descp"])){ $ret.=$arr[0]["descp"]."\n"; }
				}
			}
			//
			$strdesc = trim($strdesc);
			if(!empty($strdesc)){  $ret.=$strdesc."\n"; }
			$ret = trim($ret);
			return (!empty($ret)) ? nl2br($ret) : "" ;
		}else{ //record
			return $arr;
		}
	}

	function get_lens_used(){
		$arr = array();
		$sql = " SELECT name FROM `admn_lens_used` where del_by='0' ORDER by name ";
		$rez = imw_query($sql);
		while($row = imw_fetch_array($rez)){
			$arr[] = $row["name"];
		}
		return json_encode($arr);
	}

	function get_iop_def_method(){
		$sql = "SELECT def_mthd FROM admn_iop_def WHERE phy_id IN (0, ".$_SESSION["authId"].") AND del_by='0' ORDER BY phy_id DESC ";
		$row = sqlQuery($sql);
		if($row!=false){
			$ret = $row["def_mthd"];
		}
		$ret = trim($ret);
		if(empty($ret)){
			$ret="Applanation";
		}
		return $ret;
	}

}
?>
