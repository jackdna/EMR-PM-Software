<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[] = "CREATE TABLE `in_contact_brand_manufacture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `manufacture_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
";

$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br>Update 54 run successfully...</div>';	
}

?>