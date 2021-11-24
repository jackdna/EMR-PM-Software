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
include_once('../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php'); 
$logInOperatorId = $_SESSION['authId'];
//$logInOperatorName = $_SESSION['authUser'];

//$objManageData = new ManageData;
//----------------------- Get Data 
$multi_deduct_trans="";
if(strtolower($billing_global_server_name)=="henry"){
	$multi_deduct_trans="yes";
}

function getArrayRecords($table, $conditionId=0, $value=0, $orderBy=0){
	if($orderBy){
		if($conditionId){
			$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ORDER BY $orderBy DESC";
		}else{
			$qryStr = "SELECT * FROM $table ORDER BY $orderBy DESC";
		}
	}else{
		if($conditionId){
			$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value'";
		}else{
			$qryStr = "SELECT * FROM $table";
		}
	}
	$qryQry = imw_query($qryStr);
	if($qryQry){
		while($qryRow = imw_fetch_object($qryQry)){
			$qryRows[] = $qryRow;
		}		
		return $qryRows;
	}
}

function getRowRecord($table, $conditionId, $value, $orderBy=0){
	if($orderBy){
		$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ORDER BY $orderBy DESC";
	}else{
		$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value'";
	}		
	$qryQry = imw_query($qryStr);
	if($qryQry){
		$qryRow = imw_fetch_object($qryQry);
		return $qryRow;
	}
}


function get_query_array($qry){
	$return = array();
	$sql_qry = imw_query($qry);
	while($Row = imw_fetch_array($sql_qry)){
		$return[] = $Row;
	}
	return $return;
}

/* function getData($field, $table, $baseId, $value){
	$qryStr = "SELECT $field FROM $table WHERE $baseId = '$value'";
	$qryQry = imw_query($qryStr);
	if(imw_num_rows($qryQry)>0){
		$qryRow = imw_fetch_assoc($qryQry);
	}
	return $qryRow[$field];
} */
//----------------------- Get Data 

function getInsuranceProviderCode($id)
{
	$retVal = "";
	$sql = imw_query("SELECT 
			in_house_code,
			name 
			FROM insurance_companies 
			WHERE id='".$id."'");	
	$row = imw_fetch_array($sql);
	if($row != false)
	{
		$retVal = (!empty($row["in_house_code"])) ? $row["in_house_code"] : substr($row["name"],0,4)."..";
	}	
	return $retVal;
}

function getProcPaymentInfo($charge_list_detail_id)
{
	$sql = "SELECT 
			insurance_companies.name,
			insurance_companies.in_house_code, 
			patient_charges_detail_payment_info.paidBy,
			patient_charges_detail_payment_info.paidDate,
			patient_charges_detail_payment_info.paidForProc,			
			patient_charges_detail_payment_info.overPayment,
			patient_chargesheet_payment_info.insProviderId,
			patient_chargesheet_payment_info.paymentClaims
			FROM patient_charges_detail_payment_info 
			LEFT JOIN patient_chargesheet_payment_info 
			ON patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id     
			LEFT JOIN insurance_companies ON insurance_companies.id = patient_chargesheet_payment_info.insProviderId
			WHERE charge_list_detail_id = '".$charge_list_detail_id."' 
			AND patient_charges_detail_payment_info.deletePayment != '1'
			and (patient_charges_detail_payment_info.paidForProc>0 or patient_charges_detail_payment_info.overPayment>0)";
	$return = get_query_array($sql);		
	return $return;
}

function getProcCredits($encounter_id,$procId, $chargeListDetailId)
{
	$sql = "SELECT 
			DATE_FORMAT(creditapplied.dateApplied, '%m-%d-%Y') AS dateApplied,
			creditapplied.amountApplied,
			creditapplied.type,creditapplied.ins_case,
			creditapplied.credit_note
			FROM creditapplied 
			WHERE creditapplied.crAppliedToEncId='".$encounter_id."'
			AND creditapplied.cpt_code_id = '".$procId."'
			AND creditapplied.charge_list_detail_id = '".$chargeListDetailId."'
			and delete_credit='0' and credit_applied='1' and crAppliedTo='payment'
			";
	$return = get_query_array($sql);		
	return $return;					
}

function getProcCredits_adust($encounter_id,$procId, $chargeListDetailId)
{
	$sql = "SELECT 
			DATE_FORMAT(creditapplied.dateApplied, '%m-%d-%Y') AS dateApplied,
			creditapplied.amountApplied,creditapplied.patient_id,creditapplied.patient_id_adjust,
			creditapplied.type,creditapplied.ins_case,creditapplied.crAppliedTo  
			FROM creditapplied 
			WHERE creditapplied.crAppliedToEncId_adjust='".$encounter_id."'
			AND creditapplied.charge_list_detail_id_adjust  = '".$chargeListDetailId."'
			and delete_credit='0' and credit_applied='1' and crAppliedTo='adjustment'";
	$return = get_query_array($sql);		
	return $return;					
}

function getProcdebit_adust($encounter_id,$procId, $chargeListDetailId)
{
	$sql = "SELECT DATE_FORMAT(creditapplied.dateApplied, '%m-%d-%Y') AS dateApplied,
			creditapplied.amountApplied,creditapplied.patient_id,creditapplied.patient_id_adjust,
			creditapplied.type,creditapplied.ins_case,creditapplied.crAppliedTo,creditapplied.charge_list_detail_id 
			FROM creditapplied 
			WHERE ((creditapplied.crAppliedToEncId='".$encounter_id."' AND creditapplied.charge_list_detail_id = '".$chargeListDetailId."')
			or (creditapplied.crAppliedToEncId_adjust='".$encounter_id."' AND creditapplied.charge_list_detail_id_adjust  = '".$chargeListDetailId."'))
			and delete_credit='0' and credit_applied='1'";
	$return = get_query_array($sql);		
	return $return;				
}
function ymd2ts(&$mdy){
	if($mdy<>""){
		list($m,$d,$y)=explode('-',$mdy);
		$mdy=$y.$m.$d;
	}	
	//return mktime(0,0,0,$m,$d,$y);
	//return $dat_exp;
}
function ts2ymd(&$ts){
	if($ts<>""){
		$y=substr($ts,0,4);
		$m=substr($ts,4,2);
		$d=substr($ts,6,2);
		$ts= date('m-d-Y',mktime(0,0,0,$m,$d,$y));
	}
	//return date('m-d-Y',$ts);
	//return $dat_imp;
}
// TO get Account Balance Summary of Selected Patient
$patient_id = $_SESSION["patient"];
$aging_start=0;
$aging_to=180;
$query_acc_summary="select patient_charge_list.primaryInsuranceCoId ,patient_charge_list.patient_id,
			patient_charge_list.secondaryInsuranceCoId , patient_charge_list.charge_list_id,
			date_format(patient_charge_list.date_of_service,'%m-%d-%Y') as date_of_service,
			date_format(patient_charge_list.postedDate,'%m/%d/%Y') as postedDate,
			date_format(patient_data.DOB,'%m/%d/%Y') as patient_dob,
			patient_charge_list.encounter_id, patient_charge_list.totalBalance, 
			patient_data.lname,patient_data.fname,patient_data.mname,
			patient_data.DOB,
			patient_charge_list.tertiaryInsuranceCoId ,patient_charge_list.primary_paid,
			patient_charge_list.secondary_paid,patient_charge_list.tertiary_paid,
			patient_charge_list.patientDue,
			patient_charge_list.encounter_id,
			DATEDIFF(NOW(),patient_charge_list.date_of_service) as dos_date_diff,
			patient_charge_list_details.charge_list_detail_id,patient_charge_list_details.pri_due,patient_charge_list_details.sec_due,
			patient_charge_list_details.tri_due,patient_charge_list_details.pat_due  
			from patient_charge_list 
			LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
			LEFT JOIN patient_data on patient_data.id = patient_charge_list.patient_id 
			where patient_charge_list_details.del_status='0' and patient_charge_list.totalBalance > '0' and patient_data.id='$patient_id'";

$result_acc_summary = imw_query($query_acc_summary);
while($row_acc_summary = imw_fetch_assoc($result_acc_summary)){
	$qryRes[] = $row_acc_summary;
}

$polQry = "select elem_arCycle from copay_policies where policies_id = '1' limit 0,1";
$polQryRes = imw_query($polQry);
$polQryRow = imw_fetch_assoc($polQryRes);
$aggingCycle = $polQryRow["elem_arCycle"];
for($i=0;$i<count($qryRes);$i++){
		$priDue=$secDue=$triDue=0;
		$priInsId=$secInsId=$triInsId=0;
		$detailId = $qryRes[$i]['charge_list_detail_id'];
		$primaryInsuranceCoId = $qryRes[$i]['primaryInsuranceCoId'];
		$secondaryInsuranceCoId = $qryRes[$i]['secondaryInsuranceCoId'];
		$tertiaryInsuranceCoId = $qryRes[$i]['tertiaryInsuranceCoId'];
		$priDue= $qryRes[$i]['pri_due'];
		$secDue= $qryRes[$i]['sec_due'];
		$triDue= $qryRes[$i]['tri_due'];

		$insId = NULL;
		//--- IF PRIMARY INSURANCE NOT PAID ----
		if($primaryInsuranceCoId != '' && $priDue>0 && ($insuranceCompGroup=='' || $insuranceCompGroup=='primary')){
			if(sizeof($insuranceNameArr)>0){
				if(in_array($primaryInsuranceCoId,$insuranceNameArr)){
					$priInsId = $qryRes[$i]['primaryInsuranceCoId'];
				}
			}else{
				$priInsId = $qryRes[$i]['primaryInsuranceCoId'];
			}
		}
		//--- IF SECONDARY INSURANCE NOT PAID ----
		if($secondaryInsuranceCoId != '' && $secDue>0 && ($insuranceCompGroup=='' || $insuranceCompGroup=='secondary')){
			if(sizeof($insuranceNameArr)>0){
				if(in_array($secondaryInsuranceCoId,$insuranceNameArr)){
					$secInsId = $qryRes[$i]['secondaryInsuranceCoId'];
				}
			}else{
				$secInsId = $qryRes[$i]['secondaryInsuranceCoId'];
			}			
		}
		//--- IF TERTIARY INSURANCE NOT PAID ----
		if($tertiaryInsuranceCoId != '' && $triDue>0 && $insuranceCompGroup==''){
			if(sizeof($insuranceNameArr)>0){
				if(in_array($tertiaryInsuranceCoId,$insuranceNameArr)){
					$triInsId = $qryRes[$i]['tertiaryInsuranceCoId'];
				}
			}else{
				$triInsId = $qryRes[$i]['tertiaryInsuranceCoId'];
			}			
		}
		//--- SUNGLE INSURANCE COMPANY ---
		if(trim($insuranceName) != ''){
			if(trim($insuranceName) == $insId){
				$insId = $insuranceName;
			}
		}
		$dos_date_diff = $qryRes[$i]['dos_date_diff'];
		if($priInsId > 0 || $secInsId > 0 || $triInsId > 0){
			for($a=$aging_start;$a<=$aging_to;$a++){
				$start = $a;
				$a = $a > 0 ? $a - 1 : $a;
				$end = ($a) + $aggingCycle;
				//--- INSURANCE DUE AS A/R AGGING -------
				if($dos_date_diff >= $start and  $dos_date_diff <= $end){
					if($Process == "Summary"){
						if($priInsId>0){
							$insComIdArr[$priInsId] = $priInsId;
							$qryRes[$i]['insuranceDue']=$priDue; 
							$mainInsIdArr[$priInsId][$start][] = $qryRes[$i];
						}
						if($secInsId>0){
							$insComIdArr[$secInsId] = $secInsId;
							$qryRes[$i]['insuranceDue']=$secDue; 
							$mainInsIdArr[$secInsId][$start][] = $qryRes[$i];
						}
						if($triInsId>0){
							$insComIdArr[$triInsId] = $triInsId;
							$qryRes[$i]['insuranceDue']=$triDue; 
							$mainInsIdArr[$triInsId][$start][] = $qryRes[$i];
						}						
					}
					else{
						if($priInsId>0){
							$insComIdArr[$priInsId] = $priInsId;
							$qryRes[$i]['insuranceDue']=$priDue; 
							$mainInsIdArr[$priInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
						}
						if($secInsId>0){
							$insComIdArr[$secInsId] = $secInsId;
							$qryRes[$i]['insuranceDue']=$secDue; 
							$mainInsIdArr[$secInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
						}
						if($triInsId>0){
							$insComIdArr[$triInsId] = $triInsId;
							$qryRes[$i]['insuranceDue']=$triDue; 
							$mainInsIdArr[$triInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
						}
					}
				}
				$a += $aggingCycle;
			}
			if($All_due == true){
				//--- INSURANCE DUE AS A/R AGGING BY 181+ -------
				if($dos_date_diff >= 181){
					if($Process == "Summary"){
						if($priInsId>0){
							$insComIdArr[$priInsId] = $priInsId;
							$qryRes[$i]['insuranceDue']=$priDue; 
							$mainInsIdArr[$priInsId][181][] = $qryRes[$i];
						}
						if($secInsId>0){
							$insComIdArr[$secInsId] = $secInsId;
							$qryRes[$i]['insuranceDue']=$secDue; 
							$mainInsIdArr[$secInsId][181][] = $qryRes[$i];
						}
						if($triInsId>0){
							$insComIdArr[$triInsId] = $triInsId;
							$qryRes[$i]['insuranceDue']=$triDue; 
							$mainInsIdArr[$triInsId][181][] = $qryRes[$i];
						}						
					}
					else{
						if($priInsId>0){
							$insComIdArr[$priInsId] = $priInsId;
							$qryRes[$i]['insuranceDue']=$priDue; 
							$mainInsIdArr[$priInsId][$patient_id][$encounter_id][181][] = $qryRes[$i];
						}
						if($secInsId>0){
							$insComIdArr[$secInsId] = $secInsId;
							$qryRes[$i]['insuranceDue']=$secDue; 
							$mainInsIdArr[$secInsId][$patient_id][$encounter_id][181][] = $qryRes[$i];
						}
						if($triInsId>0){
							$insComIdArr[$triInsId] = $triInsId;
							$qryRes[$i]['insuranceDue']=$triDue; 
							$mainInsIdArr[$triInsId][$patient_id][$encounter_id][181][] = $qryRes[$i];
						}						
					}
				}
			}
		}
		//--- GET TOTAL PATIENT BALANCE -------
		for($a=$aging_start;$a<=$aging_to;$a++){
			$start = $a;
			$a = $a > 0 ? $a - 1 : $a;
			$end = ($a) + $aggingCycle;
			//--- PATIENT DUE AS A/R AGGING -------
			if($dos_date_diff >= $start and  $dos_date_diff <= $end){
				$grandPatientDueArr[$start][] = $qryRes[$i]['pat_due'];
				$grandTotalArr[$start][] = $qryRes[$i]['insuranceDue'];
			}
			$a += $aggingCycle;
		}
		if($All_due == true){
			//--- PATIENT DUE AS A/R AGGING BY 181+ -------
			if($dos_date_diff >= 181){
				$grandPatientDueArr[181][] = $qryRes[$i]['pat_due'];
				$grandTotalArr[181][] = $qryRes[$i]['insuranceDue'];
			}
		}
	}
// Print Data Of account Summary	
for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = ($a) + $aggingCycle;
		$headerTd .= <<<DATA
			<td class="text_b_w" width="90" style="text-align:right;">
				$start - $end
			</td>
DATA;
		
		$insDue = NULL;
		if(count($grandTotalArr[$start])>0){			
			$insDue = array_sum($grandTotalArr[$start]);
			// CHART	
			$chartFacility[$m][1] = $start."-".$end;
			$chartFacility[$m][2] = $insDue;			

		}
		$grand_total_arr[$start][] = $insDue;
		
		//--- GET PATIENT DUE AMOUNT WITH OUT INSURANCE CARRIER ---
		$pat_ins_due_amt = NULL;
		if(count($patBalArr[$a])>0){
			$pat_ins_due_amt = array_sum($patBalArr[$a]);
		}	
		$grand_total_arr[$start][] = $pat_ins_due_amt;	
		
		$pat_ins_due_amt = number_format($pat_ins_due_amt,2);
		$patDueWdOutIns .= <<<DATA
			<td class="text_10" width="$width" style="text-align:right;">
				$pat_ins_due_amt
			</td>
DATA;
		
		$totalBalance += $insDue;
		$patientInsDueAmountArr[] = $insDue;	
		$insDue = "$".number_format($insDue,2);
		$totalDueData .= <<<DATA
			<td class="text_10" width="$width" style="text-align:right;">
				$insDue
			</td>
DATA;
		//--- PATIENT DUE AMOUNT -----
		$patDue = NULL;		
		if(count($grandPatientDueArr[$start])>0){
			$patDue = array_sum($grandPatientDueArr[$start]);
			// CHART
			$chartFacility[$m][3]=$patDue;
		}
		$patientDueAmountArr[] = $patDue;
		$patDue = "$".number_format($patDue,2);
		
		$patientDueData .= <<<DATA
			<td class="text_10" width="$width" style="text-align:right;">
				$patDue
			</td>
DATA;
		
		$a += $aggingCycle;
		$m++;
	}	


// End of code of Account Balance Summary
if($_REQUEST['pat_id']<>""){
	$pat_id=$_REQUEST['pat_id'];
	$patient_id=$_REQUEST['pat_id'];
}else{
	$patient_id=$_SESSION['patient'];
}
$operatorName=$_SESSION['authUser'];

$arr_dx_code = array();
$qry_dx = "select dx_code,diag_description from diagnosis_code_tbl";
$res_dx = imw_query($qry_dx);
while($row_dx = imw_fetch_assoc($res_dx)){
	if(strlen($row_dx['diag_description']) > 30){
		$row_dx['diag_description'] = substr($row_dx['diag_description'],0,27)."...";
	}
	$arr_dx_code[$row_dx['dx_code']] = $row_dx['diag_description'];
}

$qry = "select * from patient_data where pid='$patient_id'";
$res = imw_query($qry);
$row = imw_fetch_array($res);
$fname = $row['fname'];
$lname = $row['lname'];
$mname = $row['mname'];
$patientName = $fname." ".$lname;
$patientFullName = $fname." ".$mname." ".$lname;
$patientName_ref = $lname.", ".$fname." ".$mname;
$street = $row['street'];
$street2 = $row["street2"];
$city = $row['city'];
$state = $row['state'];
$postal_code = $row['postal_code'];
$phone_home = $row['phone_home'];
if($row['phone_biz']!='000-000-0000' && $row['phone_biz']!=""){
		$other_phone = $row['phone_biz'];
}else{
	if($row['phone_cell']!='000-000-0000' && $row['phone_cell']!=""){
		$other_phone = $row['phone_cell'];
	}
}

$pAddress = $street;
if(trim($street2) != ""){
	$pAddress .= "<br>".$street2;
}
$pAddress .="<br>".$city.", ".$state.", ".$postal_code;
$phoneStatus = false;
$default_facility = $row['default_facility'];

	$qry = "select * from pos_facilityies_tbl where pos_facility_id = '$default_facility'";
	$qryId = imw_query($qry);
	$facilityTableDetails = imw_fetch_object($qryId);
	$facility_name = $facilityTableDetails->facility_name;	
	$fstreet = trim($facilityTableDetails->pos_facility_address.''.$facilityTableDetails->pos_facility_address2);
	$fcity = $facilityTableDetails->pos_facility_city;
	$fstate = $facilityTableDetails->pos_facility_state;
	$fpostal_code = $facilityTableDetails->pos_facility_zip;
	$posId = $facilityTableDetails->pos_id;
	$qryStr = "select a.phone, a.fax, a.default_group 
				from facility a,
				pos_tbl b
				where b.facility = a.id
				AND b.pos_id = '$posId'";
	$qryQry = imw_query($qryStr);
	$qryRow = imw_fetch_array($qryQry);
	$fphone = core_phone_format($qryRow['phone']);
	$ffax = core_phone_format($qryRow['fax']);
	$fgro_id = $qryRow['default_group'];
	
	$hq_fac_qry = imw_query("select logo from facility where facility_type='1'");
	$hq_fac_row = imw_fetch_array($hq_fac_qry);
	
$address = $fstreet."<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					 ".$fcity.", ".$fstate." ".$fpostal_code;


	

$today=getdate();
$today_year=$today['year'];
$today_mon=$today['mon'];
	if($today_mon<=9){
		$today_mon="0".$today_mon;
	}
$today_day=$today['mday'];
	if($today_day<=9){
		$today_day="0".$today_day;
	}

$date=$today_mon. "-" .$today_day. "-" .$today_year;
$encounter_id=$_REQUEST['eId'];
$ch_id=$_REQUEST['ch_id'];
if($ch_id){
	$ch_based_print="and charge_list_detail_id in($ch_id)";
}
	if($encounter_id=="" || $encounter_id=="undefined"){
		if($ch_id<>""){
			$sel_acc1=imw_query("select charge_list_id,primaryProviderId,secondaryProviderId from patient_charge_list_details where del_status='0' and charge_list_detail_id in($ch_id)");
			while($fet_acc1=imw_fetch_array($sel_acc1)){
			$charge_list_id_chk=$fet_acc1['charge_list_id'];
			$pri_Prov_show=$fet_acc1['primaryProviderId'];
			$sec_Prov_show=$fet_acc1['secondaryProviderId'];
			
			$sel_acc=imw_query("select encounter_id from patient_charge_list where del_status='0' and charge_list_id=$charge_list_id_chk");
			$fet_acc=imw_fetch_array($sel_acc);
				$encounter_id_chk[]=$fet_acc['encounter_id'];
			}
			if($encounter_id_chk){
				$encounter_id=implode(', ',array_unique($encounter_id_chk));
			}
		}else{
			$sel_acc1=imw_query("select charge_list_id from patient_charge_list_details where del_status='0' and patient_id = $patient_id and newBalance>0");
			while($fet_acc1=imw_fetch_array($sel_acc1)){
			$charge_list_id_chk=$fet_acc1['charge_list_id'];
			$charge_list_id_detail_chk_arr[]=$fet_acc1['charge_list_detail_id'];
			$sel_acc=imw_query("select encounter_id,primaryProviderId,secondaryProviderId from patient_charge_list where del_status='0' and charge_list_id=$charge_list_id_chk");
			$fet_acc=imw_fetch_array($sel_acc);
				$encounter_id_chk[]=$fet_acc['encounter_id'];
				$pri_Prov_show=$fet_acc['primaryProviderId'];
				$sec_Prov_show=$fet_acc['secondaryProviderId'];
			}
			if($encounter_id_chk){
				$encounter_id=implode(', ',array_unique($encounter_id_chk));
			}
			if($charge_list_id_detail_chk_arr){
				$ch_id=implode(',',array_unique($charge_list_id_detail_chk_arr));
			}
			if($ch_id){
				$ch_based_print="and charge_list_detail_id in($ch_id)";
			}
		}	
	}	
	if($encounter_id){
		$ch_based_print="";
	}
$get_group="SELECT gro_id,primaryProviderId,secondaryProviderId,facility_id FROM patient_charge_list WHERE del_status='0' and encounter_id in($encounter_id) order by gro_id desc";
$get_group_res=imw_query($get_group);
while($get_group_row=imw_fetch_array($get_group_res)){
	if($get_group_row['gro_id']>0){
		$gro_id_arr[$get_group_row['gro_id']]=$get_group_row['gro_id'];
		$pri_Prov_show=$get_group_row['primaryProviderId'];
		$sec_Prov_show=$get_group_row['secondaryProviderId'];
	}
	if(count($gro_id_arr)==0){
		$qryStr = "select default_group from facility where fac_prac_code = '".$get_group_row['facility_id']."'";
		$qryQry = imw_query($qryStr);
		$qryRow = imw_fetch_array($qryQry);
		$gro_id_arr[$qryRow['default_group']]=$qryRow['default_group'];	
	}
}
if(count($gro_id_arr)==0){
	$gro_id_arr[$fgro_id]=$fgro_id;
}
$gro_id=implode(',',$gro_id_arr);
$qryGroup = "select * from 
				groups_new 
				where gro_id in($gro_id)";
$qryGroupId = imw_query($qryGroup);
while($facilityGroupDetails = imw_fetch_assoc($qryGroupId)){
	$GroupName_arr[] = $facilityGroupDetails['name'];
	$address_arr[] = $facilityGroupDetails['group_Address1'];
	$group_Telephone_arr[] = $facilityGroupDetails['group_Telephone'];
	$group_Fax_arr[] = $facilityGroupDetails['group_Fax'];
	$group_Federal_EIN_arr[] = $facilityGroupDetails['group_Federal_EIN'];
	$group_State = $facilityGroupDetails['group_State'];
	$group_City = $facilityGroupDetails['group_City'];
	$group_Zip = $facilityGroupDetails['group_Zip'];
}
$GroupName=implode(',<br>',$GroupName_arr);
$group_Telephone=implode(', ',$group_Telephone_arr);
$group_Fax=implode(', ',$group_Fax_arr);
$group_address=implode(',<br>',$address_arr);
$group_Federal_EIN=implode(', ',$group_Federal_EIN_arr);
$group_address_csz=$group_City.', '.$group_State.' '.$group_Zip; 
// Primary Ins. Co.
		$getInsCaseStr="SELECT b.name FROM 
						patient_charge_list a,
						insurance_companies b
						WHERE a.del_status='0' and a.encounter_id in($encounter_id)
						AND a.primaryInsuranceCoId=b.id";
		$getInsCaseQry=@imw_query($getInsCaseStr);
		while($getInsCaseRow=@imw_fetch_array($getInsCaseQry)){
			$primaryInsuranceCo[]=ucwords(strtolower($getInsCaseRow['name']));
		}
		if($primaryInsuranceCo){
			$primaryInsuranceCo=array_unique($primaryInsuranceCo);
			$primaryInsuranceCo=implode(',<br>',$primaryInsuranceCo);
		}
// Primary Ins. Co.

// Secondary Ins. Co.
		$getInsCaseStr="SELECT b.name FROM 
						patient_charge_list a,
						insurance_companies b
						WHERE a.del_status='0' and a.encounter_id in($encounter_id)
						AND a.secondaryInsuranceCoId=b.id";
		$getInsCaseQry=@imw_query($getInsCaseStr);
		while($getInsCaseRow=@imw_fetch_array($getInsCaseQry)){
			$secondaryInsuranceCo[]=ucwords(strtolower($getInsCaseRow['name']));
		}
		if($secondaryInsuranceCo){
			$secondaryInsuranceCo=array_unique($secondaryInsuranceCo);
			$secondaryInsuranceCo=implode(',<br>',$secondaryInsuranceCo);
		}	
// Secondary Ins. Co.


// Tertiary Ins. Co.
		$getInsCaseStr="SELECT b.name FROM 
						patient_charge_list a,
						insurance_companies b
						WHERE a.del_status='0' and a.encounter_id in($encounter_id)
						AND a.tertiaryInsuranceCoId=b.id";
		$getInsCaseQry=@imw_query($getInsCaseStr);
		while($getInsCaseRow=@imw_fetch_array($getInsCaseQry)){
			$tertiaryInsuranceCo[]=ucwords(strtolower($getInsCaseRow['name']));
		}
		if($tertiaryInsuranceCo){
			$tertiaryInsuranceCo=array_unique($tertiaryInsuranceCo);
			$tertiaryInsuranceCo=implode(',<br>',$tertiaryInsuranceCo);
		}
// Tertiary Ins. Co.
	$sql = "select id,fname,lname from users";
	$row_arr = get_query_array($sql);	
	for($i=1;$i<count($row_arr);$i++){
		$row=$row_arr[$i];
		$cat_id = $row["id"];		
		$fname=$row["fname"];
		$lname=$row["lname"];
		$name=$lname.", ".$fname;
		$arrProviderCodes[$cat_id] = $name;
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Make Payment</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Accounting</title>
<style>
.printStyle{	
	font-family:Times New Roman; 
	font-size:12px;	
}	
</style>
</head>
<body  class="">
<div align="center" id="loading_img" width="100%" style="display:none; top:250px; left:530px; z-index:1000; position:absolute;">
	<img src="../../library/images/loading_image.gif">
</div>
<?php
ob_start();
?>
<page backtop="5mm" backbottom="5mm">
<page_footer>
    <table style="width:100%;">
   		<?php /*if(strtolower($billing_global_server_name)=="tyson"){?>
    	<tr>
            <td style="text-align:left;width:100%" class="text_10">*Please Note:  Effective 4-1-16, the Explanation of Benefits (EOB) from your Insurance Company may reflect a name change for Eye Associates to "Chirag S. Shah, MD, LLC - Visionary Partners Mgmt".  Dr. Tyson, Kaplan and all of our providers and staff will continue to provide you with quality care as we have in the past years.  Thank you.</td>
        </tr>
        <?php }*/ ?>
        <tr>
            <td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
        </tr>
    </table>
</page_footer>
<page_header>
<table style="width:700px;">
    <tr>
        <td class="text_b_w" style="width:200px;text-align:left;padding-left:5px"><?php echo $patientName.' - '.$patient_id; ?></td>
        <td class="text_b_w" style="width:350px;text-align:left;padding-left:5px">Receipt#:&nbsp;<?php echo $encounter_id; ?></td>
        <td class="text_b_w" style="width:150px;text-align:right;padding-right:8px">Dated: <?php echo date("m-d-Y"); ?></td>
    </tr>
	<tr>
		<td colspan="3" class="ptop_10" style="padding-top:10px">&nbsp;</td>
	</tr>
</table>
</page_header>
<?php 
$fac_logo="";
if(strtolower($billing_global_server_name)=="edison"){
	$fac_logo=$GLOBALS['php_server'].'/data/'.PRACTICE_PATH.'/gn_images/Edison_ophthalmology_logo.jpg';
}else{
	if(strtolower($billing_global_server_name)=="gewirtz"){
	}else{
		if($hq_fac_row['logo']!=""){
			$fac_logo='../../data/'.PRACTICE_PATH.'/facilitylogo/'.$hq_fac_row['logo'];
			//$fac_logo=$GLOBALS['php_server'].'/data/'.PRACTICE_PATH.'/facilitylogo/'.$hq_fac_row['logo'];
		}
	}
}
?>
<table style="width:700px;margin-top:10px;border:1px solid #cccccc" class="">
<tr>
	<td colspan="8">
    	<table cellpadding="0" cellspacing="0" style="width:700px">
            <tr>
				<td style=" width:700px; text-align:center;" class="text_10b">	
					<table style="width:700px; text-align:left;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width:300px; text-align:left;">
                                <?php if($fac_logo!=""){?><img src="<?php echo $fac_logo; ?>" style=" width:200px; height:55px;"><?php } ?>
                            </td>
                            <td style="vertical-align:top; padding-top:5px;width:400px">
                                 ITEMIZED CHARGES
                            </td>
                        </tr>
                  </table>
               </td>
            </tr>
            <tr><td style="text-align:right;width:700px;" class="text_10b">Dated : <?php echo date("m-d-Y");?></td></tr>
            <tr><td style="height:10px;width:700px"></td></tr>
            <tr height="20" class="text_10" valign="top">
				<td align="center" style="font-size:14px;">
					<table style="text-align:left;width:700px">
						 <?php 
							/*if(strtolower($billing_global_server_name)=="tyson"){ 
								$GroupName="Eye Associates";
								$group_address="251 So. Lincoln Avenue";
								$group_address_csz="Vineland, NJ 08361";
								$group_Telephone="856-691-8188";
								$group_Fax="856-691-9509";
								$group_Federal_EIN="223625449";
							}*/
						 ?>
						<tr><td class="text_10b"><?php echo $GroupName; ?></td></tr>
						<?php /*if(strtolower($billing_global_server_name)=="tyson"){?>
						 <tr><td class="text_10">Division of Chirag Shah, MD, LLC - Visionary Partners Mgmt</td></tr>
						<?php }*/?>
						<tr><td class="text_10"><?php echo $group_address; ?></td></tr>
						<?php
							if($group_address_csz != ""){
						?>
						<tr><td class="text_10"><?php echo $group_address_csz; ?></td></tr>
						<?	
							}
						?>
						<tr>
							<td class="text_10">Telephone #<?php echo core_phone_format($group_Telephone); ?></td>
						</tr>
						<tr>
							<td class="text_10">Fax #<?php echo core_phone_format($group_Fax); ?></td>
						</tr>
						 <tr>
							<td class="text_10">Tax ID #<?php echo $group_Federal_EIN; ?></td>
						 </tr>
						 <tr>
							<td style="height:20px;"></td>
						 </tr>
                    </table>
                </td>
			</tr>
			<tr>
				<td>
					<table style="width:700px;text-align:left;vertical-align:baseline">
						<tr>
							<td style="width:300px;text-align:left">
								<table width="100%" cellpadding="0" cellspacing="0" border="0">
									<tr><td class="text_10b"><?php echo $patientFullName; ?></td></tr>
									<tr><td class="text_10"><?php echo $pAddress; ?></td></tr>
								</table>
							</td>
							<td style="width:100px;text-align:left"></td>
							<td style="width:300px;text-align:left">
								<table width="100%" cellpadding="0" cellspacing="0" border="0">
                                	<tr>
										<td style="text-align:left;" class="text_10">Patient ID</td>
										<td style="text-align:left;" class="text_10"> : </td>
										<td style="text-align:left; width:200px;" class="text_10"><?php echo $patient_id; ?> </td>
									</tr>
                                    <tr>
										<td style="text-align:left;" class="text_10">Patient Phone #</td>
										<td style="text-align:left;" class="text_10"> : </td>
										<td style="text-align:left; width:200px;" class="text_10"><?php echo core_phone_format($phone_home); ?> </td>
									</tr>
                                    <tr>
                                        <td style="text-align:left;" class="text_10">Other Phone #</td>
                                        <td style="text-align:left;" class="text_10"> : </td>
                                        <td style="text-align:left; width:200px;" class="text_10"><?php echo core_phone_format($other_phone); ?> </td>
                                    </tr>
									<tr>
										<td style="text-align:left;" class="text_10">Receipt #</td>
										<td style="text-align:left;" class="text_10"> : </td>
										<td style="text-align:left; width:200px;" class="text_10"><?php echo $encounter_id; ?> </td>
									</tr>
									<tr>
										<td style="text-align:left;" class="text_10">Primary Ins </td>
										<td style="text-align:left;" class="text_10"> : </td>
										<td style="text-align:left;" class="text_10">
										<?php 
											if(strlen($primaryInsuranceCo)>40){
												echo substr($primaryInsuranceCo,0,36).'....';
											}else{
												echo $primaryInsuranceCo;
											}
										?>
										</td>
									</tr>
									<tr>
										<td style="text-align:left;" class="text_10">Secondary Ins </td>
										<td style="text-align:left;" class="text_10"> : </td>
										<td style="text-align:left;" class="text_10">
											<?php 
												if(strlen($secondaryInsuranceCo)>40){
													echo substr($secondaryInsuranceCo,0,36).'....';
												}else{
													echo $secondaryInsuranceCo;
												}
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>	
            </tr>	
        </table>
    </td>
</tr>
	<?php
    $getEncounterStr="SELECT * FROM patient_charge_list 
                            WHERE patient_id='$patient_id' 
                            AND del_status='0' and encounter_id in($encounter_id)
                            ORDER BY encounter_id DESC";
    $getEncounterQry=@imw_query($getEncounterStr);
    while($getEncounterRow=@imw_fetch_array($getEncounterQry)){			
    $encounter_id=$getEncounterRow['encounter_id'];					
    $charge_list_id=$getEncounterRow['charge_list_id'];
    $totalAmt=$getEncounterRow['totalAmt'];										
    $copay=$getEncounterRow['copay'];
    $copayPaid=$getEncounterRow['copayPaid'];
    $date_of_service=$getEncounterRow['date_of_service'];
        list($year, $month, $day)=explode("-", $date_of_service);
        $date_of_service=$month."-".$day."-".$year;
    $approvedTotalAmt=$getEncounterRow['approvedTotalAmt']; // Total Approved Amount
    $deductibleTotalAmt=$getEncounterRow['deductibleTotalAmt']; //Total Deductible Amount
    $totalAmt = $getEncounterRow['totalAmt']; // Total Cost of Procedure
    $copayDateOfPayment = get_date_format($getEncounterRow['coPayPaidDate']); //Copay Date Paid
    $copayNR = $getEncounterRow['coPayNotRequired']; // CoPay Not Required
    $coPayWriteOff = $getEncounterRow['coPayWriteOff'];
    $coPayWriteOffDate = $getEncounterRow['coPayWriteOffDate'];
	$all_dx_codes = $getEncounterRow['all_dx_codes'];
        list($yearWO, $monthWO, $dayWO)=explode("-", $coPayWriteOffDate);
        $coPayWriteOffDate=$monthWO."-".$dayWO."-".$yearWO;
    
    $coPayAdjustedDate = get_date_format($getEncounterRow["coPayAdjustedDate"]); // CoPay Date Applied
    $coPayAdjustedDate = ($coPayAdjustedDate == "00-00-0000") ? "-" : $coPayAdjustedDate;
    
    // New Balance
    $totalBalance=$getEncounterRow['totalBalance']; // New Balance
    //$totalBalance = $getEncounterRow['creditLessTotalBalance'] - $getEncounterRow['creditAmount'];
    ///
    
    $amtPaid=$getEncounterRow['amtPaid']; // Amount Paid
    // Get Credit
    $creditEncounter = $getEncounterRow['creditAmount'];									
    // Total Applied Amount
    //$totalAppliedAmt = getTotalApplied($creditEncounter,$deductibleTotalAmt,$amtPaid);
    $totalAppliedAmt = ($creditEncounter + $amtPaid);										
    $totalAppliedAmt = number_format($totalAppliedAmt,2);
    
    $ProcOverPaid = 0;
    $approvedAmt_final=0;
    $paidForProc_final=0;
    $newProcBalance_final=0;
    $overPaymentForProc_final=0;
	
	$dx_code_title_arr=array();
	$all_dx_codes_arr=unserialize(html_entity_decode($all_dx_codes));
	if($getEncounterRow['enc_icd10']>0){
		if(count($all_dx_codes_arr)>0){
			$dx_code_title_arr=get_icd10_desc($all_dx_codes_arr,0);
		}
	}
    ?>
    <tr height="10" bgcolor="Whitesmoke" class="text_10">
        <td width="100px" align="left" class="heading" style="border-top:2px solid #CCCCCC;"><b>Date</b></td>
        <td width="100px" style="padding-left:10px;border-top:2px solid #CCCCCC;" align="left" class="heading" ><b>Description</b></td>
        <td width="100px" align="center" class="heading" style="border-top:2px solid #CCCCCC;"><b>Unit</b></td>
        <td width="100px" align="center" class="heading" style="border-top:2px solid #CCCCCC;"><b>Physician</b></td>
        <td width="100px" align="right" class="heading" style="border-top:2px solid #CCCCCC;"><b>Charges</b></td>
        <td width="100px" align="right" class="heading" style="border-top:2px solid #CCCCCC;"><b>Payment</b></td>
        <td width="100px" align="right" class="heading" style="border-top:2px solid #CCCCCC;"><b>Adjustment</b></td>											
        <td width="100px" align="right" class="heading" style="border-top:2px solid #CCCCCC;"><b>Balance</b></td>
    </tr>
    <?php	
    $credit_note_arr=array();
    $getEncountersDetailStr="SELECT a.* FROM
                                patient_charge_list_details a,
                                cpt_fee_tbl b
                                WHERE a.del_status='0' and charge_list_id='$charge_list_id'
                                AND a.procCode=b.cpt_fee_id 
                                $ch_based_print
                                ORDER BY a.display_order asc,a.charge_list_detail_id ASC";
    $getEncountersDetailQry=@imw_query($getEncountersDetailStr);			
                        
    while($getEncountersDetailRows=@imw_fetch_array($getEncountersDetailQry)){
        
        $charge_list_detail_id = $getEncountersDetailRows['charge_list_detail_id'];
        $procId=$getEncountersDetailRows['procCode'];
        $procFee=$getEncountersDetailRows['procCharges'];
        $diagnosis_id1=$getEncountersDetailRows['diagnosis_id1'];
        $diagnosis_id2=$getEncountersDetailRows['diagnosis_id2'];
        $diagnosis_id3=$getEncountersDetailRows['diagnosis_id3'];
        $diagnosis_id4=$getEncountersDetailRows['diagnosis_id4'];
		$diagnosis_id5=$getEncountersDetailRows['diagnosis_id5'];
        $diagnosis_id6=$getEncountersDetailRows['diagnosis_id6'];
        $diagnosis_id7=$getEncountersDetailRows['diagnosis_id7'];
        $diagnosis_id8=$getEncountersDetailRows['diagnosis_id8'];
		$diagnosis_id9=$getEncountersDetailRows['diagnosis_id9'];
        $diagnosis_id10=$getEncountersDetailRows['diagnosis_id10'];
        $diagnosis_id11=$getEncountersDetailRows['diagnosis_id11'];
        $diagnosis_id12=$getEncountersDetailRows['diagnosis_id12'];
        $val_all_dx_code = "";
        $val_all_dx_code="<tr><td colspan='8' style='height:2px;'></td></tr>";
        if($diagnosis_id1=="" && $diagnosis_id2=="" && $diagnosis_id3=="" && $diagnosis_id4=="" && $diagnosis_id5=="" && $diagnosis_id6=="" && $diagnosis_id7=="" && $diagnosis_id8=="" && $diagnosis_id9=="" && $diagnosis_id10=="" && $diagnosis_id11=="" && $diagnosis_id12==""){
            $val_all_dx_code .="<tr><td></td><td class='text_10' style='padding-left:10px;'></td><td colspan='6'></td></tr>";
        }
		if($getEncounterRow['enc_icd10']>0){
			$diagnosis_desc_id1=$dx_code_title_arr[$diagnosis_id1];
				if(strlen($diagnosis_desc_id1) > 30){
					$diagnosis_desc_id1 = substr($diagnosis_desc_id1,0,27)."...";
				}
			$diagnosis_desc_id2=$dx_code_title_arr[$diagnosis_id2];
				if(strlen($diagnosis_desc_id2) > 30){
					$diagnosis_desc_id2 = substr($diagnosis_desc_id2,0,27)."...";
				}
			$diagnosis_desc_id3=$dx_code_title_arr[$diagnosis_id3];
				if(strlen($diagnosis_desc_id3) > 30){
					$diagnosis_desc_id3 = substr($diagnosis_desc_id3,0,27)."...";
				}
			$diagnosis_desc_id4=$dx_code_title_arr[$diagnosis_id4];
				if(strlen($diagnosis_desc_id4) > 30){
					$diagnosis_desc_id4 = substr($diagnosis_desc_id4,0,27)."...";
				}
			$diagnosis_desc_id5=$dx_code_title_arr[$diagnosis_id5];
				if(strlen($diagnosis_desc_id5) > 30){
					$diagnosis_desc_id5 = substr($diagnosis_desc_id5,0,27)."...";
				}
			$diagnosis_desc_id6=$dx_code_title_arr[$diagnosis_id6];
				if(strlen($diagnosis_desc_id6) > 30){
					$diagnosis_desc_id6 = substr($diagnosis_desc_id6,0,27)."...";
				}
			$diagnosis_desc_id7=$dx_code_title_arr[$diagnosis_id7];
				if(strlen($diagnosis_desc_id7) > 30){
					$diagnosis_desc_id7 = substr($diagnosis_desc_id7,0,27)."...";
				}
			$diagnosis_desc_id8=$dx_code_title_arr[$diagnosis_id8];
				if(strlen($diagnosis_desc_id8) > 30){
					$diagnosis_desc_id8 = substr($diagnosis_desc_id8,0,27)."...";
				}
			$diagnosis_desc_id9=$dx_code_title_arr[$diagnosis_id9];
				if(strlen($diagnosis_desc_id9) > 30){
					$diagnosis_desc_id9 = substr($diagnosis_desc_id9,0,27)."...";
				}
			$diagnosis_desc_id10=$dx_code_title_arr[$diagnosis_id10];
				if(strlen($diagnosis_desc_id10) > 30){
					$diagnosis_desc_id10 = substr($diagnosis_desc_id10,0,27)."...";
				}
			$diagnosis_desc_id11=$dx_code_title_arr[$diagnosis_id11];
				if(strlen($diagnosis_desc_id11) > 30){
					$diagnosis_desc_id11 = substr($diagnosis_desc_id11,0,27)."...";
				}
			$diagnosis_desc_id12=$dx_code_title_arr[$diagnosis_id12];
				if(strlen($diagnosis_desc_id12) > 30){
					$diagnosis_desc_id12 = substr($diagnosis_desc_id12,0,27)."...";
				}
			}else{
			$diagnosis_desc_id1=$arr_dx_code[$diagnosis_id1];
			$diagnosis_desc_id2=$arr_dx_code[$diagnosis_id2];
			$diagnosis_desc_id3=$arr_dx_code[$diagnosis_id3];
			$diagnosis_desc_id4=$arr_dx_code[$diagnosis_id4];
			$diagnosis_desc_id5=$arr_dx_code[$diagnosis_id5];
			$diagnosis_desc_id6=$arr_dx_code[$diagnosis_id6];
			$diagnosis_desc_id7=$arr_dx_code[$diagnosis_id7];
			$diagnosis_desc_id8=$arr_dx_code[$diagnosis_id8];
			$diagnosis_desc_id9=$arr_dx_code[$diagnosis_id9];
			$diagnosis_desc_id10=$arr_dx_code[$diagnosis_id10];
			$diagnosis_desc_id11=$arr_dx_code[$diagnosis_id11];
			$diagnosis_desc_id12=$arr_dx_code[$diagnosis_id12];
		}
        if($diagnosis_id1)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id1."] ".$diagnosis_desc_id1."</td><td colspan='6'></td></tr>";
            $dx=$diagnosis_id1;
        if($diagnosis_id2)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id2."] ".$diagnosis_desc_id2."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id2;
        if($diagnosis_id3)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id3."] ".$diagnosis_desc_id3."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id3;
        if($diagnosis_id4)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id4."] ".$diagnosis_desc_id4."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id4;
		if($diagnosis_id5)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id5."] ".$diagnosis_desc_id5."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id5;
		if($diagnosis_id6)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id6."] ".$diagnosis_desc_id6."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id6;
		if($diagnosis_id7)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id7."] ".$diagnosis_desc_id7."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id7;
		if($diagnosis_id8)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id8."] ".$diagnosis_desc_id8."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id8;
		if($diagnosis_id9)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id9."] ".$diagnosis_desc_id9."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id9;
		if($diagnosis_id10)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id10."] ".$diagnosis_desc_id10."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id10;
		if($diagnosis_id11)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id11."] ".$diagnosis_desc_id11."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id11;
		if($diagnosis_id12)
            $val_all_dx_code .= "<tr><td></td><td class='text_10' style='padding-left:10px;'>[".$diagnosis_id12."] ".$diagnosis_desc_id12."</td><td colspan='6'></td></tr>";
            $dx=$dx.",".$diagnosis_id12;
			
        $place_of_service=$getEncountersDetailRows['place_of_service'];									
        
        $getPosStr="SELECT * FROM pos_tbl WHERE pos_id='$place_of_service'";
            $getPosQry=@imw_query($getPosStr);
            $getPosRow=@imw_fetch_array($getPosQry);
            $posPracCode=$getPosRow['pos_prac_code'];
            $primaryProviderId=$getEncountersDetailRows['primaryProviderId'];
            $getPhysicianStr="SELECT * FROM users WHERE id='$primaryProviderId'";
                $getPhysicianQry=@imw_query($getPhysicianStr);
                $getPhysicianRow=@imw_fetch_array($getPhysicianQry);
                $phyFname=$getPhysicianRow['fname'];
                $phyLname=$getPhysicianRow['lname'];
                if($phyFname){
                    $phyName=$phyFname." ".$phyLname;
                    if(strlen($phyName)>13){
                        $phyName=substr($phyName, 0, 13);
                        $phyName=$phyName."..";
                    }
                }else{
                    $phyFname="";
                    }
        $units=$getEncountersDetailRows['units'];											
        $getProcFeeStr="SELECT * FROM cpt_fee_tbl WHERE cpt_fee_id='$procId'";
            $getProcFeeQry = imw_query($getProcFeeStr);
            $getProcFeeRow = imw_fetch_array($getProcFeeQry);
            $procCode = $getProcFeeRow['cpt_prac_code'];
            $cpt_desc = $getProcFeeRow['cpt_desc'];
            $qry = "select cpt_fee from cpt_fee_table where cpt_fee_id = '$cptId'
                    and fee_table_column_id = '$ColoumId'";
            //$procFee = $getProcFeeRow['fee'];
            $cpt4_code = $getProcFeeRow['cpt4_code']; // Cpt4Code
            $totalFee = $units*$procFee;
            
        // Deduct												
            $deductAmt = $getEncountersDetailRows["deductAmt"];												
            $paidForProc = $getEncountersDetailRows["paidForProc"];																								
            //$balForProc = $getEncountersDetailRows["balForProc"]; //
            $overPaymentForProc = $getEncountersDetailRows["overPaymentForProc"];
            $overPaymentForProc_final=$overPaymentForProc+$overPaymentForProc_final;		
            $approvedAmt = $getEncountersDetailRows["approvedAmt"]; // Approved Amount
            $approvedAmt_final=$approvedAmt+$approvedAmt_final;
            $paidForProc_final=$paidForProc+$paidForProc_final;
            $newProcBalance = $getEncountersDetailRows["newBalance"];  // New Balance
            $newProcBalance_final=$newProcBalance+$newProcBalance_final;
            $coPayAdjustedOn = $getEncountersDetailRows["coPayAdjustedAmount"];	
            $write_off_proc = $getEncountersDetailRows["write_off"];
            $write_off_proc_date = $getEncountersDetailRows["write_off_date"];
                list($year_procWO, $month_procWO, $day_procWO) = explode("-", $write_off_proc_date);
                $write_off_proc_date = $month_procWO."-".$day_procWO."-".$year_procWO;
            $write_off_proc_by = $getEncountersDetailRows["write_off_by"];
                $write_off_proc_by = getData('name', 'insurance_companies', 'id', $write_off_proc_by);
                if(strlen($write_off_proc_by)>13){	$write_off_proc_by = substr($write_off_proc_by, 0, 10)."...";	}
                if($write_off_proc_by==""){
                    $write_off_proc_by="Patient";
                }
            //copay paid amount
            
            $copay_paid_amt=0;
            $getCoPayPaidInfoStr = "SELECT b.paidForProc FROM 
            patient_chargesheet_payment_info a,
            patient_charges_detail_payment_info b
            WHERE a.encounter_id = '$encounter_id'
            AND a.payment_id = b.payment_id
            AND b.charge_list_detail_id = 0
            and b.deletePayment= 0
            ORDER BY a.payment_id DESC";
            $getCoPayPaidInfoQry = imw_query($getCoPayPaidInfoStr);
            while($copay_row=imw_fetch_array($getCoPayPaidInfoQry)){
                $copay_paid_amt=$copay_paid_amt+$copay_row['paidForProc'];
            }
            //copay paid amount	
                
            // Proc Payment Details
            $arrProcePaymentDetails = array();
            $arrPaymentDates = array();													
            $arrPaymentDates["DOS"] = $date_of_service; // Date Of Service									
            $arrProcePaymentDetails[$cpt_desc] = $approvedAmt; 
            if(!empty($deductAmt) && ($deductAmt != "0.00"))
            {
				$dk=0;	
            	$deductibleDetails = getArrayRecords('payment_deductible', 'charge_list_detail_id', $charge_list_detail_id);
				if(is_array($deductibleDetails)){
					foreach($deductibleDetails as $deductibles){
						$deduct_ins_id=$deductby="";
						$nameInsCo="";
						$deductible_id = $deductibles->deductible_id;
						$chargeDetailId = $deductibles->charge_list_detail_id;							
						$deduct_ins_id = $deductibles->deduct_ins_id;
						$deductible_by = $deductibles->deductible_by;
						$deduct_date = $deductibles->deduct_date;
						$deduct_amount = $deductibles->deduct_amount;
						if($multi_deduct_trans!=""){
							$deductby = getData('in_house_code', 'insurance_companies', 'id', $deduct_ins_id);
							if($deductby==''){ $deductby = getData('name', 'insurance_companies', 'id', $deduct_ins_id); }
							if($deductby==''){$deductby="Patient";}
							$deductDate = get_date_format($deduct_date);																										
							$arrProcePaymentDetails["Deductible"][$dk] = array($deduct_amount,"Deductible : ".$deductby);
							$arrPaymentDates["Deductible"][$dk] =  $deductDate;
							$dk++;
						}
					}
				}
				if($multi_deduct_trans==""){
					$deductDate = get_date_format($deduct_date);																										
					$arrProcePaymentDetails["Deductible"] = $deductAmt; 
					$arrPaymentDates["Deductible"] =  $deductDate;
				}
            }
            
            unset($write_off_byArr);
            unset($write_off_amountArr);
            unset($write_off_dateArr);
            $getWriteOffStr = "SELECT * FROM paymentswriteoff
                                WHERE patient_id = '$patient_id'
                                AND encounter_id = '$encounter_id'
                                AND charge_list_detail_id = '$charge_list_detail_id'
                                AND delStatus != '1'";
            $getWriteOffQry = imw_query($getWriteOffStr);
            $countWriteOffRowsCount = imw_num_rows($getWriteOffQry);
            
            if($countWriteOffRowsCount>0){
                while($getWriteOffRows = imw_fetch_array($getWriteOffQry)){
                    $write_off_id = $getWriteOffRows['write_off_id'];								
                    $write_off_by_id = $getWriteOffRows['write_off_by_id'];
                    $payment_writ_status = $getWriteOffRows['paymentStatus'];
                    
                        $write_off_by = getData('in_house_code', 'insurance_companies', 'id', $write_off_by_id);
                        if($write_off_by==''){ $write_off_by = getData('name', 'insurance_companies', 'id', $write_off_by_id); }
                        //if(strlen($write_off_by)>8){	$write_off_by = substr($write_off_by, 0, 8)."...";	}
                    if($write_off_by_id==0){
                        $write_off_by="Patient";
                    }
                    if($payment_writ_status=='Discount'){
                        $write_off_byArr[] = "Discount : ".$write_off_by;
                    }else{
                        $write_off_byArr[] = "Write off : ".$write_off_by;
                    }
                    $write_off_statArr[] = $payment_writ_status;
                    $write_off_amount = $getWriteOffRows['write_off_amount'];
					$write_off_era_amount = $getWriteOffRows['era_amt'];
                    $totalAppliedAmt = $totalAppliedAmt + $write_off_amount;
                    
                    $write_off_amountArr[] = $write_off_amount;
					
					if($write_off_proc_by=="Patient" && $write_off_era_amount==$write_off_proc && $write_off_era_amount>0){
						 $write_off_proc_by=$write_off_by;
					}
            
                    $write_off_operator_id = $getWriteOffRows['write_off_operator_id'];
                        $write_off_operator = getData('fname', 'users', 'id', $write_off_operator_id);
                        $write_off_operator.= getData('lname', 'users', 'id', $write_off_operator_id);
            
                    $write_off_date = $getWriteOffRows['write_off_date'];
                        list($yearWO, $monthWO, $dayWO)=explode("-", $write_off_date);
                        $write_off_date = $monthWO."-".$dayWO."-".$yearWO;
                    $write_off_dateArr[] = $write_off_date;
                    
                    $delStatus = $getWriteOffRows['delStatus'];
                    if($delStatus!='0'){
                        $write_off_del_date = $getWriteOffRows['write_off_del_date'];
                            list($yearDelWO, $monthDelWO, $dayDelWO)=explode("-", $write_off_del_date);
                            $write_off_del_date = $monthDelWO."-".$dayDelWO."-".$yearDelWO;
                    }
                }
            }
			
			$getAdjOffStr = "SELECT * FROM account_payments WHERE patient_id = '$patient_id' AND encounter_id = '$encounter_id'
                                AND charge_list_detail_id = '$charge_list_detail_id'  AND del_status = '0' and payment_type='Adjustment'";
            $getAdjOffQry = imw_query($getAdjOffStr);
            $countAdjOffRowsCount = imw_num_rows($getAdjOffQry);
            if($countAdjOffRowsCount>0){
                while($getWriteOffRows = imw_fetch_array($getAdjOffQry)){
                    $write_off_by_id = $getWriteOffRows['ins_id'];
                    
                    $write_off_by = getData('in_house_code', 'insurance_companies', 'id', $write_off_by_id);
                    if($write_off_by==''){ $write_off_by = getData('name', 'insurance_companies', 'id', $write_off_by_id); }
                    if($write_off_by_id==0){
                        $write_off_by="Patient";
                    }
                    $write_off_byArr[] = "Adjustment : ".$write_off_by;
					
                    $write_off_amount = $getWriteOffRows['payment_amount'];
                    $write_off_amountArr[] = $write_off_amount;
            
                    $write_off_date = get_date_format($getWriteOffRows['payment_date']);
                    $write_off_dateArr[] = $write_off_date;
                }
            }
            
            $claimDenied = $getEncountersDetailRows["claimDenied"];
            
            //DENIED DETAILS
            unset($deniedByArr);
            unset($deniedAmountArr);
            unset($deniedDateArr);
            if($claimDenied==1){
                $getDenialDetailsStr = "SELECT * FROM deniedPayment
                                        WHERE patient_id = '$patient_id'
                                        AND encounter_id = '$encounter_id'
                                        AND charge_list_detail_id = '$charge_list_detail_id'
                                        AND denialDelStatus = '0'
                                        ORDER BY deniedId DESC";
                $getDenialDetailsQry = imw_query($getDenialDetailsStr);
                $getDenialRows = imw_num_rows($getDenialDetailsQry);
                if($getDenialRows>0){
                    while($getDenialDetailsRow = imw_fetch_array($getDenialDetailsQry)){
                        $deniedBy = $getDenialDetailsRow['deniedBy'];
                        $deniedById = $getDenialDetailsRow['deniedById'];
                            $deniedBy_nam = getData('in_house_code', 'insurance_companies', 'id', $deniedById);
                            if(!$deniedBy_nam){
                                $deniedBy_nam = getData('name', 'insurance_companies', 'id', $deniedById);
                            }
                            //if(strlen($deniedBy)>8){	$deniedBy = $deniedBy
                            if($deniedBy_nam==""){
                                $deniedBy=$deniedBy;
                            }else{
                                $deniedBy=$deniedBy_nam;
                            }
                            
                        $deniedAmount = $getDenialDetailsRow['deniedAmount'];
                        $deniedDate = $getDenialDetailsRow['deniedDate'];			
                            list($yearDenial, $monthDenial, $dayDenial)=explode("-", $deniedDate);
                            $deniedDate = $monthDenial."-".$dayDenial."-".$yearDenial;
                            
                            $deniedDateArr[] = $deniedDate;
                            $deniedAmountArr[] = $deniedAmount;
                            $deniedByArr[] = $deniedBy;
                    }
                }else{
                        $deniedBy = '';
                        $deniedById = '';
                        $deniedAmount = '';
                        $deniedDate = '';
                }
            }
            //DENIED DETAILS
            
            #Credits for Procedures
            $credits4Procedure = $getEncountersDetailRows["creditProcAmount"];	  
			//Procedure Credits							
                $row_arr2 = getProcdebit_adust($encounter_id,$procId, $charge_list_detail_id);
                if($row_arr2){
                    for($f=0;$f<count($row_arr2);$f++)
                    {
						$row=$row_arr2[$f];
                        $arrProcePaymentDetails["Credits"][$f] = $row["amountApplied"]; 
                        $arrPaymentDates["Credits"][$f] =  $row["dateApplied"]; //Credit Date
                        $arr_pat_ajust["Credits"][$f] = $row["patient_id_adjust"]; 
                        $arr_pat["Credits"][$f] = $row["patient_id"];
						if($row["crAppliedTo"]=="payment"){
							 $method_crd["Credits"][$f] = "payment";
						}else if($row["crAppliedTo"]=="adjustment" && $row["charge_list_detail_id"]==$charge_list_detail_id){
							 $method_crd["Credits"][$f] = "debit";
						}else{
							 $method_crd["Credits"][$f] = "adjustment";
						}
                       
						if($row["credit_note"]){
							$credit_note_arr['cr_note'][]=$row["credit_note"];
							$credit_note_arr['cr_date'][]=$row["dateApplied"];
						}
                        $credit_by="";
                        if($row["type"]=='Insurance'){
                            //$credit_by = getData('name', 'insurance_companies', 'id', $row1["ins_case"]);
                            $ins_cas=$row["ins_case"];
                            $getInsCoStr = "SELECT * FROM insurance_companies WHERE id = '$ins_cas'";
                            $getInsCoQry = imw_query($getInsCoStr);
                            $getInsCoRow = imw_fetch_array($getInsCoQry);
                            $insCoCode = $getInsCoRow['in_house_code'];
                            if($insCoCode<>""){
                                $credit_by=$insCoCode;
                            }else{
                                $credit_by = $getInsCoRow['name'];
                            }
                        }else{
                            $credit_by=$row["type"];
                        }
                        if($credit_by==""){
                            $credit_by="Patient";
                        }
                        $arrProcePaymentby["Credits"][$f] = $credit_by; 
                    }		
                }
            //}
            //print_r($arrProcePaymentDetails);
                                            
            //(!empty($paidForProc) && ($paidForProc != "0.00"))
			$row_arr = getProcPaymentInfo($charge_list_detail_id);
			for($i=0;$i<count($row_arr);$i++)
			{
				$row=$row_arr[$i];
				if($row["paymentClaims"]=="Negative Payment"){
					$paidBy = ($row["paidBy"] == "Insurance") 
					?(!empty($row["in_house_code"]))? $row["in_house_code"] : substr($row["name"],0,4).".." 
					: $row["paidBy"];														
						
						if(strlen($paidBy)>13){ $paidBy = substr($paidBy, 0, 10)."...";	}
					if($row["paidForProc"]){
						$arrProcePaymentDetails["Paid"][$i] = array(-$row["paidForProc"],"Negative Payment : ".$paidBy);// + $row["overPayment"]
						$arrPaymentDates["Paid"][$i] =  get_date_format($row["paidDate"]); //Paid Date	
					}
				}else{
					$paidBy = ($row["paidBy"] == "Insurance") 
					?(!empty($row["in_house_code"]))? $row["in_house_code"] : substr($row["name"],0,4).".." 
					: $row["paidBy"];														
						
						if(strlen($paidBy)>13){ $paidBy = substr($paidBy, 0, 10)."...";	}
					if($row["paidForProc"]){
						$arrProcePaymentDetails["Paid"][$i] = array($row["paidForProc"],"Paid : ".$paidBy);// + $row["overPayment"]
						$arrPaymentDates["Paid"][$i] =  get_date_format($row["paidDate"]); //Paid Date	
					}
					// OVER PAYMENT	
					$arrProcePaymentDetails["OverPaid"][$i] = array($row["overPayment"],"Over Paid : ".$paidBy);
					if(($row["overPayment"]!='') && ($row["overPayment"]!=0) && ($row["overPayment"]!='0.00')){
						$arrPaymentDates["OverPaid"][$i] =  get_date_format($row["paidDate"]); //Paid Date	
					}else{
						$arrPaymentDates["OverPaid"][$i] =  ''; //Paid Date	
					}
				}
			}                                                                              
        
            //$grandTotalNewProcBalance += $newProcBalance;
        // Deduct
        ++$seq;
        ?>
        
    <!-- <tr class="text_10" valign="top">
        <td align="center" style="border-bottom:0px;"> -->
    
            <!-- Dates -->
            <?php 
                if(count($arrPaymentDates) > 0)
                {
                    $paid_date_arr = array();
                    
                    foreach($arrPaymentDates as $key => $val)
                    { 
                        $p=0;															
                        if(($key=="Paid") || ($key=="Credits")){
                            for($i=0;$i<count($val);$i++,$p++)
                            {
                                $paid_date_arr[] = $val[$i];
                            }	
                        }else if($key=="OverPaid"){
                            for($i=0;$i<count($val);$i++,$p++){
                                if($val[$i][1]!=''){
                                    $paid_date_arr[] = $val[$i];
                                }
                            }
                        }else if($key=="Deductible" && $multi_deduct_trans!=""){
                            for($i=0;$i<count($val);$i++,$p++)
                            {
                                $paid_date_arr[] = $val[$i];
                            }	
                        }else{
                            if($val){
                                $paid_date_arr[] = $val;
                            }	
                            if(($claimDenied=='1') && ($getDenialRows>0)){
                                if(count($deniedDateArr)>0){
                                    foreach($deniedDateArr as $denialDate){
                                        $paid_date_arr[] = $denialDate;
                                        unset($deniedDateArr);
                                    }
                                }
                            }
                        }
                    }													
                    if($coPayAdjustedOn==1){
                    }
                    if($countWriteOffRowsCount>0 || $countAdjOffRowsCount>0){
                        foreach($write_off_dateArr as $writeoffdate){
                            $paid_date_arr[] = $writeoffdate;
                        }
                    }
                    if($write_off_proc>0){
                        $paid_date_arr[] = $write_off_proc_date;
                    }
                }
            ?>
        <?php //overPayment
                $arrAddTR = array();
                if(count($arrProcePaymentDetails) > 0)
                {
                    $paid_desc_arr = array();
                    foreach($arrProcePaymentDetails as $key => $val)
                    {
                        $p=0;
                        if($key == "Paid")
                        {
                            for($i=0;$i<count($val);$i++,$p++)
                            {
                                $paid_desc_arr[] = $val[$i][1];
                            }
                        }else if($key == "OverPaid"){
                            for($i=0;$i<count($val);$i++,$p++)
                            {	
                                if(($val[$i][0]!='') && ($val[$i][0]!='0') && ($val[$i][0]!='0.00')){
                                    $paid_desc_arr[] = $val[$i][1];
                                }
                            }
                        }else if($key=="Deductible" && $multi_deduct_trans!=""){
                            for($i=0;$i<count($val);$i++,$p++)
                            {
                                $paid_desc_arr[] = $val[$i][1];
                            }	
                        }else if($key == "Credits"){
                            foreach($arrPaymentDates as $key1 => $val1)
                                {
                                    if($key1 == "Credits")
                                    {
                                        for($i=0;$i<count($val1);$i++,$p++)
                                        {
                                            if($method_crd[$key1][$i]=="payment"){
                                                $ref_shw="Refund : ".$arrProcePaymentby["Credits"][$i];
                                                $paid_desc_arr[] = $ref_shw;
                                                if(strlen($ref_shw) > 69){
                                                    $arrAddTR[] = $i;
                                                }
                                            }	
                                            if($method_crd[$key1][$i]=="adjustment"){
                                                if($arr_pat_ajust[$key1][$i]==$arr_pat[$key1][$i]){
                                                    $ref_shw="Adjustment Credit : ".$arrProcePaymentby["Credits"][$i];
                                                    $paid_desc_arr[] = $ref_shw;
                                                }else{
                                                    $getpat_to = getRowRecord('patient_data', 'pid', $arr_pat_ajust[$key1][$i]);
                                                    $fname_to = $getpat_to->fname;
                                                    $lname_to = $getpat_to->lname;
                                                    $mname_to = $getpat_to->mname;
                                                    $patientName_adj_to = ucwords(trim($lname_to.", ".$fname_to));
                                                    
                                                    $getpat_to_pat = getRowRecord('patient_data', 'pid', $arr_pat[$key1][$i]);
                                                    $fname_to_pat = $getpat_to_pat->fname;
                                                    $lname_to_pat = $getpat_to_pat->lname;
                                                    $mname_to_pat = $getpat_to_pat->mname;
                                                    $patientName_to = ucwords(trim($lname_to_pat.", ".$fname_to_pat));
                                                    
                                                    $ref_shw="Adjustment Credit : ".$arrProcePaymentby["Credits"][$i] . "  to ". $patientName_adj_to.' - '.$arr_pat_ajust[$key1][$i];
                                                    $paid_desc_arr[] = $ref_shw;
                                                    
                                                }
                                                    if(strlen($ref_shw) > 69){
                                                        $arrAddTR[] = $i;
                                                    }
                                            }	
                                            if($method_crd[$key1][$i]=="debit"){
                                                if($arr_pat_ajust[$key1][$i]==$arr_pat[$key1][$i]){
                                                    $ref_shw="Adjustment Debit : ".$arrProcePaymentby["Credits"][$i];
                                                    $paid_desc_arr[] = $ref_shw;
                                                }else{
                                                
                                                    $getpat_to = getRowRecord('patient_data', 'pid', $arr_pat_ajust[$key1][$i]);
                                                    $fname_to = $getpat_to->fname;
                                                    $lname_to = $getpat_to->lname;
                                                    $mname_to = $getpat_to->mname;
                                                    $patientName_adj_to = ucwords(trim($lname_to.", ".$fname_to));
                                                    
                                                    $getpat_to_pat = getRowRecord('patient_data', 'pid', $arr_pat[$key1][$i]);
                                                    $fname_to_pat = $getpat_to_pat->fname;
                                                    $lname_to_pat = $getpat_to_pat->lname;
                                                    $mname_to_pat = $getpat_to_pat->mname;
                                                    $patientName_to = ucwords(trim($lname_to_pat.", ".$fname_to_pat));
                                                    
                                                    $ref_shw="Adjustment Debit : ".$arrProcePaymentby["Credits"][$i]." to ". $patientName_adj_to.' - '.$arr_pat_ajust[$key1][$i];
                                                    $paid_desc_arr[] = $ref_shw;
                                                }
                                                if(strlen($ref_shw) > 68){
                                                    $arrAddTR[] = $i;
                                                }		
                                            }
                                                                                                                    
                                        }
                                    }	
                                }
                        }else{
                            if($deductby<>""){
                            }else{
                            }
                            $paid_desc_arr[] = $key;
                            if(($claimDenied=='1') && ($getDenialRows>0)){
                                if(count($deniedByArr)>0){
                                    foreach($deniedByArr as $denialBy){
                                        $paid_desc_arr[] = "Claim Denied : ".$denialBy;
                                        unset($deniedByArr);
                                    }
                                }
                            }
                        }
                    }					
                    if($coPayAdjustedOn==1){
                    }
                    if($countWriteOffRowsCount>0 || $countAdjOffRowsCount>0){
                        foreach($write_off_byArr as $writeoffby){
                            $paid_desc_arr[] = $writeoffby;
                        }
                    }
                    if($write_off_proc>0){
                        $paid_desc_arr[] = "Write Off : ".$write_off_proc_by;
                    }
                }
            ?>
        <!-- Cpt Payment info -->
        <?php 
            if(count($arrProcePaymentDetails) > 0)
            {
                $paid_price_arr = array();
                $write_off_arr = array();
                foreach($arrProcePaymentDetails as $key => $val)
                {
                    $p=0;
                    if($key == "Paid")
                    {
                        for($i=0;$i<count($val);$i++,$p++)
                        {
                            $paid_price_arr[] = number_format($val[$i][0],2);
                        }
                    }else if($key == "OverPaid"){
						$ProcOverPaid = 0;
						for($i=0;$i<count($val);$i++,$p++)
						{	
							if(($val[$i][0]!='') && ($val[$i][0]!='0') && ($val[$i][0]!='0.00')){
								$paid_price_arr[] = number_format($val[$i][0],2);
								$totalOverPaid = $totalOverPaid + $val[$i][0];
								$ProcOverPaid = $ProcOverPaid + $val[$i][0];
							}
						}
					}else if($key=="Deductible" && $multi_deduct_trans!=""){
						for($i=0;$i<count($val);$i++,$p++)
                        {
                            $paid_price_arr[] = number_format($val[$i][0],2);
                        }	
					}else if($key == "Credits"){
                        for($i=0;$i<count($val);$i++,$p++)
                        {
                            $paid_price_arr[] = number_format($val[$i],2);
                        }
                    }else{
                        if($val){
                            $paid_price_arr[] = number_format($val,2);
                        }
                        if(($claimDenied=='1') && ($getDenialRows>0)){
                            if(count($deniedAmountArr)>0){
                                foreach($deniedAmountArr as $denialAmount){
                                    $paid_price_arr[] = number_format($denialAmount,2);
                                    unset($deniedAmountArr);
                                }
                            }
                        }
                    }
                }
                if($countWriteOffRowsCount>0 || $countAdjOffRowsCount>0){
                    foreach($write_off_amountArr as $writeoffamount){
                        $paid_price_arr[] = number_format($writeoffamount,2);
                    }
                }
                if($write_off_proc>0){
                    $paid_price_arr[] = number_format($write_off_proc,2);
                }
				$proc_total_copay=array();
                if($coPayAdjustedOn==1){
                    $getCoPayPaidInfoStr3 = "SELECT b.paidForProc,b.paidDate FROM 
                    patient_chargesheet_payment_info a,
                    patient_charges_detail_payment_info b
                    WHERE a.encounter_id = '$encounter_id'
                    AND a.payment_id = b.payment_id
                    AND b.charge_list_detail_id = 0
                    and b.deletePayment= 0
                    ORDER BY a.payment_id asc";
                    $getCoPayPaidInfoQry3 = imw_query($getCoPayPaidInfoStr3);
                    $copay_paid_amt_top=0;
                    while($copay_row3=imw_fetch_array($getCoPayPaidInfoQry3)){
                        $copay_paid_amt_top=$copay_row3['paidForProc'];
                        $paidDate=$copay_row3['paidDate'];
                        $paid_price_arr[] = number_format($copay_paid_amt_top,2);
                        $paid_desc_arr[] = "CoPay Applied : Patient";
                        $paid_date_arr[] = get_date_format($paidDate);
						$proc_total_copay[]=$copay_paid_amt_top;
                    }
                }
            }												 
        ?>
        <!-- Cpt Payment info -->
        <!-- </td> -->
        <?php //if($credits4Procedure>0){
            $ovr_pad_proc=$getEncountersDetailRows['overPaymentForProc'];
            $ProcOverPaid=$ovr_pad_proc;
        //}?>
        <!-- <td align="right" valign="bottom" style="border-bottom:0px;"></td> -->
    <!-- </tr> -->
    <?php 
    //$val_all_data = '';
    $newDateArr = array();
	$tot_proc_total_pay_arr=array();
	for($j=0;$j<count($paid_date_arr);$j++){
       if($paid_price_arr[$j]<>'0.00' || $j==0){
		       if(stristr($paid_desc_arr[$j],"Paid") != "" || stristr($paid_desc_arr[$j],"Adjustment Credit") != "" || stristr($paid_desc_arr[$j],"Negative Payment") != "" || stristr($paid_desc_arr[$j],"CoPay Applied") != ""){
					if($j>0){
						if(stristr($paid_desc_arr[$j],"Negative Payment") != ""){
							$tot_proc_total_pay_arr[] = str_replace(',','',$paid_price_arr[$j]);
						}else{
							$tot_proc_total_pay_arr[] = str_replace(',','',$paid_price_arr[$j]);
						}
					}
				}
	   	   }
	  }
    for($j=0;$j<count($paid_date_arr);$j++){
       if($paid_price_arr[$j]<>'0.00' || $j==0){
            $val_all_data = '<tr>';
                $procs = ($j==0) ? $procCode : "&nbsp;";
                $dxs = ($j==0) ? $dx : "&nbsp;";
                $pos1 = ($j==0) ?  $posPracCode : "&nbsp;";
                $phyNames = ($j==0) ? $phyName : "&nbsp;";
                $unts = ($j==0) ? $units : 0;
				$show_unts = ($j==0) ? $units : "&nbsp;";
                $fee = ($j==0) ? $procFee : 0;
				//$proc_total_pay = ($j==0) ? $paidForProc+$overPaymentForProc+array_sum($proc_total_copay) : "&nbsp;";
				$proc_total_pay = ($j==0) ? array_sum($tot_proc_total_pay_arr) : "&nbsp;";
                if($j == count($paid_date_arr)-1){
                    $bor = 'border-top:0px; border-bottom:0px;';
                }else{
                    $bor = 'border-top:0px; border-bottom:0px;';
                }
                if(strlen($procs.$paid_desc_arr[$j]) > 33){
                    if(trim($procs) != "&nbsp;"){
                        $paid_desc_arr[$j] = substr("[".$procs."] ".$paid_desc_arr[$j],0,36)."...";
                    }
                    else{
                        $paid_desc_arr[$j] = substr($paid_desc_arr[$j],0,25)."...";
                    }
                }
                else{
                    if(trim($procs) != "&nbsp;"){
                        $paid_desc_arr[$j] = "[".$procs."] ".$paid_desc_arr[$j];
                    }
                    else{
                        $paid_desc_arr[$j] = $paid_desc_arr[$j];
                    }
                }
                if(stristr($paid_desc_arr[$j],"Write Off") != "" || stristr($paid_desc_arr[$j],"Discount") != "" || stristr($paid_desc_arr[$j],"Refund") != "" || stristr($paid_desc_arr[$j],"Adjustment Debit") != "" || stristr($paid_desc_arr[$j],"Adjustment") != ""){
                        $write_off_row="$".$paid_price_arr[$j];
                        $write_off_row=str_replace('$-','-$',$write_off_row);
                }
                else{
						if($j==0){
							$payment_row=str_replace('$-','-$',"$".number_format($proc_total_pay,2));
						}else{
							$payment_row="$".$paid_price_arr[$j];
							$payment_row=str_replace('$-','-$',$payment_row);
						}
                } 
                $charges_row = (float)$unts*(float)$fee;
                $charges_row = ($charges_row!=0) ? "$".number_format($charges_row,2) : "&nbsp;";
				if(stristr($paid_desc_arr[$j],'Deductible') != ""  || stristr($paid_desc_arr[$j],'Claim Denied') != ""){
					/*$paid_desc_arr[$j] = $paid_desc_arr[$j].' ('.$payment_row.')';
					$payment_row="";*/
				}
                $val_all_data .= '
                <td   style="'.$bor.'" class="text_10" align="center" nowrap>'.$paid_date_arr[$j].'</td>
                <td  style="'.$bor.'padding-left:10px; padding-right:10px;"  class="text_10" align="left">'.$paid_desc_arr[$j].'</td>
				<td  style="'.$bor.'padding-right:5px;text-align:right;" class="text_10" align="center">'. $show_unts .'</td>
                <td  style="'.$bor.'" class="text_10" align="center">'. $phyNames .'</td>
                <td  style="'.$bor.'padding-right:5px;text-align:right;" class="text_10" align="center">'.$charges_row.'</td>
                <td  style="'.$bor.'padding-right:5px;text-align:right;"  class="text_10" align="right">'.$payment_row.'</td>
                <td  style="'.$bor.'padding-right:5px;text-align:right;" class="text_10" align="center">'.$write_off_row.'</td>';
                
                
            //$val_all_data .= '</tr>';
            $newDateArr[$paid_date_arr[$j]][] = $val_all_data;
            $write_off_row = "";
            $payment_row = "";							
       } 
    }
    $paid_date_arr1 = array_values(array_unique($paid_date_arr));
    array_walk($paid_date_arr1,"ymd2ts");
    sort($paid_date_arr1);
    array_walk($paid_date_arr1,"ts2ymd");
    //$paid_date_arr1=array_reverse($paid_date_arr1);
    
    //print_r($paid_date_arr1);
    //sort($paid_date_arr1);
    for($u=0;$u<count($paid_date_arr1);$u++){
        $date = $paid_date_arr1[$u];
        
        for($p=0;$p<count($newDateArr[$date]);$p++){											
            if($newDateArr[$date][$p]){
                print $newDateArr[$date][$p];
            }
            //echo $u.'-'.count($paid_date_arr1);
            if($u == count($paid_date_arr1) - 1 && $p == count($newDateArr[$date]) - 1){
                echo '<td style="border-top:0px; border-bottom:0px; border-right:0px;padding-right:5px;text-align:right;" class="text_10" align="right">'.str_replace('$-','-$',"$".number_format(($newProcBalance-$ProcOverPaid),2)).'</td>';
                echo "</tr>";
                if($val_all_dx_code != ""){
                    echo $val_all_dx_code;
                }
                $val_all_dx_code = "";
            /*	if($val_all_dx_code != ""){
                    echo $val_all_dx_code;
                } */ 
                echo"<tr><td colspan='8' style='border-top:0px; background-color:#DCDCDC;' height='3'></td></tr>";
            }
            else{
                echo '<td style="border-top:0px; border-bottom:0px; border-right:0px;" class="text_10" align="right">&nbsp;</td>';
                echo "</tr>";
            }
        }
    }
    
    ?>

    <?php
        if($copayPaid ==1){
            if($coPayAdjustedOn==1){
                $chk_copay_pass=1;
            }
        }else{
            $chk_copay_pass=1;
        }	
    }
    
    $getCopayPaymentDateStr="SELECT * FROM patient_chargesheet_payment_info
                                    WHERE encounter_id='$encounter_id' AND payment_amount != '0' ";
        $getCopayPaymentDateQry=@imw_query($getCopayPaymentDateStr);
        $getCopayPaymentDateRow=@imw_fetch_array($getCopayPaymentDateQry);
        // Dates
        $arrPaymentDates = array();
        $arrCopayPayment = array();
        $arrPaymentDates[] = $date_of_service;	
        if($chk_copay_pass<>1 && $ch_id<>""){
            $copay=0;
        }

    if(($copay>0))
    {
        $copayBal=$copay-$copay_paid_amt;
        $arrCopayPayment = array();										
        $arrCopayPayment[] = "CoPay".'~~~'.$copay;											
        if($copay_paid_amt>0)
        {
            $getCoPayPaidInfoStr2 = "SELECT b.paidForProc,b.paidDate FROM 
            patient_chargesheet_payment_info a,
            patient_charges_detail_payment_info b
            WHERE a.encounter_id = '$encounter_id'
            AND a.payment_id = b.payment_id
            AND b.charge_list_detail_id = 0
            and b.deletePayment= 0
            ORDER BY a.payment_id asc";
            $getCoPayPaidInfoQry2 = imw_query($getCoPayPaidInfoStr2);
            while($copay_row2=imw_fetch_array($getCoPayPaidInfoQry2)){
                $paidForProc_copay=$copay_row2['paidForProc'];
                $paidDate_copay1=$copay_row2['paidDate'];
                list($year, $month, $day) = explode("-", $paidDate_copay1);
                $paidDate_copay = $month."-".$day."-".$year;
                $arrPaymentDates[] = $paidDate_copay;	
                $arrCopayPayment[] = $paidForProc_copay;
            }
            $copay_amt_shw=$copay_paid_amt;
            $copayBal=$copay-$copay_paid_amt;
            
        }
        else if(!empty($copayNR))
        {
            $arrCopayPayment[] = "CoPay Not Required".'~~~'."-";
			$arrPaymentDates[] = "";
            $copayBal=0;
        }											
    }
    
    if(($copay>0)){
		$ik=0;
		foreach($arrPaymentDates as $key2 => $val){
		$ik++;
    ?>
        <tr  valign="top">
            <td  class="text_10" align="center" nowrap>
                <?php 
                    if($coPayWriteOff=='1'){
                        echo $coPayWriteOffDate;													
                    }else{
						echo $val;
					}
                ?>
            </td>
            <td  style="padding-left:10px; padding-right:10px;"  class="text_10" align="left">
            <?php 
                
				$copay_pay_exp=explode('~~~',$arrCopayPayment[$key2]);
				$show_copay_name=(string)$copay_pay_exp[0];
				if($copay_pay_exp[0]){
					if($show_copay_name!='CoPay' && $show_copay_name!='CoPay Not Required'){
						$show_key='Paid - Patient';
					}else{
						$show_key=$show_copay_name;
					}
					echo $show_key;	
				}
                
                if($coPayWriteOff=='1'){
                    echo "- Write Off";
                }
            ?>
            </td>
            <td class="text_10" align="center">&nbsp;</td>
            <td class="text_10" align="center">&nbsp;</td>
            <td  style="padding-right:5px;text-align:right;" class="text_10" align="center">
             <?php
				if($copay_pay_exp[0]){
					if($show_copay_name=='CoPay'){
						$thisVal = "$".number_format($copay_pay_exp[1],2);
						echo $thisVal;	
					}
				}
                
            ?>			
            </td>
            <td style="padding-right:5px;text-align:right;"  class="text_10" align="right">
            <?php
				if($copay_pay_exp[0]){
					if($show_copay_name!='CoPay'){
						$thisVal = "$".number_format($copay_pay_exp[0],2);
						echo $thisVal;	
					}
				}
            ?>	
            </td>
            <td  style="padding-right:5px;text-align:right;" class="text_10" align="center">
                <?php  
				  if($coPayWriteOff=='1'){
					echo $thisVal;
					$copayBal = $copayBal - str_replace('$','',$thisVal);
				  }
                ?>
            </td>
            <td style="border-top:0px; border-bottom:0px; border-right:0px;padding-right:5px;text-align:right;" class="text_10" align="right">
                <?php 
					if(count($arrPaymentDates)==$ik){
						echo "$".number_format($copayBal, 2);
					}
				?>
            </td>
        </tr>									
    
    <?php
    	}
	}
        $getExtCommentsStr = "SELECT * FROM paymentscomment
                                WHERE encounter_id = '$encounter_id'
                                AND commentsType = 'External'
                                and c_type!='batch'";
        $getExtCommentsQry = imw_query($getExtCommentsStr);
        $countCommRows = @imw_num_rows($getExtCommentsQry);
        if($countCommRows>0){
            ?>
            <tr>
                <td colspan="8" align="left" class="text_10"><b>Comments</b></td>
            </tr>
            <?php
            while($getExtCommentsRows = imw_fetch_array($getExtCommentsQry)){
                $encCommentsDate = $getExtCommentsRows['encCommentsDate'];
                    list($comYear, $comMonth, $comDay) = explode("-", $encCommentsDate);
                    $encCommentsDate = $comMonth."-".$comDay."-".$comYear;
                $encComments = $getExtCommentsRows['encComments'];
                ?>
                <tr>
                    <td class="text_10" align="center" style="vertical-align:text-top;"><?php echo $encCommentsDate; ?></td>
                    <td class="text_10" align="left" colspan="7" style="width:520px;"><?php echo html_entity_decode(stripslashes($encComments)); ?></td>
                </tr>
                <?php
            }
        }
        //print_r($credit_note_arr);
        if(count($credit_note_arr['cr_note'])>0){
    ?>
            <tr>
                <td colspan="8" align="left" class="text_10"><b>Refund Cr Notes</b></td>
            </tr>
            <?php for($k=0;$k<count($credit_note_arr['cr_note']);$k++){?>
            <tr>
                <td class="text_10" align="center"><?php echo $credit_note_arr['cr_date'][$k]; ?></td>
                <td class="text_10" align="left" colspan="7"><?php echo $credit_note_arr['cr_note'][$k]; ?></td>
            </tr>
    <?php
            }	
        }
       $getPaymentStatusStr = "SELECT a.* FROM 
                            patient_chargesheet_payment_info a
                            WHERE 
                             a.encounter_id='$encounter_id'
                            AND a.payment_amount != '0' 
                            AND a.markPaymentDelete!='1'";
        $getPaymentStatusQry = imw_query($getPaymentStatusStr);											
        if(imw_num_rows($getPaymentStatusQry) > 0)
        {
    ?>	
            <!-- Payment History -->
            <tr><td style="padding-top:10px;" colspan="8"></td></tr>
            <tr class="text_10">
                <td align="left" colspan="8" style="background-color:#DCDCDC;"><b>Payment History</b></td>											
            </tr>	
            <!-- Payment History -->	
    <?php	
        }
        
        
        while($getPaymentStatusRow = imw_fetch_array($getPaymentStatusQry)){
            $date_of_payment = $getPaymentStatusRow['date_of_payment'];
                list($year, $month, $day) = explode("-", $date_of_payment);
                $date_of_payment = $month."-".$day."-".$year;												
            $payment_id = $getPaymentStatusRow['payment_id'];
            
            //GET PAYMENT AMOUNT
            $getPaymentQry = imw_query("SELECT sum(paidForProc) as payment_amount,
                                        sum(overPayment) as overPaid
                                        FROM patient_charges_detail_payment_info
                                        WHERE payment_id = '$payment_id' AND deletePayment != '1'
                                        AND (paidForProc>0 || overPayment>0)
                                        and charge_list_detail_id != '0' $ch_based_print");
            $getPaymentRow = imw_fetch_assoc($getPaymentQry);
            $paidAmt = $getPaymentRow['payment_amount'] + $getPaymentRow['overPaid'];
            //GET PAYMENT AMOUNT
            
            $expirationDate = $getPaymentStatusRow['expirationDate'];
            list($year, $month, $day) = explode("-", $expirationDate);
            $expirationDate = $month."-".$day."-".$year;
            $paid_by = $getPaymentStatusRow['paid_by'];
            
            $payMethod = $getPaymentStatusRow['payment_mode'];
            $creditCardNo = $getPaymentStatusRow['creditCardNo'];
            $creditCardCo = $getPaymentStatusRow['creditCardCo'];
            $checkNo = $getPaymentStatusRow['checkNo'];
            $balance_amount = $getPaymentStatusRow['balance_amount'];
            $insProviderId = $getPaymentStatusRow['insProviderId'];
                                                            
            if($paid_by != "Insurance"){
                if(($payMethod != 'Cash')){
					if($creditCardCo!="" || $creditCardNo!=''){
						if($creditCardNo!=''){
                        	$paid_by = $paid_by.", Credit Card#: ".$creditCardNo;
                   	 	}
					}else{
						if($checkNo!=''){
	
							$paid_by = $paid_by.",  Check#: ".$checkNo;
						}
					}
                    
                }
            }
            else
            {
                $insProvider = getInsuranceProviderCode($insProviderId);
                if(strlen($insProvider) > 24){
                        $insProvider = substr($insProvider,0,22)."..."														;
                }
                $tmpPayMethod = (!empty($checkNo)) ? "# ".$checkNo : "";
                $paid_byTmp = $insProvider." - ".$payMethod.$tmpPayMethod; //
                $payMethod = $paid_by;													
                $paid_by = $paid_byTmp;
            }
            if($ch_id){
                /*$sql_charge_detail=imw_query("select coPayAdjustedAmount from patient_charge_list_details  where charge_list_detail_id in($ch_id)");
                $fet_copay_adust=@imw_fetch_array($sql_charge_detail);
                $copay_adustedamount=$fet_copay_adust['coPayAdjustedAmount'];
                $sql_chk_copay_payment=imw_query("SELECT sum(paidForProc) as copay_amount_chk
                                        FROM patient_charges_detail_payment_info
                                        WHERE payment_id = '$payment_id' and charge_list_detail_id=0");
                $getcopay_amout = @imw_fetch_assoc($sql_chk_copay_payment);
                $amt_copy_chk = $getcopay_amout['copay_amount_chk'];*/
            }
            if($paidAmt>0){
                $negative_sign="";
                if($getPaymentStatusRow["paymentClaims"]=="Negative Payment"){
                    $negative_sign="-";
                }
            ?>
            <tr class="text_10">
                <td align="left"><?php echo $date_of_payment; ?></td>
                <td align="left"><?php echo $payMethod; ?></td>
                <td align="left" colspan="5"><?php echo $paid_by; ?></td>
                <td align="right"  valign="bottom"><?php echo $negative_sign."$".number_format($paidAmt, 2); ?></td>
            </tr>
            <?php
            }else if($copay_adustedamount==1 && $ch_id<>"" && $amt_copy_chk>0){
            ?>
                <!-- <tr class="text_10">
                    <td align="center"><?php //echo $date_of_payment; ?></td>
                    <td align="center"><?php //echo $payMethod; ?></td>
                    <td colspan="7" align="left" style="padding-left:25px;"><?php //echo $paid_by; ?></td>
                    <td align="right" valign="bottom"><?php //echo number_format($amt_copy_chk, 2); ?></td>
                </tr> -->
            <?php
            
            }
        }
        $getCoPayPaidInfoStr1 = "SELECT 
            b.paidForProc,b.paidDate,
            a.payment_mode,b.paidBy,
            a.checkNo,a.creditCardNo,a.creditCardCo,
            a.insProviderId
            FROM 
            patient_chargesheet_payment_info a,
            patient_charges_detail_payment_info b
            WHERE a.encounter_id = '$encounter_id'
            AND a.payment_id = b.payment_id
            AND b.charge_list_detail_id = 0
            and b.deletePayment= 0
            ORDER BY a.payment_id asc";
            $getCoPayPaidInfoQry1 = imw_query($getCoPayPaidInfoStr1);
            while($copay_row1=imw_fetch_array($getCoPayPaidInfoQry1)){
                $amt_copy_chk=$copay_row1['paidForProc'];
                $paidDate=$copay_row1['paidDate'];
                $payMethod=$copay_row1['payment_mode'];
                $paid_by=$copay_row1['paidBy'];
                $checkNo=$copay_row1['checkNo'];
                $creditCardNo=$copay_row1['creditCardNo'];
				$creditCardCo = $copay_row1['creditCardCo'];
                $insProviderId=$copay_row1['insProviderId'];
                list($year, $month, $day) = explode("-", $paidDate);
                $date_of_payment = $month."-".$day."-".$year;
                if($paid_by != "Insurance"){
                    if(($payMethod != 'Cash')){
						if($creditCardCo!="" || $creditCardNo!=''){
							if($creditCardNo!=''){
								$paid_by = $paid_by.", Credit Card#: ".$creditCardNo;
							}
						}else{
							if($checkNo!=''){
								$paid_by = $paid_by.",  Check#: ".$checkNo;
							}
						}
                    }
                }else{
                    $insProvider = getInsuranceProviderCode($insProviderId);
                    $tmpPayMethod = (!empty($checkNo)) ? "# ".$checkNo : "";
                    if(strlen($insProvider) > 35){
                        $insProvider = substr($insProvider,0,32)."..."														;
                }
                    $paid_byTmp = $insProvider." - ".$payMethod.$tmpPayMethod; //
                    $payMethod = $paid_by;													
                    $paid_by = $paid_byTmp;
                }	
            
            ?>
            <tr class="text_10">
                <td align="left"><?php echo $date_of_payment; ?></td>
                <td align="left"><?php echo $payMethod; ?></td>
                <td colspan="5" align="left">CoPay Applied : <?php echo $paid_by; ?></td>
                <td align="right"  valign="bottom"><?php echo "$".number_format($amt_copy_chk, 2); ?></td>
            </tr>
        <?php	
        }
    ?>
    <tr class="text_10">
      <td colspan="8" align="right" class="heading">&nbsp;</td>
    </tr>
    <tr class="text_10">
        <td colspan="7" align="right"><b>Total Approved Procedure Cost:</b></td>
        <td align="right" valign="bottom"><b>
            <?php
                if(ch_id==""){ 
                    echo "$".number_format($approvedTotalAmt,2); 
                }else{
                    echo "$".number_format($approvedAmt_final,2); 
                }	
            ?></b></td>
    </tr>
    
    <!-- Total Applied to Procedures -->
    
    <tr class="text_10">
        <td colspan="7" align="right"><b>Total Applied to Procedures:</b></td>
        <td align="right" valign="bottom"><b>
        <?php
            if(ch_id==""){ 
             echo (!empty($totalAppliedAmt)) ? "$".number_format($totalAppliedAmt+$copay_amt_shw, 2) : "$0.00"; 
            }else{
                echo "$".number_format($paidForProc_final+$copay_amt_shw, 2); 
            }
        ?></b></td>
    </tr>
    
    <!-- Total Applied to Procedures -->
    
    <tr class="text_10">
        <td colspan="7" align="right"><b>Balance:</b></td>
        <td align="right" valign="bottom"><b>
        <?php 
        if(ch_id==""){ 
            echo str_replace('$-','-$',"$".number_format(($totalBalance - $totalOverPaid), 2));
        }else{ 
            echo str_replace('$-','-$',"$".number_format(($newProcBalance_final - $overPaymentForProc_final), 2)); 
        }
        ?></b></td>
    </tr>
    <tr><td height="10" colspan="8">&nbsp;</td></tr>
    <?php } ?>
    <tr>
    	<td colspan="8">
        	<table style="width:700px;" cellpadding="0" cellspacing="0">
                <tr><td height="8px;"></td></tr>
                <tr id="postedSuperbill" style="display:block;">
                    <td height="105" colspan="2" align="left" valign="top">
                        <table style="width:700px;" border="0" cellpadding="0" cellspacing="0">
                            <tr align="left">
                                <td colspan="6" align="left" valign="top">
                                    <!-- List Cpt -->
                                    
                                    <!-- List Cpt -->
                              </td>
                            </tr>
                            <tr>
                                <td colspan="10">
                                    <table style="width:100%;">
                                        <tr>
                                            <td style="text-align:center;" class="text_10b" colspan="8">Account Balance Summary</td>
                                        </tr>
                                        <tr>
                                            <td class="text_b_w"></td>
                                            <?php echo $headerTd; ?>
                                            <td class="text_b_w" style="width:90px; text-align:right;">Total</td>
                                        </tr>
                                        
                                        <tr>
                                            <td class="text_10b">Patient</td>
                                            <?php echo $patientDueData;?>
                                            <td class="text_10" style="text-align:right;">$<?php echo number_format(array_sum($patientDueAmountArr),2); ?></td>
                                        </tr>	
                                        
                                        <tr>
                                            <td class="text_10b">Insurance</td>
                                             <?php echo $totalDueData; ?>
                                             <td class="text_10" style="text-align:right;">$<?php echo number_format(array_sum($patientInsDueAmountArr),2); ?></td>
                                        </tr>
                                        
                                    </table>
                                </td>
                            </tr>
                          <tr>
                              <td height="17" align="right" style="padding-right:30px;">&nbsp;</td>
                          </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</page>
<?php

$obData = ob_get_contents();
ob_end_clean();
$print_data = $obData;

$filebasepath =data_path().'UserId_'.$_SESSION['authId'].'/tmp/receipt/';
if( !is_dir($filebasepath) ){
	mkdir($filebasepath, 0755, true);
	chown($filebasepath, 'apache');
}
foreach(glob($filebasepath."/*.html") as $html_file_names){
	if($html_file_names){unlink($html_file_names);}
}
$pdfName = '/receipt/receipt_'.$_SESSION['authId'].'_'.date('Y_m_d_h_i_s').'.html';
$file_location = write_html($print_data,$pdfName);

//file_put_contents('../reports/new_html2pdf/receipt.html');
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript">
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	top.html_to_pdf('<?php echo $file_location; ?>','p');
	window.close();
</script>
</body>
</html>
