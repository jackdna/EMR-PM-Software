<?php 
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
if($_POST['batch_ids'] && $_POST['archive']==1)
{
	//udpate status to archive
	imw_query("update in_batch_table set del_status=1,
	del_by='$_SESSION[authId]',
	del_on='".date('Y-m-d H:i:s')."' 
	where id IN($_POST[batch_ids])
	AND status!='saved'
	AND del_status=0");
	echo imw_affected_rows()." records udpated";
}
elseif($_POST['batch_ids'] && $_POST['archive']==0)
{
	//udpate status to archive
	imw_query("update in_batch_table set del_status=0,
	del_by='',
	del_on='' where id IN($_POST[batch_ids])");
	echo imw_affected_rows()." records udpated";
}
?>