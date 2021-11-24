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
ini_set('memory_limit', '2048m');
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
$submissionType		= trim($_REQUEST['submissionType']);
$report_type		= trim($_REQUEST['report_type']);
if(!$report_type) {
	//$report_type = "ub";
}

$from_date_format_short = date("mdy",strtotime($from_date));
$to_date_format_short 	= date("mdy",strtotime($to_date));
$cur_date_format_short 	= date("mdy");
$filler 				= "";
list($phy_name) = explode(",",$phy_name_str);
$batchName = "pa".$from_date_format_short.$to_date_format_short.$cur_date_format_short.$phy_name;
//echo '<br>Ins. Carrier '.$ins_comp_name_str.'<br>Physician '.$phy_name_str.'<br>Group '.$group_name_str.'<br>From Dt '.$from_date.'<br>To Dt '.$to_date.'<br>Report Type '.$report_type;
$andQry = "";
$andgrpQry = "";
if($group_name_str) {
	$andQry .= " and p.gro_id IN(".$group_name_str.") ";
	$andgrpQry .= " and gro_id IN(".$group_name_str.") ";
}
if($phy_name_str) {
	$andQry .= " and p.primaryProviderId IN(".$phy_name_str.")";
}
if($ins_comp_name_str) {
	$andQry .= " and p.primaryInsuranceCoId IN(".$ins_comp_name_str.")";
}
if($from_date && $from_date !="--" && $to_date && $to_date !="--") {
	$andQry .= " and (p.date_of_service between '".$from_date."' and '".$to_date."')";	
}

$newContent = "";

//START CODE OF INSURANCE COMPANY
$countryCodeArr = array("AF","AX","AL","DZ","AS","AD","AO","AI","AQ","AG","AR","AM","AW","AU","AT","AZ","BS","BH","BD","BB","BY","BE","BZ","BJ","BM","BT","BO","BA","BW",
				"BV","BR","IO","BN","BG","BF","BI","KH","CM","CA","CV","KY","CF","TD","CL","CN","CX","CC","CO","KM","CG","CD","CK","CR","CI","HR","CU","CY","CZ",
				"DK","DJ","DM","DO","EC","EG","SV","GQ","ER","EE","ET","FK","FO","FJ","FI","FR","GF","PF","TF","GA","GM","GE","DE","GH","GI","GR","GL","GD","GP",
				"GU","GT","GG","GN","GW","GY","HT","HM","VA","HN","HK","HU","IS","IN","ID","IR","IQ","IE","IM","IL","IT","JM","JP","JE","JO","KZ","KE","KI","KP",
				"KR","KW","KG","LA","LV","LB","LS","LR","LY","LI","LT","LU","MO","MK","MG","MW","MY","MV","ML","MT","MH","MQ","MR","MU","YT","MX","FM","MD","MC",
				"MN","ME","MS","MA","MZ","MM","NA","NR","NP","NL","AN","NC","NZ","NI","NE","NG","NU","NF","MP","NO","OM","PK","PW","PS","PA","PG","PY","PE","PH",
				"PN","PL","PT","PR","QA","RE","RO","RU","RW","BL","SH","KN","LC","MF","PM","VC","WS","SM","ST","SA","SN","RS","SC","SL","SG","SK","SI","SB","SO",
				"ZA","GS","ES","LK","SD","SR","SJ","SZ","SE","CH","SY","TW","TJ","TZ","TH","TL","TG","TK","TO","TT","TN","TR","TM","TC","TV","UG","UA","AE","GB",
				"US","UM","UY","UZ","VU","VE","VN","VG","VI","WF","EH","YE","ZM","ZW");

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

//START CODE OF REFERRING PHYSICIAN
$reffNpiArr = $reffRowArr = array();
$reffRes=imw_query("select physician_Reffer_id, NPI, FirstName, MiddleName, LastName from refferphysician ORDER BY physician_Reffer_id");
if(imw_num_rows($reffRes)>0) {
	while($reffRow=imw_fetch_array($reffRes)) {
		$reffId = $reffRow["physician_Reffer_id"];
		$reffNpiArr[$reffId] = $reffRow["NPI"];
		$reffRowArr[$reffId] = $reffRow;
	}
}
//END CODE OF REFERRING PHYSICIAN

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

//START CODE OF facility
$facilityRowArr = $hqFacilityRowArr = array();
$facilityRes=imw_query("select * from facility ORDER BY id");
if(imw_num_rows($facilityRes)>0) {
	while($facilityRow=imw_fetch_array($facilityRes)) {
		$facility_id = $facilityRow["pos_facility_id"];
		$facility_type = $facilityRow["facility_type"];
		$facilityRowArr[$facility_id] = $facilityRow;
		if($facility_type == "1") {
			$hqFacilityRowArr["HQ"] = $facilityRow;
		}
	}
}
//END CODE OF facility

//------------------------ Group Detail ------------------------//
$grpRes = imw_query("select * from groups_new WHERE 1=1 ".$andgrpQry);
while($grpRow=imw_fetch_array($grpRes)){			
	$groupDataRowArr[$grpRow['gro_id']]=$grpRow;
}		

//------------------------ Policies Detail ------------------------//
$pol_info=imw_query("select Address1,Telephone,Zip,City,State,phone_ext,zip_ext from copay_policies");
$pol_row=imw_fetch_array($pol_info);

//------------------------ Responsible Party Detail ------------------------//
$rep_qry = imw_query("select * from resp_party WHERE fname<>'' ");
while($rep_row=imw_fetch_array($rep_qry)){			
	$resp_data[$rep_row['patient_id']]=$rep_row;
}		

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

//$andQry .= " and p.charge_list_id IN(0,84258,84257)"; //temporary query
//inc.Insurance_payment = 'HCFA1500' AND inc.name != 'SELF PAY'
	$qry = "SELECT p.*, if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%m%d%Y'),'') AS admit_date_format, if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%m-%d-%Y'),'') AS admit_date_format_new, if(p.admit_date != '0000-00-00 00:00:00', DATE_FORMAT(p.admit_date, '%m%d%y'),'') AS admit_date_format_short, if(p.disch_date != '0000-00-00 00:00:00', DATE_FORMAT(p.disch_date, '%m%d%Y'),'') AS disch_date_format,
		if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m%d%Y'),'') as date_of_service_format, if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m-%d-%Y'),'') as date_of_service_format_new, if(p.date_of_service != '0000-00-00', DATE_FORMAT(p.date_of_service, '%m%d%y'),'') as date_of_service_format_short,
		gn.group_Federal_EIN, gn.group_NPI, gn.group_institution, gn.name as groupName, 
		pd.city AS ptCity, pd.state AS ptState, pd.postal_code AS ptPostalCode, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m%d%Y'),'') AS ptDobFormat, if(pd.DOB != '0000-00-00', DATE_FORMAT(pd.DOB, '%m-%d-%Y'),'') AS ptDobFormatNew,
		pd.sex AS ptSex, pd.ss AS patientSSN, pd.race, pd.ethnicity, pd.street AS ptStreet, pd.street2 AS ptStreet2,fname AS ptFname, lname AS ptLname, mname AS ptMname, TRIM(CONCAT(pd.lname,', ',pd.fname,' ',pd.mname)) AS ptName, 
		pd.default_facility AS ptDefaultFacility, pd.country_code AS ptCountryCode, pd.status AS maritalStatus, pd.occupation AS ptOccupation, 
		pd.primary_care_id AS ptPrimaryCareId,
		sb.ascId, sb.primary_provider_id_for_reports AS primary_provider_id_for_reports_sb
		FROM patient_charge_list p
		INNER JOIN patient_data pd ON(pd.pid = p.patient_id)
		INNER JOIN insurance_companies inc ON (p.primaryInsuranceCoId = inc.id   ".$andInstitueTypeQry.")
		INNER JOIN submited_record sr ON(sr.encounter_id = p.encounter_id)
		LEFT JOIN groups_new gn ON(gn.gro_id=p.gro_id)
		INNER JOIN superbill sb ON(sb.encounterId = p.encounter_id)
		WHERE p.charge_list_id !='0' and p.del_status='0' and sb.ascId>0 and p.enc_accept_assignment!='2' ".$andQry."  
		GROUP BY p.charge_list_id
		ORDER BY pd.pid ";

$res = imw_query($qry) or die($qry.imw_error());
$errorMsgArr = array();
$tmpTotalNumberofPatientArr = array();
$tmpTotalNumberofClaims = 0;
$tmpTotalChargesSubmitted = 0;
if(imw_num_rows($res)>0) {
	$c=0;
	while($row = imw_fetch_assoc($res)) {
		$charg_id = $row["charge_list_id"];
		//and pcl.proc_selfpay!='1'
		$qrySub = "select pcl.*, cft.units as admin_cpt_unit,if(pcl.onset_date != '0000-00-00', DATE_FORMAT(pcl.onset_date, '%m%d%Y'),'') AS onset_date_format,
						cft.cpt_comments as admin_ndc,cft.cpt_desc as admin_cpt_desc,cft.cpt4_code,pcl.procCode,pcl.modifier_id1,pcl.modifier_id2,pcl.modifier_id3 
						from patient_charge_list_details pcl 
						join cpt_fee_tbl cft on cft.cpt_fee_id = pcl.procCode
						where pcl.del_status='0' and pcl.charge_list_id='".$charg_id."'
						and cft.not_covered = '0' and pcl.posted_status='1' 
						and pcl.differ_insurance_bill != 'true'
						order by pcl.display_order,pcl.charge_list_detail_id LIMIT 0,23";
		$imw_info4=imw_query($qrySub) or die($qrySub.imw_error());
		$num_charge_details=imw_num_rows($imw_info4);
		if($num_charge_details > 0) {
			$tmpTotalNumberofClaims++;
			$tmpTotalNumberofPatientArr[$row["patient_id"]] = $row["patient_id"];
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
	
			$rel_arr=array("Spouse"=>'01',"Grandparent"=>'04',"Grand Child"=>'05',"Niece/Nephew"=>'07',"Foster Child"=>'10',"Ward of The Court"=>'15',"Step Child"=>'17',
							"Self"=>'18',"Son"=>'19',"Daughter"=>'19',"Employee"=>20,"Handicapped Dependant"=>'22',"Sponsored Dependent"=>'23',
							"Minor Dependent Of a Dependent"=>'24',"Significant Other"=>'29',"Mother"=>'32',"Father"=>'33',"Donor-Dceased"=>'39',"Donor Live"=>'40',
							"Injured Plantiff"=>'41',"Child:No Fin Responsibility"=>'43',"Guardian"=>'09',"POA"=>'G8',"Other"=>'G8',
							"Unknown"=>21,"Emancipated Minor"=>'36',"Life Partner"=>'53'
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
			
			//--- Start get all charge list details ----	
			$content = array();
			
			$cpt4_code_arr=array();
			$notes_arr=array();
			$count_pro=0;
			$tot_amt=0;
			$totalNonCoveredCharges=0;
			$dignosis = array();
			$proc_code_essi_arr = array();
			$top_name_var=1;
			$recCnt = 0;
			$revStartPos = $hippsRatesStartPos = $serviceStartPos = $unitsStartPos = $totalChargesByRevStartPos = $hcpcsCodesStartPos = $nonCoveredChargesByRevStartPos = $reservedNUBC49StartPos = 0;
			$chargeListIdArr = $valueCodeArr = $valueAmtArr = array();
			$revenueChargeExists =false;
			$sub_total_proc_charges = 0;
			while($imw_row4=imw_fetch_array($imw_info4)){
				
				$recCnt++;
				$chld_arr[$imw_row4['charge_list_id']][$imw_row4['charge_list_detail_id']]=$imw_row4;
				$rev_cod							= $imw_row4['rev_code'];
				$revenueCodes						= $rCodeArr[$rev_cod];
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
				
				if($recCnt < 23) {
					$revStartPos 									= $revStartPos ? ($revStartPos+53) : 969;
					$revEndPos 										= ($revStartPos+3);
					$content 										= getValHcfaUb($content,$revenueCodes,$revStartPos,$revEndPos,"left","A"); //42a to 42v
					//echo '<br>'.$revStartPos.' - '.$revEndPos.' - '.$recCnt.$row['encounter_id'].' - '.$revenueCodes;

					$hcpcsCodes 									= trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$cpt4_code.$modifier_code)));
					$hcpcsCodesStartPos 							= $hcpcsCodesStartPos ? ($hcpcsCodesStartPos+53) : 973;
					$hcpcsCodesEndPos 								= ($hcpcsCodesStartPos+13);
					$content 										= getValHcfaUb($content,$hcpcsCodes,$hcpcsCodesStartPos,$hcpcsCodesEndPos,"left","A"); //44a to 44v
				
					$serviceDate 									= $row["date_of_service_format_short"];
					$serviceStartPos 								= $serviceStartPos ? ($serviceStartPos+53) : 987;
					$serviceEndPos 									= ($serviceStartPos+5);
					$content 										= getValHcfaUb($content,$serviceDate,$serviceStartPos,$serviceEndPos,"right","N"); //45a-45v

					$unitOfService									= preg_replace("/[^A-Za-z0-9]/","",$imw_row4['units']);
					$unitsStartPos 									= $unitsStartPos ? ($unitsStartPos+53) : 993;
					$unitsEndPos 									= ($unitsStartPos+6);
					$content 										= getValHcfaUb($content,$unitOfService,$unitsStartPos,$unitsEndPos,"right","N"); //46a-46v

					$totalChargesByRevenueCode						= preg_replace("/[^A-Za-z0-9]/","",$imw_row4['totalAmount']);
					$totalChargesByRevStartPos 						= $totalChargesByRevStartPos ? ($totalChargesByRevStartPos+53) : 1000;
					$totalChargesByRevEndPos 						= ($totalChargesByRevStartPos+9);
					$content 										= getValHcfaUb($content,$totalChargesByRevenueCode,$totalChargesByRevStartPos,$totalChargesByRevEndPos,"right","N"); //47a-47v

					$nonCoveredChargesByRevenueCode					= "";
					$nonCoveredChargesByRevStartPos 				= $nonCoveredChargesByRevStartPos ? ($nonCoveredChargesByRevStartPos+53) : 1010;
					$nonCoveredChargesByRevEndPos 					= ($nonCoveredChargesByRevStartPos+9);
					$content 										= getValHcfaUb($content,$nonCoveredChargesByRevenueCode,$nonCoveredChargesByRevStartPos,$nonCoveredChargesByRevEndPos,"right","N"); //48a-48v
				
				
					
					if(trim($totalChargesByRevenueCode)) {
						$revenueChargeExists = true;
					}
		
					if($imw_row4['totalAmount']) {
						$tot_amt+=$imw_row4['totalAmount'];
					}
					if($nonCoveredChargesByRevenueCode) {
						$totalNonCoveredCharges+= (int)$nonCoveredChargesByRevenueCode;
					}
					
					$sub_total_proc_charges += str_ireplace(',','',$imw_row4['procCharges'] * $imw_row4['units']);					
				
				}
			
				$reservedForAssignmentByTheNUBCField49aTo49w 	= "";
				$reservedNUBC49StartPos 						= $reservedNUBC49StartPos ? ($reservedNUBC49StartPos+53) : 1020;
				$reservedNUBC49EndPos 							= ($reservedNUBC49StartPos+1);
				$content 										= getValHcfaUb($content,$reservedForAssignmentByTheNUBCField49aTo49w,$reservedNUBC49StartPos,$reservedNUBC49EndPos,"left","A"); //49a-49w
				
				if($imw_row4['rev_rate']!=""){
					$rev_rate=explode('/',$imw_row4['rev_rate']);
					$valueCodeArr[$row["patient_id"]][$row["charge_list_id"]][] = trim($rev_rate[0]);
					$valueAmtArr[$row["patient_id"]][$row["charge_list_id"]][] 	= preg_replace("/[^A-Za-z0-9]/","",number_format($rev_rate[1],2)); 
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

			//START PATIENT INFORMATION
			$gro_id = $row['gro_id'];
			$billingProviderName = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$groupDataRowArr[$gro_id]['name'])));
			$content = getValHcfaUb($content,$billingProviderName,1,25,"left","A"); //01 (Line 1)
			
			$billingProviderStreetAddress = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$groupDataRowArr[$gro_id]['group_Address1'])));
			$content = getValHcfaUb($content,$billingProviderStreetAddress,26,50,"left","A"); //01 (Line 2)

			$billingProviderCity = stripslashes($groupDataRowArr[$gro_id]['group_City']);
			$content = getValHcfaUb($content,$billingProviderCity,51,62,"left","A"); //1c1

			$billingProviderSate = stripslashes($groupDataRowArr[$gro_id]['group_State']);
			$content = getValHcfaUb($content,$billingProviderSate,63,64,"left","A"); //1c2

			$billingProviderZipCode = stripslashes($groupDataRowArr[$gro_id]['group_Zip']);
			$content = getValHcfaUb($content,$billingProviderZipCode,65,73,"left","A"); //1c3

			$billingProviderTelephone = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$groupDataRowArr[$gro_id]['group_Telephone']))); 
			$content = getValHcfaUb($content,$billingProviderTelephone,74,83,"left","A"); //1d1

			$billingProviderFax = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$groupDataRowArr[$gro_id]['group_Fax']))); 
			$content = getValHcfaUb($content,$billingProviderFax,84,93,"left","A"); //1d2

			$billingProviderCountryCode = "US";
			$content = getValHcfaUb($content,$billingProviderCountryCode,94,95,"left","A"); //1d3

			
			$payToName = $billingProviderName;
			$content = getValHcfaUb($content,$payToName,96,120,"left","A"); //02 (Line 1)

			$payToAddress = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$pol_row['Address1'])));
			$content = getValHcfaUb($content,$payToAddress,121,145,"left","A"); //02 (Line 2)

			$payToCity = $pol_row['City'];
			$content = getValHcfaUb($content,$payToCity,146,161,"left","A"); //2c1 (Line 3)

			$payToState = $pol_row['State'];
			$content = getValHcfaUb($content,$payToState,162,163,"left","A"); //2c2 (Line 3)

			$payToZipCode = $pol_row['Zip'];
			$content = getValHcfaUb($content,$payToZipCode,164,168,"left","A"); //2c3 (Line 3)

			$reservedForAssignmentByTheNUBCField2d = "";
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField2d,169,193,"left","A"); //2d (Line 4)

			$patientControlNumber = $row["patient_id"];
			$content = getValHcfaUb($content,$patientControlNumber,194,217,"left","A"); //03a

			$medicalHealthRecordNumber = $row["encounter_id"]; 
			$content = getValHcfaUb($content,$medicalHealthRecordNumber,218,241,"left","A"); //03b

			$typeOfBill = "831"; //
			$content = getValHcfaUb($content,$typeOfBill,242,245,"right","N"); //04

			$federalTaxSubIdNumber = ""; 
			$content = getValHcfaUb($content,$federalTaxSubIdNumber,246,249,"left","A"); //5a

			$federalTaxNumber = preg_replace("/[^A-Za-z0-9]/","",$row["group_Federal_EIN"]);
			if($federalTaxNumber && substr($federalTaxNumber, 2, 1) != '-') { 
				$federalTaxNumber = substr($federalTaxNumber, 0, 2).'-'.substr($federalTaxNumber, 2);   
			}
			$content = getValHcfaUb($content,$federalTaxNumber,250,259,"left","A"); //5b
			
			$statementCoversPeriodFrom = $row["date_of_service_format_short"];
			$content = getValHcfaUb($content,$statementCoversPeriodFrom,260,265,"right","N"); //6a (from portion)
			
			$statementCoversPeriodThrough = $row["date_of_service_format_short"];
			$content = getValHcfaUb($content,$statementCoversPeriodThrough,266,271,"right","N"); //6b (from portion)

			$reservedForAssignmentByTheNUBCField7 = "";
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField7,272,286,"left","A"); //7

			$patientIdentifier = "";
			$content = getValHcfaUb($content,$patientIdentifier,287,305,"left","A"); //7

			$patientName = trim(stripslashes($row["ptLname"]." ".$row["ptFname"]." ".ucfirst(substr($row["ptMname"],0,1))));
			$content = getValHcfaUb($content,$patientName,306,334,"left","A"); //8b

			$PatientAddress = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$row["ptStreet"])));
			$content = getValHcfaUb($content,$PatientAddress,335,374,"left","A"); //9a

			$PatientCity = stripslashes($row["ptCity"]);
			$content = getValHcfaUb($content,$PatientCity,375,404,"left","A"); //9b

			$PatientState = stripslashes($row["ptState"]);
			$content = getValHcfaUb($content,$PatientState,405,406,"left","A"); //9c

			$patientZipCode = stripslashes($row["ptPostalCode"]);
			$content = getValHcfaUb($content,$patientZipCode,407,415,"left","A"); //9d

			$patientCountry = substr(stripslashes($row["ptCountryCode"]),0,2);
			$content = getValHcfaUb($content,$patientCountry,416,417,"left","A"); //9e
			
			if(trim($patientCountry) && !in_array($patientCountry,$countryCodeArr)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Patient Error Code: 009e - 155 Patient Country Invalid. The Patient Country does not match any values listed in Appendix D";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}

			$patientBirthDate = $row["ptDobFormat"];
			$content = getValHcfaUb($content,$patientBirthDate,418,425,"right","N"); //10
			if(!trim($patientBirthDate)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Error Code: 0010 - 101 The patient’s Birth Date is blank or not a valid date";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}

			$patientSex = strtoupper(substr($row['ptSex'],0,1));
			if(!$patientSex) { $patientSex = "U"; }
			$content = getValHcfaUb($content,$patientSex,426,426,"left","A"); //11
			if($patientSex!="M" && $patientSex!="F" && $patientSex!="U") {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Error Code: 0011 - Sex Code Invalid. The entry is not M, F, or U";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			
			$admissionStartOfCareDateTmp = $row["admit_date_format_short"];	
			if($row["acc_anes_unit"]<=0 && $row["admit_date_format_short"]=="") {
				$admissionStartOfCareDateTmp = date("mdy",strtotime($row["date_of_service"]));
			}
			$admissionStartOfCareDate = $admissionStartOfCareDateTmp;
			$content = getValHcfaUb($content,$admissionStartOfCareDate,427,432,"right","N"); //12

			$admissionHourTmp = "";
			$admitDateExp = explode(" ",$row["admit_date"]);
			if(trim($admitDateExp[1]) != "00:00:00"){
				$admissionHourTmp = substr(trim($admitDateExp[1]),0,5);
			}
			$admissionHour = $admissionHourTmp;
			$content = getValHcfaUb($content,$admissionHour,433,434,"left","A"); //13

			$priorityTypeOfVisit = "3";
			$content = getValHcfaUb($content,$priorityTypeOfVisit,435,435,"left","A"); //14

			$pointOfOriginForAdmissionOrVisit = "1";
			$content = getValHcfaUb($content,$pointOfOriginForAdmissionOrVisit,436,436,"left","A"); //15

			$dischargeHour = "";
			$content = getValHcfaUb($content,$dischargeHour,437,438,"left","A"); //16

			$patientDischargeStatus = "01";
			$content = getValHcfaUb($content,$patientDischargeStatus,439,440,"right","N"); //17

			$conditionCode1 = ""; //18
			$content = getValHcfaUb($content,$conditionCode1,441,442,"left","A");
			$conditionCode2 = ""; //19
			$content = getValHcfaUb($content,$conditionCode2,443,444,"left","A");
			$conditionCode3 = ""; //20
			$content = getValHcfaUb($content,$conditionCode3,445,446,"left","A");
			$conditionCode4 = ""; //21
			$content = getValHcfaUb($content,$conditionCode4,447,448,"left","A");
			$conditionCode5 = ""; //22
			$content = getValHcfaUb($content,$conditionCode5,449,450,"left","A");
			$conditionCode6 = ""; //23
			$content = getValHcfaUb($content,$conditionCode6,451,452,"left","A");
			$conditionCode7 = ""; //24
			$content = getValHcfaUb($content,$conditionCode7,453,454,"left","A");
			$conditionCode8 = ""; //25
			$content = getValHcfaUb($content,$conditionCode8,455,456,"left","A");
			$conditionCode9 = ""; //26
			$content = getValHcfaUb($content,$conditionCode9,457,458,"left","A");
			$conditionCode10 = ""; //27
			$content = getValHcfaUb($content,$conditionCode10,459,460,"left","A");
			$conditionCode11 = ""; //28
			$content = getValHcfaUb($content,$conditionCode11,461,462,"left","A");
			
			$accidentState = ""; //29
			$content = getValHcfaUb($content,$accidentState,463,464,"left","A");

			$reservedForAssignmentByTheNUBCField30 = ""; //30
			$content = getValHcfaUb($content,$accidentState,465,488,"left","A");
			
			$occurrenceCode1 = ""; //31a1
			$content = getValHcfaUb($content,$occurrenceCode1,489,490,"left","A");
			$occurrenceCode2 = ""; //32a1
			$content = getValHcfaUb($content,$occurrenceCode2,497,498,"left","A");
			$occurrenceCode3 = ""; //33a1
			$content = getValHcfaUb($content,$occurrenceCode3,505,506,"left","A");
			$occurrenceCode4 = ""; //34a1
			$content = getValHcfaUb($content,$occurrenceCode4,513,514,"left","A");
			$occurrenceCode5 = ""; //31b1
			$content = getValHcfaUb($content,$occurrenceCode5,521,522,"left","A");
			$occurrenceCode6 = ""; //32b1
			$content = getValHcfaUb($content,$occurrenceCode6,529,530,"left","A");
			$occurrenceCode7 = ""; //33b1
			$content = getValHcfaUb($content,$occurrenceCode7,537,538,"left","A");
			$occurrenceCode8 = ""; //34b1
			$content = getValHcfaUb($content,$occurrenceCode8,545,546,"left","A");

			$occurrenceDate1 = ""; //31a2
			$content = getValHcfaUb($content,$occurrenceDate1,491,496,"right","N");
			$occurrenceDate2 = ""; //32a2
			$content = getValHcfaUb($content,$occurrenceDate2,499,504,"right","N");
			$occurrenceDate3 = ""; //33a2
			$content = getValHcfaUb($content,$occurrenceDate3,507,512,"right","N");
			$occurrenceDate4 = ""; //34a2
			$content = getValHcfaUb($content,$occurrenceDate4,515,520,"right","N");
			$occurrenceDate5 = ""; //31b2
			$content = getValHcfaUb($content,$occurrenceDate5,523,528,"right","N");
			$occurrenceDate6 = ""; //32b2
			$content = getValHcfaUb($content,$occurrenceDate6,531,536,"right","N");
			$occurrenceDate7 = ""; //33b2
			$content = getValHcfaUb($content,$occurrenceDate7,539,544,"right","N");
			$occurrenceDate8 = ""; //34b2
			$content = getValHcfaUb($content,$occurrenceDate8,547,552,"right","N");

			$occurrenceSpanCode1 = ""; //35a1
			$content = getValHcfaUb($content,$occurrenceSpanCode1,553,554,"left","A");
			$occurrenceSpanCode2 = ""; //36a1
			$content = getValHcfaUb($content,$occurrenceSpanCode2,567,568,"left","A");
			$occurrenceSpanCode3 = ""; //35b1
			$content = getValHcfaUb($content,$occurrenceSpanCode3,581,582,"left","A");
			$occurrenceSpanCode4 = ""; //36b1
			$content = getValHcfaUb($content,$occurrenceSpanCode4,595,596,"left","A");
			$occurrenceSpanDatesFrom1 = ""; //35a2
			$content = getValHcfaUb($content,$occurrenceSpanDatesFrom1,555,560,"right","N");
			$occurrenceSpanDatesFrom2 = ""; //36a2
			$content = getValHcfaUb($content,$occurrenceSpanDatesFrom2,569,574,"right","N");
			$occurrenceSpanDatesFrom3 = ""; //35b2
			$content = getValHcfaUb($content,$occurrenceSpanDatesFrom3,583,588,"right","N");
			$occurrenceSpanDatesFrom4 = ""; //36b2
			$content = getValHcfaUb($content,$occurrenceSpanDatesFrom4,597,602,"right","N");
			$occurrenceSpanDatesThrough1 = ""; //35a3
			$content = getValHcfaUb($content,$occurrenceSpanDatesThrough1,561,566,"right","N");
			$occurrenceSpanDatesThrough2 = ""; //36a3
			$content = getValHcfaUb($content,$occurrenceSpanDatesThrough2,575,580,"right","N");
			$occurrenceSpanDatesThrough3 = ""; //35b3
			$content = getValHcfaUb($content,$occurrenceSpanDatesThrough3,589,594,"right","N");
			$occurrenceSpanDatesThrough4 = ""; //36b3
			$content = getValHcfaUb($content,$occurrenceSpanDatesThrough4,603,608,"right","N");
			
			$reservedForAssignmentByTheNUBCField37a = ""; //37a
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField37a,609,616,"left","A");
			$reservedForAssignmentByTheNUBCField37b = ""; //37b
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField37b,617,624,"left","A");
			
			$respRow = $resp_data[$row["patient_id"]];
			$responsiblePartyNameTmp = $responsiblePartyAddressTmp = "";
			if(trim($respRow["fname"])) {
				$responsiblePartyNameTmp 	 = trim($respRow["lname"]." ".$respRow["fname"]." ".$respRow["mname"]);
				$responsiblePartyAddressTmp .= trim($respRow["address"]);
				$responsiblePartyAddressTmp .= " ".trim($respRow["address2"]);
				$responsiblePartyAddressTmp  = trim($responsiblePartyAddressTmp);
				$responsiblePartyAddressTmp .= " ".trim($respRow["city"]);
				$responsiblePartyAddressTmp  = trim($responsiblePartyAddressTmp);
				$responsiblePartyAddressTmp .= " ".trim($respRow["state"]);
				$responsiblePartyAddressTmp  = trim($responsiblePartyAddressTmp);
				$responsiblePartyAddressTmp .= " ".trim($respRow["zip"]);
				$responsiblePartyAddressTmp  = trim($responsiblePartyAddressTmp);
			}else {
				$responsiblePartyNameTmp 	 = trim($row["ptLname"]." ".$row["ptFname"]." ".$row["ptMname"]);	
				$responsiblePartyAddressTmp .= trim($row["ptStreet"]);
				$responsiblePartyAddressTmp .= " ".trim($row["ptStreet2"]);
				$responsiblePartyAddressTmp  = trim($responsiblePartyAddressTmp);
				$responsiblePartyAddressTmp .= " ".trim($row["ptCity"]);
				$responsiblePartyAddressTmp  = trim($responsiblePartyAddressTmp);
				$responsiblePartyAddressTmp .= " ".trim($row["ptState"]);
				$responsiblePartyAddressTmp  = trim($responsiblePartyAddressTmp);
				$responsiblePartyAddressTmp .= " ".trim($row["ptPostalCode"]);
				$responsiblePartyAddressTmp  = trim($responsiblePartyAddressTmp);

			}
			$responsiblePartyName = stripslashes($responsiblePartyNameTmp); //38a
			$content = getValHcfaUb($content,$responsiblePartyName,625,664,"left","A");
			
			$responsiblePartyAddressTmp = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$responsiblePartyAddressTmp)));
			$responsiblePartyAddress = $responsiblePartyAddressTmp; //38b
			$content = getValHcfaUb($content,$responsiblePartyAddress,665,824,"left","A");
			
			$valueCodeNewArr = $valueAmtNewArr = array();
			if(count($valueCodeArr)>0) {
				$valueCodeNewArr 	= $valueCodeArr[$row["patient_id"]][$row["charge_list_id"]];
				$valueAmtNewArr 	= $valueAmtArr[$row["patient_id"]][$row["charge_list_id"]];
			}
			
			$valueCodes1 = $valueCodeNewArr[0]; //39a1
			$content = getValHcfaUb($content,$valueCodes1,825,826,"left","A");
			$valueCodes2 = $valueCodeNewArr[1]; //40a1
			$content = getValHcfaUb($content,$valueCodes2,837,838,"left","A");
			$valueCodes3 = $valueCodeNewArr[2]; //41a1
			$content = getValHcfaUb($content,$valueCodes3,849,850,"left","A");
			$valueCodes4 = $valueCodeNewArr[3]; //39b1
			$content = getValHcfaUb($content,$valueCodes4,861,862,"left","A");
			$valueCodes5 = $valueCodeNewArr[4]; //40b1
			$content = getValHcfaUb($content,$valueCodes5,873,874,"left","A");
			$valueCodes6 = $valueCodeNewArr[5]; //41b1
			$content = getValHcfaUb($content,$valueCodes6,885,886,"left","A");
			$valueCodes7 = $valueCodeNewArr[6]; //39c1
			$content = getValHcfaUb($content,$valueCodes7,897,898,"left","A");
			$valueCodes8 = $valueCodeNewArr[7]; //40c1
			$content = getValHcfaUb($content,$valueCodes8,909,910,"left","A");
			$valueCodes9 = $valueCodeNewArr[8]; //41c1
			$content = getValHcfaUb($content,$valueCodes9,921,922,"left","A");
			$valueCodes10 = $valueCodeNewArr[9]; //39d1
			$content = getValHcfaUb($content,$valueCodes9,933,934,"left","A");
			$valueCodes11 = $valueCodeNewArr[10]; //40d1
			$content = getValHcfaUb($content,$valueCodes10,945,946,"left","A");
			$valueCodes12 = $valueCodeNewArr[11]; //41d1
			$content = getValHcfaUb($content,$valueCodes11,957,958,"left","A");

			$valueAmts1 = $valueAmtNewArr[0]; //39a2
			$content = getValHcfaUb($content,$valueAmts1,827,836,"right","N");
			$valueAmts2 = $valueAmtNewArr[1]; //40a2
			$content = getValHcfaUb($content,$valueAmts2,839,848,"right","N");
			$valueAmts3 = $valueAmtNewArr[2]; //41a2
			$content = getValHcfaUb($content,$valueAmts3,851,860,"right","N");
			$valueAmts4 = $valueAmtNewArr[3]; //39b2
			$content = getValHcfaUb($content,$valueAmts4,863,872,"right","N");
			$valueAmts5 = $valueAmtNewArr[4]; //40b2
			$content = getValHcfaUb($content,$valueAmts5,875,884,"right","N");
			$valueAmts6 = $valueAmtNewArr[5]; //41b2
			$content = getValHcfaUb($content,$valueAmts6,887,896,"right","N");
			$valueAmts7 = $valueAmtNewArr[6]; //39c2
			$content = getValHcfaUb($content,$valueAmts7,899,908,"right","N");
			$valueAmts8 = $valueAmtNewArr[7]; //40c2
			$content = getValHcfaUb($content,$valueAmts8,911,920,"right","N");
			$valueAmts9 = $valueAmtNewArr[8]; //41c2
			$content = getValHcfaUb($content,$valueAmts9,923,932,"right","N");
			$valueAmts10 = $valueAmtNewArr[9]; //39d2
			$content = getValHcfaUb($content,$valueAmts9,935,944,"right","N");
			$valueAmts11 = $valueAmtNewArr[10]; //40d2
			$content = getValHcfaUb($content,$valueAmts10,947,956,"right","N");
			$valueAmts12 = $valueAmtNewArr[11]; //41d2
			$content = getValHcfaUb($content,$valueAmts11,959,968,"right","N");
			
			//Revenue Code from 42a to 42v (length 969 to 2085 see above in loop)
			$revenueCode23 = "0001"; //42w
			$content = getValHcfaUb($content,$revenueCode23,2135,2138,"right","N");
			
			$pageCount = "1"; //43w1
			$content = getValHcfaUb($content,$pageCount,2139,2141,"right","N");

			$totalNumberOfPages = "1"; //43w2
			$content = getValHcfaUb($content,$totalNumberOfPages,2142,2144,"right","N");

			//HCPCS Codes from 44a-44v (length 973 to 2099 see above in loop)
			//Service Date from 45a-45v (length 987 to 2105 see above in loop)
			//Service Units from 46a-46v (length 993 to 2112 see above in loop)
			//Total Charges from 47a-47v (length 1000 to 2122 see above in loop)
			
			
			if(trim($tot_amt)) { 
				$tot_amt =preg_replace("/[^A-Za-z0-9]/","",number_format($tot_amt,2)); 
			}else { 
				$tot_amt = "0";
			}
			$totalCharges = $tot_amt; //REQUIRED
			$tmpTotalChargesSubmitted += $totalCharges;
			if(!trim($totalCharges)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Sum of individual charges do not add up to total charges";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$totalCharges,2145,2154,"right","N"); //47w
			
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
			
			//Non-covered Charges By Revenue Code from 48a-48v (length 1010 to 2132 see above in loop)
			
			$content = getValHcfaUb($content,$totalNonCoveredCharges,2155,2164,"right","N"); //48w
			
			
			$tmpPayerTypePrimary = trim($insCompArr[$row["primaryInsuranceCoId"]]['ins_state_payer_code']);
			if($row["primaryInsuranceCoId"]=='0') {
				$tmpPayerTypePrimary = "P";
			}
			$payerTypePrimary = $tmpPayerTypePrimary; //REQUIRED 50A NOT FOUND NEED DISCUSSION
			if(!trim($payerTypePrimary)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Primary Error Code: 050a-1 - 134 Payer Type Invalid";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$payerTypePrimary,2167,2168,"left","A"); //50a-Payer Type

			$payerNamePrimary = trim(stripslashes($insCompArr[$row["primaryInsuranceCoId"]]['name']));
			$content = getValHcfaUb($content,$payerNamePrimary,2169,2191,"left","A"); //50a-Payer Name

			$tmpPayerTypeSecondary 	= trim($insCompArr[$row["secondaryInsuranceCoId"]]['ins_state_payer_code']);
			$payerTypeSecondary 	= $tmpPayerTypeSecondary; 
			$content = getValHcfaUb($content,$payerTypeSecondary,2403,2404,"left","A"); //50b-Payer Type

			$payerNameSecondary = trim(stripslashes($insCompArr[$row["secondaryInsuranceCoId"]]['name'])); //REQUIRED
			$content = getValHcfaUb($content,$payerNameSecondary,2405,2427,"left","A"); //50b-Payer Name

			$tmpPayerTypeTertiary 	= trim($insCompArr[$row["tertiaryInsuranceCoId"]]['ins_state_payer_code']);
			$payerTypeTertiary 		= $tmpPayerTypeTertiary; 
			$content = getValHcfaUb($content,$payerTypeTertiary,2639,2640,"left","A"); //50c-Payer Type

			$payerNameTertiary = trim(stripslashes($insCompArr[$row["tertiaryInsuranceCoId"]]['name']));
			$content = getValHcfaUb($content,$payerNameTertiary,2641,2663,"left","A"); //50c-Payer Name


			$tmpHealthPlanIDPrimary = trim($insCompArr[$row["primaryInsuranceCoId"]]["institutional_Code_id"]);
			if(!$tmpHealthPlanIDPrimary) {$tmpHealthPlanIDPrimary = trim($insCompArr[$row["primaryInsuranceCoId"]]["Payer_id_pro"]);}
			//if(!$tmpHealthPlanIDPrimary) { $tmpHealthPlanIDPrimary = "9999999"; }
			$healthPlanIDPrimary = $tmpHealthPlanIDPrimary;
			$content = getValHcfaUb($content,$healthPlanIDPrimary,2192,2206,"left","A"); //51a-Health Plan Identification Number

			$tmpHealthPlanIDSecondary = trim($insCompArr[$row["secondaryInsuranceCoId"]]["institutional_Code_id"]);
			if(!$tmpHealthPlanIDSecondary) {$tmpHealthPlanIDSecondary = trim($insCompArr[$row["secondaryInsuranceCoId"]]["Payer_id_pro"]);}
			if(strtolower($tmpHealthPlanIDSecondary)== "null") { $tmpHealthPlanIDSecondary = ""; }
			//if(!$tmpHealthPlanIDSecondary) { $tmpHealthPlanIDSecondary = "9999999"; }
			$healthPlanIDSecondary = $tmpHealthPlanIDSecondary;
			$content = getValHcfaUb($content,$healthPlanIDSecondary,2428,2442,"left","A"); //51b-Health Plan Identification Number
			
			$tmpHealthPlanIDTertiary = trim($insCompArr[$row["tertiaryInsuranceCoId"]]["institutional_Code_id"]);
			if(!$tmpHealthPlanIDTertiary) {$tmpHealthPlanIDTertiary = trim($insCompArr[$row["tertiaryInsuranceCoId"]]["Payer_id_pro"]);}
			//if(!$tmpHealthPlanIDTertiary) { $tmpHealthPlanIDTertiary = "9999999"; }
			$healthPlanIDTertiary = $tmpHealthPlanIDTertiary;
			$content = getValHcfaUb($content,$healthPlanIDTertiary,2664,2678,"left","A"); //51c-Health Plan Identification Number
			
			$pri_y = $sec_y = $ter_y = "";
			if(trim($insCompArr[$row["primaryInsuranceCoId"]]['name'])) {
				$pri_y = "Y";
			}
			if(trim($insCompArr[$row["secondaryInsuranceCoId"]]['name'])) {
				$sec_y = "Y";
			}
			if(trim($insCompArr[$row["tertiaryInsuranceCoId"]]['name'])) {
				$ter_y = "Y";
			}

			$releaseOfInformationCertificationIndicator52a = $pri_y;
			$content = getValHcfaUb($content,$releaseOfInformationCertificationIndicator52a,2207,2207,"left","A"); //52a-Release of Information Certification Indicator
			
			$releaseOfInformationCertificationIndicator52b = $sec_y;
			$content = getValHcfaUb($content,$releaseOfInformationCertificationIndicator52b,2443,2443,"left","A"); //52b-Release of Information Certification Indicator

			$releaseOfInformationCertificationIndicator52c = $ter_y;
			$content = getValHcfaUb($content,$releaseOfInformationCertificationIndicator52c,2679,2679,"left","A"); //52c-Release of Information Certification Indicator

			$assignmentOfBenefitsCertificationIndicator53a = $pri_y;
			$content = getValHcfaUb($content,$assignmentOfBenefitsCertificationIndicator53a,2208,2208,"left","A"); //53a-Assignment of Benefits Certification Indicator

			$assignmentOfBenefitsCertificationIndicator53b = $sec_y;
			$content = getValHcfaUb($content,$assignmentOfBenefitsCertificationIndicator53b,2444,2444,"left","A"); //53b-Assignment of Benefits Certification Indicator

			$assignmentOfBenefitsCertificationIndicator53c = $ter_y;
			$content = getValHcfaUb($content,$assignmentOfBenefitsCertificationIndicator53c,2680,2680,"left","A"); //53c-Assignment of Benefits Certification Indicator

			//if($chk_insurance=="secondarySubmit"){
			$insPaidAmountPri = $insPaidAmountSec = $insPaidAmountTer = "0";
			$insPriSecTerCompArr = array("primaryInsuranceCoId","secondaryInsuranceCoId","tertiaryInsuranceCoId");
			foreach($insPriSecTerCompArr as $insPriSecTerCompName) {
				$insPaidAmountArr=array();
				$insPaidAmount="";

				$priSecTerInsPay=$row[$insPriSecTerCompName];
				$piadRes = $payment_data[$row['encounter_id']][$priSecTerInsPay];
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
			//}
			
			
			$priorPaymentsPayer54a = $insPaidAmountPri;
			$content = getValHcfaUb($content,$priorPaymentsPayer54a,2209,2218,"right","N"); //54a-Prior Payments - Payer

			$priorPaymentsPayer54b = $insPaidAmountSec;
			$content = getValHcfaUb($content,$priorPaymentsPayer54b,2445,2454,"right","N"); //54b-Prior Payments - Payer

			$priorPaymentsPayer54c = $insPaidAmountTer;
			$content = getValHcfaUb($content,$priorPaymentsPayer54c,2681,2690,"right","N"); //54c-Prior Payments - Payer

			$estimatedAmountDuePayer55a = "";
			$content = getValHcfaUb($content,$estimatedAmountDuePayer55a,2219,2228,"right","N"); //55a-Estimated Amount Due - Payer

			$estimatedAmountDuePayer55b = "";
			$content = getValHcfaUb($content,$estimatedAmountDuePayer55b,2455,2464,"right","N"); //55b-Estimated Amount Due - Payer

			$estimatedAmountDuePayer55c = "";
			$content = getValHcfaUb($content,$estimatedAmountDuePayer55c,2691,2700,"right","N"); //55c-Estimated Amount Due - Payer

			$nationalProviderId = $row["group_NPI"];
			$content = getValHcfaUb($content,$nationalProviderId,2875,2889,"left","A"); //56
			
			$otherBillingProviderIdentifier57a = trim($insCompArr[$row["primaryInsuranceCoId"]]['institutional_Code_id']);
			$content = getValHcfaUb($content,$otherBillingProviderIdentifier57a,2229,2243,"left","A"); //57a
			
			$otherBillingProviderIdentifier57b = trim($insCompArr[$row["secondaryInsuranceCoId"]]['institutional_Code_id']);
			$content = getValHcfaUb($content,$otherBillingProviderIdentifier57b,2465,2479,"left","A"); //57b

			$otherBillingProviderIdentifier57c = trim($insCompArr[$row["tertiaryInsuranceCoId"]]['institutional_Code_id']);
			$content = getValHcfaUb($content,$otherBillingProviderIdentifier57c,2701,2715,"left","A"); //57c

			$insuredNamePrimary = trim(ucfirst(stripslashes($objpriinsData->subscriber_lname))." ".ucfirst(stripslashes($objpriinsData->subscriber_fname)));
			$content = getValHcfaUb($content,$insuredNamePrimary,2244,2268,"left","A"); //58a
			$insuredNameSecondary = trim(ucfirst(stripslashes($objsecinsData->subscriber_lname))." ".ucfirst(stripslashes($objsecinsData->subscriber_fname)));
			$content = getValHcfaUb($content,$insuredNameSecondary,2480,2504,"left","A"); //58b
			$insuredNameTertiary = trim(ucfirst(stripslashes($objterinsData->subscriber_lname))." ".ucfirst(stripslashes($objterinsData->subscriber_fname)));
			$content = getValHcfaUb($content,$insuredNameTertiary,2716,2740,"left","A"); //58c
			
			$patientRelationshipToInsuredPrimary = $ins_rel_final_pri; //59a
			$content = getValHcfaUb($content,$patientRelationshipToInsuredPrimary,2269,2270,"left","A");
			$patientRelationshipToInsuredSecondary = $ins_rel_final_sec; //59b
			$content = getValHcfaUb($content,$patientRelationshipToInsuredSecondary,2505,2506,"left","A");
			$patientRelationshipToInsuredTertiary = $ins_rel_final_ter; //59c
			$content = getValHcfaUb($content,$patientRelationshipToInsuredTertiary,2741,2742,"left","A");
			
			$tmpInsuredUniqueIdentifierPrimary = trim(preg_replace("/[^A-Za-z0-9]/","",$objpriinsData->policy_number));
			if(!$tmpInsuredUniqueIdentifierPrimary) {
				$tmpInsuredUniqueIdentifierPrimary = "";
			}
			$insuredUniqueIdentifierPrimary = preg_replace("/[^A-Za-z0-9]/","",$tmpInsuredUniqueIdentifierPrimary);
			$content = getValHcfaUb($content,$insuredUniqueIdentifierPrimary,2271,2290,"left","A"); //60a
			
			$tmpInsuredUniqueIdentifierSecondary = trim(preg_replace("/[^A-Za-z0-9]/","",$objsecinsData->policy_number));
			if(!$tmpInsuredUniqueIdentifierSecondary) {
				$tmpInsuredUniqueIdentifierSecondary = "";
			}
			$insuredUniqueIdentifierSecondary = $tmpInsuredUniqueIdentifierSecondary;
			$content = getValHcfaUb($content,$insuredUniqueIdentifierSecondary,2507,2526,"left","A"); //60b
			
			$tmpInsuredUniqueIdentifierTertiary = trim(preg_replace("/[^A-Za-z0-9]/","",$objterinsData->policy_number));
			if(!$tmpInsuredUniqueIdentifierTertiary) {
				$tmpInsuredUniqueIdentifierTertiary = "";
			}
			$insuredUniqueIdentifierTertiary = $tmpInsuredUniqueIdentifierTertiary;
			$content = getValHcfaUb($content,$insuredUniqueIdentifierTertiary,2743,2762,"left","A"); //60c

			$insuranceGroupNamePrimary = $objpriinsData->plan_name;
			$content = getValHcfaUb($content,$insuranceGroupNamePrimary,2291,2304,"left","A"); //61a
			$insuranceGroupNameSecondary = $objsecinsData->plan_name;
			$content = getValHcfaUb($content,$insuranceGroupNameSecondary,2527,2540,"left","A"); //61b
			$insuranceGroupNameTertiary = $objterinsData->plan_name;
			$content = getValHcfaUb($content,$insuranceGroupNameTertiary,2763,2776,"left","A"); //61c

			$insuranceGroupNumberPrimary = $objpriinsData->group_number;
			$content = getValHcfaUb($content,$insuranceGroupNumberPrimary,2305,2321,"left","A"); //62a
			$insuranceGroupNumberSecondary = $objsecinsData->group_number;
			$content = getValHcfaUb($content,$insuranceGroupNumberSecondary,2541,2557,"left","A"); //62b
			$insuranceGroupNumberTertiary = $objterinsData->group_number;
			$content = getValHcfaUb($content,$insuranceGroupNumberTertiary,2777,2793,"left","A"); //62c

			$treatmentAuthorizationCode63a = $row["auth_no"];
			$content = getValHcfaUb($content,$treatmentAuthorizationCode63a,2322,2351,"left","A"); //63a
			$treatmentAuthorizationCode63b = "";
			$content = getValHcfaUb($content,$treatmentAuthorizationCode63b,2558,2587,"left","A"); //63b
			$treatmentAuthorizationCode63c = "";
			$content = getValHcfaUb($content,$treatmentAuthorizationCode63c,2794,2823,"left","A"); //63c
			
			$documentControlNumber64a = $row["claim_ctrl_pri"];
			$content = getValHcfaUb($content,$documentControlNumber64a,2352,2377,"left","A"); //64a
			$documentControlNumber64b = $row["claim_ctrl_sec"];
			$content = getValHcfaUb($content,$documentControlNumber64b,2588,2613,"left","A"); //64b
			$documentControlNumber64c = $row["claim_ctrl_ter"];
			$content = getValHcfaUb($content,$documentControlNumber64c,2824,2849,"left","A"); //64c

			$employerNameOfTheInsured65a = "";
			$content = getValHcfaUb($content,$employerNameOfTheInsured65a,2378,2402,"left","A"); //65a
			$employerNameOfTheInsured65b = "";
			$content = getValHcfaUb($content,$employerNameOfTheInsured65b,2614,2638,"left","A"); //65b
			$employerNameOfTheInsured65c = "";
			$content = getValHcfaUb($content,$employerNameOfTheInsured65c,2850,2874,"left","A"); //65c

			$enc_icd10 = $row["enc_icd10"];
			if($enc_icd10>0){
				$enc_icd10_ind="0";
			}else{
				$enc_icd10_ind="9";
			}
			$diagnosisAndProcedureVersion = $enc_icd10_ind;
			$content = getValHcfaUb($content,$diagnosisAndProcedureVersion,2890,2890,"left","A"); //66
			
			//if(in_array(strtolower($billing_global_server_name), array('lodenvision'))){
				$dignosis = array_unique($dignosis);
			//}
			$dignosis = array_values(array_filter($dignosis));
			$principalDiagnosisCode = $dignosis[0];
			$content = getValHcfaUb($content,$principalDiagnosisCode,2891,2897,"left","A"); //67-1
			$principalDiagnosisCodePOA = "";
			$content = getValHcfaUb($content,$principalDiagnosisCodePOA,2898,2898,"left","A"); //67-2

			$otherDiagStartPos = $otherDiagPOAStartPos = 0;
			
			for($k=1;$k<=17;$k++) {
				$otherDiagnosisCode 	= $dignosis[$k];
				$otherDiagStartPos 		= $otherDiagStartPos ? ($otherDiagStartPos+8) : 2899;
				$otherDiagEndPos 		= ($otherDiagStartPos+6);
				$content 				= getValHcfaUb($content,$otherDiagnosisCode,$otherDiagStartPos,$otherDiagEndPos,"left","A"); //67a1-67q1
			
				$otherDiagnosisCodePOA 	= "";
				$otherDiagPOAStartPos 	= $otherDiagPOAStartPos ? ($otherDiagPOAStartPos+8) : 2906;
				$otherDiagPOAEndPos 	= ($otherDiagPOAStartPos+0);
				$content 				= getValHcfaUb($content,$otherDiagnosisCodePOA,$otherDiagPOAStartPos,$otherDiagPOAEndPos,"left","A"); //67a2-67q2
			
			}

			$reservedForAssignmentByTheNUBCField68 = "";
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField68,3035,3051,"left","A"); //68

			$patientReasonForVisit70a = "";
			$content = getValHcfaUb($content,$patientReasonForVisit70a,3059,3065,"left","A"); //70a
			$patientReasonForVisit70b = "";
			$content = getValHcfaUb($content,$patientReasonForVisit70b,3066,3072,"left","A"); //70b
			$patientReasonForVisit70c = "";
			$content = getValHcfaUb($content,$patientReasonForVisit70c,3073,3079,"left","A"); //70c

			$prospectivePaymentSystemCode = ""; //71
			$content = getValHcfaUb($content,$prospectivePaymentSystemCode,3080,3083,"left","A");

			$externalCauseOfInjuryCode1 = "";
			$content = getValHcfaUb($content,$externalCauseOfInjuryCode1,3084,3090,"left","A"); //72a
			$externalCauseOfInjuryCode2 = "";
			$content = getValHcfaUb($content,$externalCauseOfInjuryCode2,3092,3098,"left","A"); //72b
			$externalCauseOfInjuryCode3 = "";
			$content = getValHcfaUb($content,$externalCauseOfInjuryCode3,3100,3106,"left","A"); //72c

			$externalCauseOfInjuryCodePOA1 = "";
			$content = getValHcfaUb($content,$externalCauseOfInjuryCodePOA1,3091,3091,"left","A"); //72a1
			$externalCauseOfInjuryCodePOA2 = "";
			$content = getValHcfaUb($content,$externalCauseOfInjuryCodePOA2,3099,3099,"left","A"); //72b1
			$externalCauseOfInjuryCodePOA3 = "";
			$content = getValHcfaUb($content,$externalCauseOfInjuryCodePOA3,3107,3107,"left","A"); //72c1

			$reservedForAssignmentByTheNUBCField73 = "";
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField73,3108,3116,"left","A"); //73

			$principalProcedureCode = "";
			$content = getValHcfaUb($content,$principalProcedureCode,3117,3123,"left","A"); //74-1
			$principalProcedureDate = "";
			$content = getValHcfaUb($content,$principalProcedureDate,3124,3129,"right","N"); //74-2

			$otherProcedureCode1 = "";
			$content = getValHcfaUb($content,$otherProcedureCode,3130,3136,"left","A"); //74a1
			$otherProcedureCode2 = "";
			$content = getValHcfaUb($content,$otherProcedureCode2,3143,3149,"left","A"); //74b1
			$otherProcedureCode3 = "";
			$content = getValHcfaUb($content,$otherProcedureCode3,3156,3162,"left","A"); //74c1
			$otherProcedureCode4 = "";
			$content = getValHcfaUb($content,$otherProcedureCode4,3169,3175,"left","A"); //74d1
			$otherProcedureCode5 = "";
			$content = getValHcfaUb($content,$otherProcedureCode5,3182,3188,"left","A"); //74e1

			$otherProcedureDates1 = "";
			$content = getValHcfaUb($content,$otherProcedureDates,3137,3142,"right","N"); //74a2
			$otherProcedureDates2 = "";
			$content = getValHcfaUb($content,$otherProcedureDates2,3150,3155,"right","N"); //74b2
			$otherProcedureDates3 = "";
			$content = getValHcfaUb($content,$otherProcedureDates3,3163,3168,"right","N"); //74c2
			$otherProcedureDates4 = "";
			$content = getValHcfaUb($content,$otherProcedureDates4,3176,3181,"right","N"); //74d2
			$otherProcedureDates5 = "";
			$content = getValHcfaUb($content,$otherProcedureDates5,3189,3194,"right","N"); //74e2

			$reservedForAssignmentByTheNUBCField75a = "";
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField75a,3195,3198,"left","A"); //75a
			$reservedForAssignmentByTheNUBCField75b = "";
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField75b,3199,3202,"left","A"); //75b
			$reservedForAssignmentByTheNUBCField75c = "";
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField75c,3203,3206,"left","A"); //75c
			$reservedForAssignmentByTheNUBCField75d = "";
			$content = getValHcfaUb($content,$reservedForAssignmentByTheNUBCField75d,3207,3210,"left","A"); //75c

			$pos_facility_npi = "";
			$pos_facility_name = $pro_pri_npi = $pro_sec_npi = $pro_tri_npi_sc = $pro_pri_npi = "";
			$tmpAttendingProviderLastNamePri = $tmpAttendingProviderFirstNamePri = $tmpAttendingProviderLastNameSec = $tmpAttendingProviderFirstNameSec = "";
			$tmpAttendingProviderLastNameTer = $tmpAttendingProviderFirstNameTer = "";
			if($row['group_institution']>0) {
				if($posFacilityId == '' || $posFacilityId == '0'){
					$posFacilityId = $row['ptDefaultFacility'];
				}
			}
			if($posFacilityId) {
				$pos_facility_name = $posFacilityNameArr[$posFacilityId];
				if($pos_facility_name) {
					$pos_facility_npi = $posFacilityArr[$posFacilityId];
				}
			}
			if($row["primaryProviderId"]){
				$pro_pri_npi=$userNpiArr[$row["primaryProviderId"]];
				$pro_pri_lic=$userLicenseArr[$row["primaryProviderId"]];
				$tmpAttendingProviderLastNamePri 	= $userRowArr[$row["primaryProviderId"]]["lname"];
				$tmpAttendingProviderFirstNamePri 	= $userRowArr[$row["primaryProviderId"]]["fname"];
				
				if(constant("ASCEMR_IMW_PROVIDER_ID") && $row["primaryProviderId"]==constant("ASCEMR_IMW_PROVIDER_ID") && $row["reff_phy_id"]) {
				//if(trim($row["ascId"]) !='0'){	
					$pro_pri_npi=$reffNpiArr[$row["reff_phy_id"]];
					//$pro_pri_lic=$reffRowArr[$row["reff_phy_id"]];
					$tmpAttendingProviderLastNamePri 	= $reffRowArr[$row["reff_phy_id"]]["LastName"];
					$tmpAttendingProviderFirstNamePri 	= $reffRowArr[$row["reff_phy_id"]]["FirstName"];
				}
			}
			if($row["reff_phy_nr"]==0 && $row["primaryProviderId"]!=$row["primary_provider_id_for_reports"] && $row["primary_provider_id_for_reports"]>0){
				$pro_pri_npi=$userNpiArr[$row["primary_provider_id_for_reports"]];
				$pro_pri_lic=$userLicenseArr[$row["primary_provider_id_for_reports"]];	
				$tmpAttendingProviderLastNamePri 	= $userRowArr[$row["primary_provider_id_for_reports"]]["lname"];
				$tmpAttendingProviderFirstNamePri 	= $userRowArr[$row["primary_provider_id_for_reports"]]["fname"];

			}elseif($row["reff_phy_nr"]==0 && $row["primaryProviderId"]!=$row["primary_provider_id_for_reports_sb"] && $row["primary_provider_id_for_reports_sb"]>0 && trim($row["ascId"]) !='0'){
				$pro_pri_npi=$userNpiArr[$row["primary_provider_id_for_reports_sb"]];
				$pro_pri_lic=$userLicenseArr[$row["primary_provider_id_for_reports_sb"]];	
				$tmpAttendingProviderLastNamePri 	= $userRowArr[$row["primary_provider_id_for_reports_sb"]]["lname"];
				$tmpAttendingProviderFirstNamePri 	= $userRowArr[$row["primary_provider_id_for_reports_sb"]]["fname"];

			}
			if($row["secondaryProviderId"]){
				$pro_sec_npi=$userNpiArr[$row["secondaryProviderId"]];
				$pro_sec_lic=$userLicenseArr[$row["secondaryProviderId"]];	
				$tmpAttendingProviderLastNameSec 	= $userRowArr[$row["secondaryProviderId"]]["lname"];
				$tmpAttendingProviderFirstNameSec 	= $userRowArr[$row["secondaryProviderId"]]["fname"];
			
			}
			if($row["tertiaryProviderId"]){
				$pro_tri_npi_sc=$userNpiArr[$row["tertiaryProviderId"]];
				$pro_tri_lic_sc=$userLicenseArr[$row["tertiaryProviderId"]];	
				$tmpAttendingProviderLastNameTer 	= $userRowArr[$row["tertiaryProviderId"]]["lname"];
				$tmpAttendingProviderFirstNameTer 	= $userRowArr[$row["tertiaryProviderId"]]["fname"];
			
			}
			$tmpAttendingProviderLastNamePri 	= trim(ucfirst(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpAttendingProviderLastNamePri))));
			$tmpAttendingProviderFirstNamePri 	= trim(ucfirst(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpAttendingProviderFirstNamePri))));
			
			$tmpAttendingProviderLastNameSec 	= trim(ucfirst(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpAttendingProviderLastNameSec))));
			$tmpAttendingProviderFirstNameSec 	= trim(ucfirst(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpAttendingProviderFirstNameSec))));
			
			$tmpAttendingProviderLastNameTer 	= trim(ucfirst(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpAttendingProviderLastNameTer))));
			$tmpAttendingProviderFirstNameTer 	= trim(ucfirst(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpAttendingProviderFirstNameTer))));
			//if($row["patient_id"]=="68038") { echo '<br>hlo '.$pos_facility_name; }
			if($pos_facility_name!=""){
				$tmpAttendingPhysicianIdTnLic 			= "";
				$tmpUpinNpi 							= $pos_facility_npi;
				$tmpOperatingPhysicianIdTnLicenseNumber = $pro_pri_lic;
				$tmpOperatingPhyUpinNpi 				= $pro_pri_npi;
				$tmpOperatingPhyLastName 				= $tmpAttendingProviderLastNamePri;
				$tmpOperatingPhyFirstName 				= $tmpAttendingProviderFirstNamePri;
				$tmpOtherProviderId1TnLicenseNumber 	= $pro_sec_lic;
				$tmpOtherProviderId1UpinNpi 			= $pro_sec_npi;
				$tmpOtherProvider1LastName				= $tmpAttendingProviderLastNameSec;
				$tmpOtherProvider1FirstName				= $tmpAttendingProviderFirstNameSec;
				$tmpOtherProviderId2TnLicenseNumber 	= ($pro_tri_lic_sc) ? $pro_tri_lic_sc : "";
				$tmpOtherProviderId2UpinNpi 			= ($pro_tri_npi_sc) ? $pro_tri_npi_sc : $row['group_NPI'];
				$tmpOtherProvider2LastName				= ($tmpAttendingProviderLastNameTer) ? $tmpAttendingProviderLastNameTer : "";
				$tmpOtherProvider2FirstName				= ($tmpAttendingProviderFirstNameTer) ? $tmpAttendingProviderFirstNameTer : "";

			}else {
				$tmpAttendingPhysicianIdTnLic 			= $pro_pri_lic;
				$tmpUpinNpi 							= $pro_pri_npi;
				$tmpOperatingPhysicianIdTnLicenseNumber = $pro_sec_lic;
				$tmpOperatingPhyUpinNpi 				= $pro_sec_npi;
				$tmpOperatingPhyLastName 				= $tmpAttendingProviderLastNameSec;
				$tmpOperatingPhyFirstName 				= $tmpAttendingProviderFirstNameSec;
				$tmpOtherProviderId1TnLicenseNumber 	= $pro_tri_lic_sc;
				$tmpOtherProviderId1UpinNpi 			= $pro_tri_npi_sc;
				$tmpOtherProvider1LastName				= $tmpAttendingProviderLastNameTer;
				$tmpOtherProvider1FirstName				= $tmpAttendingProviderFirstNameTer;
				$tmpOtherProviderId2TnLicenseNumber 	= "";
				$tmpOtherProviderId2UpinNpi 			= $row['group_NPI'];
				$tmpOtherProvider2LastName				= "";
				$tmpOtherProvider2FirstName				= "";
			}

			$tmpUpinNpi = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpUpinNpi))); 
			$attendingProviderNPI = $tmpUpinNpi;
			$content = getValHcfaUb($content,$attendingProviderNPI,3211,3221,"left","A"); //76a
			$attendingProviderSecondaryIdentifierQualifier = "";
			$content = getValHcfaUb($content,$attendingProviderSecondaryIdentifierQualifier,3222,3223,"left","A"); //76b
			$attendingProviderSecondaryIdentifier = $tmpAttendingPhysicianIdTnLic; 
			$content = getValHcfaUb($content,$attendingProviderSecondaryIdentifier,3224,3232,"left","A"); //76c

			$attendingProviderLastNamePri 		= $tmpAttendingProviderLastNamePri; 
			$content = getValHcfaUb($content,$attendingProviderLastNamePri,3233,3248,"left","A"); //76d

			$attendingProviderFirstNamePri 		= $tmpAttendingProviderFirstNamePri; 
			$content = getValHcfaUb($content,$attendingProviderFirstNamePri,3249,3260,"left","A"); //76e

			$tmpOperatingPhyUpinNpi = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpOperatingPhyUpinNpi))); 
			$operatingPhysicianNPI = $tmpOperatingPhyUpinNpi;
			$content = getValHcfaUb($content,$operatingPhysicianNPI,3261,3271,"left","A"); //77a
			$operatingPhysicianSecondaryIdentifierQualifier = "";
			$content = getValHcfaUb($content,$operatingPhysicianSecondaryIdentifierQualifier,3272,3273,"left","A"); //77b
			$operatingPhysicianSecondaryIdentifier = $tmpOperatingPhysicianIdTnLicenseNumber; 
			$content = getValHcfaUb($content,$operatingPhysicianSecondaryIdentifier,3274,3282,"left","A"); //77c
			
			$operatingPhyLastName = $tmpOperatingPhyLastName; 
			$content = getValHcfaUb($content,$operatingPhyLastName,3283,3298,"left","A"); //77d
			$operatingPhyFirstName = $tmpOperatingPhyFirstName; 
			$content = getValHcfaUb($content,$operatingPhyFirstName,3299,3310,"left","A"); //77e
				
			$otherProviderTypeQualifier1 = ""; 
			$content = getValHcfaUb($content,$otherProviderTypeQualifier1,3311,3312,"left","A"); //78a
			$otherProviderTypeQualifier2 = ""; 
			$content = getValHcfaUb($content,$otherProviderTypeQualifier2,3363,3364,"left","A"); //79a

			$otherProviderNPI1 = $tmpOtherProviderId1UpinNpi; 
			$content = getValHcfaUb($content,$otherProviderNPI1,3313,3323,"left","A"); //78b
			$otherProviderNPI2 = $tmpOtherProviderId2UpinNpi; 
			$content = getValHcfaUb($content,$otherProviderNPI2,3365,3375,"left","A"); //79b
			
			$otherPhysicianSecondaryIdentifierQualifier1 = ""; 
			$content = getValHcfaUb($content,$otherPhysicianSecondaryIdentifierQualifier1,3324,3325,"left","A"); //78c
			$otherPhysicianSecondaryIdentifierQualifier2 = ""; 
			$content = getValHcfaUb($content,$otherPhysicianSecondaryIdentifierQualifier2,3376,3377,"left","A"); //79c
			
			$otherPhysicianSecondaryIdentifier1 = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpOtherProviderId1TnLicenseNumber)));
			$content = getValHcfaUb($content,$otherPhysicianSecondaryIdentifier1,3326,3334,"left","A"); //78d
			$otherPhysicianSecondaryIdentifier2 = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$tmpOtherProviderId2TnLicenseNumber)));
			$content = getValHcfaUb($content,$otherPhysicianSecondaryIdentifier2,3378,3386,"left","A"); //79d
			
			$otherProviderLastName1 = $tmpOtherProvider1LastName; 
			$content = getValHcfaUb($content,$otherProviderLastName1,3335,3350,"left","A"); //78e
			$otherProviderLastName2 = $tmpOtherProvider2LastName; 
			$content = getValHcfaUb($content,$otherProviderLastName2,3387,3402,"left","A"); //79e

			$otherProviderFirstName1 = $tmpOtherProvider1FirstName; 
			$content = getValHcfaUb($content,$otherProviderFirstName1,3351,3362,"left","A"); //78f
			$otherProviderFirstName2 = $tmpOtherProvider2FirstName; 
			$content = getValHcfaUb($content,$otherProviderFirstName2,3403,3414,"left","A"); //79f

			
			
			$primaryInsuranceCompName 			= trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$insCompArr[$row["primaryInsuranceCoId"]]['name'])));
			$primaryInsuranceCompAddress 		= trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$insCompArr[$row["primaryInsuranceCoId"]]['contact_address'])));
			$primaryInsuranceCompCity			= $insCompArr[$row["primaryInsuranceCoId"]]['City'];
			$primaryInsuranceCompState			= $insCompArr[$row["primaryInsuranceCoId"]]['State'];
			$primaryInsuranceCompZip			= $insCompArr[$row["primaryInsuranceCoId"]]['Zip'];
			
			$primaryInsuranceCompCityStateZip	= trim($primaryInsuranceCompCity." ".$primaryInsuranceCompState." ".$primaryInsuranceCompZip);
			$primaryInsuranceCompCityStateZip	= trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$primaryInsuranceCompCityStateZip)));
			
			
			$tmpRemarksField 	= trim($primaryInsuranceCompName." ".$primaryInsuranceCompAddress." ".$primaryInsuranceCompCityStateZip);
			$remarksField 		= $tmpRemarksField; 
			$content = getValHcfaUb($content,$remarksField,3415,3505,"left","A"); //80

			$codeCodeFieldCodeQualifier1 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldCodeQualifier1,3506,3507,"left","A"); //81a1
			$codeCodeFieldCodeQualifier2 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldCodeQualifier2,3530,3531,"left","A"); //81b1
			$codeCodeFieldCodeQualifier3 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldCodeQualifier3,3554,3555,"left","A"); //81c1
			$codeCodeFieldCodeQualifier4 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldCodeQualifier4,3578,3579,"left","A"); //81d1

			$codeCodeFieldCode1 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldCode1,3508,3517,"left","A"); //81a2
			$codeCodeFieldCode2 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldCode2,3532,3541,"left","A"); //81b2
			$codeCodeFieldCode3 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldCode3,3556,3565,"left","A"); //81c2
			$codeCodeFieldCode4 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldCode4,3580,3589,"left","A"); //81d2

			$codeCodeFieldNumberOrValue1 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldNumberOrValue1,3518,3529,"right","N"); //81a3
			$codeCodeFieldNumberOrValue2 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldNumberOrValue2,3542,3553,"right","N"); //81b3
			$codeCodeFieldNumberOrValue3 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldNumberOrValue3,3566,3577,"right","N"); //81c3
			$codeCodeFieldNumberOrValue4 = ""; 
			$content = getValHcfaUb($content,$codeCodeFieldNumberOrValue4,3590,3601,"right","N"); //81d3
			
			$tmpPatientSocialSecurityNumber = trim(stripslashes(preg_replace("/[^A-Za-z0-9]/","",$row["patientSSN"]))); //REQUIRED
			if(!$tmpPatientSocialSecurityNumber || $tmpPatientSocialSecurityNumber == 0) {
				$tmpPatientSocialSecurityNumber = "";	
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
			$uniformPatientIdentifierSocialSecurityNumber = $patientSocialSecurityNumber;
			$content = getValHcfaUb($content,$uniformPatientIdentifierSocialSecurityNumber,3602,3610,"left","A"); //101
			
			$arrEthnicity = array(
			"African Americans" => 2,
			"American" => 2,
			"American Indians" => 2,
			"Chinese" => 2,
			"European Americans" => 2,
			"Hispanic Or Latino" => 1,
			"Jewish" => 2,
			"Not Hispanic Or Latino" => 2,
			"Unknown" => 2,
			"Declined to Specify" => 2);																			
			$patientEthnicityExp = explode(",",$row["ethnicity"]);
			//$patientEthnicity = trim($arrEthnicity[$patientEthnicityExp[0]]); //? trim($arrEthnicity[$patientEthnicityExp[0]]) : "9";
			$patientEthnicity = "";
			if(count($patientEthnicityExp)>0 && trim(strlen($row["ethnicity"]))>0) {
				for($cntEthnicity=0;$cntEthnicity<count($patientEthnicityExp);$cntEthnicity++) {
					if($arrEthnicity[$patientEthnicityExp[$cntEthnicity]] && !trim($patientEthnicity))	 {
						$patientEthnicity = $arrEthnicity[$patientEthnicityExp[$cntEthnicity]];	
					}
				}
				$patientEthnicity = trim($patientEthnicity) ? $patientEthnicity : 2;
			}
			if(!trim($patientEthnicity)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Ethnicity - Patient Hispanic/Latino Origin or Descent is blank or not valid";
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
			$content = getValHcfaUb($content,$patientEthnicity,3611,3611,"left","A"); //103a			

			$arrRace = array(
			"American Indian or Alaska Native" => "I",
			"Asian" => "A",
			"Black or African American" => "B",
			"Black" => "B",
			"Native Hawaiian or Other Pacific Islander" => "P",
			"Latin American" => "N",
			"White" => "W",											
			"Declined to Specify" => "U");
			$patientRaceExp = explode(",",$row["race"]);
			//$patientRace = trim($arrRace[$patientRaceExp[0]]); //? trim($arrRace[$patientRaceExp[0]]) : "9";
			$patientRace = "";
			if(count($patientRaceExp)>1) { //For Two or More Race Groups
				$patientRace = "M";
			}else if(count($patientRaceExp)>0 && trim(strlen($row["race"]))>0) {
				for($cntRace=0;$cntRace<count($patientRaceExp);$cntRace++) {
					if($arrRace[$patientRaceExp[$cntRace]] && !trim($patientRace))	 {
						$patientRace = $arrRace[$patientRaceExp[$cntRace]];	
					}
				}
			}

			if(!trim($patientRace) && trim($patientRaceExp[0])) {$patientRace = "N" ; }
			if(!trim($patientRace)) {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Race is missing";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "F";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}else if(trim($patientRace)=="N" || trim($patientRace)=="U") {
				$errorMsgArr[$row["patient_id"]]["patient_id"][] 	= $row["patient_id"];
				$errorMsgArr[$row["patient_id"]]["ptName"][] 		= $row["ptName"];
				$errorMsgArr[$row["patient_id"]]["msg"][] 			= "Race is patient refused or unknown or other";
				$errorMsgArr[$row["patient_id"]]["type"][] 			= "W";
				$errorMsgArr[$row["patient_id"]]["dob"] []			= $row["ptDobFormatNew"];
				$errorMsgArr[$row["patient_id"]]["dos"] []			= $row["date_of_service_format_new"];
				$errorMsgArr[$row["patient_id"]]["admitDate"][]		= $tmpAdmissionDateNew;
				$errorMsgArr[$row["patient_id"]]["encounterId"][]	= $row["encounter_id"];
			}
			$content = getValHcfaUb($content,$patientRace,3612,3612,"left","A"); //103b
			
			
			$patientSeverityUponAdmission = "";
			$content = getValHcfaUb($content,$patientSeverityUponAdmission,3613,3613,"left","A"); //121a
			
			$patientMorbidity = "";
			$content = getValHcfaUb($content,$patientMorbidity,3614,3614,"left","A"); //121b
			
			$unusualOccurrence = "";
			$content = getValHcfaUb($content,$unusualOccurrence,3615,3616,"left","A"); //121c
			
			$hospitalAcquiredInfectionCode1 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode1,3617,3618,"left","A"); //121d1a
			$hospitalAcquiredInfectionCode2 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode2,3628,3629,"left","A"); //121d2a
			$hospitalAcquiredInfectionCode3 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode3,3639,3640,"left","A"); //121d3a
			$hospitalAcquiredInfectionCode4 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode4,3650,3651,"left","A"); //121d4a
			$hospitalAcquiredInfectionCode5 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode5,3661,3662,"left","A"); //121d5a
			$hospitalAcquiredInfectionCode6 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode6,3672,3673,"left","A"); //121d6a
			$hospitalAcquiredInfectionCode7 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode7,3683,3684,"left","A"); //121d7a
			$hospitalAcquiredInfectionCode8 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode8,3694,3695,"left","A"); //121d8a
			$hospitalAcquiredInfectionCode9 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode9,3705,3706,"left","A"); //121d9a
			$hospitalAcquiredInfectionCode10 = "";
			$content = getValHcfaUb($content,$hospitalAcquiredInfectionCode10,3716,3717,"left","A"); //121d10a

			$hospitalacquiredInfectionMDRO1 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO1,3619,3619,"left","A"); //121d1b
			$hospitalacquiredInfectionMDRO2 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO2,3630,3630,"left","A"); //121d2b
			$hospitalacquiredInfectionMDRO3 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO3,3641,3641,"left","A"); //121d3b
			$hospitalacquiredInfectionMDRO4 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO4,3652,3652,"left","A"); //121d4b
			$hospitalacquiredInfectionMDRO5 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO5,3663,3663,"left","A"); //121d5b
			$hospitalacquiredInfectionMDRO6 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO6,3674,3674,"left","A"); //121d6b
			$hospitalacquiredInfectionMDRO7 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO7,3685,3685,"left","A"); //121d7b
			$hospitalacquiredInfectionMDRO8 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO8,3696,3696,"left","A"); //121d8b
			$hospitalacquiredInfectionMDRO9 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO9,3707,3707,"left","A"); //121d9b
			$hospitalacquiredInfectionMDRO10 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionMDRO10,3718,3718,"left","A"); //121d10b

			$hospitalacquiredInfectionNHSN1 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN1,3620,3626,"left","A"); //121d1c
			$hospitalacquiredInfectionNHSN2 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN2,3631,3637,"left","A"); //121d2c
			$hospitalacquiredInfectionNHSN3 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN3,3642,3648,"left","A"); //121d3c
			$hospitalacquiredInfectionNHSN4 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN4,3653,3659,"left","A"); //121d4c
			$hospitalacquiredInfectionNHSN5 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN5,3664,3670,"left","A"); //121d5c
			$hospitalacquiredInfectionNHSN6 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN6,3675,3681,"left","A"); //121d6c
			$hospitalacquiredInfectionNHSN7 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN7,3686,3692,"left","A"); //121d7c
			$hospitalacquiredInfectionNHSN8 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN8,3697,3703,"left","A"); //121d8c
			$hospitalacquiredInfectionNHSN9 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN9,3708,3714,"left","A"); //121d9c
			$hospitalacquiredInfectionNHSN10 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionNHSN10,3719,3725,"left","A"); //121d10c

			$hospitalacquiredInfectionProcedureLocation1 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation1,3627,3627,"left","A"); //121d1d
			$hospitalacquiredInfectionProcedureLocation2 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation2,3638,3638,"left","A"); //121d2d
			$hospitalacquiredInfectionProcedureLocation3 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation3,3649,3649,"left","A"); //121d3d
			$hospitalacquiredInfectionProcedureLocation4 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation4,3660,3660,"left","A"); //121d4d
			$hospitalacquiredInfectionProcedureLocation5 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation5,3671,3671,"left","A"); //121d5d
			$hospitalacquiredInfectionProcedureLocation6 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation6,3682,3682,"left","A"); //121d6d
			$hospitalacquiredInfectionProcedureLocation7 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation7,3693,3693,"left","A"); //121d7d
			$hospitalacquiredInfectionProcedureLocation8 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation8,3704,3704,"left","A"); //121d8d
			$hospitalacquiredInfectionProcedureLocation9 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation9,3715,3715,"left","A"); //121d9d
			$hospitalacquiredInfectionProcedureLocation10 = "";
			$content = getValHcfaUb($content,$hospitalacquiredInfectionProcedureLocation10,3726,3726,"left","A"); //121d10d

			$reservedField = "";
			$content = getValHcfaUb($content,$reservedField,3727,3900,"left","A"); //121e

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
//START CODE FOR HEADER CONTENT
$contentHeader = array();
$newContentHeader = '';
$dataSourceIdentifier = $userNpiArr[$phy_name_str]; 
$tmpConstantBillingProviderId = trim(constant("ASCEMR_IMW_PROVIDER_ID"));
if($tmpConstantBillingProviderId!="") {
	$dataSourceIdentifier = $userNpiArr[$tmpConstantBillingProviderId]; 		
}
$contentHeader = getValHcfaUb($contentHeader,$dataSourceIdentifier,1,15,"left","A");

$dataSourceName = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$hqFacilityRowArr["HQ"]["name"])));
$contentHeader = getValHcfaUb($contentHeader,$dataSourceName,16,40,"left","A");

$dataSourceAddress1 = trim(stripslashes(preg_replace("/[^0-9a-zA-Z_\s]/","",$hqFacilityRowArr["HQ"]["street"])));
$contentHeader = getValHcfaUb($contentHeader,$dataSourceAddress1,41,65,"left","A");

$dataSourceAddress2 = "";
$contentHeader = getValHcfaUb($contentHeader,$dataSourceAddress2,66,90,"left","A");

$dataSourceCity = stripslashes($hqFacilityRowArr["HQ"]["city"]);
$contentHeader = getValHcfaUb($contentHeader,$dataSourceCity,91,104,"left","A");

$dataSourceState = stripslashes($hqFacilityRowArr["HQ"]["state"]);
$contentHeader = getValHcfaUb($contentHeader,$dataSourceState,105,106,"left","A");

$dataSourceZipCode = stripslashes($hqFacilityRowArr["HQ"]["postal_code"]);
$contentHeader = getValHcfaUb($contentHeader,$dataSourceZipCode,107,115,"left","A");

$periodCoveredFirstDay = $from_date_format_short;
$contentHeader = getValHcfaUb($contentHeader,$periodCoveredFirstDay,116,121,"right","6");

$periodCoveredLastDay = $to_date_format_short;
$contentHeader = getValHcfaUb($contentHeader,$periodCoveredLastDay,122,127,"right","6");

$runDate = $cur_date_format_short;
$contentHeader = getValHcfaUb($contentHeader,$runDate,128,133,"right","6");

if(!$submissionType) { $submissionType = "O"; }
$inpatientOutpatientIndicator = $submissionType; //O or R
$contentHeader = getValHcfaUb($contentHeader,$inpatientOutpatientIndicator,134,134,"left","A");

$batchJobRunNumber = $batchName;
$contentHeader = getValHcfaUb($contentHeader,$batchJobRunNumber,135,159,"left","A");

$contentHeader = getValHcfaUb($contentHeader,$filler,160,3898,"left","A");

$submissionType = "O";
$contentHeader = getValHcfaUb($contentHeader,$submissionType,3899,3899,"left","A");

$recordType = "H";
$contentHeader = getValHcfaUb($contentHeader,$recordType,3900,3900,"left","A");


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

//START CODE FOR FOOTER CONTENT
$contentFooter = array();
$newContentFooter = "";

$totalRecords 	= $tmpTotalNumberofClaims; 
$contentFooter 	= getValHcfaUb($contentFooter,$totalRecords,1,10,"right","N");
$totalClaims 	= count($tmpTotalNumberofPatientArr); 
$contentFooter 	= getValHcfaUb($contentFooter,$totalClaims,11,20,"right","N");
$totalDollars 	= $tmpTotalChargesSubmitted; 
$contentFooter 	= getValHcfaUb($contentFooter,$totalDollars,21,32,"right","N");
$contentFooter 	= getValHcfaUb($contentFooter,$filler,33,3899,"left","A");
$recordType 	= "T"; 
$contentFooter 	= getValHcfaUb($contentFooter,$recordType,3900,3900,"left","A");

$maxFooter = max(array_keys($contentFooter));
$maxCntFooter = $maxFooter+1;
for($kFooter=0;$kFooter<$maxCntFooter;$kFooter++) {
	$valFooter = $contentFooter[$kFooter];
	if(trim($valFooter)=="") { $valFooter = " "; }
	$newContentFooter .= $valFooter;
}
if(trim($newContentFooter)!="") {
	$newContentFooter = "\n".$newContentFooter;
	//echo $newContentFooter;die;
}
//END CODE FOR FOOTER CONTENT


$show_msg= "";
if(trim($newContent)) {
	$newContent = $newContentHeader.$newContent.$newContentFooter;
	if(!is_dir($updir."/state_report")){
		//Create patient directory
		mkdir($updir."/state_report", 0777,true);
	}
	$contentFileName = $updir."/state_report/pa_".$_SESSION["authId"].".txt";
	if(file_exists($contentFileName)) {
		unlink($contentFileName);	
	}
	file_put_contents($contentFileName,$newContent);	
	
	$show_msg= "<span style='font-family:verdana;font-size:12px;font-weight:bold; padding-left:100px;'> PA State Report ".$reportName." have been exported successfully.</span>";
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

