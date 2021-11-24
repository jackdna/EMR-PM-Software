<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
	
	require_once('functions.class.php'); 
	
	$object				=	new Functions();
	
	$pConfirmId	=	isset($_POST['PCI'])			?	$_POST['PCI']		:	''		;	// Patient Confirmation ID
	$request			=	isset($_POST['request'])	?	$_POST['request']	:	''		;	// request
	$unfinalize		=	$request == 'unfinalize'	?	true	:	false ; 
	
	$JSONReturn						=	array();
	$JSONReturn['success']	=	0;
	
	if($request == 'unfinalize')
	{
			$res	=	$object->unfinalizeChart($pConfirmId);
			if($res)	$JSONReturn['success']	=	1;
	}
	else if($request == 'history')
	{
			$res	=	$object->unfinalizeHistory($pConfirmId);
	
			if($res)
			{
					$JSONReturn['success']	=	1;	
					$JSONReturn['data']			=	 $object->returnData;
			}
			
	}
	
	echo json_encode($JSONReturn);
	
?>