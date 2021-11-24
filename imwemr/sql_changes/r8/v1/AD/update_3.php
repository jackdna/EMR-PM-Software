<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$qry = "RENAME TABLE  `schedule_status` TO  `schedule_status_old` ;";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "CREATE TABLE IF NOT EXISTS `schedule_status` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `alias` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `status_icon` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=274 ;";
imw_query($qry) or $msg_info[] = imw_error();

$qry = "INSERT INTO `schedule_status` (`id`, `status_name`, `alias`, `status_icon`, `status`) VALUES
(1, 'Reminder Done', 'RD', 'remainder_done.gif', 1),
(2, 'Chart Pulled', 'CP', 'chart_pulled.gif', 1),
(3, 'No Show', 'NS', 'no_show.gif', 1),
(5, 'Arrived Late', 'AL', 'arrived_late.gif', 0),
(6, 'Left without visit', 'LV', 'left_without_visit.gif', 1),
(7, 'Insurance/Financial Issue', 'FI', 'insurance_financial_crisis.gif', 1),
(8, 'Billing Done', 'BD', 'billing_done.gif', 0),
(9, 'Vitals Done', 'VD', 'vitals_done.gif', 1),
(10, 'In Exam Room', 'ER', 'in_exam_room.gif', 1),
(11, 'Checked Out', 'CO', 'checked_out.gif', 1),
(12, 'Coding Done', 'CD', 'coding_done.gif', 0),
(13, 'Check-in', 'CI', 'check_in.gif', 1),
(14, 'In Waiting Room', 'WR', 'in_waiting_room.gif', 1),
(15, 'With Technician', 'WT', 'with_technician.gif', 0),
(16, 'With Physician', 'WP', 'with_pysician.gif', 1),
(17, 'Confirm', 'CF', 'confirm.gif', 1),
(18, 'cancelled', 'Cancelled', 'cancelled.gif', 1),
(100, 'Waiting for Surgery', 'W/Sx', 'waiting_for_sx.gif', 1),
(101, 'Scheduled For Surgery', 'S/Sx', 'schedule_for_sx.gif.gif', 1),
(202, 'Reschedule', 'RS', '', 1),
(21, 'Patient-Cancel', 'PC', '', 1),
(22, 'Left-Message', 'LM', '', 1),
(23, 'Not-Confirm', 'NC', '', 1),
(200, 'Room Assigned', 'RA', '', 1),
(201, 'To-Do-Rescheduled', 'To Do', '', 1),
(203, 'Deleted', 'Deleted', '', 1),
(271, 'First Available', 'FA', '', 1),
(4, 'Arrived', 'AR', '', 1);";
imw_query($qry) or $msg_info[] = imw_error();

$qry="ALTER TABLE  `schedule_status` ADD  `added_by` INT NOT NULL ,
ADD  `added_datetime` DATETIME NOT NULL ,
ADD  `modify_by` INT NOT NULL ,
ADD  `modify_datetime` DATETIME NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry="update `schedule_status` set status=0 where id IN (1,2,5,6,8,9,10,12,14,15,16,100,101,21,22,23,200,4)";
imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 3 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 3 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 3</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>