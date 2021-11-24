<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
 
$q = "CREATE TABLE patient_goals (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  patient_id int(11) NOT NULL,
  form_id int(11) NOT NULL,
  goal_set varchar(250) NOT NULL,
  loinc_code varchar(15) NOT NULL,
  goal_data varchar(250) NOT NULL COMMENT 'can be plain text or hyphenated range',
  goal_data_type varchar(25) NOT NULL COMMENT 'plain_text or range',
  gloal_data_type_unit varchar(25) NOT NULL COMMENT 'if goal dataype is range, then put unit here',
  operator_id int(11) NOT NULL,
  goal_date date NOT NULL,
  delete_status smallint(6) NOT NULL DEFAULT '0'
) ENGINE=MyISAM;";
$r = imw_query($q) or $msg_info[]=imw_error();

if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 11  run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 11  run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 11 (CN)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>