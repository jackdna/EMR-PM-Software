<?php
class Insurance{
	//-- Insurance -----
function wv_getInsInfo($caseId){
	$arr = array();
	if(!empty($caseId)){
		//case name and id
		$elem_insuranceCaseName_id = $this->get_insurance_case_name($caseId,"NoCaseId");
		$elem_insuranceCaseName_id_alt = "<label id=\"vis_ins\">".$elem_insuranceCaseName_id."</label>";
		
		//insProviders
		$strInsProvInfo = $this->getInsAllProviderInfo($caseId,1);
		$insCompDataArr = $this->getInsAllProviderInfo($caseId);
		//VisionBar
		if(!empty($strInsProvInfo)) $elem_insuranceCaseName_id .= " (".$strInsProvInfo.") ";

			$strInsProvInfo_arr = explode(',',$strInsProvInfo);
			//$elem_insuranceCaseName_id_alt .= ' ';
			$tmp = ""; $tmp_c = "";
			if($strInsProvInfo_arr[0] != "")
			{
				$tmp .= $strInsProvInfo_arr[0];
				$tmp_c .= 	!empty($insCompDataArr["primary"]["pracCode"]) ? $insCompDataArr["primary"]["pracCode"] : $insCompDataArr["primary"]["name"];
			}
			
			if($strInsProvInfo_arr[1] != "")
			{
				$tmp .= ', '.$strInsProvInfo_arr[1];
				$tmp_c .= ', ';
				$tmp_c .= 	!empty($insCompDataArr["secondary"]["pracCode"]) ? $insCompDataArr["secondary"]["pracCode"] : $insCompDataArr["secondary"]["name"];	
			}
			
			if(!empty($tmp)){
				
				$elem_insuranceCaseName_id_alt .= ' <label id="ins_pro"  title="'.$tmp_c.'" data-toggle="tooltip" >('.$tmp.')</label>'; //onClick="getToolTip(\''.$insCompDataArr["primary"]['icid'].'\')" onmouseout="hideToolTip();"
			}
			
			//$elem_insuranceCaseName_id_alt .= ') ';
			
	}
	
	if(isset($elem_insuranceCaseName_id)){$arr["elem_insuranceCaseName_id"] = $elem_insuranceCaseName_id;}
	if(isset($elem_insuranceCaseName_id_alt)){$arr["elem_insuranceCaseName_id_alt"] = "<div id=\"infoIns_hidden\" class=\"hidden\">".$elem_insuranceCaseName_id_alt."</div>";}
	return $arr;
}
////////////Function To GET Insurance Case Name Information will return Name of case/////////////
function get_insurance_case_name($case_id,$flg=""){
	$resarray=sqlQuery("select *from insurance_case where ins_caseid='".$case_id."'");	
	$ret_val="";
	if($resarray){

		$resarraytype=sqlQuery("select *from insurance_case_types  where case_id='".$resarray["ins_case_type"]."'");		
		if($resarraytype){
			if($resarraytype["case_name"] == "Workman Comp"){
				$caseName = 'Work Comp';
			}
			else{
				$caseName = $resarraytype["case_name"];
			}

			if($flg=="NoCaseId"){
				$ret_val=$caseName;
			}else{
				$ret_val=$caseName."-".$resarray["ins_caseid"];
			}
		}
	}
	return($ret_val);

}
///////End Function To GET Insurance Case Name Information will return Name of case///////////

function getInsAllProviderInfo($insCaseId,$commSep=0){
	$str = "";
	$arr = array();
	$sql = "SELECT ".

		   "c1.ins_caseid,
			c2.type,
			c2.actInsComp,
			c3.name,c3.in_house_code,c3.id as icid ".

		   "FROM insurance_case c1 ".
		   "LEFT JOIN insurance_data c2 ON c2.ins_caseid=c1.ins_caseid ".
		   "LEFT JOIN insurance_companies c3 ON c3.id=c2.provider  ".
		   "WHERE c1.ins_caseid = '".$insCaseId."' ";
	$rez = sqlStatement($sql);
	for($i=0;$row=sqlFetchArray($rez);$i++){
		$rtype = $row["type"];
		$rname = $row["name"];
		$rihc = $row["in_house_code"];
		$icid = $row["icid"];
		if($row["actInsComp"] == 1)
		$arr[$rtype] = array("name"=>$rname,"pracCode"=>$rihc,"icid"=>$icid);
	}

	if($commSep == 1){
		$len=8;
		if(!empty($arr["primary"]["name"]) && !empty($arr["secondary"]["name"])){ $len=5; }
		
		if(!empty($arr["primary"]["name"])){
			$str .= !empty($arr["primary"]["pracCode"]) ? $arr["primary"]["pracCode"] : $arr["primary"]["name"];

			if(strlen($str) > $len){
				$str = substr($str,0,$len-2)."..";
			}

		}		
		
		if(!empty($arr["secondary"]["name"])){
			$str .= !empty($str) ? ", " : ""; //Always put space after comma
			$tmp .= !empty($arr["secondary"]["pracCode"]) ? $arr["secondary"]["pracCode"] : $arr["secondary"]["name"];
			if(strlen($tmp) > $len){
				$tmp = substr($tmp,0,$len-2)."..";
			}
			$str .= $tmp;
		}
	}

	return ($commSep == 1) ? $str : $arr;
}

function getInsFeeColumn($insCaseId, $dt="", $skip_policy="")
{
	//Check Admin Setting for default column
	if(empty($skip_policy)){
	$sql="select billing_amount from copay_policies";
	$row=sqlQuery($sql);
	if($row!=false){
		$billing_amount=$row['billing_amount'];
		if($billing_amount=='Default'){
			return 0;
		}
	}
	}
	//--
	
	$dt = (empty($dt)) ? date("Y-m-d") : wv_formatDate($dt,0,0,'insert');
	$sql = "SELECT
		insurance_data.id AS insDataId,
		insurance_data.ins_caseid,
		insurance_data.provider,
		insurance_data.type,
		insurance_companies.id AS insComId,
		insurance_companies.name,
		insurance_companies.in_house_code,
		insurance_companies.FeeTable
		FROM insurance_data
		LEFT JOIN insurance_companies ON insurance_companies.id = insurance_data.provider
		WHERE insurance_data.ins_caseid='".$insCaseId."'
		AND insurance_data.actInsComp='1'
		AND insurance_data.type='primary'
		AND effective_date <= '".$dt."'
		AND (expiration_date = '0000-00-00 00:00:00'
		OR expiration_date > '".$dt."')
		";

	$row = sqlQuery($sql);
	if($row != false)
	{
		$retValue = $row["FeeTable"];
	}
	return (!empty($retValue)) ? $retValue : 0 ;
}

function getInsProviderInfo($insCaseId, $dt="")
{
	$retValue = array();
	$dt = (empty($dt)) ? date("Y-m-d") : wv_formatDate($dt,0,0,'insert');
	$sql = "SELECT
		insurance_data.id AS insDataId,
		insurance_data.ins_caseid,
		insurance_data.provider,
		insurance_data.type,
		insurance_companies.id AS insComId,
		insurance_companies.name,
		insurance_companies.in_house_code,
		insurance_companies.FeeTable
		FROM insurance_data
		LEFT JOIN insurance_companies ON insurance_companies.id = insurance_data.provider
		WHERE insurance_data.ins_caseid='".$insCaseId."'
		AND insurance_data.actInsComp='1'
		AND insurance_data.type='primary'
		AND effective_date <= '".$dt."'
		AND (expiration_date = '0000-00-00 00:00:00'
		OR expiration_date > '".$dt."')
		";
	$row = sqlQuery($sql);
	if($row != false)
	{
		if(!empty($row["in_house_code"])){
			$retValue["inHouseCode"] = $row["in_house_code"];
		}

		if(!empty($row["name"])){
			$retValue["Name"] = $row["name"];
		}

		//
		$retValue["insPro"] = "";
		//Medicare
		if((strpos (strtolower($retValue["inHouseCode"]),strtolower("Medicare")) !== false ) ||
		   (strpos (strtolower($retValue["Name"]),strtolower("Medicare")) !== false )
		  ){
			$retValue["insPro"] = "Medicare";
		}else if(
		  (strpos (strtolower($retValue["inHouseCode"]),strtolower("Aetna")) !== false ) ||
		  (strpos (strtolower($retValue["Name"]),strtolower("Aetna")) !== false )
		){
			$retValue["insPro"] = "Aetna";
		}
	}
	return $retValue;
}

function getPtInsuranceInfo($ptId, $caseId){
	
	$ar=array();
	$insuranceCaseId = $caseId;
	$patient_id = $ptId;
	
	if(empty($patient_id) || empty($caseId)){return $ar;}

	$arr_type=array("primary", "secondary", "tertiary");

	foreach($arr_type as $key => $ins_type){

		$getInsCaseStr="SELECT a.*, b.*, c.case_name, d.*
						FROM insurance_data a,
						insurance_case b,
						insurance_case_types c,
						insurance_companies d
						WHERE a.pid='$patient_id'
						AND a.type='".$ins_type."'
						AND a.ins_caseid='".$insuranceCaseId."'
						AND a.ins_caseid=b.ins_caseid
						AND b.ins_case_type=c.case_id
						AND a.provider=d.id
						AND a.actInsComp='1' ";					
		$row=sqlQuery($getInsCaseStr);				
		if($row!=false){
			$ar[$ins_type]["providerId"]=$row['provider'];
			$ar[$ins_type]["insCoInHouseCode"]=$row['in_house_code'];
			$ar[$ins_type]["insProviderName"]=$row['name'];
			$ar[$ins_type]["InsCaseName"]=$row['case_name'];
			$ar[$ins_type]["plan_name"]=$row['plan_name'];
			$ar[$ins_type]["policy_number"]=$row['policy_number'];
			$ar[$ins_type]["group_number"]=$row['group_number'];
			$ar[$ins_type]["copay"]=$row['copay'];
			$ar[$ins_type]["ter_copay"]=$row['copay'];
			$ar[$ins_type]["effective_date"]=wv_formatDate($row['effective_date']);
			$ar[$ins_type]["expiration_date"]=wv_formatDate($row['expiration_date']);
			
			//policy & group
			$ar[$ins_type]["PG"] = "-";
			if(!empty($ar[$ins_type]["policy_number"])) {
				$ar[$ins_type]["PG"] = $ar[$ins_type]["policy_number"];
			}
			if(!empty($ar[$ins_type]["group_number"])){
				$ar[$ins_type]["PG"] .= " & ". $ar[$ins_type]["group_number"] ;
			}
		}			
	}				

	return $ar;
}
	
}
?>