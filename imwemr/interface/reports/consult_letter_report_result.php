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

/*
FILE : ProductivityResult.php
PURPOSE : PRODUCTIVITY RESULT FOR PHYSICIAN 
ACCESS TYPE : DIRECT
*/
$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');
$FCName= $_SESSION['authId'];
$pureSelfPay=false;
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

	$printFile = false;
	$Sdate = $Start_date;
	$Edate = $End_date;
	$writte_off_arr = array();
	$arrInsOfEnc=array();

	// COMBINE INS AND INS GROUPS
	if(empty($ins_carriers)==false){ $tempInsArr[] = implode(",",$ins_carriers); }
	if(empty($insuranceGrp)==false){ $tempInsArr[] = implode(",",$insuranceGrp); 	}
	$tempSelIns = implode(',', $tempInsArr);
	$tempInsArr = array();
	if(empty($tempSelIns)==false){
	$tempInsArr = explode(',', $tempSelIns);
	}
	$tempInsArr = array_unique($tempInsArr);
	$insuranceName  = implode(',', $tempInsArr);
	$arrInsurance=array();
	if(sizeof($tempInsArr)>0){
		$arrInsurance=array_combine($tempInsArr,$tempInsArr);
	}
	unset($tempInsArr);
	

	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$sc_name= (sizeof($facility_id)>0) ? implode(',',$facility_id) : '';
	$Physician= (sizeof($physician)>0) ? implode(',',$physician) : '';
	$cpt_code_id= (sizeof($cpt)>0) ? implode(',',$cpt) : '';
	$insuranceGrp= (sizeof($insuranceGrp)>0) ? implode(',',$insuranceGrp) : '';
	$insuranceName= (sizeof($arrInsurance)>0) ? implode(',',$arrInsurance) : '';
	$dx_code10= (sizeof($icd10_codes)>0) ? "'".implode("','", $icd10_codes)."'"  : '';
	//---------------------------------------

	//--- CHANGE DATE FORMAT -------
	if($Start_date != '' && $End_date != ''){
		$Start_date = getDateFormatDB($Start_date);
		$End_date = getDateFormatDB($End_date);	
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
	// ------------------------------
		
	//GET SCHEDULE FACILITIES
	$arrPosFacOfSchFac=array();
	$rs=imw_query("Select id, fac_prac_code FROM facility");	
	while($res=imw_fetch_assoc($rs)){
		$arrPosFacOfSchFac[$res['id']] = $res['fac_prac_code'];
	}unset($rs);
	
	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
		
		// two character array
		$operatorInitial = substr($providerResArr['fname'],0,1);
		$operatorInitial .= substr($providerResArr['lname'],0,1);
		$userNameTwoCharArr[$id] = strtoupper($operatorInitial);
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

	//GET ALL CPT PRACTICE CODES (FOR DELETED AMOUNTS)
	$arrAllCPTCodes[0]='No CPT';
	$qry="Select cpt_fee_id, cpt_prac_code, departmentId FROM cpt_fee_tbl";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllCPTCodes[$res['cpt_fee_id']] = $res['cpt_prac_code'];
		$arrDeptOfCptCodes[$res['cpt_fee_id']] = $res['departmentId'];
	}

	//GET INSURANCE GROUP DROP DOWN
	$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
	$arrAllInsGroups[0] = 'No Insurance';
	$arrInsMapInsGroups[0]='0';
	while ($row = imw_fetch_array($insGroupQryRes)) {
		$ins_grp_id = $row['id'];
		$ins_grp_name = $row['title'];
		$arrAllInsGroups[$ins_grp_id] = $ins_grp_name;
	
		$qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $row['id'] . "'";
		$res = imw_query($qry);
		$tmp_grp_ins_arr = array();
		if (imw_num_rows($res) > 0) {
			while ($det_row = imw_fetch_array($res)) {
				$arrInsMapInsGroups[$det_row['id']]= $ins_grp_id;
			}
		}
	}

	//GETTING MAIN DATA	
	if(trim($cpt_code_id) != '' || trim($dx_code10)!= '' || trim($insuranceName) != ''){
		$qry = "Select main.encounter_id, main.pri_ins_id, date_format(main.date_of_service,'".$dateFormat."') as date_of_service,
		main.primary_provider_id_for_reports  as 'primaryProviderId', 
		main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4,
		patient_data.id as patient_id, patient_data.DOB, 
		patient_data.lname,	patient_data.fname, patient_data.mname,
		cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code, main.facility_id
		FROM report_enc_detail main 
		JOIN patient_data on patient_data.id = main.patient_id 
		JOIN users on users.id = main.primary_provider_id_for_reports 
		JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
		WHERE (main.date_of_service between '$Start_date' and '$End_date')				 
		AND main.del_status='0'";
		if(empty($sc_name) == false){
			$qry.= " and main.facility_id IN ($sc_name)";	
		}
		if(empty($Physician) === false){
			$qry.= " and main.primary_provider_id_for_reports IN ($Physician)";
		}
		if(trim($cpt_code_id) != ''){
			$qry.= " AND main.proc_code_id in ($cpt_code_id)";
		}
		if(trim($dx_code) != '' || trim($dx_code10) != ''){
			$qry.= ' AND (';
			$andOR='';
			if(trim($dx_code)!= ''){
				$qry.= " (main.dx_id1 in ($dx_code)
				or main.dx_id2 in ($dx_code)
				or main.dx_id3 in ($dx_code)
				or main.dx_id4 in ($dx_code))";
				$andOR=' OR ';
			}
			if(trim($dx_code10)!= ''){
				$qry.=$andOR." (main.dx_id1 in ($dx_code10)
				or main.dx_id2 in ($dx_code10)
				or main.dx_id3 in ($dx_code10)
				or main.dx_id4 in ($dx_code10))";
			}
			$qry.= ') ';	
		}
		if(trim($insuranceName) != ''){
			if(trim($ins_type) == ''){
				$qry.= " and (main.pri_ins_id in ($insuranceName)
					or main.sec_ins_id in ($insuranceName)
					or main.tri_ins_id in ($insuranceName))";
			}
			else{
				$ins_type_arr=explode(',',$ins_type);
				$qry.= " and (";
				for($i=0;$i<count($ins_type_arr);$i++){
					$ins_nam=$ins_type_arr[$i];
					if(trim($ins_nam)!='Self Pay')
					{
						$mul_or="";
						if($i>0){
							$mul_or=" or ";
						}
					
						$qry.= " $mul_or main.$ins_nam in ($insuranceName)";
					}
				}
				$qry.= " )";
			}
		}
		if($age_from>0){
			$qry.= " AND TIMESTAMPDIFF(YEAR, patient_data.DOB, CURDATE())>=".$age_from;
		}
		if($age_to>0){
			$qry.= " AND TIMESTAMPDIFF(YEAR, patient_data.DOB, CURDATE())<=".$age_to;
		}

		//if(trim($phy_type) != '' && trim($phy_type) == 'pcp' && empty($id_reff_physician) == false){
		if(empty($id_reff_physician) == false){	
			$qry .= " AND patient_data.primary_care_phy_id IN ($id_reff_physician)";
		}
		$qry.= " ORDER BY users.lname,users.fname, main.date_of_service, patient_data.lname,patient_data.fname,main.encounter_id";
		$res=imw_query($qry);
		
		$main_encounter_id_arr = array();
		$facilityNameArr = array();
		$physician_initial_arr = array();
		$arrPatNoFacility=array();
		$arrMainDataCPT=array();
		$arrMainDataDX=array();
		while($rs = imw_fetch_assoc($res)){
			$eid = $rs['encounter_id'];
			$arrPatientIds[$rs['patient_id']]=$rs['patient_id'];
			$main_encounter_id_arr[$eid] = $eid;
			$primaryProviderId = $rs['primaryProviderId'];
			$deptId= $arrDeptOfCptCodes[$rs["proc_code_id"]];
			$facilityPracCode = $rs['facilityPracCode'];
			$cpt_code= $arrAllCPTCodes[$rs["proc_code_id"]];
			$arrMainDataCPT[$eid][$cpt_code]=$cpt_code;
			$arrMainDataDX[$eid][$rs["dx_id1"]]=$rs["dx_id1"];
			$arrMainDataDX[$eid][$rs["dx_id2"]]=$rs["dx_id2"];
			$arrMainDataDX[$eid][$rs["dx_id3"]]=$rs["dx_id3"];
			$arrMainDataDX[$eid][$rs["dx_id4"]]=$rs["dx_id4"];
			
			$mainResArr[$eid] = $rs;
			
		}unset($res);
	}
	//GETTING CONSULT LETTER DATA
	$arrConsultInfo=array();
	$arrFetchedRefPhyIds=array();
	$strPatient = "";
	$qryProAndFac = "";
	$fields = "";
	if(trim($cpt_code_id) != '' || trim($dx_code10)!= '' || trim($insuranceName) != ''){
		if(sizeof($arrPatientIds)>0){
			$strPatientIds = implode(',', $arrPatientIds);
			$strPatient = "patient_id IN($strPatientIds) AND";
		} else {
			$qryProAndFac = "1=2";
		}
	} else {
		$fields = " pd.lname, pd.fname, pd.mname,";
		$join = " INNER JOIN patient_data pd ON (tp.patient_id = pd.id )";
		$qryProAndFac = " AND tp.status !='1'";
		if(empty($sc_name) == false){
			$qryProAndFac = " AND pd.default_facility in(".$sc_name.")";	
		}
		if(empty($Physician) === false){
			$qryProAndFac .= " AND tp.provider_signature_id in(".$Physician.") ";
		}
		$ordby = " ORDER BY `report_sent_date` DESC";
	}		
	
	$qry="Select $fields tp.patient_consult_id, tp.patient_id, DATE_FORMAT(cur_date, '".$dateFormat."') as 'generated_date', 
	DATE_FORMAT(tp.report_sent_date , '".$dateFormat."') as 'sent_date', tp.fax_ref_phy_id, tp.fax_number, tp.templateName, sf.updox_status FROM patient_consult_letter_tbl tp $join LEFT JOIN send_fax_log_tbl sf ON sf.patient_consult_id = tp.patient_consult_id WHERE $strPatient (tp.date BETWEEN '$Start_date' and '$End_date') $qryProAndFac $ordby";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$pid=$res['patient_id'];
		$genDate=$res['generated_date'];
		$arrConsultInfo[$pid][$genDate]=$res;
		if($res['fax_ref_phy_id']>0)$tempRefPhyIds[$res['fax_ref_phy_id']]=$res['fax_ref_phy_id'];
		$defultMainArr[$pid][] = $res;
	}unset($rs);

	//GETTING FETCHED REF-PHYSICIAN NAMES
	if(sizeof($tempRefPhyIds)>0){
		$strTempRefPhyIds=implode(',', $tempRefPhyIds);
		$qry="Select physician_Reffer_id, FirstName, MiddleName, LastName FROM refferphysician WHERE physician_Reffer_id IN($strTempRefPhyIds)";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$arrFetchedRefPhyIds[$res['physician_Reffer_id']] = core_name_format($res['LastName'], $res['FirstName'], $res['MiddleName']);
			
		}unset($rs);
	}

	//HTML CREATION
	$colspan=22;
	$colspanPdf=17;
	$startColspan=11;
	$startColspanPDF=5;

	$arrForAjax=array();
	foreach($mainResArr as $enc_id => $encDataArr){
		$printFile=true;
		$patient_name = core_name_format($encDataArr['lname'], $encDataArr['fname'], $encDataArr['mname']);		
		$patient_id = $encDataArr['patient_id'];
		$dos = $encDataArr['date_of_service'];
		$age = get_age($encDataArr['DOB']);
		$facilityName = $arrAllFacilities[$encDataArr['facility_id']];
		$primaryInsName  = $arrAllInsCompanies[$encDataArr['pri_ins_id']];
		
		//IF NO-LETTER SELECTED THEN SKIP THE RECORD IF LETTER IS GENERATED.
		if($no_letter=='1' && $arrConsultInfo[$patient_id][$dos]){
			continue;
		}

		//IF UNSENT SELECTED THEN SKIP THE RECORD IF LETTER IS NOT SENT.
		if($unsent=='1'){
			if(!$arrConsultInfo[$patient_id][$dos]){ continue; }
			if($arrConsultInfo[$patient_id][$dos] && $arrConsultInfo[$patient_id][$dos]['sent_date']!='00-00-0000'){ continue; }
		}

		//IF RESEND SELECTED THEN SKIP THE RECORD WHERE NO LETTER GENERATED AND LETTER NOT SENT.
		if($resend=='1'){
			if(!$arrConsultInfo[$patient_id][$dos] || $arrConsultInfo[$patient_id][$dos]['sent_date']=='00-00-0000'){
				continue;
			}
		}
		
		$pcpid="";
		$pcpId = imw_query("SELECT primary_care_phy_id FROM `patient_data` where id = $patient_id");
		while($qryRow = imw_fetch_assoc($pcpId)) {
			$pcpid = $qryRow['primary_care_phy_id'];
		}
		$qry = "select pcp.FirstName as pcpFName,pcp.MiddleName as pcpMName,pcp.LastName as pcpLName from refferphysician pcp where pcp.physician_Reffer_id = '".$pcpid."'";
		$qryRes = imw_query($qry);
		$pcpPhyDetail = array();
		while($qryRow = imw_fetch_assoc($qryRes)) {
			$pcpPhyDetail[] = $qryRow;
		}
		$pcpName = "";
		if($pcpPhyDetail[0]['pcpLName'] != "" || $pcpPhyDetail[0]['pcpFName'] != ""){
			$pcpName = $pcpPhyDetail[0]['pcpLName'].", ".$pcpPhyDetail[0]['pcpFName']." ".$pcpPhyDetail[0]['pcpMName'];
		}
		$arrCPT = array();
		$arrCPT_CSV = array();
		$arrDxCodes = array();
		
		$strCPT = implode(', ', $arrMainDataCPT[$enc_id]);
		$arrMainDataDX[$enc_id]=array_filter($arrMainDataDX[$enc_id]);
		$strDxCodes = implode(', ', $arrMainDataDX[$enc_id]);
		
		$consult_letter='N';
		$consult_id=$sentToRefPhy=$fax=$gen_date=$sent_date=$template_name='';
		if($arrConsultInfo[$patient_id][$dos]){
			$consultInfo=$arrConsultInfo[$patient_id][$dos];
			$consult_letter='Y';
			$sentToRefPhy = $arrFetchedRefPhyIds[$consultInfo['fax_ref_phy_id']];
			if(trim($sentToRefPhy) == ""){ $sentToRefPhy = $pcpName; }
			$fax=$consultInfo['fax_number'];
			$gen_date= $consultInfo['generated_date'];
			$sent_date=$consultInfo['sent_date'];
			$consult_id=$consultInfo['patient_consult_id']; 
			$template_name= $consultInfo['templateName'];
			$updoxstatus= $consultInfo['updox_status'];
			//if($updox_status == "success"){$updoxstatus = 'Y';} else {$updoxstatus = 'N';}
			//$arrForAjax[$consult_id]= ($sent_date!='00-00-0000') ? 'y' : 'n';
            if($updoxstatus == "failure"){$updoxstatus = 'Failed';}
		}
		
		$checkbox='';
		if($consult_id>0){
			$disabled=$checked='';
			if($sent_date!='00-00-0000'){ $disabled=''; $checked='checked'; } else { $sent_date ='Not Sent';}
			$checkbox= '<input style="cursor:pointer;" type="checkbox" name="letter_patients" value="'.$consult_id.'" '.$checked.' '.$disabled.' />';
		}
		
		$csvFileData .='
		<tr>
			<td class="text_10" style="background:#FFFFFF; text-align:center">'.$checkbox.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$providerNameArr[$encDataArr['primaryProviderId']].'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$facilityName.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$patient_id.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$patient_name.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$pcpName.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$age.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$primaryInsName.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$dos.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$strCPT.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$strDxCodes.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$consult_letter.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$sentToRefPhy.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$fax.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$gen_date.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$sent_date.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.$template_name.'</td>
			<td class="text_10" style="background:#FFFFFF;">'.ucfirst($updoxstatus).'</td>
		</tr>';
	}
	
	foreach($defultMainArr as $pt_id => $mainDataArr){
		foreach($mainDataArr as $obj){
			$printFile=true;
			$patient_name = core_name_format($obj['lname'], $obj['fname'], $obj['mname']);		
			$patient_id = $obj['patient_id'];
			$generated_date = $obj['generated_date'];
			$sentdate = $obj['sent_date'];
			$dos = $obj['generated_date'];
			$updstatus= $obj['updox_status'];
			//if($upd_status == "success"){$updstatus = 'Y';} else {$updstatus = 'N';}
            if($updstatus == "failure"){$updstatus = 'Failed';}
		
		$consult_letter='N';
		$consult_id=$sentToRefPhy=$fax=$gen_date=$sent_date=$template_name='';
		
		
		if($arrConsultInfo[$patient_id][$dos]){
			$consultInfo=$arrConsultInfo[$patient_id][$dos];
			$consult_letter='Y';
			$fax=$consultInfo['fax_number'];
			$gen_date= $consultInfo['generated_date'];
			$sent_date=$consultInfo['sent_date'];
			$consult_id=$consultInfo['patient_consult_id']; 
			$template_name= $consultInfo['templateName'];
		}

		
		$checkbox='';
		if($consult_id>0){
			$disabled=$checked='';
			if($sentdate !='00-00-0000'){
				$disabled=''; $checked='checked'; 
				$checkbox= '<img src="'.$GLOBALS['php_server'].'/library/images/confirm.gif" alt=""/>';
			} else { 
				$sentdate ='Not Sent';
				$checkbox= '<input style="cursor:pointer;" type="checkbox" name="letter_patients" value="'.$consult_id.'" '.$checked.' '.$disabled.' />';
			}
			
		}
			$csvFileDataDefault .='<tr>
				<td class="text_10" style="background:#FFFFFF; text-align:center">'.$checkbox.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:left">'.$obj['patient_id'].'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:left">'.$patient_name.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:left">'.$obj['templateName'].'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:center">'.$obj['generated_date'].'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:center">'.$sentdate.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:center">'.ucfirst($updstatus).'</td>
			</tr>';
		}
	}
}
//json_encode($arrForAjax);

//GET SELECTED
$selFac = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);
$selPhy = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
$selInsurance = $CLSReports->report_display_selected($insuranceName,'insurance',1, $insurance_cnt);
$selCPT = $CLSReports->report_display_selected($cpt_code_id,'cpt_code',1, $allCPTCount);
$selDX10 = $CLSReports->report_display_selected($dx_code10,'dx_code10',1, $allDXCount10);

$HTMLCreated=0;
if($printFile == true and ($csvFileData != '' || $csvFileDataDefault != '')){
	$sel_letter_sts='';
	if($no_letter=='1')$sel_letter_sts='No Letter';
	if($unsent=='1')$sel_letter_sts='Unsent';
	if($resend=='1')$sel_letter_sts='Resend';
	
	$headPart='
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
		<tr >
			<td style="text-align:left;" class="rptbx1" width="33%">Consult Letter Report</td>
			<td style="text-align:left;" class="rptbx2" width="34%">Billing Date From: '.$Sdate.' To : '.$Edate.'</td>
			<td style="text-align:left;" class="rptbx3" width="33%">Created by '.$opInitial.' on '.$curDate.'</td>
		</tr>
		<tr>
			<td class="rptbx1">Facility: '.$selFac.'</td>
			<td class="rptbx2">Physician: '.$selPhy.'
			<td class="rptbx3">Insurance: '.$selInsurance.'</td>
		</tr>
		<tr>
			<td class="rptbx1">CPT: '.$selCPT.'</td>
			<td class="rptbx2">ICD10: '.$selDX10.'</td>
			<td class="rptbx3">Age: '.$age_from.'-'.$age_to.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Letter Status: '.$sel_letter_sts.'
			</td>
		</tr>
	</table>';

	if(trim($cpt_code_id) != '' || trim($dx_code10)!= '' || trim($insuranceName) != ''){
	$csv_file_data=
	$headPart.'
	<table class="rpt_table rpt rpt_table-bordered">
	<tr>
		<td style="text-align:center; width:10px;" class="text_b_w">
			<input style="cursor:pointer;" type="checkbox" name="check_all" id="check_all" value="1"/>
		</td>
		<td style="text-align:center; width:100px;" class="text_b_w">Physician</td>
		<td style="text-align:center; width:100px;" class="text_b_w">Facility</td>
		<td style="text-align:center; width:50px;" class="text_b_w ">Patient ID</td>
		<td style="text-align:center; width:100px;" class="text_b_w">Patient</td>
		<td style="text-align:center; width:100px;" class="text_b_w">PCP</td>
		<td style="text-align:center; width:30px" class="text_b_w">Age</td>
		<td style="text-align:center; width:100px" class="text_b_w">Ins. Pri.</td>
		<td style="text-align:center; width:70px" class="text_b_w">DOS</td>
		<td style="text-align:center; width:80px" class="text_b_w">CPT</td>
		<td style="text-align:center; width:80px" class="text_b_w">ICD-10</td>
		<td style="text-align:center; width:30px" class="text_b_w">Letter Sent</td>
		<td style="text-align:center; width:80px" class="text_b_w">Sent To Phy.</td>
		<td style="text-align:center; width:80px" class="text_b_w">Fax Number</td>
		<td style="text-align:center; width:60px" class="text_b_w">Generated Date</td>
		<td style="text-align:center; width:60px" class="text_b_w">Sent Date</td>
		<td style="text-align:center; width:60px" class="text_b_w">Letter Template</td>
		<td style="text-align:center; width:60px" class="text_b_w">Fax Status</td>
	</tr>'
	.$csvFileData.
	'</table>';
	} else {
	$csv_file_data=
	$headPart.'
	<table class="rpt_table rpt rpt_table-bordered">
	<tr>
		<td style="text-align:center; width:10px;" class="text_b_w">
			<input style="cursor:pointer;" type="checkbox" name="check_all" id="check_all" value="1"/>
		</td>
		<td style="text-align:left; width:50px;" class="text_b_w ">Patient ID</td>
		<td style="text-align:left; width:100px;" class="text_b_w">Patient</td>
		<td style="text-align:left; width:100px;" class="text_b_w">Letter Template</td>
		<td style="text-align:center; width:100px;" class="text_b_w">Generated Date</td>
		<td style="text-align:center; width:100px;" class="text_b_w">Sent Date</td>
		<td style="text-align:center; width:100px;" class="text_b_w">Fax Status</td>
	</tr>'
	.$csvFileDataDefault.
	'</table>';
	}
	
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$csv_file_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$pdf_page_content;

	$file_location = write_html($strHTML);
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}

echo $csv_file_data;	
?>