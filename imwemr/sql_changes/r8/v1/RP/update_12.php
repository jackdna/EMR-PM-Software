<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$q = array();
$msg_info = array();

$q[] = "CREATE TABLE `patient_monitor_daily` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(4) NOT NULL,
  `user_type_id` int(3) NOT NULL,
  `scheduler_appt_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `action_name` varchar(50) NOT NULL,
  `status_changed_for` int(11) NOT NULL,
  `action_date_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$q[] = "ALTER TABLE `patient_monitor_daily`  ADD `app_room` VARCHAR(50) NOT NULL COMMENT 'patient_room' AFTER `status_changed_for`";
$q[] = "ALTER TABLE `patient_monitor`  ADD `app_room` VARCHAR(50) NOT NULL COMMENT 'patient_room' AFTER `status_changed_for`";

foreach($q as $qry){
    imw_query($qry) or $msg_info[]=imw_error();
}


if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 12  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 12  run successfully!</b>";
    $color = "green";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 12 (RP)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>