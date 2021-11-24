<?php 
/*************************************************************************************************/
/*Due to update check failure since 2015, many duplicate records were enter in table in_order_fac
/*this update is created to clean up this table by removing duplicate records
/*************************************************************************************************/
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();
$table="in_order_fac_".date('dMy');
//create back up table
//imw_query("CREATE TABLE IF NOT EXIST $table AS SELECT * FROM in_order_fac");
//check is backup created
$qCheck=imw_query("select id from $table limit 0,1");
if(imw_num_rows($qCheck)==0)
{
	die('please create back up table as `'.$table.'` for table `in_order_fac` to continue');
}
//delete duplicate records
$get=imw_query("SELECT GROUP_CONCAT( id ) as id, id as keep_ida
FROM  `in_order_fac` 
GROUP BY order_id, order_det_id, patient_id, item_id, entered_date
HAVING COUNT( id ) >1
ORDER BY COUNT( id ) DESC") or $errors[] = imw_error();
while($data=imw_fetch_object($get))
{
	$ids_arr=$data->id;
	$keep_id=$data->keep_ida;
	$del="DELETE FROM in_order_fac WHERE id!='".$keep_id."' AND id in(".$ids_arr.")";
	$res=imw_query($del) or die(imw_error());
	$total+=imw_affected_rows();
}

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 133 run successfully, '.$total.' records removed.</div>';
}

?>