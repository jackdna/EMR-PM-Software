<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

imw_query("update icd10_data set icd10_desc='TYPE 2 DIABETES MELLITUS WITHOUT COMPLICATIONS' where icd10 in ('E11.9') and icd10_desc='TYPE 2 DIABETES WITHOUT OPHTHALMIC COMPLICATIONS'");

$msg_info[] = "<br><b>Release :<br> Update Success.</b>";

$color = "green";	

?>
<html>
<head>
<title>Update 77</title>
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