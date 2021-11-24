<?php
$frame_cost = str_replace('$','',$frame_cost);
$txtframePrice = str_replace('$','',$txtframePrice);
$frameCost = str_replace(',','',$frame_cost) + str_replace(',','',$txtframePrice);
$lenese_cost = str_replace('$','',$lenese_cost);
//--- Discount For Frames ------
if($discount_frames){
	$first_val = substr($discount_frames,0,1);
	$last_val = substr($discount_frames,-1);
	$frameDiscount1 = str_replace('$','',$discount_frames);
	$frameDiscount1 = str_replace('%','',$frameDiscount1);
	if($first_val == '$'){		
		$frameCost = $frameCost - $frameDiscount1;
	}
	else{
		$frameCost = $frameCost - ($frameCost * $frameDiscount1/100);
	}
}
$frameCost = number_format($frameCost,2);
//--- Discount For Lens ------
if($discount){
	$first_val = substr($discount,0,1);
	$last_val = substr($discount,-1);
	$discount1 = str_replace('$','',$discount);
	$discount1 = str_replace('$','',$discount1);
	if($first_val == '$'){		
		$lenese_cost = $lenese_cost - $discount1;
	}
	else{
		$lenese_cost = $lenese_cost - ($lenese_cost * $discount1/100);
	}
}

$frameCost = number_format($frameCost,2);
$lenese_cost = number_format($lenese_cost,2);
$deposit = str_replace('$','',$deposit);
$enc_icd10=0;
if(date('Y-m-d')<'2015-10-01'){ //ICD9
	$dx_code = '367.1';
}else{ //ICD10
	$enc_icd10=1;
	$dx_code = 'H52.1-';
}

$proc_code = array();
$prisam = false;
if($elem_visMrOdP != '' || $elem_visMrOsP != '' || $elem_visMrOdSlash != '' || $elem_visMrOsSlash){
	$prisam = true;
}
switch($order_confirm){
	case 'Patient':
		switch($lens_opt){
			case 'Single Vision':
				$proc_code['V2100'] = $lenese_cost;
				$procUnit['V2100'] = 1;
			break;
			case 'Bifocal':
				$proc_code['V2203'] = $lenese_cost;
				$procUnit['V2203'] = 1;
			break;
			case 'Trifocal':
				$proc_code['2305'] = $lenese_cost;
				$procUnit['2305'] = 1;
			break;
			case 'Progressive':
				$proc_code['V2300'] = $lenese_cost;
				$procUnit['V2300'] = 1;
			break;
			case 'Deluxe Progressive':
				$proc_code['2781'] = $lenese_cost;
				$procUnit['2781'] = 1;
			break;
		}
		if($vendor_name != '' || $frameCost != ''){
			$proc_code['92395'] = $frameCost;
			$procUnit['92395'] = 1;
		}
		if($prisam == true || $prism_cost != ''){
			$proc_code['PRISM'] = $prism_cost;
			$procUnit['PRISM'] = 1;
		}
		if($Polaroid_material != '' || $polar_cost != ''){
			$proc_code['POLAROID'] = $polar_cost;
			$procUnit['POLAROID'] = 1;
		}
		if($trans_cost != '' || $trans_cost != ''){
			$proc_code['TRANS'] = $trans_cost;
			$procUnit['TRANS'] = 1;
		}
		if($slad_off != '' || $Slad_Off_cost != ''){
			$proc_code['SLAD-OFF'] = $Slad_Off_cost;
			$procUnit['SLAD-OFF'] = 1;
		}
		if($frame_scr != '' || $scr_cost != ''){
			$proc_code['SCRATCH COATING'] = $scr_cost;
			$procUnit['SCRATCH COATING'] = 1;
		}
		if($ar_charge != '' || $ar_cost != ''){
			$proc_code['A/R'] = $ar_cost;
			$procUnit['A/R'] = 1;
		}
		if($frame_uv != '' || $uv_cost != ''){
			$proc_code['UV'] = $uv_cost;
			$procUnit['UV'] = 1;
		}
		if($Photochromatic != '' || $Photochromatic_cost != ''){
			$proc_code['PHOTOCHROMATIC'] = $Photochromatic_cost;
			$procUnit['PHOTOCHROMATIC'] = 1;
		}
		if($hi_cost_price){
			$proc_code['MISC'] = $hi_cost_price;
			$procUnit['MISC'] = 1;
		}
	break;
	case 'Commercial':
		switch($lens_opt){
			case 'Single Vision':
				$proc_code['92340'] = $lenese_cost;
				$procUnit['92340'] = 1;
			break;
			case 'Bifocal':
				$proc_code['92341'] = $lenese_cost;
				$procUnit['92341'] = 1;
			break;
			case 'Trifocal':
				$proc_code['2305'] = $lenese_cost;
				$procUnit['2305'] = 1;
			break;
			case 'Progressive':
				$proc_code['92342'] = $lenese_cost;
				$procUnit['92342'] = 1;
			break;
			case 'Deluxe Progressive':
				$proc_code['92342'] = $lenese_cost;
				$procUnit['92342'] = 1;
			break;
		}		
		if($vendor_name != '' || $frameCost != ''){
			$proc_code['92395'] = $frameCost;
			$procUnit['92395'] = 1;
		}
		if($prisam == true || $prism_cost != ''){
			$proc_code['PRISM'] = $prism_cost;
			$procUnit['PRISM'] = 1;
		}
		if($trans_cost != '' || $trans_cost != ''){
			$proc_code['TRANS'] = $trans_cost;
			$procUnit['TRANS'] = 1;
		}
		if($Polaroid_material != '' || $polar_cost != ''){
			$proc_code['POLAROID'] = $polar_cost;
			$procUnit['POLAROID'] = 1;
		}
		if($slad_off != '' || $Slad_Off_cost != ''){
			$proc_code['SLAD-OFF'] = $Slad_Off_cost;
			$procUnit['SLAD-OFF'] = 1;
		}
		if($frame_scr != '' || $scr_cost != ''){
			$proc_code['SCRATCH COATING'] = $scr_cost;
			$procUnit['SCRATCH COATING'] = 1;
		}
		if($ar_charge != '' || $ar_cost != ''){
			$proc_code['A/R'] = $ar_cost;
			$procUnit['A/R'] = 1;
		}
		if($frame_uv != '' || $uv_cost != ''){
			$proc_code['UV'] = $uv_cost;
			$procUnit['UV'] = 1;
		}
		if($Photochromatic != '' || $Photochromatic_cost != ''){
			$proc_code['PHOTOCHROMATIC'] = $Photochromatic_cost;
			$procUnit['PHOTOCHROMATIC'] = 1;
		}
		if($hi_cost_price){
			$proc_code['MISC'] = $hi_cost_price;
			$procUnit['MISC'] = 1;
		}
	break;
	case 'Medicare':
		if(date('Y-m-d')<'2015-10-01'){
			$dx_code = 'V43.1';
		}else{
			$dx_code = 'Z96.1';
			$enc_icd10=1;
		}
		switch($lens_opt){
			case 'Single Vision':
				$proc_code['V2100'] = '';
				$procUnit['V2100'] = 2;
			break;
			case 'Bifocal':
				$proc_code['V2203'] = '';
				$procUnit['V2203'] = 2;
			break;
			case 'Trifocal':
				$proc_code['2305'] = '';
				$procUnit['2305'] = 2;
			break;
			case 'Progressive':
				$proc_code['V2300'] = '';
				$procUnit['V2300'] = 2;
			break;
			case 'Deluxe Progressive':
				$proc_code['2781'] = '';
				$procUnit['2781'] = 2;
			break;
		}
		
		if($vendor_name != '' || $frameCost != ''){			
			$proc_code['92395'] = $frameCost;
			$procUnit['92395'] = 1;
		}
		if($prisam == true || $prism_cost != ''){
			$proc_code['V2715'] = $prism_cost;
			$procUnit['V2715'] = 1;
		}
		if($Polaroid_material != '' || $polar_cost){
			$proc_code['Polaroid'] = $polar_cost;
			$procUnit['Polaroid'] = 1;
		}
		if($trans_cost != '' || $trans_cost != ''){
			$proc_code['V2744'] = $trans_cost;
			$procUnit['V2744'] = 1;
		}
		if($slad_off != '' || $Slad_Off_cost != ''){
			$proc_code['V2710'] = $Slad_Off_cost;
			$procUnit['V2710'] = 1;
		}
		if($frame_scr != '' || $scr_cost != ''){
			$proc_code['V2760'] = $scr_cost;
			$procUnit['V2760'] = 1;
		}
		if($ar_charge != '' || $ar_cost != ''){
			$proc_code['V2750'] = $ar_cost;
			$procUnit['V2750'] = 1;
		}
		if($frame_uv != '' || $uv_cost != ''){
			$proc_code['2755'] = $uv_cost;
			$procUnit['2755'] = 1;
		}
		if($Photochromatic != '' || $Photochromatic_cost != ''){
			$proc_code['V2744'] = $Photochromatic_cost;
			$procUnit['V2744'] = 1;
		}
		if($tint_cost_price){
			$proc_code['TINT'] = $tint_cost_price;
			$procUnit['TINT'] = 1;
		}
		if($hi_cost_price){
			$proc_code['HI'] = $hi_cost_price;
			$procUnit['HI'] = 1;
		}
	break;
}
//--- Get Procedure Code Id -------
$proc_code_str = '';
$proc_code_arr = array();

$proc_codes = array_keys($proc_code);
if(count($proc_codes) > 0){

	for($i=0;$i<count($proc_codes);$i++){
		$proc_code_arr[] .= "'".$proc_codes[$i]."'";
	}
	$proc_code_str = join(',',$proc_code_arr);
	$qry = imw_query("select cpt4_code,cpt_fee_id from cpt_fee_tbl 
			where (cpt4_code in($proc_code_str) 
			or cpt_prac_code in($proc_code_str) 
			or cpt_desc in ($proc_code_str)) AND delete_status = '0'");
	while($row = imw_fetch_array($qry)){
		$procQryRes[] = $row;
	}
	//$procQryRes = $objManageData->mysqlifetchData($qry);
	$procArr = array();
	$cpt_fee_id_arr = array();
	for($i=0;$i<count($procQryRes);$i++){
		$cpt4_code = $procQryRes[$i]['cpt4_code'];
		$cpt_fee_id = $procQryRes[$i]['cpt_fee_id'];
		$cpt_fee_id_arr[] = $procQryRes[$i]['cpt_fee_id'];
		$procArr[$cpt4_code] = $cpt_fee_id;
	}
	//--- Get Insurance Companies Details -------
	//$patient_id = $_SESSION['patient'];
	$qry = imw_query("select insurance_case.ins_caseid from insurance_case join insurance_case_types
			on insurance_case_types.case_id = insurance_case.ins_case_type 
			join insurance_data on insurance_data.ins_caseid = insurance_case.ins_caseid
			where insurance_case.patient_id = '$patient_id'
			and insurance_case.case_status = 'Open' 
			and insurance_data.provider > 0
			group by insurance_case.ins_caseid
			order by insurance_case.ins_case_type limit 0,1");

	$qryRes = imw_fetch_array($qry);
	$ins_caseid = $qryRes['ins_caseid'];	
	$InsComArr = array();
	$InsComArr['ins_caseid'] = $qryRes['ins_caseid'];
	$priInsDetail = $optical_obj->get_ins_details($patient_id,$ins_caseid,'primary');
	$InsComArr['primary'] = $priInsDetail->provider;
	$copay_amount = $priInsDetail->copay;
	$pri_copay = $priInsDetail->copay;
	$secondaryDetail = $optical_obj->get_ins_details($patient_id,$ins_caseid,'secondary');
	$InsComArr['secondary'] = $secondaryDetail->provider;
	$tertiaryDetail = $optical_obj->get_ins_details($patient_id,$ins_caseid,'tertiary');
	$InsComArr['tertiary'] = $tertiaryDetail->provider;	
	//--- Copay apply for procedures check --------
	$copay_collect = $optical_obj->copay_apply_chk($proc_code_str,$InsComArr['primary'],$InsComArr['secondary']);
		//--- Get Copay Collect from secondary and tertairy ---------
	$qry = imw_query("select secondary_copay,tertiary_copay  
			from copay_policies where policies_id = '1'");
	while($row = imw_fetch_array($qry)){
		$copayQryRes[] = $row;
	}
	//$copayQryRes = $objManageData->mysqlifetchData($qry);
	if(ucfirst($copayQryRes[0]['secondary_copay']) == 'Yes'){
		$copay_amount += preg_replace('/,/','',$secondaryDetail->copay);
		$sec_copay = $secondaryDetail->copay;
	}
	if(ucfirst($copayQryRes[0]['tertiary_copay']) == 'Yes'){
		//$copay_amount += preg_replace('/,/','',$tertiaryDetail->copay);
	}
	
	if($copay_collect==false){
		$copay_amount=0;
		$pri_copay=0;
		$sec_copay=0;
	}
		
	$total = str_replace('$','',$balance);
	$admit_date = date('Y-m-d');
	$date_of_service = date('Y-m-d');
	$approvedTotalAmt = $total;
	$patientAmt = $copay_amount;
	$insAmt = $total - $copay_amount;
	$totalBalance = $total;
	$insDueAmt = $total;
	$operator_id = $_SESSION['authId'];
	$primaryInsuranceCoId = $InsComArr['primary'];
	$secondaryInsuranceCoId = $InsComArr['secondary'];
	$tertiaryInsuranceCoId = $InsComArr['tertiary'];	
	//--- Get Provider Id ---
	$qry = imw_query("select providerId,default_facility from patient_data where id = '$patient_id'");
	$qryRes = imw_fetch_array($qry);
	$primaryProviderId = $qryRes['providerId'];
	$default_facility = $qryRes['default_facility'];
	//--- Get new Encounter Id ------------
	$new = false;
	$columnPart='';

	if(count($procArr) > 0){
		if(empty($encounter_id)){
			$encounter_id = $optical_obj->getEncounterId();
			$new = true;
		}
		if($new == true){
			$qry1 = "insert into ";
			$columnPart="date_of_service = '$date_of_service',";
		}
		else{
			$qry1 = "update ";
			$update = " where encounter_id = '$encounter_id'";
		}
		
		$sel_proc=imw_query("select default_group from users where default_group>0 and id='$primaryProviderId'");
		$proc_row=imw_fetch_array($sel_proc);
		$gro_id=$proc_row['default_group'];
			
		//--- Insert into patient charge list table --------
		$qry = $qry1." patient_charge_list set ".$columnPart." vipStatus = 'false',
				encounter_id = '$encounter_id',patient_id = '$patient_id',
				case_type_id = '$ins_caseid', facility_id= '$default_facility',
				primaryInsuranceCoId = '$primaryInsuranceCoId',
				secondaryInsuranceCoId = '$secondaryInsuranceCoId',
				tertiaryInsuranceCoId = '$tertiaryInsuranceCoId',
				primaryProviderId = '$primaryProviderId',
				secondaryProviderId = '0',tertiaryProviderId = '0',
				admit_date = '$admit_date',
				payment_status = 'pending',superbillFormId = 0,
				copay = '$copay_amount',copayPaid = '0',
				referactionPaid = '0',totalAmt = '$total',
				approvedTotalAmt = '$approvedTotalAmt',deductibleTotalAmt = '0',
				amtPaid = '0',amountDue = '$totalBalance',patientAmt = '$copay_amount',
				patientPaidAmt = '$deposit',insPaidAmt = '0',resPartyPaid = '0',
				insAmt = '$insAmt',insuranceDue = '$insDueAmt',
				patientDue = '$copay_amount',creditAmount = '0',
				totalBalance = '$totalBalance',lastPayment = '0',submitted = 'false',
				collection = 'false',postedAmount = '0',creditLessTotalBalance = '0',
				coPayNotRequired = '0',coPayRequired = '0',collectionAmount = '0',
				coPayAdjusted = '0',overPayment = '0',submit = '0',primarySubmit = '0',
				secondarySubmit = '0',tertairySubmit = '0',hcfaStatus = '0',
				coPayWriteOff = '0',reff_phy_id = '0',operator_id = '$operator_id',
				Re_submitted = 'false',statement_status  = '0',
				submitted_operator_id = '0',primary_paid = 'false',
				secondary_paid = 'false',tertiary_paid = 'false',
				statement_count = '0',pri_copay='$pri_copay',sec_copay='$sec_copay',
				enc_icd10='".$enc_icd10."',gro_id='$gro_id' $update";
		//print $qry.'<br>';
		imw_query($qry);
		$update1 = '';
		if($new == true){
			$charge_list_id = imw_insert_id();
		}
		else{
			$qry = imw_query("select charge_list_id from patient_charge_list
					where del_status='0' and encounter_id = '$encounter_id'");
			while($row = imw_fetch_array($qry)){
				$chargeQryRes[] = $row;
			}		
			//$chargeQryRes = $objManageData->mysqlifetchData($qry);
			$charge_list_id = $chargeQryRes[0]['charge_list_id'];
			$update1 = " where charge_list_id = '$charge_list_id'";
		}
		
		//--- Get Type of service --------
		if($order_confirm == 'Medicare'){
			$qry = imw_query("select tos_id from tos_tbl where tos_description = 'DME Purchase'");
		}
		else{
			$qry = imw_query("select tos_id from tos_tbl where tos_description = 'Medical care'");
		}
		while($row = imw_fetch_array($qry)){
			$tosQryRes[] = $row;
		}
		//$tosQryRes = $objManageData->mysqlifetchData($qry);
		$type_of_service = $tosQryRes[0]['tos_id'];

		//--- Get Place of service -------
		$place_of_service='';
		$qry = imw_query("select pos_id from pos_tbl where pos_prac_code = 'O'");
		$posQryRes = imw_fetch_array($qry);
		if(count($posQryRes) >0){
			$place_of_service = $posQryRes['pos_id'];
		}
		//--- Get POS default facility ----
		$pos_facility_id='';
		$qry = imw_query("select pos_facility_id from pos_facilityies_tbl
				where pos_id = '$place_of_service'
				and facilityPracCode = 'Toms River'");
		while($row = imw_fetch_array($qry)){
			$posFacilityQryRes[] = $row;
		}
		//$posFacilityQryRes = imw_fetch_array($qry);
		if($place_of_service!=''){
			$pos_facility_id = $posFacilityQryRes[0]['pos_facility_id'];
		}
		//--- Insert into patient charge list detail table --------
		for($i=0;$i<count($proc_codes);$i++){
			$proc_code_id = $procArr[$proc_codes[$i]];
			$procCharges = $proc_code[$proc_codes[$i]];
			$procCharges = str_replace("$",'',$procCharges);
			$units = $procUnit[$proc_codes[$i]];

			$start_date = date('Y-m-d');
			if($update1){
				$updateqry = imw_query("select charge_list_detail_id from patient_charge_list_details
							$update1 and del_status='0' and procCode = '$proc_code_id'");
				while($row = imw_fetch_array($updateqry)){
					$detailQryRes[] = $row;
				}
				//$detailQryRes = $objManageData->mysqlifetchData($updateqry);
				$charge_list_detail_id = $detailQryRes[0]['charge_list_detail_id'];
				if($charge_list_detail_id){
					$qry1 = 'update ';
					$update2 = " where charge_list_detail_id = '$charge_list_detail_id'";
				}
				else{
					$qry1 = 'insert into ';
					$update2 = '';
				}
			}

			if($proc_code_id != '' && $procCharges > 0){
				$qry = "$qry1 patient_charge_list_details set
						charge_list_id = '$charge_list_id', patient_id = '$patient_id',
						procCode = '$proc_code_id',start_date = '$start_date',
						type_of_service  = '$type_of_service',
						place_of_service = '$place_of_service',
						primaryProviderId = '$primaryProviderId',
						units = '$units', procCharges = '$procCharges',
						totalAmount = '$procCharges',paidForProc = '0',
						balForProc  = '$procCharges',diagnosis_id1 = '$dx_code',
						onset_type = 'ILL',onset_date = '$start_date',
						write_off = '0',total_write_off  = '0',
						approvedAmt = '$procCharges',deductAmt = '0',
						newBalance = '$procCharges',differ_insurance_bill = 'false',
						differ_patient_bill = 'false',creditLessBalance = '$procCharges',
						creditProcAmount = '0',coPayAdjustedAmount = '0',
						overPaymentForProc  = '0',posFacilityId = '$pos_facility_id',
						superBillUpdate = '0',claimDenied = '0',write_off_by = '0' $update2";				
				imw_query($qry);
			}
		}
		
		$arrDxCodes[1]=$dx_code;
		$str_dx_codes=serialize($arrDxCodes);
		$update_chl="update patient_charge_list set all_dx_codes='".imw_real_escape_string($str_dx_codes)."' 
		where charge_list_id='$charge_list_id'";
		$update_chl_run=imw_query($update_chl);		
		
		$qry = imw_query("update optical_order_form set encounter_id = '$encounter_id' 
						where Optical_Order_Form_id = '$order_id'");	
		//$objManageData->query($qry);	
	}
}
?>
<script type="text/javascript">
	function seltab(id,eid){
		//top.Navigation.changeSelTab('Accounting');
		var tab_name="Accounting";
		var filename = "../accounting/accounting_view.php";
		var redirect_url ="../accounting/accounting_view.php?patient="+id+"&rd2="+filename+"&front=yes&encounter_id="+eid;
		top.core_redirect_to(tab_name, redirect_url) ;
	}
	var id = '<?php print $patient_id; ?>';
	var eid = '<?php print $encounter_id; ?>';
	seltab(id,eid);
</script>