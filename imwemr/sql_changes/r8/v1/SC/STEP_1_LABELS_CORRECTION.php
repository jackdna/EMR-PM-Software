<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

require_once("../../../../library/classes/scheduler/appt_schedule_functions.php");

$obj_scheduler = new appt_scheduler();
$where=" WHERE 1=1 ";
if($_REQUEST['dated'])
$where.=" AND sa_app_start_date>='$_REQUEST[dated]'";
else
$where.=" AND sa_app_start_date >= '".date('Y-m-d')."'";

if($_REQUEST['provider'])
$where.=" AND sa_doctor_id='$_REQUEST[provider]'";




if(isset($_REQUEST['st']) && !empty($_REQUEST['st'])){
	$startFrom = $_REQUEST['st'];
}else{
	$TRUNCATE = "UPDATE schedule_appointments SET sch_template_id = '0' $where "; 
	imw_query($TRUNCATE);

	$startFrom = 0;
}

//end
$endTo = 500;

//total records
$totrec = 0;
if(isset($_REQUEST['totRec']) && !empty($_REQUEST['totRec'])){
	$totrec = $_REQUEST['totRec'];
}else{
	$getTotRec = "SELECT COUNT(id) as TotRec FROM schedule_appointments $where";
	$rsGetTotRec = imw_query($getTotRec) or die(imw_error());
	if($rsGetTotRec){
		if(imw_num_rows($rsGetTotRec)>0){
			$rowGetTotRec = imw_fetch_array($rsGetTotRec);
			$totrec= $rowGetTotRec["TotRec"];	
		}
	}
}
?>
<html>
	<head>
		<title>STEP 1: LABELS CORRECTION (Template ID in Appointments)</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<style>
			.failureMsg{
				font-family:"verdana";
				font-size:10px; 
				font-weight:bold;
				color:FF0000;
			}
		</style>
	</head>
	<body>
<?php 
if($totrec == 0){
	?>
		<font face="Arial, Helvetica, sans-serif" size="2">
			<span align='center' class='failureMsg'>STEP 1: LABELS CORRECTION - No Appointments Found.</span>
		</font>
	<?php	
}else if($startFrom >= $totrec){
	?>
		<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
			<b>STEP 1: LABELS CORRECTION - Template IDs have been updated in Appointments</b>
		</font>
	<?php	
}else{
	$show_msg= "<br><br><b>Please do not hit the <span style='color:red' > BACK, STOP or REFRESH or Any Tab Button </span> until the process is completed.</b>";
	$show_msg.="<br><br><b>Updated <span style='color:red'>".$startFrom."</span> of <span style='color:red'>".$totrec."</span> Appointment records.</b>"; 
	echo $show_msg;
	
	$strAppts = "SELECT id, sa_doctor_id, sa_app_start_date, sa_facility_id, sa_app_starttime, sa_app_endtime FROM schedule_appointments $where ORDER BY id LIMIT ".$startFrom.", ".$endTo;
	$resAppts = imw_query($strAppts) or $msg_info[] = imw_error();

	while($thisResApptsCnt = imw_fetch_array($resAppts)){

		$arr_prov_sch = $obj_scheduler->get_provider_schedules($thisResApptsCnt['sa_app_start_date'], array(0 => $thisResApptsCnt['sa_doctor_id']), array(0 => $thisResApptsCnt['sa_facility_id']));
		$arr_sch_tmp_id = array();
		for($i = 0; $i < count($arr_prov_sch); $i++){
			$arr_sch_tmp_id[] = $arr_prov_sch[$i]["sch_tmp_id"];
		}
		$str_sch_tmp_id = join("','", $arr_sch_tmp_id);

		if(trim($str_sch_tmp_id) != ""){
			$str_sch_tmp_id = "'".$str_sch_tmp_id."'";
		}

		//getting timings for office
		$int_this_tmp_id = 0;
		if(trim($str_sch_tmp_id) != ""){ 
			$str_tmp = "select id, morning_start_time, morning_end_time from schedule_templates where id in (".$str_sch_tmp_id.") order by id";
			//echo $str_tmp."<br>";
			$res_tmp = imw_query($str_tmp);
			if(imw_num_rows($res_tmp)>0){
				while($arr_tmp = imw_fetch_assoc($res_tmp)){
					if(strtotime($thisResApptsCnt['sa_app_starttime']) >= strtotime($arr_tmp["morning_start_time"]) && strtotime($thisResApptsCnt['sa_app_starttime']) < strtotime($arr_tmp["morning_end_time"])){
						$int_this_tmp_id = $arr_tmp["id"];
						break;
					}
				}
			}
		}

		if(!empty($int_this_tmp_id)){
			$updAppt = "UPDATE schedule_appointments SET sch_template_id = '".$int_this_tmp_id."' WHERE id = '".$thisResApptsCnt['id']."'";
			imw_query($updAppt);
			//echo $updAppt."<br>";
		}
	}
	?>
		<form action="" method="get" name="apptRecord">
			<input type="hidden" name="st" value="<?php echo intval($startFrom)+$endTo; ?>"/>			
			<input type="hidden" name="totRec" value="<?php echo intval($totrec); ?>"/>			
			<input type="hidden" name="provider" value="<?php echo $_REQUEST['provider']; ?>"/>			
			<input type="hidden" name="dated" value="<?php echo $_REQUEST['dated']; ?>"/>	
		</form>
		<script language="javascript">
			document.apptRecord.submit();
		</script>
	<?php
}
?>
	</body>
</html>