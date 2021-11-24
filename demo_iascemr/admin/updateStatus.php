<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php

include_once("../common/functions.class.php");

$objManageData	=		new Functions();

$UF		=		isset($_REQUEST['UF'])		?	$_REQUEST['UF']		:	''	;
$UV		=		isset($_REQUEST['UV'])		?	$_REQUEST['UV']		:	''	;									
$TN		=		isset($_REQUEST['TN'])		?	$_REQUEST['TN']		:	''	;
$UO		=		isset($_REQUEST['UO'])	?	$_REQUEST['UO']		:	''	;
$UOV	=		isset($_REQUEST['UOV'])	?	$_REQUEST['UOV']	:	''	;

$return	=		array();
$return['success']	=	0 ;


 if( !empty($UF) && !empty($TN) && !empty($UO) && $UOV > 0  )
 {
	 	unset($arrayRecord);
		
		$arrayRecord[$UF]	=	$UV	;
		
	 	$res		=	$objManageData->UpdateRecord($arrayRecord, $TN, $UO, $UOV);
	 	
	 	if($res)
		{
			$return['success']	=	1;
		}
		
		else
		{
			$return['error_msg'] 	=	"Error while updation.....!! please try again." ;
		}
	 
 }
 else
 {
		$return['error_msg'] 	=	"Error while updation.....!! please try again." ;
	 
 }

echo json_encode($return);
?>