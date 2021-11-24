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

$selOperId= join(',',$_REQUEST['operator_id']);
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, $op_cnt, '');
$opr_cnt = sizeof(explode('</option>', $operatorOption)) - 1;

//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['comboFac'],$_REQUEST['comboFac']);
$posfacilityName = $CLSReports->getFacilityName($selArrFacility, '1');
$posfac_cnt = sizeof(explode('</option>', $posfacilityName)) - 1;


//--- GET PHYSICIAN NAME ---
$strPhysician = implode(',',$_REQUEST['comboProvider']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;

//CPT CODES
$qry = "select cpt_prac_code,cpt_fee_id,cpt_desc from cpt_fee_tbl where delete_status = '0' order by cpt_desc asc";
$res = imw_query($qry);
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
	$sel = (in_array($cpt_fee_id ,$_POST['cptCodeId']) === true) ? 'selected' : '';
	$cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" .$cp.' - '.$cpt_prac_code . "</option>";
}

// GET DX CODES
$diag_qry = "select dx_code,diagnosis_id,diag_description from diagnosis_code_tbl order by diag_description asc";
$diag_run = imw_query($diag_qry);
$arrDXCodes_options = "";
$arrDXCodes = array();
while($diag_fet=imw_fetch_array($diag_run)) {
	$dxId = $diag_fet['diagnosis_id'];
	$arrDXCodes[$dxId] = $diag_fet['diag_description'].' - '.$diag_fet['dx_code'];
	$sel = (in_array($dxId ,$_POST['dxCodeId']) === true) ? 'selected' : '';	
	$arrDXCodes_options .= "<option value='" . $dxId . "' ".$sel.">" .$diag_fet['diag_description'].' - '.$diag_fet['dx_code']. "</option>";
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

$dbtemp_name = "Reminder Lists";
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
                    <div id="common_drop" style="position:absolute;bottom:0px;"></div>
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Practice Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
											<div class="col-sm-2">
												<label for="age_criteria">Age</label>
												<select id="age_criteria" name="age_criteria" class="selectpicker" data-width="100%">
													<option <?php if ($_POST['age_criteria'] == 'greater') echo 'SELECTED '; ?> value="greater"> &gt; </option>											
													<option <?php if ($_POST['age_criteria'] == 'greater_equal') echo 'SELECTED '; ?> value="greater_equal"> &gt; = </option>
													<option <?php if ($_POST['age_criteria'] == 'equalsto') echo 'SELECTED '; ?> value="equalsto"> = </option>
													<option <?php if ($_POST['age_criteria'] == 'less_equal') echo 'SELECTED '; ?> value="less_equal"> &lt; = </option>
													<option <?php if ($_POST['age_criteria'] == 'less') echo 'SELECTED '; ?> value="less"> &lt; </option>
												</select>
											</div>
											<div class="col-sm-2">
												<label for="age">Yrs.</label>
												<input type="text"  id="age" name="age" class="form-control" size="4" value="<?php echo $_POST['age']; ?>">
											</div>
											<div class="col-sm-4">
												<label for="gender">Gender</label>
												<select name="gender" id="gender" class="selectpicker" data-width="100%">
													<option value=""></option>
													<option <?php if ($_POST['gender'] == 'male') echo 'SELECTED '; ?> value="male">Male</option>
													<option <?php if ($_POST['gender'] == 'female') echo 'SELECTED '; ?> value="female">Female</option>
												</select>
											</div>
											<div class="col-sm-4">
												<label for="cpt">Cpt Code</label>
												<select name="cptCodeId[]" id="cptCodeId" class="selectpicker" data-container="#common_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $cpt_code_options; ?>
												</select>
											</div>
											<div class="clearfix"></div>
											<div class="col-sm-4">
												<label>Facility</label>
												<select name="comboFac[]" id="comboFac" class="selectpicker" data-container="#common_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $posfacilityName; ?>
												</select>
											</div>
											<div class="col-sm-4">
												<label>Provider</label>
												<select name="comboProvider[]" id="comboProvider" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $physicianName; ?>
												</select>
											</div>
											<div class="col-sm-4">
												<label>ICD9</label>
												<select name="dxCodeId[]" id="dxCodeId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $arrDXCodes_options; ?>
												</select>
											</div>
											<div class="col-sm-4">
												<label>ICD10</label>
												<select name="dxCodeId10[]" id="dxCodeId10" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $all_dx10_code_options; ?>
												</select>
											</div>
											
											<div class="col-sm-4">
												<label>Tests</label>
												<select name="add_tests[]" id="add_tests" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $arrTests_options; ?>
												</select>
											</div>
											
											<div class="col-sm-4">
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
											<div class="clearfix"></div>	
											<div class="col-sm-4">
												<label>Labs</label>
												<select name="labIds[]" id="labIds" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $lab_options; ?>
												</select>
											</div>
											<div class="col-sm-8">
												<label for="lab_criteria">Labs Results</label>
												<div class="row">
												<div class="col-sm-6">
													<select id="lab_criteria" name="lab_criteria" class="selectpicker" data-width="100%">
														<option <?php if ($_POST['lab_criteria'] == 'greater') echo 'SELECTED '; ?> value="greater"> &gt; </option>											
														<option <?php if ($_POST['lab_criteria'] == 'greater_equal') echo 'SELECTED '; ?> value="greater_equal"> &gt; = </option>
														<option <?php if ($_POST['lab_criteria'] == 'equalsto') echo 'SELECTED '; ?> value="equalsto"> = </option>
														<option <?php if ($_POST['lab_criteria'] == 'less_equal') echo 'SELECTED '; ?> value="less_equal"> &lt; = </option>
														<option <?php if ($_POST['lab_criteria'] == 'less') echo 'SELECTED '; ?> value="less"> &lt; </option>
													</select>
												</div>
												<div class="col-sm-6">
													<input type="text"  id="lab_result" name="lab_result" class="form-control" size="4" value="<?php echo $_POST['lab_result']; ?>">
												</div>											
												</div>											
											</div>
											<div class="clearfix"></div>
											<div class="col-sm-4">
												<label for="lab_criteria">Report Type</label>								
												<select name="repType" id="repType" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10">
													<option <?php if ($_POST['repType'] == 'houseCalls') echo 'SELECTED '; ?> value="houseCalls">Televox</option>
													<option <?php if ($_POST['repType'] == 'pam') echo 'SELECTED '; ?> value="pam">PAM2000</option>
													<option <?php if ($_POST['repType'] == 'address_labels') echo 'SELECTED '; ?> value="address_labels">Address Labels</option>
												</select>
											</div> 
											<div class="col-sm-8">
                                                <label>Period</label>
												<div id="dateFieldControler">
                                                    <select name="dayReport" id="dayReport" class="selectpicker"   data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
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
								<div class="clearfix"></div>
                            </div>
                            <div id="module_buttons" class="ad_modal_footer text-center">
                            	<button class="savesrch" type="button" onClick="top.fmain.get_sch_report()">Search</button>
                           	</div>
                        </div>
                        <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
                            </div>
							<div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
                                        <?php
										if($_POST['form_submitted']) {
											include('reminder_lists_result.php');
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
<form name="txtForm" id="txtForm" action="save_house_csv.php?fn=<?php echo $filename; ?>" method="post"></form>
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var repType = '<?php echo $_REQUEST['repType']; ?>';
	//BUTTONS
	var i=0;
	var mainBtnArr = new Array();
	if (printFile == '1') {
			mainBtnArr[i] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
			i++;
			mainBtnArr[i] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
			i++;
		if(repType == 'houseCalls' || repType == 'pam'){
			mainBtnArr[i] = new Array("start_process", "Export TXT", "top.fmain.saveExportTXT();");
			i++;
		}
	}
	top.btn_show("PPR", mainBtnArr);
	
	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, 'l');
			window.close();
		}
	}
	function get_sch_report() {
		top.show_loading_image('show');
		if(document.getElementById("age").value == "" && document.getElementById("gender").value == ""){
			top.fAlert("Please select at least one parameter - Gender or Age");
			top.show_loading_image('hide');
			return false;
		}
		document.sch_report_form.submit();
		top.show_loading_image('hide');
	}
	
	function saveExportTXT(){
		top.show_loading_image("hide");
		window.txtForm.submit();
	}
	
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
		if(printFile==1){		
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
    
</script> 
</body>
</html>