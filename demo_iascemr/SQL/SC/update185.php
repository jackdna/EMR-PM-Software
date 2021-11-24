<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE `surgerycenter` 
			ADD `suppliesHostName` VARCHAR( 255 ) NOT NULL ,
			ADD `suppliesPortNumber` VARCHAR( 255 ) NOT NULL ,
			ADD `suppliesUsername` VARCHAR( 255 ) NOT NULL ,
			ADD `suppliesPassword` VARCHAR( 255 ) NOT NULL ,
			ADD `suppliesFilePath` VARCHAR( 255 ) NOT NULL;";

$sql[] = "ALTER TABLE `predefine_suppliesused` 
			ADD `supply_quick_code` VARCHAR( 255 ) NOT NULL ,
			ADD `supply_billable` TINYINT( 1 ) NOT NULL ,
			ADD `supply_usage_unit` VARCHAR( 255 ) NOT NULL ,
			ADD INDEX supply_quick_code(supply_quick_code), 
			ADD INDEX supp_name(`name`);";

$sql[] = "ALTER TABLE `supply_categories` ADD INDEX supp_cat_name(`name`);";


foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = $qry.imw_error();
}

//START SET SUPPLIES DEFAULT TO BILLABLE
$qry = "SELECT suppliesUsedId FROM predefine_suppliesused WHERE supply_billable = '1' LIMIT 0,1";
$res = imw_query($qry)or $msg_info[] = $qry.imw_error();
if(imw_num_rows($res)==0) {
	$updtQry = "UPDATE predefine_suppliesused SET supply_billable = '1' WHERE supply_quick_code = '' ";	
	imw_query($updtQry)or $msg_info[] = $updtQry.imw_error();
}
//END SET SUPPLIES DEFAULT TO BILLABLE

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 185 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 185 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 185</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>