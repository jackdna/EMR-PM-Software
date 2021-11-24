<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include("../../common/conDb.php");
$sql1="CREATE TABLE facility_tbl (
  `fac_id` int(11) NOT NULL AUTO_INCREMENT,
  `fac_name` varchar(250) NOT NULL,
  `fac_npi` varchar(150) NOT NULL,
  `fac_federal_ein` varchar(150) NOT NULL,
  `fac_address1` varchar(250) NOT NULL,
  `fac_address2` varchar(250) NOT NULL,
  `fac_city` varchar(250) NOT NULL,
  `fac_state` varchar(250) NOT NULL,
  `fac_zip` varchar(50) NOT NULL,
  `fac_contact_name` varchar(150) NOT NULL,
  `fac_contact_phone` varchar(50) NOT NULL,
  `fac_contact_fax` varchar(50) NOT NULL,
  `fac_contact_email` varchar(250) NOT NULL,
  `fac_idoc_link_id` varchar(255) NOT NULL,
  `fac_entered_date` date NOT NULL,
  `fac_entered_time` time NOT NULL,
  `fac_entered_by` int(11) NOT NULL,
  `fac_modified_date` date NOT NULL,
  `fac_modified_time` time NOT NULL,
  `fac_modified_by` int(11) NOT NULL,
  `fac_del_status` tinyint(2) NOT NULL,
  `fac_del_date` date NOT NULL,
  `fac_del_time` time NOT NULL,
  `fac_del_by` int(11) NOT NULL,
  `fac_head_quater` int(11) NOT NULL,
  PRIMARY KEY (`fac_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `stub_tbl` ADD `iasc_facility_id` INT( 11 ) NOT NULL  ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE surgerycenter ADD documentsExpireDays INT( 11 ) NOT NULL DEFAULT '0' AFTER acceptAssignment ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE surgerycenter SET documentsExpireDays = IF(documentsExpireDays = '0','90',documentsExpireDays) WHERE surgeryCenterId = '1' ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `scan_upload_tbl` ADD `scan_upload_save_date_time` DATETIME NOT NULL ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="CREATE TABLE scan_upload_tbl_bak_".date("d_m_Y")." AS (SELECT * FROM scan_upload_tbl)";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="CREATE TABLE iolink_scan_consent_bak_".date("d_m_Y")." AS (SELECT * FROM iolink_scan_consent)";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE iolink_scan_consent isc, patient_in_waiting_tbl piw 
		SET isc.scan_save_date_time = IF(isc.scan_save_date_time='0000-00-00 00:00:00',piw.dos,isc.scan_save_date_time)
		WHERE isc.patient_in_waiting_id = piw.patient_in_waiting_id 
		AND isc.patient_in_waiting_id !='0' AND isc.patient_in_waiting_id !=''
		";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE scan_upload_tbl sut, iolink_scan_consent isc 
		SET sut.scan_upload_save_date_time = IF(isc.scan_save_date_time='0000-00-00 00:00:00',sut.scan_upload_save_date_time,isc.scan_save_date_time)  
		WHERE sut.iolink_scan_consent_id = isc.scan_consent_id 
		AND sut.iolink_scan_consent_id !='0' AND sut.iolink_scan_consent_id !=''
		";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE scan_upload_tbl 
		SET scan_upload_save_date_time = IF(scan_upload_save_date_time='0000-00-00 00:00:00',dosOfScan,scan_upload_save_date_time)  
		WHERE dosOfScan != '0000-00-00'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="UPDATE scan_upload_tbl sut, patientconfirmation pc 
		SET sut.scan_upload_save_date_time = IF(sut.scan_upload_save_date_time='0000-00-00 00:00:00',pc.dos,sut.scan_upload_save_date_time),
		sut.dosOfScan = IF(sut.dosOfScan='0000-00-00',pc.dos,sut.dosOfScan)    
		WHERE sut.confirmation_id = pc.patientConfirmationId 
		AND sut.confirmation_id !='0' AND sut.confirmation_id !=''
		";
imw_query($sql1)or $msg_info[] = imw_error();

if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 93 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 93 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 93</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>