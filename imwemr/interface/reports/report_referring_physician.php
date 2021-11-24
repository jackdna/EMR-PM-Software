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
    $group_id = $group_res['gro_id'];
   	$sel=(in_array($group_id, $_REQUEST['grp_id'])== true)? 'SELECTED' : '';
	$groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}
$grp_cnt=sizeof($group_id_arr);
//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['sc_name'],$_REQUEST['sc_name']);
$posfacilityName = $CLSReports->getFacilityName($selArrFacility, '1');
$posfac_cnt = sizeof(explode('</option>', $posfacilityName)) - 1;
//--- GET PHYSICIAN NAME ---
$strPhysician = implode(',',$_REQUEST['phyId']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;

$str_credit_physician = implode(',',$_REQUEST['crediting_provider']);
$creditPhysicinName = $CLSCommonFunction->drop_down_providers($str_credit_physician, '1', '1', '', 'report');
$allCrPhyCount = sizeof(explode('</option>', $creditPhysicinName)) - 1;

//CPT CODES
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl 
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color = '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = $row['cpt_prac_code'];
    if ($row['delete_status'] == 1 || $row['status'] == 'Inactive'){
        $color = 'color:#CC0000!important';
	}
	$cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
	$sel=(in_array($cpt_fee_id, $_REQUEST['cpt_code_id'])== true)? 'SELECTED' : '';
	$cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
}
//SET ICD10 CODE DROP DOWN
$arrICD10Code = $CLSReports->getICD10Codes();
$all_dx10_code_options = '';
$sel_dx10_code_options = '';
foreach ($arrICD10Code as $dx10code) {
	$dx10code = str_replace("'", "", $dx10code);
	$sel=(in_array($dx10code, $_REQUEST['dx_code10'])== true)? 'SELECTED' : '';
	$all_dx10_code_options .= "<option value='" . $dx10code . "' ".$sel.">" . $dx10code . "</option>";
}
$allDXCount10 = sizeof($arrICD10Code);
//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
    if ($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance') {
        $ins_id = $insQryRes[$i]['attributes']['insCompId'];
        $ins_name = $insQryRes[$i]['attributes']['insCompName'];
        $ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
        if ($ins_name == '') {
            $ins_name = $insQryRes[$i]['attributes']['insCompName'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }
		$sel = '';
		if (sizeof($insComName_options) > 0) {
			if (in_array($ins_id,$insuranceName)) {
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
		if (in_array($grp_ins_ids,$insuranceGrp))
            $selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}

//CSV NAME
$dbtemp_name = 'Referring Physician';
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";

//REPORT LOGIC INFORMATION FLOATING DIV
$logicWidth = 65;
$logicDiv = reportLogicInfo('referring_physician', 'tpl', $logicWidth);
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
  <form name="frm_reports" id="frm_reports" action="" method="post">
		<input type="hidden" name="form_submitted" id="form_submitted" value="1">
		<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
		<div class=" container-fluid">
			<div class="anatreport">
				<div id="common_drop" style="position:absolute;bottom:0px;"></div>
				<div class="row" id="row-main">
					<div class="col-md-3" id="sidebar">
						<div class="reportlft" style="height:100%;">
							<div class="practbox">
								<div class="anatreport"><h2>Practice Filter
									<div id="rptInfoImg" style="float:right" class="rptInfoImg" onClick="showHideReportInfo(event, '<?php echo $logicWidth;?>')"></div>								
								</h2></div>
								<div class="clearfix"></div>
								<div class="pd5" id="searchcriteria">
									<div class="row">
										<div class="col-sm-4">
											<label>Groups</label>
											<select name="grp_id[]" id="grp_id" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $groupName; ?>
											</select>
										</div>
										<div class="col-sm-4">
											<label>Facility</label>
											<select name="sc_name[]" id="sc_name" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $posfacilityName; ?>
											</select>
										</div>
										<div class="col-sm-4">
											<label>Billing Provider</label>
											<select name="phyId[]" id="phyId" data-container="#common_drop" data-container="#provider_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $physicianName; ?>
											</select>
										</div>
										<div class="col-sm-4">
										  <label>Crediting Provider</label>
										  <select name="crediting_provider[]" id="crediting_provider" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
											  <?php echo $creditPhysicinName; ?>
										  </select>
										</div>                            										
										<div class="col-sm-4">
											<label for="reff_physician"><span class="text_purple">Referring Physicians</span></label>
											<input class="form-control" id="reff_physician" name="reff_physician" value="<?php echo $_REQUEST['reff_physician']; ?>" />
											<input type="hidden" name="id_reff_physician" id="id_reff_physician" value="<?php echo $_REQUEST['id_reff_physician']; ?>" />
										</div>
										<div class="col-sm-4" >
											<label>Physicians Type</label>
											<select name="phy_type" id="phy_type" class="selectpicker pull-right" data-width="100%">
												<option value="all" <?php echo ($phy_type == 'all' ? 'selected' : '');?>>All</option>  
												<option value="prf" <?php echo ($phy_type == 'prf' ? 'selected' : '');?>>Referring Physician</option>  
												<option value="pcp" <?php echo ($phy_type == 'pcp' ? 'selected' : '');?>>Primary Care Physician</option>  
												<option value="cm" <?php echo ($phy_type == 'cm' ? 'selected' : '');?>>Co-Managed Physician</option>  
												<option value="nap" <?php echo ($phy_type == 'nap' ? 'selected' : '');?>>Not Associated PCP</option>  
											</select>
										</div>	
										<div class="col-sm-12">
										<div class="checkbox pointer" style="padding-top:5px; padding-bottom:10px">
											<input type="checkbox" name="chksamebillingcredittingproviders" id="chksamebillingcredittingproviders" value="1" <?php if($_POST['chksamebillingcredittingproviders']==1)echo 'checked';?>  />
											<label for="chksamebillingcredittingproviders">Exclude where billing and crediting providers are same</label>
										</div>										  
										</div>	
										<div class="col-sm-12">
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
										<div class="col-sm-6">
											<br />
											<div class="radio radio-inline pointer">
												<input type="radio" name="Process" id="Summary" value="Summary" checked <?php if ($_POST['Process'] == 'Summary') echo 'CHECKED'; ?> /> 
												<label for="Summary">Summary</label>
											</div>
											<div class="radio radio-inline pointer">
												<input type="radio" name="Process" id="Detail" value="Detail" <?php if ($_POST['Process'] == 'Detail') echo 'CHECKED'; ?> /> 
												<label for="Detail">Detail</label>
											</div>
										</div>
										

									</div>
								</div>
							</div>
							<div class="appointflt">
								<div class="anatreport"><h2>Analytic Filter</h2></div>
								<div class="clearfix"></div>
								<div class="pd5" id="searchcriteria">
									<div class="row">
										<div class="col-sm-6">
											<label for="cpt">CPT Code</label>
											<select name="cpt_code_id[]" id="cpt_code_id" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $cpt_code_options; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label for="dx_code10">ICD10 Codes</label>
											<select name="dx_code10[]" id="dx_code10" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" >
												<?php echo $all_dx10_code_options; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Ins. Group</label>
											<select name="insuranceGrp[]" id="insuranceGrp" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $ins_group_options; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Ins. Carrier</label>
											<select name="insuranceName[]" id="insuranceName" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $insComName_options; ?>
											</select>
										</div>
										
										<div class="col-sm-6">
											<label>Encounter Type</label><br />
											<div class="radio radio-inline pointer">
												<input type="radio" name="encounter_type" id="all" value="all" <?php if ($_POST['encounter_type'] == 'all'|| !$_POST['form_submitted']) echo 'CHECKED'; ?> /> 
												<label for="all">All</label>
											</div>
											<div class="radio radio-inline pointer">
												<input type="radio" name="encounter_type" id="initial" value="initial" <?php if ($_POST['encounter_type'] == 'initial') echo 'CHECKED'; ?> /> 
												<label for="initial">Initial</label>
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
										<div class="col-sm-3">
											<div class="radio radio-inline pointer">
												<input type="radio" name="groupby" id="Physicians" value="Physician" <?php if ($_POST['groupby'] == 'Physician' || !$_POST['form_submitted']) echo 'CHECKED'; ?> /> 
												<label for="Physicians">Physician</label>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="radio radio-inline pointer">
												<input type="radio" name="groupby" id="Facility"  value="Facility" <?php if ($_POST['groupby'] == 'Facility') echo 'CHECKED'; ?>/> 
												<label for="Facility">Facility</label>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="radio radio-inline pointer">
												<input type="radio" name="groupby" id="CPT" value="CPT" <?php if ($_POST['groupby'] == 'CPT') echo 'CHECKED'; ?>/> 
												<label for="CPT">CPT Code</label>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="radio radio-inline pointer">
												<input type="radio" name="groupby" id="DX" value="DX" <?php if ($_POST['groupby'] == 'DX') echo 'CHECKED'; ?>/> 
												<label for="DX">Dx Code</label>
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
												<input type="radio" name="output_option" id="output_view_only" value="view" <?php if ($_POST['output_option'] == 'view' || !$_POST['form_submitted']) echo 'CHECKED'; ?> />
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
							<div class="grpara">
								<div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
									<button class="savesrch" type="button" onClick="top.fmain.get_report()">Search</button>
								</div>
							</div>                                                                                        
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
										include('referringPhysicianResult.php');
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
	<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
		<input type="hidden" name="file_format" id="file_format" value="csv">
		<input type="hidden" name="zipName" id="zipName" value="">	
		<input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
	</form> 
	<form name="csvDownloadForm2" id="csvDownloadForm2" action="downloadCSV.php" method ="post" > 
		<input type="hidden" name="file" id="file" value="<?php echo $file;?>" />
		<input type="hidden" name="zipName" id="zipName" value="<?php echo $zipName;?>" />
	</form>
	<form name="printAddressLabels" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" >
		<input type="hidden" name="page" value="1.3" >
		<input type="hidden" name="onePage" value="false">
		<input type="hidden" name="op" value="p" >
		<input type="hidden" name="font_size" value="7.5">
		<input type="hidden" name="file_location" value="<?php echo $address_file_location; ?>">
	</form>		
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	$(function () { $('[data-toggle="tooltip"]').tooltip(); });
	var createZip = '<?php echo $createZip; ?>';
	$(document).ready(function () {
		$(".fltimg").click(function () {
			$("#sidebar").toggleClass("collapsed");
			$("#content1").toggleClass("col-md-12 col-md-9");

			if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
			} else {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
			}
			return false;
		});
		if(createZip == 1){
			document.csvDownloadForm2.submit();
			top.show_loading_image('hide');
		}
	});
	
	$(function() {
		$("#reff_physician").click(function(){window.open('refPhy.php?name_field=reff_physician&id_field=id_reff_physician','RefPhy','height=400, width=850, top=200, left=300, location=no, toolbar=0, menubar=0, status=0,resizable=1');});	
		$("#reff_physician").blur(function(){checkVal('reff_physician');});
	});
	
	function checkVal(ctrlId){
		if(document.getElementById(ctrlId).value==''){
			$('#id_reff_physician').val(''); 
		}
	}
	
	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
		mainBtnArr[2] = new Array("start_process", "Address Labels for Referring Physicians", "top.fmain.generate_address_labels();");
	}
	top.btn_show("PPR", mainBtnArr);

	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		if(dbtemp_name=='Referring Revenue'){
			var ctrlObj=$('#selectedRef');
			if(!ctrlObj.val()){
				top.show_loading_image('hide');
				alert('Please select referring physician groups.');
				return false;
			}
		}
		if ($('#searchCriteria').val() == '') {
			if ($('#chkSaveSearch').prop('checked') == true) {
				toggle_lightbox();
				return false;
			}
			if (lightBoxFlag && $('#report_name').val() == '') {
				alert('Please enter search name');
				return false;
			} else if (lightBoxFlag && $('#report_name').val() != '') {
				for (i = 0; i < arrSearchName.length; i++) {
					if (arrSearchName[i] == $('#report_name').val()) {
						alert('Search name already exist.');
						return false;
					}
				}
			}
		}
		document.frm_reports.submit();
	}
	
	lightBoxFlag = 0;
	function toggle_lightbox(show_hide_flag) {
		show_hide_flag = show_hide_flag || '';
		if (show_hide_flag == 'hide')
			lightBoxFlag = 1;
		else if (show_hide_flag == 'show')
			lightBoxFlag = 0;

		var popupid = '#divLightBox';
		if (!lightBoxFlag) {
			$(popupid).fadeIn();
			lightBoxFlag = 1;
		} else {
			$(popupid).fadeOut();
			lightBoxFlag = 0;
		}
		$('#report_name').val('');
		var popuptopmargin = ($(popupid).height() + 10) / 2;
		var popupleftmargin = ($(popupid).width() + 10) / 2;
		$(popupid).css({
			'margin-top': -popuptopmargin,
			'margin-left': -popupleftmargin
		});
	}

	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, 'l');
			window.close();
		}
	}

	function generate_address_labels(){
		//document.printAddressLabels.submit();
		top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf('<?php echo $address_file_location;?>', 'p');
			window.close();
	}

	function addRemoveGroupBy(dateRangeFor) {
		if (dateRangeFor == 'date_of_service') {
			$("#viewBy").append('<option value="operator">Operator</option>');
			$('#without_deleted_amounts').attr('disabled', true);
		} else {
			$("#viewBy option[value='operator']").remove();
			$('#without_deleted_amounts').attr('disabled', false);
		}
	}

	// SAVED SEARCH FUNCTIONS
	var dChk = 0;
	function callAjaxFile(ddText, opIndex) {
		oDropdown.off("change");
		var returnVal = 0;
		dChk = 1;
		var dd = confirm('Are sure to delete the selected search?');
		if (dd) {
			$.ajax({
				url: "delete_search.php?sTxt=" + ddText,
				success: function (callSts) {
					if (callSts == '1') {
						oDropdown.close();
						oDropdown.remove(opIndex);
						oDropdown.set("selectedIndex", 0);
					}
				}
			});
		}
		return returnVal;
	}

	function callSavedSearch(srchVal, formId) {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		if (srchVal != '' && dChk != '1') {
			dChk = 0;

			$('#call_from_saved').val('yes');
			$('#' + formId).submit();
		}
		dChk = 0;
	}
	// END SAVED SEARCH	

	function enable_disable_time(ctrlVal){
		if(ctrlVal=='transaction_date'){
			$('#hourFrom').prop('disabled', false);
			$('#hourTo').prop('disabled', false);
		}else{
			$('#hourFrom').prop('disabled', true);
			$('#hourTo').prop('disabled', true);
		}
	}
	$(document).ready(function (e) {
		DateOptions('<?php echo $_POST['dayReport'];?>');
		enable_disable_time('<?php echo $_POST['DateRangeFor'];?>');
		function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
		$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});	
	} 

	$(window).load(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
		//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
		if(printFile==1){
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);
	});
</script>
</body>
</html>