<?php
set_time_limit(300);
$_SESSION['callFrom']="medicationTab";
include_once('../../../config/globals.php'); 
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/common_function.php");
$_SESSION['callFrom']="medicationTab";
$pid = $_SESSION['patient'];
$save = new SaveFile($pid);
$upload_path = $save->upDir.$save->pDir.'/';

$page_title = "Export CSV Medication";
$filename = "medication_csv.csv";
$pfx = ",";
$car_aux = "\r\n";

$csvtext = "";
$csvtext .= "Ocular".$pfx;
$csvtext .= "Name".$pfx;	// [ Old Data ] = Medication
$csvtext .= "Dosage".$pfx;
$csvtext .= "Sig.".$pfx;
$csvtext .= "Qty".$pfx;
$csvtext .= "Refills".$pfx;
$csvtext .= "Prescribed By".$pfx;
$csvtext .= "Begin Date".$pfx;
$csvtext .= "EndDate".$car_aux;

/*$csvtext .= "EndDate".$pfx;
$csvtext .= "Comments".$car_aux; */

//--- GET ALL MEDICATION RECORDS ----
$query = "select * from lists where pid = '$pid' and type in (1,4) and allergy_status != 'Deleted'";
$sql = imw_query($query);
$num_rows=imw_num_rows($sql);				
for($i=1;$i<=$num_rows;$i++)
{
	$row = imw_fetch_array($sql);
	if($row != false){
		$tmpz = "md_occular".$i;
		$$tmpz = $row["type"];		
		$tmpz = "md_medication".$i;
		$$tmpz = stripslashes($row["title"]);
		$tmpz = "md_dosage".$i;
		$$tmpz = $row["destination"];		
		$tmpz = "md_prescribedby".$i;
		$$tmpz = $row["referredby"];		
		$tmpz = "md_begindate".$i;
		$$tmpz = $row["begdate"];		
		$tmpz = "md_enddate".$i;
		$$tmpz = $row["enddate"];		
	/*	$tmpz = "md_comments".$i;
		$$tmpz = $row["comments"];	*/				
		$tmpz = "med_id".$i;
		$$tmpz = $row["id"];
		
		$tmpz = "md_qty".$i;
		$$tmpz = $row["qty"];

		$tmpz = "md_sig".$i;
		$$tmpz = $row["sig"];

		$tmpz = "md_refills".$i;
		$$tmpz = $row["refills"];
	}
	if($cl%2 == 0){
		$class = 'bgcolor';
	}
	else{
		$class = '';
	}
	$md_occular = "md_occular".$i;
	$md_medication = "md_medication".$i;
	$md_dosage = "md_dosage".$i;
	$md_prescribedby = "md_prescribedby".$i;
	$md_begindate = "md_begindate".$i;
	$md_enddate = "md_enddate".$i;
//	$md_comments = "md_comments".$i;
	$med_id = "med_id".$i;

	$md_qty = "md_qty".$i;
	$md_sig = "md_sig".$i;
	$md_refills = "md_refills".$i;

	if(trim($$md_occular) || trim($$md_medication) || trim($$md_dosage) || trim($$md_prescribedby) || ($$md_begindate!="" && $$md_begindate!="0000-00-00") || ($$md_enddate!="" && $$md_enddate!="0000-00-00")) {

		$$md_occular = ($$md_occular == "4")? "Yes" : "";
		$$md_begindate = ($$md_begindate!="" && $$md_begindate!="0000-00-00")? get_date_format($$md_begindate):"";
		$$md_enddate = ($$md_enddate!="" && $$md_enddate!="0000-00-00")? get_date_format($$md_enddate):"";
		
		$csvtext .= '"'.$$md_occular.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$md_medication.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$md_dosage.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$md_sig.'"';
		$csvtext .= $pfx;
		$csvtext .= '"'.$$md_qty.'"';
		$csvtext .= $pfx;
		$csvtext .= '"'.$$md_refills.'"';
		$csvtext .= $pfx;
		$csvtext .= '"'.$$md_prescribedby.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$md_begindate.'"';
		$csvtext .= $pfx;			
		$csvtext .= '"'.$$md_enddate.'"';
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