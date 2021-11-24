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

File: index.php
Purpose: This file provides listing of all providers in work view.
Access Type : Direct
*/include_once("../../../config/globals.php");
include_once("../chart_globals.php");
include_once($GLOBALS['srcdir']."/classes/merge_patients_class.php");
include_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
//include_once($GLOBALS['srcdir']."/html_to_pdf/fpdi/fpdi.php");
include(dirname(__FILE__)."/PtProviders.php");

if(empty($_SESSION["patient"])){
	echo("Please choose any patient.");
	$flgStopExec = 1;
}


if(!isset($flgStopExec) || empty($flgStopExec)){

//--- GET PATIENT NAME FORMAT ------
$patient_id = $_SESSION["patient"];
$sql_qry = imw_query("select lname, fname, mname from patient_data where id = '$patient_id'");
while($row = imw_fetch_array($sql_qry)){
	$patient_name_arr = array();
	$patient_name_arr['LAST_NAME'] = $row['lname'];
	$patient_name_arr['FIRST_NAME'] = $row['fname'];
	$patient_name_arr['MIDDLE_NAME'] = $row['mname'];
	$patientName = changeNameFormat($patient_name_arr);
	$patientName .= ' - '.$patient_id;
}


//PtProvider
$oPtPro = new PtProviders($patient_id);
$content=$oPtPro->getHTML();
$library_path = $GLOBALS['webroot'].'/library';
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Medical History:: imwemr ::</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]--> 
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<script> 
			//var global_php_var = $.parseJSON('<?php echo $global_js_arr; ?>');
		</script>	
		<script src="<?php echo $library_path; ?>/js/merge_patient.js" type="text/javascript"></script>	
		<script>	
			function funcPrint(){
				//window.print();
				window.location.replace("?prnt=1");		
			}
		</script>
	</head>
<body topmargin="0" leftmargin="0">
<?php
if(isset($_GET["prnt"]) && ($_GET["prnt"] == 1)){
?>
<!--//Print section-->
<iframe name="iframPrintPrvdrs" src="indexPdf.php" width="100%" height="100%"></iframe>
<!--//Print section-->
<?php
}else{
?>
<!--//Html section-->
<div class="mainwhtbox">
	<div class="row">
		<!-- Header -->
		<div class="col-sm-12">
			<div class="row purple_bar">
				<div class="col-sm-3">
					<label>List patient providers</label>	
				</div>
				<div class="col-sm-6 text-center">
					<label><?php echo $patientName;?></label>	
				</div>
				<div class="col-sm-3 text-right">
					<input type="button" name="elem_btnPrint" id="elem_btnPrint" onClick="funcPrint();" class="btn btn-success" value="Print" />	
				</div>	
			</div>	
		</div>

		<!-- Content -->
		<div class="col-sm-12">
			<div class="row">
				<?php echo $content;?>	
			</div>
		</div>	
	</div>
</div>

<!--//Html section-->
<?php
}
?>
</body>
<script>
	$(document).ready(function(){
		//Setting popup dimensions based on the resolution
		if(typeof(window.opener.top.innerDim)=='function'){
			var innerDim = window.opener.top.innerDim();
			if(innerDim['w'] > 1600) innerDim['w'] = 1600;
			if(innerDim['h'] > 900) innerDim['h'] = 900;
			window.resizeTo(innerDim['w'],innerDim['h']);
			brows	= get_browser();
			if(brows!='ie') innerDim['h'] = innerDim['h']-35;
			var result_div_height = innerDim['h']-210;
			//$('.mainwhtbox').height(result_div_height+'px');
		}
	});
</script>
</html>

<?php
}//$flgStopExec
?>