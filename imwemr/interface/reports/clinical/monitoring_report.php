<?php
$without_pat = "yes";
require_once("../reports_header.php");
require_once($GLOBALS['fileroot'].'/library/classes/msgConsole.php');
require_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');
require_once('../../../library/classes/class.reports.php');
require_once('../../../library/classes/cls_common_function.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

//GET LATEST SEARCH OF THIS USER
if (isset($saved_searched_id) && $saved_searched_id != '') {
    $saved_id = $saved_searched_id;
}
$searchOptions = '';
$srchQry = "Select id, DATE_FORMAT(saved_date, '".$date_format_SQL." %H:%i:%s') as saved_date,search_data, report, report_name, uid FROM reports_searches WHERE report='monitoring_report_criteria' ORDER BY saved_date DESC";
$srchRs = imw_query($srchQry);
while ($srchRes = imw_fetch_array($srchRs)) {
   $sel = '';
   if($saved_id == $srchRes['id']) {
		$searchCriteria =$saved_id = $srchRes['id'];
		$dataParts = explode('~', $srchRes['search_data']);
		$selOperId = explode(',',$dataParts[0]);
		$_POST['dayReport'] = $dataParts[1];
		if($_POST['dayReport']=='Selected Date'){ $_POST['dayReport']='Date';}
		$_REQUEST['Start_date'] = $dataParts[2];
		$_REQUEST['End_date'] = $dataParts[3];
		$_POST['task_type'] = $dataParts[4];
		$_POST['report_view'] = $dataParts[5];
   }
	$sel2 = '';
	$sel2 = ($_POST['savedCriteria'] == $srchRes['id']) ? 'selected' : ''; //SELECTION IS BASED ON SELECTED VALUE OF DD
	$searchOptions .= '<option value="' . $srchRes['id'] . '" title="'.$GLOBALS['webroot'].'/library/images/delete_icon.png" '. $sel2 . '>' . trim($srchRes['report_name']). '</option>';
	$arrSearchName[]=trim($srchRes['report_name']);	
}
json_encode($arrSearchName);

if($_POST['form_submitted']) {
	$stroperator = implode(',',$selOperId);
}

if(empty($stroperator)== true){
	$stroperator=implode(',',$operator_id);
}

$current_date=date('Y-m-d');
if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

//--- GET OPERATOR NAME ---
$operatorOption = $CLSCommonFunction->dropDown_providers($stroperator, $op_cnt, '');
$opr_cnt = sizeof(explode('</option>', $operatorOption)) - 1;

//--- GET ALL ORDER SET DETAILS ----
$q2 = imw_query("select * from order_sets order by createdy_on desc");
$orderSetArr = array();
while ($orderSetDetails = imw_fetch_assoc($q2)) {
	$id = $orderSetDetails['id'];
	$orderSetArr[$id] = $orderSetDetails;
}

//--- GET ALL ORDERS DETAILS ----
$q3 = imw_query("select * from order_details order by created_on desc");
$ordersDetailsArr = array();
while ($ordersQryRes = imw_fetch_assoc($q3)) {
	$id = $ordersQryRes['id'];
	$ordersDetailsArr[$id] = $ordersQryRes;
}

//--- GET ALL PROVIDER NAME ----
$providerRs = imw_query("Select id,fname,mname,lname from users");
$providerNameArr = array();
$providerNameArr[0] = 'No Provider';
while($providerResArr = imw_fetch_assoc($providerRs)){
	$id = $providerResArr['id'];
	$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
}

$dbtemp_name="Monitoring Report";
$dbtemp_name_CSV = strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";
?>
<style>
	.rptsearch1, .rptsearch2, .rptsearch3{ min-height:105px;}
    @media (min-width: 1400px) and (max-width: 2000px) {
	.rptsearch1 .col-sm-2 {
		width:14%;
    }}
</style>
		<style>
		.pd10.report-content {
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
		
		#searchcriteria .dropdown-menu{ height:200px; overflow-y: scroll}
	</style>
<form name="monitoring_report_form" id="monitoring_report_form" action="" method="post">
	<input type="hidden" name="form_submitted" id="form_submitted" value="1">
	<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
	<input type="hidden" name="call_from_saved" id="call_from_saved" value="0">
	<div class=" container-fluid">
		<div class="anatreport">
		<div id="select_drop" style="position:absolute;bottom:0px;"></div>                     
		<div class="row" id="row-main">
			<div class="col-md-3" id="sidebar">
			<div class="reportlft">
				<div class="practbox">
				<div class="anatreport"><h2>Practice Filter</h2></div>
				<div class="clearfix"></div>
				<div class="pd5" id="searchcriteria">
					<div class="row">
						<div class="col-sm-6">
							<label>Operator</label>
							<select name="operator_id[]" id="operator_id" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
								<?php echo $operatorOption; ?>
							</select>
						</div>
						<div class="col-sm-6">
							<label>Staff Internal Task Type</label>
							<select name="task_type" id="task_type" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10">
								<option value="" <?php if ($_POST['task_type'] == '') echo 'SELECTED'; ?>>Select</option>
								<option value="reminders" <?php if ($_POST['task_type'] == 'reminders') echo 'SELECTED'; ?>>Reminders</option>
								<option value="tasks" <?php if ($_POST['task_type'] == 'tasks') echo 'SELECTED'; ?>>Tasks</option>
								<option value="messages" <?php if ($_POST['task_type'] == 'messages') echo 'SELECTED'; ?>>Messages</option>
								<option value="orders" <?php if ($_POST['task_type'] == 'orders') echo 'SELECTED'; ?>>Orders</option>
								<option value="to_do" <?php if ($_POST['task_type'] == 'to_do') echo 'SELECTED'; ?>>To Do</option>
							</select>
						</div>
						<div class="col-sm-12">
							<label>Period</label>
							<div id="dateFieldControler">
								<select name="dayReport" id="dayReport" class="selectpicker"  data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
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
						<div class="col-sm-6"><br>
							<div class="radio radio-inline pointer">
								<input type="radio" name="report_view" id="summary" value="summary" <?php if ($_POST['report_view'] == 'summary' || !isset($_POST['report_view']) ) echo 'CHECKED'; ?>/>
								<label for="summary">Summary</label>
							</div>
							<div class="radio radio-inline pointer">
								<input type="radio" name="report_view" id="detail" value="detail" <?php if ($_POST['report_view'] == 'detail') echo 'CHECKED'; ?>/>
								<label for="detail">Detail</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<div class="grpara">
				<div class="anatreport"><h2>Saved Criteria</h2></div>
				<div class="clearfix"></div>
				<div class="pd5" id="searchcriteria">
					<div class="row">
						<div class="col-sm-5">
							<label>Saved Searches</label>
							<select name="savedCriteria" id="savedCriteria" style="width:100%;" data-maincss="blue" onchange="javascript:dChk=0; callSavedSearch(this.value, 'monitoring_report_form'); saved_functionality('savedCriteria');">
								<option value="">Select</option>
								<?php echo $searchOptions; ?>
							</select> 
							<input type="hidden" name="saved_searched_id" id="saved_searched_id" value="">
						</div>
						<div class="col-sm-2">
							<div class="checkbox pointer" style="padding-top:17px">
								<input type="checkbox" name="chkSaveSearch" id="chkSaveSearch" value="1" onClick="javascript:show_saved();" />
								<label for="chkSaveSearch">Save</label>
							</div>
						</div> 
						<div class="col-sm-5" id="div_search_name" style="display:none" >
							<label>Name of Search</label>
							<input type="text" name="search_name" id="search_name" value="" class="form-control" onBlur="javascript: saved_functionality('search_name');" />
						</div>                               
					</div>
				</div>
			</div>
		</div>
		<div id="module_buttons" class="ad_modal_footer text-center">
			<button class="savesrch" type="button" onClick="top.fmain.get_report()">Search</button>
		</div>
		</div>
		<div class="col-md-9" id="content1">
			<div class="btn-group fltimg pointer" role="group" aria-label="Controls">
				<img class="toggle-sidebar" src="../../../library/images/practflt.png" alt=""/><span class="toggle-sidebar glyphicon glyphicon-chevron-left"></span>
			</div>
			<div class="pd10 report-content">
				<div class="rptbox">
					<div id="html_data_div" class="row" >
					<?php
						if($_POST['callby_saved_dropdown']==''){
							if ($_POST['form_submitted']) {
									include('monitoring_result.php');
							} else {
								echo '<div class="text-center alert alert-info">No Search Done.</div>';
							}
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
<form name="csvDownloadForm" id="csvDownloadForm" action="../downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
	<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form> 
<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog"> 
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Modal Header</h4>
			</div>
			<form name="update_opr_frm" id="update_opr_frm">
				<input type="hidden" name="primaryID" id="primaryID" value="">	
				<input type="hidden" name="providerId" id="providerId" value="">	
				<input type="hidden" name="tableName" id="tableName" value="">	
				<input type="hidden" name="fieldName" id="fieldName" value="">	
				<input type="hidden" name="updateColum" id="updateColum" value="">	
				<div class="modal-body">
					<div class="form-group">
						<label for="operator_name">Operator Name</label>
						<select type="text" name="operator_name" id="operator_name" class="form-control minimal">
							<?php echo $operatorOption; ?>
						</select>
					</div>
				</div>	
				<div id="module_buttons" class="modal-footer ad_modal_footer">
					<button type="button" class="btn btn-success" onClick="SaveOprName();">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	var op='<?php echo $op;?>';
	
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
	});

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var HTMLCreated='<?php echo $HTMLCreated; ?>';
	var arrSearchName=[];
	var arrSearchName=<?php echo json_encode($arrSearchName); ?>;
	
	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		
		$('#callby_saved_dropdown').val('');
		
		if ($('#chkSaveSearch').prop('checked') == true) {
				if($('#savedCriteria').val()=='' && $('#search_name').val()==''){
					top.show_loading_image('hide');
					alert('Please select saved search or enter new search name.');
					return false;
				}
				else if($('#search_name').val()!=''){
					for(x in arrSearchName) {
						if (arrSearchName[x] == $('#search_name').val()) {
							top.show_loading_image('hide');
							alert('Search name already exist.');
							return false;
						}
					}
				}
			}
			
		document.monitoring_report_form.submit();
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
				url: "../delete_search.php?sTxt=" + ddText,
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

	function generate_pdf(op) {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, op);
			window.close();
		}
	}
	
	$(document).ready(function(e){
		DateOptions('<?php echo $_POST['dayReport'];?>');
		function set_container_height(){
			$_hgt = (window.innerHeight) - $('#module_buttons').outerHeight(true);
			$('.reportlft').css({
				'height':$_hgt,
				'max-height':$_hgt,
				'overflow-x':'hidden',
				'overflow-y':'auto'
			});
			$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});
		} 

		$("#savedCriteria").msDropdown({roundedBorder: false});
		oDropdown = $("#savedCriteria").msDropdown().data("dd");
		oDropdown.visibleRows(10);
		
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
	});
	
	function show_saved(){
		if($('#chkSaveSearch').is(":checked")){
			$('#div_search_name').css('display','block');
		}else{
			$('#div_search_name').css('display','none');
		}
	}

	function saved_functionality(callfrom){
	   if(callfrom=='search_name'){
		   if($('#search_name').val()!=''){
				$('#savedCriteria_child ul li').removeClass('selected');
				var oDropdown = $("#savedCriteria").msDropdown().data("dd");
				oDropdown.set("selectedIndex", 0);
		   }
	   }else{
		   if($('#savedCriteria').val()!=''){
			   $('#search_name').val('');
			   $('#div_search_name').css('display','none');
		   }else if($('#savedCriteria').val()=='' && $('#chkSaveSearch').is(":checked")){
			   $('#div_search_name').css('display','block');
		   }
	   }
	}

</script>
</body>
</html>