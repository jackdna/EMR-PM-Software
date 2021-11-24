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
ini_set('memory_limit', '2048M');
set_time_limit(600);
require_once(dirname(__FILE__).'/../../../library/classes/common_function.php');
class MUR_Reports
{
	private $createdBy,$arr_task,$provider,$dtfrom,$dtupto,$dbdtfrom,$dbdtupto;
	public $task, $taskNum, $int_percent,$temp;
	
	###################################################################
	#	constructor function to set commonally used variable on page
	###################################################################
	function __construct(){
		/*******INTIAL SETTINGS HERE*****/
		$this->temp_createdBy 	= $this->get_provider_ar($_SESSION['authId']);
		$this->createdBy 		= $temp_createdBy[$_SESSION['authId']];
		
		$this->arr_task 		= array('Invalid','Cal-Core/Menu','Cal- CMS', 'Analyze', 'Attestation', 'PQRI XML');
		$this->taskNum 			= isset($_REQUEST['task']) ? intval($_REQUEST['task']) : 0;
		$this->task 			= $arr_task[$this->taskNum];
		
		$this->provider 		= isset($_REQUEST['provider']) ? trim(strip_tags($_REQUEST['provider'])) : 0;
		$this->dtfrom 			= isset($_REQUEST['dtfrom']) ? trim(strip_tags($_REQUEST['dtfrom'])) : 0;
		$this->dtupto 			= isset($_REQUEST['dtupto']) ? trim(strip_tags($_REQUEST['dtupto'])) : 0;

		$this->dbdtfrom			= getDateFormatDB($this->dtfrom);
		$this->dbdtupto			= getDateFormatDB($this->dtupto);
		
	}
	
	/****USED IN DROPDOWN AND OTHER MAPPING****/
	function get_provider_ar($proId = 0){

		if($proId == 0){
			$qry_providers = "select id, fname, lname, mname from users where user_type IN (1,12) AND delete_status = 0 order by lname";
		}
		else{
			$qry_providers = "select id, fname, lname, mname from users where id='$proId' LIMIT 0,1";	
		}
		$result = imw_query($qry_providers);
		if($result && imw_num_rows($result)>=1){
			while($rs = imw_fetch_assoc($result)){
				$id = $rs['id'];
				$arr_proName['LAST_NAME']=$rs['lname'];
				$arr_proName['FIRST_NAME']=$rs['fname'];
				$arr_proName['MIDDLE_NAME']=$rs['mname'];
				$pro_name[$id] = changeNameFormat($arr_proName);
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
	
	function get_denominator($pro_id, $age='',$ageMethod='',$NQF=''){
		$Sch_facility_IDs = $this->get_active_facilities();
		$chkDt='';
		$ptIDs = array(); $ptAge = array();
		$ageFrom1 = '';
		$ageTo1 = '';
		if(!empty($age)){
			$currDt = date('Y-m-d');
			if($ageMethod=='yes') {
				list($mm,$dd,$yy) = explode('-',$this->dtfrom);
				$currDt = date('Y-m-d',mktime(0,0,0,$mm,$dd,$yy));
	
			}
			list($year,$month,$day) = explode('-',$currDt);
			
			list($ageFrom,$ageUpto) = explode('-',$age);
			$ageFrom1 = $ageFrom;
			$ageTo1 = $ageUpto;
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
//			$query = "SELECT distinct(pd.pid) AS 'sa_patient_id', pd.DOB as dob 
//				    FROM 
//					patient_data pd
//				    WHERE 
//					pd.lname != 'doe' 
//					AND pd.DOB <= '".$ageFrom."' $ageUpToQry
//					AND pd.id <> 0
//					";
		    
			$query = "SELECT distinct(pd.pid) AS 'sa_patient_id', pd.DOB as dob 
				    FROM 
					patient_data pd
				    WHERE 
					pd.lname != 'doe' 
					AND pd.id <> 0
					";
			
			if( !empty($ageTo1) && $ageTo1 != '' )
			{
			    $query .= " AND (TIMESTAMPDIFF(YEAR, pd.DOB, '".$this->dbdtfrom."') BETWEEN '".$ageFrom1."' AND '".$ageTo1."')";
			}
			else
			{
			    $query .= " AND TIMESTAMPDIFF(YEAR, pd.DOB, '".$this->dbdtfrom."') >= '".$ageFrom1."'";
			}
		}
		
        // Commented this is static check for particular NQF
//		if( trim($NQF) != '')
//		{
//		    $query .= " AND pd.External_MRN_4='".$NQF."'";
//		}
                
        if( isset($_REQUEST['patient_id']) && empty($_REQUEST['patient_id']) == false )
        {
            $query .= " AND pd.id = '".$_REQUEST['patient_id']."'";
        }
		
		$result = imw_query($query);
		if($result && imw_num_rows($result)>=1){
			while($rs = imw_fetch_assoc($result)){
				if(in_array($rs['sa_patient_id'],$ptIDs)) continue;
				$ptIDs[] = $rs['sa_patient_id'];
				$ptAge[] = $rs['dob'];
			}
			if($ageMethod=='yes'){return array($ptIDs,$ptAge);}
			else{return $ptIDs;}
		}
		else{
			return $ptIDs;
		}
	}
	
	
	function aged_get_denominator($provider,$age,$NQF=''){
		$patientIDs = $this->get_denominator($provider,$age, '', $NQF);
		$totalPtIDs = implode(', ',$patientIDs);
		if(!$totalPtIDs || empty($totalPtIDs)){$totalPtIDs = '0';}
		return $totalPtIDs;
	}//end of aged_get_denominator
	
	function getPatientsByAge($age='',$commaIDs=''){
		$pts = array(0);
		if(!empty($age)){
			$currDt = date('Y-m-d');
			if($ageMethod=='yes') {
				list($mm,$dd,$yy) = explode('-',$this->dtfrom);
				$currDt = date('Y-m-d',mktime(0,0,0,$mm,$dd,$yy));
	
			}
			list($year,$month,$day) = explode('-',$currDt);
			
			list($ageFrom,$ageUpto) = explode('-',$age);
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
		$taskNum = $this->taskNum;
		$achieved = round(($obtained*100)/$full);
		$this->int_percent = $achieved;
		if($full==0){return 'NA';}
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
	
	function get_patients_by_encounter($patients, $no_of_visits=0)
	{
	    $ptIDs = array();
	    
	    $comma_patients = $patients;
	    
	    $query = "SELECT count(id) cnt, patient_id FROM chart_master_table WHERE patient_id IN($comma_patients) GROUP BY patient_id HAVING cnt >= '$no_of_visits'";
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
	}
	
	function get_patients_by_visits($patients, $no_of_visits = 0, $ageMethod='',$begDate='',$excludeTemplate='',$begEndDate=''){
		if($begDate!=''){$dtfrom1 = $begDate;}
		if($begEndDate!=''){$dtupto1 = $begEndDate;}
		
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
		$query = "SELECT count(id) cnt, patient_id FROM chart_master_table WHERE patient_id IN($comma_patients) AND (date_of_service>='".$this->dbdtfrom."' AND date_of_service<='".$this->dbdtupto."')".$querypart1." GROUP BY patient_id HAVING cnt >= '$no_of_visits'";
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
		$arr_IPPtIDs = $arr_val['ipop'];
		$arr_DenoPtIDs = $arr_val['denominator'];
		$arr_NumePtIDs = $arr_val['numerator'];
		$arr_ExcuPtIDs = $arr_val['exclusion'];
		$arr_DenoExcepPtIDs = $arr_val['denominatorException'];
		if($server=='noPt'){
			$arr_val['ipop'] = $arr_val['ipop'];
			$arr_val['denominator'] = $arr_val['denominator'];
			$arr_val['numerator'] = $arr_val['numerator'];	
			$arr_val['denominatorException'] = $arr_val['denominatorException'];
		}else{
			$arr_val['ipop'] = count($arr_val['ipop']);
			$arr_val['denominator'] = count($arr_val['denominator']);
			$arr_val['numerator'] = count($arr_val['numerator']);
			$arr_val['denominatorException'] = count($arr_val['denominatorException']);
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
		/*THIS IS commented		
				$deno_should_be = round(($arr_val['numerator'] * 100)/$full);
				if($arr_val['denominator'] < $deno_should_be){
					$arr_val['denominator'] =  $arr_val['denominator'].' ('.($deno_should_be - $arr_val['denominator']).')';
				}
		*/		
				if($temp_full_numerator < $nume_should_be){
					$arr_val['numerator'] =  $arr_val['numerator'].' ('.($nume_should_be - $temp_full_numerator).')';
				}
			}
			if(((defined('APP_DEBUG_MODE') && constant('APP_DEBUG_MODE')==1) || $this->get_MUR_audit_status()) && ($server!='noPt')){//just to check which patients are appearing.
				asort($arr_IPPtIDs);
				asort($arr_DenoPtIDs);
				asort($arr_NumePtIDs);
				asort($arr_ExcuPtIDs);
				asort($arr_DenoExcepPtIDs);
				$arr_val['ipop'] = '<span class="link_cursor" onDblClick = "showPTs(\''.implode(', ',$arr_IPPtIDs).'\',this)">'.$arr_val['ipop'].'</span>';
				$arr_val['denominator'] = '<span class="link_cursor" onDblClick = "showPTs(\''.implode(', ',$arr_DenoPtIDs).'\',this)">'.$arr_val['denominator'].'</span>';
				$arr_val['numerator'] = '<span class="link_cursor" onDblClick = "showPTs(\''.implode(', ',$arr_NumePtIDs).'\',this)">'.$arr_val['numerator'].'</span>';
				$arr_val['exclusion'] = '<span class="link_cursor" onDblClick = "showPTs(\''.implode(', ',$arr_ExcuPtIDs).'\',this)">'.$arr_val['exclusion'].'</span>';
				$arr_val['denominatorException'] = '<span class="link_cursor" onDblClick = "showPTs(\''.implode(', ',$arr_DenoExcepPtIDs).'\',this)">'.$arr_val['denominatorException'].'</span>';
			}
		
			if($server=='Boston'){
				$arr_val['percent']		= 'N/A';
				$arr_val['denominator']	= 0;
				$arr_val['numerator']	= 0;
				$arr_val['exclusion']	= 0;
			}
		}
		else{
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
				$q2 = "SELECT sb.patientId, pi.cptCode AS 'procOrder' FROM superbill sb INNER JOIN procedureinfo pi ON(sb.idSuperBill=pi.idSuperBill) WHERE sb.del_status = '0' AND sb.formid IN ($all_chart_IDs)";
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
			$fromCheckList = "cmt.providerId='".$provider."' AND ";
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
		//		  AND osacn.logged_provider_id='".$provider."' 
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
		
		$total_erx = 0;
		$query2 = "SELECT SUM(prescriptions) AS total_erx FROM emdeon_erx_count WHERE 
					provider_id='".$this->provider."' AND (date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
		$result2 = imw_query($query2);
		if($result2 && imw_num_rows($result2)>=1){
			$rs2 = imw_fetch_assoc($result2);
			$total_erx = $rs2['total_erx'];
		}
		
		$denoNumExcl['denominator'] = intval($totalHandWritten)+intval($total_erx);
		$denoNumExcl['numerator'] = intval($total_erx);
		$denoNumExcl['exclusion'] = array();
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
	
	function getMedReconcil_2018($totalPtIDs){
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
			$q2 = "SELECT DISTINCT(patient_id) AS patient_id, COUNT(section_done) as section_count FROM ccd_incorporate_log 
				   WHERE section_done IN ('medications','allergies','problem_list') 
				   AND (DATE_FORMAT(done_on,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
				   AND patient_id IN($totalPtIDs2) 
				   GROUP BY (scan_doc_tbl_id) HAVING section_count = 3";
			$ptIDs2 = $this->getPtIdFun($q2,'patient_id');
			$denoNumExcl['numerator'] = $ptIDs2; //For whom all three section reconciliation done.
		}
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;
	}// end of getMedReconcil
	
	function SendSummaryCareRec($totalPtIDs,$subSection){
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$q1 = "SELECT DISTINCT(cap.form_id),cap.patient_id FROM chart_assessment_plans cap 
			   JOIN chart_master_table cmt ON (cmt.id=cap.form_id) 
			   WHERE cmt.delete_status='0' AND cmt.purge_status='0' AND cap.doctor_name!='' 
			   AND (cmt.date_of_service BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') AND cap.patient_id IN ($totalPtIDs) 
			   AND cmt.providerId='".$this->provider."'";//echo $q1.'<hr>';
		$denoNumExcl['denominator'] = $this->getPtIdFun($q1,'patient_id');
		$form_ID_arr = $this->getPtIdFun($q1,'form_id');
		
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
					 AND dm.imedic_user_id = '".$this->provider."' AND dma.patient_id IN ($totalPtIDs2) AND dm.folder_type='3' AND del_status = '0'";
				$num1c = $this->getPtIdFun($q4,'id');
			
				$denoNumExcl['numerator'] = array_unique(array_merge($num1a,$num1b,$num1c));
				break;
			case 'm2';
				$q2="SELECT dma.patient_id as id FROM direct_messages dm JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
					 WHERE (DATE_FORMAT(local_datetime,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
					 AND dm.imedic_user_id = '".$this->provider."' AND dma.patient_id IN ($totalPtIDs2) AND dma.form_id IN ($total_formIDs)";
				$denoNumExcl['numerator'] = $this->getPtIdFun($q2,'id');
				break;
		}
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;
	}
	
	function ReceiveSummaryCareRec($totalPtIDs){
		if($totalPtIDs=='0') return;
		$denoNumExcl = array();
		$q1="SELECT dma.patient_id as id FROM direct_messages dm 
			 JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
			 WHERE (DATE_FORMAT(dm.local_datetime,'%Y-%m-%d') BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
				AND dm.folder_type='1' AND del_status = '0' 
			 	AND dma.patient_id IN ($totalPtIDs) 
				
				";
		
		
		$denoNumExcl['numerator'] = $this->getPtIdFun($q2,'id');
		
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;
	}
	
	function getPatientSecureMessaging($totalPtIDs){
		$denoNumExcl = array();
		if($totalPtIDs==0){return $denoNumExcl;}
		$denoNumExcl['denominator'] = explode(',',$totalPtIDs);
		$q1= "SELECT DISTINCT(receiver_id) FROM patient_messages WHERE receiver_id IN ($totalPtIDs) 
				AND communication_type='1' AND msg_data != '' AND sender_id = '".$this->provider."' 
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
		
		
		$denoNumExcl['numerator'] = array();
		$denoNumExcl['exclusion'] = array();
		return $denoNumExcl;
	}
	
	/*******************CQM MEASURES**********************************/
	function getNQF0018($commaPatients=''){
		//CQM #NQF0018 - Hypertension.
		//if($totalPtIDs=='0') return;
		$denoNumExcl = array();	$ptIDs	= array(); $ptIDs1 = array(); $ptIDs2 = array();
		
		$denoNumExcl['ipop'] = array();
		$denoNumExcl['denominator'] = array();
		$denoNumExcl['exclusion'] = array();
		$denoNumExcl['numerator'] = array();
		$denoNumExcl['denominatorException'] = array();
	
		//getting patients of age 18-85
		$totalPtIDs = $this->aged_get_denominator($this->provider,'18-85', '0018');
		
		if($commaPatients!=''){$totalPtIDs = $commaPatients;}
		
		//PROBLEM DATE <= MEASURE START DATE
		$chkDt = $this->dbdtfrom;
		list($year,$month,$day) = explode('-',$this->dbdtfrom);
		$chkDt = date('Y-m-d',mktime(0,0,0,$month-6,$day,$year));
		
		$chkDtEnd = date('Y-m-d',mktime(0,0,0,$month+6,$day,$year));	/*Six months from start of measurement period*/
		
		//getting patients having diagnosis of HYPERTENSION (from within 6 months before MU start period)
		$query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($totalPtIDs) 
						AND (problem_name RLIKE '401.0|401.1|401.9' OR LOWER(problem_name) LIKE '%hypertension%' OR LOWER(problem_name) LIKE '%hypertention%' OR LOWER(problem_name) LIKE '%i10%')
						AND (onset_date BETWEEN '".$this->dbdtfrom."' AND '".$chkDtEnd."') 
						GROUP BY problem_id HAVING (status='Active')";
		$ptIDs = $this->getPtIdFun($query1,'pt_id');
		
		$query2 = "SELECT pt_id FROM pt_problem_list WHERE pt_id IN ($totalPtIDs) 
						AND (problem_name RLIKE '401.0|401.1|401.9' OR LOWER(problem_name) LIKE '%hypertension%' OR LOWER(problem_name) LIKE '%hypertention%' OR LOWER(problem_name) LIKE '%i10%')
						AND (onset_date BETWEEN '".$this->dbdtfrom."' AND '".$chkDtEnd."') AND LOWER(status)='active'";
		$ptIDs2 = $this->getPtIdFun($query2,'pt_id');
		
		$ptIDs = array_merge($ptIDs,$ptIDs2);
		unset($ptIDs2);
		
		//Getting patients having diagnosis of HYPERTESNION before Measurement Period
		$query3 = "SELECT pt_id FROM pt_problem_list WHERE pt_id IN ($totalPtIDs) 
						AND (problem_name RLIKE '401.0|401.1|401.9' OR LOWER(problem_name) LIKE '%hypertension%' OR LOWER(problem_name) LIKE '%hypertention%' OR LOWER(problem_name) LIKE '%i10%')
						AND ( onset_date < '".$this->dbdtfrom."' OR onset_date > '".$this->dbdtupto."') AND LOWER(status)='active'";
		$ptIDs3 = $this->getPtIdFun($query3,'pt_id');
		
		$ptIDs = array_merge($ptIDs,$ptIDs3);
		unset($ptIDs3);
		
		//getting patients having 1 or more visits.
		$ptIDs1 = $this->get_patients_by_visits(implode(',',$ptIDs),1);
		if(count($ptIDs1)>0){
			$DenoPtIDs = implode(', ',$ptIDs1);
		}
		else{
			return $denoNumExcl;
			$DenoPtIDs = 0;
		}
		
		$denoNumExcl['ipop'] = $ptIDs1;
		$denoNumExcl['denominator'] = $ptIDs1;
		
		$DenoPtIDs = implode(', ', $denoNumExcl['denominator']);
		
	//QUERYING FOR DENOMINATOR EXCLUSION
		$exclusions = array();
		
		/*Exclusions based on encounter "Discharged to Home for Hospice Care"*/
		$sql = "SELECT `sa`.`sa_patient_id` AS 'pt_id' FROM `schedule_appointments` `sa` INNER JOIN `inpatient_fields` `ipf` ON(`sa`.`id`=`ipf`.`appt_id`) WHERE `sa`.`sa_patient_id` IN (".$DenoPtIDs.") AND `field_code` IN('428361000124107', '428371000124100') AND (`sa`.`sa_app_end_date` BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
		
		$exclusions = array_merge($exclusions, $this->getPtIdFun($sql,'pt_id'));
		/*Exclusions based on encounter "Discharged to Home for Hospice Care"*/
		
		/*ExClusions based on Intervention "Hospice care ambulatory"*/
		$sql = "SELECT pid AS 'pt_id' FROM `lists` WHERE type='5' AND pid IN(".$DenoPtIDs.") "
                . " AND ( (LOWER(title) LIKE '%hospice%' AND LOWER(title) LIKE '%care%' AND LOWER(title) LIKE '%ambulatory%')"
                . " OR ccda_code IN('385765002', '385763009') ) AND (begdate BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
		$exclusions = array_merge($exclusions, $this->getPtIdFun($sql,'pt_id'));
		/*ExClusions based on Intervention "Hospice care ambulatory"*/
		
		/*Exclusion based on Diagnosis*/
		    /*List Snomed Codes*/
		    $sql = "SELECT DISTINCT(code) AS 'code' FROM snomed_valueset WHERE code_system = '2.16.840.1.113883.6.96' AND value_set IN ('2.16.840.1.113883.3.526.3.378', '2.16.840.1.113883.3.526.3.353', '2.16.840.1.113883.3.526.3.1002')";
		    $diagSnomed = $this->getPtIdFun($sql,'code');
		    $diagSnomed = implode("', '", $diagSnomed);
		    $diagSnomed = "'".$diagSnomed."'";
		   /*End Snomed Codes Listing*/
		
		$sql = "SELECT DISTINCT(pt_id) AS 'pt_id' FROM pt_problem_list_log WHERE pt_id IN ($DenoPtIDs) AND onset_date < '".$this->dbdtupto."' AND ((end_datetime BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') OR end_datetime = '0000-00-00 00:00:00') AND ccda_code IN($diagSnomed)";
		$exclusions = array_merge($exclusions, $this->getPtIdFun($sql,'pt_id'));
		    
		$sql = "SELECT DISTINCT(pt_id) AS 'pt_id' FROM pt_problem_list WHERE pt_id IN ($DenoPtIDs) AND onset_date < '".$this->dbdtupto."' AND ((end_datetime BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') OR end_datetime = '0000-00-00 00:00:00') AND ccda_code IN($diagSnomed)";
		
		$exclusions = array_merge($exclusions, $this->getPtIdFun($sql,'pt_id'));
		/*End Exclusion based on Diagnosis*/
		
		/*Procedure Exclusion*/
		    /*List Snomed Codes for procedures*/
		    $sql = "SELECT DISTINCT(code) AS 'code' FROM snomed_valueset WHERE code_system = '2.16.840.1.113883.6.96' AND value_set IN('2.16.840.1.113883.3.464.1003.109.12.1011', '2.16.840.1.113883.3.464.1003.109.12.1012', '2.16.840.1.113883.3.464.1003.109.12.1013')";
		    $procSnomed = $this->getPtIdFun($sql,'code');
		    $procSnomed = implode("', '", $procSnomed);
		    $procSnomed = "'".$procSnomed."'";
		   /*End Snomed Codes Listing for procedurs*/
		
		$sql = "SELECT DISTINCT(pid) AS 'pt_id' FROM lists WHERE pid IN($DenoPtIDs) AND type = 5 AND begdate <= '".$this->dbdtupto."' AND ccda_code IN($procSnomed)";
		$exclusions = array_merge($exclusions, $this->getPtIdFun($sql,'pt_id'));
		/*End Procedure Exclusion*/
		
		/*Exclusion based on Encounter*/
		    $visit_proc = array("ESRD Monthly Outpatient Services");
		    
		    $vistiCodes = array('90951', '90952', '90953', '90954', '90955', '90956', '90957', '90958', '90959', '90960', '90961', '90962', '90963', '90964', '90965', '90966', '90967', '90968', '90969', '90970', '90989', '90993', '90997', '90999', '99512');
		    $vistiCodes = implode("', '", $vistiCodes);
		    $vistiCodes = "'".$vistiCodes."'";

		    $sql = "SELECT DISTINCT(sb.patientId) AS 'pt_id' FROM superbill sb INNER JOIN procedureinfo pi ON(sb.idSuperBill = pi.idSuperBill) WHERE sb.patientId IN(".$DenoPtIDs.") AND pi.cptCode IN(".$vistiCodes.") ";
		    $exclusions = array_merge($exclusions, $this->getPtIdFun($sql,'pt_id'));
		
		/*End Encounter Exclusion*/
		
		$exclusions = array_unique($exclusions);	
		$denoNumExcl['exclusion'] = $exclusions; 
	//QUERYING FOR DENOMINATOR EXCLUSION
		
		$denoNumExcl['numerator']	= array();
		
		$tempNumPtArr = array_diff($denoNumExcl['denominator'], $denoNumExcl['exclusion']);
		
		if(count($tempNumPtArr)>0){
			$NumPtIDs = implode(',',$tempNumPtArr);
			//query for numerator. blood pressure recorded.
			if($NumPtIDs != '0'){
				
				$query = "SELECT DISTINCT(`patient_id`) AS 'patient_id' "
					. "FROM `chart_master_table` "
					. "WHERE `patient_id` IN($NumPtIDs)
					    AND (`date_of_service` BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') ";
				
				$NumPtIDs = $this->getPtIdFun($query,'patient_id');
				$NumPtIDs = implode(',',$NumPtIDs);
				
				$query = "SELECT vsm.patient_id, COUNT(vsm.patient_id) as cnt_pat FROM vital_sign_master vsm 
						  JOIN vital_sign_patient vsp ON (vsp.vital_master_id=vsm.id) 				  
						  WHERE vsm.patient_id IN($NumPtIDs) 
						  AND vsm.status='0' AND vsp.range_vital > 0 AND ((vsp.vital_sign_id = 1 AND vsp.range_vital < 140) or (vsp.vital_sign_id = 2 AND vsp.range_vital < 90))  
						  AND (`date_vital` BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')
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
		
	    #################### Calculate IPOP ####################
		$totalPtIDs = $this->aged_get_denominator($this->provider, 65, '0022');
		if($commaPatients!=''){$totalPtIDs = $commaPatients;}
		$ptIDs1 = $this->get_patients_by_encounter($totalPtIDs,1);
		
		/*IPOP Encounter Type - Procedure Codes Validation*/
		$visit_proc = array("Office Visit", "Ophthalmologic Outpatient Visit", "Face-to-Face Interaction", "Preventive Care Services - Established Office Visit, 18 and Up",  "Preventive Care Services-Initial Office Visit, 18 and Up",  "Annual Wellness Visit",  "Home Healthcare Services");
		
		$vistiCodes = array('99201', '99202', '99203', '99204', '99205', '99212', '99213', '99214', '99215', '92002', '92004', '92012', '92014', '12843005', '18170008', '185349003', '185463005', '185465003', '19681004', '207195004', '270427003', '270430005', '308335008', '390906007', '406547006', '439708006', '87790002', '90526000', '99395', '99396', '99397', '99385', '99386', '99387', 'G0438', 'G0439', '99341', '99342', '99343', '99344', '99345', '99347', '99348', '99349', '99350');
		$vistiCodes = implode("', '", $vistiCodes);
		$vistiCodes = "'".$vistiCodes."'";
		
		$ptIDs1 = implode(',', $ptIDs1);
		
		$sql = "SELECT DISTINCT(sb.patientId) AS 'pt_id' FROM superbill sb INNER JOIN procedureinfo pi ON(sb.idSuperBill = pi.idSuperBill) WHERE sb.patientId IN(".$ptIDs1.") AND pi.cptCode IN(".$vistiCodes.") AND (sb.dateOfService BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')  ";
		
		$ptIDs1 = $this->getPtIdFun($sql,'pt_id');
		/*End IPOP Encounter Type - Procedure Codes Validation*/
		
		if(count($ptIDs1)==0){return;}
		$denoNumExcl['ipop']	= $ptIDs1;
		
	    #################### End IPOP Calculation ####################
		
		$denoNumExcl['denominator'] = $denoNumExcl['ipop'];
		
		$strPtIds1=implode(',', $denoNumExcl['denominator']);
		
		/*Calculate Denominator Exclusions*/
		$sql1 = "SELECT DISTINCT(pid) AS 'pid' "
			. "FROM lists "
			. "WHERE "
			    . "pid IN(".$strPtIds1.") "
			    . "AND (LOWER(title) LIKE '%hospice%' AND LOWER(title) LIKE '%care%') "
			    . "AND proc_type='intervention' "
			    . "AND begdate <= '".$this->dbdtupto."' "
			    . "AND ("
				. "(begdate BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') "
				. "OR begdate >= '".$this->dbdtupto."' "
			    . ")";
		
		$ptIDsExc = $this->getPtIdFun($sql1,'pid');
		$denoNumExcl['exclusion']   = $ptIDsExc;
		/*End Denominator Exclusions*/
		
		/*Numerator Calculation*/
		$ptIDs2 = array_diff($denoNumExcl['denominator'], $denoNumExcl['exclusion']);
		$ptIDs2 = implode(',', $ptIDs2);
		
		$totalPtIDs = implode(',',$ptIDs1);
		
		/*Rx Norm Codes*/
		$sql0 = "SELECT DISTINCT(code) AS 'code' FROM snomed_valueset "
			. "WHERE (value_set='2.16.840.1.113883.3.464.1003.196.12.1253' OR value_set='2.16.840.1.113883.3.464.1003.196.12.1254') "
			. "AND LOWER(page)='medication'";
		$rxNorm = $this->getPtIdFun($sql0,'code');
		$rxNorm = implode(',', $rxNorm);
		/*Rx. Norm Codes*/
		
		$numQ = "SELECT pid,count(title) AS cnt FROM lists WHERE 
				 pid IN($ptIDs2) 
				 AND type IN (1,4) 
				 AND ccda_code IN(".$rxNorm.") 
				 AND (begdate BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
				 GROUP BY title";
		$resp = imw_query($numQ);
		
		$numerator = array();
		
		if($resp && imw_num_rows($resp)>0)
		{
		    while($row = imw_fetch_assoc($resp))
		    {
			if($m=='two' && $row['cnt']>1)
			{
			    array_push($numerator, $row['pid']);
			}
			elseif($m=='one')
			{
			    array_push($numerator, $row['pid']);
			}
		    }
		}
		$numerator = array_unique($numerator);
		$denoNumExcl['numerator'] = $numerator;
		/*End Numerator Calculation*/
		
		$denoNumExcl['denominatorException'] = array();
		
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
		list($year,$month,$day) = explode('-',$currDt);
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
		list($year,$month,$day) = explode('-',$currDt); $chkDt 	= date('Y-m-d',mktime(0,0,0,$month-6,$day,$year));
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

	function getNQF0028($row=''){
		//CMS-Clinical Core #NQF0028 - (IF nvr smoked, then add in Num; if smoker + cessation or cessation med given, then add in Num.)
		global $objDb; global $dtfrom; global $dtupto; global $objDataManage;
        $data = array();
 
		 $data['ipop'] = array();
		 $data['denominator'] = array();
		 $data['exclusion'] = array();
		 $data['numerator'] = array();
		 $data['denominatorException'] = array();
		
		$visit_proc=array("face-to-face interaction", "face to face interaction","face to face interaction   no ed", "Health & Behavioral Assessment - Individual", "Health and Behavioral Assessment - Initial", "Health and Behavioral Assessment, Reassessment", "Home Healthcare Services", "Occupational Therapy Evaluation", "Office Visit", "Ophthalmological Services", "Psych Visit - Diagnostic Evaluation", "Psych Visit - Psychotherapy", "Psychoanalysis", "Speech and Hearing Evaluation");
		$visit_proc_str=implode("','",$visit_proc);
		
		$visit_ids_arr=$visit_ids_str='';
		$sqlProc="SELECT id FROM `slot_procedures` WHERE LOWER(proc) IN ('".strtolower($visit_proc_str)."')";
		$visit_ids_arr= $this->getPtIdFun($sqlProc,'id');
		$visit_ids_str=implode(',',$visit_ids_arr);
		
		$preventive_proc=array("Annual Wellness Visit", "Preventive Care Services - Established Office Visit, 18 and Up", "Preventive Care Services - Group Counseling", "Preventive Care Services - Other", "Preventive Care Services-Individual Counseling", "Preventive Care Services-Initial Office Visit, 18 and Up");
		$preventive_proc_str=implode("','",$preventive_proc);
		
		$preventive_visit_ids_arr=$preventive_proc=$preventive_visit_ids_str='';
		$sqlProc="SELECT id FROM `slot_procedures` WHERE LOWER(proc) IN ('".strtolower($preventive_proc_str)."')";
		$preventive_visit_ids_arr= $this->getPtIdFun($sqlProc,'id');
		$preventive_visit_ids_str=implode(',',$preventive_visit_ids_arr);
		
		//getting all patients of age >= 18 years.	
		$commaPatients = $this->aged_get_denominator($this->provider,18, '0028');
		
		//getting final denominator having 2 or more office visits.
		if($commaPatients!='')
		{
		   $query="";
				$query="SELECT count(id) cnt,sa_patient_id from schedule_appointments 
				WHERE (sa_app_start_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')" ;
				if($visit_ids_str)$query.=" AND procedureid IN($visit_ids_str)";
				$query.=" AND sa_patient_id IN($commaPatients)
				GROUP BY sa_patient_id HAVING cnt >= 2
				UNION
				SELECT count(id) cnt,sa_patient_id from schedule_appointments 
				WHERE (sa_app_start_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') " ;
				if($preventive_visit_ids_str)$query.=" AND procedureid IN($preventive_visit_ids_str)";
				$query.=" AND sa_patient_id IN($commaPatients)
				GROUP BY sa_patient_id HAVING cnt >= 1";
			
			$data['ipop']= $this->getPtIdFun($query,'sa_patient_id');
		  
		}
		else{return;}
		######### END OF COUNTING IPOP HERE ###############
		
		/*List snomed codes for Tobacco User*/
		$sql0 = "SELECT DISTINCT(`code`) AS 'code' FROM `snomed_valueset` WHERE `value_set`='2.16.840.1.113883.3.526.3.1170'";
		$snomedCodesTobaccoUser = $this->getPtIdFun($sql0, 'code');
		if( count($snomedCodesTobaccoUser) > 0 ){
		    $snomedCodesTobaccoUser = implode("', '", $snomedCodesTobaccoUser);
		    $snomedCodesTobaccoUser = "'".$snomedCodesTobaccoUser."'";
		}
		else
		    $snomedCodesTobaccoUser = '';
		/*End snomed codes listing for Tobacco User*/
		
		if($row=='one' || $row=='')
		{
		    ######### START OF DENOMINATOR IPOP HERE ###############
		    $data['denominator'] = $data['ipop'];
		    ######### END OF DENOMINATOR IPOP HERE ###############

		    ######### Denominator Exclusions ###############
		    $data['exclusion'] = array();
		    ######### END OF Denominator Exclusions ###############

		    ######### NUMERATOR ###############
		    $commaPatients=implode(',',$data['denominator']);

		    //LOINC codes for Tobacco Use Screening
		    $tobaccoLionc = array('68535-4', '39240-7', '68536-2', '72166-2');
		    $tobaccoLionc = implode("', '", $tobaccoLionc);
		    $tobaccoLionc = "'".$tobaccoLionc."'";

		    $sql = "SELECT 
				    distinct(pt_id) 
			    FROM 
				    hc_observations obs 
				    LEFT JOIN hc_rel_observations robs ON(obs.id = robs.observation_id) 
			    where 
				    obs.snomed_code IN(".$tobaccoLionc.") 
				    AND (obs.observation_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') 
				    AND obs.pt_id IN (".$commaPatients.") 
				    AND (TRIM(robs.nullflavor)= '' OR robs.nullflavor IS NULL)";

		    $data['numerator']= $this->getPtIdFun($sql,'pt_id');

		    ######### NUMERATOR END HERE ###############


		    ############### denominator Exception starts here ################

		    /*Candidates for Denominator Exceptions*/
		    $ptIds = array_diff($data['denominator'], $data['numerator']);
		    $commaPatients = implode(',', $ptIds);

		    /*List snomed codes for Limited Life Expectancy*/
		    $sql0 = "SELECT `code` FROM `snomed_valueset` WHERE `value_set`='2.16.840.1.113883.3.526.3.1259'";
		    $snomedCodes = $this->getPtIdFun($sql0, 'code');
		    if( count($snomedCodes) > 0 ){
			$snomedCodes = implode("', '", $snomedCodes);
			$snomedCodes = "'".$snomedCodes."'";
		    }
		    else
			$snomedCodes = '';
		    /*End snomed codes listing form cataract surgery*/

		    /*Patient List based on Limited Life Expectancy*/
		    $sql = "SELECT 
				    DISTINCT(pt_id) AS 'pt_id' 
			    FROM 
				    pt_problem_list 
			    WHERE 
				    pt_id IN(".$commaPatients.") 
				    AND ( (LOWER(problem_name) LIKE '%limited%' AND LOWER(problem_name) LIKE '%life%' AND LOWER(problem_name) LIKE '%expectancy%') 
                    OR ccda_code IN(".$snomedCodes.") )
				    AND onset_date <= '".$this->dbdtupto."' 
				    AND (
					end_datetime = '0000-00-00 00:00:00' 
					OR end_datetime >= '".$this->dbdtupto."'
				    )";
		    $denomExceptions = $this->getPtIdFun($sql,'pt_id');
		    /*End Patient List based on Limited Life Expectancy*/

		    /*List of snomed codes for Medical Reason*/
		    $sql0 = "SELECT DISTINCT(`code`) AS 'code' FROM `snomed_valueset` WHERE `value_set` IN ('2.16.840.1.113883.3.117.1.7.1.473', '2.16.840.1.113883.3.526.3.1007', '2.16.840.1.114222.4.1.214079.1.1.7', '2.16.840.1.113883.3.600.1.1502')";
		    $snomedCodes = $this->getPtIdFun($sql0, 'code');
		    if( count($snomedCodes) > 0 ){
			$snomedCodes = implode("', '", $snomedCodes);
			$snomedCodes = "'".$snomedCodes."'";
		    }
		    else
			$snomedCodes = '';
		    /*End snomed codes listing for medical reason*/

		    /*Patient List based on "Tobacco Use Screening not done due to Medical Reason"*/
		    //getting date of 24 months before.
		    list($year,$month,$day) = explode('-',$this->dbdtupto); 
		    $chkDt = date('Y-m-d',mktime(0,0,0,$month-24,$day,$year));

		    $sql = "SELECT 
				    distinct(pt_id) 
			    FROM 
				    hc_observations 
			    where 
				    (LOWER(observation) LIKE '%tobacco%' AND LOWER(observation) LIKE '%use%' AND LOWER(observation) LIKE '%screening%') 
				    AND (observation_date BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
				    AND refusal = 1 
				    AND refusal_snomed IN(".$snomedCodes.") 
				    AND pt_id IN (".$commaPatients.")";

		    $denomExceptions = array_merge($denomExceptions, $this->getPtIdFun($sql,'pt_id'));
		    $denomExceptions = array_unique($denomExceptions);

		    $data['denominatorException']= $denomExceptions;

		    ############### End of Denominator Exception ################
		}
		elseif($row=='two')
		{
			//getting date of 24 months before.
			list($year,$month,$day) = explode('-',$this->dbdtupto); 
			$chkDt = date('Y-m-d',mktime(0,0,0,$month-24,$day,$year));
			
			######### START OF DENOMINATOR IPOP HERE ###############
			$commaPatients=implode(',',$data['ipop']);
			
			$sql="SELECT 
				DISTINCT(hc_observations.pt_id) AS 'pt_id' 
			    FROM	
				hc_observations
				JOIN hc_rel_observations rel ON rel.observation_id=hc_observations.id
			    WHERE 
				hc_observations.pt_id IN($commaPatients) AND 
                ( (LOWER(observation) LIKE '%tobacco%' AND LOWER(observation) LIKE '%use%' AND LOWER(observation) LIKE '%screening%') OR
				rel.`snomed_code` IN(".$snomedCodesTobaccoUser.") ) AND
				observation_date >= '".$chkDt."'";
			
			$data['denominator']= $this->getPtIdFun($sql,'pt_id');
			######### END OF DENOMINATOR IPOP HERE ###############

			######### Denominator Exclusions ###############
			$data['exclusion'] = array();
			######### END OF Denominator Exclusions ###############

			######### NUMERATOR ###############
			$commaPatients=implode(',',$data['ipop']);

			/*List snomed codes for Tobacco Use Cessation Counseling & Pharmacotherapy*/
			$sql0 = "SELECT DISTINCT(`code`) FROM `snomed_valueset` WHERE `value_set` IN ('2.16.840.1.113883.3.526.3.509', '2.16.840.1.113883.3.526.3.1190')";
			$snomedCodes = $this->getPtIdFun($sql0, 'code');
			if( count($snomedCodes) > 0 )
			{
			    $snomedCodes = implode("', '", $snomedCodes);
			    $snomedCodes = "'".$snomedCodes."'";
			}
			else
			    $snomedCodes = '';
			/*End snomed codes listing for Tobacco Use Cessation Counseling & Pharmacotherapy*/
			
			$sql = "SELECT 
				    DISTINCT(li.pid) AS 'pt_id' 
				FROM 
				    lists li 
				    INNER JOIN hc_observations obs ON(li.pid = obs.pt_id AND li.begdate >= obs.observation_date) 
				    INNER JOIN hc_rel_observations rel ON (rel.observation_id = obs.id) 
				where 
                    ( (LOWER(observation) LIKE '%tobacco%' AND LOWER(observation) LIKE '%use%' AND LOWER(observation) LIKE '%screening%') OR
                    rel.`snomed_code` IN(".$snomedCodesTobaccoUser.") )
				    AND li.ccda_code IN(".$snomedCodes.") 
				    AND LOWER(li.title) LIKE '%cessation%' 
				    AND li.pid IN (".$commaPatients.") 
				    AND li.begdate <= '".$this->dbdtupto."'";
			$data['numerator']= $this->getPtIdFun($sql,'pt_id');
			
			######### NUMERATOR END HERE ###############

			############### denominator Exception starts here ################
			
			/*Candidates for Denominator Exceptions*/
			$ptIds = array_diff($data['denominator'], $data['numerator']);
			$commaPatients = implode(',', $ptIds);

			/*List snomed codes for Limited Life Expectancy*/
			$sql0 = "SELECT `code` FROM `snomed_valueset` WHERE `value_set`='2.16.840.1.113883.3.526.3.1259'";
			$snomedCodes = $this->getPtIdFun($sql0, 'code');
			if( count($snomedCodes) > 0 ){
			    $snomedCodes = implode("', '", $snomedCodes);
			    $snomedCodes = "'".$snomedCodes."'";
			}
			else
			    $snomedCodes = '';
			/*End snomed codes listing form cataract surgery*/

			/*Patient List based on Limited Life Expectancy*/
			$sql = "SELECT 
				    DISTINCT(pt_id) AS 'pt_id' 
				FROM 
				    pt_problem_list 
				WHERE 
				    pt_id IN(".$commaPatients.") 
				    AND ccda_code IN(".$snomedCodes.") 
				    AND onset_date <= '".$this->dbdtupto."' 
				    AND (
					end_datetime = '0000-00-00 00:00:00' 
					OR end_datetime >= '".$this->dbdtupto."'
				    )";
			$denomExceptions = $this->getPtIdFun($sql,'pt_id');
			/*End Patient List based on Limited Life Expectancy*/
			
			/*List of snomed codes for Medical Reason*/
			$sql0 = "SELECT DISTINCT(`code`) AS 'code' FROM `snomed_valueset` WHERE `value_set` IN ('2.16.840.1.113883.3.117.1.7.1.473', '2.16.840.1.113883.3.526.3.1007', '2.16.840.1.114222.4.1.214079.1.1.7', '2.16.840.1.113883.3.600.1.1502')";
			$snomedCodes = $this->getPtIdFun($sql0, 'code');
			if( count($snomedCodes) > 0 ){
			    $snomedCodes = implode("', '", $snomedCodes);
			    $snomedCodes = "'".$snomedCodes."'";
			}
			else
			    $snomedCodes = '';
			/*End snomed codes listing for medical reason*/
			
			/*Patient List based on Intervention not done due to Medican Reason for Tobacco Use Cessation Counseling*/
			$sql = "SELECT 
				    DISTINCT(li.pid) AS 'pt_id' 
				FROM 
				    lists li
				    INNER JOIN hc_observations obs ON(li.pid = obs.pt_id AND li.begdate >= obs.observation_date) 
				    INNER JOIN hc_rel_observations rel ON (rel.observation_id = obs.id) 
				WHERE 
				    rel.snomed_code IN(".$snomedCodesTobaccoUser.") 
				    AND li.type = 5 
				    AND (
					LOWER(li.title) LIKE '%tobacco%' 
					AND LOWER(li.title) LIKE '%use%' 
					AND LOWER(li.title) LIKE '%cessation%' 
					AND LOWER(li.title) LIKE '%counseling%'
				    ) 
				    AND li.refusal = 1
				    AND TRIM(li.refusal_snomed) != ''
				    AND li.pid IN(".$commaPatients.") 
				    AND li.begdate <= '".$this->dbdtupto."'";
			$denomExceptions = array_merge($denomExceptions, $this->getPtIdFun($sql,'pt_id'));
			/*End Patient List based on Intervention not done due to Medican Reason for Tobacco Use Cessation Counseling*/
			
			/*Patient List based on Medication order not done due to Medical Reason for Tobacco Use Cessation Pharmacotherapy*/
			$sql = "SELECT 
				    DISTINCT(li.pid) AS 'pt_id' 
				FROM 
				    lists li
				    INNER JOIN hc_observations obs ON(li.pid = obs.pt_id AND li.begdate >= obs.observation_date) 
				    INNER JOIN hc_rel_observations rel ON (rel.observation_id = obs.id) 
				WHERE 
				    rel.snomed_code IN(".$snomedCodesTobaccoUser.") 
				    AND li.type IN(1,4) 
				    AND (
					LOWER(li.title) LIKE '%tobacco%' 
					AND LOWER(li.title) LIKE '%use%' 
					AND LOWER(li.title) LIKE '%cessation%' 
					AND LOWER(li.title) LIKE '%pharmacotherapy%'
				    ) 
				    AND li.refusal = 1
				    AND TRIM(li.refusal_snomed) != ''
				    AND li.pid IN(".$commaPatients.") 
				    AND li.begdate <= '".$this->dbdtupto."'";
			$denomExceptions = array_merge($denomExceptions, $this->getPtIdFun($sql,'pt_id'));
			/*End Patient List based on Medication order not done due to Medical Reason for Tobacco Use Cessation Pharmacotherapy*/
			
			$denomExceptions = array_unique($denomExceptions);
			$data['denominatorException']= $denomExceptions;
			############### denominator Exception ends here ################
		}
		elseif($row=='three')
		{
			######### START OF DENOMINATOR IPOP HERE ###############
			$data['denominator'] = $data['ipop'];
			######### END OF DENOMINATOR IPOP HERE ###############

			######### Denominator Exclusions ###############
			$data['exclusion'] = array();
			######### END OF Denominator Exclusions ###############
			
			//getting date of 24 months before.
			list($year,$month,$day) = explode('-',$this->dbdtupto); 
			$chkDt = date('Y-m-d',mktime(0,0,0,$month-24,$day,$year));
			
			######### NUMERATOR ###############
			$commaPatients=implode(',',$data['denominator']);

			//LOINC codes for Tobacco Use Screening
			$tobaccoLionc = array('68535-4', '39240-7', '68536-2', '72166-2');
			$tobaccoLionc = implode("', '", $tobaccoLionc);
			$tobaccoLionc = "'".$tobaccoLionc."'";
			
			//Snomed codes for non tobacco users
			$nonSmokers = "'105539002', '105540000', '105541001', '160618006', '160620009', '160621008', '228501004', '228502006', '228503001', '228512004', '266919005', '266921000', '266922007', '266923002', '266924008', '266925009', '266928006', '281018007', '360890004', '360900008', '360918006', '360929005', '405746006', '53896009', '8392000', '8517006', '87739003'";
			
			/*Numerator Candidates based on Non Tobacco Users*/
			$sql = "SELECT 
				    distinct(pt_id) 
				FROM 
				    hc_observations obs 
				    LEFT JOIN hc_rel_observations robs ON(obs.id = robs.observation_id) 
				where 
				    obs.snomed_code IN(".$tobaccoLionc.")
				    AND obs.refusal_snomed IN(".$nonSmokers.")
				    AND (obs.observation_date BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
				    AND obs.pt_id IN (".$commaPatients.") 
				    AND (TRIM(robs.nullflavor)= '' OR robs.nullflavor IS NULL)";
			$numerators = $this->getPtIdFun($sql,'pt_id');
			/*End Numerator Candidates based on Non Tobacco Users*/
			
			/*Numerator Candidates based on Tobacco Use Cessation Intervention*/
			$sql0 = "SELECT DISTINCT(`code`) FROM `snomed_valueset` WHERE `value_set` IN ('2.16.840.1.113883.3.526.3.509', '2.16.840.1.113883.3.526.3.1190')";
			$snomedCodes = $this->getPtIdFun($sql0, 'code');
			if( count($snomedCodes) > 0 )
			{
			    $snomedCodes = implode("', '", $snomedCodes);
			    $snomedCodes = "'".$snomedCodes."'";
			}
			else
			    $snomedCodes = '';
			/*End snomed codes listing for Tobacco Use Cessation Counseling & Pharmacotherapy*/
			
			$sql = "SELECT 
				    DISTINCT(li.pid) AS 'pt_id' 
				FROM 
				    lists li 
				    INNER JOIN hc_observations obs ON(li.pid = obs.pt_id AND li.begdate >= obs.observation_date) 
				    INNER JOIN hc_rel_observations rel ON (rel.observation_id = obs.id) 
				where 
				    rel.snomed_code IN(".$snomedCodesTobaccoUser.") 
				    AND li.ccda_code IN(".$snomedCodes.") 
				    AND LOWER(li.title) LIKE '%cessation%' 
				    AND li.pid IN (".$commaPatients.") 
				    AND li.begdate <= '".$this->dbdtupto."'";
			$numerators = array_merge($numerators, $this->getPtIdFun($sql,'pt_id'));
			/*End Numerator Candidates based on Tobacco Use Cessation Intervention*/
			
			$numerators = array_unique($numerators);
			$data['numerator'] = $numerators;
			######### NUMERATOR END HERE ###############
			
			############### denominator Exception starts here ################

		    /*Candidates for Denominator Exceptions*/
		    $ptIds = array_diff($data['denominator'], $data['numerator']);
		    $commaPatients = implode(',', $ptIds);

		    /*List snomed codes for Limited Life Expectancy*/
		    $sql0 = "SELECT `code` FROM `snomed_valueset` WHERE `value_set`='2.16.840.1.113883.3.526.3.1259'";
		    $snomedCodes = $this->getPtIdFun($sql0, 'code');
		    if( count($snomedCodes) > 0 ){
			$snomedCodes = implode("', '", $snomedCodes);
			$snomedCodes = "'".$snomedCodes."'";
		    }
		    else
			$snomedCodes = '';
		    /*End snomed codes listing form cataract surgery*/

		    /*Patient List based on Limited Life Expectancy*/
		    $sql = "SELECT 
				    DISTINCT(pt_id) AS 'pt_id' 
			    FROM 
				    pt_problem_list 
			    WHERE 
				    pt_id IN(".$commaPatients.") 
				    AND ccda_code IN(".$snomedCodes.") 
				    AND onset_date <= '".$this->dbdtupto."' 
				    AND (
					end_datetime = '0000-00-00 00:00:00' 
					OR end_datetime >= '".$this->dbdtupto."'
				    )";
		    $denomExceptions = $this->getPtIdFun($sql,'pt_id');
		    /*End Patient List based on Limited Life Expectancy*/

		    /*List of snomed codes for Medical Reason*/
		    $sql0 = "SELECT DISTINCT(`code`) AS 'code' FROM `snomed_valueset` WHERE `value_set` IN ('2.16.840.1.113883.3.117.1.7.1.473', '2.16.840.1.113883.3.526.3.1007', '2.16.840.1.114222.4.1.214079.1.1.7', '2.16.840.1.113883.3.600.1.1502')";
		    $snomedCodes = $this->getPtIdFun($sql0, 'code');
		    if( count($snomedCodes) > 0 ){
			$snomedCodes = implode("', '", $snomedCodes);
			$snomedCodes = "'".$snomedCodes."'";
		    }
		    else
			$snomedCodes = '';
		    /*End snomed codes listing for medical reason*/

		    /*Patient List based on "Tobacco Use Screening not done due to Medical Reason"*/
		    //getting date of 24 months before.
		    list($year,$month,$day) = explode('-',$this->dbdtupto); 
		    $chkDt = date('Y-m-d',mktime(0,0,0,$month-24,$day,$year));

		    $sql = "SELECT 
				    distinct(pt_id) 
			    FROM 
				    hc_observations 
			    where 
				    (LOWER(observation) LIKE '%tobacco%' AND LOWER(observation) LIKE '%use%' AND LOWER(observation) LIKE '%screening%') 
				    AND (observation_date BETWEEN '".$chkDt."' AND '".$this->dbdtupto."') 
				    AND refusal = 1 
				    AND refusal_snomed IN(".$snomedCodes.") 
				    AND pt_id IN (".$commaPatients.")";
		    $denomExceptions = array_merge($denomExceptions, $this->getPtIdFun($sql,'pt_id'));
		    
		    
		    /*List of snomed codes for Medical Reason*/
		    $sql0 = "SELECT DISTINCT(`code`) AS 'code' FROM `snomed_valueset` WHERE `value_set` IN ('2.16.840.1.113883.3.117.1.7.1.473', '2.16.840.1.113883.3.526.3.1007', '2.16.840.1.114222.4.1.214079.1.1.7', '2.16.840.1.113883.3.600.1.1502')";
		    $snomedCodes = $this->getPtIdFun($sql0, 'code');
		    if( count($snomedCodes) > 0 ){
			$snomedCodes = implode("', '", $snomedCodes);
			$snomedCodes = "'".$snomedCodes."'";
		    }
		    else
			$snomedCodes = '';
		    /*End snomed codes listing for medical reason*/

		    /*Patient List based on Intervention not done due to Medican Reason for Tobacco Use Cessation Counseling*/
		    $sql = "SELECT 
				DISTINCT(li.pid) AS 'pt_id' 
			    FROM 
				lists li
				INNER JOIN hc_observations obs ON(li.pid = obs.pt_id AND li.begdate >= obs.observation_date) 
				INNER JOIN hc_rel_observations rel ON (rel.observation_id = obs.id) 
			    WHERE 
				rel.snomed_code IN(".$snomedCodesTobaccoUser.") 
				AND li.type = 5 
				AND (
				    LOWER(li.title) LIKE '%tobacco%' 
				    AND LOWER(li.title) LIKE '%use%' 
				    AND LOWER(li.title) LIKE '%cessation%' 
				    AND LOWER(li.title) LIKE '%counseling%'
				) 
				AND li.refusal = 1
				AND TRIM(li.refusal_snomed) != ''
				AND li.pid IN(".$commaPatients.") 
				AND li.begdate <= '".$this->dbdtupto."'";
		    $denomExceptions = array_merge($denomExceptions, $this->getPtIdFun($sql,'pt_id'));
		    /*End Patient List based on Intervention not done due to Medican Reason for Tobacco Use Cessation Counseling*/

		    /*Patient List based on Medication order not done due to Medical Reason for Tobacco Use Cessation Pharmacotherapy*/
		    $sql = "SELECT 
				DISTINCT(li.pid) AS 'pt_id' 
			    FROM 
				lists li
				INNER JOIN hc_observations obs ON(li.pid = obs.pt_id AND li.begdate >= obs.observation_date) 
				INNER JOIN hc_rel_observations rel ON (rel.observation_id = obs.id) 
			    WHERE 
				rel.snomed_code IN(".$snomedCodesTobaccoUser.") 
				AND li.type IN(1,4) 
				AND (
				    LOWER(li.title) LIKE '%tobacco%' 
				    AND LOWER(li.title) LIKE '%use%' 
				    AND LOWER(li.title) LIKE '%cessation%' 
				    AND LOWER(li.title) LIKE '%pharmacotherapy%'
				) 
				AND li.refusal = 1
				AND TRIM(li.refusal_snomed) != ''
				AND li.pid IN(".$commaPatients.") 
				AND li.begdate <= '".$this->dbdtupto."'";
		    $denomExceptions = array_merge($denomExceptions, $this->getPtIdFun($sql,'pt_id'));
		    /*End Patient List based on Medication order not done due to Medical Reason for Tobacco Use Cessation Pharmacotherapy*/
		    
		    $denomExceptions = array_unique($denomExceptions);
		    $data['denominatorException']= $denomExceptions;
		    ############### End of Denominator Exception ################
		}
		return $data;
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
						   AND (date_of_service BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') AND providerId='".$this->provider."'";
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
                if($this->specific_exclusion($val,$secVal,'get')==1){
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
	
	$totalPtIDs = $this->aged_get_denominator($this->provider,18, '0086');
	
	if($totalPtIDs!=0){
            $tempDeno = $this->get_patients_by_encounter($totalPtIDs);//patients having 2 or more office visits.
        }
        if(count($tempDeno)>0){
            $totalPtIDs2 = implode(", ",$tempDeno);
        }else{$totalPtIDs=0;}

        if($commaPts != ''){$totalPtIDs = $totalPtIDs2 = $commaPts;}
	
	if($totalPtIDs!=0){
	    
	    /*List Snomed Codes for Primary Open-Angle Glaucoma*/
	    $sqlSnomed = "SELECT DISTINCT(code) AS 'code' FROM snomed_valueset WHERE value_set='2.16.840.1.113883.3.526.3.326'";
	    $snonedPOAG = $this->getPtIdFun($sqlSnomed,'code');
	    $snonedPOAGRegx = implode('|', $snonedPOAG);
	    $snonedPOAG = implode("', '", $snonedPOAG);
	    $snonedPOAG = "'".$snonedPOAG."'";
	    /*End listing of snomed codes for Primary Open-Angle Glaucoma*/
	    
	    /*List Encounter Type CPT Codes*/
	    $visit_proc =array("Ophthalmological Services", "Care Services in Long-Term Residential Facility", "Nursing Facility Visit", "Office Visit", "Outpatient Consultation", "Face-to-Face Interaction");

	    $vistiCodes = array('92002', '92004', '92012', '92014', '99324', '99325', '99326', '99327', '99328', '99334', '99335', '99336', '99337', '99304', '99305', '99306', '99307', '99308', '99309', '99310', '99201', '99202', '99203', '99204', '99205', '99212', '99213', '99214', '99215', '99241', '99242', '99243', '99244', '99245', '12843005', '18170008', '185349003', '185463005', '185465003', '19681004', '207195004', '270427003', '270430005', '308335008', '390906007', '406547006', '439708006', '87790002', '90526000');

	    $vistiCodes = implode("', '", $vistiCodes);
	    $vistiCodes = "'".$vistiCodes."'";
	    /*End List Encounter Type CPT Codes*/
	    
	    //to get patients having diagnosis of POAG (Primary Open Angle Glaucoma).
            $query = "SELECT 
		    DISTINCT(prob.pt_id) AS 'pt_id' 
		FROM 
		    pt_problem_list_log prob 
		    INNER JOIN schedule_appointments sa ON (prob.pt_id = sa.sa_patient_id) 
		    INNER JOIN superbill sb oN (sa.id = sb.sch_app_id AND sa.sa_patient_id = sb.patientId)
		    INNER JOIN procedureinfo pi ON (sb.idSuperBill = pi.idSuperBill)
		WHERE 
		    prob.pt_id IN(".$totalPtIDs2.") 
		    AND (
			prob.ccda_code IN(".$snonedPOAG.") OR prob.problem_name RLIKE '".$snonedPOAGRegx."'
                OR (
					LOWER(prob.problem_name) LIKE '%primary%' 
					AND LOWER(prob.problem_name) LIKE '%open%' 
					AND LOWER(prob.problem_name) LIKE '%angle%' 
					AND LOWER(prob.problem_name) LIKE '%glaucoma%'
				    )
		    )
		    AND pi.cptCode IN(".$vistiCodes.")
		    AND (sa.sa_app_start_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
	    
            $denoPTs1 = $this->getPtIdFun($query,'pt_id');
	    
	    $query4 = "SELECT 
		    DISTINCT(prob.pt_id) AS 'pt_id' 
		FROM 
		    pt_problem_list prob 
		    INNER JOIN schedule_appointments sa ON (prob.pt_id = sa.sa_patient_id) 
		    INNER JOIN superbill sb oN (sa.id = sb.sch_app_id AND sa.sa_patient_id = sb.patientId)
		    INNER JOIN procedureinfo pi ON (sb.idSuperBill = pi.idSuperBill)
		WHERE 
		    prob.pt_id IN(".$totalPtIDs2.") 
		    AND (
			prob.ccda_code IN(".$snonedPOAG.") OR prob.problem_name RLIKE '".$snonedPOAGRegx."'
                OR (
					LOWER(prob.problem_name) LIKE '%primary%' 
					AND LOWER(prob.problem_name) LIKE '%open%' 
					AND LOWER(prob.problem_name) LIKE '%angle%' 
					AND LOWER(prob.problem_name) LIKE '%glaucoma%'
				    )
		    )
		    AND pi.cptCode IN(".$vistiCodes.")
		    AND (sa.sa_app_start_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')
		    AND (prob.end_datetime >= sa.sa_app_start_date OR prob.end_datetime = '0000-00-00 00:00:00')";
	    
            $denoPTs = $this->getPtIdFun($query4,'pt_id');
	    
	    $denoNumExcl['ipop'] = array_unique(array_merge($denoPTs1,$denoPTs));
            $denoNumExcl['denominator'] = $denoNumExcl['ipop'];
            $pid = implode(', ',$denoNumExcl['denominator']);
	    /*End Denominator*/
	    
	    $denoNumExcl['exclusion']	= array();
	    
	    ###################### Calculate Numerators ######################
	    
	    /*Lionic Codes for Diagnosis Stydy Cup to Disc Ratio*/
	    $lionicCDR = array('71485-7', '71484-0');
	    $sql = "SELECT DISTINCT(code) AS 'code' FROM snomed_valueset WHERE value_set = '2.16.840.1.113883.3.526.3.1333'";
	    $lionicCDR = array_merge($lionicCDR, $this->getPtIdFun($sql,'code'));
	    $lionicCDR = array_unique($lionicCDR);
	    /*End Lionic Codes for Disgnosis Stude Cup to Disc Ratio*/
	    
	    /*Lionic Codes for Diagnosis Study Optic Disc Exam for Structural Abnormalities*/
	    $lionicODESA = array('71487-3', '71486-5');
	    $sql = "SELECT DISTINCT(code) AS 'code' FROM snomed_valueset WHERE value_set = '2.16.840.1.113883.3.526.3.1334'";
	    $lionicODESA = array_merge($lionicODESA, $this->getPtIdFun($sql,'code'));
	    $lionicODESA = array_unique($lionicODESA);
	    /*End Lionic Codes for Disgnosis Study Optic Disc Exam for Structural Abnormalities*/
	    
	    /*Numeric Criteria Lionic*/
	    $numeratorLionic = array_merge($lionicCDR, $lionicODESA);
	    $numeratorLionic = array_unique($numeratorLionic);
	    
	    $numeratorLionic = implode("', '", $numeratorLionic);
	    $numeratorLionic = "'".$numeratorLionic."'";
	    
	    $query2 = "SELECT 
				`cm`.`patient_id` AS `patient_id`, 
			    COUNT(`rad`.`rad_test_data_id`) AS 'cnt'
			FROM 
				`chart_master_table` `cm` 
				INNER JOIN `rad_test_data` `rad` ON(
					`cm`.`patient_id` = `rad`.`rad_patient_id` 
					AND `cm`.`date_of_service` = `rad`.`rad_order_date`
				) 
			WHERE `cm`.`patient_id` IN(".$pid.")
				AND `rad`.`rad_loinc` IN(".$numeratorLionic.") 
				AND TRIM(`rad`.`rad_results`) != '' 
				AND `rad`.`rad_results_date` != '0000-00-00'
			GROUP BY `cm`.`patient_id`
			HAVING `cnt` > 1";
	    
	    $denoNumExcl['numerator'] = $this->getPtIdFun($query2,'patient_id');
	    ###################### Numerators Calculation ######################
	    
	    /*Candidates for Denominator Exception*/
	    $pid = array_diff($denoNumExcl['denominator'], $denoNumExcl['numerator']);
	    $pid = implode(', ', $pid);
	    
	    ################## Denoninator Exception ##################
	    // Refusal Snomed codes for Medical Reason
	    $refusalSnoMed = "'183932001', '183964008', '183966005', '216952002', '266721009', '269191009', '274512008', '31438003', '35688006', '371133007', '397745006', '407563006', '410534003', '410536001', '416098002', '416406003', '428119001', '445528004', '59037007', '62014003', '79899007'";
	    
	    $query3 = "SELECT 
				`cm`.`patient_id` AS `patient_id`, 
			    COUNT(`rad`.`rad_test_data_id`) AS 'cnt'
			FROM 
				`chart_master_table` `cm` 
				INNER JOIN `rad_test_data` `rad` ON(
					`cm`.`patient_id` = `rad`.`rad_patient_id` 
					AND `cm`.`date_of_service` = `rad`.`rad_order_date`
				) 
			WHERE `cm`.`patient_id` IN(".$pid.")
				AND 
				(
				    (
					LOWER(`rad`.`rad_name`) LIKE '%cup%' 
					AND LOWER(`rad`.`rad_name`) LIKE '%to%' 
					AND LOWER(`rad`.`rad_name`) LIKE '%disc%' 
					AND LOWER(`rad`.`rad_name`) LIKE '%ratio%'
				    )
				    OR
				    (
					LOWER(`rad`.`rad_name`) LIKE '%optic%' 
					AND LOWER(`rad`.`rad_name`) LIKE '%disc%' 
					AND LOWER(`rad`.`rad_name`) LIKE '%exam%' 
					AND LOWER(`rad`.`rad_name`) LIKE '%for%'
					AND LOWER(`rad`.`rad_name`) LIKE '%structural%'
					AND LOWER(`rad`.`rad_name`) LIKE '%abnormalities%'
				    )
				    OR `rad`.`rad_loinc` IN(".$numeratorLionic.") 
				) 
				AND TRIM(`rad`.`rad_results`) = ''
				AND `rad`.`refusal` = 1
				AND TRIM(`rad`.`refusal_snomed`) IN(".$refusalSnoMed.")
				AND `rad`.`rad_results_date` != '0000-00-00'
			GROUP BY `cm`.`patient_id`
			HAVING `cnt` > 1";
	    $denoNumExcl['denominatorException'] = $this->getPtIdFun($query3,'patient_id');
	    ################## End Denoninator Exception ##################
        }
        
        return $denoNumExcl;
    }//end of getNQF0086.
    
    
    function getNQF0088($commaPts = ''){
        //CMS-Clinical Alternative Core #NQF0088 - Diabetic Retinopathy
        global $objDb; global $provider; global $dtfrom; global $dtupto; global $objDataManage;
        $dtfrom1 = $this->dbdtfrom;
        $dtupto1 = $this->dbdtupto;

        $totalPtIDs = $this->aged_get_denominator($provider,18, '0088');
	
	$denoNumExcl = array();	$ptIDs = array(); $ptIDs2 = array(); $ptIDs3 = array();
	
        $currDt = date('Y-m-d');
	list($year,$month,$day) = explode('-',$currDt);
	$chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-1));

        if($totalPtIDs!=0){
	    $tempDeno = $this->get_patients_by_encounter($totalPtIDs,1);    /*Patient having encounters*/
        }
	
        if(count($tempDeno)>0){
            $totalPtIDs = implode(", ",$tempDeno);
        }else{$totalPtIDs=0;}
	
	/*List of ICD10 & 9 Codes for Diabetic Retinopathy*/
	$sql = "SELECT DISTINCT(code) AS 'code' FROM `snomed_valueset` WHERE `value_set` = '2.16.840.1.113883.3.526.3.327' AND `code_system` IN('2.16.840.1.113883.6.90', '2.16.840.1.113883.6.103')";
	$icdDR = $this->getPtIdFun($sql,'code');
	$icdDRegx = implode('|', $icdDR);
	$icdDR = implode("', '", $icdDR);
	$icdDR = "'".$icdDR."'";
	/*End List of ICD10 & 9 Codes for Diabetic Retinopathy*/
    $icdDRegx='diabetic|retinopathy';
	
	/*List Encounter Type CPT Codes*/
	$visit_proc =array("Ophthalmological Services", "Care Services in Long-Term Residential Facility", "Nursing Facility Visit", "Office Visit", "Outpatient Consultation", "Face-to-Face Interaction");

	$vistiCodes = array('92002', '92004', '92012', '92014', '99324', '99325', '99326', '99327', '99328', '99334', '99335', '99336', '99337', '99304', '99305', '99306', '99307', '99308', '99309', '99310', '99201', '99202', '99203', '99204', '99205', '99212', '99213', '99214', '99215', '99241', '99242', '99243', '99244', '99245', '12843005', '18170008', '185349003', '185463005', '185465003', '19681004', '207195004', '270427003', '270430005', '308335008', '390906007', '406547006', '439708006', '87790002', '90526000');

	$vistiCodes = implode("', '", $vistiCodes);
	$vistiCodes = "'".$vistiCodes."'";
	/*End List Encounter Type CPT Codes*/
	
        if($commaPts != ''){$totalPtIDs = $commaPts;}
        if($totalPtIDs==0)return $denoNumExcl;

        //to get patients having diagnosis of DIABETIC RETINOPATHY
	
	$sql = "SELECT 
		    DISTINCT(prob.pt_id) AS 'pt_id' 
		FROM 
		    pt_problem_list_log prob 
		    INNER JOIN schedule_appointments sa ON (prob.pt_id = sa.sa_patient_id) 
		    INNER JOIN superbill sb oN (sa.id = sb.sch_app_id AND sa.sa_patient_id = sb.patientId)
		    INNER JOIN procedureinfo pi ON (sb.idSuperBill = pi.idSuperBill)
		WHERE 
		    prob.pt_id IN(".$totalPtIDs.") 
		    AND (
			prob.ccda_code IN(".$icdDR.") OR prob.problem_name RLIKE '".$icdDRegx."'
		    )
		    AND pi.cptCode IN(".$vistiCodes.")
		    AND (sa.sa_app_start_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
        $denoPTs = $this->getPtIdFun($query1,'pt_id');
	
	$sql = "SELECT 
		    DISTINCT(prob.pt_id) AS 'pt_id' 
		FROM 
		    pt_problem_list prob 
		    INNER JOIN schedule_appointments sa ON (prob.pt_id = sa.sa_patient_id) 
		    INNER JOIN superbill sb oN (sa.id = sb.sch_app_id AND sa.sa_patient_id = sb.patientId)
		    INNER JOIN procedureinfo pi ON (sb.idSuperBill = pi.idSuperBill)
		WHERE 
		    prob.pt_id IN(".$totalPtIDs.") 
		    AND (
			prob.ccda_code IN(".$icdDR.") OR prob.problem_name RLIKE '".$icdDRegx."'
		    )
		    AND pi.cptCode IN(".$vistiCodes.")
		    AND (sa.sa_app_start_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')
		    AND (prob.end_datetime >= sa.sa_app_start_date OR prob.end_datetime = '0000-00-00 00:00:00')";
	
	$denoPTs1 = $this->getPtIdFun($sql,'pt_id');
	
	$denoNumExcl['ipop'] = array_unique(array_merge($denoPTs1,$denoPTs));
	$denoNumExcl['denominator'] = $denoNumExcl['ipop'];
	/*End IPOP and Denominator*/
	
	$denoNumExcl['exclusion']   = array();
	
	$totalPtIDs1 = implode(", ",$denoNumExcl['denominator']);
	
	/*Calculate Numerators*/
	    # "Macular Exam" LOINC = 32451-7
	    # Level of Severity of Retinopathy Findings SNOMEDCT = '312903003', '312904009', '312905005', '399876000', '59276001'
	$query2 = "SELECT 
			DISTINCT(rad.rad_patient_id) AS 'patient_id'
		    FROM 
			rad_test_data rad
			INNER JOIN schedule_appointments sa ON (rad.rad_patient_id = sa.sa_patient_id) 
			INNER JOIN superbill sb oN (sa.id = sb.sch_app_id AND sa.sa_patient_id = sb.patientId)
			INNER JOIN procedureinfo pi ON (sb.idSuperBill = pi.idSuperBill)
		    WHERE 
			`rad`.`rad_patient_id` IN(".$totalPtIDs1.")
			AND TRIM(`rad`.`rad_loinc`) = '32451-7'
			AND TRIM(`rad`.`snowmedCode`) IN ('312903003', '312904009', '312905005', '399876000', '59276001')
			AND TRIM(`rad`.`rad_results`) != '' 
			AND `rad`.`rad_results_date` != '0000-00-00'
			AND pi.cptCode IN(".$vistiCodes.")
			AND (sa.sa_app_start_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')
			AND (rad.rad_order_date >= sa.sa_app_start_date )
			";
	$denoNumExcl['numerator'] = $this->getPtIdFun($query2,'patient_id');
	/*End Numerators Calculation*/
	
	$totalPtIDs1 = array_diff($denoNumExcl['denominator'], $denoNumExcl['numerator']);
	$totalPtIDs1 = implode(', ', $totalPtIDs1);
	
	/*Calculate Denoninator Exception*/
	    # Medical Reason - SNOMEDCT = '183932001', '183964008', '183966005', '216952002', '266721009', '269191009', '274512008', '31438003', '35688006', '371133007', '397745006', '407563006', '410534003', '410536001', '416098002', '416406003', '428119001', '445528004', '59037007', '62014003', '79899007'
	    # Patient Reason - SNOMEDCT = '105480006', '160932005', '160934006', '182890002', '182895007', '182897004', '182900006', '182902003', '183944003', '183945002', '184081006', '185479006', '185481008', '224187001', '225928004', '266710000', '266966009', '275694009', '275936005', '281399006', '310343007', '373787003', '385648002', '406149000', '408367005', '413310006', '413311005', '413312003', '416432009', '423656007', '424739004', '443390004', '713247000'
	
	$refusalSnomed = array('183932001', '183964008', '183966005', '216952002', '266721009', '269191009', '274512008', '31438003', '35688006', '371133007', '397745006', '407563006', '410534003', '410536001', '416098002', '416406003', '428119001', '445528004', '59037007', '62014003', '79899007', '105480006', '160932005', '160934006', '182890002', '182895007', '182897004', '182900006', '182902003', '183944003', '183945002', '184081006', '185479006', '185481008', '224187001', '225928004', '266710000', '266966009', '275694009', '275936005', '281399006', '310343007', '373787003', '385648002', '406149000', '408367005', '413310006', '413311005', '413312003', '416432009', '423656007', '424739004', '443390004', '713247000');
	$refusalSnomed = implode("', '", $refusalSnomed);
	$refusalSnomed = "'".$refusalSnomed."'";
	
	$query3 = "SELECT 
			DISTINCT(rad.rad_patient_id) AS 'patient_id'
		    FROM 
			rad_test_data rad
			INNER JOIN schedule_appointments sa ON (rad.rad_patient_id = sa.sa_patient_id) 
			INNER JOIN superbill sb oN (sa.id = sb.sch_app_id AND sa.sa_patient_id = sb.patientId)
			INNER JOIN procedureinfo pi ON (sb.idSuperBill = pi.idSuperBill)
		    WHERE 
			`rad`.`rad_patient_id` IN(".$totalPtIDs1.")
			AND (
			    LOWER(`rad`.`rad_name`) LIKE '%macular%' 
			    AND LOWER(`rad`.`rad_name`) LIKE '%exam%'
			)
			AND `rad`.`rad_results_date` != '0000-00-00'
			AND pi.cptCode IN(".$vistiCodes.")
			AND (sa.sa_app_start_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')
			AND (rad.rad_order_date >= sa.sa_app_start_date  AND rad.rad_order_date <= sa.sa_app_end_date)
			AND TRIM(`rad`.`refusal_snomed`) IN(".$refusalSnomed.")
			";
	
	$denoNumExcl['denominatorException'] = $this->getPtIdFun($query3,'patient_id');
	/*End Denoninator Exception Calculation*/
	
        return $denoNumExcl;
    }//end of getNQF0088.
    
    
    function getNQF0089($commaPts = ''){
        //CMS-Clinical Alternative Core #NQF0089 - Diabetic Retinopathy, PVC
        global $objDb; global $provider; global $dtfrom; global $dtupto; global $objDataManage;
        $dtfrom1 = $this->dbdtfrom;
        $dtupto1 = $this->dbdtupto;

        $totalPtIDs = $this->aged_get_denominator($provider,18, '0089');
	
        $denoNumExcl = array();	$ptIDs = array(); $ptIDs2 = array(); $ptIDs3 = array();
	$denoNumExcl['ipop'] = array();
	$denoNumExcl['denominator'] = array();
	$denoNumExcl['exclusion'] = array();
	$denoNumExcl['numerator'] = array();
	$denoNumExcl['denominatorException'] = array();
	
        list($year,$month,$day) = explode('-',$dtfrom1);
        $chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-1));

        $totalPtIDs = $this->get_patients_by_encounter($totalPtIDs,1);
	
	/*Calculate IPOP*/
	$diab_DX = '250.50|250.51|362.01|362.02|362.03|362.04|362.05|362.06|362.07|E08.311|E08.319|E08.321|E08.329|E08.331|E08.339|E08.349|E08.351|E08.359|E09.311|E09.319|309.321|E09.329|E09.331|E09.339|E09.349|E09.351|E09.359|E10.311|E10.319|E10.321|E10.329|E10.331|E10.339|E10.349|E10.351|E10.359|E11.311|E11.319|E11.321|E11.329|E11.331|E11.339|E11.349|E11.351|E11.359|E13.311|E13.319|E13.321|E13.329|E13.331|E13.339|E13.349|E13.351|E13.359';
        
	$totalPtIDs = implode(',', $totalPtIDs);
	
        //to get patients having diagnosis of DIABETIC RETINOPATHY
        $query1 = "SELECT DISTINCT(pt_id) AS 'pt_id', GROUP_CONCAT(DISTINCT(status)) as Grp_Status FROM pt_problem_list_log 
                    WHERE pt_id IN($totalPtIDs) 
                    AND problem_name RLIKE '".$diab_DX."' AND 
                    (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') 
                    GROUP BY problem_id HAVING (Grp_Status='Active')";
        $denoPTs = $this->getPtIdFun($query1,'pt_id');
	
	$query2 = "SELECT DISTINCT(pt_id) AS 'pt_id' FROM pt_problem_list 
		    WHERE pt_id IN($totalPtIDs) 
		    AND (problem_name RLIKE '".$diab_DX."' OR ccda_code RLIKE '".$diab_DX."')
            AND (onset_date BETWEEN '$dtfrom1' AND '$dtupto1') 
		    AND LOWER(status)='active'";
	
	$denoPTs1 = $this->getPtIdFun($query2,'pt_id');
	$denoNumExcl['ipop'] = array_unique(array_merge($denoPTs1,$denoPTs));
	/*End IPOP and Denominator*/
	
	$totalPtIDs = implode(',', $denoNumExcl['ipop']);
	
	/*Calculate Denominators*/
	$query3 = "SELECT 
			DISTINCT(`cm`.`patient_id`) AS `patient_id` 
		    FROM 
			`chart_master_table` `cm` 
			INNER JOIN `rad_test_data` `rad` ON(
				`cm`.`patient_id` = `rad`.`rad_patient_id` 
				AND `cm`.`date_of_service` = `rad`.`rad_order_date`
			) 
		    WHERE 
			`cm`.`patient_id` IN(".$totalPtIDs.")
			AND (
			    LOWER(`rad`.`rad_name`) LIKE '%macular%' 
			    AND LOWER(`rad`.`rad_name`) LIKE '%exam%'
			) 
			AND TRIM(`rad`.`rad_results`) != '' 
			AND `rad`.`rad_results_date` != '0000-00-00'";

	$denoNumExcl['denominator'] = $this->getPtIdFun($query3,'patient_id');
	/*End Denominator Calculation*/

        $totalPtIDs1 = implode(", ",$denoNumExcl['denominator']);
	
	/*Denominator Exclusions*/
	$denoNumExcl['exclusion'] = array();
	
	
        /*Numerator Calculation*/
	$diab_DX = '250.50|250.51|362.01|362.02|362.03|362.04|362.05|362.06|362.07|E08.311|E08.319|E08.321|E08.329|E08.331|E08.339|E08.349|E08.351|E08.359|E09.311|E09.319|309.321|E09.329|E09.331|E09.339|E09.349|E09.351|E09.359|E10.311|E10.319|E10.321|E10.329|E10.331|E10.339|E10.349|E10.351|E10.359|E11.311|E11.319|E11.321|E11.329|E11.331|E11.339|E11.349|E11.351|E11.359|E13.311|E13.319|E13.321|E13.329|E13.331|E13.339|E13.349|E13.351|E13.359';
        
        //to get patients having diagnosis of DIABETIC RETINOPATHY
        /*$query4 = "SELECT DISTINCT(pt_id) AS 'pt_id', GROUP_CONCAT(DISTINCT(status)) as Grp_Status FROM pt_problem_list_log 
                    WHERE pt_id IN($totalPtIDs1) 
                    AND problem_name RLIKE '".$diab_DX."' AND 
                    (onset_date >= '".$chkDt."') 
                    GROUP BY problem_id HAVING (Grp_Status='Active')";
        $denoPTs = $this->getPtIdFun($query4,'pt_id');
	
	$query5 = "SELECT DISTINCT(pt_id) AS 'pt_id' FROM pt_problem_list 
		    WHERE pt_id IN($totalPtIDs1) 
		    AND (problem_name RLIKE '".$diab_DX."' OR ccda_code RLIKE '".$diab_DX."')
		    AND onset_date >= '".$chkDt."'
		    AND LOWER(status)='active'";
	
	$denoPTs1 = $this->getPtIdFun($query5,'pt_id');
	$totalPtIDs2 = array_unique(array_merge($denoPTs1,$denoPTs));*/
	
	/*List Ey Care Procedure Ids*/
	$visit_proc =array("Ophthalmological Services", "Care Services in Long-Term Residential Facility", "Nursing Facility Visit", "Office Visit", "Outpatient Consultation", "Face-to-Face Interaction");
	$visit_proc_str=implode("','",$visit_proc);

	$visit_ids_arr = $visit_ids_str='';
	$sqlProc="SELECT id FROM `slot_procedures` WHERE LOWER(proc) IN ('".strtolower($visit_proc_str)."')";
	$visit_ids_arr = $this->getPtIdFun($sqlProc,'id');
	$visit_ids_str = implode(',',$visit_ids_arr);
	
	$query3 = "SELECT DISTINCT(pcl.patient_id) 
		FROM patient_consult_letter_tbl pcl
		INNER JOIN schedule_appointments sa ON(pcl.patient_id = sa.sa_patient_id AND pcl.date > sa.sa_app_start_date)
		WHERE
		    pcl.patient_id IN ($totalPtIDs1)
		    AND pcl.status = '0' 
		    AND 
		    (
			(
			    LOWER(pcl.templateData) LIKE '%macula%' 
			    AND LOWER(pcl.templateData) LIKE '%edema%'
			    AND
			    (
				LOWER(pcl.templateData) LIKE '%present%'
				OR LOWER(pcl.templateData) LIKE '%absent%'
			    )
			)
			OR
			(
			    LOWER(pcl.templateData) LIKE '%level%' 
			    AND LOWER(pcl.templateData) LIKE '%of%'
			    AND LOWER(pcl.templateData) LIKE '%severity%'
			    AND LOWER(pcl.templateData) LIKE '%of%'
			    AND LOWER(pcl.templateData) LIKE '%retinopathy%'
			    AND LOWER(pcl.templateData) LIKE '%findings%'
			)
		    )
		    AND sa.procedureid IN(".$visit_ids_str.")
		    AND (sa.sa_app_start_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')
		";
	$denoNumExcl['numerator'] 	= $this->getPtIdFun($query3,'patient_id');
	/*End Numerator Calculation*/
	
	$totalPtIDs1 = array_diff($denoNumExcl['denominator'], $denoNumExcl['numerator']);
	$totalPtIDs1 = implode(', ', $totalPtIDs1);
	
	/*Calculate Denominator Exception*/
	$query4 = "SELECT DISTINCT(patient_id) AS 'patient_id' "
		    . "FROM patient_consult_letter_tbl "
		    . "WHERE "
			. "patient_id IN ($totalPtIDs1) 
			   AND status = '0' 
			   AND LOWER(templateData) LIKE '%macula%' 
			   AND LOWER(templateData) LIKE '%edema%'
			   AND LOWER(templateData) LIKE '%findings%'
			   AND (
				LOWER(templateData) LIKE '%absent%'
				OR
				LOWER(templateData) LIKE '%present%'
			    )";
	$denoNumExcl['denominatorException'] 	= $this->getPtIdFun($query4,'patient_id');
	/*End Denominator Exception Calculation*/
	
        return $denoNumExcl;
    }
    
    function getNQF0055($commaPTs=''){
        //CMS-Clinical Alternative Core #NQF0055 - Diabetic Eye Exam
        global $objDb; global $provider; global $dtfrom; global $dtupto; global $objDataManage;
        $denoNumExcl = array();	$ptIDs = array(); $ptIDs2 = array(); $ptIDs3 = array();
        $dtfrom1 = $this->dbdtfrom;
        $dtupto1 = $this->dbdtupto;
	$totalPtIDs = $this->aged_get_denominator($this->provider,'18-75', '0055');
	
	$chkDt = $dtfrom1;
        list($year,$month,$day) = explode('-',$dtfrom1);
        $chkDt = date('Y-m-d',mktime(0,0,0,$month,$day,$year-2));
	
	################### IPOP ###################
	
        $diab_MelDX = "250.00|250.01|250.02|250.03|250.10|250.11|250.12|250.13|250.20|250.21|250.22|250.23|250.30|250.31|250.32|250.33|250.40|250.41|250.42|250.43|250.50|250.51|250.52|250.53|250.60|250.61|250.62|250.63|250.70|250.71|250.72|250.73|250.80|250.81|250.82|250.83|250.90|250.91|250.92|250.93|357.2|362.01|362.02|362.03|362.04|362.05|362.06|362.07|366.41|648.00|648.01|648.02|648.03|648.04|E10.10|E10.11|E10.21|E10.22|E10.29|E10.311|E10.319|E10.321|E10.3211|E10.3212|E10.3213|E10.3219|E10.329|E10.3291|E10.3292|E10.3293|E10.3299|E10.331|E10.3311|E10.3312|E10.3313|E10.3319|E10.339|E10.3391|E10.3392|E10.3393|E10.3399|E10.341|E10.3411|E10.3412|E10.3413|E10.3419|E10.349|E10.3491|E10.3492|E10.3493|E10.3499|E10.351|E10.3511|E10.3512|E10.3513|E10.3519|E10.3521|E10.3522|E10.3523|E10.3529|E10.3531|E10.3532|E10.3533|E10.3539|E10.3541|E10.3542|E10.3543|E10.3549|E10.3551|E10.3552|E10.3553|E10.3559|E10.359|E10.3591|E10.3592|E10.3593|E10.3599|E10.36|E10.37X1|E10.37X2|E10.37X3|E10.37X9|E10.39|E10.40|E10.41|E10.42|E10.43|E10.44|E10.49|E10.51|E10.52|E10.59|E10.610|E10.618|E10.620|E10.621|E10.622|E10.628|E10.630|E10.638|E10.641|E10.649|E10.65|E10.69|E10.8|E10.9|E11.00|E11.01|E11.10|E11.11|E11.21|E11.22|E11.29|E11.311|E11.319|E11.321|E11.3211|E11.3212|E11.3213|E11.3219|E11.329|E11.3291|E11.3292|E11.3293|E11.3299|E11.331|E11.3311|E11.3312|E11.3313|E11.3319|E11.339|E11.3391|E11.3392|E11.3393|E11.3399|E11.341|E11.3411|E11.3412|E11.3413|E11.3419|E11.349|E11.3491|E11.3492|E11.3493|E11.3499|E11.351|E11.3511|E11.3512|E11.3513|E11.3519|E11.3521|E11.3522|E11.3523|E11.3529|E11.3531|E11.3532|E11.3533|E11.3539|E11.3541|E11.3542|E11.3543|E11.3549|E11.3551|E11.3552|E11.3553|E11.3559|E11.359|E11.3591|E11.3592|E11.3593|E11.3599|E11.36|E11.37X1|E11.37X2|E11.37X3|E11.37X9|E11.39|E11.40|E11.41|E11.42|E11.43|E11.44|E11.49|E11.51|E11.52|E11.59|E11.610|E11.618|E11.620|E11.621|E11.622|E11.628|E11.630|E11.638|E11.641|E11.649|E11.65|E11.69|E11.8|E11.9|E13.00|E13.01|E13.10|E13.11|E13.21|E13.22|E13.29|E13.311|E13.319|E13.321|E13.3211|E13.3212|E13.3213|E13.3219|E13.329|E13.3291|E13.3292|E13.3293|E13.3299|E13.331|E13.3311|E13.3312|E13.3313|E13.3319|E13.339|E13.3391|E13.3392|E13.3393|E13.3399|E13.341|E13.3411|E13.3412|E13.3413|E13.3419|E13.349|E13.3491|E13.3492|E13.3493|E13.3499|E13.351|E13.3511|E13.3512|E13.3513|E13.3519|E13.3521|E13.3522|E13.3523|E13.3529|E13.3531|E13.3532|E13.3533|E13.3539|E13.3541|E13.3542|E13.3543|E13.3549|E13.3551|E13.3552|E13.3553|E13.3559|E13.359|E13.3591|E13.3592|E13.3593|E13.3599|E13.36|E13.37X1|E13.37X2|E13.37X3|E13.37X9|E13.39|E13.40|E13.41|E13.42|E13.43|E13.44|E13.49|E13.51|E13.52|E13.59|E13.610|E13.618|E13.620|E13.621|E13.622|E13.628|E13.630|E13.638|E13.641|E13.649|E13.65|E13.69|E13.8|E13.9|O24.011|O24.012|O24.013|O24.019|O24.02|O24.03|O24.111|O24.112|O24.113|O24.119|O24.12|O24.13|O24.311|O24.312|O24.313|O24.319|O24.32|O24.33|O24.811|O24.812|O24.813|O24.819|O24.82|O24.83";

        if($commaPTs != ''){$totalPtIDs = $commaPTs;}
        
	//getting patients having diagnosis of DIABETESE
        $query1 = "SELECT pt_id, GROUP_CONCAT(DISTINCT(status)) AS status FROM pt_problem_list_log WHERE pt_id IN ($totalPtIDs) 
                        AND ( problem_name RLIKE '".$diab_MelDX."'  
				OR
			      ccda_code RLIKE '".$diab_MelDX."' 
			    )
                        AND (onset_date BETWEEN '$chkDt' AND '$dtupto1') 
                        GROUP BY problem_id HAVING (status='Active')";
        $tmpARR = $this->getPtIdFun($query1,'pt_id');

        //getting patients having diagnosis of DIABETESE
        $query2 = "SELECT DISTINCT(pt_id) AS 'pt_id' FROM pt_problem_list WHERE pt_id IN ($totalPtIDs) 
                        AND ( problem_name RLIKE '".$diab_MelDX."'  
				OR
			      ccda_code RLIKE '".$diab_MelDX."' 
			    ) 
                        AND (onset_date <= '$dtupto1') AND status='Active'";
        $tmpARR2 = $this->getPtIdFun($query2,'pt_id');
	
        $tempTotal = implode(', ',array_unique(array_merge($tmpARR,$tmpARR2)));
	
	    /*Filter Candidates based on Encounters*/
	    $visit_proc = array("Office Visit", "Face-to-Face Interaction",  "Preventive Care Services - Established Office Visit, 18 and Up",  "Preventive Care Services-Initial Office Visit, 18 and Up",  "Home Healthcare Services",  "Annual Wellness Visit",  "Ophthalmological Services");
	    $visit_proc_str = implode("','",$visit_proc);
	    $visit_ids_arr=$visit_ids_str='';
	    $sqlProc="SELECT id FROM `slot_procedures` WHERE LOWER(proc) IN ('".strtolower($visit_proc_str)."')";
	    $visit_ids_arr= $this->getPtIdFun($sqlProc,'id');
	    $visit_ids_str=implode(',',$visit_ids_arr);

	    //$sql = "SELECT DISTINCT(`sa_patient_id`) AS 'pt_id' FROM `schedule_appointments` WHERE `sa_patient_id` IN (".$tempTotal.") AND (`sa_app_start_date` BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') AND (`sa_app_end_date` BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') AND `procedureid` IN(".$visit_ids_str.")";
	    $sql = "SELECT DISTINCT(`sa_patient_id`) AS 'pt_id' FROM `schedule_appointments` WHERE `sa_patient_id` IN (".$tempTotal.") AND (`sa_app_start_date` BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') AND (`sa_app_end_date` BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."') ";
	    $denoNumExcl['ipop'] = $this->getPtIdFun($sql,'pt_id');
	    
	    /*End Candites filter base on Encounter Type*/
	################### End IPOP ###################
	
	
        $denoNumExcl['denominator'] = $denoNumExcl['ipop'];

        $totalPtIDs1 = implode(", ",$denoNumExcl['denominator']);

        //chart_vision excluded from array below
        $arr_tables = array('chart_pupil', 'chart_eom', 'chart_external_exam', 'chart_lids', 'chart_lesion', 'chart_lid_pos', 'chart_lac_sys', 'chart_iop', 'chart_gonio', 'chart_dialation','chart_retinal_exam', 'chart_vitreous', 
					'chart_conjunctiva', 'chart_cornea', 'chart_ant_chamber', 'chart_iris', 'chart_lens');
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
	
	$ptIDs = array_diff($denoNumExcl['denominator'], $ptIDs2);
        $totalPtIDs2 = implode(", ",$ptIDs);
	
	#################### Calculate Denominator Exclusions #################### 
	
	/*Exclusion based on Encounter*/
	    $visit_proc = array("Encounter Inpatient");
	    $visit_proc_str = implode("','",$visit_proc);
	    $visit_ids_arr=$visit_ids_str='';
	    $sqlProc="SELECT id FROM `slot_procedures` WHERE LOWER(proc) IN ('".strtolower($visit_proc_str)."')";
	    $visit_ids_arr= $this->getPtIdFun($sqlProc,'id');
	    $visit_ids_str=implode(',',$visit_ids_arr);

	    /*Pt.Id from Appointmnets*/
	    //$sql = "SELECT sa.sa_patient_id AS 'pt_id' FROM schedule_appointments sa INNER JOIN inpatient_fields ipf ON(sa.id = ipf.appt_id) AND sa_patient_id IN(".$totalPtIDs2.") AND sa.procedureid IN(".$visit_ids_str.") AND ipf.field_code IN('428361000124107', '428371000124100') AND (sa.sa_app_end_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";
	    $sql = "SELECT sa.sa_patient_id AS 'pt_id' FROM schedule_appointments sa INNER JOIN inpatient_fields ipf ON(sa.id = ipf.appt_id) AND sa_patient_id IN(".$totalPtIDs2.") AND ipf.field_code IN('428361000124107', '428371000124100') AND (sa.sa_app_end_date BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";

	    $exclusions = $this->getPtIdFun($sql,'pt_id');
	    /*End Pt. Id from Appointments*/
	/*End Encounter Exclusion*/
	    
	/*Exclusion Based on Intervention "Hospice care ambulatory"*/
	    //$sql = "SELECT DISTINCT(pid) AS 'pt_id' FROM lists WHERE pid IN(".$totalPtIDs2.") AND type = 5 AND proc_type = 'intervention' AND ccda_code IN('385763009', '385765002') AND enddate <= '".$this->dbdtupto."' ";
	    $sql = "SELECT DISTINCT(pid) AS 'pt_id' FROM lists WHERE pid IN(".$totalPtIDs2.") AND type = 5 AND proc_type = 'intervention' AND ccda_code IN('385763009', '385765002') AND begdate <= '".$this->dbdtupto."' ";
	    $exclusions = array_merge($exclusions, $this->getPtIdFun($sql,'pt_id'));
	/*End Exclusion Based on Intervention "Hospice care ambulatory"*/
	$exclusions = array_unique($exclusions);
	$denoNumExcl['exclusion'] = $exclusions;
	#################### End Denominator Exclusions #################### 
        
	$totalPtIDs2 = array_diff($denoNumExcl['denominator'], $denoNumExcl['exclusion']);
	$totalPtIDs2 = implode(', ', $totalPtIDs2);
	
	/*Calculate Numerators*/
	$chkDt = date('Y-m-d',mktime(0,0,0,$month-12,$day,$year));
	
	$sql = "SELECT DISTINCT(hc_observations.pt_id) AS 'pt_id' FROM `hc_observations` INNER JOIN `hc_rel_observations` `rel` "
		. "ON(hc_observations.`id`=`rel`.`observation_id`) "
		. "WHERE hc_observations.`pt_id` IN(".$totalPtIDs2.") "
		. "AND LOWER(hc_observations.`observation`) LIKE '%retinal or dilated eye exam%' "
		. "AND ( "
		. "  ( (hc_observations.`observation_date` BETWEEN '".$chkDt."' AND '".$dtfrom1."') AND `rel`.`snomed_code` = '442225006' ) "
		. " OR (hc_observations.`observation_date` BETWEEN '".$dtfrom1."' AND '".$dtupto1."')"
		. ") ";
	
	
	 $tmpARR = $this->getPtIdFun($sql,'pt_id');
	 
	 $denoNumExcl['numerator'] = $tmpARR;
	
	/*End Numerator Calculation*/
	 
	$denoNumExcl['denominatorException'] = array();
	    
        return $denoNumExcl;
    }
    
    
    
   function getNQF0565($commaPTs=''){
         //CMS-Clinical Alternative Core #NQF0565 - Diabetic Eye Exam
        global $objDb; global $dtfrom; global $dtupto; global $objDataManage;
        $data = array();
 
		 $data['ipop'] = array();
		 $data['denominator'] = array();
		 $data['exclusion'] = array();
		 $data['numerator'] = array();
		 $data['denominatorException'] = array();
		 
		 $chkDtFrom = $dtfrom1 = $this->dbdtfrom;
		 $chkDtTo = $dtupto1 = $this->dbdtupto;

		 //modifiy end date by minus 92 days
		 list($year,$month,$day) = explode('-',$dtupto1);
		 $chkDtTo = date('Y-m-d',mktime(0,0,0,$month,($day-92),$year));
		 
		 //if to date is less than from date after minus 90 days then return false
		 if(strtotime($chkDtTo)<strtotime($chkDtFrom))return FALSE;
		 
		 /*Calculate IPOP*/
		 $totalPtIDs = $this->aged_get_denominator($this->provider,18, '0565');
		 		  
	   /*List snomed codes for Cataract Surgery*/
		$sql0 = "SELECT `code` FROM `snomed_valueset` WHERE `value_set`='2.16.840.1.113883.3.526.3.1411'";
		$snomedCodes = $this->getPtIdFun($sql0, 'code');
		$snomedCodes = implode(',', $snomedCodes);
		/*End snomed codes listing form cataract surgery*/

		$sql = "SELECT pid FROM lists WHERE type=5"
		 . " AND pid IN(".$totalPtIDs.")"
		 . " AND ( "
			 . "LOWER(title) LIKE '%cataract surgery%' " 
			 . "OR ccda_code IN('".$snomedCodes."') "
		 . " )"
		 . " AND (begdate BETWEEN '".$chkDtFrom."' AND '".$chkDtTo."')";
	   
		$data['denominator'] = $data['ipop'] = $this->getPtIdFun($sql,'pid');
	 
	 /*End IPOP Calculation*/
	 /* START EXCLUSION*/
	 $pt_list=implode(',',$data['ipop']);
	 if($pt_list)
	 {
		 /*List snomed codes for Cataract Surgery*/
		 $value_set='';
		 $value_set="'2.16.840.1.113883.3.526.3.1241', '2.16.840.1.113883.3.526.3.1448', '2.16.840.1.113883.3.526.3.1409', '2.16.840.1.113883.3.526.3.1410', '2.16.840.1.113883.3.526.3.1415', '2.16.840.1.113883.3.526.3.1449', '2.16.840.1.113883.3.526.3.1450', '2.16.840.1.113883.3.526.3.1451', '2.16.840.1.113883.3.526.3.1452', '2.16.840.1.113883.3.526.3.1416', '2.16.840.1.113883.3.526.3.1417', '2.16.840.1.113883.3.526.3.1418', '2.16.840.1.113883.3.526.3.1419', '2.16.840.1.113883.3.526.3.1453', '2.16.840.1.113883.3.526.3.1454', '2.16.840.1.113883.3.526.3.1455', '2.16.840.1.113883.3.526.3.327', '2.16.840.1.113883.3.526.3.1457', '2.16.840.1.113883.3.526.3.1458', '2.16.840.1.113883.3.526.3.1459', '2.16.840.1.113883.3.526.3.1460', '2.16.840.1.113883.3.526.3.1423', '2.16.840.1.113883.3.526.3.1461', '2.16.840.1.113883.3.526.3.1462', '2.16.840.1.113883.3.526.3.1424', '2.16.840.1.113883.3.526.3.1463', '2.16.840.1.113883.3.526.3.1427', '2.16.840.1.113883.3.526.3.1464', '2.16.840.1.113883.3.526.3.1465', '2.16.840.1.113883.3.526.3.1430', '2.16.840.1.113883.3.526.3.1466', '2.16.840.1.113883.3.526.3.1467', '2.16.840.1.113883.3.526.3.1468', '2.16.840.1.113883.3.526.3.1469', '2.16.840.1.113883.3.526.3.1470', '2.16.840.1.113883.3.526.3.1471', '2.16.840.1.113883.3.526.3.1472', '2.16.840.1.113883.3.526.3.1473', '2.16.840.1.113883.3.526.3.1480', '2.16.840.1.113883.3.526.3.1474', '2.16.840.1.113883.3.526.3.1432', '2.16.840.1.113883.3.526.3.1475', '2.16.840.1.113883.3.526.3.1476', '2.16.840.1.113883.3.526.3.1477', '2.16.840.1.113883.3.526.3.1478', '2.16.840.1.113883.3.526.3.1479', '2.16.840.1.113883.3.526.3.1481', '2.16.840.1.113883.3.526.3.1482', '2.16.840.1.113883.3.526.3.1444', '2.16.840.1.113883.3.526.3.1446'";
		 
		$sql0 = "SELECT `code` FROM `snomed_valueset` WHERE `value_set` IN ($value_set) ";
		$snomedCodes = $this->getPtIdFun($sql0, 'code');
		$snomedCodes = implode("','", $snomedCodes);
		 
		/*End snomed codes listing form cataract surgery*/
		 
		 $sql="SELECT DISTINCT(pt.pt_id) FROM `pt_problem_list` pt
			LEFT JOIN lists lst ON lst.pid=pt.pt_id AND lst.type = 5 
			where pt.pt_id in ($pt_list) 
			AND pt.status = 'Active' 
			AND CONCAT(pt.onset_date,' ',pt.OnsetTime) < CONCAT(lst.begdate,' ',lst.begtime) 
			AND pt.ccda_code IN('".$snomedCodes."') "; 
			
		 $data['exclusion'] = $this->getPtIdFun($sql,'pt_id'); 
	 /* END EXCLUSION*/
	
	$pt_list = array_diff($data['denominator'], $data['exclusion']);
	$pt_list = implode(', ', $pt_list);
	
	/* START Numerator*/	 

		$sql = "
			SELECT 
				DISTINCT(hc_observations.pt_id) AS 'pt_id' 
			FROM	
				hc_observations 
				LEFT JOIN lists lst ON lst.pid=hc_observations.pt_id AND lst.type = 5
				LEFT JOIN hc_rel_observations rel ON rel.observation_id=hc_observations.id
			WHERE 
				hc_observations.pt_id IN($pt_list) AND 
				LOWER(hc_observations.observation) LIKE '%best corrected visual acuity%' AND 
				hc_observations.`observation_date` BETWEEN lst.begdate AND DATE_ADD(lst.begdate, INTERVAL 90 DAY)
			";
		 $data['numerator'] = $this->getPtIdFun($sql,'pt_id');
		 
	/* END Numerator*/	 
	 }
 
     
        return $data;
    }
    
    function getNQF0564(){
         //CMS-Clinical Alternative Core #NQF0564 - Diabetic Eye Exam
        global $objDb; global $dtfrom; global $dtupto; global $objDataManage;
        $data = array();
 
	$data['ipop'] = array();
	$data['denominator'] = array();
	$data['exclusion'] = array();
	$data['numerator'] = array();
	$data['denominatorException'] = array();

	$chkDtFrom = $dtfrom1 = $this->dbdtfrom;
	$chkDtTo = $dtupto1 = $this->dbdtupto;

	//modifiy end date by minus 92 days
	list($year,$month,$day) = explode('-',$dtupto1);
	$chkDtTo = date('Y-m-d',mktime(0,0,0,$month,($day-90),$year));

	//if to date is less than from date after minus 90 days then return false
	if(strtotime($chkDtTo)<strtotime($chkDtFrom))return FALSE;

	/*Calculate IPOP*/
	$totalPtIDs = $this->aged_get_denominator($this->provider,18, '0564');
	$dt = explode(',', $totalPtIDs);
	
	/*List snomed codes for Cataract Surgery*/
	$sql0 = "SELECT `code` FROM `snomed_valueset` WHERE `value_set`='2.16.840.1.113883.3.526.3.1411'";
	$snomedCodes = $this->getPtIdFun($sql0, 'code');
	$snomedCodes = implode(',', $snomedCodes);
	/*End snomed codes listing form cataract surgery*/
	
	$sql = "SELECT pid FROM lists WHERE type=5"
	 . " AND pid IN(".$totalPtIDs.")"
	 . " AND ( "
		 . "(LOWER(title) LIKE '%cataract%' AND LOWER(title) LIKE '%surgery%') "
		 . "OR ccda_code IN(".$snomedCodes.") "
	 . " )"
	 . " AND (begdate BETWEEN '".$this->dbdtfrom."' AND '".$this->dbdtupto."')";

	$data['denominator']=$data['ipop'] = $this->getPtIdFun($sql,'pid');
	
//	array_push($data['denominator'], 77);
	/*End IPOP Calculation*/
	
	/* START EXCLUSION*/
	$pids = array_unique($data['denominator']);
	$totalPatients = implode(',', $pids);
	    
	    /*Cataract Snomeds*/
	    $sql = "SELECT code FROM  `snomed_valueset` WHERE  `value_set` = '2.16.840.1.113883.3.526.3.1411'";
	    $catSnomeds = $this->getPtIdFun($sql,'code');
	    if(count($catSnomeds)>0){
		$catSnomeds = implode("', '", $catSnomeds);
		$catSnomeds = "'".$catSnomeds."'";
	    }
	    else
		$catSnomeds = '';
	    /*End Cataract Snomeds*/
	
	
	    /*Procedure Exclusion*/
	    $sqlProc = "SELECT code FROM snomed_valueset WHERE value_set='2.16.840.1.113883.3.526.3.1434'";
	    $snomedProc = $this->getPtIdFun($sqlProc,'code');
	    $snomedProc = implode(',', $snomedProc);
	    
	    $sql = "SELECT 
			DISTINCT(prob.pid) AS 'pid'
		    FROM 
			    lists prob
			INNER JOIN(
			    select 
				DISTINCT(pid) AS 'pid', 
				begdate 
			    FROM 
				lists 
			    WHERE 
				type='5'
				AND (
				ccda_code IN(
				    ".$catSnomeds."
				)
                OR (LOWER(title) LIKE '%cataract%' AND LOWER(title) LIKE '%surgery%')
                )
			) cat ON(cat.pid=prob.pid)
		    WHERE 
			    prob.pid IN(".$totalPatients.") 
			    AND prob.type=5
			    AND (prob.ccda_code IN(".$snomedProc.")
                    OR (LOWER(title) LIKE '%vitrectomy%') )
			    AND (
				    (
					prob.begdate BETWEEN cat.begdate 
					AND cat.begdate
				    ) 
				    OR (
					prob.begdate BETWEEN cat.begdate 
					AND cat.begdate
				    )
			    )";
	    $procExc = $this->getPtIdFun($sql,'pid');
	    $pids = array_diff($pids, $procExc);
	    $totalPatients = implode(',', $pids);
	    /*End Procedure Exclusion*/
	    
	    /*Medicine Exclusion*/
	    $sqlMed = "SELECT code FROM snomed_valueset WHERE value_set='2.16.840.1.113883.3.526.3.1442'";
	    $rxnMed = $this->getPtIdFun($sqlMed,'code');
	    $rxnMed = implode(',', $rxnMed);
	    
	    $sql = "SELECT 
			DISTINCT(prob.pid) AS 'pid'
		    FROM 
			    lists prob
			INNER JOIN(
			    select 
				DISTINCT(pid) AS 'pid', 
				begdate 
			    FROM 
				lists 
			    WHERE 
				type='5'
				AND (
				ccda_code IN(
				    ".$catSnomeds."				)
                OR (LOWER(title) LIKE '%cataract%' AND LOWER(title) LIKE '%surgery%') )
			) cat ON(cat.pid=prob.pid)
		    WHERE 
			    prob.pid IN(".$totalPatients.") 
			    AND (prob.ccda_code IN(".$rxnMed.")
                    OR (LOWER(title) LIKE '%alpha%' OR LOWER(title) LIKE '%antagonists%') ) 
			    AND (
				    (
					prob.begdate BETWEEN cat.begdate 
					AND cat.begdate
				    ) 
				    AND (
					prob.begdate BETWEEN cat.begdate 
					AND cat.begdate
				    )
			    )";
	    $medExc = $this->getPtIdFun($sql,'pid');
	    $pids = array_diff($pids, $medExc);
	    $totalPatients = implode(',', $pids);
	    /*End Medicine Exclusion*/
	    
	
	    /*Diagnosis Exclusion*/
	    $diagValueSets = array('2.16.840.1.113883.3.526.3.1241', '2.16.840.1.113883.3.526.3.1405', '2.16.840.1.113883.3.526.3.1428', '2.16.840.1.113883.3.526.3.1409', '2.16.840.1.113883.3.526.3.1435', '2.16.840.1.113883.3.526.3.1441', '2.16.840.1.113883.3.526.3.1426', '2.16.840.1.113883.3.526.3.1410', '2.16.840.1.113883.3.526.3.1425', '2.16.840.1.113883.3.526.3.1427', '2.16.840.1.113883.3.526.3.1412', '2.16.840.1.113883.3.526.3.1420', '2.16.840.1.113883.3.526.3.1423', '2.16.840.1.113883.3.526.3.1413', '2.16.840.1.113883.3.526.3.1443', '2.16.840.1.113883.3.526.3.1414', '2.16.840.1.113883.3.526.3.1432', '2.16.840.1.113883.3.526.3.1430', '2.16.840.1.113883.3.526.3.1415', '2.16.840.1.113883.3.526.3.1445', '2.16.840.1.113883.3.526.3.1424', '2.16.840.1.113883.3.526.3.1444', '2.16.840.1.113883.3.526.3.1416', '2.16.840.1.113883.3.526.3.1419', '2.16.840.1.113883.3.526.3.1417', '2.16.840.1.113883.3.526.3.1433', '2.16.840.1.113883.3.526.3.1407', '2.16.840.1.113883.3.526.3.1418', '2.16.840.1.113883.3.526.3.1438', '2.16.840.1.113883.3.526.3.1406');
	    $diagValueSets = array_unique($diagValueSets);
	    $diagValueSets = implode("', '", $diagValueSets);
	    $diagValueSets = "'".$diagValueSets."'";
	    $sqlDiag = "SELECT code FROM snomed_valueset WHERE value_set IN(".$diagValueSets.")";
	    $snomedDiag = $this->getPtIdFun($sqlDiag,'code');
	    $snomedDiag = implode("', '", $snomedDiag);
	    $snomedDiag = "'".$snomedDiag."'";
	    
	    $sql = "SELECT 
			DISTINCT(prob.pt_id) AS 'pt_id'
		FROM 
			pt_problem_list prob
		    INNER JOIN(
			select 
			    DISTINCT(pid) AS 'pid', 
			    begdate 
			FROM 
			    lists 
			WHERE 
			    type='5'
			    AND (
			    ccda_code IN(
				".$catSnomeds."
			    )
                OR (LOWER(title) LIKE '%cataract%' AND LOWER(title) LIKE '%surgery%') ) 
		    ) cat ON(cat.pid=prob.pt_id)
		WHERE 
			prob.pt_id IN(".$totalPatients.") 
			AND prob.ccda_code IN(".$snomedDiag.")
			AND (
				(
				    prob.onset_date NOT BETWEEN cat.begdate 
				    AND cat.begdate
				) 
				AND (
				    prob.end_datetime NOT BETWEEN cat.begdate 
				    AND cat.begdate
				)
			)";
	    
	    $diagExec = $this->getPtIdFun($sql,'pt_id');
//	    $pids = array_diff($pids, $diagExec);
	    $pids = $diagExec;
	    $totalPatients = implode(',', $pids);
	    
	    $data['exclusion'] = $pids;
	    /*End Diagnosis Exclusion*/
	    
	    $pids = array_diff($data['denominator'], $data['exclusion']);
	    $totalPatients = implode(',', $pids);
	    
	    /*Numerator*/
	    $diagValueSets = array('2.16.840.1.113883.3.526.3.1436', '2.16.840.1.113883.3.526.3.1422', '2.16.840.1.113883.3.526.3.1408', '2.16.840.1.113883.3.526.3.1429', '2.16.840.1.113883.3.526.3.1447', '2.16.840.1.113883.3.526.3.1437', '2.16.840.1.113883.3.526.3.1440', '2.16.840.1.113883.3.526.3.1439');
	    $diagValueSets = array_unique($diagValueSets);
	    $diagValueSets = implode("', '", $diagValueSets);
	    $diagValueSets = "'".$diagValueSets."'";
	    $sqlDiag = "SELECT code FROM snomed_valueset WHERE value_set IN(".$diagValueSets.")";
	    $snomedDiag = $this->getPtIdFun($sqlDiag,'code');
	    $snomedDiag = implode("', '", $snomedDiag);
	    $snomedDiag = "'".$snomedDiag."'";
	    
	    $sql = "SELECT 
			DISTINCT(prob.pid) AS 'pt_id'
		FROM 
			lists prob
		    INNER JOIN(
			select 
			    DISTINCT(pid) AS 'pid', 
			    begdate 
			FROM 
			    lists 
			WHERE 
			    type='5'
			    AND 
			    ccda_code IN(
				".$catSnomeds."
			    )
		    ) cat ON(cat.pid=prob.pid)
		WHERE 
			type = 5
			AND prob.pid IN(".$totalPatients.") 
			AND prob.ccda_code IN(".$snomedDiag.")
			AND (
			    prob.begdate NOT BETWEEN cat.begdate AND DATE_ADD(cat.begdate, INTERVAL 30 DAY) 
			)";
	    $numer = $this->getPtIdFun($sql,'pt_id');
	    $data['numerator'] = $numer;
	    /*End Numerator*/
	    
	/* END EXCLUSION*/
 
     
        return $data;
    }
    
    
    
    
    
    
    
    public function specific_exclusion($patient_id,$section,$action,$exclusion_val=0){
	    if(empty($patient_id) || empty($section) || empty($action)  || !isset($this->auth_id) || empty($this->auth_id)){return;}
	    $exclusion = '';
	    $query = imw_query("SELECT exclusion FROM specific_exclusions WHERE patient_id='$patient_id' AND section_name='$section'");
	    if(imw_num_rows($query) > 0){
		    $rs = imw_fetch_array($query);
		    $exclusion = $rs['exclusion'];
	    }

	    if($action=='get'){
		    return $exclusion;
	    }
	    else if($action=='set'){
		    $authId = $this->auth_id;
		    $onDate = date("Y-m-d H:i:s");
		    $query2 = "specific_exclusions SET 
			    section_name='$section',
			    patient_id='$patient_id',
			    exclusion='$exclusion_val',
			    operator_id='$authId',
			    ondate='$onDate'";		
		    if($exclusion == '' && $exclusion_val == 1){
			    $query2 = "INSERT INTO ".$query2;
		    }else if($exclusion >= 0){
			    $query2 = "UPDATE ".$query2." WHERE patient_id='$patient_id' AND section_name='$section'";
		    }
		    $result2 = imw_query($query2);
		    if($result2){return true;}
	    }
    }
	
	function getNQF0419($m='one',$commaPatients=''){
		$denoNumExcl = array();	
		
		$totalPtIDs = $this->aged_get_denominator($this->provider,18, '0419');
		
		$toDate = $this->dbdtupto;
		$fromDate = $this->dbdtfrom;
		
		$data['ipop'] = array();
		$data['denominator'] = array();
		$data['exclusion'] = array();
		$data['numerator'] = array();
		$data['denominatorException'] = array();
		
		/*CPT Codes for Encounter Types*/
		$visit_proc =array("Medications Encounter Code Set");
		
		$vistiCodes = array('10197000', '108220007', '108221006', '108224003', '108311000', '13607009', '14736009', '165171009', '18091003', '18512000', '185349003', '185463005', '185465003', '209099002', '210098006', '225967005', '252592009', '252624005', '270427003', '270430005', '273643004', '274803000', '277404009', '284015009', '30346009', '308335008', '32537008', '34651001', '35025007', '36228007', '370803007', '37894004', '385973000', '386372009', '390906007', '405096004', '406547006', '408983003', '410155007', '410157004', '410158009', '410160006', '410170008', '439708006', '439952009', '440524004', '46662001', '48423005', '50357006', '53555003', '54290001', '59400', '59510', '59610', '59618', '63547008', '66902005', '78318003', '83607001', '8411005', '86013001', '90526000', '90791', '90792', '90832', '90834', '90837', '90839', '91573000', '92002', '92004', '92012', '92014', '92507', '92508', '92526', '92537', '92538', '92540', '92541', '92542', '92544', '92545', '92547', '92548', '92550', '92557', '92567', '92568', '92570', '92585', '92588', '92626', '96116', '96150', '96151', '96152', '97161', '97162', '97163', '97164', '97165', '97166', '97167', '97168', '97532', '97802', '97803', '97804', '98960', '98961', '98962', '99024', '99201', '99202', '99203', '99204', '99205', '99212', '99213', '99214', '99215', '99221', '99222', '99223', '99281', '99282', '99283', '99284', '99285', '99324', '99325', '99326', '99327', '99328', '99334', '99335', '99336', '99337', '99341', '99342', '99343', '99344', '99345', '99347', '99348', '99349', '99350', '99385', '99386', '99387', '99395', '99396', '99397', '99495', '99496', 'G0101', 'G0108', 'G0270', 'G0402', 'G0438', 'G0439');

		$vistiCodes = implode("', '", $vistiCodes);
		$vistiCodes = "'".$vistiCodes."'";
		/*End CPT Code for the encounter type*/
		
		$arrTemp = array();
		$getEncounter = "
			SELECT 
			    sa.sa_app_start_date,
			    sa.sa_patient_id
			FROM 
			    schedule_appointments sa
			    INNER JOIN superbill sb oN (sa.id = sb.sch_app_id AND sa.sa_patient_id = sb.patientId)
			    INNER JOIN procedureinfo pi ON (sb.idSuperBill = pi.idSuperBill)
			WHERE 
			    sa.sa_patient_id IN ($totalPtIDs) AND (sa.sa_app_start_date BETWEEN '$fromDate' AND '$toDate')
			    AND pi.cptCode IN(".$vistiCodes.")";
		
			$getAppt=imw_query($getEncounter);
			while($data=imw_fetch_object($getAppt))
			{
				$pt_arr[]=$data->sa_patient_id;
			}
			$pt_arr=array_unique($pt_arr);
		$data['denominator'] = $data['ipop'] = $pt_arr;
		
		/*List snomed codes for Procedure numerator*/
		$sql0 = "SELECT `code` FROM `snomed_valueset` WHERE `value_set`='2.16.840.1.113883.3.600.1.462'";
		$snomedCodes = $this->getPtIdFun($sql0, 'code');
		$snomedCodes = implode(',', $snomedCodes);
		$pt_list = implode(',',$pt_arr);
		
		$sql = "SELECT distinct(pid) FROM lists where type in (5,6) AND pid in ($pt_list) AND ccda_code in ('".$snomedCodes."')"; 
			
		$data['numerator'] = $this->getPtIdFun($sql,'pid');
		
		$pt_list = array_diff($data['denominator'], $data['numerator']);
		$pt_list = implode(', ', $pt_list);
		
		/* List of SNOMED Code for Procedure exclusion */
		$sql0 = "SELECT `code` FROM `snomed_valueset` WHERE `value_set`='2.16.840.1.113883.3.600.1.1502'";
		$snomedCodes = $this->getPtIdFun($sql0, 'code');
		$snomedCodes = implode(',', $snomedCodes);
		$pt_list = implode(',',$pt_arr);
		
			
		$sql = "SELECT distinct(pid) FROM lists where type in (5,6) AND pid in ($pt_list) AND LOWER(title) like '%current medications documented snmd%' AND ccda_code = '' "; 
		$data['denominatorException'] = $this->getPtIdFun($sql,'pid');
		
		
		return $data;
	}
    
    
} //END CLASS
?>