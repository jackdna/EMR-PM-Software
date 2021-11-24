<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$error = array();

$sql[]="INSERT INTO `in_lens_type_vcode` (`id`, `lens_type_id`, `lens_type`, `prac_code`, `prac_id`, `sph_plus_from`, `sph_plus_to`, `sph_min_from`, `sph_min_to`, `cyl_from`, `cyl_to`, `del_status`, `entered_date`, `entered_time`, `entered_by`, `modified_date`, `modified_time`, `modified_by`, `del_date`, `del_time`, `del_by`) VALUES
(1, 3, 'Single Vision', 'V2100', 0, '0', '4', '0', '-4', '0', '0', 0, '2016-07-04', '01:00:34', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(2, 3, 'Single Vision', 'V2101', 0, '4.12', '7', '-4.12', '-7', '0', '0', 0, '2016-07-04', '01:01:09', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(3, 3, 'Single Vision', 'V2102', 0, '7.12', '20', '-7.12', '-20', '0', '0', 0, '2016-07-04', '01:02:21', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(4, 3, 'Single Vision', 'V2103', 0, '0', '4', '0', '-4', '0.12', '2', 0, '2016-07-04', '01:03:11', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(5, 3, 'Single Vision', 'V2104', 0, '0', '4', '0', '-4', '2.12', '4', 0, '2016-07-04', '01:06:52', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(6, 3, 'Single Vision', 'V2105', 0, '0', '4', '0', '-4', '4.25', '6', 0, '2016-07-04', '01:07:29', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(7, 3, 'Single Vision', 'V2106', 0, '0', '4', '0', '-4', '6.12', '20', 0, '2016-07-04', '01:08:03', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(8, 3, 'Single Vision', 'V2107', 0, '4.25', '7', '-4.25', '-7', '0.12', '2', 0, '2016-07-04', '01:08:38', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(9, 3, 'Single Vision', 'V2108', 0, '4.25', '7', '-4.25', '-7', '2.12', '4', 0, '2016-07-04', '01:09:32', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(10, 3, 'Single Vision', 'V2109', 0, '4.25', '7', '-4.25', '-7', '4.25', '6', 0, '2016-07-04', '01:10:01', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(11, 3, 'Single Vision', 'V2110', 0, '4.25', '7', '-4.25', '-7', '6.12', '20', 0, '2016-07-04', '01:10:29', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(12, 3, 'Single Vision', 'V2111', 0, '7.25', '12', '-7.25', '-12', '0.25', '2.25', 0, '2016-07-04', '01:11:32', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(13, 3, 'Single Vision', 'V2112', 0, '7.25', '12', '-7.25', '-12', '2.25', '4', 0, '2016-07-04', '01:12:09', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(14, 3, 'Single Vision', 'V2113', 0, '7.25', '12', '-7.25', '-12', '4.25', '6', 0, '2016-07-04', '01:12:33', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(15, 3, 'Single Vision', 'V2114', 0, '12.12', '99', '-12.12', '-99', '0.12', '20', 0, '2016-07-04', '01:13:05', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(16, 1, 'BiFocal', 'V2200', 0, '0', '4', '0', '-4', '0', '0', 0, '2016-07-04', '01:14:16', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(17, 1, 'BiFocal', 'V2201', 0, '4.12', '-', '-4.12', '-7', '0', '0', 0, '2016-07-04', '01:14:38', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(18, 1, 'BiFocal', 'V2202', 0, '7.12', '20', '-7.12', '-20', '0', '0', 0, '2016-07-04', '01:15:04', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(19, 1, 'BiFocal', 'V2203', 0, '0', '4', '0', '-4', '0.12', '2', 0, '2016-07-04', '01:15:32', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(20, 1, 'BiFocal', 'V2204', 0, '0', '4', '0', '-4', '2.12', '4', 0, '2016-07-04', '01:15:52', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(21, 1, 'BiFocal', 'V2205', 0, '0', '4', '0', '-4', '4.25', '6', 0, '2016-07-04', '01:16:31', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(22, 1, 'BiFocal', 'V2206', 0, '0', '4', '0', '-4', '6.12', '20', 0, '2016-07-04', '01:17:09', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(23, 1, 'BiFocal', 'V2207', 0, '4.25', '7', '-4.25', '-7', '0.12', '2', 0, '2016-07-04', '01:18:00', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(24, 1, 'BiFocal', 'V2208', 0, '4.25', '7', '-4.25', '-7', '2.12', '4', 0, '2016-07-04', '01:18:26', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(25, 1, 'BiFocal', 'V2209', 0, '4.25', '7', '-4.25', '-7', '4.25', '6', 0, '2016-07-04', '01:18:50', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(26, 1, 'BiFocal', 'V2210', 0, '4.25', '7', '-4.25', '-7', '6.12', '20', 0, '2016-07-04', '01:19:15', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(27, 1, 'BiFocal', 'V2211', 0, '7.25', '12', '-7.25', '-12', '0.25', '2.25', 0, '2016-07-04', '01:19:40', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(28, 1, 'BiFocal', 'V2212', 0, '7.25', '12', '-7.25', '-12', '2.25', '4', 0, '2016-07-04', '01:20:08', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(29, 1, 'BiFocal', 'V2213', 0, '7.25', '12', '-7.25', '-12', '4.25', '6', 0, '2016-07-04', '01:20:36', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(30, 1, 'BiFocal', 'V2214', 0, '12.12', '99', '-12.12', '-99', '0.12', '20', 0, '2016-07-04', '01:21:00', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(31, 4, 'TriFocal', 'V2300', 0, '0', '4', '0', '-4', '0', '0', 0, '2016-07-04', '01:22:03', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(32, 4, 'TriFocal', 'V2301', 0, '4.12', '7', '-4.12', '-7', '0', '0', 0, '2016-07-04', '01:22:28', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(33, 4, 'TriFocal', 'V2302', 0, '7.12', '20', '-7.12', '-20', '0', '0', 0, '2016-07-04', '01:23:15', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(34, 4, 'TriFocal', 'V2303', 0, '0', '4', '0', '-4', '0.12', '2', 0, '2016-07-04', '01:23:39', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(35, 4, 'TriFocal', 'V2304', 0, '0', '4', '0', '-4', '2.12', '4', 0, '2016-07-04', '01:24:37', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(36, 4, 'TriFocal', 'V2305', 0, '0', '4', '0', '-4', '4.25', '6', 0, '2016-07-04', '01:25:03', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(37, 4, 'TriFocal', 'V2306', 0, '0', '4', '0', '-4', '6.12', '20', 0, '2016-07-04', '01:25:21', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(38, 4, 'TriFocal', 'V2307', 0, '4.25', '7', '-4.25', '-7', '0.12', '2', 0, '2016-07-04', '01:25:53', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(39, 4, 'TriFocal', 'V2308', 0, '4.25', '7', '-4.25', '-7', '2.12', '4', 0, '2016-07-04', '01:26:34', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(40, 4, 'TriFocal', 'V2309', 0, '4.25', '7', '-4.25', '-7', '4.25', '6', 0, '2016-07-04', '01:27:05', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(41, 4, 'TriFocal', 'V2310', 0, '4.25', '7', '-4.25', '-7', '6.12', '20', 0, '2016-07-04', '01:27:25', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(42, 4, 'TriFocal', 'V2311', 0, '7.25', '12', '-7.25', '-12', '0.25', '2.25', 0, '2016-07-04', '01:27:51', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(43, 4, 'TriFocal', 'V2312', 0, '7.25', '12', '-7.25', '-12', '2.25', '4', 0, '2016-07-04', '01:29:01', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(44, 4, 'TriFocal', 'V2313', 0, '7.25', '12', '-7.25', '-12', '4.25', '6', 0, '2016-07-04', '01:29:28', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0),
(45, 4, 'TriFocal', 'V2314', 0, '12.12', '99', '-12.12', '-99', '0.12', '20', 0, '2016-07-04', '01:29:49', 1, '0000-00-00', '00:00:00', 0, '0000-00-00', '00:00:00', 0);";

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
