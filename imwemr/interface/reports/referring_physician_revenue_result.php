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
FILE : ref_phy_monthly_result.php
PURPOSE : DISPLAY PROVIDER PRODUCTIVITY REPORT RESULT
ACCESS TYPE : DIRECT
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

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_id)<=0 && isPosFacGroupEnabled()){
		//FIRST GET PRIVILEGED POS FACILITIES
		$priv_pos_facility = $CLSReports->getFacilityName('', '0', 'array');
		$facility_id= $CLSReports->getSchFacilityInfo('', $priv_pos_facility);

		if(sizeof($facility_id)<=0){
			$facility_id[0]='000001';
		}
	}	

	$Ref_phy_grp_ids=implode(',', $selectedRef);
	$group_id= implode(',', $grp_id);
	$physician_ids=implode(',', $filing_provider);
	$str_crediting_physician= (sizeof($crediting_provider)>0)? implode(',', $crediting_provider): '';
	$facility_id=implode(',', $facility_id);

	if(($srh_type=="Receipts" || $srh_type=="Charges") && $processReport == 'Detail'){
		include_once "referring_physician_revenue_receipts.php";
	}else{
		$printFile = false;
				
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
		
		if($Ref_phy_grp_ids){
			$whr_grp=" and ref_group_id in($Ref_phy_grp_ids)";
		}
		$refGroupQry = "select ref_id,ref_group_id,ref_group_name from ref_group_tbl 
			where ref_group_status = '0' $whr_grp order by ref_group_name";
		$refGroupQryRs = imw_query($refGroupQry);
		$selectedRefArr = array();
		while($refGroupQryRes = imw_fetch_assoc($refGroupQryRs)){	
			$ref_id_new="";
			$ref_id_new=$refGroupQryRes['ref_id'];
			if(substr($ref_id_new,0,1)==','){
				$ref_id_new=substr($ref_id_new,1);
			}
			if(substr($ref_id_new,-1,1)==','){
				$ref_id_new=substr($ref_id_new,0,-1);
			}
			//echo $ref_id_new;
			$selectedRefArr[] = $ref_id_new;
			$selectedRefGroupArr[$refGroupQryRes['ref_group_id']]= $refGroupQryRes['ref_group_name'];
			$selectedRefIDGroupArr[$refGroupQryRes['ref_group_id']]=$ref_id_new;
		}


		$Ref_phy_ids=str_replace(',,',',',join(',',$selectedRefArr));
		if(in_array('-1',$selectedRef) || $Ref_phy_grp_ids==''){
			$ref_phy_whr="";
		}else{
			$ref_phy_whr=" where physician_Reffer_id in($Ref_phy_ids)";
		}
		$proQry1 = "select physician_Reffer_id,LastName,FirstName,MiddleName from refferphysician $ref_phy_whr order by LastName,FirstName";
		$proQryRs1 = imw_query($proQry1);
		$ref_phy_name_arr = array();
		while($proQryRes1 = imw_fetch_assoc($proQryRs1)){	
			$physician_Reffer_id = $proQryRes1['physician_Reffer_id'];
			$ref_phy_name_arr[$physician_Reffer_id]= core_name_format($proQryRes1['LastName'], $proQryRes1['FirstName'], $proQryRes1['MiddleName']);
			
			$ref_phy_flname_arr[$proQryRes1['FirstName']][$proQryRes1['LastName']]= $physician_Reffer_id;
			$ref_phy_ids_all_arr[]= $physician_Reffer_id;
		}

		if(in_array('-1',$selectedRef)){
			$selectedRefArr[] = join(',',$ref_phy_ids_all_arr);
			$selectedRefGroupArr[-1]= "All Referring Physician";
			$selectedRefIDGroupArr[-1]=join(',',$ref_phy_ids_all_arr);
			$ref_phy_whr="";
			$Ref_phy_ids=str_replace(',,',',',join(',',$selectedRefArr));
		}

		$ref_id_grp_val_ids=array();
		foreach($selectedRefIDGroupArr as $ref_id_grp_key => $ref_id_grp_val){
			$ref_id_grp_val_ids=explode(',',$ref_id_grp_val);

			foreach($ref_id_grp_val_ids as $ref_id_grp_keys => $ref_id_grp_vals){
				if($ref_id_grp_vals>0){
					$final_sel_grp_arr[$ref_id_grp_vals][]=$ref_id_grp_key;
					$final_sel_grp_ref_arr[$ref_id_grp_key][]=$ref_id_grp_vals;
				}
			}
		}


		
		if($group_id){
			$grp_whr=" and main.gro_id in($group_id)";
		}
		$phy_whr='';
		if($physician_ids){
			$phy_whr.=" and main.primary_provider_id_for_reports in($physician_ids)";
		}
		if(empty($str_crediting_physician)==false){
			$phy_whr.=" and main.sec_prov_id in($str_crediting_physician)";
		}	
		if($chksamebillingcredittingproviders==1){
			$phy_whr.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
		}			
		if($facility_id){
			$fac_qry = "select fac_prac_code from facility where id  in($facility_id)";
			$fac_qry_rs = imw_query($fac_qry);
			$pos_fac_id_arr=array();
			while($fac_qry_res = imw_fetch_assoc($fac_qry_rs)){
				if($fac_qry_res['fac_prac_code']){
					$pos_fac_id_arr[]=$fac_qry_res['fac_prac_code'];
				}
			}
			$pos_fac_id_str=join(',',$pos_fac_id_arr);
			if($pos_fac_id_str!=""){
				$fac_whr=" and main.facility_id in($pos_fac_id_str)";
			}
		}
		$ref_phy_ids_arr= explode(',',$Ref_phy_ids);
		
		$sch_qry_pat = "select id,primary_care_id from 
						patient_data 
					where primary_care_id in($Ref_phy_ids)";
		
		$apt_qry_rs = imw_query($sch_qry_pat);
		//print_r($sch_qry_res);
		while($apt_qry_res = imw_fetch_assoc($apt_qry_rs)){
			$pat_id_arr[$apt_qry_res['id']]=$apt_qry_res['id'];
			foreach($final_sel_grp_arr[$apt_qry_res['primary_care_id']] as $final_sel_grp_key5 => $final_sel_grp_val5){
				$ref_phy_pat_id[$apt_qry_res['id']][]=$final_sel_grp_val5;
				$ref_phy_id_pat_id[$apt_qry_res['id']]=$apt_qry_res['primary_care_id'];
			}
		}

		$usrqry = "select id,fname,mname,lname from users where user_type='1'";
		$usrrs = imw_query($usrqry);
		$usr_phy_mname_arr = array();
		while($usrres = imw_fetch_assoc($usrrs)){
			$usr_id = $usrres[$b]['id'];
			$usr_phy_fname_arr[$usr_id]= $usrres['fname'];
			$usr_phy_lname_arr[$usr_id]= $usrres['lname'];
			$usr_phy_mname_arr[$usr_id]= $usrres['mname'];
		}
		$sch_qry_pat1 = "select id,providerID from 
						patient_data 
					where primary_care_id ='' and providerID!=''";
		
		$apt_qry_rs1 = imw_query($sch_qry_pat1);
		while($apt_qry_res1 = imw_fetch_assoc($apt_qry_rs1)){
			$pat_id_arr[$apt_qry_res1['id']]=$apt_qry_res1['id'];
			$pat_data_tbl_phy_id[$apt_qry_res1['id']]=$apt_qry_res1['providerID'];
			$usr_fname=$usr_phy_fname_arr[$apt_qry_res1['providerID']];
			$usr_lname=$usr_phy_lname_arr[$apt_qry_res1['providerID']];
			$primary_care_id_fet="";
			if($ref_phy_flname_arr[$usr_fname][$usr_lname]!=""){
				$primary_care_id_fet=$ref_phy_flname_arr[$usr_fname][$usr_lname];
			}
			foreach($final_sel_grp_arr[$primary_care_id_fet] as $final_sel_grp_key5 => $final_sel_grp_val5){
				$ref_phy_pat_id[$apt_qry_res1['id']][]=$final_sel_grp_val5;
				$ref_phy_id_pat_id[$apt_qry_res1['id']]=$primary_care_id_fet;
			}
		}
		
		if(empty($selectedProc) === false){
					
			$cpt_group_id_str = join(',',$selectedProc);
			
			//--- GET CPT GROUP DATA ---
			$cptGroupQry = "select cpt_group_name, cpt_code_name, cpt_group_id from cpt_group_tbl
						where cpt_group_status = '0' order by cpt_group_name";
			
			$cptGroupQryRs = imw_query($cptGroupQry);
			$selectedProcArr = array();
			$select_proc_id_arr = array();
			$group_cpt_arr = array();
			while($cptGroupQryRes = imw_fetch_assoc($cptGroupQryRs)){
				$cpt_group_id = $cptGroupQryRes['cpt_group_id'];
				$cpt_group_name = ucwords($cptGroupQryRes['cpt_group_name']);
				$cptCodeNameArr = preg_split('/, /',$cptGroupQryRes['cpt_code_name']);
				$cpt_group_id_arr[$cpt_group_name] = $cptGroupQryRes['cpt_group_id'];
				
				if(in_array($cpt_group_id,$selectedProc) === true or empty($selectedProc) === true){
					$group_cpt_arr = array_merge($group_cpt_arr, $cptCodeNameArr);
					$selectedProcArr[$cpt_group_name] = $cptCodeNameArr;
					$select_proc_id_arr[] = $cptGroupQryRes['cpt_code_name'];
				}
			}
		}
		//---- GET ALL CPT CODE ----
		$procIdArr = array();
		$procQry = "select cpt_fee_id, cpt4_code,cpt_prac_code from cpt_fee_tbl where delete_status = '0'";		
		$procQryRs = imw_query($procQry);
		$procIdArr = array();
		$procCptCodeArr = array();
		
		while($procQryRes = imw_fetch_assoc($procQryRs)){
			$cpt_fee_id = $procQryRes['cpt_fee_id'];
			$procCptCodeArr[$cpt_fee_id] = $procQryRes['cpt4_code'];
			$procCptCodePractArr[$cpt_fee_id] = $procQryRes['cpt_prac_code'];
			if(count($group_cpt_arr) > 0){
				if(in_array($procQryRes['cpt4_code'], $group_cpt_arr) === true){
					$procIdArr[] = $cpt_fee_id;
				}
			}
			else{
				//$procIdArr[] = $cpt_fee_id;
			}
		}
		$dParts = explode('-',$start_date);
		$start_date_chg = date('Y-m-d', mktime(0,0,0,$dParts[1],$dParts[2],$dParts[0]));
			$procIdStr = join(',', array_unique($procIdArr));
		if($procIdStr!=""){
			$proc_whr=" and main.proc_code_id in($procIdStr)";
		}
		$chk_ref_arr=array();
		$chk_ref_arr=explode(',',$Ref_phy_ids);

 		$sch_qry = "select main.encounter_id, (main.charges*main.units) as 'totalAmount',
		main.facility_id, main.date_of_service,	main.patient_id, main.reff_phy_id,
		main.gro_id, main.primary_provider_id_for_reports as 'primaryProviderId',
		main.proc_code_id, main.charge_list_detail_id 
		FROM  
		report_enc_detail main
		WHERE main.del_status = '0' 
		$grp_whr $phy_whr 
		AND (main.date_of_service BETWEEN '$start_date_chg' AND '$end_date') order by main.date_of_service";
		$sch_qry_res = imw_query($sch_qry);
		$enc_copay_arr[]=0;

		while($sch_qry_row = imw_fetch_assoc($sch_qry_res)){
				$reff_phy_id=$ref_phy_id_pat_id[$sch_qry_row['patient_id']];
				if(in_array($reff_phy_id,$chk_ref_arr)){
					//Summary charges Start
					$sum_enc_arr[$sch_qry_row['encounter_id']]=$sch_qry_row['encounter_id'];
					$sum_ref_phy_arr[$sch_qry_row['encounter_id']]=$reff_phy_id;
					if($sch_qry_row['date_of_service']>=$start_date && $sch_qry_row['date_of_service']<=$end_date){
						$date_of_service_arr=explode('-',$sch_qry_row['date_of_service']);
						$dos_month_mk=$monthNameArr[$date_of_service_arr[1]].'-'.substr($date_of_service_arr[0],2);
						$ref_phy_id_chk2=$sum_ref_phy_arr[$sch_qry_row['encounter_id']];
						foreach($final_sel_grp_arr[$ref_phy_id_chk2] as $final_sel_grp_key2 => $final_sel_grp_val2){
							$sum_ref_phy_data_chg[$final_sel_grp_val2][$dos_month_mk][]=$sch_qry_row['totalAmount'];
							$sum_ref_phy_data_det_chg[$final_sel_grp_val2][$reff_phy_id][$dos_month_mk][]=$sch_qry_row['totalAmount'];
							$sum_hide_ref_det_data_rec[$final_sel_grp_val2][$reff_phy_id][]=$sch_qry_row['totalAmount'];
							$sum_grand_month_tot_chg_arr[$dos_month_mk][]=$sch_qry_row['totalAmount'];
						}
					}
					//Summary charges End
					
					if(in_array($sch_qry_row['facility_id'],$pos_fac_id_arr) || count($pos_fac_id_arr)<1){
						if(in_array($sch_qry_row['primaryProviderId'],$physician_id) || count($physician_id)<1){
							if(in_array($sch_qry_row['proc_code_id'],$procIdArr) || count($procIdArr)<1){
								if($procIdStr!=""){
									foreach($selectedProcArr as $grp_name_key => $grp_name_val){
										if(in_array($procCptCodeArr[$sch_qry_row['proc_code_id']], $grp_name_val) === true){
											$enc_arr[$sch_qry_row['encounter_id']]=$sch_qry_row['encounter_id'];
											$ref_phy_arr[$sch_qry_row['encounter_id']]=$reff_phy_id;
											$chld_arr[$sch_qry_row['charge_list_detail_id']]=$sch_qry_row['charge_list_detail_id'];
											if($sch_qry_row['coPayAdjustedAmount']>0){
												$enc_copay_arr[$sch_qry_row['encounter_id']]=$sch_qry_row['encounter_id'];
											}
										}
									}
								}else{
									$enc_arr[$sch_qry_row['encounter_id']]=$sch_qry_row['encounter_id'];
									$ref_phy_arr[$sch_qry_row['encounter_id']]=$reff_phy_id;
									$chld_arr[$sch_qry_row['charge_list_detail_id']]=$sch_qry_row['charge_list_detail_id'];
									if($sch_qry_row['coPayAdjustedAmount']>0){
										$enc_copay_arr[$sch_qry_row['encounter_id']]=$sch_qry_row['encounter_id'];
									}
								}
					
								if($sch_qry_row['date_of_service']>=$start_date && $sch_qry_row['date_of_service']<=$end_date){
									$date_of_service_arr=explode('-',$sch_qry_row['date_of_service']);
									$dos_month_mk=$monthNameArr[$date_of_service_arr[1]].'-'.substr($date_of_service_arr[0],2);
									$monthArr[$dos_month_mk]=$dos_month_mk;
									$ref_phy_id_chk2=$ref_phy_arr[$sch_qry_row['encounter_id']];
									foreach($final_sel_grp_arr[$ref_phy_id_chk2] as $final_sel_grp_key2 => $final_sel_grp_val2){
										if($procIdStr!=""){
											foreach($selectedProcArr as $grp_name_key => $grp_name_val){
												if(in_array($procCptCodeArr[$sch_qry_row['proc_code_id']], $grp_name_val) === true){	
													$ref_chg_data_proc_rec[$final_sel_grp_val2][$grp_name_key][$dos_month_mk][$sch_qry_row['encounter_id']]=$sch_qry_row['totalAmount'];
													$ref_chg_data_proc_det_rec[$final_sel_grp_val2][$reff_phy_id][$grp_name_key][$dos_month_mk][$sch_qry_row['encounter_id']]=$sch_qry_row['totalAmount'];
													$ref_chg_data_proc_tot_rec[$grp_name_key][$dos_month_mk][]=$sch_qry_row['totalAmount'];
													$ref_chg_data_proc_sum_det_rec[$final_sel_grp_val2][$grp_name_key][$sch_qry_row['proc_code_id']][$dos_month_mk][$sch_qry_row['encounter_id']]=$sch_qry_row['totalAmount'];
													$ref_phy_data_chg_summary[$final_sel_grp_val2][$dos_month_mk][]=$sch_qry_row['totalAmount'];
													$ref_phy_data_chg[$final_sel_grp_val2][$reff_phy_id][$dos_month_mk][]=$sch_qry_row['totalAmount'];
													$grand_month_tot_chg_arr[$dos_month_mk][]=$sch_qry_row['totalAmount'];
													$hide_ref_chg_det_data_rec[$final_sel_grp_val2][$reff_phy_id][]=$sch_qry_row['totalAmount'];
												}
											}
										}else{
											$ref_phy_data_chg_summary[$final_sel_grp_val2][$dos_month_mk][]=$sch_qry_row['totalAmount'];
											$ref_phy_data_chg[$final_sel_grp_val2][$reff_phy_id][$dos_month_mk][]=$sch_qry_row['totalAmount'];
											$hide_ref_chg_det_data_rec[$final_sel_grp_val2][$reff_phy_id][]=$sch_qry_row['totalAmount'];
											$grand_month_tot_chg_arr[$dos_month_mk][]=$sch_qry_row['totalAmount'];
										}
										
									}
								}
							}
						}
					}
				}
			}

		//$ref_phy_name_arr[$sch_qry_res[$s]['reff_phy_id']]=$RefprividerNameArr[$sch_qry_res[$s]['reff_phy_id']];
		//print_r($ref_chg_data_proc_rec);
		$enc_str=implode(',',$enc_arr);
		$enc_copay_str=implode(',',$enc_copay_arr);
		$chld_str=implode(',',$chld_arr);
		$sum_enc_str=implode(',',$sum_enc_arr);
		//echo count($sum_enc_arr).'--';
		//echo count($enc_arr);
		if($srh_type=="" || $srh_type=="Receipts"){
				$sch_qry1 = "Select trans.encounter_id, trans.patient_id, trans.trans_dot, trans.trans_dop, trans.trans_type, trans.trans_del_operator_id,
				trans.trans_amount, trans.charge_list_detail_id, trans.patient_id 
				FROM report_enc_trans trans
				WHERE LOWER(trans.trans_type) IN('paid','negative payment','copay-negative payment','copay-paid','deposit','interest payment')";
				if($DateRangeFor=='date_of_payment'){ 
					$sch_qry1.=" AND (trans.trans_dop BETWEEN '$start_date' and '$end_date')
					ORDER BY trans.trans_dot";
				}else{
					$sch_qry1.=" AND (trans.trans_dot BETWEEN '$start_date' and '$end_date')
					ORDER BY trans.trans_dot";
				}
				$sch_qry_rs1 = imw_query($sch_qry1);

				while($sch_qry_row1 = imw_fetch_assoc($sch_qry_rs1)){
					$reff_phy_id=$ref_phy_id_pat_id[$sch_qry_row1['patient_id']];
					
					if(in_array($reff_phy_id,$chk_ref_arr)){
	
						$encounter_id=$sch_qry_row1['encounter_id'];
						$tempEncId[$encounter_id]=$encounter_id;
						$trans_type= strtolower($sch_qry_row1['trans_type']);
						if($DateRangeFor=='date_of_payment'){
							$date_of_payment_arr=explode('-',$sch_qry_row1['trans_dop']);
						}else{
							$date_of_payment_arr=explode('-',$sch_qry_row1['trans_dot']);
						}
						$month_mk=$monthNameArr[$date_of_payment_arr[1]].'-'.substr($date_of_payment_arr[0],2);
						$monthArr[$month_mk]=$month_mk;
						$ref_phy_id_chk = $ref_phy_id_chk_sum  = $reff_phy_id;

						$paid_amount=$sch_qry_row1['trans_amount'];
						if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $sch_qry_row1['trans_del_operator_id']>0)$paid_amount="-".$sch_qry_row1['trans_amount'];
						if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $sch_qry_row1['trans_del_operator_id']>0)$paid_amount=$sch_qry_row1['trans_amount'];

						//TEMP FOR SUMMARY
						foreach($final_sel_grp_arr[$ref_phy_id_chk] as $final_sel_grp_key1 => $final_sel_grp_val1){
							$tempArrPayments[$encounter_id][$final_sel_grp_val1][$ref_phy_id_chk][$month_mk]+=$paid_amount;
						}
						
						if(in_array($sch_qry_row1['charge_list_detail_id'],$chld_arr) || 
						(in_array($sch_qry_row1['encounter_id'],$enc_arr) 
						 && $sch_qry_row1['charge_list_detail_id']==0)
						){
							foreach($final_sel_grp_arr[$ref_phy_id_chk] as $final_sel_grp_key1 => $final_sel_grp_val1){
								$tempArrForTotPayments[$encounter_id][$final_sel_grp_val1][$ref_phy_id_chk][$month_mk]+=$paid_amount;
							}
						}
					}
				}

				//FINAL PAYMENT ARRAY
				if(sizeof($tempEncId)>0){
					$tempEncId_str=implode(',', $tempEncId);

					$qry = "Select main.encounter_id FROM report_enc_detail main WHERE main.encounter_id IN(".$tempEncId_str.") 
					$grp_whr $phy_whr GROUP BY main.encounter_id";
					$rs = imw_query($qry);
					while($res=imw_fetch_assoc($rs)){
						$enc_id=$res['encounter_id'];
						foreach($tempArrPayments[$enc_id] as $final_sel_grp_val1 => $refArr){
							foreach($refArr as $ref_phy_id_chk => $month_arr){
								foreach($month_arr as $month_mk => $paid_amount){
									if($processReport == 'Detail'){
										$sum_ref_phy_data_det_rec[$final_sel_grp_val1][$ref_phy_id_chk][$month_mk][]=$paid_amount;
										$sum_hide_ref_pay_det_data_rec[$final_sel_grp_val1][$ref_phy_id_chk][]=$paid_amount;
										
										if($tempArrForTotPayments[$enc_id][$final_sel_grp_val1][$ref_phy_id_chk][$month_mk]){
											$paid_amt = $tempArrForTotPayments[$enc_id][$final_sel_grp_val1][$ref_phy_id_chk][$month_mk];
											$ref_phy_data_rec[$final_sel_grp_val1][$ref_phy_id_chk][$month_mk][]=$paid_amt;
											$hide_ref_pay_det_data_rec[$final_sel_grp_val1][$ref_phy_id_chk][]=$paid_amt;
										}
									}else{
										//echo $enc_id.'-'.$paid_amount.'-'.$month_mk.'<br>';
										$sum_ref_phy_data_rec[$final_sel_grp_val1][$month_mk][]=$paid_amount;
										$sum_hide_ref_pay_det_data_rec[$final_sel_grp_val1][$ref_phy_id_chk][]=$paid_amount;
										$sum_grand_month_tot_pay_arr[$month_mk][]=$paid_amount;
										
										if($tempArrForTotPayments[$enc_id][$final_sel_grp_val1][$ref_phy_id_chk][$month_mk]){
											$paid_amt = $tempArrForTotPayments[$enc_id][$final_sel_grp_val1][$ref_phy_id_chk][$month_mk];
											$ref_phy_data_rec_summary[$final_sel_grp_val1][$month_mk][]=$paid_amt;
											$hide_ref_pay_det_data_rec[$final_sel_grp_val1][$ref_phy_id_chk][]=$paid_amt;
											$grand_month_tot_pay_arr[$month_mk][]=$paid_amt;
										}
									}
								}
							}
						}
					}
					
				}
			}


			$pat_id_str=implode(',',$pat_id_arr);
			$sch_app_qry = "select appt.sa_facility_id,appt.sa_app_start_date,appt.id,appt.sa_patient_id,
						date_format(appt.sa_app_start_date, '".$dateFormat."') as sa_app_start_date_show, 
						time_format(appt.sa_app_starttime,'%h:%i %p') as sa_app_starttime_show, 
						time_format(appt.sa_app_endtime,'%h:%i %p') as sa_app_endtime_show 
						FROM schedule_appointments appt 
						where appt.sa_app_start_date BETWEEN '$start_date' and '$end_date'
						and appt.sa_patient_id in ($pat_id_str)
						and appt.sa_patient_app_status_id not in(3,18,201,203)";
			if($facility_id){
				$sch_app_qry .= " and appt.sa_facility_id IN ($facility_id)";
			}
			if($physician_ids){
				$sch_app_qry .= " and appt.sa_doctor_id IN ($physician_ids)";
			}
			$sch_app_qry .= " order by appt.sa_app_start_date,appt.sa_app_starttime,appt.sa_app_endtime";
			$sch_app_qry_res = imw_query($sch_app_qry);
			$pat_fac_id_arr=array();
			while($sch_app_row = imw_fetch_array($sch_app_qry_res)){
				$sa_app_start_date=$sch_app_row['sa_app_start_date'];
				$sa_app_start_date_arr=explode('-',$sa_app_start_date);
				$sa_app_month_mk=$monthNameArr[$sa_app_start_date_arr[1]].'-'.substr($sa_app_start_date_arr[0],2);
				$ref_grp_id=$ref_phy_pat_id[$sch_app_row['sa_patient_id']];
				foreach($ref_phy_pat_id[$sch_app_row['sa_patient_id']] as $final_sel_grp_key6 => $final_sel_grp_val6){
					$ref_app_data_rec[$final_sel_grp_val6][$sa_app_month_mk][]=$sch_app_row['id'];
					$ref_phy_app_id=$ref_phy_id_pat_id[$sch_app_row['sa_patient_id']];
					$ref_app_det_data_rec[$final_sel_grp_val6][$ref_phy_app_id][$sa_app_month_mk][]=$sch_app_row['id'];
					$hide_ref_app_det_data_rec[$final_sel_grp_val6][$ref_phy_app_id][]=$sch_app_row['id'];
					$monthArr[$sa_app_month_mk]=$sa_app_month_mk;
				}
			}
			
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
			
			$aging_start=0;
			$aging_to=180;
			$qry="Select patChg.patient_id, (pri_due + sec_due + tri_due + pat_due) as 'totalBalance', patChgDet.totalAmount, patChgDet.paidForProc,
			DATEDIFF(NOW(),date_of_service) as last_pri_dop_diff,
			DATEDIFF(NOW(),from_sec_due_date) as last_sec_dop_diff,
			DATEDIFF(NOW(),from_ter_due_date) as last_ter_dop_diff,
			DATEDIFF(NOW(),from_pat_due_date) as last_pat_dop_diff,
			DATEDIFF(NOW(),date_of_service) as last_dos_diff
			FROM patient_charge_list patChg JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
			WHERE patChgDet.del_status='0'";
			$rs= imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$arrTempAging = array();
				$patient_id = $res['patient_id'];
	
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
						foreach($ref_phy_pat_id[$patient_id] as $key => $refPhyGrpId){
							$arBalTotalMainArr[$refPhyGrpId][$b]+=$res['totalBalance'];
							$arrAgingTotalMainArr[$refPhyGrpId][$b]['balance']+=$res['totalBalance'];
							$arrAgingTotalMainArr[$refPhyGrpId][$b]['charges']+=$res['totalAmount'];
						}
					}
					$a += $aggingCycle;
				}
				
				if($smallestAging>=181){
					foreach($ref_phy_pat_id[$patient_id] as $key => $refPhyGrpId){
						$arBalTotalMainArr[$refPhyGrpId][181]+=$res['totalBalance'];
						$arrAgingTotalMainArr[$refPhyGrpId][181]['balance']+=$res['totalBalance'];
						$arrAgingTotalMainArr[$refPhyGrpId][181]['charges']+=$res['totalAmount'];
					}
				}
			}

			$arBalTotalMain=0;
			foreach($arrAgingTotalMainArr as $refPhyGrpId => $agingData){
				$arBalTotalArr = array();
				
				foreach($agingData as $agingStart => $agingDet){
					$totalBalanceAging = $agingDet['balance'];

					$arBalTotalMainArr[$refPhyGrpId][] =$totalBalanceAging;
					$arAgingDataArr[$refPhyGrpId][$agingStart][] = $totalBalanceAging;
					$arAgingDataArr_wofac[$agingStart][] = $totalBalanceAging;
					$arBalTotalArr[] =$totalBalanceAging;
				}

				$arBalTotalStr = array_sum($arBalTotalArr);
				$arAgingDataArr[$refPhyGrpId]['total_ar_bal'][] = $arBalTotalStr;
				$arAgingDataArr_wofac['total_ar_bal'][] = $arBalTotalStr;
			}
			//	END AGING CYCLE
	
		
			//echo "<pre>";
			//print_r($ref_app_det_data_rec);
			if(count($monthArr)>0){
				//--- MONTH HEADER DATA ---
				list($stY, $stM, $stD) = preg_split("/-/",$start_date);		
				list($enY, $enM, $enD) = preg_split("/-/",$end_date);
				
				//--- GET TOTAL MONTH COUNT ---
				$endDate = $enY.$enM;
				$startDate = $stY.$stM;
				$monthQry = "select period_diff($endDate, $startDate) as months";
				$monthQryRs = imw_query($monthQry);
				$monthQryRes = imw_fetch_assoc($monthQryRs);
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
					$ref_phy_data="";
					$final_grand_month_tot_pay=array();
					$grand_month_tot_pay=array();
					$month_based_width=(1024/(count($monthArr)+1));
					$ref_col=count($monthArr)+1;
					$ref_phy_data .='<tr><td class="text_b_w" style="width:'.$month_based_width.'px;"></td>';
						 foreach($monthArr as $mon_name_key => $mon_name_val){
						   $ref_phy_data .='<td class="text_b_w" style="text-align:right;width:'.$month_based_width.'px;">'.ucfirst($mon_name_val).'</td>';
						}
						 $ref_phy_data .='</tr>'; 
						 
						foreach($selectedRefGroupArr as $ref_grp_key2 => $ref_grp_val2){
							$total_month_tot_app=array();
							$total_rg_tot_mon_app_arr=array();
							$total_month_tot_app_arr=array();
							$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="2px"></td></tr>';
							$ref_phy_data .='<tr><td class="text_b_w" style="width:'.$month_based_width.'px;cursor:pointer; text-align:center;background:#FF9900;" colspan="'.$ref_col.'" onclick="toggleTblNew(\'grp_div_'.$ref_grp_key2.'\');">
							<span id="icon_grp_div_'.$ref_grp_key2.'" style="float:right;" class="ui-icon ui-icon-circle-arrow-n fl"></span>'.ucfirst($ref_grp_val2).'</td></tr>';	
							$ref_phy_data_app="";
							$ref_phy_data_app .='</table>
							<table  style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding" id="grp_div_'.$ref_grp_key2.'">';
							foreach($final_sel_grp_ref_arr[$ref_grp_key2] as $ref_key => $ref_val){
								if(count($hide_ref_app_det_data_rec[$ref_grp_key2][$ref_val])>0){
									$ref_phy_data_app .='<tr>
									<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">'.ucfirst($ref_phy_name_arr[$ref_val]).'</td>';
									$rg_tot_mon_app_arr=array();	
									foreach($monthArr as $mon_key4 => $mon_val4){
										$tot_app=count($ref_app_det_data_rec[$ref_grp_key2][$ref_val][$mon_val4]);
										$grand_month_tot_app[$mon_val4][]=$tot_app;
										$rg_tot_mon_app_arr[]=$tot_app;
										$grand_rg_tot_mon_app_arr[]=$tot_app;
										$total_month_tot_app_arr[$mon_val4][]=$tot_app;
										$total_rg_tot_mon_app_arr[]=$tot_app;
										if($mon_val4=="Total"){
											$rg_tot_mon_app=array_sum($rg_tot_mon_app_arr);
											$ref_phy_data_app .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_tot_mon_app.'</td>';
										}else{
											$ref_phy_data_app .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$tot_app.'</td>';
										}
									}
									 $ref_phy_data_app .='</tr>'; 
								}
							}
							$ref_phy_data_app .='</table>
							<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">';
							$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Appts</td>';
							foreach($monthArr as $mon_key7 => $mon_val7){
								$tot_app_mon_phy=array_sum($total_month_tot_app_arr[$mon_val7]);
								if($mon_val7=="Total"){
									$total_rg_tot_mon_app=array_sum($total_rg_tot_mon_app_arr);
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$total_rg_tot_mon_app.'</td>';
								}else{
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$tot_app_mon_phy.'</td>';	
								}
							}
							$ref_phy_data .='</tr>'.$ref_phy_data_app; 
							
						} 
						$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';
						$ref_phy_data .='<tr><td bgcolor="#009933" colspan="'.$ref_col.'" height="1px"></td></tr>';
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G. Total Appts</td>';
						foreach($monthArr as $mon_key6 => $mon_val6){
							$tot_app_mon=array_sum($grand_month_tot_app[$mon_val6]);
							if($mon_val6=="Total"){
								$grand_rg_tot_mon_app=array_sum($grand_rg_tot_mon_app_arr);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$grand_rg_tot_mon_app.'</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$tot_app_mon.'</td>';	
							}
						}
						$ref_phy_data .='</tr>'; 
						$ref_phy_data .='<tr><td bgcolor="#009933" colspan="'.$ref_col.'" height="1px"></td></tr>';

						foreach($selectedRefGroupArr as $ref_grp_key1 => $ref_grp_val1){
							$total_month_tot_app=array();
							$total_rg_tot_mon_app_arr=array();
							$total_month_tot_chg_arr=array();
							$total_rg_tot_chg_arr=array();
							$total_month_tot_pay_arr=array();
							$total_rg_tot_pay_arr=array();
							$total_month_tot_app_arr=array();
							$sum_grand_month_tot_chg=array();
							$sum_grand_rg_tot_chg_arr=array();
							$sum_grand_month_tot_pay=array();
							$sum_grand_rg_tot_pay_arr=array();
							$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px"></td></tr>';
							$ref_phy_data .='<tr><td class="text_10b" style="width:'.$month_based_width.'px;cursor:pointer;text-align:center;background:#FF9900;" colspan="'.$ref_col.'" onclick="toggleTblNew(\'grp_div_chg_'.$ref_grp_key1.'\');">
							<span id="icon_grp_div_chg_'.$ref_grp_key1.'" style="float:right;" class="ui-icon ui-icon-circle-arrow-n fl"></span>'.ucfirst($ref_grp_val1).'</td></tr>';	
							$ref_phy_data .='</table>
							<table  style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding" id="grp_div_chg_'.$ref_grp_key1.'">';
							foreach($final_sel_grp_ref_arr[$ref_grp_key1] as $ref_key => $ref_val){
								if(array_sum($hide_ref_chg_det_data_rec[$ref_grp_key1][$ref_val])>0 
									|| array_sum($hide_ref_pay_det_data_rec[$ref_grp_key1][$ref_val])>0 
									|| array_sum($sum_hide_ref_det_data_rec[$ref_grp_key1][$ref_val])>0 
									|| array_sum($sum_hide_ref_pay_det_data_rec[$ref_grp_key1][$ref_val])>0){	
									$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';  
									$ref_phy_data .='<tr><td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10b" colspan="'.$ref_col.'">'.ucfirst($ref_phy_name_arr[$ref_val]).'</td></tr>';	
									 
									$proc_chg_mon_arr=array();
									$proc_chg_tot_mon_arr=array();
									
									foreach($ref_chg_data_proc_det_rec[$ref_grp_key1][$ref_val] as $fac_grp_key_proc => $fac_grp_val_proc){
										foreach($monthArr as $mon_key12 => $mon_val12){
											$proc_chg_mon_arr[$mon_val12][]=count($ref_chg_data_proc_det_rec[$ref_grp_key1][$ref_val][$fac_grp_key_proc][$mon_val12]);
											$proc_chg_tot_mon_arr[]=count($ref_chg_data_proc_det_rec[$ref_grp_key1][$ref_val][$fac_grp_key_proc][$mon_val12]);
										}
									}
									foreach($ref_chg_data_proc_det_rec[$ref_grp_key1][$ref_val] as $fac_grp_key_proc => $fac_grp_val_proc){
										$ref_phy_data .='<tr>
											<td style="background:#ECF1EA;text-align:left;width:'.$month_based_width.'px;" class="text_10">'.ucfirst($fac_grp_key_proc).'</td>';
										$data_avg="";	
										$data_avg .='<tr>
											<td style="background:#ECF1EA;text-align:left;width:'.$month_based_width.'px;" class="text_10">&nbsp;</td>';
											$rg_proc_chg_arr=array();
											
											foreach($monthArr as $mon_key12 => $mon_val12){
												$proc_chg_mon=count($ref_chg_data_proc_det_rec[$ref_grp_key1][$ref_val][$fac_grp_key_proc][$mon_val12]);
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
													$ref_phy_data .='<td style="background:#ECF1EA;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_proc_chg.'</td>';
													$data_avg .='<td style="background:#ECF1EA;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_proc_chg_avg.'</td>';
												}else{
													$proc_chg_mon_cont = array_sum($proc_chg_mon_arr[$mon_val12]);
													$proc_chg_mon_avg="";
													if($proc_chg_mon>0){
														$proc_chg_mon_avg=number_format(($proc_chg_mon*100)/$proc_chg_mon_cont,2).'%';
													}else{
														$proc_chg_mon="";
													}
													$ref_phy_data .='<td style="background:#ECF1EA;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$proc_chg_mon.'</td>';
													$data_avg .='<td style="background:#ECF1EA;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$proc_chg_mon_avg.'</td>';
												}
											}
										$data_avg .='</tr>'; 	
										$ref_phy_data .='</tr>'.$data_avg; 
										$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
									} 

									if($srh_type=="" || $srh_type=="Charges"){
										if(array_sum($hide_ref_chg_det_data_rec[$ref_grp_key1][$ref_val])>0){
											$ref_phy_data .='<tr>
											<td style="background:#FFFFFF;width:'.$month_based_width.'px;width:'.$month_based_width.'px;" class="text_10">Charges</td>';
											$ref_phy_chg_avg="";	
											$ref_phy_chg_avg .='<tr>
												<td style="background:#FFFFFF;width:'.$month_based_width.'px;width:'.$month_based_width.'px;" class="text_10">Avg Charges</td>';
											$rg_tot_chg_arr=array();
											$rg_tot_mon_cont_arr=array();
											foreach($monthArr as $mon_key2 => $mon_val2){
												$tot_chg=array_sum($ref_phy_data_chg[$ref_grp_key1][$ref_val][$mon_val2]);
												$grand_month_tot_chg[$mon_val2][]=$tot_chg;
												$y_exp2=explode('-',$mon_val2);
												$yy2='20'.$y_exp2[1];
												//$this_month_chg_days=date('t',mktime(0,0,0,$mon_key2,1,$yy2));
												$this_month_chg_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp2[0]], $yy2);
												$tot_chg_avg_mon=(array_sum($ref_phy_data_chg[$ref_grp_key1][$ref_val][$mon_val2])/$this_month_chg_days);
												$rg_tot_chg_arr[]=$tot_chg;
												$grand_rg_tot_chg_arr[]=$tot_chg;
												$total_month_tot_chg_arr[$mon_val2][]=$tot_chg;
												$total_rg_tot_chg_arr[]=$tot_chg;
												$rg_tot_mon_cont_arr[]=$this_month_chg_days;
												if($mon_val2=="Total"){
													$rg_tot_chg=array_sum($rg_tot_chg_arr);
													$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
													$rg_tot_chg_avg_mon=($rg_tot_chg/$rg_tot_mon_cont);
													$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_chg,2).'</td>';
													$ref_phy_chg_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_chg_avg_mon,2).'</td>';
												}else{
													$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_chg,2).'</td>';
													$ref_phy_chg_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_chg_avg_mon,2).'</td>';
												}
											}
											 $ref_phy_chg_avg .='</tr>';
											 $ref_phy_data .='</tr>'.$ref_phy_chg_avg; 
											 $ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';
										}
									}
									if($srh_type=="" || $srh_type=="Receipts"){
										if(array_sum($hide_ref_pay_det_data_rec[$ref_grp_key1][$ref_val])>0){
											$ref_phy_data .='<tr>
											<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Receipts</td>';
											$ref_phy_rcp_avg="";	
											$ref_phy_rcp_avg .='<tr>
												<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Avg Receipts</td>';
											$rg_tot_pay_arr=array();
											$rg_tot_mon_cont_arr=array();	
											foreach($monthArr as $mon_key => $mon_val){
												$tot_pay=array_sum($ref_phy_data_rec[$ref_grp_key1][$ref_val][$mon_val]);
												$grand_month_tot_pay[$mon_val][]=$tot_pay;
												$y_exp=explode('-',$mon_val);
												$yy='20'.$y_exp[1];
												//$this_month_days=date('t',mktime(0,0,0,$mon_key,1,$yy));
												$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
												$tot_pay_avg_mon=(array_sum($ref_phy_data_rec[$ref_grp_key1][$ref_val][$mon_val])/$this_month_days);
												$rg_tot_pay_arr[]=$tot_pay;
												$grand_rg_tot_pay_arr[]=$tot_pay;
												$total_month_tot_pay_arr[$mon_val][]=$tot_pay;
												$total_rg_tot_pay_arr[]=$tot_pay;
												$rg_tot_mon_cont_arr[]=$this_month_days;
												if($mon_val=="Total"){
													$rg_tot_pay=array_sum($rg_tot_pay_arr);
													$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
													$rg_tot_pay_avg_mon=($rg_tot_pay/$rg_tot_mon_cont);
													$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_pay,2).'</td>';
													$ref_phy_rcp_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_pay_avg_mon,2).'</td>';
												}else{
													$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_pay,2).'</td>';
													$ref_phy_rcp_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_pay_avg_mon,2).'</td>';
												}
											}
											$ref_phy_rcp_avg .='</tr>';
											$ref_phy_data .='</tr>'.$ref_phy_rcp_avg;
											$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>'; 
										}
									}
									if($srh_type=="" || $srh_type=="Charges"){
										if(array_sum($sum_hide_ref_det_data_rec[$ref_grp_key1][$ref_val])>0){
											$ref_phy_data .='<tr>
												<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Summary Charges</td>';
											$sum_rg_tot_chg_arr=array();
											foreach($monthArr as $mon_key2 => $mon_val2){
												$sum_tot_chg=array_sum($sum_ref_phy_data_det_chg[$ref_grp_key1][$ref_val][$mon_val2]);
												$sum_grand_month_tot_chg[$mon_val2][]=$sum_tot_chg;
												$sum_rg_tot_chg_arr[]=$sum_tot_chg;
												$sum_grand_rg_tot_chg_arr[]=$sum_tot_chg;
												$final_sum_grand_month_tot_chg[$mon_val2][]=$sum_tot_chg;
												$final_sum_grand_rg_tot_chg_arr[]=$sum_tot_chg;
												if($mon_val2=="Total"){
													$sum_rg_tot_chg=array_sum($sum_rg_tot_chg_arr);
													$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($sum_rg_tot_chg,2).'</td>';
												}else{
													$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($sum_tot_chg,2).'</td>';
												}
											}
											 $ref_phy_data .='</tr>'; 
										}
									}
									if($srh_type=="" || $srh_type=="Receipts"){
										if(array_sum($sum_hide_ref_pay_det_data_rec[$ref_grp_key1][$ref_val])>0){
											$ref_phy_data .='<tr>
												<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Summary Receipts</td>';
											$sum_rg_tot_pay_arr=array();
											foreach($monthArr as $mon_key2 => $mon_val2){
												$sum_tot_pay=array_sum($sum_ref_phy_data_det_rec[$ref_grp_key1][$ref_val][$mon_val2]);
												$sum_grand_month_tot_pay[$mon_val2][]=$sum_tot_pay;
												$sum_rg_tot_pay_arr[]=$sum_tot_pay;
												$sum_grand_rg_tot_pay_arr[]=$sum_tot_pay;
												$final_sum_grand_month_tot_pay[$mon_val2][]=$sum_tot_pay;
												$final_sum_grand_rg_tot_pay_arr[]=$sum_tot_pay;
												if($mon_val2=="Total"){
													$sum_rg_tot_pay=array_sum($sum_rg_tot_pay_arr);
													$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($sum_rg_tot_pay,2).'</td>';
												}else{
													$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($sum_tot_pay,2).'</td>';
												}
											}
											 $ref_phy_data .='</tr>';  
											 
										}
									}
								}
							}
							
							$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';
							$ref_phy_data .='</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">';
							$proc_chg_mon_arr=array();
							$proc_chg_tot_mon_arr=array();
							
							foreach($ref_chg_data_proc_rec[$ref_grp_key1] as $fac_grp_key_proc => $fac_grp_val_proc){
								foreach($monthArr as $mon_key12 => $mon_val12){
									$proc_chg_mon_arr[$mon_val12][]=count($ref_chg_data_proc_rec[$ref_grp_key1][$fac_grp_key_proc][$mon_val12]);
									$proc_chg_tot_mon_arr[]=count($ref_chg_data_proc_rec[$ref_grp_key1][$fac_grp_key_proc][$mon_val12]);
								}
							}
							foreach($ref_chg_data_proc_rec[$ref_grp_key1] as $fac_grp_key_proc => $fac_grp_val_proc){
								$ref_phy_data .='<tr>
									<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">'.ucfirst($fac_grp_key_proc).'</td>';
								$data_avg="";	
								$data_avg .='<tr>
									<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">&nbsp;</td>';
									$rg_proc_chg_arr=array();
									
									foreach($monthArr as $mon_key12 => $mon_val12){
										$proc_chg_mon=count($ref_chg_data_proc_rec[$ref_grp_key1][$fac_grp_key_proc][$mon_val12]);
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
											$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_proc_chg.'</td>';
											$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_proc_chg_avg.'</td>';
										}else{
											$proc_chg_mon_cont = array_sum($proc_chg_mon_arr[$mon_val12]);
											$proc_chg_mon_avg="";
											if($proc_chg_mon>0){
												$proc_chg_mon_avg=number_format(($proc_chg_mon*100)/$proc_chg_mon_cont,2).'%';
											}else{
												$proc_chg_mon="";
											}
											$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$proc_chg_mon.'</td>';
											$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$proc_chg_mon_avg.'</td>';
										}
									}
								$data_avg .='</tr>'; 	
								$ref_phy_data .='</tr>'.$data_avg; 
								$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
							}
						
							if($srh_type=="" || $srh_type=="Charges"){
								$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Charges</td>';
								$ref_phy_pay_avg_gr ='<tr>
									<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Charges</td>';

								$rg_tot_mon_cont_arr=array();
								foreach($monthArr as $mon_key8 => $mon_val8){
									$tot_chg_mon_phy=array_sum($total_month_tot_chg_arr[$mon_val8]);
									$gy_exp=explode('-',$mon_val8);
									$gyy='20'.$gy_exp[1];
									//$this_month_days_gr_phy=date('t',mktime(0,0,0,$mon_key8,1,$gyy));
									$this_month_days_gr_phy = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$gy_exp[0]], $gyy);
									$tot_chg_avg_mon_gr_phy=($tot_chg_mon_phy/$this_month_days_gr_phy);
									$rg_tot_mon_cont_arr[]=$this_month_days_gr_phy;
									if($mon_val8=="Total"){
										$total_rg_tot_chg=array_sum($total_rg_tot_chg_arr);
										$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
										$total_rg_tot_chg_avg_mon=($total_rg_tot_chg/$rg_tot_mon_cont);
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($total_rg_tot_chg,2).'</td>';
										$ref_phy_pay_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($total_rg_tot_chg_avg_mon,2).'</td>';
									}else{
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_chg_mon_phy,2).'</td>';
										$ref_phy_pay_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_chg_avg_mon_gr_phy,2).'</td>';
									}
								}
								$ref_phy_pay_avg_gr .='</tr>'; 
								$ref_phy_data .='</tr>'.$ref_phy_pay_avg_gr;
								$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';
							}
							
							if($srh_type=="" || $srh_type=="Receipts"){
								$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Receipts</td>';
								$ref_phy_acp_avg_gr ='<tr>
									<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Receipts</td>';	
								$rg_tot_mon_cont_arr=array();
								foreach($monthArr as $mon_key9 => $mon_val9){
									$tot_pay_mon_phy=array_sum($total_month_tot_pay_arr[$mon_val9]);
									$gay_exp=explode('-',$mon_val9);
									$gayy='20'.$gay_exp[1];
									//$this_month_days_ga_phy=date('t',mktime(0,0,0,$mon_key9,1,$gayy));
									$this_month_days_ga_phy = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$gay_exp[0]], $gayy);
									$tot_chg_avg_mon_ga_phy=($tot_pay_mon_phy/$this_month_days_ga_phy);
									$rg_tot_mon_cont_arr[]=$this_month_days_ga_phy;
									if($mon_val9=="Total"){
										$total_rg_tot_pay=array_sum($total_rg_tot_pay_arr);
										$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
										$total_rg_tot_pay_avg_mon=($total_rg_tot_pay/$rg_tot_mon_cont);
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($total_rg_tot_pay,2).'</td>';
										$ref_phy_acp_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($total_rg_tot_pay_avg_mon,2).'</td>';
									}else{
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_pay_mon_phy,2).'</td>';
										$ref_phy_acp_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_chg_avg_mon_ga_phy,2).'</td>';
									}
								}
								$ref_phy_acp_avg_gr .='</tr>'; 
								$ref_phy_data .='</tr>'.$ref_phy_acp_avg_gr; 
								$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>'; 
							}
																
							if($srh_type=="" || $srh_type=="Charges"){
								$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10b">Total Summary Charges</td>';
								foreach($monthArr as $mon_key2 => $mon_val2){
									$gr_sum_tot_chg=array_sum($sum_grand_month_tot_chg[$mon_val2]);
									if($mon_val2=="Total"){
										$gr_sum_rg_tot_chg=array_sum($sum_grand_rg_tot_chg_arr);
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_sum_rg_tot_chg,2).'</td>';
									}else{
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_sum_tot_chg,2).'</td>';
									}
								}
								 $ref_phy_data .='</tr>'; 
							}
							
							if($srh_type=="" || $srh_type=="Receipts"){
								$ref_phy_data .='<tr>
									<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10b">Total Summary Receipts</td>';
								foreach($monthArr as $mon_key2 => $mon_val2){
									$gr_sum_tot_pay=array_sum($sum_grand_month_tot_pay[$mon_val2]);
									if($mon_val2=="Total"){
										$sum_rg_tot_pay=array_sum($sum_grand_rg_tot_pay_arr);
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($sum_rg_tot_pay,2).'</td>';
									}else{
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_sum_tot_pay,2).'</td>';
									}
								}
								 $ref_phy_data .='</tr>';  
							}

							// A/R Part
							$arAgingData="";
							$arAgingData_bal="";
							$arBalTotalStr="";
							$arBalTotalMain="";
							$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
							$ref_phy_data .='</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">    	
								<tr>
									<td class="text_10b" style="background:#FFFFFF;" colspan="8">Total A/R AGING</td>
								</tr>
								<tr>';
								foreach($aggingDrop as $aging_kay => $aging_val){
									$ref_phy_data .='<td class="text_10b" style="background:#FFFFFF; width:126px; text-align:center;">'.$aging_val.'</td>';
								}
								$ref_phy_data .='<td class="text_10b" style="background:#FFFFFF; width:131px;">Total</td></tr><tr>';	
								$arBalTotalMain=array_sum($arAgingDataArr[$ref_grp_key1]['total_ar_bal']);
								foreach($aggingDrop as $aging_kay => $aging_val){
									$arBalTotalStr=array_sum($arAgingDataArr[$ref_grp_key1][$aging_kay]);
									if($arBalTotalStr>0){
										$arAgingData=$CLSReports->numberFormat($arBalTotalStr,2).' ('.number_format(($arBalTotalStr * 100) / $arBalTotalMain,2).'%)';
									}
									$ref_phy_data .='<td class="text_10" style="background:#FFFFFF; width:126px; text-align:right;">'.$arAgingData.'</td>';
								}
								if($arBalTotalMain>0){
									$arAgingData_bal=$CLSReports->numberFormat($arBalTotalMain,2).' ('.number_format(($arBalTotalMain * 100) / $arBalTotalMain,2).'%)';
								}
								$ref_phy_data .='<td class="text_10" style="background:#FFFFFF; width:126px; text-align:right;">'.$arAgingData_bal.'</td>
								</tr>
							</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">';
						}
					$ref_col=count($monthArr)+1;
					$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';
					$ref_phy_data .='<tr><td bgcolor="#009933" colspan="'.$ref_col.'" height="1px"></td></tr>';
					
					$proc_chg_mon_arr=array();
					$proc_chg_tot_mon_arr=array();
						
					foreach($ref_chg_data_proc_tot_rec as $fac_grp_key_proc => $fac_grp_val_proc){
						foreach($monthArr as $mon_key12 => $mon_val12){
							$proc_chg_mon_arr[$mon_val12][]=count($ref_chg_data_proc_tot_rec[$fac_grp_key_proc][$mon_val12]);
							$proc_chg_tot_mon_arr[]=count($ref_chg_data_proc_tot_rec[$fac_grp_key_proc][$mon_val12]);
						}
					}
					foreach($ref_chg_data_proc_tot_rec as $fac_grp_key_proc => $fac_grp_val_proc){
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">'.ucfirst($fac_grp_key_proc).'</td>';
						$data_avg="";	
						$data_avg .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">&nbsp;</td>';
							$rg_proc_chg_arr=array();
							
							foreach($monthArr as $mon_key12 => $mon_val12){
								$proc_chg_mon=count($ref_chg_data_proc_tot_rec[$fac_grp_key_proc][$mon_val12]);
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
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_proc_chg.'</td>';
									$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_proc_chg_avg.'</td>';
								}else{
									$proc_chg_mon_cont = array_sum($proc_chg_mon_arr[$mon_val12]);
									$proc_chg_mon_avg="";
									if($proc_chg_mon>0){
										$proc_chg_mon_avg=number_format(($proc_chg_mon*100)/$proc_chg_mon_cont,2).'%';
									}else{
										$proc_chg_mon="";
									}
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$proc_chg_mon.'</td>';
									$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$proc_chg_mon_avg.'</td>';
								}
							}
						$data_avg .='</tr>'; 	
						$ref_phy_data .='</tr>'.$data_avg; 
						$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
					}
	
					if($srh_type=="" || $srh_type=="Charges"){
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G. Total Chg</td>';
						$ref_phy_pay_avg_gr ='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G. Avg Chg</td>';

						$rg_tot_mon_cont_arr=array();
						foreach($monthArr as $mon_key5 => $mon_val5){
							$tot_chg_mon=array_sum($grand_month_tot_chg[$mon_val5]);
							$gy_exp=explode('-',$mon_val5);
							$gyy='20'.$gy_exp[1];
							//$this_month_days_gr=date('t',mktime(0,0,0,$mon_key5,1,$gyy));
							$this_month_days_gr = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$gy_exp[0]], $gyy);
							$tot_chg_avg_mon_gr=($tot_chg_mon/$this_month_days_gr);
							$rg_tot_mon_cont_arr[]=$this_month_days_gr;
							if($mon_val5=="Total"){
								$grand_rg_tot_chg=array_sum($grand_rg_tot_chg_arr);
								$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
								$gr_rg_tot_chg_avg_mon=($grand_rg_tot_chg/$rg_tot_mon_cont);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($grand_rg_tot_chg,2).'</td>';
								$ref_phy_pay_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_rg_tot_chg_avg_mon,2).'</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_chg_mon,2).'</td>';
								$ref_phy_pay_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_chg_avg_mon_gr,2).'</td>';
							}
						}
						$ref_phy_pay_avg_gr .='</tr>'; 
						$ref_phy_data .='</tr>'.$ref_phy_pay_avg_gr; 
						$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';
					}

					if($srh_type=="" || $srh_type=="Receipts"){
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G. Total Rcpt</td>';
						$ref_phy_acp_avg_gr ='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G. Avg Rcpt</td>';	
						$rg_tot_mon_cont_arr=array();
						foreach($monthArr as $mon_key3 => $mon_val3){
							$tot_pay_mon=array_sum($grand_month_tot_pay[$mon_val3]);
							$gay_exp=explode('-',$mon_val3);
							$gayy='20'.$gay_exp[1];
							//$this_month_days_ga=date('t',mktime(0,0,0,$mon_key3,1,$gayy));
							$this_month_days_ga = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$gay_exp[0]], $gayy);
							$tot_chg_avg_mon_ga=($tot_pay_mon/$this_month_days_ga);
							$rg_tot_mon_cont_arr[]=$this_month_days_ga;
							if($mon_val3=="Total"){
								$grand_rg_tot_pay=array_sum($grand_rg_tot_pay_arr);
								$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
								$gr_rg_tot_pay_avg_mon=($grand_rg_tot_pay/$rg_tot_mon_cont);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($grand_rg_tot_pay,2).'</td>';
								$ref_phy_acp_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_rg_tot_pay_avg_mon,2).'</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_pay_mon,2).'</td>';
								$ref_phy_acp_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_chg_avg_mon_ga,2).'</td>';
							}
						}
						 $ref_phy_acp_avg_gr .='</tr>'; 
						 $ref_phy_data .='</tr>'.$ref_phy_acp_avg_gr; 
						$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>'; 
					}
		
					if($srh_type=="" || $srh_type=="Charges"){
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10b">G. Total Summary Charges</td>';
						$sum_rg_tot_chg_arr=array();
						foreach($monthArr as $mon_key2 => $mon_val2){
							$final_sum_tot_chg=array_sum($final_sum_grand_month_tot_chg[$mon_val2]);
							if($mon_val2=="Total"){
								$final_sum_rg_tot_chg=array_sum($final_sum_grand_rg_tot_chg_arr);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($final_sum_rg_tot_chg,2).'</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($final_sum_tot_chg,2).'</td>';
							}
						}
						 $ref_phy_data .='</tr>'; 
					}
					if($srh_type=="" || $srh_type=="Receipts"){
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10b">G. Total Summary Receipts</td>';
						$sum_rg_tot_pay_arr=array();
						foreach($monthArr as $mon_key2 => $mon_val2){
							$final_sum_tot_pay=array_sum($final_sum_grand_month_tot_pay[$mon_val2]);
							if($mon_val2=="Total"){
								$final_sum_rg_tot_pay=array_sum($final_sum_grand_rg_tot_pay_arr);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($final_sum_rg_tot_pay,2).'</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($final_sum_tot_pay,2).'</td>';
							}
						}
						 $ref_phy_data .='</tr>';  
					}
					
					// A/R Aging 
					$arAgingData="";
					$arAgingData_bal="";
					$arBalTotalStr="";
					$arBalTotalMain="";
					$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
					$ref_phy_data .='</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">    	
						<tr>
							<td class="text_10b" style="background:#FFFFFF;" colspan="8">G. Total A/R AGING</td>
						</tr>
						<tr>';
						foreach($aggingDrop as $aging_kay => $aging_val){
							$ref_phy_data .='<td class="text_10b" style="background:#FFFFFF; width:126px; text-align:center;">'.$aging_val.'</td>';
						}
							$ref_phy_data .='<td class="text_10b" style="background:#FFFFFF; width:131px;">Total</td></tr><tr>';	
						$arBalTotalMain=array_sum($arAgingDataArr_wofac['total_ar_bal']);
						foreach($aggingDrop as $aging_kay => $aging_val){
							$arBalTotalStr=array_sum($arAgingDataArr_wofac[$aging_kay]);
							if($arBalTotalStr>0){
								$arAgingData=$CLSReports->numberFormat($arBalTotalStr,2).' ('.number_format(($arBalTotalStr * 100) / $arBalTotalMain,2).'%)';
							}
							$ref_phy_data .='<td class="text_10" style="background:#FFFFFF; width:126px; text-align:right;">'.$arAgingData.'</td>';
						}
							if($arBalTotalMain>0){
								$arAgingData_bal=$CLSReports->numberFormat($arBalTotalMain,2).' ('.number_format(($arBalTotalMain * 100) / $arBalTotalMain,2).'%)';
							}
							$ref_phy_data .='<td class="text_10" style="background:#FFFFFF; width:126px; text-align:right;">'.$arAgingData_bal.'</td>
						</tr>
					</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding"> ';  
					$ref_phy_data .='<tr><td bgcolor="#009933" colspan="'.$ref_col.'" height="1px"></td></tr>';
				}else{
					$ref_phy_data="";
					$ref_col=count($monthArr)+1;
					$grand_month_tot_pay=array();
					$month_based_width=(1024/(count($monthArr)+1));
					$ref_phy_data='<tr><td class="text_b_w" style="width:'.$month_based_width.'px;">Ref Phy Groups</td>';
						 foreach($monthArr as $mon_name_key => $mon_name_val){
						   $ref_phy_data .='<td class="text_b_w" style="text-align:right;width:'.$month_based_width.'px;">'.ucfirst($mon_name_val).'</td>';
						}
				   $ref_phy_data .='</tr>'; 
				   
				   $rg_tot_mon_app="";
				   foreach($selectedRefGroupArr as $ref_grp_key2 => $ref_grp_val2){
						$total_month_tot_app=array();
						$total_rg_tot_mon_app_arr=array();
						$total_month_tot_app_arr=array();
						$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="2px"></td></tr>';
						$ref_phy_data .='<tr><td class="text_10b" style="width:'.$month_based_width.'px;cursor:pointer; text-align:center;background:#FF9900;" colspan="'.$ref_col.'" onclick="toggleTblNew(\'grp_div_'.$ref_grp_key2.'\');">
						<span id="icon_grp_div_'.$ref_grp_key2.'" style="float:right;" class="ui-icon ui-icon-circle-arrow-n fl"></span>'.ucfirst($ref_grp_val2).'</td></tr>';	
						$ref_phy_data_app="";
						$ref_phy_data_app .='</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding" id="grp_div_'.$ref_grp_key2.'">';
						foreach($final_sel_grp_ref_arr[$ref_grp_key2] as $ref_key => $ref_val){
							if(count($hide_ref_app_det_data_rec[$ref_grp_key2][$ref_val])>0){
								$ref_phy_data_app .='<tr>
								<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">'.ucfirst($ref_phy_name_arr[$ref_val]).'</td>';
								$rg_tot_mon_app_arr=array();	
								foreach($monthArr as $mon_key4 => $mon_val4){
									$tot_app=count($ref_app_det_data_rec[$ref_grp_key2][$ref_val][$mon_val4]);
									$grand_month_tot_app[$mon_val4][]=$tot_app;
									$rg_tot_mon_app_arr[]=$tot_app;
									$grand_rg_tot_mon_app_arr[]=$tot_app;
									$total_month_tot_app_arr[$mon_val4][]=$tot_app;
									$total_rg_tot_mon_app_arr[]=$tot_app;
									if($mon_val4=="Total"){
										$rg_tot_mon_app=array_sum($rg_tot_mon_app_arr);
										$ref_phy_data_app .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_tot_mon_app.'&nbsp;&nbsp;</td>';
									}else{
										$ref_phy_data_app .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$tot_app.'&nbsp;&nbsp;</td>';
									}
								}
								 $ref_phy_data_app .='</tr>'; 
							}
						}
						$ref_phy_data_app .='</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">';
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Appts</td>';
						foreach($monthArr as $mon_key7 => $mon_val7){
							$tot_app_mon_phy=array_sum($total_month_tot_app_arr[$mon_val7]);
							if($mon_val7=="Total"){
								$total_rg_tot_mon_app=array_sum($total_rg_tot_mon_app_arr);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$total_rg_tot_mon_app.'&nbsp;&nbsp;</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$tot_app_mon_phy.'&nbsp;&nbsp;</td>';	
							}
						}
						$ref_phy_data .='</tr>'.$ref_phy_data_app; 
						
					} 
					$ref_phy_data .='<tr><td bgcolor="#009933" colspan="'.$ref_col.'" height="1px"></td></tr>';
	
					$ref_phy_data .='<tr>
						<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">G. Total Appts</td>';
					foreach($monthArr as $mon_key6 => $mon_val6){
						$tot_app_mon=array_sum($grand_month_tot_app[$mon_val6]);
						if($mon_val6=="Total"){
							$grand_rg_tot_mon_app=array_sum($grand_rg_tot_mon_app_arr);
							$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$grand_rg_tot_mon_app.'&nbsp;&nbsp;</td>';
						}else{
							$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$tot_app_mon.'&nbsp;&nbsp;</td>';	
						}
					}
					$ref_phy_data .='</tr>'; 
					$ref_phy_data .='<tr><td bgcolor="#009933" colspan="'.$ref_col.'" height="1px"></td></tr>'; 
					
					foreach($selectedRefGroupArr as $ref_grp_key => $ref_grp_val){
						$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';
						$ref_phy_data .='<tr><td class="text_b_w" style="width:'.$month_based_width.'px;text-align:center;" colspan="'.$ref_col.'" height="1px">
						'.ucfirst($ref_grp_val).'</td></tr>';
						
						$proc_chg_mon_arr=array();
						$proc_chg_tot_mon_arr=array();
						
						foreach($ref_chg_data_proc_rec[$ref_grp_key] as $fac_grp_key_proc => $fac_grp_val_proc){
							foreach($monthArr as $mon_key12 => $mon_val12){
								$proc_chg_mon_arr[$mon_val12][]=count($ref_chg_data_proc_rec[$ref_grp_key][$fac_grp_key_proc][$mon_val12]);
								$proc_chg_tot_mon_arr[]=count($ref_chg_data_proc_rec[$ref_grp_key][$fac_grp_key_proc][$mon_val12]);
							}
						}
						foreach($ref_chg_data_proc_rec[$ref_grp_key] as $fac_grp_key_proc => $fac_grp_val_proc){
							$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">'.ucfirst($fac_grp_key_proc).'</td>';
							$data_avg="";	
							$data_avg .='<tr>
								<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">&nbsp;</td>';
								$rg_proc_chg_arr=array();
								
								foreach($monthArr as $mon_key12 => $mon_val12){
									$proc_chg_mon=count($ref_chg_data_proc_rec[$ref_grp_key][$fac_grp_key_proc][$mon_val12]);
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
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_proc_chg.'&nbsp;&nbsp;</td>';
										$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_proc_chg_avg.'&nbsp;&nbsp;</td>';
									}else{
										$proc_chg_mon_cont = array_sum($proc_chg_mon_arr[$mon_val12]);
										$proc_chg_mon_avg="";
										if($proc_chg_mon>0){
											$proc_chg_mon_avg=number_format(($proc_chg_mon*100)/$proc_chg_mon_cont,2).'%';
										}else{
											$proc_chg_mon="";
										}
										$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$proc_chg_mon.'</td>';
										$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$proc_chg_mon_avg.'</td>';
									}
								}
							$data_avg .='</tr>'; 	
							$ref_phy_data .='</tr>'.$data_avg; 
							
							foreach($ref_chg_data_proc_sum_det_rec[$ref_grp_key][$fac_grp_key_proc] as $fac_grp_key_proc_name => $fac_grp_val_proc_name){
								foreach($monthArr as $mon_key12 => $mon_val12){
									$proc_chg_mon_exp_arr[$mon_val12][]=count($ref_chg_data_proc_sum_det_rec[$ref_grp_key][$fac_grp_key_proc][$fac_grp_key_proc_name][$mon_val12]);
									$proc_chg_tot_mon_exp_arr[]=count($ref_chg_data_proc_sum_det_rec[$ref_grp_key][$fac_grp_key_proc][$fac_grp_key_proc_name][$mon_val12]);
								}
							}
							foreach($ref_chg_data_proc_sum_det_rec[$ref_grp_key][$fac_grp_key_proc] as $fac_grp_key_proc_name => $fac_grp_val_proc_name){
								$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">'.ucfirst($procCptCodePractArr[$fac_grp_key_proc_name]).'</td>';
								$data_avg="";	
								$data_avg .='<tr>
									<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10">&nbsp;</td>';
									$rg_proc_chg_arr=array();
									
									foreach($monthArr as $mon_key12 => $mon_val12){
										$proc_chg_mon=count($ref_chg_data_proc_sum_det_rec[$ref_grp_key][$fac_grp_key_proc][$fac_grp_key_proc_name][$mon_val12]);
										$rg_proc_chg_arr[]=$proc_chg_mon;
										
										if($mon_val12=="Total"){
											$rg_proc_chg=array_sum($rg_proc_chg_arr);
											$proc_chg_tot_mon_cont=array_sum($proc_chg_tot_mon_exp_arr);
											$rg_proc_chg_avg="";
											if($rg_proc_chg>0){
												$rg_proc_chg_avg=number_format(($rg_proc_chg*100)/$proc_chg_tot_mon_cont,2).'%';
											}else{
												$rg_proc_chg_avg="";
											}
											$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_proc_chg.'</td>';
											$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$rg_proc_chg_avg.'</td>';
										}else{
											$proc_chg_mon_cont = array_sum($proc_chg_mon_exp_arr[$mon_val12]);
											$proc_chg_mon_avg="";
											if($proc_chg_mon>0){
												$proc_chg_mon_avg=number_format(($proc_chg_mon*100)/$proc_chg_mon_cont,2).'%';
											}else{
												$proc_chg_mon="";
											}
											$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$proc_chg_mon.'</td>';
											$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$proc_chg_mon_avg.'</td>';
										}
									}
								$data_avg .='</tr>'; 	
								$ref_phy_data .='</tr>'.$data_avg; 
							}
							$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
						}
													 
						if($srh_type=="" || $srh_type=="Charges"){
							$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Charges</td>';
							$ref_phy_chg_avg="";	
							$ref_phy_chg_avg .='<tr>
								<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Avg Charges</td>';
							$rg_tot_chg_arr=array();
							$rg_tot_mon_cont_arr=array();
							foreach($monthArr as $mon_key2 => $mon_val2){
								$tot_chg=array_sum($ref_phy_data_chg_summary[$ref_grp_key][$mon_val2]);
								$grand_month_tot_chg[$mon_val2][]=$tot_chg;
								$y_exp2=explode('-',$mon_val2);
								$yy2='20'.$y_exp2[1];
								//$this_month_chg_days=date('t',mktime(0,0,0,$mon_key2,1,$yy2));
								$this_month_chg_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp2[0]], $yy2);
								$tot_chg_avg_mon=(array_sum($ref_phy_data_chg_summary[$ref_grp_key][$mon_val2])/$this_month_chg_days);
								$rg_tot_chg_arr[]=$tot_chg;
								$grand_rg_tot_chg_arr[]=$tot_chg;
								$rg_tot_mon_cont_arr[]=$this_month_chg_days;
								if($mon_val2=="Total"){
									$rg_tot_chg=array_sum($rg_tot_chg_arr);
									$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
									$rg_tot_chg_avg_mon=($rg_tot_chg/$rg_tot_mon_cont);
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_chg,2).'</td>';
									$ref_phy_chg_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_chg_avg_mon,2).'</td>';
								}else{
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_chg,2).'</td>';
									$ref_phy_chg_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_chg_avg_mon,2).'</td>';
								}
							}
							 $ref_phy_chg_avg .='</tr>';
							 $ref_phy_data .='</tr>'.$ref_phy_chg_avg; 
							 $ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';
						}
						if($srh_type=="" || $srh_type=="Receipts"){
							$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Receipts</td>';
							$ref_phy_rcp_avg="";	
							$ref_phy_rcp_avg .='<tr>
								<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Avg Receipts</td>';
							$rg_tot_pay_arr=array();	
							$rg_tot_mon_cont_arr=array();
							foreach($monthArr as $mon_key => $mon_val){
								$tot_pay=array_sum($ref_phy_data_rec_summary[$ref_grp_key][$mon_val]);
								$grand_month_tot_pay[$mon_val][]=$tot_pay;
								$y_exp=explode('-',$mon_val);
								$yy='20'.$y_exp[1];
								//$this_month_days=date('t',mktime(0,0,0,$mon_key,1,$yy));
								$this_month_days = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$y_exp[0]], $yy);
								$tot_pay_avg_mon=(array_sum($ref_phy_data_rec_summary[$ref_grp_key][$mon_val])/$this_month_days);
								$rg_tot_pay_arr[]=$tot_pay;
								$grand_rg_tot_pay_arr[]=$tot_pay;
								$rg_tot_mon_cont_arr[]=$this_month_days;
								if($mon_val=="Total"){
									$rg_tot_pay=array_sum($rg_tot_pay_arr);
									$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
									$rg_tot_pay_avg_mon=($rg_tot_pay/$rg_tot_mon_cont);
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_pay,2).'</td>';
									$ref_phy_rcp_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($rg_tot_pay_avg_mon,2).'</td>';
								}else{
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_pay,2).'</td>';
									$ref_phy_rcp_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($tot_pay_avg_mon,2).'</td>';
								}
							}
							 $ref_phy_rcp_avg .='</tr>';
							 $ref_phy_data .='</tr>'.$ref_phy_rcp_avg;
							$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>'; 
						}
						
						if($srh_type=="" || $srh_type=="Charges"){
							$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Summary Charges</td>';
							$sum_rg_tot_chg_arr=array();
							foreach($monthArr as $mon_key2 => $mon_val2){
								$sum_tot_chg=array_sum($sum_ref_phy_data_chg[$ref_grp_key][$mon_val2]);
								$sum_grand_month_tot_chg[$mon_val2][]=$sum_tot_chg;
								$y_exp2=explode('-',$mon_val2);
								$yy2='20'.$y_exp2[1];
								$sum_rg_tot_chg_arr[]=$sum_tot_chg;
								$sum_grand_rg_tot_chg_arr[]=$sum_tot_chg;
								if($mon_val2=="Total"){
									$sum_rg_tot_chg=array_sum($sum_rg_tot_chg_arr);
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($sum_rg_tot_chg,2).'</td>';
								}else{
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($sum_tot_chg,2).'</td>';
								}
							}
							 $ref_phy_data .='</tr>'; 
						}
						
						if($srh_type=="" || $srh_type=="Receipts"){ 
							$ref_phy_data .='<tr>
								<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10">Summary Receipts</td>';
							$sum_rg_tot_pay_arr=array();
							foreach($monthArr as $mon_key2 => $mon_val2){
								$sum_tot_pay=array_sum($sum_ref_phy_data_rec[$ref_grp_key][$mon_val2]);
								$sum_grand_month_tot_pay[$mon_val2][]=$sum_tot_pay;
								$y_exp2=explode('-',$mon_val2);
								$yy2='20'.$y_exp2[1];
								$sum_rg_tot_pay_arr[]=$sum_tot_pay;
								$sum_grand_rg_tot_pay_arr[]=$sum_tot_pay;
								if($mon_val2=="Total"){
									$sum_rg_tot_pay=array_sum($sum_rg_tot_pay_arr);
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($sum_rg_tot_pay,2).'</td>';
								}else{
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10">'.$CLSReports->numberFormat($sum_tot_pay,2).'</td>';
								}
							}
							 $ref_phy_data .='</tr>';  
							 $ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';  
						}
						// A/R Aging Part
						$arAgingData="";
						$arAgingData_bal="";
						$arBalTotalStr="";
						$arBalTotalMain="";
						$ref_phy_data .='</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">    	
							<tr>
								<td class="text_10b" style="background:#FFFFFF;" colspan="8">A/R AGING</td>
							</tr>
							<tr>';
							foreach($aggingDrop as $aging_kay => $aging_val){
								$ref_phy_data .='<td class="text_10b" style="background:#FFFFFF; width:126px; text-align:center;">'.$aging_val.'</td>';
							}
							$ref_phy_data .='<td class="text_10b" style="background:#FFFFFF; width:131px;">Total</td></tr><tr>';	
							$arBalTotalMain=array_sum($arAgingDataArr[$ref_grp_key]['total_ar_bal']);
							foreach($aggingDrop as $aging_kay => $aging_val){
								$arBalTotalStr=array_sum($arAgingDataArr[$ref_grp_key][$aging_kay]);
								if($arBalTotalStr>0){
									$arAgingData=$CLSReports->numberFormat($arBalTotalStr,2).' ('.number_format(($arBalTotalStr * 100) / $arBalTotalMain,2).'%)';
								}
								$ref_phy_data .='<td class="text_10" style="background:#FFFFFF; width:126px; text-align:right;">'.$arAgingData.'</td>';
							}
							if($arBalTotalMain>0){
								$arAgingData_bal=$CLSReports->numberFormat($arBalTotalMain,2).' ('.number_format(($arBalTotalMain * 100) / $arBalTotalMain,2).'%)';
							}
							$ref_phy_data .='<td class="text_10" style="background:#FFFFFF; width:126px; text-align:right;">'.$arAgingData_bal.'</td>
							</tr>
						</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">';
					}
					$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1050px;">&nbsp;</td></tr>';
					/*$ref_phy_data .='<tr>
						<td style="background:#FFFFFF;text-align:left;" class="text_10b">Total Appts</td>';
					foreach($monthArr as $mon_key6 => $mon_val6){
						$tot_app_mon=array_sum($grand_month_tot_app[$mon_val6]);
						if($mon_val6=="Total"){
							$grand_rg_tot_mon_app=array_sum($grand_rg_tot_mon_app_arr);
							$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;" class="text_10b">'.$grand_rg_tot_mon_app.'
							</td>';
						}else{
							$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;" class="text_10b">'.$tot_app_mon.'
							</td>';	
						}
					}
					$ref_phy_data .='</tr>';*/
					
					
						
					$rg_tot_mon_cont_arr=array();
					$ref_phy_data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';
					
					$proc_chg_mon_arr=array();
					$proc_chg_tot_mon_arr=array();
						
					foreach($ref_chg_data_proc_tot_rec as $fac_grp_key_proc => $fac_grp_val_proc){
						foreach($monthArr as $mon_key12 => $mon_val12){
							$proc_chg_mon_arr[$mon_val12][]=count($ref_chg_data_proc_tot_rec[$fac_grp_key_proc][$mon_val12]);
							$proc_chg_tot_mon_arr[]=count($ref_chg_data_proc_tot_rec[$fac_grp_key_proc][$mon_val12]);
						}
					}
					foreach($ref_chg_data_proc_tot_rec as $fac_grp_key_proc => $fac_grp_val_proc){
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">'.ucfirst($fac_grp_key_proc).'</td>';
						$data_avg="";	
						$data_avg .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">&nbsp;</td>';
							$rg_proc_chg_arr=array();
							
							foreach($monthArr as $mon_key12 => $mon_val12){
								$proc_chg_mon=count($ref_chg_data_proc_tot_rec[$fac_grp_key_proc][$mon_val12]);
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
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_proc_chg.'</td>';
									$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$rg_proc_chg_avg.'</td>';
								}else{
									$proc_chg_mon_cont = array_sum($proc_chg_mon_arr[$mon_val12]);
									$proc_chg_mon_avg="";
									if($proc_chg_mon>0){
										$proc_chg_mon_avg=number_format(($proc_chg_mon*100)/$proc_chg_mon_cont,2).'%';
									}else{
										$proc_chg_mon="";
									}
									$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$proc_chg_mon.'</td>';
									$data_avg .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$proc_chg_mon_avg.'</td>';
								}
							}
						$data_avg .='</tr>'; 	
						$ref_phy_data .='</tr>'.$data_avg; 
						$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
					}
					
					if($srh_type=="" || $srh_type=="Charges"){
						$rg_tot_mon_cont_arr=array();
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Charges</td>';
						$ref_phy_pay_avg_gr .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Charges</td>';
						foreach($monthArr as $mon_key5 => $mon_val5){
							$tot_chg_mon=array_sum($grand_month_tot_chg[$mon_val5]);
							$gy_exp=explode('-',$mon_val5);
							$gyy='20'.$gy_exp[1];
							//$this_month_days_gr=date('t',mktime(0,0,0,$mon_key5,1,$gyy));
							$this_month_days_gr = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$gy_exp[0]], $gyy);
							$tot_chg_avg_mon_gr=($tot_chg_mon/$this_month_days_gr);
							$rg_tot_mon_cont_arr[]=$this_month_days_gr;
							if($mon_val5=="Total"){
								$grand_rg_tot_chg=array_sum($grand_rg_tot_chg_arr);
								$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
								$rg_gr_tot_chg_avg_mon=($grand_rg_tot_chg/$rg_tot_mon_cont);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($grand_rg_tot_chg,2).'</td>';
								$ref_phy_pay_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($rg_gr_tot_chg_avg_mon,2).'</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_chg_mon,2).'</td>';
								$ref_phy_pay_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_chg_avg_mon_gr,2).'</td>';
							}
						}
						$ref_phy_pay_avg_gr .='</tr>'; 
						$ref_phy_data .='</tr>'.$ref_phy_pay_avg_gr; 
					}
					
					if($srh_type=="" || $srh_type=="Receipts"){
						$rg_tot_mon_cont_arr=array();
						$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Total Receipts</td>';
						$ref_phy_acp_avg_gr .='<tr>
							<td style="background:#FFFFFF;text-align:left;width:'.$month_based_width.'px;" class="text_10b">Avg Receipts</td>';	
						foreach($monthArr as $mon_key3 => $mon_val3){
							$tot_pay_mon=array_sum($grand_month_tot_pay[$mon_val3]);
							$gay_exp=explode('-',$mon_val3);
							$gayy='20'.$gay_exp[1];
							//$this_month_days_ga=date('t',mktime(0,0,0,$mon_key3,1,$gayy));
							$this_month_days_ga = cal_days_in_month(CAL_GREGORIAN, $monthNumberArr[$gay_exp[0]], $gayy);
							$tot_chg_avg_mon_ga=($tot_pay_mon/$this_month_days_ga);
							$rg_tot_mon_cont_arr[]=$this_month_days_ga;
							if($mon_val3=="Total"){
								$grand_rg_tot_pay=array_sum($grand_rg_tot_pay_arr);
								$rg_tot_mon_cont=array_sum($rg_tot_mon_cont_arr);
								$rg_gr_tot_pay_avg_mon=($grand_rg_tot_pay/$rg_tot_mon_cont);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($grand_rg_tot_pay,2).'</td>';
								$ref_phy_acp_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($rg_gr_tot_pay_avg_mon,2).'</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_pay_mon,2).'</td>';
								$ref_phy_acp_avg_gr .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($tot_chg_avg_mon_ga,2).'</td>';
							}
						}
						 $ref_phy_acp_avg_gr .='</tr>'; 
						 $ref_phy_data .='</tr>'.$ref_phy_acp_avg_gr; 
						 $ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>'; 
					}
					
					if($srh_type=="" || $srh_type=="Charges"){	
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10b">Total Summary Charges</td>';
						$sum_rg_tot_chg_arr=array();
						foreach($monthArr as $mon_key2 => $mon_val2){
							$gr_sum_tot_chg=array_sum($sum_grand_month_tot_chg[$mon_val2]);
							if($mon_val2=="Total"){
								$gr_sum_rg_tot_chg=array_sum($sum_grand_rg_tot_chg_arr);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_sum_rg_tot_chg,2).'</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_sum_tot_chg,2).'</td>';
							}
						}
						 $ref_phy_data .='</tr>'; 
					}
					
					if($srh_type=="" || $srh_type=="Receipts"){
						$ref_phy_data .='<tr>
							<td style="background:#FFFFFF;width:'.$month_based_width.'px;" class="text_10b">Total Summary Receipts</td>';
						$sum_rg_tot_pay_arr=array();
						foreach($monthArr as $mon_key2 => $mon_val2){
							$gr_sum_tot_pay=array_sum($sum_grand_month_tot_pay[$mon_val2]);
							if($mon_val2=="Total"){
								$sum_rg_tot_pay=array_sum($sum_grand_rg_tot_pay_arr);
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($sum_rg_tot_pay,2).'</td>';
							}else{
								$ref_phy_data .='<td style="background:#FFFFFF;text-align:right;width:'.$month_based_width.'px;" class="text_10b">'.$CLSReports->numberFormat($gr_sum_tot_pay,2).'</td>';
							}
						}
						 $ref_phy_data .='</tr>';
					}
					
						// A/R Aging Part
						$arAgingData="";
						$arAgingData_bal="";
						$arBalTotalStr="";
						$arBalTotalMain="";
						$ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.(count($monthArr)+1).'" height="10px" style="width:1024px;"></td></tr>';
						$ref_phy_data .='</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">    	
							<tr>
								<td class="text_10b" style="background:#FFFFFF;" colspan="8">Total A/R AGING</td>
							</tr>
							<tr>';
							foreach($aggingDrop as $aging_kay => $aging_val){
								$ref_phy_data .='<td class="text_10b" style="background:#FFFFFF; width:126px; text-align:center;">'.$aging_val.'</td>';
							}
								$ref_phy_data .='<td class="text_10b" style="background:#FFFFFF; width:131px;">Total</td></tr><tr>';	
							$arBalTotalMain=array_sum($arAgingDataArr_wofac['total_ar_bal']);
							foreach($aggingDrop as $aging_kay => $aging_val){
								$arBalTotalStr=array_sum($arAgingDataArr_wofac[$aging_kay]);
								$arAgingData=$CLSReports->numberFormat($arBalTotalStr,2).' ('.number_format(($arBalTotalStr * 100) / $arBalTotalMain,2).'%)';
								$ref_phy_data .='<td class="text_10" style="background:#FFFFFF; width:126px; text-align:right;">'.$arAgingData.'</td>';
							}
								$arAgingData_bal=$CLSReports->numberFormat($arBalTotalMain,2).' ('.number_format(($arBalTotalMain * 100) / $arBalTotalMain,2).'%)';
								$ref_phy_data .='<td class="text_10" style="background:#FFFFFF; width:126px; text-align:right;">'.$arAgingData_bal.'</td>
							</tr>
						</table><table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">'; 	  
						 $ref_phy_data .='<tr><td bgcolor="#ffffff" colspan="'.$ref_col.'" height="10px">&nbsp;</td></tr>';  
						 
					 $ref_phy_data .='<tr><td bgcolor="#009933" colspan="'.(count($monthArr)+1).'" height="1px"></td></tr>';
				 }
			 
		}
		

	}

	$HTMLCreated=0;
	if($ref_phy_data!=""){
		$printFile = true;
		$printData=true;
		$HTMLCreated=1;

		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$createdBy = ucfirst(trim($op_name_arr[1][0]));
		$createdBy .= ucfirst(trim($op_name_arr[0][0]));

		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		

		$header_part='
		<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">		
			<tr>
				<td class="rptbx1" style="width:33%;">Referring Physician Monthly Report ('.$processReport.')</td>
				<td class="rptbx2" style="text-align:center;width:33%;">Report Period : '.$Start_date.' to '.$End_date.'</td>
				<td class="rptbx3" style="text-align:right;width:34%;">
					Created By '.$createdBy.' on '.date(phpDateFormat().' h:i A').'&nbsp;
				</td>
			</tr>
		</table>';

		//PAGE DATA
		$page_data=
			$styleHTML.
			$header_part.'
			<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">
				'.$ref_phy_data.'
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
			<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">
				'.$ref_phy_data.'
			</table>
		</page>';
	
		//CREATING PDF
		$file_location = write_html($pdfData);
	}
}

if($callFrom!= 'scheduled'){
	if($page_data){
		echo $page_data;
	}else{
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>