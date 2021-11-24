<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../../common/conDb.php");

$sql1="ALTER TABLE `localanesthesiarecord` CHANGE `chartNotesReviewed` `chartNotesReviewed` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `anesthesia_profile_tbl` CHANGE `chartNotesReviewed` `chartNotesReviewed` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;"; 
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$message = "<br><br><b>Update 2 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";
}
else
{	
	$message = "<br><br><b>Update 2 Success.</b><br>";
	$color = "green";			
}

?>

<html>
<head>
<title>Update 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo $message;?></font>
<?php
@imw_close();
}
?> 
</body>
</html>