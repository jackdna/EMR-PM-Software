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
?>
<?php
/*
FILE : patient_recall_result.php
PURPOSE : PATIENT APPOINTMENT RECALL REPORT
ACCESS TYPE : INCLUDED
*/
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('common/report_logic_info.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$page_data = '';	$printFile= false;

if($start_date == ""){
	$start_date = $curDate;
	$end_date = $curDate;
}
$curDate.='&nbsp;'.date(" h:i A");

$_POST['form_submitted']='1';

if($_POST['form_submitted']){

	$printFile = false;
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


	$st_date = getDateFormatDB($Start_date);
	$en_date = getDateFormatDB($End_date);
	//$primaryProviderId = join(",",$providerID);
	//$facility_name_str = join(",",$facility_name);

	$grp_id= (sizeof($groups)>0) ? implode(',',$groups) : '';
	$sc_name= (sizeof($facility_id)>0) ? implode(',',$facility_id) : '';
	$Physician= (sizeof($Physician)>0) ? implode(',',$Physician) : '';

	$firstFac = $facility_name[0];

	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}

	//GET GROUPS NAME
	$rs = imw_query("Select  gro_id,name,Contact_Name,group_Telephone,group_Email,group_Address1,group_City,group_State,group_Zip,
	MedicareReceiverId, MedicareSubmitterId	from groups_new");
	$arrAllGroupsInfo = array();
	while ($row = imw_fetch_array($rs)) {
		if($row['group_Telephone']!=''){
			$phone=core_phone_format($row['group_Telephone']);
			$phone='('.preg_replace('/-/', ')', $phone, 1);
		}else{
			$phone='(555)555-5555';
		}	
		
		$medicare_num=($row['MedicareReceiverId']!='')?$row['MedicareReceiverId'] : $row['MedicareSubmitterId'];	
		$medicare_num=($medicare_num=='')? '0000000' : $medicare_num;	
		
		$arrAllGroups[$row['gro_id']]=$row['name'];
		$arrAllGroupsInfo[$row['gro_id']]['name']=$row['name'];
		$arrAllGroupsInfo[$row['gro_id']]['contact_name']=$row['Contact_Name'];
		$arrAllGroupsInfo[$row['gro_id']]['phone']=$phone;
		$arrAllGroupsInfo[$row['gro_id']]['email']=$row['group_Email'];
		$arrAllGroupsInfo[$row['gro_id']]['address1']=$row['group_Address1'];
		$arrAllGroupsInfo[$row['gro_id']]['city']=$row['group_City'];
		$arrAllGroupsInfo[$row['gro_id']]['state']=$row['group_State'];
		$arrAllGroupsInfo[$row['gro_id']]['zip']=$row['group_Zip'];
		$arrAllGroupsInfo[$row['medicare_num']]['zip']=$medicare_num;
	}

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname, user_npi, licence, pro_suffix from users");
	$providerNameArr = $arrUserNPI=array();
	$croley_NPI=$croley_UserLicense='';
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$arrUserNPI[$id]=$providerResArr['user_npi'];
		$arrUserLicense[$id]=$providerResArr['licence'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname'], '', $providerResArr['pro_suffix']);

		if(strtolower($providerResArr['fname'])=='thomas' && strtolower($providerResArr['lname'])=='croley'){
			$croley_NPI=$providerResArr['user_npi'];
			$croley_UserLicense=$providerResArr['licence'];
		}			
	}

	// -- GET ALL POS-FACILITIES
	$arrAllFacilities=array();
	$arrAllFacilities[0] = 'No Facility';
	$qry = "select pos_facilityies_tbl.facilityPracCode as name,
		pos_facilityies_tbl.pos_facility_id as id,
		pos_tbl.pos_prac_code
		from pos_facilityies_tbl
		left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
		order by pos_facilityies_tbl.headquarter desc,
		pos_facilityies_tbl.facilityPracCode";
	$qryRs = imw_query($qry);
	while($qryRes  =imw_fetch_assoc($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$arrAllFacilities[$id] = $pos_prac_code;
	}						
	
	// ------------------------------
	//GET ALL CPT PRACTICE CODES (FOR DELETED AMOUNTS)
	$arrAllCPTCodes[0]='No CPT';
	$qry="Select cpt_fee_id, cpt4_code, departmentId FROM cpt_fee_tbl";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllCPTCodes[$res['cpt_fee_id']] = $res['cpt4_code'];
	}
	
	//GET ALL INSURANCE COMAPNIES
	$qry="Select id as insCompId,in_house_code as insCompINHouseCode,name as insCompName, institutional_type, ins_state_payer_code FROM insurance_companies";
	$rs=imw_query($qry);
	$arrAllInsCompaniesInfo=array();
	while($res=imw_fetch_assoc($rs)){
		$id = $res['insCompId'];
		$insName = $ers['insCompINHouseCode'];
		if(trim($insName) == ''){
			$insName = substr($res['insCompName'],0,20);
		}
		
		$inst_type='';
		if($res['institutional_type']=='INST_PROF')$inst_type='P';elseif($res['institutional_type']=='INST_ONLY')$inst_type='I';
		
		$arrAllInsCompaniesInfo[$id]['name'] = $insName;
		$arrAllInsCompaniesInfo[$id]['inst_type'] = $inst_type;
		$arrAllInsCompaniesInfo[$id]['payer_code'] = $res['ins_state_payer_code'];
	}
	
	
	//ETHNICITY
	$arrEthnicity['hispanic or latino']='E1';
	$arrEthnicity['puerto rican']='E1';
	$arrEthnicity['cuban']='E1';
	$arrEthnicity['central or south american or other spanish culture or origin']='E1';
	$arrEthnicity['regardless of race']='E1';
	$arrEthnicity['not hispanic or latino']='E2';
	$arrEthnicity['unknown']='E7';
	
	//RACE
	$arrRace['american indian or alaskan native']='1';
	$arrRace['asian']='2';
	$arrRace['black or african american']='3';
	$arrRace['native hawaiian or other pacific islander']='4';
	$arrRace['white']='5';
	$arrRace['other']='6';
	$arrRace['unknown']='7';
	
	$qry = "Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.billing_facility_id, main.pri_ins_id, 
	main.facility_id, main.gro_id,
	main.sec_ins_id, main.tri_ins_id, main.operator_id,
	SUM((main.charges * main.units)) as totalAmt,
	GROUP_CONCAT(main.proc_code_id) as proc_codes,
	CONCAT_WS(',', main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4) as 'dx_codes',
	main.date_of_service, main.proc_selfpay, main.pri_ins_id,
	main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,
	pd.id as patient_id, pd.lname, pd.fname, pd.mname, pd.default_facility, pd.ss, pd.race, pd.ethnicity,
	pd.DOB as 'patient_dob', pd.sex, pd.postal_code, pd.country_code, ins.policy_number 
	FROM report_enc_detail main 
	JOIN patient_data pd on pd.id = main.patient_id 
	LEFT JOIN users on users.id = main.primary_provider_id_for_reports 
	LEFT JOIN insurance_data ins ON (ins.ins_caseid = main.case_type_id AND LOWER(ins.type)='primary')
	WHERE (main.date_of_service BETWEEN '$st_date' AND '$en_date')";
	if(empty($grp_id) == false){
		$qry.= " and main.gro_id IN ($grp_id)";
	}
	if(empty($sc_name) == false){
		$qry.= " and main.facility_id IN ($sc_name)";	
	}
	if(empty($Physician) === false){
		$qry.= " and main.primary_provider_id_for_reports IN ($Physician)";
	}
	$qry.=" GROUP BY main.encounter_id";
	$rs=imw_query($qry) or die(imw_error());
	
	$xml_data_part='';
	$record_no=1;
	while($res = imw_fetch_assoc($rs)){
		$printFile = true;
		$arrival_hour=$discharge_hour='';
		$encounter_id = $res['encounter_id'];
		$main_encounter_id_arr[$encounter_id] = $encounter_id;
		$primaryProviderId = $res['primaryProviderId'];
		$sec_phy_id=$res['sec_prov_id'];
		$operator_id = $res['operator_id'];
		$deptId= $arrDeptOfCptCodes[$res["proc_code_id"]];
		$pos_facility_id = ($pay_location=='1') ? $res['billing_facility_id'] : $res['pos_facility_id'];	
		$pos_facility_id = (empty($pos_facility_id)==true) ? '0' : $pos_facility_id;
		$chgDetId = $res['charge_list_detail_id'];
		if(strtolower($res['sex'])=='male')$sex='M';elseif(strtolower($res['sex'])=='female')$sex='F';
		$sex=($sex=='')? 'Unknown': $sex;
		
		//CHECK IN/OUT INFO
		//$qry_cio="Select created_time,payment_type FROM check_in_out_payment WHERE created_on='".$res['date_of_service']."' AND patient_id='".$res['patient_id']."' GROUP BY payment_type";		
		$qry_cio="Select status, status_time FROM previous_status WHERE new_appt_date='".$res['date_of_service']."' AND patient_id='".$res['patient_id']."' AND status IN(13,11) ORDER BY id";
		$rs_cio=imw_query($qry_cio);
		while($res_cio=imw_fetch_assoc($rs_cio)){
			if($res_cio['status']==13 && $arrival_hour==''){ //GETTING FIRST CHECK-IN TIME
				$hour=date("H", strtotime($res_cio['status_time']));
				$arrival_hour=$hour;
			}if($res_cio['status']==11){ //GETTING LAST CHECK-OUT TIME
				$hour=date("H", strtotime($res_cio['status_time']));
				$discharge_hour=$hour;
			}
		}
		$arrival_hour=($arrival_hour=='')? '99': $arrival_hour;
		$discharge_hour=($discharge_hour=='')? '99': $discharge_hour;
		
		//ETHNICITY
		$ethnicity= '';
		$ethnicity= $arrEthnicity[strtolower($res['ethnicity'])];
		$ethnicity= ($ethnicity=='')? 'E7': $ethnicity;
		
		//RACE
		$race= '';
		$race= $arrRace[strtolower($res['race'])];
		if($res['race']!='' && $race==''){
			$race='6';
		}
		$race= ($race=='')? '7': $race;	
		
		$res['patient_dob']=($res['patient_dob']=='0000-00-00') ? '1880-01-01' : $res['patient_dob'];
		$res['postal_code']=($res['postal_code']!='')? $res['postal_code']: '00000';
		$res['country_code']=($res['country_code']!='')? substr($res['country_code'],0,2):'99';
		$res['ss']=($res['ss']!='')? str_replace('-','',$res['ss']):'777777777';
		
		//PRINCIPAL PAYER CODE
		$payer_code='';
		if($res['proc_selfpay']=='1' || $res['pri_ins_id']<=0){ 
			$payer_code='L'; //SELF PAY
		}else{
			$payer_code=$arrAllInsCompaniesInfo[$res['pri_ins_id']]['payer_code'];
		}
		
		
		$xml_data_part.='
		<RECORD id="'.$record_no.'">
		<AHCA_NUM>0000000066</AHCA_NUM>
		<MED_REC_NUM>'.$encounter_id.'</MED_REC_NUM>
		<PATIENT_SSN>'.$res['ss'].'</PATIENT_SSN>
		<PATIENT_ETHNICITY>'.$ethnicity.'</PATIENT_ETHNICITY>
		<PATIENT_RACE>'.$race.'</PATIENT_RACE>
		<PATIENT_BIRTHDATE>'.$res['patient_dob'].'</PATIENT_BIRTHDATE>
		<PATIENT_SEX>'.$sex.'</PATIENT_SEX>
		<PATIENT_ZIP>'.$res['postal_code'].'</PATIENT_ZIP>
		<PATIENT_COUNTRY>'.$res['country_code'].'</PATIENT_COUNTRY>
		<SERVICE_CODE>1</SERVICE_CODE>
		<ADMIT_SOURCE>00</ADMIT_SOURCE>
		<PRINC_PAYER_CODE>'.$payer_code.'</PRINC_PAYER_CODE>';
			 
		//DX CODES
		$arrdx_codes= array_unique(array_filter(explode(',', $res['dx_codes'])));
		$i=0;
		$first_dx_code='';
		foreach($arrdx_codes as $dx_code){
			if($i==0){
				$first_dx_code=$dx_code;
				$xml_data_part.='<PRINC_DIAG_CODE>'.$dx_code.'</PRINC_DIAG_CODE>';		
			}else{
				$xml_data_part.='<OTHER_DIAG_CODE>'.$dx_code.'</OTHER_DIAG_CODE>';		
			}
			$i++;
		}
		
		//$xml_data_part.='<EVAL_MGMT_CODE></EVAL_MGMT_CODE>';
		
		//CPT CODES
		$arrcpt_codes= array_unique(array_filter(explode(',', $res['proc_codes'])));
		foreach($arrcpt_codes as $cpt_code){
			$xml_data_part.='<OTHER_CPT_HCPCS_CODE>'.$arrAllCPTCodes[$cpt_code].'</OTHER_CPT_HCPCS_CODE>';		
		}

		
		$primaryUserLicense=$arrUserLicense[$primaryProviderId];
		$primaryNPI=$arrUserNPI[$primaryProviderId];
		
		//OTHER PHYSICIAN
		if($sec_phy_id>0){
			$sec_phy_license=$arrUserLicense[$sec_phy_id];
			$sec_phy_npi=$arrUserNPI[$sec_phy_id];
		}else{
			$sec_phy_license=$arrUserLicense[$primaryProviderId];
			$sec_phy_npi=$arrUserNPI[$primaryProviderId];			
		}
		
		if($GLOBALS["LOCAL_SERVER"]=='CFEI'){ //THESE SETTINGS WERE REQUESTED FROM SERVER
			$sec_phy_license=$primaryUserLicense=$croley_UserLicense;
			$sec_phy_npi=$primaryNPI=$croley_NPI;
		}
		
		$xml_data_part.='
		 <ATTENDING_PRACT_ID>'.$primaryUserLicense.'</ATTENDING_PRACT_ID>
		 <ATTENDING_PRACT_NPI>'.$primaryNPI.'</ATTENDING_PRACT_NPI>
		 <OPERATING_PRACT_ID>'.$primaryUserLicense.'</OPERATING_PRACT_ID>
		 <OPERATING_PRACT_NPI>'.$primaryNPI.'</OPERATING_PRACT_NPI>
		 <OTHER_PRACT_ID>'.$sec_phy_license.'</OTHER_PRACT_ID>
		 <OTHER_PRACT_NPI>'.$sec_phy_npi.'</OTHER_PRACT_NPI>
		 <PHARMACY_CHARGES>0</PHARMACY_CHARGES>
		 <MED_SURG_SUPPLY_CHARGES>0</MED_SURG_SUPPLY_CHARGES>
		 <LAB_CHARGES>0</LAB_CHARGES>
		 <RADIOLOGY_IMAGING_CHARGES>0</RADIOLOGY_IMAGING_CHARGES>
		 <CARDIOLOGY_CHARGES>0</CARDIOLOGY_CHARGES>
		 <OPER_ROOM_CHARGES>0</OPER_ROOM_CHARGES>
		 <ANESTHESIA_CHARGES>0</ANESTHESIA_CHARGES>
		 <RECOVERY_ROOM_CHARGES>0</RECOVERY_ROOM_CHARGES>
		 <ER_ROOM_CHARGES>0</ER_ROOM_CHARGES>
		 <TRAUMA_RESP_CHARGES>0</TRAUMA_RESP_CHARGES>
		 <TREATMENT_OBSERVATION_ROOM_CHARGES>0</TREATMENT_OBSERVATION_ROOM_CHARGES>
		 <GI_SERVICES_CHARGES>0</GI_SERVICES_CHARGES>
		 <EXTRA_CORP_SHOCK_WAVE_CHARGES>0</EXTRA_CORP_SHOCK_WAVE_CHARGES>
		 <OTHER_CHARGES>'.round($res['totalAmt'],0).'</OTHER_CHARGES>
		 <TOTAL_CHARGES>'.round($res['totalAmt'],0).'</TOTAL_CHARGES>
		 <VISIT_BEGIN_DATE>'.$res['date_of_service'].'</VISIT_BEGIN_DATE>
		 <VISIT_END_DATE>'.$res['date_of_service'].'</VISIT_END_DATE>
		 <ARRIVAL_HOUR>'.$arrival_hour.'</ARRIVAL_HOUR>
		 <ED_DISCHARGE_HOUR>'.$discharge_hour.'</ED_DISCHARGE_HOUR>
		 <PATIENT_REASON>'.$first_dx_code.'</PATIENT_REASON>
		 <PATIENT_STATUS>01</PATIENT_STATUS>
		</RECORD>';		
		
		$res['policy_number']=(strtolower($res['policy_number'])=='null')? '' : $res['policy_number'];
		
		$tag_part=
		'<SUBMISSION_TYPE>'.$submissionType.'</SUBMISSION_TYPE>
		 <PROC_DATE>'.date('Y-m-d').'</PROC_DATE>
		 <AHCA_NUM>0000000066</AHCA_NUM>
		 <MEDICARE_NUM>'.$arrAllGroupsInfo[$res['gro_id']]['medicare_num'].'</MEDICARE_NUM>
		 <ORG_NAME>'.$arrAllGroupsInfo[$res['gro_id']]['name'].'</ORG_NAME>
		 <CONTACT_PERSON>
			 <NAME>'.$arrAllGroupsInfo[$res['gro_id']]['contact_name'].'</NAME>
			 <PHONE>'.$arrAllGroupsInfo[$res['gro_id']]['phone'].'</PHONE>
			 <EMAIL>'.$arrAllGroupsInfo[$res['gro_id']]['email'].'</EMAIL>
			 <STREET>'.$arrAllGroupsInfo[$res['gro_id']]['address1'].'</STREET>
			 <CITY>'.$arrAllGroupsInfo[$res['gro_id']]['city'].'</CITY>
			 <STATE>'.$arrAllGroupsInfo[$res['gro_id']]['state'].'</STATE>
			 <ZIP>'.$arrAllGroupsInfo[$res['gro_id']]['zip'].'</ZIP>
		</CONTACT_PERSON>';
		 
		 $record_no++;
	}unset($rs);
		
	$HTMLCreated=0;
	if($printFile ==true){
		$HTMLCreated=1;
		$record_no-=1;
		//$quarter_no= ceil(date('n')/3);
		$quarter_no=$_POST['quarter'];
		
		$xml_data='<?xml version="1.0" encoding="UTF-8"?>
		<HC_DATA xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.fdhc.state.fl.us/xmlschemas/AS10-3.xsd">
		 <HEADER>
			 <TRANS_CODE>Q</TRANS_CODE>
			 <RPT_YEAR>'.date('Y').'</RPT_YEAR>
			 <RPT_QTR>'.$quarter_no.'</RPT_QTR>
			 <DATA_TYPE>AS10-3</DATA_TYPE>
			 '.$tag_part.'
		 </HEADER>
			<RECORDS>
				'.$xml_data_part.'
			</RECORDS>
		 <TRAILER>
		 <NUMBER_OF_RECORDS>'.$record_no.'</NUMBER_OF_RECORDS>
		 </TRAILER>
		</HC_DATA>';
		
		$file_name='asc_state_info.xml';
		$xml_file_name= write_html("", $file_name);
		
		$xmlobj=new SimpleXMLElement($xml_data);
		$xmlobj->asXML($xml_file_name);
		//file_put_contents('asc_state_info.xml', $xml_data);
		
		echo '<div class="text-center alert alert-info">XML is generated.<br>Please click on link near application bottom to download XML file.</div>';
		
	}else{
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>


 

