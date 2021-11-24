<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

if($_REQUEST['dated'])
$where=" AND sa_app_start_date>='$_REQUEST[dated]'";
else
$where=" AND sa_app_start_date >= '".date('Y-m-d')."'";

if($_REQUEST['provider'])
$where.=" AND sa_doctor_id='$_REQUEST[provider]'";

$startFrom = 0;
if((isset($_REQUEST['st']) == true) && (empty($_REQUEST['st']) == false)){
	$startFrom = $_REQUEST['st'];
}else{
	if($_REQUEST['dated'])$where_sub=" AND start_date>='$_REQUEST[dated]'";
	else $where_sub=" AND start_date >= '".date('Y-m-d')."'";
	
	if($_REQUEST['provider'])$where_sub.=" AND 	provider='$_REQUEST[provider]'";
	
	$TRUNCATE = "DELETE FROM scheduler_custom_labels WHERE system_action = '1' $where_sub"; 
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
	$getTotRec = "SELECT COUNT(id) as totRec FROM schedule_appointments WHERE sa_patient_app_status_id NOT IN(201,18,19,20,203) and sch_template_id != '0' $where";
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
		<title>STEP 2: LABELS CORRECTION (Copy Template Labels to Custom System Labels)</title>
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
			<span align='center' class='failureMsg'>STEP 2: LABELS CORRECTION - No Template Labels Found.</span>
		</font>
	<?php	
}else if($startFrom >= $totrec){
	?>
		<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
			<b>STEP 2: LABELS CORRECTION - Template Labels copied to Custom System Labels successfully.</b>
		</font>
	<?php	
}else{
	$msg = "<br><br><b>Please do not hit the <span style='color:red' > BACK, STOP or REFRESH or Any Tab Button </span> until the process is completed.</b>";
	$msg.="<br><br><b>Updated <span style='color:red'>".$startFrom."</span> of <span style='color:red'>".$totrec."</span> Appointment records.</b>"; 
	echo $msg;
	
	$strQryAppts = "SELECT schedule_appointments.id, sa_doctor_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, sa_facility_id, acronym, sch_template_id FROM schedule_appointments left join slot_procedures sp ON sp.id = schedule_appointments.procedureid where sa_patient_app_status_id NOT IN(201,18,19,20,203) and sch_template_id != '0' $where ORDER BY id LIMIT ".$startFrom.", ".$endTo;
	$rsQryAppts = imw_query($strQryAppts) or $arrMsh[] = imw_error();

	while($rowQryAppts = imw_fetch_array($rsQryAppts)){

		//appt timings slot wise loop
		$st_time = strtotime($rowQryAppts["sa_app_starttime"]);
		//echo "<br>";
		$ed_time = strtotime($rowQryAppts["sa_app_endtime"]);
		//echo "<br>";

		while($st_time < $ed_time){
			$match_st_time = date("H:i", $st_time);
			//echo "<br>";
			$match_ed_time = date("H:i", ($st_time + (DEFAULT_TIME_SLOT * 60)));

			$qry3 = "select schedule_label_id, template_label, label_type, label_color, label_group from schedule_label_tbl where start_time = '".$match_st_time."' and sch_template_id = '".$rowQryAppts["sch_template_id"]."' AND (label_type = 'Procedure' OR label_type = 'Information') and template_label<>'' LIMIT 1";
			//echo "<br>".$qry3."<br>";
			$res3 = imw_query($qry3);
			if(imw_num_rows($res3)>0){
				$arr3 = imw_fetch_assoc($res3);
				
				$chk = "SELECT id FROM scheduler_custom_labels WHERE provider = '".$rowQryAppts["sa_doctor_id"]."' AND facility = '".$rowQryAppts["sa_facility_id"]."' AND start_date = '".$rowQryAppts["sa_app_start_date"]."' AND start_time = '".$match_st_time.":00' AND (l_type = 'Procedure' OR l_type = 'Information')";
				//echo "<br>".$chk."<br>";
				$chkres = imw_query($chk);
				if(imw_num_rows($chkres) == 0){

					$qry54 = "INSERT INTO scheduler_custom_labels SET labels_replaced = '', provider = '".$rowQryAppts["sa_doctor_id"]."', facility = '".$rowQryAppts["sa_facility_id"]."', start_date = '".$rowQryAppts["sa_app_start_date"]."', end_time = '".$match_ed_time.":00', start_time = '".$match_st_time.":00', l_text = '".addslashes($arr3["template_label"])."', l_show_text = '".addslashes($arr3["template_label"])."', l_type = '".addslashes($arr3["label_type"])."', l_color = '".addslashes($arr3["label_color"])."', label_group='".$arr3["label_group"]."', time_status = '".date('Y-m-d H:i:s')."', system_action = '1', temp_id='$rowQryAppts[sch_template_id]'";
					//echo "<br>".$qry54."<br>";
					imw_query($qry54);
				}
			}
			$st_time += (DEFAULT_TIME_SLOT * 60);
		}
	}
	?>
		<form action="" method="get" name="frmApptRecord">
			<input type="hidden" name="st" value="<?php echo intval($startFrom)+$endTo; ?>"/>			
			<input type="hidden" name="totRec" value="<?php echo intval($totrec); ?>"/>			
			<input type="hidden" name="provider" value="<?php echo $_REQUEST['provider']; ?>"/>			
			<input type="hidden" name="dated" value="<?php echo $_REQUEST['dated']; ?>"/>
		</form>
		<script language="javascript">
			document.frmApptRecord.submit();
		</script>
	<?php
}
?>
	</body>
</html>