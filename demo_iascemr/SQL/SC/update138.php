<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql = array();

// Update to add external fields/table for Advacned MD Api integration
$sql[] = "ALTER TABLE `operatingroomrecords` ADD `save_manual` INT( 5 ) NOT NULL ";

$sql[] = "UPDATE operatingroomrecords SET save_manual = '1' WHERE form_status = 'not completed' OR form_status = 'completed' ";


foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 138 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 138 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 138</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>