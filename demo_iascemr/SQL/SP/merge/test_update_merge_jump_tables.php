<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
set_time_limit(0);
include_once("../../../common/conDb.php");  //MYSQL CONNECTION
$sv_file_name = "merge_plan.csv";
include_once("test_update_merge_db_detail.php");  //DB Details
$fl_name= "csv/".$sv_file_name;
if(file_exists($fl_name)){
	$fileContents = fopen($fl_name,"r");
	$row=0;
	while(($data=fgetcsv($fileContents,10000,',')) !== FALSE){	
		if($row >0){
			$primary_table_name=trim($data[0]);
			$primary_column_name=trim($data[1]);
			$ids_inc = trim($inc);
			$foreign_table_name = trim($data[2]);
			$foreign_column_name = trim($data[3]);
			
			if(trim($primary_table_name) && trim($primary_column_name)) {
				$primary_updt_qry = "UPDATE $primary_table_name SET $primary_column_name=$primary_column_name+$ids_inc WHERE ($primary_column_name between 1 AND $ids_inc)";
				imw_query($primary_updt_qry) or $msg_info[] = 'PRIMARY = '.$primary_updt_qry.imw_error();
			}
			if(trim($foreign_table_name) && trim($foreign_column_name)) {
				$foreign_updt_qry = "UPDATE $foreign_table_name SET $foreign_column_name=$foreign_column_name+$ids_inc WHERE ($foreign_column_name between 1 AND $ids_inc)";
				imw_query($foreign_updt_qry) or $msg_info[] = 'FOREIGN = '.$foreign_updt_qry.imw_error();
			}
		}
		$row++;
	}
	
	$ptQry = "SELECT patient_in_waiting_id, patient_id, drOfficePatientId FROM patient_in_waiting_tbl ORDER BY patient_id ASC";
	$res = imw_query($ptQry)or $msg_info[] = $ptQry.imw_error();
	if(imw_num_rows($res)>0){
		while($row = imw_fetch_assoc($res))
		{
			list($imwPatientId, $oldPatientId) = explode('-',$row['drOfficePatientId']);
			if($imwPatientId)
			{
				$drOfficePatientID_New = $imwPatientId.'-'.$row['patient_id'];
				$uQry = "Update patient_in_waiting_tbl Set drOfficePatientId = '".$drOfficePatientID_New."' Where patient_in_waiting_id = ".$row['patient_in_waiting_id']." ";	
				imw_query($uQry) or $msg_info[] = 'PatientWaitingID = '.$uQry.imw_error();
			}
		}
	}
	
	
}

$msg_info[] = "<br><br><b> Increment Completed</b>";
?>
<html>
<head>
<title>Jump Tables </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>