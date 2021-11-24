<?php

/*
//Mysql --
//Session --
// Html --
// Date --
//String --
//-- Admin --
//-- Insurance -----
*/

//Mysql --
//mysql function set char set
/**
function sqlSetCharSet(){
	/* change character set to utf8 *-/
	if (!mysqli_set_charset($GLOBALS['dbh'], "utf8")) {
	    HelpfulDie("Error loading character set utf8: %s\n", mysqli_error($GLOBALS['dbh']));	    
	}
	
}

sqlSetCharSet();//called at load.
**/

// ------------------  

//errors handler ----------

function usr_err_handler($ins, $msg, $fnm, $lno){
	if($ins==1 || $ins==256 || $ins==4096){
		$osf = new SaveFile();
		$pth = $osf->ptDir("uslog","i");
		$lf = "log_".date("m-d-Y").".log";	
		$fp = fopen(''.$pth."/".$lf, 'a');		
		fwrite($fp, "".$ins.", ".$msg.", ".$fnm.", ".$lno."\n");
		fclose($fp);
	}
}

function fat_err_handler(){
	$error = error_get_last();
	if($error){ usr_err_handler($error["type"], $error["message"], $error["file"], $error["line"]); }
}

//Session --
//Check if patient is empty and redirect it patient search
function wv_check_session($wh="")
{	
	if(!isset($_SESSION['authId']) || empty($_SESSION['authId'])){
		print_r("<p>Error: Session has got corrupted. Close this window and restart your application.</p>");
		exit();
	}
	if(!isset($_SESSION["patient"]) || empty($_SESSION["patient"])){
		if($_REQUEST["elem_formAction"]=="load_pvc"){ exit("Please select Patient."); }
		//echo  "<!DOCTYPE html><html lang=\"en\"><body><script>top.document.getElementById('fmain').src='".$GLOBALS["web_root"]."/interface/core/index.php?pg=default-page'</script ></body></html>";
		if($wh=="save"){ exit("Current session experiencing a change, Please reload the chart.(Empty patient id in session)"); }
		else{
		header("location:".$GLOBALS['webroot']."/interface/landing/index.php");	
		exit;
		}
	}
}

// Html --
//-----------------
//get array of options and return <ul><li></li></ul>
function wv_getMenuHtmlHidden($arr, $attr, $vrsn=1){
	$flgTmpl=0;
	if(strpos($attr, "Template")!==false){$flgTmpl=1;}
	$str="";
	if($vrsn==1){
		if(count($arr)>0){
			foreach($arr as $k => $v){
				if(!empty($k)){
					$strAtr="";
					if($flgTmpl==1){ $strAtr=" data-id=\"".$v[2]."\"  "; }
					$str.="<li><a href=\"#\"  ".$strAtr." >".$k."</a></li>";
				}
			}
		}		
		if(!empty($str)){ $str="<div ".$attr." style=\"display:none;\" ><ul class=\"dropdown-menu dropdown-menu-right\">".$str."</ul></div>"; }
	}else if($vrsn==2){
		
		$arr_str = array();
	
		if(count($arr)>0){
			foreach($arr as $k => $v){
				
				$str="<li class=\"dropdown-header\">".$k."</li><li role=\"separator\" class=\"divider\"></li>";
				$arr_v = $v;
				if(count($arr_v)>0){
					foreach($arr_v as $k_v => $v_v){
						if(!empty($v_v)){
							$strAtr="";
							if($flgTmpl==1){ $strAtr=" data-id=\"".$v_v."\"  "; }
							$str.="<li><a href=\"javascript:void(0);\"  ".$strAtr." >".$v_v."</a></li>";
						}
					}
				}
				
				$str = "<li class=\"col-sm-6 \"><ul class=\"list-unstyled\">".$str."</ul></li>";
				
				$arr_str[] = $str;
				
			}
		}
		
		//
		$str="";
		if(count($arr_str)>0){	$str= implode("", $arr_str); 	}
		$str="<ul class=\"dropdown-menu mega-dropdown-menu \" >".$str."</ul>";		
	}
	
	return $str;
}

function wv_getHtmlHiddenFields($arr){
	$str_hidden_vals="";
	foreach($arr as $fldnm=>$arval){
		$nm = $fldnm;
		$id = $nm;
		$val=$arval[0];
		if(isset($arval["id"]) && !empty($arval["id"])){ $id=$arval["id"]; }
		if(isset($arval["ev"]) && !empty($arval["ev"])){ $ev=$arval["ev"]; }else{ $ev=""; }	
		
		$str_hidden_vals.="<input type=\"hidden\" name=\"".$nm."\" id=\"".$id."\" value=\"".$val."\" ".$ev." >";
	}
	return $str_hidden_vals;
}

// Date --
//Date functions --
function wv_dt_format(){
	$format = isset($GLOBALS['date_format']) && !empty($GLOBALS['date_format']) ? $GLOBALS['date_format'] : 'mm-dd-yyyy';
	$separator = "-";
	if(strpos($date,'/')!==false) { $separator = "/";}
	else if(strpos($date,'-')!==false){ $separator = "-";}
	else if(strpos($date,'\\')!==false){$separator = "\\";}
	return array($format, $separator);
}

function wv_dt_format_js(){
	list($format, $sep) = wv_dt_format();
	$format = str_replace(array("mm", "dd", "yyyy"),array("mm", "dd", "yy"),  $format);
	if($format=="m-d-Y"){ $format="mm-dd-yy"; }
	return $format;
}

function wv_formatDate($dt,$syr=0,$tm=0, $op="show", $frmt="")
{
	if(!empty($dt)){
		
		list($format, $sep) = wv_dt_format();
		if(!empty($frmt)){
			$format = $frmt;
		}
		
		//$separator = get_separator_inter($format);
		if($op == "insert"){
			$odt = $dt;
			if(preg_match("/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/",$dt,$regs))
			{					
				if($format=="dd".$sep."mm".$sep."yyyy"){
					$dt=$regs[3]."".$sep."".$regs[2]."".$sep."".$regs[1];
				}else{
					$dt=$regs[3]."".$sep."".$regs[1]."".$sep."".$regs[2];
				}
				//return $dt;
			}else if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/",$dt,$regs)){
				$dt=$regs[1]."".$sep."".$regs[2]."".$sep."".$regs[3];
			}
			
			//time
			if($tm == 3){
				if(preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/",$odt,$regs)){
					$tmp = $regs[1].":".$regs[2].":".$regs[3];
					$dt .= (!empty($tmp) && ($tmp != "00:00:00")) ? " ".$tmp : "";
				}
			}

		}else{
			$odt = $dt;
			if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/",$dt,$regs))
			{
				if($format=="dd".$sep."mm".$sep."yyyy"){
					$dt=$regs[3]."".$sep."".$regs[2]."".$sep."";
					$dt .= ($syr == 1) ? substr($regs[1], 2) : $regs[1];
				}else{
					$dt=$regs[2]."".$sep."".$regs[3]."".$sep."";
					$dt .= ($syr == 1) ? substr($regs[1], 2) : $regs[1];
				}					
				//return $dt;
			}
			//time
			if($tm == 1){
				if(preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/",$odt,$regs)){
					$tmp = $regs[1].":".$regs[2].":".$regs[3];
					$dt .= (!empty($tmp) && ($tmp != "00:00:00")) ? " ".$tmp : "";
				}
			}else if($tm == 2 || $tm == 3){
				if(preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/",$odt,$regs)){
					$tmp = $regs[1].":".$regs[2];
					if(!empty($tmp) && ($tmp != "00:00")){
						//add am:pm
						if($tm == 3){
							$pfx = "am";
							if($regs[1]==12){$pfx = "pm";}
							if($regs[1]>12){ $regs[1] = $regs[1]-12; $pfx = "pm"; }
							$tmp = $regs[1].":".$regs[2]." ".$pfx;
						}else{						
							$dt .=  " ".$tmp;
						}
					}
				}
			}
		}
	}
	return $dt;
}

function wv_dt($frmt=""){
	$dt="";
	if($frmt=="now"){
		$dt=date("Y-m-d H:i:s");
	}
	else if($frmt=="time"){
		$dt=date("H:i:s");
	}
	else{
		$dt=date("Y-m-d");
	}
	return $dt;
}

function isDt12mOld($dt1, $dt2){
	$datetime1 = strtotime($dt1);
	$datetime2 = strtotime($dt2);
	$mxTS = strtotime($dt2.' + 1 years');
	return ($datetime1>$mxTS)?1:0;
}

//get dt d-ff in years
function dt_getDtDiff($stdb, $stds=""){
	$ret=0;
	if(!empty($stdb) && $stdb!="0000-00-00"){
		if(empty($stds)){  $stds = date("Y-m-d"); }
		$date1=date_create($stdb);
		$date2=date_create($stds);
		$diff=date_diff($date1,$date2);
		$ret = $diff->y;
	}
	return $ret;		
}

function isDtPassed($dt, $strtime){
	//$datetime1 = strtotime($dt1);
	$curDate = strtotime(date("Y-m-d"));
	$mxTS = strtotime($dt.' + '.$strtime);
	return ($curDate<$mxTS)?0:1;
}

function dtCalc($dt,$formulea,$format){
	$tm = strtotime($dt.$formulea);
	return date($format,$tm);
}

//String --
function wv_str_compare_wo_space($str_h, $str_n){
	//w/o space
	$str_h=trim($str_h);
	$str_n=trim($str_n);
	$str_h=str_replace(" ","", $str_h);
	$str_n=str_replace(" ","", $str_n);
	return strpos($str_h,$str_n);
}

function wv_str_replace_html_chars($str){
	if(!empty($str)){
		$str = str_replace(array("&amp;", "&gt;", "&lt;", "&quot;","&#39;"), array("&", ">", "<", "\"", "'"), $str);
	}
	return $str;
}
function jsEscape($str) { 
    return addcslashes($str,"\\\'\"&\n\r<>"); 
}
function assignZero($val){
	return ($val != "1") ? "0" : "1";
}
function uni8char($u) {
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}
function checkHPIFormatChars($s,$w){
	$a="9675";
	$b="9679";
	$c="8209";
	$d="9678";
	
	$a1 = uni8char($a);
	$b1 = uni8char($b);
	$c1 = uni8char($c);
	$d1 = uni8char($d);	
	
	$a2="&#".$a.";";
	$b2="&#".$b.";";
	$c2="&#".$c.";";
	$d2="&#".$d.";";
	
	if($w=="en"){
		$s = str_replace(array($a1,$b1,$c1,$d1),array($a2,$b2,$c2,$d2),$s);
	}else	if($w=="de"){
		$s = str_replace(array($a2,$b2,$c2,$d2),array($a1,$b1,$c1,$d1),$s);
	}else	if($w=="pr"){ //print
		$a2 = chr(186);
		$b2 = chr(149);
		$c2 = chr(45);
		$d2 = " ";
		$s = str_replace(array($a1,$b1,$c1,$d1),array($a2,$b2,$c2,$d2),$s);
	}
	
	return  $s;
}

//function //Remove Site and Dx Codes from assessments
function remSiteDxFromAssessment($asmt){
	$asmt = trim($asmt);
	
	//remove comments	
	$indxTmp = strpos($asmt,";");
	if($indxTmp !== false){
		$asmt = substr($asmt,0,$indxTmp);
		$asmt = trim($asmt);
	}
	
	$ptrn = "/\s+(\-\s+(OD|OS|OU)\s+)?\((\s*\w{3}(\.[\w\-]{1,4})?(\,)?)+\)$/";
	if(preg_match($ptrn, $asmt, $pre_match)){
		if(!empty($pre_match[0])){ 
			$ptrn22="/[0-9]+/";					
			if(preg_match($ptrn22, $pre_match[0])){ //check alphanumeric dx code, if not do not remove
				$asmt = preg_replace($ptrn, "", $asmt);	
			}
		}
	}
	return trim($asmt);
}
//Function Remove line breaks
function removeLineBreaks($str)
{
	return preg_replace("(\r\n|\n|\r)", " ", $str);
}

function xml_safe($str){
	return str_replace("&eacute;", "&#201;",$str);
}

function wv_getNumber($string){
	$num = preg_replace('/[^0-9]/','',$string);
	return trim($num);
}
function wv_strReplace($str,$ptrn, $replacement=""){
	switch($ptrn){
		case "LASTCOMMA":
			$ptrn = '/(\s)*\,(\s)*$/i';
		break;
		case "LASTDOT":
			$ptrn = '/(\s)*\.(\s)*$/i';
		break;
		case "LASTSEMICOLON":
			$ptrn = '/(\s)*\;(\s)*$/i';
		break;
		case "DATE":
			$ptrn = '/\d{2}-\d{2}-\d{4}/i';
		break;
	}

	if(!empty($ptrn)){
		$str= preg_replace($ptrn, $replacement, $str);
	}
	return $str;
}
function mk_var_nm($str,$wh){
	$str = preg_replace("/[^A-Za-z0-9]/", '', $str);
	$str = $wh.$str;
	return $str;
}

//Array ----
function array_lsearch($str,$array,$t=0){
    $found=array();
    foreach($array as $k=>$v){
        if(strtolower($v)==strtolower($str)){
            $found[]=$v;
        }
    }
    $f=count($found);
	if($t==1){
		if($f==0){return false;}else if($f==1){return $found[0];}else{ return $found;}
	}else{
		if($f==0){return false;}else{ return true;}
	}
}

//php array search case Insensetive
//$flgRet=1: return all indexes which match
function in_array_nocase($ndl, $hs, $flgRet=0){
	$ret1=false;
	$ret2=array();
	
	if(count($hs)>0){
	foreach($hs as $key => $val){
		if(strcasecmp($val, $ndl) == 0){
			if($flgRet==1){
				$ret1=true;
				$ret2[]=$key;
			}else{
				return true;
				break;
			}
		}
	}
	}	
	return ($flgRet==0) ? false : array($ret1, $ret2) ;
}

// check recursively multidimentional
function recursive_array_search($needle,$haystack) {
	foreach($haystack as $key=>$value) {
		$current_key=$key;
		if($needle==$value || (is_array($value) && recursive_array_search($needle,$value) !== false)) {
			return $current_key;
		}
	}
	return false;
}

function ar_suffix(&$value,$key,$str){
	$value = array("label"=>$value["label"]."~~".$str, "value"=>$value["value"]."~~".$str, "dxid"=>$value["dxid"]);
}

//Array search , 2-dimentional array. returns key
function arr_search_2dim($nedl, $arr){
	
	if(empty($nedl)){return "";}
	
	$tmp = array_search($nedl,$arr);
	if($tmp===false){			
		//Check for inner arrays
		foreach($arr as $key => $val){			
			if(is_array($val)){
				$tmp2 = array_search($nedl,$val);
				if($tmp2!==false){
					$tmp=$key;
					break;
				}
			}	
		}				
	}
	
	return $tmp;	
}

function arrmultisort($arr){	
	sort($arr);
	//*
	$arrnew = array();
	if(count($arr)){
		foreach($arr as $key => $val){
			//asort($val);			
			$arrtmp=array();			
			if(count($val)>0){				
				foreach($val as $key2 => $val2){
					$arrtmp[$val2[0].$val2[1]] = $val2;
				}
			}
			ksort($arrtmp);			
			$arrnew[$key]=array_values($arrtmp);
		}
	}
	
	//asort($arrnew);
	
	return $arrnew;
}

function ar_replace_newline($ar){	
	if(count($ar) > 0){
		$ret=array();
		foreach($ar as $k => $v){
			if(!empty($v) && strpos($v,"\r")!==false){
				$v = nl2br($v);
				$v = str_replace("\r","",$v);
				$v = str_replace("\n","",$v);			
				$ret[$k]=$v;
			}else{
				$ret[$k]=$v;
			}
		}
		$ar=$ret;
	}
	return $ar;
}
//-- Admin --
// -- Menu --

function wv_get_simple_menu($arrMenu,$menuId,$elemTextId,$def=0){
	
	$right = " dropdown-menu-right ";
	if(strpos($elemTextId, "_3")!=false || strpos($elemTextId, "Overref")!=false){
		$right .= " dd-menu-top ";
	}	
	
	$cls_ul = "";
	$cls = "dropdown-toggle";
	$str_def=" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" data-trgt-id=\"".$elemTextId."\" ";
	if(!empty($def)){ $str_def = " onclick=\"vis_add_menu(this, '".$menuId."', '".$elemTextId."')\" "; $cls=""; $cls_ul = "".$menuId."_ul"; }
	
	$str="".		
		"<div class=\"input-group-btn menu ".$menuId."\">".
			"<button type=\"button\" class=\"btn ".$cls."\"  ".$str_def." ><span class=\"caret\"></span></button>"; 
	if(empty($def) || $def==2){
		$str1 = "";
		$str1.="".	"<ul class=\"dropdown-menu ".$right." ".$cls_ul."\">";
		if(count($arrMenu)>0){
			foreach($arrMenu as $k => $arv){
				$optionLabel = $arv[0];
				$optionSubMenu = $arv[1];
				$optionValue = $arv[2];
				
				$str1.="<li><a href=\"javascript:void(0);\" data-val=\"".$optionValue."\" >".$optionLabel."</a></li>";
			}
		}	
		$str1.=	"</ul>";
		if($def==2){ return $str1; }else{ $str.=$str1; }
	}
	$str.=	"</div><!-- /btn-group -->";
	
	return $str;

}

function getTestFormFields($testName,$fAssc="0"){
	switch($testName){
		case "VF":
			$field_FormId = "formId";
			$tbl_test = "vf";
			$field_Key = "vf_id";
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "VF-GL":
			$field_FormId = "formId";
			$tbl_test = "vf_gl";
			$field_Key = "vf_gl_id";
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "NFA":
		case "HRT":
			$field_FormId = "form_id";
			$tbl_test = "nfa";
			$field_Key = "nfa_id";
			$testName = "NFA"; //tst name in scan
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;
		case "OCT":
			$field_FormId = "form_id";
			$tbl_test = "oct";
			$field_Key = "oct_id";
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;
		
		case "OCT-RNFL":
			$field_FormId = "form_id";
			$tbl_test = "oct_rnfl";
			$field_Key = "oct_rnfl_id";
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;

		case "Pacchy":
		case "Pachy":
			$field_FormId = "formId";
			$tbl_test = "pachy";
			$field_Key = "pachy_id";
			$testName = "Pacchy";  //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "IVFA":
			$field_FormId = "form_id";
			$tbl_test = "ivfa";
			$field_Key = "vf_id";
			$f_ptId = "patient_id";
			$f_edt = "exam_date";

		break;
		case "ICG":
			$field_FormId = "form_id";
			$tbl_test = "icg";
			$field_Key = "icg_id";
			$f_ptId = "patient_id";
			$f_edt = "exam_date";

		break;		
		case "Disc":
		case "Fundus":
			$field_FormId = "formId";
			$tbl_test = "disc";
			$field_Key = "disc_id";
			$testName = "Disc"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;

		case "discExternal":
		case "External/Anterior":
		case "External":
			$field_FormId = "formId";
			$tbl_test = "disc_external";
			$field_Key = "disc_id";
			$testName = "discExternal"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "Topogrphy":
		case "Topography":
			$field_FormId = "formId";
			$tbl_test = "topography";
			$field_Key = "topo_id";
			$testName = "Topogrphy"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "TestOther":
		case "Other":
		case "TemplateTests":
			$field_FormId = "formId";
			$tbl_test = "test_other";
			$field_Key = "test_other_id";
			$testName = "testOther"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "Laboratories":
		case "TestLabs":
		case "Labs":
			$field_FormId = "formId";
			$tbl_test = "test_labs";
			$field_Key = "test_labs_id";
			$testName = "testLabs"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";

		break;
		case "A/Scan":
		case "Ascan":
		case "A-Scan":
			$field_FormId = "form_id";
			$tbl_test = "surgical_tbl";
			$field_Key = "surgical_id";
			$testName = "Ascan"; //tst name in scan
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;
		
		case "iOLMaster":
		case "IOL Master":
		case "IOL_Master":
			$field_FormId = "form_id";
			$tbl_test = "iol_master_tbl";
			$field_Key = "iol_master_id";
			$testName = "IOL_Master"; //tst name in scan
			$f_ptId = "patient_id";
			$f_edt = "examDate";

		break;

		case "B-Scan":
		case "BScan":
			$field_FormId = "formId";
			$tbl_test = "test_bscan";
			$field_Key = "test_bscan_id";
			$testName = "BScan"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";
		break;

		case "Cell Count":
		case "CellCount":
			$field_FormId = "formId";
			$tbl_test = "test_cellcnt";
			$field_Key = "test_cellcnt_id";
			$testName = "CellCount"; //tst name in scan
			$f_ptId = "patientId";
			$f_edt = "examDate";
		break;
		case "GDX":
			$field_FormId = "form_id";
			$tbl_test = "test_gdx";
			$field_Key = "gdx_id";
			$f_ptId = "patient_id";
			$f_edt = "examDate";
		break;		
		
		default:
			exit("NOT Defined: ".$testName);
		break;

	}

	//Test
	if($fAssc == 1){
		return array("formId"=>$field_FormId,"tbl"=>$tbl_test,"keyId"=>$field_Key,"testNm"=>$testName,"ptId"=>$f_ptId,"eDt"=>$f_edt);
	}else{
		return array($field_FormId,$tbl_test,$field_Key,$testName,$f_ptId,$f_edt);
	}
}

function checkTestDone($tstNm,$pid,$frmId="",$tstId=""){
	if($tstNm=='TemplateTests') return false;
	$arr = getTestFormFields($tstNm,"1");
	if(!empty($frmId) || !empty($tstId)){
		$sql = "SELECT ".$arr["keyId"]." FROM ".$arr["tbl"]." WHERE ".$arr["ptId"]."='".$pid."' ";
		if(!empty($frmId)){
			$sql .= "AND (".$arr["formId"]." = '".$frmId."' AND purged='0' AND del_status='0') ";
		}else if(!empty($testId)){
			$sql .= "AND ".$arr["keyId"]." = '".$tstId."' ";
		}
		$row = sqlQuery($sql);
		if($row != false){
			return !empty($row[$arr["keyId"]]) ? $row[$arr["keyId"]] : false;
		}
	}
	return false;
}

function providerViewLogFun($scan_doc_id,$provider_id,$patient_id,$section_name)
{
	if(isset($section_name) && $section_name!=''){
		$add_query = " AND section_name='".$section_name."'";
	}else{$add_query='';}
	
	$chk_sql= "SELECT id FROM provider_view_log_tbl where scan_doc_id='".$scan_doc_id."' AND provider_id='".$provider_id."' AND patient_id='".$patient_id."'".$add_query;
	$chk_res = imw_query($chk_sql);
	
	if( imw_num_rows($chk_res) <= 0)
	{
		$insrtScnQry = "INSERT INTO provider_view_log_tbl SET 
						  scan_doc_id 	= '".$scan_doc_id."', 
						  patient_id 	= '".$patient_id."',
						  provider_id 	= '".$provider_id."', 
						  section_name 	= '".$section_name."',
						  date_time 	= '".date('Y-m-d H:i:s')."'";
		$insrtScnRes = imw_query($insrtScnQry);
	}
}

function del_pt_goals($record_id)
{
	$record_id = (int) $record_id;
	if( $record_id )
	{
		$qry = "Update patient_goals Set delete_status = 1 Where id = ".$record_id." ";
		$sql = imw_query($qry);
		if( $sql )
			$msg = '1_Record deleted successfully';
		else
			$msg = '0_There is a problem while deleting record(s).';
	}
	else
		$msg = '0_There is a problem while deleting record(s).';
	
	return $msg;
}

function save_pt_goals(&$request)
{
	extract($request);
	$txt_patient_id = $_SESSION['patient'];
	$txt_form_id = !isset($_SESSION['form_id']) ? $_SESSION['finalize_id'] :  $_SESSION['form_id'];
		
	$arrMsg = array();	
	foreach($goal_id as $key => $goalId)	
	{
		$txt_goalSet = addslashes($goal_set[$key]);
		$txt_loincCode = addslashes($goal_code[$key]);
		$txt_goalDataType = (strtolower($goal_data_type[$key]) == 'range') ? 'range' : 'plain_text';
		$txt_goalData = addslashes($goal_data[$key]);
		$txt_goalDataUnit = addslashes($goal_unit[$key]);
		$txt_operatorId = $goal_opr_id[$key];
		$txt_goalDate = getDateFormatDB($goal_date[$key]);
		
		if( $txt_goalSet )
		{
			$qrySet = "patient_id = '".$txt_patient_id."', form_id= '".$txt_form_id."', goal_set= '".$txt_goalSet."', loinc_code = '".$txt_loincCode."', goal_data = '".$txt_goalData."', goal_data_type = '".$txt_goalDataType."', gloal_data_type_unit = '".$txt_goalDataUnit."', operator_id = '".$txt_operatorId."', goal_date = '".$txt_goalDate."' ";
		
			if( $goalId )
				$qry = "Update patient_goals Set ".$qrySet. " Where id = '".$goalId."'";
			else
				$qry = "Insert Into patient_goals Set ".$qrySet;
			
			$res = imw_query($qry) or $arrMsg[] =imw_error();
		}
		
	}
	
	return print_goals();
}

function save_pt_health_status(&$request)
{
	extract($request);
	$txt_patient_id = $_SESSION['patient'];
	$txt_form_id = !isset($_SESSION['form_id']) ? $_SESSION['finalize_id'] :  $_SESSION['form_id'];
	$arrMsg = array();	

	foreach($functional_id as $key => $fun_Id){
		$txt_functional_status=$txt_functional_ccd_code=$txt_functional_status_date=$txt_operatorId=$txt_entered_date="";
		$txt_functional_status = addslashes($functional_status_text[$key]);
		$txt_functional_ccd_code = addslashes($functional_ccd_code[$key]);
		$txt_functional_status_date = getDateFormatDB($functional_status_date[$key]);
		$txt_operatorId=$_SESSION['authUserID'];
		$txt_entered_date=date("Y-m-d H:i:s");
		if($txt_functional_status)
		{
			$qrySet = "status_type='functional',patient_id = '".$txt_patient_id."', form_id= '".$txt_form_id."', status_text= '".$txt_functional_status."', ccd_code = '".$txt_functional_ccd_code."',ccd_code_system='SNOMED-CT', entered_by = '".$txt_entered_date."', status_date = '".$txt_functional_status_date."' ";
		
			if( $fun_Id )
				$qry = "Update patient_health_status Set ".$qrySet. " Where id = '".$fun_Id."'";
			else
				$qry = "Insert Into patient_health_status Set ".$qrySet;
			
			$res = imw_query($qry) or $arrMsg[] =imw_error();
		}
	}
	
	foreach($cognitive_id as $key => $cong_Id){
		$txt_functional_status=$txt_functional_ccd_code=$txt_functional_status_date=$txt_operatorId=$txt_entered_date="";
		$txt_functional_status = addslashes($cognitive_status_text[$key]);
		$txt_functional_ccd_code = addslashes($cognitive_ccd_code[$key]);
		$txt_functional_status_date = getDateFormatDB($cognitive_status_date[$key]);
		$txt_operatorId=$_SESSION['authUserID'];
		$txt_entered_date=date("Y-m-d H:i:s");
		if($txt_functional_status)
		{
			$qrySet = "status_type='cognitive',patient_id = '".$txt_patient_id."', form_id= '".$txt_form_id."', status_text= '".$txt_functional_status."', ccd_code = '".$txt_functional_ccd_code."',ccd_code_system='SNOMED-CT', entered_by = '".$txt_entered_date."', status_date = '".$txt_functional_status_date."' ";
		
			if( $cong_Id )
				$qry = "Update patient_health_status Set ".$qrySet. " Where id = '".$cong_Id."'";
			else
				$qry = "Insert Into patient_health_status Set ".$qrySet;
			
			$res = imw_query($qry) or $arrMsg[] =imw_error();
		}
	}
	
	
	return print_health_status();
}
function del_pt_health_status($record_id)
{
	$record_id = (int) $record_id;
	if( $record_id )
	{
		$qry = "Update patient_health_status Set del_status = 1 Where id = ".$record_id." ";
		$sql = imw_query($qry);
		if( $sql )
			$msg = '1_Record deleted successfully';
		else
			$msg = '0_There is a problem while deleting record(s).';
	}
	else
		$msg = '0_There is a problem while deleting record(s).';
	
	return $msg;
}
function print_goals()
{
	$goalFormId = !isset($_SESSION['form_id']) ? $_SESSION['finalize_id'] :  $_SESSION['form_id'];
	$patient_id = $_SESSION['patient'];
	$goalQry = "Select * From patient_goals Where patient_id = '".$patient_id."' And form_id = '".$goalFormId."' And delete_status = 0 Order By id Asc";
	$goalData = get_array_records_query($goalQry);
	$goalRows = (count($goalData) >= 4) ? (count($goalData)+1) : 4;

	$html = '';
	
	$html .= '<div class="col-xs-12 ">';
	$html .= '<div class="headinghd">';
	$html .= '<h4 style="margin-top:5px; margin-bottom:5px;"><b>Goals</b> <span class="pull-right pointer glyphicon glyphicon-plus" id="add_goal_btn" data-rows="'.$goalRows.'" onclick="add_goals(this)" style="margin-right:20px;"></span></h4>';
	$html .= '</div>';
	$html .= '</div>';
	
	$html .= '<div class="col-xs-12">';
	
	$html .= '<div id="goal_header" style="padding-right:16px;">';
	$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0">';
	$html .= '<thead class="grythead">';
	$html .= '<tr>';
	$html .= '<td class="col-xs-3">Goal</td>';
	$html .= '<td class="col-xs-1">Code</td>';
	$html .= '<td class="col-xs-1">Type</td>';
	$html .= '<td class="col-xs-3">Value</td>';
	$html .= '<td class="col-xs-1">Unit</td>';
	$html .= '<td class="col-xs-1">Operator</td>  ';
	$html .= '<td style="width:10%">Date</td>  ';
	$html .= '<td style="width:3%">&nbsp;</td>  ';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '</table>';
	$html .= '</div>';
	
	$html .= '<div class="col-xs-12 pd0" style="max-height:140px; min-height:140px; overflow:hidden; overflow-y:auto;">';
	$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0" style="table-layout:fixed;">';
	$html .= '<tbody id="goal_body">';
	
	for( $j=0,$i=1; $j< $goalRows; $j++,$i++)
	{
		$goalData[$j]['goal_date'] = get_date_format($goalData[$j]['goal_date']);
		$delBtn = '';
		if( $i < $goalRows)
			$delBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_goal(\''.$goalData[$j]['id'].'\',\''.$i.'\');"></span>';
		$html .= '<tr id="goal_row_'.$i.'">';
		$html .= '<td class="col-xs-3">';
		$html .= '<input type="hidden" name="goal_id['.$i.']" id="goal_id_'.$i.'" value="'.$goalData[$j]['id'].'" />';
		$html .= '<input type="text" class="form-control" name="goal_set['.$i.']" id="goal_set_'.$i.'" value="'.$goalData[$j]['goal_set'].'" />';
		$html .= '</td>';
		
		$html .= '<td class="col-xs-1">';
		$html .= '<input type="text" class="form-control" name="goal_code['.$i.']" id="goal_code_'.$i.'" value="'.$goalData[$j]['loinc_code'].'" />';
		$html .= '</td>';
		
		$html .= '<td class="col-xs-1">';
		/*$html .= '<select class="selectpicker" data-width="100%" data-container="#selectContainer" data-index="'.$i.'" name="goal_data_type['.$i.']" id="goal_data_type_'.$i.'" onChange="enableField(this);" >';
		$html .= '<option value="text" selected>Text</option>';
		$html .= '<option value="range" '.($goalData[$j]['goal_data_type'] == 'range' ? 'selected' : '').'>Range</option>';
		$html .= '</select>';*/
		$tmpType = ($goalData[$j]['goal_data_type'] == 'plain_text') ? 'Text' : 'Range';
		$html	.=	'<input type="text" class="form-control" name="goal_data_type['.$i.']" id="goal_data_type_'.$i.'" value="'.$tmpType.'" maxlength="5" />';
		$html .= '</td>';
		
		$html .= '<td class="col-xs-3">';
		$html .= '<input type="text" class="form-control" name="goal_data['.$i.']" id="goal_data_'.$i.'" value="'.$goalData[$j]['goal_data'].'" />';
		$html .= '</td>';
		
		$html .= '<td class="col-xs-1">';
		$html .= '<input type="text" class="form-control" name="goal_unit['.$i.']" id="goal_unit_'.$i.'" value="'.$goalData[$j]['gloal_data_type_unit'].'" />';
		$html .= '</td>';
		
		$html .= '<td class="col-xs-1">';
		/*$html .= '<select class="selectpicker" data-width="100%" data-container="#selectContainer" name="goal_opr_id['.$i.']" id="goal_opr_id_'.$i.'"  title="select">';
		$usersArr = get_operators();
		foreach( $usersArr as $userId => $userName)
		{
			$sel = ($goalData[$j]['operator_id'] == $userId) ? 'selected' : '';
			if( !$sel )
				$sel = ($_SESSION['authUserID'] == $userId) ? 'selected' : '';
			
			$html .= '<option value="'.$userId.'" '.$sel.'>'.$userName.'</option>';
		}
		
		$html .= '</select>';*/
		$tmpUserId = ($goalData[$j]['operator_id']) ? $goalData[$j]['operator_id'] : $_SESSION['authUserID'];
		$html	.=	'<input type="hidden" class="form-control" name="goal_opr_id['.$i.']" id="goal_opr_id_'.$i.'" value="'.$tmpUserId.'" />';
		$html .=	'<input type="text" class="form-control" name="goal_opr_name['.$i.']" id="goal_opr_name_'.$i.'" value="'.getUserFirstName($tmpUserId,1).'" readonly />';
		
		$html .= '</td>';
		
		$html .= '<td style="width:10%">';
		$html .= '<div class="input-group">';
		$html .= '<input type="text" class="form-control date-pick" name="goal_date['.$i.']" id="goal_date_'.$i.'" value="'.$goalData[$j]['goal_date'].'" />';
		$html .= '<label class="input-group-addon" for="goal_date_'.$i.'"><i class="glyphicon glyphicon-calendar"></i></label>';
		$html .= '</div>';
		$html .= '</td>';  
		$html .= '<td style="width:3%;" >'.$delBtn.'</td>';
		$html .= '</tr>';		
	}
	
	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div>';
	$html .= '</div>';
	
	$html .= '<div class="col-xs-12 text-center" style="border:0;padding-top:5px;border-top:solid 1px #c0c0c0;">';
	$html .= '<button type="button" class="btn btn-success" name="save_goals" id="save_goals" onClick="top.fmain.save_goals();" >Save Goals</button>';
	$html .= '</div>';
	
	return $html;
	
}

function print_health_status()
{
	$array_functional=$array_cognitive=array();
	$goalFormId = !isset($_SESSION['form_id']) ? $_SESSION['finalize_id'] :  $_SESSION['form_id'];
	$patient_id = $_SESSION['patient'];
	$healthStatusQry = "Select * from patient_health_status Where patient_id = '".$patient_id."'  And del_status = 0 Order By id Asc";
	$healthStatusData = get_array_records_query($healthStatusQry);
	
	//pre($healthStatusData);
	foreach($healthStatusData as $key_val => $healthStatusArrVal){
		if($healthStatusArrVal['status_type']=="functional"){
			$array_functional[]=$healthStatusArrVal;
		}else if($healthStatusArrVal['status_type']=="cognitive"){
			$array_cognitive[]=$healthStatusArrVal;
		}
	}
	$array_functional_rows=(count($array_functional) >= 4)? (count($array_functional)+1):4;
	$array_cognitive_rows=(count($array_cognitive) >= 4)? (count($array_cognitive)+1):4;
	//pre($array_functional);
	//pre($array_cognitive);
	$html = '';
	
	$html .= '<div class="col-xs-12 ">';
	$html .= '<div class="headinghd">';
	$html .= '<h4 style="margin-top:5px; margin-bottom:5px;"><b>Functional</b> <span class="pull-right pointer glyphicon glyphicon-plus" id="add_hs_btn_functional" data-rows="'.$array_functional_rows.'" onclick="add_health_status(\'functional\')" style="margin-right:20px;"></span></h4>';
	$html .= '</div>';
	$html .= '</div>';
	
	$html .= '<div class="col-xs-12">';
	
	$html .= '<div id="goal_header" style="padding-right:16px;">';
	$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0">';
	$html .= '<thead class="grythead">';
	$html .= '<tr>';
	$html .= '<td class="col-xs-3">Status</td>';
	$html .= '<td class="col-xs-1">Code</td>';
	$html .= '<td style="width:10%">Date</td>  ';
	$html .= '<td style="width:3%">&nbsp;</td>  ';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '</table>';
	$html .= '</div>';
	
	$html .= '<div class="col-xs-12 pd0" style="max-height:140px; min-height:140px; overflow:hidden; overflow-y:auto;">';
	$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0" style="table-layout:fixed;">';
	$html .= '<tbody id="goal_body">';
	
	
	
	for( $j=0,$i=1; $j< $array_functional_rows; $j++,$i++){
		$array_functional[$j]['status_date'] = get_date_format($array_functional[$j]['status_date']);
		$delBtn = '';
		if( $i < $array_functional_rows){
			$delBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="del_pt_health_status(\''.$array_functional[$j]['id'].'\',\''.$i.'\',\'functional\');"></span>';
		}
		$html .= '<tr id="hs_row_functional'.$i.'">';
		$html .= '<td class="col-xs-3">';
		$html .= '<input type="hidden" name="functional_id['.$i.']" id="functional_id_'.$i.'" value="'.$array_functional[$j]['id'].'" />';
		$html .= '<input type="text" class="form-control" name="functional_status_text['.$i.']" id="functional_status_text_'.$i.'" value="'.$array_functional[$j]['status_text'].'" />';
		$html .= '</td>';
		
		$html .= '<td class="col-xs-1">';
		$html .= '<input type="text" class="form-control" name="functional_ccd_code['.$i.']" id="functional_ccd_code_'.$i.'" value="'.$array_functional[$j]['ccd_code'].'" />';
		$html .= '</td>';
		
		
		$html .= '<td style="width:10%">';
		$html .= '<div class="input-group">';
		$html .= '<input type="text" class="form-control date-pick" name="functional_status_date['.$i.']" id="functional_status_date_'.$i.'" value="'.$array_functional[$j]['status_date'].'" />';
		$html .= '<label class="input-group-addon" for="functional_status_date_'.$i.'"><i class="glyphicon glyphicon-calendar"></i></label>';
		$html .= '</div>';
		$html .= '</td>';  
		$html .= '<td style="width:3%;" >'.$delBtn.'</td>';
		$html .= '</tr>';		
	}
	
	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div>';
	$html .= '</div>';
	
	
	
	$html .= '<div class="col-xs-12 ">';
	$html .= '<div class="headinghd">';
	$html .= '<h4 style="margin-top:5px; margin-bottom:5px;"><b>Cognitive</b> <span class="pull-right pointer glyphicon glyphicon-plus" id="add_hs_btn_cognitive" data-rows="'.$array_cognitive_rows.'" onclick="add_health_status(\'cognitive\')" style="margin-right:20px;"></span></h4>';
	$html .= '</div>';
	$html .= '</div>';
	
	$html .= '<div class="col-xs-12">';
	
	$html .= '<div id="goal_header" style="padding-right:16px;">';
	$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0">';
	$html .= '<thead class="grythead">';
	$html .= '<tr>';
	$html .= '<td class="col-xs-3">Status</td>';
	$html .= '<td class="col-xs-1">Code</td>';
	$html .= '<td style="width:10%">Date</td>  ';
	$html .= '<td style="width:3%">&nbsp;</td>  ';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '</table>';
	$html .= '</div>';
	
	$html .= '<div class="col-xs-12 pd0" style="max-height:140px; min-height:140px; overflow:hidden; overflow-y:auto;">';
	$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0" style="table-layout:fixed;">';
	$html .= '<tbody id="goal_body">';
	
	
	
	for( $j=0,$i=1; $j< $array_cognitive_rows; $j++,$i++){
		$array_cognitive[$j]['status_date'] = get_date_format($array_cognitive[$j]['status_date']);
		$delBtn = '';
		if( $i < $array_cognitive_rows){
			$delBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="del_pt_health_status(\''.$array_cognitive[$j]['id'].'\',\''.$i.'\',\'cognitive\');"></span>';
		}
		$html .= '<tr id="hs_row_cognitive'.$i.'">';
		$html .= '<td class="col-xs-3">';
		$html .= '<input type="hidden" name="cognitive_id['.$i.']" id="cognitive_id_'.$i.'" value="'.$array_cognitive[$j]['id'].'" />';
		$html .= '<input type="text" class="form-control" name="cognitive_status_text['.$i.']" id="cognitive_status_text_'.$i.'" value="'.$array_cognitive[$j]['status_text'].'" />';
		$html .= '</td>';
		
		$html .= '<td class="col-xs-1">';
		$html .= '<input type="text" class="form-control" name="cognitive_ccd_code['.$i.']" id="cognitive_ccd_code_'.$i.'" value="'.$array_cognitive[$j]['ccd_code'].'" />';
		$html .= '</td>';
		
		
		$html .= '<td style="width:10%">';
		$html .= '<div class="input-group">';
		$html .= '<input type="text" class="form-control date-pick" name="cognitive_status_date['.$i.']" id="cognitive_status_date_'.$i.'" value="'.$array_cognitive[$j]['status_date'].'" />';
		$html .= '<label class="input-group-addon" for="cognitive_status_date_'.$i.'"><i class="glyphicon glyphicon-calendar"></i></label>';
		$html .= '</div>';
		$html .= '</td>';  
		$html .= '<td style="width:3%;" >'.$delBtn.'</td>';
		$html .= '</tr>';		
	}
	
	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div>';
	$html .= '</div>';
	
	
	
	
	
	$html .= '<div class="col-xs-12 text-center" style="border:0;padding:10px 0 10px 0;border-top:solid 1px #c0c0c0;">';
	$html .= '<button type="button" class="btn btn-success" name="save_pt_health_status" id="save_pt_health_status" onClick="top.fmain.save_health_status();" >Save Health Status</button>';
	$html .= '</div>';
	
	return $html;
	
}


function save_health_concern(&$request)
{
	extract($request);
	$txt_patient_id = $_SESSION['patient'];
	$txt_form_id = !isset($_SESSION['form_id']) ? $_SESSION['finalize_id'] :  $_SESSION['form_id'];
	$txt_operatorId = $_SESSION[''];
	$c_date_time = date('Y-m-d H:i:s');	
	$arrMsg = array();	
	foreach($obs_id as $key => $obsId)	
	{
		$txt_observation = addslashes($observation[$key]);
		$txt_obsStatus = strtolower(trim($obs_status[$key]));
		if( !in_array($txt_obsStatus,array('active','deactive','inactive','completed')))
			$txt_obsStatus = 'active';
		$txt_obsDate = getDateFormatDB($obs_date[$key]);
		$txt_obsCode = addslashes($obs_code[$key]);
		$txt_obstime = $obs_time[$key];
		$db_ob_type = $ob_type[$key];
        
        $db_refusal = $refusal[$key];
		$db_refusal_reason = $refusal_reason[$key];
		$db_refusal_snomed = $refusal_snomed[$key];
        if($refusal_snomed[$key] == '') {
            $db_refusal =0;
            $db_refusal_reason = '';
            $db_refusal_snomed = '';
        }
		if( $txt_observation )
		{ 
			$qrySet = "pt_id = '".$txt_patient_id."', form_id= '".$txt_form_id."', observation_date = '".$txt_obsDate."', observation_time = '".$txt_obstime."', observation = '".$txt_observation."', status = '".$txt_obsStatus."', operator_id = '".$_SESSION['authUserID']."', snomed_code = '".$txt_obsCode."', refusal = '".$db_refusal."', refusal_reason = '".$db_refusal_reason."', refusal_snomed = '".$db_refusal_snomed."', type = '".$db_ob_type."'  ";
			
			if( $obsId )
				$qry = "Update hc_observations Set ".$qrySet. ", modified_date_time = '".$c_date_time."'  Where id = '".$obsId."'";
			else
				$qry = "Insert Into hc_observations Set ".$qrySet.", entry_date_time = '".$c_date_time."'";
			
			$res = imw_query($qry) or $arrMsg[] =imw_error();
			$insert_id = ($res) ? imw_insert_id() : 0;
			
			$recordId = $obsId ? $obsId : $insert_id;
			
			if( $recordId > 0 )
			{
				$conIndex = 'con_id_'.$key;
				$tmpArr = $$conIndex; 
				foreach($tmpArr as $conKey => $conId)
				{
					$concern = 'concern_'.$key;
					$con_status = 'con_status_'.$key;
					$con_date = 'con_date_'.$key;
					
					$txt_concern = addslashes($request[$concern][$conKey]);
					$txt_conStatus = strtolower(trim($request[$con_status][$conKey]));
					if( !in_array($txt_conStatus,array('active','deactive','inactive','completed')))
						$txt_conStatus = 'active';
					$txt_conDate = getDateFormatDB($request[$con_date][$conKey]);
					
					if( $txt_concern )
					{
						$qrySetCon = "concern_date = '".$txt_conDate."', concern = '".$txt_concern."', status = '".$txt_conStatus."', operator_id = '".$_SESSION['authUserID']."', observation_id= '".$recordId."' ";
						
						if( $conId )
							$qryCon = "Update hc_concerns Set ".$qrySetCon. ", modified_date_time = '".$c_date_time."'  Where id = '".$conId."'";
						else
							$qryCon = "Insert Into hc_concerns Set ".$qrySetCon.", entry_date_time = '".$c_date_time."'";
						
						$resCon = imw_query($qryCon) or $arrMsg[] =imw_error();
					}
					
				}
			
			
				$relIndex = 'rel_id_'.$key;
				foreach($$relIndex as $relKey => $relId)
				{
					$rel_observation = 'rel_observation_'.$key;
					$rel_date = 'rel_observation_date_'.$key;
					$rel_code = 'rel_code_'.$key;
					
					$txt_relObservation= addslashes($request[$rel_observation][$relKey]);
					$txt_relDate = getDateFormatDB($request[$rel_date][$relKey]);
					$txt_relCode= addslashes($request[$rel_code][$relKey]);
					
					if( $txt_relObservation )
					{
						$qrySetRel = "rel_observation_date = '".$txt_relDate."', rel_observation = '".$txt_relObservation."', operator_id = '".$_SESSION['authUserID']."', observation_id= '".$recordId."', snomed_code = '".$txt_relCode."' ";
						
						if( $relId )
							$qryRel = "Update hc_rel_observations Set ".$qrySetRel. ", modified_date_time = '".$c_date_time."'  Where id = '".$relId."'";
						else
							$qryRel = "Insert Into hc_rel_observations Set ".$qrySetRel.", entry_date_time = '".$c_date_time."'";
						
						$resRel = imw_query($qryRel) or $arrMsg[] =imw_error();
					}
		
				}
			
			}
		
		}
		
		
		
	}
	
	return print_health_concerns();
}

function del_health_concern($type, $record_id)
{
	$record_id = (int) $record_id;
	$array = array('obs' => 'hc_observations', 'con' => 'hc_concerns', 'rel' => 'hc_rel_observations');
	$table = $array[$type];
	
	if( $table && $record_id )
	{
		$qry = "Update ".$table." Set del_status = 1 Where id = ".$record_id." ";
		$sql = imw_query($qry);
		if( $sql )
			$msg = '1_Record deleted successfully';
		else
			$msg = '0_There is a problem while deleting record(s).';
	}
	else
		$msg = '0_There is a problem while deleting record(s).';
	
	return $msg;
	
	
}

function print_health_concerns()
{
	
		// Start Collecting Data 
	$goalFormId = !isset($_SESSION['form_id']) ? $_SESSION['finalize_id'] :  $_SESSION['form_id'];
	$patient_id = $_SESSION['patient'];
	$qry = "Select obs.*, hcc.concern, hcc.concern_date, hcc.status as c_status, hcc.id as concern_id, 
								 hcc.del_status as con_del_status, 
								 rel.snomed_code as rel_code, rel.rel_observation_date, rel.rel_observation, 
								 rel.id as rel_id, rel.del_status as rel_del_status 
								 From hc_observations obs 
								 LEFT JOIN hc_concerns hcc ON obs.id = hcc.observation_id 
								 LEFT JOIN hc_rel_observations rel ON obs.id = rel.observation_id 
								 Where obs.pt_id = '".$patient_id."' 
								 And obs.form_id = '".$goalFormId."' 
								 And obs.del_status = 0 
								 Order By obs.observation_date Desc";
	$sql = imw_query($qry);
	$cnt = imw_num_rows($sql);
	$data = array();
	if( $cnt > 0 )
	{ 
		while( $row = imw_fetch_assoc($sql))
		{
			$obs_data = $concern_data = $rel_data = array();
			
			$obs_data['id'] = $row['id'];
			$obs_data['observation_date'] = $row['observation_date'];
			$obs_data['observation_time'] = $row['observation_time'];
			$obs_data['observation'] = $row['observation'];
			$obs_data['status'] = $row['status'];
			$obs_data['snomed_code'] = $row['snomed_code'];
			$obs_data['type'] = $row['type'];
			$obs_data['refusal'] = $row['refusal'];
			$obs_data['refusal_reason'] = $row['refusal_reason'];
			$obs_data['refusal_snomed'] = $row['refusal_snomed'];
			$data[$row['id']]['observation_data']	= $obs_data;
			
			if( $row['con_del_status'] == 0)
			{
				$concern_data['concern_date'] = $row['concern_date'];
				$concern_data['concern'] = $row['concern'];
				$concern_data['status'] = $row['c_status'];
				$data[$row['id']]['concern_data'][$row['concern_id']]	= $concern_data;
			}
			
			if( $row['rel_del_status'] == 0)
			{
				$rel_data['rel_observation_date'] = $row['rel_observation_date'];
				$rel_data['rel_observation'] = $row['rel_observation'];
				$rel_data['rel_code'] = $row['rel_code'];
				$data[$row['id']]['rel_data'][$row['rel_id']]	= $rel_data;
			}
			
		}
	}
	
	// End Collecting Data 
	
	$css = 'style="padding:5px; margin-top:10px; border:solid 1px #c0c0c0; box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 2px 0.5px;"';
	
	$html = '';
	
	// Start Header Printing
	//<span class="pull-right pointer glyphicon glyphicon-plus" id="add_hc_btn" data-rows="'.$rows.'" onclick="add_hc(this)" style="margin-right:20px;"></span>
	$html .= '<div class="col-xs-12 ">';
	$html .= '<div class="headinghd">';
	$html .= '<h4 style="margin-top:5px; margin-bottom:5px;"><b>Health Concern</b></h4>';
	$html .= '</div>';
	$html .= '</div>';
	
	$html .= '<div class="pd5" style="min-height: 200px; max-height: 200px; overflow: hidden; overflow-y: auto;">';
	
	$counter = 0;
	foreach( $data as $obsId => $tmpArr)
	{
		$counter++;
		$obsData = $tmpArr['observation_data'];
		$conData = $tmpArr['concern_data'];
		$relData = $tmpArr['rel_data'];
		
		$delObsBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_hc(\'obs\',\''.$obsId.'\',\''.$counter.'\',\'\');"></span>';
			
		$html .= '<div id="hc_row_'.$counter.'" class="col-xs-12 mt5">';
		$html .= '<div class="col-xs-12" '.$css.'>';
		
		$html .= '<div id="obs_row_'.$counter.'" class="col-xs-12" >';
		$html .= temp_observation($counter, $obsData, $obsId, $delObsBtn);
		$html .= '</div>';
		
		$html .= '<div id="obs_child_'.$counter.'" class="col-xs-12" >';
		$html .= '<div class="row" >';
		
		// Concerns
		$html .= '<div class="col-xs-12 col-sm-7" >';
		$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0">';
		
		$html .= '<thead class="grythead">';
		$html .= '<tr>';
		$html .= '<td class="col-xs-7">Concern</td>';
		$html .= '<td class="col-xs-2">Status</td>';
		$html .= '<td class="col-xs-2">Date</td>';
		$html .= '<td style="width:3%">&nbsp;</td>  ';
		$html .= '</tr>';
		$html .= '</thead>';
		
		$html .= '<tbody">';
		$ccnt = 0;
		foreach($conData as $conId => $conArr)
		{
			$ccnt++;
			$delConBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_hc(\'con\',\''.$conId.'\',\''.$ccnt.'\',\''.$counter.'\');"></span>';
			$html .= temp_concern($ccnt, $conArr, $conId, $counter,$delConBtn);
		}
		
		$maxConSubRows = $ccnt + 3;
		$ccnt++;
		for($con_i = $ccnt; $con_i < $maxConSubRows; $con_i++)
		{
			$ccnt = $con_i;
			$delConBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_hc(\'con\',\'\',\''.$ccnt.'\',\''.$counter.'\');"></span>';
			if( $ccnt == ($maxConSubRows-1))
				$delConBtn = '';
			$html .= blank_con($ccnt, $counter, $delConBtn);
		}
		
		$html .= '</tbody>';
		$html .= '</table>';
		
		$html .= '</div>'; // End Concern Information
		
		// Related Observation
		$html .= '<div class="col-xs-12 col-sm-5" >';
		$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0">';
		
		$html .= '<thead class="grythead">';
		$html .= '<tr>';
		$html .= '<td class="col-xs-5">Related Observation</td>';
		$html .= '<td class="col-xs-3">Code</td>';
		$html .= '<td class="col-xs-3">Date</td>';
		$html .= '<td style="width:3%">&nbsp;</td>  ';
		$html .= '</tr>';
		$html .= '</thead>';
		
		$html .= '<tbody">';
		$rcnt = 0;
		foreach($relData as $relId => $relArr)
		{
			$rcnt++;
			$delRelBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_hc(\'rel\',\''.$relId.'\',\''.$rcnt.'\',\''.$counter.'\');"></span>';
			$html .= temp_rel_observation($rcnt, $relArr, $relId, $counter,$delRelBtn);
		}
		
		$maxRelSubRows = $rcnt + 3;
		$rcnt++;
		for($rel_i = $rcnt; $rel_i < $maxRelSubRows; $rel_i++)
		{
			$rcnt = $rel_i;
			$delRelBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_hc(\'rel\',\'\',\''.$rcnt.'\',\''.$counter.'\');"></span>';
			if( $rcnt == ($maxRelSubRows-1))
				$delRelBtn = '';
			$html .= blank_rel($rcnt, $counter, $delRelBtn);
		}
		
		$html .= '</tbody>';
		$html .= '</table>';
		
		$html .= '</div>'; // End Related Observation
		
		$html .= '</div>'; 
		$html .= '</div>'; //End Child Row
		
		$html .= '</div>';
		$html .= '</div>'; // End Row
		
	}
	
	$maxRows = $counter + 5;
	$counter++;
	$html .= blank_hc($counter,$maxRows);
	
	$html .= '</div>';
	return $html;
	
}

function temp_observation($counter = 1, $obsData = array(), $obsId, $delBtn = '' )
{
	$html = '';
	$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0">';
		
	$html .= '<thead class="grythead">';
	$html .= '<tr>';
	$html .= '<td class="col-xs-3">Observation</td>';
	$html .= '<td class="col-xs-2">Type</td>';
	$html .= '<td class="col-xs-2">Code</td>';
	$html .= '<td class="col-xs-1">Status</td>';
	$html .= '<td class="col-xs-2">Date</td>';
	$html .= '<td style="width:3%">&nbsp;</td>  ';
	$html .= '<td style="width:3%">&nbsp;</td>  ';
	$html .= '</tr>';
	$html .= '</thead>';
		
	$html .= '<tbody">';
	$html .= '<tr>';
		
	$html .= '<td class="col-xs-4">';
	$html .= '<input type="hidden" name="obs_id['.$counter.']" id="obs_id_'.$counter.'" value="'.$obsData['id'].'" />';
	$html .= '<input type="text" class="form-control" name="observation['.$counter.']" id="observation_'.$counter.'" value="'.$obsData['observation'].'" />';
	$html .= '</td>';
    
	$html .= '<td class="col-xs-2">';
	$html .= '<select class="form-control minimal" name="ob_type['.$counter.']" id="ob_type_'.$counter.'">';
	$html .= '<option value="">-- Select --</option>';
	$html .= '<option value="1" '.($obsData['type']==1? 'selected' : '').' >Assessment</option>';
	$html .= '<option value="0" '.($obsData['type']==0? 'selected' : '').' >Diagnosis</option>';
	$html .= '</select>';
	$html .= '</td>';
	
	$html .= '<td class="col-xs-2">';
	$html .= '<input type="text" class="form-control" name="obs_code['.$counter.']" id="obs_code_'.$counter.'" value="'.ucwords($obsData['snomed_code']).'" />';
	$html .= '</td>';
			
	$html .= '<td class="col-xs-1">';
	$html .= '<input type="text" class="form-control" name="obs_status['.$counter.']" id="obs_status_'.$counter.'" value="'.ucwords($obsData['status']).'" />';
	$html .= '</td>';
		
	$html .= '<td class="col-xs-1">';
	$html .= '<div class="col-sm-8"><div class="input-group">';
	$html .= '<input type="text" class="form-control  date-pick" name="obs_date['.$counter.']" id="obs_date_'.$counter.'" value="'.get_date_format($obsData['observation_date']).'" />';
	$html .= '<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>';
	$html .= '</div></div>';
	$html .= '<div class="col-sm-4">';
	$html .= '<input type="text" class="form-control" name="obs_time['.$counter.']" id="obs_time_'.$counter.'" value="'.$obsData['observation_time'].'" />';
	$html .= '</div>';
	$html .= '</td>';
    
    $html .= '<td style="width:3%">';
    $html .= '<div class="checkbox">';
    $html .= '<input type="checkbox" class="checkbox" name="refusal['.$counter.']" id="refusal'.$counter.'"  '.(($obsData['refusal'] == 1) ? 'checked' : '') .' value="'.$obsData['refusal'].'" onClick="check_refusal(\''.$counter.'\'); ">';
    $html .= '<label for="refusal'.$counter.'">&nbsp;</label>';
    $html .= '</div>';
    $html .= '<input type="hidden" name="refusal_reason['.$counter.']" id="refusal_reason'.$counter.'" value="'.$obsData['refusal_reason'].'">';
    $html .= '<input type="hidden" name="refusal_snomed['.$counter.']" id="refusal_snomed'.$counter.'" value="'.$obsData['refusal_snomed'].'">';
	$html .= '</td>';
	
	$html .= '<td style="width:3%">'.$delBtn.'</td>';
	$html .= '</tr>';
	$html .= '</tbody>';
	
	$html .= '</table>';
	$html .= '<span class="hc_modal"><div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">
			<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Refusal Reason</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" name="refusal_row" id="refusal_row" value="" >
						<input type="hidden" name="rowID" id="rowID" value="" >
						<div class="form-group">
							<label for="usrname">Refusal Reason</label>
							<textarea type="text" class="form-control"  id="refusal_reason" name="refusal_reason"></textarea>
						</div>
						<div class="form-group">
							<label for="psw">Refusal Snomed</label>
							<input type="text" class="form-control" id="m_refusal_snomed" name="m_refusal_snomed">
						</div>
					</div>
					<div id="module_buttons" class="ad_modal_footer modal-footer">
						<button type="button" class="btn btn-success" onclick="check_refusal_values();">Save</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		
   		<input type="hidden" name="last_cnt" id="last_cnt" value="'.$counter.'">
      </div></span>';
	return $html;
}

function temp_concern($ccnt = 1, $conData = array(), $conId, $counter = '', $delConBtn = '')
{
	$html .= '';
	$html .= '<tr id="con_'.$counter.'_'.$ccnt.'">';
	$html .= '<td class="col-xs-7">';
	$html .= '<input type="hidden" name="con_id_'.$counter.'['.$ccnt.']" id="con_id_'.$counter.'_'.$ccnt.'" value="'.$conId.'" />';
	$html .= '<input type="text" class="form-control" name="concern_'.$counter.'['.$ccnt.']" id="concern_'.$counter.'_'.$ccnt.'" value="'.$conData['concern'].'" />';
	$html .= '</td>';
	
	$html .= '<td class="col-xs-2">';
	$html .= '<input type="text" class="form-control" name="con_status_'.$counter.'['.$ccnt.']" id="con_status_'.$counter.'_'.$ccnt.'" value="'.ucwords($conData['status']).'" />';
	$html .= '</td>';
		
	$html .= '<td class="col-xs-2">';
	$html .= '<div class="input-group">';
	$html .= '<input type="text" class="form-control date-pick" name="con_date_'.$counter.'['.$ccnt.']" id="con_date_'.$counter.'_'.$ccnt.'" value="'.get_date_format($conData['concern_date']).'" />';
	$html .= '<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>';
	$html .= '</div>';
	$html .= '</td>';
	
	$html .= '<td style="width:3%">'.$delConBtn.'</td>';
	$html .= '</tr>';
		
	return $html;
}

function temp_rel_observation($rcnt = 1, $relData = array(), $relId, $counter = '', $delRelBtn = '')
{
	$html = '';
	
	$html .= '<tr id="rel_'.$counter.'_'.$rcnt.'">';
	$html .= '<td class="col-xs-5">';
	$html .= '<input type="hidden" name="rel_id_'.$counter.'['.$rcnt.']" id="rel_id_'.$counter.'_'.$rcnt.'" value="'.$relId.'" />';
	$html .= '<input type="text" class="form-control" name="rel_observation_'.$counter.'['.$rcnt.']" id="rel_observation_'.$counter.'_'.$rcnt.'" value="'.$relData['rel_observation'].'" />';
	$html .= '</td>';
	
	$html .= '<td class="col-xs-3">';
	$html .= '<input type="text" class="form-control" name="rel_code_'.$counter.'['.$rcnt.']" id="rel_code_'.$counter.'_'.$rcnt.'" value="'.$relData['rel_code'].'" />';
	$html .= '</td>';
	
	$html .= '<td class="col-xs-2">';
	$html .= '<div class="input-group">';
	$html .= '<input type="text" class="form-control date-pick" name="rel_observation_date_'.$counter.'['.$rcnt.']" id="rel_observation_date_'.$counter.'_'.$rcnt.'" value="'.get_date_format($relData['rel_observation_date']).'" />';
	$html .= '<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>';
	$html .= '</div>';
	$html .= '</td>';
	
	$html .= '<td style="width:3%">'.$delRelBtn.'</td>';
	$html .= '</tr>';
	
	return $html;
}

function blank_hc($start = 1, $end = 2)
{
	$html = '';
	$css = 'style="padding:5px; margin-top:10px; border:solid 1px #c0c0c0; box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 2px 0.5px;"';
	
	for( $i = $start; $i < $end; $i++)
	{
		$counter = $i;
		
		$delObsBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_hc(\'obs\',\'\',\''.$counter.'\',\'\');"></span>';
		if( $i == ($end - 1))
			$delObsBtn = '';	
		
		$html .= '<div id="hc_row_'.$counter.'" class="col-xs-12">';
		$html .= '<div class="col-xs-12" '.$css.'>';
		
		$html .= '<div id="obs_row_'.$counter.'" class="col-xs-12" >';
		$html .= temp_observation($counter, array(), '', $delObsBtn);
		$html .= '</div>';
		
		$html .= '<div id="obs_child_'.$counter.'" class="col-xs-12" >';
		$html .= '<div class="row" >';
		
		// Concerns
		$html .= '<div class="col-xs-12 col-sm-7" >';
		$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0">';
		
		$html .= '<thead class="grythead">';
		$html .= '<tr>';
		$html .= '<td class="col-xs-7">Concern</td>';
		$html .= '<td class="col-xs-2">Status</td>';
		$html .= '<td class="col-xs-2">Date</td>';
		$html .= '<td style="width:3%">&nbsp;</td>  ';
		$html .= '</tr>';
		$html .= '</thead>';
		
		$html .= '<tbody">';
		
		$maxSubRows = 3;
		for($con_i = 1; $con_i < $maxSubRows; $con_i++)
		{
			$ccnt = $con_i;
			$delConBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_hc(\'con\',\''.$conId.'\',\''.$ccnt.'\',\''.$counter.'\');"></span>';
			if( $ccnt == ($maxSubRows-1))
				$delConBtn = '';
			$html .= blank_con($ccnt, $counter, $delConBtn);
		}
		
		$html .= '</tbody>';
		$html .= '</table>';
		
		$html .= '</div>'; // End Concern Information
		
		// Related Observation
		$html .= '<div class="col-xs-12 col-sm-5" >';
		$html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0">';
		
		$html .= '<thead class="grythead">';
		$html .= '<tr>';
		$html .= '<td class="col-xs-6">Related Observation</td>';
		$html .= '<td class="col-xs-2">Code</td>';
		$html .= '<td class="col-xs-3">Date</td>';
		$html .= '<td style="width:3%">&nbsp;</td>  ';
		$html .= '</tr>';
		$html .= '</thead>';
		
		$html .= '<tbody">';
		
		for($rel_i = 1; $rel_i < $maxSubRows; $rel_i++)
		{
			$rcnt = $rel_i;
			$delRelBtn = '<span class="glyphicon glyphicon-remove pointer" onclick="delete_hc(\'rel\',\'\',\''.$rcnt.'\',\''.$counter.'\');"></span>';
			if( $rcnt == ($maxSubRows-1))
				$delRelBtn = '';
			$html .= blank_rel($rcnt,$counter,$delRelBtn);
			
		}
		
		$html .= '</tbody>';
		$html .= '</table>';
		
		$html .= '</div>'; // End Related Observation
		
		$html .= '</div>'; 
		$html .= '</div>'; //End Child Row
		
		$html .= '</div>'; 
		$html .= '</div>'; // End Row
		
	}
	
	return $html;
}

function blank_con($ccnt,$counter,$delConBtn)
{
	return temp_concern($ccnt, array(), '', $counter, $delConBtn);
}

function blank_rel($rcnt,$counter,$delRelBtn)
{
	return temp_rel_observation($rcnt, array(), '', $counter, $delRelBtn);
}


function save_inpatient(&$request)
{
	extract($request);
	$patient_id = $_SESSION['patient'];
	$form_id = !isset($_SESSION['form_id']) ? $_SESSION['finalize_id'] :  $_SESSION['form_id'];

    $PrincipalDiag = $PrincipalDiag?$PrincipalDiag:'';
    $DischargeCode = $DischargeCode?$DischargeCode:'';

    $PrincipalqrySet = "appt_id = '".$appt_id."', field_type='PrincipalDiag', field_codesystem= '2.16.840.1.113883.6.90', field_code= '".$PrincipalDiag."' ";

    if( $Principalfield_id ) {
        $qry = "Update inpatient_fields Set ".$PrincipalqrySet. "  Where id = '".$Principalfield_id."'";
    }  else {
        $qry = "Insert Into inpatient_fields Set ".$PrincipalqrySet." ";
    }
    $res = imw_query($qry) or $arrMsg[] =imw_error();
    $insert_id = ($res) ? imw_insert_id() : 0;
    
    $DischargeqrySet = "appt_id = '".$appt_id."', field_type='DischargeCode', field_codesystem= '2.16.840.1.113883.6.96', field_code= '".$DischargeCode."' ";
    if ($Dischargefield_id) {
        $qry = "Update inpatient_fields Set ".$DischargeqrySet. "  Where id = '".$Dischargefield_id."'";
    } else {
        $qry = "Insert Into inpatient_fields Set ".$DischargeqrySet." ";
    }
    $res = imw_query($qry) or $arrMsg[] =imw_error();
    $insert_id = ($res) ? imw_insert_id() : 0;
    
    return print_inpatient();
}

function print_inpatient() {
    $formId = !isset($_SESSION['form_id']) ? $_SESSION['finalize_id'] :  $_SESSION['form_id'];
	$pid = $_SESSION['patient'];
    $data = array();
    $sql = "SELECT appt.id, appt.sa_app_start_date,sa_app_end_date,appt.sa_app_starttime,appt.sa_app_endtime, appt.is_inpatient
                    FROM schedule_appointments appt 
                    WHERE appt.sa_patient_id in('" . $pid . "')
			AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3)";
    $enc_qry = imw_query($sql);
    while ($enc_row = imw_fetch_array($enc_qry)) {
        $inp_qry = "Select * From inpatient_fields Where appt_id = '".$enc_row['id']."' Order By field_type ";
        $inp_sql = imw_query($inp_qry);
        $inp_cnt = imw_num_rows($inp_sql);
        if( $inp_cnt ) {
            while( $inp_row = imw_fetch_assoc($inp_sql) ) {
               $data[$inp_row['field_type']] = $inp_row;
            }
        }
    }

    $html .= '';
    $html .= '<table class="table table-bordered table-hover table-striped scroll release-table margin_0" style="min-height:140px;">';

    $html .= '<thead class="grythead">';
    $html .= '<tr>';
    $html .= '<td class="col-xs-6">Discharge Disposition Code</td>';
    $html .= '<td class="col-xs-6">Principal Diagnosis Code</td>';
    $html .= '</tr>';
    $html .= '</thead>';

    $html .= '<tbody">';
	$html .= '<tr>';
    
    $html .= '<td class="col-xs-6">';
    $html .= '<input type="text" class="form-control" name="DischargeCode" id="DischargeCode" value="' . ($data['DischargeCode']['field_code'] ? $data['DischargeCode']['field_code'] : '').'" />';
    $html .= '</td>';

    $html .= '<td class="col-xs-6">';
    $html .= '<input type="text" class="form-control" name="PrincipalDiag" id="PrincipalDiag" value="' . ($data['PrincipalDiag']['field_code'] ? $data['PrincipalDiag']['field_code'] : '') . '" />';
    $html .= '</td>';

//    $html .= '<input type="hidden" class="form-control" name="DischargeCodesystem" id="field_codesystem" value="' . $data['DischargeCode']['field_codesystem'] . '" />';
//    $html .= '<input type="hidden" class="form-control" name="PrincipalDiagsystem" id="field_codesystem" value="' . $data['PrincipalDiag']['field_codesystem'] . '" />';
    $html .= '<input type="hidden" class="form-control" name="appt_id" id="appt_id" value="' . (($data['PrincipalDiag']['appt_id']) ? $data['PrincipalDiag']['appt_id'] : $data['DischargeCode']['appt_id']) . '" />';
    $html .= '<input type="hidden" class="form-control" name="Dischargefield_id" id="Dischargefield_id" value="' . $data['DischargeCode']['id'] . '" />';
    $html .= '<input type="hidden" class="form-control" name="Principalfield_id" id="Principalfield_id" value="' . $data['PrincipalDiag']['id'] . '" />';
     
    $html .= '</tr>';
    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
    
}

function print_patient_payer(&$ptId = '', &$formId = ''){
    
	$dos = $ptPayerCode = $effStdt = $effEtdt = $ptValueSet = $ptCodeSet = '';
	$chkDos = imw_query('SELECT date_of_service FROM chart_master_table WHERE id = "'.$formId.'" AND delete_status = 0');
	if(imw_num_rows($chkDos) > 0){
		$rowDos = imw_fetch_assoc($chkDos);
		$dos = $rowDos['date_of_service'];
	}
	
	$chkQry = imw_query('SELECT * FROM patientPayer WHERE formId = "'.$formId.'" AND pid = "'.$ptId.'" AND dos = "'.$dos.'" ');
	if(imw_num_rows($chkQry) > 0){
		$row = imw_fetch_assoc($chkQry);
		$ptPayerCode = $row['valueCode'];
		$effStdt = (empty($row['EffStart']) == false && $row['EffStart'] != '0000-00-00') ? $row['EffStart'] : '';
		$effEtdt = (empty($row['EffEnd']) == false && $row['EffEnd'] != '0000-00-00') ? $row['EffEnd'] : '';
		$ptValueSet = $row['valValueSet'];
		$ptCodeSet = $row['valCodeSet'];
	}
	
	$str = '';
	$chkCodes = imw_query('SELECT * FROM payerdt');
	if(imw_num_rows($chkCodes) > 0){
		while($row11 = imw_fetch_assoc($chkCodes)){
			$selected = ($ptPayerCode == $row11['code']) ? 'selected' : '';
			$str .= '<option value="'.$row11['code'].'!~~~~~!'.$row11['name'].'" '.$selected.'>'.$row11['code'].' - '.$row11['name'].'</option>';
		}
	}
	
	list($stDate,$stTime) = explode(' ', $effStdt);
	list($enDate,$enTime) = explode(' ', $effEtdt);
	
	$stDate = ($stDate == '0000-00-00') ? '' : get_date_format($stDate);
	$enDate = ($enDate == '0000-00-00') ? '' : get_date_format($enDate);
	
	$stTime = ($stTime == '00:00:00') ? '' : $stTime;
	$enTime = ($enTime == '00:00:00') ? '' : $enTime;
	
	$htmlStr = '
		<div class="row">
			<input type="hidden" value="'.$dos.'" name="dateOfService">
			<input type="hidden" value="'.$ptId.'" name="patientId">
			<input type="hidden" value="'.$formId.'" name="formId">
			<input type="hidden" value="'.$ptValueSet.'" name="valValueSet">
			<input type="hidden" value="'.$ptCodeSet.'" name="valCodeSet">
			<div class="col-sm-4">
				<div class="form-group">
					<label>Patient Payer</label>
					<select name="payerCode" id="payerCode" class="selectpicker" data-width="100%" data-live-search="true">
						'.$str.'
					</select>	
				</div>
			</div>
			
			<div class="col-sm-4">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-6">
							<label>Start Date</label>
							<div class="input-group">
								<label for="stDt" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span></label>
								<input type="text" name="stDt" id="stDt" class="form-control datePickIn" value="'.$stDate.'">
							</div>
						</div>
						<div class="col-sm-6">
							<label>Start Time</label>
							<div class="input-group">
								<label for="stTm" class="input-group-addon btn"><span class="glyphicon glyphicon-time"></span></label>
								<input type="text" name="stTm" id="stTm" class="form-control" value="'.$stTime.'">
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-sm-4">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-6">
							<label>End Date</label>
							<div class="input-group">
								<label for="stDt" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span></label>
								<input type="text" name="enDt" id="enDt" class="form-control datePickIn" value="'.$enDate.'">
							</div>
						</div>
						<div class="col-sm-6">
							<label>End Time</label>
							<div class="input-group">
								<label for="stTm" class="input-group-addon btn"><span class="glyphicon glyphicon-time"></span></label>
								<input type="text" name="enTm" id="enTm" class="form-control" value="'.$enTime.'">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	';
	return $htmlStr;
}

function save_patient_payer(&$ptId = '', &$formId = '', &$request = ''){
	$dos = (isset($request['dateOfService']) && empty($request['dateOfService']) == false) ? $request['dateOfService'] : '';
	$patientId = (isset($request['patientId']) && empty($request['patientId']) == false) ? $request['patientId'] : $ptId;
	$formId = (isset($request['formId']) && empty($request['formId']) == false) ? $request['formId'] : $formId;
	$code = (isset($request['payerCode']) && empty($request['payerCode']) == false) ? $request['payerCode'] : '';
	$stDt = (isset($request['stDt']) && empty($request['stDt']) == false && $request['stDt'] != '00-00-0000') ? $request['stDt'] : '0000-00-00';
	$enDt = (isset($request['enDt']) && empty($request['enDt']) == false && $request['enDt'] != '00-00-0000') ? $request['enDt'] : '0000-00-00';
	$stTm = (isset($request['stTm']) && empty($request['stTm']) == false) ? $request['stTm'] : '00:00:00';
	$enTm = (isset($request['enTm']) && empty($request['enTm']) == false) ? $request['enTm'] : '00:00:00';
	$valValueSet = (isset($request['valValueSet']) && empty($request['valValueSet']) == false) ? $request['valValueSet'] : '';
	$valCodeSet = (isset($request['valCodeSet']) && empty($request['valCodeSet']) == false) ? $request['valCodeSet'] : '';
	
	if(empty($code) == false){
		list($valueCode, $displayText) = explode('!~~~~~!', $code);
	}
	
	$startTimeStamp = getDateFormatDB($stDt).' '.$stTm;
	$endTimeStamp = getDateFormatDB($enDt).' '.$enTm;
	
	if(empty($dos)){
		$chkDos = imw_query('SELECT date_of_service FROM chart_master_table WHERE id = "'.$formId.'" AND delete_status = 0');
		if(imw_num_rows($chkDos) > 0){
			$rowDos = imw_fetch_assoc($chkDos);
			$dos = $rowDos['date_of_service'];
		}
	}
	
	$arrFields = array();
	$arrFields['dos'] = $dos;
	$arrFields['formId'] = $formId;
	$arrFields['pid'] = $patientId;
	$arrFields['valueCode'] = $valueCode;
	$arrFields['displayText'] = $displayText;
	$arrFields['payer'] = '48768-6';
	$arrFields['EffStart'] = $startTimeStamp;
	$arrFields['EffEnd'] = $endTimeStamp;
	$arrFields['valValueSet'] = $valValueSet;
	$arrFields['valCodeSet'] = $valCodeSet;
	
	
	//Checking Code in DB
	$counter = 0;
	$chkQry = imw_query('SELECT * FROM patientPayer WHERE formId = "'.$formId.'" AND pid = "'.$patientId.'" AND dos = "'.$dos.'" ');
	if(imw_num_rows($chkQry) > 0){
		$row = imw_fetch_assoc($chkQry);
		$update = false;
		$update = UpdateRecords($row['id'], 'id', $arrFields, 'patientPayer');
		if($update !== false){
			$counter++;
		}
	}else{
		$counter = AddRecords($arrFields,'patientPayer');
	}
	
	$htmlStr = '';
	if($counter > 0){
		$str = '';
		$chkCodes = imw_query('SELECT * FROM payerdt');
		if(imw_num_rows($chkCodes) > 0){
			while($row11 = imw_fetch_assoc($chkCodes)){
				$selected = ($row11['code'] == $valueCode) ? 'selected' : '';
				$str .= '<option value="'.$row11['code'].'!~~~~~!'.$row11['name'].'" '.$selected.'>'.$row11['code'].' - '.$row11['name'].'</option>';
			}
		}
		
		list($stDate,$stTime) = explode(' ', $startTimeStamp);
		list($enDate,$enTime) = explode(' ', $endTimeStamp);
		
		$stDate = ($stDate == '0000-00-00') ? '' : get_date_format($stDate);
		$enDate = ($enDate == '0000-00-00') ? '' : get_date_format($enDate);
		
		$stTime = ($stTime == '00:00:00') ? '' : $stTime;
		$enTime = ($enTime == '00:00:00') ? '' : $enTime;
		
		$htmlStr = '
			<div class="row">
				<input type="hidden" value="'.$dos.'" name="dateOfService">
				<input type="hidden" value="'.$patientId.'" name="patientId">
				<input type="hidden" value="'.$formId.'" name="formId">
				<input type="hidden" value="'.$valValueSet.'" name="valValueSet">
				<input type="hidden" value="'.$valCodeSet.'" name="valCodeSet">
				<div class="col-sm-4">
					<div class="form-group">
						<label>Patient Payer</label>
						<select name="payerCode" id="payerCode" class="selectpicker" data-width="100%" data-live-search="true">
							'.$str.'
						</select>	
					</div>
				</div>
				
				<div class="col-sm-4">
					<div class="form-group">
						<div class="row">
							<div class="col-sm-6">
								<label>Start Date</label>
								<div class="input-group">
									<label for="stDt" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span></label>
									<input type="text" name="stDt" id="stDt" class="form-control datePickIn" value="'.$stDate.'">
								</div>
							</div>
							<div class="col-sm-6">
								<label>Start Time</label>
								<div class="input-group">
									<label for="stTm" class="input-group-addon btn"><span class="glyphicon glyphicon-time"></span></label>
									<input type="text" name="stTm" id="stTm" class="form-control" value="'.$stTime.'">
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="col-sm-4">
					<div class="form-group">
						<div class="row">
							<div class="col-sm-6">
								<label>End Date</label>
								<div class="input-group">
									<label for="stDt" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span></label>
									<input type="text" name="enDt" id="enDt" class="form-control datePickIn" value="'.$enDate.'">
								</div>
							</div>
							<div class="col-sm-6">
								<label>End Time</label>
								<div class="input-group">
									<label for="stTm" class="input-group-addon btn"><span class="glyphicon glyphicon-time"></span></label>
									<input type="text" name="enTm" id="enTm" class="form-control" value="'.$enTime.'">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		';
	}
	return $htmlStr;
	
}

function show_goals_hc(){
	$html = '';
	
	$html .= '<div id="pt_goal_health_modal" class="modal fade" role="dialog">';
	$html .= '<div class="modal-dialog modal-lg" style="width:80%;">';
	
	$html .= '<!-- Modal content-->';
	$html .= '<div class="modal-content">';
	
	$html .= '<div class="modal-header bg-primary">';
	$html .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
	$html .= '<h4 class="modal-title">Goals & Health Concerns</h4>';
	$html .= '</div>';
	
	$html .= '<div class="modal-body pd0">';
	$html .= '<div id="selectContainer" style="position:absolute;"></div>';
	
	$html .= '<div class="row" id="goals_hc_data_div" style="min-height:400px;">';
	
	$html .= '<div class="col-sm-12" id="goalDataDiv">'.print_goals().'</div>';
	$html .= '<div class="col-sm-12" id="hcDataDiv">'.print_health_concerns().'</div>';
	
	$html .= '</div>';
	$html .= '</div>'; // End of Modal Body
	
	$html .= '<div id="module_buttons" class="ad_modal_footer modal-footer">';
	$html .= '<button type="button" class="btn btn-success" id="hc_button" onclick="top.fmain.save_hc();">Save Health Concern</button>';
	$html .= '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
	$html .= '</div>';
	
	$html .= '</div>'; // End of Modal Content
	
	$html .= '</div>';
	$html .= '</div>';
	
	return $html;
	
}


function show_health_status(){
	
	$html = '';
	
	$html .= '<div id="pt_health_status_modal" class="modal fade" role="dialog">';
	$html .= '<div class="modal-dialog modal-lg" style="width:80%;">';
	
	$html .= '<!-- Modal content-->';
	$html .= '<div class="modal-content">';
	
	$html .= '<div class="modal-header bg-primary">';
	$html .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
	$html .= '<h4 class="modal-title">Patient Health Status</h4>';
	$html .= '</div>';
	
	$html .= '<div class="modal-body pd0">';
	$html .= '<div id="selectContainer" style="position:absolute;"></div>';
	
	$html .= '<div class="row">';
	$html .= '<div class="col-sm-12" id="pt_health_status_data_div" style="min-height:400px;">';
	
	$html .= print_health_status();
	
	$html .= '</div>';
	$html .= '</div>';
	
	$html .= '</div>'; // End of Modal Body
	
	$html .= '<div id="module_buttons" class="ad_modal_footer modal-footer">';
	$html .= '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
	$html .= '</div>';
	
	$html .= '</div>'; // End of Modal Content
	
	$html .= '</div>';
	$html .= '</div>';
	
	return $html;
}

function show_inpatient_data(){
	
	$html = '';
	
	$html .= '<div id="inpatient_modal" class="modal fade" role="dialog">';
	$html .= '<div class="modal-dialog modal-lg" style="width:80%;">';
	
	$html .= '<!-- Modal content-->';
	$html .= '<div class="modal-content">';
	
	$html .= '<div class="modal-header bg-primary">';
	$html .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
	$html .= '<h4 class="modal-title">Inpatient Data</h4>';
	$html .= '</div>';
	
	$html .= '<div class="modal-body pd0">';
	$html .= '<div id="selectContainer" style="position:absolute;"></div>';
	$html .= '<div class="row">';
	$html .= '<div class="col-sm-12" id="inpatientDataDiv">';
	$html .= print_inpatient();
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	
	$html .= '<div id="module_buttons" class="ad_modal_footer modal-footer">';
	$html .= '<button type="button" class="btn btn-success" onclick="top.fmain.save_inpatient();">Save Inpatient Data</button>';
	$html .= '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
	$html .= '</div>';
	
	$html .= '</div>';
	
	$html .= '</div>';
	$html .= '</div>';
	
	return $html;
}
?>
