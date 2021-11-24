<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
require_once("../../../../library/classes/scheduler/appt_schedule_functions.php");
$obj_scheduler = new appt_scheduler();
$startDate=date('Y-m-d');
$sa_madeby='admin';
$startFrom = 0;
if((isset($_REQUEST['st']) == true) && (empty($_REQUEST['st']) == false)){
	$startFrom = $_REQUEST['st'];
}else{
	$CREATE = "CREATE TABLE IF NOT EXISTS `prov_without_schedule` (
				 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				 `prov_id` INT NOT NULL,
				 `prov_name` VARCHAR( 100) NOT NULL ,
				 `sch_date` DATE NOT NULL ,
				 `added_on` DATETIME NOT NULL 
				) ENGINE=MYISAM";
	imw_query($CREATE);

	$TRUNCATE = "TRUNCATE TABLE `prov_without_schedule`";
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
	$getTotRec = "SELECT COUNT(id) as totRec FROM schedule_appointments WHERE sch_template_id = 0 and sa_madeby = '$sa_madeby' and sa_app_start_date >= '$startDate'";
	$rsGetTotRec = imw_query($getTotRec) or die(imw_error());
	if($rsGetTotRec){
		if(imw_num_rows($rsGetTotRec)>0){
			$rowGetTotRec = imw_fetch_array($rsGetTotRec);
			$totrec= $rowGetTotRec["totRec"];	
		}
	}
}
?>
<html>
	<head>
		<title>Reports - Add Facility to Appointments Table</title>
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
			<span align='center' class='failureMsg'>No Incorrect Appointments Found.</span>
		</font>
	<?php	
}else if($startFrom >= $totrec){
	?>
		<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
			<b><?php echo $totrec;?> Appiontment records have been corrected successfully.</b>
			<?php
			$show_html = "";

			$qry = imw_query("SELECT * FROM prov_without_schedule ORDER BY prov_name");
			if(imw_num_rows($qry) > 0){
				$show_html .= "<b>Following Provider Schedules need to be added.</b>";
				while($arr=imw_fetch_assoc($qry))
				{
					$tmp++;
					list($y, $m, $d) = explode("-", $arr['sch_date']);
					$sch_date = date("m-d-Y", mktime(0, 0, 0, $m, $d, $y));
					$show_html .= "<div style=\"clear:both;width:500px;\">
										<div style=\"float:left;width:300px;\">".$arr["prov_name"]."</div>
										<div style=\"float:left;width:100px;\">".$sch_date."</div>
									</div>";
				}
			}else{
				$show_html = "<b>No Pending Provider Schedule found.</b>";
			}
			echo $show_html;
			?>
		</font>
	<?php	
}else{
	$msg = "<br><br><b>Please do not hit the <span style='color:red' > BACK, STOP or REFRESH or Any Tab Button </span> until the process is completed.</b>";
	$msg.="<br><br><b>Updated <span style='color:red'>".$startFrom."</span> of <span style='color:red'>".$totrec."</span> Appointment records.</b>"; 
	echo $msg;
	
	 $strQryAppts = imw_query("SELECT schedule_appointments.id, schedule_appointments.sa_doctor_id, schedule_appointments.sa_app_start_date, schedule_appointments.sa_app_starttime, schedule_appointments.sa_app_endtime, users.id as uid, users.fname, users.lname, users.mname FROM schedule_appointments LEFT JOIN users ON users.id = schedule_appointments.sa_doctor_id 
					where sch_template_id = 0 and sa_madeby = '$sa_madeby' and sa_app_start_date >= '$startDate'  
					ORDER BY id LIMIT ".$startFrom.", ".$endTo);
	while($rowQryAppts = imw_fetch_array($strQryAppts)){
				
		$arr_prov_sch = $obj_scheduler->get_provider_schedules($rowQryAppts['sa_app_start_date'], array(0 => $rowQryAppts['sa_doctor_id']));
		$arr_sch_tmp_id = array();
		$arr_tmp_fac_rel = array();
		for($i = 0; $i < count($arr_prov_sch); $i++){
			$arr_sch_tmp_id[] = $arr_prov_sch[$i]["sch_tmp_id"];
			$arr_tmp_fac_rel[$arr_prov_sch[$i]["sch_tmp_id"]] = $arr_prov_sch[$i]["facility"];
		}
		$str_sch_tmp_id = join("','", $arr_sch_tmp_id);

		if(trim($str_sch_tmp_id) != ""){
			$str_sch_tmp_id = "'".$str_sch_tmp_id."'";
		}	

		//getting timings for office
		$int_this_fac_id = 0;
		if(trim($str_sch_tmp_id) != ""){ 
			$arr_all_tmp = array();
		
			$str_tmp = imw_query("select id, morning_start_time, morning_end_time from schedule_templates where id in (".$str_sch_tmp_id.") order by id");
			if(imw_num_rows($str_tmp) > 0){
				while($arr_tmp=imw_fetch_assoc($str_tmp)){
					if(strtotime($rowQryAppts['sa_app_starttime']) >= strtotime($arr_tmp["morning_start_time"]) && strtotime($rowQryAppts['sa_app_starttime']) < strtotime($arr_tmp["morning_end_time"])){
						$int_this_fac_id = $arr_tmp_fac_rel[$arr_tmp["id"]];
						break;
					}
				}
			}
		}else{
			$prov_name = core_name_format($rowQryAppts['lname'], $rowQryAppts['fname'], $rowQryAppts['mname']);
			
			$insqry = "INSERT INTO prov_without_schedule SET
							prov_id = '".$rowQryAppts['sa_doctor_id']."',
							prov_name = '".$prov_name."',
							sch_date = '".$rowQryAppts['sa_app_start_date']."',
							added_on = '".date('Y-m-d H:i:s')."'";
			imw_query($insqry);
		}

		if(!empty($int_this_fac_id)){
			$updAppt = "UPDATE schedule_appointments SET sa_facility_id = '".$int_this_fac_id."' WHERE id = '".$rowQryAppts['id']."'";
			imw_query($updAppt);
			//echo $updAppt."<br>";
		}
	}
	?>
		<form action="" method="post" name="frmApptRecord">
			<input type="hidden" name="st" value="<?php echo (int)$_REQUEST['st'] + $endTo; ?>"/>			
			<input type="hidden" name="totRec" value="<?php echo (int)$_REQUEST['totRec']; ?>"/>	
		</form>
		<script language="javascript">
			document.frmApptRecord.submit();
		</script>
	<?php
}
?>
	</body>
</html>