<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("../common/conDb.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;

$tblName	=	'procedureprofile';
$preOpMedKeyField=	'id';

$RID			= isset($_REQUEST['RID'])	?	$_REQUEST['RID']		:	0	;			// Record ID Additional 
$RT			= isset($_REQUEST['RT'])		?	$_REQUEST['RT']		:	''	;			//	 Record  Type preMed | postMed
$PID			= isset($_REQUEST['PID'])		?	$_REQUEST['PID']		:	0	;			//	Profile ID

$RID			=	empty($RID)	?	0	:	$RID;
$PID			=	empty($PID)		?	0	:	$PID;
$RT			=	strtolower($RT);
$RT			=	($RT == 'premed')	?	'pre'					:	($RT == 'postmed' ? 'post' : '' );
$col			=	($RT  == 'pre')			?	'preOpOrders'	:	($RT == 'post' ? '' : '' ) ;


	if($PID > 0 )
		$preOpMedKeyFieldVal	=	$PID ;

	if($RID > 0  && $PID > 0 && !empty($col) )
	{
			$laserDetails	=		$objManageData->getRowRecord($tblName, $preOpMedKeyField, $PID );
			$Medication		= 		$laserDetails->$col;
			$arrayMed		=		explode(",",$Medication);
			
			if(($key = array_search($RID, $arrayMed)) !== false) 
			{
    			unset($arrayMed[$key]);
			}
			
			$updateArray[$col]	=	implode(",",$arrayMed);
			
			$objManageData->UpdateRecord($updateArray, $tblName, $preOpMedKeyField,$PID )	;
		
	}
	
	include $RT.'op_order_sheet.php';
	
	
?>