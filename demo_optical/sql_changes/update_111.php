<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$error = array();

$sql[]="INSERT INTO `in_lens_type_vcode` (`id`, `lens_type_id`, `lens_type`, `prac_code`, `prac_id`, `sph_plus_from`, `sph_plus_to`, `sph_min_from`, `sph_min_to`, `cyl_from`, `cyl_to`, `wholesale_price`, `purchase_price`, `retail_price`) VALUES
(1, 3, 'Single Vision', 'V2100', 0, '0', '99', '0', '-99', '0', '99',0.00, 0.00, 0.00),
(2, 1, 'BiFocal', 'V2200', 0, '0', '99', '0', '-99', '0', '99',0.00, 0.00, 0.00),
(3, 4, 'TriFocal', 'V2314', 0, '0', '99', '0', '-99', '0', '99',0.00, 0.00, 0.00),
(4, 2, 'Progressive', 'V2100', 0, '0', '99', '0', '-99', '0', '99',0.00, 0.00, 0.00);";

foreach($sql as $qry){
	imw_query($qry) or $error[] = imw_error();
}

$proc_code_arr=array();
$sql = "select cpt_cat_id from cpt_category_tbl where cpt_category like '%optical%' order by cpt_category ASC";
$rez = imw_query($sql);
$row=imw_fetch_array($rez);
$cat_id=$row['cpt_cat_id'];

$sql = "select cpt_fee_id,cpt_prac_code,cpt4_code from cpt_fee_tbl WHERE status='active' AND delete_status = '0' order by cpt_prac_code ASC";
$rezCodes = imw_query($sql);
while($rowCodes=imw_fetch_array($rezCodes)){
	$prac_code_arr[$rowCodes['cpt_prac_code']]=$rowCodes['cpt_fee_id'];
	$cpt4_code_arr[$rowCodes['cpt4_code']]=$rowCodes['cpt_fee_id'];
}
$vcode_qry=imw_query("select id,lens_type,lens_type_id,prac_code from in_lens_type_vcode where prac_id='0'");
while($vcode_row=imw_fetch_array($vcode_qry)){
	$prac_code_id=0;
	if($prac_code_arr[$vcode_row['prac_code']]>0){
		$prac_code_id=$prac_code_arr[$vcode_row['prac_code']];
	}
	if($cpt4_code_arr[$vcode_row['prac_code']]>0 && $prac_code_id==0){
		$prac_code_id=$cpt4_code_arr[$vcode_row['prac_code']];
	}
	if($prac_code_id>0){
		imw_query("update in_lens_type_vcode set prac_id='$prac_code_id' where prac_code='".$vcode_row['prac_code']."'");
	}else{
		imw_query("insert into cpt_fee_tbl set status='Active',cpt4_code='".$vcode_row['prac_code']."',cpt_prac_code='".$vcode_row['prac_code']."',cpt_desc='".$vcode_row['prac_code']."',cpt_cat_id='".$cat_id."'");
		$cpt_insert_id=imw_insert_id();
		imw_query("update in_lens_type_vcode set prac_id='$cpt_insert_id' where prac_code='".$vcode_row['prac_code']."'");
		imw_query("insert into cpt_fee_table set cpt_fee_id='$cpt_insert_id',fee_table_column_id='1',cpt_fee='0'");
	}
}

$lens_qry=imw_query("select id,type_name from in_lens_type where del_status='0'");
while($lens_row=imw_fetch_array($lens_qry)){
	imw_query("update in_lens_type_vcode set lens_type_id='".$lens_row['id']."' where lens_type='".$lens_row['type_name']."'");
}

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $error);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 111 run successfully...</div>';	
}
?>

