<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$sql = array();

$res1 = imw_query("SELECT * FROM emdeon_reports WHERE report_recieve_date = '0000-00-00 00:00:00'");
if($res1 && imw_num_rows($res1)>0){
	while($rs1 = imw_fetch_assoc($res1)){
		$zero_dt_report_id = $rs1['emdeon_report_id'];
		$new_report_id = $zero_dt_report_id-1;
		$res2 = imw_query("SELECT report_recieve_date FROM emdeon_reports WHERE emdeon_report_id='$new_report_id' LIMIT 0,1");
		if($res2 && imw_num_rows($res2)>0){
			$rs2 = imw_fetch_assoc($res2);
			$report_recieve_date = $rs2['report_recieve_date'];
			if($report_recieve_date != '0000-00-00 00:00:00'){
				$res3 = imw_query("UPDATE emdeon_reports SET report_recieve_date='$report_recieve_date' WHERE emdeon_report_id='$zero_dt_report_id'");
				if($res3){
					echo "Updted date for report ID $zero_dt_report_id<br>";
				}
			}
		}
	}
}else{
	die('All corrections done.');
}

?>
