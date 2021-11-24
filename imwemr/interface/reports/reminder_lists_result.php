<?php

$printFile = true;
$scp_cpt = $_REQUEST["cptCodeId"];
$scp_dx = $_REQUEST["dxCodeId"];
$scp_dx10 = $_REQUEST["dxCodeId10"];
$add_tests = $_REQUEST["add_tests"];
$immunizId = $_REQUEST["immunizId"];
$age_srh = $_REQUEST["age"];
$comboProvider = $_REQUEST["comboProvider"];
$gender = $_REQUEST["gender"];
$txt_lab_name = $_REQUEST["txt_lab_name"];
$medications = $_REQUEST["medications"];
$allergies = $_REQUEST["allergies"];
$age_criteria = $_REQUEST['age_criteria'];
$lab_result = $_REQUEST['lab_result'];
$lab_criteria = $_REQUEST['lab_criteria'];
$repType = $_REQUEST['repType'];
$recallTemplatesListId=$_REQUEST['recallTemplatesListId'];

$GLOBALDATEFORMAT = $GLOBALS['date_format'];
$getSqlDateFormat= get_sql_date_format();


//get template html
if($recallTemplatesListId) {
	$recallData		= '';
	$recallTemplateQry 		= "SELECT * FROM recalltemplate WHERE recallLeter_id='".$recallTemplatesListId."'";
	$recallTemplateRes 		= imw_query($recallTemplateQry);
	$recallTemplateNumRow 	= imw_num_rows($recallTemplateRes);
	if($recallTemplateNumRow>0) {
		$recallTemplateRow 	= imw_fetch_array($recallTemplateRes);
		$recallData = stripslashes($recallTemplateRow['recallTemplateData']);	
	}
}

//getting report generator name
$report_generator_name = NULL;
if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
	$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
	$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
	$report_generator_name = strtoupper($report_generator_name);
}

// GET ALL FACILITIES	
$arrFac = array();	$allFacNames = array(); $allFacCities = array(); $allFacPAM = array();
$qry = "select id,name,city, pam_code from facility order by name";
$qryRes = array();
$recallTemplateRes=imw_query($qry);
while($recallTemplateRow=imw_fetch_array($recallTemplateRes)){
	$qryRes[] = $recallTemplateRow;
}
for($i=0;$i<count($qryRes);$i++){
	$id = $qryRes[$i]['id'];
	$allFacNames[$id] = $qryRes[$i]['name'];
	$pam = $qryRes[$i]['pam_code'];
	//if($pam =='' ) { $pam = '01'; }
	$allFacPAM[$id] = $pam;
	$allFacCities[$id] = $qryRes[$i]['city'];
}	

//MASTER ARRAY FOR FACILITY
$qry="select pos.pos_facility_id, pos.facilityPracCode, pos_tbl.pos_prac_code FROM pos_facilityies_tbl pos LEFT JOIN pos_tbl ON pos_tbl.pos_id=pos.pos_id";
$rs=imw_query($qry);
$arrAllPosFacility=array();
while($res=imw_fetch_assoc($rs)){
	$arrAllPosFacility[$res['pos_facility_id']]=$res['facilityPracCode'].' - '.$res['pos_prac_code'];
}unset($rs);

$qry="Select id, fac_prac_code FROM facility";
$rs=imw_query($qry);
$arrAllPosFacOfFacility=array();
while($res=imw_fetch_assoc($rs)){
	$arrAllPosFacOfFacility[$res['id']]=$res['fac_prac_code'];
}unset($rs);

// ADDRESS LABEL
$blIncludePatientAddress = false;
if(isset($_REQUEST['repType']) && $_REQUEST['repType'] == "address_labels"){
	$blIncludePatientAddress = true;
}

//changing date format
if(isset($_REQUEST['Start_date'])){
	$dtEffectiveDate = $_REQUEST['Start_date'];
	if($GLOBALDATEFORMAT = "dd-mm-yyyy" && $GLOBALDATEFORMAT != "")
	{
		list($d,$m,$y) = preg_split('/-/', $dtEffectiveDate);
		$dtDBEffectDate = $y.'-'.$m.'-'.$d;
	}
	else
	{
		list($m,$d,$y) = preg_split('/-/', $dtEffectiveDate);
		$dtDBEffectDate = $y.'-'.$m.'-'.$d;
	}
}
//changing date format
if(isset($_REQUEST['End_date'])){
	$dtEffectiveDate1 = $_REQUEST['End_date'];
	if($GLOBALDATEFORMAT = "dd-mm-yyyy" && $GLOBALDATEFORMAT != "")
	{
		list($d,$m,$y) = preg_split('/-/', $dtEffectiveDate1);
		$dtDBEffectDate1 = $y.'-'.$m.'-'.$d;
	}
	else
	{
		list($m,$d,$y) = preg_split('/-/', $dtEffectiveDate1);
		$dtDBEffectDate1 = $y.'-'.$m.'-'.$d;
	}
}
//START Age and Gender
$age_gender_arr = array();
if($age_srh != "" || $gender != ""){	
	if($gender){
		$pat_sex_whr = " and sex = '$gender'";
	}

	if(is_array($_REQUEST["comboFac"])){
		$comboFac = implode(',', $comboFac);
		$qryFac = imw_query("select pos_tbl.facility from pos_facilityies_tbl JOIN pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id where pos_facilityies_tbl.pos_facility_id in ($comboFac)");
		while($res = imw_fetch_array($qryFac)){
			$rowFac[] = $res['facility'];
		}
		$comboFacilityStr = implode(',', array_unique($rowFac));
		$facility_whr = " and sa.sa_facility_id IN ($comboFacilityStr) ";
	}
	
	if(is_array($comboProvider))
	{
		$comboProviderStr = implode(',', $comboProvider);
		$provider_whr = " and sa.sa_doctor_id IN ($comboProviderStr) ";
	}

	$qry = "select pd.id,pd.DOB from patient_data pd INNER JOIN schedule_appointments sa ON sa.sa_patient_id = pd.id  where 1=1 and sa_app_start_date between '$dtDBEffectDate' and '$dtDBEffectDate1' and sa_patient_app_status_id NOT IN(18, 203, 201) $pat_sex_whr $provider_whr $facility_whr group by pd.id order by lname,fname";
	$row_pat_data = array();
	$pat_data_Res=imw_query($qry);
	while($pat_data_Row=imw_fetch_array($pat_data_Res)){
		$row_pat_data[] = $pat_data_Row;
	}
	for($i=0;$i<count($row_pat_data);$i++){
		if($row_pat_data[$i]['DOB'] != '0000-00-00'){
			$patient_age = show_age($row_pat_data[$i]['DOB']);
			$age_pat = explode(" ",$patient_age);
		}
		if(trim($age_srh) != ''){
			if($age_criteria == 'greater' and $age_pat > $age_srh){
				$age_gender_arr[$row_pat_data[$i]['id']]=true;
			}else if($age_criteria == 'greater_equal' and $age_pat >= $age_srh){
				$age_gender_arr[$row_pat_data[$i]['id']]=true;
			}else if($age_criteria == 'equalsto' and $age_pat == $age_srh){
				$age_gender_arr[$row_pat_data[$i]['id']]=true;
			}else if($age_criteria == 'less_equal' and $age_pat <= $age_srh){
				$age_gender_arr[$row_pat_data[$i]['id']]=true;
			}else if($age_criteria == 'less' and $age_pat < $age_srh){
				$age_gender_arr[$row_pat_data[$i]['id']]=true;
			}
		}else{
			$age_gender_arr[$row_pat_data[$i]['id']]=true;
		}
	}
}

//Start cpt and dx code
$scp_cpt_fee_code_arr = array();
if(count($scp_cpt)>0){
	$cptCode_imp = implode(',',$scp_cpt);
	$cpt_query = imw_query("select cpt4_code,cpt_fee_id from cpt_fee_tbl where delete_status = '0' AND cpt_fee_id in($cptCode_imp)");
	$cptQryRes = array();
	while($cpt_query_Row=imw_fetch_array($cpt_query)){
		$cptQryRes[] = $cpt_query_Row;
	}
	for($i=0;$i<count($cptQryRes);$i++){
		$scp_cpt_fee_code_arr[] = $cptQryRes[$i]['cpt4_code'];
	}
}


$scp_dx_code_arr = array();
if(count($scp_dx)>0){
	$dxCode_imp = implode(',',$scp_dx);
	$dxCode_query = imw_query("select dx_code,diagnosis_id from diagnosis_code_tbl where diagnosis_id in($dxCode_imp)");
	$dxQryRes = array();
	while($dxCode_row=imw_fetch_array($dxCode_query)){
		$dxQryRes[] = $dxCode_row;
	}
	for($i=0;$i<count($dxQryRes);$i++){
		$scp_dx_code_arr[$dxQryRes[$i]['dx_code']] = $dxQryRes[$i]['dx_code'];
	}
}

//ICD10
$scp_dx10_code_arr=array();
$scp_dx_code10_imp='';
if(count($scp_dx10)>0){
	$scp_dx10=implode(',',$scp_dx10);
	$scp_dx_code10_imp = $scp_dx10;
	$scp_dx10=str_replace("'", '', $scp_dx10);
	$scp_dx10=explode(',', $scp_dx10);
	$scp_dx10_code_arr=array_combine($scp_dx10, $scp_dx10);
	$scp_dx10_code_imp=implode("','",array_unique($scp_dx10_code_arr));
}

$arr_in_cond = true ;
$scp_cpt_fee_code_imp = implode("','",array_unique($scp_cpt_fee_code_arr));
$scp_dx_code_imp = implode("','",array_unique($scp_dx_code_arr));

if($scp_dx || $scp_dx10){
	$dx_whr=" (";
	$andOR='';
	if($scp_dx){
		$dx_whr.="(pi.dx1 $in_not in('$scp_dx_code_imp') or pi.dx2 $in_not in('$scp_dx_code_imp') or
		pi.dx3 $in_not in('$scp_dx_code_imp') or pi.dx4 $in_not in('$scp_dx_code_imp'))";
		$andOR=' OR ';
	}
	if($scp_dx10){
		$dx_whr.=$andOR."(pi.dx1 $in_not in($scp_dx_code10_imp) or pi.dx2 $in_not in($scp_dx_code10_imp) or
		pi.dx3 $in_not in($scp_dx_code10_imp) or pi.dx4 $in_not in($scp_dx_code10_imp))";
	}	
	$dx_whr.=")";
}
if($scp_cpt){
	$cpt_whr=" pi.cptCode $in_not in('$scp_cpt_fee_code_imp')";
}
if($dx_whr && $cpt_whr){
	$whr_or=" or ";
}
$dx_array=array();
$cpt_array=array();

if($scp_dx!="" || $scp_dx10!="" || $scp_cpt!=""){
	$qrySelDx = "select sb.patientId,pi.dx1,pi.dx2,pi.dx3,pi.dx4,pi.cptCode  
				from 
				superbill as sb join procedureinfo as pi on
				pi.idSuperBill=sb.idSuperBill 
				where sb.postedStatus='0' 
				AND pi.delete_status = '0'
				and 
				($dx_whr $whr_or $cpt_whr ) $andDtRangeCptDxQry";						
	
	$sel_dx=imw_query($qrySelDx);
	while($row_dx=imw_fetch_array($sel_dx)){
		if($row_dx['dx1'] != '' and ($scp_dx_code_arr[$row_dx['dx1']] || $scp_dx10_code_arr[$row_dx['dx1']])){
			$dx_array[$row_dx['patientId']] = true;
		}
		if($row_dx['dx2'] != '' and ($scp_dx_code_arr[$row_dx['dx2']] || $scp_dx10_code_arr[$row_dx['dx2']])){
			$dx_array[$row_dx['patientId']] = true;
		}
		if($row_dx['dx3'] != '' and ($scp_dx_code_arr[$row_dx['dx3']] || $scp_dx10_code_arr[$row_dx['dx3']])){
			$dx_array[$row_dx['patientId']] = true;
		}
		if($row_dx['dx4'] != '' and ($scp_dx_code_arr[$row_dx['dx4']] || $scp_dx10_code_arr[$row_dx['dx4']])){
			$dx_array[$row_dx['patientId']] = true;
		}
		if($row_dx['cptCode']!='' and in_array($row_dx['cptCode'],$scp_cpt_fee_code_arr) == $arr_in_cond){
			$cpt_array[$row_dx['patientId']] = true;
		}
	}
	//exit;
	$arr_dx_codes=array_merge($scp_dx_code_arr, $scp_dx_code10_arr);
	if(count($arr_dx_codes) > 0){
		foreach($arr_dx_codes as $this_dx_code){
			if($this_dx_code!=''){
				$pl_qry = imw_query("SELECT pt_id FROM pt_problem_list where problem_name like '%".$this_dx_code."%' and status = 'Active'");
				if(imw_num_rows($pl_qry) > 0){
					$pl_arr=imw_fetch_array($pl_qry);
					if(is_array($pl_arr) && count($pl_arr) > 0){
						foreach($pl_arr as $this_pl){
							$dx_array[$this_pl["pt_id"]] = true;
						}
					}		
				}
				unset($pl_res);
			}
		}
	}
}

if($scp_dx || $scp_dx10){
	$dx_chl_whr=" (";
	$andOR='';
	if($scp_dx){
		$dx_chl_whr.=" (patient_charge_list_details.diagnosis_id1   $in_not in('$scp_dx_code_imp') 
		 or patient_charge_list_details.diagnosis_id4 $in_not in('$scp_dx_code_imp') 
		 or patient_charge_list_details.diagnosis_id3 $in_not in('$scp_dx_code_imp')
		 or patient_charge_list_details.diagnosis_id4 $in_not in('$scp_dx_code_imp')) ";
		$andOR=' OR ';
	}
	if($scp_dx10){
		$dx_chl_whr.=$andOR." (patient_charge_list_details.diagnosis_id1   $in_not in('$scp_dx10_code_imp') 
		 or patient_charge_list_details.diagnosis_id4 $in_not in('$scp_dx10_code_imp') 
		 or patient_charge_list_details.diagnosis_id3 $in_not in('$scp_dx10_code_imp')
		 or patient_charge_list_details.diagnosis_id4 $in_not in('$scp_dx10_code_imp')) ";	
	 }	
	$dx_chl_whr.=")";
}
if($scp_cpt){
	$cpt_chl_whr=" patient_charge_list_details.procCode $in_not in($cptCode_imp)";
}
if($dx_chl_whr!="" && $cpt_chl_whr!=""){
	$whr_chl_or=" or ";
}
if($dx_chl_whr || $cpt_chl_whr) {
	$cptDxQry = " and ".$dx_chl_whr.$whr_chl_or.$cpt_chl_whr;
}

$strFacIds = implode(",", $_REQUEST['comboFac']);
$strProvIds = implode(",", $_REQUEST['comboProvider']);
if($strFacIds) {
	$strFacIdsQry = " and patient_charge_list.facility_id in(".$strFacIds.") ";		
}
if($strProvIds) {
	$strProvIdsQry = " and patient_charge_list.primaryProviderId in(".$strProvIds.") ";		
}

$sel_chlist_qry="select  patient_charge_list_details.patient_id,
						patient_charge_list_details.diagnosis_id1,patient_charge_list_details.diagnosis_id2,
						patient_charge_list_details.diagnosis_id3,patient_charge_list_details.diagnosis_id4,
						patient_charge_list_details.procCode
					  from patient_charge_list_details  join patient_charge_list  on
					  patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id 
					  where 
					  patient_charge_list_details.newBalance>0 
					  and (patient_charge_list.date_of_service BETWEEN '".$dtDBEffectDate."' AND '".$dtDBEffectDate1."')
					  ".$strFacIdsQry.$strProvIdsQry."  
					  $cptDxQry 
					  $andDtRangeCptDxCHLQry
					  ";

$sel_chlist=imw_query($sel_chlist_qry);
while($row_chlist=@imw_fetch_array($sel_chlist)){
	if(($scp_dx == "") || ($row_chlist['diagnosis_id1'] !="" and ($scp_dx_code_arr[$row_chlist['diagnosis_id1']] || $scp_dx10_code_arr[$row_chlist['diagnosis_id1']]))){
		$dx_array[$row_chlist['patient_id']]=true;
	}
	if(($scp_dx == "") || ($row_chlist['diagnosis_id2'] !="" and ($scp_dx_code_arr[$row_chlist['diagnosis_id2']] || $scp_dx10_code_arr[$row_chlist['diagnosis_id2']]))){
		$dx_array[$row_chlist['patient_id']]=true;
	}
	if(($scp_dx == "") || ($row_chlist['diagnosis_id3'] !="" and ($scp_dx_code_arr[$row_chlist['diagnosis_id3']] || $scp_dx10_code_arr[$row_chlist['diagnosis_id3']]))){
		$dx_array[$row_chlist['patient_id']]=true;
	}
	if(($scp_dx == "") || ($row_chlist['diagnosis_id4'] !="" and ($scp_dx_code_arr[$row_chlist['diagnosis_id4']] || $scp_dx10_code_arr[$row_chlist['diagnosis_id4']]))){
		$dx_array[$row_chlist['patient_id']]=true;
	}
	if(($scp_cpt == "") || ($row_chlist['procCode'] !="" and in_array($row_chlist['procCode'],$scp_cpt) == $arr_in_cond)){
		$cpt_array[$row_chlist['patient_id']]=true;
	}
}
//ENd cpt and dx code

//Start medication 
$med_arr=array();
if($medications){
	$meqQry = imw_query("select pid,title from lists where title $simple_not = '$medications' and allergy_status = 'Active' and type in(1,4) $andMedicationRangeQry");
	$medQryRes = array();
	while($meqQry_row=imw_fetch_array($meqQry)){
		$medQryRes[] = $meqQry_row;
	}
	for($m=0;$m<count($medQryRes);$m++){
		$med_pid = $medQryRes[$m]['pid'];
		$med_arr[$med_pid]=true;
	}
}
//End medication 

//START ALLERGIES
$allerg_arr=array();
if($allergies) {
	$allerQry = imw_query("SELECT pid FROM lists WHERE title $simple_not = '$allergies' and type in (3,7) $andMedicationRangeQry");
	$allergyQryRes = array();
	while($allerQry_row=imw_fetch_array($allerQry)){
		$allergyQryRes[] = $allerQry_row;
	}
	for($i=0;$i<count($allergyQryRes);$i++){
		$all_pid = $allergyQryRes[$i]['pid'];
		$allerg_arr[$all_pid] = true;
	}
}
//END ALLERGIES

//START IMMUNIZATION
$immu_arr = array();
if(count($immunizId)>0) {
	$immunizId_imp = implode(',',$immunizId);
	if($immunizId_imp){
		$immunizQry = imw_query("SELECT patient_id FROM immunizations WHERE imnzn_id $in_not in($immunizId_imp) $andDtRangeImmunizQry");
		$immunizQryRes = array();
		while($immuniz_row=imw_fetch_array($immunizQry)){
			$immunizQryRes[] = $immuniz_row;
		}
		for($i=0;$i<count($immunizQryRes);$i++){
			$imm_pid = $immunizQryRes[$i]['patient_id'];
			$immu_arr[$imm_pid] = true;
		}
		
		$immu_arr_name = array();
		$immuniz_Qry = imw_query("SELECT imnzn_name FROM immunization_admin WHERE imnzn_id in($immunizId_imp)");
		$immuniz_Qry_Res = array();	
		while($immunizRow=imw_fetch_array($immunizQry)){
			$immuniz_Qry_Res[] = $immunizRow;
		}
		for($i=0;$i<count($immuniz_Qry_Res);$i++){
			$immu_arr_name[] = $immuniz_Qry_Res[$i]['imnzn_name'];
		}
	}
	
}
//END IMMUNIZATION

//START Lab
$lab_arr = array();
if(count($txt_lab_name)>0) {
	$lab_imp = implode("','",$txt_lab_name);
	
	if($lab_result != ''){
		if($lab_criteria == 'greater'){
			$lab_txt_whr = " and lab_results > '$lab_result'";
		}else if($lab_criteria == 'greater_equal'){
			$lab_txt_whr = " and lab_results >= '$lab_result'";
		}else if($lab_criteria == 'equalsto'){
			$lab_txt_whr = " and lab_results = '$lab_result'";
		}else if($lab_criteria == 'less_equal'){
			$lab_txt_whr = " and lab_results <= '$lab_result'";
		}else if($lab_criteria == 'less'){
			$lab_txt_whr = " and lab_results < '$lab_result'";
		}
	}
	$query = imw_query("SELECT lab_patient_id FROM lab_test_data WHERE lab_test_name $in_not in('$lab_imp') $lab_txt_whr $andLabRangeQry");
	$labQryRes = array();
	while($labRow=imw_fetch_array($query)){
		$labQryRes[] = $labRow;
	}
	for($i=0;$i<count($labQryRes);$i++){
		$lab_patient_id = $labQryRes[$i]['lab_patient_id'];
		$lab_arr[$lab_patient_id] = true;
	}	
}
//END Lab

//Start tests shown	
$final_test_arr=$add_tests;
$test_arr=array();
if($add_tests){
	if(in_array('VF',$final_test_arr) == $arr_in_cond){
		$query = imw_query("SELECT patientId,vf_id FROM vf where 1=1 $andExamDateRangeQry");
		$testQryRes= array();
		while($VFRow=imw_fetch_array($query)){
			$testQryRes[] = $VFRow;
		}
		for($i=0;$i<count($testQryRes);$i++){
			$test_arr[$testQryRes[$i]['patientId']] = true;
		}
	}
	
	if(in_array('HRT',$final_test_arr) == $arr_in_cond){
		$query = imw_query("SELECT patient_id,nfa_id FROM nfa where 1=1 $andExamDateRangeQry");
		$testQryRes= array();
		while($HRTRow=imw_fetch_array($query)){
			$testQryRes[] = $HRTRow;
		}
		for($i=0;$i<count($testQryRes);$i++){
			$test_arr[$testQryRes[$i]['patient_id']] = true;
		}	
	}
	
	if(in_array('OCT',$final_test_arr) == $arr_in_cond){
		$query = imw_query("SELECT patient_id,oct_id FROM oct where 1=1 $andExamDateRangeQry");
		$testQryRes= array();
		while($OCTRow=imw_fetch_array($query)){
			$testQryRes[] = $OCTRow;
		}
		for($i=0;$i<count($testQryRes);$i++){
			$test_arr[$testQryRes[$i]['patient_id']] = true;
		}	
	}
	
	if(in_array('Pachy',$final_test_arr) == $arr_in_cond){
		$query = imw_query("SELECT patientId,pachy_id FROM pachy where 1=1 $andExamDateRangeQry");
		$testQryRes= array();
		while($PachyRow=imw_fetch_array($query)){
			$testQryRes[] = $PachyRow;
		}
		for($i=0;$i<count($testQryRes);$i++){
			$test_arr[$testQryRes[$i]['patientId']] = true;
		}	
	}
	
	if(in_array('IVFA',$final_test_arr) == $arr_in_cond){
		$query = imw_query("SELECT patient_id,vf_id FROM ivfa where 1=1 $andExam_DateRangeQry");
		$testQryRes= array();
		while($IVFARow=imw_fetch_array($query)){
			$testQryRes[] = $IVFARow;
		}
		for($i=0;$i<count($testQryRes);$i++){
			$test_arr[$testQryRes[$i]['patient_id']] = true;
		}	
	}
	
	if(in_array('fundus',$final_test_arr) == $arr_in_cond){
		$query = imw_query("SELECT patientId,disc_id FROM disc where 1=1 $andExamDateRangeQry");
		$testQryRes= array();
		while($fundusRow=imw_fetch_array($query)){
			$testQryRes[] = $fundusRow;
		}
		for($i=0;$i<count($testQryRes);$i++){
			$test_arr[$testQryRes[$i]['patientId']] = true;
		}	
	}
	
	if(in_array('External',$final_test_arr) == $arr_in_cond || in_array('Anterior Photos',$final_test_arr) == $arr_in_cond){
		$query = imw_query("SELECT patientId,disc_id FROM disc_external where 1=1 $andExamDateRangeQry");
		$testQryRes= array();
		while($ExternalRow=imw_fetch_array($query)){
			$testQryRes[] = $ExternalRow;
		}
		for($i=0;$i<count($testQryRes);$i++){
			$test_arr[$testQryRes[$i]['patientId']] = true;
		}	
	}
	
	if(in_array('Topography',$final_test_arr) == $arr_in_cond){
		$query = imw_query("SELECT patientId,topo_id FROM topography where 1=1 $andExamDateRangeQry");
		$testQryRes= array();
		while($TopoRow=imw_fetch_array($query)){
			$testQryRes[] = $TopoRow;
		}
		for($i=0;$i<count($testQryRes);$i++){
			$test_arr[$testQryRes[$i]['patientId']] = true;
		}	
	}
	
	if(in_array('Ophthalmoscopy',$final_test_arr) == $arr_in_cond){
		$query = imw_query("SELECT patient_id,ophtha_id FROM ophtha where 1=1 $andExamDateRangeQry");
		$testQryRes= array();
		while($OphthaRow=imw_fetch_array($query)){
			$testQryRes[] = $OphthaRow;
		}
		for($i=0;$i<count($testQryRes);$i++){
			$test_arr[$testQryRes[$i]['patient_id']] = true;
		}
	}
}

$arr_mrg = array();
$arr_mrg_val = array();
$arr_mrg = array_merge($arr_mrg,array_keys($dx_array));
$arr_mrg = array_merge($arr_mrg,array_keys($cpt_array));
$arr_mrg = array_merge($arr_mrg,array_keys($med_arr));
$arr_mrg = array_merge($arr_mrg,array_keys($test_arr));
$arr_mrg = array_merge($arr_mrg,array_keys($allerg_arr));
$arr_mrg = array_merge($arr_mrg,array_keys($immu_arr));
$arr_mrg = array_merge($arr_mrg,array_keys($lab_arr));
$arr_mrg = array_merge($arr_mrg,array_keys($age_gender_arr));

if(count($scp_cpt_fee_code_arr)>0){
	$srh_cpt = implode(", ",array_unique($scp_cpt_fee_code_arr));
	if(count($scp_cpt_fee_code_arr)>29){
		$cpt_mar = count($scp_cpt_fee_code_arr)/29;
		$cpt_top_mar = $cpt_mar*4.5;
	}else{
		$cpt_top_mar = 5;
	}
}else{
	$srh_cpt = "None";
	$cpt_top_mar = 5;
}

$icd9And10=array_merge($scp_dx_code_arr, $scp_dx10_code_arr);
if(count($icd9And10)>0){
	$srh_dx = implode(", ",array_unique($icd9And10));
	if(count($icd9And10)>29){
		$dx_mar = count($icd9And10)/29;
		$dx_top_mar = $dx_mar*4.5;
	}else{
		$dx_top_mar = 5;
	}
}else{
	$srh_dx = "None";
	$dx_top_mar = 5;
}

if($medications){
	$srh_med = $medications;
}else{
	$srh_med = "None";
}
if($allergies){
	$srh_allergies = $allergies;
}else{
	$srh_allergies = "None";
}
if(count($immu_arr_name)>0){
	$srh_immu = implode(", ",array_unique($immu_arr_name));
}else{
	$srh_immu = "None";
}
if(count($txt_lab_name)>0){
	$srh_lab = implode(", ",array_unique($txt_lab_name));
}else{
	$srh_lab = "None";
}
if($add_tests){
	$srh_test=implode(", ",array_unique($add_tests));
}else{
	$srh_test="None";
}

//End tests shown

$arr_mrg_val = array_values(array_unique($arr_mrg));
$data = '';
$final_arr = array();
if(count($arr_mrg_val) > 0){
	for($j=0;$j<count($arr_mrg_val);$j++){
		$pat_id = $arr_mrg_val[$j];
		if($dx_array[$pat_id] == true || $scp_dx == ""){
			if($cpt_array[$pat_id] == true || $scp_cpt == ""){
				if($med_arr[$pat_id] == true || $medications == ""){
					if($allerg_arr[$pat_id] == true || $allergies == ""){
						if($immu_arr[$pat_id] == true || $immunizId == ""){
							if($test_arr[$pat_id] == true || $add_tests == ""){
								if($lab_arr[$pat_id] == true || $txt_lab_name == ""){
									$final_arr[] = $pat_id;
								}
							}
						}
					}	
				}
			}
		}
	}
	if(count($final_arr)>0){
		$pat_imp = implode(',',$final_arr);
		/*----UPDATING mur REMINDER LIST----*/
		$insert_query = "INSERT INTO patient_app_recall (procedure_id, recall_code, descriptions, patient_id, facility_id, recall_months, operator, recalldate, current_date1, procedure_name, reason) values";
		$str_insert_val = '';
		if(count($final_arr)>0){
			foreach($final_arr as $val){
				$str_insert_val .= "('0','','MUR_PATCH','".$val."', '0', '','".$_SESSION['authId']."', '".date('Y-m-d')."', '".date('Y-m-d H:i:s')."', '',''), ";
			}
		}
		if($str_insert_val != ''){
			$str_insert_val = substr($str_insert_val,0,-2);
			$insert_query .= $str_insert_val;
			//echo $insert_query;
			$insert_result = imw_query($insert_query);
		}
		/*----------------------------------*/
		$top_mar = 13;
		$data .= '
		<page backtop="'.$top_mar.'mm" backbottom="5mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>';
		$common_data.= '<table class="rpt rpt_table rpt_table-bordered rpt_padding" style="width: 100%;">
				<tr>
					 <td class="rptbx1" width="525" align="left">
						Reminder Lists Report 
					</td>
					<td class="rptbx2"  width="525" align="right" style="text-align:right;">
						Created By: '.$report_generator_name.' on '.date("".phpDateFormat()." h:i A").'&nbsp;
					</td>
				</tr>
			</table>';
		$disp_data.= $common_data;	
		$data.= $common_data;	
		
		$disp_data.='<table class="rpt_table rpt rpt_table-bordered rpt_padding"  style="width: 100%;">
				<tr height="15">';
		if($repType == 'send_email')
		{
			$disp_data.='<td style="width:20px" class="text_b_w" align="left"><input type="checkbox" name="check_all" id="check_all" onClick=""></td>';
		}
		$disp_data.='
				<td style="width:125px" class="text_b_w" align="left">Pt. Last Name</td>
				<td style="width:125px" class="text_b_w" align="left">Pt. First Name</td>
				<td style="width:100px" class="text_b_w" align="left">Pt. ID</td>
				<td style="width:140px" class="text_b_w" align="left">DOB(Age)</td>
				<td style="width:250px" class="text_b_w" align="left">Address</td>
				<td style="width:120px" class="text_b_w" align="left">Phone</td>';
		if($repType == 'send_email')
		{
			$disp_data.='
					<td style="width:200px" class="text_b_w" align="left">Email</td>';
		}
		$disp_data.='
					<td style="width:200px" class="text_b_w" align="left">Reminder Choices</td>
				</tr>
			</table>';
			
		$data.='<table class="rpt_table rpt rpt_table-bordered rpt_padding" bgcolor="#FFF3E8" style="width: 100%;">
				<tr height="15">
					<td style="width:125px" class="text_b_w" align="left">Pt. Last Name</td>
					<td style="width:125px" class="text_b_w" align="left">Pt. First Name</td>
					<td style="width:100px" class="text_b_w" align="left">Pt. ID</td>
					<td style="width:120px" class="text_b_w" align="left">DOB(Age)</td>
					<td style="width:250px" class="text_b_w" align="left">Address</td>
					<td style="width:120px" class="text_b_w" align="left">Phone</td>
					<td style="width:200px" class="text_b_w" align="left">Reminder Choices</td>
				</tr>
			</table>';	
		$data.='</page_header>';
		
		$disp_data.= '<table class="rpt_table rpt rpt_table-bordered rpt_padding" bgcolor="#FFF3E8" style="width: 100%;">';


		$hipaaVoice = '';	$strHouseData = '';
				
		if(trim($gender) != ''){
			$sex_whr = " and pd.sex = '$gender'";
		}
		if($repType == 'houseCalls'){
			$hipaaVoice = " AND pd.hipaa_voice = '1'";
		}
		
		if($repType == 'send_email'){
			$hipaaVoice = " AND pd.hipaa_email='1' AND pd.email<>''";
			if($excSentEmail)
			{
				//get ids with sent mail in date range
				$query=imw_query("select appt_id from exclude_sent_email where appt_date >= '$from_date' and appt_date <='$to_date' and report='Reminder Lists'");
				if(imw_num_rows($query)>=1)
				{
					while($data1=imw_fetch_object($query))
					{
						$apptIds[]=$data1->appt_id;
					}	
					$apptIdStr=implode(',',$apptIds);
				}
				
				if($apptIdStr)
				{
					//$hipaaVoice .= " AND schedule_appointments.id NOT IN($apptIdStr)";	
				}
			}
		}
		// GETTING FOLLOW UP PROCEDURE FOR HOUSE CALLS AND PAM
		$qry = imw_query("Select id, proc from slot_procedures WHERE proc = 'Follow Up'");
		$procQryRes=imw_fetch_array($qry);
		$procID = $procQryRes['id'];
		$procName = $procQryRes['proc'];
		//---------------------------------
					
		$qurey = imw_query("select pd.id,pd.fname, pd.lname, CONCAT(pd.lname,' ',pd.mname,', ',pd.fname) as `pat_name`,
					pd.DOB, pd.preferr_contact, DATE_FORMAT(pd.DOB, '%m-%d-%Y') as 'pt_dob', pd.street, pd.street2, pd.city, pd.state, pd.postal_code, 
					pd.phone_home, pd.phone_biz, pd.phone_cell, 
					pd.hipaa_mail,pd.hipaa_email,pd.hipaa_voice, pd.providerID, pd.default_facility, pd.email, us.fname as dFname, us.lname as dLname from  
					patient_data pd LEFT JOIN users us ON pd.providerID = us.id 
					where pd.id in ($pat_imp)
					$sex_whr $hipaaVoice order by lname,fname");

		$patientQryRes= array();
		while($queryRow=imw_fetch_array($qurey)){
			$patientQryRes[] = $queryRow;
		}			
		$i = 0;
		$arrAddLabels = array();	$arrHouseCalls = array();	$arrPAM = array();	
		$num=count($patientQryRes);
		}
		
		for($pi=0;$pi<count($patientQryRes);$pi++){
			$i++;	$docNameArr = array();
			$pat_id = $patientQryRes[$pi]['id'];
			$pat_fname = $patientQryRes[$pi]['fname'];
			$pat_lname = $patientQryRes[$pi]['lname'];
			if($patientQryRes[$pi]['DOB'] != '0000-00-00'){
				$age = show_age($patientQryRes[$pi]['DOB']);
				list($y,$m,$d) = explode('-',$patientQryRes[$pi]['DOB']);
				if($GLOBALDATEFORMAT = "dd-mm-yyyy" && $GLOBALDATEFORMAT != "")
				{
					$pat_dob = $d.'-'.$m.'-'.$y.' ('.$age.')';
				}
				else
				{
					$pat_dob = $m.'-'.$d.'-'.$y.' ('.$age.')';
				}
				//$pat_dob = $m.'-'.$d.'-'.$y.' ('.$age.')';
			}
			$street = $patientQryRes[$pi]['street'];
			$email=$patientQryRes[$pi]['email'];
			$phone_default = $patientQryRes[$pi]["phone_home"];
			$prefer_contact = $patientQryRes[$pi]["preferr_contact"];
			if($prefer_contact == 0)
			{
				if(trim($patientQryRes[$pi]["phone_home"]) != ""){$phone_default = $patientQryRes[$pi]["phone_home"]; }
			}
			else if($prefer_contact == 1)
			{
				if(trim($patientQryRes[$pi]["phone_biz"]) != ""){$phone_default = $patientQryRes[$pi]["phone_biz"]; }				
			}
			else if($prefer_contact == 2)
			{
				if(trim($patientQryRes[$pi]["phone_cell"]) != ""){$phone_default = $patientQryRes[$pi]["phone_cell"]; }				
			}			
			$phone_home = $phone_default;
			
			$phoneHome=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$patientQryRes[$pi]["phone_home"]))));
			$phone_cell=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$patientQryRes[$pi]["phone_cell"]))));
			$phone_biz=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$patientQryRes[$pi]["phone_biz"]))));
			
			$phone=($phoneHome!='') ?  $phoneHome :str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$phone_home))));
			$cell_phone= ($phone_cell!='') ?  $phone_cell :$phone_biz;
			if($phone==$cell_phone){ $cell_phone='';}
			
			if($pat_fname!="" && $pat_lname!=""){
				$pat_comma=', ';
			}
			
			$reminder_choices_arr = array();
			if($patientQryRes[$pi]['hipaa_mail'] == '1'){
				$reminder_choices_arr[]="Postal Mail";
			}
			if($patientQryRes[$pi]['hipaa_email'] == '1'){
				$reminder_choices_arr[]="Email";
			}
			if($patientQryRes[$pi]['hipaa_voice'] == '1'){
				$reminder_choices_arr[]="Voice";
			}
			$reminder_choices = "";
			if(count($reminder_choices_arr) > 0){
				$reminder_choices = implode(', ',$reminder_choices_arr);
			}			
			
			$pat_name = $pat_lname.$pat_comma.$pat_fname;
			$pag_break = 40-($cpt_mar+$dx_mar);
			$page_brk = round($pag_break);
			
			$facility = $patientQryRes[$pi]['default_facility'];

			// GETTING DEFAULT DOCTOR NAME
			$docNameArr["LAST_NAME"] = $patientQryRes[$pi]['dLname'];
			$docNameArr["FIRST_NAME"] = $patientQryRes[$pi]['dFname'];
			$docName = changeNameFormat($docNameArr);
			
				// STRING HOUSE CALLS
				if(in_array(strtolower($billing_global_server_name), array('arizonaeye'))){
					$strHouseData.= ''.$pat_name.''.'|'.''.$phone_home.''.'|'.''.$patientQryRes[$pi]['phone_cell'].''.'|'.''.'|'.''.'|'.''.$pat_id.''.'|'.
					''.$patientQryRes[$pi]['providerID'].''.'|'.''.$procID.''.'|'.''.$docName.''.'|'.''.$procName.''.'|'.''.$allFacCities[$facility].''.'|'.
					''.$patientQryRes[$pi]['street'].''.'|'.''.$patientQryRes[$pi]['city'].''.'|'.''.$patientQryRes[$pi]['state'].''.'|'.''.$patientQryRes[$pi]['postal_code'].''.'|'.''.$patientQryRes[$pi]['email'].'';
					$strHouseData.= "\n";
				} else{
					$strHouseData.= '"'.$pat_name.'"'.','.'"'.$phone_home.'"'.','.'"'.$patientQryRes[$pi]['phone_cell'].'"'.','.'""'.','.'""'.','.'"'.$pat_id.'"'.','.
					'"'.$patientQryRes[$pi]['providerID'].'"'.','.'"'.$procID.'"'.','.'"'.$docName.'"'.','.'"'.$procName.'"'.','.'"'.$allFacCities[$facility].'"'.','.
					'"'.$patientQryRes[$pi]['street'].'"'.','.'"'.$patientQryRes[$pi]['city'].'"'.','.'"'.$patientQryRes[$pi]['state'].'"'.','.'"'.$patientQryRes[$pi]['postal_code'].'"'.','.'"'.$patientQryRes[$pi]['email'].'"';
					$strHouseData.= "\n";
				}
				// ------------------	
				
				// PAM DATA
				if($facility <= 0){
					$rs=imw_query("Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$patientid."' 
					AND sa_app_start_date<='".date('Y-m-d')."' ORDER BY sa_app_start_date DESC LIMIT 0,1");
					$facRes=imw_fetch_array($rs);
					$facility= $facRes['sa_facility_id'];
					if($facility>0){
						$facility= $arrAllPosFacOfFacility[$facility];
					}
				}
				
				$facname = $arrAllPosFacility[$facility];
				if(empty($facname) == false){
					$facname = strtoupper(trim(array_shift(explode('-', $facname))));
				}
				
				$office_code="01";
				$pos_fac_city_key=array_search($facname,$GLOBALS['PAM2000']);
				if($pos_fac_city_key) $office_code=$pos_fac_city_key;
				
				$arrPAM[$i]['PATID'] = '"'.$pat_id.'"';
				$arrPAM[$i]['MESSAGE_TYPE'] = '"01"';
				$arrPAM[$i]['OFFICE'] = '"'.$office_code.'"';
				$arrPAM[$i]['LANGUAGE_TYPE'] = '"01"';
				$arrPAM[$i]['PATIENT_FNAME'] = '"'.$patientQryRes[$pi]['fname'].'"';
				$arrPAM[$i]['PATIENT_LNAME'] = '"'.$patientQryRes[$pi]['lname'].'"';
				$arrPAM[$i]['APP_DATE'] = '""';
				$arrPAM[$i]['APP_TIME'] = '""';
				$arrPAM[$i]['STATUS_OPERATOR_ID'] = '"'.$patientQryRes[$pi]['providerID'].'"';
				$arrPAM[$i]['PROCEDURE_ID'] = '"'.$procID.'"';
				$arrPAM[$i]['PHONE'] = '"'.$phone.'"';
				$arrPAM[$i]['EMAIL'] = '"'.$patientQryRes[$pi]['email'].'"';
				$arrPAM[$i]['CELL_PHONE'] = '"'.$cell_phone.'"';
				// END PAM DATA----
				
				// ARRAY FOR ADDRESS LABLES
				$patAddress = $patientQryRes[$pi]['street'].' '.$patientQryRes[$pi]['street2'].'<br>'.$patientQryRes[$pi]['city'].' '.$patientQryRes[$pi]['state'].' '.$patientQryRes[$pi]['postal_code'];
				$arrAddLabels[$pat_id]['NAME'] = $pat_name."-".$pat_id;
				$arrAddLabels[$pat_id]['ADDRESS'] = $patAddress;
				// END --------------------
				
				$printFile = true;
				$data .= '					
				<table class="rpt_table rpt rpt_table-bordered rpt_padding" bgcolor="#ffffff" style="width: 100%;">
					<tr>
						<td style="width:125px" class="text" valign="top" align="left">'.$patientQryRes[$pi]['lname'].'</td>
						<td style="width:125px" class="text" valign="top" align="left">'.$patientQryRes[$pi]['fname'].'</td>
						<td style="width:100px" class="text" valign="top" align="left">'.$pat_id.'</td>
						<td style="width:120px" class="text" valign="top" align="left">'.$pat_dob.'</td>
						<td style="width:250px" class="text" valign="top" align="left">'.$street.'</td>
						<td style="width:120px" class="text" valign="top" align="left">'.$phone_home.'</td>
						<td style="width:200px" class="text" valign="top" align="left">'.$reminder_choices.'</td>
					</tr>';
				$data .= '</table>';
				
				$disp_data .= '<tr>';
				if($repType == 'send_email')
				{
					$disp_data .= '	<td style="width:30px" valign="top" align="left"><input type="checkbox" id="pat_email_'.$i.'" name="pat_email[]" value="'.$pat_id.'" class="checkbox1"></td>';
				}
				$disp_data .= '
						<td style="width:125px" class="text" valign="top" align="left">'.$patientQryRes[$pi]['lname'].'</td>
						<td style="width:125px" class="text" valign="top" align="left">'.$patientQryRes[$pi]['fname'].'</td>
						<td style="width:100px" class="text" valign="top" align="left">'.$pat_id.'</td>
						<td style="width:140px" class="text_11 white" valign="top" align="left">&nbsp;'.$pat_dob.'</td>
						<td style="width:250px" class="text_11 white" valign="top" align="left">&nbsp;'.$street.'</td>
						<td style="width:120px" class="text_11 white" valign="top" align="left">&nbsp;'.$phone_home.'</td>';
				if($repType == 'send_email')
				{
						$disp_data .= '<td style="width:200px" class="text_11 white" valign="top" align="left">&nbsp;'.$email.'</td>';
				}
				$disp_data .= '<td style="width:200px" class="text_11 white" valign="top" align="left">&nbsp;'.$reminder_choices.'</td>
					</tr>';
			//}

		}
		if(empty($data) == false) $data .= '</page>';
		$disp_data.='</table>';
		
		// ADD ADDRESS LABELS
		$strHTML_Label = '';
		if($blIncludePatientAddress == true){
			$strHTML_Label_Head = '';
			$num = sizeof($arrAddLabels);
			$p =0;	$l = 0;
			$width = "275px";
			$strHTML_Label_St.='<page backtop="10mm" backbottom="10mm">';
			$strHTML_Label_Footer= '<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>';
			$strHTML_Label_Head.='<page_header>';
			$strHTML_Label_Head.='<table class="rpt_table rpt rpt_table-bordered rpt_padding">				
					<tr>
						<td width="760" class="text_b_w" align="left">Address Labels:</td>
					</tr>						
				</table>
				</page_header>';
			$strHTML_Label_Head.= "
			<table class=\"rpt_table rpt rpt_table-bordered rpt_padding\"><tr>";
			
			foreach($arrAddLabels as $patId => $patAdd)
			{
				$strHTML_Label.= "
				<td valign=\"top\" width=\"250\"><br><br>
					<table align=\"left\"  height=\"100%\" border=\"1\" rules=\"rows\"  cellpadding=\"2\" cellspacing=\"0\" width=\"".$width."\">
						<tr>
							<td width=\"180\" align=\"left\" valign=\"middle\" class=\"text_13b\">".$patAdd['NAME']."</TD>
						</tr>
						<tr>
						<td width=\"180\" valign=\"middle\" align=\"left\" class=\"text_13\">";
				
						if($patAdd['ADDRESS'] <> ""){ 
							$strHTML_Label .= $patAdd['ADDRESS'];
						}
						
						$strHTML_Label.= "
						</td>
						</tr>
					</table>
				</td>";
				$l++;
				$p++;
				$break = '';
				if($p >= 3){
					$break = "</tr><tr>";
					$p=0;
				}
				if($l == $num){
					$break = "</tr>";
				}
				$strHTML_Label .= $break;
				
			}
			
			if(empty($strHTML_Label) == false){
				$strHTML_Label = $strHTML_Label_Head.$strHTML_Label;
				$strHTML_Label .= "</table></page>";
				$data.=$strHTML_Label_St.$strHTML_Label_Footer.$strHTML_Label;
				$disp_data.="<br><br>".$strHTML_Label;
			}
			
		}
		//----------END LABELS-------
		if($repType =='houseCalls'){
			if(in_array(strtolower($billing_global_server_name), array('arizonaeye'))){
				$strHouseCalls = "Patient Name|Patient Home Phone|Patient Mobile Number|Appointment Date|Appointment Time|Patient Account Number|Doctor Number|Procedure Number|Doctor Name|Procedure Name|Location (office) Name|Patient Address|Patient City|Patient State|Patient Zip Code|Patient Email Address";
			
			} else{
				$strHouseCalls = "Patient Name,Patient Home Phone,Patient Mobile Number,Appointment Date,Appointment Time,Patient Account Number,Doctor Number,Procedure Number,Doctor Name,Procedure Name,Location (office) Name,Patient Address,Patient City,Patient State,Patient Zip Code,Patient Email Address";
			}
			$strHouseCalls.="\n".$strHouseData; 
			
	
			$filename = data_path().'users/UserId_'.$_SESSION['authId'].'/reminder.txt';
			$fileInfo = pathinfo($filename);
			if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
			$fp=fopen($filename,'w');
			@fwrite($fp,$strHouseCalls);
			@fclose($fp);
		}
		
		if($repType=='pam'){
			$exceltext="";
			$exceltext ='Account-ID,Message Type,Office,Language Type,Patient Fname,Patient Lname,App Date,App Time,Provider,App Type,Home Phone,Email,Cell Phone';
			$exceltext.="\n";
			for($k=0;$k<count($arrPAM);$k++)
			{
				$exceltext.= implode(",",$arrPAM[$k]);
				$exceltext.="\n";
			}
			
			$filename = data_path().'users/UserId_'.$_SESSION['authId'].'/reminder.txt';
			$fileInfo = pathinfo($filename);
			if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
			$fp=fopen($filename,'w');
			@fwrite($fp,$exceltext);
			@fclose($fp);
		}
	}
if($printFile == true && empty($data) == false){
	$styleHTML = '<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
	$page_content = <<<DATA
		$styleHTML
		$data
DATA;
} else{
	echo '<div class="text-center alert alert-info">No Record Exists.</div>';
} 
$file_location = write_html($page_content);

if($printFile == true and trim($data) != ''){
	echo $disp_data;
}
?>