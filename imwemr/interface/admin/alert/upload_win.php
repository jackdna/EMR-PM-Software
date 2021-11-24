<?php 
	require_once("../../../config/globals.php");
	$library_path = $GLOBALS['webroot'].'/library';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Patient Refractive Sheet</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/admin.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/bootstrap-colorpicker.css" rel="stylesheet" type="text/css">
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
		<script src="<?php echo $library_path; ?>/js/jquery.mCustomScrollbar.concat.min.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/bootstrap-formhelpers-colorpicker.js" type="text/javascript"></script>
		<script>
			//var global_js_vars = JSON.parse('<?php echo $global_js_array; ?>');
			var upload_url = '<?php echo $GLOBALS['webroot']."/interface/admin/facility/ajax.php?imwemr=".session_id()."&upType=".$opType ?>';
		</script>
		<script src="<?php echo $library_path; ?>/js/admin_facility.js" type="text/javascript"></script>
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
			.adminbox{min-height:inherit}
			.adminbox label{overflow:initial;}
			.adminbox .panel-body{padding:5px}
			.adminbox div:nth-child(odd) {padding-right: 1%;}
			.od{color:blue;}
			.os{color:green;}
			.ou{color:#9900cc;}
			.pad-left1{	padding-left:1px;}
			footer::before {
				content: "";
				position: fixed;
				left: 0;
				width: 100%;
				height: 100%;
				-webkit-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
				-moz-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
				box-shadow: 0px 0px 6px rgba(0,0,0,.8);
				z-index: 100;
			}
			.modal .modal-lg{width:90%!important}
		</style>
	</head>
	<body>
		<div class="mainwhtbox">
			<div class="row"> 
				<div class="col-sm-12">
					<?php 
						include($GLOBALS['srcdir']."/upload/index.php");
					?>	
				</div>	
			</div>	
		</div>
	</body>
	<script>
		window.resizeTo(700,500);
	</script>
</html>