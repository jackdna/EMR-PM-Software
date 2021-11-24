<?php
set_time_limit(300);
set_time_limit(300);
$_SESSION['callFrom']="medicationTab";
include_once('../../../config/globals.php'); 
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/common_function.php");
$pid = $_SESSION['patient'];
$save = new SaveFile($pid);
$upload_path = $save->upDir.$save->pDir.'/';

$page_title = "Export CSV Allergy";
$filename = "allergies_csv.csv";
$pfx = ",";
$car_aux = "\r\n";

if(($recno=="") || ($recno==1)){
	$recno = 1;
	$csvtext = "";
	$csvtext .= "Drug".$pfx;
	$csvtext .= "Name".$pfx;
	$csvtext .= "Begin Date".$pfx;
/*	$csvtext .= "Acute".$pfx;
	$csvtext .= "Chronic".$pfx; */
	$csvtext .= "Comments".$pfx;
	$csvtext .= "Status".$car_aux;
}

$sql = "select * from lists where pid = '$pid' and type in(3,7) and allergy_status != 'Deleted'";
if($_REQUEST["type"]!=""){
	$sql = "select * from lists where pid = '$pid' and type = ".$_REQUEST["type"]." and allergy_status != 'Deleted'";
}

$rez =@imw_query($sql);				
$num_rows=@imw_num_rows($rez);				
for($i=1;$i<=$num_rows;$i++)
{
	$row = @imw_fetch_array($rez);					
	$ag_obj_id = "ag_obj_id".$i;
	$$ag_obj_id = $row["ag_obj_id"];
	$ag_occular_drug = "ag_occular_drug".$i;
	$$ag_occular_drug = $row["ag_occular_drug"];
	$tmpz = "ag_occular".$i;
	$$tmpz = $row["type"];		
	$tmpz = "ag_title".$i;
	$$tmpz = $row["title"];								
	$tmpz = "ag_begindate".$i;
	$$tmpz = $row["begdate"];		
	$tmpz = "ag_enddate".$i;
	$tmpz = "ag_comments".$i;
	$$tmpz = $row["comments"];
/*	$tmpz = "ag_acute".$i;
	$$tmpz = $row["acute"];
	$tmpz = "ag_chronic".$i;
	$$tmpz = $row["chronic"];		*/			 
	$tmpz = "ag_id".$i;
	$$tmpz = $row["id"];
	$tmpz = "ag_status".$i;
	$$tmpz = $row["allergy_status"];
}

for($i=1;$i<=$num_rows;$i++)
{		
	if($cl%2 == 0){
		$class = 'bgcolor';
		$bgTrColor = "#F3F8F2";
	}
	else{
		$class = '';
	}
	$ag_obj_id = "ag_obj_id".$i;
	$ag_occular_drug = "ag_occular_drug".$i;
	$ag_occular = "ag_occular".$i;
	$ag_title = "ag_title".$i;					
	$ag_begindate = "ag_begindate".$i;
/*	$ag_acute = "ag_acute".$i;
	$ag_chronic = "ag_chronic".$i; */
	$ag_comments = "ag_comments".$i;				
	$ag_id = "ag_id".$i;
	$ag_status = "ag_status".$i;
	
	$onclick1 = ( $i != $lm ) ? "" : "<img id=\"icnag_$i\" src=\"../../../library/images/add_small.png\" alt=\"Add More\"  onClick=\"showNextAllergy('$i');\" >";					
	
	if(trim($$ag_occular_drug) || trim($$ag_title) || ($$ag_begindate!="" && $$ag_begindate!="0000-00-00") || ($$ag_enddate!="" && $$ag_enddate!="0000-00-00") || $$ag_acute == "acute" || $$ag_chronic == "chronic" || trim($$ag_comments)) {
		$ag_occular_drugNew='';
		if($$ag_occular_drug == 'fdbATDrugName'){ 
			$ag_occular_drugNew.='Drug';
		}else if($$ag_occular_drug == 'fdbATIngredient'){ 
			$ag_occular_drugNew.='Ingredient';
		}else if($$ag_occular_drug == 'fdbATAllergenGroup'){ 
			$ag_occular_drugNew.='Allergen';
		}
		
		$$ag_begindate = ($$ag_begindate!="" && $$ag_begindate!="0000-00-00")? get_date_Format($$ag_begindate):"";
		$$ag_acute = ($$ag_acute == "acute") ? "Yes" : "";
		$$ag_chronic = ($$ag_chronic == "chronic") ? "Yes" : "";
		$csvtext .= '"'.$ag_occular_drugNew.'"';
		$csvtext .= $pfx;
		$csvtext .= '"'.$$ag_title.'"';
		$csvtext .= $pfx;
		$csvtext .= '"'.$$ag_begindate.'"';
		$csvtext .= $pfx;
	/*	$csvtext .= '"'.$$ag_acute.'"';
		$csvtext .= $pfx;
		$csvtext .= '"'.$$ag_chronic.'"';
		$csvtext .= $pfx;   */
		$csvtext .= '"'.$$ag_comments.'"';			
		$csvtext .= $pfx;
		$csvtext .= '"'.$$ag_status.'"';
		$csvtext .= $car_aux;
	}
}
		
file_put_contents($upload_path.$filename,$csvtext);
if(empty($csvtext) == false) {	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream;");
	header("Content-disposition:attachment; filename=\"".$filename."\"");
	header("Content-Length: ".@filesize($upload_path.$filename));
	@readfile($upload_path.$filename) or die("File not found.");
	unlink($upload_path.$filename);
	exit;
}	
?>