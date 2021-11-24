<?php


?>
<?php
require_once("../../config/globals.php");

//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

$library_path = $GLOBALS['webroot'].'/library';

$current_patient = $_SESSION['patient'];
$current_provider = $_SESSION['authId'];
$sx_planning_sheet_id = $_GET['sx_planning_sheet_id'];

$getPatientName = "SELECT id,fname, lname, mname FROM patient_data WHERE id = '$current_patient' ";
$rsGetPatientName = imw_query($getPatientName);
if(imw_num_rows($rsGetPatientName)>0) {					
	$rowGetPatientName = imw_fetch_array($rsGetPatientName);
	$ptId = $rowGetPatientName['id'];
	$ptFName = $rowGetPatientName['fname'];
	$ptLName = $rowGetPatientName['lname'];
	$ptMName = $rowGetPatientName['mname'];		
}	
($rsGetPatientName) ? imw_free_result($rsGetPatientName) : "";		   
$pt_name_arr = array($ptId,$ptFName,$ptLName,$ptMName);

//GET TORIC URL
$toric_url='';
if($_REQUEST['id'])
$qry="Select url FROM sps_admin_lens_calc WHERE id=$_REQUEST[id]";
else
$qry="Select url FROM sps_admin_lens_calc WHERE lens_calc LIKE '%toric%'";

$rs=imw_query($qry);
if(imw_num_rows($rs)>0){
	$res=$res=imw_fetch_assoc($rs);
	$toric_url=$res['url'];
}elseif(!$_REQUEST['id']){
	$qry="Select url FROM sps_admin_lens_calc WHERE lens_calc LIKE '%toric%'";
	$rs=imw_query($qry);
	$res=$res=imw_fetch_assoc($rs);
	$toric_url=$res['url'];
}

$sendResponseTo= $GLOBALS['php_server'].'/addons/toric/upload.php';
?>
<!DOCTYPE html> 
<html>
<head>
	<link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/apkdhnkhljpadcbmbkebfpnbmoehhhaa">
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	
	
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Toric Calculator</title>
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
	 <script type="text/javascript" src="toric.js?<?php echo filemtime('toric.js');?>"></script>
	<!--
	<title>imwemr</title>
	<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/apkdhnkhljpadcbmbkebfpnbmoehhhaa">
	<link rel="stylesheet" type="text/css" href="../../interface/themes/default/css_core.php">
    <link rel="stylesheet" type="text/css" href="../../interface/chart_notes/test_manager/colorbox.css">
    
    
    <script type="text/javascript" src="../../js/jquery-1.9.0.min.js?<?php echo filemtime('../../js/jquery-1.9.0.min.js');?>"></script>
    <script type="text/javascript" src="../../js/common.js?<?php echo filemtime('../../js/common.js');?>"></script>
    <script type="text/javascript" src="../../library/messi/messi.js?<?php echo filemtime('../../library/messi/messi.js');?>"></script>
   
   	<script type="text/javascript" src="../../interface/admin/menuIncludes_menu/js/disableKeyBackspace.js"></script>
    <script type="text/javascript" src="../../interface/chart_notes/test_manager/jquery.colorbox-min.js?<?php echo filemtime('../../interface/chart_notes/test_manager/jquery.colorbox-min.js');?>"></script> -->
    <script type="text/javascript">
	var toric_open_log_id = 0;
	var sx_planning_sheet_id = '<?php echo $sx_planning_sheet_id;?>';
	
	$(document).ready(function(e) {
		if(typeof(window.opener.top.innerDim)=='function'){
			var innerDim = window.opener.top.innerDim();
			if(innerDim['w'] > 1600) innerDim['w'] = 1600;
			if(innerDim['h'] > 900) innerDim['h'] = 900;
			window.resizeTo(innerDim['w'],innerDim['h']);
			brows	= get_browser();
			if(brows!='ie') innerDim['h'] = innerDim['h']-35;
			var result_div_height = innerDim['h']-210;
			$('.mainwhtbox,#toric_right_block,#iframe_toric').height(result_div_height+'px');
			$('#iframe_toric').css('height',result_div_height+'px');
			
		}
		
		$('#iframe_toric').load(function(){
			logOpenToric();
		});
	});
	
	$(window).resize(function() {
			if(typeof(window.opener.top.innerDim)=='function'){
				var innerDim = window.opener.top.innerDim();
				if(innerDim['w'] > 1600) innerDim['w'] = 1600;
				if(innerDim['h'] > 900) innerDim['h'] = 900;
				window.resizeTo(innerDim['w'],innerDim['h']);
				brows	= get_browser();
				if(brows!='ie') innerDim['h'] = innerDim['h']-35;
				var result_div_height = innerDim['h']-210;
				$('.mainwhtbox,#toric_right_block,#iframe_toric').height(result_div_height+'px');
				$('#iframe_toric').css('height',result_div_height+'px');
			}
		});
	</script>
    <style type="text/css">
		.div_thumb{border:2px solid #999; margin:5px 0px 15px;}
	</style>
</head>
<body>
	<div class="mainwhtbox">
		<div class="row">
			<div class="col-sm-10 text-center">
			<?php
			if($toric_url=='https://www.apacrs.org/toric_calculator104/')
			{
				$img="temp_images/barrett.png";
			}
			elseif($toric_url=='http://www.acrysoftoriccalculator.com/')	
			{
				$img="temp_images/alcon.png";
			}

			?>
			<!--
			iframe for static iframe 
			<iframe id="iframe_toric" frameborder="0" src="imw_toric_cal.php?id=<?php echo $img;?>" width="100%" height="<?php echo $_SESSION['wn_height']-230;?>px"></iframe>	-->
			<iframe id="iframe_toric" frameborder="0" src="<?php echo $toric_url;?>" width="100%" height="<?php echo $_SESSION['wn_height']-230;?>px"></iframe>
			</div>
			<div id="toric_right_block" class="col-sm-2">
				<div class="row">
					<div class="col-sm-12 purple_bar">
						<label>Capture Image</label>	
					</div>
					<div id="server_images" class="col-sm-12 pt10">
						<div class="row">
							<div id="div_images" class="col-sm-12"></div>
							<div id="div_images_footer" class="col-sm-12"></div>		
						</div>
					</div>
					<div class="col-sm-12 pt10 text-center">
						<input type="hidden" name="patient_id" id="addon_capture_patient_id" value="<?php echo $current_patient;?>">
						<input type="hidden" name="addon_capture_upload_api_url" id="addon_capture_upload_api_url" value="<?php echo $sendResponseTo;?>">
<!--						<input type="button" id="capture" class="btn btn-success" value="Save &amp; Close" onClick="capture_screen('<?php echo $img;?>')" >-->
					<input type="button" id="addon_capture_img_call" class="btn btn-success" value="Save &amp; Close" onClick="javascript:$('#divLoader').css('display','block');" >
					<!--<button class="btn btn-primary" onclick="chrome.webstore.install()" id="install-button">Add to Chrome</button>	-->
					</div>
					<div id="divLoader" class="col-sm-12 text-center" style="display:none; margin-top:5%; color:#9F1F22">
						<img src="../../library/images/ajax-loader.gif"></img><br>
							Please wait while image capturing and uploading.
					</div>	
				</div>	
			</div>	
		</div>
	</div>

</body>
<script>
/*if (chrome.app.isInstalled) {
  document.getElementById('install-button').style.display = 'none';
}*/
</script>
</html>
<?php /*header('http://www.apacrs.org/toric_calculator104/Toric%20Calculator.aspx');*/ ?>
