<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql1="ALTER TABLE `tests_name` ADD `version` SMALLINT NOT NULL DEFAULT '0' COMMENT 'pk_id of tests_version' AFTER `test_type`";
imw_query($sql1) or $msg_info[] = imw_error();

$sql2="CREATE TABLE `tests_version` (
		 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		`tests_name_id` INT NOT NULL COMMENT 'pk_id of tests_name',
		`test_main_options` TEXT NOT NULL ,
		`test_main_option_mo_counter` SMALLINT NOT NULL ,
		`test_treatment` TEXT NOT NULL ,
		`test_treatment_mo_counter` SMALLINT NOT NULL ,
		`test_results` TEXT NOT NULL ,
		`created_by` INT NOT NULL COMMENT 'pk_id of users',
		`created_on` DATETIME NOT NULL 
		) ENGINE = MYISAM ;";
imw_query($sql2) or $msg_info[] = imw_error();


$sql3 = "CREATE TABLE `test_custom_patient` (
  `test_id` int(10) NOT NULL AUTO_INCREMENT,
  `test_other` varchar(255) NOT NULL,
  `test_other_eye` varchar(200) NOT NULL,
  `performedBy` int(10) NOT NULL,
  `diagnosis` varchar(250) NOT NULL,
  `ptUnderstanding` varchar(20) NOT NULL,
  `reliabilityOd` varchar(20) NOT NULL,
  `reliabilityOs` varchar(20) NOT NULL,
  `test_main_options` text NOT NULL,
  `test_result` text NOT NULL,
  `test_treatment` text NOT NULL,
  `phyName` int(10) NOT NULL,
  `examDate` date NOT NULL,
  `patientId` int(10) NOT NULL,
  `formId` int(10) NOT NULL,
  `diagnosisOther` varchar(250) NOT NULL,
  `status_operator_id` int(12) NOT NULL,
  `status` int(12) NOT NULL,
  `reason_text` varchar(250) NOT NULL,
  `cur_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `del_status` enum('0','1') NOT NULL,
  `techComments` varchar(255) NOT NULL,
  `examTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `encounter_id` int(10) NOT NULL,
  `ordrby` int(10) NOT NULL,
  `ordrdt` date NOT NULL,
  `purged` int(2) NOT NULL,
  `finished` smallint(6) NOT NULL DEFAULT '0' COMMENT 'task_finished:1, otherwise:0',
  `sign_path` varchar(255) NOT NULL,
  `sign_path_date_time` datetime NOT NULL,
  `study_uid` varchar(255) DEFAULT NULL,
  `test_template_id` int(11) NOT NULL DEFAULT '0' COMMENT '0 for testOther; >0 for template tests',
  `version` int(11) NOT NULL COMMENT 'pk_id of tests_version',
  PRIMARY KEY (`test_id`),
  KEY `indxtestother` (`patientId`,`formId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1";

imw_query($sql3) or $msg_info[] = imw_error();
/*************************************/

if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 5 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>CN &gt; Update 5 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 5 (CN)</title>
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