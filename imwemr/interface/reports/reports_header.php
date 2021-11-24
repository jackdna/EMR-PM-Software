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

include_once(dirname(__FILE__)."/../../config/globals.php");
//require_once(dirname(__FILE__).'/../common/functions.inc.php');
//require_once(dirname(__FILE__).'/../common/CLSCommonFunction.php');
require_once('common/report_logic_info.php');
//require_once('common/CLSReports.php');

//Returns header title depending on provided file name
function get_header_title($url){
	//Get the file details
	$replace_val = (strpos($url,$GLOBALS['fileroot']) > 0) ? $GLOBALS['fileroot'] : $GLOBALS['webroot'];
	$current_page = str_ireplace($replace_val,'',$url);
	$page_info = pathinfo($current_page);
	if(empty($page_info['filename']) === false){
		$page_name = $page_info['filename'];
	}
	
	//Return header title based on filename
	$return_val = 'Reports';
	if(empty($page_name) === false){
		switch(strtolower($page_name)){
			case 'productivity':
				$return_val = 'Practice Analytics';
			break;
		}
	}
	return $return_val;
}

$session_user_id = $_SESSION['authId'];

//For setting header title variable value
$header_title = get_header_title($_SERVER[PHP_SELF]);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<title><?php echo $title; ?></title>
<!--<link href="css_accounting.php" type="text/css" rel="stylesheet">-->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.datetimepicker.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/report.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/dd.css" type="text/css" rel="stylesheet">
<?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
<?php } ?>
<style type="text/css">
#divLightBox{
	border:solid 1px #000033;
	width:450px;
	height:60px;
	position:fixed;
	display:none;
	z-index:10;
	float:left;
	top:50%;
	left:50%;
}
#fade{
	background:#fff;
	position:fixed;left:0;top:0;
	width:100%;height:100%;
	opacity:80;
	z-index:1;
}
#content{
	position:relative;
	top:20%;left:20%;
}

/* Info Image CSS */
.rptInfoImg{
	background-image: url("../../library/images/infobutton.png");
	width:21px;
	height:21px;
	background-repeat: no-repeat;
	cursor: pointer;
	title:'Report Logic';
}


.rptInfoImg:hover { 
	-khtml-opacity:.80; 
	-moz-opacity:.80; 
	-ms-filter:"alpha(opacity=80)";
	filter:alpha(opacity=80);
	filter: progid:DXImageTransform.Microsoft.Alpha(opacity=0.8);
	opacity:.80; 
}
.infoBox{ border:2px solid #FCA635;  background-color:#F2F2F2; font-family:Verdana, Arial, Helvetica, sans-serif;
 -moz-border-radius: 8px;
 -webkit-border-radius: 8px;
 border-radius: 8px;
}
.infoTitle{ size:16px; color:#000;  font-weight:bold; padding:2px 0px 2px 5px; background-color:#E1E1E1;
 -moz-border-radius: 8px 8px 0px 0px;
 -webkit-border-radius: 8px 8px 0px 0px;
 border-radius: 8px 8px 0px 0px;
}
.infoTitleLine { border-bottom:1px solid #FCA635;}
.infoInnerDiv { padding:7px; size:12px; color:#000; line-height:18px}
.infoSubTitle { size:12px; color:#000; font-weight:bold; margin-right:10px; float:left; }
.infoDataLine { width:100%; border-bottom:1px solid #CCC; margin-top:3px; margin-bottom:3px;}
#closeRptInfo{cursor: pointer;}
</style>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
	var server_name='<?php echo strtolower($billing_global_server_name); ?>';
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/reports_functions.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/table2CSV.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.dd.js"></script>
</head>
<script>
	$(function(){		// Init. bootstrap tooltip
		$('[data-toggle="tooltip"]').tooltip();
		$('.selectpicker').selectpicker();
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:top.global_date_format,
			formatDate:'Y-m-d'
		});
	});
	
	//Sets Title of current page in header
	set_header_title('<?php echo $header_title; ?>');

	function export_csv() {
		//top.show_loading_image('hide');
		getCSVData();
		document.csvDownloadForm.submit();
	}

	function getCSVData(){
		if($("#csv_text").val()==''){
			var csv_value=$('#html_data_div').table2CSV({delivery:'value'});
			if($("#tblaudit").length>0){	csv_value = csv_value.replace(/\u2713\s*"/g, '"'); csv_value = csv_value.replace(/\u2713/g, ', ');  }			
			$("#csv_text").val(csv_value);
		}
	}	

	function download_csv() {
		document.getElementById("csvDirectDownloadForm").submit();	
	}

	function callSavedSearch(srchVal, formId){
		if(formId == 'ar_report_form'){
			$('#form_submitted').val(0);
		}
		$('#saved_searched_id').val(srchVal);
		$('#callby_saved_dropdown').val(srchVal);
		top.show_loading_image("show");
		$('#'+formId).submit();
	}


	function callAjaxFile(ddText,opIndex){
		oDropdown.off("change");
		var returnVal=0;
		var dd=confirm('Are sure to delete the selected search?');
		if(dd==true){
				$.ajax({ 
				url: "delete_search.php?sTxt="+ddText,
				success: function(callSts){
					if(callSts=='1'){
						oDropdown.close();
						oDropdown.remove(opIndex);
						oDropdown.set("selectedIndex", 0);
						return false;
					}
				}
			});		
		}else{
			return false;
		}
		return returnVal;
	}
	
	function validDateCheck(StartDate,EndDate){
		var Start_Date=Date.parse(document.getElementById(StartDate).value);
		var End_Date=Date.parse(document.getElementById(EndDate).value);
		var return_val=false;
		if(Start_Date!=''&&End_Date!=''&&Start_Date>End_Date){
			return_val=true;
		}
		return return_val;
	}

	function showHideReportInfo(e, divWidth){
		var openedWinWidth=$(window).width();
		var setWidth= (openedWinWidth*divWidth) / 100;

		$("#infoTitleText").text('Report Logic');
		$("#div_rpt_info").css('width',setWidth);
		$("#div_rpt_info").css('left', -100);
		$("#div_rpt_info").css('top', e.pageY+20);
		$("#div_rpt_info").toggle("slow");	
		$('#div_rpt_info .infoTitle').css('cursor', 'move');
		$("#div_rpt_info").draggable({ handle: ".infoTitle" });
	}		
	
</script>
<body>
<div >
		
