<?php
	$ignoreAuth = true;
	include("../../../../config/globals.php");
	$qry = imw_query("select date_of_service, encounter_id, patient_id,charge_list_id,primaryProviderId from patient_charge_list where primaryProviderId=0 or primary_provider_id_for_reports=0");  
	while($row=imw_fetch_assoc($qry)){
		$temp[$row['encounter_id']] = $row;
	}	
	foreach($temp as $eid => $encDetail){
	$encDos =	$temp[$eid]['date_of_service'];
	$patient_id =	$temp[$eid]['patient_id'];
	$charge_list_id =	$temp[$eid]['charge_list_id'];
	$chl_primaryProviderId =	$temp[$eid]['primaryProviderId'];
	
	$sa_qry = imw_query("select sa_doctor_id from schedule_appointments,users where users.id=schedule_appointments.sa_doctor_id and users.user_type=1 and sa_doctor_id>0 and sa_app_start_date='$encDos' and sa_patient_id='$patient_id' and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_starttime desc limit 0,1");
	$res=imw_fetch_assoc($sa_qry);
	$primaryProviderId =$res['sa_doctor_id'];
	if($primaryProviderId <= 0){
		$qry = imw_query("select providerID from patient_data where id = '$patient_id'");
		$patientDetail = imw_fetch_object($qry);
		$primaryProviderId  = $patientDetail->providerID;
	}
	if($chl_primaryProviderId>0){
		$primaryProviderId=$chl_primaryProviderId;
	}
	if($primaryProviderId > 0) {
		imw_query("update patient_charge_list set primaryProviderId='$primaryProviderId' where charge_list_id = '$charge_list_id' and  patient_id='$patient_id' and primaryProviderId='0'");
		imw_query("update patient_charge_list_details set primaryProviderId='$primaryProviderId' where charge_list_id = '$charge_list_id' and  patient_id='$patient_id'  and primaryProviderId='0'");
		
		imw_query("update patient_charge_list set primary_provider_id_for_reports='$primaryProviderId'  where charge_list_id = '$charge_list_id' and  patient_id='$patient_id' and primary_provider_id_for_reports='0'");
		imw_query("update patient_charge_list_details set primary_provider_id_for_reports='$primaryProviderId'  where charge_list_id = '$charge_list_id' and  patient_id='$patient_id' and primary_provider_id_for_reports='0'");
	}	
	}
	if($primaryProviderId>0) { 
		$msg_info[] = "<br><br><b>Updated records successfully.</b>";
		$color = "green";
	} else{
		$msg_info[] = "<br><br>No record found for update.</b>";
		$color = "red";	
	}
?>
<html>
<head>
<title>Special update for update provider ids in Accounting tables</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>