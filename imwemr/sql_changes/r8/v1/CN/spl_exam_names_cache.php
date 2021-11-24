<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();

$sql="CREATE TABLE `chart_global_settings` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`field_name` TEXT NOT NULL ,
	`field_val` TEXT NOT NULL
	) ENGINE = MYISAM ;
	";
$row = imw_query($sql)or $msg_info[] = imw_error();


require_once("../../../../library/classes/work_view/exam_options.php");
$sql = "SELECT id FROM chart_global_settings WHERE field_name = 'exam_names_arr'";
$res = imw_query($sql);
if(imw_num_rows($res)>0){
	$row = imw_fetch_assoc($res);
	$sql = "UPDATE chart_global_settings 
			SET field_val = '".base64_encode(imw_real_escape_string(serialize($arrMain_examswise)))."' 
			WHERE id = '".$row['id']."'
		";
	
	imw_query($sql) or die(imw_error());	
}else{
	$sql = "INSERT INTO chart_global_settings 
				SET field_name = 'exam_names_arr' ,
				 field_val = '".base64_encode(imw_real_escape_string(serialize($arrMain_examswise)))."'
			";
	imw_query($sql);
}

$sql = "SELECT id FROM chart_global_settings WHERE field_name = 'exam_search_arr'";
$res = imw_query($sql);
if(imw_num_rows($res)>0){
	$row = imw_fetch_assoc($res);
	$sql = "UPDATE chart_global_settings 
			SET field_val = '".base64_encode(imw_real_escape_string(serialize($arrMain)))."' 
			WHERE id = '".$row['id']."'
		";
	imw_query($sql);	
}else{
	$sql = "INSERT INTO chart_global_settings 
				SET field_name = 'exam_search_arr' ,
				 field_val = '".base64_encode(imw_real_escape_string(serialize($arrMain)))."'
			";
	imw_query($sql);
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Release 6: Update 25 run FAILED!</b>";
	$msg_info[] = "<br><br><b style='color:green'>Exam Names cahce refreshed successfully </b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Release 6: Update 25 run successfully </b>";
	$msg_info[] = "<br><br><b>Exam Names cahce refreshed successfully </b>";
	$color = "green";
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Update 25</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color_sts;?>" size="2">
    <?php echo(@implode("<br>",$msg_info_sts));?>
</font>
</body>
</html>