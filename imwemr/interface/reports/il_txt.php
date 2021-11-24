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
File: il_txt.php
Purpose: To Export IL In Fixed Length Format.
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
$insCompRes=imw_query("select id, `name`, in_house_code, Payer_id_pro, Payer_id, emdeon_payer_eligibility, ins_state_payer_code, payer_mapping_code from insurance_companies ORDER BY id");
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

//START CODE OF REVENUE CODE
$rCodeArr = array();
$revCodeRes=imw_query("select r_id, r_code from revenue_code ORDER BY r_code");
if(imw_num_rows($revCodeRes)>0) {
	while($revCodeRow=imw_fetch_array($revCodeRes)) {
		$rCodeId = $revCodeRow["r_id"];
		$rCodeArr[$rCodeId] = $revCodeRow["r_code"];	
	}
}
//END CODE OF REVENUE CODE

//START CODE OF USER NPI
$userNpiArr = array();
$userNpiRes=imw_query("select id, user_npi, licence from users ORDER BY id");
if(imw_num_rows($userNpiRes)>0) {
	while($userNpiRow=imw_fetch_array($userNpiRes)) {
		$uId = $userNpiRow["id"];
		$userNpiArr[$uId] = $userNpiRow["user_npi"];
		$userLicenseArr[$uId] = $userNpiRow["licence"];	
	}
}
//END CODE OF USER NPI


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
		if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%H'),'') AS admit_hour_format, if(p.disch_date != '0000-00-00 00:00:00', DATE_FORMAT(p.disch_date, '%H'),'') AS disch_hour_format,
		if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m%d%Y'),'') as date_of_service_format,
		if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m%d%y'),'') as date_of_service_format_short, if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m-%d-%Y'),'') as date_of_service_format_new,
		gn.group_Federal_EIN, gn.group_NPI, gn.group_institution, gn.name as groupName, 
		pd.city AS ptCity, pd.state AS ptState, pd.postal_code AS ptPostalCode, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m%d%Y'),'') AS ptDobFormat, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m-%d-%Y'),'') AS ptDobFormatNew,
		pd.sex AS ptSex, pd.ss AS patientSSN, pd.race, pd.ethnicity, pd.street AS ptStreet, pd.street2 AS ptStreet2,pd.fname AS ptFname, pd.lname AS ptLname, pd.suffix AS ptSuffix,pd.mname AS ptMname, TRIM(CONCAT(pd.lname,', ',pd.fname,' ',pd.mname)) AS ptName, 
		pd.default_facility AS ptDefaultFacility, pd.country_code AS ptCountryCode, pd.status AS maritalStatus, pd.occupation AS ptOccupation, 
		pd.primary_care_id AS ptPrimaryCareId, pd.zip_ext  AS ptZip_ext 
		FROM patient_charge_list p
		
		INNER JOIN patient_data pd ON(pd.pid = p.patient_id)
		LEFT JOIN insurance_companies inc ON (p.primaryInsuranceCoId = inc.id   ".$andInstitueTypeQry.")
		INNER JOIN submited_record sr ON(sr.encounter_id = p.encounter_id)
		LEFT JOIN groups_new gn ON(gn.gro_id=p.gro_id)
		WHERE p.charge_list_id !='0' and p.del_status='0' and p.enc_accept_assignment!='2' ".$andQry."  
		GROUP BY p.charge_list_id
		ORDER BY pd.pid ";

$filler = ""; //NOT FOUND		
$errorMsgArr = array();
$res = imw_query($qry) or die($qry.imw_error());
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
	
			$objInsGroupNumber = $objSecondaryInsGroupNumber = $objTertiaryInsGroupNumber = "";
			$secondryFlag = false;
			if($row["patient_id"]){
				if($row["case_type_id"]){
					$objInsGroupNumber = getInsGroupNumberState($row["case_type_id"],$row["patient_id"],'primary',$row["date_of_service"],'1');				
					if($row["secondaryInsuranceCoId"]){
						$objSecondaryInsGroupNumber = getInsGroupNumberState($row["case_type_id"],$row["patient_id"],'secondary',$row["date_of_service"],'1');
						$secondryFlag = true;
					}
					if($row["tertiaryInsuranceCoId"]){
						if($secondryFlag == false) {
							$objSecondaryInsGroupNumber	= getInsGroupNumberState($row["case_type_id"],$row["patient_id"],'tertiary',$row["date_of_service"],'1');
						}else {
							$objTertiaryInsGroupNumber 	= getInsGroupNumberState($row["case_type_id"],$row["patient_id"],'tertiary',$row["date_of_service"],'1');
						}
						
					}
				}
			}
	
			$dignosisArr = $dignosisArrNew = $cpt4CodeArr = $dateOfServiceArr = $paidChargesArr = $totalUnitArr = $hcpcsRatesCodeArr = $revenueCodesArr = array();
			$sub_total_proc_charges = 0;
			for($d = 0;$d< count($arrpatientChargeDetails);$d++){
				$charge_list_detail_id = $arrpatientChargeDetails[$d]['charge_list_detail_id'];
				
				//REVENUE CODE
				$rev_cod			= $arrpatientChargeDetails[$d]['rev_code'];
				$revenueCodesArr[]	= $rCodeArr[$rev_cod];
				
				
				//DIAGNOSIS CODE
				for($f=1;$f<=24;$f++){
					if($arrpatientChargeDetails[$d]['diagnosis_id'.$f]!=""){
						$diagId = $arrpatientChargeDetails[$d]['diagnosis_id'.$f];
						$diagId = preg_replace("/[^A-Za-z0-9]/","",$diagId);
						$dignosisArr[] = $diagId;
						$dignosisArrNew[$d][] = $diagId;
					}
				}
				

				$dateOfServiceArr[] = $row["date_of_service_format_short"];//24-A HCFA
				
				//get CPT4Code //24-D HCFA
				if(strtoupper(substr($arrpatientChargeDetails[$d]['cpt4_code'],0,1)) != "G") { //DO NOT INCLUDE G-CODES
					$cpt4CodeArr[] = $arrpatientChargeDetails[$d]['cpt4_code'];
				}
				
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
					$totalcharges 		= numberformat($arrpatientChargeDetails[$d]['procCharges']* $arrpatientChargeDetails[$d]['units'],2);
					$paidCharges 		= $totalcharges;
					//$paidCharges 		= substr($paidCharges,0,-3);
					$paidCharges 		= str_ireplace('.','',$paidCharges);
					$paidCharges 		= str_ireplace(',','',$paidCharges);
					$paidCharges		= str_ireplace('$','',$paidCharges);
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
				$modifierCodeNewArr[] 				= $modifierCodeArr[$modifier_id];
				$modifierCodeNewArr[] 				= $modifierCodeArr[$modifier_id2];
				$modifierCodeNewArr[] 				= $modifierCodeArr[$modifier_id3];
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
			//END 
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
			
			//START FOR VENDOR USE
			$content = array();
			$patientDateOfBirth = $row["ptDobFormat"]; //3 HCFA
			$content = getValHcfaUb($content,$patientDateOfBirth,1,8,"right","N");
			
			$tmpPatientSex = "U";
			if(strtolower($row["ptSex"])=="male"){ 
				$tmpPatientSex = "M"; 
			}else if(strtolower($row["ptSex"])=="female") {
				$tmpPatientSex = "F"; 
			}
			$patientSex = $tmpPatientSex; //3 HCFA
			$content = getValHcfaUb($content,$patientSex,9,9,"left","A");
			$PatientAddressZipCode = ($row["ptPostalCode"]) ? stripslashes($row["ptPostalCode"]) : "00000"; //5 HCFA
			$content = getValHcfaUb($content,$PatientAddressZipCode,10,14,"right","N");
			$PatientAddressZipExt = ($row["ptZip_ext"]) ? stripslashes($row["ptZip_ext"]) : "00000"; //5 HCFA
			$content = getValHcfaUb($content,$PatientAddressZipExt,15,18,"left","A");
			
			$insName = strtoupper($insCompArr[$row["primaryInsuranceCoId"]]['name']);
			$insHouseCode = strtoupper($insCompArr[$row["primaryInsuranceCoId"]]['in_house_code']);
			preg_match('/MEDICAID/',$insHouseCode,$ins_house_code);

			$tmpFirstIndividualPayerID = $insCompArr[$row["primaryInsuranceCoId"]]['payer_mapping_code'];
			//if(!$tmpFirstIndividualPayerID) { $tmpFirstIndividualPayerID = "999999999"; }
			$firstIndividualPayerID = $tmpFirstIndividualPayerID; //1 HCFA
			$content = getValHcfaUb($content,$firstIndividualPayerID,19,27,"left","A");

			$tmpSecondIndividualPayerID = $insCompArr[$row["primaryInsuranceCoId"]]['payer_mapping_code'];
			if(!$tmpSecondIndividualPayerID) { $tmpSecondIndividualPayerID = "999999999"; }
			$secondIndividualPayerID = $tmpSecondIndividualPayerID; //1 HCFA
			$content = getValHcfaUb($content,$secondIndividualPayerID,28,36,"left","A");

			$tmpThirdIndividualPayerID = $insCompArr[$row["primaryInsuranceCoId"]]['payer_mapping_code'];
			if(!$tmpThirdIndividualPayerID) { $tmpThirdIndividualPayerID = "999999999"; }
			$thirdIndividualPayerID = $tmpThirdIndividualPayerID; //1 HCFA
			$content = getValHcfaUb($content,$thirdIndividualPayerID,37,45,"left","A");

			$dateOfAdmission = $row["date_of_service_format_short"]; //24-A HCFA
			$content = getValHcfaUb($content,$dateOfAdmission,46,51,"right","N");
			
			$pointOfOrigin = "9"; 
			$content = getValHcfaUb($content,$pointOfOrigin,52,52,"left","A");

			$typeOfVisit = "3";
			$content = getValHcfaUb($content,$typeOfVisit,53,53,"right","N");

			$typeOfBill = "831"; //REQUIRED
			$content = getValHcfaUb($content,$typeOfBill,54,56,"right","N");

			$principalDiagnosis = $dignosisArr[0]; //21 HCFA
			$content = getValHcfaUb($content,$principalDiagnosis,57,64,"left","A");
			$otherDiagStartPos = 0;
			for($k=1;$k<=8;$k++) {
				$otherDiagnosisCode = $dignosisArr[$k];
				$otherDiagStartPos 	= $otherDiagStartPos ? ($otherDiagStartPos+8) : 65;
				$otherDiagEndPos 	= ($otherDiagStartPos+7);
				$content 			= getValHcfaUb($content,$otherDiagnosisCode,$otherDiagStartPos,$otherDiagEndPos,"left","A");//21 HCFA
			}
			
			$content 								= getValHcfaUb($content,$filler,129,129,"left","A");
			$content 								= getValHcfaUb($content,$filler,130,143,"left","A");
			$content 								= getValHcfaUb($content,$filler,144,149,"left","A");
			
			$patientDischargeStatus 				= "01"; //REQUIRED
			$content 								= getValHcfaUb($content,$patientDischargeStatus,150,151,"left","A");

			$content 								= getValHcfaUb($content,$filler,152,165,"left","A");
			$content 								= getValHcfaUb($content,$filler,166,171,"left","A");
			$content 								= getValHcfaUb($content,$filler,172,185,"left","A");
			$content 								= getValHcfaUb($content,$filler,186,191,"left","A");
			$content 								= getValHcfaUb($content,$filler,192,205,"left","A");
			$content 								= getValHcfaUb($content,$filler,206,211,"left","A");
			$content 								= getValHcfaUb($content,$filler,212,225,"left","A");
			$content 								= getValHcfaUb($content,$filler,226,231,"left","A");
			$content 								= getValHcfaUb($content,$filler,232,245,"left","A");
			$content 								= getValHcfaUb($content,$filler,246,251,"left","A");
			
			if(trim($hcpcsRatesCodeArr[0])) {
				$revenueCodes1						= $revenueCodesArr[0]; //42-1 UB04
				$content 							= getValHcfaUb($content,$revenueCodes1,252,255,"right","N");
				$daysOrUnits1						= $totalUnitArr[0]; //24-G1 HCFA
				$content 							= getValHcfaUb($content,$daysOrUnits1,256,262,"right","N");
				$charges1							= $paidChargesArr[0]; //24-F1 HCFA
				$content 							= getValHcfaUb($content,$charges1,263,272,"right","N");
			}

			if(trim($hcpcsRatesCodeArr[1])) {
				$revenueCodes2						= $revenueCodesArr[1]; //42-2 UB04
				$content 							= getValHcfaUb($content,$revenueCodes2,273,276,"right","N");
				$daysOrUnits2						= $totalUnitArr[1]; //24-G2 HCFA
				$content 							= getValHcfaUb($content,$daysOrUnits2,277,283,"right","N");
				$charges2							= $paidChargesArr[1]; //24-F2 HCFA
				$content 							= getValHcfaUb($content,$charges2,284,293,"right","N");
			}

			if(trim($hcpcsRatesCodeArr[2])) {
				$revenueCodes3						= $revenueCodesArr[2]; //42-3 UB04
				$content 							= getValHcfaUb($content,$revenueCodes3,294,297,"right","N");
				$daysOrUnits3						= $totalUnitArr[2]; //24-G3 HCFA
				$content 							= getValHcfaUb($content,$daysOrUnits3,298,304,"right","N");
				$charges3							= $paidChargesArr[2]; //24-F3 HCFA
				$content 							= getValHcfaUb($content,$charges3,305,314,"right","N");
			}

			if(trim($hcpcsRatesCodeArr[3])) {
				$revenueCodes4						= $revenueCodesArr[3]; //42-4 UB04
				$content 							= getValHcfaUb($content,$revenueCodes4,315,318,"right","N");
				$daysOrUnits4						= $totalUnitArr[3]; //24-G4 HCFA
				$content 							= getValHcfaUb($content,$daysOrUnits4,319,325,"right","N");
				$charges4							= $paidChargesArr[3]; //24-F4 HCFA
				$content 							= getValHcfaUb($content,$charges4,326,335,"right","N");
			}

			if(trim($hcpcsRatesCodeArr[4])) {
				$revenueCodes5						= $revenueCodesArr[4]; //42-5 UB04
				$content 							= getValHcfaUb($content,$revenueCodes5,336,339,"right","N");
				$daysOrUnits5						= $totalUnitArr[4]; //24-G5 HCFA
				$content 							= getValHcfaUb($content,$daysOrUnits5,340,346,"right","N");
				$charges5							= $paidChargesArr[4]; //24-F5 HCFA
				$content 							= getValHcfaUb($content,$charges5,347,356,"right","N");
			}

			$revenueCodes6							= $revenueCodesArr[5]; //42-6 UB04
			$content 								= getValHcfaUb($content,$revenueCodes6,357,360,"right","N");
			$daysOrUnits6							= $totalUnitArr[5]; //24-G6 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits6,361,367,"right","N");
			$charges6								= $paidChargesArr[5]; //24-F6 HCFA
			$content 								= getValHcfaUb($content,$charges6,368,377,"right","N");

			$revenueCodes7							= $revenueCodesArr[6]; //42-7 UB04
			$content 								= getValHcfaUb($content,$revenueCodes7,378,381,"right","N");
			$daysOrUnits7							= $totalUnitArr[6]; //24-G7 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits7,382,388,"right","N");
			$charges7								= $paidChargesArr[6]; //24-F7 HCFA
			$content 								= getValHcfaUb($content,$charges7,389,398,"right","N");

			$revenueCodes8							= $revenueCodesArr[7]; //42-8 UB04
			$content 								= getValHcfaUb($content,$revenueCodes8,399,402,"right","N");
			$daysOrUnits8							= $totalUnitArr[7]; //24-G8 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits8,403,409,"right","N");
			$charges8								= $paidChargesArr[7]; //24-F8 HCFA
			$content 								= getValHcfaUb($content,$charges8,410,419,"right","N");

			$revenueCodes9							= $revenueCodesArr[8]; //42-9 UB04
			$content 								= getValHcfaUb($content,$revenueCodes9,420,423,"right","N");
			$daysOrUnits9							= $totalUnitArr[8]; //24-G9 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits9,424,430,"right","N");
			$charges9								= $paidChargesArr[8]; //24-F9 HCFA
			$content 								= getValHcfaUb($content,$charges9,431,440,"right","N");

			$revenueCodes10							= $revenueCodesArr[9]; //42-10 UB04
			$content 								= getValHcfaUb($content,$revenueCodes10,441,444,"right","N");
			$daysOrUnits10							= $totalUnitArr[9]; //24-G10 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits10,445,451,"right","N");
			$charges10								= $paidChargesArr[9]; //24-F10 HCFA
			$content 								= getValHcfaUb($content,$charges10,452,461,"right","N");

			$revenueCodes11							= $revenueCodesArr[10]; //42-11 UB04
			$content 								= getValHcfaUb($content,$revenueCodes11,462,465,"right","N");
			$daysOrUnits11							= $totalUnitArr[10]; //24-G11 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits11,466,472,"right","N");
			$charges11								= $paidChargesArr[10]; //24-F11 HCFA
			$content 								= getValHcfaUb($content,$charges11,473,482,"right","N");

			$revenueCodes12							= $revenueCodesArr[11]; //42-12 UB04
			$content 								= getValHcfaUb($content,$revenueCodes12,483,486,"right","N");
			$daysOrUnits12							= $totalUnitArr[11]; //24-G12 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits12,487,493,"right","N");
			$charges12								= $paidChargesArr[11]; //24-F12 HCFA
			$content 								= getValHcfaUb($content,$charges12,494,503,"right","N");

			$revenueCodes13							= $revenueCodesArr[12]; //42-13 UB04
			$content 								= getValHcfaUb($content,$revenueCodes13,504,507,"right","N");
			$daysOrUnits13							= $totalUnitArr[12]; //24-G13 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits13,508,514,"right","N");
			$charges13								= $paidChargesArr[12]; //24-F13 HCFA
			$content 								= getValHcfaUb($content,$charges13,515,524,"right","N");

			$revenueCodes14							= $revenueCodesArr[13]; //42-14 UB04
			$content 								= getValHcfaUb($content,$revenueCodes14,525,528,"right","N");
			$daysOrUnits14							= $totalUnitArr[13]; //24-G14 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits14,529,535,"right","N");
			$charges14								= $paidChargesArr[13]; //24-F14 HCFA
			$content 								= getValHcfaUb($content,$charges14,536,545,"right","N");

			$revenueCodes15							= $revenueCodesArr[14]; //42-15 UB04
			$content 								= getValHcfaUb($content,$revenueCodes15,546,549,"right","N");
			$daysOrUnits15							= $totalUnitArr[14]; //24-G15 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits15,550,556,"right","N");
			$charges15								= $paidChargesArr[14]; //24-F15 HCFA
			$content 								= getValHcfaUb($content,$charges15,557,566,"right","N");

			$revenueCodes16							= $revenueCodesArr[15]; //42-16 UB04
			$content 								= getValHcfaUb($content,$revenueCodes16,567,570,"right","N");
			$daysOrUnits16							= $totalUnitArr[15]; //24-G16 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits16,571,577,"right","N");
			$charges16								= $paidChargesArr[15]; //24-F16 HCFA
			$content 								= getValHcfaUb($content,$charges16,578,587,"right","N");

			$revenueCodes17							= $revenueCodesArr[16]; //42-17 UB04
			$content 								= getValHcfaUb($content,$revenueCodes17,588,591,"right","N");
			$daysOrUnits17							= $totalUnitArr[16]; //24-G17 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits17,592,598,"right","N");
			$charges17								= $paidChargesArr[16]; //24-F17 HCFA
			$content 								= getValHcfaUb($content,$charges17,599,608,"right","N");

			$revenueCodes18							= $revenueCodesArr[17]; //42-18 UB04
			$content 								= getValHcfaUb($content,$revenueCodes18,609,612,"right","N");
			$daysOrUnits18							= $totalUnitArr[17]; //24-G18 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits18,613,619,"right","N");
			$charges18								= $paidChargesArr[17]; //24-F18 HCFA
			$content 								= getValHcfaUb($content,$charges18,620,629,"right","N");

			$revenueCodes19							= $revenueCodesArr[18]; //42-19 UB04
			$content 								= getValHcfaUb($content,$revenueCodes19,630,633,"right","N");
			$daysOrUnits19							= $totalUnitArr[18]; //24-G19 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits19,634,640,"right","N");
			$charges19								= $paidChargesArr[18]; //24-F19 HCFA
			$content 								= getValHcfaUb($content,$charges19,641,650,"right","N");
			
			$revenueCodes20							= $revenueCodesArr[19]; //42-20 UB04
			$content 								= getValHcfaUb($content,$revenueCodes20,651,654,"right","N");
			$daysOrUnits20							= $totalUnitArr[19]; //24-G20 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits20,655,661,"right","N");
			$charges20								= $paidChargesArr[19]; //24-F20 HCFA
			$content 								= getValHcfaUb($content,$charges20,662,671,"right","N");

			$revenueCodes21							= $revenueCodesArr[20]; //42-21 UB04
			$content 								= getValHcfaUb($content,$revenueCodes21,672,675,"right","N");
			$daysOrUnits21							= $totalUnitArr[20]; //24-G21 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits21,676,682,"right","N");
			$charges21								= $paidChargesArr[20]; //24-F21 HCFA
			$content 								= getValHcfaUb($content,$charges21,683,692,"right","N");

			$revenueCodes22							= $revenueCodesArr[21]; //42-22 UB04
			$content 								= getValHcfaUb($content,$revenueCodes22,693,696,"right","N");
			$daysOrUnits22							= $totalUnitArr[21]; //24-G22 HCFA
			$content 								= getValHcfaUb($content,$daysOrUnits22,697,703,"right","N");
			$charges22								= $paidChargesArr[21]; //24-F22 HCFA
			$content 								= getValHcfaUb($content,$charges22,704,713,"right","N");
			
			$revenueCodes23							= $revenueCodesArr[22]; //42-23 UB04
			if(!trim($revenueCodes23)) {
				$revenueCodes23						= "1";	
			}
			$content 								= getValHcfaUb($content,$revenueCodes23,714,717,"right","N");
			$content 								= getValHcfaUb($content,$filler,718,724,"left","A");

			$revenueChargeExists = false;
			if(count($paidChargesArr)>0) {
				$revenueChargeExists = true;
			}			

			if(trim($sub_total_proc_charges)) {
				$sub_total_proc_charges = number_format($sub_total_proc_charges,2);
				$sub_total_proc_charges = str_ireplace('.','',$sub_total_proc_charges);
				$sub_total_proc_charges = str_ireplace(',','',$sub_total_proc_charges);
			}else {
				$sub_total_proc_charges = "";	
			}
			$totalCharges = $sub_total_proc_charges; //28
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
			
			if($revenueChargeExists == false) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Revenue charge is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$totalCharges,725,734,"right","N"); //28 HCFA
			
			$pageNumber = "0101";
			$content = getValHcfaUb($content,$pageNumber,735,738,"right","N"); //47 UB04 NOT FOUND

			$pos_facility_npi = "";
			$pos_facility_name = $pro_pri_npi = $pro_sec_npi = $pro_tri_npi_sc = $pro_pri_npi = "";
			if($row['group_institution']>0) {
				if($posFacilityId == '' || $posFacilityId == '0'){
					$posFacilityId = $row['ptDefaultFacility'];
				}
				$pos_facility_name = $posFacilityNameArr[$posFacilityId];
				if($pos_facility_name) {
					$pos_facility_npi = $posFacilityArr[$posFacilityId];
				}
			}
			if($row["primaryProviderId"]){
				$pro_pri_npi=$userNpiArr[$row["primaryProviderId"]];
				$pro_pri_lic=$userLicenseArr[$row["primaryProviderId"]];
			}
			if($row["reff_phy_nr"]==0 && $row["primaryProviderId"]!=$row["primary_provider_id_for_reports"] && $row["primary_provider_id_for_reports"]>0){
				$pro_pri_npi=$userNpiArr[$row["primary_provider_id_for_reports"]];
				$pro_pri_lic=$userLicenseArr[$row["primary_provider_id_for_reports"]];	
			}
			if($row["secondaryProviderId"]){
				$pro_sec_npi=$userNpiArr[$row["secondaryProviderId"]];
				$pro_sec_lic=$userLicenseArr[$row["secondaryProviderId"]];	
			}
			if($row["tertiaryProviderId"]){
				$pro_tri_npi_sc=$userNpiArr[$row["tertiaryProviderId"]];
				$pro_tri_lic_sc=$userLicenseArr[$row["tertiaryProviderId"]];	
			}
			
			
			if($pos_facility_name!=""){
				$tmpAttendingPhysicianIdTnLic = "";
				//$tmpUpinNpi = $pos_facility_npi;
				$tmpOperatingPhysicianIdTnLicenseNumber = $pro_pri_lic;
				//$tmpOperatingPhyUpinNpi = $pro_pri_npi;
				$tmpOtherProviderId1TnLicenseNumber = $pro_sec_lic;
				$tmpOtherProviderId1UpinNpi = $pro_sec_npi;
				$tmpOtherProviderId2TnLicenseNumber = ($pro_tri_lic_sc) ? $pro_tri_lic_sc : "";
				$tmpOtherProviderId2UpinNpi = ($pro_tri_npi_sc) ? $pro_tri_npi_sc : $row['group_NPI'];
			}else {
				$tmpAttendingPhysicianIdTnLic = $pro_pri_lic;
				//$tmpUpinNpi = $pro_pri_npi;
				$tmpOperatingPhysicianIdTnLicenseNumber = $pro_sec_lic;
				//$tmpOperatingPhyUpinNpi = $pro_sec_npi;
				$tmpOtherProviderId1TnLicenseNumber = $pro_tri_lic_sc;
				$tmpOtherProviderId1UpinNpi = $pro_tri_npi_sc;
				$tmpOtherProviderId2TnLicenseNumber = "";
				$tmpOtherProviderId2UpinNpi = $row['group_NPI'];
			}
			$tmpOperatingPhyUpinNpi = $pro_pri_npi; //should reflect the NPI of the doctor who is performing the procedure
			//echo '<br>enounter# '.$row["encounter_id"].' - '.$pos_facility_name.' ^^ '.$pro_pri_lic.'@@'.$pro_sec_lic.'@@'.$pro_tri_lic_sc.' !! '.$pro_pri_npi.'@@'.$pro_sec_npi.'@@'.$pro_tri_npi_sc;
			
			$tmpUpinNpi 							= $pro_pri_npi; //should reflect the NPI of the doctor who is performing the procedure.
			$attendingClinicianNPI 					= $tmpUpinNpi;
			$content 								= getValHcfaUb($content,$attendingClinicianNPI,739,748,"left","A"); //76 UB04
			$content 								= getValHcfaUb($content,$filler,749,760,"left","A");
			
			$patientAccountNumber 					= $row["encounter_id"]; //$row["patient_id"]; //26 HCFA
			$content 								= getValHcfaUb($content,$patientAccountNumber,761,780,"left","A");

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

			//$InsuredPolicyGroupOrFecaNumber = $show_box11_val; //11 HCFA
			//$content = getValHcfaUb($content,$InsuredPolicyGroupOrFecaNumber,781,797,"left","A");


			$insuranceGroupNumberPrimary = $objpriinsData->group_number; //62A - UB04
			$content = getValHcfaUb($content,$insuranceGroupNumberPrimary,781,797,"left","A");
			$insuranceGroupNumberSecondary = $objsecinsData->group_number; //62B - UB04
			$content = getValHcfaUb($content,$insuranceGroupNumberSecondary,798,814,"left","A");
			$insuranceGroupNumberTertiary = $objterinsData->group_number; //62C - UB04
			$content = getValHcfaUb($content,$insuranceGroupNumberTertiary,815,831,"left","A");

			
			$sec_ins_name=strtoupper($insCompArr[$row["secondaryInsuranceCoId"]]['name']);
			$sec_ins_house_code=strtoupper($insCompArr[$row["secondaryInsuranceCoId"]]['in_house_code']);
			
			$otherProviderId1UpinNpi = $tmpOtherProviderId1UpinNpi; //78 UB04
			$content = getValHcfaUb($content,$otherProviderId1UpinNpi,832,841,"left","A");
			$otherProviderId2UpinNpi = $tmpOtherProviderId2UpinNpi; //79 UB04
			$content = getValHcfaUb($content,$otherProviderId2UpinNpi,842,851,"left","A");

			$outpatientSiteID = '01';
			$content 								= getValHcfaUb($content,$outpatientSiteID,852,853,"right","N");
			
			$otherClinicianType1 = ""; //NOT FOUND
			$content 								= getValHcfaUb($content,$otherClinicianType1,854,855,"left","A");
			$otherClinicianType2 = ""; //NOT FOUND
			$content 								= getValHcfaUb($content,$otherClinicianType2,856,857,"left","A");
			$content 								= getValHcfaUb($content,$filler,858,858,"left","A");

			$enc_icd10 = $row["enc_icd10"];
			if($enc_icd10>0){
				$enc_icd10_ind="0";
			}else{
				$enc_icd10_ind="9";
			}
			$icdVersionIndicator = $enc_icd10_ind; //REQUIRED
			$content = getValHcfaUb($content,$icdVersionIndicator,859,859,"left","A");

			$otherDiagStartPos = 0;
			for($k=9;$k<=24;$k++) {
				$otherDiagnosisCode 				= $dignosisArr[$k];
				$otherDiagStartPos 					= $otherDiagStartPos ? ($otherDiagStartPos+8) : 860;
				$otherDiagEndPos 					= ($otherDiagStartPos+7);
				$content 							= getValHcfaUb($content,$otherDiagnosisCode,$otherDiagStartPos,$otherDiagEndPos,"left","A"); //21 HCFA
			}
			
			$eciICD9OrEcmIcd10Code1 				= $dignosisArr[0]; //72-A (ADDED SAME AS 67 UBO4)
			$content 								= getValHcfaUb($content,$eciICD9OrEcmIcd10Code1,988,995,"left","A");
			$eciICD9OrEcmIcd10Code2					= $dignosisArr[1]; //72-B (ADDED SAME AS 67-A UBO4)
			$content 								= getValHcfaUb($content,$eciICD9OrEcmIcd10Code2,996,1003,"left","A");
			$eciICD9OrEcmIcd10Code3					= $dignosisArr[2]; //72-C (ADDED SAME AS 67-B UBO4)
			$content 								= getValHcfaUb($content,$eciICD9OrEcmIcd10Code3,1004,1011,"left","A");

			$content 								= getValHcfaUb($content,$filler,1012,1025,"left","A");
			$content 								= getValHcfaUb($content,$filler,1026,1031,"left","A");
			$content 								= getValHcfaUb($content,$filler,1032,1045,"left","A");
			$content 								= getValHcfaUb($content,$filler,1046,1051,"left","A");
			$content 								= getValHcfaUb($content,$filler,1052,1065,"left","A");
			$content 								= getValHcfaUb($content,$filler,1066,1071,"left","A");
			$content 								= getValHcfaUb($content,$filler,1072,1085,"left","A");
			$content 								= getValHcfaUb($content,$filler,1086,1091,"left","A");
			$content 								= getValHcfaUb($content,$filler,1092,1105,"left","A");
			$content 								= getValHcfaUb($content,$filler,1106,1111,"left","A");
			$content 								= getValHcfaUb($content,$filler,1112,1125,"left","A");
			$content 								= getValHcfaUb($content,$filler,1126,1131,"left","A");
			$content 								= getValHcfaUb($content,$filler,1132,1145,"left","A");
			$content 								= getValHcfaUb($content,$filler,1146,1151,"left","A");
			$content 								= getValHcfaUb($content,$filler,1152,1165,"left","A");
			$content 								= getValHcfaUb($content,$filler,1166,1171,"left","A");
			$content 								= getValHcfaUb($content,$filler,1172,1185,"left","A");
			$content 								= getValHcfaUb($content,$filler,1186,1191,"left","A");
			$content 								= getValHcfaUb($content,$filler,1192,1205,"left","A");
			$content 								= getValHcfaUb($content,$filler,1206,1211,"left","A");
			$content 								= getValHcfaUb($content,$filler,1212,1225,"left","A");
			$content 								= getValHcfaUb($content,$filler,1226,1231,"left","A");
			$content 								= getValHcfaUb($content,$filler,1232,1245,"left","A");
			$content 								= getValHcfaUb($content,$filler,1246,1251,"left","A");
			$content 								= getValHcfaUb($content,$filler,1252,1265,"left","A");
			$content 								= getValHcfaUb($content,$filler,1266,1271,"left","A");
			$content 								= getValHcfaUb($content,$filler,1272,1285,"left","A");
			$content 								= getValHcfaUb($content,$filler,1286,1291,"left","A");
			$content 								= getValHcfaUb($content,$filler,1292,1305,"left","A");
			$content 								= getValHcfaUb($content,$filler,1306,1311,"left","A");
			$content 								= getValHcfaUb($content,$filler,1312,1325,"left","A");
			$content 								= getValHcfaUb($content,$filler,1326,1331,"left","A");
			$content 								= getValHcfaUb($content,$filler,1332,1345,"left","A");
			$content 								= getValHcfaUb($content,$filler,1346,1351,"left","A");
			$content 								= getValHcfaUb($content,$filler,1352,1365,"left","A");
			$content 								= getValHcfaUb($content,$filler,1366,1371,"left","A");
			$content 								= getValHcfaUb($content,$filler,1372,1385,"left","A");
			$content 								= getValHcfaUb($content,$filler,1386,1391,"left","A");

			$operatingClinicianNPI					= $tmpOperatingPhyUpinNpi; //77 UB04
			$content 								= getValHcfaUb($content,$operatingClinicianNPI,1392,1402,"left","A");
			
			$billingProviderFacilityNPI 			= $row["group_NPI"]; //REQUIRED
			$content 								= getValHcfaUb($content,$billingProviderFacilityNPI,1403,1417,"left","A");
			$content 								= getValHcfaUb($content,$filler,1418,1432,"left","A");

			//$otherProviderIdentifier 				= preg_replace("/[^A-Za-z0-9]/","",$row["group_Federal_EIN"]); //25 HCFA
			$otherProviderIdentifier 				= '1083615470'; //25 HCFA - The Provider ID/NPI should be “1083615470” for all entries.
			$content 								= getValHcfaUb($content,$otherProviderIdentifier,1433,1447,"left","A");
			$content 								= getValHcfaUb($content,$filler,1448,1522,"left","A");
			
			$statementCoverFromTo 					= $row["date_of_service_format_short"].$row["date_of_service_format_short"]; 
			$content 								= getValHcfaUb($content,$statementCoverFromTo,1523,1534,"right","A"); //6 UBO4
			
			if((in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_DME_payers) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],$arr_DME_payers)) && in_array(strtolower($billing_global_server_name), array('gewirtz'))){
				$tmpInsurancePlanNameOrProgramNamePrimary = "DMER-NHIC";
			}else{
				//$tmpInsurancePlanNameOrProgramNamePrimary = stripslashes($objInsGroupNumber->plan_name);
				$tmpInsurancePlanNameOrProgramNamePrimary = $insCompArr[$objInsGroupNumber->provider]['name'];
			}		
			$insurancePlanNameOrProgramNamePrimary 	= $tmpInsurancePlanNameOrProgramNamePrimary; //11-C HCFA
			$content 								= getValHcfaUb($content,$insurancePlanNameOrProgramNamePrimary,1535,1557,"left","A");
			
			$tmpInsurancePlanNameOrProgramNameSecondary = $insCompArr[$objSecondaryInsGroupNumber->provider]['name'];
			if(($sec_ins_name == "MEDICARE" || $sec_ins_house_code == "MEDICARE") && in_array(strtolower($billing_global_server_name), array('north central'))){
				$tmpInsurancePlanNameOrProgramNameSecondary = "";
			}
			//$insurancePlanNameOrProgramNameSecondary= $tmpInsurancePlanNameOrProgramNameSecondary; //9-D HCFA
			$insurancePlanNameOrProgramNameSecondary= ""; //9-D HCFA  Only the Primary Payer is necessary for the reports, so leave the Secondary and Tertiary blank
			$content 								= getValHcfaUb($content,$insurancePlanNameOrProgramNameSecondary,1558,1580,"left","A");
			
			
			$tmpInsurancePlanNameOrProgramNameSecondary = "";
			if($secondryFlag == true) {
				$tmpInsurancePlanNameOrProgramNameSecondary = $insCompArr[$objSecondaryInsGroupNumber->provider]['name'];
			}
			//$insurancePlanNameOrProgramNameTertiary = $tmpInsurancePlanNameOrProgramNameTertiary; //9-D HCFA
			$insurancePlanNameOrProgramNameTertiary= ""; //9-D HCFA  Only the Primary Payer is necessary for the reports, so leave the Secondary and Tertiary blank
			$content 								= getValHcfaUb($content,$insurancePlanNameOrProgramNameTertiary,1581,1603,"left","A");
			$content 								= getValHcfaUb($content,$filler,1604,1606,"left","A");

			//RaceEthnicity REQUIRED
			$arrRace = array(
			"American Indian or Alaska Native" => "R1",
			"Asian" => "R2",
			"Black or African American" => "R3",
			"Native Hawaiian or Other Pacific Islander" => "R4",
			"White" => "R5",
			"Multi-Racial" => "R6",
			"Declined to Specify" => "R7",
			"Latin American" => "R9",
			"Other" => "R9");
			$patientRaceExp = explode(",",$row["race"]);
			$patientRace 							= trim($arrRace[$patientRaceExp[0]]);// ? trim($arrRace[$patientRaceExp[0]]) : "R9";
			if(!trim($patientRace) && trim($patientRaceExp[0])) {
				$patientRace = "R9";
			}
			$content 								= getValHcfaUb($content,$patientRace,1607,1608,"left","A");
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


			$arrEthnicity = array(
			"African Americans" => "E2",
			"American" => "E2",
			"American Indians" => "E2",
			"Chinese" => "E2",
			"European Americans" => "E2",
			"Hispanic or Latino" => "E1",
			"Jewish" => "E2",
			"Not Hispanic or Latino" => "E2",
			"Unknown" => "E2",
			"Declined to Specify" => "E7");
			$patientEthnicityeExp 					= explode(",",$row["ethnicity"]);
			$patientEthnicity 						= trim($arrEthnicity[$patientEthnicityeExp[0]]); //? trim($arrEthnicity[$patientEthnicityeExp[0]]) : "E8";
			if(!trim($patientEthnicity) && trim($arrEthnicity[0])) {
				$patientEthnicity = "E2";
			}
			$content 								= getValHcfaUb($content,$patientEthnicity,1609,1610,"left","A");
			if(!trim($patientEthnicity)) {
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

			$tmpAdmissionHour 						= $row["admit_hour_format"];
			$admissionHour 							= $tmpAdmissionHour; //13 UBO4
			$content 								= getValHcfaUb($content,$admissionHour,1611,1612,"left","A");
			$tmpDischargeHour 						= $row["disch_hour_format"];
			$dischargeHour 							= $tmpDischargeHour; //16 UBO4
			$content 								= getValHcfaUb($content,$dischargeHour,1613,1614,"left","A");
			$content 								= getValHcfaUb($content,$filler,1615,1621,"left","A");
			$content 								= getValHcfaUb($content,$filler,1622,1623,"left","A");
			$content 								= getValHcfaUb($content,$filler,1624,1629,"left","A");
			$content 								= getValHcfaUb($content,$filler,1630,1635,"left","A");
			$content 								= getValHcfaUb($content,$filler,1636,1637,"left","A");
			$accidentState 							= ""; //29 UBO4 NOT FOUND
			$content 								= getValHcfaUb($content,$accidentState,1638,1639,"left","A");
			
			$ConditionEmploymentRelated				= ""; //18 TO 28 UBO4 NOT FOUND
			$content 								= getValHcfaUb($content,$ConditionEmploymentRelated,1640,1641,"left","A");
			$accidentEmployRelatedOccurrenceCode= ""; //31 TO 34 UBO4 NOT FOUND
			$content 								= getValHcfaUb($content,$accidentEmployRelatedOccurrenceCode,1642,1643,"left","A");
			$accidentEmployRelatedOccurrenceCodeDate= ""; //31 TO 34 UBO4 NOT FOUND
			$content 								= getValHcfaUb($content,$accidentEmployRelatedOccurrenceCodeDate,1644,1649,"right","N");
			$crimeVictimOccurrenceCode 				= ""; //31 TO 34 UBO4 NOT FOUND
			$content 								= getValHcfaUb($content,$crimeVictimOccurrenceCode,1650,1651,"left","A");
			$crimeVictimOccurrenceCodeDate 			= ""; //31 TO 34 UBO4 NOT FOUND
			$content 								= getValHcfaUb($content,$crimeVictimOccurrenceCodeDate,1652,1657,"left","A");

			$content 								= getValHcfaUb($content,$filler,1658,1659,"left","A");
			$content 								= getValHcfaUb($content,$filler,1660,1663,"left","A");

			//$patientReasonForVisitDiagnosisCode1	= $dignosisArr[0]; //70-A (ADDED SAME AS 67 UBO4)
			$patientReasonForVisitDiagnosisCode1	= ""; //70-A (SHOULD BE LEFT BLANK)
			$content 								= getValHcfaUb($content,$patientReasonForVisitDiagnosisCode1,1664,1670,"left","A");
			//$patientReasonForVisitDiagnosisCode2	= $dignosisArr[1]; //70-B (ADDED SAME AS 67-A UBO4)
			$patientReasonForVisitDiagnosisCode2	= ""; //70-B (SHOULD BE LEFT BLANK)
			$content 								= getValHcfaUb($content,$patientReasonForVisitDiagnosisCode2,1671,1677,"left","A");
			//$patientReasonForVisitDiagnosisCode3	= $dignosisArr[2]; //70-C (ADDED SAME AS 67-B UBO4)
			$patientReasonForVisitDiagnosisCode3	= ""; //70-C (SHOULD BE LEFT BLANK)
			$content 								= getValHcfaUb($content,$patientReasonForVisitDiagnosisCode3,1678,1684,"left","A");

			$hcpcsRatesCode1 						= $hcpcsRatesCodeArr[0]; //24-D1 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode1,1685,1698,"left","A");
			$hcpcsServiceDate1 						= $dateOfServiceArr[0];//24-A1 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate1,1699,1704,"right","N");

			$hcpcsRatesCode2 						= $hcpcsRatesCodeArr[1];//24-D2 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode2,1705,1718,"left","A");
			$hcpcsServiceDate2 						= $dateOfServiceArr[1];//24-A2 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate2,1719,1724,"right","N");

			$hcpcsRatesCode3 						= $hcpcsRatesCodeArr[2];//24-D3 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode3,1725,1738,"left","A");
			$hcpcsServiceDate3 						= $dateOfServiceArr[2];//24-A3 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate3,1739,1744,"right","N");

			$hcpcsRatesCode4 						= $hcpcsRatesCodeArr[3];//24-D4 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode4,1745,1758,"left","A");
			$hcpcsServiceDate4 						= $dateOfServiceArr[3];//24-A4 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate4,1759,1764,"right","N");

			$hcpcsRatesCode5 						= $hcpcsRatesCodeArr[4];//24-D5 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode5,1765,1778,"left","A");
			$hcpcsServiceDate5 						= $dateOfServiceArr[4];//24-A5 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate5,1779,1784,"right","N");

			$hcpcsRatesCode6 						= $hcpcsRatesCodeArr[5];//24-D6 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode6,1785,1798,"left","A");
			$hcpcsServiceDate6 						= $dateOfServiceArr[5];//24-A6 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate6,1799,1804,"right","N");

			$hcpcsRatesCode7 						= $hcpcsRatesCodeArr[6];//24-D7 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode7,1805,1818,"left","A");
			$hcpcsServiceDate7 						= $dateOfServiceArr[6];//24-A7 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate7,1819,1824,"right","N");

			$hcpcsRatesCode8 						= $hcpcsRatesCodeArr[7];//24-D8 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode8,1825,1838,"left","A");
			$hcpcsServiceDate8 						= $dateOfServiceArr[7];//24-A8 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate8,1839,1844,"right","N");

			$hcpcsRatesCode9 						= $hcpcsRatesCodeArr[8];//24-D9 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode9,1845,1858,"left","A");
			$hcpcsServiceDate9 						= $dateOfServiceArr[8];//24-A9 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate9,1859,1864,"right","N");

			$hcpcsRatesCode10 						= $hcpcsRatesCodeArr[9];//24-D10 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode10,1865,1878,"left","A");
			$hcpcsServiceDate10 					= $dateOfServiceArr[9];//24-A10 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate10,1879,1884,"right","N");

			$hcpcsRatesCode11 						= $hcpcsRatesCodeArr[10];//24-D11 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode11,1885,1898,"left","A");
			$hcpcsServiceDate11 					= $dateOfServiceArr[10];//24-A11 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate11,1899,1904,"right","N");

			$hcpcsRatesCode12 						= $hcpcsRatesCodeArr[11];//24-D12 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode12,1905,1918,"left","A");
			$hcpcsServiceDate12 					= $dateOfServiceArr[11];//24-A12 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate12,1919,1924,"right","N");

			$hcpcsRatesCode13 						= $hcpcsRatesCodeArr[12];//24-D13 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode13,1925,1938,"left","A");
			$hcpcsServiceDate13 					= $dateOfServiceArr[12];//24-A13 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate13,1939,1944,"right","N");

			$hcpcsRatesCode14 						= $hcpcsRatesCodeArr[13];//24-D14 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode14,1945,1958,"left","A");
			$hcpcsServiceDate14 					= $dateOfServiceArr[13];//24-A14 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate14,1959,1964,"right","N");

			$hcpcsRatesCode15 						= $hcpcsRatesCodeArr[14];//24-D15 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode15,1965,1978,"left","A");
			$hcpcsServiceDate15 					= $dateOfServiceArr[14];//24-A15 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate15,1979,1984,"right","N");

			$hcpcsRatesCode16 						= $hcpcsRatesCodeArr[15];//24-D16 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode16,1985,1998,"left","A");
			$hcpcsServiceDate16 					= $dateOfServiceArr[15];//24-A16 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate16,1999,2004,"right","N");

			$hcpcsRatesCode17 						= $hcpcsRatesCodeArr[16];//24-D17 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode17,2005,2018,"left","A");
			$hcpcsServiceDate17 					= $dateOfServiceArr[16];//24-A17 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate17,2019,2024,"right","N");

			$hcpcsRatesCode18 						= $hcpcsRatesCodeArr[17];//24-D18 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode18,2025,2038,"left","A");
			$hcpcsServiceDate18 					= $dateOfServiceArr[17];//24-A18 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate18,2039,2044,"right","N");

			$hcpcsRatesCode19 						= $hcpcsRatesCodeArr[18];//24-D19 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode19,2045,2058,"left","A");
			$hcpcsServiceDate19 					= $dateOfServiceArr[18];//24-A19 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate19,2059,2064,"right","N");

			$hcpcsRatesCode20 						= $hcpcsRatesCodeArr[19];//24-D20 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode20,2065,2078,"left","A");
			$hcpcsServiceDate20 					= $dateOfServiceArr[19];//24-A20 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate20,2079,2084,"right","N");

			$hcpcsRatesCode21 						= $hcpcsRatesCodeArr[20];//24-D21 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode21,2085,2098,"left","A");
			$hcpcsServiceDate21 					= $dateOfServiceArr[20];//24-A21 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate21,2099,2104,"right","N");

			$hcpcsRatesCode22 						= $hcpcsRatesCodeArr[21];//24-D22 HCFA
			$content 								= getValHcfaUb($content,$hcpcsRatesCode22,2105,2118,"left","A");
			$hcpcsServiceDate22 					= $dateOfServiceArr[21];//24-A22 HCFA
			$content 								= getValHcfaUb($content,$hcpcsServiceDate22,2119,2124,"right","N");

			$patientCountyCode						= ""; //NOT FOUND
			$content 								= getValHcfaUb($content,$patientCountyCode,2125,2129,"left","A");
			$content 								= getValHcfaUb($content,$filler,2130,2158,"left","A");

			$eciICD9OrEcmIcd10Code4					= $dignosisArr[3]; //72-D (ADDED SAME AS 67-C UBO4)
			$content 								= getValHcfaUb($content,$eciICD9OrEcmIcd10Code4,2159,2165,"left","A");
			$content 								= getValHcfaUb($content,$filler,2166,2166,"left","A");
			$eciICD9OrEcmIcd10Code5					= $dignosisArr[4]; //72-E (ADDED SAME AS 67-D UBO4)
			$content 								= getValHcfaUb($content,$eciICD9OrEcmIcd10Code5,2167,2173,"left","A");
			$content 								= getValHcfaUb($content,$filler,2174,2174,"left","A");
			$eciICD9OrEcmIcd10Code6					= $dignosisArr[5]; //72-F (ADDED SAME AS 67-E UBO4)
			$content 								= getValHcfaUb($content,$eciICD9OrEcmIcd10Code6,2175,2181,"left","A");
			$content 								= getValHcfaUb($content,$filler,2182,2182,"left","A");
			$eciICD9OrEcmIcd10Code7					= $dignosisArr[6]; //72-G (ADDED SAME AS 67-F UBO4)
			$content 								= getValHcfaUb($content,$eciICD9OrEcmIcd10Code7,2183,2189,"left","A");
			$content 								= getValHcfaUb($content,$filler,2190,2190,"left","A");
			$eciICD9OrEcmIcd10Code8					= $dignosisArr[7]; //72-H (ADDED SAME AS 67-G UBO4)
			$content 								= getValHcfaUb($content,$eciICD9OrEcmIcd10Code8,2191,2197,"left","A");
			$content 								= getValHcfaUb($content,$filler,2198,2198,"left","A");
			$content 								= getValHcfaUb($content,$filler,2199,2228,"left","A");

			$patientAddressState 					= $row["ptState"]; //REQUIRED
			$content 								= getValHcfaUb($content,$patientAddressState,2229,2230,"left","A");
			$content 								= getValHcfaUb($content,$filler,2231,2257,"left","A");

			$tmpInsuredUniqueIdNumberPrimary 		= trim(preg_replace("/[^A-Za-z0-9]/","",$objpriinsData->policy_number));
			if(!$tmpInsuredUniqueIdNumberPrimary) {
				$tmpInsuredUniqueIdNumberPrimary 	= "99999999999999999999";	
			}
			$insuredUniqueIdNumberPrimary 			= preg_replace("/[^A-Za-z0-9]/","",$tmpInsuredUniqueIdNumberPrimary); //8-B
			$content 								= getValHcfaUb($content,$insuredUniqueIdNumberPrimary,2258,2277,"left","A");
			$content 								= getValHcfaUb($content,$filler,2278,2301,"left","A");

			$patientNameLast 						= stripslashes($row["ptLname"]); //8-B UBO4
			$content 								= getValHcfaUb($content,$patientNameLast,2302,2336,"left","A");
			$patientNameSuffix 						= stripslashes($row["ptSuffix"]); //8-B UBO4
			$content 								= getValHcfaUb($content,$patientNameSuffix,2337,2339,"left","A");
			$patientNameFirst 						= stripslashes($row["ptFname"]); //8-B UBO4
			$content 								= getValHcfaUb($content,$patientNameFirst,2340,2364,"left","A");
			$patientNameMiddle 						= stripslashes($row["ptMname"]); //8-B UBO4
			$content 								= getValHcfaUb($content,$patientNameMiddle,2365,2389,"left","A");
			$patientAddressStreet 					= stripslashes($row["ptStreet"]); //8-B UBO4
			$content 								= getValHcfaUb($content,$patientAddressStreet,2390,2444,"left","A");
			$patientAddressCity 					= $row["ptCity"]; //8-B UBO4
			$content 								= getValHcfaUb($content,$patientAddressCity,2445,2484,"left","A");
			$conditionCode1 						= "P7"; //18 UBO4 NOT FOUND
			$content 								= getValHcfaUb($content,$conditionCode1,2485,2486,"left","A");

			$content 								= getValHcfaUb($content,$filler,2487,2491,"left","A");
			
			
			
			//start			
			$tmpPatientSocialSecurityNumber = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$row["patientSSN"]))); //REQUIRED
			if(!$tmpPatientSocialSecurityNumber || $tmpPatientSocialSecurityNumber == 0) {
				//$tmpPatientSocialSecurityNumber = "999999999";
				$tmpPatientSocialSecurityNumber = "0000";
			}
			$patientSocialSecurityNumber = $tmpPatientSocialSecurityNumber; //REQUIRED
			if(!trim($patientSocialSecurityNumber)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "SSN is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}else if(strlen(trim($patientSocialSecurityNumber))<9) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "SSN is invalid length";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			
			$patientLastFourDigitOfSSN = "";
			if($patientSocialSecurityNumber) {
				$patientLastFourDigitOfSSN 							= substr($patientSocialSecurityNumber,-4,4);	
			}
			$content = getValHcfaUb($content,$patientLastFourDigitOfSSN,2492,2495,"left","A");
			
			
			//end
			
			$content 								= getValHcfaUb($content,$filler,2496,2500,"left","A");

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

$show_msg= "";
if(trim($newContent)) {
	if(!is_dir($updir."/state_report")){
		//Create patient directory
		mkdir($updir."/state_report", 0777,true);
	}
	$contentFileName = $updir."/state_report/il_".$report_type.'_'.$_SESSION["authId"].".txt";
	if(file_exists($contentFileName)) {
		unlink($contentFileName);	
	}
	file_put_contents($contentFileName,$newContent);	
	
	$show_msg= "<span style='font-family:verdana;font-size:12px;font-weight:bold; padding-left:100px;'> IL State Report ".$reportName." have been exported successfully.</span>";
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
