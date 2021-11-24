<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");


$sql[]="CREATE TABLE `xml_outbound_interface` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL,
  `charge_list_id` int(11) NOT NULL,
  `charge_list_detail_id` int(11) NOT NULL,
  `xml_text` text NOT NULL,
  `created_for` varchar(25) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `sent` smallint(6) NOT NULL,
  `sent_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
)";

$sql[] = "ALTER TABLE `xml_outbound_interface` CHANGE `charge_list_detail_id` `charge_list_detail_id` VARCHAR(250) NOT NULL";
$sql[] = "ALTER TABLE `xml_outbound_interface`  ADD `uploaded_filename` VARCHAR(200) NOT NULL";

foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}


if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Release 8:<br>HL7 &gt; Update 4 Failed!</b>";
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Release 8:<br>HL7 &gt; Update 4 Success.</b>";
	$color = "green";	
}
?>
<html>
<head>
<title>Release 8 Updates 5 (XML Interface)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>