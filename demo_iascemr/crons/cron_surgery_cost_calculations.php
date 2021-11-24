<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../common/conDb.php");
include_once("../admin/classObjectFunction.php");
$objManageData = new manageData;

$dateTime	=	date('Y-m-d h:i:s A');

$qry		=	"Select P.dos, P.surgeonId
									From patientconfirmation P
									JOIN operatingroomrecords OP ON P.patientConfirmationId = OP.confirmation_id
									JOIN injection INJ ON P.patientConfirmationId = INJ.confirmation_id 
									JOIN laser_procedure_patient_table LP ON P.patientConfirmationId = LP.confirmation_id
									Where  P.finalize_status = 'true' And 
									(LP.start_time_status = 1 || OP.start_time_status = 1 || INJ.start_time_status = 1)
				";
$sql		=	imw_query($qry) or $msgInfo	=	'Error found at line no.'.(__LINE__).': '.imw_error();	
$cnt		=	imw_num_rows($sql);
$uDates	=	'';
$uDatesArr	=	array();
if($cnt  > 0 )
{
		
	$counter =	0;
	$arr		 =	array();
	while	( $row	=	imw_fetch_object($sql))
	{
			if(!array_key_exists($row->dos,$arr) || !array_key_exists($row->surgeonId,$arr[$row->dos]))
			{
				$counter++;
				$uDatesArr[]	=	"(P.dos = '".$row->dos."' And P.surgeonId = '".$row->surgeonId."' ) ";
			}
			
			if(!array_key_exists($row->dos,$arr))
				$arr[$row->dos] = array();
			if(!array_key_exists($row->surgeonId,$arr[$row->dos]))
				$arr[$row->dos][$row->surgeonId]  = $row->surgeonId;
	}
	
	if(is_array($uDatesArr) && count($uDatesArr) > 0 )
	{
		$uDates	.=	' || (';
		$uDates .=	implode(' || ',$uDatesArr);
		$uDates	.=	' )';	
	}
	
}

$qry	=	" Select P.patientConfirmationId,
									LP.start_time_status as LaserStatus, OP.start_time_status  as OPStatus, INJ.start_time_status as InjStatus
									From patientconfirmation P
									JOIN operatingroomrecords OP ON P.patientConfirmationId = OP.confirmation_id
									JOIN injection INJ ON P.patientConfirmationId = INJ.confirmation_id 
									JOIN laser_procedure_patient_table LP ON P.patientConfirmationId = LP.confirmation_id 
									Where P.finalize_status = 'true' And (
									P.dos = '".date('Y-m-d', strtotime($dateTime))."' 
									$uDates )
									
				";

$msgInfo =	'';				
$sql	=	imw_query($qry) or $msgInfo	=	'Error found at line no.'.(__LINE__).': '.imw_error();

$cnt	=	imw_num_rows($sql);

if($cnt > 0 )
{
		while($row = imw_fetch_object($sql))
		{
				$confirmationID	=	$row->patientConfirmationId;
				$objManageData->calculateCost($confirmationID);
				
				$tblName = '';
				if($row->LaserStatus == 1)
				{
					$tblName	=	'laser_procedure_patient_table';
				}
				elseif($row->InjStatus == 1)
				{
					$tblName	=	'injection';
				}
				elseif($row->OPStatus == 1)
				{
					$tblName	=	'operatingroomrecords';
				}
				
				if($tblName)
				{
					imw_query("Update ".$tblName." Set start_time_status = '0'  Where confirmation_id = '".$confirmationID."' ");
				}
		}
}

$message	=	$cnt." Record(s) Updated ";
if($msgInfo) $message	=	$msgInfo;

$flName = "logs/log_surgery_cost_calculations_".date('m_d_Y',strtotime($dateTime)).'.txt';
file_put_contents($flName,$message);
$daysBack30 = date('m_d_Y',strtotime($dateTime.'-30 days'));

$delFlName = "logs/log_surgery_cost_calculations_".$daysBack30.'.txt';
if(file_exists($delFlName)) {
	unlink($delFlName);
}

?>