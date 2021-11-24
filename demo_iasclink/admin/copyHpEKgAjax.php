<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("../common/conDb.php");
include("../common/iOLinkCommonFunction.php");
$previousWaitingId 					= $_REQUEST['previousWaitingId'];
$currentWatingId 					= $_REQUEST['currentWatingId'];
$folderName 						= $_REQUEST['folderName'];
$msgInfo							= array();
$folderNameChk 						= $folderName;
if($folderName=='HP') 							{ $folderNameChk = 'h&p'; 
}else if($folderName=='EKG') 					{ $folderNameChk = 'ekg'; 
}else if($folderName=='Patient Information')	{ $folderNameChk = 'ptInfo'; 
}else if($folderName=='Clinical') 				{ $folderNameChk = 'clinical'; 
}else if($folderName=='Health Quest') 			{ $folderNameChk = 'healthQuest'; 
}else if($folderName=='Ocular Hx') 				{ $folderNameChk = 'ocularHx'; 
}
$docAlreadyExist					= true;
$selQry 							= "SELECT * FROM iolink_scan_consent WHERE patient_in_waiting_id='".$previousWaitingId."' AND iolink_scan_folder_name='".$folderNameChk."'";
$selRes 							= imw_query($selQry) or die(imw_error());
if(imw_num_rows($selRes)) {
	while($selRow = imw_fetch_array($selRes)) {
		$scan_consent_id 			= $selRow['scan_consent_id'];
		$patient_id 				= $selRow['patient_id'];
		$consent_template_id 		= $selRow['consent_template_id'];
		$scan1Upload 				= $selRow['scan1Upload'];
		$scan1Status				= $selRow['scan1Status'];
		$scan2Upload 				= $selRow['scan2Upload'];
		$scan2Status 				= $selRow['scan2Status'];
		$scan_save_date_time 		= $selRow['scan_save_date_time'];
		$document_name 				= $selRow['document_name'];
		$iolink_scan_folder_name	= $selRow['iolink_scan_folder_name'];
		$image_type 				= $selRow['image_type'];
		$document_size 				= $selRow['document_size'];
		$pdfFilePath 				= $selRow['pdfFilePath'];
		$mask 						= $selRow['mask'];
		$idoc_consent_template_id 	= $selRow['idoc_consent_template_id'];
		$copy_from_scan_consent_id 	= $selRow['copy_from_scan_consent_id'];

		$chkScnExistQry 			= "SELECT * FROM iolink_scan_consent WHERE patient_in_waiting_id='".$currentWatingId."' AND (copy_from_scan_consent_id='".$scan_consent_id."' OR copy_from_scan_consent_id='".$copy_from_scan_consent_id."')  AND copy_from_scan_consent_id!='0'";		
		$chkScnExistRes 			= imw_query($chkScnExistQry) or die(imw_error()).$msgInfo[]=imw_error();
		if(imw_num_rows($chkScnExistRes)>0) {
			$insrtUpdtQry 			= "UPDATE ";
			$whereQry				= "WHERE patient_in_waiting_id='".$currentWatingId."' AND copy_from_scan_consent_id='".$scan_consent_id."'  AND copy_from_scan_consent_id!='0'";	
		}else {
			$insrtUpdtQry 			= "INSERT INTO ";
			$whereQry				= "";
			$docAlreadyExist		= false;
		}
		$insrtUpdtQry 			   .= " iolink_scan_consent SET
									  	 patient_id 				= '".$patient_id."',
										 patient_in_waiting_id		= '".$currentWatingId."',
										 consent_template_id 		= '".$consent_template_id."',
										 scan1Upload 				= '".addslashes($scan1Upload)."',
										 scan1Status 				= '".$scan1Status."',
										 scan2Upload 				= '".addslashes($scan2Upload)."',
										 scan2Status 				= '".$scan2Status."',
										 scan_save_date_time 		= '".$scan_save_date_time."',
										 document_name 				= '".addslashes($document_name)."',
										 iolink_scan_folder_name 	= '".addslashes($iolink_scan_folder_name)."',
										 image_type 				= '".$image_type."',
										 document_size 				= '".$document_size."',
										 pdfFilePath 				= '".$pdfFilePath."',
										 mask 						= '".$mask."',
										 idoc_consent_template_id 	= '".$idoc_consent_template_id."',	
										 copy_from_scan_consent_id 	= '".$scan_consent_id."'	
										 $whereQry
									";
		$insrtUpdtRes				= imw_query($insrtUpdtQry) or die(imw_error()).$msgInfo[]=imw_error();						
	}
}

if(count($msgInfo)==0) {
	$msg = 'Document(s) Copied';	
	if($docAlreadyExist==true) { 
		$msg='Document(s) Already Copied'; 
	}else {  
		setReSyncroStatus($currentWatingId,'scanDoc');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
	}
	echo $msg;	
}else {
	echo(implode("<br>",$msg_info)); 	
}
?>
