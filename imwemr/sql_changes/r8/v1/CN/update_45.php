<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$q = array();
$q[] = '
	ALTER TABLE `chart_pt_assessment_plans` ADD `diag_order` INT(5) NOT NULL AFTER `modify_time`;
';

foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 45 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 45 completed successfully. </b>";
	$color = "green";	
	
}
?>
<html>
<head>
<title>Update 45</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>