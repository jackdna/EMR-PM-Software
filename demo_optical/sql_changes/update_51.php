<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql[] = "ALTER TABLE in_frame_styles ADD prac_code VARCHAR(1000) NOT NULL AFTER style_name";
$sql[] = "ALTER TABLE in_frame_styles CHANGE prac_code prac_code INT(12) NOT NULL";

$err = array();
foreach($sql as $qry){
	imw_query($qry) or $err[]=imw_error();
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 51 run successfully...</div>';	
}

?>