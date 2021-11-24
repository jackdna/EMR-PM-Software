<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

//Create Backup table of
$tbl = "post_nurse_alderate";
$bkTbl = $tbl.'_'.date('Y_m_d');

$sql1="CREATE  TABLE ".$bkTbl." LIKE ".$tbl;
$res=imw_query($sql1) or $msg_info[] = imw_error();
if( $res ) {
	$sql1="INSERT INTO ".$bkTbl." (SELECT *  FROM ".$tbl.");";
	imw_query($sql1)or $msg_info[] = imw_error();
}

$sql1="CREATE TABLE `post_nurse_alderate_data` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`confirmation_id` int(11) NOT NULL,
	`points_detail` text NOT NULL,
	`is_deleted` tinyint(1) NOT NULL,
	`created_on` datetime NOT NULL,
	`created_by` int(11) NOT NULL,
	`modified_on` datetime NOT NULL,
	`modified_by` int(11) NOT NULL,
	PRIMARY KEY (`id`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="INSERT INTO ".$tbl."_data (confirmation_id, points_detail) (SELECT confirmation_id, points_detail FROM ".$tbl.");"; 
imw_query($sql1)or $msg_info[] = $sql1.imw_error();

$sql1="ALTER TABLE `post_nurse_alderate` DROP `points_detail` ;"; 
imw_query($sql1)or $msg_info[] = imw_error();

if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 180 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 180 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 180</title>
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