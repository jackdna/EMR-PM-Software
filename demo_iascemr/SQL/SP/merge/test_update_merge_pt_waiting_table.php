<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
set_time_limit(0);
include_once("../../../common/conDb.php");  //MYSQL CONNECTION
$sv_file_name = "merge_patient_in_waiting_tbl.csv";
include_once("test_update_merge_db_detail.php");  //DB Details

imw_query("CREATE TABLE patient_in_waiting_tbl_bak_".date("d_m_Y")." AS (SELECT * FROM patient_in_waiting_tbl)") or $msg_info[] = imw_error();

//$inc_less = (int)$inc - 100001;
$inc_less = (int)$inc;
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
				$primary_updt_qry = "UPDATE $primary_table_name SET $primary_column_name=$primary_column_name+$ids_inc WHERE ($primary_column_name between 1 AND $inc_less)";
				imw_query($primary_updt_qry) or $msg_info[] = 'PRIMARY = '.$primary_updt_qry.imw_error();
			}
			if(trim($foreign_table_name) && trim($foreign_column_name)) {
				$foreign_updt_qry = "UPDATE $foreign_table_name SET $foreign_column_name=$foreign_column_name+$ids_inc WHERE ($foreign_column_name between 1 AND $inc_less)";
				imw_query($foreign_updt_qry) or $msg_info[] = 'FOREIGN = '.$foreign_updt_qry.imw_error();
			}
		}
		$row++;
	}
}

$msg_info[] = "<br><br><b> Increment Completed</b>";
?>
<html>
<head>
<title>Merge patient_in_waiting_tbl </title>
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