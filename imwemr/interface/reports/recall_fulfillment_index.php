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

// GET MONTHS
$mon = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sept", "10" => "Oct", "11" => "Nov", "12" => "Dec");
$curMonth = date("m");
$month_options ="";
foreach($mon as $key => $val){
	$select = "";
	if($_REQUEST['months'] == "" ){
		$_REQUEST['months'] = $curMonth;
	}
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
for($i=date("Y")-1;$i<=($maxyear);$i++){
	$arrYears[$i] = $i;
	$selct = "";
	if($_REQUEST['years'] == "" ){
		$_REQUEST['years'] = $curYear;
	}
	if($_REQUEST['years'] == $i){
		$selct = 'SELECTED';
	}
	$year_options .= "<option value='" . $i . "' " . $selct . ">" . $i . "</option>";
}

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();
//SET Recall CODE
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

//GET Provider
$strPhysician = implode(',',$_REQUEST['phyId']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');

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

$dbtemp_name = 'Recall Fulfillment';
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
					<div id="common_drop" style="position:absolute;bottom:0px;"></div>
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Practice Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        
										<div class="row">
											<div class="col-sm-12">
												<label>Facility</label>
												<select name="facility_name[]" id="facility_name" class="selectpicker" data-container="#common_drop" data-width="100%" data-size="10"	multiple data-actions-box="true" data-title="Select All">
													<?php echo $facilityName; ?>
												</select>
											</div>
											<div class="col-sm-12">
                                                <label>Recall Procedures</label>
                                                <select name="procedures[]" id="procedures" data-container="#common_drop" <?php echo ($temp_id && !isset($filter_arr['procedures']))?'disabled':''; ?> class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $proc_options; ?>
                                                </select>
                                            </div>											
											<div class="col-sm-6">
												<label>Recall Month</label>
												<select name="months" id="months" class="selectpicker" data-width="100%" data-size="10" data-container="#common_drop">
													<?php echo $month_options; ?>
												</select>
											</div>
											<div class="col-sm-6">
                                                <label>Recall Year</label>
												<select name="years" id="years" class="selectpicker" data-width="100%" data-size="10" data-container="#common_drop">
													<?php echo $year_options; ?>
												</select>
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
											<div class="col-sm-6">	
												<label>Report Type</label>
												<select name="repType" id="repType" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" onchange="check_box(this.value)" >
													<option value="">Select</option>
													<option <?php if ($_POST['repType'] == 'houseCalls') echo 'SELECTED'; ?> value="houseCalls" >House Calls</option>
													<option <?php if ($_POST['repType'] == 'pam') echo 'SELECTED'; ?> value="pam">PAM2000</option>
													<option <?php if ($_POST['repType'] == 'recall_letters') echo 'SELECTED'; ?> value="recall_letters">Recall Letters</option>
													<option <?php if ($_POST['repType'] == 'address_labels') echo 'SELECTED'; ?> value="address_labels">Address Labels</option>
													<option <?php if ($_POST['repType'] == 'send_email') echo 'SELECTED'; ?> value="send_email">Email</option>
												</select>
											</div>
											<div class="col-sm-6">	
                                                <label>Template</label>
												<select name="recallTemplatesListId" id="recallTemplatesListId" disabled class="selectpicker" data-width="100%" data-size="10" data-container="#common_drop">
												<option value="">Select Template</option>
												<?php echo $recall_options; ?>
												</select>
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
                        <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
                            </div>
							<div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
                                        <?php
										if($_POST['form_submitted']) {
											include('recall_fulfillment_result.php');
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
<form name="frmPrint" action="print.php" target="_blank" method="post">
	<input type="hidden" name="prn_header" value="">
	<input type="hidden" name="prn_body" value="">
</form>
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form> 
<form name="frmRecallPDF" action="recall_report_print_letters.php" method="post" target="_blank">
	<input type="hidden" name="months" value="<?php echo $months;?>">
	<input type="hidden" name="years" value="<?php echo $years;?>">
	<input type="hidden" name="pat_id_imp" value="<?php echo $pat_id_imp;?>">
	<input type="hidden" name="recallTemplatesListId" value="<?php echo $recallTemplatesListId;?>">
</form>
<form name="frmPDF" action="recall_report_print_labels.php" method="post" target="_blank">
	<input type="hidden" name="months" id="labelMoths" value="<?php echo $months;?>">
	<input type="hidden" name="years" id="labelYears" value="<?php echo $years;?>">
	<input type="hidden" name="pat_id_imp" id="labelPatIds" value="<?php echo $pat_id_imp;?>">
</form>
<form name="txtForm" id="txtForm" action="" method="post"></form>
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	$(function () { $('[data-toggle="tooltip"]').tooltip(); });
	
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
		check_box($("#repType").val());
	});

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	
	function print_records_in_new_window(print_section){
		if(print_section == "unfulfilled"){
			var print_header = escape(document.getElementById("unful_header").innerHTML);
			var print_body = escape(document.getElementById("unfulfilled_recalls").innerHTML);
		}
		if(print_section == "fulfilled"){
			var print_header = escape(document.getElementById("ful_header").innerHTML);
			var print_body = escape(document.getElementById("fulfilled_recalls").innerHTML);
		}
		document.frmPrint.prn_header.value = print_header;
		document.frmPrint.prn_body.value = print_body;
		document.frmPrint.submit();
	}
	
	var i=0;
	var mainBtnArr = new Array();
	<?php  if(count($fulfilled) > 0 && $repType ==''){ ?>
		mainBtnArr[i] = new Array("search","Print A/F","top.fmain.print_records_in_new_window('fulfilled');");
		i++;
	<?php  } 
	if(count($unfulfilled) > 0 && $repType ==''){ ?>
		mainBtnArr[i] = new Array("search","Print N/F","top.fmain.print_records_in_new_window('unfulfilled');");
		i++;
	<?php } if((count($fulfilled) > 0 || count($unfulfilled) > 0) && $repType =='') {?>
		mainBtnArr[i] = new Array("search","Export CSV","top.fmain.export_csv();");
		i++;
	<?php  } if(count($fulfilled) > 0 && $repType =='houseCalls') {?>
		mainBtnArr[i] = new Array("search","TEXT A/F","top.fmain.saveExportTXT('full');");
		i++;
	<?php  } if(count($unfulfilled) > 0 && $repType =='houseCalls') {?>
		mainBtnArr[i] = new Array("search","TEXT N/F","top.fmain.saveExportTXT('unfull');");
		i++;
	<?php  } if(count($fulfilled) > 0 && $repType =='pam') {?>
		mainBtnArr[i] = new Array("search","TEXT A/F","top.fmain.saveExportPamTXT('full');");
		i++;
	<?php  } if(count($unfulfilled) > 0 && $repType =='pam') {?>
		mainBtnArr[i] = new Array("search","TEXT N/F","top.fmain.saveExportPamTXT('unfull');");
		i++;
	<?php  } if(count($unfulfilled) > 0 && $repType =='address_labels') {?>
		mainBtnArr[i] = new Array("search","Address Labels","top.fmain.unfill_report();");
		i++;
	<?php  } if((count($unfulfilled) > 0 || count($unfulfilled) > 0) && $repType =='recall_letters') {?>
		mainBtnArr[i] = new Array("search","Recall Letters","top.fmain.unfill_recall_report();");
	<?php  } ?>
	top.btn_show("O4A",mainBtnArr);
	
	//-------	
	function unfill_report(){
		document.frmPDF.submit();
	}
	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		
		if((document.getElementById('repType').value == 'recall_letters' || document.getElementById('repType').value == 'send_email') && (document.getElementById('recallTemplatesListId').value == ''))
		{
			top.show_loading_image("hide");
			top.fAlert("Select Recall Letter Template first!");
			return false;
		}
		document.frm_reports.submit();
	}
	
	function unfill_recall_report(){
		document.frmRecallPDF.submit();
	}
	
	function saveExportTXT(mod){
		top.show_loading_image("hide");
		if(mod =='full') {
			document.txtForm.action = 'save_house_csv.php?fn=<?php echo $full_filename;?>';
		}else {
			document.txtForm.action  = 'save_house_csv.php?fn=<?php echo $unfull_filename;?>';
		}
		window.txtForm.submit();
	}
	function saveExportPamTXT(mod){
		top.show_loading_image("hide");
		if(mod =='full') {	
			document.txtForm.action = 'save_house_csv.php?fn=<?php echo $fullPam_filename;?>';
		}else{
			document.txtForm.action = 'save_house_csv.php?fn=<?php echo $unfullPam_filename;?>';
		}
		window.txtForm.submit();
	}

	function check_box(objVal){
		//START CODE TO DISABLE SELECT LIST OF TEMPLATES,DATE RANGE(FROM-TO)
		if(objVal == 'recall_letters' || objVal == 'send_email') {
			$('#recallTemplatesListId').prop('disabled', false);
				if(document.getElementById("excSentEmail")){
					if(document.getElementById("repType").value =='send_email'){
					document.getElementById("excSentEmail").disabled=false;
					$('#excSentEmail').prop('disabled', false);
					}else
					{
						$('#excSentEmail').prop('disabled', true);	
						$('#excSentEmail').prop('checked', false);	
					
					}
				}
		}else if(objVal == 'address_labels'){
			document.getElementById("recallTemplatesListId").value='';
			$('#recallTemplatesListId').prop('disabled', true);	
		}else{
			document.getElementById("recallTemplatesListId").value='';
			$('#recallTemplatesListId').prop('disabled', true);	
			if(document.getElementById("excSentEmail")){
				$('#excSentEmail').prop('disabled', true);	
			}
		}
		$('.selectpicker').selectpicker('refresh');
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
	} 

	$(window).load(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
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