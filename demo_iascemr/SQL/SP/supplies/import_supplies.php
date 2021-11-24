<?php
/*
*	import data from CSV Files
*	increase memory_limit and max_input_vars in php.ini file 
*/

include_once '../../../common/conDb.php';

if( isset($_POST['submit_supplies']) )
{
	// Initialize variable
	$msg = $catNameArr = array();
	$csvFile = '';
	
	//validate whether uploaded file is a csv file
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
	
	
	// Taking Database backup
	imw_query("CREATE TABLE supply_categories_".date("d_m_Y")." AS (SELECT * FROM supply_categories)");
	imw_query("CREATE TABLE predefine_suppliesused_".date("d_m_Y")." AS (SELECT * FROM predefine_suppliesused)");
	
	// validate supply categories function
	function validate_cat($name) {
		
		global $catNameArr;
		
		if( $catNameArr[$name] ) return $catNameArr[$name];
		
		if($name) {
			$qry = "Select * From supply_categories Where name='".$name."' ";
			$sql = imw_query($qry);
			$cnt = imw_num_rows($sql);
			if( $sql && $cnt > 0 ) {
				$row = imw_fetch_object($sql);
				$id = $row->id;
			}
			else {
				$qry = "Insert into supply_categories Set name = '".$name."', date_created = '".date('Y-m-d H:i:s')."' ";	
				$sql = imw_query($qry);
				$id =  imw_insert_id();
			}
			
			if( $id ){
				if( !array_key_exists($name,$catNameArr) ) 
					$catNameArr[$cat_name] = $cat_id;
				
				return $id;
			}
		}
		return false;
	}
	
	// Check for CSV file
	if( isset($_POST['csv_file_name']) && $_POST['csv_file_name'] ) {
		if( file_exists($_POST['csv_file_name']) ) {
			//open uploaded csv file with read only mode
			$csvFile = fopen($_POST['csv_file_name'], 'r');
		}
	}
	elseif( !empty($_FILES['supplies_csv']['name']) && in_array($_FILES['supplies_csv']['type'],$csvMimes) ) {
		if(is_uploaded_file($_FILES['supplies_csv']['tmp_name'])){
			//open uploaded csv file with read only mode
			$csvFile = fopen($_FILES['supplies_csv']['tmp_name'], 'r');
		}
	}
	else {
		exit ('Please select valid .csv file only');
	}
	
	
	
	
	if( $csvFile)
	{
		//skip first line
		fgetcsv($csvFile);
		$counter = 0;
		while(($line = fgetcsv($csvFile)) !== FALSE){
			$counter++;
			$line[4] = str_replace(array('$','N/A'),"",trim($line[4]));
			$line[4] = ($line[4]) ? $line[4] : 0;
			$cat_name = addslashes(trim($line[1]));
			$sup_name = addslashes(trim($line[2]));
			//$mfg_name = trim($line[3]);
			$cost = $line[4];
			
			$cat_name = $cat_name ? $cat_name : 'Other';
			
			// validate supply category;
			$cat_id = validate_cat($cat_name);
			
			$qry = "Select * From predefine_suppliesused Where name = '".$sup_name."' ";
			$sql = imw_query($qry);
			$cnt = imw_num_rows($sql);
			
			$str = "Supply Name - ".$sup_name.", Category Name - ".$cat_name.", Cost - ".$cost;
			
			if( $cnt > 0 ) {
				// Update cost only
				if( $cost > 0 )
				{
					$row = imw_fetch_object($sql);
					$qry = "Update predefine_suppliesused Set supplies_cost = '".$cost."' Where suppliesUsedId = $row->suppliesUsedId ";
					$sql = imw_query($qry);	
					if( $sql )
						$msg[] = "<span style='color:green'>".$counter.") Update Success : ".$str."</span>";
					else
						$msg[] = "<span style='color:red'>".$counter.") Update  Error: ".$str."</span>";
				}
				else
					$msg[] = "<span style='color:green'>".$counter.") Already Exists: ".$str."</span>";
			}
			else {
				$qry = "Insert into predefine_suppliesused Set name = '".$sup_name."', cat_id = ".$cat_id.", qtyChkBox = 1, supplies_cost = '".$cost."' ";
				$sql = imw_query($qry);	
				if( $sql )
					$msg[] = "<span style='color:green'>".$counter.") Insert Success : ".$str."</span>";
				else
					$msg[] = "<span style='color:red'>".$counter.") Insert Error: ".$str."</span>";
			}
				
			
			
		}
	
	}
	
	echo implode('<br>',$msg);
	exit;
					
}

?>
<!DOCTYPE html>
<html lang="en">
<body>
   	
    <form name="form_supplies" method="post"  enctype="multipart/form-data">    
			<select name="csv_file_name" id="csv_file_name">
					<option value="">Select</option>
					<?php
						foreach(glob("*.csv") as $fileName)
							echo '<option value="'.$fileName.'">'.$fileName.'</option>';				
					?>
			</select>
			OR 
			<input type="file" name="supplies_csv" /><small>(Only .csv file)</small>
			<button type="submit" name="submit_supplies">Submit</button>
    </form>
</body>
</html>