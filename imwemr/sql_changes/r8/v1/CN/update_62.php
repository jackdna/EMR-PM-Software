<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");
include($GLOBALS['srcdir']."/classes/SaveFile.php");

$oSv = new SaveFile();
$upd = $oSv->getUploadDirPath();

$ar=array();
$ar = array("lacrimalSys_od", "lacrimalSys_os");

foreach($ar as $k => $v){
	$fnm = $v; 
	$up = $upd."/exam_ext/xml/".$fnm.".xml";
	$back_up = $upd."/exam_ext/xml/".$fnm."_".date("m-d-Y-H-i-s").".xml";	
	
	if(!file_exists($up)){ echo("file ".$fnm.".xml not exists."); }
	else{	
	
		if(copy($up, $back_up)){
		
			$up_contents = file_get_contents($up);
			
			if($fnm == "lacrimalSys_od" ){
				if(strpos($up_contents, "elem_schirmerOd_date")===false){
					$up_contents = str_ireplace("</schirmertest>", "<sdt elem_name=\"elem_schirmerOd_date\" ></sdt></schirmertest>", $up_contents);
				}
			}else if( $fnm == "lacrimalSys_os"){
				if(strpos($up_contents, "elem_schirmerOs_date")===false){
					$up_contents = str_ireplace("</schirmertest>", "<sdt elem_name=\"elem_schirmerOs_date\" ></sdt></schirmertest>", $up_contents);
				}
			}			
			
			file_put_contents($up, $up_contents);
		}
		
	}
}

echo "Process done";

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update Change xml</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>