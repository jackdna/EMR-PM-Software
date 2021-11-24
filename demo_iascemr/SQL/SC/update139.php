<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql = array();

//UPDATE DISCHARGE SUMMARY CHANGES
//imw_query("CREATE TABLE superbill_tbl_bak_".date("d_m_Y")." AS (SELECT * FROM superbill_tbl)") or $msg_info[] = imw_error();
imw_query("CREATE TABLE superbill_tbl_bak_".date("d_m_Y")." LIKE superbill_tbl") or $msg_info[] = imw_error();
imw_query("INSERT INTO superbill_tbl_bak_".date("d_m_Y")." SELECT * FROM superbill_tbl") or $msg_info[] = imw_error();
$sql[] = "ALTER TABLE  `superbill_tbl` CHANGE  `isAnesthesia`  `bill_user_type` TINYINT( 4 ) NOT NULL";
$sql[] = "ALTER TABLE `superbill_tbl` CHANGE `bill_user_type` `bill_user_type` TINYINT( 4 ) NOT NULL COMMENT '1 anesthesia, 2 surgeon, 3 facility'";
$sql[] = "UPDATE superbill_tbl SET bill_user_type = '2' WHERE bill_user_type = '0'"; //SURGEON

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$qrySuberbill = "SELECT * FROM superbill_tbl WHERE bill_user_type = '3'"; //FOR FACILITY
$resSuberbill = imw_query($qrySuberbill);
if(imw_num_rows($resSuberbill)==0) {
	$qryNew = "SELECT * FROM superbill_tbl WHERE bill_user_type = '2'";
	$resNew = imw_query($qryNew);
	if(imw_num_rows($resNew)>0) {
		while($rowNew = imw_fetch_assoc($resNew)) {
			$rowArr[] = $rowNew;
		}//print'<pre>';print_r($rows);
		foreach($rowArr as $rowArrNew) {
			$columnsArr = array();
			foreach($rowArrNew as $field => $val) {
				$val = ($field == 'bill_user_type') ? '3' : $val;
				if($field != 'superbill_id') {
					$columnsArr[] = $field.' = '."'".$val."'";
				}
			}
			$columns = implode(", ",$columnsArr);
			if($columns) {
				$insQry = "INSERT INTO superbill_tbl SET ".$columns;
				$insRes = imw_query($insQry) or die($insQry.imw_error());
			}
		}
		
	}
}


$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 139 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 139 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 139</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>