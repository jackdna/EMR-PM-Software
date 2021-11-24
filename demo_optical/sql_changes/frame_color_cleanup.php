<?php 
/*
 * Purpose: Remove leading and trailing Quotes from frame Color Name
*/

$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$errors = array();

$sql = 'UPDATE `in_frame_color` SET `color_name`=TRIM(BOTH \'"\' FROM `color_name`) WHERE `color_name` LIKE \'"%"\'';

$count = 0;
if( imw_query($sql) ){
	$count = imw_affected_rows();
}

echo '<div><br><br>'.$count.' Record(s) Updated.</div>';