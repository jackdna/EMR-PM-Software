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

ini_set("memory_limit","3072M");
set_time_limit (0);

$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');
$FCName= $_SESSION['authId'];

//-- OPERATOR INITIAL -------
$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$opInitial = $authProviderNameArr[1][0];
$opInitial .= $authProviderNameArr[0][0];
$opInitial = strtoupper($opInitial);

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

	$search='DOS';
	if($DateRangeFor == 'transaction_date'){
		$search='DOT';
	}else if($DateRangeFor == 'date_of_payment'){
		$search='DOR';
	}
	
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
	
	// Merge CPT Code and CPT Categories
	$tmpCptCodeArr = array();
	$cpt_code_id= array_filter($cpt_code_id);
	$cpt_cat_id= array_filter($cpt_cat_id);
	
	$cpt_code_id_title = implode(',',$cpt_code_id);
	$cpt_cat_id_title  = implode(',',$cpt_cat_id);
	
	if(empty($cpt_code_id)==false) $tmpCptCodeArr[] = implode(',',$cpt_code_id);
	if(empty($cpt_cat_id)==false ) $tmpCptCodeArr[] = implode(',',$cpt_cat_id);
	
	$tmpCptCodeStr = implode(',',$tmpCptCodeArr);
	$cpt_code_id = explode(',',$tmpCptCodeStr);

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
	$physician= (sizeof($filing_provider)>0) ? implode(',',$filing_provider) : '';
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';	
	$cpt_code_id= (sizeof($cpt_code_id)>0) ? implode(',',$cpt_code_id) : '';
	$insuranceGrp= (sizeof($insuranceGrp)>0) ? implode(',',$insuranceGrp) : '';
	$insuranceName= (sizeof($arrInsurance)>0) ? implode(',',$arrInsurance) : '';
	$ins_types=array_combine($ins_types, $ins_types);
	$ins_type= (sizeof($ins_types)>0) ? implode(',',$ins_types) : '';
	$cpt_cat_2= (sizeof($cpt_cat_2)>0) ? implode(',',$cpt_cat_2) : '';
	//---------------------------------------
	
	$Sdate = $Start_date;
	$Edate = $End_date;

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

	$mainResArr = array();
	
	
	if($search == 'DOT' || $search == 'DOR'){
		$qry = "SELECT ret.trans_ins_id, ret.encounter_id, ret.charge_list_detail_id
	  					FROM report_enc_trans ret
							WHERE ret.trans_by = 'Insurance' 
	  						AND ret.trans_del_operator_id = 0 
							AND ret.trans_type IN('paid','copay-paid','Negative Payment','copay-Negative Payment')";
	  if($search=='DOR'){
		  $qry.=" AND (ret.trans_dop BETWEEN '$Start_date' AND '$End_date')";
	  }else{
		  $qry.=" AND (ret.trans_dot BETWEEN '$Start_date' AND '$End_date')";
	  }
	  if(empty($insuranceName)==false){
		  $qry.= " AND ret.trans_ins_id IN (".$insuranceName.")";
	  } 
		$sql = imw_query($qry);
	  $arrPayment = array();
	  while($row = imw_fetch_array($sql)){	
		  $encounter_id = $row['encounter_id'];
		  $arrEncIds[$encounter_id] = $encounter_id;
		  $enc_for_temp_query.='('.$encounter_id.'),';

		  if($row['charge_list_detail_id']>0){
			  $arrChgIds[$row['charge_list_detail_id']] = $row['charge_list_detail_id'];
		  }
	  }
	  $strEncIds= implode(',', $arrEncIds);
	  
	  if(empty($enc_for_temp_query)==false){
		  $enc_for_temp_query=substr($enc_for_temp_query,0, -1);
	  }
	  unset($sql);
  }
	
	//GET CONTRACT FEE FOR ENCOUNTERS
	if($search == 'DOS' || sizeof($arrEncIds)>0){
		
		$billingRs = imw_query("Select billing_amount from copay_policies");
		$billingRes= imw_fetch_assoc($billingRs);
		$billing_amount=$billingRes['billing_amount'];
		unset($billingRs);
		
		if($billing_amount=='Default'){
			
			//CREATE TEMP TABLE AND INSERT DATA
			$temp_join_part='';
			if(empty($enc_for_temp_query)==false){
				$tmp_table="IMWTEMP_reports_enc_ids_".time().'_'.$_SESSION["authId"];
				imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
				imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (enc_id INT)");
				imw_query("INSERT INTO $tmp_table (enc_id) VALUES ".$enc_for_temp_query);
				$temp_join_part=" INNER JOIN ".$tmp_table." t_tbl ON main.encounter_id = t_tbl.enc_id";
			}
			
			$qry = "Select main.encounter_id, main.proc_code_id, insurance_companies.id, insurance_companies.FeeTable 
			FROM report_enc_detail main 
			JOIN insurance_companies ON insurance_companies.id = main.pri_ins_id
			JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id 	
			".$temp_join_part."
			WHERE main.del_status='0' AND main.pri_ins_id > 0";
			if($search == 'DOS'){
				$qry.=" AND (main.date_of_service BETWEEN '$Start_date' AND '$End_date')";
			}
			if(empty($cpt_cat_2) == false){
				$qry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";
			}
			$sql = imw_query($qry);
			while($res = imw_fetch_assoc($sql)){	
				$FeeTable = (int)$res['FeeTable'];
				if($FeeTable == 0){	$FeeTable = 1;	}
			
				$arrProcId[$res['proc_code_id']]=$res['proc_code_id'];
				$arrInsComp[$res['id']][$res['proc_code_id']] = $res['proc_code_id'];
				$arrInsCompColumn[$res['id']] = $FeeTable;
			}unset($rs);
			//DROP TEMP TABLE
			imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
			
			if(sizeof($arrInsComp)>0){
				foreach($arrInsComp as $insId => $arrProc){
					$strProc= implode(',', $arrProc);

					if(empty($strProc)==false){
						$qry="Select cpt_fee_tbl.cpt_fee_id, cpt_fee_table.cpt_fee FROM cpt_fee_tbl
						JOIN cpt_fee_table ON cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id 
						WHERE cpt_fee_tbl.cpt_fee_id IN(".$strProc.") AND cpt_fee_table.fee_table_column_id = '".$arrInsCompColumn[$insId]."'  
						AND cpt_fee_tbl.delete_status = '0'";
						$rs = imw_query($qry);
						while($res = imw_fetch_assoc($rs)){	
							$arrConctractFee[$insId][$res['cpt_fee_id']] = $res['cpt_fee'];
						}unset($rs);
					}
				}
			}
		}
	}

	if($search == 'DOS' || sizeof($arrChgIds)>0){	
		$arrInsComp = array();
	  $arrTerInsComp=array();
		//JOIN reports_enc_trans ret ON ret.charge_list_id = main.charge_list_id 
		$qry = "SELECT  main.pri_ins_id, main.sec_ins_id, main.tri_ins_id, DATE_FORMAT(main.date_of_service, '".$dateFormat."') as 'date_of_service',
							main.patient_id,main.proc_code_id, main.encounter_id,
							main.charge_list_detail_id, main.last_pri_paid_date, 
							(main.charges * main.units) as totalAmount, main.approved_amt,
							main.pri_due, main.sec_due, main.tri_due, cpt_fee_tbl.cpt_prac_code, 
							patient_data.lname, patient_data.fname, patient_data.mname 
							FROM report_enc_detail main 
							JOIN patient_data ON patient_data.id = main.patient_id
							JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = main.proc_code_id 
							WHERE main.del_status='0'";
		
		if($search == 'DOS'){
		  $qry .= " AND main.date_of_service BETWEEN '$Start_date' AND '$End_date'";
		}
		if(trim($grp_id) != ''){
		  $qry .= " AND main.gro_id IN ($grp_id)";
		}
		if(empty($insuranceName) === false){
			if($ins_types['pri_ins_id'])$qry .= " AND main.pri_ins_id in ($insuranceName)";
			else $qry .= " AND main.sec_ins_id in ($insuranceName)";
		}
		if(trim($cpt_code_id) != ''){
			$qry .= " AND main.proc_code_id in ($cpt_code_id)";
		}
		if(trim($sc_name) != ''){
			$qry .= " AND main.facility_id in ($sc_name)";
		}
		if(trim($physician) != ''){
			$qry .= " AND main.pri_prov_id in ($physician)";
		}
		if(empty($credit_physician) === false){
			$qry.= " and main.sec_prov_id IN ($credit_physician)";
		}
		if($chksamebillingcredittingproviders==1){
			$qry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
		}	  
		if(empty($cpt_cat_2) == false){
			$qry.= " and cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";
		}
		if($search == 'DOS'){
			$qry .= " order by patient_data.lname, patient_data.fname, patient_data.mname";
		}
	  if($search == 'DOS'){

		  $chargesQryRes = get_array_records_query($qry);
		  $arrResult = array();
		  $arrEnc = array();
		  $arrChgDetIds=array();
		  $arrCopayEnc=array();
	  
		  for($i=0;$i<count($chargesQryRes);$i++){
			  $contract_price='';
			  $patName = $chargesQryRes[$i]['lname'].', '.$chargesQryRes[$i]['fname'];
			  $patientID = $chargesQryRes[$i]['patient_id'];
			  $encounterID = $chargesQryRes[$i]['encounter_id'];
			  $posted_status = $chargesQryRes[$i]['posted_status'];
			  
			  $totalAmount = $chargesQryRes[$i]['totalAmount'];
			  $insComID = $chargesQryRes[$i]['pri_ins_id'];
			  $secInsComID = $chargesQryRes[$i]['sec_ins_id'];
			  $terInsComID = $chargesQryRes[$i]['tri_ins_id'];
			  $arrEnc[$encounterID]+= $totalAmount;
			  $arrChgDetIds[$chargesQryRes[$i]['charge_list_detail_id']]=$chargesQryRes[$i]['charge_list_detail_id'];

			  //GETTING CONTRACT FEE
			  //$contract_price = $objManageData->getContractFee($chargesQryRes[$i]['cpt_prac_code'], $insComID, true);
			  $contract_price = $arrConctractFee[$insComID][$chargesQryRes[$i]['proc_code_id']];
			  //--------------------

			  //ALLOWED AMOUNT LOGIC
			  $apporvedAmt=$chargesQryRes[$i]['approved_amt'];
			  if($chargesQryRes[$i]['last_pri_paid_date']=='0000-00-00'){
				$apporvedAmt=0;
			  }
				  
			  $mainInsId=($ins_types['pri_ins_id'])?$insComID:$secInsComID;

			  $arrResult['insurance'][$mainInsId][$patientID]['name'] = $patName ;
			  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['dos'] = $chargesQryRes[$i]['date_of_service'] ;
			  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['cpt'][] = $chargesQryRes[$i]['cpt_prac_code'];
			  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_billed'] += $totalAmount;
			  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_approved_amt']+= $apporvedAmt;
			  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_contract_fee']+= $contract_price;
			  
			  
			  //-- FOR PRIMARY INSURANCE -----
			  if($insComID != "0"){
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due'] += $chargesQryRes[$i]['pri_due'];
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_pri_due'] += $chargesQryRes[$i]['pri_due'];
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['approved_amt'] += $apporvedAmt;
				  if($chargesQryRes[$i]['pri_due']>0)
				  {
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due_by'][] = "Pri";
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_pri_billed'] += $totalAmount;
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['contract_fee'] += $contract_price;
				  unset($totalAmount);
				  }
				  unset($apporvedAmt);				  
			  }
			  
			  //--FOR SECONDARY INSURANCE ----
			  if($secInsComID != "0"){
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due'] += $chargesQryRes[$i]['sec_due'];
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_sec_due'] += $chargesQryRes[$i]['sec_due'];
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['approved_amt'] += $apporvedAmt;				  
				  if($chargesQryRes[$i]['sec_due']>0)
				  {
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_sec_billed'] += $totalAmount;
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['contract_fee'] += $contract_price;
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due_by'][] = "Sec";
				  unset($totalAmount);
				  }
  				  unset($apporvedAmt);
			  }
			  
			  //--FOR TERTIARY INSURANCE ----
			  if($terInsComID){
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due'] += $chargesQryRes[$i]['tri_due'];
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_tri_due'] += $chargesQryRes[$i]['tri_due'];
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['approved_amt'] += $apporvedAmt;				  
				  if($chargesQryRes[$i]['tri_due']>0)
				  {
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due_by'][] = "Ter";
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_tri_billed'] += $totalAmount;
				  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['contract_fee'] += $contract_price;
				  unset($totalAmount);
				  }
				  unset($apporvedAmt);				  
			  }
		  }
	  }
		else{
		//  echo implode(', ', $arrEncIds);
		  $splitted_chg_ids = array_chunk($arrEncIds,1500);
		  foreach($splitted_chg_ids as $arr){
			  $qryPart1='';
			  $str_splitted_chg_ids 	 = implode(',',$arr);
			  
			  $qryPart1= " AND main.encounter_id IN(".$str_splitted_chg_ids.")";
			  $qryPart1.= " ORDER BY patient_data.lname, patient_data.fname, patient_data.mname";
				
			  $rs=imw_query($qry.$qryPart1);
			  while($res=imw_fetch_array($rs)){
				  if($arrChgIds[$res['charge_list_detail_id']]){
					  $contract_price='';
					  $patName = $res['lname'].', '.$res['fname'];
					  $patientID = $res['patient_id'];
					  $encounterID = $res['encounter_id'];
					  $posted_status = $res['posted_status'];
					  $totalAmount = $res['totalAmount'];
					  
					  $insComID = $res['pri_ins_id'];
					  $secInsComID = $res['sec_ins_id'];
					  $terInsComID = $res['tri_ins_id'];
					  $arrChgDetIds[$res['charge_list_detail_id']]=$res['charge_list_detail_id'];

					  //GETTING TERTIARY INSURANCE COMPANY
					  if($terInsComID>0){
						  $arrTerInsComp[$terInsComID] = $terInsComID;
					  }
					  
					  //GETTING CONTRACT FEE
					  //$contract_price = $objManageData->getContractFee($res['cpt_prac_code'], $insComID, true);
					  $contract_price = $arrConctractFee[$insComID][$res['proc_code_id']];
					  //--------------------
					  
					  //ALLOWED AMOUNT LOGIC
					  $apporvedAmt=$res['approved_amt'];
					  if($res['last_pri_paid_date']=='0000-00-00'){
					  	$apporvedAmt=0;
					  }
					  
					  $arrEnc[$encounterID]+= $totalAmount;
					  
					  $mainInsId=($ins_types['pri_ins_id'])?$insComID:$secInsComID; 
					  $arrResult['insurance'][$mainInsId][$patientID]['name'] = $patName ;
					  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['dos'] = $res['date_of_service'] ;
					  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['cpt'][] = $res['cpt_prac_code'];
					  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_billed'] += $totalAmount;
					  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_approved_amt'] += $apporvedAmt;
					  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_contract_fee'] += $contract_price;
					  
					   
					  //-- FOR PRIMARY INSURANCE -----
					  if($insComID != "0"){
					  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due'] += $res['pri_due'];
					  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_pri_due'] += $res['pri_due'];
					  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['approved_amt'] += $apporvedAmt;					  
					  if($res['pri_due']>0){
						  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due_by'][] = "Pri";
						  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_pri_billed'] += $totalAmount;
						  $arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['contract_fee'] += $contract_price;
						  unset($totalAmount);
					  }
					  unset($apporvedAmt);
					  }
  
					  //--FOR SECONDARY INSURANCE ----
					  if($secInsComID){
							$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due'] += $res['sec_due'];
							$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_sec_due'] += $res['sec_due'];
							$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['approved_amt'] += $apporvedAmt;					  
							if($res['sec_due']>0){
								$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due_by'][] = "Sec";
								$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_sec_billed'] += $totalAmount;
								$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['contract_fee'] += $contract_price;
								unset($totalAmount);
							}
							unset($apporvedAmt);
						}
					  
					  //--FOR TERTIARY INSURANCE ----
					  if($terInsComID){
							$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due'] += $res['tri_due'];
							$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_tri_due'] += $res['tri_due'];
							$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['approved_amt'] += $apporvedAmt;					  
							if($res['tri_due']>0){
								$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_due_by'][] = "Ter";
								$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['ins_tri_billed'] += $totalAmount;
								$arrResult['insurance'][$mainInsId][$patientID]['encounter'][$encounterID]['contract_fee'] += $contract_price;
								unset($totalAmount);
							}
							unset($apporvedAmt);
						 }
					  					
				  }
			  }
			  unset($rs);
		  }
	  }

	  //PAYMENTS
	  if(sizeof($arrChgDetIds)>0){
	 		$ins_com = array_keys($arrResult['insurance']);
		  $ins_com = array_combine($ins_com, $ins_com);
			$arr_ins_com = array_merge($ins_com, $arrTerInsComp);
		  $str_ins_com = implode(',', $arr_ins_com);
		  
		  $splitted_chg_ids = array_chunk($arrChgDetIds,1500);
		  foreach($splitted_chg_ids as $arr){
			  $str_splitted_chg_ids 	 = implode(',',$arr);
				$qry = "SELECT ret.trans_amount, ret.trans_ins_id, ret.encounter_id,ret.trans_del_operator_id,
				 ret.charge_list_detail_id, ret.trans_type  
				 FROM report_enc_trans ret  
				 WHERE ret.charge_list_detail_id IN (".$str_splitted_chg_ids.") 
				 AND ret.trans_by = 'Insurance' 
				 AND ret.trans_ins_id IN (".$str_ins_com.")
				 AND ret.trans_type IN('paid','copay-paid','Negative Payment','copay-Negative Payment','interest payment','deposit')";
				if($search=='DOR'){
					$qry.= " AND (ret.trans_dop BETWEEN '$Start_date' AND '$End_date')";
				}else if($search=='DOT'){
					$qry.= " AND (ret.trans_dot BETWEEN '$Start_date' AND '$End_date')";
				}
	  		
			  $rs = imw_query($qry);
			  while($res = imw_fetch_assoc($rs)){	  
				  $encounter_id = $res['encounter_id'];
					$res['trans_type'] = strtolower($res['trans_type']);	
					//if( $encounter_id == '262843') { pre($res); }
					// GET PAYMENT which is deleted from transaction
					
				  $insComID = $res['trans_ins_id'];
				  $paidAmt = $res['trans_amount'];
					
					if($res['trans_type']=='negative payment' || $res['trans_type']=='copay-negative payment' || $res['trans_del_operator_id']>0)
						$paidAmt="-".$res['trans_amount'];
					if(($res['trans_type']=='negative payment' || $res['trans_type']=='copay-negative payment') && $res['trans_del_operator_id']>0)
						$paidAmt=$res['trans_amount'];
					
				   $encArr[$encounter_id]=$encounter_id;
				  $arrPaymentTempN[$insComID][$encounter_id]['ins_paid'] += $paidAmt;	
			  }
		  }
		}
	 

	  $encIds='';
	  if(sizeof($encArr)>0) {
			$encIds=implode(',',$encArr);
			$query=imw_query("SELECT pri_ins_id, sec_ins_id, tri_ins_id, encounter_id from report_enc_detail where encounter_id IN($encIds)");
			while($data=imw_fetch_object($query))
			{
				$mainInsId=($ins_types['pri_ins_id'])?$data->pri_ins_id:$data->sec_ins_id; 
				//get primary paid
				if($data->pri_ins_id)
				{ 
					if($arrPaymentTempN[$data->pri_ins_id][$data->encounter_id]){
						$arrPayment[$mainInsId][$data->encounter_id]['ins_pri_paid']=$arrPaymentTempN[$data->pri_ins_id][$data->encounter_id]['ins_paid'];
						unset($arrPaymentTempN[$data->pri_ins_id][$data->encounter_id]['ins_paid']);
					}
				}

				//get sec paid
				if($data->sec_ins_id)
				{
					if($arrPaymentTempN[$data->sec_ins_id][$data->encounter_id]){
						$arrPayment[$mainInsId][$data->encounter_id]['ins_sec_paid']=$arrPaymentTempN[$data->sec_ins_id][$data->encounter_id]['ins_paid'];
						unset($arrPaymentTempN[$data->sec_ins_id][$data->encounter_id]['ins_paid']);
					}
				}

				//get tri paid
				if($data->tri_ins_id)
				{
					if($arrPaymentTempN[$data->tri_ins_id][$data->encounter_id]){
						$arrPayment[$mainInsId][$data->encounter_id]['ins_tri_paid']=$arrPaymentTempN[$data->tri_ins_id][$data->encounter_id]['ins_paid'];
						unset($arrPaymentTempN[$data->tri_ins_id][$data->encounter_id]['ins_paid']);
					}
				}

			}
	  }
	}
}

//GET SELECTED
$selgroup = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
$selFac = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);
$selPhy = $CLSReports->report_display_selected($physician,'physician',1, $allPhyCount);
$selCrPhy = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
$selInsurance = $CLSReports->report_display_selected($insuranceName,'insurance',1, $insurance_cnt);
$selCPT = $CLSReports->report_display_selected($cpt_code_id,'cpt_code',1, $allCPTCount);
$selOpr = $CLSReports->report_display_selected($operatorName,'operator',1, $allOprCount);

if($processReport == "Summary"){
	//-- GET SUMMARY REPORT  ---------
	require_once(dirname(__FILE__).'/summary_ins_analytics.php');
}
else{
	require_once(dirname(__FILE__).'/detail_ins_analytics.php');
}

$HTMLCreated=0;
if($printFile == true and $csv_file_data != ''){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$csv_file_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$pdf_page_content;

	$file_location = write_html($strHTML);
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}

if($output_option=='view' || $output_option=='output_csv'){
	if($callFrom != 'scheduled'){
		echo $csv_file_data;	
	}
}
?>