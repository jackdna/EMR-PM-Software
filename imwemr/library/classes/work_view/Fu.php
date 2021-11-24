<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
/*
File: Fu.php
Coded in PHP7
Purpose: This class provides F/U section related functionality.
Access Type : Include file
*/
//FU: Follow Up

class Fu{
private $pid, $fid;
public function __construct($pid="",$fid=""){
	$this->pid = $pid;
	$this->fid = $fid;
}
//Get xml values from database
public function getDbVal(){

	$sql= "SELECT followup FROM chart_assessment_plans ".
			"WHERE form_id='".$this->fid."'".
			"AND patient_id = '".$this->pid."'";
	$res = sqlQuery($sql); //$this->db->Execute($sql) or die("Error in Query: ".$this->db->errorMsg());
	if($res !== false){
		$strXml = $res["followup"];
	}

	return !empty($strXml) ? $strXml : "";
}

function fu_getXmlValsArr($xfup, $proid=""){
	$arr = array();

	if(!empty($xfup)){
		//echo "<xmp>".$xfup."</xmp>";
		$ox = simplexml_load_string($xfup);
		$len = count($ox->fu);
		if($len > 0){
			foreach($ox->fu as $fux){
				if(!empty($fux->number)){
					$arrTmp = array();
					$arrTmp["number"] = "".$fux->number;
					$arrTmp["time"] = "".$fux->time;
					$arrTmp["visit_type"] = "".$fux->visit_type;
					$arrTmp["provider"] = (!empty($proid)) ? $proid : "".$fux->provider;
					$arrTmp["chk_str"] = $this->getChkStr($arrTmp["number"], $arrTmp["time"], $arrTmp["visit_type"]);
					$arr[] = $arrTmp;
				}
			}
		}
	}

	return array($len, $arr);
}

function getChkStr($nm, $tm, $vt){
	$nm = trim("".$nm);
	$tm = trim($tm);
	$vt = trim($vt);
	if(!empty($nm)||!empty($tm)||!empty($vt)){
		$str = $nm."-".$tm."-".$vt;
	}else{
		$str = "";
	}
	return $str;
}

function fu_getXmlValsArr_db(){
	$ret=array();
	if(!empty($this->pid)&& !empty($this->fid)){
		$xfup=$this->getDbVal();
		$ret= $this->fu_getXmlValsArr($xfup);
	}
	return $ret;
}

function fu_getXml($arrnum,$arrfu=array(),$arrvisit=array(),$arrvisitOthr=array(),$arrProNm=array()){
	$len = count($arrnum);

	$dom = new DOMDocument('1.0','utf-8');
	$followup = $dom->appendChild($dom->createElement('followup'));
	$followup->setAttribute("len",$len);

	$i=0;

	do{
		if(is_array($arrnum[$i]) && isset($arrnum[$i]["number"])) {
			$tmp_num = (!empty($arrnum[$i]["number"])) ? $arrnum[$i]["number"] : "";
			$tmp_time = (!empty($arrnum[$i]["time"])) ? $arrnum[$i]["time"] : "" ; //clean4xml()
			$tmp_vtype = (!empty($arrnum[$i]["visit_type"])) ? $arrnum[$i]["visit_type"] : "" ;
			$tmp_vtypeOthr = ""; //(!empty($arrvisitOthr[$i])) ? $arrvisitOthr[$i] : "" ;
			$tmp_vPro = (!empty($arrnum[$i]["provider"])) ? $arrnum[$i]["provider"] : "" ;
		} else {
			$tmp_num = (!empty($arrnum[$i])) ? $arrnum[$i] : "";
			$tmp_time = (!empty($arrfu[$i])) ? $arrfu[$i] : "" ; //clean4xml()
			$tmp_vtype = (!empty($arrvisit[$i])) ? $arrvisit[$i] : "" ;
			$tmp_vtypeOthr = (!empty($arrvisitOthr[$i])) ? $arrvisitOthr[$i] : "" ;
			$tmp_vPro = (!empty($arrProNm[$i])) ? $arrProNm[$i] : "" ;
		}
		
		
		if(empty($tmp_num)&&empty($tmp_time)&&empty($tmp_vtype)&&empty($tmp_vtypeOthr)){
		$i++;
		continue;
		}
		
		//clear special characters
		$tmp_num = filter_var($tmp_num, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$tmp_time = filter_var($tmp_time, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$tmp_vtype = filter_var($tmp_vtype, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$tmp_vtypeOthr = filter_var($tmp_vtypeOthr, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		//clear special characters
		
		
		$fu = $followup->appendChild($dom->createElement('fu'));

		$number = $dom->createElement('number');
		$cNumber = $dom->createCDATASection($tmp_num);
		$number->appendChild($cNumber);
		$fu->appendChild($number);
		
		$time = $fu->appendChild($dom->createElement('time',$tmp_time));

		$visit_type = $dom->createElement('visit_type');
		$c_visit_type = $dom->createCDATASection($tmp_vtype);
		$visit_type->appendChild($c_visit_type);
		$fu->appendChild($visit_type);
		
		$provider = $dom->createElement('provider');
		$c_provider = $dom->createCDATASection($tmp_vPro);
		$provider->appendChild($c_provider);
		$fu->appendChild($provider);

		//Insert Options
		if(($tmp_vtypeOthr==1) && (!empty($tmp_vtype)) && ($tmp_vtype != "CEE/DFE") &&
			($tmp_vtype != "ER/Acute") && ($tmp_vtype != "CL Fit")){
			$oAdmn = new Admn();
			$oAdmn->insertFuOption($tmp_vtype);
		}

		$i++;
	}while($i<$len);

	$str = $dom->saveXML();
	return sqlEscStr($str);
}

public function mergeFuArr($arr1,$arr2){
	$ln = count($arr2);
	for($i=0;$i<$ln;$i++){
		$strNum ="".$arr2[$i]["number"];
		$strTime ="".$arr2[$i]["time"];
		$strVT ="".$arr2[$i]["visit_type"];
		$strChk = "".$arr2[$i]["chk_str"];
		if(empty($strChk))continue;

		$flg=true;
		$ln2= count($arr1);
		for($j=0;$j<$ln2;$j++){
			if(empty($arr1[$j]["chk_str"]))continue;

			$tmp = "".$arr1[$j]["chk_str"];
			if($tmp == $strChk){
				$flg=false;
				break;
			}
		}

		if($flg==true){
			$chk_str = $this->getChkStr($strNum, $strTime, $strVT);
			$arr1[]=array("number"=>$strNum,"time"=>$strTime,"visit_type"=>$strVT,"chk_str"=>$chk_str);
		}
	}
	return $arr1;
}

public function getFormInfo(){
	global $elem_physicianId;
	$arrFuVals = array();
	$lenFu = 0;
	
	list($lenFu, $arrFuVals) = $this->fu_getXmlValsArr_db();
	
	$arrOpFu = array("Days","Weeks","Months","Year");
	$arrOpProvidersFu = array();
	
	$oUsr1 = new User();	
	
	//Follow Up default ------------	
	if(empty($lenFu)){
		$arrFuVals[] = array( "number" => $elem_followUpNumber,
							  "time" => $elem_followUp,
							  "visit_type" => $elem_followUpVistType,
								"provider" => "");
		$lenFu = 1;
	}	
	
	for($i=0;$i<$lenFu;$i++){
		//Set Pro Name
		if(empty($arrFuVals[$i]["number"])&&empty($arrFuVals[$i]["time"])&&empty($arrFuVals[$i]["visit_type"])&&empty($arrFuVals[$i]["provider"])){
			if(!empty($GLOBALS['alwaysDocFU'])){
				$arrFuVals[$i]["provider"]=$GLOBALS['alwaysDocFU'];
			}else if(isset($_SESSION["res_fellow_sess"]) &&  !empty($_SESSION["res_fellow_sess"])){
				$arrFuVals[$i]["provider"]=$_SESSION["res_fellow_sess"];
			}else{
				$arrFuVals[$i]["provider"]=$elem_physicianId;
			}
		}
		
		//		
		$tmp="";		
		$tmp = $oUsr1->getUsersDropDown("Fu", "", $arrFuVals[$i]["provider"], "", "", 1, 1);
		$arrFuVals[$i]["provider_options"]=$tmp;
		
		//
		$tmp="";
		foreach($arrOpFu as $tmp_id => $tmp_nm){			
			$sel = ($tmp_nm==$arrFuVals[$i]["time"]) ? "selected" : "";			
			$tmp.="<option value=\"".$tmp_nm."\"  ".$sel." >".$tmp_nm."</option>";
		}
		$arrFuVals[$i]["time_options"]=$tmp;
	}

	return array($lenFu, $arrFuVals);

}

function getFuSelNum($id, $val="",$moe="", $adcs="", $coords=""){
	global $arrFuNum_menu;

	// ---
	// ---

	$str = "";
	//$str = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
	//$str .="<tr><td>";
	$str .="<div class='input-group'><input type=\"text\" name=\"elem_followUpNumber[]\" id=\"elem_followUpNumber_".$id."\" value=\"".$val."\"
			class=\" ".$adcs." form-control\" onchange=\"fu_refineNum(this)\" ".$moe.">";
	//$str .="<input type=\"hidden\" name=\"elem_followUpNumberHidden[]\" id=\"elem_followUpNumberHidden_".$id."\" value=\"".$dbVal."\" >";
	//$str .="</td><td>";
	
	$str .= get_simple_menu($arrFuNum_menu, 'menu_FuNum'.$id,  'elem_followUpNumber_'.$id);
	
	//$str .= wv_get_simple_menu($arrFuNum_menu,"menu_FuNum","elem_followUpNumber_".$id,0,1,
	//				array("coords"=>$coords,"stpClkHide"=>1,"pdiv"=>"divWorkView"));
	//$str .="</td></tr>";
	//$str .="</table>";
	$str .= "</div>";
	return $str;
}

function getSelectFuHtml($id, $dbVal="",$moe="",$adcs="",$coords=""){
	global $arrFuVist_menu;

	// ---
	// ---

	$str = "";
	//$str = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
	//$str .="<tr><td id=\"sp_followUpVistType_".$id."\">";
	$str .="<div class='input-group'><input type=\"text\" name=\"elem_followUpVistType[]\" id=\"elem_followUpVistType_".$id."\" value=\"".$dbVal."\"
			 class=\" ".$adcs." form-control\" onchange=\"changeOther(this)\" ".$moe." title=\"".$dbVal."\">";
	$str .="<input type=\"hidden\" name=\"elem_followUpVistTypeOther[]\" id=\"elem_followUpVistTypeOther_".$id."\" value=\"\">";
	//$str .="<td id=\"sp_followUpVistTypeMenu_".$id."\" >";
	$str .= get_simple_menu($arrFuVist_menu, 'menu_FuOptions'.$id,  'elem_followUpVistType_'.$id);
	//$str .= getSimpleMenu($arrFuVist_menu,"menu_FuOptions","elem_followUpVistType_".$id,0,0,array("coords"=>$coords,"pdiv"=>"divWorkView"));
	//$str .="</td>";
	//$str .="<td id=\"sp_fu_vis_other_".$id."\" width=\"14\" align=\"center\" style=\"visibility:hidden;\">";
	//$str .="<input type=\"text\" size=\"32\" class=\"txt_10\" name=\"elem_followUpVistTypeOther[]\" id=\"elem_followUpVistTypeOther_".$id."\" value=\"\">&nbsp;
	//$str .="<span class=\"fu_num_close\" onclick=\"removeMe('".$id."');\"></span>";
	//$str .="</td>";
	//$str .="</tr>";
	//$str .="</table>";
	$str .= "</div>";
	return $str;
}

function get_fu_menu($w=""){
	if($w=="n"){ //Number
		$arrFuNum_menu = array();
		for ($i = 1; $i <= 18; $i++) {
			$txt = $i;
			if ($i == 11) {
				$txt = "12";
			}
			if ($i == 12) {
				$txt = "Today";
			}
			if ($i == 13) {
				$txt = "Calendar";
			}
			if ($i == 14) {
				$txt = "PRN";
			}
			if ($i == 15) {
				$txt = "PMD";
			}
			if ($i == 16) {
				$txt = "First available";
			}
			if ($i == 17) {
				$txt = "As Scheduled";
			}
			if ($i == 18) {
				$txt = "Next Available";
			}			
			$arrFuNum_menu[] = array($txt, $emp, $txt);
		}
		$arrFuNum_menu[] = array("-", $emp, "-");
		return $arrFuNum_menu;
	}
	
	if($w=="v"){ //visit
		$ad = new Admn();
		$arrFuVist = array(); //array("CEE/DFE", "ER/Acute", "CL Fit"); //20353-Michigan Eye Institute- Cannot Edit/Remove Follow Up Option
		$tmp = $ad->getFuOptions(0, 1);
		if (count($tmp) > 0) {	$arrFuVist = array_merge($arrFuVist, $tmp);	}
		
		$arrFuVist_menu = array();
		foreach ($arrFuVist as $key => $val) {
			$arrFuVist_menu[] = array($val, $emp, $val);
		}
		$arrFuVist_menu[] = array("Other", $emp, "Other"); //Other
		return $arrFuVist_menu;	
	}
	
}


}


?>