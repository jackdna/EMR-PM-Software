<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include("../../common/conDb.php");
// update to add fields to store Start/Stop time in superbill table for anesthesia type bill
$msg_info = array();
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_label TEXT NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_label TEXT NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_1 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_2 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_3 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_4 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_5 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_6 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_7 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_8 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_9 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_10 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_11 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_12 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_13 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_14 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_15 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_16 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_17 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_18 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_19 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank3_20 VARCHAR(255) NOT NULL;";
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_1 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_2 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_3 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_4 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_5 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_6 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_7 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_8 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_9 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_10 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_11 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_12 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_13 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_14 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_15 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_16 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_17 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_18 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_19 VARCHAR(255) NOT NULL;"; 
$sql[] = " ALTER TABLE localanesthesiarecord ADD blank4_20 VARCHAR(255) NOT NULL;"; 


foreach($sql as $qry){
	imw_query($qry) or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 178 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 178 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 178</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
imw_close();
}
?> 
</body>
</html>