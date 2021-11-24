<?php
$ignoreAuth=true;
include("../../../../config/globals.php");
set_time_limit(0);


if(!$_REQUEST['auto_refresh']){
	//Chech If username, password, Temp key is empty
	$sqlQry = imw_query("Update patient_data SET temp_key_chk_val = 0 WHERE temp_key = '' AND (username is NULL || username = '') AND (password is NULL || password = '') AND temp_key_chk_val = 1 ");
	if($sqlQry) $msg[] = 'All Patients with empty Temp key and Login credentials has been reset.';
}
$refreshPage=0;
$msg = array();
$q = "SELECT id FROM patient_data WHERE temp_key = '' AND username is NULL AND password is NULL LIMIT 0,100000";		
$res = imw_query($q);
if($res && imw_num_rows($res)>0){
	while($rs = imw_fetch_assoc($res)){
		$pid		= $rs['id'];
		$new_pt_tempKey = temp_key_gen();
		$qry = "UPDATE patient_data SET temp_key = '".$new_pt_tempKey."', temp_key_expire='', username='', password='', preferred_image = '',temp_key_chk_val='' WHERE id = '".$pid."' LIMIT 1";
		imw_query($qry);
	}
	$msg[] = 'Do not close page untill process completed message appear.';
	$refreshPage=1;
}else{
	$msg[] = 'Process Completed. NO record left where temp_key is not assigned.';
	$refreshPage=0;
}

echo '<div>'.implode('<br>',$msg).'</div>';
if($refreshPage==1)
{
	echo'<script type="text/javascript">window.location.href="give_tempkey.php?auto_refresh=on";</script>';
}
?>
