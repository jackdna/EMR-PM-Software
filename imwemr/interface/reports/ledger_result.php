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

//check is pure self pay (only)selected
if($pureSelfPay==1)
{
	//if this option is selected then we need to ignore selected insurance companies
	$ins_carriers=$insuranceGrp=array();
}

$printFile = true;
if($_POST['form_submitted']){
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport!='Date'){ $Start_date = $End_date= $chk_src_date = ''; }
	if($dayReport=='Daily'){
		$Start_date = $End_date = $chk_src_date = date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date = $chk_src_date = date($phpDateFormat);
		
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date = $chk_src_date=  date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
		$End_date = $chk_src_date = $arrDateRange['QUARTER_DATE_END'];
	}
	if($dayReport=='Date'|| $dayReport=='Weekly' || $dayReport=='Weekly' || $dayReport=='Quarterly'){ 
		if($End_date == ""){
			$chk_src_date = getDateFormatDB(date($phpDateFormat));
		}else{
			$chk_src_date = getDateFormatDB($End_date); 		
		} 
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
	

	$arrSelOprators=array();
	if(empty($operator_id)===false){
		$arrSelOprators=explode(',', $operator_id);
		$arrSelOprators=array_combine($arrSelOprators, $arrSelOprators);
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
	$sc_name= (sizeof($facility_id)>0) ? implode(',',$facility_id) : '';
	$Physician= (sizeof($filing_provider)>0) ? implode(',',$filing_provider) : '';
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';
	$adjustmentId= (sizeof($adjustmentId)>0) ? implode(',',$adjustmentId) : '';
	$insuranceGrp= (sizeof($insuranceGrp)>0) ? implode(',',$insuranceGrp) : '';
	$insuranceName= (sizeof($arrInsurance)>0) ? implode(',',$arrInsurance) : '';
	$operatorName= (sizeof($operator_id)>0) ? implode(',',$operator_id) : '';
	$departmentIds= (sizeof($department)>0) ? implode(',',$department) : '';
	$str_cc_type='';
	if(sizeof($cc_type)>0){ //IF CREDIT CARD
		$str_cc_type= "'".implode("','", $cc_type)."'";
	}				
	
	//---------------------------------------

	// MAKE Search Criteria Vars
	$varCriteria=$grp_id.'~'.$sc_name.'~'.$Physician.'~'.$operatorName.'~'.$departmentIds.'~'.$insuranceName;
	$varCriteria.='~'.$checkNo.'~'.$amtCriteria.'~'.$checkAmt.'~'.$DateRangeFor.'~'.$dayReport.'~'.$Sdate.'~'.$Edate;
	$varCriteria.='~'.$reportType.'~'.$reportWise.'~'.$processReport.'~'.$batchFiles.'~'.$viewBy.'~'.$insuranceGrp.'~'.$pureSelfPay;
	$varCriteria.='~'.$pay_method.'~'.$str_cc_type.'~'.$output_option.'~'.$credit_physician;
	//---------------------


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

	//GET SCHEDULE FACILITIES
	$arrPosFacOfSchFac=array();
	$arrSchFacilites=array();
	$arrSchFacilites[0]='No Pay Location';
	$rs=imw_query("Select id, name, fac_prac_code FROM facility ORDER BY name");	
	while($res=imw_fetch_assoc($rs)){
		$arrPosFacOfSchFac[$res['id']] = $res['fac_prac_code'];
		$arrSchFacilites[$res['id']] = $res['name'];
	}unset($rs);
		

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
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

	//GET ALL DEPARTMENT CODES
	$arrDeptNames[0] = 'Undefined';
	$qry="Select DepartmentId, DepartmentCode, DepartmentDesc FROM department_tbl";
	$rs=imw_query($qry);
	while($res = imw_fetch_array($rs)){
		$arrDeptNames[$res['DepartmentId']] = $res['DepartmentDesc'].' - '.$res['DepartmentCode'];
	}
	asort($arrDeptNames);
	
	//GET ALL MODIFIERS
	$arrAllModifiers = array();
	$rs= imw_query("Select modifiers_id, mod_prac_code FROM modifiers_tbl");
	while($res = imw_fetch_array($rs)){
		$arrAllModifiers[$res['modifiers_id']] = $res['mod_prac_code'];
	}


	$reptBy = 'encounterIds';
	$reptByCredit = 'encounterIds';
	
	$reptByForFun='';
	if($DateRangeFor =='date_of_payment'){ $reptByForFun='dop';}
	else if($DateRangeFor =='transaction_date'){ $reptByForFun='dot';}


	// IF SEARCH BY BATCH FILES
	$arrBatchIds=array();
	$arrBatchIds=$strBatchIds='';
	if(empty($batchFiles)==false){
		$arrTrackingNos= explode(',',$batchFiles);
		foreach($arrTrackingNos as $val){
			$trackNo="'".trim($val)."'";
			$tempArr[$trackNo]=$trackNo;
		}
		$batchFilesSearch = implode(',', $tempArr); 
		$qry="Select batch_id FROM manual_batch_file WHERE tracking IN(".$batchFilesSearch.") AND del_status='0' AND post_status='1'";
		$rs=imw_query($qry);
		while($res =imw_fetch_array($rs)){
			$arrBatchIds[$res['batch_id']]=$res['batch_id'];
		}

		if(sizeof($arrBatchIds)>0){
			$strBatchIds=implode(',', $arrBatchIds);
			$batchQryEnc = " AND patChg.batch_id IN(".$strBatchIds.")";
			$batchQryEnc1 = " AND patient_chargesheet_payment_info.batch_id IN(".$strBatchIds.")";
			$batchQryEncDet1 = " AND patient_charges_detail_payment_info.batch_id IN(".$strBatchIds.")";
			$batchQryEncDetForChk = " AND trans.batch_id IN(".$strBatchIds.")";
		}
	}

	//IF SEARCH BY CHECK#/CHECK AMT/PAYMENT METHOD
	$strChSearchEnc='';
	$chAmtQryCriteria='';
	$arrChSearchEnc=array();
	$arrChSearchEnc[1]=1;	//PURPOSE:	IF STARTING QUERIES FOUND NO RESULT THEN MAIN QUERY WILL NOT FETCH ANY ROW
	if($checkNo!='' || $checkAmt>0 || empty($pay_method)==false || sizeof($cc_type)>0){
	
		if(empty($Start_date)==false && empty($End_date)==false){
			if($DateRangeFor=='DOR'){
				$chAmtQryCriteria.=" AND (trans.trans_dop BETWEEN '".$Start_date."' AND '".$End_date."')";
			}else if($DateRangeFor=='transaction_date'){
				$chAmtQryCriteria.=" AND (trans.trans_dot BETWEEN '".$Start_date."' AND '".$End_date."')";
			}
		}
		if($checkNo!=''){
			$checkNoTemp = explode(',',$checkNo);
			$or='';
			for($i=0;$i< sizeof($checkNoTemp); $i++){
				$checkNoTemp[$i]=strtoupper(trim($checkNoTemp[$i]));
				if(empty($checkNoTemp[$i])==false){
					if($i>0)$or=' OR ';
					$checkTemp[$checkNoTemp[$i]] = $checkNoTemp[$i];
					$chAmtQryPart.= $or." UPPER(TRIM(trans.check_number)) LIKE '%".$checkNoTemp[$i]."' ";
				}
			}
			if(empty($chAmtQryPart)==false){
				$chAmtQryCriteria.=" AND (".$chAmtQryPart.")";
			}
			$checkNo= implode(',', $checkTemp);
		}
		if($checkAmt>0){
			$chAmtQryCriteria.=" AND trans.trans_amount $amtCriteria $checkAmt";
		}
		if(empty($pay_method)==false){ //PAYMENT MODE
			$chAmtQryCriteria.=" AND LOWER(trans.trans_method )='".$pay_method."'";
		}
		if(sizeof($cc_type)>0){ //IF CREDIT CARD
			$chAmtQryCriteria.=" AND LOWER(trans.cc_type ) IN(".$str_cc_type.")";
		}				
	
		$chAmtQry="Select trans.encounter_id FROM report_enc_trans trans WHERE 1=1 ".$chAmtQryCriteria;
		if(empty($strBatchIds)==false){
			$chAmtQry.=" AND trans.batch_id IN(".$strBatchIds.")";
		}
		$chAmtRs = imw_query($chAmtQry);
		while($chAmtRes = imw_fetch_array($chAmtRs)){
			$arrChSearchEnc[$chAmtRes['encounter_id']]= $chAmtRes['encounter_id'];
		}
		
		if(sizeof($arrChSearchEnc)>0){
			$strChSearchEnc=implode(',', $arrChSearchEnc);
		}
	}

	$pay_crd_deb_arr =array();
	$mainResArr = array();
	$search = 'DOP';
	if($DateRangeFor=='transaction_date'){ $search='DOT'; }
	if($DateRangeFor == 'date_of_service' || $DateRangeFor == 'doc'){
		$search = ($DateRangeFor=='doc')? 'DOC':'DOS';
		require_once(dirname(__FILE__).'/ledger_dos.php');
	}else{
		require_once(dirname(__FILE__).'/ledger_dot.php');
	}


	//FOR DELETED CHARGES AND PAYMENTS
	if($DateRangeFor != 'date_of_service'){
		$whereQry='';
		if(empty($sc_name) == false){
			if($home_facility=='1'){
				$whereQry .= " and patient_data.default_facility IN ($sc_name)";
			}else{
			$whereQry .= " and main.facility_id IN ($sc_name)";
		}
		}
		if(empty($grp_id) == false){
			$whereQry .= " and main.gro_id IN ($grp_id)";
		}
		if(empty($Physician) === false or empty($credit_physician) === false){
			if(empty($Physician) === false and empty($credit_physician) === false){
				if($Physician != $credit_physician){
					$whereQry.= " and (main.primary_provider_id_for_reports IN ($Physician) 
												and main.sec_prov_id IN ($credit_physician_edited))";
				}
				else{
					$whereQry.= " and (main.primary_provider_id_for_reports IN ($Physician) 
												and main.sec_prov_id IN (0))";
				}
			}
			else if(empty($Physician) === false){
				$whereQry.= " and main.primary_provider_id_for_reports IN ($Physician)";
			}
			else if(empty($credit_physician) === false){
				$whereQry.= " and main.sec_prov_id IN ($credit_physician)";
			}
		}
		if(empty($operatorName)==false){
			$whereQry= " AND trans.trans_del_operator_id in($operatorName)";
		}
		if(trim($cpt_code_id) != ''){
			$whereQry.= " AND main.proc_code_id in ($cpt_code_id)";
		}
		if(trim($dx_code) != '' || trim($dx_code10) != ''){
			$whereQry.= ' AND (';
			$andOR='';
			if(trim($dx_code)!= ''){
				$whereQry.= " (main.dx_id1 in ($dx_code)
				or main.dx_id2 in ($dx_code)
				or main.dx_id3 in ($dx_code)
				or main.dx_id4 in ($dx_code))";
				$andOR=' OR ';
			}
			if(trim($dx_code10)!= ''){
				$whereQry.=$andOR." (main.dx_id1 in ($dx_code10)
				or main.dx_id2 in ($dx_code10)
				or main.dx_id3 in ($dx_code10)
				or main.dx_id4 in ($dx_code10))";
			}
			$whereQry.= ') ';	
		}
		if(trim($modifiers)!=''){
			$whereQry.= " and (main.mod_id1 IN($modifiers)
			OR main.mod_id2 IN($modifiers)
			OR main.mod_id3 IN($modifiers))";
		}
		if(trim($insuranceName) != ''){
			if(trim($ins_type) == ''){
				$whereQry .= " and (main.pri_ins_id in ($insuranceName)
				or main.sec_ins_id in ($insuranceName)
				or main.tri_ins_id in ($insuranceName))";
			}
			else{
				$ins_type_arr=explode(',',$ins_type);
				$whereQry .= " and (";
				for($i=0;$i<count($ins_type_arr);$i++){
					$ins_nam=$ins_type_arr[$i];
					if(trim($ins_nam)!='Self Pay')
					{
						$mul_or="";
						if($i>0){
							$mul_or=" or ";
						}
						$whereQry .= " $mul_or main.$ins_nam in ($insuranceName)";
					}
				}
				$whereQry .= " )";
			}
		}

		// GET DELETED AMOUNTS
		$arrVoidPay = array();
		$tempRecords=array();
		$qry="Select trans.patient_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, trans.trans_method, 
		trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_dot, trans.trans_qry_type,
		DATE_FORMAT(trans.trans_dot,'".$dateFormat."') as trans_dot, trans.trans_dot, trans.trans_del_operator_id, trans.trans_del_date,
		DATE_FORMAT(main.date_of_service, '".$dateFormat."') as 'date_of_service', main.primary_provider_id_for_reports  as 'primaryProviderId',
		main.proc_code_id, main.facility_id, main.first_posted_date, pd.fname, pd.lname, pd.mname, pd.default_facility 
		FROM report_enc_trans trans 
		JOIN report_enc_detail main ON main.charge_list_detail_id = trans.charge_list_detail_id 
		LEFT JOIN patient_data pd ON pd.id = trans.patient_id 
		WHERE trans_del_operator_id>0 AND trans.trans_type!='default_writeoff'";
		//IF "WITHOUT DELETED AMOUNTS" CHECKED THEN ONLY DISPLAY DELETED AMOUNT	THAT HAVE DOS BEFORE SEARCH CRITERIA
		//if($inc_del_trans=='1'){
		//	$qry.=" AND (DATE_FORMAT(patChgDet.trans_del_date, '%Y-%m-%d') BETWEEN '$Start_date' AND '$End_date') AND patChg.date_of_service<'$Start_date'"; 	
		//}else{
			$qry.=" AND (trans.trans_del_date BETWEEN '$Start_date' AND '$End_date')"; 	
		//}
		$qry.=$whereQry .' Order By ';
		$qry.= ($sort_by == 'patient') ? "pd.lname,pd.fname,main.date_of_service" : "main.date_of_service, patient_data.lname,patient_data.fname,";
		
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			//IF DELETED BEFORE FIRST POSTED DATE THEN IGNORE THAT
			if($res['first_posted_date']=='0000-00-00' || $res['first_posted_date'] <= $res['trans_del_date']){
				$primaryProviderId=$res['primaryProviderId'];
				if($home_facility=='1'){
					$facility_id=$res['default_facility'];
				}else{
					$facility_id=$res['facility_id'];
				}
				$facility_id= (empty($facility_id)==true)? '0' : $facility_id;
				$encID = $res['encounter_id'];	

				$chgDetId= $res['charge_list_detail_id'];
				$cptCode = $arrAllCPTCodes[$res['proc_code_id']];
				$cptCode = (strlen($cptCode)>8) ? substr($cptCode,0, 8).'...' : $cptCode;
				$trans_type= strtolower($res['trans_type']);
				$mode = strtolower($res['trans_method']);
				$paid_by = strtolower($res['trans_by']);
				$delOprId= $res['trans_del_operator_id'];
				
				$firstGrp = $primaryProviderId;
				$secGrp = $facility_id;
				if($viewBy=='facility'){
					$firstGrp = $facility_id;
					$secGrp = $primaryProviderId;
				}
		
				if(!$tempRecords[$encID]){
					$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_id'] = $res['patient_id'];
					$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_name'] = core_name_format($res['lname'], $res['fname'], $res['mname']);						
					$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['dos'] = $res['date_of_service'];
					$tempRecords[$encID]=$encID;
				}
				
				switch($trans_type){
					case 'charges':
						$delChargesArr[$firstGrp][$secGrp][$encID][$chgDetId] = $res;
						$delChargesSummArr[$firstGrp][$secGrp]+= $res['trans_amount']; // FOR SUMMARY
						$arrVoidPay[$firstGrp][$secGrp]['charges'] += $res['trans_amount']; 
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['charges'] += $res['trans_amount']; 
					break;
					case 'paid':
					case 'copay-paid':
					case 'deposit':
					case 'interest payment':
					case 'negative payment':
					case 'copay-negative payment':
						$paidForProc=$res['trans_amount'];
						if($trans_type=='negative payment')$paidForProc="-".$res['trans_amount'];

						switch($mode){
							case 'cash':
								$arrVoidPay[$firstGrp][$secGrp]['cash']+= $paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['cash'] += $paidForProc;
							break;
							case 'check':
								if($paid_by=='patient'){
									$arrVoidPay[$firstGrp][$secGrp]['pt_check'] +=$paidForProc;
									$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_check'] += $paidForProc;
								}else{
									$arrVoidPay[$firstGrp][$secGrp]['ins_check'] += $paidForProc;
									$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['ins_check'] += $paidForProc;
								}
							break;
							case 'credit card':
								$arrVoidPay[$firstGrp][$secGrp]['CC'] += $paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['CC'] += $paidForProc;
							break;
							case 'eft':
								$arrVoidPay[$firstGrp][$secGrp]['EFT'] += $paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['EFT'] += $paidForProc;
							break;
							case 'money order':
								$arrVoidPay[$firstGrp][$secGrp]['MO'] +=$paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['MO'] += $paidForProc;
							break;
							case 'veep':
								$arrVoidPay[$firstGrp][$secGrp]['VEEP'] +=$paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['VEEP'] += $paidForProc;
							break;
						}
					break;
					case 'credit':
					case 'debit':
						$crddbtamt=$res['trans_amount'];
						if($trans_type=='credit'){ 
							$crddbtamt= "-".$res['trans_amount'];							
						}else{  //debit
							$crddbtamt= $res['trans_amount'];				
						}

						$arrVoidPay[$firstGrp][$secGrp]['adjustments']+= $crddbtamt;
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['adjustments']+= $crddbtamt;
					break;
					case 'write off':
					case 'discount':
					case 'over adjustment':
						$arrVoidPay[$firstGrp][$secGrp]['adjustments']+= $res['trans_amount'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['adjustments']+= $res['trans_amount'];
					break;
					case 'adjustment':
					case 'returned check':
					case 'refund':
						$arrVoidPay[$firstGrp][$secGrp]['adjustments']+= "-".$res['trans_amount'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['adjustments']+= "-".$res['trans_amount'];
					break;
				}
				$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['opr'][$delOprId] = $delOprId;
				$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['proc_code'][$cptCode] = $cptCode;				
			}
		}

	}// END DELETED	
}


//GET SELECTED
$selgroup = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
$selFac = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);
$selPhy = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
$selCrPhy = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
$selInsurance = $CLSReports->report_display_selected($insuranceName,'insurance',1, $insurance_cnt);
$selOpr = $CLSReports->report_display_selected($operatorName,'operator',1, $allOprCount);
$selDept = $CLSReports->report_display_selected($departmentIds,'department',1, $allDeptCount);
$selAmtCriteria= ($checkAmt!='') ? $amtCriteria : '';
$selPayMethod= ($cc_type!='') ? ucfirst($pay_method).'('.ucfirst($cc_type).')' : ucfirst($pay_method);
$selRangeType = 'DOR';
if($DateRangeFor=='transaction_date')$selRangeType='DOT';
elseif($DateRangeFor == 'date_of_service')$selRangeType='DOS';


if($processReport == "Summary"){
	if($reportType=='checkView'){
		require_once(dirname(__FILE__).'/ledger_summary_check.php');
	}
	require_once(dirname(__FILE__).'/ledger_summary.php');
}
else{
	if($reportType=='checkView'){
		require_once(dirname(__FILE__).'/ledger_detail_check.php');
	}
	require_once(dirname(__FILE__).'/ledger_detail.php');
}


// SAVE Search Criteria
if(!isset($callFrom) || $callFrom != 'scheduled'){
	if((isset($search_name) && $search_name!='' && empty($varCriteria)==false) || ($chkSaveSearch=='1' && empty($varCriteria)==false)){
		$search_name=trim($search_name);
		$qryPart='Insert into';
		$fieldPart=", report_name='".addslashes($search_name)."'";
		$qryWhere='';
		if($savedCriteria!='' && $chkSaveSearch=='1'){
			$qryPart='Update'; 
			$fieldPart='';
			$qryWhere=" WHERE id='".$savedCriteria."'";
		}
		
		$qry="Select id FROM reports_searches WHERE report_name='".$search_name."' AND report='ledger_criteria'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)<=0 || (imw_num_rows($rs)>0 && $qryPart=='Update')){
			$qry="$qryPart reports_searches SET uid='".$_SESSION['authId']."', report='ledger_criteria',
			search_data='".addslashes($varCriteria)."', saved_date='".date('Y-m-d H:i:s')."' ".$fieldPart.$qryWhere;
			imw_query($qry);
		}
	}
}
//---------------------

$HTMLCreated=0;
if($printFile == true and $csv_file_data != ''){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$csv_file_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$csv_file_data;

	$file_location = write_html($strHTML);
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}

if($output_option=='view' || $output_option=='output_csv'){
	if($callFrom!='scheduled'){
		if($output_option=='output_csv' && $processReport == "Detail"){
			echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
		}else{
			echo $csv_file_data;	
		}		
	}
}
?>
