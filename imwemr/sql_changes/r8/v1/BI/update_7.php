<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

$sql[] = "CREATE TABLE era_835_proc_posted ( id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , era_835_proc_id INT( 11 ) NOT NULL ,
patient_id INT( 11 ) NOT NULL ,encounter_id INT( 11 ) NOT NULL ,charge_list_id INT( 11 ) NOT NULL ,charge_list_detail_id INT( 11 ) NOT NULL ,
cas_type VARCHAR( 250 ) NOT NULL ,cas_code VARCHAR( 250 ) NOT NULL ,cas_amt VARCHAR( 250 ) NOT NULL ,ins_type INT( 11 ) NOT NULL)";

$sql[] = "ALTER TABLE `era_835_proc_posted` ADD `chk_date` DATE NOT NULL ,ADD `entered_date` DATETIME NOT NULL";

foreach(  $sql as $query)
	imw_query($query) or $msg_info[] = imw_error();
	
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 7 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 7 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 7</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>