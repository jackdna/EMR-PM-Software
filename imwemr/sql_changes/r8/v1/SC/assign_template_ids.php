<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
if($_REQUEST['dated'])
{$dated=$_REQUEST['dated'];}
else
{$dated=date('Y-m-d');}

if(isset($_REQUEST['st']) && !empty($_REQUEST['st'])){
	$startFrom = $_REQUEST['st'];
}else{
	$startFrom = 0;
}

//end
$endTo = 500;

//total records
$totrec = 0;
if(isset($_REQUEST['totRec']) && !empty($_REQUEST['totRec'])){
	$totrec = $_REQUEST['totRec'];
}else{
	$getTotRec = "SELECT COUNT(id) as TotRec FROM schedule_appointments where sch_template_id=0 and sa_app_start_date>='".$dated."'";
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
		<title>Reports - Add Schedule Template reference in Appointments Table</title>
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
			<span align='center' class='failureMsg'>No Appointments Found.</span>
		</font>
	<?php	
}else if($startFrom >= $totrec){
	?>
		<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
			<b>Appiontment records have been updated successfully.</b>
		</font>
	<?php	
}else{
	$show_msg= "<br><br><b>Please do not hit the <span style='color:red' > BACK, STOP or REFRESH or Any Tab Button </span> until the process is completed.</b>";
	$show_msg.="<br><br><b>Updated <span style='color:red'>".$startFrom."</span> of <span style='color:red'>".$totrec."</span> Appointment records.</b>"; 
	echo $show_msg;
	$filetime = (isset($_REQUEST['fileTime']) == true) ? $_REQUEST['fileTime'] : date("hsi");
	if(is_dir(data_path().'log')==false)mkdir(data_path().'log');
	
	$fileName = data_path()."log/template_app_update".date("ymd")."_".$filetime.".csv";
	if(file_exists($fileName) == false){
		$fp = fopen($fileName, 'w');
		fputcsv($fp, array("appId"), ",");
	}
	else{
		$fp = fopen($fileName, 'a+');
	}
	
	$strAppts = "SELECT id, sa_doctor_id, sa_app_start_date, sa_facility_id, sa_app_starttime, sa_app_endtime FROM schedule_appointments WHERE sch_template_id=0 and sa_app_start_date>='".$dated."' ORDER BY id LIMIT ".$startFrom.", ".$endTo;
	$resAppts = imw_query($strAppts) or $msg_info[] = imw_error();

	while($thisResApptsCnt = imw_fetch_array($resAppts)){
		list($y, $m, $d) = explode("-", $thisResApptsCnt['sa_app_start_date']);
		$week = ceil($d/7);
		$intTimeStamp = mktime(0, 0, 0, $m, $d, $y);
		$weekDay = date("N", $intTimeStamp);
		$strQryCheck = "SELECT st.id 
						FROM schedule_templates st 
						INNER JOIN provider_schedule_tmp pst ON st.id = pst.sch_tmp_id 
						WHERE pst.provider = '".$thisResApptsCnt['sa_doctor_id']."'  
						AND '".$thisResApptsCnt['sa_app_start_date']."' >= pst.today_date  
						AND pst.del_status = 0
						AND ((pst.status  = 'yes') OR (pst.status  = 'no' AND pst.today_date = '".$thisResApptsCnt['sa_app_start_date']."')) 
						AND pst.week$week = '".$weekDay."'  
						AND pst.facility = '".$thisResApptsCnt['sa_facility_id']."'";
		$resQryCheck = imw_query($strQryCheck) or $msg_info[] = imw_error();
		while($arrQryCheck = imw_fetch_array($resQryCheck)){		
			$strQryTemp = "SELECT morning_start_time, morning_end_time FROM schedule_templates WHERE id = '".$arrQryCheck["id"]."'";
			$resQryTemp = imw_query($strQryTemp) or $msg_info[] = imw_error();
			$arrQryTemp = imw_fetch_array($resQryTemp);
	
			if($arrQryCheck["id"] > 0 && strtotime($thisResApptsCnt['sa_app_starttime']) >= strtotime($arrQryTemp["morning_start_time"]) && strtotime($thisResApptsCnt['sa_app_starttime']) <= strtotime($arrQryTemp["morning_end_time"])){
				$updAppt = "UPDATE schedule_appointments SET sch_template_id = '".$arrQryCheck["id"]."' WHERE id = '".$thisResApptsCnt['id']."'";
				imw_query($updAppt);
				if(imw_affected_rows() > 0){
					fputcsv($fp, array($thisResApptsCnt['id']), ",");
				}
			}
		}
	}
	?>
		<form action="" method="get" name="apptRecord">
			<input type="hidden" name="st" value="<?php echo intval($startFrom)+$endTo; ?>"/>		
			<input type="hidden" name="dated" value="<?php echo $dated; ?>"/>			
			<input type="hidden" name="totRec" value="<?php echo (isset($totrec) == true ? $totrec : $totrec); ?>"/>	
            <input type="hidden" name="fileTime" value="<?php echo (isset($_REQUEST['fileTime']) == true ? $_REQUEST['fileTime'] : $filetime); ?>"/>	
		</form>
		<script language="javascript">
			document.apptRecord.submit();
		</script>
	<?php
}
?>
	</body>
</html>