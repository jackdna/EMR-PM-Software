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

$arrFacilitySel=array();
$arrDoctorSel=array();
$arrInsSel=array();

$printFile = false;
if($_POST['form_submitted']){
	
	//--- CHANGE DATE FORMAT ----
	$startDate = getDateFormatDB($Start_date);
	$endDate   = getDateFormatDB($End_date);
	
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

	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname, pro_title, pro_suffix FROM users");	
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$title= ($res['pro_suffix']!='')? $res['pro_suffix'].' ' : $res['pro_title'].' ';
		$pro_name = core_name_format($res['lname'], $res['fname'], $res['mname']);		

		$providerNameArr[$id] = $title.$pro_name;
	}

	//--- GET GROUP NAME ---
	$group_name = "All Groups Selected";
	$arrAllGroups=array();
	$group_query = imw_query("select gro_id, name from groups_new");
	while($groupQryRes = imw_fetch_array($group_query)){
		$group_name = $groupQryRes['name'];
		$arrAllGroups[$groupQryRes['gro_id']]= $groupQryRes['name'];
	}

	//GET ALL CPT PRACTICE CODES
	$arrAllCPTCodes[0]='No CPT';
	$qry="Select cpt_fee_id, cpt_prac_code, cpt_desc FROM cpt_fee_tbl";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllCPTCodes[$res['cpt_fee_id']]['code'] = $res['cpt_prac_code'];
		$arrAllCPTCodes[$res['cpt_fee_id']]['desc'] = $res['cpt_desc'];
	}
	
	//GET ALL ADJUSTMENT CODES
	$arrAllAdjCodes=array();
	$qry="Select a_id, a_code, a_desc FROM adj_code";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllAdjCodes[$res['a_id']]['code'] = $res['a_code'];
		$arrAllAdjCodes[$res['a_id']]['desc'] = $res['a_desc'];
	}

	//GET ALL DISCOUNT CODES
	$arrAllDisCodes=array();		
	$qry="Select d_id,d_code,d_desc FROM discount_code";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllDisCodes[$res['d_id']]['code'] = $res['d_code'];
		$arrAllDisCodes[$res['d_id']]['desc'] = $res['d_desc'];
	}

	//GET ALL Write-off CODES	
	$arrAllWriteCodes=array();	
	$qry="Select w_id,w_code,w_desc FROM write_off_code";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllWriteCodes[$res['w_id']]['code'] = $res['w_code'];
		$arrAllWriteCodes[$res['w_id']]['desc'] = $res['w_desc'];
	}

	//GET ALL Modifiers
	$arrAllModifiers=array();	
	$qry="Select modifiers_id, mod_prac_code FROM modifiers_tbl";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllModifiers[$res['modifiers_id']] = $res['mod_prac_code'];
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
	
	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
		$facility_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_name[0]='NULL';
		}
	}	
	
	$arrFacilitySel=$facility_name;
	$arrDoctorSel=$Physician;
	$arrInsSel=$insuranceName;
	if(sizeof($groups)>0){ $grp_id = implode(',', $groups); }
	if(sizeof($facility_name)>0){ $sc_name = implode(',', $facility_name); }
	if(sizeof($Physician)>0){ $Physician = implode(',', $Physician); }
	if(sizeof($insuranceName)>0){ $insuranceName = implode(',', $insuranceName); }

	//---------------------------------------START DATA --------------------------------------------
	$qry = "Select main.patient_id, main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id, main.sec_ins_id, main.tri_ins_id, main.operator_id,
	(main.charges * main.units) as totalAmt, main.units, date_format(main.date_of_service,'".$date_format_SQL."') as date_of_service,
	main.mod_id1, main.mod_id2, main.mod_id3, main.mod_id4, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4, 
	date_format(main.entered_date,'".$date_format_SQL."') as entered_date, main.first_posted_date, main.facility_id, main.reff_phy_id,
	main.primary_provider_id_for_reports  as 'primaryProviderId', 
	main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment, main.proc_code_id, 
	patient_data.lname,	patient_data.fname, patient_data.mname, date_format(patient_data.DOB,'".$date_format_SQL."') as 'dob', patient_data.sex, main.gro_id   
	FROM report_enc_detail main 
	JOIN patient_data on patient_data.id = main.patient_id 
	WHERE main.del_status='0'";
	if($having_balance=='1'){
		$qry.=" AND main.proc_balance>0"; 
	}
	if($DateRangeFor=='date_of_service'){
		$qry.=" AND (main.date_of_service between '$startDate' and '$endDate')"; 
	}else{
		$qry.=" AND (DATE_FORMAT(main.entered_date, '%Y-%m-%d') BETWEEN '$startDate' and '$endDate')"; 
	}	
	if(empty($grp_id) === false){
		$qry .= " AND main.gro_id in($grp_id)";
	}
	if(empty($Physician) === false){
		$qry .= " AND main.primary_provider_id_for_reports in($Physician)";
	}
	if(empty($sc_name) === false){
		$qry .= " AND main.facility_id in($sc_name)";
	}
	if(trim($insuranceName) != ''){			
/*		$qry .= " AND (main.pri_ins_id in($insuranceName) 
			OR main.sec_ins_id in($insuranceName) 
			OR main.tri_ins_id in($insuranceName))";
*/	
		$qry .= " AND main.pri_ins_id in($insuranceName)";
	}
	$qry.= " ORDER BY main.date_of_service, patient_data.lname,patient_data.fname, main.encounter_id";
	$res=imw_query($qry);
	
	$main_encounter_id_arr = array();
	$facilityNameArr = array();
	$physician_initial_arr = array();
	$arrPatNoFacility=array();
	while($rs = imw_fetch_assoc($res)){
		$encounter_id = $rs['encounter_id'];
		$chgDetId = $rs['charge_list_detail_id'];
		$arrRefPhysicians[$rs['reff_phy_id']]=$rs['reff_phy_id'];
	
		$arrChgDetIds[$chgDetId] = $chgDetId;
	
		$arrResultData[$chgDetId] = $rs;
		$arrtmpencids[$encounter_id]=$encounter_id;
	}
	unset($rs);
	unset($tempArr);

	//GETTING LAST DATE OF ENCOUNTER MODIFICATION
	if(sizeof($arrtmpencids)>0){
		$arr_last_modified_date=array();
		$strtmpencids = implode(',',$arrtmpencids);
		$qry="Select enc_id,  date_format(modifier_on,'".$date_format_SQL."') as 'modified_date' FROM patient_charge_list_modifiy 
		WHERE enc_id IN(".$strtmpencids.") ORDER BY id";
		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){		
			$arr_last_modified_date[$res['enc_id']]=$res['modified_date'];
		}
		unset($rs);
		unset($tempArr);		
	}
	
	

	//TRANSACTIONS TABLE
	if(sizeof($arrChgDetIds)>0){
		$tempDefaultWriteCodes=array();
		$splitted_chgids = array_chunk($arrChgDetIds,2000);
	
		foreach($splitted_chgids as $arr){
			$str_splitted_chg_ids = implode(',',$arr);
	
			$qry="Select trans.encounter_id, trans.parent_id,trans.report_trans_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
			trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_dot, trans.trans_qry_type,
			DATE_FORMAT(trans.trans_dot,'".$dateFormat."') as trans_dot, trans.trans_del_operator_id  
			FROM report_enc_trans trans 
			WHERE trans.charge_list_detail_id IN(".$str_splitted_chg_ids.") 
			AND trans.trans_type!='charges' 
			ORDER BY trans.trans_dot, trans.trans_dot_time";
			$rs=imw_query($qry);
			while($res = imw_fetch_assoc($rs)){
				$prevFetchedAmt=0;
				$eid= $res['encounter_id'];
				$chgDetId= $res['charge_list_detail_id'];
				$insCompId = $res['trans_ins_id'];
				$trans_type= strtolower($res['trans_type']);
				$trans_by= strtolower($res['trans_by']);			
				$code_id=$res['trans_code_id'];
				$report_trans_id=$res['report_trans_id'];
				$tempRecordData[$report_trans_id]=$res['trans_amount'];												
				switch($trans_type){
					case 'paid':
					case 'copay-paid':
					case 'deposit':
					case 'interest payment':
					case 'negative payment':
					case 'copay-negative payment':
						$paidForProc=$res['trans_amount'];
						if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $res['trans_del_operator_id']>0)$paidForProc="-".$res['trans_amount'];
						if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $res['trans_del_operator_id']>0)$paidForProc=$res['trans_amount'];

						//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
						}
						
						if($trans_by == 'patient' || $trans_by == 'res. party'){
							$patPayDetArr[$chgDetId]['patPaid']+= $paidForProc + $prevFetchedAmt;
						}else if($trans_by == 'insurance'){
							if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
								$patPayDetArr[$chgDetId]['insPaid']+= $paidForProc + $prevFetchedAmt;
							}else{
								$paidForProc=0;
							}
						}
						$mainEncounterPayArr[$chgDetId]+= $paidForProc + $prevFetchedAmt;
					break;
					
					case 'credit':
					case 'debit':
						$crddbtamt=$res['trans_amount'];
						if($trans_type=='credit'){ 
							$crddbtamt= ($res['trans_del_operator_id']>0) ? "-".$res['trans_amount'] : $res['trans_amount'];							
						}else{  //debit
							$crddbtamt= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];				
						}

						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						
						if($trans_by=='insurance'){
							$pay_crd_deb_arr[$chgDetId]['Insurance']+= $crddbtamt + $prevFetchedAmt;
						}else{
							$pay_crd_deb_arr[$chgDetId]['Patient']+= $crddbtamt + $prevFetchedAmt;			
						}


					break;
					case 'default_writeoff':
						$normalWriteOffAmt[$chgDetId]= $res['trans_amount'];
						if($code_id>0){
							$tempDefaultWriteCodes=array(); //TO REMOVE LAST VALUES
							$tempDefaultWriteCodes[$chgDetId][$code_id]=$code_id;
						}
					break;
					case 'write off':
					case 'discount':
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
						$writte_off_arr[$chgDetId]+= $res['trans_amount'] + $prevFetchedAmt;		
						
						if($trans_type=='discount'){
							if($code_id>0)$arrAdjCodesFetched[$chgDetId][$arrAllDisCodes[$code_id]['code']]=$arrAllDisCodes[$code_id]['code'].'-'.$arrAllDisCodes[$code_id]['desc'];
						}else{
							if($code_id>0)$arrAdjCodesFetched[$chgDetId][$arrAllWriteCodes[$code_id]['code']]=$arrAllWriteCodes[$code_id]['code'].'-'.$arrAllWriteCodes[$code_id]['desc'];							
						}
					break;
					
					case 'over adjustment':
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
					
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'] + $prevFetchedAmt;
						
						if($code_id>0)$arrAdjCodesFetched[$chgDetId][$arrAllAdjCodes[$code_id]['code']]=$arrAllAdjCodes[$code_id]['code'].'-'.$arrAllAdjCodes[$code_id]['desc'];
					break;
					
					case 'adjustment':
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'] + $prevFetchedAmt;
						
						if($code_id>0)$arrAdjCodesFetched[$chgDetId][$arrAllAdjCodes[$code_id]['code']]=$arrAllAdjCodes[$code_id]['code'].'-'.$arrAllAdjCodes[$code_id]['desc'];
					break;
					
					case 'returned check':
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'] + $prevFetchedAmt;
					break;
					
					case 'refund':
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'] + $prevFetchedAmt;
					break;
				}
			}
			
		}

		foreach($tempDefaultWriteCodes as $chgDetId => $codeData){
			foreach($codeData as $code_id){
				$arrAdjCodesFetched[$chgDetId][$arrAllWriteCodes[$code_id]['code']]=$arrAllWriteCodes[$code_id]['code'].'-'.$arrAllWriteCodes[$code_id]['desc'];
			}
		}
		unset($tempDefaultWriteCodes);
	}	
	
	//GETTING ALL NAMES OF FETCHED REF-PHYSICIAN
	if(sizeof($arrRefPhysicians)>0){
		$strRefPhysicians=implode(',', $arrRefPhysicians);
		$qry="Select physician_Reffer_id, FirstName, MiddleName, LastName FROM refferphysician WHERE physician_Reffer_id IN($strRefPhysicians)";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$arrRefPhyNames[$res['physician_Reffer_id']] = core_name_format($res['LastName'], $res['FirstName'], $res['MiddleName']);		
		}
		unset($rs);
		unset($arrRefPhysicians);
		unset($strRefPhysicians);
	}
	

	//MAKING OUTPUT DATA
	$file_name="eid_status_".time().".csv";
	$csv_file_name= write_html("", $file_name);

	//CSV FILE NAME
	//$csv_file_name = "../../data/".PRACTICE_PATH."/UserId_".$_SESSION['authId']."/tmp/eid_status_".time().'.csv';
	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');
	
	$arr=array();
	
	$arr[]="Patient ID";
	$arr[]="Full Name";
	$arr[]="Date of Birth";
	$arr[]="Sex";
	$arr[]="CPT Code";
	$arr[]="CPT Code Description";
	$arr[]="DX Codes";
	$arr[]="Modifiers";
	$arr[]="Units";
	$arr[]="Charge Amount";
	$arr[]="Charge Adjustments";
	if($having_balance=='1'){
		$arr[]="Insurance Balance";
		$arr[]="Patient Balance";
	}
	$arr[]="Charge Balance";
	$arr[]="Insurance Payments";
	$arr[]="Patient Payments";
	$arr[]="Charge Payments";
	$arr[]="Adjustment Codes";
	$arr[]="Claim ID";
	$arr[]="DOS Begin";
	$arr[]="Charge Entry Date";
	$arr[]="Facility";
	$arr[]="Billing Entity";
	$arr[]="Location";
	$arr[]="Physician";
	$arr[]="Referring Physician";
	$arr[]="Charge Enetered Operator";
	$arr[]="Primary Coverage";
	$arr[]="Secondary Coverage";
	$arr[]="Claim Responsible";
	$arr[]="Process Status";
	$arr[]="Orig Submit Date";
	$arr[]="Last Modified Date";
	
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name, 'a+');
	
	
	$firstGroupTitle = 'Physician';
	$secGroupTitle = 'Facility';
	
	if($groupBy=='facility'){
		$firstGroupTitle = 'Facility';
		$secGroupTitle = 'Physician';
	}
	

	if(sizeof($arrResultData)>0){
		$grandCharges='';
		$dataExists=true;
		$printFile=true;
		
		// DETAILS
		foreach($arrResultData  as $chgdetid => $grpDetail){
			$arrMod=$arrAdj=$arrDX=array();

			$patient_name = core_name_format($grpDetail['lname'], $grpDetail['fname'], $grpDetail['mname']);
						
			$secGrpTotal+= $grpDetail['charges'];
			$patient_id = $grpDetail['pat_id'];
			$totPaid=$patPayDetArr[$chgdetid]['insPaid'] + $patPayDetArr[$chgdetid]['patPaid'];
			$posted_status= ($grpDetail['first_posted_date']=='0000-00-00') ? 'Not Posted' : 'Posted';
			$first_posted_date=($grpDetail['first_posted_date']!='0000-00-00') ? $grpDetail['first_posted_date']: '';
			
			$claim_responsible='';
			if($grpDetail['pri_due']>0)$claim_responsible=$arrAllInsCompanies[$grpDetail['pri_ins_id']];
	        elseif($grpDetail['sec_due']>0)$claim_responsible=$arrAllInsCompanies[$grpDetail['sec_ins_id']];
			elseif($grpDetail['tri_due']>0)$claim_responsible=$arrAllInsCompanies[$grpDetail['tri_ins_id']];
			elseif($grpDetail['pat_due']>0)$claim_responsible='Patient';
			
			if(empty($grpDetail['mod_id1'])===false)$arrMod[$arrAllModifiers[$grpDetail['mod_id1']]]=$arrAllModifiers[$grpDetail['mod_id1']];
			if(empty($grpDetail['mod_id2'])===false)$arrMod[$arrAllModifiers[$grpDetail['mod_id2']]]=$arrAllModifiers[$grpDetail['mod_id2']];
			if(empty($grpDetail['mod_id3'])===false)$arrMod[$arrAllModifiers[$grpDetail['mod_id3']]]=$arrAllModifiers[$grpDetail['mod_id3']];
			if(empty($grpDetail['mod_id4'])===false)$arrMod[$arrAllModifiers[$grpDetail['mod_id4']]]=$arrAllModifiers[$grpDetail['mod_id4']];

			if(empty($grpDetail['dx_id1'])===false)$arrDX[$grpDetail['dx_id1']]=$grpDetail['dx_id1'];
			if(empty($grpDetail['dx_id2'])===false)$arrDX[$grpDetail['dx_id2']]=$grpDetail['dx_id2'];
			if(empty($grpDetail['dx_id3'])===false)$arrDX[$grpDetail['dx_id3']]=$grpDetail['dx_id3'];
			if(empty($grpDetail['dx_id4'])===false)$arrDX[$grpDetail['dx_id4']]=$grpDetail['dx_id4'];
			
			$strAdjCodes='';
			if(sizeof($arrAdjCodesFetched[$chgdetid])>0){
				$strAdjCodes=implode(', ', $arrAdjCodesFetched[$chgdetid]);
			}
			
			$pat_bal=$ins_bal=0;
			$pat_bal=$grpDetail['pat_due'];
			$ins_bal=$grpDetail['pri_due']+$grpDetail['sec_due']+$grpDetail['tri_due'];
			
			$balAmt=0;
			$balAmt= ($grpDetail['over_payment']>0 && $grpDetail['proc_balance']<=0)? "-".$grpDetail['over_payment'] : $grpDetail['proc_balance'];

			$arr=array();
			
			$arr[]=$grpDetail['patient_id'];
			$arr[]=$patient_name;
			$arr[]=$grpDetail['dob'];
			$arr[]=$grpDetail['sex'];
			$arr[]=$arrAllCPTCodes[$grpDetail['proc_code_id']]['code'];
			$arr[]=$arrAllCPTCodes[$grpDetail['proc_code_id']]['desc'];
			$arr[]=implode(", ",$arrDX);
			$arr[]=implode(", ",$arrMod);
			$arr[]=$grpDetail['units'];
			$arr[]=$grpDetail['totalAmt'];
			$arr[]=$normalWriteOffAmt[$chgdetid] + $writte_off_arr[$chgdetid] + $arrAdjustmentAmt[$chgdetid];
			if($having_balance=='1'){
				$arr[]=$ins_bal;
				$arr[]=$pat_bal;
			}
			$arr[]=$balAmt;
			$arr[]=$patPayDetArr[$chgdetid]['insPaid'];
			$arr[]=$patPayDetArr[$chgdetid]['patPaid'];
			$arr[]=$totPaid;
			$arr[]=$strAdjCodes;
			$arr[]=$grpDetail['encounter_id'];
			$arr[]=$grpDetail['date_of_service'];
			$arr[]=$grpDetail['entered_date'];
			$arr[]=$arrAllFacilities[$grpDetail['facility_id']];
			$arr[]=$arrAllGroups[$grpDetail['gro_id']];
			$arr[]=$arrAllFacilities[$grpDetail['facility_id']];
			$arr[]=$providerNameArr[$grpDetail['primaryProviderId']];
			$arr[]=$arrRefPhyNames[$grpDetail['reff_phy_id']];
			$arr[]=$providerNameArr[$grpDetail['operator_id']];				
			$arr[]=$arrAllInsCompanies[$grpDetail['pri_ins_id']];
			$arr[]=$arrAllInsCompanies[$grpDetail['sec_ins_id']];
			$arr[]=$claim_responsible;
			$arr[]=$posted_status;
			$arr[]=$first_posted_date;
			$arr[]=$arr_last_modified_date[$grpDetail['encounter_id']];
			
			fputcsv($fp,$arr, ",","\"");	
		}
		
	}
	fclose($fp);

	//CREATING ZIP
/*	if(file_exists($csv_file_name)){
		
		$zip_file_name= str_replace('.csv', '.zip', $csv_file_name);
		$fParts=explode("/", $csv_file_name);
		$csvName = $fParts[count($fParts)-1];
		$zipName = $fParts[count($fParts)-1].'.zip';
		$zip = new ZipArchive();
		if ($zip->open(str_replace('.csv','',$csv_file_name).'.zip', ZipArchive::CREATE)!==TRUE) {
			exit("cannot open <$zipName>\n");
		}
		
		$zip->addFile($csv_file_name, $csvName);
		$zip->close();
		
		if(file_exists($zip_file_name)){
			unlink($csv_file_name);
		}
	}	
*/
	if($printFile==false){
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}else{
		echo '<div class="text-center alert alert-info">Click on the link at bottom to download the report result.</div>';
	}
}

?>
