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
//--- GET Groups SELECT BOX ----
$selArrGroups = array_combine($_REQUEST['groups'],$_REQUEST['groups']);
$group_query = "Select  gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
    $group_id_arr[$group_id] = $group_res['name'];
	if($selArrGroups[$group_id])$sel='SELECTED';
	$groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}
$grp_cnt=sizeof($group_id_arr);

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
$qry = "Select facilityPracCode, pos_facility_id from pos_facilityies_tbl";
$rs=imw_query($qry)or die(imw_error().'_44');
$fac_id_arr = array();
$fac_id_arr[0] = 'No Facility';
while($posQryRes = imw_fetch_array($rs)){
	$pos_facility_id = $posQryRes['pos_facility_id'];
	$fac_id_arr[$pos_facility_id] = addslashes($posQryRes['facilityPracCode']);
}
$fac_cnt=sizeof($fac_id_arr);
//--- GET FACILITY NAME ----
$posfacilityName = $CLSReports->getFacilityName($selArrFacility, '1');
$posfac_cnt = sizeof(explode('</option>', $posfacilityName)) - 1;


//GET ALL CPT PRACTICE CODES
$arrAllCPTCodes[0]='No CPT';
$qry="Select cpt_fee_id, cpt_prac_code FROM cpt_fee_tbl";
$rs=imw_query($qry);
while($res=imw_fetch_array($rs)){
	$arrAllCPTCodes[$res['cpt_fee_id']] = $res['cpt_prac_code'];
}

//--- GET PHYSICIAN NAME ---
$arr_phy=array_combine($_REQUEST['phyId'], $_REQUEST['phyId']);
$strPhysician = implode(',',$_REQUEST['phyId']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_sel= ($arr_phy['0']=='0')? 'SELECTED' : '';
$physicianName.= '<option value="0" '.$phy_sel.'>None</option>';
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;

//CREDITING PHYSICIAN
$str_credit_physician = implode(',',$_REQUEST['crediting_provider']);
$creditPhysicinName = $CLSCommonFunction->drop_down_providers($str_credit_physician, '1', '1', '', 'report');
$allCrPhyCount = sizeof(explode('</option>', $creditPhysicinName)) - 1;


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

if($temp_id){
	$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='$temp_id' and `delete_status` = 0");
	if(imw_num_rows($sql_query) > 0){
		$row = imw_fetch_assoc($sql_query);
		$dbtemp_id  = $row['id'];
		$dbtemp_name  = $row['template_name'];
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
            if (in_array($wkey,$selwriteOffData)) {
                $wselected = 'selected="selected"';
            }
            $writeOffData_options .= "<option value='" . $wkey . "' " . $wselected . ">" . $wrow_name . "</option>";
        }
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
            if (in_array($dkey,$selwriteOffData)) {
                $dselected = 'selected="selected"';
            }
            $discountData_options .= "<option value='" . $dkey . "' " . $dselected . ">" . $drow_name . "</option>";
        }
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
            if (in_array($akey,$selwriteOffData)) {
                $aselected = 'selected="selected"';
            }
            $adjustData_options .= "<option value='" . $akey . "' " . $aselected . ">" . $arow_name . "</option>";
        }
        $adjustData_options .= '</optgroup>';
    }
    
    $allWriteoffCount = sizeof($writeOffData) + sizeof($discountData) + sizeof($adjustData);
    
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
                                    <div class="anatreport"><h2>Practice Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        
										<div class="row">
                                            <div class="col-sm-6">
                                                <label>Groups</label>
                                                <select name="groups[]" id="groups" data-container="#provider_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $groupName; ?>
												</select>
                                            </div>
											<div class="col-sm-6">
                                                <label>Facility</label>
                                                <select name="facility_name[]" id="facility_name" data-container="#provider_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $posfacilityName; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Billing Provider</label>
                                                <select name="phyId[]" id="phyId" data-container="#provider_drop" class="selectpicker"  data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $physicianName; ?>
                                                </select>
                                            </div>
											<div class="col-sm-6">
											  <label>Crediting Provider</label>
											  <select name="crediting_provider[]" id="crediting_provider" data-container="#provider_drop" class="selectpicker"  data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												  <?php echo $creditPhysicinName; ?>
											  </select>
											</div>	
											<div class="col-sm-12">
											<div class="checkbox pointer" style="padding-top:5px; padding-bottom:10px">
												<input type="checkbox" name="chksamebillingcredittingproviders" id="chksamebillingcredittingproviders" value="1" <?php if($_POST['chksamebillingcredittingproviders']==1)echo 'checked';?>  />
												<label for="chksamebillingcredittingproviders">Exclude where billing and crediting providers are same</label>
											</div>										  
											</div>																				
                                            <div class="col-sm-5">
                                                <label>Operator</label>
                                                <select name="operator_id[]" id="operator_id" data-container="#provider_drop" class="selectpicker"data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $operatorOption; ?>
                                                </select>
                                            </div>
											<div class="col-sm-7">
                                                <label>Period</label>
												<div id="dateFieldControler">
                                                    <select name="dayReport" id="dayReport" class="selectpicker" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
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
                                                    <input type="radio" name="output_option" id="output_view_only" value="view" <?php if ($_POST['output_option'] == 'view' || $_POST['output_option'] == '') echo 'CHECKED'; ?>/> 
                                                    <label for="output_view_only">View Only</label>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer">
                                                    <input type="radio" name="output_option" id="output_pdf" value="output_pdf" <?php if ($_POST['output_option'] == 'output_pdf') echo 'CHECKED'; ?>/> 
                                                    <label for="output_pdf">PDF</label>
                                                </div>
                                            </div>                                                
                                            <div class="col-sm-4">
                                                <div class="radio radio-inline pointer" style="padding-left: 42%;">
                                                    <input type="radio" name="output_option" id="output_csv" value="output_csv" <?php if ($_POST['output_option'] == 'output_csv') echo 'CHECKED'; ?>/> 
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
										if($_POST['form_submitted']) {
											include('deleted_payments_result.php');
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
        </form>
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
	<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
	</form> 
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
				mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
				btncnt++;
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

	if(HTMLCreated==1){
		if(output=='output_pdf'){
			generate_pdf(op);
			top.show_loading_image('hide');
		}
		if(output=='output_csv'){
			export_csv();
			top.show_loading_image('hide');
		}
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
</script> 
</body>
</html>