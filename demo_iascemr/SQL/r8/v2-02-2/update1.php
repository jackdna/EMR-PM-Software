<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../../common/conDb.php");

$sql1="ALTER TABLE `post_nurse_alderate_data` ADD `scoring_comments` TEXT NOT NULL;"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `post_nurse_alderate` ADD `version_num` INT( 11 ) NOT NULL, ADD `version_date_time` DATETIME NOT NULL, ADD  INDEX version_num(version_num) ;"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1=" UPDATE `post_nurse_alderate` Set version_num = 1, version_date_time = '".date('Y-m-d H:i:s')."' 
				Where (form_status = 'completed' || form_status = 'not completed') And version_num = 0 ";
imw_query($sql1)or $msg_info[] = imw_error();


if(imw_error() || count($msg_info)>0)
{
	$message = "<br><br><b>Update 1 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";
}
else
{	
	$message = "<br><br><b>Update 1 Success.</b><br>";
	$color = "green";			
}

?>

<html>
<head>
<title>Update 1</title>
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