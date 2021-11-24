<?php

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');//to get save location
require_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');//to get save location

if($_REQUEST['upload'])
{
	$strToTime=strtotime(date('Y-m-d H:i:s'));
	$imgName= $_SESSION['patient']."_".$strToTime."_img.png";
	echo "source=".$s_img 			= $_REQUEST['img'];
	echo "destination=".$d_img 			= 'screenshots/'.$imgName;
	echo "result=". copy($s_img,$d_img);
	die('images generated');
	
}
else{
	echo "<div style='text-align:center'><img src='$_REQUEST[id]'></div>";
}
?>