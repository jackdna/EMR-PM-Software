<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="ALTER TABLE `icd10_data` CHANGE `icd9` `icd9` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;";
imw_query($sql1) or $msg_info[] = imw_error();

$sql1="ALTER TABLE `diagnosis_code_tbl` CHANGE `d_prac_code` `d_prac_code` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;";
imw_query($sql1) or $msg_info[] = imw_error();

$sql1="ALTER TABLE `diagnosis_code_tbl` CHANGE `dx_code` `dx_code` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;";
imw_query($sql1) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 5 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>PI &gt; Update 5 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 5 (PI)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>