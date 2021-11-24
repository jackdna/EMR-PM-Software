<?php 
/*
update hipaa content
replace `hippa` with `hipaa`
*/
	
$ignoreAuth = true;
include("../../../../config/globals.php");

$q=imw_query("select * from hippa_setting") or $msg_info[] = imw_error();
while($d=imw_fetch_object($q))
{
	$content=$d->loginLegalNotice;
	$content=str_replace('HIPPA','HIPAA',$content);
	imw_query("update hippa_setting set loginLegalNotice='$content' where id=$d->id") or $msg_info[] = imw_error();
}
if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 24 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 24 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 24</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>