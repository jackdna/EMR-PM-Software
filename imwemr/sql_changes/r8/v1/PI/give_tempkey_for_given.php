<?php
$ignoreAuth=true;
include("../../../../config/globals.php");
set_time_limit(0);

//FETCH THOSE PATIENTS WHICH HAVE PT PORTAL GIVEN KEY AS "GIVEN" BUT KEY IS EMEPTY
$msg = array();
$q = "SELECT id FROM patient_data WHERE temp_key = '' AND temp_key_chk_val='1' LIMIT 0,100000";
$res = imw_query($q);
if($res && imw_num_rows($res)>0){
	while($rs = imw_fetch_assoc($res)){
		$pid		= $rs['id'];
		$new_pt_tempKey = temp_key_gen();
		$qry = "UPDATE patient_data SET temp_key = '".$new_pt_tempKey."' WHERE id = '".$pid."' LIMIT 1";
		imw_query($qry);
	}
	$msg[] = 'Loop End. Please Refresh the page.';
}else{
	$msg[] = 'NO record left where temp_key empty and given is checked as "Given".';
}

echo '<div>'.implode('<br>',$msg).'</div>';