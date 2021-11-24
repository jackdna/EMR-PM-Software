<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sql[]="CREATE TABLE IF NOT EXISTS `xml_frames_collections` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `CollectionFramesMasterID` varchar(255) NOT NULL,
  `CollectionName` varchar(255) NOT NULL,
  `ManufacturerFramesMasterID` varchar(255) NOT NULL,
  `BrandFramesMasterID` varchar(255) NOT NULL,
  `Market` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1432 DEFAULT CHARSET=latin1;";

$sql[]="ALTER TABLE  `in_manufacturer_details` ADD `ManufacturerFramesMasterID` varchar(10) NOT NULL";
$sql[]="ALTER TABLE  `in_manufacturer_details` ADD `Market` varchar(10) NOT NULL";

$sql[]="ALTER TABLE  `in_frame_sources` ADD `ManufacturerFramesMasterID` varchar(10) NOT NULL";
$sql[]="ALTER TABLE  `in_frame_sources` ADD `BrandFramesMasterID` varchar(10) NOT NULL";
$sql[]="ALTER TABLE  `in_frame_sources` ADD `Market` varchar(10) NOT NULL";

$sql[]="ALTER TABLE  `in_frame_styles` ADD `BrandFramesMasterID` varchar(10) NOT NULL";
$sql[]="ALTER TABLE  `in_frame_styles` ADD `StyleFramesMasterID` varchar(10) NOT NULL";

$sql[]="ALTER TABLE  `in_frame_color` ADD  `import_id` BIGINT( 15 ) NOT NULL";	
	
$sql[]="ALTER TABLE  `in_options` CHANGE  `opt_type`  `opt_type` INT( 11 ) NOT NULL COMMENT  'Option Type (1=Usage, 2=Type, 3=Disinfecting, 4=Replenishment, 5=Packaging,6=Manufacturer,7=Brand,8=Color)'";
$sql[]="ALTER TABLE  `in_options` CHANGE  `module_id`  `module_id` INT( 11 ) NOT NULL COMMENT  'Option(1=Frames,2=Lenses,3=Contact Lenses,5=Supplies,6=Medicines,7=Accessories,0=Global,8=Frames Data Import Log)'";

/*Clean Table if Columns already there*/
$sql[] = "ALTER TABLE  `in_frame_styles` DROP  `BrandFramesMasterID`;";
$sql[] = "ALTER TABLE  `in_item` DROP  `ManufacturerFramesMasterID`;";
$sql[] = "ALTER TABLE  `in_item` DROP `Market`;";

$sql[] = "ALTER TABLE  `in_frame_styles` ADD `CollectionFramesMasterID` varchar(10) NOT NULL COMMENT 'Collection ID for the style linked to table xml_frames_collections'";
$sql[] = "ALTER TABLE  `in_frame_styles` ADD `StyleFramesMasterID` varchar(10) NOT NULL COMMENT 'Style Frame ID from Frames Data'";

$sql[] = "ALTER TABLE  `in_frame_types` ADD `CollectionFramesMasterID` varchar(10) NOT NULL COMMENT 'Collection ID for the type linked to table xml_frames_collections'";
$sql[] = "ALTER TABLE  `in_frame_types` ADD `StyleFramesMasterID` varchar(10) NOT NULL COMMENT 'Style Frame ID from Frames Data'";

$sql[] = "ALTER TABLE  `in_frame_shapes` ADD `CollectionFramesMasterID` varchar(10) NOT NULL COMMENT 'Collection ID for the shapes linked to table xml_frames_collections'";
$sql[] = "ALTER TABLE  `in_frame_shapes` ADD `StyleFramesMasterID` varchar(10) NOT NULL COMMENT 'Style Frame ID from Frames Data'";

$sql[] = "ALTER TABLE `in_item` ADD `CollectionFramesMasterID` varchar(10) NOT NULL COMMENT 'Collection ID for the Items linked to table xml_frames_collections'";
$sql[] = "ALTER TABLE `in_item` ADD `StyleFramesMasterID` varchar(10) NOT NULL COMMENT 'Style Frame ID from Frames Data'";
$sql[] = "ALTER TABLE `in_item` ADD `ConfigurationFramesMasterID` varchar(10) NOT NULL COMMENT 'configuration ID from Frames Data'";
$sql[] = "ALTER TABLE `in_item` Add `ConfigurationFPC` varchar(50) NOT NULL COMMENT 'Configuration ID from Frames Data'";


$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 67 run successfully...</div>';	
}

?>