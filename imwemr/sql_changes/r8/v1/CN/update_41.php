<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$q = array();
$q[] = '
	ALTER TABLE `order_details` CHANGE `inform` `inform` TEXT NOT NULL;
';

$q[] = '
	ALTER TABLE `order_set_associate_chart_notes_details` CHANGE `inform` `inform` TEXT  NOT NULL;
';

foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 41 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 41 completed successfully. </b>";
	$color = "green";
	
	
}
?>
<html>
<head>
<title>Update 41</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>