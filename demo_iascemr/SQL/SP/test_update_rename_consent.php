<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(900);
	include_once("common/conDb.php");  //MYSQL CONNECTION
	
	$updtIolinkScnConsentQry 	= "UPDATE iolink_scan_consent SET iolink_scan_folder_name='consent' WHERE iolink_scan_folder_name='anesthesiaConsent'";
	$updtIolinkScnConsentRes 	= imw_query($updtIolinkScnConsentQry) or die(imw_error());
	$updtScnDocQry 				= "UPDATE scan_documents SET document_name='Consent' WHERE document_name='Anesthesia Consent'";
	$updtScnDocRes 				= imw_query($updtScnDocQry) or die(imw_error());
$msg_info[] = "<br><br><b> 'Anesthesia Consent' renamed to 'Consent' updated successfully</b>";
?>
<html>
<head>
<title>Mysql Updates After Launch </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>