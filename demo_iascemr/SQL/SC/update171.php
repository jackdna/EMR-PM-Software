<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include("../../common/conDb.php");

$sql[] = "ALTER TABLE  `label_size` CHANGE  `large_top`  `large_top` DOUBLE( 4, 2 ) NOT NULL;";
$sql[] = "ALTER TABLE  `label_size` CHANGE  `large_bottom`  `large_bottom` DOUBLE( 4, 2 ) NOT NULL;";
$sql[] = "ALTER TABLE  `label_size` CHANGE  `large_inner`  `large_inner` DOUBLE( 4, 2 ) NOT NULL;";
$sql[] = "ALTER TABLE  `label_size` CHANGE  `small_top`  `small_top` DOUBLE( 4, 2 ) NOT NULL;";
$sql[] = "ALTER TABLE  `label_size` CHANGE  `small_bottom`  `small_bottom` DOUBLE( 4, 2 ) NOT NULL;";
$sql[] = "ALTER TABLE  `label_size` CHANGE  `small_inner`  `small_inner` DOUBLE( 4, 2 ) NOT NULL;";


foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 171 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 171 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 171</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>