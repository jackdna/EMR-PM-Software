<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*
Coded in PHP 7
Purpose: Providing required data to main interface of login (header/footer).
Access Type: include file.
*/
ini_set('memory_limit', '2048M');
set_time_limit(600);
require_once(dirname(__FILE__).'/common_function.php');
class MUR_Reports
{
	public $createdBy,$arr_task,$provider,$dtfrom,$dtupto,$dbdtfrom,$dbdtupto;
	public $task, $taskNum, $int_percent,$facilities,$mur_version;
	
	###################################################################
	#	constructor function to set commonally used variable on page
	###################################################################
	function __construct(){
		/*******INTIAL SETTINGS HERE*****/
		$this->temp_createdBy 	= $this->get_provider_ar($_SESSION['authId']);
		$this->createdBy 		= $this->temp_createdBy[$_SESSION['authId']];
		
		$this->arr_task 		= array('Invalid','Cal-Core/Menu','Cal- CMS', 'Analyze', 'Attestation', 'PQRI XML');
		$this->taskNum 			= isset($_REQUEST['task']) ? intval($_REQUEST['task']) : 0;
		//$this->task 			= $arr_task[$this->taskNum];
		
		$this->provider 		= isset($_REQUEST['provider']) ? trim(strip_tags($_REQUEST['provider'])) : 0;
		$this->dtfrom 			= isset($_REQUEST['dtfrom']) ? trim(strip_tags($_REQUEST['dtfrom'])) : 0;
		$this->dtupto 			= isset($_REQUEST['dtupto']) ? trim(strip_tags($_REQUEST['dtupto'])) : 0;

		$this->dbdtfrom			= getDateFormatDB($this->dtfrom);
		$this->dbdtupto			= getDateFormatDB($this->dtupto);
		$this->mur_version		= '';
	}
	
	/****GET USER DETAILS*****************/
	function getUserDetails($id){
		$r = imw_query("SELECT * FROM users WHERE id IN ($id)");
		if($r && imw_num_rows($r)>0){
			$ret = array();
			while($rs = imw_fetch_assoc($r)){
				$ret[] = $rs;
			}
			return $ret;
		}
		return false;
	}
	
	/****GET PRACTICE GROUP DETAILS*****************/
	function getPhysicianGroupInfo($id){
		$ret = false;
		$r = imw_query("SELECT * FROM groups_new WHERE gro_id IN ($id)");
		if($r && imw_num_rows($r)>0){
			$ret = array();
			while($rs = imw_fetch_assoc($r)){
				$ret[] = $rs;
			}
		}
		return $ret;
	}
	
	/****USED IN DROPDOWN AND OTHER MAPPING****/
	function get_provider_ar($proId = 0){
		$count = explode(',',$proId);
		if($proId == 0){
			$qry_providers = "select id, fname, lname, mname,user_npi from users where user_type IN (1,12) AND delete_status = 0 order by lname";
		}else if(count($count)>1){
			$qry_providers = "select id, fname, lname, mname from users where id IN ($proId) ORDER BY lname";
		}else{
			$qry_providers = "select id, fname, lname, mname from users where id='$proId' LIMIT 0,1";	
		}
		$result = imw_query($qry_providers);
		if($result && imw_num_rows($result)>=1){
			while($rs = imw_fetch_assoc($result)){
				$id = $rs['id'];
				$arr_proName['LAST_NAME']=$rs['lname'];
				$arr_proName['FIRST_NAME']=$rs['fname'];
				$arr_proName['MIDDLE_NAME']=$rs['mname'];
				if($proId==0){
					$pro_name[$id]['name'] = changeNameFormat($arr_proName);
					$pro_name[$id]['npi'] = $rs['user_npi'];
				}else{
					$pro_name[$id] = changeNameFormat($arr_proName);
				}
			}//end of while.
			return $pro_name;
		}
		else
			return false;
	}//end of function get_provider_ar;
	
	/******USED TO FILTER DENOMINATOR ONLY FOR FACILITIES WHICH ARE CURRENTLY ACTIVE IN PRACTICE*******/
	function get_active_facilities(){
		$fac_IDs = '';
		$q = "SELECT id FROM facility";
		$result = imw_query($q);
		if($result && imw_num_rows($result)>=1){
			$temp_arr_facs = array();
			while($rs = imw_fetch_assoc($result)){
				$temp_arr_facs[] = $rs['id'];
			}
			$fac_IDs = implode(',',$temp_arr_facs);	
		}
		return $fac_IDs;
	}
	
	function get_tin_options(){
		$res = imw_query("SELECT id,name,fac_tin FROM facility WHERE fac_tin != ''");
		if($res && imw_num_rows($res)>0){
			$ar = array();
			while($rs = imw_fetch_assoc($res)){
				$ar[$rs['id']] = $rs;
			}
			return $ar;
		}
		return false;
	}
	
	function get_tin_options2(){
		$res = imw_query("SELECT fac_tin,GROUP_CONCAT(id) AS id, GROUP_CONCAT(name SEPARATOR ', ') AS name FROM facility WHERE fac_tin != '' GROUP BY fac_tin");
		if($res && imw_num_rows($res)>0){
			$ar = array();
			while($rs = imw_fetch_assoc($res)){
				$id = $rs['id']; unset($rs['id']);
				$ar[$id] = $rs;
			}
			return $ar;
		}
		return false;
	}
	
	function get_denominator($pro_id='', $age='',$ageMethod=''){
		if($this->mur_version!='2019'){
			$Sch_facility_IDs = $this->get_active_facilities();
		}
		
		if($_REQUEST['facility_id'] && $_REQUEST['facility_id'] != ''){
			$Sch_facility_IDs = $_REQUEST['facility_id'];
		}else{
			if($this->mur_version=='2019'){
				die('TIN information not found.');
			}
		}
		$this->facilities = $Sch_facility_IDs;
		
		if($pro_id != '') $this->provider = $pro_id;
		
		$chkDt='';
		$ptIDs = array(); $ptAge = array();
		if(!empty($age)){
			$currDt = date('Y-m-d');
			if($ageMethod=='yes') {
				$ArrDtFrom = explode('-',$this->dtfrom);
				$mm	= $ArrDtFrom[0];
				$dd = $ArrDtFrom[1];
				$yy = $ArrDtFrom[2];
				$currDt = date('Y-m-d',mktime(0,0,0,$mm,$dd,$yy));
	
			}
			$ArrCurrDate = explode('-',$currDt);
			$year	= $ArrCurrDate[0];
			$month	= $ArrCurrDate[1];
			$day	= $ArrCurrDate[2];
			
			$ArAge = explode('-',$age);
			$ageFrom	= $ArAge[0];
			$ageUpto	= $ArAge[1];
			$ageFrom = date('Y-m-d',mktime(0,0,0,$month,$day,$year-$ageFrom));
			$ageUpToQry = "";
			if(isset($ageUpto) || $ageUpto!=''){
				$ageUpto = date('Y-m-d',mktime(0,0,0,$month,$day,$year-$ageUpto));
				$ageUpToQry = " AND pd.DOB>='".$ageUpto."' ";
			}
		}
		
		if(empty($age)) {
			$query = "SELECT distinct(appt.sa_patient_id) FROM schedule_appointments appt 
						INNER JOIN patient_data pd ON (pd.pid=appt.sa_patient_id AND pd.lname != 'doe') 
						WHERE appt.sa_doctor_id IN (".$this->provider.") 
						AND pd.id <> 0 AND pd.pid <> 0 
						AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) AND appt.sa_facility_id IN ($Sch_facility_IDs) 
						AND date_format(appt.sa_app_start_date,'%Y-%m-%d')>='".$this->dbdtfrom."' 
						AND date_format(appt.sa_app_start_date,'%Y-%m-%d')<='".$this->dbdtupto."' ";
		}else{
			$query = "SELECT distinct(appt.sa_patient_id), pd.DOB as dob FROM schedule_appointments appt 
						INNER JOIN patient_data pd ON (pd.pid=appt.sa_patient_id AND pd.lname != 'doe' AND pd.DOB<='".$ageFrom."' $ageUpToQry)
						WHERE appt.sa_doctor_id IN (".$this->provider.") 
						AND pd.id <> 0 AND pd.pid <> 0 
						AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) AND appt.sa_facility_id IN ($Sch_facility_IDs) 
						AND date_format(appt.sa_app_start_date,'%Y-%m-%d')>='".$this->dbdtfrom."' 
						AND date_format(appt.sa_app_start_date,'%Y-%m-%d')<='".$this->dbdtupto."' ";
			//echo '<br><br>'.$query;
		}
       // echo $query;
       // echo "<br />";
	//	if($ageMethod=='yes'){echo $query;}
		$result = imw_query($query);
		if($result && imw_num_rows($result)>=1){
			while($rs = imw_fetch_assoc($result)){
				if(in_array($rs['sa_patient_id'],$ptIDs)) continue;
				$ptIDs[] = $rs['sa_patient_id'];
				if(!empty($age)){
					$ptAge[] = $rs['dob'];
				}
			}
			if($ageMethod=='yes'){return array($ptIDs,$ptAge);}
			else{return $ptIDs;}
		}
		else{
			return $ptIDs;
		}
	}
	
	
	function aged_get_denominator($provider,$age){
		$patientIDs = $this->get_denominator($provider,$age);
		$totalPtIDs = implode(', ',$patientIDs);
		if(!$totalPtIDs || empty($totalPtIDs)){$totalPtIDs = '0';}
		return $totalPtIDs;
	}//end of aged_get_denominator
	
	function getPatientsByAge($age='',$commaIDs=''){
		$pts = array(0);
		if(!empty($age)){
			$currDt = date('Y-m-d');
			if($ageMethod=='yes') {
				$ArrDtFrom = explode('-',$this->dtfrom);
				$mm	= $ArrDtFrom[0];
				$dd = $ArrDtFrom[1];
				$yy = $ArrDtFrom[2];
				$currDt = date('Y-m-d',mktime(0,0,0,$mm,$dd,$yy));
	
			}
			$ArrCurrDate = explode('-',$currDt);
			$year	= $ArrCurrDate[0];
			$month	= $ArrCurrDate[1];
			$day	= $ArrCurrDate[2];
			
			$ArAge = explode('-',$age);
			$ageFrom	= $ArAge[0];
			$ageUpto	= $ArAge[1];
			$ageFrom = date('Y-m-d',mktime(0,0,0,$month,$day,$year-$ageFrom));
			$ageUpToQry = "";
			if(isset($ageUpto) || $ageUpto!=''){
				$ageUpto = date('Y-m-d',mktime(0,0,0,$month,$day,$year-$ageUpto));
				$ageUpToQry = " AND DOB>='".$ageUpto."' ";
			}
		}
		$id_query="";
		if($commaIDs!=''){$id_query="id IN (".$commaIDs.") AND ";}
		$q = "SELECT DISTINCT(id) AS patient_id FROM patient_data WHERE ".$id_query."DOB<='".$ageFrom."' $ageUpToQry";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$pts = array();
			while($rs=imw_fetch_assoc($res)){
				$pts[] = $rs['patient_id'];
			}
		}
		return implode(',',$pts);
	}
	
	function get_percentage($full,$obtained,$goal=''){
		if($full==0){return 'NA';}
		$taskNum = $this->taskNum;
		$achieved = round(($obtained*100)/$full);
		$this->int_percent = $achieved;

		if($taskNum==3 && !empty($goal)){
			$achieved = $achieved.'%';
			if(str_replace('%','',$achieved) < str_replace('%','',$goal)){
				$color = 'ff0000';
				$achieved = '<b style="color:#'.$color.'">'.$achieved.'</b> ('.$goal.'%)';
			}else{
				$achieved = $achieved.' ('.$goal.'%)';
			}
		}else{
			$achieved.='%';
		}
		return $achieved;
	}//end of get_percentage;
	
	/*****POINTS CALCULATION****/
	function pointsMeasureWise($measure,$int_percent){
		$points = 0;
		if($measure=='SummaryCareRec2' || $measure=='PtViewAccess'){
			if($int_percent>0 && $int_percent <= 10) 			$points += 2;
			else if($int_percent>10 && $int_percent <= 20) 		$points += 4;
			else if($int_percent>20 && $int_percent <= 30) 		$points += 6;
			else if($int_percent>30 && $int_percent <= 40) 		$points += 8;
			else if($int_percent>40 && $int_percent <= 50) 		$points += 10;
			else if($int_percent>50 && $int_percent <= 60) 		$points += 12;
			else if($int_percent>60 && $int_percent <= 70) 		$points += 14;
			else if($int_percent>70 && $int_percent <= 80) 		$points += 16;
			else if($int_percent>80 && $int_percent <= 90) 		$points += 18;
			else if($int_percent>90 && $int_percent <= 100)		$points += 20;
		}else{
			if($int_percent>0 && $int_percent <= 10) 			$points += 1;
			else if($int_percent>10 && $int_percent <= 20) 		$points += 2;
			else if($int_percent>20 && $int_percent <= 30) 		$points += 3;
			else if($int_percent>30 && $int_percent <= 40) 		$points += 4;
			else if($int_percent>40 && $int_percent <= 50) 		$points += 5;
			else if($int_percent>50 && $int_percent <= 60) 		$points += 6;
			else if($int_percent>60 && $int_percent <= 70) 		$points += 7;
			else if($int_percent>70 && $int_percent <= 80) 		$points += 8;
			else if($int_percent>80 && $int_percent <= 90) 		$points += 9;
			else if($int_percent>90 && $int_percent <= 100)		$points += 10;
		}
		
		if($points==0) $points = '-';
		return $points;
	}
	
	function get_patients_by_visits($patients, $no_of_visits = 0, $ageMethod='',$begDate='',$excludeTemplate='',$begEndDate=''){
		if($begDate!=''){$dtfrom1 = $begDate;}
		if($begEndDate!=''){$dtupto1 = $begEndDate;}
		if(empty($patients)) return;
		$ptIDs = array(); $ptIDs1 = array(); $arr_key_val = array();
		if($ageMethod=='yes'){
			$comma_patients = implode(', ',$patients[0]);
			for($i=0; $i<count($patients[0]); $i++){
				$arr_key_val[$patients[0][$i]] = $patients[1][$i];
			}
		}else{
			$comma_patients = $patients;
		}
	
		$querypart1 = "";	
		if($excludeTemplate!=''){
			$querypart1 = " AND templateId NOT IN ($excludeTemplate)";
		}
		$query = "SELECT count(id) cnt, patient_id 
				  FROM chart_master_table 
				  WHERE patient_id IN($comma_patients) AND delete_status='0' AND purge_status='0' 
				  	AND (date_of_service>='".$this->dbdtfrom."' AND date_of_service<='".$this->dbdtupto."') 
					".$querypart1." 
					GROUP BY patient_id 
						HAVING cnt >= '$no_of_visits'";
		$result = imw_query($query);
		if($result && imw_num_rows($result)>=1){
			while($rs = imw_fetch_assoc($result)){
				$ptIDs[] = $rs['patient_id'];
			}
			if($ageMethod != 'yes'){
				return $ptIDs;
			}
			else{
				for($j=0; $j<count($ptIDs); $j++){
					if(array_key_exists($ptIDs[$j],$arr_key_val)){
						$ptIDs1[$ptIDs[$j]] = $arr_key_val[$ptIDs[$j]];
					}
				}
				return $ptIDs1;
			}
		}
		else{
			return $ptIDs;
		}
	}//end of get_patietn_by_visits.
	
	function format_values($arr_val,$full='',$server=''){
		$taskNum = $this->taskNum;
		$arr_val['denominator'] = isset($arr_val['denominator']) 	? $arr_val['denominator'] 	: array();
		$arr_val['numerator'] 	= isset($arr_val['numerator']) 		? $arr_val['numerator'] 	: array();
		$arr_val['exclusion'] 	= isset($arr_val['exclusion']) 		? $arr_val['exclusion'] 	: '';
		$arr_val['specialcase'] = isset($arr_val['specialcase']) 	? $arr_val['specialcase'] 	: '';
		
		$arr_DenoPtIDs = $arr_val['denominator'];
		$arr_NumePtIDs = $arr_val['numerator'];
		$arr_ExcuPtIDs = $arr_val['exclusion'];
		$arr_AntiPtIDs = $arr_val['anti'];
		$str_specialcase = $arr_val['specialcase'];
		if($server=='noPt' || $server=='noPt1'){
			$arr_val['denominator'] = $arr_val['denominator'];
			$arr_val['numerator'] 	= $arr_val['numerator'];
		}else{
			$arr_val['denominator'] = count($arr_val['denominator']);
			$arr_val['numerator'] 	= count($arr_val['numerator']);
			$arr_val['anti'] 		= count($arr_val['anti']);
		}
		if(strtolower($full)!='exempt'){
			$arr_val['percent'] = $this->get_percentage(($arr_val['denominator']),$arr_val['numerator'],$full);
			if($server == 'nored' && $arr_val['denominator'] < 100) $arr_val['percent'] = strip_tags($arr_val['percent']);
			if($server=='FAIL' && intval($arr_val['denominator']) > 0){
				if(intval($arr_val['numerator']) > 0){$arr_val['percent'] = 'PASS';}
				else if(intval($arr_val['numerator']) == 0){$arr_val['percent'] = 'FAIL';}
			}
			$arr_val['exclusion'] = !empty($arr_val['exclusion']) ? count($arr_val['exclusion']) : 0;
			$temp_full_numerator = $arr_val['numerator'];
			//$arr_val['numerator'] = $arr_val['numerator'] - $arr_val['exclusion'];
				
			if($taskNum == 3 && !empty($full) && $arr_val['denominator']>0 && $arr_val['numerator']>=0){// if used viewing in Analyze Mode..
				$nume_should_be = round(($arr_val['denominator'] * $full)/100);
		/*THIS IS COMMENTED 		
				$deno_should_be = round(($arr_val['numerator'] * 100)/$full);
				if($arr_val['denominator'] < $deno_should_be){
					$arr_val['denominator'] =  $arr_val['denominator'].' ('.($deno_should_be - $arr_val['denominator']).')';
				}
		*/		
				if($temp_full_numerator < $nume_should_be){
					$arr_val['numerator'] =  $arr_val['numerator'].' ('.($nume_should_be - $temp_full_numerator).')';
				}
			}


			if(((defined('APP_DEBUG_MODE') && constant('APP_DEBUG_MODE')==1) || $this->get_MUR_audit_status()) && ($server!='noPt1')){//just to check which patients are appearing.
				if(!isset($str_specialcase)){$str_specialcase = '';}
					asort($arr_DenoPtIDs);
					asort($arr_NumePtIDs);
					asort($arr_ExcuPtIDs);
					asort($arr_AntiPtIDs);


if($server!='noPt')
{
					$arr_val['denominator'] = '<span class="link_cursor" onDblClick = "showPTs(\''.implode(', ',$arr_DenoPtIDs).'\',this,\'Denominator\',\''.$str_specialcase.'\')">'.$arr_val['denominator'].'</span>';
					$arr_val['numerator'] = '<span class="link_cursor" onDblClick = "showPTs(\''.implode(', ',$arr_NumePtIDs).'\',this,\'Numerator\',\''.$str_specialcase.'\')">'.$arr_val['numerator'].'</span>';
					$arr_val['exclusion'] = '<span class="link_cursor" onDblClick = "showPTs(\''.implode(', ',$arr_ExcuPtIDs).'\',this,\'Exclusion\',\''.$str_specialcase.'\')">'.$arr_val['exclusion'].'</span>';
					$arr_val['anti'] = '<span class="link_cursor" onDblClick = "showPTs(\''.implode(', ',$arr_AntiPtIDs).'\',this,\'Anti-Numerator\',\''.$str_specialcase.'\')">'.$arr_val['anti'].'</span>';
}

else
{

 $arr_val['denominator'] = '<span class="link_cursor" onDblClick = "showPTs(\''.$arr_val['denum_pid'].'\',this,\'Denominator\',\''.$str_specialcase.'\')">'.$arr_val['denominator'].'</span>';
                                        $arr_val['numerator'] = '<span class="link_cursor" onDblClick = "showPTs(\''.$arr_val['num_pid'].'\',this,\'Numerator\',\''.$str_specialcase.'\')">'.$arr_val['numerator'].'</span>';

}
		
			if($server=='Boston'){
				$arr_val['percent']		= 'N/A';
				$arr_val['denominator']	= 0;
				$arr_val['numerator']	= 0;
				$arr_val['exclusion']	= 0;
			}
		}else{
			$arr_val['percent']		= 'Exempt';
			$arr_val['denominator']	= '';
			$arr_val['numerator']	= '';
			$arr_val['exclusion']	= '';	
		}
		return $arr_val;
	}//end of format_values.
	
	function get_MUR_audit_status(){
		$return = 0;
		$query = "select mur_audit from copay_policies where policies_id='1' limit 0,1";
		$result = imw_query($query);
		if($result && imw_num_rows($result)==1){
			$rs = imw_fetch_assoc($result);
			$return = $rs['mur_audit'];
		}
		return $return;
	}

	function get_ocular_medArray(){
		$return = array();
		$result = imw_query("SELECT medicine_name FROM medicine_data WHERE ocular=1 AND prescription=1 AND del_status = '0'");
		if($result && imw_num_rows($result)>0){
			while($rs = imw_fetch_assoc($result)){
				$return[] = $rs['medicine_name'];
			}
		}
		return $return;
	}

	//TO GET ARRAY OF PATIENT-IDs for provided query
	function getPtIdFun($query,$ptId) {
		$pidArr = array();
		$result = imw_query($query);
		if($result && imw_num_rows($result)>=1){
			while($rs 		= imw_fetch_assoc($result)){
				$pidArr[] 	= $rs[$ptId];
			}
			$pidArr = array_unique($pidArr);
		}
		return $pidArr;
	}

	//START FUNCTION GET PATIENT FROM CHART-SUPERBILL join
	function chart_superbill_patients($p,$procs,$dtfrom1,$dtupto1){
		$array_needle	= explode('|',$procs);	
		$return_ptIDs = array();
		
		$q1 = "SELECT id as form_id FROM chart_master_table WHERE patient_id IN ($p) and (date_of_service BETWEEN '".$dtfrom1."' AND '".$dtupto1."') AND purge_status='0'";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)>0){
			$form_ids = array();
			while($rs1 = imw_fetch_assoc($res1)){
				$form_ids[] = $rs1['form_id'];
			}
			imw_free_result($res1);
			unset($q1);
			
			//IF CHART IDs FOUND
			if(count($form_ids)>0){
				$all_chart_IDs = implode(',',$form_ids); unset($form_ids);
				$q2 = "SELECT patientId, procOrder FROM superbill WHERE del_status='0' AND formid IN ($all_chart_IDs)";
				$res2 = imw_query($q2);
				if($res2 && imw_num_rows($res2)){
					while($rs2 = imw_fetch_assoc($res2)){
						$pt_id		= $rs2['patientId'];
						$procOrder	= $rs2['procOrder'];
						foreach($array_needle as $needle){
							if(stristr($procOrder, $needle)) $return_ptIDs[] = $pt_id;
						}
					}
				}
				
			}
		}
		return array_unique($return_ptIDs);
	}
	
	//function to get comma separated vals from array+ vise versa
	function comma_n_array($arrORstr,$do='implode'){
		if($do=='implode' && is_array($arrORstr)){
			if(count($arrORstr)>0){
				return implode(', ',$arrORstr);
			}else{
				return 0;
			}
		}else if($do=='explode' && !is_array($arrORstr)){
			if(strlen($arrORstr)>0){
				return explode(', ',$arr);
			}else{
				return array();
			}
		}
	}//end of function to get comma separated vals from array+ vise versa
	
	
	
	
	
	/*************************MEASURE COUNTING FUNCTIONS BELOW******************/
	function getCPOE($type,$totalPtIDs=''){
		$denoNumExcl = array();
		$ptIDs1 = array(); $ptIDs2 = array(); $ptIDs3 = array();
		$ptIDsEPrescribe = $ptIDsLab = $ptIDsRad = $ptIDsChartAssPln = array();
		
		//GETTING DENOMINATOR
		if($totalPtIDs==''){
			$totalHandMeds 		= 0;//getHandWritten_Orders($provider,$dtfrom1,$dtupto1,$type,$totalPtIDs);
		}else{
			$totalHandMeds 		= $this->getHandWritten_Orders($this->provider,$this->dbdtfrom,$this->dtupto,$type,$totalPtIDs);	
		}
		$totalCPOEorders 	= $this->getCPOE_Orders($this->provider,$this->dbdtfrom,$this->dtupto,$type,$totalPtIDs);
		$arr_EPrescribe		= $this->getEPrescribe($totalPtIDs);
		$denoNumExcl['denominator'] = $totalHandMeds+$totalCPOEorders;
		$denoNumExcl['numerator'] = $totalCPOEorders;
		if($type=='Meds'){
			$denoNumExcl['denominator'] += intval($arr_EPrescribe['denominator']);
			$denoNumExcl['numerator'] += intval($arr_EPrescribe['numerator']);
		}
		$denoNumExcl['exclusion'] = array();//get_excluded_patients($denoNumExcl['numerator'],array('cpoe'));
		return $denoNumExcl;
	}
	
	function getHandWritten_Orders($provider,$dtfrom1,$dtupto1,$type,$totalPtIDs=''){
		$fromCheckList = "";
		if($totalPtIDs==''){
			$fromCheckList = "cmt.providerId IN (".$provider.") AND ";
			$ptIDs = $this->get_denominator($provider);
			$totalPtIDs = implode(', ',$ptIDs);
		}
		if($totalPtIDs=='0') return;
		
		$totalHandWritten = 0;
		$field = "cap.rxhandwritten";
		if($type=='Labs') 			$field = "cap.labhandwritten";
		if($type=='Imaging/Rad') 	$field = "cap.imagehandwritten";
		$query = "SELECT SUM($field) AS total_handwritten FROM chart_assessment_plans cap 
		JOIN chart_master_table cmt ON (cap.form_id=cmt.id AND cap.patient_id=cmt.patient_id) 
		WHERE ".$fromCheckList."$field > 0 AND (cmt.date_of_service BETWEEN '$dtfrom1' AND '$dtupto1') 
		AND cmt.patient_id IN ($totalPtIDs)";
		//echo 'HW: '.$query.'<hr>';
		$result = imw_query($query);
		if($result && imw_num_rows($result)>=1){
			$rs = imw_fetch_assoc($result);
			$totalHandWritten = $rs['total_handwritten'];
		}
		return $totalHandWritten;
	}



        function getHandWritten_Orders_patient_id($provider,$dtfrom1,$dtupto1,$type,$totalPtIDs=''){
		$fromCheckList = "";
		if($totalPtIDs==''){
			$fromCheckList = "cmt.providerId IN (".$provider.") AND ";
			$ptIDs = $this->get_denominator($provider);
			$totalPtIDs = implode(', ',$ptIDs);
		}
		if($totalPtIDs=='0') return;
		
		$totalHandWritten = 0;
		$field = "cap.rxhandwritten";
		if($type=='Labs') 			$field = "cap.labhandwritten";
		if($type=='Imaging/Rad') 	$field = "cap.imagehandwritten";
		$query = "SELECT group_concat(cmt.patient_id) as patient_id FROM chart_assessment_plans cap 
		JOIN chart_master_table cmt ON (cap.form_id=cmt.id AND cap.patient_id=cmt.patient_id) 
		WHERE ".$fromCheckList."$field > 0 AND (cmt.date_of_service BETWEEN '$dtfrom1' AND '$dtupto1') 
		AND cmt.patient_id IN ($totalPtIDs)";
		//echo 'HW: '.$query.'<hr>';
		$result = imw_query($query);
		if($result && imw_num_rows($result)>=1){
			$rs = imw_fetch_assoc($result);
			$totalHandWritten = $rs['patient_id'];
		}
		return $totalHandWritten;
	}
	
	function getCPOE_Orders($provider,$dtfrom1,$dtupto1,$type,$totalPtIDs=''){
		if($totalPtIDs==''){
			$ptIDs = $this->get_denominator($provider);
			$totalPtIDs = implode(', ',$ptIDs);
		}
		if($totalPtIDs=='0') return;
		
		$query2 ="SELECT COUNT(order_set_associate_details_id) AS total_orders FROM order_set_associate_chart_notes osacn 
				  JOIN order_set_associate_chart_notes_details osacnd ON (osacnd.order_set_associate_id=osacn.order_set_associate_id) 
				  JOIN order_details od ON (od.id=osacnd.order_id AND od.o_type='".$type."') 
				  JOIN chart_master_table cmt ON (cmt.id = osacn.form_id) 
				  WHERE osacn.delete_status='0' AND osacnd.delete_status='0' AND osacn.patient_id IN ($totalPtIDs) ";
		//		  AND osacn.logged_provider_id IN (".$provider.") 
		$query2 .= "AND (DATE_FORMAT(cmt.date_of_service,'%Y-%m-%d') BETWEEN '$dtfrom1' AND '$dtupto1')";
		$result2 = imw_query($query2);//echo 'CPOE: '.$query2.'<hr>';
		$results = 0;
		if($result2 && imw_num_rows($result2)>=1){
			while($rs2 = imw_fetch_assoc($result2)){
				$results = $rs2['total_orders'];
			}
		}
		return $results;
	}

	function getEPrescribe($totalPtIDs){
		//Stage 1- Core (#2)
		//if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$totalHandWritten = $this->getHandWritten_Orders($this->provider,$this->dbdtfrom,$this->dbdtupto,'Meds');
                $denum_pid= $this->getHandWritten_Orders_patient_id($this->provider,$this->dbdtfrom,$this->dbdtupto,'Meds');

		//die('Null');
		$total_erx = 0;
		$query2 = "SELECT SUM(prescriptions) AS total_erx FROM emdeon_erx_count WHERE 
					provider_id IN (".$this->provider.") AND (date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
		$result2 = imw_query($query2);
		if($result2 && imw_num_rows($result2)>=1){
			$rs2 = imw_fetch_assoc($result2);
			$total_erx = $rs2['total_erx'];
		}


                 $query3 = "SELECT  GROUP_CONCAT( patient_id ) AS pid FROM emdeon_erx_count WHERE
                                        provider_id IN (".$this->provider.") AND patient_id!='' AND (date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
                $result3 = imw_query($query3);
                if($result3 && imw_num_rows($result3)>=1){
                        $rs3 = imw_fetch_assoc($result3);
                        $num_pid = $rs3['pid'];
               $num_pid_unique= implode(',',array_unique(explode(',', $num_pid)));
                }
                //creating pid list for denum
                if(!empty($denum_pid)){
                $denum_pid=$denum_pid.",".$num_pid;
                }else { $denum_pid= $num_pid; } 
                $denum_pid_unique=implode(',',array_unique(explode(',', $denum_pid)));
                
		
		$denoNumExcl['denominator'] = intval($totalHandWritten)+intval($total_erx);
		$denoNumExcl['numerator'] = intval($total_erx);
		$denoNumExcl['exclusion'] = array();
                $denoNumExcl['num_pid'] = $num_pid_unique;
                $denoNumExcl['denum_pid'] = $denum_pid_unique;
		return $denoNumExcl;
	}
	
	function getTimelyPtElectHlthInfo($totalPtIDs){
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$denoNumExcl['denominator'] = explode(',',$totalPtIDs);
		$query 	= "SELECT id FROM patient_data 
					WHERE id IN($totalPtIDs) 
					AND ((username != '' AND password != '') 
						  OR (temp_key!='' AND temp_key_expire!='yes' AND temp_key_chk_val='1' AND ".constant('IPORTAL')."='1')) AND locked = '0'"; 
		$denoNumExcl['numerator'] = array();
		$result = imw_query($query);
		if($result && imw_num_rows($result)>=1){
			$ptIDs = array();
			while($rs = imw_fetch_assoc($result)){
				$ptIDs[] = $rs['id'];
			}
			$denoNumExcl['numerator'] = $ptIDs;
		}
		$denoNumExcl['anti'] = array_diff($denoNumExcl['denominator'],$denoNumExcl['numerator']);
		$denoNumExcl['exclusion'] = array();//get_excluded_patients($denoNumExcl['numerator'],array('timelyPtElectHlthInfo'));
		return $denoNumExcl;
	}// end of getElectronicHealthInfo

	function getTimelyPtElectHlthInfo2020($totalPtIDs){
		if($totalPtIDs=='0' || empty($totalPtIDs)) return;
		$denoNumExcl = array();
		$query = "SELECT id, patient_id, date_of_service FROM chart_master_table WHERE patient_id IN($totalPtIDs) AND delete_status='0' 
					AND purge_status='0' AND (date_of_service>='".$this->dbdtfrom."' AND date_of_service<='".$this->dbdtupto."')";
		$result = imw_query($query);
		if($result && imw_num_rows($result)>=1){
			$denoPts = $numePts = array();
			while($rs = imw_fetch_assoc($result)){
				$denoPts[]  = $rs['patient_id'];
				$pt_dos		= $rs['date_of_service'];
				$form_id	= $rs['id'];
				//--PROCEED FOR NUMERATOR CHECK IF PATIENT HAVE PT.PORTAL ACCESS--
				if(constant('IPORTAL')=='1'){
					$PortalRes = imw_query("SELECT id FROM patient_data WHERE id ='".$rs['patient_id']."' AND ".constant('IPORTAL')."='1' AND 
					((username != '' AND password != '') OR (temp_key!='' AND temp_key_expire!='yes' AND temp_key_chk_val='1')) AND locked = '0'");
					if($PortalRes && imw_num_rows($PortalRes)>=1){
						//--CHECK IF CHART FINALIZED WITHIN 4 WORKING DAYS OF DOS.--
						$CslRes=imw_query("SELECT DATE_FORMAT(dttime,'%Y-%m-%d') as finalized_date FROM `chart_save_log` WHERE form_id='".$form_id."' AND finalized='1' ORDER BY dttime LIMIT 0,1");
						//check if finalize need to be done in reporting period or not.
						if($CslRes && imw_num_rows($CslRes)==1){
							$CslRs = imw_fetch_assoc($CslRes);
							$finalize_date = $CslRs['finalized_date'];
							$WorkingDW = getWorkingDaysWithin($pt_dos,$finalize_date);//if finalized within 4 days.
							if($WorkingDW<=4){
								$numePts[] = $rs['patient_id'];
							}
						}
					}
				}
			}
			$denoNumExcl['denominator']	= array_unique($denoPts);
			$denoNumExcl['numerator']	= array_unique($numePts);
		}		
		
		$denoNumExcl['anti'] = array_diff($denoNumExcl['denominator'],$denoNumExcl['numerator']);
		$denoNumExcl['exclusion'] = array();//get_excluded_patients($denoNumExcl['numerator'],array('timelyPtElectHlthInfo'));
		return $denoNumExcl;
	}// end of getElectronicHealthInfo
	
	function getTimelyPtInfoViewed($totalPtIDs){
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$denoNumExcl['denominator'] = explode(',',$totalPtIDs);
		$query 	= "SELECT DISTINCT(pd.id) FROM patient_data pd 
				   JOIN patient_loginhistory plh ON (plh.patient_id=pd.id) 
				   WHERE pd.id IN($totalPtIDs) AND ((pd.username != '' AND pd.password != '') OR (pd.temp_key!='' AND pd.temp_key_expire!='yes')) 
				   AND (DATE_FORMAT(plh.logindatetime,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
		$result = imw_query($query);
		$ptIDs = array();
		if($result && imw_num_rows($result)>=1){
			while($rs = imw_fetch_assoc($result)){
				$ptIDs[] = $rs['id'];
			}
		}
		$denoNumExcl['numerator'] = $ptIDs;
		$denoNumExcl['exclusion'] = array();//get_excluded_patients($denoNumExcl['numerator'],array('timelyPtElectHlthInfo'));
		return $denoNumExcl;
	}
	
	function getEduResourceToPt($totalPtIDs,$EduDocFrom=''){
		if($totalPtIDs=='0') return;
		if($EduDocFrom!='') $dtfrom1 = $EduDocFrom;
		else $dtfrom1 =$this->dbdtfrom;
		
		$denoNumExcl = array();
		$denoNumExcl['denominator'] = explode(',',$totalPtIDs);
		$query 	= "SELECT DISTINCT(p_id) FROM document_patient_rel WHERE p_id IN($totalPtIDs) 
				   AND (DATE_FORMAT(date_time,'%Y-%m-%d') BETWEEN '".$dtfrom1."' AND '".$this->dbdtupto."')";    
		$denoNumExcl['numerator'] = $this->getPtIdFun($query,'p_id');
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;
	}
	
	function getMedReconcil($totalPtIDs){//old logic below, new logic pending.
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$ptIDs = $ptIDs2 = array();
		$q1 = "SELECT DISTINCT(patient_id) FROM patient_multi_ref_phy pmrp 
				JOIN patient_data pd ON (pmrp.patient_id=pd.id) 
				JOIN refferphysician rf ON (rf.physician_Reffer_id=pmrp.ref_phy_id) 
				LEFT JOIN users u ON (u.id=pd.providerID AND u.user_npi!=rf.NPI) 
				WHERE pmrp.patient_id IN ($totalPtIDs)"; 
		//		AND (pd.date BETWEEN '$dtfrom1' AND '$dtupto1')";
	
		$ptIDs = $this->getPtIdFun($q1,'patient_id');
		$denoNumExcl['denominator'] = $ptIDs; //No. of cares where the EP was receiving party.
		
		if(count($denoNumExcl['denominator'])>0){
			$totalPtIDs2 = implode(',',$ptIDs);
			$q2 = "SELECT DISTINCT(patient_id) FROM patient_last_examined WHERE patient_id IN($totalPtIDs2) 
					AND LOWER(section_name) IN ('medications','complete') AND save_or_review = '2' 
					AND (DATE_FORMAT(created_date,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
			$ptIDs2 = $this->getPtIdFun($q2,'patient_id');
			$denoNumExcl['numerator'] = $ptIDs2; //No. of cares, for which Reconcillation was performed (med Reviewed).
		}
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;
	}// end of getMedReconcil
	
	function getMedReconcil_2017($totalPtIDs){
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		/***GETTING PATIENTS FOR WHOM, A CCD DOCUMENT RECIEVED VIA DIRECT OR UPLOADED**/
		$RecvdCCD = $this->ReceiveSummaryCareRec($totalPtIDs);
		$denoNumExcl['denominator'] = $RecvdCCD['denominator'];

		if(count($denoNumExcl['denominator'])>0){
			$mu_year = substr($this->dbdtfrom,0,4);
			$st_from = $mu_year.'-01-01';
			$st_upto = $mu_year.'-12-31';
			
			$deno_sch_ids = implode(',',$denoNumExcl['denominator']);
			$q2 = "SELECT DISTINCT(sdt.sch_id) AS sch_id FROM ".constant('IMEDIC_SCAN_DB').".scan_doc_tbl sdt 
				   JOIN ".constant('IMEDIC_IDOC').".ccd_incorporate_log cil ON (cil.scan_doc_tbl_id = sdt.scan_doc_id) 
				   WHERE cil.section_done IN ('medications') 
				   AND sdt.sch_id IN ($deno_sch_ids) 
				   AND (DATE_FORMAT(cil.done_on,'%Y-%m-%d') BETWEEN '".$st_from."' AND '".$st_upto."')
				   ";
			$denoNumExcl['numerator'] = $this->getPtIdFun($q2,'sch_id');
		}
		
		$denoNumExcl['exclusion'] = array();
		$denoNumExcl['specialcase'] = 'receive_toc';
		return $denoNumExcl;
	}// end of getMedReconcil_2017
	
	function getMedReconcil_2018($totalPtIDs){
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		/***GETTING PATIENTS FOR WHOM, A CCD DOCUMENT RECIEVED VIA DIRECT OR UPLOADED**/
		$RecvdCCD = $this->ReceiveSummaryCareRec($totalPtIDs);
		$denoNumExcl['denominator'] = $RecvdCCD['denominator'];

		if(count($denoNumExcl['denominator'])>0){
			$mu_year = substr($this->dbdtfrom,0,4);
			$st_from = $mu_year.'-01-01';
			$st_upto = $mu_year.'-12-31';
			
			$deno_sch_ids = implode(',',$denoNumExcl['denominator']);
			$q2 = "SELECT DISTINCT(sdt.sch_id) AS sch_id,COUNT(section_done) as section_count FROM ".constant('IMEDIC_SCAN_DB').".scan_doc_tbl sdt 
				   JOIN ".constant('IMEDIC_IDOC').".ccd_incorporate_log cil ON (cil.scan_doc_tbl_id = sdt.scan_doc_id) 
				   WHERE cil.section_done IN ('medications','allergies','problem_list') 
				   AND sdt.sch_id IN ($deno_sch_ids) 
				   AND (DATE_FORMAT(cil.done_on,'%Y-%m-%d') BETWEEN '".$st_from."' AND '".$st_upto."')
				   GROUP BY (cil.scan_doc_tbl_id) HAVING section_count = 3";
			$denoNumExcl['numerator'] = $this->getPtIdFun($q2,'sch_id');
		}
		$denoNumExcl['anti'] = array_diff($denoNumExcl['denominator'],$denoNumExcl['numerator']);
		$denoNumExcl['exclusion'] = array();
		$denoNumExcl['specialcase'] = 'receive_toc';
		return $denoNumExcl;
	}// end of getMedReconcil_2018
	
	function getMedReconcil2020($totalPtIDs){
		//if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$basePatients = array();
		$arr_sch_ids = array();
		$q = "SELECT appt.id as sch_id, appt.sa_patient_id,appt.sa_doctor_id FROM schedule_appointments appt 
				INNER JOIN patient_data pd ON (pd.pid=appt.sa_patient_id AND pd.lname != 'doe') 
				WHERE appt.sa_doctor_id IN (".$this->provider.") 
				AND appt.sa_facility_id IN (".$this->facilities.") 
				AND pd.id <> 0 AND pd.pid <> 0 
				AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
				AND date_format(appt.sa_app_start_date,'%Y-%m-%d')>='".$this->dbdtfrom."' 
				AND date_format(appt.sa_app_start_date,'%Y-%m-%d')<='".$this->dbdtupto."' 
				AND appt.sa_patient_id IN ($totalPtIDs) 
				ORDER BY appt.sa_app_start_date";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			while($rs = imw_fetch_assoc($res)){
				$sch_id = $rs['sch_id'];
				$pat_id = $rs['sa_patient_id'];
				$sa_doc_id = $rs['sa_doctor_id'];
				$uniquecombination = $sch_id.'_'.$pat_id;
				if(!in_array($uniquecombination,$basePatients)){//ORDER BY OLDEST APPT WITH THIS PROVIDER HERE. NOT ADDING sch_id IF PATIENT IS ALREADY SEEN.
					$arr_sch_ids[] = $sch_id;
					$basePatients[] = $uniquecombination;
				}
			}
		}
		$str_sch_ids = implode(',',$arr_sch_ids);
		
		$res1 = imw_query("SELECT email FROM users WHERE id IN (".$this->provider.")");
		if($res1 && imw_num_rows($res1)>0){
			$direct_mails = array();
			while($rs1 = imw_fetch_assoc($res1)){
				$direct_mails[] = $rs1['email'];
			}
			if(count($direct_mails)>0){//IF DIRECT EMAIL IDs FOUND.
				$strDirectMails = "'".implode("','",$direct_mails)."'";
				$q2 = "SELECT GROUP_CONCAT(dma.id) as id, dma.sch_id FROM direct_messages_attachment dma ";
				$q2 .="JOIN direct_messages dm ON (dma.direct_message_id = dm.id AND dm.folder_type='1') ";
				$q2 .="WHERE dm.to_email IN ($strDirectMails) AND dm.to_email!=dm.from_email AND dma.is_cda='1' AND dma.sch_id IN ($str_sch_ids) ";
				$q2 .="AND (DATE_FORMAT(dm.local_datetime,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') GROUP BY (dma.sch_id)";
				$q2 .=" ORDER BY dma.id";
				$res2 = imw_query($q2);
				$dmaArr = $schArr = array();
				if($res2 && imw_num_rows($res2)>0){
					while($rs2 = imw_fetch_assoc($res2)){
						$dmaArr[] = $rs2['id'];
						$schArr[] = $rs2['sch_id'];
					}
				}
				$denoNumExcl['denominator'] = array_unique($schArr);// Unique visits as denominator.
				$strDeno = implode(',',$dmaArr);
				$sdt_dma_ids = array();
				if(count($dmaArr)>0){//IF CDA DOCUMENT RECEIVED, CHECK FOR NUMERATOR
					$basePatients = array();
					$q3="SELECT GROUP_CONCAT(sdt.scan_doc_id) as scan_doc_id FROM ".constant('IMEDIC_SCAN_DB').".scan_doc_tbl sdt 
						WHERE sdt.task_status != '2' AND sdt.direct_attach_id IN ($strDeno) 
					    GROUP BY (sdt.patient_id)";
					$arr_scan_doc_id = $this->getPtIdFun($q3,'scan_doc_id');
					$str_scan_doc_id = implode(',',$arr_scan_doc_id);
					
					$q4="SELECT sdt.direct_attach_id,sdt.patient_id, GROUP_CONCAT(cil.section_done) AS section_done 
						FROM ".constant('IMEDIC_SCAN_DB').".scan_doc_tbl sdt 
						JOIN ".constant('IMEDIC_IDOC').".ccd_incorporate_log cil ON (cil.scan_doc_tbl_id = sdt.scan_doc_id) 
						WHERE sdt.scan_doc_id IN ($str_scan_doc_id) AND 
						(DATE_FORMAT(cil.done_on,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')
						GROUP BY (sdt.scan_doc_id)";
					$res4 = imw_query($q4);
					if($res4 && imw_num_rows($res4)>0){
						while($rs4 = imw_fetch_assoc($res4)){
							$dma_id 		= $rs4['direct_attach_id'];
							$section_done 	= $rs4['section_done'];
							$pat_id			= $rs4['patient_id'];
							$sa_doc_id_arr	= $this->getSchIdByDMAid(array($dma_id));
							$uniquecombination = $sa_doc_id_arr[0].'_'.$pat_id;
							if(stristr($section_done,'medications') && stristr($section_done,'allergies') && stristr($section_done,'problem_list')){
								//if any reconciliation done once for this patient, don't add other.
								if(!in_array($uniquecombination,$basePatients)){
									$sdt_dma_ids[] = $dma_id;
									$basePatients[] = $uniquecombination;
								}

							}
						}
					}
				}
				if(count($sdt_dma_ids)>0){		
					$denoNumExcl['numerator'] = $this->getSchIdByDMAid(array_unique($sdt_dma_ids));
				}
			}
			
			
		}
		$denoNumExcl['anti'] = array_diff($denoNumExcl['denominator'],$denoNumExcl['numerator']);
		$denoNumExcl['exclusion'] = array();
		$denoNumExcl['specialcase'] = 'receive_toc';
		return $denoNumExcl;
	}// end of getMedReconcil_2018
	
	function getSchIdByDMAid($DMAidArr){//take the Direct Message Attachment IDs array as arguement.
		if(count($DMAidArr)==0) return false;
		$res = imw_query("SELECT sch_id FROM direct_messages_attachment WHERE id IN (".implode(',',$DMAidArr).")");
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[] = $rs['sch_id'];
			}
			return $return;
		}
		return false;
	}

	function getSchDocIdByDMAid($DMAid){//take the Direct Message Attachment ID value as arguement.
		if(empty($DMAid)) return false;
		$q = "SELECT sa_doctor_id FROM schedule_appointments sa 
				JOIN direct_messages_attachment dma ON (dma.sch_id = sa.id) 
				WHERE dma.id = '".$DMAid."' LIMIT 1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$return = array();
			$rs = imw_fetch_assoc($res);
			return $rs['sa_doctor_id'];
		}
		return false;
	}	
	
	
	function SendSummaryCareRec($totalPtIDs,$subSection){//TOC measure for 2017,2018,2019,2020.
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$q1 = "SELECT cmt.id as form_id,cmt.patient_id as patient_id FROM chart_master_table cmt 
			   JOIN chart_assessment_plans cap ON (cmt.id=cap.form_id) 
			   WHERE cap.doctor_name!='' 
				   AND cmt.delete_status='0' AND cmt.purge_status='0' 
				   AND (cmt.date_of_service BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
				   AND cmt.patient_id IN ($totalPtIDs) 
				   AND cmt.providerId IN (".$this->provider.") 
			   	   AND cmt.facilityid IN (".$this->facilities.")";
			 //	GROUP BY(concat(cmt.patient_id,'--',cmt.providerId))";//echo $q1.'<hr>';
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)){
			$form_ID_arr = array();
			$denoPts	 = array();
			$denoPatients= array();
			while($rs1 = imw_fetch_assoc($res1)){
				$form_ID_arr[] 	= $rs1['form_id'];
				$denoPts[] 		= $rs1;
				$denoPatients[]	= $rs1['patient_id'];
			}
			$denoNumExcl['denominator'] = array_unique($form_ID_arr);	
			
			//-----GETTING NUMERATOR-------
			$totalPtIDs2 = implode(',',$denoPatients);
			$total_formIDs = implode(',',$form_ID_arr);
			
			$q2="SELECT dma.form_id,dma.patient_id 
				 FROM direct_messages dm 
				 JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
				 JOIN direct_messages_log dml ON (dml.updox_message_id = dm.MID) 
				 WHERE (DATE_FORMAT(dm.local_datetime,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
				 AND dm.folder_type = '3' 
				 AND dma.patient_id IN ($totalPtIDs2) 
				 AND dma.form_id IN ($total_formIDs) 
				 AND LOWER(dml.status)='dispatched'";
				/* AND dm.imedic_user_id IN (".$this->provider.") */ //Change done on March 14, 2019 (as per discussion with TK)
			$res2 = imw_query($q2);
			if($res2 && imw_num_rows($res2)){
				$form_ID_arr = array();
				$NUMEPts	 = array();
				$NUMEPatients= array();
				while($rs2 = imw_fetch_assoc($res2)){
					$form_ID_arr[] 	= $rs2['form_id'];
					$NUMEPts[] 		= $rs2;
					$NUMEPatients[]	= $rs2['patient_id'];
				}
				$denoNumExcl['numerator'] = array_unique($form_ID_arr);
			}
		}
		$denoNumExcl['anti'] = array_diff($denoNumExcl['denominator'],$denoNumExcl['numerator']);
		$denoNumExcl['exclusion'] = array();
		$denoNumExcl['specialcase'] = 'send_toc';
		return $denoNumExcl;
	}
	
	function SendSummaryCareRec2020($totalPtIDs,$subSection){//for 2020 MIPS PI scorecard.
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$q1 = "SELECT cmt.id as form_id,cmt.patient_id as patient_id,cap.doctorName_id, cap.refer_to_id FROM chart_master_table cmt 
			   JOIN chart_assessment_plans cap ON (cmt.id=cap.form_id) 
			   WHERE (cap.doctorName_id !='' OR cap.refer_to_id != '') 
				   AND cmt.delete_status='0' AND cmt.purge_status='0' 
				   AND (cmt.date_of_service BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
				   AND cmt.patient_id IN ($totalPtIDs) 
				   AND cmt.providerId IN (".$this->provider.")";
			//   	   AND cmt.facilityid IN (".$this->facilities.")";
			 //	GROUP BY(concat(cmt.patient_id,'--',cmt.providerId))";//echo $q1.'<hr>';
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)){
			$refPhyDirectEmails = $this->getDirectEmailRefPhy();
			$Dform_ID_arr = $Nform_ID_arr = array();
			//$numerator_condition_array = array();
			//$denoPts	 = array();
			//$denoPatients= array();
			while($rs1 = imw_fetch_assoc($res1)){
				$Dform_ID_arr[] 	= $rs1['form_id']; //Arry to get DENOMINATOR.
				
				//NUMERATOR TASK BELWO
				$doctorName_id 	= $rs1['doctorName_id'];
				$refer_to_id	= $rs1['refer_to_id'];
				$mails			= array();
				if(!empty($doctorName_id)) 	$mails[] = $refPhyDirectEmails[$doctorName_id];
				if(!empty($refer_to_id)) 	$mails[] = $refPhyDirectEmails[$refer_to_id];
				
				if(count($mails)>0){//IF DIRECT MAIL ADDRESS AVAILABLE FOR TRANSITION OF CARE OR REFER TO PROVIDERS..?
					$direct_mail = "'".implode("','",$mails)."'";
					$q2="SELECT dma.form_id FROM direct_messages dm 
						 JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
						 JOIN direct_messages_log dml ON (dml.updox_message_id = dm.MID) 
						 WHERE (DATE_FORMAT(dm.local_datetime,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
						 AND dm.folder_type = '3' 
						 AND dm.to_email IN ($direct_mail)
						 AND dma.form_id = '".$rs1['form_id']."' AND dma.size != '' 
						 AND LOWER(dml.status)='dispatched' LIMIT 0,1";
					/* AND dm.imedic_user_id IN (".$this->provider.") */ //Change done on March 14, 2019 (as per discussion with TK)
					$res2 = imw_query($q2);
					if($res2 && imw_num_rows($res2)>=1){
						$Nform_ID_arr[] = $rs1['form_id'];
					}
				}				
			}
			$denoNumExcl['denominator'] = array_unique($Dform_ID_arr);
			$denoNumExcl['numerator'] 	= array_unique($Nform_ID_arr);
		}
		$denoNumExcl['anti'] = array_diff($denoNumExcl['denominator'],$denoNumExcl['numerator']);
		$denoNumExcl['exclusion'] = array();
		$denoNumExcl['specialcase'] = 'send_toc';
		return $denoNumExcl;
	}
	
	function getDirectEmailRefPhy($id=''){
		$whr = " AND delete_status != '1'";
		if(!empty($id)){
			$whr = " AND physician_Reffer_id = '".$id."'";
		}
		$q = "SELECT physician_Reffer_id AS id, direct_email AS mail FROM refferphysician WHERE direct_email != ''";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[$rs['id']] = $rs['mail'];
			}
			return $return;
		}
		return false;
	}

	function SendSummaryCareRec2017($totalPtIDs,$subSection){//TOC measure for 2017,2018.
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$q1 = "SELECT cmt.id as form_id,cmt.patient_id as patient_id FROM chart_master_table cmt 
			   JOIN chart_assessment_plans cap ON (cmt.id=cap.form_id) 
			   WHERE cap.doctor_name!='' 
				   AND cmt.delete_status='0' AND cmt.purge_status='0' 
				   AND (cmt.date_of_service BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
				   AND cmt.patient_id IN ($totalPtIDs) 
				   AND cmt.providerId IN (".$this->provider.") 
			   	   AND cmt.facilityid IN (".$this->facilities.")";
			 //	GROUP BY(concat(cmt.patient_id,'--',cmt.providerId))";//echo $q1.'<hr>';
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)){
			$form_ID_arr = array();
			$denoPts	 = array();
			$denoPatients= array();
			while($rs1 = imw_fetch_assoc($res1)){
				$form_ID_arr[] 	= $rs1['form_id'];
				$denoPts[] 		= $rs1;
				$denoPatients[]	= $rs1['patient_id'];
			}
			$denoNumExcl['denominator'] = $form_ID_arr;	
			
			//-----GETTING NUMERATOR-------
			$totalPtIDs2 = implode(',',$denoPatients);
			$total_formIDs = implode(',',$form_ID_arr);
			
			$q2="SELECT dma.form_id,dma.patient_id 
				 FROM direct_messages dm 
				 JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
				 WHERE dm.folder_type = '3' 
				 AND dma.patient_id IN ($totalPtIDs2) 
				 AND dma.form_id IN ($total_formIDs)";
			$res2 = imw_query($q2);
			if($res2 && imw_num_rows($res2)){
				$form_ID_arr = array();
				$NUMEPts	 = array();
				$NUMEPatients= array();
				while($rs2 = imw_fetch_assoc($res2)){
					$form_ID_arr[] 	= $rs2['form_id'];
					$NUMEPts[] 		= $rs2;
					$NUMEPatients[]	= $rs2['patient_id'];
				}
				$denoNumExcl['numerator'] = $form_ID_arr;
			}
		}
		$denoNumExcl['exclusion'] = array();
		$denoNumExcl['specialcase'] = 'send_toc';
		return $denoNumExcl;
	}

	function getSummaryCareRec2016($totalPtIDs,$subSection){
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$query1 = "SELECT id as form_id FROM chart_master_table WHERE (date_of_service BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
		AND patient_id IN ($totalPtIDs) AND purge_status='0'";
		$form_ids_arr = $this->getPtIdFun($query1,'form_id');
		
		$form_ID_arr = array();
		$patient_id_arr = array();
		if($form_ids_arr){
			foreach($form_ids_arr as $form_id){
				$query = "SELECT form_id,patient_id FROM chart_assessment_plans  
				WHERE form_id='$form_id' AND doctorId='".$this->provider."' AND doctor_name!=''";
				$result = imw_query($query);//echo $query.'<hr>';
				if($result && imw_num_rows($result)>=1){
					while($rs = imw_fetch_assoc($result)){
						$form_ID_arr[]		= $rs['form_id'];
						$patient_id_arr[]	= $rs['patient_id'];
					}
				}
			}
		}
		$denoNumExcl['denominator'] = $patient_id_arr;
			
		$totalPtIDs2 = implode(',',$denoNumExcl['denominator']);
		$total_formIDs = implode(',',$form_ID_arr);
		switch($subSection){
			case 'm1';
				$q2="SELECT DISTINCT(patient_id) AS id FROM pt_printed_records 
					  WHERE form_id IN($total_formIDs)  
					  AND sending_application = 'iDoc'";
				$num1a	= $this->getPtIdFun($q2,'id');
					 
				$strDenoPTs = implode(',',$denoNumExcl['denominator']);			
				$q3 = "SELECT DISTINCT(patient_id) as id FROM patient_consult_letter_tbl				
				WHERE patient_form_id IN ($total_formIDs) AND status = '0'";
				$num1b	= $this->getPtIdFun($q3,'id');
			
				$q4="SELECT dma.patient_id as id FROM direct_messages dm JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
					 WHERE (DATE_FORMAT(local_datetime,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
					 AND dm.imedic_user_id = '".$this->provider."' AND dma.patient_id IN ($totalPtIDs2)";
				$num1c = $this->getPtIdFun($q4,'id');
			
				$denoNumExcl['numerator'] = array_unique(array_merge($num1a,$num1b,$num1c));
				break;
			case 'm2';
				$q2="SELECT dma.patient_id as id FROM direct_messages dm JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
					 WHERE (DATE_FORMAT(local_datetime,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
					 AND dm.imedic_user_id = '".$this->provider."' AND dma.patient_id IN ($totalPtIDs2)";
				$denoNumExcl['numerator'] = $this->getPtIdFun($q2,'id');
				break;
		}
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;
}// end of getSummaryCareRec
	
	function ReceiveSummaryCareRec($totalPtIDs){
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		
		/****GETTING APPOINTMENT ID OF THESE PATIENTS FOR THIS FACILITY AND PROVIDER IN MU PERIOD****/
		$q1 = "SELECT appt.id as sch_id FROM schedule_appointments appt 
				INNER JOIN patient_data pd ON (pd.pid=appt.sa_patient_id AND pd.lname != 'doe') 
				WHERE appt.sa_doctor_id IN (".$this->provider.") 
				AND appt.sa_facility_id IN (".$this->facilities.") 
				AND pd.id <> 0 AND pd.pid <> 0 
				AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
				AND date_format(appt.sa_app_start_date,'%Y-%m-%d')>='".$this->dbdtfrom."' 
				AND date_format(appt.sa_app_start_date,'%Y-%m-%d')<='".$this->dbdtupto."' 
				AND appt.sa_patient_id IN ($totalPtIDs)";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)>0){
			$sch_ids_array = array();
			while($rs1=imw_fetch_assoc($res1)){
				$sch_ids_array[] = $rs1['sch_id'];
			}
			$sch_id_str = implode(',',$sch_ids_array);
			
			/****FILTER VISITS WHERE CCD RECEIVED******/
			$q2="SELECT dma.sch_id FROM direct_messages dm 
			 JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
			 WHERE dm.folder_type='1' AND del_status = '0' 
			 	AND dma.sch_id IN ($sch_id_str) 
				";
			$dms_sch_ids = $this->getPtIdFun($q2,'sch_id');
			
			/*****FILTER VISITS WHERE CCD UPLOADED FOR RECONCILIATIN***/
			$q3="SELECT sdt.sch_id FROM ".constant('IMEDIC_SCAN_DB').".scan_doc_tbl sdt 
				 WHERE sdt.CCDA_type LIKE '%ccda%' AND sdt.task_status != '2' AND sdt.sch_id IN ($sch_id_str)";
			$sdt_sch_ids = $this->getPtIdFun($q3,'sch_id');
			
			/*****MAKING DENOMINATOR******/
			$denoNumExcl['denominator'] = array_unique(array_merge($dms_sch_ids,$sdt_sch_ids));
			
			if(count($denoNumExcl['denominator'])>0){
				$mu_year = substr($this->dbdtfrom,0,4);
				$st_from = $mu_year.'-01-01';
				$st_upto = $mu_year.'-12-31';
				
				$deno_sch_ids = implode(',',$denoNumExcl['denominator']);
				$q2 = "SELECT DISTINCT(sdt.sch_id) AS sch_id FROM ".constant('IMEDIC_SCAN_DB').".scan_doc_tbl sdt 
					   JOIN ccd_incorporate_log cil ON (cil.scan_doc_tbl_id = sdt.scan_doc_id) 
					   WHERE cil.section_done IN ('medications','allergies','problem_list') 
					   AND sdt.sch_id IN ($deno_sch_ids) 
					   AND (DATE_FORMAT(done_on,'%Y-%m-%d') BETWEEN '".$st_from."' AND '".$st_upto."')";
				$denoNumExcl['numerator'] = $this->getPtIdFun($q2,'sch_id');
			}
			$denoNumExcl['exclusion'] = array();
			$denoNumExcl['specialcase'] = 'receive_toc';
		}
		
		
		return $denoNumExcl;
	}
	
	function getPatientSecureMessaging($totalPtIDs){
		$denoNumExcl = array();
		if($totalPtIDs==0){return $denoNumExcl;}
		$denoNumExcl['denominator'] = explode(',',$totalPtIDs);
		$q1= "SELECT DISTINCT(receiver_id) FROM patient_messages WHERE receiver_id IN ($totalPtIDs) 
				AND communication_type='1' AND msg_data != '' AND sender_id IN (".$this->provider.") 
				AND (DATE_FORMAT(msg_date_time,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
		$ptIDs = $this->getPtIdFun($q1,'receiver_id');//echo $q1.'<hr>';
		
		/*****ADDING EDUCATION PART*********/
		$eduPtIDsAll = $this->getEduResourceToPt($totalPtIDs);
		$eduPtIDs = $eduPtIDsAll['numerator'];
		$denoNumExcl['numerator'] = array_unique(array_merge($eduPtIDs,$ptIDs));
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;
	}
	
	function getPatientGeneratedHealthData($totalPtIDs){
		$denoNumExcl = array();
		if($totalPtIDs==0){return $denoNumExcl;}
		$denoNumExcl['denominator'] = explode(',',$totalPtIDs);
		$q = "SELECT DISTINCT(patientId) FROM user_messages 
			  WHERE msg_type = '2' 
			  	AND patientId IN ($totalPtIDs) 
				AND Pt_Communication = '1' 
				AND del_operator_id = '0'";
		$ptIDs = $this->getPtIdFun($q,'patientId');
		
		$denoNumExcl['numerator'] = $ptIDs;
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;
	}
	
	
	
	
	
	
	/*******************CQM MEASURES**********************************/
	function getNQF0018($commaPatients=''){
		//CQM #NQF0018 - Hypertension.
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();	$ptIDs	= array(); $ptIDs1 = array(); $ptIDs2 = array();
	
		//getting patients of age 18-85
		$totalPtIDs = $this->aged_get_denominator($this->provider,'18-85');
		if($commaPatients!=''){$totalPtIDs = $commaPatients;}
		
		//PROBLEM DATE <= MEASURE START DATE
		$chkDt = $this->dbdtfrom;
		$ArrDbDtFrom	= explode('-',$this->dbdtfrom);
		$year	= $ArrDbDtFrom[0];
		$month	= $ArrDbDtFrom[1];
		$day	= $ArrDbDtFrom[2];
		$chkDt = date('Y-m-d',mktime(0,0,0,$month-6,$day,$year));
		
		//getting patients having diagnosis of HYPERTENSION (from within 6 months before MU start period)
		$query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($totalPtIDs) 
						AND (problem_name RLIKE '401.0|401.1|401.9' OR (LOWER(problem_name) LIKE '%essential%' AND (LOWER(problem_name) LIKE '%hypertension%' OR LOWER(problem_name) LIKE '%hypertention%'))) 
						AND (onset_date BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
						GROUP BY problem_id HAVING (status='Active')";
		$ptIDs = $this->getPtIdFun($query1,'pt_id');
		
		$query2 = "SELECT pt_id FROM pt_problem_list WHERE pt_id IN ($totalPtIDs) 
						AND (problem_name RLIKE '401.0|401.1|401.9' OR (LOWER(problem_name) LIKE '%essential%' AND (LOWER(problem_name) LIKE '%hypertension%' OR LOWER(problem_name) LIKE '%hypertention%'))) 
						AND (onset_date BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') AND LOWER(status)='active'";
		$ptIDs2 = $this->getPtIdFun($query2,'pt_id');
	
		$ptIDs = array_merge($ptIDs,$ptIDs2);
		unset($ptIDs2);
		
		//getting patients having 1 or more visits.
		$ptIDs1 = $this->get_patients_by_visits(implode(',',$ptIDs),1);
		if(count($ptIDs1)>0){
			$DenoPtIDs = implode(', ',$ptIDs1);
		}
		else{
			return;
			$DenoPtIDs = 0;
		}
		$denoNumExcl['denominator'] = $ptIDs1;
		
		//QUERYING FOR DENOMINATOR EXCLUSION
		//getting patients having diagnosis of .....
		$excluQ1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($DenoPtIDs) 
						AND LOWER(problem_name) RLIKE 'pregnancy|end stage renal disease|chronic kidney diesease' 
						AND onset_date <= '".$this->dbdtfrom."' 
						GROUP BY problem_id HAVING (status='Active')";
		$X1ptIDs = $this->getPtIdFun($excluQ1,'pt_id');
		
		//Getting patient having procedure perormed.....
		$DenoPtIDs = implode(',',array_diff($ptIDs1,$X1ptIDs));
		if(strlen($DenoPtIDs)==0){$DenoPtIDs = 0;}	
		$excluQ2 = "SELECT DISTINCT(pid) FROM lists 
					WHERE type IN (5,6) AND 
					LOWER(title) RLIKE 'kidney transplant|dialysis service|dialysis procedure|vascular access for dialysis' 
					AND allergy_status='Active' AND (DATE_FORMAT(begdate,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
					AND pid IN ($DenoPtIDs)";
		$X2ptIDs = $this->getPtIdFun($excluQ2,'pid');
		
		$denoNumExcl['exclusion'] = array_unique(array_merge($X1ptIDs,$X2ptIDs));
		$denoNumExcl['numerator']	= array();
		
		$tempNumPtArr = array_diff($denoNumExcl['denominator'],$denoNumExcl['exclusion']);
		if(count($tempNumPtArr)>0){
			$NumPtIDs = implode(',',$tempNumPtArr);
			//query for numerator. blood pressure recorded.
			if($NumPtIDs != '0'){
				$query = "SELECT vsm.patient_id, COUNT(vsm.patient_id) as cnt_pat FROM vital_sign_master vsm 
						  JOIN vital_sign_patient vsp ON (vsp.vital_master_id=vsm.id) 				  
						  WHERE vsm.patient_id IN($NumPtIDs) 
						  AND vsm.status='0' AND vsp.range_vital > 0 AND ((vsp.vital_sign_id = 1 AND vsp.range_vital < 140) or (vsp.vital_sign_id = 2 AND vsp.range_vital < 90))  
						  GROUP BY vsm.id HAVING cnt_pat>=1";
					$ptIDs2 = $this->getPtIdFun($query,'patient_id');
					if(count($ptIDs2)>0){
						$denoNumExcl['numerator'] 	= array_unique($ptIDs2);
					}
			}
		}
		return $denoNumExcl;
	}
	
	function getNQF0022($m='one',$commaPatients=''){
		$denoNumExcl = array();	
		$totalPtIDs = $this->aged_get_denominator($this->provider,'66');
		if($commaPatients!=''){$totalPtIDs = $commaPatients;}
		$ptIDs1 = $this->get_patients_by_visits($totalPtIDs,1);
	
		$strPtIds1=implode(',', $ptIDs1);
		$ptIDs1	= $this->chart_superbill_patients($strPtIds1,'99201|99202|99203|99204|99204|99211|99212|99213|99214|99215',$this->dbdtfrom,$this->dbdtupto);
		
		if(count($ptIDs1)==0){return;}
		$denoNumExcl['denominator']	= $ptIDs1;
		$denoNumExcl['exclusion']	= array();
		
		$totalPtIDs = implode(',',$ptIDs1);
		$arrElederlySalts = array('Carisoprodol', 'Chlorzoxazone', 'Cyclobenzaprine', 'Metaxalone', 'Methocarbamol', 
								  'Orphenadrine', 'Dipyridamole', 'Ergot Mesylate', 'Isoxsuprine', 'Hydroxyzine Hydrochloride', 
								  'Brompheniramine', 'Carbinoxamine', 'Benztropine', 'Butalbital', 'Chloral Hydrate', 'Chlorpheniramine', 
								  'Clemastine', 'Clomipramine', 'Conjugated Synthetic Estrogens', 'Dexbrompheniramine', 'Disopyramide', 
								  'Doxylamine', 'Estradiol', 'Glyburide', 'Guanabenz', 'Guanfacine', 'Imipramine', 'Indomethacin', 
								  'Megestrol', 'Meprobamate', 'Methyldopa', 'Ticlopidine', 'Trihexyphenidyl', 'Trimipramine', 
								  'Triprolidine','Estrogens');
		
		$arrElederlyMedsDaySupply = array('Nitrofurantoin', 'Nitrofurantoin Monohydrate Macrocrystalline', 'Nitrofurantoin Macrocrystallin', 
										  'Eszopiclone', 'Zaleplon', 'Zolpidem','Morphine Sulfate');
		
		$numQ = "SELECT pid,GROUP_CONCAT(title) AS title FROM lists WHERE 
				 pid IN($totalPtIDs) 
				 AND type in (1,4) 
				 AND allergy_status = 'Active' 
				 AND (begdate BETWEEN '".$this->dbdtfrom."' AND '".$this->dtupto."') 
				 GROUP BY pid";
		$result = imw_query($numQ);
		$numPts = array();
		$NumArr2Plus = array();
		if($result && imw_num_rows($result)>0){
			While($rs = imw_fetch_assoc($result)){
				$pid 	= $rs['pid'];
				$ptMed 	= $rs['title'];
				//echo $pid.' - '.$ptMed.'<br>';
				foreach($arrElederlySalts as $EldMed){
					if(stristr($ptMed,$EldMed)!==false){
						$numPts[] = $pid;
						$NumArr2Plus[$pid][] = $EldMed;
					}
				}
				foreach($arrElederlyMedsDaySupply as $EldMed2){
					if(stristr($ptMed,$EldMed2)!==false){
						$numPts[] = $pid;
						$NumArr2Plus[$pid][] = $EldMed;
					}
				}
			}		
		}
		if($m=='two'){
			$finalNumPts = array();
			foreach($NumArr2Plus as $pid=>$medsArr){
				if(count($medsArr)>=2){
					$finalNumPts[] = $pid;
				}			
			}
			$denoNumExcl['numerator']	= array_unique($finalNumPts);
		}else{
			$denoNumExcl['numerator']	= array_unique($numPts);
		}
		return $denoNumExcl;
	}
	
	function getNQF0421a($commaPatients=''){
		$denoNumExcl = array();	$ptIDs = array();	$ptIDs1 = array();	$ptIDs2 = array(); $patients2 = array();
		$totalPtIDs = $this->aged_get_denominator($this->provider,'65');
		if($commaPatients!=''){$totalPtIDs = $commaPatients;}	
		$ptIDs	= $this->chart_superbill_patients($totalPtIDs,'90791|90792|90832|90834|90837|90839|96150|96151|96152|97001|97003|97802|97803|98960|99201|99202|99203|99204|99205|99212|99213|99214|99215|D7140|D7210|G0101|G0108|G0270|G0271|G0402|G0438|G0439|G0447',$this->dbdtfrom,$this->dbdtupto);
	
		//AND SHOULD NOT BE "Palliative Care" 
		if(count($ptIDs)>0){
			$strPatIds=implode(',',$ptIDs);
			$qry="Select DISTINCT(pid) FROM lists WHERE pid IN(".$strPatIds.") AND type IN(5,6) 
			AND LOWER(lists.title)='palliative care' AND (begdate BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
			AND allergy_status='Active'";
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$ind = array_search($res['pid'],$ptIDs);
				if($ind!==false) unset($ptIDs[$ind]);
			}
		}
		$denoNumExcl['denominator'] = $ptIDs;

		//NUMERATOR
		$currDt = $this->dbdtfrom;
		$ArrcurrDt	= explode('-',$currDt);
		$year	= $ArrcurrDt[0];
		$month	= $ArrcurrDt[1];
		$day	= $ArrcurrDt[2];
		$chkDt 	= date('Y-m-d',mktime(0,0,0,$month-6,$day,$year));
		$ptIDs = array_unique(array_filter($ptIDs));
	
		//PREGNENCY SHOULD NOT ADDED
		if(sizeof($ptIDs)>0){
			$strPatIds2=implode(',', $ptIDs);
			$query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($strPatIds2) 
						AND (LOWER(problem_name) LIKE '%pregnancy%') 
						AND (onset_date <='".$this->dbdtfrom."') 
						GROUP BY problem_id HAVING (status='Active')";
			$ptIDs1 = $this->getPtIdFun($query1,'pt_id');
		}
		$denoNumExcl['exclusion'] 	= $ptIDs1;
	
		$arrFirst=$ptIDs;
		//EXCLUDE IF ANY PAT WITH PREGNENCY
		if(count($ptIDs1)>0){
			$arrFirst = array_diff($ptIDs, $ptIDs1);
		}
		
		// CHECK IF BMI CREATED
		if(count($arrFirst)>0){
			$strFirst = implode(',', $arrFirst);
			$qry="Select DISTINCT(vsm.patient_id), vsm.date_vital, vsp.range_vital as vrv FROM chart_master_table chm 
			JOIN vital_sign_master vsm ON vsm.date_vital = chm.date_of_service 
			JOIN vital_sign_patient vsp ON (vsp.vital_master_id = vsm.id AND vsp.vital_sign_id ='9' AND vsp.range_vital != '') 
			WHERE chm.patient_id IN (".$strFirst.") AND vsm.patient_id IN (".$strFirst.") 
			AND (chm.date_of_service BETWEEN '".$chkDt."' AND '".$this->dbdtupto."')
			AND vsm.status<>'1'";
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				//if($res['date_vital']>=$chkDt && $res['date_vital']<=$dtfrom1){
					if($res['vrv']>=23 && $res['vrv']<30){
						$numeratorPats[$res['patient_id']]=$res['patient_id'];
					}
					if($res['vrv']>=30){
						$arrFirstStep[$res['patient_id']]=$res['patient_id'];
					}
					if($res['vrv']<23 && $res['vrv']!=''){
						$arrSecondStep[$res['patient_id']]=$res['patient_id'];
					}
				//}
			}
		}
		
		// CHECK IF >=30
		if(count($arrFirstStep)>0){
			$strFirstStep=implode(',', $arrFirstStep);
			$Q2= "Select DISTINCT(pid) FROM lists JOIN followup ON followup.code = lists.ccda_code 
					WHERE pid IN(".$strFirstStep.") 
					AND proc_type='intervention' 
					AND type IN(5,6) 
					AND (begdate BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
					AND followup.follow_type='above' 
					AND allergy_status='Active'";
			$rs=imw_query($Q2);
			while($res=imw_fetch_array($rs)){
				$numPats[$res['pid']]=$res['pid'];
				$numeratorPats[$res['pid']]=$res['pid'];
			}
			
			if(count($numPats)>0){ $arrFirstStep1= array_diff($arrFirstStep, $numPats); }else{ $arrFirstStep1=$arrFirstStep; }
	
			if(count($arrFirstStep1)>0){
				$strFirstStep1=implode(',', $arrFirstStep1);
				$Q3= "Select DISTINCT(pid) FROM lists JOIN medication_followup ON medication_followup.code = lists.ccda_code 
					WHERE pid IN (".$strFirstStep1.") 
					AND type IN(1,4) 
					AND (begdate BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
					AND medication_followup.follow_type='above' 
					AND allergy_status='Active'";
				$rs3=imw_query($Q3);
				while($res3=imw_fetch_array($rs3)){
					$numeratorPats[$res3['pid']]=$res3['pid'];
				}
			}
		}
		
		// CHECK IF < 23
		if(count($arrSecondStep)>0){
			$strSecondStep=implode(',', $arrSecondStep);
			$Q2= "Select DISTINCT(pid) FROM lists JOIN followup ON followup.code = lists.ccda_code 
					WHERE pid IN(".$strSecondStep.") 
					AND proc_type='intervention' 
					AND type IN(5,6) 
					AND (begdate BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
					AND followup.follow_type='below' 
					AND allergy_status='Active'";
			$rs=imw_query($Q2);
			while($res=imw_fetch_array($rs)){
				$numPats1[$res['pid']]=$res['pid'];
				$numeratorPats[$res['pid']]=$res['pid'];
			}
			
			if(count($numPats1)>0){ $arrSecondStep1= array_diff($arrSecondStep, $numPats1); }else{ $arrFirstStep1=$arrSecondStep; }
	
			if(count($arrSecondStep1)>0){
				$strSecondStep1=implode(',', $arrSecondStep1);
				$Q3= "Select DISTINCT(pid) FROM lists JOIN medication_followup ON medication_followup.code = lists.ccda_code 
					WHERE pid IN (".$strSecondStep1.") 
					AND type IN(1,4) 
					AND (begdate BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
					AND medication_followup.follow_type='below' 
					AND allergy_status='Active'";
				$rs3=imw_query($Q3);
				while($res3=imw_fetch_array($rs3)){
					$numeratorPats[$res3['pid']]=$res3['pid'];
				}
			}
		}
		
		$denoNumExcl['numerator'] 	= $numeratorPats;
		$denoNumExcl['exclusion']	= array();//get_excluded_patients($denoNumExcl['numerator'],array('NQF0421'));
		return $denoNumExcl;
	}
	
	function getNQF0421b($commaPatients=''){
		$denoNumExcl = array();	$ptIDs = array();	$ptIDs1 = array();	$ptIDs2 = array(); $patients2 = array();
		
		$totalPtIDs = $this->aged_get_denominator($provider,'18-64');
		if($commaPatients!=''){$totalPtIDs = $commaPatients;}	
		$ptIDs	= $this->chart_superbill_patients($totalPtIDs,'90791|90792|90832|90834|90837|90839|96150|96151|96152|97001|97003|97802|97803|98960|99201|99202|99203|99204|99205|99212|99213|99214|99215|D7140|D7210|G0101|G0108|G0270|G0271|G0402|G0438|G0439|G0447',$this->dbdtfrom,$this->dbdtupto);
	
		//AND SHOULD NOT BE "Palliative Care" 
		if(count($ptIDs)>0){
			$strPatIds=implode(',',$ptIDs);
			$qry="Select DISTINCT(pid) FROM lists WHERE pid IN(".$strPatIds.") AND type IN(5,6) 
			AND LOWER(lists.title)='palliative care' AND (begdate BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
			AND allergy_status='Active'";
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$ind = array_search($res['pid'],$ptIDs);
				if($ind!==false) unset($ptIDs[$ind]);
			}
		}
		$denoNumExcl['denominator'] = $ptIDs;
	
		//NUMERATOR
		$currDt = $this->dbdtfrom;
		$ArrcurrDt	= explode('-',$currDt);
		$year	= $ArrcurrDt[0];
		$month	= $ArrcurrDt[1];
		$day	= $ArrcurrDt[2];
		$chkDt 	= date('Y-m-d',mktime(0,0,0,$month-6,$day,$year));
		$ptIDs = array_unique(array_filter($ptIDs));
	
		//PREGNENCY SHOULD NOT ADDED
		if(sizeof($ptIDs)>0){
			$strPatIds2=implode(',', $ptIDs);
			$query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($strPatIds2) 
						AND (LOWER(problem_name) LIKE '%pregnancy%') 
						AND (onset_date <='".$this->dbdtupto."') 
						GROUP BY problem_id HAVING (status='Active')";
			$ptIDs1 = $this->getPtIdFun($query1,'pt_id');
		}
		$denoNumExcl['exclusion'] 	= $ptIDs1;

		$arrFirst=$ptIDs;
		//EXCLUDE IF ANY PAT WITH PREGNENCY
		if(count($ptIDs1)>0){
			$arrFirst = array_diff($ptIDs, $ptIDs1);
		}
		
		// CHECK IF BMI CREATED
		if(count($arrFirst)>0){
			$strFirst = implode(',', $arrFirst);
			$qry="Select DISTINCT(vsm.patient_id), vsm.date_vital, vsp.range_vital as vrv FROM chart_master_table chm 
			JOIN vital_sign_master vsm ON vsm.date_vital = chm.date_of_service 
			JOIN vital_sign_patient vsp ON (vsp.vital_master_id = vsm.id AND vsp.vital_sign_id ='9' AND vsp.range_vital != '') 
			WHERE chm.patient_id IN (".$strFirst.") AND vsm.patient_id IN (".$strFirst.") 
			AND (chm.date_of_service BETWEEN '".$chkDt."' AND '".$this->dbdtupto."')
			AND vsm.status<>'1'";
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				//if($res['date_vital']>=$chkDt && $res['date_vital']<=$dtfrom1){
					if($res['vrv']>=18.5 && $res['vrv']<25){
						$numeratorPats[$res['patient_id']]=$res['patient_id'];
					}
					if($res['vrv']>=25){
						$arrFirstStep[$res['patient_id']]=$res['patient_id'];
					}
					if($res['vrv']<18.5 && $res['vrv']!=''){
						$arrSecondStep[$res['patient_id']]=$res['patient_id'];
					}
				//}
			}
		}
		
		// CHECK IF >=25
		if(count($arrFirstStep)>0){
			$strFirstStep=implode(',', $arrFirstStep);
			$Q2= "Select DISTINCT(pid) FROM lists JOIN followup ON followup.code = lists.ccda_code 
					WHERE pid IN(".$strFirstStep.") 
					AND proc_type='intervention' 
					AND type IN(5,6) 
					AND (begdate BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
					AND followup.follow_type='above' 
					AND allergy_status='Active'";
			$rs=imw_query($Q2);
			while($res=imw_fetch_array($rs)){
				$numPats[$res['pid']]=$res['pid'];
				$numeratorPats[$res['pid']]=$res['pid'];
			}
			
			if(count($numPats)>0){ $arrFirstStep1= array_diff($arrFirstStep, $numPats); }else{ $arrFirstStep1=$arrFirstStep; }
	
			if(count($arrFirstStep1)>0){
				$strFirstStep1=implode(',', $arrFirstStep1);
				$Q3= "Select DISTINCT(pid) FROM lists JOIN medication_followup ON medication_followup.code = lists.ccda_code 
					WHERE pid IN (".$strFirstStep1.") 
					AND type IN(1,4) 
					AND (begdate BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
					AND medication_followup.follow_type='above' 
					AND allergy_status='Active'";
				$rs3=imw_query($Q3);
				while($res3=imw_fetch_array($rs3)){
					$numeratorPats[$res3['pid']]=$res3['pid'];
				}
			}
		}
		
		// CHECK IF < 18.5
		if(count($arrSecondStep)>0){
			$strSecondStep=implode(',', $arrSecondStep);
			$Q2= "Select DISTINCT(pid) FROM lists JOIN followup ON followup.code = lists.ccda_code 
					WHERE pid IN(".$strSecondStep.") 
					AND proc_type='intervention' 
					AND type IN(5,6) 
					AND (begdate BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
					AND followup.follow_type='below' 
					AND allergy_status='Active'";
			$rs=imw_query($Q2);
			while($res=imw_fetch_array($rs)){
				$numPats1[$res['pid']]=$res['pid'];
				$numeratorPats[$res['pid']]=$res['pid'];
			}
			
			if(count($numPats1)>0){ $arrSecondStep1= array_diff($arrSecondStep, $numPats1); }else{ $arrFirstStep1=$arrSecondStep; }
	
			if(count($arrSecondStep1)>0){
				$strSecondStep1=implode(',', $arrSecondStep1);
				$Q3= "Select DISTINCT(pid) FROM lists JOIN medication_followup ON medication_followup.code = lists.ccda_code 
					WHERE pid IN (".$strSecondStep1.") 
					AND type IN(1,4) 
					AND (begdate BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
					AND medication_followup.follow_type='below' 
					AND allergy_status='Active'";
				$rs3=imw_query($Q3);
				while($res3=imw_fetch_array($rs3)){
					$numeratorPats[$res3['pid']]=$res3['pid'];
				}
			}
		}
	
		$denoNumExcl['numerator'] 	= $numeratorPats;
		return $denoNumExcl;
	}

	function getNQF0028($commaPatients=''){
		//CMS-Clinical Core #NQF0028 - (IF nvr smoked, then add in Num; if smoker + cessation or cessation med given, then add in Num.)
		$denoNumExcl = array();
		$ptIDs1 = array(); $ptIDs = array();
		
		//getting all patients of age >= 18 years.	
		$totalPtIDs = $this->aged_get_denominator($this->provider,18);	
		if($commaPatients!=''){$totalPtIDs = $commaPatients;}
		
		//getting final denominator having 2 or more office visits.
		if($totalPtIDs!=0){
			$query = "SELECT count(cmt.id) cnt, cmt.patient_id FROM chart_master_table cmt 
					  LEFT JOIN superbill sup ON (sup.formid=cmt.id AND sup.del_status=0) 
					  WHERE cmt.patient_id IN($totalPtIDs) 
					  AND (cmt.date_of_service BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
					  GROUP BY patient_id HAVING cnt >= 2";
			$denoNumExcl['denominator'] = $this->getPtIdFun($query,'patient_id');
			
			//GET DENOMINATOR EXCEPTIONS
			$query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($totalPtIDs) 
						AND (LOWER(problem_name) RLIKE 'terminal Illness|Limited Life Expectancy' 
						OR problem_name RLIKE '162607003') 
						AND (onset_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
						GROUP BY problem_id HAVING (status='Active')";
			$denoNumExcl['exception'] = $this->getPtIdFun($query1,'pt_id');
		}
		else{return;}
		$denoNumExcl['exclusion']		= array();
		
		
		//NUMERATOR PART.	
		if(count($denoNumExcl['denominator'])>0){
			$totalPtIDs = implode(', ',$denoNumExcl['denominator']);
		}else{
			$totalPtIDs = 0;
		}		
		//getting date of 24 months before.
		$Arrdtupto1	= explode('-',$dtupto1);
		$year	= $Arrdtupto1[0];
		$month	= $Arrdtupto1[1];
		$day	= $Arrdtupto1[2];
		$chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-2));
		
		$query = "SELECT * FROM social_history WHERE patient_id IN ($totalPtIDs) AND 
					smoking_status != '' AND (date_format(modified_on,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
		$result = imw_query($query);
		$numPts = array();
		if($result && imw_num_rows($result)>0){
			while($rs = imw_fetch_assoc($result)){
				$pid = $rs['patient_id'];
				$ss	 = $rs['smoking_status'];
				if(stristr($ss,'never smoked')!==false){
					$numPts[] = $pid;
				}if(stristr($ss,'smoker')!==false){
					$cessDT 	= $rs['offered_cessation_counselling_date'];
					$cessTYPE	= $rs['smoke_counseling'];
					if($cessTYPE=='1'){
						$numPts[] = $pid;	
					}
				}
			}
		}
		$denoNumExcl['numerator'] = array_unique($numPts);
		return $denoNumExcl;
	}
	
	
	function getRefLoop($commaPatients=''){
		$ptIDs = $this->get_denominator($this->provider);
		$totalPtIDs = implode(', ',$ptIDs);
		if($commaPatients!=''){$totalPtIDs = $commaPatients;}
		if($totalPtIDs=='0') return;
	
		$denoNumExcl = array();
		$ARRstep1PTs = $this->get_patients_by_visits($totalPtIDs,1);
		$STRstep1PTs = implode(',',$ARRstep1PTs);
		$Q1			 = "SELECT DISTINCT(patient_id) FROM patient_multi_ref_phy WHERE patient_id IN ($STRstep1PTs) 
				   AND phy_type='1' AND deleted_by='0'";
		$ArrDenoPTs  = $this->getPtIdFun($Q1,'patient_id');
		
		if(count($ArrDenoPTs)>0){
			$STRDenoPTs = implode(',',$ArrDenoPTs);
			$Q2 	= "Select DISTINCT(pid) FROM lists 
							WHERE pid IN(".$STRDenoPTs.") 
							AND proc_type='intervention' 
							AND type IN(5,6) 
							AND (begdate BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
							AND LOWER(title) LIKE '%referral%' 
							AND allergy_status='Active'";
			$denoNumExcl['denominator'] = $this->getPtIdFun($Q2,'pid');
			
			//GETTING NUMERATOR
			if(count($denoNumExcl['denominator'])>0){
				$strDenoPTs = implode(',',$denoNumExcl['denominator']);			
				$Q3 = "SELECT DISTINCT(patient_id) FROM patient_consult_letter_tbl patCons 
				LEFT JOIN lists ON lists.pid = patCons.patient_id 
				WHERE patient_id IN ($strDenoPTs) AND patCons.date >= lists.begdate 
				AND lists.proc_type='intervention' 
				AND lists.type IN(5,6) 
				AND LOWER(lists.title) LIKE '%referral%' 
				AND lists.allergy_status='Active' 
				AND patCons.status = '0'";
				$denoNumExcl['numerator'] 	= $this->getPtIdFun($Q3,'patient_id');
			}
		}
		return $denoNumExcl;
	}

	
	
	
	
	
	
	
	
	
	function getENotes($totalPtIDs){
		$denoNumExcl = array();
		if($totalPtIDs==0){return $denoNumExcl;}
		$denoNumExcl['denominator'] = explode(',',$totalPtIDs);
		$denoNumExcl['numerator'] = $this->get_patients_by_visits($totalPtIDs,1,'','',31);//SPECIFYING EXCLUDED TEMPLATE id OF CHARTNOTE.
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;	
	}
	
		
	function getClinicalSummary($totalPtIDs){// clinical summary for patient for each office visit
		if($totalPtIDs=='0') return;
		$denoNumExcl = $num_type1 = $num_type2 = array();
		$chartMasterQry = "SELECT DISTINCT(id) FROM chart_master_table 
						   WHERE patient_id IN($totalPtIDs) 
						   AND (date_of_service BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') AND providerId IN (".$this->provider.") ";
		$denoNumExcl['denominator'] = $this->getPtIdFun($chartMasterQry,'id');
		if(count($denoNumExcl['denominator'])>0){
			$totalFormIDs = implode(',',$denoNumExcl['denominator']);
			$query = "SELECT DISTINCT(form_id) FROM pt_printed_records 
					  WHERE form_id IN($totalFormIDs)  
					  AND sending_application = 'iDoc'
					  AND DATEDIFF(DATE_FORMAT(date_time,'%Y-%m-%d'),dos)<=1";
			$num_type1 = $this->getPtIdFun($query,'form_id');
			
			$chartMasterQry = "SELECT DISTINCT(cmt.id) AS id FROM chart_master_table cmt 
						   JOIN patient_data pd ON (pd.id=cmt.patient_id) 
						   WHERE cmt.id IN($totalFormIDs) 
						   AND (cmt.date_of_service BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
						   AND DATEDIFF(DATE_FORMAT(cmt.finalizeDate,'%Y-%m-%d'),cmt.date_of_service)<=1 
						   AND ((pd.username != '' AND pd.password != '') 
								OR (pd.temp_key!='' AND pd.temp_key_expire!='yes' AND pd.temp_key_chk_val='1' AND ".constant('IPORTAL')."='1'))";
			$num_type2 = $this->getPtIdFun($chartMasterQry,'id');
		}
		$denoNumExcl['numerator'] = count(array_unique(array_merge($num_type1,$num_type2)));
		$denoNumExcl['denominator'] = count($denoNumExcl['denominator']);
		return $denoNumExcl;
	}// end of getClinicalSummary
	
    
    function updateCQMpatients($task,$nqf='',$pat_arr=''){
        switch($task){
            case 'empty':
                $q = "TRUNCATE TABLE cqm_patients";
                $res = imw_query($q);	
                break;
            case 'update':
                $q_str = '';
                foreach($pat_arr as $pid){
                    $q_str .= "('$nqf','$pid'),";
                }
                $q_str = substr($q_str,0,-1);
                $q = "INSERT INTO cqm_patients(nqf_id,patient_id) VALUES ".$q_str;
                $res = imw_query($q);
        }
    }
    
    
    function get_excluded_patients($numerator,$arr_sections){
    //	specific_exclusion($patient_id,'AandP','get')
        $arr_exc_ptIDs = array();
        foreach($arr_sections as $secVal){
            foreach($numerator as $val){
                if(specific_exclusion($val,$secVal,'get')==1){
                    $arr_exc_ptIDs[] = $val;
                }
            }
        }
        if(count($arr_exc_ptIDs)>0){
            $arr_exc_ptIDs = array_unique($arr_exc_ptIDs);
        }
        return $arr_exc_ptIDs;
    }//end of get_excluded_patients
    
    
    function getNQF0052(){
        //global $objDb; global $provider; global $dtfrom; global $dtupto; global $objDataManage;
        $totalPtIDs = $this->aged_get_denominator($this->provider,'18-50');
        $denoNumExcl = array();
        $dtfrom1 = $this->dbdtfrom;
        $dtupto1 = $this->dbdtupto;

        //getting patients having diagnosis of LOW BACK PAIN during measurement period
        $IIP_Q = "SELECT pt_id, DATEDIFF('$dtfrom1',onset_date) AS pb_old_days, GROUP_CONCAT(DISTINCT(status)) as Grp_Status 
                  FROM pt_problem_list_log WHERE pt_id IN ($totalPtIDs) 
                  AND LOWER(problem_name) LIKE '%low back pain%' 
                  GROUP BY problem_id HAVING (Grp_Status='Active')";
        $denoPts = $excluPts = array();
        $IIP_res = imw_query($IIP_Q);
        if($IIP_res && imw_num_rows($IIP_res)>0){
            while($IIP_rs = imw_fetch_assoc($IIP_res)){
                $old_days	= $IIP_rs['pb_old_days'];
                $ptId		= $IIP_rs['pt_id'];
                //if($old_days>0 && $old_days<=180){//exlusion
                //	$excluPts[]	= $ptId;
                //}else if($old_days>180 && $old_days<=337){//denominator
                    $denoPts[]	= $ptId;
                //}
            }
        }
        $denoNumExcl['denominator']	= array_unique(array_merge($excluPts,$denoPts));

        //getting other exclusions from filtered patients of denominator
        if(count($denoPts)>0){
            $denoPtsStr = implode(',',$denoPts);
            $Exclu_Q1	= "SELECT DISTINCT(pt_id) FROM pt_problem_list 
                           WHERE pt_id IN ($denoPtsStr) 
                           AND LOWER(problem_name) LIKE '%cancer%' 
                           AND LOWER(status) RLIKE 'active|inactive|resolved' 
                           AND onset_date < '$dtupto1'";
            $Exclu_Res1	= imw_query($Exclu_Q1);
            if($Exclu_Res1 && imw_num_rows($Exclu_Res1)>0){
                while($Exclu_rs1 = imw_fetch_assoc($Exclu_Res1)){
                    $excluPts[] = $Exclu_rs1['pt_id'];
                }
            }

        }

        $denoPts		= array_diff($denoPts,$excluPts);
        if(count($denoPts)>0){
            $denoPtsStr = implode(',',$denoPts);
            $Exclu_Q2	= "SELECT DISTINCT(pt_id),GROUP_CONCAT(DISTINCT(status)) as Grp_Status FROM pt_problem_list_log 
                           WHERE LOWER(problem_name) RLIKE 'trauma|iv drug abuse|neurologic impairment' 
                           AND onset_date < '$dtupto1' AND DATEDIFF('$dtfrom1',onset_date) <= 365 
                           GROUP BY problem_id HAVING (Grp_Status='Active')";
            $Exclu_Res2	= imw_query($Exclu_Q2);
            if($Exclu_Res2 && imw_num_rows($Exclu_Res2)>0){
                while($Exclu_rs2 = imw_fetch_assoc($Exclu_Res2)){
                    $excluPts[] = $Exclu_rs2['pt_id'];
                }
            }

        }

        $denoNumExcl['exclusion']	= array_unique($excluPts);

        //COUNTING NUMERATOR NOW
        $denoPts		= array_diff($denoPts,$excluPts);
        $inverse_NUM_pts = array();
        if(count($denoPts)>0){
            $denoPtsStr = implode(',',$denoPts);
            $Num_Q	= "SELECT DISTINCT(rad_patient_id), GROUP_CONCAT(rad_name) as radiologies FROM rad_test_data 
                       WHERE (rad_performed_date BETWEEN '$dtfrom1' AND '$dtupto1') 
                       AND rad_patient_id IN ($denoPtsStr) 
                       GROUP BY rad_patient_id 
                       HAVING (UPPER(radiologies) NOT RLIKE 'X RAY OF LOWER SPINE|XRAY OF LOWER SPINE|X-RAY OF LOWER SPINE' 
                               OR UPPER(radiologies) NOT LIKE '%MRI OF LOWER SPINE%' 
                               OR UPPER(radiologies) NOT RLIKE 'CT SCAN OF LOWER SPINE|CTSCAN OF LOWER SPINE|CT-SCAN OF LOWER SPINE')";
            $Num_Res	= imw_query($Num_Q);
            if($Num_Res && imw_num_rows($Num_Res)>0){
                while($Num_rs = imw_fetch_assoc($Num_Res)){
                    $inverse_NUM_pts[] = $Num_rs['rad_patient_id'];
                }
            }
        }
        $denoNumExcl['numerator']	= array_diff($denoPts,$inverse_NUM_pts);		
        return $denoNumExcl;	
    }
    
    
    function getNQF0086($commaPts = ''){
        //CMS-Clinical Alternative Core #NQF0086 - Primary Open Angle Guaucoma
        global $objDb; global $provider; global $dtfrom; global $dtupto; global $objDataManage;
        $denoNumExcl = array();	$ptIDs	= array();
        
        $dtfrom1 = $this->dbdtfrom;
        $dtupto1 = $this->dbdtupto;

        $totalPtIDs = $this->aged_get_denominator($this->provider,18);

        if($totalPtIDs!=0){
            $tempDeno = $this->get_patients_by_visits($totalPtIDs,2);//patients having 2 or more office visits.
        }
        if(count($tempDeno)>0){
            $totalPtIDs2 = implode(", ",$tempDeno);
        }else{$totalPtIDs=0;}

        if($commaPts != ''){$totalPtIDs = $totalPtIDs2 = $commaPts;}

        if($totalPtIDs!=0){
            //to get patients having diagnosis of POAG (Primary Open Angle Glaucoma).
            $query = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log 
                      WHERE pt_id IN($totalPtIDs2) 
                      AND ((LOWER(problem_name) like '%glaucoma%' 
                             AND LOWER(problem_name) like '%primary%' 
                             AND LOWER(problem_name) like '%open%' 
                             AND LOWER(problem_name) like '%angle%') 
                           OR (UPPER(problem_name) like '%POAG%') or problem_name RLIKE 'H40.11X1|H40.11X2|H40.11X3|H40.10X0|H40.10X1|H40.10X2|H40.10x3|H40.10X4|H40.11X0|H40.11X4|h40.1210|H40.1211|H40.1212|H40.1213|H40.1214|H40.1220|H40.1221|H40.1222|H40.1223|H40.1224|H40.1230|H40.1231|H40.1232|H40.1233|H40.1234|H40.1290|H40.1291|H40.1292|H40.1293|H40.1294|H40.151|H40.152|H40.153|H40.159'
                           ) 
                      AND (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') 
                      GROUP BY problem_id HAVING (status='Active')";
            $denoPTs1 = $this->getPtIdFun($query,'pt_id');

            $query4 = "SELECT pt_id FROM pt_problem_list 
                      WHERE pt_id IN($totalPtIDs2) 
                      AND ((LOWER(problem_name) like '%glaucoma%' 
                             AND LOWER(problem_name) like '%primary%' 
                             AND LOWER(problem_name) like '%open%' 
                             AND LOWER(problem_name) like '%angle%') 
                           OR (UPPER(problem_name) like '%POAG%') or problem_name RLIKE 'H40.11X1|H40.11X2|H40.11X3|H40.10X0|H40.10X1|H40.10X2|H40.10x3|H40.10X4|H40.11X0|H40.11X4|h40.1210|H40.1211|H40.1212|H40.1213|H40.1214|H40.1220|H40.1221|H40.1222|H40.1223|H40.1224|H40.1230|H40.1231|H40.1232|H40.1233|H40.1234|H40.1290|H40.1291|H40.1292|H40.1293|H40.1294|H40.151|H40.152|H40.153|H40.159'
                           ) 
                      AND (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') LOWER(status)='active'";
            $denoPTs = $this->getPtIdFun($query4,'pt_id');

            $denoNumExcl['denominator'] = array_unique(array_merge($denoPTs1,$denoPTs));
            $pid = implode(', ',$denoNumExcl['denominator']);

            $query2 = "SELECT patient_id, exam_date FROM chart_optic 
                       WHERE patient_id IN($pid) 
                       AND (DATE_FORMAT(exam_date,'%Y-%m-%d') BETWEEN '$dtfrom1' AND '$dtupto1') 
                       AND (cdr_od_summary LIKE '%C:D%' OR cdr_os_summary LIKE '%C:D%')";
            $denoNumExcl['numerator'] = $this->getPtIdFun($query2,'patient_id');
        }
        $denoNumExcl['exclusion'] = array(); //$this->get_excluded_patients($denoNumExcl['numerator'],array('NQF0086'));
        return $denoNumExcl;
    }//end of getNQF0086.
    
    
    function getNQF0088($commaPts = ''){
        //CMS-Clinical Alternative Core #NQF0088 - Diabetic Retinopathy
        global $objDb; global $provider; global $dtfrom; global $dtupto; global $objDataManage;
        $dtfrom1 = $this->dbdtfrom;
        $dtupto1 = $this->dbdtupto;

        $totalPtIDs = $this->aged_get_denominator($provider,18);
        $denoNumExcl = array();	$ptIDs = array(); $ptIDs2 = array(); $ptIDs3 = array();
        $currDt = date('Y-m-d'); 
		$ArrDtFrom = explode('-',$currDt);
		$year 	= $ArrDtFrom[0];
		$month	= $ArrDtFrom[1];
		$day 	= $ArrDtFrom[2];
		$chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-1));

        if($totalPtIDs!=0){
            $tempDeno = $this->get_patients_by_visits($totalPtIDs,2);//patients having 2 or more office visits.
        }
        if(count($tempDeno)>0){
            $totalPtIDs = implode(", ",$tempDeno);
        }else{$totalPtIDs=0;}

        $diab_DX = '250.50|250.51|362.01|362.02|362.03|362.04|362.05|362.06|362.07|E08.311|E08.319|E08.321|E08.329|E08.331|E08.339|E08.349|E08.351|E08.359|E09.311|E09.319|309.321|E09.329|E09.331|E09.339|E09.349|E09.351|E09.359|E10.311|E10.319|E10.321|E10.329|E10.331|E10.339|E10.349|E10.351|E10.359|E11.311|E11.319|E11.321|E11.329|E11.331|E11.339|E11.349|E11.351|E11.359|E13.311|E13.319|E13.321|E13.329|E13.331|E13.339|E13.349|E13.351|E13.359';
        if($commaPts != ''){$totalPtIDs = $commaPts;}
        if($totalPtIDs==0)return $denoNumExcl;

        //to get patients having diagnosis of DIABETIC RETINOPATHY
        $query1 = "SELECT DISTINCT(pt_id), GROUP_CONCAT(DISTINCT(status)) as Grp_Status FROM pt_problem_list_log 
                    WHERE pt_id IN($totalPtIDs) 
                    AND problem_name RLIKE '".$diab_DX."' AND 
                    (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') 
                    GROUP BY problem_id HAVING (Grp_Status='Active')";
        $tmpARR = $this->getPtIdFun($query1,'pt_id');
        $denoNumExcl['denominator'] = $tmpARR;

        $totalPtIDs1 = implode(", ",$denoNumExcl['denominator']);
        //to get patients having FUNDUS EXAM DONE & MACULA EDEMA recorded
        $query2 = "SELECT DISTINCT(crv.patient_id) FROM chart_retinal_exam crv WHERE crv.patient_id IN($totalPtIDs1) 
                    AND (LOWER(retinal_od_summary) like '%macular edema%' OR LOWER(retinal_os_summary) like '%macular edema%') 
                    AND (LOWER(retinal_od_summary) like '%npdr%' OR LOWER(retinal_os_summary) like '%npdr%') 
                    AND crv.exam_date >= '".$chkDt."'";
        $ptIDs2 = $this->getPtIdFun($query2,'patient_id');
        $totalPtIDs2 = implode(", ",$ptIDs2);
        /*
        //to get patients having LEVEL OF SEVERITY O RETINOPATHY recorded.
        $query3 = "SELECT DISTINCT(vsm.patient_id),vsp.* FROM vital_sign_master vsm
                    INNER JOIN vital_sign_patient vsp ON (vsp.vital_master_id=vsm.id AND vsp.vital_sign_id ='10' AND vsp.range_vital <>'')
                    WHERE vsm.patient_id IN($totalPtIDs2) AND vsm.date_vital >= '".$chkDt."' AND vsm.status <> '1'";
        $denoNumExcl['numerator'] 	= getPtIdFun($query3,'patient_id');*/
        $denoNumExcl['numerator'] 	= $ptIDs2;
        $denoNumExcl['exclusion']	= array();//get_excluded_patients($denoNumExcl['numerator'],array('NQF0088'));
        return $denoNumExcl;
    }//end of getNQF0088.
    
    
    function getNQF0089($commaPts = ''){
        //CMS-Clinical Alternative Core #NQF0089 - Diabetic Retinopathy, PVC
        global $objDb; global $provider; global $dtfrom; global $dtupto; global $objDataManage;
        $dtfrom1 = $this->dbdtfrom;
        $dtupto1 = $this->dbdtupto;

        $totalPtIDs = $this->aged_get_denominator($provider,18);
        $denoNumExcl = array();	$ptIDs = array(); $ptIDs2 = array(); $ptIDs3 = array();
		
		$Arrdtfrom1	= explode('-',$dtfrom1);
		$year	= $Arrdtfrom1[0];
		$month	= $Arrdtfrom1[1];
		$day	= $Arrdtfrom1[2];
        $chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-1));

        $tempDeno = $this->get_patients_by_visits($totalPtIDs,2);	
        $totalPtIDs = implode(", ",$tempDeno);
        $diab_DX = '250.50|250.51|362.01|362.02|362.03|362.04|362.05|362.06|362.07|E08.311|E08.319|E08.321|E08.329|E08.331|E08.339|E08.349|E08.351|E08.359|E09.311|E09.319|309.321|E09.329|E09.331|E09.339|E09.349|E09.351|E09.359|E10.311|E10.319|E10.321|E10.329|E10.331|E10.339|E10.349|E10.351|E10.359|E11.311|E11.319|E11.321|E11.329|E11.331|E11.339|E11.349|E11.351|E11.359|E13.311|E13.319|E13.321|E13.329|E13.331|E13.339|E13.349|E13.351|E13.359';
        if($commaPts != ''){$totalPtIDs = $commaPts;}

        if($totalPtIDs==0)return $denoNumExcl;

        //to get patients having diagnosis of DIABETIC RETINOPATHY
        $query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log 
                    WHERE pt_id IN($totalPtIDs) 
                    AND problem_name RLIKE '".$diab_DX."' 
                    AND (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') 
                      GROUP BY problem_id HAVING (status='Active')";
        $tmpARR = $this->getPtIdFun($query1,'pt_id');
        $denoNumExcl['denominator'] = array_unique($tmpARR);

        //to get patients having diagnosis of DIABETIC RETINOPATHY
        $query1 = "SELECT pt_id FROM pt_problem_list 
                    WHERE pt_id IN($totalPtIDs) 
                    AND problem_name RLIKE '".$diab_DX."' 
                    AND (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') AND status='Active'";
        $tmpARR2 = $this->getPtIdFun($query1,'pt_id');echo imw_error();
        $denoNumExcl['denominator'] = array_unique(array_merge($tmpARR,$tmpARR2));


        $totalPtIDs1 = implode(", ",$denoNumExcl['denominator']);

        //to get patients having FUNDUS EXAM DONE & MACULA EDEMA recorded
        $query2 = "SELECT DISTINCT(crv.patient_id) FROM chart_retinal_exam crv 
                    WHERE crv.patient_id IN($totalPtIDs1) 
                    AND (LOWER(retinal_od_summary) like '%macular edema%' OR LOWER(retinal_os_summary) like '%macular edema%') 
                    AND crv.exam_date >= '".$chkDt."'";

            $result2 = imw_query($query2);
        if($result2 && imw_num_rows($result2) >= 1){
            while($rs2 		= imw_fetch_assoc($result2)){
                $ptIDs2[] 	= $rs2['patient_id'];
            }
            $totalPtIDs2 = implode(", ",$ptIDs2);

            //to get patients having documented communication REGARDING FINDINGS OF MACULAR OR FUNDUS EXAM WITHING LAST 12 MONTHS.
            $query3 = "SELECT DISTINCT(patient_id) FROM patient_consult_letter_tbl WHERE patient_id IN ($totalPtIDs2) 
                        AND status = '0' AND LOWER(templateData) LIKE '%macula%' AND LOWER(templateData) LIKE '%edema%'";

            $denoNumExcl['numerator'] 	= $this->getPtIdFun($query3,'patient_id');
        }
        if($commaPts != ''){
            $denoNumExcl['exclusion']=$ptIDs2;
        }else{
            $denoNumExcl['exclusion']	= array();//$this->get_excluded_patients($denoNumExcl['numerator'],array('NQF0089'));
        }
        return $denoNumExcl;
    }
    
    
    function getNQF0055($commaPTs=''){
        //CMS-Clinical Alternative Core #NQF0055 - Diabetic Eye Exam
        global $objDb; global $provider; global $dtfrom; global $dtupto; global $objDataManage;
        $denoNumExcl = array();	$ptIDs = array(); $ptIDs2 = array(); $ptIDs3 = array();
        $dtfrom1 = $this->dbdtfrom;
        $dtupto1 = $this->dbdtupto;
        $totalPtIDs = $this->aged_get_denominator($provider,'18-75');
        //$diab_MelDX = '250.00|250.01|250.02';

        $diab_MelDX = 'E10.10|E10.11|E10.21|E10.22|E10.29|E10.36|E10.39|E10.40|E10.41|E10.42|E10.43|E10.44|E10.49|E10.51|E10.52|E10.59|E10.610|E10.618|E10.620|E10.621|E10.622|E10.628|E10.630|E10.638|E10.641|E10.649|E10.65|E10.69|E10.8|E10.9|E11.00|E11.0l|E11.21|E11.22|E11.29|E11.36|E11.39|E11.40|E11.41|E11.42|E11.43|E11.44|E11.49|E11.51|E11.52|E11.59|E11.610|E11.618|E11.620|E11.621|E11.622|El1.628|El1.630|E11.638|E11.641|E11.649|E11.65|E11.69|E11.8|E11.9|E13.00|E13.01|E13.10|E13.11|E13.21|E13.22|E13.29|E13.36|E13.39|E13.40|E13.41|E13.42|E13.43|E13.44|E13.49|E13.51|E13.52|E13.59|E13.610|E13.618|E13.620|E13.621|E13.622|E13.628|E13.630|E13.638|E13.641|E13.649|E13.65|E13.69|E13.8|E13.9|024.011|024.012|024.013|024.019|024.02|024.03|024.111|024.112|024.113|024.119|024.12|024.13|024.311|024.312|024.313|024.319|024.32|024.33|024.811|024.812|024.813|024.819|024.82|024.83';

        $diab_MelDX .= '|250.50|250.51|362.01|362.02|362.03|362.04|362.05|362.06|362.07|E08.311|E08.319|E08.321|E08.329|E08.331|E08.339|E08.349|E08.351|E08.359|E09.311|E09.319|309.321|E09.329|E09.331|E09.339|E09.349|E09.351|E09.359|E10.311|E10.319|E10.321|E10.329|E10.331|E10.339|E10.349|E10.351|E10.359|E11.311|E11.319|E11.321|E11.329|E11.331|E11.339|E11.349|E11.351|E11.359|E13.311|E13.319|E13.321|E13.329|E13.331|E13.339|E13.349|E13.351|E13.359';

        if($commaPTs != ''){$totalPtIDs = $commaPTs;}
        //PROBLEM DATE <= MEASURE START DATE
        $chkDt = $dtfrom1;
		$Arrdtfrom1	= explode('-',$dtfrom1);
		$year	= $Arrdtfrom1[0];
		$month	= $Arrdtfrom1[1];
		$day	= $Arrdtfrom1[2];
        $chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-2));

        //getting patients having diagnosis of DIABETESE
        $query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($totalPtIDs) 
                        AND problem_name RLIKE '".$diab_MelDX."'  
                        AND (onset_date BETWEEN '$chkDt' AND '$dtupto1') 
                        GROUP BY problem_id HAVING (status='Active')";
        $tmpARR = $this->getPtIdFun($query1,'pt_id');

        //getting patients having diagnosis of DIABETESE
        $query2 = "SELECT pt_id FROM pt_problem_list WHERE pt_id IN ($totalPtIDs) 
                        AND problem_name RLIKE '".$diab_MelDX."'  
                        AND (onset_date BETWEEN '$chkDt' AND '$dtupto1') AND status='Active'";
        $tmpARR2 = $this->getPtIdFun($query2,'pt_id');

        $tempTotal = implode(', ',array_unique(array_merge($tmpARR,$tmpARR2)));
        $denoNumExcl['denominator'] = $this->get_patients_by_visits($tempTotal,1);	


        $totalPtIDs1 = implode(", ",$denoNumExcl['denominator']);

        //chart_vision excluded from array below
        $arr_tables = array('chart_pupil', 'chart_eom', 'chart_external_exam', 'chart_lids', 'chart_lesion', 'chart_lid_pos', 'chart_lac_sys', 'chart_iop', 'chart_gonio', 'chart_dialation','chart_vitreous', 'chart_macula','chart_retinal_exam','chart_periphery','chart_blood_vessels', 
					'chart_conjunctiva', 'chart_cornea', 'chart_ant_chamber','chart_iris','chart_lens');
        //to get patients having FUNDUS EXAM DONE & MACULA EDEMA recorded
        foreach($arr_tables as $table){
            if($table=='chart_pupil'){$ptfield = 'patientId';}else{$ptfield = 'patient_id';}
            $query2 = "SELECT DISTINCT(".$ptfield.") as patient_id FROM ".$table." WHERE ".$ptfield." IN($totalPtIDs1)";
            $result2 = imw_query($query2);
            if($result2 && imw_num_rows($result2) >= 1){
                while($rs2 		= imw_fetch_assoc($result2)){
                    $ptIDs2[] 	= $rs2['patient_id'];
                }
            }
        }//end of foreach.
        $ptIDs2 = array_unique($ptIDs2);
        $totalPtIDs2 = implode(", ",$ptIDs2);

        //to get exclusions.
        $query3 = "SELECT DISTINCT(ppl.pt_id) FROM pt_problem_list ppl WHERE ppl.pt_id IN($totalPtIDs2) 
                    AND ppl.problem_name LIKE '%gestational diabetes%' AND ppl.status='Active'";
        $result3 = imw_query($query3);
        if($result3 && imw_num_rows($result3) >= 1){
            while($rs3 		= imw_fetch_assoc($result3)){
                $ptIDs3[] 	= $rs3['pt_id'];
            }
        }
        $denoNumExcl['numerator'] = $ptIDs2;
        if(count($ptIDs3)>0){
            $denoNumExcl['exclusion'] = $ptIDs3;
            $denoNumExcl['numerator'] = array_diff($ptIDs2,$ptIDs3);
        }
        return $denoNumExcl;
    }
    
    
} //END CLASS
?>
