<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("common/conDb.php");
$chartVal = $_GET['chartVal'];
$chartLogId = $_GET['chartLogId'];
$dt = date("Y-m-d");
$tm = date("H:i:s");

if($chartVal=='chart_close' && $chartLogId) {
	$updateChartLogQry = "UPDATE chart_log SET chart_close_date = '".$dt."', chart_close_time = '".$tm."' WHERE chart_log_id ='".$chartLogId."'";
}
$updateChartLogRes = imw_query($updateChartLogQry);
?>