<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$q[] = 'ALTER TABLE `schedule_appointments` ADD `is_inpatient` TINYINT(2) NOT NULL;';      

$q[] = 'CREATE TABLE inpatient_fields (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  appt_id int(11) NOT NULL,
  field_type varchar(255) NOT NULL,
  field_code varchar(100) NOT NULL,
  field_codesystem varchar(100) NOT NULL,
  field_valuset varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;';

  
foreach($q as $qry) {
    imw_query($qry) or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 30 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 30 completed successfully. </b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 30</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>