<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

imw_query("update icd10_data set icd10_desc='TYPE 2 DIABETES MELLITUS WITH SEVERE PROLIFERATIVE DIABETIC RETINOPATHY WITH MACULAR EDEMA' where icd10 = 'E11.35--'") or $msg_info[] = imw_error();
imw_query("update icd10_data set icd10_desc='TYPE 2 DIABETES MELLITUS WITH SEVERE PROLIFERATIVE DIABETIC RETINOPATHY WITHOUT MACULAR EDEMA' where icd10 = 'E11.359-'") or $msg_info[] = imw_error();
	
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 18 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 18 completed successfully.</b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 18</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>