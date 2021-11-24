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

include("../../../config/globals.php");
require_once('../../../library/classes/common_function.php');
require_once('../../../library/classes/cls_common_function.php');
$OBJCommonFunction = new CLSCommonFunction;
$phpDateFormat=phpDateFormat();
function getFacilityName($selFacilities='', $savedSearch='0'){
	$query = "select pos_facilityies_tbl.facilityPracCode as name,
			pos_facilityies_tbl.pos_facility_id as id,
			pos_tbl.pos_prac_code
			from pos_facilityies_tbl
			left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
			order by pos_facilityies_tbl.headquarter desc,
			pos_facilityies_tbl.facilityPracCode";
	$qry = imw_query($query);
	$return = '';
	while($qryRes = imw_fetch_assoc($qry)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$sel='';
		if($savedSearch=='1'){ $sel=''; }
		if(sizeof($selFacilities)>0){
			if(in_array($id,$selFacilities)) { $sel='selected'; }
		}
		//-----------------------
		
		$return .= '<option '.$sel.' value="'.$id.'">'.$name.' - '.$pos_prac_code.'</option>';
	}						
	return $return;
}
$library_path = $GLOBALS['webroot'].'/library';

if($_REQUEST["del_id"]) {
	$delQry = "UPDATE ccda_export_schedule SET delete_status='1' WHERE id = '".$_REQUEST["del_id"]."' ";
	$delRes = imw_query($delQry);
}
if($_REQUEST["mode_save_form"]=='save') {
	
	$strFacility 			= implode(",",$_REQUEST["facility"]);
	$strProvider 			= implode(",",$_REQUEST["provider"]);
	$strInsType 			= implode(",",$_REQUEST["ins_type"]);
	$strinsId 				= implode(",",$_REQUEST["insId"]);
	$startDate 				= $_REQUEST["Start_date"];
	$endDate 				= $_REQUEST["End_date"];
	$scheduleType 			= $_REQUEST["schedule_type"];
	$scheduleDate 			= $_REQUEST["schedule_date"];
	$startHour 				= (int)$_REQUEST["start_hour"];
	$startMin 				= (int)$_REQUEST["start_min"];
	$startTime 				= $_REQUEST["start_time"];
	
	$reoccurringDayWeek 	= $_REQUEST["reoccurring_day_week"];
	$reoccurringDayNum		= (int)$_REQUEST["reoccurring_day_num"];
	$reoccurringTimePeriod 	= $_REQUEST["reoccurring_time_period"];
	$startHourReoccurring	= (int)$_REQUEST["start_hour_reoccurring"];
	$startMinReoccurring	= (int)$_REQUEST["start_min_reoccurring"];
	$startTimeReoccurring	= $_REQUEST["start_time_reoccurring"];
	$encKey					= $_REQUEST["enc_key"];
	$zipEncrypt				= $_REQUEST["zip_encrypt"];
	
	$editId					= $_REQUEST["edit_id"];
	
	$startHour 				= ($startHour == 12) ? 0 : $startHour;
	if(strtoupper($startTime)=="PM") {$startHour = ((int)$startHour+12); }
	$scheduleTime = "";
	if($startHour || $startMin) {
		$scheduleTime = $startHour.":".$startMin;
	}
	$scheduleDateTime = trim(getDateFormatDB($scheduleDate)." ".$scheduleTime);

	$startHourReoccurring	= ($startHourReoccurring == 12) ? 0 : $startHourReoccurring;
	if(strtoupper($startTimeReoccurring)=="PM") {$startHourReoccurring = ((int)$startHourReoccurring+12); }
	$scheduleTimeReoccurring = "";
	if($startHourReoccurring || $startMinReoccurring) {
		$scheduleTimeReoccurring = $startHourReoccurring.":".$startMinReoccurring;
		if(strtolower($scheduleType)=="reoccurring date time") {
			$startDate = $endDate = $scheduleDateTime = "";
		}
	}


	//pre($_SESSION);pre($_REQUEST);
	$qryStart = " INSERT INTO ";
	if($editId) {
		$qryStart = " UPDATE ";
		$qryWhr = " WHERE id = '".$editId."' ";
	}
	$saveQry = $qryStart." ccda_export_schedule SET 
				facility_id 			= '".$strFacility."',
				provider_id 			= '".$strProvider."',
				date_from 				= '".getDateFormatDB($startDate)."',
				date_to 				= '".getDateFormatDB($endDate)."',
				schedule_type 			= '".$scheduleType."',
				schedule_date_time 		= '".$scheduleDateTime."',
				reoccurring_time_period	= '".$reoccurringTimePeriod."',
				reoccurring_day_num 	= '".$reoccurringDayNum."',
				reoccurring_day_week 	= '".$reoccurringDayWeek."',
				reoccurring_time 		= '".$scheduleTimeReoccurring."',
				enc_key 				= '".$encKey."',
				zip_encrypt 			= '".$zipEncrypt."',
				operator_id 			= '".$_SESSION["authId"]."',
				operator_date_time 		= '".date("Y-m-d H:i:s")."',
				ins_type 				= '".$strInsType."',
				ins_comp_id 			= '".$strinsId."'
				".$qryWhr;	
	$saveRes = imw_query($saveQry);
}

//START FETCHING DATA
$getQry = "SELECT ces.id,ces.schedule_type AS scheduleType,ces.facility_id,ces.provider_id,ces.enc_key,ces.zip_encrypt,ces.ins_type,ces.ins_comp_id,
			ces.reoccurring_time_period as reoccurringTimePeriod,ces.reoccurring_day_num as reoccurringDayNum,
			ces.reoccurring_day_week as reoccurringDayWeek,
			DATE_FORMAT(ces.reoccurring_time, '%h:%i:%p') as reoccurringTime,
			IF(ces.reoccurring_time='00:00:00','',DATE_FORMAT(ces.reoccurring_time, '%h:%i %p')) as reoccurringTimeShow,
			IF(ces.date_from='0000-00-00','',DATE_FORMAT(ces.date_from, '".get_sql_date_format('','Y','-')."')) as dateFrom,
			IF(ces.date_to='0000-00-00','',DATE_FORMAT(ces.date_to, '".get_sql_date_format('','Y','-')."')) as dateTo,
			IF(ces.schedule_date_time='0000-00-00 00:00:00','',DATE_FORMAT(ces.schedule_date_time, '".get_sql_date_format('','Y','-')." %h:%i %p')) as scheduleDateTime,
			IF(ces.operator_date_time='0000-00-00 00:00:00','',DATE_FORMAT(ces.operator_date_time, '".get_sql_date_format('','Y','-')." %h:%i %p')) as operatorDateTime,
			CONCAT(u.lname,', ',u.fname,' ',u.mname) as operatorName,
			DATE_FORMAT(ces.schedule_date_time, '".get_sql_date_format('','Y','-')."') as scheduleDate,
			DATE_FORMAT(ces.schedule_date_time, '%h:%i:%p') as scheduleTime
			FROM ccda_export_schedule ces 
			INNER JOIN users u ON(u.id = ces.operator_id)
			WHERE ces.delete_status = '0' AND ces.operator_id='".$_SESSION["authId"]."'
			ORDER BY ces.schedule_type, ces.schedule_date_time DESC";
$getRows = get_array_records_query($getQry);
$facility_id_arr = array();
$dayNumArr = array("1"=>"1<sup>st</sup>","2"=>"2<sup>nd</sup>","3"=>"3<sup>rd</sup>","4"=>"4<sup>th</sup>","5"=>"5<sup>th</sup>");
$selArrInsType = $selArrInsComp = array();
foreach($getRows as $getRow) {
	if($getRow["id"] == $_REQUEST["edit_id"]) {
		if($getRow["facility_id"]) {
			$facility_id_arr 			= explode(",",$getRow["facility_id"]);
		}
		$provider_id 					= $getRow["provider_id"];
		$enc_key 						= $getRow["enc_key"];
		$zip_encrypt 					= $getRow["zip_encrypt"];
		$Start_date 					= $getRow["dateFrom"];
		$End_date 						= $getRow["dateTo"];
		$schedule_type 					= $getRow["scheduleType"];
		$schedule_date 					= $getRow["scheduleDate"];
		list($HH,$MM,$AMPM) 			= explode(":",$getRow["scheduleTime"]);
		$operatorName 					= $getRow["operatorName"];
		$operatorDateTime 				= $getRow["operatorDateTime"];
		$reoccurring_time_period		= $getRow["reoccurringTimePeriod"];
		$reoccurring_day_num			= $getRow["reoccurringDayNum"];
		$reoccurring_day_week			= $getRow["reoccurringDayWeek"];
		list($reocHH,$reocMM,$reocAMPM)	= explode(":",$getRow["reoccurringTime"]);
		$ins_comp_id					= $getRow["ins_comp_id"];
		if($ins_comp_id) {
			$selArrInsComp				= explode(",",$ins_comp_id);	
		}
		$ins_type						= $getRow["ins_type"];
		if($ins_type) {
			$selArrInsType				= explode(",",$ins_type);
		}
		break;
	}
}


//START SET DEFAULT VALUES
if(!$_REQUEST["edit_id"]) {
	$HH = date("h");
	$MM = date("i");
	if($MM%5!=0){
		$remider=$MM%5;
		$MM=($MM-$remider)+5;
	}
	
	$reocHH = $HH;	
	$reocMM = $MM;
}
if($MM==60){$MM='00';}

//END SET DEFAULT VALUES

//--- GET INSURANCE COMPANY DETAILS ----------
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
	$sel='';
	$ins_id = $insQryRes[$i]['attributes']['insCompId'];
	$ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
	$ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
	if ($ins_name == '') {
		$ins_name = $insQryRes[$i]['attributes']['insCompName'];
		if (strlen($ins_name) > 20) {
			$ins_name = substr($ins_name, 0, 20) . '....';
		}
	}
	if(in_array($ins_id,$selArrInsComp)) { $sel='SELECTED'; }
	//if($selArrInsId[$ins_id])$sel='SELECTED';
	$ins_comp_arr[$ins_id] = $ins_name;
	if ($insQryRes[$i]['attributes']['insCompStatus'] == 0) {
		$sel_ins_comp_options .= "<option value='" . $ins_id . "' $sel>" . $ins_name . "</option>";
	} else {
		$sel_ins_comp_options .= "<option value='" . $ins_id . "' style='color:red' $sel>" . $ins_name . "</option>";
	}
}
//END FETCHING DATA
?>
<html>
    <title>imwemr</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery-ui.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-colorpicker.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/messi/messi.css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/admin.css" type="text/css">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
		<script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
	<![endif]-->
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery-ui.min.1.11.2.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.dragToSelect.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-formhelpers-colorpicker.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/messi/messi.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/simple_drawing.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/Driving_License_Scanning.js"></script>
		<script language="javascript">	
		$(function(){		// Init. bootstrap tooltip
		$('[data-toggle="tooltip"]').tooltip();
		$('.selectpicker').selectpicker();
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:top.global_date_format,
			formatDate:'Y-m-d'
		});
	});
		$(document).ready( function() {     
			//$('#div_enckey').draggable({"handle":'#divHeader'});           
		});
			var strItestFileNameENC = "";
			var strItestFileNamePLAIN = "";
			
			function GetXmlHttpObject(){            
                var objXMLHttp=null;
                if(window.XMLHttpRequest){
                    objXMLHttp=new XMLHttpRequest();
                }else if(window.ActiveXObject){
                    objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
                return objXMLHttp;
            }
         var mode = "<?php echo $_REQUEST["mode_save_form"];?>";
		 if(mode=="save") {
			 top.alert_notification_show('Record saved successfully'); 
			 top.show_loading_image("hide");
		 }
		</script>
        <style>
		.text_12{
			font-size:11px;
		}
		</style>
    </head>
<body class="whtbox">
<form name="frm_ccd_schedule" id="frm_ccd_schedule" action="manage_ccd_schedule.php" method="post" enctype="multipart/form-data" >
<input type="hidden" name="mode_save_form" id="mode_save_form"/>
<input type="hidden" name="edit_id" id="edit_id" value="<?php echo $_REQUEST["edit_id"];?>" />
<div class="container-fluid" id="report_form">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-4">
                <div class="col-sm-3">
                    <label for="facility">Facility</label>
                    <select name="facility[]" id="facility" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select Facility" data-size="15">
                        <?php echo getFacilityName($facility_id_arr,'1');?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="provider">Provider</label>
                    <select name="provider[]" id="provider" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select Provider" data-size="15">
                        <?php echo $OBJCommonFunction->dropDown_providers($provider_id,'');?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label>Ins. Type</label>
                    <select class="selectpicker" name="ins_type[]" id="ins_type" data-container="#common_drop" <?php echo ($temp_id && !isset($filter_arr['ins_types']))?'disabled':''; ?> data-width="100%"  multiple data-actions-box="true" data-title="Select All">
                        <option value="primary" <?php if(in_array('primary', $selArrInsType)) echo 'selected'; ?>>Primary</option>
                        <option value="secondary" <?php if(in_array('secondary', $selArrInsType)) echo 'selected'; ?>>Secondary</option>
                        <option value="tertiary" <?php if(in_array('tertiary', $selArrInsType)) echo 'selected'; ?>>Tertiary</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="insId">Ins. Carrier</label>
                    <select name="insId[]" id="insId" class="selectpicker"  <?php echo ($temp_id && !isset($filter_arr['ins_carriers']))?'disabled':''; ?> data-width="100%" data-size="15" multiple data-actions-box="true" data-title="Select All">
                        <?php echo $sel_ins_comp_options; ?>
                    </select>
                </div>
                
            </div>
            <div class="col-sm-7">
            	 <?php if($schedule_type=="") { $schedule_type="Specific Date Time"; }?>
                <div class="col-sm-4">
                	<div class="col-sm-5">
                        <label for="enc_key">Encryption Key</label>
                        <input type="text" name="enc_key" id="enc_key" class="form-control" value="<?php echo $enc_key;?>" />
                    </div>
                    <div class="col-sm-7">
                        <label for="schedule_type">Schedule Type</label>
                        <select name="schedule_type" id="schedule_type" class="selectpicker"  data-width="100%" data-actions-box="true" data-title="Select" data-size="15" onChange="show_hide_fields(this.value);">
                            <option>Select</option>
                            <option value="Specific Date Time" <?php if($schedule_type=="Specific Date Time"){ echo "selected";}?>>Specific Date Time</option>
                            <option value="Reoccurring Date Time" <?php if($schedule_type=="Reoccurring Date Time"){ echo "selected";}?>>Reoccurring Date Time</option>
                        </select>
                   </div> 
                </div>
                <div class="col-sm-8">
                    <div id="div_specific_dt_tm_id" style="display:<?php if($schedule_type=="Specific Date Time"){ echo "block";}else { echo "none" ;}?>;">		
                        <div class="col-sm-8">
                            <div class="col-sm-4">
                                <label for="Start_date">DOS From</label>
                                <div class="input-group">
                                    <input type="text" name="Start_date" onBlur="top.checkdate(this);" id="Start_date" value="<?php if($Start_date) {echo $Start_date;}else {echo date($phpDateFormat);}?>" class="form-control date-pick">
                                    <label class="input-group-addon btn" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="End_date">DOS To</label>
                                <div class="input-group">
                                    <input type="text" name="End_date" onBlur="top.checkdate(this);" id="End_date" value="<?php if($End_date) {echo $End_date;}else {echo date($phpDateFormat);}?>" class="form-control date-pick">
                                    <label class="input-group-addon btn" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                </div>
                            </div>
                            
                            <div class="col-sm-4">
                                <label for="schedule_date">Schedule Date</label>
                                <div class="input-group">
                                    <input type="text" name="schedule_date" onBlur="top.checkdate(this);" id="schedule_date" value="<?php if($schedule_date) {echo $schedule_date;}else {echo date($phpDateFormat);}?>" class="form-control date-pick">
                                    <label class="input-group-addon btn" for="schedule_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="start_hour">Schedule Timing</label><br>
                            <select name="start_hour" class="selectpicker" id="start_hour" data-width="30%" data-actions-box="true"  data-size="15">
                                <option value="">--</option>
                                <?php
                                    $hour_option = '';
                                    for($h=1;$h<=12;$h++){
                                        if($h<10){
                                            $h = '0'.$h;
                                        }
                                        $hour_sel = $h == $HH ? 'selected' : '';
                                        $hour_option .= '<option value="'.$h.'" '.$hour_sel.' >'.$h.'</option>';
                                    }
                                    print $hour_option;
                                ?>
                            </select>	<select name="start_min" class="selectpicker" id="start_min" data-width="30%" data-actions-box="true"  data-size="15">
                                <option value="" >--</option>
                                <?php
                                    $min_option = '';
                                    for($m=0;$m<60;$m = $m + 10){
                                        if($m<10){
                                            $m = '0'.$m;
                                        }
                                        $min_sel = $m == $MM ? 'selected' : '';
                                        $min_option .= '<option value="'.$m.'" '.$min_sel.' >'.$m.'</option>';
                                    }
                                    print $min_option;
                                ?>
                            </select> <select name="start_time" class="selectpicker" id="start_time" data-width="35%"  data-actions-box="true" data-size="15">
                                <option value="AM" <?php if($AMPM=="AM"){ echo "selected";}?>>AM</option>
                                <option value="PM" <?php if($AMPM=="PM"){ echo "selected";}?>>PM</option>
                            </select>
                        </div>
                    </div>
			
                     <div id="div_reoccurring_dt_tm_id" style="display:<?php if($schedule_type=="Reoccurring Date Time"){ echo "block";}else { echo "none" ;}?>;">
                        <div class="col-sm-3">
                            <label for="reoccurring_time_period">Time Period</label><br>
                            <select name="reoccurring_time_period" class="selectpicker" id="reoccurring_time_period" data-width="100%" data-actions-box="true"  data-size="10">
                                <option value="Last Week" <?php if($reoccurring_time_period=="Last Week"){ echo "selected";}?>>Last Week</option>
                                <option value="Last Month" <?php if($reoccurring_time_period=="Last Month"){ echo "selected";}?>>Last Month</option>
                                <option value="Last 3 Months" <?php if($reoccurring_time_period=="Last 3 Months"){ echo "selected";}?>>Last 3 Months</option>
                            </select>
                       </div>
                       <div class="col-sm-2">
                            <label for="reoccurring_day_num">Day</label><br>
                            <select name="reoccurring_day_num" class="selectpicker" id="reoccurring_day_num" data-width="100%" data-actions-box="true"  data-size="10">
                                <option value="">--</option>
                                <option value="1" <?php if($reoccurring_day_num=="1"){ echo "selected";}?>>1st</option>
                                <option value="2" <?php if($reoccurring_day_num=="2"){ echo "selected";}?>>2nd</option>
                                <option value="3" <?php if($reoccurring_day_num=="3"){ echo "selected";}?>>3rd</option>
                                <option value="4" <?php if($reoccurring_day_num=="4"){ echo "selected";}?>>4th</option>
                                <option value="5" <?php if($reoccurring_day_num=="5"){ echo "selected";}?>>5th</option>
                            </select>
                       </div>
                       <div class="col-sm-3">     
                            <label for="reoccurring_day_week">Week Day Of Month</label><br>
                            <select name="reoccurring_day_week" class="selectpicker" id="reoccurring_day_week" data-width="100%" data-actions-box="true"  data-size="10">
                                <option value="">--</option>
                                <option value="Sunday" <?php if($reoccurring_day_week=="Sunday"){ echo "selected";}?>>Sunday</option>
                                <option value="Monday" <?php if($reoccurring_day_week=="Monday"){ echo "selected";}?>>Monday</option>
                                <option value="Tuesday" <?php if($reoccurring_day_week=="Tuesday"){ echo "selected";}?>>Tuesday</option>
                                <option value="Wednesday" <?php if($reoccurring_day_week=="Wednesday"){ echo "selected";}?>>Wednesday</option>
                                <option value="Thursday" <?php if($reoccurring_day_week=="Thursday"){ echo "selected";}?>>Thursday</option>
                                <option value="Friday" <?php if($reoccurring_day_week=="Friday"){ echo "selected";}?>>Friday</option>
                                <option value="Saturday" <?php if($reoccurring_day_week=="Saturday"){ echo "selected";}?>>Saturday</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-4">
                            <label for="start_hour_reoccurring">Schedule Timing</label><br>
                            <select name="start_hour_reoccurring" class="selectpicker" id="start_hour_reoccurring" data-width="30%" data-actions-box="true"  data-size="15">
                                <option value="">--</option>
                                <?php
                                    $hour_option = '';
                                    for($h=1;$h<=12;$h++){
                                        if($h<10){
                                            $h = '0'.$h;
                                        }
                                        $hour_sel = $h == $reocHH ? 'selected' : '';
                                        $hour_option .= '<option value="'.$h.'" '.$hour_sel.' >'.$h.'</option>';
                                    }
                                    print $hour_option;
                                ?>
                            </select>	<select name="start_min_reoccurring" class="selectpicker" id="start_min_reoccurring" data-width="30%" data-actions-box="true"  data-size="15">
                                <option value="" >--</option>
                                <?php
                                    $min_option='';
                                    for($m=0;$m<60;$m = $m + 10){
                                        if($m<10){
                                            $m = '0'.$m;
                                        }
                                        $min_sel = $m == $reocMM ? 'selected' : '';
                                        $min_option .= '<option value="'.$m.'" '.$min_sel.' >'.$m.'</option>';
                                    }
                                    print $min_option;
                                ?>
                            </select> 
                            <select name="start_time_reoccurring" class="selectpicker" id="start_time_reoccurring" data-width="35%"  data-actions-box="true" data-size="15">
                                <option value="AM" <?php if($reocAMPM=="AM"){ echo "selected";}?>>AM</option>
                                <option value="PM" <?php if($reocAMPM=="PM"){ echo "selected";}?>>PM</option>
                            </select>
                        </div>
                    </div>
            	</div>
            </div>
			<div class="col-sm-1">
                <label for="zip_encrypt">Zip Encrypt</label>
                <div class="checkbox">
                    <input type="checkbox" name="zip_encrypt" class="mudata" title="Zip Encryption" id="zip_encrypt" value="1" <?php if($zip_encrypt=='1') { echo "checked"; }?> ><label for="zip_encrypt"  class="a_clr1 " style="cursor:pointer"></label>
                </div>
            </div>        
        </div>
		<div class="row">
			<script>
				function select_all_mu(obj){
					if($(obj).attr('checked') == true){
						$('.mudata').each(function(index, element) {
						$(this).attr({"checked":true});
						});
					}else{
						$('.mudata').each(function(index, element) {
						$(this).attr({"checked":false});
						});
					}
				}
			</script>
		</div>
	</div>
    <?php $col_height_frame = (int) ($_SESSION['wn_height'] - 630);?>
	<div class="col-sm-12">
	</div>
</div>
</form>	
<?php 
$col_height_frame = (int) ($_SESSION['wn_height'] - 370);
if(count($getRows)>0) {
?>
    <div style=" width:100%;height:<?php echo $col_height_frame;?>px; overflow:scroll; overflow-x:hidden; ">
        <table class="table table-bordered adminnw" >	
            <thead>
                <tr >
                    <th align="left" width="5%">S.No.</th>
                    <th align="left" width="10%">DOS From</th>
                    <th align="left" width="10%">DOS To</th>
                    <th align="left" width="10%">Encryption Key</th>
                    <th align="left" width="10%">Schedule Type</th>
                    <th align="left" width="15%">Scheduled By</th>
                    <th align="left" width="25%">Schedule Date/Time</th>
                    <th align="left" width="10%">Zip Encrypt</th>
                    <th align="left" width="5%">Del</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $cnt=0;
                foreach($getRows as $getRow) {
                    $cnt++;
                    $editId 				= $getRow["id"];
					$encKey 				= $getRow["enc_key"];
					$zip_encrypt			= $getRow["zip_encrypt"];
					$dateFrom 				= $getRow["dateFrom"];
                    $dateTo 				= $getRow["dateTo"];
                    $scheduleType 			= $getRow["scheduleType"];
                    $scheduleDateTime 		= $getRow["scheduleDateTime"];
                    $operatorName 			= $getRow["operatorName"];
                    $operatorDateTime 		= $getRow["operatorDateTime"];
					

					$reoccurringTimePeriod	= $getRow["reoccurringTimePeriod"];
					$reoccurringDayNum		= $getRow["reoccurringDayNum"];
					$reoccurringDayNumShow	= $dayNumArr[$getRow["reoccurringDayNum"]];
					$reoccurringDayWeek		= $getRow["reoccurringDayWeek"];
					$reoccurringTimeShow	= $getRow["reoccurringTimeShow"];
					$scheduleDateTimeShow	= $scheduleDateTime;
					if(strtolower($scheduleType)=="reoccurring date time") {
						$scheduleDateTimeShow = "";
						if($reoccurringTimePeriod) {
							$dateTo 	= $reoccurringTimePeriod;
						}
						if($reoccurringDayNumShow && $reoccurringDayWeek) {
							$scheduleDateTimeShow .= $reoccurringDayNumShow." ".$reoccurringDayWeek." Of Every Month";
						}
						if($reoccurringTimeShow) {
							$scheduleDateTimeShow 	.= " At ".$reoccurringTimeShow;
						}
					}
					
                	$clickRow = "edit_ccd_export('".$editId."')";
				?>
                <tr >
                    <td align="left" width="5%"  onClick="<?php echo $clickRow;?>" ><?php echo $cnt;?></td>
                    
                    <td align="left" width="10%" onClick="<?php echo $clickRow;?>"><?php echo $dateFrom;?></td>
                    <td align="left" width="10%" onClick="<?php echo $clickRow;?>"><?php echo $dateTo;?></td>
                    <td align="left" width="10%" onClick="<?php echo $clickRow;?>"><?php echo $encKey;?></td>
                    <td align="left" width="10%" onClick="<?php echo $clickRow;?>"><?php echo $scheduleType;?></td>
                    <td align="left" width="15%" onClick="<?php echo $clickRow;?>"><?php echo $operatorName." On ".$operatorDateTime;?></td>
                    <td align="left" width="25%" onClick="<?php echo $clickRow;?>"><?php echo $scheduleDateTimeShow;?></td>
                    <td align="left" width="10%"><?php if($zip_encrypt=="1") {?><label style="color:green; font-size:24px;">&#x2714;</label><?php }?></td>
                    <td align="left" width="5%"><span class="glyphicon glyphicon-remove pointer" onclick="del_ccd('<?php echo $getRow["id"];?>')" alt="Delete Row"></span></td>
                    
                </tr>
                
                <?php	
                }
                ?>
            </tbody>
        </table>
        
        
    </div>    
<?php
}
?>	
		<script type="text/javascript">
			var ar = [["add_ccd_schedule","Add New Schedule","top.fmain.submitForm(0);"], ["save_ccd_schedule","Save Schedule","top.fmain.submitForm(1);"], ["manage_ccd_export","Manage CCD Export","top.fmain.manage_ccd_export();"],["view_log","View Download Log","top.fmain.view_download_log();"]];			
			top.btn_show("O4A",ar);
			//Btn--
			function submitForm(getEnc){
				if(!getEnc || getEnc=='0') {
					top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/manage_ccd_schedule.php";	
					return;
				}
				
				
				var curFrmObj = document.frm_ccd_schedule;
				
				facility					= $('#facility').val();
				provider					= $('#provider').val();
				Start_date 					= $('#Start_date').val();
				End_date 					= $('#End_date').val();
				schedule_type 				= $('#schedule_type').val();
				schedule_date 				= $('#schedule_date').val();
				start_hour 					= $('#start_hour').val();
				start_min 					= $('#start_min').val();
				start_time 					= $('#start_time').val();
				reoccurring_day_num 		= $('#reoccurring_day_num').val();
				reoccurring_day_week 		= $('#reoccurring_day_week').val();
				enc_key 					= $('#enc_key').val();
				zip_encrypt 				= $('#zip_encrypt').val();
				
				var msg = "";
				var msgReoc = "";
				var commonMsg = "";
				if(enc_key == "" || enc_key.length < 16 || enc_key.length > 16){
					commonMsg = "Encryption Key with 16 characters<br>";
				}				
				
				if(commonMsg) {
					msg 	+= commonMsg;
					msgReoc += commonMsg;	
				}
				
				if(!Start_date) 	msg+="From Date<br>";
				if(!End_date) 		msg+="To Date<br>";
				if(!schedule_type) 	msg+="Schedule Type<br>";
				if(!schedule_date) 	msg+="Schedule Date<br>";
				
				
				
				if(schedule_type=="Reoccurring Date Time") {
					if(!reoccurring_day_num) 	msgReoc+="Day<br>";
					if(!reoccurring_day_week) 	msgReoc+="Week Day Of Month<br>";
				}
				
				msg_show = "";
				if(msg && (schedule_type=="Specific Date Time" || schedule_type=="")) {
					msg_show = msg;
				}
				if(msgReoc && (schedule_type=="Reoccurring Date Time")) {
					msg_show = msgReoc;
				}
				
				
				if(msg_show) {
					top.fAlert("Please enter following<br>"+msg_show);
					return;
				}
				top.show_loading_image("show");
				$('#mode_save_form').val("save");
				curFrmObj.submit();
				
				
				
			}	
			function manage_ccd_export() {
				top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/index.php";
			}
			function view_download_log() {
				top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/view_ccd_download_log.php";
			}
			function edit_ccd_export(edit_id) {
				top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/manage_ccd_schedule.php?edit_id="+edit_id;	
			}
			function del_ccd(del_id) {
				top.fancyConfirm('Do you want to delete selected?','',"top.fmain.del_ccd_export('"+del_id+"')");
			}
			function del_ccd_export(del_id) {
				top.fmain.location.href = "<?php echo $GLOBALS['webroot']; ?>/interface/reports/ccd/manage_ccd_schedule.php?del_id="+del_id;	
			}
			function show_hide_fields(sch_type) {
				document.getElementById("div_specific_dt_tm_id").style.display="none";
				document.getElementById("div_reoccurring_dt_tm_id").style.display="none";
				if(sch_type=="Specific Date Time") {
					document.getElementById("div_specific_dt_tm_id").style.display="block";
				}else if(sch_type=="Reoccurring Date Time") {
					document.getElementById("div_reoccurring_dt_tm_id").style.display="block";
				}
			}
			set_header_title('CCD Export');
			
		</script>
    </body>
</html>