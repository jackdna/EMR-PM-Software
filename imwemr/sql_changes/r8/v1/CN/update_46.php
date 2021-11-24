<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$q = array();
$q[] = '
	ALTER TABLE `chart_pt_data` ADD `poe_cpt` VARCHAR(255) NOT NULL AFTER `rfs_comm`;
';

foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 46 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 46 completed successfully. </b>";
	$color = "green";	
	
}
?>
<html>
<head>
<title>Update 46</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>