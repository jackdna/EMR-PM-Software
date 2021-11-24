<?php
$ignoreAuth = true;
set_time_limit(0);
include_once(dirname(__FILE__)."/../../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");

$oSv = new SaveFile();
$upd = $oSv->getUploadDirPath();

$_GET["op"]="la3";//

$ar=array();
$ar = array("lids_od", "lids_os");
if($_GET["op"]=="lens"){
	$ar = array("lens_od", "lens_os");
}else if($_GET["op"]=="la"){
	$ar = array("lids_od", "lids_os", "lesion_od", "lesion_os");
}else if($_GET["op"]=="la2" || $_GET["op"]=="la3"){	
	$ar = array("lids_od", "lids_os");
}else{
	exit("please specify operation: ?op=lens OR ?op=la ");
}

foreach($ar as $k => $v){
	$fnm = $v; 
	$up = $upd."/exam_ext/xml/".$fnm.".xml";
	$back_up = $upd."/exam_ext/xml/".$fnm."_".date("m-d-Y-H-i-s").".xml";
	
	
	if(!file_exists($up)){ echo("file ".$fnm.".xml not exists."); }
	else{	
		
		if(copy($up, $back_up)){
		
			$up_contents = file_get_contents($up);
			
			if($fnm == "lids_od" || $fnm == "lids_os" || $fnm == "lesion_od" || $fnm == "lesion_os"){
				
				if($fnm == "lids_od" || $fnm == "lids_os"){
					if($_GET["op"]=="la"){
						$ar_search = array("Lacrimal Gland Prolapse"=>"", 
									"Degree improvement with lid taped"=>"",
									"% improvement with lids taped"=>"",
									"Punctal Ectropion"=>"",
									"Punctal Stenosis"=>"",
									"Cicatricial Skin Changes"=>"",
									"Lagophthalmos"=>"Lagophthalmus",
									"Central Fat Prolapse"=>"",
									"Lateral Fat Prolapse"=>"",
									"Nasojugal Fold"=>"");
					}else if($_GET["op"]=="la2"){
						$ar_search = array("Advanced Plastics"=>"advanced_plastics");
					}else if($_GET["op"]=="la3"){
						$ar_search = array();
						if(strpos($up_contents, "cmnts examname=")===false){
						if($fnm == "lids_od"){	$up_contents = str_ireplace(array("</upper_lids>", "</lower_lids>"), array("<cmnts examname=\"UL Comments\"><text elem_name=\"lidsAdCmntUprOd\"></text></cmnts></upper_lids>", "<cmnts examname=\"LL Comments\"><text elem_name=\"lidsAdCmntLwrOd\"></text></cmnts></lower_lids>"), $up_contents); }
						else if($fnm == "lids_os"){ $up_contents = str_ireplace(array("</upper_lids>", "</lower_lids>"), array("<cmnts examname=\"UL Comments\"><text elem_name=\"lidsAdCmntUprOs\"></text></cmnts></upper_lids>", "<cmnts examname=\"LL Comments\"><text elem_name=\"lidsAdCmntLwrOs\"></text></cmnts></lower_lids>"), $up_contents); }			
						}	
					}
				}else if($fnm == "lesion_od" || $fnm == "lesion_os"){
					$ar_search = array("Seborrheic Keratosis"=>"", "Intradermal Nevus"=>"");	
				}
				
				//
				if(count($ar_search)>0){
				foreach($ar_search as $k => $v){
					$resp = $k;  $srch = $k;
					if(!empty($v)){ $srch = $v;  }			
					
					//echo "<br/>".$resp." - ".$srch;
					if($_GET["op"]=="la2"){
						$up_contents = str_ireplace("tabname=\"".$srch."\"", "tabname=\"".$resp."\"", $up_contents);
					}else{
						$up_contents = str_ireplace("examname=\"".$srch."\"", "examname=\"".$resp."\"", $up_contents);
					}	
				}
				}
				
			}else if($fnm == "lens_od" || $fnm == "lens_os"){
				if(strpos($up_contents, "symfony elem_name")===false){
				if($fnm == "lens_od"){	$up_contents = str_ireplace("</Ve_CapsuleOd>", "<symfony elem_name=\"elem_symfonyOd_pciol\"></symfony></Ve_CapsuleOd>", $up_contents); }
				else if($fnm == "lens_os"){ $up_contents = str_ireplace("</Ve_CapsuleOs>", "<symfony elem_name=\"elem_symfonyOs_pciol\"></symfony></Ve_CapsuleOs>", $up_contents); }			
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