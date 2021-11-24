<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE  `iolink_preophealthquestionnaire` ADD  `signWitness1Id` INT( 11 ) NOT NULL ,
ADD  `signWitness1FirstName` VARCHAR( 255 ) NOT NULL ,
ADD  `signWitness1MiddleName` VARCHAR( 255 ) NOT NULL ,
ADD  `signWitness1LastName` VARCHAR( 255 ) NOT NULL ,
ADD  `signWitness1Status` VARCHAR( 5 ) NOT NULL ,
ADD  `signWitness1DateTime` DATETIME NOT NULL;";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 162 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 162 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 162</title>
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