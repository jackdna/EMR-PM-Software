<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");
$sql[] = "ALTER TABLE  `surgerycenter` 
		ADD  `small_label_enable_surgeon` VARCHAR( 5 ) NOT NULL ,
		ADD  `small_label_enable_procedure` VARCHAR( 5 ) NOT NULL ,
		ADD  `small_label_enable_patient_mrn` VARCHAR( 5 ) NOT NULL ,
		ADD  `small_label_enable_patient_gender` VARCHAR( 5 ) NOT NULL ,
		ADD  `small_label_enable_patient_dos` VARCHAR( 5 ) NOT NULL ,
		ADD  `large_label_enable_surgeon` VARCHAR( 5 ) NOT NULL ,
		ADD  `large_label_enable_procedure` VARCHAR( 5 ) NOT NULL ,
		ADD  `large_label_enable_patient_mrn` VARCHAR( 5 ) NOT NULL ,
		ADD  `large_label_enable_patient_gender` VARCHAR( 5 ) NOT NULL ,
		ADD  `large_label_enable_patient_dos` VARCHAR( 5 ) NOT NULL
		"; 

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$qry = "SELECT small_label_enable_surgeon, small_label_enable_procedure, small_label_enable_patient_dos, large_label_enable_surgeon, large_label_enable_procedure, large_label_enable_patient_dos FROM surgerycenter WHERE surgeryCenterId = '1' LIMIT 0,1";
$res = imw_query($qry)or $msg_info[] = imw_error();
if(imw_num_rows($res)>0) {
	$row = imw_fetch_assoc($res);
	$small_label_enable_surgeon 	= trim($row["small_label_enable_surgeon"]);
	$small_label_enable_procedure 	= trim($row["small_label_enable_procedure"]);
	$small_label_enable_patient_dos = trim($row["small_label_enable_patient_dos"]);
	$large_label_enable_surgeon 	= trim($row["large_label_enable_surgeon"]);
	$large_label_enable_procedure 	= trim($row["large_label_enable_procedure"]);
	$large_label_enable_patient_dos = trim($row["large_label_enable_patient_dos"]);
	if(!$small_label_enable_surgeon && constant("SMALL_LABEL_SHOW_SURGEON")=="YES") {
		$sql1="UPDATE surgerycenter SET small_label_enable_surgeon = 'Y' Where surgeryCenterId = '1' ";
		imw_query($sql1)or $msg_info[] = imw_error();
	}
	if(!$small_label_enable_procedure) {
		$sql1="UPDATE surgerycenter SET small_label_enable_procedure = 'Y' Where surgeryCenterId = '1' ";
		imw_query($sql1)or $msg_info[] = imw_error();
	}
	if(!$small_label_enable_patient_dos) {
		$sql1="UPDATE surgerycenter SET small_label_enable_patient_dos = 'Y' Where surgeryCenterId = '1' ";
		imw_query($sql1)or $msg_info[] = imw_error();
	}
	
	if(!$large_label_enable_surgeon) {
		$sql1="UPDATE surgerycenter SET large_label_enable_surgeon = 'Y' Where surgeryCenterId = '1' ";
		imw_query($sql1)or $msg_info[] = imw_error();
	}
	if(!$large_label_enable_procedure) {
		$sql1="UPDATE surgerycenter SET large_label_enable_procedure = 'Y' Where surgeryCenterId = '1' ";
		imw_query($sql1)or $msg_info[] = imw_error();
	}
	if(!$large_label_enable_patient_dos) {
		$sql1="UPDATE surgerycenter SET large_label_enable_patient_dos = 'Y' Where surgeryCenterId = '1' ";
		imw_query($sql1)or $msg_info[] = imw_error();
	}
	
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 166 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 166 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 166</title>
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