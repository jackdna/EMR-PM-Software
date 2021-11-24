<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
//include_once("../../common/conDb.php");
include_once("../../connect_imwemr.php");

//START SQL UPDATE IN IDOC
$sql="ALTER TABLE `superbill` ADD `primary_provider_id_for_reports` INT NOT NULL";
imw_query($sql) or $msg_info[] = imw_error();


$sql="ALTER TABLE `patient_charge_list` ADD `primary_provider_id_for_reports` INT NOT NULL";
imw_query($sql) or $msg_info[] = imw_error();


$sql="ALTER TABLE `patient_charge_list_details` ADD `primary_provider_id_for_reports` INT NOT NULL";
imw_query($sql) or $msg_info[] = imw_error();

$sql="ALTER TABLE `users` ADD `sx_physician` INT( 1 ) NOT NULL ";
imw_query($sql) or $msg_info[] = imw_error();
//END SQL UPDATE IN IDOC


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 54 run OK";

?>

<html>
<head>
<title>Update 54</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







