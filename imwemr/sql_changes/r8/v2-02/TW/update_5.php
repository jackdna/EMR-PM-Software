<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = array();

$qry[] = "ALTER TABLE `as_credentials` ADD COLUMN `use_org_id` TINYINT(1) NOT NULL COMMENT 'Whether to use TW organization Id in the API calls'";
$qry[] = "ALTER TABLE `as_credentials` ADD COLUMN `ehr_username` VARCHAR(255) NOT NULL COMMENT 'Generic TW EHR username'";
$qry[] = "ALTER TABLE `as_credentials` ADD COLUMN `ehr_password` VARCHAR(255) NOT NULL COMMENT 'Password for TW generic user Id'";

foreach ($qry  as $sql){
	imw_query($sql) or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>TouchWorks Update 5 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>TouchWorks Update 5 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 5</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>