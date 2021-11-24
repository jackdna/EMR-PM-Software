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
File: ub_txt.php
Purpose: To Export UBO4 In Fixed Length Format.
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

$newContent = "";

//START CODE OF INSURANCE COMPANY
$insCompArr = array();
$insCompRes=imw_query("select id, `name`, in_house_code, Payer_id_pro, Payer_id, emdeon_payer_eligibility, ins_state_payer_code, institutional_Code_id from insurance_companies ORDER BY id");
if(imw_num_rows($insCompRes)>0) {
	while($insCompRow=imw_fetch_array($insCompRes)) {
		$cId 											= $insCompRow["id"];
		$insCompArr[$cId]["name"] 						= $insCompRow["name"];
		$insCompArr[$cId]["in_house_code"] 				= $insCompRow["in_house_code"];
		$insCompArr[$cId]["Payer_id_pro"] 				= $insCompRow["Payer_id_pro"];
		$insCompArr[$cId]["Payer_id"] 					= $insCompRow["Payer_id"];
		$insCompArr[$cId]["emdeon_payer_eligibility"] 	= $insCompRow["emdeon_payer_eligibility"];
		$insCompArr[$cId]["ins_state_payer_code"] 		= $insCompRow["ins_state_payer_code"];
		$insCompArr[$cId]['institutional_Code_id'] 		= $insCompRow["institutional_Code_id"];
		
	}
}
//END CODE OF INSURANCE COMPANY

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

//START CODE OF CPT4 CODE
$cpt4CodeArr = array();
$cpfFeeRes=imw_query("select cpt_fee_id,cpt4_code from cpt_fee_tbl where delete_status = '0'");
if(imw_num_rows($cpfFeeRes)>0) {
	while($cpfFeeRow=imw_fetch_array($cpfFeeRes)) {
		$cptFeeId = $cpfFeeRow["cpt_fee_id"];
		$cpt4CodeArr[$cptFeeId] = $cpfFeeRow["cpt4_code"];
	}
}
//END CODE OF CPT4 CODE

//START CODE OF MODIFIER CODE
$modifierCodeArr = array();
$modifierRes=imw_query("select * from modifiers_tbl where (modifiers_id='$modifier_id' or modifiers_id='$modifier_id2' 
						or modifiers_id='$modifier_id3') and delete_status = '0'");
if(imw_num_rows($modifierRes)>0) {
	while($modifierRow=imw_fetch_array($modifierRes)){
		$modifyId = $modifierRow['modifiers_id'];
		$modifierCodeArr[$modifyId]=$modifierRow['modifier_code'];
	}
}
//END CODE OF MODIFIER CODE

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

//$andQry .= " and p.charge_list_id IN(0,84258,84257)"; //temporary query
//inc.Insurance_payment = 'HCFA1500' and 
$qry = "SELECT p.*, if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%m%d%Y'),'') AS admit_date_format, if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%m-%d-%Y'),'') AS admit_date_format_new, if(p.disch_date != '0000-00-00 00:00:00', DATE_FORMAT(p.disch_date, '%m%d%Y'),'') AS disch_date_format,
		if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m%d%Y'),'') as date_of_service_format, if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m-%d-%Y'),'') as date_of_service_format_new,
		gn.group_Federal_EIN, gn.group_NPI, gn.group_institution, gn.name as groupName, 
		pd.city AS ptCity, pd.state AS ptState, pd.postal_code AS ptPostalCode, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m%d%Y'),'') AS ptDobFormat, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m-%d-%Y'),'') AS ptDobFormatNew,
		pd.sex AS ptSex, pd.ss AS patientSSN, pd.race, pd.ethnicity, pd.street AS ptStreet, pd.street2 AS ptStreet2,fname AS ptFname, lname AS ptLname, TRIM(CONCAT(pd.lname,', ',pd.fname,' ',pd.mname)) AS ptName, 
		pd.default_facility AS ptDefaultFacility, pd.country_code AS ptCountryCode, pd.status AS maritalStatus, pd.occupation AS ptOccupation, 
		pd.primary_care_id AS ptPrimaryCareId
		FROM patient_charge_list p
		
		INNER JOIN patient_data pd ON(pd.pid = p.patient_id)
		INNER JOIN insurance_companies inc ON (p.primaryInsuranceCoId = inc.id AND inc.name != 'SELF PAY' AND inc.institutional_type ='INST_ONLY')
		INNER JOIN submited_record sr ON(sr.encounter_id = p.encounter_id)
		LEFT JOIN groups_new gn ON(gn.gro_id=p.gro_id)
		WHERE p.charge_list_id !='0' and p.del_status='0' and p.enc_accept_assignment!='2' ".$andQry."  
		GROUP BY p.charge_list_id
		ORDER BY pd.pid ";

$res = imw_query($qry) or die($qry.imw_error());
$errorMsgArr = array();
if(imw_num_rows($res)>0) {
	$c=0;
	while($row = imw_fetch_assoc($res)) {
		$charg_id = $row["charge_list_id"];
		
		$imw_info4=imw_query("select patient_charge_list_details.* from patient_charge_list_details join cpt_fee_tbl
						on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
						where patient_charge_list_details.del_status='0' and patient_charge_list_details.charge_list_id='".$charg_id."'
						and patient_charge_list_details.proc_selfpay!='1' and cpt_fee_tbl.not_covered = '0' and patient_charge_list_details.posted_status='1' 
						and patient_charge_list_details.differ_insurance_bill != 'true'
						order by patient_charge_list_details.display_order,patient_charge_list_details.charge_list_detail_id LIMIT 0,23");
		$num_charge_details=imw_num_rows($imw_info4);
		if($num_charge_details > 0) {
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
			$objterinsData='';
			if($row["tertiaryInsuranceCoId"]){
				$objterinsData = getRecords_ins_data_con('insurance_data','pid',$row["patient_id"],'provider',$row["tertiaryInsuranceCoId"],'type','tertiary','ins_caseid',$row["case_type_id"],$row["date_of_service"]);
			}
	
			$rel_arr=array("Spouse"=>'01',"Self"=>'18',"Son"=>'19',"Daughter"=>'19',"Mother"=>'32',
				"Father"=>'33',"Guardian"=>'09',"POA"=>'G8',"Employee"=>20,"Other Relationship"=>'G8');
			 $ins_rel_final_pri="18";
			 foreach($rel_arr as $key=>$val) {
				if(ucfirst($objpriinsData->subscriber_relationship)==$key){
					$ins_rel_final_pri=$val;
				}
			 }
			 $ins_rel_final_sec="18";
			 foreach($rel_arr as $key1=>$val1) {
				if(ucfirst($objsecinsData->subscriber_relationship)==$key1){
					$ins_rel_final_sec=$val1;
				}
			 }
			$ins_rel_final_ter="18";
			 foreach($rel_arr as $key2=>$val2) {
				if(ucfirst($objterinsData->subscriber_relationship)==$key2){
					$ins_rel_final_ter=$val2;
				}
			 }
			
			//START FOR VENDOR USE
			$content = array();
			$facType = "";
			$tmpAdmissionDate 		= $row["date_of_service_format"];	
			$tmpAdmissionDateNew 	= $row["date_of_service_format_new"];	
			$tmpAdmissionHour 		= $row["admit_hour_format"];
			
			$content = getValHcfaUb($content,$facType,1,1,"left","A");
			$dateYear= "";
			$content = getValHcfaUb($content,$dateYear,2,5,"left","A");
			$VendorIdentificationNumber= "";
			$content = getValHcfaUb($content,$VendorIdentificationNumber,6,7,"left","A");
			$billNumber = "";
			$content = getValHcfaUb($content,$billNumber,8,14,"left","A");
			$recordSequenceNumber = "";
			$content = getValHcfaUb($content,$recordSequenceNumber,15,16,"right","A");
			$facilityIdentificationNumber = "";
			$content = getValHcfaUb($content,$facilityIdentificationNumber,17,21,"right","A");
			$formType = "";
			$content = getValHcfaUb($content,$formType,24,25,"left","A");
			$doNotResuscitateFlag = ""; 
			$content = getValHcfaUb($content,$doNotResuscitateFlag,180,180,"left","A");
	
			$patientInitialsFirstName = ""; //REQUIRED
			$content = getValHcfaUb($content,$patientInitialsFirstName,2275,2276,"left","A");
			$patientInitialsLastName = ""; //REQUIRED
			$content = getValHcfaUb($content,$patientInitialsLastName,2277,2280,"left","A");
	
			$primaryInsuredInitialsFirstName = ""; //REQUIRED
			$content = getValHcfaUb($content,$primaryInsuredInitialsFirstName,2281,2282,"left","A");
			$primaryInsuredInitialsLastName = ""; //REQUIRED
			$content = getValHcfaUb($content,$primaryInsuredInitialsLastName,2283,2286,"left","A");
	
			$secondaryInsuredInitialsFirstName = ""; //REQUIRED
			$content = getValHcfaUb($content,$secondaryInsuredInitialsFirstName,2287,2288,"left","A");
			$secondaryInsuredInitialsLastName = ""; //REQUIRED
			$content = getValHcfaUb($content,$secondaryInsuredInitialsLastName,2289,2292,"left","A");
	
			$tertiaryInsuredInitialsFirstName = ""; //REQUIRED
			$content = getValHcfaUb($content,$tertiaryInsuredInitialsFirstName,2293,2294,"left","A");
			$tertiaryInsuredInitialsLastName = ""; //REQUIRED
			$content = getValHcfaUb($content,$tertiaryInsuredInitialsLastName,2295,2298,"left","A");
	
			//END FOR VENDOR USE
			
			//START PATIENT INFORMATION
			$patientControlNumber = $row["patient_id"];
			$content = getValHcfaUb($content,$patientControlNumber,26,50,"left","A");
			$medicalHealthRecordNumber = $row["encounter_id"]; //REQUIRED
			$content = getValHcfaUb($content,$medicalHealthRecordNumber,51,75,"left","A");
			$typeOfBill = "831"; //REQUIRED
			$content = getValHcfaUb($content,$typeOfBill,76,79,"right","N");
			$federalTaxSubIdNumber = ""; //REQUIRED 5 NOT FOUND
			$content = getValHcfaUb($content,$federalTaxSubIdNumber,80,83,"left","A");
			
			$federalTaxNumber = preg_replace("/[^A-Za-z0-9]/","",$row["group_Federal_EIN"]); //REQUIRED
			if($federalTaxNumber && substr($federalTaxNumber, 2, 1) != '-') { 
				$federalTaxNumber = substr($federalTaxNumber, 0, 2).'-'.substr($federalTaxNumber, 2);   
			}
			$content = getValHcfaUb($content,$federalTaxNumber,84,93,"left","A");
			
			$statementCoverFromTo = $row["date_of_service_format"].$row["date_of_service_format"]; //REQUIRED
			$content = getValHcfaUb($content,$statementCoverFromTo,94,109,"right","A");
	
			$patientAddressCity = $row["ptCity"]; //REQUIRED
			$content = getValHcfaUb($content,$patientAddressCity,110,139,"left","A");
			$patientAddressState = $row["ptState"]; //REQUIRED
			$content = getValHcfaUb($content,$patientAddressState,140,141,"left","A");
			$patientAddressZip = $row["ptPostalCode"]; //REQUIRED
			$content = getValHcfaUb($content,$patientAddressZip,142,150,"left","A");
			$patientAddressCountryCode = $row["ptCountryCode"]; //REQUIRED 9E NOT FOUND
			$content = getValHcfaUb($content,$patientAddressCountryCode,151,154,"left","A");
			$patientDateOfBirth = $row["ptDobFormat"]; //REQUIRED
			$content = getValHcfaUb($content,$patientDateOfBirth,155,162,"right","N");
			
			$patientSex = strtoupper(substr($row['ptSex'],0,1)); //REQUIRED
			if(!$patientSex) { $patientSex = "U"; }
			$content = getValHcfaUb($content,$patientSex,163,163,"left","A");
			
			$admissionDate = $tmpAdmissionDate; //REQUIRED
			$content = getValHcfaUb($content,$admissionDate,164,171,"right","N");
			$admissionHour = $tmpAdmissionHour; //REQUIRED
			$content = getValHcfaUb($content,$admissionHour,172,173,"left","A");
			$typeOfAdmissionVisit = "3"; //REQUIRED
			$content = getValHcfaUb($content,$typeOfAdmissionVisit,174,175,"left","A");
			$sourceOfAdmission = "1"; //REQUIRED
			$content = getValHcfaUb($content,$sourceOfAdmission,176,177,"left","A");
			$patientDischargeStatus = "01"; //REQUIRED
			$content = getValHcfaUb($content,$patientDischargeStatus,178,179,"left","A");
			$accidentState = ""; //REQUIRED 29 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$accidentState,181,182,"left","A");
			$accidentCode = ""; //REQUIRED NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$accidentCode,183,184,"left","A");
			$accidentDate = ""; //REQUIRED NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$accidentDate,185,192,"right","N");
	
	
			//--- Start get all charge list details ----	
			$cpt4_code_arr=array();
			$notes_arr=array();
			$count_pro=0;
			$tot_amt=0;
			$dignosis = array();
			$proc_code_essi_arr = array();
			$top_name_var=1;
			$recCnt = 0;
			$revStartPos = $hippsRatesStartPos = $serviceStartPos = $unitsStartPos = $totalChargesStartPos = 0;
			$tot_amt=0;
			$chargeListIdArr = array();
			$revenueChargeExists =false;
			while($imw_row4=imw_fetch_array($imw_info4)){
				
				$recCnt++;
				$rev_cod							= $imw_row4['rev_code'];
				$revenueCodes						= $rCodeArr[$rev_cod];
				
				$revStartPos 						= $revStartPos ? ($revStartPos+4) : 193;
				$revEndPos 							= ($revStartPos+3);
				$content 							= getValHcfaUb($content,$revenueCodes,$revStartPos,$revEndPos,"left","A");
					
				$proc_cod							= $imw_row4['procCode'];
				$cpt4_code							= $cpt4CodeArr[$proc_cod];
				
				$modifier_id						= $imw_row4['modifier_id1'];
				$modifier_id2						= $imw_row4['modifier_id2'];
				$modifier_id3						= $imw_row4['modifier_id3'];
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
					
				$hippsRatesCode 					= trim($cpt4_code.' '.$modifier_code);
				$hippsRatesStartPos 				= $hippsRatesStartPos ? ($hippsRatesStartPos+14) : 285;
				$hippsRatesEndPos 					= ($hippsRatesStartPos+13);
				$content 							= getValHcfaUb($content,$hippsRatesCode,$hippsRatesStartPos,$hippsRatesEndPos,"left","A");
				
				$serviceDate 						= $row["date_of_service_format"]; //REQUIRED
				$serviceStartPos 					= $serviceStartPos ? ($serviceStartPos+8) : 607;
				$serviceEndPos 						= ($serviceStartPos+7);
				$content 							= getValHcfaUb($content,$serviceDate,$serviceStartPos,$serviceEndPos,"right","N");
				
				
				$unitOfService						= preg_replace("/[^A-Za-z0-9]/","",$imw_row4['units']);
				$unitsStartPos 						= $unitsStartPos ? ($unitsStartPos+7) : 799;
				$unitsEndPos 						= ($unitsStartPos+6);
				$content 							= getValHcfaUb($content,$unitOfService,$unitsStartPos,$unitsEndPos,"right","N");
				
				$totalChargesByRevenueCodeCategory	= preg_replace("/[^A-Za-z0-9]/","",$imw_row4['totalAmount']);
				$totalChargesStartPos 				= $totalChargesStartPos ? ($totalChargesStartPos+10) : 960;
				$totalChargesEndPos 				= ($totalChargesStartPos+9);
				$content 							= getValHcfaUb($content,$totalChargesByRevenueCodeCategory,$totalChargesStartPos,$totalChargesEndPos,"right","N");
			
				if(trim($totalChargesByRevenueCodeCategory)) {
					$revenueChargeExists = true;
				}
	
				if($imw_row4['totalAmount']) {
					$tot_amt+=$imw_row4['totalAmount'];
				}
				
				//START CODE FOR DIAGNOSIS CODE 67
				for($f=1;$f<=12;$f++){
					if($imw_row4['diagnosis_id'.$f]){
						$diagId = $imw_row4['diagnosis_id'.$f];
						/*
						if(trim($diagId)) {
							$diagIdExp = preg_split('/\./',$diagId);
							if(strlen($diagIdExp[1])==1) {
								$diagId = $diagId.'0';
							}else if(!trim($diagIdExp[1])) {
								$diagId = $diagId.'00';
							}
						}*/
						$diagId = preg_replace("/[^A-Za-z0-9]/","",$diagId);
						
						
						$dignosis[]=$diagId;
					}
				}
				//END CODE FOR DIAGNOSIS CODE 67
				
				$posFacilityId = "";
				if($imw_row4['posFacilityId']>0){
					$posFacilityId=$imw_row4['posFacilityId'];
				}			
			}
				
			$creationDate = str_ireplace("-","",$cur_dat); //REQUIRED
			$content = getValHcfaUb($content,$creationDate,791,798,"right","N");
	
	
	
			if(trim($tot_amt)) { 
				$tot_amt =preg_replace("/[^A-Za-z0-9]/","",number_format($tot_amt,2)); 
			}else { 
				$tot_amt = "";
			}
			$totalOfTotalCharges = $tot_amt; //REQUIRED
			if(!trim($totalOfTotalCharges)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Sum of individual charges do not add up to total charges";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$totalOfTotalCharges,1190,1199,"right","N");
	
			if($revenueChargeExists == false) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Revenue charge is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
	
	
			$nonCoveredChargesByRevenueCodeCategory1 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory1,1200,1209,"right","N");
			$nonCoveredChargesByRevenueCodeCategory2 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory2,1210,1219,"right","N");
			$nonCoveredChargesByRevenueCodeCategory3 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory3,1220,1229,"right","N");
			$nonCoveredChargesByRevenueCodeCategory4 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory4,1230,1239,"right","N");
			$nonCoveredChargesByRevenueCodeCategory5 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory5,1240,1249,"right","N");
			$nonCoveredChargesByRevenueCodeCategory6 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory6,1250,1259,"right","N");
			$nonCoveredChargesByRevenueCodeCategory7 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory7,1260,1269,"right","N");
			$nonCoveredChargesByRevenueCodeCategory8 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory8,1270,1279,"right","N");
			$nonCoveredChargesByRevenueCodeCategory9 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory9,1280,1289,"right","N");
			$nonCoveredChargesByRevenueCodeCategory10 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory10,1290,1299,"right","N");
			$nonCoveredChargesByRevenueCodeCategory11 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory11,1300,1309,"right","N");
			$nonCoveredChargesByRevenueCodeCategory12 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory12,1310,1319,"right","N");
			$nonCoveredChargesByRevenueCodeCategory13 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory13,1320,1329,"right","N");
			$nonCoveredChargesByRevenueCodeCategory14 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory14,1330,1339,"right","N");
			$nonCoveredChargesByRevenueCodeCategory15 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory15,1340,1349,"right","N");
			$nonCoveredChargesByRevenueCodeCategory16 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory16,1350,1359,"right","N");
			$nonCoveredChargesByRevenueCodeCategory17 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory17,1360,1369,"right","N");
			$nonCoveredChargesByRevenueCodeCategory18 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory18,1370,1379,"right","N");
			$nonCoveredChargesByRevenueCodeCategory19 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory19,1380,1389,"right","N");
			$nonCoveredChargesByRevenueCodeCategory20 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory20,1390,1399,"right","N");
			$nonCoveredChargesByRevenueCodeCategory21 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory21,1400,1409,"right","N");
			$nonCoveredChargesByRevenueCodeCategory22 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory22,1410,1419,"right","N");
			$nonCoveredChargesByRevenueCodeCategory23 = ""; //REQUIRED 48 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nonCoveredChargesByRevenueCodeCategory23,1420,1429,"right","N");
	
			$totalOfNonCoveredCharges = ""; //REQUIRED 48(23) NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$totalOfNonCoveredCharges,1430,1439,"right","N");
	
			//$tmpSelfPayPrimary = $insDataArr["self_pay_provider"][$row["patient_id"]]["primary"][$row["primaryInsuranceCoId"]]["1"];
			$tmpProgramPayerClassificationCodePrimary = trim($insCompArr[$row["primaryInsuranceCoId"]]['ins_state_payer_code']);
			if($row["primaryInsuranceCoId"]=='0') {
				$tmpProgramPayerClassificationCodePrimary = "P";
			}
			$payerClassificationCodePrimary = $tmpProgramPayerClassificationCodePrimary; //REQUIRED 50A NOT FOUND NEED DISCUSSION
			if(!trim($payerClassificationCodePrimary)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Primary payer is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$payerClassificationCodePrimary,1440,1443,"left","A");
			
			$tmpProgramPayerClassificationCodeSecondary = trim($insCompArr[$row["secondaryInsuranceCoId"]]['ins_state_payer_code']);
			$payerClassificationCodeSecondary = $tmpProgramPayerClassificationCodeSecondary; //REQUIRED 50B NOT FOUND NEED DISCUSSION
			$content = getValHcfaUb($content,$payerClassificationCodeSecondary,1444,1447,"left","A");
			
			$tmpProgramPayerClassificationCodeTertiary = trim($insCompArr[$row["tertiaryInsuranceCoId"]]['ins_state_payer_code']);
			$payerClassificationCodeTertiary = $tmpProgramPayerClassificationCodeTertiary; //REQUIRED 50C NOT FOUND NEED DISCUSSION
			$content = getValHcfaUb($content,$payerClassificationCodeTertiary,1448,1451,"left","A");
	
			//$healthPlanIDPrimary = trim($insCompArr[$row["primaryInsuranceCoId"]]['institutional_Code_id']); //REQUIRED
			//if(in_array(strtolower($billing_global_server_name), array('mackool'))){$tmpHealthPlanIDPrimary = ""; }
			$tmpHealthPlanIDPrimary = trim(substr($objpriinsData->plan_name, 0, 15));
			if(!$tmpHealthPlanIDPrimary) { $tmpHealthPlanIDPrimary = "999999999999999"; }
			$healthPlanIDPrimary = $tmpHealthPlanIDPrimary;
			$content = getValHcfaUb($content,$healthPlanIDPrimary,1452,1466,"left","A");
			//$healthPlanIDSecondary = trim($insCompArr[$row["secondaryInsuranceCoId"]]['institutional_Code_id']); //REQUIRED
			//if(in_array(strtolower($billing_global_server_name), array('mackool'))){$healthPlanIDSecondary = ""; }
			$tmpHealthPlanIDSecondary = trim(substr($objsecinsData->plan_name, 0, 15));
			if(!$tmpHealthPlanIDSecondary) { $tmpHealthPlanIDSecondary = "999999999999999"; }
			$healthPlanIDSecondary = $tmpHealthPlanIDSecondary;
			$content = getValHcfaUb($content,$healthPlanIDSecondary,1467,1481,"left","A");
			

			//$healthPlanIDTertiary = trim($insCompArr[$row["tertiaryInsuranceCoId"]]['institutional_Code_id']); //REQUIRED
			//if(in_array(strtolower($billing_global_server_name), array('mackool'))){$healthPlanIDTertiary = ""; }
			$tmpHealthPlanIDTertiary = trim(substr($objsecinsData->plan_name, 0, 15));
			if(!$tmpHealthPlanIDTertiary) { $tmpHealthPlanIDTertiary = "999999999999999"; }
			$healthPlanIDTertiary = $tmpHealthPlanIDTertiary;
			$content = getValHcfaUb($content,$healthPlanIDTertiary,1482,1496,"left","A");
	
			$nationalProviderId = $row["group_NPI"]; //REQUIRED
			$content = getValHcfaUb($content,$nationalProviderId,1497,1511,"left","A");
	
			$patientRelationshipToInsuredPrimary = $ins_rel_final_pri; //REQUIRED
			$content = getValHcfaUb($content,$patientRelationshipToInsuredPrimary,1512,1513,"left","A");
			$patientRelationshipToInsuredSecondary = $ins_rel_final_sec; //REQUIRED
			$content = getValHcfaUb($content,$patientRelationshipToInsuredSecondary,1514,1515,"left","A");
			$patientRelationshipToInsuredTertiary = $ins_rel_final_ter; //REQUIRED
			$content = getValHcfaUb($content,$patientRelationshipToInsuredTertiary,1516,1517,"left","A");
	
	
			$tmpInsuredUniqueIdNumberPrimary = trim(preg_replace("/[^A-Za-z0-9]/","",$objpriinsData->policy_number));
			if(!$tmpInsuredUniqueIdNumberPrimary) {
				$tmpInsuredUniqueIdNumberPrimary = "99999999999999999999";	
			}
			$insuredUniqueIdNumberPrimary = preg_replace("/[^A-Za-z0-9]/","",$tmpInsuredUniqueIdNumberPrimary); //REQUIRED
			$content = getValHcfaUb($content,$insuredUniqueIdNumberPrimary,1518,1537,"left","A");
			
			$tmpInsuredUniqueIdNumberSecondary = trim(preg_replace("/[^A-Za-z0-9]/","",$objsecinsData->policy_number));
			if(!$tmpInsuredUniqueIdNumberSecondary) {
				$tmpInsuredUniqueIdNumberSecondary = "99999999999999999999";	
			}
			$insuredUniqueIdNumberSecondary = $tmpInsuredUniqueIdNumberSecondary; //REQUIRED
			$content = getValHcfaUb($content,$insuredUniqueIdNumberSecondary,1538,1557,"left","A");
			
			$tmpInsuredUniqueIdNumberTertiary = trim(preg_replace("/[^A-Za-z0-9]/","",$objterinsData->policy_number));
			if(!$tmpInsuredUniqueIdNumberTertiary) {
				$tmpInsuredUniqueIdNumberTertiary = "99999999999999999999";	
			}
			$insuredUniqueIdNumberTertiary = $tmpInsuredUniqueIdNumberTertiary; //REQUIRED
			$content = getValHcfaUb($content,$insuredUniqueIdNumberTertiary,1558,1577,"left","A");
	
			$insuranceGroupNumberPrimary = $objpriinsData->group_number; //REQUIRED
			$content = getValHcfaUb($content,$insuranceGroupNumberPrimary,1578,1594,"left","A");
			$insuranceGroupNumberSecondary = $objsecinsData->group_number; //REQUIRED
			$content = getValHcfaUb($content,$insuranceGroupNumberSecondary,1595,1611,"left","A");
			$insuranceGroupNumberTertiary = $objterinsData->group_number; //REQUIRED
			$content = getValHcfaUb($content,$insuranceGroupNumberTertiary,1612,1628,"left","A");
	
			$nameOfPrimaryInsuredEmployer = "9999999999999999999999999"; //REQUIRED 65A NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$nameOfPrimaryInsuredEmployer,1629,1653,"left","A");
			
			$enc_icd10 = $row["enc_icd10"];
			if($enc_icd10>0){
				$enc_icd10_ind="0";
			}else{
				$enc_icd10_ind="9";
			}
			$diagnosisAndProcedureVersion = $enc_icd10_ind; //REQUIRED
			$content = getValHcfaUb($content,$diagnosisAndProcedureVersion,1654,1655,"left","A");
			
			
			if(in_array(strtolower($billing_global_server_name), array('lodenvision'))){
				$dignosis = array_unique($dignosis);
			}
			$dignosis = array_values(array_filter($dignosis));
			$principalDiagnosisCode = $dignosis[0]; //REQUIRED
			$content = getValHcfaUb($content,$principalDiagnosisCode,1656,1663,"left","A");
			
			$otherDiagStartPos = 0;
			
			for($k=1;$k<=17;$k++) {
				$otherDiagnosisCode = $dignosis[$k];
				$otherDiagStartPos 	= $otherDiagStartPos ? ($otherDiagStartPos+8) : 1664;
				$otherDiagEndPos 	= ($otherDiagStartPos+7);
				$content 			= getValHcfaUb($content,$otherDiagnosisCode,$otherDiagStartPos,$otherDiagEndPos,"left","A");
			}
	
	
			$admittingDiagnosisCode = ""; //REQUIRED 69 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$admittingDiagnosisCode,1800,1806,"left","A");
			$patientReasonForVisitCode = ""; //REQUIRED 70A – C NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$patientReasonForVisitCode,1807,1827,"left","A");
			$prospectivePaymentSystemCode = ""; //REQUIRED 71 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$prospectivePaymentSystemCode,1828,1831,"left","A");
	
			$externalCauseOfInjuryCode1 = ""; //REQUIRED 72A NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$externalCauseOfInjuryCode1,1832,1839,"left","A");
			$externalCauseOfInjuryCode2 = ""; //REQUIRED 72B NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$externalCauseOfInjuryCode2,1840,1847,"left","A");
			$externalCauseOfInjuryCode3 = ""; //REQUIRED 72C NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$externalCauseOfInjuryCode3,1848,1855,"left","A");
	
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
				$tmpUpinNpi = $pos_facility_npi;
				$tmpOperatingPhysicianIdTnLicenseNumber = $pro_pri_lic;
				$tmpOperatingPhyUpinNpi = $pro_pri_npi;
				$tmpOtherProviderId1TnLicenseNumber = $pro_sec_lic;
				$tmpOtherProviderId1UpinNpi = $pro_sec_npi;
				$tmpOtherProviderId2TnLicenseNumber = ($pro_tri_lic_sc) ? $pro_tri_lic_sc : "";
				$tmpOtherProviderId2UpinNpi = ($pro_tri_npi_sc) ? $pro_tri_npi_sc : $row['group_NPI'];
			}else {
				$tmpAttendingPhysicianIdTnLic = $pro_pri_lic;
				$tmpUpinNpi = $pro_pri_npi;
				$tmpOperatingPhysicianIdTnLicenseNumber = $pro_sec_lic;
				$tmpOperatingPhyUpinNpi = $pro_sec_npi;
				$tmpOtherProviderId1TnLicenseNumber = $pro_tri_lic_sc;
				$tmpOtherProviderId1UpinNpi = $pro_tri_npi_sc;
				$tmpOtherProviderId2TnLicenseNumber = "";
				$tmpOtherProviderId2UpinNpi = $row['group_NPI'];
			}
			$attendingPhysicianIdProfessionCode = ""; //REQUIRED NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$attendingPhysicianIdProfessionCode,1946,1947,"left","A");
			$attendingPhysicianIdTnLicenseNumber = $tmpAttendingPhysicianIdTnLic; //REQUIRED 76 FOUND
			$content = getValHcfaUb($content,$attendingPhysicianIdTnLicenseNumber,1948,1957,"left","A");
			$attendingPhysicianIdUpinNpi = $tmpUpinNpi; //REQUIRED 76
			$content = getValHcfaUb($content,$attendingPhysicianIdUpinNpi,1958,1970,"left","A");
	
			$operatingPhysicianIdProfessionCode = ""; //REQUIRED NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$operatingPhysicianIdProfessionCode,1971,1972,"left","A");
			$operatingPhysicianIdTnLicenseNumber = $tmpOperatingPhysicianIdTnLicenseNumber; //REQUIRED 77 FOUND
			$content = getValHcfaUb($content,$operatingPhysicianIdTnLicenseNumber,1973,1982,"left","A");
			$operatingPhysicianIdUpinNpi = $tmpOperatingPhyUpinNpi; //REQUIRED 77
			$content = getValHcfaUb($content,$operatingPhysicianIdUpinNpi,1983,1995,"left","A");
	
			$otherProviderId1ProfessionCode = ""; //REQUIRED NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$otherProviderId1ProfessionCode,1996,1997,"left","A");
			$otherProviderId1TnLicenseNumber = $tmpOtherProviderId1TnLicenseNumber; //REQUIRED 78 FOUND
			$content = getValHcfaUb($content,$otherProviderId1TnLicenseNumber,1998,2007,"left","A");
			$otherProviderId1UpinNpi = $tmpOtherProviderId1UpinNpi; //REQUIRED 78
			$content = getValHcfaUb($content,$otherProviderId1UpinNpi,2008,2020,"left","A");
	
			$otherProviderId2ProfessionCode = ""; //REQUIRED NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$otherProviderId2ProfessionCode,2021,2022,"left","A");
			$otherProviderId2TnLicenseNumber = $tmpOtherProviderId2TnLicenseNumber; //REQUIRED 79 FOUND
			$content = getValHcfaUb($content,$otherProviderId2TnLicenseNumber,2023,2032,"left","A");
			$otherProviderId2UpinNpi = $tmpOtherProviderId2UpinNpi; //REQUIRED 79
			$content = getValHcfaUb($content,$otherProviderId2UpinNpi,2033,2045,"left","A");
	
			$tmpPatientSocialSecurityNumber = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$row["patientSSN"]))); //REQUIRED
			if(!$tmpPatientSocialSecurityNumber || $tmpPatientSocialSecurityNumber == 0) {
				$tmpPatientSocialSecurityNumber = "999999999";	
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
			$content = getValHcfaUb($content,$patientSocialSecurityNumber,2058,2067,"left","A");
			
			//RaceEthnicity REQUIRED
			$arrRace = array(
			"American Indian or Alaska Native" => 3,
			"Asian" => 4,
			"Black or African American" => 2,
			"Native Hawaiian or Other Pacific Islander" => 5,
			"Latin American" => 5,
			"White" => "1",											
			"Declined to Specify" => 9);
			$patientRaceExp = explode(",",$row["race"]);
			$patientRace = trim($arrRace[$patientRaceExp[0]]) ? trim($arrRace[$patientRaceExp[0]]) : "9";
			if(!trim($patientRace)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Race is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}else if(trim($patientRace)==9) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Race is patient refused or unknown";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$arrEthnicity = array(
			"African Americans" => "",
			"American" => "",
			"American Indians" => "",
			"Chinese" => "",
			"European Americans" => "",
			"Hispanic or Latino" => 1,
			"Jewish" => "",
			"Not Hispanic or Latino" => 2,
			"Unknown" => 9,
			"Declined to Specify" => 9);																			
			$patientEthnicityeExp = explode(",",$row["ethnicity"]);
			$patientEthnicity = trim($arrEthnicity[$patientEthnicityeExp[0]]) ? trim($arrEthnicity[$patientEthnicityeExp[0]]) : "9";
			if(!trim($patientEthnicity)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Ethnicity is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}else if(trim($patientEthnicity)==9) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Ethnicity is patient refused or unknown";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			
			$patientRaceEthnicity = $patientRace.$patientEthnicity;
			$content = getValHcfaUb($content,$patientRaceEthnicity,2068,2069,"left","A");
			
			$patientAddressStreet = stripslashes($row["ptStreet"]); //REQUIRED
			$content = getValHcfaUb($content,$patientAddressStreet,2299,2338,"left","A");
			$patientNameFirst = stripslashes($row["ptFname"]); //REQUIRED
			$content = getValHcfaUb($content,$patientNameFirst,2339,2358,"left","A");
			$patientNameLast = stripslashes($row["ptLname"]); //REQUIRED
			$content = getValHcfaUb($content,$patientNameLast,2359,2388,"left","A");
	
			
			$primaryInsuredNameFirst = trim(ucfirst(stripslashes($objpriinsData->subscriber_fname))); //REQUIRED
			$content = getValHcfaUb($content,$primaryInsuredNameFirst,2389,2408,"left","A");
			$primaryInsuredNameLast = trim(ucfirst(stripslashes($objpriinsData->subscriber_lname))); //REQUIRED
			$content = getValHcfaUb($content,$primaryInsuredNameLast,2409,2438,"left","A");
	
			$secondaryInsuredNameFirst = trim(ucfirst(stripslashes($objsecinsData->subscriber_fname))); //REQUIRED
			$content = getValHcfaUb($content,$secondaryInsuredNameFirst,2439,2458,"left","A");
			$secondaryInsuredNameLast = trim(ucfirst(stripslashes($objsecinsData->subscriber_lname))); //REQUIRED
			$content = getValHcfaUb($content,$secondaryInsuredNameLast,2459,2488,"left","A");
		
			$tertiaryInsuredNameFirst = trim(ucfirst(stripslashes($objterinsData->subscriber_fname))); //REQUIRED
			$content = getValHcfaUb($content,$tertiaryInsuredNameFirst,2489,2508,"left","A");
			$tertiaryInsuredNameLast = trim(ucfirst(stripslashes($objterinsData->subscriber_lname))); //REQUIRED
			$content = getValHcfaUb($content,$tertiaryInsuredNameLast,2509,2538,"left","A");
	
			$payerNamePrimary = trim(stripslashes($insCompArr[$row["primaryInsuranceCoId"]]['name'])); //REQUIRED
			$content = getValHcfaUb($content,$payerNamePrimary,2539,2563,"left","A");
			$payerNameSecondary = trim(stripslashes($insCompArr[$row["secondaryInsuranceCoId"]]['name'])); //REQUIRED
			$content = getValHcfaUb($content,$payerNameSecondary,2564,2588,"left","A");
			$payerNameTertiary = trim(stripslashes($insCompArr[$row["tertiaryInsuranceCoId"]]['name'])); //REQUIRED
			$content = getValHcfaUb($content,$payerNameTertiary,2589,2613,"left","A");
	
			$conditionCode1 = ""; //REQUIRED 18 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode1,2614,2615,"left","A");
			$conditionCode2 = ""; //REQUIRED 19 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode2,2616,2617,"left","A");
			$conditionCode3 = ""; //REQUIRED 20 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode3,2618,2619,"left","A");
			$conditionCode4 = ""; //REQUIRED 21 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode4,2620,2621,"left","A");
			$conditionCode5 = ""; //REQUIRED 22 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode5,2622,2623,"left","A");
			$conditionCode6 = ""; //REQUIRED 23 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode6,2624,2625,"left","A");
			$conditionCode7 = ""; //REQUIRED 24 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode7,2626,2627,"left","A");
			$conditionCode8 = ""; //REQUIRED 25 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode8,2628,2629,"left","A");
			$conditionCode9 = ""; //REQUIRED 26 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode9,2630,2631,"left","A");
			$conditionCode10 = ""; //REQUIRED 27 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode10,2632,2633,"left","A");
			$conditionCode11 = ""; //REQUIRED 28 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$conditionCode11,2634,2635,"left","A");
			
			$occurrenceCode1 = ""; //REQUIRED 31a NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceCode1,2636,2637,"left","A");
			$occurrenceCode2 = ""; //REQUIRED 31b NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceCode2,2646,2647,"left","A");
			$occurrenceCode3 = ""; //REQUIRED 32a NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceCode3,2656,2657,"left","A");
			$occurrenceCode4 = ""; //REQUIRED 32b NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceCode4,2666,2667,"left","A");
			$occurrenceCode5 = ""; //REQUIRED 33a NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceCode5,2676,2677,"left","A");
			$occurrenceCode6 = ""; //REQUIRED 33b NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceCode6,2686,2687,"left","A");
			$occurrenceCode7 = ""; //REQUIRED 34a NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceCode7,2696,2697,"left","A");
			$occurrenceCode8 = ""; //REQUIRED 34b NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceCode8,2706,2707,"left","A");
			
			$occurrenceDate1 = ""; //REQUIRED 31a NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceDate1,2638,2645,"right","N");
			$occurrenceDate2 = ""; //REQUIRED 31b NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceDate2,2648,2655,"right","N");
			$occurrenceDate3 = ""; //REQUIRED 32a NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceDate3,2658,2665,"right","N");
			$occurrenceDate4 = ""; //REQUIRED 32b NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceDate4,2668,2675,"right","N");
			$occurrenceDate5 = ""; //REQUIRED 33a NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceDate5,2678,2685,"right","N");
			$occurrenceDate6 = ""; //REQUIRED 33b NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceDate6,2688,2695,"right","N");
			$occurrenceDate7 = ""; //REQUIRED 34a NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceDate7,2698,2705,"right","N");
			$occurrenceDate8 = ""; //REQUIRED 34b NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$occurrenceDate8,2708,2715,"right","N");
			
			//END PATIENT INFORMATION
			
			
			//pre($content);
			$max = max(array_keys($content));
			$maxCnt = $max+1;
			for($k=0;$k<$maxCnt;$k++) {
				$val = $content[$k];
				//if(trim($val)=="") { $val = "&nbsp;"; }
				if(trim($val)=="") { $val = " "; }
				$newContent .= $val;
			}
			$c++;
		}
	}
	
}

//echo $newContent;
$show_msg= "";
if(trim($newContent)) {
	$contentFileName = $updir."/state_report/ub_".$_SESSION["authId"].".txt";
	if(file_exists($contentFileName)) {
		unlink($contentFileName);	
	}
	file_put_contents($contentFileName,$newContent);	
	
	$show_msg= "<span style='font-family:verdana;font-size:12px;font-weight:bold; padding-left:100px;'> TN State Report (UB04) have been exported successfully.</span>";
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
						<td class="text_b_w alignCenter" style="width:250px;">Patient Name: '.$errorPtName.'</td>
						<td class="text_b_w alignCenter" style="width:250px;">Birth Date: '.$errorDob.'</td>
						<td class="text_b_w alignCenter" style="width:240px;">DOS: '.$errorDos.'</td>
						<td class="text_b_w alignCenter" style="width:250px;">Admit Date: '.$errorAdmitDate.'</td>
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

