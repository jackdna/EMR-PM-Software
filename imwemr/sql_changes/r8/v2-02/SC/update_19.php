<?php 
/**
 * EXPECTED ARRIVAL TIME COLUMN ADDED FOR PROCEDURES
 */ 
 
//------ENABLE TO ACCESS FILE------
$ignoreAuth = true; 

//------GLOBAL FILE INCLUSION------
include("../../../../config/globals.php"); 

//------ADD COLUMN QUERY-----------
$sql[]="ALTER TABLE
			`slot_procedures` 
		ADD  
			`exp_arrival_time` varchar(255) DEFAULT NULL
		AFTER  
			`non_billable`";

foreach($sql as $q)
{
	imw_query($q) or $msg_info[] = imw_error();
}

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 19 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 19 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 19 - Expected Arrival Column Added For Procedure Templates</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>