<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");

function update_getDxFromAssessment($asmt){
	$asmt_full = $asmt = trim($asmt);
	$dx="";	
	$ptrn = "/\s+(\-\s+(OD|OS|OU)\s+)?\((\s*\w{3}(\.[\w\-]{1,4})?(\,)?)+\)$/";
	if(preg_match($ptrn, $asmt, $pre_match)){
		if(!empty($pre_match[0])){ 
			$ptrn22="/[0-9]+/";					
			if(preg_match($ptrn22, $pre_match[0])){ //check alphanumeric dx code, if not do not remove
				//$asmt = preg_replace($ptrn, "", $asmt);
				$dx=trim($pre_match[0]);
				$dx=str_replace(array("(",")"), "",  $dx);
				$dx=trim($dx);
			}
		}
	}
	$asmt_full = trim($asmt_full);
	return array($asmt_full, $dx);
}

function convertICDDxCode($strChkCd, $codetype, $changetype){
	$ret="";
	$strChkCd=trim($strChkCd);
	if(!empty($strChkCd)){	
		$sql = "SELECT GROUP_CONCAT(icd9), COUNT(icd9) AS counter,
						icd9, icd10,concat(laterality,',',staging,',',severity) as lat_all 
						FROM icd10_data ";
		$sqlTmp = "";
		if($codetype==9){
			$strChkCdTmp  = '0';
			if(stristr($strChkCd,'.')) {
				list($beforeDecimal,$afterDecimal) = explode(".",$strChkCd);
				if(strlen(trim($afterDecimal))==1) { $afterDecimal = $afterDecimal.'0'; }
				$strChkCdTmp = $beforeDecimal.'.'.$afterDecimal;
				$sqlTmp = $sql."WHERE icd9 IN ('".sqlEscStr($strChkCdTmp)."') GROUP BY icd9";
			}
			$sql .= "WHERE icd9 IN ('".sqlEscStr($strChkCd)."') GROUP BY icd9";
		}else if($codetype==1){
			$sql .= "WHERE icd10 IN ('".sqlEscStr($strChkCd)."') GROUP BY icd10";
		}
		$row=sqlQuery($sql);
		if($row==false && $codetype==9 && $sqlTmp){
			$row=sqlQuery($sqlTmp);	
		}
		//echo '<br>'.$sql;
		//echo '<br>'.$sqlTmp;
		if($row!=false){
			if($changetype==1){
				$ret=$row["icd10"];
			}else{
				$ret=$row["icd9"];
			}
		}
	}
	return $ret;
}

$msg_info=array();

//--
/*
$_POST["elem_go"] = "Change it.";
if(!empty($_POST["elem_go"])){
$elem_ptid = $_POST["elem_ptid"];
$elem_formId = $_POST["elem_formId"];
$elem_sbId = $_POST["elem_sbId"];
if(empty($elem_ptid)){ exit("Error: patient id is empty!");  }
if(empty($elem_formId) && empty($elem_sbId)){ exit("Error: Please provide Form id OR Superbill id ! ");  }
*/
$curDt = date("Ymd");
$res1 = imw_query("CREATE  TABLE chart_assessment_plans_".$curDt." LIKE chart_assessment_plans");
$res2 = imw_query("INSERT INTO chart_assessment_plans_".$curDt." (SELECT *  FROM chart_assessment_plans)");

$totalPatImport = 0;
$dos_date="2016-10-01";
$qry  = "select id,encounterId,patient_id from chart_master_table where date_of_service >= '".$dos_date."' order by id";
$res = imw_query($qry) or die($qry.imw_error());
$numRow = imw_num_rows($res);
if($numRow>0) {
	while($row = imw_fetch_assoc($res)) {
		$encounterId=$row["encounterId"];
		$elem_ptid=$row["patient_id"];
		$elem_formId=$row["id"];
		$sql = "UPDATE chart_master_table SET enc_icd10='1' WHERE patient_id='".$elem_ptid."' AND id ='".$elem_formId."' ";
		$row=sqlQuery($sql);
	
		//Make Array of assessment and plan elements
		$arrAp_assess=array();			
		$arrAp_plan=array();
		$arrAp_resolve=array();
		$arrAp_ne=array();
		$arrAp_eye=array();
		$arrAp_conmed=array();
		$oChartApXml = new ChartAP($elem_ptid,$elem_formId);
		$arrApVals_prev = $oChartApXml->getVal();
		$arrAp_prev = $arrApVals_prev["data"]["ap"];
		$lenAssess_prev = count($arrAp_prev);
		for($i=0,$j=1;$i<$lenAssess_prev;$i++){
			$tmp_as = trim($arrAp_prev[$i]["assessment"]);
			if(!empty($tmp_as)){
				list($tmpasmt, $dx) = update_getDxFromAssessment($tmp_as);	
				if(!empty($dx)){
					$ar_dx = explode(",", $dx);
					$ln = count($ar_dx);
					for($j=0;$j<$ln; $j++){
						$t_dx=trim($ar_dx[$j]);
						if(!empty($t_dx)){
							$dxRef=convertICDDxCode($t_dx, 9, 1);
							$dxRef=trim($dxRef);
							$ar_dx[$j]=$dxRef;
						}	
					}
					
					$dxRef=implode(", ",$ar_dx);
				}			
				
				if(!empty($dxRef)){
					$tmp_as = str_replace($dx, $dxRef, $tmp_as);
					$tmp_as = trim($tmp_as);
				}
				
				$arrAp_assess[] = $tmp_as; 
				$arrAp_plan[] = trim($arrAp_prev[$i]["plan"]);
				$arrAp_resolve[] = trim($arrAp_prev[$i]["resolve"]);
				$arrAp_ne[] = trim($arrAp_prev[$i]["ne"]);
				$arrAp_eye[] = trim($arrAp_prev[$i]["eye"]);
				$arrAp_conmed[] = trim($arrAp_prev[$i]["conmed"]);			
			}
		}
		
		if(count($arrAp_assess)>0){
			$sql = "SELECT * FROM chart_assessment_plans WHERE patient_id='".$elem_ptid."' AND form_id='".$elem_formId."'  ";
			$row = sqlQuery($sql);
			if($row!=false){
				$assess_plan_db = trim($row["assess_plan"]);
				if(!empty($assess_plan_db)){
					list($strAPXml, $str_modi_note_Asses) = $oChartApXml->getXml(array($arrAp_assess,$arrAp_plan,$arrAp_resolve,$arrAp_ne,$arrAp_eye, $arrAp_conmed),1,1);
					$strAPXml = imw_real_escape_string($strAPXml);		
					$sql = "UPDATE chart_assessment_plans SET assess_plan='".$strAPXml."' WHERE patient_id='".$elem_ptid."' AND form_id='".$elem_formId."' ";
					//echo "<xmp>".$sql."</xmp>";
					$row = sqlQuery($sql);
					$totalPatImport++;
				}else {
					echo "<br>Not Found1 -  patient_id='".$elem_ptid."' AND form_id='".$elem_formId."'";	
				}
			}else {
				echo "<br>Not Found2 -  patient_id='".$elem_ptid."' AND form_id='".$elem_formId."'";	
			}
		}else {
			echo "<br>Not Found3 -  patient_id='".$elem_ptid."' AND form_id='".$elem_formId."'";	
		}
	}
	exit("<br>Process done ".$totalPatImport.' of '.$numRow);
}

/*
if(!empty($elem_sbId) || !empty($elem_formId)){
	
	$sql = "SELECT idSuperBill, arr_dx_codes FROM superbill WHERE patientId = '".$elem_ptid."'  ";
	if(!empty($elem_sbId)){  $sql .= " AND idSuperBill= '".$elem_sbId."' "; }
	else if(!empty($elem_formId)){  $sql .= " AND formId= '".$elem_formId."' "; }
	$row = sqlQuery($sql);
	if($row!=false){
		$id_sb = $row["idSuperBill"];
		$str_dx_codes = $row["arr_dx_codes"];
		if(!empty($str_dx_codes)){
			$arr_dx_codes=unserialize($str_dx_codes);
			$ln = count($arr_dx_codes);
			for($i=0; $i<$ln; $i++){
				$tmp = trim($arr_dx_codes[$i]);
				if(!empty($tmp)){
					//echo "<br/>".$tmp;
					$arr_dx_codes[$i]=convertICDDxCode($tmp, 9, 1);					
				}
			}
			
			//
			$str_dx_codes=serialize($arr_dx_codes);
			$sql = "UPDATE superbill SET sup_icd10='1', arr_dx_codes='".imw_real_escape_string($str_dx_codes)."' WHERE idSuperBill='".$id_sb."' ";
			//echo "<xmp>$sql</xmp>";
			$row = sqlQuery($sql);
		}
		
		//echo "<br/>";
		
		$sql = "SELECT id, dx1,dx2,dx3,dx4,dx5,dx6,dx7,dx8,dx9,dx10,dx11,dx12 from procedureinfo where idSuperBill = '".$id_sb."' ";
		//echo $sql; 
		$rez = sqlStatement($sql);
		for($j=0;$row=sqlFetchArray($rez);$j++){
			
			$id = $row["id"];
			$sql="";
			for($i=1;$i<13;$i++){
				$tmp_dx = trim($row["dx".$i]);
				//echo "<br/>".$tmp_dx;
				if(!empty($tmp_dx)){
					$tmp_dx_2="";
					$tmp_dx_2=$oDx->convertICDDxCode($tmp_dx, 9, 1);
					if(!empty($tmp_dx_2)){   if(!empty($sql)){ $sql.=", ";  }  	$sql.= " dx".$i." = '".$tmp_dx_2."' ";	}
				}
			}
			
			//
			if(!empty($sql)){
				$sql = "UPDATE procedureinfo SET ".$sql." WHERE id='".$id."' ";
				//echo "<br/><xmp>".$sql."</xmp>";
				$row=sqlQuery($sql);
			}
		}
	}	
}
*/
?>
