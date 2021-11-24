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
FILE : CLSReport.php
PURPOSE : 
ACCESS TYPE : INCLUDED
*/
class CLSReports{

	function re_get_scheduler_facility_db(){
		try{
			$sql = "select id, name from facility order by name";
			$result = $this->conn->Execute($sql);
			return $result->GetArray();
		}catch(Exception $error){
			die($error.mysql_error());
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
			die($error.mysql_error());
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
			die($error.mysql_error());
		}
	}

	function re_get_pt_copay($patient_id, $case_typeid){

		$total_copay = "";

		$query_pri = "select copay from insurance_data where pid=$patient_id and ins_caseid ='$case_typeid' and type='primary' and actInsComp = '1'"; 
		$sql_pri = @mysql_query($query_pri);
		$rt_pri = @mysql_fetch_array($sql_pri);
		$ins_pri=$rt_pri["copay"];
			
		$copay_policy_qry="SELECT secondary_copay,tertiary_copay FROM `copay_policies`";
		$copay_policy_res=@mysql_query($copay_policy_qry);
		list($secondary_copay ,$tertiary_copay)=@mysql_fetch_array($copay_policy_res);

		$ins_sec=0;
		if(strtolower($secondary_copay) == 'yes'){
			$query_sec = "select copay from insurance_data where pid=$patient_id and ins_caseid ='$case_typeid' and type='secondary' and actInsComp = '1'"; 
			$sql_sec = @mysql_query($query_sec);
			$rt_sec = @mysql_fetch_array($sql_sec);
			$ins_sec=$rt_sec["copay"];
		}
		$ins_ter = 0;
		if(strtolower($tertiary_copay) == 'yes'){
			$query_ter = "select copay from insurance_data where pid=$patient_id  and ins_caseid ='$case_typeid' and type='tertiary' and actInsComp = '1'"; 
			$sql_ter = @mysql_query($query_ter);
			$rt_ter = @mysql_fetch_array($sql_ter);
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
	
	function getICD10Codes($withInvCommas='', $returnData='', $returnOptions='no'){
		$arrICD10Code = array();	
		$arrLaterality= $this->getLaterality();
		
		$orderBy=' icd10 asc';
		if($returnData=='desc'){
			$orderBy=' icd10_desc asc';
		}
		
		$qry = "Select TRIM(icd10) as 'icd10', laterality, staging, severity, TRIM(icd10_desc) as 'icd10_desc' FROM icd10_data WHERE icd10!='' ORDER BY ".$orderBy;
		$rs = mysql_query($qry);
		while($res=mysql_fetch_array($rs)){
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
					$arrICD10Code.='<option value="'.$val_concat.'">'.$dispData.'</option>';					
				}else{
					$arrICD10Code[$val_concat]=$dispData;
				}
			}else{
				if($returnOptions=='yes'){
					if($withInvCommas==''){
						$arrICD10Code.='<option value="\''.$ICD10.'\'">'.$dispData.'</option>';
					}else{
						$arrICD10Code.='<option value="'.$ICD10.'">'.$dispData.'</option>';
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

		return($arrICD10Code);
	}
	function getLaterality(){
		$qry="Select code, under FROM icd10_laterality WHERE deleted='0' AND under>0 ORDER BY id";
		$rs=mysql_query($qry);
		while($res=mysql_fetch_assoc($rs)){
			$arrLaterality[$res['under']][]=$res['code'];
		}
		unset($rs);
		return $arrLaterality;
	}

	function getFacilityName($selFacilities='', $savedSearch='0'){
		$qry = "select pos_facilityies_tbl.facilityPracCode as name,
				pos_facilityies_tbl.pos_facility_id as id,
				pos_tbl.pos_prac_code
				from pos_facilityies_tbl
				left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
				order by pos_facilityies_tbl.headquarter desc,
				pos_facilityies_tbl.facilityPracCode";
		$res = imw_query($qry);
		
		//$return = '<option  value="">All</option>';
		$return = '';
		while($row=imw_fetch_assoc($res)){	
			$id = $row['id'];
			$name = $row['name'];
			$pos_prac_code = $row['pos_prac_code'];
			$sel='';
			//$sel = $i == 0 ? 'selected' : '';
			
			// TO HANDLE SAVED SEARCH
			if($savedSearch=='1'){ $sel=''; }
			if(sizeof($selFacilities)>0){
				if(in_array($id,$selFacilities)) { $sel='selected'; }
			}
			
			$return .= '<option '.$sel.' value="'.$id.'">'.$name.' - '.$pos_prac_code.'</option>';
		}						
		return $return;
	}	
}
?>