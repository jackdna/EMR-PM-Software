<?php
include_once("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_hx_import.class.php");
$import_obj = New Medical_Import;
if(!$import_obj->patient_id) die;
$library_path = $GLOBALS['webroot'].'/library';
$direct = '';
//Uploading Files to the system
if(isset($_REQUEST['upload_file']) && $_REQUEST['upload_file'] == 'yes'){
	$upload_status = $import_obj->import_document($_REQUEST,$_FILES);
	$import_obj->import_error = ($upload_status['error']) ? $upload_status['error'] : '' ;
}
if($_REQUEST['showpage'] == 'medication'){
	$_REQUEST['showpage'] = 'medications';
}

//PHP value array to be used in jquery
$ret_array = array();
$ret_array['curr_tab']	= $_REQUEST['showpage'];
$ret_array['xml_id'] 	= $_REQUEST['xml_id'];
$ret_array['page_request'] 	= $_REQUEST['page_request'];
$ret_array['webroot'] 	= $GLOBALS['webroot'];
$global_js_arr =  json_encode($ret_array);
?>
<!DOCTYPE html>
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
	<script src="<?php echo $library_path; ?>/js/medical_hx.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/js/medical_import.js" type="text/javascript"></script>
	<script> 
		var global_php_var = $.parseJSON('<?php echo $global_js_arr; ?>');
	</script>	
	<style>
		.process_loader {
			border: 16px solid #f3f3f3;
			border-radius: 50%;
			border-top: 16px solid #3498db;
			width: 80px;
			height: 80px;
			-webkit-animation: spin 2s linear infinite;
			animation: spin 2s linear infinite;
			display: inline-block;
		}
		.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus{
			background-color:#727272 !important;
			color: #fff;
			font-weight:bold;
		}
		
		p{margin:0px!important}
		.nav-tabs.medical_import{border-bottom:none!important}
		
		
	</style>
  </head>
	<body>
		<input type="hidden" name="preObjBack" id="preObjBack" value="a">
		<div class="container-fluid pd0">
			<div class="mainwhtbox">
				<div class="row">
					<div id="div_loading_image" class="col-sm-12 text-center" style="top:50%;margin-top: 0px; display: none;position:absolute;z-index:9999">
						<div class="loading_container">
							<div class="process_loader"></div>
							<div id="div_loading_text" class="text-info"></div>
						</div>
					</div>	
					<!-- Header -->
					<div class="col-sm-12">
						<?php
							include_once('index_header.php');
						?>
					</div>
					
					<!-- Content Block -->
					<div id="content_block" class="col-sm-12">
						<?php
							$folder = $_REQUEST['showpage'];
							if($folder == '') $folder = 'ocular';
							elseif($folder == 'medication') $folder = 'medications';
							$path = $GLOBALS['fileroot']. '/interface/Medical_history/import/'.$folder.'/index.php';
							include_once $path;
						?>
					</div>
					
					<!-- Footer Buttons -->
					<div id="core_buttons_bar" class="col-sm-12 pt10 text-center">
						<div id="page_buttons">
							<input type="button" value="Consolidate" name="consolidate"  id="consolidate" class="btn btn-success" onClick="submit_frame()"> 
							<input type="button" value="Merge" name="merge" id="merge" class="btn btn-success" onClick="submit_frame()">
							<input type="button" value="Close" name="close" id="close" class="btn btn-danger" onClick="window.close();">
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>