<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "CREATE TABLE predefine_history_physical (
  id int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(1024) COLLATE latin1_general_ci NOT NULL,
  deleted tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";

$sql[] = "CREATE TABLE history_physical_ques (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  confirmation_id int(11) NOT NULL DEFAULT '0',
  patient_id int(11) NOT NULL DEFAULT '0',
  ques varchar(1024) NOT NULL,
  ques_status varchar(5) NOT NULL,
  ques_desc text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 156 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 156 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 156</title>
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