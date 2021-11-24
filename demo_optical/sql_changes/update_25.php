<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("CREATE TABLE `in_order_fac` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_det_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `entered_date` date NOT NULL,
  `entered_time` time NOT NULL,
  `entered_by` int(11) NOT NULL,
  `modified_date` date NOT NULL,
  `modified_time` time NOT NULL,
  `modified_by` int(11) NOT NULL,
  `del_status` int(2) NOT NULL,
  `del_date` date NOT NULL,
  `del_time` time NOT NULL,
  `del_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>