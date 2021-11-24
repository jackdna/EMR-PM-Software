<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../../common/conDb.php");


$sql1="ALTER TABLE `iolink_insurance_case` 
		ADD  INDEX patient_id(patient_id),
		ADD  INDEX athenaID(athenaID),
		ADD  INDEX case_name(case_name),
		ADD  INDEX case_status(case_status),
		ADD  INDEX vision(vision),
		ADD  INDEX normal(normal),
		ADD  INDEX ins_caseid(ins_caseid);"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `insurance_data` 
		ADD  INDEX type(type),
		ADD  INDEX ins_caseid(ins_caseid),
		ADD  INDEX actInsComp(actInsComp),
		ADD  INDEX ins_provider(ins_provider),
		ADD  INDEX ins_in_house_code(ins_in_house_code),
		ADD  INDEX active_date(active_date),
		ADD  INDEX expiry_Date(expiry_Date);"; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$message = "<br><br><b>Update 6 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";
}
else
{	
	$message = "<br><br><b>Update 6 Success.</b><br>";
	$color = "green";			
}

?>

<html>
<head>
<title>Update 6</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo $message;?></font>
<?php
@imw_close();
}
?> 
</body>
</html>