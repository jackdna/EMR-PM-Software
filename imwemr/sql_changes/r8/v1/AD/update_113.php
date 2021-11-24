<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$msg_info=array();

$d = imw_query("DROP TABLE IF EXISTS imonitor_extended_cols");

$sql1="CREATE TABLE `imonitor_extended_cols` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`column_name` VARCHAR( 255 ) NOT NULL ,
`column_desc` VARCHAR( 1024 ) NOT NULL ,
`show_status` TINYINT( 5 ) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2="INSERT INTO `imonitor_extended_cols` (`id`, `column_name`, `column_desc`, `show_status`) VALUES
(1, 'Patient Name', 'Displays the name of patient directly pulled from Appointment Schedular.', 1),
(2, 'Appt. Reason', 'Displays the type of visit that the patient schedule for today.', 1),
(3, 'Appt. Time', 'Displays the time when patient marked as arrived.', 1),
(4, 'Arrival', 'Displays the schedule appointment time for the patient visit.', 1),
(5, 'Check-In', 'Displays the time that the check-in process was completed by the Front Desk staff.', 1),
(6, 'Front Desk Time', 'Displays the accumulative time for the Front Desk to complete their process.', 1),
(7, 'FSh Y/N', 'Displays that the Face Sheet for the patient was printed.', 1),
(8, 'Appt./Arrival to Now', 'Displays the accumulative time from Arrival or Appointment (if arrival not recorded) to the present time & continues until the patient checked out.', 1),
(9, 'Check-In to Now', 'Displays the accumulative time for the Front Desk process to present time & continues until the patient checked out.', 1),
(10, 'Work Up with Tech', 'Displays the time of the Tech started the process - triggered login to the chart.', 1),
(11, 'Tech Room', 'Displays the room where the Technician has logged into the patient\'s chart.', 1),
(12, 'Subwait Time', 'Displays the time that the Tech has saved and closed the chart and or manually moved the patient to WR.', 1),
(13, 'Total Tech Work-Up Time', 'Displays total time Technician spent on patient chart.', 1),
(14, 'Dilation Time', 'Displays the total number of minutes a patient has been dilating - triggered from Dialation Time in chart.', 1),
(15, 'Total Subwait Time', 'Displays the accumulative minutes from when the Tech moved the patient untill the Doctor starts his exam.', 1),
(16, 'Doctor Start Time', 'Displays the time Doctor logged into the patient chart.', 1),
(17, 'Doctor Room', 'Displays the room where the Physician has logged into the patient\'s chart.', 1),
(18, 'Doctor End Time', 'Displays the time in which the doctor has finalized the chart and or patient has checked whichever is first.', 1),
(19, 'Doctor In Room Time', 'Displays the accumulative time the doctor was in the room and or chart with the patient.', 1),
(20, 'Checked Out', 'Displays the time the patient has checked out by the front desk.', 1); ";
imw_query($sql2) or $msg_info[] = imw_error();

if(count($msg_info)>0)
{
    $msg_info[] = '<br><br><b>Update 113  run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update 113  run successfully!</b>";
    $color = "green";
}
?>
<html>
<head>
<title>Update 113</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>