<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");


$updateQry = "UPDATE localanesthesiarecord SET 
				mgPropofol_label	='mg Propofol',
				mgMidazolam_label	='mg Midazolam',
				mgKetamine_label	='mg Ketamine',
				mgLabetalol_label	='mg Labetalol',
				mcgFentanyl_label	='mg Fentanyl'
				WHERE mgPropofol_label	=''";
$updateRes = imw_query($updateQry) or die(imw_error());			 


$msg_info[] = "<br><br><b>Local Anesthesia Medicine Updated Successfully</b>";

?>
<html>
<head>
<title>Mysql Updates After Launch </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>