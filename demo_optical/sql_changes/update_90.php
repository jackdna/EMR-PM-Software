<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

/*Create Table For Disinfecting*/
$sql1="CREATE TABLE  `in_cl_disinfecting` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name` varchar(255)	NOT NULL ,
	`prac_code` int(11) NOT NULL,
	`del_status` tinyint(1) NOT NULL,
	`entered_date` date NOT NULL,
	`entered_time` time NOT NULL,
	`entered_by` int(11) NOT NULL,
	`modified_date` date NOT NULL,
	`modified_time` time NOT NULL,
	`modified_by` int(11) NOT NULL,
	`del_date` date NOT NULL,
	`del_time` time NOT NULL,
	`del_by` int(11) NOT NULL
) ENGINE = MYISAM;";

$error = array();
$success = array();
if(imw_query($sql1)){
	array_push($success, "New table for disinfecting created successfully.");
}
else{
	array_push($error, imw_error());
}

/*Insterting Disinfecting to new table created if data exists and table created*/
if(count($error)==0){
	$sql2 = imw_query("SELECT COUNT(`id`) AS 'count' FROM `in_options` WHERE `opt_type`='3' AND `module_id`='3'");
	$rows = imw_num_rows($sql2);
	if($sql2 && $rows>0){
		$data_rows = imw_fetch_assoc($sql2);
		if($data_rows['count']>0){
			array_push($success, $data_rows['count']." existing entries found for disinfecting, to be moved to new table.");
			
			$sql3 = "INSERT INTO 
								`in_cl_disinfecting`(
													`name`, `del_status`, `entered_date`, `entered_time`, `entered_by`, `modified_date`, `modified_time`, `modified_by`, `del_date`, `del_time`, `del_by`)
													SELECT `opt_val`, `del_status`, `entered_date`, `entered_time`, `entered_by`, `modified_date`, `modified_time`, `modified_by`, `del_date`, `del_time`, `del_by` FROM `in_options` WHERE `opt_type`='3' AND `module_id`='3' ORDER BY `opt_val` ASC";
			if(imw_query($sql3)){
				array_push($success, "Data successfully copied to new table.");
				
				$sql4 =  "DELETE FROM `in_options` WHERE `opt_type`= '3' AND `module_id`= '3'";
				if(imw_query($sql4)){
					array_push($success, "Existing entries for disinfecting are deleted from previous table.");
				}
				else{
					array_push($error, "Existing entries for disinfecting not deleted from previous table.");
				}
			}
			else{
				array_push($error, "Data not copied to new table.");
			}
		}
		else{
			array_push($success, "No existing entries found for disinfecting.");
		}
	}
	else{
		array_push($error, "Error in counting existing entries for disinfecting.");
	}
}


print '<div style="color:green;"><br><br>Update 90 run successfully...<br /><br /><pre>';	
print implode("\n", $success);
print "</pre></div>";

print "<div style=\"color:red;\"><br><br><pre>";
print implode("\n", $error);
print "</pre></div>";


?>