<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("CREATE TABLE IF NOT EXISTS `in_upc_no` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upc_num` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


$rs2=imw_query("INSERT INTO `in_upc_no` (`id`, `upc_num`) VALUES(1, 10000)");

if($rs || $rs2){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>