<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$error = array();

$sql1 = "ALTER TABLE `chart_master_table` CHANGE `service_eligibility` `service_eligibility` VARCHAR(300) NOT NULL DEFAULT '0' COMMENT 'specify if DSS Visit is service connected eligibility 1=Yes and 0=No';";
imw_query($sql1) or $error[] = imw_error();

$sql2 = "CREATE TABLE `dss_test_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `test_id` int(11) NOT NULL,
  `service_ien` int(11) NOT NULL COMMENT 'DSS svcIen',
  `service_name` varchar(300) NOT NULL COMMENT 'DSS svcName',
  `service_orderable_item` int(11) NOT NULL COMMENT 'DSS orderableItem',
  `status` TINYINT NOT NULL DEFAULT '0' COMMENT '0=active,1=deleted',
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
imw_query($sql2) or $error[] = imw_error();

$sql3 = "ALTER TABLE `test_custom_patient` 
  ADD `dss_orderable_item` INT NOT NULL COMMENT 'DSS' , 
  ADD `dss_placeOfConsult` CHAR(10) NOT NULL COMMENT 'DSS' , 
  ADD `dss_reasonForRequest` TEXT NOT NULL COMMENT 'DSS' , 
  ADD `dss_dxCode` VARCHAR(10) NOT NULL COMMENT 'DSS' , 
  ADD `dss_dxText` VARCHAR(300) NOT NULL COMMENT 'DSS' , 
  ADD `dss_orderNumber` INT NOT NULL COMMENT 'DSS response order number' , 
  ADD `dss_group` INT NOT NULL COMMENT 'DSS response group' , 
  ADD `dss_orderTime` DATETIME NOT NULL COMMENT 'DSS response order dt' , 
  ADD `dss_status` INT NOT NULL COMMENT 'DSS Status';";
imw_query($sql3) or $error[] = imw_error();

$sql4 = "ALTER TABLE `test_custom_patient` 
  ADD `dss_service` VARCHAR(300) NOT NULL COMMENT 'DSS' , 
  ADD `dss_service_ien` INT(11) NOT NULL COMMENT 'DSS' ;";
imw_query($sql4) or $error[] = imw_error();

if(count($error)>0)
{
  $error[] = "<br><br><b>Update 5 Failed!</b>";
  $color = "red";
}
else
{
  $error[] = "<br><br><b>Update 5 Success.</b>";
  $color = "green"; 
}
?>

<html>
<head>
<title>Update 5</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$error));?></font>

</body>
</html>