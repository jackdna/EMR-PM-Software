<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$sql = [];
$sql[] = "ALTER TABLE facility ADD erp_id varchar(100) NULL;";
$sql[] = "ALTER TABLE facility ADD erp_contact_id varchar(100) NULL;";
$sql[] = "ALTER TABLE facility ADD erp_email_id varchar(100) NULL;";
$sql[] = "ALTER TABLE facility ADD erp_phone_id varchar(100) NULL;";
$sql[] = "ALTER TABLE facility ADD erp_fax_id varchar(100) NULL;";
$sql[] = "ALTER TABLE facility ADD erp_address_id varchar(100) NULL;";
$sql[] = "ALTER TABLE facility ADD erp_country_id varchar(100) NULL;";

foreach($sql as $query)
	imw_query($query) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>V2.01.1 - Update 1 run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>V2.01.1 - Update 1 run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>V2.01.1 - Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>