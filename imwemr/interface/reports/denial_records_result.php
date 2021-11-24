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
FILE : INS_DEFER_RESULT_RESULT.PHP
PURPOSE :  FETCH ALL RESULT FOR PAYROLL REPORT DATA
ACCESS TYPE : DIRECT
*/
$dateFormat= get_sql_date_format();
$curDate=date($phpDateFormat);
$billTypeArr = explode(",", $billType);

$printFile = true;
if($_POST['form_submitted']){

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

	if(trim($Start_date) == ""){
		$Start_date = $curDate;
		// entered_date
	}
	if(trim($End_date) == ""){
		$End_date = $curDate;
	}

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_id)<=0 && isPosFacGroupEnabled()){
		$facility_id = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_id)<=0){
			$facility_id[0]='NULL';
		}
	}

	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$facility_name= (sizeof($facility_id)>0) ? implode(',',$facility_id) : '';
	$Physician= (sizeof($filing_provider)>0) ? implode(',',$filing_provider) : '';

	//--- CHANGE DATE FORMAT FOR DATABASE -----------
	$StartDate = getDateFormatDB($Start_date);
	$EndDate = getDateFormatDB($End_date);

	//--- GET GROUP NAME ---
	$group_query = "select gro_id,name from groups_new";
	$groupQryRs = imw_query($group_query);		
	while($groupQryRes = imw_fetch_assoc($groupQryRs)){
		$arrAllGroups[$groupQryRes['gro_id']] = $groupQryRes['name'];
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
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}

	//GET ALL INSURANCE COMAPNIES
	$arrAllInsCompanies[0]='No Insurance';
	$qry="Select id as insCompId,in_house_code as insCompINHouseCode,name as insCompName FROM insurance_companies";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$id = $res['insCompId'];
		$insName = $ers['insCompINHouseCode'];
		if(trim($insName) == ''){
			$insName = substr($res['insCompName'],0,20);
		}
		
		$arrAllInsCompanies[$id] = $insName;
	}

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}

	//GET ALL CPT PRACTICE CODES
	$arrAllCPTCodes[0]='No CPT';
	$qry="Select cpt_fee_id, cpt_prac_code FROM cpt_fee_tbl";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllCPTCodes[$res['cpt_fee_id']] = $res['cpt_prac_code'];
	}
	//------------------------------------------------------------
	
	// Collecting Insurance Companies and groups
	if(empty($ins_carriers)==false){ $tempInsArr[] = implode(',',$ins_carriers); }
	if(empty($insuranceGrp)==false){ $tempInsArr[] = implode(',',$insuranceGrp); }
	$tempSelIns = implode(',', $tempInsArr);
	$tempInsArr = array();
	if(empty($tempSelIns)==false){
	$tempInsArr = explode(',', $tempSelIns);
	}
	$tempInsArr = array_unique($tempInsArr);
	$insuranceName  = implode(',', $tempInsArr);
	$arrInsurance=array();
	if(sizeof($tempInsArr)>0){
		$arrInsurance = array_combine($tempInsArr,$tempInsArr);
	} 
	unset($tempInsArr);
	$insCompanies = '';
	if(is_array($arrInsurance) && count($arrInsurance) > 0){
		$insCompanies = implode(',',$arrInsurance);
	}
	
	$dateCondition = "";	
	if($DateRangeFor=='dot'){
		$dateCondition =  "(DATE_FORMAT(entered_date, '%Y-%m-%d') BETWEEN '$StartDate' and '$EndDate')";
	} else {
		$dateCondition = "(deniedDate BETWEEN '$StartDate' and '$EndDate')";
	}
	
	//GETTING DENIAL RECORDS
	$qry="Select charge_list_detail_id, encounter_id, deniedBy, deniedById, deniedAmount, CAS_type, CAS_code, DATE_FORMAT(deniedDate, '".$dateFormat."') as 'denied_date', denialOperatorId FROM deniedpayment WHERE $dateCondition ORDER BY deniedId";	
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){	
		$chgdetid=$res['charge_list_detail_id'];
		$arrDeniedInfo[$chgdetid]=$res;
	}
	if(sizeof($arrDeniedInfo)>0){
		$arrChgDetIds=array_keys($arrDeniedInfo);
		$strChgDetIds=implode(',', $arrChgDetIds);
	
		$qry = "Select patient_charge_list.encounter_id,patient_charge_list.patient_id,
		date_format(patient_charge_list.date_of_service, '".$dateFormat."') as date_of_service, patient_charge_list.gro_id,
		patient_charge_list.facility_id, pos_facilityies_tbl.facilityPracCode,
		patient_charge_list.patient_id,patient_charge_list_details.newBalance, patient_charge_list_details.procCode,
		patient_charge_list_details.procCharges * patient_charge_list_details.units as procCharges,
		cpt_fee_tbl.cpt4_code, patient_charge_list_details.charge_list_detail_id,
		patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
		patient_charge_list.primaryInsuranceCoId,
		patient_charge_list.secondaryInsuranceCoId,
		patient_charge_list.tertiaryInsuranceCoId, 
		patient_data.lname, patient_data.fname,	patient_data.mname, date_format(patient_data.DOB, '".$dateFormat."') as 'dob' 
		FROM patient_charge_list 
		JOIN patient_charge_list_details on patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
		JOIN patient_data on patient_data.id = patient_charge_list.patient_id 
		JOIN users on users.id = patient_charge_list.primary_provider_id_for_reports
		JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = patient_charge_list.facility_id
		JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
		WHERE patient_charge_list_details.charge_list_detail_id IN(".$strChgDetIds.")";
		if(empty($grp_id) == false){
			$qry.= " AND patient_charge_list.gro_id IN ($grp_id)";
		}
		if(empty($Physician) == false){
			$qry.= " AND patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
		}
		if(trim($facility_name) != ''){
			$qry.= " and patient_charge_list.facility_id IN ($facility_name)";
		}
		if(empty($insCompanies) === false){
			$qry.= " AND (patient_charge_list.primaryInsuranceCoId IN ($insCompanies)
						OR patient_charge_list.secondaryInsuranceCoId IN ($insCompanies) 
						OR patient_charge_list.tertiaryInsuranceCoId IN ($insCompanies))";	
		}
		if(empty($inc_refraction) == false){
			$qry.= " AND cpt_fee_tbl.cpt4_code != '92015' AND (cpt_fee_tbl.cpt_desc!='Refraction' OR cpt_fee_tbl.cpt_desc!='refraction') ";
		}
		$qry.= " ORDER BY users.lname, users.fname, users.mname, patient_charge_list.date_of_service desc";		
		$rs=imw_query($qry);		
		$encounter_id_arr = array();
		$ins_id_arr = array();
		while($res=imw_fetch_assoc($rs)){	
			$chgdetid=$res['charge_list_detail_id'];
						
			$arrMainData[$chgdetid]=$res;
		}		
	}

	$printFile = false;

	//--- GET ALL PAYMENTS -----
	$encounter_id_str = join(',',$encounter_id_arr);
	
	if(count($arrMainData) > 0){
		$printFile = true;
		$page_content_data = '';

		foreach($arrMainData as $chgdetid => $chgDetails){

			$deniedByIns='';
			
			$patient_name = core_name_format($chgDetails['lname'], $chgDetails['fname'], $chgDetails['mname']);
			if(strtolower($arrDeniedInfo[$chgdetid]['deniedBy'])=='insurance')$deniedByIns= $arrAllInsCompanies[$arrDeniedInfo[$chgdetid]['deniedById']];
			$denied_code=$arrDeniedInfo[$chgdetid]['CAS_type'].' '.$arrDeniedInfo[$chgdetid]['CAS_code'];
			$denied_code=$arrDeniedInfo[$chgdetid]['CAS_type'].' '.$arrDeniedInfo[$chgdetid]['CAS_code'];
			$den_op_name = $providerNameArr[$arrDeniedInfo[$chgdetid]['denialOperatorId']];
		
			$csv_data .='
			<tr>
				<td class="text_10" style="background:#FFFFFF;">'.$arrAllGroups[$chgDetails['gro_id']].'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$chgDetails['patient_id'].'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$patient_name.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$chgDetails['dob'].'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$chgDetails['encounter_id'].'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$arrDeniedInfo[$chgdetid]['deniedBy'].'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$deniedByIns.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$chgDetails['date_of_service'].'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$arrAllCPTCodes[$chgDetails['procCode']].'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($chgDetails['procCharges'],2).'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$CLSReports->numberFormat($chgDetails['newBalance'],2).'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$providerNameArr[$chgDetails['primaryProviderId']].'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$arrAllFacilities[$chgDetails['facility_id']].'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$arrDeniedInfo[$chgdetid]['denied_date'].'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$denied_code.'</td>
				<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$arrDeniedInfo[$chgdetid]['denied_date'].'</td>
				<td class="text_10" style="text-align:center; background:#FFFFFF;">'.$den_op_name.'</td>
			</tr>';
		}
		
		
		//--- OPERATOR INITIAL ----
		$pro_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$pro_name = $pro_name_arr[1][0];
		$pro_name .= $pro_name_arr[0][0];
		$pro_name = strtoupper($pro_name);
		
		$curDate = date(phpDateFormat().' h:i A');

		$group_name = $CLSReports->report_display_selected($grp_id,'group',1,$allGrpCount);
		$physician_name = $CLSReports->report_display_selected($Physician,'physician',1,$allPhyCount);
		$practice_name = $CLSReports->report_display_selected($facility_name,'practice',1,$allFacCount);

		
		//---  HTML PAGE HEADER ---
/*		$page_content ='
			<page backtop="19mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>	
					<td align="left" class="rptbx1" style="width:345px;">Denial Records Report</td>
					<td align="left" class="rptbx2" style="width:345px;">From : '.$Sdate.' To '.$Edate.'</td>
					<td align="left" class="rptbx3" style="width:345px;">Created by '.$pro_name.' on '.$curDate.'</td>
				</tr>
				<tr>
					<td align="left" class="rptbx1">Selected Group : '.$group_name.'</td>
					<td align="left" class="rptbx2">Selected Physician : '.$physician_name.'</td>
					<td align="left" class="rptbx3">Selected Facility : '.$practice_name.'</td>
				</tr>
			</table>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>
					<td class="text_b_w" style="text-align:center;" width="190">Biling Entity</td>
					<td class="text_b_w" style="text-align:center;" width="60">Patient Account</td>
					<td class="text_b_w" style="text-align:center;" width="60">Name</td>
					<td class="text_b_w" style="text-align:center;" width="90">DOB</td>
					<td class="text_b_w" style="text-align:center;" width="90">Claim id</td>
					<td class="text_b_w" style="text-align:center;" width="90">Denied From</td>
					<td class="text_b_w" style="text-align:center;" width="90">Denied by Ins.</td>
					<td class="text_b_w" style="text-align:center;" width="90">DOS</td>
					<td class="text_b_w" style="text-align:center;" width="90">CPT Code</td>
					<td class="text_b_w" style="text-align:center;" width="90">Charge Amount</td>
					<td class="text_b_w" style="text-align:center;" width="90">Charge Balance</td>
					<td class="text_b_w" style="text-align:center;" width="90">Physician</td>
					<td class="text_b_w" style="text-align:center;" width="90">Location</td>
					<td class="text_b_w" style="text-align:center;" width="90">DOE</td>
					<td class="text_b_w" style="text-align:center;" width="90">Last Denial Code</td>
					<td class="text_b_w" style="text-align:center;" width="90">Last Denial Date</td>
				</tr>
			</table>
			</page_header>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				'.$pdf_data.'
			</table>
			</page>';*/



		// for CSV data
		$csv_content ='
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>	
					<td align="left" class="rptbx1" style="width:225px;">Denial Records Report</td>
					<td align="left" class="rptbx2" style="width:215px;">('.strtoupper($DateRangeFor).') From : '.$Start_date.' To '.$End_date.'</td>
					<td align="left" class="rptbx3" style="width:235px;">Created by '.$pro_name.' on '.$curDate.'</td>
				</tr>
				<tr>
					<td align="left" class="rptbx1">Selected Group : '.$group_name.'</td>
					<td align="left" class="rptbx2">Selected Physician : '.$physician_name.'</td>
					<td align="left" class="rptbx3">Selected Facility : '.$practice_name.'</td>
				</tr>
			</table>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>
					<td class="text_b_w" style="text-align:center;" width="190">Billing Entity</td>
					<td class="text_b_w" style="text-align:center;" width="60">Patient Account</td>
					<td class="text_b_w" style="text-align:center;" width="60">Name</td>
					<td class="text_b_w" style="text-align:center;" width="90">DOB</td>
					<td class="text_b_w" style="text-align:center;" width="90">Claim id</td>
					<td class="text_b_w" style="text-align:center;" width="90">Denied From</td>
					<td class="text_b_w" style="text-align:center;" width="90">Denied by Ins.</td>
					<td class="text_b_w" style="text-align:center;" width="90">DOS</td>
					<td class="text_b_w" style="text-align:center;" width="90">CPT Code</td>
					<td class="text_b_w" style="text-align:center;" width="90">Charge Amount</td>
					<td class="text_b_w" style="text-align:center;" width="90">Charge Balance</td>
					<td class="text_b_w" style="text-align:center;" width="90">Physician</td>
					<td class="text_b_w" style="text-align:center;" width="90">Location</td>
					<td class="text_b_w" style="text-align:center;" width="90">DOE</td>
					<td class="text_b_w" style="text-align:center;" width="90">Last Denial Code</td>
					<td class="text_b_w" style="text-align:center;" width="90">Last Denial Date</td>
					<td class="text_b_w" style="text-align:center;" width="90">Denial Opr</td>
				</tr>
				'.$csv_data.'
			</table>';

	}

	$HTMLCreated=0;
	//--- CREATE HTML FILE FOR PDF ----
	if($printFile == true){
		$HTMLCreated=1;
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';				
		$csv_file_data= $styleHTML.$csv_content;

/*		$stylePDF='<style>'.file_get_contents('css/reports_pdf.css').'</style>';	
		$strHTML = $stylePDF.$page_content;
		$file_location = write_html($strHTML);*/
	}
	else{
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}

$op='l';
if($output_option=='view' || $output_option=='output_csv'){
	echo $csv_file_data;	
}
?>