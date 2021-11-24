<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

?><?php
/*
File: nc_txt.php
Purpose: To Export NC In Fixed Length Format.
Access Type: Direct Access (data posted to this page) 
*/
$updir=substr(data_path(), 0, -1);
$srcDir = substr(data_path(1), 0, -1);

//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
$arrDateRange= $CLSCommonFunction->changeDateSelection();
if($dayReport=='Daily'){
	$Start_date = $End_date= date($phpDateFormat);
}else if($dayReport=='Weekly'){
	$Start_date = $arrDateRange['WEEK_DATE'];
	$End_date= date($phpDateFormat);
}else if($dayReport=='Monthly'){
	$Start_date = $arrDateRange['MONTH_DATE'];
	$End_date= date($phpDateFormat);
}else if($dayReport=='Quarterly'){
	$Start_date = $arrDateRange['QUARTER_DATE_START'];
	$End_date = $arrDateRange['QUARTER_DATE_END'];
}

$cur_dat			= date(''.phpDateFormat().'');
$ins_comp_name_str 	= trim(implode(',',$_REQUEST["insuranceName"]));
$phy_name_str 		= trim(implode(',',$_REQUEST["Physician"]));
$group_name_str 	= trim(implode(',',$_REQUEST["groups"]));
$from_date 			= getDateFormatDB($Start_date);
$to_date 			= getDateFormatDB($End_date);
$report_type		= $_REQUEST['report_type'];

//echo '<br>Ins. Carrier '.$ins_comp_name_str.'<br>Physician '.$phy_name_str.'<br>Group '.$group_name_str.'<br>From Dt '.$from_date.'<br>To Dt '.$to_date.'<br>Report Type '.$report_type;
$andQry = "";
if($group_name_str) {
	$andQry .= " and p.gro_id IN(".$group_name_str.") ";
}
if($phy_name_str) {
	$andQry .= " and p.primaryProviderId IN(".$phy_name_str.")";
}
if($ins_comp_name_str) {
	$andQry .= " and p.primaryInsuranceCoId IN(".$ins_comp_name_str.")";
}
if($from_date && $from_date !="--" && $to_date && $to_date !="--") {
	$andQry .= " and p.date_of_service between '".$from_date."' and '".$to_date."' ";	
}

//START CODE OF INSURANCE COMPANY
$insCompArr = array();
$insCompRes=imw_query("select id, `name`, in_house_code, Payer_id_pro, Payer_id, emdeon_payer_eligibility, ins_state_payer_code, payer_mapping_code, ins_type, claim_type from insurance_companies ORDER BY id");
if(imw_num_rows($insCompRes)>0) {
	while($insCompRow=imw_fetch_array($insCompRes)) {
		$cId 											= $insCompRow["id"];
		$insCompArr[$cId]["name"] 						= $insCompRow["name"];
		$insCompArr[$cId]["in_house_code"] 				= $insCompRow["in_house_code"];
		$insCompArr[$cId]["Payer_id_pro"] 				= $insCompRow["Payer_id_pro"];
		$insCompArr[$cId]["Payer_id"] 					= $insCompRow["Payer_id"];
		$insCompArr[$cId]["emdeon_payer_eligibility"] 	= $insCompRow["emdeon_payer_eligibility"];
		$insCompArr[$cId]["ins_state_payer_code"] 		= $insCompRow["ins_state_payer_code"];
		$insCompArr[$cId]["payer_mapping_code"] 		= $insCompRow["payer_mapping_code"];
		$insCompArr[$cId]["ins_type"] 					= $insCompRow["ins_type"];
		$insCompArr[$cId]["claim_type"] 				= $insCompRow["claim_type"];
		
	}
}
//END CODE OF INSURANCE COMPANY

//START CODE OF REFERRING PHYSICIAN
$reffPhyArr = array();
$reffPhyRes=imw_query("select physician_Reffer_id, MDCD, MDCR, NPI FROM refferphysician ORDER BY physician_Reffer_id");
if(imw_num_rows($reffPhyRes)>0) {
	while($reffPhyRow=imw_fetch_array($reffPhyRes)) {
		$reffId 											= $reffPhyRow["physician_Reffer_id"];
		$reffPhyArr[$reffId]['MDCD'] 						= $reffPhyRow["MDCD"];
		$reffPhyArr[$reffId]['MDCR'] 						= $reffPhyRow["MDCR"];
		$reffPhyArr[$reffId]['NPI'] 						= $reffPhyRow["NPI"];
	}
}
//END CODE OF REFERRING PHYSICIAN

//START CODE OF Insurance Case
$insCaseArr = array();
$insCaseRes=imw_query("select ins_caseid, ins_case_type from insurance_case ORDER BY ins_caseid");
if(imw_num_rows($insCaseRes)>0) {
	while($insCaseRow=imw_fetch_array($insCaseRes)) {
		$csId = $insCaseRow["ins_caseid"];
		$insCaseArr[$csId]['ins_case_type'] = $insCaseRow["ins_case_type"];
	}
}
//END CODE OF Insurance Case

//START CODE OF Insurance Case Types
$insCaseTypeArr = array();
$insCaseTypeRes=imw_query("select case_id, case_name from insurance_case_types ORDER BY case_id");
if(imw_num_rows($insCaseTypeRes)>0) {
	while($insCaseTypeRow=imw_fetch_array($insCaseTypeRes)) {
		$cstId = $insCaseTypeRow["case_id"];
		$insCaseTypeArr[$cstId]['case_name'] = $insCaseTypeRow["case_name"];
	}
}
//END CODE OF Insurance Case Types

//START CODE OF pos_tbl
$posArr = array();
$posRes=imw_query("SELECT pos_id,pos_code FROM pos_tbl ORDER BY pos_id");
if(imw_num_rows($posRes)>0) {
	while($posRow=imw_fetch_array($posRes)) {
		$posId 							= $posRow["pos_id"];
		$posArr[$posId]['pos_code'] 	= $posRow["pos_code"];
	}
}
//END CODE OF pos_tbl

//START CODE OF USER NPI
$userArr = array();
$userNpiRes=imw_query("select id, user_npi, licence, TaxonomyId, MedicaidId from users ORDER BY id");
if(imw_num_rows($userNpiRes)>0) {
	while($userNpiRow=imw_fetch_array($userNpiRes)) {
		$uId = $userNpiRow["id"];
		$userArr[$uId]['user_npi'] 		= $userNpiRow["user_npi"];	
		$userArr[$uId]['licence'] 		= $userNpiRow["licence"];	
		$userArr[$uId]['TaxonomyId'] 	= $userNpiRow["TaxonomyId"];	
		$userArr[$uId]['MedicaidId'] 	= $userNpiRow["MedicaidId"];
	}
}
//END CODE OF USER NPI

//START CODE OF CPT4 CODE
$cpt4CodeNewArr = array();
$cpfFeeRes=imw_query("select cpt_fee_id,cpt4_code from cpt_fee_tbl where delete_status = '0'");
if(imw_num_rows($cpfFeeRes)>0) {
	while($cpfFeeRow=imw_fetch_array($cpfFeeRes)) {
		$cptFeeId = $cpfFeeRow["cpt_fee_id"];
		$cpt4CodeNewArr[$cptFeeId] = $cpfFeeRow["cpt4_code"];
	}
}
//END CODE OF CPT4 CODE

//START CODE OF pos_facilityies_tbl
$posFacilityArr = $posFacilityNameArr = array();
$posFacilityRes=imw_query("select pos_facility_id, npiNumber,facility_name from pos_facilityies_tbl ORDER BY pos_facility_id");
if(imw_num_rows($posFacilityRes)>0) {
	while($posFacilityRow=imw_fetch_array($posFacilityRes)) {
		$pos_facility_id = $posFacilityRow["pos_facility_id"];
		$posFacilityArr[$pos_facility_id] = $posFacilityRow["npiNumber"];
		$posFacilityNameArr[$pos_facility_id] = $posFacilityRow["facility_name"];	
	}
}
//END CODE OF pos_facilityies_tbl

//------------------------ Modifier Code Detail ------------------------//
$modifierCodeArr = array();
$sql = imw_query("select * from modifiers_tbl ORDER BY modifiers_id");
while($rowModifier=imw_fetch_array($sql)){			
	$modifierCodeArr[$rowModifier["modifiers_id"]]=$rowModifier;
}
//------------------------ Modifier Code Detail	------------------------//

//------------------------ TOS Detail ------------------------//
$tos_data_arr =array();
$selQry = "select tos_id,tos_code,tos_prac_cod from tos_tbl ORDER BY tos_id";
$res = imw_query($selQry) or die(imw_error().$selQry);
while($row = imw_fetch_array($res)){
	$tos_data_arr[$row["tos_id"]]=$row;
}
//------------------------ TOS Detail ------------------------//

$newContent = "";

$eAndMCptArray = array(99201, 99202, 99203, 99204, 99205, 99211, 99212,
					   99213, 99214, 99215, 99242, 99243, 99244, 99245);


$andInstitueTypeQry = "";
$reportName = "";
if($report_type == "ub_hcfa") {
	$andInstitueTypeQry = "";
	$reportName = "";
}elseif($report_type == "ub") {
	$andInstitueTypeQry = " AND inc.institutional_type ='INST_ONLY' ";
	$reportName = "(UB04)";
}elseif($report_type == "hcfa") {
	$andInstitueTypeQry = " AND inc.institutional_type !='INST_ONLY' ";
	$reportName = "(HCFA)";
} 
//inc.Insurance_payment = 'HCFA1500' AND inc.name != 'SELF PAY'
$qry = "SELECT p.*, if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%m%d%Y'),'') AS admit_date_format, if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%m-%d-%Y'),'') AS admit_date_format_new, if(p.disch_date != '0000-00-00 00:00:00', DATE_FORMAT(p.disch_date, '%m%d%Y'),'') AS disch_date_format,
		if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m%d%Y'),'') as date_of_service_format,
		if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m%d%y'),'') as date_of_service_format_short, if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m-%d-%Y'),'') as date_of_service_format_new,
		gn.group_Federal_EIN, gn.group_NPI, gn.group_institution, gn.name as groupName, 
		pd.city AS ptCity, pd.state AS ptState, pd.postal_code AS ptPostalCode, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m%d%Y'),'') AS ptDobFormat, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m-%d-%Y'),'') AS ptDobFormatNew,
		pd.sex AS ptSex, pd.ss AS patientSSN, pd.race, pd.ethnicity, pd.street AS ptStreet, pd.street2 AS ptStreet2,fname AS ptFname, lname AS ptLname, TRIM(CONCAT(pd.lname,', ',pd.fname,' ',pd.mname)) AS ptName, 
		pd.default_facility AS ptDefaultFacility, pd.country_code AS ptCountryCode, pd.status AS maritalStatus, pd.occupation AS ptOccupation, 
		pd.primary_care_id AS ptPrimaryCareId
		FROM patient_charge_list p
		
		INNER JOIN patient_data pd ON(pd.pid = p.patient_id)
		LEFT JOIN insurance_companies inc ON (p.primaryInsuranceCoId = inc.id   ".$andInstitueTypeQry.")
		INNER JOIN submited_record sr ON(sr.encounter_id = p.encounter_id)
		LEFT JOIN groups_new gn ON(gn.gro_id=p.gro_id)
		WHERE p.charge_list_id !='0' and p.del_status='0' and p.enc_accept_assignment!='2' ".$andQry."  
		GROUP BY p.charge_list_id
		ORDER BY pd.pid ";

$filler = ""; 		
$errorMsgArr = array();
$res = imw_query($qry) or die($qry.imw_error());
$tmpTotalNumberofClaims = 0;
$tmpTotalChargesSubmitted = 0;
if(imw_num_rows($res)>0) {
	$c=0;
	while($row = imw_fetch_assoc($res)) {
		$charg_id = $row["charge_list_id"];
		//and pcl.proc_selfpay!='1'
		$qrySub = "select pcl.*,cft.units as admin_cpt_unit,if(pcl.onset_date != '0000-00-00', DATE_FORMAT(pcl.onset_date, '%m%d%Y'),'') AS onset_date_format,
				cft.cpt_comments as admin_ndc,cft.cpt_desc as admin_cpt_desc,cft.cpt4_code,pcl.procCode,pcl.modifier_id1,pcl.modifier_id2,pcl.modifier_id3
				from patient_charge_list_details pcl 
				join cpt_fee_tbl cft on (cft.cpt_fee_id = pcl.procCode)
				where pcl.del_status='0' and pcl.charge_list_id = '".$charg_id."'
				and cft.not_covered = '0' and pcl.posted_status='1'
				and pcl.differ_insurance_bill != 'true'
				order by pcl.display_order,
				pcl.charge_list_detail_id limit 0,24";
		$arrpatientChargeDetails = get_array_records_query($qrySub);
		$arrpatientChargeDetailsCount = count($arrpatientChargeDetails);
		if($arrpatientChargeDetailsCount >0) {
			$tmpTotalNumberofClaims++;
			if($c>0) {
				//$newContent .= "<br>";	
				$newContent .= "\n";
			}
	
			$objpriinsData = "";
			if($row["primaryInsuranceCoId"]){
				$objpriinsData = getRecords_ins_data_con('insurance_data','pid',$row["patient_id"],'provider',$row["primaryInsuranceCoId"],'type','primary','ins_caseid',$row["case_type_id"],$row["date_of_service"]);
			}
			$objsecinsData='';
			if($row["secondaryInsuranceCoId"]){
				$objsecinsData = getRecords_ins_data_con('insurance_data','pid',$row["patient_id"],'provider',$row["secondaryInsuranceCoId"],'type','secondary','ins_caseid',$row["case_type_id"],$row["date_of_service"]);
			}
	
			$objInsGroupNumber = $objSecondaryInsGroupNumber = "";
			if($row["patient_id"]){
				if($row["case_type_id"]){
					$objInsGroupNumber = getInsGroupNumberState($row["case_type_id"],$row["patient_id"],'primary',$row["date_of_service"],'1');				
					if($row["secondaryInsuranceCoId"]){
						$objSecondaryInsGroupNumber = getInsGroupNumberState($row["case_type_id"],$row["patient_id"],'secondary',$row["date_of_service"],'1');
					}
					elseif($row["tertiaryInsuranceCoId"] && $secondryFlag == false){
						$objSecondaryInsGroupNumber = getInsGroupNumberState($row["case_type_id"],$row["patient_id"],'tertiary',$row["date_of_service"],'1');
					}
				}
			}
	
			$dignosisArr = $dignosisArrNew = $cpt4CodeArr = $dateOfServiceArr = $posCodeArr = $paidChargesArr = $totalUnitArr = $hcpcsRatesCodeArr = $modifierCodeArr1 = $modifierCodeArr2 = $modifierCodeArr3 = $typeOfServiceArr = array();
			
			$sub_total_proc_charges = 0;
			for($d = 0;$d< count($arrpatientChargeDetails);$d++){
				$charge_list_detail_id = $arrpatientChargeDetails[$d]['charge_list_detail_id'];
				//DIAGNOSIS CODE
				for($f=1;$f<=24;$f++){
					if($arrpatientChargeDetails[$d]['diagnosis_id'.$f]!=""){
						$diagId = $arrpatientChargeDetails[$d]['diagnosis_id'.$f];
						$diagId = preg_replace("/[^A-Za-z0-9]/","",$diagId);
						$dignosisArr[] = $diagId;
						$dignosisArrNew[$d][] = $diagId;
					}
				}

				$dateOfServiceArr[] = $row["date_of_service_format"];//24-A HCFA
				
				//get POS Code //24-C
				$placeOfServiceDb = $arrpatientChargeDetails[$d]['place_of_service'];
				$posCodeArr[] = $posArr[$placeOfServiceDb]['pos_code'];
				
				$type_of_service_id = $arrpatientChargeDetails[$d]['type_of_service'];
				$typeOfService = $tos_data_arr[$type_of_service_id]['tos_prac_cod'];
				$typeOfServiceArr[] = $typeOfService;
				/*
				if($row["encounter_id"]=='237408') {
					pre($tos_data_arr);
				}*/
				
				//get CPT4Code //24-D HCFA
				//if(strtoupper(substr($arrpatientChargeDetails[$d]['cpt4_code'],0,1)) != "G") { //DO NOT INCLUDE G-CODES
					$cpt4CodeArr[] = $arrpatientChargeDetails[$d]['cpt4_code'];
				//}
				
				//CODE FOR 24-F
				if($row['acc_anes_unit']>0) {
					if(trim($arrpatientChargeDetails[$d]['cpt4_code']) != 'V2785'){
						//$arr_admin_cpt_units[$charge_list_detail_id]=$arrpatientChargeDetails[$d]['admin_cpt_unit'];
						$paidChargesTmp	= $arrpatientChargeDetails[$d]['admin_cpt_unit'];
						if(!trim($paidChargesTmp)) {
							$paidChargesTmp	= '0';	
						}
						$paidChargesArr[]	= $paidChargesTmp;
					}	
				}else {
					$totalcharges 		= numberformat($arrpatientChargeDetails[$d]['procCharges'] * $arrpatientChargeDetails[$d]['units'],2);
					$paidCharges 		= $totalcharges;
					/*
					if($row["encounter_id"]=='237408') {
						echo '<br>'.$arrpatientChargeDetails[$d]['procCharges'].' * '.$arrpatientChargeDetails[$d]['units'].' = '.$paidCharges;
					}*/
					//$paidCharges 		= substr($paidCharges,0,-3);
					$paidCharges 		= str_ireplace('.','',$paidCharges);
					$paidCharges 		= str_ireplace(',','',$paidCharges);
					$paidCharges		= str_ireplace('$','',$paidCharges);
					$paidCharges		= str_ireplace('&pound;','',$paidCharges);
					if(!trim($paidCharges)) {
						$paidCharges 	= '0';	
					}
					$paidChargesArr[]	= $paidCharges;
				}

				//CODE FOR 24-G HCFA
				$total_unit = "";
				if($row['acc_anes_unit']>0) {
					$total_unit=$arrpatientChargeDetails[$d]['admin_cpt_unit']+number_format($row['acc_anes_unit'],2);
				}else if($arrpatientChargeDetails[$d]['units']) {
					$total_unit = unit_format($arrpatientChargeDetails[$d]['units']);	
				}
				$totalUnitArr[] = $total_unit;
				
				//CODE FOR 28
				$sub_total_proc_charges += str_ireplace(',','',$arrpatientChargeDetails[$d]['procCharges'] * $arrpatientChargeDetails[$d]['units']);			
				/*
				if($row["encounter_id"]=='237408') {
					$test = $arrpatientChargeDetails[$d]['procCharges'] * $arrpatientChargeDetails[$d]['units'];
					echo '<br>'.$arrpatientChargeDetails[$d]['procCharges'].' * '.$arrpatientChargeDetails[$d]['units'].' = '.$test.' <br>Total = '.$sub_total_proc_charges;
				}
				*/
				$posFacilityId = "";
				if($arrpatientChargeDetails[$d]['posFacilityId']>0){
					$posFacilityId=$arrpatientChargeDetails[$d]['posFacilityId'];
				}
				
				//START CODE FOR 24-D HCFA
				$proc_cod							= $arrpatientChargeDetails[$d]['procCode'];
				$cpt4_code							= $cpt4CodeNewArr[$proc_cod];
				
				$modifier_id						= $arrpatientChargeDetails[$d]['modifier_id1'];
				$modifier_id2						= $arrpatientChargeDetails[$d]['modifier_id2'];
				$modifier_id3						= $arrpatientChargeDetails[$d]['modifier_id3'];
				$modifierCodeNewArr					= array();
				
				$modifierCodeArr1[]					= $modifierCodeArr[$modifier_id]["modifier_code"];
				$modifierCodeArr2[]					= $modifierCodeArr[$modifier_id2]["modifier_code"];
				$modifierCodeArr3[]					= $modifierCodeArr[$modifier_id3]["modifier_code"];
				
				$modifierCodeNewArr[] 				= $modifierCodeArr[$modifier_id]["modifier_code"];
				$modifierCodeNewArr[] 				= $modifierCodeArr[$modifier_id2]["modifier_code"];
				$modifierCodeNewArr[] 				= $modifierCodeArr[$modifier_id3]["modifier_code"];
				$modifierCodeNewArr 				= array_filter($modifierCodeNewArr);
				$modifier_code 						= implode(",",$modifierCodeNewArr);
				$modifier_code						= str_replace(",","",$modifier_code);
				$newSpaceCpt = "";
				if(strlen($cpt4_code)<5) {  
					$totalLenCpt = 5;
					$cpt4_codeLen = strlen($cpt4_code);
					$spaceCntCpt = ($totalLenCpt - $cpt4_codeLen);
					for($z=0;$z<$spaceCntCpt;$z++) {
						$newSpaceCpt .= " ";
					}
				}
				$cpt4_code = $cpt4_code.$newSpaceCpt;
				$hcpcsRatesCodeArr[]				= trim($cpt4_code.' '.$modifier_code);

			}
			//END CODE FOR 24-D HCFA
			 
			/*
			if($row["encounter_id"]=='237408') {
				pre($paidChargesArr);
				echo count($arrpatientChargeDetails).'<br>'
				pre($dignosisArr);
			}
			*/

			$dignosisArr = array_unique($dignosisArr);
			$dignosisArr = array_values(array_filter($dignosisArr));
			$j=0;
			$diagnose_arr = array();
			foreach($dignosisArr as $key => $vals) {
				$diagnose_arr[$vals] = $j+1;
				if($vals!=""){
					$diagnose_arr[$vals] = chr($diagnose_arr[$vals]+64);	
				}
				$j++;
			}
			
			//START NEW CODE
			$content = array();
			
			$processCode = "1"; 
			$content = getValHcfaUb($content,$processCode,1,1,"right","N");
			
			$objInsGroupNumber->policy_number = preg_replace("/[^A-Za-z0-9]/","",$objInsGroupNumber->policy_number);
			$show_box11_val=$objInsGroupNumber->policy_number;
			if(in_array(strtolower($billing_global_server_name), array('manahan','heca')) && $objInsGroupNumber->group_number!=""){
				$show_box11_val=$objInsGroupNumber->group_number;
			}
			if(in_array(strtolower($billing_global_server_name), array('liesc','liasc','domi','swagelwootton','pilkintoneye'))){
				$show_box11_val=$objInsGroupNumber->group_number;
			}
			if(($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro']=='59274' || $insCompArr[$row["primaryInsuranceCoId"]]['Payer_id']=='59274') && in_array(strtolower($billing_global_server_name), array('sakowitz'))){
				$show_box11_val=$objInsGroupNumber->group_number;
			}
			if(strtolower($insHouseCode) == "medso" && in_array(strtolower($billing_global_server_name), array('witlin'))){
				$show_box11_val	=	"";
			}else if((strtolower($insHouseCode) == "affinity health plan" || strtolower($insCompArr[$row["primaryInsuranceCoId"]]['emdeon_payer_eligibility'])=='afnty') && in_array(strtolower($billing_global_server_name), array('northshore'))){
				$show_box11_val	=	"";
			}else if((in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_Medicare_payers) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],$arr_Medicare_payers)) && in_array(strtolower($billing_global_server_name), array('gewirtz','kung','desert'))){
				$show_box11_val	=	"None";
			}else if(in_array(strtolower($billing_global_server_name), array('leps'))){
				$show_box11_val	=	"";
			}else{
				if($show_box11_val==""){
					$show_box11_val	=	"None";
				}
			}
			$insuredIDNumber = $show_box11_val; //1-A OR 11 HCFA		
			$content = getValHcfaUb($content,$insuredIDNumber,2,21,"left","A");

			$patientDateOfBirth = $row["ptDobFormat"]; //3 HCFA
			$content = getValHcfaUb($content,$patientDateOfBirth,22,29,"right","N");

			$tmpPatientSex = "U";
			if(strtolower($row["ptSex"])=="male"){ 
				$tmpPatientSex = "M"; 
			}else if(strtolower($row["ptSex"])=="female") {
				$tmpPatientSex = "F"; 
			}
			$patientSex = $tmpPatientSex; //3 HCFA
			$content = getValHcfaUb($content,$patientSex,30,30,"left","A");
			
			$PatientAddressZipCode = ($row["ptPostalCode"]) ? stripslashes($row["ptPostalCode"]) : ""; //5 HCFA
			$content = getValHcfaUb($content,$PatientAddressZipCode,31,39,"left","A");

			$ins_type = $claim_type = "";
			if($row["primaryInsuranceCoId"]) {
				$ins_type 	= $insCompArr[$row["primaryInsuranceCoId"]]["ins_type"];
				$claim_type = $insCompArr[$row["primaryInsuranceCoId"]]["claim_type"];
			}
			$tmpPayerANSIcode = 'CI';
			if(!$objpriinsData) {
				$tmpPayerANSIcode = '09';	
			}else {
				if($claim_type == "1"){
					$tmpPayerANSIcode = 'MB';
				}elseif($ins_type != '' && strlen(trim($ins_type))==2){
					$tmpPayerANSIcode = strtoupper(trim($ins_type));
				}
			}

			$payerANSIcode = $tmpPayerANSIcode;
			$content = getValHcfaUb($content,$payerANSIcode,40,41,"left","A");


			$content = getValHcfaUb($content,$filler,42,44,"left","A");

			$pos_facility_name = $pro_pri_npi = $pro_sec_npi = "";
			if($row['group_institution']>0) {
				if($posFacilityId == '' || $posFacilityId == '0'){
					$posFacilityId = $row['ptDefaultFacility'];
				}
				$pos_facility_name 	= $posFacilityNameArr[$posFacilityId];
				if($pos_facility_name) {
					$pos_facility_npi = $posFacilityArr[$posFacilityId];
				}
			}
			
			if($row["primaryProviderId"]){
				$pro_pri_npi=$userArr[$row['primaryProviderId']]['user_npi'];
			}
			if($row["reff_phy_nr"]==0 && $row["primaryProviderId"]!=$row["primary_provider_id_for_reports"] && $row["primary_provider_id_for_reports"]>0){
				$pro_pri_npi=$userArr[$row['primary_provider_id_for_reports']]['user_npi'];
			}
			if($row["secondaryProviderId"]){
				$pro_sec_npi=$userArr[$row['secondaryProviderId']]['user_npi'];
			}
			
			if($pos_facility_name!=""){
				$tmpUpinNpi = $pos_facility_npi;
			}else {
				$tmpUpinNpi = $pro_pri_npi;
			}
			
			if($pos_facility_name!=""){
				$tmpUpinNpi = $pos_facility_npi;
			}else {
				$tmpUpinNpi = $pro_pri_npi;
			}
			$attendingPhysicianNPI = $tmpUpinNpi; //REQUIRED 76 UB04
			$content = getValHcfaUb($content,$attendingPhysicianNPI,45,54,"right","N");

			$enc_icd10 = $row["enc_icd10"];
			if($enc_icd10>0){
				$enc_icd10_ind="0";
			}else{
				$enc_icd10_ind="9";
			}
			$diagnosisVersionQualifier = $enc_icd10_ind; //66 UB04
			$content = getValHcfaUb($content,$diagnosisVersionQualifier,55,55,"left","A");


			$PrincipalDiagnosisCode = $dignosisArr[0]; //21-1 HCFA
			$content = getValHcfaUb($content,$PrincipalDiagnosisCode,56,63,"left","A");
			
			$otherDiagnosisCode1 = $dignosisArr[1]; //21-2 HCFA
			$content = getValHcfaUb($content,$otherDiagnosisCode1,64,71,"left","A");
			
			$otherDiagnosisCode2 = $dignosisArr[2]; //21-3 HCFA
			$content = getValHcfaUb($content,$otherDiagnosisCode2,72,79,"left","A");

			$otherDiagnosisCode3 = $dignosisArr[3]; //21-4 HCFA
			$content = getValHcfaUb($content,$otherDiagnosisCode3,80,87,"left","A");

			//RaceEthnicity REQUIRED
			$arrRace = array(
			"American Indian or Alaska Native" => 1,
			"Asian" => 2,
			"Black or African American" => 3,
			"Native Hawaiian or Other Pacific Islander" => 4,
			"Latin American" => 6,
			"White" => "5",	//Caucasian	= White								
			"Declined to Specify" => 9);
			$patientRaceExp = explode(",",$row["race"]);
			$patientRace = trim($arrRace[$patientRaceExp[0]]);
			
			if((trim($patientRace)=="" || !trim($patientRace)) && $patientRaceExp[0] != "" ) { //CODE FOR OTHER RACE
				$patientRace = "6";
			}
			if(!trim($patientRace)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Race is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}else if(trim($patientRace)==9) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Race is patient refused or unknown";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$patientRace,88,89,"left","A");
			
			if(trim($cpt4CodeArr[0]) && trim($cpt4CodeArr[0]) != "") {
				$fromDateofService1 = $dateOfServiceArr[0]; //24-A1 HCFA
				$content = getValHcfaUb($content,$fromDateofService1,90,97,"right","N");
				
				$toDateofService1 = $dateOfServiceArr[0]; //24-A1 HCFA
				$content = getValHcfaUb($content,$toDateofService1,98,105,"right","N");
	
	
				$placeOfService1 = $posCodeArr[0]; //24-B1
				$content = getValHcfaUb($content,$placeOfService1,106,107,"left","A");
	
				$typeofService1 = $typeOfServiceArr[0]; //24-C1
				$content = getValHcfaUb($content,$typeofService1,108,109,"left","A");
	
				$cptHCPCS1	= $cpt4CodeArr[0]; //24-D1
				$content = getValHcfaUb($content,$cptHCPCS1,110,114,"left","A");
	
				$modifier1_1	= $modifierCodeArr1[0]; //24-D1
				$content = getValHcfaUb($content,$modifier1_1,115,116,"left","A");
	
				$modifier2_1	= $modifierCodeArr2[0]; //24-D1
				$content = getValHcfaUb($content,$modifier2_1,117,118,"left","A");
	
				$modifier3_1	= $modifierCodeArr3[0]; //24-D1
				$content = getValHcfaUb($content,$modifier3_1,119,120,"left","A");
	
				$modifier4_1	= ""; //24-D1 NOT FOUND
				$content = getValHcfaUb($content,$modifier4_1,121,122,"left","A");
	
				$modifier5_1	= ""; //24-D1 NOT FOUND
				$content = getValHcfaUb($content,$modifier5_1,123,124,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[0])) {
					$detailCharges1						= $paidChargesArr[0]; //24-F1 HCFA
					$content 							= getValHcfaUb($content,$detailCharges1,125,134,"right","N");
					$daysOrUnits1						= $totalUnitArr[0]; //24-G1 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits1,135,136,"right","N");
				}
			}
			
			if(trim($cpt4CodeArr[1]) && trim($cpt4CodeArr[1]) != "") {
				$fromDateofService2 = $dateOfServiceArr[1]; //24-A2 HCFA
				$content = getValHcfaUb($content,$fromDateofService2,137,144,"right","N");
				
				$toDateofService2 = $dateOfServiceArr[1]; //24-A2 HCFA
				$content = getValHcfaUb($content,$toDateofService2,145,152,"right","N");
	
	
				$placeOfService2 = $posCodeArr[1]; //24-B2
				$content = getValHcfaUb($content,$placeOfService2,153,154,"left","A");
	
				$typeofService2 = $typeOfServiceArr[1]; //24-C2
				$content = getValHcfaUb($content,$typeofService2,155,156,"left","A");
	
				$cptHCPCS2	= $cpt4CodeArr[1]; //24-D2
				$content = getValHcfaUb($content,$cptHCPCS2,157,161,"left","A");
	
				$modifier1_2	= $modifierCodeArr1[1]; //24-D2
				$content = getValHcfaUb($content,$modifier1_2,162,163,"left","A");
	
				$modifier2_2	= $modifierCodeArr2[1]; //24-D2
				$content = getValHcfaUb($content,$modifier2_2,164,165,"left","A");
	
				$modifier3_2	= $modifierCodeArr3[1]; //24-D2
				$content = getValHcfaUb($content,$modifier3_2,166,167,"left","A");
	
				$modifier4_2	= ""; //24-D2 NOT FOUND
				$content = getValHcfaUb($content,$modifier4_2,168,169,"left","A");
	
				$modifier5_2	= ""; //24-D2 NOT FOUND
				$content = getValHcfaUb($content,$modifier5_2,170,171,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[1])) {
					$detailCharges2						= $paidChargesArr[1]; //24-F2 HCFA
					$content 							= getValHcfaUb($content,$detailCharges2,172,181,"right","N");
					$daysOrUnits2						= $totalUnitArr[1]; //24-G2 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits2,182,183,"right","N");
				}
			}

			if(trim($cpt4CodeArr[2]) && trim($cpt4CodeArr[2]) != "") {
				$fromDateofService3 = $dateOfServiceArr[2]; //24-A3 HCFA
				$content = getValHcfaUb($content,$fromDateofService3,184,191,"right","N");
				
				$toDateofService3 = $dateOfServiceArr[2]; //24-A3 HCFA
				$content = getValHcfaUb($content,$toDateofService3,192,199,"right","N");
	
	
				$placeOfService3 = $posCodeArr[2]; //24-B3 HCFA
				$content = getValHcfaUb($content,$placeOfService3,200,201,"left","A");
	
				$typeofService3 = $typeOfServiceArr[2]; //24-C3
				$content = getValHcfaUb($content,$typeofService3,202,203,"left","A");
	
				$cptHCPCS3	= $cpt4CodeArr[2]; //24-D3 HCFA
				$content = getValHcfaUb($content,$cptHCPCS3,204,208,"left","A");
	
				$modifier1_3	= $modifierCodeArr1[2]; //24-D3 HCFA
				$content = getValHcfaUb($content,$modifier1_3,209,210,"left","A");
	
				$modifier2_3	= $modifierCodeArr2[2]; //24-D3 HCFA
				$content = getValHcfaUb($content,$modifier2_3,211,212,"left","A");
	
				$modifier3_3	= $modifierCodeArr3[2]; //24-D3 HCFA
				$content = getValHcfaUb($content,$modifier3_3,213,214,"left","A");
	
				$modifier4_3	= ""; //24-D3 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_3,215,216,"left","A");
	
				$modifier5_3	= ""; //24-D3 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_3,217,218,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[2])) {
					$detailCharges3						= $paidChargesArr[2]; //24-F3 HCFA
					$content 							= getValHcfaUb($content,$detailCharges3,219,228,"right","N");
					$daysOrUnits3						= $totalUnitArr[2]; //24-G3 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits3,229,230,"right","N");
				}
			}

			if(trim($cpt4CodeArr[3]) && trim($cpt4CodeArr[3]) != "") {
				$fromDateofService4 = $dateOfServiceArr[3]; //24-A4 HCFA
				$content = getValHcfaUb($content,$fromDateofService4,231,238,"right","N");
				
				$toDateofService4 = $dateOfServiceArr[3]; //24-A4 HCFA
				$content = getValHcfaUb($content,$toDateofService4,239,246,"right","N");
	
	
				$placeOfService4 = $posCodeArr[3]; //24-B4 HCFA
				$content = getValHcfaUb($content,$placeOfService4,247,248,"left","A");
	
				$typeofService4 = $typeOfServiceArr[3]; //24-C4
				$content = getValHcfaUb($content,$typeofService4,249,250,"left","A");
	
				$cptHCPCS4	= $cpt4CodeArr[3]; //24-D4 HCFA
				$content = getValHcfaUb($content,$cptHCPCS4,251,255,"left","A");
	
				$modifier1_4	= $modifierCodeArr1[3]; //24-D4 HCFA
				$content = getValHcfaUb($content,$modifier1_4,256,257,"left","A");
	
				$modifier2_4	= $modifierCodeArr2[3]; //24-D4 HCFA
				$content = getValHcfaUb($content,$modifier2_4,258,259,"left","A");
	
				$modifier3_4	= $modifierCodeArr3[3]; //24-D4 HCFA
				$content = getValHcfaUb($content,$modifier3_4,260,261,"left","A");
	
				$modifier4_4	= ""; //24-D4 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_4,262,263,"left","A");
	
				$modifier5_4	= ""; //24-D4 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_4,264,265,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[3])) {
					$detailCharges4						= $paidChargesArr[3]; //24-F4 HCFA
					$content 							= getValHcfaUb($content,$detailCharges4,266,275,"right","N");
					$daysOrUnits4						= $totalUnitArr[3]; //24-G4 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits4,276,277,"right","N");
				}
			}

			if(trim($cpt4CodeArr[4]) && trim($cpt4CodeArr[4]) != "") {
				$fromDateofService5 = $dateOfServiceArr[4]; //24-A5 HCFA
				$content = getValHcfaUb($content,$fromDateofService5,278,285,"right","N");
				
				$toDateofService5 = $dateOfServiceArr[4]; //24-A5 HCFA
				$content = getValHcfaUb($content,$toDateofService5,286,293,"right","N");
	
	
				$placeOfService5 = $posCodeArr[4]; //24-B5 HCFA
				$content = getValHcfaUb($content,$placeOfService5,294,295,"left","A");
	
				$typeofService5 = $typeOfServiceArr[4]; //24-C5
				$content = getValHcfaUb($content,$typeofService5,296,297,"left","A");
	
				$cptHCPCS5	= $cpt4CodeArr[4]; //24-D5 HCFA
				$content = getValHcfaUb($content,$cptHCPCS5,298,302,"left","A");
	
				$modifier1_5	= $modifierCodeArr1[4]; //24-D5 HCFA
				$content = getValHcfaUb($content,$modifier1_5,303,304,"left","A");
	
				$modifier2_5	= $modifierCodeArr2[4]; //24-D5 HCFA
				$content = getValHcfaUb($content,$modifier2_5,305,306,"left","A");
	
				$modifier3_5	= $modifierCodeArr3[4]; //24-D5 HCFA
				$content = getValHcfaUb($content,$modifier3_5,307,308,"left","A");
	
				$modifier4_5	= ""; //24-D5 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_5,309,310,"left","A");
	
				$modifier5_5	= ""; //24-D5 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_5,311,312,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[4])) {
					$detailCharges5						= $paidChargesArr[4]; //24-F5 HCFA
					$content 							= getValHcfaUb($content,$detailCharges5,313,322,"right","N");
					$daysOrUnits5						= $totalUnitArr[5]; //24-G5 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits5,323,324,"right","N");
				}
			}

			if(trim($cpt4CodeArr[5]) && trim($cpt4CodeArr[5]) != "") {
				$fromDateofService6 = $dateOfServiceArr[5]; //24-A6 HCFA
				$content = getValHcfaUb($content,$fromDateofService6,325,332,"right","N");
				
				$toDateofService6 = $dateOfServiceArr[5]; //24-A6 HCFA
				$content = getValHcfaUb($content,$toDateofService6,333,340,"right","N");
	
	
				$placeOfService6 = $posCodeArr[5]; //24-B6 HCFA
				$content = getValHcfaUb($content,$placeOfService6,341,342,"left","A");
	
				$typeofService6 = $typeOfServiceArr[5]; //24-C6
				$content = getValHcfaUb($content,$typeofService6,343,344,"left","A");
	
				$cptHCPCS6	= $cpt4CodeArr[5]; //24-D6 HCFA
				$content = getValHcfaUb($content,$cptHCPCS6,345,349,"left","A");
	
				$modifier1_6	= $modifierCodeArr1[5]; //24-D6 HCFA
				$content = getValHcfaUb($content,$modifier1_6,350,351,"left","A");
	
				$modifier2_6	= $modifierCodeArr2[5]; //24-D6 HCFA
				$content = getValHcfaUb($content,$modifier2_6,352,353,"left","A");
	
				$modifier3_6	= $modifierCodeArr3[5]; //24-D6 HCFA
				$content = getValHcfaUb($content,$modifier3_6,354,355,"left","A");
	
				$modifier4_6	= ""; //24-D6 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_6,356,357,"left","A");
	
				$modifier5_6	= ""; //24-D6 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_6,358,359,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[5])) {
					$detailCharges6						= $paidChargesArr[5]; //24-F6 HCFA
					$content 							= getValHcfaUb($content,$detailCharges6,360,369,"right","N");
					$daysOrUnits6						= $totalUnitArr[5]; //24-G6 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits6,370,371,"right","N");
				}
			}

			if(trim($cpt4CodeArr[6]) && trim($cpt4CodeArr[6]) != "") {
				$fromDateofService7 = $dateOfServiceArr[6]; //24-A7 HCFA
				$content = getValHcfaUb($content,$fromDateofService7,372,379,"right","N");
				
				$toDateofService7 = $dateOfServiceArr[6]; //24-A7 HCFA
				$content = getValHcfaUb($content,$toDateofService7,380,387,"right","N");
	
	
				$placeOfService7 = $posCodeArr[6]; //24-B7 HCFA
				$content = getValHcfaUb($content,$placeOfService7,388,389,"left","A");
	
				$typeofService7 = $typeOfServiceArr[6]; //24-C7
				$content = getValHcfaUb($content,$typeofService7,390,391,"left","A");
	
				$cptHCPCS7	= $cpt4CodeArr[6]; //24-D7 HCFA
				$content = getValHcfaUb($content,$cptHCPCS7,392,396,"left","A");
	
				$modifier1_7	= $modifierCodeArr1[6]; //24-D7 HCFA
				$content = getValHcfaUb($content,$modifier1_7,397,398,"left","A");
	
				$modifier2_7	= $modifierCodeArr2[6]; //24-D7 HCFA
				$content = getValHcfaUb($content,$modifier2_7,399,400,"left","A");
	
				$modifier3_7	= $modifierCodeArr3[6]; //24-D7 HCFA
				$content = getValHcfaUb($content,$modifier3_7,401,402,"left","A");
	
				$modifier4_7	= ""; //24-D7 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_7,403,404,"left","A");
	
				$modifier5_7	= ""; //24-D7 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_7,405,406,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[6])) {
					$detailCharges7						= $paidChargesArr[6]; //24-F7 HCFA
					$content 							= getValHcfaUb($content,$detailCharges7,407,416,"right","N");
					$daysOrUnits7						= $totalUnitArr[6]; //24-G7 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits7,417,418,"right","N");
				}
			}

			if(trim($cpt4CodeArr[7]) && trim($cpt4CodeArr[7]) != "") {
				$fromDateofService8 = $dateOfServiceArr[7]; //24-A8 HCFA
				$content = getValHcfaUb($content,$fromDateofService8,419,426,"right","N");
				
				$toDateofService8 = $dateOfServiceArr[7]; //24-A8 HCFA
				$content = getValHcfaUb($content,$toDateofService8,427,434,"right","N");
	
	
				$placeOfService8 = $posCodeArr[7]; //24-B8 HCFA
				$content = getValHcfaUb($content,$placeOfService8,435,436,"left","A");
	
				$typeofService8 = $typeOfServiceArr[7]; //24-C8
				$content = getValHcfaUb($content,$typeofService8,437,438,"left","A");
	
				$cptHCPCS8	= $cpt4CodeArr[7]; //24-D8 HCFA
				$content = getValHcfaUb($content,$cptHCPCS8,439,443,"left","A");
	
				$modifier1_8	= $modifierCodeArr1[7]; //24-D8 HCFA
				$content = getValHcfaUb($content,$modifier1_8,444,445,"left","A");
	
				$modifier2_8	= $modifierCodeArr2[7]; //24-D8 HCFA
				$content = getValHcfaUb($content,$modifier2_8,446,447,"left","A");
	
				$modifier3_8	= $modifierCodeArr3[7]; //24-D8 HCFA
				$content = getValHcfaUb($content,$modifier3_8,448,449,"left","A");
	
				$modifier4_8	= ""; //24-D8 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_8,450,451,"left","A");
	
				$modifier5_8	= ""; //24-D8 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_8,452,453,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[7])) {
					$detailCharges8						= $paidChargesArr[7]; //24-F8 HCFA
					$content 							= getValHcfaUb($content,$detailCharges8,454,463,"right","N");
					$daysOrUnits8						= $totalUnitArr[7]; //24-G8 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits8,464,465,"right","N");
				}
			}

			if(trim($cpt4CodeArr[8]) && trim($cpt4CodeArr[8]) != "") {
				$fromDateofService9 = $dateOfServiceArr[8]; //24-A9 HCFA
				$content = getValHcfaUb($content,$fromDateofService9,466,473,"right","N");
				
				$toDateofService9 = $dateOfServiceArr[8]; //24-A9 HCFA
				$content = getValHcfaUb($content,$toDateofService9,474,481,"right","N");
	
	
				$placeOfService9 = $posCodeArr[8]; //24-B9 HCFA
				$content = getValHcfaUb($content,$placeOfService9,482,483,"left","A");
	
				$typeofService9 = $typeOfServiceArr[8]; //24-C9
				$content = getValHcfaUb($content,$typeofService9,484,485,"left","A");
	
				$cptHCPCS9	= $cpt4CodeArr[8]; //24-D9 HCFA
				$content = getValHcfaUb($content,$cptHCPCS9,486,490,"left","A");
	
				$modifier1_9	= $modifierCodeArr1[8]; //24-D9 HCFA
				$content = getValHcfaUb($content,$modifier1_9,491,492,"left","A");
	
				$modifier2_9	= $modifierCodeArr2[8]; //24-D9 HCFA
				$content = getValHcfaUb($content,$modifier2_9,493,494,"left","A");
	
				$modifier3_9	= $modifierCodeArr3[8]; //24-D9 HCFA
				$content = getValHcfaUb($content,$modifier3_9,495,496,"left","A");
	
				$modifier4_9	= ""; //24-D9 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_9,497,498,"left","A");
	
				$modifier5_9	= ""; //24-D9 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_9,499,500,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[8])) {
					$detailCharges9						= $paidChargesArr[8]; //24-F9 HCFA
					$content 							= getValHcfaUb($content,$detailCharges9,501,510,"right","N");
					$daysOrUnits9						= $totalUnitArr[8]; //24-G9 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits9,511,512,"right","N");
				}
			}

			if(trim($cpt4CodeArr[9]) && trim($cpt4CodeArr[9]) != "") {
				$fromDateofService10 = $dateOfServiceArr[9]; //24-A10 HCFA
				$content = getValHcfaUb($content,$fromDateofService10,513,520,"right","N");
				
				$toDateofService10 = $dateOfServiceArr[9]; //24-A10 HCFA
				$content = getValHcfaUb($content,$toDateofService10,521,528,"right","N");
	
	
				$placeOfService10 = $posCodeArr[9]; //24-B10 HCFA
				$content = getValHcfaUb($content,$placeOfService10,529,530,"left","A");
	
				$typeofService10 = $typeOfServiceArr[9]; //24-C10
				$content = getValHcfaUb($content,$typeofService10,531,532,"left","A");
	
				$cptHCPCS10	= $cpt4CodeArr[9]; //24-D10 HCFA
				$content = getValHcfaUb($content,$cptHCPCS10,533,537,"left","A");
	
				$modifier1_10	= $modifierCodeArr1[9]; //24-D10 HCFA
				$content = getValHcfaUb($content,$modifier1_10,538,539,"left","A");
	
				$modifier2_10	= $modifierCodeArr2[9]; //24-D10 HCFA
				$content = getValHcfaUb($content,$modifier2_10,540,541,"left","A");
	
				$modifier3_10	= $modifierCodeArr3[9]; //24-D10 HCFA
				$content = getValHcfaUb($content,$modifier3_10,542,543,"left","A");
	
				$modifier4_10	= ""; //24-D10 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_10,544,545,"left","A");
	
				$modifier5_10	= ""; //24-D10 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_10,546,547,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[9])) {
					$detailCharges10					= $paidChargesArr[9]; //24-F10 HCFA
					$content 							= getValHcfaUb($content,$detailCharges10,548,557,"right","N");
					$daysOrUnits10						= $totalUnitArr[9]; //24-G10 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits10,558,559,"right","N");
				}
			}

			if(trim($cpt4CodeArr[10]) && trim($cpt4CodeArr[10]) != "") {
				$fromDateofService11 = $dateOfServiceArr[10]; //24-A11 HCFA
				$content = getValHcfaUb($content,$fromDateofService11,560,567,"right","N");
				
				$toDateofService11 = $dateOfServiceArr[10]; //24-A11 HCFA
				$content = getValHcfaUb($content,$toDateofService11,568,575,"right","N");
	
	
				$placeOfService11 = $posCodeArr[10]; //24-B11 HCFA
				$content = getValHcfaUb($content,$placeOfService11,576,577,"left","A");
	
				$typeofService11 = $typeOfServiceArr[10]; //24-C11
				$content = getValHcfaUb($content,$typeofService11,578,579,"left","A");
	
				$cptHCPCS11	= $cpt4CodeArr[10]; //24-D11 HCFA
				$content = getValHcfaUb($content,$cptHCPCS11,580,584,"left","A");
	
				$modifier1_11	= $modifierCodeArr1[10]; //24-D11 HCFA
				$content = getValHcfaUb($content,$modifier1_11,585,586,"left","A");
	
				$modifier2_11	= $modifierCodeArr2[10]; //24-D11 HCFA
				$content = getValHcfaUb($content,$modifier2_11,587,588,"left","A");
	
				$modifier3_11	= $modifierCodeArr3[10]; //24-D11 HCFA
				$content = getValHcfaUb($content,$modifier3_11,589,590,"left","A");
	
				$modifier4_11	= ""; //24-D11 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_11,591,592,"left","A");
	
				$modifier5_11	= ""; //24-D11 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_11,593,594,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[10])) {
					$detailCharges11					= $paidChargesArr[10]; //24-F11 HCFA
					$content 							= getValHcfaUb($content,$detailCharges11,595,604,"right","N");
					$daysOrUnits11						= $totalUnitArr[10]; //24-G11 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits11,605,606,"right","N");
				}
			}

			if(trim($cpt4CodeArr[11]) && trim($cpt4CodeArr[11]) != "") {
				$fromDateofService12 = $dateOfServiceArr[11]; //24-A12 HCFA
				$content = getValHcfaUb($content,$fromDateofService12,607,614,"right","N");
				
				$toDateofService12 = $dateOfServiceArr[11]; //24-A12 HCFA
				$content = getValHcfaUb($content,$toDateofService12,615,622,"right","N");
	
	
				$placeOfService12 = $posCodeArr[11]; //24-B12 HCFA
				$content = getValHcfaUb($content,$placeOfService12,623,624,"left","A");
	
				$typeofService12 = $typeOfServiceArr[11]; //24-C12
				$content = getValHcfaUb($content,$typeofService12,625,626,"left","A");
	
				$cptHCPCS12	= $cpt4CodeArr[11]; //24-D12 HCFA
				$content = getValHcfaUb($content,$cptHCPCS12,627,631,"left","A");
	
				$modifier1_12	= $modifierCodeArr1[11]; //24-D12 HCFA
				$content = getValHcfaUb($content,$modifier1_12,632,633,"left","A");
	
				$modifier2_12	= $modifierCodeArr2[11]; //24-D12 HCFA
				$content = getValHcfaUb($content,$modifier2_12,634,635,"left","A");
	
				$modifier3_12	= $modifierCodeArr3[11]; //24-D12 HCFA
				$content = getValHcfaUb($content,$modifier3_12,636,637,"left","A");
	
				$modifier4_12	= ""; //24-D12 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier4_12,638,639,"left","A");
	
				$modifier5_12	= ""; //24-D12 HCFA NOT FOUND
				$content = getValHcfaUb($content,$modifier5_12,640,641,"left","A");
	
	
				if(trim($hcpcsRatesCodeArr[11])) {
					$detailCharges12					= $paidChargesArr[11]; //24-F12 HCFA
					$content 							= getValHcfaUb($content,$detailCharges12,642,651,"right","N");
					$daysOrUnits12						= $totalUnitArr[11]; //24-G12 HCFA
					$content 							= getValHcfaUb($content,$daysOrUnits12,652,653,"right","N");
				}
			}

			$federalTaxIdNumber 					= preg_replace("/[^A-Za-z0-9]/","",$row["group_Federal_EIN"]); //25 HCFA
			$content 								= getValHcfaUb($content,$federalTaxIdNumber,654,663,"left","A");

			$patientAccountNumber 					= $row["encounter_id"]; //$row["patient_id"]; //26 HCFA
			$content 								= getValHcfaUb($content,$patientAccountNumber,664,683,"left","A");
			

			if(trim($sub_total_proc_charges)) {
				$sub_total_proc_charges = number_format($sub_total_proc_charges,2);
				$sub_total_proc_charges = str_ireplace('.','',$sub_total_proc_charges);
				$sub_total_proc_charges = str_ireplace(',','',$sub_total_proc_charges);
			}else {
				$sub_total_proc_charges = "0";	
			}
			$totalCharges = $sub_total_proc_charges; //28
			//echo '<br>'.$tmpTotalNumberofClaims.'@@'.$tmpTotalChargesSubmitted.' + '.$totalCharges.' = ';
			$tmpTotalChargesSubmitted += $totalCharges;
			//echo $tmpTotalChargesSubmitted;
			if(!trim($totalCharges)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Sum of individual charges do not add up to total charges";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$totalCharges,684,693,"right","N"); //28 HCFA

			$arrEthnicity = array(
			"African Americans" => "1",
			"American" => "1",
			"American Indians" => "1",
			"Chinese" => "1",
			"European Americans" => "1",
			"Hispanic or Latino" => "2",
			"Jewish" => "1",
			"Not Hispanic or Latino" => "1",
			"Unknown" => "1",
			"Declined to Specify" => "9");
			$patientEthnicityeExp 	= explode(",",$row["ethnicity"]);
			$tmpPatientEthnicity 	= trim($arrEthnicity[$patientEthnicityeExp[0]]); //? trim($arrEthnicity[$patientEthnicityeExp[0]]) : "E8";
			if(trim($patientEthnicityeExp[0])!="" && trim($tmpPatientEthnicity)=="") {
				$tmpPatientEthnicity = "1";	//IF ETHNICITY EXIST BUT NOT IN ARRAY THEN SET ALL TO NON HISPANIC
				if(stristr(trim(strtolower($patientEthnicityeExp[0])),'hispanic')) {
					$tmpPatientEthnicity = "2";
				}
			}
			$patientEthnicity = $tmpPatientEthnicity;
			$content 								= getValHcfaUb($content,$patientEthnicity,694,694,"left","A");

			if(!trim($patientEthnicity) || trim($patientEthnicity)=="") {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Ethnicity is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}else if(trim($patientEthnicity)==9) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Ethnicity is patient refused or unknown";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}

			/*
			if($row["encounter_id"]=='237408') {
				if(!trim($patientEthnicity)) {
					pre($errorMsgArr);	
				}
			}
			*/

			$content = getValHcfaUb($content,$filler,695,702,"left","A");


			//END NEW CODE

			//END PATIENT INFORMATION
			
			
			//pre($content);die;
			$max = max(array_keys($content));
			$maxCnt = $max+1;
			for($k=0;$k<$maxCnt;$k++) {
				$val = $content[$k];
				//if(trim($val)=="") { $val = "&nbsp;"; }
				if(trim($val)=="") { $val = " "; }
				$newContent .= $val;
			}
			$c++;
			$content = array();
		}
	}
}

//START CODE FOR HEADER CONTENT
$contentHeader = array();
$newContentHeader = "";
$headerIdentification = "**********"; 
$contentHeader = getValHcfaUb($contentHeader,$headerIdentification,1,10,"left","A");

$baseProviderNumber = ""; //NOT FOUND
$contentHeader = getValHcfaUb($contentHeader,$baseProviderNumber,11,17,"left","A");

$providerSpecialtyCode = ""; //NOT FOUND
$contentHeader = getValHcfaUb($contentHeader,$providerSpecialtyCode,18,18,"left","A");

$totalNumberofClaims = $tmpTotalNumberofClaims;
$contentHeader = getValHcfaUb($contentHeader,$totalNumberofClaims,19,25,"right","N");

$totalChargesSubmitted = $tmpTotalChargesSubmitted;
$contentHeader = getValHcfaUb($contentHeader,$totalChargesSubmitted,26,35,"right","N");

$batchTypeIndicator = "A"; 
$contentHeader = getValHcfaUb($contentHeader,$batchTypeIndicator,36,36,"left","A");

$claimTypeIndicator = "H"; 
$contentHeader = getValHcfaUb($contentHeader,$claimTypeIndicator,37,37,"left","A");

$version = "A1"; 
$contentHeader = getValHcfaUb($contentHeader,$version,38,39,"left","A");

$contentHeader = getValHcfaUb($contentHeader,$filler,40,702,"left","A");


$maxHeader = max(array_keys($contentHeader));
$maxCntHeader = $maxHeader+1;
for($kHeader=0;$kHeader<$maxCntHeader;$kHeader++) {
	$valHeader = $contentHeader[$kHeader];
	if(trim($valHeader)=="") { $valHeader = " "; }
	$newContentHeader .= $valHeader;
}
if(trim($newContentHeader)!="") {
	$newContentHeader .= "\n";
	//echo $newContentHeader;die;
}
//END CODE FOR HEADER CONTENT

$show_msg= "";
if(trim($newContent)) {
	$newContent = $newContentHeader.$newContent;
	if(!is_dir($updir."/state_report")){
		//Create patient directory
		mkdir($updir."/state_report", 0777,true);
	}
	$contentFileName = $updir."/state_report/nc_".$report_type.'_'.$_SESSION["authId"].".txt";
	if(file_exists($contentFileName)) {
		unlink($contentFileName);	
	}
	file_put_contents($contentFileName,$newContent);	
	
	$show_msg= "<span style='font-family:verdana;font-size:12px;font-weight:bold; padding-left:100px;'> NC State Report ".$reportName." have been exported successfully.</span>";
	$show_msg.="<b> <a href='file_save_export.php?fn=".$contentFileName."' style='font-family:verdana;font-size:12px;font-weight:bold;color:#FF0000;' > Click here to download file</a></b>";
}else {
	$show_msg= '<div class="text-center alert alert-info">No Record Exists.</div>';
}
echo $show_msg;
//pre($errorMsgArr);
$page_data = '';
if(count($errorMsgArr)>0) {
	$page_data .= '
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
		<tr>
			<td class="rptbx1" style="width:33%">OUTPATIENT ERROR DETAIL REPORT</td>
			<td class="rptbx2" style="text-align:center; width:33%">Discharge Period From : '.$Start_date.' to '.$End_date.'</td>
			<td class="rptbx3" style="text-align:center; width:33%">
				Created By '.$createdBy.' on '.$curDate.'
			</td>
		</tr>
	</table>	
	<div id="csvFileDataTable" style="height:400px; overflow:auto; overflow-x:hidden;">
		<table class="rpt_table rpt rpt_table-bordered">';
	foreach($errorMsgArr as $ptIdKey => $ptIdArr) {
		//pre($ptIdArr);
		$errorPtId 		= $ptIdKey;
		$errorPtName	= $errorMsgArr[$ptIdKey]["ptName"][0];
		$errorDob 		= ($errorMsgArr[$ptIdKey]["dob"][0]!="00-00-0000") ? $errorMsgArr[$ptIdKey]["dob"][0] : "&nbsp;&nbsp;";
		$errorDos 		= ($errorMsgArr[$ptIdKey]["dos"][0]!="00-00-0000") ? $errorMsgArr[$ptIdKey]["dos"][0] : "&nbsp;&nbsp;";
		$errorAdmitDate	= ($errorMsgArr[$ptIdKey]["admitDate"][0]!="00-00-0000") ? $errorMsgArr[$ptIdKey]["admitDate"][0] : "&nbsp;&nbsp;";
		$page_data .= '<tr>
						<td class="text_b_w alignCenter" style="width:200px;">Patient-ID: '.$errorPtId.'</td>
						<td class="text_b_w alignCenter" style="width:300px;">Patient Name: '.$errorPtName.'</td>
						<td class="text_b_w alignCenter" style="width:250px;">Birth Date: '.$errorDob.'</td>
						<td class="text_b_w alignCenter" style="width:240px;">DOS: '.$errorDos.'</td>
						<td class="text_b_w alignCenter" style="width:200px;">Admit Date: '.$errorAdmitDate.'</td>
					</tr>
					<tr>
						<td class="text_b_w alignCenter">Error Type F/W </td>
						<td class="text_b_w alignCenter">Med Rec (Encounter ID)</td>
						<td colspan="3" class="text_b_w alignCenter">Error Message</td>
					</tr>
					';						
		$n=0;
		foreach($ptIdArr as $ptColumns => $ptRowArr) {
			$errorType 				= trim($errorMsgArr[$ptIdKey]["type"][$n]);
			$errorEncounterId 		= trim($errorMsgArr[$ptIdKey]["encounterId"][$n]);
			$errorMsg 				= trim($errorMsgArr[$ptIdKey]["msg"][$n]);
			
			$n++;
			if($errorMsg) {
				$page_data .= '
					<tr>
						<td class="white" style="padding-left:30px;">'.$errorType.'</td>
						<td class="white" style="padding-left:30px;">'.$errorEncounterId.'</td>
						<td colspan="3" class="white" style="padding-left:5px;">'.$errorMsg.'</td>
					</tr>';
			}
		}
		
		
	}
	$page_data .= '</table></div>';	
}
echo $page_data;

?>
