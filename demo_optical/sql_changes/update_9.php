<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("CREATE TABLE `in_contact_style` (`id` int(11) NOT NULL AUTO_INCREMENT,`style_name` text NOT NULL, `del_status` tinyint(1) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;") or die(imw_error());
if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>