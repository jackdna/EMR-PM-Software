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
$phy_name_str 		= trim(implode(',',$_REQUEST["Physician"]));
$group_name_str 	= trim(implode(',',$_REQUEST["groups"]));
$from_date 			= getDateFormatDB($Start_date);
$to_date 			= getDateFormatDB($End_date);

//START CODE OF USER NPI
$userNpiArr = $userRowArr = array();
$userNpiRes=imw_query("select id, user_npi, licence, fname, mname, lname from users ORDER BY id");
if(imw_num_rows($userNpiRes)>0) {
	while($userNpiRow=imw_fetch_array($userNpiRes)) {
		$uId = $userNpiRow["id"];
		$userNpiArr[$uId] = $userNpiRow["user_npi"];
		$userLicenseArr[$uId] = $userNpiRow["licence"];	
		$userRowArr[$uId] = $userNpiRow;
	}
}
//END CODE OF USER NPI

$andQry = "";
$andgrpQry = "";
if($group_name_str) {
	$andQry .= " and p.gro_id IN(".$group_name_str.") ";
	$andgrpQry .= " and gro_id IN(".$group_name_str.") ";
}
if($phy_name_str) {
	$andQry .= " and p.primaryProviderId IN(".$phy_name_str.")";
}
if($from_date && $from_date !="--" && $to_date && $to_date !="--") {
	$andQry .= " and p.date_of_service between '".$from_date."' and '".$to_date."' ";	
}

$insCompArr = array();
$insCompRes=imw_query("select id, `name`, in_house_code, Payer_id_pro, Payer_id, emdeon_payer_eligibility, ins_state_payer_code, institutional_Code_id, contact_address, City, State, Zip from insurance_companies ORDER BY id");
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
		$insCompArr[$cId]['contact_address'] 			= $insCompRow["contact_address"];
		$insCompArr[$cId]['City'] 						= $insCompRow["City"];
		$insCompArr[$cId]['State'] 						= $insCompRow["State"];
		$insCompArr[$cId]['Zip'] 						= $insCompRow["Zip"];
	}
}

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
$modifierRes=imw_query("select * from modifiers_tbl where 1=1 and delete_status = '0'");
if(imw_num_rows($modifierRes)>0) {
	while($modifierRow=imw_fetch_array($modifierRes)){
		$modifyId = $modifierRow['modifiers_id'];
		$modifierCodeArr[$modifyId]=$modifierRow['modifier_code'];
	}
}
//END CODE OF MODIFIER CODE

//START CODE OF USER NPI
$userNpiArr = $userRowArr = array();
$userNpiRes=imw_query("select id, user_npi, licence, fname, mname, lname from users ORDER BY id");
if(imw_num_rows($userNpiRes)>0) {
	while($userNpiRow=imw_fetch_array($userNpiRes)) {
		$uId = $userNpiRow["id"];
		$userNpiArr[$uId] = $userNpiRow["user_npi"];
		$userLicenseArr[$uId] = $userNpiRow["licence"];	
		$userRowArr[$uId] = $userNpiRow;
	}
}
//END CODE OF USER NPI

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
//------------------------ TOS Detail ------------------------//
$tos_data_arr =array();
$selQry = "select tos_id,tos_code,tos_prac_cod from tos_tbl ORDER BY tos_id";
$res = imw_query($selQry) or die(imw_error().$selQry);
while($row = imw_fetch_array($res)){
	$tos_data_arr[$row["tos_id"]]=$row;
}
//------------------------ TOS Detail ------------------------//

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
//------------------------ Payment Detail ------------------------//
$pay_qry = imw_query("select pcd.paidForProc + pcd.overPayment as paidForProc,
				   pcd.paidDate,pcpi.paymentClaims,pcpi.encounter_id,
				   pcd.charge_list_detail_id,pcpi.insProviderId,pcpi.insCompany 
				   from patient_charges_detail_payment_info pcd 
				   join patient_chargesheet_payment_info pcpi on pcpi.payment_id = pcd.payment_id
				   where pcpi.paid_by = 'Insurance'
				   and pcd.deletePayment = '0'");
while($pay_row=imw_fetch_array($pay_qry)){			
	$payment_data[$pay_row['encounter_id']][$pay_row['insProviderId']][]=$pay_row;
}

//START CODE OF INSURANCE COMPANY
$countryCodeArr = array("AF","AX","AL","DZ","AS","AD","AO","AI","AQ","AG","AR","AM","AW","AU","AT","AZ","BS","BH","BD","BB","BY","BE","BZ","BJ","BM","BT","BO","BA","BW","BV","BR","IO","BN","BG","BF","BI","KH","CM","CA","CV","KY","CF","TD","CL","CN","CX","CC","CO","KM","CG","CD","CK","CR","CI","HR","CU","CY","CZ","DK","DJ","DM","DO","EC","EG","SV","GQ","ER","EE","ET","FK","FO","FJ","FI","FR","GF","PF","TF","GA","GM","GE","DE","GH","GI","GR","GL","GD","GP","GU","GT","GG","GN","GW","GY","HT","HM","VA","HN","HK","HU","IS","IN","ID","IR","IQ","IE","IM","IL","IT","JM","JP","JE","JO","KZ","KE","KI","KP","KR","KW","KG","LA","LV","LB","LS","LR","LY","LI","LT","LU","MO","MK","MG","MW","MY","MV","ML","MT","MH","MQ","MR","MU","YT","MX","FM","MD","MC","MN","ME","MS","MA","MZ","MM","NA","NR","NP","NL","AN","NC","NZ","NI","NE","NG","NU","NF","MP","NO","OM","PK","PW","PS","PA","PG","PY","PE","PH","PN","PL","PT","PR","QA","RE","RO","RU","RW","BL","SH","KN","LC","MF","PM","VC","WS","SM","ST","SA","SN","RS","SC","SL","SG","SK","SI","SB","SO","ZA","GS","ES","LK","SD","SR","SJ","SZ","SE","CH","SY","TW","TJ","TZ","TH","TL","TG","TK","TO","TT","TN","TR","TM","TC","TV","UG","UA","AE","GB","US","UM","UY","UZ","VU","VE","VN","VG","VI","WF","EH","YE","ZM","ZW");

$mainqry = "SELECT p.*, if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%m%d%Y'),'') AS admit_date_format, if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%m-%d-%Y'),'') AS admit_date_format_new, if(p.disch_date != '0000-00-00 00:00:00', DATE_FORMAT(p.disch_date, '%m%d%Y'),'') AS disch_date_format,
if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m%d%Y'),'') as date_of_service_format,
if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m%d%y'),'') as date_of_service_format_short, if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m-%d-%Y'),'') as date_of_service_format_new,
gn.group_Federal_EIN, gn.group_NPI, gn.group_institution, gn.name as groupName, 
pd.city AS ptCity, pd.state AS ptState, pd.postal_code AS ptPostalCode, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m%d%Y'),'') AS ptDobFormat, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m-%d-%Y'),'') AS ptDobFormatNew,
pd.sex AS ptSex, pd.ss AS patientSSN, pd.race, pd.ethnicity, pd.street AS ptStreet, pd.street2 AS ptStreet2,fname AS ptFname, lname AS ptLname, TRIM(CONCAT(pd.lname,', ',pd.fname,' ',pd.mname)) AS ptName, 
pd.default_facility AS ptDefaultFacility, pd.country_code AS ptCountryCode, pd.status AS maritalStatus, pd.occupation AS ptOccupation, pd.primary_care_id AS ptPrimaryCareId
FROM patient_charge_list p
INNER JOIN patient_data pd ON(pd.pid = p.patient_id)
INNER JOIN submited_record sr ON(sr.encounter_id = p.encounter_id)
LEFT JOIN groups_new gn ON(gn.gro_id=p.gro_id)
LEFT JOIN insurance_companies inc ON (p.primaryInsuranceCoId = inc.id AND inc.institutional_type ='INST_ONLY')
WHERE p.charge_list_id !='0' and p.del_status='0' and p.enc_accept_assignment!='2' ".$andQry."  
GROUP BY p.charge_list_id
ORDER BY pd.pid ";
$res = imw_query($mainqry) or die($qry.imw_error());
$main_arr = $record_type_a_arr = $chrListIdArr = array();
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$charg_id = $row["charge_list_id"];
		$chrListIdArr[$charg_id] = $charg_id;
		$main_arr[$charg_id]['A'][] = $row;	
		$main_arr[$charg_id]['E'][] = $row;	
	}
}

$charge_list_id_str = join(',',$chrListIdArr);

$qrySub = "select pcl.*, date_format(patient_charge_list.date_of_service, '%m%d%y') as date_of_service, cft.units as admin_cpt_unit,if(pcl.onset_date != '0000-00-00', DATE_FORMAT(pcl.onset_date, '%m%d%Y'),'') AS onset_date_format,
cft.cpt_comments as admin_ndc,cft.cpt_desc as admin_cpt_desc,cft.cpt4_code,pcl.procCode,pcl.modifier_id1,pcl.modifier_id2,pcl.modifier_id3 
from patient_charge_list_details pcl 
join patient_charge_list on patient_charge_list.charge_list_id = pcl.charge_list_id
join cpt_fee_tbl cft on cft.cpt_fee_id = pcl.procCode
where pcl.del_status='0' and pcl.charge_list_id IN (".$charge_list_id_str.")
and cft.not_covered = '0' and pcl.posted_status='1' 
and pcl.differ_insurance_bill != 'true'
order by pcl.display_order,pcl.charge_list_detail_id";
$imw_infoB=imw_query($qrySub) or die($qrySub.imw_error());	
if(imw_num_rows($imw_infoB)>0) {
	while($row = imw_fetch_assoc($imw_infoB)) {
		$charg_id = $row["charge_list_id"];
		$main_arr[$charg_id]['B'][] = $row;			
		$main_arr[$charg_id]['C'][] = $row;				
		$main_arr[$charg_id]['G'][] = $row;			
	}
}

$a=0;$newContent='';
$errorMsgArr = array();
foreach($main_arr as $main_arr_key => $main_arr_val){
	$content = $bcontent= array();
	if(isset($main_arr_val['A'])){
		foreach($main_arr_val['A'] as $main_arr_val_a){
			if($a>0) {
				//$newContent .= "<br>";	
				$newContent .= "\n";
			}
			$a++;
			$record_type = "A";
			$content = getValHcfaUb($content,$record_type,1,1,"left","A"); //01 (Line 1)
			
			//14 2 15 North Carolina Healthcare Association (NCHA) Facility ID
 			$NCHAFacilityID = "302322";
			$content = getValHcfaUb($content,$NCHAFacilityID,2,15,"left","A"); //01 (Line 1)
			
			//1 16 16 Test or Production Run (enter T or P)
			$test_or_production = "P";
			$content = getValHcfaUb($content,$test_or_production,16,16,"left","A"); //01 (Line 1)
			
			//1 24 17 40 Patient Control Number (Patient Account Number)
			$patien_control_number = $main_arr_val_a['patient_id'];
			$content = getValHcfaUb($content,$patien_control_number,17,40,"left","A"); //01 (Line 1)
			
			//1 2 41 42 Record Sequence Number (value = 01)
			$record_sequence_number = "01";
			$content = getValHcfaUb($content,$record_sequence_number,41,42,"left","A"); //01 (Line 1)
			
			//1 24 43 66 Medical Record Number
			$medical_record_number = $main_arr_val_a["encounter_id"];
			$content = getValHcfaUb($content,$medical_record_number,43,66,"left","A"); //01 (Line 1)
			
			//1 4 67 70 Type of Bill
			$type_of_bill = "831";
			$content = getValHcfaUb($content,$type_of_bill,67,70,"left","A"); //01 (Line 1)
			
			//1 4 71 74 Federal Tax Sub-ID Number
			$federal_tax_subid = "";
			$content = getValHcfaUb($content,$federal_tax_subid,71,74,"left","A"); //01 (Line 1)
					 
			// 05 25 1 10 75 84 Federal Tax Number
			$federal_tax_number = preg_replace("/[^A-Za-z0-9]/","",$main_arr_val_a["group_Federal_EIN"]);
			if($federal_tax_number && substr($federal_tax_number, 2, 1) != '-') { 
				$federal_tax_number = substr($federal_tax_number, 0, 2).'-'.substr($federal_tax_number, 2);   
			}
			$content = getValHcfaUb($content,$federalTaxNumber,75,84,"left","A"); //5b
			
			//* / ** 06 24a 1 8 85 92 Statement Covers Period - From (see Special Instructions)
			$statement_covers_period_from = $main_arr_val_a["date_of_service_format"];
			$content = getValHcfaUb($content,$statement_covers_period_from,85,92,"left","A"); //01 (Line 1)
			
			//* / ** 06 24a 1 8 93 100 Statement Covers Period - Through (see Special Instructions)
			$statement_covers_period_through = $main_arr_val_a["date_of_service_format"];
			$content = getValHcfaUb($content,$statement_covers_period_through,93,100,"left","A"); //01 (Line 1)

			//08 1 19 101 119 Patient Name - ID
			$patient_nameId = $main_arr_val_a["patient_id"];
			$content = getValHcfaUb($content,$patient_nameId,101,119,"left","A"); //01 (Line 1)
			
			//* / ** 08 2 1 18 120 137 Patient Last Name
			$patient_last_name = $main_arr_val_a["ptLname"];
			$content = getValHcfaUb($content,$patient_last_name,120,137,"left","A"); //01 (Line 1)
			
			//* / ** 08 2 1 9 138 146 Patient First Name
			$patient_first_name = $main_arr_val_a["ptFname"];
			$content = getValHcfaUb($content,$patient_first_name,138,146,"left","A"); //01 (Line 1)
		
			//* / ** 08 2 1 3 147 149 Patient Name Suffix
			$patient_first_suffix =  $main_arr_val_a["suffix"];
			$content = getValHcfaUb($content,$patient_first_suffix,147,149,"left","A"); //01 (Line 1)
			
			//* / ** 09 5 1 40 150 189 Patient Address - Street (line 1) See Record type G for line 2
			$patient_address_street1 = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$main_arr_val_a["ptStreet"])));
			$content = getValHcfaUb($content,$patient_address_street1,150,189,"left","A"); //01 (Line 1)
			
			//* / ** 09 5 1 30 190 219 Patient Address - City
			$patient_address_city = stripslashes($main_arr_val_a["ptCity"]);
			$content = getValHcfaUb($content,$patient_address_city,190,219,"left","A"); //01 (Line 1)
			
			//* / ** 09 5 1 2 220 221 Patient Address - State
			$patient_address_state = stripslashes($main_arr_val_a["ptState"]);
			$content = getValHcfaUb($content,$patient_address_state,220,221,"left","A"); //01 (Line 1)

			//* / ** 09 5 1 9 222 230 Patient Address - ZIP
			$patient_address_zip = stripslashes($main_arr_val_a["ptPostalCode"]);
			$content = getValHcfaUb($content,$patient_address_zip,222,230,"left","A"); //01 (Line 1)

			//* 09 1 2 231 232 Patient Address - Country Code
			$patient_address_countrycode = substr(stripslashes($main_arr_val_a["ptCountryCode"]),0,2);
			$content = getValHcfaUb($content,$patient_address_countrycode,231,232,"left","A"); //01 (Line 1)
			
			if(trim($patient_address_countrycode) && !in_array($patient_address_countrycode,$countryCodeArr)) {
				$errorMsgArr[$main_arr_val_a["patient_id"]]["patient_id"][] 	= $main_arr_val_a["patient_id"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["ptName"][] 		= $main_arr_val_a["ptName"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["msg"][] 			= "Patient Error Code: 009e - 155 Patient Country Invalid. The Patient Country does not match any values listed in Appendix D";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dob"] []			= $main_arr_val_a["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dos"] []			= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["admitDate"][]		= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["encounterId"][]	= $main_arr_val_a["encounter_id"];
			}
			
			
			//1 3 233 235 Reserved
			$reserved = "";
			$content = getValHcfaUb($content,$reserved,233,235,"left","A"); //01 (Line 1)
			
			//1 9 236 244 Patient Social Security Number
			$tmpPatientSocialSecurityNumber = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$main_arr_val_a["patientSSN"]))); //REQUIRED
			if(!$tmpPatientSocialSecurityNumber || $tmpPatientSocialSecurityNumber == 0) {
				$tmpPatientSocialSecurityNumber = "";	
			}
			if(!trim($tmpPatientSocialSecurityNumber)) {
				$errorMsgArr[$main_arr_val_a["patient_id"]]["patient_id"][] 	= $main_arr_val_a["patient_id"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["ptName"][] 		= $main_arr_val_a["ptName"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["msg"][] 			= "SSN is missing";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dob"] []			= $main_arr_val_a["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dos"] []			= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["admitDate"][]		= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["encounterId"][]	= $main_arr_val_a["encounter_id"];
			}else if(strlen(trim($tmpPatientSocialSecurityNumber))<9) {
				$errorMsgArr[$main_arr_val_a["patient_id"]]["patient_id"][] 	= $main_arr_val_a["patient_id"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["ptName"][] 		= $main_arr_val_a["ptName"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["msg"][] 			= "SSN is invalid length";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dob"] []			= $main_arr_val_a["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dos"] []			= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["admitDate"][]		= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["encounterId"][]	= $main_arr_val_a["encounter_id"];
			}
			
			$patient_social_security_number = $tmpPatientSocialSecurityNumber; //REQUIRED
			$content = getValHcfaUb($content,$patient_social_security_number,236,244,"left","A"); //01 (Line 1)
						
			//* / ** 10 3 1 8 245 252 Patient Birthdate (MMDDYYYY)
			$patient_birthdate = $main_arr_val_a["ptDobFormat"];
			$content = getValHcfaUb($content,$patient_birthdate,245,252,"left","A"); //01 (Line 1)
			
			if(!trim($patient_birthdate)) {
				$errorMsgArr[$main_arr_val_a["patient_id"]]["patient_id"][] 	= $main_arr_val_a["patient_id"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["ptName"][] 		= $main_arr_val_a["ptName"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["msg"][] 			= "Error Code: 0010 - 101 The patient’s Birth Date is blank or not a valid date";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dob"] []			= $main_arr_val_a["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dos"] []			= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["admitDate"][]		= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["encounterId"][]	= $main_arr_val_a["encounter_id"];
			}


			//* / ** 11 3 1 1 253 253 Patient Sex
			$patient_sex = strtoupper(substr($main_arr_val_a['ptSex'],0,1));
			if(!$patient_sex) { $patient_sex = "U"; }
			$content = getValHcfaUb($content,$patient_sex,253,253,"left","A"); //01 (Line 1)
			if($patient_sex!="M" && $patient_sex!="F" && $patient_sex!="U") {
				$errorMsgArr[$main_arr_val_a["patient_id"]]["patient_id"][] 	= $main_arr_val_a["patient_id"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["ptName"][] 		= $main_arr_val_a["ptName"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["msg"][] 			= "Error Code: 0011 - Sex Code Invalid. The entry is not M, F, or U";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dob"] []			= $main_arr_val_a["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dos"] []			= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["admitDate"][]		= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["encounterId"][]	= $main_arr_val_a["encounter_id"];
			}

			//* 12 1 8 254 261 Admission Date (MMDDYYYY)
			$admissionStartOfCareDateTmp = $main_arr_val_a["admit_date_format_short"];	
			if($main_arr_val_a["acc_anes_unit"]<=0 && $main_arr_val_a["admit_date_format_short"]=="") {
				$admissionStartOfCareDateTmp = date("mdy",strtotime($main_arr_val_a["date_of_service"]));
			}
			$admission_date = $admissionStartOfCareDateTmp;
			$content = getValHcfaUb($content,$admission_date,236,244,"left","A"); //01 (Line 1)
			
			//*1 13 1 2 262 263 Admission Hour
			$admissionHourTmp = "";
			$admitDateExp = explode(" ",$main_arr_val_a["admit_date"]);
			if(trim($admitDateExp[1]) != "00:00:00"){
				$admissionHourTmp = substr(trim($admitDateExp[1]),0,5);
			}
			$admission_hour = $admissionHourTmp;
			$content = getValHcfaUb($content,$admission_hour,262,263,"left","A"); //01 (Line 1)

			//*1 14 1 1 264 264 Type of Admission/Visit
			$type_of_admission = "";
			$content = getValHcfaUb($content,$type_of_admission,264,264,"left","A"); //01 (Line 1)
			
			//* 15 1 1 265 265 Point of Origin
			$point_of_origin = "";
			$content = getValHcfaUb($content,$point_of_origin,265,265,"left","A"); //01 (Line 1)
			
			//*1 16 1 2 266 267 Discharge Hour
			$discharge_hour = "";
			$content = getValHcfaUb($content,$discharge_hour,266,267,"left","A"); //01 (Line 1)
			
			//* 17 1 2 268 269 Patient Discharge Status
			$patient_discharge_status = "01";
			$content = getValHcfaUb($content,$patient_discharge_status,268,269,"left","A"); //01 (Line 1)
			
			//1 2 270 271 Reserved
			$reserved = "";
			$content = getValHcfaUb($content,$reserved,270,271,"left","A"); //01 (Line 1)
			
			//71 1 4 272 275 MS-DRG Code
			$ms_drg_code = "";
			$content = getValHcfaUb($content,$ms_drg_code,272,275,"left","A"); //01 (Line 1)

			//* / ** 76 1 11 276 286 Attending Physician - NPI
			$attending_physician_npi = $pro_pri_npi=$userNpiArr[$main_arr_val_a["primaryProviderId"]];
			$content = getValHcfaUb($content,$attending_physician_npi,276,286,"left","A"); //01 (Line 1)

			//* / ** 76 1 11 287 297 Attending Physician - QUAL/ID
			$attending_physician_qual_id = "";
			$content = getValHcfaUb($content,$attending_physician_qual_id,287,297,"left","A"); //01 (Line 1)

			//* 77 1 11 298 308 Operating Physician - NPI
			$operating_physician_npi = "";
			$content = getValHcfaUb($content,$operating_physician_npi,298,308,"left","A"); //01 (Line 1)

			//* 77 1 11 309 319 Operating Physician - QUAL/ID
			$operating_physician_qual_id = "";
			$content = getValHcfaUb($content,$operating_physician_qual_id,309,319,"left","A"); //01 (Line 1)

			//* 78 1 13 320 332 Other Physician ID - QUAL/NPI
			$other_physician_id_qual_npi = "";
			$content = getValHcfaUb($content,$other_physician_id_qual_npi,320,332,"left","A"); //01 (Line 1)
			
			//* 78 1 11 333 343 Other Physician ID - QUAL/ID
			$other_physician_id_qual_npi1 = "";
			$content = getValHcfaUb($content,$other_physician_id_qual_npi1,333,343,"left","A"); //01 (Line 1)
			
			//* 79 1 13 344 356 Other Physician ID - QUAL/NPI
			$other_physician_id_qual_npi2 = "";
			$content = getValHcfaUb($content,$other_physician_id_qual_npi2,344,356,"left","A"); //01 (Line 1)
			
			//* 79 1 11 357 367 Other Physician ID - QUAL/ID
			$other_physician_id_qual_npi3 = "";
			$content = getValHcfaUb($content,$other_physician_id_qual_npi3,357,367,"left","A"); //01 (Line 1)
		
			//1 3 368 370 Reserved 
			$reserved = "";
			$content = getValHcfaUb($content,$reserved,368,370,"left","A"); //01 (Line 1)

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
			$patientEthnicityeExp 	= explode(",",$main_arr_val_a["ethnicity"]);
			$tmpPatientEthnicity 	= trim($arrEthnicity[$patientEthnicityeExp[0]]); //? trim($arrEthnicity[$patientEthnicityeExp[0]]) : "E8";
			if(trim($patientEthnicityeExp[0])!="" && trim($tmpPatientEthnicity)=="") {
				$tmpPatientEthnicity = "1";	//IF ETHNICITY EXIST BUT NOT IN ARRAY THEN SET ALL TO NON HISPANIC
				if(stristr(trim(strtolower($patientEthnicityeExp[0])),'hispanic')) {
					$tmpPatientEthnicity = "2";
				}
			}
			if(!trim($tmpPatientEthnicity)) {
				$errorMsgArr[$main_arr_val_a["patient_id"]]["patient_id"][] 	= $main_arr_val_a["patient_id"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["ptName"][] 		= $main_arr_val_a["ptName"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["msg"][] 			= "Ethnicity - Patient Hispanic/Latino Origin or Descent is blank or not valid";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dob"] []			= $main_arr_val_a["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dos"] []			= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["admitDate"][]		= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["encounterId"][]	= $main_arr_val_a["encounter_id"];
			}else if(trim($tmpPatientEthnicity)==9) {
				$errorMsgArr[$main_arr_val_a["patient_id"]]["patient_id"][] 	= $main_arr_val_a["patient_id"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["ptName"][] 		= $main_arr_val_a["ptName"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["msg"][] 			= "Ethnicity is patient refused or unknown";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dob"] []			= $main_arr_val_a["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dos"] []			= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["admitDate"][]		= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["encounterId"][]	= $main_arr_val_a["encounter_id"];
			}
			$patient_ethnicity = $tmpPatientEthnicity;
			$content = getValHcfaUb($content,$patient_ethnicity,371,371,"left","A"); //01 (Line 1)
			
			//RaceEthnicity REQUIRED
			$arrRace = array(
			"American Indian or Alaska Native" => 1,
			"Asian" => 2,
			"Black or African American" => 3,
			"Native Hawaiian or Other Pacific Islander" => 4,
			"Latin American" => 6,
			"White" => "5",	//Caucasian	= White								
			"Declined to Specify" => 9);
			$patientRaceExp = explode(",",$main_arr_val_a["race"]);
			$patientRace = trim($arrRace[$patientRaceExp[0]]);
			
			if((trim($patientRace)=="" || !trim($patientRace)) && $patientRaceExp[0] != "" ) { //CODE FOR OTHER RACE
				$patientRace = "6";
			}
			
			if(!trim($patientRace)) {
				$errorMsgArr[$main_arr_val_a["patient_id"]]["patient_id"][] 	= $main_arr_val_a["patient_id"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["ptName"][] 		= $main_arr_val_a["ptName"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["msg"][] 			= "Race is missing";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dob"] []			= $main_arr_val_a["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dos"] []			= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["admitDate"][]		= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["encounterId"][]	= $main_arr_val_a["encounter_id"];
			}else if(trim($patientRace)=="N" || trim($patientRace)=="U") {
				$errorMsgArr[$main_arr_val_a["patient_id"]]["patient_id"][] 	= $main_arr_val_a["patient_id"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["ptName"][] 		= $main_arr_val_a["ptName"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["msg"][] 			= "Race is patient refused or unknown or other";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dob"] []			= $main_arr_val_a["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["dos"] []			= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["admitDate"][]		= $main_arr_val_a["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_a["patient_id"]]["encounterId"][]	= $main_arr_val_a["encounter_id"];
			}
			$content = getValHcfaUb($content,$patientRace,372,372,"left","A"); //01 (Line 1)
			
			//1 1 373 373 Reserved 
			$reserved = "";
			$content = getValHcfaUb($content,$reserved,373,373,"left","A"); //01 (Line 1)
			
			//1 1 374 374 Reserved 
			$reserved = "";
			$content = getValHcfaUb($content,$reserved,374,374,"left","A"); //01 (Line 1)
			
			//* / ** 1 4 375 378 Primary Payer Identification (see Special Instructions)
			$insName = strtoupper($insCompArr[$main_arr_val_a["primaryInsuranceCoId"]]['name']);
			$insHouseCode = strtoupper($insCompArr[$main_arr_val_a["primaryInsuranceCoId"]]['in_house_code']);
			preg_match('/MEDICAID/',$insHouseCode,$ins_house_code);
			if($insName == "MEDICARE" || $insHouseCode == "MEDICARE" ){ 
				$primary_payer_identification = "MA";
			}
			else if($insName == "MEDICAID" || $insHouseCode == "MEDICAID" || count($ins_house_code)>0){
				$primary_payer_identification = "MC";
			}
			else if($insName == "CENTRAL CERTIFICATION" || $insHouseCode == "CENTRAL CERTIFICATION"){
				$primary_payer_identification = 10;
			}
			else if($insName == "OTHER NON-FEDERAL PROGRAMS" || $insHouseCode == "OTHER NON-FEDERAL PROGRAMS"){
				$primary_payer_identification = 11;
			}
			else if($insName == "PREFERRED PROVIDER ORGANIZATION" || $insHouseCode == "PREFERRED PROVIDER ORGANIZATION"){
				$primary_payer_identification = 12;
			}
			else if($insName == "POINT OF SERVICE" || $insHouseCode == "POINT OF SERVICE"){
				$primary_payer_identification = 13;
			}
			else if($insName == "EXCLUSIVE PROVIDER ORGANIZATION" || $insHouseCode == "EXCLUSIVE PROVIDER ORGANIZATION"){
				$primary_payer_identification = 14;
			}
			else if($insName == "INDEMNITY INSURANCE" || $insHouseCode == "INDEMNITY INSURANCE"){
				$primary_payer_identification = 15;
			}
			else if($insName == "HEALTH MAINTENANCE ORGANIZATION" || $insHouseCode == "HEALTH MAINTENANCE ORGANIZATION"){
				$primary_payer_identification = 15;
			}
			else if($insName == "HEALTH MAINTENANCE ORGANIZATION MEDICARE RISK" || $insHouseCode == "HEALTH MAINTENANCE ORGANIZATION MEDICARE RISK"){
				$primary_payer_identification = 16;
			}
			else if($insName == "AUTOMOBILE MEDICAL" || $insHouseCode == "AUTOMOBILE MEDICAL"){
				$primary_payer_identification = "AM";
			}
			else if($insName == "BLUE CROSS" || $insHouseCode == "BLUE CROSS"){
				$primary_payer_identification = "BL";
			}
			else if($insName == "BLUE SHIELD" || $insHouseCode == "BLUE SHIELD"){
				$primary_payer_identification = "BL";
			}
			else if($insName == "CHAMPUS" || $insHouseCode == "CHAMPUS"){
				$primary_payer_identification = "CH";
			}
			else if($insName == "COMMERCIAL INSURANCE CO." || $insHouseCode == "COMMERCIAL INSURANCE CO."){
				$primary_payer_identification = "CI";
			}
			else if($insName == "DISABILITY" || $insHouseCode == "DISABILITY"){
				$primary_payer_identification = "DS";
			}
			else if($insName == "HEALTH MAINTENANCE ORGANIZATION" || $insHouseCode == "HEALTH MAINTENANCE ORGANIZATION"){
				$primary_payer_identification = "HM";
			}
			else if($insName == "LIABILITY" || $insHouseCode == "LIABILITY"){
				$primary_payer_identification = "LI";
			}
			else if($insName == "LIABILITY MEDICAL" || $insHouseCode == "LIABILITY MEDICAL"){
				$primary_payer_identification = "LM";
			}
			else if($insName == "OTHER FEDERAL PROGRAM" || $insHouseCode == "OTHER FEDERAL PROGRAM"){
				$primary_payer_identification = "OF";
			}
			else if($insName == "TITLE V" || $insHouseCode == "TITLE V"){
				$primary_payer_identification = "TV";
			}
			else if($insName == "VETERAN ADMINISTRATION PLAN" || $insHouseCode == "VETERAN ADMINISTRATION PLAN"){
				$primary_payer_identification = "VA";
			}
			else if($insName =="WORKERS COMPENSATION HEALTH CLAIM" || $insHouseCode == "WORKERS COMPENSATION HEALTH CLAIM"){
				$primary_payer_identification = "WC";
			}
			else if($insName == "MUTUALLY DEFINED" || $insHouseCode == "MUTUALLY DEFINED"){
				$primary_payer_identification = "ZZ";
			}
			else{
				$primary_payer_identification = "09";
			}
			$content = getValHcfaUb($content,$primary_payer_identification,375,378,"left","A"); //01 (Line 1)
			
			//* / ** 1 4 379 382 Secondary Payer Identification (see Special Instructions)
			$insNameSec = strtoupper($insCompArr[$main_arr_val_a["secondaryInsuranceCoId"]]['name']);
			$insHouseCodeSec = strtoupper($insCompArr[$main_arr_val_a["secondaryInsuranceCoId"]]['in_house_code']);
			preg_match('/MEDICAID/',$insHouseCodeSec,$insHouseCodeSec);
			if($insNameSec == "MEDICARE" || $insHouseCodeSec == "MEDICARE" ){ 
				$secondary_payer_identification = "MA";
			}
			else if($insNameSec == "MEDICAID" || $insHouseCodeSec == "MEDICAID" || count($ins_house_code)>0){
				$secondary_payer_identification = "MC";
			}
			else if($insNameSec == "CENTRAL CERTIFICATION" || $insHouseCodeSec == "CENTRAL CERTIFICATION"){
				$secondary_payer_identification = 10;
			}
			else if($insNameSec == "OTHER NON-FEDERAL PROGRAMS" || $insHouseCodeSec == "OTHER NON-FEDERAL PROGRAMS"){
				$secondary_payer_identification = 11;
			}
			else if($insNameSec == "PREFERRED PROVIDER ORGANIZATION" || $insHouseCodeSec == "PREFERRED PROVIDER ORGANIZATION"){
				$secondary_payer_identification = 12;
			}
			else if($insNameSec == "POINT OF SERVICE" || $insHouseCodeSec == "POINT OF SERVICE"){
				$secondary_payer_identification = 13;
			}
			else if($insNameSec == "EXCLUSIVE PROVIDER ORGANIZATION" || $insHouseCodeSec == "EXCLUSIVE PROVIDER ORGANIZATION"){
				$secondary_payer_identification = 14;
			}
			else if($insNameSec == "INDEMNITY INSURANCE" || $insHouseCodeSec == "INDEMNITY INSURANCE"){
				$secondary_payer_identification = 15;
			}
			else if($insNameSec == "HEALTH MAINTENANCE ORGANIZATION" || $insHouseCodeSec == "HEALTH MAINTENANCE ORGANIZATION"){
				$secondary_payer_identification = 15;
			}
			else if($insNameSec == "HEALTH MAINTENANCE ORGANIZATION MEDICARE RISK" || $insHouseCodeSec == "HEALTH MAINTENANCE ORGANIZATION MEDICARE RISK"){
				$secondary_payer_identification = 16;
			}
			else if($insNameSec == "AUTOMOBILE MEDICAL" || $insHouseCodeSec == "AUTOMOBILE MEDICAL"){
				$secondary_payer_identification = "AM";
			}
			else if($insNameSec == "BLUE CROSS" || $insHouseCodeSec == "BLUE CROSS"){
				$secondary_payer_identification = "BL";
			}
			else if($insNameSec == "BLUE SHIELD" || $insHouseCodeSec == "BLUE SHIELD"){
				$secondary_payer_identification = "BL";
			}
			else if($insNameSec == "CHAMPUS" || $insHouseCodeSec == "CHAMPUS"){
				$secondary_payer_identification = "CH";
			}
			else if($insNameSec == "COMMERCIAL INSURANCE CO." || $insHouseCodeSec == "COMMERCIAL INSURANCE CO."){
				$secondary_payer_identification = "CI";
			}
			else if($insNameSec == "DISABILITY" || $insHouseCodeSec == "DISABILITY"){
				$secondary_payer_identification = "DS";
			}
			else if($insNameSec == "HEALTH MAINTENANCE ORGANIZATION" || $insHouseCodeSec == "HEALTH MAINTENANCE ORGANIZATION"){
				$secondary_payer_identification = "HM";
			}
			else if($insNameSec == "LIABILITY" || $insHouseCodeSec == "LIABILITY"){
				$secondary_payer_identification = "LI";
			}
			else if($insNameSec == "LIABILITY MEDICAL" || $insHouseCodeSec == "LIABILITY MEDICAL"){
				$secondary_payer_identification = "LM";
			}
			else if($insNameSec == "OTHER FEDERAL PROGRAM" || $insHouseCodeSec == "OTHER FEDERAL PROGRAM"){
				$secondary_payer_identification = "OF";
			}
			else if($insNameSec == "TITLE V" || $insHouseCodeSec == "TITLE V"){
				$secondary_payer_identification = "TV";
			}
			else if($insNameSec == "VETERAN ADMINISTRATION PLAN" || $insHouseCodeSec == "VETERAN ADMINISTRATION PLAN"){
				$secondary_payer_identification = "VA";
			}
			else if($insNameSec =="WORKERS COMPENSATION HEALTH CLAIM" || $insHouseCodeSec == "WORKERS COMPENSATION HEALTH CLAIM"){
				$secondary_payer_identification = "WC";
			}
			else if($insNameSec == "MUTUALLY DEFINED" || $insHouseCodeSec == "MUTUALLY DEFINED"){
				$secondary_payer_identification = "ZZ";
			}
			else{
				$secondary_payer_identification = "09";
			}
			$content = getValHcfaUb($content,$secondary_payer_identification,379,382,"left","A"); //01 (Line 1)
			
			//* / ** 1 4 383 386 Tertiary Payer Identification (see Special Instructions)
			$insNameTer = strtoupper($insCompArr[$main_arr_val_a["tertiaryInsuranceCoId"]]['name']);
			$insHouseCodeTer = strtoupper($insCompArr[$main_arr_val_a["tertiaryInsuranceCoId"]]['in_house_code']);
			preg_match('/MEDICAID/',$insHouseCodeTer,$insHouseCodeTer);
			if($insNameTer == "MEDICARE" || $insHouseCodeTer == "MEDICARE" ){ 
				$tertiary_payer_identification = "MA";
			}
			else if($insNameTer == "MEDICAID" || $insHouseCodeTer == "MEDICAID" || count($ins_house_code)>0){
				$tertiary_payer_identification = "MC";
			}
			else if($insNameTer == "CENTRAL CERTIFICATION" || $insHouseCodeTer == "CENTRAL CERTIFICATION"){
				$tertiary_payer_identification = 10;
			}
			else if($insNameTer == "OTHER NON-FEDERAL PROGRAMS" || $insHouseCodeTer == "OTHER NON-FEDERAL PROGRAMS"){
				$tertiary_payer_identification = 11;
			}
			else if($insNameTer == "PREFERRED PROVIDER ORGANIZATION" || $insHouseCodeTer == "PREFERRED PROVIDER ORGANIZATION"){
				$tertiary_payer_identification = 12;
			}
			else if($insNameTer == "POINT OF SERVICE" || $insHouseCodeTer == "POINT OF SERVICE"){
				$tertiary_payer_identification = 13;
			}
			else if($insNameTer == "EXCLUSIVE PROVIDER ORGANIZATION" || $insHouseCodeTer == "EXCLUSIVE PROVIDER ORGANIZATION"){
				$tertiary_payer_identification = 14;
			}
			else if($insNameTer == "INDEMNITY INSURANCE" || $insHouseCodeTer == "INDEMNITY INSURANCE"){
				$tertiary_payer_identification = 15;
			}
			else if($insNameTer == "HEALTH MAINTENANCE ORGANIZATION" || $insHouseCodeTer == "HEALTH MAINTENANCE ORGANIZATION"){
				$tertiary_payer_identification = 15;
			}
			else if($insNameTer == "HEALTH MAINTENANCE ORGANIZATION MEDICARE RISK" || $insHouseCodeTer == "HEALTH MAINTENANCE ORGANIZATION MEDICARE RISK"){
				$tertiary_payer_identification = 16;
			}
			else if($insNameTer == "AUTOMOBILE MEDICAL" || $insHouseCodeTer == "AUTOMOBILE MEDICAL"){
				$tertiary_payer_identification = "AM";
			}
			else if($insNameTer == "BLUE CROSS" || $insHouseCodeTer == "BLUE CROSS"){
				$tertiary_payer_identification = "BL";
			}
			else if($insNameTer == "BLUE SHIELD" || $insHouseCodeTer == "BLUE SHIELD"){
				$tertiary_payer_identification = "BL";
			}
			else if($insNameTer == "CHAMPUS" || $insHouseCodeTer == "CHAMPUS"){
				$tertiary_payer_identification = "CH";
			}
			else if($insNameTer == "COMMERCIAL INSURANCE CO." || $insHouseCodeTer == "COMMERCIAL INSURANCE CO."){
				$tertiary_payer_identification = "CI";
			}
			else if($insNameTer == "DISABILITY" || $insHouseCodeTer == "DISABILITY"){
				$tertiary_payer_identification = "DS";
			}
			else if($insNameTer == "HEALTH MAINTENANCE ORGANIZATION" || $insHouseCodeTer == "HEALTH MAINTENANCE ORGANIZATION"){
				$tertiary_payer_identification = "HM";
			}
			else if($insNameTer == "LIABILITY" || $insHouseCodeTer == "LIABILITY"){
				$tertiary_payer_identification = "LI";
			}
			else if($insNameTer == "LIABILITY MEDICAL" || $insHouseCodeTer == "LIABILITY MEDICAL"){
				$tertiary_payer_identification = "LM";
			}
			else if($insNameTer == "OTHER FEDERAL PROGRAM" || $insHouseCodeTer == "OTHER FEDERAL PROGRAM"){
				$tertiary_payer_identification = "OF";
			}
			else if($insNameTer == "TITLE V" || $insHouseCodeTer == "TITLE V"){
				$tertiary_payer_identification = "TV";
			}
			else if($insNameTer == "VETERAN ADMINISTRATION PLAN" || $insHouseCodeTer == "VETERAN ADMINISTRATION PLAN"){
				$tertiary_payer_identification = "VA";
			}
			else if($insNameTer =="WORKERS COMPENSATION HEALTH CLAIM" || $insHouseCodeTer == "WORKERS COMPENSATION HEALTH CLAIM"){
				$tertiary_payer_identification = "WC";
			}
			else if($insNameTer == "MUTUALLY DEFINED" || $insHouseCodeTer == "MUTUALLY DEFINED"){
				$tertiary_payer_identification = "ZZ";
			}
			else{
				$tertiary_payer_identification = "09";
			}
			$content = getValHcfaUb($content,$tertiary_payer_identification,383,386,"left","A"); //01 (Line 1)
			
			//1 14 387 400 Reserved for future use
			$reserved = "";
			$content = getValHcfaUb($content,$reserved,387,400,"left","A"); //01 (Line 1)
		
			$max = max(array_keys($content));
			$maxCnt = $max+1;
			for($k=0;$k<$maxCnt;$k++) {
				$val = $content[$k];
				//if(trim($val)=="") { $val = "&nbsp;"; }
				if(trim($val)=="") { $val = " "; }
				$newContent .= $val;
			}
		}
		
	}
	
	if(isset($main_arr_val['B'])){
		if($newContent) { $newContent.="\n"; } $b=0;
		foreach($main_arr_val['B'] as $main_arr_val_b){
			$bcontent = array();
			if($b>0){ $newContent .= "\n"; } $b++;
			
			$record_type = "B";
			$bcontent = getValHcfaUb($bcontent,$record_type,1,1,"left","A"); //01 (Line 1)
			
			$NCHAFacilityID = "302322";
			$bcontent = getValHcfaUb($bcontent,$NCHAFacilityID,2,15,"left","A"); //01 (Line 1)
			
			$reserved = "";
			$bcontent = getValHcfaUb($bcontent,$reserved,16,16,"left","A"); //01 (Line 1)
			
			$patient_control_number = $main_arr_val_b['patient_id'];
			$bcontent = getValHcfaUb($bcontent,$patient_control_number,17,40,"left","A"); //01 (Line 1)
			
			if($b <= 9) { $b = '0'.$b; }
			$record_sequence_number = $b;
			$bcontent = getValHcfaUb($bcontent,$record_sequence_number,41,42,"left","A"); //01 (Line 1)
				
			$revenue_code = $rCodeArr[$main_arr_val_b['rev_code']];
			$bcontent = getValHcfaUb($bcontent,$revenue_code,43,70,"left","A"); //01 (Line 1)
			
			if(empty($revenue_code) == true){
				$errorMsgArr[$main_arr_val_b["patient_id"]]["patient_id"][] 	= $main_arr_val_b["patient_id"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["ptName"][] 		= $main_arr_val_b["ptName"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["msg"][] 			= "Revenue charge is missing";
				$errorMsgArr[$main_arr_val_b["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$main_arr_val_b["patient_id"]]["dob"] []			= $main_arr_val_b["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["dos"] []			= $main_arr_val_b["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["admitDate"][]		= $main_arr_val_b["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["encounterId"][]	= $main_arr_val_b["encounter_id"];
			}
			
			$proc_cod							= $main_arr_val_b['procCode'];
			$cpt4_code							= $cpt4CodeArr[$proc_cod];
			$modifier_id						= $main_arr_val_b['modifier_id1'];
			$modifier_id2						= $main_arr_val_b['modifier_id2'];
			$modifier_id3						= $main_arr_val_b['modifier_id3'];
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
			$hcpcs_codes = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$cpt4_code.$modifier_code)));
			$bcontent = getValHcfaUb($bcontent,$hcpcs_codes,71,168,"left","A"); //01 (Line 1)
			
			$service_date = $main_arr_val_b['date_of_service'];
			$bcontent = getValHcfaUb($bcontent,$service_date,169,224,"left","A"); //01 (Line 1)
			
			$units_of_service = $main_arr_val_b['units'];
			$bcontent = getValHcfaUb($bcontent,$units_of_service,225,273,"left","A"); //01 (Line 1)
			
			$total_chargesby_revenue_code = preg_replace("/[^A-Za-z0-9]/","",$main_arr_val_b['totalAmount']);
			$bcontent = getValHcfaUb($bcontent,$total_chargesby_revenue_code,274,336,"left","A"); //01 (Line 1)
			
			if(!trim($total_chargesby_revenue_code)) {
				$errorMsgArr[$main_arr_val_b["patient_id"]]["patient_id"][] 	= $main_arr_val_b["patient_id"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["ptName"][] 		= $main_arr_val_b["ptName"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["msg"][] 			= "Sum of individual charges do not add up to total charges";
				$errorMsgArr[$main_arr_val_b["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$main_arr_val_b["patient_id"]]["dob"] []			= $main_arr_val_b["ptDobFormatNew"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["dos"] []			= $main_arr_val_b["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["admitDate"][]		= $main_arr_val_b["date_of_service_format_new"];
				$errorMsgArr[$main_arr_val_b["patient_id"]]["encounterId"][]	= $main_arr_val_b["encounter_id"];
			}
			
			$non_covered_charges_by_revenue_code = "";
			$bcontent = getValHcfaUb($bcontent,$non_covered_charges_by_revenue_code,337,399,"left","A"); //01 (Line 1)
			
			$reserved = "";
			$bcontent = getValHcfaUb($bcontent,$reserved,400,400,"left","A"); //01 (Line 1)
	
			$max = max(array_keys($bcontent));
			$maxCnt = $max+1;
			for($k=0;$k<$maxCnt;$k++) {
				$val = $bcontent[$k];
				//if(trim($val)=="") { $val = "&nbsp;"; }
				if(trim($val)=="") { $val = " "; }
				$newContent .= $val;
				
			}
		}
	}
	
	if(isset($main_arr_val['C'])){
		if($newContent) { $newContent.="\n"; } $c=0;
		foreach($main_arr_val['C'] as $main_arr_val_c){
			$ccontent = $dignosis = array();
			if($c>0){ $newContent .= "\n"; } $c++;
			$record_type = "C";
			$ccontent = getValHcfaUb($ccontent,$record_type,1,1,"left","A"); //01 (Line 1)
			
			$NCHAFacilityID = "302322";
			$ccontent = getValHcfaUb($ccontent,$NCHAFacilityID,2,15,"left","A"); //01 (Line 1)
			
			$reserved = "";
			$ccontent = getValHcfaUb($ccontent,$reserved,16,16,"left","A"); //01 (Line 1)
			
			$patient_control_number = $main_arr_val_c['patient_id'];
			$ccontent = getValHcfaUb($ccontent,$patient_control_number,17,40,"left","A"); //01 (Line 1)
			
			if($c <= 9) { $c = '0'.$c; }
			$record_sequence_number = $c;
			$ccontent = getValHcfaUb($ccontent,$record_sequence_number,41,42,"left","A"); //01 (Line 1)
			
			$enc_icd10 = $main_arr_val_c["enc_icd10"];
			if($enc_icd10>0){
				$enc_icd10_ind="0";
			}else{
				$enc_icd10_ind="9";
			}
			$dx_version_qualifier = $enc_icd10_ind;
			$ccontent = getValHcfaUb($ccontent,$dx_version_qualifier,43,43,"left","A"); //01 (Line 1)
			
			$admitting_diagnosis = "";
			$ccontent = getValHcfaUb($ccontent,$admitting_diagnosis,44,50,"left","A"); //01 (Line 1)
			
			$patient_reason_for_visit_code = "";
			$ccontent = getValHcfaUb($ccontent,$patient_reason_for_visit_code,51,71,"left","A"); //01 (Line 1)
	
			$external_cause_of_injury_code = "";
			$ccontent = getValHcfaUb($ccontent,$external_cause_of_injury_code,72,95,"left","A"); //01 (Line 1)
			
			//START CODE FOR DIAGNOSIS CODE 67
			for($f=1;$f<=12;$f++){
				if($main_arr_val_c['diagnosis_id'.$f]){
					$diagId = $main_arr_val_c['diagnosis_id'.$f];
					$diagId = preg_replace("/[^A-Za-z0-9]/","",$diagId);
					$dignosis[]=$diagId;
				}
			}
			$dignosis = array_unique($dignosis);
			
			$principal_diagnosis_code = array_values(array_filter($dignosis));
			$ccontent = getValHcfaUb($ccontent,$principal_diagnosis_code,96,103,"left","A"); //01 (Line 1)


			$proc_cod							= $main_arr_val_h['procCode'];
			$cpt4_code							= $cpt4CodeArr[$proc_cod];
			$cpt4_code = $cpt4_code.$main_arr_val_h['date_of_service'];
			$principal_icd10_pro_code_date = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$cpt4_code)));
		
			$ccontent = getValHcfaUb($ccontent,$principal_icd10_pro_code_date,104,118,"left","A"); //01 (Line 1)
			
			for($k=1;$k<=17;$k++) {
				$other_diagnosis 	= $dignosis[$k];
				$ccontent = getValHcfaUb($ccontent,$other_diagnosis,119,254,"left","A"); //01 (Line 1)	
			}
			
			$other_icd10= $main_arr_val_c["enc_icd10"];
			$ccontent = getValHcfaUb($ccontent,$other_icd10,255,389,"left","A"); //01 (Line 1)
			
			$reserved = "";
			$ccontent = getValHcfaUb($ccontent,$reserved,390,400,"left","A"); //01 (Line 1)

			$max = max(array_keys($ccontent));
			$maxCnt = $max+1;
			for($k=0;$k<$maxCnt;$k++) {
				$val = $ccontent[$k];
				if(trim($val)=="") { $val = " "; }
				$newContent .= $val;
			}
		}
	}
	
	
	if(isset($main_arr_val['E'])){
		if($newContent) { $newContent.="\n"; } $e=0;
		foreach($main_arr_val['E'] as $main_arr_val_e){
			
			$objpriinsData = "";
			if($main_arr_val_e["primaryInsuranceCoId"]){
				$objpriinsData = getRecords_ins_data_con('insurance_data','pid',$main_arr_val_e["patient_id"],'provider',$main_arr_val_e["primaryInsuranceCoId"],'type','primary','ins_caseid',$main_arr_val_e["case_type_id"],$main_arr_val_e["date_of_service"]);
			}
			$objsecinsData='';
			if($main_arr_val_e["secondaryInsuranceCoId"]){
				$objsecinsData = getRecords_ins_data_con('insurance_data','pid',$main_arr_val_e["patient_id"],'provider',$main_arr_val_e["secondaryInsuranceCoId"],'type','secondary','ins_caseid',$main_arr_val_e["case_type_id"],$main_arr_val_e["date_of_service"]);
			}
			$objterinsData='';
			if($main_arr_val_e["tertiaryInsuranceCoId"]){
				$objterinsData = getRecords_ins_data_con('insurance_data','pid',$main_arr_val_e["patient_id"],'provider',$main_arr_val_e["tertiaryInsuranceCoId"],'type','tertiary','ins_caseid',$main_arr_val_e["case_type_id"],$main_arr_val_e["date_of_service"]);
			}

			$rel_arr=array("Spouse"=>'01',"Grandparent"=>'04',"Grand Child"=>'05',"Niece/Nephew"=>'07',"Foster Child"=>'10',"Ward of The Court"=>'15',"Step Child"=>'17',
					"Self"=>'18',"Son"=>'19',"Daughter"=>'19',"Employee"=>'20',"Handicapped Dependant"=>'22',"Sponsored Dependent"=>'23',
					"Minor Dependent Of a Dependent"=>'24',"Significant Other"=>'29',"Mother"=>'32',"Father"=>'33',"Donor-Dceased"=>'39',"Donor Live"=>'40',
					"Injured Plantiff"=>'41',"Child:No Fin Responsibility"=>'43',"Guardian"=>'09',"POA"=>'G8',"Other"=>'G8',
					"Unknown"=>'21',"Emancipated Minor"=>'36',"Life Partner"=>'53'
					);
			
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
			$econtent = array();
			if($e>0){ $newContent .= "\n"; } $e++;
			$record_type = "E";
			$econtent = getValHcfaUb($econtent,$record_type,1,1,"left","A"); //01 (Line 1)
			
			$NCHAFacilityID = "302322";
			$econtent = getValHcfaUb($econtent,$NCHAFacilityID,2,15,"left","A"); //01 (Line 1)
			
			$reserved = "";
			$econtent = getValHcfaUb($econtent,$reserved,16,16,"left","A"); //01 (Line 1)
			
			$patient_control_number = $main_arr_val_e['patient_id'];
			$econtent = getValHcfaUb($econtent,$patient_control_number,17,40,"left","A"); //01 (Line 1)
			
			$record_sequence_number = '01';
			$econtent = getValHcfaUb($econtent,$record_sequence_number,41,42,"left","A"); //01 (Line 1)
			
			$tmpHealthPlanIDPrimary = trim($insCompArr[$main_arr_val_e["primaryInsuranceCoId"]]["institutional_Code_id"]);
			if(!$tmpHealthPlanIDPrimary) {$tmpHealthPlanIDPrimary = trim($insCompArr[$main_arr_val_e["primaryInsuranceCoId"]]["Payer_id_pro"]);}
			if(strtolower($healthPlanIDPrimary)== "null") { $healthPlanIDPrimary = ""; }
			$healthPlanIDPrimary = $tmpHealthPlanIDPrimary;
			
			$tmpHealthPlanIDSecondary = trim($insCompArr[$main_arr_val_e["secondaryInsuranceCoId"]]["institutional_Code_id"]);
			if(!$tmpHealthPlanIDSecondary) {$tmpHealthPlanIDSecondary = trim($insCompArr[$main_arr_val_e["secondaryInsuranceCoId"]]["Payer_id_pro"]);}
			if(strtolower($tmpHealthPlanIDSecondary)== "null") { $tmpHealthPlanIDSecondary = ""; }
			$healthPlanIDSecondary = $tmpHealthPlanIDSecondary;
						
			$tmpHealthPlanIDTertiary = trim($insCompArr[$main_arr_val_e["tertiaryInsuranceCoId"]]["institutional_Code_id"]);
			if(!$tmpHealthPlanIDTertiary) {$tmpHealthPlanIDTertiary = trim($insCompArr[$main_arr_val_e["tertiaryInsuranceCoId"]]["Payer_id_pro"]);}
			if(strtolower($healthPlanIDTertiary)== "null") { $healthPlanIDTertiary = ""; }
			$healthPlanIDTertiary = $tmpHealthPlanIDTertiary;
			
			$health_plan_id = $healthPlanIDPrimary.$healthPlanIDSecondary.$healthPlanIDTertiary;
			$econtent = getValHcfaUb($econtent,$health_plan_id,43,87,"left","A"); //01 (Line 1)
			
			$pri_y = $sec_y = $ter_y = "";
			if(trim($insCompArr[$main_arr_val_e["primaryInsuranceCoId"]]['name'])) {
				$pri_y = "Y";
			}
			if(trim($insCompArr[$main_arr_val_e["secondaryInsuranceCoId"]]['name'])) {
				$sec_y = "Y";
			}
			if(trim($insCompArr[$main_arr_val_e["tertiaryInsuranceCoId"]]['name'])) {
				$ter_y = "Y";
			}
			
			$release_of_information = $pri_y.$sec_y.$ter_y;
			$econtent = getValHcfaUb($econtent,$release_of_information,88,90,"left","A"); //01 (Line 1)
			
			$assignment_of_benefits = $pri_y.$sec_y.$ter_y;
			$econtent = getValHcfaUb($econtent,$assignment_of_benefits,91,93,"left","A"); //01 (Line 1)
			
			$insPaidAmountPri = $insPaidAmountSec = $insPaidAmountTer = "0";
			$insPriSecTerCompArr = array("primaryInsuranceCoId","secondaryInsuranceCoId","tertiaryInsuranceCoId");
			foreach($insPriSecTerCompArr as $insPriSecTerCompName) {
				$insPaidAmountArr=array();
				$insPaidAmount="";

				$priSecTerInsPay=$main_arr_val_e[$insPriSecTerCompName];
				$piadRes = $payment_data[$main_arr_val_e['encounter_id']][$priSecTerInsPay];
				$insPaidAmountArr = array();
				$priInsPaidAmountByCLDidArr = array();
				for($r=0;$r<count($piadRes);$r++){
					$paidForProc = $piadRes[$r]['paidForProc'];	
					if($piadRes[$r]['paymentClaims'] == 'Negative Payment'){
						$paidForProc = '-'.$paidForProc;
					}										
					$insPaidAmountArr[] = $paidForProc;
				}
				$insPaidAmount = array_sum($insPaidAmountArr);
				$insPaidAmount =preg_replace("/[^A-Za-z0-9]/","",number_format($insPaidAmount,2)); 	
				if($insPriSecTerCompName=="primaryInsuranceCoId") {
					$insPaidAmountPri = $insPaidAmount;
				}
				if($insPriSecTerCompName=="secondaryInsuranceCoId") {
					$insPaidAmountSec = $insPaidAmount;
				}
				if($insPriSecTerCompName=="tertiaryInsuranceCoId") {
					$insPaidAmountTer = $insPaidAmount;
				}
			}
			$prior_payments = $insPaidAmountPri.$insPaidAmountSec.$insPaidAmountTer;
			$econtent = getValHcfaUb($econtent,$prior_payments,94,123,"left","A"); //01 (Line 1)
	
			$estimated_amount_due = "";
			$econtent = getValHcfaUb($econtent,$estimated_amount_due,124,153,"left","A"); //01 (Line 1)

			$otherBillingProviderPri = trim($insCompArr[$main_arr_val_e["primaryInsuranceCoId"]]['institutional_Code_id']);
			$otherBillingProviderSec = trim($insCompArr[$main_arr_val_e["secondaryInsuranceCoId"]]['institutional_Code_id']);
			$otherBillingProviderTer = trim($insCompArr[$main_arr_val_e["tertiaryInsuranceCoId"]]['institutional_Code_id']);
			
			$other_provider_id = $otherBillingProviderPri.$otherBillingProviderSec.$otherBillingProviderTer;
			$econtent = getValHcfaUb($econtent,$other_provider_id,154,198,"left","A"); //01 (Line 1)
			
			$insuredNamePrimary = trim(ucfirst(stripslashes($objpriinsData->subscriber_lname))." ".ucfirst(stripslashes($objpriinsData->subscriber_fname)));
			$insuredNameSecondary = trim(ucfirst(stripslashes($objsecinsData->subscriber_lname))." ".ucfirst(stripslashes($objsecinsData->subscriber_fname)));
			$insuredNameTertiary = trim(ucfirst(stripslashes($objterinsData->subscriber_lname))." ".ucfirst(stripslashes($objterinsData->subscriber_fname)));
			
			$insured_name = $insuredNamePrimary.$insuredNameSecondary.$insuredNameTertiary;
			$econtent = getValHcfaUb($econtent,$insured_name,199,273,"left","A"); //01 (Line 1)
			
			$patient_relationship= $ins_rel_final_pri.$ins_rel_final_sec.$ins_rel_final_ter;
			$econtent = getValHcfaUb($econtent,$patient_relationship,274,279,"left","A"); //01 (Line 1)
			
			$tmpInsuredUniqueIdentifierPrimary = trim(preg_replace("/[^A-Za-z0-9]/","",$objpriinsData->policy_number));
			if(!$tmpInsuredUniqueIdentifierPrimary) {
				$tmpInsuredUniqueIdentifierPrimary = "";
			}
			$insuredUniqueIdentifierPrimary = preg_replace("/[^A-Za-z0-9]/","",$tmpInsuredUniqueIdentifierPrimary);
			
			$tmpInsuredUniqueIdentifierSecondary = trim(preg_replace("/[^A-Za-z0-9]/","",$objsecinsData->policy_number));
			if(!$tmpInsuredUniqueIdentifierSecondary) {
				$tmpInsuredUniqueIdentifierSecondary = "";
			}
			$insuredUniqueIdentifierSecondary = $tmpInsuredUniqueIdentifierSecondary;
			
			$tmpInsuredUniqueIdentifierTertiary = trim(preg_replace("/[^A-Za-z0-9]/","",$objterinsData->policy_number));
			if(!$tmpInsuredUniqueIdentifierTertiary) {
				$tmpInsuredUniqueIdentifierTertiary = "";
			}
			$insuredUniqueIdentifierTertiary = $tmpInsuredUniqueIdentifierTertiary;
			
			$insured_unique_id= $insuredUniqueIdentifierPrimary.$insuredUniqueIdentifierSecondary.$insuredUniqueIdentifierTertiary;
			$econtent = getValHcfaUb($econtent,$insured_unique_id,280,339,"left","A"); //01 (Line 1)
			
			$reserved = "";
			$econtent = getValHcfaUb($econtent,$reserved,340,400,"left","A"); //01 (Line 1)

	
			$max = max(array_keys($econtent));
			$maxCnt = $max+1;
			for($k=0;$k<$maxCnt;$k++) {
				$val = $econtent[$k];
				if(trim($val)=="") { $val = " "; }
				$newContent .= $val;
			}
		}
	}

	if(isset($main_arr_val['G'])){
		if($newContent) { $newContent.="\n"; } $g=0;
		foreach($main_arr_val['G'] as $main_arr_val_g){
			$gcontent = array();
			if($g>0){ $newContent .= "\n"; } $g++;
			$record_type = "G";
			$gcontent = getValHcfaUb($gcontent,$record_type,1,1,"left","A"); //01 (Line 1)
			
			$NCHAFacilityID = "302322";
			$gcontent = getValHcfaUb($gcontent,$NCHAFacilityID,2,15,"left","A"); //01 (Line 1)
			
			$reserved = "";
			$gcontent = getValHcfaUb($gcontent,$reserved,16,16,"left","A"); //01 (Line 1)
			
			$patient_control_number = $main_arr_val_g['patient_id'];
			$gcontent = getValHcfaUb($gcontent,$patient_control_number,17,40,"left","A"); //01 (Line 1)
			
			if($g <= 9) { $g = '0'.$g; }
			$record_sequence_number = $g;
			$gcontent = getValHcfaUb($gcontent,$record_sequence_number,41,42,"left","A"); //01 (Line 1)
			
			$reserved = "";
			$gcontent = getValHcfaUb($gcontent,$reserved,43,158,"left","A"); //01 (Line 1)
			
			$patient_address_street2 = $main_arr_val_a['ptStreet2'];
			$gcontent = getValHcfaUb($gcontent,$patient_address_street2,159,198,"left","A"); //01 (Line 1)
			
			$billing_provider_npi = $posFacilityArr[$main_arr_val_g['posFacilityId']];
			$gcontent = getValHcfaUb($gcontent,$billing_provider_npi,199,213,"left","A"); //01 (Line 1)
	
			$reserved = "";
			$gcontent = getValHcfaUb($gcontent,$reserved,214,400,"left","A"); //01 (Line 1)

	
			$max = max(array_keys($gcontent));
			$maxCnt = $max+1;
			for($k=0;$k<$maxCnt;$k++) {
				$val = $gcontent[$k];
				if(trim($val)=="") { $val = " "; }
				$newContent .= $val;
			}
		}
	}	

	/* if(isset($main_arr_val['H'])){
		if($newContent) { $newContent.="\n"; } $h=0;
		foreach($main_arr_val['H'] as $main_arr_val_h){
			$hcontent = array();
			if($h>0){ $newContent .= "\n"; } $h++;
			$record_type = "H";
			$hcontent = getValHcfaUb($hcontent,$record_type,1,1,"left","A"); //01 (Line 1)
			
			$NCHAFacilityID = "302322";
			$hcontent = getValHcfaUb($hcontent,$NCHAFacilityID,2,15,"left","A"); //01 (Line 1)
			
			$reserved = "";
			$hcontent = getValHcfaUb($hcontent,$reserved,16,16,"left","A"); //01 (Line 1)
			
			$patient_control_number = $main_arr_val_h['patient_id'];
			$hcontent = getValHcfaUb($hcontent,$patient_control_number,17,40,"left","A"); //01 (Line 1)
			
			if($h <= 9) { $h = '0'.$h; }
			$record_sequence_number = $h;
			$hcontent = getValHcfaUb($hcontent,$record_sequence_number,41,42,"left","A"); //01 (Line 1)
			
			$service_date_from = $main_arr_val_h['date_of_service_format'];
			$hcontent = getValHcfaUb($hcontent,$service_date_from,43,90,"left","A"); //01 (Line 1)
			
			$service_date_to = $main_arr_val_h['date_of_service_format'];
			$hcontent = getValHcfaUb($hcontent,$service_date_to,91,138,"left","A"); //01 (Line 1)
			
			$placeOfServiceDb = $main_arr_val_h['place_of_service'];
			$place_of_service = $posArr[$placeOfServiceDb]['pos_code'];
			$hcontent = getValHcfaUb($hcontent,$place_of_service,139,150,"left","A"); //01 (Line 1)
	
			$type_of_service_id = $main_arr_val_h['type_of_service'];
			$type_of_service = $tos_data_arr[$type_of_service_id]['tos_prac_cod'];
			$hcontent = getValHcfaUb($hcontent,$type_of_service,151,162,"left","A"); //01 (Line 1)
			
			$proc_cod							= $main_arr_val_h['procCode'];
			$cpt4_code							= $cpt4CodeArr[$proc_cod];
			$modifier_id						= $main_arr_val_h['modifier_id1'];
			$modifier_id2						= $main_arr_val_h['modifier_id2'];
			$modifier_id3						= $main_arr_val_h['modifier_id3'];
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
			$cpt4_hcpcs_modifiers = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$cpt4_code.$modifier_code)));
			$hcontent = getValHcfaUb($hcontent,$cpt4_hcpcs_modifiers,163,240,"left","A"); //01 (Line 1)
			
			$charges_by_service=$main_arr_val_h['totalAmount'];
			$hcontent = getValHcfaUb($hcontent,$charges_by_service,241,300,"left","A"); //01 (Line 1)
			
			$units_days_of_service=$main_arr_val_h['units'];
			$hcontent = getValHcfaUb($hcontent,$units_days_of_service,301,342,"left","A"); //01 (Line 1)
	
			$reserved = "";
			$hcontent = getValHcfaUb($hcontent,$reserved,343,400,"left","A"); //01 (Line 1)

			$max = max(array_keys($hcontent));
			$maxCnt = $max+1;
			for($k=0;$k<$maxCnt;$k++) {
				$val = $hcontent[$k];
				if(trim($val)=="") { $val = " "; }
				$newContent .= $val;
			}
		}
	} */
}
	
$show_msg= "";
if(trim($newContent)) {
$newContent = $newContent.$newContent1;
if(!is_dir($updir."/state_report")){
//Create patient directory
mkdir($updir."/state_report", 0777,true);
}
$contentFileName = $updir."/state_report/newstate_".$_SESSION["authId"].".txt";
if(file_exists($contentFileName)) {
unlink($contentFileName);	
}
file_put_contents($contentFileName,$newContent);	


//$show_msg= "<div class="text-center alert alert-info">New State Report ".$reportName." have been exported successfully. <b> <a href='file_save_export.php?fn=".$contentFileName."' style='font-family:verdana;font-size:12px;font-weight:bold;color:#FF0000;' > Click here to download file</a></b></div>";

$show_msg= "<div class='text-center alert alert-info' style='font-family:verdana;font-size:12px;font-weight:bold;'> New State Report ".$reportName." have been exported successfully. <b> <a href='file_save_export.php?fn=".$contentFileName."' style='font-family:verdana;font-size:12px;font-weight:bold;color:#FF0000;' > Click here to download file</a></b></div>";
//$show_msg.="<b> <a href='file_save_export.php?fn=".$contentFileName."' style='font-family:verdana;font-size:12px;font-weight:bold;color:#FF0000;' > Click here to download file</a></b>";

}else {
$show_msg = '<div class="text-center alert alert-info">No Record Exists.</div>';
}
echo $show_msg;

$page_data = '';
if(count($errorMsgArr)>0) {
	$page_data .= '
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
		<tr>
			<td class="rptbx1" style="width:33%">OUTPATIENT ERROR DETAIL REPORT</td>
			<td class="rptbx2" style="text-align:center; width:33%">Date From : '.$Start_date.' to '.$End_date.'</td>
			<td class="rptbx3" style="text-align:center; width:33%">
				Created By '.$createdBy.' on '.$curDate.'
			</td>
		</tr>
	</table>	
	<div id="csvFileDataTable" style="height:400px; overflow:auto; overflow-x:hidden;">
		<table class="rpt_table rpt rpt_table-bordered">';
		foreach($errorMsgArr as $ptIdKey => $ptIdArr) {
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