<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

/* if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}
 */
// GET MONTHS
$mon = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sept", "10" => "Oct", "11" => "Nov", "12" => "Dec");
$curMonth = date("m");
$month_options ="";
if(!isset($_REQUEST['months'])) $_REQUEST['months'] = $curMonth;
foreach($mon as $key => $val){
	$select = "";
	if($_REQUEST['months'] == $key){
		$select = 'SELECTED';
	}
	$month_options .= "<option value='" . $key . "' " . $select . ">" . $val . "</option>";
}

// GET YEARS
$q=imw_query("SELECT max( YEAR( `recalldate` ) ) as maxyear FROM patient_app_recall");
$arrYears = array();
$curYear = date("Y");
list($maxyear)=imw_fetch_array($q);
if(!isset($_REQUEST['years'])) $_REQUEST['years'] = $curYear;
for($i=date("Y")-3;$i<=($maxyear);$i++){
	$arrYears[$i] = $i;
	$selct = "";
	if($_REQUEST['years'] == $i){
		$selct = 'SELECTED';
	}
	$year_options .= "<option value='" . $i . "' " . $selct . ">" . $i . "</option>";
}

$qry = "SELECT recallLeter_id, recallTemplateName FROM `recalltemplate` order by recallTemplateName";
$res = imw_query($qry);
$recallArr = array();
$recall_options = '';
while ($row = imw_fetch_array($res)) {
	$sel = '';
	$r_id = $row['recallLeter_id'];
	$r_TName = $row['recallTemplateName'];
	$recallArr[$r_id] = $r_TName;
	if ($_REQUEST['recallTemplatesListId'] == $r_id) $sel = 'SELECTED';
	$recall_options .= "<option value='" . $r_id . "' " . $sel . ">" . $r_TName . "</option>";
}

$selOperId= join(',',$_REQUEST['operator_id']);
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, $op_cnt, '');
$opr_cnt = sizeof(explode('</option>', $operatorOption)) - 1;

//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['facility_name'],$_REQUEST['facility_name']);
$posfacilityName = $CLSReports->getFacilityName($selArrFacility, '1');
$posfac_cnt = sizeof(explode('</option>', $posfacilityName)) - 1;


//--- GET PHYSICIAN NAME ---
$strPhysician = implode(',',$_REQUEST['phyId']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;

//CPT CODES
$cpt_qry = "select cpt_prac_code,cpt_fee_id,cpt_desc from cpt_fee_tbl where delete_status = '0' order by cpt_prac_code asc";
$res = imw_query($cpt_qry);
while ($row = imw_fetch_array($res)) {
    $color = '';
    $cpt_fee_id = $row['cpt_fee_id'];
	if(strpos($row['cpt_desc'],'<')>0)
	{
		$cp=str_replace('<','&lt;',$row['cpt_desc']);
	}
	if(strpos($row['cpt_desc'],'>')>0)
	{
		$cp=str_replace('>','&gt;',$row['cpt_desc']);
	}
    $cpt_prac_code = $row['cpt_prac_code'];
		$sel = '';
		if (sizeof($cpt_code_id) > 0) {
			if ($cpt_code_id[$cpt_fee_id]) {
				$sel = 'selected';
			}
    }
    $cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
	$sel = (in_array($cpt_prac_code ,$_POST['cptCodeId']) === true) ? 'selected' : '';
	$cpt_code_options .= "<option value='" . $cpt_prac_code . "' style='" . $color . "' ".$sel.">" .$cpt_prac_code.' - '.$cp. "</option>";
}

// GET DX CODES
$diag_qry = "select dx_code,diagnosis_id,diag_description from diagnosis_code_tbl order by dx_code asc";
$diag_run = imw_query($diag_qry);
$arrDXCodes_options = "";
$arrDXCodes = array();
while($diag_fet=imw_fetch_array($diag_run)) {
	$dxId = $diag_fet['diagnosis_id'];
	$arrDXCodes[$dxId] = $diag_fet['diag_description'].' - '.$diag_fet['dx_code'];
	$sel = (in_array($diag_fet['dx_code'] ,$_POST['dxCodeId']) === true) ? 'selected' : '';	
	$arrDXCodes_options .= "<option value='" . $diag_fet['dx_code'] . "' ".$sel.">" .$diag_fet['dx_code'].' - '.$diag_fet['diag_description']. "</option>";
}		
//--- SET ICD10 CODE DROP DOWN ----
$all_dx10_code_options = '';
$arrICD10Code = $CLSReports->getICD10Codes($withInvCommas = '', $returnData = 'desc');
foreach ($arrICD10Code as $dx10code){
	$sel = (in_array($dx10code ,$_POST['dxCodeId10']) === true) ? 'selected' : '';	
    $all_dx10_code_options .= "<option value='" . $dx10code . "' ".$sel.">" . $dx10code . "</option>";
}

// GET ALL TESTS LIST
$arrTestNms = array("Anterior Photos","Cell Count","External Photos","Fundus","HRT","IVFA","Laboratories","Other","OCT","Pachy","Topography","VF","Ophthalmoscopy","fundus","B-Scan");
sort($arrTestNms);
$arrTests = array();
$arrTests_options = "";
foreach($arrTestNms as $value){
	$arrTests[$value] = $value;
	$sel = (in_array($value ,$_POST['add_tests']) === true) ? 'selected' : '';	
	$arrTests_options .= "<option value='" . $value . "' ".$sel.">" . $value . "</option>";
}

// GET ALL IMMUNIZATIONS
$imunni_qry = "select imnzn_id,imnzn_name from  immunization_admin order by imnzn_name";
$imunni_run = imw_query($imunni_qry);
$arrImmId = array();
while($imunni_fet=imw_fetch_array($imunni_run)) {
	$immId = $imunni_fet['imnzn_id'];
	$arrImmId[$immId] = $imunni_fet['imnzn_name'];
	$sel = (in_array($immId ,$_POST['immunizId']) === true) ? 'selected' : '';	
	$imunni_options .= "<option value='" . $immId . "' ".$sel.">" . $imunni_fet['imnzn_name'] . "</option>";
}														

// GET ALL LAB
$labQryRes = imw_query("select lab_radiology_name, lab_radiology_tbl_id	from lab_radiology_tbl where lab_radiology_status = '0' and lab_type='Lab'"); 
$arrLab = array();
while($labQry=imw_fetch_array($labQryRes)) {
	$lab_radiology_name = $labQry['lab_radiology_name'];
	$arrLab[$lab_radiology_name] = $lab_radiology_name;
	$sel = (in_array($lab_radiology_name ,$_POST['labIds']) === true) ? 'selected' : '';	
	$lab_options .= "<option value='" . $lab_radiology_name . "' ".$sel.">" . $lab_radiology_name . "</option>";
}

//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['facility_name'],$_REQUEST['facility_name']);
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
	$fac_id = $fac_res['id'];
	$fac_id_arr[$fac_id] = $fac_res['name'];
	if($selArrFacility[$fac_id])$sel='SELECTED';
	$facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}
$fac_cnt=sizeof($fac_id_arr);

//--- GETTING SCHEDULER PROCEDURES---------------
$selArrProc= array_combine($_REQUEST['procedures'],$_REQUEST['procedures']);
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
		if($selArrProc[$procKey]) $sel='SELECTED';
		$proc_options .= '<option value="' . $procKey . '" '.$sel.' >' . $proVal . '</option>';
	}
}


$dbtemp_name = "Reminder Recall";
//CSV NAME
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";
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
        
<div class=" container-fluid">
	<div class="anatreport">
		<div id="common_drop" style="position:absolute;bottom:0px;"></div>
		<div class="row" id="row-main">
			<div class="col-md-3" id="sidebar">
				<form name="sch_report_form" id="sch_report_form" method="post"  action="" autocomplete="off">
				<input name="processReport" id="processReport" autocomplete="off" value="Daily" type="hidden">
				<input type="hidden" name="SchedulerNewReport" id="SchedulerNewReport" value="">
				<input type="hidden" name="Submit" id="Submit" value="get reports">
				<input type="hidden" name="form_submitted" id="form_submitted" value="1">
				<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
				<div class="reportlft" style="height:100%;">
					<div class="practbox">
						<div class="anatreport"><h2>Practice Filter</h2></div>
						<div class="clearfix"></div>
						<div class="pd5" id="searchcriteria">
							<div class="row">
								<div class="col-sm-6">
									<label>Facility</label>
									<select name="facility_name[]" id="facility_name" class="selectpicker" data-container="#common_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $facilityName; ?>
										<option value="0" <?php if(in_array(0,$facility_name)) echo "Selected"; ?>> </option>
									</select>
								</div>
								<div class="col-sm-3">
									<label>Month</label>
									<select name="months" id="months" class="selectpicker" data-container="#common_drop" data-width="100%" data-size="10">
										<option value="" <?php if ($_POST['months'] == '') echo 'SELECTED'; ?>>Select</option>
										<?php echo $month_options; ?>
									</select>
								</div>
								<div class="col-sm-3">
									<label>Year</label>
									<select name="years" id="years" class="selectpicker" data-container="#common_drop" data-width="100%" data-size="10">
										<option value="" <?php if ($_POST['years'] == '') echo 'SELECTED'; ?>>Select</option>
										<?php echo $year_options; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Date From</label>
									<div class="input-group">
										<input type="text" name="recall_date_from" placeholder="From" style="font-size: 12px;" id="recall_date_from" value="<?php echo ($_REQUEST['recall_date_from']!="")?$_REQUEST['recall_date_from']:''; ?>" class="form-control date-pick">
										<label class="input-group-addon" for="recall_date_from"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
								</div>	
								<div class="col-sm-6">	
									<label>Date To</label>
									<div class="input-group">
										<input type="text" name="recall_date_to" placeholder="To" style="font-size: 12px;" id="recall_date_to" value="<?php echo ($_REQUEST['recall_date_to']!="")?$_REQUEST['recall_date_to']:''; ?>" class="form-control date-pick">
										<label class="input-group-addon" for="recall_date_to"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
								</div>
								<div class="col-sm-6">
									<label>Fullfilled From</label>
									<div class="input-group">
										<input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo ($_REQUEST['Start_date']!="")?$_REQUEST['Start_date']:''; ?>" class="form-control date-pick">
										<label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
								</div>	
								<div class="col-sm-6">	
									<label>Fullfilled To</label>
									<div class="input-group">
										<input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo ($_REQUEST['End_date']!="")?$_REQUEST['End_date']:''; ?>" class="form-control date-pick">
										<label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
								</div>
								<div class="col-sm-12">
									<label>Recall Procedures</label>
									<select name="procedures[]" id="procedures" data-container="#common_drop" <?php echo ($temp_id && !isset($filter_arr['procedures']))?'disabled':''; ?> class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $proc_options; ?>
									</select>
								</div>									
								<div class="col-sm-6">	
									<label>Report Type</label>
									<select name="repType" id="repType" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" onchange="check_box(this.value)" >
										<option <?php if ($_POST['repType'] == 'House_Calls') echo 'SELECTED'; ?> value="House_Calls" >Televox</option>
										<option <?php if ($_POST['repType'] == 'pam') echo 'SELECTED'; ?> value="pam">PAM2000</option>
										<option <?php if ($_POST['repType'] == 'recall_letters') echo 'SELECTED'; ?> value="recall_letters">Recall Letters</option>
										<option <?php if ($_POST['repType'] == 'create_label') echo 'SELECTED'; ?> value="create_label">Address Labels</option>
										<option <?php if ($_POST['repType'] == 'send_email') echo 'SELECTED'; ?> value="send_email">Email</option>
									</select>
								</div>
								<div class="col-sm-6">	
									<label>Template</label>
									<select name="recallTemplatesListId" id="recallTemplatesListId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10">
									<option value="">Select Template</option>
									<?php echo $recall_options; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Last Name From</label>
									<input type="text"  id="last_nam_frm" name="last_nam_frm" class="form-control" size="4" value="<?php echo $_REQUEST['last_nam_frm']; ?>">
								</div>
								<div class="col-sm-6">
									<label>To</label>
									<input type="text"  id="last_nam_to" name="last_nam_to" class="form-control" size="4" value="<?php echo $_REQUEST['last_nam_to']; ?>">
								</div>
								<div class="clearfix"></div>
								<div id="conditional_div" class="hide">
									<div class="col-sm-6">
										<label for="cpt">Cpt Code</label>
										<select name="cptCodeId[]" id="cptCodeId" class="selectpicker" data-container="#common_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
											<?php echo $cpt_code_options; ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label>Dx Code</label>
										<select name="dxCodeId[]" id="dxCodeId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
											<?php echo $arrDXCodes_options; ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label>Tests/Labs</label>
										<select name="add_tests[]" id="add_tests" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
											<?php echo $arrTests_options; ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label>Immunization</label>
										<select name="immunizId[]" id="immunizId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
											<?php echo $imunni_options; ?>
										</select>
									</div>
									<div class="col-sm-6">
										<label>Medications</label>
										<input name="medications" id="medications" class="form-control"/ value="<?php echo $_POST['medications']; ?>">	
									</div>
									<div class="col-sm-6">
										<label>Allergies</label>
										<input name="allergies" id="allergies" class="form-control" value="<?php echo $_POST['allergies']; ?>"/>	
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="grpara">
						<div class="anatreport">
							<h2>Include</h2>
						</div>
						<div class="pd5" id="searchcriteria">
							<div class="row">
								<div class="col-sm-6">
									<div class="checkbox pointer">
										<input type="checkbox" name="unfullRec" id="unfullRec" value="1" <?php if ($_POST['unfullRec'] == '1') echo 'CHECKED'; ?>/>
										<label for="unfullRec">Unfulfilled Records</label>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="checkbox pointer">
										<input type="checkbox" name="excSentEmail" id="excSentEmail" value="1" <?php if ($_POST['excSentEmail'] == '1') echo 'CHECKED'; ?>/>
										<label for="excSentEmail">Exclude Sent Email</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="module_buttons" class="ad_modal_footer text-center">
					<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
				</div>
				</form>
			</div>
			<div class="col-md-9" id="content1">
				<div class="btn-group fltimg" role="group" aria-label="Controls">
					<img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
				</div>
				<div class="pd5 report-content">
					<div class="rptbox">
						<div id="html_data_div" class="row">
							<?php
							if($_POST['form_submitted']){
								include('reminder_recall_result.php');
							}else{ 
								echo '<div class="text-center alert alert-info">No Search Done.</div>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>	
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
	<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form> 
<form name="frmPDF" action="recall_report_print_labels.php" target="_blank" method="post">
	<input type="hidden" name="months" value="<?php echo $_REQUEST["months"];?>">
	<input type="hidden" name="years" value="<?php echo $_REQUEST["years"];?>">
	<input type="hidden" name="recall_date_from" value="<?php echo $_REQUEST["recall_date_from"];?>">
	<input type="hidden" name="recall_date_to" value="<?php echo $_REQUEST["recall_date_to"];?>">
	<input type="hidden" name="last_nam_frm" value="<?php echo $_REQUEST["last_nam_frm"];?>">
	<input type="hidden" name="last_nam_to" value="<?php echo $_REQUEST["last_nam_to"];?>">
	<input type="hidden" name="pat_id_imp" value="<?php echo $pat_id_imp;?>">
	<input type="hidden" name="excSentEmail" value="<?php echo $excSentEmail;?>">
</form>
<form name="frmRecallLetterPDF" action="<?php echo $recallLetterAction;?>" target="_blank" method="<?php echo $mthd;?>">
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
	<input type="hidden" name="pat_id_imp" value="<?php echo $pat_id_imp;?>">
	<input type="hidden" name="facility_name" value="<?php echo $facility_name;?>">
	<input type="hidden" name="procedures" value="<?php echo $procedures;?>">
</form>
<form name="txtForm" id="txtForm" action="save_house_csv.php?fn=<?php echo $filename;?>" method="post"></form>
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var create_label = '<?php echo $create_label; ?>';
	var recall_letters = '<?php echo $recall_letters; ?>';
	var House_Calls = '<?php echo $House_Calls; ?>';
	var send_email = '<?php echo $send_email; ?>';
	var pam = '<?php echo $pam; ?>';
	var num = '<?php echo $num; ?>';
	if(create_label == 1){
		document.frmPDF.submit();
	}
	if(recall_letters == 1){
		document.frmRecallLetterPDF.submit();
	}
	function sendEmail() {
		var res = $("input[name='pat_id_imp[]']:checked").length;
		if(res<=0)
		{
			top.fAlert("Please select at least one record");
			return false;	
		}
		top.show_loading_image('show');
		$('#frmRecallLetterEmail').submit();
	}
	
	var i=0;
	var mainBtnArr = new Array();
	if (num > 0) {
		if(create_label != 1 && recall_letters != 1 && send_email != 1){
			mainBtnArr[i] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
			i++;
		}
		if(House_Calls == 1) {
			mainBtnArr[i] = new Array("recall_repp","Export CSV","top.fmain.export_csv();");
			i++;
			mainBtnArr[i] = new Array("recall_repp1","Export TXT","top.fmain.exportTXT();");
			i++;
		}
		if(send_email == 1) {
			mainBtnArr[i] = new Array("recall_repp","Send Email","top.fmain.sendEmail();");
			i++;
		}
		if(pam == 1) {
			mainBtnArr[i] = new Array("recall_repp","Export CSV","top.fmain.export_csv();");
			i++;
			mainBtnArr[i] = new Array("recall_repp","Export TXT","top.fmain.exportTXT();");
		}
		top.btn_show("PPR", mainBtnArr);
	}
	
	function exportTXT(){
		window.txtForm.submit();
		top.show_loading_image("hide");
	}
	
	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, 'l');
			window.close();
		}
	}
	
	function get_sch_report() {
		top.show_loading_image('show');
		if((document.getElementById('months').value!='' && document.getElementById('years').value!='') || (document.getElementById('recall_date_from').value!='' && document.getElementById('recall_date_to').value!='')){
			if(validReport()==true) {
				document.sch_report_form.submit();
				top.show_loading_image('hide');
			}	
		} else {
			top.show_loading_image("hide");
			top.fAlert('Select Month/Year/Date Range to proceed.');
			return false;
		}
		
	}

	function validReport() {
		var flag=true;
		var msg='';
		if(document.getElementById("repType").value =='recall_letters' || document.getElementById("repType").value =='send_email') {
			
			if(document.getElementById("recallTemplatesListId")) {
				if(document.getElementById("recallTemplatesListId").value=='') {
					flag=false;
					msg = msg+'"Select Recall Letter Template first!"\n';	
				}
			}
		}
		if(flag==false) {
			top.fAlert(msg);
			top.show_loading_image("hide");
			return false
		}else {
			return true;	
		}
			
	}
	
	function check_box(objVal){
		//START CODE TO DISABLE SELECT LIST OF TEMPLATES,DATE RANGE(FROM-TO)
		$('#recallTemplatesListId').prop('disabled', true);	
		if(objVal == 'send_email' || objVal == 'recall_letters') {
			$('#recallTemplatesListId').prop('disabled', false);	
		} else {
			$('#recallTemplatesListId').prop('disabled', true);
		}
		if(objVal == 'recall_letters') {
			$('#conditional_div').removeClass('hide');
		}else{
			$('#conditional_div').addClass('hide');
		}
		$('.selectpicker').selectpicker('refresh');
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
		
		$('#check_all').click(function(event) {  //on click 
				if(this.checked) { // check select status
					$('.checkbox1').each(function() { //loop through each checkbox
						this.checked = true;  //select all checkboxes with class "checkbox1"               
					});
				}else{
					$('.checkbox1').each(function() { //loop through each checkbox
						this.checked = false; //deselect all checkboxes with class "checkbox1"                       
					});         
				}
			});
		check_box($("#repType").val());
		set_container_height();
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
		if(num>0){		
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);  
</script> 
</body>
</html>