<?php
$ignoreAuth = true;
set_time_limit(0);
include_once(dirname(__FILE__)."/../../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");

$oSv = new SaveFile();
$upd = $oSv->getUploadDirPath();

$ar=array();
$ar = array("cd_od", "cd_os");


foreach($ar as $k => $v){
	$fnm = $v; 
	$up = $upd."/exam_ext/xml/".$fnm.".xml";
	$back_up = $upd."/exam_ext/xml/".$fnm."_".date("m-d-Y-H-i-s").".xml";
	
	
	if(!file_exists($up)){ echo("file ".$fnm.".xml not exists."); }
	else{	
		
		if(copy($up, $back_up)){
		
			$up_contents = file_get_contents($up);
			
			if($fnm == "cd_od" || $fnm == "cd_os"){
				if(strpos($up_contents, "Optic Nerve Hmg")!==false){
					$up_contents = str_ireplace("examname=\"Optic Nerve Hmg\"", "examname=\"Optic Nerve\"", $up_contents);
				
				}	
			}
			
			//echo "<br/><xmp>".$up_contents."</xmp>";
			
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