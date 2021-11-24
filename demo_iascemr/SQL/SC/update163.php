<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "CREATE TABLE `predefine_mac_regional_questions` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `question` varchar(1024) NOT NULL,
  `f_type` tinyint(2) NOT NULL,
  `d_type` tinyint(2) NOT NULL,
  `options` text NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `sort_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  `modified_by` int(11) NOT NULL
) ENGINE=MyISAM;";

$sql[] = "CREATE TABLE `patient_mac_regional_questions` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `confirmation_id` int(11) NOT NULL,
  `question` varchar(1024) NOT NULL,
  `f_type` tinyint(2) NOT NULL,
  `d_type` tinyint(2) NOT NULL,
  `list_options` text NOT NULL,
  `answer` text NOT NULL,
  `created_on` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  `modified_by` int(11) NOT NULL
) ENGINE=MyISAM;";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 163 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 163 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 163</title>
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