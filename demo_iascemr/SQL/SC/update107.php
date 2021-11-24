<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");


$sql1="ALTER TABLE  `surgerycenter` 
ADD  `vital_sign_oproom` VARCHAR( 5 ) NOT NULL ,
ADD  `vital_sign_macAnes` VARCHAR( 5 ) NOT NULL ,
ADD  `vital_sign_genAnes` VARCHAR( 5 ) NOT NULL ,
ADD  `vital_sign_transferFollowup` VARCHAR( 5 ) NOT NULL ,
ADD  `peer_review` VARCHAR( 5 ) NOT NULL
"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `vital_sign_grid` ADD  `rr` VARCHAR( 32 ) NOT NULL AFTER  `pulse` "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `operatingroomrecords` ADD  `vitalSignGridStatus` TINYINT( 2 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `localanesthesiarecord` ADD  `vitalSignGridStatus` TINYINT( 2 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `transfer_followups` ADD  `vitalSignGridStatus` TINYINT( 2 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `genanesthesiarecord` ADD  `vitalSignGridStatus` TINYINT( 2 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `healthquestioner` ADD  `isDefault` int( 11 ) NOT NULL "; 
imw_query($sql1)or $msg_info[] = imw_error();


$chkQry	=	"Select operatingRoomRecordsId From operatingroomrecords Where vitalSignGridStatus = '1' LIMIT 0,1 ";
$chkSql	=	imw_query($chkQry)or $msg_info[] = imw_error();
$chkCnt	=	imw_num_rows($chkSql);

if($chkCnt > 0)
{	
		$msg_info[]	=	"Update already run ";
}
else
{
		$sql1="UPDATE genanesthesiarecord SET vitalSignGridStatus = 1 WHERE (form_status = 'completed' OR form_status = 'not completed' )";
		imw_query($sql1)or $msg_info[] = imw_error();
		
		$sql1="UPDATE transfer_followups SET vitalSignGridStatus = 1 WHERE (form_status = 'completed' OR form_status = 'not completed' )";
		imw_query($sql1)or $msg_info[] = imw_error();
		
		$sql1="UPDATE localanesthesiarecord SET vitalSignGridStatus = 1 WHERE (form_status = 'completed' OR form_status = 'not completed' )";
		imw_query($sql1)or $msg_info[] = imw_error();
		
		$sql1="UPDATE operatingroomrecords SET vitalSignGridStatus = 1 WHERE (form_status = 'completed' OR form_status = 'not completed' )";
		imw_query($sql1)or $msg_info[] = imw_error();
		
		$sql1="UPDATE surgerycenter SET vital_sign_oproom = 'Y', vital_sign_macAnes = 'Y', vital_sign_genAnes = 'Y', vital_sign_transferFollowup = 'Y', peer_review = 'N' Where surgeryCenterId = '1' ";
		imw_query($sql1)or $msg_info[] = imw_error();
		
}


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 107 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 107 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 107</title>
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