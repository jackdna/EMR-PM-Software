<?php
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");
$error = array();
$msg = array();

$sql0 = "ALTER TABLE `in_prac_codes` ADD COLUMN `sub_module_label` VARCHAR(25) NOT NULL AFTER `sub_module`";
$resp0 = imw_query($sql0) or $error[] = imw_error();
if($resp0){
	
	$sql1 = "UPDATE `in_prac_codes` SET `sub_module_label`=`sub_module`";
	$reps1 = imw_query($sql1) or $error[] = imw_error();
	if(imw_affected_rows()>0){
		
		$msg[] = "Module labels created successfully.";
		
		$sql2 = "UPDATE `in_prac_codes` SET `sub_module_label`='Design/Type' WHERE `module_id`='2' AND `sub_module_label`='type'";
		$resp2 = imw_query($sql2) or $error[] = imw_error();
		if(imw_affected_rows()==0){
			$error[] = "Type label already changed";
		}
		else{
			$msg[] = "Type labels changed successfully.";
		}
		
		$sql3 = "UPDATE `in_prac_codes` SET `sub_module_label`='Treatment' WHERE `module_id`='2' AND `sub_module_label`='coating'";
		$resp3 = imw_query($sql3) or $error[] = imw_error();
		if(imw_affected_rows()==0){
			$error[] = "Coating label already changed";
		}
		else{
			$msg[] = "Coating labels changed successfully.";
		}
	}
	else{
		$error[] = "Module labels already created.";
	}
}

/*Limit Seg Type or Lens Type to Types from vision web only*/
$sql0 = "UPDATE `in_lens_type` SET `del_status`='1' WHERE `type_name` NOT IN('BiFocal', 'Progressive', 'Single Vision', 'TriFocal')";
$resp0 = imw_query($sql0) or $error[] = imw_error();
if(imw_affected_rows()==0){
	$msg[] = "No type to delete.";
}

/*Insert Lens/Seg types*/
$vw_types = array('BFF'=>'BiFocal', 'PAL'=>'Progressive', 'SV'=>'Single Vision', 'TFF'=>'TriFocal');
foreach($vw_types as $key=>$value){
	
	$sql1 = "SELECT `id` FROM `in_lens_type` WHERE `type_name`='".$value."' AND `del_status`=0";
	$resp1 = imw_query($sql1);
	if(imw_num_rows($resp1)>0){
		$i = 1;
		while($row = imw_fetch_object($resp1)){
			
			if($i=1){
				imw_query("UPDATE `in_lens_type` SET `vw_code`='".$key."' WHERE `id`='".$row->id."'") or $error[] = imw_error();;
			}
			else{
				imw_query("UPDATE `in_lens_type` SET `del_status`=1 WHERE `id`='".$row->id."'") or $error[] = imw_error();;
			}
			$i++;
		}
	}
	else{
		$sql1 = "INSERT INTO `in_lens_type`(`type_name`, `vw_code`) VALUES('".$value."', '".$key."')";
		$sql1 = imw_query($sql1) or $error[] = imw_error();
	}
}
$msg[] = "VisionWeb styles insertion/updation done.";
/*End Insert Lens/Seg types*/

/*Disable Default Prac Code*/
$sql = "ALTER TABLE `in_prac_codes` ADD `del_status` INT(2) NOT NULL DEFAULT 0";
imw_query($sql) or $error[] = imw_error();
	/*Mark Status to deleted for unused Prac Codes*/
	$sql = "UPDATE `in_prac_codes` SET `del_status`=1 WHERE `module_id`=2 AND `sub_module` IN('transition', 'polarized', 'progressive', 'color', 'tint', 'edge')";
	imw_query($sql);
/*End Disable Default Prac Code*/

imw_query("ALTER TABLE `in_order_details` ADD `loc_id` INT(11) NOT NULL") or $error[] = imw_error();
imw_query("ALTER TABLE `in_order` ADD `loc_id` INT(11) NOT NULL") or $error[] = imw_error();

imw_query("ALTER TABLE `in_lens_material_design` ADD `xml_add_id` VARCHAR( 250 ) NOT NULL ");
imw_query("ALTER TABLE `in_lens_ar_material` ADD `xml_add_id` VARCHAR( 250 ) NOT NULL ");
imw_query("CREATE TABLE `in_vw_order_status_detail` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`vw_order_id` VARCHAR( 250 ) NOT NULL ,
`vw_status` INT( 11 ) NOT NULL ,
`vw_received_date` DATETIME NOT NULL
) ENGINE = MYISAM ;");

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $error);
	print "</pre></div>";
	
	echo '<div style="color:green;"><br><br<pre>';
	echo implode("<br>", $msg);
	echo '</pre></div>';
}
else{
	echo '<div style="color:green;"><br><br>Update 97 run successfully...<pre';
	echo implode("<br>", $msg);
	echo '</pre></div>';	
	
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $error);
	print "</pre></div>";
}
?>