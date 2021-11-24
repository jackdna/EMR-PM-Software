<?php
$without_pat = "yes";
require_once("reports_header.php");
$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');		
$FCName= $_SESSION['authId'];
$page_data = NULL;
$pdf_data = NULL;
$curDate = date($phpDateFormat);
if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}

$arrFacilitySel=array();
$arrDoctorSel=array();

//CHECK FOR PRIVILEGED FACILITIES
if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
	$facility_name = $CLSReports->getFacilityName('', '0', 'array');

	if(sizeof($facility_name)<=0){
		$facility_name[0]='NULL';
	}
}

$sel_grp = $CLSReports->report_display_selected(implode(',',$groups),'group',1,$grp_cnt);
$sel_fac = $CLSReports->report_display_selected(implode(',',$facility_name),'facility',1,$posfac_cnt);
$sel_phy = $CLSReports->report_display_selected(implode(',',$phyId),'physician',1,$phy_cnt);
$sel_opr = $CLSReports->report_display_selected(implode(',',$operator_id),'operator',1,$opr_cnt);

//pre($_REQUEST);
unset($_REQUEST['operator']);
unset($_REQUEST['department']);
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
	//---------------------
	//VARIABLE DECLARATION

	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	//FACILITY
	$sc_name = join(',',$facility_name);
	
	//PHYSICIAN
	$rqArrPhyId = $_REQUEST['phyId'];
	$Physician = join(',',$rqArrPhyId);
	//CREDITING PHYSICIAN
	$str_crediting_phy='';
	if(sizeof($_REQUEST['crediting_provider'])>0){
		$str_crediting_phy = implode(',',$_REQUEST['crediting_provider']);
	}
	
	//operator_id
	$rqArrOprId = $_REQUEST['operator_id'];
	$operator = join(',',$rqArrOprId);
	
	$groupId = $_REQUEST['groups'];
	$grp_id = join(',',$groupId);
	
	
	$departmentId = $_REQUEST['department'];
	$department = join(',',$departmentId);
	
	
	//--- CHANGE DATE FORMAT ----
	$startDate = getDateFormatDB($Start_date);
	$endDate   = getDateFormatDB($End_date);
	
	$formatDelDate='%Y-%m-%d';
	$delDate=$endDate;
	
	//GET CREDITY TYPE ARRAY
	$arrCreditTypes=unserialize(html_entity_decode($ccTypeArr));	
	$arrCreditTypes[0]='No CC Type';
	
	// GET DEFAULT FACILITY
	$rs=imw_query("Select fac_prac_code from facility where facility_type  = '1' LIMIT 1")or die(imw_error().'_27');
	$res = imw_fetch_array($rs);
	$headPosFacility = $res['fac_prac_code'];
	
	// GET SELECTED SCHEDULER FACILITIES
	$sch_fac_id_str='';
	$fac_query = "Select id,name,fac_prac_code from facility";
	$fac_query_rs = imw_query($fac_query)or die(imw_error().'_33');
	$sch_fac_id_arr = array();
	while($fac_query_res = imw_fetch_array($fac_query_rs)){	
		$fac_id = $fac_query_res['id'];
		$pos_fac_id = addslashes($fac_query_res['fac_prac_code']);
		$sch_pos_fac_arr[$fac_id] = $pos_fac_id;
		$sch_fac_arr[$pos_fac_id][] = $fac_id;
	}
	//pre($sch_fac_arr);pre($sch_pos_fac_arr);
	// GET ALL POS FACILITIES DETAILS
	$qry = "Select facilityPracCode, pos_facility_id from pos_facilityies_tbl";
	$rs=imw_query($qry)or die(imw_error().'_44');
	$posFacilityArr = array();
	$posFacilityArr[0] = 'No Facility';
	while($posQryRes = imw_fetch_array($rs)){
		$pos_facility_id = $posQryRes['pos_facility_id'];
		$posFacilityArr[$pos_facility_id] = $posQryRes['facilityPracCode'];
	}	

	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname FROM users")or die(imw_error().'_54');
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		$providerNameArr[$id] = $pro_name;
	}
	
	
	$operatorNameArr[0] = 'No Operator';
	$rs=imw_query("Select id, fname, mname, lname FROM users")or die(imw_error().'_54');
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$opr_name_arr = array();
		$opr_name_arr["LAST_NAME"] = $res['lname'];
		$opr_name_arr["FIRST_NAME"] = $res['fname'];
		$opr_name_arr["MIDDLE_NAME"] = $res['mname'];
		$opr_name = changeNameFormat($opr_name_arr);
		$operatorNameArr[$id] = $opr_name;
	}

	//--- GET GROUP NAME ---
	$group_name = "All";
	if(empty($grp_id) === false){
		$group_query = imw_query("select name from groups_new where gro_id = '$grp_id'");
		if(imw_num_rows($group_query) > 0){
			while($groupQryRes=imw_fetch_assoc($group_query)){
				$group_name = $groupQryRes['name'];
			}
		}
	}

	if(empty($sc_name)===false){ $arrFacilitySel = explode(',', $sc_name); }
	if(empty($Physician)===false){ $arrDoctorSel = explode(',', $Physician); }

	//WHERE PART
	if($Physician != ""){
		$wherePart .= " AND patChg.primary_provider_id_for_reports in($Physician)";
	}
	if(empty($str_crediting_phy)==false){
		$wherePart .= " AND patChg.secondaryProviderId in($str_crediting_phy)";
	}	
	if($chksamebillingcredittingproviders==1){
		$wherePart.= " and patChg.primary_provider_id_for_reports!=patChg.secondaryProviderId";							
	}	
	if($sc_name != ""){
		$wherePart .= " AND patChg.facility_id in($sc_name)";
	}
	if($grp_id != ""){
		$wherePart .= " AND patChg.gro_id in($grp_id)";
	}
	if($operator != ""){
		$wherePart .= " AND payChgDet.del_operator_id in($operator)";
	}
	//----GET GROUP BY ----------
	if($department != ""){
		$groupBy = "department";
	}else if($operator != ""){
		$groupBy = "operator";
	}else{
		$groupBy = "physician";
	}
	$showFacCol = false;
	if($sc_name != ""){
		$showFacCol = true;
	}
	//--------------------------
	$orderBy='';

	if($groupBy=='physician' || $groupBy=='operator'){ $orderBy = 'users.lname, users.fname'; }
	$orderBy = ($groupBy=='facility') ? 'pos_facilityies_tbl.facilityPracCode' : $orderBy;
	if(empty($orderBy)===false){
		$orderByPart = ' ORDER BY '.$orderBy.', pd.lname, pd.fname, patChg.del_status ASC';
	}

//--------------- DELETED PAYMENTS -------------
	//--- GET POSTED PAYMENT
	$qry = "SELECT patChg.patient_id, 
	patChg.facility_id,
	patChg.primary_provider_id_for_reports as 'primaryProviderId', payChg.creditCardCo,
	DATE_FORMAT(patChg.date_of_service, '".get_sql_date_format()."') as 'date_of_service',
	DATE_FORMAT(payChgDet.deleteDate, '".get_sql_date_format()."') as 'deleteDate',
	patChg.del_status,
	payChg.encounter_id, 
	payChgDet.charge_list_detail_id, 
	payChg.transaction_date, 
	payChg.payment_mode, 
	payChgDet.paidBy, 
	payChgDet.paidForProc, 
	payChgDet.overPayment, 
	payChgDet.del_operator_id, 
	payChgDet.deletePayment,
	payChg.paymentClaims, 
	pd.fname, 
	pd.mname, 
	pd.lname,
	patChgDet.procCode	
	FROM patient_chargesheet_payment_info payChg 
	JOIN patient_charges_detail_payment_info payChgDet ON payChgDet.payment_id = payChg.payment_id 
	JOIN patient_charge_list patChg ON patChg.encounter_id = payChg.encounter_id
	JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_detail_id = payChgDet.charge_list_detail_id";
	if($groupBy == "physician"){
		$qry.=" LEFT JOIN users ON users.id = patChg.primary_provider_id_for_reports";
	}else if($groupBy == "operator"){
		$qry.=" LEFT JOIN users ON users.id = payChgDet.del_operator_id";
	}
	$qry.=" 
		JOIN patient_data pd ON pd.id = patChg.patient_id 
		LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = patChg.facility_id 
		WHERE 1=1";
	$qry.=" AND payChgDet.deletePayment='1' AND (deleteDate BETWEEN '$startDate' AND '$endDate')";
	if(empty($pay_method)==false){ //PAYMENT MODE
		$qry.=" AND LOWER(payChg.payment_mode)='".$pay_method."'";
	}
	if(empty($cc_type)==false){ //IF CREDIT CARD
		$qry.=" AND LOWER(payChg.creditCardCo) IN(".$cc_type.")";
	}
	
	$qry.= $wherePart.$orderByPart;

	$rs = imw_query($qry)or die(imw_error());
	$tempDelPostedPay=array();
	$arrDelAmounts=array();
	$arrDelTemp=array();
	while($res = imw_fetch_array($rs)){
		$eid = $res['encounter_id'];
		
		$printFile=true;
		$paidAmt=0;
		$pid = $res['patient_id'];

		$chgDetId = $res['charge_list_detail_id'];
		$paidBy = strtolower($res['paidBy']);
		$payMode = strtolower($res['payment_mode']);
		$payMode = str_replace(' ', '_', $payMode);
		$phyId = $res['primaryProviderId'];
		$facId = $res['facility_id'];
		$oprId = $res['del_operator_id'];
		$opName = $operatorNameArr[$oprId];
		$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
		$encounterIdDelArr[$eid] = $eid;
		$deleteDate = $res['deleteDate'];
		
		$grpId = $phyId;
		$grpId = ($groupBy=='operator') ? $oprId : $grpId; 
		
		$paidAmt = $res['paidForProc'] + $res['overPayment'];
		if($res['paymentClaims'] == 'Negative Payment'){
			$paidAmt= '-'.$paidAmt;
		}
		
		$cptCode = $arrAllCPTCodes[$res['procCode']];
		$cptCode = (strlen($cptCode)>8) ? substr($cptCode,0, 8).'...' : $cptCode;
		
		if($showFacCol){
				$arrDelPostedAmounts[$grpId][$facId][$eid]['pat_name']=$patName;
				$arrDelPostedAmounts[$grpId][$facId][$eid]['eid']=$eid;
				$arrDelPostedAmounts[$grpId][$facId][$eid]['dos']=$res['date_of_service'];
				$arrDelPostedAmounts[$grpId][$facId][$eid]['del_amount']+= $paidAmt;
				$arrDelPostedAmounts[$grpId][$facId][$eid]['opr']= $opName;
				$arrDelPostedAmounts[$grpId][$facId][$eid]['delDate']= $deleteDate;
				$arrDelPostedAmounts[$grpId][$facId][$eid]['cpt']= $cptCode;
			}else{
				$arrDelPostedAmounts[$grpId][$eid]['pat_name']=$patName;
				$arrDelPostedAmounts[$grpId][$eid]['eid']=$eid;
				$arrDelPostedAmounts[$grpId][$eid]['dos']=$res['date_of_service'];
				$arrDelPostedAmounts[$grpId][$eid]['del_amount']+= $paidAmt;
				$arrDelPostedAmounts[$grpId][$eid]['opr']= $opName;
				$arrDelPostedAmounts[$grpId][$eid]['delDate']= $deleteDate;
				$arrDelPostedAmounts[$grpId][$eid]['cpt']= $cptCode;
			}
		} 
	unset($rs);
	//END DELETED PRE-PAYMENTS

		
	if($printFile==true){
		$page_content='';

		//--- PAGE HEADER DATA ---
		$dateRangeFor=strtoupper($DateRangeFor);
		$curDate = date(phpDateFormat().' H:i A');
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = $op_name_arr[1][0];
		$op_name .= $op_name_arr[0][0];

		$facilitySelected='All';
		$doctorSelected='All';
		if(sizeof($arrFacilitySel)>0){
			$facilitySelected = (sizeof($arrFacilitySel)>1) ? 'Multi' : $posFacilityArr[$sc_name];  
		}
		if(sizeof($arrDoctorSel)>0){
			$doctorSelected = (sizeof($arrDoctorSel)>1) ? 'Multi' : $providerNameArr[$Physician];  
		}
		
		$sel_pay_method= ucwords($pay_method);
		$sel_cc_type= ucwords($sel_cc_type);		
		if($sel_pay_method=='')$sel_pay_method='All';
		if($sel_cc_type=='')$sel_cc_type='All';		

		require_once(dirname(__FILE__)."/deleted_payments_view.php");
		
		if(trim($page_content) != ''){				
			$html_page_content = '';
			$html_page_content.='
				<page backtop="13mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					'.$mainHeaderPDF.'
				</page_header>
				'.$pdf_content.'
				</page>';
			$op='l';
			
			//--- CSV FILE DATA --
$page_content = <<<DATA
	<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
		<tr >
			<td class="rptbx1" style="width:260px;">
				Deleted Payments
			</td>
			<td class="rptbx2" style="width:260px;">
				Selected Group: $sel_grp
			</td>
			<td class="rptbx3" style="width:260px;">
				Selected Date: $Start_date to $End_date
			</td>
			<td class="rptbx1" style="width:260px;">
				Created by: $op_name on $curDate
			</td>
		</tr>	
		<tr>
			<td class="rptbx1" style="width:260px;">
				Selected Facility: $sel_fac
			</td>
			<td class="rptbx2" style="width:260px;">
				Selected Physician: $sel_phy
			</td>
			<td class="rptbx3" style="width:260px;">
				Selected Operator: $sel_opr
			</td>
			<td class="rptbx1" style="width:260px;">
			</td>
		</tr>					
	</table>
	$page_content
DATA;
		}
	}
	$conditionChk = true;
}
$HTMLCreated=0;
if($printFile == true and $page_content != ''){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$page_content;
	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$page_content;
	$file_location = write_html($strHTML);
	
	if($output_option=='view' || $output_option=='output_csv'){
		echo $csv_file_data;	
	} else {
		echo '<div class="text-center alert alert-info">Please Check PDF.</div>';
	}
}else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>
