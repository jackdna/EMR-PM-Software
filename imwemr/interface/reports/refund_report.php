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
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = addslashes($fac_res['name']);
	if($selArrFacility[$fac_id])$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}
$fac_cnt=sizeof($fac_id_arr);
    
//--- GET FACILITY NAME ----
$posfacilityName = $CLSReports->getFacilityName($selArrFacility, '1');
$posfac_cnt = sizeof(explode('</option>', $posfacilityName)) - 1;


//--- GET PHYSICIAN NAME ---
$strPhysician = implode(',',$_REQUEST['phyId']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;


//CSV NAME
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";

//REPORT LOGIC INFORMATION FLOATING DIV
$logicWidth = 50;
$logicDiv = reportLogicInfo('refund_report', 'tpl', $logicWidth);
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
										<div id="rptInfoImg" style="float:right" class="rptInfoImg" onClick="showHideReportInfo(event, '<?php echo $logicWidth;?>')"></div>
									</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        
										<div class="row">
                                            <div class="col-sm-4">
                                                <label>Groups</label>
                                                <select name="groups[]" id="groups" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $groupName; ?>
												</select>
                                            </div>
											<div class="col-sm-4">
                                                <label>Facility</label>
                                                <select name="facility_name[]" id="facility_name" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $posfacilityName; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Physician</label>
                                                <select name="phyId[]" id="phyId" data-container="#provider_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $physicianName; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Operator</label>
                                                <select name="operator_id[]" id="operator_id" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $operatorOption; ?>
                                                </select>
                                            </div>
											
											<div class="col-sm-8">
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
                                                    <input type="radio" name="summary_detail" id="Summary" value="Summary" checked <?php if ($_POST['summary_detail'] == 'Summary') echo 'CHECKED'; ?> /> 
                                                    <label for="Summary">Summary</label>
                                                </div>
												<div class="radio radio-inline pointer">
                                                    <input type="radio" name="summary_detail" id="Detail" value="Detail" <?php if ($_POST['summary_detail'] == 'Detail') echo 'CHECKED'; ?> /> 
                                                    <label for="Detail">Detail</label>
                                                </div>
                                            </div>
                                            
<!--											<div class="col-sm-9"><br />
													<div class="radio radio-inline pointer">
														<input type="radio" name="DateRangeFor" id="dot" value="dot" <?php if ($_POST['DateRangeFor'] == 'dot' || !$_POST['form_submitted']) echo 'CHECKED'; ?>/> 
														<label for="dot">DOT</label>
													</div>
											</div>-->
											
										</div>
                                    </div>
                                </div>
								
								<div class="appointflt">
                                    <div class="anatreport"><h2>Analytic Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
                                            <div class="col-sm-12"><label>Search Patient</label></div>
                                        </div> 
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <input type="hidden" name="patientId" id="patientId" value="<?php //echo $patientId;   ?>">
                                                <input placeholder="Patient" type="text" id="patient" name="patient" onkeypress="{
                                                            if (event.keyCode == 13)
                                                                return searchPatient()
                                                        }" class="form-control" onblur="searchPatient(this)" value="<?php //echo $patient; ?>">
                                            </div> 
                                            <div class="col-sm-4">
                                                <select name="txt_findBy" id="txt_findBy" onkeypress="{
                                                            if (event.keyCode == 13)
                                                                return searchPatient()
                                                        }" class="form-control minimal">
                                                    <option value="Active" <?php if ($_POST['txt_findBy'] == 'Active') echo 'selected="selected"'; ?>>Active</option>
                                                    <option value="Inactive" <?php if ($_POST['txt_findBy'] == 'Inactive') echo 'selected="selected"'; ?>>Inactive</option>
                                                    <option value="Deceased" <?php if ($_POST['txt_findBy'] == 'Deceased') echo 'selected="selected"'; ?>>Deceased</option> 
                                                    <option value="Resp.LN" <?php if ($_POST['txt_findBy'] == 'Resp.LN') echo 'selected="selected"'; ?>>Resp.LN</option> 
                                                    <option value="Ins.Policy" <?php if ($_POST['txt_findBy'] == 'Ins.Policy') echo 'selected="selected"'; ?>>Ins.Policy</option>
                                                </select>
                                            </div> 
                                            <div class="col-sm-3 text-left">
                                                <button class="btn btn-success btn-sm" type="button" onclick="searchPatient();">Search</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>Last Name From</label>
                                                <input type="text" name="startLname" placeholder="From" id="startLname" value="<?php echo ($_POST['startLname'] ? $_POST['startLname']: 'a'); ?>" class="form-control">
                                            </div>
                                            <div class="col-sm-6">
                                                <label>To</label>
                                                <input type="text" name="endLname" placeholder="To" id="endLname" value="<?php echo ($_POST['endLname'] ? $_POST['endLname']: 'z'); ?>" class="form-control">
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
										if($_POST['form_submitted'] && $dbtemp_name == "Refund Report") {
                                            include('refund_report_result.php');
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
<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var conditionChk = '<?php echo $conditionChk; ?>';
	var pdfbtnCheck = '1';
	var csvbtnCheck = '1';
	var op='<?php echo $op;?>';
	var output='<?php echo $_POST['output_option'];?>';
	var HTMLCreated='<?php echo $HTMLCreated; ?>';
	var mainBtnArr = new Array();
	var btncnt=0;
	
	if(conditionChk==true){
		if (pdfbtnCheck == '1') {
			mainBtnArr[btncnt] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
			btncnt++;
		}
		if (csvbtnCheck == '1') {
			mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
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
			download_csv();
			top.show_loading_image('hide');
		}
	}	
	
	function searchPatient() {
        $("#patientId").val('');
        var name = document.getElementById("patient").value;
        var findBy = document.getElementById("txt_findBy").value;
        var validate = true;
        if (name.indexOf('-') != -1) {
            name = name.replace(' ', '');
            name = name.split('-');
            name = name[0]
            validate = false;
        }
        if (validate) {
            if (isNaN(name)) {
                pt_win = window.open("../../interface/scheduler/search_patient_popup.php?btn_enter=" + findBy + "&btn_sub=" + name + "&call_from=physician_console", "mywindow", "width=800,height=500,scrollbars=yes");
            } else {
                getPatientName(name);
            }
        }

        return false;
    }

    function getPatientName(id, obj) {
        $.ajax({
            type: "POST",
            url: "<?php echo $GLOBALS['webroot']; ?>/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid=" + id,
            dataType: 'JSON',
            success: function (r) {
                if (r.id) {
                    if (obj) {
                        set_xml_modal_values(r.id, r.pt_name);
                    } else {
                        $("#patient").val(r.pt_name);
                        $("#patientId").val(r.id);
                    }
                } else {
                    fAlert("Patient not exists");
                    $("#patient").val('');
                    return false;
                }
            }
        });
    }

    function physician_console(id, name) {
        $("#patient").val(name);
        $("#patientId").val(id);

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
        getPatientName('<?php echo $_REQUEST['patientId']; ?>');
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
    
</script> 
</body>
</html>