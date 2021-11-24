<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

imw_query("ALTER TABLE  `in_item` ADD  `purchase_price` DOUBLE NOT NULL AFTER  `wholesale_cost`") or $err[]=imw_error();


if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 95 run successfully...</div>';	
}

?>