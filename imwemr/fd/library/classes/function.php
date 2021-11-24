<?php
/*
 * File: function.php
 * Coded in PHP7
 * Purpose: Contains all the functions
 * Access Type: Include file
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
/*function showCurrency(){
	if(isset($GLOBALS['currency'])){
		return $GLOBALS['currency'];
	}else{
		return '$';
	}
}
function numberFormat($value='',$format,$show_zero='',$currency='',$show_currency=''){
	$currency = $currency!="" ? $currency : showCurrency();
	$value = number_format($value, $format);
	if($value > 0){
		//$value = '$'.$value;
		$value = $currency.$value;
	}
	else if($value < 0){
		$value = str_replace('-', '-'.$currency, $value);
	}
	else{
		if(empty($show_zero) === true){
			$value = NULL;
		}
		else{
			$value = preg_replace("/,/","",$value);
			if(empty($value)===true){
				$value='0.00';
			}
			$value = $currency.$value;
		}
	}
	return $value;
}*/

function pos_fun($cond_arr=array()){
	if(in_array('other',$cond_arr)){
		$pos_fac_arr[0]='Other';
	}
	$fac_qry=imw_query("select pos_tbl.pos_prac_code,pos_facilityies_tbl.facilityPracCode,pos_facilityies_tbl.pos_facility_id from 
	pos_facilityies_tbl join pos_tbl on pos_tbl.pos_id=pos_facilityies_tbl.pos_id order by pos_facilityies_tbl.facilityPracCode asc");
	while($fac_row=imw_fetch_array($fac_qry)){
		$pos_fac_arr[$fac_row['pos_facility_id']]=str_replace("'",'',$fac_row['facilityPracCode']).' - '.$fac_row['pos_prac_code'];
	}
	
	$ret_pos_arr['pos_name_by_id']=$pos_fac_arr;
	return $ret_pos_arr;
}

function facility_fun($cond_arr=array()){
	if(in_array('other',$cond_arr)){
		$fac_arr[0]='Other';
	}
	$fac_qry=imw_query("select id,name,fac_prac_code from facility order by name asc");
	while($fac_row=imw_fetch_array($fac_qry)){
		$fac_arr[$fac_row['fac_prac_code']]=$fac_row['name'];
		$fac_name_arr[$fac_row['id']]=$fac_row['name'];
	}
	
	$ret_fac_arr['fac_name_by_prac']=$fac_arr;
	$ret_fac_arr['fac_name_by_id']=$fac_name_arr;
	return $ret_fac_arr;
}

function cpt_fun($cond_arr=array()){
	if(in_array('other',$cond_arr)){
		$cpt_name_arr[0]='Other';
	}
	$cpt_qry=imw_query("select cpt_fee_id,departmentId,cpt_prac_code,cpt4_code from cpt_fee_tbl order by cpt_prac_code asc");
	while($cpt_row=imw_fetch_array($cpt_qry)){
		$cpt_prac_code=$cpt_row['cpt_prac_code'];
		if($cpt_prac_code==""){
			$cpt_prac_code=$cpt_row['cpt4_code'];
		}
		if($cpt_prac_code!=""){
			$cpt_dept_arr[$cpt_row['cpt_fee_id']]=$cpt_row['departmentId'];
			$cpt_name_arr[$cpt_row['cpt_fee_id']]=$cpt_prac_code;
			$cpt_dept_id_arr[$cpt_row['departmentId']][]=$cpt_row['cpt_fee_id'];
		}
	}
	$ret_cpt_arr['dept_id_by_cpt']=$cpt_dept_arr;
	$ret_cpt_arr['cpt_name_by_id']=$cpt_name_arr;
	$ret_cpt_arr['cpt_id_by_dept']=$cpt_dept_id_arr;
	return $ret_cpt_arr;
}

function department_fun($cond_arr=array()){
	if(in_array('other',$cond_arr)){
		$dept_arr[0]='Other';
	}
	$dept_qry=imw_query("select DepartmentId,DepartmentDesc from department_tbl order by DepartmentDesc asc");
	while($dept_row=imw_fetch_array($dept_qry)){
		$dept_arr[$dept_row['DepartmentId']]=str_replace("'",'',$dept_row['DepartmentDesc']);
	}
	$ret_dept_arr['dept_desc_by_id']=$dept_arr;
	return $ret_dept_arr;
}


function ins_comp_fun($cond_arr=array()){
	if(in_array('other',$cond_arr)){
		$ins_comp_arr[0]='Other';
	}
	$ins_qry=imw_query("select id,in_house_code,name,groupedIn from insurance_companies WHERE in_house_code IS NOT NULL order by in_house_code");
	while($ins_row=imw_fetch_array($ins_qry)){
		if($ins_row['name']==""){
			$ins_name=str_replace("'",'',$ins_row['in_house_code']);
		}else{
			$ins_name=str_replace("'",'',$ins_row['name']);
		}
		$ins_comp_arr[$ins_row['id']]=$ins_name;
		$ins_comp_grp_arr[$ins_row['id']]=$ins_row['groupedIn'];
	}
	
	$ret_ins_arr['ins_name_by_id']=$ins_comp_arr;
	$ret_ins_arr['ins_grp_by_id']=$ins_comp_grp_arr;
	return $ret_ins_arr;
}

function users_fun($cond_arr=array()){
	if(in_array('other',$cond_arr)){
		$usr_arr[0]='Other';
	}
	$whr_cond="";
	if(count($cond_arr['type'])>0){
		$db_type_arr=implode("','",$cond_arr['type']);
		$whr_cond .=" and user_type in('$db_type_arr')";
	}
	if(count($cond_arr['del_status'])>0){
		$db_del_status=implode("','",$cond_arr['del_status']);
		$whr_cond .=" and delete_status='$db_del_status'";
	}
	$qry=imw_query("select id,fname,mname,lname,delete_status from users where id>0 $whr_cond order by lname,fname,mname");
	while($row=imw_fetch_array($qry)){
		$usr_arr[$row['id']]=$row['lname'].', '.$row['fname'].' '.$row['mname'];
		$usr_arr[$row['id']]=str_replace("'",'',$usr_arr[$row['id']]);
		$usr_del_arr[$row['id']]=$row['delete_status'];
	}
	$ret_usr_arr['user_name_by_id']=$usr_arr;
	$ret_usr_arr['user_del_status_by_id']=$usr_del_arr;
	return $ret_usr_arr;
}

function ref_phy_fun($cond_arr=array()){
	
	$qry=imw_query("select physician_Reffer_id,FirstName,MiddleName,LastName,delete_status from refferphysician order by LastName,FirstName asc");
	while($row=imw_fetch_array($qry)){
		$ref_arr[$row['physician_Reffer_id']]=$row['LastName'].', '.$row['FirstName'].' '.$row['MiddleName'];
		$ref_del_arr[$row['physician_Reffer_id']]=$row['delete_status'];
	}
	if(in_array('other',$cond_arr)){
		$ref_arr[0]='Other';
	}
	$ret_ref_arr['ref_name_by_id']=$ref_arr;
	$ret_ref_arr['ref_del_status_by_id']=$ref_del_arr;
	return $ret_ref_arr;
}

function sch_proc_fun($cond_arr=array()){
	if(in_array('other',$cond_arr)){
		$sch_proc_arr[0]='Other';
	}
	$whr_cond="";
	if(count($cond_arr['active_status'])!=''){
		$whr_cond .=" and sp1.active_status='yes' and sp1.user_group!=''";
	}
	
	$qry=imw_query("SELECT sp1.id, sp1.proc, sp1.acronym FROM slot_procedures sp1 
	 LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id WHERE sp1.times = '' 
	 AND sp1.proc != '' $whr_cond ORDER BY sp1.proc");
	while($row=imw_fetch_array($qry)){
		$sch_proc_arr[$row['id']]=$row['proc'];
	}
	$ret_sch_proc_arr['proc_name_by_id']=$sch_proc_arr;
	return $ret_sch_proc_arr;
}
	
function enc_charges_fun($cond_arr=array()){
	$other_arr=array("other");
	if(in_array('pos',$cond_arr)){
		$ret_pos_arr=pos_fun($other_arr);
		$pos_fac_arr=$ret_pos_arr['pos_name_by_id'];
	}

	if(in_array('facility',$cond_arr)){
		$ret_fac_arr=facility_fun($other_arr);
		$fac_arr=$ret_fac_arr['fac_name_by_prac'];
	}
	
	if(in_array('cpt',$cond_arr)){
		$ret_cpt_arr=cpt_fun($other_arr);
		$cpt_dept_arr=$ret_cpt_arr['dept_id_by_cpt'];
		$dept_cpt_id_arr=$ret_cpt_arr['cpt_id_by_dept'];
		$cpt_name_arr=$ret_cpt_arr['cpt_name_by_id'];
	}
	
	if(in_array('department',$cond_arr)){
		$ret_dept_arr=department_fun($other_arr);
		$dept_arr=$ret_dept_arr['dept_desc_by_id'];
	}
	
	if(in_array('insurance',$cond_arr)){
		$ret_ins_arr=ins_comp_fun($other_arr);
		$ins_grp_id_arr=$ret_ins_arr['ins_grp_by_id'];
		$ins_id_arr=$ret_ins_arr['ins_name_by_id'];
	}
	
	if(in_array('users',$cond_arr)){
		$ret_users_arr=users_fun($other_arr);
		$users_arr=$ret_users_arr['user_name_by_id'];
	}
	
	

	$whr_arr=$cond_arr['cond'];
	$whr_cond="";
	$whr_date_cond="";
	$trend_dos_cond_arr=array();
	if(isset($whr_arr['date_range_year_trend']) && count($whr_arr['date_range_year_trend'])>0){
		$whr_cond .=" and (";
		foreach($whr_arr['date_range_year_trend'] as $trend_date_key=>$trend_date_val){
			$trend_dos_cond_arr[]="date_of_service like '$trend_date_val%'";
		}
		$whr_cond .=implode(" or ",$trend_dos_cond_arr)." )";
	}
	if($whr_arr['start_date']!=""){
		$start_date_exp=explode('-',$whr_arr['start_date']);
		$db_start_date=$start_date_exp[2].'-'.$start_date_exp[0].'-'.$start_date_exp[1];
		if($whr_arr['date_range_for']=="date_of_service"){
			$whr_date_cond .=" and date_of_service>='$db_start_date'";
		}else if($whr_arr['date_range_for']=="date_of_payment" || $whr_arr['date_range_for']=="transaction_date"){
			$whr_date_cond .=" and first_posted_date>='$db_start_date'";
		}
	}
	if($whr_arr['end_date']!=""){
		$end_date_exp=explode('-',$whr_arr['end_date']);
		$db_end_date=$end_date_exp[2].'-'.$end_date_exp[0].'-'.$end_date_exp[1];
		if($whr_arr['date_range_for']=="date_of_service"){
			$whr_date_cond .=" and date_of_service<='$db_end_date'";
		}else if($whr_arr['date_range_for']=="date_of_payment" || $whr_arr['date_range_for']=="transaction_date"){
			$whr_date_cond .=" and first_posted_date<='$db_end_date'";
		}
	}
	if($whr_arr['date_range_for']=="date_of_payment" || $whr_arr['date_range_for']=="transaction_date"){
		//$whr_cond .=" and submitted='true'";
	}
	$db_ins=$db_pos_fac=$db_cpt=$db_users="";
	if(isset($whr_arr['ins_drop'])){$db_ins=implode("','",$whr_arr['ins_drop']);}
	if(isset($whr_arr['pos_fac_drop'])){$db_pos_fac=implode("','",$whr_arr['pos_fac_drop']);}
	if(isset($whr_arr['cpt_drop'])){$db_cpt=implode("','",$whr_arr['cpt_drop']);}
	if(isset($whr_arr['users_drop'])){$db_users=implode("','",$whr_arr['users_drop']);}
	
	if($db_ins!=""){
		$whr_cond .=" and (patient_charge_list.primaryInsuranceCoId in('$db_ins')
							or patient_charge_list.secondaryInsuranceCoId in ('$db_ins')
							or patient_charge_list.tertiaryInsuranceCoId in ('$db_ins'))";
	}
	if($db_pos_fac!=""){
		$whr_cond .=" and patient_charge_list_details.posFacilityId in('$db_pos_fac')";
	}
	if($db_cpt!=""){
		$whr_cond .=" and patient_charge_list_details.procCode in('$db_cpt')";
	}
	if($db_users!=""){
		$whr_cond .=" and patient_charge_list.primary_provider_id_for_reports in('$db_users')";
	}
	//$whr_cond .=" and patient_charge_list.primary_provider_id_for_reports in(181)";
	
	if(isset($whr_arr['dept_drop']) && count($whr_arr['dept_drop'])>0){
		$db_cpt_by_dept_str="";
		foreach($whr_arr['dept_drop'] as $dept_key=>$dept_val){
			if($dept_cpt_id_arr[$dept_val]!=""){
				$db_cpt_by_dept_str.=implode(',',$dept_cpt_id_arr[$dept_val]);
			}
		}
		if($db_cpt_by_dept_str!=""){
			$db_cpt_by_dept_arr=explode(',',$db_cpt_by_dept_str);
			$db_cpt_by_dept=implode("','",$db_cpt_by_dept_arr);
			$whr_cond .=" and procCode in('$db_cpt_by_dept')";
		}
	}
	$whr_cond .=" and patient_charge_list.primary_provider_id_for_reports>0";
	
	if($whr_arr['date_range_for']=="date_of_payment" || $whr_arr['date_range_for']=="transaction_date"){
		//$whr_chld_del_cond =" ((patient_charge_list_details.del_status='0') OR (patient_charge_list_details.del_status='1' AND patient_charge_list_details.trans_del_date> '$db_end_date')) ";
		$whr_chld_del_cond=" ((patient_charge_list_details.del_status='1' AND (DATE_FORMAT(patient_charge_list_details.trans_del_date,'%Y-%m-%d') BETWEEN '$db_start_date' AND '$db_end_date')) or ((patient_charge_list_details.del_status>=0) $whr_date_cond))";
	}else{
		$whr_chld_del_cond =" patient_charge_list_details.del_status='0' $whr_date_cond";
	}
	$qry="select patient_charge_list.encounter_id,patient_charge_list_details.totalAmount,
					 patient_charge_list_details.place_of_service,patient_charge_list_details.posFacilityId,
					 patient_charge_list_details.procCode,patient_data.default_facility,patient_charge_list.primaryProviderId,
					 patient_charge_list.primaryInsuranceCoId,patient_charge_list.secondaryInsuranceCoId,patient_charge_list.tertiaryInsuranceCoId,
					 patient_charge_list.date_of_service,patient_charge_list_details.charge_list_detail_id,patient_charge_list_details.pat_due,
					 patient_charge_list_details.posted_status,patient_charge_list_details.del_status,patient_charge_list.first_posted_date,
					 DATE_FORMAT(patient_charge_list_details.trans_del_date,'%Y-%m-%d') as trans_del_date,patient_charge_list.primary_provider_id_for_reports 
					 from patient_charge_list join patient_charge_list_details on 
					 patient_charge_list_details.charge_list_id=patient_charge_list.charge_list_id 
					 join patient_data on patient_data.id=patient_charge_list.patient_id
					 where patient_charge_list_details.procCode>0 and $whr_chld_del_cond $whr_cond group by patient_charge_list_details.charge_list_detail_id
					 order by patient_charge_list.date_of_service asc";
	$qry_run=imw_query($qry);
	$chk_tot_chrg_arr=array();
	while($row=imw_fetch_array($qry_run)){
		$show_del_pay="1";
		if($whr_arr['date_range_for']!="date_of_service" && $row['del_status']==1 
		&& ($row['first_posted_date']>=$db_start_date && $row['first_posted_date']<=$db_end_date) 
		&& ($row['trans_del_date']>=$db_start_date && $row['trans_del_date']<=$db_end_date)){
			$show_del_pay="0";
			$chk_tot_chrg_arr[$row['charge_list_detail_id']][0]=0;
		}
		if(($row['trans_del_date']<$row['first_posted_date'] || $row['first_posted_date']=='0000-00-00') && $whr_arr['date_range_for']!="date_of_service" && $row['del_status']==1){
			$show_del_pay="0";
		}
		if($show_del_pay==1){
			if($whr_arr['date_range_for']!="date_of_service" && $row['del_status']==1 && ($row['trans_del_date']>=$db_start_date && $row['trans_del_date']<=$db_end_date)){
				$row['totalAmount']='-'.$row['totalAmount'];
				$chk_tot_chrg_arr[$row['charge_list_detail_id']][1]=$row['totalAmount'];
			}else{
				$chk_tot_chrg_arr[$row['charge_list_detail_id']][2]=$row['totalAmount'];
			}
			$pos_fac_name=$pos_fac_arr[$row['posFacilityId']];
			$pos_fac_org_name=$pos_fac_arr[$row['default_facility']];
			$dept_id=$cpt_dept_arr[$row['procCode']];
			$cpt_name=$cpt_name_arr[$row['procCode']];
			$chl_dos=$row['date_of_service'];
			$chl_user_name=$users_arr[$row['primary_provider_id_for_reports']];
			if($dept_id==""){
				$dept_id=0;
			}
			$dept_name=$dept_arr[$dept_id];
			if($pos_fac_name==""){
				$pos_fac_name="Other";
			}
			if($pos_fac_org_name==""){
				$pos_fac_org_name="Other";
			}
			
			$tot_charges_fac_arr[]=$row['totalAmount'];
			
			$charges_fac_org_arr[$pos_fac_org_name][]=$row['totalAmount'];
			$tot_enc_fac_org_arr[$row['encounter_id']]=$pos_fac_org_name;
			
			$charges_fac_arr[$pos_fac_name][]=$row['totalAmount'];
			$tot_enc_fac_arr[$row['encounter_id']]=$pos_fac_name;
			
			$charges_dept_arr[$dept_id][]=$row['totalAmount'];
			$tot_enc_dept_arr[$row['encounter_id']]=$dept_id;
			
			$charges_phy_arr[$row['primary_provider_id_for_reports']][]=$row['totalAmount'];
			
			$tot_chrg_enc_arr[$row['encounter_id']]=$row['encounter_id'];
			$tot_chrg_chld_arr[$row['charge_list_detail_id']]=$row['charge_list_detail_id'];
			
			$cpt_charges_arr[$cpt_name][]=$row['totalAmount'];
			
			$tot_enc_phy_arr[$row['encounter_id']]=$row['primary_provider_id_for_reports'];
			
			$enc_dos_arr[$row['encounter_id']]=$row['date_of_service'];
			$enc_fpd_arr[$row['encounter_id']]=$row['first_posted_date'];
			
			$chld_proc_arr[$row['charge_list_detail_id']]=$cpt_name;
			
			$tot_enc_usr_arr[$row['encounter_id']]=$chl_user_name;
			
			$enc_ins_id=$row['primaryInsuranceCoId'];
			if(count($whr_arr['ins_drop'])>0 && $row['secondaryInsuranceCoId']>0 && in_array($row['secondaryInsuranceCoId'],$whr_arr['ins_drop'])){
				$enc_ins_id=$row['secondaryInsuranceCoId'];
			}else if(count($whr_arr['ins_drop'])>0 && $row['tertiaryInsuranceCoId']>0 && in_array($row['tertiaryInsuranceCoId'],$whr_arr['ins_drop'])){
				$enc_ins_id=$row['tertiaryInsuranceCoId'];
			}
			$ins_grp_id_show=0;
			if($ins_grp_id_arr[$enc_ins_id]>0){
				$ins_grp_id_show=$ins_grp_id_arr[$enc_ins_id];
			}
			$ins_grp_charges_arr[$ins_grp_id_show][]=$row['totalAmount'];
			$ins_charges_arr[$enc_ins_id][]=$row['totalAmount'];
			
			if($whr_arr['date_range_for']=="date_of_payment" || $whr_arr['date_range_for']=="transaction_date"){
				$chl_dos_exp=explode('-',$row['first_posted_date']);
			}else{
				$chl_dos_exp=explode('-',$chl_dos);
			}
			//$chl_dos_month=date('M',mktime(0, 0, 0, $chl_dos_exp[1], $chl_dos_exp[2], $chl_dos_exp[0]));
			$tot_charges_date_arr[$chl_dos_exp[0]][$chl_dos_exp[1]][]=$row['totalAmount'];
			
			$tot_charges_user_arr[$chl_user_name][]=$row['totalAmount'];
			
			$tot_pat_due_arr[$row['charge_list_detail_id']]=$row['pat_due'];
			
			if($chl_dos_exp[1]>9){
				$quarter_val="3";
			}else if($chl_dos_exp[1]>6){
				$quarter_val="2";
			}else if($chl_dos_exp[1]>3){
				$quarter_val="1";
			}else{
				$quarter_val="0";
			}
			
			$charges_paid_by_quarter_dept[$dept_name][$quarter_val][]=$row['totalAmount'];
		}
	}

	$k=0;
	foreach($pos_fac_arr as $key=>$val){
		$charges_fac_org_tot_arr[$k]["kee"]=$val;
		$charges_fac_org_tot_arr[$k]["val"]=array_sum($charges_fac_org_arr[$val]);
		$k++;
	}
	
	$k=0;
	foreach($pos_fac_arr as $key=>$val){
		$charges_fac_tot_arr[$k]["kee"]=$val;
		$charges_fac_tot_arr[$k]["val"]=array_sum($charges_fac_arr[$val]);
		$charges_fac_tot_detail_arr[$key]=array_sum($charges_fac_arr[$val]);
		$k++;
	}

	$k=0;
	foreach($dept_arr as $key=>$val){
		$charges_dept_tot_arr[$k]["kee"]=$val;
		$charges_dept_tot_arr[$k]["val"]=array_sum($charges_dept_arr[$key]);
		$charges_dept_tot_detail_arr[$key]=array_sum($charges_dept_arr[$key]);
		$k++;
	}
	
	$k=0;
	$charges_cpt_name_arr=array();
	foreach($cpt_name_arr as $key=>$val){
		if(array_sum($cpt_charges_arr[$val])!=0 && !in_array($val,$charges_cpt_name_arr)){
			$charges_cpt_name_arr[]=$val;
			$charges_cpt_tot_arr[$k]["kee"]=$val;
			$charges_cpt_tot_arr[$k]["val"]=array_sum($cpt_charges_arr[$val]);
			$charges_cpt_tot_val_arr["val"][$k]=array_sum($cpt_charges_arr[$val]);
			$k++;
		}
	}
	arsort($charges_cpt_tot_val_arr["val"]);

	$k=0;
	foreach($charges_cpt_tot_val_arr["val"] as $key=>$val){
		if(array_sum($charges_cpt_tot_arr[$key])!=0 && $k<10){
			$charges_top_cpt_tot_arr[$k]["kee"]=$charges_cpt_tot_arr[$key]["kee"];
			$charges_top_cpt_tot_arr[$k]["val"]=$charges_cpt_tot_arr[$key]["val"];
			$k++;
		}
	}
	
	$k=0;
	foreach($users_arr as $key=>$val){
		$charges_phy_tot_detail_arr[$key]=array_sum($tot_charges_user_arr[$val]);
		$k++;
	}
	
	$sel_grp=imw_query("select * from ins_comp_groups where delete_status='0'");
	while($row_grp=imw_fetch_array($sel_grp)){
		$ins_grp_arr[$row_grp['id']]=str_replace("'",'',$row_grp['title']);
	}
	$ins_grp_arr[0]="Patient";
	
	$k=0;
	foreach($ins_grp_arr as $key=>$val){
		$charges_ins_grp_tot_arr[$k]["kee"]=$val;
		$charges_ins_grp_tot_arr[$k]["val"]=array_sum($ins_grp_charges_arr[$key]);
		$k++;
	}
	
	$k=0;
	foreach($ins_id_arr as $key=>$val){
		if(array_sum($ins_charges_arr[$key])!=0){
			$charges_ins_tot_arr[$k]["kee"]=$val;
			$charges_ins_tot_arr[$k]["val"]=array_sum($ins_charges_arr[$key]);
			$k++;
		}
	}
	
	//print_r($charges_ins_tot_arr);
	ksort($tot_charges_date_arr);
	foreach($tot_charges_date_arr as $key=>$val){
		if($whr_arr['date_range_trend']=="monthly"){
			for($day_key=1;$day_key<=12;$day_key++){
				if($day_key<10){
					$day_key='0'.$day_key;
				}
				$key2_val=date('M',mktime(0, 0, 0, $day_key, '1', '2015'));
				$tot_charges_date_tot_arr[$key][$key2_val]=array_sum($tot_charges_date_arr[$key][$day_key]);
			}
		}else{
			$quarter_val_arr=array();
			for($day_key=1;$day_key<=12;$day_key++){
				if($day_key>9){
					$quarter_val="Quarter4";
				}else if($day_key>6){
					$quarter_val="Quarter3";
				}else if($day_key>3){
					$quarter_val="Quarter2";
				}else{
					$quarter_val="Quarter1";
				}
				if($day_key<10){
					$day_key='0'.$day_key;
				}
				$quarter_val_arr[$quarter_val][]=array_sum($tot_charges_date_arr[$key][$day_key]);
			}
			foreach($quarter_val_arr as $qt_key=>$qt_val){
				$tot_charges_date_tot_arr[$key][$qt_key]=array_sum($quarter_val_arr[$qt_key]);
			}
		}
	}
	//print_r($quarter_val_arr);
	//print_r($tot_charges_date_tot_arr);
	
	if(in_array('payment_fun',$cond_arr)){
		
		if($whr_arr['date_range_for']=="date_of_service"){
			$multi_arr['fac_in_enc']=$tot_enc_fac_arr;
			$multi_arr['fac_org_in_enc']=$tot_enc_fac_org_arr;
			$multi_arr['dept_in_enc']=$tot_enc_dept_arr;
			$multi_arr['tot_chrg_enc_arr']=$tot_chrg_enc_arr;
			$multi_arr['tot_chrg_chld_arr']=$tot_chrg_chld_arr;
			$multi_arr['enc_dos_arr']=$enc_dos_arr;
			$multi_arr['enc_fpd_arr']=$enc_fpd_arr;
			$multi_arr['usr_in_enc']=$tot_enc_phy_arr;
			$multi_arr['chld_proc_arr']=$chld_proc_arr;
			$multi_arr['from_charges_fun']='from_charges_fun';
			$cond_pay_arr['cond']['ins_drop']=$whr_arr['ins_drop'];
		}else{
			$cond_pay_arr[]='charges';
			$cond_pay_arr['cond']['pos_fac_drop']=$whr_arr['pos_fac_drop'];
			$cond_pay_arr['cond']['cpt_drop']=$whr_arr['cpt_drop'];
			$cond_pay_arr['cond']['users_drop']=$whr_arr['users_drop'];
			$cond_pay_arr['cond']['ref_drop']=$whr_arr['ref_drop'];
			$cond_pay_arr['cond']['ins_drop']=$whr_arr['ins_drop'];
		}
		$multi_arr['pos_fac_arr']=$pos_fac_arr;
		$multi_arr['dept_arr']=$dept_arr;
		$multi_arr['cpt_dept_arr']=$cpt_dept_arr;
		$multi_arr['ins_grp_id_arr']=$ins_grp_id_arr;
		$multi_arr['cpt_name_arr']=$cpt_name_arr;
		$multi_arr['users_arr']=$users_arr;
		$multi_arr['ins_id_arr']=$ins_id_arr;
		$cond_pay_arr['cond']['date_range_for']=$whr_arr['date_range_for'];
		$cond_pay_arr['cond']['start_date']=$whr_arr['start_date'];
		$cond_pay_arr['cond']['end_date']=$whr_arr['end_date'];
		$cond_pay_arr['cond']['date_range_trend']=$whr_arr['date_range_trend'];
		$pay_ret_arr[]=enc_payment_fun($cond_pay_arr,$multi_arr);
	
		foreach($pay_ret_arr[0] as $pay_key=>$pay_val){
			$return_arr[$pay_key]=$pay_ret_arr[0][$pay_key];
		}
	}
			
	$return_arr['tot_chrg']=$tot_charges_fac_arr;
	$return_arr['fac_org_chrg']=$charges_fac_org_tot_arr;
	$return_arr['fac_chrg']=$charges_fac_tot_arr;
	$return_arr['dept_chrg']=$charges_dept_tot_arr;
	$return_arr['top_cpt_chrg']=$charges_top_cpt_tot_arr;
	$return_arr['all_cpt_chrg']=$charges_cpt_tot_arr;
	$return_arr['dept_detail_chrg']=$charges_dept_tot_detail_arr;
	$return_arr['ins_grp_detail_chrg']=$charges_ins_grp_tot_arr;
	$return_arr['tot_ins_chrg']=$charges_ins_tot_arr;
	$return_arr['year_detail_chrg']=$tot_charges_date_tot_arr;
	$return_arr['fac_detail_chrg']=$charges_fac_tot_detail_arr;
	$return_arr['phy_detail_chrg']=$charges_phy_tot_detail_arr;
	$return_arr['chld_pat_due_arr']=$tot_pat_due_arr;
	$return_arr['tot_chrg_by_quarter_dept']=$charges_paid_by_quarter_dept;
	$return_arr['tot_phy_chg']=$charges_phy_arr;


	return $return_arr;
}

function enc_payment_fun($cond_arr=array(),$multi_arr=array()){
	$other_arr=array("other");
	if(in_array('pos',$cond_arr)){
		$ret_pos_arr=pos_fun($other_arr);
		$pos_fac_arr=$ret_pos_arr['pos_name_by_id'];
	}

	if(in_array('facility',$cond_arr)){
		$ret_fac_arr=facility_fun($other_arr);
		$fac_arr=$ret_fac_arr['fac_name_by_prac'];
	}
	
	if(in_array('cpt',$cond_arr)){
		$ret_cpt_arr=cpt_fun($other_arr);
		$cpt_dept_arr=$ret_cpt_arr['dept_id_by_cpt'];
		$dept_cpt_id_arr=$ret_cpt_arr['cpt_id_by_dept'];
		$cpt_name_arr=$ret_cpt_arr['cpt_name_by_id'];
	}
	
	if(in_array('department',$cond_arr)){
		$ret_dept_arr=department_fun($other_arr);
		$dept_arr=$ret_dept_arr['dept_desc_by_id'];
	}
	
	if(in_array('users',$cond_arr)){
		$ret_users_arr=users_fun($other_arr);
		$users_arr=$ret_users_arr['user_name_by_id'];
	}
	 
	if(in_array('ref_phy',$cond_arr)){
		$ret_ref_arr=ref_phy_fun($other_arr);
		$ref_arr=$ret_ref_arr['ref_name_by_id'];
	}
	
	if(in_array('insurance',$cond_arr)){
		$ret_ins_arr=ins_comp_fun($other_arr);
		$ins_grp_id_arr=$ret_ins_arr['ins_grp_by_id'];
		$ins_id_arr=$ret_ins_arr['ins_name_by_id'];
	}

	$collectionIds_arr=array();
	$collectionIds = get_account_status_id_collections();
	$collectionIds_arr=explode(',',$collectionIds);

	$whr_arr=$cond_arr['cond'];
	$whr_cond="";
	$db_pos_fac=implode("','",$whr_arr['pos_fac_drop']);
	$db_cpt=implode("','",$whr_arr['cpt_drop']);
	$db_users=implode("','",$whr_arr['users_drop']);
	$db_ref=implode("','",$whr_arr['ref_drop']);
	$db_ins=implode("','",$whr_arr['ins_drop']);
	
	if($db_ins!=""){
		$whr_cond .=" and (patient_charge_list.primaryInsuranceCoId in('$db_ins')
							or patient_charge_list.secondaryInsuranceCoId in ('$db_ins')
							or patient_charge_list.tertiaryInsuranceCoId in ('$db_ins'))";
	}
	if($db_pos_fac!=""){
		$whr_cond .=" and posFacilityId in('$db_pos_fac')";
	}
	if($db_cpt!=""){
		$whr_cond .=" and procCode in('$db_cpt')";
	}
	if($db_users!=""){
		$whr_cond .=" and patient_charge_list.primary_provider_id_for_reports in('$db_users')";
	}
	if($db_ref!=""){
		$whr_cond .=" and patient_charge_list.reff_phy_id in('$db_ref')";
	}
	if(count($whr_arr['dept_drop'])>0){
		$db_cpt_by_dept_str="";
		foreach($whr_arr['dept_drop'] as $dept_key=>$dept_val){
			if($dept_cpt_id_arr[$dept_val]!=""){
				$db_cpt_by_dept_str.=implode(',',$dept_cpt_id_arr[$dept_val]);
			}
		}
		if($db_cpt_by_dept_str!=""){
			$db_cpt_by_dept_arr=explode(',',$db_cpt_by_dept_str);
			$db_cpt_by_dept=implode("','",$db_cpt_by_dept_arr);
			$whr_cond .=" and procCode in('$db_cpt_by_dept')";
		}
	}
	if(count($multi_arr)>0){
		if(count($multi_arr['pos_fac_arr'])>0){
			$pos_fac_arr=$multi_arr['pos_fac_arr'];
		}
		if(count($multi_arr['dept_arr'])>0){
			$dept_arr=$multi_arr['dept_arr'];
		}
		if(count($multi_arr['cpt_dept_arr'])>0){
			$cpt_dept_arr=$multi_arr['cpt_dept_arr'];
		}
		if(count($multi_arr['ins_grp_id_arr'])>0){
			$ins_grp_id_arr=$multi_arr['ins_grp_id_arr'];
		}
		if(count($multi_arr['ins_id_arr'])>0){
			$ins_id_arr=$multi_arr['ins_id_arr'];
		}
		if(count($multi_arr['users_arr'])>0){
			$users_arr=$multi_arr['users_arr'];
		}
		if(count($multi_arr['cpt_name_arr'])>0){
			$cpt_name_arr=$multi_arr['cpt_name_arr'];
		}
	}
	
	$def_wrt_chld_arr=array();	
	if(in_array('charges',$cond_arr)){
		$whr_enc_cond="";
		if($whr_arr['date_range_for']=="date_of_service"){
			if($whr_arr['start_date']!=""){
				$start_date_exp=explode('-',$whr_arr['start_date']);
				$db_start_date=$start_date_exp[2].'-'.$start_date_exp[0].'-'.$start_date_exp[1];
				if($whr_arr['date_range_for']=="date_of_service"){
					$whr_cond .=" and date_of_service>='$db_start_date'";
				}
			}
			if($whr_arr['end_date']!=""){
				$end_date_exp=explode('-',$whr_arr['end_date']);
				$db_end_date=$end_date_exp[2].'-'.$end_date_exp[0].'-'.$end_date_exp[1];
				if($whr_arr['date_range_for']=="date_of_service"){
					$whr_cond .=" and date_of_service<='$db_end_date'";
				}
			}
		}else{
			if($whr_arr['start_date']!=""){
				$start_date_exp=explode('-',$whr_arr['start_date']);
				$db_start_date=$start_date_exp[2].'-'.$start_date_exp[0].'-'.$start_date_exp[1];
				if($whr_arr['date_range_for']=="date_of_payment"){
					$whr_enc_cond .=" and payment_date>='$db_start_date'";
				}else if($whr_arr['date_range_for']=="transaction_date"){
					$whr_enc_cond .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')>='$db_start_date'";
				}
			}
			if($whr_arr['end_date']!=""){
				$end_date_exp=explode('-',$whr_arr['end_date']);
				$db_end_date=$end_date_exp[2].'-'.$end_date_exp[0].'-'.$end_date_exp[1];
				if($whr_arr['date_range_for']=="date_of_payment"){
					$whr_enc_cond .=" and payment_date<='$db_end_date'";
				}else if($whr_arr['date_range_for']=="transaction_date"){
					$whr_enc_cond .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')<='$db_end_date'";
				}
			}
			
			$whr_del_cond="del_status='0'";
			if($whr_arr['date_range_for']!="date_of_service"){
				$whr_del_cond=" ((del_status='1' AND (DATE_FORMAT(del_date_time,'%Y-%m-%d') BETWEEN '$db_start_date' AND '$db_end_date')) or ((del_status>=0) $whr_enc_cond))";
			}
			//echo "select encounter_id from account_trans where $whr_del_cond order by payment_date asc";
			$pay_enc_qry=imw_query("select encounter_id from account_trans where $whr_del_cond order by payment_date asc");
			while($pay_enc_row=imw_fetch_array($pay_enc_qry)){
				$pay_enc_arr[$pay_enc_row['encounter_id']]=$pay_enc_row['encounter_id'];
			}
			
			if($whr_arr['date_range_for']!="date_of_service"){
				if($whr_arr['date_range_for']=="date_of_payment"){
					$whr_enc_cond=str_replace('payment_date','write_off_dop',$whr_enc_cond);
					$whr_enc_ord_cond=' ORDER BY encounter_id ASC , charge_list_detail_id ASC , write_off_dop ASC';
				}
				if($whr_arr['date_range_for']=="transaction_date"){
					$whr_enc_cond=str_replace('entered_date','write_off_dot',$whr_enc_cond);
					$whr_enc_ord_cond=' ORDER BY encounter_id ASC , charge_list_detail_id ASC , write_off_dot ASC';
				}
				$whr_del_cond=" ((del_status='1' AND (DATE_FORMAT(del_date,'%Y-%m-%d') BETWEEN '$db_start_date' AND '$db_end_date')) or ((del_status>=0) $whr_enc_cond))";
			}
			
			//echo "select encounter_id,write_off_amount,charge_list_detail_id from defaultwriteoff where $whr_del_cond $whr_enc_ord_cond";
			$wrt_enc_qry=imw_query("select encounter_id,write_off_amount,charge_list_detail_id from defaultwriteoff where $whr_del_cond $whr_enc_ord_cond");
			while($wrt_enc_row=imw_fetch_array($wrt_enc_qry)){
				$pay_enc_arr[$wrt_enc_row['encounter_id']]=$wrt_enc_row['encounter_id'];
				$def_wrt_chld_arr[$wrt_enc_row['charge_list_detail_id']]=$wrt_enc_row['write_off_amount'];
			}
			
			$tot_pay_enc_str=implode("','",$pay_enc_arr);
			$whr_cond .=" and encounter_id in('$tot_pay_enc_str')";
		}
		
		$whr_cond .=" and patient_charge_list.primary_provider_id_for_reports>0";
		
		if($whr_arr['date_range_for']=="date_of_payment" || $whr_arr['date_range_for']=="transaction_date"){
			$whr_chld_del_cond=" ((patient_charge_list_details.del_status='1' AND (DATE_FORMAT(patient_charge_list_details.trans_del_date,'%Y-%m-%d') BETWEEN '$db_start_date' AND '$db_end_date')) or ((patient_charge_list_details.del_status>=0)))";
		}else{
			$whr_chld_del_cond =" patient_charge_list_details.del_status='0' ";
		}
		
		$enc_qry="select patient_charge_list.encounter_id,patient_charge_list_details.totalAmount,patient_charge_list_details.write_off,
					 patient_charge_list.date_of_service,patient_charge_list_details.place_of_service,patient_charge_list_details.posFacilityId,
					 patient_charge_list_details.procCode, patient_data.default_facility,patient_charge_list.primary_provider_id_for_reports,
					 patient_charge_list.reff_phy_id,patient_charge_list.patient_id,patient_charge_list.totalBalance,
					 DATEDIFF(NOW(),patient_charge_list.date_of_service) as last_dos_diff,patient_charge_list_details.charge_list_detail_id,
					 patient_charge_list_details.write_off_dot,patient_charge_list_details.write_off_date,patient_charge_list.first_posted_date,
					 patient_charge_list_details.newBalance,patient_charge_list_details.pri_due,patient_charge_list_details.sec_due,
					 patient_charge_list_details.tri_due,patient_charge_list_details.pat_due,patient_data.pat_account_status
					 from patient_charge_list join patient_charge_list_details on 
					 patient_charge_list_details.charge_list_id=patient_charge_list.charge_list_id 
					 join patient_data on patient_data.id=patient_charge_list.patient_id
					 where patient_charge_list_details.procCode>0 and $whr_chld_del_cond $whr_cond group by patient_charge_list_details.charge_list_detail_id 
					 order by patient_charge_list.date_of_service asc";
		$qry=imw_query($enc_qry);
		while($row=imw_fetch_array($qry)){
			$pos_fac_name=$pos_fac_arr[$row['posFacilityId']];
			$pos_fac_org_name=$pos_fac_arr[$row['default_facility']];
			$dept_id=$cpt_dept_arr[$row['procCode']];
			$cpt_name=$cpt_name_arr[$row['procCode']];
			if($dept_id==""){
				$dept_id=0;
			}
			$dept_name=$dept_arr[$dept_id];
			if($pos_fac_name==""){
				$pos_fac_name="Other";
			}
			if($pos_fac_org_name==""){
				$pos_fac_org_name="Other";
			}
			
			$tot_enc_fac_org_arr[$row['encounter_id']]=$pos_fac_org_name;
			$tot_enc_fac_arr[$row['encounter_id']]=$pos_fac_name;
			$tot_enc_dept_arr[$row['encounter_id']]=$dept_id;
			
			
			$tot_enc_phy_arr[$row['encounter_id']]=$row['primary_provider_id_for_reports'];
			
			$charges_phy_arr[$row['primary_provider_id_for_reports']][]=$row['totalAmount'];
			
			$tot_chrg_enc_arr[$row['encounter_id']]=$row['encounter_id'];
			$tot_chrg_chld_arr[$row['charge_list_detail_id']]=$row['charge_list_detail_id'];
			$tot_enc_ref_phy_arr[$row['encounter_id']]=$row['reff_phy_id'];
			$charges_ref_phy_arr[$row['reff_phy_id']][]=$row['totalAmount'];
			$tot_chrg_ref_enc_arr[$row['encounter_id']][$row['reff_phy_id']][]=$row['totalAmount'];
			
			$tot_chld_proc_arr[$row['charge_list_detail_id']]=$cpt_name;
			
			$arrAllPatients[$row['patient_id']]=$row['patient_id'];
			
			if($whr_arr['date_range_for']!="date_of_service"){
				$payment_wrt_by_arr[]=$def_wrt_chld_arr[$row['charge_list_detail_id']];
				$payment_wrt_dept_arr[$dept_id][]=$def_wrt_chld_arr[$row['charge_list_detail_id']];
				$spl_wrt_enc_arr[$row['encounter_id']][]=$def_wrt_chld_arr[$row['charge_list_detail_id']];
				$payment_wrt_phy_arr[$row['primary_provider_id_for_reports']][]=$def_wrt_chld_arr[$row['charge_list_detail_id']];
				$payment_wrt_ref_by_arr['write off'][]=$def_wrt_chld_arr[$row['charge_list_detail_id']];
			}else{
				$payment_wrt_by_arr[]=$row['write_off'];
				$payment_wrt_dept_arr[$dept_id][]=$row['write_off'];
				$spl_wrt_enc_arr[$row['encounter_id']][]=$row['write_off'];
				$payment_wrt_phy_arr[$row['primary_provider_id_for_reports']][]=$row['write_off'];
				$payment_wrt_ref_by_arr['write off'][]=$row['write_off'];
			}
			if($row['last_dos_diff']<=30){
				$last_dos_diff_show=0;
			}else if($row['last_dos_diff']>30 && $row['last_dos_diff']<=60){
				$last_dos_diff_show=1;
			}else if($row['last_dos_diff']>60 && $row['last_dos_diff']<=90){
				$last_dos_diff_show=2;
			}else if($row['last_dos_diff']>90 && $row['last_dos_diff']<=120){
				$last_dos_diff_show=3;
			}else if($row['last_dos_diff']>120 && $row['last_dos_diff']<=150){
				$last_dos_diff_show=4;
			}else if($row['last_dos_diff']>150 && $row['last_dos_diff']<=180){
				$last_dos_diff_show=5;
			}else{
				$last_dos_diff_show=6;
			}
			
			if($row['totalBalance']>0 && !in_array($row['pat_account_status'],$collectionIds_arr)){
				$tot_chrg_phy_dos_arr[$row['primary_provider_id_for_reports']][$last_dos_diff_show][]=$row['pri_due']+$row['sec_due']+$row['tri_due']+$row['pat_due'];
			}
			if($whr_arr['date_range_for']!="date_of_service"){
				$date_of_service_exp=explode('-',$row['first_posted_date']);
			}else{
				$date_of_service_exp=explode('-',$row['date_of_service']);
			}
			if($date_of_service_exp[1]>9){
				$quarter_val="3";
			}else if($date_of_service_exp[1]>6){
				$quarter_val="2";
			}else if($date_of_service_exp[1]>3){
				$quarter_val="1";
			}else{
				$quarter_val="0";
			}
			
			$charges_paid_by_quarter_phy[$row['primary_provider_id_for_reports']][$quarter_val][]=$row['totalAmount'];
			$charges_paid_by_quarter_dept[$dept_name][$quarter_val][]=$row['totalAmount'];
		}
	}else{
		if(count($multi_arr['tot_chrg_chld_arr'])>0){
			$tot_chrg_chld=implode("','",$multi_arr['tot_chrg_chld_arr']);
		}
		$enc_qry="select patient_charge_list.encounter_id,patient_charge_list_details.write_off,
					patient_charge_list.primary_provider_id_for_reports,patient_charge_list_details.procCode
					 from patient_charge_list join patient_charge_list_details on 
					 patient_charge_list_details.charge_list_id=patient_charge_list.charge_list_id 
					 join patient_data on patient_data.id=patient_charge_list.patient_id
					 where patient_charge_list_details.charge_list_detail_id in('$tot_chrg_chld') 
					 group by patient_charge_list_details.charge_list_detail_id 
					 order by patient_charge_list.date_of_service asc";
		$qry=imw_query($enc_qry);
		while($row=imw_fetch_array($qry)){
			if($whr_arr['date_range_for']=="date_of_service"){
				$dept_id=$cpt_dept_arr[$row['procCode']];
				if($dept_id==""){
					$dept_id=0;
				}
				$payment_wrt_by_arr[]=$row['write_off'];
				$payment_wrt_dept_arr[$dept_id][]=$row['write_off'];
				$spl_wrt_enc_arr[$row['encounter_id']][]=$row['write_off'];
				$payment_wrt_phy_arr[$row['primary_provider_id_for_reports']][]=$row['write_off'];
				$payment_wrt_ref_by_arr['write off'][]=$row['write_off'];
			}
		}
	}

	//LOGIC: IF INITIAL SELECTED THEN FETCH ENCOUNTER OF PATIENT IF THAT IS FIRST ENCOUNTER OF THAT PATIENT
	if($whr_arr['all_initial_enc'] == "initial_enc"){
		
		//GET FIRST ENCOUNTER OF EVERY PATIENT
		if(sizeof($arrAllPatients)>0){
			$strAllPatients=implode(',', $arrAllPatients);
			$qry="Select patient_id, encounter_id, date_of_service FROM patient_charge_list WHERE patient_id IN(".$strAllPatients.")";
			$qry.=" ORDER BY date_of_service DESC, charge_list_id DESC";
			
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$arrInitialPatDos[$res['patient_id']]=$res['date_of_service'];
				$arrInitialPatEnc[$res['patient_id']]=$res['encounter_id'];
			}
		}
		foreach($arrInitialPatEnc as $key=>$val){
			if(($arrInitialPatDos[$key]>=$db_start_date && $arrInitialPatDos[$key]<=$db_end_date)){
				if($tot_chrg_enc_arr[$val]!=0){
					$new_tot_chrg_enc_arr[$val]=$tot_chrg_enc_arr[$val];
					foreach($tot_chrg_ref_enc_arr[$val] as $key2=>$val2){
						foreach($tot_chrg_ref_enc_arr[$val][$key2] as $key3=>$val3){
							$new_charges_ref_phy_arr[$key2][]=$val3;
						}
					}
					
				}
			
			}
		}
		$tot_chrg_enc_arr=array();
		$tot_chrg_enc_arr=array();
		$tot_chrg_enc_arr=$new_tot_chrg_enc_arr;
		$charges_ref_phy_arr=$new_charges_ref_phy_arr;
	}
		//print_r($tot_chrg_ref_enc_arr);
	
	$whr_cond="";
	$whr_cond_pre="";
	$whr_cond_final="";
	if($whr_arr['date_range_for']!="date_of_service"){
		if($whr_arr['start_date']!=""){
			$start_date_exp=explode('-',$whr_arr['start_date']);
			$db_start_date=$start_date_exp[2].'-'.$start_date_exp[0].'-'.$start_date_exp[1];
			if($whr_arr['date_range_for']=="date_of_payment"){
				$whr_cond .=" and payment_date>='$db_start_date'";
				$whr_cond_pre .=" and paid_date>='$db_start_date'";
				$whr_cond_final .=" and payment_date>='$db_start_date'";
			}else if($whr_arr['date_range_for']=="transaction_date"){
				$whr_cond .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')>='$db_start_date'";
				$whr_cond_pre .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')>='$db_start_date'";
				$whr_cond_final .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')>='$db_start_date'";
			}
		}
		if($whr_arr['end_date']!=""){
			$end_date_exp=explode('-',$whr_arr['end_date']);
			$db_end_date=$end_date_exp[2].'-'.$end_date_exp[0].'-'.$end_date_exp[1];
			if($whr_arr['date_range_for']=="date_of_payment"){
				$whr_cond .=" and payment_date<='$db_end_date'";
				$whr_cond_pre .=" and paid_date<='$db_end_date'";
				$whr_cond_final .=" and payment_date<='$db_end_date'";
			}else if($whr_arr['date_range_for']=="transaction_date"){
				$whr_cond .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')<='$db_end_date'";
				$whr_cond_pre .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')<='$db_end_date'";
				$whr_cond_final .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')<='$db_end_date'";
			}
		}
	}else{
		if($whr_arr['start_date']!=""){
			$start_date_exp=explode('-',$whr_arr['start_date']);
			$db_start_date=$start_date_exp[2].'-'.$start_date_exp[0].'-'.$start_date_exp[1];
			$whr_cond_pre .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')>='$db_start_date'";
		}
		if($whr_arr['end_date']!=""){
			$end_date_exp=explode('-',$whr_arr['end_date']);
			$db_end_date=$end_date_exp[2].'-'.$end_date_exp[0].'-'.$end_date_exp[1];
			$whr_cond_pre .=" and DATE_FORMAT(entered_date, '%Y-%m-%d')<='$db_end_date'";
		}
	}
	
	$whr_ins_cond_final="";
	$db_ins=implode("','",$whr_arr['ins_drop']);
	if($db_ins!=""){
		$whr_cond .=" and ins_id in('$db_ins')";
		$whr_ins_cond_final .=" and ins_id in('$db_ins')";
	}
	
	if(count($multi_arr)>0){
		if(count($multi_arr['fac_in_enc'])>0){
			$tot_enc_fac_arr=$multi_arr['fac_in_enc'];
		}
		if(count($multi_arr['fac_org_in_enc'])>0){
			$tot_enc_fac_org_arr=$multi_arr['fac_org_in_enc'];
		}
		if(count($multi_arr['dept_in_enc'])>0){
			$tot_enc_dept_arr=$multi_arr['dept_in_enc'];
		}
		if(count($multi_arr['enc_dos_arr'])>0){
			$tot_enc_dos_arr=$multi_arr['enc_dos_arr'];
		}
		if(count($multi_arr['enc_fpd_arr'])>0){
			$tot_enc_fpd_arr=$multi_arr['enc_fpd_arr'];
		}
		if(count($multi_arr['chld_proc_arr'])>0){
			$tot_chld_proc_arr=$multi_arr['chld_proc_arr'];
		}
		if(count($multi_arr['usr_in_enc'])>0){
			$tot_enc_phy_arr=$multi_arr['usr_in_enc'];
		}
	}
	
	if(in_array('charges',$cond_arr)){
		$tot_chrg_enc=implode("','",$tot_chrg_enc_arr);
		$whr_cond .=" and encounter_id in('$tot_chrg_enc')";
		
		if(count($tot_chrg_chld_arr)>0){
			$tot_chrg_chld=implode("','",$tot_chrg_chld_arr);
			$whr_cond .=" and (charge_list_detail_id in('$tot_chrg_chld') or copay_chld_id in('$tot_chrg_chld'))";
		}
	}else{
		if(in_array('from_charges_fun',$multi_arr)){
			$tot_chrg_enc=implode("','",$multi_arr['tot_chrg_enc_arr']);
			$whr_cond .=" and encounter_id in('$tot_chrg_enc')";
			
			if(count($multi_arr['tot_chrg_chld_arr'])>0){
				$tot_chrg_chld=implode("','",$multi_arr['tot_chrg_chld_arr']);
				$whr_cond .=" and (charge_list_detail_id in('$tot_chrg_chld') or copay_chld_id in('$tot_chrg_chld'))";
			}
		}
	}
	if(in_array('pt_pre_payments',$cond_arr)){
		$un_post_pre_amt_arr=array();
		$depo_qry = "select paid_amount,id,id as pt_pre_patients_id,payment_mode,credit_card_co,provider_id,facility_id from 
		patient_pre_payment where del_status = '0'
		and apply_payment_type!='manually' $whr_cond_pre order by entered_date desc";
		$depo_mysql = imw_query($depo_qry);
		while($depo_fet=imw_fetch_array($depo_mysql)){
			$un_post_pre_amt_arr[$depo_fet['pt_pre_patients_id']]= $depo_fet['paid_amount'];
			$pt_pre_patients_id_arr[$depo_fet['pt_pre_patients_id']]= $depo_fet['pt_pre_patients_id'];
			$un_post_pre_amt_method_arr[$depo_fet['pt_pre_patients_id']]= $depo_fet['payment_mode'];
			$un_post_pre_amt_cc_method_arr[$depo_fet['pt_pre_patients_id']]= $depo_fet['credit_card_co'];
			$un_post_pre_amt_provider_id_arr[$depo_fet['pt_pre_patients_id']]= $depo_fet['provider_id'];
			$un_post_pre_amt_facility_id_arr[$depo_fet['pt_pre_patients_id']]= $depo_fet['facility_id'];
		}
		
		$final_un_post_pre_amt_arr=array();
		if(count($pt_pre_patients_id_arr)>0){
			$pt_pre_patients_id="";
			$pt_pre_patients_id=implode(',',$pt_pre_patients_id_arr);
			$pre_amt_qry=imw_query("SELECT patient_charges_detail_payment_info.paidForProc,patient_charges_detail_payment_info.overPayment,patient_charges_detail_payment_info.patient_pre_payment_id
							FROM  patient_chargesheet_payment_info 
							JOIN patient_charges_detail_payment_info on patient_charges_detail_payment_info.payment_id=patient_chargesheet_payment_info.payment_id 
							WHERE 
							patient_charges_detail_payment_info.deletePayment='0' 
							and patient_charges_detail_payment_info.patient_pre_payment_id>0
							and  patient_charges_detail_payment_info.patient_pre_payment_id in($pt_pre_patients_id)
							and patient_chargesheet_payment_info.unapply='0'
							ORDER BY patient_chargesheet_payment_info.date_of_payment");
			while($pre_amt_fet=imw_fetch_array($pre_amt_qry)){
				$apply_post_pre_amt_arr[$pre_amt_fet['patient_pre_payment_id']][]=$pre_amt_fet['paidForProc']+$pre_amt_fet['overPayment'];
			}
			$final_un_post_pre_amt_str="";
			$ppp_un_post_amount="";
			foreach($pt_pre_patients_id_arr as $pre_key => $pre_val){
				$final_un_post_pre_amt="";
				$apply_post_pre_amt_str=array_sum($apply_post_pre_amt_arr[$pre_val])+array_sum($pmt_past_payment_chk_arr[$pre_val]);
				$final_un_post_pre_amt=$un_post_pre_amt_arr[$pre_val]-$apply_post_pre_amt_str;
				if($final_un_post_pre_amt!=0){
					$final_un_post_pre_amt_arr[]=$final_un_post_pre_amt;
					$payment_method_arr[$un_post_pre_amt_method_arr[$pre_val]][]=$final_un_post_pre_amt;
					$payment_cc_method_arr[$un_post_pre_amt_method_arr[$pre_val]][$un_post_pre_amt_cc_method_arr[$pre_val]][]=$final_un_post_pre_amt;
					$payment_phy_arr[$un_post_pre_amt_provider_id_arr[$pre_val]][]=$final_un_post_pre_amt;
				}
			}
		}
	}
	$whr_del_cond="del_status='0' ";
	if($whr_arr['date_range_for']!="date_of_service"){
		//$whr_del_cond=" (del_status='1' AND (del_date_time BETWEEN '$db_start_date' AND '$db_end_date')) or del_status='0' ";
		$whr_del_cond=" ((del_status='1' AND (DATE_FORMAT(del_date_time,'%Y-%m-%d') BETWEEN '$db_start_date' AND '$db_end_date')) or ((del_status>=0) $whr_cond_final))";
	}
	$tot_chrg_enc_in=explode(',',str_replace("'","",$tot_chrg_enc));
	$tot_chrg_chld_in=explode(',',str_replace("'","",$tot_chrg_chld));
	$tot_chrg_enc_in_comb=array_combine($tot_chrg_enc_in,$tot_chrg_enc_in);
	$tot_chrg_chld_in_comb=array_combine($tot_chrg_chld_in,$tot_chrg_chld_in);
	//print_r($tot_chrg_enc_in_comb);
	//echo "select encounter_id,payment_amount,payment_type,payment_method,payment_by,payment_date,ins_id,copay_chld_id,charge_list_detail_id,cc_type,del_date_time,del_status,DATE_FORMAT(entered_date, '%Y-%m-%d') as entered_date_tran from account_trans where $whr_del_cond $whr_ins_cond_final order by payment_date asc";
	$pay_qry=imw_query("select encounter_id,payment_amount,payment_type,payment_method,payment_by,payment_date,ins_id,copay_chld_id,charge_list_detail_id,cc_type,DATE_FORMAT(del_date_time,'%Y-%m-%d') as del_date_time_tran,del_status,DATE_FORMAT(entered_date, '%Y-%m-%d') as entered_date_tran from account_trans where $whr_del_cond $whr_ins_cond_final order by payment_date asc");
	while($pay_row=imw_fetch_array($pay_qry)){
		$show_del_pay="1";
		if($whr_arr['date_range_for']=="date_of_payment" && $pay_row['del_status']==1 && $pay_row['payment_date']>=$db_start_date && $pay_row['payment_date']<=$db_end_date && $pay_row['del_date_time_tran']>=$db_start_date && $pay_row['del_date_time_tran']<=$db_end_date){
			$show_del_pay="0";
		}
		if($whr_arr['date_range_for']=="transaction_date" && $pay_row['del_status']==1 && $pay_row['entered_date_tran']>=$db_start_date && $pay_row['entered_date_tran']<=$db_end_date && $pay_row['del_date_time_tran']>=$db_start_date && $pay_row['del_date_time_tran']<=$db_end_date){
			$show_del_pay="0";
		}
		if($tot_chrg_enc_in_comb[$pay_row['encounter_id']] && $show_del_pay==1){
			if($tot_chrg_chld_in_comb[$pay_row['charge_list_detail_id']] || $tot_chrg_chld_in_comb[$pay_row['copay_chld_id']] || $tot_chrg_chld==""){
				$pos_fac_org_name=$tot_enc_fac_org_arr[$pay_row['encounter_id']];
				$pos_fac_name=$tot_enc_fac_arr[$pay_row['encounter_id']];
				$dept_id=$tot_enc_dept_arr[$pay_row['encounter_id']];
				$phy_id=$tot_enc_phy_arr[$pay_row['encounter_id']];	
				$ref_phy_id=$tot_enc_ref_phy_arr[$pay_row['encounter_id']];		
				$enc_dos=$tot_enc_dos_arr[$pay_row['encounter_id']];
				$enc_fpd=$tot_enc_fpd_arr[$pay_row['encounter_id']];
				$dept_name=$dept_arr[$dept_id];
				$pay_ins_comp_name=$ins_id_arr[$pay_row['ins_id']];
				if($pay_row['copay_chld_id']>0){
					$chld_cpt=$tot_chld_proc_arr[$pay_row['copay_chld_id']];	
				}else{
					$chld_cpt=$tot_chld_proc_arr[$pay_row['charge_list_detail_id']];	
				}
				
				if($chld_cpt==""){
					$chld_cpt="Other";
				}
				
				$payment_method=$pay_row['payment_method'];
				if($payment_method==""){
					$payment_method="Cash";
				}
				$payment_by=$pay_row['payment_by'];
				if($payment_by==""){
					$payment_by="Patient";
				}
				$payment_type=strtolower($pay_row['payment_type']);
				
				$payment_date_exp=explode('-',$pay_row['payment_date']);
			
				if($whr_arr['date_range_for']=="date_of_payment" || $whr_arr['date_range_for']=="transaction_date"){
					$chl_dos_exp=explode('-',$enc_fpd);
				}else{
					$chl_dos_exp=explode('-',$enc_dos);
				}
				//$chl_dos_month=date('M',mktime(0, 0, 0, $chl_dos_exp[1], $chl_dos_exp[2], $chl_dos_exp[0]));
		
				if($payment_type=='deposit' || $payment_type=='interest payment' || $payment_type=='paid' || $payment_type=='debit' || $payment_type=='credit' || $payment_type=='negative payment' || $payment_type=='copay'){
					
					if($payment_type=='debit' || $payment_type=='negative payment'){
						$payment_amount='-'.$pay_row['payment_amount'];
						if($whr_arr['date_range_for']!="date_of_service" && $pay_row['del_status']==1 && ($pay_row['del_date_time_tran']>=$db_start_date && $pay_row['del_date_time_tran']<=$db_end_date)){
							$payment_amount=str_replace('-','',$payment_amount);
						}
					}else{
						$payment_amount=$pay_row['payment_amount'];
						if($whr_arr['date_range_for']!="date_of_service" && $pay_row['del_status']==1 && ($pay_row['del_date_time_tran']>=$db_start_date && $pay_row['del_date_time_tran']<=$db_end_date)){
							$payment_amount='-'.$payment_amount;
						}
					}
					//echo $pay_row['encounter_id'].'--'.$payment_amount.'<br>';
					$payment_fac_org_arr[$pos_fac_org_name][]=$payment_amount;
					
					$payment_fac_arr[$pos_fac_name][]=$payment_amount;
					
					$payment_dept_arr[$dept_id][]=$payment_amount;
					
					$payment_phy_arr[$phy_id][]=$payment_amount;
					
					$payment_ref_phy_arr[$ref_phy_id][]=$payment_amount;
					
					
					$tot_payment_arr[]=$payment_amount;
					
					$payment_method_arr[$payment_method][]=$payment_amount;
					$payment_cc_method_arr[$payment_method][$pay_row['cc_type']][]=$payment_amount;
					
					$payment_paid_by_arr[$payment_by][]=$payment_amount;
		
					if($payment_date_exp[1]>9){
						$quarter_val="3";
					}else if($payment_date_exp[1]>6){
						$quarter_val="2";
					}else if($payment_date_exp[1]>3){
						$quarter_val="1";
					}else{
						$quarter_val="0";
					}
					$payment_paid_by_quarter_fac[$pos_fac_name][$quarter_val][]=$payment_amount;
					$payment_paid_by_quarter_phy[$phy_id][$quarter_val][]=$payment_amount;
					$payment_paid_by_quarter_dept[$dept_name][$quarter_val][]=$payment_amount;
					//$payment_paid_by_quarter[$payment_date_exp[0]][$quarter_val]=$payment_amount;
					
					$ins_grp_id_show=0;
					if($ins_grp_id_arr[$pay_row['ins_id']]>0){
						//echo $pay_row['ins_id'].'<br>';
						$ins_grp_id_show=$ins_grp_id_arr[$pay_row['ins_id']];
					}
					$payment_paid_by_ins_grp[$ins_grp_id_show][]=$payment_amount;
					$payment_paid_by_ins[$pay_ins_comp_name][]=$payment_amount;
					
					$payment_paid_by_dos[$chl_dos_exp[0]][$chl_dos_exp[1]][]=$payment_amount;
					
					$cpt_receipts_arr[$chld_cpt][]=$payment_amount;
					
					$payment_phy_cpt_arr[$phy_id][$chld_cpt][]=$payment_amount;
					
				}else{
					$payment_amount=$pay_row['payment_amount'];
					if($whr_arr['date_range_for']!="date_of_service" && $pay_row['del_status']==1 && ($pay_row['del_date_time_tran']>=$db_start_date && $pay_row['del_date_time_tran']<=$db_end_date)){
						$payment_amount='-'.$payment_amount;
					}
					if($payment_type=='write off' || $payment_type=='discount'){
						$payment_wrt_by_arr[]=$payment_amount;
						$payment_wrt_dept_arr[$dept_id][]=$payment_amount;
						$payment_wrt_phy_arr[$phy_id][]=$payment_amount;
						$payment_wrt_ref_by_arr[$payment_type][]=$payment_amount;
						$spl_wrt_enc_arr[$pay_row['encounter_id']][]=$payment_amount;
					}else{
						if($payment_type=='returned check' || $payment_type=='refund' || $payment_type=='adjustment'){
							$payment_adj_by_arr[]=-$payment_amount;
							$payment_adj_dept_arr[$dept_id][]=-$payment_amount;
							if($payment_type=='refund'){
								$payment_refund_phy_arr[$phy_id][]=$payment_amount;
								$payment_refund_arr[]=$payment_amount;
								$payment_wrt_ref_by_arr[$payment_type][]=$payment_amount;
							}else{
								$payment_wrt_ref_by_arr['Adjustment'][]=-$payment_amount;
								$payment_adj_phy_arr[$phy_id][]=-$payment_amount;
							}
						}else{
							$payment_adj_by_arr[]=$payment_amount;
							$payment_adj_dept_arr[$dept_id][]=$payment_amount;
							$payment_adj_phy_arr[$phy_id][]=$payment_amount;
							$payment_wrt_ref_by_arr['Adjustment'][]=$payment_amount;
						}
					}
					//$payment_fac_arr[$pos_fac_name][]=-$payment_amount;
					//$tot_payment_fac_arr[]=-$payment_amount;
				}
			}
		}
	}
	//echo array_sum($payment_wrt_ref_by_arr['Adjustment'])+(array_sum($payment_wrt_ref_by_arr['write off'])-(array_sum($payment_wrt_ref_by_arr['refund'])))+array_sum($payment_wrt_ref_by_arr['discount']);
	//print_r($spl_wrt_enc_arr);
	$payment_fac_arr['Other'][]=array_sum($final_un_post_pre_amt_arr);
	$payment_dept_arr[0][]=array_sum($final_un_post_pre_amt_arr);
	$payment_paid_by_ins_grp[0][]=array_sum($final_un_post_pre_amt_arr);
	$tot_payment_arr[]=array_sum($final_un_post_pre_amt_arr);
	$k=0;
	foreach($pos_fac_arr as $key=>$val){
		
		$payment_fac_org_tot_arr[$k]["kee"]=$val;
		$payment_fac_org_tot_arr[$k]["val"]=array_sum($payment_fac_org_arr[$val]);
		$k++;
	}
	//print_r($payment_fac_arr);
	$k=0;
	foreach($pos_fac_arr as $key=>$val){
		
		$payment_fac_tot_arr[$k]["kee"]=$val;
		$payment_fac_tot_arr[$k]["val"]=array_sum($payment_fac_arr[$val]);
		$payment_fac_tot_detail_arr[$key]=array_sum($payment_fac_arr[$val]);
		$k++;
	}
	//print_r($payment_fac_tot_arr);
	$k=0;
	foreach($dept_arr as $key=>$val){
		
		$payment_dept_tot_arr[$k]["kee"]=$val;
		$payment_dept_tot_arr[$k]["val"]=array_sum($payment_dept_arr[$key]);
		$payment_dept_tot_detail_arr[$key]=array_sum($payment_dept_arr[$key]);
		$payment_wrt_dept_detail_arr[$key]=array_sum($payment_wrt_dept_arr[$key]);
		$payment_adj_dept_detail_arr[$key]=array_sum($payment_adj_dept_arr[$key]);
		$k++;
	}
	
	$k=0;
	foreach($users_arr as $key=>$val){
		if(array_sum($payment_phy_arr[$key])!=0){
			$payment_phy_tot_arr[$k]["kee"]=$val;
			$payment_phy_tot_arr[$k]["val"]=array_sum($payment_phy_arr[$key]);
			$payment_phy_tot_detail_arr[$key]=array_sum($payment_phy_arr[$key]);
			
			foreach($payment_phy_cpt_arr[$key] as $cpt_key=>$cpt_val){
				$payment_phy_cpt_final_arr[$val][$cpt_key]=array_sum($payment_phy_cpt_arr[$key][$cpt_key]);
			}
			$k++;
		}
	}

	$k=0;
	foreach($ref_arr as $key=>$val){
		if(array_sum($payment_ref_phy_arr[$key])!=0){
			$payment_ref_phy_tot_arr[$k]["kee"]=$val;
			$payment_ref_phy_tot_arr[$k]["val"]=array_sum($payment_ref_phy_arr[$key]);
			$payment_ref_phy_tot_val_arr["val"][$k]=array_sum($payment_ref_phy_arr[$key]);
			$k++;
		}
	}
	
	arsort($payment_ref_phy_tot_val_arr["val"]);

	$k=0;
	foreach($payment_ref_phy_tot_val_arr["val"] as $key=>$val){
		if(array_sum($payment_ref_phy_tot_arr[$key])!=0 && $k<10){
			$payment_top_ref_tot_arr[$k]["kee"]=$payment_ref_phy_tot_arr[$key]["kee"];
			$payment_top_ref_tot_arr[$k]["val"]=$payment_ref_phy_tot_arr[$key]["val"];
			$k++;
		}
	}
	
	$sel_grp=imw_query("select * from ins_comp_groups where delete_status='0'");
	while($row_grp=imw_fetch_array($sel_grp)){
		$ins_grp_arr[$row_grp['id']]=str_replace("'",'',$row_grp['title']);
	}
	$ins_grp_arr[0]="Patient";
	//print_r($payment_paid_by_ins_grp);
	$k=0;
	foreach($ins_grp_arr as $key=>$val){
		$payment_ins_grp_tot_arr[$k]["kee"]=$val;
		$payment_ins_grp_tot_arr[$k]["val"]=array_sum($payment_paid_by_ins_grp[$key]);
		$k++;
	}
	
	$k=0;
	foreach($payment_paid_by_ins as $key=>$val){
		$payment_ins_tot_arr[$k]["kee"]=$key;
		$payment_ins_tot_arr[$k]["val"]=array_sum($payment_paid_by_ins[$key]);
		$k++;
	}
	
	
	
	ksort($payment_paid_by_dos);
	foreach($payment_paid_by_dos as $key=>$val){
		if($whr_arr['date_range_trend']=="monthly"){
			for($day_key=1;$day_key<=12;$day_key++){
				if($day_key<10){
					$day_key='0'.$day_key;
				}
				$key2_val=date('M',mktime(0, 0, 0, $day_key, '1', '2015'));
				$payment_dos_tot_arr[$key][$key2_val]=array_sum($payment_paid_by_dos[$key][$day_key]);
			}
		}else{
			$quarter_val_arr=array();
			for($day_key=1;$day_key<=12;$day_key++){
				if($day_key>9){
					$quarter_val="Quarter4";
				}else if($day_key>6){
					$quarter_val="Quarter3";
				}else if($day_key>3){
					$quarter_val="Quarter2";
				}else{
					$quarter_val="Quarter1";
				}
				if($day_key<10){
					$day_key='0'.$day_key;
				}
				$quarter_val_arr[$quarter_val][]=array_sum($payment_paid_by_dos[$key][$day_key]);
			}
			foreach($quarter_val_arr as $qt_key=>$qt_val){
				$payment_dos_tot_arr[$key][$qt_key]=array_sum($quarter_val_arr[$qt_key]);
			}
		}
	}
	
	$k=0;
	$receipts_cpt_name_arr=array();
	foreach($cpt_name_arr as $key=>$val){
		if(array_sum($cpt_receipts_arr[$val])!=0 && !in_array($val,$receipts_cpt_name_arr)){
			$receipts_cpt_name_arr[]=$val;
			$receipts_cpt_tot_arr[$k]["kee"]=$val;
			$receipts_cpt_tot_arr[$k]["val"]=array_sum($cpt_receipts_arr[$val]);
			$receipts_cpt_tot_val_arr["val"][$k]=array_sum($cpt_receipts_arr[$val]);
			$k++;
		}
	}

	arsort($receipts_cpt_tot_val_arr["val"]);
	$k=0;
	foreach($receipts_cpt_tot_val_arr["val"] as $key=>$val){
		if(array_sum($receipts_cpt_tot_arr[$key])!=0 && $k<10){
			$receipts_top_cpt_tot_arr[$k]["kee"]=$receipts_cpt_tot_arr[$key]["kee"];
			$receipts_top_cpt_tot_arr[$k]["val"]=$receipts_cpt_tot_arr[$key]["val"];
			$k++;
		}
	}

	$k=0;
	foreach($payment_wrt_ref_by_arr as $key=>$val){
		
		$payment_wrt_ref_tot_arr[$k]["kee"]=ucfirst($key);
		$payment_wrt_ref_tot_arr[$k]["val"]=array_sum($payment_wrt_ref_by_arr[$key]);
		$k++;
	}
	//print_r($payment_dos_tot_arr);
	
	//print_r($payment_wrt_dept_arr);
	
	$return_arr['tot_pay']=$tot_payment_arr;
	$return_arr['fac_org_pay']=$payment_fac_org_tot_arr;
	$return_arr['fac_pay']=$payment_fac_tot_arr;
	$return_arr['dept_pay']=$payment_dept_tot_arr;
	$return_arr['tot_pay_method']=$payment_method_arr;
	$return_arr['tot_pay_cc_method']=$payment_cc_method_arr;
	$return_arr['tot_pay_paid_by']=$payment_paid_by_arr;
	$return_arr['tot_pay_wrt_by']=$payment_wrt_by_arr;
	$return_arr['tot_pay_ref_by']=$payment_refund_arr;
	$return_arr['tot_pay_adj_by']=$payment_adj_by_arr;
	$return_arr['tot_pay_wrt_ref_by']=$payment_wrt_ref_tot_arr;
	
	$return_arr['dept_detail_pay']=$payment_dept_tot_detail_arr;
	$return_arr['dept_pay_wrt']=$payment_wrt_dept_detail_arr;
	$return_arr['dept_pay_adj']=$payment_adj_dept_detail_arr;
	
	$return_arr['tot_phy_pay']=$payment_phy_tot_arr;
	$return_arr['tot_phy_detail_pay']=$payment_phy_arr;
	$return_arr['tot_phy_chg']=$charges_phy_arr;
	
	
	$return_arr['tot_ref_phy_pay']=$payment_top_ref_tot_arr;
	$return_arr['tot_ref_phy_detail_pay']=$payment_ref_phy_arr;
	$return_arr['tot_ref_phy_chg']=$charges_ref_phy_arr;
	$return_arr['grand_ref_phy_chg']=$charges_ref_phy_arr;
	
	$return_arr['tot_pay_by_quarter_fac']=$payment_paid_by_quarter_fac;
	$return_arr['tot_pay_by_quarter_phy']=$payment_paid_by_quarter_phy;
	$return_arr['tot_pay_by_quarter_chrg']=$charges_paid_by_quarter_phy;
	
	$return_arr['tot_pay_by_quarter_dept']=$payment_paid_by_quarter_dept;
	
	
	$return_arr['phy_pay_wrt']=$payment_wrt_phy_arr;
	$return_arr['phy_pay_refund']=$payment_refund_phy_arr;
	$return_arr['phy_pay_adj']=$payment_adj_phy_arr;
	$return_arr['phy_dos_chrg']=$tot_chrg_phy_dos_arr;
	
	$return_arr['tot_ins_grp_pay']=$payment_ins_grp_tot_arr;
	$return_arr['tot_ins_pay']=$payment_ins_tot_arr;
	
	$return_arr['tot_dos_pay']=$payment_dos_tot_arr;
	
	$return_arr['top_cpt_rcpt']=$receipts_top_cpt_tot_arr;
	$return_arr['all_cpt_rcpt']=$receipts_cpt_tot_arr;
	
	
	$return_arr['tot_phy_rcpt']=$payment_phy_tot_detail_arr;
	$return_arr['tot_fac_rcpt']=$payment_fac_tot_detail_arr;
	$return_arr['tot_pt_un_post_pre_amt']=$final_un_post_pre_amt_arr;
	$return_arr['tot_chrg_by_quarter_dept']=$charges_paid_by_quarter_dept;
	
	$return_arr['payment_phy_cpt_final_arr']=$payment_phy_cpt_final_arr;
	
	
	return $return_arr;
}


function appointment_fun($cond_arr=array()){
	
	$whr_cond="";
	$whr_arr=$cond_arr['cond'];
	if($whr_arr['start_date']!=""){
		$start_date_exp=explode('-',$whr_arr['start_date']);
		$db_start_date=$start_date_exp[2].'-'.$start_date_exp[0].'-'.$start_date_exp[1];
		$whr_cond .=" and sa_app_start_date>='$db_start_date'";
	}
	if($whr_arr['end_date']!=""){
		$end_date_exp=explode('-',$whr_arr['end_date']);
		$db_end_date=$end_date_exp[2].'-'.$end_date_exp[0].'-'.$end_date_exp[1];
		$whr_cond .=" and sa_app_start_date<='$db_end_date'";
	}
	if(count($whr_arr['appt_drop'])>0){
		$db_appt=implode("','",$whr_arr['appt_drop']);
		$whr_cond .=" and sa_patient_app_status_id in('$db_appt')";
	}
	if(count($whr_arr['fac_drop'])>0){
		$db_fac=implode("','",$whr_arr['fac_drop']);
		$whr_cond .=" and sa_facility_id in('$db_fac')";
	}
	if(count($whr_arr['users_drop'])>0){
		$db_user=implode("','",$whr_arr['users_drop']);
		$whr_cond .=" and sa_doctor_id in('$db_user')";
	}
	if(count($whr_arr['sch_proc_drop'])>0){
		$db_sch_proc=implode("','",$whr_arr['sch_proc_drop']);
		$whr_cond .=" and procedureid in('$db_sch_proc')";
	}
	
	$sch_qry="select sa.id,sa.sa_patient_id,sa.sa_patient_name,sa.sa_patient_app_status_id,sa.sa_doctor_id,sa.sa_app_start_date,
	sa.sa_facility_id,sa.procedureid,TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as starttime,TIME_FORMAT(sa.sa_app_endtime, '%h:%i %p') as endtime 
	from patient_data pd INNER JOIN schedule_appointments sa ON pd.id = sa.sa_patient_id where sa_patient_id>0 $whr_cond";	
	$sch_run=imw_query($sch_qry);
	while($sch_row=imw_fetch_array($sch_run)){
		$id=$sch_row['id'];
		$sch_data[$id]['sa_patient_id']=$sch_row['sa_patient_id'];
		$sch_data[$id]['sa_patient_name']=$sch_row['sa_patient_name'];
		$sch_data[$id]['sa_patient_app_status_id']=$sch_row['sa_patient_app_status_id'];
		$sch_data[$id]['sa_doctor_id']=$sch_row['sa_doctor_id'];
		$sch_data[$id]['sa_app_start_date']=$sch_row['sa_app_start_date'];
		$sch_data[$id]['sa_app_starttime']=$sch_row['starttime'];
		$sch_data[$id]['sa_app_endtime']=$sch_row['endtime'];
		$sch_data[$id]['sa_facility_id']=$sch_row['sa_facility_id'];
		$sch_data[$id]['procedureid']=$sch_row['procedureid'];
		$sch_id_arr[$id]=$id;
		
		$appt_status_data_arr[$sch_row['sa_patient_app_status_id']][]=$id;
		$appt_proc_status_data_arr[$sch_row['procedureid']][$sch_row['sa_patient_app_status_id']][]=$id;
		$appt_proc_data_arr[$sch_row['procedureid']][]=$sch_row;
	}

	if(in_array('sch_pay_chrg',$cond_arr)){
		$sch_id_imp="'".implode("','",$sch_id_arr)."'";

		$sup_qry=imw_query("select todaysCharges,sch_app_id,encounterId from superbill where del_status='0' and sch_app_id>0 and sch_app_id in($sch_id_imp) and postedStatus='0'");
		while($sup_row=imw_fetch_array($sup_qry)){
			$sch_chrg_arr[$sup_row['sch_app_id']][]=$sup_row['todaysCharges'];
			$sch_chrg_enc_arr[$sup_row['encounterId']]=$sup_row['encounterId'];
			$enc_id_by_sch_id_arr[$sup_row['sch_app_id']]=$sup_row['encounterId'];
		}
		
		$chl_qry=imw_query("select totalAmt,sch_app_id,encounter_id from patient_charge_list where del_status='0' and sch_app_id>0 and sch_app_id in($sch_id_imp)");
		while($chl_row=imw_fetch_array($chl_qry)){
			$sch_chrg_arr[$chl_row['sch_app_id']][]=$chl_row['totalAmt'];
			$sch_chrg_enc_arr[$chl_row['encounter_id']]=$chl_row['encounter_id'];
			$enc_id_by_sch_id_arr[$chl_row['sch_app_id']]=$chl_row['encounter_id'];
		}
				
		$sch_enc_id_imp="'".implode("','",$sch_chrg_enc_arr)."'";
		$pay_qry=imw_query("select encounter_id,payment_amount,payment_type from account_trans where del_status='0' and encounter_id in($sch_enc_id_imp)");
		while($pay_row=imw_fetch_array($pay_qry)){
			if(strtolower($pay_row['payment_type'])=='deposit' || strtolower($pay_row['payment_type'])=='interest payment' || strtolower($pay_row['payment_type'])=='paid'){
				$sch_pay_arr[$pay_row['encounter_id']][]=$pay_row['payment_amount'];
			}
		}
		foreach($sch_data as $key=>$val){
			$sch_enc_id=$enc_id_by_sch_id_arr[$key];
			$appt_proc_chrg[$sch_data[$key]['procedureid']][]=array_sum($sch_chrg_arr[$key]);
			$appt_proc_pay[$sch_data[$key]['procedureid']][]=array_sum($sch_pay_arr[$sch_enc_id]);
		}
	}
	//print_r($appt_proc_pay);
	$return_arr['sch_data_detail']=$sch_data;
	$return_arr['sch_data_detail']=$sch_data;
	$return_arr['appt_status_detail']=$appt_status_data_arr;
	$return_arr['appt_proc_status_detail']=$appt_proc_status_data_arr;
	$return_arr['appt_proc_chrg']=$appt_proc_chrg;
	$return_arr['appt_proc_pay']=$appt_proc_pay;
	return $return_arr;
}
function line_chart($graph_base,$graph_data){
	$key_i=0;$kk=0;
	if($graph_base="quarter"){
		$quarter_arr=array("0"=>"Quarter1","1"=>"Quarter2","2"=>"Quarter3","3"=>"Quarter4");
		foreach($quarter_arr as $key=>$val){
			$line_payment_tot_arr[$key]["category"]=$val;
		}
	}
	foreach($graph_data as $key=>$val){	
		$key_i++;
		$line_pay_graph_var_arr[]=array("alphaField"=> "C",
			"balloonText"=> "[[title]] of [[category]]: $[[value]]",
			"bullet"=> "round",
			"bulletField"=> "C",
			"bulletSizeField"=> "C",
			"closeField"=> "C",
			"colorField"=> "C",
			"customBulletField"=> "C",
			"dashLengthField"=> "C",
			"descriptionField"=> "C",
			"errorField"=> "C",
			"fillColorsField"=> "C",
			"gapField"=> "C",
			"highField"=> "C",
			"id"=> "AmGraph-$key_i",
			"labelColorField"=> "C",
			"lineColorField"=> "C",
			"lowField"=> "C",
			"openField"=> "C",
			"patternField"=> "C",
			"title"=> "$key",
			"valueField"=> "column-$key_i",
			"xField"=> "C",
			"yField"=> "C");
		
		foreach($graph_data[$key] as $key2=>$val2){	
			$line_payment_tot_arr[$key2]["column-".$key_i]=array_sum($graph_data[$key][$key2]);
			$kk++;
		}
	}
	//print_r($line_pay_graph_var_arr);
	$return_arr['line_payment_tot_detail']=$line_payment_tot_arr;
	$return_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr;
	return $return_arr;
}

// FUNTION TO GET ID's OF ACCOUNT STATUS ELEMENTS
/*function get_account_status_id_collections(){
	$qry="Select id from account_status WHERE LOWER(status_type)='collection'";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$tempArr[$res['id']]= $res['id'];
	}
	$collectionIds= implode(',', $tempArr);
	return $collectionIds;
}*/
?>