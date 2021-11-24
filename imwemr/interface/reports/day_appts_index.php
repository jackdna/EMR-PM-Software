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
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('common/report_logic_info.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if (empty($_REQUEST['from_date']) == true && empty($_REQUEST['to_date']) == true) {
    $_REQUEST['from_date'] = $_REQUEST['to_date'] = date($phpDateFormat);
}

if ($_POST['form_submitted']) {
	
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();
	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}
	//---------------------
	$phpDateFormat 		= phpDateFormat();
	$curDate 			= date($phpDateFormat.' h:i A');
	$op_name_arr 		= preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy 			= ucfirst(trim($op_name_arr[1][0]));
	$createdBy 		   .= ucfirst(trim($op_name_arr[0][0]));
    $processReport = $_REQUEST['process'];
}

//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['facl'],$_REQUEST['facl']);
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

//--- GET PROCEDURE SELECT BOX ----
$selArrProcedure= array_combine($_REQUEST['procedureType'],$_REQUEST['procedureType']);
$qry_procedure = "SELECT id, proc, acronym FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id =0 AND active_status = 'yes' AND user_group <> ''
					ORDER BY proc";
$res_procedure = @imw_query($qry_procedure);
$proName = "";
while($row_procedure = @imw_fetch_array($res_procedure)){	
	$select = '';
	if(strpos($row_procedure['proc'],'<') > 0){
		$row_procedure['proc'] = str_replace('<',' &lt; ',$row_procedure['proc']);
	}else if(strpos($row_procedure['cpt_desc'],'>') > 0){
		$row_procedure['proc'] = str_replace('>',' &gt; ',$row_procedure['proc']);
	}
	if($selArrProcedure[$row_procedure['id']])$select='SELECTED';	
	$proName .= '<option value="'.$row_procedure['id'].'" '.$select.'>' . $row_procedure['proc'] . '</option>';
	$arrDDProcCount[$row_procedure['id']]=$row_procedure['id'];
}

//--- GET ALL PHYSICIAN DETAILS ----
$physicianName = $CLSCommonFunction->dropDown_providers(implode(',',$doctor_IDS),'1','1');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

//SET Recall Template CODE
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

$dbtemp_name = 'Day Appts';
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
<form name="frm_reports" id="frm_reports" action="" method="post">
<input type="hidden" name="form_submitted" id="form_submitted" value="1">
<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
<div class=" container-fluid">
	<div class="anatreport">
		 <div id="select_drop" style="position:absolute;bottom:0px;"></div>   
		<div class="row" id="row-main">
			<div class="col-md-3" id="sidebar">
				<div class="reportlft" style="height:100%;">
					<div class="practbox">
						<div class="anatreport"><h2>Practice Filter</h2></div>
						<div class="clearfix"></div>
						<div class="pd5" id="searchcriteria">
							<div class="row">
								<div class="col-sm-6">
									<label>Export From</label>
									<div class="input-group">
										<input type="text" name="from_date" placeholder="From" style="font-size: 12px;" id="from_date" value="<?php echo $_REQUEST['from_date']; ?>" class="form-control date-pick">
										<label class="input-group-addon" for="from_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
								</div>	
								<div class="col-sm-6">	
									<label>Export To</label>
									<div class="input-group">
										<input type="text" name="to_date" placeholder="To" style="font-size: 12px;" id="to_date" value="<?php echo $_REQUEST['to_date']; ?>" class="form-control date-pick">
										<label class="input-group-addon" for="to_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
								</div>
								<div class="col-sm-6">
									<label>Facility</label>
									<select name="facl[]" id="facl" class="selectpicker" data-container="#select_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
									<?php echo $facilityName; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Provider</label>
									<select name="doctor_IDS[]" id="doctor_IDS" class="selectpicker" data-container="#select_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $physicianName; ?>
									</select>
								</div>
								<div class="col-sm-12">
									<label>Procedure Type</label>
									<select name="procedureType[]" id="procedureType" class="selectpicker" data-container="#select_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
									<?php echo $proName; ?>
									</select>
								</div>
								<div class="col-sm-6">	
									<label>Report Type</label>
									<select name="repType" id="repType" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" onchange="check_box(this.value)" >
										<option value="">Select</option>
										<option <?php if ($_POST['repType'] == 'House_Calls') echo 'SELECTED'; ?> value="House_Calls" >Televox</option>
										<option <?php if ($_POST['repType'] == 'pam') echo 'SELECTED'; ?> value="pam">PAM2000</option>
										<option <?php if ($_POST['repType'] == 'phoneTree') echo 'SELECTED'; ?> value="phoneTree">PhoneTree</option>
										<option <?php if ($_POST['repType'] == 'send_email') echo 'SELECTED'; ?> value="send_email">Email</option>
									</select>
								</div>
								<div class="col-sm-6">	
									<label>Template</label>
									<select name="recallTemplatesListId" id="recallTemplatesListId" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10">
									<option value="">Select Template</option>
									<?php echo $recall_options; ?>
									</select>
								</div>
								<div class="col-sm-6">
										<div class="checkbox checkbox-inline pointer">
											<input type="checkbox" name="excSentEmail" id="excSentEmail" value="1" <?php if ($_POST['excSentEmail'] == '1') echo 'CHECKED'; ?>/> 
											<label for="excSentEmail">Exclude Sent Email</label>
										</div>
								</div>
								<div class="col-sm-6">
									<div class="checkbox checkbox-inline pointer">
										<input type="checkbox" name="sendUnique" id="sendUnique" value="1" <?php if ($_POST['sendUnique'] == '1') echo 'CHECKED'; ?>/> 
										<label for="sendUnique">Send Unique Email</label>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="checkbox checkbox-inline pointer">
										<input type="checkbox" name="patHavingPhoneNotEmail" id="patHavingPhoneNotEmail" value="1" <?php if ($_POST['patHavingPhoneNotEmail'] == '1') echo 'CHECKED'; ?>/> 
										<label for="patHavingPhoneNotEmail">Patients having phone no but not email ids</label>
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
			</form>
			<div class="col-md-9" id="content1">
				<div class="btn-group fltimg" role="group" aria-label="Controls">
					<img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
				</div>
				<div class="pd5 report-content">
					<div class="rptbox">
						<div id="html_data_div" class="row">
							<?php
							if($_POST['form_submitted']) {
								 include('day_appts_result.php');
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
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	$(function () { $('[data-toggle="tooltip"]').tooltip(); });
	
	$(document).ready(function () {
		$('#check_all').click(function(event) {  //on click 
			if($(this).is(':checked')) { // check select status
				$('.checkbox1').each(function() { //loop through each checkbox
					$(this).prop('checked', true);  //select all checkboxes with class "checkbox1"               
				});
			}else{
				$('.checkbox1').each(function() { //loop through each checkbox
					$(this).prop('checked', false); //deselect all checkboxes with class "checkbox1"                       
				});         
			}
		});	
		$(".fltimg").click(function (){
			$("#sidebar").toggleClass("collapsed");
			$("#content1").toggleClass("col-md-12 col-md-9");

			if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
			} else {
				$('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
			}
			return false;
		});
		check_box($("#repType").val());
	});
	
	function checkdata(){	
		var mainBtnArr = new Array();
		mainBtnArr[0] = new Array("get_rep","Get1 Report","top.fmain.sbmtForm();");
		top.btn_show("O4A",mainBtnArr);
		//reset target
		frm_reports.target="";
		//set frame empty
		//document.blankForm.submit();
		var msg = ''; 
		if(frm_reports.from_date.value==""){
			msg = msg + '  Please enter from date.\n'; 
		}		
		if(frm_reports.to_date.value==""){
			msg = msg + '  Please enter to date.\n'; 
		}
		if(msg!=""){
			fAlert (msg);
			msg='';
			return false;
		}
		if(frm_reports.repType.value=='send_email'){
			if(!frm_reports.recallTemplatesListId.value){
				top.show_loading_image("hide");
				top.fAlert("Select Letter Template first!");
				return false;	
			}
			var mainBtnArr = new Array();
			mainBtnArr[0] = new Array("get_rep","Get Report","top.fmain.sbmtForm();");
			mainBtnArr[1] = new Array("sendEmail","Send Email","top.fmain.send_email();");
			top.btn_show("O4A",mainBtnArr);
		}	
		document.frm_reports.submit();
	}			
	function send_email(){
		var res = $("input[name='pat_email[]']:checked").length;
		if(res<=0){
			top.fAlert("Please select at least one record");
			return false;	
		}
		top.show_loading_image('show');
		document.send_email.submit();
	}
	
	function check_box(objVal){
		//START CODE TO DISABLE SELECT LIST OF TEMPLATES,DATE RANGE(FROM-TO)
		if(objVal == 'recall_letters' || objVal == 'send_email') {
			$('#recallTemplatesListId').prop('disabled', false);
				if(document.getElementById("patHavingPhoneNotEmail")){
					if(document.getElementById("repType").value =='send_email'){
					$('#patHavingPhoneNotEmail').prop('disabled', true)	;
					}else{
						$('#patHavingPhoneNotEmail').prop('disabled', true);	
						$('#patHavingPhoneNotEmail').prop('checked', false);	
					
					}
				}
		}else{
			document.getElementById("recallTemplatesListId").value='';
			$('#recallTemplatesListId').prop('disabled', true);	
			if(document.getElementById("patHavingPhoneNotEmail")){
				$('#patHavingPhoneNotEmail').prop('disabled', false);	
			}
		}
		$('.selectpicker').selectpicker('refresh');
	}	

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var HTMLCreated = '<?php echo $HTMLCreated; ?>';
	
	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	

	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		if((document.getElementById('repType').value == 'recall_letters' || document.getElementById('repType').value == 'send_email') && (document.getElementById('recallTemplatesListId').value == '')){
			top.show_loading_image("hide");
			top.fAlert("Select Recall Letter Template first!");
			return false;
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
	
	function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
	} 
	
	function sbmtForm(){
		checkdata();
	}
	$(document).ready(function (e) {
		DateOptions('<?php echo $_POST['dayReport'];?>');
		enable_disable_time('<?php echo $_POST['DateRangeFor'];?>');
		var page_heading = "<?php echo $dbtemp_name; ?>";
		set_header_title(page_heading);
	});

	$(window).load(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
		if(HTMLCreated==1){		
			$(".fltimg .toggle-sidebar").click();
		}
	});

	$(window).resize(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
	});
	
	<?php if(isset($_REQUEST['recallTemplatesListId']) && isset($_REQUEST['form_submitted']) && empty($_REQUEST['recallTemplatesListId']) == false){ ?>
		var mainBtnArr = new Array;
		mainBtnArr[0] = new Array("sendEmail","Send Email","top.fmain.send_email();");
		top.btn_show("O4A",mainBtnArr);
	<?php } ?>
</script>
</body>
</html>