<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

//CREDIT TYPES
$ccTypeCtrl='';
$ccTypeCtrl.='<option value="ax">American Express</option>';
$ccTypeCtrl.='<option value="care credit">Care Credit</option>';
$ccTypeCtrl.='<option value="dis">Discover</option>';
$ccTypeCtrl.='<option value="mc">Master Card</option>';
$ccTypeCtrl.='<option value="visa">Visa</option>';
$ccTypeArr=array('ax'=>'American Express', 'care credit'=>'care credit', 'dis'=>'Discover', 'mc'=>'Master Card', 'visa'=>'Visa');
$ccTypeArr=htmlentities(serialize($ccTypeArr));

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

if($_POST['form_submitted']){
    $exclude_procedures= array_combine($exclude_procedures, $exclude_procedures);
    $exclude_status= array_combine($exclude_status, $exclude_status);
}

//--- GET Groups SELECT BOX ----
$selArrGroups = array_combine($_REQUEST['groups'],$_REQUEST['groups']);
$group_query = "Select  gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
$group_id_arr[0] = 'No Group';
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
    $group_id_arr[$group_id] = $group_res['name'];
	if($selArrGroups[$group_id])$sel='SELECTED';
	$groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}
$grp_cnt=sizeof($group_id_arr);

//--- GET Payment Method SELECT BOX ----
$pm_query = "SELECT pm_name FROM payment_methods ORDER BY pm_id ASC";
$pm_query_res = imw_query($pm_query);
$methodName = "";
while($pm_res = imw_fetch_array($pm_query_res)) {
	$sel='';
    $pm_name = $pm_res['pm_name'];
	if($_REQUEST['pay_method'] == $pm_name)$sel='SELECTED';
	$methodName .= '<option value="'.$pm_name.'" '.$sel.'>'.$pm_name.'</option>';
}

//--- GET ALL OPERATORS DETAILS ----
$op_cnt = "";
if ($dbtemp_name == "Unapplied Superbills"){ // if Unapplied Superbills report selected 
	$op_cnt = 1;
}
$selOperId= join(',',$_REQUEST['operator_id']);
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, $op_cnt, '');
$opr_cnt = sizeof(explode('</option>', $operatorOption)) - 1;

//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['facility_name'],$_REQUEST['facility_name']);
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = addslashes($fac_res['name']);
	if($selArrFacility[$fac_id])$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>'.$fac_res['name'].'</option>';
}
$sch_facility_options=addslashes($facilityName);
$fac_cnt=sizeof($fac_id_arr);

//--- GET FACILITY NAME ----
$posfacilityName = $CLSReports->getFacilityName($selArrFacility, '1');
$posfac_cnt = sizeof(explode('</option>', $posfacilityName)) - 1;


//--- GET PHYSICIAN NAME ---
$arr_phy=array_combine($_REQUEST['phyId'], $_REQUEST['phyId']);
$strPhysician = implode(',',$_REQUEST['phyId']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_sel= ($arr_phy['0']=='0')? 'SELECTED' : '';
$physicianName.= '<option value="0" '.$phy_sel.'>None</option>';
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;

//--- GET DEPARTMENT NAME ---
$strdepartment = implode(',',$_REQUEST['department']);
$departmentName = $CLSReports->get_department_dropdown($strdepartment);
$dept_cnt = sizeof(explode('</option>', $departmentName)) - 1;

$timeOptions = '<option value="0">00 am</option>';
for ($i = 1; $i <= 23; $i++) {
    $fromSel = $toSel = '';
    $ampm = 'am';
    $num = $i;
    if ($i > 11) {
        if ($i > 12)
            $num = $i - 12;
        $ampm = 'pm';
    }
    if ($num < 10)
        $num = '0' . $num;

    if ($_POST['hourFrom'] == $i)
        $fromSel = 'SELECTED';
    $timeHourFromOptions .= '<option value="' . $i . '" ' . $fromSel . '>' . $num . ' ' . $ampm . '</option>';

    if ($_POST['hourTo'] == $i)
        $toSel = 'SELECTED';
    $timeHourToOptions .= '<option value="' . $i . '" ' . $toSel . '>' . $num . ' ' . $ampm . '</option>';
}

//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
    if ($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
        $ins_id = $insQryRes[$i]['attributes']['insCompId'];
        $ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
        $ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
        if ($ins_name == '') {
            $ins_name = $insQryRes[$i]['attributes']['insCompName'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }
				
				$sel = '';
				if (sizeof($ins_carriers) > 0) {
					if (in_array($ins_id,$ins_carriers)) {
							$sel = 'selected';
					}
        }

        $ins_comp_arr[$ins_id] = $ins_name;
        if ($insQryRes[$i]['attributes']['insCompStatus'] == 0)
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel.">" . $ins_name . "</option>";
        else
            $insComName_options .= "<option value='" . $ins_id . "' ".$sel." style='color:red'>" . $ins_name . "</option>";

        
    }
}
$insurance_cnt = sizeof($ins_comp_arr);


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
        $selected = '';
        $grp_ins_ids = implode(",", $tmp_grp_ins_arr);
        $ins_group_arr[$grp_ins_ids] = $ins_grp_name;

        if (in_array($grp_ins_ids,$ins_group))
            $selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}

//--- GETTING SCHEDULER PROCEDURES---------------
$schPro_qry_res = imw_query("SELECT id, proc, acronym FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id =0 AND active_status = 'yes' AND user_group <> '' ORDER BY proc");
$procDetails = array();
while ($procRow = imw_fetch_assoc($schPro_qry_res)) {
	$procDetails[] = $procRow;
}
$proc_options_arr = array();
for($i=0;$i<count($procDetails);$i++){
	$proc_options_dr_arr = array();
	$procId_arr=array();
	$procId = $procDetails[$i]['id'];
	$procName = $procDetails[$i]['proc'];
	$subQry = imw_query("SELECT id, proc, acronym FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id != 0 AND active_status = 'yes' and procedureId='$procId' ORDER BY proc");
	$procDetails_dr = array();
	while ($subRow = imw_fetch_assoc($subQry)) {
		$procDetails_dr[] = $subRow;
	}
	$procId_arr[]=$procId;
	for($k=0;$k<count($procDetails_dr);$k++){
		$procId_arr[]=$procDetails_dr[$k]['id'];
	}
	$procId_imp=implode(',',$procId_arr);
	$proc_options_arr[$procId_imp] = $procName;
}
if(count($proc_options_arr) > 0){
	foreach($proc_options_arr as $procKey => $proVal){
		$sel = '';
		if($exclude_procedures[$procKey]) $sel='SELECTED';
		$proc_options .= '<option value="' . $procKey . '" '.$sel.' >' . $proVal . '</option>';
	}
}

//-----GETTING APPOINTMENT STATUS----
$appt_query = "SELECT id, status_name FROM schedule_status WHERE id NOT IN (1,5,19,201,203)";
$appt_query_res = imw_query($appt_query);
$appt_opts_arr = array();
$apptOption = "";
while ($apptDetails = imw_fetch_array($appt_query_res)) {
	$sel='';
    $apptId = $apptDetails['id'];
    $appt_opts_arr[$apptId] = $apptDetails['status_name'];
	if($exclude_status[$apptId])$sel="SELECTED";

    $apptOption .= '<option value="' . $apptDetails['id'] . '" '.$sel.'>' . $apptDetails['status_name'] . '</option>';
}

if($temp_id){
	$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='$temp_id' and `delete_status` = 0");
	if(imw_num_rows($sql_query) > 0){
		$row = imw_fetch_assoc($sql_query);
		$dbtemp_id  = $row['id'];
		$dbtemp_name  = $row['template_name'];
		$default_report  = $row['default_report'];
		$dbtemplate_fields  = unserialize($row['template_fields']);
	}
	$filter_arr = array();
	foreach($dbtemplate_fields as $obj){
		$filter_arr[$obj['name']] = $obj['value'];
	}
}

//For Adjustment Report Starts
if ($dbtemp_name == 'Adjustment Report') {
    $selwriteOffData= $_REQUEST['wrt_code'];
	  $selwriteOffData=array_combine($selwriteOffData,$selwriteOffData);
	  
    //writeoff code
    $wsql = "select w_id,w_code from write_off_code where trim(w_code) != '' order by w_code";
    $wres = imw_query($wsql);
    $writeOffData = array();
    if (imw_num_rows($wres) > 0) {
        while ($writeOffQryRes = imw_fetch_array($wres)) {
            $w_id = $writeOffQryRes['w_id'];
            $w_code = $writeOffQryRes['w_code'];
            $writeOffData['WC_' . $w_id] = $w_code;
        }
        
        $writeOffData_options = '<optgroup label="Write Off Code">';
        foreach ($writeOffData as $wkey => $wrow_name) {
            $wselected = '';
            if ($selwriteOffData[$wkey]) {
                $wselected = 'selected="selected"';
            }
            $writeOffData_options .= "<option value='" . $wkey . "' " . $wselected . ">" . $wrow_name . "</option>";
        }
			$wselected = ($selwriteOffData['WC_0'])? 'selected="selected"' : '';
			$writeOffData_options .= '<option value="WC_0" '. $wselected .'>Other</option>';
			$writeOffData_options .= '</optgroup>';   
    }
		
	
    //discount code
    $dsql = "select d_id,d_code from discount_code where trim(d_code) != '' order by d_code";
    $dres = imw_query($dsql);
    $discountData = array();
    if (imw_num_rows($dres) > 0) {
        while ($discountQryRes = imw_fetch_array($dres)) {
            $d_id = $discountQryRes['d_id'];
            $d_code = $discountQryRes['d_code'];
            $discountData['DC_' . $d_id] = $d_code;
        }
        
        $discountData_options = '<optgroup label="Discount Code">';
        foreach ($discountData as $dkey => $drow_name) {
            $dselected = '';
            if ($selwriteOffData[$dkey]) {
                $dselected = 'selected="selected"';
            }
            $discountData_options .= "<option value='" . $dkey . "' " . $dselected . ">" . $drow_name . "</option>";
        }
			$dselected= ($selwriteOffData['DC_0']) ? 'selected="selected"' : '';
			$discountData_options .= '<option value="DC_0" '.$dselected.'>Other</option>';   
			$discountData_options .= '</optgroup>';
    }
    //adjust code
    $acsql = "select a_id,a_code from adj_code where trim(a_code) != '' order by a_code";
    $acres = imw_query($acsql);
    $adjustData = array();
    if (imw_num_rows($acres) > 0) {
        while ($adjustcodeQryRes = imw_fetch_array($acres)) {
            $a_id = $adjustcodeQryRes['a_id'];
            $a_code = $adjustcodeQryRes['a_code'];
            $adjustData['AC_' . $a_id] = $a_code;
        }
        
        $adjustData_options = '<optgroup label="Adjustment Code">';
        foreach ($adjustData as $akey => $arow_name) {
            $aselected = '';
            if ($selwriteOffData[$akey]) {
                $aselected = 'selected="selected"';
            }
            $adjustData_options .= "<option value='" . $akey . "' " . $aselected . ">" . $arow_name . "</option>";
        }
        $aselected= ($selwriteOffData['AC_0']) ? 'selected="selected"' : '';
			 $adjustData_options .= '<option value="AC_0" '.$aselected.'>Other</option>';
			 $adjustData_options .= '</optgroup>';
    }
	
	//Reason code
	$acsql = "select cas_id,cas_code from cas_reason_code where trim(cas_code) != '' order by cas_code";
    $acres = imw_query($acsql);
    $reasonData = array();
    if (imw_num_rows($acres) > 0) {
        while ($reasoncodeQryRes = imw_fetch_array($acres)) {
            $cas_id = $reasoncodeQryRes['cas_id'];
            $cas_code = $reasoncodeQryRes['cas_code'];
            $reasonData['RC_' . $cas_id] = $cas_code;
        }
        
        $reasonData_options = '<optgroup label="Reason Code">';
        foreach ($reasonData as $akey => $arow_name) {
            $aselected = '';
            if ($selwriteOffData[$akey]) {
                $aselected = 'selected="selected"';
            }
            $reasonData_options .= "<option value='" . $akey . "' " . $aselected . ">" . $arow_name . "</option>";
        }
        $reasonData_options .= '</optgroup>';
    }
    
    $allWriteoffCount = sizeof($writeOffData) + sizeof($discountData) + sizeof($adjustData) + sizeof($reasonData);
    
    //-  GET ACTB TYPE AHEAD FOR BATCHES
    $strTypeAhead = array();
    $sel_qry=imw_query("select tracking from manual_batch_file WHERE del_status='0' AND post_status='1'");
    while($sel_rs=imw_fetch_array($sel_qry)){
        $strTypeAhead[] = $sel_rs['tracking'];	
    }
}
//For Adjustment Report Ends

//CSV NAME
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";

//REPORT LOGIC INFORMATION FLOATING DIV
$logicWidth = 50;
if($dbtemp_name == "Unapplied Payments"){
	$logicDiv = reportLogicInfo('unapplied_payments', 'tpl', $logicWidth);
}elseif($dbtemp_name == "Daily Balance"){
	$logicDiv = reportLogicInfo('daily_balance', 'tpl', $logicWidth);
}elseif($dbtemp_name == "Unapplied Superbills"){
	$logicDiv = reportLogicInfo('unapplied_superbills', 'tpl', $logicWidth);
}elseif($dbtemp_name == "Payments"){
	$logicDiv = reportLogicInfo('payments', 'tpl', $logicWidth);
}elseif($dbtemp_name == "Day Sheet"){
	$logicDiv = reportLogicInfo('day_sheet', 'tpl', $logicWidth);
}elseif($dbtemp_name == "Adjustment Report"){
	$logicDiv = reportLogicInfo('adjustment', 'tpl', $logicWidth);
}elseif($dbtemp_name == "Unfinalized Encounters"){
	$logicDiv = reportLogicInfo('unfinalized_encounters', 'tpl', $logicWidth);
}elseif($dbtemp_name == "Copay Reconciliation"){
	$logicDiv = reportLogicInfo('copay_reconciliation', 'tpl', $logicWidth);
}
?> 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: imwemr ::</title>
        <!-- Bootstrap -->
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
              <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
              <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->

        <style>
            .pd5.report-content {
                position:relative;
                margin-left:40px;

                background-color: #EAEFF5;
            }
            .fltimg {
                position:absolute;
            }
            .fltimg span.glyphicon {
                position: absolute;
                top: 170px;
                left: 10px;
                color: #fff;
            }
            .reportlft .btn.btn-mkdef {
                padding-top: 6px;
                padding-bottom: 6px;
            }
            #content1{
                background-color:#EAEFF5;
            }
			.total-row {
				height: 1px;
				padding: 0px;
				background: #009933;
			}	
		</style>
    </head>
    <body>
        <form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">
            <input name="processReport" id="processReport" autocomplete="off" value="Daily" type="hidden">
            <input type="hidden" name="SchedulerNewReport" id="SchedulerNewReport" value="">
            <input type="hidden" name="Submit" id="Submit" value="get reports">
            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
            <div class=" container-fluid">
                <div class="anatreport">
                    <div id="provider_drop" style="position:absolute;bottom:0px;"></div>
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Practice Filter
									<?php if($dbtemp_name == "Payments" || $dbtemp_name == "Unapplied Payments" || $dbtemp_name == "Daily Balance" 
									|| $dbtemp_name == "Unapplied Superbills" || $dbtemp_name == "Day Sheet" || $dbtemp_name == "Adjustment Report"
									|| $dbtemp_name == "Unfinalized Encounters" || $dbtemp_name == "Copay Reconciliation"){ ?>
										<div id="rptInfoImg" style="float:right" class="rptInfoImg" onClick="showHideReportInfo(event, '<?php echo $logicWidth;?>')"></div>
									<?php }?>	
									</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        
										<div class="row">
											<?php if($dbtemp_name == "Adjustment Report"){?>
											<div class="col-sm-5">
												<div class="checkbox checkbox-inline pointer">
													<input type="checkbox" name="billing_location" id="billing_location" value="1" <?php if($_POST['billing_location']=='1') echo 'CHECKED'; ?> onClick="show_facility('billing_location');" /> 
													<label for="billing_location">Billing Location</label>
												</div>
											</div>                                            								
											<?php }?>											
   											
											<?php if($dbtemp_name == "Payments" || $dbtemp_name == "Day Close Payment Report"){?>
                                            <div class="col-sm-7">
                                            	<div class="checkbox checkbox-inline pointer">
													<input type="checkbox" name="pay_location" id="pay_location" value="1" <?php if($_POST['pay_location']=='1') echo 'CHECKED'; ?> onClick="show_facility('pay_location');" /> 
													<label for="pay_location">Pay Location</label>
												</div>
											</div>                                            
											<?php }?>
											<div class="clearfix"></div>
                                            <div class="col-sm-4">
                                                <label>Groups</label>
                                                <select name="groups[]" id="groups" data-container="#provider_drop" class="selectpicker" <?php echo (!isset($filter_arr['groups']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $groupName; ?>
												</select>
                                            </div>
											<div class="col-sm-4">
                                                <label>Facility</label>
                                                <select name="facility_name[]" id="facility_name" class="selectpicker" data-container="#provider_drop" <?php echo (!isset($filter_arr['facility']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php 
													if($dbtemp_name == "Payments" || $dbtemp_name=="Adjustment Report" || $dbtemp_name == "Day Close Payment Report" || $default_report == '0'){
													 	echo $posfacilityName; 
													}else{
                                                        echo $facilityName;
													}
													 ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Provider</label>
                                                <select name="phyId[]" id="phyId" data-container="#provider_drop" class="selectpicker" <?php echo (!isset($filter_arr['physician']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $physicianName; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Operator</label>
                                                <select name="operator_id[]" id="operator_id" data-container="#provider_drop" class="selectpicker" <?php echo (!isset($filter_arr['operators']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $operatorOption; ?>
                                                </select>
                                            </div>
											
											<div class="col-sm-8">
                                                <label>Period</label>
												<?php if($dbtemp_name == "Day Sheet") {?>	
													<div class="input-group">
														<input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick">
														<label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
													</div>
												<?php } else {?>	
													
                                                <div id="dateFieldControler">
                                                    <select name="dayReport" id="dayReport" data-container="#provider_drop" class="selectpicker" <?php echo (!isset($filter_arr['date_range']))?'disabled':''; ?> data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
                                                        <option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'selected="selected"'; ?>>Daily</option>
                                                        <option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'selected="selected"'; ?>>Weekly</option>
                                                        <option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'selected="selected"'; ?>>Monthly</option>
                                                        <option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'selected="selected"'; ?>>Quarterly</option>
                                                        <option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'selected="selected"'; ?>>Date Range</option>
                                                    </select>
                                                </div>
                                                <div class="row" style="display:none" id="dateFields">
                                                    <div class="col-sm-5">
                                                        <div class="input-group">
                                                            <input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                        </div>
                                                    </div>	
                                                    <div class="col-sm-5">	
                                                        <div class="input-group">
                                                            <input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo $_REQUEST['End_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button type="button" class="btn" onclick="DateOptions('x');"><span class="glyphicon glyphicon-arrow-left"></span></button>
                                                    </div>
                                                </div>
												<?php } ?>	
                                            </div>
											<?php if ($dbtemp_name == 'Adjustment Report') { ?>
                                                <div class="col-sm-6">
                                                    <label>Adjustment Code</label>
                                                    <select class="selectpicker" name="wrt_code[]" id="wrt_code" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                        <?php echo $writeOffData_options; ?>
                                                        <?php echo $discountData_options; ?>
                                                        <?php echo $adjustData_options; ?>
                                                        <?php echo $reasonData_options; ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="Summary">Batch Track#</label>
                                                    <div class="input-group">
                                                        <input type="text" name="batchFiles" id="batchFiles" value="" data-provide="multiple" class="form-control" placeholder="Batch Track" onblur="enableDisableControls(this.value,'batchCtrl')" />
                                                    </div>
                                                </div>
                                            <?php } ?>
											<?php if ($dbtemp_name == "Unapplied Superbills"){?>
												<div class="col-sm-6">
                                                <label>Encounter Status</label>
													<select class="selectpicker" name="enctStatus[]" id="enctStatus" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
														<option value="noSuperBill" <?php if (in_array('noSuperBill', $_POST['enctStatus'])) echo 'selected="selected"'; ?>>No SuperBill</option>
														<option value="chargesNotEntered" <?php if (in_array('chargesNotEntered', $_POST['enctStatus'])) echo 'selected="selected"'; ?>>Charges Not Entered</option>
														<option value="chargesNotPosted" <?php if (in_array('chargesNotPosted', $_POST['enctStatus'])) echo 'selected="selected"'; ?>>Charges Not Posted</option>                  
														<option value="chargesNotSubmitted" <?php if (in_array('chargesNotSubmitted', $_POST['enctStatus'])) echo 'selected="selected"'; ?>>Charges Not Submitted</option>
													</select>
												</div>
											<?php } else { ?>
												<div class="col-sm-6">
													<label>Department</label>
													<select name="department[]" id="department" class="selectpicker" <?php echo (!isset($filter_arr['department']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
														<?php echo $departmentName; ?>
													</select>
												</div>
											<?php } ?>	
                                            <div class="col-sm-6">
												<br />
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="summary_detail" id="Summary" value="Summary" checked <?php if ($_POST['summary_detail'] == 'Summary') echo 'CHECKED'; ?> <?php echo (!isset($filter_arr['summary_detail']))?'disabled':''; ?>/> 
                                                    <label for="Summary">Summary</label>
                                                </div>
												<div class="radio radio-inline pointer">
                                                    <input type="radio" name="summary_detail" id="Detail" value="Detail" <?php if ($_POST['summary_detail']=='Detail' || $dbtemp_name == 'Day Close Payment Report') echo 'CHECKED'; ?> <?php echo (!isset($filter_arr['summary_detail']))?'disabled':''; ?>/> 
                                                    <label for="Detail">Detail</label>
                                                </div>
                                            </div>
											<div class="col-sm-9"><br />
													<div class="radio radio-inline pointer">
														<input type="radio" name="DateRangeFor" id="dos" value="dos" <?php if ($_POST['DateRangeFor'] == 'dos') echo 'CHECKED'; ?> <?php echo (!isset($filter_arr['dos']))?'disabled':''; ?>/> 
														<label for="dos">DOS</label>
													</div>
													<div class="radio radio-inline pointer">
														<input type="radio" name="DateRangeFor" id="doc" value="doc" <?php if ($_POST['DateRangeFor'] == 'doc') echo 'CHECKED'; ?> <?php echo (!isset($filter_arr['doc']))?'disabled':''; ?>/> 
														<label for="doc">DOC</label>
													</div>
													<div class="radio radio-inline pointer">
														<input type="radio" name="DateRangeFor" id="dor" value="dor" <?php if ($_POST['DateRangeFor'] == 'dor') echo 'CHECKED'; ?> <?php echo (!isset($filter_arr['dor']))?'disabled':''; ?> onClick="show_hide_controls();"   /> 
														<label for="dor">DOR</label>
													</div>
													<div class="radio radio-inline pointer">
														<input type="radio" name="DateRangeFor" id="dot" value="dot" <?php if ($_POST['DateRangeFor'] == 'dot' || !$_POST['form_submitted']) echo 'CHECKED'; ?> <?php echo (!isset($filter_arr['dot']))?'disabled':''; ?> onClick="show_hide_controls();"/> 
														<label for="dot">DOT</label>
													</div>
											</div>
											<div class="col-sm-3">
													<div class="row">
														<div class="col-sm-6">
															<label for="hourFrom">Time</label>
															<select name="hourFrom" id="hourFrom" class="form-control minimal" <?php echo (!isset($filter_arr['time_range']))?'disabled':''; ?>>
																<option value="">-</option>
																<?php echo $timeHourFromOptions; ?>
															</select>
														</div>
														<div class="col-sm-6">
															<label for="hourTo"></label>
															<select name="hourTo" id="hourTo" class="form-control minimal" <?php echo (!isset($filter_arr['time_range']))?'disabled':''; ?>>
																<option value="">-</option>
																<?php echo $timeHourToOptions; ?>
															</select>	
														</div>
													</div>
											</div>
											
											<?php if($dbtemp_name == "Unapplied Superbills") { ?>
											<div class="col-sm-12">
												<div class="checkbox checkbox-inline pointer">
													<input type="checkbox" name="ExRescheduleAppts" id="ExRescheduleAppts" value="1" <?php if ($_POST['ExRescheduleAppts']=='1') echo 'CHECKED'; ?>/> 
													<label for="ExRescheduleAppts">Exclude Reschedule Appointments</label>
												</div>
											</div>
											<?php } if($dbtemp_name == "Unfinalized Encounters") { ?>
												<div class="col-sm-4">
												  <div class="checkbox checkbox-inline pointer">
													<input type="checkbox" name="charts" id="charts" value="1" <?php if ($_POST['charts']=='1') echo 'CHECKED'; ?>/> 
													<label for="charts">Charts Only</label>
												  </div>
												</div>
												<div class="col-sm-8">
												  <div class="checkbox checkbox-inline pointer">
													<input type="checkbox" name="exclude_user_types" id="exclude_user_types" value="1" <?php if ($_POST['exclude_user_types']=='1') echo 'CHECKED'; ?>/> 
													<label for="exclude_user_types">Exclude User Types Filter</label>
												  </div>
												</div>
											<?php } ?>
											<?php if($dbtemp_name == "Payments" || $dbtemp_name == "Day Close Payment Report"){?>
											<div class="col-sm-6">
                                            	<div class="checkbox checkbox-inline pointer">
                                                	<input type="checkbox" name="consolidation" id="consolidation" value="1" <?php if(trim($_REQUEST['Submit'])== '' || $_POST['consolidation']=='1') echo 'CHECKED'; ?>/> 
                                                	<label for="consolidation">Consolidation</label>
                                            	</div>
											</div>                                            
                                            <?php }?>
											<?php if($dbtemp_name == "Daily Balance"){?>
											<div class="col-sm-6" style="display:none" id="div_not_display_deleted_block">
                                            	<div class="checkbox checkbox-inline pointer">
                                                	<input type="checkbox" name="not_display_deleted_block" id="not_display_deleted_block" value="1" <?php if($_POST['not_display_deleted_block']=='1') echo 'CHECKED'; ?>/> 
                                                	<label for="not_display_deleted_block">Not Display Deleted Block</label>
                                            	</div>
											</div>                                            
                                            <?php }?>
										</div>
                                    </div>
                                </div>
								<div class="appointflt">
                                    <div class="anatreport"><h2>Analytic Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
											<div class="col-sm-6">
                                                <label>Ins. Group</label>
                                                <select name="ins_group[]" id="ins_group" <?php echo (!isset($filter_arr['ins_group']))?'disabled':''; ?> class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $ins_group_options; ?>
                                                </select>
                                            </div>
											<div class="col-sm-6">
                                                <label>Ins. Carrier</label>
                                                <select name="ins_carriers[]" id="ins_carriers" class="selectpicker" <?php echo (!isset($filter_arr['ins_carriers']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $insComName_options; ?>
                                                </select>
                                            </div>
											<div class="col-sm-6">
												<label for="pay_method">Payment Method</label>
												<select class="selectpicker" name="pay_method" id="pay_method" <?php echo (!isset($filter_arr['payment_method']))?'disabled':''; ?> data-width="100%" data-size="10" onchange="dispCCTypes(this.value);">
													<option value="">Select</option>
													<?php echo $methodName; ?>
												</select>
                                            </div>
                                            
											<?php if($dbtemp_name == "Copay Reconciliation")
											{
											?>
											<div class="col-sm-6">
												<label for="report_type">Exclude Procedures</label>
                                                <select name="exclude_procedures[]" id="exclude_procedures" class="selectpicker" data-container="#provider_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="None">
                                                    <?php echo $proc_options;?>
												</select>
                                            </div>
											<div class="col-sm-6">
												<label for="report_type">Exclude Appt. Status</label>
												<select name="exclude_status[]" id="exclude_status" class="selectpicker" data-container="#provider_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="None">
													<?php echo $apptOption;?>
												</select>
                                            </div>  
											<div class="col-sm-6 mt20" >
												<div class="checkbox checkbox-inline pointer">
													<input type="checkbox" name="copay_not_collected" id="copay_not_collected" value="1" <?php if ($_POST['copay_not_collected'] == '1') echo 'CHECKED'; ?> /> 
													<label for="copay_not_collected">Copay Not Collected</label>
												</div>
											</div>                                                                                      
											<?php
											}
											?>

											<?php if($dbtemp_name == "Daily Balance")
											{
											?>	
											<div class="col-sm-6">
												<label for="report_type">Report Type</label>
												<select class="selectpicker" name="report_type" data-container="#provider_drop" id="report_type"data-width="100%">
													<option value="">Select</option>
													<option value="WithDeleted" <?php if ($_REQUEST['report_type'] =='WithDeleted') echo 'SELECTED'; ?>>With Deleted/Applied</option>
													<option value="WithoutDeleted" <?php if ($_REQUEST['report_type'] =='WithoutDeleted') echo 'SELECTED'; ?>>Without Deleted/Applied</option>
												</select>
											</div>
											<?php
											}
											?>
											<div class="col-sm-6" id="ccdiv" style="display:none">
                                                <label for="multiple">CC Types</label>
                                                <select class="selectpicker" name="cc_type[]" id="cc_type" multiple data-actions-box="true" data-width="100%" data-size="10">
                                                    <option value="ax" <?php if (in_array('ax',$_POST['cc_type'])) echo 'SELECTED'; ?>>American Express</option>
                                                    <option value="care credit" <?php if (in_array('care credit',$_POST['cc_type'])) echo 'SELECTED'; ?>>Care Credit</option>
                                                    <option value="dis" <?php if (in_array('dis',$_POST['cc_type'])) echo 'SELECTED'; ?>>Discover</option>
                                                    <option value="mc" <?php if (in_array('mc',$_POST['cc_type'])) echo 'SELECTED'; ?>>Master Card</option>
                                                    <option value="visa" <?php if (in_array('visa',$_POST['cc_type'])) echo 'SELECTED'; ?>>Visa</option>
                                                </select>
                                            </div>
                                            
											<div class="col-sm-12">
												<div class="checkbox checkbox-inline pointer">
													<input type="checkbox" name="un_processed" id="un_processed" value="1" <?php if ($_POST['un_processed'] == '1') echo 'CHECKED'; ?> <?php echo (!isset($filter_arr['un_processed']))?'disabled':''; ?>/> 
													<label for="un_processed">Un-processed</label>
												</div>
											</div>
										</div>
									</div>
                                </div>
								<div class="grpara">
                                    <div class="anatreport"><h2>Group BY</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_groups" <?php echo (!isset($filter_arr['grpby_groups']))?'disabled':''; ?> value="grpby_groups" <?php if ($_POST['grpby_block'] == 'grpby_groups') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_groups">Groups</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_facility" <?php echo (!isset($filter_arr['grpby_facility']))?'disabled':''; ?> value="grpby_facility" <?php if ($_POST['grpby_block'] == 'grpby_facility') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_facility">Facility</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_physician" <?php echo (!isset($filter_arr['grpby_physician']))?'disabled':''; ?> value="grpby_physician" <?php if ($_POST['grpby_block'] == 'grpby_physician') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_physician">Physician</label>
                                                </div>
                                            </div>                                                
                                            <div class="col-sm-4">	
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_operators" <?php echo (!isset($filter_arr['grpby_operators']))?'disabled':''; ?> value="grpby_operators" <?php if ($_POST['grpby_block'] == 'grpby_operators' || ($dbtemp_name == 'Copay Reconciliation') || ($dbtemp_name == 'Adjustment Report') ) echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_operators">Operators</label>
                                                </div>
                                            </div>
											<div class="col-sm-4">	
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_department" <?php echo (!isset($filter_arr['grpby_department']))?'disabled':''; ?> value="grpby_department" <?php if ($_POST['grpby_block'] == 'grpby_department') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_department">Department</label>
                                                </div>
                                            </div>
											<div class="col-sm-4">	
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="grpby_block" id="grpby_view_order" <?php echo (!isset($filter_arr['grpby_view_order']))?'disabled':''; ?> value="grpby_view_order" <?php if ($_POST['grpby_block'] == 'grpby_view_order') echo 'CHECKED'; ?>/> 
                                                    <label for="grpby_view_order">View Order</label>
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                </div>    
                                			
								<div class="grpara">
                                    <div class="anatreport"><h2>Include</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
                                          	<div class="col-sm-3">
                                            	<div class="checkbox checkbox-inline pointer">
                                                	<input type="checkbox" name="inc_appt" id="inc_appt" <?php echo (!isset($filter_arr['inc_appt']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_appt'] == '1') echo 'CHECKED'; ?>/> 
                                                	<label for="inc_appt">Appt</label>
                                            	</div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_trans" id="inc_trans" <?php echo (!isset($filter_arr['inc_trans']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_trans'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_trans">Transactions</label>
                                                </div>
                                            </div>                                                
                                            <div class="col-sm-5">
                                                <div class="checkbox checkbox-inline pointer">
                                                    <input type="checkbox" name="inc_count_summary" id="inc_count_summary" <?php echo (!isset($filter_arr['inc_count_summary']))?'disabled':''; ?> value="1" <?php if ($_POST['inc_count_summary'] == '1') echo 'CHECKED'; ?>/> 
                                                    <label for="inc_count_summary">Count Summary</label>
                                                </div>
                                            </div>  
										</div>
                                    </div>
                                </div>     

								<div class="grpara">
                                    <div class="anatreport"><h2>Format</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
                                           <div class="col-sm-4">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="output_option" id="output_view_only" <?php echo (!isset($filter_arr['output_view_only']))?'disabled':''; ?> value="view" <?php if ($_POST['output_option'] == 'view' || $_POST['output_option'] == '') echo 'CHECKED'; ?>/> 
                                                    <label for="output_view_only">View Only</label>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="output_option" id="output_pdf" <?php echo (!isset($filter_arr['output_pdf']))?'disabled':''; ?> value="output_pdf" <?php if ($_POST['output_option'] == 'output_pdf') echo 'CHECKED'; ?>/> 
                                                    <label for="output_pdf">PDF</label>
                                                </div>
                                            </div>                                                
                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer" style="padding-left: 42%;">
                                                    <input type="radio" name="output_option" id="output_csv" <?php echo (!isset($filter_arr['output_csv']))?'disabled':''; ?> value="output_csv" <?php if ($_POST['output_option'] == 'output_csv') echo 'CHECKED'; ?>/> 
                                                    <label for="output_csv">CSV</label>
                                                </div>
                                            </div>  
										</div>
                                    </div>
									
                                </div>                                                                                        
															<div class="clearfix">&nbsp;</div>
                            </div>
                            
                            <div id="module_buttons" class="ad_modal_footer text-center">
                            	<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
                           	</div>
                                    
                        </div>
                        <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="toggle-sidebar glyphicon glyphicon-chevron-left"></span>
                            </div>
							<div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
                                        <?php
										if($_POST['form_submitted'] && $dbtemp_name == "Day Sheet") {
											include('billing_report_result.php');
										}elseif($_POST['form_submitted'] && $dbtemp_name == "Payments") {
                                            include('payments_result.php');
                                        }elseif($_POST['form_submitted'] && $dbtemp_name == "Daily Balance") {
                                            include('daily_balance_result.php');
										}elseif($_POST['form_submitted'] && $dbtemp_name == "Unapplied Superbills") {
                                            include('missing_encounters_report.php');
										}elseif($_POST['form_submitted'] && $dbtemp_name == "Unapplied Payments") {
                                            include('unapplied_amounts_result.php');			
                                        }elseif($_POST['form_submitted'] && $dbtemp_name == "Unfinalized Encounters") {
                                            include('unfinalized_encounters_result.php');
                                        }elseif($_POST['form_submitted'] && $dbtemp_name == "Copay Reconciliation") {
                                            include('copay_reconciliation_result.php');
                                        }elseif($_POST['form_submitted'] && $dbtemp_name == "Adjustment Report") {
                                            include('adjustment_report_result.php');
                                        }elseif($_POST['form_submitted'] && $dbtemp_name == "Day Close Payment Report") {
                                            include('day_close_payment_result.php');											
                                        }elseif($_POST['form_submitted'] && $dbtemp_name == "Prepayments") {
                                            include('prepayments_result.php');																					
										}elseif($_POST['form_submitted']) {
											include('daily_custom_result.php');
										}else{
                                            echo '<div class="text-center alert alert-info">No Search Done.</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
							<?php echo $logicDiv;?>
                        </div>
                    </div>
				</div>
            </div>
        </form>
<?php if($dbtemp_name == "Adjustment Report" || $dbtemp_name == "Day Sheet" || $dbtemp_name == "Daily Balance" || $dbtemp_name == "Unapplied Superbills" || $dbtemp_name == "Unapplied Payments" || $dbtemp_name == "Copay Reconciliation"){ ?> 
	<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
		<input type="hidden" name="file_format" id="file_format" value="csv">
		<input type="hidden" name="zipName" id="zipName" value="">	
		<input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
	</form> 
<?php } else { ?>
	<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
		<input type="hidden" name="csv_text" id="csv_text">	
		<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
	</form> 
<?php } ?>
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var conditionChk = '<?php echo $conditionChk; ?>';
	var pdfbtnCheck = '<?php echo $filter_arr['output_pdf']; ?>';
	var csvbtnCheck = '<?php echo $filter_arr['output_csv']; ?>';
	var op='l';
	var output='<?php echo $_POST['output_option'];?>';
	var HTMLCreated='<?php echo $HTMLCreated; ?>';
	var mainBtnArr = new Array();
	var btncnt=0;
	var dbtemp_name = "<?php echo $dbtemp_name; ?>";
	
	
	if(conditionChk==true){
		if (pdfbtnCheck == '1') {
			mainBtnArr[btncnt] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
			btncnt++;
		}
		if (csvbtnCheck == '1') {
			if(dbtemp_name == "Adjustment Report" || dbtemp_name == "Day Sheet" || dbtemp_name == "Daily Balance" || dbtemp_name == "Unapplied Superbills" || dbtemp_name == "Unapplied Payments" || dbtemp_name == "Copay Reconciliation"){
				mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
				btncnt++;
			} else {
				mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
				btncnt++;
			}
		}
	}
	top.btn_show("PPR", mainBtnArr);
	
	function generate_pdf(op) {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, op);
			window.close();
		}
	}
	function get_sch_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		document.sch_report_form.submit();
	}


	
	/* function dispCCTypes(cc_val){
		alert(cc_val);
		if(cc_val=='credit card'){
		   	var ccTypeCtrl='<select class="selectpicker" multiple name="cc_type" id="cc_type">';
			ccTypeCtrl+='<option value="">Select</option>';
			ccTypeCtrl+='<option>'+ccTypeCtrl+'<option>';
			ccTypeCtrl+='</select>';
			ccTypeCtrl+='<div class="label">CC Type</div>';
			$('#ccdiv').html(ccTypeCtrl);
			$('#ccdiv').show();			
			$('#cc_type').val(' ');
		}else{
			$('#ccdiv').html('');
		}
	} */
	
	function chk_temp(val) {
		document.getElementById("spanTemp1Id").style.display = 'inline-block';
		document.getElementById("spanTemp2Id").style.display = 'none';
		if (val == "Recall letter") {
			document.getElementById("recallTemplatesListId").disabled = false;
		} else if (val == "Package") {
			document.getElementById("spanTemp1Id").style.display = 'none';
			document.getElementById("spanTemp2Id").style.display = 'inline-block';
			document.getElementById("packageListId").value = '';
		} else {
			document.getElementById("recallTemplatesListId").value = '';
			document.getElementById("recallTemplatesListId").disabled = true;
		}

		if (val == 'Address Labels')
			$('#dymoOptions').slideDown();
		else
			$('#dymoOptions').slideUp();
		$('#sel_dymo').prop('checked', false);
		document.getElementById("dymoPrintersList").disabled = true;
	}

	$(document).ready(function () {
		DateOptions("<?php echo $_POST['dayReport']; ?>");
		$(".toggle-sidebar").click(function () {
			$("#sidebar").toggleClass("collapsed");
			$("#content1").toggleClass("col-md-12 col-md-9");

			if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
			} else {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
			}
			return false;
		});
        
        $("#batchFiles").val("<?php echo $batchFiles;?>");
		// TYPE HEAD BATCH FILES
		var arrTypeAhead= new Array();
		var c=0;
		<?php foreach($strTypeAhead as $foo) {?>
			arrTypeAhead[c]="<?php echo $foo;?>";
			c++;
        <?php } ?>
        $('#batchFiles').typeahead({source:arrTypeAhead});
		
		if(dbtemp_name == 'Payments'){
			show_facility();
			dispCCTypes('<?php echo $_POST['pay_method'];?>');
		}
		
		if(dbtemp_name == 'Daily Balance'){
			show_hide_controls();
		}
		
		if(dbtemp_name != 'Prepayments'){
			show_facility('<?php echo $p=($_POST['billing_location']=='1')?'billing_location':'pay_location';?>');
		}
		

		if(HTMLCreated==1){
			if(output=='output_pdf'){
				generate_pdf(op);
				top.show_loading_image('hide');
			}
			if(output=='output_csv'){
				if(dbtemp_name == "Adjustment Report" || dbtemp_name == "Day Sheet" || dbtemp_name == "Daily Balance" || dbtemp_name == "Unapplied Superbills" || dbtemp_name == "Unapplied Payments" || dbtemp_name == "Copay Reconciliation"){
					download_csv();
				}else{
					export_csv();
				}
				top.show_loading_image('hide');
			}
		}			
        
	});

	function set_container_height(){
		$_hgt = (window.innerHeight) - $('#module_buttons').outerHeight(true);
		$('.reportlft').css({
			'height':$_hgt,
			'max-height':$_hgt,
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
		$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});
	} 

	$(window).load(function(){
		set_container_height();
		//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
		if(HTMLCreated==1){		
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);
    
    function enableDisableControls(vals,callFrom){
        if(vals!=''){
            DateOptions("x");
            $('#Start_date').val("");
            $('#End_date').val("");

            if($("input[name=DateRangeFor]:checked").val()=='dos'){
                $('#dot').prop('checked', true);
            }

            $('#dayReport').val("Daily");
            $('#dayReport').prop('disabled', true);
        }else{
            $('#dayReport').prop('disabled', false);
        }
    }
   
/*    function show_facility(){
	   if($('#billing_facility').is(":checked")){
		   $('#facility_name').html('<?php echo $sch_facility_options;?>');
		   $('#facility_name').selectpicker("refresh");
	   }else{
		   $('#facility_name').html('<?php echo $posfacilityName;?>');
	       $('#facility_name').selectpicker("refresh");
	   }
   }
 */
  function show_facility(ctrl){
	   if($('#'+ctrl).is(":checked")){
		   if(ctrl=='billing_location'){
			   $('#pay_location').prop('checked',false);

			   //$("input[name='DateRangeFor'][id='dor']").prop("checked", true);
			   $("input[name='DateRangeFor'][id='dos']").prop("disabled", false);
			}else{
			   $('#billing_location').prop('checked',false);

			   //IF PAY LOCATION SELECTED THEN DISABLE DOS AND DOC
			   $("input[name='DateRangeFor'][id='dot']").prop("checked", true);
			   $("input[name='DateRangeFor'][id='dos']").prop("disabled", true);
		   }
		   $('#facility_name').html('<?php echo $sch_facility_options;?>');
		   $('#facility_name').selectpicker("refresh");
	   }else{
		   $('#facility_name').html('<?php echo $posfacilityName;?>');
           if(dbtemp_name == 'Unapplied Superbills' || dbtemp_name=='Daily Balance' || dbtemp_name == "Unapplied Payments"){
                $('#facility_name').html('<?php echo $sch_facility_options;?>');
		   }
	       $('#facility_name').selectpicker("refresh");
	   }
   }

   function show_hide_controls(){
	   if(dbtemp_name=='Daily Balance'){
		   if($("input[name='DateRangeFor']:checked").val()=='dor'){
			   $('#div_not_display_deleted_block').css('display', 'block');
		   }else{
			   $('#not_display_deleted_block').prop('checked', false);
			   $('#div_not_display_deleted_block').css('display', 'none');
		   }
		}
	   
   }

	function dispCCTypes(cc_val){
		if(dbtemp_name=='Payments'){
			if(cc_val=='Credit Card'){
				$('#ccdiv').show();			
			}else{
				$('#ccdiv').hide();			
			}
		}
	}
   
	

</script> 
</body>
</html>
