<?php
ini_set("memory_limit","3072M");

//CHECK FOR PRIVILEGED FACILITIES
if(sizeof($sc_name)<=0 && isPosFacGroupEnabled()){
	$sc_name = $CLSReports->getFacilityName('', '0', 'array');

	if(sizeof($sc_name)<=0){
		$sc_name[0]='NULL';
	}
}

$sel_ins_grp = $CLSReports->report_display_selected($insuranceGrp,'insurance_group',1,$insGrp_cnt);
$sel_cpt = $CLSReports->report_display_selected(str_replace('none','',$cpt_code_id),'cpt_code',1,$cpt_cnt);
$sel_fac = $CLSReports->report_display_selected($sc_name,'facility',1,$facility_cnt);
$sel_dx = $CLSReports->report_display_selected($dx_code,'dx_code',1,$dx_cnt);
$sel_phy = $CLSReports->report_display_selected(implode(',',$phyId),'physician',1,$phy_cnt);
$sel_cr_phy = $CLSReports->report_display_selected(implode(',',$crediting_provider),'physician',1,$allCrPhyCount);
$sel_grp = $CLSReports->report_display_selected($grp_id, 'group','1', $grp_cnt);
$sel_ref_phy = $CLSReports->report_display_selected($id_reff_physician,'ref_phy',1,$ref_phy_cnt);

if(in_array(strtolower($billing_global_server_name), array('eyephysicianslancaster'))){
	$ptUTColumn = ':';
} else{
	$ptUTColumn = '/';	
}
//eyephysicianslancaster
if(empty($insuranceName)==false){
	$tempInsArr[] = $insuranceName; 
	$insuranceName = implode(',', $insuranceName);
}
if(empty($insuranceGrp)==false){
	$tempInsArr[] = $insuranceGrp; 
	$insuranceGrp = implode(',', $insuranceGrp);
}
$tempSelIns = implode(',', $tempInsArr);
$tempInsArr = array();
if(empty($tempSelIns)==false){
	$tempInsArr = explode(',', $tempSelIns);
}
$tempInsArr = array_unique($tempInsArr);

if(empty($phyId)==false){
	$phyId = join(',', $phyId);
}
if(empty($crediting_provider)==false){
	$crediting_physician = join(',', $crediting_provider);
}
if(empty($sc_name)==false){
	$sc_name = join(',', $sc_name);
}
if(empty($grp_id)==false){
	$grp_id = join(',', $grp_id);
}
if(empty($cpt_code_id)==false){
	$cpt_code_id = join(',', $cpt_code_id);
}
if(empty($dx_code10)==false){
	$dx_code10 = join(',', $dx_code10);
}

$sel_ins = $CLSReports->report_display_selected($insuranceName,'insurance',1,$insurance_cnt);
$curDate = date(''.$phpDateFormat.' H:i A');
$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$op_name = $op_name_arr[1][0];
$op_name .= $op_name_arr[0][0];

$printFile = true;
if(empty($form_submitted) === false){
	
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

	//--- CHANGE DATE FORMAT ----
	$start_date = getDateFormatDB($Start_date);
	$end_date = getDateFormatDB($End_date);
	$facility_ids = $sc_name;

	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname FROM users order by lname asc");
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		$providerNameArr[$id] = $pro_name;
	}unset($rs);
	//function to sort ref phy ids in last name asc order
	function sortRefPhy($ids)
	{
		if($ids)
		{
			$ids=implode(',',$ids);
			$qry=imw_query("Select physician_Reffer_id as id FROM refferphysician where physician_Reffer_id IN($ids) order by LastName asc");
			while($res = imw_fetch_array($qry))$refPhyId[]=$res['id'];
			//$qry.close;
		}	
		return $refPhyId;
	}
	//function to sort phy ids in last name asc order
	function sortPhy($phyIds)
	{
		if($phyIds)
		{
			$phyIds=implode(',',$phyIds);
			$rs=imw_query("Select id FROM users where id IN($phyIds) order by lname asc");
			while($res=imw_fetch_array($rs)){
				$providerIDArr[] = $res['id'];
			}
			return $providerIDArr;
		}
	}	
	
	function sortFac($facIds){
		if($facIds){
			$facIds=implode(',',$facIds);
			$rs=imw_query("Select pos_facility_id FROM pos_facilityies_tbl WHERE pos_facility_id IN(".$facIds.") ORDER BY facilityPracCode ASC");
			while($res=imw_fetch_array($rs)){
				$providerIDArr[] = $res['pos_facility_id'];
			}
			return $providerIDArr;
		}
	}
	function sortCPT($cptIds){
		if($cptIds){
			$cptIds=implode(',',$cptIds);
			$rs=imw_query("Select cpt_fee_id FROM cpt_fee_tbl WHERE cpt_fee_id IN(".$cptIds.") ORDER BY cpt_prac_code ASC");
			while($res=imw_fetch_array($rs)){
				$providerIDArr[] = $res['cpt_fee_id'];
			}
			return $providerIDArr;
		}
	}			
	//--- GET ALL RESULTS FROM MAIN ACCOUNTING TABLE ---
	$mainQryRes = $CLSReports->__getChargeListDetails($start_date,$end_date,$id_reff_physician,$cpt_code_id,$facility_ids,$dx_code,$insuranceName,$encounter_type,$insuranceGrp,$grp_id,$phyId,$dx_code10,$phy_type,$crediting_physician,$chksamebillingcredittingproviders);
	$procCodeArr = array();
	$ref_phy_id_arr = array();
	$facility_id_arr = array();
	$encounter_arr = array();
	$mainResArr = array();
	$detail_id_arr = array();
	$grand_total_amt = array();
	
	$arrAllPatients=array();
	$arrInitialPatDos=array();
	
	//LOGIC: IF INITIAL SELECTED THEN FETCH ENCOUNTER OF PATIENT IF THAT IS FIRST ENCOUNTER OF THAT PATIENT
	if($encounter_type == "initial"){
		//GET ALL PATIENT IDS
		for($i=0;$i<count($mainQryRes);$i++){
			$patid = trim($mainQryRes[$i]['patient_id']);
			$arrAllPatients[$patid]=$patid;
		}
		//GET FIRST ENCOUNTER OF EVERY PATIENT
		if(sizeof($arrAllPatients)>0){
			$strAllPatients=implode(',', $arrAllPatients);
			$qry="Select patient_id, encounter_id, date_of_service FROM patient_charge_list WHERE patient_id IN(".$strAllPatients.")";
			$qry.=" ORDER BY date_of_service DESC, charge_list_id DESC";
			
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$arrInitialPatDos[$res['patient_id']]=$res['date_of_service'];
				$arrInitialPatEnc[$res['patient_id']]=$res['encounter_id'];
			}unset($rs);
		}
	}

	//MAKING GROUPING OF ARRAY
	for($i=0;$i<count($mainQryRes);$i++){
		$makeData=1;
		$patid = $mainQryRes[$i]['patient_id'];
		$encounter_id = $mainQryRes[$i]['encounter_id'];
		
		//IF INITIAL SELECTED
		if($encounter_type=="initial"){
			if(($arrInitialPatDos[$patid]>=$start_date && $arrInitialPatDos[$patid]<=$end_date) && $arrInitialPatEnc[$patid]==$encounter_id){
				$makeData=1;
			}else{
				$makeData=0;
			}
		}
		
		if($makeData==1){
			$encounter_id = $mainQryRes[$i]['encounter_id'];
			$procCode = trim($mainQryRes[$i]['procCode']);
			$doctor_id=$mainQryRes[$i]['primaryProviderId'];
			
			if(empty($phyId) == true && empty($crediting_physician) === false){
				$doctor_id=$mainQryRes[$i]['secondaryProviderId'];
			}							
			
			$facility_id = $mainQryRes[$i]['facility_id'];
			$charge_list_detail_id = $mainQryRes[$i]['charge_list_detail_id'];
	
			$firstGroupId = $phy_id = $doctor_id;
			$secGroupId  = $reff_phy_id = (trim($phy_type) == 'prf')? $mainQryRes[$i]['primary_care_id']: $mainQryRes[$i]['reff_phy_id'];
			if($groupby=='Facility'){
				$firstGroupId = $mainQryRes[$i]['facility_id'];
			}else if($groupby=='CPT'){
				$firstGroupId = $mainQryRes[$i]['procCode'];
			}	
		
			if($groupby=='DX'){
				for($j=1; $j<=12; $j++){
					if($mainQryRes[$i]['diagnosis_id'.$j]!=''){
						$firstGroupId = $mainQryRes[$i]['diagnosis_id'.$j];
						$ref_phy_id_arr[$reff_phy_id] = $reff_phy_id;
						$facility_id_arr[$facility_id] = $facility_id;
						$procCodeArr[$procCode] = $procCode;
						$encounter_arr[$encounter_id] = $encounter_id;
						$detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResArr[$firstGroupId][$secGroupId][$encounter_id][] = $mainQryRes[$i];
					}
				}
			}else{
				$ref_phy_id_arr[$reff_phy_id] = $reff_phy_id;
				$facility_id_arr[$facility_id] = $facility_id;
				$procCodeArr[$procCode] = $procCode;
				$encounter_arr[$encounter_id] = $encounter_id;
				$detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResArr[$firstGroupId][$secGroupId][$encounter_id][] = $mainQryRes[$i];
			}
		}
	}

	$printFile = false;
	if(count($mainResArr)>0){
		$printFile = true;
		$procCodeArr = $CLSReports->__removeBlankValues($procCodeArr);
		$procCodeStr = join(',',$procCodeArr);
		$ref_phy_id_arr = $CLSReports->__removeBlankValues($ref_phy_id_arr);
		$ref_phy_id_str = join(',',$ref_phy_id_arr);
		$detail_id_str = join(',',$detail_id_arr);
		$encounter_str = join(',',$encounter_arr);
		
		//--- GET ALL PAYMENTS DATA ----
		$payQryRes = $CLSReports->__getPaymentDetails($encounter_str);
		$paymentDetailArr = array();
		for($i=0;$i<count($payQryRes);$i++){
			$paidForProc=0;
			$detail_id = $payQryRes[$i]['charge_list_detail_id'];
			$encounter_id = $payQryRes[$i]['encounter_id'];
			$paid_by = ucfirst(trim($payQryRes[$i]['paid_by']));
			$paidForProc=$payQryRes[$i]['paidForProc'];
			
			if($payQryRes[$i]['paymentClaims']=='Negative Payment'){
				$paidForProc='-'.$paidForProc;
			}
			if($detail_id > 0){
				$paymentDetailArr[$detail_id][$paid_by][] = $paidForProc;
			}
			else{
				$paymentDetailArr['COPAY'][$encounter_id][$paid_by][] = $paidForProc;
			}
		}
		
		//--- GET TOTAL WRITE OFF AMOUNTS ---
		$writeOffRes = array();
		$writeOff = $CLSReports->__getWriteOffAmount($encounter_str);
		while($write_off_qry_res = imw_fetch_assoc($writeOff)){
			$writeOffRes[] = $write_off_qry_res;
		}
		$writeOffDetailsArr = array();
		for($i=0;$i<count($writeOffRes);$i++){
			$detail_id = $writeOffRes[$i]['charge_list_detail_id'];
			$writeOffDetailsArr[$detail_id][] = $writeOffRes[$i]['write_off_amount'];
		}
		
		//--- GET DEDUCT AMOUNTS ----
		$deductAmtRes = $CLSReports->__getDeductableAmount($detail_id_str);
		$deductAmtArr = array();
		for($i=0;$i<count($deductAmtRes);$i++){
			$detail_id = $deductAmtRes[$i]['charge_list_detail_id'];
			$deduct_amount = $deductAmtRes[$i]['deduct_amount'];
			$deductAmtArr[$detail_id][] = $deduct_amount;
		}
		
		//--- GET REFERRING PHYSICIAN DETAILS ----
		$ref_phy_arr = array();
		$ref_phy_commnet_arr = array();
		$ref_phy_address_arr=array();
		$qry="Select physician_Reffer_id as refphyId, Title as refPhyTitle,	FirstName as refphyFName,LastName as refphyLName, 
		MiddleName as refPhyMname,physician_fax,physician_fax, comments, Address1, Address2, ZipCode, City, State FROM refferphysician";
		if(empty($ref_phy_id_str)==false){
			$qry.= " WHERE physician_Reffer_id IN(".$ref_phy_id_str.")";
		}
		$qry.=" order by refphyLName asc";
		$rs=imw_query($qry);		
		while($res = imw_fetch_array($rs)){
			$phy_id = $res['refphyId'];
			$comments = $res['comments'];

			$phy_name=core_name_format($res['refphyLName'], $res['refphyFName'], $res['refPhyMname'],'','', 'no'); // "no" means "dot" will not display with middle name.

			//ADDRESS
			$arr1=$arr2=array();
			$arr1[]=$res['Address1'];
			$arr1[]=$res['Address2'];
			$arr2[]=$res['City'];
			$arr2[]=$res['State'];
			$arr2[]=$res['ZipCode'];
			$arr1=array_filter($arr1);
			$arr2=array_filter($arr2);

			$ref_phy_address_arr[$phy_id]['address_part1']=implode(', ', $arr1);
			$ref_phy_address_arr[$phy_id]['address_part2']=implode(', ', $arr2);

			$ref_phy_arr[$phy_id] = $phy_name;
			$ref_phy_commnet_arr[$phy_id] = $comments;
		}unset($rs);
		
		//--- GET PATIENT REFERRING PHYSICIAN DETAILS ----
		$pt_ref_phy_arr = array();
		$qryPtRef="Select physician_Reffer_id as refphyId, Title as refPhyTitle, FirstName as refphyFName,LastName as refphyLName, 
		MiddleName as refPhyMname,physician_fax,physician_fax, comments FROM refferphysician order by refphyLName asc";
		$rs=imw_query($qryPtRef);	
		while($res = imw_fetch_array($rs)){
			$pt_phy_name_arr = array();
			$refphyId = $res['refphyId'];
			$pt_phy_name_arr["LAST_NAME"] = $res['refphyLName'];
			$pt_phy_name_arr["FIRST_NAME"] = $res['refphyFName'];
			$pt_phy_name_arr["MIDDLE_NAME"] = $res['refPhyMname'];
			$pt_phy_name = changeNameFormat($pt_phy_name_arr);
			$pt_ref_phy_arr[$refphyId] = $pt_phy_name;
		}unset($rs);

		// -- GET ALL FACILITIES NAMES
		if(sizeof($facility_id_arr)>0){
			$arrAllFacilities=array();
			$facility_id_str = implode(',',$facility_id_arr);
			$qry = "select pos_facilityies_tbl.facilityPracCode as name,
						pos_facilityies_tbl.pos_facility_id as id,
						pos_tbl.pos_prac_code
						from pos_facilityies_tbl
						left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id WHERE pos_facilityies_tbl.pos_facility_id IN(".$facility_id_str.")";
			$qryRs = imw_query($qry);
			while($qryRes  =imw_fetch_array($qryRs)){
				$id = $qryRes['id'];
				$name = $qryRes['name'];
				$pos_prac_code = $qryRes['pos_prac_code'];
				$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
			}
		}

		//--- GET PROCEDURE DETAILS ----
		$qry= "Select cpt_fee_id,cpt_desc,cpt4_code,cpt_prac_code from cpt_fee_tbl where cpt_fee_id in ($procCodeStr)";
		$rs=imw_query($qry);
		$procCodeArr = array();
		$cptPracCodeArr=array();
		while($res=imw_fetch_array($rs)){
			$cpt_fee_id = $res['cpt_fee_id'];
			$cpt4_code = $res['cpt4_code'];
			$procCodeArr[$cpt_fee_id] = $cpt4_code;
			$cptPracCodeArr[$cpt_fee_id] = $res['cpt_prac_code'];
		}unset($rs);


		// MAKE HTML
		$total_cols = 10;
		$first_col = "15";
		$w_cols = $w_cols1 = floor((100 - ($first_col))/($total_cols-1));
		$first_col = $first_col1 = 100 - (($total_cols-1) * $w_cols);
		$w_cols = $w_cols."%";
		$first_col = $first_col."%";
		
		$header_data = '';
		$header_data ='
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			 <tr class="rpt_headers">
				<td class="rptbx1" align="center" style="width:210px;">Referring Physician Report</td>
				<td class="rptbx2" align="center" style="width:210px;">Selected Referring Phy: '.$sel_ref_phy.'</td>
				<td class="rptbx3" align="center" style="width:210px;">DOS: '.$Start_date.' To '.$End_date.'</td>
				<td class="rptbx2" align="center" style="width:210px;">Provider: '.$sel_phy.'&nbsp;&nbsp;&nbsp;&nbsp;Provider: '.$sel_cr_phy.'</td>
				<td class="rptbx1" align="center" style="width:210px;">Created by '.$op_name.' on '.$curDate.'</td>										
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1" align="center" style="width:210px;">Selected Group: '.$sel_grp.' </td>
				<td class="rptbx2" align="center" style="width:210px;">Selected Facility: '.$sel_fac.' </td>
				<td class="rptbx3" align="center" style="width:210px;">Selected CPT Code: '.$sel_cpt.'</td>
				<td class="rptbx2" align="center" style="width:210px;">Selected DX Code: '.$sel_dx.'</td>
				<td class="rptbx1" align="center" style="width:210px;">Selected Insurance Company: '.$sel_ins.'</td>
			</tr>
		</table>';

	//MAKING OUTPUT DATA
	$file_name="referring_physician.csv";
	$csv_file_name= write_html("", $file_name);

	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');

	$arr=array();
	$arr[]="Referring Physician Report";
	$arr[]="Selected Referring Phy:" .$sel_ref_phy;
	$arr[]="DOS :" .$Start_date." To :" .$End_date;
	$arr[]="Selected Provider:" .$sel_phy;
	$arr[]="Selected Crediting Provider:" .$sel_cr_phy;
	$arr[]="Created by" .$op_name." on" .$curDate;
	
	fputcsv($fp,$arr, ",","\"");
	$arr=array();
	$arr[]="Selected Group: ".$sel_grp;
	$arr[]="Selected Facility: ".$sel_fac;
	$arr[]="Selected CPT Code: ".$sel_cpt;
	$arr[]="Selected DX Code: ".$sel_dx;
	$arr[]="Selected Insurance Company: ".$sel_ins;
	fputcsv($fp,$arr, ",","\"");
		
		
		
		$first_id_arr = array_keys($mainResArr);
		//SORT ASC ORDER BY
		if($groupby=='Physician'){
			$first_id_arr=sortPhy($first_id_arr);
		}else if($groupby=='Facility'){
			$first_id_arr=sortFac($first_id_arr);
		}else if($groupby=='CPT'){
			$first_id_arr=sortCPT($first_id_arr);
		}else if($groupby=='DX'){
			sort($first_id_arr);
		}
		
		if($Process=='Summary'){
		$arr=array();
		if($groupby=='Physician'){
			$arr[]= 'Physician';
		}
		if($groupby=='CPT'){
			$arr[]= 'CPT Code';
		}
		if($groupby=='DX'){
			$arr[]= 'DX Code';
		}
			$arr[]="Referring Physician";
			$arr[]="Facility";
			$arr[]="PT";
			$arr[]="Units";
			$arr[]="Billed amount";
			$arr[]="Allowed amount";
			$arr[]="Primary Ins.";
			$arr[]="Ins. Paid";
			$arr[]="Pt. Paid";
			$arr[]="Deductible";
			$arr[]="Write Off";
			$arr[]="Comments";
			fputcsv($fp,$arr, ",","\"");
				
			for($i=0;$i<count($first_id_arr);$i++){
				$mainGrpTotArr=array();
				$firstGroupId = $first_id_arr[$i];
				$secGroupArr  = $mainResArr[$firstGroupId];
				$secGroupKeys = array_keys($secGroupArr);
				$firstGroupName = $providerNameArr[$firstGroupId];
				$firstGroupTitle='Physician';
				if($groupby=='Facility'){
					$firstGroupName = $arrAllFacilities[$firstGroupId];
					$firstGroupTitle='Facility';
				}else if($groupby=='CPT'){
					$firstGroupName = $cptPracCodeArr[$firstGroupId];
					$firstGroupTitle='CPT Code';
				}else if($groupby=='DX'){
					$firstGroupName = $firstGroupId;
					$firstGroupTitle='DX Code';
				}
				
				//CHECK IF PHYSICIAN AND REF. PHYSICIAN NAME SHOULD NOT SAME
				if($groupby=='Physician'){
					for($s=0; $s<count($secGroupKeys); $s++){
						if($firstGroupName == $ref_phy_arr[$secGroupKeys[$s]]){
							unset($secGroupKeys[$s]);
						}
					}
				}
				$secGroupKeys=array_values($secGroupKeys);
				//sort ref physician name in asc order
				$secGroupKeys=sortRefPhy($secGroupKeys);
				
				if(sizeof($secGroupKeys)>0){
					$proc_data .= '<tr><td class="text_b_w" style="text-align:left" colspan="12">'.$firstGroupTitle.': '.$firstGroupName.'</td></tr>';
					$procedure_data_arr = array();
					for($s=0; $s<count($secGroupKeys); $s++){
						$sub_total_amount=array();
						$secGroupId = $secGroupKeys[$s];
						$enc_detail_arr = $mainResArr[$firstGroupId][$secGroupId];
						$enc_detail_id = array_keys($enc_detail_arr);
						$refPhyName = $ref_phy_arr[$secGroupId];
						$arrRefPhyIdsForLabels[$secGroupId]=$secGroupId;

						$comments = $ref_phy_commnet_arr[$secGroupId];
						
						for($e=0;$e<count($enc_detail_id);$e++){
							$encounter_id = $enc_detail_id[$e];
							$detail_arr = $enc_detail_arr[$encounter_id];
							//pre($detail_arr);
							for($d=0;$d<count($detail_arr);$d++){
								$patId = trim($detail_arr[$d]['patient_id']);
								$procCode = trim($detail_arr[$d]['procCode']);
								$units = $detail_arr[$d]['units'];
								$procCharges = $detail_arr[$d]['procCharges'];
								$approvedAmt = $detail_arr[$d]['approvedAmt'];
								$procedure_data_arr[$procCode]['units'][] = $units;
								$procedure_data_arr[$procCode]['procCharges'][] = $procCharges;
								$procedure_data_arr[$procCode]['approvedAmt'][] = $approvedAmt;
								$list_detail_id = $detail_arr[$d]['charge_list_detail_id'];
								$coPayAdjustedAmount = $detail_arr[$d]['coPayAdjustedAmount'];
								$priInsCoId = trim($detail_arr[$d]['primaryInsuranceCoId']);
								$primaryInsuranceName = $ins_comp_arr[$priInsCoId];
								$facility_id = $arrAllFacilities[$detail_arr[$d]['facility_id']];
								
								//--- PATIENT PAYMENTS ---
								$pat_payment_arr = array();
								if(count($paymentDetailArr[$list_detail_id]['Patient'])>0){
									$pat_payment_arr[] = array_sum($paymentDetailArr[$list_detail_id]['Patient']);
								}
								
								//--- INSURANCE PAYMENTS ---
								$ins_payment_arr = array();
								if(count($paymentDetailArr[$list_detail_id]['Insurance'])>0){
									$ins_payment_arr[] = array_sum($paymentDetailArr[$list_detail_id]['Insurance']);
								}
								
								//--- COPAY PAYMENTS ----
								if($coPayAdjustedAmount > 0){
									if(count($paymentDetailArr['COPAY'][$encounter_id]['Patient'])){
										$pat_payment_arr[] = array_sum($paymentDetailArr['COPAY'][$encounter_id]['Patient']);
									}
									if(count($paymentDetailArr['COPAY'][$encounter_id]['Insurance'])){
										$ins_payment_arr[] = array_sum($paymentDetailArr['COPAY'][$encounter_id]['Insurance']);
									}
								}		
								$pat_payments = array_sum($pat_payment_arr);
								$ins_payment = array_sum($ins_payment_arr);
								
								//--- DEDUCT AMOUNT ---
								$deduct_amount = 0;
								if(count($deductAmtArr[$list_detail_id]) > 0){
									$deduct_amount = array_sum($deductAmtArr[$list_detail_id]);
								}
								
								//--- WRITE OFF AMOUNT ---					
								$writeOfAmtArr = array();
								$writeOfAmtArr[] = $detail_arr[$d]['write_off'];
								if(count($writeOffDetailsArr[$list_detail_id])>0){
									$writeOfAmtArr[] = array_sum($writeOffDetailsArr[$list_detail_id]);
								}
								$writeOfAmt = array_sum($writeOfAmtArr);
								
								//--- SUB TOTAL AMOUNTS ---
								$sub_total_amount['units']+= $units;
								$sub_total_amount['patients'][$patId]= $patId;
								$sub_total_amount['procCharges']+= $procCharges;
								$sub_total_amount['approvedAmt']+= $approvedAmt;
								$sub_total_amount['ins_payment']+= $ins_payment;
								$sub_total_amount['pat_payments']+= $pat_payments;				
								$sub_total_amount['deduct_amount']+= $deduct_amount;
								$sub_total_amount['writeOfAmt']+= $writeOfAmt;

								if(!$temp[$list_detail_id]){
									$grand_total_amt['units']+= $units; 
									$grand_total_amt['procCharges']+= $procCharges;
									$grand_total_amt['approvedAmt']+= $approvedAmt;
									$grand_total_amt['ins_payment']+= $ins_payment;
									$grand_total_amt['pat_payments']+= $pat_payments;				
									$grand_total_amt['deduct_amount']+= $deduct_amount;
									$grand_total_amt['writeOfAmt']+= $writeOfAmt;																
									$temp[$list_detail_id]=$list_detail_id;
								}
								$grand_total_amt['patients'][$patId]= $patId; 
							}
						}
		
						//--- GET SUB TOTAL ROW ---------
						$subUnits = $sub_total_amount['units'];
						$subPatients = count($sub_total_amount['patients']);
						$subProcCharges = $sub_total_amount['procCharges'];
						$subApprovedAmt = $sub_total_amount['approvedAmt'];
						$sub_ins_payment = $sub_total_amount['ins_payment'];
						$sub_pat_payments = $sub_total_amount['pat_payments'];			
						$sub_deduct_amount = $sub_total_amount['deduct_amount'];
						$subWriteOfAmt = $sub_total_amount['writeOfAmt'];
			
						//--- MAIN GROUP TOTAL ---------
						$mainGrpTotArr['units']+= $subUnits;
						$mainGrpTotArr['patients']+= $subPatients;
						$mainGrpTotArr['procCharges']+= $subProcCharges;
						$mainGrpTotArr['approvedAmt']+= $subApprovedAmt;
						$mainGrpTotArr['ins_payment']+= $sub_ins_payment;
						$mainGrpTotArr['pat_payments']+= $sub_pat_payments;
						$mainGrpTotArr['deduct_amount']+= $sub_deduct_amount;			
						$mainGrpTotArr['writeOfAmt']+= $subWriteOfAmt;
						
						$proc_data .='
						<tr bgcolor="#FFFFFF">
							<td width="" class="text_10" style="text-align:left; word-wrap:break-word">'.$refPhyName.'</td>
							<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.wordwrap($facility_id, 15, "<br>\n", true).'&nbsp;</td>
							<td width="" class="text_10" style="text-align:center; word-wrap:break-word">\''.$subPatients.$ptUTColumn.$subUnits.'\'</td>
							<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($subProcCharges,2).'&nbsp;</td>
							<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($subApprovedAmt,2).'&nbsp;</td>
							<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.wordwrap($primaryInsuranceName, 12, "<br>\n", true).'&nbsp;</td>
							<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($sub_ins_payment,2).'&nbsp;</td>
							<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($sub_pat_payments,2).'&nbsp;</td>
							<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($sub_deduct_amount,2).'&nbsp;</td>
							<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($subWriteOfAmt,2).'&nbsp;</td>
							<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.wordwrap($comments, 12, "<br>\n", true).'&nbsp;</td>
						</tr>';	
						
						
							//FOR CSV
							$arr=array();
							
							if($groupby=='Physician' || $groupby=='CPT' || $groupby=='DX'){
								$arr[]=$firstGroupName;
							}
							$arr[]=$refPhyName;
							$arr[]=$facility_id;
							$arr[]=$subPatients;
							$arr[]=$subUnits;
							$arr[]=$CLSReports->numberFormat($subProcCharges,2);
							$arr[]=$CLSReports->numberFormat($subApprovedAmt,2);
							$arr[]=$primaryInsuranceName;
							$arr[]=$CLSReports->numberFormat($sub_ins_payment,2);
							$arr[]=$CLSReports->numberFormat($sub_pat_payments,2);
							$arr[]=$CLSReports->numberFormat($sub_deduct_amount,2);
							$arr[]=$CLSReports->numberFormat($subWriteOfAmt,2);
							$arr[]=$comments;
							fputcsv($fp,$arr, ",","\"");
						}
		
					//FIRST GROUP TOTAL
/*					$grand_total_amt['units']+= $mainGrpTotArr['units']; 
					$grand_total_amt['patients']+= $mainGrpTotArr['patients']; 
					$grand_total_amt['procCharges']+= $mainGrpTotArr['procCharges'];
					$grand_total_amt['approvedAmt']+= $mainGrpTotArr['approvedAmt'];
					$grand_total_amt['ins_payment']+= $mainGrpTotArr['ins_payment'];
					$grand_total_amt['pat_payments']+= $mainGrpTotArr['pat_payments'];			
					$grand_total_amt['deduct_amount']+= $mainGrpTotArr['deduct_amount'];
					$grand_total_amt['writeOfAmt']+= $mainGrpTotArr['writeOfAmt'];*/
					
					$proc_data .= '
						<tr><td class="total-row" colspan="11"></td></tr>
						<tr bgcolor="#FFFFFF">
						<td class="text_10b" style="text-align:left; word-wrap:break-word">'.$firstGroupTitle.' Total:</td>
						<td class="text_10b" style="text-align:center; word-wrap:break-word"></td>
						<td class="text_10b" style="text-align:center; word-wrap:break-word">\''.$mainGrpTotArr['patients'].$ptUTColumn.$mainGrpTotArr['units'].'\'</td>
						<td class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['procCharges'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['approvedAmt'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; word-wrap:break-word"></td>
						<td class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['ins_payment'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['pat_payments'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['deduct_amount'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['writeOfAmt'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; word-wrap:break-word"></td>
					</tr>
					<tr><td class="total-row" colspan="12"></td></tr>';
				}
			}

			//GRAND HTML FOR SUMMARY
			if(is_array($grand_total_amt) && count($grand_total_amt) > 0){
				$proc_data .= '
				<tr><td colspan="11" class="total-row"></td></tr>					
				<tr bgcolor="#FFFFFF">
					<td class="text_10b" style="text-align:left">Grand Total</td>
					<td class="text_10b" style="text-align:right"></td>
					<td class="text_10b" style="text-align:center">\''.count($grand_total_amt['patients']).$ptUTColumn.$grand_total_amt['units'].'\'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['procCharges'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['approvedAmt'],2).'</td>
					<td class="text_10b" style="text-align:right"></td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['ins_payment'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['pat_payments'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['deduct_amount'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['writeOfAmt'],2).'</td>
					<td class="text_10b" style="text-align:right"></td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>';
				
				$total_PT = count($grand_total_amt['patients']);
				$total_units = $grand_total_amt['units'];
				//FOR CSV
				$arr=array();
				$arr[]="Grand Total";
				if($groupby=='Physician' || $groupby=='CPT' || $groupby=='DX'){
				$arr[]="";
				}
				$arr[]="";
				$arr[]=$total_PT;
				$arr[]=$total_units;
				$arr[]=$CLSReports->numberFormat($grand_total_amt['procCharges'],2);
				$arr[]=$CLSReports->numberFormat($grand_total_amt['approvedAmt'],2);
				$arr[]="";
				$arr[]=$CLSReports->numberFormat($grand_total_amt['ins_payment'],2);
				$arr[]=$CLSReports->numberFormat($grand_total_amt['pat_payments'],2);
				$arr[]=$CLSReports->numberFormat($grand_total_amt['deduct_amount'],2);
				$arr[]=$CLSReports->numberFormat($grand_total_amt['writeOfAmt'],2);
				$arr[]="";
				fputcsv($fp,$arr, ",","\"");
				
			}
			
			$page_content1 .=
			'<tr>
				<td class="text_b_w" align="center" width="130">Referring Physician</td>
				<td class="text_b_w"  width="100" style="text-align:right">Facility</td>
				<td class="text_b_w" width="40" align="center">PT'.$ptUTColumn.'Units</td>
				<td class="text_b_w" width="90" style="text-align:right">Billed amount&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Allowed amount&nbsp;</td>
				<td class="text_b_w" width="100" style="text-align:right">Primary Ins.&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Ins. Paid&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Pt. Paid&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Deductible&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Write Off&nbsp;</td>
				<td class="text_b_w" width="100" style="text-align:right">Comments&nbsp;</td>
			</tr>';			
	
			$page_content = '<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">';
			$page_content .= $page_content1;
			$page_content .= $proc_data.'</table>';
		
			$csv_page_content1 .= 
			'<tr>
				<td class="text_b_w" align="center" width="130">Referring Physician</td>
				<td class="text_b_w"  width="100" style="text-align:right">Facility</td>
				<td class="text_b_w" width="40" align="center">PT'.$ptUTColumn.'Units</td>
				<td class="text_b_w" width="90" style="text-align:right">Billed amount&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Allowed amount&nbsp;</td>
				<td class="text_b_w" width="100" style="text-align:right">Primary Ins.&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Ins. Paid&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Pt. Paid&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Deductible&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Write Off&nbsp;</td>
				<td class="text_b_w" width="100" style="text-align:right">Comments&nbsp;</td>
			</tr>';		
			$csv_page_content = $header_data.'<table class="rpt_table rpt rpt_table-bordered rpt_padding">';
			$csv_page_content .= $csv_page_content1;
			$csv_page_content .= $proc_data.'</table>';			
		}else{
			//DETAIL
			$arr=array();
			if($groupby=='Physician'){
			$arr[]= 'Physician';
			}
			if($groupby=='CPT'){
				$arr[]= 'CPT Code';
			}
			$arr[]="Patient";
			$arr[]="Facility";
			$arr[]="DOS";
			$arr[]="Primary Care Physician";
			$arr[]="Co-Managed Physician";
			$arr[]="Referring Physician";
			$arr[]="Billed amount";
			$arr[]="Allowed amount";
			$arr[]="DX Codes";
			$arr[]="Primary Ins.";
			$arr[]="Ins. Paid";
			$arr[]="Pt. Paid";
			$arr[]="Deductible";
			$arr[]="Write Off";
			fputcsv($fp,$arr, ",","\"");
			
			for($i=0;$i<count($first_id_arr);$i++){
				$mainGrpTotArr=array();
				$firstGroupId = $first_id_arr[$i];
				$secGroupArr  = $mainResArr[$firstGroupId];
				$secGroupKeys = array_keys($secGroupArr);
				$firstGroupName = $providerNameArr[$firstGroupId];
				$firstGroupTitle='Physician';
				if($groupby=='Facility'){
					$firstGroupName = $arrAllFacilities[$firstGroupId];
					$firstGroupTitle='Facility';
				}else if($groupby=='CPT'){
					$firstGroupName = $cptPracCodeArr[$firstGroupId];
					$firstGroupTitle='CPT Code';
				}else if($groupby=='DX'){
					$firstGroupName = $firstGroupId;
					$firstGroupTitle='DX Code';
				}
	
				//CHECK IF PHYSICIAN AND REF. PHYSICIAN NAME SHOULD NOT SAME
				if($groupby=='Physician'){
					for($s=0; $s<count($secGroupKeys); $s++){
						if($firstGroupName == $ref_phy_arr[$secGroupKeys[$s]]){
							unset($secGroupKeys[$s]);
						}
					}
				}
				$secGroupKeys=array_values($secGroupKeys);
				//sort ref physician name in asc order
				$secGroupKeys=sortRefPhy($secGroupKeys);
				
				if(sizeof($secGroupKeys)>0){
					$proc_data .= '<tr><td class="text_b_w" style="text-align:left" colspan="14">'.$firstGroupTitle.': '.$firstGroupName.'</td></tr>';
					$pfd_proc_data .= '<tr><td class="text_b_w" style="text-align:left" colspan="11">'.$firstGroupTitle.': '.$firstGroupName.'</td></tr>';
					$procedure_data_arr = array();
				
					for($s=0; $s<count($secGroupKeys); $s++){
						$sub_total_amount=array();
						$secGroupId = $secGroupKeys[$s];
						$enc_detail_arr = $mainResArr[$firstGroupId][$secGroupId];
						$enc_detail_id = array_keys($enc_detail_arr);
						$refPhyName = $ref_phy_arr[$secGroupId];
						$comments = $ref_phy_commnet_arr[$secGroupId];
						$proc_data .= '<tr><td class="text_b_w" style="text-align:left" colspan="7">Referring Physician: '.$refPhyName.'</td><td class="text_b_w" style="text-align:left" colspan="7">Comment: '.$comments.'</td></tr>';
						$pdf_proc_data .= '<tr><td class="text_b_w" style="text-align:left" colspan="5">Referring Physician: '.$refPhyName.'</td><td class="text_b_w" style="text-align:left" colspan="6">Comment: '.$comments.'</td></tr>';
						$ptCountArr = $ptUniqueArr = array();
						for($e=0;$e<count($enc_detail_id);$e++){
							$arrPatDetails = array();
							$diagnosisIdArr = array();
							$encounter_id = $enc_detail_id[$e];
							$detail_arr = $enc_detail_arr[$encounter_id];
							
								for($d=0;$d<count($detail_arr);$d++){
								$diagnosis_id1 = $diagnosis_id2 = $diagnosis_id3 = $diagnosis_id4 = $diagnosis_id5 = $diagnosis_id6 = $diagnosis_id7 = $diagnosis_id8 = $diagnosis_id9 = 
								$diagnosis_id10 = $diagnosis_id11 = $diagnosis_id12 = "";
								
								$patId = trim($detail_arr[$d]['patient_id']);
								$procCode = trim($detail_arr[$d]['procCode']);
								$procCharges = $detail_arr[$d]['procCharges'];
								$approvedAmt = $detail_arr[$d]['approvedAmt'];
								$procedure_data_arr[$procCode]['procCharges'][] = $procCharges;
								$procedure_data_arr[$procCode]['approvedAmt'][] = $approvedAmt;
								$list_detail_id = $detail_arr[$d]['charge_list_detail_id'];
								$coPayAdjustedAmount = $detail_arr[$d]['coPayAdjustedAmount'];
								$priInsCoId = trim($detail_arr[$d]['primaryInsuranceCoId']);
								$primaryInsuranceName = $ins_comp_arr[$priInsCoId];
								
								$facility_id = $arrAllFacilities[$detail_arr[$d]['facility_id']];

								$diagnosis_id1 = $detail_arr[$d]['diagnosis_id1'];
								$diagnosis_id2 = $detail_arr[$d]['diagnosis_id2'];
								$diagnosis_id3 = $detail_arr[$d]['diagnosis_id3'];
								$diagnosis_id4 = $detail_arr[$d]['diagnosis_id4'];
								$diagnosis_id5 = $detail_arr[$d]['diagnosis_id5'];
								$diagnosis_id6 = $detail_arr[$d]['diagnosis_id6'];
								$diagnosis_id7 = $detail_arr[$d]['diagnosis_id7'];
								$diagnosis_id8 = $detail_arr[$d]['diagnosis_id8'];
								$diagnosis_id9 = $detail_arr[$d]['diagnosis_id9'];
								$diagnosis_id10 = $detail_arr[$d]['diagnosis_id10'];
								$diagnosis_id11 = $detail_arr[$d]['diagnosis_id11'];
								$diagnosis_id12 = $detail_arr[$d]['diagnosis_id12'];

								$diagnosisIdArr[$diagnosis_id1] = $diagnosis_id1;
								$diagnosisIdArr[$diagnosis_id2] = $diagnosis_id2;
								$diagnosisIdArr[$diagnosis_id3] = $diagnosis_id3;
								$diagnosisIdArr[$diagnosis_id4] = $diagnosis_id4;
								$diagnosisIdArr[$diagnosis_id5] = $diagnosis_id5;
								$diagnosisIdArr[$diagnosis_id6] = $diagnosis_id6;
								$diagnosisIdArr[$diagnosis_id7] = $diagnosis_id7;
								$diagnosisIdArr[$diagnosis_id8] = $diagnosis_id8;
								$diagnosisIdArr[$diagnosis_id9] = $diagnosis_id9;
								$diagnosisIdArr[$diagnosis_id10] = $diagnosis_id10;
								$diagnosisIdArr[$diagnosis_id11] = $diagnosis_id11;
								$diagnosisIdArr[$diagnosis_id12] = $diagnosis_id12;

								$diagnosisIdArr = array_unique($diagnosisIdArr);
								sort($diagnosisIdArr);
								if($diagnosisIdArr[0] == ''){
									array_shift($diagnosisIdArr);
								}
								$diagnosisId = join(', ',$diagnosisIdArr);
								
								//--- PATIENT PAYMENTS ---
								$pat_payment_arr = array();
								if(count($paymentDetailArr[$list_detail_id]['Patient'])>0){
									$pat_payment_arr[] = array_sum($paymentDetailArr[$list_detail_id]['Patient']);
								}
								
								//--- INSURANCE PAYMENTS ---
								$ins_payment_arr = array();
								if(count($paymentDetailArr[$list_detail_id]['Insurance'])>0){
									$ins_payment_arr[] = array_sum($paymentDetailArr[$list_detail_id]['Insurance']);
								}
								
								//--- COPAY PAYMENTS ----
								if($coPayAdjustedAmount > 0){
									if(count($paymentDetailArr['COPAY'][$encounter_id]['Patient'])){
										$pat_payment_arr[] = array_sum($paymentDetailArr['COPAY'][$encounter_id]['Patient']);
									}
									if(count($paymentDetailArr['COPAY'][$encounter_id]['Insurance'])){
										$ins_payment_arr[] = array_sum($paymentDetailArr['COPAY'][$encounter_id]['Insurance']);
									}
								}		
								
								$pat_payments = array_sum($pat_payment_arr);
								$ins_payment = array_sum($ins_payment_arr);
								
								//--- DEDUCT AMOUNT ---
								$deduct_amount = 0;
								if(count($deductAmtArr[$list_detail_id]) > 0){
									$deduct_amount = array_sum($deductAmtArr[$list_detail_id]);
								}
								
								//--- WRITE OFF AMOUNT ---					
								$writeOfAmtArr = array();
								$writeOfAmtArr[] = $detail_arr[$d]['write_off'];
								if(count($writeOffDetailsArr[$list_detail_id])>0){
									$writeOfAmtArr[] = array_sum($writeOffDetailsArr[$list_detail_id]);
								}
								$writeOfAmt = array_sum($writeOfAmtArr);
	
								$arrPatDetails['procCharges']+= $procCharges;
								$arrPatDetails['approvedAmt']+= $approvedAmt;
								$arrPatDetails['ins_payment']+= $ins_payment;
								$arrPatDetails['pat_payments']+= $pat_payments;				
								$arrPatDetails['deduct_amount']+= $deduct_amount;
								$arrPatDetails['writeOfAmt']+= $writeOfAmt;

								//--- SUB TOTAL AMOUNTS ---
								$sub_total_amount['procCharges']+= $procCharges;
								$sub_total_amount['approvedAmt']+= $approvedAmt;
								$sub_total_amount['ins_payment']+= $ins_payment;
								$sub_total_amount['pat_payments']+= $pat_payments;				
								$sub_total_amount['deduct_amount']+= $deduct_amount;
								$sub_total_amount['writeOfAmt']+= $writeOfAmt;

								if(!$temp[$list_detail_id]){
									$grand_total_amt['procCharges']+= $procCharges;
									$grand_total_amt['approvedAmt']+= $approvedAmt;
									$grand_total_amt['ins_payment']+= $ins_payment;
									$grand_total_amt['pat_payments']+= $pat_payments;				
									$grand_total_amt['deduct_amount']+= $deduct_amount;
									$grand_total_amt['writeOfAmt']+= $writeOfAmt;	
									$temp[$list_detail_id]=$list_detail_id;
								}
								$pt_pcp = $detail_arr[$d]['primary_care_phy_id'];
								$pt_cmp = $detail_arr[$d]['co_man_phy_id'];
								$pt_rp = $detail_arr[$d]['primary_care_id'];		
								$pt_pcp_data = $pt_ref_phy_arr[$pt_pcp];
								$pt_cmp_data = $pt_ref_phy_arr[$pt_cmp];
								$pt_rp_data = $pt_ref_phy_arr[$pt_rp];
							}
							
							$patient_name_arr = array();
							$patient_name_arr["LAST_NAME"] = $detail_arr[0]['lname'];
							$patient_name_arr["FIRST_NAME"] = $detail_arr[0]['fname'];
							$patient_name_arr["MIDDLE_NAME"] = $detail_arr[0]['mname'];		
							$patient_name = changeNameFormat($patient_name_arr);
							
							$pt_name = $patient_name.' - '.$patId;
							$ptCountArr[$encounter_id] = $patId;
								
							$date = date_create($detail_arr[0]['date_of_service']);
							$dosDate = date_format($date,"m/d/Y");
							
							$proc_data .='
							<tr bgcolor="#FFFFFF">
								<td width="" class="text_10" style="text-align:left; word-wrap:break-word">'.wordwrap($pt_name, 28, "<br>\n", true).'</td>
								<td width="" class="text_10" style="text-align:center; word-wrap:break-word">'.wordwrap($facility_id, 15, "<br>\n", true).'</td>
								<td width="" class="text_10" style="text-align:center; word-wrap:break-word">'.$dosDate.'</td>
								<td width="" class="text_10" style="text-align:center; word-wrap:break-word">'.$pt_pcp_data.'</td>
								<td width="" class="text_10" style="text-align:center; word-wrap:break-word">'.$pt_cmp_data.'</td>
								<td width="" class="text_10" style="text-align:center; word-wrap:break-word">'.$pt_rp_data.'</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['procCharges'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['approvedAmt'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.wordwrap($diagnosisId, 12, "<br>\n", true).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.wordwrap($primaryInsuranceName, 10, "<br>\n", true).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['ins_payment'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['pat_payments'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right;  word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['deduct_amount'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['writeOfAmt'],2).'&nbsp;</td>
							</tr>';
							
							$pdf_proc_data .='
							<tr bgcolor="#FFFFFF">
								<td width="" class="text_10" style="text-align:left; word-wrap:break-word">'.wordwrap($pt_name, 28, "<br>\n", true).'</td>
								<td width="" class="text_10" style="text-align:center; word-wrap:break-word">'.wordwrap($facility_id, 15, "<br>\n", true).'</td>
								<td width="" class="text_10" style="text-align:center; word-wrap:break-word">'.$dosDate.'</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['procCharges'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['approvedAmt'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.wordwrap($diagnosisId, 12, "<br>\n", true).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.wordwrap($primaryInsuranceName, 10, "<br>\n", true).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['ins_payment'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['pat_payments'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right;  word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['deduct_amount'],2).'&nbsp;</td>
								<td width="" class="text_10" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($arrPatDetails['writeOfAmt'],2).'&nbsp;</td>
							</tr>';
							
							
							
							$arr=array();
							if($groupby=='Physician' || $groupby=='CPT'){
							$arr[]= $firstGroupName;
							}
							$arr[]=$pt_name;
							$arr[]=$facility_id;
							$arr[]=$dosDate;
							$arr[]=$pt_pcp_data;
							$arr[]=$pt_cmp_data;
							$arr[]=$pt_rp_data;
							$arr[]=$CLSReports->numberFormat($arrPatDetails['procCharges'],2);
							$arr[]=$CLSReports->numberFormat($arrPatDetails['approvedAmt'],2);
							$arr[]=$diagnosisId;
							$arr[]=$primaryInsuranceName;
							$arr[]=$CLSReports->numberFormat($arrPatDetails['ins_payment'],2);
							$arr[]=$CLSReports->numberFormat($arrPatDetails['pat_payments'],2);
							$arr[]=$CLSReports->numberFormat($arrPatDetails['deduct_amount'],2);
							$arr[]=$CLSReports->numberFormat($arrPatDetails['writeOfAmt'],2);
							fputcsv($fp,$arr, ",","\"");
						}
						// 	pre($ptCountArr);
						
						$totalPtCount = $uniquePtCount = '';
						if(count($ptCountArr) > 0){
							$totalPtCount = count(array_keys($ptCountArr));
							$uniquePtCount = count(array_unique(array_values($ptCountArr)));
						}
						
						//--- GET SUB TOTAL ROW ---------
						$subPatients = count($sub_total_amount['patients']);
						$subProcCharges = $sub_total_amount['procCharges'];
						$subApprovedAmt = $sub_total_amount['approvedAmt'];
						$sub_ins_payment = $sub_total_amount['ins_payment'];
						$sub_pat_payments = $sub_total_amount['pat_payments'];			
						$sub_deduct_amount = $sub_total_amount['deduct_amount'];
						$subWriteOfAmt = $sub_total_amount['writeOfAmt'];
			
						//--- MAIN GROUP TOTAL ---------
						$mainGrpTotArr['patients']+= $subPatients;
						$mainGrpTotArr['procCharges']+= $subProcCharges;
						$mainGrpTotArr['approvedAmt']+= $subApprovedAmt;
						$mainGrpTotArr['ins_payment']+= $sub_ins_payment;
						$mainGrpTotArr['pat_payments']+= $sub_pat_payments;
						$mainGrpTotArr['deduct_amount']+= $sub_deduct_amount;			
						$mainGrpTotArr['writeOfAmt']+= $subWriteOfAmt;
						
						$proc_data .='
						<tr><td class="total-row" colspan="14"></td></tr>
						<tr bgcolor="#FFFFFF">
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word" colspan="6">Ref. Phyisian Total:&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($subProcCharges,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($subApprovedAmt,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word"></td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word"></td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($sub_ins_payment,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($sub_pat_payments,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($sub_deduct_amount,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($subWriteOfAmt,2).'&nbsp;</td>
						</tr>
						<tr><td class="total-row" colspan="14"></td></tr>
						<tr>
						<td width="" class="text_10b" style="text-align:left; word-wrap:break-word" colspan="14">
							Unique Patients: '.$uniquePtCount.' / Total Visits: '.$totalPtCount.'
						</td>
						</tr>
						<tr><td class="total-row" colspan="14"></td></tr><tr><td colspan="14">&nbsp;</td></tr>';
						
						$pdf_proc_data .='
						<tr><td class="total-row" colspan="11"></td></tr>
						<tr bgcolor="#FFFFFF">
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word" colspan="3">Ref. Phyisian Total:&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($subProcCharges,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($subApprovedAmt,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word"></td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word"></td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($sub_ins_payment,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($sub_pat_payments,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($sub_deduct_amount,2).'&nbsp;</td>
							<td width="" class="text_10b" style="text-align:right; word-wrap:break-word">'.$CLSReports->numberFormat($subWriteOfAmt,2).'&nbsp;</td>
						</tr>
						<tr><td class="total-row" colspan="11"></td></tr>
						<tr>
						<td width="" class="text_10b" style="text-align:left; word-wrap:break-word" colspan="11">
							Unique Patients: '.$uniquePtCount.' / Total Visits: '.$totalPtCount.'
						</td>
						</tr>
						<tr><td class="total-row" colspan="11"></td></tr><tr><td colspan="11">&nbsp;</td></tr>';
					}
		
					//FIRST GROUP TOTAL
					/*$grand_total_amt['patients']+= $mainGrpTotArr['patients']; 
					$grand_total_amt['procCharges']+= $mainGrpTotArr['procCharges'];
					$grand_total_amt['approvedAmt']+= $mainGrpTotArr['approvedAmt'];
					$grand_total_amt['ins_payment']+= $mainGrpTotArr['ins_payment'];
					$grand_total_amt['pat_payments']+= $mainGrpTotArr['pat_payments'];			
					$grand_total_amt['deduct_amount']+= $mainGrpTotArr['deduct_amount'];
					$grand_total_amt['writeOfAmt']+= $mainGrpTotArr['writeOfAmt'];*/
					
					$proc_data .= '
					<tr><td class="total-row" colspan="14"></td></tr>
					<tr bgcolor="#FFFFFF">
						<td class="text_10b" style="text-align:right; width:'.$first_col.'; word-wrap:break-word" colspan="6">'.$firstGroupTitle.' Total:&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['procCharges'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['approvedAmt'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word"></td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word"></td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['ins_payment'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['pat_payments'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['deduct_amount'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-wordt">'.$CLSReports->numberFormat($mainGrpTotArr['writeOfAmt'],2).'&nbsp;</td>
					</tr>
					<tr><td class="total-row" colspan="14"></td></tr>';
					
					$pdf_proc_data .= '
					<tr><td class="total-row" colspan="11"></td></tr>
					<tr bgcolor="#FFFFFF">
						<td class="text_10b" style="text-align:right; width:'.$first_col.'; word-wrap:break-word" colspan="3">'.$firstGroupTitle.' Total:&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['procCharges'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['approvedAmt'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word"></td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word"></td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['ins_payment'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['pat_payments'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-word">'.$CLSReports->numberFormat($mainGrpTotArr['deduct_amount'],2).'&nbsp;</td>
						<td class="text_10b" style="text-align:right; width:'.$w_cols.'; word-wrap:break-wordt">'.$CLSReports->numberFormat($mainGrpTotArr['writeOfAmt'],2).'&nbsp;</td>
					</tr>
					<tr><td class="total-row" colspan="11"></td></tr>';
					
				}
			}	

			
			if(is_array($grand_total_amt) && count($grand_total_amt) > 0){
				//GRAND HTML FOR DETAIL
				$proc_data .= '
				<tr><td colspan="14" class="total-row"></td></tr>					
				<tr bgcolor="#FFFFFF">
					<td class="text_10b" style="text-align:right" colspan="6">Grand Total:&nbsp;</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['procCharges'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['approvedAmt'],2).'</td>
					<td class="text_10b" style="text-align:right"></td>
					<td class="text_10b" style="text-align:right"></td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['ins_payment'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['pat_payments'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['deduct_amount'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['writeOfAmt'],2).'</td>
				</tr>
				<tr><td colspan="14" class="total-row"></td></tr>';
				
				
				$pdf_proc_data .= '
				<tr><td colspan="11" class="total-row"></td></tr>					
				<tr bgcolor="#FFFFFF">
					<td class="text_10b" style="text-align:right" colspan="3">Grand Total:&nbsp;</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['procCharges'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['approvedAmt'],2).'</td>
					<td class="text_10b" style="text-align:right"></td>
					<td class="text_10b" style="text-align:right"></td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['ins_payment'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['pat_payments'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['deduct_amount'],2).'</td>
					<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($grand_total_amt['writeOfAmt'],2).'</td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>';
				
				
				
				
				$arr=array();
				$arr[]="Grand Total";
				if($groupby=='Physician' || $groupby=='CPT'){
				$arr[]="";
				}
				$arr[]="";
				$arr[]="";							
				$arr[]="";							
				$arr[]="";							
				$arr[]="";							
				$arr[]=$CLSReports->numberFormat($grand_total_amt['procCharges'],2);
				$arr[]=$CLSReports->numberFormat($grand_total_amt['approvedAmt'],2);
				$arr[]="";
				$arr[]="";
				$arr[]=$CLSReports->numberFormat($grand_total_amt['ins_payment'],2);
				$arr[]=$CLSReports->numberFormat($grand_total_amt['pat_payments'],2);
				$arr[]=$CLSReports->numberFormat($grand_total_amt['deduct_amount'],2);
				$arr[]=$CLSReports->numberFormat($grand_total_amt['writeOfAmt'],2);
				fputcsv($fp,$arr, ",","\"");
				
			}
			
			$page_content1 .=
			'<tr>
				<td class="text_b_w" align="center" style="width:110px">Patient</td>
				<td class="text_b_w" align="center" width="90">Facility</td>
				<td class="text_b_w" width="90" align="center">DOS</td>
				<td class="text_b_w" width="90" style="text-align:right">Billed amount&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Allowed amount&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">DX Codes</td>
				<td class="text_b_w" width="85" style="text-align:right">Primary Ins.&nbsp;</td>
				<td class="text_b_w" width="85" style="text-align:right">Ins. Paid&nbsp;</td>
				<td class="text_b_w" width="85" style="text-align:right">Pt. Paid&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Deductible&nbsp;</td>
				<td class="text_b_w" width="90" style="text-align:right">Write Off&nbsp;</td>
			</tr>';			
	
			$page_content = '<table class="rpt_table rpt rpt_table-bordered rpt_padding" tyle="width:100%">';
			$page_content .= $page_content1;
			$page_content .= $pdf_proc_data.'</table>';
		
			$csv_page_content1 .= 
			'<tr>
				<td class="text_b_w" align="center" style="width:120px">Patient</td>
				<td class="text_b_w" align="center" width="95">Facility</td>
				<td class="text_b_w" width="95" align="center">DOS</td>
				<td class="text_b_w" width="95" align="center">PCP</td>
				<td class="text_b_w" width="95" align="center">CMP</td>
				<td class="text_b_w" width="95" align="center">RP</td>
				<td class="text_b_w" width="95" style="text-align:right">Billed amount&nbsp;</td>
				<td class="text_b_w" width="95" style="text-align:right">Allowed amount&nbsp;</td>
				<td class="text_b_w" width="95" style="text-align:right">DX Codes</td>
				<td class="text_b_w" width="95" style="text-align:right">Primary Ins.&nbsp;</td>
				<td class="text_b_w" width="95" style="text-align:right">Ins. Paid&nbsp;</td>
				<td class="text_b_w" width="95" style="text-align:right">Pt. Paid&nbsp;</td>
				<td class="text_b_w" width="95" style="text-align:right">Deductible&nbsp;</td>
				<td class="text_b_w" width="95" style="text-align:right">Write Off&nbsp;</td>
			</tr>';		
			$csv_page_content = $header_data.'<table class="rpt_table rpt rpt_table-bordered rpt_padding">';
			$csv_page_content .= $csv_page_content1;
			$csv_page_content .= $proc_data.'</table>';					
		}
		//	END MAIN LOOP
				

		//PRINT FALSE IF NO HTML CREATED
		if(empty($proc_data)==true){
			$printFile = false;
		}
	}
}

fclose($fp);

//--- GET REFERRING PHYSICIAN NAME ---
$page_height = '5mm';
$header_data = $pdf_header_data = '';
$page_height = '5mm';

$pdf_header_data = <<<DATA
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
		<tr class="rpt_headers">
			<td class="rptbx1" align="center" style="width:180px;">Ref Phy Report</td>
			<td class="rptbx2" align="center" style="width:180px;">Sel Ref Phy: $sel_ref_phy</td>
			<td class="rptbx3" align="center" style="width:230px;">DOS: $Start_date To $End_date</td>
			<td class="rptbx2" align="center" style="width:180px;">Provider: $sel_phy &nbsp;&nbsp;&nbsp;&nbsp;Crediting Phy.: $sel_cr_phy</td>
			<td class="rptbx1" align="center" style="width:250px;">Created by $op_name on $curDate</td>										
		</tr>
		<tr class="rpt_headers">
			<td class="rptbx1" align="center" style="width:180px;">Sel Group: $sel_grp </td>
			<td class="rptbx2" align="center" style="width:180px;">Sel Facility: $sel_fac </td>
			<td class="rptbx3" align="center" style="width:230px;">Sel CPT Code: $sel_cpt</td>
			<td class="rptbx2" align="center" style="width:180px;">Sel DX Code: $sel_dx</td>
			<td class="rptbx1" align="center" style="width:250px;">Sel Ins Comp: $sel_ins</td>										
		</tr>
	</table>
DATA;


//MAKING ADDRESS LABELS OF REFERRING PHYSICIANS
$address_file_location='';
if(sizeof($arrRefPhyIdsForLabels)>0){
	//setting margins
	$sql_margin=imw_query("select * from create_margins where margin_type='recall'");
	$row_margin=imw_fetch_array($sql_margin);
	$top_margin = $row_margin['top_margin'];
	$bottom_margin = $row_margin['bottom_margin'];
	$line_margin = $row_margin['line_margin'];
	$coloumn_margin = $row_margin['column_margin'];

	$addressHTML = '
	<style>
		.tb_heading{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
			background-color:#FE8944;
		}
		.text_b{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#FFFFFF;
			background-color:#4684AB;
		}
		.text{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#FFFFFF;
		}
	</style>
	<page backtop="'.$top_margin.'mm" backbottom="5mm">
	<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';

	$i = 1;
	$j = 1;
	$num=sizeof($arrRefPhyIdsForLabels);	
	$width = "100%";


	foreach($arrRefPhyIdsForLabels as $phy_id){
		
		$address=$ref_phy_address_arr[$phy_id]['address_part1'];
		$address2=$ref_phy_address_arr[$phy_id]['address_part2'];

		if($address==''){
			$address="&nbsp;";
		}
		if($address2==''){
			$address2="&nbsp;";
		}
		
		$addressHTML .= '
				<td valign="top" width="'.$coloumn_margin.'" style="margion:0px;">
					<table align="left"  border="0" rules="rows"  cellpadding="2" cellspacing="0" width="'.$width.'">
						<tr>
							<td width="230" align="left" valign="middle" class="text_13b">'.$ref_phy_arr[$phy_id].'</TD>
						</tr>
						<tr><td height="'.$line_margin.'"></td></tr>
						<tr>
							<td width="230" valign="middle" align="left" class="text_13">';
				
						if($address <> ""){ 
							$addressHTML .= substr($address,0,30);
						}
						
						$addressHTML .= '
						</TD>
						</tr>
						<tr><td height="'.$line_margin.'"></td></tr>
						<TR>
							<td width="230" valign="middle" align="left" class="text_13">'.$address2.'</TD>
						</tr>
						<tr><td height="'.$line_margin.'"></td></tr>
					</table>
				</td>';
		$break = '';
		if($i%3 == 0){
			if($i%30 == 0){
				$break = '</tr><tr><td></td></tr><tr>';
			}else{
				$break = '</tr><tr><td height="'.$bottom_margin.'"></td></tr><tr>';
			}
		}
		if($j == $num){
			$break = "</tr>";
		}
		$addressHTML .= $break;
		$i++;
		$j++; 			
		
	}
	$addressHTML .= "</table></page>";
	
	$address_file_location = write_html($addressHTML, 'referring_physician_address_labels.html');
}

if($printFile == true && $page_content != ''){
	
	$csv_html_content = '<style>';
	$csv_html_content .= file_get_contents('css/reports_html.css');
	$csv_html_content .= '</style>';
	
	$csv_file_data = $csv_html_content.$csv_page_content;
	
	$html_content = '<style>';
	$html_content .= file_get_contents('css/reports_pdf.css');
	$html_content .= '</style>';
	
	$html_content .=<<<DATA
		<page backtop="9mm" backbottom="5mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>
		$pdf_header_data
		</page_header>
		$page_content
		</page>
DATA;
	$file_location = write_html($html_content);
	if(file_exists($file_location)){
		$size_bytes = filesize($file_location);
		$size_mb = $size_bytes/1048576;
	}
	if((isset($GLOBALS['rp_html_size']) && $size_mb > $GLOBALS['rp_html_size']) || (!isset($GLOBALS['rp_html_size']) && $size_mb > 3)){
		$tempDir = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/UserId_".$_SESSION['authId']."/tmp/";
		if(!is_dir($tempDir)){
			mkdir($tempDir,0777, true);
		}
		$file = $tempDir.'referring_physician';
		if(file_exists($file.'.csv')){
			$fParts=explode("/", $file);
			$zipName = $fParts[count($fParts)-1].'.zip';
			$zip = new ZipArchive();
			if ($zip->open($file.'.zip', ZipArchive::CREATE)!==TRUE) {
				exit("cannot open <$zipName>\n");
			}
			$zip->addFile($file.'.csv', 'referring_physician.csv');
			$zip->close();
			
			$createZip = 1;
			$file = $file.'.zip';
		}
		echo '<div class="text-center alert alert-info">Created zip file.</div>';
	} else{
		echo $csv_file_data;	
	}
} else {
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}

?>