<?php
set_time_limit(300);
$_SESSION['callFrom']="medicationTab";
include_once('../../../config/globals.php');
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
$pid = $_SESSION['patient'];
$save = new SaveFile($pid);
$upload_path = $save->upDir.$save->pDir.'/';

$page_title = "Export CSV Surgeries";
$filename = "sx_procedure_csv.csv";
$pfx = ",";
$car_aux = "\r\n";

if(($recno=="") || ($recno==1)){
	$recno = 1;
	$csvtext = "";
	$csvtext .= "Ocular".$pfx;
	$csvtext .= "Name".$pfx;
	$csvtext .= "Date of Surgery".$pfx;
	$csvtext .= "Physician".$pfx;
	$csvtext .= "Comments".$car_aux;
	file_put_contents($filename,$csvtext);
}

$query = "select * from lists where pid=$pid and type in(5,6) and allergy_status != 'Deleted' Order By begdate Desc, id Desc ";
$sql = imw_query($query);
$cnt = imw_num_rows($sql);

$i = 0;
while( $row = imw_fetch_assoc($sql) )
{
		$i++;
		$tmpz = "sg_occular".$i;
		$$tmpz = $row["type"];		
		$tmpz = "sg_title".$i;
		$$tmpz = $row["title"];					
		$tmpz = "sg_referredby".$i;
		$$tmpz = $row["referredby"];		
		$tmpz = "sg_begindate".$i;
		$$tmpz = $row["begdate"];		
		$tmpz = "sg_enddate".$i;
		$$tmpz = $row["enddate"];		
		$tmpz = "sg_comments".$i;
		$$tmpz = $row["comments"];					
		$tmpz = "sg_id".$i;
		$$tmpz = $row["id"];
	
		$sg_occular = "sg_occular".$i;
		$sg_title = "sg_title".$i;					
		$sg_begindate = "sg_begindate".$i;
		$sg_enddate = "sg_enddate".$i;
		$sg_referredby = "sg_referredby".$i;
		$sg_comments = "sg_comments".$i;				
		$sg_id = "sg_id".$i;
	
		if(trim($$sg_occular) || trim($$sg_title) || ($$sg_begindate!="" && $$sg_begindate!="0000-00-00") || trim($$sg_referredby) || trim($$sg_comments)) 		{
				$$sg_occular = ($$sg_occular == "6")? "Yes" : "";
				$$sg_begindate = ($$sg_begindate!="" && $$sg_begindate!="0000-00-00") ? get_date_format($$sg_begindate) : "";		
				$csvtext .= '"'.$$sg_occular.'"';
				$csvtext .= $pfx;
				$csvtext .= '"'.html_entity_decode($$sg_title).'"';
				$csvtext .= $pfx;
				$csvtext .= '"'.$$sg_begindate.'"';
				$csvtext .= $pfx;
				$csvtext .= '"'.$$sg_referredby.'"';
				$csvtext .= $pfx;
				$csvtext .= '"'.$$sg_comments.'"';
				$csvtext .= $car_aux;
		}

}
		
file_put_contents($upload_path.$filename,$csvtext);
if(empty($csvtext) == false){
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