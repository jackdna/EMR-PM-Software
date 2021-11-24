<?php
set_time_limit(300);
$_SESSION['callFrom']="medicationTab";
include_once('../../../config/globals.php'); 
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../../library/classes/common_function.php");
$pid = $_SESSION['patient'];
$save = new SaveFile($pid);
$upload_path = $save->upDir.$save->pDir.'/';

$page_title = "Export CSV Immunization";
$filename="immunization_csv.csv";
$pfx=",";
$car_aux = "\r\n";

if(($recno=="") || ($recno==1)){
	$recno   =1;
	$csvtext ="";
	$csvtext.="Name".$pfx;
	$csvtext.="Type".$pfx;
	$csvtext.="Dose".$pfx;
	$csvtext.="Route & Site".$pfx;
	$csvtext.="Lot#".$pfx;
	$csvtext.="Expiration Date".$pfx;
	$csvtext.="Manufacturer".$pfx;
	$csvtext.="Admintd. Date Time".$pfx;
	$csvtext.="Administered By".$pfx;
	$csvtext.="Consent Date".$pfx;
	$csvtext.="Reaction".$pfx;
	$csvtext.="Comments".$car_aux;
	$fp=fopen($upload_path.$filename,"w");
	fwrite($fp,$csvtext);
	fclose($fp);
	
}

$sql = "select * from immunizations  where patient_id='".$pid."' and status='Given'";
$rez =@imw_query($sql);				
$num_rows=@imw_num_rows($rez);				
for($i=1;$i<=$num_rows;$i++)
{
	$checkImmunizations="disabled";
	$row = @imw_fetch_array($rez);					
	
	$tmpz = "administered_by_id".$i;
	$$tmpz = $row["administered_by_id"];
	
	$tmpz = "imnzn_id".$i;//foregin key Id From Table immunizatio_admin
	$$tmpz = $row["imnzn_id"];	
	
	$tmpz = "immzn_dose_id".$i;
	$$tmpz = $row["immzn_dose_id"];
	
	$tmpz = "manufacturer".$i;
	$$tmpz = $row["manufacturer"];	
		
	$tmpz = "immunization_id".$i;
	$$tmpz = $row["immunization_id"];								
		
	$tmpz = "administered_date".$i;
	$$tmpz = $row["administered_date"];		
	
	$tmpz = "immzn_note".$i;
	$$tmpz = $row["note"];
	
	$tmpz = "lot_number".$i;
	$$tmpz = $row["lot_number"];		
	
	$tmpz = "imu_autoid".$i;//primary key ID
	$$tmpz = $row["id"];
	
	$tmpz = "immzn_type".$i;
	$$tmpz = $row["immzn_type"];
	
	$tmpz = "immzn_type".$i;
	$$tmpz = $row["immzn_type"];
	 
	$tmpz = "immzn_dose".$i;
	$$tmpz = $row["immzn_dose"];
	
	$tmpz = "immzn_route_site".$i;
	$$tmpz = $row["immzn_route_site"];
	
	$tmpz = "expiration_date".$i;
	$$tmpz = $row["expiration_date"];
	
	$tmpz = "consent_date".$i;
	$$tmpz = $row["consent_date"];
	
	$tmpz = "adverse_reaction".$i;
	$$tmpz = $row["adverse_reaction"];
	
	$administered_by_id="administered_by_id".$i;//
	$immunization_id = "immunization_id".$i;	//				
	$expiration_date = "expiration_date".$i;//
	$administered_date = "administered_date".$i;//
	$consent_date = "consent_date".$i;
	$manufacturer = "manufacturer".$i;
	$lot_number = "lot_number".$i;
	$immzn_note = "immzn_note".$i;
	
	$adverse_reaction = "adverse_reaction".$i;
	$immzn_route_site = "immzn_route_site".$i;
	$immzn_type = "immzn_type".$i;
	$immzn_dose = "immzn_dose".$i;
					
	$imu_autoid = "imu_autoid".$i;
	$imnzn_id  = "imnzn_id".$i;
	
	$immzn_dose_id  = "immzn_dose_id".$i;
	
	$onclick1 = ( $i != $lm ) ? "" : "<img id=\"icnIM_$i\" src=\"../../images/add_medical_history.gif\" alt=\"Add More\"  onClick=\"showNextImuniZations('$i');\" >";					
	
	if(trim($$administered_by_id) || trim($$immunization_id) || ($$expiration_date!="" && $$expiration_date!="0000-00-00") || ($$administered_date!="" && $$administered_date!="0000-00-00") || ($$consent_date!="" && $$consent_date!="0000-00-00") || trim($$manufacturer) || trim($$lot_number) || trim($$immzn_note) || trim($$adverse_reaction) || trim($$immzn_route_site) || trim($$immzn_type) || trim($$immzn_dose) || trim($$imu_autoid) || trim($$imnzn_id)) {
		
		$$expiration_date 	= ($$expiration_date!="" && $$expiration_date!="0000-00-00")? get_date_format($$expiration_date):"";
		$$administered_date = ($$administered_date!="" && $$administered_date!="0000-00-00")? get_date_format($$administered_date):"";
		$$consent_date  	= ($$consent_date!="" && $$consent_date!="0000-00-00")? get_date_format($$consent_date):"";
		
		$csvtext.='"'.$$immunization_id.'"';
		$csvtext.=$pfx;
		$csvtext.='"'.$$immzn_type.'"';
		$csvtext.=$pfx;
		$csvtext.='"'.$$immzn_dose.'"';
		$csvtext.=$pfx;
		$csvtext.='"'.$$immzn_route_site.'"';
		$csvtext.=$pfx;
		$csvtext.='"'.$$lot_number.'"';
		$csvtext.=$pfx;
		$csvtext.='"'.$$expiration_date.'"';
		$csvtext.=$pfx;
		$csvtext.='"'.$$manufacturer.'"';
		$csvtext.=$pfx;
		$csvtext.='"'.$$administered_date.'"';
		
		//START CODE TO GET ADMINISTRATOR FULL NAME BY ID
		$administeredByFullName='';
		$sqlFullNameQry = "select id, concat(fname,', ',lname) as full_name " .
		"from users " .
		"WHERE id='".$$administered_by_id."'";
		$sqlFullNameRes = imw_query($sqlFullNameQry);
		if(imw_num_rows($sqlFullNameRes)>0) {
			$sqlFullNameRow = imw_fetch_array($sqlFullNameRes);
			$administeredByFullName = $sqlFullNameRow['full_name'];
		}
		
		$csvtext.=$pfx;
		$csvtext.='"'.$administeredByFullName.'"';
		//END CODE TO GET ADMINISTRATOR FULL NAME BY ID
		
		$csvtext.=$pfx;
		$csvtext.='"'.$$consent_date.'"';
		$csvtext.=$pfx;
		$csvtext.='"'.$$adverse_reaction.'"';
		$csvtext.=$pfx;
		$csvtext.='"'.$$immzn_note.'"';
		$csvtext.=$car_aux;
		
	}
}
$fp=fopen($upload_path.$filename,"w+");
if(fwrite($fp,$csvtext)) {	
	fclose($fp);
	
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
