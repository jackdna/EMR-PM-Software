<?php 	
/*this column added because previousely we were comparing data on behalf of number but this value is now being duplicate veom VW so we introduced another check field that is ID coming along with number field for lens lab details*/
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$qry = "ALTER TABLE  `in_lens_lab_detail` ADD  `ac_id` BIGINT NOT NULL";
imw_query($qry) or $errors[] = imw_error();

if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
    echo '<div style="color:green;"><br><br>Update 146: successfull</div>';
}

?>
