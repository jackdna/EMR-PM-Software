<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
	session_start();
	require_once('conDb.php'); 
	require_once('../admin/classObjectFunction.php'); 
	
	$JSONReturn		=	array();
	
	$JSONReturn['success']	=	false;
	
	$obj			=	new manageData();
	
	$recordID		=	isset($_POST['recordID'])	?	$_POST['recordID']	:	'0'	;
	
	
	if($recordID > 0) 
	{
			
		$response	=	$obj->DeleteRecord('scan_upload_tbl', 'scan_upload_id', $recordID);
		
		if($response)
		{
			$JSONReturn['success']	=	true	;
			
		}
	}
	echo json_encode($JSONReturn);
		
?>