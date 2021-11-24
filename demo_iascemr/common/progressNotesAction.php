<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 

	require_once('functions.class.php'); 
	
	$object			=	new Functions();
		
	$action							=	isset($_POST['action'])							?	$_POST['action']							:	''		;
	$confirmation_id			=	isset($_POST["confirmation_id"])			?	$_POST["confirmation_id"]			:	$_SESSION["pConfId"]	;
	$asc_id							=	isset($_POST['asc_id'])							?	$_POST['asc_id']							:	0				;
	$note_id						=	isset($_POST['hidd_userid'])					?	$_POST['hidd_userid']					:	0				;
	$login_user_type			=	isset($_POST['progress_UserType'])		?	$_POST['progress_UserType']		:	0				;
	$login_user_id				=	isset($_POST['hidd_progressUserId'])	?	$_POST['hidd_progressUserId']	:	0				;
	$text_note					=	isset($_POST['txtNote'])						?	$_POST['txtNote']							:	''				;
	
	$return 						=	array();
	
	$return['status']			=	'';
	
	if($action ==	'delete' )
	{
		$note_id					=		isset($_POST['noteid'])		?	$_POST['noteid']		:	0				;	
	}
	
	
	
	if(!empty($action))
	{
			$response	=		$object->ProgressNotesAction($action, $confirmation_id, $asc_id, $note_id, $login_user_type, $login_user_id, $text_note );
			
			$notesCount=		$object->ProgressNotesCount(array('confirmation_id = ' => $confirmation_id ));
			
			$return['status']		=		($response)	?	'success'	:	'fail'	;
			
			$return['count']		=		$notesCount ;
	}
	
	echo json_encode($return);
	
?>