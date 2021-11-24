<?php
$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

// Report ID to display selected report
$temp_id = $_REQUEST['sch_temp_id'];

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();


$page_title = "Financial - Account Receivable";
if($temp_id){
	$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='$temp_id' and `delete_status` = 0");
	if(imw_num_rows($sql_query) > 0){
		$row = imw_fetch_assoc($sql_query);
		$dbtemp_id  = $row['id'];
		$dbtemp_name  = $row['template_name'];
		$dbtemplate_fields  = unserialize($row['template_fields']);
		$page_title = 'Financial - '.$dbtemp_name;
	}
	$filter_arr = array();
	foreach($dbtemplate_fields as $obj){
		$filter_arr[$obj['name']] = $obj['value'];
	}
}

if($dbtemp_name == "Days In A/R"){
	$_POST['DateRangeFor']='dos';
}

if($dbtemp_name == "A/R Aging Insurance"){
//GET LATEST SEARCH OF THIS USER
if (isset($saved_searched_id) && $saved_searched_id != '') {
    $saved_id = $saved_searched_id;
}
$searchOptions = '';
$srchQry = "Select id, DATE_FORMAT(saved_date, '".$date_format_SQL." %H:%i:%s') as saved_date,search_data, report, report_name, uid FROM 
reports_searches WHERE report='ar_aging_insurance_criteria' ORDER BY saved_date DESC";
$srchRs = imw_query($srchQry);
while ($srchRes = imw_fetch_assoc($srchRs)) {
	$sel = '';
	if($saved_id == $srchRes['id']) {
		$sel = 'Selected';
		$saved_id = $srchRes['id'];
		$dataParts = explode('~', $srchRes['search_data']);
		$groups = explode(',',$dataParts[0]);
		$phyId = explode(',',$dataParts[1]);
		$facility_name = explode(',',$dataParts[2]);
		$_REQUEST['Start_date'] = $dataParts[3];
		$_REQUEST['End_date'] = $dataParts[4];
		$_POST['DateRangeFor'] = $dataParts[5];
		$_POST['summary_detail'] = $dataParts[6];
		$ins_carriers = explode(',',$dataParts[7]);
		$_POST['ins_group'] = $dataParts[8];
		$_REQUEST['aging_start'] = trim($dataParts[9]);
		$_REQUEST['aging_to'] = trim($dataParts[10]);
		$_REQUEST['BalanceAmount'] = $dataParts[11];
		$task_assign_operator_id = explode(',',$dataParts[12]);
		$_REQUEST['due_start_date'] = $dataParts[13];
		$_REQUEST['due_end_date'] = $dataParts[14];
		$_POST['accNotes'] = $dataParts[15];
		$_POST['output_option'] = $dataParts[16];
		$_POST['task_status'] = $dataParts[17];
	}

	$sel2 = '';
	$sel2 = ($_POST['savedCriteria'] == $srchRes['id']) ? 'selected' : ''; //SELECTION IS BASED ON SELECTED VALUE OF DD
	$searchOptions .= '<option value="' . $srchRes['id'] . '" title="'.$GLOBALS['webroot'].'/library/images/delete_icon.png" '. $sel2 . '>' . trim($srchRes['report_name']). '</option>';

	$arrSearchName[]=$srchRes['report_name'];	
}
json_encode($arrSearchName);
}
//CSV NAME
//$dbtemp_name_CSV= strtolower(str_replace([' ','/'], ['_',''], $dbtemp_name)).".csv";
$dbtemp_name1= str_replace("/", "", $dbtemp_name);
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name1)).".csv";

if(empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
	if($dbtemp_name!="A/R Aging Patient" && $dbtemp_name!="A/R Aging Insurance"){
		$_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
	}
}

//REPORT LOGIC INFORMATION FLOATING DIV
$logicWidth = 50;
if($dbtemp_name == "Provider A/R"){
	$report_key='provider_ar';
}else if($dbtemp_name == "A/R Aging Insurance"){
	$report_key='insurance_ar_aging';
}else if($dbtemp_name == "A/R Aging Patient"){
	$report_key='patient_ar_aging';
}
$logicDiv = reportLogicInfo($report_key, 'tpl', $logicWidth);
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
<form name="ar_report_form" id="ar_report_form" method="post"  action="" autocomplete="off">
  <input type="hidden" name="form_submitted" id="form_submitted" value="1">
  <input type="hidden" name="sch_temp_id" id="sch_temp_id" value="<?php echo $temp_id;?>">
	<input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
  <div class=" container-fluid">
    <div class="anatreport">
		<div id="common_drop" style="position:absolute;bottom:0px;"></div>
		<div class="row" id="row-main">
        <div class="col-md-3" id="sidebar">
          <?php include_once 'account_receivable_left.php' ?>
        </div>
        <div class="col-md-9" id="content1">
          <div class="btn-group fltimg pointer" role="group" aria-label="Controls">
            <img class="toggle-sidebar" src="../../library/images/practflt.png" alt=""/><span class="toggle-sidebar glyphicon glyphicon-chevron-left"></span>
          </div>
          
          <div class="pd5 report-content">
            <div class="rptbox">
              <div id="html_data_div" class="row">
                <?php 
					if($_POST['form_submitted'] && $dbtemp_name == "Provider A/R") {
						include('dr_total_report_result.php');
					}elseif($_POST['form_submitted'] && $dbtemp_name == "Days In A/R") {
						include('days_in_ar_result.php');
					}elseif($_POST['form_submitted'] && $dbtemp_name == "Unworked A/R") {
						include('unworked_ar_result.php');
					}elseif($_POST['form_submitted'] && $dbtemp_name == "A/R Aging Patient") {
						include('patient_ar_aging.php');
					}elseif($_POST['form_submitted'] && $dbtemp_name == "A/R Aging Insurance") {
						include('insuarnce_ar_aging.php');
					}elseif($_POST['form_submitted']){
						include('account_receivable_result.php');
					}else {
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

<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form> 
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
</form> 
<?php
$pdfbtnCheck = '';
if ($_POST['output_option'] == 'output_pdf'){
	$pdfbtnCheck = 1;
}
$csvbtnCheck = '';
if ($_POST['output_option'] == 'output_csv'){
	$csvbtnCheck = 1;
}
?>
  
<script type="text/javascript">
	var report_name='<?php echo $dbtemp_name;?>';
	var file_location = '<?php echo $file_location; ?>';
	var pdfbtnCheck = '<?php echo $pdfbtnCheck; ?>';
	var csvbtnCheck = '<?php echo $csvbtnCheck; ?>';
	var hasData = '<?php echo $hasData; ?>';
	var op='<?php echo $op;?>';
	var output='<?php echo $_POST['output_option'];?>';
	var summary_detail='<?php echo $_POST['summary_detail'];?>';
	var arrSearchName=[];
	var arrSearchName=<?php echo json_encode($arrSearchName); ?>;
	
	var mainBtnArr = new Array();
	var btncnt=0;
	if(hasData==1){
		if (pdfbtnCheck == '1') {
			mainBtnArr[btncnt] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
			btncnt++;
		}
		if (csvbtnCheck == '1') {
			if(report_name=='Provider A/R' && summary_detail=="detail"){
				mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
			}else{
				mainBtnArr[btncnt] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
			}
		}
		
		if(output=='output_pdf'){
			generate_pdf(op);
			top.show_loading_image('hide');
		}
		if(output=='output_csv'){
			if(report_name=='Provider A/R' && summary_detail=="detail"){
				download_csv();
			}else{
				export_csv();
			}
			top.show_loading_image('hide');
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
		if(report_name == 'A/R Aging Insurance'){
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
		}
		var startRange = parseInt($('#aging_start').val());
		var endRange = parseInt($('#aging_to').val());
		
		if(startRange !== '' && endRange !== ''){
			if(endRange < startRange){ alert('Aging To should be greater.');
				top.show_loading_image('hide');
				return false;
			}
		}
		document.ar_report_form.submit();
	}
		
	$(document).ready(function () {
		if(report_name!='A/R Aging Patient' && report_name!='A/R Aging Insurance'){
			DateOptions("<?php echo $_POST['dayReport']; ?>");
		}
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
		
		$('body').on("change","input[name=summary_detail]:checked", function(){
		if($(this).val() == 'detail'){
			$('#searchcriteria .hideclass').each(function(id, elem){
				if($(elem).hasClass('hide')) $(elem).removeClass('hide');
				$(elem).find('input,select').prop('disabled', false).selectpicker('refresh');
			});
		}else{
			$('#searchcriteria .hideclass').each(function(id, elem){
				if($(elem).hasClass('hide') == false) $(elem).addClass('hide');
				$(elem).find('input,select').prop('disabled', true).selectpicker('refresh');
				});
			}
		});
		var radioChk = $('input[name=summary_detail]:checked').val();
		if(radioChk == 'detail'){
			$('#searchcriteria .hideclass').each(function(id, elem){
				if($(elem).hasClass('hide')) $(elem).removeClass('hide');
				$(elem).find('input,select').prop('disabled', false).selectpicker('refresh');
			});
		}else{
			$('#searchcriteria .hideclass').each(function(id, elem){
				if($(elem).hasClass('hide') == false) $(elem).addClass('hide');
				$(elem).find('input,select').prop('disabled', true).selectpicker('refresh');
			});
		}

		$(window).load(function(){
			set_container_height();
		});

		$(window).resize(function(){
			set_container_height();
		});
		
	});
	
	if(report_name == 'A/R Aging Insurance'){
		$("#savedCriteria").msDropdown({roundedBorder: false});
		oDropdown = $("#savedCriteria").msDropdown().data("dd");
		oDropdown.visibleRows(10);
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
		pt_win = window.open("../../interface/scheduler/search_patient_popup.php?sel_by="+findBy+"&txt_for="+name+"&btn_enter="+findBy+"&btn_sub="+name+"&call_from=physician_console","mywindow","width=800,height=500,scrollbars=yes");
		} else {
			getPatientName(name);
		}
	}
	return false;
	}
	function getPatientName(id,obj){
		$.ajax({
			type: "POST",
			url: "<?php echo $GLOBALS['webroot']; ?>/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
			dataType:'JSON',
			success: function(r){
				if(r.id){
					if(obj){
						set_xml_modal_values(r.id,r.pt_name);
					}else{
						$("#txt_patient_name").val(r.pt_name);
						$("#patientId").val(r.id);
						load_ptcomm_ptinfo(r.id);
					}
				}else{
					fAlert("Patient not exists");
					$("#txt_patient_name").val('');
					return false;
				}	
			}
		});
	}

	function physician_console(id, name) {
		$("#txt_patient_name").val(name);
		$("#patientId").val(id);
	}	
	
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
	
	var page_heading = "<?php echo $page_title; ?>";
	set_header_title(page_heading);
</script> 
</body>
</html>