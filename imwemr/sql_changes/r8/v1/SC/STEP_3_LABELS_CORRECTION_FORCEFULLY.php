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
	$startFrom = 0;
}

//end
$endTo = 500;

//total records
$totrec = 0;
if(isset($_REQUEST['totRec']) && !empty($_REQUEST['totRec'])){
	$totrec = $_REQUEST['totRec'];
}else{
	$getTotRec = "SELECT COUNT(id) as totRec FROM schedule_appointments WHERE sa_patient_app_status_id NOT IN(201,18,19,20,203) $where";
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
		<title>STEP 3: LABELS CORRECTION (Replacing Labels with added Appointments)</title>
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
			<span align='center' class='failureMsg'>STEP 3: LABELS CORRECTION - No appointments Found.</span>
		</font>
	<?php	
}else if($startFrom >= $totrec){
	?>
		<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
			<b>STEP 3: LABELS CORRECTION - Labels have been replaced with added appointments.</b>
		</font>
	<?php	
}else{
	$msg = "<br><br><b>Please do not hit the <span style='color:red' > BACK, STOP or REFRESH or Any Tab Button </span> until the process is completed.</b>";
	$msg.="<br><br><b>Updated <span style='color:red'>".$startFrom."</span> of <span style='color:red'>".$totrec."</span> Appointment records.</b>"; 
	echo $msg;
	
	$strQryAppts = "SELECT schedule_appointments.id, sa_doctor_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, sa_facility_id, acronym FROM schedule_appointments left join slot_procedures sp ON sp.id = schedule_appointments.procedureid where sa_patient_app_status_id NOT IN(201,18,19,20,203) $where ORDER BY id LIMIT ".$startFrom.", ".$endTo;
	$rsQryAppts = imw_query($strQryAppts) or $arrMsh[] = imw_error();

	while($rowQryAppts = imw_fetch_array($rsQryAppts)){

		//appt timings slot wise loop
		$st_time = strtotime($rowQryAppts["sa_app_starttime"]);
		$ed_time = strtotime($rowQryAppts["sa_app_endtime"]);

		while($st_time < $ed_time){
			$match_st_time = date("H:i", $st_time);
				
			$qry3 = "select id, l_text, l_show_text, labels_replaced, label_group from scheduler_custom_labels where start_date = '".$rowQryAppts["sa_app_start_date"]."' and start_time = '".$match_st_time.":00' and provider = '".$rowQryAppts["sa_doctor_id"]."' and facility = '".$rowQryAppts["sa_facility_id"]."' and (l_type = 'Procedure' or l_type = 'Information') and l_show_text!='' LIMIT 1";
			//echo "<br>".$qry3."<br>";die();
			$res3 = imw_query($qry3);
			if(imw_num_rows($res3) > 0){
				$arr3 = imw_fetch_assoc($res3);
				if($arr3["label_group"]==1)$arr_l_text[] = $arr3["l_show_text"];
				else $arr_l_text = explode("; ", $arr3["l_show_text"]);
				
				$boooooooooool = true;
				$arr_labels_replaced = explode("::", $arr3["labels_replaced"]);
				if(count($arr_labels_replaced) > 0){
					foreach($arr_labels_replaced as $chack_de_phatte){
						$arr_chack_de_phatte = explode(":", $chack_de_phatte);
						if(trim($arr_chack_de_phatte[0]) == $rowQryAppts["id"]){
							$boooooooooool = false;
						}
					}
				}

				if($boooooooooool === true){
					$arr_replace = array();
					$str_replace = "";
					$bl_do = false;
					$labels_replaced = "::".$rowQryAppts["id"].":".$arr_l_text[0];
					$str_replace="";
					if(count($arr_l_text)>1){
						array_shift($arr_l_text);
						$str_replace=implode("; ",$arr_l_text);
					}else{
						$str_replace=$arr_l_text[1];
					}
					$qry4 = "update scheduler_custom_labels set labels_replaced = CONCAT(labels_replaced,'".$labels_replaced."'), l_show_text = '".$str_replace."' where id = '".$arr3["id"]."'";
					imw_query($qry4);
					
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