<?php
set_time_limit(0); 
$ignoreAuth = true;
$skip_file="skipthisfile";
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/acc_functions.php');


$qry = imw_query("SELECT creditapplied.crAppId, creditapplied.crAppliedToEncId,creditapplied.charge_list_detail_id, creditapplied.patient_id, patient_charge_list.patient_id as pcl_patient_id, patient_charge_list.encounter_id
					FROM `creditapplied`
					JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_detail_id = creditapplied.charge_list_detail_id
					JOIN patient_charge_list ON patient_charge_list.charge_list_id = patient_charge_list_details.charge_list_id
					WHERE creditapplied.crAppliedTo = 'adjustment'
					AND patient_charge_list.encounter_id != creditapplied.crAppliedToEncId");
while($qryRes = imw_fetch_array($qry)){
	if($qryRes['crAppliedToEncId']!=$qryRes['encounter_id'] && $qryRes['patient_id']==$qryRes['pcl_patient_id']){	
		imw_query("update creditapplied set crAppliedToEncId='".$qryRes['encounter_id']."' where crAppId='".$qryRes['crAppId']."'");
		imw_query("update report_enc_trans set encounter_id='".$qryRes['encounter_id']."' where master_tbl_id='".$qryRes['crAppId']."' and charge_list_detail_id='".$qryRes['charge_list_detail_id']."' and trans_type in('debit','credit')");
		$end++;
	}
}

$msg_info[] = "<br><b>".($start_val + $end)." Records Updates encounter credit debit Successfully!</b>";

?>
<html>
<head>
<title>Mysql Updates - Encounter in credit debit id Update</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>