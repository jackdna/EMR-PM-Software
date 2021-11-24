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
File: curl.php
Purpose: To Export HCFA In Fixed Length Format.
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
$insCompRes=imw_query("select id, `name`, in_house_code, Payer_id_pro, Payer_id, emdeon_payer_eligibility, ins_state_payer_code from insurance_companies ORDER BY id");
if(imw_num_rows($insCompRes)>0) {
	while($insCompRow=imw_fetch_array($insCompRes)) {
		$cId 											= $insCompRow["id"];
		$insCompArr[$cId]["name"] 						= $insCompRow["name"];
		$insCompArr[$cId]["in_house_code"] 				= $insCompRow["in_house_code"];
		$insCompArr[$cId]["Payer_id_pro"] 				= $insCompRow["Payer_id_pro"];
		$insCompArr[$cId]["Payer_id"] 					= $insCompRow["Payer_id"];
		$insCompArr[$cId]["emdeon_payer_eligibility"] 	= $insCompRow["emdeon_payer_eligibility"];
		$insCompArr[$cId]["ins_state_payer_code"] 		= $insCompRow["ins_state_payer_code"];
		
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

$newContent = "";

$eAndMCptArray = array(99201, 99202, 99203, 99204, 99205, 99211, 99212,
					   99213, 99214, 99215, 99242, 99243, 99244, 99245);


//$andQry .= " and p.charge_list_id IN(0,77926,84258,84257)"; //temporary query
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
		INNER JOIN insurance_companies inc ON (p.primaryInsuranceCoId = inc.id  AND inc.name != 'SELF PAY')
		INNER JOIN submited_record sr ON(sr.encounter_id = p.encounter_id)
		LEFT JOIN groups_new gn ON(gn.gro_id=p.gro_id)
		WHERE p.charge_list_id !='0' and p.del_status='0' and p.enc_accept_assignment!='2' ".$andQry."  
		GROUP BY p.charge_list_id
		ORDER BY pd.pid ";
		
$errorMsgArr = array();
$res = imw_query($qry) or die($qry.imw_error());
if(imw_num_rows($res)>0) {
	$c=0;
	while($row = imw_fetch_assoc($res)) {
		$charg_id = $row["charge_list_id"];
		$qrySub = "select pcl.*,cft.units as admin_cpt_unit,if(pcl.onset_date != '0000-00-00', DATE_FORMAT(pcl.onset_date, '%m%d%Y'),'') AS onset_date_format,
				cft.cpt_comments as admin_ndc,cft.cpt_desc as admin_cpt_desc,cft.cpt4_code 
				from patient_charge_list_details pcl 
				join cpt_fee_tbl cft on (cft.cpt_fee_id = pcl.procCode)
				where pcl.del_status='0' and pcl.charge_list_id = '".$charg_id."'
				and pcl.proc_selfpay!='1' and cft.not_covered = '0' and pcl.posted_status='1'
				and pcl.differ_insurance_bill != 'true'
				order by pcl.display_order,
				pcl.charge_list_detail_id limit 0,6";
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
	
			
			//START FOR VENDOR USE
			$content = array();
			$facType = "";
			$content = getValHcfaUb($content,$facType,1,1,"left","A");
			$dateYear= "";
			$content = getValHcfaUb($content,$dateYear,2,5,"right","A");
			$VendorIdentificationNumber= "";
			$content = getValHcfaUb($content,$VendorIdentificationNumber,6,7,"left","A");
			$billNumber = "";
			$content = getValHcfaUb($content,$billNumber,8,14,"left","A");
			$recordSequenceNumber = "";
			$content = getValHcfaUb($content,$recordSequenceNumber,15,16,"left","A");
			$facilityIdentificationNumber = "";
			$content = getValHcfaUb($content,$facilityIdentificationNumber,17,21,"left","A");
			$formType = "";
			$content = getValHcfaUb($content,$formType,24,25,"left","A");
			$doNotResuscitateFlag = ""; 
			$content = getValHcfaUb($content,$doNotResuscitateFlag,180,180,"left","A");
	
			
			//$tmpSelfPayPrimary = $insDataArr["self_pay_provider"][$row["patient_id"]]["primary"][$row["primaryInsuranceCoId"]]["1"];
			$tmpProgramPayerClassificationCodePrimary = trim($insCompArr[$row["primaryInsuranceCoId"]]['ins_state_payer_code']);
			if($row["primaryInsuranceCoId"]=='0') {
				$tmpProgramPayerClassificationCodePrimary = "P";
			}
			$programPayerClassificationCodePrimary = $tmpProgramPayerClassificationCodePrimary; 
			if(!trim($programPayerClassificationCodePrimary)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Primary payer is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$programPayerClassificationCodePrimary,1007,1010,"left","A");
			 
			$tmpProgramPayerClassificationCodeOther = trim($insCompArr[$row["secondaryInsuranceCoId"]]['ins_state_payer_code']);
			$programPayerClassificationCodeOther = $tmpProgramPayerClassificationCodeOther;
			$content = getValHcfaUb($content,$programPayerClassificationCodeOther,1011,1014,"left","A");
			
			$patientInitialsFirstName = ""; 
			$content = getValHcfaUb($content,$patientInitialsFirstName,1055,1056,"left","A");
			$patientInitialsLastName = ""; 
			$content = getValHcfaUb($content,$patientInitialsLastName,1057,1060,"left","A");
			$primaryInsuredInitialsFirstName = ""; 
			$content = getValHcfaUb($content,$primaryInsuredInitialsFirstName,1061,1062,"left","A");
			$primaryInsuredInitialsLastName = ""; 
			$content = getValHcfaUb($content,$primaryInsuredInitialsLastName,1063,1066,"left","A");
			$secondaryInsuredInitialsFirstName = ""; 
			$content = getValHcfaUb($content,$secondaryInsuredInitialsFirstName,1067,1068,"left","A");
			$secondaryInsuredInitialsLastName = ""; 
			$content = getValHcfaUb($content,$secondaryInsuredInitialsLastName,1069,1072,"left","A");
	
			//END FOR VENDOR USE
			
			//START PATIENT INFORMATION
			$insName = strtoupper($insCompArr[$row["primaryInsuranceCoId"]]['name']);
			$insHouseCode = strtoupper($insCompArr[$row["primaryInsuranceCoId"]]['in_house_code']);
			preg_match('/MEDICAID/',$insHouseCode,$ins_house_code);
	
			if($insName == "MEDICARE" || $insHouseCode == "MEDICARE" ){ 
				// || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_RRM_payers) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],$arr_RRM_payers)
				$tmpTypeOfHealthInsurance = 1;
			}
			else if($insName == "MEDICAID" || $insHouseCode == "MEDICAID" || count($ins_house_code)>0){
				$tmpTypeOfHealthInsurance = 2;
			}
			else if($insName == "MEDICAID/UNISYS" || $insHouseCode == "MEDICAID/UNISYS"){
				$tmpTypeOfHealthInsurance = 2;
			}
			else if($insName == "TRICARE CHAMPUS" || $insHouseCode == "TRICARE CHAMPUS"){
				$tmpTypeOfHealthInsurance = 3;
			}
			else if($insName == "CHAMPVA" || $insHouseCode == "CHAMPVA"){
				$tmpTypeOfHealthInsurance = 4;
			}
			else if($insName == "GROUP HEALTH PLAN" || $insHouseCode == "GROUP HEALTH PLAN"){
				$tmpTypeOfHealthInsurance = 5;
			}
			else if($insName == "FECA BLKLUNG" || $insHouseCode == "FECA BLKLUNG"){
				$tmpTypeOfHealthInsurance = 6;
			}else{
				$tmpTypeOfHealthInsurance = 7;
			}
			
			$typeOfHealthInsurance = $tmpTypeOfHealthInsurance; //1
			$content = getValHcfaUb($content,$typeOfHealthInsurance,26,26,"left","A");
			
			$objInsGroupNumber->policy_number = preg_replace("/[^A-Za-z0-9]/","",$objInsGroupNumber->policy_number);
			$insuredIDNumber = $objInsGroupNumber->policy_number; //1-A
			$content = getValHcfaUb($content,$insuredIDNumber,27,55,"left","A");
			$patientDateOfBirth = $row["ptDobFormat"]; //3
			$content = getValHcfaUb($content,$patientDateOfBirth,56,63,"right","N");
			
			$tmpPatientSex = "U";
			if(strtolower($row["ptSex"])=="male"){ 
				$tmpPatientSex = "M"; 
			}else if(strtolower($row["ptSex"])=="female") {
				$tmpPatientSex = "F"; 
			}
			$patientSex = $tmpPatientSex; //3
			$content = getValHcfaUb($content,$patientSex,64,64,"left","A");
			$patientAddressCity = stripslashes($row["ptCity"]); //5
			$content = getValHcfaUb($content,$patientAddressCity,65,94,"left","A");
			$PatientAddressState = stripslashes($row["ptState"]); //5
			$content = getValHcfaUb($content,$PatientAddressState,95,98,"left","A");
			$PatientAddressZipCode = stripslashes($row["ptPostalCode"]); //5
			$content = getValHcfaUb($content,$PatientAddressZipCode,99,110,"left","A");
			
			$relationship = strtolower($objInsGroupNumber->subscriber_relationship);
			
			if($relationship=="self") {
				$tmpPatientRelationshipToInsured = '1';	
			}else if($relationship=="spouse") {
				$tmpPatientRelationshipToInsured = '2';	
			}if(stristr($relationship,"child")) {
				$tmpPatientRelationshipToInsured = '3';	
			}else {
				$tmpPatientRelationshipToInsured = '4'; //OTHER	
			}
			$patientRelationshipToInsured	= $tmpPatientRelationshipToInsured; //6
			$content = getValHcfaUb($content,$patientRelationshipToInsured,111,111,"left","A");
			$insuredAddressCity 			= stripslashes($objInsGroupNumber->subscriber_city); //7
			$content = getValHcfaUb($content,$insuredAddressCity,112,141,"left","A");
			$insuredAddressState 			= stripslashes($objInsGroupNumber->subscriber_state); //7
			$content = getValHcfaUb($content,$insuredAddressState,142,145,"left","A");
			$insuredAddressZipCode 			= stripslashes($objInsGroupNumber->subscriber_postal_code); //7
			$content = getValHcfaUb($content,$insuredAddressZipCode,146,157,"left","A");
			
			if(strtolower($row["maritalStatus"])=="single") {
				$tmpPatientStatusMarital = "1";	
			}else if(strtolower($row["maritalStatus"])=="married") {
				$tmpPatientStatusMarital = "2";	
			}else {
				$tmpPatientStatusMarital = "3";
			}
			$patientStatusMarital 			= $tmpPatientStatusMarital; //8
			$content = getValHcfaUb($content,$patientStatusMarital,158,158,"left","A");
	
			$patientStatusEmployment 		= ""; //8 NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$patientStatusEmployment,159,159,"left","A");
			
			$sec_ins_name=strtoupper($insCompArr[$row["secondaryInsuranceCoId"]]['name']);
			$sec_ins_house_code=strtoupper($insCompArr[$row["secondaryInsuranceCoId"]]['in_house_code']);
			if(($sec_ins_name == "MEDICARE" || $sec_ins_house_code == "MEDICARE") && in_array(strtolower($billing_global_server_name), array('north central'))){
				$tmpOtherInsuredPolicyGrpNumber = "";
			}else{
				$tmpOtherInsuredPolicyGrpNumber = preg_replace("/[^A-Za-z0-9]/","",$objSecondaryInsGroupNumber->policy_number);
			}
			$otherInsuredPolicyGrpNumber 	= $tmpOtherInsuredPolicyGrpNumber; //9-A
			$content = getValHcfaUb($content,$otherInsuredPolicyGrpNumber,160,189,"left","A");
			
			$tmpInsurancePlanNameOrProgramNameOther = $insCompArr[$objSecondaryInsGroupNumber->provider]['name'];
			if(($sec_ins_name == "MEDICARE" || $sec_ins_house_code == "MEDICARE") && in_array(strtolower($billing_global_server_name), array('north central'))){
				$tmpInsurancePlanNameOrProgramNameOther = "";
			}
			$insurancePlanNameOrProgramNameOther = $tmpInsurancePlanNameOrProgramNameOther; //9-D
			$content = getValHcfaUb($content,$insurancePlanNameOrProgramNameOther,190,219,"left","A");
			
			$outsideLab = "N"; //20
			$content = getValHcfaUb($content,$outsideLab,240,240,"left","A");
			$outsideLabCharges = ""; //20
			$content = getValHcfaUb($content,$outsideLabCharges,241,249,"right","N");
			
			$insCaseType = $insCaseArr[$row["case_type_id"]]['ins_case_type'];
			$case_name = strtolower($insCaseTypeArr[$insCaseType]['case_name']);
			$chk_work_val = $chk_auto_val = "N";
			$chk_auto_st = "";
			if(strtolower($case_name)=="workman comp"){
				$chk_work_val="Y";
			}
			if(strtolower($case_name)=="auto"){
				$chk_auto_val="Y";
				$chk_auto_st=$patientListData->auto_state;
			}		
			$patientConditionRelatedToEmployment = $chk_work_val; //10-A
			$content = getValHcfaUb($content,$patientConditionRelatedToEmployment,250,250,"left","A");
			$patientConditionRelatedToAutoAccident = $chk_auto_val; //10-B
			$content = getValHcfaUb($content,$patientConditionRelatedToAutoAccident,251,251,"left","A");
			$patientConditionRelatedToOtherAccident = "N"; //10-C
			$content = getValHcfaUb($content,$patientConditionRelatedToOtherAccident,252,252,"left","A");
			$patientConditionRelatedPlaceState = ""; //10-B NOT FOUND CONFIRMED
			$content = getValHcfaUb($content,$patientConditionRelatedPlaceState,253,254,"left","A");
			
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
			$InsuredPolicyGroupOrFecaNumber = $show_box11_val; //11
			$content = getValHcfaUb($content,$InsuredPolicyGroupOrFecaNumber,255,284,"left","A");
			
			$insuredEmployerSchoolName = $row["ptOccupation"]; //11-B
			$content = getValHcfaUb($content,$insuredEmployerSchoolName,285,314,"left","A");
			
			if((in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_DME_payers) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],$arr_DME_payers)) && in_array(strtolower($billing_global_server_name), array('gewirtz'))){
				$tmpInsurancePlanNameOrProgramNamePrimary = "DMER-NHIC";
			}else{
				$tmpInsurancePlanNameOrProgramNamePrimary = stripslashes($objInsGroupNumber->plan_name);
			}		
			$insurancePlanNameOrProgramNamePrimary = $tmpInsurancePlanNameOrProgramNamePrimary; //11-C
			$content = getValHcfaUb($content,$insurancePlanNameOrProgramNamePrimary,315,344,"left","A");
			
			$tmpAnotherHealthBenefitPlan = "Y";
			if($objSecondaryInsGroupNumber == ""){
				$tmpAnotherHealthBenefitPlan = "N";
			}
			$anotherHealthBenefitPlan = $tmpAnotherHealthBenefitPlan; //11-D
			$content = getValHcfaUb($content,$anotherHealthBenefitPlan,345,345,"left","A");
			
			$onset_date_arr = $dignosisArr = $dateOfServiceArr = $posCodeArr = $cpt4CodeArr = $dignosisArrNew = array();
			$paidChargesArr = $totalUnitArr = $npiNumberArr = array();
			$sub_total_proc_charges = 0;
			for($d = 0;$d< count($arrpatientChargeDetails);$d++){
				
				$charge_list_detail_id = $arrpatientChargeDetails[$d]['charge_list_detail_id'];
				//ONSET DATE
				$onset_date_exp="";
				if($arrpatientChargeDetails[$d]['onset_date_format']!=""){
					$onset_date_arr[] = $arrpatientChargeDetails[$d]['onset_date_format'];
				}
				
				//DIAGNOSIS CODE
				for($f=1;$f<=12;$f++){
					if($arrpatientChargeDetails[$d]['diagnosis_id'.$f]!=""){
						$diagId = $arrpatientChargeDetails[$d]['diagnosis_id'.$f];
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
						$dignosisArr[] = $diagId;
						$dignosisArrNew[$d][] = $diagId;
					}
				}
				
				//datesOfServiceFromDate AND datesOfServiceThroughDate //24-A, 24-B
				$dateOfServiceArr[] = $row["date_of_service_format"];
				
				
				//get POS Code //24-C
				$placeOfServiceDb = $arrpatientChargeDetails[$d]['place_of_service'];
				$posCodeArr[] = $posArr[$placeOfServiceDb]['pos_code'];
				
				//get CPT4Code //24-D
				$cpt4CodeArr[] = $arrpatientChargeDetails[$d]['cpt4_code'];
				
				//CODE FOR 24-F
				if($row['acc_anes_unit']>0) {
					if(trim($arrpatientChargeDetails[$d]['cpt4_code']) != 'V2785'){
						//$arr_admin_cpt_units[$charge_list_detail_id]=$arrpatientChargeDetails[$d]['admin_cpt_unit'];
						$paidChargesArr[]	= $arrpatientChargeDetails[$d]['admin_cpt_unit'];
					}	
				}else {
					$totalcharges 		= numberformat($arrpatientChargeDetails[$d]['procCharges']* $arrpatientChargeDetails[$d]['units'],2);
					$paidCharges 		= $totalcharges;
					//$paidCharges 		= substr($paidCharges,0,-3);
					$paidCharges 		= str_ireplace('.','',$paidCharges);
					$paidCharges 		= str_ireplace(',','',$paidCharges);
					$paidCharges		= str_ireplace('$','',$paidCharges);
					$paidChargesArr[]	= $paidCharges;
				}
				
				//CODE FOR 24-G
				$total_unit = "";
				if($row['acc_anes_unit']>0) {
					$total_unit=$arrpatientChargeDetails[$d]['admin_cpt_unit']+number_format($row['acc_anes_unit'],2);
				}else if($arrpatientChargeDetails[$d]['units']) {
					$total_unit = unit_format($arrpatientChargeDetails[$d]['units']);	
				}
				
	
				//CODE FOR 24-I AND 24-J-1
				$idQualifierVal = $renderingProviderIdNumberVal = "";
				if(count($ins_house_code)>0){
					if($row["acc_anes_unit"]>0){
						//DO NOTHING
					}else if($arrpatientChargeDetails[$d]['admin_ndc']!=""){
						//DO NOTHING
					}else{
						$show_taxonomy_no=0;
						if(in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_NE_Medicaid_payers) && in_array(strtolower($billing_global_server_name), array('manahan','azar'))){
							//DO NOTHING
						}else{
							$show_taxonomy_no=1;
							$renderingProviderIdNumberVal = $userArr[$row['primaryProviderId']]['MedicaidId'];
						}
						if((in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],array('SB804','SX091','22248','22326')) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],array('SB804','SX091','22248','22326'))) && in_array(strtolower($billing_global_server_name), array('ues','lehigh','westfall','tutela')) && $show_taxonomy_no==0){
							$idQualifierVal = "ZZ";
							$renderingProviderIdNumberVal = $userArr[$row['primaryProviderId']]['TaxonomyId'];
						}
					}
				}else{
					if($row["acc_anes_unit"]>0){
						//DO NOTHING
					}else if($arrpatientChargeDetails[$d]['admin_ndc']!=""){
						//DO NOTHING
					}else{
						if((in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],array('SB804','SX091','22248','22326')) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],array('SB804','SX091','22248','22326'))) && in_array(strtolower($billing_global_server_name), array('ues','lehigh','westfall','tutela'))){
							$idQualifierVal = "ZZ";
							$renderingProviderIdNumberVal = $userArr[$row['primaryProviderId']]['TaxonomyId'];
						}else{
							//DO NOTHING	
						}
					}	
				}
				$idQualifierArr[] = $idQualifierVal; //24-I
				$renderingProviderIdNumberArr[] = $renderingProviderIdNumberVal; //24-J-1	
				
			
				//CODE FOR 24-J-2
				if(strtoupper($insCompArr[$row["primaryInsuranceCoId"]]['name'])=="NHIC CORP"){
					$npiNumberArr[] = "";
				}else{
					if(in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_NE_Medicaid_payers) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],$arr_NE_Medicaid_payers)){
						if(in_array(strtolower($billing_global_server_name), array('azar'))){
							$npiNumberArr[] = "";
						}else{
							$npiNumberArr[] = $userArr[$row['primaryProviderId']]['user_npi'];
						}
					}else{
						if($row["group_institution"] == 1){
							$npiNumberArr[] = $row['group_NPI'];
						}else{	
							$npiNumberArr[] = $userArr[$row['primaryProviderId']]['user_npi'];
						}
					}
				}
				
				//CODE FOR 28
				$sub_total_proc_charges += str_ireplace(',','',$arrpatientChargeDetails[$d]['procCharges'] * $arrpatientChargeDetails[$d]['units']);			
			
			}
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
	
			$dateOfCurrentIllnessInjuryPregnancy = $onset_date_arr[0]; //14
			$content = getValHcfaUb($content,$dateOfCurrentIllnessInjuryPregnancy,346,353,"right","N");
			$firstDatePatientHadSameOrSimilarIllness = ""; //15 NOT FOUND
			$content = getValHcfaUb($content,$firstDatePatientHadSameOrSimilarIllness,354,361,"right","N");
			$datesPatientUnableToWorkFromDate = ""; //16 NOT FOUND
			$content = getValHcfaUb($content,$datesPatientUnableToWorkFromDate,362,369,"right","N");
			$datesPatientUnableToWorkThroughDate = ""; //16 NOT FOUND
			$content = getValHcfaUb($content,$datesPatientUnableToWorkThroughDate,370,377,"right","N");
			
			if($row["reff_phy_id"]){
				$refferPhysicianId = $row["reff_phy_id"];
			}
			else{
				$refferPhysicianId = $row["ptPrimaryCareId"];
			}		
			if(in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_DME_payers) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],$arr_DME_payers)){
				$tmpIdNumberOfReferringProvider 	= "";
			}else{
				if(count($ins_house_code)>0){
					$tmpIdNumberOfReferringProvider = $reffPhyArr[$refferPhysicianId]['MDCD'];
				}else{
					$tmpIdNumberOfReferringProvider = $reffPhyArr[$refferPhysicianId]['MDCR'];
				}
			}		
	
			$qualifierCodeOfReferringProvider = ""; //17-A
			$content = getValHcfaUb($content,$qualifierCodeOfReferringProvider,378,379,"left","A");
			$idNumberOfReferringProvider = $tmpIdNumberOfReferringProvider; //17-A
			$content = getValHcfaUb($content,$idNumberOfReferringProvider,380,402,"left","A");
			$npiNumberOfReferringProvider = $reffPhyArr[$refferPhysicianId]['NPI']; //17-B
			$content = getValHcfaUb($content,$npiNumberOfReferringProvider,403,412,"left","A");
			$hospitalizationRelatedCurrentSvcsFromDate = $row["date_of_service_format"]; //18
			$content = getValHcfaUb($content,$hospitalizationRelatedCurrentSvcsFromDate,413,420,"right","N");
			$hospitalizationRelatedCurrentSvcsThroughDate = $row["disch_date_format"]; //18
			$content = getValHcfaUb($content,$hospitalizationRelatedCurrentSvcsThroughDate,421,428,"right","N");
			
			$diagnosisOrNatureOfIllnessOrInjuryCode1 = $dignosisArr[0]; //21
			$content = getValHcfaUb($content,$diagnosisOrNatureOfIllnessOrInjuryCode1,429,436,"left","A");
			$diagnosisOrNatureOfIllnessOrInjuryCode2 = $dignosisArr[1]; //21
			$content = getValHcfaUb($content,$diagnosisOrNatureOfIllnessOrInjuryCode2,437,444,"left","A");
			$diagnosisOrNatureOfIllnessOrInjuryCode3 = $dignosisArr[2]; //21
			$content = getValHcfaUb($content,$diagnosisOrNatureOfIllnessOrInjuryCode3,445,452,"left","A");
			$diagnosisOrNatureOfIllnessOrInjuryCode4 = $dignosisArr[3]; //21
			$content = getValHcfaUb($content,$diagnosisOrNatureOfIllnessOrInjuryCode4,453,460,"left","A");
	
			$datesOfServiceFromStartPos = $datesOfServiceThroughStartPos = $placeOfServiceStartPos  = 0;
			$proceduresServicesAndSuppliesStartPos = $diagnosisPointerStartPos = $chargesStartPos = $daysOrUnitsStartPos = 0;
			$idQualifierStartPos = $renderingProviderIdNumberStartPos = $renderingProviderNpiNumberStartPos = 0;
			$revenueChargeExists =false;
			for($g=0; $g<6;$g++) {
				$datesOfServiceFromDate 	= $dateOfServiceArr[$g]; //24-A
				$datesOfServiceFromStartPos = $datesOfServiceFromStartPos ? ($datesOfServiceFromStartPos+8) : 461;
				$datesOfServiceFromEndPos 	= ($datesOfServiceFromStartPos+7);
				$content = getValHcfaUb($content,$datesOfServiceFromDate,$datesOfServiceFromStartPos,$datesOfServiceFromEndPos,"right","N");	
	
				$datesOfServiceThroughDate	= $dateOfServiceArr[$g]; //24-A
				$datesOfServiceThroughStartPos = $datesOfServiceThroughStartPos ? ($datesOfServiceThroughStartPos+8) : 509;
				$datesOfServiceThroughEndPos 	= ($datesOfServiceThroughStartPos+7);
				$content = getValHcfaUb($content,$datesOfServiceThroughDate,$datesOfServiceThroughStartPos,$datesOfServiceThroughEndPos,"right","N");	
			
				$placeOfService			= $posCodeArr[$g]; //24-B
				$placeOfServiceStartPos = $placeOfServiceStartPos ? ($placeOfServiceStartPos+2) : 557;
				$placeOfServiceEndPos 	= ($placeOfServiceStartPos+1);
				$content = getValHcfaUb($content,$placeOfService,$placeOfServiceStartPos,$placeOfServiceEndPos,"left","A");
				
				$proceduresServicesAndSupplies			= $cpt4CodeArr[$g]; //24-D
				$proceduresServicesAndSuppliesStartPos 	= $proceduresServicesAndSuppliesStartPos ? ($proceduresServicesAndSuppliesStartPos+14) : 581;
				$proceduresServicesAndSuppliesEndPos 	= ($proceduresServicesAndSuppliesStartPos+13);
				$content = getValHcfaUb($content,$proceduresServicesAndSupplies,$proceduresServicesAndSuppliesStartPos,$proceduresServicesAndSuppliesEndPos,"left","A");
	
				$Pointer = "";
				$diagnosisPointerArr = array();
				foreach($dignosisArrNew[$g] as $key=>$dia_val){
					if(empty($dia_val) == false){
						$diagnosisPointerArr[] = $diagnose_arr[$dia_val];
					}
				}	
				if(in_array(strtolower($billing_global_server_name), array('manahan','centerforsight'))){
					$Pointer = $diagnosisPointerArr[0];
				}else{
					if((in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],array('SKMI0')) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],array('SKMI0'))) && in_array(strtolower($billing_global_server_name), array('shoreline'))){
						$Pointer = join('',arraySlice($diagnosisPointerArr,0,4));
					}else{
						//$Pointer = join(',',arraySlice($diagnosisPointerArr,0,4));
						$Pointer = join('',arraySlice($diagnosisPointerArr,0,4));
					}
				}
				$diagnosisPointer			= $Pointer; //24-E
				$diagnosisPointerStartPos 	= $diagnosisPointerStartPos ? ($diagnosisPointerStartPos+4) : 665;
				$diagnosisPointerEndPos 	= ($diagnosisPointerStartPos+3);
				$content = getValHcfaUb($content,$diagnosisPointer,$diagnosisPointerStartPos,$diagnosisPointerEndPos,"left","A");
				
				$charges		= $paidChargesArr[$g]; //24-F
				$chargesStartPos= $chargesStartPos ? ($chargesStartPos+10) : 689;
				$chargesEndPos 	= ($chargesStartPos+9);
				if(trim($charges)) {
					$revenueChargeExists = true;
				}			
				$content = getValHcfaUb($content,$charges,$chargesStartPos,$chargesEndPos,"right","N");
				
				$daysOrUnits			= $totalUnitArr[$g]; //24-G
				$daysOrUnitsStartPos 	= $daysOrUnitsStartPos ? ($daysOrUnitsStartPos+3) : 749;
				$daysOrUnitsEndPos 		= ($daysOrUnitsStartPos+2);
				$content = getValHcfaUb($content,$daysOrUnits,$daysOrUnitsStartPos,$daysOrUnitsEndPos,"left","A");
	
				$idQualifier			= $idQualifierArr[$g]; //24-I
				$idQualifierStartPos 	= $idQualifierStartPos ? ($idQualifierStartPos+2) : 785;
				$idQualifierEndPos 		= ($idQualifierStartPos+1);
				$content = getValHcfaUb($content,$idQualifier,$idQualifierStartPos,$idQualifierEndPos,"left","A");
	
				$renderingProviderIdNumber			= $renderingProviderIdNumberArr[$g]; //24-J-1
				$renderingProviderIdNumberStartPos 	= $renderingProviderIdNumberStartPos ? ($renderingProviderIdNumberStartPos+11) : 797;
				$renderingProviderIdNumberEndPos 	= ($renderingProviderIdNumberStartPos+10);
				$content = getValHcfaUb($content,$renderingProviderIdNumber,$renderingProviderIdNumberStartPos,$renderingProviderIdNumberEndPos,"left","A");
	
				$renderingProviderNpiNumber				= $npiNumberArr[$g]; //24-J-2
				$renderingProviderNpiNumberStartPos 	= $renderingProviderNpiNumberStartPos ? ($renderingProviderNpiNumberStartPos+10) : 863;
				$renderingProviderNpiNumberEndPos 		= ($renderingProviderNpiNumberStartPos+9);
				$content = getValHcfaUb($content,$renderingProviderNpiNumber,$renderingProviderNpiNumberStartPos,$renderingProviderNpiNumberEndPos,"left","A");
				
			}
	
			$emergencyEmg1 = ""; //24-C NOT FOUND
			$content = getValHcfaUb($content,$emergencyEmg1,569,570,"left","A");
			$emergencyEmg2 = ""; //24-C NOT FOUND
			$content = getValHcfaUb($content,$emergencyEmg2,571,572,"left","A");
			$emergencyEmg3 = ""; //24-C NOT FOUND
			$content = getValHcfaUb($content,$emergencyEmg3,573,574,"left","A");
			$emergencyEmg4 = ""; //24-C NOT FOUND
			$content = getValHcfaUb($content,$emergencyEmg4,575,576,"left","A");
			$emergencyEmg5 = ""; //24-C NOT FOUND
			$content = getValHcfaUb($content,$emergencyEmg5,577,578,"left","A");
			$emergencyEmg6 = ""; //24-C NOT FOUND
			$content = getValHcfaUb($content,$emergencyEmg6,579,580,"left","A");
			
	
	
	
	
			$epsdtFamilyPlan1 = ""; //24-H NOT FOUND
			$content = getValHcfaUb($content,$epsdtFamilyPlan1,767,769,"left","A");
			$epsdtFamilyPlan2 = ""; //24-H NOT FOUND
			$content = getValHcfaUb($content,$epsdtFamilyPlan2,770,772,"left","A");
			$epsdtFamilyPlan3 = ""; //24-H NOT FOUND
			$content = getValHcfaUb($content,$epsdtFamilyPlan3,773,775,"left","A");
			$epsdtFamilyPlan4 = ""; //24-H NOT FOUND
			$content = getValHcfaUb($content,$epsdtFamilyPlan4,776,778,"left","A");
			$epsdtFamilyPlan5 = ""; //24-H NOT FOUND
			$content = getValHcfaUb($content,$epsdtFamilyPlan5,779,781,"left","A");
			$epsdtFamilyPlan6 = ""; //24-H NOT FOUND
			$content = getValHcfaUb($content,$epsdtFamilyPlan6,782,784,"left","A");
	
	
			$renderingProviderIdNumber1 = ""; //24-J-1 NOT FOUND
			$content = getValHcfaUb($content,$renderingProviderIdNumber1,797,807,"left","A");
			$renderingProviderIdNumber2 = ""; //24-J-1 NOT FOUND
			$content = getValHcfaUb($content,$renderingProviderIdNumber2,808,818,"left","A");
			$renderingProviderIdNumber3 = ""; //24-J-1 NOT FOUND
			$content = getValHcfaUb($content,$renderingProviderIdNumber3,819,829,"left","A");
			$renderingProviderIdNumber4 = ""; //24-J-1 NOT FOUND
			$content = getValHcfaUb($content,$renderingProviderIdNumber4,830,840,"left","A");
			$renderingProviderIdNumber5 = ""; //24-J-1 NOT FOUND
			$content = getValHcfaUb($content,$renderingProviderIdNumber5,841,851,"left","A");
			$renderingProviderIdNumber6 = ""; //24-J-1 NOT FOUND
			$content = getValHcfaUb($content,$renderingProviderIdNumber6,852,862,"left","A");
	
	
			$federalTaxIdNumber = preg_replace("/[^A-Za-z0-9]/","",$row["group_Federal_EIN"]); //25
			$content = getValHcfaUb($content,$federalTaxIdNumber,923,937,"left","A");
			$federalTaxIdNumberSsn = ""; //25 NOT FOUND
			$content = getValHcfaUb($content,$federalTaxIdNumberSsn,938,938,"left","A");
			$federalTaxIdNumberEin = "E"; //25
			$content = getValHcfaUb($content,$federalTaxIdNumberEin,939,939,"left","A");
			
			$patientAccountNumber = $row["patient_id"]; //26
			$content = getValHcfaUb($content,$patientAccountNumber,940,954,"left","A");
			
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
			
			$content = getValHcfaUb($content,$totalCharges,955,964,"right","N");
			$billingProviderNpiNumber = $row["group_NPI"]; //33-A
			$content = getValHcfaUb($content,$billingProviderNpiNumber,965,974,"left","A");
			
			
			//CODE FOR 33-B
			$tmpBillingProviderOtherIdNumber= "";
			$userTaxonomyId 				= $userArr[$row['primaryProviderId']]['TaxonomyId'];
			if(in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_NE_Medicaid_payers) && stristr($row["groupName"],'Manahan')){
				$tmpBillingProviderOtherIdNumber = "193400000X";
			}else if(in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_NE_Medicaid_payers) &&  stristr($row["groupName"],'Papillion')){
				$tmpBillingProviderOtherIdNumber = "261QA1903X";
			}else if(in_array(strtolower($billing_global_server_name), array('manahan'))){
				$tmpBillingProviderOtherIdNumber = preg_replace("/[^A-Za-z0-9]/","",$row["group_Federal_EIN"]);
			}else if($row["group_Federal_EIN"]=="721288671" && in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_NE_Medicaid_payers) && in_array(strtolower($billing_global_server_name), array('azar'))){
				$tmpBillingProviderOtherIdNumber = "1660060";
			}else if($row["group_Federal_EIN"]=="721410176" && in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_NE_Medicaid_payers) && in_array(strtolower($billing_global_server_name), array('azar'))){
				$tmpBillingProviderOtherIdNumber = "1796298";
			}else if((in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],array('HPRNT')) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],array('HPRNT'))) && in_array(strtolower($billing_global_server_name), array('azar'))){
				$tmpBillingProviderOtherIdNumber = "216586";
			}else if((in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],array('SB804','SX091','22248','22326')) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],array('SB804','SX091','22248','22326'))) && in_array(strtolower($billing_global_server_name), array('westfall','tutela'))){
				if($userTaxonomyId!=""){
					$userTaxonomyId="ZZ".$userTaxonomyId;
				}
				
				$tmpBillingProviderOtherIdNumber = $userTaxonomyId;
			}else if(in_array(strtolower($billing_global_server_name), array('tyson'))){
				if($userTaxonomyId!=""){
					$userTaxonomyId="ZZ".$userTaxonomyId;
				}
				$tmpBillingProviderOtherIdNumber = $userTaxonomyId;
			}else{
				if(in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_RRM_payers) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],$arr_RRM_payers) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id_pro'],$arr_DME_payers) || in_array($insCompArr[$row["primaryInsuranceCoId"]]['Payer_id'],$arr_DME_payers)){
					$tmpBillingProviderOtherIdNumber = "";
				}else{
					$tmpBillingProviderOtherIdNumber = $insurance_Practice_Code_id;
				}
			}
			$billingProviderOtherIdNumber = $tmpBillingProviderOtherIdNumber; //33-B
			$content = getValHcfaUb($content,$billingProviderOtherIdNumber,975,994,"left","A");
			
			$tmpPatientSocialSecurityNumber	 = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$row["patientSSN"])));
			if(!$tmpPatientSocialSecurityNumber || $tmpPatientSocialSecurityNumber == 0) {
				$tmpPatientSocialSecurityNumber = "999999999";	
			}
			$patientSocialSecurityNumber = $tmpPatientSocialSecurityNumber; 
			if(!trim($patientSocialSecurityNumber)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "SSN is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}else if(strlen(trim($patientSocialSecurityNumber))<9) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "SSN is invalid length";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$patientSocialSecurityNumber,995,1004,"left","A");
			
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
			
			$patientRaceEthnicity = $patientRace.$patientEthnicity;		
			$content = getValHcfaUb($content,$patientRaceEthnicity,1005,1006,"left","A");
	
			$patientAddressStreet = stripslashes($row["ptStreet"]); //FL - 5
			$content = getValHcfaUb($content,$patientAddressStreet,1241,1280,"left","A");
			$patientAddressStreet2 = stripslashes($row["ptStreet2"]); //FL - 5
			$content = getValHcfaUb($content,$patientAddressStreet2,1281,1320,"left","A");
			$patientFirstName = stripslashes($row["ptFname"]);; //FL - 2
			$content = getValHcfaUb($content,$patientFirstName,1321,1340,"left","A");
			$patientLastName = stripslashes($row["ptLname"]); //FL - 2
			$content = getValHcfaUb($content,$patientLastName,1341,1370,"left","A");
			$primaryInsuredFirstName = trim(ucfirst(stripslashes($objpriinsData->subscriber_fname))); //FL - 4
			$content = getValHcfaUb($content,$primaryInsuredFirstName,1371,1390,"left","A");
			$primaryInsuredLastName = trim(ucfirst(stripslashes($objpriinsData->subscriber_lname))); //FL - 4
			$content = getValHcfaUb($content,$primaryInsuredLastName,1391,1420,"left","A");
			$secondaryInsuredFirstName = trim(ucfirst(stripslashes($objsecinsData->subscriber_fname))); //FL - 9
			$content = getValHcfaUb($content,$secondaryInsuredFirstName,1421,1440,"left","A");
			$secondaryInsuredLastName = trim(ucfirst(stripslashes($objsecinsData->subscriber_lname))); //FL - 9
			$content = getValHcfaUb($content,$secondaryInsuredLastName,1441,1470,"left","A");
			
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

$show_msg= "";
if(trim($newContent)) {
	$contentFileName = $updir."/state_report/electronic_".$_SESSION["authId"].".txt";
	if(file_exists($contentFileName)) {
		unlink($contentFileName);	
	}
	file_put_contents($contentFileName,$newContent);	
	
	$show_msg= "<span style='font-family:verdana;font-size:12px;font-weight:bold; padding-left:100px;'> TN State Report (Electronic) have been exported successfully.</span>";
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
