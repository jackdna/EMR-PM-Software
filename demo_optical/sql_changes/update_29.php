<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("CREATE TABLE `in_return_modifier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ord_return_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `ret_reason` int(11) NOT NULL,
  `ret_status` varchar(100) NOT NULL,
  `modified_date` date NOT NULL,
  `modified_time` time NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>