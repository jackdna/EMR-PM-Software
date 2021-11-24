<?php
	if(!isset($GLOBALS)){
		include_once('../admin_header.php');
	}
	$browser = browser();
	//Check IP
	if(trim($phpServerIP) != trim($_SERVER['HTTP_HOST']))
	{
		$GLOBALS['php_server'] = $phpHTTPProtocol.$_SERVER['HTTP_HOST'].$phpServerPort.$web_root;
	}
	$userauthorized = $_SESSION['authId'];

	$upload_scan_url = $GLOBALS['php_server']."/interface/common/upload_scan_doc.php?operator=".$_SESSION['authId']."&imwemr=".session_id()."&page=adminDoc&recordId=".$_REQUEST['edit_id']."&method=scan";
?>
<script>
	var autoLoad = false;
	var autoScan = 'no';
	var upload_scan_url = '<?php echo $upload_scan_url; ?>';
	var scan_container_height = 400;
</script>
<div class="row">
	<div class="col-xs-12">
	<?php
	if($browser['name'] == 'msie'){
		include_once $GLOBALS['fileroot']. "/library/scan/scan_control.php";
	}
	else include_once $GLOBALS['fileroot']. "/library/scanc/scan_control.php";
	?>
	</div>
</div>