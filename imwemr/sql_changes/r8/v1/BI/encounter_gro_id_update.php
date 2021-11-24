<?php
$ignoreAuth = true;
$skip_file="skipthisfile";
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/acc_functions.php');

if(empty($start_val) == true){
	$start_val = 0;
}
$start_val = 0;
$end = 1;

//---- UPDATE PATIENT CHARGE LIST FOR PATIENT DUE AND INSURANCE DUE -----

$qry = imw_query("SELECT idSuperBill,encounterId,dateOfService,patientId,physicianId FROM superbill WHERE postedStatus='0' AND gro_id='0' order by encounterId desc limit $start_val , $end");
$qryRes = imw_fetch_array($qry);
$encounter_id = $qryRes['encounterId'];
$idSuperBill = $qryRes['idSuperBill'];
$dateOfService = $qryRes['dateOfService'];
$patientId = $qryRes['patientId'];
$physicianId = $qryRes['physicianId'];

if(imw_num_rows($qry)==0){
	$qry_chl = imw_query("SELECT charge_list_id,encounter_id,date_of_service,patient_id,primaryProviderId FROM patient_charge_list WHERE gro_id='0' order by encounter_id desc limit $start_val , $end");
	$qryResChl = imw_fetch_array($qry_chl);
	$encounter_id = $qryResChl['encounter_id'];
	$charge_list_id = $qryResChl['charge_list_id'];
	$dateOfService = $qryResChl['date_of_service'];
	$patientId = $qryResChl['patient_id'];
	$physicianId = $qryResChl['primaryProviderId'];
}

$sel_provider_qry=imw_query("select sa_doctor_id,sa_facility_id from schedule_appointments,users where users.id=schedule_appointments.sa_doctor_id and
	users.user_type=1 and sa_doctor_id>0 and sa_app_start_date='".$dateOfService."' and sa_patient_id='".$patientId."'
	and sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_starttime desc limit 0,1");
$sel_provider_fet=imw_fetch_array($sel_provider_qry);
$sa_doctor_id =$sel_provider_fet['sa_doctor_id'];
$sa_facility_id =$sel_provider_fet['sa_facility_id'];

if($sa_facility_id>0){
	$sel_grp_qry=imw_query("select default_group from facility where id='".$sa_facility_id."'");
	$sel_grp_fet=imw_fetch_array($sel_grp_qry);
	$gro_id=$sel_grp_fet['default_group'];
}
if($gro_id>0){
}else{
	$sel_proc=imw_query("select default_group from users where default_group>0 and id='".$physicianId."'");
	$proc_row=imw_fetch_array($sel_proc);
	if($proc_row['default_group']>0){
		$gro_id=$proc_row['default_group'];
	}else{
		$sel_proc=imw_query("select gro_id from groups_new where group_institution='0' and del_status='0' order by gro_id asc");
		$proc_row=imw_fetch_array($sel_proc);
		$gro_id=$proc_row['gro_id'];
	}
}

if(imw_num_rows($qry)>0){
	imw_query("update superbill set gro_id='$gro_id' where idSuperBill='$idSuperBill' and gro_id='0'");
	$msg_info[] = "<br><b>Group is updated in encounter id ".($encounter_id)." at superbill</b>";
}else{
	imw_query("update patient_charge_list set gro_id='$gro_id' where charge_list_id='$charge_list_id' and gro_id='0'");
	$msg_info[] = "<br><b>Group is updated in encounter id ".($encounter_id)." at Enter Charges</b>";
}



?>
<html>
<head>
<title>Mysql Updates - Group update in Encounter</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
	<form action="" method="get" name="submit_frm" id="submit_frm">
		<input type="hidden" name="start_val" value="<?php print $start_val + $end; ?>">
	</form>
	<?php
	if(imw_num_rows($qry) > 0 || imw_num_rows($qry_chl) > 0){
	?>
	<script type="text/javascript">
		document.getElementById("submit_frm").submit();
	</script>
	<?php
	}
	?>
</body>
</html>