<?php
$getSqlDateFormat=get_sql_date_format();
$currency = showCurrency();
$curDate = date(phpDateFormat());
if(empty($Start_date) === true){
	$Start_date = $curDate;
	$End_date = $curDate;
}
$printFile = true;
$csvFileData = NULL;


if ($_POST['form_submitted']) {
	$pdfData = NULL;
	$printFile = false;

	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$curDate = date(phpDateFormat().' h:i A');	


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
	
	//--- CHANGE DATE FORMAT ---
	$start_date = getDateFormatDB($Start_date);
	$end_date = getDateFormatDB($End_date);
	
	$arr_physicians=$physician;
	$physicians = implode(',',$physician);
	$facilities = implode(',',$facility_id);
	
	//GET ALL SCHEDULE FACILITIES
	$fac_query = "select id,name from facility order by name";
	$fac_query_res = imw_query($fac_query);
	$arr_all_sch_fac_arr = array();
	while ($fac_res = imw_fetch_array($fac_query_res)) {
		$fac_id = $fac_res['id'];
		$arr_all_sch_fac_arr[$fac_id]=addslashes($fac_res['name']);
	}

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	$providerNameArr[0] = 'No Provider';
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}

	//GET TEST NAMES WHICH ARE UNDER OTHER CATEGORY
	$qry = "SELECT id, temp_name FROM tests_name where test_type='1'";
	$rs=imw_query($qry);
	$arr_other_test_id=array();
	$arr_other_test_names=array();
	while($res=imw_fetch_assoc($rs)){
		$arr_other_test_id[$res['temp_name']]=$res['id'];
		$arr_other_test_names[$res['id']]=$res['temp_name'];
	}
	
	//SELECTED TEST NAMES
	$selTestCounts=sizeof($test_names);
	$arr_sel_test_names=array();
	if($selTestCounts>0){
		$arr_sel_test_names=array_combine($test_names,$test_names);
	}

	//LIST OF ALL Tests
	$arr_all_tests=array();
	
	
	// GET REPORT DATA
	$columnsArr = array();
	$none_charge_list = array();
	$get_charge_list_id = array();
	$printFile = false;
	$sortByPayment = NULL;
	$curDate = date(phpDateFormat().' h:i A');
	
	//--- GET ALL RESULTS FROM PATIENT CHARGE LIST TABLE ---------
	//IVFA
	if($selTestCounts<=0 || $arr_sel_test_names['IVFA']){
		$qry = "Select ivfa.vf_id as 'test_id', ivfa.patient_id, DATE_FORMAT(ivfa.exam_date, '$getSqlDateFormat') as 'DOS', ivfa.phy as physicianId, ivfa.performed_by technicianId, patient_data.fname, patient_data.mname, patient_data.lname FROM ivfa 
		LEFT JOIN patient_data ON patient_data.id = ivfa.patient_id
		WHERE (ivfa.exam_date BETWEEN '$start_date' AND '$end_date')";
		//if(empty($physicians)==false){
		if(sizeof($arr_physicians)>0){
			$qry.=" AND ivfa.phy IN(".$physicians.")";
		}
		$qry.=" GROUP BY ivfa.vf_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='IVFA';
			$res['testType_sp'] = 'IVFA';
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['IVFA'][$res['test_id']]=$res['test_id'];
		}
	}

	//--- VF -----
	if($selTestCounts<=0 || $arr_sel_test_names['VF']){
		$qry = "Select vf.vf_id as 'test_id', vf.patientId as patient_id, DATE_FORMAT(vf.examTime, '$getSqlDateFormat %h:%i') testDate, vf.phyName as physicianId, vf.performedBy as technicianId, DATE_FORMAT(vf.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM vf 
		LEFT JOIN patient_data ON patient_data.id = vf.patientId
		WHERE (vf.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND vf.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY vf.vf_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='VF';
			$res['testType_sp'] = 'VF';
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['VF'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- VF-GL -----
	if($selTestCounts<=0 || $arr_sel_test_names['VF-GL']){
		$qry = "Select vf_gl.vf_gl_id as 'test_id', vf_gl.patientId as patient_id, DATE_FORMAT(vf_gl.examTime, '$getSqlDateFormat %h:%i') testDate, vf_gl.phyName as physicianId, vf_gl.performedBy as technicianId, DATE_FORMAT(vf_gl.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  vf_gl 
		LEFT JOIN patient_data ON patient_data.id = vf_gl.patientId
		WHERE (vf_gl.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND vf_gl.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY vf_gl.vf_gl_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='VF-GL';
			$res['testType_sp'] = 'VF-GL';
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['VF-GL'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- ICG -----
	if($selTestCounts<=0 || $arr_sel_test_names['ICG']){
		$qry = "Select icg.icg_id as 'test_id',icg.patient_id as patient_id, DATE_FORMAT(icg.examTime, '$getSqlDateFormat %h:%i') testDate, icg.phy as physicianId, icg.performed_by as technicianId, DATE_FORMAT(icg.exam_date, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  icg 
		LEFT JOIN patient_data ON patient_data.id = icg.patient_id
		WHERE (icg.exam_date BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND icg.phy IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY icg.icg_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='ICG';
			$res['testType_sp'] = 'ICG';
			
			$arrMainResults[$res['DOS']][]= $res;
			$arrTemp['ICG'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- TOPOGRAPHY -----
	if($selTestCounts<=0 || $arr_sel_test_names['Topography']){
		$qry = "Select topography.topo_id as 'test_id',topography.patientId as patient_id, DATE_FORMAT(topography.examTime, '$getSqlDateFormat %h:%i') testDate, topography.phyName as physicianId, topography.performedBy as technicianId, DATE_FORMAT(topography.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  topography 
		LEFT JOIN patient_data ON patient_data.id = topography.patientId
		WHERE (topography.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND topography.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY topography.topo_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='Topography';
			$res['testType_sp'] = 'Topography';
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['Topography'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- PACHY -----
	if($selTestCounts<=0 || $arr_sel_test_names['Pachy']){
		$qry = "Select pachy.pachy_id as 'test_id', pachy.patientId as patient_id, DATE_FORMAT(pachy.examTime, '$getSqlDateFormat %h:%i') testDate, pachy.phyName as physicianId, pachy.performedBy as technicianId, DATE_FORMAT(pachy.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  pachy 
		LEFT JOIN patient_data ON patient_data.id = pachy.patientId
		WHERE (pachy.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND pachy.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY pachy.pachy_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='Pachy';
			$res['testType_sp'] = 'Pachy';
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['Pachy'][$res['test_id']]=$res['test_id'];
		}		
	}
	
	//--- OCT -----
	if($selTestCounts<=0 || $arr_sel_test_names['OCT']){
		$qry = "Select oct.oct_id as 'test_id', oct.patient_id as patient_id, DATE_FORMAT(oct.examTime, '$getSqlDateFormat %h:%i') testDate, oct.phyName as physicianId, oct.performBy as technicianId, DATE_FORMAT(oct.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  oct 
		LEFT JOIN patient_data ON patient_data.id = oct.patient_id
		WHERE (oct.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND oct.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY oct.oct_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='OCT';
			$res['testType_sp'] = 'OCT';	
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['OCT'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- OCT-RNFL -----
	if($selTestCounts<=0 || $arr_sel_test_names['OCT-RNFL']){	
		$qry = "Select oct_rnfl.oct_rnfl_id as 'test_id', oct_rnfl.patient_id as patient_id, DATE_FORMAT(oct_rnfl.examTime, '$getSqlDateFormat %h:%i') testDate, oct_rnfl.phyName as physicianId, oct_rnfl.performBy as technicianId, DATE_FORMAT(oct_rnfl.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  oct_rnfl 
		LEFT JOIN patient_data ON patient_data.id = oct_rnfl.patient_id
		WHERE (oct_rnfl.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND oct_rnfl.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY oct_rnfl.oct_rnfl_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='OCT-RNFL';
			$res['testType_sp'] = 'OCT-RNFL';	
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['OCT-RNFL'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- LABORATORIES -----
	if($selTestCounts<=0 || $arr_sel_test_names['Laboratories']){
		$qry = "Select test_labs.test_labs_id as 'test_id', test_labs.patientId as patient_id, test_labs.phyName as physicianId, test_labs.performedBy as technicianId, DATE_FORMAT(test_labs.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  test_labs 
		LEFT JOIN patient_data ON patient_data.id = test_labs.patientId
		WHERE (test_labs.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND test_labs.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY test_labs.test_labs_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='Laboratories';
			$res['testType_sp'] = 'Labs';	
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['Labs'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- HRT -----
	if($selTestCounts<=0 || $arr_sel_test_names['HRT']){	
		$qry = "Select nfa.nfa_id as 'test_id', nfa.patient_id, nfa.phyName as physicianId, nfa.performBy as technicianId, DATE_FORMAT(nfa.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  nfa 
		LEFT JOIN patient_data ON patient_data.id = nfa.patient_id
		WHERE (nfa.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND nfa.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY nfa.nfa_id ORDER BY patient_data.lname";
		$rs=imw_query($qry) or die(imw_error());
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='HRT';
			$res['testType_sp'] = 'HRT';	
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['HRT'][$res['test_id']]=$res['test_id'];
		}
	}	
	
	//--- GDX -----
	if($selTestCounts<=0 || $arr_sel_test_names['GDX']){
		$qry = "Select test_gdx.gdx_id as 'test_id', test_gdx.patient_id as patient_id, DATE_FORMAT(test_gdx.examTime, '$getSqlDateFormat %h:%i') testDate, test_gdx.phyName as physicianId, test_gdx.performBy as technicianId, DATE_FORMAT(test_gdx.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  test_gdx 
		LEFT JOIN patient_data ON patient_data.id = test_gdx.patient_id
		WHERE (test_gdx.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND test_gdx.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY test_gdx.gdx_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='GDX';
			$res['testType_sp'] = 'GDX';	
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['GDX'][$res['test_id']]=$res['test_id'];
		}				
	}
	
	//--- FUNDUS -----
	if($selTestCounts<=0 || $arr_sel_test_names['Fundus']){
		$qry = "Select disc.disc_id as 'test_id', disc.patientId as patient_id, DATE_FORMAT(disc.examTime, '$getSqlDateFormat %h:%i') testDate, disc.phyName as physicianId, disc.performedBy as technicianId, 
		DATE_FORMAT(disc.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  disc  
		LEFT JOIN schedule_appointments ON schedule_appointments.sa_patient_id=disc.patientId 
		LEFT JOIN patient_data ON patient_data.id = disc.patientId
		WHERE (disc.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND disc.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY disc.disc_id ORDER BY patient_data.lname";
		
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='Fundus';
			$res['testType_sp'] = 'Fundus';	
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['Fundus'][$res['test_id']]=$res['test_id'];
		}
	}	
	
	//--- EXTERNAL/ANTERIOR -----
	if($selTestCounts<=0 || $arr_sel_test_names['External/Anterior']){
		$qry = "Select disc_external.disc_id as 'test_id', disc_external.patientId as patient_id, DATE_FORMAT(disc_external.examTime, '$getSqlDateFormat %h:%i') testDate, disc_external.phyName as physicianId, disc_external.performedBy as technicianId, DATE_FORMAT(disc_external.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  disc_external 
		LEFT JOIN patient_data ON patient_data.id = disc_external.patientId
		WHERE (disc_external.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND disc_external.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY disc_external.disc_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='External/Anterior';
			$res['testType_sp'] = 'External';	
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['External'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- CELL COUNT -----
	if($selTestCounts<=0 || $arr_sel_test_names['Cell Count']){
		$qry = "Select test_cellcnt.test_cellcnt_id as 'test_id', test_cellcnt.patientId as patient_id, DATE_FORMAT(test_cellcnt.examTime, '$getSqlDateFormat') testDate, test_cellcnt.phyName as physicianId, test_cellcnt.performedBy as technicianId, DATE_FORMAT(test_cellcnt.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  test_cellcnt 
		LEFT JOIN patient_data ON patient_data.id = test_cellcnt.patientId
		WHERE (test_cellcnt.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND test_cellcnt.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY test_cellcnt.test_cellcnt_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='Cell Count';
			$res['testType_sp'] = 'CellCount';		
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['CellCount'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- IOL-MASTER -----
	if($selTestCounts<=0 || $arr_sel_test_names['IOL Master']){
		$qry = "Select test.iol_master_id as 'test_id', test.patient_id, test.signedById as physicianId, DATE_FORMAT(test.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM iol_master_tbl test  
		LEFT JOIN patient_data ON patient_data.id = test.patient_id
		WHERE (test.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND test.signedById IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY test.iol_master_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$res['testType']='IOL Master';
			$res['testType_sp'] = 'iOLMaster';		
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['iOLMaster'][$res['test_id']]=$res['test_id'];
		}
	}	
	
	//--- B-SCAN -----
	if($selTestCounts<=0 || $arr_sel_test_names['B-Scan']){
		$qry = "Select test_bscan.test_bscan_id as 'test_id', test_bscan.patientId as patient_id, DATE_FORMAT(test_bscan.examTime, '$getSqlDateFormat %h:%i') testDate, test_bscan.phyName as physicianId, test_bscan.performedBy as technicianId, DATE_FORMAT(test_bscan.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM   test_bscan 
		LEFT JOIN patient_data ON patient_data.id = test_bscan.patientId
		WHERE (test_bscan.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND test_bscan.phyName IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY test_bscan.test_bscan_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$res['testType']='B-Scan'; 
			$res['testType_sp'] = 'B-Scan';
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['B-Scan'][$res['test_id']]=$res['test_id'];
		}
	}
	
	//--- A-SCAN -----
	if($selTestCounts<=0 || $arr_sel_test_names['A/Scan']){
		$qry = "Select surgical_tbl.surgical_id as 'test_id', surgical_tbl.surgical_id,surgical_tbl.patient_id as patient_id, DATE_FORMAT(surgical_tbl.examTime, '$getSqlDateFormat %h:%i') testDate, signedById as physicianId, DATE_FORMAT(surgical_tbl.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  surgical_tbl 
		LEFT JOIN patient_data ON patient_data.id = surgical_tbl.patient_id
		WHERE (surgical_tbl.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND surgical_tbl.signedById IN(".$physicians.")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY surgical_tbl.surgical_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];
			$facId = $res['facility_id'];
			$res['testType']='A-Scan'; 
			$res['testType_sp'] = 'A-Scan';
			
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp['A-Scan'][$res['test_id']]=$res['test_id'];
		}	
	}


	
	//--- OTHER -----
	if($selTestCounts<=0 || $arr_sel_test_names['Other'] || $arr_sel_test_names['Tear Lab'] || $arr_sel_test_names['Optomap'] || $arr_sel_test_names['Spindel Test']){	
		$qry = "Select test_other.test_other_id as 'test_id', test_other.patientId as patient_id, test_other.test_template_id, test_other.test_other, DATE_FORMAT(test_other.examTime, '$getSqlDateFormat %h:%i') testDate, test_other.phyName as physicianId, test_other.performedBy as technicianId, DATE_FORMAT(test_other.examDate, '$getSqlDateFormat') as DOS, patient_data.fname, patient_data.mname, patient_data.lname FROM  test_other 
		LEFT JOIN patient_data ON patient_data.id = test_other.patientId
		WHERE (test_other.examDate BETWEEN '$start_date' AND '$end_date')";
		if(sizeof($arr_physicians)>0){
			$qry.=" AND test_other.phyName IN(".$physicians.")";
		}
		if($arr_sel_test_names['Other'] || $arr_sel_test_names['Tear Lab'] || $arr_sel_test_names['Optomap'] || $arr_sel_test_names['Spindel Test']){
			$andor="";
			$qry.=" AND (";
			if($arr_sel_test_names['Other']){ $qry.=$andor." test_other.test_other!='TemplateTests'"; $andor='OR';}
			if($arr_sel_test_names['Tear Lab']){ $qry.=$andor." test_other.test_template_id='".$arr_other_test_id['Tear Lab']."'"; $andor='OR';}
			if($arr_sel_test_names['Optomap']){ $qry.=$andor." test_other.test_template_id='".$arr_other_test_id['Optomap']."'"; $andor='OR';}
			if($arr_sel_test_names['Spindel Test']){ $qry.=$andor." test_other.test_template_id='".$arr_other_test_id['Spindel Test']."'";}
			$qry.=")";
		}
		$qry.=" AND LOWER(patient_data.lname)!='doe' GROUP BY test_other.test_other_id ORDER BY patient_data.lname";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$phyId = $res['physicianId'];			
			$facId = $res['facility_id'];
			$testTempId = $res['test_template_id'];
			$test_type='Other';
			if($res['test_other'] == 'TemplateTests' && $testTempId >0){
				$res['testType'] = $arr_other_test_names[$testTempId];
				$res['testType_sp'] = 'TemplateTests';
				$test_type='TemplateTests';
			}else{
				$res['testType'] = $res['test_other'];
				$res['testType_sp'] = 'Other';
			}
			$arrMainResults[$res['DOS']][] = $res;
			$arrTemp[$test_type][$res['test_id']]=$res['test_id'];
		}	
	}
	//--------------------------------------------------

	//CHECK SUPERBILLS FOR ALL FETCHED TESTS
	if(sizeof($arrTemp)>0){
		$arr_tests_superbills=array();
		$arr_superbills_posted=array();
		foreach($arrTemp as $test_name => $arrTestIds){
			$strTestIds=implode(',', $arrTestIds);
			
			$qry="Select idSuperBill, test_id, postedStatus FROM superbill WHERE test_name='".$test_name."' AND test_id IN(".$strTestIds.") AND del_status<='0'";
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$arr_tests_superbills[$test_name][$res['test_id']]=$res['idSuperBill'];
				$arr_superbills_posted[$test_name][$res['test_id']]=$res['postedStatus'];
			}
		}
	}
	
	//SORTING OF MAIN DATA
	if(sizeof($arrMainResults)>0){
		$arrDOS=array_keys($arrMainResults);
		sort($arrDOS);
		$tempData= $arrMainResults;
		unset($arrMainResults);
		
		foreach($arrDOS as $examDate){
			foreach($tempData[$examDate] as $key =>$data){
				$arrMainResults[]=$data;
			}
		}
		unset($arrDOS);
		unset($tempData);
	}

	
	//OUTPUT CREATION
	$physician_name = $CLSReports->report_display_selected($physicians,'physician',1, $allPhyCount);
	
	$op = 'p';
	$main_provider_arr=array();
	//--- GET ADJUSTMENT AMOUNT ---

	//MAKING OUTPUT DATA
	$file_name="unbilled_tests_".time().".csv";
	$csv_file_name= write_html("", $file_name);

	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');

	$arr=array();
	$arr[]='Unbilled Tests Report';
	$arr[]="Exam From ".$Start_date." To ".$End_date;
	$arr[]="Created by: $op_name on $curDate";
	fputcsv($fp,$arr, ",","\"");
	$arr=array();
	$arr[]="Sel Physician :".$physician_name;
	fputcsv($fp,$arr, ",","\"");

	$arr=array();
	$arr[]="Patient Name-ID";
	$arr[]="Exam Date";
	$arr[]="Test";
	$arr[]="Physician";
	$arr[]="Superbill";
	fputcsv($fp,$arr, ",","\"");
	
	$page_header_val = '
	<table style="width:100%" class="rpt_table rpt_table-bordered rpt_padding">
			<tr>
				<td class="rptbx1" style="width:33%;">Unbilled Tests Report</td>
				<td class="rptbx2" style="width:33%;">Exam From '.$Start_date.' To '.$End_date.'</td>
				<td class="rptbx3" style="width:34%;">Created by: '.$op_name.' on '.$curDate.'</td>
			</tr>
			<tr>
				<td class="rptbx1">Sel Phy.: '.$physician_name.'</td>
				<td class="rptbx2"></td>
				<td class="rptbx3"></td>
			</tr>
	</table>';
	
	$countRes=$countSuperbills=$countNoSuperbills=0;	
	foreach($arrMainResults as $detail_data){
		$test_id=$detail_data['test_id'];
		$test_name_sp=$detail_data['testType_sp'];
		
		//IF NOT POSTED/PROCESSED THEN MAKE OUTPUT
		if($arr_superbills_posted[$test_name_sp][$test_id]!='1'){
			$printFile=true;	

			$pat_name = core_name_format($detail_data['lname'], $detail_data['fname'], $detail_data['mname']).' - '.$detail_data['patient_id'];
			
			$if_superbill= ($arr_tests_superbills[$test_name_sp][$detail_data['test_id']]) ? 'Yes' : 'No';
			
			
			//--- PDF FILE DATA ----
			$pdfData2 .='<tr>
				<td class="text_12" bgcolor="#FFFFFF" width="20%">'.$pat_name.'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="left" width="10%">'.$detail_data['DOS'].'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="left" width="20%">'.$detail_data['testType'].'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="15%" align="left">'.$providerNameArr[$detail_data['physicianId']].'</td>
				<td class="text_12" bgcolor="#FFFFFF" width="10%" align="left">'.$if_superbill.'</td>
			</tr>';
			
			$csvFileData2 .='<tr>
				<td class="text_12" bgcolor="#FFFFFF" style="width:30%">'.$pat_name.'</td>
				<td class="text_12" bgcolor="#FFFFFF" align="left" style="width:10%">'.$detail_data['DOS'].'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:25%">'.$detail_data['testType'].'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:25%; align:left">'.$providerNameArr[$detail_data['physicianId']].'</td>
				<td class="text_12" bgcolor="#FFFFFF" style="width:10%" style="text-align:left;">'.$if_superbill.'</td>
			</tr>';	

			//FOR CSV
			$arr=array();
			
			$arr[]=$pat_name;
			$arr[]=$detail_data['DOS'];
			$arr[]=$detail_data['testType'];
			$arr[]=$providerNameArr[$detail_data['physicianId']];
			$arr[]=$if_superbill;
			fputcsv($fp,$arr, ",","\"");				
			
			if($if_superbill=='Yes')$countSuperbills++;else$countNoSuperbills++;
			$countRes++;
		}

	}
	

	
	$pdfData = '
	<page backtop="15mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>		
	<page_header>     
		'.$page_header_val.'
		<table style="width:100%;" class="rpt_table rpt_table-bordered">		
			<tr>
				<td class="text_b_w" align="center" style="width:30%">Patient Name-ID</td>
				<td class="text_b_w" align="center" style="width:10%">Exam Date</td>
				<td class="text_b_w" align="center" style="width:25%">Test</td>
				<td class="text_b_w" align="center" style="width:25%">Physician</td>
				<td class="text_b_w" align="center" style="width:10%">Superbill</td>
			</tr>
		</table>
	</page_header>
	<table style="width:100%" class="rpt_table rpt_table-bordered">
		'.$csvFileData2.'
		<tr><td colspan="5">&nbsp;</td></tr>
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" align="right">Superbills:</td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right" >'.$countSuperbills.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="left" ></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" align="right">No Superbills:</td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right" >'.$countNoSuperbills.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="left" ></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
		</tr>		
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" align="right">Total Records:</td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right" >'.$countRes.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="left" ></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
		</tr>		
		<tr><td colspan="5" class="total-row"></td></tr>		
	</table>
	</page>';

	$csvFileData = $page_header_val.'
		<table style="width:100%" class="rpt_table rpt_table-bordered">			
		<tr>
			<td class="text_b_w" width="30%" align="center">Patient Name-ID</td>
			<td class="text_b_w" width="10%" align="center">Exam Date</td>
			<td class="text_b_w" width="25%" align="center">Test Name</td>
			<td class="text_b_w" width="25%" align="center">Physician</td>
			<td class="text_b_w" width="10%" align="center">Superbill</td>
		</tr>
		'.$csvFileData2.'
		<tr><td colspan="5">&nbsp;</td></tr>
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" align="right">Superbills:</td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right" >'.$countSuperbills.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="left" ></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" align="right">No Superbills:</td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right" >'.$countNoSuperbills.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="left" ></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
		</tr>		
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr>
			<td class="text_12b" bgcolor="#FFFFFF" align="right">Total Records:</td>
			<td class="text_12b" bgcolor="#FFFFFF" align="right" >'.$countRes.'</td>
			<td class="text_12" bgcolor="#FFFFFF" align="left" ></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
			<td class="text_12" bgcolor="#FFFFFF" align="left"></td>
		</tr>		
		<tr><td colspan="5" class="total-row"></td></tr>
	</table>';

	//FOR CSV
	$arr=array();
	$arr[]="";
	fputcsv($fp, $arr, ",","\"");
	
	$arr=array();
	$arr[]="Superbills:";
	$arr[]=$countSuperbills;
	fputcsv($fp,$arr, ",","\"");				
	
	$arr=array();
	$arr[]="No Superbills:";
	$arr[]=$countNoSuperbills;
	fputcsv($fp,$arr, ",","\"");				
	
	$arr=array();
	$arr[]="Total Records:";
	$arr[]=$countRes;
	fputcsv($fp,$arr, ",","\"");	
	fclose($fp);

	$HTMLCreated='0';
	if($printFile == true){
		$HTMLCreated='1';
		$styleHTML='<style>'.file_get_contents('../css/reports_html.css').'</style>';	
		$pdf_css= '<style>'.file_get_contents("../css/reports_pdf.css").'</style>';
		
		$csvFileData=$styleHTML.$csvFileData;
		
		$strHTML =$pdf_css.$pdfData;
		$file_location = write_html($strHTML, 'unbilled_tests.html');


		if($output_option=='output_csv'){
			echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
		}elseif($output_option=='output_pdf'){
			echo '<div class="text-center alert alert-info">PDF generated in separate window.</div>';
		}elseif($output_option=='view'){
			echo $csvFileData;	
		}
	}else{
		echo '<div class="text-center alert alert-info">No record exists.</div>';
	}
}
?> 