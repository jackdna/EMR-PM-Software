<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql1="SELECT `id` FROM `in_lens_tint` WHERE `tint_type` IN('uv400', 'uv 400')";
$resp1 = imw_query($sql1);
if($resp1 && imw_num_rows($resp1)==0){
	$date = date("Y-m-d");
	$time = date("H:i:s");
	$sql2 = "INSERT INTO `in_lens_tint` 
				SET 
					`tint_type`='UV400',
					`entered_by`='1',
					`entered_date`='".$date."',
					`entered_time`='".$time."'";
	imw_query($sql2) or $err[]=imw_error();
}
else{
	$err[] = "Data Already Exists";
}

if(count($err)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("\n", $err);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 94 run successfully...</div>';	
}

?>