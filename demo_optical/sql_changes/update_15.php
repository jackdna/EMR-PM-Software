<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$rs=imw_query("ALTER TABLE  `in_lens_progressive` ADD  `prac_code` INT( 11 ) NOT NULL AFTER  `progressive_name`") or die(imw_error());

if($rs){
	echo 'Query Executed Successfuly';
}else{
	echo 'Error in Query.<br>'.$rs;
}

?>