<?php 
set_time_limit(0);
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$msg_info=array();

//QUERY TO GET AFFECTED ROWS OF DEFAULT SIG PAD SIGNATURE IMAGE
$sql='SELECT 
		form_information_id, 
		patient_id, 
		consent_form_name, 
		consent_form_content_data
	FROM 
		`patient_consent_form_information` 
	WHERE 
		`form_created_date` >= "2020-05-25 00:00:01" 
	AND 
		`form_created_date` <= "2020-06-18 23:59:59"
	AND
	( 
	`consent_form_content_data` like "%/consent_forms/sign_pat_%"  
	OR 
	`consent_form_content_data` like "%/consent_forms/sign_wit_%" 
	OR
	`consent_form_content_data` like "%/consent_forms/sign_phy_%" 
	)';

//echo $sql;  Limit 0, 1
$res = imw_query($sql) or $msg_info[] = imw_error();

if( imw_num_rows($res)>0 )
{ 
	
	$updatedPatientDetails = array();
	$cnt = 0;
	while($row = imw_fetch_assoc($res))
	{
		$existingPath = $replacePathstr = $findpathstr = $findpathstr_pat = $findpathstr_wit = $findpathstr_phy = "";
		
		$form_information_id 		= $row["form_information_id"];
		$patient_id 				= $row["patient_id"];
		$consent_form_content_data 	= $row["consent_form_content_data"];
		
		//MAKE SIGNATURE DATA DIRECTORY PATH
		$consentDirPath = data_path()."PatientId_".$patient_id."/consent_forms/";
		
		$existingPath = "/imwemr/data/imwemr/PatientId_".$patient_id."/consent_forms/";
		$replacePathstr = "/imwemr/data/imwemr/tmp/blank.jpg";
		
		if(is_dir($consentDirPath))
		{
			
			foreach(glob($consentDirPath."/*") as $jpeg_file_name_eng)
			{	 
				$jpeg_doc_name_en=basename($jpeg_file_name_eng);
				
				//PATIENT SIGNATURE REPLACEMENT WORKS STARTS HERE
				if(stristr($jpeg_doc_name_en,"sign_pat_"))
				{	
					$findpathstr_pat = $existingPath.$jpeg_doc_name_en;
					$consent_form_content_data= str_ireplace($findpathstr_pat,$replacePathstr,$consent_form_content_data);
				}
				//WITNESS SIGNATURE REPLACEMENT WORKS STARTS HERE
				if(stristr($jpeg_doc_name_en,"sign_wit_"))
				{	
					$findpathstr_wit = $existingPath.$jpeg_doc_name_en;
					$consent_form_content_data= str_ireplace($findpathstr_wit,$replacePathstr,$consent_form_content_data);
				}
				//PHYSICIAN SIGNATURE REPLACEMENT WORKS STARTS HERE
				if(stristr($jpeg_doc_name_en,"sign_phy_"))
				{	
					$findpathstr_phy = $existingPath.$jpeg_doc_name_en;
					$consent_form_content_data= str_ireplace($findpathstr_phy,$replacePathstr,$consent_form_content_data);
				}
			}
		} //END IS_DIR CONDITION
		$updtQry =  "UPDATE 
						patient_consent_form_information 
					SET 
						consent_form_content_data = '".addslashes($consent_form_content_data)."'
				    WHERE 
						form_information_id	= '".$form_information_id."'
				   ";
		//echo '<br>'.$updtQry; die;
		$updtRes = imw_query($updtQry) or $msg_info[] = imw_error();
		$updatedPatientDetails[] = 'Patient_id='.$patient_id.' @@ form_information_id='.$form_information_id;
		$cnt++;
	}
	pre($updatedPatientDetails).'<br>';
}
echo 'Total Records: '.$cnt;

if(count($msg_info)>0)
{

    $msg_info[] = '<br><br><b>Update Consent run FAILED!</b><br>';
    $color = "red";
}
else
{
    $msg_info[] = "<br><br><b>Update Consent run successfully!</b>";
    $color = "green";
}

?>
<html>
<head>
<title>Special Update Consent IM-9194</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>