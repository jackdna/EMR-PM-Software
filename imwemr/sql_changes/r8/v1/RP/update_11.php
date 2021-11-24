<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$q = array();
$msg_info = array();

$q[] = "CREATE TABLE `batch_file_tx_report` (
  `batch_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `header_control_identifier` int(11) NOT NULL,
  `Transaction_set_unique_control` int(11) NOT NULL,
  `file_name` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `file_data` longtext COLLATE latin1_general_ci NOT NULL,
  `create_date` date NOT NULL,
  `encounter_id` longtext COLLATE latin1_general_ci NOT NULL,
  `pcld_id` longtext COLLATE latin1_general_ci NOT NULL COMMENT 'patient_charge_list_detials_id',
  `submitter_id` longtext COLLATE latin1_general_ci NOT NULL,
  `reciever_id` longtext COLLATE latin1_general_ci NOT NULL,
  `group_id` int(10) NOT NULL,
  `production_code` varchar(2) COLLATE latin1_general_ci NOT NULL,
  `Interchange_control` int(12) NOT NULL,
  `operatorId` longtext COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`batch_file_id`),
  KEY `file_name` (`file_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";

foreach($q as $qry){
    imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 11  run FAILED!</b><br>';
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
<title>Update 11 (RP)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>