<?php

$masterEncArr =array();
$arrMasterPatDet =array();
$masterPatArr=array();
$getSqlDateFormat = get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');		
$page_data = NULL;
$pdf_data = NULL;

if(empty($_POST['form_submitted']) === false){
	
	$search = "DOS";
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	
	$Start_date = $_POST['Start_date'];
	$End_date = $_POST['End_date'];
	$Sdate=$Start_date;
	$Edate=$End_date;
	$Start_date = getDateFormatDB($Start_date);
	$End_date = getDateFormatDB($End_date);
	
	$statements= ($statements=='')? '0' : $statements;
	$aging_days= ($aging_days=='')? '0' : $aging_days;
	$minBalance= ($minBalance=='')? '0' : $minBalance;

	$next_action_status_arr = $next_action_status;
	if($key=array_search('other', $next_action_status_arr)){
		unset($next_action_status_arr[$key]);
		$next_action_status = implode(',', $next_action_status_arr);
	}else{
		$next_action_status = implode(',', $next_action_status_arr);
	}

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
		$facility_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_name[0]='NULL';
		}
	}
		
	//FACILITY
	$sc_name = join(',',$facility_name);
	
	//PHYSICIAN
	$rqArrPhyId = $_REQUEST['phyId'];
	$Physician = join(',',$rqArrPhyId);
	
	$groupId = $_REQUEST['groups'];
	$grp_id = join(',',$groupId);
	
	//--- GET GROUP NAME ---
	$group_name = $CLSReports->report_display_selected($grp_id,'group','1');
	$practice_name = $CLSReports->report_display_selected($sc_name,'practice','1');
	$physician_name = $CLSReports->report_display_selected($Physician,'physician','1');
	$action_name = $CLSReports->report_display_selected($next_action_status,'nextAction','1');
	
	$chkbox_after_collection_arr = explode(',', $chkbox_after_collection);

	// GET ID OF COLLECTION STATUS
	$collectionId = get_account_status_id_collections();

	// GET ALL ACTION CODES
	$arrAllActionCodes=array();
	$qry = "select id, action_status from patient_next_action";
	$rs=imw_query($qry);
	while($res = imw_fetch_array($rs)){
		$arrAllActionCodes[$res['id']] = $res['action_status'];
	}
	
	if(empty($letter_chk_imp) === false){
		require_once(dirname(__FILE__).'/assessment_letter_pdf.php');
	}
}

//PATIENT IDS
$qry= "Select id FROM patient_data WHERE trim(patient_data.lname) != 'doe'";
if(empty($next_action_status)==true){	
	if($startLname){
		$qry .= " and patient_data.lname > '$startLname'";
	}
	if($endLname){
		$qry.= " and (patient_data.lname < '$endLname' or patient_data.lname like '$endLname%')";
	}
	if($patientId){
		$qry .= " and patient_data.id = '$patientId'";
	}
}
if(empty($next_action_status)==false){
	$qry.= " AND patient_data.next_action_status IN(".$next_action_status.")";
}
if(empty($collectionId)==false){
	$qry.= " AND patient_data.pat_account_status NOT IN (".$collectionId.")";
}
$rs=imw_query($qry);

while($res=imw_fetch_array($rs)){
	$arrMainPats[$res['id']]=$res['id'];
}unset($rs); unset($res);

// MASTER QUERY TO GET PATIENTS HAVING PAT DUE > 0
if(sizeof($arrMainPats)>0){
	$strMainPats = implode(',', $arrMainPats);
	$minBalance= ($minBalance=='0') ? '1' : $minBalance;
	$groupPart =" GROUP BY patient_id HAVING SUM(pat_due)>=$minBalance";	
	
	$masterQry = "Select patient_id, encounter_id, SUM(pat_due) as pat_due FROM report_enc_detail WHERE del_status='0'";
	if(empty($next_action_status)==true){	
		if(empty($Start_date)==false && empty($End_date)==false){
			$masterQry.= " AND date_of_service between '$Start_date' and '$End_date'";
		}
		if(empty($sc_name) == false){
			$masterQry.= " and facility_id IN ($sc_name)";
		}
		if(empty($grp_id) == false){
			$masterQry.= " and gro_id IN ($grp_id)";
		}
		if(empty($Physician) == false){
			$masterQry.= " and primary_provider_id_for_reports IN ($Physician)";
		}
	}	
	$masterQry.= $groupPart;
	$masterQry.= " ORDER BY encounter_id";
	$masterRs=imw_query($masterQry);
	$groupPart='';
	while($masterRes = imw_fetch_array($masterRs)){
		$arrMainChargePats[$masterRes['patient_id']]= $masterRes['patient_id'];
		$arrMainChargeDue[$masterRes['patient_id']]+= $masterRes['pat_due'];
	} unset($masterRs); unset($masterRes);

	$resultArr= array_intersect($arrMainPats, $arrMainChargePats);
	
	foreach($resultArr as $pid){
		$masterPatArr[$pid] = $pid;
		$masterPatBal[$pid]+= $arrMainChargeDue[$pid];
		$arrTotBalance[$pid]+= $arrMainChargeDue[$pid];
	}
	$masterPatStr =implode(',', $masterPatArr);
	unset($resultArr);
}

// GET STATMENTS COUNTS OF PATIENTS
$arrLastPaidDate = array();
$arrPatStatments=array();
if(sizeof($masterPatArr)>0){
	$masterPatStr = implode(',', $masterPatArr);
	$masterPatArr== array();
	$tempArr =array();
	// 	GET ALL LASTPAID DATES OF PATIENTS
	$qry="Select patient_id, IF(lastPaymentDate!='0000-00-00', lastPaymentDate, date_of_service) as lastPaymentDate 
	FROM report_enc_detail WHERE patient_id IN(".$masterPatStr.") AND del_status='0'";
	if(empty($next_action_status)==true){	
		if(empty($Start_date)==false && empty($End_date)==false){
			$qry.= " AND report_enc_detail.date_of_service between '$Start_date' and '$End_date'";
		}
		if(empty($sc_name) == false){
			$qry.= " and report_enc_detail.facility_id IN ($sc_name)";
		}
		if(empty($grp_id) == false){
			$qry.= " and report_enc_detail.gro_id IN ($grp_id)";
		}
		if(empty($Physician) == false){
			$qry.= " and report_enc_detail.primary_provider_id_for_reports IN ($Physician)";
		}
	}	
	$qry.=" ORDER BY lastPaymentDate";
	$rs =imw_query($qry);
	while($res=imw_fetch_array($rs)){
		if($tempArr[$res['patient_id']]['LAST_DATE']==0 || ($res['lastPaymentDate']!='0000-00-00' && $res['lastPaymentDate']>$arrLastPaidDate[$masterRes['patient_id']])){
			$arrLastPaidDate[$res['patient_id']] = $res['lastPaymentDate'];
			$tempArr[$res['patient_id']]['LAST_DATE'] = 1;
		}
	}

	$arrLastPaidPats= array_keys($arrLastPaidDate);
	$strLastPaidPats = implode(',', $arrLastPaidPats);
	$tempArr=array();
	if(empty($strLastPaidPats)===false){
		$qry="Select patient_id, previous_statement_id, created_date FROM previous_statement WHERE patient_id IN(".$strLastPaidPats.")";
		$rs=imw_query($qry);
		while($res= imw_fetch_array($rs)){
			$pid = $res['patient_id'];
			$previousStatePats[$pid] = $pid;

			if($res['created_date'] > $arrLastPaidDate[$pid]){
				$arrPatStatments[$pid]+=1;
				$arrMasterPatDet[$pid]['STATEMENT_DATE']= $res['created_date'];
				$tempArr[$pid] = $pid;
			}
		}
		if(empty($statements) == false){
			foreach($arrPatStatments as $pid => $count){
				if($count < $statements){
					unset($masterPatArr[$pid]);
				}
			}
			//GET ALL PATIENT IDS THAT ARE NOT IN previous_statement TABLE
			$tempArr1 = array_intersect($previousStatePats, $tempArr);
			$resultArr = array_diff($arrLastPaidPats, $tempArr1);
			foreach($resultArr as $pid){
				unset($masterPatArr[$pid]);
			}
		}
		unset($tempArr);
		unset($resultArr);
		unset($previousStatePats);
	}
}
// RE-IMPLODE ARRAY
$masterPatStr  =implode(',', $masterPatArr);


// QUERY TO GET LAST ENCOUNTER FOR EVERY PATIENT
if(sizeof($masterPatArr)>0){
$tempCheckArr  =array();
$masterQry = "Select report_enc_detail.patient_id, report_enc_detail.encounter_id,lastPaymentDate, report_enc_detail.date_of_service, 
		report_enc_detail.statement_status, date_format(report_enc_detail.statement_date,'$getSqlDateFormat') as statement_date,
		report_enc_detail.statement_date as 'st_date',
		date_format(report_enc_detail.letter_sent_date,'$getSqlDateFormat') as letter_sent_date, report_enc_detail.pat_due,
		IF(lastPaymentDate<>'0000-00-00', report_enc_detail.lastPaymentDate, report_enc_detail.date_of_service) as lastARDate,
		IF(lastPaymentDate<>'0000-00-00',DATEDIFF(NOW(),report_enc_detail.lastPaymentDate),DATEDIFF(NOW(),report_enc_detail.date_of_service)) as arAgingDays  
		FROM report_enc_detail 
		WHERE patient_id IN(".$masterPatStr.") AND report_enc_detail.del_status='0'";
		if(empty($next_action_status)==true){		
			if(empty($Start_date)==false && empty($End_date)==false){
				$masterQry.= " AND report_enc_detail.date_of_service between '$Start_date' and '$End_date'";
			}
			if(empty($sc_name) == false){
				$masterQry.= " and report_enc_detail.facility_id IN ($sc_name)";
			}
			if(empty($grp_id) == false){
				$masterQry.= " and report_enc_detail.gro_id IN ($grp_id)";
			}
			if(empty($Physician) == false){
				$masterQry.= " and report_enc_detail.primary_provider_id_for_reports IN ($Physician)";
			}		
			//if(empty($aging_days) == false){
			//	$masterQry.= " AND IF(lastPaymentDate<>'0000-00-00',DATEDIFF(NOW(),report_enc_detail.lastPaymentDate),DATEDIFF(NOW(),report_enc_detail.date_of_service)) >=$aging_days";
			//}
		}
		$masterQry.= " ORDER BY date_of_service";
		
		$masterRs=imw_query($masterQry);
		while($masterRes = imw_fetch_array($masterRs)){
			$masterEncArr[$masterRes['patient_id']] =$masterRes['encounter_id'];
/*			if($masterRes['statement_status']=='1' && $masterRes['st_date'] > $tempCheckArr[$masterRes['patient_id']]['STATEMENT_DATE']){
				$arrMasterPatDet[$masterRes['patient_id']]['STATEMENT_DATE']= $masterRes['statement_date'];
				$tempCheckArr[$masterRes['patient_id']]['STATEMENT_DATE'] = $masterRes['st_date'];
			}*/
			if($masterRes['letter_sent_date']!='00-00-0000' && $masterRes['letter_sent_date'] > $tempCheckArr[$masterRes['patient_id']]['LETTER_DATE']){
				$arrMasterPatDet[$masterRes['patient_id']]['LETTER_SENT_DATE']= $masterRes['letter_sent_date'];
				$tempCheckArr[$masterRes['patient_id']]['LETTER_DATE'] = $masterRes['letter_sent_date'];
			}
			if($tempCheckArr[$masterRes['patient_id']]['LAST_DATE_FIND']!='1' || ($masterRes['lastPaymentDate']!='0000-00-00' && $masterRes['lastPaymentDate']>$tempCheckArr[$masterRes['patient_id']]['LAST_DATE'])){
				$arrMasterPatDet[$masterRes['patient_id']]['AR_DAYS']= $masterRes['arAgingDays'];
				if($masterRes['lastPaymentDate']!='0000-00-00'){
					$tempCheckArr[$masterRes['patient_id']]['LAST_DATE_FIND'] = '1';
					$tempCheckArr[$masterRes['patient_id']]['LAST_DATE'] = $masterRes['lastPaymentDate'];
				}
			}
		}

		//UNSET ALL PATIENT DATA IF AGING OF THIS ENCOUNTER IS LESS THAN SEARCHED AGING
		if($aging_days>0){
			foreach($masterEncArr as $patient_id =>$encid){
				if($arrMasterPatDet[$patient_id]['AR_DAYS']<$aging_days){
					unset($masterEncArr[$patient_id]);
				}
			}
		}
}

$masterEncStr = implode(',', $masterEncArr);

// FINAL QUERY TO GET PATIENT ENCOUNTER DETAIL
if(sizeof($masterEncArr)>0){
$pcl_qry = "Select report_enc_detail.charge_list_id,report_enc_detail.encounter_id,
		date_format(report_enc_detail.date_of_service,'$getSqlDateFormat') as date_of_service,
		report_enc_detail.primary_provider_id_for_reports as 'primaryProviderId',
		users.lname as physicianLname,users.fname as physicianFname,
		users.mname as physicianMname,pos_facilityies_tbl.facilityPracCode,
		pos_facilityies_tbl.pos_facility_id,patient_data.id as patient_id,
		patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.next_action_status,
		patient_data.pat_account_status  
		FROM report_enc_detail
		LEFT JOIN users on users.id = report_enc_detail.primary_provider_id_for_reports 
		LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = report_enc_detail.facility_id 
		LEFT JOIN patient_data on patient_data.id = report_enc_detail.patient_id 
		WHERE report_enc_detail.encounter_id IN(".$masterEncStr.") AND report_enc_detail.del_status='0'";

	if(empty($next_action_status)==true){	
		if(empty($Start_date)==false && empty($End_date)==false){
			$pcl_qry.= " AND report_enc_detail.date_of_service between '$Start_date' and '$End_date'";
		}
		if(empty($sc_name) == false){
			$pcl_qry.= " and report_enc_detail.facility_id IN ($sc_name)";
		}
		if(empty($grp_id) == false){
			$pcl_qry.= " and report_enc_detail.gro_id IN ($grp_id)";
		}
		if(empty($Physician) == false){
			$pcl_qry.= " and report_enc_detail.primary_provider_id_for_reports IN ($Physician)";
		}
	}		
	
	$pcl_qry.= " ORDER BY patient_data.lname, report_enc_detail.date_of_service";
	$qryRes=imw_query($pcl_qry);
	$mainResArr = array();
	while($qryRow = imw_fetch_array($qryRes)){
		$encounter_id = $qryRow['encounter_id'];
		$mainResArr[$encounter_id][] = $qryRow;
	}
}

// GET RECORDS FOR UNDER COLLECTION
if($view_collections!=''){
	$collectionEncArr =array();
	$collectionPatArr = array();
	$arrTotalPatCollDet =array();
	$tempCheckArr  =array();
	if(empty($next_action_status)==false){
		$collQryPart= " AND patient_data.next_action_status IN(".$next_action_status.")";
	}
	// GET LAST ENCOUNER OF PATIENTS
	$rs=imw_query("Select report_enc_detail.patient_id, report_enc_detail.encounter_id, report_enc_detail.statement_status, 
		date_format(report_enc_detail.statement_date,'$getSqlDateFormat') as statement_date, report_enc_detail.statement_date as 'st_date',
		date_format(report_enc_detail.letter_sent_date,'$getSqlDateFormat') as letter_sent_date, 
		IF(report_enc_detail.lastPaymentDate<>'0000-00-00', report_enc_detail.lastPaymentDate, report_enc_detail.date_of_service) as lastARDate,
		report_enc_detail.pat_due as pat_due,
		if(report_enc_detail.lastPaymentDate!='0000-00-00', DATEDIFF(NOW(),report_enc_detail.lastPaymentDate), DATEDIFF(NOW(),report_enc_detail.date_of_service)) as arAgingDays 
		FROM report_enc_detail 
		LEFT JOIN patient_data ON patient_data.id=report_enc_detail.patient_id 
		WHERE patient_data.pat_account_status IN (".$collectionId.") AND report_enc_detail.del_status='0' $collQryPart ORDER BY report_enc_detail.charge_list_id");
	while($collectRes = imw_fetch_array($rs)){
		$collectionEncArr[$collectRes['patient_id']] =$collectRes['encounter_id'];
		$collectionPatArr[$collectRes['patient_id']] = $collectRes['patient_id'];
		$collectionPatDue[$collectRes['patient_id']]+= $collectRes['pat_due'];
		$arrTotBalance[$collectRes['patient_id']]+= $collectRes['pat_due'];
		$masterPatArr[$collectRes['patient_id']] = $collectRes['patient_id'];	

		if($collectRes['statement_status']=='1' && $collectRes['st_date'] > $arrTemp[$collectRes['patient_id']]['STATEMENT_DATE']){
			$arrTotalPatCollDet[$collectRes['patient_id']]['STATEMENT_DATE']= $collectRes['statement_date'];
			$arrTemp[$collectRes['patient_id']]['STATEMENT_DATE']=$collectRes['st_date'];
		}
		if($collectRes['letter_sent_date']!='00-00-0000' && $collectRes['statement_date'] > $arrTotalPatCollDet[$collectRes['patient_id']]['LETTER_SENT_DATE']){
			$arrTotalPatCollDet[$collectRes['patient_id']]['LETTER_SENT_DATE']= $collectRes['letter_sent_date'];
		}
		if($tempCheckArr[$collectRes['patient_id']]['LAST_DATE_FIND']!='1' || ($collectRes['lastPaymentDate']!='0000-00-00' && $collectRes['lastPaymentDate']>$tempCheckArr[$collectRes['patient_id']]['LAST_DATE'])){
			$arrTotalPatCollDet[$collectRes['patient_id']]['AR_DAYS']= $collectRes['arAgingDays'];
			if($collectRes['lastPaymentDate']!='0000-00-00'){
				$tempCheckArr[$collectRes['patient_id']]['LAST_DATE_FIND']='1';
				$tempCheckArr[$collectRes['patient_id']]['LAST_DATE']=$collectRes['lastPaymentDate'];
			}
		}
	}
	$collectionEncStr  =implode(',', $collectionEncArr);
	$collectionPatArr  =implode(',', $collectionPatArr);
	
	// GET STATMENTS COUNTS OF UNDER COLLECTION PATIENTS
	$arrLastPaidDate = array();
	//$arrPatStatments=array();
	if(sizeof($collectionPatArr)>0){
		$tempArr =array();
		// 	GET ALL LASTPAID DATES OF PATIENTS
		$qry="Select patient_id, IF(lastPaymentDate!='0000-00-00', lastPaymentDate, date_of_service) as lastPaymentDate, lastPaymentDate FROM report_enc_detail WHERE patient_id IN(".$collectionPatArr.") AND del_status='0' ORDER BY lastPaymentDate";
		$rs =imw_query($qry);
		while($res=imw_fetch_array($rs)){
			if($tempArr[$res['patient_id']]['LAST_DATE']==0 || ($res['lastPaymentDate']!='0000-00-00' && $res['lastPaymentDate']>$arrLastPaidDate[$masterRes['patient_id']])){
				$arrLastPaidDate[$res['patient_id']] = $res['lastPaymentDate'];
				$tempArr[$res['patient_id']]['LAST_DATE'] = 1;
			}
		}

		$arrLastPaidPats= array_keys($arrLastPaidDate);
		$strLastPaidPats = implode(',', $arrLastPaidPats);
		$tempArr=array();
		$previousStatePats=array();
		if(empty($strLastPaidPats)===false){
			$qry="Select patient_id, previous_statement_id, created_date FROM previous_statement 
			WHERE patient_id IN(".$strLastPaidPats.")";
			$rs=imw_query($qry);
			while($res= imw_fetch_array($rs)){
				$pid = $res['patient_id'];
				$previousStatePats[$pid] = $pid;
	
				if($res['created_date'] > $arrLastPaidDate[$pid]){
					$arrPatStatments[$pid]+=1;
					$tempArr[$pid] = $pid;
				}
			}
			if(empty($statements) == false){
				foreach($arrPatStatments as $pid => $count){
					if($count < $statements){
						unset($masterPatArr[$pid]);
					}
				}
				//GET ALL PATIENT IDS THAT ARE NOT IN previous_statement TABLE
				$tempArr1 = array_intersect($previousStatePats, $tempArr);
				$resultArr = array_diff($arrLastPaidPats, $tempArr1);
				foreach($resultArr as $pid){
					unset($masterPatArr[$pid]);
				}
			}
			unset($tempArr);
			unset($resultArr);
			unset($previousStatePats);
		}		
	}

	$qryCollection = "Select report_enc_detail.charge_list_id,
			report_enc_detail.encounter_id,
			date_format(report_enc_detail.date_of_service,'$getSqlDateFormat') as date_of_service,
			report_enc_detail.primary_provider_id_for_reports as 'primaryProviderId',
			report_enc_detail.statement_status,
			date_format(report_enc_detail.statement_date,'$getSqlDateFormat') as statement_date,
			date_format(report_enc_detail.letter_sent_date,'$getSqlDateFormat') as letter_sent_date, 
			report_enc_detail.pat_due,
			users.lname as physicianLname,users.fname as physicianFname,
			users.mname as physicianMname,pos_facilityies_tbl.facilityPracCode,
			pos_facilityies_tbl.pos_facility_id,patient_data.id as patient_id,
			patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.next_action_status,
			patient_data.pat_account_status  
			FROM report_enc_detail 
			LEFT JOIN users on users.id = report_enc_detail.primary_provider_id_for_reports 
			LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = report_enc_detail.facility_id 
			LEFT JOIN patient_data on patient_data.id = report_enc_detail.patient_id 
			WHERE report_enc_detail.encounter_id IN(".$collectionEncStr.") AND report_enc_detail.del_status='0'";
	$qryCollection .= " ORDER BY patient_data.lname,report_enc_detail.date_of_service";
	$resCollection =imw_query($qryCollection);
	$mainResCollectionArr = array();
	while($rowCollection=imw_fetch_array($resCollection)){
		$encounter_id = $rowCollection['encounter_id'];
		$mainResCollectionArr[$encounter_id][] = $rowCollection;
	}	
}



// ---------------------------------

$main_colspan=10;
$sub_colspan=9;
$blank_td="";

$grandTotalsArr=array();
// ---------- NORMAL BLOCK-----
if(count($mainResArr)>0){
	$TotalBalanceArr = array();
	$totalPatients=0;
	//--- GET PROVIDER ID ARRAY ----
	$conditionChk = true;
	$encounterIdArr = array_keys($mainResArr);
	for($e=0;$e<count($encounterIdArr);$e++){
		$totalPatients++;
		$chkSelected='';
		$encounterId = $encounterIdArr[$e];
		//--- GET ENCUNTER DETAILS ----
		$encounterDetailsArr = $mainResArr[$encounterId];

		//---- GET PATIENT NAME --------
		$patient_name = $encounterDetailsArr[0]['lname'].', ';
		$patient_name .= $encounterDetailsArr[0]['fname'].' ';
		$patient_name .= $encounterDetailsArr[0]['mname'];
		$patient_name = ucfirst(trim($patient_name));
		if($patient_name[0] == ','){
			$patient_name = substr($patient_name,1);
		}
		$patient_id= $encounterDetailsArr[0]['patient_id'];
		//---- ENCOUNTER DETAILS ------
		$date_of_service = $encounterDetailsArr[0]['date_of_service'];
		$totalBalance = $masterPatBal[$patient_id];
		$statement_status = ($arrPatStatments[$patient_id]>0) ? $arrPatStatments[$patient_id] : '';
		$statementDate = ($arrMasterPatDet[$patient_id]['STATEMENT_DATE']!='00-00-0000') ? $arrMasterPatDet[$patient_id]['STATEMENT_DATE'] : '';
		$letterSentDate = ($arrMasterPatDet[$patient_id]['LETTER_SENT_DATE']!='00-00-0000') ? $arrMasterPatDet[$patient_id]['LETTER_SENT_DATE'] : '';
		$arAgingDays = $arrMasterPatDet[$patient_id]['AR_DAYS'];
		$next_action_code='';
		if($encounterDetailsArr[0]['next_action_status']>0){
			$next_action_code = $arrAllActionCodes[$encounterDetailsArr[0]['next_action_status']];
		}
		
		//---- GRAND TOATAL VARIABLES -------
		$TotalBalanceArr[] = $totalBalance;
		
		//---NUMBER FORMAT FOR SINGLE ENCOUNTER --------
		$totalBalance = numberFormat($totalBalance,2);
		
		if(in_array($encounterId,$chkbox_after_collection_arr)){ $chkSelected='checked="checked"';	}
		
		$data .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td style="text-align:center; width:50px" class="text_10" valign="top">$patient_id</td>
				<td style="text-align:left; width:110px" class="text_10" valign="top">$patient_name</td>
				<td style="text-align:center; width:70px" class="text_10" valign="top">$date_of_service</td>
				<td style="text-align:center; width:85px" class="text_10" valign="top">$statementDate</td>
				<td style="text-align:center; width:70px" class="text_10" valign="top">$letterSentDate</td>
				<td style="text-align:right; width:70px" class="text_10" valign="top">$statement_status</td>
				<td style="text-align:left; width:105px" class="text_10" valign="top">$next_action_code</td>
				<td style="text-align:right; width:60px" class="text_10" valign="top">$arAgingDays</td>
				<td style="text-align:right; width:100px" class="text_10" valign="top">$totalBalance</td>
			</tr>
DATA;

		$patient_id_csv="<a href='javascript:void();' onClick=new_window('".$GLOBALS['rootdir']."/reports/set_session.php?patient=".$patient_id."'); class='text_10'>".$patient_id."</a>";
		$patient_name_csv="<a href='javascript:void();' onClick=new_window('".$GLOBALS['rootdir']."/reports/set_session.php?patient=".$patient_id."'); class='text_10'>".$patient_name."</a>";

		$data_csv .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td style="text-align:left;" class="text_10" valign="top">
					<label class="checkbox checkbox-inline pointer">
						<input style="cursor:pointer;" type="checkbox" name="chk_box[]" id="chk_all_$encounterId" class="chk_all" value="$encounterId" $chkSelected>
						<label for="chk_all_$encounterId"></label>
					</label>
					<input type="hidden" name="pat_collection[]" value="$patient_id">
				</td>
				<td style="text-align:left;" class="text_10" valign="top">$patient_id_csv</td>
				<td style="text-align:left;" class="text_10" valign="top">$patient_name_csv</td>
				<td class="text_10" style="text-align:center;" valign="top">$date_of_service</td>
				<td class="text_10" style="text-align:center;" valign="top">$statementDate</td>
				<td class="text_10" style="text-align:center;" valign="top">$letterSentDate</td>
				<td style="text-align:right;" class="text_10" valign="top">$statement_status</td>
				<td style="text-align:left;" class="text_10" valign="top">$next_action_code</td>
				<td style="text-align:right;" class="text_10" valign="top">$arAgingDays</td>
				<td style="text-align:right;" class="text_10" valign="top">$totalBalance</td>
			</tr>
DATA;
		}
	}
	
	//---- GET GRAND TOTAL AMOUNT -------
	$TotalBalance = array_sum($TotalBalanceArr);

	$grandTotalsArr['patients'][]=$totalPatients;
	$grandTotalsArr['balance'][]=$TotalBalance;
	
	//---- NUMBER FORMAT FOR GRAND TOTAL AMOUNT ------
	$TotalBalance = $TotalBalance > 0 ? ''.showCurrency().''.number_format($TotalBalance,2) : '0.00';
	
	//--- GET HEADER DATA -----
	if(empty($data) == false){
		//-- OPERATOR INITIAL -------
		$authProviderNameArr = preg_split('/ /',strtoupper($_SESSION['authProviderName']));
		$opInitial = $authProviderNameArr[1][0];
		$opInitial .= $authProviderNameArr[0][0];
		$opInitial = strtoupper($opInitial);
		$printFile = true;
		$displDates='';
		if($Start_date!='' || $End_date !=''){ $displDates ="From $Sdate&nbsp;To $Edate"; }else{ $displDates="All"; }
		
		
		//--- PAGE HEADER DATA --
		$page_head_data =<<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:342px;">
						$dbtemp_name $Process Report
					</td>
					<td class="rptbx2" style="width:350px;">
						Selected DOS : $displDates
					</td>
					<td class="rptbx3" style="width:350px;">
						Created by : $opInitial on $curDate
					</td>
				</tr>	
				<tr class="rpt_headers">
					<td class="rptbx1" >
						Selected Groups : $group_name
					</td>
					<td class="rptbx2" >Selected Facility : $practice_name</td>
					<td class="rptbx3" >Selected Physician : $physician_name</td>
				</tr>
				<tr class="rpt_headers">
					<td class="rptbx1" >
						Next Action : $action_name
					</td>
					<td class="rptbx2" >A/R Aging Day : $aging_days</td>
					<td class="rptbx3" ></td>
				</tr>
			</table>
DATA;
		
		$pdf_file_data = <<<DATA
			<page backtop="18mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center; width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>		
				$page_head_data
				<table width="100%" cellpadding="0" cellspacing="1" border="0" bgcolor="#FFF3E8">
					<tr>
						<td style="text-align:center; width:120px" class="text_b_w">Pat. Id</td>
						<td style="text-align:left; width:150px" class="text_b_w">Patient Name</td>
						<td style="text-align:center; width:100px" class="text_b_w">Last DOS</td>
						<td style="text-align:center; width:100px" class="text_b_w">Statement Date</td>
						<td style="text-align:center; width:100px" class="text_b_w">Letter Date</td>
						<td style="text-align:center; width:100px" class="text_b_w">Statement #</td>
						<td style="text-align:center; width:160px" class="text_b_w">Action Code</td>
						<td style="text-align:center; width:100px" class="text_b_w">A/R Days</td>
						<td style="text-align:center; width:110px" class="text_b_w">Pat. Bal. Amt.</td>
					</tr>
				</table>
			</page_header>
			<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFF3E8">
				<tr bgcolor="#FFFFFF" height="0px">
					<td style="width:120px"></td>
					<td style="width:150px"></td>
					<td style="width:100px"></td>
					<td style="width:100px"></td>
					<td style="width:100px"></td>
					<td style="width:100px"></td>
					<td style="width:160px"></td>
					<td style="width:100px"></td>
					<td style="width:110px"></td>
				</tr>				
				$data
				
				<tr>
					<td align="left" class="text_10b" colspan="3">Total Number of Patients : $totalPatients</td>
					<td align="right" class="text_10b" colspan="5">Total : </td>
					<td align="right" class="text_10b">$TotalBalance</td>
				</tr>
					
			</table>
			</page>
DATA;
		
		//--- CSV FILE CONTENT ---
		$csv_file_content = <<<DATA
			$page_head_data
			<form action="" method="post" name="frm_csv">
			<table class="rpt_table rpt rpt_table-bordered table" style="width:100%">
				<tr>
					<td class="text_b_w">
						<label class="checkbox checkbox-inline pointer">
							<input style="cursor:pointer;" type="checkbox" name="chk_box" id="chk_box" onclick="chk_all_fun(this.checked,'');">
							<label for="chk_box"></label>
						</label>
					</td>
					<td class="text_b_w" style="text-align:center;">Pat. Id</td>
					<td class="text_b_w" style="text-align:center;">Patient Name</td>
					<td class="text_b_w" style="text-align:center;">Last DOS</td>
					<td class="text_b_w" style="text-align:center;">Statement Date</td>
					<td class="text_b_w" style="text-align:center;">Letter Sent Date</td>
					<td class="text_b_w" style="text-align:center;">Statement #</td>
					<td class="text_b_w" style="text-align:center;">Action Code</td>
					<td class="text_b_w" style="text-align:center;">A/R Days</td>
					<td class="text_b_w" style="text-align:center;">Total Pat. Bal.</td>
				</tr>
				$data_csv
				
				<tr bgcolor="#FFFFFF">
					<td style="text-align:left;" class="text_10b" colspan="3">Total Number of Patients : $totalPatients</td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;" class="text_10b">Total : </td>
					<td style="text-align:right;" class="text_10b">$TotalBalance</td>
				</tr>
						
			</table>
		</form>
DATA;
	}
	
// ------------- Block for Under Collection Records 
if(count($mainResCollectionArr)>0){
	$data = $data_csv='';
	$TotalBalanceArr = array();
	$totalPatients=0;
	//--- GET PROVIDER ID ARRAY ----
	$conditionChk = true;
	$encounterIdArr = array_keys($mainResCollectionArr);
	for($e=0;$e<count($encounterIdArr);$e++){
		$totalPatients++;
		$chkSelected='';
		$encounterId = $encounterIdArr[$e];
		//--- GET ENCUNTER DETAILS ----
		$encounterDetailsArr = $mainResCollectionArr[$encounterId];

		//---- GET PATIENT NAME --------
		$patient_name = $encounterDetailsArr[0]['lname'].', ';
		$patient_name .= $encounterDetailsArr[0]['fname'].' ';
		$patient_name .= $encounterDetailsArr[0]['mname'];
		$patient_name = ucfirst(trim($patient_name));
		if($patient_name[0] == ','){
			$patient_name = substr($patient_name,1);
		}
		$patient_id= $encounterDetailsArr[0]['patient_id'];
		//---- ENCOUNTER DETAILS ------
		$date_of_service = $encounterDetailsArr[0]['date_of_service'];
		$totalBalance = $collectionPatDue[$patient_id];
		$statement_status = ($arrPatStatments[$patient_id]>0) ? $arrPatStatments[$patient_id] : '';
		$statementDate = ($arrTotalPatCollDet[$patient_id]['STATEMENT_DATE']!='00-00-0000') ? $arrTotalPatCollDet[$patient_id]['STATEMENT_DATE'] : '';
		$letterSentDate = ($arrTotalPatCollDet[$patient_id]['LETTER_SENT_DATE']!='00-00-0000') ? $arrTotalPatCollDet[$patient_id]['LETTER_SENT_DATE'] : '';
		$arAgingDays = $arrTotalPatCollDet[$patient_id]['AR_DAYS'];
		$next_action_code='';
		if($encounterDetailsArr[0]['next_action_status']>0){
			$next_action_code = $arrAllActionCodes[$encounterDetailsArr[0]['next_action_status']];
		}
		
		//---- GRAND TOATAL VARIABLES -------
		
		//---NUMBER FORMAT FOR SINGLE ENCOUNTER --------
		$totBal = $totalBalance;
		$totalBalance = numberFormat($totalBalance,2);
		
		if(in_array($encounterId,$chkbox_after_collection_arr)){ $chkSelected='checked="checked"';	}

		$data .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td style="text-align:center; width:50px" class="text_10" valign="top">$patient_id</td>
				<td style="text-align:left; width:110px" class="text_10" valign="top">$patient_name</td>
				<td style="text-align:center; width:70px" class="text_10" valign="top">$date_of_service</td>
				<td style="text-align:center; width:85px" class="text_10" valign="top">$statementDate</td>
				<td style="text-align:center; width:70px" class="text_10" valign="top">$letterSentDate</td>
				<td style="text-align:right; width:70px" class="text_10" valign="top">$statement_status</td>
				<td style="text-align:left; width:105px" class="text_10" valign="top">$next_action_code</td>
				<td style="text-align:right; width:60px" class="text_10" valign="top">$arAgingDays</td>
				<td style="text-align:right; width:100px" class="text_10" valign="top">$totalBalance</td>
			</tr>
DATA;

		$patient_id_csv="<a href='javascript:void();' onClick=new_window('".$GLOBALS['rootdir']."/reports/set_session.php?patient=".$patient_id."'); class='text_10'>".$patient_id."</a>";
		$patient_name_csv="<a href='javascript:void();' onClick=new_window('".$GLOBALS['rootdir']."/reports/set_session.php?patient=".$patient_id."'); class='text_10'>".$patient_name."</a>";

		$data_csv .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td style="text-align:left;" class="text_10" valign="top">
					<label class="checkbox checkbox-inline pointer">
						<input style="cursor:pointer;" type="checkbox" name="chk_box[]" id="chk_all_$encounterId" class="chk_all" value="$encounterId" $chkSelected>
						<label for="chk_all_$encounterId"></label>
					</label>
					<input type="hidden" name="pat_collection[]" value="$patient_id">
				</td>
				<td style="text-align:left;" class="text_10" valign="top">$patient_id_csv</td>
				<td style="text-align:left;" class="text_10" valign="top">$patient_name_csv</td>
				<td class="text_10" style="text-align:center;" valign="top">$date_of_service</td>
				<td class="text_10" style="text-align:center;" valign="top">$statementDate</td>
				<td class="text_10" style="text-align:center;" valign="top">$letterSentDate</td>
				<td style="text-align:right;" class="text_10" valign="top">$statement_status</td>
				<td style="text-align:left;" class="text_10" valign="top">$next_action_code</td>
				<td style="text-align:right;" class="text_10" valign="top">$arAgingDays</td>
				<td style="text-align:right;" class="text_10" valign="top">$totalBalance</td>
			</tr>
DATA;
		}
	
	//---- GET GRAND TOTAL AMOUNT -------
	$TotalBalance = array_sum($TotalBalanceArr);

	$grandTotalsArr['patients'][]=$totalPatients;
	$grandTotalsArr['balance'][]=$TotalBalance;
		
	//---- NUMBER FORMAT FOR GRAND TOTAL AMOUNT ------
	$TotalBalance = $TotalBalance > 0 ? ''.showCurrency().''.number_format($TotalBalance,2) : '0.00';
	
	//--- GET HEADER DATA -----
	if(empty($data) == false){
		//-- OPERATOR INITIAL -------
		$authProviderNameArr = preg_split('/ /',strtoupper($_SESSION['authProviderName']));
		$opInitial = $authProviderNameArr[1][0];
		$opInitial .= $authProviderNameArr[0][0];
		$opInitial = strtoupper($opInitial);
		$printFile = true;
		$displDates='';
		if($Sdate!='' || $Edate !=''){ $displDates ="From $Sdate&nbsp;To $Edate"; }
		
		//--- PAGE HEADER DATA --
		$page_head_data =<<<DATA
			<table class="rptbx3" width="100%" >
				<tr>
					<td style="width:1078px; text-align:left;" class="rptbx3">Patients Under Collection</td>
				</tr>
			</table>
DATA;

		$pdf_file_data.= <<<DATA
			<page backtop="13mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center; width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>		
				$page_head_data
				<table width="100%" cellpadding="0" cellspacing="1" border="0" bgcolor="#FFF3E8">
					<tr>
						<td style="text-align:center; width:120px" class="text_b_w">Pat. Id</td>
						<td style="text-align:left; width:150px" class="text_b_w">Patient Name</td>
						<td style="text-align:center; width:100px" class="text_b_w">Last DOS</td>
						<td style="text-align:center; width:100px" class="text_b_w">Statement Date</td>
						<td style="text-align:center; width:100px" class="text_b_w">Letter Date</td>
						<td style="text-align:center; width:100px" class="text_b_w">Statement #</td>
						<td style="text-align:center; width:160px" class="text_b_w">Action Code</td>
						<td style="text-align:center; width:100px" class="text_b_w">A/R Days</td>
						<td style="text-align:center; width:110px" class="text_b_w">Pat. Bal. Amt.</td>
					</tr>
				</table>
			</page_header>
			<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFF3E8">
				<tr bgcolor="#FFFFFF" height="0px">
					<td style="width:120px"></td>
					<td style="width:150px"></td>
					<td style="width:100px"></td>
					<td style="width:100px"></td>
					<td style="width:100px"></td>
					<td style="width:100px"></td>
					<td style="width:160px"></td>
					<td style="width:100px"></td>
					<td style="width:110px"></td>
				</tr>				
				$data
					
				<tr>
					<td align="left" class="text_10b" colspan="3">Total Number of Patients : $totalPatients</td>
					<td align="right" class="text_10b" colspan="5">Total : </td>
					<td align="right" class="text_10b">$TotalBalance</td>
				</tr>
						
			</table>
			</page>
DATA;
		
		//--- CSV FILE CONTENT ---
		$csv_file_content.= <<<DATA
			$page_head_data
			<form action="" method="post" name="frm_csv">
			<table class="rpt_table rpt rpt_table-bordered table" style="width:100%" id="tblUnderColl">
				<tr>
					<td width="10" class="text_b_w">
						<label class="checkbox checkbox-inline pointer">
							<input style="cursor:pointer;" type="checkbox" name="chk_all_u" id="chk_all_u" onclick="chk_all_fun(this.checked,'underCollection');">
							<label for="chk_all_u"></label>
						</label>
					</td>
					<td width="90" class="text_b_w" style="text-align:center;">Pat. Id</td>
					<td width="180" class="text_b_w" style="text-align:center;">Patient Name</td>
					<td width="101" class="text_b_w" style="text-align:center;">Last DOS</td>
					<td width="110" class="text_b_w" style="text-align:center;">Statement Date</td>
					<td width="110" class="text_b_w" style="text-align:center;">Letter Sent Date</td>
					<td width="120" class="text_b_w" style="text-align:center;">Statement #</td>
					<td width="120" class="text_b_w" style="text-align:center;">Action Code</td>
					<td width="120" class="text_b_w" style="text-align:center;">A/R Days</td>
					<td width="120" class="text_b_w" style="text-align:center;">Total Balance</td>
				</tr>
				$data_csv
				
				<tr bgcolor="#FFFFFF">
					<td style="text-align:left;" class="text_10b" colspan="3">Total Number of Patients : $totalPatients</td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;" class="text_10b">Total : </td>
					<td style="text-align:right;" class="text_10b">$TotalBalance</td>
				</tr>
					
			</table>
		</form>
DATA;
	}
}
// -- END Under collection Block		
if(array_sum($grandTotalsArr['patients'])>0){
		$grandPatients = array_sum($grandTotalsArr['patients']);
		$grandBalance = array_sum($grandTotalsArr['balance']);
		$grandBalance = $grandBalance > 0 ? ''.showCurrency().''.number_format($grandBalance,2) : '0.00';

		$pdf_file_data.= <<<DATA
			<table width="100%" border="0" cellpadding="10" cellspacing="1" bgcolor="#FFF3E8">
				<tr bgcolor="#FFFFFF" height="0px">
					<td style="width:120px"></td>
					<td style="width:150px"></td>
					<td style="width:100px"></td>
					<td style="width:100px"></td>
					<td style="width:100px"></td>
					<td style="width:100px"></td>
					<td style="width:160px"></td>
					<td style="width:100px"></td>
					<td style="width:110px"></td>
				</tr>				
				<tr>
					<td align="left" class="text_10b" colspan="3">Grand Total Patients : $grandPatients</td>
					<td align="right" class="text_10b" colspan="5"> Grand Total : </td>
					<td align="right" class="text_10b">$grandBalance</td>
				</tr>
					
			</table>
DATA;
		
		//--- CSV FILE CONTENT ---
		$csv_file_content.= <<<DATA
			<table width="100%" cellpadding="5" cellspacing="1" border="0" bgcolor="#FFF3E8">
				<tr style="height:0px">
					<td width="10"></td>
					<td  width="90" ></td>
					<td  width="180" ></td>
					<td  width="101" ></td>
					<td  width="110" ></td>
					<td  width="110" ></td>
					<td  width="120" ></td>
					<td  width="120" ></td>
					<td  width="120" ></td>
					<td  width="120" ></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:left;" class="text_10b" colspan="3">Grand Total Patients : $grandPatients</td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;" class="text_10b">Grand Total : </td>
					<td style="text-align:right;" class="text_10b">$grandBalance</td>
				</tr>
					
			</table>
DATA;

// SET ARRAY FOR PRINT LETTER
$strTotBalance = htmlentities(serialize($arrTotBalance));
$csv_file_content.='<input type="hidden" name="patBalance" id="patBalance" value="'.$strTotBalance.'">';

}else{
	$csv_file_content = '<div class="text-center alert alert-info">No Record Found.</div>';
}


$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
echo $styleHTML.$csv_file_content;

$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
$strHTML = $stylePDF.$pdf_file_data;

$file_location = write_html($strHTML);
//$dbtemp_name_CSV=$styleHTML.$csv_file_content;
?>