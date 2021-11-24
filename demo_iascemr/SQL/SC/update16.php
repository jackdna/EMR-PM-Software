<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$sql = "
CREATE TABLE `scan_documents_user` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `dosOfScan` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `save_date_time` datetime NOT NULL,
  UNIQUE KEY `document_id` (`document_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;
";
$row = imw_query($sql) or $msg_info[] = imw_error();

$sql = "
CREATE TABLE `scan_upload_tbl_user` (
  `scan_upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `image_type` varchar(60) COLLATE latin1_general_ci NOT NULL,
  `document_type` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `img_content` longblob NOT NULL,
  `document_name` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `document_size` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `form_name` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `scan_upload_form_id` int(11) NOT NULL DEFAULT '0',
  `document_id` int(11) NOT NULL DEFAULT '0',
  `parent_sub_doc_id` int(11) NOT NULL,
  `pdfFilePath` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `iolink_scan_consent_id` bigint(11) NOT NULL,
  `dosOfScan` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `save_date_time` datetime NOT NULL,
  PRIMARY KEY (`scan_upload_id`),
  KEY `document_id` (`document_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;
";
$row = imw_query($sql) or $msg_info[] = imw_error();


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 16 run OK";

//line 12323
?>

<html>
<head>
<title>Mysql Updates For Query Optimization</title>
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







