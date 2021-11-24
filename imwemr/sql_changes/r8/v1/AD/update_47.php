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
$insertedengAAOrecords =0;
$insertedspaAAOrecords =0;

if(file_exists($csvFileName)){
	while(($data = fgetcsv($fileContents,10000,',')) !== FALSE){
		if($row >0){ 
			
			$engAAOdocs 	= trim(addslashes($data[3]));
			$spaAAOdocs 	= trim(addslashes($data[4]));
			//pre($data); die;
			$qry_EngAAOdocs = "SELECT  `id`, `name` FROM `document` WHERE pt_edu='1' AND doc_from='uploadDoc' AND upload_doc_type='pdf' AND `name` = '".$engAAOdocs."' AND status ='0' order by `name` ASC";
			
			$row_engdocs=imw_query($qry_EngAAOdocs);
					while($res_EngAAOdocs=imw_fetch_assoc($row_engdocs)){
						$EngAAOdocsID = $res_EngAAOdocs['id'];   
						//$EngAAOdocsNAME = $res_EngAAOdocs['name'];
						  	$updateEngAAOdocs =imw_query("UPDATE `document` set status=1 WHERE id='".$EngAAOdocsID."'");	
							if(imw_affected_rows()>0){
								$insertedengAAOrecords++;
								
							}
					}
			
			$qry_SpaAAOdocs = "SELECT  `id`, `name` FROM `document` WHERE pt_edu='1' AND doc_from='uploadDoc' AND upload_doc_type='pdf' AND `name` = '".$spaAAOdocs."' AND status ='0' order by `name` ASC";
			
			$row_spadocs=imw_query($qry_SpaAAOdocs);
					while($res_SpaAAOdocs=imw_fetch_assoc($row_spadocs)){
						$SpaAAOdocsID = $res_SpaAAOdocs['id'];   
						//$SpaAAOdocsNAME = $res_SpaAAOdocs['name'];
							$updateSPaAAOdocs =imw_query("UPDATE `document` set status=1 WHERE id='".$SpaAAOdocsID."'");	
							if(imw_affected_rows()>0){
								$insertedspaAAOrecords++;	
							}
					}		
		}
		
		$row++;
	}
	fclose($fileContents);
	echo "<div style=\"color:green\"><br/><h3>Update run successfully... <br/> <br/> Updated AAO Records:-   English:  ".$insertedengAAOrecords."  ||  Spanish: ". $insertedspaAAOrecords." </h3></div>";
}else{echo "<p style='color:red;font-size:14px;'>".$csvFileName." file not found</p>";}
?>

<html>
<head>
<title>Updates 47 (UPDATE STATUS OF OLD AAO 2017-18 TEMPLATES)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body></body>
</html>