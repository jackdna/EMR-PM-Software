<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

// Update to create a Immunization Manufacturer table and insert records from XML file

$msg_info = array();

$qry = "CREATE TABLE IF NOT EXISTS `immunization_manufacturer` (
  `manufacturer_id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `MVX_CODE` varchar(255) NOT NULL,
  `manufacturer_name` text NOT NULL,
  `notes` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `last_updated_date` varchar(255) NOT NULL,
  `highlight` varchar(255) NOT NULL   
) ENGINE=MyISAM COMMENT='To store manufacturer details' AUTO_INCREMENT=1 ;";
imw_query($qry) or $msg_info[]=imw_error();

$qry = "Select manufacturer_id From immunization_manufacturer";
$sql = imw_query($qry) or $msg_info[]=imw_error();
$cnt = imw_num_rows($sql);

if( $cnt == 0) {
	$xml_file = $GLOBALS['fileroot']."/interface/Medical_history/MVXxml.xml";
	if(file_exists($xml_file)){
		$xml=simplexml_load_file($xml_file);
		foreach($xml->MVXInfo as $MVX_companies){
			$comp_MVXCode=$MVX_companies->MVX_CODE;
			$comp_name=$MVX_companies->ManufacturerName;
			$comp_notes=$MVX_companies->Notes;
			$comp_status=$MVX_companies->Status;
			$comp_last_update=$MVX_companies->LastUpdated;
			$qryInsert="INSERT INTO immunization_manufacturer
						set MVX_CODE='".$comp_MVXCode."',manufacturer_name='".$comp_name."',notes='".$comp_notes."',
							status='".$comp_status."',last_updated_date='".$comp_last_update."'";
			$resInsert=imw_query($qryInsert)or $msg_info[]=imw_error();				
		}
	}else{$msg_info[]="Qry Fail: XML file not exist"; }
}
if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 8 run FAILED!</b><br>';
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 8 run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 8 (MH)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>