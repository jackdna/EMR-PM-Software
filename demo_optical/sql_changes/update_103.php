<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$mat_qry=imw_query("SELECT GROUP_CONCAT( `id` ) as m_id FROM in_lens_material GROUP BY vw_code HAVING count( vw_code ) >1");
while($mat_row=imw_fetch_array($mat_qry)){
	$m_id_arr[]=$mat_row['m_id'];
}
$m_id_str=implode(',',$m_id_arr);

$mat_qry=imw_query("SELECT GROUP_CONCAT( `id` ) as d_id FROM in_lens_design GROUP BY vw_code HAVING count( vw_code ) >1");
while($mat_row=imw_fetch_array($mat_qry)){
	$d_id_arr[]=$mat_row['d_id'];
}
$d_id_str=implode(',',$d_id_arr);

imw_query("delete from in_lens_material where id in ($m_id_str)");
imw_query("delete from `in_lens_material_design` WHERE `material_id` IN ($m_id_str)");
imw_query("delete from `in_lens_ar_material` WHERE `material_id` IN ($m_id_str)");

imw_query("delete from in_lens_design where id in ($d_id_str)");
imw_query("delete from `in_lens_material_design` WHERE `design_id` IN ($d_id_str)");



echo '<div style="color:green;"><br><br>Duplicate Design and Material deleted successfully...</div>';	
?>