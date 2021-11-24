<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(300);
	include_once("common/conDb.php");  //MYSQL CONNECTION
	
	//START UNSET PROFILES FROM DEFAULT PROFILES
	$updateUnsetDefaultProfileQry = "UPDATE surgeonprofile SET defaultProfile='0'";
	$updateUnsetDefaultProfileRes = imw_query($updateUnsetDefaultProfileQry) or die(imw_error());
	//END UNSET PROFILES FROM DEFAULT PROFILES

$msg_info[] = "<br><br><b>All default profile are unset successfully</b>";

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