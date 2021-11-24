<?php
require_once(dirname(__FILE__)."/../../../../config/globals.php");
//////////////////////Delete Xml FILES From Load_XML folder /////////////////////
$boolDeleted=false;
$thePath = $GLOBALS['fileroot'].'/data/'.constant('PRACTICE_PATH').'/scheduler_common/load_xml';
//delete all unused labels from custom label table
imw_query("delete from scheduler_custom_labels where labels_replaced='' and system_action=1 and l_show_text = l_text COLLATE latin1_swedish_ci");
//$dir = realpath($thePath);
$op = opendir($thePath);
while($file = readdir($op)){
	$extn = substr($file,-3);
	if(strtolower($extn) == 'sch'){
	
		$fileDate = explode('_',$file);
		unlink($thePath.'/'.$file);
		$boolDeleted=true;
	}
}
if($boolDeleted==true){
	print("XML files deleted successfully.");
}else{
	print("No XML file found.");
}
//////////////////////Delete Xml FILES From Load_XML folder/////////////////////
?>