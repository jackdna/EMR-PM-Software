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

//--- GET FACILITY SELECT BOX ----
//$sc_name = join(',',$facility_name);
$facility_id = array_combine($facility_name, $facility_name);
$facilityName = $CLSReports->getFacilityName($facility_id, '1');
$allFacCount = sizeof(explode('</option>', $facilityName)) - 1;

//---- GET COLLECTION TEMPLATE ---
$col_qry = imw_query("select id,collection_name from collection_letter_template order by collection_name");
while ($col_res = imw_fetch_array($col_qry)) {
	$sel='';
	if($collection_template_id==$col_res['id'])$sel='SELECTED';
	$collectionTemplate .= '<option value="'.$col_res['id'].'" '.$sel.'>' . $col_res['collection_name'] . '</option>';
}	

//	get next action codes
if($_SESSION['sess_privileges']['priv_admin']=='1'){
	$optionsPart.='<option value="other">Other</option>';
}else{
	$qryPart=" AND addedBy='0'";
}
$next_action_status_id = array_combine($next_action_status, $next_action_status);
$rs=imw_query("Select id,action_status FROM patient_next_action WHERE del_status='0' $qryPart ORDER BY action_status");
while($res=imw_fetch_array($rs)){
	$sel='';
	if(sizeof($next_action_status_id)>0){
		if($next_action_status_id[$res['id']]) { $sel='selected'; }
	}
	$nextActionOptions.='<option value="'.$res['id'].'" '.$sel.'>'.$res['action_status'].'</option>';
}

// GET MIN. BAL AMOUNT FROM POLICIES
$rsMinBal = imw_query("Select min_balance_bill FROM copay_policies LIMIT 0,1");
$resMinBal = imw_fetch_array($rsMinBal);
//$minBalance = $resMinBal['min_balance_bill'];

//--- GET PHYSICIAN NAME ---
$strPhysician = implode(',',$_REQUEST['phyId']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

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


//CSV NAME
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";
$aging_days = 90;
$statements = 5;
if($_REQUEST['Submit']){
	$aging_days=$_REQUEST['aging_days'];
	$statements=$_REQUEST['statements'];
	$minBalance=$_REQUEST['minBalance'];
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
            <input type="hidden" name="letter_chk_imp" id="letter_chk_imp" value="" />
            <input type="hidden" name="letter_pat_bal" id="letter_pat_bal" value="" />
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
                                            <div class="col-sm-4">
                                                <label>Groups</label>
                                                <select name="groups[]" id="groups" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
													<?php echo $groupName; ?>
												</select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Provider</label>
                                                <select name="phyId[]" id="phyId" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $physicianName; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label>Facility</label>
                                                <select name="facility_name[]" data-container="#common_drop" id="facility_name" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $facilityName; ?>
                                                </select>
                                            </div>
											<div class="col-sm-4">
                                                <label>Collection Letters</label>
                                                <select name="collection_template_id" data-container="#common_drop" id="collection_template_id" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" data-title="Select">
                                                	<option value="">Select</option>
													<?php echo $collectionTemplate; ?>
                                            	</select>	
                                            </div>
                                            <?php if($dbtemp_name == "Assessment"){?> 
                                            <div class="col-sm-4">
                                                <label>Start Date</label>
                                                <div class="input-group">
                                                    <input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo ($_POST['Start_date']!="")?$_POST['Start_date']:''; ?>" class="form-control date-pick">
                                                    <label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                </div>
                                            </div>	
                                            <div class="col-sm-4">	
                                                <label>End Date</label>
                                                <div class="input-group">
                                                    <input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo ($_POST['End_date']!="")?$_POST['End_date']:''; ?>" class="form-control date-pick">
                                                    <label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                </div>
                                            </div>
                                            <?php }else{ 
											
												if($_REQUEST['Start_date']==""){$_REQUEST['Start_date']=date($phpDateFormat);}
												if($_REQUEST['End_date']==""){$_REQUEST['End_date']=date($phpDateFormat);}
											?>
                                            <div class="col-sm-8">
                                                <label>Period</label>
                                                <div id="dateFieldControler">
                                                    <select name="dayReport" id="dayReport" class="selectpicker" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
                                                    <option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'SELECTED'; ?>>Daily</option>
                                                    <option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'SELECTED'; ?>>Weekly</option>
                                                    <option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'SELECTED'; ?>>Monthly</option>
                                                    <option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'SELECTED'; ?>>Quarterly</option>
                                                    <option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'SELECTED'; ?>>Date Range</option>
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
                                            <?php } ?>
                                            <div class="col-sm-6">
                                                <label for="next_action_status">Action Code</label>
                                                <select name="next_action_status[]" id="next_action_status" data-container="#common_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                	<?php echo $nextActionOptions; ?>
                                            	</select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="startLname">Last Name</label>
                                                <input type="text" name="startLname" id="startLname" placeholder="From" value="<?php echo $_REQUEST['startLname'];?>" class="form-control" />
                                            </div>   
                                            <div class="col-sm-3">
                                                <label for="endLname"></label>
                                                <input type="text" name="endLname" id="endLname" placeholder="To" value="<?php echo $_REQUEST['endLname'];?>" class="form-control" />
                                            </div>  
                                            <?php if($dbtemp_name == "Assessment"){?> 
                                            <div class="col-sm-6">
                                                <label for="aging_days">A/R Aging Days</label>
                                                <input type="text" name="aging_days" id="aging_days" value="<?php echo $aging_days;?>" class="form-control" />
                                            </div> 
                                            <div class="col-sm-6">
                                                <label for="statements">Statement Cycle#</label>
                                                <input type="text" name="statements" id="statements" value="<?php echo $_REQUEST['statements'];?>" class="form-control" />
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="minBalance">Min. Bal. Amt.</label>
                                                <input type="text" name="minBalance" id="minBalance" value="<?php echo $_REQUEST['minBalance'];?>" class="form-control" />
                                            </div>
                                            <div class="col-sm-6" style="margin-top:15px;">
                                                <label class="checkbox checkbox-inline pointer">
                                                    <input style="cursor:pointer;" type="checkbox" name="view_collections" id="view_collections" value="1" <?php if ($_POST['view_collections'] == '1') echo 'CHECKED'; ?>/>
                                                    <label for="view_collections">Include Pt U/Collection</label>
                                                </label>
                                            </div>  
                                            <?php } ?>
                                            <div class="col-sm-12">
												<div class="">
													<!-- Pt. Search -->
													<div class="col-sm-12"><label>Patient</label></div>
													<div class="col-sm-5">
														<input type="hidden" name="patientId" id="patientId" value="<?php echo $_REQUEST['patientId'];?>">
														<input class="form-control" type="text" id="txt_patient_name" value="<?php echo $_REQUEST['txt_patient_name'];?>" name="txt_patient_name" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control" >

													</div> 
													<div class="col-sm-5">
														<select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control minimal">
															<option value="Active">Active</option>
															<option value="Inactive">Inactive</option>
															<option value="Deceased">Deceased</option> 
															<option value="Resp.LN">Resp.LN</option> 
															<option value="Ins.Policy">Ins.Policy</option>
														</select>
													</div> 
													<div class="col-sm-2 text-center">
														<button class="btn btn-success" type="button" onclick="searchPatient();"><span class="glyphicon glyphicon-search"></span></button>
													</div> 	
												</div>
                                            </div>
										</div>
                                    </div>
                                </div>
								<div class="grpara">
									
                                </div>                                                                                        
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
										if($_POST['form_submitted'] && $dbtemp_name == "Assessment") {
											include('assessment_result.php');
										}elseif($_POST['form_submitted'] && $dbtemp_name == "Report") {
											include('collection_result.php');
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

		
<?php if($dbtemp_name == "Report"){ ?> 
	<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
		<input type="hidden" name="file_format" id="file_format" value="csv">
		<input type="hidden1" name="zipName" id="zipName" value="">	
		<input type="hidden1" name="file" id="file" value="<?php echo $csv_file_name1;?>" />
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
	var mainBtnArr = new Array();
	var btncnt=0;
	
	if(conditionChk==true){
		mainBtnArr[btncnt] = new Array("print", "Print", "top.fmain.generate_pdf();");
		btncnt++;
		mainBtnArr[btncnt] = new Array("print_next_action", "Print Letter & Next Action", "top.fmain.submit_letter();");
		btncnt++;
		<?php if($dbtemp_name == "Report"){?> 
		mainBtnArr[btncnt] = new Array("set_account_status", "Set Account Status", "top.fmain.set_patient_account_status();");
		btncnt++;
		<?php } if($dbtemp_name == "Report"){?>
		mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
		<?php } else {?>
		mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
		<?php } ?>
	
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
		top.show_loading_image('hide');
		top.show_loading_image('show');
		var curFrmObj = document.sch_report_form;
		
		if(document.getElementById("txt_patient_name").value==""){
			document.getElementById("patientId").value = '';
		}

		if($('#Start_date').val()=='' && $('#End_date').val()==''){
			if($('#aging_days').val()==''){
				top.show_loading_image('hide');
				top.fAlert('Either A/R Aging Days OR Date Range should be selected.');
				return false;
			}
		}else{
			var returnVal = validDateCheck("Start_date","End_date");
			if(returnVal == true){
				top.show_loading_image('hide');
				top.fAlert('Start date Should be less than End date.');	
				return false;
			}
		}

		document.sch_report_form.submit();
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
	
	function setOtherVal(){
		var selVals=$("#next_action_status").selectedValuesString();
		var selValsArr=selVals.split(',');
		if(selValsArr.length==1){
			if(selVals=='other'){
				$('#nextActionDiv').style.display='none';
				$('#otherNextActionDiv').style.display='block';
			}
		}
	}
	
	function hideAction(){
		$('#otherNextActionDiv').style.display='none';
		$('#nextActionDiv').style.display='block';
	}
	
	function saveOtherAction(){
		var otherVal = $('#otherNextAction').value;
		if(otherVal!=''){
			$.ajax({ 
				url: "add_other_action.php?otherVal="+otherVal,
				success: function(updateSts){
					top.show_loading_image("hide");
					var retVals= updateSts.split('~~~');
					if(retVals[0]==1){
						top.fAlert("New Action Code is Saved.");
					}
					if(retVals[0]==0){
						top.fAlert("<font color=\"#CC0000\">Error - Action Code already exists</font>.");
					}
					$('#otherNextAction').value= '';
					$('#otherNextActionDiv').style.display='none';
					$('#nextActionDiv').style.display='block';
				}
			});
		}
	}
	
	function submit_letter(){
		var obj= document.getElementsByName('chk_box[]');
		var txtObj= document.getElementsByName('pat_collection[]');
		var encId_arr = new Array();
		var patId_arr = new Array();
		var submitFlag = false;
		var popWidth = 500;
		var popHeight = 300;
		var wleft= (screen.width / 2) - popWidth/2;
		var wtop= (screen.height / 2) - popHeight/2;

		for(i=0,j=0;i<obj.length;i++){
			if(obj[i].checked==true){
				submitFlag = true;
				encId_arr[j] = obj[i].value;
				patId_arr[j] = txtObj[i].value;
				j++;
			}
		}
		
		if(submitFlag == true){
			var encId_str = encId_arr.join(",");
			var patId_str = patId_arr.join(",");
			
			$("#letter_chk_imp").val(encId_str);
			$("#letter_pat_bal").val($('#patBalance').val());
			
			JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			window.open(JS_WEB_ROOT_PATH+'/interface/reports/action_status_patients.php?patId_str='+patId_str,'setActionSts','left='+wleft+'px,top='+wtop+'px,width='+popWidth+'px, height='+popHeight+'px');
			get_sch_report();
		}
		else{
			top.show_loading_image("hide");
			top.fAlert("Please select any patient.");
		}
	}
	
	function chk_all_fun(val,mode){
		var obj= document.getElementsByName('chk_box[]');
	
		if(mode=='underCollection'){
			$("#tblUnderColl input:checkbox").each(function() {
				this.checked=val;
			});
		}else{
			for(i=0;i<obj.length;i++){
				obj[i].checked=val;
			}
			document.getElementById('chk_all_u').checked=val;
		}
	}
	
	function searchPatient(){
	var name = document.getElementById("txt_patient_name").value;
	var findBy = document.getElementById("txt_findBy").value;
	var validate = true;
	  if(name.indexOf('-') != -1){
		name = name.replace(' ','');
		name = name.split('-');
		name = name[0]
		validate = false;
	  }
	  if(validate){
		if(isNaN(name)){
			pt_win = window.open("../../interface/scheduler/search_patient_popup.php?btn_enter="+findBy+"&btn_sub="+name+"&call_from=physician_console","mywindow","width=800,height=500,scrollbars=yes");
		}
		else{
			getPatientName(name);
		}
	  }
	
	return false;
}
function getPatientName(id,obj){
	$.ajax({
		type: "POST",
		url: top.JS_WEB_ROOT_PATH+"/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
		dataType:'JSON',
		success: function(r){
			if(r.id){
				if(obj){
					set_xml_modal_values(r.id,r.pt_name);
				}else{
					$("#txt_patient_name").val(r.pt_name);
					$("#patientId").val(r.id);
				}
			}else{
				top.fAlert("Patient not exists");
				$("#txt_patient_name").val('');
				return false;
			}	
		}
	});
}

//previous name was getvalue
function physician_console(id,name){
	document.getElementById("txt_patient_name").value = name;
	document.getElementById("patientId").value = id;
}
	
	$(window).load(function(){
		set_container_height();
	});

	$(window).resize(function(){
		set_container_height();
	});
	var page_heading = "<?php echo $dbtemp_name; ?>";
	set_header_title(page_heading);
	
	function set_patient_account_status()
	{
		top.show_loading_image("show");
		var obj= document.getElementsByName('chk_box[]');
		var txtObj= document.getElementsByName('pat_collection[]');
		var patId_arr = new Array();
		var submitFlag = false;
		var popWidth = 500;
		var popHeight = 300;
		var wleft= (screen.width / 2) - popWidth/2;
		var wtop= (screen.height / 2) - popHeight/2;

		for(i=0,j=0;i<obj.length;i++){
			if(obj[i].checked==true){
				submitFlag = true;
				patId_arr[j] = txtObj[i].value;
				j++;
			}
		}
		
		if(submitFlag == true){
			var patId_str = patId_arr.join(",");
			JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			window.open(JS_WEB_ROOT_PATH+'/interface/reports/account_status_patients.php?patId_str='+patId_str,'setAccountSts','left='+wleft+'px,top='+wtop+'px,width='+popWidth+'px, height='+popHeight+'px');
			top.show_loading_image("hide");
		}else{
			top.show_loading_image("hide");
			top.fAlert("<font color='#CC0000'>Select the patients to set the Account Status.</font>");
		}
		top.show_loading_image("hide");				
	}
</script> 
</body>
</html>