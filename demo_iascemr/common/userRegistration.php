<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
	session_start();
	
	require_once('conDb.php'); 
	
	require_once('functions.class.php'); 
	
	$object			=	new Functions();
	
	$request		=	isset($_POST['request'])	?	$_POST['request']	:	'register'	;	// Request for -> register (default)
	$SID			=	isset($_POST['SID'])		?	$_POST['SID']		:	''			;	// Patient Stub ID
	$SDate			=	isset($_POST['SDate'])		?	$_POST['SDate']		:	date('Y-m-d');	// Current Selected Date - Default is current
	
	$JSONReturn		=	array(); //array($request,$SID,$PS);
	
	
	if($request == 'register')
	{
		
		$JSONReturn	=	$object->RegisterUser($SID,$SDate);
		
	}
	
	echo json_encode($JSONReturn);
		
?>