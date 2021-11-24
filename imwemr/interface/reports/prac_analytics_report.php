<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('CLSSchedulerReports.php');


$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

// Report ID to display selected report
$temp_id = $_REQUEST['sch_temp_id'];

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}
if (empty($_REQUEST['revenue_from']) == true && empty($_REQUEST['revenue_to']) == true) {
    $_REQUEST['revenue_from'] = $_REQUEST['revenue_to'] = date($phpDateFormat);
}

$is_parent = true;
if($temp_id){
	$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='$temp_id' and `delete_status` = 0");
	if(imw_num_rows($sql_query) > 0){
		$is_parent = false;		
		$row = imw_fetch_assoc($sql_query);
		$dbtemp_id  = $row['id'];
		$dbtemp_name  = $row['template_name'];
		$dbtemplate_fields  = unserialize($row['template_fields']);
		$page_title = $dbtemp_name;
	}
	$filter_arr = array();
	foreach($dbtemplate_fields as $obj){
		$filter_arr[$obj['name']] = $obj['value'];
	}
}
if($is_parent){
	$page_title = "Practice Analytic";
	$dbtemp_name='practice_analytic';
}
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
		.pd5.report-content { position:relative; margin-left:40px; background-color: #EAEFF5; }
		.fltimg { position:absolute; }
		.fltimg span.glyphicon { position: absolute; top: 170px; left: 10px; color: #fff; }
		.reportlft .btn.btn-mkdef { padding-top: 6px; padding-bottom: 6px; }
		#content1{ background-color:#EAEFF5; }
	</style>
</head>
<body>
<form name="analytics_report_form" id="analytics_report_form" method="post"  action="" autocomplete="off">
  <!--<input name="processReport" id="processReport" autocomplete="off" value="Daily" type="hidden">
  <input type="hidden" name="SchedulerNewReport" id="SchedulerNewReport" value="">
  <input type="hidden" name="Submit" id="Submit" value="get reports">-->
  <input type="hidden" name="form_submitted" id="form_submitted" value="1">
  <input type="hidden" name="sch_temp_id" id="sch_temp_id" value="<?php echo $temp_id;?>">
	<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
  <div class=" container-fluid">
    <div class="anatreport">
	<div id="common_drop" style="position:absolute;bottom:0px;"></div>
      <div class="row" id="row-main">
        <div class="col-md-3" id="sidebar">
		  <?php 
		 	if($dbtemp_name == "Email Log"){
			   include 'email_log_left_bar.php';
			   //include 'prac_analytics_left_bar.php'; 
			 }else{
				include 'prac_analytics_left_bar.php'; 
			 }	  
		 ?>
        </div>
        <div class="col-md-9" id="content1">
          <div class="btn-group fltimg pointer" role="group" aria-label="Controls">
            <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
          </div>
          
          <div class="pd5 report-content">
            <div class="rptbox">
              <div id="html_data_div" class="row">
                <?php 
                  if($_POST['form_submitted'] && $dbtemp_name == "Heard about us") {
                    include('heard_about_us_report_print.php');
                  }elseif($_POST['form_submitted'] && $dbtemp_name == "Lost to follow") {
                    include('patients_lost_followup_report.php');
				  }elseif($_POST['form_submitted'] && $dbtemp_name == "Registered New Patient") {
					 include('new_account_report_result.php');
				  }elseif($_POST['form_submitted'] && $dbtemp_name == "Followups"){
					include('followups_report_result.php'); 	 
				  }elseif($_POST['form_submitted'] && $dbtemp_name == "Email Log"){
                    include('email_log_result.php'); 	 
                  }elseif($_POST['form_submitted']){
                    include('prac_analytics_report_result.php');
                  }else {
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
<form name="txtForm" id="txtForm" action="save_house_csv.php?fn=<?php echo $filename;?>" method="post"></form>
<form name="labelDownloadForm" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" target="_blank">
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="p" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
</form>
<form name="letterDownloadForm" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" target="_blank">
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="p" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
</form>
<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form> 
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
</form> 

<script type="text/javascript">
	var file_location = '<?php echo $file_location; ?>';
	var pdfbtnCheck = '<?php echo $filter_arr['output_pdf']; ?>';
	var printPdFBtn = '<?php echo $filter_arr['output_csv']; ?>';
	var hasData = '<?php echo $hasData; ?>';
	var repType = '<?php echo $repType;?>';
	var output='<?php echo $_POST['output_option'];?>';
	var op= '<?php echo $op;?>';
	var dbtemp_name= '<?php echo $dbtemp_name;?>';
	
	var btncnt=0;
	var mainBtnArr = new Array();
 	if(hasData==1){
		//if (pdfbtnCheck == '1') {
			mainBtnArr[btncnt] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
			btncnt++;
			if(dbtemp_name=='Email Log'){
				mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
			}else{
				mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
			}
			btncnt++;
		//}
		if(repType == 'houseCalls'){					
			mainBtnArr[btncnt] = new Array("patient_rep","Export Televox","top.fmain.saveExportTXT();");
			btncnt++;
		}
		 if(repType == 'pam' ){					
			mainBtnArr[btncnt] = new Array("patient_rep","Export PAM2000","top.fmain.saveExportTXT();");			
			btncnt++;
		}
		if(repType == 'phoneTree'){					
			mainBtnArr[btncnt] = new Array("patient_rep","Export PhoneTree","top.fmain.saveExportTXT();");			
			btncnt++;
		}
	}
	top.btn_show("PPR", mainBtnArr);
	
	function generate_pdf() {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			if(op=='')op='l';
			top.html_to_pdf(file_location, op);
			window.close();
		}
	}
	function get_sch_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		<?php if($dbtemp_name == "Lost to follow"){ ?>
		var returnVal = validDateCheck("End_date", "Future_date");
		if(returnVal == true){
			top.show_loading_image('hide');
			top.fAlert('Future date Should be Greater than End date.');	
			return false;
		}
		<?php } ?>
		document.analytics_report_form.submit();
	}
	
	function saveExportFile(){
		top.show_loading_image("hide");
		export_csv();
		document.csvDownloadForm.submit();
	}	
	
	
	function saveExportTXT(){
		window.txtForm.submit();
		top.show_loading_image("hide");
	}
	
	function label_print(){
		top.show_loading_image("hide");
		document.labelDownloadForm.submit();
	}
	
	function letter_print() {
		top.show_loading_image("hide");
		document.letterDownloadForm.submit();
	}
	
	function enableDisable(){
		if($('#include_claims').is(":checked")){
			//$("#insuranceName").prop('disabled',false);
			$("#cpt_code_id").prop('disabled',false);
			$("#all_dx10").prop('disabled',false);
		}else{
			//$("#insuranceName").prop('disabled',true);
			$("#cpt_code_id").prop('disabled',true);
			$("#all_dx10").prop('disabled',true);
		}
	}

	function display_futureDate(){
		if($('#check_sch_date').is(":checked")){
			$('#lbl_date_title_future_date').text('Appt. date after');
			DateOptions('Date');
			//$('#div_future_date').show();
			$('#div_future_date').css('visibility','visible');
		}
		else{
			DateOptions('Daily');
			//$('#div_future_date').hide();
			$('#div_future_date').css('visibility','hidden');
		}
	}
	$('#check_sch_date').on('click', function(){
		display_futureDate();
	});

	$(document).ready(function () {
		DateOptions("<?php echo $_POST['dayReport']; ?>");
		DateOptions("<?php echo $_POST['dayReport2']; ?>", $('#dayReport2'));
		'<?php if($_POST['check_sch_date']=='1'){?>';
			display_futureDate();
		'<?php } ?>'	
		$("#recall_fulfilment").click(function () {
			if($(this)[0].checked==true)
				$("#recal_month").prop('disabled',false);
			else
				$("#recal_month").prop('disabled',true);
		
			$("#recal_month").selectpicker("refresh");
		});
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
		<?php if(count($rep_proc)>0) {?>
		var proc = JSON.parse('<?php echo json_encode($rep_proc); ?>');
		if(proc.length){
			$("#rep_proc").selectpicker('val', proc).selectpicker('refresh');
		}
		<?php } ?>
		check_box($("#repType").val());
		<?php if($boolPdf==true && $showBtn) {?>
			letter_print();
		<?php } ?>

		enableDisable();

		if(dbtemp_name=='Email Log' && hasData==1){
			if(output=='output_pdf'){
				generate_pdf(op);
				top.show_loading_image('hide');
			}
			if(output=='output_csv'){
				download_csv();
				top.show_loading_image('hide');
			}
		}
	});
	
	function check_box(objVal){
		var req = '<?php echo $_POST['letterTempId']; ?>';
		if($("#letterTempId").length>0){
			if(objVal == 'letters') {
				document.getElementById("letterTempId").disabled=false;
				document.getElementById("letterTempId").value = req;
			}else{
				document.getElementById("letterTempId").value='';
				document.getElementById("letterTempId").disabled=true;
			}
			$("#letterTempId").selectpicker('refresh');
		}
	}

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
		if(hasData==1){
			$(".fltimg .toggle-sidebar").click();
		}
		
	});

	$(window).resize(function(){
		set_container_height();
	});
	var page_heading = "<?php echo $page_title; ?>";
	set_header_title(page_heading);
</script> 
</body>
</html>