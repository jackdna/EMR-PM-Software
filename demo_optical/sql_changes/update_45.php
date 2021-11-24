<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("CREATE TABLE IF NOT EXISTS `in_medicines_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(300) NOT NULL,
  `entered_date` date NOT NULL,
  `entered_time` time NOT NULL,
  `entered_by` int(11) NOT NULL,
  `modified_date` date NOT NULL,
  `modified_time` time NOT NULL,
  `modified_by` int(11) NOT NULL,
  `del_status` tinyint(1) NOT NULL,
  `del_date` date NOT NULL,
  `del_time` time NOT NULL,
  `del_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1") or die(imw_error());

if($rs){
	echo '<b><em>Query Executed Successfuly</em></b>';
}else{
	echo '<b><em>Error in Query.</em></b><br>'.$rs;
}
?>