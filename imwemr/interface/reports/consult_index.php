<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if (empty($_REQUEST['from_date']) == true && empty($_REQUEST['to_date']) == true) {
    $_REQUEST['from_date'] = $_REQUEST['to_date'] = date($phpDateFormat);
}

//--- GET FACILITY SELECT BOX ----
$facility_id= array_combine($_REQUEST['comboFac'],$_REQUEST['comboFac']);
$facilityName = $CLSReports->getFacilityName($facility_id, '1');
$allFacCount = sizeof(explode('</option>', $facilityName)) - 1;

//--- GET PHYSICIAN NAME ---
$strPhysician = implode(',',$_REQUEST['comboProvider']);
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_cnt = sizeof(explode('</option>', $physicianName)) - 1;

//--- GET REF PHYSICIAN DETAIL FROM XML ---
$stringAllPhy = "";
$stringAllPhyId = "";
$refPhyXMLFileExits = false;
$refPhyXMLFile = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/xml/Referring_Physicians.xml";
if(file_exists($refPhyXMLFile)){
	$refPhyXMLFileExits = true;
}
else{
	$CLSCommonFunction -> create_ref_phy_xml();
	if(file_exists($refPhyXMLFile)){
		$refPhyXMLFileExits = true;	
	}	
}

$refPhyFaxArr= array();
if($refPhyXMLFileExits == true){
	$values = array();
	$XML = file_get_contents($refPhyXMLFile);
	$values = $CLSCommonFunction -> xml_to_array($XML);		
	$refPhyAll = array();
	$str_faxRefPhy = ''; $str_faxRefPhyId='';
	$str_arrRefIdNameFax = '';
	foreach($values as $key => $val){	
		if(($val["tag"] =="refPhyInfo") && ($val["type"]=="complete") && ($val["level"]=="2") ){		
			$refPhyFname = str_replace("'","\'",stripslashes($val["attributes"]["refphyFName"]));	
			$refPhyLname = str_replace("'","\'",stripslashes($val["attributes"]["refphyLName"]));
			
			$refPhyFname = str_replace('"','\"',($refPhyFname));	
			$refPhyLname = str_replace('"','\"',($refPhyLname));						
			$refPhyAll[]="'".$refPhyLname.', '.$refPhyFname."'";
			
			$refPhyIdAll[]="'".$val["attributes"]["refphyId"]."'";
			$refPhyIdFax=$val["attributes"]["refphyId"];
			$refPhyFax=$val["attributes"]["refFax"];
			$refPhyFaxArr[$refPhyIdFax."@@".$refPhyFax]=$refPhyLname.', '.$refPhyFname;
			$str_faxRefPhyId = $refPhyIdFax."@@".$refPhyFax;
			$str_faxRefPhy = $refPhyLname.', '.$refPhyFname;
			$str_arrRefIdNameFax .= 'arrRefIdNameFax["'.$refPhyIdFax.'"] = "'.$str_faxRefPhy.'@@'.$refPhyFax.'";';

		}
	}
	if(count($refPhyAll)>0){
		$stringAllPhy=implode(',',$refPhyAll);
		$stringAllPhyId=implode(',',$refPhyIdAll);
		$stringAllPhyFax=implode(',',$refPhyIdFax);
		
	}
}

$dbtemp_name = 'Consult Letters';
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
												<label>Facility</label>
                                                <select name="comboFac[]" id="comboFac" class="selectpicker" data-container="#select_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                    <?php echo $facilityName; ?>
                                                </select>
											</div>
											<div class="col-sm-6">
                                                <label>Provider</label>
												<select name="comboProvider[]" id="comboProvider" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
												<?php echo $physicianName; ?>
												</select>
                                            </div>
											<div class="col-sm-12">
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
                                                            <input type="text" name="from_date" placeholder="From" style="font-size: 12px;" id="from_date" value="<?php echo $_REQUEST['from_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="from_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                        </div>
                                                    </div>	
                                                    <div class="col-sm-5">	
                                                        <div class="input-group">
                                                            <input type="text" name="to_date" placeholder="To" style="font-size: 12px;" id="to_date" value="<?php echo $_REQUEST['to_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="to_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button type="button" class="btn" onclick="DateOptions('x');"><span class="glyphicon glyphicon-arrow-left"></span></button>
                                                    </div>
                                                </div>
											</div>
											<div class="col-sm-6">
												<div class="checkbox pointer">
													<input type="checkbox" name="cbkInculdeSentDate" id="cbkInculdeSentDate" value="1" <?php if ($_POST['cbkInculdeSentDate'] == '1') echo 'CHECKED'; ?>/>
													<label for="cbkInculdeSentDate">Include sent date</label>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
								<div class="grpara">
									<div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
                                        <button class="savesrch" type="button" onClick="top.fmain.compare_dates()">Search</button>
                                    </div>
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
											include('consult_letters_report.php');
										}else{
                                            echo '<div id="page_div" class="text-center alert alert-info">No Search Done.</div>';
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
		<?php $csvName = preg_replace('/\s+/', '_', $dbtemp_name); ?>
		<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
			<input type="hidden" name="csv_text" id="csv_text">	
			<input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $csvName; ?>.csv" />
		</form>
		<div id="counsult_letter_patient" style="width:770px; top:115px; left:350px; position:absolute; overflow:hidden; display:none;"></div>
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
		
	});

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	
	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	

	function compare_dates(){				
		if(document.getElementById("from_date").value == ""){
			fAlert("Please enter from date.");
			return false;
		}	
		else if(document.getElementById("to_date").value == ""){
			fAlert("Please enter to date.");
			return false;
		}		
		getConsultOtherProvider(document.getElementById("from_date").value, document.getElementById("to_date").value);
	}
	
	
	function getConsultOtherProvider(fromDate, toDate){
	var fromDate = fromDate;
	var toDate = toDate;	
	var url="get_counsult_other_provider.php?fromDate="+fromDate+"&toDate="+toDate;
	$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				if(resp){
					top.show_loading_image("none");
					document.getElementById("counsult_letter_patient").style.display = "block";		
					document.getElementById("counsult_letter_patient").innerHTML = resp;
				}else{
					document.getElementById("counsult_letter_patient").innerHTML = "";
					document.getElementById("counsult_letter_patient").style.display = "none";		
					var fromDate = document.getElementById("from_date").value;
					var toDate = document.getElementById("to_date").value;
					var intIncSentDate = 0;
					if(document.getElementById("cbkInculdeSentDate").checked == true){
						intIncSentDate = 1;
					}
					getConsultPatient(fromDate, toDate, intIncSentDate);
				}
			}
		});
	}
	
	function getConsultPatient(fromDate, toDate, intIncSentDate){
		var fromDate = fromDate;
		var toDate = toDate;		
		var intIncSentDate = intIncSentDate;
		var selectedFac = "";
		var selectedProvider = "";
		var sel_fac_id_arr = new Array;
		$('#comboFac option').each(function(id,elem){
		if($(elem).is(':selected')){
			var value = $(elem).val();
				sel_fac_id_arr.push(value);
			}
		});
		if(sel_fac_id_arr.length > 0) {
			selectedFac = sel_fac_id_arr.join(','); 
		}
		
		var sel_pro_id_arr = new Array;
		$('#comboProvider option:selected').each(function(id,elem){
		var value = $(elem).val();
			sel_pro_id_arr.push(value);
		});
		if(sel_pro_id_arr.length > 0) {
			selectedProvider = sel_pro_id_arr.join(','); 
		}
		var url = 'get_counsult_letter_patient.php?fromDate='+fromDate+'&toDate='+toDate+'&intIncSentDate='+intIncSentDate+'&selectedFac='+selectedFac+'&selectedProvider='+selectedProvider;
		$.ajax({
			type: "POST",
			url: url,
			success: function(resp){
				if(resp){
					top.show_loading_image("none");
					document.getElementById("counsult_letter_patient").style.display = "block";		
					document.getElementById("counsult_letter_patient").innerHTML = resp;
					document.getElementById("page_div").innerHTML = "Result populate in popup.";
				} else{
					document.getElementById("counsult_letter_patient").innerHTML = "";
					document.getElementById("counsult_letter_patient").style.display = "none";		
					top.show_loading_image("none");
					fAlert("Sorry no record(s) found!");
				}
			}
		});
	}
	
	function closeCousultDiv(){
		$("#counsult_letter_patient").hide();
		document.getElementById("page_div").innerHTML = "Please search.";
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
	});
	
	function closeOthProDiv(){
		var fromDate; 
		if(document.getElementById("from_date").value == ""){
			alert("Please enter date.");
			return false;
		}				
		else{
			var fromDate = document.getElementById("from_date").value;
			var toDate = document.getElementById("to_date").value;
			var intIncSentDate = 0;
			if(document.getElementById("cbkInculdeSentDate").checked == true){
				intIncSentDate = 1;
			}
			getConsultPatient(fromDate, toDate, intIncSentDate);
		}				
		//document.getElementById("counsult_letter_patient").style.display = "none";
	}
	function registerOtherprovider(){		
		var otherProviderVal = "";
		var field = document.getElementsByName("cbkOthPro");		
		if(document.getElementById("otherProvider").value == ""){
			for (var i = 0; i < field.length; i++){
				if(field[i].checked == true){								
					otherProviderVal += field[i].value+"~";
				}	
			}	
		}

		if(otherProviderVal != ""){			
			var strLen = otherProviderVal.length; 
			otherProviderVal = otherProviderVal.slice(0,strLen-1); 			
			document.getElementById("otherProvider").value = otherProviderVal;			
			//alert(document.getElementById("otherProvider").value);					
			document.frmRegOtherProvider.submit();
			var fromDate = document.getElementById("from_date").value;
			var toDate = document.getElementById("to_date").value;
			var intIncSentDate = 0;
			if(document.getElementById("cbkInculdeSentDate").checked == true){
				intIncSentDate = 1;
			}
			getConsultPatient(fromDate, toDate, intIncSentDate);
		}
		else if(document.getElementById("otherProvider").value != ""){	
			otherProviderVal =	document.getElementById("otherProvider").value;
			var strLen = otherProviderVal.length; 
			if(otherProviderVal.substring(strLen-1,strLen) == "~"){
				otherProviderVal = otherProviderVal.slice(0,strLen-1); 			
			}
			document.getElementById("otherProvider").value = otherProviderVal;			
			//alert(document.getElementById("otherProvider").value);					
			document.frmRegOtherProvider.submit();
			fromDate = document.getElementById("from_date").value;
			var toDate = document.getElementById("to_date").value;
			var intIncSentDate = 0;
			if(document.getElementById("cbkInculdeSentDate").checked == true){
				intIncSentDate = 1;
			}
			getConsultPatient(fromDate, toDate, intIncSentDate);
		}
		else{
			fAlert("Please select provider(s) to register them.");
		}
	}	
	function sendMultipleConsultLetterFax(){
		
		var hidPatientVal = hidConsultLeterId = "";
		var field = document.getElementsByName("cbk");		
		if(document.getElementById("patients").value == ""){
			for (var i = 0; i < field.length; i++){
				if(field[i].checked == true){
					var arr = field[i].value.split("-");					
					var patid = arr[0];
					var consultLeterId = arr[1];
					hidPatientVal += patid+",";
					hidConsultLeterId += consultLeterId+",";
				}	
			}	
		}
		var SendFaxValue="";
		SendFaxObj=document.getElementById('send_fax_number');
		string=SendFaxObj.value;
		TrimStringValue=string.replace(/^\s+|\s+$/g,"");
		SendFaxObj.value=TrimStringValue;
		if(SendFaxObj.value!=""){
			if(document.getElementById("div_load_image")) {
				document.getElementById("div_load_image").style.display="inline-block";
			}
		}	
		if(hidPatientVal != ""){
			if(SendFaxObj.value!=""){	
				var strLen = hidPatientVal.length; 
				hidPatientVal = hidPatientVal.slice(0,strLen-1); 	
			
				var strLenConLt = hidConsultLeterId.length; 			
				hidConsultLeterId = hidConsultLeterId.slice(0,strLenConLt-1);
				
				document.getElementById("patients").value = hidPatientVal;
				document.getElementById("hidConsultLeterId").value = hidConsultLeterId;			
				//alert(document.getElementById('hiddFrameForSendFax'));
				document.frmConsultLetterDiV.target='hiddFrameForSendFax';
				//alert(document.frmConsultLetterDiV);
				document.getElementById("sendFaxCase").value="1";
				document.frmConsultLetterDiV.submit();
			}else{fAlert("please enter the fax number");SendFaxObj.focus();return false;}	
		}
		else if(document.getElementById("patients").value != ""){	
			if(SendFaxObj.value!=""){	
				hidPatientVal =	document.getElementById("patients").value;
				var strLen = hidPatientVal.length; 
				if(hidPatientVal.substring(strLen-1,strLen) == ","){
					hidPatientVal = hidPatientVal.slice(0,strLen-1); 			
				}
				
				hidConsultLeterId =	document.getElementById("hidConsultLeterId").value;
				var strLenConLt = hidConsultLeterId.length; 
				if(hidConsultLeterId.substring(strLenConLt-1,strLenConLt) == ","){
					hidConsultLeterId = hidConsultLeterId.slice(0,strLenConLt-1); 			
				}
				document.getElementById("patients").value = hidPatientVal;			
				document.getElementById("hidConsultLeterId").value = hidConsultLeterId;			
				
				document.frmConsultLetterDiV.target='hiddFrameForSendFax';
				document.getElementById("sendFaxCase").value="1";
				document.frmConsultLetterDiV.submit();
			}else{fAlert("please enter the fax number");SendFaxObj.focus();return false;}		
		}
		else{
			fAlert("Please select patient(s) to get their report.");
		}
	}
	
	function set_fax_format(obj, format){
	
	fax_min_length = top.phone_min_length;
	default_format = format || top.phone_format;
	phone_reg_exp_js = "[^0-9+]";
	
	regExp = new RegExp(phone_reg_exp_js,'g');
	var refinedPh = obj.value;
	refinedPh = refinedPh.trim();
	if(refinedPh==''){
		return;
	}
	refinedPh = refinedPh.replace(regExp,"");
	if(refinedPh.length < fax_min_length){
		invalid_input_msg(obj, "Please Enter a valid Fax number");return;
	}else{
			switch(default_format){
				case "###-###-####":
					obj.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6);
				break;
				case "(###) ###-####":
					obj.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6);
				break;
				case "(##) ###-####":
					obj.value = "("+refinedPh.substring(0,2)+") "+refinedPh.substring(2,5)+"-"+refinedPh.substring(5);
				break;
				case "(###) ###-###":
					obj.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6);
				break;
				case "(####) ######":
					obj.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4);
				break;
				case "(####) #####":
					obj.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4);
				break;
				case "(#####) #####":
					obj.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5);
				break;
				case "(#####) ####":
					obj.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5);
				break;
				default:
					obj.value = refinedPh;
				break;
			}
	}
	//changeClass(obj);
}

function setFaxNumber(faxObj,Cc1Obj,Cc2Obj,Cc3Obj){
	var faxName = faxNo = ccName = ccFax = "";
	if(faxObj.value!=0 && faxObj.value!='') {
		var faxDetail = arrRefIdNameFax[faxObj.value];
		var faxDetailArr = new Array();
		if(faxDetail!="" && faxDetail!="-" && faxDetail!="undefined"){
			faxDetailArr= faxDetail.split("@@");
			faxName 	= faxDetailArr[0];
			faxNo 		= faxDetailArr[1];
			document.getElementById("send_fax_number").value=faxNo;	
		}
	}
	for(var i=1;i<=3;i++) {
		if(i==1) 		{ CcObj = Cc1Obj;
		}else if(i==2) 	{ CcObj = Cc2Obj;
		}else if(i==3) 	{ CcObj = Cc3Obj;
		}
		if(CcObj.value!=0 && CcObj.value!='') {
			var ccDetail = arrRefIdNameFax[CcObj.value];
			var ccDetailArr = new Array();
			if(ccDetail!="" && ccDetail!="-" && ccDetail!="undefined"){
				ccDetailArr = ccDetail.split("@@");
				ccName 		= ccDetailArr[0];
				ccFax 		= ccDetailArr[1];
				document.getElementById("send_fax_numberCc"+i).value=ccFax;	
			}
		}
	}
		
}

function getFax_Num(){
	//timer_refPhyFax = setTimeout("return setFaxNumber(document.getElementById('hiddselectReferringPhy'))",1000);
	timer_refPhyFax = setTimeout("return setFaxNumber(document.getElementById('hiddselectReferringPhy'),document.getElementById('hiddselectReferringPhyCc1'),document.getElementById('hiddselectReferringPhyCc2'),document.getElementById('hiddselectReferringPhyCc3'))",1);
}
//== End Function ===//	

function faxChbxClick(objChBx,RefId,Cc1RefId,Cc2RefId,Cc3RefId,preffered_fax) {
	var refName = refFax = ccName = ccFax = "";
	document.getElementById("selectReferringPhy").value="";
	document.getElementById("send_fax_number").value="";
	if(RefId!=0 && objChBx.checked==true) {
		var refDetail = arrRefIdNameFax[RefId];
		var refDetailArr = new Array();
		if(refDetail!="" && refDetail!="-" && refDetail!="undefined"){
			refDetailArr= refDetail.split("@@");
			refName 	= refDetailArr[0];
			refFax 		= refDetailArr[1];
			document.getElementById("selectReferringPhy").value=refName;
			document.getElementById("send_fax_number").value=refFax;	
		}
	}
	
	for(var i=1;i<=3;i++) {
		if(i==1) 		{ CcRefId = Cc1RefId;
		}else if(i==2) 	{ CcRefId = Cc2RefId;
		}else if(i==3) 	{ CcRefId = Cc3RefId;
		}
		
		document.getElementById("selectReferringPhyCc"+i).value="";
		document.getElementById("send_fax_numberCc"+i).value="";	
		
		if(CcRefId!=0 && objChBx.checked==true) {
			var ccDetail = arrRefIdNameFax[CcRefId];
			var ccDetailArr = new Array();
			if(ccDetail!="" && ccDetail!="-" && ccDetail!="undefined"){
				ccDetailArr = ccDetail.split("@@");
				ccName 		= ccDetailArr[0];
				ccFax 		= ccDetailArr[1];
				document.getElementById("selectReferringPhyCc"+i).value=ccName;
				document.getElementById("send_fax_numberCc"+i).value=ccFax;	
				if(document.getElementById("selectReferringPhy").value=="" && i==1){
					document.getElementById("selectReferringPhy").value=ccName;
					document.getElementById("send_fax_number").value=ccFax;
					document.getElementById("selectReferringPhyCc"+i).value="";
					document.getElementById("send_fax_numberCc"+i).value="";
				}
			}
		}
	}
	if(preffered_fax){
		reff_preff=preffered_fax.split("~||~");
		document.getElementById("selectReferringPhy").value=reff_preff[0];
		document.getElementById("send_fax_number").value=reff_preff[1];
	}	
}

function selDeSelAllChkBox(op){	
	if(document.getElementById("cbkSelectAll").checked == false){
		if(op == "sel"){
			op = "deSel";
		}
	}		
	var field = document.getElementsByName("cbk");
	var hidPatientVal = hidConsultLeterId = "";
	if(op == "sel"){
	//	document.getElementById("cbkDeSelectAll").checked = false;
		for (i = 0; i < field.length; i++){
			field[i].checked = true;
			var arr = field[i].value.split("-");					
			var patid = arr[0];
			var consultLeterId = arr[1];
			hidPatientVal += patid+",";
			hidConsultLeterId += consultLeterId+",";
			//hidPatientVal += field[i].value+",";				
		}
		var strLen = hidPatientVal.length; 
		hidPatientVal = hidPatientVal.slice(0,strLen-1); 	
		
		var strLenConLt = hidConsultLeterId.length; 			
		hidConsultLeterId = hidConsultLeterId.slice(0,strLenConLt-1);
		
		document.getElementById("patients").value = hidPatientVal;				
		document.getElementById("hidConsultLeterId").value = hidConsultLeterId;				
	}
	else if(op == "deSel"){
		document.getElementById("cbkSelectAll").style.visibility
		document.getElementById("cbkSelectAll").checked = false;
		for (i = 0; i < field.length; i++){
			if(field[i].style.display == "inline"){ 
				field[i].checked = false;
			}
		}	
		document.getElementById("patients").value = "";				
		document.getElementById("hidConsultLeterId").value = "";
	}
}
	
	function getConsultReport(){
	if(document.getElementById("sendFaxCase")){
		document.getElementById("sendFaxCase").value="";
	}
	var hidPatientVal = hidConsultLeterId = "";
	var field = document.getElementsByName("cbk");		
	if(document.getElementById("patients").value == ""){
		for (var i = 0; i < field.length; i++){
			if(field[i].checked == true){
				var arr = field[i].value.split("-");					
				var patid = arr[0];
				var consultLeterId = arr[1];
				hidPatientVal += patid+",";
				hidConsultLeterId += consultLeterId+",";
			}	
		}	
	}
	
	if(hidPatientVal != ""){			
		var strLen = hidPatientVal.length; 
		hidPatientVal = hidPatientVal.slice(0,strLen-1); 	
		
		var strLenConLt = hidConsultLeterId.length; 			
		hidConsultLeterId = hidConsultLeterId.slice(0,strLenConLt-1);
		
		document.getElementById("patients").value = hidPatientVal;
		document.getElementById("hidConsultLeterId").value = hidConsultLeterId;			
		
		document.frmConsultLetterDiV.submit();
	}
	else if(document.getElementById("patients").value != ""){	
		hidPatientVal =	document.getElementById("patients").value;
		var strLen = hidPatientVal.length; 
		if(hidPatientVal.substring(strLen-1,strLen) == ","){
			hidPatientVal = hidPatientVal.slice(0,strLen-1); 			
		}
		
		hidConsultLeterId =	document.getElementById("hidConsultLeterId").value;
		var strLenConLt = hidConsultLeterId.length; 
		if(hidConsultLeterId.substring(strLenConLt-1,strLenConLt) == ","){
			hidConsultLeterId = hidConsultLeterId.slice(0,strLenConLt-1); 			
		}
		document.getElementById("patients").value = hidPatientVal;			
		document.getElementById("hidConsultLeterId").value = hidConsultLeterId;			
		
		document.frmConsultLetterDiV.submit();
	}
	else{
		fAlert("Please select patient(s) to get their report.");
	}
}
</script>
</body>
</html>