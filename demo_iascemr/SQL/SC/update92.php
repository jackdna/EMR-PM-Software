<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include("../../common/conDb.php");

$sql1="CREATE TABLE procedureprofile (
  id int(11) NOT NULL AUTO_INCREMENT,
  procedureId int(11) NOT NULL DEFAULT '0',
  procedureName varchar(255) NOT NULL DEFAULT '',
  operativeTemplateId int(11) NOT NULL DEFAULT '0',
  instructionSheetId int(11) NOT NULL DEFAULT '0',
  consentTemplateId varchar(255) NOT NULL,
  cpt_id text NOT NULL,
  cpt_id_default text NOT NULL,
  dx_id text NOT NULL,
  dx_id_default text NOT NULL,
  dx_id_icd10 text NOT NULL,
  dx_id_default_icd10 text NOT NULL,
  preOpOrders text NOT NULL,
  postOpDrop text NOT NULL,
  otherPreOpOrders text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1; ";

imw_query($sql1)or $msg_info[] = imw_error();

if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 92 Failed! </b>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 92 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 92</title>
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