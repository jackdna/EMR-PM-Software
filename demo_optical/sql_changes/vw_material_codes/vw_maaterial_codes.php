<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../../config/config.php");

$error = array();

if(file_exists('./MaterialsVCodes.csv')){
	
	/*Data Data type for Prac Code Field*/
	imw_query("ALTER TABLE `in_lens_material` CHANGE `prac_code` `prac_code` VARCHAR(255) NOT NULL") or $error[] = imw_error();
	
	$optical_prac_codes = array();
	$cpt_categories = array();
	$prac_cateegory_resp = imw_query("SELECT `cpt_cat_id` FROM `cpt_category_tbl` WHERE `cpt_category` LIKE '%optical%'");
	if($prac_cateegory_resp && imw_num_rows($prac_cateegory_resp)>0){
		while($row = imw_fetch_object($prac_cateegory_resp)){
			array_push($cpt_categories, $row->cpt_cat_id);
		}
	}
	imw_free_result($prac_cateegory_resp);
	$cpt_categories = implode(",",$cpt_categories);
	
	$prac_resp = imw_query("SELECT `cpt_fee_id`, `cpt_prac_code` FROM `cpt_fee_tbl` WHERE `cpt_cat_id` IN(".$cpt_categories.") AND `status`='active' AND `delete_status` = '0'");
	if($prac_resp && imw_num_rows($prac_resp)>0){
		
		while($row = imw_fetch_object($prac_resp)){
			$optical_prac_codes[$row->cpt_prac_code] = $row->cpt_fee_id;
		}
	}
	
	$fp = fopen('./MaterialsVCodes.csv', "r");
	while($line = fgetcsv($fp)){
		
		if($line[2]!=""){
			
			$material_row = imw_query("SELECT `id` FROM `in_lens_material` WHERE `vw_code`='".$line[1]."' AND `del_status`!=2") or $error[] = imw_error();
			if($material_row && imw_num_rows($material_row)>0){
				while($row = imw_fetch_object($material_row)){
					
					$prac_codes = explode(";",$line[2]);
					array_map('trim', $prac_codes);
					$prac_ids = "";
					foreach($prac_codes as $prac_code){
						if(!isset($optical_prac_codes[$prac_code]))
							continue;
						$prac_ids .= $optical_prac_codes[$prac_code].";";
					}
					$prac_ids = rtrim($prac_ids, ";");
					$material_update = imw_query("UPDATE `in_lens_material` SET `material_name`='".$line[0]."', `prac_code`='".$prac_ids."' WHERE `id`='".$row->id."'") or $error[] = imw_error();
				}
			}
		}
	}
	fclose($fp);
}
else{
	$error[] = "CSV file does not exists.";
}

if(count($error)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $error);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update run successfully...</div>';	
}

?>