<?php 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

include_once('common/conDb.php');
$waiting_id=$_REQUEST['multiPatientInWaitingId'];
$waiting_id_arr=explode(",",$waiting_id);
$old_days_count = intval($_REQUEST['old_days_count']);
$days	=	"";
$date_scanned	=	array();
$returnData		=	array();
foreach($waiting_id_arr as $waiting_id)
{
	$counter	=	0;
	if(!array_key_exists($waiting_id,$returnData))
	$returnData[$waiting_id]	=	array('patientDetail'=>'','signedForm'=>array(),'scanDocs'=>array() );
	$getPatientInWaitingTblInfoQry=("select * from iolink_consent_filled_form,patient_in_waiting_tbl where iolink_consent_filled_form.fldPatientWaitingId IN (".$waiting_id.") and patient_in_waiting_tbl.patient_in_waiting_id IN (".$waiting_id.")");
	$getPatientInWaitingTblInfoRes 	= imw_query($getPatientInWaitingTblInfoQry) or die(imw_error());
	while($row=imw_fetch_array($getPatientInWaitingTblInfoRes))
	{
		$dos_days=strtotime($row['dos']);
		$consent_days=strtotime($row['consent_save_date_time']);	
		$d1=$dos_days-$consent_days;
		$days=ceil($d1/(60*60*24));
		if($consent_days!="" && intval($consent_days)<intval($dos_days))
		{
			if(intval($days)>=$old_days_count)
			{
				$returnData[$waiting_id]['signedForm'][]	=	trim(ucwords($row['surgery_consent_name']));
				$counter++;
			}
		}		
	}
	$scanned_upload=imw_query("select isc.*, piw.dos from iolink_scan_consent isc,patient_in_waiting_tbl piw where isc.patient_in_waiting_id = piw.patient_in_waiting_id AND isc.patient_in_waiting_id IN(".$waiting_id.")");
	while($res=imw_fetch_array($scanned_upload))
	{
		$dos_days=strtotime($res['dos']);
		$folder_name='';
		  switch($res['iolink_scan_folder_name'])
		  {
			  case 'ptInfo' :
			  $folder_name='Patient Info';
			  break;
			  case 'ocularHx':
			  $folder_name='Ocular Hx';
			  break;
			  case 'h&p':
			  $folder_name='H&P';
			  break;
			  case 'healthQuest':
			  $folder_name='Health Questionnaire';
			  break;
			  case 'ekg':
			  $folder_name='EKG';
			  break;
			  case 'clinical':
			  $folder_name='Clinical';
			  break;
			  case 'consent':
			  $folder_name='Consent';
			  break;
		  }
		if(!array_key_exists($res['iolink_scan_folder_name'],$returnData[$waiting_id]['scanDocs']))
		  	$returnData[$waiting_id]['scanDocs'][$res['iolink_scan_folder_name']]	=	array();
		
		if(!array_key_exists($res['patient_id'],$returnData[$waiting_id]['patientDetail']))
		  	$returnData[$waiting_id]['patientDetail'][$res['patient_id']]	=	'';
						
		 	$signed_patient_consent=imw_query("select * from patient_data_tbl where patient_id=".$res['patient_id']."");
			$row1=imw_fetch_array($signed_patient_consent);
		  if(strtotime($res['scan_save_date_time'])!="" && intval(strtotime($res['scan_save_date_time']))<intval($dos_days))
		  {
			  $date_scanned[$i]=ceil(($dos_days-(strtotime($res['scan_save_date_time'])))/(60*60*24));
			  if(intval($date_scanned[$i])>$old_days_count)
			  {
			  	$returnData[$waiting_id]['scanDocs'][ucwords($folder_name)][]	=	$res['document_name'];
				$returnData[$waiting_id]['patientDetail'][$res['patient_id']]	=	$row1['patient_lname'].",&nbsp;".$row1['patient_fname'];
				$counter++;
			  }
		  }
	  }
	if($counter == 0 ) unset($returnData[$waiting_id]);
}
echo json_encode($returnData);
?>