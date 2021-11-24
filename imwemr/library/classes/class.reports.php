<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
/*
FILE : CLSReport.php
PURPOSE : 
ACCESS TYPE : INCLUDED
*/
class CLSReports{

	function re_get_scheduler_facility_db(){
		try{
			$qry = "select id, name from facility order by name";
			$sql = imw_query($qry);
			$arr = array();
			while( $row = imw_fetch_assoc($sql)) 
			{
				$arr[$row['id']] = trim($row['name']);
			}
			
			return $arr;
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	function re_get_scheduler_facility_list($saved_val = ""){		
		$tmp_return = "";
		$return = "";
		$arr_scheduler_facility = $this->re_get_scheduler_facility_db();
		$str_all_facilities = "";
		foreach ($arr_scheduler_facility as $s) {
			$tmp_return .= "<option value='".$s["id"]."'";
			if ($s["id"] == $saved_val){
				$tmp_return .= "selected";
			}
			$tmp_return .= ">".ucfirst($s["name"])."</option>";
			$str_all_facilities .= $s["id"].",";
		}
		if($str_all_facilities != ""){
			$str_all_facilities = substr($str_all_facilities,0,-1);
		}
		if($tmp_return != ""){
			//$return = "<option value=\"".$str_all_facilities."\">All Facilities</option>".$tmp_return;
			$return = $tmp_return;
		}
		return $return;
	}

	function re_get_provider_db(){
		try{
			$sql = "select id, fname, lname from users where user_type NOT IN(5,6) order by lname, fname";
			$result = $this->conn->Execute($sql);
			return $result->GetArray();
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	function re_get_provider_list($saved_val = ""){		
		$tmp_return = "";
		$return = "";
		$arr_provider = $this->re_get_provider_db();
		$str_all_providers = "";
		foreach ($arr_provider as $s) {
			$tmp_return .= "<option value='".$s["id"]."'";
			if ($s["id"] == $saved_val){
				$tmp_return .= "selected";
			}
			$tmp_return .= ">".ucfirst($s["lname"]);
			if($s["fname"] != ""){
				$tmp_return .= ", ".ucfirst($s["fname"]);
			}
			$tmp_return .= "</option>";
			$str_all_providers .= $s["id"].",";
		}
		if($str_all_providers != ""){
			$str_all_providers = substr($str_all_providers,0,-1);
		}
		if($tmp_return != ""){
			$return = "<option value=\"".$str_all_providers."\">All Providers</option>".$tmp_return;
		}
		return $return;
	}
	
	function re_change_date_format($mmddyyyy, $sep = "-"){
		list($m,$d,$y) = explode($sep,$mmddyyyy);			
		return $y.'-'.$m.'-'.$d;
	}

	function re_show_date_format($yyyymmdd, $sep = "-"){
		list($y,$m,$d) = explode($sep,$yyyymmdd);			
		return $m.'/'.$d.'/'.$y;
	}

	function re_change_name_format($fname, $lname){
		$return = "N/A";
		if($fname != '' && $lname != ''){
			$return = $lname.', '.$fname;
		}else if($lname != ''){
			$return = $lname;
		}
		return $return;
	}
	
	function re_html_to_pdf($strHTML, $rel_path){
		$return = "<html><head><style>.failureMsg{font-family:verdana;font-size:12px;font-weight:bold;color:#FF0000;}</style></head><body><div style=\"text-align:center;background-color:#ffffff\"><span class=\"failureMsg\">No Record Found.</span></div><script type=\"text/javascript\">parent.show_img('none');</script></body></html>";
		if(trim($strHTML) != ""){
			$fp = fopen($rel_path."/pdffile.html","w");
			$intBytes = fputs($fp,$strHTML);
			fclose($fp);
			if($intBytes !== false){
				$return = "<html><body><script type=\"text/javascript\">var parWidth = parent.document.body.clientWidth;var parHeight = parent.document.body.clientHeight;window.open('".$rel_path."/createPdf.php?op=l','pdfPrint','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');parent.show_img('none');</script></body></html>";
			}
		}
		print $return;
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
		try{
			$sql = "select primary_care from patient_data where id = '".$patient_id."'";
			$result = $this->conn->Execute($sql);
			return $result->GetArray();
		}catch(Exception $error){
			die($error.imw_error());
		}
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

	function getNextRunTime($selHour='', $arrWeekdays=array(), $arrMonths=array(), $arrQuarterly=''){
		$arrNextExeTime = array();
	
		$curHour=date('H');
		$curDay=date('d');
		$curMonth=date('m');
		$curYear=date('Y');
		
		$curWeekDayNum=date('N'); 
		
		sort($arrWeekdays);
		sort($arrMonths);
		sort($arrQuarterly);
	
		//GET TOT DAYS OF EVERY MONTHS
		$num=intval($curMonth);
		$year = $curYear;
		for($i=$curMonth; $i<= $curMonth+12; $i++){
			if($i==13){ $num=1; $year = $curYear+1;}
	
			$arrMonthDays[$year][$num] = cal_days_in_month(CAL_GREGORIAN, $num, $year);
			$num++;
		}
		
		foreach($arrWeekdays as $weekDay){
			$arrWeekdays1[$weekDay] = $weekDay;
		}
		foreach($arrMonths as $months){
			$arrMonths1[$months] = $months;
		}
		foreach($arrQuarterly as $months){
			$arrQuarterly1[$months] = $months;
		}
	
		$oneYear = $curMonth + 12;
		$monNum  = $curMonth;
		$thisYear = $curYear;
		for($i=$curMonth; $i<= $oneYear; $i++){
			if($i==13){
				$monNum = 1;
				$thisYear = $curYear+1;
			}
			$monNum = intval($monNum);
			if($arrQuarterly1[$monNum]){
				$qMon = $monNum-1;
				$qYear = $thisYear;
				if($monNum=='1'){
					$qMon=12;  $qYear=$thisYear-1;
				}
				$arrSelQuarters[$qYear][$qMon] = $qMon;
			}
			if($arrMonths1[$monNum]){
				$arrSelMonths[$thisYear][$monNum] = $monNum;
			}
			$monNum++;
		}
	
		$j=0;
		foreach($arrSelMonths as $year => $arrMonthData){
			foreach($arrMonthData as $month){
				$monthDays = $arrMonthDays[$year][$month];
				
				$d=1;
				if($year==$curYear && $month==$curMonth){
					if($selHour > $curHour){ 
						$d= $curDay;
					}else{
						$d = $curDay + 1;
					}
				}
				for(;$d<= $monthDays; $d++){
					$arrMade=0;
					$weekDayOfMon = date('N', mktime(0,0,0, $month, $d, $year));
	
					if($arrWeekdays1[$weekDayOfMon]){
						$arrNextExeTime[$j]['year'] = $year;
						$arrNextExeTime[$j]['month'] = $month;
						$arrNextExeTime[$j]['day'] = $d;
						$arrNextExeTime[$j]['hour'] = $selHour;
						$arrNextExeTime[$j]['status'] = 0;
						$j++;
					}
				}
			}
		}
	
		if(sizeof($arrSelQuarters)>0){
			foreach($arrSelQuarters as $year => $arrMonthData){
				foreach($arrMonthData as $month){
					$monthDays = $arrMonthDays[$year][$month];
					$arrNextExeTime[$j]['year'] = $year;
					$arrNextExeTime[$j]['month'] = $month;
					$arrNextExeTime[$j]['day'] = $monthDays;
					$arrNextExeTime[$j]['hour'] = $selHour;
					$arrNextExeTime[$j]['status'] = 0;
					$j++;
				}
			}
		}
		//SORTING
		array_multisort($arrNextExeTime);
	
		return $arrNextExeTime;
	}
	
	function getICD10Codes($withInvCommas='', $returnData='', $returnOptions='no', $arrSelectedDXCodes=array()){
		$arrICD10Code = array();	
		$arrLaterality= $this->getLaterality();
		
		$orderBy=' icd10 asc';
		if($returnData=='desc'){
			$orderBy=' icd10_desc asc';
		}
		
		$qry = "Select TRIM(icd10) as 'icd10', laterality, staging, severity, TRIM(icd10_desc) as 'icd10_desc', deleted FROM icd10_data WHERE icd10!='' ORDER BY ".$orderBy;
		$rs = imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$color=$sel='';
			$tempArray=array();
			$newICD10=$ICD10=$ICD10_Copy=$res['icd10'];
			$dispData=$ICD10;
			if($returnData=='desc'){
				if(strlen($res['icd10_desc'])>28){
					$dispData=substr(trim($res['icd10_desc']), 0, 20).'.. - '.$ICD10;
				}else{
					$dispData=trim($res['icd10_desc']).' - '.$ICD10;
				}
			}
			if(strpos($ICD10, '-')!==false){
				$tempArray=array();
				//SITE
				if($res['laterality']>0){
					foreach($arrLaterality[$res['laterality']] as $code){
						$ICD10_Copy=$ICD10;
						$newICD10=preg_replace('/-/', $code, $ICD10_Copy,1);
						$tempArray[$newICD10]=$newICD10;
					}
				}else{
					$tempArray[$ICD10]=$ICD10;
				}
				//STAGING
				if($res['staging']>0){
					$loopArray=$tempArray;
					unset($tempArray);
					foreach($arrLaterality[$res['staging']] as $code){
						foreach($loopArray as $icd_code){
							$ICD10_Copy=$icd_code;
							$newICD10=preg_replace('/-/', $code, $ICD10_Copy,1);
							$tempArray[$newICD10]=$newICD10;						
						}
					}
				}
				//SEVERITY
				if($res['severity']>0){
					$loopArray=$tempArray;
					unset($tempArray);
					foreach($arrLaterality[$res['severity']] as $code){
						foreach($loopArray as $icd_code){
							$ICD10_Copy=$icd_code;
							$newICD10=preg_replace('/-/', $code, $ICD10_Copy,1);
							$tempArray[$newICD10]=$newICD10;	
						}
					}
				}
				unset($loopArray);
				//FINAL ARRAY
				$val_concat='';
				if($withInvCommas==''){
					$val_concat="'".implode("','", $tempArray)."'"; 
				}else{
					$val_concat=implode(",", $tempArray); 
				}
				if($returnOptions=='yes'){
					if($arrSelectedDXCodes[$val_concat])$sel='SELECTED';
					if($res['deleted']==1){
						$color='color:#CC0000!important';
						$arrICD10CodeDeleted.='<option value="'.$val_concat.'" '.$sel.' style="'.$color.'">'.$dispData.'</option>';					
					}else{
						$arrICD10Code.='<option value="'.$val_concat.'" '.$sel.' style="'.$color.'">'.$dispData.'</option>';					
					}
				}else{
					$arrICD10Code[$val_concat]=$dispData;
				}
			}else{
				if($returnOptions=='yes'){
					if($arrSelectedDXCodes[$ICD10])$sel='SELECTED';

					if($withInvCommas==''){
						if($res['deleted']==1){
							$color='color:#CC0000!important';
							$arrICD10CodeDeleted.='<option value="\''.$ICD10.'\'" '.$sel.' style="'.$color.'">'.$dispData.'</option>';
						}else{
							$arrICD10Code.='<option value="\''.$ICD10.'\'" '.$sel.' style="'.$color.'">'.$dispData.'</option>';
						}
					}else{
						if($res['deleted']==1){
							$color='color:#CC0000!important';
							$arrICD10CodeDeleted.='<option value="'.$ICD10.'" '.$sel.' style="'.$color.'">'.$dispData.'</option>';
						}else{
							$arrICD10Code.='<option value="'.$ICD10.'" '.$sel.' style="'.$color.'">'.$dispData.'</option>';
						}
					}
				}else{
					if($withInvCommas==''){
						$arrICD10Code["'".$ICD10."'"]=$dispData;
					}else{
						$arrICD10Code[$ICD10]=$dispData;
					}					
				}				

			}
			
		}unset($rs);

		if($returnOptions=='yes'){
			$arrICD10Code.=$arrICD10CodeDeleted;
		}

		return($arrICD10Code);
	}
	function getLaterality(){
		$qry="Select code, under FROM icd10_laterality WHERE deleted='0' AND under>0 ORDER BY id";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$arrLaterality[$res['under']][]=$res['code'];
		}
		unset($rs);
		return $arrLaterality;
	}

	function getFacilityName($selFacilities='', $savedSearch='0', $return_type='options'){
		$posGroupFac = isPosFacGroupEnabled();
		if($posGroupFac){
			$posfacUser_query = "SELECT posfacilitygroup_id FROM users WHERE id='".$_SESSION["authId"]."'";
			$posfacUser_res = imw_query($posfacUser_query);
			$posfacilitygroup_id_arr = array();
			$posfacUser_row = imw_fetch_array($posfacUser_res);
			$posfacilitygroup_id_arr=json_decode(html_entity_decode($posfacUser_row['posfacilitygroup_id']), true);
			$posfacilitygroup_ids = implode(",",$posfacilitygroup_id_arr);

			$qry = "select pos_facilityies_tbl.facilityPracCode as name,
				pos_facilityies_tbl.pos_facility_id as id,
				pos_tbl.pos_prac_code
				from pos_facilityies_tbl
				left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
				WHERE pos_facilityies_tbl.posfacilitygroup_id in (".$posfacilitygroup_ids.")
				order by pos_facilityies_tbl.headquarter desc,
				pos_facilityies_tbl.facilityPracCode";		
		}else{
			$qry = "select pos_facilityies_tbl.facilityPracCode as name,
				pos_facilityies_tbl.pos_facility_id as id,
				pos_tbl.pos_prac_code
				from pos_facilityies_tbl
				left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
				order by pos_facilityies_tbl.headquarter desc,
				pos_facilityies_tbl.facilityPracCode";
		}
		$res = imw_query($qry);
		
		if($return_type=='array')$return=array();else$return = '';
		
		while($row=imw_fetch_assoc($res)){	
			$id = $row['id'];
			$name = addslashes($row['name']);
			$pos_prac_code = addslashes($row['pos_prac_code']);
			$sel='';

			// TO HANDLE SAVED SEARCH
			if($savedSearch=='1'){ $sel=''; }
			if(sizeof($selFacilities)>0){
				if($selFacilities[$id]) { $sel='selected'; }
			}
			
			if($return_type=='array'){
				$return[$id]=$id;
			}else{
				$return .= '<option '.$sel.' value="'.$id.'">'.$name.' - '.$pos_prac_code.'</option>';
			}
		}						
		return $return;
	}

	function get_pos_facility_groups($selected_val=array(), $return_type='options'){
		$fetch=1;
		$posfacilitygroup_ids='';
		$result='';
		$options='';
		
		//CHECKING PRIVILEGES
		if(isPosFacGroupEnabled()){
			$posfacilitygroup_id_arr=array();
			$qry = imw_query("SELECT posfacilitygroup_id FROM users WHERE id='".$_SESSION["authId"]."'");
			$rs = imw_fetch_array($qry);
			$posfacilitygroup_id_arr=json_decode(html_entity_decode($rs['posfacilitygroup_id']), true);
			
			if(sizeof($posfacilitygroup_id_arr)>0){
				$posfacilitygroup_ids = implode(",",$posfacilitygroup_id_arr);
			}else{
				$fetch=0;
			}
		}

		//FECTHING POS FACILITY GROUPS
		if($fetch==1){
			$q="Select pos_fac_grp_id,pos_facility_group FROM pos_facility_group WHERE delete_status=0";
			if(empty($posfacilitygroup_ids)==false){
				$q.=" AND pos_fac_grp_id IN(".$posfacilitygroup_ids.")";
			}
			$q.=" ORDER BY pos_facility_group";
			$res=imw_query($q);
			while($rs=imw_fetch_assoc($res)){
				$sel = ($selected_val!='' && in_array($rs['pos_fac_grp_id'], $selected_val) ) ? "selected" : "";
				$result.='<option value="'.$rs['pos_fac_grp_id'].'" '.$sel.'>'.$rs['pos_facility_group'].'</option>';
			}
		}

		return $result;
	}

	//GET SHCEDULE FACILITY ID CORRESPONDENCE OF POS FACILITY
	function getSchFacilityInfo($arrSelFacilites=array(), $arrSelPosFacIds=array(), $return_type='array'){
		$strSelPosFacIds=implode(',', $arrSelPosFacIds);

		$qry="Select id, name FROM facility WHERE 1=1";
		if(empty($strSelPosFacIds)==false){
			$qry.=" AND fac_prac_code IN(".$strSelPosFacIds.")";
		}
		$qry.=" ORDER BY name";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$sel='';
			$res['name']= addslashes($res['name']);
			if($arrSelFacilites[$res['id']])$sel='SELECTED';

			if($return_type=='options'){
				$return .= '<option value="'.$res['id'].'" '.$sel.'>' .$res['name']. '</option>';
			}else{
				$return[$res['id']]=$res['id'];
			}
		}	
		return $return;	
	}
	//GET CONTRACT FEE
	public function getContractFee($proc,$pri_ins,$reports=''){
		$contract_price="";
		$qry = "select billing_amount from copay_policies";
		$qryRs = imw_query($qry);
		$qryRes = imw_fetch_assoc($qryRs); 
		if($qryRes['billing_amount']=='Default'){
			$contract_price=0;
			if($pri_ins>0){
				$rs = imw_query("select FeeTable from insurance_companies where id = '$pri_ins'");
				$qry_feeRes = imw_fetch_assoc($rs);
				
				$FeeTable = (int)$qry_feeRes['FeeTable'];
				if($FeeTable == 0 and empty($reports) === false){
					$FeeTable = 1;
				}
				if($FeeTable>0){
					$qry = "select cpt_fee_table.cpt_fee from cpt_fee_tbl
						join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
						where (cpt_fee_tbl.cpt_prac_code='$proc' OR 
						cpt_fee_tbl.cpt4_code='$proc' OR cpt_fee_tbl.cpt_desc='$proc')
						and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id and cpt_fee_tbl.delete_status = '0'";
					$qry_feeRs1 = imw_query($qry);
					$qry_feeRes1 = imw_query($qry_feeRs1);
					$contract_price = $qry_feeRes1['cpt_fee'];
				}
			}	
		}
		return $contract_price;
	}		

	function report_display_selected($id, $type, $total_disp=2, $total_rec=0){
		if(is_array($id)){ $id = implode(',',$id); }
		$query = "";
		$display_name="All";
		$arr_sc_name = explode(',',$id);
		if($id != ""){
			if($total_rec==count($arr_sc_name)){
				$display_name="All";
			}else if(count($arr_sc_name) == $total_disp){
				switch($type){
					case "operator":
					case "physician":
						$query="SELECT CONCAT(lname, ',',mname, ' ',fname) As name FROM users WHERE id IN ($id)";
						break;
					case "facility":
					case "practice":
						$query = "select CONCAT(pos_facilityies_tbl.facilityPracCode,' - ',pos_tbl.pos_prac_code) AS name
										from pos_facilityies_tbl
										left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
										where pos_facilityies_tbl.pos_facility_id IN($id)
										order by pos_facilityies_tbl.headquarter desc,
										pos_facilityies_tbl.facilityPracCode";	
						break;
					case "facility_tbl":
						$query = "select name from  facility where id IN($id) limit 0,$total_disp";	
						break;	
					case "group":
						$query = "select name from groups_new where gro_id IN($id)";	
						break;
					case "procedure":
						$query = "select cpt4_code as name from cpt_fee_tbl where cpt_fee_id IN($id) order by cpt4_code desc";	
						break;
					case "insurance":	
						$query = "select name from insurance_companies where id IN($id) limit 0,$total_disp";
						break;
					case "insurance_group":	
						$query = "SELECT id, title FROM ins_comp_groups WHERE id IN($id) limit 0,$total_disp";
						break;
					case "ref_phy":	
					   $query = "SELECT physician_Reffer_id,CONCAT(LastName,', ',FirstName,' ',MiddleName) AS name  FROM refferphysician WHERE physician_Reffer_id IN($id) limit 0,$total_disp";
						break;		
					case "nextAction":	
						$query = "select action_status as name from patient_next_action where id IN($id)";
						break;
					case "cpt_code":	
						$query = "select cpt_prac_code as name from cpt_fee_tbl WHERE cpt_fee_id IN($id)";
						break;
					case "cpt_category":	
						$query = "select cpt_category as name from cpt_category_tbl WHERE cpt_cat_id IN($id)";
						break;	
					case "dx_code":	
						$query = "select dx_code as name from diagnosis_code_tbl WHERE dx_code IN($id)";
						break;
					case "dx_code10":	
						$query = "select icd10 as name from icd10_data WHERE icd10 IN($id)";
						break;					
					case "department":	
						$query = "select DepartmentCode as name from department_tbl WHERE DepartmentId IN($id)";
						break;	
					case "account_status":	
						$query = "select status_name as name from account_status WHERE id IN($id)";
						break;
					case "modifiers":
						$query = "SELECT mod_prac_code as name FROM modifiers_tbl WHERE modifiers_id IN($id)";	
						break;					
					case "adj_code":
						$query = "select a_code as name from adj_code where a_id IN($id)";	
						break;					
					case "writeoff_code":
						$query = "select w_code as name from write_off_code where w_id IN($id)";	
						break;					
					case "heard_abt_us":
						$query = "select heard_options as name from heard_about_us where heard_id IN($id)";	
						break;					
				}
				$display_name = "";
				$arr_name = array();
				$result = imw_query($query);	
				while($row = imw_fetch_assoc($result)){
					$arr_name[] = $row["name"];
				}
				$display_name = join('; ',$arr_name);
			}
			else{
				$display_name="Multi";
			}
		}
		return $display_name;
	}

	function numberFormat($value='',$format,$show_zero='',$currency='',$show_currency=''){
		$currency = $currency!="" ? $currency : show_currency();
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
				//if(empty($show_currency) != true){
					$value = $currency.$value;
				//}
			}
		}
		return $value;
	}

	//GET WRITE-OFF AMOUNT OF ENCOUNTERS MAINLY USED FOR REPORTS
	public function __getWriteOffAmount($encounter_id_str, $mode='ENCOUNTERID', $dateFrom='', $dateTo='', $strBatchIds='', $operatorId='', $getDeleted='', $checkDel='', $writeoffCode='', $delLastDate='', $searchMode='dop', $insIds='', $hourFrom='', $hourTo=''){
		$delLastDate = ($delLastDate=='') ? $dateTo : $delLastDate;
		$qry = "select write_off_amount,write_off_by_id,encounter_id,
				write_off_code_id, charge_list_detail_id, era_amt, CAS_type, CAS_code, delStatus, write_off_operator_id    
				from paymentswriteoff where 1=1";
		if($encounter_id_str!=''){
			if($mode=='CHARGELISTID'){
				$qry.= "	AND charge_list_detail_id in ($encounter_id_str)";
			}else{
				$qry.= "	AND encounter_id in ($encounter_id_str)";
			}
		}
		if($dateFrom!='' && $dateTo!=''){
			if($searchMode=='dop'){
				$qry.= "	AND (write_off_date BETWEEN '$dateFrom' AND '$dateTo')";
			}else{
				$qry.= "	AND (DATE_FORMAT(entered_date, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo')";
			}
		}
		if(empty($insIds)==false){
			$qry.= "	AND write_off_by_id in ($insIds)";
		}
		if($strBatchIds!=''){
			$qry.= " AND batch_id IN(".$strBatchIds.")";
		}
		if($operatorId!=''){
			$qry.= " AND write_off_operator_id IN(".$operatorId.")";
		}
		if($getDeleted=='' && $checkDel==''){
			$qry.= "	AND delStatus != '1'";
		}
		if($writeoffCode!=''){
			$qry.= "	AND write_off_code_id IN(".$writeoffCode.")";
		}
		if($checkDel=='yes'){
			$qry.= "	AND ((delStatus='0') OR (delStatus='1' AND write_off_del_date >'$delLastDate'))";
		}
		if($hourFrom!='' && $hourTo!=''){
			$qry.= "	AND (DATE_FORMAT(entered_date, '%H:%i:%s') BETWEEN '$hourFrom' AND '$hourTo')";			
		}
		$qry.= " AND charge_list_detail_id>0";

		$mainQryRes = imw_query($qry);
		return $mainQryRes;
	}

	//GETTING ADJUSTMENT APPLIED AMOUNTS MAINLY USED FOR REPORTS
	public function __getAdjappliedData($encounter_id_str, $mode='ENCOUNTERID',$dateFrom='', $dateTo='', $strBatchIds='', $operatorId='', $reportType='', $checkNo='',$amtCriteria='',$checkAmt='',$getDeleted='',$checkDel='', $adjCodes='', $delLastDate='', $searchMode='dop', $insIds='', $pay_method='', $cc_type='', $hourFrom='', $hourTo=''){
		$delLastDate = ($delLastDate=='') ? $dateTo : $delLastDate;
		$qry= "select  encounter_id,charge_list_id,charge_list_detail_id,payment_type,payment_amount,del_status,payment_method, operator_id from 
							account_payments where 1=1";
		if($encounter_id_str!=''){
			if($mode=='CHARGELISTID'){		
				$qry.= " AND	charge_list_detail_id in($encounter_id_str)";
			}else{
				$qry.= "	AND encounter_id in($encounter_id_str)";
			}
		}
		if($dateFrom!='' && $dateTo!=''){
			if($searchMode=='dop'){
				$qry.= " and (payment_date BETWEEN '$dateFrom' AND '$dateTo')";
			}else{
				$qry.= " and (DATE_FORMAT(entered_date, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo')";
			}
		}
		if(empty($insIds)==false){
			$qry.= " AND ins_id IN(".$insIds.")";
		}			
		if($strBatchIds!=''){
			$qry.= " AND batch_id IN(".$strBatchIds.")";
		}			
		if($operatorId!=''){
			$qry.= " AND operator_id IN(".$operatorId.")";
		}
		if($adjCodes!=''){
			$qry.= " AND payment_code_id IN(".$adjCodes.")";
		}
		if($checkNo!='' || $checkAmt!='' || $reportType=='checkView'){
			$qry.= " AND LOWER(payment_method) IN('check', 'eft', 'money order')"; 
		}
		if($checkNo!=''){
			$qry.= " AND check_number IN(".$checkNo.")";
		}
		if($checkAmt!=''){
			$qry.= " AND payment_amount $amtCriteria $checkAmt";
		}
		if($getDeleted=='' && $checkDel==''){
			$qry.= " and del_status='0'";
		}
		if($checkDel=='yes'){
			$qry.= " AND ((del_status='0') OR (del_status='1' AND DATE_FORMAT(del_date_time, '%Y-%m-%d')> '$delLastDate'))";
		}
		if(empty($pay_method)==false){ //PAYMENT MODE
			$qry.=" AND LOWER(payment_method)='".$pay_method."'";
		}
		if(empty($cc_type)==false){ //IF CREDIT CARD
			$qry.=" AND LOWER(cc_type) IN(".$cc_type.")";
		}
		if($hourFrom!='' && $hourTo!=''){
			$qry.= "	AND (DATE_FORMAT(entered_date, '%H:%i:%s') BETWEEN '$hourFrom' AND '$hourTo')";			
		}

		$mainQryRs = imw_query($qry);
		return $mainQryRs;
	}	

	//GETTING CREDIT APPLIED AMOUNTS MAINLY USED FOR REPORTS
	public function __getCreditappliedRept($encounter_id_str, $mode='ENCOUNTERID',$dateFrom='', $dateTo='',$strBatchIds='',$operatorId='',$reportType='',$checkNo='',$amtCriteria='',$checkAmt='',$getDeleted='', $checkDel='', $delLastDate='', $searchMode='dop', $insIds='', $pay_method='', $cc_type='', $hourFrom='', $hourTo=''){
		$delLastDate = ($delLastDate=='') ? $dateTo : $delLastDate;
		$qry = "select amountApplied,crAppliedTo,charge_list_detail_id,
							crAppliedToEncId,crAppliedToEncId_adjust,charge_list_detail_id_adjust,type,
							payment_mode,checkCcNumber,creditCardNo,insCompany,delete_credit,operatorApplied from 
							creditapplied where 1=1";
		if($encounter_id_str!=''){
			if($mode=='CHARGELISTID'){												 
				$qry.= " AND	charge_list_detail_id_adjust in($encounter_id_str)";
			}else{
				$qry.= "	AND crAppliedToEncId_adjust in($encounter_id_str)";
			}
		}
		if($dateFrom!='' && $dateTo!=''){
			if($searchMode=='dop'){
				$qry.= " and (dateApplied BETWEEN '$dateFrom' AND '$dateTo')";
			}else{
				$qry.= " and (DATE_FORMAT(entered_date, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo')";				
			}
		}
		if($insIds!=''){
				$qry.= " AND insCompany IN(".$insIds.")";
		}
		if($strBatchIds!=''){
			$qry.= " AND batch_id IN(".$strBatchIds.")";
		}
		if($operatorId!=''){
			$qry.= " AND operatorApplied IN(".$operatorId.")";
		}
		if($checkNo!=''){
			$qry.= " AND checkCcNumber IN(".$checkNo.")";
		}
		if($checkAmt!=''){
			$qry.= " AND amountApplied $amtCriteria $checkAmt";
		}
		if($getDeleted=='' && $checkDel==''){
			$qry.= " and delete_credit='0'";			
		}
		if($checkDel=='yes'){
			$qry.= " AND ((delete_credit='0') OR (delete_credit='1' AND DATE_FORMAT(del_date_time, '%Y-%m-%d')> '$delLastDate'))";
		}
		if(empty($pay_method)==false){ //PAYMENT MODE
			$qry.=" AND LOWER(payment_mode)='".$pay_method."'";
		}
		if(empty($cc_type)==false){ //IF CREDIT CARD
			$qry.=" AND LOWER(creditCardCo) IN(".$cc_type.")";
		}
		if($hourFrom!='' && $hourTo!=''){
			$qry.= "	AND (DATE_FORMAT(entered_date, '%H:%i:%s') BETWEEN '$hourFrom' AND '$hourTo')";			
		}	
			
		$mainQryRs = imw_query($qry);
		return $mainQryRs;
	}	

	//GETTING DEBIT APPLIED AMOUNTS MAINLY USED FOR REPORTS
	public function __getDebitappliedData($encounter_id_str, $mode='ENCOUNTERID',$dateFrom='', $dateTo='',$strBatchIds='',$operatorId='',$reportType='',$checkNo='',$amtCriteria='',$checkAmt='',$getDeleted='',$checkDel='', $delLastDate='', $searchMode='dop', $insIds='', $pay_method='', $cc_type='', $hourFrom='', $hourTo=''){
		$delLastDate = ($delLastDate=='') ? $dateTo : $delLastDate;
		$qry = "select amountApplied,crAppliedTo,charge_list_detail_id,
							crAppliedToEncId,crAppliedToEncId_adjust,charge_list_detail_id_adjust,
							type,insCompany,delete_credit,payment_mode, operatorApplied,
							date_format(entered_date,'%m-%d-%Y') as entered_date from 
							creditapplied where 1=1"; 
		if($encounter_id_str!=''){
			if($mode=='CHARGELISTID'){						
				$qry.= "	AND charge_list_detail_id in($encounter_id_str)";
			}else{
				$qry.= "	AND crAppliedToEncId in($encounter_id_str)";
			}
		}
		if($dateFrom!='' && $dateTo!=''){
			if($searchMode=='dop'){
				$qry.= " and (dateApplied BETWEEN '$dateFrom' AND '$dateTo')";
			}else{
				$qry.= " and (DATE_FORMAT(entered_date, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo')";
			}
		}
		if($insIds!=''){
			$qry.= " AND insCompany IN(".$insIds.")";
		}
		if($strBatchIds!=''){
			$qry.= " AND batch_id IN(".$strBatchIds.")";
		}
		if($operatorId!=''){
			$qry.= " AND operatorApplied IN(".$operatorId.")";
		}
		if($checkNo!=''){
			$qry.= " AND checkCcNumber IN(".$checkNo.")";
		}
		if($checkAmt!=''){
			$qry.= " AND amountApplied $amtCriteria $checkAmt";
		}		
		if($getDeleted=='' && $checkDel==''){
			$qry.= " AND delete_credit='0'";
		}
		if($checkDel=='yes'){
			$qry.= " AND ((delete_credit='0') OR (delete_credit='1' AND DATE_FORMAT(del_date_time, '%Y-%m-%d')> '$delLastDate'))";
		}
		if(empty($pay_method)==false){ //PAYMENT MODE
			$qry.=" AND LOWER(payment_mode)='".$pay_method."'";
		}
		if(empty($cc_type)==false){ //IF CREDIT CARD
			$qry.=" AND LOWER(creditCardCo) IN(".$cc_type.")";
		}
		if($hourFrom!='' && $hourTo!=''){
			$qry.= "	AND (DATE_FORMAT(entered_date, '%H:%i:%s') BETWEEN '$hourFrom' AND '$hourTo')";			
		}					
		$mainQryRs = imw_query($qry);
		return $mainQryRs;
	}

	//GETTING WRITE-OF AMOUNTS
	public function __getDetailWriteOffAmt($encounter_id_str, $dateFrom='', $dateTo='',$strBatchIds='',$operatorId='',$mode='ENCOUNTERID', $insIds='', $searchMode='dop', $checkDel='', $delLastDate='', $writeoffCode='', $hourFrom='', $hourTo=''){
		$delLastDate = ($delLastDate=='') ? $dateTo : $delLastDate;
		$qry = "Select patChg.encounter_id, patChgDet.charge_list_detail_id, patChgDet.write_off,patChgDet.write_off_opr_id FROM patient_charge_list patChg 
			LEFT JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
			where 1=1";
		if($dateFrom!='' && $dateTo!=''){
			if($searchMode=='dop'){
				$qry.= " AND (write_off_date BETWEEN '$dateFrom' AND '$dateTo')";
			}else{
				$qry.= " AND (write_off_dot BETWEEN '$dateFrom' AND '$dateTo')";
			}
		}
		if($encounter_id_str!=''){
			if($mode=='ENCOUNTERID'){
				$qry.= "	AND patChg.encounter_id IN ($encounter_id_str)";
			}else{
				$qry.= "	AND patChgDet.charge_list_detail_id IN ($encounter_id_str)";
			}
		}
		if($checkDel=='yes'){
			$qry.= "	AND ((patChgDet.del_status='0') OR (patChgDet.del_status='1' AND DATE_FORMAT(patChgDet.trans_del_date, '%Y-%m-%d')> '".$delLastDate."'))";
		}else{
			$qry.= "	AND patChgDet.del_status='0'";
		}
		if($insIds!=''){
			$qry.= " AND write_off_by IN(".$insIds.")";
		}
		if($strBatchIds!=''){
			$qry.= " AND batch_id IN(".$strBatchIds.")";
		}
		if($operatorId!='') {
			$qry.= " AND write_off_opr_id IN(".$operatorId.")";
		}
		if(empty($writeoffCode)==false){
			$qry.= " AND write_off_code_id IN(".$writeoffCode.")";
		}
		if($hourFrom!='' && $hourTo!=''){
			$qry.= "	AND (DATE_FORMAT(patChgDet.entered_date, '%H:%i:%s') BETWEEN '$hourFrom' AND '$hourTo')";			
		}		
		
		$mainQryRs = imw_query($qry);
		return $mainQryRs;
	}
	
	//GETTING WRITE-OF AMOUNTS
	public function __getDetailWriteOffAmtNew($encounter_id_str, $dateFrom='', $dateTo='',$strBatchIds='',$operatorId='',$mode='ENCOUNTERID', $insIds='', $searchMode='dop', $checkDel='', $delLastDate='', $writeoffCode='', $hourFrom='', $hourTo='', $getDeleted=''){
		$delLastDate = ($delLastDate=='') ? $dateTo : $delLastDate;
		$qry = "Select encounter_id, charge_list_detail_id, write_off_amount, write_off_operator_id 
		FROM defaultwriteoff WHERE 1=1";
		if($dateFrom!='' && $dateTo!=''){
			if($searchMode=='dop'){
				$qry.= " AND (write_off_dop BETWEEN '$dateFrom' AND '$dateTo')";
			}else{
				$qry.= " AND (DATE_FORMAT(write_off_dot, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo')";
			}
		}
		if($encounter_id_str!=''){
			if($mode=='ENCOUNTERID'){
				$qry.= "	AND encounter_id IN ($encounter_id_str)";
			}else{
				$qry.= "	AND charge_list_detail_id IN ($encounter_id_str)";
			}
		}
		if($checkDel=='yes'){
			$qry.= "	AND ((del_status='0') OR (del_status='1' AND del_date> '".$delLastDate."'))";
		}
		if($getDeleted=='' && $checkDel==''){
			$qry.= "	AND del_status='0'";
		}
		if($insIds!=''){
			$qry.= " AND write_off_by IN(".$insIds.")";
		}
		if($strBatchIds!=''){
			$qry.= " AND batch_id IN(".$strBatchIds.")";
		}
		if($operatorId!='') {
			$qry.= " AND write_off_operator_id IN(".$operatorId.")";
		}
		if(empty($writeoffCode)==false){
			$qry.= " AND write_off_code_id IN(".$writeoffCode.")";
		}
		if($hourFrom!='' && $hourTo!=''){
			$qry.= "	AND (DATE_FORMAT(write_off_dot, '%H:%i:%s') BETWEEN '$hourFrom' AND '$hourTo')";			
		}
		if($searchMode=='dop'){
			$qry.= "	ORDER BY encounter_id ASC, charge_list_detail_id ASC, write_off_dop ASC"; //TO RECORD ONLY LAST WRITE-OFF
		}else{
			$qry.= "	ORDER BY encounter_id ASC, charge_list_detail_id ASC, write_off_dot ASC"; //TO RECORD ONLY LAST WRITE-OFF			
		}
		
		$mainQryRs = imw_query($qry);
		return $mainQryRs;
	}

	//GETTING PAYMENTS ENCOUNTERS ENCOUNTERS
	public function __getPaymentDetails($encounterIdStr='',$Start_date='',$End_date='',$DateRangeFor='date_of_payment',$chargeListDetIds='',$mode='ENCOUNTERID',$insId='', $checkDel='', $hourFrom='', $hourTo=''){
		$qry = "select patient_chargesheet_payment_info.paid_by,
				patient_chargesheet_payment_info.paid_by,
				patient_chargesheet_payment_info.encounter_id,
				patient_chargesheet_payment_info.payment_mode,
				patient_chargesheet_payment_info.checkNo,
				patient_chargesheet_payment_info.creditCardNo,
				patient_chargesheet_payment_info.creditCardCo,
				date_format(patient_chargesheet_payment_info.date_of_payment,'".get_sql_date_format()."') as date_of_payment,
				date_format(patient_chargesheet_payment_info.transaction_date,'".get_sql_date_format()."') as transaction_date,
				patient_chargesheet_payment_info.operatorId,
				patient_chargesheet_payment_info.insProviderId,
				patient_chargesheet_payment_info.insCompany,
				patient_chargesheet_payment_info.paymentClaims,
				patient_charges_detail_payment_info.charge_list_detail_id,
				patient_charges_detail_payment_info.paidBy,
				patient_charges_detail_payment_info.paidForProc + 
				patient_charges_detail_payment_info.overPayment as paidForProc, patient_charges_detail_payment_info.deletePayment 
				FROM patient_chargesheet_payment_info join patient_charges_detail_payment_info on
				patient_charges_detail_payment_info.payment_id = 
				patient_chargesheet_payment_info.payment_id
				where 1=1";
		if($checkDel==''){
			$qry.= " AND patient_charges_detail_payment_info.deletePayment != '1'";
		}
		if($checkDel=='yes'){
			$qry.= " AND ((patient_charges_detail_payment_info.deletePayment='0') OR (patient_charges_detail_payment_info.deletePayment='1' AND deleteDate> '$End_date'))";
		}
		if(trim($encounterIdStr) != ''){
			if($mode=='ENCOUNTERID'){
				$qry .= " and patient_chargesheet_payment_info.encounter_id in ($encounterIdStr)";
			}
			if($mode!='ENCOUNTERID'){
				if(trim($chargeListDetIds) !=''){
					$qry .= " and (patient_charges_detail_payment_info.charge_list_detail_id in ($chargeListDetIds) 
					OR (patient_chargesheet_payment_info.encounter_id in ($encounterIdStr) AND patient_charges_detail_payment_info.charge_list_detail_id=0))";
				}
			}
		}
		if($Start_date != '' and $End_date != ''){
			$qry .= " and patient_chargesheet_payment_info.$DateRangeFor 
					between '$Start_date' and '$End_date'";
		}
		if(empty($insId) == false){
			$qry .= " AND patient_chargesheet_payment_info.insProviderId IN($insId)";
		}
		if($hourFrom!='' && $hourTo!=''){
			$qry.= "	AND (DATE_FORMAT(patient_charges_detail_payment_info.entered_date, '%H:%i:%s') BETWEEN '$hourFrom' AND '$hourTo')";			
		}		

		$rs = imw_query($qry);
		$mainQryRs = array();
		while($qryRes=imw_fetch_assoc($rs)){
			$mainQryRs[] = $qryRes;
		}
		return $mainQryRs;
	}
	
	// FUNCTION TO GET ADJUSTMENT AMOUNT FOR REPORTS FOR ALL RATIOS AND RETURN RESULT by Charges Ids
	function getReportAdjustmentAmtCharges($encounter_id_str, $mode='ENCOUNTERID', $writeOff='1', $dateFrom='', $dateTo='', $strBatchIds='',$operatorId='', $reportType='', $checkNo='',$amtCriteria='',$checkAmt='', $checkDel='', $delLastDate='', $searchMode='dop', $insIds='', $pay_method='', $cc_type=''){
		$delLastDate = ($delLastDate=='') ? $dateTo : $delLastDate;
		
		$arrAdjustmentAmt = array();
		if($writeOff=='1' && $reportType!='checkView' && $checkNo=='' && $checkAmt==''){
			$write_off_qry_rs = $this->__getWriteOffAmount($encounter_id_str, $mode, $dateFrom, $dateTo, $strBatchIds, $operatorId, '',$checkDel, '',$delLastDate, $searchMode);
			while($write_off_qry_res = imw_fetch_assoc($write_off_qry_rs)){
				$era_amt=0;
				$chargeListId = $write_off_qry_res['charge_list_detail_id'];
				//if($write_off_qry_res['era_amt']!='' && $write_off_qry_res['CAS_type']=='' && $write_off_qry_res['CAS_code']==''){
				//	$era_amt = $write_off_qry_res['era_amt'];
				//}
				$write_off = $write_off_qry_res['write_off_amount'];
				$writte_off_arr[$chargeListId][] = $write_off;
				$arrTempChgIds[$chargeListId] = $chargeListId;
			}
		}
		
		$debit_qry_rs = $this->__getDebitappliedData($encounter_id_str, $mode, $dateFrom, $dateTo, $strBatchIds, $operatorId, $reportType, $checkNo, $amtCriteria, $checkAmt, '',$checkDel, $delLastDate, $searchMode, $insIds, $pay_method, $cc_type);
		while($debit_qry_res = imw_fetch_assoc($debit_qry_rs)){
			$chargeListId = $debit_qry_res['charge_list_detail_id'];
			if($debit_qry_res['crAppliedTo']=="payment"){
				$ref_total_amt_enc_arr[$chargeListId][] = $debit_qry_res['amountApplied'];
			}
			$arrTempChgIds[$chargeListId] = $chargeListId;
		}
		
		$adj_qry_rs = $this->__getAdjappliedData($encounter_id_str, $mode, $dateFrom, $dateTo, $strBatchIds, $operatorId, $reportType, $checkNo, $amtCriteria, $checkAmt, '',$checkDel, '', $delLastDate, $searchMode, $insIds, $pay_method, $cc_type);
		while($adj_qry_res = imw_fetch_assoc($adj_qry_rs)){
			$chargeListId = $adj_qry_res['charge_list_detail_id'];
			if($adj_qry_res['payment_type']=="Adjustment"){
				$adj_total_amt_enc_arr[$chargeListId][] = $adj_qry_res['payment_amount'];
			}
			if($adj_qry_res['payment_type']=="Over Adjustment"){
				$ovr_adj_total_amt_arr[$chargeListId][] = $adj_qry_res['payment_amount'];
			}
			if($adj_qry_res['payment_type']=="Returned Check"){
				$ret_check_total_amt_arr[$chargeListId][] = $adj_qry_res['payment_amount'];
			}
			$arrTempChgIds[$chargeListId] = $chargeListId;
		}
	
	
		$strTempChgIds = implode(",", $arrTempChgIds);
		$arrTempChgIds = explode(",", $strTempChgIds);
		$tempArrSize = sizeof($arrTempChgIds);

		//CALCULATE TOTAL ADJUSTMENT FOR EVERY ENCOUNTER
		for($i=0;$i< $tempArrSize; $i++){
			$chargeDet_id = $arrTempChgIds[$i];
			$pro_sub_amt_arr = array();
	
			if($writeOff=='1'){
				if(count($writte_off_arr[$chargeDet_id])>0){
					$pro_sub_amt_arr["write_off"][] = array_sum($writte_off_arr[$chargeDet_id]);
				}
			}
			if(count($ref_total_amt_enc_arr[$chargeDet_id])>0){
				$pro_sub_amt_arr["write_off"][] = -array_sum($ref_total_amt_enc_arr[$chargeDet_id]);
			}

			if(count($adj_total_amt_enc_arr[$chargeDet_id])>0){
				$pro_sub_amt_arr["write_off"][] =  -array_sum($adj_total_amt_enc_arr[$chargeDet_id]);
			}	
			if(count($ovr_adj_total_amt_arr[$chargeDet_id])>0){
				$pro_sub_amt_arr["write_off"][] = array_sum($ovr_adj_total_amt_arr[$chargeDet_id]);
			}	
			if(count($ret_check_total_amt_arr[$chargeDet_id])>0){
				$pro_sub_amt_arr["write_off"][] = -array_sum($ret_check_total_amt_arr[$chargeDet_id]);
			}	
	
			$arrAdjustmentAmt[$chargeDet_id] = array_sum($pro_sub_amt_arr["write_off"]);
		}
		return $arrAdjustmentAmt;
	}

	function getReportAdjustmentAmtCopy($encounter_id_str, $mode='ENCOUNTERID',$writeOff='1', $dateFrom='', $dateTo='', $checkDel='', $searchMode='dop', $insId='', $delLastDate='', $getDeleted=''){
		$delLastDate = ($delLastDate=='') ? $dateTo : $delLastDate;
		$arrAdjustmentAmt = array();
		$arrTempEncIds = explode(",", $encounter_id_str);
		$tempArrSize = sizeof($arrTempEncIds);
		$arrTempEncIds=array();
		 
		$write_off_qry_rs = $this->__getWriteOffAmount($encounter_id_str, $mode, $dateFrom, $dateTo, '', '', $getDeleted, $checkDel, '', $delLastDate, $searchMode, $insId);
		while($write_off_qry_res = imw_fetch_assoc($write_off_qry_rs)){
			$era_amt=0;
			$idColumn = ($mode=='CHARGELISTID') ? 'charge_list_detail_id' : 'encounter_id';
			$encounter_id = $write_off_qry_res[$idColumn];
			//if($write_off_qry_res[$i]['era_amt']!='' && $write_off_qry_res[$i]['CAS_type']=='' && $write_off_qry_res[$i]['CAS_code']==''){
			//	$era_amt = $write_off_qry_res[$i]['era_amt'];
			//}
			$write_off = $write_off_qry_res['write_off_amount'];
			$writte_off_arr[$encounter_id][] = $write_off;
			$arrTempEncIds[$encounter_id] = $encounter_id;
		}
		
		$debit_qry_rs = $this->__getDebitappliedData($encounter_id_str, $mode, $dateFrom, $dateTo, '', '', '', '', '', '', $getDeleted, $checkDel, $delLastDate, $searchMode, $insId);
		while($debit_qry_res = imw_fetch_assoc($debit_qry_rs)){
			$idColumn = ($mode=='CHARGELISTID') ? 'charge_list_detail_id' : 'crAppliedToEncId';
			$encounter_id = $debit_qry_res[$idColumn];
			if($debit_qry_res['crAppliedTo']=="payment"){
				$ref_total_amt_enc_arr[$encounter_id][] = $debit_qry_res['amountApplied'];
				$arrTempEncIds[$encounter_id] = $encounter_id;
			}
		}
		
/*		$credit_qry_res = $this->__getCreditappliedRept($encounter_id_str);
		for($i=0;$i<count($credit_qry_res);$i++){
			$encounter_id_adjust = $credit_qry_res[$i]['crAppliedToEncId_adjust'];
			if($credit_qry_res[$i]['crAppliedTo']=="adjustment"){
				$crd_total_amt_arr[$encounter_id_adjust][] = $credit_qry_res[$i]['amountApplied'];
			}
		}
*/		
		$adj_qry_rs = $this->__getAdjappliedData($encounter_id_str, $mode, $dateFrom, $dateTo, $strBatchIds, $operatorId, $reportType, $checkNo,$amtCriteria,$checkAmt,$getDeleted,$checkDel, '', $delLastDate, $searchMode, $insId);
		while($adj_qry_res = imw_fetch_assoc($adj_qry_rs)){
			$idColumn = ($mode=='CHARGELISTID') ? 'charge_list_detail_id' : 'encounter_id';
			$encounter_id = $adj_qry_res[$idColumn];
			if($adj_qry_res['payment_type']=="Adjustment"){
				$adj_total_amt_enc_arr[$encounter_id][] = $adj_qry_res['payment_amount'];
			}
			if($adj_qry_res['payment_type']=="Over Adjustment"){
				$ovr_adj_total_amt_arr[$encounter_id][] = $adj_qry_res['payment_amount'];
			}
			if($adj_qry_res['payment_type']=="Returned Check"){
				$ret_check_total_amt_arr[$encounter_id][] = $adj_qry_res['payment_amount'];
			}
			$arrTempEncIds[$encounter_id] = $encounter_id;
		}

		$tempArrSize = sizeof($arrTempEncIds);

		//CALCULATE TOTAL ADJUSTMENT FOR EVERY ENCOUNTER
		foreach($arrTempEncIds as $enc_id){
			$pro_sub_amt_arr = array();
	
			if(count($writte_off_arr[$enc_id])>0){
				$pro_sub_amt_arr["write_off"][] = array_sum($writte_off_arr[$enc_id]);
			}
			if(count($ref_total_amt_enc_arr[$enc_id])>0){
				$pro_sub_amt_arr["write_off"][] = -array_sum($ref_total_amt_enc_arr[$enc_id]);
			}
/*			if(count($deb_total_amt_arr[$enc_id])>0){
				$pro_sub_amt_arr["write_off"][] = -array_sum($deb_total_amt_arr[$enc_id]);
			}*/
/*			if(count($crd_total_amt_arr[$enc_id])>0){
				$pro_sub_amt_arr["write_off"][] = array_sum($crd_total_amt_arr[$enc_id]);
			}			
*/			if(count($adj_total_amt_enc_arr[$enc_id])>0){
				$pro_sub_amt_arr["write_off"][] =  -array_sum($adj_total_amt_enc_arr[$enc_id]);
				
			}	
			if(count($ovr_adj_total_amt_arr[$enc_id])>0){
				$pro_sub_amt_arr["write_off"][] = array_sum($ovr_adj_total_amt_arr[$enc_id]);
			}	
			if(count($ret_check_total_amt_arr[$enc_id])>0){
				$pro_sub_amt_arr["write_off"][] = -array_sum($ret_check_total_amt_arr[$enc_id]);
			}	

			$arrAdjustmentAmt[$enc_id] = array_sum($pro_sub_amt_arr["write_off"]);
		}
		return $arrAdjustmentAmt;
	}
		
	// DELETED ADJUSTMENTS
	function getDelReportAdjustmentAmt($dateFrom='', $dateTo='', $provId='', $facId='', $oprId='', $grpId='', $secProvId='', $strAllBegEncs='' , $endless='', $mode='ENCOUNTERID', $searchMode='dop'){
		$arrAdjustmentAmt = array();
		$sno=0;
		$write_off_qry_rs = $this->__getDelWriteOffAmount($dateFrom, $dateTo, $provId, $facId, $oprId, $grpId, $secProvId, $strAllBegEncs, $endless, $searchMode);

		while($write_off_qry_res = imw_fetch_assoc($write_off_qry_rs)){
			$encounter_id = $write_off_qry_res['encounter_id'];
			$write_off = $write_off_qry_res['write_off_amount'];
			$delOprId = $write_off_qry_res['del_operator_id'];
			$chgDetId = $write_off_qry_res['charge_list_detail_id'];
			if($mode=='ENCOUNTERID'){
				$arrAdjustmentAmt[$encounter_id]['OPERATOR'][$delOprId]= $delOprId;
				$arrAdjustmentAmt[$encounter_id]['CNT'][]=1;
				$arrAdjustmentAmt[$encounter_id]['AMT']+=$write_off;
				$arrAdjustmentAmt[$encounter_id]['DETAIL_ID']= $chgDetId;
				
			}else{
				$arrAdjustmentAmt[$encounter_id][$sno]['OPERATOR']= $delOprId;
				$arrAdjustmentAmt[$encounter_id][$sno]['CNT'][]=1;
				$arrAdjustmentAmt[$encounter_id][$sno]['AMT']=$write_off;
				$arrAdjustmentAmt[$encounter_id][$sno]['DETAIL_ID']= $chgDetId;				
				$sno++;
			}
		}
		
		$debit_qry_rs = $this->__getDelDebitappliedData($dateFrom, $dateTo, $provId, $facId, $oprId, $grpId, $secProvId, $strAllBegEncs, $endless, $searchMode);
		while($debit_qry_res = imw_fetch_assoc($debit_qry_rs)){
			$encounter_id = $debit_qry_res['crAppliedToEncId'];
			$encounter_id_adjust = $debit_qry_res['crAppliedToEncId_adjust'];
			$chgDetId = $debit_qry_res['charge_list_detail_id'];
			$chgDetIdAdjust = $debit_qry_res['charge_list_detail_id_adjust'];
			$delOprId = $debit_qry_res['del_operator_id'];
			if($debit_qry_res['crAppliedTo']=="adjustment"){
				if($mode=='ENCOUNTERID'){
					$arrAdjustmentAmt[$encounter_id]['OPERATOR'][$delOprId]= $delOprId;
					$arrAdjustmentAmt[$encounter_id]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id]['AMT']-=$debit_qry_res['amountApplied'];
					$arrAdjustmentAmt[$encounter_id_adjust]['AMT']+=$debit_qry_res['amountApplied'];
					$arrAdjustmentAmt[$encounter_id]['DETAIL_ID']= $chgDetId;
					$arrAdjustmentAmt[$encounter_id]['DETAIL_ID']= $chgDetIdAdjust;
				}else{
					$arrAdjustmentAmt[$encounter_id][$sno]['OPERATOR']= $delOprId;
					$arrAdjustmentAmt[$encounter_id][$sno]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id][$sno]['AMT']='-'.$debit_qry_res['amountApplied'];
					$arrAdjustmentAmt[$encounter_id_adjust][$sno]['AMT']+=$debit_qry_res['amountApplied'];
					$arrAdjustmentAmt[$encounter_id][$sno]['DETAIL_ID']= $chgDetId;
					$arrAdjustmentAmt[$encounter_id][$sno]['DETAIL_ID']= $chgDetIdAdjust;
					$sno++;
				}
			}
			if($debit_qry_res['crAppliedTo']=="payment"){
				if($mode=='ENCOUNTERID'){
					$arrAdjustmentAmt[$encounter_id]['OPERATOR'][$delOprId]= $delOprId;
					$arrAdjustmentAmt[$encounter_id]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id]['AMT']-=$debit_qry_res['amountApplied'];
					$arrAdjustmentAmt[$encounter_id]['DETAIL_ID']= $chgDetId;
				}else{
					$arrAdjustmentAmt[$encounter_id][$sno]['OPERATOR']= $delOprId;
					$arrAdjustmentAmt[$encounter_id][$sno]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id][$sno]['AMT']='-'.$debit_qry_res['amountApplied'];
					$arrAdjustmentAmt[$encounter_id][$sno]['DETAIL_ID']= $chgDetId;
					$sno++;
				}
			}
		}
		
		$adj_qry_rs = $this->__getDelAdjappliedData($dateFrom, $dateTo, $provId, $facId, $oprId, $grpId, $secProvId, $strAllBegEncs, $endless, $searchMode);
		while($adj_qry_res = imw_fetch_assoc($adj_qry_rs)){
			$encounter_id = $adj_qry_res['encounter_id'];
			$delOprId = $adj_qry_res['del_operator_id'];
			$chgDetId = $adj_qry_res['charge_list_detail_id'];
			if($adj_qry_res['payment_type']=="Adjustment"){
				if($mode=='ENCOUNTERID'){
					$arrAdjustmentAmt[$encounter_id]['OPERATOR'][$delOprId]= $delOprId;
					$arrAdjustmentAmt[$encounter_id]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id]['AMT']-=$adj_qry_res['payment_amount'];
					$arrAdjustmentAmt[$encounter_id]['DETAIL_ID']= $chgDetId;
				}else{
					$arrAdjustmentAmt[$encounter_id][$sno]['OPERATOR']= $delOprId;
					$arrAdjustmentAmt[$encounter_id][$sno]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id][$sno]['AMT']='-'.$adj_qry_res['payment_amount'];
					$arrAdjustmentAmt[$encounter_id][$sno]['DETAIL_ID']= $chgDetId;
					$sno++;
				}
			}
			if($adj_qry_res['payment_type']=="Over Adjustment"){
				if($mode=='ENCOUNTERID'){
					$arrAdjustmentAmt[$encounter_id]['OPERATOR'][$delOprId]= $delOprId;
					$arrAdjustmentAmt[$encounter_id]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id]['AMT']+=$adj_qry_res['payment_amount'];
					$arrAdjustmentAmt[$encounter_id]['DETAIL_ID']= $chgDetId;
				}else{
					$arrAdjustmentAmt[$encounter_id][$sno]['OPERATOR']= $delOprId;
					$arrAdjustmentAmt[$encounter_id][$sno]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id][$sno]['AMT']=$adj_qry_res['payment_amount'];
					$arrAdjustmentAmt[$encounter_id][$sno]['DETAIL_ID']= $chgDetId;
					$sno++;
				}
			}
			if($adj_qry_res['payment_type']=="Returned Check"){
				if($mode=='ENCOUNTERID'){
					$arrAdjustmentAmt[$encounter_id]['OPERATOR'][$delOprId]= $delOprId;
					$arrAdjustmentAmt[$encounter_id]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id]['AMT']-=$adj_qry_res['payment_amount'];
					$arrAdjustmentAmt[$encounter_id]['DETAIL_ID']= $chgDetId;
				}else{
					$arrAdjustmentAmt[$encounter_id][$sno]['OPERATOR']= $delOprId;
					$arrAdjustmentAmt[$encounter_id][$sno]['CNT'][]=1;
					$arrAdjustmentAmt[$encounter_id][$sno]['AMT']='-'.$adj_qry_res['payment_amount'];
					$arrAdjustmentAmt[$encounter_id][$sno]['DETAIL_ID']= $chgDetId;
					$sno++;
				}
			}
		}
	
		return $arrAdjustmentAmt;
	}					

	public function __getDelWriteOffAmount($dateFrom='', $dateTo='', $provId='', $facId='', $oprId='', $grpId='', $secProvId='', $strAllBegEncs='' ,$endless='', $searchMode='dop'){
		$qry = "Select pWrite.write_off_amount, pWrite.del_operator_id, pWrite.encounter_id, pWrite.write_off_del_date,pWrite.charge_list_detail_id  
				FROM paymentswriteoff pWrite LEFT JOIN patient_charge_list ptChg ON ptChg.encounter_id = pWrite.encounter_id  
				WHERE delStatus='1' AND write_off_amount>0";
		if(empty($strAllBegEncs)===false){
			$qry.= "	AND pWrite.encounter_id IN(".$strAllBegEncs.")";
		}
		if($endless=='yes'){
			if($dateFrom!=''){
				if($searchMode=='dop'){
					$qry.= "	AND ((pWrite.write_off_del_date BETWEEN '$dateFrom' AND '$dateTo') AND pWrite.write_off_date<'$dateFrom')";
				}else{
					$qry.= "	AND ((pWrite.write_off_del_date BETWEEN '$dateFrom' AND '$dateTo') AND DATE_FORMAT(pWrite.entered_date, '%Y-%m-%d')<'$dateFrom')";
				}
			}
		}else{
			if($dateFrom!='' && $dateTo!=''){
				$qry.= "	AND (pWrite.write_off_del_date BETWEEN '$dateFrom' AND '$dateTo')";
			}
		}
		if(empty($grpId) === false){
			$qry .= " AND ptChg.gro_id IN (".$grpId.")";
		}
		if(empty($provId)=== false){
			$qry.= "	AND ptChg.primaryProviderId IN(".$provId.")";
		}
		if(empty($secProvId)=== false){
			$qry.= "	AND ptChg.secondaryProviderId IN(".$secProvId.")";
		}
		if(empty($facId)=== false){
			$qry.= "	AND ptChg.facility_id IN(".$facId.")";
		}
		if(empty($oprId)=== false){
			$qry.= "	AND pWrite.del_operator_id IN(".$oprId.")";
		} 
		$mainQryRes = imw_query($qry);
		return $mainQryRes;
	}

	public function __getDelDebitappliedData($dateFrom='', $dateTo='', $provId='', $facId='', $oprId='', $grpId='', $secProvId='', $strAllBegEncs='', $endless='', $searchMode='dop'){
		$qry = "Select cApp.amountApplied,cApp.crAppliedTo,cApp.del_operator_id,	cApp.crAppliedToEncId, cApp.crAppliedToEncId_adjust, cApp.type,
							cApp.charge_list_detail_id, cApp.charge_list_detail_id_adjust FROM 
							creditapplied cApp LEFT JOIN patient_charge_list ptChg ON ptChg.encounter_id = cApp.crAppliedToEncId 
							WHERE delete_credit='1'"; 
		if(empty($strAllBegEncs)===false){
			$qry.= "	AND (cApp.crAppliedToEncId IN(".$strAllBegEncs.") OR cApp.crAppliedToEncId_adjust IN(".$strAllBegEncs."))";
		}
		if($endless=='yes'){
			if($dateFrom!=''){
				if($searchMode=='dop'){
					$qry.= "	AND (DATE_FORMAT(cApp.del_date_time, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo') AND cApp.dateApplied<'$dateFrom'";
				}else{
					$qry.= "	AND (DATE_FORMAT(cApp.del_date_time, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo') AND (DATE_FORMAT(cApp.entered_date, '%Y-%m-%d')<'$dateFrom'";					
				}
			}
		}else{
			if($dateFrom!='' && $dateTo!=''){
				$qry.= " AND (DATE_FORMAT(cApp.del_date_time, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo')";
			}
		}
		if(empty($grpId) === false){
			$qry .= " AND ptChg.gro_id IN (".$grpId.")";
		}
		if(empty($provId)=== false){
			$qry.= "	AND ptChg.primaryProviderId IN(".$provId.")";
		}
		if(empty($secProvId)=== false){
			$qry.= "	AND ptChg.secondaryProviderId IN(".$secProvId.")";
		}
		if(empty($facId)=== false){
			$qry.= "	AND ptChg.facility_id IN(".$facId.")";
		}
		if(empty($oprId)=== false){
			$qry.= "	AND cApp.del_operator_id IN(".$oprId.")";
		} 
		$mainQryRes = imw_query($qry);
		return $mainQryRes;
	}

	public function __getDelAdjappliedData($dateFrom='', $dateTo='', $provId='', $facId='', $oprId='', $grpId='', $secProvId='', $strAllBegEncs='' , $endless='', $searchMode='dop'){
		$qry = "Select aPay.encounter_id,aPay.charge_list_id,aPay.del_operator_id,aPay.payment_type,aPay.payment_amount,aPay.del_date_time, aPay.charge_list_detail_id FROM 
							account_payments aPay LEFT JOIN patient_charge_list ptChg ON ptChg.encounter_id = aPay.encounter_id 
							WHERE aPay.del_status='1'";
		if(empty($strAllBegEncs)===false){
			$qry.= "	AND aPay.encounter_id IN(".$strAllBegEncs.")";
		}
		if($endless=='yes'){
			if($dateFrom!=''){
				if($searchMode=='dop'){
					$qry.= "	AND ((DATE_FORMAT(aPay.del_date_time, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo') AND aPay.payment_date<'$dateFrom')";
				}else{
					$qry.= "	AND ((DATE_FORMAT(aPay.del_date_time, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo') AND DATE_FORMAT(aPay.entered_date, '%Y-%m-%d')<'$dateFrom')";					
				}
			}
		}else{
			if($dateFrom!='' && $dateTo!=''){
				$qry.= " AND (DATE_FORMAT(aPay.del_date_time, '%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo')";
			}
		}
		if(empty($grpId) === false){
			$qry .= " AND ptChg.gro_id IN (".$grpId.")";
		}
		if(empty($provId)=== false){
			$qry.= "	AND ptChg.primaryProviderId IN(".$provId.")";
		}
		if(empty($secProvId)=== false){
			$qry.= "	AND ptChg.secondaryProviderId IN(".$secProvId.")";
		}
		if(empty($facId)=== false){
			$qry.= "	AND ptChg.facility_id IN(".$facId.")";
		}
		if(empty($oprId)=== false){
			$qry.= "	AND aPay.del_operator_id IN(".$oprId.")";
		}
		$mainQryRes = imw_query($qry);
		return $mainQryRes;
	}

	function get_pdf_name($operator_id,$reportname,$set_path="",$subFolder1='new_html2pdf',$subFolder2='pdf'){
	
		if($subFolder1=='new_html2pdf'){
			$user_pdf_folder = $set_path.$subFolder1."/".$subFolder2."/pdf_".$operator_id;
		}else{
			$user_pdf_folder = $set_path.$subFolder2."/pdf_".$operator_id;
		}
		
		if(!is_dir($user_pdf_folder)){
			mkdir($user_pdf_folder,0777);
		}
		
		$user_pdf_folder_list = scandir($user_pdf_folder);
		for($i=0;$i<count($user_pdf_folder_list);$i++){
			if($user_pdf_folder_list[$i] != "." && $user_pdf_folder_list[$i] != ".." &&  $user_pdf_folder_list[$i] != date("m_d_y")){
				
				$current_directory = $user_pdf_folder."/".$user_pdf_folder_list[$i];
				$open_current_dir = @opendir($current_directory."/");
				while(false !== ($old_file = readdir($open_current_dir))) {
					if($file != "." && $old_file != "..") {
							@unlink($current_directory.'/'.$old_file);
					}
				}
				@rmdir($user_pdf_folder."/".$user_pdf_folder_list[$i]);
			}
		}

		$today_pdf_folder = $user_pdf_folder.'/'.date('m_d_y');
		if(!is_dir($today_pdf_folder)){
			mkdir($today_pdf_folder,0777);
		}

		$newfile_pdf_path = $subFolder2.'/pdf_'.$operator_id.'/'.date('m_d_y').'/'.$reportname.'_'.$operator_id.'_'.time();
		return($newfile_pdf_path); 
	}	
	
	function getFacilityArr(){
		$arrFac = array();
		$arrFac[0] = 'No Facility';
		$qry = "select pos_facilityies_tbl.facilityPracCode as name,
									 pos_facilityies_tbl.pos_facility_id as id,
									 pos_tbl.pos_prac_code
									 From pos_facilityies_tbl
									 Left Join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
									 Order By pos_facilityies_tbl.headquarter desc,
									 pos_facilityies_tbl.facilityPracCode";
		$sql = imw_query( $qry );
		$cnt = imw_num_rows($sql);
		if( $cnt > 0 )
		{
			while( $row = imw_fetch_assoc($sql) )
			{
				$id = $row['id'];
				$name = $row['name'];
				$pos_prac_code = $row['pos_prac_code'];
				$fullName = $name.' - '.$pos_prac_code;
				$arrFac[$id] = $fullName;
			}
		}
						
		return $arrFac;
	}
		
	function get_department_dropdown($sel_val = ''){
		if(!is_array($sel_val)){
			$sel_val = explode(',',$sel_val);
		}
		$qry = imw_query("select DepartmentId,DepartmentCode from department_tbl
		order by DepartmentCode");
		$return = '';
		if(imw_num_rows($qry) > 0){
			while($res=imw_fetch_assoc($qry)){
				$selected = '';
				if(in_array($res['DepartmentId'],$sel_val)){
					$selected = 'selected';
				}
				$return .= '<option value="'.$res['DepartmentId'].'" '.$selected.'>'.$res['DepartmentCode'].'</option>';
			}
		}
		return $return;
	}
	
	public function __getPatientLastAppointment($patient_id){
		$qry = "select sa_doctor_id , sa_facility_id from schedule_appointments
				where sa_patient_app_status_id not in (201, 18, 203, 19, 20)
				and sa_patient_id = '$patient_id' and sa_app_start_date <= now()
				order by sa_app_start_date desc, sa_app_starttime desc limit 0, 1"; 
		$appointmentQryRes = get_array_records_query($qry);
		return $appointmentQryRes;
	}	
	
	public function getPatientAppointments($pid = 0, $startDate = '' ,$endDate = '', $hourFrom = '', $hourTo = '', $groupBy = '', $provider = '', $facility = '', $group_id = '', $operator = '', $ins_company = '')
	{
		$where_qry = $fields = $joins = '';
		
		$fields = "sa.id, sa.sa_patient_id, sa_patient_name, sa_doctor_id, sa.sa_facility_id, sa_app_start_date, sa_app_starttime, sa_patient_app_status_id, sa.status_update_operator_id, sp.proc as proc_name, sp.acronym as proc_alias";
		
		$joins .= " JOIN slot_procedures sp ON sp.id = sa.procedureid ";
		
		if( $pid ){
			$where_qry .= " And sa.sa_patient_id In($pid) ";
		}
		if( $startDate && $endDate ){
			$where_qry .= " And sa.sa_app_start_date Between '$startDate' AND '$endDate' ";
		}
		if( $hourFrom && $hourTo ){
			$where_qry .= " And sa.sa_app_starttime Between '$hourFrom' AND '$hourTo' ";
		}
		if( $provider ){
			$where_qry .= " And sa.sa_doctor_id In ($provider) ";
		}
		if( $facility ){
			$where_qry .= " And sa.sa_facility_id In ($facility) ";
		}
		if( $group_id || $groupBy == 'grpby_groups' || $groupBy == 'groups' ){
			if( $group_id)	$where_qry .= " And facility.default_group In ($group_id) ";
			$fields .= ', facility.default_group';
			$joins .= " JOIN facility on facility.id = sa.sa_facility_id";
		}
		if( $operator ){
			$where_qry .= " And sa.status_update_operator_id In ($operator) ";
		}
		if( $ins_company ){
			$qry_ins = "Select group_concat(ins_caseid) as case_ids From insurance_data Where provider in ($ins_company)";
			$sql_ins = imw_query($qry_ins);
			$row_ins = imw_fetch_assoc($sql_ins);
			$case_ids = (isset($row_ins['case_ids']) && $row_ins['case_ids']) ? $row_ins['case_ids'] : '';
			
			if( $case_ids )
			{
				$case_ids = implode(",",array_unique(array_filter(explode(",",$case_ids))));	
				$where_qry .= " And sa.case_type_id In ($case_ids) ";
			}
		}
		
		$data = $data_summary = array();
		$qry = "select $fields from schedule_appointments sa ".$joins." Where 1 = 1 ".$where_qry ." Order By sa.sa_facility_id, sa_doctor_id,  sa.sa_app_start_date Desc, sa.sa_app_starttime Asc";
		$sql = imw_query( $qry );
		$cnt = imw_num_rows($sql);
		
		if( $cnt > 0 )
		{
			$firstGroupBy = 'sa_doctor_id';
			$secGroupBy = 'sa_facility_id';
			if($groupBy=='grpby_facility' || $groupBy=='facility'){
				$firstGroupBy = 'sa_facility_id';
				$secGroupBy = 'sa_doctor_id';
			}elseif($groupBy=='grpby_groups' || $groupBy=='groups'){
				$firstGroupBy = 'default_group';
				$secGroupBy = 'sa_doctor_id';
			}elseif($groupBy=='grpby_operators' || $groupBy=='operator'){
				$firstGroupBy = 'status_update_operator_id';
				$secGroupBy = 'sa_facility_id';
			}
			while( $row = imw_fetch_assoc($sql) ) {
				$row['sec_group_id'] = $row[$secGroupBy];
				$data[$row[$firstGroupBy]][] = $row;
				$data_summary[$row['sa_doctor_id']][$row['sa_facility_id']] += 1;
			}
			
		}
		$return = array('detail' => $data , 'summary' => $data_summary);
		return $return;
	}

	function getTestRecord($checkFormId,$phy_id_implode, $st_date='', $en_date=''){
		$testArr = $testPtIdArr = $testPhyIdArr = $testExamDateArr = array();//AND phyName IN(".$phy_id_implode.") 
		$dateQry1=$dateQry2='';
		//$phy_id_implode_new = "0";
		if($phy_id_implode) { 
			$phy_id_implode_new = $phy_id_implode;
			$commonWhrQry = " AND ordrby IN(".$phy_id_implode_new.") "; 
		}
		
		$formIdQry1	= " AND form_id='0' "; 
		$formIdQry2 = " AND formId='0' ";
		$phyQry1 	= " AND (signedById='0' || signedById='') ";
		$phyQry2 	= " AND (phyName='0' || phyName='') ";
		$phyQry3 	= " AND (phy='0' || phy='') ";
		if($checkFormId=='yes') { 
			$formIdQry1	= " AND form_id!='0' "; 
			$formIdQry2 = " AND formId!='0' ";
			$phyQry1 = $phyQry2 = $phyQry3 = "";
		}
		
		if(empty($st_date)==false && empty($en_date)==false){
			$dateQry1=" AND (examDate BETWEEN '".$st_date."' AND '".$en_date."')";
			$dateQry2=" AND (exam_date BETWEEN '".$st_date."' AND '".$en_date."')";
		}
		
		$qry = "SELECT surgical_id,patient_id,form_id,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',signedById as phy,ordrby as ordrby FROM surgical_tbl 
		WHERE del_status='0' AND purged='0' ".$formIdQry1.$phyQry1.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['ascan'][$qryRes['patient_id']][$qryRes['form_id']] = $qryRes['surgical_id'];
			$testPtIdArr[] = $qryRes['patient_id'];
			$testPhyIdArr[]['ascan']['phyid'][$qryRes['patient_id']] = $qryRes['ordrby'];
			$testExamDateArr[]['ascan']['exm_dt'][$qryRes['patient_id']] = $qryRes['exm_dt'];
		
		}
		$qry = "SELECT test_bscan_id,patientId,formId,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM test_bscan 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['bscan'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['test_bscan_id'];
			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['bscan']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['bscan']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		
		}
		
		$qry = "SELECT test_cellcnt_id,patientId,formId,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM test_cellcnt 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['cellcount'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['test_cellcnt_id'];
			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['cellcount']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['cellcount']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		}
		
		$qry = "SELECT disc_id,patientId,formId,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM disc_external 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['external_interior'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['disc_id'];
			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['external_interior']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['external_interior']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		
		}
		
		$qry = "SELECT disc_id,patientId,formId,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM disc 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['fundus'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['disc_id'];
			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['fundus']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['fundus']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		
		}
		
		$qry = "SELECT gdx_id,patient_id,form_id,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM test_gdx 
		WHERE del_status='0' AND purged='0' ".$formIdQry1.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['gdx'][$qryRes['patient_id']][$qryRes['form_id']] = $qryRes['gdx_id'];
			$testPtIdArr[] = $qryRes['patient_id'];
			$testPhyIdArr[]['gdx']['phyid'][$qryRes['patient_id']] = $qryRes['phy'];
			$testExamDateArr[]['gdx']['exm_dt'][$qryRes['patient_id']] = $qryRes['ordrby'];
		
		}
		
		$qry = "SELECT nfa_id,patient_id,form_id,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName AS phy,ordrby as ordrby FROM nfa 
		WHERE del_status='0' AND purged='0' ".$formIdQry1.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['hrt'][$qryRes['patient_id']][$qryRes['form_id']] = $qryRes['nfa_id'];
			$testPtIdArr[] = $qryRes['patient_id'];
			$testPhyIdArr[]['hrt']['phyid'][$qryRes['patient_id']] = $qryRes['ordrby'];
			$testExamDateArr[]['hrt']['exm_dt'][$qryRes['patient_id']] = $qryRes['exm_dt'];
		}
		
		$qry = "SELECT icg_id,patient_id,form_id,DATE_FORMAT(exam_date, '".get_sql_date_format()."') AS 'exm_dt',phy AS phy,ordrby as ordrby FROM icg 
		WHERE del_status='0' AND purged='0' ".$formIdQry1.$phyQry3.$commonWhrQry.$dateQry2;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['icg'][$qryRes['patient_id']][$qryRes['form_id']] = $qryRes['icg_id'];
			$testPtIdArr[] = $qryRes['patient_id'];
			$testPhyIdArr[]['icg']['phyid'][$qryRes['patient_id']] = $qryRes['ordrby'];
			$testExamDateArr[]['icg']['exm_dt'][$qryRes['patient_id']] = $qryRes['exm_dt'];
		}
		
		$qry = "SELECT iol_master_id,patient_id,form_id,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',signedById as phy,ordrby as ordrby FROM iol_master_tbl 
		WHERE del_status='0' AND purged='0' ".$formIdQry1.$phyQry1.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['iol_master'][$qryRes['patient_id']][$qryRes['form_id']] = $qryRes['iol_master_id'];
			$testPtIdArr[] = $qryRes['patient_id'];
			$testPhyIdArr[]['iol_master']['phyid'][$qryRes['patient_id']] = $qryRes['ordrby'];
			$testExamDateArr[]['iol_master']['exm_dt'][$qryRes['patient_id']] = $qryRes['exm_dt'];
		
		}
		
		$qry = "SELECT vf_id,patient_id,form_id,DATE_FORMAT(exam_date, '".get_sql_date_format()."') AS 'exm_dt',phy as phy,ordrby as ordrby FROM ivfa 
		WHERE del_status='0' AND purged='0' ".$formIdQry1.$phyQry3.$commonWhrQry.$dateQry2;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['ivfa'][$qryRes['patient_id']][$qryRes['form_id']] = $qryRes['vf_id'];
			$testPtIdArr[] = $qryRes['patient_id'];
			$testPhyIdArr[]['ivfa']['phyid'][$qryRes['patient_id']] = $qryRes['ordrby'];
			$testExamDateArr[]['ivfa']['exm_dt'][$qryRes['patient_id']] = $qryRes['exm_dt'];
		
		}

		$qry = "SELECT test_labs_id,patientId,formId,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM test_labs 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['laboratories'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['test_labs_id'];
			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['laboratories']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['laboratories']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		
		}
		
		$qry = "SELECT oct_id,patient_id,form_id,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM oct 
		WHERE del_status='0' AND purged='0' ".$formIdQry1.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['oct'][$qryRes['patient_id']][$qryRes['form_id']] = $qryRes['oct_id'];
			$testPtIdArr[] = $qryRes['patient_id'];
			$testPhyIdArr[]['oct']['phyid'][$qryRes['patient_id']] = $qryRes['ordrby'];
			$testExamDateArr[]['oct']['exm_dt'][$qryRes['patient_id']] = $qryRes['exm_dt'];
		
		}
		
		$qry = "SELECT oct_rnfl_id,patient_id,form_id,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM oct_rnfl 
		WHERE del_status='0' AND purged='0' ".$formIdQry1.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['oct_rnfl'][$qryRes['patient_id']][$qryRes['form_id']] = $qryRes['oct_rnfl_id'];
			$testPtIdArr[] = $qryRes['patient_id'];
			$testPhyIdArr[]['oct_rnfl']['phyid'][$qryRes['patient_id']] = $qryRes['ordrby'];
			$testExamDateArr[]['oct_rnfl']['exm_dt'][$qryRes['patient_id']] = $qryRes['exm_dt'];
		}
		
		$qry = "SELECT pachy_id,patientId,formId,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM pachy 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['pachy'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['pachy_id'];
			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['pachy']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['pachy']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		}
		
		$qry = "SELECT topo_id,patientId,formId,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM topography 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['topography'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['topo_id'];
			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['topography']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['topography']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		}
		
		$qry = "SELECT vf_id,patientId,formId,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM vf 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['vf'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['vf_id'];
			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['vf']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['vf']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		}
		
		$qry = "SELECT vf_gl_id,patientId,formId,DATE_FORMAT(examDate,'".get_sql_date_format()."') AS 'exm_dt',phyName as phy,ordrby as ordrby FROM vf_gl 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['vf_gl'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['vf_gl_id'];
			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['vf_gl']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['vf_gl']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		}
		
		$qry = "SELECT test_other_id,patientId,formId,DATE_FORMAT(examDate, '".get_sql_date_format()."') AS 'exm_dt',phyName as phy,test_other,ordrby as ordrby FROM test_other 
		WHERE del_status='0' AND purged='0' ".$formIdQry2.$phyQry2.$commonWhrQry.$dateQry1;
		$rs = imw_query($qry);
		while($qryRes=imw_fetch_assoc($rs)){
			$testArr['test_other'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['test_other_id'];
			$testArr['test_other_name'][$qryRes['patientId']][$qryRes['formId']] = $qryRes['test_other'];

			$testPtIdArr[] = $qryRes['patientId'];
			$testPhyIdArr[]['test_other']['phyid'][$qryRes['patientId']] = $qryRes['ordrby'];
			$testExamDateArr[]['test_other']['exm_dt'][$qryRes['patientId']] = $qryRes['exm_dt'];
		
		}
		//pre($testArr);
		return array($testArr,$testPtIdArr,$testPhyIdArr,$testExamDateArr);
	}	
	
	public function __getDeductableAmount($detailId=0){
		$qry = "select * from payment_deductible where delete_deduct = '0'";
		if($detailId > 0){
			$qry .= " and charge_list_detail_id in ($detailId)";
		}
		$rs = imw_query($qry);
		$deductQryRes = array();
		while($qryRes=imw_fetch_assoc($rs)){
			$deductQryRes[] = $qryRes;
		}
		return $deductQryRes;
	}
	
	public function __removeBlankValues($array_val=array()){
		$returnArray = array();
		foreach($array_val as $key => $val){
			if(trim($val) != ''){
				$returnArray[$key] = $val;
			}
		}
		return $returnArray;
	}
	
	public function __getChargeListDetails_new($start_date,$end_date,$reff_physician_id='',$cpt_code_id='',$facility_ids='',$dx_code='',$insuranceName='',$encounter_type='',$insuranceGrp='',$grp_id='',$phy_id='', $dx_code10=''){
		//--- GET RESULTS FROM ACCOUNTING MAIN TALBE ----
		$qyery = "SELECT main.patient_id, main.encounter_id, main.date_of_service, main.charge_list_detail_id, main.reff_phy_id,main.gro_id, main.facility_id,main.primary_provider_id_for_reports as 'primaryProviderId', main.proc_code_id as procCode, main.units, (main.charges * main.units) as procCharges, 	main.write_off,main.approved_amt as approvedAmt, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4, main.dx_id5, main.dx_id6, main.dx_id7, main.dx_id8, main.dx_id9, 
		main.dx_id10, main.dx_id11, main.dx_id12 FROM report_enc_detail main
		LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id 
		LEFT JOIN pos_tbl on pos_tbl.pos_id =  pos_facilityies_tbl.pos_id 
		JOIN patient_data on patient_data.id = main.patient_id 
		LEFT JOIN users ON users.id = main.primary_provider_id_for_reports
		WHERE main.reff_phy_id > 0";
		if(trim($start_date) != '' and trim($end_date) != ''){
			$qyery .= " and (main.date_of_service between '$start_date' and '$end_date')";
		}
		if(trim($facility_ids) != ''){
			$qyery .= " and main.facility_id in($facility_ids)";
		}
		if(trim($reff_physician_id) != ''){
			$qyery .= " and main.reff_phy_id in($reff_physician_id)";
		}
		if(trim($cpt_code_id) != ''){
			$qyery .= " and main.proc_code_id in($cpt_code_id)";
		}
		
		if(trim($phy_id) != ''){
			$qyery .= " and main.primary_provider_id_for_reports in ($phy_id)";
		}
		
		if(trim($dx_code10) != ''){
			$qyery .= " and (main.dx_id1 in ($dx_code10)
						or main.dx_id2 in ($dx_code10)
						or main.dx_id3 in ($dx_code10)
						or main.dx_id4 in ($dx_code10)
						or main.dx_id5 in ($dx_code10)
						or main.dx_id6 in ($dx_code10)
						or main.dx_id7 in ($dx_code10)
						or main.dx_id8 in ($dx_code10)
						or main.dx_id9 in ($dx_code10)
						or main.dx_id10 in ($dx_code10)
						or main.dx_id11 in ($dx_code10)
						or main.dx_id12 in ($dx_code10))";
		}
	
		if(empty($insuranceName) === false){
			$qry.= " and ( main.pri_ins_id in($insCompanies) 
					OR	main.sec_ins_id in($insCompanies)
					OR	main.tri_ins_id in($insCompanies) )";
		}
		if(empty($grp_id) === false){
			$qyery .= " and main.gro_id IN ($grp_id)";
		}
		
		if($encounter_type=='initial'){ // ORDER BY date_of_service IS MUST
			$qyery .= " ORDER BY patient_data.lname, patient_data.fname, date_of_service, main.charge_list_id";
		}else{
			$qyery .= " ORDER BY patient_data.lname, patient_data.fname";
		}
		
		$rs = imw_query($qyery);
		$getChargeListDetails = array();
		while($qryRes=imw_fetch_assoc($rs)){
			$getChargeListDetails[] = $qryRes;
		}
		return $getChargeListDetails;
	}
	
	public function __getChargeListDetails($start_date,$end_date,$reff_physician_id='',$cpt_code_id='',$facility_ids='',$dx_code='',$insuranceName='',$encounter_type='',$insuranceGrp='',$grp_id='',$phy_id='', $dx_code10='', $phy_type='',$str_crediting_phy,$exclude_same_billing_crediting){
		//--- GET RESULTS FROM ACCOUNTING MAIN TALBE ----
		$qyery = "select patient_charge_list.encounter_id, patient_charge_list.patient_id,
				patient_charge_list.date_of_service,
				patient_charge_list_details.charge_list_detail_id,
				patient_charge_list.reff_phy_id,
				patient_charge_list.facility_id,
				patient_charge_list.primaryInsuranceCoId,
				patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId', patient_charge_list.secondaryProviderId,
				patient_charge_list_details.procCode,
				patient_charge_list_details.units,
				patient_charge_list_details.units *
				patient_charge_list_details.procCharges as procCharges,
				patient_charge_list_details.coPayAdjustedAmount,
				patient_charge_list_details.write_off,
				patient_charge_list_details.approvedAmt,
				patient_charge_list_details.diagnosis_id1,patient_charge_list_details.diagnosis_id2,patient_charge_list_details.diagnosis_id3,
				patient_charge_list_details.diagnosis_id4,patient_charge_list_details.diagnosis_id5,patient_charge_list_details.diagnosis_id6,
				patient_charge_list_details.diagnosis_id7,patient_charge_list_details.diagnosis_id8,patient_charge_list_details.diagnosis_id9,
				patient_charge_list_details.diagnosis_id10,patient_charge_list_details.diagnosis_id11,patient_charge_list_details.diagnosis_id12,
				patient_data.fname, patient_data.mname, patient_data.lname,
				patient_data.primary_care_phy_id,patient_data.co_man_phy_id,patient_data.primary_care_id		
				FROM patient_charge_list join patient_charge_list_details on
				patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
				JOIN patient_data on patient_data.id = patient_charge_list.patient_id
				where patient_charge_list_details.del_status='0' and patient_data.lname != 'doe'
				and patient_charge_list.reff_phy_id > 0";
		if(trim($start_date) != '' and trim($end_date) != ''){
			$qyery .= " and (patient_charge_list.date_of_service between 
					'$start_date' and '$end_date')";
		}
		if(trim($facility_ids) != ''){
			$qyery .= " and patient_charge_list.facility_id in($facility_ids)";
		}
		/* if(trim($reff_physician_id) != ''){
			$qyery .= " and patient_charge_list.reff_phy_id in($reff_physician_id)";
		} */
		if(trim($cpt_code_id) != ''){
			$qyery .= " and patient_charge_list_details.procCode in($cpt_code_id)";

		}
		if(trim($phy_id) != ''){
			$qyery .= " and patient_charge_list.primary_provider_id_for_reports in ($phy_id)";
		}
		if(empty(trim($str_crediting_phy))==false){
			$qyery .= " and patient_charge_list.secondaryProviderId in ($str_crediting_phy)";
		}		
		if($exclude_same_billing_crediting==1){
			$qyery.= " and patient_charge_list.primary_provider_id_for_reports!=patient_charge_list.secondaryProviderId";							
		}	  		
		if(trim($dx_code) != '' || trim($dx_code10) != ''){
			$qyery .= ' AND (';
			$andOR='';
			if(trim($dx_code)!= ''){
				$qyery .= " (patient_charge_list_details.diagnosis_id1 in ($dx_code)
				or patient_charge_list_details.diagnosis_id2 in ($dx_code)
				or patient_charge_list_details.diagnosis_id3 in ($dx_code)
				or patient_charge_list_details.diagnosis_id4 in ($dx_code)
				or patient_charge_list_details.diagnosis_id5 in ($dx_code)
				or patient_charge_list_details.diagnosis_id6 in ($dx_code)
				or patient_charge_list_details.diagnosis_id7 in ($dx_code)
				or patient_charge_list_details.diagnosis_id8 in ($dx_code)
				or patient_charge_list_details.diagnosis_id9 in ($dx_code)
				or patient_charge_list_details.diagnosis_id10 in ($dx_code)
				or patient_charge_list_details.diagnosis_id11 in ($dx_code)
				or patient_charge_list_details.diagnosis_id12 in ($dx_code))";
				$andOR=' OR ';
			}
			if(trim($dx_code10)!= ''){
				$qyery .=$andOR." (patient_charge_list_details.diagnosis_id1 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id2 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id3 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id4 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id5 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id6 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id7 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id8 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id9 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id10 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id11 in ($dx_code10)
				or patient_charge_list_details.diagnosis_id12 in ($dx_code10))";
			}	
			$qyery .= ') ';
		}
		if(trim($insuranceName) != '' || trim($insuranceGrp) != ''){			
			$qyery .= " and (";
			if(trim($insuranceName) != ''){
			$qyery .= 
			"patient_charge_list.primaryInsuranceCoId in($insuranceName) 
			OR patient_charge_list.secondaryInsuranceCoId in($insuranceName) 
			OR patient_charge_list.tertiaryInsuranceCoId in($insuranceName)";
			}
			if(trim($insuranceName) != '' && trim($insuranceGrp) != '')
			$qyery .= " OR ";
			if(trim($insuranceGrp) != ''){
			$qyery .= 
			"patient_charge_list.primaryInsuranceCoId in($insuranceGrp) 
			OR patient_charge_list.secondaryInsuranceCoId in($insuranceGrp) 
			OR patient_charge_list.tertiaryInsuranceCoId in($insuranceGrp)";
			}
			$qyery .= ")";
		}
		if(empty($grp_id) === false){
			$qyery .= " and patient_charge_list.gro_id IN ($grp_id)";
		}
		if(trim($phy_type) != '' && trim($phy_type) == 'all' && trim($reff_physician_id) != ''){
			$qyery .= " and patient_charge_list.reff_phy_id in($reff_physician_id)";
		}
		if(trim($phy_type) != '' && trim($phy_type) == 'prf' && trim($reff_physician_id) != ''){
			$qyery .= " and patient_data.primary_care_id in($reff_physician_id)";
		}
		if(trim($phy_type) != '' && trim($phy_type) == 'pcp' && trim($reff_physician_id) != ''){
			$qyery .= " and patient_data.primary_care_phy_id in($reff_physician_id)";
		}
		if(trim($phy_type) != '' && trim($phy_type) == 'cm' && trim($reff_physician_id) != ''){
			$qyery .= " and patient_data.co_man_phy_id in($reff_physician_id)";
		}
		if(trim($phy_type) != '' && trim($phy_type) == 'prf'){
			$qyery .= " and patient_data.primary_care_id != '0'";
		}
		if(trim($phy_type) != '' && trim($phy_type) == 'pcp'){
			$qyery .= " and patient_data.primary_care_phy_id != '0'";
		}
		if(trim($phy_type) != '' && trim($phy_type) == 'cm'){
			$qyery .= " and patient_data.co_man_phy_id != '0'";
		}
		if(trim($phy_type) != '' && trim($phy_type) == 'nap'){
			$qyery .= " and patient_data.primary_care_phy_id = '0'";
		}
		if($encounter_type=='initial'){ // ORDER BY date_of_service IS MUST
			$qyery .= " ORDER BY patient_data.lname, patient_data.fname, date_of_service, patient_charge_list.charge_list_id";
		}else{
			$qyery .= " ORDER BY patient_data.lname, patient_data.fname";
		}
		$rs = imw_query($qyery);
		$getChargeListDetails = array();
		while($qryRes=imw_fetch_assoc($rs)){
			$getChargeListDetails[] = $qryRes;
		}
		return $getChargeListDetails;
	}

    function timeNumbers($end=12, $selected=''){
        $opts='';
        $timeSlot=1;
        if($end==59){ $timeSlot=5;}
        for($i=0; $i<=$end;){
            $sel='';
            if($i<10){ $i='0'.$i; }
            if($i==$selected){ $sel='selected'; }
            $opts.='<option value="'.$i.'" '.$sel.'>'.$i.'</option>';
            $i+=$timeSlot;
        }
        return $opts;
    }
	
	function getpatient_Insurance($patient_id){
		$insur_Det=array();
		$qry_chkses=imw_query("select ins_caseid from insurance_case where patient_id='".$patient_id."' and  case_status='Open' order by ins_case_type  LIMIT 0 ,1");
		if(imw_num_rows($qry_chkses)>0){
			$res_ses=imw_fetch_array($qry_chkses);
			 $current_caseids=$res_ses["ins_caseid"];
		}
		$typ=array("'primary'","'secondary'","'tertiary'");
		$type=implode(",",$typ);
		$Ins_nameqry=imw_query("SELECT b.case_name FROM `insurance_case` a ,insurance_case_types b WHERE b.case_id=a.ins_case_type and a.ins_caseid=$current_caseids");
		list($ins_name)=imw_fetch_array($Ins_nameqry);	
		$qry="select insurance_data.id,insurance_data.type,
			insurance_data.policy_number,
			insurance_companies.name 
			from insurance_data
			JOIN 
			insurance_companies ON insurance_companies.id=insurance_data.provider
			where
			insurance_data.pid='$patient_id' and insurance_data.actInsComp=1
			and (insurance_data.type in($type))
			and insurance_data.ins_caseid ='$current_caseids'
			/*and referal_required='Yes'*/  order by insurance_data.type";
		//echo $qry;
		$r=imw_query($qry);
		$instype=array();
		$policy=array();
		if(imw_num_rows($r)>0){
			while($rw=imw_fetch_array($r)){
				
				$insprovider[]=ucwords($rw[3]);
				$policy[]=$rw[2]."#";
			}
			
			$insurance_provider=@implode("<br>",($insprovider));
			$policy_number=@implode("<br>",$policy);
			/*$qry="select reffral_no  from patient_reff where 
				patient_id='$patient_id'
				 and ins_data_id='$id'
				 and  ((patient_reff.end_date >= current_date() and 
				 patient_reff.effective_date <= current_date())
				or(patient_reff.no_of_reffs > 0))
				  order by effective_date desc,reff_id desc limit 0,1";
			//echo $qry;
			$r=imw_query($qry);
			list($reffral_no)=imw_fetch_array($r);	*/ 
			$insur_Det[0]=ucwords($insurance_provider);
			$insur_Det[1]=$policy_number;
		}else{
			$insur_Det=array("N/A","N/A");
		}
		return $insur_Det;
	}
	
	//$fileclue - any pre fix or post fix of file like post fix is ".csv"
	//$pre_or_post - to check if $fileclue is prefix or postfix. Options are "pre" and "post".
	//$days_old - how many old days files need to delete, default is 7 days old files
	function delete_old_files($fileclue='', $pre_or_post='pre', $days_old=7){
		$csv_path= write_html("", "test.txt"); //FOR GETTING CURRENT PATH
		$csv_path=str_replace("test.txt","", $csv_path);

		$file_like= ($pre_or_post=='pre')? $fileclue."*" : "*".$fileclue;
		$files = glob($csv_path.$file_like);
		$now   = time();

		foreach ($files as $file) {
			if (is_file($file)) {
			  if ($now - filemtime($file) >= 60 * 60 * 24 * $days_old) { // Default 7 days
					unlink($file);
			  }
			}
		}
	}
}
?>
