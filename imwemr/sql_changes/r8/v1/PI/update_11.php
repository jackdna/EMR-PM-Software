<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
 
$q = "CREATE TABLE `ccd_incorporate_log` (
	 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`scan_doc_tbl_id` INT NOT NULL COMMENT 'pkid of scan_doc_tbl',
	`patient_id` INT NOT NULL COMMENT 'pkid of patient_data',
	`section_done` VARCHAR( 50 ) NOT NULL ,
	`done_by` INT NOT NULL COMMENT 'pkid of users',
	`done_on` DATETIME NOT NULL 
	) ENGINE = MYISAM";
$r = imw_query($q) or $msg_info[]=imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 11  run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 11  run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 11 (PI)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<h3>CCD Electronic Incorporate Log Table Creation</h3>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>