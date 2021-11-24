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


if($_POST['form_submitted']) {
	$groups_sel = array_combine($groups, $groups);
	$sc_name_sel = array_combine($sc_name, $sc_name);
	$strPhysician = implode(',',$Physician);
	$str_credit_physician= implode(',', $crediting_provider);
	$ins_carriers_sel = array_combine($ins_carriers, $ins_carriers);
	$ins_group_sel = array_combine($insuranceGrp, $insuranceGrp);
	$cpt_cat_id_sel = array_combine($cpt_cat_id, $cpt_cat_id);
	$Procedure_sel = array_combine($cpt_code_id, $cpt_code_id);
}

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

//--- GET Groups SELECT BOX ----
$group_query = "Select gro_id,name,del_status from groups_new order by name";
$group_query_res = imw_query($group_query);
$group_id_arr = array();
$groupName = "";
while ($group_res = imw_fetch_array($group_query_res)) {
	$sel='';
    $group_id = $group_res['gro_id'];
	if(in_array($group_id,$grp_id))$sel='SELECTED';
	$groupName .= '<option value="'.$group_res['gro_id'].'" '.$sel.'>' . $group_res['name'] . '</option>';
}
$grp_cnt=sizeof($group_id_arr);

//--- GET FACILITY SELECT BOX ----
$posfacilityName = $CLSReports->getFacilityName($sc_name_sel, '1');
$posfac_cnt = sizeof(explode('</option>', $posfacilityName)) - 1;

//--- GET PHYSICIAN NAME ---
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;

$creditPhysicinName = $CLSCommonFunction->drop_down_providers($str_credit_physician, '1', '1', '', 'report');
$allCrPhyCount = sizeof(explode('</option>', $creditPhysicinName)) - 1;


//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options = '';
$sel_ins_comp_options = '';
for ($i = 0; $i < count($insQryRes); $i++) {
    if ($insQryRes[$i]['attributes']['insCompName'] != 'No Insurance' && $insQryRes[$i]['attributes']['insCompName']!='') {
        $ins_id = $insQryRes[$i]['attributes']['insCompId'];
        //$ins_name = $insQryRes[$i]['attributes']['insCompINHouseCode'];
        $ins_name = $insQryRes[$i]['attributes']['insCompName'];
        $ins_status = $insQryRes[$i]['attributes']['insCompStatus'];
        if ($ins_name == '') {
            $ins_name = $insQryRes[$i]['attributes']['insCompName'];
            if (strlen($ins_name) > 20) {
                $ins_name = substr($ins_name, 0, 20) . '....';
            }
        }

		$sel = '';
		if (sizeof($ins_carriers_sel) > 0) {
			if ($ins_carriers_sel[$ins_id]) {
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
		if ($ins_group_sel[$grp_ins_ids])
            $selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}

//--- GET ALL CPT CATEGORIES NAME FOR SEARCH ---
$qry = "select cpt_cat_id, cpt_category from cpt_category_tbl order by cpt_category";
$rs = imw_query($qry);
//$category_data_arr = array();
$category_data='';
$category_data_arr[0] = 'No CPT Category';
while($res=imw_fetch_assoc($rs)){
	$sel='';
	$id = $res['cpt_cat_id'];
	$category_data_arr[$id] = $res['cpt_category'];
	if($cpt_cat_id_sel[$id])$sel='SELECTED';
	$category_data.='<option value="'.$id.'" '.$sel.'>'.$res['cpt_category'].'</option>';
}

//CPT CODES
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color =$sel= '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = $row['cpt_prac_code'];
    if ($row['delete_status'] == 1 || $row['status'] == 'Inactive'){
        $color = 'color:#CC0000!important';
	}
	$cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
	if($Procedure_sel[$cpt_fee_id])$sel='SELECTED';
	$cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
}
$allCPTCatCount = sizeof($cptDetailsArr);
//CSV NAME
$dbtemp_name = 'Cash Lag Analysis';
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
  <form name="frm_reports" id="frm_reports" action="" method="post">
		<input type="hidden" name="form_submitted" id="form_submitted" value="1">
        <input type="hidden" name="callby_saved_dropdown" id="callby_saved_dropdown" value="<?php echo $_POST['callby_saved_dropdown'];?>">
		<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
		<div class=" container-fluid">
			<div class="anatreport">
				<div id="common_drop" style="position:absolute;bottom:0px;"></div>
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
											<select name="grp_id[]" id="grp_id" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $groupName; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Facility</label>
											<select name="sc_name[]" id="sc_name" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $posfacilityName; ?>
											</select>
										</div>
										<div class="col-sm-6">
											<label>Provider</label>
											<select name="Physician[]" id="Physician" data-container="#common_drop" data-container="#provider_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $physicianName; ?>
											</select>
										</div>
										<div class="col-sm-6">
										  <label>Crediting Provider</label>
										  <select name="crediting_provider[]" id="crediting_provider" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
											  <?php echo $creditPhysicinName; ?>
										  </select>
										</div>										
										<div class="col-sm-12">
											<div class="checkbox pointer" style="padding-top:5px; padding-bottom:10px">
												<input type="checkbox" name="chksamebillingcredittingproviders" id="chksamebillingcredittingproviders" value="1" <?php if($_POST['chksamebillingcredittingproviders']==1)echo 'checked';?>  />
												<label for="chksamebillingcredittingproviders">Exclude where billing and crediting providers are same</label>
											</div>										  
										</div>	
										<div class="col-sm-10">
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
										<div class="col-sm-2"><br />
												<div class="radio radio-inline pointer">
													<input type="radio" name="DateRangeFor" id="date_of_service" value="date_of_service" <?php if ($_POST['DateRangeFor'] == 'date_of_service' || !$_POST['form_submitted']) echo 'CHECKED'; ?> />
													<label for="date_of_service">DOS</label>
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

<!--										<div class="col-sm-4">
											<div class="radio radio-inline pointer">
												<input type="radio" name="output_option" id="output_pdf" value="output_pdf" <?php if ($_POST['output_option'] == 'output_pdf') echo 'CHECKED'; ?>/>
												<label for="output_pdf">PDF</label>
											</div>
										</div> -->
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
									if($_POST['callby_saved_dropdown']==''){
										if($_POST['form_submitted']) {
											include('cash_lag_analyses_result.php');
										}else{
											echo '<div class="text-center alert alert-info">No Search Done.</div>';
										}
									}else{
										echo '<div class="text-center alert alert-info">No Search Done.</div>';
									}
									?>
									<iframe name="iframe_download_csv" id="iframe_download_csv" scrolling="0" frameborder="0" height="0" hspace="0" width="0"></iframe>
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
	<form name="csvDownloadForm2" id="csvDownloadForm2" action="downloadCSV.php" method ="post" >
		<input type="hidden" name="file" id="file" value="<?php echo $file;?>" />
		<input type="hidden" name="zipName" id="zipName" value="<?php echo $zipName;?>" />
	</form>
	<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" target="iframe_download_csv">
		<input type="hidden" name="file_format" id="file_format" value="csv">
		<input type="hidden" name="zipName" id="zipName" value="">
		<input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
	</form>
	
	
	
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	$(function () { $('[data-toggle="tooltip"]').tooltip(); });
	var createZip = '<?php echo $createZip; ?>';
	var arrSearchName=[];
	var arrSearchName=<?php echo json_encode($arrSearchName); ?>;
	var output_option='<?php echo $output_option; ?>';

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
	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';

	var mainBtnArr = new Array();
	if (printFile == '1') {
		//mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		mainBtnArr[0] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
	}
	top.btn_show("PPR", mainBtnArr);

	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		document.frm_reports.submit();
	}


	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, 'l');
			window.close();
		}
	}

	$(document).ready(function (e) {
		DateOptions('<?php echo $_POST['dayReport'];?>');

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

		if(printFile=='1' && output_option=='output_csv'){
			download_csv();
			top.show_loading_image('hide');
		}		
	});
</script>
</body>
</html>