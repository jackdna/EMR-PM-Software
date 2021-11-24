<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();

/*Table for Retail Price Markup*/
$sql_table = "CREATE TABLE `in_frames_data` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `frame_user_id` VARCHAR(255) NOT NULL,
  `szip_code` VARCHAR(255) NOT NULL,
  `bzip_code` VARCHAR(255) NOT NULL,
  `entered_by` INT(11) NOT NULL,
  `entered_data_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` INT(11) NOT NULL,
  `modified_data_time` TIMESTAMP NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM";

if( imw_query($sql_table) ){
	/*Add an entry*/	
	$sql = "INSERT INTO `in_frames_data`(`id`) VALUES(1)";
	imw_query($sql) or $errors[] = imw_error();
}
else{
	$errors[] = imw_error();
}

/*Mofifications for Frames Data credentials practice Specific*/
$sqlDelFAcility = 'ALTER TABLE `in_frames_data` DROP COLUMN `facility_id`';
if( imw_query($sqlDelFAcility) ){
	
	/*Copy all the data to the first Record*/
	$sqlGetData = "SELECT `frame_user_id`, `szip_code`, `bzip_code`, `entered_by`, `entered_data_time`, `modified_by`, `modified_data_time` FROM `in_frames_data` WHERE `frame_user_id` != '' AND `szip_code` != '' AND `bzip_code` != '' AND `id`!=1 LIMIT 1";
	$dataResp = imw_query($sqlGetData);
	
	if($dataResp && imw_num_rows($dataResp)==1){
		$dataVals = imw_fetch_object($dataResp);
		
		$sqlCopyData = "UPDATE `in_frames_data`
						SET
							`frame_user_id`='".$dataVals->frame_user_id."',
							`szip_code`='".$dataVals->szip_code."',
							`bzip_code`='".$dataVals->bzip_code."',
							`entered_by`='".$dataVals->entered_by."',
							`entered_data_time`='".$dataVals->entered_data_time."',
							`modified_by`='".$dataVals->modified_by."',
							`modified_data_time`='".$dataVals->modified_data_time."'
						WHERE
							`id`=1";
		imw_query($sqlCopyData) or $errors[] = imw_error();
		/*Delete additional records and keep only one */
		$sqlDelRecords = 'DELETE FROM `in_frames_data` WHERE `id`!=1';
		imw_query($sqlDelRecords) or $errors[] = imw_error();
	}
}
else
	$errors[] = imw_error();


if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 123 run successfully...</div>';
}

?>