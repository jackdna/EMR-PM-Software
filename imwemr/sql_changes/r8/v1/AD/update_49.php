<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
set_time_limit(0);
$csvFileName=$GLOBALS['fileroot']."/sql_changes/r8/v1/AD/Education_documents_aligned_with_AAO_documents_2018.csv";

if(!file_exists($csvFileName)){
	die("File not exists: ".$csvFileName);
}

$fileContents = fopen($csvFileName,"r");

$row = 0;
$updatedxcoderecords=0;
$updateMsg ="";
if(file_exists($csvFileName)){
	while(($data = fgetcsv($fileContents,10000,',')) !== FALSE){
		if($row >0){ 
			
			$newengAAOdoc 	= trim(addslashes($data[5]));
			$insertedDxcode = trim($data[6]);   
			//pre($data); die;
			$newaaodocs = "SELECT  `id`, `name` FROM `document` WHERE pt_edu='1'and andOrCondition='A'and lab_criteria='greater' and doc_from='uploadDoc' AND upload_doc_type='pdf' AND `name` = '".$newengAAOdoc."' order by `name` ASC";
		 		//echo $newaaodocs.'<br>';
				$row_newaaodocs=imw_query($newaaodocs);
					while($res_newaaodocs=imw_fetch_assoc($row_newaaodocs)){
						$newaaodocsID = $res_newaaodocs['id'];   
						// LINKED ICD10 CODES WITH AAO TEMPLATES
						$updatenewaaodocs =imw_query("UPDATE `document` set dx='".$insertedDxcode."' WHERE id='".$newaaodocsID."'");	
						if(imw_affected_rows()>0){
							$updatedxcoderecords++;	
						}
					}	
		}
		
		$row++;
	}
	fclose($fileContents);
	if($updatedxcoderecords>0){ $updateMsg = "Update run successfully..."; }else{ $updateMsg = "No Record Found!!"; }
	echo "<div style=\"color:green\"><br/><h4>".$updateMsg."</h4></div>";
}else{echo "<p style='color:red;font-size:14px;'>".$csvFileName." file not found</p>";}
?>
<html>
<head>
<title>Update: 49 - Linked ICD10 Codes with AAO Templates</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body style="font-family:verdana; color:#090; font-weight:bold;">
</body>
</html>