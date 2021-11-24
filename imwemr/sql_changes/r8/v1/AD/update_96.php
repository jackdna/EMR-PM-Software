<?php 
/*previousely ythis index was created under special index but being  used regularly in msg_console class file which then raising an error, to sort this out now we have CREATED an regular update for it*/
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql="SHOW INDEX FROM user_messages WHERE key_name = 'usermsg_multicol'";
$row=imw_query($sql);
if(imw_num_rows($row)==0){
	$sql="CREATE INDEX usermsg_multicol ON user_messages(message_status,receiver_delete,message_to,message_sender_id,delivery_date,Pt_Communication,user_message_id);";
	$row = imw_query($sql)or $msg_info[] = imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 96  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 96  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 96</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>