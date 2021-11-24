<?php 
	require_once("../admin_header.php");
	$library_path = $GLOBALS['webroot'].'/library';
?>
<script>
	var upload_url = '<?php echo $GLOBALS['webroot']."/interface/admin/optical/ajax_frames.php?imwemr=".session_id()."&upType=".$opType ?>';
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
<body>
	<div class="whtbox">
		<div class="row"> 
			<div class="col-sm-12">
				<?php include($GLOBALS['srcdir']."/upload/index.php"); ?>	
			</div>	
		</div>	
	</div>
	<script>
	window.resizeTo(700,500);
</script>
<?php 	
	require_once("../admin_footer.php");
?>