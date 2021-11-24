<?php
$ignoreAuth = true;
$skip_file="skipthisfile";
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/acc_functions.php');

if(empty($start_val) == true){
	$start_val = 0;
}
$end = 1;

//---- UPDATE PATIENT CHARGE LIST FOR PATIENT DUE AND INSURANCE DUE -----

$qry = imw_query("SELECT patient_charge_list.charge_list_id,patient_charge_list.encounter_id, patient_charge_list_details.posFacilityId FROM patient_charge_list JOIN  patient_charge_list_details ON patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id 
				WHERE patient_charge_list_details.posFacilityId != patient_charge_list.facility_id and patient_charge_list_details.posFacilityId>0");
while($qryRes = imw_fetch_array($qry)){

	$qry2 = imw_query("SELECT posFacilityId FROM patient_charge_list_details WHERE charge_list_id='".$qryRes['charge_list_id']."' and posFacilityId>0 ORDER BY charge_list_detail_id ASC ");
	$qryRes2 = imw_fetch_array($qry2);
	
	imw_query("update patient_charge_list set facility_id='".$qryRes2['posFacilityId']."' where charge_list_id='".$qryRes['charge_list_id']."'");
	$end++;
}

$msg_info[] = "<br><b>".($start_val + $end)." Records Updates encounter facility id Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Encounter Facility id Update</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>