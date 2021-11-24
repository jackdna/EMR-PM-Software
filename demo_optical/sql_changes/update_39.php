<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sel_loc = imw_query("select id from in_location where del_status='0'")  or die(imw_error());
if(imw_num_rows($sel_loc)==0)
{
	$sel_fac = imw_query("select pos_facility_id from pos_facilityies_tbl order by headquarter desc");
	$get_pos_id = imw_fetch_array($sel_fac);
	$pos_id = $get_pos_id['pos_facility_id'];
	$rs=imw_query("insert into in_location(loc_name,pos)values('iMW','$pos_id')") or die(imw_error());
}
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>