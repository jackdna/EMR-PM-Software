<?php
	function sr_get_day_report_records($fac_id, $selected_date, $sort_by, $doc_id=''){
		
			$sql = imw_query("SELECT fy.name as facilityName,pd.fname as patientFName,pd.lname as patientLName,
						pd.primary_care as refPhysician,
						us.fname as physicianFName,
						us.lname as physicianLName,
						us1.fname as operatorFName,				
						us1.lname as operatorLName,								
						co.totCopay as copay,
						sa.id as appt_id,
						sa.sa_patient_app_status_id,
						TIME_FORMAT(sa.sa_app_time, '%l:%i %p') as appTime,
						sa.sa_comments as comments,
						sa.sa_facility_id,
						sa.sa_doctor_id,
						sa.sa_patient_app_status_id
					FROM schedule_appointments sa 
					LEFT JOIN patient_data pd ON pd.pid = sa.sa_patient_id 
					LEFT JOIN users us ON us.id = sa.sa_doctor_id 
					LEFT JOIN users us1 ON us1.username = sa.sa_madeby 
					LEFT JOIN 
					(
						SELECT insData.pid AS insPid, sum( insData.copay ) AS totCopay
						FROM patient_data pd1, insurance_data insData
						WHERE pd1.pid = insData.pid
						GROUP BY pd1.pid
					) co on co.insPid=sa.sa_patient_id 
					LEFT JOIN facility fy ON fy.id = sa.sa_facility_id 
					WHERE sa.sa_facility_id IN (".$fac_id.") 
					AND sa.sa_doctor_id IN (".$doc_id.")  
					AND '".$selected_date."' 
					BETWEEN sa.sa_app_start_date AND sa.sa_app_end_date	
					ORDER BY sa.sa_facility_id,".$sort_by.", sa.sa_app_starttime
				");
			$result_array = array();	
			while($res = imw_fetch_array($sql)){
				$result_array[] = $res;
			}	
			$result = $result_array;
			return $result;
	}

	function sr_get_appt_prev_status_time($appt_id, $status_id, $status_date){
		if($status_date!=""){
			$st_whr=" and ps1.status_date = '".$status_date."'" ;
		}
		
			$sql = imw_query("SELECT DATE_FORMAT(ps1.status_date, '%m-%d-%Y') as status_date, TIME_FORMAT(ps1.status_time, '%l:%i %p') as appCheckTime  
					FROM previous_status ps1 
					WHERE ps1.sch_id  = '".$appt_id."' 
					AND ps1.status = '".$status_id."' 
					$st_whr
					ORDER BY ps1.status_date LIMIT 1
					");
			$result_array = array();	
			while($res = imw_fetch_array($sql)){
				$result_array[] = $res;
			}	
			$result = $result_array;
			return $result;
	}

	function sr_get_appt_status_name_new($status_id){
		if($status_id == 0){
			return "New Appointment";
		}
		$status_qry = imw_query("SELECT id, status_name FROM `schedule_status` where id=$status_id");
		$res = imw_fetch_assoc($status_qry);
		return $res['status_name'];
	}
	
	function sr_get_appt_status_name($status_id){
		$return = "";
		switch ($status_id):
			case 0:
				$return = "New Appointment";
			break;
			case 2:
				$return = "Chart Pulled";
			break;
			case 3:
				$return = "No-Show";
			break;			
			case 6:
				$return = "Left Without Visit";
			break;	
			case 7:
				$return = "Insurance/Financial Issue";
			break;	
			case 11:
				$return = "Check-out";
			break;
			case 13:
				$return = "Check-in";
			break;
			case 17:
				$return = "Confirmed";
			break;
			case 18:
				$return = "Canceled";
			break;
			case 200:
				$return = "Room assignment";
			break;
			case 201:
				$return = "Rescheduled";
			break;
		endswitch;
		return $return;
	}
	
	function re_get_scheduler_procedure_db(){
		
			$sql = imw_query("select group_concat(id separator ',') as id, proc from slot_procedures where proc != '' group by proc order by proc");
			$result_array = array();	
			while($res = imw_fetch_array($sql)){
				$result_array[] = $res;
			}	
			$result = $result_array;
			return $result;
		
	}

	function sr_get_scheduler_procedure_list(){
		$return = "";
		$tmp_return = "";
		$arr_scheduler_procedure = re_get_scheduler_procedure_db();
		$str_all_procedures = "";
		foreach ($arr_scheduler_procedure as $s){
			if(strlen($s["proc"])>45){
				$s["proc"]=substr($s["proc"],0,45).'...';
			}
			$tmp_return .= "<option value='".$s["id"]."'>".ucfirst($s["proc"])."</option>";
			$str_all_procedures .= $s["id"].",";
		}
		if($str_all_procedures != ""){
			$str_all_procedures = substr($str_all_procedures,0,-1);
		}
		if($tmp_return != ""){
			$return = $tmp_return;
		}
		return $return;
	}

	function sr_get_procedure_report_summary($start_date, $end_date, $proc_ids,$facility_name,$phy_name, $group_by='facility'){
		$proc_ids_str=implode(',',$proc_ids);
		if($proc_ids_str){
			$proc_qry=" AND sa.procedureid in ('".str_replace(",","','",$proc_ids_str)."')";
		}
		$pos_facility_id = implode(',',$facility_name);	
		$provider_id = implode(',',$phy_name);
		
		if($pos_facility_id){
			$fac_whr=" and sa.sa_facility_id IN (".$pos_facility_id.")";
		}
		if($provider_id){
			$prov_whr=" and sa.sa_doctor_id IN (".$provider_id.")";
		}

		$qry="SELECT sa.id, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_facility_id, 
		COUNT(sa.procedureid) AS cprocs, COUNT(sa.sa_patient_app_status_id) as stsCount, sp.proc, sp.acronym,
		u.fname, u.lname , sa.sa_doctor_id
		FROM schedule_appointments sa 
		LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid 
		LEFT JOIN users u ON u.id = sa.sa_doctor_id 
		LEFT JOIN facility ON facility.id= sa.sa_facility_id 
		WHERE (sa.sa_app_start_date BETWEEN '".$start_date."' AND '".$end_date."')  
		and sa.sa_patient_app_status_id NOT IN (203) 
		and sa.sch_template_id != '0'
		$proc_qry
		$fac_whr
		$prov_whr
		";
		if($group_by=='facility'){
			$qry.=" GROUP BY sa.procedureid, sa.sa_patient_app_status_id, sa.sa_facility_id ORDER BY facility.name, sp.proc";
		}else{
			$qry.=" GROUP BY sa.procedureid, sa.sa_patient_app_status_id, sa.sa_doctor_id ORDER BY u.lname, u.fname, sp.proc";
		}		
		
		$sql = imw_query($qry);
			$result_array = array();	
			while($res = imw_fetch_array($sql)){
				$result_array[] = $res;
			}	
			$result = $result_array;
			return $result;
	}

	function sr_get_procedure_report_detail($start_date, $end_date, $proc_id,$facility_name,$phy_name){
		$pos_facility_id = implode(',',$facility_name);	
		$provider_id = implode(',',$phy_name);
		
		if($pos_facility_id){
			$fac_whr=" and sa.sa_facility_id IN (".$pos_facility_id.")";
		}
		if($provider_id){
			$prov_whr=" and sa.sa_doctor_id IN (".$provider_id.")";
		}
		
			$sql = imw_query("SELECT sa.case_type_id,sa.sa_patient_app_status_id,
					sa.sa_patient_id, sa.sa_patient_name, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid,
					sa.sa_app_start_date, DATE_FORMAT(sa.sa_app_starttime,'%l:%i %p') as sa_app_starttime, u.fname, u.lname, ss.status_name,
					sa.id, sa.sa_doctor_id, pd.DOB  
					FROM schedule_appointments sa 
					LEFT JOIN users u ON u.id = sa.sa_doctor_id 
					LEFT JOIN schedule_status ss ON ss.id = sa.sa_patient_app_status_id 
					LEFT JOIN patient_data pd ON pd.id=sa.sa_patient_id 
					WHERE (sa.sa_app_start_date BETWEEN '".$start_date."' AND '".$end_date."') 
					and sa.sa_patient_app_status_id NOT IN (203) 
					and sa.sch_template_id != 0 
					AND sa.procedureid = '".$proc_id."' 
					$fac_whr
					$prov_whr
					ORDER BY sa.sa_doctor_id
					");
			
			$result_array = array();	
			while($res = imw_fetch_array($sql)){
				$result_array[] = $res;
			}	
			$result = $result_array;
			return $result;
	}
	
	function sr_get_provider_report_summary($start_date, $end_date, $proc_ids,$facility_name,$phy_name){
		$proc_ids_str=implode(',',$proc_ids);
		if($proc_ids_str){
			$proc_qry=" AND sa.procedureid in ('".str_replace(",","','",$proc_ids_str)."')";
		}
		$pos_facility_id = implode(',',$facility_name);	
		$provider_id = implode(',',$phy_name);
		
		if($pos_facility_id){
			$fac_whr=" and sa.sa_facility_id IN (".$pos_facility_id.")";
		}
		if($provider_id){
			$prov_whr=" and sa.sa_doctor_id IN (".$provider_id.")";
		}
		
			$sql = imw_query("SELECT sa.id, sa.sa_patient_app_status_id AS sa_patient_app_status_id, sa.procedureid,
					sp.proc, sp.acronym,
					u.fname, u.lname , sa.sa_doctor_id
					FROM schedule_appointments sa 
					LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid 
					LEFT JOIN users u ON u.id = sa.sa_doctor_id 
					WHERE sa.sa_app_start_date BETWEEN '".$start_date."' AND '".$end_date."'  
					and sa.sa_patient_app_status_id NOT IN (203) 
					$proc_qry
					$fac_whr
					$prov_whr
					ORDER BY u.lname,u.fname,sp.proc
					");
			$result_array = array();	
			while($res = imw_fetch_array($sql)){
				$result_array[] = $res;
			}	
			$result = $result_array;
			return $result;
	}
	
	function sr_get_provider_report_detail($start_date, $end_date,$doc_ids,$facility_name,$phy_name){
		$pos_facility_id = implode(',',$facility_name);	
		$provider_id = implode(',',$phy_name);
		
		if($pos_facility_id){
			$fac_whr=" and sa.sa_facility_id IN (".$pos_facility_id.")";
		}
		if($provider_id){
			$prov_whr=" and sa.sa_doctor_id IN (".$provider_id.")";
		}
			$sql = imw_query("SELECT sa.case_type_id,sa.sa_patient_app_status_id,
					sa.sa_patient_id, sa.sa_patient_name,
					sa.sa_app_start_date, u.fname, u.lname, 
					sa.id,sp.proc 
					FROM schedule_appointments sa 
					LEFT JOIN users u ON u.id = sa.sa_doctor_id 
					LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid 
					WHERE sa.sa_app_start_date BETWEEN '".$start_date."' AND '".$end_date."' 
					and sa.sa_patient_app_status_id NOT IN (203) 
					AND sa.sa_doctor_id = '".$doc_ids."' 
					$fac_whr
					$prov_whr
					ORDER BY sp.proc,sa.sa_patient_name
					");
			$result_array = array();	
			while($res = imw_fetch_array($sql)){
				$result_array[] = $res;
			}	
			$result = $result_array;
			return $result;
	}
	
	function sr_populate_yr_qr_mn_wk_lists(){
		$cr_y = date('Y',mktime(0,0,0,date("d"),date("m"),(date("Y")+1)));//date('Y');
		$year_list = "";
		$month_list = "";
		$quarter_list = "";
		$week_list = "";
		
		$fourweeklater = time() + (86400 * 28);
		$twomonthlater = time() + (86400); //$twomonthlater = time() + (86400 * (28*2)); 
		$eff_date_add = date('m-d-Y-w',$fourweeklater);
		$eff_date_arr = explode("-",$eff_date_add);								
		$m = $eff_date_arr[0];
		$d = $eff_date_arr[1];								
		$y = $eff_date_arr[2];
		$w = $eff_date_arr[3];								
		$Array = re_get_week_dates($m,$d,$y,$w);	
		$dt_first = $Array[1][1];
		$curr_week_of_the_year = date("W");
		$total_weeks = (4 * 52) + $curr_week_of_the_year;
		for($days=0;$days<$total_weeks;$days++){
			$dt_chk_first = $Array[3][1];
			$dt_chk_last = $Array[3][7];
			$dt_first = $Array[1][1];
			$dt_last = $Array[1][7];								
			$week_list .= "<option value='".$dt_first."|".$dt_last."'>";
			$week_list .= get_date_format($dt_chk_first,"mm-dd-yyyy")." -- ".get_date_format($dt_chk_last,"mm-dd-yyyy");
			//$week_list .= $dt_chk_first." -- ".$dt_chk_last;
			$week_list .= "</option>";								
			$eff_date_ar = explode("-",$dt_first);								
			$yr = $eff_date_ar[0];
			$mr = $eff_date_ar[1];
			$dr = $eff_date_ar[2];								
			$eff_date_arrs = date('m-d-Y-w',mktime(0,0,0,$mr,$dr-2,$yr));
			$eff_date_arr = explode("-",$eff_date_arrs);								
			$mms = $eff_date_arr[0];
			$dds = $eff_date_arr[1];
			$yys = $eff_date_arr[2];
			$wws = $eff_date_arr[3];								
			$Array = re_get_week_dates($mms,$dds,$yys,$wws);								
		}

		//year
		for($dt_y = $cr_y; $dt_y >= ($cr_y - 5); $dt_y--){												
			$val_y = $dt_y."-01-01|".$dt_y."-12-31";
			$year_list .= "<option value=\"".$val_y."\">".$dt_y."</option>";
			
			if($dt_y == $cr_y){
				
				$dt_m_max = intval(date("m",$twomonthlater));
				
				switch($dt_m_max){
					case 1:
					case 2:
					case 3:
						$dt_m_st_qr = 1;
					break;
					case 4:
					case 5:
					case 6:
						$dt_m_st_qr = 4;
					break;
					case 7:
					case 8:
					case 9:
						$dt_m_st_qr = 7;
					break;
					case 10:
					case 11:
					case 12:
						$dt_m_st_qr = 10;
					break;
				}
				
			}else{
				$dt_m_max = 12;
				$dt_m_st_qr = 10;
			}
			$month_list .= "<OPTGROUP LABEL=\"Year ".$dt_y."\">";
			$quarter_list .= "<OPTGROUP LABEL=\"Year ".$dt_y."\">";
			//2010 - 6 5 4 3 2 1; 2009 - 12 11 10 9 8 7 6 5 4 3 2 1;
			for($dt_m = $dt_m_max; $dt_m > 0; $dt_m--){
				//month
				$misc_dt = date('M-Y~Y-m-d~t',mktime(0, 0, 0, $dt_m, 1, $dt_y));
				$arr_misc_dt = explode("~", $misc_dt);
				$date_make = $arr_misc_dt[0];
				$date_query = $arr_misc_dt[1];
				$lt_day = $arr_misc_dt[2];
				$date_queryy = date('Y-m-d',mktime(0, 0, 0, $dt_m, $lt_day, $dt_y));
				$month_list .= "<option value='".$date_query."|".$date_queryy."'>".$date_make."</option>";

				//quarter
				$dt_m_st_qr = ($dt_m_st_qr < 0) ? 10 : $dt_m_st_qr;		
				if($dt_m == $dt_m_st_qr){
					$dt_m_ed_qr = $dt_m_st_qr + 2;
					$misc_dt_ed_qr = date('M-Y~t',mktime(0, 0, 0, $dt_m_ed_qr, 1, $dt_y));
					$arr_misc_dt_ed_qr = explode("~", $misc_dt_ed_qr);
					$date_make_ed_qr = $arr_misc_dt_ed_qr[0];
					$lt_day_ed_qr = $arr_misc_dt_ed_qr[1];
					$date_queryy_ed_qr = date('Y-m-d',mktime(0, 0, 0, $dt_m_ed_qr, $lt_day_ed_qr, $dt_y));
					$quarter_list .= "<option value='".$date_query."|".$date_queryy_ed_qr."'>".$date_make." -- ".$date_make_ed_qr."</option>";
					$dt_m_st_qr = $dt_m_st_qr - 3;
				}
			}
			$month_list .= "</OPTGROUP>";
			$quarter_list .= "</OPTGROUP>";
		}
		return array($year_list, $quarter_list, $month_list, $week_list);
	}
	
	function re_get_week_dates($m,$d,$y,$w){	
		for($x=0; $x<=8; $x++){
				$j=0;
				$k=1;
				$l=2;
				$mm=3;
				
				$DateArray[$j][$x] = date('l, F d Y',mktime(0,0,0,$m,($d-$w)+$x,$y));
				$DateArray[$k][$x] = date('Y-m-d',mktime(0,0,0,$m,($d-$w)+$x,$y));
				$DateArray[$l][$x] = date('D',mktime(0,0,0,$m,($d-$w)+$x,$y));
				$DateArray[$mm][$x] = date('m-d-Y',mktime(0,0,0,$m,($d-$w)+$x,$y));
				
		}
		return $DateArray;
	}
	
	function sr_get_pt_ref_phy($patient_id){
		$sql = "select primary_care from patient_data where id = '".$patient_id."'";
		$sql_pri = @imw_query($sql);
		$rt_pri = @imw_fetch_array($sql_pri);
		return $rt_pri;
	}
	
	
	function re_get_pt_copay($patient_id, $case_typeid){

		$total_copay = "";

		$query_pri = "select copay from insurance_data where pid=$patient_id and ins_caseid ='$case_typeid' and type='primary' and actInsComp = '1'"; 
		$sql_pri = @imw_query($query_pri);
		$rt_pri = @imw_fetch_array($sql_pri);
		$ins_pri=$rt_pri["copay"];
			
		$copay_policy_qry="SELECT secondary_copay,tertiary_copay FROM `copay_policies`";
		$copay_policy_res=@imw_query($copay_policy_qry);
		list($secondary_copay ,$tertiary_copay)=@imw_fetch_array($copay_policy_res);

		$ins_sec=0;
		if(strtolower($secondary_copay) == 'yes'){
			$query_sec = "select copay from insurance_data where pid=$patient_id and ins_caseid ='$case_typeid' and type='secondary' and actInsComp = '1'"; 
			$sql_sec = @imw_query($query_sec);
			$rt_sec = @imw_fetch_array($sql_sec);
			$ins_sec=$rt_sec["copay"];
		}
		$ins_ter = 0;
		if(strtolower($tertiary_copay) == 'yes'){
			$query_ter = "select copay from insurance_data where pid=$patient_id  and ins_caseid ='$case_typeid' and type='tertiary' and actInsComp = '1'"; 
			$sql_ter = @imw_query($query_ter);
			$rt_ter = @imw_fetch_array($sql_ter);
			$ins_ter=$rt_ter["copay"];
		}
		$total_copay = trim($ins_pri+$ins_sec+$ins_ter);
		return $total_copay;
	}

?>