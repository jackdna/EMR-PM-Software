<?php

$configs['pId'] = "FAD67E74-BB42-45A8-93A0-DD6F5B00768E";
/*
$configs['kaId'] = "KA0426053";
$configs['szipcode'] = "07728";
$configs['bzipcode'] = "07728";
*/

/*Get Credentials for the selected facility*/
$cred_qry	= "SELECT `frame_user_id`, `szip_code`, `bzip_code` FROM `in_frames_data` LIMIT 1";
$cred_resp	= imw_query($cred_qry);

if( $cred_resp && imw_num_rows($cred_resp) > 0 ){
	
	$cred_row = imw_fetch_object($cred_resp);
	$configs['kaId']	= $cred_row->frame_user_id;
	$configs['szipcode']= $cred_row->szip_code;
	$configs['bzipcode']= $cred_row->bzip_code;
}
