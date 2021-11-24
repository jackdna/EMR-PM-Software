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
?>
<!--<link href="css_accounting.php" type="text/css" rel="stylesheet">-->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.datetimepicker.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/core.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/report.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/dd.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css" type="text/css" rel="stylesheet" >
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
	var JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/buttons.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/reports_functions.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/table2CSV.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.dd.js"></script>
<script>
	$(function(){		// Init. bootstrap tooltip
		$('[data-toggle="tooltip"]').tooltip();
		$('.selectpicker').selectpicker();
		$('.date-pick').datetimepicker({
			timepicker:false,
			format:window.opener.top.global_date_format,
			formatDate:'Y-m-d',
			scrollInput:false
		});
	});
	

	function export_csv() {
		//top.show_loading_image('hide');
		getCSVData();
		document.csvDownloadForm.submit();
	}

	function getCSVData(){
		if($("#csv_text").val()==''){
			var csv_value=$('#html_data_div').table2CSV({delivery:'value'});
			$("#csv_text").val(csv_value);
		}
	}	

	function download_csv() {
		document.getElementById("csvDirectDownloadForm").submit();	
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
	
</script>
		
