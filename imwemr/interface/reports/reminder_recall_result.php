<?php 
$repFrom = $_REQUEST['repFrom'];
if(isset($_REQUEST['repType']) || isset($_REQUEST['recallRepType'])){
	$recallRepType 	= ($_REQUEST['repType']!='') ? $_REQUEST['repType'] : $_REQUEST['recallRepType'];
	$create_label 	= $recallRepType=='create_label' ? 1 : 0;
	$recall_letters = $recallRepType=='recall_letters' ? 1 : 0;
	$House_Calls 	= $recallRepType=='House_Calls' ? 1 : 0;
	$pam			= $recallRepType=='pam' ? 1 : 0;
	$send_email		= $recallRepType=='send_email' ? 1 : 0;
}
$recallTemplatesListId=$_REQUEST['recallTemplatesListId'];
$recall_date_from=$_REQUEST['recall_date_from'];
$recall_date_to=$_REQUEST['recall_date_to'];
$excSentEmail=$_REQUEST["excSentEmail"];
$months=$_REQUEST['months'];
$years=$_REQUEST['years'];

// RECALL FULLFILLMENT DATES
$Start_date = getDateFormatDB($_REQUEST['Start_date']);
$End_date = getDateFormatDB($_REQUEST['End_date']);

$last_nam_to=$_REQUEST['last_nam_to'];	
if($last_nam_frm =='' && $last_nam_to==''){
	$last_nam_frm = 'a';
	$last_nam_to='z';
}

$facility_name = $_REQUEST["facility_name"];
$facility_name = implode(',',$facility_name);

$procedures = implode(',' ,$_REQUEST['procedures']);

$cptCodeId = $_REQUEST["cptCodeId"];
$cptCodeId 	= implode("','",$cptCodeId);

$dxCodeId = $_REQUEST["dxCodeId"];
$dxCodeId = implode(',', $dxCodeId);

$dxCodeId10 = $_REQUEST["dxCodeId10"];
$dxCodeId10   = implode("','",$dxCodeId10);

$add_tests = $_REQUEST["add_tests"];
$add_tests = implode("','",$add_tests);

$elemImmunizId = $_REQUEST["immunizId"];
$elemImmunizId = implode(',',$elemImmunizId);

$medications	= $_REQUEST["medications"];
$allergies		= $_REQUEST["allergies"];

if($recall_date_from!='' || $recall_date_to!=''){
	$recall_date_from = getDateFormatDB($recall_date_from);
	$recall_date_to = getDateFormatDB($recall_date_to);
}

$ArrCreatedBy = getUserDetails($_SESSION['authId']);
$createdBy = strtoupper(substr($ArrCreatedBy['fname'],0,1).substr($ArrCreatedBy['lname'],0,1));
$createdOn = date()." ".date('h:i A');
$unfullQry = '';
$pat_id_imp ='';
$dateQry = '';

//MASTER ARRAY FOR FACILITY
$qry="select pos.pos_facility_id, pos.facilityPracCode, pos_tbl.pos_prac_code FROM pos_facilityies_tbl pos LEFT JOIN pos_tbl ON pos_tbl.pos_id=pos.pos_id";
$rs=imw_query($qry);
$arrAllPosFacility=array();
while($res=imw_fetch_assoc($rs)){
	$arrAllPosFacility[$res['pos_facility_id']]=$res['facilityPracCode'].' - '.$res['pos_prac_code'];
}unset($rs);

//--- GET ALL PROVIDER NAME ----
$providerRs = imw_query("Select id,fname,mname,lname from users");
$providerNameArr = array();
while($providerResArr = imw_fetch_assoc($providerRs)){
	$id = $providerResArr['id'];
	$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
}

$qry="Select id, name, fac_prac_code FROM facility";
$rs=imw_query($qry);
$arrAllPosFacOfFacility=array();
$arrFacility=array();
while($res=imw_fetch_assoc($rs)){
	$arrAllPosFacOfFacility[$res['id']]=$res['fac_prac_code'];
	$arrFacility[$res['id']]=$res['name'];
}unset($rs);

// GETTING UNFULLFILLMENT PATIENT RECORDS
if($_REQUEST['unfullRec'] || (empty($Start_date)==false && empty($End_date)==false)){
	if($months){
		$whr_month = " and date_format(prec.recalldate,'%m') = '".$months."'";
	}
	if($years){
		$whr_year = " and date_format(prec.recalldate,'%Y') = '".$years."'";
	}
	if($recall_date_from!='' || $recall_date_to!='')
	{
		if($recall_date_from!='') {
			$dateQry = " AND prec.recalldate >='".$recall_date_from."'";
		}
		if($recall_date_to!='') {
			$dateQry = " AND prec.recalldate <='".$recall_date_to."'";
		}
	}
	
	//$hippa_whr = " AND pd.hipaa_voice='1'";
	
	if($facility_name != ""){
		$whr_fac = " AND prec.facility_id in($facility_name)";
	}
	if($procedures != ""){
		$whr_proc = " AND prec.procedure_id in($procedures)";
	}	
	
	$arrQry = array();
	$qry = imw_query("SELECT pd.id, pd.lname, pd.fname, pd.DOB, pd.phone_home, prec.descriptions, prec.recalldate, sp.proc  
			FROM patient_app_recall AS prec
			LEFT JOIN patient_data AS pd ON prec.patient_id = pd.id 
			LEFT JOIN slot_procedures sp ON prec.procedure_id = sp.id 
			WHERE 1 = 1 
			AND prec.descriptions != 'MUR_PATCH' 
			AND pd.patientStatus='Active' 
			$whr_fac $whr_proc $whr_year $whr_month $dateQry $hippa_whr 
			ORDER BY pd.lname asc, pd.fname asc");
	while($res=imw_fetch_assoc($qry)){
		$arrQry[]=$res;
	}

	if(empty($Start_date)==false && empty($End_date)==false){
		$prevQry = " (date_format(sa_app_start_date,'%Y-%m-%d') BETWEEN '".$Start_date."' AND '".$End_date."')";
	}
	
	$arrSchDetails = array();
	$schQry = imw_query("select sa_patient_id, DATE_FORMAT(sa_app_start_date, '".get_sql_date_format('','Y','/')."') as sa_app_start_date, TIME_FORMAT(sa_app_starttime, '%h:%i %p') as sa_app_starttime 
				from schedule_appointments 
				where $prevQry");
	while($resSchQry=imw_fetch_assoc($schQry)){
		$arrSchDetails[$resSchQry["sa_patient_id"]] = $resSchQry;
	}
	
	
	$fulfilled = array();
	$unfulfilled = array();
	$pat_id_arr = array();
	
	if(is_array($arrQry) && count($arrQry) > 0){
		$unfulid = 0;
		foreach($arrQry as $thisRecall){
			$lname = $thisRecall['lname'];
			if($thisRecall['fname']){
				$fname = ', '.$thisRecall['fname'];
			}
			$DOB = strtotime($thisRecall['DOB']);
			$birth = get_date_format($thisRecall['DOB'],'','','','/');
			$pat_nam = $lname.$fname;
			
			if(isset($arrSchDetails[$thisRecall["id"]]) && count($arrSchDetails[$thisRecall["id"]]) > 0){
				// DO NOTHING
			}else{
				$pat_id_arr[] = $thisRecall["id"];
				$unfulfilled[$unfulid]['pat_name'] = $pat_nam." - ".$thisRecall["id"];
				$unfulfilled[$unfulid]['dob'] = $birth;
				
				$phone_default = $thisRecall["phone_home"];
				$prefer_contact = $thisRecall["preferr_contact"];
				if($prefer_contact == 0)
				{
					if(trim($thisRecall["phone_home"]) != ""){$phone_default = $thisRecall["phone_home"]; }
				}
				else if($prefer_contact == 1)
				{
					if(trim($thisRecall["phone_biz"]) != ""){$phone_default = $thisRecall["phone_biz"]; }				
				}
				else if($prefer_contact == 2)
				{
					if(trim($thisRecall["phone_cell"]) != ""){$phone_default = $thisRecall["phone_cell"]; }				
				}	
							
				$unfulfilled[$unfulid]['phone'] = $phone_default;			
				$unfulfilled[$unfulid]['proc'] = $thisRecall['proc'];
				$unfulfilled[$unfulid]['desc'] = $thisRecall['descriptions'];
				$unfulid++;
			}
		}
	}
	$pat_id_imp = implode(',', $pat_id_arr);
	
	unset($pat_id_arr);
	unset($arrQry);
	unset($arrSchDetails);
	$unfullQry =" AND pd.id IN(".$pat_id_imp.")"; 
} // END UNFULLFILLMENT RECORDS

if( isset($_REQUEST['json']) && (bool)$_REQUEST['json'] === true){
	print $pat_id_imp;
	exit;
}

if($create_label == 1 || $House_Calls == 1  || $pam == 1){
	if($months<>""){
		$recalldate=date("Y-m-d",mktime(0,0,0,date("m")+$months,date("d"),date("y")));	
		$where="where MONTH(recalldate)='$months' and YEAR(recalldate)='$years'";
		$where1="where MONTH(appt_date)='$months' and YEAR(appt_date)='$years'";
		
	}else{
		$selDate = ($recall_date_to!='') ? $recall_date_to : $recall_date_from;
		$dt = explode("-", $selDate);

		$recalldate=date("Y-m-d",mktime(0,0,0,date("m")+$months, date("d"), date("y")));			
		$where="where (recalldate BETWEEN '".$recall_date_from."' AND '".$recall_date_to."')";
		$where1="where (appt_date BETWEEN '".$recall_date_from."' AND '".$recall_date_to."')";
	}
	if($last_nam_frm){
		$last_whr=" and (pd.lname between '$last_nam_frm' and '$last_nam_to' or pd.lname like '$last_nam_to%')";
	}
	
	if($facility_name != ""){
		$whr_fac = " AND par.facility_id in($facility_name)";
	}
	if($procedures != ""){
		$whr_proc = " AND par.procedure_id in($procedures)";
	}		
	
	$procedureIdwhere=" and par.procedure_id not in ('-1')";
	$hippa_whr='';
	//$hippa_whr = " AND pd.hipaa_voice='1'";
	if($pam){
		$hippa_whr = " AND pd.hipaa_email='1'";
	}

	if($excSentEmail)
	{
		//get ids with sent mail in date range
		$query=imw_query("select appt_id as recall_id from exclude_sent_email $where1 and report='Recalls'");
		if(imw_num_rows($query)>=1)
		{
			while($data=imw_fetch_object($query))
			{
				$recalIds[]=$data->recall_id;
			}	
			$recalIdStr=implode(',',$recalIds);
		}
		
		if($recalIdStr)
		{
			$hipaaEmail .= " AND par.id NOT IN($recalIdStr)";	
		}
	}	
	
	$qry="select par.*,pd.* from  patient_app_recall as par,patient_data as pd $where and par.patient_id=pd.id AND par.descriptions != 'MUR_PATCH' $hippa_whr $last_whr $procedureIdwhere $unfullQry AND pd.patientStatus='Active' $hipaaEmail $whr_fac $whr_proc ORDER BY pd.lname asc,pd.fname asc";
 	$qryIMM="select par.*,pd.* from  patient_app_recall as par,patient_data as pd $where and par.patient_id=pd.id AND par.descriptions != 'MUR_PATCH' $hippa_whr $last_whr  and par.procedure_id in ('-1') $unfullQry AND pd.patientStatus='Active' $hipaaEmail $whr_fac $whr_proc ORDER BY pd.lname asc,pd.fname asc";
}

if($send_email == 1){
	if($months<>""){
		$recalldate=date("Y-m-d",mktime(0,0,0,date("m")+$months,date("d"),date("y")));	
		$where="where MONTH(recalldate)='$months' and YEAR(recalldate)='$years'";
		$where1="where MONTH(appt_date)='$months' and YEAR(appt_date)='$years'";
		
		}else{
			$selDate = ($recall_date_to!='') ? $recall_date_to : $recall_date_from;
			$dt = explode("-", $selDate);
	
			$recalldate=date("Y-m-d",mktime(0,0,0,date("m")+$months, date("d"), date("y")));			
			$where="where (recalldate BETWEEN '".$recall_date_from."' AND '".$recall_date_to."')";
			$where1="where (appt_date BETWEEN '".$recall_date_from."' AND '".$recall_date_to."')";
		}
		if($last_nam_frm){
			$last_whr=" and (pd.lname between '$last_nam_frm' and '$last_nam_to' or pd.lname like '$last_nam_to%')";
		}
		
		if($facility_name != ""){
			$whr_fac = " AND par.facility_id in($facility_name)";
		}
		if($procedures != ""){
			$whr_proc = " AND par.procedure_id in($procedures)";
		}			
		
		$procedureIdwhere=" and par.procedure_id not in ('-1')";
		$hippa_whr='';
		
		$hippa_whr = " AND pd.hipaa_email='1' AND pd.email<>''";
		if($excSentEmail){
			//get ids with sent mail in date range
			$query=imw_query("select appt_id as recall_id from exclude_sent_email $where1 and report='Recalls'");
			if(imw_num_rows($query)>=1)
			{
				while($data=imw_fetch_object($query))
				{
					$recalIds[]=$data->recall_id;
				}	
				$recalIdStr=implode(',',$recalIds);
			}
			
			if($recalIdStr)
			{
				$hipaaEmail .= " AND par.id NOT IN($recalIdStr)";	
			}
		}
		
		$qry="select par.*,pd.*,par.id as recall_id from  patient_app_recall as par,patient_data as pd $where and par.patient_id=pd.id AND par.descriptions != 'MUR_PATCH' $hippa_whr $last_whr $procedureIdwhere $unfullQry AND pd.patientStatus='Active' $pt_string $hipaaEmail $whr_fac $whr_proc ORDER BY pd.lname asc,pd.fname asc";
		
		$qryIMM="select par.*,pd.*,par.id as recall_id from  patient_app_recall as par,patient_data as pd $where and par.patient_id=pd.id AND par.descriptions != 'MUR_PATCH' $hippa_whr $last_whr  and par.procedure_id in ('-1') $unfullQry AND pd.patientStatus='Active' $pt_string $hipaaEmail $whr_fac $whr_proc ORDER BY pd.lname asc,pd.fname asc";	
	//die($qryIMM);
}

$res = @imw_query($qry);
$num = @imw_num_rows($res);

$resIMM = @imw_query($qryIMM);
$numIMM = @imw_num_rows($resIMM);
	if($create_label == 1){
		echo '<div class="text-center alert alert-info">Result is populated in separate window</div>';
	}else if($recall_letters==1){
		$recallLetterAction = "recall_report_print_letters.php";
		$blDtRange = false;
		$mthd='post';
		if($_REQUEST["recall_date_from"]!='' || $_REQUEST["recall_date_to"]!='') {
			$recallLetterAction = "recall_date_range_letter.php";
			$mthd='post';
			$blDtRange=true;
		}
		echo '<div class="text-center alert alert-info">Result is populated in separate window</div>';
    }else if($num > 0 && $send_email == 1){
		$recallLetterAction = "recall_report_send_letters.php";
		$blDtRange = false;
	?>
    <form name="frmRecallLetterEmail" id="frmRecallLetterEmail" action="recall_report_send_letters.php" target="iframeHidden" method="post"><!---->
        <input type="hidden" name="months" value="<?php echo $_REQUEST["months"];?>">
        <input type="hidden" name="years" value="<?php echo $_REQUEST["years"];?>">
        <input type="hidden" name="last_nam_frm" value="<?php echo $_REQUEST["last_nam_frm"];?>">
        <input type="hidden" name="last_nam_to" value="<?php echo $_REQUEST["last_nam_to"];?>">
        <input type="hidden" name="recallTemplatesListId" value="<?php echo $_REQUEST["recallTemplatesListId"];?>">
        <input type="hidden" name="recall_date_from" value="<?php echo $_REQUEST["recall_date_from"];?>">
        <input type="hidden" name="recall_date_to" value="<?php echo $_REQUEST["recall_date_to"];?>">
        <input type="hidden" name="cptCodeId" value="<?php echo $cptCodeId;?>">
        <input type="hidden" name="dxCodeId" value="<?php echo $dxCodeId;?>">
		<input type="hidden" name="dxCodeId10" value="<?php echo $dxCodeId10;?>">
        <input type="hidden" name="add_tests" value="<?php echo $add_tests;?>">
        <input type="hidden" name="elemImmunizId" value="<?php echo $elemImmunizId;?>">
        <input type="hidden" name="medications" value="<?php echo $_REQUEST["medications"];?>">
        <input type="hidden" name="allergies" value="<?php echo $_REQUEST["allergies"];?>">
		<input type="hidden" name="facility_name" value="<?php echo $facility_name;?>">
		<input type="hidden" name="procedures" value="<?php echo $procedures;?>">
        <table width="100%" class="rpt_table rpt rpt_table-bordered rpt_padding">	
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:350px;">Recall Report</td>
				<td class="rptbx2" style="width:350px;">Total Recalls: <?php echo ($num + $numIMM);?></td>
				<td class="rptbx3" style="width:350px;" style="text-align:right">Created by <?php echo $createdBy.' on '.$createdOn;?></td>
			</tr>
		</table>	
		<table width="100%" class="rpt_table rpt rpt_table-bordered rpt_padding">
            <tr>
                <td align="left" nowrap="nowrap" class="text_b_w"><input type="checkbox" name="check_all" id="check_all" onClick="">&nbsp;Account-ID</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Message Type</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Office</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Language Type</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Patient Fname</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Patient Lname</td>
                <td align="left" nowrap="nowrap" class="text_b_w">App Date</td>
                <td align="left" nowrap="nowrap" class="text_b_w">App Time</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Provider</td>
				<td align="left" nowrap="nowrap" class="text_b_w">App Type</td>
				<td align="left" nowrap="nowrap" class="text_b_w">Phone</td>
				<td align="left" nowrap="nowrap" class="text_b_w">Email</td>
            </tr>
		<?php
		$i=0;
		
		while($rw=@imw_fetch_array($res)){
			$patientid=$rw['patient_id'];
			$hipaa_voice=$rw['hipaa_voice'];
			$pat_lname=$rw['lname'];
			$pat_fname=$rw['fname'];
			$pat_det=patient_data($patientid);
			$proc_id=$rw['procedure_id'];
			$oper=$rw['operator'];
			$provID = $rw['providerID'];
			$recalldate=get_date_format($rw['recalldate'],'','','','/');
			$pat_email=$rw['email'];
			$phone=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['8']))));

			if($proc_id>0){
			$sel_proc_nam=imw_query("select proc from slot_procedures where id='$proc_id'");
			$row_proc=imw_fetch_array($sel_proc_nam);
			$proc=$row_proc['proc'];
			}else if($proc_id=="-1") {
				$proc=$rw["procedure_name"];
			}
			
			if(!empty($provID)){
				$sel_proc_nam1=imw_query("select fname, lname from users where id='".$provID."'");
				$row_proc1=imw_fetch_array($sel_proc_nam1);

				$prov_fname=$row_proc1['fname']." ".$row_proc1['lname'];
			}
			
			$fac_id=$rw["default_facility"];
			if($fac_id <= 0){
				$rs=imw_query("Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$patientid."' 
				AND sa_app_start_date<='".date('Y-m-d')."' ORDER BY sa_app_start_date DESC LIMIT 0,1");
				$facRes=imw_fetch_array($rs);
				$fac_id= $facRes['sa_facility_id'];
				if($fac_id>0){
					$fac_id= $arrAllPosFacOfFacility[$fac_id];
				}
			}
			
			$facname = $arrAllPosFacility[$fac_id];
			if(empty($facname) == false){
				$facname = strtoupper(trim(array_shift(explode('-', $facname))));
			}
			
			$office_code="01";
			$pos_fac_city_key=array_search($facname,$GLOBALS['PAM2000']);
			if($pos_fac_city_key) $office_code=$pos_fac_city_key;
			
			/* $fac_id=$rw["default_facility"];
			$fac_qry=imw_query("select pos_facility_city from pos_facilityies_tbl where pos_facility_id ='$fac_id'");
			$fac_run=imw_fetch_array($fac_qry);
			
			$office_code="";
			if($fac_run['pos_facility_city']=='Vineland'){
				$office_code='01';
			}else if($fac_run['pos_facility_city']=='Mays Landing'){
				$office_code='02';
			}else if($fac_run['pos_facility_city']=='Hammonton'){
				$office_code='03';
			}else if($fac_run['pos_facility_city']=='Cherry Hill'){
				$office_code='04';
			}else if($fac_run['pos_facility_city']=='Blackwood'){
				$office_code='05';
			}else{
				$office_code='01';
			} */

			// END PAM DATA----		
			$i++;
			?>
            <tr>
                <td class="text_10" align="left"><input type="checkbox" name="pat_id_imp[]" id="pat_id_imp_<?php echo $i;?>" value="<?php echo $rw["recall_id"];?>" class="checkbox1">&nbsp;<?php echo $patientid;?></td>                
                <td class="text_10" align="left">02</td>
                <td class="text_10" align="left"><?php echo $office_code;?></td>				
                <td class="text_10" align="left">01</td>
                <td class="text_10" align="left"><?php echo strtoupper($pat_fname);?></td>
                <td class="text_10" align="left"><?php echo strtoupper($pat_lname);?></td>
                <td class="text_10" align="left"><?php echo $recalldate;?></td>
                <td class="text_10" align="left">00:00</td>			
                <td class="text_10" align="left"><?php echo $prov_fname;?></td>
				<td class="text_10" align="left"><?php echo $proc;?></td>
				<td class="text_10" align="left"><?php echo core_phone_format($phone);?></td>
				<td class="text_10" align="left"><?php echo $pat_email;?></td>
           </tr>
			<?php
		}
///by Ram To Show Immunization Entries//

while($rwIMM=@imw_fetch_array($resIMM)){

			$patientid=$rwIMM['patient_id'];
			$hipaa_voice=$rw['hipaa_voice'];
			$pat_lname=$rwIMM['lname'];
			$pat_fname=$rwIMM['fname'];
			$pat_det= patient_data($patientid);
			$proc_id=$rwIMM['procedure_id'];
			$oper=$rwIMM['operator'];
			$provID = $rwIMM['providerID'];
			$recalldate=$rwIMM['recalldate'];
			//$recalldate=strtotime($rwIMM['recalldate']);
			//$recalldate=date("m/d/Y",$recalldate);
			$pat_email=$rwIMM['email'];
			$phone=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['8']))));

			if($proc_id>0){
			$sel_proc_nam=imw_query("select proc from slot_procedures where id='$proc_id'");
			$row_proc=imw_fetch_array($sel_proc_nam);
			$proc=$row_proc['proc'];
			}else if($proc_id=="-1") {
				$proc=$rwIMM["procedure_name"];
			}
			
			if(!empty($provID)){
				$sel_proc_nam1=imw_query("select fname, lname from users where id='".$provID."'");
				$row_proc1=imw_fetch_array($sel_proc_nam1);

				$prov_fname=$row_proc1['fname']." ".$row_proc1['lname'];
			}
			
			$fac_id=$rwIMM["default_facility"];
			if($fac_id <= 0){
				$rs=imw_query("Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$patientid."' 
				AND sa_app_start_date<='".date('Y-m-d')."' ORDER BY sa_app_start_date DESC LIMIT 0,1");
				$facRes=imw_fetch_array($rs);
				$fac_id= $facRes['sa_facility_id'];
				if($fac_id>0){
					$fac_id= $arrAllPosFacOfFacility[$fac_id];
				}
			}
			
			$facname = $arrAllPosFacility[$fac_id];
			if(empty($facname) == false){
				$facname = strtoupper(trim(array_shift(explode('-', $facname))));
			}
			
			$office_code="01";
			$pos_fac_city_key=array_search($facname,$GLOBALS['PAM2000']);
			if($pos_fac_city_key) $office_code=$pos_fac_city_key;
			
			/* $fac_id=$rwIMM["default_facility"];
			$fac_qry=imw_query("select pos_facility_city from pos_facilityies_tbl where pos_facility_id ='$fac_id'");
			$fac_run=imw_fetch_array($fac_qry);
			
			$office_code="";
			if($fac_run['pos_facility_city']=='Vineland'){
				$office_code='01';
			}else if($fac_run['pos_facility_city']=='Mays Landing'){
				$office_code='02';
			}else if($fac_run['pos_facility_city']=='Hammonton'){
				$office_code='03';
			}else if($fac_run['pos_facility_city']=='Cherry Hill'){
				$office_code='04';
			}else if($fac_run['pos_facility_city']=='Blackwood'){
				$office_code='05';
			}else{
				$office_code='01';
			} */
			// END PAM DATA----	
			$i++;
			?>
            <tr>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo $patientid;?></td>                
                <td nowrap="nowrap"  class="text_10" align="left">02</td>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo $office_code;?></td>				
                <td nowrap="nowrap"  class="text_10" align="left">01</td>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo strtoupper($pat_fname);?></td>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo strtoupper($pat_lname);?></td>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo $recalldate;?></td>
                <td nowrap="nowrap"  class="text_10" align="left">00:00</td>			
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo $prov_fname;?></td>
				<td nowrap="nowrap"  class="text_10" align="left"><?php echo $proc;?></td>
				<td nowrap="nowrap"  class="text_10" align="left"><?php echo core_phone_format($phone);?></td>
				<td nowrap="nowrap"  class="text_10" align="left"><?php echo $pat_email;?></td>
           </tr>
			<?php
		}
		?>
        </table>
        </form>
        <iframe name="iframeHidden" id="iframeHidden" height="0" width="0" src=""></iframe>
	<?php
	}else if($num > 0 && $House_Calls == 1){
		?>
		<table width="100%" class="rpt_table rpt rpt_table-bordered rpt_padding">	
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:350px;">Recall Report</td>
				<td class="rptbx2" style="width:350px;">Total Recalls: <?php echo ($num + $numIMM);?></td>
				<td class="rptbx3" style="width:350px;" style="text-align:right">Created by <?php echo $createdBy.' on '.$createdOn;?></td>
			</tr>
		</table>	
		<table width="100%" class="rpt_table rpt rpt_table-bordered rpt_padding">		
            <tr class='text_9b'>
                <td class="text_b_w" width="95" nowrap="nowrap">Account-ID</td>
                <td class="text_b_w" width="95" nowrap="nowrap">Patient Name</td>
                <td class="text_b_w" width="95" nowrap="nowrap">Address</td>
                <td class="text_b_w" width="90" align="center" nowrap="nowrap">City</td>
                <td class="text_b_w" width="90" align="center" nowrap="nowrap">State</td>
                <td class="text_b_w" width="90" align="center" nowrap="nowrap">Zip</td>
                <td class="text_b_w" align="center" width="95">Telephone</td>
                <td class="text_b_w" align="center" width="95">DOB</td>
                <td class="text_b_w" width="95">Provider</td>
                <td class="text_b_w" width="95">Default Facility</td>
				<td class="text_b_w" align="center" width="95">Recall Date</td>
				<td class="text_b_w" align="center" width="95">Recall Procedure</td>
				<td class="text_b_w" align="center" width="95">Recall Desc</td>
            </tr>
		<?php
		// house calls
		if(in_array(strtolower($billing_global_server_name), array('arizonaeye'))){
			$pfx="|";
		}else{
			$pfx=",";	
		} 
		$exceltext="";
		$exceltext.="Patient Name".$pfx;
		$exceltext.="Patient Home Phone".$pfx;
		$exceltext.="Patient Mobile Number".$pfx;
		$exceltext.="Appointment Date".$pfx;
		$exceltext.="Appointment Time".$pfx;
		$exceltext.="Patient Account Number".$pfx;
		$exceltext.="Doctor Number".$pfx;
		$exceltext.="Procedure Number".$pfx;
		
		$exceltext.="Doctor Name".$pfx;
		$exceltext.="Procedure Name".$pfx;
		$exceltext.="Location (office) Name".$pfx;
		
		$exceltext.="Patient Address".$pfx;
		$exceltext.="Patient City".$pfx;
		$exceltext.="Patient State".$pfx;
		$exceltext.="Patient Zip Code".$pfx;
		$exceltext.="Patient Email Address";
		//------------	
		$cssHTML =  '<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
		$cssHTML .= '
			<page backtop="10mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';
		$pdfData = '
		<page_header>';
		$commonDataH = '
			<table width="1050" class="rpt_table rpt rpt_table-bordered rpt_padding">	
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">Recall Report</td>
					<td class="rptbx2" style="width:350px;">Total Recalls: '.($num + $numIMM).'</td>
					<td class="rptbx3" style="width:350px;" align="right">Created by '.$createdBy.' on '.$createdOn.'</td>
				</tr>
			</table>	
			<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">	
				<tr>
					<td class="text_b_w" width="55" nowrap="nowrap">Account-ID</td>
					<td class="text_b_w" width="80" nowrap="nowrap">Patient Name</td>
					<td class="text_b_w" width="80" nowrap="nowrap">Address</td>
					<td class="text_b_w" width="80" align="center" nowrap="nowrap">City</td>
					<td class="text_b_w" width="80" align="center" nowrap="nowrap">State</td>
					<td class="text_b_w" width="60" align="center" nowrap="nowrap">Zip</td>
					<td class="text_b_w" align="center" width="70">Telephone</td>
					<td class="text_b_w" align="center" width="60">DOB</td>
					<td class="text_b_w" width="70">Provider</td>
					<td class="text_b_w" align="center" width="70">Default Fac.</td>
					<td class="text_b_w" align="center" width="60">Recall Date</td>
					<td class="text_b_w" align="center" width="75">Recall Proc</td>
					<td class="text_b_w" align="center" width="75">Recall Desc</td>
				</tr>
			</table>';
		$pdfData .= $commonDataH.'
		</page_header>';
		
		$commonData.= '
		<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">';

		while($rw=@imw_fetch_array($res)){
			$patientid=$rw['patient_id'];
			$hipaa_voice=$rw['hipaa_voice'];
			$pat_det= patient_data($patientid);
			$proc_id=$rw['procedure_id'];
			$desc=$rw['descriptions'];
			$oper=$rw['operator'];
			$providerID=$rw['providerID'];
			
			$recalldate=strtotime($rw['recalldate']);
			$recalldate=get_date_format($rw['recalldate']);
			
			$pt_dob = $pat_det[4];
			$dob = date('m-d-Y', strtotime($pt_dob));
			$patNameArr = array();
			$patNameArr["LAST_NAME"] = $rw['lname'];
			$patNameArr["FIRST_NAME"] = $rw['fname'];
			$patNameArr["MIDDLE_NAME"] = $rw['mname'];
			$patName = changeNameFormat($patNameArr);

			$qry=imw_query("Select fname,mname,lname from users where id = '".$oper."'") or die(imw_error());
			$op_res =imw_fetch_array($qry);
			$docNameArr=array();
			$docNameArr["LAST_NAME"] = $op_res['lname'];
			$docNameArr["FIRST_NAME"] = $op_res['fname'];
			$docName = changeNameFormat($docNameArr);
			
			$qry=imw_query("Select proc from slot_procedures where id='".$proc_id."'") or die(imw_error());
			$proc_res =imw_fetch_array($qry);
			$proc_name = $proc_res['proc'];
			
			$facility_id = $rw['facility_id'];
			
			$pos_facility=$rw['default_facility'];
			if($pos_facility<=0){
				//GETTING LAST APPT FACILITY
				$rs=imw_query("Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$patientid."' 
				AND sa_app_start_date<='".date('Y-m-d')."' ORDER BY sa_app_start_date DESC LIMIT 0,1");
				$facRes=imw_fetch_array($rs);
				$fac_id= $facRes['sa_facility_id'];
				
				if($fac_id>0){
					$pos_facility= $arrAllPosFacOfFacility[$fac_id];
				}
			}
			$facName = "";
			if ($facility_id != 0 && $facility_id != ""){
				$facName = $arrFacility[$facility_id];
			}else{
				$facName = $arrAllPosFacility[$pos_facility];
			}
			
			$phone_default = $rw["phone_home"];
			$prefer_contact = $rw["preferr_contact"];
			if($prefer_contact == 0)
			{
				if(trim($rw["phone_home"]) != ""){$phone_default = $rw["phone_home"]; }
			}
			else if($prefer_contact == 1)
			{
				if(trim($rw["phone_biz"]) != ""){$phone_default = $rw["phone_biz"]; }				
			}
			else if($prefer_contact == 2)
			{
				if(trim($rw["phone_cell"]) != ""){$phone_default = $rw["phone_cell"]; }				
			}
			
			$telephone = core_phone_format($phone_default);			
			
			// STRING HOUSE CALLS
			if(in_array(strtolower($billing_global_server_name), array('arizonaeye'))){
				$strHouseData.= ''.$patName.''.'|'.''.$phone_default.''.'|'.''.$rw["phone_cell"].''.'|'.''.$recalldate.''.'|'.'""'.'|'.''.$patientid.''.'|'.
				''.$oper.''.'|'.''.$proc_id.''.'|'.''.$docName.''.'|'.''.$proc_name.''.'|'.''.$facName.''.'|'.
				''.$rw['street'].''.'|'.''.$rw['city'].''.'|'.''.$rw['state'].''.' '.''.$rw['postal_code'].''.'|'.''.$rw["email"].'';
			} else {
				$strHouseData.= '"'.$patName.'"'.','.'"'.$phone_default.'"'.','.'"'.$rw["phone_cell"].'"'.','.'"'.$recalldate.'"'.','.'""'.','.'"'.$patientid.'"'.','.
				'"'.$oper.'"'.','.'"'.$proc_id.'"'.','.'"'.$docName.'"'.','.'"'.$proc_name.'"'.','.'"'.$facName.'"'.','.
				'"'.$rw['street'].'"'.','.'"'.$rw['city'].'"'.','.'"'.$rw['state'].'"'.' '.'"'.$rw['postal_code'].'"'.','.'"'.$rw["email"].'"';
			}
			$strHouseData.= "\n";
			
			// ------------------	
			
			$commonData .= '
            <tr>
                <td nowrap="nowrap" class="text_10" valign="top" width="55">'.$patientid.'</td>                
                <td nowrap="nowrap"  class="text_10" valign="top" width="80">'.$pat_det[9].'</td>
                <td class="text_10" align="left" valign="top" width="80">'.$pat_det[1].' '.$pat_det[2].'</td>							
                <td align="center" valign="top" nowrap="nowrap"  class="text_10" width="80">'.$pat_det[5].'</td>
                <td class="text_10" align="center" valign="top" width="80">'.$pat_det[6].'</td>
                <td class="text_10" align="center" valign="top" width="60">'.$pat_det[7].'</td>
                <td class="text_10" align="center" valign="top" width="70">'.$telephone.'</td>
                <td class="text_10" align="center" valign="top" width="60">'.$dob.'</td>
                <td class="text_10" nowrap="nowrap" valign="top" width="70">'.$providerNameArr[$providerID].'</td>
				<td class="text_10" align="center" valign="top" width="70">'.$facName.'</td>			
                <td class="text_10" align="center" valign="top" width="60">'.$recalldate.'</td>
				<td class="text_10" align="left" valign="top" width="75">'.$proc_name.'</td>
				<td class="text_10" align="left" valign="top" width="75">'.$rw['descriptions'].'</td>
           </tr>
			';
			?>
            <tr>
                <td nowrap="nowrap"  class="text_10" valign="top"><?php echo $patientid;?></td>                
                <td nowrap="nowrap"  class="text_10" valign="top"><?php echo $pat_det[9];?></td>
                <td class="text_10" align="left" valign="top"><?php echo $pat_det[1]." ".$pat_det[2]; ?> </td>							
                <td align="center" valign="top" nowrap="nowrap"  class="text_10"><?php echo $pat_det[5];?></td>
                <td align="center" valign="top" class="text_10"><?php echo $pat_det['6'];?></td>
                <td align="center" valign="top" class="text_10"><?php echo $pat_det['7'];?></td>
                <td align="center" valign="top" class="text_10"><?php echo $telephone;?></td>
                <td align="center"  valign="top" class="text_10"><?php echo $dob; ?></td>			
                <td nowrap="nowrap" valign="top" class="text_10"><?php echo $providerNameArr[$providerID]; ?></td>			
                <td align="center"  valign="top" class="text_10"><?php echo $facName; ?></td>			
				<td align="center"  valign="top" class="text_10"><?php echo $recalldate;?></td>
				<td align="left"  valign="top" class="text_10"><?php echo $proc_name;?></td>
				<td align="left"  valign="top" class="text_10"><?php echo $rw['descriptions'];?></td>
           </tr>
			<?php
		}
	?>
	<?php
		while($rwIMM=@imw_fetch_array($resIMM)){
			$patientid=$rwIMM['patient_id'];
			$hipaa_voice=$rwIMM['hipaa_voice'];
			$pat_det=patient_data($patientid);
			$proc_id=$rwIMM['procedure_id'];
			$desc=$rwIMM['descriptions'];
			$oper=$rwIMM['operator'];
			$providerID=$rwIMM['providerID'];
			
			$recalldate=strtotime($rwIMM['recalldate']);
			$recalldate = get_date_format($rwIMM['recalldate']);
			
			$pt_dob = $pat_det[4];
			$dob = date('m-d-Y', strtotime($pt_dob));
			
			$patNameArr = array();
			$patNameArr["LAST_NAME"] = $rwIMM['lname'];
			$patNameArr["FIRST_NAME"] = $rwIMM['fname'];
			$patNameArr["MIDDLE_NAME"] = $rwIMM['mname'];
			$patName = changeNameFormat($patNameArr);

			$qry=imw_query("Select fname,mname,lname from users where id = '".$oper."'") or die(imw_error());
			$op_res =imw_fetch_array($qry);
			$docNameArr=array();
			$docNameArr["LAST_NAME"] = $op_res['lname'];
			$docNameArr["FIRST_NAME"] = $op_res['fname'];
			$docName = changeNameFormat($docNameArr);
			
			$qry=imw_query("Select proc from slot_procedures where id='".$proc_id."'") or die(imw_error());
			$proc_res =imw_fetch_array($qry);
			$proc_name = $proc_res['proc'];

			$pos_facility=$rwIMM['default_facility'];
			if($pos_facility<=0){
				//GETTING LAST APPT FACILITY
				$rs=imw_query("Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$patientid."' 
				AND sa_app_start_date<='".date('Y-m-d')."' ORDER BY sa_app_start_date DESC LIMIT 0,1");
				$facRes=imw_fetch_array($rs);
				$fac_id= $facRes['sa_facility_id'];
				
				if($fac_id>0){
					$pos_facility= $arrAllPosFacOfFacility[$fac_id];
				}
			}
			
			$facName = "";
			if ($facility_id != 0 && $facility_id != ""){
				$facName = $arrFacility[$facility_id];
			}else{
				$facName = $arrAllPosFacility[$pos_facility];
			}
			
			$phone_default = $rwIMM["phone_home"];
			$prefer_contact = $rwIMM["preferr_contact"];
			if($prefer_contact == 0)
			{
				if(trim($rwIMM["phone_home"]) != ""){$phone_default = $rwIMM["phone_home"]; }
			}
			else if($prefer_contact == 1)
			{
				if(trim($rwIMM["phone_biz"]) != ""){$phone_default = $rwIMM["phone_biz"]; }				
			}
			else if($prefer_contact == 2)
			{
				if(trim($rwIMM["phone_cell"]) != ""){$phone_default = $rwIMM["phone_cell"]; }				
			}		
			$telephone2 = core_phone_format($phone_default);	
			
			// STRING HOUSE CALLS
			$strHouseData.= '"'.$patName.'"'.','.'"'.$phone_default.'"'.','.'"'.$recalldate.'"'.','.'""'.','.'"'.$patientid.'"'.','.
			'"'.$oper.'"'.','.'"'.$proc_id.'"'.','.'"'.$docName.'"'.','.'"'.$proc_name.'"'.','.'"'.$facName.'"'.','.
			'"'.$rwIMM['street'].'"'.','.'"'.$rwIMM['city'].'"'.','.'"'.$rwIMM['state'].'"'.' '.'"'.$rwIMM['postal_code'].'"';
			$strHouseData.= "\n";
			// ------------------	
				
			$commonData .= '
            <tr>
                <td nowrap="nowrap" class="text_10" valign="top" width="95">'.$patientid.'</td>                
                <td nowrap="nowrap"  class="text_10" valign="top" width="95">'.$pat_det[9].'</td>
                <td  class="text_10" align="left" valign="top" width="95">'.$pat_det[1].' '.$pat_det[2].'</td>							
                <td align="center" valign="top" nowrap="nowrap"  class="text_10" width="90">'.$pat_det[5].'</td>
                <td align="center" valign="top" class="text_10" width="90">'.$pat_det['6'].'</td>
                <td align="center" valign="top" class="text_10" width="90">'.$pat_det['7'].'</td>
                <td align="center" valign="top" class="text_10" width="95">'.$telephone2.'</td>
                <td align="center"  valign="top" class="text_10" width="95">'.$dob.'</td>			
                <td align="center"  valign="top" class="text_10" width="95">'.$providerNameArr[$providerID].'</td>			
				<td align="center"  valign="top" class="text_10" width="95">'.$facName.'</td>			
                <td align="center"  valign="top" class="text_10" width="95">'.$recalldate.'</td>
           </tr>			
			';
			?>
            <tr>
                <td nowrap="nowrap"  class="text_10" valign="top"><?php echo $patientid;?></td>                
                <td nowrap="nowrap"  class="text_10" valign="top"><?php echo $pat_det[9];?></td>
                <td  class="text_10" align="left" valign="top"><?php echo $pat_det[1]." ".$pat_det[2]; ?> </td>							
                <td align="center" valign="top" nowrap="nowrap"  class="text_10"><?php echo $pat_det[5];?></td>
                <td align="center" valign="top" class="text_10"><?php echo $pat_det['6'];?></td>
                <td align="center" valign="top" class="text_10"><?php echo $pat_det['7'];?></td>
                <td align="center" valign="top" class="text_10"><?php echo $telephone2;?></td>
                <td align="center"  valign="top" class="text_10"><?php echo $dob; ?></td>			
                <td nowrap="nowrap" valign="top" class="text_10"><?php echo $providerNameArr[$providerID]; ?></td>			
                <td align="center"  valign="top" class="text_10"><?php echo $facName; ?></td>			
                <td align="center"  valign="top" class="text_10"><?php echo $recalldate;?></td>
           </tr>
			<?php
		}
		?>
        </table>
	<?php 
	$commonData .= '</table>';
		
		// house call file write
		$exceltext.="\n".$strHouseData; 
		$filename = data_path().'users/UserId_'.$_SESSION['authId'].'/recall.txt';
		$fileInfo = pathinfo($filename);
		if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
		$fp=fopen($filename,"w");
		@fwrite($fp,$exceltext);
		@fclose($fp);
		$printFile = 1;
		//----------------------
		
	}else if($num > 0 && $pam == 1){
		$arrPAM= array();
		// PAM DATA
		$exceltext="";
		$exceltext ='Account-ID,Message Type,Office,Language Type,Patient Fname,Patient Lname,App Date,App Time,Provider,App Type,Home Phone,Email,Cell Phone';
		$exceltext.="\n";
		//------------
		$cssHTML  ='<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
		
		$cssHTML .= 
		
		'<page backtop="13mm" backbottom="10mm">			
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>';
		$pdfData .= '
		<page_header>';
		$commonDataH = '
			<table width="100%" class="rpt_table rpt rpt_table-bordered rpt_padding">	
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:350px;">Recall Report</td>
					<td class="rptbx2" style="width:350px;">Total Recalls: '.($num + $numIMM).'</td>
					<td class="rptbx3" style="width:350px;" align="right">Created by '.$createdBy.' on '.$createdOn.'</td>
				</tr>
			</table>
			<table width:"1050" class="rpt_table rpt rpt_table-bordered rpt_padding">	
				<tr>
					<td width="55" nowrap="nowrap" class="text_b_w">Account-ID</td>
					<td width="40" nowrap="nowrap" class="text_b_w">Message Type</td>
					<td width="40" nowrap="nowrap" class="text_b_w">Office</td>
					<td width="45" nowrap="nowrap" class="text_b_w">Language Type</td>
					<td width="70" nowrap="nowrap" class="text_b_w">Patient Fname</td>
					<td width="80" nowrap="nowrap" class="text_b_w">Patient Lname</td>
					<td width="60" nowrap="nowrap" class="text_b_w">App Date</td>
					<td width="60" nowrap="nowrap" class="text_b_w">App Time</td>
					<td width="110" nowrap="nowrap" class="text_b_w">Provider</td>
					<td width="80" nowrap="nowrap" class="text_b_w">App Type</td>
					<td width="80" nowrap="nowrap" class="text_b_w">Home Phone</td>
					<td width="110" nowrap="nowrap" class="text_b_w">Email</td>
					<td width="80" nowrap="nowrap" class="text_b_w">Cell Phone</td>
				   </tr>
			</table>';
		$pdfData .= $commonDataH.'
		</page_header>';
		
		$commonData .= '
		<table width:"1050" class="rpt_table rpt rpt_table-bordered rpt_padding">	
		';
	?>
	 	<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">	
			<tr>
				<td class="rptbx1" style="width:350px;">Recall Report</td>
				<td class="rptbx2" style="width:350px;">Total Recalls: <?php echo ($num + $numIMM);?></td>
				<td class="rptbx3" style="width:350px;" style="text-align:right">Created by <?php echo $createdBy.' on '.$createdOn;?></td>
			</tr>
		</table>	
		<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">	
            <tr class='text_9b' height="20">
                <td align="left" nowrap="nowrap" class="text_b_w">Account-ID</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Message Type</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Office</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Language Type</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Patient Fname</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Patient Lname</td>
                <td align="left" nowrap="nowrap" class="text_b_w">App Date</td>
                <td align="left" nowrap="nowrap" class="text_b_w">App Time</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Provider</td>
				<td align="left" nowrap="nowrap" class="text_b_w">App Type</td>
				<td align="left" nowrap="nowrap" class="text_b_w">Home Phone</td>
				<td align="left" nowrap="nowrap" class="text_b_w">Email</td>
                <td align="left" nowrap="nowrap" class="text_b_w">Cell Phone</td>
            </tr>
		<?php
		$i=0;
		
		while($rw=@imw_fetch_array($res)){
			$patientid=$rw['patient_id'];
			$hipaa_voice=$rw['hipaa_voice'];
			$pat_lname=$rw['lname'];
			$pat_fname=$rw['fname'];
			$pat_det= patient_data($patientid);
			$proc_id=$rw['procedure_id'];
			$oper=$rw['operator'];
			$provID = $rw['providerID'];
			$recalldate=get_date_format($rw['recalldate'],'','','','/');
			$pat_email=$rw['email'];
			$phone_num=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['8']))));
			$phone_home=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['28']))));
			$phone_cell=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['29']))));
			$phone_biz=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['30']))));
			
			$phone=($phone_home!='') ?  $phone_home :$phone_num;
			$cell_phone= ($phone_cell!='') ?  $phone_cell :$phone_biz;
			if($phone==$cell_phone){ $cell_phone='';}

			if($proc_id>0){
			$sel_proc_nam=imw_query("select proc from slot_procedures where id='$proc_id'");
			$row_proc=imw_fetch_array($sel_proc_nam);
			$proc=$row_proc['proc'];
			}else if($proc_id=="-1") {
				$proc=$rw["procedure_name"];
			}
			
			if(!empty($provID)){
				$sel_proc_nam1=imw_query("select id, fname, lname from users where id='".$provID."'");
				$row_proc1=imw_fetch_array($sel_proc_nam1);
				$prov_fname=$row_proc1['fname']." ".$row_proc1['lname'];
				$prov_id = $row_proc1['id'];
			}
			
			$fac_id=$rw["default_facility"];
			if($fac_id <= 0){
				$rs=imw_query("Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$patientid."' 
				AND sa_app_start_date<='".date('Y-m-d')."' ORDER BY sa_app_start_date DESC LIMIT 0,1");
				$facRes=imw_fetch_array($rs);
				$fac_id= $facRes['sa_facility_id'];
				if($fac_id>0){
					$fac_id= $arrAllPosFacOfFacility[$fac_id];
				}
			}
			
			$facname = $arrAllPosFacility[$fac_id];
			if(empty($facname) == false){
				$facname = strtoupper(trim(array_shift(explode('-', $facname))));
			}
			
			$office_code="01";
			$pos_fac_city_key=array_search($facname,$GLOBALS['PAM2000']);
			if($pos_fac_city_key) $office_code=$pos_fac_city_key;
			
			/* 
			$office_code="";
			if($fac_run['pos_facility_city']=='Vineland'){
				$office_code='01';
			}else if($fac_run['pos_facility_city']=='Mays Landing'){
				$office_code='02';
			}else if($fac_run['pos_facility_city']=='Hammonton'){
				$office_code='03';
			}else if($fac_run['pos_facility_city']=='Cherry Hill'){
				$office_code='04';
			}else if($fac_run['pos_facility_city']=='Blackwood'){
				$office_code='05';
			}else{
				$office_code='01';
			} */
			
			// PAM DATA
			$arrPAM[$i]['PATID'] = '"'.$patientid.'"';
			$arrPAM[$i]['MESSAGE_TYPE'] = '"02"';
			$arrPAM[$i]['OFFICE'] = '"'.$office_code.'"';
			$arrPAM[$i]['LANGUAGE_TYPE'] = '"01"';
			$arrPAM[$i]['PATIENT_FNAME'] = '"'.$pat_fname.'"';
			$arrPAM[$i]['PATIENT_LNAME'] = '"'.$pat_lname.'"';
			$arrPAM[$i]['APP_DATE'] = '"'.$recalldate.'"';
			$arrPAM[$i]['APP_TIME'] = '""';
			$arrPAM[$i]['STATUS_OPERATOR_ID'] = '"'.$prov_id.'"';
			$arrPAM[$i]['PROCEDURE_ID'] = '"'.$proc_id.'"';
			$arrPAM[$i]['PHONE'] = '"'.$phone.'"';
			$arrPAM[$i]['EMAIL'] = '"'.$pat_email.'"';
			$arrPAM[$i]['CELL_PHONE'] = '"'.$cell_phone.'"';
			// END PAM DATA----		
			$i++;
			
			$commonData .= '
            <tr>
                <td nowrap="nowrap" width="55" align="left">'.$patientid.'</td>                
                <td nowrap="nowrap" width="40" align="left">02</td>
                <td nowrap="nowrap" width="40" align="left">'.$office_code.'</td>				
                <td nowrap="nowrap" width="45" align="left">01</td>
                <td nowrap="nowrap" width="70" align="left">'.strtoupper($pat_fname).'</td>
                <td nowrap="nowrap" width="80" align="left">'.strtoupper($pat_lname).'</td>
                <td nowrap="nowrap" width="60" align="left">'.$recalldate.'</td>
                <td nowrap="nowrap" width="60" align="left">00:00</td>			
                <td nowrap="nowrap" width="110" align="left">'.$prov_fname.'</td>
				<td nowrap="nowrap" width="80" align="left">'.$proc.'</td>
				<td nowrap="nowrap" width="80" align="left">'.core_phone_format($phone).'</td>
				<td nowrap="nowrap" width="110" align="left">'.$pat_email.'</td>
				<td nowrap="nowrap" width="80" align="left">'.$cell_phone.'</td>
			</tr>';
			?>
            <tr>
                <td class="text_10" align="left"><?php echo $patientid;?></td>                
                <td class="text_10" align="left">02</td>
                <td class="text_10" align="left"><?php echo $office_code;?></td>				
                <td class="text_10" align="left">01</td>
                <td class="text_10" align="left"><?php echo strtoupper($pat_fname);?></td>
                <td class="text_10" align="left"><?php echo strtoupper($pat_lname);?></td>
                <td class="text_10" align="left"><?php echo $recalldate;?></td>
                <td class="text_10" align="left">00:00</td>			
                <td class="text_10" align="left"><?php echo $prov_fname;?></td>
				<td class="text_10" align="left"><?php echo $proc;?></td>
				<td class="text_10" align="left"><?php echo core_phone_format($phone);?></td>
				<td class="text_10" align="left"><?php echo $pat_email;?></td>
                <td class="text_10" align="left"><?php echo $cell_phone;?></td>
           </tr>
			<?php
		}
		while($rwIMM=@imw_fetch_array($resIMM)){
			$patientid=$rwIMM['patient_id'];
			$hipaa_voice=$rw['hipaa_voice'];
			$pat_lname=$rwIMM['lname'];
			$pat_fname=$rwIMM['fname'];
			$pat_det= patient_data($patientid);
			$proc_id=$rwIMM['procedure_id'];
			$oper=$rwIMM['operator'];
			$provID = $rwIMM['providerID'];
			$recalldate=$rwIMM['recalldate'];
			$pat_email=$rwIMM['email'];
			$phone_num=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['8']))));
			$phone_home=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['28']))));
			$phone_cell=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['29']))));
			$phone_biz=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$pat_det['30']))));
			
			$phone=($phone_home!='') ?  $phone_home :$phone_num;
			$cell_phone= ($phone_cell!='') ?  $phone_cell :$phone_biz;
			if($phone==$cell_phone){ $cell_phone='';}

			if($proc_id>0){
			$sel_proc_nam=imw_query("select proc from slot_procedures where id='$proc_id'");
			$row_proc=imw_fetch_array($sel_proc_nam);
			$proc=$row_proc['proc'];
			}else if($proc_id=="-1") {
				$proc=$rwIMM["procedure_name"];
			}
			
			if(!empty($provID)){
				$sel_proc_nam1=imw_query("select id, fname, lname from users where id='".$provID."'");
				$row_proc1=imw_fetch_array($sel_proc_nam1);
				$prov_fname=$row_proc1['fname']." ".$row_proc1['lname'];
				$prov_id = $row_proc1['id'];
			}
			
			$fac_id=$rwIMM["default_facility"];
			if($fac_id <= 0){
				$rs=imw_query("Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$patientid."' 
				AND sa_app_start_date<='".date('Y-m-d')."' ORDER BY sa_app_start_date DESC LIMIT 0,1");
				$facRes=imw_fetch_array($rs);
				$fac_id= $facRes['sa_facility_id'];
				if($fac_id>0){
					$fac_id= $arrAllPosFacOfFacility[$fac_id];
				}
			}
			
			$facname = $arrAllPosFacility[$fac_id];
			if(empty($facname) == false){
				$facname = strtoupper(trim(array_shift(explode('-', $facname))));
			}
			
			$office_code="01";
			$pos_fac_city_key=array_search($facname,$GLOBALS['PAM2000']);
			if($pos_fac_city_key) $office_code=$pos_fac_city_key;
			
			/* $fac_id=$rwIMM["default_facility"];
			$fac_qry=imw_query("select pos_facility_city from pos_facilityies_tbl where pos_facility_id ='$fac_id'");
			$fac_run=imw_fetch_array($fac_qry);
			
			$office_code="";
			if($fac_run['pos_facility_city']=='Vineland'){
				$office_code='01';
			}else if($fac_run['pos_facility_city']=='Mays Landing'){
				$office_code='02';
			}else if($fac_run['pos_facility_city']=='Hammonton'){
				$office_code='03';
			}else if($fac_run['pos_facility_city']=='Cherry Hill'){
				$office_code='04';
			}else if($fac_run['pos_facility_city']=='Blackwood'){
				$office_code='05';
			}else{
				$office_code='01';
			} */
		
			$arrPAM[$i]['PATID'] = '"'.$patientid.'"';
			$arrPAM[$i]['MESSAGE_TYPE'] = '"02"';
			$arrPAM[$i]['OFFICE'] = '"'.$office_code.'"';
			$arrPAM[$i]['LANGUAGE_TYPE'] = '"01"';
			$arrPAM[$i]['PATIENT_FNAME'] = '"'.$pat_fname.'"';
			$arrPAM[$i]['PATIENT_LNAME'] = '"'.$pat_lname.'"';
			$arrPAM[$i]['APP_DATE'] = '"'.$recalldate.'"';
			$arrPAM[$i]['APP_TIME'] = '""';
			$arrPAM[$i]['STATUS_OPERATOR_ID'] = '"'.$prov_id.'"';
			$arrPAM[$i]['PROCEDURE_ID'] = '"'.$proc_id.'"';
			$arrPAM[$i]['PHONE'] = '"'.$phone.'"';
			$arrPAM[$i]['EMAIL'] = '"'.$pat_email.'"';
			$arrPAM[$i]['CELL_PHONE'] = '"'.$cell_phone.'"';
			// END PAM DATA----	
			$i++;
			$commonData .= '<tr>
				<td width="55" align="left">'.$patientid.'</td>                
                <td width="40" align="left">02</td>
                <td width="40" align="left">'.$office_code.'</td>				
                <td width="45" align="left">01</td>
                <td width="70" align="left">'.strtoupper($pat_fname).'</td>
                <td width="80" align="left">'.strtoupper($pat_lname).'</td>
                <td width="60" align="left">'.$recalldate.'</td>
                <td width="60" align="left">00:00</td>			
                <td width="110" align="left">'.$prov_fname.'</td>
				<td width="80" align="left">'.$proc.'</td>
				<td width="80" align="left">'.core_phone_format($phone).'</td>
				<td width="110" align="left">'.$pat_email.'</td>
				<td width="80" align="left">'.$cell_phone.'</td>
           </tr>';
			?>
            <tr>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo $patientid;?></td>                
                <td nowrap="nowrap"  class="text_10" align="left">02</td>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo $office_code;?></td>				
                <td nowrap="nowrap"  class="text_10" align="left">01</td>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo strtoupper($pat_fname);?></td>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo strtoupper($pat_lname);?></td>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo $recalldate;?></td>
                <td nowrap="nowrap"  class="text_10" align="left">00:00</td>			
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo $prov_fname;?></td>
				<td nowrap="nowrap"  class="text_10" align="left"><?php echo $proc;?></td>
				<td nowrap="nowrap"  class="text_10" align="left"><?php echo core_phone_format($phone);?></td>
				<td nowrap="nowrap"  class="text_10" align="left"><?php echo $pat_email;?></td>
                <td nowrap="nowrap"  class="text_10" align="left"><?php echo $cell_phone;?></td>
           </tr>
<?php } $commonData .= '</table>'; ?>
    </table>
<?php
	// WRITE PAM FILE	
	for($k=0;$k<count($arrPAM);$k++){
		$exceltext.= implode(",",$arrPAM[$k]);
		$exceltext.="\n";
	}
	$filename = data_path().'users/UserId_'.$_SESSION['authId'].'/recall.txt';
	$fileInfo = pathinfo($filename);
	if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
	$fp=fopen($filename,"w");
	@fwrite($fp,$exceltext);
	@fclose($fp);
	//------------------
	}else{
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
if($num>0){
	$PdfText = $cssHTML.$pdfData.$commonData.'</page>';
	$file_location = write_html($PdfText);		
}
?>	