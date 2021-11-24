<?php 
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('common/report_logic_info.php');
require_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if (empty($_REQUEST['eff_date']) == true) {
    $_REQUEST['eff_date'] = date($phpDateFormat);
}

//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['rep_fac'],$_REQUEST['rep_fac']);
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = $fac_res['name'];
	if($selArrFacility[$fac_id])
	$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}
$fac_cnt=sizeof($fac_id_arr);

//--- GET ALL PHYSICIAN DETAILS ----
$physicianName = $CLSCommonFunction->drop_down_providers(implode(',',$providerID),'1','1');
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

$arrchrtNote = array('Chart Notes','Surgeries Procedure','Medication List');

//CONSENT PACKAGE
$consQry = imw_query("SELECT consent_form_id,consent_form_name,cat_id FROM consent_form ORDER BY consent_form_id");
$consRes = array();
while($res = imw_fetch_assoc($consQry)) {
	$consRes[]=$res;
}
for($x_cnt=0;$x_cnt<count($consRes);$x_cnt++){
	$consFormId = $consRes[$x_cnt]['consent_form_id'];
	$consFormName = stripslashes($consRes[$x_cnt]['consent_form_name']);
	$consFormArr[$consFormId] = $consFormName;
}

$qryPackage = imw_query("SELECT package_category_id, package_category_name, package_consent_form FROM consent_package WHERE delete_status!='yes' AND package_consent_form!='' ORDER BY package_category_name");
$resPackage = array();
while($resRow = imw_fetch_assoc($qryPackage)) {
	$resPackage[]=$resRow;
}
$consentPackageOption = "";
for($z_pack=0;$z_pack<count($resPackage);$z_pack++){
	$sel = "";
	$packageCategoryId = $resPackage[$z_pack]['package_category_id'];
	$packageName = $resPackage[$z_pack]['package_category_name'];
	$packageConsentForm = trim($resPackage[$z_pack]['package_consent_form']);
	$packageConsentFormArr = array();
	$sel = (in_array($packageCategoryId, $_REQUEST['consent_package_template'])) ? 'selected' : '';
	$consentPackageOption .= '<option value="'.$packageCategoryId.'" '.$sel.'>'.$packageName.'</option>';
}

//CSV NAME
$dbtemp_name = 'Surgery Appointment';
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
<div class=" container-fluid">
	<div class="anatreport">
		<div id="select_drop" style="position:absolute;bottom:0px;"></div>
		<div class="row" id="row-main">
			<div class="col-md-3" id="sidebar">
				<form name="frm_reports" id="frm_reports" action="" method="post">
				<input type="hidden" name="form_submitted" id="form_submitted" value="1">
				<input type="hidden" value="" name="hidd_selected_patient_id" id="hidd_selected_patient_id">
				<input type="hidden" value="" name="package_print_page" id="package_print_page">
				<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
				<div class="reportlft" style="height:100%;">
					<div class="practbox">
						<div class="anatreport"><h2>Practice Filter</h2></div>
						<div class="clearfix"></div>
						<div class="pd5" id="searchcriteria">
							<div class="row">
								<div class="col-sm-6">
									<label>Facility</label>
									<select name="rep_fac[]" id="rep_fac" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
									<?php echo $facilityName; ?>
									</select>
								</div>
								<div class="col-sm-6">
									<label>Provider</label>
									<select name="providerID[]" id="providerID" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
									<?php echo $physicianName; ?>
									</select>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6">
									<label for="eff_date">Date</label>
									<div class="input-group">
										<input type="text" name="eff_date" placeholder="From" style="font-size: 12px;" id="eff_date" value="<?php echo $_REQUEST['eff_date']; ?>" class="form-control date-pick">
										<label class="input-group-addon" for="eff_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
									</div>
								</div>
								<div class="col-sm-6"><br />
									<div class="checkbox checkbox-inline pointer">
										 <input style="cursor:pointer" type="checkbox" value="1" name="include_pat_Add" id="include_pat_Add"  <?php if ($_POST['include_pat_Add'] == '1') echo 'CHECKED'; ?>>
										<label for="include_pat_Add"> Include Patient Address</label>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6"><br />
									<div class="checkbox checkbox-inline pointer">
										 <input style="cursor:pointer" type="checkbox" value="surgical_package_id" name="package_notes_option" id="surgical_package_id" onClick="checkSingle('surgical_package_id','package_notes_option'); showHidFacPhy();showHidConsentPackage();" <?php if ($_POST['package_notes_option'] == 'surgical_package_id') echo 'CHECKED'; ?>>
										<label for="surgical_package_id">  Surgical&nbsp;Package</label>
									</div>
								</div>
								<div class="col-sm-6"><br />
									<div class="checkbox checkbox-inline pointer">
										 <input style="cursor:pointer" type="checkbox" value="visit_notes_id" name="package_notes_option" id="visit_notes_id" onClick="checkSingle('visit_notes_id','package_notes_option');" <?php if ($_POST['package_notes_option'] == 'visit_notes_id') echo 'CHECKED'; ?>>
										<label for="visit_notes_id">  Visit&nbsp;Notes</label>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6"><br />
									<div class="checkbox checkbox-inline pointer">
										<input style="cursor:pointer" type="checkbox" value="consent_package_id" name="package_notes_option" id="consent_package_id" onClick="checkSingle('consent_package_id','package_notes_option'); showHidFacPhy();showHidConsentPackage();" <?php if ($_POST['package_notes_option'] == 'consent_package_id') echo 'CHECKED'; ?>>
										<label for="consent_package_id">   Consent&nbsp;Package</label>
									</div>
								</div>
								<div class="col-sm-6">
									<label>Select&nbsp;Template</label>
									<select name="consent_package_template[]" id="consent_package_template" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
									<?php echo $consentPackageOption; ?>
									</select>
								</div>
								<div class="clearfix"></div>
								<div class="col-sm-6"><br />
									<div class="checkbox checkbox-inline pointer">
										 <input style="cursor:pointer" type="checkbox" value="1" name="chk_latest_chart" id="chk_latest_chart" <?php if ($_POST['chk_latest_chart'] == '1') echo 'CHECKED'; ?> disabled>
										<label for="chk_latest_chart"> Latest Chart</label>
									</div>
								</div>
								<div class="col-sm-6"><br />
									<div class="checkbox checkbox-inline pointer">
										<input style="cursor:pointer" type="checkbox" value="1" name="chk_iol" id="chk_iol" <?php if ($_POST['chk_iol'] == '1') echo 'CHECKED'; ?> disabled>
										<label for="chk_iol">IOL</label>
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
			<?php foreach($arrchrtNote as $key => $val){ ?><input type="hidden" name="chart_nopro[]" value="<?php echo $val; ?>"><?php } ?>
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
								include('surgery_report_result.php');
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
<?php if($sug_app_fac==1){ ?>
<form name="printFrmALLPDF" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" target="_blank">
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="p" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
	<input type="hidden" name="images" value="<?php echo $queryStaringString; ?>" >
</form>	
<?php } ?>
<?php if($con_pac_print==1 && $_REQUEST['package_print_page'] == 1){ ?>
<form name="printFrmALLPDF" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" target="_blank">
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="L" >
	<input type="hidden" name="font_size" value="9.5" >
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
	<input type="hidden" name="images" value="<?php echo $queryStaringString; ?>" >
</form>
<?php } ?>

<?php if($visit_plan==1){ ?>
<form name="printFrmALLPDF" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" target="_blank">
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="op" value="P" >
	<input type="hidden" name="font_size" value="7.5">
	<?php if($hidexport_report){ ?>
	<input type="hidden" name="saveOption" value="F">
	<input type="hidden" name="encPassword" value="<?php echo $encPassword; ?>" >
	<input type="hidden" name="images" value="<?php echo $ChartNoteImagesStringFinal; ?>" >
	<?php } ?>
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
</form>
<?php } ?>
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form> 
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
		showHidConsentPackage();
	});
	
	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var visit_plan = '<?php echo $visit_plan; ?>';
	var con_pac_print = '<?php echo $con_pac_print; ?>';
	var sug_app_fac = '<?php echo $sug_app_fac; ?>';
	
	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, 'l');
			window.close();
		}
	}
	
	function get_report() {
		if(document.frm_reports.hidd_selected_patient_id) {
			document.frm_reports.hidd_selected_patient_id.value = '';	
		}
		document.frm_reports.submit();
	}
	
	function package_consent_form() {
		var obj = document.getElementsByName("patient_id_arr[]");
		var objLength = document.getElementsByName("patient_id_arr[]").length;
		var objVal = "";
		var q=0
		for(var i=0; i<objLength; i++){
			if(obj[i].checked == true){
				var boxesChk = true;
				if(q==0) {
					objVal = obj[i].value;		
				}else {
					objVal += ","+obj[i].value;
				}
				q++;
			}
		}
		
		if(objVal) {
			if(document.frm_reports.hidd_selected_patient_id) {
				document.frm_reports.hidd_selected_patient_id.value = objVal;
				document.frm_reports.package_notes_option.value = 'consent_package_id';
				document.frm_reports.package_print_page.value = '1';
				document.frm_reports.submit();
			}
		}else {
			top.show_loading_image("hide");
			alert("Please select patient(s) to print consent package");	
		}
		
	}
	//BUTTONS
	var mainBtnArr = new Array();
	var btncnt=0;
	if (printFile == '1') {
		mainBtnArr[btncnt] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		btncnt++;
		mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
		btncnt++;
	}
	if (con_pac_print == '1') {
		mainBtnArr[btncnt] = new Array("patient_rep", "Print Package", "top.fmain.package_consent_form();");
		btncnt++;
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	

	function checkSingle(elemId,grpName)
	{
		var obgrp = document.getElementsByName(grpName);
		var objele = document.getElementById(elemId);
		var len = obgrp.length;		
		if(objele.checked == true)
		{		
			for(var i=0;i<len;i++)
			{
				if((obgrp[i].id != objele.id) && (obgrp[i].checked == true) )
				{
					obgrp[i].checked = false;
				}
			}	
		}
	}
	function showHidFacPhy() {
		document.getElementById('rep_fac').disabled=false;		
		document.getElementById('providerID').disabled=false;		
		if(document.getElementById('visit_notes_id')) {
			if(document.getElementById('visit_notes_id').checked==true) {
				document.getElementById('rep_fac').disabled=true;		
				document.getElementById('providerID').disabled=true;		
			}
		}
	}
	
	function showHidConsentPackage() {
		if(document.getElementById('consent_package_id')) {
			document.getElementById('consent_package_template').disabled=true;
			document.getElementById('chk_latest_chart').disabled=true;
			document.getElementById('chk_iol').disabled=true;

			if(document.getElementById('consent_package_id').checked==true) {
				document.getElementById('consent_package_template').disabled=false;
				document.getElementById('consent_package_template').disabled=false;
				document.getElementById('chk_latest_chart').disabled=false;
				document.getElementById('chk_iol').disabled=false;
			}
		}
	}
	
	function checkAllChkBoxConsent($_this){
		var cObj = $(".chk_box_package");
		if($_this.checked == true){
			cObj.attr('checked','checked');
		}else{
			cObj.removeAttr('checked');
		}
	}
	
	
	var obj = document.getElementsByName("patient_id_arr[]");
	var objLength = document.getElementsByName("patient_id_arr[]").length;
	var objVal = "";
	var q=0
	var hiddPtIdArr = new Array();
	var hiddSelPtId = document.frm_reports.hidd_selected_patient_id.value;
	if(hiddSelPtId) {
		var hiddSelPtIdArr = hiddSelPtId.split(",");
		for(var i=0; i<objLength; i++){
			objVal = obj[i].value;
			if(hiddSelPtIdArr) {
				for(var j=0; j<hiddSelPtIdArr.length; j++){
					if(hiddSelPtIdArr[j]==objVal) {
						obj[i].checked = true;	
					}
				}
			}
		}
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

	$(document).ready(function (e) {
		function set_container_height(){
		$('.reportlft').css({
			'height':(window.innerHeight),
			'max-height':(window.innerHeight),
			'overflow-x':'hidden',
			'overflowY':'auto'
		});
		<?php if(isset($file_location) && ($file_location != "") && $_REQUEST['package_print_page'] == 1){ ?>
			document.printFrmALLPDF.submit();
		<?php } ?>
		<?php if(isset($file_location) && ($file_location != "") && ($visit_plan == 1 || $sug_app_fac == 1)){ ?>
			document.printFrmALLPDF.submit();
		<?php } ?>
	} 

	$(window).load(function(){
		set_container_height();
		$('#csvFileDataTable').height($('.reportlft').height()-70);
	});
		var page_heading = "<?php echo $dbtemp_name; ?>";
		set_header_title(page_heading);
	});

</script>
</body>
</html>