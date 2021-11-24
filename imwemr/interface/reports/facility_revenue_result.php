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
?>
<?php
/*
FILE : FAC_MONTHLY_RESULT.PHP
PURPOSE :  PROVIDER PRODUCTVITY REPORT RESULT
ACCESS TYPE : INCLUDED
*/
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

	$Ref_phy_grp_ids=implode(',', $selectedRef);
	$physician_ids=implode(',', $filing_provider);
	
	
	$group_id = join(',', $grp_id);
	$physician_id = join(',', $filing_provider);
	$facility_id=array_values($facility_id);
	$grp_fac_ids_arr=array();

	for($h=0;$h<count($facility_id);$h++){
		$fac_grp="";
		$fac_grp=strstr($facility_id[$h],'grp_');
		if($fac_grp!=""){
			$grp_fac_ids_arr[]=str_replace('grp_','',$facility_id[$h]);
		}else{
			$pos_fac_id_arr[]=$facility_id[$h];
		}
	}

	$facility_id = join(',', $grp_fac_ids_arr);
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';
	
	$showYear = substr($Start_date, -2);
	
	$start_date = getDateFormatDB($Start_date);
	$end_date = getDateFormatDB($End_date);
	
	//--- GET ALL Ref PROVIDER NAME --
	$monthNameArr['01']='Jan';
	$monthNameArr['02']='Feb';
	$monthNameArr['03']='Mar';
	$monthNameArr['04']='Apr';
	$monthNameArr['05']='May';
	$monthNameArr['06']='Jun';
	$monthNameArr['07']='Jul';
	$monthNameArr['08']='Aug';
	$monthNameArr['09']='Sept';
	$monthNameArr['10']='Oct';
	$monthNameArr['11']='Nov';
	$monthNameArr['12']='Dec';
	
	$monthNumberArr['Jan']='1';
	$monthNumberArr['Feb']='2';
	$monthNumberArr['Mar']='3';
	$monthNumberArr['Apr']='4';
	$monthNumberArr['May']='5';
	$monthNumberArr['Jun']='6';
	$monthNumberArr['Jul']='7';
	$monthNumberArr['Aug']='8';
	$monthNumberArr['Sept']='9';
	$monthNumberArr['Oct']='10';
	$monthNumberArr['Nov']='11';
	$monthNumberArr['Dec']='12';
	
	$fac_id_data_arr=array();
	$fac_query = imw_query("select pos_facilityies_tbl.pos_facility_id, pos_facilityies_tbl.facilityPracCode, 
				pos_tbl.pos_prac_code from pos_facilityies_tbl join pos_tbl 
				on pos_facilityies_tbl.pos_id = pos_tbl.pos_id order by headquarter desc, pos_facilityies_tbl.facilityPracCode");
	while($row_fac=imw_fetch_array($fac_query)){
		$pos_facility_id = $row_fac['pos_facility_id'];
		$facilityPracCode = $row_fac['facilityPracCode'];
		$pos_prac_code = $row_fac['pos_prac_code'];
		$fac_id_data_arr[$pos_facility_id] = $facilityPracCode.' - '.$pos_prac_code;
	}
	$fac_id_data_arr[0]="No Facility";
	if($group_id){
		$grp_whr=" and main.gro_id in($group_id)";
	}
	if($physician_id){
		$phy_whr=" and (main.primary_provider_id_for_reports in($physician_id) or main.sec_prov_id in($physician_id))";
	}
	if(count($grp_fac_ids_arr)==0 && count($pos_fac_id_arr)>0){
		$facility_id="";
		$pos_fac_id_str=join(',',$pos_fac_id_arr);
		if($pos_fac_id_str!=""){
			$fac_whr=" AND main.facility_id in($pos_fac_id_str)";
		}
		$fac_qry = "select fac_prac_code,name,id from facility where fac_prac_code  in($pos_fac_id_str)";
		$fac_qry_res = imw_query($fac_qry);
		$pos_fac_id_arr=array();
		while($fac_qry_row = imw_fetch_assoc($fac_qry_res)){
			if($fac_qry_row['fac_prac_code']>0){
				$fac_tbl_id_arr[$fac_qry_row['fac_prac_code']]=$fac_qry_row['id'];
				$fac_tbl_name_arr[$fac_qry_row['id']]=$fac_id_data_arr[$fac_qry_row['fac_prac_code']];
				$fac_tbl_pos_id_arr[$fac_qry_row['id']]=$fac_qry_row['fac_prac_code'];
			}
		}
		$Fac_ids="";
		$Fac_ids=join(',',$fac_tbl_id_arr);
		$selectedFacGroupArr[0]= "No Group";
		$selectedFacIDGroupArr[0]=$Fac_ids;
		foreach($fac_tbl_id_arr as $pos_fac_id_key => $pos_fac_id_val){
			$final_sel_grp_arr[$pos_fac_id_val][]=0;
			$final_sel_grp_fac_arr[0][]=$pos_fac_id_val;
		}
	}else{
		if($facility_id){
			$whr_grp=" and fac_group_id in($facility_id)";
		}
		$GroupQry = "select fac_group_name,fac_group_id,fac_id from fac_group_tbl
				where fac_group_status = '0' $whr_grp order by fac_group_name";
		$GroupQryRes = imw_query($GroupQry);
		$selectedGrpArr = array();
		while($GroupQryRow = imw_fetch_assoc($GroupQryRes)){
			$fac_id_new=$GroupQryRow['fac_id'];
			if(substr($fac_id_new,0,1)==','){
				$fac_id_new=substr($fac_id_new,1);
			}
			if(substr($fac_id_new,-1,1)==','){
				$fac_id_new=substr($fac_id_new,0,-1);
			}
			//echo $fac_id_new;
			$selectedFacArr[] = $fac_id_new;
			$selectedFacGroupArr[$GroupQryRow['fac_group_id']]= $GroupQryRow['fac_group_name'];
			$selectedFacIDGroupArr[$GroupQryRow['fac_group_id']]=$fac_id_new;
		}
		$fac_id_grp_val_ids=array();
		foreach($selectedFacIDGroupArr as $fac_id_grp_key => $fac_id_grp_val){
			$fac_id_grp_val_ids=explode(',',$fac_id_grp_val);
			foreach($fac_id_grp_val_ids as $fac_id_grp_keys => $fac_id_grp_vals){
				if($fac_id_grp_vals>0){
					$final_sel_grp_arr[$fac_id_grp_vals][]=$fac_id_grp_key;
					$final_sel_grp_fac_arr[$fac_id_grp_key][]=$fac_id_grp_vals;
				}
			}
		}

		$Fac_ids=str_replace(',,',',',join(',',$selectedFacArr));
		foreach($selectedFacArr as $facIds){
			if($facIds!=''){
				$fac_arr[] = $facIds;
			}
		}
		$Fac_ids=str_replace(',,',',',join(',',$fac_arr));
		
		if($Fac_ids){
			$fac_qry = "select fac_prac_code,name,id from facility where id  in($Fac_ids)";
			$fac_qry_res = imw_query($fac_qry);
			$pos_fac_id_arr=array();
			while($fac_qry_row = imw_fetch_assoc($fac_qry_res)){
				if($fac_qry_row['fac_prac_code']){
					$pos_fac_id_arr[]=$fac_qry_row['fac_prac_code'];
					$fac_tbl_id_arr[$fac_qry_row['fac_prac_code']]=$fac_qry_row['id'];
					$fac_tbl_name_arr[$fac_qry_row['id']]=$fac_qry_row['name'];
					$fac_tbl_pos_id_arr[$fac_qry_row['id']]=$fac_qry_row['fac_prac_code'];
				}
			}
			$pos_fac_id_str=join(',',$pos_fac_id_arr);
			if($pos_fac_id_str!=""){
				$fac_whr=" AND main.facility_id in($pos_fac_id_str)";
			}
		}
	}

	$Fac_ids_arr= explode(',',$Fac_ids);

	if(empty($selectedProc) === false){
				
		$cpt_group_id_str = join(',',$selectedProc);
		
		//--- GET CPT GROUP DATA ---
		$cptGroupQry = "select cpt_group_name, cpt_code_name, cpt_group_id from cpt_group_tbl
					where cpt_group_status = '0' order by cpt_group_name";
		
		$cptGroupQryRes = imw_query($cptGroupQry);
		$selectedProcArr = array();
		$select_proc_id_arr = array();
		$group_cpt_arr = array();
		while($cptGroupQryRow = imw_fetch_assoc($cptGroupQryRes)){
			$cpt_group_id = $cptGroupQryRow['cpt_group_id'];
			$cpt_group_name = ucwords($cptGroupQryRow['cpt_group_name']);
			$cptCodeNameArr = preg_split('/, /',$cptGroupQryRow['cpt_code_name']);
			$cpt_group_id_arr[$cpt_group_name] = $cptGroupQryRow['cpt_group_id'];
			
			if(in_array($cpt_group_id,$selectedProc) === true or empty($selectedProc) === true){
				$group_cpt_arr = array_merge($group_cpt_arr, $cptCodeNameArr);
				$selectedProcArr[$cpt_group_name] = $cptCodeNameArr;
				$select_proc_id_arr[] = $cptGroupQryRow['cpt_code_name'];
			}
		}
	}
	//---- GET ALL CPT CODE ----
	$procIdArr = array();
	$procQry = "select cpt_fee_id, cpt4_code from cpt_fee_tbl where delete_status = '0'";		
	$procQryRes = imw_query($procQry);
	$procIdArr = array();
	$procCptCodeArr = array();
	while($procQryRow = imw_fetch_assoc($procQryRes)){
		$cpt_fee_id = $procQryRow['cpt_fee_id'];
		$procCptCodeArr[$cpt_fee_id] = $procQryRow['cpt4_code'];
		if(count($group_cpt_arr) > 0){
			if(in_array($procQryRow['cpt4_code'], $group_cpt_arr) === true){
				$procIdArr[] = $cpt_fee_id;
			}
		}
		else{
			//$procIdArr[] = $cpt_fee_id;
		}
	}

	//---- GET ALL patient data ----
	$default_facility_arr = array();
	$PatQry = "select id,default_facility from patient_data where default_facility!=''";
	$PatQryRes = imw_query($PatQry);
	while($PatQryRow = imw_fetch_assoc($PatQryRes)){
		$default_facility_arr[$PatQryRow['id']] = $PatQryRow['default_facility'];
	}

	$selProcIdArr=array_combine($procIdArr, $procIdArr);
	$procIdStr = join(',', array_unique($procIdArr));
		
	$dParts = explode('-',$start_date);
	$start_date_chg = date('Y-m-d', mktime(0,0,0,$dParts[1],$dParts[2],$dParts[0]));
	
	//echo "<pre>";
	//print_r($fac_app_org_data_rec);
/*	if($procIdStr!=""){
		$proc_whr=" and main.procCode in($procIdStr)";
	}
*/
	$sch_qry = "Select main.encounter_id, (main.charges*main.units) as 'totalAmount', main.facility_id, main.date_of_service,
	main.patient_id,main.proc_code_id as 'procCode', main.primary_provider_id_for_reports as 'primaryProviderId', main.charge_list_detail_id  
	FROM report_enc_detail main 
	where main.del_status = '0'	$fac_whr $grp_whr $phy_whr AND (date_of_service BETWEEN '$start_date_chg' AND '$end_date')";
	if(empty($credit_physician) === false){
		$sch_qry.= " and main.sec_prov_id IN ($credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$sch_qry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
	}			
	$sch_qry_res = imw_query($sch_qry);
	$patIdsForAppts=array();
	$patDOSForAppts=array();
	$FacIdsForAppts=array();
	$summaryTotalChrgArr = array();
	$summaryTotalPaidArr = array();

	while($sch_qry_ResRow = imw_fetch_assoc($sch_qry_res)){
		$default_facility=$default_facility_arr[$sch_qry_ResRow['patient_id']];
		$primaryProviderId=$sch_qry_ResRow['primaryProviderId'];
		$proc_id=$sch_qry_ResRow['procCode'];
		if($default_facility==""){
			$default_facility=0;
		}
		$date_of_service_arr=explode('-',$sch_qry_ResRow['date_of_service']);
		$dos_month_mk=$monthNameArr[$date_of_service_arr[1]].'-'.substr($date_of_service_arr[0],2);

		//--SUMMARY ---
		$enc_sum_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['encounter_id'];
		$pat_sum_enc_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['patient_id'];
		$fac_sum_enc_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['facility_id'];
		
		$fac_main_id=$fac_tbl_id_arr[$sch_qry_ResRow['facility_id']];
		foreach($final_sel_grp_arr[$fac_main_id] as $final_sel_grp_key4 => $final_sel_grp_val4){
			$summaryTotalChrgArr[$final_sel_grp_val4][$dos_month_mk][]=$sch_qry_ResRow['totalAmount'];
			$summaryFacTotalChrgArr[$final_sel_grp_val4][$fac_main_id][$dos_month_mk][]=$sch_qry_ResRow['totalAmount'];
			$summaryOrgTotalChrgArr[$dos_month_mk][]=$sch_qry_ResRow['totalAmount'];
			$grSummaryChgArr[$dos_month_mk][$sch_qry_ResRow['charge_list_detail_id']]= $sch_qry_ResRow['totalAmount'];
		}
		//-------------------		
		
		if(sizeof($selProcIdArr)<=0 || (sizeof($selProcIdArr)>0 && $selProcIdArr[$proc_id])){
			if($procIdStr!=""){
				foreach($selectedProcArr as $grp_name_key => $grp_name_val){
					if(in_array($procCptCodeArr[$sch_qry_ResRow['procCode']], $grp_name_val) === true){
						$enc_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['encounter_id'];
						$pat_enc_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['patient_id'];
						$chld_arr[$sch_qry_ResRow['charge_list_detail_id']]=$sch_qry_ResRow['charge_list_detail_id'];	
						if($sch_qry_ResRow['coPayAdjustedAmount']>0){
							$enc_copay_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['encounter_id'];
						}
					}
				}
			}else{
				$enc_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['encounter_id'];
				$pat_enc_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['patient_id'];
				$chld_arr[$sch_qry_ResRow['charge_list_detail_id']]=$sch_qry_ResRow['charge_list_detail_id'];
				if($sch_qry_ResRow['coPayAdjustedAmount']>0){
					$enc_copay_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['encounter_id'];
				}
			}
			$fac_enc_arr[$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['facility_id'];
			
			if($sch_qry_ResRow['date_of_service']>=$start_date && $sch_qry_ResRow['date_of_service']<=$end_date){
				$monthArr[$dos_month_mk]=$dos_month_mk;
				$fac_main_id=$fac_tbl_id_arr[$sch_qry_ResRow['facility_id']];
	
				foreach($final_sel_grp_arr[$fac_main_id] as $final_sel_grp_key4 => $final_sel_grp_val4){
					if($procIdStr!=""){
						foreach($selectedProcArr as $grp_name_key => $grp_name_val){
							if(in_array($procCptCodeArr[$sch_qry_ResRow['procCode']], $grp_name_val) === true){	
								$fac_chg_data_rec[$final_sel_grp_val4][$dos_month_mk][]=$sch_qry_ResRow['totalAmount'];
								$fac_chg_data_proc_rec[$final_sel_grp_val4][$grp_name_key][$dos_month_mk][$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['totalAmount'];
								$fac_chg_data_proc_det_rec[$final_sel_grp_val4][$fac_main_id][$grp_name_key][$dos_month_mk][$sch_qry_ResRow['encounter_id']]=$sch_qry_ResRow['totalAmount'];
								$fac_chg_data_det_rec[$final_sel_grp_val4][$fac_main_id][$dos_month_mk][]=$sch_qry_ResRow['totalAmount'];
								$fac_org_chg_data_rec[$final_sel_grp_val4][$default_facility][$dos_month_mk][]=$sch_qry_ResRow['totalAmount'];
								$patIdsForAppts[$sch_qry_ResRow['patient_id']]=$sch_qry_ResRow['patient_id'];
								$patDOSForAppts[$sch_qry_ResRow['patient_id']][]=$sch_qry_ResRow['date_of_service'];
								$patDOSDocForAppts[$primaryProviderId][$sch_qry_ResRow['patient_id']][]=$sch_qry_ResRow['date_of_service'];
								$FacIdsForAppts[$sch_qry_ResRow['patient_id']][$sch_qry_ResRow['date_of_service']][]=$fac_main_id;
							}
						}
					}else{
						$fac_chg_data_rec[$final_sel_grp_val4][$dos_month_mk][]=$sch_qry_ResRow['totalAmount'];
						$fac_chg_data_det_rec[$final_sel_grp_val4][$fac_main_id][$dos_month_mk][]=$sch_qry_ResRow['totalAmount'];
						$fac_org_chg_data_rec[$final_sel_grp_val4][$default_facility][$dos_month_mk][]=$sch_qry_ResRow['totalAmount'];
						$gr_fac_chg_data_arr[$dos_month_mk][$sch_qry_ResRow['charge_list_detail_id']]= $sch_qry_ResRow['totalAmount'];
						$patIdsForAppts[$sch_qry_ResRow['patient_id']]=$sch_qry_ResRow['patient_id'];
						$patDOSForAppts[$sch_qry_ResRow['patient_id']][]=$sch_qry_ResRow['date_of_service'];
						$patDOSDocForAppts[$primaryProviderId][$sch_qry_ResRow['patient_id']][]=$sch_qry_ResRow['date_of_service'];
						$FacIdsForAppts[$sch_qry_ResRow['patient_id']][$sch_qry_ResRow['date_of_service']][]=$fac_main_id;
					}
					
				}
			}
		}
	}
	//print_r($patDOSDocForAppts);
	$enc_str=implode(',',$enc_arr);
	$enc_copay_str=implode(',',$enc_copay_arr);
	$chld_str=implode(',',$chld_arr);
	$enc_sum_str=implode(',',$enc_sum_arr);

	//if($enc_str){
	$sch_qry1 = "select trans.report_trans_id, trans.encounter_id, trans.trans_dot, trans.trans_dop, trans.trans_amount, trans.charge_list_detail_id,
	trans.trans_type, trans.trans_del_operator_id  
	FROM report_enc_trans trans 
	WHERE LOWER(trans.trans_type) IN('paid','negative payment','copay-negative payment','copay-paid','interest payment','deposit')";
	if($DateRangeFor=='date_of_payment'){ 
		$sch_qry1.=" AND (trans.trans_dop BETWEEN '$start_date' and '$end_date')";
	}else{
		$sch_qry1.=" AND (trans.trans_dot BETWEEN '$start_date' and '$end_date')";
	}
	$sch_qry1.=" ORDER BY trans.trans_dot";
		$sch_qry_res1 = imw_query($sch_qry1);
		while($sch_qry_row1 = imw_fetch_assoc($sch_qry_res1)){
			$default_facility=$default_facility_arr[$pat_enc_arr[$sch_qry_row1['encounter_id']]];
			$trans_type= strtolower($sch_qry_row1['trans_type']);
			$encounter_id=$sch_qry_row1['encounter_id'];
			$charge_list_detail_id=$sch_qry_row1['charge_list_detail_id'];
			$tempEncId[$encounter_id]=$encounter_id;
			
			if($default_facility==""){
				$default_facility=0;
			}

			$date_of_payment_arr=explode('-',$sch_qry_row1['trans_dot']);
			$month_mk=$monthNameArr[$date_of_payment_arr[1]].'-'.substr($date_of_payment_arr[0],2);
			$monthArr[$month_mk]=$month_mk;

			$pay_facility_id=$fac_enc_arr[$encounter_id];
			$fac_main_id=$fac_tbl_id_arr[$pay_facility_id];

			$paid_amount=$sch_qry_row1['trans_amount'];
			if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $sch_qry_row1['trans_del_operator_id']>0)$paid_amount="-".$sch_qry_row1['trans_amount'];
			if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $sch_qry_row1['trans_del_operator_id']>0)$paid_amount=$sch_qry_row1['trans_amount'];

			if($chld_arr[$charge_list_detail_id]){
				foreach($final_sel_grp_arr[$fac_main_id] as $final_sel_grp_key4 => $final_sel_grp_val4){
					$fac_pay_data_rec[$final_sel_grp_val4][$month_mk][]=$paid_amount;
					$fac_pay_data_det_rec[$final_sel_grp_val4][$fac_main_id][$month_mk][]=$paid_amount;
					$fac_org_pay_data_rec[$final_sel_grp_val4][$default_facility][$month_mk][]=$paid_amount;
					$gr_fac_pay_data_arr[$month_mk][$sch_qry_row1['report_trans_id']] = $paid_amount;
				}
			}

			//TEMP FOR SUMMARY
			$tempArrPayments[$encounter_id][$month_mk]+=$paid_amount;
/*			foreach($final_sel_grp_arr[$fac_main_id] as $final_sel_grp_key4 => $final_sel_grp_val4){
				$tempArrPayments[$encounter_id][$final_sel_grp_val4][$fac_main_id][$month_mk]+=$paid_amount;
			}
*/		}


		//FINAL SUMMARY PAYMENT ARRAY
		if(sizeof($tempEncId)>0){
			$tempEncId_str=implode(',', $tempEncId);

			$qry = "Select main.encounter_id, main.facility_id FROM report_enc_detail main WHERE main.encounter_id IN(".$tempEncId_str.") 
			$fac_whr GROUP BY main.encounter_id";
			$rs = imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$enc_id=$res['encounter_id'];
				$fac_id=$res['facility_id'];
				$fac_main_id=$fac_tbl_id_arr[$fac_id];
				foreach($tempArrPayments[$enc_id] as $month_mk => $paid_amount){

					foreach($final_sel_grp_arr[$fac_main_id] as $final_sel_grp_key4 => $final_sel_grp_val4){
						$summaryTotalPaidArr[$final_sel_grp_val4][$month_mk][]=$paid_amount;
						$summaryFacTotalPaidArr[$final_sel_grp_val4][$fac_main_id][$month_mk][]=$paid_amount;
						$summaryOrgTotalPaidArr[$month_mk][]=$paid_amount;
						$grSummaryPayArr[$month_mk][] = $paid_amount;
					}
				}
				
/*				foreach($tempArrPayments[$enc_id] as $final_sel_grp_val4 => $facArr){
					foreach($facArr as $fac_main_id => $month_arr){
						foreach($month_arr as $month_mk => $paid_amount){

							$summaryTotalPaidArr[$final_sel_grp_val4][$month_mk][]=$paid_amount;
							$summaryFacTotalPaidArr[$final_sel_grp_val4][$fac_main_id][$month_mk][]=$paid_amount;
							$summaryOrgTotalPaidArr[$month_mk][]=$paid_amount;
							$grSummaryPayArr[$month_mk][] = $paid_amount;
						}
					}
				}
*/			}
		}
		unset($tempArrPayments);	


	//  GET APPOINTMENTS
	$apptPart='';
	$patIdsForAppts=array_unique($patIdsForAppts);
	$patIdsForApptsStr=implode(',',$patIdsForAppts);
	if(empty($patIdsForApptsStr)==false){
		$apptPart=" AND appt.sa_patient_id IN($patIdsForApptsStr)";
	}
	$sch_app_qry = "select appt.sa_facility_id,appt.sa_app_start_date,appt.id,appt.sa_patient_id, appt.sa_doctor_id,
	date_format(appt.sa_app_start_date, '".$dateFormat."') as sa_app_start_date_show, 
	time_format(appt.sa_app_starttime,'%h:%i %p') as sa_app_starttime_show, 
	time_format(appt.sa_app_endtime,'%h:%i %p') as sa_app_endtime_show,
	patient_data.lname, patient_data.fname, patient_data.mname,
	patient_data.default_facility,
	slot_procedures.proc as procedure_name
		from schedule_appointments appt 
	join slot_procedures on slot_procedures.id = appt.procedureid
	join patient_data on patient_data.id = appt.sa_patient_id
	where (appt.sa_app_start_date BETWEEN '$start_date' and '$end_date') $apptPart
	and appt.sa_patient_app_status_id not in(3,18,201,203)";
	if($Fac_ids){
		//$sch_app_qry .= " and appt.sa_facility_id IN ($Fac_ids)";
	}
	if($physician_id){
		//$sch_app_qry .= " and appt.sa_doctor_id IN ($physician_id)";
	}
	$sch_app_qry .= " order by appt.sa_app_start_date,appt.sa_app_starttime,appt.sa_app_endtime";
	
	$sch_app_qry_res = imw_query($sch_app_qry);
	$pat_fac_id_arr=array();
	//echo '<br>----------<br>';
	$tempArr=array();
	while($sch_app_qry_row = imw_fetch_assoc($sch_app_qry_res)){
		$sa_app_start_date=$sch_app_qry_row['sa_app_start_date'];
		$docId = $sch_app_qry_row['sa_doctor_id'];
		
		//if((in_array($sa_app_start_date, $patDOSDocForAppts[$docId][$sch_app_qry_row['sa_patient_id']])) || (in_array($sa_app_start_date, $patDOSForAppts[$sch_app_qry_row['sa_patient_id']]) && count($tempArr[$sch_app_qry_row['sa_patient_id']])<=0)){
		if((in_array($sa_app_start_date, $patDOSDocForAppts[$docId][$sch_app_qry_row['sa_patient_id']]))){
			$tempArr[$sch_app_qry_row['sa_patient_id']]=$sch_app_qry_row['sa_patient_id'];
			$sa_facility_id=$sch_app_qry_row['sa_facility_id'];
		//echo $docId.' - '.$sch_app_qry_row['sa_patient_id'].' - '.$sa_app_start_date.' - '.$sch_app_qry_row['sa_facility_id'].'<br>';			
			$sa_app_start_date_arr=explode('-',$sa_app_start_date);
			$sa_app_month_mk=$monthNameArr[$sa_app_start_date_arr[1]].'-'.substr($sa_app_start_date_arr[0],2);
			$monthArr[$sa_app_month_mk]=$sa_app_month_mk;
			$patient_name = core_name_format($sch_app_qry_row['lname'], $sch_app_qry_row['fname'], $sch_app_qry_row['mname']);
			
			$sch_app_qry_row['patient_name'] = $patient_name. ' - '. $sch_app_qry_row['sa_patient_id'];
			$pat_fac_id_arr[$sch_app_qry_row['sa_patient_id']]=$sch_app_qry_row['default_facility'];
			$default_facility=$sch_app_qry_row['default_facility'];
			if($default_facility==""){
				$default_facility=0;
			}
			if($sa_facility_id>0){
				foreach($final_sel_grp_arr[$sa_facility_id] as $final_sel_grp_key3 => $final_sel_grp_val3){
					if(in_array($sa_facility_id,$FacIdsForAppts[$sch_app_qry_row['sa_patient_id']][$sa_app_start_date])){
						$fac_app_data_rec[$final_sel_grp_val3][$sa_app_month_mk][]=$sch_app_qry_row['id'];
						$fac_app_data_rec_detail[$final_sel_grp_val3][$sa_facility_id][$sa_app_month_mk][]=$sch_app_qry_row['id'];
						$fac_app_data_all_detail[$final_sel_grp_val3][$sa_facility_id][]=$sch_app_qry_row;
						$fac_app_org_data_rec[$default_facility][$sa_app_month_mk][]=$sch_app_qry_row['id'];
						$fac_app_org_data_rec_detail[$final_sel_grp_val3][$default_facility][$sa_app_month_mk][]=$sch_app_qry_row['id'];
						$fac_app_org_data_all_detail[$final_sel_grp_val3][$default_facility][]=$sch_app_qry_row;
					}
				}
			}
		}
	}
	//--------------
//	echo '<pre>';print_r($final_sel_grp_arr);
		
		//--- GET AGGING CYCLE -----
		$policiesQry = imw_query("Select elem_arCycle from copay_policies where policies_id = '1'");
		$polociesDetails = imw_fetch_assoc($policiesQry);
		$aggingCycle = $polociesDetails['elem_arCycle'];
		
		$aggingDrop = array();			
		for($i=0;$i<180;$i++){
			$j = $i == 0 ? '00' : $i + 1;  	
			$aggingDrop[$j] = $j .'-'. ($aggingCycle+$i);
			$i += ($aggingCycle - 1);
		}
		$aggingDrop[181] = '181+';
		
		$arAgingDataArr = array();
		$totalAmtAgingArr = array();
		$arAgingDataArr_wofac=array();
		$selectedFacIDGroupArr_Agg=array();
		foreach($selectedFacIDGroupArr as $fac_id_grp_key => $fac_id_grp_val){
			$fac_id_grp_val_ids=explode(',',$fac_id_grp_val);
			$selectedFacIDGroupArr_Agg_val=0;
			foreach($fac_id_grp_val_ids as $fac_id_grp_keys => $fac_id_grp_vals){
				if($fac_id_grp_vals>0){
					$selectedFacIDGroupArr_Agg_val .=','.$fac_tbl_pos_id_arr[$fac_id_grp_vals];
				}
			}
			$selectedFacIDGroupArr_Agg[$fac_id_grp_key]=$selectedFacIDGroupArr_Agg_val;
		}
		
		$aging_start=0;
		$aging_to=180;
		foreach($selectedFacIDGroupArr_Agg as $fac_id_grp_key => $fac_id_grp_val){
			$qry="Select (pri_due + sec_due + tri_due + pat_due) as 'totalBalance', total_charges, 
			DATEDIFF(NOW(),date_of_service) as last_pri_dop_diff,
			DATEDIFF(NOW(),from_sec_due_date) as last_sec_dop_diff,
			DATEDIFF(NOW(),from_ter_due_date) as last_ter_dop_diff,
			DATEDIFF(NOW(),from_pat_due_date) as last_pat_dop_diff,
			DATEDIFF(NOW(),date_of_service) as last_dos_diff
			FROM report_enc_detail WHERE del_status='0' AND facility_id in($fac_id_grp_val)";
			
			$rs= imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$arrTempAging = array();
	
				if($res['last_pri_dop_diff']!='') { $arrTempAging[] = $res['last_pri_dop_diff']; }
				if($res['last_sec_dop_diff']!='') { $arrTempAging[] = $res['last_sec_dop_diff']; }
				if($res['last_ter_dop_diff']!='') { $arrTempAging[] = $res['last_ter_dop_diff']; }
				if($res['last_pat_dop_diff']!='') { $arrTempAging[] = $res['last_pat_dop_diff']; }
	
				sort($arrTempAging);
				$smallestAging = $arrTempAging[0];
				
				for($a=$aging_start;$a<=$aging_to;$a++){
					$start = $a;
					$a = $a > 0 ? $a - 1 : $a;
					$end = ($a) + $aggingCycle;
					$b=$a;
					if($b==0){ $b='00'; }else{ $b++;}
	
					if($smallestAging >= $start and  $smallestAging <= $end){
						$arBalTotalMainArr[$fac_id_grp_key][$b]+=$res['totalBalance'];
						$arrAgingTotalMainArr[$fac_id_grp_key][$b]['balance']+=$res['totalBalance'];
						$arrAgingTotalMainArr[$fac_id_grp_key][$b]['charges']+=$res['total_charges'];
					}
					$a += $aggingCycle;
				}
				
				if($smallestAging>=181){
					$arBalTotalMainArr[$fac_id_grp_key][181]+=$res['totalBalance'];
					$arrAgingTotalMainArr[$fac_id_grp_key][181]['balance']+=$res['totalBalance'];
					$arrAgingTotalMainArr[$fac_id_grp_key][181]['charges']+=$res['total_charges'];
				}
			}
		}
		
		$arBalTotalMain=0;
		foreach($arrAgingTotalMainArr as $fac_id_grp_key => $agingData){
			
			$arBalTotalMain= array_sum($arBalTotalMainArr[$fac_id_grp_key]);

			foreach($agingData as $agingStart => $agingDet){
				$totalAmtAging = $agingDet['charges'];
				$totalBalanceAging = $agingDet['balance'];
				
				
				$enc_avg = number_format(($totalBalanceAging * 100) / $arBalTotalMain, 2);
				
				$arBalTotalArr[] =$totalBalanceAging;
				$totalAmtAgingArr[] =$totalAmtAging;
				
				if($totalBalanceAging > 0){
					$arAgingDataArr[$fac_id_grp_key][$agingStart] = $CLSReports->numberFormat($totalBalanceAging,2).' ('.$enc_avg.'%)';
					$arAgingDataArr_wofac[$agingStart][] = $totalBalanceAging;
				}
			}

			$arBalTotalStr = array_sum($arBalTotalArr);
			$totalAmtAging = array_sum($totalAmtAgingArr);
			unset($arBalTotalArr);
			unset($totalAmtAgingArr);
			$enc_avg = number_format(($arBalTotalStr * 100) / $arBalTotalMain, 2);

			$arAgingDataArr[$fac_id_grp_key]['total_ar_bal'] = $CLSReports->numberFormat($arBalTotalStr, 2).' ('.$enc_avg.'%)';
			$arAgingDataArr_wofac['total_ar_bal'][] = $arBalTotalStr;
		}
		//  END AGING CYCLE			
		
				
	//echo "<pre>";
	//print_r($fac_pay_data_rec);
	if(count($monthArr)>0){
		//--- MONTH HEADER DATA ---
		list($stY, $stM, $stD) = preg_split("/-/",$start_date);		
		list($enY, $enM, $enD) = preg_split("/-/",$end_date);
		
		//--- GET TOTAL MONTH COUNT ---
		$endDate = $enY.$enM;
		$startDate = $stY.$stM;
		$monthRs = imw_query("select period_diff($endDate, $startDate) as months");
		$monthQryRes = imw_fetch_assoc($monthRs);
		$totalMonthCount = $monthQryRes['months']+1;
		
		$monthArr=array();
		$yearArr = array();
		for($d=0;$d<$totalMonthCount;$d++){
			$selectMonth = date('m', mktime(0,0,0,$stM + $d, $stD, $stY));
			$selectYear = date('y', mktime(0,0,0,$stM + $d, $stD, $stY));
			$selectMonthName = $monthNameArr[$selectMonth].'-'.$selectYear;
			$yearArr[$selectMonthName] = $selectMonthName;
		}
		$monthArr = array_keys($yearArr);
		$monthArr['total']="Total";
		if($processReport == 'Detail'){
			$data="";
			$grand_month_tot_pay=array();
			$month_based_width=(1024/(count($monthArr)+1));
			$data='<tr><td class="text_b_w" style="width:'.$month_based_width.'px;">&nbsp;</td>';
				 foreach($monthArr as $mon_name_key => $mon_name_val){
				   $data .='<td class="text_b_w" style="text-align:right;width:'.$month_based_width.'px;">'.ucfirst($mon_name_val).'</td>';
				}
		   $data .='</tr>'; 
		  if($org_fac!=""){
			   foreach($selectedFacGroupArr as $fac_grp_key => $fac_grp_val){
				   $data .='<tr><td class="text_b_w" colspan="'.(count($monthArr)+1).'" height="1px" style="width:'.$month_based_width.'px;text-align:center;">'.ucfirst($fac_grp_val).'</td></tr>';
					$data .='<tr>
						<td class="text_10b" style="width:'.$month_based_width.'px;background:#FFFFFF;">Total Appts</td>';
					$rg_mon_app_arr=array();
					foreach($monthArr as $mon_key => $mon_val){
						$tot_app=count($fac_app_data_rec[$fac_grp_key][$mon_val]);
						$grand_month_tot_app[$mon_val][]=$tot_app;
						$rg_mon_app_arr[]=$tot_app;
						if($mon_val=="Total"){
							$rg_mon_app=array_sum($rg_mon_app_arr);
							$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10b">'.$rg_mon_app.'</td>';
						}else{
							$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10b">'.$tot_app.'</td>';	
						}
					}
					$data .='</tr>'; 
					foreach($fac_id_data_arr as $fac_org_key => $fac_org_val){	
						$sr=0;
						if(count($fac_app_org_data_rec_detail[$fac_grp_key][$fac_org_key])>0){	
							$data .='<tr>
							<td  class="text_10b" style="width:'.$month_based_width.'px;background:#FF9900;cursor:pointer;" onclick="toggleTblNew(\'app_det_tbl_'.$fac_grp_key.$fac_org_key.'\');">
							<span id="icon_app_det_tbl_'.$fac_grp_key.$fac_org_key.'" style="float:right;" class="ui-icon ui-icon-circle-arrow-n fl"></span>'.ucfirst($fac_id_data_arr[$fac_org_key]).'</td>';
							$rg_tot_mon_app_arr=array();
							foreach($monthArr as $mon_key8 => $mon_val8){
								$tot_fac_app=count($fac_app_org_data_rec_detail[$fac_grp_key][$fac_org_key][$mon_val8]);
								$rg_tot_mon_app_arr[]=$tot_fac_app;
								if($mon_val8=="Total"){
									$rg_tot_mon_app=array_sum($rg_tot_mon_app_arr);
									$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10">'.$rg_tot_mon_app.'</td>';
								}else{
									$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10">'.$tot_fac_app.'</td>';
								}
							}
							$data .='</tr>'; 
						}
						if(count($fac_app_org_data_all_detail[$fac_grp_key][$fac_org_key])>0){
							$data .='</table><table  class="rpt_table rpt rpt_table-bordered rpt_padding" id="app_det_tbl_'.$fac_grp_key.$fac_org_key.'">
									<tr>
										<td class="text_b_w" style="width:60px;">#</td>
										<td class="text_b_w" style="width:330px;">Patient Name -  ID</td>
										<td class="text_b_w" style="width:330px;">Appointment Date Time</td>
										<td class="text_b_w" style="width:320px;">Procedure</td>
									</tr>'; 
							foreach($fac_app_org_data_all_detail[$fac_grp_key][$fac_org_key] as $fac_grp_id_key2 => $fac_grp_id_val2){
								$sr++;
								$app_dat_time=$fac_grp_id_val2['sa_app_start_date_show'].' ('.$fac_grp_id_val2['sa_app_starttime_show']
								.' - '.$fac_grp_id_val2['sa_app_endtime_show'].')';
								$data .='<tr>
										<td style="background:#FFFFFF;" class="text_10">'.$sr.'</td>
										<td style="background:#FFFFFF;" class="text_10">'.$fac_grp_id_val2['patient_name'].'</td>
										<td style="background:#FFFFFF;" class="text_10">'.$app_dat_time.'</td>
										<td style="background:#FFFFFF;" class="text_10">'.$fac_grp_id_val2['procedure_name'].'</td>
									</tr>';
							}
							 $data .='<tr><td bgcolor="#ffffff" colspan="4" height="10px" style="width:1024px;"></td></tr>';
							$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">';
						}
			   		}
				}
			}else{
				 foreach($selectedFacGroupArr as $fac_grp_key => $fac_grp_val){
				   $data .='<tr><td class="text_b_w" colspan="'.(count($monthArr)+1).'" height="1px" style="width:'.$month_based_width.'px;text-align:center;">'.ucfirst($fac_grp_val).'</td></tr>';
	
					$data .='<tr>
						<td class="text_10b" style="width:'.$month_based_width.'px;background:#FFFFFF;">Total Appts</td>';
					$rg_mon_app_arr=array();
					foreach($monthArr as $mon_key => $mon_val){
						$tot_app=count($fac_app_data_rec[$fac_grp_key][$mon_val]);
						$grand_month_tot_app[$mon_val][]=$tot_app;
						$rg_mon_app_arr[]=$tot_app;
						if($mon_val=="Total"){
							$rg_mon_app=array_sum($rg_mon_app_arr);
							$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10b">'.$rg_mon_app.'</td>';
						}else{
							$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10b">'.$tot_app.'</td>';	
						}
					}
					$data .='</tr>'; 
					$sr=0;
					foreach($final_sel_grp_fac_arr[$fac_grp_key] as $fac_grp_id_key => $fac_grp_id_val){
						if(count($fac_app_data_rec_detail[$fac_grp_key][$fac_grp_id_val])>0){	
							$data .='<tr>
							<td  class="text_10b" style="width:'.$month_based_width.'px;background:#FF9900;cursor:pointer;" onclick="toggleTblNew(\'app_det_tbl_'.$fac_grp_key.$fac_grp_id_val.'\');">
							<span id="icon_app_det_tbl_'.$fac_grp_key.$fac_grp_id_val.'" style="float:right;" class="ui-icon ui-icon-circle-arrow-n fl"></span>'.ucfirst($fac_tbl_name_arr[$fac_grp_id_val]).'</td>';
							$rg_tot_mon_app_arr=array();
							foreach($monthArr as $mon_key8 => $mon_val8){
								$tot_fac_app=count($fac_app_data_rec_detail[$fac_grp_key][$fac_grp_id_val][$mon_val8]);
								$rg_tot_mon_app_arr[]=$tot_fac_app;
								if($mon_val8=="Total"){
									$rg_tot_mon_app=array_sum($rg_tot_mon_app_arr);
									$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10">'.$rg_tot_mon_app.'</td>';
								}else{
									$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10">'.$tot_fac_app.'</td>';
								}
							}
							$data .='</tr>'; 
						}
						if(count($fac_app_data_all_detail[$fac_grp_key][$fac_grp_id_val])>0){
							$data .='</table><table  class="rpt_table rpt rpt_table-bordered rpt_padding" id="app_det_tbl_'.$fac_grp_key.$fac_grp_id_val.'">
									<tr>
										<td class="text_b_w" style="width:60px;">#</td>
										<td class="text_b_w" style="width:330px;">Patient Name -  ID</td>
										<td class="text_b_w" style="width:330px;">Appointment Date Time</td>
										<td class="text_b_w" style="width:320px;">Procedure</td>
									</tr>'; 
							foreach($fac_app_data_all_detail[$fac_grp_key][$fac_grp_id_val] as $fac_grp_id_key2 => $fac_grp_id_val2){
								$sr++;
								$app_dat_time=$fac_grp_id_val2['sa_app_start_date_show'].' ('.$fac_grp_id_val2['sa_app_starttime_show']
								.' - '.$fac_grp_id_val2['sa_app_endtime_show'].')';
								$data .='<tr>
										<td style="background:#FFFFFF;" class="text_10">'.$sr.'</td>
										<td style="background:#FFFFFF;" class="text_10">'.$fac_grp_id_val2['patient_name'].'</td>
										<td style="background:#FFFFFF;" class="text_10">'.$app_dat_time.'</td>
										<td style="background:#FFFFFF;" class="text_10">'.$fac_grp_id_val2['procedure_name'].'</td>
									</tr>';
							}
							 $data .='<tr><td bgcolor="#ffffff" colspan="4" height="10px" style="width:1024px;"></td></tr>';
							$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">';
						}
					}
				}
			}
			$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Grand Total</td>';
			$rg_gr_mon_app_arr=array();
			foreach($monthArr as $mon_key3 => $mon_val3){
				$tot_app_mon=array_sum($grand_month_tot_app[$mon_val3]);
				$rg_gr_mon_app_arr[]=$tot_app_mon;
				if($mon_val3=="Total"){
					$rg_gr_mon_app=array_sum($rg_gr_mon_app_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_gr_mon_app.'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$tot_app_mon.'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';

			
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
		if($org_fac!=""){
			foreach($selectedFacGroupArr as $fac_grp_key_sec => $fac_grp_val_sec){
				$data .='<tr><td class="text_10b" style="width:'.$month_based_width.'px;cursor:pointer;background:#FF9900;text-align:center;" colspan="'.(count($monthArr)+1).'" onclick="toggleTblNew(\'chg_det_tbl_'.$fac_grp_key_sec.'\');toggleTblNew(\'pay_det_tbl_'.$fac_grp_key_sec.'\');">
				<span id="icon_chg_det_tbl_'.$fac_grp_key_sec.'" style="float:right;" class="ui-icon ui-icon-circle-arrow-n fl"></span>'.ucfirst($fac_grp_val_sec).'</td></tr>';
				if(count($fac_org_chg_data_rec[$fac_grp_key_sec])>0){
					$data .='<tr>
								<td colspan="'.(count($monthArr)+1).'" style="text-align:left;width:'.$month_based_width.'px;" class="text_b_w" style="width:1024px;">
								Originating Facility Charges</td>
						</tr>';
					$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding" id="chg_det_tbl_'.$fac_grp_key_sec.'">';
					$grand_month_org_chg=array();
					foreach($fac_id_data_arr as $fac_org_key => $fac_org_val){	
						$fac_org_id=$fac_org_key;
						if(count($fac_org_chg_data_rec[$fac_grp_key_sec][$fac_org_id])>0){
							$data .='<tr>
								<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">'.$fac_id_data_arr[$fac_org_id].'</td>';
							$rg_org_chg_arr=array();
							foreach($monthArr as $mon_key8 => $mon_val8){
								$tot_org_fac_mon=array_sum($fac_org_chg_data_rec[$fac_grp_key_sec][$fac_org_id][$mon_val8]);
								$rg_org_chg_arr[]=$tot_org_fac_mon;
								$gr_fac_chg_data_rec[$mon_val8][]=$tot_org_fac_mon;
								if($mon_val8=="Total"){
									$rg_org_chg=array_sum($rg_org_chg_arr);
									$grand_month_org_chg[$mon_val8][]=$rg_org_chg;
									$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_org_chg,2).'</td>';
								}else{
									$grand_month_org_chg[$mon_val8][]=$tot_org_fac_mon;
									$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_org_fac_mon,2).'</td>';
								}
							}
							$data .='</tr>';
						}
					}
					$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">';
					$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px" style="width:1024px;"></td></tr>';
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total</td>';
					foreach($monthArr as $mon_key9 => $mon_val9){
						$grand_org_fac_chg=array_sum($grand_month_org_chg[$mon_val9]);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($grand_org_fac_chg,2).'</td>';
					}
					$data .='</tr>'; 
					$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px" style="width:1024px;"></td></tr>';
					$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				}
				if(count($fac_org_pay_data_rec[$fac_grp_key_sec])>0){
					$data .='<tr>
								<td colspan="'.(count($monthArr)+1).'" style="text-align:left;width:'.$month_based_width.'px;" class="text_b_w" style="width:1024px;">
								Originating Facility Rcpt</td>
						</tr>';
					$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding" id="pay_det_tbl_'.$fac_grp_key_sec.'">';
					$grand_month_org_pay=array();	
					foreach($fac_id_data_arr as $fac_org_key2 => $fac_org_val2){	
						$fac_org_id2=$fac_org_key2;
						if(count($fac_org_pay_data_rec[$fac_grp_key_sec][$fac_org_id2])>0){
							$data .='<tr>
								<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">'.$fac_id_data_arr[$fac_org_id2].'</td>';
							$rg_org_pay_arr=array();
							foreach($monthArr as $mon_key10 => $mon_val10){
								$tot_org_fac_pay_mon=array_sum($fac_org_pay_data_rec[$fac_grp_key_sec][$fac_org_id2][$mon_val10]);
								$rg_org_pay_arr[]=$tot_org_fac_pay_mon;
								$gr_fac_pay_data_rec[$mon_val10][]=$tot_org_fac_pay_mon;
								if($mon_val10=="Total"){
									$rg_org_pay=array_sum($rg_org_pay_arr);
									$grand_month_org_pay[$mon_val10][]=$rg_org_pay;
									$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_org_pay,2).'</td>';
								}else{
									$grand_month_org_pay[$mon_val10][]=$tot_org_fac_pay_mon;
									$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_org_fac_pay_mon,2).'</td>';
								}
							}
							$data .='</tr>'; 
						}
					}
					$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">';
					$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px" style="width:1024px;"></td></tr>';
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total</td>';
					foreach($monthArr as $mon_key11 => $mon_val11){
						$grand_org_fac_pay=array_sum($grand_month_org_pay[$mon_val11]);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($grand_org_fac_pay,2).'</td>';
					}
					$data .='</tr>'; 
					$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px" style="width:1024px;"></td></tr>';
					$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				}
				$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				$data .='<tr>
					<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Summary Charges</td>';
				$rg_tot_sum_chg_arr=array();
				foreach($monthArr as $mon_key7 => $mon_val7){
					$tot_sum_chg_mon=array_sum($summaryTotalChrgArr[$fac_grp_key_sec][$mon_val7]);
					$rg_tot_sum_chg_arr[]=$tot_sum_chg_mon;
					$gr_tot_sum_chg_arr[$mon_val7][]=$tot_sum_chg_mon;
					$tot_tot_sum_chg_arr[$mon_val7][]=$tot_sum_chg_mon;
					if($mon_val7=="Total"){
						$rg_tot_sum_chg=array_sum($rg_tot_sum_chg_arr);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_tot_sum_chg,2).'</td>';
					}else{
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($tot_sum_chg_mon,2).'</td>';
					}
				}
				$data .='</tr>';
				$data .='<tr>
					<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Summary Receipts</td>';
				$rg_tot_sum_pay_arr=array();
				foreach($monthArr as $mon_key7 => $mon_val7){
					$tot_sum_pay_mon=array_sum($summaryTotalPaidArr[$fac_grp_key_sec][$mon_val7]);
					$rg_tot_sum_pay_arr[]=$tot_sum_pay_mon;
					$gr_tot_sum_pay_arr[$mon_val7][]=$tot_sum_pay_mon;
					$tot_tot_sum_pay_arr[$mon_val7][]=$tot_sum_pay_mon;
					if($mon_val7=="Total"){
						$rg_tot_sum_pay=array_sum($rg_tot_sum_pay_arr);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_tot_sum_pay,2).'</td>';
					}else{
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($tot_sum_pay_mon,2).'</td>';
					}
				}
				$data .='</tr>';
				$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">    	
					<tr>
						<td class="text_10b" style="background:#FFFFFF;" colspan="8">Total A/R AGING</td>
					</tr>
					<tr>';
					foreach($aggingDrop as $aging_kay => $aging_val){
						$data .='<td class="text_10b" style="background:#FFFFFF; width:125px; text-align:center;">'.$aging_val.'</td>';
					}
						$data .='<td class="text_10b" style="background:#FFFFFF; width:115px;">Total</td></tr><tr>';	
					
					foreach($aggingDrop as $aging_kay => $aging_val){
						$arAgingData=$arAgingDataArr[$fac_grp_key_sec][$aging_kay];
						$data .='<td class="text_10" style="background:#FFFFFF; width:125px; text-align:right;">'.$arAgingData.'</td>';
					}
						$arAgingData_bal=$arAgingDataArr[$fac_grp_key_sec]['total_ar_bal'];
						$data .='<td class="text_10" style="background:#FFFFFF; width:115px; text-align:right;">'.$arAgingData_bal.'</td>
					</tr>
				</table><table class="rpt_table rpt rpt_table-bordered rpt_padding"> ';
			}
		}else{
			$gr_fac_chg_data_rec=array();
			$gr_fac_pay_data_rec=array();
			foreach($selectedFacGroupArr as $fac_grp_key_sec => $fac_grp_val_sec){
				$data .='<tr><td class="text_10b" style="width:'.$month_based_width.'px;cursor:pointer;background:#FF9900;text-align:center;" colspan="'.(count($monthArr)+1).'" onclick="toggleTblNew(\'chg_det_tbl_'.$fac_grp_key_sec.'\');">
				<span id="icon_chg_det_tbl_'.$fac_grp_key_sec.'" style="float:right;"  class="ui-icon ui-icon-circle-arrow-n fl"></span>'.ucfirst($fac_grp_val_sec).'</td></tr>';
				$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding" id="chg_det_tbl_'.$fac_grp_key_sec.'">';
				$tot_tot_sum_chg_arr=array();
				$tot_tot_sum_pay_arr=array();
				foreach($final_sel_grp_fac_arr[$fac_grp_key_sec] as $fac_grp_id_key => $fac_grp_id_val){
					$proc_chg_mon_arr=array();
					$proc_chg_tot_mon_arr=array();
					foreach($fac_chg_data_proc_det_rec[$fac_grp_key_sec][$fac_grp_id_val] as $fac_grp_key_proc => $fac_grp_val_proc){
						foreach($monthArr as $mon_key12 => $mon_val12){
							$proc_chg_mon_arr[$mon_val12][]=count($fac_chg_data_proc_det_rec[$fac_grp_key_sec][$fac_grp_id_val][$fac_grp_key_proc][$mon_val12]);
							$proc_chg_tot_mon_arr[]=count($fac_chg_data_proc_det_rec[$fac_grp_key_sec][$fac_grp_id_val][$fac_grp_key_proc][$mon_val12]);
						}
					}
					$fac_id_chk="";
					$fac_id_chk=$fac_tbl_pos_id_arr[$fac_grp_id_val];
					$pos_fac_name=$fac_id_data_arr[$fac_id_chk];
					$data .='<tr>
								<td  class="text_10b" colspan="'.(count($monthArr)+1).'" style="background:#FFFFFF;width:'.$month_based_width.'px;">'.ucfirst($pos_fac_name).'</td></tr>';
					
					foreach($fac_chg_data_proc_det_rec[$fac_grp_key_sec][$fac_grp_id_val] as $fac_grp_key_proc => $fac_grp_val_proc){
						$data_avg="";	
						$data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">'.ucfirst($fac_grp_key_proc).'</td>';
						$data_avg .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">&nbsp;</td>';
							$rg_proc_chg_arr=array();
							foreach($monthArr as $mon_key12 => $mon_val12){
								$proc_chg_mon=count($fac_chg_data_proc_det_rec[$fac_grp_key_sec][$fac_grp_id_val][$fac_grp_key_proc][$mon_val12]);
								$rg_proc_chg_arr[]=$proc_chg_mon;
								if($mon_val12=="Total"){
									$rg_proc_chg=array_sum($rg_proc_chg_arr);
									$proc_chg_tot_mon_cont=array_sum($proc_chg_tot_mon_arr);
									$rg_proc_chg_avg="";
									if($rg_proc_chg>0){
										$rg_proc_chg_avg=number_format(($rg_proc_chg*100)/$proc_chg_tot_mon_cont,2).'%';
									}else{
										$rg_proc_chg_avg="";
									}
									$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_proc_chg.'</td>';
									$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_proc_chg_avg.'</td>';
								}else{
									$proc_chg_mon_cont = array_sum($proc_chg_mon_arr[$mon_val12]);
									$proc_chg_mon_avg="";
									if($proc_chg_mon>0){
										$proc_chg_mon_avg=number_format(($proc_chg_mon*100)/$proc_chg_mon_cont,2).'%';
									}else{
										$proc_chg_mon="";
									}
									$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$proc_chg_mon.'</td>';
									$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$proc_chg_mon_avg.'</td>';
								}
							}
						$data_avg .='</tr>'; 	
						$data .='</tr>'.$data_avg; 
						$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
					}
					
					$data .='<tr>
					<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Charges</td>';
					$rg_tot_proc_chg_arr=array();
					foreach($monthArr as $mon_key4 => $mon_val4){
						$tot_chg_mon=array_sum($fac_chg_data_det_rec[$fac_grp_key_sec][$fac_grp_id_val][$mon_val4]);
						$rg_tot_proc_chg_arr[]=$tot_chg_mon;
						if($mon_val4=="Total"){
							$rg_tot_proc_chg=array_sum($rg_tot_proc_chg_arr);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($rg_tot_proc_chg,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($tot_chg_mon,2).'</td>';
						}
					}
					$data .='</tr>'; 
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Avg Chg</td>';
					$rg_tot_chg_arr=array();
					$rg_tot_chg_avg_mon=array();
					foreach($monthArr as $mon_key5 => $mon_val5){
						$y_exp=explode('-',$mon_val5);
						$yy='20'.$y_exp[1];
						//$this_month_days=date('t',mktime(0,0,0,$mon_key5,1,$yy));
						$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
						$tot_avg_mon=(array_sum($fac_chg_data_det_rec[$fac_grp_key_sec][$fac_grp_id_val][$mon_val5])/$this_month_days);
						$rg_tot_chg_arr[]=array_sum($fac_chg_data_det_rec[$fac_grp_key_sec][$fac_grp_id_val][$mon_val5]);
						$rg_tot_mon_cont_arr[]=$this_month_days;
						if($mon_val5=="Total"){
							$rg_tot_chg=array_sum($rg_tot_chg_arr);
							$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
							$rg_tot_chg_avg_mon=($rg_tot_chg/$rg_tot_mon_cont);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_chg_avg_mon,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_avg_mon,2).'</td>';
						}
					}
					$data .='</tr>'; 
					$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Receipts</td>';
					$rg_tot_proc_pay_arr=array();
					foreach($monthArr as $mon_key7 => $mon_val7){
						$tot_pay_mon=array_sum($fac_pay_data_det_rec[$fac_grp_key_sec][$fac_grp_id_val][$mon_val7]);
						$rg_tot_proc_pay_arr[]=$tot_pay_mon;
						if($mon_val7=="Total"){
							$rg_tot_proc_pay=array_sum($rg_tot_proc_pay_arr);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_proc_pay,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_pay_mon,2).'</td>';
						}
					}
					$data .='</tr>'; 
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Avg Rcpt</td>';
					$rg_tot_pay_arr=array();
					$rg_tot_mon_cont_arr=array();
					foreach($monthArr as $mon_key6 => $mon_val6){
						$y_exp=explode('-',$mon_val6);
						$yy='20'.$y_exp[1];
						//$this_month_days=date('t',mktime(0,0,0,$mon_key6,1,$yy));
						$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
						$tot_pay_avg_mon=(array_sum($fac_pay_data_det_rec[$fac_grp_key_sec][$fac_grp_id_val][$mon_val6])/$this_month_days);
						$rg_tot_pay_arr[]=array_sum($fac_pay_data_det_rec[$fac_grp_key_sec][$fac_grp_id_val][$mon_val6]);
						$rg_tot_mon_cont_arr[]=$this_month_days;
						if($mon_val6=="Total"){
							$rg_tot_pay=array_sum($rg_tot_pay_arr);
							$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
							$rg_tot_pay_avg_mon=($rg_tot_pay/$rg_tot_mon_cont);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_pay_avg_mon,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_pay_avg_mon,2).'</td>';
						}
					}
					$data .='</tr>';
					$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Summary Charges</td>';
					$rg_tot_sum_chg_arr=array();
					foreach($monthArr as $mon_key7 => $mon_val7){
						$tot_sum_chg_mon=array_sum($summaryFacTotalChrgArr[$fac_grp_key_sec][$fac_grp_id_val][$mon_val7]);
						$rg_tot_sum_chg_arr[]=$tot_sum_chg_mon;
						$gr_tot_sum_chg_arr[$mon_val7][]=$tot_sum_chg_mon;
						$tot_tot_sum_chg_arr[$mon_val7][]=$tot_sum_chg_mon;
						if($mon_val7=="Total"){
							$rg_tot_sum_chg=array_sum($rg_tot_sum_chg_arr);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($rg_tot_sum_chg,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($tot_sum_chg_mon,2).'</td>';
						}
					}
					$data .='</tr>';
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Summary Receipts</td>';
					$rg_tot_sum_pay_arr=array();
					foreach($monthArr as $mon_key7 => $mon_val7){
						$tot_sum_pay_mon=array_sum($summaryFacTotalPaidArr[$fac_grp_key_sec][$fac_grp_id_val][$mon_val7]);
						$rg_tot_sum_pay_arr[]=$tot_sum_pay_mon;
						$gr_tot_sum_pay_arr[$mon_val7][]=$tot_sum_pay_mon;
						$tot_tot_sum_pay_arr[$mon_val7][]=$tot_sum_pay_mon;
						if($mon_val7=="Total"){
							$rg_tot_sum_pay=array_sum($rg_tot_sum_pay_arr);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($rg_tot_sum_pay,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($tot_sum_pay_mon,2).'</td>';
						}
					}
					$data .='</tr>';
					$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				}
				$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">';
				$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Charges</td>';
				$rg_tot_proc_chg_arr=array();
				foreach($monthArr as $mon_key4 => $mon_val4){
					$tot_chg_mon=array_sum($fac_chg_data_rec[$fac_grp_key_sec][$mon_val4]);
					$gr_fac_chg_data_rec[$mon_val4][]=$tot_chg_mon;
					$rg_tot_proc_chg_arr[]=$tot_chg_mon;
					if($mon_val4=="Total"){
						$rg_tot_proc_chg=array_sum($rg_tot_proc_chg_arr);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_tot_proc_chg,2).'</td>';
					}else{
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($tot_chg_mon,2).'</td>';
					}
				}
				$data .='</tr>'; 
				$data .='<tr>
					<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Charges</td>';
				$total_rg_tot_chg_arr=array();
				$rg_tot_mon_cont_arr=array();
				foreach($monthArr as $mon_key5 => $mon_val5){
					$y_exp=explode('-',$mon_val5);
					$yy='20'.$y_exp[1];
					//$this_month_days=date('t',mktime(0,0,0,$mon_key5,1,$yy));
					$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
					$tot_avg_mon=(array_sum($fac_chg_data_rec[$fac_grp_key_sec][$mon_val5])/$this_month_days);
					$total_rg_tot_chg_arr[]=array_sum($fac_chg_data_rec[$fac_grp_key_sec][$mon_val5]);
					$rg_tot_mon_cont_arr[]=$this_month_days;
					if($mon_val5=="Total"){
						$total_rg_tot_chg=array_sum($total_rg_tot_chg_arr);
						$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
						$total_rg_tot_chg_avg_mon=($total_rg_tot_chg/$rg_tot_mon_cont);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($total_rg_tot_chg_avg_mon,2).'</td>';
					}else{
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_avg_mon,2).'</td>';
					}
				}
				$data .='</tr>'; 
				$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				$data .='<tr>
					<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Receipts</td>';
				$rg_tot_proc_pay_arr=array();
				foreach($monthArr as $mon_key7 => $mon_val7){
					$tot_pay_mon=array_sum($fac_pay_data_rec[$fac_grp_key_sec][$mon_val7]);
					$gr_fac_pay_data_rec[$mon_val7][]=$tot_pay_mon;
					$rg_tot_proc_pay_arr[]=$tot_pay_mon;
					if($mon_val7=="Total"){
						$rg_tot_proc_pay=array_sum($rg_tot_proc_pay_arr);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_tot_proc_pay,2).'</td>';
					}else{
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($tot_pay_mon,2).'</td>';
					}
				}
				$data .='</tr>'; 
				$data .='<tr>
					<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Receipts</td>';
				$total_rg_tot_pay_arr=array();
				$rg_tot_mon_cont_arr=array();
				foreach($monthArr as $mon_key6 => $mon_val6){
					$y_exp=explode('-',$mon_val6);
					$yy='20'.$y_exp[1];
					//$this_month_days=date('t',mktime(0,0,0,$mon_key6,1,$yy));
					$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
					$tot_pay_avg_mon=(array_sum($fac_pay_data_rec[$fac_grp_key_sec][$mon_val6])/$this_month_days);
					$total_rg_tot_pay_arr[]=array_sum($fac_pay_data_rec[$fac_grp_key_sec][$mon_val6]);
					$rg_tot_mon_cont_arr[]=$this_month_days;
					if($mon_val6=="Total"){
						$total_rg_tot_pay=array_sum($total_rg_tot_pay_arr);
						$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
						$total_rg_tot_pay_avg_mon=($total_rg_tot_pay/$rg_tot_mon_cont);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($total_rg_tot_pay_avg_mon,2).'</td>';
					}else{
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_pay_avg_mon,2).'</td>';
					}
				}
				$data .='</tr>';
				$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				$data .='<tr>
					<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Summary Charges</td>';
				$rg_tot_tot_sum_chg_arr=array();
				foreach($monthArr as $mon_key7 => $mon_val7){
					$tot_tot_sum_chg=array_sum($tot_tot_sum_chg_arr[$mon_val7]);
					$rg_tot_tot_sum_chg_arr[]=$tot_tot_sum_chg;
					if($mon_val7=="Total"){
						$rg_tot_tot_sum_chg=array_sum($rg_tot_tot_sum_chg_arr);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_tot_tot_sum_chg,2).'</td>';
					}else{
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($tot_tot_sum_chg,2).'</td>';
					}
				}
				$data .='</tr>'; 
				$data .='<tr>
					<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Summary Receipts</td>';
				$rg_tot_tot_sum_pay_arr=array();
				foreach($monthArr as $mon_key7 => $mon_val7){
					$tot_tot_sum_pay=array_sum($tot_tot_sum_pay_arr[$mon_val7]);
					$rg_tot_tot_sum_pay_arr[]=$tot_tot_sum_pay;
					if($mon_val7=="Total"){
						$rg_tot_tot_sum_pay=array_sum($rg_tot_tot_sum_pay_arr);
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_tot_tot_sum_pay,2).'</td>';
					}else{
						$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($tot_tot_sum_pay,2).'</td>';
					}
				}
				$data .='</tr>'; 	
				$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">    	
					<tr>
						<td class="text_10b" style="background:#FFFFFF;" colspan="8">Total A/R AGING</td>
					</tr>
					<tr>';
					foreach($aggingDrop as $aging_kay => $aging_val){
						$data .='<td class="text_10b" style="background:#FFFFFF; width:125px; text-align:center;">'.$aging_val.'</td>';
					}
						$data .='<td class="text_10b" style="background:#FFFFFF; width:115px;">Total</td></tr><tr>';	
					
					foreach($aggingDrop as $aging_kay => $aging_val){
						$arAgingData=$arAgingDataArr[$fac_grp_key_sec][$aging_kay];
						$data .='<td class="text_10" style="background:#FFFFFF; width:125px; text-align:right;">'.$arAgingData.'</td>';
					}
						$arAgingData_bal=$arAgingDataArr[$fac_grp_key_sec]['total_ar_bal'];
						$data .='<td class="text_10" style="background:#FFFFFF; width:115px; text-align:right;">'.$arAgingData_bal.'</td>
					</tr>
				</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">';
			}
		}
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
			$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';
			$data .='<tr>
			<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G.Total Chg</td>';
			$rg_tot_proc_chg_arr=array();
			foreach($monthArr as $mon_key4 => $mon_val4){
				$tot_chg_mon=array_sum($gr_fac_chg_data_rec[$mon_val4]);
				$rg_tot_proc_chg_arr[]=$tot_chg_mon;
				if($mon_val4=="Total"){
					$rg_tot_proc_chg=array_sum($rg_tot_proc_chg_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_tot_proc_chg,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($tot_chg_mon,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Chg</td>';
			$gr_rg_tot_chg_arr=array();
			$rg_tot_mon_cont_arr=array();
			foreach($monthArr as $mon_key5 => $mon_val5){
				$y_exp=explode('-',$mon_val5);
				$yy='20'.$y_exp[1];
				//$this_month_days=date('t',mktime(0,0,0,$mon_key5,1,$yy));
				$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
				$tot_avg_mon=(array_sum($gr_fac_chg_data_rec[$mon_val5])/$this_month_days);
				$gr_rg_tot_chg_arr[]=array_sum($gr_fac_chg_data_rec[$mon_val5]);
				$rg_tot_mon_cont_arr[]=$this_month_days;
				if($mon_val5=="Total"){
					$gr_rg_tot_chg=array_sum($gr_rg_tot_chg_arr);
					$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
					$gr_rg_tot_chg_avg_mon=($gr_rg_tot_chg/$rg_tot_mon_cont);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_rg_tot_chg_avg_mon,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_avg_mon,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G.Total Rcpt</td>';
			$rg_tot_proc_pay_arr=array();
			foreach($monthArr as $mon_key7 => $mon_val7){
				$tot_pay_mon=array_sum($gr_fac_pay_data_rec[$mon_val7]);
				$rg_tot_proc_pay_arr[]=$tot_pay_mon;
				if($mon_val7=="Total"){
					$rg_tot_proc_pay=array_sum($rg_tot_proc_pay_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_tot_proc_pay,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($tot_pay_mon,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Rcpt</td>';
			$gr_rg_tot_pay_arr=array();
			$rg_tot_mon_cont_arr=array();
			foreach($monthArr as $mon_key6 => $mon_val6){
				$y_exp=explode('-',$mon_val6);
				$yy='20'.$y_exp[1];
				//$this_month_days=date('t',mktime(0,0,0,$mon_key6,1,$yy));
				$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
				$tot_pay_avg_mon=(array_sum($gr_fac_pay_data_rec[$mon_val6])/$this_month_days);
				$gr_rg_tot_pay_arr[]=array_sum($gr_fac_pay_data_rec[$mon_val6]);
				$rg_tot_mon_cont_arr[]=$this_month_days;
				if($mon_val6=="Total"){
					$gr_rg_tot_pay=array_sum($gr_rg_tot_pay_arr);
					$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
					$gr_rg_tot_pay_avg_mon=($gr_rg_tot_pay/$rg_tot_mon_cont);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_rg_tot_pay_avg_mon,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_pay_avg_mon,2).'</td>';
				}
			}
			$data .='</tr>';
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G.Total Summary Charges</td>';
			$rg_gr_tot_sum_chg_arr=array();
			foreach($monthArr as $mon_key7 => $mon_val7){
				$gr_tot_sum_chg=array_sum($summaryOrgTotalChrgArr[$mon_val7]);
				$rg_gr_tot_sum_chg_arr[]=$gr_tot_sum_chg;
				if($mon_val7=="Total"){
					$rg_gr_tot_sum_chg=array_sum($rg_gr_tot_sum_chg_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_gr_tot_sum_chg,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($gr_tot_sum_chg,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G.Total Summary Receipts</td>';
			$rg_gr_tot_sum_pay_arr=array();
			foreach($monthArr as $mon_key7 => $mon_val7){
				$gr_tot_sum_pay=array_sum($summaryOrgTotalPaidArr[$mon_val7]);
				$rg_gr_tot_sum_pay_arr[]=$gr_tot_sum_pay;
				if($mon_val7=="Total"){
					$rg_gr_tot_sum_pay=array_sum($rg_gr_tot_sum_pay_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_gr_tot_sum_pay,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($gr_tot_sum_pay,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
			$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">    	
				<tr>
					<td class="text_10b" style="background:#FFFFFF;" colspan="8">G. Total A/R AGING</td>
				</tr>
				<tr>';
				$arAgingData="";
				$arAgingData_bal="";
				$arBalTotalStr="";
				$arBalTotalMain="";
				foreach($aggingDrop as $aging_kay => $aging_val){
					$data .='<td class="text_10b" style="background:#FFFFFF; width:125px; text-align:center;">'.$aging_val.'</td>';
				}
					$data .='<td class="text_10b" style="background:#FFFFFF; width:115px;">Total</td></tr><tr>';	
				$arBalTotalMain=array_sum($arAgingDataArr_wofac['total_ar_bal']);
				foreach($aggingDrop as $aging_kay => $aging_val){
					$arBalTotalStr=array_sum($arAgingDataArr_wofac[$aging_kay]);
					if($arBalTotalStr>0){
						$arAgingData=$CLSReports->numberFormat($arBalTotalStr,2).' ('.number_format(($arBalTotalStr * 100) / $arBalTotalMain,2).'%)';
					}
					$data .='<td class="text_10" style="background:#FFFFFF; width:125px; text-align:right;">'.$arAgingData.'</td>';
				}
					if($arBalTotalMain>0){
						$arAgingData_bal=$CLSReports->numberFormat($arBalTotalMain,2).' ('.number_format(($arBalTotalMain * 100) / $arBalTotalMain,2).'%)';
					}
					$data .='<td class="text_10" style="background:#FFFFFF; width:125px; text-align:right;">'.$arAgingData_bal.'</td>
				</tr>
			</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">';
			$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';
		}else{
			$data="";
			$grand_month_tot_pay=array();
			$month_based_width=(1024/(count($monthArr)+1));
			$data='<tr><td class="text_b_w" style="width:'.$month_based_width.'px;">&nbsp;</td>';
				 foreach($monthArr as $mon_name_key => $mon_name_val){
				   $data .='<td class="text_b_w" style="text-align:right;width:'.$month_based_width.'px;">'.ucfirst($mon_name_val).'</td>';
				}
		   $data .='</tr>'; 
		   
			$fac_data="";
			if($org_fac!=""){
			   foreach($selectedFacGroupArr as $fac_grp_key => $fac_grp_val){
				   $data .='<tr><td class="text_b_w" colspan="'.(count($monthArr)+1).'" height="1px" style="width:'.$month_based_width.'px;text-align:center;">'.ucfirst($fac_grp_val).'</td></tr>';
					$data .='<tr>
						<td class="text_10b" style="width:'.$month_based_width.'px;background:#FFFFFF;">Total Appts</td>';
					$rg_mon_app_arr=array();
					foreach($monthArr as $mon_key => $mon_val){
						$tot_app=count($fac_app_data_rec[$fac_grp_key][$mon_val]);
						$grand_month_tot_app[$mon_val][]=$tot_app;
						$rg_mon_app_arr[]=$tot_app;
						if($mon_val=="Total"){
							$rg_mon_app=array_sum($rg_mon_app_arr);
							$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10b">'.$rg_mon_app.'</td>';
						}else{
							$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10b">'.$tot_app.'</td>';	
						}
					}
					$data .='</tr>'; 
					foreach($fac_id_data_arr as $fac_org_key => $fac_org_val){	
						$sr=0;
						if(count($fac_app_org_data_rec_detail[$fac_grp_key][$fac_org_key])>0){	
							$data .='<tr>
							<td  class="text_10b" style="width:'.$month_based_width.'px;background:#FFFFFF;cursor:pointer;">'.ucfirst($fac_id_data_arr[$fac_org_key]).'</td>';
							$rg_tot_mon_app_arr=array();
							foreach($monthArr as $mon_key8 => $mon_val8){
								$tot_fac_app=count($fac_app_org_data_rec_detail[$fac_grp_key][$fac_org_key][$mon_val8]);
								$rg_tot_mon_app_arr[]=$tot_fac_app;
								if($mon_val8=="Total"){
									$rg_tot_mon_app=array_sum($rg_tot_mon_app_arr);
									$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10">'.$rg_tot_mon_app.'</td>';
								}else{
									$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10">'.$tot_fac_app.'</td>';
								}
							}
							$data .='</tr>'; 
						}
			   		}
				}
			}else{
				 foreach($selectedFacGroupArr as $fac_grp_key => $fac_grp_val){
				   $data .='<tr><td class="text_b_w" colspan="'.(count($monthArr)+1).'" height="1px" style="width:'.$month_based_width.'px;text-align:center;">'.ucfirst($fac_grp_val).'</td></tr>';
	
					$data .='<tr>
						<td class="text_10b" style="width:'.$month_based_width.'px;background:#FFFFFF;">Total Appts</td>';
					$rg_mon_app_arr=array();
					foreach($monthArr as $mon_key => $mon_val){
						$tot_app=count($fac_app_data_rec[$fac_grp_key][$mon_val]);
						$grand_month_tot_app[$mon_val][]=$tot_app;
						$rg_mon_app_arr[]=$tot_app;
						if($mon_val=="Total"){
							$rg_mon_app=array_sum($rg_mon_app_arr);
							$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10b">'.$rg_mon_app.'</td>';
						}else{
							$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10b">'.$tot_app.'</td>';	
						}
					}
					$data .='</tr>'; 
					$sr=0;
					foreach($final_sel_grp_fac_arr[$fac_grp_key] as $fac_grp_id_key => $fac_grp_id_val){
						if(count($fac_app_data_rec_detail[$fac_grp_key][$fac_grp_id_val])>0){	
							$data .='<tr>
							<td  class="text_10" style="width:'.$month_based_width.'px;background:#FFFFFF;cursor:pointer;">'.ucfirst($fac_tbl_name_arr[$fac_grp_id_val]).'</td>';
							$rg_tot_mon_app_arr=array();
							foreach($monthArr as $mon_key8 => $mon_val8){
								$tot_fac_app=count($fac_app_data_rec_detail[$fac_grp_key][$fac_grp_id_val][$mon_val8]);
								$rg_tot_mon_app_arr[]=$tot_fac_app;
								if($mon_val8=="Total"){
									$rg_tot_mon_app=array_sum($rg_tot_mon_app_arr);
									$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10">'.$rg_tot_mon_app.'</td>';
								}else{
									$data .='<td style="text-align:right;width:'.$month_based_width.'px;background:#FFFFFF;" class="text_10">'.$tot_fac_app.'</td>';
								}
							}
							$data .='</tr>'; 
						}
					}
				}
			}
			$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Grand Total</td>';
			$rg_gr_mon_app_arr=array();
			foreach($monthArr as $mon_key3 => $mon_val3){
				$tot_app_mon=array_sum($grand_month_tot_app[$mon_val3]);
				$rg_gr_mon_app_arr[]=$tot_app_mon;
				if($mon_val3=="Total"){
					$rg_gr_mon_app=array_sum($rg_gr_mon_app_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_gr_mon_app.'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$tot_app_mon.'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';

			
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
			
			$gr_fac_chg_data_rec=array();
			$gr_fac_pay_data_rec=array();
			foreach($selectedFacGroupArr as $fac_grp_key_sec => $fac_grp_val_sec){
				$data .='<tr><td class="text_b_w" style="width:'.$month_based_width.'px;text-align:center;" colspan="'.(count($monthArr)+1).'">'.ucfirst($fac_grp_val_sec).'</td></tr>';
				
				$proc_chg_mon_arr=array();
				$proc_chg_tot_mon_arr=array();
				foreach($fac_chg_data_proc_rec[$fac_grp_key_sec] as $fac_grp_key_proc => $fac_grp_val_proc){
					foreach($monthArr as $mon_key12 => $mon_val12){
						$proc_chg_mon_arr[$mon_val12][]=count($fac_chg_data_proc_rec[$fac_grp_key_sec][$fac_grp_key_proc][$mon_val12]);
						$proc_chg_tot_mon_arr[]=count($fac_chg_data_proc_rec[$fac_grp_key_sec][$fac_grp_key_proc][$mon_val12]);
					}
				}
				foreach($fac_chg_data_proc_rec[$fac_grp_key_sec] as $fac_grp_key_proc => $fac_grp_val_proc){
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">'.ucfirst($fac_grp_key_proc).'</td>';
					$data_avg="";	
					$data_avg .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">&nbsp;</td>';
						$rg_proc_chg_arr=array();
						
						foreach($monthArr as $mon_key12 => $mon_val12){
							$proc_chg_mon=count($fac_chg_data_proc_rec[$fac_grp_key_sec][$fac_grp_key_proc][$mon_val12]);
							$rg_proc_chg_arr[]=$proc_chg_mon;
							
							if($mon_val12=="Total"){
								$rg_proc_chg=array_sum($rg_proc_chg_arr);
								$proc_chg_tot_mon_cont=array_sum($proc_chg_tot_mon_arr);
								$rg_proc_chg_avg="";
								if($rg_proc_chg>0){
									$rg_proc_chg_avg=number_format(($rg_proc_chg*100)/$proc_chg_tot_mon_cont,2).'%';
								}else{
									$rg_proc_chg_avg="";
								}
								$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_proc_chg.'</td>';
								$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_proc_chg_avg.'</td>';
							}else{
								$proc_chg_mon_cont = array_sum($proc_chg_mon_arr[$mon_val12]);
								$proc_chg_mon_avg="";
								if($proc_chg_mon>0){
									$proc_chg_mon_avg=number_format(($proc_chg_mon*100)/$proc_chg_mon_cont,2).'%';
								}else{
									$proc_chg_mon="";
								}
								$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$proc_chg_mon.'</td>';
								$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$proc_chg_mon_avg.'</td>';
							}
						}
					$data_avg .='</tr>'; 	
					$data .='</tr>'.$data_avg; 
					$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				}
				if($org_fac){ 
					$grand_month_org_chg=array();
					if(count($fac_org_chg_data_rec[$fac_grp_key_sec])>0){
						$data .='<tr>
									<td colspan="'.(count($monthArr)+1).'" style="text-align:left;background:#FFFFFF;width:1024px;" class="text_10b">
									Originating Facility Charges</td>
							</tr>';
						foreach($fac_id_data_arr as $fac_org_key => $fac_org_val){	
							$fac_org_id=$fac_org_key;
							if(count($fac_org_chg_data_rec[$fac_grp_key_sec][$fac_org_id])>0){
								$data .='<tr>
									<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">'.$fac_id_data_arr[$fac_org_id].'</td>';
								$rg_org_chg_arr=array();
								foreach($monthArr as $mon_key8 => $mon_val8){
									$tot_org_fac_mon=array_sum($fac_org_chg_data_rec[$fac_grp_key_sec][$fac_org_id][$mon_val8]);
									$rg_org_chg_arr[]=$tot_org_fac_mon;
									$gr_fac_chg_data_rec[$mon_val8][]=$tot_org_fac_mon;
									if($mon_val8=="Total"){
										$rg_org_chg=array_sum($rg_org_chg_arr);
										$grand_month_org_chg[$mon_val8][]=$rg_org_chg;
										$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_org_chg,2).'</td>';
									}else{
										$grand_month_org_chg[$mon_val8][]=$tot_org_fac_mon;
										$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_org_fac_mon,2).'</td>';
									}
								}
								$data .='</tr>';
							}
						}
						$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px" style="width:1024px;"></td></tr>';
						$data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total</td>';
						foreach($monthArr as $mon_key9 => $mon_val9){
							$grand_org_fac_chg=array_sum($grand_month_org_chg[$mon_val9]);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($grand_org_fac_chg,2).'</td>';
						}
						$data .='</tr>'; 
						$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px" style="width:1024px;"></td></tr>';
					}
					if(count($fac_org_pay_data_rec[$fac_grp_key_sec])>0){
						$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
						$data .='<tr>
									<td colspan="'.(count($monthArr)+1).'" style="text-align:left;background:#FFFFFF;width:1024px;" class="text_10b">
									Originating Facility Receipts</td>
							</tr>';
						$grand_month_org_pay=array();	
						foreach($fac_id_data_arr as $fac_org_key2 => $fac_org_val2){	
							$fac_org_id2=$fac_org_key2;
							if(count($fac_org_pay_data_rec[$fac_grp_key_sec][$fac_org_id2])>0){
								$data .='<tr>
									<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">'.$fac_id_data_arr[$fac_org_id2].'</td>';
								$rg_org_pay_arr=array();
								foreach($monthArr as $mon_key10 => $mon_val10){
									$tot_org_fac_pay_mon=array_sum($fac_org_pay_data_rec[$fac_grp_key_sec][$fac_org_id2][$mon_val10]);
									$rg_org_pay_arr[]=$tot_org_fac_pay_mon;
									$gr_fac_pay_data_rec[$mon_val10][]=$tot_org_fac_pay_mon;
									if($mon_val10=="Total"){
										$rg_org_pay=array_sum($rg_org_pay_arr);
										$grand_month_org_pay[$mon_val10][]=$rg_org_pay;
										$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_org_pay,2).'</td>';
									}else{
										$grand_month_org_pay[$mon_val10][]=$tot_org_fac_pay_mon;
										$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_org_fac_pay_mon,2).'</td>';
									}
								}
								$data .='</tr>'; 
							}
						}
						$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px" style="width:1024px;"></td></tr>';
						$data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total</td>';
						foreach($monthArr as $mon_key11 => $mon_val11){
							$grand_org_fac_pay=array_sum($grand_month_org_pay[$mon_val11]);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($grand_org_fac_pay,2).'</td>';
						}
						$data .='</tr>'; 
						$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px" style="width:1024px;"></td></tr>';
					}
					if(count($summaryTotalChrgArr[$fac_grp_key_sec])>0){
						$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
						$data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Summary Charges</td>';
						$rg_tot_sum_chg_arr=array();
						foreach($monthArr as $mon_key7 => $mon_val7){
							$tot_sum_chg_mon=array_sum($summaryTotalChrgArr[$fac_grp_key_sec][$mon_val7]);
							$rg_tot_sum_chg_arr[]=$tot_sum_chg_mon;
							$gr_tot_sum_chg_arr[$mon_val7][]=$tot_sum_chg_mon;
							if($mon_val7=="Total"){
								$rg_tot_sum_chg=array_sum($rg_tot_sum_chg_arr);
								$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($rg_tot_sum_chg,2).'</td>';
							}else{
								$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($tot_sum_chg_mon,2).'</td>';
							}
						}
						$data .='</tr>';
						$data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Summary Receipts</td>';
						$rg_tot_sum_pay_arr=array();
						foreach($monthArr as $mon_key7 => $mon_val7){
							$tot_sum_pay_mon=array_sum($summaryTotalPaidArr[$fac_grp_key_sec][$mon_val7]);
							$rg_tot_sum_pay_arr[]=$tot_sum_pay_mon;
							$gr_tot_sum_pay_arr[$mon_val7][]=$tot_sum_pay_mon;
							if($mon_val7=="Total"){
								$rg_tot_sum_pay=array_sum($rg_tot_sum_pay_arr);
								$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($rg_tot_sum_pay,2).'</td>';
							}else{
								$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($tot_sum_pay_mon,2).'</td>';
							}
						}
						$data .='</tr>';
					}
				}else{
					$data .='<tr>
					<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Charges</td>';
					$rg_tot_proc_chg_arr=array();	
					foreach($monthArr as $mon_key4 => $mon_val4){
						$tot_chg_mon=array_sum($fac_chg_data_rec[$fac_grp_key_sec][$mon_val4]);
						$rg_tot_proc_chg_arr[]=$tot_chg_mon;
						$gr_fac_chg_data_rec[$mon_val4][]=$tot_chg_mon;
						if($mon_val4=="Total"){
							$rg_tot_proc_chg=array_sum($rg_tot_proc_chg_arr);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($rg_tot_proc_chg,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($tot_chg_mon,2).'</td>';
						}
					}
					$data .='</tr>'; 
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Avg Charges</td>';
					$rg_tot_mon_cont_arr=array();
					$rg_tot_chg_arr=array();	
					foreach($monthArr as $mon_key5 => $mon_val5){
						$y_exp=explode('-',$mon_val5);
						$yy='20'.$y_exp[1];
						//$this_month_days=date('t',mktime(0,0,0,$mon_key5,1,$yy));
						$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
						$tot_avg_mon=(array_sum($fac_chg_data_rec[$fac_grp_key_sec][$mon_val5])/$this_month_days);
						$rg_tot_mon_cont_arr[]=$this_month_days;
						$rg_tot_chg_arr[]=array_sum($fac_chg_data_rec[$fac_grp_key_sec][$mon_val5]);
						if($mon_val5=="Total"){
							$rg_tot_chg=array_sum($rg_tot_chg_arr);
							$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
							$rg_tot_chg_avg_mon=($rg_tot_chg/$rg_tot_mon_cont);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_chg_avg_mon,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_avg_mon,2).'</td>';
						}
					}
					$data .='</tr>'; 
					$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Receipts</td>';
					$rg_tot_proc_pay_arr=array();
					foreach($monthArr as $mon_key7 => $mon_val7){
						$tot_pay_mon=array_sum($fac_pay_data_rec[$fac_grp_key_sec][$mon_val7]);
						$rg_tot_proc_pay_arr[]=$tot_pay_mon;
						$gr_fac_pay_data_rec[$mon_val7][]=$tot_pay_mon;
						if($mon_val7=="Total"){
							$rg_tot_proc_pay=array_sum($rg_tot_proc_pay_arr);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($rg_tot_proc_pay,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($tot_pay_mon,2).'</td>';
						}
					}
					$data .='</tr>'; 
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Avg Receipts</td>';
					$rg_tot_mon_cont_arr=array();
					$rg_tot_pay_arr=array();	
					foreach($monthArr as $mon_key6 => $mon_val6){
						$y_exp=explode('-',$mon_val6);
						$yy='20'.$y_exp[1];
						//$this_month_days=date('t',mktime(0,0,0,$mon_key6,1,$yy));
						$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
						$tot_pay_avg_mon=(array_sum($fac_pay_data_rec[$fac_grp_key_sec][$mon_val6])/$this_month_days);
						$rg_tot_mon_cont_arr[]=$this_month_days;
						$rg_tot_pay_arr[]=array_sum($fac_pay_data_rec[$fac_grp_key_sec][$mon_val6]);
						if($mon_val6=="Total"){
							$rg_tot_pay=array_sum($rg_tot_pay_arr);
							$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
							$rg_tot_pay_avg_mon=($rg_tot_pay/$rg_tot_mon_cont);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_pay_avg_mon,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_pay_avg_mon,2).'</td>';
						}
					}
					$data .='</tr>';
					$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Summary Charges</td>';
					$rg_tot_sum_chg_arr=array();
					foreach($monthArr as $mon_key7 => $mon_val7){
						$tot_sum_chg_mon=array_sum($summaryTotalChrgArr[$fac_grp_key_sec][$mon_val7]);
						$rg_tot_sum_chg_arr[]=$tot_sum_chg_mon;
						$gr_tot_sum_chg_arr[$mon_val7][]=$tot_sum_chg_mon;
						if($mon_val7=="Total"){
							$rg_tot_sum_chg=array_sum($rg_tot_sum_chg_arr);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($rg_tot_sum_chg,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($tot_sum_chg_mon,2).'</td>';
						}
					}
					$data .='</tr>';
					$data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">Summary Receipts</td>';
					$rg_tot_sum_pay_arr=array();
					foreach($monthArr as $mon_key7 => $mon_val7){
						$tot_sum_pay_mon=array_sum($summaryTotalPaidArr[$fac_grp_key_sec][$mon_val7]);
						$rg_tot_sum_pay_arr[]=$tot_sum_pay_mon;
						$gr_tot_sum_pay_arr[$mon_val7][]=$tot_sum_pay_mon;
						if($mon_val7=="Total"){
							$rg_tot_sum_pay=array_sum($rg_tot_sum_pay_arr);
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($rg_tot_sum_pay,2).'</td>';
						}else{
							$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10"> '.$CLSReports->numberFormat($tot_sum_pay_mon,2).'</td>';
						}
					}
					$data .='</tr>';
				}
				$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
				$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">    	
					<tr>
						<td class="text_10b" style="background:#FFFFFF;" colspan="8">A/R AGING</td>
					</tr>
					<tr>';
					foreach($aggingDrop as $aging_kay => $aging_val){
						$data .='<td class="text_10b" style="background:#FFFFFF; width:170px; text-align:center;">'.$aging_val.'</td>';
					}
						$data .='<td class="text_10b" style="background:#FFFFFF; width:170px;">Total</td></tr><tr>';	
					
					foreach($aggingDrop as $aging_kay => $aging_val){
						$arAgingData=$arAgingDataArr[$fac_grp_key_sec][$aging_kay];
						$data .='<td class="text_10" style="background:#FFFFFF; width:170px; text-align:right;">'.$arAgingData.'</td>';
					}
						$arAgingData_bal=$arAgingDataArr[$fac_grp_key_sec]['total_ar_bal'];
						$data .='<td class="text_10" style="background:#FFFFFF; width:170px; text-align:right;">'.$arAgingData_bal.'</td>
					</tr>
				</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">';
			}
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
			$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';
			$data .='<tr>
			<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Charges</td>';
			$rg_tot_proc_chg_arr=array();
			foreach($monthArr as $mon_key4 => $mon_val4){
				$tot_chg_mon=array_sum($gr_fac_chg_data_arr[$mon_val4]);
				$rg_tot_proc_chg_arr[]=$tot_chg_mon;
				if($mon_val4=="Total"){
					$rg_tot_proc_chg=array_sum($rg_tot_proc_chg_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_tot_proc_chg,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($tot_chg_mon,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Charges</td>';
			$rg_tot_mon_cont_arr=array();
			$grand_rg_tot_chg_arr=array();
			foreach($monthArr as $mon_key5 => $mon_val5){
				$y_exp=explode('-',$mon_val5);
				$yy='20'.$y_exp[1];
				//$this_month_days=date('t',mktime(0,0,0,$mon_key5,1,$yy));
				$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
				$tot_avg_mon=(array_sum($gr_fac_chg_data_arr[$mon_val5])/$this_month_days);
				$grand_rg_tot_chg_arr[]=array_sum($gr_fac_chg_data_arr[$mon_val5]);
				$rg_tot_mon_cont_arr[]=$this_month_days;
				if($mon_val5=="Total"){
					$grand_rg_tot_chg=array_sum($grand_rg_tot_chg_arr);
					$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
					$gr_rg_tot_chg_avg_mon=($grand_rg_tot_chg/$rg_tot_mon_cont);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_rg_tot_chg_avg_mon,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_avg_mon,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Receipts</td>';
			$rg_tot_proc_pay_arr=array();
			foreach($monthArr as $mon_key7 => $mon_val7){
				$tot_pay_mon=array_sum($gr_fac_pay_data_arr[$mon_val7]);
				$rg_tot_proc_pay_arr[]=$tot_pay_mon;
				if($mon_val7=="Total"){
					$rg_tot_proc_pay=array_sum($rg_tot_proc_pay_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($rg_tot_proc_pay,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_pay_mon,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Receipts</td>';
			$rg_tot_mon_cont_arr=array();
			$grand_rg_tot_pay_arr=array();
			foreach($monthArr as $mon_key6 => $mon_val6){
				$y_exp=explode('-',$mon_val6);
				$yy='20'.$y_exp[1];
				//$this_month_days=date('t',mktime(0,0,0,$mon_key6,1,$yy));
				$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
				$tot_pay_avg_mon=(array_sum($gr_fac_pay_data_arr[$mon_val6])/$this_month_days);
				$grand_rg_tot_pay_arr[]=array_sum($gr_fac_pay_data_arr[$mon_val6]);
				$rg_tot_mon_cont_arr[]=$this_month_days;
				if($mon_val6=="Total"){
					$grand_rg_tot_pay=array_sum($grand_rg_tot_pay_arr);
					$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
					$gr_rg_tot_pay_avg_mon=($grand_rg_tot_pay/$rg_tot_mon_cont);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_rg_tot_pay_avg_mon,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_pay_avg_mon,2).'</td>';
				}
			}
			$data .='</tr>';
			
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Summary Charges</td>';
			$rg_gr_tot_sum_chg_arr=array();
			foreach($monthArr as $mon_key7 => $mon_val7){
				$gr_tot_sum_chg=array_sum($grSummaryChgArr[$mon_val7]);
				$rg_gr_tot_sum_chg_arr[]=$gr_tot_sum_chg;
				if($mon_val7=="Total"){
					$rg_gr_tot_sum_chg=array_sum($rg_gr_tot_sum_chg_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_gr_tot_sum_chg,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($gr_tot_sum_chg,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr>
				<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Summary Receipts</td>';
			$rg_gr_tot_sum_pay_arr=array();
			foreach($monthArr as $mon_key7 => $mon_val7){
				$gr_tot_sum_pay=array_sum($grSummaryPayArr[$mon_val7]);
				$rg_gr_tot_sum_pay_arr[]=$gr_tot_sum_pay;
				if($mon_val7=="Total"){
					$rg_gr_tot_sum_pay=array_sum($rg_gr_tot_sum_pay_arr);
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($rg_gr_tot_sum_pay,2).'</td>';
				}else{
					$data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b"> '.$CLSReports->numberFormat($gr_tot_sum_pay,2).'</td>';
				}
			}
			$data .='</tr>'; 
			$data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
			$data .='</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">    	
				<tr>
					<td class="text_10b" style="background:#FFFFFF;" colspan="8">Total A/R AGING</td>
				</tr>
				<tr>';
				$arAgingData="";
				$arAgingData_bal="";
				$arBalTotalStr="";
				$arBalTotalMain="";
				foreach($aggingDrop as $aging_kay => $aging_val){
					$data .='<td class="text_10b" style="background:#FFFFFF; width:125px; text-align:center;">'.$aging_val.'</td>';
				}
					$data .='<td class="text_10b" style="background:#FFFFFF; width:115px;">Total</td></tr><tr>';	
				$arBalTotalMain=array_sum($arAgingDataArr_wofac['total_ar_bal']);
				foreach($aggingDrop as $aging_kay => $aging_val){
					$arBalTotalStr=array_sum($arAgingDataArr_wofac[$aging_kay]);
					$arAgingData=$CLSReports->numberFormat($arBalTotalStr,2).' ('.number_format(($arBalTotalStr * 100) / $arBalTotalMain,2).'%)';
					$data .='<td class="text_10" style="background:#FFFFFF; width:125px; text-align:right;">'.$arAgingData.'</td>';
				}
					$arAgingData_bal=$CLSReports->numberFormat($arBalTotalMain,2).' ('.number_format(($arBalTotalMain * 100) / $arBalTotalMain,2).'%)';
					$data .='<td class="text_10" style="background:#FFFFFF; width:115px; text-align:right;">'.$arAgingData_bal.'</td>
				</tr>
			</table><table class="rpt_table rpt rpt_table-bordered rpt_padding">';
			$data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';
		 }
	}
		 
	
	$HTMLCreated=0;
	//print_r($monthArr);
	if($data!=""){
		$printFile = true;
		$HTMLCreated=1;
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$createdBy = ucfirst(trim($op_name_arr[1][0]));
		$createdBy .= ucfirst(trim($op_name_arr[0][0]));

		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';

		$header_part='
			<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">		
				<tr>
					<td class="rptbx1" style="width:33%">Facility Monthly Report ('.$processReport.')</td>
					<td class="rptbx2" style="text-align:center;width:33%">Report Period : '.$Start_date.' to '.$End_date.'</td>
					<td class="rptbx3" style="text-align:center;width:34%;">
						Created By '.$createdBy.' on '.date(phpDateFormat().' h:i A').'
					</td>
				</tr>
			</table>';
		
		if($data!=""){	
			
			//PAGE DATA
			$page_data=
				$styleHTML.
				$header_part.'
				<table class="rpt_table rpt rpt_table-bordered rpt_padding">
					'.$data.'
				</table>';
		
		
			//PDF
			$pdfData =
			$stylePDF.'
			<page backtop="5mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>		
			<page_header>
				'.$header_part.'
			</page_header>
				<table class="rpt_table rpt rpt_table-bordered rpt_padding">
					'.$data.'
				</table>
			</page>';
		
		
			//--- CREATE HTML FILE FOR PDF PRINTING ----
			$file_location = write_html($pdfData);
		}
	}
}

if($callFrom!='scheduled'){
	if($page_data){
		if($output_option=='view' || $output_option=='output_csv'){
			echo $page_data;
		}
	}else{
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>