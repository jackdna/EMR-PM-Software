<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$qry = array();

$qry[] = "ALTER TABLE `superbill` ADD `as_encounter_id` VARCHAR(30) NOT NULL COMMENT 'All scripts encounter ID (Unique for superbill and encoutner)'";
$qry[] = "ALTER TABLE `superbill` ADD `as_date_time` VARCHAR(30) NOT NULL COMMENT 'Date and time of saving encounter in Touch Works'";
$qry[] = "ALTER TABLE `procedureinfo` ADD `as_charge_id` VARCHAR(30) NOT NULL COMMENT 'Allscripts charge Id'";
$qry[] = "ALTER TABLE `icd10_data` ADD `as_id` VARCHAR(50) NOT NULL COMMENT 'Allscripts ID for ICD 10 code'";

$qry[] = "ALTER TABLE `patient_data`  ADD `External_MRN_4` VARCHAR(50) NOT NULL COMMENT 'TouchWorks MRN' AFTER `External_MRN_3`";
$qry[] = "ALTER TABLE `patient_data`  ADD `as_id` VARCHAR(50) NOT NULL COMMENT 'AllScript/TW Patient Id' AFTER `External_MRN_4`";

$qry[] = "ALTER TABLE `chart_master_table`  ADD `as_encounterId` VARCHAR(30) NOT NULL";
$qry[] = "ALTER TABLE `chart_master_table`  ADD `as_date_time` VARCHAR(30) NOT NULL";
$qry[] = "ALTER TABLE `chart_master_table`  ADD `as_document_ids` VARCHAR(255) NOT NULL COMMENT 'Allscript Document Ids'";

$qry[] = "ALTER TABLE `pt_problem_list` ADD COLUMN `as_id` VARCHAR(50) NOT NULL COMMENT 'Allscripts trans id' ";
$qry[] = "ALTER TABLE `pt_problem_list` ADD COLUMN `as_data` VARCHAR(255) NOT NULL COMMENT 'Additional Data from AllScripts' ";

$qry[] = "ALTER TABLE `allergies_data` ADD COLUMN `as_id` VARCHAR(50) NOT NULL COMMENT 'Allscripts Allergy Id' ";
$qry[] = "ALTER TABLE `allergies_data` ADD COLUMN `as_type` VARCHAR(10) NOT NULL COMMENT 'Allscripts Allergy type (MED | NONMED)' ";

$qry[] = "ALTER TABLE `lists` ADD COLUMN `as_id` VARCHAR(50) NOT NULL COMMENT 'Allscripts trans id' ";
$qry[] = "ALTER TABLE `lists` ADD COLUMN `as_data` VARCHAR(255) NOT NULL COMMENT 'Additional Data from AllScripts' ";

$qry[] = "ALTER TABLE `users` ADD COLUMN `as_username` VARCHAR(255) NOT NULL COMMENT 'EHR username for TouchWorks.'";
/*$qry[] = "ALTER TABLE `users` ADD COLUMN `as_password` VARCHAR(255) NOT NULL COMMENT 'EHR password for TouchWorks.'";*/
$qry[] = "ALTER TABLE `users` ADD COLUMN `as_entry_code` VARCHAR(50) NOT NULL COMMENT 'Provider entry code from TouchWorks'";


$qry[] = "CREATE TABLE `as_token_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tokenId` varchar(255) NOT NULL,
  `response` varchar(255) NOT NULL,
  `error` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

$qry[] = "CREATE TABLE  `as_server_info_log` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`timestamp` DATETIME NOT NULL,
	`info_data` TEXT NOT NULL,
	`license_key` VARCHAR( 40 ) NOT NULL,
	`user_id` BIGINT( 20 ) NOT NULL,
	`facility_id` INT( 11 ) NOT NULL
) ENGINE = MYISAM";

$qry[] = "CREATE TABLE `as_api_call_log` (
	`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	`action` VARCHAR(50) NOT NULL COMMENT 'Unity API call used', 
	`url_endpoint` VARCHAR(100) NOT NULL COMMENT 'API endpoint', 
	`parameters_sent` TEXT NOT NULL, 
	`response_code` CHAR(4) NOT NULL COMMENT 'http response code', 
	`response_data` TEXT NOT NULL, 
	`date_time` DATETIME NOT NULL COMMENT 'when API call triggered', 
	`facility_id` INT NOT NULL COMMENT 'logged in facility', 
	`user_id` INT NOT NULL COMMENT 'IMW user ID'
) ENGINE = MyISAM";


$qry[] = "CREATE TABLE  `as_credentials` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`appname` VARCHAR( 255 ) NOT NULL ,
	`username` VARCHAR( 255 ) NOT NULL ,
	`password` VARCHAR( 255 ) NOT NULL ,
	`url` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM";

/*`ehr_user` VARCHAR( 255 ) NOT NULL ,
`ehr_password` VARCHAR( 255 ) NOT NULL ,*/

$qry[] = "CREATE TABLE `as_dictionary` (
	`uid` int(11) NOT NULL AUTO_INCREMENT,
	`SiteDE_Active` varchar(255) NOT NULL,
	`EntryCode` varchar(255) NOT NULL,
	`SiteDE_Entryname` varchar(255) NOT NULL,
	`EntryMnemonic` varchar(255) NOT NULL,
	`SiteDE_Entrymnemonic` varchar(255) NOT NULL,
	`SiteDE_ID` varchar(255) NOT NULL,
	`ID` int(11) NOT NULL COMMENT 'Allscript ID',
	`Dictionary` varchar(40) NOT NULL COMMENT 'Dictionary table name',
	`SiteDE_EntryCode` varchar(255) NOT NULL,
	`EntryName` varchar(255) NOT NULL COMMENT 'Allscript item name',
	`Active` varchar(255) NOT NULL COMMENT 'Allscrtipt Status',
	PRIMARY KEY (`uid`),
	UNIQUE KEY `unique_record` (`ID`,`Dictionary`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";


$qry[] = "CREATE TABLE `as_problems` (
	`uid` int(11) NOT NULL AUTO_INCREMENT,
	`LowAge` varchar(10) NOT NULL,
	`ID` varchar(50) NOT NULL,
	`ICD9DiagnosisDE` varchar(255) NOT NULL,
	`DisplayName` varchar(255) NOT NULL,
	`ICD9DiagnosisCode` varchar(255) NOT NULL,
	`termAttributes` varchar(255) NOT NULL,
	`imo` varchar(20) NOT NULL,
	`source` varchar(20) NOT NULL,
	`IsBillableFLAG` varchar(10) NOT NULL,
	`Sorter` varchar(10) NOT NULL,
	`InjuryTypeReqFLAG` varchar(10) NOT NULL,
	`EntryCode` varchar(20) NOT NULL,
	`HighAge` varchar(10) NOT NULL,
	`ICD10DiagnosisText` varchar(255) NOT NULL,
	`title` varchar(255) NOT NULL,
	`IsPreferred` varchar(10) NOT NULL,
	`Sex` varchar(20) NOT NULL,
	`TermUID` varchar(255) NOT NULL,
	`detailflag` varchar(10) NOT NULL,
	`ICD10DiagnosisCode` varchar(255) NOT NULL,
	`code` varchar(50) NOT NULL,
	`snomed` varchar(20) NOT NULL,
	PRIMARY KEY (`uid`),
	UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";

$qry[] = "ALTER TABLE `as_token_log` ADD COLUMN `entry_date_time` DATETIME NOT NULL COMMENT 'Date and time of creating log entry'";
$qry[] = "ALTER TABLE `as_token_log` ADD COLUMN `update_date_time` DATETIME NOT NULL COMMENT 'Date and time of Updating log entry'";

$qry[] = "ALTER TABLE `as_credentials` ADD COLUMN `ubq_appname` VARCHAR(255) NOT NULL";
$qry[] = "ALTER TABLE `as_credentials` ADD COLUMN `ubq_username` VARCHAR(255) NOT NULL";
$qry[] = "ALTER TABLE `as_credentials` ADD COLUMN `ubq_password` VARCHAR(255) NOT NULL";
$qry[] = "ALTER TABLE `as_credentials` ADD COLUMN `ubq_url` VARCHAR(255) NOT NULL";
$qry[] = "ALTER TABLE `as_credentials` ADD COLUMN `ubq_status` BOOL NOT NULL";

$qry[] = "ALTER TABLE `as_api_call_log` CHANGE `response_data` `response_data` LONGTEXT NOT NULL";

/*Drop Columns redendent columns*/
$qry[] = "ALTER TABLE `as_credentials` DROP COLUMN `ehr_user`";
$qry[] = "ALTER TABLE `as_credentials` DROP COLUMN `ehr_password`";
$qry[] = "ALTER TABLE `users` DROP COLUMN `as_password`";

foreach ($qry  as $sql){
	imw_query($sql) or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>TouchWorks Update 1 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>TouchWorks Update 1 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>