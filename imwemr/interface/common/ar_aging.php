<?php
$ignoreAuth = true;
if($argv[1]){
        $practicePath = trim($argv[1]);
        $_SERVER['REQUEST_URI'] = $practicePath;
}

include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/assign_new_task.php");
include_once(dirname(__FILE__).'/../../library/classes/cls_common_function.php');
$CLSCommonFunction = new CLSCommonFunction;

ini_set("memory_limit","3072M");

$printFile = false;
$checkInDataArr=array();

$aging_start=30;
$aging_to=0;
$BalanceAmount=0;

$arr_group_str='';
$arr_facility_str='';
$arr_ins_group_str='';
$arr_ins_comp_str='';
//GETTING AGING RULES INFO
$qry="Select ar_aging,tm_group,ar_facility,tm_ins_group,tm_ins_comp FROM tm_rules_list WHERE cat_id='3' AND rule_status='0'";
$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
	list($aging, $amount)=explode("#", $res['ar_aging']);
	if($aging==181){
		$a_start=$a_to=181;
	}else{
		list($a_start, $a_to)=explode("-", $aging);
	}
	$amount=($amount=='any')? 0 : $amount;
	
	$arr_aging_start[]=$a_start;
	$arr_aging_to[]=$a_to;
	$arr_amount[]=$amount;
    if($res['tm_group']!='')
	$arr_group_str.=trim($res['tm_group']).',';
    if($res['ar_facility']!='')
	$arr_facility_str.=trim($res['ar_facility']).',';
    if($res['tm_ins_group']!='')
	$arr_ins_group_str.=trim($res['tm_ins_group']).',';
    if($res['tm_ins_comp']!='')
	$arr_ins_comp_str.=trim($res['tm_ins_comp']).',';
}unset($rs);

$aging_start=min($arr_aging_start);
$aging_to=max($arr_aging_to);
$BalanceAmount=min($arr_amount);


$arr_group=array();
$arr_group_str = substr($arr_group_str,0,-1);
$arr_groups=explode(',',$arr_group_str);
foreach($arr_groups as $grp) {
    if($grp && !in_array($grp,$arr_group))
    $arr_group[]=trim($grp);
}
$arr_facility=array();
$arr_facility_str = substr($arr_facility_str,0,-1);
$arr_facilitys=explode(',',$arr_facility_str);
foreach($arr_facilitys as $fac) {
    if($fac && !in_array($fac,$arr_facility))
    $arr_facility[]=trim($fac);
}

$arr_ins_group=array();
$arr_ins_group_str = substr($arr_ins_group_str,0,-1);
$arr_ins_groups=explode(',',$arr_ins_group_str);
foreach($arr_ins_groups as $ins_grp) {
    if($ins_grp && !in_array($ins_grp,$arr_ins_group)){
        $qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $ins_grp . "' ORDER BY id limit 10";
        $res = imw_query($qry);
        $tmp_grp_ins_arr = array();
        if (imw_num_rows($res) > 0) {
            while ($det_row = imw_fetch_array($res)) {
                $tmp_grp_ins_arr[] = $det_row['id'];
            }
            $grp_ins_ids = implode(",", $tmp_grp_ins_arr);
        }
    }
}

$arr_ins_comp=array();
$arr_ins_comp_str = substr($arr_ins_comp_str,0,-1);
if($grp_ins_ids) {
    $arr_ins_comp_str=$arr_ins_comp_str.','.$grp_ins_ids;
}
$arr_ins_comps=explode(',',$arr_ins_comp_str);
foreach($arr_ins_comps as $ins_comp) {
    if($ins_comp && !in_array($ins_comp,$arr_ins_comp))
    $arr_ins_comp[]=trim($ins_comp);
}

//pre($arr_ins_comp);
/*$collectionIds = get_account_status_id_collections();
if(empty($collectionIds)==false){
	$arrCollectionIds= explode(',', $collectionIds);
	$arrCollectionIds = array_combine($arrCollectionIds, $arrCollectionIds);
}*/

/*$qry ="Select elem_arCycle FROM copay_policies WHERE policies_id='1'";
$rs=imw_query($qry);
$res=imw_fetch_array($rs);
$aggingCycle = $res['elem_arCycle'];*/
//---------------------//

$All_due = false;
if($aging_to == '181'){
	$All_due = true;
}


//--- GET MAIN DATA AS SELECTED GROUP ID AND AS A/R AGGING --------	
$qryPriPart = $qrySecPart = $qryTerPart = $qryPatPart= $qryDOSPart= $qryAggings='';

if($All_due == false){
	$lastPriPart= " AND (DATEDIFF(NOW(),patient_charge_list.date_of_service) <= $aging_to)";
	$lastSecPart= " AND (DATEDIFF(NOW(),from_sec_due_date) <= $aging_to)";
	$lastTerPart= " AND (DATEDIFF(NOW(),from_ter_due_date) <= $aging_to)";
	$lastPatPart= " AND IF(from_pat_due_date >0, (DATEDIFF( NOW(), from_pat_due_date ) <=$aging_to ) , (DATEDIFF( NOW() , patient_charge_list.date_of_service) <=$aging_to))";
}

$lastDatePart=" AND (
	(pri_due>0 AND (DATEDIFF(NOW(),patient_charge_list.date_of_service) >= $aging_start) $lastPriPart)
	OR (sec_due>0 AND (DATEDIFF(NOW(),from_sec_due_date) >= $aging_start) $lastSecPart) 
	OR (tri_due>0 AND (DATEDIFF(NOW(),from_ter_due_date) >= $aging_start) $lastTerPart)
	OR (pat_due >0 AND (IF(from_pat_due_date >0, (DATEDIFF(NOW(),from_pat_due_date) >=$aging_start ) , (DATEDIFF(NOW(),patient_charge_list.date_of_service) >= $aging_start))) $lastPatPart)
	)";

$grp_where='';
if(empty($arr_group)==false) {
    $grp_where= " AND patient_charge_list.gro_id IN(".implode(',',$arr_group).") ";
}
$fac_where='';
if(empty($arr_facility)==false) {
    $fac_where= " AND patient_charge_list.facility_id IN(".implode(',',$arr_facility).") ";
}
$ins_comp_where='';
if(empty($arr_ins_comp) === false){
	$ins_comp_where = " AND (patient_charge_list.primaryInsuranceCoId IN (".implode(',',$arr_ins_comp).")
				OR patient_charge_list.secondaryInsuranceCoId IN (".implode(',',$arr_ins_comp).") 
				OR patient_charge_list.tertiaryInsuranceCoId IN (".implode(',',$arr_ins_comp)."))";	
}
$query = "select patient_charge_list.primaryInsuranceCoId,
patient_charge_list.secondaryInsuranceCoId, patient_charge_list.tertiaryInsuranceCoId,
patient_charge_list.gro_id,patient_charge_list.facility_id,
patient_charge_list.patient_id,patient_charge_list.charge_list_id,
patient_charge_list.date_of_service,
patient_charge_list.encounter_id, patient_charge_list.totalBalance, 
patient_data.lname,patient_data.fname,patient_data.mname,
patient_data.pat_account_status,
patient_charge_list.patientDue, patient_charge_list.encounter_id, patient_charge_list.comment,
DATEDIFF(NOW(),patient_charge_list.date_of_service) as dos_date_diff,	
DATEDIFF(NOW(),patient_charge_list.date_of_service) as last_pri_dop_diff,
DATEDIFF(NOW(),patient_charge_list_details.from_sec_due_date) as last_sec_dop_diff,
DATEDIFF(NOW(),patient_charge_list_details.from_ter_due_date) as last_ter_dop_diff,
DATEDIFF(NOW(),patient_charge_list_details.from_pat_due_date) as last_pat_dop_diff,
patient_charge_list_details.charge_list_detail_id,
patient_charge_list_details.pri_due as pri_due,
patient_charge_list_details.sec_due as sec_due,
patient_charge_list_details.tri_due as tri_due,
patient_charge_list_details.pat_due as pat_due
FROM patient_charge_list 
LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
JOIN patient_data on patient_data.id = patient_charge_list.patient_id 
where patient_charge_list_details.del_status='0' 
and ((pri_due + sec_due + tri_due)>$BalanceAmount OR pat_due>$BalanceAmount) AND patient_charge_list.totalBalance>0 
".$lastDatePart." 
".$grp_where." 
".$fac_where." 
".$ins_comp_where." 
ORDER BY patient_charge_list.encounter_id";

$qryRes = array();
$qry = imw_query($query);
while($res=imw_fetch_assoc($qry)){
	$qryRes[] = $res;
}


//Ar aging Group, facility , ins group, ins comp
//--- GET Groups SELECT BOX ----
$group_id_arr = array();
$group_query = "Select gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
while ($group_res = imw_fetch_array($group_query_res)) {
    $group_id = $group_res['gro_id'];
    $group_id_arr[$group_id] = $group_res['name'];
}

//SET INSURANCE COMPANY DROP DOWN
$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
$arrAllInsGroups[0] = 'No Insurance';
$arrInsMapInsGroups[0]='0';
while ($row = imw_fetch_array($insGroupQryRes)) {
    $ins_grp_id = $row['id'];
    $ins_grp_name = $row['title'];
    $arrAllInsGroups[$ins_grp_id] = trim($ins_grp_name);

    $qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $row['id'] . "'";
    $res = imw_query($qry);
    $tmp_grp_ins_arr = array();
    if (imw_num_rows($res) > 0) {
        while ($det_row = imw_fetch_array($res)) {
            $arrInsMapInsGroups[$det_row['id']]= trim($ins_grp_id);
        }
    }
}
$ins_comp_arr = array();
$insQryRes = insurance_provider_xml_extract();
for ($i = 0; $i < count($insQryRes); $i++) {
    if(!$insQryRes[$i]['attributes']) continue;
    if($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
        $ins_id = $insQryRes[$i]['attributes']['insCompId'];
        $ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
        if ($ins_name == '') {
            $ins_name = $insQryRes[$i]['attributes']['insCompName'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }
        $ins_comp_arr[$ins_id] = $ins_name;
    }
}

//pre($ins_comp_arr);
//GET INSURANCE GROUP DROP DOWN
$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
$ins_group_arr = array();
$ins_group_options = '';
while ($row = imw_fetch_array($insGroupQryRes)) {
    $ins_grp_id = $row['id'];
    $ins_grp_name = $row['title'];

    $qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $row['id'] . "' ORDER BY id";
    $res = imw_query($qry);
    $tmp_grp_ins_arr = array();
    if (imw_num_rows($res) > 0) {
        while ($det_row = imw_fetch_array($res)) {
            $tmp_grp_ins_arr[] = $det_row['id'];
        }
        $grp_ins_ids = implode(",", $tmp_grp_ins_arr);
        $ins_group_arr[$grp_ins_ids] = $ins_grp_name;
    }
}

//Get POS facilities
$pos_facilities_arr = array();
$qry = "select pos_facilityies_tbl.facilityPracCode as name,
				pos_facilityies_tbl.pos_facility_id as id,
				pos_tbl.pos_prac_code
				from pos_facilityies_tbl
				left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
				order by pos_facilityies_tbl.headquarter desc,
				pos_facilityies_tbl.facilityPracCode";
$res = imw_query($qry);
while($row=imw_fetch_assoc($res)){
    $id = $row['id'];
    $name = $row['name'];
    $pos_prac_code = $row['pos_prac_code'];
    $pos_facilities_arr[$id] = $name.' - '.$pos_prac_code;
}

$insComIdArr = array();
$mainInsIdArr = array();
$patientNameArr = array();
$grandPatientDueArr = array();
$patient_age_arr = array();
$totalCollectionInsBalance=0;
for($i=0;$i<count($qryRes);$i++){

	$eid=$qryRes[$i]['encounter_id'];

	//ALL COLLECTION STATUS TOTAL & OVER-PAYMENT
	//if(sizeof($arrCollectionIds)>0 && $arrCollectionIds[$qryRes[$i]['pat_account_status']]){
	//	$totalCollectionBalance+= $qryRes[$i]['pri_due']+$qryRes[$i]['sec_due']+$qryRes[$i]['tri_due']+$qryRes[$i]['pat_due'];
	//}else{

		$priDue = trim($qryRes[$i]['pri_due']);
		$secDue = trim($qryRes[$i]['sec_due']);
		$terDue = trim($qryRes[$i]['tri_due']);
		$patDue = trim($qryRes[$i]['pat_due']);
			
		$last_pri_dop_diff = $qryRes[$i]['last_pri_dop_diff'];
		$last_sec_dop_diff = $qryRes[$i]['last_sec_dop_diff'];
		$last_ter_dop_diff = $qryRes[$i]['last_ter_dop_diff'];
		$last_pat_dop_diff = $qryRes[$i]['last_pat_dop_diff'];
		$dos_date_diff = $qryRes[$i]['dos_date_diff'];
		
		//--- PATIENT DETAILS ---
		$patient_id = $qryRes[$i]['patient_id'];
		$patient_name = core_name_format($qryRes[$i]['lname'], $qryRes[$i]['fname'], $qryRes[$i]['mname']);		
		
		//INSURANCE
		$duePicked=0;
		$dueFrom='insurance';
		$amount_due=0;
		if($priDue>0){
			$duePicked=1;
			if($mainResultArr[$eid]['insurance']['days_aged']){
				if($last_pri_dop_diff<$mainResultArr[$eid]['insurance']['days_aged']){
					$mainResultArr[$eid]['insurance']['days_aged']= $last_pri_dop_diff;
				}
			}else{
				$mainResultArr[$eid]['insurance']['days_aged']= $last_pri_dop_diff;
			}
			$amount_due+=$priDue;
		}
		if($secDue>0){
			$duePicked=1;
			if($mainResultArr[$eid]['insurance']['days_aged']){
				if($last_sec_dop_diff<$mainResultArr[$eid]['insurance']['days_aged']){
					$mainResultArr[$eid]['insurance']['days_aged']= $last_sec_dop_diff;
				}
			}else{
				$mainResultArr[$eid]['insurance']['days_aged']= $last_sec_dop_diff;
			}
			
			$amount_due+=$secDue;
		}
		if($terDue>0){
			$duePicked=1;
			if($mainResultArr[$eid]['insurance']['days_aged']){
				if($last_ter_dop_diff<$mainResultArr[$eid]['insurance']['days_aged']){
					$mainResultArr[$eid]['insurance']['days_aged']= $last_ter_dop_diff;
				}
			}else{
				$mainResultArr[$eid]['insurance']['days_aged']= $last_ter_dop_diff;
			}			
			$amount_due+=$terDue;
		}
        
        
        $primaryInsuranceCoId = trim($qryRes[$i]['primaryInsuranceCoId']);
		$secondaryInsuranceCoId = trim($qryRes[$i]['secondaryInsuranceCoId']);
		$tertiaryInsuranceCoId = trim($qryRes[$i]['tertiaryInsuranceCoId']);
        
        $ins_group_ids=array();
        $ins_comp_ids=array();
        $insurance_group_str='';
        $insurance_comp_str='';
        if($arrInsMapInsGroups[$primaryInsuranceCoId]!=0 && $arrInsMapInsGroups[$primaryInsuranceCoId]!='') {
            $ins_group_ids[]=$arrInsMapInsGroups[$primaryInsuranceCoId];
            $ins_comp_ids[]=$primaryInsuranceCoId;
        }
        if($arrInsMapInsGroups[$secondaryInsuranceCoId]!=0 && $arrInsMapInsGroups[$secondaryInsuranceCoId]!='') {
            $ins_group_ids[]=$arrInsMapInsGroups[$secondaryInsuranceCoId];
            $ins_comp_ids[]=$secondaryInsuranceCoId;
        }
        if($arrInsMapInsGroups[$tertiaryInsuranceCoId]!=0 && $arrInsMapInsGroups[$tertiaryInsuranceCoId]!='') {
            $ins_group_ids[]=$arrInsMapInsGroups[$tertiaryInsuranceCoId];
            $ins_comp_ids[]=$tertiaryInsuranceCoId;
        }

        $ins_group_ids=array_unique($ins_group_ids);
        foreach($ins_group_ids as $insgid) {
            $insurance_group_str.=$arrAllInsGroups[$insgid].',';
        }
        
        $ins_comp_ids=array_unique($ins_comp_ids);
        foreach($ins_comp_ids as $inscoid) {
            $insurance_comp_str.=$ins_comp_arr[$inscoid].',';
        }
        
        $insurance_group_str = substr($insurance_group_str,0,-1);
        $insurance_comp_str = substr($insurance_comp_str,0,-1);

		if($duePicked=='1'){
			$mainResultArr[$eid]['insurance']['patientid']=$patient_id;
			$mainResultArr[$eid]['insurance']['patient_name']=$patient_name;
			$mainResultArr[$eid]['insurance']['section']='ar_aging';
			$mainResultArr[$eid]['insurance']['sub_section']=$dueFrom;
			$mainResultArr[$eid]['insurance']['encounter_id']=$eid;
			$mainResultArr[$eid]['insurance']['amount_due']+= $amount_due;
			$mainResultArr[$eid]['insurance']['date_of_service']=$qryRes[$i]['date_of_service'];
			$mainResultArr[$eid]['insurance']['ar_comment']=$qryRes[$i]['comment'];
			$mainResultArr[$eid]['insurance']['task_group']=$group_id_arr[$qryRes[$i]['gro_id']];
			$mainResultArr[$eid]['insurance']['task_group_id']=$qryRes[$i]['gro_id'];
            $mainResultArr[$eid]['insurance']['task_ins_group']=trim($insurance_group_str);
            $mainResultArr[$eid]['insurance']['task_ins_group_id']=$ins_group_ids;
			$mainResultArr[$eid]['insurance']['task_ins_comp']=trim($insurance_comp_str);
			$mainResultArr[$eid]['insurance']['task_ins_comp_id']=$ins_comp_ids;
			$mainResultArr[$eid]['insurance']['task_facility']=$pos_facilities_arr[$qryRes[$i]['facility_id']];
			$mainResultArr[$eid]['insurance']['task_facility_id']=$qryRes[$i]['facility_id'];
		}
		
		//PATIENT
		$duePicked=0;
		$amount_due=0;
		if($patDue>0){
			if($last_pat_dop_diff>=0){
				$duePicked=1;
				$dueFrom='patient';
				if($mainResultArr[$eid]['patient']['days_aged']){
					if($last_pat_dop_diff<$mainResultArr[$eid]['patient']['days_aged']){
						$mainResultArr[$eid]['patient']['days_aged']= $last_pat_dop_diff;
					}
				}else{
					$mainResultArr[$eid]['patient']['days_aged']= $last_pat_dop_diff;
				}					
				$amount_due+=$patDue;
			}else{
				$duePicked=1;
				$dueFrom='patient';
				if($mainResultArr[$eid]['patient']['days_aged']){

					if($dos_date_diff<$mainResultArr[$eid]['patient']['days_aged']){
						$mainResultArr[$eid]['patient']['days_aged']= $dos_date_diff;
					}
				}else{
					
					$mainResultArr[$eid]['patient']['days_aged']= $dos_date_diff;
				}	
				$amount_due+=$patDue;
			}
		}
        
        $primaryInsuranceCoId = trim($qryRes[$i]['primaryInsuranceCoId']);
		$secondaryInsuranceCoId = trim($qryRes[$i]['secondaryInsuranceCoId']);
		$tertiaryInsuranceCoId = trim($qryRes[$i]['tertiaryInsuranceCoId']);
        
        $ins_group_ids=array();
        $ins_comp_ids=array();
        $insurance_group_str='';
        $insurance_comp_str='';
        if($arrInsMapInsGroups[$primaryInsuranceCoId]!=0 && $arrInsMapInsGroups[$primaryInsuranceCoId]!='') {
            $ins_group_ids[]=$arrInsMapInsGroups[$primaryInsuranceCoId];
            $ins_comp_ids[]=$primaryInsuranceCoId;
        }
        if($arrInsMapInsGroups[$secondaryInsuranceCoId]!=0 && $arrInsMapInsGroups[$secondaryInsuranceCoId]!='') {
            $ins_group_ids[]=$arrInsMapInsGroups[$secondaryInsuranceCoId];
            $ins_comp_ids[]=$secondaryInsuranceCoId;
        }
        if($arrInsMapInsGroups[$tertiaryInsuranceCoId]!=0 && $arrInsMapInsGroups[$tertiaryInsuranceCoId]!='') {
            $ins_group_ids[]=$arrInsMapInsGroups[$tertiaryInsuranceCoId];
            $ins_comp_ids[]=$tertiaryInsuranceCoId;
        }

        $ins_group_ids=array_unique($ins_group_ids);
        foreach($ins_group_ids as $insgid) {
            $insurance_group_str.=$arrAllInsGroups[$insgid].',';
        }
        
        $ins_comp_ids=array_unique($ins_comp_ids);
        foreach($ins_comp_ids as $inscoid) {
            $insurance_comp_str.=$ins_comp_arr[$inscoid].',';
        }
        
        $insurance_group_str = substr($insurance_group_str,0,-1);
        $insurance_comp_str = substr($insurance_comp_str,0,-1);
        
		if($duePicked=='1'){
			$mainResultArr[$eid]['patient']['patientid']=$patient_id;
			$mainResultArr[$eid]['patient']['patient_name']=$patient_name;
			$mainResultArr[$eid]['patient']['section']='ar_aging';
			$mainResultArr[$eid]['patient']['sub_section']=$dueFrom;
			$mainResultArr[$eid]['patient']['encounter_id']=$eid;
			$mainResultArr[$eid]['patient']['amount_due']+= $amount_due;
			$mainResultArr[$eid]['patient']['date_of_service']=$qryRes[$i]['date_of_service'];
			$mainResultArr[$eid]['patient']['ar_comment']=$qryRes[$i]['comment'];
            $mainResultArr[$eid]['patient']['task_group']=$group_id_arr[$qryRes[$i]['gro_id']];
			$mainResultArr[$eid]['patient']['task_group_id']=$qryRes[$i]['gro_id'];
            $mainResultArr[$eid]['patient']['task_ins_group']=trim($insurance_group_str);
            $mainResultArr[$eid]['patient']['task_ins_group_id']=$ins_group_ids;
			$mainResultArr[$eid]['patient']['task_ins_comp']=trim($insurance_comp_str);
			$mainResultArr[$eid]['patient']['task_ins_comp_id']=$ins_comp_ids;
			$mainResultArr[$eid]['patient']['task_facility']=$pos_facilities_arr[$qryRes[$i]['facility_id']];
			$mainResultArr[$eid]['patient']['task_facility_id']=$qryRes[$i]['facility_id'];
		}		
		

	//}
}

assign_ar_aging_task_rules_to($mainResultArr);


