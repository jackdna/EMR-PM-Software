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

ini_set("memory_limit","3072M");
set_time_limit (0);

$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$FCName= $_SESSION['authId'];

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
	$str_insuranceGrp=implode("|",$insuranceGrp);
	$str_insuranceComp=implode(",",$ins_carriers);
	
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
	
	
	// Merge CPT Code and CPT Categories
	$tmpCptCodeArr = array();
	$cpt_code_id= array_filter($cpt_code_id);
	$cpt_cat_id= array_filter($cpt_cat_id);
	
	$cpt_categories=(sizeof($cpt_cat_id)>0)? implode('|', $cpt_cat_id) :'';
	$cpt_ids=(sizeof($cpt_code_id)>0)? implode(',', $cpt_code_id) :'';
	$cpt_code_id_title = implode(',',$cpt_code_id);
	$cpt_cat_id_title  = implode(',',$cpt_cat_id);
	
	if(empty($cpt_code_id)==false) $tmpCptCodeArr[] = implode(',',$cpt_code_id);
	if(empty($cpt_cat_id)==false ) $tmpCptCodeArr[] = implode(',',$cpt_cat_id);
	
	$tmpCptCodeStr = implode(',',$tmpCptCodeArr);
	$cpt_code_id = explode(',',$tmpCptCodeStr);
	
	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($sc_name)<=0 && isPosFacGroupEnabled()){
		$sc_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($sc_name)<=0){
			$sc_name[0]='NULL';
		}
	}
	
	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$facility_name= (sizeof($sc_name)>0) ? implode(',',$sc_name) : '';
	$Physician= (sizeof($Physician)>0) ? implode(',',$Physician) : '';
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';
	$cpt_code_id= (sizeof($cpt_code_id)>0) ? implode(',',$cpt_code_id) : '';
	$insuranceGrp= (sizeof($insuranceGrp)>0) ? implode(',',$insuranceGrp) : '';
	$insuranceName= (sizeof($arrInsurance)>0) ? implode(',',$arrInsurance) : '';
	//---------------------------------------

	//--- CHANGE DATE FORMAT -------
	if($Start_date != '' && $End_date != ''){
		$Start_date = getDateFormatDB($Start_date);
		$End_date = getDateFormatDB($End_date);	
	}

	
	//--- GET GROUP NAME ---
	$group_name = "All Selected";
	if(empty($grp_id) === false){
		$group_query = "select name from groups_new where gro_id = '$grp_id'";
		$groupQryRs = imw_query($group_query);		
		$groupQryRes = imw_fetch_assoc($groupQryRs);
		if(count($groupQryRes)>0){
			$group_name = $groupQryRes[0]['name'];
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
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}						
	// ------------------------------
		
	
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
	$arrAllInsCompanies['SELF PAY']='SELF PAY';
	$arrAllInsCompaniesCSV['SELF PAY']='SELF PAY';
	$qry="Select id as insCompId,in_house_code as insCompINHouseCode,name as insCompName FROM insurance_companies";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$id = $res['insCompId'];
		$insName = $res['insCompINHouseCode'];
		$insNameCSV = $res['insCompINHouseCode'];
		if(trim($insName) == ''){
			$insName = substr($res['insCompName'],0,20);
			$insNameCSV = $res['insCompName'];
		}
		$arrAllInsCompanies[$id] = $insName;
		$arrAllInsCompaniesCSV[$id] = $insNameCSV;
	}
	
	//CPT CODES
	$procedureCodeArr=array();
	$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl WHERE cpt_prac_code != '' order by cpt_prac_code asc";
	$res = imw_query($qry);
	while ($row = imw_fetch_array($res)) {
		$cpt_fee_id = $row['cpt_fee_id'];
		$cpt_prac_code = $row['cpt_prac_code'];

		$procedureCodeArr[$cpt_fee_id] = $cpt_prac_code;
	}

	//MOFIDIERS
	$arModifiers = array();
	$qry = "SELECT modifiers_id, mod_prac_code, delete_status FROM modifiers_tbl ORDER BY mod_prac_code";
	$rs = imw_query($qry);
	while ($row = imw_fetch_assoc($rs)) {
		$arrModifiers[$row['modifiers_id']] = $row['mod_prac_code'];
	}

	//GET INSURANCE GROUP DROP DOWN
	$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
	$arrAllInsGroups[0] = 'No Insurance';
	$arrAllInsGroups['SELF PAY'] = 'SELF PAY';
	$arrAllInsGroups['Group Not Mapped'] = 'Group Not Mapped';
	$arrInsMapInsGroups[0]='0';
	$arrInsMapInsGroups['SELF PAY'] = 'SELF PAY';
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
	$mainResArr = array();
	//--- GET ALL CHARGES ----
	$qry = "Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.facility_id, main.pri_ins_id, main.sec_ins_id, main.tri_ins_id, main.operator_id,
	(main.charges * main.units) as totalAmt, main.units, main.approved_amt,
	date_format(main.date_of_service,'".$dateFormat."') as date_of_service,
	main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	
	main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4,
	main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment,
	main.mod_id1, main.mod_id2, main.mod_id3, main.mod_id4,
	patient_data.id as patient_id,
	patient_data.lname,	patient_data.fname, patient_data.mname 
	FROM report_enc_detail main 
	JOIN patient_data on patient_data.id = main.patient_id 
	WHERE (main.date_of_service between '$Start_date' and '$End_date') AND main.del_status='0'";
	
	if(empty($facility_name) == false){
		$qry.= " and main.facility_id IN ($facility_name)";	
	}
	if(empty($grp_id) == false){
		$qry.= " and main.gro_id IN ($grp_id)";
	}
	if(empty($Physician) === false){
		$qry.= " and main.primary_provider_id_for_reports IN ($Physician)";
	}
	if(empty($credit_physician) === false){
		$qry.= " and main.sec_prov_id IN ($credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$qry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
	}	
	if(trim($cpt_code_id) != ''){
		$qry.= " AND main.proc_code_id in ($cpt_code_id)";
	}
	if(trim($insuranceName) != ''){
		$qry.= " and (main.pri_ins_id in ($insuranceName)
		or main.sec_ins_id in ($insuranceName)
		or main.tri_ins_id in ($insuranceName))";
	}
	$qry .=" ORDER BY patient_data.lname, patient_data.fname, main.date_of_service, main.encounter_id";
	$res=imw_query($qry);

	$main_encounter_id_arr = array();
	$facilityNameArr = array();
	$physician_initial_arr = array();
	$arrPatNoFacility=array();
	$chargeids_for_temp_query='';
	while($rs = imw_fetch_assoc($res)){
		$encounter_id = $rs['encounter_id'];
		$chgDetId = $rs['charge_list_detail_id'];

		$mainResultArr[$encounter_id][$chgDetId] = $rs;

		$arrInsOfEnc[$encounter_id][$rs['pri_ins_id']]['primary']=$rs['pri_ins_id'];
		$arrInsOfEnc[$encounter_id][$rs['sec_ins_id']]['secondary']=$rs['sec_ins_id'];
		$arrInsOfEnc[$encounter_id][$rs['tri_ins_id']]['tertiary']=$rs['tri_ins_id'];
		
		$arrChgDetIds[$chgDetId] = $chgDetId;
		$chargeids_for_temp_query.='('.$chgDetId.'),';
	}unset($rs);
	
	if(empty($chargeids_for_temp_query)==false){
		$chargeids_for_temp_query=substr($chargeids_for_temp_query,0, -1);
	}

	//TRANSACTIONS TABLE
	if(sizeof($arrChgDetIds)>0){
		
		//CREATE TEMP TABLE AND INSERT DATA
		$temp_join_part='';
		if(empty($chargeids_for_temp_query)==false){
			$tmp_table="reports_pro_payments_charge_ids_".time().'_'.$_SESSION["authId"];
			imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
			imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (charge_id INT)");
			imw_query("INSERT INTO $tmp_table (charge_id) VALUES ".$chargeids_for_temp_query);
			$temp_join_part=" INNER JOIN ".$tmp_table." t_tbl ON trans.charge_list_detail_id = t_tbl.charge_id";
		}

		if(empty($temp_join_part)==false){
		
			$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
			trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.parent_id,
			date_format(trans.trans_dop,'".$dateFormat."') as trans_dop, trans.trans_del_operator_id  
			FROM report_enc_trans trans 
			".$temp_join_part."
			WHERE trans.trans_type!='charges' 
			ORDER BY trans.trans_dot, trans.trans_dot_time";
			$rs=imw_query($qry);
			while($res = imw_fetch_assoc($rs)){
				$report_trans_id=$res['report_trans_id'];
				$encounter_id= $res['encounter_id'];
				$chgDetId= $res['charge_list_detail_id'];
				$insCompId = $res['trans_ins_id'];
				$trans_type= strtolower($res['trans_type']);
				$trans_by= strtolower($res['trans_by']);	
				
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
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
						}
						$paidForProc+=$prevFetchedAmt; 
						
						if($trans_by == 'patient' || $trans_by == 'res. party'){
							$patPayDetArr[$chgDetId]['patPaid']+= $paidForProc;
							$patPayDetArr[$chgDetId]['lastPatDOP'] = $res['trans_dop'];
						}else if($trans_by == 'insurance'){
							if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
								$patPayDetArr[$chgDetId]['insPaid']+= $paidForProc;
		
								//GET PAYMENTS BASED ON PRIMARY/SEC/TER COMPMAY
								if($arrInsOfEnc[$encounter_id][$insCompId]['primary']){
									$patPayDetArr[$chgDetId]['priPaid']+= $paidForProc;
									if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastPriDOP'] = $res['trans_dop'];
								}else if($arrInsOfEnc[$encounter_id][$insCompId]['secondary']){
									$patPayDetArr[$chgDetId]['secPaid']+= $paidForProc;
									if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastSecDOP'] = $res['trans_dop'];
								}else if($arrInsOfEnc[$encounter_id][$insCompId]['tertiary']){
									$patPayDetArr[$chgDetId]['secPaid']+= $paidForProc;
									if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastSecDOP'] = $res['trans_dop'];
									}else{
									$patPayDetArr[$chgDetId]['priPaid']+= $paidForProc;	
									if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastPriDOP'] = $res['trans_dop'];									
								}
							}else{
								$paidForProc=0;
							}
						}
						$mainEncounterPayArr[$chgDetId]+= $paidForProc;
										
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
						$crddbtamt+=$prevFetchedAmt; 
						
						if($trans_by=='insurance'){
							if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
								//GET PAYMENTS BASED ON PRIMARY/SEC/TER COMPMAY
								if($arrInsOfEnc[$encounter_id][$insCompId]['primary']){
									$patPayDetArr[$chgDetId]['priPaid']+= $crddbtamt;
									if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastPriDOP'] = $res['trans_dop'];
								}else if($arrInsOfEnc[$encounter_id][$insCompId]['secondary']){
									$patPayDetArr[$chgDetId]['secPaid']+= $crddbtamt;
									if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastSecDOP'] = $res['trans_dop'];
								}else if($arrInsOfEnc[$encounter_id][$insCompId]['tertiary']){
									$patPayDetArr[$chgDetId]['secPaid']+= $crddbtamt;
									if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastSecDOP'] = $res['trans_dop'];
								}else{
									$patPayDetArr[$chgDetId]['priPaid']+= $crddbtamt;
									if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastPriDOP'] = $res['trans_dop'];									
								}
							}
						}else{
							$patPayDetArr[$chgDetId]['patPaid']+= $crddbtamt;				
							if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastPatDOP'] = $res['trans_dop'];
						}
						
					break;
					case 'default_writeoff':
						$normalWriteOffAmt[$chgDetId]= $res['trans_amount'];
					break;
					case 'write off':
					case 'discount':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$writte_off_arr[$chgDetId]+= $res['trans_amount'];
					break;
					case 'over adjustment':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
					break;
					case 'adjustment':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
					break;
					case 'returned check':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
					break;
					case 'refund':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						}
						$res['trans_amount']+=$prevFetchedAmt;
						
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
					break;
				}
			}
			//DROP TEMP TABLE
			imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
		}
	}
}



$cols=23;
$w_cols=100/23;
$w_cols.='%';

//HTML CREATION
if(sizeof($mainResultArr)>0){

	//GET SELECTED
	$selgroup = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
	$selFac = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);	
	$selPhy = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
	$selCrPhy = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
	$selInsurance = $CLSReports->report_display_selected($insuranceName,'insurance',1, $insurance_cnt);
	$selCPT = $CLSReports->report_display_selected($cpt_code_id,'cpt_code',1, $allCPTCount);	
	
	//MAKING OUTPUT DATA
	$file_name="procedure_payments_extract.csv";
	$csv_file_name= write_html("", $file_name);

	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');

	$arr[]="Procedure Payments Report";
	$arr[]="$dayReport (DOS) From : $Sdate To : $Edate"."";
	$arr[]="Created by $opInitial on $curDate";
	$arr[]="Group : $selgroup";
	$arr[]="Facility : $selFac";
	$arr[]="Physician : $selPhy";
	$arr[]="Crediting Physician : $selCrPhy";
	$arr[]="Insurance : $selInsurance";
	$arr[]="CPT : $selCPT";
	$arr[]="\n";
	fputcsv($fp,$arr, ",","\"");

	$arr=array();
	$arr[]="CPT";
	$arr[]="Modifiers";
	$arr[]="Provider";
	$arr[]="Pt Name";
	$arr[]="Pt ID";
	$arr[]="DOS";
	$arr[]="Primary Ins";
	$arr[]="Secondary Ins";
	$arr[]="Units";
	$arr[]="Charges";
	$arr[]="Allowed";
	$arr[]="Pri Ins Paid";
	$arr[]="Paid Date";
	$arr[]="Sec Ins Paid";
	$arr[]="Paid Date";
	$arr[]="Pt Paid";
	$arr[]="Paid Date";
	$arr[]="Total Paid";
	$arr[]="Credit";
	$arr[]="Adjustment";
	$arr[]="Pt Due";
	$arr[]="Ins Due";
	$arr[]="Balance";
	fputcsv($fp,$arr, ",","\"");
	
	
	foreach($mainResultArr as $encounter_id => $chargeDetData){	

		foreach($chargeDetData as $charge_list_detail_id =>$encounterDataArr){
			$printFile=true;
			
			$date_of_service = $encounterDataArr['date_of_service'];
			$postedDate = $encounterDataArr['postedDate'];
			$doctor_id=$encounterDataArr['primaryProviderId'];

			if(empty($Physician) === true && empty($credit_physician) === false){
				$doctor_id = $encounterDataArr['sec_prov_id'];
			}			
			
			$providerName= $providerNameArr[$doctor_id];
			
			$procedureName = $procedureCodeArr[$encounterDataArr['proc_code_id']];
			$patientName = core_name_format($encounterDataArr['lname'], $encounterDataArr['fname'], $encounterDataArr['mname']);
			$patient_id = $encounterDataArr['patient_id'];
			
			//MODIFIERS
			$strModifiers='';
			$arrMod=array();
			$arrMod[]= $arrModifiers[$encounterDataArr['mod_id1']];
			$arrMod[]= $arrModifiers[$encounterDataArr['mod_id2']];
			$arrMod[]= $arrModifiers[$encounterDataArr['mod_id3']];
			$arrMod[]= $arrModifiers[$encounterDataArr['mod_id4']];
			$arrMod=array_filter($arrMod);
			$strModifiers=(sizeof($arrMod)>0)? implode(', ',$arrMod): '';
			
			//--- GET INSURANCE COMPANY NAME ----
			$primaryInsurance = $arrAllInsCompaniesCSV[$encounterDataArr['pri_ins_id']];	
			$secondaryInsurance = $arrAllInsCompaniesCSV[$encounterDataArr['sec_ins_id']];

			// BALANCE DEDUCT BY OVER PAYMENT
			if($encounterDataArr["proc_balance"]>0){
				$totalBalance = $encounterDataArr['proc_balance'];
			}else{
				if($encounterDataArr['over_payment']>0){
					$totalBalance = - $encounterDataArr['over_payment']; //overPayment
				}else{
					$totalBalance = $encounterDataArr['proc_balance'];
				}
			}			
			$insuranceDue = $encounterDataArr['pri_due']+$encounterDataArr['sec_due']+$encounterDataArr['tri_due'];
			$insuranceDue = ($insuranceDue<0) ? 0 : $insuranceDue; 
			$patientDue = $encounterDataArr['pat_due'];
			$patientDue = ($patientDue<0) ? 0 : $patientDue;
			$patientDue = ($totalBalance<=0) ? 0 : $patientDue;

			//--- ENCOUNTER CHARGES AMOUNT -----
			$units='';
			$totalAmount = $encounterDataArr['totalAmt'];
			$units = intval($encounterDataArr['units']);

			//ALLOWED AMOUNT
			$approvedAmt = $encounterDataArr['approved_amt'];			
			
			//PAYMENTS
			$paidForProc = $patPaidAmt = $priPaidAmt=$secPaidAmt=$insPaidAmt = 0 ;
			$lastPriPaidDate=$lastSecPaidDate=$lastPatPaidDate='';

			$priPaidAmt=$patPayDetArr[$charge_list_detail_id]['priPaid'];
			$lastPriPaidDate=$patPayDetArr[$charge_list_detail_id]['lastPriDOP'];
			$secPaidAmt=$patPayDetArr[$charge_list_detail_id]['secPaid'];
			$lastSecPaidDate=$patPayDetArr[$charge_list_detail_id]['lastSecDOP'];
			$patPaidAmt=$patPayDetArr[$charge_list_detail_id]['patPaid'];
			$lastPatPaidDate=$patPayDetArr[$charge_list_detail_id]['lastPatDOP'];
			$paidForProc=$priPaidAmt+$secPaidAmt+$patPaidAmt;

			// CREDIT AMOUNT and TOTAL BALANCE
			$creditAmount = $encounterDataArr['over_payment'];
			// ADJUSTMENT AMOUNT
			$adjustmentAmount = $normalWriteOffAmt[$charge_list_detail_id] + $writte_off_arr[$charge_list_detail_id] + $arrAdjustmentAmt[$charge_list_detail_id];
									
			$arrTotals['units']+=$units;
			$arrTotals['charges']+=$totalAmount;
			$arrTotals['approved']+=$approvedAmt;
			$arrTotals['pat_paid']+=$patPaidAmt;
			$arrTotals['pri_paid']+=$priPaidAmt;
			$arrTotals['sec_paid']+=$secPaidAmt;
			$arrTotals['total_paid']+=$paidForProc;
			$arrTotals['credit']+=$creditAmount;
			$arrTotals['adjustment']+=$adjustmentAmount;
			$arrTotals['pat_due']+=$patientDue;
			$arrTotals['ins_due']+=$insuranceDue;
			$arrTotals['balance']+=$totalBalance;
			
			//--- NUMBER FORMAT FOR PATIENT AMOUNT ---------
			$totalAmount = $CLSReports->numberFormat($totalAmount,2);
			$approvedAmt = $CLSReports->numberFormat($approvedAmt,2,1);
			$patPaidAmt = $CLSReports->numberFormat($patPaidAmt,2);
			$priPaidAmt = $CLSReports->numberFormat($priPaidAmt,2);
			$secPaidAmt = $CLSReports->numberFormat($secPaidAmt,2);
			$paidForProc = $CLSReports->numberFormat($paidForProc,2);
			$creditAmount = $CLSReports->numberFormat($creditAmount,2);
			$adjustmentAmount = $CLSReports->numberFormat($adjustmentAmount,2);
			$patientDue = $CLSReports->numberFormat($patientDue,2);
			$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
			$totalBalance = $CLSReports->numberFormat($totalBalance,2);
			
			$html_part.='<tr>
				<td class="text_10" style="background:#FFFFFF;">'.$procedureName.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$strModifiers.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$providerName.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$patientName.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$patient_id.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$date_of_service.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$primaryInsurance.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$secondaryInsurance.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$units.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$totalAmount.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$approvedAmt.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$priPaidAmt.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$lastPriPaidDate.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$secPaidAmt.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$lastSecPaidDate.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$patPaidAmt.'</td>
				<td class="text_10" style="background:#FFFFFF;">'.$lastPatPaidDate.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$paidForProc.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$creditAmount.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$adjustmentAmount.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$patientDue.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$insuranceDue.'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right;">'.$totalBalance.'</td>
			</tr>';
			

			$arr=array();
			$arr[]=$procedureName;
			$arr[]=$strModifiers;
			$arr[]=$providerName;
			$arr[]=$patientName;
			$arr[]=$patient_id;
			$arr[]=$date_of_service;
			$arr[]=$primaryInsurance;
			$arr[]=$secondaryInsurance;
			$arr[]=$units;
			$arr[]=$totalAmount;
			$arr[]=$approvedAmt;
			$arr[]=$priPaidAmt;
			$arr[]=$lastPriPaidDate;
			$arr[]=$secPaidAmt;
			$arr[]=$lastSecPaidDate;
			$arr[]=$patPaidAmt;
			$arr[]=$lastPatPaidDate;
			$arr[]=$paidForProc;
			$arr[]=$creditAmount;
			$arr[]=$adjustmentAmount;
			$arr[]=$patientDue;
			$arr[]=$insuranceDue;
			$arr[]=$totalBalance;
			
			fputcsv($fp,$arr, ",","\"");						
		}
	}

	$html_part.='<tr>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;" colspan="8">Total:</td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$arrTotals['units'].'</td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['charges'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['approved'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['pri_paid'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF;"></td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['sec_paid'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF;"></td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['pat_paid'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF;"></td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['total_paid'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['credit'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['adjustment'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['pat_due'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['ins_due'],2).'</td>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;">'.$CLSReports->numberFormat($arrTotals['balance'],2).'</td>
	</tr>';	
}
fclose($fp);

$HTMLCreated=0;
if($printFile == true){
	
	$html_file_data='<table class="rpt_table rpt rpt_table-bordered rpt_padding">
    <tr >
        <td style="text-align:left;" class="rptbx1" width="33%">Procedure Payments Report</td>
        <td style="text-align:left;" class="rptbx2" width="34%">'.$dayReport.' (DOS) From : '.$Sdate.' To : '.$Edate.'</td>
        <td style="text-align:left;" class="rptbx3" width="33%">Created by '.$opInitial.' on '.$curDate.'</td>
    </tr>
    <tr>
        <td class="rptbx1">Group : '.$selgroup.'</td>
        <td class="rptbx2">Facility : '.$selFac.'</td>
        <td class="rptbx3">Physician : '.$selPhy.'&nbsp;&nbsp;&nbsp;Cr. Phy.: '.$selCrPhy.'</td>
    </tr>
    <tr>
        <td class="rptbx1">Insurance : '.$selInsurance.'</td>
        <td class="rptbx2">CPT : '.$selCPT.'</td>
        <td class="rptbx3"></td>
    </tr>
	</table>
	<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
		<tr>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">CPT</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';">Modifiers</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Provider</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Pt Name</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Pt ID</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >DOS</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Primary Ins</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Secondary Ins</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Units</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Charges</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Allowed</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Pri Ins Paid</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Paid Date</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Sec Ins Paid</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Paid Date</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Pt Paid</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Paid Date</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Total Paid</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Credit</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Adjustment</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Pt Due</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Ins Due</td>
			<td class="text_b_w" style="text-align:center; width:'.$w_cols.';" >Balance</td>
		</tr>
		'.$html_part.'
	</table>';
	
	
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$html_file_data= $styleHTML.$html_file_data;
	
	if($callFrom!='scheduled'){
		if($output_option=='output_csv'){
			echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
		}else{
			echo $html_file_data;	
		}	
	}
}else{
	if($callFrom!='scheduled'){
		echo $html_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>
