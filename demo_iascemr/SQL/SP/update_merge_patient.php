<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(0);
	include_once("../../common/conDb.php");  //MYSQL CONNECTION
	$sv_file_name = "merge_patient_data.csv";
	$fl_name= "merge/csv/".$sv_file_name;
	

	$p_from = (int) (isset($_REQUEST['p_from']) ? $_REQUEST['p_from'] : 0);
	$p_to = (int) (isset($_REQUEST['p_to']) ? $_REQUEST['p_to'] : 0);

	//$patient_arr = array($p_from=>$p_to);
	if( $p_from && $p_to) 
	{
		imw_query("CREATE TABLE patient_data_tbl_bak_".date("d_m_Y")." LIKE patient_data_tbl") or $msg_info[] = imw_error();
		imw_query("INSERT INTO  patient_data_tbl_bak_".date("d_m_Y")." (SELECT *  FROM patient_data_tbl)") or $msg_info[] = imw_error();
		
		//foreach($patient_arr as $p_from => $p_to) {
		$qry = "Select group_concat(patient_id) as ids from patient_data_tbl Where patient_id in ($p_from,$p_to) ";
		$sql = imw_query($qry) or $msg_info[] = ' Check Patient Qry = '.$qry.imw_error();
		$cnt = imw_num_rows($sql);
		$row = imw_fetch_assoc($sql);
		$pArr= explode(",",$row['ids']);
		$p_exists = (in_array($p_from,$pArr) && in_array($p_from,$pArr)) ? true : false; 	
		
		if( $p_exists )
		{
			if(file_exists($fl_name))
			{
				$fileContents = fopen($fl_name,"r");
				$row=0;
				while(($data=fgetcsv($fileContents,10000,',')) !== FALSE)
				{	
					if($row >0)
					{
						$table_name	= trim($data[0]);
						$column_name= trim($data[1]);

						if(trim($table_name) && trim($column_name) && $table_name <> 'patient_data_tbl') {
							$updt_qry = "UPDATE $table_name SET $column_name ='".$p_to."' WHERE $column_name = '".$p_from."'";
							imw_query($updt_qry) or $msg_info[] = ' Update Qry = '.$updt_qry.imw_error();
							$a = imw_affected_rows();
							echo $a.' Rows Affected in table '.$table_name;
						}
					}
					$row++;
				}

				// Remove row from patient_data_tbl
				$qry = "Delete from patient_data_tbl Where patient_id = '".$p_from."'";
				$sql = imw_query($qry) or $msg_info[] = ' Delete Qry = '.$qry.imw_error();
			}
			$msg_info[] = "<br><br><b> Merge Patient Data Completed</b><br/>";
		}
		else {
			$msg_info[] = "<br><br><b> Patient does not exists</b><br/>";
		}
		
	}
	else {
			$msg_info[] = "<br><br><b> Invalid Patients!!!</b><br/>";
	}

?>
<html>
<head>
<title>Merge Patient Data </title>
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