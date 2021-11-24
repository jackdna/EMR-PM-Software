<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$qry = "
SELECT DATE_FORMAT(pc.dos,'%m-%d-%Y') as ptDos, 
pi.patient_instruction_id, 
DATE_FORMAT(pi.signSurgeon1DateTime,'%m-%d-%Y') as signSurgeon1Date,
DATE_FORMAT(pi.signNurseDateTime,'%m-%d-%Y') as signNurseDate,
DATE_FORMAT(pi.signWitness1DateTime,'%m-%d-%Y') as signWitness1Date 
FROM patientconfirmation pc, patient_instruction_sheet pi 
WHERE pc.patientConfirmationId = pi.patient_confirmation_id
ORDER BY pi.patient_instruction_id DESC";
$res = imw_query($qry) or $msg_info[] = imw_error();
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_array($res)) {
		$setDate = $row["ptDos"];
		if($row["signSurgeon1Date"]<>"00-00-0000") {
			$setDate = $row["signSurgeon1Date"];
		}else if($row["signNurseDate"]<>"00-00-0000") {
			$setDate = $row["signNurseDate"];
		}else if($row["signWitness1Date"]<>"00-00-0000") {
			$setDate = $row["signWitness1Date"];
		}
		$updtQry = "UPDATE patient_instruction_sheet SET instruction_sheet_data = REPLACE(instruction_sheet_data, '{DATE}', '<b>".$setDate."</b>') WHERE patient_instruction_id = '".$row["patient_instruction_id"]."'";	
		$updtRes = imw_query($updtQry) or $msg_info[] = imw_error();		
	}
	
}

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 35 run OK";

?>

<html>
<head>
<title>Update 35</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>







