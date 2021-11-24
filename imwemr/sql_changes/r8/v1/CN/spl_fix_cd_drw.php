<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");
include($GLOBALS['srcdir']."/classes/SaveFile.php");

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors",1);

/**
erase_obj_pixel(oPrvImg,0,160, 410);
erase_obj_pixel(oPrvImg,0,180, 410);
erase_obj_pixel(oPrvImg,0,200, 410);

erase_obj_pixel(oPrvImg,0,510, 420);
erase_obj_pixel(oPrvImg,0,530, 420);
erase_obj_pixel(oPrvImg,0,550, 420);


--
var x = 150;
var y = 420;	

var x = 500;
var y = 420;


"text":"C:D: 0.1"

--
**/



function set_cd_vals($f_fixid, $f_cdd, $f_cds){
	global $oSv, $upd;
	
	

	$ar_cl_1 = array("0"=>array("150", "422"), 
				"1"=>array("160", "422"),
				"2"=>array("180", "422"),
				"3"=>array("200", "422")
					
			);
			
	$ar_cl_2 = array( 
			  "0"=>array("500", "422"),	
			  "1"=>array("510", "422"),
			  "2"=>array("530", "422"),
			  "3"=>array("550", "422")			  
			);		
			
	$len_cl_1 = count($ar_cl_1);
	$len_cl_2 = count($ar_cl_2);	
	
	if(!empty($f_fixid) && (!empty($f_cdd) || !empty($f_cds))){
		$sql = "SELECT drawing_image_path, id FROM ".IMEDIC_SCAN_DB.".idoc_drawing WHERE id='".$f_fixid."'  ";
		$row = sqlQuery($sql);
		if($row!=false){
			$drawing_image_path  = $row["drawing_image_path"];
			
			if(!empty($drawing_image_path)){
				$drawing_image_path_tmp = $upd.$drawing_image_path;
				
				if(file_exists($drawing_image_path_tmp)){
				
					//back
					copy($drawing_image_path_tmp, $drawing_image_path_tmp.".4cdr".time().".bak");
					//big back
					$drawing_image_path_tmp_b = str_replace(".png", "_b.png", $drawing_image_path_tmp);
					if(file_exists($drawing_image_path_tmp_b)){ copy($drawing_image_path_tmp_b, $drawing_image_path_tmp_b.".4cdr".time().".bak"); }
					
					//small back
					$drawing_image_path_tmp_s = str_replace(".png", "_s.png", $drawing_image_path_tmp);
					if(file_exists($drawing_image_path_tmp_s)){ copy($drawing_image_path_tmp_s, $drawing_image_path_tmp_s.".4cdr".time().".bak"); }
					//--
					
					//--
					// generate empty image : below order is imp.
					$image = imagecreatefrompng($drawing_image_path_tmp);
					imagecolortransparent($image, imagecolorat($image,0,0));	
					if(imageistruecolor($image))
					{
						imagetruecolortopalette($image, true, 256);						
					}				
					
					imagealphablending($image, false);
					imagesavealpha($image, true);
					
					// define transparent
					//$transparent = imagecolorallocatealpha($image, 0, 0, 0, 0);					
					//if($transparent===false){ 
					//	$transparent = imagecolorallocate($image, 255, 255, 255);
					//	imagecolortransparent($image, $transparent);
					//}
					
					//black
					$ColorText = imagecolorallocate($image, 0, 0, 0);
					
					//
					$bgWhite = imagecolorallocate($image, 255,255,255);
					$transparent = imagecolorat($image, 1, 1);
					//$font_file = '/fonts/PT_SANS-WEB-REGULAR.TTF';
					$font_file = '/fonts/OPENSANS-REGULAR_0.TTF';
					
					if(!empty($f_cdd)){
						
						for($i=0; $i<$len_cl_1; $i++){
							if(!empty($ar_cl_1[$i][0]) && !empty($ar_cl_1[$i][1])){
								$x = $ar_cl_1[$i][0]; $y = $ar_cl_1[$i][1];
								$x1 = $x - 10; $y1 = $y - 12; $x2 = $x + 50; $y2 = $y + 15;
								imagefilledrectangle($image, $x1, $y1, $x2, $y2, $transparent);	
							}
						}
						
						// create the transparent area
						$x = 160; $y = 442;
						$x1 = $x - 10; $y1 = $y - 10; $x2 = $x + 100; $y2 = $y + 5;
						imagefilledrectangle($image, $x1, $y1-10, $x2-50, $y2-10, $bgWhite);
						imagettftext($image, 10, 0, $x1, $y1, $ColorText, $GLOBALS['srcdir'].$font_file, "C:D: ".$f_cdd);						
					}
					
					if(!empty($f_cds)){						
						for($i=0; $i<$len_cl_2; $i++){
							if(!empty($ar_cl_2[$i][0]) && !empty($ar_cl_2[$i][1])){
								$x = $ar_cl_2[$i][0]; $y = $ar_cl_2[$i][1];
								$x1 = $x - 10; $y1 = $y - 12; $x2 = $x + 50; $y2 = $y + 15;								
								imagefilledrectangle($image, $x1, $y1, $x2, $y2, $transparent);
							}
						}						
					
						// create the transparent area
						$x = 510; $y = 442;
						$x1 = $x - 10; $y1 = $y - 10; $x2 = $x + 100; $y2 = $y + 5;
						imagefilledrectangle($image, $x1, $y1-10, $x2-50, $y2-10, $bgWhite);
						imagettftext($image, 10, 0, $x1, $y1, $ColorText, $GLOBALS['srcdir'].$font_file, "C:D: ".$f_cds);
					}
					
					// save image
					imagepng($image, $drawing_image_path_tmp);
					imagedestroy($image);
					
					//create w/ background
					$backImg = $GLOBALS['incdir']."/../library/images/idoc_draw/la.jpg";
					if(empty($backImg) == false){
						//$bakImgResource = imagecreatefromstring(file_get_contents($backImg));
						$bakImgResource = (strpos($backImg,".png")!==false) ? imagecreatefrompng($backImg): imagecreatefromjpeg($backImg); 
						$canvasImgResource = imagecreatefrompng($drawing_image_path_tmp);										
						imagecopy($bakImgResource, $canvasImgResource, 0, 0, 0, 0, 730, 465);
						//imagepng($bakImgResource, $drawingFilePath);
						imagepng($bakImgResource, $drawing_image_path_tmp_b);
						
						//s
						//$imageResized = imagescale($bakImgResource, 265, 169);						
						//$src = imagecreatefromjpeg($bakImgResource);
						$imageResized = imagecreatetruecolor(265, 169);
						imagecopyresampled($imageResized, $bakImgResource, 0, 0, 0, 0, 265, 169, 730, 465);
						imagepng($imageResized, $drawing_image_path_tmp_s);
						
						imagedestroy($imageResized);
						imagedestroy($bakImgResource);
					}
					
					//--
				}
			}
		}
	}
}

//---

$lm = 100;
$st = !empty($_GET["st"]) ? $_GET["st"] : 0; 

$phrase_pt="";
if(!empty($_GET["pt"])){
	$phrase_pt.=" AND c1.patient_id='".$_GET["pt"]."' ";
}
//
if(!empty($_GET["frm"])){
	$phrase_pt.=" AND c1.form_id='".$_GET["frm"]."' ";
}
//--


$oSv = new SaveFile();
$upd = $oSv->getUploadDirPath();

if(isset($_POST["chk_nm"]) && count($_POST["chk_nm"]) > 0){	

	foreach($_POST["chk_nm"] as $kg => $av){
	$av = trim($av);
	if(!empty($av)){
		$ar = explode(",", $av);
		$fid = trim($ar[0]);
		$cdd = trim($ar[1]);
		$cds = trim($ar[2]);	
	
		 
	
		set_cd_vals($fid, $cdd, $cds);
	}
	}
	
	$qpt="";
	if(!empty($_GET["pt"])){  $qpt="&pt=".$_GET["pt"]; }
	if(!empty($_GET["frm"])){  $qpt="&frm=".$_GET["frm"]; }
	
	header("Location: ?st=".$st.$qpt);
	exit();
}
//--

$sql = "SELECT c1.optic_id, c1.form_id, c1.patient_id, 
			od_text, os_text, 
			cd_val_od, cd_val_os, 
			drawing_image_path, drw_data_json, c3.id as idoc_id
		FROM chart_optic c1 
		INNER JOIN chart_rv c2 ON c1.form_id = c2.form_id
		INNER JOIN ".IMEDIC_SCAN_DB.".idoc_drawing c3 ON c3.patient_form_id = c1.form_id
	WHERE (od_text!='' OR os_text!='' OR cd_val_od!='' OR cd_val_os!='')  
	AND ((c3.drawing_image_path != '' OR drw_data_json!='') AND c3.drawing_for='Fundus_Exam' AND deletedby='0' ".$phrase_pt." )	
	ORDER BY c1.optic_id 	
	LIMIT $st, $lm  
";
$res = sqlStatement($sql);
for($i=1; $row=sqlFetchArray($res);$i++){
	$cd_od=$cd_os="";
	
	if(!empty($row["cd_val_od"]) || !empty($row["cd_val_os"])){
		$cd_od=$row["cd_val_od"];
		$cd_os=$row["cd_val_os"];
	}else if(!empty($row["od_text"]) || !empty($row["os_text"])){
		$cd_od=$row["od_text"];
		$cd_os=$row["os_text"];
	}
	
	$drawing_image_path = $drawing_image_path_w="";
	$drawing_image_path  = $row["drawing_image_path"];
	
	
	if(!empty($drawing_image_path)){
		$drawing_image_path_tmp = $upd.$drawing_image_path;
		
		if(file_exists($drawing_image_path_tmp)){
			$drawing_image_path_w =  $oSv->getFilePath($drawing_image_path, 'w');
		}		
	}
	
	if((!empty($cd_od) || !empty($cd_os)) && !empty($row["idoc_id"]) && !empty($drawing_image_path_w)){
		$echo .= "<tr><td><input type=\"checkbox\" name=\"chk_nm[]\" value=\"".$row["idoc_id"].", ".$cd_od.", ".$cd_os."\"></td>
				<td>".$cd_od."</td><td>".$cd_os."</td><td style=\"width:600px; height:100px;\" >
				<div style=\"width:600px; height:100px; overflow:auto;  position:relative; padding:0px; margin:0px;\" >
				<img src=\"".$drawing_image_path_w."\" alt=\"".$row["form_id"]."\" style=\"position:absolute; top:-400px; \"  />
				</div>
			</td><td title=\"OPTIC:".$row["optic_id"].", PatID:".$row["patient_id"].", FormId:".$row["form_id"]."\"><a href=\"javascript:void()\" onclick=\"fix('".$row["idoc_id"]."', '".$cd_od."', '".$cd_os."' )\">FIX</a> </td></tr>";
	}
}

if(!empty($echo)){
	$echo = "<form name=\"frmfx\" action=\"\" method=\"post\"><table width=\"100%\" border=\"1\"><tr><th>SR</th><th>CD OD</th><th>CD OS</th><th>Draw</th><th>FIX</th></tr>".$echo."</table></form><br/><br/>"; 
}

$echo .= "<a href=\"javascript:void();\" onclick=\"fix_all()\">Fix All</a>";

if(!empty($st)){
	$pp = $st - $lm; if($pp<=0){ $pp=0; }	
	$echo .= "<a href='?st=".$pp."'><< Prev</a> |";
}

if($lm<=$i){
	$nn = $st + $lm;
	$echo .= "| <a href='?st=".$nn."'>Next >></a>";
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update CD values</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
<script>
	function fix(id, cdd, cds){ window.location.replace("?st=<?php echo $st; ?>&fix="+id+"&cdd="+cdd+"&cds="+cds); }
	function fix_all(){
		document.frmfx.submit();
	} 
</script>
</head>
<body>
<?php
if(!empty($echo)){
	echo $echo;
}else{
?>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>
<?php } ?>
</body>
</html>