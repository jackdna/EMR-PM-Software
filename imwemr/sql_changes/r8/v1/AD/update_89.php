<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = array();

//$qry[] = "UPDATE custom_reports SET template_name = 'ACI (Promoting Interoperability) 2018' WHERE template_name = 'ACI 2018' AND report_type='compliance'";
//$qry[] = "UPDATE custom_reports SET template_name = 'Transitional ACI (Promoting Interoperability) 2018' WHERE template_name = 'Transition ACI 2018' AND report_type='compliance'";

//foreach($qry as $q){imw_query($q) or $msg_info[] = imw_error();}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 89 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 89 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
    <title>Update 89</title>
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