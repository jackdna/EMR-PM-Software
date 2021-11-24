<?php
ini_set("memory_limit","20072M");
set_time_limit (0);
$ignoreAuth = true;

if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
}

include_once(dirname(__FILE__)."/../../config/globals.php");
require_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');
require_once($GLOBALS['fileroot'].'/library/classes/class.reports.php');


	//---Get Global Date Format
	$date_format_SQL = get_sql_date_format();
	$phpDateFormat = phpDateFormat();
	$curDate = date($phpDateFormat.'_h:i');
	$extracted_on = date('Y-m-d H:i:s');
	//$extracted_on ='2019-12-21 10:50:08';


 	/*if($_REQUEST['start_date']!='' && $_REQUEST['end_date']!=''){
		$Start_date = $_REQUEST['start_date'];
		$End_date = $_REQUEST['end_date'];
	}else{
		$Start_date = '2018-08-12';
		$End_date = '2019-02-19';
	}*/

	$Start_date='01-01-2017';
	$End_date= date('m-d-Y');
	//$End_date= '12-21-2019';
	
	
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
		$name = trim($qryRes['name']);
		$pos_prac_code = $qryRes['pos_prac_code'];
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}	
	
	//--- GET FACILITY----
	$fac_query = "select id,name from facility order by name";
	$fac_query_res = imw_query($fac_query);
	$fac_id_arr = array();
	$facilityName = "";
	while ($fac_res = imw_fetch_array($fac_query_res)) {
		$sel='';
		$fac_id = $fac_res['id'];
		$fac_name = trim($fac_res['name']);
		$fac_id_arr[$fac_id] = addslashes($fac_name);
	}
	
	//GET ALL USERS
	//$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname, pro_title, pro_suffix FROM users");	
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$title = ($res['pro_suffix']!='')? $res['pro_suffix'] : $res['pro_title'];
		$title = trim($title);
		$uLname = trim($res['lname']);
		$uFname = trim($res['fname']);
		$uMname = trim($res['mname']);
		
		$pro_name = core_name_format($title, $uFname, $uMname,$uLname);		
		$providerNameArr[$id] = $pro_name;
	}

	//--- GET GROUP NAME ---
	$group_name = "All Groups Selected";
	$arrAllGroups=array();
	$group_query = imw_query("select gro_id, name from groups_new");
	while($groupQryRes = imw_fetch_array($group_query)){
 		$group_name = trim(str_replace("&amp;","&",$groupQryRes['name']));
		$arrAllGroups[$groupQryRes['gro_id']] = $group_name;
	}

	//GET ALL CPT PRACTICE CODES
	$arrAllCPTCodes[0]='No CPT';
	$qry="Select cpt_fee_id,cpt_prac_code,cpt_desc FROM cpt_fee_tbl";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllCPTCodes[$res['cpt_fee_id']]['code'] = trim($res['cpt_prac_code']);
		$arrAllCPTCodes[$res['cpt_fee_id']]['desc'] = trim($res['cpt_desc']);
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
	$arrAllInsCompanies[0]='';
	$qry="Select id as insCompId,in_house_code as insCompINHouseCode,name as insCompName FROM insurance_companies";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$id = $res['insCompId'];
		$insName = $ers['insCompINHouseCode'];
		if(trim($insName) == ''){
			$insName = $res['insCompName'];
		}
		$arrAllInsCompanies[$id] = trim($insName);
	}

	//---------------------------------------START DATA --------------------------------------------
	$qry = "Select main.report_enc_detail_id, main.lastPaymentDate, main.patient_id, main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id, main.sec_ins_id, main.tri_ins_id, main.operator_id,
	(main.charges * main.units) as totalAmt, main.units, main.date_of_service,
	main.mod_id1, main.mod_id2, main.mod_id3, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4, main.entered_date, main.first_posted_date, main.from_pat_due_date,
	main.facility_id, main.billing_facility_id, main.reff_phy_id, main.primary_provider_id_for_reports  as 'primaryProviderId', 
	main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment, main.proc_code_id, 
	patient_data.lname, patient_data.fname, patient_data.mname, patient_data.suffix, patient_data.street, patient_data.street2, patient_data.postal_code, patient_data.city, patient_data.state, 
	patient_data.phone_home, patient_data.phone_biz, patient_data.phone_home, patient_data.phone_cell, patient_data.email, patient_data.preferr_contact, main.gro_id,
	main.del_status	 
	FROM report_enc_detail main 
	JOIN patient_data on patient_data.id = main.patient_id 
	WHERE (main.date_of_service between '$startDate' and '$endDate')				 
	AND main.gro_id='4'";
	if($_REQUEST['disp_deleted']=='no'){
		$qry.=" AND main.del_status='0'";
	}elseif($_REQUEST['disp_deleted']=='yes'){
		$qry.=" AND main.del_status='1'";
	}
	$qry.= " ORDER BY main.date_of_service, patient_data.lname,patient_data.fname, main.encounter_id";
	
	$res=imw_query($qry);

//  AND main.first_posted_date='0000-00-00' 
	
	$main_encounter_id_arr = array();
	$facilityNameArr = array();
	$physician_initial_arr = array();
	$arrPatNoFacility=array();
	$chgdetid_for_temp_query='';
	while($rs = imw_fetch_assoc($res)){
		$encounter_id = $rs['encounter_id'];
		$chgDetId = $rs['charge_list_detail_id'];
		$arrRefPhysicians[$rs['reff_phy_id']]=$rs['reff_phy_id'];

		$arrChgDetIds[$chgDetId] = $chgDetId;
		//ADDED ONLY SO THAT IF EVEN NO TRANSACTION DONE THEN IT MAY SHOW ENCOUNTER DATA.
		$transactionData[$chgDetId][0]=1;
		$arrResultData[$chgDetId] = $rs;
		
		$chgdetid_for_temp_query.='('.$chgDetId.'),';		
	}
	unset($rs);
	unset($tempArr);

	if(empty($chgdetid_for_temp_query)==false){
		$chgdetid_for_temp_query=substr($chgdetid_for_temp_query,0, -1);
	}	

	$sql = "SELECT DISTINCT(`sa_patient_id`) AS 'pt_id', sa_app_starttime, sa_app_start_date FROM `schedule_appointments` WHERE (`sa_app_start_date` BETWEEN '".$startDate."' AND '".$endDate."') AND (`sa_app_end_date` BETWEEN '".$startDate."' AND '".$endDate."') and sa_patient_app_status_id NOT IN(18, 203, 201)";
	$rs=imw_query($sql);
	$schedule_appointments = array();
	while($resq = imw_fetch_assoc($rs)){
		$schedule_appointments[$resq['pt_id']] = $resq['sa_app_starttime'];
	}
	
	// Getting Insurance claim numbers
	$sqlPtClaim = "Select id, encounter_id,posted_for from posted_record ORDER BY id";
	$rsClaim=imw_query($sqlPtClaim);
	$ptClaimNumber = array();
	while($resClaim = imw_fetch_assoc($rsClaim)){
		$ptClaimNumber[$resClaim['encounter_id']][$resClaim['posted_for']] = $resClaim;
	}
	
	$qry="Select encounter_id, submited_date, LOWER( Ins_type ) AS Ins_type FROM submited_record ORDER BY submited_id";
	$rs = imw_query($qry);
	$arrSubmittedEncIds = array();
	while($res = imw_fetch_array($rs)){
		$arrSubmittedEncIds[$res['encounter_id']][$res['Ins_type']]=$res['submited_date'];
	}
		
	//TRANSACTIONS TABLE
	if(sizeof($arrChgDetIds)>0){
		$tempDefaultWriteCodes=array();
	
		//CREATE TEMP TABLE AND INSERT DATA
		$temp_join_part='';
		if(empty($chgdetid_for_temp_query)==false){
			$tmp_table="reports_ecp_billing_chg_ids_".time();
			imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
			imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (chgdet_id INT)");
			imw_query("INSERT INTO $tmp_table (chgdet_id) VALUES ".$chgdetid_for_temp_query);
			$temp_join_part=" INNER JOIN ".$tmp_table." t_tbl ON trans.charge_list_detail_id = t_tbl.chgdet_id";
		}
		
		$qry="Select trans.report_trans_id, trans.encounter_id, trans.parent_id,trans.report_trans_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
		trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.trans_method, trans.check_number,
		trans.date_timestamp as trans_dot, trans.trans_del_operator_id, trans.trans_operator_id  
		FROM report_enc_trans trans 
		".$temp_join_part."
		WHERE trans.trans_type!='charges' 
		ORDER BY trans.master_tbl_id, trans.report_trans_id, trans.trans_dot, trans.trans_dot_time";
		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){
			$prevFetchedAmt=0;
			$eid= $res['encounter_id'];
			$chgDetId= $res['charge_list_detail_id'];
			$insCompId = $res['trans_ins_id'];
			$trans_dot = $res['trans_dot'];
			$trans_type= strtolower($res['trans_type']);
			$trans_by= strtolower($res['trans_by']);			
			$trans_method= strtolower($res['trans_method']);			
			$code_id=$res['trans_code_id'];
			$id=$report_trans_id=$res['report_trans_id'];
			$tempRecordData[$report_trans_id]=$res['trans_amount'];												

			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']])$id=$res['parent_id'];
			$transactionData[$chgDetId]['check_no']= $res['check_number'];
			$transactionData[$chgDetId]['operator']= $res['trans_operator_id'];

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
						$transactionData[$chgDetId]['patPaid']+= $paidForProc + $prevFetchedAmt;
					}else if($trans_by == 'insurance'){
						$transactionData[$chgDetId]['insPaid']+= $paidForProc + $prevFetchedAmt;
						$transactionData[$chgDetId]['transInsComp']= $insCompId;
					}
				break;
				
				case 'credit':
				case 'debit':
					$crddbtamt=$res['trans_amount'];
					if($trans_type=='credit'){ 
						$crddbtamt= ($res['trans_del_operator_id']>0) ? "-".$res['trans_amount'] : $res['trans_amount'];							
					}else{  //debit
						$crddbtamt= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];				
					}

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];
					}
					
					if($trans_by=='insurance'){
						$transactionData[$chgDetId]['insPaid']+= $crddbtamt + $prevFetchedAmt;
					}else{
						$transactionData[$chgDetId]['patPaid']+= $crddbtamt + $prevFetchedAmt;			
					}
					
					$transactionData[$chgDetId]['trans_dot']= $trans_dot;

				break;
				case 'default_writeoff':
					$transactionData[$chgDetId]['default_writeoff']= $res['trans_amount'];
					$transactionData[$chgDetId]['trans_dot']= $trans_dot;

					if($code_id>0){
						$tempDefaultWriteCodes=array(); //TO REMOVE LAST VALUES
						$tempDefaultWriteCodes[$chgDetId][$id][$code_id]=$code_id;
					}
				break;
				case 'write off':
				case 'discount':
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
					}
					
					if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
					$transactionData[$chgDetId]['adj']+= $res['trans_amount'] + $prevFetchedAmt;		
					$transactionData[$chgDetId]['trans_dot']= $trans_dot;
					
					if($trans_type=='discount'){
						if($code_id>0)$arrAdjCodesFetched[$chgDetId][$id][$arrAllDisCodes[$code_id]['code']]=$arrAllDisCodes[$code_id]['code'].'-'.$arrAllDisCodes[$code_id]['desc'];
					}else{
						if($code_id>0)$arrAdjCodesFetched[$chgDetId][$id][$arrAllWriteCodes[$code_id]['code']]=$arrAllWriteCodes[$code_id]['code'].'-'.$arrAllWriteCodes[$code_id]['desc'];							
					}
				break;
				
				case 'over adjustment':
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
					}
				
					if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
					$transactionData[$chgDetId]['adj']+= $res['trans_amount'] + $prevFetchedAmt;
					$transactionData[$chgDetId]['trans_dot']= $trans_dot;
					
					if($code_id>0)$arrAdjCodesFetched[$chgDetId][$id][$arrAllAdjCodes[$code_id]['code']]=$arrAllAdjCodes[$code_id]['code'].'-'.$arrAllAdjCodes[$code_id]['desc'];
				break;
				
				case 'adjustment':
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
					$transactionData[$chgDetId]['adj']+= $res['trans_amount'] + $prevFetchedAmt;
					$transactionData[$chgDetId]['trans_dot']= $trans_dot;
					
					if($code_id>0)$arrAdjCodesFetched[$chgDetId][$id][$arrAllAdjCodes[$code_id]['code']]=$arrAllAdjCodes[$code_id]['code'].'-'.$arrAllAdjCodes[$code_id]['desc'];
				break;
				
				case 'returned check':
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
					$transactionData[$chgDetId]['adj']+= $res['trans_amount'] + $prevFetchedAmt;
					$transactionData[$chgDetId]['trans_dot']= $trans_dot;
				break;
				
				case 'refund':
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
					$transactionData[$chgDetId]['adj']+= $res['trans_amount'] + $prevFetchedAmt;
					$transactionData[$chgDetId]['trans_dot']= $trans_dot;
				break;
			}
		}
		

		foreach($tempDefaultWriteCodes as $chgDetId => $transData){
			foreach($transData as $id => $codeData){
				foreach($codeData as $code_id){
					$arrAdjCodesFetched[$chgDetId][$id][$arrAllWriteCodes[$code_id]['code']]=$arrAllWriteCodes[$code_id]['code'].'-'.$arrAllWriteCodes[$code_id]['desc'];
				}
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
	$pfx="|";
	$date_now = date("Y-m-d"); 
	//$date_now = '2019-12-21'; 
	
	$date = str_replace("-","",$date_now);	
	$file_name="bb_imedicware_export_".$date.".csv";
	$csv_file_name= write_html("", $file_name);
	
	//CSV FILE NAME
	//$csv_file_name = "../../data/".PRACTICE_PATH."/UserId_".$_SESSION['authId']."/tmp/eid_status_".time().'.csv';
	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	//$fp = fopen ($csv_file_name, 'a+');
	
	//$strData.="INTERNAL_ID".$pfx;
	$strData.="ENCOUNTER_ID".$pfx;
	$strData.="BILLING_ENTITY".$pfx;
	$strData.="LOCATION".$pfx;
	$strData.="FACILITY".$pfx;
	$strData.="PHYSICIAN".$pfx;
	$strData.="PATIENT_ID".$pfx;
	$strData.="PATIENT_LAST_NAME".$pfx;
	$strData.="PATIENT_FIRST_NAME".$pfx;
	$strData.="PATIENT_MIDDLE_INTITIAL".$pfx;
	$strData.="PATIENT_END_TITLE".$pfx;
	$strData.="PRIMARY_COVERAGE".$pfx;
	$strData.="PRIMARY_MOST_RECENT_CLAIM".$pfx;
	$strData.="PRIMARY_MOST_RECENT_CLAIM_ENTERED".$pfx;
	$strData.="SECONDARY_COVERAGE".$pfx;
	$strData.="SECONDARY_MOST_RECENT_CLAIM".$pfx;
	$strData.="SECONDARY_MOST_RECENT_CLAIM_ENTERED".$pfx;
	$strData.="TERTIARY_COVERAGE".$pfx;
	$strData.="TERTIARY_MOST_RECENT_CLAIM".$pfx;
	$strData.="TERTIARY_MOST_RECENT_CLAIM_ENTERED".$pfx;
	$strData.="CHARGE_ENTRY_DATE".$pfx;
	$strData.="DOS_BEGIN".$pfx;
	$strData.="SCHEDULED_TIME_OF_SERVICE".$pfx;
	$strData.="LAST_TRANS_DATE".$pfx;
	$strData.="PATIENT_RESP_DATE".$pfx;
	$strData.="CPT_CODE".$pfx;
	$strData.="MODIFIERS".$pfx;
	$strData.="CHARGE_AMOUNT".$pfx;
	$strData.="INSURANCE_PAYMENTS".$pfx;
	$strData.="PATIENT_PAYMENTS".$pfx;
	$strData.="OTHER_PAYMENTS".$pfx;
	$strData.="ADJUSTMENT".$pfx;
	$strData.="PRIMARY_COVERAGE_BALANCE".$pfx;
	$strData.="SECONDARY_COVERAGE_BALANCE".$pfx;
	$strData.="TERTIARY_COVERAGE_BALANCE".$pfx;
	$strData.="INSURANCE_BALANCE".$pfx;
	$strData.="PATIENT_BALANCE".$pfx;
	$strData.="OTHER_BALANCE".$pfx;
	$strData.="SOURCE_SYSTEM".$pfx;
	$strData.="EXTRACTED_ON";
	$strData.= "\n";
	//$fp=fopen($csv_file_name,'w');
	//@fwrite($fp,$strData);
	//@fclose($fp);
	
	if(sizeof($arrResultData)>0){
		$grandCharges='';
		$dataExists=true;
		$printFile=true;
		//pre($arrResultData);
		$arryuni=array();		
		
		foreach($arrResultData  as $chgdetid => $grpDetail){
			if(!$arryuni[$grpDetail['encounter_id']][$chgdetid]) $arryuni[$grpDetail['encounter_id']][$chgdetid] = array();
			$arryuni[$grpDetail['encounter_id']][$chgdetid]=$grpDetail;
		}
		foreach($arryuni as $eid => $chrgObj){
			$enc_inc = 1;
			foreach($chrgObj as $chrId => $detail_obj){
				$arrMod=$arrAdj=$arrDX=array();		
				$strMod=$strDxCodes='';
				$balAmt=0;
				
				$claim_pri = $ptClaimNumber[$eid][1]['id'];
				$claim_sec = $ptClaimNumber[$eid][2]['id'];
				$claim_ter = $ptClaimNumber[$eid][3]['id'];
				
				$Claim_enter_pri = $arrSubmittedEncIds[$eid]['primary'];
				$Claim_enter_sec = $arrSubmittedEncIds[$eid]['secondary'];
				$Claim_enter_ter = $arrSubmittedEncIds[$eid]['tertiary'];
				
				$patient_id = $detail_obj['patient_id'];
				
				$phone='';
				$phone=($detail_obj['preferr_contact']=='0' && $detail_obj['phone_home']!='')? $detail_obj['phone_home'] : '';
				$phone=($phone=='' && $detail_obj['preferr_contact']=='1' && $detail_obj['phone_biz']!='')? $detail_obj['phone_biz'] : '';
				$phone=($phone=='' && $detail_obj['preferr_contact']=='2' && $detail_obj['phone_cell']!='')? $detail_obj['phone_cell'] : '';
				if($phone==''){
					if($detail_obj['phone_home']!='')$phone=$detail_obj['phone_home'];
					else if($detail_obj['phone_biz']!='')$phone=$detail_obj['phone_biz'];
					else if($detail_obj['phone_cell']!='')$phone=$detail_obj['phone_cell'];
				}				
			
				$appttime = $schedule_appointments[$patient_id];
				$report_enc_detail_id = $detail_obj['charge_list_detail_id'];		
				$from_pat_due_date = ($detail_obj['from_pat_due_date']=='0000-00-00') ? '' : $detail_obj['from_pat_due_date'];
				$detail_obj['first_posted_date']= ($detail_obj['first_posted_date']=='0000-00-00') ? '1970-01-01' : $detail_obj['first_posted_date'];
				
				$balAmt= ($detail_obj['over_payment']>0 && $detail_obj['proc_balance']<=0)? "-".$detail_obj['over_payment'] : $detail_obj['proc_balance'];
				
				//CLAIM RESPONSIBLE
				$claim_responsible='';
				if($detail_obj['pri_due']>0)$claim_responsible=$arrAllInsCompanies[$detail_obj['pri_ins_id']];
				elseif($detail_obj['sec_due']>0)$claim_responsible=$arrAllInsCompanies[$detail_obj['sec_ins_id']];
				elseif($detail_obj['tri_due']>0)$claim_responsible=$arrAllInsCompanies[$detail_obj['tri_ins_id']];
				elseif($detail_obj['pat_due']>0)$claim_responsible='Patient';

				//$pat_bal=$detail_obj['pat_due'];
				$pat_bal=$detail_obj['pat_due']-$detail_obj['over_payment'];
				$ins_bal=$detail_obj['pri_due']+$detail_obj['sec_due']+$detail_obj['tri_due'];
				
				if(empty($detail_obj['mod_id1'])===false)$arrMod[$arrAllModifiers[$detail_obj['mod_id1']]]=$arrAllModifiers[$detail_obj['mod_id1']];
				if(empty($detail_obj['mod_id2'])===false)$arrMod[$arrAllModifiers[$detail_obj['mod_id2']]]=$arrAllModifiers[$detail_obj['mod_id2']];
				if(empty($detail_obj['mod_id3'])===false)$arrMod[$arrAllModifiers[$detail_obj['mod_id3']]]=$arrAllModifiers[$detail_obj['mod_id3']];
				$strMod=implode(", ",$arrMod);
		
				if(empty($detail_obj['dx_id1'])===false)$arrDX[$detail_obj['dx_id1']]=$detail_obj['dx_id1'];
				if(empty($detail_obj['dx_id2'])===false)$arrDX[$detail_obj['dx_id2']]=$detail_obj['dx_id2'];
				if(empty($detail_obj['dx_id3'])===false)$arrDX[$detail_obj['dx_id3']]=$detail_obj['dx_id3'];
				if(empty($detail_obj['dx_id4'])===false)$arrDX[$detail_obj['dx_id4']]=$detail_obj['dx_id4'];
				$strDxCodes=implode(", ",$arrDX);
				
				$trans_dot = $transactionData[$chrId]['trans_dot'];				
				$patPaid = $transactionData[$chrId]['patPaid'];
				$insPaid = $transactionData[$chrId]['insPaid'];
				$adj = $transactionData[$chrId]['default_writeoff'] + $transactionData[$chrId]['adj'];
				
				//CHANGE NEGATVIE/POSITIVE AS PER BASED ON REQUIREMENT RECIEVED
				$patPaid = ($patPaid<0)? -($patPaid) : -$patPaid;
				$insPaid = ($insPaid<0)? -($insPaid) : -$insPaid; 
				$adj = ($adj<0)? -($adj) : -$adj;
								
				$strLength = strlen($enc_inc);
				$showZero = 6;
				
				if($strLength > 1) $showZero = ($showZero - $strLength);
				if($showZero <= 1) $showZero = 6;
				$enc_inc = str_pad($enc_inc, $showZero, '0', STR_PAD_LEFT);
				$arr=array();

				$strData.=$eid.$enc_inc.$pfx;
				$strData.="BBECPROF".$pfx;
				$strData.=$fac_id_arr[$detail_obj['billing_facility_id']].$pfx; //swapped
				$strData.=$arrAllFacilities[$detail_obj['facility_id']].$pfx;  //swapped
				$strData.=$providerNameArr[$detail_obj['primaryProviderId']].$pfx;
				$strData.=$patient_id.$pfx;
				$strData.=trim($detail_obj['lname']).$pfx;
				$strData.=trim($detail_obj['fname']).$pfx;
				$strData.=trim($detail_obj['mname']).$pfx;
				$strData.=($detail_obj['del_status']=='1' ? 'DELETE' : trim($detail_obj['suffix'])).$pfx;
				$strData.=$arrAllInsCompanies[$detail_obj['pri_ins_id']].$pfx;
				$strData.=$claim_pri.$pfx;
				$strData.=$Claim_enter_pri.$pfx;
				$strData.=$arrAllInsCompanies[$detail_obj['sec_ins_id']].$pfx;
				$strData.=$claim_sec.$pfx;
				$strData.=$Claim_enter_sec.$pfx;
				$strData.=$arrAllInsCompanies[$detail_obj['tri_ins_id']].$pfx;
				$strData.=$claim_ter.$pfx;
				$strData.=$Claim_enter_ter.$pfx;
				$strData.=$detail_obj['first_posted_date'].$pfx;
				$strData.=$detail_obj['date_of_service'].$pfx;
				$strData.=$appttime.$pfx;
				$strData.=$trans_dot.$pfx;
				$strData.=$from_pat_due_date.$pfx;
				$strData.=$arrAllCPTCodes[$detail_obj['proc_code_id']]['code'].$pfx;
				$strData.=$strMod.$pfx;
				$strData.=($detail_obj['totalAmt'] ? round($detail_obj['totalAmt'],2) : '0').$pfx;
				$strData.=($insPaid ? round($insPaid,2) : '0').$pfx;
				$strData.=($patPaid ? round($patPaid,2) : '0').$pfx;
				$strData.="".$pfx;
				$strData.=($adj ? round($adj,2) : '0').$pfx;
				$strData.=($detail_obj['pri_due'] ? round($detail_obj['pri_due'],2) : '0').$pfx;
				$strData.=($detail_obj['sec_due'] ? round($detail_obj['sec_due'],2) : '0').$pfx;
				$strData.=($detail_obj['tri_due'] ? round($detail_obj['tri_due'],2) : '0').$pfx;
				$strData.=($ins_bal ? round($ins_bal,2) : '0').$pfx;
				$strData.=(($pat_bal!=0) ? round($pat_bal,2) : '0').$pfx;
				$strData.="".$pfx;
				$strData.="BB_IMEDICWARE".$pfx;
				$strData.=$extracted_on;
				$strData.= "\n";

				//$fp=fopen($csv_file_name,"w");
				//@fwrite($fp,$strData);
				//@fclose($fp);
				$enc_inc++;
				
 				/*
				$b1= round($pat_bal+$ins_bal,2);
				$b2=round($detail_obj['totalAmt']-($insPaid+$patPaid+$adj),2);
				
				$t_charges+=round($detail_obj['totalAmt'],2);
				$t_ins_paid+=round($insPaid,2);
				$t_pat_paid+=round($patPaid,2);
				$t_over_paid+=round($detail_obj['over_payment'],2);					
				$t_adj+=round($adj,2);
				$t_pat_bal+=round($pat_bal,2);
				$t_ins_bal+=round($ins_bal,2);
				$t_tot_bal+=round($pat_bal+$ins_bal,2);
				$t_cal_bal+=round($detail_obj['totalAmt']-($insPaid+$patPaid+$adj),2);
				if($_REQUEST['disp_diff']==1){
					if($b1!=$b2){
						echo $patient_id.'-'.$eid.'-'.$chrId.'-'.round($detail_obj['totalAmt'],2).'-'.$b1.'-'.$b2.'<br>';
					}
				} 
				*/
				
				//$t_arr[$patient_id]+=round($pat_bal,2);
			}
		}
	}
//unset($arryuni);

$fp=fopen($csv_file_name,"w");
@fwrite($fp,$strData);
@fclose($fp);


/*foreach($t_arr as $encounter_id => $amt){
	if($amt!='0'){
		echo $encounter_id.'-'.$amt.'<br>';
	}
}
echo 'Total :'.array_sum($t_arr);*/

/*
echo 'charges '.round($t_charges,2).'<br>';
echo 'ins paid '.round($t_ins_paid,2).'<br>';
echo 'pat paid '.round($t_pat_paid,2).'<br>';
echo 'over paid '.round($t_over_paid,2).'<br>';
echo 'adj '.round($t_adj,2).'<br>';
echo 'pat bal '.round($t_pat_bal,2).'<br>';
echo 'ins bal '.round($t_ins_bal,2).'<br>';
echo 'tot bal '.round($t_tot_bal,2).'<br>';
echo 'cal bal '.round($t_cal_bal,2).'<br>';

fclose($fp);*/
//echo 'Execution completed From '.$Start_date.' - '.$End_date;
//echo '<br><br>'.$csv_file_name;


//SFTP EXECUTION
//UPLOAD FILE ON SERVER
if(file_exists($csv_file_name)){


	$sftp_strServerIP = "54.156.2.56";
	$sftp_strServerPort = "22";
	$sftp_strServerUsername = "ftpuser";
	$sftp_strServerPassword = "0neb@dpassword!";
	$remote_directory="/file_drop";
	
	$file='';
 	$t_arr= explode('/', $csv_file_name);
	$fileNAME=end($t_arr);
	array_pop($t_arr);
	$dirName= implode('/', $t_arr).'/';
	
	include('Net/SFTP.php');
	/* Change the following directory path to your specification */
	$local_directory = $dirName;
	$remote_directory1 = $remote_directory.'/';//providing physical(full) path
	$file = $fileNAME;

	/* Add the correct FTP credentials below */
	$sftp = new Net_SFTP($sftp_strServerIP,$sftp_strServerPort,'1000');
	if (!$sftp->login($sftp_strServerUsername,$sftp_strServerPassword)){
		//exit('Login Failed');
	} else{
		//echo 'Login Successful';
	}

	if(file_exists($local_directory.$file))	{
		/* Upload the local file to the remote server put('remote file', 'local file'); */
		$success = $sftp->put($remote_directory1.$file, $local_directory.$file, NET_SFTP_LOCAL_FILE);
		//echo "upload physical :".$success;
	}else{
		//echo 'file not found';
	}	
}

//DELETE 7 DAYS OLD FILES
//$CLSReports = new CLSReports;
//$CLSReports->delete_old_files("bb_imedicware", 'pre');
?>
