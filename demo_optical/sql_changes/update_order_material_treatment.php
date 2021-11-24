<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

/*Update Lens material and Treatment for saved orders as per new modification*/

$treatments_updated = "";
/*Treatments*/
$sql = "UPDATE 
			`in_order_lens_price_detail` 
		SET 
			`itemized_name` = CONCAT(
				SUBSTRING_INDEX(`itemized_name`, '_', 2), 
				'_', 
				SUBSTRING_INDEX(`itemized_name`, '_', -1)
			) 
		WHERE 
			`itemized_name` LIKE 'a_r_%' 
			AND (
				LENGTH(`itemized_name`) - LENGTH(
					REPLACE(`itemized_name`, '_', '')
				)
			) = 3";
if(imw_query($sql)){
	$treatments_updated = imw_affected_rows();
}

$materials_updated = "";
/*Materials*/
$sql1 = "UPDATE 
			`in_order_lens_price_detail` 
		 SET 
			`itemized_name` = CONCAT(
				SUBSTRING_INDEX(`itemized_name`, '_', 1), 
				'_', 
				SUBSTRING_INDEX(`itemized_name`, '_', -1)
			) 
		 WHERE 
			`itemized_name` LIKE 'material_%' 
			AND (
				LENGTH(`itemized_name`) - LENGTH(
					REPLACE(`itemized_name`, '_', '')
				)
			) = 2";
if(imw_query($sql1)){
	$materials_updated = imw_affected_rows();
}

/*Materials Pending*/
$mat_pending = imw_query("SELECT 
								count(`id`) AS 'count'
							FROM 
								`in_order_lens_price_detail` 
							WHERE 
								`itemized_name` LIKE 'material_%' 
								AND (
									`itemized_name` LIKE '%;%' 
									OR (
										LENGTH(`itemized_name`) - LENGTH(
											REPLACE(`itemized_name`, '_', '')
										)
									)> 1
								)");
$mat_pending = imw_fetch_assoc($mat_pending);
$mat_pending = $mat_pending['count'];

/*Treatments Pending*/
$treat_pending = imw_query("SELECT 
								count(`id`) AS 'count' 
							FROM 
								`in_order_lens_price_detail` 
							WHERE 
								`itemized_name` LIKE 'a_r_%' 
								AND (
									`itemized_name` LIKE '%;%' 
									OR (
										LENGTH(`itemized_name`) - LENGTH(
											REPLACE(`itemized_name`, '_', '')
										)
									) > 2
								)");
$treat_pending = imw_fetch_assoc($treat_pending);
$treat_pending = $treat_pending['count'];

echo '<div style="color:green;">';
	echo '<br>Update run successfully...<br><br>';
	echo 'Treatments Updated: '.$treatments_updated.'<br>';
	echo 'Materials Updated: '.$materials_updated;
echo '</div><br><br><br>';	

if($treat_pending>0 || $mat_pending>0){
	echo '<div style="color:red;">';
		echo "Data to be Updated Manualy";
		echo 'Treatments : '.$treat_pending.'<br>';
		echo 'Materials : '.$mat_pending;
	echo '</div><br><br>';	
}
?>