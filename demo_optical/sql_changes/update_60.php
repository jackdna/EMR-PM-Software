<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$sql[] = "CREATE TABLE `in_prac_codes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `module_id` int(10) NOT NULL,
  `sub_module` varchar(25) NOT NULL,
  `prac_code` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

$sql1="INSERT INTO `in_prac_codes` (`id`, `module_id`, `sub_module`, `prac_code`) VALUES
(1, 1, '', ''),
(2, 2, 'type', ''),
(3, 2, 'material', ''),
(4, 2, 'ar', ''),
(5, 2, 'transition', ''),
(6, 2, 'polarized', ''),
(7, 2, 'progressive', ''),
(8, 2, 'color', ''),
(9, 2, 'tint', ''),
(10, 2, 'edge', ''),
(11, 3, 'brand', '');";

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
	echo '<div style="color:green;"><br><br>Update 60 run successfully...</div>';	
}

?>