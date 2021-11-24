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
$sql[] = "ALTER TABLE  `users` ADD  `amd_user_id` VARCHAR( 255 ) NOT NULL COMMENT  'comma seprated multiple ids'";

$sql[] = "ALTER TABLE  `patient_in_waiting_tbl` ADD  `amd_user_id` INT NOT NULL ,
ADD  `amd_facility_code` VARCHAR( 255 ) NOT NULL";

$sql[] = "ALTER TABLE  `patient_in_waiting_tbl` ADD  `amd_respparty` VARCHAR( 20 ) NOT NULL ,
ADD  `amd_finclasscode` VARCHAR( 20 ) NOT NULL";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 137 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 137 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 137</title>
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