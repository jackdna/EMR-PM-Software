<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sql[] = "CREATE TABLE `in_print_option_stock` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `module_id` int(10) NOT NULL,
  `option_chk` varchar(25) NOT NULL,
  `status` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=64;";

$sql1 = "INSERT INTO `in_print_option_stock` (`id`, `module_id`, `option_chk`, `status`) VALUES
(1, 1, 'upc_chk', 1),
(2, 1, 'mf_chk', 1),
(3, 1, 'type_chk', 1),
(4, 1, 'ven_chk', 1),
(5, 1, 'wholesale_chk', 0),
(6, 1, 'retail_chk', 1),
(7, 1, 'qnt_chk', 1),
(8, 1, 'colr_chk', 1),
(9, 1, 'brnd_chk', 1),
(10, 1, 'shp_chk', 0),
(11, 1, 'styl_chk', 0),
(12, 1, 'gender_chk', 1),
(13, 2, 'upc_chk', 1),
(14, 2, 'mf_chk', 1),
(15, 2, 'type_chk', 1),
(16, 2, 'ven_chk', 1),
(17, 2, 'wholesale_chk', 0),
(18, 2, 'retail_chk', 1),
(19, 2, 'qnt_chk', 1),
(20, 2, 'colr_chk', 1),
(21, 2, 'lens_focl_chk', 1),
(22, 2, 'lens_mate_chk', 0),
(23, 2, 'lens_a_r_chk', 0),
(24, 2, 'lens_tran_chk', 0),
(25, 2, 'lens_pol_chk', 0),
(26, 2, 'lens_edge_chk', 0),
(27, 2, 'lens_tint_chk', 0),
(28, 3, 'upc_chk', 1),
(29, 3, 'mf_chk', 1),
(30, 3, 'type_chk', 1),
(31, 3, 'ven_chk', 1),
(32, 3, 'wholesale_chk', 0),
(33, 3, 'retail_chk', 1),
(34, 3, 'qnt_chk', 1),
(35, 3, 'colr_chk', 1),
(36, 3, 'brnd_chk', 1),
(37, 3, 'cnt_len_mat_chk', 1),
(38, 3, 'cnt_len_wer_chk', 0),
(39, 3, 'cnt_len_sup_chk', 0),
(40, 6, 'upc_chk', 1),
(41, 6, 'mf_chk', 1),
(42, 6, 'type_chk', 1),
(43, 6, 'ven_chk', 1),
(44, 6, 'wholesale_chk', 0),
(45, 6, 'retail_chk', 1),
(46, 6, 'qnt_chk', 1),
(47, 6, 'med_exp_chk', 1),
(48, 5, 'upc_chk', 1),
(49, 5, 'mf_chk', 1),
(50, 5, 'type_chk', 1),
(51, 5, 'ven_chk', 1),
(52, 5, 'wholesale_chk', 0),
(53, 5, 'retail_chk', 1),
(54, 5, 'qnt_chk', 1),
(55, 5, 'suply_mnt_chk', 1),
(56, 7, 'upc_chk', 1),
(57, 7, 'mf_chk', 1),
(58, 7, 'type_chk', 1),
(59, 7, 'ven_chk', 1),
(60, 7, 'wholesale_chk', 0),
(61, 7, 'retail_chk', 1),
(62, 7, 'qnt_chk', 1),
(63, 7, 'suply_mnt_chk', 0);";

$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)==0){
	imw_query($sql1) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 58 run successfully...</div>';	
}

?>