<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	include('connect_sqlserver.php'); 
	$txt_sql = stripslashes($_REQUEST['txt_sql']);
	$sql = trim($txt_sql);
?>
<form name="frm_sqlqry" action="sql_server_qry.php" method="post">
	<textarea name="txt_sql" cols="40" rows="5"><?php echo $txt_sql;?></textarea>
	<input type="submit" name="submitQry" value="Run Query">
</form>

<?php	
	if($_POST['submitQry']<>"") {
		
		if($sql=="") {
			$sql="SELECT * FROM appointments
					WHERE appt_date = '20080924' OR appt_date = '2008-09-24'  OR appt_date = '2008/09/24'";
					
		}
		
		echo $sql."<br><br><br>";
		$rs = $db->Execute($sql);
		echo "Query Run = ".count($rs)."<br><br>";
		if($rs) {
			foreach($rs as $k => $row) {
				//echo $rs->DoctorID;
				echo "Row1 Value = ".$row[1]."<br>";
			}
		}else {
			echo "<font color= 'red'><br>No Record Found<br></font>";
		}	
	
		print '<pre>';
		print_r($rs);
	}
	$db->close();
?>
