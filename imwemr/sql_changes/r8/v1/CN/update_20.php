<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$q = array();
$q[] = 'ALTER TABLE `chart_procedures` ADD `encounter_id` INT(10) NOT NULL; ';

  
foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 20 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 20 completed successfully. </b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 20</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>