<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "ALTER TABLE `chart_drawings` ADD `draw_type` INT(2) NOT NULL ;";
if(!empty($GLOBALS["CHK_ARCHIVE_TABLE"])){
$s[] = "ALTER TABLE `chart_drawings_archive` ADD `draw_type` INT(2) NOT NULL ;";
}

//
$sql = "SELECT * FROM superbill_test 
	where test = 'Ophthalmoscopy Optic Nerve & Macula'
	";
$row = sqlQuery($sql);
if($row==false){
	$sql = "INSERT INTO `superbill_test` (`id`, `test`, `test_type`, `tests_name_pkid`) VALUES (NULL, 'Ophthalmoscopy Optic Nerve & Macula', '0', '0');";
	$inId = sqlInsert($sql);
	/*
	if(!empty($inId)){
		$sql = "INSERT INTO `superbill_test_cpt` (`id`, `superbill_test_id`, `practice_cpt`, `ins_commercial`, `ins_medicare`, `site`, `custom_test_variation_id` ) VALUES 
				(NULL, '".$inId."', '92201', '1', '1', 'OU', '0');";
		$row = sqlQuery($sql);
	}*/
}

//
$sql = "SELECT * FROM superbill_test 
	where test = 'Ophthalmoscopy Retina drawing and scleral depression'
	";
$row = sqlQuery($sql);
if($row==false){
	$sql = "INSERT INTO `superbill_test` (`id`, `test`, `test_type`, `tests_name_pkid`) VALUES (NULL, 'Ophthalmoscopy Retina drawing and scleral depression', '0', '0');";
	$inId = sqlInsert($sql);
	/*
	if(!empty($inId)){
		$sql = "INSERT INTO `superbill_test_cpt` (`id`, `superbill_test_id`, `practice_cpt`, `ins_commercial`, `ins_medicare`, `site`, `custom_test_variation_id` ) VALUES 
				(NULL, '".$inId."', '92202', '1', '1', 'OU', '0');";
		$row = sqlQuery($sql);
	}
	*/
}

//DELETE OLD Ophthalmoscopy and Ophth Ext. Sub.
$s[] = "DELETE FROM superbill_test 
	where test = 'Ophthalmoscopy' OR test = 'Ophth Ext. Sub.'
	";

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 85</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>