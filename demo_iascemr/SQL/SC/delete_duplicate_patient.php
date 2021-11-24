<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");
//$staticDos = '2013-05-23';
$staticDos = date('Y-m-d');
$qry = "
SELECT stub_id, group_concat(stub_id order by stub_id desc) as stub_id_comma, appt_id, count( appt_id ) AS countAppt,dos,CONCAT_WS(', ', patient_last_name,patient_first_name) as ptName
FROM stub_tbl 
WHERE dos >= '".$staticDos."' AND patient_status != 'Canceled'
GROUP BY appt_id desc
HAVING count( appt_id )>1 ORDER BY dos  DESC
";
$res = imw_query($qry) or $msg_info[] = imw_error();
$affectedRows='';
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_array($res)) {	
		$stub_id_comma = $row["stub_id_comma"];
		$stub_dos = $row["dos"];
		$ptName = $row["ptName"];
		if($stub_id_comma) {
			$delQry = "DELETE FROM stub_tbl 
						WHERE stub_id IN(".$stub_id_comma.") AND patient_confirmation_id='0' 
						AND stub_id NOT IN(SELECT stub_id FROM scan_upload_tbl WHERE stub_id IN(".$stub_id_comma."))";
			echo '<br><br>'.$delQry;
			$delRes = imw_query($delQry) or $msg_info[] = imw_error();
			echo $affectedRows = "<br>Number of Records Affected on for patient <b><font color='#FF0000'>".$ptName."</font></b> with DOS <b><font color='#FF0000'>".$stub_dos."</font></b> = ".imw_affected_rows();
		}
	}
}

if($affectedRows=='') {
	$msg_info[] = "No Record Affected";	
}
$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Duplicate record deleted";

?>

<html>
<head>
<title>Delete Duplicate Records</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







